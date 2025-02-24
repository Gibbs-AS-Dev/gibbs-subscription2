<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/category_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/insurance_data_manager.php';
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
    $locations = Test_Data_Manager::LOCATIONS;
    $categories = Test_Data_Manager::CATEGORIES;
    $insurance_products = Test_Data_Manager::INSURANCE_PRODUCTS;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $category_data = new Category_Data_Manager($access_token);
    $insurance_data = new Insurance_Data_Manager($access_token);

    $locations = $location_data->read();
    $categories = $category_data->read();
    $insurance_products = $insurance_data->read();
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js"></script>
    <script type="text/javascript" src="/subscription/js/book_subscription.js"></script>
    <script type="text/javascript" src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script type="text/javascript" src="/subscription/components/gibbs_leaflet_map/gibbs_leaflet_map.js"></script>
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var DAY_NAMES = <?= $text->get(6, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(7, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(8, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

var settings = <?= $settings->as_javascript() ?>;  
var locations = <?= $locations ?>;
var categories = <?= $categories ?>;
var insuranceProducts = <?= $insurance_products ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(0, 'Bestill lagerbod')) ?>
    <div class="content">
      <div id="tabset">
        &nbsp;
      </div>
      <div id="tab_0">
        <div class="column-container">
          <div id="locationBox" class="column select-location-box">
            &nbsp;
          </div>
          <div class="column map-and-address-box">
            <div id="addressBox">
              &nbsp;
            </div>
            <br />
            <div id="mapBox" class="map">
              &nbsp;
            </div>
          </div>
        </div>
        <div class="button-container">
          <a href="/subscription/html/user_dashboard.php" class="button"> <?= $text->get(2, 'Avbryt') ?><i class="fa-solid fa-xmark"></i></a>
          <button type="button" id="tab0NextButton" onclick="displaySelectDatePage();"><?= $text->get(3, 'Neste') ?> <i class="fa-solid fa-caret-right"></i></button>
        </div>
      </div>

      <div id="tab_1">
        <div class="form-element all-center">
          <label class="standard-label"><?= $text->get(5, 'Valgt dato:') ?></label>
          <input type="text" id="selectedDateEdit" readonly="readonly" class="long-text" />
        </div>
        <div class="form-element">
          <div id="calendarBox" class="calendar-box">
            &nbsp;
          </div>
          <br />
          <p class="help-text all-center">
            <?= $text->get(9, 'Abonnementet l&oslash;per til du sier det opp. Du kan si opp n&aring;r som helst fra Min side.') ?>
          </p>
        </div>
        <div class="button-container">
          <button type="button" onclick="tabset.activeTab = 0;"><i class="fa-solid fa-caret-left"></i> <?= $text->get(1, 'Forrige') ?></button>
          <a href="/subscription/html/user_dashboard.php" class="button"><i class="fa-solid fa-xmark"></i> <?= $text->get(2, 'Avbryt') ?></a>
          <button type="button" id="tab1NextButton" onclick="findAvailableProducts();"><?= $text->get(3, 'Neste') ?> <i class="fa-solid fa-caret-right"></i></button>
        </div>
      </div>
      
      <div id="tab_2">
        <div class="column-container">
          <div id="categoriesBox" class="column categories-box">
            &nbsp;
          </div>
          <div id="productsBox" class="column products-box">
            &nbsp;
          </div>
        </div>
        <div class="button-container">
          <button type="button" onclick="tabset.activeTab = 1;"><i class="fa-solid fa-caret-left"></i> <?= $text->get(1, 'Forrige') ?></button>
          <a href="/subscription/html/user_dashboard.php" class="button"><i class="fa-solid fa-xmark"></i> <?= $text->get(2, 'Avbryt') ?></a>
          <button type="button" id="tab2NextButton" onclick="displayInsurancePage();"><?= $text->get(3, 'Neste') ?> <i class="fa-solid fa-caret-right"></i></button>
        </div>
      </div>

      <div id="tab_3">
        <div class="column-container">
          <div id="selectInsuranceBox" class="column select-insurance-box">
            &nbsp;
          </div>
          <div id="insuranceDescriptionBox" class="column insurance-description-box">
            &nbsp;
          </div>
        </div>
        <div class="button-container">
          <button type="button" onclick="tabset.activeTab = 2;"><i class="fa-solid fa-caret-left"></i> <?= $text->get(1, 'Forrige') ?></button>
          <a href="/subscription/html/user_dashboard.php" class="button"><i class="fa-solid fa-xmark"></i> <?= $text->get(2, 'Avbryt') ?></a>
          <button type="button" id="tab3NextButton" onclick="displayPaymentInfo();"><?= $text->get(3, 'Neste') ?> <i class="fa-solid fa-caret-right"></i></button>
        </div>
      </div>

      <div id="tab_4">
        <div id="paymentBox" class="payment-box">
          &nbsp;
        </div>
        <div class="button-container">
          <button type="button" onclick="tabset.activeTab = 3;"><i class="fa-solid fa-caret-left"></i> <?= $text->get(1, 'Forrige') ?></button>
          <a href="/subscription/html/user_dashboard.php" class="button"><i class="fa-solid fa-xmark"></i> <?= $text->get(2, 'Avbryt') ?></a>
          <button type="button" class="wide-button" onclick="confirmAndPay();"><?= $text->get(4, 'Bekreft og betal') ?> <i class="fa-solid fa-caret-right"></i></button>
        </div>
      </div>

      <div id="tab_5">
        <p>If you can read this, you have fallen through the world. Please hit F5 to respawn.</p>
      </div>
    </div>
  </body>
</html>
