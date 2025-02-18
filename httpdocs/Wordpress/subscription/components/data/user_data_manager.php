<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

class User_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct()
  {
    // Doing nothing in particular.
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return information about all the users whose IDs are given in the $user_ids array. The information will be returned
  // as a string containing a Javascript array declaration, with the following fields:
  //   id, name, eMail, phone, address, postcode, area, entityType, companyIdNumber (only for companies)
  // Use the c.rqu column constants to index the table.
  public static function get_users($user_ids)
  {
    global $wpdb;

    // Turn the array of user IDs into a comma-separated string.
    if (!is_array($user_ids))
    {
      return '[]';
    }
    $id_string = implode(',', array_map('intval', $user_ids));

    // Perform SQL query.
    $query = "
      SELECT
        u.ID AS user_id,
        u.display_name AS display_name,
        u.user_email AS user_email,
        um.meta_key AS meta_key,
        um.meta_value AS meta_value
      FROM
        {$wpdb->prefix}users u
      LEFT JOIN
        {$wpdb->prefix}usermeta um ON u.ID = um.user_id
      WHERE
        u.ID IN ({$id_string}) AND
        ((um.meta_key IS NULL) OR (um.meta_key IN ('first_name', 'last_name', 'phone', 'billing_address_1', 'billing_postcode', 'billing_city', 'profile_type', 'company_number')))
      ORDER BY
        display_name;
    ";
    $results = $wpdb->get_results($query, ARRAY_A);

    // Organise the results into a PHP array. The SQL query returns one row for each meta key. Each row includes the
    // user information, which means that a user is duplicated if it has several meta values - which it hopefully has.
    // On the other hand, if a user has no associated meta values, it appears once, but with empty meta fields.
    //
    // Create a PHP array with one row for each user. Each user holds the required meta values as part of the row.
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
          $users[$user_id] = self::get_simple_user_array($result_row);
        }
        // Add first name, last name or phone number, depending on the meta key included in this row.
        self::add_metadata_to_user($result_row, $users[$user_id]);
      }
    }

    // For each user, if both first and last names were found, overwrite the name. Note the reference to the $user
    // object - without it, the changes would not affect the $users table. Note also that the name of the variable must
    // be different from the name in the foreach loop below. Apparently these are not separate variables.
    foreach ($users as &$user2)
    {
      User_Data_Manager::replace_display_name_with_full_name($user2);
    }

    // Compose Javascript array.
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
        $table .= "', '";
        $table .= $user['address'];
        $table .= "', '";
        $table .= $user['postcode'];
        $table .= "', '";
        $table .= $user['area'];
        $table .= "', ";
        $table .= strval($user['entity_type']);
        if ($user['entity_type'] === Utility::ENTITY_TYPE_COMPANY)
        {
          $table .= ", '";
          $table .= strval($user['company_id_number']);
          $table .= "'";
        }
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Return information about the user with the given ID. $user_id is optional. If not present, the method will return
  // information about the currently logged-in user. The resulting array will have the following fields:
  //   user_id : integer
  //   name : string
  //   email : string
  //   phone : string
  //   first_name : string
  //   last_name : string
  //   address : string
  //   postcode : string
  //   area : string
  //   company_id_number : string
  //   entity_type : integer
  public static function get_user_data($user_id = null)
  {
    global $wpdb;
    if (empty($user_id))
    {
      $user_id = get_current_user_id();
    }
    else
    {
      $user_id = intval($user_id);
    }

    // Perform SQL query.
    $query = "
      SELECT
        u.ID AS user_id,
        u.display_name AS display_name,
        u.user_email AS user_email,
        um.meta_key AS meta_key,
        um.meta_value AS meta_value
      FROM {$wpdb->prefix}users u
      LEFT JOIN {$wpdb->prefix}usermeta um ON u.ID = um.user_id
      WHERE
        u.ID = {$user_id} AND
        ((um.meta_key IS NULL) OR (um.meta_key IN ('first_name', 'last_name', 'phone', 'billing_address_1', 'billing_postcode', 'billing_city', 'profile_type', 'company_number')))
      ORDER BY
        display_name;
    ";
    $results = $wpdb->get_results($query, ARRAY_A);
    if (!Utility::non_empty_array($results))
    {
      return null;
    }
    $user = self::get_simple_user_array($results[0]);
    foreach ($results as $result_row)
    {
      self::add_metadata_to_user($result_row, $user);
    }
    self::replace_display_name_with_full_name($user, false);
    return $user;
  }

  // *******************************************************************************************************************
  // Return a string containing a JSON object declaration that holds a new, empty user. The entity type will say it's an
  // individual. All other fields will be blank.
  public static function get_empty_user()
  {
    return '{"id": -1, "name": "", "firstName": "", "lastName": "", "eMail": "", "phone": "", "address": "", "postcode": "", "area": "", "entityType": ' .
      Utility::ENTITY_TYPE_INDIVIDUAL . '}';
  }

  // *******************************************************************************************************************
  // Return information about the user with the given ID. $user_id is optional. If not present, the method will return
  // information about the currently logged-in user. The information will be returned as a string containing a JSON
  // object declaration. For an individual, the object will have the following fields:
  //   id, name, firstName, lastName, eMail, phone, address, postcode, area, entityType
  // For a company, the object will have the following fields:
  //   id, name, companyIdNumber, eMail, phone, address, postcode, area, entityType
  public static function get_user($user_id = null)
  {
    $user = self::get_user_data($user_id);
    if ($user === null)
    {
      return self::get_empty_user();
    }

    $object = '{"id": ';
    $object .= $user['user_id'];
    $object .= ', "name": "';
    $object .= $user['name'];
    $object .= '", ';
    if ($user['entity_type'] === Utility::ENTITY_TYPE_COMPANY)
    {
      $object .= '"companyIdNumber": "';
      $object .= $user['company_id_number'];
      $object .= '", ';
    }
    else
    {
      $object .= '"firstName": "';
      $object .= $user['first_name'];
      $object .= '", "lastName": "';
      $object .= $user['last_name'];
      $object .= '", ';
    }
    $object .= '"eMail": "';
    $object .= $user['email'];
    $object .= '", "phone": "';
    $object .= $user['phone'];
    $object .= '", "address": "';
    $object .= $user['address'];
    $object .= '", "postcode": "';
    $object .= $user['postcode'];
    $object .= '", "area": "';
    $object .= $user['area'];
    $object .= '", "entityType": ';
    $object .= strval($user['entity_type']);
    $object .= '}';
    return $object;
  }

  // *******************************************************************************************************************
  // Return the role with the given ID, or null if it was not found. If found, the role will have the following fields:
  //   user_id : integer
  //   role_id : integer          The database ID of the role. This can be used to switch roles.
  //   role_number : integer      Use the ROLE_NUMBER_ constants.
  //   user_group_id : integer
  //   user_group_name : string
  //   licence_status : integer   0: inactive, 1: active
  public static function get_role($id)
  {
    global $wpdb;

    // Perform SQL query.
    $sql = $wpdb->prepare("
        SELECT
          u.ID AS user_id,
          uug.id AS role_id,
          uug.role AS role_number,
          ug.id AS user_group_id,
          ug.name AS user_group_name,
          ugl.licence_is_active AS licence_status
        FROM {$wpdb->prefix}users_and_users_groups uug
        JOIN {$wpdb->prefix}users u ON uug.users_id = u.ID
        JOIN {$wpdb->prefix}users_groups ug ON uug.users_groups_id = ug.id
        JOIN {$wpdb->prefix}users_and_users_groups_licence ugl ON ug.id = ugl.users_groups_id
        JOIN {$wpdb->prefix}users_groups_licence l ON ugl.licence_id = l.id
        WHERE uug.id = %d AND l.licence_name = 'Minilager';
      ",
      $id
    );
    $results = $wpdb->get_results($sql, ARRAY_A);
    if (Utility::non_empty_array($results))
    {
      // Ensure data types are correct.
      $role_data = $results[0];
      self::verify_role($role_data);
      return $role_data;
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return an array that contains the roles that the user with the given $user_id can have. $user_id is optional. If
  // not present, the currently logged-in user will be used. Roles for a particular company are included even if that
  // company's licence has expired. However, when the user tries to access that company's, pages, he will receive
  // appropriate information.
  //
  // Each role entry has the following fields:
  //   user_id : integer
  //   role_id : integer          The database ID of the role. This can be used to switch roles.
  //   role_number : integer      Use the ROLE_NUMBER_ constants.
  //   user_group_id : integer
  //   user_group_name : string
  //   licence_status : integer   0: inactive, 1: active
  public static function get_user_roles($user_id = null)
  {
    global $wpdb;
    if ($user_id === null)
    {
      $user_id = get_current_user_id();
    }

    // Perform SQL query.
    $query = "
      SELECT
        uug.users_id AS user_id,
        uug.id AS role_id,
        uug.role AS role_number,
        ug.id AS user_group_id,
        ug.name AS user_group_name,
        ugl.licence_is_active AS licence_status
      FROM {$wpdb->prefix}users_and_users_groups uug
      JOIN {$wpdb->prefix}users_groups ug ON uug.users_groups_id = ug.id
      JOIN {$wpdb->prefix}users_and_users_groups_licence ugl ON ug.id = ugl.users_groups_id
      JOIN {$wpdb->prefix}users_groups_licence l ON ugl.licence_id = l.id
      WHERE uug.users_id = {$user_id} AND l.licence_name = 'Minilager';
    ";
    $results = $wpdb->get_results($query, ARRAY_A);
    if (is_array($results))
    {
      // Ensure data types are correct.
      foreach($results as &$role_data)
      {
        self::verify_role($role_data);
      }
    }
    else
    {
      $results = array();
    }
    // If the user is a Gibbs administrator, add the administrator role.
    $admin_role = self::get_gibbs_admin_role();
    if (isset($admin_role))
    {
      $results[] = $admin_role;
    }
    return $results;
  }

  // *******************************************************************************************************************
  // If the current user is a Gibbs admin, return a role that gives him the given role (use the ROLE_ constants) for the
  // user group with the given ID, or Gibbs administrator rights in general, if the user group ID was not passed.
  public static function get_gibbs_admin_role($user_group_id = -1, $role = Utility::ROLE_GIBBS_ADMIN)
  {
    if (User::is_gibbs_admin())
    {
      if ($role === Utility::ROLE_USER)
      {
        $role_number = Utility::ROLE_NUMBER_USER;
      }
      elseif ($role === Utility::ROLE_COMPANY_ADMIN)
      {
        $role_number = Utility::ROLE_NUMBER_COMPANY_ADMIN;
      }
      elseif ($role === Utility::ROLE_GIBBS_ADMIN)
      {
        $role_number = Utility::ROLE_NUMBER_GIBBS_ADMINISTRATOR;
      }
      else
      {
        return null;
      }

      // We don't have the group name, so use a generic one. The Gibbs administrator always has access, so set the
      // licence status to active.
      return array(
        'user_id' => get_current_user_id(),
        'role_id' => Utility::ROLE_ID_GIBBS_ADMINISTRATOR,
        'role_number' => $role_number,
        'user_group_id' => $user_group_id,
        'user_group_name' => 'Gibbs administrator',
        'licence_status' => 1
      );
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return the URL that allows the user to assume the given role. The role in question is an array, which may have been
  // returned by the get_user_roles method, and should have the same fields as described there.
  public static function get_role_url($role)
  {
    // Ensure the role has both a valid role number and valid user group ID. The role number is stored as a tinyint in
    // the database, which might mean it's returned as a string. Convert to a number, just to be sure.
    $role_number = $role['role_number'];
    if (!isset($role_number) || !is_numeric($role_number))
    {
      return '.';
    }
    $role_number = intval($role_number);
    
    // Compose the appropriate URL.
    if ($role_number === Utility::ROLE_NUMBER_GIBBS_ADMINISTRATOR)
    {
      return '/subscription/html/gibbs_dashboard.php';
    }
    if (($role_number === Utility::ROLE_NUMBER_LOCAL_ADMIN) || ($role_number === Utility::ROLE_NUMBER_COMPANY_ADMIN))
    {
      if (intval($role['licence_status']) !== 1)
      {
        return '/subscription/html/licence_expired.php';
      }
      return '/subscription/html/admin_dashboard.php';
    }
    if ($role_number === 1)
    {
      if (intval($role['licence_status']) !== 1)
      {
        return '/subscription/html/temporarily_unavailable.php';
      }
      return '/subscription/html/user_dashboard.php';
    }
    return '.';
  }

  // *******************************************************************************************************************
  // Return the role of the currently logged-in user in the user group with the given ID, provided that that user group
  // has a licence to use the system. Potential return values are the ROLE_ constants.
  //
  // Note that, if the licence is not valid, the role will be returned, but the $licence_expired flag will be set to
  // true.
  public static function get_role_in_user_group($user_group_id, &$licence_expired)
  {
    global $wpdb;
    $current_user_id = get_current_user_id();

    // Perform SQL query.
    $query = "
      SELECT
        uug.role AS role,
        ugl.licence_is_active AS licence_status
      FROM {$wpdb->prefix}users u
      JOIN {$wpdb->prefix}users_and_users_groups uug ON u.ID = uug.users_id
      JOIN {$wpdb->prefix}users_groups ug ON uug.users_groups_id = ug.id
      JOIN {$wpdb->prefix}users_and_users_groups_licence ugl ON ug.id = ugl.users_groups_id
      JOIN {$wpdb->prefix}users_groups_licence l ON ugl.licence_id = l.id
      WHERE u.ID = {$current_user_id} AND ug.id = {$user_group_id} AND l.licence_name = 'Minilager';
    ";
    $results = $wpdb->get_results($query, OBJECT);

    // Consider the results. A particular user should only ever have one role in a user group, so we need only check
    // the first one.
    if (!Utility::non_empty_array($results) || (!is_numeric($results[0]->licence_status)) ||
      (!is_numeric($results[0]->role)))
    {
      return -1;
    }
    if (count($results) > 1)
    {
      error_log("Warning: user {$current_user_id} has {count($results)} roles in user group {$user_group_id}. This is not permitted.");
    }
    if (intval($results[0]->licence_status) !== 1)
    {
      $licence_expired = true;
    }
    // The role is returned as a string, even though it is stored as a "tinyint" in the database. Convert to a number,
    // and then convert from a role number to a role, before returning.
    return Utility::role_number_to_role(intval($results[0]->role));
  }

  // *******************************************************************************************************************
  // Return an array with the following fields:
  //   user_id, name, email, phone, first_name, last_name, address, postcode, area, company_id_number, entity_type
  // The first three fields will be filled in from the given source, which is assumed to have fields called:
  //   user_id, display_name, user_email
  public static function get_simple_user_array($source)
  {
    return array(
      'user_id' => $source['user_id'],
      'name' => $source['display_name'],
      'email' => $source['user_email'],
      'phone' => '',
      'first_name' => '',
      'last_name' => '',
      'address' => '',
      'postcode' => '',
      'area' => '',
      'company_id_number' => '',
      'entity_type' => Utility::ENTITY_TYPE_INDIVIDUAL
    );
  }

  // *******************************************************************************************************************
  // Check the given source array for the presence of a meta_key field. If a key with the value "first_name",
  // "last_name", "phone", 'billing_address_1', 'billing_postcode' or 'billing_city' exists, add the corresponding
  // meta_value to the appropriate field in the given user array. Note that, without passing the reference to $user, the
  // original object would not be affected.
  public static function add_metadata_to_user($source, &$user)
  {
    if ($source['meta_key'] === 'first_name')
    {
      $user['first_name'] = $source['meta_value'];
    }
    elseif ($source['meta_key'] === 'last_name')
    {
      $user['last_name'] = $source['meta_value'];
    }
    elseif ($source['meta_key'] === 'phone')
    {
      $user['phone'] = $source['meta_value'];
    }
    elseif ($source['meta_key'] === 'billing_address_1')
    {
      $user['address'] = $source['meta_value'];
    }
    elseif ($source['meta_key'] === 'billing_postcode')
    {
      $user['postcode'] = $source['meta_value'];
    }
    elseif ($source['meta_key'] === 'billing_city')
    {
      $user['area'] = $source['meta_value'];
    }
    elseif ($source['meta_key'] === 'company_number')
    {
      $user['company_id_number'] = $source['meta_value'];
    }
    elseif ($source['meta_key'] === 'profile_type')
    {
      if ($source['meta_value'] === 'company')
      {
        $user['entity_type'] = Utility::ENTITY_TYPE_COMPANY;
      }
      else
      {
        $user['entity_type'] = Utility::ENTITY_TYPE_INDIVIDUAL;
      }
    }
  }

  // *******************************************************************************************************************
  // A user has a display name in ptn_users. However, he also has a first name and last name in ptn_usermeta. The latter
  // is supposed to be used, if present. In the given array of user information, check whether first_name and last_name
  // exist. If they do, replace the current name entry, and delete first_name and last_name. Note that, without passing
  // the reference to $user, the original object would not be affected.
  public static function replace_display_name_with_full_name(&$user, $delete_first_and_last_name = true)
  {
    if (!empty($user['first_name']) && !empty($user['last_name']))
    {
      $user['name'] = $user['first_name'] . ' ' . $user['last_name'];
    }
    if ($delete_first_and_last_name)
    {
      unset($user['first_name']);
      unset($user['last_name']);
    }
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Ensure the given $role_data holds the correct data types. The $role_data will end up having the following fields:
  //   user_id : integer
  //   role_id : integer          The database ID of the role. This can be used to switch roles.
  //   role_number : integer      Use the ROLE_NUMBER_ constants.
  //   user_group_id : integer
  //   user_group_name : string
  //   licence_status : integer   0: inactive, 1: active
  protected static function verify_role(&$role_data)
  {
    $role_data['user_id'] = intval($role_data['user_id']);
    $role_data['role_id'] = intval($role_data['role_id']);
    $role_data['role_number'] = intval($role_data['role_number']);
    $role_data['user_group_id'] = intval($role_data['user_group_id']);
    $role_data['licence_status'] = intval($role_data['licence_status']);
  }

  // *******************************************************************************************************************
}
?>