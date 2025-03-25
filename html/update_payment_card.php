<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/dynamic_styles.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_subscription_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';

  // If the user is not logged in as an ordinary user, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_user();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read settings
  $settings = Settings_Manager::read_settings($access_token);

  // Check if we have the required parameters
  if (!isset($_GET['subscription_id']) || !isset($_GET['nets_subscription_id'])) {
    // Redirect back to dashboard with error
    header('Location: /subscription/html/user_dashboard.php?error=missing_parameters');
    exit;
  }

  $subscription_id = $_GET['subscription_id'];
  $nets_subscription_id = $_GET['nets_subscription_id'];

  // Verify that the subscription belongs to the current user
  $subscription_data = new User_Subscription_Data_Manager($access_token);
  $subscription_data->set_user_id(get_current_user_id());
  $subscription = $subscription_data->get_subscription($subscription_id);

  if (!$subscription || $subscription->nets_subscription_id !== $nets_subscription_id) {
    // Subscription not found or mismatch with nets_subscription_id
    header('Location: /subscription/html/user_dashboard.php?error=invalid_subscription');
    exit;
  }

  // Create a dummy order with zero amount for card update
  $order_data = new Order_Data_Manager($access_token);
  $payment_data = $order_data->create_payment_for_card_update($nets_subscription_id);

  if (!$payment_data || !isset($payment_data->hostedPaymentPageUrl)) {
    // Failed to create payment for card update
    error_log('Failed to create payment for card update: ' . print_r($payment_data, true));
    header('Location: /subscription/html/user_dashboard.php?error=payment_creation_failed');
    exit;
  }

  // Redirect to Nets hosted payment page
  header('Location: ' . $payment_data->hostedPaymentPageUrl);
  exit;
?> 