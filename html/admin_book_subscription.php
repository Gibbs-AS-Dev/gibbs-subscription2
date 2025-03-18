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
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/category_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/insurance_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // *******************************************************************************************************************
  // *** Functions.
  // *******************************************************************************************************************
  // Return true if the given $location_id is the ID of a location found in the given $location_list.
  function is_valid_location_id($location_id, $location_list)
  {
    $location_count = count($location_list);
    for ($i = 0; $i < $location_count; $i++)
    {
      if (intval($location_list[$i]->id) === $location_id)
      {
        return true;
      }
    }
    return false;
  }

  // *******************************************************************************************************************

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $locations = Test_Data_Manager::LOCATIONS;
    $categories = Test_Data_Manager::CATEGORIES;
    $insurance_products = Test_Data_Manager::INSURANCE_PRODUCTS;
    $has_insurance_products = true;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $category_data = new Category_Data_Manager($access_token);
    $insurance_data = new Insurance_Data_Manager($access_token);

    $location_list = $location_data->get_location_list();
    $locations = $location_data->read($location_list);
    $categories = $category_data->read();
    $insurance_products = $insurance_data->read();
    $has_insurance_products = $insurance_products !== '[]';
  }

  // Read parameters.
  // Location. If a valid location ID is passed, use that. If there is only one location, use that.
  $location_specified = false;
  $initial_location_id = 'null';
  if (Utility::integer_passed('initial_location_id'))
  {
    $location_id = Utility::read_passed_integer('initial_location_id');
    if (is_valid_location_id($location_id, $location_list))
    {
      $location_specified = true;
      $initial_location_id = $location_id;
    }
  }
  elseif (count($location_list) === 1)
  {
    $location_specified = true;
    $initial_location_id = intval($location_list[0]->id);
  }

  // Product.
  if ($location_specified && Utility::integer_passed('initial_product_id'))
  {
    $single_product = true;
    $initial_product_id = Utility::read_passed_integer('initial_product_id');
  }
  else
  {
    $single_product = false;
    $initial_product_id = 'null';
  }

  // Date. Make sure it is a properly formatted date, but read and pass along a string with quotes.
  if (Utility::date_passed('initial_date'))
  {
    $initial_date = "'" . Utility::read_passed_string('initial_date') . "'";
  }
  else
  {
    $initial_date = 'null';
  }

  // Category.
  if (Utility::integer_passed('initial_category_id'))
  {
    $initial_category_id = Utility::read_passed_integer('initial_category_id');
  }
  else
  {
    $initial_category_id = 'null';
  }

  // User.
  if (Utility::integer_passed('initial_user_id'))
  {
    $initial_user_id = Utility::read_passed_integer('initial_user_id');
  }
  else
  {
    $initial_user_id = 'null';
  }

  // Calculate tab indices.
  $next_index = 0;
  // Location. If there is only one, don't bother displaying the tab. We can guess what the user is going to select.
  // Also, if the parameter has already specified a location, don't let the user select another.
  if ($location_specified)
  {
    $location_tab_index = -1;
  }
  else
  {
    $location_tab_index = $next_index;
    $next_index++;
  }
  // Product type.
  $product_type_tab_index = $next_index;
  $next_index++;
  // Insurance.
  if ($has_insurance_products)
  {
    $insurance_tab_index = $next_index;
    $next_index++;
  }
  else
  {
    $insurance_tab_index = -1;
  }
  // Summary.
  $summary_tab_index = $next_index;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/admin.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
<?= Dynamic_Styles::get_user_styles($settings) ?>
    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/price_plan/price_plan.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_book_subscription.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script type="text/javascript" src="/subscription/components/gibbs_leaflet_map/gibbs_leaflet_map.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/number_tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var DAY_NAMES = <?= $text->get(5, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(6, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(7, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

var settings = <?= $settings->as_javascript() ?>;  
var locations = <?= $locations ?>;
var categories = <?= $categories ?>;
var insuranceProducts = <?= $insurance_products ?>;

// Tab indices.
var locationTabIndex = <?= $location_tab_index ?>;
var productTypeTabIndex = <?= $product_type_tab_index ?>;
var insuranceTabIndex = <?= $insurance_tab_index ?>;
var summaryTabIndex = <?= $summary_tab_index ?>;

    </script>
  </head>
  <body class="admin" onload="initialise(<?= $initial_location_id ?>, <?= $initial_product_id ?>, <?= $initial_date ?>, <?= $initial_category_id ?>, <?= $initial_user_id ?>);">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(16, 'Opprett abonnement'), 'fa-plus') ?>
    <div class="background-area">
      <div class="content-area">
        <div class="toolbar">
          <div class="back-button-box">
            <button type="button" class="back-button" onclick="goBack();"><?= $text->get(0, 'Tilbake') ?></button>
          </div>
          <div id="tabButtonArea" class="tab-button-area">
          </div>
        </div>
