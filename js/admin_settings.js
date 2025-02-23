// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Notes.
// *************************************************************************************************
//
// - For documentation of settings, see the properties of the Settings class in settings.php.
//
// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Setting name constants.
var USE_TEST_DATA = 'use_test_data';
var APPLICATION_ROLE = 'application_role';
var BOOKABLE_PRODUCT_COUNT = 'bookable_product_count';
var FEW_AVAILABLE_COUNT = 'few_available_count';
var NETS_SECRET_KEY = 'nets_secret_key';
var NETS_CHECKOUT_KEY = 'nets_checkout_key';

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var settingsBox;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  settingsBox = document.getElementById('settingsBox');

  // Create the user interface.
  displaySettings();

  // Display the results of a previous operation, if required.
  if (resultCode >= 0)
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
      [String(resultCode)]));
}

// *************************************************************************************************
// Display the user interface to edit settings.
function displaySettings()
{
  var o, p;

  o = new Array(86);
  p = 0;

  o[p++] = '<form action="/subscription/html/admin_settings.php" method="post">';
  // Setting: use_test_data
  o[p++] = '<div class="form-element"><input type="hidden" name="action" value="update_settings" /><input type="checkbox" id="';
  o[p++] = USE_TEST_DATA;
  o[p++] = '" name="';
  o[p++] = USE_TEST_DATA;
  o[p++] = '" value="true" ';
  if (settings.useTestData)
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="';
  o[p++] = USE_TEST_DATA;
  o[p++] = '">';
  o[p++] = getText(1, 'Bruk predefinerte testdata, i stedet for data fra databasen p&aring; serveren');
  o[p++] = '</label></div>';
  // Setting: application_role
  o[p++] = '<div class="form-element"><label for="';
  o[p++] = APPLICATION_ROLE;
  o[p++] = '" class="wide-label">';
  o[p++] = getText(2, 'Form&aring;l:');
  o[p++] = '</label><select id="';
  o[p++] = APPLICATION_ROLE;
  o[p++] = '" name="';
  o[p++] = APPLICATION_ROLE;
  o[p++] = '" class="long-text" onchange="enableSubmitButton();"><option value="';
  o[p++] = APP_ROLE_PRODUCTION;
  o[p++] = '"';
  if (settings.applicationRole === APP_ROLE_PRODUCTION)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(3, 'Produksjon');
  o[p++] = '</option><option value="';
  o[p++] = APP_ROLE_EVALUATION;
  o[p++] = '"';
  if (settings.applicationRole === APP_ROLE_EVALUATION)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(4, 'Evaluering');
  o[p++] = '</option><option value="';
  o[p++] = APP_ROLE_TEST;
  o[p++] = '"';
  if (settings.applicationRole === APP_ROLE_TEST)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(5, 'Automatiserte tester');
  o[p++] = '</option></select></div>';
  // Setting: bookable_product_count
  o[p++] = '<div class="form-element"><label for="';
  o[p++] = BOOKABLE_PRODUCT_COUNT;
  o[p++] = '" class="wide-label">';
  o[p++] = getText(7, 'Antall lagerboder:');
  o[p++] = '</label><input type="number" id="';
  o[p++] = BOOKABLE_PRODUCT_COUNT;
  o[p++] = '" name="';
  o[p++] = BOOKABLE_PRODUCT_COUNT;
  o[p++] = '" min="-1" max="100" value="';
  o[p++] = String(settings.bookableProductCount);
  o[p++] = '" class="numeric" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" /></div>';
  // Setting: few_available_count
  o[p++] = '<div class="form-element"><label for="';
  o[p++] = FEW_AVAILABLE_COUNT;
  o[p++] = '" class="wide-label">';
  o[p++] = getText(8, 'Grense for &quot;f&aring; igjen&quot;:');
  o[p++] = '</label><input type="number" id="';
  o[p++] = FEW_AVAILABLE_COUNT;
  o[p++] = '" name="';
  o[p++] = FEW_AVAILABLE_COUNT;
  o[p++] = '" min="0" max="100" value="';
  o[p++] = String(settings.fewAvailableCount);
  o[p++] = '" class="numeric" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" /></div>';
  // Setting: nets_secret_key
  o[p++] = '<div class="form-element"><label for="';
  o[p++] = NETS_SECRET_KEY;
  o[p++] = '" class="wide-label">';
  o[p++] = getText(9, 'Nets hemmelig n&oslash;kkel:');
  o[p++] = '</label><input type="text" id="';
  o[p++] = NETS_SECRET_KEY;
  o[p++] = '" name="';
  o[p++] = NETS_SECRET_KEY;
  o[p++] = '" value="';
  o[p++] = settings.netsSecretKey;
  o[p++] = '" class="api-key" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" /></div>';
  // Setting: nets_checkout_key
  o[p++] = '<div class="form-element"><label for="';
  o[p++] = NETS_CHECKOUT_KEY;
  o[p++] = '" class="wide-label">';
  o[p++] = getText(10, 'Nets offentlig n&oslash;kkel:');
  o[p++] = '</label><input type="text" id="';
  o[p++] = NETS_CHECKOUT_KEY;
  o[p++] = '" name="';
  o[p++] = NETS_CHECKOUT_KEY;
  o[p++] = '" value="';
  o[p++] = settings.netsCheckoutKey;
  o[p++] = '" class="api-key" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" /></div>';
  // Submit button.
  o[p++] = '<div class="button-container"><button type="submit" id="submitButton" class="wide-button">';
  o[p++] = getText(6, 'Lagre');
  o[p++] = '</button></div></form>';

  settingsBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Return true if the given value is valid for the applicationRole setting.
function isValidApplicationRole(value)
{
  return (value === APP_ROLE_PRODUCTION) || (value === APP_ROLE_EVALUATION) ||
    (value === APP_ROLE_TEST);
}

// *************************************************************************************************
// Return true if the given value is valid for the bookableProductCount setting.
function isValidBookableProductCount(value)
{
  value = parseInt(value, 10);
  return isFinite(value) && ((value === -1) || ((value >= 1) && (value <= 100)));
}

// *************************************************************************************************
// Return true if the given value is valid for the fewAvailableCount setting.
function isValidFewAvailableCount(value)
{
  value = parseInt(value, 10);
  return isFinite(value) && (value >= 0) && (value <= 100);
}

// *************************************************************************************************
// Enable the submit button if all the data is valid.
function enableSubmitButton()
{
  var applicationRoleCombo, bookableProductCountEdit, fewAvailableCountEdit, submitButton;

  applicationRoleCombo = document.getElementById(APPLICATION_ROLE);
  bookableProductCountEdit = document.getElementById(BOOKABLE_PRODUCT_COUNT);
  fewAvailableCountEdit = document.getElementById(FEW_AVAILABLE_COUNT);
  submitButton = document.getElementById('submitButton');

  submitButton.disabled = !isValidApplicationRole(applicationRoleCombo.value) ||
    !isValidBookableProductCount(bookableProductCountEdit.value) ||
    !isValidFewAvailableCount(fewAvailableCountEdit.value);
}

// *************************************************************************************************
