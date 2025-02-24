<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/subscription_utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';

class All_Subscription_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The list of unique user IDs found during the last read request.
  protected $user_ids = null;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('cancel_subscription', Utility::ROLE_COMPANY_ADMIN, 'cancel_subscription_any_time');
    $this->database_table = 'subscriptions';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return an array of all subscriptions accessible to the current user from the database.
  public function get_subscriptions()
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
        p.product_type_id AS product_type_id,
        pm.meta_value AS access_code,
        pm2.meta_value AS access_link,
        i.id AS insurance_id,
        i.title AS insurance_name,
        pp.id AS price_plan_id,
        pp.type AS price_plan_type,
        ppl.from_date AS price_plan_line_start_date,
        ppl.price AS price_plan_line_price,
        ppl.cause AS price_plan_line_cause,
        ppl.description AS price_plan_line_description
      FROM
        {$this->database_table} s
      JOIN
        {$wpdb->prefix}posts p ON p.ID = s.product_id
      LEFT JOIN
        {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'access_code'
      LEFT JOIN
        {$wpdb->prefix}postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'access_link'
      LEFT JOIN
        subscription_product_optional i ON i.id = s.subscription_product_optional_insurance_id
      LEFT JOIN
        subscription_price_plan pp ON pp.subscription_id = s.id
      LEFT JOIN
        subscription_price_plan_line ppl ON ppl.price_plan_id = pp.id
      WHERE
        s.owner_id = {$this->get_user_group_user_id()}
      ORDER BY
        buyer_id, location_id, product_name;
    ";
    return $this->get_subscriptions_with_query($query);
  }

  // *******************************************************************************************************************
  // Return an array of all billable subscriptions accessible to the current user. Billable means that an order should
  // be created for the given $month, which is a string with the format "yyyy-mm". The rules for whether orders should
  // be created depend on the subscription's start and end dates, and the active flag:
  //
  //   active is 0: do not create order
  //
  // Provided the subscription was not excluded due to the active flag:
  //
  //   start date before the given month: order should be created
  //     The subscription must be paid for the given month, so an order should be created.
  //   start date in the given month, or later: do not create order
  //     The subscription either does not require an order for the given month (as it starts later), or else that order
  //     already exists, having been created when the subscription was created.
  //
  // Provided the subscription was not excluded due to the start date:
  //
  //   end date is null: order should be created
  //     The subscription is ongoing, and an order should be created.
  //   end date in the given month, or later: order should be created
  //     The subscription has not yet expired, and an order should be created.
  //   end date before the given month: do not create order
  //     The subscription is expired, and no longer needs to be paid for.
  //
  // If the given $month is not valid, the method returns false.
  public function get_billable_subscriptions($month)
  {
    global $wpdb;

    if (!Utility::is_valid_month($month))
    {
      return false;
    }
    $reference_date = $month . '-01';

    $sql = $wpdb->prepare("
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
        p.product_type_id AS product_type_id,
        i.id AS insurance_id,
        i.title AS insurance_name,
        pp.id AS price_plan_id,
        pp.type AS price_plan_type,
        ppl.from_date AS price_plan_line_start_date,
        ppl.price AS price_plan_line_price,
        ppl.cause AS price_plan_line_cause,
        ppl.description AS price_plan_line_description
      FROM
        {$this->database_table} s
      JOIN
        {$wpdb->prefix}posts p ON p.ID = s.product_id
      LEFT JOIN
        subscription_product_optional i ON i.id = s.subscription_product_optional_insurance_id
      LEFT JOIN
        subscription_price_plan pp ON pp.subscription_id = s.id
      LEFT JOIN
        subscription_price_plan_line ppl ON ppl.price_plan_id = pp.id
      WHERE
        (s.owner_id = {$this->get_user_group_user_id()}) AND
        (s.active = 1) AND
        (s.start_date < %s) AND
        ((s.end_date IS NULL) OR (s.end_date >= %s))
      ORDER BY
        buyer_id, location_id, product_name;
    ", $reference_date, $reference_date);
    return $this->get_subscriptions_with_query($sql);
  }

  // *******************************************************************************************************************
  // Read all subscriptions accessible to the current user from the database. Return them as a string containing a
  // Javascript array declaration. At the same time, populate the object's user_ids array with the unique owner IDs
  // found among the requests.
  //
  // Use the c.sua column constants to index these fields.
  public function read()
  {
    $subscriptions = $this->get_subscriptions();

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
        $table .= ", ";
        $table .= $subscription['product_type_id'];
        $table .= ", ";
        $table .= strval(Subscription_Utility::get_subscription($subscription)['status']);
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
                $table .= $lines[$j]['cause'];
                $table .= "', '";
                $table .= $lines[$j]['description'];
                $table .= "'],";
              }
              $table = Utility::remove_final_comma($table);
            }
            $table .= "]],";
          }
          $table = Utility::remove_final_comma($table);
        }
        // Payment history (always null).
        $table .= "], null, ";
        // Buyer ID.
        $table .= $subscription['buyer_id'];
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Read information about each of the users in the object's user_ids array, which is populated whenever requests are
  // read using the read method. Return a Javascript array declaration, where each entry in the array is an array with
  // the following fields:
  //   id, name, eMail, phone, address, postcode, area
  public function read_users()
  {
    if (!isset($this->user_ids))
    {
      return '[]';
    }
    $user_data = new User_Data_Manager($this->access_token);
    return $user_data->get_users($this->user_ids);
  }

  // *******************************************************************************************************************
  // Cancel the subscription by recording the end date in the database. Return an integer result code that can be used
  // to inform the user of the result of the operation:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  //
  // The method reads the following parameters to determine the end date:
  //   cancel_type : integer          The means by which the end date is determined. Use the CANCEL_TYPE_ constants.
  //                                  This field is optional. The default value is CANCEL_TYPE_STANDARD.
  //   end_date : string              If the cancel_type is CANCEL_TYPE_CUSTOM, this field is the date, as a string in
  //                                  the format "yyyy-mm-dd", on which the subscription should end.
  //
  // This method is available to administrators only.
  public function cancel_subscription_any_time()
  {
    // Ensure the ID was posted.
    if (!Utility::integer_posted($this->id_posted_name))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    // Find the subscription end date. If the user posted a cancel_type, use that.
    $end_date = Subscription_Utility::get_end_date_if_cancelled();
    if (Utility::integer_posted('cancel_type'))
    {
      $cancel_type = Utility::read_posted_integer('cancel_type');
      if ($cancel_type === Utility::CANCEL_TYPE_IMMEDIATE)
      {
        // The subscription should end immediately. The end date is yesterday.
        $end_date = Utility::get_previous_day();
      }
      elseif ($cancel_type === Utility::CANCEL_TYPE_CUSTOM)
      {
        // The subscription should end on the posted end date. Read the date.
        if (!Utility::date_posted('end_date'))
        {
          return Result::MISSING_INPUT_FIELD;
        }
        $end_date = Utility::read_posted_string('end_date');
      }
    }
    // Store the end date.
    return Subscription_Utility::set_subscription_end_date(Utility::read_posted_integer($this->id_posted_name),
      $end_date, $this->access_token);
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Run the given query, and return an array of subscriptions, as described by the parse_subscriptions method in
  // Subscription_Utility.
  protected function get_subscriptions_with_query($query)
  {
    global $wpdb;

    $results = $wpdb->get_results($query, ARRAY_A);
    $subscriptions = Subscription_Utility::parse_subscriptions($results);

    $user_ids = array();
    if (count($subscriptions) > 0)
    {
      foreach ($subscriptions as $subscription)
      {
        // Gather the buyer ID from this request into the list of unique user IDs. Users are stored under their ID.
        // Adding a user that already exists makes no difference.
        $buyer_id = intval($subscription['buyer_id']);
        $user_ids[$buyer_id] = $buyer_id;
      }
    }
    // Store the list of unique user IDs found. Remove the keys, as they were only needed to avoid duplicates.
    $this->user_ids = array_values($user_ids);

    return $subscriptions;
  }

  // *******************************************************************************************************************
}
?>