<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/price_rule_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
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
    $locations = Test_Data_Manager::LOCATIONS_WITH_ACCESS_CODE;
    $product_types = Test_Data_Manager::PRODUCT_TYPES;
    $price_rules = array(
      'capacity' => Test_Data_Manager::CAPACITY_PRICE_RULES,
      'special_offer' => Test_Data_Manager::SPECIAL_OFFER_PRICE_RULES
    );
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $product_type_data = new Product_Type_Data_Manager($access_token);
    $price_rule_data = new Price_Rule_Data_Manager($access_token);

    $locations = $location_data->read();
    $product_types = $product_type_data->read();

    // Handle create, update and delete operations.
    $result_code = $price_rule_data->perform_action();
    // Read insurance products to be displayed to the user.
    $price_rules = $price_rule_data->read();
  }

  // Read initial tab.
  if (Utility::integer_passed('active_tab'))
  {
    $initial_tab = Utility::read_passed_integer('active_tab');
    if (($initial_tab < 0) || ($initial_tab > 1))
    {
      $initial_tab = 0;
    }
  }
  else
  {
    $initial_tab = 0;
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
    <script type="text/javascript" src="/subscription/js/admin_price_rules.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var STATUS_TEXTS = <?= $text->get(2, "['Utl&oslash;pt', 'P&aring;g&aring;ende', 'Ikke startet']") ?>;

<?= Utility::write_initial_sorting(-1, -1, 'sort_on_capacity_ui_column', 'capacity_sort_direction', 'initialCapacityUiColumn', 'initialCapacityDirection') ?>

<?= Utility::write_initial_sorting(-1, -1, 'sort_on_special_offer_ui_column', 'special_offer_sort_direction', 'initialSpecialOfferUiColumn', 'initialSpecialOfferDirection') ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var productTypes = <?= $product_types ?>;
var capacityPriceRules = <?= $price_rules['capacity'] ?>;
var specialOfferPriceRules = <?= $price_rules['special_offer'] ?>;
var initialTab = <?= $initial_tab ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Priser'), 'fa-square-dollar') ?>
    <div id="tabButtonArea" class="tab-button-area">
      &nbsp;
    </div>
    <div class="content">
      <div id="tab_0">
        <div class="toolbar">
          <button type="button" class="wide-button" onclick="editPriceRule(RULE_TYPE_CAPACITY, -1);"><i class="fa-solid fa-location-dot"></i> <?= $text->get(1, 'Opprett prisregel') ?></button>
        </div>
        <div id="capacityPriceRulesBox">
          &nbsp;
        </div>
      </div>

      <div id="tab_1">
        <div class="toolbar">
          <button type="button" class="wide-button" onclick="editPriceRule(RULE_TYPE_SPECIAL_OFFER, -1);"><i class="fa-solid fa-location-dot"></i> <?= $text->get(1, 'Opprett prisregel') ?></button>
        </div>
        <div id="specialOfferPriceRulesBox">
          &nbsp;
        </div>
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editPriceRuleDialogue" class="dialogue edit-price-rule-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
