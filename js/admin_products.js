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
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var productsBox, filterToolbar, overlay, editProductDialogue, createSubscriptionDialogue,
  editLocationFilterDialogue, editProductTypeFilterDialogue, editStatusFilterDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editProductForm, editProductSubmitButton, locationCombo, nameEdit, productTypeCombo,
  multipleCheckbox, multipleBox, numberSignText, fromEditBox, toEditBox, padBox, padCheckbox,
  paddingLengthEditBox;
var createSubscriptionForm, subscriptionStartDateEdit, subscriptionSubmitButton;

// The number of displayed products. This depends on the current filter settings.
var displayedCount = 0;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['productsBox', 'filterToolbar', 'overlay', 'editProductDialogue',
    'createSubscriptionDialogue', 'editLocationFilterDialogue', 'editProductTypeFilterDialogue',
    'editStatusFilterDialogue']);

  displayProducts();

  // Display the results of a previous operation, if required.
  if (resultCode === result.MISSING_NUMBER_PLACEHOLDER_IN_PRODUCT_NAME)
    alert(getText(15, 'Du må ha med et nummertegn ("#") i navnet for å opprette flere lagerboder på en gang. Dette tegnet erstattes av nummeret på lagerboden.'));
  else
    if (resultCode >= 0)
      alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
        [String(resultCode)]));
}

// *************************************************************************************************
// Return hidden form elements that specify the current filter settings. These should be included
// whenever a request is submitted to the current page, so that the filter settings are maintained
// when the page is reloaded.
function getFilterFormElements()
{
  var o, p;

  o = new Array(9);
  p = 0;

  if (locationFilter !== null)
  {
    o[p++] = '<input type="hidden" name="location_filter" value="';
    o[p++] = JSON.stringify(locationFilter);
    o[p++] = '" />';
  }
  if (productTypeFilter !== null)
  {
    o[p++] = '<input type="hidden" name="product_type_filter" value="';
    o[p++] = JSON.stringify(productTypeFilter);
    o[p++] = '" />';
  }
  if (statusFilter !== null)
  {
    o[p++] = '<input type="hidden" name="status_filter" value="';
    o[p++] = JSON.stringify(statusFilter);
    o[p++] = '" />';
  }
  return o.join('');
}

// *************************************************************************************************

function deleteProduct(id)
{
  var index;

  index = Utility.getProductIndex(id);
  if ((index >= 0) &&
    confirm(getText(0, 'Er du sikker på at du vil slette lagerbod $1?', [products[index][c.prd.NAME]])))
  {
    o = new Array(5);
    p = 0;

    o[p++] = '<form id="deleteProductForm" action="/subscription/html/admin_products.php" method="post">';
    o[p++] = getFilterFormElements();
    o[p++] = '<input type="hidden" name="action" value="delete_product" /><input type="hidden" name="id" value="';
    o[p++] = String(products[index][c.prd.ID]);
    o[p++] = '" /></form>';
    editProductDialogue.innerHTML = o.join('');
    document.getElementById('deleteProductForm').submit();
  }
}

// *************************************************************************************************
// Products table functions.
// *************************************************************************************************

