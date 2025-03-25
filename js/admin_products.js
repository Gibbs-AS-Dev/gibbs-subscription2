// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var productsBox, filterToolbar, overlay, editProductDialogue, productNotesDialogue,
  editLocationFilterDialogue, editProductTypeFilterDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editProductForm, editProductSubmitButton, locationCombo, nameEdit, productTypeCombo,
  multipleCheckbox, multipleBox, numberSignText, fromEditBox, toEditBox, padBox, padCheckbox,
  paddingLengthEditBox, productNotesTextArea, freetextEdit;

// The sorting object that controls the sorting of the products table.
var sorting;

// The popup menu for the products table.
var menu;

// The number of displayed products. This depends on the current filter settings.
var displayedCount = 0;

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
  Utility.readPointers(['productsBox', 'filterToolbar', 'overlay', 'editProductDialogue',
    'productNotesDialogue', 'editLocationFilterDialogue', 'editProductTypeFilterDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

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
  if (resultCode === result.MISSING_NUMBER_PLACEHOLDER_IN_PRODUCT_NAME)
    alert(getText(15, 'Du må ha med et nummertegn ("#") i navnet for å opprette flere lagerboder på en gang. Dette tegnet erstattes av nummeret på lagerboden.'));
  else
    if (Utility.isError(resultCode))
      alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Return hidden form elements that specify the current state of the page, including sorting, search
// and filter settings. These should be included whenever a request is submitted to the current
// page, so that the state is maintained when the page is reloaded.
function getPageStateFormElements()
{
  var o, p;

  o = new Array(5);
  p = 0;

  if (locationFilter !== null)
    o[p++] = Utility.getHidden('location_filter', locationFilter.join(','));
  if (productTypeFilter !== null)
    o[p++] = Utility.getHidden('product_type_filter', productTypeFilter.join(','));
  if (freetextFilter !== '')
    o[p++] = Utility.getHidden('freetext_filter', freetextFilter);
  o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// Upon confirmation, delete the product with the given index in the products table.
function deleteProduct(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, products) && canDeleteProduct(index) &&
    confirm(getText(0, 'Er du sikker på at du vil slette lagerbod $1?',
      [products[index][c.prd.NAME]])))
  {
    o = new Array(5);
    p = 0;

    o[p++] = '<form id="deleteProductForm" action="/subscription/html/admin_products.php" method="post">';
    o[p++] = getPageStateFormElements();
    o[p++] = '<input type="hidden" name="action" value="delete_product" />';
    o[p++] = Utility.getHidden('id', products[index][c.prd.ID]);
    o[p++] = '</form>';
    editProductDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteProductForm'));
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
  o = new Array((products.length * 13) + 9);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(2, 'Lager'));
  o[p++] = sorting.getTableHeader(1, getText(3, 'Lagerbod'));
  o[p++] = sorting.getTableHeader(2, getText(4, 'Bodtype'));
  o[p++] = sorting.getTableHeader(3, getText(36, 'Bodstatus'));
  o[p++] = sorting.getTableHeader(4, getText(37, 'Notater'));
  o[p++] = sorting.getTableHeader(5, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < products.length; i++)
  {
    if (shouldHide(products[i]))
      continue;
    displayedCount++;

    // Location name.
    o[p++] = '<tr><td>';
    o[p++] = Utility.getLocationName(products[i][c.prd.LOCATION_ID]);
    // Enabled flag and product name.
    o[p++] = '</td><td>';
    o[p++] = products[i][c.prd.NAME];
    // Product type.
    o[p++] = '</td><td>';
    o[p++] = Utility.getProductTypeNameWithNotes(products[i][c.prd.PRODUCT_TYPE_ID]);
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
// Return true if the product with the given index in the products table can be deleted. A product
// can be deleted if it has status NEW or VACATED. This function assumes that the product's status
// has been set for the date on which we need to know whether or not it can be deleted. If the
// product was not found, the function will return false.
function canDeleteProduct(index)
{
  index = parseInt(index, 10);
  return Utility.isValidIndex(index, products) &&
    ((products[index][c.prd.STATUS] === st.prod.NEW) ||
    (products[index][c.prd.STATUS] === st.prod.VACATED));
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p, canDelete;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, products))
    return '';
  canDelete = canDeleteProduct(index);
  o = new Array(5);
  p = 0;

  // Edit button.
  o[p++] = sender.getMenuItem(getText(8, 'Rediger'), 'fa-pen-to-square', true,
    'displayEditProductDialogue(' + String(index) + ');');
  // Edit product notes button.
  o[p++] = sender.getMenuItem(getText(39, 'Se notater'), 'fa-file-pen', true,
    'displayProductNotes(' + String(index) + ');');
  // Delete button. Disabled if the product is not free.
  o[p++] = sender.getMenuItem(getText(34, 'Slett'), 'fa-trash', canDelete,
    'deleteProduct(' + String(index) + ');');
  // Help text if the product is in use, and cannot be deleted.
  if (!canDelete)
  {
    o[p++] = '<span class="help-text">';
    o[p++] = getText(35, 'Boden er utleid.');
    o[p++] = '</span>';
  }
  return o.join('');
}

// *************************************************************************************************
// Create and edit product functions.
// *************************************************************************************************
// Display the dialogue box to edit the product with the given index in the products table. Pass -1
// in order to create a new product.
function displayEditProductDialogue(index)
{
  var o, p, i, isNew;
  
  index = parseInt(index, 10);
  if ((index !== -1) && !Utility.isValidIndex(index, products))
    return;
  isNew = index === -1;
  if (isNew && ((categories.length <= 0) || (productTypes <= 0) || (locations <= 0)))
  {
    alert(getText(29, 'Du må opprette både kategorier, bodtyper og lager før du kan legge til lagerboder.'));
    return
  }
  o = new Array((locations.length * 7) + (productTypes.length * 7) + 49);
  p = 0;
  
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(12, 'Legg til lagerbod');
  else
    o[p++] = getText(13, 'Rediger lagerbod');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editProductForm" action="/subscription/html/admin_products.php" method="post"><div class="form-element">';
  o[p++] = getPageStateFormElements();
  if (isNew)
  {
    o[p++] = '<input type="hidden" name="action" value="create_product" />';
    o[p++] = Utility.getHidden('ready_status', st.ready.YES);
  }
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_product" />';
    o[p++] = Utility.getHidden('id', products[index][c.prd.ID]);
    o[p++] = Utility.getHidden('ready_status', products[index][c.prd.READY_STATUS]);
  }
  o[p++] = '<label for="locationCombo" class="standard-label">';
  o[p++] = getText(16, 'Lager:');
  o[p++] = Utility.getMandatoryMark();
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
      o[p++] = ' selected="selected"';
    o[p++] = '>';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</option>';
  }
  o[p++] = '</select></div><div class="form-element"><label for="nameEdit" class="standard-label">';
  o[p++] = getText(17, 'Navn:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="nameEdit" name="name" class="long-text" onkeyup="enableEditProductSubmitButton();" onchange="enableEditProductSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = products[index][c.prd.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="productTypeCombo" class="standard-label">';
  o[p++] = getText(18, 'Bodtype:');
  o[p++] = Utility.getMandatoryMark();
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
    if (productTypes[i][c.typ.NOTES]) {
      o[p++] = ' - ';
      o[p++] = productTypes[i][c.typ.NOTES];
    }
    o[p++] = '</option>';
  }
  o[p++] = '</select></div><div class="form-element"><label class="standard-label">&nbsp;</label><input type="checkbox" id="enabledCheckbox" name="enabled" value="1" ';
  if (isNew || products[index][c.prd.ENABLED])
    o[p++] = 'checked="checked" ';
  o[p++] = '/><label for="enabledCheckbox">';
  o[p++] = getText(30, 'Kan bestilles n&aring;r ledig');
  o[p++] = '</label></div>';
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
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" id="editProductSubmitButton" onclick="Utility.displaySpinnerThenSubmit(editProductForm);"><i class="fa-solid fa-check"></i> ';
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
  o[p++] = getText(37, 'Notater');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><textarea id="productNotesTextArea">';
  o[p++] = products[index][c.prd.NOTES];
  o[p++] = '</textarea></div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="saveProductNotes(';
  o[p++] = String(index);
  o[p++] = ');"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(38, 'Lagre');
  o[p++] = '</button> <button type="button" onclick="closeProductNotesDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
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
      alert(getText(1, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
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
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  o = new Array(24);
  p = 0;

  // Clear all filters button.
  o[p++] = getText(9, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(33, 'Vis alle');
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
  o[p++] = getText(7, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Display counter box.
  o[p++] = '<span class="counter">';
  if (displayedCount === products.length)
    o[p++] = getText(10, 'Viser $1 lagerboder', [String(products.length)]);
  else
    o[p++] = getText(11, 'Viser $1 av $2 lagerboder',
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
    ((freetextFilter !== '') && !matchesFreetextFilter(product));
}

// *************************************************************************************************

function clearAllFilters()
{
  locationFilter = null;
  productTypeFilter = null;
  freetextFilter = '';
  freetextEdit.value = '';
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
  
  o = new Array((locations.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(31, 'Velg hvilke lager som skal vises');
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
  o[p++] = getText(5, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(6, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateLocationFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(27, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeLocationFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
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
  o[p++] = getText(32, 'Velg hvilke bodtyper som skal vises');
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
    if (productTypes[i][c.typ.NOTES]) {
      o[p++] = ' - ';
      o[p++] = productTypes[i][c.typ.NOTES];
    }
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllProductTypesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(5, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllProductTypesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(6, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateProductTypeFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(27, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeProductTypeFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(28, 'Avbryt');
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
