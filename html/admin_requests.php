<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/request_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/category_data_manager.php';
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
    $result_code = Result::NO_ACTION_TAKEN;
    $locations = Test_Data_Manager::LOCATIONS;
    $categories = Test_Data_Manager::CATEGORIES;
    $requests = Test_Data_Manager::REQUESTS;
    $request_users = Test_Data_Manager::REQUEST_USERS;
  }
  else
  {
    $request_data = new Request_Data_Manager($access_token);
    $location_data = new Location_Data_Manager($access_token);
    $category_data = new Category_Data_Manager($access_token);

    // Read locations and categories that may be referenced by requests.
    $locations = $location_data->read();
    $categories = $category_data->read();

    // Handle create, update and delete operations.
    $result_code = $request_data->perform_action();
    // Read requests to be displayed to the user, and the list of users that submitted them.
    $requests = $request_data->read();
    $request_users = $request_data->read_users();
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
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_requests.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var REQUEST_STATUS_TEXTS = <?= $text->get(1, "['Mottatt', 'Trenger info', 'Venter p&aring; info', 'Lag tilbud', 'Venter p&aring; salg', 'Solgt', 'Ikke solgt', 'Ikke kvalifisert']") ?>;
var DAY_NAMES = <?= $text->get(2, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(3, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(4, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

// The current status filter, or null if all requests are displayed, regardless of status. The
// filter is an array of integers, containing statuses that should be displayed.
var statusFilter = <?= Utility::verify_filter('status_filter') ?>;

// The current freetext filter, or an empty string if all requests are displayed, regardless of
// what they contain. If a text is supplied, requests will only be displayed if they contain that
// text, as part of either the comment, user name, location name, category name or start date
// fields.
var freetextFilter = '<?= Utility::read_passed_string('freetext_filter', '') ?>';

<?= Utility::write_initial_sorting() ?>

var settings = <?= $settings->as_javascript() ?>;
var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var categories = <?= $categories ?>;
var requests = <?= $requests ?>;
var requestUsers = <?= $request_users ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Foresp&oslash;rsler'), 'fa-message-question') ?>
    <div class="content">
      <div class="toolbar">
        <div id="filterToolbar" class="filter">
          &nbsp;
        </div>
      </div>
      <div id="requestsBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editRequestDialogue" class="dialogue edit-request-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="userNotesDialogue" class="dialogue user-notes-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editStatusFilterDialogue" class="dialogue edit-status-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
