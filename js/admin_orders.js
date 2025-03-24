// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// The filter settings for the various tabs in the filter tabset toolbar. Note that each array must
// be sorted in increasing order.
var FILTER_TABSET_PRESETS =
  [
    null,
    [PAYMENT_STATUS_NOT_PAID_NO_INVOICE_SENT],
    [PAYMENT_STATUS_NOT_PAID_INVOICE_SENT],
    [
      PAYMENT_STATUS_NOT_PAID,
      PAYMENT_STATUS_NOT_PAID_OVERDUE,
      PAYMENT_STATUS_NOT_PAID_REMINDER_SENT,
      PAYMENT_STATUS_NOT_PAID_WARNING_SENT,
      PAYMENT_STATUS_NOT_PAID_SENT_TO_COLLECTION,
      PAYMENT_STATUS_FAILED_AT_PROVIDER,
      PAYMENT_STATUS_ERROR
    ],
    [PAYMENT_STATUS_PAID]
  ];

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var filterToolbar, ordersBox, overlay, editStatusDialogue, editLocationFilterDialogue,
  editMonthFilterDialogue, editMethodFilterDialogue, editStatusFilterDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var statusForm;

// Filter tabset which filters on payment status.
var filterTabset = null;

// The sorting object that controls the sorting of the orders table.
var sorting;

// The popup menu for the orders table.
var menu;

// The number of displayed orders. This depends on the current filter settings.
var displayedCount = 0;

