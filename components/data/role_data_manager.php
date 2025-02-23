<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_user_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/subscription_data_manager.php';

// The user group ID must be set before the object is used for anything.
//
// The user_id is used for the create, update and delete methods. It will be used if the user ID was not posted as part
// of the request.  
class Role_Data_Manager extends Single_User_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // Role filter. If set, the read method will only return users with this role number. Optional. Use the ROLE_NUMBER_
  // constants.
  protected $role_number = -1;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    global $wpdb;

    parent::__construct($new_access_token);
      // *** // At the moment, the role data manager does not provide any actions. You can still call the methods directly, however.
/*
    $this->add_action('create_role', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_role', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_role', Utility::ROLE_COMPANY_ADMIN, 'delete');
*/
    $this->database_table = $wpdb->prefix . 'users_and_users_groups';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************

  public function get_user_list()
  {
    global $wpdb;

      // *** // In the future, we will likely need to add the role ID to the table.
    $query = "
      SELECT
        u.ID AS user_id,
        u.display_name,
        u.user_email,
        um.meta_key,
        um.meta_value,
        s.id AS subscription_id,
        s.buyer_id AS buyer_id,
        s.start_date AS start_date,
        s.end_date AS end_date
      FROM {$wpdb->prefix}users u
      JOIN {$this->database_table} uug ON u.ID = uug.users_id
      LEFT JOIN 
        {$wpdb->prefix}usermeta um ON u.ID = um.user_id
      LEFT JOIN
        subscriptions s ON u.ID = s.buyer_id AND s.owner_id = {$this->get_user_group_user_id()}
      WHERE
        uug.users_groups_id = {$this->get_user_group_id()} AND
        (um.meta_key = 'first_name' OR um.meta_key = 'last_name' OR um.meta_key = 'phone')
    ";
    // If we are only interested in one role, add that condition to the query.
    if ($this->role_number >= 0)
    {
      $query .= " AND uug.role = {$this->role_number} ORDER BY display_name;";
    }
    else
    {
      $query .= ' ORDER BY display_name;';
    }
    $results = $wpdb->get_results($query, ARRAY_A);

    // Organise the results into a PHP array. The SQL query returns one row for each meta key. Each row includes the
    // user information, which means that a user is duplicated if it has several meta values - which it hopefully has.
    // On the other hand, if a user has no associated meta values, it appears once, but with empty meta fields.
    //
    // In addition, the SQL query returns one row for each subscription.
    //
    // Create a PHP array with one row for each user. Each user holds the required meta values as part of the row, and
    // a flag for the presence of active subscriptions.
    $users = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $result_row)
      {
        $user_id = $result_row['user_id'];
        // If the user has not already been found, create one. Note the extra fields for first and last name. These will
        // be combined to a single name once all the rows have been read.
        if (!isset($users[$user_id]))
        {
          $users[$user_id] = User_Data_Manager::get_simple_user_array($result_row);
          $users[$user_id]['has_active_subscription'] = false;
        }
        // Add first name, last name or phone number, depending on the meta key included in this row.
        User_Data_Manager::add_metadata_to_user($result_row, $users[$user_id]);
        // If an active subscription was found, note the fact. If one was already found, we don't need to check.
        if (!$users[$user_id]['has_active_subscription'] && is_numeric($result_row['subscription_id']))
        {
          $subscription = Subscription_Data_Manager::get_subscription($result_row);
          if (($subscription['status'] === Utility::SUB_ONGOING) || ($subscription['status'] === Utility::SUB_CANCELLED))
          {
            $users[$user_id]['has_active_subscription'] = true;
          }
        }
      }
    }

    // For each user, if both first and last names were found, overwrite the name. Note the reference to the $user
    // object - without it, the changes would not affect the $users table.
    foreach ($users as &$user)
    {
      User_Data_Manager::replace_display_name_with_full_name($user);
    }
    return $users;
  }

  // *******************************************************************************************************************
  // Read information about all users that have roles in the user group specified by the class' $user_group_id property.
  // Return them as a string containing a Javascript array declaration. If the $role_number property is set, the method
  // will only return users with that role.
  public function read()
  {
    $users = self::get_user_list();

    $table = "[";
    if (!empty($users))
    {
      foreach ($users as $user)
      {
        $table .= "[";
        $table .= $user['user_id'];
        $table .= ", '";
        $table .= $user['name'];
        $table .= "', '";
        $table .= $user['email'];
        $table .= "', '";
        $table .= $user['phone'];
        $table .= "', ";
        $table .= var_export($user['has_active_subscription'], true);
        $table .= "],";
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
  // Return an array that describes a role, using the information provided or posted to the server. If any of the fields
  // was not available, the method will return null. The user_group_id and role will always be the value set in the
  // object. The user_id can be posted, but if it wasn't, the method will use the value set in the object, or - if the
  // user_id was not set - the ID of the currently logged-in user. Valid role numbers are:
  //   1: customer
  //   2: normal member (local company admin)
  //   3: admin (global company admin)
  // The role number cannot be posted, or else a user could post an admin role number, and have himself made
  // administrator instead of an ordinary user.
  protected function get_data_item()
  {
    // Read values from the request, or from the object.
    if (Utility::integer_posted('user_id'))
    {
      $user_id = Utility::read_posted_integer('user_id');
    }
    else
    {
      $user_id = $this->get_user_id();
    }
    $role_number = $this->role_number;

    // Validate values.
      // *** // Use constants.
    $user_group_id = $this->get_user_group_id();
    if (($user_group_id < 0) || ($user_id < 0) || ($role_number < 1) || ($role_number > 3))
    {
      return null;
    }

    // Create role.
    return array(
      'users_groups_id' => $user_group_id,
      'users_id' => $user_id,
      'role' => $role_number
    );
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_role_filter()
  {
    return $this->role_number;
  }

  // *******************************************************************************************************************

  public function set_role_filter($new_value)
  {
    if (is_numeric($new_value))
    {
      $new_value = intval($new_value);
      if ($new_value >= -1)
      {
        $this->role_number = $new_value;
      }
    }
  }

  // *******************************************************************************************************************
}
?>