<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Product_Type_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_product_type', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_product_type', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_product_type', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = 'subscription_product_type';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return the product type with the given $id as an object with the following fields:
  //   id
  //   name
  //   product_type_notes
  //   size
  //   price
  //   category_id
  // If an error occurred, the method will return null.
  public function get_product_type($id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT
          id,
          name,
          notes AS product_type_notes,
          size,
          price,
          category_id
        FROM
          {$this->database_table}
        WHERE
          owner_id = {$this->get_user_group_user_id()} AND
          id = %d;
      ",
      $id
    );
    $results = $wpdb->get_results($sql, OBJECT);
    if (!Utility::array_with_one($results))
    {
      return null;
    }
    $product_type = $results[0];
    self::validate_product_type($product_type);
    return $product_type;
  }

  // *******************************************************************************************************************
  // Return an array of product type objects, each of which has the following fields:
  //   id
  //   name
  //   product_type_notes
  //   size
  //   price
  //   category_id
  // If an error occurred, the method will return an empty array.
  public function get_product_types()
  {
    global $wpdb;

    $results = $wpdb->get_results("
        SELECT
          id,
          name,
          notes AS product_type_notes,
          size,
          price,
          category_id
        FROM
          {$this->database_table}
        WHERE
          owner_id = {$this->get_user_group_user_id()}
        ORDER BY
          name;
      ",
      OBJECT
    );
    if (!is_array($results))
    {
      return array();
    }
    foreach ($results as &$product_type)
    {
      self::validate_product_type($product_type);
    }
    return $results;
  }

  // *******************************************************************************************************************
  // Read all product types owned by the current user from the database. Return them as a string containing a Javascript
  // array declaration. Use the c.typ column constants.
  public function read()
  {
    $results = $this->get_product_types();
    $table = "[";
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $product_type)
      {
        $table .= "[";
        $table .= $product_type->id;
        $table .= ", '";
        $table .= $product_type->name;
        $table .= "', '";
        $table .= (isset($product_type->product_type_notes) ? $product_type->product_type_notes : '');
        $table .= "', ";
        $table .= $product_type->size;
        $table .= ", ";
        $table .= $product_type->price;
        $table .= ", ";
        $table .= $product_type->category_id;
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Return the price of the product type with the given $id. The value will be an integer. Return -1 if the product
  // type was not found.
    // *** // Currently unused.
/*
  public static function get_price($id)
  {
    global $wpdb;

    if (!is_numeric($id))
    {
      error_log('Failed to get price for product type with ID ' . strval($id));
      return -1;
    }
    $sql = $wpdb->prepare("SELECT price FROM subscription_product_type WHERE id = %d;", $id);
    $results = $wpdb->get_results($sql, ARRAY_A);
    if (!Utility::array_with_one($results) || !is_array($results[0]) || !is_numeric($results[0]['price']))
    {
      error_log("Failed to get price for product type {$id}. Result: " . print_r($results, true));
      return -1;
    }
    return intval($results[0]['price']);
  }
*/

  // *******************************************************************************************************************
  // Return the price stored in the product type of the product with the given $id. The value will be an integer.
  // Return -1 if the product was not found.
    // *** // Currently unused.
/*
  public static function get_price_for_product($id)
  {
    global $wpdb;

    if (!is_numeric($id))
    {
      error_log('Failed to get price for product with ID ' . strval($id));
      return -1;
    }
    $id = intval($id);
    $results = $wpdb->get_results("
      SELECT
        pt.price
      FROM
        {$wpdb->prefix}posts p
      JOIN
        subscription_product_type pt ON pt.id = p.product_type_id
      WHERE
        p.ID = {$id};
    ", ARRAY_A);
    if (!Utility::array_with_one($results) || !is_array($results[0]) || !is_numeric($results[0]['price']))
    {
      error_log("Failed to get price from product type for product {$id}. Result: " . print_r($results, true));
      return -1;
    }
    return intval($results[0]['price']);
  }
*/
  // *******************************************************************************************************************
  // If the given $result_row has a field called "product_type_id", add the value of that field to the array in the
  // given $item stored under the key "for_product_types". If necessary, create the array. The product type ID is stored
  // with itself as the key, so it can be found later on. If the $result_row has no product type ID, nothing will
  // happen.
  public static function add_product_type_to_item(&$item, $result_row)
  {
    // Add product type, if present.
    if (isset($result_row['product_type_id']))
    {
      // Add a table for product types, if not already present.
      if (!isset($item['for_product_types']))
      {
        $item['for_product_types'] = array();
      }
      // Store the product type. If we already had it, nothing will have changed.
      $product_type_id = intval($result_row['product_type_id']);
      $item['for_product_types'][$product_type_id] = $product_type_id;
    }
  }

  // *******************************************************************************************************************
  // Read product type IDs posted to the server. Return false if insufficient information was posted. Return null if all
  // product type IDs were posted. The null value typically signifies that the entity in question applies to all product
  // types. Otherwise, return an array of product type IDs. Note that the array might be empty. The method expects the
  // following fields to be posted:
  //   for_all_product_types : integer            1 for true, 0 for false. Remaining fields are only present if this
  //                                              value is 0.
  //   product_type_count : integer               The total number of product types in existence - not the number of
  //                                              product type IDs that were actually posted.
  //   for_product_type_$1 : integer              $1 represents a number from 0 to (product_type_count - 1). Each item
  //                                              may or may not hold a product type ID. If posted, it signifies that
  //                                              the product type with that ID was selected.
  public static function read_posted_product_types()
  {
    // Read the radio button setting.
    if (!Utility::integer_posted('for_all_product_types'))
    {
      return false;
    }
    $for_all_product_types = Utility::read_posted_integer('for_all_product_types');

    // If the entity applies to all product types, return null to signify this.
    if ($for_all_product_types === 1)
    {
      return null;
    }

    // If the entity applies to only some product types, check all potential product types, and record the ones that
    // were selected.
    if ($for_all_product_types === 0)
    {
      if (!Utility::integer_posted('product_type_count'))
      {
        return false;
      }
      // The product type count is the total number of product types in existence, not the number of posted items.
      $product_type_count = Utility::read_posted_integer('product_type_count');
      $for_product_types = array();
      for ($i = 0; $i < $product_type_count; $i++)
      {
        $parameter = 'for_product_type_' . strval($i);
        if (Utility::integer_posted($parameter))
        {
          $for_product_types[] = Utility::read_posted_integer($parameter);
        }
        // If the product type was not selected, the value will not be posted. This does not indicate an error.
      }
      // If all product types were posted, return null.
      if (count($for_product_types) === $product_type_count)
      {
        return null;
      }
      return $for_product_types;
    }
    return false;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Ensure the values of the given $product_type are valid and have the correct data type.
  protected static function validate_product_type(&$product_type)
  {
    $product_type->id = intval($product_type->id);
    $product_type->price = intval($product_type->price);
    $product_type->category_id = intval($product_type->category_id);
  }

  // *******************************************************************************************************************
  // Return an array that describes a product type, using the information posted to the server. The owner_id field will
  // be set to the current user, and updated_at to the current time. The created_at field will not be set. If either of
  // the fields was not passed from the client, the method will return null.
  protected function get_data_item()
  {
    if (!Utility::string_posted('name') || !Utility::integers_posted(array('price', 'category_id')))
    {
      return null;
    }

    $category = array(
      'owner_id' => $this->get_user_group_user_id(),
      'name' => Utility::read_posted_string('name'),
      'notes' => Utility::read_posted_string('product_type_notes', null),
      'size' => Utility::read_posted_float('size'),
      'price' => Utility::read_posted_integer('price'),
      'category_id' => Utility::read_posted_integer('category_id'),
      'updated_at' => current_time('mysql')
    );
    if (!Utility::non_empty_strings($category, array('name')))
    {
      return null;
    }
    // We do not need to ensure that the category_id refers to an existing category, as the database will not permit
    // the operation if it does not.

    return $category;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the data item with the given ID can be deleted from the database. If not, return another
  // result code defined in utility.php. Descendants may want to override this method.
/*
  protected function can_delete($id)
  {
      // *** // Check whether the product type is used by any products. If it is, return false, so the user will get an
             // error message that makes sense. Alternatively, have the client ask the server before the delete request
             // is submitted.
    return Result::OK;
  }
*/
  // *******************************************************************************************************************
}
?>