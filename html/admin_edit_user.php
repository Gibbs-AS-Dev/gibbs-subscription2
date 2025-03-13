<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_subscription_data_manager.php';
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

  // Read ID parameter. If the parameter does not specify an existing customer, a new customer will be created once the
  // admin has filled in the information. If an existing customer is specified, display information on that customer.
  $user_id = Utility::read_passed_string('user_id', '');
  $is_new_user = $user_id === '';

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($is_new_user)
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $user = User_Data_Manager::get_empty_user();
    $subscriptions = 'null';
  }
  else
  {
    if ($settings->get_use_test_data())
    {
      $result_code = Result::NO_ACTION_TAKEN;
      $user = Test_Data_Manager::USER;
      $subscriptions = Test_Data_Manager::USER_SUBSCRIPTIONS;
    }
    else
    {
      $subscription_data = new User_Subscription_Data_Manager($access_token);
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
        // Cancel subscriptions.
        $result_code = $subscription_data->perform_action();
      }
      // Read user information and subscriptions to be displayed to the user.
      $user = User_Data_Manager::get_user($user_id);
      $subscriptions = $subscription_data->read();
    }
  }

  if ($settings->get_use_test_data())
  {
    $product_types = Test_Data_Manager::PRODUCT_TYPES;
    $locations = Test_Data_Manager::LOCATIONS;
  }
  else
  {
    $product_type_data = new Product_Type_Data_Manager($access_token);
    $location_data = new Location_Data_Manager($access_token);
    // Read product types and locations to be displayed to the user.
    $product_types = $product_type_data->read();
    $locations = $location_data->read();
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
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/menu/popup_menu.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/price_plan/price_plan.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_edit_user.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

st.sub.TEXTS = <?= $text->get(4, "['Feil ved bestilling', 'Avsluttet', 'L&oslash;pende', 'Sagt opp', 'Bestilt']") ?>;
var PAYMENT_STATUS_TEXTS = <?= $text->get(5, "['Ukjent', 'Ikke betalt', 'Betalt', 'Delvis betalt', 'Ikke betalt - forfalt', 'Ikke betalt - betalingsp&aring;minnelse sendt', 'Ikke betalt - purring sendt', 'Ikke betalt - sendt til inkasso', 'Betalt - betalt til inkassoselskap', 'Tapt / kan ikke kreves inn', 'Kreditert', 'Feil hos betalingsselskap', 'Teknisk feil ved betaling', 'Betalt - refundert', 'Omstridt krav', 'Faktura ikke sendt', 'Faktura sendt', 'Betaling startet', 'Slettet']") ?>;
var PAYMENT_METHOD_TEXTS = <?= $text->get(7, "['Ukjent', 'Nets (kort)', 'Faktura', 'Kort, s&aring; faktura']") ?>;
var ADDITIONAL_PRODUCT_TEXTS = <?= $text->get(6, "['', 'forsikring']") ?>;
var DAY_NAMES = <?= $text->get(8, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(9, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(10, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

<?= $text->get_js_strings() ?>

var displayExpiredSubscriptions = true;

<?= Utility::write_initial_sorting() ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var isNewUser = <?= var_export($is_new_user, true) ?>;
var user = <?= $user ?>;
var productTypes = <?= $product_types ?>;
var locations = <?= $locations ?>;
var subscriptions = <?= $subscriptions ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Kundekort'), 'fa-user') ?>
    <div class="content" style="display: flex; gap: 20px;">
      <div id="userInfoBox" style="flex: 1;">
        &nbsp;
      </div>
      <div id="userNotesBox" style="flex: 1; display: <?= $is_new_user ? 'none' : 'block' ?>;">
        <div class="toolbar">
          <h3><?= $text->get(67, 'Notater') ?></h3>
        </div>
        <div class="form-element help-text">
          <?= $text->get(68, 'Deres private notater om denne kunden. Kunden vil ikke få tilgang til disse.') ?>
        </div>
        <div id="userNotesContent">
          <form action="/subscription/json/user_notes.php" method="post" id="notesForm" target="notesTarget" onsubmit="return encodeNotesBeforeSubmit();">
            <input type="hidden" name="action" value="set_user_notes">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <textarea id="inlineUserNotesTextArea" name="user_notes" style="width: 100%; min-height: 200px;"></textarea>
            <input type="hidden" id="encodedUserNotes" name="encoded_user_notes" value="">
            <div class="button-container" style="margin-top: 10px;">
              <button type="submit" id="saveNotesButton">
                <i class="fa-solid fa-check"></i> <?= $text->get(23, 'Lagre') ?>
                <span id="saveNotesSpinner" class="button-spinner" style="display: none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
              </button>
            </div>
          </form>
          <iframe id="notesTarget" name="notesTarget" style="display:none;"></iframe>
        </div>
      </div>
    </div>
    <div id="subscriptionsFrame" class="content">
      <div class="toolbar">
        <h3><?= $text->get(1, 'Abonnementer') ?></h3>
        <br />
        <button type="button" class="button wide-button" onclick="Utility.displaySpinnerThenGoTo('/subscription/html/admin_book_subscription.php?initial_user_id=<?= $user_id ?>');"><i class="fa-solid fa-boxes-stacked"></i> <?= $text->get(2, 'Opprett abonnement') ?></button>
        <div class="filter filter-next-to-buttons">
          <label id="expiredSubscriptionsLine" for="expiredSubscriptionsCheckbox">
            <input id="expiredSubscriptionsCheckbox" type="checkbox" onchange="toggleExpiredSubscriptions();" checked="checked" />
            <?= $text->get(3, 'Vis avsluttede avtaler') ?>
          </label>
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
    <div id="userNotesDialogue" class="dialogue user-notes-dialogue" style="display: none;">
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
  </body>
</html>
