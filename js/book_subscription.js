// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// - The availableProductTypes table is loaded asynchronously, and is not found in the HTML when the
//   page loads.

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// The number of months from which subscription start dates can be selected. The first month is
// always the current one.
var SELECTABLE_MONTH_COUNT = 6;

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var locationBox, addressBox, selectedDateEdit, mapBox, categoriesBox, productsBox,
  selectInsuranceBox, insuranceDescriptionBox, paymentBox, tab0NextButton, tab1NextButton,
  tab2NextButton, tab3NextButton;

// The tabset that displays the steps in the booking process.
var tabset;

// The GibbsLeafletMap component used to display the map.
var map;

// The calendar component that allows the user to select the starting date for his subscription.
var calendar;

// The index in the locations table of the selected location, or -1 if no location is selected.
var selectedLocation = -1;

// The list of months that can be selected. Each entry is an object, with the following fields:
// displayName, year and month. The displayName holds both the name of the month and the year, for
// instance: "January 2024". The month is in Javascript format - that is, zero based.
var selectableMonths = new Array(SELECTABLE_MONTH_COUNT);

// The currently displayed month, as an index into the selectableMonths table. A month is always
// displayed, so -1 is not a valid value.
var displayedMonth = 0;

// The list of available product types, or null if the table has not yet been loaded. The table is
// loaded asynchronously, once the user has selected a location and a starting date.
var availableProductTypes = null;

// The index in the categories table of the selected category, or -1 if no category is selected.
var selectedCategory = -1;

// The index in the availableProductTypes table of the selected product type, or -1 if no product
// type is selected.
var selectedProductType = -1;

// The index in the insuranceProducts table of the selected insurance, or -1 if no insurance is
// selected.
var selectedInsurance = -1;

// Flag that says whether an alert error message has already been displayed. If so, we should not
// display another.
var errorDisplayed = false;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and displaying the first page of the tabset.
function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['locationBox', 'addressBox', 'selectedDateEdit', 'mapBox', 'categoriesBox',
    'productsBox', 'selectInsuranceBox', 'insuranceDescriptionBox', 'paymentBox', 'tab0NextButton',
    'tab1NextButton', 'tab2NextButton', 'tab3NextButton']);
  
  tabset = new Tabset(
    [
      getText(0, 'Velg lager'),
      getText(1, 'Velg startdato'),
      getText(2, 'Velg lagerbod'),
      getText(36, 'Velg forsikring'),
      getText(22, 'Oppsummering'),
      getText(11, 'Betaling')
    ], true);
  tabset.activeTab = 0;
  tab0NextButton.disabled = true;

  // Initialise the map.
  map = new GibbsLeafletMap('mapBox');

  // Create calendar component.
  calendar = new Calendar(SELECTABLE_MONTH_COUNT);
  calendar.dayNames = DAY_NAMES;
  calendar.monthNames = MONTH_NAMES;
  calendar.monthNamesInSentence = MONTH_NAMES_IN_SENTENCE;
  calendar.onSelectDate = selectDate;

  addressBox.innerHTML = '&nbsp;';
  Utility.hide(mapBox);
  tab0NextButton.disabled = true;
  displayLocationBox();
}

