// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************
// Each product can have several statuses (see status constants from common.js):
//   0: Never rented; not booked
//     Display nothing.
//   1: Not currently rented; not booked
//     Display the date the last booking ended.
//   2: Never rented; booked from some future date
//     Display the date the next booking starts.
//   3: Not currently rented; booked from some future date
//     Display the date the last booking ended.
//     Display the date the next booking starts.
//   4: Currently rented; not cancelled
//     Display the date the current booking started.
//     Display the number of years, months, days the current booking has lasted.
//   5: Currently rented; cancelled from some future date; not booked
//     Display the date the current booking started.
//     Display the number of years, months, days the current booking will have lasted when it ends.
//     Display the date the current booking ends.
//   6: Currently rented; cancelled from some future date; booked from some future date
//     Display the date the current booking started.
//     Display the number of years, months, days the current booking will have lasted when it ends.
//     Display the date the current booking ends.
//     Display the date the next booking starts.

// Colour codes:
//   Previous booking: grey
//   Current booking, ongoing: green
//   Current booking, cancelled: orange
//   Future booking: green

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// The filter settings for the various tabs in the filter tabset toolbar. Each entry contains an
// object with three fields: statusFilter, readyStatusFilter and enabledFilter. Each of these is
// either null (in case there is no filtering for that component, or an array of filter values. Note
// that each array must be sorted in increasing order. Tabs contain:
//   * All: no filter.
//   * Check-in / check-out: product status "moving out" or "moving in".
//   * Needs check: ready status is "check".
//   * Rented: product status "utleid".
//   * Free: product status "ledig".
//   * Inactive: enabled is false.
var FILTER_TABSET_PRESETS =
  [
    {
      statusFilter: null,
      readyStatusFilter: null,
      enabledFilter: null
    },
    {
      statusFilter: [st.prod.BOOKED, st.prod.VACATED_BOOKED, st.prod.CANCELLED, st.prod.CANCELLED_BOOKED],
      readyStatusFilter: null,
      enabledFilter: null
    },
    {
      statusFilter: null,
      readyStatusFilter: [st.ready.CHECK],
      enabledFilter: null
    },
    {
      statusFilter: [st.prod.RENTED],
      readyStatusFilter: null,
      enabledFilter: null
    },
    {
      statusFilter: [st.prod.NEW, st.prod.VACATED],
      readyStatusFilter: null,
      enabledFilter: null
    },
    {
      statusFilter: null,
      readyStatusFilter: null,
      enabledFilter: [0]
    }
  ];

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var productsBox, filterToolbar, overlay, createSubscriptionDialogue, cancelSubscriptionDialogue,
  productNotesDialogue, accessInformationDialogue, editLocationFilterDialogue,
  editProductTypeFilterDialogue, editTabsetFiltersDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var freetextEdit, createSubscriptionForm, subscriptionStartDateEdit, subscriptionSubmitButton,
  cancelSubscriptionForm, standardCancelBox, immediateCancelBox, customCancelBox,
  customCancelResultBox, endDateEdit, openCalendarButton, closeCalendarButton, calendarBox,
  productNotesTextArea;

// Filter tabset which filters on product status.
var filterTabset = null;

// The sorting object that controls the sorting of the products table.
var sorting;

// The popup menu for the products table.
var menu;

// The number of displayed products. This depends on the current filter settings.
var displayedCount = 0;

// The calendar component that allows the user to select the end date when cancelling a
// subscription.
var calendar;

// Flag that says whether an alert error message has already been displayed. If so, we should not
// display another.
var errorDisplayed = false;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  var i;

  // Ensure product notes do not contain encoded line breaks.
  for (i = 0; i < products.length; i++)
    products[i][c.prd.NOTES] = Utility.decodeLineBreaks(products[i][c.prd.NOTES]);

  // Obtain pointers to user interface elements.
  Utility.readPointers(['productsBox', 'filterToolbar', 'overlay', 'createSubscriptionDialogue',
    'cancelSubscriptionDialogue', 'productNotesDialogue', 'accessInformationDialogue',
    'editLocationFilterDialogue', 'editProductTypeFilterDialogue', 'editTabsetFiltersDialogue']);

  // Create the filter tabset.
  filterTabset = new FilterTabset(FILTER_TABSET_TEXTS, FILTER_TABSET_PRESETS);
  setFilterTabsetItemCounts();
  filterTabset.setActiveTabFromFilter(
    {
      statusFilter: statusFilter,
      readyStatusFilter: readyStatusFilter,
      enabledFilter: enabledFilter
    });
  filterTabset.onChangeTab = changeFilterTabsetTab;
  filterTabset.onConfigure = displayStatusFilterDialogue;

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents, 260);

  // Initialise sorting.
  sorting = new Sorting(products,
      [
        Sorting.createUiColumn(c.prd.LOCATION_ID, Sorting.SORT_AS_STRING,
          function (product)
          {
            return Utility.getLocationName(product[c.prd.LOCATION_ID]) + ' ' + product[c.prd.NAME];
          }),
        Sorting.createUiColumn(c.prd.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.prd.PRODUCT_TYPE_ID, Sorting.SORT_AS_STRING,
          function (product)
          {
            return Utility.getProductTypeName(product[c.prd.PRODUCT_TYPE_ID]);
          }),
        Sorting.createUiColumn(c.prd.READY_STATUS, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.prd.STATUS, Sorting.SORT_AS_STRING,
          function (product)
          {
            return st.prod.TEXTS_BRIEF[product[c.prd.STATUS]];
          }),
        Sorting.createUiColumn(c.prd.END_DATE, Sorting.SORT_AS_STRING,
          function (product, sorting)
          {
            var placeholder;

            // Sort all end dates first, in ascending or descending order. Then all reserved dates,
            // and then all products that don't have either date, by last modified date.
            placeholder = (sorting.direction === Sorting.DIR_ASCENDING ? 'z' : '0');
            return (product[c.prd.END_DATE] === '' ? placeholder : product[c.prd.END_DATE]) +
              (product[c.prd.RESERVED_DATE] === '' ? placeholder : product[c.prd.RESERVED_DATE]) +
              product[c.prd.MODIFIED_DATE];
          }),
        Sorting.createUiColumn(c.prd.RESERVED_DATE, Sorting.SORT_AS_STRING,
          function (product, sorting)
          {
            var placeholder;

            // Sort all reserved dates first, in ascending or descending order. Then all end dates,
            // and then all products that don't have either date, by last modified date.
            placeholder = (sorting.direction === Sorting.DIR_ASCENDING ? 'z' : '0');
            return (product[c.prd.RESERVED_DATE] === '' ? placeholder : product[c.prd.RESERVED_DATE]) +
              (product[c.prd.END_DATE] === '' ? placeholder : product[c.prd.END_DATE]) +
              product[c.prd.MODIFIED_DATE];
          }),
        Sorting.createUiColumn(c.prd.ENABLED, Sorting.SORT_AS_BOOLEAN),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayProducts
    );
  // Set the initial sorting. If that didn't cause products to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayProducts();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Return hidden form elements that specify the current state of the page, including sorting, search
// and filter settings. These should be included whenever a request is submitted to the current
// page, so that the state is maintained when the page is reloaded.
function getPageStateFormElements()
{
  var o, p;

  o = new Array(7);
  p = 0;

  if (locationFilter !== null)
    o[p++] = Utility.getHidden('location_filter', locationFilter.join(','));
  if (productTypeFilter !== null)
    o[p++] = Utility.getHidden('product_type_filter', productTypeFilter.join(','));
  if (statusFilter !== null)
    o[p++] = Utility.getHidden('status_filter', statusFilter.join(','));
  if (readyStatusFilter !== null)
    o[p++] = Utility.getHidden('ready_status_filter', readyStatusFilter.join(','));
  if (enabledFilter !== null)
    o[p++] = Utility.getHidden('enabled_filter', enabledFilter.join(','));
  if (freetextFilter !== '')
    o[p++] = Utility.getHidden('freetext_filter', freetextFilter);
  o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// Open the booking page with the product with the given index in the products table pre-selected.
// The product's location is also pre-selected. If the product is cancelled, the start date is set
// to the day after the current subscription ends.
function bookProduct(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, products))
  {
    o = new Array(6);
    p = 0;

    // Redirect to the booking page, with this product preselected.
    o[p++] = '/subscription/html/admin_book_subscription.php?initial_location_id=';
    o[p++] = String(products[index][c.prd.LOCATION_ID]);
    o[p++] = '&initial_product_id=';
    o[p++] = String(products[index][c.prd.ID]);
    // If the product is cancelled, set the initial_date to the day after the subscription's end
    // date.
    if (products[index][c.prd.STATUS] === st.prod.CANCELLED)
    {
      o[p++] = '&initial_date=';
      o[p++] = Utility.getDayAfter(products[index][c.prd.END_DATE]);
    }

    Utility.displaySpinnerThenGoTo(o.join(''));
  }
}

