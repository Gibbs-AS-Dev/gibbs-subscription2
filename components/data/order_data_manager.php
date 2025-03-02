<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_subscription_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_plan_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/all_subscription_data_manager.php';

/*
Orders are stored in a meta information table in the database. It holds a set of key / value pairs. At the moment, we
use the following keys:

------------------------------------------------------------------------------------------------------------------------
Order information
------------------------------------------------------------------------------------------------------------------------

order_id : integer		              This is actually not stored as a key/value pair. Use the database ID instead.
order_title : string                Headline which explains what the order is for.
order_date : date                   The date when the order was created. String with the format "yyyy-mm-dd".
order_pay_by_date : date            The date by which the order needs to be paid. String with the format "yyyy-mm-dd".
order_line_count : integer		      The number of order lines (see below).
name : string                       The customer's name.
billing_phone : string              The customer's phone number at the time the order was created. If the number has no
                                    country code, assume it is a Norwegian phone number (+47).
billing_email : string              The customer's e-mail address at the time the order was created.
billing_address : string            The customer's street address at the time the order was created.
billing_postcode : string           The customer's postcode at the time the order was created.
billing_city : string               The customer's area at the time the order was created.
profile_type : string               The entity type, stored as a string. Possible values are "company" and "personal".
company_number : string             The customer's company ID number (Norwegian "organisasjonsnummer") at the time the
                                    order was created, provided the profile_type is "company". Otherwise, the field will
                                    not be present.

------------------------------------------------------------------------------------------------------------------------
Payment information
------------------------------------------------------------------------------------------------------------------------

payment_method : integer  		      Values (use the PAYMENT_METHOD_ constants):
					                              0  	Unknown
					                              1   Dibs Easy (Nets)
                                        2   Invoice payment (eFaktura, or any other kind)
payment_status : integer	      	  Values (use the PAYMENT_STATUS_ constants):
					                              0   Unknown
					                              1	  Not paid
					                              2	  Paid
					                              3	  Partially paid
					                              4	  Not paid - overdue
					                              5	  Not paid - reminder sent
					                              6	  Not paid - collection agency warning sent
					                              7	  Not paid - transferred to collection agency
					                              8	  Paid - paid to collection agency
					                              9	  Lost / uncollectable
					                             10	  Credited
					                             11	  Payment failed at payment provider
					                             12	  Technical error occurred during payment
					                             13	  Paid - refunded
					                             14	  Disputed
                                       15   Not paid - no invoice sent
                                       16   Not paid - invoice sent
                                       17   Not paid - charge request submitted to payment provider
period_month : date                 The month covered by this order, as a string with the format "yyyy-mm", or null if
                                    not relevant. If the order covers more than one month, the value will be the last
                                    month covered. For instance, if the period_start is 2024-04-17 and the period_end is
                                    2024-05-31, the period_month will be 2024-05.
period_start : date                 The start of the time period covered by this order, as a string with the format
                                    "yyyy-mm-dd", or null if not relevant.
period_end : date                   The end of the time period covered by this order, as a string with the format
                                    "yyyy-mm-dd", or null if not relevant.
renewal_date : date                 The date on which the subscription expires, and must be re-confirmed by the user in
                                    order to keep running. If not set, the subscription never expires until cancelled.
                                    This value may be required by some payment providers, who do not support indefinite
                                    subscriptions.

For Dibs Easy (Nets):

nets_subscription_id : string       The ID of the Nets subscription. Used to trigger subsequent charges to the
                                    customer's credit card. Will be added as soon as the subscription has been created
                                    with Nets. The subscription ID is the same every month.
nets_payment_id : string            The ID of the payment for this order. Will be added as soon as the subscription has
                                    been created with Nets. The payment ID is unique for each payment.
nets_charge_id : string             The ID of the charge for this order. A charge is a reservation. Not present for
                                    initial orders, but will be added for monthly orders once the bulk payment has been
                                    submitted to Nets. The charge ID is unique every month.
nets_bulk_id : string               The ID of the bulk charge payment in which this order was included. Payment is
                                    requested for all of a user group's orders (that use Nets) at once, using a bulk
                                    payment request.
nets_date_paid : date               Not implemented.
nets_payment_method : string        Not implemented.

------------------------------------------------------------------------------------------------------------------------
Order lines
------------------------------------------------------------------------------------------------------------------------

line_N_id           integer         The database ID of the product being charged in this line.
line_N_text		      string		      Description of what the charge is for. N is zero based.
line_N_amount		    integer		      Amount (we'll ignore VAT for now). N is zero based.

------------------------------------------------------------------------------------------------------------------------
*/

// Unless otherwise noted, the Order_Data_Manager manipulates orders for a single subscription at a time. Set the
// subscription_id property to choose which one. The data manager may not work otherwise.
class Order_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The database ID of the subscription to which orders are linked.
  protected $subscription_id = -1;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('set_payment_status', Utility::ROLE_COMPANY_ADMIN, 'update_payment_status');
    $this->database_table = 'subscription_order';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all orders connected to the subscription whose ID is stored in this class from the database. Return them as a
  // string containing JSON data.
  //
    // *** // Return Nets payment method as an integer, to deal with translation. How to get it from Nets?
  // The array holds the following fields:
  //   id : integer
  //   name : string
  //   order_date : date (string in the format "yyyy-mm-dd")
  //   pay_by_date : date (string in the format "yyyy-mm-dd")
  //   payment_method : integer (use the PAYMENT_METHOD_ constants)
  //   payment_status : integer (use the PAYMENT_STATUS_ constants)
  //   payment_info : object (information from the payment provider; differs between providers)
  //   order_lines : array with the following fields:
  //                   id : integer
  //                   text : string
  //                   amount : integer
  //   open : boolean (always false)
  public function read()
  {
    global $wpdb;

    if ($this->get_subscription_id() < 0)
    {
      return '[]';
    }

    $query = "
        SELECT 
          o.ID AS id,
          o.order_buyer AS buyer_id,
          om.meta_key AS `key`,
          om.meta_value AS `value`
        FROM
          {$this->database_table} o
        LEFT JOIN
          subscription_ordermeta om ON om.order_id = o.ID
        WHERE
          o.order_subscription_id = {$this->get_subscription_id()}
        ORDER BY
          CASE 
            WHEN om.meta_key = 'order_date' THEN om.meta_value
            ELSE NULL
          END DESC;
      ";
    $results = $wpdb->get_results($query, ARRAY_A);

    // Create a table where all keys for a particular order are gathered.
    $orders = self::parse_orders($results);

    $table = '[';
    if (count($orders) > 0)
    {
      foreach ($orders as $order)
      {
        $table .= '[';
        $table .= strval($order['id']);
        $table .= ', "';
        $table .= self::get_value('order_title', $order);
        $table .= '", "';
        $table .= self::get_value('order_date', $order);
        $table .= '", "';
        $table .= self::get_value('order_pay_by_date', $order);
        $table .= '", ';
        $table .= self::get_integer('payment_method', $order, Utility::PAYMENT_METHOD_UNKNOWN);
          // $table .= self::get_value('nets_payment_method', $order);
        $table .= ', ';
        $table .= self::get_integer('payment_status', $order, Utility::PAYMENT_STATUS_UNKNOWN);
        $table .= ', ';
          // *** // Fill this in. Should we include the payment ID?
        $table .= 'null';
        $table .= ', [';
        $line_count = self::get_integer('order_line_count', $order, 0);
        if ($line_count > 0)
        {
          for ($i = 0; $i < $line_count; $i++)
          {
            $table .= '[';
            $table .= self::get_value('line_' . strval($i) . '_id', $order);
            $table .= ', "';
            $table .= self::get_value('line_' . strval($i) . '_text', $order);
            $table .= '", ';
            $table .= self::get_integer('line_' . strval($i) . '_amount', $order, 0);
            $table .= '],';
          }
          $table = Utility::remove_final_comma($table);
        }
        $table .= '], false';
        $table .= '],';
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= ']';
    return $table;
  }

  // *******************************************************************************************************************
  // Read the order with the given $id from the database. Return null if it was not found. This method does not require
  // you to set the subscription ID property.
  //
  // The returned order is an array with the following fields:
  //   id             The order ID
  //   buyer_id       The ID of the customer who is paying for the order.
  //   data           An array that holds key / value metadata items. Use the get_value and get_integer methods to read
  //                  individual metadata items.
  public function read_order($id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT 
          o.ID AS id,
          o.order_buyer AS buyer_id,
          om.meta_key AS `key`,
          om.meta_value AS `value`
        FROM
          {$this->database_table} o
        LEFT JOIN
          subscription_ordermeta om ON om.order_id = o.ID
        WHERE
          o.ID = %d;
      ",
      $id
    );
    $results = $wpdb->get_results($sql, ARRAY_A);

    // Create a table where all keys for a particular order are gathered.
    $orders = self::parse_orders($results);

    // Return the first order, if it was found. There should only ever be one.
    if (count($orders) <= 0)
    {
      return null;
    }
    // Convert from an array with the order ID as a key, to a plain array. Then return the first element.
    return array_values($orders)[0];
  }

  // *******************************************************************************************************************
  // Read the order with the given $payment_id from the database. Return null if it was not found. This method does not
  // require you to set the subscription ID property.
  //
  // The returned order is an array with the following fields:
  //   id             The order ID
  //   buyer_id       The ID of the customer who is paying for the order.
  //   data           An array that holds key / value metadata items. Use the get_value and get_integer methods to read
  //                  individual metadata items.
  public function read_order_with_payment_id($payment_id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT 
          o.ID AS id,
          o.order_buyer AS buyer_id,
          om.meta_key AS `key`,
          om.meta_value AS `value`
        FROM 
          {$this->database_table} o
        LEFT JOIN 
          subscription_ordermeta om ON om.order_id = o.ID
        WHERE 
          o.ID = (
            SELECT order_id 
            FROM subscription_ordermeta 
            WHERE meta_key = 'payment_id' AND meta_value = %s
          );
      ",
      $payment_id
    );
    $results = $wpdb->get_results($sql, ARRAY_A);

    // Create a table where all keys for a particular order are gathered.
    $orders = self::parse_orders($results);

    // Return the first order, if it was found. There should only ever be one.
    if (count($orders) <= 0)
    {
      return null;
    }
    // Convert from an array with the order ID as a key, to a plain array. Then return the first element.
    return array_values($orders)[0];
  }

  // *******************************************************************************************************************
  // Read the order with the given $charge_id from the database. Return null if it was not found. This method does not
  // require you to set the subscription ID property.
  //
  // The returned order is an array with the following fields:
  //   id             The order ID
  //   buyer_id       The ID of the customer who is paying for the order.
  //   data           An array that holds key / value metadata items. Use the get_value and get_integer methods to read
  //                  individual metadata items.
  public function read_order_with_charge_id($charge_id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT 
          o.ID AS id,
          o.order_buyer AS buyer_id,
          om.meta_key AS `key`,
          om.meta_value AS `value`
        FROM 
          {$this->database_table} o
        LEFT JOIN 
          subscription_ordermeta om ON om.order_id = o.ID
        WHERE 
          o.ID = (
            SELECT order_id 
            FROM subscription_ordermeta 
            WHERE meta_key = 'charge_id' AND meta_value = %s
          );
      ",
      $charge_id
    );
    $results = $wpdb->get_results($sql, ARRAY_A);

    // Create a table where all keys for a particular order are gathered.
    $orders = self::parse_orders($results);

    // Return the first order, if it was found. There should only ever be one.
    if (count($orders) <= 0)
    {
      return null;
    }
    // Convert from an array with the order ID as a key, to a plain array. Then return the first element.
    return array_values($orders)[0];
  }

  // *******************************************************************************************************************
  // Read the all orders for the current user group from the database. Return the orders as a Javascript array. Use the
  // c.ord column constants. This method does not require you to set the subscription ID property.
  public function read_all_orders()
  {
    global $wpdb;

    $query = "
        SELECT 
          o.ID AS id,
          o.order_subscription_id AS subscription_id,
          o.order_buyer AS buyer_id,
          o.created_at AS created_at,
          p.location_id AS location_id,
          p.post_title AS product_name,
          (
            SELECT GROUP_CONCAT(CONCAT(om.meta_key, '>', om.meta_value) SEPARATOR '|')
            FROM subscription_ordermeta om
            WHERE om.order_id = o.ID
          ) AS data
        FROM 
          {$this->database_table} o
        JOIN 
          subscription_ordermeta d ON o.ID = d.order_id AND d.meta_key = 'order_date'
        JOIN
          subscriptions s ON o.order_subscription_id = s.id
        JOIN
          {$wpdb->prefix}posts p ON p.ID = s.product_id
        WHERE 
          o.order_owner = {$this->get_user_group_user_id()};
      ";
    $results = $wpdb->get_results($query, ARRAY_A);
    if (!is_array($results))
    {
      return '[]';
    }
    // Parse metadata and write Javascript table.
    self::parse_concatenated_metadata($results);
    return self::export_orders_as_javascript($results);
  }

  // *******************************************************************************************************************
  // Read the all orders for the current user group for the given $month from the database. $month should be a string
  // with the format "yyyy-mm". If not valid, the method will return a string with an empty Javascript array. This
  // method does not require you to set the subscription ID property.
  //
  // Return the orders as a Javascript array. Use the c.ord column constants.
