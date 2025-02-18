<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/licencee_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

/*
  This file will allow the user to enter information and register a new user. When user information is posted to this
  page, a user will be created, and linked to the user group. The user will not be logged in.

  This file can create both regular users and administrators. It is only available to Gibbs administrators.
*/

  // If the user is not logged in as a Gibbs administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_gibbs_admin();

  // Read licencees to be displayed to the user.
  $licencee_data = new Licencee_Data_Manager($access_token);
  $licencees = $licencee_data->get_licencees();
  if ($licencees === null)
  {
    $licencees = array();
  }

  // Attempt to register a new user. $new_user_id will be updated to contain either the ID of the new user, or a
  // WP_Error object.
  $new_user_id = null;
  $result_code = User::register($new_user_id);
  switch ($result_code)
  {
    case Result::OK:
      // The user was created successfully, but is not logged in. Create connection to a user group, if required.
      if (Utility::integer_posted('user_group_id'))
      {
        $role_result_code = User::register_with_user_group(Utility::read_posted_integer('user_group_id'), $new_user_id,
          (Utility::read_posted_boolean('as_admin') ? Utility::ROLE_NUMBER_COMPANY_ADMIN : Utility::ROLE_NUMBER_USER));
        if (($role_result_code === Result::MISSING_INPUT_FIELD) ||
          ($role_result_code === Result::DATABASE_QUERY_FAILED))
        {
          error_log('User group linkup failed after registration. Error code: ' . strval($role_result_code));
          $error_message = 'User group linkup failed after registration. Error code: ' . strval($role_result_code);
          break;
        }
      }
      $error_message = 'User registered successfully.';
        // *** // Format the message in such a way that it doesn't appear to be an error.
      break;
    case Result::MISSING_INPUT_FIELD:
      $error_message = 'You have to fill in all the fields.';
      break;
    case Result::INVALID_PASSWORD:
      $error_message = 'The password contained illegal characters. Please use another password.';
      break;
    case Result::PASSWORD_TOO_SHORT:
      $error_message = 'The password was too short. Please use another password.';
      break;
    case Result::INVALID_EMAIL:
      $error_message = 'Invalid e-mail address.';
      break;
    case Result::EMAIL_EXISTS:
      $error_message = 'This e-mail is already registered. Please use another e-mail address.';
      break;
    case Result::WORDPRESS_ERROR:
      $error_message = 'The registration failed due to a Wordpress error.';
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
    <?= Sidebar::get_gibbs_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, 'Register new user', 'fa-user-plus') ?>
    <div class="content">
      <form id="newUserForm" action="/subscription/html/gibbs_register.php" method="post">
        <div class="form-element">
          <label for="userGroupCombo">Register new user at:</label>
          <select id="userGroupCombo" name="user_group_id" class="long-text">
<?php
  foreach ($licencees as $licencee)
  {
    echo('<option value="' . strval($licencee->user_group_id) . '">' . $licencee->user_group_name . '</option>');
  }
?>
          </select>
        </div>
        <div class="form-element">
          <label for="adminCheckbox">
            <input type="checkbox" id="adminCheckbox" name="as_admin" value="true" /> Administrator
          </label>        
        </div>
        <div class="form-element">
          <label>
            <input type="radio" id="newIndividualButton" name="entity_type" value="0" checked="checked" onchange="selectEntityType();" />
            Individual <span class="mandatory">*</span>
          </label>
          <label>
            <input type="radio" id="newCompanyButton" name="entity_type" value="1" onchange="selectEntityType();" />
            Company <span class="mandatory">*</span>
          </label>
        </div>
        <div class="form-element">
          <label for="userNameEdit" class="standard-label">E-mail: <span class="mandatory">*</span></label>
          <input type="email" id="userNameEdit" name="user_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div id="individualDataBox">
          <div class="form-element">
            <label for="firstNameEdit" class="standard-label">First name: <span class="mandatory">*</span></label>
            <input type="text" id="firstNameEdit" name="first_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
          <div class="form-element">
            <label for="lastNameEdit" class="standard-label">Last name: <span class="mandatory">*</span></label>
            <input type="text" id="lastNameEdit" name="last_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
        </div>
        <div id="companyDataBox" style="display: none;">
          <div class="form-element">
            <label for="newCompanyNameEdit" class="standard-label">Name: <span class="mandatory">*</span></label>
            <input type="text" id="newCompanyNameEdit" name="company_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
          <div class="form-element">
            <label for="newCompanyIdEdit" class="standard-label">ID number: <span class="mandatory">*</span></label>
            <input type="text" id="newCompanyIdEdit" name="company_id_number" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
        </div>
        <div class="form-element">
          <label for="phoneEdit" class="standard-label">Phone no: <span class="mandatory">*</span></label>
          <input type="text" id="phoneEdit" name="phone" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="addressEdit" class="standard-label">Address: <span class="mandatory">*</span></label>
          <input type="text" id="addressEdit" name="address" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="postcodeEdit" class="standard-label">Postcode: <span class="mandatory">*</span></label>
          <input type="text" id="postcodeEdit" name="postcode" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="areaEdit" class="standard-label">Area: <span class="mandatory">*</span></label>
          <input type="text" id="areaEdit" name="area" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="passwordEdit" class="standard-label">Password: <span class="mandatory">*</span></label>
          <input type="password" id="passwordEdit" name="password" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          (at least <?= Utility::PASSWORD_MIN_LENGTH ?> characters)
        </div>
        <div class="button-container fixed-width-container">
          <button type="button" id="submitButton" class="wide-button" onclick="Utility.displaySpinnerThenSubmit(newUserForm);"><i class="fa-solid fa-check"></i> Register user</button>
        </div>
        <?= Utility::enclose_in_error_div($error_message) ?>
      </form>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
