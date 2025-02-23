<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/settings_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data. Note that the settings are always live. We never use dummy data.
  $settings_data = new Settings_Data_Manager($access_token);
  // Update the settings if required.
  $result_code = $settings_data->perform_action();
  // If the settings were updated, read the updated settings from the database and store them on the session. Otherwise,
  // just read the existing settings from the session.
  if ($result_code === Result::OK)
  {
    $settings = $settings_data->read();
    Settings_Manager::store_settings($settings);
  }
  else
  {
    $settings = Settings_Manager::read_settings($access_token);
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - innstillinger</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/admin_settings.js"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var resultCode = <?= $result_code ?>;
var settings = <?= $settings->as_javascript(true) ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_expanding_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(0, 'Innstillinger'), 'fa-gear') ?>
    <div class="content">
      <div id="settingsBox">
        &nbsp;
      </div>
    </div>
  </body>
</html>
