// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var insuranceProductsBox, filterToolbar, overlay, editInsuranceProductDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editInsuranceProductForm, nameEdit, priceEdit, descriptionEdit, productTypesBox, locationsBox,
  submitButton, freetextEdit;

// The sorting object that controls the sorting of the insuranceProducts table.
var sorting;

// The popup menu for the insuranceProducts table.
var menu;

// The number of displayed insurance products. This depends on the current filter settings.
var displayedCount = 0;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['insuranceProductsBox', 'filterToolbar', 'overlay',
    'editInsuranceProductDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(insuranceProducts,
      [
        Sorting.createUiColumn(c.ins.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.ins.DESCRIPTION, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.ins.PRICE, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayInsuranceProducts
    );
  // Set the initial sorting. If that didn't cause insurance products to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayInsuranceProducts();

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

  o = new Array(2);
  p = 0;

  if (freetextFilter !== '')
    o[p++] = Utility.getHidden('freetext_filter', freetextFilter);
  o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// Display the spinner. Once visible, display insurance products.
function displayInsuranceProducts()
{
  Utility.displaySpinnerThen(doDisplayInsuranceProducts);
}

// *************************************************************************************************
// Display the list of insurance products.
function doDisplayInsuranceProducts()
{
  var o, p, i;
  
  if (insuranceProducts.length <= 0)
  {
    insuranceProductsBox.innerHTML = '<div class="form-element">' +
      getText(1, 'Det er ikke opprettet noen forsikringer enn&aring;.') + '</div>';
    filterToolbar.innerHTML = '&nbsp;';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((insuranceProducts.length * 13) + 9);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(2, 'Navn'));
  o[p++] = sorting.getTableHeader(1, getText(3, 'Beskrivelse'));
  o[p++] = sorting.getTableHeader(2, getText(4, 'Pris'));
  o[p++] = sorting.getTableHeader(3, getText(5, 'For bodtyper'));
  o[p++] = sorting.getTableHeader(4, getText(6, 'For lager'));
  o[p++] = sorting.getTableHeader(5, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < insuranceProducts.length; i++)
  {
    if (shouldHide(insuranceProducts[i])) continue;
    displayedCount++;

    // Insurance name.
    o[p++] = '<tr><td>';
    o[p++] = insuranceProducts[i][c.ins.NAME];
    // Description.
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(insuranceProducts[i][c.ins.DESCRIPTION], 50);
    // Price per month.
    o[p++] = '</td><td>';
    o[p++] = String(insuranceProducts[i][c.ins.PRICE]);
    // For product types.
    o[p++] = ',-</td><td>';
    if (insuranceProducts[i][c.ins.FOR_PRODUCT_TYPES] === null)
      o[p++] = getText(9, 'Alle bodtyper');
    else
      o[p++] = getText(10, '$1 av $2 bodtyper',
        [
          String(insuranceProducts[i][c.ins.FOR_PRODUCT_TYPES].length),
          String(productTypes.length)
        ]);
    // For locations.
    o[p++] = '</td><td>';
    if (insuranceProducts[i][c.ins.FOR_LOCATIONS] === null)
      o[p++] = getText(11, 'Alle lager');
    else
      o[p++] = getText(12, '$1 av $2 lager',
        [
          String(insuranceProducts[i][c.ins.FOR_LOCATIONS].length),
          String(locations.length)
        ]);
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  insuranceProductsBox.innerHTML = o.join('');
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
  if (!Utility.isValidIndex(index, insuranceProducts))
    return '';
  o = new Array(2);
  p = 0;

  // Edit button.
  o[p++] = sender.getMenuItem(getText(7, 'Rediger forsikring'), 'fa-pen-to-square', true,
    'displayEditInsuranceProductDialogue(' + String(index) + ');');
  // Delete button. Disabled if the product is not free.
  o[p++] = sender.getMenuItem(getText(8, 'Slett forsikring'), 'fa-trash', true,
    'deleteInsuranceProduct(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************

function deleteInsuranceProduct(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, insuranceProducts) &&
    confirm(getText(13, 'Er du sikker på at du vil slette forsikring: $1?',
      [insuranceProducts[index][c.ins.NAME]])))
  {
    o = new Array(4);
    p = 0;

    o[p++] = '<form id="deleteInsuranceProductForm" action="/subscription/html/admin_insurance.php" method="post"><input type="hidden" name="action" value="delete_insurance_product" />';
    o[p++] = getPageStateFormElements();
    o[p++] = Utility.getHidden('id', insuranceProducts[index][c.ins.ID]);
    o[p++] = '</form>';
    editInsuranceProductDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteInsuranceProductForm'));
  }
}

// *************************************************************************************************

function displayEditInsuranceProductDialogue(index)
{
  var o, p, isNew, i, forProductTypes, forAllProductTypes, forLocations, forAllLocations;
  
  index = parseInt(index, 10);
  isNew = index === -1;
  if (!(isNew || Utility.isValidIndex(index, insuranceProducts)))
    return;
  o = new Array((productTypes.length * 13) + (locations.length * 13) + 65);
  p = 0;
  
  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(14, 'Opprett forsikring');
  else
    o[p++] = getText(7, 'Rediger forsikring');
  o[p++] = '</h1></div>';
  
  // Content.
  o[p++] = '<div class="dialogue-content"><form id="editInsuranceProductForm" action="/subscription/html/admin_insurance.php" method="post"><div class="form-element">';
  o[p++] = Utility.getHidden('product_type_count', productTypes.length);
  o[p++] = Utility.getHidden('location_count', locations.length);
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_insurance_product" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_insurance_product" />';
    o[p++] = Utility.getHidden('id', insuranceProducts[index][c.ins.ID]);
  }
  // Name.
  o[p++] = '<label for="nameEdit" class="standard-label">';
  o[p++] = getText(19, 'Navn:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="nameEdit" name="name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = insuranceProducts[index][c.ins.NAME];
    o[p++] = '"';
  }
  // Price.
  o[p++] = ' /></div><div class="form-element"><label for="priceEdit" class="standard-label">';
  o[p++] = getText(26, 'Pris pr mnd:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="number" id="priceEdit" name="price" min="0" class="numeric" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = insuranceProducts[index][c.ins.PRICE];
    o[p++] = '"';
  }
  // Description.
  o[p++] = ' /></div><div class="form-element"><label for="descriptionEdit" class="standard-label">';
  o[p++] = getText(20, 'Beskrivelse:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><textarea id="descriptionEdit" name="description" rows="10" cols="80" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">';
  if (!isNew)
    o[p++] = insuranceProducts[index][c.ins.DESCRIPTION];
  // For product types.
  o[p++] = '</textarea></div><div class="form-element">';
  o[p++] = getText(23, 'Forsikringen tilbys:');
  o[p++] = '</div><div class="column-container"><div class="column for-product-types-column"><div class="form-element"><input type="radio" id="allProductTypesRadio" name="for_all_product_types" value="1" onchange="toggleProductTypesBox(this);"';
  if (isNew)
    forProductTypes = null;
  else
    forProductTypes = insuranceProducts[index][c.ins.FOR_PRODUCT_TYPES];
  forAllProductTypes = forProductTypes === null;
  if (forAllProductTypes)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="allProductTypesRadio">';
  o[p++] = getText(21, 'For alle bodtyper');
  o[p++] = '</label></div><div class="form-element"><input type="radio" id="someProductTypesRadio" name="for_all_product_types" value="0" onchange="toggleProductTypesBox(this);"';
  if (!forAllProductTypes)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="someProductTypesRadio">';
  o[p++] = getText(22, 'For noen bodtyper');
  o[p++] = '</label><div id="productTypesBox" class="indented-box"';
  if (forAllProductTypes)
    o[p++] = ' style="display: none;"';
  o[p++] = '><ul class="checkbox-list">';
  for (i = 0; i < productTypes.length; i++)
  {
    o[p++] = '<li><input type="checkbox" id="productType';
    o[p++] = String(i);
    o[p++] = '" name="for_product_type_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = String(productTypes[i][c.typ.ID]);
    o[p++] = '"';
    if (forAllProductTypes || Utility.valueInArray(productTypes[i][c.typ.ID], forProductTypes))
      o[p++] = ' checked="checked"';
    o[p++] = ' /> <label for="productType';
    o[p++] = String(i);
    o[p++] = '">';
    o[p++] = productTypes[i][c.typ.NAME];
    o[p++] = '</label></li>';
  }
  o[p++] = '</ul><button type="button" onclick="setAllProductTypesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(27, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllProductTypesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(28, 'Ingen');
  // For locations.
  o[p++] = '</button></div></div></div><div class="column for-locations-column"><div class="form-element"><input type="radio" id="allLocationsRadio" name="for_all_locations" value="1" onchange="toggleLocationsBox(this);"';
  if (isNew)
    forLocations = null;
  else
    forLocations = insuranceProducts[index][c.ins.FOR_LOCATIONS];
  forAllLocations = forLocations === null;
  if (forAllLocations)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="allLocationsRadio">';
  o[p++] = getText(24, 'Ved alle lager');
  o[p++] = '</label></div><div class="form-element"><input type="radio" id="someLocationsRadio" name="for_all_locations" value="0" onchange="toggleLocationsBox(this);"';
  if (!forAllLocations)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="someLocationsRadio">';
  o[p++] = getText(25, 'Ved noen lager');
  o[p++] = '</label><div id="locationsBox" class="indented-box"';
  if (forAllLocations)
    o[p++] = ' style="display: none;"';
  o[p++] = '><ul class="checkbox-list">';
  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<li><input type="checkbox" id="location';
    o[p++] = String(i);
    o[p++] = '" name="for_location_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = String(locations[i][c.loc.ID]);
    o[p++] = '"';
    if (forAllLocations || Utility.valueInArray(locations[i][c.loc.ID], forLocations))
      o[p++] = ' checked="checked"';
    o[p++] = ' /> <label for="location';
    o[p++] = String(i);
    o[p++] = '">';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</label></li>';
  }
  o[p++] = '</ul><button type="button" onclick="setAllLocationsTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(27, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(28, 'Ingen');
  o[p++] = '</button></div></div></div></div></form></div>';
  
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" id="submitButton" onclick="Utility.displaySpinnerThenSubmit(editInsuranceProductForm);"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(16, 'Opprett');
  else
    o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeInsuranceProductDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editInsuranceProductDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editInsuranceProductForm', 'nameEdit', 'priceEdit', 'descriptionEdit',
    'productTypesBox', 'locationsBox', 'submitButton']);

  Utility.display(overlay);
  Utility.display(editInsuranceProductDialogue);
  enableSubmitButton();
}