// *************************************************************************************************
// *** Map and location functions.
// *************************************************************************************************
// Display the list of available locations in the locations box. If a location is selected, it will
// be highlighted.
function displayLocationBox()
{
  var o, p, i;
  
  o = new Array((locations.length * 5) + 3);
  p = 0;

  o[p++] = '<div class="form-element list-caption">';
  o[p++] = getText(3, 'Velg lager:');
  o[p++] = '</div>';
  for (i = 0; i < locations.length; i++)
  {
    if (i === selectedLocation)
      o[p++] = '<button type="button" class="selected">';
    else
    {
      o[p++] = '<button type="button" onclick="selectLocation(';
      o[p++] = String(i);
      o[p++] = ');">';
    }
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</button><br />';
  }

  locationBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Select the location with the given index in the locations table.
function selectLocation(index)
{
  var o, p, address;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, locations))
  {
    selectedLocation = index;
    address = Utility.getAddress(locations[index]);
    // Update the list of locations to show the location as selected.
    displayLocationBox();

    // Display information about the selected location, including the map.
    o = new Array(15);
    p = 0;
    o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th colspan="2"><h3>';
    o[p++] = locations[index][c.loc.NAME];
    o[p++] = '</h3></th></tr></thead><tbody><tr><td>';
    o[p++] = getText(4, 'Adresse:');
    o[p++] = '</td><td>';
    o[p++] = address;
    o[p++] = '</td></tr><tr><td>';
    o[p++] = getText(25, '&Aring;pningstider:');
    o[p++] = '</td><td>';
    o[p++] = locations[index][c.loc.OPENING_HOURS];
    o[p++] = '</td></tr><tr><td>';
    o[p++] = getText(26, 'Tjenester:');
    o[p++] = '</td><td>';
    o[p++] = locations[index][c.loc.SERVICES];
    o[p++] = '</td></tr></tbody></table>';
    addressBox.innerHTML = o.join('');
    Utility.display(mapBox);
    map.displayAddress(address);
    tab0NextButton.disabled = false;
  }
}

// *************************************************************************************************
// *** Calendar functions.
// *************************************************************************************************
// Display the tab that allows the user to select the starting date for his subscription.
function displaySelectDatePage()
{
  selectedDateEdit.value = '';
  calendar.selectedDate = null;
  calendar.display();
  tabset.activeTab = 1;
  tab1NextButton.disabled = true;
}

// *************************************************************************************************
// Select the given date as the starting date of the user's subscription. dateString is a string
// with a date in ISO format - that is, "yyyy-mm-dd". Update the calendar to display the date as
// selected. We know the date is visible, as you can only select dates within the currently
// displayed month.
function selectDate(sender, selectedDate)
{
  selectedDateEdit.value = selectedDate;
  tab1NextButton.disabled = false;
}

// *************************************************************************************************
// *** Category and product functions.
// *************************************************************************************************
// Fetch the list of available product types from the server, based on the selected location and
// starting date. The server will return a table of product types, with information on their
// availability. A "please wait" message will be displayed in the meantime.
function findAvailableProducts()
{
  var o, p;

  availableProductTypes = null;
  selectedCategory = -1;
  selectedProductType = -1;
  tab2NextButton.disabled = true;
  categoriesBox.innerHTML = '&nbsp;';
  productsBox.innerHTML =
    '<p>' + getText(5, 'Finner ledige lagerboder. Vennligst vent...') + '</p>';
  tabset.activeTab = 2;

  o = new Array(4);
  p = 0;

  o[p++] = '/subscription/json/available_products.php?action=get_available_product_types&selected_location_id=';
  o[p++] = String(locations[selectedLocation][c.loc.ID]);
  o[p++] = '&selected_date=';
  o[p++] = calendar.selectedDate;

  fetch(o.join(''))
    .then(Utility.extractJson)
    .catch(logAvailableProductTypesError)
    .then(storeProducts)
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
function storeProducts(data)
{
  if (data)
  {
    if (data.resultCode && (data.resultCode >= 0))
    {
      alert(getText(27, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
        [String(data.resultCode)]));
      return;
    }
    if (data.availableProductTypes)
    {
      availableProductTypes = data.availableProductTypes;
      displayCategoriesTable();
    }
    else
      console.error('Error fetching available product types: product type data missing.');
  }
  else
    console.error('Wut?');
}

// *************************************************************************************************
// Display the list of categories, so that the user can select a category and see the list of
// available product types within that category.
function displayCategoriesTable()
{
  var o, p, i;

  o = new Array(categories.length * 5);
  p = 0;

  o[p++] = '<div class="form-element list-caption">';
  o[p++] = getText(24, 'Velg st&oslash;rrelse:');
  o[p++] = '</div>';
  for (i = 0; i < categories.length; i++)
  {
    if (i === selectedCategory)
      o[p++] = '<button type="button" class="selected">';
    else
    {
      o[p++] = '<button type="button" onclick="selectCategory(';
      o[p++] = String(i);
      o[p++] = ');">';
    }
    o[p++] = categories[i][c.cat.NAME];
    o[p++] = '</button><br />';
  }

  categoriesBox.innerHTML = o.join('');
  productsBox.innerHTML = '&nbsp;';
}

// *************************************************************************************************
// Select the category with the given index in the categories table, if the given index is valid.
// Display the category as selected, then display the product types within that category.
function selectCategory(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, categories))
  {
    selectedCategory = index;
    selectedProductType = -1;
    tab2NextButton.disabled = true;
    displayCategoriesTable();
    displayAvailableProductTypesTable();
  }
}

