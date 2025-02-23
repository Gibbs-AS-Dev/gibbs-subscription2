<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_user_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/insurance_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_plan_data_manager.php';

// Access to operations depend on the role. Set the role to a higher access level if required. The caller is responsible
// for checking credentials.
class Subscription_Data_Manager extends Single_User_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The product ID of a subscription to be created or updated. This value will be used if the product ID is not
  // provided on the request.
  protected $product_id = -1;

  // Flag that says whether a subscription can have an end date when it is created. This is, as a rule, only permitted
  // when creating test subscriptions.
  protected $allow_expired_subscriptions = false;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_test_subscription', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('create_subscription', Utility::ROLE_USER, 'create_subscription_from_list');
    $this->add_action('create_subscription', Utility::ROLE_COMPANY_ADMIN, 'create_subscription_from_list');
    $this->add_action('cancel_subscription', Utility::ROLE_USER, 'cancel_subscription');
    $this->add_action('cancel_subscription', Utility::ROLE_COMPANY_ADMIN, 'cancel_subscription');
      // *** // At the moment, the subscription data manager does not provide any further actions. You can still call them directly, however.
/*
    $this->add_action('update_subscription', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_subscription', Utility::ROLE_COMPANY_ADMIN, 'delete');
*/
    $this->database_table = 'subscriptions';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all subscriptions owned by the user whose ID is stored in this class from the database. Return them as a
  // string containing a Javascript array declaration with the following fields:
  //   subscription_id
  //   product_name
  //   location_id
  //   product_type_name
  //   status
  //   start_date
  //   end_date                 The subscription end date, as a string in the format "yyyy-mm-dd", or an empty string if
  //                            the subscription has not been cancelled yet.
  //   insurance_name           The name of the insurance product selected for this subscription, or an empty string if
  //                            no insurance is selected.
  //   price_plans              Array of price plans for this subscription. Each entry has the following fields:
  //                              type
  //                              lines       Array of price plan lines for this price plan. Each entry has the
  //                                          following fields:
  //                                            start_date      String that holds the starting date for this price plan
  //                                                            line, in the format 'yyyy-mm-dd'.
  //                                            price           The price that applies, starting on the given date.
  //                                            description     String that describes this price change.
  //   payment_history          Array. Always null, as the payment history is not loaded until needed.
  public function read()
  {
    global $wpdb;

    $query = "
      SELECT 
        s.id AS subscription_id,
        s.buyer_id AS buyer_id,
        s.owner_id AS owner_id,
        s.active AS active,
        s.start_date AS start_date,
        s.end_date AS end_date,
        p.ID AS product_id,
        p.post_title AS product_name,
        p.location_id AS location_id,
        pt.name AS product_type_name,
        i.id AS insurance_id,
        i.title AS insurance_name,
        pp.id AS price_plan_id,
        pp.type AS price_plan_type,
        ppl.from_date AS price_plan_line_start_date,
        ppl.price AS price_plan_line_price,
        ppl.description AS price_plan_line_description
      FROM
        {$this->database_table} s
      JOIN
        {$wpdb->prefix}posts p ON p.ID = s.product_id
      JOIN
        subscription_product_type pt ON pt.id = p.product_type_id
      JOIN
        subscription_product_optional i ON i.id = s.subscription_product_optional_insurance_id
      LEFT JOIN
        subscription_price_plan pp ON pp.subscription_id = s.id
      LEFT JOIN
        subscription_price_plan_line ppl ON ppl.price_plan_id = pp.id
      WHERE
        s.owner_id = {$this->get_user_group_user_id()} AND
        s.buyer_id = {$this->get_user_id()} AND
        s.active = 1
      ORDER BY
        location_id, product_name;
    ";

    $results = $wpdb->get_results($query, ARRAY_A);
    $subscriptions = self::parse_subscriptions($results);

    $table = "[";
    if (count($subscriptions) > 0)
    {
      foreach ($subscriptions as $subscription)
      {
        $table .= "[";
        $table .= $subscription['subscription_id'];
        $table .= ", '";
        $table .= $subscription['product_name'];
        $table .= "', ";
        $table .= $subscription['location_id'];
        $table .= ", '";
        $table .= $subscription['product_type_name'];
        $table .= "', ";
        $table .= strval(self::get_subscription($subscription)['status']);
        $table .= ", '";
        $table .= $subscription['start_date'];
        $table .= "', '";
        if (isset($subscription['end_date']))
        {
          $table .= $subscription['end_date'];
        }
        $table .= "', '";
        if (isset($subscription['insurance_name']))
        {
          $table .= $subscription['insurance_name'];
        }
        $table .= "', [";
        // Price plans.
        $price_plan_count = count($subscription['price_plans']);
        if ($price_plan_count > 0)
        {
          for ($i = 0; $i < $price_plan_count; $i++)
          {
            $table .= "[";
            $table .= $subscription['price_plans'][$i]['type'];
            $table .= ", [";
            // Price plan lines.
            $lines = $subscription['price_plans'][$i]['lines'];
            $line_count = count($lines);
            if ($line_count > 0)
            {
              for ($j = 0; $j < $line_count; $j++)
              {
                $table .= "['";
                $table .= $lines[$j]['start_date'];
                $table .= "', ";
                $table .= $lines[$j]['price'];
                $table .= ", '";
                $table .= $lines[$j]['description'];
                $table .= "'],";
              }
              // Remove final comma.
              $table = substr($table, 0, -1);
            }
            $table .= "]],";
          }
          // Remove final comma.
          $table = substr($table, 0, -1);
        }
        $table .= "], null],";
      }
      // Remove final comma.
      $table = substr($table, 0, -1);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Read the subscription with the given ID. Return an array with the following fields:
  //   id
  //   buyer_id
  //   owner_id
  //   active               Boolean field that says whether the subscription was created successfully. It does not say
  //                        whether the subscription is currently running (the end_date is used for that).
  //   start_date
  //   end_date
  //   product_id
  //   product_name
  //   location_id
  //   rent_per_month
  //   insurance_id
  //   insurance_name
  //   price_plans          Array of price plans, each of which has the following fields:
  //                          type        The type of additional product for which the price plan applies, or -1 if the
  //                                      price plan applies to the rent.
  //                          lines       Array of price plan lines, each of which has the following fields:
  //                                        start_date      String that holds the starting date for this price plan
  //                                                        line, in the format 'yyyy-mm-dd'.
  //                                        price           The price that applies, starting on the given date. Integer.
  //                                        description     String that describes this price change.
  public function read_subscription($id)
  {
    global $wpdb;

    $query = "
      SELECT 
        s.id AS subscription_id,
        s.buyer_id AS buyer_id,
        s.owner_id AS owner_id,
        s.active AS active,
        s.start_date AS start_date,
        s.end_date AS end_date,
        p.ID AS product_id,
        p.post_title AS product_name,
        p.location_id AS location_id,
        pt.name AS product_type_name,
        i.id AS insurance_id,
        i.title AS insurance_name,
        pp.id AS price_plan_id,
        pp.type AS price_plan_type,
        ppl.from_date AS price_plan_line_start_date,
        ppl.price AS price_plan_line_price,
        ppl.description AS price_plan_line_description
      FROM
        {$this->database_table} s
      JOIN
        {$wpdb->prefix}posts p ON p.ID = s.product_id
      JOIN
        subscription_product_type pt ON pt.id = p.product_type_id
      JOIN
        subscription_product_optional i ON i.id = s.subscription_product_optional_insurance_id
      LEFT JOIN
        subscription_price_plan pp ON pp.subscription_id = s.id
      LEFT JOIN
        subscription_price_plan_line ppl ON ppl.price_plan_id = pp.id
      WHERE
        s.id = {$id};
    ";
    $results = $wpdb->get_results($query, ARRAY_A);
    $subscriptions = self::parse_subscriptions($results);
    if (!Utility::array_with_one($subscriptions))
    {
      error_log("Failed to read subscription {$id}. Result: {print_r($results, true)}.");
      return null;
    }
    return $subscriptions[0];
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
  // Override to also create price plans for rent and insurance.
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
    
    // Create the subscription in the database.
    $wpdb->query('START TRANSACTION');
    $result = parent::create($data_item);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Add price plans for rent and insurance.
    if (!self::add_rent_and_insurance_price_plans($this->created_item_id, $data_item))
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
  // Override to also delete the subscription's price plans.
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

    // Delete all price plans associated with this subscription.
    $wpdb->query('START TRANSACTION');
    if (!Price_Plan_Data_Manager::delete_price_plans_for($id))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // Delete the subscription itself.
    $result = parent::delete($id);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // All operations succeeded.
    $wpdb->query('COMMIT');
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Cancel the subscription by recording the end date in the database. Return an integer result code that can be used
  // to inform the user of the result of the operation:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  public function cancel_subscription()
  {
    global $wpdb;

    // Ensure the ID was posted.
    if (!Utility::integer_posted($this->id_posted_name))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    // Find the subscription end date, and add it to a table of values to be updated.
    $data_item = array('end_date' => $this->get_end_date_if_cancelled());
    // Store the end date.
      // *** // Do not set an end date if the subscription already has one, or if the start date is after today's date.
    $subscription_id = Utility::read_posted_integer($this->id_posted_name);
    $result = $wpdb->update($this->database_table, $data_item, array($this->id_db_name => $subscription_id));
    if ($result === false)
    {
      error_log("Database query failed while cancelling subscription {$subscription_id}: {$wpdb->last_error}");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Database query failed to update the expected number of rows while cancelling subscription {$subscription_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Read a posted list of product IDs, and attempt to create a single subscription to one of the products.
  public function create_subscription_from_list()
  {
    // Read the comma-separated string of product IDs, and convert it to an array of numbers.
    if (!Utility::string_posted('product_ids'))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $product_ids = array_map('intval', explode(',', Utility::read_posted_string('product_ids')));
    // If there weren't any product IDs in the list, let the caller know there was something wrong with the list.
    if (empty($product_ids))
    {
      return Result::MISSING_INPUT_FIELD;
    }

    // Try to create a subscription for each product, until one succeeds.
    foreach ($product_ids as $product_id)
    {
      $this->set_product_id($product_id);
      $result = $this->create();
      // If the subscription was created successfully, let the caller know.
      if ($result === Result::OK)
      {
        return Result::OK;
      }
      // If the product wasn't found (result is Result::PRODUCT_NOT_FOUND), there was something wrong with the input.
      // Try to subscribe to the next product in line, in case that exists. If the product was no longer free (result is
      // Result::PRODUCT_ALREADY_BOOKED), we just need to move on and try the next product ID. If neither of those were
      // the problem, log an error due to confusion, then continue.
      if (($result !== Result::PRODUCT_NOT_FOUND) && ($result !== Result::PRODUCT_ALREADY_BOOKED))
      {
        error_log('Unexpected result code in create_subscription_from_list: ' . strval($result));
      }
    }
    // We've tried all the available products, but none could be booked successfully. Let the caller know.
    error_log('All products were already booked. Tried: ' . implode(', ', $product_ids));
    return Result::PRODUCT_ALREADY_BOOKED;
  }

  // *******************************************************************************************************************
  // Set whether or not the subscription with the given ID should be active. An inactive subscription is not visible to
  // anyone but Gibbs administrators. Subscriptions are created as inactive, and activated when paid for. $new_value
  // should be a boolean. If you pass the integer 1, the subscription will be deactivated. Return a result code that
  // says whether the operation was successful.
  public function set_subscription_active_flag($id, $new_value)
  {
    global $wpdb;

    // Ensure $id and $new_value were provided.
    if (!isset($id) || !is_numeric($id))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $id = intval($id);
    $new_value = ($new_value === true ? 1 : 0);
    // Set the flag.
    $result = $wpdb->update($this->database_table, array('active' => $new_value), array($this->id_db_name => $id));
    // If the operation returned false, something went wrong. Otherwise, $result says how many rows were updated. If the
    // figure is 0, that just means the value remained unchanged.
    if ($result === false)
    {
      error_log("Database query failed while activating or deactivating subscription {$id}: {$wpdb->last_error}. New value: {$new_value}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return an array of subscriptions, with all price plans gathered neatly in a table. Each price plan has its own
  // table of lines. If $results is null, or anything other than an array, return an empty array.
  //
  // Each subscription has the following fields:
  //   subscription_id
  //   buyer_id
  //   owner_id
  //   active               Boolean field that says whether the subscription was created successfully. It does not say
  //                        whether the subscription is currently running (the end_date is used for that).
  //   start_date
  //   end_date
  //   product_id
  //   product_name
  //   location_id
  //   product_type_name
  //   insurance_id
  //   insurance_name
  //   price_plans          Array of price plans, each of which has the following fields:
  //                          type        The type of additional product for which the price plan applies, or -1 if the
  //                                      price plan applies to the rent.
  //                          lines       Array of price plan lines, each of which has the following fields:
  //                                        start_date      String that holds the starting date for this price plan
  //                                                        line, in the format 'yyyy-mm-dd'.
  //                                        price           The price that applies, starting on the given date. Integer.
  //                                        description     String that describes this price change.
  protected static function parse_subscriptions($results)
  {
    $subscriptions = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $subscription_row)
      {
        $subscription_id = intval($subscription_row['subscription_id']);
        // Add the subscription if it does not already exist.
        if (!isset($subscriptions[$subscription_id]))
        {
          $subscriptions[$subscription_id] = array(
            'subscription_id' => $subscription_id,
            'buyer_id' => intval($subscription_row['buyer_id']),
            'owner_id' => intval($subscription_row['owner_id']),
            'active' => isset($subscription_row['active']) && is_numeric($subscription_row['active']) &&
              (intval($subscription_row['active']) === 1),
            'start_date' => $subscription_row['start_date'],
            'end_date' => $subscription_row['end_date'],
            'product_id' => intval($subscription_row['product_id']),
            'product_name' => $subscription_row['product_name'],
            'location_id' => intval($subscription_row['location_id']),
            'product_type_name' => $subscription_row['product_type_name'],
            'insurance_id' => intval($subscription_row['insurance_id']),
            'insurance_name' => $subscription_row['insurance_name'],
            'price_plans' => array()
          );
        }
        // If the source row has price plan information, add it to the appropriate price plan.
        if (isset($subscription_row['price_plan_id']))
        {
          // Add the price plan if it does not already exist.
          $price_plan_id = $subscription_row['price_plan_id'];
          if (!isset($subscriptions[$subscription_id]['price_plans'][$price_plan_id]))
          {
            $type = $subscription_row['price_plan_type'];
            if (!isset($type) || ($type === 'null') || !is_numeric($type))
            {
              $type = -1;
            }
            else
            {
              $type = intval($type);
            }
            $subscriptions[$subscription_id]['price_plans'][$price_plan_id] = array(
              'type' => $type,
              'lines' => array()
            );
          }

          // Add the line to the price plan.
          if (isset($subscription_row['price_plan_line_description']))
          {
            $description = $subscription_row['price_plan_line_description'];
          }
          else
          {
            $description = '';
          }
          $subscriptions[$subscription_id]['price_plans'][$price_plan_id]['lines'][] = array(
            'start_date' => $subscription_row['price_plan_line_start_date'],
            'price' => intval($subscription_row['price_plan_line_price']),
            'description' => $description
          );
        }
      }
    }

    // Remove array keys for price plans and price plan lines. Sort price plan lines by date.
    foreach ($subscriptions as &$subscription)
    {
      $subscription['price_plans'] = array_values($subscription['price_plans']);
      foreach ($subscription['price_plans'] as &$price_plan)
      {
        // $price_plan['lines'] = array_values($price_plan['lines']);
        // Sort price plan lines in ascending order, based on the date. The date is a string with the format
        // "yyyy-mm-dd", and can be sorted alphabetically using the strcmp function.
        usort($price_plan['lines'], 'strcmp');
      }
    }
    return array_values($subscriptions);
  }

  // *******************************************************************************************************************
  // Return the final day of a subscription, as string in "yyyy-mm-dd" format, provided it is cancelled right now.
  // Current rules are that, if the subscription is cancelled on the 15th of the month, or earlier, the subscription
  // will end on the final day of the current month. If it is cancelled on the 16th, or later, it will end on the final
  // day of the next month.
  //
  // PHP date function documentation: https://www.w3schools.com/php/func_date_date.asp
  protected function get_end_date_if_cancelled()
  {
    $currentYear = intval(date('Y'));
    $currentMonth = intval(date('m'));
    if (intval(date('j')) <= 15)
    {
      // Today is the 15th or earlier. Return the last day of the current month.
      return Utility::get_last_date("{$currentYear}-{$currentMonth}");
    }
    // Today is the 16th or later. Calculate the next month and year.
    $nextMonth = ($currentMonth + 1) % 12;
    $nextYear = $currentYear + (($currentMonth + 1) > 12 ? 1 : 0);
    // Return the last day of the next month.
    return Utility::get_last_date("{$nextYear}-{$nextMonth}");
  }

  // *******************************************************************************************************************
  // Create price plans for rent and insurance. A price plan states how the price varies over time. For now, we just
  // set a fixed price for each, starting at the subscription start date. Return true if the price plans were created
  // successfully.
  protected function add_rent_and_insurance_price_plans($subscription_id, $data_item)
  {
    // Find the price per month for rent and insurance.
    $rent_per_month = Product_Type_Data_Manager::get_price_for_product($data_item['product_id']);
    if ($rent_per_month < 0)
    {
      error_log('Failed to find price for product ' . $data_item['product_id']);
      return false;
    }
    $insurance_per_month = Insurance_Data_Manager::get_price($data_item['subscription_product_optional_insurance_id']);
    if ($insurance_per_month < 0)
    {
      error_log('Failed to find price for insurance with ID ' .
        $data_item['subscription_product_optional_insurance_id']);
      return false;
    }

    // Create simple price plans with the monthly price, starting at the subscription start date.
    $rent_prices = Price_Plan_Data_Manager::create_single_price_price_dates(
      $rent_per_month, $data_item['start_date'], 'Normal price');
    $insurance_prices = Price_Plan_Data_Manager::create_single_price_price_dates(
      $insurance_per_month, $data_item['start_date'], 'Normal price');

    // Add one price plan each for rent and insurance.
    return Price_Plan_Data_Manager::create_price_plan($subscription_id, null, $rent_prices) &&
      Price_Plan_Data_Manager::create_price_plan(
        $subscription_id, Utility::ADDITIONAL_PRODUCT_INSURANCE, $insurance_prices);
  }

  // *******************************************************************************************************************
  // Return an array that describes a subscription, using the information posted to the server. The buyer_id field will
  // be set to the user ID stored in this object. Start and end dates are read from the request. The latter is optional.
  // If no end date is given, the subscription will be ongoing. The former is optional - if not posted, the value will
  // be set to the product ID stored in this object.
  //
  // Note that, when validating, we need to ensure that posted date strings represent valid dates. However, when reading
  // the same dates, we read the text version as passed from the client. The database does not accept DateTime objects.
  //
  // Database table fields: id, buyer_id, owner_id, product_id, active, start_date, end_date, created_at, updated_at.
  protected function get_data_item()
  {
    if (Utility::integer_posted('product_id'))
    {
      $product_id = Utility::read_posted_integer('product_id');
    }
    else
    {
      $product_id = $this->get_product_id();
    }

    if (!Utility::integer_posted('insurance_id') || !Utility::date_posted('start_date') || empty($product_id) ||
      ($product_id < 0))
    {
      return null;
    }

    // When creating test subscriptions, an an end date might be passed.
    if ($this->allow_expired_subscriptions && Utility::date_posted('end_date'))
    {
      $end_date = Utility::read_posted_string('end_date');
    }
    else
    {
      $end_date = null;
    }

    $subscription = array(
      // id and created_at will be set automatically.
      'buyer_id' => $this->get_user_id(),
      'owner_id' => $this->get_user_group_user_id(),
      'product_id' => $product_id,
      'subscription_product_optional_insurance_id' => Utility::read_posted_integer('insurance_id'),
      // By default, subscriptions are created as inactive. They can be activated once paid for.
      'active' => 0,
      'start_date' => Utility::read_posted_string('start_date'),
      'end_date' => $end_date,
      'updated_at' => current_time('mysql')
    );
    return $subscription;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the given data item can be added to the database. If not, return another result code defined
  // in utility.php. Descendants may want to override this method.
  //
  // Override to ensure that the product exists, and can be booked on the provided start date.
  protected function can_create($data_item)
  {
    // Create a product data manager. Tell it to check status on the date on which the subscription is supposed to
    // start.
    $product_data = new Product_Data_Manager($this->access_token);
    $product_data->set_status_date(Utility::string_to_date($data_item['start_date']));

    // Find the product.
    $product = $product_data->get_product($data_item['product_id']);
    if (!isset($product))
    {
      return Result::PRODUCT_NOT_FOUND;
    }
    // Check whether the product is free. If the product has an end date, it only needs to be free until then.
    // Otherwise, it needs to be free indefinitely, since we don't know when the subscription will end.
    if (empty($data_item['end_date']))
    {
      if (!$product_data::is_free($product))
      {
        return Result::PRODUCT_ALREADY_BOOKED;
      }
    }
    else
    {
      if (!$product_data::is_free_until($product, $data_item['end_date']))
      {
        return Result::PRODUCT_ALREADY_BOOKED;
      }
    }
    // The product exists, and is not already booked. Allow the subscription to be created.
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the data item with the given ID can be deleted from the database. If not, return another
  // result code defined in utility.php. Descendants may want to override this method.
/*
  protected function can_delete($id)
  {
      // *** // What to check?
    return Result::OK;
  }
*/
  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_product_id()
  {
    return $this->product_id;
  }

  // *******************************************************************************************************************

  public function set_product_id($new_value)
  {
    if (is_numeric($new_value))
    {
      $new_value = intval($new_value);
      if ($new_value >= -1)
      {
        $this->product_id = $new_value;
      }
    }
  }

  // *******************************************************************************************************************

  public function get_allow_expired_subscriptions()
  {
    return $this->allow_expired_subscriptions;
  }
  
  // *******************************************************************************************************************

  public function set_allow_expired_subscriptions($new_value)
  {
    $this->allow_expired_subscriptions = !!$new_value;
  }
  
  // *******************************************************************************************************************
  // *** Static methods.
  // *******************************************************************************************************************
  // Return a table that holds information about a subscription, based on reading information with the following field
  // names from the given source:
  //   subscription_id
  //   buyer_id
  //   start_date
  //   end_date
  // source is expected to be an array. The resulting table will have the following fields:
  //   id
  //   buyer_id
  //   start_date
  //   end_date
  //   status
  // status uses the SUB_ constants defined in utility.php. $reference_date is the date on which the status is
  // determined. $reference_date is optional. If not passed, today's date will be used.
  public static function get_subscription($source, $reference_date = null)
  {
    $start_date = Utility::string_to_date($source['start_date']);
    $start_date->setTime(0, 0, 0);
    if (empty($source['end_date']))
    {
      $end_date = null;
    }
    else
    {
      $end_date = Utility::string_to_date($source['end_date']);
      $end_date->setTime(0, 0, 0);
    }
    $subscription = array(
      'id' => $source['subscription_id'],
      'buyer_id' => $source['buyer_id'],
      'start_date' => $start_date,
      'end_date' => $end_date,
    );
    $subscription['status'] = self::get_subscription_status($subscription, $reference_date);
    return $subscription;
  }

  // *******************************************************************************************************************
  // Return the given subscription's status on the given reference date, using the SUB_ constants defined in
  // utility.php. $reference_date is optional. If not passed, today's date will be used. The status is determined using
  // the subscription's start and end dates.
  //
  // Each subscription can be:    Start date                          End date
  // - Finished                   Before or at the reference date     Exists; before the reference date
  // - Ongoing                    Before or at the reference date     Does not exist
  // - Cancelled                  Before or at the reference date     Exists; after or at the reference date
  // - Booked                     After the reference date            Who cares?
  protected static function get_subscription_status($subscription, $reference_date = null)
  {
    if (empty($reference_date))
    {
      // No reference date was passed. Create a date object representing today's date, but without any time. A
      // subscription's start and end dates are dates only.
      $reference_date = new DateTime();
      $reference_date->setTime(0, 0, 0);
    }

    // If the start date is after today, it's booked (regardless of whether an end date is set).
    if ($subscription['start_date'] > $reference_date)
    {
      return Utility::SUB_BOOKED;
    }
    // The start date is today, or earlier. If there's no end date, it's ongoing.
    if (!isset($subscription['end_date']))
    {
      return Utility::SUB_ONGOING;
    }
    // There is an end date. If it's before today, the subscription is finished.
    if ($subscription['end_date'] < $reference_date)
    {
      return Utility::SUB_EXPIRED;
    }
    // The end date is today or later. It's cancelled.
    return Utility::SUB_CANCELLED;
  }

  // *******************************************************************************************************************
}
?>