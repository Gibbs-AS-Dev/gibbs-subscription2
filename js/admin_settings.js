// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Notes.
// *************************************************************************************************
//
// - For documentation of settings, see the properties of the Settings class in settings.php.
// - When changing the colours assigned when pressing the "revert to default" button in this file
//   (in resetToDefaultColours), also modify the default colours in settings.php.
//
// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var settingsForm, generalSettingsBox, colourSettingsBox, emailSettingsBox, submitButton, overlay,
  fullModeInfoDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var useTestDataCheckbox, applicationRoleCombo, bookingTypeCombo, bookingTypeLocationsBox,
  fullModeCombo, fullModeLocationsBox, paymentMethodPrivateNetsCheckbox,
  paymentMethodPrivateInvoiceCheckbox, paymentMethodPrivateNetsThenInvoiceCheckbox,
  paymentMethodCompanyNetsCheckbox, paymentMethodCompanyInvoiceCheckbox,
  paymentMethodCompanyNetsThenInvoiceCheckbox, requireCheckAfterCancelCheckbox,
  selectableMonthCountEdit, bookableProductCountEdit, fewAvailableCountEdit, netsSecretKeyEdit,
  netsCheckoutKeyEdit, termsUrlsField, termsUrlsFrame, bgColourEdit, buttonBgColourEdit,
  buttonTextColourEdit, buttonHoverBgColourEdit, buttonHoverTextColourEdit,
  completedStepBgColourEdit, completedStepTextColourEdit, activeStepBgColourEdit,
  activeStepTextColourEdit, incompleteStepBgColourEdit, incompleteStepTextColourEdit,
  sumBgColourEdit, sumTextColourEdit, newLanguageCodeEdit, newTermsUrlEdit;

// The tabset that displays different types of settings.
var tabset;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['settingsForm', 'generalSettingsBox', 'colourSettingsBox',
    'emailSettingsBox', 'submitButton', 'overlay', 'fullModeInfoDialogue']);

  // Validate the contents of the booking type and full mode location lists. The set of locations
  // might have been altered since the settings were stored.
  validateLocationList('bookingTypeLocations');
  validateLocationList('fullModeLocations');

  // Create the user interface.
  tabset = new Tabset(
    [
      getText(6, 'Innstillinger'),
      getText(11, 'Farger'),
      getText(47, 'E-post')
    ], initialTab);
  tabset.display();

  displayGeneralSettings();
  displayColourSettings();
  displayEmailSettings();
  Utility.hideSpinner();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Some settings require a list of locations. However, locations might change, and the settings will
// not be updated at the same time. In settings, remove from the array with the given name any
// location ID which does not correspond to an actual location.
function validateLocationList(name)
{
  var i, index;

  // If it wasn't an array, create an empty one.
  if (!Array.isArray(settings[name]))
    settings[name] = [];
  else
  {
    // It was an array. Check each location ID in the array.
    i = 0;
    while (i < settings[name].length)
    {
      index = Utility.getLocationIndex(settings[name][i]);
      // If the location ID did not represent a location, delete the entry. Otherwise move on to the
      // next one.
      if (index < 0)
        settings[name].splice(i, 1);
      else
        i++;
    }
  }
}

// *************************************************************************************************
// Return true if the user has checked at least one of the payment methods, for both private
// individuals and companies.
function paymentMethodSelected()
{
  return (paymentMethodPrivateNetsCheckbox.checked || paymentMethodPrivateInvoiceCheckbox.checked ||
    paymentMethodPrivateNetsThenInvoiceCheckbox.checked) &&
    (paymentMethodCompanyNetsCheckbox.checked || paymentMethodCompanyInvoiceCheckbox.checked ||
    paymentMethodCompanyNetsThenInvoiceCheckbox.checked);
}

