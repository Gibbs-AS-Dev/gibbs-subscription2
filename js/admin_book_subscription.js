// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// - The availableProductTypes and users tables are loaded asynchronously, and are not found in the
//   HTML when the page loads.

// - This page differs from the book_subscription page in at least the following respects:
//     * A product might be preselected. If so, the "few available" warning is not displayed.
//     * Prices can be modified. This affects the price calculation.
//     * There is no submit request button.
//     * The page does not redirect to the requests page if the user needs to submit a request for
//       a particular location.

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************

// Offer type constants.
var OFFER_TYPE_BOOK_FOR_CUSTOMER = 0;
var OFFER_TYPE_SEND_OFFER = 1;

// Customer type constants.
var CUSTOMER_TYPE_NEW = 0;
var CUSTOMER_TYPE_EXISTING = 1;

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var locationBox, locationDescriptionBox, startDateBox, selectedDateEdit, openCalendarButton,
  closeCalendarButton, calendarBox, productTypesBox, resetFilterButton, insuranceBox, summaryBox,
  bookForCustomerButton, sendOfferButton, newCustomerButton, existingCustomerButton,
  paymentMethodNotification, newIndividualButton, newCompanyButton, individualDataBox,
  companyDataBox, newUserBox, existingUserBox, existingUserContent, existingUserToolbar,
  existingUserFilterEdit, clearExistingUserFilterButton, newUserNameEdit, newFirstNameEdit,
  newLastNameEdit, newCompanyNameEdit, newCompanyIdEdit, newPhoneEdit, newAddressEdit,
  newPostcodeEdit, newAreaEdit, /*newPasswordEdit,*/ loginErrorBox, confirmBookingButton, overlay,
  editCategoryFilterDialogue, editCategoryFilterDialogueContent, priceInformationDialogue,
  priceInformationDialogueContent, editPricePlanDialogue, editPricePlanDialogueContent;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var mapBox, alternativeLocationCombo, existingUserList, customBasePriceEdit,
  customInsurancePriceEdit, priceModEditorBox;

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

// The modified base price, or -1 if the administrator has not elected to override the base price
// for the current customer.
var customBasePrice = -1;

// The modified list of price mods, or null if the administrator has not elected to override the
// price mods for the current customer. If present, it will be an array of price mods, where each
// price mod has the following fields:
//   PRICE_MOD      The percentage change in prices. -10 is a 10% discount.
//   DURATION       The number of months that this change will last, or -1 if the change applies
//                  indefinitely.
// Use the c.apt column constants. Note that, if the administrator has modified the available
// product type's price mods by removing all of them, this field will hold an empty array.
var customPriceMods = null;

// Array of price mods currently being edited in the edit price plan dialogue box, or null if the
// dialogue box is not open. The array has the same format as the customPriceMods field.
var editedPriceMods = null;

// The ID of the currently selected product, or null if none is selected. A product may be selected
// when the page is loaded, in order to restrict the booking process to that product's location and
// product type. The server will be instructed to provide an offer for that product only.
var selectedProductId = null;

// The index in the insuranceProducts table of the selected insurance, or -1 if no insurance is
// selected. This could be because there are no available insurance products.
var selectedInsurance = -1;

// The modified insurance price, or -1 if the administrator has not elected to override the price
// for the current customer.
var customInsurancePrice = -1;

// The currently selected offer type, using the OFFER_TYPE constants.
var selectedOfferType = OFFER_TYPE_BOOK_FOR_CUSTOMER;

// The currently selected customer type, using the CUSTOMER_TYPE constants.
var selectedCustomerType = CUSTOMER_TYPE_NEW;

// The currently selected entity type. That is, whether the new customer is a company or a private
// individual.
var selectedEntityType = ENTITY_TYPE_INDIVIDUAL;

// The list of existing users, from which a buyer can be selected. The table is loaded
// asynchronously, once the user has chosen to select an existing user.
var users = null;

// Flag that says whether the list of users has been requested from the server. If true, the list
// has either been requested, or has finished loading.
var usersLoaded = false;

// The ID of the user selected to own and pay for the subscription.
var selectedUserId = -1;

// Flag that says whether a buyer can be selected. If false, the selectedUserId cannot be changed.
var enableUserSelection = true;