// *************************************************************************************************
// Display the list of available product types within the selected category. If a product type is
// selected, it will be highlighted. If a product type is unavailable, the reason may be displayed,
// as may eligible alternatives.
function displayAvailableProductTypesTable()
{
  var o, p, i, buttonId, alternativeCount, hasProductTypes;

  o = new Array((availableProductTypes.length * 23) + 6); // *** //
  p = 0;

  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>&nbsp;</th><th>';
  o[p++] = getText(6, 'Navn');
  o[p++] = '</th><th>';
  o[p++] = getText(7, 'Pris');
  o[p++] = '</th></tr></thead><tbody>';
  hasProductTypes = false;
  for (i = 0; i < availableProductTypes.length; i++)
  {
    // Only display product types in the selected category.
    if (availableProductTypes[i][c.apt.CATEGORY_ID] !== categories[selectedCategory][c.cat.ID])
      continue;

    hasProductTypes = true;
    buttonId = 'productButton_' + String(i);
    o[p++] = '<tr class="';
    if (availableProductTypes[i][c.apt.IS_AVAILABLE])
    {
      if (i === selectedProductType)
        o[p++] = 'selected';
      else
        o[p++] = 'enabled';
    }
    else
      o[p++] = 'disabled';
    o[p++] = '" onclick="selectProductType(';
    o[p++] = String(i);
    o[p++] = ');"><td>';
    if (availableProductTypes[i][c.apt.IS_AVAILABLE])
    {
      o[p++] = '<input type="radio" id="';
      o[p++] = buttonId;
      o[p++] = '" name="product" value="';
      o[p++] = availableProductTypes[i][c.apt.ID];
      if (i === selectedProductType)
        o[p++] = '" checked="checked">';
      else
        o[p++] = '">';
    }
    o[p++] = '</td><td><label for="';
    o[p++] = buttonId;
    o[p++] = '">';
    o[p++] = availableProductTypes[i][c.apt.NAME];
    o[p++] = '</label></td><td>';
    if (availableProductTypes[i][c.apt.IS_AVAILABLE])
    {
      o[p++] = getText(8, '$1,- pr mnd', [String(availableProductTypes[i][c.apt.PRICE])]);
      if ((settings.fewAvailableCount > 0) &&
        (availableProductTypes[i][c.apt.AVAILABLE_PRODUCT_IDS].length <= settings.fewAvailableCount))
      {
        o[p++] = '<br /><span class="help-text">';
        o[p++] = getText(33, 'Obs! Bare noen f&aring; ledige lagerboder igjen.');
        o[p++] = '</span>';
      }
    }
    else
    {
      o[p++] = getText(9, 'Ingen ledige lagerboder');
      if (availableProductTypes[i][c.apt.FIRST_AVAILABLE_DATE] !== null)
      {
        o[p++] = '<br /><span class="help-text">';
        o[p++] = getText(10, '- Det blir en bod ledig p&aring; dette lageret fra $1',
          [availableProductTypes[i][c.apt.FIRST_AVAILABLE_DATE]]);
        o[p++] = '</span>';
      }
      o[p++] = '<br /><span class="help-text">';
      alternativeCount = availableProductTypes[i][c.apt.ALTERNATIVE_LOCATION_IDS].length;
      if (alternativeCount <= 0)
          o[p++] = getText(28,
            '- Ingen andre lager har ledig lagerbod av denne typen.');
      else
      {
        if (alternativeCount === 1)
          o[p++] = getText(29,
            '- Ett annet lager har ledig lagerbod av denne typen.');
        else
          o[p++] = getText(23,
            '- $1 andre lager har ledig lagerbod av denne typen.',
            [String(alternativeCount)]);
        o[p++] = '<a href=\"\" class="button wide-button" onclick="return displayAlternativeLocations(';
        o[p++] = String(i);
        o[p++] = ');">';
        o[p++] = getText(30, 'Vis alternativene');
        o[p++] = '</a>';
      }
      o[p++] = '</span>';
    }
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  // If we had product types, display the table. If not, just display a message.
  if (hasProductTypes)
    productsBox.innerHTML = o.join('');
  else
    productsBox.innerHTML = '<div class="form-element">' +
      getText(32, 'Lageret har ingen lagerboder i denne kategorien.') + '</div>';
}

// *************************************************************************************************
// Display a list of alternative locations for the product type with the given index in the
// availableProductTypes table. This method always returns false.
function displayAlternativeLocations(index)
{
  var o, p, i, alternatives;

  alternatives = availableProductTypes[index][c.apt.ALTERNATIVE_LOCATION_IDS];
  if (alternatives.length > 0)
  {
    o = new Array((alternatives.length * 2) + 1);
    p = 0;

    o[p++] = getText(31, 'Denne bodtypen er ledig på følgende lager fra datoen du valgte:\r\n\r\n');
    for (i = 0; i < alternatives.length; i++)
    {
      o[p++] = Utility.getLocationName(alternatives[i], '');
      o[p++] = '\r\n';
    }
    alert(o.join(''));
  }
  return false;
}

// *************************************************************************************************
// Select the product type with the given index in the available product types table, if the given
// index is valid. Also, the product type can only be selected if it is available. Update the list
// of available product types to display the product type as selected.
function selectProductType(index)
{
  var index;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, availableProductTypes) &&
    availableProductTypes[index][c.apt.IS_AVAILABLE])
  {
    selectedProductType = index;
    displayAvailableProductTypesTable();
    tab2NextButton.disabled = false;
  }
}

