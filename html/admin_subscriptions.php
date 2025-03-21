<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/all_subscription_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_plan_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $product_types = Test_Data_Manager::PRODUCT_TYPES;
    $locations = Test_Data_Manager::LOCATIONS;
    $subscriptions = Test_Data_Manager::ALL_SUBSCRIPTIONS;
    $subscription_users = Test_Data_Manager::REQUEST_USERS;
  }
  else
  {
    $product_type_data = new Product_Type_Data_Manager($access_token);
    $location_data = new Location_Data_Manager($access_token);
    $subscription_data = new All_Subscription_Data_Manager($access_token);
    // Read product types and locations to be displayed to the user.
    $product_types = $product_type_data->read();
    $locations = $location_data->read();
    // Handle create, update and delete operations.
    $result_code = $subscription_data->perform_action();
    // Read subscription to be displayed to the user.
    $subscriptions = $subscription_data->read();
    $subscription_users = $subscription_data->read_users();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/common.css?v=<?= Utility::BUILD_NO ?>" />
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/sorting/sorting.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/menu/popup_menu.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/price_plan/price_plan.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_subscriptions.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

st.sub.TEXTS = <?= $text->get(0, "['Feil ved bestilling', 'Avsluttet', 'L&oslash;pende', 'Sagt opp', 'Bestilt']") ?>;
var PAYMENT_STATUS_TEXTS = <?= $text->get(1, "['Ukjent', 'Ikke betalt', 'Betalt', 'Delvis betalt', 'Ikke betalt - forfalt', 'Ikke betalt - betalingsp&aring;minnelse sendt', 'Ikke betalt - purring sendt', 'Ikke betalt - sendt til inkasso', 'Betalt - betalt til inkassoselskap', 'Tapt / kan ikke kreves inn', 'Kreditert', 'Feil hos betalingsselskap', 'Teknisk feil ved betaling', 'Betalt - refundert', 'Omstridt krav', 'Faktura ikke sendt', 'Faktura sendt', 'Betaling startet', 'Slettet']") ?>;
var PAYMENT_METHOD_TEXTS = <?= $text->get(4, "['Ukjent', 'Nets (kort)', 'Faktura', 'Kort, s&aring; faktura']") ?>;
var ADDITIONAL_PRODUCT_TEXTS = <?= $text->get(2, "['', 'forsikring']") ?>;
var DAY_NAMES = <?= $text->get(5, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(6, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(7, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

<?= $text->get_js_strings() ?>

// The current location filter, or null if all subscriptions are displayed, regardless of location.
// The filter is an array of integers, containing IDs of locations that should be displayed.
var locationFilter = <?= Utility::verify_filter('location_filter') ?>;

// The current product type filter, or null if all subscriptions are displayed, regardless of
// product type. The filter is an array of integers, containing IDs of product types that should be
// displayed.
var productTypeFilter = <?= Utility::verify_filter('product_type_filter') ?>;

// The current status filter, or null if all subscriptions are displayed, regardless of status. The
// filter is an array of integers, containing statuses that should be displayed.
var statusFilter = <?= Utility::verify_filter('status_filter') ?>;

// The current freetext filter, or an empty string if all subscriptions are displayed, regardless of
// what they contain. If a text is supplied, subscriptions will only be displayed if they contain
// that text, as part of either the buyer's name, location name, product name, product type name,
// status, start date, end date or insurance name fields.
var freetextFilter = '<?= Utility::read_passed_string('freetext_filter', '') ?>';

<?= Utility::write_initial_sorting() ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var productTypes = <?= $product_types ?>;
var locations = <?= $locations ?>;
var subscriptions = <?= $subscriptions ?>;
var subscriptionUsers = <?= $subscription_users ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(3, 'Abonnementer'), 'fa-repeat') ?>
    <div class="content">
      <div class="toolbar">
        <button type="button" class="wide-button" onclick="window.location.href = '/subscription/html/admin_book_subscription.php';"><i class="fa-solid fa-plus"></i> <?= $text->get(10, 'Opprett abonnement') ?></button>
        <div id="filterToolbar" class="filter filter-next-to-buttons">
          &nbsp;
        </div>
      </div>
      <div id="subscriptionsBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="pricePlanDialogue" class="dialogue price-plan-dialogue-admin" style="display: none;">
      &nbsp;
    </div>
    <div id="paymentHistoryDialogue" class="dialogue payment-history-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="cancelSubscriptionDialogue" class="dialogue cancel-subscription-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editPricePlanDialogue" class="dialogue edit-price-plan-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editPricePlanDateDialogue" class="dialogue edit-price-plan-date-dialogue" style="display: none;">
      <div class="dialogue-header">
        <h1><?= $text->get(9, "Velg startdato for prisendring") ?></h1>
      </div>
      <div class="dialogue-content">
        <div class="form-element">
          <div id="editPricePlanDateDialogueContent" class="calendar-box">
            &nbsp;
          </div>
        </div>
      </div>
      <div class="dialogue-footer">
        <button type="button" onclick="closeEditPricePlanDateDialogue();"><i class="fa-solid fa-xmark"></i> <?= $text->get(8, "Avbryt") ?></button>
      </div>
    </div>
    <div id="editLocationFilterDialogue" class="dialogue edit-location-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editProductTypeFilterDialogue" class="dialogue edit-product-type-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editStatusFilterDialogue" class="dialogue edit-status-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
