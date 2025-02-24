<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/config.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/monthly_payments_utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as a Gibbs administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_gibbs_admin();

  // Read the config file with user information. Get the user name and password to access the webhooks folder.
  $config = Config::read_config_file();
  if ($config !== null)
  {
    $user_name = Config::get_nets_webhook_user_name($config);
    $password = Config::get_nets_webhook_password($config);
  }
  else
  {
    $user_name = '';
    $password = '';
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
    <script type="text/javascript" src="/subscription/js/gibbs_monthly_orders.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

// Mode constants.
var MODE_NORMAL = <?= Monthly_Payments_Utility::MODE_NORMAL ?>;
var MODE_SIMULATION = <?= Monthly_Payments_Utility::MODE_SIMULATION ?>;

// Credentials.
var userName = '<?= $user_name ?>';
var password = '<?= $password ?>';

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_gibbs_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, 'Monthly orders', 'fa-file-invoice-dollar') ?>
    <div class="content">
      <div class="toolbar">
        <label for="monthEdit">Month:
          <input type="text" id="monthEdit" value="<?= Utility::get_this_month() ?>" />
        </label>
        <label for="userGroupIdEdit">User groups:
          <input type="text" id="userGroupIdEdit" value="" />
        </label>
        <label for="simulationCheckbox">
          <input type="checkbox" id="simulationCheckbox" checked="true" /> Simulated
        </label>
        <button type="button" class="wide-button" onclick="createMonthlyOrders();"><i class="fa-solid fa-file-invoice-dollar"></i> Create orders</button>
        <br />
        <p class="help-text">
          The month must be in the format &quot;yyyy-mm&quot;, and must be this month or later. The user groups should
          be a comma separated list of IDs of user groups to include. Leave blank to process all user groups with a
          valid licence.
        </p>
      </div>
      <div id="resultLogBox" class="log-box">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>