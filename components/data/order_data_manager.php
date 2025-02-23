<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_user_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/subscription_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_plan_data_manager.php';

/*
Orders are stored in a meta information table in the database. It holds a set of key / value pairs. At the moment, we
use the following keys:

------------------------------------------------------------------------------------------------------------------------
Order information
------------------------------------------------------------------------------------------------------------------------

order_id		        integer		      This is actually not stored as a key/value pair. Use the database ID instead.
order_title		      string          Headline which explains what the order is for.
order_date		      date            The date when the order was created. String with the format "yyyy-mm-dd".
order_pay_by_date		date            The date by which the order needs to be paid. String with the format "yyyy-mm-dd".
order_line_count	  integer		      The number of order lines (see below).

------------------------------------------------------------------------------------------------------------------------
Payment information
------------------------------------------------------------------------------------------------------------------------

payment_method		  integer		      Values (use the PAYMENT_METHOD_ constants):
					                              0  	Customer manual payment
					                              1   Dibs Easy (Nets)
payment_status		  integer	      	Values:
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
period_month        date            The month covered by this order, as a string with the format "yyyy-mm", or null if
                                    not relevant. If the order covers more than one month, the value will be the last
                                    month covered. For instance, if the period_start is 2024-04-17 and the period_end is
                                    2024-05-31, the period_month will be 2024-05.
period_start        date            The start of the time period covered by this order, as a string with the format
                                    "yyyy-mm-dd", or null if not relevant.
period_end          date            The end of the time period covered by this order, as a string with the format
                                    "yyyy-mm-dd", or null if not relevant.
renewal_date        date            The date on which the subscription expires, and must be re-confirmed by the user in
                                    order to keep running. If not set, the subscription never expires until cancelled.
                                    This value may be required by some payment providers, who do not support indefinite
                                    subscriptions.

For Dibs Easy (Nets):

nets_payment_id		  integer
nets_charge_id		  integer
nets_date_paid		  date
nets_payment_method	string

------------------------------------------------------------------------------------------------------------------------
Order lines
------------------------------------------------------------------------------------------------------------------------

line_N_id           integer         The database ID of the product being charged in this line.
line_N_text		      string		      Description of what the charge is for. N is zero based.
line_N_amount		    integer		      Amount (we'll ignore VAT for now). N is zero based.

------------------------------------------------------------------------------------------------------------------------
*/

