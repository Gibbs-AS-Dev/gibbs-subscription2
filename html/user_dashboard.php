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

  // If the user is not logged in as an ordinary user, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_user();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $locations = Test_Data_Manager::LOCATIONS_WITH_ACCESS_CODE;
    $subscriptions = Test_Data_Manager::SUBSCRIPTIONS;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $subscription_data = new Subscription_Data_Manager($access_token);
    $subscription_data->set_user_id(get_current_user_id());
    $result_code = Result::NO_ACTION_TAKEN;

      // *** // Include access codes, but make sure to not include it unless the user has at least one active subscription in that location.
      // Note: $locations = Test_Data_Manager::LOCATIONS_WITH_ACCESS_CODE;
    $locations = $location_data->read();

    // Handle create, update and delete operations.
    $result_code = $subscription_data->perform_action();
    // Read the subscriptions to be displayed to the user.
    $subscriptions = $subscription_data->read();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/components/price_plan/price_plan.js"></script>
    <script type="text/javascript" src="/subscription/js/user_dashboard.js"></script>
    <script type="text/javascript" src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script type="text/javascript" src="/subscription/components/gibbs_leaflet_map/gibbs_leaflet_map.js"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var SUB_TEXTS = <?= $text->get(3, "['Avsluttet', 'L&oslash;pende', 'Sagt opp', 'Bestilt']") ?>;
var PAYMENT_STATUS_TEXTS = <?= $text->get(4, "['Ukjent', 'Ikke betalt', 'Betalt', 'Delvis betalt', 'Ikke betalt - forfalt', 'Ikke betalt - betalingsp&aring;minnelse sendt', 'Ikke betalt - purring sendt', 'Ikke betalt - sendt til inkasso', 'Betalt - betalt til inkassoselskap', 'Tapt / kan ikke kreves inn', 'Kreditert', 'Feil hos betalingsselskap', 'Teknisk feil ved betaling', 'Betalt - refundert', 'Omstridt krav']") ?>;
var ADDITIONAL_PRODUCT_TEXTS = <?= $text->get(5, "['', 'forsikring']") ?>;

var displayExpiredSubscriptions = false;

var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var subscriptions = <?= $subscriptions ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(0, 'Dine lagerboder')) ?>
    <div class="content">
      <div class="toolbar">
        <a href="/subscription/html/book_subscription.php" class="button wide-button"><i class="fa-solid fa-boxes-stacked"></i> <?= $text->get(1, 'Bestill lagerbod') ?></a>
        <div class="filter">
          <label id="expiredSubscriptionsLine" for="expiredSubscriptionsCheckbox">
            <input id="expiredSubscriptionsCheckbox" type="checkbox" onchange="toggleExpiredSubscriptions();" />
            <?= $text->get(2, 'Vis avsluttede avtaler') ?>
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
