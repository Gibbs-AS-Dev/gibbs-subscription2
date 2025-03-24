// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// - The availableProductTypes table is loaded asynchronously, and is not found in the HTML when the
//   page loads.

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var locationBox, locationDescriptionBox, startDateBox, selectedDateEdit, openCalendarButton,
  closeCalendarButton, calendarBox, productTypesBox, resetFilterButton, insuranceBox, summaryBox,
  newIndividualButton, newCompanyButton, individualDataBox, companyDataBox, userInfoBox,
  newUserNameEdit, newFirstNameEdit, newLastNameEdit, newCompanyNameEdit, newCompanyIdEdit,
  newPhoneEdit, newAddressEdit, newPostcodeEdit, newAreaEdit, /* newPasswordEdit,*/ paymentMethodBox,
  acceptTermsCheckbox, loginErrorBox, confirmBookingButton, overlay, editCategoryFilterDialogue,
  editCategoryFilterDialogueContent, priceInformationDialogue, priceInformationDialogueContent,
  loginDialogue, userNameEdit, passwordEdit, loginButton;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var mapBox, alternativeLocationCombo;

// The tabset that displays the steps in the booking process.
var progressTabset;

// The index in the locations table of the selected location, or -1 if no location is selected.
var selectedLocation = -1;

// The GibbsLeafletMap component used to display the map on the product type tab.
var map;

// The calendar component that allows the user to select the starting date for his subscription. The
// currently selected date is stored in calendar.selectedDate.
var calendar;

// Array of boolean flags, saying which categories should be displayed when listing available
// product types. The array has one entry for each item in the categories table. The entry is a
// boolean value that says whether product types of that category should be visible.
var categoryDisplayed = null;

// The list of available product types, or null if the table has not yet been loaded. The table is
// loaded asynchronously, once the user has selected a location and a starting date. Use the c.apt
// constants.
var availableProductTypes = null;

// The index in the availableProductTypes table of the selected product type, or -1 if no product
// type is selected.
var selectedProductType = -1;

// The index in the insuranceProducts table of the selected insurance, or -1 if no insurance is
// selected. This could be because there are no available insurance products.
var selectedInsurance = -1;

// The currently selected entity type. That is, whether the new customer is a company or a private
// individual.
var selectedEntityType = ENTITY_TYPE_INDIVIDUAL;

// Flag that says whether an alert error message has already been displayed. If so, we should not
// display another.
var errorDisplayed = false;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and displaying the first page of the progress tabset.
//
// If an initialLocationId is passed, that location will be selected, and the select product type
// tab will be the initial tab. Pass null if no location is selected.
function initialise(initialLocationId)
{
  var today;

  // Obtain pointers to user interface elements.
  Utility.readPointers(['locationBox', 'locationDescriptionBox', 'startDateBox', 'selectedDateEdit',
    'openCalendarButton', 'closeCalendarButton', 'calendarBox', 'productTypesBox',
    'resetFilterButton', 'insuranceBox', 'summaryBox', 'newIndividualButton', 'newCompanyButton',
    'individualDataBox', 'companyDataBox', 'userInfoBox', 'newUserNameEdit', 'newFirstNameEdit',
    'newLastNameEdit', 'newCompanyNameEdit', 'newCompanyIdEdit', 'newPhoneEdit', 'newAddressEdit',
    'newPostcodeEdit', 'newAreaEdit', /*'newPasswordEdit',*/ 'paymentMethodBox', 'acceptTermsCheckbox',
    'loginErrorBox', 'confirmBookingButton', 'overlay', 'editCategoryFilterDialogue',
    'editCategoryFilterDialogueContent', 'priceInformationDialogue',
    'priceInformationDialogueContent', 'loginDialogue', 'userNameEdit', 'passwordEdit',
    'loginButton']);
  
  // Initialise category display flags. All categories are set to not be displayed, which -
  // curiously enough - will cause all of them to be displayed.
  categoryDisplayed = new Array(categories.length);
  hideAllCategories();

  // Create tabset.
  progressTabset = new NumberTabset(summaryTabIndex + 1);
  progressTabset.display();

  // Create calendar component.
  calendar = new Calendar(settings.selectableMonthCount);
  calendar.dayNames = DAY_NAMES;
  calendar.monthNames = MONTH_NAMES;
  calendar.monthNamesInSentence = MONTH_NAMES_IN_SENTENCE;
  today = Utility.getCurrentIsoDate();
  selectedDateEdit.value = today;
  calendar.selectedDate = today;
  calendar.onSelectDate = selectDate;

  // Set the location, if one was passed.
  if (initialLocationId !== null)
  {
    index = Utility.getLocationIndex(initialLocationId);
    if (index >= 0)
      selectedLocation = index;
  }

  // If there is only one location, select it regardless of whether a parameter was passed.
  if (locations.length <= 1)
    selectedLocation = 0;

  // Display the select location tab, if it exists. If not, the product type tab will be displayed
  // instead.
  displayLocationTab();
}

// *************************************************************************************************
// Handle a click on the back button at the top left of the screen. Display the previous tab, if
// possible. If there wasn't a previous tab, return to the index page.
function goBack()
{
  if (!progressTabset.displayPreviousTab())
    window.history.back();
  // window.location.href = '/subscription/';
}

// *************************************************************************************************
// Return true if the product type with the given index in the availableProductTypes table has price
// modifiers that affect the price. If index was not valid, return false. index is optional. If not
// present, the selectedProductType will be used.
function hasPriceMods(index)
{
  var priceMods;

  if (typeof index === 'undefined')
    index = selectedProductType;
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, availableProductTypes))
  {
    priceMods = availableProductTypes[index][c.apt.PRICE_MODS];
    return (priceMods !== null) && (priceMods.length > 0);
  }
  return false;
}

// *************************************************************************************************
// *** Location tab functions.
// *************************************************************************************************
// Display the tab that allows the user to select a location, if it exists. If not, move on to the
// select product type tab.
function displayLocationTab()
{
  if (locationTabIndex < 0)
    displayProductTypeTab();
  else
  {
    selectedLocation = -1;
    displayLocationBox();
    progressTabset.activeTabIndex = locationTabIndex;
  }
}

// *************************************************************************************************
// Display the list of available locations in the locations box.
function displayLocationBox()
{
  var o, p, i;
  
  o = new Array(locations.length * 9);
  p = 0;

  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<div class="button-box location"><div class="button-box-left location-left"><h3>';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</h3><p>';
    o[p++] = Utility.getAddress(locations[i]);
    o[p++] = '</p></div><div class="button-box-right location-right"><button type="button" onclick="selectLocation(';
    o[p++] = String(i);
    o[p++] = ');">';
    o[p++] = getText(0, 'Velg');
    o[p++] = '&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button></div></div>';
  }

  locationBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Select the location with the given index in the locations table. If that location requires that
// the customer submit a request, redirect to the submit request page. If the user has already
// selected a product type, pass the index of the product type in the availableProductTypes table in
// desiredProductType. desiredProductType is optional.
function selectLocation(index, desiredProductType)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, locations))
  {
    if ((settings.bookingType === BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS) &&
      settings.bookingTypeLocations.includes(locations[index][c.loc.ID]))
    {
      // The selected location requires customers to submit a request. Go to the submit request
      // page. Pass all the information we have gathered thus far about the customer's requirements.
      submitRequestFor(index, desiredProductType);
      return;
    }

    // Store the selected location, and proceed to look for available product types.
    selectedLocation = index;
    displayProductTypeTab();
  }
}