// Flag that says whether an alert error message has already been displayed. If so, we should not
// display another.
var errorDisplayed = false;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and displaying the appropriate page of the progress
// tabset.
//
// If an initialLocationId is passed, that location will be selected, and the select product type
// tab will be the initial tab. Pass null if no location is selected.
//
// If a location is given, an initialProductId may also be passed. If it is, the product with that
// ID will be the only selectable one. The server will be asked to provide an offer for that
// particular product. Pass null if no product is selected.
//
// If an initialDate is passed, that date will be selected as the moving-in date. Otherwise, today's
// date will be used. Pass a string with a date in ISO format, or null in order to not select a
// date. If the passed date is before today's date, today's date will be used.
//
// If an initialCategoryId is passed, only storage unit types in that category will be displayed
// once available storage unit types have been loaded. Pass null in order to display storage unit
// types in all categories.
//
// If an initialUserId is passed, that user will be selected as the buyer, and the user
// interface to select users will be disabled. Pass null if no user is selected.
function initialise(initialLocationId, initialProductId, initialDate, initialCategoryId,
  initialUserId)
{
  var today, index;

  // Obtain pointers to user interface elements.
  Utility.readPointers(['locationBox', 'locationDescriptionBox', 'startDateBox', 'selectedDateEdit',
    'openCalendarButton', 'closeCalendarButton', 'calendarBox', 'productTypesBox',
    'resetFilterButton', 'insuranceBox', 'summaryBox', 'bookForCustomerButton', 'sendOfferButton',
    'newCustomerButton', 'existingCustomerButton', 'paymentMethodNotification',
    'newIndividualButton', 'newCompanyButton', 'individualDataBox', 'companyDataBox', 'newUserBox',
    'existingUserBox', 'existingUserContent', 'existingUserToolbar', 'existingUserFilterEdit',
    'clearExistingUserFilterButton', 'newUserNameEdit',  'newFirstNameEdit', 'newLastNameEdit',
    'newCompanyNameEdit', 'newCompanyIdEdit', 'newPhoneEdit', 'newAddressEdit', 'newPostcodeEdit',
    'newAreaEdit', /*'newPasswordEdit',*/ 'loginErrorBox', 'confirmBookingButton', 'overlay',
    'editCategoryFilterDialogue', 'editCategoryFilterDialogueContent', 'priceInformationDialogue',
    'priceInformationDialogueContent', 'editPricePlanDialogue', 'editPricePlanDialogueContent']);
  
  // Initialise category display flags. If the display is not limited to a single category, then all
  // categories are set to not be displayed. This - curiously enough - will cause all of them to be
  // displayed.
  categoryDisplayed = new Array(categories.length);
  hideAllCategories();
  if (initialCategoryId !== null)
  {
    index = Utility.getCategoryIndex(initialCategoryId);
    if (index >= 0)
      categoryDisplayed[index] = true;
  }

  // Create tabset.
  progressTabset = new NumberTabset(summaryTabIndex + 1);
  progressTabset.display();

  // Create calendar component. If no start date is provided, or if it is before today's date, use
  // today's date.
  calendar = new Calendar(settings.selectableMonthCount);
  calendar.dayNames = DAY_NAMES;
  calendar.monthNames = MONTH_NAMES;
  calendar.monthNamesInSentence = MONTH_NAMES_IN_SENTENCE;
  today = Utility.getCurrentIsoDate();
  if ((initialDate === null) || (initialDate < today))
    initialDate = today;
  selectedDateEdit.value = initialDate;
  calendar.selectedDate = initialDate;
  calendar.onSelectDate = selectDate;

  // Set the selected user, if one was passed.
  if (initialUserId !== null)
  {
    initialUserId = parseInt(initialUserId, 10);
    if (isFinite(initialUserId) && (initialUserId >= 0))
    {
      selectedUserId = initialUserId;
      enableUserSelection = false;
    }
  }

  // Set the location, if one was passed.
  if (initialLocationId !== null)
  {
    index = Utility.getLocationIndex(initialLocationId);
    if (index >= 0)
    {
      selectedLocation = index;

      // If a location was selected, see if a product was also selected.
      if (initialProductId !== null)
      {
        initialProductId = parseInt(initialProductId, 10);
        if (isFinite(initialProductId) && (initialProductId >= 0))
          selectedProductId = initialProductId;
      }
    }
  }

  // If there is only one location, select it regardless of whether a parameter was passed. Note
  // that we had to go through the previous section in any case, in order to potentially select a
  // product.
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
// Select the location with the given index in the locations table.
function selectLocation(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, locations))
  {
    selectedLocation = index;
    displayProductTypeTab();
  }
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

  o = new Array(7);
  p = 0;

  o[p++] = '/subscription/json/available_product_types.php?action=';
  if (selectedProductId)
  {
    // Ask for a specific product.
    o[p++] = 'get_available_product&selected_product_id=';
    o[p++] = String(selectedProductId);
  }
  else
  {
    // Ask for all available product types at the selected location.
    o[p++] = 'get_available_product_types';
  }
  o[p++] = '&selected_location_id=';
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
      o[p++] = getUnavailableProductType(i);
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
  o[p++] = '<div class="button-box product-type-box "><div class="product-type-box-left"><h3>';
  o[p++] = getProductTypeName(index);
  o[p++] = '</h3>';
  // Add the "few available" notice, if appropriate. Note that the notice is never displayed if
  // a single product is selected. If so, there is always just one available.
  if ((!selectedProductId) && (settings.fewAvailableCount > 0) &&
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
  priceMods = availableProductTypes[index][c.apt.PRICE_MODS];
  if ((priceMods !== null) && (priceMods.length > 0))
  {
    // The product type is affected by price mods.
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
  o[p++] = ', false);"><i class="fa-solid fa-circle-info"></i></a></div>';

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
// Return the name to be displayed for the product type with the given index in the
// availableProductTypes table. If a particular product was selected, display that as well.
function getProductTypeName(index)
{
  var o, p;

  if (!selectedProductId)
    return availableProductTypes[index][c.apt.NAME];

  o = new Array(4);
  p = 0;

  o[p++] = availableProductTypes[index][c.apt.PRODUCT_NAME];
  o[p++] = ' (';
  o[p++] = availableProductTypes[index][c.apt.NAME];
  o[p++] = ')';
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

  o = new Array(25);
  p = 0;

  // Write name. Left aligned.
  o[p++] = '<div class="button-box product-type-box product-type-unavailable"><div class="product-type-box-left"><h3>';
  o[p++] = getProductTypeName(index);
  o[p++] = '</h3></div>';

  // Write empty box, as there is no price to be displayed. Right aligned.
  o[p++] = '<div class="product-type-box-right">&nbsp;</div><br>';

  // Write "none available" label. Left aligned.
  o[p++] = '<div class="product-type-box-left">';
  o[p++] = getText(8, 'Ingen ledige lagerboder.');
  o[p++] = '</div>';

  // Write empty box in place of the submit request button. Right aligned.
  o[p++] = '<div class="product-type-box-right">&nbsp;</div><br>';

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
    o[p++] = getText(52, 'Ledig ved:');
    o[p++] = '&nbsp;&nbsp;';
    o[p++] = getAlternativeLocationCombo(index);
    o[p++] = '</div>';

    // Write change location button. Right aligned.
    o[p++] = '<div class="product-type-box-right separator-above"><button type="button" class="wide-button" onclick="selectLocation(alternativeLocationCombo.value);"><i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;';
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
// the index of the product type in the availableProductTypes table. If the useCustomPrices flag is
// true, use the custom prices defined by the administrator in the calculation. Otherwise, use the
// default price for the product type with the given index.
function displayPriceInformationDialogue(index, useCustomPrices)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, availableProductTypes))
    return;

  o = new Array(4);
  p = 0;

  o[p++] = '<div class="form-element">';
  o[p++] = getRentDescription(index, useCustomPrices);
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
// availableProductTypes table. If the useCustomPrices flag is true, use the custom prices defined
// by the administrator in the calculation. Otherwise, use the default price for the product type
// with the given index.
  // *** // Replace numbers with words if the number is less than or equal to 5?
function getRentDescription(index, useCustomPrices)
{
  var o, p, i, capacityPrice, priceMods;

  // If no price mods are present, return a description of the capacity price.
  if (useCustomPrices)
  {
    capacityPrice = getCapacityPrice();
    priceMods = getPriceMods();
  }
  else
  {
    capacityPrice = availableProductTypes[index][c.apt.PRICE];
    priceMods = availableProductTypes[index][c.apt.PRICE_MODS];
  }
  if ((priceMods === null) || (priceMods.length <= 0))
    return getText(16, '$1 kr pr måned', [String(capacityPrice)]);

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

  insurancePerMonth = getInsurancePrice();
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
    deleteCustomPrices();
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
  // Reset custom prices. Since selections may have changed, the customisations may no longer be
  // applicable or valid.
  customBasePrice = -1;
  customPriceMods = null;
  customInsurancePrice = -1;
  // Display the price summary.
  displaySummaryBox();
  // Display offer and customer type.
  setOfferType(OFFER_TYPE_BOOK_FOR_CUSTOMER);
  if (enableUserSelection)
    setCustomerType(CUSTOMER_TYPE_NEW);
  else
  {
    setCustomerType(CUSTOMER_TYPE_EXISTING);
    newCustomerButton.disabled = true;
    existingCustomerButton.disabled = true;
  }
  // Display the tab, and enable or disable the submit button.
  progressTabset.activeTabIndex = summaryTabIndex;
  enableConfirmBookingButton();
}

// *************************************************************************************************

function displaySummaryBox()
{
  var o, p, startDate, lastDayOfMonth, daysLeft, thisMonth, nextMonth, rentThisMonth, rentNextMonth,
    insuranceThisMonth, insurancePerMonth, finalPricePerMonth, twoMonths, priceMods, hasPriceMods;

  // Calculate prices for rent and insurance. It is possible that no insurance products exist, in
  // which case no insurance will have been selected.
  priceMods = getPriceMods();
  hasPriceMods = (priceMods !== null) && (priceMods.length > 0);
  insurancePerMonth = Math.max(getInsurancePrice(), 0);
  finalPricePerMonth = insurancePerMonth + getCapacityPrice();
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
  
  o = new Array(55);
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
  o[p++] = getText(33, 'På første faktura');
  o[p++] = '</td><td class="amount sum">'
  o[p++] = getText(30, '$1 kr',
    [String(rentThisMonth + rentNextMonth + insuranceThisMonth + insurancePerMonth)]);
  o[p++] = '</td></tr>';
  // Permanent price. This is always displayed, as the administrator may have changed the price.
  o[p++] = '<tr><td class="permanent-price">';
  if (hasPriceMods)
    o[p++] = getText(34, 'Pris etter kampanjeperioden');
  else
    o[p++] = getText(40, 'Total pris');
  o[p++] = '</td><td class="permanent-price amount">';
  o[p++] = getText(16, '$1 kr pr måned', [String(finalPricePerMonth)])
  o[p++] = '&nbsp;&nbsp;<a onclick="displayPriceInformationDialogue(';
  o[p++] = String(selectedProductType);
  o[p++] = ', true);" class="info-button"><i class="fa-solid fa-circle-info"></i></a></td></tr></table>';

  // Modify price buttons.
  o[p++] = '<div class="summary-box-footer"><button type="button" class="wide-button" onclick="displayPricePlanDialogue();"><i class="fa-solid fa-pen-to-square"></i>&nbsp;&nbsp;';
  o[p++] = getText(39, 'Endre pris');
  o[p++] = '</button>';
  if (hasCustomPrices())
  {
    o[p++] = '<button type="button" class="wide-button" onclick="deleteCustomPrices();"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
    o[p++] = getText(41, 'Slett prisendringer');
    o[p++] = '</button>';
  }
  o[p++] = '</div>';

  summaryBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Return true if any of the prices or price mods has been modified by the administrator for this
// customer.
function hasCustomPrices()
{
  return (customBasePrice !== -1) || (customPriceMods !== null) || (customInsurancePrice !== -1);
}

// *************************************************************************************************
// Cancel any price modifications made by the administrator for this customer.
function deleteCustomPrices()
{
  customBasePrice = -1;
  customPriceMods = null;
  customInsurancePrice = -1;
  displaySummaryBox();
}

// *************************************************************************************************
// Return the capacity price for the current offer. If the administrator has elected to override the
// price, the modified price is used. Otherwise, get the price from the currently selected available
// product type.
function getCapacityPrice()
{
  if (customBasePrice < 0)
    return availableProductTypes[selectedProductType][c.apt.PRICE];
  return customBasePrice;
}

// *************************************************************************************************
// Return the price mods for the current offer. If the administrator has elected to override the
// price, the modified price mods are used. Otherwise, get the price mods from the currently
// selected available product type. If there are no price mods, the function will return null. If
// the administrator has overriden the product type's price mods by removing them all, the function
// will return an empty array.
function getPriceMods()
{
  if (customPriceMods === null)
    return availableProductTypes[selectedProductType][c.apt.PRICE_MODS];
  return customPriceMods;
}

// *************************************************************************************************
// Return the insurance price for the current offer. If the administrator has elected to override
// the price, the modified price is used. Otherwise, get the price from the currently selected
// insurance product. If no insurance is selected at all, return -1.
function getInsurancePrice()
{
  if (selectedInsurance < 0)
    return -1;
  if (customInsurancePrice < 0)
    return insuranceProducts[selectedInsurance][c.ins.PRICE];
  return customInsurancePrice;
}

// *************************************************************************************************
// Return the price per month for the first month of the subscription.
function getPriceFirstMonth()
{
  var capacityPrice, priceMods;

  // If no special offer applies, just return the capacity price.
  capacityPrice = getCapacityPrice();
  priceMods = getPriceMods();
  if ((priceMods === null) || (priceMods.length <= 0))
    return capacityPrice;

  // We have a special offer. The first mod will apply to the first month, so use that.
  return Utility.getModifiedPrice(capacityPrice, priceMods[0][c.apt.PRICE_MOD]);
}

// *************************************************************************************************
// Return the price per month for the second month of the subscription.
function getPriceSecondMonth()
{
  var capacityPrice, priceMods;

  // If no special offer applies, just return the capacity price.
  capacityPrice = getCapacityPrice();
  priceMods = getPriceMods();
  if ((priceMods === null) || (priceMods.length <= 0))
    return capacityPrice;

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
// Handle a change to the way the subscription is generated. The user can choose whether to create a
// subscription on behalf of the customer or create an offer to be sent to a prospective customer.
// Store the user's choice.
function selectOfferType()
{
  setOfferType(parseInt(Utility.getRadioButtonValue('offer_type', -1), 10));
}

// *************************************************************************************************
// Set a new value for the offer type.
function setOfferType(newValue)
{
  newValue = parseInt(newValue, 10);

  if (isFinite(newValue) && (newValue >= OFFER_TYPE_BOOK_FOR_CUSTOMER) &&
    (newValue <= OFFER_TYPE_SEND_OFFER) && (newValue !== selectedOfferType))
  {
    selectedOfferType = newValue;
    if (selectedOfferType === OFFER_TYPE_BOOK_FOR_CUSTOMER)
    {
      bookForCustomerButton.checked = true;
      Utility.display(paymentMethodNotification);
    }
    else
    {
      sendOfferButton.checked = true;
      Utility.hide(paymentMethodNotification);
    }
  }
}

// *************************************************************************************************
// Handle a change to the way the buyer is identified. The user can choose whether to create a new
// customer or select an existing one. Display the correct user interface to do either of these,
// depending on the user's choice.
function selectCustomerType()
{
  setCustomerType(parseInt(Utility.getRadioButtonValue('customer_type', -1), 10));
}

// *************************************************************************************************
// Set a new value for the customer type, and update the user interface.
function setCustomerType(newValue)
{
  // Validate new value. The existing value will not be updated unless it has changed.
  newValue = parseInt(newValue, 10);
  if (isFinite(newValue) && (newValue >= CUSTOMER_TYPE_NEW) &&
    (newValue <= CUSTOMER_TYPE_EXISTING) && (newValue !== selectedCustomerType))
  {
    selectedCustomerType = newValue;
    if (selectedCustomerType === CUSTOMER_TYPE_NEW)
      newCustomerButton.checked = true;
    else
      existingCustomerButton.checked = true;
    Utility.setDisplayState(newUserBox, selectedCustomerType === CUSTOMER_TYPE_NEW);
    Utility.setDisplayState(existingUserBox, selectedCustomerType === CUSTOMER_TYPE_EXISTING);
    if (selectedCustomerType === CUSTOMER_TYPE_EXISTING)
    {
      displayExistingUsers();
      // Load the list of users, if it has not been loaded already.
      if (!usersLoaded)
        loadUsers();
    }
    enableConfirmBookingButton();
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
    enableConfirmBookingButton();
  }
}

// *************************************************************************************************
// Update the contents of the list of existing users, based on the current filter settings and
// whether the list has been loaded.
function displayExistingUsers()
{
  var o, p, i, filter, selectedUserDisplayed;
  
  if (users === null)
  {
    Utility.hide(existingUserToolbar);
    o = new Array(3);
    p = 0;

    // Display wait message.
    o[p++] = '<div class="form-element">';
    o[p++] = getText(38, 'Henter kunder. Vennligst vent...');
    o[p++] = '</div>';

    existingUserContent.innerHTML = o.join('');
  }
  else
  {
    Utility.setDisplayState(existingUserToolbar, enableUserSelection);
    filter = existingUserFilterEdit.value.toLowerCase();
    Utility.setDisplayState(clearExistingUserFilterButton, filter !== '');
    o = new Array((users.length * 9) + 2);
    p = 0;
    selectedUserDisplayed = false;

    // Display list of existing users.
    o[p++] = '<div class="form-element"><select id="existingUserList" class="existing-user-list" size="20" onchange="selectExistingUser();">';
    for (i = 0; i < users.length; i++)
    {
      // Check whether to display this user.
      if ((filter !== '') && (users[i][c.usr.NAME].toLowerCase().indexOf(filter) < 0) &&
        (users[i][c.usr.EMAIL].toLowerCase().indexOf(filter) < 0))
        continue;
      o[p++] = '<option value="';
      o[p++] = String(users[i][c.usr.ID]);
      o[p++] = '"';
      if (users[i][c.usr.ID] === selectedUserId)
      {
        o[p++] = ' selected';
        selectedUserDisplayed = true;
      }
      o[p++] = '>';
      o[p++] = users[i][c.usr.NAME];
      o[p++] = ' (';
      o[p++] = users[i][c.usr.EMAIL];
      o[p++] = ')</option>';
    }
    o[p++] = '</select></div>';
    if (!selectedUserDisplayed)
      selectedUserId = -1;

    existingUserContent.innerHTML = o.join('');
    Utility.readPointers(['existingUserList']);
    existingUserList.disabled = !enableUserSelection;
  }
  enableConfirmBookingButton();
}

// *************************************************************************************************
// Read the selection from the list of existing users, and store the selected user's ID.
function selectExistingUser()
{
  var newValue;

  newValue = parseInt(existingUserList.value, 10);
  if (isFinite(newValue) && (newValue >= 0))
  {
    selectedUserId = newValue;
    enableConfirmBookingButton();
  }
}

// *************************************************************************************************
// Remove the contents of the existing user filter, to display all users.
function clearExistingUserFilter()
{
  existingUserFilterEdit.value = '';
  displayExistingUsers();
}

// *************************************************************************************************
// Submit asynchronous request to load the list of existing users, from which a buyer can be
// selected. The list will only contain ID, name and e-mail.
function loadUsers()
{
  var options, requestData;

  requestData = new FormData();
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/list_users.php', options)
    .then(Utility.extractJson)
    .then(receiveUsers)
    .catch(logListUsersError);
  usersLoaded = true;
}

// *************************************************************************************************
// Receive the response to the request to load the list of users. Display an error message, or - if
// the request succeeded - display the user list.
function receiveUsers(data)
{
  var resultCode;

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Assume something failed. Cheerful, innit?
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
      // The user list was successfully retrieved. Store and display it.
      users = data.users;
      displayExistingUsers();
      return;
    }
  }
  errorDisplayed = true;
  alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), Utility.getTimestamp()]));
  window.location.href = '/subscription/html/admin_dashboard.php';
}

