<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/monthly_payments_utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';

/*
  This file will return the status of a Nets bulk payment. It accepts the following parameters:

    bulk_id : string            The bulk ID that identifies the bulk payment. Mandatory.
    user_group_id : integer     The ID of the user group for which the bulk payment was submitted.

  The file will return a JSON object with the following fields:

    resultCode : integer        The result of the entire operation, using constants from the Result class.
    byUserId : integer          The ID of the user that was used to create the payments.
    startTime : string          The time at which the payment creation was started. Stored in the format returned by the
                                microtime function (a string in the format "msec sec", where sec is the number of
                                seconds since January 1, 1970, and msec is the number of milliseconds since that
                                second).
    endTime : string            The time at which the payment creation finished. Stored in the format returned by the
                                microtime function.
    status : object             The information returned by Nets.

  See the Nets bulk charge documentation at:
    https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-bulkid-get
*/

  // Store timestamp.
  $start_time = microtime();
  $status_log = '{}';

  // Ensure a bulk ID is provided.
  $bulk_id = Utility::read_passed_string('bulk_id', null);
  $user_group_id = Utility::read_passed_integer('user_group_id', -1);
  if (!empty($bulk_id) && ($user_group_id >= 0))
  {
    // Log in as a Gibbs administrator. The credentials are found in the config file.
    $config = null;
    $access_token = null;
    $result_code = Result::NO_ACTION_TAKEN;
    Monthly_Payments_Utility::log_in_as_gibbs_admin($config, $access_token, $result_code);

    // If we managed to find an access token, make sure it didn't report any errors.
    if (($config !== null) && ($access_token !== null) && (!$access_token->is_error()))
    {
      // Store the user group ID on the session.
      User::set_user_group_id($user_group_id);
      // Get an access token that allows the current user to act as an administrator for this user group. A
      // Gibbs administrator is entitled to act as an administrator for any user group.
      $user_group_access_token = User::verify_is_admin(false);
      // Create an order data manager for this user group.
      $order_data = new Order_Data_Manager($user_group_access_token);
      // Find the status.
      $status_log = $order_data->get_bulk_payment_status($bulk_id);
      $result_code = Result::OK;
    }
  }
  else
  {
    // The bulk ID or user group ID were not present.
    $result_code = Result::MISSING_INPUT_FIELD;
  }

  // The get_current_user_id function will return 0 if no user is logged in.
?>
{
  "resultCode": <?= $result_code ?>,
  "byUserId": <?= get_current_user_id() ?>,
  "bulkId": "<?= $bulk_id ?>",
  "status": <?= $status_log ?>,
  "startTime": "<?= $start_time ?>",
  "endTime": "<?= microtime() ?>"
}