<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  // $month = Utility::get_this_month();
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $locations = Test_Data_Manager::LOCATIONS;
    $orders = Test_Data_Manager::ORDERS;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $order_data = new Order_Data_Manager($access_token);

    $locations = $location_data->read();

    // Handle create, update and delete operations.
    $result_code = $order_data->perform_action();
    // Read orders to be displayed to the user.
    $orders = $order_data->read_all_orders();
    // $orders = $order_data->read_orders_for_month($month);
  }

  // Generate a string that holds the default payment status filter. The default value is to display all the statuses
  // except PAYMENT_STATUS_DELETED. Add all statuses to an array, then convert to a Javascript array declaration.
  $default_status_filter = array();
  for ($i = Utility::PAYMENT_STATUS_FIRST; $i <= Utility::PAYMENT_STATUS_LAST; $i++)
  {
    if ($i !== Utility::PAYMENT_STATUS_DELETED)
    {
      $default_status_filter[] = $i;
    }
  }
  $default_status_filter = '[' . implode(',', $default_status_filter) . ']';
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
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/filter_tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_orders.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

var FILTER_TABSET_TEXTS = <?= $text->get(3, "['Alle', 'Faktura ikke sendt', 'Venter p&aring; betaling', 'Ikke betalt / feil ved betaling', 'Betalt']") ?>;
var PAYMENT_STATUS_TEXTS = <?= $text->get(1, "['Ukjent', 'Ikke betalt', 'Betalt', 'Delvis betalt', 'Ikke betalt - forfalt', 'Ikke betalt - betalingsp&aring;minnelse sendt', 'Ikke betalt - purring sendt', 'Ikke betalt - sendt til inkasso', 'Betalt - betalt til inkassoselskap', 'Tapt / kan ikke kreves inn', 'Kreditert', 'Feil hos betalingsselskap', 'Teknisk feil ved betaling', 'Betalt - refundert', 'Omstridt krav', 'Faktura ikke sendt', 'Faktura sendt', 'Betaling startet', 'Slettet']") ?>;
var PAYMENT_METHOD_TEXTS = <?= $text->get(2, "['Ukjent', 'Nets (kort)', 'Faktura', 'Kort, s&aring; faktura']") ?>;

<?= $text->get_js_strings() ?>

// The current location filter, or null if all orders are displayed, regardless of location. The
// filter is an array of integers, containing IDs of locations that should be displayed.
var locationFilter = <?= Utility::verify_filter('location_filter') ?>;

// The current month filter, or null if all months are displayed. The filter is an array of strings,
// with each string being a month that should be displayed. Each month has the format "yyyy-mm". The
// filter uses the order's "period_month", which is found in the data table.
var monthFilter = <?= Utility::verify_month_filter('month_filter') ?>;

// The current payment method filter, or null if all payment methods are displayed. The filter is
// an array of integers, with each entry being the ID of a payment method that should be displayed.
// The filter uses the order's "payment_method", which is found in the data table.
var methodFilter = <?= Utility::verify_filter('method_filter') ?>;

// The current payment status filter, or null if all payment statuses are displayed. The filter is
// an array of integers, with each entry being the ID of a payment status that should be displayed.
// The filter uses the order's "payment_status", which is found in the data table.
var statusFilter = <?= Utility::verify_filter('status_filter', $default_status_filter) ?>;

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var locations = <?= $locations ?>;
var orders = <?= $orders ?>;
<?php
/*
<- January 2025 ->

Here:
var selectedMonth = '<?= $month ?>';
var orders =
  {
    '< ?= $month ? >': < ?= $orders ? >
  };

In Javascript:
  var monthOrders;
  monthOrders = orders[selectedMonth];
*/
?>

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Ordre'), 'fa-file-invoice-dollar') ?>
    <div class="content">

    <!--div class="tab-menu">
    <div class="tab-item active">Alle <span class="tab-count">(120)</span></div>
    <div class="tab-item">Faktura usendt <span class="tab-count">(2)</span></div>
    <div class="tab-item">Venter betaling <span class="tab-count">(12)</span></div>
    <div class="tab-item">Ikke betalt/feilet <span class="tab-count">(1)</span></div>
    <div class="tab-item">Betalt <span class="tab-count">(98)</span></div>
    </div-->

      <div id="filterTabsetBox" class="filter-tabset">
        &nbsp;
      </div>
      <div class="toolbar">
        <div id="filterToolbar" class="filter">
          &nbsp;
        </div>
      </div>
      <div id="ordersBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editStatusDialogue" class="dialogue edit-status-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editLocationFilterDialogue" class="dialogue edit-location-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editMonthFilterDialogue" class="dialogue edit-month-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editMethodFilterDialogue" class="dialogue edit-method-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editStatusFilterDialogue" class="dialogue edit-status-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>