// *************************************************************************************************
// Display an error that occurred while loading the list of users.
function logListUsersError(error)
{
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while loading list of users: ' + error);
    alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED), Utility.getTimestamp()]));
  }
}

// *************************************************************************************************
// Enable or disable the confirm booking button, depending on whether the user has filled in all
// the required information.
function enableConfirmBookingButton()
{
  if (selectedCustomerType === CUSTOMER_TYPE_NEW)
  {
    // For a new customer, the user must fill in all the mandatory user information fields. There
    // are different fields, depending on whether the new user is a company or an individual.
    if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
    {
      confirmBookingButton.disabled = (newFirstNameEdit.value === '') ||
        (newLastNameEdit.value === '') || !Utility.isValidEMail(newUserNameEdit.value) ||
        (newPhoneEdit.value === '') || (newAddressEdit.value === '') ||
        (newPostcodeEdit.value === '') || (newAreaEdit.value === ''); /*||
        (newPasswordEdit.value.length < PASSWORD_MIN_LENGTH);*/
    }
    else
    {
      confirmBookingButton.disabled = (newCompanyNameEdit.value === '') ||
        (newCompanyIdEdit.value === '') || !Utility.isValidEMail(newUserNameEdit.value) ||
        (newPhoneEdit.value === '') || (newAddressEdit.value === '') ||
        (newPostcodeEdit.value === '') || (newAreaEdit.value === ''); /*||
        (newPasswordEdit.value.length < PASSWORD_MIN_LENGTH);*/
    }
  }
  else
  {
    // For an existing customer, the user must select a customer - or else filter the list of
    // existing customers such that there is only one option left.
    confirmBookingButton.disabled = (selectedUserId < 0) &&
      (!existingUserList || (existingUserList.options.length !== 1));
  }
}

