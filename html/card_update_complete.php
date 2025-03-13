<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/dynamic_styles.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';

  // If the user is not logged in as an ordinary user, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_user();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read settings
  $settings = Settings_Manager::read_settings($access_token);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/user.css?v=<?= Utility::BUILD_NO ?>" />
    <style>
<?= Dynamic_Styles::get_user_styles($settings) ?>
    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">
      function goToDashboard() {
        Utility.displaySpinnerThenGoTo('/subscription/html/user_dashboard.php');
      }
    </script>
  </head>
  <body>
    <div class="content-area">
      <div class="toolbar">
        <?= Header::get_header_for_mobile($access_token) ?>
      </div>
      <div class="tab">
        <h1><?= $text->get(0, 'Betalingskort oppdatert') ?></h1>
        <div class="button-box">
          <div class="form-element">
            <?= $text->get(1, 'Ditt betalingskort har blitt oppdatert. Fremtidige betalinger vil bli trukket fra det nye kortet.') ?>
          </div>
          <div class="form-element center">
            <button type="button" class="wide-button" onclick="goToDashboard();"><?= $text->get(2, 'Tilbake til oversikten') ?></button>
          </div>
        </div>
      </div>
    </div>
    <?= Utility::get_spinner() ?>
  </body>
</html> 