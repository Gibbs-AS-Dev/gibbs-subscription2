<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/email_sms_log_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $message_log = Test_Data_Manager::MESSAGE_LOG;
  }
  else
  {
    $log_data = new Email_Sms_Log_Data_Manager($access_token);
    // Read message log to be displayed to the user.
    $message_log = $log_data->read();
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
    <script type="text/javascript" src="/subscription/components/sorting/sorting.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/menu/popup_menu.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_email_sms_log.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var MESSAGE_TYPE_TEXTS = <?= $text->get(1, "['SMS', 'E-post']") ?>;
var DELIVERED_TEXTS = <?= $text->get(2, "['Nei', 'Ja']") ?>;

// The current filter on delivered messages, or null if all messages are displayed, regardless of
// whether they were delivered. The filter is an array of integers, containing a zero if
// non-delivered messages should be displayed, and a 1 if delivered messages should be displayed.
var deliveredFilter = <?= Utility::verify_filter('delivered_filter') ?>;

// The current freetext filter, or an empty string if all messages are displayed, regardless of
// what they contain. If a text is supplied, messages will only be displayed if they contain that
// text, as part of either the recipient, product_name, header, content or error_message fields.
var freetextFilter = '<?= Utility::read_passed_string('freetext_filter', '') ?>';

var messageLog = <?= $message_log ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'SMS- og e-postlogg'), 'fa-envelopes') ?>
    <div class="content">
      <div class="toolbar">
        <div id="filterToolbar" class="filter">
          &nbsp;
        </div>
      </div>
      <div id="messageLogBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="contentDialogue" class="dialogue log-content-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editDeliveredFilterDialogue" class="dialogue edit-delivered-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