// *************************************************************************************************
// Go to the submit request page, to request the location with the given index in the locations
// table, and the product type with the given index in the availableProductTypes table.
// productTypeIndex is optional. If not present, only the location will be passed.
function submitRequestFor(locationIndex, productTypeIndex)
{
  var o, p;

  if (!Utility.isValidIndex(locationIndex, locations))
    return;

  o = new Array(6);
  p = 0;

  o[p++] = '/subscription/html/submit_request.php?location_id=';
  o[p++] = String(locations[locationIndex][c.loc.ID]);
  o[p++] = '&start_date=';
  o[p++] = calendar.selectedDate;
  if (productTypeIndex && Utility.isValidIndex(productTypeIndex, availableProductTypes))
  {
    o[p++] = '&category_id=';
    o[p++] = String(availableProductTypes[productTypeIndex][c.apt.CATEGORY_ID]);
  }

  Utility.displaySpinnerThenGoTo(o.join(''));
}

// *************************************************************************************************
// *** Product type tab functions.
// *************************************************************************************************
// Display the tab that allows the user to select the starting date for his subscription.
function displayProductTypeTab()
{
  displayLocationDescriptionBox();

  closeCalendar();
  calendar.display();

  progressTabset.activeTabIndex = productTypeTabIndex;
  findAvailableProductTypes();
}

// *************************************************************************************************

function displayLocationDescriptionBox()
{
  var o, p, address;

  o = new Array(17);
  p = 0;

  address = Utility.getAddress(locations[selectedLocation]);
  o[p++] = '<div class="separator-box"><p>';
  o[p++] = getText(2, 'Valgt lager:');
  o[p++] = '</p><h2>';
  o[p++] = locations[selectedLocation][c.loc.NAME];
  o[p++] = '</h2></div><div id="mapBox" class="map"></div><table cellspacing="0" cellpadding="0"><tbody><tr><td>';
  o[p++] = getText(3, 'Adresse:');
  o[p++] = '</td><td>';
  o[p++] = address;
  o[p++] = '</td></tr><tr><td>';
  o[p++] = getText(4, 'Åpningstider:');
  o[p++] = '</td><td>';
  o[p++] = locations[selectedLocation][c.loc.OPENING_HOURS];
  o[p++] = '</td></tr><tr><td>';
  o[p++] = getText(5, 'Tjenester:');
  o[p++] = '</td><td>';
  o[p++] = locations[selectedLocation][c.loc.SERVICES];
  o[p++] = '</td></tr></tbody></table>';

  locationDescriptionBox.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['mapBox']);

  // Initialise the map. Yield to the page rendering thread, so that the page is fully rendered
  // before we drop the map into the div we just created. Otherwise, the map will not be displayed
  // correctly.
  setTimeout(
    function ()
    {
      map = new GibbsLeafletMap('mapBox');
      map.displayAddress(address);
    },
    10);
}

// *************************************************************************************************

function toggleCalendar()
{
  if (Utility.displayed(calendarBox))
    closeCalendar();
  else
    openCalendar();
}

// *************************************************************************************************

function openCalendar()
{
  Utility.hide(openCalendarButton);
  Utility.display(closeCalendarButton);
  Utility.display(calendarBox);
}

// *************************************************************************************************

function closeCalendar()
{
  Utility.hide(closeCalendarButton);
  Utility.display(openCalendarButton);
  Utility.hide(calendarBox);
}

// *************************************************************************************************
// Select the given date as the starting date of the user's subscription. dateString is a string
// with a date in ISO format - that is, "yyyy-mm-dd". Update the calendar to display the date as
// selected. We know the date is visible, as you can only select dates within the currently
// displayed month.
function selectDate(sender, selectedDate)
{
  selectedDateEdit.value = selectedDate;
  closeCalendar();
  findAvailableProductTypes();
}

// *************************************************************************************************
// Fetch the list of available product types from the server, based on the selected location and
// starting date. The server will return a table of product types, with information on their
// availability. A "please wait" message will be displayed in the meantime.
function findAvailableProductTypes()
{
  var o, p;

  availableProductTypes = null;
  selectedProductType = -1;
  productTypesBox.innerHTML = '<div class="form-element">' +
    getText(6, 'Finner ledige lagerboder. Vennligst vent...') + '</div>';

  o = new Array(4);
  p = 0;

  o[p++] = '/subscription/json/available_product_types.php?action=get_available_product_types&selected_location_id=';
  o[p++] = String(locations[selectedLocation][c.loc.ID]);
  o[p++] = '&selected_date=';
  o[p++] = calendar.selectedDate;
  fetch(o.join(''))
    .then(Utility.extractJson)
    .then(storeProductTypes)
    .catch(logAvailableProductTypesError);
}

// *************************************************************************************************
// Log an error that occurred while fetching available product types from the server.
function logAvailableProductTypesError(error)
{
  console.error('Error fetching available product types: ' + error);
}

// *************************************************************************************************
// Store the list of available product types, as returned by the server. Then display the list of
// categories, so that the user can subsequently select a product.
function storeProductTypes(data)
{
  if (data)
  {
    if (data.resultCode && Utility.isError(data.resultCode))
    {
      alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(data.resultCode), Utility.getTimestamp()]));
      return;
    }
    if (data.availableProductTypes)
    {
      availableProductTypes = data.availableProductTypes;
      displayAvailableProductTypes();
    }
    else
      console.error('Error fetching available product types: product type data missing.');
  }
  else
    console.error('Wut?');
}

// *************************************************************************************************
// Display the list of available product types within the selected category.
function displayAvailableProductTypes()
{
  var o, p, i, hasProductTypes, allHidden, displayAll, categoryIndex, message;

  o = new Array(availableProductTypes.length);
  p = 0;

  allHidden = allCategoriesHidden();
  displayAll = allHidden || allCategoriesShown();
  hasProductTypes = false;
  for (i = 0; i < availableProductTypes.length; i++)
  {
    // Only display product types in the selected category. Note that if all categories are hidden,
    // that will in fact cause all of them to be displayed, instead.
    categoryIndex = Utility.getCategoryIndex(availableProductTypes[i][c.apt.CATEGORY_ID]);
    if ((categoryIndex >= 0) && !categoryDisplayed[categoryIndex] && !allHidden)
      continue;

    hasProductTypes = true;
    if (availableProductTypes[i][c.apt.IS_AVAILABLE])
      o[p++] = getAvailableProductType(i);
    else
    {
      if (maskUnavailableStatus())
        o[p++] = getMaskedUnavailableProductType(i);
      else
        o[p++] = getUnavailableProductType(i);
    }
  }

  // If we had product types, display the list. If not, just display a message.
  if (hasProductTypes)
  {
    productTypesBox.innerHTML = o.join('');

    // Obtain pointers to user interface elements.
    Utility.readPointers(['alternativeLocationCombo']);
  }
  else
  {
    // If all product types are displayed (or hidden, which has the effect of displaying them all
    // anyway), and there were no product types to display, the location is full. Otherwise, the
    // list may be empty because the list is filtered. Display appropriate message.
      // *** // Display the message as status-red?
    if (displayAll)
      message = getText(15, 'Lageret er dessverre fullt på valgt dato.');
    else
      message = getText(37,
        'Ingen ledige lagerboder av valgt kategori. Velg flere kategorier, eller en annen innflyttingsdato.');
    productTypesBox.innerHTML = '<div class="form-element">' + message + '</div>';
  }
  // Display or hide the reset filter button. The button is visible if the filter displays some,
  // but not all, of the categories.
  Utility.setDisplayState(resetFilterButton, !displayAll);
}