// *************************************************************************************************
// *** Insurance functions.
// *************************************************************************************************
// Initialise and display the select insurance tab.
function displayInsurancePage()
{
  selectedInsurance = -1;
  displayInsuranceProducts();
  tabset.activeTab = 3;
  tab3NextButton.disabled = true;
}

// *************************************************************************************************
// Display the list of eligible insurance products. Insurances are not always offered at all
// locations, or for all products. The list will only display the insurances available for the
// selected product type at the selected location.
function displayInsuranceProducts()
{
  var o, p, i;
  
  o = new Array((insuranceProducts.length * 5) + 3);
  p = 0;

  o[p++] = '<div class="form-element list-caption">';
  o[p++] = getText(37, 'Velg forsikring:');
  o[p++] = '</div>';
  for (i = 0; i < insuranceProducts.length; i++)
  {
    if (!availableForProductType(i, selectedProductType) ||
      !availableForLocation(i, selectedLocation))
      continue;

    if (i === selectedInsurance)
      o[p++] = '<button type="button" class="selected">';
    else
    {
      o[p++] = '<button type="button" onclick="selectInsurance(';
      o[p++] = String(i);
      o[p++] = ');">';
    }
    o[p++] = insuranceProducts[i][c.ins.NAME];
    o[p++] = '</button><br />';
  }

  selectInsuranceBox.innerHTML = o.join('');
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
// Select the insurance product with the given index in the insuranceProducts table, and update the
// user interface. This includes displaying detailed information about the insurance product.
function selectInsurance(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, insuranceProducts))
  {
    selectedInsurance = index;
    // Update the list of insurance products to show the insurance product as selected.
    displayInsuranceProducts();

    // Display information about the selected insurance product.
    o = new Array(9);
    p = 0;
    o[p++] = '<h3>';
    o[p++] = insuranceProducts[index][c.ins.NAME];
    o[p++] = '</h3><p>';
    o[p++] = insuranceProducts[index][c.ins.DESCRIPTION];
    o[p++] = '</p><p>';
    o[p++] = getText(38, 'Pris pr mnd:');
    o[p++] = ' ';
    o[p++] = insuranceProducts[index][c.ins.PRICE];
    o[p++] = ',-</p>';

    insuranceDescriptionBox.innerHTML = o.join('');
    tab3NextButton.disabled = false;
  }
}