// *************************************************************************************************
// Products table functions.
// *************************************************************************************************
// Display the spinner. Once visible, display products.
function displayProducts()
{
  Utility.displaySpinnerThen(doDisplayProducts);
}

// *************************************************************************************************
// Display the list of products.
function doDisplayProducts()
{
  var o, p, i;
  
  if (products.length <= 0)
  {
    productsBox.innerHTML = '<div class="form-element">' +
      getText(14, 'Det er ikke opprettet noen lagerboder enn&aring;.') + '</div>';
    filterToolbar.innerHTML = '&nbsp;';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((products.length * 21) + 13);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(2, 'Lager'));
  o[p++] = sorting.getTableHeader(1, getText(3, 'Lagerbod'));
  o[p++] = sorting.getTableHeader(2, getText(4, 'Bodtype'));
  o[p++] = sorting.getTableHeader(3, getText(36, 'M&aring; sjekkes'));
  o[p++] = sorting.getTableHeader(4, getText(5, 'Utleiestatus'));
  o[p++] = sorting.getTableHeader(5, getText(6, 'Flytter ut'));
  o[p++] = sorting.getTableHeader(6, getText(7, 'Flytter inn'));
  o[p++] = sorting.getTableHeader(7, getText(37, 'Bodstatus'));
  o[p++] = sorting.getTableHeader(8, getText(59, 'Notater'));
  o[p++] = sorting.getTableHeader(9, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < products.length; i++)
  {
    if (shouldHide(products[i]))
      continue;
    displayedCount++;

    // Location name.
    o[p++] = '<tr><td>';
    o[p++] = Utility.getLocationName(products[i][c.prd.LOCATION_ID]);
    // product name.
    o[p++] = '</td><td>';
    o[p++] = products[i][c.prd.NAME];
    // Product type.
    o[p++] = '</td><td>';
    o[p++] = Utility.getProductTypeName(products[i][c.prd.PRODUCT_TYPE_ID]);
    // Ready status.
    o[p++] = '</td><td>';
    o[p++] = Utility.getStatusLabel(st.ready.TEXTS, st.ready.COLOURS,
      products[i][c.prd.READY_STATUS], st.ready.ICONS);
    // Status image and text.
    o[p++] = '</td><td>';
    if (false)
    {
      o[p++] = '<img src="/subscription/resources/status_';
      o[p++] = String(products[i][c.prd.STATUS]);
      o[p++] = '.png?v=';
      o[p++] = String(BUILD_NO);
      o[p++] = '" alt="';
      o[p++] = st.prod.TEXTS_BRIEF[products[i][c.prd.STATUS]];
      o[p++] = '" class="status-image" />&nbsp;';
    }
    o[p++] = Utility.getStatusLabel(st.prod.TEXTS_BRIEF, st.prod.COLOURS,
      products[i][c.prd.STATUS]);
    // End date.
    o[p++] = '</td><td>';
    if (products[i][c.prd.END_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = products[i][c.prd.END_DATE];
    // Reservation date.
    o[p++] = '</td><td>';
    if (products[i][c.prd.RESERVED_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = products[i][c.prd.RESERVED_DATE];
    // Enabled status.
    o[p++] = '</td><td>';
    o[p++] = Utility.getStatusLabel(st.enabled.TEXTS, st.enabled.COLOURS,
      (products[i][c.prd.ENABLED] ? 1 : 0), st.enabled.ICONS);
    // Product notes.
    o[p++] = '</td><td>';
    if (products[i][c.prd.NOTES] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = Utility.curtail(products[i][c.prd.NOTES], 25);
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  productsBox.innerHTML = o.join('');
  displayFilterToolbar();
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return true if the product with the given index in the products table is free and can be booked.
// This is the case if it has status NEW, VACATED or CANCELLED, is enabled and has ready status
// "yes". Note that, if the status is CANCELLED, the product cannot be booked until the current
// subscription ends. This function assumes that the product's status has been set for the date on
// which we need to know whether or not it is free. If the product was not found, the function will
// return false.
function isProductFree(index)
{
  index = parseInt(index, 10);
  return Utility.isValidIndex(index, products) &&
    ((products[index][c.prd.STATUS] === st.prod.NEW) ||
    (products[index][c.prd.STATUS] === st.prod.VACATED) ||
    (products[index][c.prd.STATUS] === st.prod.CANCELLED)) &&
    products[index][c.prd.ENABLED] &&
    (products[index][c.prd.READY_STATUS] === st.ready.YES);
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, products))
    return '';
  o = new Array(13);
  p = 0;

  // Set ready status YES button.
  if (canSetReadyStatusYes(index))
    o[p++] = sender.getMenuItem(getText(40, 'Klar for utleie'), st.ready.ICONS[0] + ' icon-green',
      true, 'setReadyStatus(' + String(index) + ', st.ready.YES);');

  // Set ready status CHECK button.
  if (canSetReadyStatusCheck(index))
    o[p++] = sender.getMenuItem(getText(41, 'M&aring; sjekkes'), st.ready.ICONS[1] + ' icon-red',
      true, 'setReadyStatus(' + String(index) + ', st.ready.CHECK);');

  // Set enabled flag button. The button is visible if the item is not enabled.
  if (canSetEnabledFlag(index))
  {
    o[p++] = sender.getMenuItem(getText(34, 'Aktiver'), st.enabled.ICONS[1] + ' icon-green', true,
      'setEnabled(' + String(index) + ', true);');
    o[p++] = '<br />';
  }

  // Remove enabled flag button. The button is visible if the item is enabled.
  if (canClearEnabledFlag(index))
  {
    o[p++] = sender.getMenuItem(getText(35, 'Deaktiver'), st.enabled.ICONS[0] + ' icon-red', true,
      'setEnabled(' + String(index) + ', false);');
    o[p++] = '<br />';
  }

  // Edit product notes button.
  o[p++] = sender.getMenuItem(getText(61, 'Se notater'), 'fa-file-pen', true,
    'displayProductNotes(' + String(index) + ');');
  // Display access information button.
  o[p++] = sender.getMenuItem(getText(67, 'Vis adgangsinformasjon'), 'fa-key', true,
    'displayAccessInformation(' + String(index) + ');');
  o[p++] = '<br />';

  // Book product button.
  o[p++] = sender.getMenuItem(getText(8, 'Bestill'), 'fa-plus', isProductFree(index),
    'bookProduct(' + String(index) + ');');
  // Cancel subscription button. The button is visible if the product has an ongoing subscription.
  o[p++] = sender.getMenuItem(getText(42, 'Si opp abonnement'), 'fa-hand-wave',
    products[index][c.prd.STATUS] === st.prod.RENTED,
    'displayCancelSubscriptionDialogue(' + String(index) + ');');
  // Create test subscription button.
  if (settings.applicationRole !== APP_ROLE_PRODUCTION)
    o[p++] = sender.getMenuItem(getText(15, 'Lag testabonnement'), 'fa-repeat', true,
      'displayCreateSubscriptionDialogue(' + String(index) + ');');
  // Customer link buttons.
  o[p++] = getCustomerLinks(index);
  return o.join('');
}

// *************************************************************************************************
// Return HTML code for a set of buttons that link to the previous, current and next subscribers, if
// they exist.
// - A link to the previous subscriber is provided if the product is VACATED or VACATED_BOOKED.
// - A link to the current subscriber is provided if the product is RENTED, CANCELLED or
//   CANCELLED_BOOKED.
// - A link to the next subscriber is provided if the product is BOOKED, VACATED_BOOKED or
//   CANCELLED_BOOKED.
function getCustomerLinks(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, products))
    return '';
  o = new Array(4);
  p = 0;

  o[p++] = '<br />';
  // Generate link to the previous subscriber, if the product's subscription has ended.
  o[p++] = getCustomerLinkButton(getPreviousSubscriberId(products[index]), 'previous_customer',
    getText(9, 'Forrige leietaker'));
  
  // Generate link to the current subscriber, if the product is currently rented out.
  o[p++] = getCustomerLinkButton(getCurrentSubscriberId(products[index]), 'current_customer',
    getText(10, 'N&aring;v&aelig;rende leietaker'));
  
  // Generate link to the next subscriber, if the product has been booked.
  o[p++] = getCustomerLinkButton(getNextSubscriberId(products[index]), 'next_customer',
    getText(11, 'Kommende leietaker'));
  return o.join('');
}

// *************************************************************************************************
// Return a single button that links to the admin_edit_user page for the user with the given
// targetUserId. If targetUserId is -1, the button will be disabled. imageName is the name of the
// button icon, without the extension. imageName can be either "previous_customer",
// "current_customer" or "next_customer". A disabled button will use the same image name, with
// "_disabled" appended. All images are PNGs. altText is the alt thext for the image.
function getCustomerLinkButton(targetUserId, imageName, text)
{
  o = new Array(13);
  p = 0;

  if (targetUserId >= 0)
  {
    // Write an enabled button.
    o[p++] = '<button type="button" class="menu-item" onclick="Utility.getInstance(';
    o[p++] = String(menu.registryIndex);
    o[p++] = ')._close(); Utility.displaySpinnerThenGoTo(\'/subscription/html/admin_edit_user.php?user_id=';
    o[p++] = String(targetUserId);
    o[p++] = '\');"><span>';
    o[p++] = text;
    o[p++] = '</span> <img src="/subscription/resources/';
    o[p++] = imageName;
    o[p++] = '.png?v=';
    o[p++] = String(BUILD_NO);
    o[p++] = '" alt="';
    o[p++] = text;
    o[p++] = '" /></button>';
  }
  else
  {
    // Write a disabled button.
    o[p++] = '<button type="button" class="menu-item" disabled="disabled"><span>';
    o[p++] = text;
    o[p++] = '</span> <img src="/subscription/resources/';
    o[p++] = imageName;
    o[p++] = '_disabled.png?v=';
    o[p++] = String(BUILD_NO);
    o[p++] = '" alt="';
    o[p++] = text;
    o[p++] = '" /></button>';
  }
  return o.join('');
}

// *************************************************************************************************
// Return the ID of the previous subscriber, or -1 if there is none, or none should be displayed.
// The ID is provided if the given product is VACATED or VACATED_BOOKED. The ID is drawn from the
// subscription with status EXPIRED that has the latest END_DATE.
function getPreviousSubscriberId(product)
{
  var subscriptions, thisEndDate, lastEndDate, lastId, i;

  // To begin with, we have found no expired subscriptions.
  lastEndDate = null;
  lastId = -1;
  // Consult the product status, to see if a subscription with the right properties will be found.
  if ((product[c.prd.STATUS] === st.prod.VACATED) ||
    (product[c.prd.STATUS] === st.prod.VACATED_BOOKED))
  {
    // Check all subscriptions that this product has ever had.
    subscriptions = product[c.prd.SUBSCRIPTIONS];
    for (i = 0; i < subscriptions.length; i++)
    {
      if (subscriptions[i][c.prs.STATUS] == st.sub.EXPIRED)
      {
        // This subscription is expired. See if we either haven't found any expired subscriptions
        // yet - or, if we have, whether this subscription's end date is later than the previously
        // found end date.
        thisEndDate = new Date(subscriptions[i][c.prs.END_DATE]);
        if ((lastEndDate === null) || (thisEndDate > lastEndDate))
        {
          // This expired subscription is later than any we have found before. Store it.
          lastEndDate = thisEndDate;
          lastId = subscriptions[i][c.prs.USER_ID];
        }
      }
    }
  }
  return lastId;
}

// *************************************************************************************************
// Return the ID of the current subscriber, of -1 if there is none. The ID is provided if the given
// product is RENTED, CANCELLED or CANCELLED_BOOKED. The ID is drawn from the first subscription
// that is either ONGOING or CANCELLED.
function getCurrentSubscriberId(product)
{
  var subscriptions, i;

  // Consult the product status, to see if a subscription with the right properties will be found.
  if ((product[c.prd.STATUS] === st.prod.RENTED) ||
    (product[c.prd.STATUS] === st.prod.CANCELLED) ||
    (product[c.prd.STATUS] === st.prod.CANCELLED_BOOKED))
  {
    // Check all subscriptions that this product has ever had.
    subscriptions = product[c.prd.SUBSCRIPTIONS];
    for (i = 0; i < subscriptions.length; i++)
    {
      // If the subscription is active, return the buyer's ID.
      if ((subscriptions[i][c.prs.STATUS] == st.sub.ONGOING) ||
        (subscriptions[i][c.prs.STATUS] == st.sub.CANCELLED))
        return subscriptions[i][c.prs.USER_ID];
    }
  }
  return -1;
}

// *************************************************************************************************
// Return the ID of the next subscriber, or -1 if there is none. The ID is provided if the given
// product is BOOKED, VACATED_BOOKED or CANCELLED_BOOKED. The ID is drawn from the first
// subscription that is BOOKED.
function getNextSubscriberId(product)
{
  var subscriptions, i;

  // Consult the product status, to see if a subscription with the right properties will be found.
  if ((product[c.prd.STATUS] === st.prod.BOOKED) ||
    (product[c.prd.STATUS] === st.prod.VACATED_BOOKED) ||
    (product[c.prd.STATUS] === st.prod.CANCELLED_BOOKED))
  {
    // Check all subscriptions that this product has ever had.
    subscriptions = product[c.prd.SUBSCRIPTIONS];
    for (i = 0; i < subscriptions.length; i++)
    {
      // If the subscription is booked, return the buyer's ID.
      if (subscriptions[i][c.prs.STATUS] == st.sub.BOOKED)
        return subscriptions[i][c.prs.USER_ID];
    }
  }
  return -1;
}

// *************************************************************************************************
// Ready status functions.
// *************************************************************************************************
// Return true if the product with the given index in the products table can have its ready status
// set to st.ready.YES.
function canSetReadyStatusYes(index)
{
  index = parseInt(index, 10);
  return Utility.isValidIndex(index, products) &&
    (products[index][c.prd.READY_STATUS] !== st.ready.YES);
}

// *************************************************************************************************
// Return true if the product with the given index in the products table can have its ready status
// set to st.ready.CHECK.
function canSetReadyStatusCheck(index)
{
  index = parseInt(index, 10);
  return Utility.isValidIndex(index, products) && 
    (products[index][c.prd.READY_STATUS] !== st.ready.CHECK);
}

// *************************************************************************************************
// Display the spinner, then set the ready status for the item with the given index in the products
// table to the given newStatus. For newStatus, use the st.ready constants.
function setReadyStatus(index, newStatus)
{
  index = parseInt(index, 10);
  newStatus = parseInt(newStatus, 10);
  if (Utility.isValidIndex(index, products) && Utility.isValidReadyStatus(newStatus))
  {
    Utility.displaySpinnerThen(
        function ()
        {
          doSetReadyStatus(index, newStatus);
        }
      );
  }
}

// *************************************************************************************************
// Set the product readiness status for the item with the given index in the products table to the
// value given in newStatus. For newStatus, use the st.ready constants. This function performs no
// error checking on index and newStatus.
function doSetReadyStatus(index, newStatus)
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('product_id', products[index][c.prd.ID]);
  requestData.append('ready_status', newStatus);
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/product_ready.php', options)
    .then(Utility.extractJson)
    .then(onSetReadyStatus)
    .catch(logSetReadyStatusError);
}

// *************************************************************************************************
// Receive the response to the request to set a product's readiness status. Display an error
// message, or - if the request succeeded - display the list of products.
function onSetReadyStatus(data)
{
  Utility.hideSpinner();

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  if (data && data.resultCode)
  {
    if (Utility.isError(data.resultCode))
    {
      console.error('Error setting ready status: result code: ' + String(data.resultCode));
      errorDisplayed = true;
      alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(data.resultCode), Utility.getTimestamp()]));
    }
    else
    {
      if ((typeof data.productsText !== 'undefined') && (data.productsText !== null) &&
        (data.productsText !== 'null'))
      {
        // Store the updated list of products. This will include the modified product, and have the
        // new value of the ready status flag.
        products = new Function('var productTable = ' + data.productsText + '; return productTable;')();
        // Make the sorting object aware of the new data table, sort it if necessary, and display
        // the new list of products if the sorting did not cause it to be displayed.
        if (!sorting.setDataTable(products))
          displayProducts();
      }
      else
        console.error('Error setting ready status: productsText field missing.');
    }
  }
  else
    console.error('Error setting ready status: data object or result code missing.');
}

