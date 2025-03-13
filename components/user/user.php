<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/role_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/licencee_data_manager.php';

class User
{
  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Switch to the role with the given ID. If the role was found, and it belongs to the current user, store the user
  // group ID for the new role on the session, redirect to appropriate initial page with HTTP status code 302, and stop
  // executing the current script. The role will not be found unless it is a Minilager role, so we don't have to check
  // for that here. If the role is not found, or does not belong to the currently logged-on user, no action will be
  // taken.
  public static function switch_to_role_id($role_id)
  {
    if (is_int($role_id))
    {
      // If the user asked to be a Gibbs administrator, get the dummy role, and switch to it.
      if ($role_id === Utility::ROLE_ID_GIBBS_ADMINISTRATOR)
      {
        self::switch_to_role(User_Data_Manager::get_gibbs_admin_role());
      }
      elseif ($role_id >= 0)
      {
        // Read the requested role from the database, and switch to it.
        self::switch_to_role(User_Data_Manager::get_role($role_id));
      }
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
      update_user_meta(get_current_user_id(), Utility::ACTIVE_ROLE_ID, $role_data['role_id']);
      Utility::redirect_to(User_Data_Manager::get_role_url($role_data));
    }
  }

  // *******************************************************************************************************************
  // If the user is already logged in, redirect to the appropriate initial page with HTTP status code 302, and stop
  // executing the current script. If he is logged in and is a Gibbs administrator, redirect to the "Gibbs minilager"
  // administration dashboard. If he is logged in, is not a Gibbs administrator, and does not have any roles, redirect
  // him to the dashboard on the main site. If none of the above is the case, return without performing any action.
  //
  // If the user is logged in, and has a valid role, the user group ID will be stored on the session for later use.
  public static function check_login_and_redirect()
  {
    if (is_user_logged_in())
    {
      // get_primary_role_index will get the user group ID from the session, and use the user's role in that user group,
      // if he has one. Otherwise, if he has a stored preference, that one will be used.
      $roles = User_Data_Manager::get_user_roles();
      $index = self::get_primary_role_index($roles);
      if ($index == -2)
      {
        // The user was granted a user role, which is not in the $roles table. Reload the roles, and try again.
        $roles = User_Data_Manager::get_user_roles();
        $index = self::get_primary_role_index($roles);
      }
      if ($index < 0)
      {
        self::set_user_group_id(-1);
        update_user_meta(get_current_user_id(), Utility::ACTIVE_ROLE_ID, -1);
        Utility::redirect_to('/dashbord/');
      }
      else
      {
        self::set_user_group_id(intval($roles[$index]['user_group_id']));
        update_user_meta(get_current_user_id(), Utility::ACTIVE_ROLE_ID, $roles[$index]['role_id']);
        Utility::redirect_to(User_Data_Manager::get_role_url($roles[$index]));
      }
    }
  }