// *************************************************************************************************
// *** Payment functions.
// *************************************************************************************************
// Display the payment summary tab. This shows the user how much he will be charged, and what
// happens when he cancels the subscription. This tab contains only text.
function displayPaymentInfo()
{
  var o, p, startDate, lastDayOfMonth, daysLeft, pricePerMonth, priceThisMonth, month,
    insurancePerMonth, insuranceThisMonth;

  pricePerMonth = availableProductTypes[selectedProductType][c.apt.PRICE];
  insurancePerMonth = insuranceProducts[selectedInsurance][c.ins.PRICE]
  startDate = new Date(calendar.selectedDate);
  month = new CalendarMonth(startDate.getFullYear(), startDate.getMonth(), MONTH_NAMES,
    MONTH_NAMES_IN_SENTENCE);
  if (startDate.getDate() !== 1)
  {
    // Get the last day of the month.
    var endDate = new Date(month.year, month.month + 1, 0);
    lastDayOfMonth = endDate.getDate();
    // Calculate days left in the month.
    daysLeft = lastDayOfMonth - startDate.getDate() + 1;

    priceThisMonth = Math.floor((daysLeft / lastDayOfMonth) * pricePerMonth);
    insuranceThisMonth = Math.floor((daysLeft / lastDayOfMonth) * insurancePerMonth);
  }
  else
  {
    priceThisMonth = 0;
    insuranceThisMonth = 0;
  }

  o = new Array(41); // *** //
  p = 0;

  o[p++] = '<ul><li>';
  o[p++] = getText(12, 'Du overtar lagerboden $1.', [calendar.selectedDate]);
  o[p++] = '<br />';
  o[p++] = getText(34, 'Lager: ');
  o[p++] = locations[selectedLocation][c.loc.NAME];
  o[p++] = '<br />';
  o[p++] = getText(35, 'Bodtype: ');
  o[p++] = availableProductTypes[selectedProductType][c.apt.NAME];
  o[p++] = '</li><li>';
  o[p++] = getText(13,
    'M&aring;nedsprisen er $1 kr for leie av lagerbod, og $2 kr for forsikring. Til sammen: $3 kr.',
    [String(pricePerMonth), String(insurancePerMonth), String(pricePerMonth + insurancePerMonth)]);
  o[p++] = '</li><li>';
  o[p++] = getText(14, 'Du vil n&aring; betale for:');
  o[p++] = '<table cellspacing="0" cellpadding="0" class="payment-overview"><tbody>';
  if (startDate.getDate() !== 1)
  {
    // Storage price this month.
    o[p++] = '<tr><td>';
    o[p++] = getText(39, 'Leie av lagerbod, ');
    o[p++] = getText(15, '$1 av $2 dager i $3:',
      [String(daysLeft), String(lastDayOfMonth), month.displayNameInSentence]);
    o[p++] = '</td><td>';
    o[p++] = String(daysLeft);
    o[p++] = '/';
    o[p++] = String(lastDayOfMonth);
    o[p++] = ' * ';
    o[p++] = String(pricePerMonth);
    o[p++] = ' =</td><td class="amount">';
    o[p++] = getText(16, '$1 kr', [String(priceThisMonth)]);
    o[p++] = '</td></tr>';

    // Insurance price this month.
    o[p++] = '<tr><td>';
    o[p++] = getText(40, 'Forsikring, ');
    o[p++] = getText(15, '$1 av $2 dager i $3:',
      [String(daysLeft), String(lastDayOfMonth), month.displayNameInSentence]);
    o[p++] = '</td><td>';
    o[p++] = String(daysLeft);
    o[p++] = '/';
    o[p++] = String(lastDayOfMonth);
    o[p++] = ' * ';
    o[p++] = String(insurancePerMonth);
    o[p++] = ' =</td><td class="amount">';
    o[p++] = getText(16, '$1 kr', [String(insuranceThisMonth)]);
    o[p++] = '</td></tr>';

    month = month.getNextMonth();
  }
  // Storage price next month.
  o[p++] = '<tr><td colspan="2">';
  o[p++] = getText(39, 'Leie av lagerbod, ');
  o[p++] = month.displayNameInSentence;
  o[p++] = ':</td><td class="amount">';
  o[p++] = getText(16, '$1 kr', [String(pricePerMonth)]);
  // Insurance next month.
  o[p++] = '</td></tr><tr><td colspan="2">';
  o[p++] = getText(40, 'Forsikring, ');
  o[p++] = month.displayNameInSentence;
  o[p++] = ':</td><td class="amount">';
  o[p++] = getText(16, '$1 kr', [String(insurancePerMonth)]);
  // Sum.
  o[p++] = '</td></tr><tr><td colspan="2" class="sum">';
  o[p++] = getText(17, 'Til sammen:');
  o[p++] = '</td><td class="amount sum">'
  o[p++] = getText(16, '$1 kr',
    [String(priceThisMonth + pricePerMonth + insuranceThisMonth + insurancePerMonth)]);
  o[p++] ='</td></tr></table></li><li>';

    month = month.getNextMonth();
  o[p++] = getText(18,
    'Fra og med $1 trekkes du automatisk $2 kr den f&oslash;rste i hver m&aring;ned, inntil du sier opp abonnementet.',
    [month.displayNameInSentence, String(pricePerMonth + insurancePerMonth)]);
  o[p++] = '</li><li>';
  o[p++] = getText(19, 'Du kan n&aring;r som helst si opp abonnementet fra Min side.');
  o[p++] = '<br />';
  o[p++] = getText(20, '- Hvis du sier opp f&oslash;r midten av m&aring;neden (til og med 15.) disponerer du lagerboden fram til slutten av m&aring;neden.');
  o[p++] = '<br />';
  o[p++] = getText(21, '- Hvis du sier opp etter midten av m&aring;neden, betaler du ogs&aring; for neste m&aring;ned, og disponerer lagerboden fram til slutten av neste m&aring;ned.');
  o[p++] = '</li></ul>';

  paymentBox.innerHTML = o.join('');
  tabset.activeTab = 4;
}