// *************************************************************************************************
// Return HTML code to display an available product type that is, uh, available - meaning that the
// product type exists at the selected location, and there are free products that can be booked. The
// product type in question has the given index in the availableProductTypes table. This method
// performs no error checking on index.
function getAvailableProductType(index)
{
  var o, p, capacityPrice, modifiedPrice, priceMods;

  o = new Array(28);
  p = 0;

  // Write name. Left aligned.
  o[p++] = '<div class="button-box product-type-box"><div class="product-type-box-left"><h3>';
  o[p++] = availableProductTypes[index][c.apt.NAME];
  o[p++] = '</h3>';
  // Add the "few available" notice, if appropriate.
  if ((settings.fewAvailableCount > 0) &&
    (availableProductTypes[index][c.apt.AVAILABLE_PRODUCT_COUNT] <= settings.fewAvailableCount))
  {
    o[p++] = '<span class="status-label status-yellow"><i class="fa-solid fa-triangle-exclamation"></i> ';
    o[p++] = getText(7, 'Kun $1 igjen.',
      [String(availableProductTypes[index][c.apt.AVAILABLE_PRODUCT_COUNT])]);
    o[p++] = '</span>';
  }
  o[p++] = '</div>';

  // Write price. Right aligned.
  o[p++] = '<div class="product-type-box-right">';
  capacityPrice = availableProductTypes[index][c.apt.PRICE];
  if (hasPriceMods(index))
  {
    // The product type is affected by price mods.
    priceMods = availableProductTypes[index][c.apt.PRICE_MODS];
    modifiedPrice = Utility.getModifiedPrice(capacityPrice, priceMods[0][c.apt.PRICE_MOD]);
    // Write the capacity price as the superseded price, except if the modified price is higher. In
    // that case, we don't want to advertise it.
    if (modifiedPrice < capacityPrice)
    {
      o[p++] = '<span class="superseded-price">';
      o[p++] = getText(30, '$1 kr', [String(capacityPrice)]);
      o[p++] = getText(13, ' / mnd');
      o[p++] = '</span><br>';
    }
    // Write the first price mod as the new monthly price.
    o[p++] = '<span class="price profile-text-colour">';
    o[p++] = getText(30, '$1 kr', [String(modifiedPrice)]);
    o[p++] = '</span>';
    o[p++] = getText(13, ' / mnd');
  }
  else
  {
    // The product type is not affected by price mods. Write the capacity price as the monthly
    // price.
    o[p++] = '<span class="price profile-text-colour">';
    o[p++] = getText(30, '$1 kr', [String(capacityPrice)]);
    o[p++] = '</span>';
    o[p++] = getText(13, ' / mnd');
  }
  o[p++] = '</div><br>';

  // Write price information button. Left aligned.
  o[p++] = '<div class="product-type-box-left">';
  o[p++] = getText(12, 'Prisinformasjon:');
  o[p++] = '&nbsp;&nbsp;<a href="javascript:void(0);" class="info-button" onclick="displayPriceInformationDialogue(';
  o[p++] = String(index);
  o[p++] = ');"><i class="fa-solid fa-circle-info"></i></a></div>';

  // Write select button. Right aligned.
  o[p++] = '<div class="product-type-box-right"><button type="button" class="wide-button" onclick="selectProductType(';
  o[p++] = String(index);
  o[p++] = ');">';
  o[p++] = getText(0, 'Velg');
  o[p++] = '&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button></div>';

  o[p++] = '</div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code to display an available product type that is, in fact, unavailable - in that it
// does exist at the selected location, but there are no free products, so the product type cannot
// be booked. The product type in question has the given index in the availableProductTypes table.
// This method performs no error checking on index.
//
// This method displays the product type to look like it's available, and redirects to the submit
// request page when selected.
function getMaskedUnavailableProductType(index)
{
  var o, p;

  o = new Array(11);
  p = 0;

  // Write name. Left aligned. Terminate the line afterwards.
  o[p++] = '<div class="button-box product-type-box"><div class="product-type-box-left"><h3>';
  o[p++] = availableProductTypes[index][c.apt.NAME];
  o[p++] = '</h3></div><br>';

  // Write empty box. Left aligned.
  o[p++] = '<div class="product-type-box-left">&nbsp;</div>';

  // Write select button. Right aligned.
  o[p++] = '<div class="product-type-box-right"><button type="button" class="wide-button" onclick="submitRequestFor(';
  o[p++] = String(selectedLocation);
  o[p++] = ', ';
  o[p++] = String(index);
  o[p++] = ');">';
  o[p++] = getText(0, 'Velg');
  o[p++] = '&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button></div></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code to display an available product type that is, in fact, unavailable - in that it
// does exist at the selected location, but there are no free products, so the product type cannot
// be booked. The product type in question has the given index in the availableProductTypes table.
// This method performs no error checking on index.
//
// This method displays the product type to look like it's unavailable, and offers alternatives
// where possible.
function getUnavailableProductType(index)
{
  var o, p, freeFromDate;

  o = new Array(35);
  p = 0;

  // Write name. Left aligned.
  o[p++] = '<div class="button-box product-type-box product-type-unavailable"><div class="product-type-box-left"><h3>';
  o[p++] = availableProductTypes[index][c.apt.NAME];
  o[p++] = '</h3></div>';

  // Write empty box, as there is no price to be displayed. Right aligned.
  o[p++] = '<div class="product-type-box-right">&nbsp;</div><br>';

  // Write "none available" label. Left aligned.
  o[p++] = '<div class="product-type-box-left">';
  o[p++] = getText(8, 'Ingen ledige lagerboder.');
  o[p++] = '</div>';

  // Write submit request button. Right aligned.
  o[p++] = '<div class="product-type-box-right"><button type="button" class="wide-button" onclick="Utility.displaySpinnerThenGoTo(\'/subscription/html/submit_request.php?location_id=';
  o[p++] = String(locations[selectedLocation][c.loc.ID]);
  o[p++] = '&category_id=';
  o[p++] = String(availableProductTypes[index][c.apt.CATEGORY_ID]);
  o[p++] = '&start_date=';
  o[p++] = calendar.selectedDate;
  o[p++] = '\');"><i class="fa-solid fa-envelope"></i>&nbsp;&nbsp;';
  o[p++] = getText(39, 'Send foresp&oslash;rsel');
  o[p++] = '</button></div><br>';

  // If the storage unit type will be available in the future, add a line with the option to change the start date.
  freeFromDate = availableProductTypes[index][c.apt.FIRST_AVAILABLE_DATE];
  if (freeFromDate !== null)
  {
    // Write "free from" date. Left aligned.
    o[p++] = '<div class="product-type-box-left separator-above">';
    o[p++] = getText(9, 'Ledig her, fra $1.', [freeFromDate]);
    o[p++] = '</div>';

    // Write change date button. Right aligned.
    o[p++] = '<div class="product-type-box-right separator-above"><button type="button" class="wide-button" onclick="selectStartDate(\'';
    o[p++] = freeFromDate;
    o[p++] = '\');"><i class="fa-solid fa-calendar-days"></i>&nbsp;&nbsp;';
    o[p++] = getText(10, 'Endre innflyttingsdato');
    o[p++] = '</button></div><br>';
  }

  if (availableProductTypes[index][c.apt.ALTERNATIVE_LOCATION_IDS].length <= 0)
  {
    // The product type is not available elsewhere. Write a text to say so. Full width.
    o[p++] = '<div class="product-type-box-full-width separator-above">';
    o[p++] = getText(11, 'Ingen andre lager har ledig lagerbod av denne typen.');
    o[p++] = '</div>';
  }
  else
  {
    // The product type is available elsewhere. Write a combo box to choose an alternate
    // location. Left aligned.
    o[p++] = '<div class="product-type-box-left separator-above">';
    o[p++] = getText(44, 'Ledig ved:');
    o[p++] = '&nbsp;&nbsp;';
    o[p++] = getAlternativeLocationCombo(index);
    o[p++] = '</div>';

    // Write change location button. Right aligned.
    o[p++] = '<div class="product-type-box-right separator-above"><button type="button" class="wide-button" onclick="selectLocation(alternativeLocationCombo.value, ';
    o[p++] = String(index);
    o[p++] = ');"><i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;';
    o[p++] = getText(14, 'Bytt lager');
    o[p++] = '</button></div>';
  }
  o[p++] = '</div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML for a combo box that holds the names of locations that have free products of the
// product type with the given index in the availableProductTypes table. The index of the location
// will be used as the value of each item (not the ID, as we are not submitting the form).
function getAlternativeLocationCombo(index)
{
  var o, p, i, alternativeCount, locationIndex;

  alternativeCount = availableProductTypes[index][c.apt.ALTERNATIVE_LOCATION_IDS].length;
  o = new Array((alternativeCount * 5) + 2);
  p = 0;

  o[p++] = '<select id="alternativeLocationCombo">';
  for (i = 0; i < alternativeCount; i++)
  {
    locationIndex = Utility.getLocationIndex(
      availableProductTypes[index][c.apt.ALTERNATIVE_LOCATION_IDS][i]);
    if (locationIndex < 0)
      continue;
    o[p++] = '<option value="';
    o[p++] = String(locationIndex);
    o[p++] = '">';
    o[p++] = locations[locationIndex][c.loc.NAME];
    o[p++] = '</option>';
  }
  o[p++] = '</select>';

  return o.join('');
}

// *************************************************************************************************
// Open the price information dialogue box, and display a text that describes a price plan. index is
// the index of the product type in the availableProductTypes table.
function displayPriceInformationDialogue(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, availableProductTypes))
    return;

  o = new Array(4);
  p = 0;

  o[p++] = '<div class="form-element">';
  o[p++] = getRentDescription(index);
  o[p++] = getInsuranceDescription();
  o[p++] = '</div>';

  priceInformationDialogueContent.innerHTML = o.join('');

  Utility.display(overlay);
  Utility.display(priceInformationDialogue);
}

// *************************************************************************************************

function closePriceInformationDialogue()
{
  Utility.hide(priceInformationDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Return a text that describes a rent price plan. index is the index of the product type in the
// availableProductTypes table.
  // *** // Replace numbers with words if the number is less than or equal to 5?
function getRentDescription(index)
{
  var o, p, i, capacityPrice, priceMods;

  // If no price mods are present, return a description of the capacity price.
  capacityPrice = availableProductTypes[index][c.apt.PRICE];
  if (!hasPriceMods(index))
    return getText(35, 'Leie: ') + getText(16, '$1 kr pr måned', [String(capacityPrice)]);
  priceMods = availableProductTypes[index][c.apt.PRICE_MODS];

  o = new Array((priceMods.length * 3) + 7);
  p = 0;

  // Compose the first price mod description line. This line is different, as we are not dealing
  // with a full month to begin with.
  o[p++] = getText(35, 'Leie: ');
  o[p++] = '<ul><li class="offer">';
  o[p++] = describeFirstPriceMod(capacityPrice, priceMods[0][c.apt.DURATION],
    priceMods[0][c.apt.PRICE_MOD]);
  o[p++] = '</li>';

  // Compose the rest of the lines, if any.
  for (i = 1; i < priceMods.length; i++)
  {
    o[p++] = '<li class="offer">';
    o[p++] = describePriceMod(capacityPrice, priceMods[i][c.apt.DURATION],
      priceMods[i][c.apt.PRICE_MOD]);
    o[p++] = '</li>';
  }

  // Compose the final line, with the permanent price.
  o[p++] = '<li>';
  o[p++] = getText(17, 'Deretter $1 kr pr måned', [String(capacityPrice)]);
  o[p++] = '</li></ul>';

  return o.join('');
}

// *************************************************************************************************
// Return a text that describes an insurance price plan. If no insurance has been selected, the
// method will return an empty string.
function getInsuranceDescription()
{
  var insurancePerMonth;

  if (selectedInsurance < 0)
    insurancePerMonth = 0;
  else
    insurancePerMonth = insuranceProducts[selectedInsurance][c.ins.PRICE];

  if (insurancePerMonth > 0)
    return '<br>' + getText(36, 'Forsikring: $1 kr pr måned', [String(insurancePerMonth)]);
  return '';
}

// *************************************************************************************************
// Return a text description of the price mod with the given duration and modifier. The mod is
// assumed to be the first mod in a price plan. capacityPrice is the price to which the modifier is
// applied.
function describeFirstPriceMod(capacityPrice, duration, modifier)
{
  var o, p, modifiedPrice;

  o = new Array(3);
  p = 0;

  modifiedPrice = String(Utility.getModifiedPrice(capacityPrice, modifier));
  if (duration === 1)
  {
    // The first price mod applies to the first month only. Describe the price and duration.
    if (isFree(modifier))
      o[p++] = getText(18, 'Resten av måneden gratis!');
    else
      o[p++] = getText(19, '$1 kr pr måned resten av måneden', [modifiedPrice]);
  }
  else
  {
    // The first price mod applies for several months. Describe the price.
    if (isFree(modifier))
      o[p++] = getText(20, 'Gratis resten av måneden');
    else
      o[p++] = getText(19, '$1 kr pr måned resten av måneden', [modifiedPrice]);
    // Describe the duration.
    if (duration === 2)
      o[p++] = getText(22, ', og i én måned til');
    else
      o[p++] = getText(23, ', og i $1 måneder til', [String(duration - 1)]);
  }
  // Describe the discount.
  o[p++] = getDiscountDescription(modifier);

  return o.join('');
}

// *************************************************************************************************
// Return a text description of the price mod with the given duration and modifier. The mod is
// assumed to not be the first mod in a price plan, as those have different texts. capacityPrice is
// the price to which the modifier is applied.
function describePriceMod(capacityPrice, duration, modifier)
{
  var o, p, modifiedPrice;

  o = new Array(2);
  p = 0;

  modifiedPrice = String(Utility.getModifiedPrice(capacityPrice, modifier));
  if (duration === 1)
  {
    // The price mod applies for a single month. Describe the price and duration.
    if (isFree(modifier))
      o[p++] = getText(24, 'Én måned gratis!');
    else
      o[p++] = getText(25, '$1 kr pr måned i én måned', [modifiedPrice]);
  }
  else
  {
    // The price mod applies for several months. Describe the price and duration.
    if (isFree(modifier))
      o[p++] = getText(26, 'Gratis i $1 måneder', [String(duration)]);
    else
      o[p++] = getText(27, '$1 kr pr måned i $2 måneder',
        [modifiedPrice, String(duration)]);
  }
  // Describe the discount.
  o[p++] = getDiscountDescription(modifier);

  return o.join('');
}

// *************************************************************************************************
// Return true if the given price modifier says the price is zero, that is, it's free. This is true
// if the price modifier gives a 100% discount (or more, although that is not supposed to happen).
function isFree(modifier)
{
  return modifier <= -100;
}

// *************************************************************************************************
// Return true if the given price modifier does not represent a discount. This is true if the
// modifier is 0 or higher.
function isIncrease(modifier)
{
  return modifier >= 0;
}

// *************************************************************************************************
// Return a text description of the given price modifier, in parentheses. The function returns
// nothing if the discount is 100%, as that is presumed to be described elsewhere. Also, the
// function returns nothing if the price modifier represents an increase, as we don't particularly
// want to brag about that.
function getDiscountDescription(modifier)
{
  if (isFree(modifier) || isIncrease(modifier))
    return '';
  return getText(28, ' ($1% rabatt)', [String(-modifier)]);
}

// *************************************************************************************************
// Select the product type with the given index in the availableProductTypes table, then move on to
// the insurance tab.
function selectProductType(index)
{
  var index;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, availableProductTypes) &&
    availableProductTypes[index][c.apt.IS_AVAILABLE])
  {
    selectedProductType = index;
    displayInsuranceTab();
  }
}

// *************************************************************************************************
// Select the given start date, then update the calendar. This will, in turn, load the list of
// available product types on that date and update the user interface. startDate should be a string
// in the format "yyyy-mm-dd".
function selectStartDate(startDate)
{
  calendar.selectedDate = startDate;
}

// *************************************************************************************************
// Set all categories in the category filter to not be displayed. Interestingly, this will cause
// them all to be displayed.
function hideAllCategories()
{
  var i;
  
  for (i = 0; i < categories.length; i++)
    categoryDisplayed[i] = false;
}

// *************************************************************************************************
// Set all categories in the category filter to be displayed.
function displayAllCategories()
{
  var i;
  
  for (i = 0; i < categories.length; i++)
    categoryDisplayed[i] = true;
}

// *************************************************************************************************
// Return true if all categories in the category filter are set to not be displayed. Interestingly,
// this will cause them all to be displayed.
function allCategoriesHidden()
{
  var i;
  
  for (i = 0; i < categories.length; i++)
  {
    if (categoryDisplayed[i])
      return false;
  }
  return true;
}

// *************************************************************************************************
// Return true if all categories in the category filter are set to be displayed.
function allCategoriesShown()
{
  var i;
  
  for (i = 0; i < categories.length; i++)
  {
    if (!categoryDisplayed[i])
      return false;
  }
  return true;
}

// *************************************************************************************************
// Display the dialogue which allows the user to filter on categories.
function displayCategoryFilterDialogue()
{
  // Write the contents of the filter dialogue.
  displayCategoryFilterContents();

  // Display the filter dialogue.
  Utility.display(overlay);
  Utility.display(editCategoryFilterDialogue);
}

// *************************************************************************************************
// Write the contents of the filter dialogue.
function displayCategoryFilterContents()
{
  var o, p, i;
  
  o = new Array(categories.length * 9);
  p = 0;

  for (i = 0; i < categories.length; i++)
  {
    o[p++] = '<div class="button-box category"><div class="button-box-left category-left"><label for="categoryCheckbox_';
    o[p++] = String(i);
    o[p++] = '">';
    o[p++] = categories[i][c.cat.NAME];
    o[p++] = '</label></div><div class="button-box-right category-right"><input type="checkbox" id="categoryCheckbox_';
    o[p++] = String(i);
    o[p++] = '"';
    if (categoryDisplayed[i])
      o[p++] = ' checked';
    o[p++] = '></div></div>';
  }

  editCategoryFilterDialogueContent.innerHTML = o.join('');
}

// *************************************************************************************************

function clearCategoryFilter()
{
  hideAllCategories();
  displayAvailableProductTypes();
}

// *************************************************************************************************

function selectAllCategories()
{
  displayAllCategories();
  displayCategoryFilterContents();
}

// *************************************************************************************************

function deselectAllCategories()
{
  hideAllCategories();
  displayCategoryFilterContents();
}

// *************************************************************************************************
// Update the category filter, then close the category filter dialogue.
function updateCategoryFilter()
{
  var i, box;

  // Read the contents of the filter dialogue.
  for (i = 0; i < categories.length; i++)
  {
    box = document.getElementById('categoryCheckbox_' + String(i));
    if (box)
      categoryDisplayed[i] = box.checked;
  }

  // Hide the filter dialogue.
  closeCategoryFilterDialogue();

  // Update the list of product types.
  displayAvailableProductTypes();
}

// *************************************************************************************************
// Close the filter dialogue.
function closeCategoryFilterDialogue()
{
  Utility.hide(editCategoryFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Return true if the current settings specify that an unavailable product type at the currently
// selected location should be shown as available, but with a link to submit a request.
function maskUnavailableStatus()
{
  // The status should be masked if we do it everywhere, or if we do it at the selected location.
  return (settings.fullMode === FULL_MODE_REQUEST) ||
    ((settings.fullMode === FULL_MODE_REQUEST_AT_SOME_LOCATIONS) &&
    settings.fullModeLocations.includes(locations[selectedLocation][c.loc.ID]));
}

// *************************************************************************************************
// *** Insurance tab functions.
// *************************************************************************************************
// Initialise and display the select insurance tab, if it exists. If not, move on to the summary
// tab.
function displayInsuranceTab()
{
  if (insuranceTabIndex < 0)
    displaySummaryTab();
  else
  {
    selectedInsurance = -1;
    progressTabset.activeTabIndex = insuranceTabIndex;
    displayInsuranceBox();
  }
}

// *************************************************************************************************
// Display the list of eligible insurance products. Insurances are not always offered at all
// locations, or for all product types. The list will only display the insurances available for the
// selected product type at the selected location. If none are available, the summary tab will be
// displayed instead.
function displayInsuranceBox()
{
  var o, p, i, displayedCount;
  
  displayedCount = 0;
  o = new Array(insuranceProducts.length * 13);
  p = 0;

  for (i = 0; i < insuranceProducts.length; i++)
  {
    if (!availableForProductType(i, selectedProductType) ||
      !availableForLocation(i, selectedLocation))
      continue;
    displayedCount++;

    o[p++] = '<div class="button-box insurance"><div class="button-box-left insurance-left"><h3>';
    o[p++] = insuranceProducts[i][c.ins.NAME];
    o[p++] = '</h3><p>';
    o[p++] = insuranceProducts[i][c.ins.DESCRIPTION];
    o[p++] = '</p></div><div class="button-box-right insurance-right">';
    if (insuranceProducts[i][c.ins.PRICE] > 0)
    {
      o[p++] = '<p>';
      o[p++] = getText(16, '$1 kr pr måned', [String(insuranceProducts[i][c.ins.PRICE])]);
      o[p++] = '</p>';
    }
    o[p++] = '<button type="button" onclick="selectInsurance(';
    o[p++] = String(i);
    o[p++] = ');">';
    o[p++] = getText(0, 'Velg');
    o[p++] = '&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button></div></div>';
  }

  if (displayedCount <= 0)
    displaySummaryTab();
  insuranceBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Return true if the insurance product with the given index in the insuranceProducts table is
// available for the product type with the given index in the productTypes table.
//
// The insurance is available if a) the insurance is available for all product types, in which case
// the forProductTypes table is null, or b) the product type's ID appears in the insurance product's
// forProductTypes table.
function availableForProductType(insuranceIndex, productTypeIndex)
{
  var forProductTypes;

  forProductTypes = insuranceProducts[insuranceIndex][c.ins.FOR_PRODUCT_TYPES];
  return (forProductTypes === null) ||
    (Utility.valueInArray(availableProductTypes[productTypeIndex][c.apt.ID], forProductTypes));
}

// *************************************************************************************************
// Return true if the insurance product with the given index in the insuranceProducts table is
// available for the location with the given index in the locations table.
//
// The insurance is available if a) the insurance is available for all locations, in which case the
// forLocations table is null, or b) the location's ID appears in the insurance product's
// forLocations table.
function availableForLocation(insuranceIndex, locationIndex)
{
  var forLocations;

  forLocations = insuranceProducts[insuranceIndex][c.ins.FOR_LOCATIONS];
  return (forLocations === null) ||
    (Utility.valueInArray(locations[locationIndex][c.loc.ID], forLocations));
}

// *************************************************************************************************

function selectInsurance(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, insuranceProducts))
  {
    selectedInsurance = index;
    displaySummaryTab();
  }
}

// *************************************************************************************************
// *** Summary tab functions.
// *************************************************************************************************
// Display the summary tab and its contents.
function displaySummaryTab()
{
  displaySummaryBox();
  displayUserInfoBox();
  displayPaymentMethods();
  progressTabset.activeTabIndex = summaryTabIndex;
  enableConfirmBookingButton();
}

// *************************************************************************************************

function displaySummaryBox()
{
  var o, p, startDate, lastDayOfMonth, daysLeft, thisMonth, nextMonth, rentThisMonth, rentNextMonth,
    insuranceThisMonth, insurancePerMonth, finalPricePerMonth, twoMonths;

  // Calculate prices for rent and insurance. It is possible that no insurance products exist, in
  // which case no insurance will have been selected.
  if (selectedInsurance < 0)
    insurancePerMonth = 0;
  else
    insurancePerMonth = insuranceProducts[selectedInsurance][c.ins.PRICE];
  finalPricePerMonth = insurancePerMonth + availableProductTypes[selectedProductType][c.apt.PRICE];
  startDate = new Date(calendar.selectedDate);
  thisMonth = new CalendarMonth(startDate.getFullYear(), startDate.getMonth(), MONTH_NAMES,
    MONTH_NAMES_IN_SENTENCE);
  twoMonths = startDate.getDate() !== 1;
  if (twoMonths)
  {
    // We are charging for the rest of this month, as well as all of next month.
    // Get the last day of the month.
    var endDate = new Date(thisMonth.year, thisMonth.month + 1, 0);
    lastDayOfMonth = endDate.getDate();
    // Calculate days left in the month.
    daysLeft = lastDayOfMonth - startDate.getDate() + 1;

    rentThisMonth = Math.floor((daysLeft / lastDayOfMonth) * getPriceFirstMonth());
    rentNextMonth = getPriceSecondMonth();
    insuranceThisMonth = Math.floor((daysLeft / lastDayOfMonth) * insurancePerMonth);
    // Insurance next month is just the price for a full month.
    nextMonth = thisMonth.getNextMonth();
  }
  else
  {
    // We are at the start of a month, and will be charging for next month only.
    rentThisMonth = 0;
    rentNextMonth = getPriceFirstMonth();
    insuranceThisMonth = 0;
    // Insurance next month is just the price for a full month.
    nextMonth = thisMonth;
  }
  
  o = new Array(49);
  p = 0;

  // Location name, product type name, start date.
  o[p++] = '<div class="separator-box"><h2>';
  o[p++] = locations[selectedLocation][c.loc.NAME];
  o[p++] = ', ';
  o[p++] = availableProductTypes[selectedProductType][c.apt.NAME];
  o[p++] = '</h2><p class="start-date">';
  o[p++] = getText(21, 'Innflyttingsdato: $1', [calendar.selectedDate]);
  o[p++] = '</p></div>';

  o[p++] = '<table cellspacing="0" cellpadding="0"><tbody>';
  if (twoMonths)
  {
    // Storage price this month.
    o[p++] = '<tr><td class="rent';
    if (insurancePerMonth > 0)
      o[p++] = ' no-separator';
    o[p++] = '">';
    o[p++] = getText(29, 'Leie, $3 ($1 av $2 dager)',
      [String(daysLeft), String(lastDayOfMonth), thisMonth.displayNameInSentence]);
    o[p++] = '</td><td class="rent amount';
    if (insurancePerMonth > 0)
      o[p++] = ' no-separator';
    o[p++] = '">';
    o[p++] = getText(30, '$1 kr', [String(rentThisMonth)]);
    o[p++] = '</td></tr>';

    // Insurance price this month.
    if (insurancePerMonth > 0)
    {
      o[p++] = '<tr><td class="insurance-description">';
      o[p++] = getText(31, 'Forsikring');
      o[p++] = '</td><td class="insurance amount">';
      o[p++] = getText(30, '$1 kr', [String(insuranceThisMonth)]);
      o[p++] = '</td></tr>';
    }
  }
  // Storage price next month.
  o[p++] = '<tr><td class="rent';
  if (insurancePerMonth > 0)
    o[p++] = ' no-separator';
  o[p++] = '">';
  o[p++] = getText(32, 'Leie, $1', [nextMonth.displayNameInSentence]);
  o[p++] = '</td><td class="rent amount';
  if (insurancePerMonth > 0)
    o[p++] = ' no-separator';
  o[p++] = '">';
  o[p++] = getText(30, '$1 kr', [String(rentNextMonth)]);
  o[p++] = '</td></tr>';
  // Insurance next month.
  if (insurancePerMonth > 0)
  {
    o[p++] = '<tr><td class="insurance-description">';
    o[p++] = getText(31, 'Forsikring');
    o[p++] = '</td><td class="insurance amount">';
    o[p++] = getText(30, '$1 kr', [String(insurancePerMonth)]);
    o[p++] = '</td></tr>';
  }
  // Sum.
  o[p++] = '<tr><td class="sum">';
  o[p++] = getText(33, 'Til betaling nå');
  o[p++] = '</td><td class="amount sum">'
  o[p++] = getText(30, '$1 kr',
    [String(rentThisMonth + rentNextMonth + insuranceThisMonth + insurancePerMonth)]);
  o[p++] = '</td></tr>';
  // Permanent price. This is only displayed if the initial price is affected by price mods.
  // Otherwise, the explanation is not required, as the figures are already displayed.
  if (hasPriceMods())
  {
    o[p++] = '<tr><td class="permanent-price">';
    o[p++] = getText(34, 'Pris etter kampanjeperioden');
    o[p++] = '</td><td class="permanent-price amount">';
    o[p++] = getText(16, '$1 kr pr måned', [String(finalPricePerMonth)])
    o[p++] = '&nbsp;&nbsp;<a onclick="displayPriceInformationDialogue(';
    o[p++] = String(selectedProductType);
    o[p++] = ');" class="info-button"><i class="fa-solid fa-circle-info"></i></a></td></tr>';
  }
  o[p++] = '</table>';

  summaryBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Return the price per month for the first month of the subscription.
function getPriceFirstMonth()
{
  var capacityPrice, priceMods;

  // If no special offer applies, just return the capacity price.
  capacityPrice = availableProductTypes[selectedProductType][c.apt.PRICE];
  if (!hasPriceMods())
    return capacityPrice;
  priceMods = availableProductTypes[selectedProductType][c.apt.PRICE_MODS];

  // We have a special offer. The first mod will apply to the first month, so use that.
  return Utility.getModifiedPrice(capacityPrice, priceMods[0][c.apt.PRICE_MOD]);
}

// *************************************************************************************************
// Return the price per month for the second month of the subscription.
function getPriceSecondMonth()
{
  var capacityPrice, priceMods;

  // If no special offer applies, just return the capacity price.
  capacityPrice = availableProductTypes[selectedProductType][c.apt.PRICE];
  if (!hasPriceMods())
    return capacityPrice;
  priceMods = availableProductTypes[selectedProductType][c.apt.PRICE_MODS];

  // We have a special offer. If the first price mod applies to the second month as well, use that.
  if (priceMods[0][c.apt.DURATION] > 1)
    return Utility.getModifiedPrice(capacityPrice, priceMods[0][c.apt.PRICE_MOD])

  // If the special offer only had one modifier, and it didn't apply to the second month, return
  // the capacity price.
  if (priceMods.length <= 1)
    return capacityPrice;

  // The second price mod will apply to the second month, so use that.
  return Utility.getModifiedPrice(capacityPrice, priceMods[1][c.apt.PRICE_MOD]);
}

// *************************************************************************************************
// Update the contents of the user info box. When the page is loaded, the box may come with a user
// interface to enter information about a new user. If the user is logged in, this interface is not
// needed (and will not be needed at any point, as the user cannot log out). If so, replace the
// contents of the box with a message to say who is logged in.
function displayUserInfoBox()
{
  var o, p;

  if (isLoggedIn)
  {
    o = new Array(3);
    p = 0;
    o[p++] = '<div class="form-element"><p>';
    o[p++] = getText(38, 'Logget inn som $1. Velkommen!', [user.name]);
    o[p++] = '</p></div>';
    userInfoBox.innerHTML = o.join('');
  }
}

// *************************************************************************************************
// Handle a change to the entity type, and update the user interface accordingly. Different fields
// are displayed, depending on whether the new user is a company or a private individual.
function selectEntityType()
{
  setEntityType(parseInt(Utility.getRadioButtonValue('entity_type', -1), 10));
}

// *************************************************************************************************
// Set a new value for the entity type, and update the user interface.
function setEntityType(newValue)
{
  // Validate new value. The existing value will not be updated unless it has changed.
  newValue = parseInt(newValue, 10);
  if (isFinite(newValue) && (newValue >= ENTITY_TYPE_INDIVIDUAL) &&
    (newValue <= ENTITY_TYPE_COMPANY) && (newValue !== selectedEntityType))
  {
    selectedEntityType = newValue;
    if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
      newIndividualButton.checked = true;
    else
      newCompanyButton.checked = true;
    Utility.setDisplayState(individualDataBox, selectedEntityType === ENTITY_TYPE_INDIVIDUAL);
    Utility.setDisplayState(companyDataBox, selectedEntityType === ENTITY_TYPE_COMPANY);
    displayPaymentMethods();
    enableConfirmBookingButton();
  }
}

// *************************************************************************************************
// Return the payment methods for the currently selected entity type.
function getPaymentMethods()
{
  if (selectedEntityType === ENTITY_TYPE_COMPANY)
    return settings.paymentMethodsCompany;
  return settings.paymentMethodsPrivate;
}

// *************************************************************************************************
// Display or hide the payment method box, depending on the entity type of the currently logged-in
// user, or - if the user is not logged in - the entity type selected in the user information box.
// If a user with the selected entity type has only one available payment method, hide the box. If
// he has several, display the box and list the alternatives, with the first one being selected by
// default.
function displayPaymentMethods()
{
  var o, p, paymentMethods, isFirst;

  // Find the payment methods that are available to the current type of user.
  paymentMethods = getPaymentMethods();

  // If there is only one payment methods (or none, which should never happen), hide the box.
  if (paymentMethods.length <= 1)
    Utility.hide(paymentMethodBox);
  else
  {
    // Display the box with appropriate contents.
    isFirst = true;
    o = new Array(25);
    p = 0;

    o[p++] = '<div class="separator-box"><h2>';
    o[p++] = getText(40, 'Betalingsmåte');
    o[p++] = '</h2></div>';

    if (paymentMethods.includes(PAYMENT_METHOD_NETS))
    {
      o[p++] = '<div class="form-element"><label><input type="radio" name="payment_method" value="';
      o[p++] = String(PAYMENT_METHOD_NETS);
      o[p++] = '"';
      if (isFirst)
      {
        o[p++] = ' checked';
        isFirst = false;
      }
      o[p++] = '> ';
      o[p++] = getText(41, 'Betal med kort (Visa eller Mastercard)');
      o[p++] = Utility.getMandatoryMark();
      o[p++] = '</label></div>';
    }
    if (paymentMethods.includes(PAYMENT_METHOD_INVOICE))
    {
      o[p++] = '<div class="form-element"><label><input type="radio" name="payment_method" value="';
      o[p++] = String(PAYMENT_METHOD_INVOICE);
      o[p++] = '"';
      if (isFirst)
      {
        o[p++] = ' checked';
        isFirst = false;
      }
      o[p++] = '> ';
      o[p++] = getText(42, 'Motta faktura');
      o[p++] = Utility.getMandatoryMark();
      o[p++] = '</label></div>';
    }
    if (paymentMethods.includes(PAYMENT_METHOD_NETS_THEN_INVOICE))
    {
      o[p++] = '<div class="form-element"><label><input type="radio" name="payment_method" value="';
      o[p++] = String(PAYMENT_METHOD_NETS_THEN_INVOICE);
      o[p++] = '"';
      if (isFirst)
      {
        o[p++] = ' checked';
        isFirst = false;
      }
      o[p++] = '> ';
      o[p++] = getText(43, 'Betal med kort nå, deretter faktura');
      o[p++] = Utility.getMandatoryMark();
      o[p++] = '</label></div>';
    }

    paymentMethodBox.innerHTML = o.join('');
    Utility.display(paymentMethodBox);
  }
}

// *************************************************************************************************
// Enable or disable the confirm booking button, depending on whether the user has filled in all
// the required information, and checked the "I accept the terms and conditions" checkbox.
function enableConfirmBookingButton()
{
  // If you are logged in, the summary page only displays the terms and conditions checkbox, which
  // has to be checked in order to proceed.
  if (isLoggedIn)
    confirmBookingButton.disabled = !acceptTermsCheckbox.checked;
  else
  {
    // The user is not logged in. He has to fill in all the mandatory user information fields, as
    // well as check the terms and conditions checkbox. There are different fields, depending on
    // whether the new user is a company or an individual.
    if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
    {
      confirmBookingButton.disabled = !acceptTermsCheckbox.checked ||
        (newFirstNameEdit.value === '') || (newLastNameEdit.value === '') ||
        !Utility.isValidEMail(newUserNameEdit.value) || (newPhoneEdit.value === '') ||
        (newAddressEdit.value === '') || (newPostcodeEdit.value === '') ||
        (newAreaEdit.value === ''); // || (newPasswordEdit.value.length < PASSWORD_MIN_LENGTH);
    }
    else
    {
      confirmBookingButton.disabled = !acceptTermsCheckbox.checked ||
        (newCompanyNameEdit.value === '') || (newCompanyIdEdit.value === '') ||
        !Utility.isValidEMail(newUserNameEdit.value) || (newPhoneEdit.value === '') ||
        (newAddressEdit.value === '') || (newPostcodeEdit.value === '') ||
        (newAreaEdit.value === ''); // || (newPasswordEdit.value.length < PASSWORD_MIN_LENGTH);
    }
  }
}

// *************************************************************************************************
// Ensure the user is logged in, then create a subscription.
function confirmBooking()
{
  if (isLoggedIn)
    createSubscription();
  else
    createUser();
}

// *************************************************************************************************
// Display the spinner. Once visible, create a new user.
function createUser()
{
  Utility.displaySpinnerThen(doCreateUser);
}

// *************************************************************************************************
// Submit an asynchronous request to create a new user.
function doCreateUser()
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('entity_type', selectedEntityType);
  if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
  {
    requestData.append('first_name', newFirstNameEdit.value);
    requestData.append('last_name', newLastNameEdit.value);
  }
  else
  {
    requestData.append('company_name', newCompanyNameEdit.value);
    requestData.append('company_id_number', newCompanyIdEdit.value);
  }
  requestData.append('user_name', newUserNameEdit.value);
  requestData.append('phone', newPhoneEdit.value);
  requestData.append('address', newAddressEdit.value);
  requestData.append('postcode', newPostcodeEdit.value);
  requestData.append('area', newAreaEdit.value);
  // requestData.append('password', newPasswordEdit.value);
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/create_user.php', options)
    .then(Utility.extractJson)
    .then(confirmUserCreated)
    .catch(logCreateUserError);
}

// *************************************************************************************************
// Receive the response to the request to create a new user. Display an error message, or - if the
// request succeeded - move on to create a subscription.
function confirmUserCreated(data)
{
  var resultCode;

  Utility.hideSpinner();

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Assume something failed.
  resultCode = result.REQUEST_FAILED;
  if (data)
  {
    // If a more specific error code was returned, display that instead.
    if (data.resultCode && Utility.isError(data.resultCode))
    {
      // The server returned an error. Display an error message, if one was available. In case of
      // errors from the server, the user can continue, correct the error, and try again.
      if (data.errorMessage)
      {
        loginErrorBox.innerHTML = data.errorMessage;
        Utility.display(loginErrorBox);
        return;
      }
      resultCode = data.resultCode;
    }
    else
    {
      // The user was successfully created and logged in. Move on to create the subscription.
      createSubscription();
      return;
    }
  }
  errorDisplayed = true;
  alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), Utility.getTimestamp()]));
  window.location.href = '/subscription/html/user_dashboard.php';
}