// *************************************************************************************************
// Log an error that occurred while setting a product's readiness status.
function logSetReadyStatusError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error setting ready status: ' + error);
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED), Utility.getTimestamp()]));
  }
}

// *************************************************************************************************
// Enabled flag functions.
// *************************************************************************************************
// Return true if the product with the given index in the products table can have its enabled flag
// set.
  // *** // Revisit this, and find which conditions make sense.
function canSetEnabledFlag(index)
{
  index = parseInt(index, 10);
  return Utility.isValidIndex(index, products) && !products[index][c.prd.ENABLED];
}

// *************************************************************************************************
// Return true if the product with the given index in the products table can have its enabled flag
// removed.
  // *** // Revisit this, and find which conditions make sense.
function canClearEnabledFlag(index)
{
  index = parseInt(index, 10);
  return Utility.isValidIndex(index, products) && products[index][c.prd.ENABLED];
}

// *************************************************************************************************
// Display the spinner, then set the enabled flag for the item with the given index in the products
// table.
function setEnabled(index, enabled)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, products))
  {
    Utility.displaySpinnerThen(
        function ()
        {
          doSetEnabled(index, !!enabled);
        }
      );
  }
}

// *************************************************************************************************
// Set the enabled flag for the item with the given index in the products table to the value given
// in enabled, which should be a boolean. This function performs no error checking on index and
// enabled.
function doSetEnabled(index, enabled)
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('product_id', products[index][c.prd.ID]);
  requestData.append('enabled', (enabled ? 'true' : 'false'));
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/product_enabled.php', options)
    .then(Utility.extractJson)
    .then(onSetEnabled)
    .catch(logSetEnabledError);
}

