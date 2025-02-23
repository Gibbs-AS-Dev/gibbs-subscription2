<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/subscription_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';

class Product_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // The character which will be replaced with a number when creating several products at once.
  public const PRODUCT_NUMBER_PLACEHOLDER = '#';

  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The date to be used when product status is calculated. Leave it at null to use today's date. Otherwise, the value
  // should be a DateTime object.
  protected $status_date = null;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    global $wpdb;

    parent::__construct($new_access_token);
    $this->add_action('create_product', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_product', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_product', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->add_action('create_test_subscription', Utility::ROLE_COMPANY_ADMIN, 'create_test_subscription');
    $this->add_action('get_available_product_types', Utility::ROLE_USER, 'get_available_product_types');
    $this->database_table = $wpdb->prefix . 'posts';
    $this->id_db_name = 'ID';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return the product with the given ID, or null if it was not found. The product must belong to the current user
  // group. The returned product is an array with the following fields:
  //   id : integer
  //   name : string
  //   location_id : integer
  //   product_type_id : integer
  //   status : integer // Use the STATUS_ constants declared in utility.php.
  //   subscriptions : array
  // 
  // subscriptions is an array of subscriptions to the product, each of which has the following fields:
  //   id : integer
  //   buyer_id : integer
  //   start_date : DateTime // Always present.
  //   end_date : DateTime // null if not set.
  //   status : integer // Use the SUB_ status constants declared in utility.php.
  public function get_product($product_id)
  {
    // Compose SQL query.
    $query = "
      SELECT 
        p.ID AS product_id,
        p.post_title,
        p.location_id,
        p.product_type_id,
        s.id AS subscription_id,
        s.buyer_id,
        s.start_date,
        s.end_date
      FROM 
        {$this->database_table} p
      LEFT JOIN 
        subscriptions s ON p.ID = s.product_id
      WHERE
        p.ID = {$product_id} AND
        p.post_author = {$this->get_user_group_user_id()} AND
        p.post_type = 'listing' AND
        p.subscription IS NOT NULL
      ORDER BY
        p.location_id, p.post_title;
    ";

    // Return the results.
    $products = $this->get_products_with_query($query);
    if (count($products) > 1)
    {
      // Did we find several products with that ID? That's not possible, is it?
      error_log('Found ' . strval(count($products)) . ' products with ID ' . strval($product_id) .
        '. This should not be possible. Please investigate.');
      return null;
    }
    if (count($products) < 1)
    {
      return null;
    }
    // Convert from an array with the product ID as a key, to a plain array. Then return the first element.
    return array_values($products)[0];
 }

  // *******************************************************************************************************************
  // Return a PHP array of products belonging to the current user group. Each product is an array with the following
  // fields:
  //   id : integer
  //   name : string
  //   location_id : integer
  //   product_type_id : integer
  //   status : integer // Use the STATUS_ constants declared in utility.php.
  //   subscriptions : array
  // 
  // subscriptions is an array of subscriptions to this product, each of which has the following fields:
  //   id : integer
  //   buyer_id : integer
  //   start_date : DateTime // Always present.
  //   end_date : DateTime // null if not set.
  //   status : integer // Use the SUB_ status constants declared in utility.php.
  public function get_products()
  {
    // Compose SQL query.
    $query = "
      SELECT 
        p.ID AS product_id,
        p.post_title,
        p.location_id,
        p.product_type_id,
        s.id AS subscription_id,
        s.buyer_id,
        s.start_date,
        s.end_date
      FROM 
        {$this->database_table} p
      LEFT JOIN 
        subscriptions s ON p.ID = s.product_id
      WHERE
        p.post_author = {$this->get_user_group_user_id()} AND
        p.post_type = 'listing' AND
        p.subscription IS NOT NULL
      ORDER BY
        p.location_id, p.post_title;
    ";

    // Return the results.
    return $this->get_products_with_query($query);
  }

  // *******************************************************************************************************************
  // Read all products owned by the current user group from the database. Return them as a string containing a
  // Javascript array declaration.
  public function read()
  {
    // Read all products for the current user group from the database.
    $products = $this->get_products();

    $table = "[";
    if (!empty($products))
    {
      foreach ($products as $product)
      {
        // ID.
        $table .= "[";
        $table .= $product['id'];
        // Name.
        $table .= ", '";
        $table .= $product['name'];
        // Location ID.
        $table .= "', ";
        $table .= $product['location_id'];
        // Prouct type ID.
        $table .= ", ";
        $table .= $product['product_type_id'];
        // Subscriptions.
        $table .= ", [";
        if (!empty($product['subscriptions']))
        {
          foreach ($product['subscriptions'] as $subscription)
          {
            // ID.
            $table .= "[";
            $table .= $subscription['id'];
            // User ID.
            $table .= ", ";
            $table .= $subscription['buyer_id'];
            // Status.
            $table .= ", ";
            $table .= $subscription['status'];
            // Start date. Should always be present, but check anyway.
            $table .= ", '";
            if (!empty($subscription['start_date']))
            {
              $table .= Utility::date_to_string($subscription['start_date']);
            }
            // End date. May not be set, in which case an empty string is returned.
            $table .= "', '";
            if (!empty($subscription['end_date']))
            {
              $table .= Utility::date_to_string($subscription['end_date']);
            }
            $table .= "'],";
          }
          // Remove final comma.
          $table = substr($table, 0, -1);
        }
        // Status. Uses the STATUS_ constants declared in this class.
        $table .= "], ";
        $table .= strval($product['status']);
        // Subscription end date. This is only present if the status is STATUS_CANCELLED or STATUS_CANCELLED_BOOKED;
        // otherwise it should be an empty string.
        $table .= ", '";
        $table .= Utility::date_to_string($this->get_subscription_end_date($product));
        // Reserved date. This is only present if the status is STATUS_BOOKED, STATUS_VACATED_BOOKED or
        // STATUS_CANCELLED_BOOKED; otherwise it should be an empty string.
        $table .= "', '";
        $table .= Utility::date_to_string($this->get_reserved_date($product));
        $table .= "'],";
      }
      // Remove final comma.
      $table = substr($table, 0, -1);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Create an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations. Override to be able to create multiple products at the same time, and to set the created_at
  // fields, which are not set automatically by the Wordpress database.
  //
  // Posted fields:
  //   id, name, location_id, product_type_id, create_multiple, from_number, to_number, pad_with_zeroes, digit_count
  //
  // The item to be created can be passed as a parameter. If not, it will be read from the request.
  public function create($data_item = null)
  {
    global $wpdb;

    // Read the product information submitted from the client. For products, post_date and post_date_gmt will not be set
    // automatically. Since we are creating a product, we must set them here.
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
    $data_item['post_date'] = current_time('mysql');
    $data_item['post_date_gmt'] = current_time('mysql', 1);

    // See if we are creating several products at once. The client will submit "on" if the checkbox is checked, and omit
    // it if not. Thus, we only care whether the value is set at all. We don't care what the value actually is.
    if (Utility::string_posted('create_multiple'))
    {
      return $this->create_multiple($data_item);
    }

    // We are creating a single product. Ensure the item can be created.
    $result = $this->can_create($data_item);
    if ($result !== Result::OK)
    {
      return $result;
    }
    // Insert a new record, and report the result.
    $result = $wpdb->insert($this->database_table, $data_item);
    if ($result === false)
    {
      error_log("Error while creating product: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Failed to insert the correct number of rows while creating product. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    // Store the ID of the last inserted item.
    $this->created_item_id = $wpdb->insert_id;
    return Result::OK;
  }

  // *******************************************************************************************************************

  public function create_test_subscription()
  {
    $settings = Settings_Manager::read_settings($this->access_token);
    if ($settings->get_application_role() !== Settings::APP_ROLE_PRODUCTION)
    {
      if (!Utility::integer_posted('buyer_id'))
      {
        return Result::MISSING_INPUT_FIELD;
      }

      $subscription_data = new Subscription_Data_Manager($this->access_token);
      $subscription_data->set_user_id(Utility::read_posted_integer('buyer_id'));
      $subscription_data->set_allow_expired_subscriptions(true);
      return $subscription_data->create();
    }
    return Result::INCORRECT_APPLICATION_ROLE;
  }

  // *******************************************************************************************************************
  // Compose a list of product types, with information on whether or not any products of that type are available on the
  // selected date. Return the list as a string containing a JSON array declaration, where each entry has the following
  // fields:
  //   id                         The product type ID.
  //   name                       The product type name.
  //   price                      The price per month the buyer will have to pay.
  //   category_id                The ID of the category to wich the product type belongs.
  //   is_available               A boolean value that says whether the product type is available at the selected
  //                              location, from the selected date.
  //   alternate_location_ids     If the product type is not available, this is an array of IDs of all the other
  //                              locations where the selected product type is available. If no such location exists,
  //                              the list will be empty.
  //   first_available_date       If the product type is not available, this is the date, as a string in the format
  //                              "yyyy-mm-dd", on which the product type becomes available for rent. If there is no
  //                              such date, the value will be null.
  //   available_product_ids      A string with a comma-separated list of products in this category that are free and
  //                              can be booked. The user will only need to book one of them, but since somebody else
  //                              might be using the system, the first product in the list may be unavailable by the
  //                              time he has finished the booking process. Therefore, a few options are provided, if
  //                              available. If the product type is unavailable, the string will be empty.
  // If the query did not succeed, return an integer error code from the Result class instead.
  public function get_available_product_types()
  {
    // Read parameters.
    if (!Utility::integer_passed('selected_location_id') || !Utility::date_passed('selected_date'))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $selected_location_id = Utility::read_passed_integer('selected_location_id');
    $this->set_status_date(Utility::read_passed_date('selected_date'));

    // Read all products for the current user group from the database. Each subscription and product will have its
    // status set to the value it will be on the selected date.
    $products = $this->get_products();

    // Read all product types from the database. The client needs information about each one.
    $product_type_data = new Product_Type_Data_Manager($this->access_token);
    $product_types = $product_type_data->get_product_types();

    // Find which product types are found at the location, and whether or not they are available.
    $availability_list = $this->list_product_types_for_location($products, $selected_location_id);

    // Compose the full list of product types found at the location. Note that the & in front of $availabile_products is
    // for performance reasons. Available products will not be modified here.
    $available_product_types = array();
    foreach ($availability_list as $product_type_id => &$available_products)
    {
      $product_type = self::get_product_type($product_type_id, $product_types);
      if (isset($product_type))
      {
        $is_available = count($available_products) > 0;

        // If required, limit the list of available products to what is needed, in order to save bandwidth.
        $settings = Settings_Manager::read_settings($this->access_token);
        if ($settings->get_bookable_product_count() > 0)
        {
          $available_products = array_slice($available_products, 0, $settings->get_bookable_product_count());
        }
        // If required, the available products array can be converted to a comma-separated string. However, the client
        // may need to count the number of elements, so at the moment we pass it as an array.
        // $available_products = implode(',', $available_products);

        if ($is_available)
        {
          $alternative_location_ids = array();
          $first_available_date = null;
        }
        else
        {
          // For all unavailable product types, find alternative locations where the product type is available, and the date
          // when the product might be available at the original location, if possible.
          $alternative_location_ids =
            $this->list_alternate_locations_for($product_type_id, $selected_location_id, $products);
          $first_available_date =
            Utility::date_to_string(
              $this->find_first_available_date_for($product_type_id, $selected_location_id, $products), null);
        }
        $available_product_types[] = array(
          $product_type_id,
          $product_type->name,
          intval($product_type->price),
          intval($product_type->category_id),
          $is_available,
          $alternative_location_ids,
          $first_available_date,
          $available_products
        );
      }
      else
      {
        error_log ('A product specified a product type (' . strval($product_type_id) .
          ') that was not found in the list of product types. This hole in the fabric of the universe should never occur.');
      }
    }

    // Compose JSON.
    return json_encode($available_product_types);
  }

  // *******************************************************************************************************************
  // Return true if the given product is free - that is, it can be booked. A product is free if it has STATUS_NEW or
  // STATUS_VACATED. This method assumes that the product's status has been set for the date on which we need to know
  // whether or not it is free.
  public static function is_free($product)
  {
    return ($product['status'] === Utility::STATUS_NEW) || ($product['status'] === Utility::STATUS_VACATED);
  }

  // *******************************************************************************************************************
  // Return true if the given product is free until the given end date - that is, it can be booked as long as the
  // subscription ends on or before the given date. A product is free if it has STATUS_NEW or STATUS_VACATED. It can
  // also be free if it has STATUS_BOOKED or STATUS_VACATED_BOOKED, as long as the date from which it is booked is
  // after the given end date. This method assumes that the product's status has been set for the date on which we need
  // to know whether or not it is free.
  public static function is_free_until($product, $end_date)
  {
    if (self::is_free($product))
    {
      return true;
    }
    if (($product['status'] === Utility::STATUS_BOOKED) || ($product['status'] === Utility::STATUS_VACATED_BOOKED))
    {
      return $end_date < self::get_reserved_date($product);
    }
    return false;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Run the given query, and return an array of products, as described by the get_products and get_product methods.
  protected function get_products_with_query($query)
  {
    global $wpdb;

    // Perform SQL query.
    $results = $wpdb->get_results($query, ARRAY_A);

    // Organise the results into a PHP array. The SQL query has one row for each subscription that
    // belongs to a product. Each row includes the product information, which means that a product
    // is duplicated if it has several subscriptions. On the other hand, if a product has no
    // subscriptions, it appears once, but with empty subscription fields.
    //
    // Create a PHP array with one row for each product. Each product holds product information,
    // and an array of subscriptions.
    $products = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $product_row)
      {
        $product_id = intval($product_row['product_id']);
        // Create product, if not already present.
        if (!isset($products[$product_id]))
        {
          $products[$product_id] = array(
            'id' => $product_id,
            'name' => $product_row['post_title'],
            'location_id' => intval($product_row['location_id']),
            'product_type_id' => intval($product_row['product_type_id']),
            'subscriptions' => array()
          );
        }
        // Add a subscription, if present. The get_subscription method also calculates the subscription's status.
        if (isset($product_row['subscription_id']))
        {
          $products[$product_id]['subscriptions'][] =
            Subscription_Data_Manager::get_subscription($product_row, $this->get_status_date());
        }
      }
    }
    // Calculate the status for each product, based on its subscriptions. Note the reference to the $product object -
    // without it, the changes would not affect the $products table.
    foreach ($products as &$product)
    {
      $product['status'] = $this->get_product_status($product);
    }
    return $products;
  }

  // *******************************************************************************************************************
  // From the given list of product types, return the product type with the given ID, or null if it was not found.
  protected static function get_product_type($product_type_id, $product_types)
  {
    // Note that the & in front of $product_type is for performance reasons. No product types will be modified here.
    foreach ($product_types as &$product_type)
    {
      if (intval($product_type->id) === $product_type_id)
      {
        return $product_type;
      }
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return an array of location IDs, each of which has at least one available product with the given product type ID.
  // The array does not include the location given in $found_at_location_id. The locations are drawn from the given list
  // of products. If no locations have free products, the returned array will be empty.
  protected function list_alternate_locations_for($product_type_id, $found_at_location_id, $products)
  {
    $alternate_locations = array();
    // Note that the & in front of $product is for performance reasons. No product will be modified here.
    foreach ($products as &$product)
    {
      // See if the product is the right type, is free, and resides at an alternate location. If it is, then also check
      // whether its location has previously been added to the list of alternates. If it hasn't, add it now.
      if (($product['product_type_id'] === $product_type_id) && self::is_free($product) &&
        ($product['location_id'] !== $found_at_location_id) && !isset($alternate_locations[$product['location_id']]))
      {
        $alternate_locations[$product['location_id']] = $product['location_id'];
      }
    }

    // Convert from an array with the location ID as a key, to a plain array.
    return array_values($alternate_locations);
  }

  // *******************************************************************************************************************
  // Return a DateTime object that holds the earliest date there will be an available product of the given product type
  // at the location with the given ID. If no product will be available, return null. The products are found in the
  // given list of products.
  protected function find_first_available_date_for($product_type_id, $location_id, $products)
  {
    $first_date = null;
    // Note that the & in front of $product is for performance reasons. No product will be modified here.
    foreach ($products as &$product)
    {
      // See if the product is the right type, resides at the given location, and has been cancelled. If so, it will 
      // eventually be free. Check the date, to see when. If it is earlier than any other product found at the same
      // location, store the date.
      if (($product['product_type_id'] === $product_type_id) && ($product['location_id'] === $location_id) &&
        ($product['status'] === Utility::STATUS_CANCELLED))
      {
        $available_on = $this->get_subscription_end_date($product);
        if (isset($available_on))
        {
          $available_on = $available_on->modify('+1 day');
          if (!isset($first_date) || ($available_on < $first_date))
          {
            $first_date = $available_on;
          }
        }
      }
    }
    return $first_date;
  }

  // *******************************************************************************************************************
  // For the given product, return a DateTime object with the date on which its ongoing subscription ends, or null if it
  // does not have a cancelled subscription. The end date will be returned, even if the product is already booked later
  // on.
  protected function get_subscription_end_date($product)
  {
    // If the product is not cancelled, we won't find a subscription that is.
    $product_status = $product['status'];
    if (($product_status === Utility::STATUS_CANCELLED) || ($product_status === Utility::STATUS_CANCELLED_BOOKED))
    {
      // The product is cancelled. Find the subscription that is cancelled, thus giving the product its status. There
      // should only be one such subscription.
      foreach ($product['subscriptions'] as $subscription)
      {
        if ($subscription['status'] === Utility::SUB_CANCELLED)
        {
          return $subscription['end_date'];
        }
      }
      error_log('Product ' . strval($product['id']) .
        ' had status cancelled or cancelled/booked, but had no cancelled subscriptions. This should not happen.');
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return a DateTime object which contains the date from which the given product has been reserved (that is, booked).
  // Such a date only exists if the product's status is STATUS_BOOKED, STATUS_VACATED_BOOKED or STATUS_CANCELLED_BOOKED;
  // otherwise return null. 
  protected function get_reserved_date($product)
  {
    $status = $product['status'];
    if (($status === Utility::STATUS_BOOKED) || ($status === Utility::STATUS_VACATED_BOOKED) ||
      ($status === Utility::STATUS_CANCELLED_BOOKED))
    {
      // Return the earliest start date of any booked subscriptions we encounter. There should only be one booked
      // subscription, but it is conceivable that this might change in the future. So examine them all, and return the
      // earliest date.
      $start_date = null;
      foreach ($product['subscriptions'] as $subscription)
      {
        if ($subscription['status'] === Utility::SUB_BOOKED)
        {
          // If we haven't recorded a start date, or this subscription's start date is earlier, use this subscription's
          // start date.
          if (!isset($start_date) || ($subscription['start_date'] < $start_date))
          {
            $start_date = $subscription['start_date'];
          }
        }
      }
      // The start date should be set, or the product's status is wrong.
      if (!isset($start_date))
      {
        error_log('Product ' . strval($product['id']) .
          ' had status booked, vacated/booked or cancelled/booked, but had no booked subscriptions. This should not happen.');
      }
      return $start_date;
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return a PHP array of all the product types found at the location with the given ID. The product type ID will be
  // used as the array key. Each entry in the array will be an array that holds the IDs of all the products found that
  // were of the product type given in the key, and had status STATUS_NEW or STATUS_VACATED. If no such products were
  // found, the entry will remain an empty array, which denotes that the product type was found at the given location,
  // but none of the products were free.
  //
  // The method searches for product types in the given table of products, each of which should have the following
  // fields:
  //   id : integer
  //   name : string
  //   location_id : integer
  //   product_type_id : integer
  //   status : integer // Use the STATUS_ constants declared in utility.php.
  //   subscriptions : array
  protected function list_product_types_for_location($products, $location_id)
  {
    $result = array();

    // Note that products are passed by reference, to avoid the overhead of copying them. They will not be modified,
    // anyway.
    foreach ($products as &$product)
    {
      if ($product['location_id'] === $location_id)
      {
        // This product belongs to the requested location. Ensure its product type is added to the table.
        $product_type_id = $product['product_type_id'];
        if (!isset($result[$product_type_id]))
        {
          $result[$product_type_id] = array();
        }
        // If this product is free, add it to the list.
        if (self::is_free($product))
        {
          $result[$product_type_id][] = $product['id'];
        }
      }
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Return a product's status on the reference date, as stored in the $this->status_date field. If status_date is null,
  // today's date will be used.
  // The return value uses the status constants declared in this class.
  //  
  // Each subscription can be:    Start date                          End date
  // - Finished                   Before or at the reference date     Exists; before the reference date
  // - Ongoing                    Before or at the reference date     Does not exist
  // - Cancelled                  Before or at the reference date     Exists; after or at the reference date
  // - Booked                     After the reference date            Who cares?
  //
  // Potential status values are:
  // STATUS_NEW: 0                There are no subscriptions at all.
  //                              = No subscriptions exist
  // STATUS_VACATED: 1            All subscriptions have an end date, and they are all before the reference date.
  //                              = All subscriptions finished
  // STATUS_BOOKED: 2             All subscriptions (there is likely only one) have a start date after the reference
  //                              date.
  //                              = All subscriptions booked
  // STATUS_VACATED_BOOKED: 3     All subscriptions have either an end date before the reference date, or a start date
  //                              after the reference date.
  //                              = All subscriptions either finished or booked
  // STATUS_RENTED: 4             One subscription has a start date before or at the reference date, and no end date.
  //                              = An ongoing subscription exists
  // STATUS_CANCELLED: 5          One subscription has a start date before or at the reference date, and an end date at 
  //                              or after the reference date. No subscription has a start date after the reference
  //                              date.
  //                              = A cancelled subscription exists; no booked subscriptions exist
  // STATUS_CANCELLED_BOOKED: 6   One subscription has a start date before or at the reference date, and an end date at 
  //                              or after the reference date. One (or more, although there shouldn't be any) 
  //                              subscription has a start date after the reference date.
  //                              = A cancelled subscription exists; a booked subscription exists
  protected function get_product_status($product)
  {
    // If there are no subscriptions at all, the product is new, and has never been booked.
    if (empty($product['subscriptions']))
    {
      return Utility::STATUS_NEW;
    }

    // Initialise flags.
    $all_finished = true;
    $all_booked = true;
    $all_finished_or_booked = true;
    $cancelled_found = false;
    $booked_found = false;
 
    // Examine all subscriptions.
    foreach ($product['subscriptions'] as $subscription)
    {
      $status = $subscription['status'];
      // If we find an ongoing subscription, the product is definitely rented.
      if ($status === Utility::SUB_ONGOING)
      {
        return Utility::STATUS_RENTED;
      }
      // Check for the remaining subscription statuses and update flags accordingly.
      if ($status === Utility::SUB_EXPIRED)
      {
        // This one is finished, so all are not booked. All might be booked or finished, though.
        $all_booked = false;
      }
      elseif ($status === Utility::SUB_BOOKED)
      {
        // This one is booked, so all are not finished. All might be booked or finished, though.
        $all_finished = false;
        // Found one.
        $booked_found = true;
      }
      else
      {
        // This one is cancelled, so all are not either booked or finished.
        $all_booked = false;
        $all_finished = false;
        $all_finished_or_booked = false;
        // Found one.
        $cancelled_found = true;
      }
    }

    // Find the product status.
    if ($all_finished)
    {
      return Utility::STATUS_VACATED;
    }
    if ($all_booked)
    {
      return Utility::STATUS_BOOKED;
    }
    if ($all_finished_or_booked)
    {
      return Utility::STATUS_VACATED_BOOKED;
    }
    if ($cancelled_found)
    {
      if ($booked_found)
      {
        return Utility::STATUS_CANCELLED_BOOKED;
      }
      return Utility::STATUS_CANCELLED;
    }
    // I am confused. This should never happen. -1 to sanity.
    return -1;
  }

  // *******************************************************************************************************************
  // Return an array that describes a product, using the information posted to the server.
  protected function get_data_item()
  {
    if (!Utility::string_posted('name') || !Utility::integers_posted(array('location_id', 'product_type_id')))
    {
      return null;
    }

    $product = array(
      'post_author' => $this->get_user_group_user_id(),
      // post_date and post_date_gmt will not be set automatically. When creating items, the caller should set these
      // fields.
      'post_content' => '',
      'post_title' => Utility::read_posted_string('name'),
      'post_excerpt' => '',
      'post_status' => 'publish',
      'comment_status' => 'closed',
      'ping_status' => '',
      'post_password' => '',
      'post_name' => '',
      'to_ping' => '',
      'pinged' => '',
      'post_modified' => current_time('mysql'),
      'post_modified_gmt' => current_time('mysql', 1),
      'post_content_filtered' => '',
      'post_parent' => 0,
      'guid' => '',
      'menu_order' => 0,
      'post_type' => 'listing',
      'post_mime_type' => '',
      'comment_count' => 0,
      // 'users_groups_id' => NULL,
      'subscription' => 1,
      'location_id' => Utility::read_posted_integer('location_id'),
      'product_type_id' => Utility::read_posted_integer('product_type_id')
    );
    if (!Utility::non_empty_strings($product, array('post_title')))
    {
      return null;
    }

    return $product;
  }

  // *******************************************************************************************************************
  // Create several products in the database at once, based on the one given. Return an integer result code that can be
  // used to inform the user of the result of these operations.
  //
  // Note that, at the moment, this method does not call can_create to verify that products can be created.
  protected function create_multiple($data_item)
  {
    global $wpdb;

    // Verify that the product name contains the placeholder that will be replaced with a number.
    if (strpos($data_item['post_title'], self::PRODUCT_NUMBER_PLACEHOLDER) === false)
    {
      return Result::MISSING_NUMBER_PLACEHOLDER_IN_PRODUCT_NAME;
    }

    // Find the range of numbers to be inserted into the name.
    if (!Utility::integers_posted(array('from_number', 'to_number')))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $from_number = Utility::read_posted_integer('from_number');
    $to_number = Utility::read_posted_integer('to_number');
    // If the numbers are in the wrong order, switch them.
    if ($to_number < $from_number)
    {
      $temp = $to_number;
      $to_number = $from_number;
      $from_number = $temp;
    }

    // Find out whether to pad numbers with leading zeroes - and if so, to what length.
    $pad_number = false;
    if (Utility::string_posted('pad_with_zeroes'))
    {
      if (!Utility::integer_posted('digit_count'))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      $pad_number = true;
      // The number of digits can be between 1 and 10.
      $digit_count = Utility::clamp_number(Utility::read_posted_integer('digit_count'), 1, 10);
    }

    // Compose the SQL query to insert all the products at once. When creating several products, the only thing that
    // will vary is the name (stored in post_title). Create a set of data that can be inserted, that contains everything
    // before and after the post_title. That way, we can add products to the query string by just inserting the
    // post_title.
    $query = "INSERT INTO {$this->database_table} (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, subscription, location_id, product_type_id) VALUES ";
    $product_data_1 = $this->get_product_data_1($data_item);
    $product_data_2 = $this->get_product_data_2($data_item);
    // Complete the query by inserting the post_title. Replace the placeholder with a number - padded or unpadded, as
    // appropriate.
    if ($pad_number)
    {
      for ($i = $from_number; $i <= $to_number; $i++)
      {
        $query .= $product_data_1 . str_replace('#', Utility::pad_number($i, $digit_count), $data_item['post_title']) .
          $product_data_2;
      }
    }
    else
    {
      for ($i = $from_number; $i <= $to_number; $i++)
      {
        $query .= $product_data_1 . str_replace('#', strval($i), $data_item['post_title']) . $product_data_2;
      }
    }
    // Remove the final comma, and add a semicolon instead.
    $query = substr($query, 0, -1);
    $query .= ';';

    // Perform the bulk insert query.
    $result = $wpdb->query($query);
    if ($result === false)
    {
      // Database error.
      error_log("Error while creating multiple products: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result === 0)
    {
      // We failed to insert anything.
      error_log("Failed to insert any rows while creating multiple products. Expected: lots. Actual: nope.");
      return Result::DATABASE_QUERY_FAILED;
    }
    $expected = $to_number - $from_number + 1;
    if ($result !== $expected)
    {
      // We inserted some rows, but not all. We've no idea how that happened, but report it the client nonetheless.
      error_log("Failed to insert the expected number of rows while creating multiple products. Expected: {$expected}. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED_PARTIALLY;
    }
    // The expected number of products was inserted. Report success.
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Return a string that contains all the data values from the given data item, so that the string can be used in an
  // SQL statement to insert data into the database. This method returns the first part, which comes before the
  // post_title field.
  protected function get_product_data_1($data_item)
  {
    $text = "(";
    $text .= strval($data_item['post_author']);
    $text .= ", '";
    // The date is already a string. If it wasn't, we could convert it using:
    //   $data_item['post_date']->format('Y-m-d H:i:s');
    $text .= $data_item['post_date'];
    $text .= "', '";
    $text .= $data_item['post_date_gmt'];
    $text .= "', '";
    $text .= $data_item['post_content'];
    $text .= "', '";
    return $text;
  }

  // *******************************************************************************************************************
  // Return a string that contains all the data values from the given data item, so that the string can be used in an
  // SQL statement to insert data into the database. This method returns the second part, which comes after the
  // post_title field.
  protected function get_product_data_2($data_item)
  {
    $text = "', '";
    $text .= $data_item['post_excerpt'];
    $text .= "', '";
    $text .= $data_item['post_status'];
    $text .= "', '";
    $text .= $data_item['comment_status'];
    $text .= "', '";
    $text .= $data_item['ping_status'];
    $text .= "', '";
    $text .= $data_item['post_password'];
    $text .= "', '";
    $text .= $data_item['post_name'];
    $text .= "', '";
    $text .= $data_item['to_ping'];
    $text .= "', '";
    $text .= $data_item['pinged'];
    $text .= "', '";
    $text .= $data_item['post_modified'];
    $text .= "', '";
    $text .= $data_item['post_modified_gmt'];
    $text .= "', '";
    $text .= $data_item['post_content_filtered'];
    $text .= "', ";
    $text .= strval($data_item['post_parent']);
    $text .= ", '";
    $text .= $data_item['guid'];
    $text .= "', ";
    $text .= strval($data_item['menu_order']);
    $text .= ", '";
    $text .= $data_item['post_type'];
    $text .= "', '";
    $text .= $data_item['post_mime_type'];
    $text .= "', ";
    $text .= strval($data_item['comment_count']);
    $text .= ", ";
    $text .= strval($data_item['subscription']);
    $text .= ", ";
    $text .= strval($data_item['location_id']);
    $text .= ", ";
    $text .= strval($data_item['product_type_id']);
    $text .= "),";
    return $text;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the data item with the given ID can be deleted from the database. If not, return another
  // result code defined in utility.php. Descendants may want to override this method.
/*
  protected function can_delete($id)
  {
      // *** // Verify that the storage room has no active or booked subscriptions. Finished subscriptions are acceptable.
    return Result::OK;
  }
*/
  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_status_date()
  {
    return $this->status_date;
  }

  // *******************************************************************************************************************

  public function set_status_date($new_value)
  {
    if (($new_value === null) || ($new_value instanceof DateTime))
    {
      $this->status_date = $new_value;
    }
  }

  // *******************************************************************************************************************
}
?>