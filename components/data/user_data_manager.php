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
  // Return information about the user with the given ID. $user_id is optional. If not present, the method will return
  // information about the currently logged-in user. The information will be returned as a string containing a
  // Javascript object declaration, with the following fields:
  //   id, name, firstName, lastName, eMail, phone
  public static function get_user($user_id = null)
  {
    global $wpdb;
    if (empty($user_id))
    {
      $user_id = get_current_user_id();
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
        (um.meta_key IS NULL OR um.meta_key = 'first_name' OR um.meta_key = 'last_name' OR um.meta_key = 'phone')
      ORDER BY
        display_name;
    ";
    $results = $wpdb->get_results($query, ARRAY_A);
    if (!Utility::non_empty_array($results))
    {
      return "{id: -1, name: '', eMail: '', phone: ''}";
    }
    $user = self::get_simple_user_array($results[0]);
    foreach ($results as $result_row)
    {
      self::add_metadata_to_user($result_row, $user);
    }
    self::replace_display_name_with_full_name($user, false);

    $object = "{id: ";
    $object .= $user['user_id'];
    $object .= ", name: '";
    $object .= $user['name'];
    $object .= "', firstName: '";
    $object .= $user['first_name'];
    $object .= "', lastName: '";
    $object .= $user['last_name'];
    $object .= "', eMail: '";
    $object .= $user['email'];
    $object .= "', phone: '";
    $object .= $user['phone'];
    $object .= "'}";
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
    $query = "
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
      WHERE uug.id = {$id} AND l.licence_name = 'Minilager';
    ";
    $results = $wpdb->get_results($query, ARRAY_A);
    if (Utility::non_empty_array($results))
    {
      return $results[0];
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return an array that contains the roles that the currently logged-in user can have. Roles for a particular company
  // are included even if that company's licence has expired. However, when the user tries to access that company's,
  // pages, he will receive appropriate information.
  //
  // Each role entry has the following fields:
  //   user_id : integer
  //   role_id : integer          The database ID of the role. This can be used to switch roles.
  //   role_number : integer      Use the ROLE_NUMBER_ constants.
  //   user_group_id : integer
  //   user_group_name : string
  //   licence_status : integer   0: inactive, 1: active
  public static function get_user_roles()
  {
    global $wpdb;
    $current_user_id = get_current_user_id();

    // Perform SQL query.
    $query = "
      SELECT
        u.ID AS user_id,
        uug.id AS role_id,
        uug.role AS role_number,
        ug.id AS user_group_id,
        ug.name AS user_group_name,
        ugl.licence_is_active AS licence_status
      FROM {$wpdb->prefix}users u
      JOIN {$wpdb->prefix}users_and_users_groups uug ON u.ID = uug.users_id
      JOIN {$wpdb->prefix}users_groups ug ON uug.users_groups_id = ug.id
      JOIN {$wpdb->prefix}users_and_users_groups_licence ugl ON ug.id = ugl.users_groups_id
      JOIN {$wpdb->prefix}users_groups_licence l ON ugl.licence_id = l.id
      WHERE u.ID = {$current_user_id} AND l.licence_name = 'Minilager';
    ";
    $results = $wpdb->get_results($query, ARRAY_A);
    if (!is_array($results))
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
  // has a licence to use the system. Potential return values are:
  //   -1: The user does not have a role in the user group, or the user group does not have a licence to use the
  //       product.
  //    1: customer
  //    2: normal member (local company admin)
  //    3: admin (global company admin)
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
    // The role is returned as a string, even though it is stored as a "tinyint" in the database. Convert to a number
    // before returning.
    return intval($results[0]->role);
  }

  // *******************************************************************************************************************
  // Read all user groups. Return them as a string containing a Javascript array declaration.
  public static function get_user_groups()
  {
    global $wpdb;

    $results = $wpdb->get_results("
      SELECT
        ug.id AS user_group_id,
        ug.name AS user_group_name
      FROM {$wpdb->prefix}users_groups ug
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
      // Remove final comma.
      $table = substr($table, 0, -1);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Return an array with the following fields:
  //   user_id, name, email, phone, first_name, last_name
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
    );
  }

  // *******************************************************************************************************************
  // Check the given source array for the presence of a meta_key field. If a key with the value "first_name",
  // "last_name" or "phone" exists, add the corresponding meta_value to the appropriate field in the given user array.
  // Note that, without passing the reference to $user, the original object would not be affected.
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
}
?>