  // *******************************************************************************************************************
  // Attempt to log in, using credentials posted from the client in fields named "user_name" and "password". If
  // successful, the action depends on $redirect_on_success. If true, redirect to the appropriate initial page and
  // exit, in which case script execution will cease. If false, return Result::OK. If login was not successful, the
  // method returns a result code, so the caller can let the user know what happened. The method can be called even if
  // the user has not posted credentials, in which case the method returns Result::NO_ACTION_TAKEN.
  //
  // Possible return values:
  //   OK                     Login was successful, and $redirect_on_success was false.
  //   NO_ACTION_TAKEN        The user did not post any of the fields required in order to log in, or was already logged
  //                          in.
  //   MISSING_INPUT_FIELD    The user submitted some fields, but not all.
  //   INVALID_PASSWORD       The password contained invalid characters.
  //   WORDPRESS_ERROR        Login failed. This is probably due to the user entering the wrong password.
  public static function log_in($redirect_on_success = true)
  {
    // If the user is already logged in, redirect to the initial page with HTTP status code 302, or return an
    // appropriate error code.
    if ($redirect_on_success)
    {
      self::check_login_and_redirect();
    }
    else
    {
      if (is_user_logged_in())
      {
        return Result::NO_ACTION_TAKEN;
      }
    }

    // See if the user posted an e-mail and password.
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
      $data = Utility::read_posted_strings(array('user_name', 'password'));

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
        if ($data['password'] !== $_POST['password'])
        {
          return Result::INVALID_PASSWORD;
        }

        // Attempt to log in. If successful, the user may be redirected to the initial page, and script execution will
        // be halted. If not, return a result code.
        return self::log_in_with($data['user_name'], $data['password'], $redirect_on_success);
      }
    }
    return Result::NO_ACTION_TAKEN;
  }

  // *******************************************************************************************************************
  // Attempt to log in the Wordpress user with the given $user_name and $password. If successful, the result depends on
  // the $redirect_on_success flag. If true, redirect to the initial page with HTTP status code 302, and halt script
  // execution. The initial page may vary, depending on the user's settings. If false, return Result::OK. If login was
  // not successful, return an error code. 
  //
  //  Possible return values:
  //   OK                     Login succeeded, and the $redirect_on_success flag was false.
  //   WORDPRESS_ERROR        Login failed. This is probably due to the user entering the wrong password.
  public static function log_in_with($user_name, $password, $redirect_on_success = true)
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

    if ($redirect_on_success)
    {
      self::check_login_and_redirect();
    }
    else
    {
      return Result::OK;
    }
    // We should never get here. If the user was logged in, script execution should be terminated. If he was not,
    // Wordpress should report an error in the step above.
    error_log('The user fell through the world. Please investigate.');
    return Result::WORDPRESS_ERROR;
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
      array('user_name', 'password', 'first_name', 'last_name', 'phone'));

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
      if ($data['password'] !== $_POST['password'])
      {
        return Result::INVALID_PASSWORD;
      }
      // Verify that the password was long enough.
      if (strlen($data['password']) < Utility::PASSWORD_MIN_LENGTH)
      {
        return Result::PASSWORD_TOO_SHORT;
      }
      // Verify that the provided e-mail is valid.
      if (!Utility::is_valid_email($data['user_name']))
      {
        return Result::INVALID_EMAIL;
      }
      // See whether the username and e-mail are available.
      if (username_exists($data['user_name']) || email_exists($data['user_name']))
      {
        return Result::EMAIL_EXISTS;
      }

      // The user posted all required fields. Create a new user. $new_user_id will contain either the user ID of the new
      // user, or a WP_Error object.
      return array(
        'user_login' => $data['user_name'],
        'user_email' => $data['user_name'],
        'user_pass' => $data['password'],
        'display_name' => $data['first_name'] . ' ' . $data['last_name'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'phone' => $data['phone'], // Wordpress will store this, even though it is not in the documentation.
        'locale' => Utility::DEFAULT_LANGUAGE
      );
    }
