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
  This file will create payments and charge the buyer for all unpaid orders for all - or a subset of - user groups,
  provided the user group has a valid licence, and the order can be paid automatically. At the moment, only Nets
  payments can be charged automatically. The file will only create payments for orders that cover one particular month.
  This file accepts the following parameters:

    unique_id : string          An identifier that uniquely identifies this round of payments. Mandatory.
    month : string              The month for which payments should be created, in the format "yyyy-mm". month is
                                optional. If present, but invalid, nothing will be done. If not present, payments will
                                be created for the current month, which is also the earliest month for which payments
                                can be created.
    user_group_ids : string     Comma separated list of IDs of user groups for which orders should be created. Only user
                                groups that have an active licence for the Gibbs self storage application will actually
                                have payments created.
    mode : integer              Possible values:
                                  MODE_NORMAL: Create payments and perform logging. This is the default value.
                                  MODE_SIMULATION: Generate the log, but do not actually create the payments. This lets
                                     you see what would happen if payments were created, without actually modifying the
                                     database in any way.

  The file will return a JSON object with the following fields:

    resultCode : integer        The result of the entire operation, using constants from the Result class.
    byUserId : integer          The ID of the user that was used to create the payments.
    startTime : string          The time at which the payment creation was started. Stored in the format returned by the
                                microtime function (a string in the format "msec sec", where sec is the number of
                                seconds since January 1, 1970, and msec is the number of milliseconds since that
                                second).
    endTime : string            The time at which the payment creation finished. Stored in the format returned by the
                                microtime function.
    userGroups : array          Array of objects; one for each user group that was processed. Description below.
    month : string              The month for which payments were created, in the format "yyyy-mm", or an empty string
                                if the month was invalid.

    Each user group object holds the following fields:

    userGroupId : integer       The ID of the user group for which orders are processed.
    userGroupName : string      The name of the user group.
    orders : array              Array of objects; one for each order that was processed. See the documentation
                                for the Order_Data_Manager.create_bulk_payment method.
    resultCode : integer        The result of the payment creation for this user group, using constants from the Result
                                class. Possible values are:
                                  OK                The user group was processed.
                                  LICENCE_EXPIRED   The user group had a licence for the Gibbs self storage application,
                                                    but the licence was not active. No payments were created.

  See the Nets bulk charge documentation at:
    https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-post
    https://developer.nexigroup.com/nexi-checkout/en-EU/docs/track-events-using-webhooks/
    https://developer.nexigroup.com/nexi-checkout/en-EU/api/webhooks/
*/

  // Store timestamp.
  $start_time = microtime();
  $user_groups_log = '[]';
  $month = '';

  // Ensure a unique ID is provided.
  $unique_id = Utility::read_passed_string('unique_id', null);
  if (!empty($unique_id))
  {
    // Log in as a Gibbs administrator. The credentials are found in the config file.
    $config = null;
    $access_token = null;
    $result_code = Result::NO_ACTION_TAKEN;
    Monthly_Payments_Utility::log_in_as_gibbs_admin($config, $access_token, $result_code);

    // If we managed to find an access token, make sure it didn't report any errors.
    if (($config !== null) && ($access_token !== null) && (!$access_token->is_error()))
    {
      // Find the operating mode (simulation or actual).
      $mode = Monthly_Payments_Utility::get_mode();
      // Find the month for which orders should be created.
      $month = Monthly_Payments_Utility::get_selected_month(Utility::get_this_month());
      if ($month !== '')
      {
        // Find the list of user groups to be processed. This is either a subset of, or the full list of, user
        // groups that have an active licence for the Gibbs self storage application.
        $user_groups = Monthly_Payments_Utility::get_user_groups($access_token);
        // Create orders for each user group in turn. Subscriptions are processed for each user group, to ensure we
        // only create orders for user groups with an active licence.
        $user_groups_log = '[';
        if (count($user_groups) > 0)
        {
          foreach ($user_groups as $user_group)
          {
            $user_groups_log .= '{"userGroupId": ' . $user_group->user_group_id . ', "userGroupName": "' .
              $user_group->user_group_name . '", ';
            // Ensure the user group's licence is active.
            if ($user_group->licence_status === 1)
            {
              // Store the user group ID on the session.
              User::set_user_group_id($user_group->user_group_id);
              // Get an access token that allows the current user to act as an administrator for this user group. A
              // Gibbs administrator is entitled to act as an administrator for any user group.
              $user_group_access_token = User::verify_is_admin(false);
              // Create an order data manager for this user group.
              $order_data = new Order_Data_Manager($user_group_access_token);
              // Charge for all unpaid orders for this user group for the given month where the payment method is
              // Nets.
              $user_groups_log .= '"orders": ';
              $user_groups_log .= $order_data->create_bulk_payment($month, $unique_id,
                Config::get_nets_webhook_authorization($config),
                $mode === Monthly_Payments_Utility::MODE_SIMULATION);
              $user_groups_log .= ', "resultCode": ' . Result::OK;
            }
            else
            {
              // This user group does not have an active licence.
              $user_groups_log .= '"resultCode": ' . Result::LICENCE_EXPIRED;
            }
            $user_groups_log .= '},';
          }
          $user_groups_log = Utility::remove_final_comma($user_groups_log);
        }
        $user_groups_log .= ']';
        // Remove the last-used user group ID from the session, just to tidy up.
        User::set_user_group_id(-1);
      }
      else
      {
        // The month was provided, but not valid.
        $result_code = Result::INVALID_MONTH;
      }
    }
  }
  else
  {
    // The unique ID was not supplied.
    $result_code = Result::MISSING_INPUT_FIELD;
  }

  // The get_current_user_id function will return 0 if no user is logged in.
?>
{
  "resultCode": <?= $result_code ?>,
  "byUserId": <?= get_current_user_id() ?>,
  "month": "<?= $month ?>",
  "userGroups": <?= $user_groups_log ?>,
  "startTime": "<?= $start_time ?>",
  "endTime": "<?= microtime() ?>"
}