<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';

class Insurance_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_insurance_product', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_insurance_product', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_insurance_product', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = 'subscription_product_optional';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all insurance products owned by the current user group from the database. Return them as a string containing a
  // Javascript array declaration.
  public function read()
  {
    global $wpdb;

    $results = $wpdb->get_results("
      SELECT
        i.id AS id,
        i.title AS name,
        i.description AS description,
        i.price AS price,
        ipt.product_type_id AS product_type_id,
        il.product_location_id AS location_id
      FROM
        {$this->database_table} i
      LEFT JOIN 
        subscription_product_optional_id_and_product_type_id ipt ON i.id = ipt.product_optional_id
      LEFT JOIN 
        subscription_product_optional_id_and_product_location_id il ON i.id = il.product_optional_id
      WHERE
        i.owner_id = {$this->get_user_group_user_id()} AND
        i.type = 1
      ORDER BY name;
    ", ARRAY_A);

    // Organise the results into a PHP array. The SQL query returns one row for each product type associated with the
    // insurance product. Each row includes the insurance product information, which means that an insurance product is
    // duplicated if it is linked to several product types. On the other hand, if the insurance product is for all
    // product types, the insurance product information will appear once, with a NULL in the product_type_id field.
    //
    // Similarly, and in addition, the SQL query returns one row for each location linked to the insurance product.
    //
    // Create a PHP array with one row for each insurance product. Each insurance product holds all of their linked
    // product types and locations in two arrays, each of which may be null if the insurance product has no linked
    // items.
    $insurance_products = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $result_row)
      {
        $insurance_product_id = $result_row['id'];

        // If the insurance product has not already been found, create it.
        if (!isset($insurance_products[$insurance_product_id]))
        {
          $insurance_products[$insurance_product_id] = self::get_insurance_product($result_row);
        }

        // Add product type and location, if present.
        Product_Type_Data_Manager::add_product_type_to_item($insurance_products[$insurance_product_id], $result_row);
        Location_Data_Manager::add_location_to_item($insurance_products[$insurance_product_id], $result_row);
      }
    }

    // Create the Javascript table.
    $table = "[";
    if (!empty($insurance_products))
    {
      foreach ($insurance_products as $insurance_product)
      {
        $table .= "[";
        $table .= $insurance_product['id'];
        $table .= ", '";
        $table .= $insurance_product['name'];
        $table .= "', '";
        $table .= $insurance_product['description'];
        $table .= "', ";
        $table .= $insurance_product['price'];
        $table .= ", ";
        $table .= Utility::get_js_array_of_values($insurance_product['for_product_types']);
        $table .= ", ";
        $table .= Utility::get_js_array_of_values($insurance_product['for_locations']);
        $table .= "],";
      }
      // Remove final comma.
      $table = substr($table, 0, -1);
    }
    $table .= "]";
    return $table;
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
  // Override to set up connections to product types and locations. These are in separate tables.
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
    // Keep the tables for afterwards. Remove them from the insurance product that goes to the database. They have to be
    // passed as part of the data item: we need them, but cannot change the signature of the method we are overriding.
    $for_product_types = $data_item['for_product_types'];
    unset($data_item['for_product_types']);
    $for_locations = $data_item['for_locations'];
    unset($data_item['for_locations']);

    // Create the new insurance product in the database.
    $wpdb->query('START TRANSACTION');
    $result = parent::create($data_item);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Create links to product types and locations.
    if (!self::link_insurance_to_product_types($this->created_item_id, $for_product_types) ||
      !self::link_insurance_to_locations($this->created_item_id, $for_locations))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // All operations succeeded.
    $wpdb->query('COMMIT');
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
  // Override to update the connections to product types and locations. These are in separate tables.
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
    if (!isset($id))
    {
      if (!Utility::integer_posted($this->id_posted_name))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      $id = Utility::read_posted_integer($this->id_posted_name);
    }

    // Keep the tables for afterwards. Remove them from the insurance product that goes to the database. They have to be
    // passed as part of the data item: we need them, but cannot change the signature of the method we are overriding.
    $for_product_types = $data_item['for_product_types'];
    unset($data_item['for_product_types']);
    $for_locations = $data_item['for_locations'];
    unset($data_item['for_locations']);

    // Update the insurance product in the database.
    $wpdb->query('START TRANSACTION');
    $result = parent::update($id, $data_item);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Delete existing links to product types and locations, and create new ones depending on the data passed from the
    // client.
    if (!self::clear_insurance_product_type_links($id) ||
      !self::link_insurance_to_product_types($id, $for_product_types) ||
      !self::clear_insurance_location_links($id) ||
      !self::link_insurance_to_locations($id, $for_locations))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // Somehow, all operations succeeded.
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
  // Override to first delete the connections to product types and locations. These are in separate tables, and the
  // entries must be removed before the insurance product can be deleted.
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

    // Ensure the data item with that ID can be deleted.
    $result = $this->can_delete($id);
    if ($result !== Result::OK)
    {
      return $result;
    }

    // Delete product type and location links.
    $wpdb->query('START TRANSACTION');
    if (!self::clear_insurance_product_type_links($id) || !self::clear_insurance_location_links($id))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // Delete the data item itself.
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
  // Return the price of the insurance product with the given $id. The value will be an integer. Return -1 if the
  // insurance product was not found.
  public static function get_price($id)
  {
    global $wpdb;

    if (!is_numeric($id))
    {
      error_log('Failed to get price for insurance with ID ' . strval($id));
      return -1;
    }

    $results = $wpdb->get_results("SELECT price FROM subscription_product_optional WHERE id = {$id};", ARRAY_A);
    if (!Utility::array_with_one($results) || !is_array($results[0]) || !is_numeric($results[0]['price']))
    {
      error_log("Failed to get price for insurance {$id}. Result: {print_r($results, true)}.");
      return -1;
    }
    return intval($results[0]['price']);
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Remove all links in the database between the insurance product with the given $id and any product types.
  protected static function clear_insurance_product_type_links($id)
  {
    global $wpdb;

    $result = $wpdb->query(
      "DELETE FROM subscription_product_optional_id_and_product_type_id WHERE product_optional_id = {$id};");
    if ($result === false)
    {
      error_log("Error while deleting product type links for insurance product {$id}: {$wpdb->last_error}.");
      return false;
    }
    return true;
  }

  // *******************************************************************************************************************
  // Remove all links in the database between the insurance product with the given $id and any locations.
  protected static function clear_insurance_location_links($id)
  {
    global $wpdb;

    $result = $wpdb->query(
      "DELETE FROM subscription_product_optional_id_and_product_location_id WHERE product_optional_id = {$id};");
    if ($result === false)
    {
      error_log("Error while deleting location links for insurance product {$id}: {$wpdb->last_error}.");
      return false;
    }
    return true;
  }

  // *******************************************************************************************************************
  // Create an insurance product's links to product types, if required. If the given $for_product_types table is null,
  // nothing needs to be done. If not, insert links between the insurance product with the ID given in $id and the
  // product types for which it will be offered. For each product type ID in $for_product_types, insert a row in the
  // "subscription_product_optional_id_and_product_type_id" table with that product type ID, and the ID of the insurance
  // product. Return true if everything that needed to be done was done successfully. Return false if something went
  // wrong.
  protected static function link_insurance_to_product_types($id, $for_product_types)
  {
    global $wpdb;

    if (Utility::non_empty_array($for_product_types))
    {
      $values = Utility::get_value_data_string($id, $for_product_types);
      $result = $wpdb->query("
        INSERT INTO
          subscription_product_optional_id_and_product_type_id (product_optional_id, product_type_id)
        VALUES
          {$values};
      ");
      if ($result === false)
      {
        error_log("Error while creating product type links for insurance product {$id}: {$wpdb->last_error}. Tried to insert product type IDs: {$values}.");
        return false;
      }
      if ($result !== count($for_product_types))
      {
        error_log("Failed to insert the correct number of product type links for insurance product {$id}. Expected: {count($for_product_types)}. Actual: {$result}. Tried to insert product type IDs: {$values}.");
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Create an insurance product's links to locations, if required. If the given $for_locations table is null, nothing
  // needs to be done. If not, insert links between the insurance product with the ID given in $id and the locations at
  // which it will be offered. For each location ID in $for_locations, insert a row in the
  // "subscription_product_optional_id_and_product_location_id" (Sorry! We couldn't think of a longer name) table with
  // that location ID, and the ID of the insurance product. Return true if everything that needed to be done was done
  // successfully. Return false if something went wrong.
  protected static function link_insurance_to_locations($id, $for_locations)
  {
    global $wpdb;

    if (Utility::non_empty_array($for_locations))
    {
      $values = Utility::get_value_data_string($id, $for_locations);
      $result = $wpdb->query("
        INSERT INTO
          subscription_product_optional_id_and_product_location_id (product_optional_id, product_location_id)
        VALUES
          {$values};
      ");
      if ($result === false)
      {
        error_log("Error while creating location links for insurance product {$id}: {$wpdb->last_error}. Tried to insert location IDs: {$values}");
        return false;
      }
      if ($result !== count($for_locations))
      {
        error_log("Failed to insert the correct number of location links for insurance product {$id}. Expected: {count($for_locations)}. Actual: {$result}. Tried to insert location IDs: {$values}");
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************

  protected static function get_insurance_product($result_row)
  {
    return array(
      'id' => intval($result_row['id']),
      'name' => $result_row['name'],
      'description' => $result_row['description'],
      'price' => intval($result_row['price']),
      'for_product_types' => null,
      'for_locations' => null
    );
  }

  // *******************************************************************************************************************
  // Return an array that describes an insurance product, using the information posted to the server. If any of the
  // fields was not passed from the client, the method will return null. Note that the for_product_types and
  // for_locations tables must be removed from the array before it is used to update the database. These tables must be
  // inserted or updated separately in the database.
  protected function get_data_item()
  {
    // Ensure everything was posted.
    if (!Utility::strings_posted(array('name', 'description')) || !Utility::integer_posted('price'))
    {
      return null;
    }

    // Read product types.
    $for_product_types = Product_Type_Data_Manager::read_posted_product_types();
    if ($for_product_types === false)
    {
      return null;
    }

    // Read locations.
    $for_locations = Location_Data_Manager::read_posted_locations();
    if ($for_locations === false)
    {
      return null;
    }

    // Create data item.
    return array(
      // id will be set automatically.
      'owner_id' => $this->get_user_group_user_id(),
      'title' => Utility::read_posted_string('name'),
      'description' => Utility::read_posted_string('description'),
      'price' => Utility::read_posted_integer('price'),
      'type' => 1,
      'for_product_types' => $for_product_types,
      'for_locations' => $for_locations
    );
  }

  // *******************************************************************************************************************
}
?>