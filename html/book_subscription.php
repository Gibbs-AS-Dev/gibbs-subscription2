<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/dynamic_styles.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/category_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/insurance_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

  // See if the user is already logged in as an ordinary user, or else try to let him use the booking process
  // anonymously.
  $access_token = User::verify_is_user_or_anonymous();

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

  // Calculate tab indices.
  $next_index = 0;
  // Location. If there is only one, don't bother displaying the tab. We can guess what the user is going to select.
  if (count($location_list) === 1)
  {
    // If the customer needs to submit a request when booking at this location, redirect him to the submit request page
    // straight away, and stop executing this script.
    $location_id = intval($location_list[0]->id);
    if ($settings->submit_request_when_booking_at($location_id))
    {
      Utility::redirect_to('/subscription/html/submit_request.php?location_id=' . $location_id);
    }
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

  // Load user information.
  $is_logged_in = $access_token->get_role() === Utility::ROLE_USER;
  if ($is_logged_in)
  {
    $user = User_Data_Manager::get_user();
  }
  else
  {
    $user = 'null';
  }

  // Find the URL for the supplier's terms and conditions.
  $terms_url = $settings->get_terms_url_for_language();
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
<?= Dynamic_Styles::get_user_styles($settings) ?>
    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/book_subscription.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script type="text/javascript" src="/subscription/components/gibbs_leaflet_map/gibbs_leaflet_map.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/tabset/number_tabset.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var DAY_NAMES = <?= $text->get(5, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(6, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(7, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

var isLoggedIn = <?= ($is_logged_in ? 'true' : 'false') ?>;
var user = <?= $user ?>;

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
  <body onload="initialise();">
    <div class="content-area">
      <div class="toolbar">
        <div class="back-button-box">
          <button type="button" class="back-button" onclick="goBack();"><?= $text->get(0, 'Tilbake') ?></button>
        </div>
        <div id="tabButtonArea" class="tab-button-area">
        </div>
      </div>
      <div id="tab_<?= $location_tab_index ?>" class="tab" style="display: none;">
        <h1><?= $text->get(1, 'Velg lager') ?></h1>
        <div id="locationBox" class="location-box"></div>
      </div>
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
            <button type="button" class="wide-button" onclick="displayCategoryFilterDialogue();"><i class="fa-solid fa-filter"></i>&nbsp;&nbsp;<?= $text->get(20, 'Velg kategori') ?></button>
            <button type="button" id="resetFilterButton" class="reset-filter-button" style="display: none;" onclick="clearCategoryFilter();"><i class="fa-solid fa-filter-slash"></i></button>
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
        <div id="userInfoBox" class="area-box">
<?php
  // If the user is not logged in, display the user interface to create a new user. Otherwise, the client will fill in
  // information about the logged-in user.
  if (!$is_logged_in)
  {
?>
          <div class="separator-box">
            <button type="button" class="login-button" onclick="displayLoginDialogue();"><i class="fa-solid fa-person-to-door"></i>&nbsp;&nbsp;<?= $text->get(24, 'Logg inn') ?></button>
            <h2><?= $text->get(10, 'Kontaktinformasjon') ?></h2>
          </div>
          <div class="entity-type-box">
            <label>
              <input type="radio" id="newIndividualButton" name="entity_type" value="0" checked onchange="selectEntityType();">
              <?= $text->get(33, 'Privatperson') ?> <span class="mandatory">*</span>
            </label>
            <label>
              <input type="radio" id="newCompanyButton" name="entity_type" value="1" onchange="selectEntityType();">
              <?= $text->get(34, 'Bedrift') ?> <span class="mandatory">*</span>
            </label>
          </div>
          <div class="user-info-box">
            <label for="newUserNameEdit"><?= $text->get(13, 'E-post:') ?> <span class="mandatory">*</span></label>
            <input type="email" id="newUserNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
            <div id="individualDataBox">
              <label for="newFirstNameEdit"><?= $text->get(11, 'Fornavn:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newFirstNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              <label for="newLastNameEdit"><?= $text->get(12, 'Etternavn:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newLastNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
            </div>
            <div id="companyDataBox" style="display: none;">
              <label for="newCompanyNameEdit"><?= $text->get(35, 'Selskapets navn:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newCompanyNameEdit" onkeyup="enableConfirmBookingButton();" onchange="enableConfirmBookingButton();">
              <label for="newCompanyIdEdit"><?= $text->get(36, 'Organisasjonsnummer:') ?> <span class="mandatory">*</span></label>
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
<?php
  }
?>
        </div>
        <div id="paymentMethodBox" class="area-box payment-method-box" style="display: none;"></div>
        <div class="area-box terms-box">
          <div class="separator-box">
            <h2><?= $text->get(16, 'Avtalevilkår') ?></h2>
          </div>
          <div class="form-element">
            <p>
              <i class="fa-solid fa-section"></i> <a href="<?= Utility::get_gibbs_terms_url() ?>" target="_blank" class="button wide-button"><?= $text->get(16, 'Avtalevilkår for databehandler') ?></a> <?= $text->get(17, '(Åpnes i nytt vindu)') ?><br>
<?php
  // If the supplier has provided a terms and conditions link for the currently selected language, display a link.
  if (!empty($terms_url))
  {
?>
              <i class="fa-solid fa-section"></i> <a href="<?= $terms_url ?>" target="_blank" class="button wide-button"><?= $text->get(37, 'Avtalevilkår for leverandør') ?></a> <?= $text->get(17, '(Åpnes i nytt vindu)') ?><br>
<?php
  }
?>
            </p>
            <label>
              <input type="checkbox" id="acceptTermsCheckbox" onchange="enableConfirmBookingButton();">
              <?= $text->get(18, 'Jeg aksepterer avtalevilkårene') ?> <span class="mandatory">*</span>
            </label>
          </div>
        </div>
        <div id="loginErrorBox" class="error-message" style="display: none;"></div>
        <div class="submit-box">
          <button type="button" id="confirmBookingButton" class="wide-button" onclick="confirmBooking();"><?= $text->get(19, 'Bekreft og betal') ?>&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button>
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
        <h3><?= $text->get(30, 'Prisinformasjon') ?></h3>
      </div>
      <div id="priceInformationDialogueContent" class="dialogue-content"></div>
      <div class="dialogue-footer">
        <button type="button" onclick="closePriceInformationDialogue();"><i class="fa-solid fa-check"></i>&nbsp;&nbsp;<?= $text->get(31, 'Lukk') ?></button>
      </div>
    </div>

    <div id="loginDialogue" class="dialogue login-dialogue" style="display: none;">
      <div class="dialogue-header">
        <button type="button" class="low-profile close-button" onclick="closeLoginDialogue();"><i class="fa-solid fa-xmark"></i></button>
        <h3><?= $text->get(24, 'Logg inn') ?></h3>
      </div>
      <div class="dialogue-content">
        <div class="form-element">
          <p>
            <?= $text->get(25, 'Er du allerede kunde? Logg inn her.') ?>
          </p>
        </div>
        <label for="userNameEdit"><?= $text->get(13, 'E-post:') ?> <span class="mandatory">*</span></label>
        <input type="text" id="userNameEdit" onkeypress="handleLoginDialogueKeyPress(event);" onkeyup="enableLoginButton();" onchange="enableLoginButton();">
        <label for="passwordEdit"><?= $text->get(26, 'Passord:') ?> <span class="mandatory">*</span></label>
        <input type="password" id="passwordEdit" onkeypress="handleLoginDialogueKeyPress(event);" onkeyup="enableLoginButton();" onchange="enableLoginButton();">
      </div>
      <div class="dialogue-footer">
        <button type="button" id="loginButton" onclick="logIn();"><i class="fa-solid fa-person-to-door"></i>&nbsp;&nbsp;<?= $text->get(24, 'Logg inn') ?></button>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
