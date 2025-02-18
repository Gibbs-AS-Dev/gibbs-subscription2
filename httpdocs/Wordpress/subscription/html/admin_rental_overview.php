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
    // Read products to be displayed to the user.
    $products = $product_data->read();
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
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/filter_tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_rental_overview.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var BUILD_NO = <?= Utility::BUILD_NO ?>;
var MAX_PADDING_DIGIT_COUNT = <?= Utility::MAX_PADDING_DIGIT_COUNT ?>;
var FILTER_TABSET_TEXTS = <?= $text->get(1, "['Alle', 'Inn- / utsjekk', 'M&aring sjekkes', 'Utleid', 'Ledig', 'Inaktiv']") ?>;
var DAY_NAMES = <?= $text->get(5, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(6, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(7, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

st.prod.TEXTS = <?= $text->get(2, "['<b>Ledig</b>&nbsp;&nbsp;Aldri utleid.', '<b>Ledig</b>&nbsp;&nbsp;Tidligere leieforhold avsluttet.', '<b>Flytter inn</b>&nbsp;&nbsp;Aldri utleid f&oslash;r; bestilt av ny kunde.', '<b>Flytter inn</b>&nbsp;&nbsp;Tidligere leieforhold avsluttet; bestilt av ny kunde.', '<b>Utleid</b>', '<b>Flytter ut</b>&nbsp;&nbsp;N&aring;v&aelig;rende leieforhold oppsagt.', '<b>Flytter inn</b>&nbsp;&nbsp;N&aring;v&aelig;rende leieforhold oppsagt; bestilt av ny kunde.']") ?>;
st.prod.TEXTS_BRIEF = <?= $text->get(3, "['Ledig', 'Ledig', 'Flytter inn', 'Flytter inn', 'Utleid', 'Flytter ut', 'Flytter inn']") ?>;

st.ready.TEXTS = <?= $text->get(4, "['Nei', 'Ja']") ?>;

st.enabled.TEXTS = <?= $text->get(8, "['Inaktiv', 'Aktiv']") ?>;

var settings = <?= $settings->as_javascript() ?>;

// The current location filter, or null if all products are displayed, regardless of location. The
// filter is an array of integers, containing IDs of locations that should be displayed.
var locationFilter = <?= Utility::verify_filter('location_filter') ?>;

// The current product type filter, or null if all products are displayed, regardless of product
// type. The filter is an array of integers, containing IDs of product types that should be
// displayed.
var productTypeFilter = <?= Utility::verify_filter('product_type_filter') ?>;

// The current status filter, or null if all products are displayed, regardless of status. The
// filter is an array of integers, containing statuses that should be displayed. Use the st.prod
// constants.
var statusFilter = <?= Utility::verify_filter('status_filter') ?>;

// The current ready status filter, or null if all products are displayed, regardless of ready
// status. The filter is an array of integers, containing statuses that should be displayed. Use the
// st.ready constants.
var readyStatusFilter = <?= Utility::verify_filter('ready_status_filter') ?>;

// The current enabled flag ilter, or null if all products are displayed, regardless of whether they
// are enabled or disabled. The filter is an array of integers, containing a zero if disabled
// products should be displayed, and a 1 if enabled products should be displayed.
var enabledFilter = <?= Utility::verify_filter('enabled_filter') ?>;

// The current freetext filter, or an empty string if all products are displayed, regardless of
// their name. If a text is supplied, products will only be displayed if they contain that text, as
// part of either the location name, product name, product type name or product notes fields.
var freetextFilter = '<?= Utility::read_passed_string('freetext_filter', '') ?>';

<?= Utility::write_initial_sorting(5, 1) ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var categories = <?= $categories ?>;
var productTypes = <?= $product_types ?>;
var locations = <?= $locations ?>;
var products = <?= $products ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Utleieoversikt'), 'fa-boxes-stacked') ?>
    <div class="content">
      <div id="filterTabsetBox" class="filter-tabset">
        &nbsp;
      </div>
      <div class="toolbar">
        <div id="filterToolbar" class="filter">
          &nbsp;
        </div>
      </div>
      <div id="productsBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="createSubscriptionDialogue" class="dialogue create-subscription-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="cancelSubscriptionDialogue" class="dialogue cancel-subscription-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="productNotesDialogue" class="dialogue product-notes-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="accessInformationDialogue" class="dialogue access-information-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editLocationFilterDialogue" class="dialogue edit-location-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editProductTypeFilterDialogue" class="dialogue edit-product-type-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editTabsetFiltersDialogue" class="dialogue edit-status-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
