<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
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
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    // Handle create, update and delete operations.
    $result_code = $location_data->perform_action();
    // Read locations to be displayed to the user.
    $locations = $location_data->read();
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
    <script type="text/javascript" src="/subscription/js/admin_locations.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

<?= Utility::write_initial_sorting() ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var bookingUrl = '<?= Utility::get_booking_url() ?>';

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Lager'), 'fa-location-dot') ?>
    <div class="content">
      <div class="toolbar">
        <button type="button" class="wide-button" onclick="displayEditLocationDialogue(-1);"><i class="fa-solid fa-location-dot"></i> <?= $text->get(1, 'Opprett nytt lager') ?></button>
      </div>
      <div id="locationsBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>

    <div id="bookingUrlDialogue" class="dialogue booking-url-dialogue" style="display: none;">
      <div class="dialogue-header">
        <h1><?= $text->get(2, 'URL for bestilling') ?></h1>
      </div>
      <div class="dialogue-content">
        <div class="form-element">
          <span class="help-text"><?= $text->get(3, 'URL for &aring; la en kunde bestille lagerbod ved dette lageret.') ?></span>
        </div>
        <div class="form-element">
          <input id="bookingUrlEdit" type="text" readonly="readonly" class="url-text" />
          <button type="button" class="icon-button" onclick="Utility.copyToClipboard('bookingUrlEdit');"><i class="fa-solid fa-copy"></i></button>
        </div>
      </div>
      <div class="dialogue-footer">
        <button type="button" onclick="closeBookingUrlDialogue();"><i class="fa-solid fa-xmark"></i> <?= $text->get(4, 'Lukk') ?></button>
      </div>
    </div>

    <div id="editLocationDialogue" class="dialogue edit-location-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