// *************************************************************************************************
// Receive the response to the request to set a product's enabled flag. Display an error message,
// or - if the request succeeded - display the list of products.
function onSetEnabled(data)
{
  Utility.hideSpinner();

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  if (data && data.resultCode)
  {
    if (Utility.isError(data.resultCode))
    {
      console.error('Error setting enabled flag: result code: ' + String(data.resultCode));
      errorDisplayed = true;
      alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(data.resultCode), Utility.getTimestamp()]));
    }
    else
    {
      if ((typeof data.productsText !== 'undefined') && (data.productsText !== null) &&
        (data.productsText !== 'null'))
      {
        // Store the updated list of products. This will include the modified product, and have the
        // new value of the enabled flag.
        products = new Function('var productTable = ' + data.productsText + '; return productTable;')();
        // Make the sorting object aware of the new data table, sort it if necessary, and display
        // the new list of products if the sorting did not cause it to be displayed.
        if (!sorting.setDataTable(products))
          displayProducts();
      }
      else
        console.error('Error setting enabled flag: productsText field missing.');
    }
  }
  else
    console.error('Error setting enabled flag: data object or result code missing.');
}

// *************************************************************************************************
// Log an error that occurred while setting a product's enabled flag.
function logSetEnabledError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error setting enabled flag: ' + error);
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED), Utility.getTimestamp()]));
  }
}

// *************************************************************************************************
// *** Cancel subscription functions.
// *************************************************************************************************
// Return the ID of the ongoing subscription (there should only be one) for the product with the
// given index in the products table, or -1 if none was found.
function getOngoingSubscriptionId(index)
{
  var subscriptions, i;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, products))
    return -1;

  subscriptions = products[index][c.prd.SUBSCRIPTIONS];
  for (i = 0; i < subscriptions.length; i++)
  {
    if (subscriptions[i][c.prs.STATUS] === st.sub.ONGOING)
      return subscriptions[i][c.prs.ID];
  }
  return -1;
}

