<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/licencee_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as a Gibbs administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_gibbs_admin();

  // Read data.
  $licencee_data = new Licencee_Data_Manager($access_token);

  $gibbs_abonnement_licence_id = $licencee_data::get_gibbs_abonnement_licence_id();
  $user_groups = User_Data_Manager::get_user_groups();

  // Handle create, update and delete operations.
  $result_code = $licencee_data->perform_action();
  // Read licencees to be displayed to the user.
  $licencees = $licencee_data->read();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - licences</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/gibbs_licencees.js"></script>
    <script type="text/javascript">

var resultCode = <?= $result_code ?>;
var gibbsAbonnementLicenceId = <?= $gibbs_abonnement_licence_id ?>;
var licencees = <?= $licencees ?>;
var userGroups = <?= $user_groups ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_header_with_user_info('Customers and licences') ?>
    <div class="content">
      <div class="toolbar">
        <button type="button" class="wide-button" onclick="alert('Not implemented');"><i class="fa-solid fa-boxes-stacked"></i> Create customer</button>
        <button type="button" class="wide-button" onclick="displayAddLicenceDialogue();"><i class="fa-solid fa-boxes-stacked"></i> Grant licence</button>
        <div id="filterToolbar" class="filter">
          &nbsp;
        </div>
      </div>
      <div id="licenceesBox">
        &nbsp;
      </div>
    </div>

    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="addLicenceDialogue" class="dialogue add-licence-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editUserGroupDialogue" class="dialogue edit-user-group-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>