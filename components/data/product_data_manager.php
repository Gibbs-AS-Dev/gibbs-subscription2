<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/product_info_utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/subscription_utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/offer/offer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_subscription_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_rule_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/offer_data_manager.php';

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
    $this->add_action('get_available_product_types', Utility::ROLE_NONE, 'get_available_product_types');
    $this->add_action('get_available_product_types', Utility::ROLE_USER, 'get_available_product_types');
    $this->add_action('get_available_product_types', Utility::ROLE_COMPANY_ADMIN, 'get_available_product_types');
    $this->add_action('get_available_product', Utility::ROLE_COMPANY_ADMIN, 'get_available_product');
    $this->add_action('cancel_subscription', Utility::ROLE_COMPANY_ADMIN, 'cancel_subscription');
    $this->add_action('set_product_notes', Utility::ROLE_COMPANY_ADMIN, 'set_product_notes');
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
  //   enabled : boolean
  //   modified_date : DateTime
  //   location_id : integer
  //   product_type_id : integer
  //   status : integer // Use the STATUS_ constants declared in utility.php.
  //   ready_status : integer // Use the READY_STATUS_ constants defined in utility.php.
  //   access_code : string // String which holds the access code to use for the storage unit or location lock.
  //   access_link : string // String which holds a URL that can be used to unlock the storage unit or location.
  //   product_notes : string
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
    global $wpdb;

    // Compose SQL query. The ID is unique, but we will only return the product with that ID if the other WHERE clauses
    // are also satisfied.
    $sql = $wpdb->prepare("
        SELECT 
          p.{$this->id_db_name} AS product_id,
          p.post_title,
          p.post_status,
          p.location_id,
          p.product_type_id,
          p.post_modified,
          pm.meta_value AS ready_status,
          pm2.meta_value AS product_notes,
          pm3.meta_value AS access_code,
          pm4.meta_value AS access_link,
          s.id AS subscription_id,
          s.buyer_id,
          s.start_date,
          s.end_date
        FROM 
          {$this->database_table} p
        LEFT JOIN
          {$wpdb->prefix}postmeta pm ON p.{$this->id_db_name} = pm.post_id AND pm.meta_key = 'ready_status'
        LEFT JOIN
          {$wpdb->prefix}postmeta pm2 ON p.{$this->id_db_name} = pm2.post_id AND pm2.meta_key = 'product_notes'
        LEFT JOIN
          {$wpdb->prefix}postmeta pm3 ON p.{$this->id_db_name} = pm3.post_id AND pm3.meta_key = 'access_code'
        LEFT JOIN
          {$wpdb->prefix}postmeta pm4 ON p.{$this->id_db_name} = pm4.post_id AND pm4.meta_key = 'access_link'
        LEFT JOIN 
          subscriptions s ON p.{$this->id_db_name} = s.product_id
        WHERE
          p.{$this->id_db_name} = %d AND
          p.post_author = {$this->get_user_group_user_id()} AND
          p.post_type = 'subscription' AND
          p.subscription IS NOT NULL;
      ",
      $product_id
    );

    // Return the results.
    $products = $this->get_products_with_query($sql);
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
  //   enabled : boolean
  //   modified_date : DateTime
  //   location_id : integer
  //   product_type_id : integer
  //   status : integer // Use the STATUS_ constants declared in utility.php.
  //   ready_status : integer // Use the READY_STATUS_ constants defined in utility.php.
  //   access_code : string // String which holds the access code to use for the storage unit or location lock.
  //   access_link : string // String which holds a URL that can be used to unlock the storage unit or location.
  //   product_notes : string
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
    global $wpdb;

    // Compose SQL query.
    $query = "
      SELECT 
        p.{$this->id_db_name} AS product_id,
        p.post_title,
        p.post_status,
        p.location_id,
        p.product_type_id,
        p.post_modified,
        pm.meta_value AS ready_status,
        pm2.meta_value AS product_notes,
        pm3.meta_value AS access_code,
        pm4.meta_value AS access_link,
        s.id AS subscription_id,
        s.buyer_id,
        s.start_date,
        s.end_date
      FROM 
        {$this->database_table} p
      LEFT JOIN
        {$wpdb->prefix}postmeta pm ON p.{$this->id_db_name} = pm.post_id AND pm.meta_key = 'ready_status'
      LEFT JOIN
        {$wpdb->prefix}postmeta pm2 ON p.{$this->id_db_name} = pm2.post_id AND pm2.meta_key = 'product_notes'
      LEFT JOIN
        {$wpdb->prefix}postmeta pm3 ON p.{$this->id_db_name} = pm3.post_id AND pm3.meta_key = 'access_code'
      LEFT JOIN
        {$wpdb->prefix}postmeta pm4 ON p.{$this->id_db_name} = pm4.post_id AND pm4.meta_key = 'access_link'
      LEFT JOIN 
        subscriptions s ON p.{$this->id_db_name} = s.product_id
      WHERE
        p.post_author = {$this->get_user_group_user_id()} AND
        p.post_type = 'subscription' AND
        p.subscription IS NOT NULL
      ORDER BY
        p.location_id, p.post_title;
    ";

    // Return the results.
    return $this->get_products_with_query($query);
  }

  // *******************************************************************************************************************
  // Read all products owned by the current user group from the database. Return them as a string containing a
  // Javascript array declaration. Use the c.prd column constants.
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
          $table = Utility::remove_final_comma($table);
        }
        // Status. Uses the STATUS_ constants declared in utility.php.
        $table .= "], ";
        $table .= strval($product['status']);
        // Subscription end date. This is present if the status is STATUS_CANCELLED or STATUS_CANCELLED_BOOKED;
        // otherwise it should be an empty string.
        $table .= ", '";
        $table .= Utility::date_to_string($this->get_subscription_end_date($product));
        // Reserved date. This is only present if the status is STATUS_BOOKED, STATUS_VACATED_BOOKED or
        // STATUS_CANCELLED_BOOKED; otherwise it should be an empty string.
        $table .= "', '";
        $table .= Utility::date_to_string($this->get_reserved_date($product));
        // Modified date.
        $table .= "', '";
        $table .= $product['modified_date']->format('Y-m-d H:i:s');
        // Enabled flag.
        $table .= "', ";
        $table .= var_export($product['enabled'], true);
        // Access code.
        $table .= ", '";
        $table .= $product['access_code'];
        // Access link.
        $table .= "', '";
        $table .= $product['access_link'];
        // Ready status.
        $table .= "', ";
        $table .= $product['ready_status'];
        // Notes.
        $table .= ", '";
        $table .= $product['product_notes'];
        $table .= "'],";
      }
      $table = Utility::remove_final_comma($table);
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

    // Remove the product readiness status, which goes in a separate table.
    $ready_status = $data_item['ready_status'];
    unset($data_item['ready_status']);

    // Start transaction to create the product, and add its ready status in ptn_postmeta.
    $wpdb->query('START TRANSACTION');

    // Insert a new record in ptn_posts, and check the result.
    $result = $wpdb->insert($this->database_table, $data_item);
    if ($result === false)
    {
      error_log("Error while creating product: {$wpdb->last_error}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Failed to insert the correct number of rows while creating product. Expected: 1. Actual: {$result}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    // Store the ID of the last inserted item.
    $this->created_item_id = $wpdb->insert_id;

    // Add product readiness status in ptn_postmeta, and check the result.
    $result = Product_Info_Utility::set_ready_status($this->created_item_id, $ready_status);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Commit the transaction.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while creating product: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Update an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_update method.
  //
  // The item to be updated, and its ID, can be passed as parameters. If not, they will be read from the request.
  //
  // Override to also update the product readiness status, which is stored in a separate table.
  public function update($id = null, $data_item = null)
  {
    global $wpdb;

    // Sanitise input data, and ensure the ID is available as well.
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
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

    // Extract the product readiness status from the data item. It goes in a separate table.
    $ready_status = $data_item['ready_status'];
    unset($data_item['ready_status']);

    // Start transaction to update both the product and the ready status.
    $wpdb->query('START TRANSACTION');

    // Update the product.
    $result = parent::update($id, $data_item);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Set the ready status. If it did not exist, create it.
    $result = Product_Info_Utility::set_or_update_ready_status($id, $ready_status);
    if (Result::is_error($result))
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Commit the transaction.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while updating product: ' . $wpdb->last_error);
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
  // Override to first delete the product readiness status. This is stored in a separate table.
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

    // Ensure the data item with that ID can be deleted.
    $result = $this->can_delete($id);
    if ($result !== Result::OK)
    {
      return $result;
    }

    // Delete product readiness status.
    $wpdb->query('START TRANSACTION');
    $result = Product_Info_Utility::delete_ready_status($id);
    if (Result::is_error($result))
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Delete the data item itself.
    $result = parent::delete($id);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // All operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while deleting product: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Create a test subscription, without going through the booking process. Test subscriptions can start in the past.
  public function create_test_subscription()
  {
    // Check the application role. Test subscriptions cannot be created in production.
    $settings = Settings_Manager::read_settings($this->access_token);
    if ($settings->get_application_role() === Settings::APP_ROLE_PRODUCTION)
    {
      return Result::INCORRECT_APPLICATION_ROLE;
    }

    // Verify posted data.
    if (!Utility::integers_posted(array('product_id', 'location_id', 'product_type_id', 'buyer_id')))
    {
      return Result::MISSING_INPUT_FIELD;
    }

    // Read product ID.
    $product_id = Utility::read_posted_integer('product_id');

    // Look up the product type in the database.
    $product_type_data = new Product_Type_Data_Manager($this->access_token);
    $product_type = $product_type_data->get_product_type(Utility::read_posted_integer('product_type_id'));
    if ($product_type === null)
    {
      return Result::MISSING_INPUT_FIELD;
    }

    // Create an offer to hold the product ID. We will use the base price, and not worry about capacity or special
    // offers.
    $offer = new Offer(Utility::read_posted_integer('location_id'), $product_type);
    $offer->set_product_ids(array($product_id));

    // Create the subscription.
    $subscription_data = new User_Subscription_Data_Manager($this->access_token);
    $subscription_data->set_user_id(Utility::read_posted_integer('buyer_id'));
    $subscription_data->set_allow_expired_subscriptions(true);
    $subscription_data->set_offer($offer);
    $result = $subscription_data->create();

    // If the subscription was created successfully, activate it straight away.
    if ($result === Result::OK)
    {
      $result = $subscription_data->set_subscription_active_flag($subscription_data->get_created_item_id(), true);
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Compose a list of product types, with information on whether or not any products of that type are available on the
  // selected date. Return the list as a string containing a JSON array declaration, where each entry has the following
  // fields (on the client, use the c.apt constants):
  //   id                         The product type ID.
  //   name                       The product type name.
  //   price                      The price per month the buyer will have to pay, unless altered by special offers. The
  //                              price may have been modified due to the location's capacity.
  //   price_mods                 List of price mods that apply to this product type at the given location. Each entry
  //                              is an array with the following fields:
  //                                price_mod      The percentage change in prices. -10 is a 10% discount.
  //                                duration       The number of months that this change will last, or -1 if the change
  //                                               applies indefinitely.
  //                              If the product is not available, or if there are no applicable price mods, the value
  //                              will be null.
  //   category_id                The ID of the category to wich the product type belongs.
  //   is_available               A boolean value that says whether the product type is available at the selected
  //                              location, from the selected date.
  //   alternative_location_ids   If the product type is not available, this is an array of IDs of all the other
  //                              locations where the selected product type is available. If the product is available,
  //                              or if no alternative locations exist, the list will be empty.
  //   first_available_date       If the product type is not available, this is the date, as a string in the format
  //                              "yyyy-mm-dd", on which the product type becomes available for rent. If there is no
  //                              such date, the value will be null.
  //   available_product_count    The number of products in this category that are free and can be booked. If the
  //                              product type is unavailable, the number will be 0.
  //   product_name               An empty string. This method returns information about product types, not a particular
  //                              product.
  //   base_price                 The product type's base price, before capacity and special offer modifiers. This
  //                              figure is only available to administrators. If the user is not an administrator, the
  //                              column will not be present at all.
  //
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

    // Initialise.
    $available_product_types = array();
    $offers = array();
    $product_type_data = new Product_Type_Data_Manager($this->access_token);
    $price_data = new Price_Rule_Data_Manager($this->access_token);

    // Read all product types from the database. The client needs information about each one.
    $product_types = $product_type_data->get_product_types();

    // Find which product types exist at the location, and whether or not they are available. The result will be an
    // array of product types, using the product type ID as the array key. Each entry will be an array with the IDs of
    // products that have status STATUS_NEW or STATUS_VACATED on the selected date, and which are bookable - that is,
    // they are enabled and have ready status "yes".
    $availability_list = $this->list_product_types_for_location($products, $selected_location_id);

    // Compose the full list of product types found at the location. Note that the & in front of $availabile_products is
    // for performance reasons. Available products will not be modified here.
    foreach ($availability_list as $product_type_id => &$available_products)
    {
      $product_type = self::get_product_type($product_type_id, $product_types);
      if (isset($product_type))
      {
        $available_product_count = count($available_products);
        // $is_available states whether the product type can be rented, not whether it is found at the location.
        $is_available = $available_product_count > 0;

        // If required, limit the list of available products to what is needed.
        $settings = Settings_Manager::read_settings($this->access_token);
        if ($settings->get_bookable_product_count() > 0)
        {
          $available_products = array_slice($available_products, 0, $settings->get_bookable_product_count());
        }

        $price_mods = null;
        if ($is_available)
        {
          $alternative_location_ids = array();
          $first_available_date = null;
          // Create an offer, and apply any applicable price rules.
          $offer = new Offer($selected_location_id, $product_type);
          $offer->set_product_ids($available_products);
          $price_data->apply_price_rules_to($offer);
          // Add the offer to the list which will be stored on the session for later.
          $offers[] = $offer;
          // Calculate the capacity price.
          $price = $offer->get_capacity_price();
          // Get price modifiers from a special offer, if available.
          if ($offer->has_special_offer_price_mod())
          {
            // Remove the keys from both the price mods array, and its contents. We send just the raw data to the
            // client, for performance reasons.
            $price_mods = array_values($offer->get_special_offer_price_mods());
            $price_mods = array_map('array_values', $price_mods);
          }
        }
        else
        {
          // The product type is not available for rent at the location, so we know nothing about what the price might
          // have been.
          $price = -1;
          // For all unavailable product types, find alternative locations where the product type is available, and the date
          // when the product might be available at the original location, if possible.
          $alternative_location_ids =
            $this->list_alternate_locations_for($product_type_id, $selected_location_id, $products);
          $first_available_date =
            Utility::date_to_string(
              $this->find_first_available_date_for($product_type_id, $selected_location_id, $products), null);
        }
        // Add an available product type for this product type.
        $available_product_type = array(
          $product_type_id,
          $product_type->name,
          $price,
          $price_mods,
          intval($product_type->category_id),
          $is_available,
          $alternative_location_ids,
          $first_available_date,
          $available_product_count,
          ''
        );
        // Add the base price, if available.
        if ($this->access_token->is_company_admin())
        {
          $available_product_type[] = $product_type->price;
        }
        // Add the available product type to the list.
        $available_product_types[] = $available_product_type;
      }
      else
      {
        error_log ('A product specified a product type (' . strval($product_type_id) .
          ') that was not found in the list of product types. This hole in the fabric of the universe should never occur.');
      }
    }

    // Store offers on the session, so they can be used to compose a subscription later.
    Offer_Data_Manager::store_offers_on_session($offers);

    // Sort the available product types by capacity price, in ascending order. However, place unavailable types last and
    // sort them alphabetically.
    usort($available_product_types, array('Product_Data_Manager', 'compare_available_product_types'));    

    // Compose JSON.
    return json_encode($available_product_types);
  }

  // *******************************************************************************************************************
  // Compare two available product types $a and $b, for the purposes of sorting. If both are unavailable, sort them
  // alphabetically. If either product type is unavailable, sort them last. If both are available, sort them on capacity
  // price, in ascending order.
  public static function compare_available_product_types($a, $b)
  {
    $a_unavailable = $a[2] < 0;
    $b_unavailable = $b[2] < 0;

    // If both product types are unavailable, sort them alphabetically by name.
    if ($a_unavailable && $b_unavailable)
    {
      return strcmp($a[1], $b[1]);
    }
    // If a is unavailable, place it last.
    if ($a_unavailable)
    {
      return 1;
    }
    // If b is unavailable, place it last.
    if ($b_unavailable)
    {
      return -1;
    }
    // Both product types are available. Sort them on capacity price, in ascending order.
    return $a[2] - $b[2];
  }

  // *******************************************************************************************************************
  // For a specific product, determine if that product is available on the selected date. If so, return a JSON array
  // declaration with a single entry for the product's product type. If not, return an empty array. The returned entry
  // has the following fields (on the client, use the c.apt constants):
  //   id                         The product type ID.
  //   name                       The product type name.
  //   price                      The price per month the buyer will have to pay, unless altered by special offers. The
  //                              price may have been modified due to the location's capacity.
  //   price_mods                 List of price mods that apply to this product type at the given location. Each entry
  //                              is an array with the following fields:
  //                                price_mod      The percentage change in prices. -10 is a 10% discount.
  //                                duration       The number of months that this change will last, or -1 if the change
  //                                               applies indefinitely.
  //                              If the product is not available, or if there are no applicable price mods, the value
  //                              will be null.
  //   category_id                The ID of the category to wich the product type belongs.
  //   is_available               A boolean value that says whether the product type is available at the selected
  //                              location, from the selected date. If this method returns an available product type, it
  //                              is always available (or else the method returns an empty list), so this value is
  //                              always true.
  //   alternative_location_ids   An empty list, as the product type is always available.
  //   first_available_date       Always null, as the product type is always available.
  //   available_product_count    Always 1, as this method only checks a single product.
  //   product_name               The name of the requested product.
  //   base_price                 The product type's base price, before capacity and special offer modifiers. This
  //                              figure is only available to administrators. This method should only be called when the
  //                              user is an administrator, so the figure should always be present.
  //
  // If the query did not succeed, return an integer error code from the Result class instead. Note that the return
  // value from this function is equivalent to the return from the get_available_product_types method, and can be
  // processed in the same way.
  public function get_available_product()
  {
    // Read parameters.
    if (!Utility::integer_passed('selected_location_id') || !Utility::integer_passed('selected_product_id') ||
      !Utility::date_passed('selected_date'))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $selected_location_id = Utility::read_passed_integer('selected_location_id');
    $selected_product_id = Utility::read_passed_integer('selected_product_id');
    $this->set_status_date(Utility::read_passed_date('selected_date'));

    // Read the selected product from the database. The product and its subscriptions will have their status set to the
    // value it will be on the selected date.
    $product = $this->get_product($selected_product_id);

    // Initialise.
    $available_product_types = array();
    $offers = array();
    $product_type_data = new Product_Type_Data_Manager($this->access_token);
    $price_data = new Price_Rule_Data_Manager($this->access_token);

    // Create an offer for the product only if the product was found, was in the right location, and is free to be
    // booked.
    if (isset($product) && ($selected_location_id === $product['location_id']) && self::is_free($product))
    {
      // Read the product's product type from the database.
      $product_type_data = new Product_Type_Data_Manager($this->access_token);
      $product_type = $product_type_data->get_product_type($product['product_type_id']);
      if (isset($product_type))
      {
        // Create an offer, and apply any applicable price rules.
        $offer = new Offer($selected_location_id, $product_type);
        $offer->set_product_ids(array($selected_product_id));
        $price_data->apply_price_rules_to($offer);
        // Add the offer to the list which will be stored on the session for later.
        $offers[] = $offer;
        // Calculate the capacity price.
        $price = $offer->get_capacity_price();
        // Get price modifiers from a special offer, if available.
        if ($offer->has_special_offer_price_mod())
        {
          // Remove the keys from both the price mods array, and its contents. We send just the raw data to the
          // client, for performance reasons.
          $price_mods = array_values($offer->get_special_offer_price_mods());
          $price_mods = array_map('array_values', $price_mods);
        }
        else
        {
          $price_mods = null;
        }
        // Add an available product type for this product.
        $available_product_type = array(
          $product_type->id,
          $product_type->name,
          $price,
          $price_mods,
          intval($product_type->category_id),
          true,
          array(),
          null,
          1,
          $product['name']
        );
        // Add the base price, if available.
        if ($this->access_token->is_company_admin())
        {
          $available_product_type[] = $product_type->price;
        }
        // Add the available product type to the list.
        $available_product_types[] = $available_product_type;
      }
    }

    // Store offers on the session, so they can be used to compose a subscription later.
    Offer_Data_Manager::store_offers_on_session($offers);

    // Compose JSON.
    return json_encode($available_product_types);
  }

  // *******************************************************************************************************************
  // Use a User_Subscription_Data_Manager to cancel a product's ongoing subscription. Return an integer result code that
  // can be used to inform the user of the result of the operation. For details, see
  // User_Subscription_Data_Manager.cancel_subscription_any_time. Note that the caller must provide the ID of the
  // subscription to be cancelled, not the product ID.
  public function cancel_subscription()
  {
    $subscription_data = new User_Subscription_Data_Manager($this->access_token);
    return $subscription_data->cancel_subscription_any_time();
  }

  // *******************************************************************************************************************
  // Store the provided product_notes for the product with the given product_id in the ptn_postmeta table. product_notes
  // and product_id must be posted to the server.
  public function set_product_notes()
  {
    // Read parameters.
    if (!Utility::integer_posted('product_id') || !Utility::string_posted('product_notes'))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $product_id = Utility::read_posted_integer('product_id');
    $product_notes = Utility::read_posted_string('product_notes');

    return Product_Info_Utility::set_or_update_product_notes($product_id, $product_notes);
  }

  // *******************************************************************************************************************
  // Return true if the given product can be booked when it has no active subscription. This is the case if it is
  // enabled and has ready status "yes".
  public static function is_bookable($product)
  {
    return $product['enabled'] && ($product['ready_status'] === Utility::READY_STATUS_YES);
  }

  // *******************************************************************************************************************
  // Return true if the given product is free and can be booked. This is the case if it has STATUS_NEW or
  // STATUS_VACATED, and is bookable when is has no subscription. This method assumes that the product's status has been
  // set for the date on which we need to know whether or not it is free.
  public static function is_free($product)
  {
    return (($product['status'] === Utility::STATUS_NEW) || ($product['status'] === Utility::STATUS_VACATED)) &&
      self::is_bookable($product);
  }

  // *******************************************************************************************************************
  // Return true if the given product is free until the given end date - that is, it can be booked as long as the
  // subscription ends on or before the given date. A product is free if it has STATUS_NEW or STATUS_VACATED, and is
  // bookable when is has no subscription. It can also be free if it has STATUS_BOOKED or STATUS_VACATED_BOOKED, and is
  // bookable when is has no subscription, as long as the date from which it is booked is after the given end date. This
  // method assumes that the product's status has been set for the date on which we need to know whether or not it is
  // free.
  public static function is_free_until($product, $end_date)
  {
    if (self::is_free($product))
    {
      return true;
    }
    if ((($product['status'] === Utility::STATUS_BOOKED) || ($product['status'] === Utility::STATUS_VACATED_BOOKED)) &&
      self::is_bookable($product))
    {
      return $end_date < self::get_reserved_date($product);
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return the value of the enabled flag for the product with the given $product_id, or null if it could not be found.
  public function get_enabled($product_id)
  {
    global $wpdb;

    // Compose SQL query. The ID is unique, but we will only return data for the product with that ID if the other WHERE
    // clauses are also satisfied.
    $sql = $wpdb->prepare("
        SELECT 
          p.post_status
        FROM 
          {$this->database_table} p
        WHERE
          p.{$this->id_db_name} = %d AND
          p.post_author = {$this->get_user_group_user_id()} AND
          p.post_type = 'subscription' AND
          p.subscription IS NOT NULL;
      ",
      $product_id
    );

    // Interpret the results.
    $results = $wpdb->get_results($sql, ARRAY_A);
    if (Utility::array_with_one($results) && isset($results[0]['post_status']))
    {
      if ($results[0]['post_status'] === 'publish')
      {
        return true;
      }
      return false;
    }
    return null;
  }
  
  // *******************************************************************************************************************
  // Set the value of the enabled flag for the product with the given $product_id to the value passed in $enabled.
  // Return a result code to indicate the result of the operation. If the operation succeeded, the return value will be
  // Result::OK.
  public function set_enabled($product_id, $enabled)
  {
    global $wpdb;

    if ($enabled)
    {
      $new_value = 'publish';
    }
    else
    {
      $new_value = 'draft';
    }

    $result = $wpdb->update($this->database_table, array('post_status' => $new_value),
      array($this->id_db_name => $product_id));
    if ($result === false)
    {
      error_log("Error while setting the enabled flag for product {$product_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Database query updated the wrong number of rows while setting the enabled flag for product {$product_id}. Expected: 1. Actual: {$result}. Attempted to set the flag to: {$new_value}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
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
          // Find ready status.
          if ($product_row['ready_status'] === null)
          {
            $ready_status = Utility::READY_STATUS_YES;
          }
          else
          {
            $ready_status = intval($product_row['ready_status']);
          }
          // Find access code.
          if ($product_row['access_code'] === null)
          {
            $access_code = '';
          }
          else
          {
            $access_code = $product_row['access_code'];
          }
          // Find access link.
          if ($product_row['access_link'] === null)
          {
            $access_link = '';
          }
          else
          {
            $access_link = $product_row['access_link'];
          }
          // Find product notes.
          if ($product_row['product_notes'] === null)
          {
            $product_notes = '';
          }
          else
          {
            $product_notes = $product_row['product_notes'];
          }
          // Create the product.
          $products[$product_id] = array(
            'id' => $product_id,
            'name' => $product_row['post_title'],
            'enabled' => ($product_row['post_status'] === 'publish' ? true : false),
            'modified_date' => new DateTime($product_row['post_modified']),
            'location_id' => intval($product_row['location_id']),
            'product_type_id' => intval($product_row['product_type_id']),
            'ready_status' => $ready_status,
            'access_code' => $access_code,
            'access_link' => $access_link,
            'product_notes' => $product_notes,
            'subscriptions' => array()
          );
        }
        // Add a subscription, if present. The get_subscription method also calculates the subscription's status.
        if (isset($product_row['subscription_id']))
        {
          $products[$product_id]['subscriptions'][] =
            Subscription_Utility::get_subscription($product_row, $this->get_status_date());
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
  // given list of products. This method assumes that there are no currently available products of the given product
  // type at the given location.
  protected function find_first_available_date_for($product_type_id, $location_id, $products)
  {
    $first_date = null;
    // Note that the & in front of $product is for performance reasons. No product will be modified here.
    foreach ($products as &$product)
    {
      // See if the product is the right type, resides at the given location, and has been cancelled but can be booked
      // (it can be booked if it is not disabled, and the ready status is READY_STATUS_YES). If so, it will eventually
      // be free. Check the date, to see when. If it is earlier than any other product found at the same location, store
      // the date.
      if (($product['product_type_id'] === $product_type_id) && ($product['location_id'] === $location_id) &&
        ($product['status'] === Utility::STATUS_CANCELLED) && $product['enabled'] &&
        ($product['ready_status'] === Utility::READY_STATUS_YES))
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
  // on. If $include_vacated is true, the method will also return a DateTime object with the date on which its previous
  // subscription ended, if the product was previously vacated, but is now empty or booked.
  protected function get_subscription_end_date($product, $include_vacated = false)
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
    if ($include_vacated &&
      (($product_status === Utility::STATUS_VACATED) || ($product_status === Utility::STATUS_VACATED_BOOKED)))
    {
      $end_date = null;
      // The product was vacated. Find the subscription that expired most recently.
      foreach ($product['subscriptions'] as $subscription)
      {
        if ($subscription['status'] === Utility::SUB_EXPIRED)
        {
          // If this is the first expired subscription, store it. If not, store it if it expired more recently than the
          // one we already knew about.
          if (!isset($end_date) || ($subscription['end_date'] > $end_date))
          {
            $end_date = $subscription['end_date'];
          }
        }
      }
      if (!isset($end_date))
      {
        error_log('Product ' . strval($product['id']) .
          ' had status vacated or vacated/booked, but had no expired subscriptions. This should not happen.');
      }
      return $end_date;
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
  // were of the product type given in the key, had status STATUS_NEW or STATUS_VACATED, and were bookable - that is,
  // they were enabled and had ready status "yes". If no such products were found, the entry will remain an empty array,
  // which denotes that the product type was found at the given location, but none of the products were free.
  //
  // The method searches for product types in the given table of products, each of which should have the following
  // fields:
  //   id : integer
  //   name : string
  //   location_id : integer
  //   product_type_id : integer
  //   status : integer // Use the STATUS_ constants declared in utility.php.
  //   subscriptions : array
  // It may have more fields, but they are not needed.
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
        // If this product is free and can be booked, add it to the list.
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
  // STATUS_NEW: 0                There are no subscriptions at all, and never have been since the product was created.
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
  // Return an array that describes a product, using the information posted to the server. The array will include the
  // ready_status entry, although that must be extracted and stored separately, as it goes in a different table.
  protected function get_data_item()
  {
    if (!Utility::string_posted('name') ||
      !Utility::integers_posted(array('location_id', 'product_type_id', 'ready_status')))
    {
      return null;
    }
    $ready_status = Utility::read_posted_integer('ready_status');
    if (!Utility::is_valid_ready_status($ready_status))
    {
      return null;
    }

    $product = array(
      // The product readiness status should not be part of the data item when written to the database. It must be
      // extracted and stored separately in the ptn_postmeta table.
      'ready_status' => $ready_status,
      'post_author' => $this->get_user_group_user_id(),
      // post_date and post_date_gmt will not be set automatically. When creating items, the caller should set these
      // fields.
      'post_content' => '',
      'post_title' => Utility::read_posted_string('name'),
      'post_excerpt' => '',
      'post_status' => (Utility::read_posted_boolean('enabled') === true ? 'publish' : 'draft'),
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
      'post_type' => 'subscription',
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

    // Remove the product readiness status, which goes in a separate table.
    $ready_status = $data_item['ready_status'];
    unset($data_item['ready_status']);

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
    $query = Utility::remove_final_comma($query) . ';';

    // Start transaction to create several products, and add their ready_status values in ptn_postmeta.
    $wpdb->query('START TRANSACTION');

    // Perform the bulk insert query.
    $result = $wpdb->query($query);
    if ($result === false)
    {
      // Database error.
      error_log("Error while creating multiple products: {$wpdb->last_error}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result === 0)
    {
      // We failed to insert anything.
      error_log("Failed to insert any rows while creating multiple products. Expected: lots. Actual: nope.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    $expected = $to_number - $from_number + 1;
    if ($result !== $expected)
    {
      // We inserted some rows, but not all. We've no idea how that happened, but report it the client nonetheless.
      error_log("Failed to insert the expected number of rows while creating multiple products. Expected: {$expected}. Actual: {$result}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED_PARTIALLY;
    }
    // The expected number of products was inserted. Store the IDs of the inserted elements.
    $product_ids = range($wpdb->insert_id, $wpdb->insert_id + $expected - 1);
    $this->created_item_id = $product_ids;

    // Do another bulk insert query to add a ready status for each of the created products.
    $values = [];
    foreach ($product_ids as $product_id)
    {
      $values[] = '(' . $product_id . ', "ready_status", ' . $ready_status . ')';
    }
    $query = "INSERT INTO {$wpdb->prefix}postmeta (post_id, meta_key, meta_value) VALUES " . implode(', ', $values);
    $result = $wpdb->query($query);
    if ($result === false)
    {
      // Database error.
      error_log("Error while adding ready status for multiple created products: {$wpdb->last_error}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== $expected)
    {
      // We either inserted nothing, or just a few rows, but not all. Report the error.
      error_log("Failed to insert the expected number of rows while adding ready status for multiple created products. Expected: {$expected}. Actual: {$result}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // Everything succeeded. Commit the transaction and report success.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while creating multiple products: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
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
  // result code defined in utility.php.
  //
  // Override to verify that the product exists, and has no active or booked subscriptions.
  protected function can_delete($id)
  {
    // Verify that the product exists.
    $product = $this->get_product($id);
    if ($product === null)
    {
      error_log ('Unable to delete product with ID ' . $id . ': product not found.');
      return Result::PRODUCT_NOT_FOUND;
    }
    // Verify that the storage unit has no active or booked subscriptions. Finished subscriptions are acceptable.
    if (($product['status'] === Utility::STATUS_NEW) || ($product['status'] === Utility::STATUS_VACATED))
    {
      return Result::OK;
    }
    error_log('Unable to delete product with ID ' . $id . ': product has active booking or reservation.');
    return Result::PRODUCT_ALREADY_BOOKED;
  }

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