<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_user_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/subscription_utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/offer/offer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/insurance_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_plan_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/offer_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_rule_data_manager.php';

// Access to operations depend on the role. Set the role to a higher access level if required. The caller is responsible
// for checking credentials.
class User_Subscription_Data_Manager extends Single_User_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The offer that will be used to create a price plan for a newly created subscription. This offer must be set, in
  // order to create a subscription. The value should be null, or an Offer object.
  protected $offer = null;

  // Flag that says whether a subscription can have an end date when it is created. This is, as a rule, only permitted
  // when creating test subscriptions.
  protected $allow_expired_subscriptions = false;

  // Flag that says whether a subscription will be active when created. The default is false, in which case the
  // subscription will have to be activated later, usually after it has been paid for.
  protected $create_active_subscription = false;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_subscription', Utility::ROLE_USER, 'create_subscription_from_list');
    $this->add_action('create_subscription', Utility::ROLE_COMPANY_ADMIN, 'create_subscription_from_list');
    $this->add_action('cancel_subscription', Utility::ROLE_USER, 'cancel_subscription');
    $this->add_action('cancel_subscription', Utility::ROLE_COMPANY_ADMIN, 'cancel_subscription_any_time');
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
  //   product_type_id
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
  //                                            cause           The reason the price changed.
  //                                            description     String that describes this price change to the customer.
  //   payment_history          Array. Always null, as the payment history is not loaded until needed.
  //   access_code              String which holds the access code to use for the storage unit or location lock.
  //   access_link              String which holds a URL that can be used to unlock the storage unit or location.
  // Use the c.sub column constants to index these fields.
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
        s.owner_id = {$this->get_user_group_user_id()} AND
        s.buyer_id = {$this->get_user_id()} AND
        s.active = 1
      ORDER BY
        start_date DESC;
    ";

    $results = $wpdb->get_results($query, ARRAY_A);
    $subscriptions = Subscription_Utility::parse_subscriptions($results);

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
        $table .= "], null, '";
        if (!empty($subscription['access_code']))
        {
          $table .= $subscription['access_code'];
        }
        $table .= "', '";
        if (!empty($subscription['access_link']))
        {
          $table .= $subscription['access_link'];
        }
        $table .= "'],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Read the subscription with the given ID. Return an array with the following fields:
    // *** // This list may be outdated.
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
  //   insurance_id         -1 if no subscription is selected.
  //   insurance_name       An empty string if no subscription is selected.
  //   price_plans          Array of price plans, each of which has the following fields:
  //                          type        The type of additional product for which the price plan applies, or -1 if the
  //                                      price plan applies to the rent.
  //                          lines       Array of price plan lines, each of which has the following fields:
  //                                        start_date      String that holds the starting date for this price plan
  //                                                        line, in the format 'yyyy-mm-dd'.
  //                                        price           The price that applies, starting on the given date. Integer.
  //                                        cause           The reason the price changed.
  //                                        description     String that describes this price change to the customer.
  //
  // This method does not require the user ID to be set.
  public function read_subscription($id)
  {
    global $wpdb;

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
          s.id = %d;
      ",
      $id
    );
    $results = $wpdb->get_results($sql, ARRAY_A);
    $subscriptions = Subscription_Utility::parse_subscriptions($results);
    if (!Utility::array_with_one($subscriptions))
    {
      error_log("Failed to read subscription {$id}. Result: " . print_r($results, true));
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
  //   OFFER_NOT_FOUND                The data manager's offer field was not set. This field must be set, in order to
  //                                  create a subscription.
  // The method may return other results as well, depending on the result of the can_create method.
  //
  // The item to be created can be passed as a parameter. If not, it will be read from the request.
  //
  // Override to also create price plans for rent and insurance, using the offer stored in the offer property.
  public function create($data_item = null)
  {
    global $wpdb;

    // Read input data.
    if ($this->get_offer() === null)
    {
      return Result::OFFER_NOT_FOUND;
    }
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

    // Add price plans for rent, and insurance if selected.
    if (!$this->add_rent_and_insurance_price_plans($this->created_item_id, $data_item))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // All operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while creating subscription: ' . $wpdb->last_error);
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
    if (!Price_Plan_Data_Manager::delete_all_price_plans_for($id))
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

    // All operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while deleting subscription: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
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
    // Ensure the ID was posted.
    if (!Utility::integer_posted($this->id_posted_name))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    // Find and store the end date.
    return Subscription_Utility::set_subscription_end_date(Utility::read_posted_integer($this->id_posted_name),
      Subscription_Utility::get_end_date_if_cancelled(), $this->access_token);
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
  // Attempt to create a subscription to one of the products listed in an offer stored on the session. If no matching
  // offer is found on the session, the subscription cannot be created.
  public function create_subscription_from_list()
  {
    // Read the product type and location IDs from the request.
    if (!Utility::integers_posted(array('location_id', 'product_type_id')))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $location_id = Utility::read_posted_integer('location_id');
    $product_type_id = Utility::read_posted_integer('product_type_id');

    // Read the list of offers from the session. A subscription can only be created based on an offer.
    $offers = Offer_Data_Manager::read_offers_from_session();
    if ($offers === null)
    {
      return Result::OFFER_NOT_FOUND;
    }

    // Find the offer on which the subscription should be based.
    $offer = self::find_offer($location_id, $product_type_id, $offers);
    if ($offer === null)
    {
      return Result::OFFER_NOT_FOUND;
    }
    $offer->reset_product_counter();

    // If there weren't any product IDs in the offer, let the caller know there was something wrong with it.
    if (!$offer->has_more_products())
    {
      return Result::NO_PRODUCTS_IN_OFFER;
    }

    // If required by an administrator, update the terms of the offer.
    $this->set_offer($offer);
    $this->update_offer_terms();

    // Try to create a subscription for each product, until one succeeds.
    while ($offer->has_more_products())
    {
      $result = $this->create();
      // If the subscription was created successfully, let the caller know.
      if ($result === Result::OK)
      {
        return Result::OK;
      }
      // If the product wasn't found (result is Result::PRODUCT_NOT_FOUND), there was something wrong with the offer.
      // Perhaps an admin deleted the product in the meantime? Try to subscribe to the next product in line, in case
      // that exists. If the product was no longer free (result is Result::PRODUCT_ALREADY_BOOKED), we just need to move
      // on and try the next product ID. If neither of those were the problem, log an error due to confusion, then
      // continue.
      if (($result !== Result::PRODUCT_NOT_FOUND) && ($result !== Result::PRODUCT_ALREADY_BOOKED))
      {
        error_log('Unexpected result code in create_subscription_from_list: ' . strval($result));
      }
    }
    // We've tried all the available products, but none could be booked successfully. Let the caller know.
    error_log('All products were already booked. Tried: ' . implode(', ', $offer->get_product_ids()));
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
  // See if an administrator has specified any updates to the terms of the current offer.
  protected function update_offer_terms()
  {
    // Make sure the user is a company administrator.
    $offer = $this->get_offer();
    if (!$this->access_token->is_company_admin() || ($offer === null))
    {
      return;
    }

    // Update base price, if required.
    if (Utility::integer_posted('custom_base_price'))
    {
      $offer->override_base_price(Utility::read_posted_integer('custom_base_price'));
    }

    // Update insurance price, if required.
    if (Utility::integer_posted('custom_insurance_price'))
    {
      $offer->set_custom_insurance_price(Utility::read_posted_integer('custom_insurance_price'));
    }

    // Update price mods, if required. Note that an empty array of price mods can be posted here,
    // in order to remove existing price mods. In that case, override the existing price mods with
    // an empty array. Otherwise, use the Price_Rule_Data_Manager to read and verify the price mods.
    if (Utility::integer_posted('price_mod_count'))
    {
      $custom_price_mod_count = Utility::read_posted_integer('price_mod_count');
      if ($custom_price_mod_count === 0)
      {
        $price_mods = array();
      }
      else
      {
        $price_mods = Price_Rule_Data_Manager::read_price_mods(
          Price_Rule_Data_Manager::RULE_TYPE_SPECIAL_OFFER);
        if ($price_mods === false)
        {
          error_log('Error reading custom price mods.');
          return;
        }
      }
      $offer->override_price_mods($price_mods, 'Custom offer');
    }
  }

  // *******************************************************************************************************************
  // Find the offer with the given $location_id and $product_type_id in the given list of $offers. Return null if it was
  // not found.
  protected static function find_offer($location_id, $product_type_id, $offers)
  {
    foreach ($offers as $offer)
    {
      if (($offer->get_location_id() === $location_id) && ($offer->get_product_type_id() === $product_type_id))
      {
        return $offer;
      }
    }
    return null;
  }

  // *******************************************************************************************************************
  // Create price plans for rent and insurance. A price plan states how the price varies over time. The price plans will
  // be created based on the $offer stored in this object. Return true if the price plans were created successfully.
  protected function add_rent_and_insurance_price_plans($subscription_id, $data_item)
  {
    $offer = $this->get_offer();

    // Find the price per month for rent, and insurance if selected.
    $rent_per_month = $offer->get_capacity_price();
    $insurance_id = $data_item['subscription_product_optional_insurance_id'];
    if (isset($insurance_id))
    {
      // An insurance was selected. If the offer holds a custom price, use that. Otherwise, read it from the database.
      if ($offer->get_custom_insurance_price() >= 0)
      {
        $insurance_per_month = $offer->get_custom_insurance_price();
      }
      else
      {
        $insurance_per_month = Insurance_Data_Manager::get_price($insurance_id);
      }
      if ($insurance_per_month < 0)
      {
        error_log('Failed to find price for insurance with ID ' . $insurance_id);
        return false;
      }
    }

    // Create a price plan for the storage unit rent.
    if ($offer->has_special_offer_price_mod())
    {
      // There is a special offer. Create a complete price plan, starting at the subscription start date.
      $rent_prices = Price_Plan_Data_Manager::create_price_dates_from_price_mods(
        $rent_per_month,
        $data_item['start_date'],
        $offer->get_special_offer_price_mods(),
        $offer->get_special_offer_rule_name(),
        $offer->get_special_offer_rule_name(),
        $offer->get_capacity_rule_name(),
        $offer->get_capacity_price_mod(),
        'Normal price'
      );
    }
    else
    {
      // There is no special offer. Create a simple price plan, using just the monthly price, starting at the
      // subscription start date.
      $rent_prices = Price_Plan_Data_Manager::create_single_price_price_dates($rent_per_month, $data_item['start_date'],
        $offer->get_capacity_rule_name(), $offer->get_capacity_price_mod(), 'Normal price');
    }

    // Add price plan for storage unit rent to the database.
    if (!Price_Plan_Data_Manager::create_price_plan($subscription_id, null, $rent_prices))
    {
      return false;
    }

    // If no insurance was selected, or the price is 0, return without creating an insurance price plan. Administrators
    // may add an insurance product with zero price in order to allow users to provide their own insurance. There's no
    // point having an insurance price plan if nothing is paid.
    if (!isset($insurance_id) || ($insurance_per_month === 0))
    {
      return true;
    }

    // Create a simple price plan for the insurance, using the monthly price, starting at the subscription start date.
    $insurance_prices = Price_Plan_Data_Manager::create_single_price_price_dates(
      $insurance_per_month, $data_item['start_date'], '', 0, 'Normal price');

    // Add insurance price plan to the database.
    return Price_Plan_Data_Manager::create_price_plan(
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
    $offer = $this->get_offer();
    if (($offer === null) || !$offer->has_more_products())
    {
      return null;
    }
    $product_id = $offer->get_next_product_id();
    if (!Utility::date_posted('start_date') || empty($product_id) || ($product_id < 0))
    {
      return null;
    }

    // Read insurance ID if present. Insurance is optional.
    if (Utility::integer_posted('insurance_id'))
    {
      $insurance_id = Utility::read_posted_integer('insurance_id');
    }
    else
    {
      $insurance_id = null;
    }

    // When creating test subscriptions, an end date might be passed.
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
      'subscription_product_optional_insurance_id' => $insurance_id,
      'active' => ($this->get_create_active_subscription() ? 1 : 0),
      'start_date' => Utility::read_posted_string('start_date'),
      'end_date' => $end_date,
      'updated_at' => current_time('mysql')
    );
    if ($end_date !== null)
    {
      $subscription['cancelled_at'] = current_time('mysql');
    }
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
    // Check whether the product is free and can be booked. If the product has an end date, it only needs to be free until then.
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

  public function get_offer()
  {
    return $this->offer;
  }

  // *******************************************************************************************************************

  public function set_offer($new_value)
  {
    if (($new_value === null) || (is_object($new_value) && ($new_value instanceof Offer)))
    {
      $this->offer = $new_value;
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

  public function get_create_active_subscription()
  {
    return $this->create_active_subscription;
  }
  
  // *******************************************************************************************************************

  public function set_create_active_subscription($new_value)
  {
    $this->create_active_subscription = !!$new_value;
  }

  // *******************************************************************************************************************
}
?>