// *************************************************************************************************
// Submit an asynchronous POST request to the server, in order to create the subscription (it will
// be deleted again if the payment fails). Once done, the user will be redirected to the payment
// page, in order to select means of payment and enter the relevant details.
function confirmAndPay()
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('action', 'create_subscription');
  requestData.append('location_id', String(locations[selectedLocation][c.loc.ID]));
  requestData.append('start_date', calendar.selectedDate);
  requestData.append('insurance_id', String(insuranceProducts[selectedInsurance][c.ins.ID]));
  requestData.append('product_ids',
    availableProductTypes[selectedProductType][c.apt.AVAILABLE_PRODUCT_IDS]);
  options =
    {
      method: 'POST',
      body: requestData
    };
  fetch('/subscription/json/create_subscription.php', options)
    .then(Utility.extractJson)
    .catch(logPaymentError)
    .then(storePaymentId)
    .catch(logPaymentError);
}

// *************************************************************************************************

function storePaymentId(data)
{
  var resultCode;

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Assume something failed.
  resultCode = result.PAYMENT_FAILED;
  if (data)
  {
    // If a more specific error code was returned, display that instead.
    if (data.resultCode && (data.resultCode >= 0))
      resultCode = data.resultCode;
    else
    {
      if (data.paymentId && (data.paymentId !== ''))
      {
        // The payment was created successfully. Move on to ask the customer to pay.
        window.location.href = '/subscription/html/pay.php?paymentId=' + String(data.paymentId);
        return;
      }
    }
  }
  errorDisplayed = true;
  alert(getText(27, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
    [String(resultCode)]));
  //window.location.href = '/subscription/html/user_dashboard.php';
}

// *************************************************************************************************
// Log an error that occurred while creating a subscription and processing the payment.
function logPaymentError(error)
{
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while creating a subscription and processing the payment: ' + error);
    alert(getText(27, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
      [String(result.ASYNCHRONOUS_REQUEST_FAILED)]));
   // window.location.href = '/subscription/html/user_dashboard.php';
  }
}

// *************************************************************************************************
