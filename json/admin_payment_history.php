<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

  $subscription_id = -1;
  $payment_history = '[]';

  // If the user is not logged in as an administrator, return an error code.
  $access_token = User::verify_is_admin(false);
  if ($access_token->is_error())
  {
    $result_code = $access_token->get_result_code();
  }
  else
  {
    // Read the user_id and subscription_id parameters. If not present, return an error.
    if (!Utility::integer_passed('user_id') || !Utility::integer_passed('subscription_id'))
    {
      $result_code = Result::MISSING_INPUT_FIELD;
    }
    else
    {
      $user_id = Utility::read_passed_integer('user_id');
      $subscription_id = Utility::read_passed_integer('subscription_id');

      $settings = Settings_Manager::read_settings($access_token);
      if ($settings->get_use_test_data())
      {
        // Use dummy data. We have two options.
        $result_code = Result::OK;
        if (Utility::is_even($subscription_id))
        {
          $payment_history = Test_Data_Manager::PAYMENT_HISTORY_EVEN;
        }
        else
        {
          $payment_history = Test_Data_Manager::PAYMENT_HISTORY_ODD;
        }
      }
      else
      {
        // Read the payment history from the database. We need to set the user ID; otherwise the data manager will use
        // the currently logged-in user.
        $order_data = new Order_Data_Manager($access_token);
        $order_data->set_user_id($user_id);
        $order_data->set_subscription_id($subscription_id);
        $payment_history = $order_data->read();
        $result_code = Result::OK;
      }
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "userId": <?= $user_id ?>,
  "subscriptionId": <?= $subscription_id ?>,
  "paymentHistory": <?= $payment_history ?>
}