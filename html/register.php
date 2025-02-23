<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is already logged in, redirect to the initial page with HTTP status code 302.
  User::check_login_and_redirect();

  // Set field names.
  $user_name_field = 'user_name';
  $password_field = 'password';
  $first_name_field = 'first_name';
  $last_name_field = 'last_name';
  $phone_no_field = 'phone';
  $user_group_id_field = 'user_group_id';
  $as_admin_field = 'as_admin';

  // Read posted fields.
  $user_group_id = null;
  if (Utility::integer_posted($user_group_id_field))
  {
    $user_group_id = Utility::read_posted_integer($user_group_id_field);
  }
    // *** // When a viable Gibbs administrator interface exists, either remove this flag (it is vulnerable to hacking.
           // Users can make themselves admin, just by faking a request with that flag set), or verify that the user is
           // an administrator for the user group, or a Gibbs admin, before allowing it.
  $as_admin = Utility::read_posted_boolean($as_admin_field);

  // Attempt to register a new user. $new_user will be updated to contain either the ID of the new user, or a WP_Error
  // object.
  $new_user = null;
  $result_code = User::register($user_name_field, $password_field, $first_name_field, $last_name_field,
    $phone_no_field, $new_user);
  switch ($result_code)
  {
    case Result::OK:
      // The user was created successfully, but is not logged in. Create connection to a user group, if required. Note
      // that the user group ID need not be stored on the session, as that will happen when the user is logged in below.
      if (isset($user_group_id))
      {
        $role_result_code = User::register_with_user_group(new Access_Token($user_group_id, Utility::ROLE_NONE),
          $new_user, ($as_admin ? Utility::ROLE_NUMBER_COMPANY_ADMIN : Utility::ROLE_NUMBER_USER));
        if (($role_result_code === Result::MISSING_INPUT_FIELD) ||
          ($role_result_code === Result::DATABASE_QUERY_FAILED))
        {
          error_log('User group linkup failed after registration. This should not happen. Error code: ' . strval($role_result_code));
          $error_message =
            'Det oppstod en feil ved registrering. Vennligst pr&oslash;v igjen, eller kontakt kundeservice med feilkode ' .
            strval($role_result_code) . '.';
          break;
        }
      }

      // Attempt to log in now. If successful, the user will be redirected to the initial page, and script execution
      // will be halted.
      $login_result_code = User::log_in($user_name_field, $password_field);
      // This should not happen, as the user was just registered successfully.
      error_log('Login failed after registration. This should not happen. Error code: ' . strval($login_result_code));
      $error_message = 'Brukeren ble registrert, men det oppstod en feil ved innlogging (feilkode ' .
        strval($login_result_code) .
        '). Du kan pr&oslash;ve &aring; <a href="/subscription/html/log_in.php">logge inn</a>.';
      break;
    case Result::MISSING_INPUT_FIELD:
      $error_message = 'Alle feltene m&aring; fylles inn.';
      break;
    case Result::INVALID_PASSWORD:
      $error_message = 'Passordet inneholdt ugyldige tegn. Vennligst bruk et annet passord.';
      break;
    case Result::PASSWORD_TOO_SHORT:
      $error_message = 'Passordet var for kort. Vennligst bruk et annet passord.';
      break;
    case Result::INVALID_EMAIL:
      $error_message = 'Ugyldig e-postadresse.';
      break;
    case Result::EMAIL_EXISTS:
      $error_message = 'Det finnes allerede en konto som bruker denne e-postadressen. <a href="/subscription/html/log_in.php">Logg inn</a> eller bruk en annen e-postadresse.';
      break;
    case Result::WORDPRESS_ERROR:
      $error_message = 'Registreringen mislyktes. Kontakt kundeservice, eller pr&oslash;v igjen senere.';
      break;
    case Result::NO_ACTION_TAKEN:
      // The user did not post any registration fields, so the page is being displayed for the first time.
      $error_message = '';
      break;
    default:
      // The registration method returned an unknown result. Log the error, but display the registration page normally.
      error_log('User::register returned an unknown result: ' . strval($result_code));
      $error_message = '';
  }
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - registrer ny bruker</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/register.js"></script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_simple_header('Registrer ny bruker') ?>
    <div class="content">
      <form action="/subscription/html/register.php" method="post">
        <div class="form-element">
          <input type="hidden" name="<?= $user_group_id_field ?>" value="<?= (isset($user_group_id) ? strval($user_group_id) : '') ?>" />
          <input type="hidden" name="<?= $as_admin_field ?>" value="<?= (isset($as_admin) ? var_export($as_admin, true) : 'false') ?>" />
          <label for="firstNameEdit" class="standard-label">Fornavn:</label>
          <input type="text" id="firstNameEdit" name="<?= $first_name_field ?>" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="lastNameEdit" class="standard-label">Etternavn:</label>
          <input type="text" id="lastNameEdit" name="<?= $last_name_field ?>" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="userNameEdit" class="standard-label">E-post:</label>
          <input type="text" id="userNameEdit" name="<?= $user_name_field ?>" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="phoneEdit" class="standard-label">Telefonnr:</label>
          <input type="text" id="phoneEdit" name="<?= $phone_no_field ?>" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="passwordEdit" class="standard-label">Passord:</label>
          <input type="password" id="passwordEdit" name="<?= $password_field ?>" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          (minst <?= strval(Utility::PASSWORD_MIN_LENGTH) ?> tegn)
        </div>
        <div class="button-container fixed-width-container">
          <button type="submit" id="submitButton" class="wide-button"><i class="fa-solid fa-check"></i> Registrer bruker</button>
        </div>
        <?= Utility::enclose_in_error_div($error_message) ?>
      </form>
    </div>
  </body>
</html>
