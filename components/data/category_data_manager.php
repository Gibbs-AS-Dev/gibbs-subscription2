<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Category_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_category', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_category', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_category', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = 'subscription_product_category';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all categories owned by the current user from the database. Return them as a string containing a Javascript
  // array declaration.
  //
  // Database table fields: id, name, owner_id, created_at, updated_at
  public function read()
  {
    global $wpdb;

    $results = $wpdb->get_results(
      "SELECT id, name FROM {$this->database_table} WHERE owner_id = {$this->get_user_group_user_id()} ORDER BY name;",
      OBJECT);
    $table = "[";
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $category)
      {
        $table .= "[";
        $table .= $category->id;
        $table .= ", '";
        $table .= $category->name;
        $table .= "'],";
      }
      // Remove final comma.
      $table = substr($table, 0, -1);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return an array that describes a location, using the information posted to the server. The owner_id field will be
  // set to the current user, and updated_at to the current time. The created_at field will not be set. If either of the
  // fields was not passed from the client, the method will return null.
  protected function get_data_item()
  {
    if (!Utility::string_posted('name'))
    {
      return null;
    }

    $category = array(
      'owner_id' => $this->get_user_group_user_id(),
      'name' => Utility::read_posted_string('name'),
      'updated_at' => current_time('mysql')
    );
    if (!Utility::non_empty_strings($category, array('name')))
    {
      return null;
    }

    return $category;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the data item with the given ID can be deleted from the database. If not, return another
  // result code defined in utility.php. Descendants may want to override this method.
/*
  protected function can_delete($id)
  {
    // We could check whether the category is used by any product types. However, the database will enforce this for us.
    // And the client should check before submitting the request, so we don't need to check here.
    return Result::OK;
  }
*/

  // *******************************************************************************************************************
}
?>