// *************************************************************************************************
// Log an error that occurred while creating a new user.
function logCreateUserError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while creating a user: ' + error);
    alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED), Utility.getTimestamp()]));
    window.location.href = '/subscription/html/user_dashboard.php';
  }
}

// *************************************************************************************************
// Display the spinner. Once visible, create the subscription.
function createSubscription()
{
  Utility.displaySpinnerThen(doCreateSubscription);
}

// *************************************************************************************************
// Submit an asynchronous POST request to the server, in order to create the subscription (it will
// be deleted again if the payment fails). Once done, the user will be redirected to the payment
// page, in order to select means of payment and enter the relevant details.
function doCreateSubscription()
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('action', 'create_subscription');
  requestData.append('location_id', String(locations[selectedLocation][c.loc.ID]));
  requestData.append('product_type_id',
    String(availableProductTypes[selectedProductType][c.apt.ID]));
  requestData.append('start_date', calendar.selectedDate);
  if (selectedInsurance >= 0)
    requestData.append('insurance_id', String(insuranceProducts[selectedInsurance][c.ins.ID]));
  if (getPaymentMethods().length > 1)
    requestData.append('payment_method', String(getSelectedPaymentMethod()));
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/create_subscription.php', options)
    .then(Utility.extractJson)
    .then(confirmSubscriptionCreated)
    .catch(logCreateSubscriptionError);
}

