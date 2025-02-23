// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var userInfoBox, subscriptionsFrame, subscriptionsBox, expiredSubscriptionsCheckbox, overlay,
  pricePlanDialogue, paymentHistoryDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var firstNameEdit, lastNameEdit, userNameEdit, phoneEdit, passwordEdit, submitButton;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['userInfoBox', 'subscriptionsFrame', 'subscriptionsBox',
   'expiredSubscriptionsCheckbox', 'overlay', 'pricePlanDialogue', 'paymentHistoryDialogue']);

  displayUserInfo();
  if (isNewUser)
    Utility.hide(subscriptionsFrame);
  else
  {
    Utility.display(subscriptionsFrame);
    displaySubscriptions();
  }

  // Display the results of a previous operation, if required.
  if (resultCode === result.PASSWORD_CHANGED)
    alert('Passordet ble endret. Husk å informere kunden.');
  else
    if (resultCode >= 0)
    {
      alert('Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode ' +
        String(resultCode) + '.');
    }
}

// *************************************************************************************************
// User info functions.
// *************************************************************************************************

function displayUserInfo()
{
  var o, p;

  o = new Array(28); // *** //
  p = 0;

  o[p++] = '<div class="toolbar"><h3>';
  if (isNewUser)
    o[p++] = 'Opprett bruker';
  else
    o[p++] = 'Rediger brukerinformasjon';
  o[p++] = '</h3></div><form action="/subscription/html/admin_edit_user.php" method="post">';
  if (!isNewUser)
  {
    o[p++] = '<input type="hidden" name="user_id" value="';
    o[p++] = String(user.id);
    o[p++] = '" />';
  }
  o[p++] = '<div class="form-element"><label for="firstNameEdit" class="standard-label">Fornavn:</label><input type="text" id="firstNameEdit" name="name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" ';
  if (!isNewUser)
  {
    o[p++] = 'value="';
    o[p++] = user.firstName;
    o[p++] = '" ';
  }
  o[p++] = '/></div><div class="form-element"><label for="lastNameEdit" class="standard-label">Etternavn:</label><input type="text" id="lastNameEdit" name="name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" ';
  if (!isNewUser)
  {
    o[p++] = 'value="';
    o[p++] = user.lastName;
    o[p++] = '" ';
  }
  o[p++] = '/></div><div class="form-element"><label for="userNameEdit" class="standard-label">E-post:</label><input type="text" id="userNameEdit" name="user_name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" ';
  if (!isNewUser)
  {
    o[p++] = 'value="';
    o[p++] = user.eMail;
    o[p++] = '" ';
  }
  o[p++] = '/></div><div class="form-element"><label for="phoneEdit" class="standard-label">Telefonnr:</label><input type="text" id="phoneEdit" name="phone" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" ';
  if (!isNewUser)
  {
    o[p++] = 'value="';
    o[p++] = user.phone;
    o[p++] = '" ';
  }
  o[p++] = '/></div>';
  if (isNewUser)
  {
    o[p++] = '<div class="form-element"><label for="passwordEdit" class="standard-label">Passord:</label><input type="password" id="passwordEdit" name="password" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" /> <span class="help-text">(minst ';
    o[p++] = String(PASSWORD_MIN_LENGTH);
    o[p++] = ' tegn)</span></div>';
  }
  o[p++] = '<div class="button-container fixed-width-container">';
  if (!isNewUser)
    o[p++] = '<button type="button" class="wide-button" onclick="changePassword();"><i class="fa-solid fa-key"></i> Endre passord</button> ';
  o[p++] = '<button type="submit" id="submitButton" class="wide-button"><i class="fa-solid fa-check"></i> ';
  if (isNewUser)
    o[p++] = 'Opprett bruker';
  else
    o[p++] = 'Lagre endringer';
  o[p++] = '</button></div></form>';

  userInfoBox.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['firstNameEdit', 'lastNameEdit', 'userNameEdit', 'phoneEdit',
    'passwordEdit', 'submitButton']);

  enableSubmitButton();
}

// *************************************************************************************************

function enableSubmitButton()
{
  var invalid;
  
  // The form cannot be submitted if the main edit boxes are empty.
  invalid = ((firstNameEdit.value === '') && (lastNameEdit.value === '')) ||
    (userNameEdit.value === '') || (phoneEdit.value === '');
  if (isNewUser)
  {
    // For a new customer, the user also has to fill in the password, and it has to contain at least
    // the minimum number of characters.
    invalid = invalid || (passwordEdit.value === '') ||
      (passwordEdit.value.length < PASSWORD_MIN_LENGTH);
  }
  else
  {
    // For an existing customer, the contents have to differ from the stored value. If nothing has
    // changed, there is no point in saving.
    invalid = invalid || ((firstNameEdit.value === user.firstName) &&
      (lastNameEdit.value === user.lastName) && (userNameEdit.value === user.eMail) &&
      (phoneEdit.value === user.phone));
  }

  submitButton.disabled = invalid;
}