// *************************************************************************************************
// Ensure the user exists, then create a subscription.
function confirmBooking()
{
  if (selectedOfferType === OFFER_TYPE_SEND_OFFER)
  {
      // *** // Implement.
    alert('Sorry! Sending offers is not yet implemented.');
  }
  else
  {
    if (selectedCustomerType === CUSTOMER_TYPE_NEW)
    {
      // The buyer is a new user, so create the user now. Once that's done, the subscription can be
      // created as well.
      createUser();
    }
    else
    {
      // if an existing user has already been selected as the buyer, create the subscription
      // straight away. See if the user was selected.
      if (selectedUserId < 0)
      {
        // No user was selected. If the list of existing users contains only a single entry, use
        // that one. Otherwise, we can't do anything.
        if (existingUserList && (existingUserList.options.length === 1))
          selectedUserId = existingUserList.options[0].value;
        else
          return;
      }
      createSubscription();
    }
  }
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
  requestData.append('entity_type', String(selectedEntityType));
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
      // The user was successfully created, but not logged in. Verify and store the ID of the newly
      // created user.
      selectedUserId = parseInt(data.userId, 10);
      if (isFinite(selectedUserId) && (selectedUserId >= 0))
      {
        // Move on to create the subscription.
        createSubscription();
        return;
      }
      selectedUserId = -1;
    }
  }
  errorDisplayed = true;
  alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), Utility.getTimestamp()]));
  window.location.href = '/subscription/html/admin_dashboard.php';
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
    window.location.href = '/subscription/html/admin_dashboard.php';
  }
}