// *************************************************************************************************
// Display the dialogue box to cancel the ongoing subscription for the product with the given index
// in the products table.
function displayCancelSubscriptionDialogue(index)
{
  var o, p, today, subscriptionId;

  today = Utility.getCurrentIsoDate();
  // Find the ID of the subscription to be cancelled.
  subscriptionId = getOngoingSubscriptionId(index);
  if (subscriptionId < 0)
    return;

  o = new Array(41);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(42, 'Si opp abonnement');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><form id="cancelSubscriptionForm" action="/subscription/html/admin_rental_overview.php" method="post">';
  o[p++] = getPageStateFormElements();
  o[p++] = '<input type="hidden" name="action" value="cancel_subscription" />';
  o[p++] = Utility.getHidden('id', String(subscriptionId));
  // Confirmation caption.
  o[p++] = '<div class="form-element"><p>';
  o[p++] = getText(43, 'Si opp $1 på vegne av kunden?', [products[index][c.prd.NAME]]);
  o[p++] = '</p></div>';
  // Standard cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="normalCancelButton" name="cancel_type" value="0" checked="checked" onChange="switchCancelType();" /> <label for="normalCancelButton">';
  o[p++] = getText(44, 'Vanlig oppsigelse');
  o[p++] = '</label></div>';
  // Standard cancellation message.
  o[p++] = '<div id="standardCancelBox" class="radio-indent-box"><span class="help-text">';
  if (Utility.canCancelThisMonth())
    o[p++] = getText(45, 'Kunden beholder lagerboden til og med siste dag i innev&aelig;rende m&aring;ned.');
  else
    o[p++] = getText(46, 'Kunden trekkes for neste m&aring;ned, og beholder lagerboden til og med siste dag neste m&aring;ned.');
  o[p++] = '</span></div>';
  // Immediate cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="immediateCancelButton" name="cancel_type" value="1" onChange="switchCancelType();" /> <label for="immediateCancelButton">';
  o[p++] = getText(47, 'Si opp umiddelbart');
  o[p++] = '</label></div>';
  // Immediate cancellation message.
  o[p++] = '<div id="immediateCancelBox" class="radio-indent-box" style="display: none;"><div class="custom-cancel-result-box">';
  o[p++] = getImmediateCancelResultText(Utility.getDayBefore(today));
  o[p++] = '</div></div>';
  // Custom cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="customCancelButton" name="cancel_type" value="2" onChange="switchCancelType();" /> <label for="customCancelButton">';
  o[p++] = getText(48, 'Velg sluttdato');
  o[p++] = '</label></div>';
  // Custom cancel box.
  o[p++] = '<div id="customCancelBox" class="radio-indent-box" style="display: none;">';
  // End date.
  o[p++] = '<div><label for="endDateEdit" class="standard-label">';
  o[p++] = getText(49, 'Siste dag:');
  o[p++] = '</label><input type="text" id="endDateEdit" name="end_date" readonly="readonly" value="';
  o[p++] = today;
  o[p++] = '" /><button type="button" id="openCalendarButton" class="icon-button" onclick="openCalendar();"><i class="fa-solid fa-calendar-days"></i></button><button type="button" id="closeCalendarButton" class="icon-button" style="display: none;" onclick="closeCalendar();"><i class="fa-solid fa-xmark"></i></button><div id="calendarBox" class="calendar-box" style="display: none;">&nbsp;</div></div>';
  // Result caption.
  o[p++] = '<div id="customCancelResultBox" class="custom-cancel-result-box">';
  o[p++] = getCustomCancelResultText(today);
  o[p++] = '</div>';
  // End of custom cancel box.
  o[p++] = '</div>';
  // End of content.
  o[p++] = '</form></div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="Utility.displaySpinnerThenSubmit(cancelSubscriptionForm);"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(50, 'Si opp');
  o[p++] = '</button> <button type="button" onclick="closeCancelSubscriptionDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(17, 'Avbryt');
  o[p++] = '</button></div>';

  cancelSubscriptionDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['cancelSubscriptionForm', 'standardCancelBox', 'immediateCancelBox',
    'customCancelBox', 'customCancelResultBox', 'endDateEdit', 'openCalendarButton',
    'closeCalendarButton', 'calendarBox']);

  // Create calendar component.
  calendar = new Calendar(24);
  calendar.dayNames = DAY_NAMES;
  calendar.monthNames = MONTH_NAMES;
  calendar.monthNamesInSentence = MONTH_NAMES_IN_SENTENCE;
  calendar.selectedDate = today;
  calendar.onSelectDate = selectDate;
  calendar.display();

  Utility.display(overlay);
  Utility.display(cancelSubscriptionDialogue);
}

// *************************************************************************************************
// Return a text that explains what happens if a customer's subscription is ended on the given
// endDateIso, which is assumed to be before today's date. The date must be a string in "yyyy-mm-dd"
// format.
  // *** // If today is the first day of the month, the buyer might already have been charged for
         // the coming month. In that case, he will lose all of it. Include a bullet point about that?
function getImmediateCancelResultText(endDateIso)
{
  var o, p, endDate, lastDayOfMonth, lostDayCount;

  endDate = new Date(endDateIso);
  lastDayOfMonth = Utility.getLastDay(endDate);
  o = new Array(8);
  p = 0;

  o[p++] = getText(51, 'Kunden');
  o[p++] = '<ul><li>';
  o[p++] = getText(52, 'Mister tilgang umiddelbart.');
  o[p++] = '</li>';
  if (endDateIso !== lastDayOfMonth)
  {
    o[p++] = '<li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(53, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(54, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li>';
  }
  o[p++] = '</ul>';
  return o.join('');
}

// *************************************************************************************************
// Return a text that explains what happens if a customer's subscription is ended on the given
// endDateIso. The date must be a string in "yyyy-mm-dd" format.
  // *** // The calculation will be wrong if the buyer just booked, and paid for more than the first month.