// *************************************************************************************************

function changePassword()
{
  var newPassword, o, p;
  
  newPassword = prompt('Nytt passord:');
  if (newPassword === null)
    return;
  if (newPassword.length < PASSWORD_MIN_LENGTH)
  {
    alert('Passordet er for kort. Bruk et annet passord.');
    return;
  }

  o = new Array(5);
  p = 0;

  o[p++] = '<form id="changePasswordForm" action="/subscription/html/admin_edit_user.php" method="post"><input type="hidden" name="action" value="change_password" /><input type="hidden" name="user_id" value="';
  o[p++] = String(user.id);
  o[p++] = '" /><input type="hidden" name="new_password" value="';
  o[p++] = newPassword;
  o[p++] = '" /></form>';
  paymentHistoryDialogue.innerHTML = o.join('');
  document.getElementById('changePasswordForm').submit();
}

// *************************************************************************************************
// Subscription functions.
// *************************************************************************************************

function displaySubscriptions()
{
  var o, p, i;
  
  o = new Array((subscriptions.length * 25) + 18);
  p = 0;

  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = 'Avdeling';
  o[p++] = '</th><th>';
  o[p++] = 'Lagerbod';
  o[p++] = '</th><th>';
  o[p++] = 'Bodtype';
  o[p++] = '</th><th>';
  o[p++] = 'Status';
  o[p++] = '</th><th>';
  o[p++] = 'Fra dato';
  o[p++] = '</th><th>';
  o[p++] = 'Til dato';
  o[p++] = '</th><th>';
  o[p++] = 'Forsikring'; // getText(24, 'Forsikring');
  o[p++] = '</th><th>';
  o[p++] = 'Betalingshistorikk';
  o[p++] = '</th><th>';
  o[p++] = 'Si opp'
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < subscriptions.length; i++)
  {
    if (!displayExpiredSubscriptions && (subscriptions[i][c.sub.STATUS] === st.sub.EXPIRED))
      continue;

    o[p++] = '<tr><td>';
    o[p++] = Utility.getLocationName(subscriptions[i][c.sub.LOCATION_ID]);
    o[p++] = '</td><td>';
    o[p++] = subscriptions[i][c.sub.NAME];
    o[p++] = getPriceButton(i, PricePlan.getProductPricePlan(subscriptions, i));
    o[p++] = '</td><td>';
    o[p++] = subscriptions[i][c.sub.PRODUCT_TYPE];
    o[p++] = '</td><td><span class="status-label ';
    o[p++] = st.sub.COLOURS[subscriptions[i][c.sub.STATUS]];
    o[p++] = '">';
    o[p++] = SUB_TEXTS[subscriptions[i][c.sub.STATUS]];
    o[p++] = '</span></td><td>';
    o[p++] = subscriptions[i][c.sub.START_DATE];
    o[p++] = '</td><td>';
    if (subscriptions[i][c.sub.END_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = subscriptions[i][c.sub.END_DATE];
    o[p++] = '</td><td>';
    if (subscriptions[i][c.sub.INSURANCE_NAME] === '')
      o[p++] = '&nbsp;';
    else
    {
      o[p++] = subscriptions[i][c.sub.INSURANCE_NAME];
      o[p++] = getPriceButton(i, PricePlan.getInsurancePricePlan(subscriptions, i));
    }
    o[p++] = '</td><td><button type="button" class="icon-button" onclick="loadPaymentHistory(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-file-invoice-dollar"></i></button></td><td>';
    if (subscriptions[i][c.sub.STATUS] === st.sub.ONGOING)
    {
      o[p++] = '<button type="button" class="icon-button" onclick="cancelSubscription(\'';
      o[p++] = String(i);
      o[p++] = '\');"><i class="fa-solid fa-trash"></i></button>';
    }
    else  
      o[p++] = '&nbsp;';
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';
  
  subscriptionsBox.innerHTML = o.join('');
}

// *************************************************************************************************

function cancelSubscription(index)
{
  var approved;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, subscriptions))
  {
    // Display confirmation dialogue with correct information, depending on today's date.
    if (Utility.canCancelThisMonth())
      approved = confirm('Er du sikker på at du vil si opp ' + subscriptions[index][c.sub.NAME] +
        ' på vegne av kunden? Kunden beholder lagerboden til og med siste dag i inneværende måned.');
    else
      approved = confirm('Er du sikker på at du vil si opp ' + subscriptions[index][c.sub.NAME] +
        ' på vegne av kunden? Kunden trekkes for neste måned, og beholder lagerboden til og med siste dag neste måned.');
    if (approved)
    {
      o = new Array(5);
      p = 0;

      o[p++] = '<form id="cancelSubscriptionForm" action="/subscription/html/admin_edit_user.php" method="post"><input type="hidden" name="action" value="cancel_subscription" /><input type="hidden" name="user_id" value="';
      o[p++] = String(user.id);
      o[p++] = '" /><input type="hidden" name="id" value="';
      o[p++] = String(subscriptions[index][c.sub.ID]);
      o[p++] = '" /></form>';
      paymentHistoryDialogue.innerHTML = o.join('');
      document.getElementById('cancelSubscriptionForm').submit();
    }
  }
}

