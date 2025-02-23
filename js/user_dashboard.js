// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var subscriptionsBox, expiredSubscriptionsCheckbox, expiredSubscriptionsLine, overlay,
  pricePlanDialogue, paymentHistoryDialogue;

// Array of information about the maps displayed in the list of subscriptions. The array has an
// entry for each location displayed in the list. Each entry is an object with the following format:
//   {
//     index : integer
//     id : string
//     map : GibbsLeafletMap
//   }
// index is the index of the location in the locations table. id is the ID of the HTML element that
// will hold the map. map is the GibbsLeafletMap instance that displays the map of that location in
// the list of subscriptions.
var maps = [];

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and displaying subscriptions.
function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['subscriptionsBox', 'expiredSubscriptionsCheckbox',
    'expiredSubscriptionsLine', 'overlay', 'pricePlanDialogue', 'paymentHistoryDialogue']);

  expiredSubscriptionsCheckbox.checked = displayExpiredSubscriptions;
  displaySubscriptions();

  // Display the results of a previous operation, if required.
  if (resultCode === result.PRODUCT_ALREADY_BOOKED)
    alert(getText(23,
      'Beklager! Alle lagerbodene av typen du valgte er nå bestilt. Vennligst prøv igjen!'));
  else
    if (resultCode >= 0)
      alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
        [String(resultCode)]));
}

// *************************************************************************************************
// Display a confirmation dialogue asking the user to cancel the subscription with the given ID. The
// confirmation will display appropriate information on when the subscription will end, depending on
// the current date. If the user confirms, ask the server to cancel the subscription.
//
// The information presented to the user depends on the time and date being set correctly on the
// client. However, the server has the last word on when the subscription actually ends.
function cancelSubscription(id)
{
  var index, approved, o, p;

  index = Utility.getSubscriptionIndex(id);
  if (index >= 0)
  {
    // Display confirmation dialogue with correct information, depending on today's date.
    if (Utility.canCancelThisMonth())
      approved = confirm(getText(0,
        'Er du sikker på at du vil si opp $1? Du beholder lagerboden til og med siste dag i inneværende måned.',
        [subscriptions[index][c.sub.NAME]]));
    else
      approved = confirm(getText(14,
        'Er du sikker på at du vil si opp $1? Du trekkes for neste måned, og beholder lagerboden til og med siste dag neste måned.',
        [subscriptions[index][c.sub.NAME]]));
    if (approved)
    {
      o = new Array(3);
      p = 0;

      o[p++] = '<form id="cancelSubscriptionForm" action="/subscription/html/user_dashboard.php" method="post"><input type="hidden" name="action" value="cancel_subscription" /><input type="hidden" name="id" value="';
      o[p++] = String(subscriptions[index][c.sub.ID]);
      o[p++] = '" /></form>';
      paymentHistoryDialogue.innerHTML = o.join('');
      document.getElementById('cancelSubscriptionForm').submit();
    }
  }
}

// *************************************************************************************************
// Display or hide subscriptions that have been cancelled, depending on the current setting.
function toggleExpiredSubscriptions()
{
  displayExpiredSubscriptions = expiredSubscriptionsCheckbox.checked;
  displaySubscriptions();
}

// *************************************************************************************************
// Return true if all of the user's subscriptions are expired. If the user has no subscriptions, the
// method will return false.
function allSubscriptionsExpired()
{
  var i;

  if (subscriptions.length <= 0)
    return false;
  for (i = 0; i < subscriptions.length; i++)
  {
    if (subscriptions[i][c.sub.STATUS] !== st.sub.EXPIRED)
      return false;
  }
  return true;
}

// *************************************************************************************************
// Return a Javascript array of indexes (into the subscriptions table) of subscriptions at the
// location with the given index (in the locations table). If displayExpiredSubscriptions is
// false, expired subscriptions will not be included in the returned array.
function listSubscriptionsAtLocation(locationId)
{
  var i, result;

  result = [];
  for (i = 0; i < subscriptions.length; i++)
  {
    // If the subscription is at the correct location, and the subscription is either not expired,
    // or we want to include expired subscriptions, add the subscription index to the list of
    // indexes to be displayed.
    if ((subscriptions[i][c.sub.LOCATION_ID] === locationId) &&
      ((subscriptions[i][c.sub.STATUS] !== st.sub.EXPIRED) || displayExpiredSubscriptions))
      result.push(i);
  }
  return result;
}