<?php
  // If a single product is selected, we already know where it is located. Do not display the select location tab.
  if (!$single_product)
  {
?>
        <div id="tab_<?= $location_tab_index ?>" class="tab" style="display: none;">
          <h1><?= $text->get(1, 'Velg lager') ?></h1>
          <div id="locationBox" class="location-box"></div>
        </div>
<?php
  }
?>
        <div id="tab_<?= $product_type_tab_index ?>" class="tab" style="display: none;">
          <h1><?= $text->get(2, 'Velg lagerbod') ?></h1>
          <div id="locationDescriptionBox" class="area-box location-description-box"></div>
          <div id="startDateBox" class="area-box start-date-box">
            <div class="separator-box">
              <h2><?= $text->get(8, 'Innflyttingsdato') ?></h2>
            </div>
            <div class="form-element">
              <input type="text" id="selectedDateEdit" readonly class="selected-date-edit" onfocus="this.blur();" onclick="toggleCalendar();" /><button type="button" id="openCalendarButton" class="date-editor-button" onclick="openCalendar();"><i class="fa-solid fa-calendar-days"></i></button><button type="button" id="closeCalendarButton" class="date-editor-button" style="display: none;" onclick="closeCalendar();"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="form-element">
              <div id="calendarBox" class="calendar-box"></div>
            </div>
          </div>
          <div>
            <div class="form-element product-types-headline">
              <h2><?= $text->get(9, 'Ledige lagerboder') ?></h2>
<?php
  // If a single product is selected, there is no need to filter. Do not display the filter buttons.
  if (!$single_product)
  {
?>
              <button type="button" class="wide-button" onclick="displayCategoryFilterDialogue();"><i class="fa-solid fa-filter"></i>&nbsp;&nbsp;<?= $text->get(20, 'Velg kategori') ?></button>
              <button type="button" id="resetFilterButton" class="reset-filter-button" style="display: none;" onclick="clearCategoryFilter();"><i class="fa-solid fa-filter-slash"></i></button>
<?php
  }
?>
            </div>
            <div id="productTypesBox" class="product-types-box"></div>
          </div>
        </div>
<?php
  if ($has_insurance_products)
  {
?>
        <div id="tab_<?= $insurance_tab_index ?>" class="tab" style="display: none;">
          <h1><?= $text->get(3, 'Velg forsikring') ?></h1>
          <div id="insuranceBox" class="insurance-box"></div>
        </div>
<?php
  }
