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
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/insurance_data_manager.php';
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
    $product_types = Test_Data_Manager::PRODUCT_TYPES;
    $insurance_products = Test_Data_Manager::INSURANCE_PRODUCTS;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $product_type_data = new Product_Type_Data_Manager($access_token);
    $insurance_data = new Insurance_Data_Manager($access_token);

    $locations = $location_data->read();
    $product_types = $product_type_data->read();

    // Handle create, update and delete operations.
    $result_code = $insurance_data->perform_action();
    // Read insurance products to be displayed to the user.
    $insurance_products = $insurance_data->read();
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
    <script type="text/javascript" src="/subscription/js/admin_insurance.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

// The current freetext filter, or an empty string if all insurance products are displayed,
// regardless of what they contain. If a text is supplied, insurance products will only be displayed
// if they contain that text, as part of either the name or description fields.
var freetextFilter = '<?= Utility::read_passed_string('freetext_filter', '') ?>';

<?= Utility::write_initial_sorting() ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var productTypes = <?= $product_types ?>;
var insuranceProducts = <?= $insurance_products ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Forsikringer'), 'fa-fire') ?>
    <div class="content">
      <div class="toolbar">
        <button type="button" class="wide-button" onclick="displayEditInsuranceProductDialogue(-1);"><i class="fa-solid fa-location-dot"></i> <?= $text->get(1, 'Opprett forsikring') ?></button>
        <div id="filterToolbar" class="filter filter-next-to-buttons">
          &nbsp;
        </div>
      </div>
      <div id="insuranceProductsBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editInsuranceProductDialogue" class="dialogue edit-insurance-product-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