// *************************************************************************************************
// Display all of the user's subscriptions. Cancelled subscriptions may be concealed, depending on
// the displayExpiredSubscriptions flag. Subscriptions are grouped by location, and information
// about each location will be displayed. This includes a map.
function displaySubscriptions()
{
  var o, p, i;
  
  // If the user has no subscriptions, active or inactive, just display a message to that effect.
  if (subscriptions.length <= 0)
  {
    subscriptionsBox.innerHTML = '<div class="form-element"><p>' +
      getText(9, 'Velkommen som bruker av Gibbs minilager! Du har ingen lagerboder i &oslash;yeblikket. Klikk Bestill lagerbod for &aring; komme i gang.') + '</p></div>'
    Utility.hide(expiredSubscriptionsLine);
    return;
  }
  if (!displayExpiredSubscriptions && allSubscriptionsExpired())
  {
    subscriptionsBox.innerHTML = '<div class="form-element"><p>' +
      getText(8, 'Alle dine avtaler er avsluttet. Kryss av i boksen for &quot;avsluttede avtaler&quot; for &aring; vise dem.') +
      '</p></div>'
    Utility.display(expiredSubscriptionsLine);
    return;
  }

  // The user has subscriptions. Note that none may be displayed, if the user has opted to not
  // display expired subscriptions.
  Utility.display(expiredSubscriptionsLine);

  // Clear the list of map components. The code generated below will replace it.
  maps = [];
  
  // Display subscriptions for each location in turn.
  o = new Array(locations.length);
  p = 0;

  for (i = 0; i < locations.length; i++)
    o[p++] = displayLocationSubscriptions(i);
  subscriptionsBox.innerHTML = o.join('');

  // Create all required maps.
  for (i = 0; i < maps.length; i++)
  {
    maps[i].map = new GibbsLeafletMap(maps[i].id);
    maps[i].map.displayAddress(Utility.getAddress(locations[maps[i].index]));
  }
}

// *************************************************************************************************
// Return HTML code to display all of the user's subscriptions at the location with the given index
// in the locations table. Cancelled subscriptions may be concealed, depending on the
// displayExpiredSubscriptions flag. If the user has no displayable subscriptions at the given
// location, return an empty string.
function displayLocationSubscriptions(index)
{
  var o, p, subscriptionIndexes;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, locations))
    return '';

  // See if the user has displayable subscriptions.
  subscriptionIndexes = listSubscriptionsAtLocation(locations[index][c.loc.ID]);
  if (subscriptionIndexes.length <= 0)
    return '';

  // Add an entry to the list of maps, to signal that a map must be created here.
  mapId = 'mapOfLocation' + String(index);
  maps.push({index: index, id: mapId, map: null});

  // Display location information, including a placeholder for the map.
  o = new Array(25);
  p = 0;

  o[p++] = '<div class="location-box"><div class="column-container"><div class="column"><div id="';
  o[p++] = mapId;
  o[p++] = '" class="map">&nbsp;</div></div><div class="column"><table cellspacing="0" cellpadding="0"><thead><tr><th colspan="2"><h3>';
  o[p++] = locations[index][c.loc.NAME];
  o[p++] = '</h3></th></tr></thead><tbody><tr><td>';
  o[p++] = getText(11, 'Adresse:');
  o[p++] = '</td><td>';
  o[p++] = Utility.getAddress(locations[index]);
  o[p++] = '</td></tr><tr><td>';
  o[p++] = getText(12, '&Aring;pningstider:');
  o[p++] = '</td><td>';
  o[p++] = locations[index][c.loc.OPENING_HOURS];
  o[p++] = '</td></tr><tr><td>';
  o[p++] = getText(13, 'Tjenester:');
  o[p++] = '</td><td>';
  o[p++] = locations[index][c.loc.SERVICES];
  o[p++] = '</td></tr>';
  if (locations[index][c.loc.ACCESS_CODE] !== '')
  {
    o[p++] = '<tr><td>';
    o[p++] = getText(2, 'Adgangskode:');
    o[p++] = '</td><td class="access-code">';
    o[p++] = locations[index][c.loc.ACCESS_CODE];
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table></div></div>';

  // Display subscriptions at this location.
  o[p++] = displaySubscriptionSet(subscriptionIndexes);
  o[p++] = '</div>';
  return o.join('');
}

