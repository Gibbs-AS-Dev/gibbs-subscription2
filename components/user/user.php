<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/role_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';

class User
{
  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // If the user is not logged in, or there is no user group ID on the session, send HTTP 401 and redirect to the login
  // page. Otherwise, take no action. However, Gibbs admins may continue even if there is no user group.
  public static function verify_logged_in()
  {
    if (!is_user_logged_in() || ((self::get_user_group_id() < 0) && !self::is_gibbs_admin()))
    {
      self::send_access_denied();
    }
  }

  // *******************************************************************************************************************
  // Switch to the role with the given ID. If the role was found, and it belongs to the current user, store the user
  // group ID for the new role on the session, redirect to appropriate initial page with HTTP status code 302, and stop
  // executing the current script. The role will not be found unless it is a Minilager role, so we don't have to check
  // for that here. If the role is not found, or does not belong to the currently logged-on user, no action will be
  // taken.
  public static function switch_to_role_id($role_id)
  {
    if (is_int($role_id) && ($role_id >= 0))
    {
      self::switch_to_role(User_Data_Manager::get_role($role_id));
    }
  }

  // *******************************************************************************************************************
  // Switch to the given role. Store the user group ID for the new role on the session, redirect to appropriate initial
  // page with HTTP status code 302, and stop executing the current script. If the role is not valid, or does not belong
  // to the currently logged-on user, no action will be taken.
  public static function switch_to_role($role_data)
  {
    if (isset($role_data) && ($role_data['user_id'] === get_current_user_id()))
    {
      self::set_user_group_id($role_data['user_group_id']);
      Utility::redirect_to(User_Data_Manager::get_role_url($role_data));
    }
  }

  // *******************************************************************************************************************
  // If the user is already logged in, redirect to the appropriate initial page with HTTP status code 302, and stop
  // executing the current script. If he is logged in and is a Gibbs administrator, redirect to the Gibbs abonnement
  // administration dashboard. If he is logged in, is not a Gibbs administrator, and does not have any roles, redirect
  // him to the dashboard on the main site. If none of the above is the case, return without performing any action.
  //
  // If the user is logged in, and has a valid role, the user group ID will be stored on the session for later use.
  public static function check_login_and_redirect()
  {
    if (is_user_logged_in())
    {
        // *** // Should we check here whether the group ID is already stored on the session? If not, write why not.
      $roles = User_Data_Manager::get_user_roles();
      $index = self::get_primary_role_index($roles);
      if ($index < 0)
      {
        self::set_user_group_id(-1);
        Utility::redirect_to('/dashbord/');
      }
      else
      {
        self::set_user_group_id(intval($roles[$index]['user_group_id']));
        Utility::redirect_to(User_Data_Manager::get_role_url($roles[$index]));
      }
    }
  }

