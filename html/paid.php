<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/subscription_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an ordinary user, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_user();
  $current_user = wp_get_current_user();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Activate the subscription.
  $subscription_data = new Subscription_Data_Manager($access_token);
    // *** // Handle error.
  $subscription_data->set_subscription_active_flag(intval($_SESSION['subscription_id']), true);

  // Update the order's payment status to "paid".
  $order_data = new Order_Data_Manager($access_token);
    // *** // Handle error.
  $order_data->set_payment_status(intval($_SESSION['order_id']), 2);

  // Tidy up by removing the subscription ID and order ID from the session.
  unset($_SESSION['subscription_id']);
  unset($_SESSION['order_id']);
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
  </head>
  <body>
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(0, 'Abonnement opprettet')) ?>
    <div class="content">
      <form action="/subscription/html/user_dashboard.php" method="post">
        <div class="form-element">
          <p>
            <?= $text->get(1, 'Tusen takk for bestillingen! Den er n&aring; registrert og betalt, og vi har sendt en e-post med kvittering til: <span class="status-blue">$0</span>. Du vil finne det nye abonnementet i listen p&aring; neste side.', array($current_user->user_email)) ?>
          </p>
        </div>
        <div class="button-container">
          <button type="submit" id="submitButton"><?= $text->get(2, 'Fortsett') ?> <i class="fa-solid fa-caret-right"></i></button>
        </div>
      </form>
    </div>
  </body>
</html>