// *************************************************************************************************
// Check or uncheck all the product type checkboxes in the "for product types" filter, depending on
// checked, which should be a boolean.
function setAllProductTypesTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < productTypes.length; i++)
  {
    checkbox = document.getElementById('productType' + String(i));
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************
// Check or uncheck all the location checkboxes in the "for location" filter, depending on checked,
// which should be a boolean.
function setAllLocationsTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < locations.length; i++)
  {
    checkbox = document.getElementById('location' + String(i));
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function closeInsuranceProductDialogue()
{
  Utility.hide(editInsuranceProductDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableSubmitButton()
{
  var price;
  
  price = parseInt(priceEdit.value, 10);
  submitButton.disabled = (nameEdit.value === '') || !isFinite(price) || (price < 0) ||
    (descriptionEdit.value === '');
}

// *************************************************************************************************

function toggleProductTypesBox(radioButton)
{
  if (radioButton.value === '1')
    Utility.hide(productTypesBox);
  else
    Utility.display(productTypesBox);
  enableSubmitButton();
}

// *************************************************************************************************

function toggleLocationsBox(radioButton)
{
  if (radioButton.value === '1')
    Utility.hide(locationsBox);
  else
    Utility.display(locationsBox);
  enableSubmitButton();
}

// *************************************************************************************************
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  o = new Array(12);
  p = 0;

  // Clear all filters button.
  o[p++] = getText(29, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(30, 'Vis alle');
  o[p++] = '</button>';
  // Freetext filter edit.
  o[p++] = '<input type="text" id="freetextEdit" placeholder="';
  o[p++] = getText(31, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Display counter box.
  o[p++] = '<span class="counter">';
  if (displayedCount === insuranceProducts.length)
    o[p++] = getText(32, 'Viser $1 forsikringer', [String(insuranceProducts.length)]);
  else
    o[p++] = getText(33, 'Viser $1 av $2 forsikringer',
      [String(displayedCount), String(insuranceProducts.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['freetextEdit']);
}

// *************************************************************************************************
// Return true if the list of insurance products should not include the given insuranceProduct.
function shouldHide(insuranceProduct)
{
  return ((freetextFilter !== '') && !matchesFreetextFilter(insuranceProduct));
}

// *************************************************************************************************

function clearAllFilters()
{
  freetextFilter = '';
  freetextEdit.value = '';
  displayInsuranceProducts();
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
  displayInsuranceProducts();
}

// *************************************************************************************************
// Return true if the given insuranceProduct matches the current freetext filter.
function matchesFreetextFilter(insuranceProduct)
{
  var filter;

  filter = freetextFilter.toLowerCase();
  // If there is no filter (or no insurance product), everything matches. Otherwise, return a match
  // if the insurance product's name or description fields contain the filter text.
  return (insuranceProduct === null) || (filter === '') ||
    (insuranceProduct[c.ins.NAME].toLowerCase().indexOf(filter) >= 0) ||
    (insuranceProduct[c.ins.DESCRIPTION].toLowerCase().indexOf(filter) >= 0);
}

// *************************************************************************************************