?>
        <div id="tab_<?= $summary_tab_index ?>" class="tab" style="display: none;">
          <h1><?= $text->get(4, 'Oppsummering') ?></h1>
          <div id="summaryBox" class="area-box summary-box"></div>
          <div class="area-box">
            <div class="separator-box">
              <h2><?= $text->get(17, 'Velg kunde') ?></h2>
            </div>
            <div class="form-element">
              <label>
                <input id="bookForCustomerButton" type="radio" name="offer_type" value="0" checked onchange="selectOfferType();">
                <?= $text->get(31, 'Opprett abonnement på vegne av') ?> <span class="mandatory">*</span>
              </label>
            </div>
            <!--div class="form-element">
              <label>
                <input id="sendOfferButton" type="radio" name="offer_type" value="1" onchange="selectOfferType();">
                <?= $text->get(30, 'Send tilbud til') ?> <span class="mandatory">*</span>
              </label>
            </div-->
            <br>
            <div class="form-element">
              <label>
                <input id="newCustomerButton" type="radio" name="customer_type" value="0" checked onchange="selectCustomerType();">
                <?= $text->get(10, 'En ny kunde') ?> <span class="mandatory">*</span>
              </label>
            </div>
            <div class="form-element">
              <label>
                <input id="existingCustomerButton" type="radio" name="customer_type" value="1" onchange="selectCustomerType();">
                <?= $text->get(18, 'En eksisterende kunde') ?> <span class="mandatory">*</span>
              </label>
            </div>
            <br>
            <div id="paymentMethodNotification" class="form-element">
              <?= $text->get(38, 'Merk: Når du bestiller på vegne av en kunde, vil betalingsmåten alltid være &quot;faktura&quot;.') ?>
            </div>
            <br>
          </div>
          <div id="newUserBox" class="area-box">
            <div class="separator-box">
              <h2><?= $text->get(32, 'Ny kunde') ?></h2>
            </div>
            <div class="entity-type-box">
              <label>
                <input type="radio" id="newIndividualButton" name="entity_type" value="0" checked onchange="selectEntityType();">
                <?= $text->get(34, 'Privatperson') ?> <span class="mandatory">*</span>
              </label>
              <label>
                <input type="radio" id="newCompanyButton" name="entity_type" value="1" onchange="selectEntityType();">
                <?= $text->get(35, 'Bedrift') ?> <span class="mandatory">*</span>
              </label>
            </div>
            <div class="user-info-box">
              <label for="newUserNameEdit"><?= $text->get(13, 'E-post:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newUserNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              <div id="individualDataBox">
                <label for="newFirstNameEdit"><?= $text->get(11, 'Fornavn:') ?> <span class="mandatory">*</span></label>
                <input type="text" id="newFirstNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
                <label for="newLastNameEdit"><?= $text->get(12, 'Etternavn:') ?> <span class="mandatory">*</span></label>
                <input type="text" id="newLastNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              </div>
              <div id="companyDataBox" style="display: none;">
                <label for="newCompanyNameEdit"><?= $text->get(36, 'Selskapets navn:') ?> <span class="mandatory">*</span></label>
                <input type="text" id="newCompanyNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
                <label for="newCompanyIdEdit"><?= $text->get(37, 'Organisasjonsnummer:') ?> <span class="mandatory">*</span></label>
                <input type="text" id="newCompanyIdEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              </div>
              <label for="newPhoneEdit"><?= $text->get(14, 'Telefonnummer:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newPhoneEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              <label for="newAddressEdit"><?= $text->get(27, 'Adresse:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newAddressEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              <label for="newPostcodeEdit"><?= $text->get(28, 'Postnummer:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newPostcodeEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              <label for="newAreaEdit"><?= $text->get(29, 'Poststed:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newAreaEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              <!--label for="newPasswordEdit"><?= $text->get(15, 'Nytt passord (minst $0 tegn):', [Utility::PASSWORD_MIN_LENGTH]) ?> <span class="mandatory">*</span></label>
              <input type="password" id="newPasswordEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();"--><br>
            </div>
          </div>
          <div id="existingUserBox" class="area-box existing-user-box" style="display: none;">
            <div class="separator-box">
              <h2><?= $text->get(24, 'Eksisterende kunde') ?></h2>
            </div>
            <div id="existingUserToolbar">
              <div class="form-element">
                <?= $text->get(25, 'Søk:') ?>
                <input type="text" id="existingUserFilterEdit" onkeyup="displayExistingUsers();" onchange="displayExistingUsers();">
                <button type="button" id="clearExistingUserFilterButton" onclick="clearExistingUserFilter();"><i class="fa-solid fa-filter-slash"></i> <?= $text->get(33, 'Vis alle') ?></button>
              </div>
            </div>
            <div id="existingUserContent"></div>
            <br>
          </div>
          <div id="loginErrorBox" class="error-message" style="display: none;"></div>
          <div class="submit-box">
            <button type="button" id="confirmBookingButton" class="wide-button" onclick="confirmBooking();"><?= $text->get(19, 'Bekreft bestilling') ?>&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
      </div>

      <div id="overlay" class="overlay" style="display: none;"></div>

      <div id="editCategoryFilterDialogue" class="dialogue edit-category-filter-dialogue" style="display: none;">
        <div class="dialogue-header">
          <button type="button" class="low-profile close-button" onclick="closeCategoryFilterDialogue();"><i class="fa-solid fa-xmark"></i></button>
          <h3><?= $text->get(20, 'Velg kategori') ?></h3>
        </div>
        <div id="editCategoryFilterDialogueContent" class="dialogue-content"></div>
        <div class="dialogue-footer">
          <button type="button" class="low-profile" onclick="selectAllCategories();"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;<?= $text->get(22, 'Alle') ?></button>
          <button type="button" class="low-profile none-button" onclick="deselectAllCategories();"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;<?= $text->get(23, 'Ingen') ?></button>
          <button type="button" onclick="updateCategoryFilter();"><i class="fa-solid fa-check"></i>&nbsp;&nbsp;<?= $text->get(21, 'Bruk') ?></button>
        </div>
      </div>

      <div id="priceInformationDialogue" class="dialogue price-information-dialogue" style="display: none;">
        <div class="dialogue-header">
          <button type="button" class="low-profile close-button" onclick="closePriceInformationDialogue();"><i class="fa-solid fa-xmark"></i></button>
          <h3><?= $text->get(39, 'Prisinformasjon') ?></h3>
        </div>
        <div id="priceInformationDialogueContent" class="dialogue-content"></div>
        <div class="dialogue-footer">
          <button type="button" onclick="closePriceInformationDialogue();"><i class="fa-solid fa-check"></i>&nbsp;&nbsp;<?= $text->get(40, 'Lukk') ?></button>
        </div>
      </div>

      <div id="editPricePlanDialogue" class="dialogue edit-price-plan-dialogue" style="display: none;">
        <div class="dialogue-header">
          <button type="button" class="low-profile close-button" onclick="closePricePlanDialogue();"><i class="fa-solid fa-xmark"></i></button>
          <h3><?= $text->get(26, 'Endre pris') ?></h3>
        </div>
        <div id="editPricePlanDialogueContent" class="dialogue-content"></div>
        <div class="dialogue-footer">
          <button type="button" onclick="updatePricePlan();"><i class="fa-solid fa-check"></i>&nbsp;&nbsp;<?= $text->get(21, 'Bruk') ?></button>
        </div>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
