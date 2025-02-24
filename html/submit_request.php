<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/dynamic_styles.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/category_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/request_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

/*
  This page accepts the following parameters:
    location_id : integer               The ID of the initially selected location. This parameter is optional. If
                                        invalid or not present, no location will be selected.
    category_id : integer               The ID of the initially selected category. This parameter is optional. If
                                        invalid or not present, no category will be selected.
    start_date : string                 The initially selected start date, in the format "yyyy-mm-dd". This parameter is
                                        optional. If invalid or not present, no date will be selected.
*/

  // See if the user is already logged in as an ordinary user, or else try to let him submit a request anonymously.
  $access_token = User::verify_is_user_or_anonymous();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read parameters.
  $location_id = Utility::read_passed_integer('location_id', -1);
  $category_id = Utility::read_passed_integer('category_id', -1);
  if (Utility::date_passed('start_date'))
  {
    $start_date = Utility::read_passed_string('start_date');
  }
  else
  {
    $start_date = '';
  }

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $locations = Test_Data_Manager::LOCATIONS;
    $categories = Test_Data_Manager::CATEGORIES;
  }
  else
  {
    $location_data = new Location_Data_Manager($access_token);
    $category_data = new Category_Data_Manager($access_token);
    $request_data = new Request_Data_Manager($access_token);

    $locations = $location_data->read();
    $categories = $category_data->read();
    // Handle create, update and delete operations.
    $result_code = $request_data->perform_action();
  }

  $is_logged_in = $access_token->get_role() === Utility::ROLE_USER;
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
    <style>
<?= Dynamic_Styles::get_user_styles($settings) ?>
    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/calendar/calendar.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/submit_request.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var DAY_NAMES = <?= $text->get(8, "['', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'L&oslash;r', 'S&oslash;n']") ?>;
var MONTH_NAMES = <?= $text->get(9, "['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember']") ?>;
var MONTH_NAMES_IN_SENTENCE = <?= $text->get(10, "['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember']") ?>;

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var isLoggedIn = <?= ($is_logged_in ? 'true' : 'false') ?>;

var settings = <?= $settings->as_javascript() ?>;  
var locations = <?= $locations ?>;
var categories = <?= $categories ?>;

var initialLocationId = <?= $location_id ?>;
var initialCategoryId = <?= $category_id ?>;
var initialStartDate = '<?= $start_date ?>';

    </script>
  </head>
  <body onload="initialise();">
    <div class="content-area">
      <div class="toolbar">
        <div class="back-button-box">
          <button type="button" class="back-button" onclick="window.history.back();"><?= $text->get(1, 'Tilbake') ?></button>
        </div>
      </div>
      <div class="tab">
        <h1><?= $text->get(0, 'Send forespørsel') ?></h1>
<?php
  if (!$is_logged_in)
  {
?>
        <div id="userInfoBox" class="area-box">
          <div class="separator-box">
            <button type="button" class="login-button" onclick="displayLoginDialogue();"><i class="fa-solid fa-person-to-door"></i>&nbsp;&nbsp;<?= $text->get(12, 'Logg inn') ?></button>
            <h2><?= $text->get(2, 'Kontaktinformasjon') ?></h2>
          </div>
          <div class="entity-type-box">
            <label>
              <input type="radio" id="newIndividualButton" name="entity_type" value="0" checked onchange="selectEntityType();">
              <?= $text->get(18, 'Privatperson') ?> <span class="mandatory">*</span>
            </label>
            <label>
              <input type="radio" id="newCompanyButton" name="entity_type" value="1" onchange="selectEntityType();">
              <?= $text->get(19, 'Bedrift') ?> <span class="mandatory">*</span>
            </label>
          </div>
          <div class="user-info-box">
            <label for="newUserNameEdit"><?= $text->get(5, 'E-post:') ?> <span class="mandatory">*</span></label>
            <input type="email" id="newUserNameEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
            <div id="individualDataBox">
              <label for="newFirstNameEdit"><?= $text->get(3, 'Fornavn:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newFirstNameEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
              <label for="newLastNameEdit"><?= $text->get(4, 'Etternavn:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newLastNameEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
            </div>
            <div id="companyDataBox" style="display: none;">
              <label for="newCompanyNameEdit"><?= $text->get(20, 'Selskapets navn:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newCompanyNameEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
              <label for="newCompanyIdEdit"><?= $text->get(21, 'Organisasjonsnummer:') ?> <span class="mandatory">*</span></label>
              <input type="text" id="newCompanyIdEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
            </div>
            <label for="newPhoneEdit"><?= $text->get(6, 'Telefonnummer:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="newPhoneEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
            <label for="newAddressEdit"><?= $text->get(15, 'Adresse:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="newAddressEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
            <label for="newPostcodeEdit"><?= $text->get(16, 'Postnummer:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="newPostcodeEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
            <label for="newAreaEdit"><?= $text->get(17, 'Poststed:') ?> <span class="mandatory">*</span></label>
            <input type="text" id="newAreaEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">
            <!--label for="newPasswordEdit"><?= $text->get(7, 'Nytt passord (minst $0 tegn):', array(Utility::PASSWORD_MIN_LENGTH)) ?> <span class="mandatory">*</span></label>
            <input type="password" id="newPasswordEdit" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"--><br>
          </div>
        </div>
<?php
  }
?>
        <div id="needsBox" class="area-box needs-box"></div>
        <div id="loginErrorBox" class="error-message" style="display: none;"></div>
        <div class="submit-box">
          <button type="button" id="submitButton" class="wide-button" onclick="createUserAndSubmitRequest();"><?= $text->get(11, 'Send forespørsel') ?>&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>
    </div>

    <div id="overlay" class="overlay" style="display: none;"></div>
    <div id="loginDialogue" class="dialogue login-dialogue" style="display: none;">
      <div class="dialogue-header">
        <button type="button" class="low-profile close-button" onclick="closeLoginDialogue();"><i class="fa-solid fa-xmark"></i></button>
        <h3><?= $text->get(12, 'Logg inn') ?></h3>
      </div>
      <div class="dialogue-content">
        <div class="form-element">
          <p>
            <?= $text->get(13, 'Er du allerede kunde? Logg inn her.') ?>
          </p>
        </div>
        <label for="userNameEdit"><?= $text->get(5, 'E-post:') ?> <span class="mandatory">*</span></label>
        <input type="text" id="userNameEdit" onkeypress="handleLoginDialogueKeyPress(event);" onkeyup="enableLoginButton();" onchange="enableLoginButton();">
        <label for="passwordEdit"><?= $text->get(14, 'Passord:') ?> <span class="mandatory">*</span></label>
        <input type="password" id="passwordEdit" onkeypress="handleLoginDialogueKeyPress(event);" onkeyup="enableLoginButton();" onchange="enableLoginButton();">
      </div>
      <div class="dialogue-footer">
        <button type="button" id="loginButton" onclick="logIn();"><i class="fa-solid fa-person-to-door"></i>&nbsp;&nbsp;<?= $text->get(12, 'Logg inn') ?></button>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
