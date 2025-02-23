<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/category_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_data_manager.php';
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
    $locations = Test_Data_Manager::LOCATIONS;
    $categories = Test_Data_Manager::CATEGORIES;
    $product_types = Test_Data_Manager::PRODUCT_TYPES;
    $products = Test_Data_Manager::PRODUCTS;
  }
  else
  {
    $category_data = new Category_Data_Manager($access_token);
    $product_type_data = new Product_Type_Data_Manager($access_token);
    $location_data = new Location_Data_Manager($access_token);
    $product_data = new Product_Data_Manager($access_token);

    $categories = $category_data->read();
    $product_types = $product_type_data->read();
    $locations = $location_data->read();

    // Handle create, update and delete operations.
    $result_code = $product_data->perform_action();
    // Read locations to be displayed to the user.
    $products = $product_data->read();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - lagerboder</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/admin_products.js"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var MAX_PADDING_DIGIT_COUNT = <?= Utility::MAX_PADDING_DIGIT_COUNT ?>;

st.prod.TEXTS = <?= $text->get(2, "['Ledig (aldri utleid)', 'Ledig (tidligere leieforhold avsluttet)', 'Reservert (aldri utleid f&oslash;r)', 'Reservert (tidligere leieforhold avsluttet)', 'Utleid', 'Blir ledig (n&aring;v&aelig;rende leieforhold oppsagt)', 'Reservert (n&aring;v&aelig;rende leieforhold oppsagt)']") ?>;

st.prod.TEXTS_BRIEF = <?= $text->get(3, "['Ledig', 'Ledig', 'Reservert', 'Reservert', 'Utleid', 'Blir ledig', 'Reservert']") ?>;

var settings = <?= $settings->as_javascript() ?>;

// The current location filter, or null if all products are displayed, regardless of location. The
// filter is an array of integers, containing IDs of locations that should be displayed. Note that
// the parameter must include the brackets, not just a comma separated list of numbers.
var locationFilter = <?= Utility::read_passed_string('location_filter', 'null') ?>;

// The current product type filter, or null if all products are displayed, regardless of product
// type. The filter is an array of integers, containing IDs of product types that should be
// displayed. Note that the parameter must include the brackets, not just a comma separated list of
// numbers.
var productTypeFilter = <?= Utility::read_passed_string('product_type_filter', 'null') ?>;

// The current status filter, or null if all products are displayed, regardless of status. The
// filter is an array of integers, containing statuses that should be displayed. Note that the
// parameter must include the brackets, not just a comma separated list of numbers.
var statusFilter = <?= Utility::read_passed_string('status_filter', 'null') ?>;

var resultCode = <?= $result_code ?>;
var categories = <?= $categories ?>;
var productTypes = <?= $product_types ?>;
var locations = <?= $locations ?>;
var products = <?= $products ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_expanding_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(0, 'Lagerboder'), 'fa-boxes-stacked') ?>
    <div class="content">
      <div class="toolbar">
        <button type="button" class="wide-button" onclick="displayEditProductDialogue(-1);"><i class="fa-solid fa-boxes-stacked"></i> <?= $text->get(1, 'Legg til lagerbod') ?></button>
        <div id="filterToolbar" class="filter">
          &nbsp;
        </div>
      </div>
      <div id="productsBox">
        &nbsp;
      </div>
    </div>

    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editProductDialogue" class="dialogue edit-product-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="createSubscriptionDialogue" class="dialogue create-subscription-dialogue" style="display: none;">
      &nbsp;
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
