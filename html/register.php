<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

/*
  This file will allow the user to enter information and register a new user. When user information is posted to this
  page, a user will be created, and linked to the user group. Then the user will be logged in, and redirected to the
  dashboard.

  Note that this file can only create regular users - not administrators. It also assumes that the user is not already
  logged in.
*/

  // If the user is already logged in, redirect to the initial page with HTTP status code 302.
  User::check_login_and_redirect();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read parameters.
  $user_group_id = null;
  if (Utility::integer_passed('user_group_id'))
  {
    $user_group_id = Utility::read_passed_integer('user_group_id');
  }

  // Attempt to register a new user. $new_user_id will be updated to contain either the ID of the new user, or a
  // WP_Error object.
  $new_user_id = null;
  $result_code = User::register($new_user_id);
  switch ($result_code)
  {
    case Result::OK:
      // The user was created successfully, but is not logged in. Create connection to a user group, if required. Note
      // that the user group ID need not be stored on the session, as that will happen when the user is logged in below.
      if (isset($user_group_id))
      {
        $role_result_code = User::register_with_user_group($user_group_id, $new_user_id, Utility::ROLE_NUMBER_USER);
        if (($role_result_code === Result::MISSING_INPUT_FIELD) ||
          ($role_result_code === Result::DATABASE_QUERY_FAILED))
        {
          error_log('User group linkup failed after registration. This should not happen. Error code: ' . strval($role_result_code));
          $error_message = $text->get(8, 
            'Det oppstod en feil ved registrering. Vennligst pr&oslash;v igjen, eller kontakt kundeservice med feilkode $0.',
            array(strval($role_result_code))
          );
          break;
        }
      }

      // Attempt to log in now. If successful, the user will be redirected to the initial page, and script execution
      // will be halted.
      $login_result_code = User::log_in();
      // This should not happen, as the user was just registered successfully.
      error_log('Login failed after registration. This should not happen. Error code: ' . strval($login_result_code));
      $error_message = $text->get(9, 
        'Brukeren ble registrert, men det oppstod en feil ved innlogging (feilkode $0). Du kan pr&oslash;ve &aring; <a href="/subscription/html/log_in_to_dashboard.php">logge inn</a>.',
        array(strval($login_result_code))
      );
      break;
    case Result::MISSING_INPUT_FIELD:
      $error_message = $text->get(10, 'Alle feltene m&aring; fylles inn.');
      break;
    case Result::INVALID_PASSWORD:
      $error_message = $text->get(11, 'Passordet inneholdt ugyldige tegn. Vennligst bruk et annet passord.');
      break;
    case Result::PASSWORD_TOO_SHORT:
      $error_message = $text->get(12, 'Passordet var for kort. Vennligst bruk et annet passord.');
      break;
    case Result::INVALID_EMAIL:
      $error_message = $text->get(13, 'Ugyldig e-postadresse.');
      break;
    case Result::EMAIL_EXISTS:
      $error_message = $text->get(14, 'Det finnes allerede en konto som bruker denne e-postadressen. <a href="/subscription/html/log_in_to_dashboard.php">Logg inn</a> eller bruk en annen e-postadresse.');
      break;
    case Result::WORDPRESS_ERROR:
      $error_message = $text->get(15, 'Registreringen mislyktes. Kontakt kundeservice, eller pr&oslash;v igjen senere.');
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
  
  // Read selected language from the session.
  $current_language = Utility::get_current_language();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/common.css?v=<?= Utility::BUILD_NO ?>" />
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/register.js?v=<?= Utility::BUILD_NO ?>"></script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_simple_header($text->get(0, 'Registrer ny bruker')) ?>
    <div class="content">
      <div class="form-element">
        <form id="selectLanguageForm" action="/subscription/html/set_language.php" method="post">
          <input type="hidden" name="redirect_to" value="<?= sanitize_text_field($_SERVER['REQUEST_URI']) ?>" />
          <select name="language" onchange="submitLanguageSelection();">
            <option value="<?= Utility::NORWEGIAN ?>" <?= ($current_language === Utility::NORWEGIAN ? 'selected="selected"' : '') ?>>Norsk (bokm&aring;l)</option>
            <option value="<?= Utility::ENGLISH ?>" <?= ($current_language === Utility::ENGLISH ? 'selected="selected"' : '') ?>>English (UK)</option>
          </select>
        </form>
      </div>

      <form id="newUserForm" action="/subscription/html/register.php" method="post">
        <div class="form-element">
          <label>
            <input type="radio" id="newIndividualButton" name="entity_type" value="0" checked="checked" onchange="selectEntityType();" />
            <?= $text->get(19, 'Privatperson') ?> <span class="mandatory">*</span>
          </label>
          <label>
            <input type="radio" id="newCompanyButton" name="entity_type" value="1" onchange="selectEntityType();" />
            <?= $text->get(20, 'Bedrift') ?> <span class="mandatory">*</span>
          </label>
        </div>
        <div class="form-element">
          <input type="hidden" name="user_group_id" value="<?= (isset($user_group_id) ? strval($user_group_id) : '') ?>" />
          <label for="userNameEdit" class="standard-label"><?= $text->get(3, 'E-post:') ?> <span class="mandatory">*</span></label>
          <input type="email" id="userNameEdit" name="user_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div id="individualDataBox">
          <div class="form-element">
            <label for="firstNameEdit" class="standard-label"><?= $text->get(1, 'Fornavn:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="firstNameEdit" name="first_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
          <div class="form-element">
            <label for="lastNameEdit" class="standard-label"><?= $text->get(2, 'Etternavn:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="lastNameEdit" name="last_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
        </div>
        <div id="companyDataBox" style="display: none;">
          <div class="form-element">
            <label for="newCompanyNameEdit" class="standard-label"><?= $text->get(21, 'Navn:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="newCompanyNameEdit" name="company_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
          <div class="form-element">
            <label for="newCompanyIdEdit" class="standard-label"><?= $text->get(22, 'Org. nr:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="newCompanyIdEdit" name="company_id_number" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
        </div>
        <div class="form-element">
          <label for="phoneEdit" class="standard-label"><?= $text->get(4, 'Telefonnr:') ?> <span class="mandatory">*</span></label>
          <input type="text" id="phoneEdit" name="phone" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="addressEdit" class="standard-label"><?= $text->get(16, 'Adresse:') ?> <span class="mandatory">*</span></label>
          <input type="text" id="addressEdit" name="address" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="postcodeEdit" class="standard-label"><?= $text->get(17, 'Postnr:') ?> <span class="mandatory">*</span></label>
          <input type="text" id="postcodeEdit" name="postcode" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="areaEdit" class="standard-label"><?= $text->get(18, 'Poststed:') ?> <span class="mandatory">*</span></label>
          <input type="text" id="areaEdit" name="area" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="passwordEdit" class="standard-label"><?= $text->get(5, 'Passord:') ?> <span class="mandatory">*</span></label>
          <input type="password" id="passwordEdit" name="password" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          <?= $text->get(6, '(minst $0 tegn)', array(strval(Utility::PASSWORD_MIN_LENGTH))) ?>
        </div>
        <div class="button-container fixed-width-container">
          <button type="button" id="submitButton" class="wide-button" onclick="Utility.displaySpinnerThenSubmit(newUserForm);"><i class="fa-solid fa-check"></i> <?= $text->get(7, 'Registrer bruker') ?></button>
        </div>
        <?= Utility::enclose_in_error_div($error_message) ?>
      </form>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