// The list of months with orders. Array of strings, where each string is a month in the format
// "yyyy-mm". A month will appear in the list if there is at least one order in the orders table
// that has that month as its "period_month" data item.
var monthsWithOrders = [];

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['filterToolbar', 'ordersBox', 'overlay', 'editStatusDialogue',
    'editLocationFilterDialogue', 'editMonthFilterDialogue', 'editMethodFilterDialogue',
    'editStatusFilterDialogue']);

  listMonthsWithOrders();

  // Create the filter tabset.
  filterTabset = new FilterTabset(FILTER_TABSET_TEXTS, FILTER_TABSET_PRESETS);
  setFilterTabsetItemCounts();
  filterTabset.setActiveTabFromFilter(statusFilter);
  filterTabset.onChangeTab = changeFilterTabsetTab;
  filterTabset.onConfigure = displayStatusFilterDialogue;

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(orders,
      [
        Sorting.createUiColumn(c.ord.ID, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.ord.AMOUNT, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.ord.LOCATION_ID, Sorting.SORT_AS_STRING,
          function (order)
          {
            return Utility.getLocationName(order[c.ord.LOCATION_ID]) + ' ' +
              order[c.ord.PRODUCT_NAME];
          }),
        Sorting.createUiColumn(c.ord.PRODUCT_NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.ord.CREATED, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.ord.DATA, Sorting.SORT_AS_STRING,
          function (order)
          {
            return String(getOrderData('period_start', order)) + ' ' +
              String(getOrderData('period_end', order));
          }),
        Sorting.createUiColumn(c.ord.DATA, Sorting.SORT_AS_STRING,
          function (order)
          {
            return PAYMENT_METHOD_TEXTS[getPaymentMethod(order)];
          }),
        Sorting.createUiColumn(c.ord.DATA, Sorting.SORT_AS_STRING,
          function (order)
          {
            return PAYMENT_STATUS_TEXTS[getPaymentStatus(order)];
          }),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayOrders
    );
  doDisplayOrders();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert(getText(0,
      'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Update the monthsWithOrders array to hold all the months for which orders exist in the orders
// table. This function checks the "period_month" data field.
function listMonthsWithOrders()
{
  var i, periodMonth;

  monthsWithOrders = [];
  for (i = 0; i < orders.length; i++)
  {
    periodMonth = getOrderData('period_month', orders[i], null);
    if (periodMonth !== null)
    {
      if (!monthsWithOrders.includes(periodMonth))
        monthsWithOrders.push(periodMonth);
    }
  }
}

// *************************************************************************************************
// Return hidden form elements that specify the current state of the page, including sorting, search
// and filter settings. These should be included whenever a request is submitted to the current
// page, so that the state is maintained when the page is reloaded.
function getPageStateFormElements()
{
  var o, p;

  o = new Array(4);
  p = 0;

  if (locationFilter !== null)
    o[p++] = Utility.getHidden('location_filter', locationFilter.join(','));
  if (monthFilter !== null)
    o[p++] = Utility.getHidden('month_filter', monthFilter.join(','));
  if (methodFilter !== null)
    o[p++] = Utility.getHidden('method_filter', methodFilter.join(','));
  // The default value is not null, so if we don't have a filter, send a value to say so.
  if (statusFilter === null)
    o[p++] = Utility.getHidden('status_filter', 'null');
  else
    o[p++] = Utility.getHidden('status_filter', statusFilter.join(','));
  o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// Display the spinner. Once visible, display orders.
function displayOrders()
{
  Utility.displaySpinnerThen(doDisplayOrders);
}

// *************************************************************************************************
// Display the list of orders.
function doDisplayOrders()
{
  var o, p, i;
  
  if (orders.length <= 0)
  {
    ordersBox.innerHTML = '<div class="form-element">' +
      getText(1, 'Det er ikke opprettet noen ordre enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((orders.length * 22) + 12);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(2, 'Ordre-ID'));
  o[p++] = sorting.getTableHeader(1, getText(27, 'Bel&oslash;p'));
  o[p++] = sorting.getTableHeader(2, getText(26, 'Lager'));
  o[p++] = sorting.getTableHeader(3, getText(25, 'Lagerbod'));
  o[p++] = sorting.getTableHeader(4, getText(5, 'Opprettet'));
  o[p++] = sorting.getTableHeader(5, getText(6, 'Periode'));
  o[p++] = sorting.getTableHeader(6, getText(8, 'Betalingsm&aring;te'));
  o[p++] = sorting.getTableHeader(7, getText(3, 'Betalingsstatus'));
  o[p++] = sorting.getTableHeader(8, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < orders.length; i++)
  {
    if (shouldHide(orders[i]))
      continue;
    displayedCount++;

    // Order ID.
    o[p++] = '<tr><td>';
    o[p++] = String(orders[i][c.ord.ID]);
    o[p++] = '</td><td>';
    // Amount
    o[p++] = String(orders[i][c.ord.AMOUNT]);
    o[p++] = getText(28, ' kr');
    o[p++] = '</td><td>';
    // Location name.
    o[p++] = Utility.getLocationName(orders[i][c.ord.LOCATION_ID]);
    o[p++] = '</td><td>';
    // Product name.
    o[p++] = orders[i][c.ord.PRODUCT_NAME];
    o[p++] = '</td><td>';
    // Created date.
    o[p++] = String(orders[i][c.ord.CREATED]);
    o[p++] = '</td><td>';
    // Time period.
    o[p++] = String(getOrderData('period_start', orders[i]));
    o[p++] = ' - ';
    o[p++] = String(getOrderData('period_end', orders[i]));
    o[p++] = '</td><td>';
    // Payment method.
    o[p++] = PAYMENT_METHOD_TEXTS[getPaymentMethod(orders[i])];
    o[p++] = '</td><td>';
    // Payment status.
    o[p++] = Utility.getStatusLabel(PAYMENT_STATUS_TEXTS, PAYMENT_STATUS_COLOURS,
      getPaymentStatus(orders[i]));
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  ordersBox.innerHTML = o.join('');
  displayFilterToolbar();
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, orders))
    return '';
  o = new Array(2);
  p = 0;

  // Edit status button.
  o[p++] = sender.getMenuItem(getText(4, 'Endre status'), 'fa-pen-to-square', true,
    'displayStatusDialogue(' + String(index) + ');');
  // Display customer button.
  o[p++] = sender.getMenuItem(getText(7, 'Vis kundekort'), 'fa-user', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/admin_edit_user.php?user_id=' +
    String(orders[index][c.ord.USER_ID]) + '\');');
  return o.join('');
}

// *************************************************************************************************
// Return the payment method for the given order. Use the PAYMENT_METHOD_ constants. Return
// PAYMENT_METHOD_UNKNOWN if not found.
function getPaymentMethod(order)
{
  var paymentMethod;
  
  paymentMethod = getOrderData('payment_method', order);
  if (paymentMethod !== '')
  {
    paymentMethod = parseInt(paymentMethod, 10);
    if (Utility.isValidPaymentMethod(paymentMethod))
      return paymentMethod;
  }
  return PAYMENT_METHOD_UNKNOWN;
}

// *************************************************************************************************
// Return the payment status for the given order. Use the PAYMENT_STATUS_ constants. Return
// PAYMENT_STATUS_UNKNOWN if not found.
function getPaymentStatus(order)
{
  var paymentStatus;

  paymentStatus = getOrderData('payment_status', order);
  if (paymentStatus === '')
    return PAYMENT_STATUS_UNKNOWN;
  paymentStatus = parseInt(paymentStatus, 10);
  if (!isFinite(paymentStatus))
    return PAYMENT_STATUS_UNKNOWN;
  return paymentStatus;
}

// *************************************************************************************************
// From the given order, return the metadata with the given key, or the given defaultValue if it was
// not found. defaultValue is optional. The, uh, default value is an empty string.
function getOrderData(key, order, defaultValue)
{
  var i, data;

  data = order[c.ord.DATA];
  for (i = 0; i < data.length; i++)
  {
    if (data[i][c.ord.KEY] === key)
      return data[i][c.ord.VALUE];
  }
  if (typeof defaultValue !== 'undefined')
    return defaultValue;
  return '';
}

// *************************************************************************************************
// *** Set status functions.
// *************************************************************************************************
// Display the dialogue box to allow the user to edit the status of the order with the given index
// in the orders table.
function displayStatusDialogue(index)
{
  var o, p, i, currentStatus;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, orders))
    return;

  currentStatus = getPaymentStatus(orders[index]);
  o = new Array((PAYMENT_STATUS_TEXTS.length * 7) + 17);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(23, 'Endre status');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><form id="statusForm" action="/subscription/html/admin_orders.php" method="post"><div class="form-element">';
  o[p++] = getPageStateFormElements();
  o[p++] = '<input type="hidden" name="action" value="set_payment_status" />';
  o[p++] = Utility.getHidden('id', orders[index][c.ord.ID]);
  // Status combo.
  o[p++] = '<label for="statusCombo" class="standard-label">';
  o[p++] = getText(24, 'Ny status:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <select id="statusCombo" name="payment_status" class="long-text">';
  for (i = 0; i < PAYMENT_STATUS_TEXTS.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = String(i);
    o[p++] = '"';
    if (currentStatus === i)
      o[p++] = ' selected="selected"'
    o[p++] = '>';
    o[p++] = PAYMENT_STATUS_TEXTS[i];
    o[p++] = '</option>';
  }
  o[p++] = '</select></div></form></div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="Utility.displaySpinnerThenSubmit(statusForm);"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeStatusDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editStatusDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['statusForm']);

  Utility.display(overlay);
  Utility.display(editStatusDialogue);
}

// *************************************************************************************************

function closeStatusDialogue()
{
  Utility.hide(editStatusDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  filterTabset.display();

  o = new Array(25);
  p = 0;

  // Clear all button.
  o[p++] = getText(11, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(12, 'Vis alle');
  o[p++] = '</button>';
  // Location filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (locationFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayLocationFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(26, 'Lager');
  o[p++] = '</button>';
  // Clear location filter button.
  if (locationFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearLocationFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Month filter.
  o[p++] = '<button type="button" class="filter-button';
  if (monthFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayMonthFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(13, 'M&aring;ned');
  o[p++] = '</button>';
  if (monthFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearMonthFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Payment method filter.
  o[p++] = '<button type="button" class="filter-button';
  if (methodFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayMethodFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(21, 'Betalingsm&aring;te');
  o[p++] = '</button>';
  if (methodFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearMethodFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Number of displayed orders.
  o[p++] = '<span class="counter">';
  if (displayedCount === orders.length)
    o[p++] = getText(10, 'Viser $1 ordre', [String(orders.length)]);
  else
    o[p++] = getText(9, 'Viser $1 av $2 ordre', [String(displayedCount), String(orders.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');
}

// *************************************************************************************************
// Return true if the list of orders should not include the given order. If the order has no
// "period_month" value (which should not happen), it will be displayed.
function shouldHide(order)
{
  var periodMonth, paymentMethod, paymentStatus;

  periodMonth = getOrderData('period_month', order, null);
  paymentMethod = getPaymentMethod(order);
  paymentStatus = getPaymentStatus(order);
  return ((locationFilter !== null) && !locationFilter.includes(order[c.ord.LOCATION_ID])) ||
    ((monthFilter !== null) && (periodMonth !== null) && !monthFilter.includes(periodMonth)) ||
    ((methodFilter !== null) && !methodFilter.includes(paymentMethod)) ||
    ((statusFilter !== null) && !statusFilter.includes(paymentStatus));
}

// *************************************************************************************************

function clearAllFilters()
{
  locationFilter = null;
  monthFilter = null;
  methodFilter = null;
  statusFilter = null;
  displayOrders();
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
// Return the number of orders that match the given payment status filter. If filter is false, there
// is no number to display, so return false. If filter is null, return the total number of orders.
// If it is an array of payment statuses, return the number of matching orders. Otherwise, return 0.
function getMatchingItemCount(filter)
{
  var i, count;

  if (filter === false)
    return false;
  if (filter === null)
    return orders.length;
  if (Array.isArray(filter))
  {
    count = 0;
    for (i = 0; i < orders.length; i++)
    {
      if (filter.includes(getPaymentStatus(orders[i])))
        count++;
    }
    return count;
  }
  return 0;
}

// *************************************************************************************************
// Handle a change to the filter tabset tab. Set the payment status filter to the preset for the
// selected tab, and display the corresponding orders.
function changeFilterTabsetTab(sender, activeTabIndex)
{
  var filter;
  
  filter = sender.getFilterPreset(activeTabIndex);
  if (filter !== false)
  {
    statusFilter = filter;
    displayOrders();
  }
}

// *************************************************************************************************
// Return true if the list of orders is currently filtered on status, and the filter includes the
// given status.
function inStatusFilter(status)
{
  return (statusFilter !== null) && statusFilter.includes(status);
}

// *************************************************************************************************

function displayStatusFilterDialogue()
{
  var o, p, i;
  
  o = new Array((PAYMENT_STATUS_TEXTS.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(20, 'Velg hvilke betalingsstatuser som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < PAYMENT_STATUS_TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inStatusFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> ';
    o[p++] = '<label for="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = PAYMENT_STATUS_TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllStatusesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(15, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllStatusesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(16, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateStatusFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeStatusFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editStatusFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editStatusFilterDialogue);
}

// *************************************************************************************************
// Check or uncheck all the status checkboxes in the status filter dialogue, depending on checked,
// which should be a boolean.
function setAllStatusesTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < PAYMENT_STATUS_TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function updateStatusFilter()
{
  var i, checkbox;

  // Update the status filter. Note that the filter will be sorted in ascending order when
  // constructed.
  statusFilter = [];
  for (i = 0; i < PAYMENT_STATUS_TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      statusFilter.push(i);
  }
  // If the user unchecks all statuses, instead of displaying nothing, clear the filter. If the user
  // checks all statuses, also clear the filter.
  if ((statusFilter.length === 0) || (statusFilter.length === PAYMENT_STATUS_TEXTS.length))
    statusFilter = null;
  closeStatusFilterDialogue();
  filterTabset.setActiveTabFromFilter(statusFilter);
  displayOrders();
}

// *************************************************************************************************

function closeStatusFilterDialogue()
{
  Utility.hide(editStatusFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Location filter functions.
// *************************************************************************************************
// Return true if the list of orders is currently filtered on locations, and the filter includes
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
  o[p++] = getText(29, 'Velg hvilke lager som skal vises');
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
  o[p++] = getText(15, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(16, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateLocationFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeLocationFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
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
  displayOrders();
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
  displayOrders();
}

// *************************************************************************************************

function closeLocationFilterDialogue()
{
  Utility.hide(editLocationFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Month filter functions.
// *************************************************************************************************
// Return true if the list of orders is currently filtered on months, and the filter includes the
// given month, in the format "yyyy-mm". 
function inMonthFilter(month)
{
  return (monthFilter !== null) && monthFilter.includes(month);
}

// *************************************************************************************************

function displayMonthFilterDialogue()
{
  var o, p, i, id;
  
  o = new Array((monthsWithOrders.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(14, 'Velg hvilke m&aring;neder som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < monthsWithOrders.length; i++)
  {
    id = getMonthCheckboxId(monthsWithOrders[i]);
    o[p++] = '<div class="form-element"><input type="checkbox" id="';
    o[p++] = id;
    o[p++] = '" ';
    if (inMonthFilter(monthsWithOrders[i]))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> ';
    o[p++] = '<label for="';
    o[p++] = id;
    o[p++] = '">';
    o[p++] = monthsWithOrders[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllMonthsTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(15, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllMonthsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(16, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateMonthFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeMonthFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editMonthFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editMonthFilterDialogue);
}

// *************************************************************************************************
// Based on the given month, in the format "yyyy-mm", return an ID for the corresponding filter
// checkbox.
function getMonthCheckboxId(month)
{
  return String(month).replace('-', '_') + 'Checkbox';
}

// *************************************************************************************************
// Check or uncheck all the month checkboxes in the month filter dialogue, depending on checked,
// which should be a boolean.
function setAllMonthsTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < monthsWithOrders.length; i++)
  {
    checkbox = document.getElementById(getMonthCheckboxId(monthsWithOrders[i]));
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function clearMonthFilter()
{
  monthFilter = null;
  displayOrders();
}

// *************************************************************************************************

function updateMonthFilter()
{
  var i, checkbox;

  monthFilter = [];
  for (i = 0; i < monthsWithOrders.length; i++)
  {
    checkbox = document.getElementById(getMonthCheckboxId(monthsWithOrders[i]));
    if (checkbox && checkbox.checked)
      monthFilter.push(monthsWithOrders[i]);
  }
  // If the user unchecks all months, instead of displaying nothing, clear the filter. If the user
  // checks all months, also clear the filter.
  if ((monthFilter.length === 0) || (monthFilter.length === monthsWithOrders.length))
    monthFilter = null;
  closeMonthFilterDialogue();
  displayOrders();
}

// *************************************************************************************************

function closeMonthFilterDialogue()
{
  Utility.hide(editMonthFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Payment method filter functions.
// *************************************************************************************************
// Return true if the list of orders is currently filtered on payment method, and the filter
// includes the given paymentMethod.
function inMethodFilter(paymentMethod)
{
  return (methodFilter !== null) && methodFilter.includes(paymentMethod);
}

// *************************************************************************************************

function displayMethodFilterDialogue()
{
  var o, p, i;
  
  o = new Array((PAYMENT_METHOD_TEXTS.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(22, 'Velg hvilke betalingsm&aring;ter som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < PAYMENT_METHOD_TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="method';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inMethodFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> ';
    o[p++] = '<label for="method';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = PAYMENT_METHOD_TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllMethodsTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(15, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllMethodsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(16, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateMethodFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeMethodFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editMethodFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editMethodFilterDialogue);
}

// *************************************************************************************************

function setAllMethodsTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < PAYMENT_METHOD_TEXTS.length; i++)
  {
    checkbox = document.getElementById('method' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function clearMethodFilter()
{
  methodFilter = null;
  displayOrders();
}

// *************************************************************************************************

function updateMethodFilter()
{
  var i, checkbox;

  methodFilter = [];
  for (i = 0; i < PAYMENT_METHOD_TEXTS.length; i++)
  {
    checkbox = document.getElementById('method' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      methodFilter.push(i);
  }
  // If the user unchecks all payment methods, instead of displaying nothing, clear the filter. If
  // the user checks all payment methods, also clear the filter.
  if ((methodFilter.length === 0) || (methodFilter.length === PAYMENT_METHOD_TEXTS.length))
    methodFilter = null;
  closeMethodFilterDialogue();
  displayOrders();
}

// *************************************************************************************************

function closeMethodFilterDialogue()
{
  Utility.hide(editMethodFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
