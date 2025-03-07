<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/settings_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
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
  $location_data = new Location_Data_Manager($access_token);
  $locations = $location_data->read();
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

  // Read initial tab.
  if (Utility::integer_passed('active_tab'))
  {
    $initial_tab = Utility::read_passed_integer('active_tab');
    if (($initial_tab < 0) || ($initial_tab > 2))
    {
      $initial_tab = 0;
    }
  }
  else
  {
    $initial_tab = 0;
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
    <script type="text/javascript" src="/subscription/js/admin_settings.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var settings = <?= $settings->as_javascript(true) ?>;
var initialTab = <?= $initial_tab ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Innstillinger'), 'fa-gear') ?>
    <div id="tabButtonArea" class="tab-button-area">
      &nbsp;
    </div>
    <div class="content">
      <form id="settingsForm" action="/subscription/html/admin_settings.php" method="post">
        <input type="hidden" name="action" value="update_settings" />
        <div id="tab_0">
          <div id="generalSettingsBox">
            &nbsp;
          </div>
        </div>
        <div id="tab_1">
          <div id="colourSettingsBox">
            &nbsp;
          </div>
        </div>
        <div id="tab_2">
          <div id="emailSettingsBox">
            &nbsp;
          </div>
        </div>
        <div class="button-container">
          <button type="button" id="submitButton" class="wide-button" onclick="Utility.displaySpinnerThenSubmit(settingsForm);">
            <?= $text->get(1, 'Lagre') ?>
          </button>
        </div>
      </form>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="fullModeInfoDialogue" class="dialogue full-mode-info-dialogue" style="display: none;">
      <div class="dialogue-header">
        <h1><?= $text->get(2, 'N&aring;r en bodtype ikke er ledig') ?></h1>
      </div>
      <div class="dialogue-content">
        <h3><?= $text->get(3, 'Vis status og alternativer') ?></h3>
        <div class="form-element">
          <img src="/subscription/resources/full_mode_alternatives.png" width="576" height="239" alt="" /><br />
          <span class="help-text"><?= $text->get(4, 'Brukeren f&aring;r vite at bodtypen ikke er ledig. Hvis det finnes alternativer, vises disse.') ?></span>
        </div>
        <h3><?= $text->get(5, 'Skjul status og send foresp&oslash;rsel') ?></h3>
        <div class="form-element">
          <img src="/subscription/resources/full_mode_request.png" width="576" height="137" alt="" /><br />
          <span class="help-text"><?= $text->get(6, 'Brukeren ser ikke at bodtypen ikke er ledig. N&aring;r han klikker &quot;Velg&quot;, sender han en foresp&oslash;rsel.') ?></span>
        </div>
      </div>
      <div class="dialogue-footer">
        <button type="button" onclick="closeFullModeInfo();"><i class="fa-solid fa-xmark"></i> <?= $text->get(7, 'Lukk') ?></button>
      </div>
    </div>
  </body>
</html>
