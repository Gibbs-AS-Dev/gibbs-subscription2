<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/subscription_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

  $payment_id = '';
  // If the user is not logged in as an ordinary user, return an access token with an error code.
  $access_token = User::verify_is_user(false);

  // $order_data = new Order_Data_Manager($access_token);
  // $order_data->create_monthly_payment(37);
  if ($access_token->is_error())
  {
    $result_code = $access_token->get_result_code();
  }
  else
  {
    $settings = Settings_Manager::read_settings($access_token);
    if ($settings->get_use_test_data())
    {
      // Pretend we created a subscription. The pay.php page will display a fake payment image.
      $result_code = Result::OK;
      $payment_id = '0';
    }
    else
    {
      // Create an actual subscription. We do not need to set the user ID. The subscription data manager will use the
      // current user's ID.
      
      $subscription_data = new Subscription_Data_Manager($access_token);
      $result_code = $subscription_data->perform_action();
      $subscription_id = $subscription_data->get_created_item_id();

      // If the subscription was created successfully, also create an order and a payment object.
      if ($result_code === Result::OK)
      {
        $order_data = new Order_Data_Manager($access_token);
        // We do not need to set the user ID. The order data manager will use the current user's ID.
        // We do, however, need to set the ID of the subscription that was just created.
        $order_data->set_subscription_id($subscription_id);

        // Create the order.
        $result_code = $order_data->create_initial_order_from_subscription();
        if ($result_code === Result::OK)
        {
          // The order was created successfully. Contact the payment provider to create a payment based on the order.
          $order_id = $order_data->get_created_item_id();
          $payment_data = $order_data->create_initial_payment();
          $result_code = $order_data::parse_payment_data($payment_data);
          if ($result_code === Result::OK)
          {
            // The payment was created successfully as well. Grab the payment ID, to return to the client. Store it on
            // the order as well, so we can keep charging subsequent months.
            $payment_id = $payment_data->paymentId;
            $result_code = $order_data->store_payment_id($payment_id);
              // *** // What to do if we failed to store the payment ID? Currently we delete everything.
            if ($result_code === Result::OK)
            {
              // Finally, store the new subscription and order on the session. Once payment succeeds, the subscription
              // can be activated and the order's payment status updated.
              $_SESSION['subscription_id'] = strval($subscription_id);
              $_SESSION['order_id'] = strval($order_id);
            }
          }

          if ($result_code !== Result::OK)
          {
            // Some part of the payment process failed. Delete the order that was created earlier.
            $order_data->delete($order_id);
          }
        }
        if ($result_code !== Result::OK)
        {
          // Some part of the payment process failed. Delete the subscription that was created earlier.
          $subscription_data->delete($subscription_id);
        }
      }
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "paymentId": "<?= $payment_id ?>"
}