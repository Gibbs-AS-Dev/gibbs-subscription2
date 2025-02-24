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

  // If the user is not logged in as a Gibbs administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_gibbs_admin();

  // See if the user wants to go to booking without logging in.
  if (Utility::string_passed('action'))
  {
    $action = Utility::read_passed_string('action');
    if (($action === 'book_without_login') && Utility::integer_passed('user_group_id'))
    {
      // He does. Log him out, and redirect to booking.
      User::log_out();
      Utility::redirect_to('/subscription/html/select_booking_type.php?user_group_id=' .
        Utility::read_passed_integer('user_group_id'));
    }
  }

  // Read licencees to be displayed to the user.
  $licencee_data = new Licencee_Data_Manager($access_token);
  $licencees = $licencee_data->get_licencees();
  if ($licencees === null)
  {
    $licencees = array();
  }
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
    <script type="text/javascript" src="/subscription/js/gibbs_dashboard.js?v=<?= Utility::BUILD_NO ?>"></script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_gibbs_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, 'Gibbs administrator dashboard', 'fa-house') ?>
    <div class="content">
      <div class="form-element">
        <img src="/subscription/resources/devotion_to_duty.png?v=<?= Utility::BUILD_NO ?>" width="638" height="247" alt="Welcome, sysadmin!" />
        <!--a href="/subscription/html/gibbs_licencees.php">Customers and licences</a><br /-->
        <!--br />
        <form id="connectUserForm" action="/subscription/html/gibbs_dashboard.php" method="post">
          <button type="button" class="wide-button" onclick="connectUser();">Connect user to user group</button>
        </form-->
        <br />
        <br />
        <form action="/subscription/html/gibbs_dashboard.php" method="post">
          <input type="hidden" name="action" value="book_without_login" />
          <label for="userGroupCombo">Book a storage unit without logging in at:</label>
          <select id="userGroupCombo" name="user_group_id" class="long-text">
<?php
  foreach ($licencees as $licencee)
  {
    echo('<option value="' . strval($licencee->user_group_id) . '">' . $licencee->user_group_name . '</option>');
  }
?>
          </select>
          <button type="submit" class="wide-button">Book storage</button>
        </form>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>