// *************************************************************************************************

function toggleExpiredSubscriptions()
{
  displayExpiredSubscriptions = expiredSubscriptionsCheckbox.checked;
  displaySubscriptions();
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
    o[p++] = 'Pris'; // getText(31, 'Pris');
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
  o = new Array((planLines.length * 9) + 14);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  planType = subscriptions[index][c.sub.PRICE_PLANS][pricePlanIndex][c.sub.PLAN_TYPE];
  if (planType < 0)
    o[p++] = 'Prishistorikk, leie'; // getText(32, 'Prishistorikk, leie');
  else
    o[p++] = 'Prishistorikk, ' + ADDITIONAL_PRODUCT_TEXTS[planType]; // getText(33, 'Prishistorikk, $1', [ADDITIONAL_PRODUCT_TEXTS[planType]]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = 'Fra dato'; // getText(5, 'Fra dato');
  o[p++] = '</th><th>';
  o[p++] = 'Til dato'; // getText(6, 'Til dato');
  o[p++] = '</th><th>';
  o[p++] = 'Pris'; // getText(31, 'Pris');
  o[p++] = '</th><th>';
  o[p++] = 'Beskrivelse'; // getText(35, 'Beskrivelse');
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
    o[p++] = 'Abonnementet avsluttet'; // getText(36, 'Abonnementet avsluttet');
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table></div><div class="dialogue-footer"><button type="button" onclick="closePricePlanDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = 'Lukk'; // getText(22, 'Lukk');
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
    return 'Inntil videre'; // getText(34, 'Inntil videre');
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
        'Laster betalingshistorikk. Vennligst vent...' + '</p>';
      Utility.display(overlay);
      Utility.display(paymentHistoryDialogue);
      fetch('/subscription/json/admin_payment_history.php?subscription_id=' +
        String(subscriptions[index][c.sub.ID]) + '&user_id=' + String(user.id))
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
  o[p++] = 'Betalingshistorikk for ' + subscriptions[index][c.sub.NAME] + ', ' + Utility.getLocationName(subscriptions[index][c.sub.LOCATION_ID]);
//  o[p++] = getText(15, 'Betalingshistorikk for $1, $2',
//    [subscriptions[index][c.sub.NAME], Utility.getLocationName(subscriptions[index][c.sub.LOCATION_ID])]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>&nbsp;</th><th>';
  o[p++] = 'Type'; // getText(16, 'Type');
  o[p++] = '</th><th>';
  o[p++] = 'Fakturanr'; // getText(17, 'Fakturanr');
  o[p++] = '</th><th>';
  o[p++] = 'Betalingsm&aring;te'; // getText(18, 'Betalingsm&aring;te');
  o[p++] = '</th><th>';
  o[p++] = 'Utstedt'; // getText(19, 'Utstedt');
  o[p++] = '</th><th>';
  o[p++] = 'Forfallsdato'; // getText(20, 'Forfallsdato');
  o[p++] = '</th><th>';
  o[p++] = 'Status'; // getText(21, 'Status');
  o[p++] = '</th><th>';
  o[p++] = 'Sum'; // getText(29, 'Sum');
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
    o[p++] = 'Ukjent'; // getText(30, 'Ukjent');
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
  o[p++] = 'Lukk'; // getText(22, 'Lukk');
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
  o[p++] = 'Beskrivelse'; // getText(26, 'Beskrivelse');
  o[p++] = '</th><th>';
  o[p++] = 'Produkt-ID'; // getText(27, 'Produkt-ID');
  o[p++] = '</th><th>';
  o[p++] = 'Bel&oslash;p'; // getText(28, 'Bel&oslash;p');
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
  o[p++] = 'Sum'; // getText(29, 'Sum');
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