/*
  public function read_orders_for_month($month)
  {
    global $wpdb;

    if (!Utility::is_valid_month($month))
    {
      return '[]';
    }

    $sql = $wpdb->prepare("
        SELECT 
          o.ID AS id,
          o.order_subscription_id AS subscription_id,
          o.order_buyer AS buyer_id,
          o.created_at AS created_at,
          GROUP_CONCAT(CONCAT(om.meta_key, '>', om.meta_value) SEPARATOR '|') AS data
        FROM 
          {$this->database_table} o
        JOIN 
          subscription_ordermeta om1 ON o.ID = om1.order_id
        JOIN 
          subscription_ordermeta om ON o.ID = om.order_id
        WHERE 
          (o.order_owner = {$this->get_user_group_user_id()}) AND
          (om1.meta_key = 'period_month') AND
          (om1.meta_value = %s)
        GROUP BY 
          o.ID, o.order_subscription_id, o.order_buyer;
      ",
      $month
    );
    $results = $wpdb->get_results($sql, ARRAY_A);
    if (!is_array($results))
    {
      return '[]';
    }
    // Parse metadata and write Javascript table.
    self::parse_concatenated_metadata($results);
    return self::export_orders_as_javascript($results);
  }
*/
  // *******************************************************************************************************************
  // Create an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_create method.
  //
  // The item to be created can be passed as a parameter. If not, it will be read from the request.
  //
  // Override to add data to the ordermeta table, once the order has been created. Note that this class is unable to
  // read the order data item from the request.
  public function create($data_item = null)
  {
    global $wpdb;

    // Read input data.
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
    // Keep the metadata for afterwards, but remove it from the order that goes to the database. It has to be passed as
    // part of the data item: we need it, but cannot change the signature of the method we are overriding.
    $metadata = $data_item['data'];
    unset($data_item['data']);

    // Create the order in the database.
    $wpdb->query('START TRANSACTION');
    $result = parent::create($data_item);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Add fields to the ordermeta table.
    if (!self::add_metadata_for_order($this->created_item_id, $metadata))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // All operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while creating order: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Delete an item from the database. Return an integer result code that can be used to inform the user of the result
  // of these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_delete method.
  //
  // The ID of the item to be deleted can be passed as a parameter. If not, it will be read from the request.
  //
  // Override to delete entries from the ordermeta table, before the order itself is deleted.
  public function delete($id = null)
  {
    global $wpdb;

    // Ensure the ID is available.
    if (isset($id) && is_numeric($id))
    {
      $id = intval($id);
    }
    else
    {
      if (!Utility::integer_posted($this->id_posted_name))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      $id = Utility::read_posted_integer($this->id_posted_name);
    }

    // Delete the order's metadata from the database.
    $wpdb->query('START TRANSACTION');
    $result = $wpdb->query("DELETE FROM subscription_ordermeta WHERE order_id = {$id};");
    if ($result === false)
    {
      error_log("Error while deleting metadata for order {$id}: {$wpdb->last_error}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // Delete the order from the database.
    $result = parent::delete($id);
    if ($result === Result::OK)
    {
      if ($wpdb->query('COMMIT') === false)
      {
        error_log('Commit failed while deleting order: ' . $wpdb->last_error);
        $wpdb->query('ROLLBACK');
        return Result::DATABASE_QUERY_FAILED;
      }
    }
    else
    {
      $wpdb->query('ROLLBACK');
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Create an order based on the subscription with the ID stored in the subscription_id property. The order will be the
  // one created when the subscription is created. That means, it will charge the buyer for the remainder of the current
  // month, as well as next month. The exception is if it is currently the first of the month - in which case the order
  // will only charge for the whole of the current month. The order will charge the user for storage unit rent, and
  // insurance if it was ordered. The order will use the given $payment_method (use the Utility::PAYMENT_METHOD_
  // constants). Return a result code to say what happened.
  public function create_initial_order_from_subscription($payment_method)
  {
    if (!Utility::is_valid_payment_method($payment_method))
    {
      error_log('Failed to create initial order: payment method not valid.');
      return Result::UNABLE_TO_CREATE_ORDER;
    }
    $payment_method = intval($payment_method);

    // Read the subscription for which the order will be created.
    $subscription_data = new User_Subscription_Data_Manager($this->access_token);
    $subscription = $subscription_data->read_subscription($this->get_subscription_id());
    if (!isset($subscription))
    {
      error_log('Failed to create initial order: subscription with ID ' . $this->get_subscription_id() . ' not found.');
      return Result::UNABLE_TO_CREATE_ORDER;
    }

    // Read information about the buyer. The buyer's information might subsequently change, so it needs to be recorded
    // in the order.
    $buyer = User_Data_Manager::get_user_data($subscription['buyer_id']);
    if ($buyer === null)
    {
      error_log('Failed to create initial order: buyer with ID ' . $subscription['buyer_id'] . ' not found.');
      return Result::UNABLE_TO_CREATE_ORDER;
    }

    // The buyer will pay rent, and insurance if available. Find the price plans that determine the prices for each.
    $product_price_plan = Price_Plan_Data_Manager::get_product_price_plan($subscription);
    $insurance_price_plan = Price_Plan_Data_Manager::get_insurance_price_plan($subscription);

    // Find the prices per month for the first month, based on the subscription start date.
    $rent_per_month = Price_Plan_Data_Manager::get_price_from_price_plan(
      $product_price_plan, $subscription['start_date']);
    if (isset($insurance_price_plan))
    {
      $insurance_per_month = Price_Plan_Data_Manager::get_price_from_price_plan(
        $insurance_price_plan, $subscription['start_date']);
    }
    // Find the first and second month that is being paid for now. Unless the buyer pays the first month only, the second
    // month is one month later than the first one. Both months are formatted as "yyyy-mm".
    $start_date = Utility::string_to_date($subscription['start_date']);
    $first_month = $start_date->format('Y-m');
    $first_month_only = self::pay_for_first_month_only($start_date);
    if ($first_month_only)
    {
      $second_month = $first_month;
    }
    else
    {
      $second_month = Utility::get_next_month($first_month);
    }

    // Create order metadata.
    $metadata = $this->get_order_metadata(
      'Initial payment, ' . $second_month,
      self::get_pay_by_date(),
      $payment_method,
      $buyer,
      Utility::date_to_string($start_date),
      Utility::get_last_date($second_month),
      $second_month,
      self::get_subscription_renewal_date($start_date)->format('Y-m-d')
    );

    // Create order lines.
    $line_index = 0;
    if ($first_month_only)
    {
      // The buyer is only paying for the first month. We'll have one line for the rent, and one for the insurance if
      // it exists.
      self::add_order_line($metadata, $line_index, $subscription['product_id'], 'Rent, ' . $first_month,
        $rent_per_month);
      if (isset($insurance_price_plan))
      {
        self::add_order_line($metadata, $line_index, $subscription['insurance_id'], 'Insurance, ' . $first_month,
          $insurance_per_month);
      }
    }
    else
    {
      // The buyer is paying for both the first and the next month. We'll have rent and insurance for the first month,
      // then rent and insurance for the second month. Note that the insurance may not be present. For the first month,
      // the user pays only from the start date until the end of the month.
      $days_in_month = Utility::get_days_in_month($start_date);
      $days_left = $days_in_month - intval($start_date->format('d')) + 1;
      $month_fraction = $days_left / $days_in_month;
      self::add_order_line($metadata, $line_index, $subscription['product_id'], 'Rent, ' . $first_month,
        floor($month_fraction * $rent_per_month));
      if (isset($insurance_price_plan))
      {
        self::add_order_line($metadata, $line_index, $subscription['insurance_id'], 'Insurance, ' . $first_month,
          floor($month_fraction * $insurance_per_month));
      }
      // Find the prices for the second month, based on the first day of the second month.
      $rent_per_month = Price_Plan_Data_Manager::get_price_from_price_plan($product_price_plan, $second_month . '-01');
      $insurance_per_month = Price_Plan_Data_Manager::get_price_from_price_plan(
        $insurance_price_plan, $second_month . '-01');
      self::add_order_line($metadata, $line_index, $subscription['product_id'], 'Rent, ' . $second_month,
        $rent_per_month);
      if (isset($insurance_price_plan))
      {
        self::add_order_line($metadata, $line_index, $subscription['insurance_id'], 'Insurance, ' . $second_month,
          $insurance_per_month);
      }
    }

    // Create the order.
    return $this->create(self::get_order_data_item($subscription, $metadata));
  }

  // *******************************************************************************************************************
  // Contact the Nets payment provider, and initiate a payment for the order with the given ID. This is expected to be
  // the first order for this particular subscription. Return an array of data from Nets, or null if the payment was not
  // created successfully.
  //
  // See the Nets payment documentation at:
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-post
  //
  // $order_id is optional - if not present, the ID of the last created order will be used. Return an object that holds
  // the payment ID created by the payment provider. This can be returned to the client, which will be able to use it
  // when communicating with the payment provider. $order_id is the database ID of the order that is being paid for.
  // This method assumes that no subscription has been registered with the payment provider yet. Subsequent orders can
  // be charged without involving the buyer.
  public function create_initial_payment($order_id = null)
  {
    // Read the order ID for which the payment should be created. If it wasn't passed, try the last created order. If
    // that is not present either, we cannot create a payment.
    if (!isset($order_id))
    {
      $order_id = $this->get_created_item_id();
    }
    if (!isset($order_id))
    {
      error_log('Failed to create initial payment: order ID not passed.');
      return null;
    }

    // Read the order for which the payment should be created.
    $order = $this->read_order($order_id);
    if (!isset($order) || ($order === false))
    {
      error_log('Failed to create initial payment: order with ID {$order_id} not found.');
      return null;
    }
    $payment_method = self::get_integer('payment_method', $order, Utility::PAYMENT_METHOD_UNKNOWN);

    // Read information about the buyer who is paying for the order.
    $buyer = User_Data_Manager::get_user_data($order['buyer_id']);
    if ($buyer === null)
    {
      error_log('Failed to create initial payment: Buyer with ID ' . $order['buyer_id'] . ' not found.');
      return null;
    }

    // Create a payment item for each order line, and sum up the amount to be charged. Add the information to an order
    // data structure.
    $message = '';
    $order_data = self::get_order_data($order, $message);
    if ($order_data === null)
    {
      error_log('Failed to create initial payment: ' . $message);
      return null;
    }

    // Create data structure to describe the subscription to the payment provider.
    $payload = array(
      'checkout' => array(
        'integrationType' => 'EmbeddedCheckout',
        'url' => Utility::get_domain() . '/subscription/html/pay.php',
        'termsUrl' => Utility::get_gibbs_terms_url(),
        'merchantHandlesConsumerData' => true,
        'consumer' => array(
          'reference' => $buyer['user_id'],
          'email' => $buyer['email'],
          'phoneNumber' => array(
            'number' => $buyer['phone'],
              // *** // Permit foreign numbers.
            'prefix' => '+47'
          )
        ),
        'charge' => true
      ),
      'order' => $order_data
    );
    // If the payment method will always be Nets, we need to create a subscription. Otherwise, this is just a one-time
    // payment, and no subscription is required.
    if ($payment_method === Utility::PAYMENT_METHOD_NETS)
    {
      $payload['subscription'] = array(
        'interval' => 0,
        'endDate' => self::get_subscription_renewal_date(self::get_value('order_date', $order))->format('Y-m-d\TH:i:sP')
      );
    }
    if (!empty($buyer['address']) && !empty($buyer['postcode']) && !empty($buyer['area']))
    {
      $payload['checkout']['consumer']['shippingAddress'] = array(
        'addressLine1' => $buyer['address'],
        'addressLine2' => '',
        'postalCode' => $buyer['postcode'],
        'city' => $buyer['area'],
          // *** // Permit other countries.
        'country' => 'NOR'
      );
    }
    if ($buyer['entity_type'] === Utility::ENTITY_TYPE_COMPANY)
    {
      $payload['checkout']['consumer']['company'] = array(
        'name' => $buyer['name']
      );
    }
    else
    {
      $payload['checkout']['consumer']['privatePerson'] = array(
        'firstName' => $buyer['first_name'],
        'lastName' => $buyer['last_name']
      );
    }

    // Create and submit a request to the payment provider.
    $settings = Settings_Manager::read_settings($this->access_token);
    $request = curl_init($settings->get_nets_payment_url());
    curl_setopt_array($request, array(
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: ' . $settings->get_nets_secret_key()
      )
    ));
    $response = curl_exec($request);

    // Check the response.
    $error = curl_error($request);
    $http_code = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
    curl_close($request);
    if (($http_code === false) || !is_numeric($http_code))
    {
      error_log('Failed to create initial payment: curl_getinfo failed to return HTTP response code.');
      return null;
    }
    if ($response === false)
    {
      error_log('Failed to create initial payment: cURL error: ' . $error);
      return null;
    }
    $response = json_decode($response);
    $http_code = intval($http_code);
    switch ($http_code)
    {
      case 201:
        // HTTP 201 Created: The payment request succeeded.
        return $response;
      case 400:
        // HTTP 400 Bad request: The request was not valid. Log a description of the errors.
        error_log('Failed to create initial payment: Nets returned HTTP 400 Bad request. Errors: ' . print_r($response, true));
        return null;
      case 500:
        // HTTP 500 Internal server error: Something went wrong at Nets. Log an error message and code for debugging
        // purposes.
        error_log('Failed to create initial payment: Nets returned HTTP 500 Internal server error. Errors: ' .
          print_r($response, true));
        return null;
    }
    // The request failed with a different HTTP error code. Log it.
    error_log('Failed to create initial payment: Nets returned HTTP ' . $http_code);
    return null;
  }

  // *******************************************************************************************************************
  // See whether the given payment data were valid. Report any errors, and return a result code. If the method returns
  // Result::OK, the payment ID can be retrieved from $payment_data.
  public static function verify_payment_data($payment_data)
  {
    if (isset($payment_data))
    {
      if (isset($payment_data->errors))
      {
        error_log('Payment failed: ' . print_r($payment_data->errors, true));
        return Result::PAYMENT_FAILED;
      }
      if (isset($payment_data->paymentId))
      {
        // The payment was created successfully, and no errors were reported. Let the caller know.
        return Result::OK;
      }
      error_log('Payment failed: paymentId missing, but no errors reported');
      return Result::PAYMENT_FAILED;
    }
    error_log('Payment failed: failed to create payment, or failed to parse the JSON data returned from the payment provider');
    return Result::PAYMENT_FAILED;
  }

  // *******************************************************************************************************************
  // Read the status for the payment with the given $payment_id from Nets and return the subscription ID. Return a
  // result code to say whether the operation succeeded.
  //
  // Note that the Nets subscription ID is not the same value as the subscription ID used in our database.
  //
  // See the Nets payment documentation at:
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-get
  public function read_nets_subscription_id(&$nets_subscription_id, $payment_id)
  {
    // Verify payment ID.
    if (!isset($payment_id))
    {
      error_log('Failed to read subscription ID: payment ID not provided');
      return Result::MISSING_INPUT_FIELD;
    }

    // Create and submit a request to the payment provider.
    $settings = Settings_Manager::read_settings($this->access_token);
    $request = curl_init($settings->get_nets_payment_url() . '/' . $payment_id);
    curl_setopt_array($request, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => [
        'Authorization: ' . $settings->get_nets_secret_key()
      ],
    ));
    $response = curl_exec($request);

    // Check the response.
    $error = curl_error($request);
    $http_code = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
    curl_close($request);
    if (($http_code === false) || !is_numeric($http_code))
    {
      error_log('Failed to read subscription ID: curl_getinfo failed to return HTTP response code.');
      return Result::REQUEST_FAILED;
    }
    if ($response === false)
    {
      error_log('Failed to read subscription ID: ' . $error);
      return Result::REQUEST_FAILED;
    }
    $response = json_decode($response);
    $http_code = intval($http_code);
    switch ($http_code)
    {
      case 200:
        // HTTP 200 OK: The payment information request succeeded. Return the information.
        if (isset($response->payment) && isset($response->payment->subscription) &&
          isset($response->payment->subscription->id))
        {
          $nets_subscription_id = $response->payment->subscription->id;
          return Result::OK;
        }
        error_log('Failed to read subscription ID: Nets did not return the subscription ID.');
        return Result::REQUEST_FAILED;
      case 400:
        // HTTP 400 Bad request: The request was not valid. Log a description of the errors.
        error_log('Failed to read subscription ID: Nets returned HTTP 400 Bad request. Errors: ' . print_r($response, true));
        return Result::REQUEST_FAILED;
      case 500:
        // HTTP 500 Internal server error: Something went wrong at Nets. Log an error message and code for debugging
        // purposes.
        error_log('Failed to read subscription ID: Nets returned HTTP 500 Internal server error. Errors: ' .
          print_r($response, true));
        return Result::REQUEST_FAILED;
    }
    // The request failed with a different HTTP error code. Log it.
    error_log('Failed to read subscription ID: Nets returned HTTP ' . $http_code);
    return Result::REQUEST_FAILED;
  }

  // *******************************************************************************************************************
  // Store the given $subscription_id and $payment_id on the order with the given $order_id. $order_id is optional - if
  // not present, the ID of the last created order will be used. Return a result code that says whether the operation
  // succeeded.
  public function store_subscription_id($subscription_id, $payment_id, $order_id = null)
  {
    if (empty($subscription_id) || empty($payment_id))
    {
      error_log('Failed to store subscription and payment ID: subscription ID or payment ID not passed');
      return Result::MISSING_INPUT_FIELD;
    }

    // Find the order ID for which the payment ID should be stored. If it wasn't passed, try the last created order. If
    // that is not present either, report an error.
    if (!isset($order_id))
    {
      $order_id = $this->get_created_item_id();
    }
    if (!isset($order_id))
    {
      error_log('Failed to store subscription and payment ID ({$subscription_id}, {$payment_id}): order ID not passed');
      return Result::MISSING_INPUT_FIELD;
    }

    // Store the values by adding metadata to the order.
    if (self::add_metadata_for_order($order_id, array(
      'nets_subscription_id' => $subscription_id,
      'nets_payment_id' => $payment_id
    )))
    {
      return Result::OK;
    }
    error_log('Failed to store subscription and payment ID ({$subscription_id}, {$payment_id}) for order {$order_id}');
    return Result::DATABASE_QUERY_FAILED;
  }

  // *******************************************************************************************************************
  // Read parameters passed from the client ("id" and "payment_status"), and update the payment status of the order with
  // the given ID.
  public function update_payment_status()
  {
    // Read parameters.
    if (!Utility::integers_posted(array($this->id_posted_name, 'payment_status')))
    {
      error_log('Error when updating payment status: Missing input fields: "' . $this->id_posted_name .
        '" or "payment_status".');
      return Result::MISSING_INPUT_FIELD;
    }

    return $this->set_payment_status(Utility::read_posted_integer($this->id_posted_name),
      Utility::read_posted_integer('payment_status'));
  }

  // *******************************************************************************************************************
  // Set the payment status for the order with the given $order_id to the given value. $new_value should be an integer.
  // Return a result code that says whether the operation was successful.
  public function set_payment_status($order_id, $new_value)
  {
    global $wpdb;

    // Ensure $order_id and $new_value were provided.
    if (!isset($order_id) || !is_numeric($order_id) || !Utility::is_valid_payment_status($new_value))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $order_id = intval($order_id);
    $new_value = intval($new_value);
    // Set the payment status. The payment status resides in the ordermeta table.
    $result = $wpdb->update('subscription_ordermeta', array('meta_value' => $new_value), array(
        'order_id' => $order_id,
        'meta_key' => 'payment_status'
      ));
    if ($result === false)
    {
      error_log("Error while setting payment status for order {$order_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Database query updated the wrong number of rows while setting payment status for order {$order_id}. Expected: 1. Actual: {$result}. Attempted to set: {$new_value}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Create orders for all eligible subscriptions belonging to the current user group, for the given $month. $month
  // should be a string in the format "yyyy-mm". A subscription is eligible if it is active and ongoing. An order will
  // only be created if no order already exists for the given $month.
  //
  // Return a string, in JSON format, that holds an array of information about the subscriptions that were processed,
  // and the orders that were, or were not, created. Each object in the array represents a subscription, and holds the
  // following fields:
  //
  //   subscriptionId : integer   The database ID of the subscription for which an order was are generated.
  //   buyerId : integer          The database ID of the end user who will be paying for the subscription. Note that
  //                              additional buyer information will be found in the metadata.
  //   metadata : object          An object that holds all the metadata generated for the order, provided the order was
  //                              generated. The field will be present if the resultCode is NO_ACTION_TAKEN (in the case
  //                              of a simulation). The field may be present even if the resultCode is not OK (if the
  //                              order was generated, but was not added successfully to the database).
  //   resultCode : integer       The result of the operation for this particular subscription, using constants from the
  //                              Result class.
  //
  // $simulation is a boolean value. If true, the log will be generated as usual, but no orders will be added to the
  // database. In fact, the database will not be modified in any way.
  //
  // This method does not require the subscription ID to be set.
  public function create_orders_for_month($month, $simulation = false)
  {
    if (!Utility::is_valid_month($month))
    {
      error_log('Failed to create monthly orders: Invalid month: ' . $month);
      return '[]';
    }
    $reference_date = $month . '-01';
    $subscriptions_log = '[';
    // Get a list of all subscriptions for the current user group for which orders should be created. The subscriptions
    // are filtered based on their start and end dates.
    $subscription_data = new All_Subscription_Data_Manager($this->access_token);
    $subscriptions = $subscription_data->get_billable_subscriptions($month);
    // Note: the get_billable_subscriptions method could in theory return a simplified data set. We only use:
    //   subscription_id, buyer_id, owner_id, product_id, insurance_id, price_plans
    // However, this would preclude us using the common SQL result handling, so would be a bit of work.

    if (count($subscriptions) > 0)
    {
      foreach ($subscriptions as $subscription)
      {
        $subscriptions_log .= '{"subscriptionId": ' . $subscription['subscription_id'] . ', ';
        // See if the subscription already has an order that covers the given month.
        if (!$this->has_order_for_month($subscription['subscription_id'], $month))
        {
          // It does not. An order should be created. Log buyer ID.
          $subscriptions_log .= '"buyerId": ' . $subscription['buyer_id'] . ', ';
          //Read information about the buyer. The buyer's information might subsequently change, so it needs to be
          // recorded in the order.
          $buyer = User_Data_Manager::get_user_data($subscription['buyer_id']);
          if ($buyer !== null)
          {
            // Find the most recent previous order. It holds the payment method - and, if the subscription is paid
            // through Nets, the Nets subscription ID and renewal date - all of which are needed to construct another
            // order.
            $previous_order = $this->get_previous_order($subscription['subscription_id'], $month);
            if ($previous_order !== null)
            {
              // The buyer will pay rent, and insurance if available. Find the price plans that determine the prices for
              // each.
              $product_price_plan = Price_Plan_Data_Manager::get_product_price_plan($subscription);
              $insurance_price_plan = Price_Plan_Data_Manager::get_insurance_price_plan($subscription);

              // Find the price on the first of the given month.
              $rent_price = Price_Plan_Data_Manager::get_price_from_price_plan($product_price_plan, $reference_date);
              if (isset($insurance_price_plan))
              {
                $insurance_price = Price_Plan_Data_Manager::get_price_from_price_plan($insurance_price_plan,
                  $reference_date);
              }

              // Create order metadata.
              $metadata = $this->get_order_metadata(
                'Monthly payment, ' . $month,
                self::get_pay_by_date($reference_date),
                self::get_next_payment_method($previous_order),
                $buyer,
                $reference_date,
                Utility::get_last_date($month),
                $month,
                self::get_value('renewal_date', $previous_order)
              );
              // Note that, in some cases, the renewal date will occur before the pay-by date. In that case, the order
              // cannot be paid (at least not using Nets payments). We will need to contact the customer, and have him
              // renew the subscription, preferably before the pay-by date.
                // *** // Note here how it will be handled.

              // Add Nets payment information, if appropriate.
              if (($metadata['payment_method'] === Utility::PAYMENT_METHOD_NETS) ||
                ($metadata['payment_method'] === Utility::PAYMENT_METHOD_NETS_THEN_INVOICE))
              {
                // Try to get the Nets subscription ID from the previous order. It will be null if there was none.
                $nets_subscription_id = self::get_value('nets_subscription_id', $previous_order);
                if (empty($nets_subscription_id))
                {
                  // The Nets subscription ID was not found on the previous order. If we had a payment ID instead, try
                  // to get it from Nets. Note that the $nets_subscription_id is passed by reference, and will be
                  // updated if successful.
                  $payment_id = self::get_value('nets_payment_id', $previous_order);
                  if ($payment_id !== null)
                  {
                    $result_code = $this->read_nets_subscription_id($nets_subscription_id, $payment_id,
                      $previous_order['id']);
                    if ($result_code !== Result::OK)
                    {
                      error_log('Error while creating monthly orders: Error while looking up the Nets subscription ID: ' .
                        $result_code);
                      $nets_subscription_id = null;
                    }
                  }
                }
                // If the Nets subscription ID was not found, no Nets payment can be carried out. That headache is for
                // the payment process to deal with.
                $metadata['nets_subscription_id'] = $nets_subscription_id;
              }

              // Create order lines. Add one order line for the rent, and another for the insurance if it exists.
              $line_index = 0;
              self::add_order_line($metadata, $line_index, $subscription['product_id'], 'Rent, ' . $month, $rent_price);
              if (isset($insurance_price_plan))
              {
                self::add_order_line($metadata, $line_index, $subscription['insurance_id'], 'Insurance, ' . $month,
                  $insurance_price);
              }

              // Log all the metadata generated for the order.
              $subscriptions_log .= '"metadata": ' . json_encode($metadata) . ', ';

              // Create the order, unless we are running a simulation.
              if (!$simulation)
              {
                $subscriptions_log .= '"resultCode": ' .
                  $this->create(self::get_order_data_item($subscription, $metadata));
              }
              else
              {
                $subscriptions_log .= '"resultCode": ' . Result::NO_ACTION_TAKEN;
              }
            }
            else
            {
              // There was no previous order for this subscription within the past 12 months. Without that, we do not
              // have enough information to create another order.
              $subscriptions_log .= '"resultCode": ' . Result::PREVIOUS_ORDER_NOT_FOUND;
            }
          }
          else
          {
            // The buyer for this subscription was not found.
            $subscriptions_log .= '"resultCode": ' . Result::USER_NOT_FOUND;
          }
        }
        else
        {
          // The subscription already has an order that covers the given month.
          $subscriptions_log .= '"resultCode": ' . Result::ORDER_ALREADY_EXISTS;
        }
        $subscriptions_log .= '},';
      }
      $subscriptions_log = Utility::remove_final_comma($subscriptions_log);
    }
    $subscriptions_log .= ']';
    return $subscriptions_log;
  }

  // *******************************************************************************************************************
  // Create a bulk payment and charge all unpaid orders that use PAYMENT_METHOD_NETS, for the current user group and the
  // specified $month. $month should be a string in the format "yyyy-mm". The orders are assumed to already exist.
  //
  // See the Nets bulk charge documentation at:
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-post
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/docs/track-events-using-webhooks/
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/api/webhooks/
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/docs/manage-subscriptions/
  //
  // Return a string, in JSON format, that holds an object with the following fields:
  //
  //   resultCode : integer       The result of the operation for the current user group, using constants from the
  //                              Result class. Error codes used include:
  //                                OK: Everything went smoothly.
  //                                NO_ACTION_TAKEN: The user group had no payable orders for this month.
  //                                INVALID_MONTH: the $month held an invalid value.
  //                                MISSING_INPUT_FIELD: the $unique_id was empty.
  //                                INVALID_PAYMENT_INFO: The Nets secret key could not be retrieved from settings, or
  //                                  was not valid.
  //                                REQUEST_FAILED: Some sort of cURL error occurred. See the errorMessage
  //                                  field for details. This code is used both if the request was not sent, and if it
  //                                  was sent, but the Nets server returned an error.
  //   httpCode : integer         If the request was submitted to the Nets server, and an HTTP response was received,
  //                              this field will hold the HTTP status (including if the request succeeded).
  //   errorMessage : string      If a cURL error occurred, this field will hold the error message. Otherwise, it will
  //                              not be present.
  //   errors : object            If httpCode is 400, this field will contain information about why the request was
  //                              invalid.
  //   message : string           If httpCode is 500: an error message provided by Nets.
  //   code : string              If httpCode is 500: a numeric error code provided by Nets.
  //   source : string            If httpCode is 500: the source of the error, as provided by Nets.
  //   payload : object           An object that holds information that was sent to the payment provider. If an error
  //                              occurred, the payload object may not be included.
  //   excluded : array           An array that holds a list of orders that were excluded from the payload. Each entry
  //                              is an object with the subscription_id, order_id and message, which states the reason
  //                              the order was excluded. If an error occurred, this array may not be included.
  //
  // The payload object holds the following fields:
  //
  //   externalBulkChargeId : string  A unique string to describe this bulk payment operation. This allows us to repeat
  //                                  the payment request without charging customers more than once.
  //   notifications : object         An object that holds webhooks to receive status updates.
  //   subscriptions : array          An array of information about each subscription to be charged.
  //
  // Each item in the subscriptions array is an object with the following fields:
  //
  //   subscriptionId : string    The nets subscription ID that allows the customer to be charged repeatedly.
  //   order : object             An object that holds information about this order. For details, see the get_order_data
  //                              method.
  //
  // $unique_id is a string which should uniquely identify this round of payments. It will be combined with the month
  // and the user group ID to form a unique identifier to be passed to the payment provider.
  //
  // $webhook_authorization should contain the credentials required to access the webhook that will receive the results
  // of the bulk payment operation.
  //
  // $simulation is a boolean value. If true, the log will be generated as usual, but no payments will be created. The
  // database will not be modified in any way.
  //
  // This method does not require the subscription ID to be set.
  public function create_bulk_payment($month, $unique_id, $webhook_authorization, $simulation = false)
  {
    // Verify the month.
    if (!Utility::is_valid_month($month))
    {
      return '{"resultCode": ' . Result::INVALID_MONTH . '}';
    }

    // Verify that the $unique_id and $webhook_authorization fields were provided.
    if (empty($unique_id) || empty($webhook_authorization))
    {
      return '{"resultCode": ' . Result::MISSING_INPUT_FIELD . '}';
    }

    // Get a list of all orders for the current user group for which payments should be created.
    $orders = $this->get_payable_orders($month);
    if (count($orders) <= 0)
    {
      return '{"resultCode": ' . Result::NO_ACTION_TAKEN . '}';
    }

    // Get the Nets secret key for the current user group from settings.
    $settings = Settings_Manager::read_settings($this->access_token);
    if ($settings->get_nets_secret_key() === null)
    {
      return '{"resultCode": ' . Result::INVALID_PAYMENT_INFO . '}';
    }

    // There are orders to be paid. Compile information about each payment.
    $excluded_log = '[';
    $payment_data = array();
    foreach ($orders as $order)
    {
      // Create a payment item for each order line, and sum up the amount to be charged. Add the information to an order
      // data structure.
      $message = '';
      $order_data = self::get_order_data($order, $message);
      // If the order has no order lines (which should never happen), move on to the next one.
      if ($order_data === null)
      {
        error_log('Unable to include order with ID ' . $order['id'] . ' in bulk payment: ' . $message);
        $excluded_log .= '{"order_id": ' . $order['id'] . ', "subscription_id": ' . $order['subscription_id'] .
          ', "message": "' . $message . '"},';
        continue;
      }

      // Find the subscription ID. If it wasn't there, move on to the next order.
      $nets_subscription_id = self::get_value('nets_subscription_id', $order);
      if (empty($nets_subscription_id))
      {
        error_log('Unable to include order with ID ' . $order['id'] .
          ' in bulk payment. The order had no Nets subscription ID.');
        $excluded_log .= '{"order_id": ' . $order['id'] . ', "subscription_id": ' . $order['subscription_id'] .
          ', "message": "The order had no Nets subscription ID."},';
        continue;
      }

      // Build payment for this order.
      $payment_data[] = array(
        'subscriptionId' => $nets_subscription_id,
        'order' => $order_data
      );
    }
    if ($excluded_log !== '[')
    {
      $excluded_log = Utility::remove_final_comma($excluded_log);
    }
    $excluded_log .= ']';

    // Create data structure to describe the order to the payment provider.
    $payload = array(
      'externalBulkChargeId' => $this->get_unique_payment_id($month, $unique_id),
      'notifications' => array(
        'webHooks' => array(
          array(
            'eventName' => 'payment.charge.created.v2',
            'url' => Utility::get_nets_webhook_url(),
            'authorization' => $webhook_authorization
            // 'headers' => null
          ),
          array(
            'eventName' => 'payment.charge.failed',
            'url' => Utility::get_nets_webhook_url(),
            'authorization' => $webhook_authorization
            // 'headers' => null
          )
        )
      ),
      'subscriptions' => $payment_data
    );
    $payload = json_encode($payload);

    // If we are doing a simulation, return the compiled information without sending it to the payment provider.
    if ($simulation)
    {
      return '{"resultCode": ' . Result::NO_ACTION_TAKEN . ', "payload": ' . $payload . ', "excluded": ' .
        $excluded_log . '}';
    }

    // Compose and submit request to create a bulk charge.
    $request = curl_init($settings->get_nets_payment_url(true));
    curl_setopt_array($request, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $payload,
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $settings->get_nets_secret_key(),
        'content-type: application/*+json'
      )
    ));
    $response = curl_exec($request);

    // Check the response.
    $error = curl_error($request);
    $http_code = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
    curl_close($request);
    if (($http_code === false) || !is_numeric($http_code))
    {
      return '{"resultCode": ' . Result::REQUEST_FAILED .
        ', "errorMessage": "Error creating bulk payment: curl_getinfo failed to return HTTP response code."}';
    }
    if ($response === false)
    {
      return '{"resultCode": ' . Result::REQUEST_FAILED . ', "httpCode": ' . $http_code .
        ', "errorMessage": "' . $error . '"}';
    }
    $response = json_decode($response);
    $http_code = intval($http_code);
    switch ($http_code)
    {
      case 202:
        // HTTP 202 Accepted: The payment request was valid, and Nets will process the payments and notify the
        // web hook provided when done. Read the chargeId and paymentId for each order from Nets, and store the values
        // in the database.
        $payment_id_log = $this->read_and_store_payment_ids($orders, $response->bulkId, $settings);
        return '{"resultCode": ' . Result::OK . ', "httpCode": 202, "payload": ' . $payload .
          ', "excluded": ' . $excluded_log . ', "bulkId": "' . $response->bulkId .
          '", "readPaymentIds": ' . $payment_id_log . '}';
      case 400:
        // HTTP 400 Bad request: The request was not valid. Provide a description of the errors.
        return '{"resultCode": ' . Result::REQUEST_FAILED .
          ', "httpCode": 400, "errorMessage": "Nets bulk payment request failed with HTTP ' . $http_code .
          '" , "payload": ' . $payload . ', "excluded": ' . $excluded_log . ', "errors": ' .
          json_encode($response->errors) . '}';
      case 500:
        // HTTP 500 Internal server error: Something went wrong at Nets. Provide an error message and code for debugging
        // purposes.
        return '{"resultCode": ' . Result::REQUEST_FAILED .
          ', "httpCode": 500, "errorMessage": "Nets bulk payment request failed with HTTP ' . $http_code .
          '" , "payload": ' . $payload . ', "excluded": ' . $excluded_log . ', "message": "' .
          $response->message . '", "code": "' . $response->code . '", "source": "' . $response->source . '"}';
    }
    // The request failed with a different HTTP error code. Record it.
    return '{"resultCode": ' . Result::REQUEST_FAILED . ', "httpCode": ' . $http_code .
      ', "errorMessage": "Nets bulk payment request failed with HTTP ' . $http_code . '" , "payload": ' . $payload .
      ', "excluded": ' . $excluded_log . '}';
  }

  // *******************************************************************************************************************
  // Get the total amount for the given $order.
  public static function get_total_amount($order)
  {
    $total = 0;
    if (isset($order))
    {
      $line_count = self::get_integer('order_line_count', $order, 0);
      for ($i = 0; $i < $line_count; $i++)
      {
        $total += self::get_integer('line_' . $i . '_amount', $order, 0);
      }
    }
    return $total;
  }

  // *******************************************************************************************************************
  // Read the status of the bulk payment with the given $bulk_id from Nets. $settings should contain a Settings object
  // with settings for the current user group. $settings is optional. If not present, settings will be loaded. $skip is
  // the number of records to not retrieve. $take is the maximum number of records to retrieve, once the prescribed
  // number of records have been skipped.
  //
  // Return a string that holds a JSON object declaration to say what happened.
  //
  // See Nets documentation:
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-bulkid-get
  public function get_bulk_payment_status($bulk_id, $settings = null, $skip = 0, $take = 10000)
  {
    // Read settings, if required.
    if ($settings === null)
    {
      $settings = Settings_Manager::read_settings($this->access_token);
    }

    // Compose and submit request to get bulk charge status information.
    $request = curl_init($settings->get_nets_payment_url(true) . '/' . $bulk_id . '?skip=' . $skip . '&take=' . $take);
    curl_setopt_array($request, array(
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: ' . $settings->get_nets_secret_key()
      )
    ));
    $response = curl_exec($request);

    // Check the response.
    $error = curl_error($request);
    $http_code = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
    curl_close($request);
    if (($http_code === false) || !is_numeric($http_code))
    {
      return '{"resultCode": ' . Result::REQUEST_FAILED .
        ', "errorMessage": "Error reading bulk payment status: curl_getinfo failed to return HTTP response code."}';
    }
    if ($response === false)
    {
      return '{"resultCode": ' . Result::REQUEST_FAILED . ', "httpCode": ' . $http_code .
        ', "errorMessage": "' . $error . '"}';
    }
    $http_code = intval($http_code);
    switch ($http_code)
    {
      case 200:
        // HTTP 200 OK: The information request was valid, and Nets has provided the information. Return it.
        return $response;
      case 400:
        // HTTP 400 Bad request: The request was not valid. Provide a description of the errors.
        $response = json_decode($response);
        return '{"resultCode": ' . Result::REQUEST_FAILED .
          ', "httpCode": 400, "errorMessage": "Nets bulk payment information request failed with HTTP ' . $http_code .
          '" , "errors": ' . json_encode($response->errors) . '}';
      case 500:
        // HTTP 500 Internal server error: Something went wrong at Nets. Provide an error message and code for debugging
        // purposes.
        $response = json_decode($response);
        return '{"resultCode": ' . Result::REQUEST_FAILED .
          ', "httpCode": 500, "errorMessage": "Nets bulk payment information request failed with HTTP ' . $http_code .
          '" , "message": "' . $response->message . '", "code": "' . $response->code . '", "source": "' .
          $response->source . '"}';
    }
    // The request failed with a different HTTP error code. Record it.
    return '{"resultCode": ' . Result::REQUEST_FAILED . ', "httpCode": ' . $http_code .
      ', "errorMessage": "Nets bulk payment information request failed with HTTP ' . $http_code . '"}';
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Read the status of the bulk payment with the given $bulk_id from Nets. The status includes the paymentId and
  // chargeId, which connect the Nets payment to the order in the $orders list. Match them using the subscription ID,
  // and store the chargeId and paymentId for each order in the database. This can be used to identify the order later,
  // when status updates are received from Nets. $settings should contain a Settings object with settings for the
  // current user group.
  //
  // Return a string that holds a JSON object declaration to say what happened.
  //
  // See Nets documentation:
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-bulkid-get
  //
  // Each $orders item is expected to be an order, as returned by the get_payable_orders method. See that method for
  // more information.
    // *** // Update to call get_nets_bulk_payment_status, instead of making the request here.
  protected function read_and_store_payment_ids($orders, $bulk_id, $settings)
  {
    // Compose and submit request to get bulk charge status information. Try to get all the results in one request,
    // rather than using pagination.
    $request = curl_init($settings->get_nets_payment_url(true) . '/' . $bulk_id . '?skip=0&take=' . count($orders));
    curl_setopt_array($request, array(
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: ' . $settings->get_nets_secret_key()
      )
    ));
    $response = curl_exec($request);

    // Check the response.
    $error = curl_error($request);
    $http_code = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
    curl_close($request);
    if (($http_code === false) || !is_numeric($http_code))
    {
      return '{"resultCode": ' . Result::REQUEST_FAILED .
        ', "errorMessage": "Error storing paymentIds: curl_getinfo failed to return HTTP response code."}';
    }
    if ($response === false)
    {
      return '{"resultCode": ' . Result::REQUEST_FAILED . ', "httpCode": ' . $http_code .
        ', "errorMessage": "' . $error . '"}';
    }
    $response = json_decode($response);
    $http_code = intval($http_code);
    switch ($http_code)
    {
      case 200:
        // HTTP 200 OK: The information request was valid, and Nets has provided the information. Read the paymentId and
        // chargeId for each order, and store them in the database.
        $payment_id_log = $this->store_payment_ids($orders, $bulk_id, $response->page);
        return '{"resultCode": ' . Result::OK . ', "httpCode": 200, "storePaymentId": ' . $payment_id_log . '}';
      case 400:
        // HTTP 400 Bad request: The request was not valid. Provide a description of the errors.
        return '{"resultCode": ' . Result::REQUEST_FAILED .
          ', "httpCode": 400, "errorMessage": "Nets bulk payment information request failed with HTTP ' . $http_code .
          '" , "errors": ' . json_encode($response->errors) . '}';
      case 500:
        // HTTP 500 Internal server error: Something went wrong at Nets. Provide an error message and code for debugging
        // purposes.
        return '{"resultCode": ' . Result::REQUEST_FAILED .
          ', "httpCode": 500, "errorMessage": "Nets bulk payment information request failed with HTTP ' . $http_code .
          '" , "message": "' . $response->message . '", "code": "' . $response->code . '", "source": "' .
          $response->source . '"}';
    }
    // The request failed with a different HTTP error code. Record it.
    return '{"resultCode": ' . Result::REQUEST_FAILED . ', "httpCode": ' . $http_code .
      ', "errorMessage": "Nets bulk payment information request failed with HTTP ' . $http_code . '"}';
  }

  // *******************************************************************************************************************
  // Use the given $order_data to locate the paymentId and chargeId for each order in the $orders list, and store the
  // values as metadata lines in the database. The method also stores the bulk ID, and updates the payment status.
  // Return a string that holds a JSON object declaration to say what happened. Each $order_data item is expected to be
  // an array with the following fields:
  //
  //   subscriptionId : string      For instance: 2d079718b-ff63-45dd-947b-4950c023750f".
  //   paymentId : string           For instance: "472e651e-5a1e-424d-8098-23858bf03ad7".
  //   chargeId : string            For instance: "aec0aceb-a4db-49fb-b366-75e90229c640".
  //   status : "string"
  //   message : "string"
  //   code : "string"
  //   source : "string"
  //   externalReference : "string"
  //
  // For more information, see the Nets documentation:
  //   https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-bulkid-get
  //
  // Each $orders item is expected to be an order, as returned by the get_payable_orders method. See that method for
  // more information.
    // *** // The $order_data holds objects, not arrays.
  protected function store_payment_ids($orders, $bulk_id, $order_data)
  {
    $payment_id_log = '[';
    // Go through all the order data received from Nets.
    if (count($order_data) > 0)
    {
      foreach ($order_data as $order_data_item)
      {
        // Log the payment ID and charge ID.
        $payment_id_log .= '{"paymentId": "' . $order_data_item->paymentId . '", "chargeId": "' .
          $order_data_item->chargeId . '", ';
        // Locate the order with that payment ID.
        $order = self::get_order_with_payment_id($orders, $order_data_item->paymentId);
        if ($order === null)
        {
          // We couldn't find it, so try the charge ID.
          $order = self::get_order_with_charge_id($orders, $order_data_item->chargeId);
        }
        if ($order !== null)
        {
          // The order for this payment was found. Store the payment ID, charge ID and bulk ID in the database. Also,
          // set a flag on the order, so we know we found it.
          $payment_id_log .= '"orderId": ' . $order['id'] . ', "storePaymentIdResultCode": ';
          $order['data']['processed'] = true;
          $metadata = array(
            'payment_id' => $order_data_item->paymentId,
            'charge_id' => $order_data_item->chargeId,
            'bulk_id' => $bulk_id
          );
          if (self::add_metadata_for_order($order['id'], $metadata))
          {
            // The charge ID and bulk ID were stored as expected. Report success.
            $payment_id_log .= Result::OK . ', ';
          }
          else
          {
            // The charge ID and bulk ID were not stored as expected. Report error and move on to the next order.
            $payment_id_log .= Result::DATABASE_QUERY_FAILED . '},';
            continue;
          }
          
          // Update the payment status in the database to say that we are waiting for the update from Nets.
          $result_code = $this->set_payment_status($order['id'], Utility::PAYMENT_STATUS_NOT_PAID_CHARGE_REQUESTED);
          $payment_id_log .= '"updateStatusResultCode": ' . $result_code;
        }
        else
        {
          // The order was not found. Record the order ID as -1.
          $payment_id_log .= '"orderId": -1';
        }
        $payment_id_log .= '},';
      }

      // Log error for each order that did not receive a charge ID, and update the payment status to record the failure.
      foreach ($orders as &$order)
      {
        if (!self::get_value('processed', $order))
        {
          $result_code = $this->set_payment_status($order['id'], Utility::PAYMENT_STATUS_ERROR);
          $payment_id_log .= '{"orderId": ' . $order['id'] . ', "paymentId": "not found", "updateStatusResultCode": ' .
            $result_code . '},';
        }
      }
      $payment_id_log = Utility::remove_final_comma($payment_id_log);
    }
    $payment_id_log .= ']';
    return $payment_id_log;
  }

  // *******************************************************************************************************************
  // Combine the given $unique_id string with the user group ID and given $month, to form a unique payment ID that can
  // be passed to the payment provider.
  protected function get_unique_payment_id($month, $unique_id)
  {
    return $this->get_user_group_id() . '_' . $month . '_' . $unique_id;
  }

  // *******************************************************************************************************************
  // Return an array of orders that can be paid automatically for the given month. $month should be a string in the
  // format "yyyy-mm". This method does not do error checking on $month. An order is payable if it has not already been
  // paid, and uses Nets for payment.
  //
  // The returned array holds the following fields:
  //   id               The order ID.
  //   buyer_id         The ID of the customer who is paying for the order.
  //   subscription_id  The ID of the subscription to which the order applies.
  //   data             An array that holds key / value metadata items. Use the get_value and get_integer methods to
  //                    read individual metadata items.
  //
  // Metadata items are documented at the top of this file.
  protected function get_payable_orders($month)
  {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT 
          o.ID AS id,
          o.order_subscription_id AS subscription_id,
          o.order_buyer AS buyer_id,
          GROUP_CONCAT(CONCAT(om.meta_key, '>', om.meta_value) SEPARATOR '|') AS data
        FROM 
          {$this->database_table} o
        JOIN 
          subscription_ordermeta om1 ON o.ID = om1.order_id
        JOIN 
          subscription_ordermeta om2 ON o.ID = om2.order_id
        JOIN 
          subscription_ordermeta om3 ON o.ID = om3.order_id
        JOIN 
          subscription_ordermeta om ON o.ID = om.order_id
        WHERE 
          (o.order_owner = {$this->get_user_group_user_id()}) AND
          (om1.meta_key = 'period_month') AND
          (om1.meta_value = %s) AND
          (om2.meta_key = 'payment_method') AND
          (om2.meta_value = %d) AND
          (om3.meta_key = 'payment_status') AND
          (om3.meta_value = %d)
        GROUP BY 
          o.ID, o.order_subscription_id, o.order_buyer;
      ",
      $month,
      Utility::PAYMENT_METHOD_NETS,
      Utility::PAYMENT_STATUS_NOT_PAID
    );
    $results = $wpdb->get_results($sql, ARRAY_A);
    if (!is_array($results))
    {
      return array();
    }

    // Parse metadata.
    self::parse_concatenated_metadata($results);
    return $results;
  }

  // *******************************************************************************************************************
  // For the given set of database query $results, turn a concatenated string of metadata into a PHP array with keys and
  // values. The $results will be updated.
  protected static function parse_concatenated_metadata(&$results)
  {
    foreach ($results as &$result)
    {
      $data = array();
      $pairs = explode('|', $result['data']);
      foreach ($pairs as $pair)
      {
        list($key, $value) = explode('>', $pair);
        $data[$key] = $value;
      }
      $result['data'] = $data;
    }
  }

  // *******************************************************************************************************************
  // Return a PHP array that holds the information that goes into the "items" section of the order data in a request to
  // the Nets payment provider. Information is drawn from the given order. If the order has no lines, in which case no
  // payment can be created, return an empty array. It is up to the caller to handle this case. The total amount will be
  // returned in the $total field.
  //
  // Each entry in the returned array will be another array with the following fields:
  //   reference : string
  //   name : string
  //   quantity : integer
  //   unit : string
  //   unitPrice : integer
  //   grossTotalAmount : integer
  //   netTotalAmount : integer
    // *** // Document contents.
  protected static function get_payment_items_from_order($order, &$total)
  {
    $total = 0;
    $order_line_count = self::get_integer('order_line_count', $order, 0);
    $items = array();
    for ($i = 0; $i < $order_line_count; $i++)
    {
      // The payment provider wants the sum to include the fraction. Multiply by 100, assuming that the selected
      // currency uses hundredths of a unit.
      $price = self::get_integer("line_{$i}_amount", $order, 0);
      if ($price < 0)
      {
        // This price was negative. That should never happen. Move on to the next line.
        error_log('Error while compiling payment items for order ' . $order['id'] . ': Negative price on line ' . $i);
        continue;
      }
      $price *= 100;
        // *** // Also set optional fields taxRate : integer and taxAmount : integer.
      $items[] = array(
        'reference' => strval(self::get_integer("line_{$i}_id", $order, -1)),
        'name' => self::get_value("line_{$i}_text", $order),
        'quantity' => (float) 1,
        'unit' => 'month',
        'unitPrice' => $price,
        'grossTotalAmount' => $price,
        'netTotalAmount' => $price
      );
      $total += $price;
    }
    return $items;
  }

  // *******************************************************************************************************************
  // Return a PHP array that holds the information that goes into the "order" section of the payload in a request to the
  // Nets payment provider. Information is drawn from the given $order. Return null if the order should not be included.
  // This could be because the order had no lines, or the total price was negative, in which case no payment can be
  // created. If the method returns null, the $message will say why. The returned order data can be used both for bulk
  // and individual payments.
  //
  // The returned order has the following fields:
  //
  //   items : array          An array of order items, as returned by the get_payment_items_from_order method.
  //   amount : integer       // *** //
  //   currency : string      // *** //
  //   reference : string     // *** //
  protected static function get_order_data($order, &$message)
  {
    $total = 0;
    $items = self::get_payment_items_from_order($order, $total);
    if (count($items) <= 0)
    {
      $message = 'The order with ID ' . $order['id'] . ' had no valid lines.';
      return null;
    }
    if ($total <= 0)
    {
      $message = 'The total amount for the order with ID ' . $order['id'] . ' was zero or negative.';
      return null;
    }

    return array(
      'items' => $items,
      'amount' => $total,
        // *** // Permit various currencies.
      'currency' => 'NOK',
      'reference' => $order['subscription_id'] . ' ' . $order['id']
    );
  }
      
  // *******************************************************************************************************************
  // Parse a set of result rows, and return an array of orders, with metadata neatly gathered in the data table. If
  // $results is null, or anything other than an array, return an empty array.
  protected static function parse_orders($results)
  {
    // Create a table where all keys for a particular order are gathered.
    $orders = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $result_row)
      {
        $order_id = $result_row['id'];
        // Add the order if it does not already exist.
        if (!isset($orders[$order_id]))
        {
          $orders[$order_id] = array(
            'id' => $order_id,
            'buyer_id' => $result_row['buyer_id'],
            'data' => array()
          );
        }
        // Add a key / value pair. If the value already existed, it will be overwritten.
        if (isset($result_row['key']))
        {
          $orders[$order_id]['data'][$result_row['key']] = $result_row['value'];
        }
      }
    }
    return $orders;
  }

  // *******************************************************************************************************************
  // Add metadata for the order with the given ID to the database. The key / value pairs to be added are given in the
  // $metadata array. Return true if all the entries were successfully inserted.
  protected static function add_metadata_for_order($id, $metadata)
  {
    global $wpdb;

    if (!is_numeric($id))
    {
      return false;
    }
    $id = intval($id);

    if (Utility::non_empty_array($metadata))
    {
      $values = Utility::get_key_value_data_string($id, $metadata);
      $result = $wpdb->query("
        INSERT INTO
          subscription_ordermeta (order_id, meta_key, meta_value)
        VALUES
          {$values};
      ");
      if ($result === false)
      {
        error_log("Error while inserting metadata for order {$id}: {$wpdb->last_error}. Tried to insert metadata: {$values}");
        return false;
      }
      if ($result !== count($metadata))
      {
        error_log("Failed to insert the correct number of metadata lines for order {$id}. Expected: {count($metadata)}. Actual: {$result}. Tried to insert metadata: {$values}");
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return the given $target_date, which should be a string with the format "yyyy-mm-dd". However, if the target date
  // is less than 14 days from now, instead return a date which is 14 days from the current date. $target_date is optional.
  // If not provided, the method always returns a date 14 days from the current date.
  protected static function get_pay_by_date($target_date = null)
  {
    // Create a date that holds the current moment. Remove the time, up the date by 14 days and format it as
    // "yyyy-mm-dd".
    $date = new DateTime();
    $date->setTime(0, 0, 0);
    $date->modify('+14 days');
    $two_weeks_from_now = $date->format('Y-m-d');
    // If a target date is provided, and it is sufficiently far in the future, use that.
    if (Utility::is_valid_date($target_date) && ($target_date >= $two_weeks_from_now))
    {
      return $target_date;
    }
    // There was no target date, or it is too soon. Return a date 14 days from now.
    return $two_weeks_from_now;
  }

  // *******************************************************************************************************************
  // Based on the given subscription start date, return a date that is three years in the future, or null if the given
  // $start_date string was invalid. $start_date can be either a DateTime object, or a string in the format
  // "yyyy-mm-dd".
  protected static function get_subscription_renewal_date($start_date)
  {
    // Parse the start date.
    if ($start_date instanceof DateTime)
    {
      $renewal_date = clone $start_date;
    }
    else
    {
      $renewal_date = Utility::string_to_date($start_date);
    }

    // Calculate the renewal date.
    if (isset($renewal_date))
    {
      $renewal_date->modify('+3 years');
      return $renewal_date;
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return the appropriate payment method for the next order, based on the given $previous_order. Use the
  // PAYMENT_METHOD_ constants. If the payment method was not found, return PAYMENT_METHOD_UNKNOWN.
  //
  // The payment method is simply copied from the previous order, unless the payment method was
  // PAYMENT_METHOD_NETS_THEN_INVOICE. If so, the next order will have PAYMENT_METHOD_INVOICE.
  protected static function get_next_payment_method($previous_order)
  {
    $payment_method = self::get_integer('payment_method', $previous_order, Utility::PAYMENT_METHOD_UNKNOWN);
    if (Utility::is_valid_payment_method($payment_method))
    {
      if ($payment_method === Utility::PAYMENT_METHOD_NETS_THEN_INVOICE)
      {
        return Utility::PAYMENT_METHOD_INVOICE;
      }
      return $payment_method;
    }
    return Utility::PAYMENT_METHOD_UNKNOWN;
  }

  // *******************************************************************************************************************
  // Return true if a buyer, when starting a subscription on the given start date, should pay for the first month
  // only. If the method returns false, he should pay for both the first month and the next one.
  //
  // At the moment, the buyer will pay for both months unless the subscription is started on the first of the month.
  protected static function pay_for_first_month_only($start_date)
  {
    $day = (int) $start_date->format('d');
    return $day === 1;
  }

  // *******************************************************************************************************************
  // From the given $order, return the metadata value with the given $key, or null if it was not found.
  protected static function get_value($key, $order)
  {
    if (isset($order['data'][$key]))
    {
      return $order['data'][$key];
    }
    return null;
  }

  // *******************************************************************************************************************
  // From the given $order, return the metadata with the given $key. The metadata value is converted to an integer. If
  // it was not a valid integer, return the given $default_value instead.
  protected static function get_integer($key, $order, $default_value = null)
  {
    if (isset($order['data'][$key]) && is_numeric($order['data'][$key]))
    {
      return intval($order['data'][$key]);
    }
    return $default_value;
  }

  // *******************************************************************************************************************
  // From the given list of $orders, return the (first) one with the given $payment_id, or null if no such order was
  // found.
  protected static function get_order_with_payment_id($orders, $payment_id)
  {
    // Note that we use the reference to the order. That way, the caller can modify it.
    foreach ($orders as &$order)
    {
      if ($order['data']['nets_payment_id'] === $payment_id)
      {
        return $order;
      }
    }
    return null;
  }

  // *******************************************************************************************************************
  // From the given list of $orders, return the (first) one with the given $charge_id, or null if no such order was
  // found.
  protected static function get_order_with_charge_id($orders, $charge_id)
  {
    // Note that we use the reference to the order. That way, the caller can modify it.
    foreach ($orders as &$order)
    {
      if ($order['data']['nets_charge_id'] === $charge_id)
      {
        return $order;
      }
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return true if the subscription with the given $subscription_id has an order that covers the given $month. $month
  // is a string with the format "yyyy-mm". This method performs no error checking on its input parameters. Note that
  // this method only checks the period_month field; if the order covers several months, this method will only return
  // true for the last month that the order covers. If an error occurred, the method will return false.
  protected function has_order_for_month($subscription_id, $month)
  {
    global $wpdb;

    $sql = $wpdb->prepare("
      SELECT
        1
      FROM
        {$this->database_table} o
      JOIN
        subscription_ordermeta om ON om.order_id = o.ID
      WHERE
        (o.order_subscription_id = %d) AND
        (om.meta_key = 'period_month') AND
        (om.meta_value = %s)
      LIMIT
        1;
    ", $subscription_id, $month);
    $results = $wpdb->get_results($sql, ARRAY_A);
    return Utility::array_with_one($results);
  }

  // *******************************************************************************************************************
  // For the subscription with the given $subscription_id, return the most recent order before the given $month, which
  // should be a string with the format "yyyy-mm". For instance, if you pass "2023-10", the method will look for the
  // order that covers 2023-09. If not found, it will look for the order for 2023-08, and so on. The method will at most
  // go 12 months back in time. If no order was found, the method returns null.
  protected function get_previous_order($subscription_id, $month)
  {
    global $wpdb;

    $search_month = $month;
    for ($i = 0; $i < 12; $i++)
    {
      // Move to the previous month.
      $search_month = Utility::get_previous_month($search_month);
      // Find the ID of the order for that month, if it exists.
      $sql = $wpdb->prepare("
          SELECT 
            o.ID AS id
          FROM
            {$this->database_table} o
          JOIN
            subscription_ordermeta om ON om.order_id = o.ID
          WHERE
            (o.order_subscription_id = %d) AND
            (om.meta_key = 'period_month') AND
            (om.meta_value = %s)
        ",
        $subscription_id, $search_month
      );
      $results = $wpdb->get_results($sql, ARRAY_A);
      if (Utility::array_with_one($results))
      {
        // The order for that month existed. Read the entire order.
        return $this->read_order($results[0]['id']);
      }
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return an array that holds the metadata for an order, except for the payment provider data and order lines. These
  // will have to be added later.
  protected function get_order_metadata($order_title, $pay_by_date, $payment_method, $buyer, $start_date, $end_date,
    $month, $renewal_date)
  {
    // If we need to send an invoice, record that it has not been sent. Otherwise, just state that the order has not yet
    // been paid.
    if ($payment_method === Utility::PAYMENT_METHOD_INVOICE)
    {
      $payment_status = Utility::PAYMENT_STATUS_NOT_PAID_NO_INVOICE_SENT;
    }
    else
    {
      $payment_status = Utility::PAYMENT_STATUS_NOT_PAID;
    }

    // Create an array that holds the order metadata. The metadata goes in a separate table to the order itself. The
    // order date is set to today's date.
    $metadata = array(
      'order_title' => $order_title,
      'order_date' => Utility::get_today(),
      'order_pay_by_date' => $pay_by_date,
      'name' => $buyer['name'],
      'billing_phone' => $buyer['phone'],
      'billing_email' => $buyer['email'],
      'billing_address' => $buyer['address'],
      'billing_postcode' => $buyer['postcode'],
      'billing_city' => $buyer['area'],
      'profile_type' => ($buyer['entity_type'] === Utility::ENTITY_TYPE_COMPANY ? 'company' : 'personal'),
      'payment_method' => $payment_method,
      'payment_status' => $payment_status,
      'period_month' => $month,
      'period_start' => $start_date,
      'period_end' => $end_date,
      'renewal_date' => $renewal_date
    );
    if ($buyer['entity_type'] === Utility::ENTITY_TYPE_COMPANY)
    {
      $metadata['company_number'] = $buyer['company_id_number'];
    }
    return $metadata;
  }

  // *******************************************************************************************************************
  // Add an order line to the provided order $metadata. Also, increase the line index in preparation for the next line,
  // and update the line count in $metadata.
  protected static function add_order_line(&$metadata, &$line_index, $id, $description, $amount)
  {
    $metadata['line_' . $line_index . '_id'] = $id;
    $metadata['line_' . $line_index . '_text'] = $description;
    $metadata['line_' . $line_index . '_amount'] = $amount;
    $line_index++;
    $metadata['order_line_count'] = $line_index;
  }

  // *******************************************************************************************************************
  // Create an order data item for the given $subscription and with the given $metadata. The metadata is included in the
  // data item, but the create method will extract it and store it separately.
  protected static function get_order_data_item($subscription, $metadata)
  {
    return array(
      'order_buyer' => $subscription['buyer_id'],
      'order_owner' => $subscription['owner_id'],
      'order_subscription_id' => $subscription['subscription_id'],
      'updated_at' => current_time('mysql'),
      'data' => $metadata
    );
  }

  // *******************************************************************************************************************
  // Return a string with a Javascript table declaration that holds the orders given in $results. Use the c.ord column
  // constants.
  protected static function export_orders_as_javascript($results)
  {
    $table = "[";
    if (!empty($results))
    {
      foreach ($results as $order)
      {
        $table .= "[";
        $table .= strval($order['id']);
        $table .= ", ";
        $table .= strval($order['buyer_id']);
        $table .= ", ";
        $table .= strval($order['subscription_id']);
        $table .= ", ";
        $table .= strval($order['location_id']);
        $table .= ", '";
        $table .= strval($order['product_name']);
        $table .= "', ";
        $table .= strval(self::get_total_amount($order));
        $table .= ", '";
        $table .= strval($order['created_at']);
        $table .= "', [";
        if (count($order['data']) > 0)
        {
          foreach($order['data'] as $key => $value)
          {
            $table .= "['";
            $table .= $key;
            $table .= "', '";
            $table .= $value;
            $table .= "'],";
          }
          $table = Utility::remove_final_comma($table);
        }
        $table .= "]],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

// *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_subscription_id()
  {
    return $this->subscription_id;
  }

  // *******************************************************************************************************************

  public function set_subscription_id($new_value)
  {
    if (is_numeric($new_value))
    {
      $new_value = intval($new_value);
      if ($new_value >= -1)
      {
        $this->subscription_id = $new_value;
      }
    }
  }

  // *******************************************************************************************************************
}
?>