// *************************************************************************************************
// If the user is permitted to choose how to pay, return his choice using the PAYMENT_METHOD_
// constants. If the user is not given a choice, the elements referenced here may not even exist.
// The method assumes that the user has been given the choice.
function getSelectedPaymentMethod()
{
  var value;

  value = parseInt(Utility.getRadioButtonValue('payment_method', PAYMENT_METHOD_UNKNOWN), 10);
  if (Utility.isValidPaymentMethod(value))
    return value;
  return PAYMENT_METHOD_UNKNOWN;
}

// *************************************************************************************************
// Receive the response after creating a subscription. The response will, if successful, contain a
// paymentId, which needs to be stored. If the request failed, display an error message. Otherwise,
// move on to the payment page.
function confirmSubscriptionCreated(data)
{
  var resultCode, paymentMethod;

  Utility.hideSpinner();

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Assume something failed.
  resultCode = result.PAYMENT_FAILED;
  if (data)
  {
    // If a more specific error code was returned, display that instead.
    if (data.resultCode && Utility.isError(data.resultCode))
      resultCode = data.resultCode;
    else
    {
      // Check the returned payment method. If the payment is by invoice, go directly to the
      // appropriate confirmation page. If it is by Nets, or by Nets first and then invoice, go to
      // the Nets payment page.
      paymentMethod = readPaymentMethod(data);
      if (!Utility.isValidPaymentMethod(paymentMethod))
        resultCode = result.INVALID_PAYMENT_METHOD;
      else
      {
        if ((paymentMethod === PAYMENT_METHOD_NETS) ||
          (paymentMethod === PAYMENT_METHOD_NETS_THEN_INVOICE))
        {
          if (data.paymentId && (data.paymentId !== ''))
          {
            // The payment was created successfully. Move on to ask the customer to pay.
            window.location.href = '/subscription/html/pay.php?paymentId=' + String(data.paymentId);
            return;
          }
        }
        else
        {
          // The customer will receive an invoice. Move on to the confirmation page.
          window.location.href = '/subscription/html/booked.php';
          return;
        }
      }
    }
  }
  errorDisplayed = true;
  alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), Utility.getTimestamp()]));
  window.location.href = '/subscription/html/user_dashboard.php';
}