// *************************************************************************************************
// Display the spinner. Once visible, create the subscription.
function createSubscription()
{
  Utility.displaySpinnerThen(doCreateSubscription);
}

// *************************************************************************************************
// Submit an asynchronous POST request to the server, in order to create the subscription.
function doCreateSubscription()
{
  var options, requestData, i;

  // Add parameters.
  requestData = new FormData();
  requestData.append('action', 'create_subscription');
  requestData.append('buyer_id', selectedUserId);
  requestData.append('location_id', String(locations[selectedLocation][c.loc.ID]));
  requestData.append('product_type_id',
    String(availableProductTypes[selectedProductType][c.apt.ID]));
  requestData.append('start_date', calendar.selectedDate);
  if (selectedInsurance >= 0)
    requestData.append('insurance_id', String(insuranceProducts[selectedInsurance][c.ins.ID]));

  // Add custom prices, if specified.
  if (customBasePrice !== -1)
    requestData.append('custom_base_price', customBasePrice);
  if (customPriceMods !== null)
  {
    requestData.append('price_mod_count', customPriceMods.length);
    for (i = 0; i < customPriceMods.length; i++)
    {
      requestData.append('price_mod_' + String(i), customPriceMods[i][c.apt.PRICE_MOD]);
      requestData.append('duration_' + String(i), customPriceMods[i][c.apt.DURATION]);
    }
  }
  if (customInsurancePrice !== -1)
    requestData.append('custom_insurance_price', customInsurancePrice);

  // Set options.
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;

  // Submit request.
  fetch('/subscription/json/create_subscription.php', options)
    .then(Utility.extractJson)
    .then(confirmSubscriptionCreated)
    .catch(logCreateSubscriptionError);
}