// *************************************************************************************************
// Return HTML code to display a table of subscriptions. The indexes array holds the indexes in the
// subscriptions table of the subscriptions to be displayed.
function displaySubscriptionSet(indexes)
{
  var o, p, i, index;

  o = new Array((indexes.length * 23) + 18);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(3, 'Lagerbod');
  o[p++] = '</th><th>';
  o[p++] = getText(4, 'Bodtype');
  o[p++] = '</th><th>';
  o[p++] = getText(21, 'Status');
  o[p++] = '</th><th>';
  o[p++] = getText(5, 'Fra dato');
  o[p++] = '</th><th>';
  o[p++] = getText(6, 'Til dato');
  o[p++] = '</th><th>';
  o[p++] = getText(24, 'Forsikring');
  o[p++] = '</th><th>';
  o[p++] = getText(10, 'Betalingshistorikk');
  o[p++] = '</th><th>';
  o[p++] = getText(7, 'Si opp');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < indexes.length; i++)
  {
    index = parseInt(indexes[i], 10);
    if ((!Utility.isValidIndex(index, subscriptions)) ||
      (!displayExpiredSubscriptions && (subscriptions[index][c.sub.STATUS] === st.sub.EXPIRED)))
      continue;

    o[p++] = '<tr><td>';
    o[p++] = subscriptions[index][c.sub.NAME];
    o[p++] = getPriceButton(index, PricePlan.getProductPricePlan(subscriptions, index));
    o[p++] = '</td><td>';
    o[p++] = subscriptions[index][c.sub.PRODUCT_TYPE];
    o[p++] = '</td><td><span class="status-label ';
    o[p++] = st.sub.COLOURS[subscriptions[index][c.sub.STATUS]];
    o[p++] = '">';
    o[p++] = SUB_TEXTS[subscriptions[index][c.sub.STATUS]];
    o[p++] = '</span></td><td>';
    o[p++] = subscriptions[index][c.sub.START_DATE];
    o[p++] = '</td><td>';
    if (subscriptions[index][c.sub.END_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = subscriptions[index][c.sub.END_DATE];
    o[p++] = '</td><td>';
    if (subscriptions[index][c.sub.INSURANCE_NAME] === '')
      o[p++] = '&nbsp;';
    else
    {
      o[p++] = subscriptions[index][c.sub.INSURANCE_NAME];
      o[p++] = getPriceButton(index, PricePlan.getInsurancePricePlan(subscriptions, index));
    }
    o[p++] = '</td><td><button type="button" class="icon-button" onclick="loadPaymentHistory(';
    o[p++] = String(index);
    o[p++] = ');"><i class="fa-solid fa-file-invoice-dollar"></i></button></td><td>';
    if (subscriptions[index][c.sub.STATUS] === st.sub.ONGOING)
    {
      o[p++] = '<button type="button" class="icon-button" onclick="cancelSubscription(';
      o[p++] = subscriptions[index][c.sub.ID];
      o[p++] = ');"><i class="fa-solid fa-trash"></i></button>';
    }
    else  
      o[p++] = '&nbsp;';
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  return o.join('');
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
    o[p++] = getText(31, 'Pris');
  o[p++] = '</button>';
  return o.join('');
}

// *************************************************************************************************
// Open a dialogue box that displays the price plan with the given pricePlanIndex, for the
// subscription with the given index.
function displayPricePlan(index, pricePlanIndex)
{
  var o, p, i, planType, planLines;

  planLines = PricePlan.getPricePlanLines(subscriptions, index, pricePlanIndex);
  if (planLines === null)
    return;
  o = new Array((planLines.length * 9) + 19);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  planType = subscriptions[index][c.sub.PRICE_PLANS][pricePlanIndex][c.sub.PLAN_TYPE];
  if (planType < 0)
    o[p++] = getText(32, 'Prishistorikk, leie');
  else
    o[p++] = getText(33, 'Prishistorikk, $1', [ADDITIONAL_PRODUCT_TEXTS[planType]]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(5, 'Fra dato');
  o[p++] = '</th><th>';
  o[p++] = getText(6, 'Til dato');
  o[p++] = '</th><th>';
  o[p++] = getText(31, 'Pris');
  o[p++] = '</th><th>';
  o[p++] = getText(35, 'Beskrivelse');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < planLines.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = planLines[i][c.sub.LINE_START_DATE];
    o[p++] = '</td><td>';
    o[p++] = getEndDate(planLines, i, subscriptions[index][c.sub.END_DATE]);
    o[p++] = '</td><td>';
    o[p++] = String(planLines[i][c.sub.LINE_PRICE]);
    o[p++] = ',-</td><td>';
    if (planLines[i][c.sub.LINE_DESCRIPTION] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = planLines[i][c.sub.LINE_DESCRIPTION];
    o[p++] = '</td></tr>';
  }
  if (subscriptions[index][c.sub.END_DATE] !== '')
  {
    o[p++] = '<tr><td>';
    o[p++] = subscriptions[index][c.sub.END_DATE];
    o[p++] = '</td><td>&nbsp;</td><td>&nbsp;</td><td>';
    o[p++] = getText(36, 'Abonnementet avsluttet');
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table></div><div class="dialogue-footer"><button type="button" onclick="closePricePlanDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(22, 'Lukk');
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
  if (index >= (planLines.length - 1))
  {
    // This is the last element in the price plan. If the subscription will, that's the end date.
    // Otherwise, the price applies until further notice.
    if (subscriptionEndDate !== '')
      return subscriptionEndDate;
    return getText(34, 'Inntil videre');
  }
  // The price ends the day before the next price in the price plan takes effect.
  return Utility.getDayBefore(planLines[index + 1][c.sub.LINE_START_DATE]);
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
    if (subscriptions[index][c.sub.PAYMENT_HISTORY] !== null)
      displayPaymentHistory(index);
    else
    {
      // Fetch the payment history from the server, then store and display it.
      paymentHistoryDialogue.innerHTML = '<p>' +
        getText(25, 'Laster betalingshistorikk. Vennligst vent...') + '</p>';
      Utility.display(overlay);
      Utility.display(paymentHistoryDialogue);
      fetch('/subscription/json/payment_history.php?subscription_id=' +
        String(subscriptions[index][c.sub.ID]))
        .then(Utility.extractJson)
        .catch(logPaymentHistoryError)
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
    if (data.resultCode >= 0)
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
// Display the payment history for the subscription with the given index in the subscriptions table.
// This method assumes that the payment history is available in the subscriptions table.
function displayPaymentHistory(index)
{
  var o, p, i, paymentHistory, style, amount;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return;
  paymentHistory = subscriptions[index][c.sub.PAYMENT_HISTORY];
  o = new Array((paymentHistory.length * 38) + 20);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(15, 'Betalingshistorikk for $1, $2',
    [subscriptions[index][c.sub.NAME], Utility.getLocationName(subscriptions[index][c.sub.LOCATION_ID])]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>&nbsp;</th><th>';
  o[p++] = getText(16, 'Type');
  o[p++] = '</th><th>';
  o[p++] = getText(17, 'Fakturanr');
  o[p++] = '</th><th>';
  o[p++] = getText(18, 'Betalingsm&aring;te');
  o[p++] = '</th><th>';
  o[p++] = getText(19, 'Utstedt');
  o[p++] = '</th><th>';
  o[p++] = getText(20, 'Forfallsdato');
  o[p++] = '</th><th>';
  o[p++] = getText(21, 'Status');
  o[p++] = '</th><th>';
  o[p++] = getText(29, 'Sum');
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
    o[p++] = getText(30, 'Ukjent');
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
    o[p++] = '><span class="status-label ';
    o[p++] = PAYMENT_STATUS_COLOURS[paymentHistory[i][c.pay.PAYMENT_STATUS]];
    o[p++] = '">';
    o[p++] = PAYMENT_STATUS_TEXTS[paymentHistory[i][c.pay.PAYMENT_STATUS]];
    o[p++] = '</span></td><td class="currency">';
    amount = getOrderAmount(index, i);
    o[p++] = String(amount);
    o[p++] = ',-</td></tr>';
    // Write table of order lines, if the user has opened the box.
    if (paymentHistory[i][c.pay.OPEN])
      o[p++] = getOrderLines(paymentHistory, i, amount);
  }
  o[p++] = '</tbody></table></div><div class="dialogue-footer"><button type="button" onclick="closePaymentHistoryDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(22, 'Lukk');
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
  o[p++] = getText(26, 'Beskrivelse');
  o[p++] = '</th><th>';
  o[p++] = getText(27, 'Produkt-ID');
  o[p++] = '</th><th>';
  o[p++] = getText(28, 'Bel&oslash;p');
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
  o[p++] = getText(29, 'Sum');
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
    paymentHistory = subscriptions[subscriptionIndex][c.sub.PAYMENT_HISTORY];

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
    paymentHistory = subscriptions[subscriptionIndex][c.sub.PAYMENT_HISTORY];

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