// *************************************************************************************************
// Enable the submit button if all the data is valid.
function enableSubmitButton()
{
  submitButton.disabled = !isValidApplicationRole(applicationRoleCombo.value) ||
    !isValidBookingType(bookingTypeCombo.value) ||
    !isValidFullMode(fullModeCombo.value) ||
    !paymentMethodSelected() ||
    !isValidSelectableMonthCount(selectableMonthCountEdit.value) ||
    !isValidBookableProductCount(bookableProductCountEdit.value) ||
    !isValidFewAvailableCount(fewAvailableCountEdit.value) ||
    !Utility.isValidColour(bgColourEdit.value) ||
    !Utility.isValidColour(buttonBgColourEdit.value) ||
    !Utility.isValidColour(buttonTextColourEdit.value) ||
    !Utility.isValidColour(buttonHoverBgColourEdit.value) ||
    !Utility.isValidColour(buttonHoverTextColourEdit.value) ||
    !Utility.isValidColour(completedStepBgColourEdit.value) ||
    !Utility.isValidColour(completedStepTextColourEdit.value) ||
    !Utility.isValidColour(activeStepBgColourEdit.value) ||
    !Utility.isValidColour(activeStepTextColourEdit.value) ||
    !Utility.isValidColour(incompleteStepBgColourEdit.value) ||
    !Utility.isValidColour(incompleteStepTextColourEdit.value) ||
    !Utility.isValidColour(sumBgColourEdit.value) ||
    !Utility.isValidColour(sumTextColourEdit.value);
  // The fields on the e-mail tab are all optional strings, so no validation is required.
}

