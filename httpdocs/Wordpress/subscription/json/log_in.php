<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

/*
  This file will log a user in to a particular user group, the ID of which must be found on the session. The user does
  not need to have a role in that user group. If he does not, he will be granted the user role later, after he confirms
  the purchase, but before the subscription is created. This file is used asynchronously, and the user will not be
  redirected anywhere.

  The actions taken depend on the current state of the user:

    E-mail in use | Logged in       | Has role        | Action
    --------------+-----------------+-----------------+-----------------------------------------------------------------
    No            | No (impossible) | No (impossible) | Return error.
    Yes           | No              | No              | Log in.
    Yes           | No              | Yes             | Log in.
    Yes           | Yes             | No              | None.
    Yes           | Yes             | Yes             | None.

  This file expects the following parameters to be posted:
    user_name : string        The e-mail used to log in.
    password : string         The user's password.

  This file will return a JSON object with the following fields:
    resultCode : integer      Specifies what happened, using the Result codes from utility.php. If the user was already
                              logged in, the value will be Result::NO_ACTION_TAKEN. If the user was logged in
                              successfully, the value will be Result::OK. Other values are possible.
    errorMessage : string     A description of any errors that may have occurred, in the currently selected language.
    user : object             If the user logged in successfully, this will be an object that holds user information.
                              Otherwise, it will be null. If present, the object will hold the following fields:
                                user_id : integer
                                name : string
                                email : string
                                phone : string
                                first_name : string
                                last_name : string
                                address : string
                                postcode : string
                                area : string
                                company_id_number : string
                                entity_type : integer (use the ENTITY_TYPE_ constants)
*/

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  $user = 'null';
  $error_message = '';
  // Catch wrong password. In this case, Wordpress redirects to the same page, with a custom parameter.
  if (Utility::read_passed_string('login', '') === 'incorrect_password')
  {
    $result_code = Result::WRONG_PASSWORD;
    $error_message = $text->get(3, 'Feil passord.');
  }
  else
  {
    if (is_user_logged_in())
    {
      $result_code = Result::NO_ACTION_TAKEN;
    }
    else
    {
      // Attempt to log in using the user name and password passed to the page,
      $result_code = User::log_in(false);
      switch ($result_code)
      {
        case Result::OK:
          $user = User_Data_Manager::get_user();
          break;
        case Result::NO_ACTION_TAKEN:
        case Result::MISSING_INPUT_FIELD:
          // If the result is NO_ACTION_TAKEN, the user did not post any credentials. This file always requires the user
          // name and password to be posted.
          $result_code = Result::MISSING_INPUT_FIELD;
          $error_message = $text->get(0, 'Alle feltene m&aring; fylles inn.');
          break;
        case Result::INVALID_PASSWORD:
          $error_message = $text->get(1, 'Passordet inneholdt ugyldige tegn. Vennligst bruk et gyldig passord.');
          break;
        case Result::WORDPRESS_ERROR:
          $error_message = $text->get(2, 'Innlogging mislyktes. Dette kan skyldes at du har tastet feil passord.');
          break;
        default:
          // The login method returned an unknown result. Log the error, and return the result code.
          error_log('User::log_in returned an unknown result: ' . strval($result_code));
          $error_message = '';
      }
    }
  }
?>
{
  "resultCode": <?= $result_code ?>,
  "errorMessage": "<?= $error_message ?>",
  "user": <?= $user ?>
}