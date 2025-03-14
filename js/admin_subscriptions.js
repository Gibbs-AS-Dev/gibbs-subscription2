// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var subscriptionsBox, filterToolbar, overlay, pricePlanDialogue, paymentHistoryDialogue,
  cancelSubscriptionDialogue, editPricePlanDialogue, editLocationFilterDialogue,
  editProductTypeFilterDialogue, editStatusFilterDialogue;

var cancelSubscriptionForm, standardCancelBox, immediateCancelBox, customCancelBox,
  customCancelResultBox, endDateEdit, openCalendarButton, closeCalendarButton, calendarBox,
  freetextEdit;

// The sorting object that controls the sorting of the subscriptions table.
var sorting;

// The popup menu for the subscriptions table.
var menu;

// The number of displayed subscriptions. This depends on the current filter settings.
var displayedCount = 0;

// Array of price plan lines currently being edited in the edit price plan dialogue box, or null if
// the dialogue box is not open. The array has the same format as the price plan lines in the
// subscriptions table. Use the c.sua.LINE_ column constants to index them.
var editedPricePlanLines = null;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['subscriptionsBox', 'filterToolbar', 'overlay', 'pricePlanDialogue',
    'paymentHistoryDialogue', 'cancelSubscriptionDialogue', 'editPricePlanDialogue',
    'editLocationFilterDialogue', 'editProductTypeFilterDialogue', 'editStatusFilterDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents, 300);

  // Initialise sorting.
  sorting = new Sorting(subscriptions,
      [
        Sorting.createUiColumn(c.sua.BUYER_ID, Sorting.SORT_AS_STRING,
          function (subscription)
          {
            var user;

            user = getUser(subscription[c.sua.BUYER_ID]);
            if (user === null)
              return '';
            return user[c.rqu.NAME];
          }),
        Sorting.createUiColumn(c.sua.LOCATION_ID, Sorting.SORT_AS_STRING,
          function (subscription)
          {
            return Utility.getLocationName(subscription[c.sua.LOCATION_ID]) + ' ' +
              subscription[c.sua.PRODUCT_NAME];
          }),
        Sorting.createUiColumn(c.sua.PRODUCT_NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.sua.PRODUCT_TYPE_ID, Sorting.SORT_AS_STRING,
          function (subscription)
          {
            return Utility.getProductTypeName(subscription[c.sua.PRODUCT_TYPE_ID]);
          }),
        Sorting.createUiColumn(c.sua.STATUS, Sorting.SORT_AS_STRING,
          function (subscription)
          {
            return st.sub.TEXTS[subscription[c.sua.STATUS]];
          }),
        Sorting.createUiColumn(c.sua.START_DATE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.sua.END_DATE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.sua.INSURANCE_NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplaySubscriptions
    );
  // Set the initial sorting. If that didn't cause subscriptions to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplaySubscriptions();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
  {
    alert(getText(32, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
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
  if (productTypeFilter !== null)
    o[p++] = Utility.getHidden('product_type_filter', productTypeFilter.join(','));
  if (statusFilter !== null)
    o[p++] = Utility.getHidden('status_filter', statusFilter.join(','));
  if (freetextFilter !== '')
    o[p++] = Utility.getHidden('freetext_filter', freetextFilter);
  o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// *** Display subscriptions functions.
// *************************************************************************************************
// Display the spinner. Once visible, display subscriptions.
function displaySubscriptions()
{
  Utility.displaySpinnerThen(doDisplaySubscriptions);
}

// *************************************************************************************************
// Display the list of subscriptions.
function doDisplaySubscriptions()
{
  var o, p, i;
  
  if (subscriptions.length <= 0)
  {
    subscriptionsBox.innerHTML = '<div class="form-element">' +
      getText(34, 'Det er ikke opprettet noen abonnementer enn&aring;.') + '</div>';
    filterToolbar.innerHTML = '&nbsp;';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((subscriptions.length * 21) + 12);
  p = 0;

  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(0, 'Navn'));
  o[p++] = sorting.getTableHeader(1, getText(1, 'Lager'));
  o[p++] = sorting.getTableHeader(2, getText(2, 'Lagerbod'));
  o[p++] = sorting.getTableHeader(3, getText(3, 'Bodtype'));
  o[p++] = sorting.getTableHeader(4, getText(4, 'Status'));
  o[p++] = sorting.getTableHeader(5, getText(5, 'Fra dato'));
  o[p++] = sorting.getTableHeader(6, getText(6, 'Til dato'));
  o[p++] = sorting.getTableHeader(7, getText(7, 'Forsikring'));
  o[p++] = sorting.getTableHeader(8, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < subscriptions.length; i++)
  {
    if (shouldHide(subscriptions[i]))
      continue;
    // Find the user that owns this subscription.
    user = getUser(subscriptions[i][c.sua.BUYER_ID]);
    // If the user was not found, do not display the subscription. This should not happen.
    if (user === null)
      continue;
    displayedCount++;

    // Buyer's name.
    o[p++] = '<tr><td>';
    o[p++] = user[c.rqu.NAME];
    // Location name.
    o[p++] = '</td><td>';
    o[p++] = Utility.getLocationName(subscriptions[i][c.sua.LOCATION_ID]);
    // Product name and price.
    o[p++] = '</td><td>';
    o[p++] = subscriptions[i][c.sua.PRODUCT_NAME];
    o[p++] = getPriceButton(i, PricePlan.getProductPricePlan(subscriptions, i));
    // Product type name.
    o[p++] = '</td><td>';
    o[p++] = Utility.getProductTypeName(subscriptions[i][c.sua.PRODUCT_TYPE_ID]);
    // Status.
    o[p++] = '</td><td>';
    o[p++] = Utility.getStatusLabel(st.sub.TEXTS, st.sub.COLOURS, subscriptions[i][c.sua.STATUS]);
    // Start date.
    o[p++] = '</td><td>';
    o[p++] = subscriptions[i][c.sua.START_DATE];
    // End date.
    o[p++] = '</td><td>';
    if (subscriptions[i][c.sua.END_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = subscriptions[i][c.sua.END_DATE];
    // Insurance name and price.
    o[p++] = '</td><td>';
    if (subscriptions[i][c.sua.INSURANCE_NAME] === '')
      o[p++] = '&nbsp;';
    else
    {
      o[p++] = subscriptions[i][c.sua.INSURANCE_NAME];
      o[p++] = getPriceButton(i, PricePlan.getInsurancePricePlan(subscriptions, i));
    }
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';
  
  subscriptionsBox.innerHTML = o.join('');
  displayFilterToolbar();
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p, editable;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return '';
  o = new Array(6);
  p = 0;

  // Payment history button.
  o[p++] = sender.getMenuItem(getText(8, 'Vis ordrehistorikk'), 'fa-file-invoice-dollar', true,
    'loadPaymentHistory(' + String(index) + ');');
  // Cancel subscription button. Disabled if the subscription is not ongoing.
  o[p++] = sender.getMenuItem(getText(50, 'Si opp abonnement'), 'fa-hand-wave',
    subscriptions[index][c.sua.STATUS] === st.sub.ONGOING,
    'displayCancelSubscriptionDialogue(' + String(index) + ');');
  // Display customer button.
  o[p++] = sender.getMenuItem(getText(59, 'Vis kundekort'), 'fa-up-right-from-square', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/admin_edit_user.php?user_id=' +
    String(subscriptions[index][c.sua.BUYER_ID]) + '\');');
  o[p++] = '<br />';
  // Modify price buttons.
  editable = (subscriptions[index][c.sua.STATUS] === st.sub.ONGOING) ||
    (subscriptions[index][c.sua.STATUS] === st.sub.CANCELLED) ||
    (subscriptions[index][c.sua.STATUS] === st.sub.BOOKED);
  o[p++] = sender.getMenuItem(getText(-1, 'Endre pris p&aring; abonnement'), 'fa-pen-to-square',
    editable, 'displayEditPricePlanDialogue(' + String(index) + ', -1);');
  o[p++] = sender.getMenuItem(getText(-1, 'Endre pris p&aring; forsikring'), 'fa-pen-to-square',
    editable, 'displayEditPricePlanDialogue(' + String(index) + ', ' +
    String(ADDITIONAL_PRODUCT_INSURANCE) + ');');
  return o.join('');
}

// *************************************************************************************************
// Return the user in the subscriptionUsers table with the given user ID, or null if it was not
// found.
function getUser(id)
{
  var i;

  for (i = 0; i < subscriptionUsers.length; i++)
  {
    if (subscriptionUsers[i][c.rqu.ID] === id)
      return subscriptionUsers[i];
  }
  return null;
}

// *************************************************************************************************
// *** Cancel subscription functions.
// *************************************************************************************************

function displayCancelSubscriptionDialogue(index)
{
  var o, p, today;

  today = Utility.getCurrentIsoDate();
  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return;

  o = new Array(41);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(50, 'Si opp abonnement');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><form id="cancelSubscriptionForm" action="/subscription/html/admin_subscriptions.php" method="post">';
  o[p++] = getPageStateFormElements();
  o[p++] = '<input type="hidden" name="action" value="cancel_subscription" />';
  o[p++] = Utility.getHidden('id', subscriptions[index][c.sua.ID]);
  // Confirmation caption.
  o[p++] = '<div class="form-element"><p>';
  o[p++] = getText(46, 'Si opp $1 på vegne av kunden?', [subscriptions[index][c.sua.PRODUCT_NAME]]);
  o[p++] = '</p></div>';
  // Standard cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="normalCancelButton" name="cancel_type" value="0" checked="checked" onChange="switchCancelType();" /> <label for="normalCancelButton">';
  o[p++] = getText(51, 'Vanlig oppsigelse');
  o[p++] = '</label></div>';
  // Standard cancellation message.
  o[p++] = '<div id="standardCancelBox" class="radio-indent-box"><span class="help-text">';
  if (Utility.canCancelThisMonth())
    o[p++] = getText(30, 'Kunden beholder lagerboden til og med siste dag i innev&aelig;rende m&aring;ned.');
  else
    o[p++] = getText(31, 'Kunden trekkes for neste m&aring;ned, og beholder lagerboden til og med siste dag neste m&aring;ned.');
  o[p++] = '</span></div>';
  // Immediate cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="immediateCancelButton" name="cancel_type" value="1" onChange="switchCancelType();" /> <label for="immediateCancelButton">';
  o[p++] = getText(52, 'Si opp umiddelbart');
  o[p++] = '</label></div>';
  // Immediate cancellation message.
  o[p++] = '<div id="immediateCancelBox" class="radio-indent-box" style="display: none;"><div class="custom-cancel-result-box">';
  o[p++] = getImmediateCancelResultText(Utility.getDayBefore(today));
  o[p++] = '</div></div>';
  // Custom cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="customCancelButton" name="cancel_type" value="2" onChange="switchCancelType();" /> <label for="customCancelButton">';
  o[p++] = getText(53, 'Velg sluttdato');
  o[p++] = '</label></div>';
  // Custom cancel box.
  o[p++] = '<div id="customCancelBox" class="radio-indent-box" style="display: none;">';
  // End date.
  o[p++] = '<div><label for="endDateEdit" class="standard-label">';
  o[p++] = getText(54, 'Siste dag:');
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
  o[p++] = getText(55, 'Si opp');
  o[p++] = '</button> <button type="button" onclick="closeCancelSubscriptionDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(45, 'Avbryt');
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

  o[p++] = getText(27, 'Kunden');
  o[p++] = '<ul><li>';
  o[p++] = getText(56, 'Mister tilgang umiddelbart.');
  o[p++] = '</li>';
  if (endDateIso !== lastDayOfMonth)
  {
    o[p++] = '<li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(57, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(48, 'Mister $1 dagers leie.', [String(lostDayCount)]);
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
  o[p++] = getText(27, 'Kunden');
  o[p++] = '<ul>';

  // The end date cannot be before today's date, but catch the case here.
  if (endDateIso <= todayIso)
  {
    // The end date is today.
    o[p++] = '<li>';
    o[p++] = getText(33, 'Beholder lagerboden fram til midnatt.');
    o[p++] = '</li><li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(today, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(57, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(48, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li></ul>';
    return o.join('');
  }

  if (endDateIso === lastDayOfMonth)
  {
    // The end date is the last day of the current month.
    o[p++] = '<li>';
    o[p++] = getText(47, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li></ul>';
    return o.join('');
  }

  if (endDateIso < lastDayOfMonth)
  {
    // The end date is later this month, but before the last day of the month.
    o[p++] = '<li>';
    o[p++] = getText(47, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li><li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(57, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(48, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li></ul>';
    return o.join('');
  }

  lastDayOfMonth = Utility.getLastDay(endDate);
  if (endDateIso === lastDayOfMonth)
  {
    // The end date is the last day of a future month.
    o[p++] = '<li>';
    o[p++] = getText(49, 'Trekkes som vanlig til og med $1 $2.',
      [MONTH_NAMES_IN_SENTENCE[endDate.getMonth()], String(endDate.getFullYear())]);
    o[p++] = '</li><li>';
    o[p++] = getText(47, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li></ul>';
    return o.join('');
  }

  // The end date is any other day of a future month.
  o[p++] = '<li>';
  o[p++] = getText(49, 'Trekkes som vanlig til og med $1 $2.',
    [MONTH_NAMES_IN_SENTENCE[endDate.getMonth()], String(endDate.getFullYear())]);
  o[p++] = '</li><li>';
  o[p++] = getText(47, 'Beholder lagerboden til og med $1.', [endDateIso]);
  o[p++] = '</li><li><span class="warning-text">';
  lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
  if (lostDayCount === 1)
    o[p++] = getText(57, 'Mister &eacute;n dags leie.');
  else
    o[p++] = getText(48, 'Mister $1 dagers leie.', [String(lostDayCount)]);
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
// *** Price plan functions.
// *************************************************************************************************
// Return HTML code for a button to open a price plan window for the price plan with the given
// pricePlanIndex, for the subscription with the given index. The button displays the current price.
// If the price is -1, it will not be displayed.
function getPriceButton(index, pricePlanIndex)
{
  var o, p, price;

  if (pricePlanIndex < 0)
    return '';
  o = new Array(8);
  p = 0;

  o[p++] = '<button type="button" class="table-button" onclick="displayPricePlan(';
  o[p++] = String(index);
  o[p++] = ', ';
  o[p++] = String(pricePlanIndex);
  o[p++] = ');">';
  price = PricePlan.getPriceFromPricePlan(subscriptions, index, pricePlanIndex);
  if (price >= 0)
  {
    o[p++] = String(price);
    o[p++] = ',-';
  }
  else
    o[p++] = getText(9, 'Pris');
  o[p++] = '</button>';
  return o.join('');
}

// *************************************************************************************************
// Open a dialogue box that displays the price plan with the given pricePlanIndex, for the
// subscription with the given index.
function displayPricePlan(index, pricePlanIndex)
{
  var o, p, i, planType, planLines, subscriptionEndDate;

  planLines = PricePlan.getPricePlanLines(subscriptions, index, pricePlanIndex);
  if (planLines === null)
    return;
  o = new Array((planLines.length * 11) + 14);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  planType = subscriptions[index][c.sua.PRICE_PLANS][pricePlanIndex][c.sua.PLAN_TYPE];
  if (planType < 0)
    o[p++] = getText(10, 'Prishistorikk, leie');
  else
    o[p++] = getText(11, 'Prishistorikk, $1', [ADDITIONAL_PRODUCT_TEXTS[planType]]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(12, 'Fra dato');
  o[p++] = '</th><th>';
  o[p++] = getText(13, 'Til dato');
  o[p++] = '</th><th>';
  o[p++] = getText(9, 'Pris');
  o[p++] = '</th><th>';
  o[p++] = getText(14, 'Grunn');
  o[p++] = '</th><th>';
  o[p++] = getText(15, 'Beskrivelse');
  o[p++] = '</th></tr></thead><tbody>';
  subscriptionEndDate = subscriptions[index][c.sua.END_DATE];
  for (i = 0; i < planLines.length; i++)
  {
    // Do not display this line in the price plan if the subscription ends before this line comes
    // into effect.
    if ((subscriptionEndDate !== '') && (planLines[i][c.sua.LINE_START_DATE] > subscriptionEndDate))
      continue;
    o[p++] = '<tr><td>';
    o[p++] = planLines[i][c.sua.LINE_START_DATE];
    o[p++] = '</td><td>';
    o[p++] = getEndDate(planLines, i, subscriptionEndDate);
    o[p++] = '</td><td>';
    o[p++] = String(planLines[i][c.sua.LINE_PRICE]);
    o[p++] = ',-</td><td>';
    if (planLines[i][c.sua.LINE_CAUSE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = planLines[i][c.sua.LINE_CAUSE];
    o[p++] = '</td><td>';
    if (planLines[i][c.sua.LINE_DESCRIPTION] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = planLines[i][c.sua.LINE_DESCRIPTION];
    o[p++] = '</td></tr>';
  }
  if (subscriptions[index][c.sua.END_DATE] !== '')
  {
    o[p++] = '<tr><td>';
    o[p++] = subscriptions[index][c.sua.END_DATE];
    o[p++] = '</td><td>&nbsp;</td><td>&nbsp;</td><td>';
    o[p++] = getText(16, 'Abonnementet avsluttet');
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table></div><div class="dialogue-footer"><button type="button" onclick="closePricePlanDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(17, 'Lukk');
  o[p++] = '</button></div></form>';

  pricePlanDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(pricePlanDialogue);
}

// *************************************************************************************************
// Return the end date of the price plan with the given index, in the given array of price plan
// lines. The end date is the day before the start date of the next line. If there is no next line,
// the price will apply until further notice, unless the subscription itself has an end date - as
// given in subscriptionEndDate. If the subscription has no end date, subscriptionEndDate should be
// an empty string.
function getEndDate(planLines, index, subscriptionEndDate)
{
  var endDate;

  if (index >= (planLines.length - 1))
  {
    // This is the last element in the price plan. If the subscription will end, that's the end
    // date. Otherwise, the price applies until further notice.
    if (subscriptionEndDate !== '')
      return subscriptionEndDate;
    return getText(18, 'Inntil videre');
  }
  // The price ends the day before the next price in the price plan takes effect. However, if the
  // subscription ends before that, that's the end date.
  endDate = Utility.getDayBefore(planLines[index + 1][c.sua.LINE_START_DATE]);
  if ((subscriptionEndDate !== '') && (subscriptionEndDate < endDate))
    return subscriptionEndDate;
  return endDate;
}

// *************************************************************************************************
// Close the price plan dialogue box.
function closePricePlanDialogue()
{
  Utility.hide(pricePlanDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// *** Payment history functions.
// *************************************************************************************************
// Display the payment history for the subscription with the given index in the subscriptions table.
// If the payment history has already been loaded, display it straight away. Otherwise, load it from
// the server and display it when received.
function loadPaymentHistory(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, subscriptions))
  {
    // See if the payment history for this subscription is already available. If so, display it.
    if (subscriptions[index][c.sua.PAYMENT_HISTORY] !== null)
      displayPaymentHistory(index);
    else
    {
      // Fetch the payment history from the server, then store and display it.
      paymentHistoryDialogue.innerHTML = '<p>' +
        getText(19, 'Laster ordrehistorikk. Vennligst vent...') + '</p>';
      Utility.display(overlay);
      Utility.display(paymentHistoryDialogue);
      fetch('/subscription/json/admin_payment_history.php?subscription_id=' +
        String(subscriptions[index][c.sua.ID]) + '&user_id=' +
        String(subscriptions[index][c.sua.BUYER_ID]))
        .then(Utility.extractJson)
        .then(storePaymentHistory)
        .catch(logPaymentHistoryError);
    }
  }
}

// *************************************************************************************************
// Log an error that occurred while fetching the payment history from the server.
function logPaymentHistoryError(error)
{
  console.error('Error fetching payment history: ' + error);
  closePaymentHistoryDialogue();
}

// *************************************************************************************************
// Store payment history for a single subscription in the subscriptions table, then display it.
function storePaymentHistory(data)
{
  var index;

  if (data && data.resultCode)
  {
    if (Utility.isError(data.resultCode))
    {
      console.error('Error fetching payment history: result code: ' + String(data.resultCode));
      closePaymentHistoryDialogue();
    }
    else
    {
      if (data.subscriptionId && data.paymentHistory)
      {
        index = Utility.getSubscriptionIndex(data.subscriptionId);
        if (index < 0)
        {
          console.error('Error fetching payment history: subscription ID ' +
            String(data.subscriptionId) + ' not found in the table.');
          closePaymentHistoryDialogue();
        }
        else
        {
          // All the information was present and valid. Store and display the payment history.
          subscriptions[index][c.sua.PAYMENT_HISTORY] = data.paymentHistory;
          displayPaymentHistory(index);
        }
      }
      else
      {
        console.error('Error fetching payment history: subscription ID or payment history missing.');
        closePaymentHistoryDialogue();
      }
    }
  }
  else
  {
    console.error('Error fetching payment history: data object or result code missing.');
    closePaymentHistoryDialogue();
  }
}

// *************************************************************************************************
// Display the payment history for the subscription with the given index in the subscriptions table.
// This method assumes that the payment history is available in the subscriptions table.
function displayPaymentHistory(index)
{
  var o, p, i, paymentHistory, style, amount;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return;
  paymentHistory = subscriptions[index][c.sua.PAYMENT_HISTORY];
  o = new Array((paymentHistory.length * 36) + 20);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(20, 'Ordrehistorikk for $1, $2',
    [subscriptions[index][c.sua.PRODUCT_NAME], Utility.getLocationName(subscriptions[index][c.sua.LOCATION_ID])]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>&nbsp;</th><th>';
  o[p++] = getText(21, 'Type');
  o[p++] = '</th><th>';
  o[p++] = getText(22, 'Fakturanr');
  o[p++] = '</th><th>';
  o[p++] = getText(23, 'Betalingsm&aring;te');
  o[p++] = '</th><th>';
  o[p++] = getText(24, 'Utstedt');
  o[p++] = '</th><th>';
  o[p++] = getText(25, 'Forfallsdato');
  o[p++] = '</th><th>';
  o[p++] = getText(4, 'Status');
  o[p++] = '</th><th>';
  o[p++] = getText(26, 'Sum');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < paymentHistory.length; i++)
  {
    if (paymentHistory[i][c.pay.OPEN])
      style = ' class="payment-details-open"';
    else
      style = '';
    o[p++] = '<tr><td';
    o[p++] = style;
    o[p++] = '><button type="button" class="icon-button" onclick="togglePaymentLine(';
    o[p++] = String(index);
    o[p++] = ', ';
    o[p++] = String(i);
    o[p++] = ');">';
    if (paymentHistory[i][c.pay.OPEN])
      o[p++] = '<i class="fa-solid fa-minus"></i>';
    else
      o[p++] = '<i class="fa-solid fa-plus"></i>';
    o[p++] = '</button></td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.NAME];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.ID];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = PAYMENT_METHOD_TEXTS[paymentHistory[i][c.pay.PAYMENT_METHOD]];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.ORDER_DATE];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.PAY_BY_DATE];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = Utility.getStatusLabel(PAYMENT_STATUS_TEXTS, PAYMENT_STATUS_COLOURS,
      paymentHistory[i][c.pay.PAYMENT_STATUS]);
    o[p++] = '</td><td class="currency">';
    amount = getOrderAmount(index, i);
    o[p++] = String(amount);
    o[p++] = ',-</td></tr>';
    // Write table of order lines, if the user has opened the box.
    if (paymentHistory[i][c.pay.OPEN])
      o[p++] = getOrderLines(paymentHistory, i, amount);
  }
  o[p++] = '</tbody></table></div><div class="dialogue-footer"><button type="button" onclick="closePaymentHistoryDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(17, 'Lukk');
  o[p++] = '</button></div></form>';

  paymentHistoryDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(paymentHistoryDialogue);
}

// *************************************************************************************************
// Return HTML code for a table line that contains a box that displays order lines for the order
// with the given index in the given payment history. amount is the total amount for the order.
function getOrderLines(paymentHistory, orderIndex, amount)
{
  var o, p, i, orderLines;

  orderLines = paymentHistory[orderIndex][c.pay.ORDER_LINES];
  o = new Array((orderLines.length * 7) + 12);
  p = 0;

  // Headline.
  o[p++] = '<tr class="payment-details"><td colspan="8" class="payment-details"><div class="payment-details"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(15, 'Beskrivelse');
  o[p++] = '</th><th>';
  o[p++] = getText(28, 'Produkt-ID');
  o[p++] = '</th><th>';
  o[p++] = getText(29, 'Bel&oslash;p');
  o[p++] = '</th></tr></thead><tbody>';
  // Order lines.
  for (i = 0; i < orderLines.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = orderLines[i][c.pay.LINE_TEXT];
    o[p++] = '</td><td>';
    o[p++] = String(orderLines[i][c.pay.LINE_ID]);
    o[p++] = '</td><td class="currency">';
    o[p++] = String(orderLines[i][c.pay.LINE_AMOUNT]);
    o[p++] = ',-</td></tr>';
  }
  // Sum.
  o[p++] = '<tr><td colspan="2" class="sum">';
  o[p++] = getText(26, 'Sum');
  o[p++] = '</td><td class="sum currency">';
  o[p++] = String(amount);
  o[p++] = ',-</td></tr></tbody></table></div></td></tr>';
  return o.join('');
}

// *************************************************************************************************
// Return the total amount paid for an order. The order is specified by the given index into the
// subscription table, and then the given orderIndex for that subscription.
function getOrderAmount(subscriptionIndex, orderIndex)
{
  var paymentHistory, orderLines, i, amount;

  amount = 0;
  subscriptionIndex = parseInt(subscriptionIndex, 10);
  if (Utility.isValidIndex(subscriptionIndex, subscriptions))
  {
    paymentHistory = subscriptions[subscriptionIndex][c.sua.PAYMENT_HISTORY];

    orderIndex = parseInt(orderIndex, 10);
    if (Utility.isValidIndex(orderIndex, paymentHistory))
    {
      orderLines = paymentHistory[orderIndex][c.pay.ORDER_LINES];
      for (i = 0; i < orderLines.length; i++)
        amount += orderLines[i][c.pay.LINE_AMOUNT];
    }
  }
  return amount;
}

// *************************************************************************************************

function togglePaymentLine(subscriptionIndex, orderIndex)
{
  var paymentHistory;

  subscriptionIndex = parseInt(subscriptionIndex, 10);
  if (Utility.isValidIndex(subscriptionIndex, subscriptions))
  {
    paymentHistory = subscriptions[subscriptionIndex][c.sua.PAYMENT_HISTORY];

    orderIndex = parseInt(orderIndex, 10);
    if (Utility.isValidIndex(orderIndex, paymentHistory))
    {
      paymentHistory[orderIndex][c.pay.OPEN] = !paymentHistory[orderIndex][c.pay.OPEN];
      displayPaymentHistory(subscriptionIndex);
    }
  }
}

// *************************************************************************************************
// Close the payment history dialogue.
function closePaymentHistoryDialogue()
{
  Utility.hide(paymentHistoryDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Edit price plan functions.
// *************************************************************************************************
// Open the edit price plan dialogue, and generate its contents in order to edit a price plan for
// the subscription with the given index in the subscriptions table. planType indicates which price
// plan to edit. Use the ADDITIONAL_PRODUCT_ constants, or pass -1 to edit the price plan for the
// subscription itself. This function copies the price mods for the specified price plan, allowing
// the user to edit it in the dialogue box without affecting anything else.
function displayEditPricePlanDialogue(index, planType)
{
  var pricePlanIndex;

  // Find the index of the price plan to be edited.
  if (planType === ADDITIONAL_PRODUCT_INSURANCE)
    pricePlanIndex = PricePlan.getInsurancePricePlan(subscriptions, index);
  else
    pricePlanIndex = PricePlan.getProductPricePlan(subscriptions, index);
  if (pricePlanIndex < 0)
    return;

  // Copy the current price mods to the editedPricePlanLines global variable. The copy is never
  // null, although it might be an empty array.
  editedPricePlanLines = copyPricePlanLines(PricePlan.getPricePlanLines(subscriptions, index, pricePlanIndex));

  // Write the contents of the edit price plan dialogue.
  displayPricePlanLines(index, planType);

  // Display the edit price plan dialogue.
  Utility.display(overlay);
  Utility.display(editPricePlanDialogue);
}

// *************************************************************************************************
// Display the editedPricePlanLines in the price plan dialogue.
function displayPricePlanLines(index, planType)
{
  var o, p, i, today, isPast;

  today = Utility.getCurrentIsoDate();
  o = new Array(); // *** //
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h3>';
  if (planType === ADDITIONAL_PRODUCT_INSURANCE)
    o[p++] = getText(-1, 'Endre prisplan for forsikring');
  else
    o[p++] = getText(-1, 'Endre prisplan for abonnement');
  o[p++] = '</h3></div>';

  // Content.
  o[p++] = '<div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(-1, 'Fra og med dato');
  o[p++] = '</th><th>';
  o[p++] = getText(-1, 'Ny pris');
  o[p++] = '</th><th>';
  o[p++] = getText(-1, 'Beskrivelse (synlig for kunden)');
  o[p++] = '</th><th class="delete-column">&nbsp;</th></tr></thead><tbody>';
  // Write the lines of this price plan. Note that a line cannot be edited if it applies today or
  // previously. There's no point in changing the past, as billing has already happened. If the
  // change applies as of today, billing may already have happened. If it applies from tomorrow, we
  // know for sure billing has not happened, so we can change the line freely.
  for (i = 0; i < editedPricePlanLines.length; i++)
  {
    isPast = editedPricePlanLines[i][c.sua.LINE_START_DATE] <= today;
    // Date.
    o[p++] = '<tr><td><input type="text" id="lineStartDateEdit_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = editedPricePlanLines[i][c.sua.LINE_START_DATE];
    o[p++] = '" disabled="disabled" class="date-edit" /></td>';

    // Price.
    o[p++] = '<td><input type="number" id="linePriceEdit_';
    o[p++] = String(i);
    o[p++] = '" min="0" value="';
    o[p++] = String(editedPricePlanLines[i][c.sua.LINE_PRICE]);
    o[p++] = '"';
    if (isPast)
      o[p++] = ' disabled="disabled"';
    o[p++] = ' class="price-edit" onchange="" /></td>';

    // Description.
    o[p++] = '<td><input type="text" id="lineDescriptionEdit_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = String(editedPricePlanLines[i][c.sua.LINE_DESCRIPTION]);
    o[p++] = '"';
    if (isPast)
      o[p++] = ' disabled="disabled"';
    o[p++] = ' onchange="" /></td>';

    // Delete button.
    o[p++] = '<td><button type="button" class="icon-button" onclick="deletePricePlanLine(';
    o[p++] = String(i);
    o[p++] = ');"';
    if (isPast)
      o[p++] = ' disabled="disabled"';
    o[p++] = '><i class="fa-solid fa-trash"></i></button></td></tr>';
  }
  o[p++] = '</tbody></table></div>';

  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="storePricePlan();"><i class="fa-solid fa-check"></i>&nbsp;&nbsp;';
  o[p++] = getText(44, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeEditPricePlanDialogue();"><i class="fa-solid fa-xmark"></i>&nbsp;&nbsp;';
  o[p++] = getText(45, 'Avbryt');
  o[p++] = '</button></div>';


  editPricePlanDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  // Utility.readPointers(['customBasePriceEdit', 'customInsurancePriceEdit', 'priceModEditorBox']);
}

// *************************************************************************************************
// Delete the price plan line with the given index in the price plan currently being edited.
function deletePricePlanLine(index)
{
    // *** //
  alert('Delete ' + String(index));
}

// *************************************************************************************************

function closeEditPricePlanDialogue()
{
  editedPricePlanLines = null;
  Utility.hide(editPricePlanDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function storePricePlan()
{
    // *** //
  closeEditPricePlanDialogue();
}

// *************************************************************************************************
// Return a copy of the given price plan lines. If null is passed, the function returns an empty
// array.
function copyPricePlanLines(pricePlanLines)
{
  var result, i;

  if (pricePlanLines === null)
    return [];
  result = new Array(pricePlanLines.length);
  for (i = 0; i < pricePlanLines.length; i++)
    result[i] = Array.from(pricePlanLines[i]);
  return result;
}

// *************************************************************************************************
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  o = new Array(30);
  p = 0;

  // Clear all filters button.
  o[p++] = getText(35, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(36, 'Vis alle');
  o[p++] = '</button>';
  // Location filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (locationFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayLocationFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(1, 'Lager');
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
  o[p++] = getText(3, 'Bodtype');
  o[p++] = '</button>';
  // Clear product type filter button.
  if (productTypeFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearProductTypeFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Status filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (statusFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayStatusFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(4, 'Status');
  o[p++] = '</button>';
  // Clear status filter button.
  if (statusFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearStatusFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Freetext filter edit.
  o[p++] = '<input type="text" id="freetextEdit" placeholder="';
  o[p++] = getText(58, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Display counter box.
  o[p++] = '<span class="counter">';
  if (displayedCount === subscriptions.length)
    o[p++] = getText(37, 'Viser $1 abonnementer', [String(subscriptions.length)]);
  else
    o[p++] = getText(38, 'Viser $1 av $2 abonnementer',
      [String(displayedCount), String(subscriptions.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['freetextEdit']);
}

// *************************************************************************************************
// Return true if the list of subscriptions should not include the given subscription.
function shouldHide(subscription)
{
  return ((locationFilter !== null) && !locationFilter.includes(subscription[c.sua.LOCATION_ID])) ||
    ((productTypeFilter !== null) && !productTypeFilter.includes(subscription[c.sua.PRODUCT_TYPE_ID])) ||
    ((statusFilter !== null) && !statusFilter.includes(subscription[c.sua.STATUS])) ||
    ((freetextFilter !== '') && !matchesFreetextFilter(subscription));
}

// *************************************************************************************************

function clearAllFilters()
{
  locationFilter = null;
  productTypeFilter = null;
  statusFilter = null;
  freetextFilter = '';
  freetextEdit.value = '';
  displaySubscriptions();
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
  o[p++] = getText(39, 'Velg hvilke lager som skal vises');
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
  o[p++] = getText(42, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(43, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateLocationFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(44, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeLocationFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(45, 'Avbryt');
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
  displaySubscriptions();
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
  displaySubscriptions();
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
  o[p++] = getText(40, 'Velg hvilke bodtyper som skal vises');
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
  o[p++] = getText(42, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllProductTypesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(43, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateProductTypeFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(44, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeProductTypeFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(45, 'Avbryt');
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
  displaySubscriptions();
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
  displaySubscriptions();
}

// *************************************************************************************************

function closeProductTypeFilterDialogue()
{
  Utility.hide(editProductTypeFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Status filter functions.
// *************************************************************************************************
// Return true if the list of products is currently filtered on status, and the filter includes the
// given status. 
function inStatusFilter(status)
{
  return (statusFilter !== null) && statusFilter.includes(status);
}

// *************************************************************************************************

function displayStatusFilterDialogue()
{
  var o, p, i;
  
  o = new Array((st.sub.TEXTS.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(41, 'Velg hvilke statuser som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  // Status checkboxes.
  for (i = 0; i < st.sub.TEXTS.length; i++)
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
    o[p++] = st.sub.TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllStatusesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(42, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllStatusesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(43, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateStatusFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(44, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeStatusFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(45, 'Avbryt');
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
  for (i = 0; i < st.sub.TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function clearStatusFilter()
{
  statusFilter = null;
  displaySubscriptions();
}

// *************************************************************************************************

function updateStatusFilter()
{
  var i, checkbox;

  statusFilter = [];
  for (i = 0; i < st.sub.TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      statusFilter.push(i);
  }
  // If the user unchecks all statuses, instead of displaying nothing, clear the filter. If the
  // user checks all statuses, also clear the filter.
  if ((statusFilter.length === 0) || (statusFilter.length === st.sub.TEXTS.length))
    statusFilter = null;
  closeStatusFilterDialogue();
  displaySubscriptions();
}

// *************************************************************************************************

function closeStatusFilterDialogue()
{
  Utility.hide(editStatusFilterDialogue);
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
  displaySubscriptions();
}

// *************************************************************************************************
// Return true if the given subscription matches the current freetext filter.
function matchesFreetextFilter(subscription)
{
  var filter, user;

  filter = freetextFilter.toLowerCase();
  user = getUser(subscription[c.sua.BUYER_ID]);
  if (user === null)
      return false;
  // If there is no filter (or no subscription), everything matches. Otherwise, return a match if
  // the subscription's buyer's name, location name, product name, product type name, status, start
  // date, end date or insurance name fields contain the filter text.
  return (subscription === null) || (filter === '') ||
    (user[c.rqu.NAME].toLowerCase().indexOf(filter) >= 0) ||
    (Utility.getLocationName(subscription[c.sua.LOCATION_ID]).toLowerCase().indexOf(filter) >= 0) ||
    (subscription[c.sua.PRODUCT_NAME].toLowerCase().indexOf(filter) >= 0) ||
    (Utility.getProductTypeName(subscription[c.sua.PRODUCT_TYPE_ID]).toLowerCase().indexOf(filter) >= 0) ||
    (st.sub.TEXTS[subscription[c.sua.STATUS]].toLowerCase().indexOf(filter) >= 0) ||
    (subscription[c.sua.START_DATE].indexOf(filter) >= 0) ||
    (subscription[c.sua.END_DATE].indexOf(filter) >= 0) ||
    (subscription[c.sua.INSURANCE_NAME].toLowerCase().indexOf(filter) >= 0);
}

// *************************************************************************************************
