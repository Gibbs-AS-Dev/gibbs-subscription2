<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/licencee_data_manager.php';

// This class can be used in its entirety without setting a particular group ID. This class deals with user groups in
// general. In order to process user groups that have a licence for the Gibbs self storage application, use the
// Licencee_Data_Manager.
class User_Group_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    global $wpdb;

    parent::__construct($new_access_token);
    $this->add_action('create_user_group', Utility::ROLE_GIBBS_ADMIN, 'create');
    $this->database_table = $wpdb->prefix . 'users_groups';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all user groups. Return them as a string containing a Javascript array declaration. Use the c.ugr constants.
  public function read()
  {
    global $wpdb;

    $results = $wpdb->get_results("
      SELECT
        ug.id AS user_group_id,
        ug.name AS user_group_name
      FROM {$this->database_table} ug
      ORDER BY user_group_name;
    ", OBJECT);

    $table = "[";
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $user_group)
      {
        $table .= "[";
        $table .= $user_group->user_group_id;
        $table .= ", '";
        $table .= trim(Utility::remove_line_breaks($user_group->user_group_name));
        $table .= "'],";
      }
      $table = Utility::remove_final_comma($table);
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
  // Override to create the user group's dummy user and grant the user group an active Gibbs self storage licence.
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

    // Create a dummy user to represent the new user group. Note that, if the user group is not created successfully,
    // the dummy user will not be deleted.
    $dummy_user_id = $this->create_dummy_user($data_item['name']);
    if ($dummy_user_id < 0)
    {
      error_log('Failed to create dummy user for new user group.');
      return Result::DATABASE_QUERY_FAILED;
    }
    $data_item['group_admin'] = $dummy_user_id;

    // Create the user group.
    $wpdb->query('START TRANSACTION');
    $result = parent::create($data_item);
    if ($result !== Result::OK)
    {
      error_log('Error while creating user group: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Add an active Gibbs self storage licence for the new user group.
    $licencee_data = new Licencee_Data_Manager($this->access_token);
    $result = $licencee_data->create_licence_for_user_group($this->get_created_item_id());
    if ($result !== Result::OK)
    {
      error_log('Error while adding licence for new user group: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // All operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while creating user group: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Create a dummy user for the user group with the given name. Return the ID of the new user, or -1 if no user could
  // be created.
  protected function create_dummy_user($name)
  {
    // Generate user data for the dummy user.
    $dummy_email = str_replace(' ', '_', strtolower($name)) . '@dummyuser.com';
    $data_item = array(
      'user_login' => $dummy_email,
      'user_email' => $dummy_email,
      'user_pass' => Utility::get_random_string(rand(10, 16)),
      'display_name' => $name,
      'first_name' => $name,
      'last_name' => '',
      'locale' => Utility::DEFAULT_LANGUAGE
    );

    // Create a new user. $new_user_id will contain either the user ID of the new user, or a WP_Error object.
    $new_user_id = wp_insert_user($data_item);

    // See if the user was created successfully. If not, return -1.
    if (is_wp_error($new_user_id))
    {
      error_log('Failed to create dummy user for new user group "' . $name . '": ' . $new_user_id->get_error_message());
      return -1;
    }

    // Store the entity type. A dummy user is a person, not a company.
    update_user_meta($new_user_id, 'profile_type', 'personal');
    return $new_user_id;
  }

  // *******************************************************************************************************************
  // Return an array that describes a user group, using the information posted to the server. If any of the fields was
  // not passed from the client, the method will return null.
  protected function get_data_item()
  {
    if (!Utility::string_posted('name'))
    {
      return null;
    }
  
    // "group_admin" is the user group's dummy user. When creating a new user group, that user must be created before
    // the user group is created. The caller must then fill in the "group_admin" field.
    $user_id = get_current_user_id();
    $user_group = array(
      'name' => Utility::read_posted_string('name'),
      'group_admin' => 0,
      'show_in_form' => null,
      'type_of_form' => 1,
      'published_at' => current_time('mysql'),
      'created_by' => $user_id,
      'updated_by' => $user_id
    );

    return $user_group;
  }

  // *******************************************************************************************************************
}
?>