// *************************************************************************************************
// Read the returned payment method from the given data object. If it did not exist, or was not
// valid, return PAYMENT_METHOD_UNKNOWN.
function readPaymentMethod(data)
{
  var paymentMethod;

  if (data.paymentMethod)
  {
    paymentMethod = parseInt(data.paymentMethod, 10);
    if (Utility.isValidPaymentMethod(paymentMethod))
      return paymentMethod;
  }
  return PAYMENT_METHOD_UNKNOWN;
}

// *************************************************************************************************
// Log an error that occurred while creating a subscription and processing the payment.
function logCreateSubscriptionError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while creating a subscription and processing the payment: ' + error);
    alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED), Utility.getTimestamp()]));
    window.location.href = '/subscription/html/user_dashboard.php';
  }
}

// *************************************************************************************************
// Display the dialogue which allows existing users to log in.
function displayLoginDialogue()
{
  Utility.display(overlay);
  Utility.display(loginDialogue);
  enableLoginButton();
  userNameEdit.focus();
}

// *************************************************************************************************
// Close the login dialogue.
function closeLoginDialogue()
{
  Utility.hide(loginDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Handle a button press on any input element on the login dialogue. If the user pressed enter, try
// to click the login button.
function handleLoginDialogueKeyPress(event)
{
  if ((event.key === 'Enter') && !loginButton.disabled)
    logIn();
}

// *************************************************************************************************
// Enable or disable the login button, depending on whether the user has filled in both a user name
// and a password.
function enableLoginButton()
{
  loginButton.disabled = !Utility.isValidEMail(userNameEdit.value) || (passwordEdit.value === '');
}

// *************************************************************************************************
// Display the spinner. Once visible, log in.
function logIn()
{
  Utility.displaySpinnerThen(doLogIn);
}

// *************************************************************************************************
// Submit an asynchronous request to log in an existing user.
function doLogIn()
{
  var options, requestData;

  closeLoginDialogue();
  requestData = new FormData();
  requestData.append('user_name', String(userNameEdit.value));
  requestData.append('password', String(passwordEdit.value));
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/log_in.php', options)
    .then(Utility.extractJson)
    .then(confirmUserLoggedIn)
    .catch(logLoginError);
}

// *************************************************************************************************
// Receive the response to the request to log in an existing user. Display an error message, or - if
// the request succeeded - display a success message and remove the user information box.
function confirmUserLoggedIn(data)
{
  var resultCode;

  Utility.hideSpinner();

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Assume something failed.
  resultCode = result.REQUEST_FAILED;
  if (data)
  {
    // If a more specific error code was returned, display that instead.
    if (data.resultCode && Utility.isError(data.resultCode))
    {
      // The server returned an error. Display an error message, if one was available. In case of
      // errors from the server, the user can continue, correct the error, and try again.
      if (data.errorMessage)
      {
        loginErrorBox.innerHTML = data.errorMessage;
        Utility.display(loginErrorBox);
        return;
      }
      resultCode = data.resultCode;
    }
    else
    {
      // The user was successfully logged in. Replace the user information fields with a success
      // message.
      isLoggedIn = true;
      user = data.user;
      selectedEntityType = user.entityType;
      displayUserInfoBox();
      displayPaymentMethods();
      Utility.hide(loginErrorBox);
      enableConfirmBookingButton();
      return;
    }
  }
  errorDisplayed = true;
  alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), Utility.getTimestamp()]));
  window.location.href = '/subscription/html/user_dashboard.php';
}

// *************************************************************************************************
// Log an error that occurred while logging in an existing user.
function logLoginError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while logging in: ' + error);
    alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED), Utility.getTimestamp()]));
  }
}

// *************************************************************************************************