// *************************************************************************************************
// Receive the response after creating a subscription. If the request failed, display an error
// message. Otherwise, move on to the confirmation page.
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
      // Check the returned payment method. When booking as an admin, the payment method should
      // always be "invoice". Move on to the confirmation page.
      paymentMethod = readPaymentMethod(data);
      if (paymentMethod !== PAYMENT_METHOD_INVOICE)
        resultCode = result.INVALID_PAYMENT_METHOD;
      else
      {
        window.location.href = '/subscription/html/admin_booked.php';
        return;
      }
    }
  }
  errorDisplayed = true;
  alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), Utility.getTimestamp()]));
  window.location.href = '/subscription/html/admin_dashboard.php';
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
// Log an error that occurred while creating a subscription.
function logCreateSubscriptionError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while creating a subscription: ' + error);
    alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED), Utility.getTimestamp()]));
    window.location.href = '/subscription/html/admin_dashboard.php';
  }
}

// *************************************************************************************************
// Open the edit price plan dialogue, and generate its contents. This function copies the current
// set of price mods, if any, allowing the user to edit them in the dialogue box without affecting
// anything else.
function displayPricePlanDialogue()
{
  // Copy the current price mods to the editedPriceMods global variable. The copy is never null,
  // although it might be an empty array.
  editedPriceMods = copyCurrentPriceMods();

  // Write the contents of the edit price plan dialogue.
  displayPricePlan();

  // Display the edit price plan dialogue.
  Utility.display(overlay);
  Utility.display(editPricePlanDialogue);
}

