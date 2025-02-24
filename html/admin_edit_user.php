<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/subscription_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Read ID parameter. If the parameter does not specify an existing customer, a new customer will be created once the
  // admin has filled in the information. If an existing customer is specified, display information on that customer.
  $user_id = Utility::read_passed_string('user_id', '');
  $is_new_user = $user_id === '';

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($is_new_user)
  {
    $result_code = Result::OK;
    $user = 'null';
    $subscriptions = 'null';
  }
  else
  {
    if ($settings->get_use_test_data())
    {
      $result_code = Result::NO_ACTION_TAKEN;
      $user = Test_Data_Manager::USER;
      $subscriptions = Test_Data_Manager::SUBSCRIPTIONS;
    }
    else
    {
      $subscription_data = new Subscription_Data_Manager($access_token);
      $subscription_data->set_user_id($user_id);

      // Handle create, update and delete operations.
      $requested_action = $subscription_data->get_requested_action();
        // *** // Create a new user.
        // *** // Update an existing user.
      if (($requested_action === 'change_password'))
      {
        // Change the user's password. Note that the new password is passed unchanged, so that the
        // change_password method can sanitise and validate it.
        $result_code = User::change_password($user_id, $_POST['new_password']);
      }
      else
      {
        $result_code = $subscription_data->perform_action();
      }
      // Read user information and subscriptions to be displayed to the user.
      $user = User_Data_Manager::get_user($user_id);
      $subscriptions = $subscription_data->read();
    }
  }

  if ($settings->get_use_test_data())
  {
    $locations = Test_Data_Manager::LOCATIONS;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    // Read locations to be displayed to the user.
    $locations = $location_data->read();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - brukerinformasjon og abonnementer</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/components/price_plan/price_plan.js"></script>
    <script type="text/javascript" src="/subscription/js/admin_edit_user.js"></script>
    <script type="text/javascript">

var SUB_TEXTS = ['Avsluttet', 'L&oslash;pende', 'Sagt opp', 'Bestilt'];
/*< ?= $text->get(, "['Avsluttet', 'L&oslash;pende', 'Sagt opp', 'Bestilt']") ? >;*/
var PAYMENT_STATUS_TEXTS = ['Ukjent', 'Ikke betalt', 'Betalt', 'Delvis betalt', 'Ikke betalt - forfalt', 'Ikke betalt - betalingsp&aring;minnelse sendt', 'Ikke betalt - purring sendt', 'Ikke betalt - sendt til inkasso', 'Betalt - betalt til inkassoselskap', 'Tapt / kan ikke kreves inn', 'Kreditert', 'Feil hos betalingsselskap', 'Teknisk feil ved betaling', 'Betalt - refundert', 'Omstridt krav'];
/*< ?= $text->get(4, "['Ukjent', 'Ikke betalt', 'Betalt', 'Delvis betalt', 'Ikke betalt - forfalt', 'Ikke betalt - betalingsp&aring;minnelse sendt', 'Ikke betalt - purring sendt', 'Ikke betalt - sendt til inkasso', 'Betalt - betalt til inkassoselskap', 'Tapt / kan ikke kreves inn', 'Kreditert', 'Feil hos betalingsselskap', 'Teknisk feil ved betaling', 'Betalt - refundert', 'Omstridt krav']") ? >;*/
var ADDITIONAL_PRODUCT_TEXTS = ['', 'forsikring'];
/*< ?= $text->get(5, "['', 'forsikring']") ?>;*/

var displayExpiredSubscriptions = true;

var resultCode = <?= $result_code ?>;
var isNewUser = <?= var_export($is_new_user, true) ?>;
var user = <?= $user ?>;
var locations = <?= $locations ?>;
var subscriptions = <?= $subscriptions ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_expanding_sidebar() ?>
    <?= Header::get_header_with_user_info('Brukerinformasjon og abonnementer', 'fa-repeat') ?>
    <div class="content">
      <div id="userInfoBox">
        &nbsp;
      </div>
    </div>
    <div id="subscriptionsFrame" class="content">
      <div class="toolbar">
        <h3>Brukerens abonnementer</h3>
        <br />
        <a href="/subscription/html/book_subscription.php" class="button wide-button"><i class="fa-solid fa-boxes-stacked"></i> Bestill lagerbod</a>
        <div class="filter">
          <label id="expiredSubscriptionsLine" for="expiredSubscriptionsCheckbox">
            <input id="expiredSubscriptionsCheckbox" type="checkbox" onchange="toggleExpiredSubscriptions();" checked="checked" />
            Vis avsluttede avtaler
          </label>
        </div>
      </div>
      <div id="subscriptionsBox">
        &nbsp;
      </div>
    </div>

    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="pricePlanDialogue" class="dialogue price-plan-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="paymentHistoryDialogue" class="dialogue payment-history-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>

