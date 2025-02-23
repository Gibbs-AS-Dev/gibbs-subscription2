<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/role_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $users = Test_Data_Manager::USERS;
  }
  else
  {
    $user_data = new Role_Data_Manager($access_token);
    $user_data->set_role_filter(Utility::ROLE_NUMBER_USER);
    $result_code = Result::NO_ACTION_TAKEN;
    // Read list of users to be displayed.
    $users = $user_data->read();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - brukere</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/admin_users.js"></script>
    <script type="text/javascript">

var resultCode = <?= $result_code ?>;
var users = <?= $users ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_expanding_sidebar() ?>
    <?= Header::get_header_with_user_info('Brukere', 'fa-users') ?>
    <div class="content">
      <div class="toolbar">
        <a href="/subscription/html/admin_edit_user.php" class="button wide-button"><i class="fa-solid fa-user-plus"></i> Opprett bruker</a>
        <div class="filter">
          Vis brukere:
          <input type="checkbox" id="displayActiveUsersCheckbox" onchange="displayUsers();" checked="checked" />
          <label for="displayActiveUsersCheckbox">Med aktive abonnement</label>
          <input type="checkbox" id="displayInactiveUsersCheckbox" onchange="displayUsers();" checked="checked" />
          <label for="displayInactiveUsersCheckbox">Uten aktive abonnement</label>
        </div>
      </div>
      <div id="usersBox">
        &nbsp;
      </div>
    </div>
  </body>
</html>