// *************************************************************************************************
// Display the current price plan in the price plan dialogue.
function displayPricePlan()
{
  var o, p, insurancePrice;

  o = new Array(32);
  p = 0;

  // Base price.
  o[p++] = '<h4>';
  o[p++] = getText(42, 'Leie pr måned');
  o[p++] = '</h4><table cellspacing="0" cellpadding="0"><tbody><tr><td class="no-separator">';
  o[p++] = getText(43, 'Pris for bodtypen');
  o[p++] = '</td><td class="no-separator amount">';
  o[p++] = String(availableProductTypes[selectedProductType][c.apt.BASE_PRICE]);
  o[p++] = '</td></tr><tr><td class="no-separator">';
  o[p++] = getText(44, 'Pris, justert for kapasitet');
  o[p++] = '</td><td class="no-separator amount">';
  o[p++] = String(availableProductTypes[selectedProductType][c.apt.PRICE]);
  o[p++] = '</td></tr><tr><td>';
  o[p++] = getText(45, 'Denne kunden skal betale');
  o[p++] = '</td><td class="amount"><input type="number" id="customBasePriceEdit" min="0" value="';
  o[p++] = String(getCapacityPrice());
  o[p++] = '"></td></tr></tbody></table>';
  // Insurance.
  insurancePrice = getInsurancePrice();
  if (insurancePrice >= 0)
  {
    o[p++] = '<h4>';
    o[p++] = getText(46, 'Forsikring pr måned');
    o[p++] = '</h4><table cellspacing="0" cellpadding="0"><tbody><tr><td class="no-separator">';
    o[p++] = getText(47, 'Pris for valgt forsikring');
    o[p++] = '</td><td class="no-separator amount">';
    o[p++] = String(insuranceProducts[selectedInsurance][c.ins.PRICE]);
    o[p++] = '</td></tr><tr><td>';
    o[p++] = getText(45, 'Denne kunden skal betale');
    o[p++] = '</td><td class="amount"><input type="number" id="customInsurancePriceEdit" min="0" value="';
    o[p++] = String(insurancePrice);
    o[p++] = '"></td></tr>';
  }
  o[p++] = '</tbody></table>';
  // Price mods.
  o[p++] = '<h4>';
  o[p++] = getText(48, 'Tilbud');
  o[p++] = '</h4><div id="priceModEditorBox" class="price-mod-editor-box">';
  o[p++] = getPriceModEditorBoxContents();
  o[p++] = '</div>';

  editPricePlanDialogueContent.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['customBasePriceEdit', 'customInsurancePriceEdit', 'priceModEditorBox']);
}

// *************************************************************************************************
// Return the HTML code to fill the priceModEditorBox. The code is generated based on the contents
// of the editedPriceMods array.
function getPriceModEditorBoxContents()
{
  var o, p, i;

  o = new Array(editedPriceMods.length + 3);
  p = 0;

  for (i = 0; i < editedPriceMods.length; i++)
    o[p++] = getPriceModEditor(i);
  o[p++] = '<div class="price-mod-editor-frame"><button type="button" class="low-profile wide-button" onclick="addEditedPriceMod();"><i class="fa-solid fa-plus"></i>&nbsp;&nbsp;';
  o[p++] = getText(49, 'Legg til prisendring');
  o[p++] = '</button></div>';

  return o.join('');
}

// *************************************************************************************************
// Refresh the contents of the priceModEditorBox, based on the current values in the editedPriceMods
// array.
function updatePriceModEditorBox()
{
  priceModEditorBox.innerHTML = getPriceModEditorBoxContents();
}

// *************************************************************************************************
// Return HTML for a single line of user interface elements that can be used to edit a price mod. A
// price mod is a percentage price modifier, along with the conditions that state when it will
// apply.
function getPriceModEditor(index)
{
  var o, p;

  o = new Array(17);
  p = 0;

  o[p++] = '<div class="price-mod-editor-frame"><div>';

  // Price modifier.
  o[p++] = '<input type="number" id="modifier_';
  o[p++] = String(index);
  o[p++] = '" class="spaced" min="-1000" max="1000" value="';
  o[p++] = String(editedPriceMods[index][c.apt.PRICE_MOD]);
  o[p++] = '" onchange="storeEditedPriceModChanges();"> % ';
  o[p++] = getText(50, 'i');

  // Duration. A duration of 0 is indefinite.
  o[p++] = ' <input type="number" id="duration_';
  o[p++] = String(index);
  o[p++] = '" class="spaced" min="0" max="24" value="';
  o[p++] = String(editedPriceMods[index][c.apt.DURATION]);
  o[p++] = '" onchange="storeEditedPriceModChanges();"> ';
  o[p++] = getText(51, 'måned(er)');

  // Delete button.
  o[p++] = '<button type="button" class="low-profile icon-button spaced" onclick="deleteEditedPriceMod(';
  o[p++] = String(index);
  o[p++] = ');"><i class="fa-solid fa-trash"></i></button>';

  o[p++] = '</div></div>';

  return o.join('');
}

