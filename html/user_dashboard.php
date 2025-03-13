<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/dynamic_styles.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_subscription_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';

  // If the user is not logged in as an ordinary user, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_user();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $product_types = Test_Data_Manager::PRODUCT_TYPES;
    $locations = Test_Data_Manager::LOCATIONS_WITH_ACCESS_CODE;
    $subscriptions = Test_Data_Manager::USER_SUBSCRIPTIONS;
  }
  else
  {
    $product_type_data = new Product_Type_Data_Manager($access_token);
    $location_data = new Location_Data_Manager($access_token);
    $subscription_data = new User_Subscription_Data_Manager($access_token);
    $subscription_data->set_user_id(get_current_user_id());
    $result_code = Result::NO_ACTION_TAKEN;

    // Read product types and locations to be displayed to the user.
    $product_types = $product_type_data->read();
    $locations = $location_data->read();

    // Handle create, update and delete operations.
    $result_code = $subscription_data->perform_action();
    // Read the subscriptions to be displayed to the user.
    $subscriptions = $subscription_data->read();
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/user.css?v=<?= Utility::BUILD_NO ?>" />
    <style>
<?= Dynamic_Styles::get_user_styles($settings) ?>
    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/price_plan/price_plan.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/user_dashboard.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

st.sub.TEXTS = <?= $text->get(1, "['Feil ved bestilling', 'Avsluttet', 'L&oslash;pende', 'Sagt opp', 'Bestilt']") ?>;
var PAYMENT_STATUS_TEXTS = <?= $text->get(4, "['Ukjent', 'Ikke betalt', 'Betalt', 'Delvis betalt', 'Ikke betalt - forfalt', 'Ikke betalt - betalingsp&aring;minnelse sendt', 'Ikke betalt - purring sendt', 'Ikke betalt - sendt til inkasso', 'Betalt - betalt til inkassoselskap', 'Tapt / kan ikke kreves inn', 'Kreditert', 'Feil hos betalingsselskap', 'Teknisk feil ved betaling', 'Betalt - refundert', 'Omstridt krav', 'Faktura ikke sendt', 'Faktura sendt', 'Betaling startet', 'Slettet']") ?>;
var PAYMENT_METHOD_TEXTS = <?= $text->get(5, "['Ukjent', 'Kredittkort', 'Faktura', 'Kort, s&aring; faktura']") ?>;
var UPDATE_PAYMENT_CARD_TEXT = <?= $text->get(36, "'Oppdater betalingskort'") ?>;

var displayExpiredSubscriptions = false;

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var productTypes = <?= $product_types ?>;
var locations = <?= $locations ?>;
var subscriptions = <?= $subscriptions ?>;

    </script>
  </head>
  <body onload="initialise();">
    <div class="content-area">
      <div class="toolbar user-dashboard-toolbar">
        <?= Header::get_header_for_mobile($access_token) ?>
      </div>
      <div class="tab">
        <h1><?= $text->get(0, 'Dine lagerboder') ?></h1>
        <div id="subscriptionsBox" class=""></div>
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;"></div>
    <div id="cancelSubscriptionDialogue" class="dialogue cancel-subscription-dialogue" style="display: none;">
      <div class="dialogue-header">
        <button type="button" class="low-profile close-button" onclick="closeCancelSubscriptionDialogue();"><i class="fa-solid fa-xmark"></i></button>
        <h3><?= $text->get(2, 'Si opp lagerbod?') ?></h3>
      </div>
      <div id="cancelSubscriptionDialogueContent" class="dialogue-content"></div>
      <div class="dialogue-footer">
        <button type="button" onclick="cancelSubscription();"><i class="fa-solid fa-hand-wave"></i>&nbsp;&nbsp;<?= $text->get(3, 'Si opp') ?></button>
      </div>
    </div>
    <div id="paymentHistoryDialogue" class="dialogue payment-history-dialogue" style="display: none;"></div>
  </body>
</html>
