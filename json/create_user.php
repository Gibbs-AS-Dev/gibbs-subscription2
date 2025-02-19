<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';

/*
  This file will create a new user, and - if successful, and the user was not created by an administrator - log him in.
  This file is used asynchronously, and the user will not be redirected anywhere. If a user group ID is found on the
  session, this file will also create a connection to a user group after the user is created.

  The actions taken depend on the current state of the user:

    E-mail in use | Logged in        | Has role        | Action
    --------------+------------------+-----------------+-----------------------------------------------------------------
    No            | No               | No (impossible) | Register new user. Grant user role. Log in.
    No            | Yes (admin)      | No (impossible) | Register new user. Grant user role.
    No            | Yes (other user) | Who cares?      | Return error.
    Yes           | No               | No              | Return error.
    Yes           | No               | Yes             | Return error.
    Yes           | Yes              | No              | Return error.
    Yes           | Yes              | Yes             | Return error.

  If a user is already logged in, but is not an administrator, the file will return Result::ACCESS_DENIED. If the posted e-mail is already in use,
  the file will return EMAIL_EXISTS. If not, the file will return Result::OK if everything succeeded, or an appropriate
  error message.

  This file expects the following parameters to be posted:
    user_name : string
    first_name : string
    last_name : string
    phone : string
  It will also read the following fields if passed:
    password : string
    address : string
    postcode : string
    area : string

  If no password is posted, the file will assign a password. If the application role is APP_ROLE_PRODUCTION, the
  password will be a random collection of letters and numbers (a..z, A..Z, 0..9) with a length of between 10 and 16
  characters. Otherwise, the password will be "test1234".

  This file will return a JSON object, with the following fields:
    resultCode : integer      Specifies what happened, using the Result codes from utility.php.
    errorMessage : string     A description of any errors that may have occurred, in the currently selected language.
    userId : integer          The ID of the user that was created, or -1 if it was not.
*/

  // Get translated texts.
  $text = new Translation('', 'storage', '');
 
  $is_admin = false;
  $new_user_id = -1;
  $error_message = '';
  // Catch wrong password. In this case, Wordpress redirects to the same page, with a custom parameter. Since we are
  // logging in a user we just created, this should not happen.
  if (Utility::read_passed_string('login', '') === 'incorrect_password')
  {
    $result_code = Result::INVALID_PASSWORD;
    $error_message = $text->get(7, 'Passordfeil.');
  }
  else
  {
    // See if the user is logged in. If he is, he needs to be an administrator. Only administrators can create accounts
    // on behalf of other people. Users can create accounts for themselves, but they cannot be logged in as someone else
    // while doing it.
    if (is_user_logged_in())
    {
      $access_token = User::verify_is_admin(false);
      $result_code = $access_token->get_result_code();
      if ($access_token->is_error())
      {
        $error_message = $text->get(8, 
          'Det oppstod en feil ved registrering. Vennligst pr&oslash;v igjen, eller kontakt kundeservice med feilkode $0.',
          array(strval($result_code)));
      }
      else
      {
        $is_admin = true;
      }
    }
    else
    {
      $result_code = Result::OK;
    }

    if ($result_code === Result::OK)
    {
      // Read posted e-mail, and figure out whether it is in use.
      if (!Utility::string_posted('user_name'))
      {
        $result_code = Result::MISSING_INPUT_FIELD;
        $error_message = $text->get(0, 'Alle feltene m&aring; fylles inn.');
      }
      else
      {
        $email = Utility::read_posted_string('user_name');
        if (User::email_in_use($email))
        {
          // An account with that e-mail already exists. The user has to use the log_in.php file to log in, or use another
          // e-mail address.
          $result_code = Result::EMAIL_EXISTS;
          $error_message = $text->get(1,
            'Det finnes allerede en konto som bruker denne e-postadressen. Logg inn, eller bruk en annen e-postadresse.');
        }
        else
        {
          // Set a default password if none was posted.
          if (Utility::string_posted('password'))
          {
            $password = Utility::read_posted_string('password');
          }
          else
          {
            // No password was posted. Generate a random password of random length (between 10 and 16 characters).
            $password = Utility::get_random_string(rand(10, 16));
            // Try to load the settings for the current user group, in order to find the application role. If the
            // application role is not APP_ROLE_PRODUCTION, use a hard coded password instead.
            $user_group_id = User::get_user_group_id();
            if ($user_group_id >= 0)
            {
              $access_token = User::use_anonymously($user_group_id, false);
              if (!$access_token->is_error())
              {
                $settings = Settings_Manager::read_settings($access_token);
                if ($settings->get_application_role() !== Settings::APP_ROLE_PRODUCTION)
                {
                  $password = 'test1234';
                }
              }
            }
          }
          // Attempt to register a new user. $new_user_id will be updated to contain either the ID of the new user, or a
          // WP_Error object.
          $result_code = User::register($new_user_id, $password);
          switch ($result_code)
          {
            case Result::OK:
              // The user was created successfully, but is not logged in. Create connection to a user group, if a user
              // group ID is found on the session.
              $user_group_id = User::get_user_group_id();
              if (isset($user_group_id))
              {
                $role_result_code = User::register_with_user_group($user_group_id, $new_user_id,
                  ($as_admin ? Utility::ROLE_NUMBER_COMPANY_ADMIN : Utility::ROLE_NUMBER_USER));
                if (($role_result_code === Result::MISSING_INPUT_FIELD) ||
                  ($role_result_code === Result::DATABASE_QUERY_FAILED))
                {
                  error_log('User group linkup failed after registration. This should not happen. Error code: ' .
                    strval($role_result_code));
                  $error_message = $text->get(8, 
                    'Det oppstod en feil ved registrering. Vennligst pr&oslash;v igjen, eller kontakt kundeservice med feilkode $0.',
                    array(strval($role_result_code))
                  );
                  break;
                }
              }

              // Attempt to log in now, unless the user was created by an administrator (who is already logged in).
              if (!$is_admin)
              {
                $result_code = User::log_in_with(Utility::read_posted_string('user_name'), $password, false);
                if ($result_code !== Result::OK)
                {
                  // This should not happen, as the user was just registered successfully.
                  error_log('Login failed after registration. This should not happen. Error code: ' . strval($result_code));
                  $error_message = $text->get(2, 
                    'Brukeren ble registrert, men det oppstod en feil ved innlogging (feilkode $0). Du kan pr&oslash;ve &aring; logge inn, eller kontakte kundeservice.',
                    array(strval($result_code))
                  );
                }
              }
              break;
            case Result::NO_ACTION_TAKEN:
            case Result::MISSING_INPUT_FIELD:
              // If the result is NO_ACTION_TAKEN, the user did not post any information. This file always requires the
              // information to be posted.
              $result_code = Result::MISSING_INPUT_FIELD;
              $error_message = $text->get(0, 'Alle feltene m&aring; fylles inn.');
              break;
            case Result::INVALID_PASSWORD:
              $error_message = $text->get(3, 'Passordet inneholdt ugyldige tegn. Vennligst bruk et annet passord.');
              break;
            case Result::PASSWORD_TOO_SHORT:
              $error_message = $text->get(4, 'Passordet var for kort. Vennligst bruk et annet passord.');
              break;
            case Result::INVALID_EMAIL:
              $error_message = $text->get(5, 'Ugyldig e-postadresse.');
              break;
            case Result::EMAIL_EXISTS:
              $error_message = $text->get(1, 'Det finnes allerede en konto som bruker denne e-postadressen. Logg inn, eller bruk en annen e-postadresse.');
              break;
            case Result::WORDPRESS_ERROR:
              $error_message = $text->get(6, 'Registreringen mislyktes. Kontakt kundeservice, eller pr&oslash;v igjen senere.');
              break;
            default:
              // The login method returned an unknown result. Log the error, and return the result code.
              error_log('User::register returned an unknown result: ' . strval($result_code));
          }
        }
      }
    }
  }
?>
{
  "resultCode": <?= $result_code ?>,
  "errorMessage": "<?= $error_message ?>",
  "userId": <?= $new_user_id ?>
}