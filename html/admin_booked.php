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
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  $order_id = $_SESSION['order_id'];

  // Tidy up by removing the subscription ID and order ID from the session.
  unset($_SESSION['subscription_id']);
  unset($_SESSION['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/admin.css?v=<?= Utility::BUILD_NO ?>" />
    <style>
<?= Dynamic_Styles::get_user_styles($settings) ?>
    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
  </head>
  <body class="admin">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(3, 'Opprett abonnement'), 'fa-plus') ?>
    <div class="background-area">
      <div class="content-area">
        <div class="toolbar">
          <div class="back-button-box">
            <button type="button" class="back-button" disabled><?= $text->get(0, 'Tilbake') ?></button>
          </div>
          <div id="tabButtonArea" class="tab-button-area">
          </div>
        </div>
        <div id="tab_0" class="tab">
          <div class="area-box confirmation-box">
            <div class="starfield-outer">
              <div class="starfield-inner">
                <i class="star star-1 fa-solid fa-star"></i>
                <i class="star star-2 fa-solid fa-star"></i>
                <i class="star star-3 fa-solid fa-star"></i>
                <i class="star star-4 fa-solid fa-star"></i>
                <i class="star star-5 fa-solid fa-star"></i>
                <i class="star star-6 fa-solid fa-star"></i>
                <i class="check fa-solid fa-circle-check"></i>
              </div>
            </div>
            <div class="separator-box">
              <h2><?= $text->get(1, 'Gratulerer med nok et salg!') ?></h2>
            </div>
            <div class="form-element">
              <p>
                <?= $text->get(2, 'Ordrenummer: <b>$0</b>', array($order_id)) ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>