function getCustomCancelResultText(endDateIso)
{
  var o, p, today, todayIso, endDate, lastDayOfMonth, lostDayCount;

  today = new Date();
  todayIso = Utility.getIsoDate(today);
  lastDayOfMonth = Utility.getLastDay(today);
  endDate = new Date(endDateIso);
  o = new Array(9);
  p = 0;
  o[p++] = getText(51, 'Kunden');
  o[p++] = '<ul>';

  // The end date cannot be before today's date, but catch the case here.
  if (endDateIso <= todayIso)
  {
    // The end date is today.
    o[p++] = '<li>';
    o[p++] = getText(55, 'Beholder lagerboden fram til midnatt.');
    o[p++] = '</li><li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(today, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(53, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(54, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li></ul>';
    return o.join('');
  }

  if (endDateIso === lastDayOfMonth)
  {
    // The end date is the last day of the current month.
    o[p++] = '<li>';
    o[p++] = getText(56, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li></ul>';
    return o.join('');
  }

  if (endDateIso < lastDayOfMonth)
  {
    // The end date is later this month, but before the last day of the month.
    o[p++] = '<li>';
    o[p++] = getText(56, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li><li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(53, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(54, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li></ul>';
    return o.join('');
  }

  lastDayOfMonth = Utility.getLastDay(endDate);
  if (endDateIso === lastDayOfMonth)
  {
    // The end date is the last day of a future month.
    o[p++] = '<li>';
    o[p++] = getText(57, 'Trekkes som vanlig til og med $1 $2.',
      [MONTH_NAMES_IN_SENTENCE[endDate.getMonth()], String(endDate.getFullYear())]);
    o[p++] = '</li><li>';
    o[p++] = getText(56, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li></ul>';
    return o.join('');
  }

  // The end date is any other day of a future month.
  o[p++] = '<li>';
  o[p++] = getText(57, 'Trekkes som vanlig til og med $1 $2.',
    [MONTH_NAMES_IN_SENTENCE[endDate.getMonth()], String(endDate.getFullYear())]);
  o[p++] = '</li><li>';
  o[p++] = getText(56, 'Beholder lagerboden til og med $1.', [endDateIso]);
  o[p++] = '</li><li><span class="warning-text">';
  lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
  if (lostDayCount === 1)
    o[p++] = getText(53, 'Mister &eacute;n dags leie.');
  else
    o[p++] = getText(54, 'Mister $1 dagers leie.', [String(lostDayCount)]);
  o[p++] = '</span></li></ul>';
  return o.join('');
}

// *************************************************************************************************
// Event handler called when the user chooses a different way to cancel a subscription.
function switchCancelType()
{
  var value;

  value = parseInt(Utility.getRadioButtonValue('cancel_type', 0), 10);
  Utility.setDisplayState(standardCancelBox, value === 0);
  Utility.setDisplayState(immediateCancelBox, value === 1);
  Utility.setDisplayState(customCancelBox, value === 2);
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
// Select the given date as the end date of the subscription. selectedDate is a string with a date
// in ISO format - that is, "yyyy-mm-dd".
function selectDate(sender, selectedDate)
{
  endDateEdit.value = selectedDate;
  customCancelResultBox.innerHTML = getCustomCancelResultText(selectedDate);
  closeCalendar();
}

// *************************************************************************************************

function closeCancelSubscriptionDialogue()
{
  Utility.hide(cancelSubscriptionDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Create subscription functions.
// *************************************************************************************************

function displayCreateSubscriptionDialogue(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, products))
    return;

  o = new Array(31);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(27, 'Opprett testabonnement');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="createSubscriptionForm" action="/subscription/html/admin_rental_overview.php" method="post"><div class="form-element"><p class="help-text">';
  o[p++] = getText(28, 'Denne funksjonen er kun for testform&aring;l. Her kan du opprette abonnementer p&aring; vilk&aring;rlige datoer, ogs&aring; tilbake i tid. Dermed kan du umiddelbart teste hvordan systemet virker, uten &aring; m&aring;tte vente p&aring; at tiden g&aring;r. For &aring; opprette et l&oslash;pende abonnement, la "til dato"-feltet være blankt.');
  o[p++] = '</p>';
  o[p++] = getPageStateFormElements();
  o[p++] = '<input type="hidden" name="action" value="create_test_subscription" />';
  o[p++] = Utility.getHidden('product_id', products[index][c.prd.ID]);
  o[p++] = Utility.getHidden('location_id', products[index][c.prd.LOCATION_ID]);
  o[p++] = Utility.getHidden('product_type_id', products[index][c.prd.PRODUCT_TYPE_ID]);
  o[p++] = '</div><div class="form-element"><label for="subscriptionUserId" class="wide-label">';
  o[p++] = getText(32, 'Bruker-ID:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="subscriptionUserId" name="buyer_id" class="short-text" onkeyup="enableSubscriptionSubmitButton();" onchange="enableSubscriptionSubmitButton();" /></div><div class="form-element"><label for="subscriptionUserId" class="wide-label">';
  o[p++] = getText(33, 'Forsikrings-ID:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="insuranceId" name="insurance_id" class="short-text" onkeyup="enableSubscriptionSubmitButton();" onchange="enableSubscriptionSubmitButton();" /></div><div class="form-element"><label for="subscriptionStartDateEdit" class="wide-label">';
  o[p++] = getText(29, 'Fra dato:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="subscriptionStartDateEdit" name="start_date" class="short-text" onkeyup="enableSubscriptionSubmitButton();" onchange="enableSubscriptionSubmitButton();" /> ';
  o[p++] = getText(30, '(yyyy-mm-dd)');
  o[p++] = '</div><div class="form-element"><label for="subscriptionEndDateEdit" class="wide-label">';
  o[p++] = getText(31, 'Til dato:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="subscriptionEndDateEdit" name="end_date" class="short-text" onkeyup="enableSubscriptionSubmitButton();" onchange="enableSubscriptionSubmitButton();" /> ';
  o[p++] = getText(30, '(yyyy-mm-dd)');
  o[p++] = '</div></form></div><div class="dialogue-footer"><button type="button" id="subscriptionSubmitButton" onclick="Utility.displaySpinnerThenSubmit(createSubscriptionForm);"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(20, 'Opprett');
  o[p++] = '</button> <button type="button" onclick="closeSubscriptionDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(17, 'Avbryt');
  o[p++] = '</button></div>';

  createSubscriptionDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['createSubscriptionForm', 'subscriptionStartDateEdit',
    'subscriptionSubmitButton']);

  Utility.display(overlay);
  Utility.display(createSubscriptionDialogue);
  enableSubscriptionSubmitButton();
}

// *************************************************************************************************

function closeSubscriptionDialogue()
{
  Utility.hide(createSubscriptionDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableSubscriptionSubmitButton()
{
  subscriptionSubmitButton.disabled = (subscriptionStartDateEdit.value === '');
}

// *************************************************************************************************
// Product notes functions.
// *************************************************************************************************
// Display a dialogue to read and edit the notes for the product with the given index in the
// products table.
function displayProductNotes(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, products))
    return;

  o = new Array(13);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(59, 'Notater');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><textarea id="productNotesTextArea">';
  o[p++] = products[index][c.prd.NOTES];
  o[p++] = '</textarea></div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="saveProductNotes(';
  o[p++] = String(index);
  o[p++] = ');"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(60, 'Lagre');
  o[p++] = '</button> <button type="button" onclick="closeProductNotesDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(17, 'Avbryt');
  o[p++] = '</button></div></form>';

  productNotesDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['productNotesTextArea']);

  Utility.display(overlay);
  Utility.display(productNotesDialogue);
}

// *************************************************************************************************

function saveProductNotes(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, products))
  {
    Utility.displaySpinnerThen(
        function ()
        {
          doSaveProductNotes(index);
        }
      );
  }
}

// *************************************************************************************************

function doSaveProductNotes(index)
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('action', 'set_product_notes');
  requestData.append('product_id', String(products[index][c.prd.ID]));
  requestData.append('product_notes', Utility.encodeLineBreaks(productNotesTextArea.value));
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/product_notes.php', options)
    .then(Utility.extractJson)
    .then(storeProductNotes)
    .catch(logProductNotesError);
}

// *************************************************************************************************
// Store and display the product notes that were edited, saved, and then returned from the server.
function storeProductNotes(data)
{
  var index;

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  if (data && data.resultCode)
  {
    if (Utility.isError(data.resultCode))
    {
      console.error('Error saving product notes: result code: ' + String(data.resultCode));
      errorDisplayed = true;
      alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(data.resultCode), Utility.getTimestamp()]));
      closeProductNotesDialogue();
    }
    else
    {
      if ((typeof data.productNotes !== 'undefined') && (typeof data.productId !== 'undefined') &&
        (data.productId >= 0))
      {
        // Find the index in the products table of the product with the given productId.
        index = Utility.getProductIndex(data.productId);
        if (index >= 0)
        {
          // Store the updated notes, close the dialogue box and display the list of products.
          products[index][c.prd.NOTES] = Utility.decodeLineBreaks(data.productNotes);
          closeProductNotesDialogue();
          displayProducts();
        }
        else
        {
          console.error('Error saving product notes: product with ID ' + String(data.productId) +
            ' not found.');
          closeProductNotesDialogue();
        }
      }
      else
      {
        console.error('Error saving product notes: productNotes or productId field missing.');
        closeProductNotesDialogue();
      }
    }
  }
  else
  {
    console.error('Error saving product notes: data object or result code missing.');
    closeProductNotesDialogue();
  }
}

// *************************************************************************************************
// Log an error that occurred while saving product notes.
function logProductNotesError(error)
{
  console.error('Error saving product notes: ' + error);
  closeProductNotesDialogue();
}

// *************************************************************************************************

function closeProductNotesDialogue()
{
  Utility.hide(productNotesDialogue);
  Utility.hide(overlay);
  Utility.hideSpinner();
}

// *************************************************************************************************
// Access information functions.
// *************************************************************************************************
// Open a dialogue box to display access information for the product with the given index in the
// products table.
function displayAccessInformation(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, products))
    return;

  o = new Array(15);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(62, 'Adgangsinformasjon');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content">';
  if ((products[index][c.prd.ACCESS_CODE] === '') && (products[index][c.prd.ACCESS_LINK] === ''))
    o[p++] = getText(63, 'L&aring;s ikke aktiv');
  else
  {
    if (products[index][c.prd.ACCESS_CODE] !== '')
    {
      o[p++] = getText(64, 'Adgangskode: ');
      o[p++] = products[index][c.prd.ACCESS_CODE];
    }
    if (products[index][c.prd.ACCESS_LINK] !== '')
    {
      o[p++] = '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.open(\'';
      o[p++] = products[index][c.prd.ACCESS_LINK];
      o[p++] = '\', \'_blank\');"><i class="fa-solid fa-key"></i>&nbsp;&nbsp;';
      o[p++] = getText(65, '&Aring;pne');
      o[p++] = '</button>';
    }
  }
  o[p++] = '</div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="closeAccessInformationDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(66, 'Lukk');
  o[p++] = '</button></div></form>';

  accessInformationDialogue.innerHTML = o.join('');

  Utility.display(overlay);
  Utility.display(accessInformationDialogue);
}

// *************************************************************************************************

