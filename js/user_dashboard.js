// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var subscriptionsBox, overlay, cancelSubscriptionDialogue, cancelSubscriptionDialogueContent,
  paymentHistoryDialogue;

// The index in the subscriptions table of the subscription which is currently being cancelled, or
// -1 if the cancel subscription dialogue is not displayed.
var cancellingIndex = -1;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and displaying the first page of the progress tabset.
function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['subscriptionsBox', 'overlay', 'cancelSubscriptionDialogue',
    'cancelSubscriptionDialogueContent', 'paymentHistoryDialogue']);

  // Display the list of subscriptions.
  displaySubscriptions();
}

// *************************************************************************************************

function displaySubscriptions()
{
  var o, p, i, locationIndex, isOngoing, rent, insurance, price;

  o = new Array((subscriptions.length * 60) + 3);
  p = 0;

  for (i = 0; i < subscriptions.length; i++)
  {
    locationIndex = Utility.getLocationIndex(subscriptions[i][c.sub.LOCATION_ID]);
    isOngoing = subscriptions[i][c.sub.STATUS] === st.sub.ONGOING;
    // Subscription box.
    o[p++] = '<div class="button-box">';
    // Header.
    o[p++] = '<div class="button-box-left subscription-left';
    if (isOngoing)
      o[p++] = '-ongoing';
    o[p++] = '"><h3>';
    // Location name.
    o[p++] = locations[locationIndex][c.loc.NAME];
    o[p++] = '</h3>';
    // Location address.
    o[p++] = Utility.getAddress(locations[locationIndex]);
    o[p++] = '</div>';
    // Cancel button.
    if (isOngoing)
    {
      o[p++] = '<div class="button-box-right subscription-right-ongoing"><button type="button" class="low-profile" onclick="displayCancelSubscriptionDialogue(';
      o[p++] = String(i);
      o[p++] = ');"><i class="fa-solid fa-hand-wave"></i>&nbsp;&nbsp;';
      o[p++] = getText(2, 'Si opp');
      o[p++] = '</button></div>';
    }
    // Content.
    o[p++] = '<div class="button-box-bottom subscription-bottom"><table cellspacing="0" cellpadding="0"><tbody>';
    // Product name.
    o[p++] = '<tr><td class="subscription-caption">';
    o[p++] = getText(3, 'Lagerbod');
    o[p++] = '</td><td class="subscription-data">';
    o[p++] = subscriptions[i][c.sub.PRODUCT_NAME];
    o[p++] = '</td></tr>';
    // Storage unit type name.
    o[p++] = '<tr><td class="subscription-caption">';
    o[p++] = getText(4, 'Bodtype');
    o[p++] = '</td><td class="subscription-data">';
    o[p++] = Utility.getProductTypeName(subscriptions[i][c.sub.PRODUCT_TYPE_ID]);
    o[p++] = '</td></tr>';
    // Start date.
    o[p++] = '<tr><td class="subscription-caption">';
    o[p++] = getText(5, 'Fra dato');
    o[p++] = '</td><td class="subscription-data">';
    o[p++] = subscriptions[i][c.sub.START_DATE];
    o[p++] = '</td></tr>';
    // Status and end date.
    o[p++] = '<tr><td class="subscription-caption">';
    o[p++] = getText(6, 'Status');
    o[p++] = '</td><td class="subscription-data">';
    o[p++] = Utility.getStatusLabel(st.sub.TEXTS, st.sub.COLOURS, subscriptions[i][c.sub.STATUS]);
    if (subscriptions[i][c.sub.END_DATE] !== '')
    {
      o[p++] = getText(7, ' Siste&nbsp;dag:&nbsp;');
      o[p++] = subscriptions[i][c.sub.END_DATE];
    }
    o[p++] = '</td></tr>';
    // Current price. This includes both rent and insurance.
    o[p++] = '<tr><td class="subscription-caption">';
    o[p++] = getText(8, 'Pris');
    o[p++] = '</td><td class="subscription-data">';
    rent = PricePlan.getPriceFromPricePlan(subscriptions, i,
      PricePlan.getProductPricePlan(subscriptions, i));
    if (rent >= 0)
      price = rent;
    else
      price = 0;
    insurance = PricePlan.getPriceFromPricePlan(subscriptions, i,
      PricePlan.getInsurancePricePlan(subscriptions, i));
    if (insurance >= 0)
      price += insurance;
    if ((rent >= 0) || (insurance >= 0))
    {
      o[p++] = String(price);
      o[p++] = getText(9, ' kr');
    }
    else
      o[p++] = '&nbsp;';
    o[p++] = '</td></tr>';
    // Order history.
    o[p++] = '<tr><td class="subscription-caption">';
    o[p++] = getText(10, 'Betalinger');
    o[p++] = '</td><td class="subscription-data"><button type="button" class="low-profile wide-button" onclick="loadPaymentHistory(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-file-invoice"></i>&nbsp;&nbsp;';
    o[p++] = getText(11, 'Se betalinger');
    o[p++] = '</button></td></tr>';
    
    // Payment method update button - only show for ongoing subscriptions with Nets payment method
    if (isOngoing && subscriptions[i][c.sub.PAYMENT_METHOD] === Utility.PAYMENT_METHOD_NETS && 
        subscriptions[i][c.sub.NETS_SUBSCRIPTION_ID])
    {
      o[p++] = '<tr><td class="subscription-caption">';
      o[p++] = getText(16, 'Betalingskort');
      o[p++] = '</td><td class="subscription-data"><button type="button" class="low-profile wide-button" onclick="updatePaymentCard(';
      o[p++] = String(i);
      o[p++] = ');"><i class="fa-solid fa-credit-card"></i>&nbsp;&nbsp;';
      o[p++] = UPDATE_PAYMENT_CARD_TEXT;
      o[p++] = '</button></td></tr>';
    }
    
    // Access code.
    if ((subscriptions[i][c.sub.STATUS] === st.sub.ONGOING) || (subscriptions[i][c.sub.STATUS] === st.sub.CANCELLED))
    {
      o[p++] = '<tr><td class="subscription-caption">';
      o[p++] = getText(12, 'Adgangskode');
      o[p++] = '</td><td class="subscription-data">';
      if ((subscriptions[i][c.sub.ACCESS_CODE] === '') &&
        (subscriptions[i][c.sub.ACCESS_LINK] === ''))
        o[p++] = getText(13, 'Lås ikke aktiv');
      else
      {
        if (subscriptions[i][c.sub.ACCESS_CODE] !== '')
          o[p++] = subscriptions[i][c.sub.ACCESS_CODE];
        if (subscriptions[i][c.sub.ACCESS_LINK] !== '')
        {
          o[p++] = '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.open(\'';
          o[p++] = subscriptions[i][c.sub.ACCESS_LINK];
          o[p++] = '\', \'_blank\');"><i class="fa-solid fa-key"></i>&nbsp;&nbsp;';
          o[p++] = getText(35, 'Åpne');
          o[p++] = '</button>';
        }
      }
      o[p++] = '</td></tr>';
    }
    // End of content. End of subscription box.
    o[p++] = '</tbody></table></div></div>';
  }
  // Text to say there are no subscriptions.
  if (subscriptions.length <= 0)
  {
    o[p++] = '<div class="button-box"><div class="form-element">';
    o[p++] = getText(15, 'Velkommen som bruker av Gibbs minilager! Du har ingen lagerboder i øyeblikket. Hvis du allerede har sendt forespørsel, er det bare å vente til vi kontakter deg. Hvis ikke, klikk Bestill lagerbod for å komme i gang.');
    o[p++] = '</div></div>';
  }
  // Button to book subscriptions.
  o[p++] = '<div class="create-subscription-button-box"><button type="button" class="wide-button" onclick="Utility.displaySpinnerThenGoTo(\'/subscription/html/select_booking_type.php\');"><i class="fa-solid fa-boxes-stacked"></i>&nbsp;&nbsp;';
  o[p++] = getText(14, 'Bestill lagerbod');
  o[p++] = '</button></div>';

  subscriptionsBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// *** Cancel subscription functions.
// *************************************************************************************************
// Display a dialogue to allow the user to delete the subscription with the given index in the
// subscriptions table.
function displayCancelSubscriptionDialogue(index)
{
  var o, p;

  // Write the cancel subscription dialogue contents.
  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return;
  cancellingIndex = index;
  o = new Array(3);
  p = 0;

  // Display confirmation dialogue with correct information, depending on today's date.
  o[p++] = '<div class="form-element">';
  if (Utility.canCancelThisMonth())
    o[p++] = getText(0,
      'Er du sikker på at du vil si opp $1? Du beholder lagerboden til og med siste dag i inneværende måned.',
      [subscriptions[index][c.sub.PRODUCT_NAME]]);
  else
    o[p++] = getText(1,
      'Er du sikker på at du vil si opp $1? Du trekkes for neste måned, og beholder lagerboden til og med siste dag neste måned.',
      [subscriptions[index][c.sub.PRODUCT_NAME]]);
  o[p++] = '</div>';
  cancelSubscriptionDialogueContent.innerHTML = o.join('');

  // Display the cancel subscription dialogue.
  Utility.display(overlay);
  Utility.display(cancelSubscriptionDialogue);
}

// *************************************************************************************************
// Cancel the subscription with the index stored in cancellingIndex.
function cancelSubscription()
{
  var o, p;

  if (cancellingIndex >= 0)
  {
    o = new Array(3);
    p = 0;

    o[p++] = '<form id="cancelSubscriptionForm" action="/subscription/html/user_dashboard.php" method="post"><input type="hidden" name="action" value="cancel_subscription" />';
    o[p++] = Utility.getHidden('id', subscriptions[cancellingIndex][c.sub.ID]);
    o[p++] = '</form>';
    cancelSubscriptionDialogueContent.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('cancelSubscriptionForm'));
  }
}

