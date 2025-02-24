<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/config.php';
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
    <script type="text/javascript" src="/subscription/js/gibbs_test_webhook_access.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

// Credentials.
var userName = '<?= $user_name ?>';
var password = '<?= $password ?>';

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_gibbs_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, 'Test webhook access', 'fa-check-double') ?>
    <div class="content">
      <div class="toolbar">
        <button type="button" class="wide-button" onclick="testWebhookAccess();"><i class="fa-solid fa-check-double"></i> Test webhook access</button>
      </div>
      <div id="resultLogBox" class="log-box">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>