// *************************************************************************************************
// *** General settings functions.
// *************************************************************************************************
// Display the user interface to edit settings.
function displayGeneralSettings()
{
  var o, p, i;

  o = new Array((locations.length * 26) + 174);
  p = 0;

  // Setting: use_test_data
  o[p++] = '<div class="form-element separator"><input type="checkbox" id="useTestDataCheckbox" name="use_test_data" value="true" ';
  if (settings.useTestData)
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="useTestDataCheckbox">';
  o[p++] = getText(1, 'Bruk predefinerte testdata, i stedet for data fra databasen p&aring; serveren');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label></div>';

  // Setting: application_role
  o[p++] = '<div class="form-element"><label for="applicationRoleCombo" class="wide-label">';
  o[p++] = getText(2, 'Form&aring;l:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><select id="applicationRoleCombo" name="application_role" class="long-text" onchange="enableSubmitButton();"><option value="';
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
  o[p++] = '<div class="form-element separator"><span class="help-text">';
  o[p++] = getText(59, '&quot;Produksjon&quot; bruker ekte penger. De andre alternativene er til testform&aring;l.');
  o[p++] = '</span></div>';

  // Setting: booking_type
  o[p++] = '<div class="form-element"><label for="bookingTypeCombo" class="wide-label">';
  o[p++] = getText(27, 'Bestilling:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><select id="bookingTypeCombo" name="booking_type" class="long-text" onchange="selectBookingType(this.value);"><option value="';
  o[p++] = String(BOOKING_TYPE_SELF_SERVICE);
  o[p++] = '"';
  if (settings.bookingType === BOOKING_TYPE_SELF_SERVICE)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(28, 'Selvbetjening');
  o[p++] = '</option><option value="';
  o[p++] = String(BOOKING_TYPE_REQUEST);
  o[p++] = '"';
  if (settings.bookingType === BOOKING_TYPE_REQUEST)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(29, 'Send foresp&oslash;rsel');
  o[p++] = '</option><option value="';
  o[p++] = String(BOOKING_TYPE_BOTH);
  o[p++] = '"';
  if (settings.bookingType === BOOKING_TYPE_BOTH)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(30, 'Kunden velger');
  o[p++] = '</option><option value="';
  o[p++] = String(BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS);
  o[p++] = '"';
  if (settings.bookingType === BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(62, 'Velg for hvert lager');
  o[p++] = '</option></select></div><div id="bookingTypeLocationsBox"';
  if (settings.bookingType !== BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS)
    o[p++] = ' style="display: none;"';
  o[p++] = '><div class="form-element"><input type="hidden" name="location_count" value="';
  o[p++] = String(locations.length);
  o[p++] = '" /><span class="help-text">';
  o[p++] = getText(63, 'Velg lager der det skal sendes foresp&oslash;rsel; resten har selvbetjening.');
  o[p++] = '</span></div><div class="form-element">';
  for (i = 0; i < locations.length; i++)
  {
    // Add checkbox and label for each location. Use index in ID and name. Use location ID as value.
    o[p++] = '<input type="checkbox" id="bookingTypeLocation_';
    o[p++] = String(i);
    o[p++] = '" name="booking_type_location_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = String(locations[i][c.loc.ID]);
    o[p++] = '" ';
    if (settings.bookingTypeLocations.includes(locations[i][c.loc.ID]))
      o[p++] = ' checked="checked"';
    o[p++] = ' /> <label for="bookingTypeLocation_';
    o[p++] = String(i);
    o[p++] = '">';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</label><br />';
  }
  o[p++] = '</div><div class="form-element"><button type="button" onclick="setAllLocationCheckboxesTo(true, \'bookingTypeLocation_\');"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(64, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationCheckboxesTo(false, \'bookingTypeLocation_\');"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(65, 'Ingen');
  o[p++] = '</button></div></div><div class="form-element separator">&nbsp;</div>';

  // Setting: full_mode. Note that the location count must be submitted, but it has already been
  // added for the booking type.
  o[p++] = '<div class="form-element"><label for="fullModeCombo" class="wide-label">';
  o[p++] = getText(66, 'Bodtype opptatt:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><select id="fullModeCombo" name="full_mode" class="long-text" onchange="selectFullMode(this.value);"><option value="';
  o[p++] = String(FULL_MODE_ALTERNATIVES);
  o[p++] = '"';
  if (settings.fullMode === FULL_MODE_ALTERNATIVES)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(67, 'Vis status og alternativer');
  o[p++] = '</option><option value="';
  o[p++] = String(FULL_MODE_REQUEST);
  o[p++] = '"';
  if (settings.fullMode === FULL_MODE_REQUEST)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(68, 'Skjul status og send foresp&oslash;rsel');
  o[p++] = '</option><option value="';
  o[p++] = String(FULL_MODE_REQUEST_AT_SOME_LOCATIONS);
  o[p++] = '"';
  if (settings.fullMode === FULL_MODE_REQUEST_AT_SOME_LOCATIONS)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(62, 'Velg for hvert lager');
  o[p++] = '</option></select> <button type="button" class="icon-button" onclick="displayFullModeInfo();"><i class="fa-solid fa-circle-info"></i></button></div><div id="fullModeLocationsBox"';
  if (settings.fullMode !== FULL_MODE_REQUEST_AT_SOME_LOCATIONS)
    o[p++] = ' style="display: none;"';
  o[p++] = '><div class="form-element"><span class="help-text">';
  o[p++] = getText(69, 'Velg lager der det skal sendes foresp&oslash;rsel; resten viser status og eventuelle alternativer.');
  o[p++] = '</span></div><div class="form-element">';
  for (i = 0; i < locations.length; i++)
  {
    // Add checkbox and label for each location. Use index in ID and name. Use location ID as value.
    o[p++] = '<input type="checkbox" id="fullModeLocation_';
    o[p++] = String(i);
    o[p++] = '" name="full_mode_location_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = String(locations[i][c.loc.ID]);
    o[p++] = '" ';
    if (settings.fullModeLocations.includes(locations[i][c.loc.ID]))
      o[p++] = ' checked="checked"';
    o[p++] = ' /> <label for="fullModeLocation_';
    o[p++] = String(i);
    o[p++] = '">';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</label><br />';
  }
  o[p++] = '</div><div class="form-element"><button type="button" onclick="setAllLocationCheckboxesTo(true, \'fullModeLocation_\');"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(64, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationCheckboxesTo(false, \'fullModeLocation_\');"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(65, 'Ingen');
  o[p++] = '</button></div></div><div class="form-element separator">&nbsp;</div>';

  // Setting: payment_methods_private
  o[p++] = '<div class="form-element"><label class="wide-label top">';
  o[p++] = getText(32, 'Betalingsalternativer for privatpersoner:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><div class="block"><input type="checkbox" id="paymentMethodPrivateNetsCheckbox" name="payment_method_private_';
  o[p++] = String(PAYMENT_METHOD_NETS);
  o[p++] = '" value="1" ';
  if (settings.paymentMethodsPrivate.includes(PAYMENT_METHOD_NETS))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="paymentMethodPrivateNetsCheckbox">';
  o[p++] = getText(33, 'Nets (debetkort eller kredittkort)');
  o[p++] = '</label><br /><input type="checkbox" id="paymentMethodPrivateInvoiceCheckbox" name="payment_method_private_';
  o[p++] = String(PAYMENT_METHOD_INVOICE);
  o[p++] = '" value="1" ';
  if (settings.paymentMethodsPrivate.includes(PAYMENT_METHOD_INVOICE))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="paymentMethodPrivateInvoiceCheckbox">';
  o[p++] = getText(34, 'Faktura (EHF, eFaktura eller manuell fakturering)');
  o[p++] = '</label><br /><input type="checkbox" id="paymentMethodPrivateNetsThenInvoiceCheckbox" name="payment_method_private_';
  o[p++] = String(PAYMENT_METHOD_NETS_THEN_INVOICE);
  o[p++] = '" value="1" ';
  if (settings.paymentMethodsPrivate.includes(PAYMENT_METHOD_NETS_THEN_INVOICE))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="paymentMethodPrivateNetsThenInvoiceCheckbox">';
  o[p++] = getText(51, 'F&oslash;rste betaling med Nets; p&aring;f&oslash;lgende betalinger med faktura');
  o[p++] = '</label></div></div>';

  // Setting: payment_methods_company
  o[p++] = '<div class="form-element"><label class="wide-label top">';
  o[p++] = getText(53, 'Betalingsalternativer for bedrifter:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><div class="block"><input type="checkbox" id="paymentMethodCompanyNetsCheckbox" name="payment_method_company_';
  o[p++] = String(PAYMENT_METHOD_NETS);
  o[p++] = '" value="1" ';
  if (settings.paymentMethodsCompany.includes(PAYMENT_METHOD_NETS))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="paymentMethodCompanyNetsCheckbox">';
  o[p++] = getText(33, 'Nets (debetkort eller kredittkort)');
  o[p++] = '</label><br /><input type="checkbox" id="paymentMethodCompanyInvoiceCheckbox" name="payment_method_company_';
  o[p++] = String(PAYMENT_METHOD_INVOICE);
  o[p++] = '" value="1" ';
  if (settings.paymentMethodsCompany.includes(PAYMENT_METHOD_INVOICE))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="paymentMethodCompanyInvoiceCheckbox">';
  o[p++] = getText(34, 'Faktura (EHF, eFaktura eller manuell fakturering)');
  o[p++] = '</label><br /><input type="checkbox" id="paymentMethodCompanyNetsThenInvoiceCheckbox" name="payment_method_company_';
  o[p++] = String(PAYMENT_METHOD_NETS_THEN_INVOICE);
  o[p++] = '" value="1" ';
  if (settings.paymentMethodsCompany.includes(PAYMENT_METHOD_NETS_THEN_INVOICE))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="paymentMethodCompanyNetsThenInvoiceCheckbox">';
  o[p++] = getText(51, 'F&oslash;rste betaling med Nets; p&aring;f&oslash;lgende betalinger med faktura');
  o[p++] = '</label></div></div>';
  o[p++] = '<div class="form-element separator"><span class="help-text">';
  o[p++] = getText(52, 'Hvis du krysser av flere alternativer, vil kunden bli bedt om &aring; velge.');
  o[p++] = '</span></div>';

  // Setting: require_check_after_cancel
  o[p++] = '<div class="form-element separator"><input type="checkbox" id="requireCheckAfterCancelCheckbox" name="require_check_after_cancel" value="true" ';
  if (settings.requireCheckAfterCancel)
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="enableSubmitButton();"/> <label for="requireCheckAfterCancelCheckbox">';
  o[p++] = getText(56, 'Lagerboder m&aring; sjekkes etter endt utleie');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label></div>';

  // Setting: selectable_month_count
  o[p++] = Utility.getNumericEditBox(
    'selectableMonthCountEdit',
    'selectable_month_count',
    getText(31, 'Antall kalenderm&aring;neder'),
    settings.selectableMonthCount,
    2,
    24,
    null,
    'wide-label'
  );
  o[p++] = '<div class="form-element separator"><span class="help-text">';
  o[p++] = getText(48, 'Hvor langt fram i tid kunden kan velge dato n&aring;r han skal bestille et abonnement.');
  o[p++] = '</span></div>';

  // Setting: bookable_product_count
  o[p++] = Utility.getNumericEditBox(
    'bookableProductCountEdit',
    'bookable_product_count',
    getText(7, 'Antall lagerboder:'),
    settings.bookableProductCount,
    -1,
    100,
    null,
    'wide-label'
  );
  o[p++] = '<div class="form-element separator"><span class="help-text">';
  o[p++] = getText(49, 'Antall lagerboder som reserveres n&aring;r kunden velger bodtype. Hvis ingen av dem fortsatt er ledig n&aring;r kunden bekrefter bestillingen, vil han f&aring; feilmelding. Gyldige verdier er 1-1000, eller -1 for &aring; ta med alle (p&aring;virker ytelse).');
  o[p++] = '</span></div>';

  // Setting: few_available_count
  o[p++] = Utility.getNumericEditBox(
    'fewAvailableCountEdit',
    'few_available_count',
    getText(8, 'Grense for &quot;f&aring; igjen&quot;:'),
    settings.fewAvailableCount,
    0,
    100,
    null,
    'wide-label'
  );
  o[p++] = '<div class="form-element separator"><span class="help-text">';
  o[p++] = getText(50, 'Hvis det er s&aring; mange eller f&aelig;rre lagerboder ledig, vises varsel om &quot;f&aring; igjen&quot;.');
  o[p++] = '</span></div>';

  // Setting: nets_secret_key
  o[p++] = Utility.getEditBox(
    'netsSecretKeyEdit',
    'nets_secret_key',
    getText(9, 'Nets hemmelig n&oslash;kkel:'),
    settings.netsSecretKey,
    null,
    'wide-label',
    'api-key');

  // Setting: nets_checkout_key
  o[p++] = Utility.getEditBox(
    'netsCheckoutKeyEdit',
    'nets_checkout_key',
    getText(10, 'Nets betalingsn&oslash;kkel:'),
    settings.netsCheckoutKey,
    null,
    'wide-label',
    'api-key');
  o[p++] = '<div class="form-element separator"><span class="help-text">';
  o[p++] = getText(58, 'N&oslash;klene finner du hos <a href="https://portal.dibspayment.eu/" target="_blank">Nets</a>. Logg inn, og g&aring; til &quot;Company&quot; &gt; &quot;Integration&quot;.');
  o[p++] = '</span></div><br />';

  // Setting: terms_urls. Display the list of existing language codes and URLs, with the option to
  // delete them. Also display two edit boxes to add a new line. In addition, include a hidden input
  // to hold the concatenated table to be submitted to the server.
  o[p++] = '<input type="hidden" id="termsUrlsField" name="terms_urls" /><div class="form-element"><h3>';
  o[p++] = getText(60, 'Brukervilk&aring;r');
  o[p++] = '</h3><span class="help-text">';
  o[p++] = getText(61, 'Legg til URL til brukervilk&aring;rene nedenfor. Kunden m&aring; akseptere disse n&aring;r han bestiller. Du kan ha forskjellig URL for forskjellige spr&aring;k.');
  o[p++] = '</span></div><div id="termsUrlsFrame" class="terms-urls-frame">&nbsp;</div><div class="form-element"><span class="help-text">';
  o[p++] = getText(55, 'Fyll inn en hel rad, og klikk &quot;Legg til&quot; for &aring; legge den til i listen. URL-en m&aring; begynne med &quot;http://&quot; eller &quot;https://&quot;.');
  o[p++] = '<br />';
  o[p++] = getText(54, 'Eksempler p&aring; spr&aring;k: nb_NO, nn_NO, sv_SE, da_DK, en_GB, en_US, de_DE. <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">Se oversikt</a>');
  o[p++] = '</span></div>';

  generalSettingsBox.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['useTestDataCheckbox', 'applicationRoleCombo', 'bookingTypeCombo',
    'bookingTypeLocationsBox', 'fullModeCombo', 'fullModeLocationsBox',
    'paymentMethodPrivateNetsCheckbox', 'paymentMethodPrivateInvoiceCheckbox',
    'paymentMethodPrivateNetsThenInvoiceCheckbox', 'paymentMethodCompanyNetsCheckbox',
    'paymentMethodCompanyInvoiceCheckbox', 'paymentMethodCompanyNetsThenInvoiceCheckbox',
    'requireCheckAfterCancelCheckbox', 'selectableMonthCountEdit', 'bookableProductCountEdit',
    'fewAvailableCountEdit', 'netsSecretKeyEdit', 'netsCheckoutKeyEdit', 'termsUrlsField',
    'termsUrlsFrame']);

  // Fill in the termsUrlsField and termsUrlsFrame contents based on the current settings.
  updateTermsUrlsFrame();
}

// *************************************************************************************************
// Update the contents of the table that displays terms URLs for various languages. Also update the
// field that submits concatenated information to the server when the user submits the form.
function updateTermsUrlsFrame()
{
  var keys, o, p, i;

  keys = Object.keys(settings.termsUrls)
  o = new Array((keys.length * 9) + 8);
  p = 0;

  // Write table of URLs for each language, with a delete button for each.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(36, 'Spr&aring;k');
  o[p++] = '</th><th>';
  o[p++] = getText(37, 'URL');
  o[p++] = '</th><th>&nbsp;</th></tr></thead><tbody>';
  for (i = 0; i < keys.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = keys[i];
    o[p++] = '</td><td>';
    o[p++] = settings.termsUrls[keys[i]];
    o[p++] = '</td><td><button type="button" onclick="deleteTermsUrl(\'';
    o[p++] = keys[i];
    o[p++] = '\');"><i class="fa-solid fa-trash"></i> ';
    o[p++] = getText(38, 'Slett');
    o[p++] = '</button></td></tr>';
  }
  // Add a row with edit boxes, and a button to add a new entry.
  o[p++] = '<tr><td><input type="text" id="newLanguageCodeEdit" class="language-code-edit" /></td><td><input type="text" id="newTermsUrlEdit" class="terms-url-edit" /></td><td><button type="button" onclick="addTermsUrl();"><i class="fa-solid fa-plus"></i> ';
  o[p++] = getText(57, 'Legg til');
  o[p++] = '</button></td></tr></tbody></table>';
  
  termsUrlsFrame.innerHTML = o.join('');
  termsUrlsField.value = getConcatenatedTermsUrls();

  // Obtain pointers to user interface elements.
  Utility.readPointers(['newLanguageCodeEdit', 'newTermsUrlEdit']);
}

// *************************************************************************************************
// Return a string that holds a concatenated list of terms URLs for various languages. The language
// code is separated from the URL with a space. Each pair of language code / URL is separated by a
// pipe character.
function getConcatenatedTermsUrls()
{
  var keys, o, p, i;

  keys = Object.keys(settings.termsUrls)
  o = new Array(keys.length);
  p = 0;

  for (i = 0; i < keys.length; i++)
  {
    // Add a country code and URL pair, separated by a space.
    o[p++] = keys[i] + ' ' + settings.termsUrls[keys[i]];
  }
  // Join the pairs with pipe characters in between.
  return o.join('|');
}

// *************************************************************************************************
// Delete the terms URL for the language code given in key.
function deleteTermsUrl(key)
{
  if (settings.termsUrls[key])
  {
    delete settings.termsUrls[key];
    updateTermsUrlsFrame();
  }
}

// *************************************************************************************************
// Add a new terms URL using the information found in the user interface.
function addTermsUrl()
{
  var newKey, newValue;

  newKey = newLanguageCodeEdit.value;
  newValue = newTermsUrlEdit.value;
  if ((newKey !== '') && (newValue !== '') && (!settings.termsUrls[newKey]))
  {
    settings.termsUrls[newKey] = newValue;
    updateTermsUrlsFrame();
  }
}

// *************************************************************************************************
// Return true if the given value is valid for the applicationRole setting.
function isValidApplicationRole(value)
{
  return (value === APP_ROLE_PRODUCTION) || (value === APP_ROLE_EVALUATION) ||
    (value === APP_ROLE_TEST);
}

// *************************************************************************************************
// Return true if the given value is valid for the bookingType setting.
function isValidBookingType(value)
{
  value = parseInt(value, 10);
  return isFinite(value) && (value >= BOOKING_TYPE_SELF_SERVICE) &&
    (value <= BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS);
}

// *************************************************************************************************
// Return true if the given value is valid for the fullMode setting.
function isValidFullMode(value)
{
  value = parseInt(value, 10);
  return isFinite(value) && (value >= FULL_MODE_ALTERNATIVES) &&
    (value <= FULL_MODE_REQUEST_AT_SOME_LOCATIONS);
}

// *************************************************************************************************
// Return true if the given value is a valid selectable month count.
function isValidSelectableMonthCount(value)
{
  value = parseInt(value, 10);
  return isFinite(value) && ((value >= 2) && (value <= 24));
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
// Check or uncheck all the location checkboxes with the given boxId, depending on checked, which
// should be a boolean. The index of the location will be added to the boxId.
function setAllLocationCheckboxesTo(checked, boxId)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < locations.length; i++)
  {
    checkbox = document.getElementById(boxId + String(i));
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************
// Event handler that should be triggered when the selected booking type has been modified.
function selectBookingType(selectedBookingType)
{
  selectedBookingType = parseInt(selectedBookingType, 10);
  if (!isValidBookingType(selectedBookingType))
    return;
  Utility.setDisplayState(bookingTypeLocationsBox,
    selectedBookingType === BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS);
  enableSubmitButton();
}

// *************************************************************************************************
// Event handler that should be triggered when the selected full mode has been modified.
function selectFullMode(selectedFullMode)
{
  selectedFullMode = parseInt(selectedFullMode, 10);
  if (!isValidFullMode(selectedFullMode))
    return;
  Utility.setDisplayState(fullModeLocationsBox,
    selectedFullMode === FULL_MODE_REQUEST_AT_SOME_LOCATIONS);
  enableSubmitButton();
}

// *************************************************************************************************
// *** Colour settings functions.
// *************************************************************************************************

function displayColourSettings()
{
  var o, p;

  o = new Array(18);
  p = 0;

  o[p++] = '<div class="form-element"><button type="button" class="wide-button" onclick="resetToDefaultColours();">';
  o[p++] = getText(12, 'Bruk standardfarger');
  o[p++] = '</button></div><div class="form-element"><span class="help-text">';
  o[p++] = getText(13, 'Disse fargene brukes p&aring; bestillingssiden. Juster fargene slik at de stemmer med din selskapsprofil.');
  o[p++] = '</span></div>';

  o[p++] = Utility.getColourEditBox('bgColourEdit', 'bg_colour',
    getText(35, 'Bakgrunnsfarge:'), settings.bgColour,
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('buttonBgColourEdit', 'button_bg_colour',
    getText(14, 'Knapp, bakgrunnsfarge:'), settings.buttonBgColour,
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('buttonTextColourEdit', 'button_text_colour', 
    getText(15, 'Knapp, tekstfarge:'), settings.buttonTextColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('buttonHoverBgColourEdit', 'button_hover_bg_colour', 
    getText(16, 'Knapp mouseover, bakgrunnsfarge:'), settings.buttonHoverBgColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('buttonHoverTextColourEdit', 'button_hover_text_colour', 
    getText(17, 'Knapp mouseover, tekstfarge:'), settings.buttonHoverTextColour,
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('completedStepBgColourEdit', 'completed_step_bg_colour', 
    getText(18, 'Forrige steg, bakgrunnsfarge:'), settings.completedStepBgColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('completedStepTextColourEdit', 'completed_step_text_colour', 
    getText(19, 'Forrige steg, tekstfarge:'), settings.completedStepTextColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('activeStepBgColourEdit', 'active_step_bg_colour', 
    getText(20, 'N&aring;v&aelig;rende steg, bakgrunnsfarge:'), settings.activeStepBgColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('activeStepTextColourEdit', 'active_step_text_colour', 
    getText(21, 'N&aring;v&aelig;rende steg, tekstfarge:'), settings.activeStepTextColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('incompleteStepBgColourEdit', 'incomplete_step_bg_colour', 
    getText(22, 'Neste steg, bakgrunnsfarge:'), settings.incompleteStepBgColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('incompleteStepTextColourEdit', 'incomplete_step_text_colour', 
    getText(23, 'Neste steg, tekstfarge:'), settings.incompleteStepTextColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('sumBgColourEdit', 'sum_bg_colour', 
    getText(24, 'Sum, bakgrunnsfarge:'), settings.sumBgColour, 
    'enableSubmitButton();', null, null, true);

  o[p++] = Utility.getColourEditBox('sumTextColourEdit', 'sum_text_colour', 
    getText(25, 'Sum, tekstfarge:'), settings.sumTextColour, 
    'enableSubmitButton();', null, null, true);

  colourSettingsBox.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['bgColourEdit', 'buttonBgColourEdit', 'buttonTextColourEdit',
    'buttonHoverBgColourEdit',  'buttonHoverTextColourEdit', 'completedStepBgColourEdit',
    'completedStepTextColourEdit',  'activeStepBgColourEdit', 'activeStepTextColourEdit',
    'incompleteStepBgColourEdit',  'incompleteStepTextColourEdit', 'sumBgColourEdit',
    'sumTextColourEdit']);
}

// *************************************************************************************************
// Fill the colour tab edit boxes with default values.
function resetToDefaultColours()
{
  if (!confirm(getText(26, 'Er du sikker på at du vil gå tilbake til standardfargene?')))
    return;

  bgColourEdit.value = '#fff';
  buttonBgColourEdit.value = '#299583';
  buttonTextColourEdit.value = '#fff';
  buttonHoverBgColourEdit.value = '#007969';
  buttonHoverTextColourEdit.value = '#fff';
  completedStepBgColourEdit.value = '#eaf4f3';
  completedStepTextColourEdit.value = '#299583';
  activeStepBgColourEdit.value = '#299583';
  activeStepTextColourEdit.value = '#fff';
  incompleteStepBgColourEdit.value = '#f3f3f3';
  incompleteStepTextColourEdit.value = '#9d9d9d';
  sumBgColourEdit.value = '#b3dad5';
  sumTextColourEdit.value = '#1e1e2d';

  updateColourPreview(bgColourEdit);
  updateColourPreview(buttonBgColourEdit);
  updateColourPreview(buttonTextColourEdit);
  updateColourPreview(buttonHoverBgColourEdit);
  updateColourPreview(buttonHoverTextColourEdit);
  updateColourPreview(completedStepBgColourEdit);
  updateColourPreview(completedStepTextColourEdit);
  updateColourPreview(activeStepBgColourEdit);
  updateColourPreview(activeStepTextColourEdit);
  updateColourPreview(incompleteStepBgColourEdit);
  updateColourPreview(incompleteStepTextColourEdit);
  updateColourPreview(sumBgColourEdit);
  updateColourPreview(sumTextColourEdit);

  enableSubmitButton();
}

// *************************************************************************************************
// *** E-mail settings functions.
// *************************************************************************************************

function displayEmailSettings()
{
  var o, p;

  o = new Array(8);
  p = 0;

  // Setting: from_email_name
  o[p++] = Utility.getEditBox(
    'fromEmailNameEdit',
    'from_email_name',
    getText(39, 'Avsenders navn i e-post:'),
    settings.fromEmailName,
    null,
    'wide-label',
    null,
    false);

  // Setting: from_email
  o[p++] = Utility.getEditBox(
    'fromEmailEdit',
    'from_email',
    getText(40, 'Fra e-post:'),
    settings.fromEmail,
    null,
    'wide-label',
    null,
    false);

  // Setting: reply_to_email
  o[p++] = Utility.getEditBox(
    'replyToEmailEdit',
    'reply_to_email',
    getText(41, 'Svar til e-post:'),
    settings.replyToEmail,
    null,
    'wide-label',
    null,
    false);

  // Setting: company_name
  o[p++] = Utility.getEditBox(
    'companyNameEdit',
    'company_name',
    getText(42, 'Selskapets navn:'),
    settings.companyName,
    null,
    'wide-label',
    null,
    false);

  // Setting: company_address
  o[p++] = Utility.getEditBox(
    'companyAddressEdit',
    'company_address',
    getText(43, 'Adresse:'),
    settings.companyAddress,
    null,
    'wide-label',
    null,
    false);

  // Setting: company_postcode
  o[p++] = Utility.getEditBox(
    'companyPostcodeEdit',
    'company_postcode',
    getText(44, 'Postnummer:'),
    settings.companyPostcode,
    null,
    'wide-label',
    null,
    false);

  // Setting: company_area
  o[p++] = Utility.getEditBox(
    'companyAreaEdit',
    'company_area',
    getText(45, 'Poststed:'),
    settings.companyArea,
    null,
    'wide-label',
    null,
    false);

  // Setting: company_country
  o[p++] = Utility.getEditBox(
    'companyCountryEdit',
    'company_country',
    getText(46, 'Land:'),
    settings.companyCountry,
    null,
    'wide-label',
    null,
    false);

  emailSettingsBox.innerHTML = o.join('');
}

// *************************************************************************************************
// *** Full mode information functions.
// *************************************************************************************************

function displayFullModeInfo()
{
  Utility.display(overlay);
  Utility.display(fullModeInfoDialogue);
}

// *************************************************************************************************

function closeFullModeInfo()
{
  Utility.hide(fullModeInfoDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