// *************************************************************************************************
// Close the cancel subscription dialogue.
function closeCancelSubscriptionDialogue()
{
  Utility.hide(cancelSubscriptionDialogue);
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
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, subscriptions))
  {
    // See if the payment history for this subscription is already available. If so, display it.
    if (subscriptions[index][c.sub.PAYMENT_HISTORY] !== null)
      displayPaymentHistory(index);
    else
    {
      // Fetch the payment history from the server, then store and display it.
      o = new Array(5);
      p = 0;
      o[p++] = '<div class="dialogue-header"><h2>';
      o[p++] = getText(16, 'Ordrehistorikk');
      o[p++] = '</h2></div><div class="dialogue-content"><div class="form-element">';
      o[p++] = getText(17, 'Laster ordrehistorikk. Vennligst vent...');
      o[p++] = '</div></div><div class="dialogue-footer"></div>';
      paymentHistoryDialogue.innerHTML = o.join('');
      Utility.display(overlay);
      Utility.display(paymentHistoryDialogue);
      fetch('/subscription/json/payment_history.php?subscription_id=' +
        String(subscriptions[index][c.sub.ID]))
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
          subscriptions[index][c.sub.PAYMENT_HISTORY] = data.paymentHistory;
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
// Display the payment history and price plans for the subscription with the given index in the
// subscriptions table. These are all intertwined, in order to give a single timeline of changes.
// The displayed list of payments and price changes is sorted with the most recent event on top.
// Price changes apply on the day they are introduced, and are thus displayed below any payments
// that occurred on the same day. This method assumes that the payment history has been loaded, and
// is available in the subscriptions table.
function displayPaymentHistory(index)
{
  var o, p, data, item;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return;

  data = new PaymentHistoryData(subscriptions, index);
  o = new Array(data.itemCount + 16);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header payment-history-header"><button type="button" class="low-profile close-button" onclick="closePaymentHistoryDialogue();"><i class="fa-solid fa-xmark"></i></button><h2>';
  o[p++] = getText(16, 'Ordrehistorikk');
  o[p++] = '</h2>';
  o[p++] = Utility.getLocationName(subscriptions[index][c.sub.LOCATION_ID]);
  o[p++] = ', '
  o[p++] = subscriptions[index][c.sub.PRODUCT_NAME];
  o[p++] = '</div>';
  // Content.
  o[p++] = '<div class="dialogue-content list-background">';
  // Display subscription end date, if it has one.
  if (data.hasEndDate)
  {
    o[p++] = '<div class="button-box"><div class="form-element">';
    o[p++] = data.subscriptionEndDate;
    o[p++] = getText(18, ': Abonnementet avsluttet');
    o[p++] = '</div></div>';
  }
  // Payments and price changes. The getNextItem method will return null when all items have been
  // displayed.
  while (true)
  {
    item = getNextItem(data);
    if (item === null)
      break;
    o[p++] = item;
  }
  // End of content.
  o[p++] = '</div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="closePaymentHistoryDialogue();"><i class="fa-solid fa-xmark"></i>&nbsp;&nbsp;';
  o[p++] = getText(19, 'Lukk');
  o[p++] = '</button></div>';

  paymentHistoryDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(paymentHistoryDialogue);
}

// *************************************************************************************************
// Inspect both the two price plans and the orders list, and choose the newest undisplayed item for
// display. The orders list is sorted in descending order, so we examine the first undisplayed item
// in the list. Price plans are sorted in ascending order, so we examine the last undisplayed item.
// Return null when there are no more items to be displayed. Otherwise, return a string with HTML
// code to display the next item.
function getNextItem(data)
{
  var itemType;

  itemType = data.getNextItemType();
  switch (itemType)
  {
    case PaymentHistoryData.DISPLAY_PAYMENT:
      return getPayment(data.nextItem);

    case PaymentHistoryData.DISPLAY_INITIAL_BOOKING:
      return getInitialBooking(data.nextItem.rentPricePlanLine,
        data.nextItem.insurancePricePlanLine);

    case PaymentHistoryData.DISPLAY_INITIAL_RENT:
    case PaymentHistoryData.DISPLAY_RENT_PRICE_CHANGE:
      return getRentEntry(data.nextItem, itemType === PaymentHistoryData.DISPLAY_INITIAL_RENT);

    case PaymentHistoryData.DISPLAY_INITIAL_INSURANCE:
    case PaymentHistoryData.DISPLAY_INSURANCE_PRICE_CHANGE:
      return getInsuranceEntry(data.nextItem,
        itemType === PaymentHistoryData.DISPLAY_INITIAL_INSURANCE);
  }
  // The display data object returned DISPLAY_NOTHING, or some other value.
  return null;
}

// *************************************************************************************************
// Return HTML code to display a box that contains information about the given payment.
function getPayment(payment)
{
  var o, p;

  o = new Array(32);
  p = 0;

  // Payment.
  o[p++] = '<div class="button-box">';
  // Payment header.
  o[p++] = '<div class="button-box-left payment-history-left"><h3>';
  o[p++] = payment[c.pay.NAME];
  o[p++] = '</h3></div>';
  // Payment content.
  o[p++] = '<div class="button-box-bottom payment-history-bottom"><table cellspacing="0" cellpadding="0"><tbody>';
  // Status.
  o[p++] = '<tr><td class="payment-history-caption">';
  o[p++] = getText(6, 'Status');
  o[p++] = '</td><td class="subscription-data">';
  o[p++] = Utility.getStatusLabel(PAYMENT_STATUS_TEXTS, PAYMENT_STATUS_COLOURS,
    payment[c.pay.PAYMENT_STATUS]);
  o[p++] = '</td></tr>';
  // Invoice number.
  o[p++] = '<tr><td class="payment-history-caption">';
  o[p++] = getText(20, 'Fakturanummer');
  o[p++] = '</td><td class="subscription-data">';
  o[p++] = String(payment[c.pay.ID]);
  o[p++] = '</td></tr>';
  // Payment method.
  o[p++] = '<tr><td class="payment-history-caption">';
  o[p++] = getText(21, 'Betalingsmåte');
  o[p++] = '</td><td class="subscription-data">';
  o[p++] = PAYMENT_METHOD_TEXTS[payment[c.pay.PAYMENT_METHOD]];
  o[p++] = '</td></tr>';
  // Order date.
  o[p++] = '<tr><td class="payment-history-caption">';
  o[p++] = getText(22, 'Utstedt');
  o[p++] = '</td><td class="subscription-data">';
  o[p++] = payment[c.pay.ORDER_DATE];
  o[p++] = '</td></tr>';
  // Pay by date.
  o[p++] = '<tr><td class="payment-history-caption">';
  o[p++] = getText(23, 'Forfallsdato');
  o[p++] = '</td><td class="subscription-data">';
  o[p++] = payment[c.pay.PAY_BY_DATE];
  o[p++] = '</td></tr></tbody></table>';
  // Order lines.
  o[p++] = getOrderLines(payment);
  // End of payment content. End of payment.
  o[p++] = '</div></div>';
  return o.join('');
}

// *************************************************************************************************
// Return HTML code for a div tag that contains a table that displays order lines for the given
// payment.
function getOrderLines(payment)
{
  var o, p, i, orderLines, amount;

  orderLines = payment[c.pay.ORDER_LINES];
  o = new Array((orderLines.length * 6) + 11);
  p = 0;

  // Header row.
  o[p++] = '<div class="payment-details"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(24, 'Ordrelinjer');
  o[p++] = '</th><th>';
  o[p++] = getText(25, 'Beløp');
  o[p++] = '</th></tr></thead><tbody>';
  // Order lines.
  amount = 0;
  for (i = 0; i < orderLines.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = orderLines[i][c.pay.LINE_TEXT];
    o[p++] = '</td><td class="currency">';
    o[p++] = String(orderLines[i][c.pay.LINE_AMOUNT]);
    amount += orderLines[i][c.pay.LINE_AMOUNT];
    o[p++] = getText(9, ' kr');
    o[p++] = '</td></tr>';
  }
  // Sum.
  o[p++] = '<tr><td class="sum">';
  o[p++] = getText(26, 'Sum');
  o[p++] = '</td><td class="sum currency">';
  o[p++] = String(amount);
  o[p++] = getText(9, ' kr');
  o[p++] = '</td></tr></tbody></table></div>';
  return o.join('');
}

// *************************************************************************************************
// Return HTML code to display a price plan line that describes a change to the rent price.
function getRentEntry(pricePlanLine, isInitialBooking)
{
  var o, p;

  o = new Array(10);
  p = 0;

  // Rent price change caption.
  o[p++] = '<div class="button-box"><div class="form-element">';
  o[p++] = pricePlanLine[c.sub.LINE_START_DATE];
  if (isInitialBooking)
    o[p++] = getText(27, ': Bestilt lagerbod');
  else
    o[p++] = getText(28, ': Prisendring, lagerbod');
  o[p++] = '<br>&nbsp;&nbsp;&nbsp;&nbsp;';
  // New price.
  if (isInitialBooking)
    o[p++] = getText(29, 'Pris: ');
  else
    o[p++] = getText(30, 'Ny pris: ');
  o[p++] = String(pricePlanLine[c.sub.LINE_PRICE]);
  o[p++] = getText(9, ' kr');
  // Description.
  o[p++] = ' (';
  o[p++] = pricePlanLine[c.sub.LINE_DESCRIPTION];
  o[p++] = ')</div></div>';
  return o.join('');
}

// *************************************************************************************************
// Return HTML code to display a price plan line that describes a change to the insurance price.
function getInsuranceEntry(pricePlanLine, isInitialBooking)
{
  var o, p;

  o = new Array(10);
  p = 0;

  // Insurance price change caption.
  o[p++] = '<div class="button-box"><div class="form-element">';
  o[p++] = pricePlanLine[c.sub.LINE_START_DATE];
  if (isInitialBooking)
    o[p++] = getText(31, ': Bestilt forsikring');
  else
    o[p++] = getText(32, ': Prisendring, forsikring');
  o[p++] = '<br>&nbsp;&nbsp;&nbsp;&nbsp;';
  // New price.
  if (isInitialBooking)
    o[p++] = getText(29, 'Pris: ');
  else
    o[p++] = getText(30, 'Ny pris: ');
  o[p++] = String(pricePlanLine[c.sub.LINE_PRICE]);
  o[p++] = getText(9, ' kr');
  // Description.
  o[p++] = ' (';
  o[p++] = pricePlanLine[c.sub.LINE_DESCRIPTION];
  o[p++] = ')</div></div>';
  return o.join('');
}

// *************************************************************************************************
// Return HTML code to display two price plan lines that represent the initial subscription booking.
function getInitialBooking(rentPricePlanLine, insurancePricePlanLine)
{
  var o, p;

  o = new Array(16);
  p = 0;

  // Rent price change caption.
  o[p++] = '<div class="button-box"><div class="form-element">';
  o[p++] = rentPricePlanLine[c.sub.LINE_START_DATE];
  o[p++] = getText(27, ': Bestilt lagerbod');
  o[p++] = '<br>&nbsp;&nbsp;&nbsp;&nbsp;';
  // Rent.
  o[p++] = getText(33, 'Leie: ');
  o[p++] = String(rentPricePlanLine[c.sub.LINE_PRICE]);
  o[p++] = getText(9, ' kr');
  // Rent description.
  o[p++] = ' (';
  o[p++] = rentPricePlanLine[c.sub.LINE_DESCRIPTION];
  o[p++] = ')<br>&nbsp;&nbsp;&nbsp;&nbsp;';
  // Insurance.
  o[p++] = getText(34, 'Forsikring: ');
  o[p++] = String(insurancePricePlanLine[c.sub.LINE_PRICE]);
  o[p++] = getText(9, ' kr');
  // Insurance description.
  o[p++] = ' (';
  o[p++] = insurancePricePlanLine[c.sub.LINE_DESCRIPTION];
  o[p++] = ')</div></div>';
  return o.join('');
}

// *************************************************************************************************
// Close the payment history dialogue.
function closePaymentHistoryDialogue()
{
  Utility.hide(paymentHistoryDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// *** Payment history data class.
// *************************************************************************************************
// The PaymentHistoryData gathers the information to be displayed in the payment history dialogue.
// This includes the list of orders (payments), and the price plans for rent and insurance. These
// need to be intertwined, in order to give a single timeline of changes.
//
// This class will ensure that the displayed list of payments and price changes is sorted with the
// most recent event on top. Price changes apply on the day they are introduced, and are thus
// displayed below any payments that occurred on the same day.
//
// Note that price changes that occur after the end of the subscription will not be displayed.
class PaymentHistoryData
{

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// Constants to say which type of item, if any, should be displayed next.
static DISPLAY_NOTHING = -1;
static DISPLAY_PAYMENT = 0;
static DISPLAY_RENT_PRICE_CHANGE = 1;
static DISPLAY_INITIAL_RENT = 2;
static DISPLAY_INSURANCE_PRICE_CHANGE = 3;
static DISPLAY_INITIAL_INSURANCE = 4;
static DISPLAY_INITIAL_BOOKING = 5;

// *************************************************************************************************
// *** Constructors.
// *************************************************************************************************

constructor(subscriptions, index)
{
  // The subscription's end date, if any, is stored to prevent price plan entries that occur after
  // that date from being displayed.
  this._subscriptionEndDate = subscriptions[index][c.sub.END_DATE];
  this._hasEndDate = this._subscriptionEndDate !== '';

  // Initialise payment history fields. The counter points to the start of the list, which is sorted
  // with most recent events first.
  this._paymentHistory = subscriptions[index][c.sub.PAYMENT_HISTORY];
  this._hasPayments = this._paymentHistory !== null;
  this._nextPaymentIndex = (this._hasPayments ? 0 : -1);

  // Initialise rent price plan fields. The counter points to the end of the list, which is sorted
  // from first to last. We want to display the last entry at the top, so we start at the end.
  this._rentPricePlanLines = PricePlan.getPricePlanLines(subscriptions, index,
    PricePlan.getProductPricePlan(subscriptions, index));
  this._hasRent = this._rentPricePlanLines !== null;
  this._nextRentPriceChangeIndex = this._getMostRecentDisplayableRentPriceChangeIndex();

  // Initialise insurance price plan fields. The counter points to the end of the list, which is
  // sorted from first to last. We want to display the last entry at the top, so we start at the
  // end.
  this._insurancePricePlanLines = PricePlan.getPricePlanLines(subscriptions, index,
    PricePlan.getInsurancePricePlan(subscriptions, index));
  this._hasInsurance = this._insurancePricePlanLines !== null;
  this._nextInsurancePriceChangeIndex = this._getMostRecentDisplayableInsurancePriceChangeIndex();

  // Initialise the allDisplayed flag to be true if we didn't find anything to display at all. If
  // _allDisplayed is set, that means there is nothing more to be displayed, and the list is
  // complete.
  this._allDisplayed = !(this._hasPayments || this._hasRent || this._hasInsurance);

  // The next item to be displayed, or null if it has not yet been calculated. If the next item type
  // is DISPLAY_INITIAL_BOOKING, this field will hold an object with the following fields:
  //   rentPricePlanLine
  //   insurancePricePlanLine
  // Otherwise, the field will hold either a payment or a price plan line.
  this._nextItem = null;
  // Flag that says whether _nextItem contains the first entry in a price plan, representing the
  // initial booking.
  this._isInitialBooking = false;
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Return the next type of item to be displayed (payment, rent price change or insurance price
// change), using the DISPLAY_ constants in this class.
getNextItemType()
{
  var paymentDate, rentDate, insuranceDate;

  // See if we are already finished.
  if (this._getAllDisplayed())
    return PaymentHistoryData.DISPLAY_NOTHING;

  // We're not finished. Figure out what to display next. Find the dates of the next payments and
  // price plan entries, if there are any.
  paymentDate = this._getNextPaymentDate();
  rentDate = this._getNextRentDate();
  insuranceDate = this._getNextInsuranceDate();

  // In a day, price plan changes always occur first, and then payments use the updated price.
  // We are displaying newest first, so display payments before price plan changes.
  if (!this._hasFinishedPayments() && (paymentDate >= rentDate) && (paymentDate >= insuranceDate))
  {
    this._nextItem = this._getNextPayment();
    return PaymentHistoryData.DISPLAY_PAYMENT;
  }

  // The payment should not be displayed. See if we should display an insurance price change.
  // Insurance happens as a result of the subscription, so its events are displayed above.
  if (!this._hasFinishedRent() && (insuranceDate >= rentDate))
  {
    this._nextItem = this._getNextInsurancePriceChange();
    if (this._isInitialBooking)
    {
      // This is the initial insurance price. If we are also ready to display the initial rent
      // price, combine the two.
      if (this._nextRentPriceChangeIndex === 0)
      {
        this._nextItem =
          {
            rentPricePlanLine: this._getNextRentPriceChange(),
            insurancePricePlanLine: this._nextItem
          };
        return PaymentHistoryData.DISPLAY_INITIAL_BOOKING;
      }
      return PaymentHistoryData.DISPLAY_INITIAL_INSURANCE;
    }
    return PaymentHistoryData.DISPLAY_INSURANCE_PRICE_CHANGE;
  }

  // None of the others take precedence. Display rent price change.
  this._nextItem = this._getNextRentPriceChange();
  if (this._isInitialBooking)
    return PaymentHistoryData.DISPLAY_INITIAL_RENT;
  return PaymentHistoryData.DISPLAY_RENT_PRICE_CHANGE;
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Return the index in the _rentPricePlanLines table of the most recent price change that will
// actually be displayed. Price changes that occur after the subscription has ended will not be
// displayed.
_getMostRecentDisplayableRentPriceChangeIndex()
{
  var index;

  // If we don't have a rent price plan, return -1 to signify that.
  if (!this._hasRent)
    return -1;

  // Use the last entry in the price plan.
  index = this._rentPricePlanLines.length - 1;

  // If the subscription ends, skip any changes that occur after the end date.
  if (this._hasEndDate)
  {
    while ((index >= 0) &&
      (this._rentPricePlanLines[index][c.sub.LINE_START_DATE] >= this._subscriptionEndDate))
      index--;
  }
  return index;
}

// *************************************************************************************************
// Return the index in the _insurancePricePlanLines table of the most recent price change that will
// actually be displayed. Price changes that occur after the subscription has ended will not be
// displayed.
_getMostRecentDisplayableInsurancePriceChangeIndex()
{
  var index;

  // If we don't have an insurance price plan, return -1 to signify that.
  if (!this._hasInsurance)
    return -1;

  // Use the last entry in the price plan.
  index = this._insurancePricePlanLines.length - 1;

  // If the subscription ends, skip any changes that occur after the end date.
  if (this._hasEndDate)
  {
    while ((index >= 0) &&
      (this._insurancePricePlanLines[index][c.sub.LINE_START_DATE] >= this._subscriptionEndDate))
      index--;
  }
  return index;
}

// *************************************************************************************************
// Return true if the payment history has been completely displayed. This is the case if there is no
// table (the _hasPayments flag is false), or if the _nextPaymentIndex index is beyond the end of the
// _paymentHistory table.
_hasFinishedPayments()
{
  return !this._hasPayments || (this._nextPaymentIndex >= this._paymentHistory.length);
}

// *************************************************************************************************
// Return true if the rent price plan has been completely displayed. This is the case if there is no
// table (the _hasRent flag is false), or if the _nextRentPriceChangeIndex index is before the start
// of the _rentPricePlanLines table.
_hasFinishedRent()
{
  return !this._hasRent || (this._nextRentPriceChangeIndex < 0);
}

// *************************************************************************************************
// Return true if the insurance price plan has been completely displayed. This is the case if there
// is no table (the _hasInsurance flag is false), or if the _nextInsurancePriceChangeIndex index is
// before the start of the _insurancePricePlanLines table.
_hasFinishedInsurance()
{
  return !this._hasInsurance || (this._nextInsurancePriceChangeIndex < 0);
}

// *************************************************************************************************
// Return the next payment date, if any. The value is a string with a date in "yyyy-mm-dd" format,
// or an empty string if there are no more payments to be displayed.
_getNextPaymentDate()
{
  if (this._hasFinishedPayments())
    return '';
  return this._paymentHistory[this._nextPaymentIndex][c.pay.PAY_BY_DATE];
}

// *************************************************************************************************
// Return the next rent date, if any. The value is a string with a date in "yyyy-mm-dd" format, or
// an empty string if there are no more rent price plan elements to be displayed.
_getNextRentDate()
{
  if (this._hasFinishedRent())
    return '';
  return this._rentPricePlanLines[this._nextRentPriceChangeIndex][c.sub.LINE_START_DATE];
}

// *************************************************************************************************
// Return the next insurance date, if any. The value is a string with a date in "yyyy-mm-dd" format,
// or an empty string if there are no more insurance price plan elements to be displayed.
_getNextInsuranceDate()
{
  if (this._hasFinishedInsurance())
    return '';
  return this._insurancePricePlanLines[this._nextInsurancePriceChangeIndex][c.sub.LINE_START_DATE];
}

// *************************************************************************************************
// Return the next payment to be displayed, and update the counter to reflect the fact that it has
// been displayed. This method assumes that there is another item to be displayed.
_getNextPayment()
{
  var index;

  index = this._nextPaymentIndex;
  this._nextPaymentIndex++;
  this._isInitialBooking = false;
  return this._paymentHistory[index];
}

// *************************************************************************************************
// Return the next line to be displayed in the rent price plan, and update the counter to reflect
// the fact that it has been displayed. This method assumes that there is another item to be
// displayed.
_getNextRentPriceChange()
{
  var index;

  index = this._nextRentPriceChangeIndex;
  this._nextRentPriceChangeIndex--;
  this._isInitialBooking = index === 0;
  return this._rentPricePlanLines[index];
}

// *************************************************************************************************
// Return the next line to be displayed in the insurance price plan, and update the counter to
// reflect the fact that it has been displayed. This method assumes that there is another item to be
// displayed.
_getNextInsurancePriceChange()
{
  var index;

  index = this._nextInsurancePriceChangeIndex;
  this._nextInsurancePriceChangeIndex--;
  this._isInitialBooking = index === 0;
  return this._insurancePricePlanLines[index];
}

// *************************************************************************************************
// Return true if all payments and price plan changes have already been displayed. The method
// returns true if we already know that everything has been displayed, or if it finds that we have
// now displayed everyting in all the tables.
_getAllDisplayed()
{
  // If we already know, let the caller know.
  if (this._allDisplayed)
    return true;

  // See if we're now done, which occurs when we have displayed everyting in all the tables.
  this._allDisplayed = this._hasFinishedPayments() && this._hasFinishedRent() &&
    this._hasFinishedInsurance();
  return this._allDisplayed;
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the itemCount property. This is the total number of payments and price plan entries. Note
// that this method does not take into account the fact that some price plan entries may not be
// displayed due to being after the subscription's end date.
get itemCount()
{
  return (this._hasPayments ? this._paymentHistory.length : 0) +
    (this._hasRent ? this._rentPricePlanLines.length : 0) +
    (this._hasInsurance ? this._insurancePricePlanLines.length : 0);
}

// *************************************************************************************************
// Return true if the subscription has an end date.
get hasEndDate()
{
  return this._hasEndDate;
}

// *************************************************************************************************
// Return the subscriptionEndDate property. If the subscription has no end date, the value will be
// an empty string.
get subscriptionEndDate()
{
  return this._subscriptionEndDate;
}

// *************************************************************************************************
// Return the nextItem property. The value will be null until the next item is calculated using
// getNextItemType.
get nextItem()
{
  return this._nextItem;
}

// *************************************************************************************************

}

// *************************************************************************************************
// *** Update payment card functions
// *************************************************************************************************
// Redirect to the update payment card page for the subscription with the given index
function updatePaymentCard(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, subscriptions))
  {
    const subscriptionId = subscriptions[index][c.sub.ID];
    const netsSubscriptionId = subscriptions[index][c.sub.NETS_SUBSCRIPTION_ID];
    
    if (netsSubscriptionId) {
      Utility.displaySpinnerThenGoTo('/subscription/html/update_payment_card.php?subscription_id=' + 
        encodeURIComponent(subscriptionId) + '&nets_subscription_id=' + encodeURIComponent(netsSubscriptionId));
    }
  }
}