function displayProducts()
{
  var o, p, i;
  
  if (products.length <= 0)
  {
    productsBox.innerHTML = '<div class="form-element">' +
      getText(14, 'Det er ikke opprettet noen lagerboder enn&aring;.') + '</div>';
    filterToolbar.innerHTML = '&nbsp;';
    return;
  }

  displayedCount = 0;
  o = new Array((products.length * 25) + 16);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(2, 'Lager');
  o[p++] = '</th><th>';
  o[p++] = getText(3, 'Lagerbod');
  o[p++] = '</th><th>';
  o[p++] = getText(4, 'Bodtype');
  o[p++] = '</th><th>';
  o[p++] = getText(5, 'Utleiestatus');
  o[p++] = '</th><th>';
  o[p++] = getText(6, 'Utleid til');
  o[p++] = '</th><th>';
  o[p++] = getText(7, 'Reservert fra');
  o[p++] = '</th><th>';
  o[p++] = getText(8, 'Rediger | Slett | Se kunde');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < products.length; i++)
  {
    if (shouldHide(products[i])) continue;
    displayedCount++;
    o[p++] = '<tr><td>';
    o[p++] = Utility.getLocationName(products[i][c.prd.LOCATION_ID]);
    o[p++] = '</td><td>';
    o[p++] = products[i][c.prd.NAME];
    o[p++] = '</td><td>';
    o[p++] = Utility.getProductTypeName(products[i][c.prd.PRODUCT_TYPE_ID]);
    o[p++] = '</td><td><img src="/subscription/resources/status_';
    o[p++] = String(products[i][c.prd.STATUS]);
    o[p++] = '.png" alt="';
    o[p++] = st.prod.TEXTS_BRIEF[products[i][c.prd.STATUS]];
    o[p++] = '" class="status-image" />&nbsp;<span class="status-label ';
    o[p++] = st.prod.COLOURS[products[i][c.prd.STATUS]];
    o[p++] = '">';
    o[p++] = st.prod.TEXTS_BRIEF[products[i][c.prd.STATUS]];
    o[p++] = '</span></td><td>';
    if (products[i][c.prd.END_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = products[i][c.prd.END_DATE];
    o[p++] = '</td><td>';
    if (products[i][c.prd.RESERVED_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = products[i][c.prd.RESERVED_DATE];
    o[p++] = '</td><td><button type="button" class="icon-button" onclick="displayEditProductDialogue(';
    o[p++] = String(products[i][c.prd.ID]);
    o[p++] = ');"><i class="fa-solid fa-pen-to-square"></i></button> <button type="button" class="icon-button" onclick="deleteProduct(';
    o[p++] = String(products[i][c.prd.ID]);
    o[p++] = ');"><i class="fa-solid fa-trash"></i></button>&nbsp;&nbsp;&nbsp;';
    o[p++] = getCustomerLinks(i);
    if (settings.applicationRole !== APP_ROLE_PRODUCTION)
    {
      o[p++] = ' <button type="button" class="icon-button" onclick="displayCreateSubscriptionDialogue(';
      o[p++] = String(products[i][c.prd.ID]);
      o[p++] = ');"><i class="fa-solid fa-repeat"></i></button>';
    }
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  productsBox.innerHTML = o.join('');
  displayFilterToolbar();
}

// *************************************************************************************************
// Return links to the previous, current and next subscribers, if they exist.
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
  o = new Array(3);
  p = 0;

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
function getCustomerLinkButton(targetUserId, imageName, altText)
{
  o = new Array(7);
  p = 0;

  if (targetUserId >= 0)
  {
    o[p++] = '<button type="button" class="icon-button" onclick="window.location.href = \'/subscription/html/admin_edit_user.php?user_id=';
    o[p++] = String(targetUserId);
    o[p++] = '\';"><img src="/subscription/resources/';
    o[p++] = imageName;
    o[p++] = '.png" alt="';
    o[p++] = altText;
    o[p++] = '" /></button>&nbsp;';
  }
  else
  {
    o[p++] = '<button type="button" class="icon-button" disabled="disabled"><img src="/subscription/resources/';
    o[p++] = imageName;
    o[p++] = '_disabled.png" alt="';
    o[p++] = altText;
    o[p++] = '" /></button>&nbsp;';
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
  if ((product[c.prd.STATUS] === st.prod.RENTED) || (product[c.prd.STATUS] === st.prod.CANCELLED) ||
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
// Create and edit product functions.
// *************************************************************************************************

function displayEditProductDialogue(id)
{
  var o, p, i, index, isNew;
  
  if (id === -1)
    index = -1;
  else
  {
    index = Utility.getProductIndex(id);
    if (index < 0)
      return;
  }
  isNew = index === -1;
  o = new Array((locations.length * 7) + (productTypes.length * 7) + 42);
  p = 0;
  
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(12, 'Legg til lagerbod');
  else
    o[p++] = getText(13, 'Rediger lagerbod');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editProductForm" action="/subscription/html/admin_products.php" method="post"><div class="form-element">';
  o[p++] = getFilterFormElements();
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_product" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_product" /><input type="hidden" name="id" value="';
    o[p++] = String(products[index][c.loc.ID]);
    o[p++] = '" />';
  }
  o[p++] = '<label for="locationCombo" class="standard-label">';
  o[p++] = getText(16, 'Lager:');
  o[p++] = '</label> <select id="locationCombo" name="location_id" class="long-text" onchange="enableEditProductSubmitButton();">';
  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = locations[i][c.loc.ID];
    o[p++] = '"';
    // When creating a new product, select the first location in the locationFilter, if the table is
    // currently filtered. For an existing product, select the location where that product is
    // located.
    if ((isNew && (locationFilter !== null) && (locationFilter[0] === locations[i][c.loc.ID])) ||
      (!isNew && (locations[i][c.loc.ID] === products[index][c.prd.LOCATION_ID])))
      o[p++] = ' selected="selected"'
    o[p++] = '>';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</option>';
  }
  o[p++] = '</select></div><div class="form-element"><label for="nameEdit" class="standard-label">';
  o[p++] = getText(17, 'Navn:');
  o[p++] = '</label> <input type="text" id="nameEdit" name="name" class="long-text" onkeyup="enableEditProductSubmitButton();" onchange="enableEditProductSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = products[index][c.prd.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="productTypeCombo" class="standard-label">';
  o[p++] = getText(18, 'Bodtype:');
  o[p++] = '</label> <select id="productTypeCombo" name="product_type_id" class="long-text" onchange="enableEditProductSubmitButton();">';
  for (i = 0; i < productTypes.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = productTypes[i][c.typ.ID];
    o[p++] = '"';
    if ((!isNew) && (productTypes[i][c.typ.ID] === products[index][c.prd.PRODUCT_TYPE_ID]))
      o[p++] = ' selected="selected"';
    o[p++] = '>';
    o[p++] = productTypes[i][c.typ.NAME];
    o[p++] = '</option>';
  }
  o[p++] = '</select></div>';
  if (isNew)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="multipleCheckbox" name="create_multiple" onchange="toggleMultipleCheckbox();" /><label for="multipleCheckbox">';
    o[p++] = getText(19, 'Opprett flere lagerboder p&aring; en gang');
    o[p++] = '</label><div id="multipleBox" class="indented-box" style="display: none;"><p id="numberSignText" class="help-text">';
    o[p++] = getText(20, 'Bruk &num; i navnet for &aring; sette inn nummeret p&aring; lagerboden. F.eks: &quot;A &num;&quot;.');
    o[p++] = '</p><label for="fromEditBox">';
    o[p++] = getText(21, 'Sett inn nummer fra og med');
    o[p++] = '</label> <input type="number" id="fromEditBox" name="from_number" min="0" value="1" class="numeric" onkeyup="enableEditProductSubmitButton();" onchange="enableEditProductSubmitButton();" /> <label for="toEditBox">';
    o[p++] = getText(22, 'til og med');
    o[p++] = '</label> <input type="number" id="toEditBox" name="to_number" min="0" value="100" class="numeric" onkeyup="enableEditProductSubmitButton();" onchange="enableEditProductSubmitButton();" /> ';
    o[p++] = getText(23, '');
    o[p++] = '<br /><br /><input type="checkbox" id="padCheckbox" name="pad_with_zeroes" onchange="togglePaddingCheckbox();" /><label for="padCheckbox">';
    o[p++] = getText(24, 'Legg til nuller i starten av nummeret');
    o[p++] = '</label><div id="padBox" class="indented-box" style="display: none;"><label for="paddingLengthEditBox">';
    o[p++] = getText(25, 'Antall siffer skal v&aelig;re minst:');
    o[p++] = ' </label><input type="number" id="paddingLengthEditBox" name="digit_count" min="1" max="';
    o[p++] = String(MAX_PADDING_DIGIT_COUNT);
    o[p++] = '" value="3" class="numeric" onkeyup="enableEditProductSubmitButton();" onchange="enableEditProductSubmitButton();" /></div></div></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" id="editProductSubmitButton" onclick="editProductForm.submit();"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(26, 'Opprett');
  else
    o[p++] = getText(27, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeProductDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
  o[p++] = '</button></div>';

  editProductDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editProductForm', 'editProductSubmitButton', 'locationCombo', 'nameEdit',
    'productTypeCombo']);
  if (isNew)
  {
    Utility.readPointers(['multipleCheckbox', 'multipleBox', 'numberSignText', 'fromEditBox',
      'toEditBox', 'padBox', 'padCheckbox', 'paddingLengthEditBox']);
  }
  else
  {
    multipleCheckbox = null;
    multipleBox = null;
    fromEditBox = null;
    toEditBox = null;
    padCheckbox = null;
    padBox = null;
    paddingLengthEditBox = null;
  }

  if (isNew)
    productTypeCombo.selectedIndex = -1;
  Utility.display(overlay);
  Utility.display(editProductDialogue);
  enableEditProductSubmitButton();
}

// *************************************************************************************************

function toggleMultipleCheckbox()
{
  Utility.toggle(multipleBox);
  enableEditProductSubmitButton();
}

// *************************************************************************************************

function togglePaddingCheckbox()
{
  Utility.toggle(padBox);
  enableEditProductSubmitButton();
}

// *************************************************************************************************

function closeProductDialogue()
{
  Utility.hide(editProductDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableEditProductSubmitButton()
{
  var nameValid, hasNumberSign, fromNumber, toNumber, rangeValid, paddingLength, paddingValid;

  nameValid = nameEdit.value !== '';
  rangeValid = true;
  paddingValid = true;
  if (multipleCheckbox && multipleCheckbox.checked)
  {
    // We are creating several products at once. Ensure that the product name has a placeholder for
    // the numbers.
    hasNumberSign = nameEdit.value.indexOf('#') >= 0;
    nameValid = nameValid && hasNumberSign;
    if (hasNumberSign)
      numberSignText.className = 'help-text';
    else
      numberSignText.className = 'status-red';
    // Check from and to range.
    fromNumber = parseInt(fromEditBox.value, 10);
    toNumber = parseInt(toEditBox.value, 10);
    rangeValid = isFinite(fromNumber) && (fromNumber >= 0) && isFinite(toNumber) &&
      (toNumber >= 0) && (fromNumber <= toNumber);
    if (padCheckbox.checked)
    {
      // Check padding length.
      paddingLength = parseInt(paddingLengthEditBox.value, 10);
      paddingValid = isFinite(paddingLength) && (paddingLength >= 1) &&
        (paddingLength <= MAX_PADDING_DIGIT_COUNT);
    }
  }

  editProductSubmitButton.disabled = (locationCombo.selectedIndex <= -1) || !nameValid ||
    (productTypeCombo.selectedIndex <= -1)  || !rangeValid || !paddingValid;
}

// *************************************************************************************************
// Create subscription functions.
// *************************************************************************************************

function displayCreateSubscriptionDialogue(productId)
{
  var o, p;

  o = new Array(21);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(29, 'Opprett testabonnement');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="createSubscriptionForm" action="/subscription/html/admin_products.php" method="post"><div class="form-element"><p class="help-text">';
  o[p++] = getText(30, 'Denne funksjonen er kun for testform&aring;l. Her kan du opprette abonnementer p&aring; vilk&aring;rlige datoer, ogs&aring; tilbake i tid. Dermed kan du umiddelbart teste hvordan systemet virker, uten &aring; m&aring;tte vente p&aring; at tiden g&aring;r. For &aring; opprette et l&oslash;pende abonnement, la "til dato"-feltet være blankt.');
  o[p++] = '</p>';
  o[p++] = getFilterFormElements();
  o[p++] = '<input type="hidden" name="action" value="create_test_subscription" /><input type="hidden" name="product_id" value="';
  o[p++] = String(productId);
  o[p++] = '" /></div><div class="form-element"><label for="subscriptionUserId" class="standard-label">';
  o[p++] = getText(43, 'Bruker-ID:');
  o[p++] = '</label> <input type="text" id="subscriptionUserId" name="buyer_id" class="short-text" onkeyup="enableSubscriptionSubmitButton();" onchange="enableSubscriptionSubmitButton();" /></div><div class="form-element"><label for="subscriptionStartDateEdit" class="standard-label">';
  o[p++] = getText(31, 'Fra dato:');
  o[p++] = '</label> <input type="text" id="subscriptionStartDateEdit" name="start_date" class="short-text" onkeyup="enableSubscriptionSubmitButton();" onchange="enableSubscriptionSubmitButton();" /> ';
  o[p++] = getText(32, '(yyyy-mm-dd)');
  o[p++] = '</div><div class="form-element"><label for="subscriptionEndDateEdit" class="standard-label">';
  o[p++] = getText(33, 'Til dato:');
  o[p++] = '</label> <input type="text" id="subscriptionEndDateEdit" name="end_date" class="short-text" onkeyup="enableSubscriptionSubmitButton();" onchange="enableSubscriptionSubmitButton();" /> ';
  o[p++] = getText(32, '(yyyy-mm-dd)');
  o[p++] = '</div></form></div><div class="dialogue-footer"><button type="button" id="subscriptionSubmitButton" onclick="createSubscriptionForm.submit();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(26, 'Opprett');
  o[p++] = '</button> <button type="button" onclick="closeSubscriptionDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
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
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  o = new Array(25);
  p = 0;

  o[p++] = getText(34, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(35, 'Vis alle');
  o[p++] = '</button>';
  o[p++] = '<button type="button" class="filter-button';
  if (locationFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayLocationFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(2, 'Lager');
  o[p++] = '</button>';
  if (locationFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearLocationFilter();"><i class="fa-solid fa-xmark"></i></button>';
  o[p++] = '<button type="button" class="filter-button';
  if (productTypeFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayProductTypeFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(4, 'Bodtype');
  o[p++] = '</button>';
  if (productTypeFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearProductTypeFilter();"><i class="fa-solid fa-xmark"></i></button>';
  o[p++] = '<button type="button" class="filter-button';
  if (statusFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayStatusFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(36, 'Utleiestatus');
  o[p++] = '</button>';
  if (statusFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearStatusFilter();"><i class="fa-solid fa-xmark"></i></button>';
  o[p++] = '<span class="counter">';
  if (displayedCount === products.length)
    o[p++] = getText(38, 'Viser $1 lagerboder', [String(products.length)]);
  else
    o[p++] = getText(37, 'Viser $1 av $2 lagerboder',
      [String(displayedCount), String(products.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');
}

// *************************************************************************************************
// Return true if the list of products should not include the given product.
function shouldHide(product)
{
  return ((locationFilter !== null) && !locationFilter.includes(product[c.prd.LOCATION_ID])) ||
    ((productTypeFilter !== null) && !productTypeFilter.includes(product[c.prd.PRODUCT_TYPE_ID])) ||
    ((statusFilter !== null) && !statusFilter.includes(product[c.prd.STATUS]));
}

// *************************************************************************************************

function clearAllFilters()
{
  locationFilter = null;
  productTypeFilter = null;
  statusFilter = null;
  displayProducts();
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
  
  o = new Array((locations.length * 10) + 8);
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
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" onclick="updateLocationFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(27, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeLocationFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
  o[p++] = '</button></div>';

  editLocationFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editLocationFilterDialogue);
};

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
  // If the user unchecks all locations, instead of displaying nothing, clear the filter.
  if (locationFilter.length === 0)
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
  
  o = new Array((productTypes.length * 10) + 8);
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
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" onclick="updateProductTypeFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(27, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeProductTypeFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
  o[p++] = '</button></div>';

  editProductTypeFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editProductTypeFilterDialogue);
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
  // If the user unchecks all product types, instead of displaying nothing, clear the filter.
  if (productTypeFilter.length === 0)
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
  
  o = new Array((st.prod.TEXTS.length * 14) + 2);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(41, 'Velg hvilke utleiestatuser som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < st.prod.TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inStatusFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> ';
    o[p++] = '<label for="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox"> <img src="/subscription/resources/status_';
    o[p++] = String(i);
    o[p++] = '.png" alt="';
    o[p++] = st.prod.TEXTS[i];
    o[p++] = '" class="status-image" /> ';
    o[p++] = st.prod.TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '<div class="form-element"><span class="help-text">';
  o[p++] = getText(42, 'Pilene l&oslash;per langs tidslinjen. Den r&oslash;de firkanten er dagens dato.');
  o[p++] = '</span></div></form></div><div class="dialogue-footer"><button type="button" onclick="updateStatusFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(27, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeStatusFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
  o[p++] = '</button></div>';

  editStatusFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editStatusFilterDialogue);
}

// *************************************************************************************************

function clearStatusFilter()
{
  statusFilter = null;
  displayProducts();
}

// *************************************************************************************************

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
  // If the user unchecks all statuses, instead of displaying nothing, clear the filter.
  if (statusFilter.length === 0)
    statusFilter = null;
  closeStatusFilterDialogue();
  displayProducts();
}

// *************************************************************************************************

function closeStatusFilterDialogue()
{
  Utility.hide(editStatusFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
