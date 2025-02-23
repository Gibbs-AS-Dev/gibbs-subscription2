<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
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
    $capacity_price_rules = Test_Data_Manager::CAPACITY_PRICE_RULES;
    $special_offer_price_rules = Test_Data_Manager::SPECIAL_OFFER_PRICE_RULES;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $product_type_data = new Product_Type_Data_Manager($access_token);

    $locations = $location_data->read();
    $product_types = $product_type_data->read();

      // *** // Read from database.
    $result_code = Result::NO_ACTION_TAKEN;
    $capacity_price_rules = Test_Data_Manager::CAPACITY_PRICE_RULES;
    $special_offer_price_rules = Test_Data_Manager::SPECIAL_OFFER_PRICE_RULES;
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - lager</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js"></script>
    <script type="text/javascript" src="/subscription/js/admin_prices.js"></script>
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var productTypes = <?= $product_types ?>;
var capacityPriceRules = <?= $capacity_price_rules ?>;
var specialOfferPriceRules = <?= $special_offer_price_rules ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_expanding_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(0, 'Priser'), 'fa-square-dollar') ?>
    <div id="tabset" class="tabset">
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

    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editPriceRuleDialogue" class="dialogue edit-price-rule-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
