<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_subscription_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

/*
  The actions taken depend on the current state of the user for which a subscription is being created:

    E-mail in use | Logged in       | Has role        | Action
    --------------+-----------------+-----------------+-----------------------------------------------------------------
    No            | No (impossible) | No (impossible) | Return error.
    Yes           | No              | No              | Return error.
    Yes           | No              | Yes             | Return error.
    Yes           | Yes             | No              | Grant user role. Create subscription, order and payment.
    Yes           | Yes             | Yes (user)      | Create subscription, order and (if the payment method dictates)
                  |                 |                 | payment for the currently logged-in user.
    Yes           | Yes             | Yes (admin)     | Create subscription and order for a buyer with the specified ID.
*/

  $payment_method = Utility::PAYMENT_METHOD_UNKNOWN;
  $payment_id = '0';
  // The user needs to be logged in, in order to create a subscription. However, he does not need to have a role in the
  // current user group (and as a result, we cannot use verify_is_user here). That role will be added below, if missing.
  //
  // Verify that the user is logged in, and that there is a user group ID on the session. We will verify later that the
  // user group has an active licence. The access token will have ROLE_NONE, as we have not yet checked which role the
  // user has in the user group, if any.
  $access_token = User::verify_logged_in(false);
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
    }
    else
    {
      // Find the buyer_id. If a buyer_id was submitted, the subscription will be assigned to that user. Otherwise, it
      // will belong to the currently logged-in user.
      $as_admin = Utility::integer_posted('buyer_id');
      if ($as_admin)
      {
        $buyer_id = Utility::read_posted_integer('buyer_id');
        // Only an administrator can assign a subscription to a user that is not logged in. See if the user is an
        // administrator in the current user group. If so, replace the access token with one that has the correct role.
        // If the user group did not have an active licence, the access token will hold the result code LICENCE_EXPIRED.
        $access_token = User::verify_is_admin(false);
      }
      else
      {
        // Setting the buyer_id to -1 will assign the subscription to the currently logged-in user.
        $buyer_id = -1;
        // See if the user has a role in the current user group. If not, add the user role. Replace the access token
        // with one that has the correct role. If the user group did not have an active licence, the access token will
        // hold the result code LICENCE_EXPIRED.
        $access_token = User::ensure_is_user();
      }
      
      // If the user is not correct, report the error to the client and take no further action.
      if ($access_token->is_error())
      {
       $result_code = $access_token->get_result_code();
      }
      else
      {
        // Determine the payment method for this subscription. When an admin books a subscription on behalf of a buyer,
        // PAYMENT_METHOD_INVOICE is the only valid option, so use that. Otherwise, if the payment method was not
        // determined by the settings, see if the user has selected one.
        $result_code = Result::OK;
        if ($as_admin)
        {
          $payment_method = Utility::PAYMENT_METHOD_INVOICE;
        }
        else
        {
          // Read potential payment methods from settings. These depend on the type of user.
          $payment_methods = $settings->get_payment_methods_for_entity_type(User::get_entity_type());
          if (count($payment_methods) < 1)
          {
            // There are no payment methods. This reveals a fundamental and traumatising flaw in the fabric of the
            // universe. Compensate by sending an invoice.
            $payment_method = Utility::PAYMENT_METHOD_INVOICE;
            error_log('No payment method found in create_subscription.php. This should never happen.');
          }
          elseif (count($payment_methods) === 1)
          {
            // There is only one possible payment method. I guess we'll use that one, then.
            $payment_method = $payment_methods[0];
          }
          else
          {
            // The user can choose the payment method. Verify that a valid choice was submitted.
            $payment_method = Utility::read_posted_integer('payment_method', Utility::PAYMENT_METHOD_UNKNOWN);
            if (Utility::is_valid_payment_method($payment_method))
            {
              if (!in_array($payment_method, $payment_methods))
              {
                $result_code === Result::INVALID_PAYMENT_METHOD;
              }
            }
            else
            {
              $result_code === Result::MISSING_INPUT_FIELD;
            }
          }
        }

        if ($result_code === Result::OK)
        {
          // Create an actual subscription.
          $subscription_data = new User_Subscription_Data_Manager($access_token);
          // For a user, the buyer ID is -1, which means the subscription data manager will use the current user's ID.
          // An administrator, on the other hand, must specify who the subscription is for by passing a valid buyer ID.
          $subscription_data->set_user_id($buyer_id);
          // If the user will pay by invoice, the subscription will be active when created. Otherwise, we will activate
          // it once payment is complete.
          $subscription_data->set_create_active_subscription($payment_method === Utility::PAYMENT_METHOD_INVOICE);
          $result_code = $subscription_data->perform_action();
          $subscription_id = $subscription_data->get_created_item_id();

          // If the subscription was created successfully, also create an order for the initial payment.
          if ($result_code === Result::OK)
          {
            $order_data = new Order_Data_Manager($access_token);
            // Store the ID of the subscription that was just created.
            $order_data->set_subscription_id($subscription_id);

            // Create the order using the selected payment method.
            $result_code = $order_data->create_initial_order_from_subscription($payment_method);
            $order_id = $order_data->get_created_item_id();
            // If the order was created successfully, and the user is paying with Nets, also contact the payment
            // provider to create a payment, and possibly a subscription, based on the order.
            if (($result_code === Result::OK) && (($payment_method === Utility::PAYMENT_METHOD_NETS) ||
              ($payment_method === Utility::PAYMENT_METHOD_NETS_THEN_INVOICE)))
            {
              $payment_data = $order_data->create_initial_payment();
              $result_code = $order_data::verify_payment_data($payment_data);
              if ($result_code === Result::OK)
              {
                // The payment was created successfully as well. Grab the payment ID, to return to the client. It is
                // needed to complete the payment.
                $payment_id = $payment_data->paymentId;

                // If we started a Nets subscription (instead of just doing a one-time payment), use the payment ID to
                // find the Nets subscription ID.
                if ($payment_method === Utility::PAYMENT_METHOD_NETS)
                {
                  $nets_subscription_id = null;
                  $result_code_2 = $order_data->read_nets_subscription_id($nets_subscription_id, $payment_id,
                    $order_id);
                  if ($result_code_2 === Result::OK)
                  {
                    // The Nets subscription ID was found. Store both the Nets subscription ID and payment ID on the
                    // order, so we can keep charging the customer for subsequent months. Once payment succeeds (which
                    // happens later), the subscription will be activated and the order's payment status updated.
                    $result_code_2 = $order_data->store_subscription_id($nets_subscription_id, $payment_id, $order_id);
                    if ($result_code_2 === Result::OK)
                    {
                      // The Nets subscription ID and payment ID could not be stored. Note the fact in the server log, but
                      // allow the subscription to be activated normally (provided the payment succeeds). Once the initial
                      // payment is expended, and it is time to charge the customer, we can ask for the Nets subscription
                      // ID again.
                      error_log('Error while creating subscription: failed to store Nets subscription ID and payment ID. Result code: ' .
                        $result_code_2);
                    }
                  }
                  else
                  {
                    // The Nets subscription ID was not found. Note the fact in the server log, but allow the subscription
                    // to be activated normally (provided the payment succeeds). Once the initial payment is expended, and
                    // it is time to charge the customer, we can ask for the Nets subscription ID again.
                    error_log('Error while creating subscription: failed to get Nets subscription ID. Result code: ' .
                      $result_code_2);
                  }
                }
              }

              if ($result_code !== Result::OK)
              {
                // Some part of the payment process failed. Delete the order that was created earlier.
                $order_data->delete($order_id);
              }
            }

            if ($result_code === Result::OK)
            {
              // Everything succeeded. Store the new subscription and order on the session.
              $_SESSION['subscription_id'] = strval($subscription_id);
              $_SESSION['order_id'] = strval($order_id);
            }
            else
            {
              // Some part of the payment process failed. Delete the subscription that was created earlier.
              $subscription_data->delete($subscription_id);
            }
          }
        }
      }
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "paymentMethod": <?= $payment_method ?>,
  "paymentId": "<?= $payment_id ?>"
}