// *************************************************************************************************
// Close the dialogue to edit price plans.
function closePricePlanDialogue()
{
  editedPriceMods = null;
  Utility.hide(editPricePlanDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Store the price changes from the edit price plan dialogue, and update the price summary.
function updatePricePlan()
{
  var newBasePrice, newInsurancePrice;

  // Update custom base price. If the user has entered a valid value that is not the same as the
  // capacity price he would have got anyway, store the value.
  newBasePrice = parseInt(customBasePriceEdit.value, 10);
  if (isFinite(newBasePrice) && (newBasePrice >= 0) &&
    (newBasePrice !== availableProductTypes[selectedProductType][c.apt.PRICE]))
    customBasePrice = newBasePrice;
  else
    customBasePrice = -1;

  // Update custom insurance price. If an insurance has been selected, then if the user has entered
  // a valid value that is not the same as the regular insurance price, store the value.
  if (selectedInsurance >= 0)
  {
    newInsurancePrice = parseInt(customInsurancePriceEdit.value, 10);
    if (isFinite(newInsurancePrice) && (newInsurancePrice >= 0) &&
      (newInsurancePrice !== insuranceProducts[selectedInsurance][c.ins.PRICE]))
      customInsurancePrice = newInsurancePrice;
    else
      customInsurancePrice = -1;
  }

  // Update custom price mods. See if the edited price mods equal the default ones, in which case
  // the administrator has made no changes. If they were not the same, store the changes made by the
  // administrator. If the administrator has removed existing price mods, the stored custom price
  // mods will be an empty table, as the editedPriceMods are never null.
  if (priceModsEqual(editedPriceMods, availableProductTypes[selectedProductType][c.apt.PRICE_MODS]))
    customPriceMods = null;
  else
    customPriceMods = editedPriceMods;

  // Recalculate all prices and update the user interface.
  closePricePlanDialogue();
  displaySummaryBox();
}

// *************************************************************************************************
// Return true if the two given tables of price mods contain the same information. Being null counts
// as being empty, for the purposes of comparison.
function priceModsEqual(a, b)
{
  var aEmpty, bEmpty, i;

  // If one of the lists is empty, they're equal if the other one is as well.
  aEmpty = (a === null) || (a.length <= 0);
  bEmpty = (b === null) || (b.length <= 0);
  if (aEmpty || bEmpty)
    return aEmpty === bEmpty;
  // Both of the lists contain price mods. If they don't have the same number of them, they're not
  // equal.
  if (a.length !== b.length)
    return false;
  // The number of price mods is the same. Compare the price mods. Both the modifier and duration
  // must match.
  for (i = 0; i < a.length; i++)
  {
    if ((a[i][c.apt.PRICE_MOD] !== b[i][c.apt.PRICE_MOD]) ||
      (a[i][c.apt.DURATION] !== b[i][c.apt.DURATION]))
      return false;
  }
  // The price mods all match. They're the same!
  return true;
}

// *************************************************************************************************
// Read the price mods associated with the currently selected product type, and provide a copy. If
// the administrator has made a custom set of price mods, those are used. If not, the default price
// mods for the currently selected product are copied. If no price mods currently exist, the
// function returns an empty array of price mods.
function copyCurrentPriceMods()
{
  var source, result, i;

  source = getPriceMods();
  if (source === null)
    return [];
  result = new Array(source.length);
  for (i = 0; i < source.length; i++)
    result[i] = Array.from(source[i]);
  return result;
}

// *************************************************************************************************
// Add an empty price mod to the list of price mods displayed in the edit price plan dialogue, then
// update the contents of the dialogue box.
function addEditedPriceMod()
{
  // Add a price mod with a 0% modifier and a one-month duration.
  editedPriceMods.push([0, 1]);
  updatePriceModEditorBox();
}

// *************************************************************************************************
// Read information from the user interface, and store it in the editedPriceMods array.
function storeEditedPriceModChanges()
{
  var i;

  for (i = 0; i < editedPriceMods.length; i++)
  {
    setEditedPriceModElement(i, c.apt.PRICE_MOD, 'modifier_', -1000, 1000);
    // A duration of 0 is indefinite.
    setEditedPriceModElement(i, c.apt.DURATION, 'duration_', 0, 24);
  }
  updatePriceModEditorBox();
}

// *************************************************************************************************
// Update the one value in the element with the given index in the editedPriceMods table by reading
// the value from the user interface. The value is located in the given column. It will be read from
// the edit box with the given label.
function setEditedPriceModElement(index, column, label, minValue, maxValue)
{
  var editBox, newValue;

  editBox = document.getElementById(label + String(index));
  newValue = Utility.getValidInteger(editBox.value, editedPriceMods[index][column]);
  if ((newValue >= minValue) && (newValue <= maxValue))
    editedPriceMods[index][column] = newValue;
}

// *************************************************************************************************

function deleteEditedPriceMod(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, editedPriceMods))
  {
    editedPriceMods.splice(index, 1);
    updatePriceModEditorBox();
  }
}

// *************************************************************************************************