  // *******************************************************************************************************************
  // Attempt to log in, using credentials from the fields with the given names. The request type must be 'POST'. If
  // successful, redirect to the appropriate initial page and exit, in which case the function return value is moot. If
  // not, the function returns a result code, so the caller can let the user know what happened. The method can be
  // called even if the user has not posted credentials, in which case the method returns Result::NO_ACTION_TAKEN.
  // Possible return values:
  //   NO_ACTION_TAKEN          The user did not post any of the fields required in order to log in.
  //   MISSING_INPUT_FIELD    The user submitted some fields, but not all.
  //   INVALID_PASSWORD       The password contained invalid characters.
  //   WORDPRESS_ERROR        Login failed. This is probably due to the user entering the wrong password.
  public static function log_in($user_name_field, $password_field)
  {
    // If the user is already logged in, redirect to the initial page with HTTP status code 302.
    self::check_login_and_redirect();

    // See if the user posted an e-mail and password.
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
      $data = Utility::read_posted_strings(array($user_name_field, $password_field));

      // If neither field was posted, we'll just display the login page normally. Otherwise, verify that both fields
      // were posted, and display an error message if they were not.
      $state = Utility::verify_fields($data);
      if ($state === Utility::SOME_PRESENT)
      {
        return Result::MISSING_INPUT_FIELD;
      }
      if ($state === Utility::ALL_PRESENT)
      {
        // The user posted both a user name and a password. Verify that the password was not modified due to illegal
        // characters.
        if ($data[$password_field] !== $_POST[$password_field])
        {
          return Result::INVALID_PASSWORD;
        }

        // Attempt to log in. If successful, the user will be redirected to the initial page, and script execution will
        // be halted. If not, return an error code.
        return self::log_in_with($data[$user_name_field], $data[$password_field]);
      }
    }
    return Result::NO_ACTION_TAKEN;
  }

  // *******************************************************************************************************************

  protected static function get_posted_user_data()
  {
/*
    // See if the user posted registration data.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    {
      return null;
    }
    $data = Utility::read_posted_strings(
      array($user_name_field, $password_field, $first_name_field, $last_name_field, $phone_no_field));

    // If none of the fields were posted, we'll just display the registration page normally. Otherwise, verify that
    // all fields were posted, and display an error message if they were not.
    $state = Utility::verify_fields($data);
    if ($state === Utility::SOME_PRESENT)
    {
      return Result::MISSING_INPUT_FIELD;
    }
    if ($state === Utility::ALL_PRESENT)
    {
      // All fields were posted. Verify that the password was not modified due to illegal characters.
      if ($data[$password_field] !== $_POST[$password_field])
      {
        return Result::INVALID_PASSWORD;
      }
      // Verify that the password was long enough.
      if (strlen($data[$password_field]) < Utility::PASSWORD_MIN_LENGTH)
      {
        return Result::PASSWORD_TOO_SHORT;
      }
      // Verify that the provided e-mail is valid.
      if (!Utility::is_valid_email($data[$user_name_field]))
      {
        return Result::INVALID_EMAIL;
      }
      // See whether the username and e-mail are available.
      if (username_exists($data[$user_name_field]) || email_exists($data[$user_name_field]))
      {
        return Result::EMAIL_EXISTS;
      }

      // The user posted all required fields. Create a new user. $new_user will contain either the user ID of the new
      // user, or a WP_Error object.
      return array(
        'user_login' => $data[$user_name_field],
        'user_email' => $data[$user_name_field],
        'user_pass' => $data[$password_field],
        'display_name' => $data[$first_name_field] . ' ' . $data[$last_name_field],
        'first_name' => $data[$first_name_field],
        'last_name' => $data[$last_name_field],
        'phone' => $data[$phone_no_field], // Wordpress will store this, even though it is not in the documentation.
        'locale' => Utility::DEFAULT_LANGUAGE
      );
    }
*/
  }

  // *******************************************************************************************************************
  // Register a new user. The user's connection to and role in a user group can be modified later. Note that the user
  // will not be logged in or redirected anywhere as a result of being registered. The method returns an integer result
  // code:
  //   NO_ACTION_TAKEN        The user did not post any of the fields required in order to do the registration.
  //   OK                     The user submitted valid information, and was registered successfully.
  //   MISSING_INPUT_FIELD    The user submitted some fields, but not all.
  //   INVALID_PASSWORD       The password contained invalid characters.
  //   PASSWORD_TOO_SHORT     The password was not long enough to be secure.
  //   INVALID_EMAIL          The provided e-mail address was not a valid e-mail address.
  //   EMAIL_EXISTS           An account with this e-mail address already exists. A new account cannot be registered
  //                          using an existing e-mail address.
  //   WORDPRESS_ERROR        The user submitted valid information, but the Wordpress registration failed.
  // After the method has run, $new_user will contain either the user ID of the new user, or a WP_Error object. In case
  // of a WORDPRESS_ERROR, you can use $new_user->get_error_message() to find out what happened.
  public static function register($user_name_field, $password_field, $first_name_field, $last_name_field,
    $phone_no_field, &$new_user)
  {
    // See if the user posted registration data.
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
      $data = Utility::read_posted_strings(
        array($user_name_field, $password_field, $first_name_field, $last_name_field, $phone_no_field));

      // If none of the fields were posted, we'll just display the registration page normally. Otherwise, verify that
      // all fields were posted, and display an error message if they were not.
      $state = Utility::verify_fields($data);
      if ($state === Utility::SOME_PRESENT)
      {
        return Result::MISSING_INPUT_FIELD;
      }
      if ($state === Utility::ALL_PRESENT)
      {
        // All fields were posted. Verify that the password was not modified due to illegal characters.
        if ($data[$password_field] !== $_POST[$password_field])
        {
          return Result::INVALID_PASSWORD;
        }
        // Verify that the password was long enough.
        if (strlen($data[$password_field]) < Utility::PASSWORD_MIN_LENGTH)
        {
          return Result::PASSWORD_TOO_SHORT;
        }
        // Verify that the provided e-mail is valid.
        if (!Utility::is_valid_email($data[$user_name_field]))
        {
          return Result::INVALID_EMAIL;
        }
        // See whether the username and e-mail are available.
        if (username_exists($data[$user_name_field]) || email_exists($data[$user_name_field]))
        {
          return Result::EMAIL_EXISTS;
        }

        // The user posted all required fields. Create a new user. $new_user will contain either the user ID of the new
        // user, or a WP_Error object.
        $new_user = wp_insert_user(array(
          'user_login' => $data[$user_name_field],
          'user_email' => $data[$user_name_field],
          'user_pass' => $data[$password_field],
          'display_name' => $data[$first_name_field] . ' ' . $data[$last_name_field],
          'first_name' => $data[$first_name_field],
          'last_name' => $data[$last_name_field],
          'phone' => $data[$phone_no_field], // Wordpress will store this, even though it is not in the documentation.
          'locale' => Utility::DEFAULT_LANGUAGE
        ));

        // See if the user was created successfully. If not, return an error code.
        if (is_wp_error($new_user))
        {
          error_log('Wordpress user registration failed. Error message: ' . $new_user->get_error_message() .
            ' | User name: ' . $data[$user_name_field] . ' | Password: ' . $data[$password_field] . ' | First name: ' .
            $data[$first_name_field] . ' | Last name: ' . $data[$last_name_field] . ' | Phone no: ' .
            $data[$phone_no_field]);
          return Result::WORDPRESS_ERROR;
        }
        return Result::OK;
      }
    }
    return Result::NO_ACTION_TAKEN;
  }

  // *******************************************************************************************************************
  // Give the user with the given ID the given role number in the given user group. For $role_number, use the
  // ROLE_NUMBER_ constants. The method returns an integer result code:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  public static function register_with_user_group($access_token, $user_id, $role_number)
  {
    $role_data = new Role_Data_Manager($access_token);
    $role_data->set_role_filter($role_number);
    $role_data->set_user_id($user_id);
    return $role_data->create();
  }

  // *******************************************************************************************************************
  // Ensure that the user is not logged in. Note that this will not redirect the user to an appropriate page.
  //
  // Note that the call to wp_logout() would cause the browser to be redirected to https://staging6.dev.gibbs.no/.
  // This is due to code in wp-content/themes/listeo-child/functions.php which reads:
  // add_action('wp_logout','auto_redirect_after_logout');
  // function auto_redirect_after_logout(){
  //   wp_safe_redirect( home_url() );
  //   exit();
  // }
  // This behaviour is undesirable here, as we don't want to go to the main application. Remove the behaviour before
  // logging out.
  public static function log_out()
  {
    if (is_user_logged_in())
    {
      remove_action('wp_logout', 'auto_redirect_after_logout');
      wp_logout();
      $_SESSION = array();
      session_destroy();
    }
  }

  // *******************************************************************************************************************
  // Change the password for the user with the given e-mail address to the given value. The user does not need to be
  // logged in. Possible return values:
  //   PASSWORD_CHANGED     The operation succeeded, and the password was changed.
  //   USER_NOT_FOUND       The user was not found.
  //   INVALID_PASSWORD     The password contained illegal characters.
  //   PASSWORD_TOO_SHORT   The password did not contain enough characters.
  public static function change_password($user_id, $new_password)
  {
    // Verify that the password was not modified due to illegal characters.
    $valid_password = sanitize_text_field($new_password);
    if ($valid_password !== $new_password)
    {
      return Result::INVALID_PASSWORD;
    }
    // Verify that the password was long enough.
    if (strlen($valid_password) < Utility::PASSWORD_MIN_LENGTH)
    {
      return Result::PASSWORD_TOO_SHORT;
    }

    // Find the user, and update the password.
    // We could also find him by e-mail: $user = get_user_by('login', $email);
    $user = get_userdata($user_id);
    if ($user)
    {
      wp_set_password($valid_password, $user->ID);
      return Result::PASSWORD_CHANGED;
    }
    return Result::USER_NOT_FOUND;
  }

  // *******************************************************************************************************************
  // Verify that the user is logged in, and is a Gibbs administrator. If he is, return an access token that does not
  // have a user group ID. If not, report an error as determined by $redirect_on_error. If true, ensure that the user is
  // logged out, and redirect to the login page with HTTP status code 401. If false, return an access token with an
  // appropriate error code. 
  public static function verify_is_gibbs_admin($redirect_on_error = true)
  {
    // We will ignore the $licence_expired flag. A Gibbs administrator does not require a valid licence.
    $licence_expired = false;
    if (!self::user_has_role(Utility::ROLE_GIBBS_ADMIN, $licence_expired))
    {
      if ($redirect_on_error)
      {
        self::send_access_denied();
      }
      else
      {
        return new Access_Token(-1, Utility::ROLE_NONE, Result::ACCESS_DENIED);
      }
    }
    return new Access_Token(-1, Utility::ROLE_GIBBS_ADMIN);
  }

  // *******************************************************************************************************************
  // Verify that the user is logged in, and is an administrator in the current user group. The user group is read from
  // the session. If the user is valid, return an access token that includes the user group ID and the access level
  // granted to the user. If not, report an error as determined by $redirect_on_error. If true, ensure that the user is
  // logged out, and redirect to the login page with HTTP status code 401. If the user is logged in as an administrator,
  // but the licence has expired, redirect to the licence expired page. If false, return an access token with an
  // appropriate error code.
  public static function verify_is_admin($redirect_on_error = true)
  {
    $licence_expired = false;
    $user_group_id = self::verify_has_role(Utility::ROLE_COMPANY_ADMIN, $licence_expired, $redirect_on_error);
    if ($licence_expired)
    {
      if ($redirect_on_error)
      {
        // Redirect to licence expired page for admins.
        Utility::redirect_to('/subscription/html/licence_expired.php');
      }
      else
      {
        return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::LICENCE_EXPIRED);
      }
    }
    return new Access_Token($user_group_id, Utility::ROLE_COMPANY_ADMIN);
  }

  // *******************************************************************************************************************
  // Verify that the user is logged in, and is an ordinary user in the current user group. The user group is read from
  // the session. If the user is valid, return an access token that includes the user group ID and the access level
  // granted to the user. If not, report an error as determined by $redirect_on_error. If true, ensure that the user is
  // logged out, and redirect to the login page with HTTP status code 401. If the user is logged in as a user, but the
  // licence has expired, redirect to the information page. If false, return an access token with an appropriate error
  // code.
  public static function verify_is_user($redirect_on_error = true)
  {
    $licence_expired = false;
    $user_group_id = self::verify_has_role(Utility::ROLE_USER, $licence_expired, $redirect_on_error);
    if ($licence_expired)
    {
      if ($redirect_on_error)
      {
        // Redirect to licence expired page for users.
        Utility::redirect_to('/subscription/html/temporarily_unavailable.php');
      }
      else
      {
        return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::LICENCE_EXPIRED);
      }
    }
    return new Access_Token($user_group_id, Utility::ROLE_USER);
  }

  // *******************************************************************************************************************

  public static function is_gibbs_admin()
  {
      $user_id = get_current_user_id();
        // *** // We may want to find a better way to identify Gibbs administrators. Use user_metadata.
      return $user_id === 2528;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Verify that the user is logged in, and has the given role in the current user group. If the user is valid, return
  // the user group ID. If not, report an error as determined by $redirect_on_error. If true, ensure that the user is
  // logged out, and redirect to the login page with HTTP status code 401. If false, return an access token with an
  // appropriate error code.
  protected static function verify_has_role($role, &$licence_expired, $redirect_on_error = true)
  {
    $user_group_id = self::get_user_group_id();
    if (!self::user_has_role($role, $licence_expired, $user_group_id))
    {
      if ($redirect_on_error)
      {
        self::send_access_denied();
      }
      else
      {
        return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::ACCESS_DENIED);
      }
    }
    return $user_group_id;
  }

  // *******************************************************************************************************************
  // Return true if a user is currently logged in, and that user has sufficient permissions to assume the given role in
  // the user group with the given ID. $role is an integer, using the ROLE_ constants. For the ROLE_GIBBS_ADMIN, the
  // user group ID is irrelevant, as the Gibbs admin has access to all of them. If the licence is expired, the method
  // will return true. However, the $licence_expired flag will also be set to true.
  protected static function user_has_role($role, &$licence_expired, $user_group_id = -1)
  {
    if (is_user_logged_in())
    {
      // See if this is a Gibbs admin. A Gibbs admin has access, period.
      if (self::is_gibbs_admin())
      {
        return true;
      }

      // Other roles require the $user_group_id to be set.
      if ($user_group_id >= 0)
      {
        // Consult the database to find the user's role. This also returns information about the licence status.
        $role_number = User_Data_Manager::get_role_in_user_group($user_group_id, $licence_expired);
        if ($role_number > 0)
        {
          // Verify that the role number in the database matches the required role.
          if ($role === Utility::ROLE_USER)
          {
            return ($role_number === 1);
          }
          if ($role === Utility::ROLE_COMPANY_ADMIN)
          {
            return ($role_number === 2) || ($role_number === 3);
          }
        }
      }
    }
    return false;
  }

  // *******************************************************************************************************************
  // Attempt to log in the Wordpress user with the given user name and password. If successful, redirect to the initial
  // page with HTTP status code 302, and halt script execution. If not, return an error code. The initial page may vary,
  // depending on the user's settings. Possible return values:
  //   WORDPRESS_ERROR        Login failed. This is probably due to the user entering the wrong password.
  protected static function log_in_with($user_name, $password)
  {
    // Attempt to log in.
    $credentials = array(
      'user_login' => $user_name,
      'user_password' => $password,
      'remember' => true
    );
    $current_user = wp_signon($credentials, false);

    // If the user was successfully logged in, redirect to the initial page with HTTP status code 302. If not, return an
    // error message.
    if (is_wp_error($current_user))
    {
      // We do not log this, as it is probably due to the user entering an invalid password. We don't want to fill up
      // the log.
      // error_log('Wordpress error during login: ' . $current_user->get_error_message());
      return Result::WORDPRESS_ERROR;
    }

    self::check_login_and_redirect();
    // We should never get here. If the user was logged in, script execution should be terminated. If he was not,
    // Wordpress should report an error in the step above.
    error_log('The user fell through the world. Please investigate.');
    return Result::WORDPRESS_ERROR;
  }

  // *******************************************************************************************************************
  // Return the index in the given $roles table of the user's primary role. The method will return the index of the
  // first admin role found. If the user has no admin roles, it will return the index of the first user role. If there
  // were no roles at all, the method will return -1. $roles is presumed to be an array of roles, as returned by the
  // User_Data_Manager.get_user_roles method.
  protected static function get_primary_role_index($roles)
  {
    if (is_array($roles) && !empty($roles))
    {
      // Look for admin roles.
      foreach ($roles as $index => $role_data)
      {
        // The role is stored as a tinyint in the database, which might mean it's returned as a string. Convert to a
        // number, just to be sure.
        $role_number = $role_data['role_number'];
        if (!isset($role_number) || !is_numeric($role_number))
        {
          continue;
        }
        $role_number = intval($role_number);
        if (($role_number === Utility::ROLE_NUMBER_LOCAL_ADMIN) ||
          ($role_number === Utility::ROLE_NUMBER_COMPANY_ADMIN) ||
          ($role_number === Utility::ROLE_NUMBER_GIBBS_ADMINISTRATOR))
        {
          return $index;
        }
      }
      // There were no admin roles. Return the first role, which must be a user.
      return 0;
    }
    // There were no roles at all.
    return -1;
  }

  // *******************************************************************************************************************
  // The user tried to access a page without being logged in, or was logged in but did not have permission to access
  // the page. Ensure the user is logged out, then send HTTP 401 and redirect to the login page.
  protected static function send_access_denied()
  {
    self::log_out();
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /subscription/html/log_in.php');
    exit('Du har dessverre ikke tilgang til denne siden. Vennligst <a href="/subscription/html/log_in.php">logg inn</a> med en bruker som har adgang.');
    // Alternately, we could use $_SERVER['HTTP_ORIGIN'] or wp_login_url().
  }

  // *******************************************************************************************************************
  // Return the ID of the user group of which the current user is a member, or -1 if it could not be found. The user
  // group ID is read from the session, and is an integer.
  protected static function get_user_group_id()
  {
    // Read the user group ID from the session.
    $user_group_id = $_SESSION['user_group_id'];
    if (isset($user_group_id) && is_numeric($user_group_id))
    {
      return intval($user_group_id);
    }
    // The user group ID was nowhere to be found.
    return -1;
  }

  // *******************************************************************************************************************
  // Store the given user group ID on the session. If $new_value is -1 or otherwise invalid, the user group ID will be
  // removed entirely. Return the stored user group ID, or -1 if it was invalid.
  protected static function set_user_group_id($new_value)
  {
    if (is_int($new_value) && ($new_value >= 0))
    {
      $_SESSION['user_group_id'] = $new_value;
      return $new_value;
    }
    unset($_SESSION['user_group_id']);
    return -1;
  }

  // *******************************************************************************************************************
}
?>