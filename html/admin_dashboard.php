<?php
  // global $wpdb;

  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/admin_dashboard.js"></script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_expanding_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(3, 'Gibbs minilager'), 'fa-house') ?>
    <div class="content">
      <div class="form-element">
        <div class="search-box-container">
          <input type="text" placeholder="<?= $text->get(0, 'Finn lagerbod') ?>" class="search-box long-text" />
          <button type="button" class="search-button" onclick="window.location.href = '/subscription/html/admin_products.php';"><i class="fa-solid fa-search"></i></button>
        </div>
        <div class="search-box-container">
          <input type="text" placeholder="<?= $text->get(1, 'Finn bruker') ?>" class="search-box long-text" />
          <button type="button" class="search-button" onclick="window.location.href = '/subscription/html/admin_edit_user.php?user_id=1001';"><i class="fa-solid fa-search"></i></button>
        </div>
      </div>
    </div>
    <div class="content">
      <img src="/subscription/resources/statistics.png" alt="<?= $text->get(2, 'Statistikk') ?>" />
    </div>
  </body>
</html>
