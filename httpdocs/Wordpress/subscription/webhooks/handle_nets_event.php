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
  Process a Nets event. This file will be called by Nets.

  At the moment, this file listens to the following events:
    payment.charge.created.v2     Charge created. The file will identify the order with the given charge ID, and update
                                  the payment status for that order to reflect the amount paid.
    payment.charge.failed         Charge failed. The file will identify the order with the given charge ID, and update
                                  the payment status for that order to reflect the failure.

  Sample data structure for the "charge created" event:
  {
      "id": "01ee00006091b2196937598058c4e488",
      "timestamp": "2021-05-04T22:44:10.1185+02:00",
      "merchantNumber": 100017120,
      "event": "payment.charge.created.v2",
      "data": {
          "chargeId": "01ee00006091b2196937598058c4e488",
          "orderItems": [
              {
                  "reference": "Sneaky NE2816-82",
                  "name": "Sneaky",
                  "quantity": 2,
                  "unit": "pcs",
                  "unitPrice": 2500,
                  "taxRate": 1000,
                  "taxAmount": 500,
                  "netTotalAmount": 5000,
                  "grossTotalAmount": 5500
              }
          ],
          "paymentMethod": "Visa",
          "paymentType": "CARD",
          "amount": {
              "amount": 5500,
              "currency": "SEK"
          },
          "paymentId": "025400006091b1ef6937598058c4e487"
      }
  }

  See Nets documentation at:
    https://developer.nexigroup.com/nexi-checkout/en-EU/api/webhooks/#charge-created
    https://developer.nexigroup.com/nexi-checkout/en-EU/api/webhooks/#charge-failed
*/

  // Log in as a Gibbs administrator. The credentials are found in the config file.
  $config = null;
  $access_token = null;
  $result_code = Result::NO_ACTION_TAKEN;
  Monthly_Payments_Utility::log_in_as_gibbs_admin($config, $access_token, $result_code);

  // If we managed to find an access token, make sure it didn't report any errors.
  if (($config !== null) && ($access_token !== null) && (!$access_token->is_error()))
  {
    // Parse request contents.
    $json = file_get_contents('php://input');
    $payload = json_decode($json, true);
    if (json_last_error() === JSON_ERROR_NONE)
    {
      $name = $payload['event'];
      $data = $payload['data'];
      if (!empty($data))
      {
        if ($name === 'payment.charge.created.v2')
        {
          // Process the charge created event. Attempt to find the order which was paid, using the payment ID or, if
          // that did not work, the charge ID.
          $order_data = new Order_Data_Manager($access_token);
          $order = $order_data->read_order_with_payment_id($data['paymentId']);
          if ($order === null)
          {
            $order = $order_data->read_order_with_charge_id($data['chargeId']);
          }
          if ($order === null)
          {
            // We couldn't find the order. This might be because we haven't stored the payment ID or charge ID in the
            // database yet. Return HTTP 406 Not acceptable. This will cause Nets to retry the webhook call again later,
            // hopefully with better results.
            error_log('Nets webhook error (charge created): Order with payment ID ' . $data['paymentId'] .
              ', charge ID ' . $data['chargeId'] . ' not found.');
            http_response_code(406);
            exit;
          }

          // We found the order. Verify the amount.
          $total = $order_data::get_total_amount($order);
          $paid_amount = $data['amount']['amount'];
            // *** // Deal with currency.
            // *** // Is the nets amount multiplied by 100 on both numbers?
          if ($paid_amount > $total)
          {
            error_log('Nets webhook warning (charge created): Order with ID ' . $order['id'] .
              ' was charged more than the order total (Charged: ' . $paid_amount . '; order total: ' . $total . ').');
            $new_status = Utility::PAYMENT_STATUS_PAID;
          }
          elseif ($paid_amount < $total)
          {
            error_log('Nets webhook warning (charge created): Order with ID ' . $order['id'] .
              ' was charged less than the order total (Charged: ' . $paid_amount . '; order total: ' . $total . ').');
            $new_status = Utility::PAYMENT_STATUS_PARTIALLY_PAID;
          }
          else
          {
            // The amount was exactly right.
            $new_status = Utility::PAYMENT_STATUS_PAID;
          }
          
          // Update the payment status.
          $result_code = $order_data->set_payment_status($order['id'], $new_status);
          if ($result_code === Result::OK)
          {
            // The update succeeded.
            http_response_code(200);
            exit;
          }
          // The update failed, for whatever reason. Return HTTP 500 Internal server error.
          error_log('Nets webhook error (charge created): Failed to update payment status. Result code: ' . $result_code);
          http_response_code(500);
          exit;
        }
        elseif ($name === 'payment.charge.failed')
        {
          // Process the charge failed event. Attempt to find the order which was not paid, using the payment ID or, if
          // that did not work, the charge ID.
          $order_data = new Order_Data_Manager($access_token);
          $order = $order_data->read_order_with_payment_id($data['paymentId']);
          if ($order === null)
          {
            $order = $order_data->read_order_with_charge_id($data['chargeId']);
          }
          if ($order === null)
          {
            // We couldn't find the order. This might be because we haven't stored the payment ID or charge ID in the
            // database yet. Return HTTP 406 Not acceptable. This will cause Nets to retry the webhook call again later,
            // hopefully with better results.
            error_log('Nets webhook error (charge failed): Order with payment ID ' . $data['paymentId'] .
              ', charge ID ' . $data['chargeId'] . ' not found.');
            http_response_code(406);
            exit;
          }

          // We found the order. Update the payment status.
          $result_code = $order_data->set_payment_status($order['id'], Utility::PAYMENT_STATUS_FAILED_AT_PROVIDER);
          if ($result_code === Result::OK)
          {
            // The update succeeded.
            http_response_code(200);
            exit;
          }
          // The update failed, for whatever reason. Return HTTP 500 Internal server error.
          error_log('Nets webhook error (charge failed): Failed to update payment status. Result code: ' . $result_code);
          http_response_code(500);
          exit;
        }
        else
        {
          // We didn't recognise the event name. Return HTTP 405 Method not allowed.
          error_log('Nets webhook error: Unsupported event name.');
          http_response_code(405);
          exit;
        }
      }
      else
      {
        // We parsed the request content, but the data field was empty. Return HTTP 400 Bad request.
        error_log('Nets webhook error: Request contained no data.');
        http_response_code(400);
        exit;
      }
    }
    else
    {
      // We couldn't parse the request content. Return HTTP 400 Bad request.
      error_log('Nets webhook error: Unable to parse request content: ' . json_last_error_msg());
      http_response_code(400);
      exit;
    }
  }
  // We couldn't log in as a Gibbs administrator, or some other error occurred that caused us to arrive here. Return
  // HTTP 500 Internal server error.
  error_log('Nets webhook error: Unable to log in, or other error occurred.');
  http_response_code(500);
  exit;
?>