// The Order_Data_Manager manipulates orders for a single user at a time. Set the user_id property to control which one,
// or leave it at -1 in order to use the currently logged-on user.
//
// Furthermore, the Order_Data_Manager manipulates orders for a single subscription at a time. Set the subscription_id
// property to choose which one. The data manager will not work otherwise.
class Order_Data_Manager extends Single_User_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************

  protected $subscription_id = -1;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    // The order data manager does not provide any services that can be accessed from the client, but you can call its
    // methods directly, from the server.
    $this->database_table = 'subscription_order';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all orders connected to the user whose ID is stored in this class from the database. Return them as a string
  // containing JSON data.
  //
    // *** // Return payment method as an integer, to deal with translation. How to get it from Nets?
  // The array holds the following fields:
  //   id : integer
  //   name : string
  //   order_date : date (string in the format "yyyy-mm-dd")
  //   pay_by_date : date (string in the format "yyyy-mm-dd")
  //   payment_method : string
  //   payment_status : integer
  //   payment_info : object (information from the payment provider; differs between providers)
  //   order_lines : array with the following fields:
  //                   id : integer
  //                   text : string
  //                   amount : integer
  //   open : boolean (always false)

  public function get_orders()
  {
    $today_date = date("Y-m-d");
    global $wpdb;
    $query = "
      SELECT 
        o.*,
        om.meta_key AS `key`,
        om.meta_value AS `value`
      FROM
        {$this->database_table} o
      LEFT JOIN
        subscription_ordermeta om ON om.order_id = o.ID
      LEFT JOIN
         subscriptions so ON so.id = o.order_subscription_id   
      WHERE
        so.active != 0 AND
        om.meta_key = 'period_end' AND
        om.meta_value < %s";
    return $results = $wpdb->get_results($wpdb->prepare($query, $today_date), ARRAY_A);
    
  }
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
        om.meta_key AS `key`,
        om.meta_value AS `value`
      FROM
        {$this->database_table} o
      LEFT JOIN
        subscription_ordermeta om ON om.order_id = o.ID
      WHERE
        order_owner = {$this->get_user_group_user_id()} AND
        order_buyer = {$this->get_user_id()} AND
        order_subscription_id = {$this->get_subscription_id()};
    ";
    $results = $wpdb->get_results($query, ARRAY_A);

    // Create a table where all keys for a particular order are gathered.
    $orders = self::parse_orders($results);

    $table = '[';
    if (count($orders) > 0)
    {
      foreach ($orders as $order)
      {
        $nets = self::get_integer('payment_method', $order, Utility::PAYMENT_METHOD_UNKNOWN) ===
          Utility::PAYMENT_METHOD_NETS;
        $table .= '[';
        $table .= strval($order['id']);
        $table .= ', "';
        $table .= self::get_value('order_title', $order);
        $table .= '", "';
        $table .= self::get_value('order_date', $order);
        $table .= '", "';
        $table .= self::get_value('order_pay_by_date', $order);
        $table .= '", "';
        if ($nets)
        {
          $table .= self::get_value('nets_payment_method', $order);
        }
        $table .= '", ';
        $table .= self::get_integer('payment_status', $order, 0);
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
          // Remove final comma.
          $table = substr($table, 0, -1);
        }
        $table .= '], false';
        $table .= '],';
      }
      // Remove final comma.
      $table = substr($table, 0, -1);
    }
    $table .= ']';
    return $table;
  }

  // *******************************************************************************************************************
  // Read the order with the given $id from the database. Return null if it was not found. This method does not require
  // you to set any user or subscription ID properties.
  //
  // The returned order has the following fields:
  //   id             The order ID
  //   data           An array that holds key / value metadata items. Use the get_value and get_integer methods to read
  //                  individual metadata items.
  public function read_order($id)
  {
    global $wpdb;

    $query = "
      SELECT 
        o.ID AS id,
        om.meta_key AS `key`,
        om.meta_value AS `value`
      FROM
        {$this->database_table} o
      LEFT JOIN
        subscription_ordermeta om ON om.order_id = o.ID
      WHERE
        o.ID = {$id};
    ";

    $results = $wpdb->get_results($query, ARRAY_A);

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
  // Create an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_create method.
  //
  // The item to be created can be passed as a parameter. If not, it will be read from the request.
  //
  // Override to add data to the ordermeta table, once the order has been created.
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

    // All operations succeeded.
    $wpdb->query('COMMIT');
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
    if (!isset($id))
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
      $wpdb->query('COMMIT');
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
  // will only charge for the whole of the current month. The order will charge the user for both insurance and storage
  // room rent. Return a result code to say what happened.
  public function create_initial_order_from_subscription()
  {
    // Read the subscription for which the order will be created.
    $subscription_data = new Subscription_Data_Manager($this->access_token);
    $subscription_data->set_user_id($this->get_user_id());
    $subscription = $subscription_data->read_subscription($this->get_subscription_id());
    if (!isset($subscription))
    {
      return Result::UNABLE_TO_CREATE_ORDER;
    }

    // The buyer will pay both rent and insurance. Find the price plans that determine the prices for each.
    $product_price_plan = Price_Plan_Data_Manager::get_product_price_plan($subscription);
    $insurance_price_plan = Price_Plan_Data_Manager::get_insurance_price_plan($subscription);

    // Find the prices per month for the first month, based on the subscription start date.
    $rent_per_month = Price_Plan_Data_Manager::get_price_from_price_plan(
      $product_price_plan, $subscription['start_date']);
    $insurance_per_month = Price_Plan_Data_Manager::get_price_from_price_plan(
      $insurance_price_plan, $subscription['start_date']);
    // Find the first and last month that is being paid for now. Unless the buyer pays the first month only, the last
    // month is one month later than the first one. Both months are formatted as "yyyy-mm".
    $start_date = Utility::string_to_date($subscription['start_date']);
    $first_month = $start_date->format('Y-m');
    $last_month = clone $start_date;
    $first_month_only = self::pay_for_first_month_only($start_date);
    if (!$first_month_only)
    {
      $last_month->modify('+1 month');
    }
    $last_month = $last_month->format('Y-m');

    // Create an array that holds the order metadata. This goes in a separate table.
    $metadata = array(
      'order_title' => 'Initial payment, ' . $last_month,
      'order_date' => date('Y-m-d'),
      'order_pay_by_date' => self::get_pay_by_date(),
      'payment_method' => Utility::PAYMENT_METHOD_NETS,
      'payment_status' => 1,
      'period_month' => $last_month,
      'period_start' => Utility::date_to_string($start_date),
      'period_end' => Utility::get_last_date($last_month),
      'renewal_date' => self::get_subscription_renewal_date($start_date)->format('Y-m-d')
      // The payment provider data will be added later.
    );
    if ($first_month_only)
    {
      // The buyer is only paying for the first month. We'll have one line for the rent, and one for the insurance.
      $metadata['order_line_count'] = 2;
      $metadata['line_0_id'] = $subscription['product_id'];
      $metadata['line_0_text'] = 'Rent, ' . $first_month;
      $metadata['line_0_amount'] = $rent_per_month;
      $metadata['line_1_id'] = $subscription['insurance_id'];
      $metadata['line_1_text'] = 'Insurance, ' . $first_month;
      $metadata['line_1_amount'] = $insurance_per_month;
    }
    else
    {
      // The buyer is paying for both the first and the next month. We'll have rent and insurance for the first month,
      // then rent and insurance for the second month. For the first month, the user pays only from the start date until
      // the end of the month.
      $days_in_month = Utility::get_days_in_month($start_date);
      $days_left = $days_in_month - intval($start_date->format('d')) + 1;
      $month_fraction = $days_left / $days_in_month;
      $metadata['order_line_count'] = 4;
      $metadata['line_0_id'] = $subscription['product_id'];;
      $metadata['line_0_text'] = 'Rent, ' . $first_month;
      $metadata['line_0_amount'] = floor($month_fraction * $rent_per_month);
      $metadata['line_1_id'] = $subscription['insurance_id'];
      $metadata['line_1_text'] = 'Insurance, ' . $first_month;
      $metadata['line_1_amount'] = floor($month_fraction * $insurance_per_month);
      // Find the prices for the last month, based on the first day of the last month.
      $rent_per_month = Price_Plan_Data_Manager::get_price_from_price_plan($product_price_plan, $last_month . '-01');
      $insurance_per_month = Price_Plan_Data_Manager::get_price_from_price_plan(
        $insurance_price_plan, $last_month . '-01');
      $metadata['line_2_id'] = $subscription['product_id'];;
      $metadata['line_2_text'] = 'Rent, ' . $last_month;
      $metadata['line_2_amount'] = $rent_per_month;
      $metadata['line_3_id'] = $subscription['insurance_id'];
      $metadata['line_3_text'] = 'Insurance, ' . $last_month;
      $metadata['line_3_amount'] = $insurance_per_month;
    }

    // Create the order data item. The metadata is included, but the create method will deal with that.
    $order = array(
      'order_buyer' => $subscription['buyer_id'],
      'order_owner' => $subscription['owner_id'],
      'order_subscription_id' => $subscription['subscription_id'],
      'updated_at' => current_time('mysql'),
      'data' => $metadata
    );
    
    // Create the order.
    return $this->create($order);
  }

  // *******************************************************************************************************************
  // Contact the payment provider, and initiate a payment for the order with the given ID. $order_id is optional - if
  // not present, the ID of the last created order will be used. Return an object that holds the payment ID created by
  // the payment provider. This can be returned to the client, which will be able to use it when communicating with the
  // payment provider. $order_id is the database ID of the order that is being paid for. This method assumes that no
  // subscription has been created yet. Subsequent orders can be charged without involving the buyer.
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
      error_log('Failed to create payment: order ID not passed');
      return null;
    }
    // Read the order for which the payment should be created.
    $order = $this->read_order($order_id);
    if (!isset($order) || ($order === false))
    {
      error_log('Failed to create payment: order with ID {$order_id} not found');
      return null;
    }

    // Create a payment item for each order line, and sum up the amount to be charged.
    $total = 0;
    $order_line_count = self::get_integer('order_line_count', $order, 0);
    $items = array();
    for ($i = 0; $i < $order_line_count; $i++)
    {
      // The payment provider wants the sum to include the fraction. Multiply by 100, assuming that the selected
      // currency uses hundredths of a unit.
      $price = self::get_integer("line_{$i}_amount", $order, 0) * 100;
      $items[] = array(
        'reference' => self::get_integer("line_{$i}_id", $order, -1),
        'name' => self::get_value("line_{$i}_text", $order),
        'quantity' => 1,
        'unit' => 'day',
        'unitPrice' => $price,
        'grossTotalAmount' => $price,
        'netTotalAmount' => $price
      );
      $total += $price;
    }
    $settings = Settings_Manager::read_settings($this->access_token);

    // Create data structure to describe the subscription to the payment provider.
    $payload = array(
      'checkout' => array(
        'integrationType' => 'EmbeddedCheckout',
        'url' => Utility::get_domain() . '/subscription/html/pay.php',
        'termsUrl' => Utility::get_terms_url(),
        'charge' => true
      ),
      'subscription' => array(
        'interval' => 0,
        'endDate' => self::get_subscription_renewal_date(self::get_value('order_date', $order))->format('Y-m-d\TH:i:sP')
      ),
      'order' => array(
        'items' => $items,
        'amount' => $total,
          // *** // Permit various currencies.
        'currency' => 'NOK',
        'reference' => strval(self::get_integer('order_id', $order, -1))
      ),
      'notifications' => array(
        'webHooks' =>[
          array(
            'eventName' => "payment.charge.created.v2",
            'url' => Utility::get_domain() . '/subscription/html/notify.php',
            "authorization" => $settings->get_nets_secret_key(),
            "headers" => null
          ),
          array(
            'eventName' => "payment.charge.failed",
            'url' => Utility::get_domain() . '/subscription/html/notify.php',
            "authorization" => $settings->get_nets_secret_key(),
            "headers" => null
          ),
          array(
            'eventName' => "payment.created",
            'url' => Utility::get_domain() . '/subscription/html/notify.php',
            "authorization" => $settings->get_nets_secret_key(),
            "headers" => null
          )
        ]
      )
    );


    //echo "<pre>"; print_r($payload); die;

    // Create and submit a request to the payment provider.
    
    if ($settings->get_application_role() === Settings::APP_ROLE_PRODUCTION)
    {
      $url = Utility::NETS_API_URL_PROD;
    }
    else
    {
      $url = Utility::NETS_API_URL_TEST;
    }
    $request = curl_init($url);
    curl_setopt($request, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: ' . $settings->get_nets_secret_key())
    );
    return json_decode(curl_exec($request));
  }

  // *******************************************************************************************************************
  // See whether the given payment data were valid. Report any errors, and return a result code. If the method returns
  // Result::OK, the payment ID can be retrieved from $payment_data.
  public static function parse_payment_data($payment_data)
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
  // Store the given $payment_id on the order with the given $order_id. $order_id is optional - if not present, the ID
  // of the last created order will be used. Return a result code that says whether the operation succeeded.
  public function store_payment_id($payment_id, $order_id = null)
  {
    // Read the order ID for which the payment ID should be stored. If it wasn't passed, try the last created order. If
    // that is not present either, report an error.
    if (!isset($order_id))
    {
      $order_id = $this->get_created_item_id();
    }
    if (!isset($order_id))
    {
      error_log('Failed to store payment ID ({$payment_id}): order ID not passed');
      return Result::MISSING_INPUT_FIELD;
    }

    // Store the value by adding a piece of metadata.
    if (self::add_metadata_for_order($order_id, array('nets_payment_id' => $payment_id)))
    {
      return Result::OK;
    }
    error_log('Failed to store payment ID ({$payment_id}) for order {$order_id}');
    return Result::DATABASE_QUERY_FAILED;
  }

  // *******************************************************************************************************************
  // Set the payment status for the order with the given $order_id to the given value. $new_value should be an integer.
  // Return a result code that says whether the operation was successful.
  public function set_payment_status($order_id, $new_value)
  {
    global $wpdb;

    // Ensure $order_id and $new_value were provided.
    if (!isset($order_id) || !is_numeric($order_id) || !isset($new_value) || !is_numeric($new_value))
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
  // *** Protected methods.
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
  // Add metadata for the order with the given ID. The key / value pairs to be added are given in the $metadata array.
  // Return true if all the entries were successfully inserted.
  protected static function add_metadata_for_order($id, $metadata)
  {
    global $wpdb;

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
  // Return a date, as a string with the format "yyyy-mm-dd", which is 14 days from the current date.
  protected static function get_pay_by_date()
  {
    // Create a date that holds the current moment. Remove the time, then up the date by 14 days. Finally, return the
    // resulting date in the desired format.
    $pay_by_date = new DateTime();
    $pay_by_date->setTime(0, 0, 0);
    $pay_by_date->modify('+14 days');
    return $pay_by_date->format('Y-m-d');
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
  // From the given $order, return the metadata with the given $key.
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
    if (is_numeric($order['data'][$key]))
    {
      return intval($order['data'][$key]);
    }
    return $default_value;
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
  public function create_monthly_payment($order_id = null)
  {
   
    // Read the order ID for which the payment should be created. If it wasn't passed, try the last created order. If
    // that is not present either, we cannot create a payment.
    if (!isset($order_id))
    {
      $order_id = $this->get_created_item_id();
    }
    if (!isset($order_id))
    {
      error_log('Failed to create payment: order ID not passed');
      return null;
    }
    // Read the order for which the payment should be created.
    $order = $this->read_order($order_id);
    
    if (!isset($order) || ($order === false))
    {
      error_log('Failed to create payment: order with ID {$order_id} not found');
      return null;
    }

    // Create a payment item for each order line, and sum up the amount to be charged.
    $total = 0;
    $order_line_count = self::get_integer('order_line_count', $order, 0);
    $items = array();
    for ($i = 0; $i < $order_line_count; $i++)
    {
      // The payment provider wants the sum to include the fraction. Multiply by 100, assuming that the selected
      // currency uses hundredths of a unit.
      $price = self::get_integer("line_{$i}_amount", $order, 0) * 100;
      $items[] = array(
        'reference' => self::get_integer("line_{$i}_id", $order, -1),
        'name' => self::get_value("line_{$i}_text", $order),
        'quantity' => 1,
        'unit' => 'day',
        'unitPrice' => $price,
        'grossTotalAmount' => $price,
        'netTotalAmount' => $price
      );
      $total += $price;
    }
    
    $settings = Settings_Manager::read_settings($this->access_token);

    // Create data structure to describe the subscription to the payment provider.
    $payload = array(
      'externalBulkChargeId' => "3ed0492879fc49759b827367f43bd15a",
      'subscriptions' => [
        array(
          "subscriptionId" => "f1eb563455d443f68204cd230e40afcc",
          'order' => array(
            'items' => $items,
            'amount' => $total,
              // *** // Permit various currencies.
            'currency' => 'NOK',
            'reference' => strval(self::get_integer('order_id', $order, -1))
          ),
        )
      ],
      'notifications' => array(
        'webHooks' =>[
          array(
            'eventName' => "payment.charge.created.v2",
            'url' => Utility::get_domain() . '/subscription/html/notify.php',
            "authorization" => $settings->get_nets_secret_key(),
            "headers" => null
          ),
          array(
            'eventName' => "payment.charge.failed",
            'url' => Utility::get_domain() . '/subscription/html/notify.php',
            "authorization" => $settings->get_nets_secret_key(),
            "headers" => null
          ),
          array(
            'eventName' => "payment.created",
            'url' => Utility::get_domain() . '/subscription/html/notify.php',
            "authorization" => $settings->get_nets_secret_key(),
            "headers" => null
          )
        ]
      )
    );
    //echo "<pre>"; print_r($settings->get_nets_secret_key()); die("dfdf");
    


    //echo "<pre>"; print_r($payload); die;

    // Create and submit a request to the payment provider.
    
    if ($settings->get_application_role() === Settings::APP_ROLE_PRODUCTION)
    {
      $url = Utility::NETS_API_URL_PROD;
    }
    else
    {
      $url = Utility::NETS_API_URL_TEST;
    }
    $url = "https://test.api.dibspayment.eu/v1/subscriptions/charges";
    $request = curl_init($url);
    curl_setopt($request, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: ' . $settings->get_nets_secret_key())
    );
    //$data =  json_decode(curl_exec($request));

    echo "<pre>"; print_r(curl_exec($request)); die("sdjfkdjf");
  }
}
?>