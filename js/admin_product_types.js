// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var productTypesBox, editProductTypeDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editProductTypeForm, productTypeSubmitButton, productTypeCategoryCombo, productTypeNameEdit,
  productTypeSizeEdit, productTypePriceEdit;

// The sorting object that controls the sorting of the product types table.
var sorting;

// The popup menu for the product types table.
var menu;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['productTypesBox', 'editProductTypeDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(productTypes,
      [
        Sorting.createUiColumn(c.typ.CATEGORY_ID, Sorting.SORT_AS_STRING,
          function (productType)
          {
            // Sort on category and, within that, name.
            return Utility.getCategoryName(productType[c.typ.CATEGORY_ID]) + ' ' +
              productType[c.typ.NAME]; 
          }),
        Sorting.createUiColumn(c.typ.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.typ.NOTES, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.typ.SIZE, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.typ.PRICE, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayProductTypes
    );
  // Set the initial product types sorting. If that didn't cause product types to be displayed, do
  // so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayProductTypes();

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
  return sorting.getPageStateFormElements();
}

// *************************************************************************************************
// *** Product type functions.
// *************************************************************************************************
// Display the spinner. Once visible, display product types.
function displayProductTypes()
{
  Utility.displaySpinnerThen(doDisplayProductTypes);
}

// *************************************************************************************************
// Display the list of product types.
function doDisplayProductTypes()
{
  var o, p, i;
  
  if (productTypes.length <= 0)
  {
    productTypesBox.innerHTML = '<div class="form-element">' +
      getText(2, 'Det er ikke opprettet noen bodtyper enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  o = new Array((productTypes.length * 13) + 9);
  p = 0;
  
  // Header.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(14, 'Kategori'));
  o[p++] = sorting.getTableHeader(1, getText(3, 'Navn'));
  o[p++] = sorting.getTableHeader(2, getText(17, 'Notater'));
  o[p++] = sorting.getTableHeader(3, getText(18, 'St&oslash;rrelse (m&sup2;)'));
  o[p++] = sorting.getTableHeader(4, getText(15, 'Pris'));
  o[p++] = sorting.getTableHeader(5, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < productTypes.length; i++)
  {
    // Category name.
    o[p++] = '<tr><td>';
    o[p++] = Utility.getCategoryName(productTypes[i][c.typ.CATEGORY_ID]);
    // Product type name.
    o[p++] = '</td><td>';
    o[p++] = productTypes[i][c.typ.NAME];
    // Notes.
    o[p++] = '</td><td>';
    if (productTypes[i][c.typ.NOTES] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = productTypes[i][c.typ.NOTES];
    // Size.
    o[p++] = '</td><td>';
    o[p++] = String(productTypes[i][c.typ.SIZE]);
    // Base price.
    o[p++] = '</td><td>';
    o[p++] = String(productTypes[i][c.typ.PRICE]);
    // Buttons.
    o[p++] = ',-</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  productTypesBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, productTypes))
    return '';
  o = new Array(2);
  p = 0;

  // Edit product type button.
  o[p++] = sender.getMenuItem(getText(4, 'Rediger bodtype'), 'fa-pen-to-square', true,
    'displayEditProductTypeDialogue(' + String(index) + ');');
  // Delete product type button.
  o[p++] = sender.getMenuItem(getText(5, 'Slett bodtype'), 'fa-trash', true,
    'deleteProductType(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************

function deleteProductType(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, productTypes) &&
    confirm(getText(16, 'Er du sikker på at du vil slette bodtype: $1? Merk at en bodtype ikke kan slettes hvis det finnes lagerboder av denne typen.',
      [productTypes[index][c.typ.NAME]])))
  {
    o = new Array(4);
    p = 0;

    o[p++] = '<form id="deleteProductTypeForm" action="/subscription/html/admin_product_types.php" method="post"><input type="hidden" name="action" value="delete_product_type" />';
    o[p++] = getPageStateFormElements();
    o[p++] = Utility.getHidden('id', productTypes[index][c.typ.ID]);
    o[p++] = '</form>';
    editProductTypeDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteProductTypeForm'));
  }
}

// *************************************************************************************************
// Set the contents of, and display, the edit product type dialogue. index is the index in the
// productTypes table of the product type to be edited. Pass -1 to create a new product type.
function displayEditProductTypeDialogue(index)
{
  var o, p, i, isNew;

  index = parseInt(index, 10);
  isNew = index === -1;
  if (!(isNew || Utility.isValidIndex(index, productTypes)))
    return;
  if (isNew && (categories.length <= 0))
  {
    alert(getText(1, 'Du må ha minst én kategori før du kan opprette bodtyper. Lag en kategori først.'));
    return;
  }

  o = new Array((categories.length * 7) + 50);
  p = 0;
  
  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(6, 'Opprett bodtype');
  else
    o[p++] = getText(4, 'Rediger bodtype');
  o[p++] = '</h1></div>';
  
  // Content.
  o[p++] = '<div class="dialogue-content"><form id="editProductTypeForm" action="/subscription/html/admin_product_types.php" method="post">';
  o[p++] = getPageStateFormElements();
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_product_type" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_product_type" />';
    o[p++] = Utility.getHidden('id', productTypes[index][c.typ.ID]);
  }

  // Category.
  o[p++] = '<div class="form-element"><label for="productTypeCategoryCombo" class="standard-label">';
  o[p++] = getText(8, 'Kategori:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <select id="productTypeCategoryCombo" name="category_id" class="long-text" onchange="enableProductTypeSubmitButton();">';
  for (i = 0; i < categories.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = categories[i][c.cat.ID];
    o[p++] = '"';
    if ((!isNew) && (categories[i][c.cat.ID] === productTypes[index][c.typ.CATEGORY_ID]))
      o[p++] = ' selected="selected"'
    o[p++] = '>';
    o[p++] = categories[i][c.cat.NAME];
    o[p++] = '</option>';
  }
  o[p++] = '</select></div>';
  
  // Name.
  o[p++] = '<div class="form-element"><label for="productTypeNameEdit" class="standard-label">';
  o[p++] = getText(10, 'Navn:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="productTypeNameEdit" name="name" class="long-text" onkeyup="enableProductTypeSubmitButton();" onchange="enableProductTypeSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = productTypes[index][c.typ.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div>';
  
  // Notes.
  o[p++] = '<div class="form-element"><label for="productTypeNotesEdit" class="standard-label">';
  o[p++] = getText(19, 'Notater:');
  o[p++] = '</label> <input type="text" id="productTypeNotesEdit" name="product_type_notes" class="long-text" onkeyup="enableProductTypeSubmitButton();" onchange="enableProductTypeSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = productTypes[index][c.typ.NOTES];
    o[p++] = '"';
  }
  o[p++] = ' /></div>';

  // Size.
  o[p++] = '<div class="form-element"><label for="productTypeSizeEdit" class="standard-label">';
  o[p++] = getText(20, 'St&oslash;rrelse:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="number" id="productTypeSizeEdit" name="size" class="long-text" onkeyup="enableProductTypeSubmitButton();" onchange="enableProductTypeSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(productTypes[index][c.typ.SIZE]);
    o[p++] = '"';
  }
  o[p++] = ' /> ';
  o[p++] = getText(21, 'm&sup2;');
  o[p++] = '</div>';
  
  // Price.
  o[p++] = '<div class="form-element"><label for="productTypePriceEdit" class="standard-label">';
  o[p++] = getText(9, 'Pris:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="number" id="productTypePriceEdit" name="price" class="long-text" onkeyup="enableProductTypeSubmitButton();" onchange="enableProductTypeSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(productTypes[index][c.typ.PRICE]);
    o[p++] = '"';
  }
  o[p++] = ' /></div></form></div>';
  
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" id="productTypeSubmitButton" onclick="Utility.displaySpinnerThenSubmit(editProductTypeForm);"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(11, 'Opprett');
  else
    o[p++] = getText(12, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeProductTypeDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(13, 'Avbryt');
  o[p++] = '</button></div>';

  editProductTypeDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editProductTypeForm', 'productTypeSubmitButton',
    'productTypeCategoryCombo', 'productTypeNameEdit', 'productTypeSizeEdit',
    'productTypePriceEdit']);

  Utility.display(overlay);
  Utility.display(editProductTypeDialogue);
  enableProductTypeSubmitButton();
}

// *************************************************************************************************

function closeProductTypeDialogue()
{
  Utility.hide(editProductTypeDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Return true if the size in the editProductTypeDialogue is valid.
function isValidSize()
{
  var value;

  if (productTypeSizeEdit.value === '')
    return false;
  value = parseFloat(productTypeSizeEdit.value);
  return isFinite(value) && (value >= 0.0);
}

// *************************************************************************************************
// Return true if the price in the editProductTypeDialogue is valid.
function isValidPrice()
{
  var value;

  if (productTypePriceEdit.value === '')
    return false;
  value = parseInt(productTypePriceEdit.value, 10);
  return isFinite(value) && (value >= 0);
}

// *************************************************************************************************

function enableProductTypeSubmitButton()
{
  productTypeSubmitButton.disabled = (productTypeCategoryCombo.selectedIndex <= -1) ||
    (productTypeNameEdit.value === '') || !isValidSize() || !isValidPrice();
}

// *************************************************************************************************