*/
  }

  // *******************************************************************************************************************
  // Register a new user using information posted to the page. The fields must have the following names:
  //   entity_type, user_name, password, first_name, last_name, company_name, company_id_number, phone, address,
  //   postcode, area
  //
  // password is optional. If not posted, the given $default_password will be used. The resulting password must have a
  // valid length, and contain valid characters.
  //
  // The user's connection to and role in a user group can be modified later. Note that the user will not be logged in
  // or redirected anywhere as a result of being registered. The method returns an integer result code:
  //   NO_ACTION_TAKEN        The user did not post any of the fields required in order to do the registration.
  //   OK                     The user submitted valid information, and was registered successfully.
  //   MISSING_INPUT_FIELD    The user submitted some fields, but not all.
  //   INVALID_PASSWORD       The password contained invalid characters.
  //   PASSWORD_TOO_SHORT     The password was not long enough to be secure.
  //   INVALID_EMAIL          The provided e-mail address was not a valid e-mail address.
  //   EMAIL_EXISTS           An account with this e-mail address already exists. A new account cannot be registered
  //                          using an existing e-mail address.
  //   WORDPRESS_ERROR        The user submitted valid information, but the Wordpress registration failed.
  // After the method has run, $new_user_id will contain either the user ID of the new user, or a WP_Error object. In
  // case of a WORDPRESS_ERROR, you can use $new_user_id->get_error_message() to find out what happened.
  public static function register(&$new_user_id, $default_password = '')
  {
    // See if the user posted registration data.
    if (($_SERVER['REQUEST_METHOD'] === 'POST') && Utility::integer_posted('entity_type'))
    {
      $entity_type = Utility::read_posted_integer('entity_type');
      if (($entity_type < Utility::ENTITY_TYPE_INDIVIDUAL) || ($entity_type > Utility::ENTITY_TYPE_COMPANY))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      // Read the fields that specify either an individual or a company.
      if ($entity_type === Utility::ENTITY_TYPE_INDIVIDUAL)
      {
        $data = Utility::read_posted_strings(array('user_name', 'password', 'first_name', 'last_name',
          'phone', 'address', 'postcode', 'area', 'country_code'));
      }
      else
      {
        $data = Utility::read_posted_strings(array('user_name', 'password', 'company_name', 'company_id_number',
          'phone', 'address', 'postcode', 'area', 'country_code'));
      }

      // If a password was not posted, use the default value.
      $password_posted = !empty($data['password']);
      if (!$password_posted)
      {
        $data['password'] = $default_password;
      }
      
      // If country_code was not posted, use the default value
      if (empty($data['country_code'])) {
        $data['country_code'] = '+47';
      }

      // Verify that all fields were posted, and display an error message if they were not.
      $state = Utility::verify_fields($data);
      if ($state !== Utility::ALL_PRESENT)
      {
        return Result::MISSING_INPUT_FIELD;
      }

      // All fields were available. Verify that the password was not modified due to illegal characters.
      if ($password_posted && ($data['password'] !== $_POST['password']))
      {
        return Result::INVALID_PASSWORD;
      }
      // Verify that the password was long enough.
      if (strlen($data['password']) < Utility::PASSWORD_MIN_LENGTH)
      {
        return Result::PASSWORD_TOO_SHORT;
      }
      // Verify that the provided e-mail is valid.
      if (!Utility::is_valid_email($data['user_name']))
      {
        return Result::INVALID_EMAIL;
      }
      // See whether the username and e-mail are available.
      if (self::email_in_use($data['user_name']))
      {
        return Result::EMAIL_EXISTS;
      }

      // The user posted all required fields.
      if ($entity_type === Utility::ENTITY_TYPE_INDIVIDUAL)
      {
        // Create a user data array for an individual.
        $data_item = array(
          'user_login' => $data['user_name'],
          'user_email' => $data['user_name'],
          'user_pass' => $data['password'],
          'display_name' => $data['first_name'] . ' ' . $data['last_name'],
          'first_name' => $data['first_name'],
          'last_name' => $data['last_name'],
          'phone' => $data['phone'], // Wordpress will store this, even though it is not in the documentation.
          'locale' => Utility::DEFAULT_LANGUAGE
        );
      }
      else
      {
        // Create a user data array for a company.
        $data_item = array(
          'user_login' => $data['user_name'],
          'user_email' => $data['user_name'],
          'user_pass' => $data['password'],
          'display_name' => $data['company_name'],
          'first_name' => $data['company_name'],
          'last_name' => '',
          'phone' => $data['phone'], // Wordpress will store this, even though it is not in the documentation.
          'locale' => Utility::DEFAULT_LANGUAGE
        );
      }
      
      // Create a new user. $new_user_id will contain either the user ID of the new user, or a WP_Error object.
      $new_user_id = wp_insert_user($data_item);

      // See if the user was created successfully. If not, return an error code.
      if (is_wp_error($new_user_id))
      {
        error_log('Wordpress user registration failed. Error message: ' . $new_user_id->get_error_message() .
          ' | Entity type: ' . $entity_type . ' | User name: ' . $data['user_name'] . ' | Password: ' .
          $data['password'] . ' | First name: ' . $data['first_name'] . ' | Last name: ' . $data['last_name'] .
          ' | Phone no: ' . $data['phone']);
        return Result::WORDPRESS_ERROR;
      }

      // Store entity type and, for a company, the company ID number.
      update_user_meta($new_user_id, 'profile_type', $entity_type === Utility::ENTITY_TYPE_COMPANY ? 'company' : 'personal');
      if ($entity_type === Utility::ENTITY_TYPE_COMPANY)
      {
        update_user_meta($new_user_id, 'company_number', $data['company_id_number']);
        // Add billing company field
        update_user_meta($new_user_id, 'billing_company', $data['company_name']);
      }

      // Store address and customer details with billing_ prefix
      update_user_meta($new_user_id, 'billing_address_1', $data['address']);
      update_user_meta($new_user_id, 'billing_postcode', $data['postcode']);
      update_user_meta($new_user_id, 'billing_city', $data['area']);
      
      // Add billing meta fields
      update_user_meta($new_user_id, 'billing_first_name', $data['first_name']);
      update_user_meta($new_user_id, 'billing_last_name', $data['last_name']);
      update_user_meta($new_user_id, 'billing_phone', $data['phone']);
      update_user_meta($new_user_id, 'billing_email', $data['user_name']);
      update_user_meta($new_user_id, 'country_code', $data['country_code']);
      
      return Result::OK;
    }
    return Result::NO_ACTION_TAKEN;
  }

  // *******************************************************************************************************************
  // Return true if the given $email is in use in an existing Wordpress account.
  public static function email_in_use($email)
  {
    return username_exists($email) || email_exists($email);
  }

  // *******************************************************************************************************************
  // Ensure that the currently logged-in user has at least a user role in the current user group (found on the session).
  // If he does not, add him. Return an access token with a result code to say whether the operation succeeded. If the
  // role was granted successfully, the result will be Result::OK. If the user already had a role in the user group, the
  // result will be Result::NO_ACTION_TAKEN. If the user group did not have a valid licence, the result will be
  // Result::LICENCE_EXPIRED, and the user will not be added. Other results are possible.
  public static function ensure_is_user()
  {
    // Verify the user group.
    $user_group_id = self::get_user_group_id();
    if ($user_group_id < 0)
    {
      return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::USER_GROUP_NOT_FOUND);
    }

    // Verify the user.
    if (!is_user_logged_in())
    {
      return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::ACCESS_DENIED);
    }

    // Find the user's current role, if any.
    $licence_expired = false;
    $role = User_Data_Manager::get_role_in_user_group($user_group_id, $licence_expired);
    if ($licence_expired)
    {
      return new Access_Token($user_group_id, $role, Result::LICENCE_EXPIRED);
    }
  
    // Add the role, if required. A Gibbs administrator does not have a role - but he always has access anyway, so he
    // doesn't need one.
    if (($role === Utility::ROLE_NONE) && !self::is_gibbs_admin())
    {
      $result_code = self::register_with_user_group($user_group_id, get_current_user_id(), Utility::ROLE_NUMBER_USER);
      return new Access_Token($user_group_id, Utility::ROLE_USER, $result_code);
    }

    // The user was logged in, and had a role in the current user group. Nothing needs to be done.
    return new Access_Token($user_group_id, $role, Result::NO_ACTION_TAKEN);
  }

  // *******************************************************************************************************************
  // Give the user with the given $user_id the given $role_number in the user group with the given $user_group_id. For
  // $role_number, use the ROLE_NUMBER_ constants. The method returns an integer result code:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  public static function register_with_user_group($user_group_id, $user_id, $role_number)
  {
    $role_data = new Role_Data_Manager(new Access_Token($user_group_id, Utility::ROLE_NONE));
    $role_data->set_role_filter($role_number);
    $role_data->set_user_id($user_id);
    $result = $role_data->create();
    if ($result === Result::OK)
    {
      update_user_meta($user_id, Utility::ACTIVE_ROLE_ID, $role_data->get_created_item_id());
    }
    return $result;
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
        // Redirect to the licence expired page for administrators.
        self::send_licence_expired_for_admins();
      }
      else
      {
        return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::LICENCE_EXPIRED);
      }
    }
    if ($user_group_id instanceof Access_Token)
    {
      return $user_group_id;
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
        // Redirect to the licence expired page for users. This will terminate script execution.
        self::send_licence_expired_for_users();
      }
      return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::LICENCE_EXPIRED);
    }
    if ($user_group_id instanceof Access_Token)
    {
      return $user_group_id;
    }
    return new Access_Token($user_group_id, Utility::ROLE_USER);
  }

  // *******************************************************************************************************************
  // Check whether the user is logged in, and is an ordinary user in the current user group. If he is, return the access
  // token. If not, and if a user group ID was passed on the request or found on the session, return an access token
  // that lets the user use the application anonymously in the specified user group. If $redirect_on_error is true, and
  // we could not find a usable access token, redirect to the login page with HTTP status code 401. If false, return an
  // access token with an appropriate error code.
  public static function verify_is_user_or_anonymous($redirect_on_error = true)
  {
    // See if the user is already logged in as an ordinary user. 
    $access_token = self::verify_is_user(false);
    if ($access_token->is_error())
    {
      // The user was not logged in, or was not a regular user, or perhaps something else went wrong. He can still go
      // through the booking process, though; he will simply have to register before a subscription is created. We need
      // to know which user group to use, however. See if a parameter was passed.
      if (Utility::integer_passed('user_group_id'))
      {
        self::log_out();
        $access_token = self::use_anonymously(Utility::read_passed_integer('user_group_id'), false);
      }
      else
      {
        // No parameter was passed. See if the user group ID is available on the session.
        $user_group_id = self::get_user_group_id();
        if ($user_group_id >= 0)
        {
          self::log_out();
          $access_token = self::use_anonymously($user_group_id, false);
        }
      }
    }
    else
    {
      // The user was already logged in. If a user group ID was passed, the user group in the access token must match.
      // Otherwise, the user was logged in to the wrong user group, and must be logged out before using the booking
      // process anonymously.
      if (Utility::integer_passed('user_group_id'))
      {
        $user_group_id = Utility::read_passed_integer('user_group_id');
        if (($user_group_id >= 0) && ($user_group_id !== $access_token->get_user_group_id()))
        {
          self::log_out();
          $access_token = self::use_anonymously($user_group_id, false);
        }
      }
    }
    // If we still have an error, and we should redirect on error, send an appropriate HTTP response.
    if ($redirect_on_error)
    {
      $access_token->redirect_on_error();
    }
    return $access_token;
  }

  // *******************************************************************************************************************
  // If the user is not logged in, or there is no user group ID on the session, report an error as determined by
  // $redirect_on_error. If true, ensure that the user is logged out, and redirect to the login page with HTTP status
  // code 401. If false, return an access token with Result::ACCESS_DENIED. If the user is logged in, and there is a
  // user group ID on the session, take no action except to return an access token with Result::OK. This access token
  // will have ROLE_NONE, as we do not know here which role the user has in the current user group. Note that Gibbs
  // admins may continue even if there is no user group ID on the session.
  public static function verify_logged_in($redirect_on_error = true)
  {
    $user_group_id = self::get_user_group_id();
    if (!is_user_logged_in() || (($user_group_id < 0) && !self::is_gibbs_admin()))
    {
      if ($redirect_on_error)
      {
        self::send_access_denied();
      }
      return new Access_Token($user_group_id, Utility::ROLE_NONE, Result::ACCESS_DENIED);
    }
    return new Access_Token($user_group_id, Utility::ROLE_NONE);
  }

  // *******************************************************************************************************************
  // Return an access token for the user group with the given $user_group_id. The method does not check the user's role,
  // assuming that the currently logged-in user, if any, has no role in the user group. If the user group does not
  // exist, the access token will have result code Result::USER_GROUP_NOT_FOUND. If the user group exists, but does not
  // have a valid licence, the result code will be Result::LICENCE_EXPIRED. If $redirect_on_error is true, the method
  // will send an appropriate HTTP response in case of an error, and return the access token only if no error was
  // encountered. If everything succeeded, the user group ID will be stored on the session.
  public static function use_anonymously($user_group_id, $redirect_on_error = true)
  {
    // Look for the user group, and check its licence.
    $result_code = Result::USER_GROUP_NOT_FOUND;
    if ($user_group_id >= 0)
    {
      $has_licence = Licencee_Data_Manager::has_active_licence($user_group_id);
      if ($has_licence === true)
      {
        $result_code = Result::OK;
        self::set_user_group_id($user_group_id);
      }
      elseif ($has_licence === false)
      {
        $result_code = Result::LICENCE_EXPIRED;
      }
    }

    // Create the access token, and send an HTTP response if required. If an HTTP response is sent, script execution
    // ceases.
    $access_token = new Access_Token($user_group_id, Utility::ROLE_NONE, $result_code);
    if ($redirect_on_error)
    {
      $access_token->redirect_on_error();
    }
    return $access_token;
  }

  // *******************************************************************************************************************
  // Return the entity type of the user with the given $user_id, using the ENTITY_TYPE_ constants. $user_id is optional.
  // If not provided, the currently logged-in user will be used.
  public static function get_entity_type($user_id = null)
  {
    if ($user_id === null)
    {
      $user_id = get_current_user_id();
    }
    $entity_type = get_user_meta($user_id, 'profile_type', true);
    if ($entity_type === 'company')
    {
      return Utility::ENTITY_TYPE_COMPANY;
    }
    return Utility::ENTITY_TYPE_INDIVIDUAL;
  }

  // *******************************************************************************************************************
  // Return true if the user with the given $user_id is a Gibbs administrator. $user_id is optional. If not provided,
  // the currently logged-in user will be used.
  public static function is_gibbs_admin($user_id = null)
  {
    if ($user_id === null)
    {
      $user_id = get_current_user_id();
    }
    $role = get_user_meta($user_id, 'role', true);
    return $role === 'administrator';
  }

  // *******************************************************************************************************************
  // Redirect to the licence expired page for administrators.
  public static function send_licence_expired_for_admins()
  {
    Utility::redirect_to('/subscription/html/licence_expired.php');
  }

  // *******************************************************************************************************************
  // Redirect to the licence expired page for users.
  public static function send_licence_expired_for_users()
  {
    Utility::redirect_to('/subscription/html/temporarily_unavailable.php');
  }

  // *******************************************************************************************************************
  // The user tried to access a page without being logged in, or was logged in but did not have permission to access
  // the page. Ensure the user is logged out, then send HTTP 401 and redirect to the login page.
  public static function send_access_denied()
  {
    self::log_out();
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /subscription/html/log_in_to_dashboard.php');
    exit('Du har dessverre ikke tilgang til denne siden. Vennligst <a href="/subscription/html/log_in_to_dashboard.php">logg inn</a> med en bruker som har adgang.');
    // Alternately, we could use $_SERVER['HTTP_ORIGIN'] or wp_login_url().
  }

  // *******************************************************************************************************************
  // Return the ID of the user group of which the current user is a member, or -1 if it could not be found. The user
  // group ID is read from the session, and is an integer.
  public static function get_user_group_id()
  {
    // Read the user group ID from the session.
    if (isset($_SESSION['user_group_id']) && is_numeric($_SESSION['user_group_id']))
    {
      return intval($_SESSION['user_group_id']);
    }
    // The user group ID was nowhere to be found.
    return -1;
  }

  // *******************************************************************************************************************
  // Store the given user group ID on the session. If $new_value is -1 or otherwise invalid, the user group ID will be
  // removed entirely. Return the stored user group ID, or -1 if it was invalid.
  public static function set_user_group_id($new_value)
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
        // Consult the database to find the user's actual role, and compare it to the desired $role. The call also
        // returns information about the licence status.
        return $role === User_Data_Manager::get_role_in_user_group($user_group_id, $licence_expired);
      }
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return the index in the given $roles table of the currently logged-in user's primary role.
  //
  // If a user group ID is stored on the session:
  //   This is taken to mean that we are on that user group's web site, and the customer should not be sent anywhere
  //   else. If the user already has a role in that user group, that role will be used. Otherwise, he will be given a
  //   user role in that group. Note that, in that case, the user's role will not exist in the $roles table. Return -2
  //   to indicate that the caller must reload the user roles and call this method again.
  //
  // If a user group ID is not stored on the session:
  //   This is taken to mean that the user is logging in from a Gibbs website, and is not tied to a particular user
  //   group. If a role ID is stored in the user's meta information (which happens whenever you visit a user group; the
  //   stored value will therefore be the last visited user group), that role will be used. Otherwise, the method will
  //   return the index of the first admin role found. If the user has no admin roles, it will return the index of the
  //   first user role. If there were no roles at all, the method will return -1. $roles is presumed to be an array of
  //   roles, as returned by the User_Data_Manager.get_user_roles method.
  protected static function get_primary_role_index($roles)
  {
    if (is_array($roles) && !empty($roles))
    {
      // Look for a user group ID on the session. If the user has a role in that user group, use it.
      $user_group_id = self::get_user_group_id();
      if ($user_group_id >= 0)
      {
        // Look for an existing role.
        foreach ($roles as $index3 => $role_data3)
        {
          if ($role_data3['user_group_id'] === $user_group_id)
          {
            return $index3;
          }
        }
        // No role was found. Assign a user role.
        $role_result_code = self::register_with_user_group($user_group_id, get_current_user_id(),
          Utility::ROLE_NUMBER_USER);
        if (($role_result_code === Result::MISSING_INPUT_FIELD) ||
          ($role_result_code === Result::DATABASE_QUERY_FAILED))
        {
          error_log('Failed to give user role when logging in to a particular user group. Error code: ' .
            strval($role_result_code));
          return -1;
        }
      }

      // Look for a current user group setting.
      $active_role_id = get_user_meta(get_current_user_id(), Utility::ACTIVE_ROLE_ID, true);
      if (($active_role_id !== false) && is_numeric($active_role_id))
      {
        $active_role_id = intval($active_role_id);
        if ($active_role_id >= 0)
        {
          foreach ($roles as $index2 => $role_data2)
          {
            if ($role_data2['role_id'] === $active_role_id)
            {
              return $index2;
            }
          }
        }
      }
 
      // Look for admin roles.
      foreach ($roles as $index => $role_data)
      {
        $role_number = $role_data['role_number'];
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
}
?>