function closeAccessInformationDialogue()
{
  Utility.hide(accessInformationDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  filterTabset.display();

  o = new Array(24);
  p = 0;

  // Clear all filters button.
  o[p++] = getText(18, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(19, 'Vis alle');
  o[p++] = '</button>';
  // Location filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (locationFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayLocationFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(2, 'Lager');
  o[p++] = '</button>';
  // Clear location filter button.
  if (locationFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearLocationFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Product type filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (productTypeFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayProductTypeFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(4, 'Bodtype');
  o[p++] = '</button>';
  // Clear product type filter button.
  if (productTypeFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearProductTypeFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Freetext filter edit.
  o[p++] = '<input type="text" id="freetextEdit" placeholder="';
  o[p++] = getText(1, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Display counter box.
  o[p++] = '<span class="counter">';
  if (displayedCount === products.length)
    o[p++] = getText(22, 'Viser $1 lagerboder', [String(products.length)]);
  else
    o[p++] = getText(21, 'Viser $1 av $2 lagerboder',
      [String(displayedCount), String(products.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['freetextEdit']);
}

// *************************************************************************************************
// Return true if the list of products should not include the given product.
function shouldHide(product)
{
  return ((locationFilter !== null) && !locationFilter.includes(product[c.prd.LOCATION_ID])) ||
    ((productTypeFilter !== null) && !productTypeFilter.includes(product[c.prd.PRODUCT_TYPE_ID])) ||
    !matchesStatusFilter(product, statusFilter) ||
    !matchesReadyStatusFilter(product, readyStatusFilter) ||
    !matchesEnabledFilter(product, enabledFilter) ||
    ((freetextFilter !== '') && !matchesFreetextFilter(product));
}

// *************************************************************************************************

function clearAllFilters()
{
  locationFilter = null;
  productTypeFilter = null;
  statusFilter = null;
  freetextFilter = '';
  freetextEdit.value = '';
  displayProducts();
}

// *************************************************************************************************
// Filter tabset functions.
// *************************************************************************************************
// For each of the filter tabset tabs, calculate and store the number of items that match the filter
// preset for that tab.
function setFilterTabsetItemCounts()
{
  var i, tabCount;

  tabCount = filterTabset.tabCount;
  for (i = 0; i < tabCount; i++)
    filterTabset.setItemCount(i, getMatchingItemCount(filterTabset.getFilterPreset(i)));
}

// *************************************************************************************************
// Return the number of products that match the given filterSet. filterSet is an object, and is
// expected to contain the following fields:
//   statusFilter, readyStatusFilter, enabledFilter
// If filterSet is false, there is no number to display, so return false. If all filters in the set
// are null, return the total number of products. Otherwise, return the number of products that
// match all of the filters. This number may be 0.
function getMatchingItemCount(filterSet)
{
  var i, count, product;

  if (filterSet === false)
    return false;
  count = 0;
  for (i = 0; i < products.length; i++)
  {
    product = products[i];
    if (matchesStatusFilter(product, filterSet.statusFilter) &&
      matchesReadyStatusFilter(product, filterSet.readyStatusFilter) &&
      matchesEnabledFilter(product, filterSet.enabledFilter))
      count++;
  }
  return count;
}

// *************************************************************************************************
// Return true if the given product matches the given filter, which is assumed to be a product
// status filter - although not necessarily the current one. If there is no filter (filter is null),
// the product is considered to match, and the function returns true.
function matchesStatusFilter(product, filter)
{
  return (filter === null) || filter.includes(product[c.prd.STATUS]);
}

// *************************************************************************************************
// Return true if the given product matches the given filter, which is assumed to be a ready status
// filter - although not necessarily the current one. If there is no filter (filter is null), the
// product is considered to match, and the function returns true.
function matchesReadyStatusFilter(product, filter)
{
  return (filter === null) || filter.includes(product[c.prd.READY_STATUS]);
}

// *************************************************************************************************
// Return true if the given product matches the given filter, which is assumed to be an enabled
// filter - although not necessarily the current one. If there is no filter (filter is null), the
// product is considered to match, and the function returns true.
function matchesEnabledFilter(product, filter)
{
  var enabled;

  enabled = (product[c.prd.ENABLED] ? 1 : 0);
  return (filter === null) || filter.includes(enabled);
}

// *************************************************************************************************
// Handle a change to the filter tabset tab. Set the product status filter, ready status filter and
// enabled filter to the preset for the selected tab, and display the corresponding products.
function changeFilterTabsetTab(sender, activeTabIndex)
{
  var filterSet;
  
  filterSet = sender.getFilterPreset(activeTabIndex);
  if (filterSet !== false)
  {
    statusFilter = filterSet.statusFilter;
    readyStatusFilter = filterSet.readyStatusFilter;
    enabledFilter = filterSet.enabledFilter;
    displayProducts();
  }
}

// *************************************************************************************************
// Return true if the list of products is currently filtered on product status, and the filter
// includes the given status. 
function inStatusFilter(status)
{
  return (statusFilter !== null) && statusFilter.includes(status);
}

// *************************************************************************************************
// Return true if the list of products is currently filtered on ready status, and the filter
// includes the given readyStatus. 
function inReadyStatusFilter(readyStatus)
{
  return (readyStatusFilter !== null) && readyStatusFilter.includes(readyStatus);
}

// *************************************************************************************************
// Return true if the list of products is currently filtered on enabled or disabled products, and
// the filter includes the given enabledValue. 
function inEnabledFilter(enabledValue)
{
  return (enabledFilter !== null) && enabledFilter.includes(enabledValue);
}

// *************************************************************************************************

function displayStatusFilterDialogue()
{
  var o, p, i;
  
  o = new Array((st.prod.TEXTS.length * 16) + (st.ready.TEXTS.length * 10) +
    (st.enabled.TEXTS.length * 10) + 22);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(38, 'Konfigurer visning');

  // Product status.
  o[p++] = '</h1></div><div class="dialogue-content"><h3>';
  o[p++] = getText(25, 'Velg hvilke utleiestatuser som skal vises');
  o[p++] = '</h3><form action="#">';
  for (i = 0; i < st.prod.TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inStatusFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/>&nbsp;&nbsp;&nbsp;';
    o[p++] = '<label for="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox"> <img src="/subscription/resources/status_';
    o[p++] = String(i);
    o[p++] = '.png?v=';
    o[p++] = String(BUILD_NO);
    o[p++] = '" alt="';
    o[p++] = st.prod.TEXTS_BRIEF[i];
    o[p++] = '" class="status-image" /> ';
    o[p++] = st.prod.TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '<div class="form-element"><span class="help-text">';
  o[p++] = getText(26, 'Pilene viser leieforhold, og l&oslash;per langs tidslinjen. Den r&oslash;de firkanten er dagens dato.');
  o[p++] = '</span></div><div class="form-element"><button type="button" onclick="setAllProductStatusesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(12, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllProductStatusesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(13, 'Ingen');
  o[p++] = '</button></div><h3>';

  // Ready status.
  o[p++] = getText(39, 'Velg om boder som m&aring; sjekkes skal vises');
  o[p++] = '</h3>';
  for (i = 0; i < st.ready.TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="readyStatus';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inReadyStatusFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/>&nbsp;&nbsp;&nbsp;';
    o[p++] = '<label for="readyStatus';
    o[p++] = String(i);
    o[p++] = 'Checkbox"> ';
    o[p++] = Utility.getStatusLabel(st.ready.TEXTS, st.ready.COLOURS, i, st.ready.ICONS);
    o[p++] = '</label></div>';
  }

  // Enabled or disabled.
  o[p++] = '<h3>';
  o[p++] = getText(58, 'Velg om aktive eller inaktive boder skal vises');
  o[p++] = '</h3>';
  for (i = 0; i < st.enabled.TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="enabled';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inEnabledFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/>&nbsp;&nbsp;&nbsp;';
    o[p++] = '<label for="enabled';
    o[p++] = String(i);
    o[p++] = 'Checkbox"> ';
    o[p++] = Utility.getStatusLabel(st.enabled.TEXTS, st.enabled.COLOURS, i, st.enabled.ICONS);
    o[p++] = '</label></div>';
  }

  // Footer.
  o[p++] = '</h3></form></div><div class="dialogue-footer"><button type="button" onclick="updateTabsetFilters();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(16, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeTabsetFiltersDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(17, 'Avbryt');
  o[p++] = '</button></div>';

  editTabsetFiltersDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editTabsetFiltersDialogue);
}

// *************************************************************************************************
// Check or uncheck all the status checkboxes in the product status filter section of the edit
// tabset filters dialogue, depending on checked, which should be a boolean.
function setAllProductStatusesTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < st.prod.TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************
// Update the tabset filters based on the contents of the edit tabset filters dialogue.
function updateTabsetFilters()
{
  updateStatusFilter();
  updateReadyStatusFilter();
  updateEnabledFilter();

  closeTabsetFiltersDialogue();
  filterTabset.setActiveTabFromFilter(
    {
      statusFilter: statusFilter,
      readyStatusFilter: readyStatusFilter,
      enabledFilter: enabledFilter
    });
  displayProducts();
}

// *************************************************************************************************
// Update the product status filter based on the contents of the edit tabset filters dialogue.
function updateStatusFilter()
{
  var i, checkbox;

  statusFilter = [];
  for (i = 0; i < st.prod.TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      statusFilter.push(i);
  }
  // If the user unchecks all statuses, instead of displaying nothing, clear the filter. If the
  // user checks all statuses, also clear the filter.
  if ((statusFilter.length === 0) || (statusFilter.length === st.prod.TEXTS.length))
    statusFilter = null;
}

// *************************************************************************************************
// Update the ready status filter based on the contents of the edit tabset filters dialogue.
function updateReadyStatusFilter()
{
  var i, checkbox;

  readyStatusFilter = [];
  for (i = 0; i < st.ready.TEXTS.length; i++)
  {
    checkbox = document.getElementById('readyStatus' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      readyStatusFilter.push(i);
  }
  // If the user unchecks all statuses, instead of displaying nothing, clear the filter. If the
  // user checks all statuses, also clear the filter.
  if ((readyStatusFilter.length === 0) || (readyStatusFilter.length === st.ready.TEXTS.length))
    readyStatusFilter = null;
}

// *************************************************************************************************
// Update the enabled filter based on the contents of the edit tabset filters dialogue.
function updateEnabledFilter()
{
  var i, checkbox;

  enabledFilter = [];
  for (i = 0; i < st.enabled.TEXTS.length; i++)
  {
    checkbox = document.getElementById('enabled' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      enabledFilter.push(i);
  }
  // If the user unchecks all statuses, instead of displaying nothing, clear the filter. If the
  // user checks all statuses, also clear the filter.
  if ((enabledFilter.length === 0) || (enabledFilter.length === st.enabled.TEXTS.length))
    enabledFilter = null;
}

// *************************************************************************************************

function closeTabsetFiltersDialogue()
{
  Utility.hide(editTabsetFiltersDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Location filter functions.
// *************************************************************************************************
// Return true if the list of products is currently filtered on locations, and the filter includes
// the given location ID. 
function inLocationFilter(locationId)
{
  return (locationFilter !== null) && locationFilter.includes(locationId);
}

// *************************************************************************************************

function displayLocationFilterDialogue()
{
  var o, p, i;
  
  o = new Array((locations.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(23, 'Velg hvilke lager som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="location';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inLocationFilter(locations[i][c.loc.ID]))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> ';
    o[p++] = '<label for="location';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllLocationsTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(12, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(13, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateLocationFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(16, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeLocationFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(17, 'Avbryt');
  o[p++] = '</button></div>';

  editLocationFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editLocationFilterDialogue);
};

// *************************************************************************************************
// Check or uncheck all the location checkboxes in the location filter dialogue, depending on
// checked, which should be a boolean.
function setAllLocationsTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < locations.length; i++)
  {
    checkbox = document.getElementById('location' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function clearLocationFilter()
{
  locationFilter = null;
  displayProducts();
}

// *************************************************************************************************

function updateLocationFilter()
{
  var i, checkbox;

  locationFilter = [];
  for (i = 0; i < locations.length; i++)
  {
    checkbox = document.getElementById('location' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      locationFilter.push(locations[i][c.loc.ID]);
  }
  // If the user unchecks all locations, instead of displaying nothing, clear the filter. If the
  // user checks all locations, also clear the filter.
  if ((locationFilter.length === 0) || (locationFilter.length === locations.length))
    locationFilter = null;
  closeLocationFilterDialogue();
  displayProducts();
}

// *************************************************************************************************

function closeLocationFilterDialogue()
{
  Utility.hide(editLocationFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Product type filter functions.
// *************************************************************************************************
// Return true if the list of products is currently filtered on product types, and the filter
// includes the given product type ID. 
function inProductTypeFilter(productTypeId)
{
  return (productTypeFilter !== null) && productTypeFilter.includes(productTypeId);
}

// *************************************************************************************************

function displayProductTypeFilterDialogue()
{
  var o, p, i;
  
  o = new Array((productTypes.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(24, 'Velg hvilke bodtyper som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < productTypes.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="productType';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inProductTypeFilter(productTypes[i][c.typ.ID]))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> ';
    o[p++] = '<label for="productType';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = productTypes[i][c.typ.NAME];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllProductTypesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(12, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllProductTypesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(13, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateProductTypeFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(16, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeProductTypeFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(17, 'Avbryt');
  o[p++] = '</button></div>';

  editProductTypeFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editProductTypeFilterDialogue);
}

// *************************************************************************************************
// Check or uncheck all the product type checkboxes in the product type filter dialogue, depending
// on checked, which should be a boolean.
function setAllProductTypesTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < productTypes.length; i++)
  {
    checkbox = document.getElementById('productType' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function clearProductTypeFilter()
{
  productTypeFilter = null;
  displayProducts();
}

// *************************************************************************************************

function updateProductTypeFilter()
{
  var i, checkbox;

  productTypeFilter = [];
  for (i = 0; i < productTypes.length; i++)
  {
    checkbox = document.getElementById('productType' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      productTypeFilter.push(productTypes[i][c.typ.ID]);
  }
  // If the user unchecks all product types, instead of displaying nothing, clear the filter. If the
  // user checks all product types, also clear the filter.
  if ((productTypeFilter.length === 0) || (productTypeFilter.length === productTypes.length))
    productTypeFilter = null;
  closeProductTypeFilterDialogue();
  displayProducts();
}

// *************************************************************************************************

function closeProductTypeFilterDialogue()
{
  Utility.hide(editProductTypeFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Freetext filter functions.
// *************************************************************************************************
// Monitor key presses in the freetext edit box. If <enter> is pressed, update the filter.
function freetextEditKeyDown(event)
{
  if (event.key === 'Enter')
    updateFreetextFilter();
}

// *************************************************************************************************

function updateFreetextFilter()
{
  freetextFilter = freetextEdit.value;
  displayProducts();
}

// *************************************************************************************************
// Return true if the given product matches the current freetext filter.
function matchesFreetextFilter(product)
{
  var filter;

  filter = freetextFilter.toLowerCase();
  // If there is no filter (or no product), everything matches. Otherwise, return a match if the
  // location name, product name, product type name or product notes fields contain the filter text.
  return (product === null) || (filter === '') ||
    (Utility.getLocationName(product[c.prd.LOCATION_ID]).toLowerCase().indexOf(filter) >= 0) ||
    (product[c.prd.NAME].toLowerCase().indexOf(filter) >= 0) ||
    (Utility.getProductTypeName(product[c.prd.PRODUCT_TYPE_ID]).toLowerCase().indexOf(filter) >= 0) ||
    (product[c.prd.NOTES].toLowerCase().indexOf(filter) >= 0);
}

// *************************************************************************************************
