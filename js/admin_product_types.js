// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var categoriesBox, productTypesBox, editCategoryDialogue, editProductTypeDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editCategoryForm, categorySubmitButton, categoryNameEdit, editProductTypeForm,
  productTypeSubmitButton, productTypeCategoryCombo, productTypeNameEdit, productTypePriceEdit;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['categoriesBox', 'productTypesBox', 'editCategoryDialogue',
    'editProductTypeDialogue']);

  displayCategories();
  displayProductTypes();

  // Display the results of a previous operation, if required.
  if (resultCode >= 0)
    alert('Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode ' +
      String(resultCode) + '.');
/*
    alert(getText(, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
      [String(resultCode)]));
*/
}

// *************************************************************************************************
// *** Category functions.
// *************************************************************************************************

function displayCategories()
{
  var o, p, i;
  
  if (categories.length <= 0)
  {
    categoriesBox.innerHTML = '<div class="form-element">Det er ikke opprettet noen kategorier enn&aring;.</div>';
    // categoriesBox.innerHTML = '<div class="form-element">' +
    //  getText(, 'Det er ikke opprettet noen kategorier enn&aring;.') + '</div>';
    return;
  }

  o = new Array((categories.length * 9) + 2);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>Navn</th><th>Rediger</th><th>Slett</th></tr></thead><tbody>';
  for (i = 0; i < categories.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = categories[i][c.cat.NAME];
    o[p++] = '</td><td><button type="button" class="icon-button" onclick="displayEditCategoryDialogue(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-pen-to-square"></i></button></td><td><button type="button" class="icon-button" onclick="deleteCategory(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-trash"></i></button></td></tr>';
  }
  o[p++] = '</tbody></table>';

  categoriesBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Return true if the category with the given index in the categories table is referenced by any
// product type.
function categoryInUse(index)
{
  var i;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, categories))
  {
    for (i = 0; i < productTypes.length; i++)
    {
      if (productTypes[i][c.typ.CATEGORY_ID] === categories[index][c.cat.ID])
        return true;
    }
  }
  return false;
}

// *************************************************************************************************

function deleteCategory(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, categories))
    return;
  if (categoryInUse(index))
  {
    alert('Denne kategorien kan ikke slettes, fordi det finnes bodtyper som bruker den.');
    return;
  }

  if (confirm('Er du sikker på at du vil slette kategorien ' + categories[index][c.cat.NAME] + '?'))
  {
    o = new Array(3);
    p = 0;

    o[p++] = '<form id="deleteCategoryForm" action="/subscription/html/admin_product_types.php" method="post"><input type="hidden" name="action" value="delete_category" /><input type="hidden" name="id" value="';
    o[p++] = String(categories[index][c.cat.ID]);
    o[p++] = '" /></form>';
    editCategoryDialogue.innerHTML = o.join('');
    document.getElementById('deleteCategoryForm').submit();
  }
}

// *************************************************************************************************

function displayEditCategoryDialogue(index)
{
  var o, p, isNew;
  
  index = parseInt(index, 10);
  isNew = index === -1;
  if (!(isNew || Utility.isValidIndex(index, categories)))
    return;
  o = new Array(14);
  p = 0;
  
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = 'Opprett kategori';
  else
    o[p++] = 'Rediger kategori';
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editCategoryForm" action="/subscription/html/admin_product_types.php" method="post"><div class="form-element">';
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_category" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_category" /><input type="hidden" name="id" value="';
    o[p++] = String(categories[index][c.cat.ID]);
    o[p++] = '" />';
  }
  o[p++] = '<label for="categoryNameEdit" class="standard-label">Navn:</label> <input type="text" id="categoryNameEdit" name="name" class="long-text" onkeyup="enableCategorySubmitButton();" onchange="enableCategorySubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = categories[index][c.cat.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div></form></div><div class="dialogue-footer"><button type="button" id="categorySubmitButton" onclick="editCategoryForm.submit();"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = 'Opprett';
  else
    o[p++] = 'Oppdater';
  o[p++] = '</button> <button type="button" onclick="closeCategoryDialogue();"><i class="fa-solid fa-xmark"></i> Avbryt</button></div>';

  editCategoryDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editCategoryForm', 'categorySubmitButton', 'categoryNameEdit']);

  Utility.display(overlay);
  Utility.display(editCategoryDialogue);
  enableCategorySubmitButton();
}

// *************************************************************************************************

function closeCategoryDialogue()
{
  Utility.hide(editCategoryDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableCategorySubmitButton()
{
  categorySubmitButton.disabled = (categoryNameEdit.value === '');
}

// *************************************************************************************************

function getCategoryName(id)
{
  var i;

  for (i = 0; i < categories.length; i++)
    if (categories[i][c.cat.ID] === id)
      return categories[i][c.cat.NAME];
  return '&nbsp;';
}

// *************************************************************************************************
// *** Product type functions.
// *************************************************************************************************

function displayProductTypes()
{
  var o, p, i;
  
  if (productTypes.length <= 0)
  {
    productTypesBox.innerHTML = '<div class="form-element">Det er ikke opprettet noen bodtyper enn&aring;.</div>';
    // categoriesBox.innerHTML = '<div class="form-element">' +
    //  getText(, 'Det er ikke opprettet noen bodtyper enn&aring;.') + '</div>';
    return;
  }

  o = new Array((productTypes.length * 9) + 2);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>Kategori</th><th>Navn</th><th>Pris</th><th>Rediger</th><th>Slett</th></tr></thead><tbody>';
  for (i = 0; i < productTypes.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = getCategoryName(productTypes[i][c.typ.CATEGORY_ID]);
    o[p++] = '</td><td>';
    o[p++] = productTypes[i][c.typ.NAME];
    o[p++] = '</td><td>';
    o[p++] = String(productTypes[i][c.typ.PRICE]);
    o[p++] = ',-</td><td><button type="button" class="icon-button" onclick="displayEditProductTypeDialogue(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-pen-to-square"></i></button></td><td><button type="button" class="icon-button" onclick="deleteProductType(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-trash"></i></button></td></tr>';
  }
  o[p++] = '</tbody></table>';

  productTypesBox.innerHTML = o.join('');
}

// *************************************************************************************************

function deleteProductType(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, productTypes) &&
    confirm('Er du sikker på at du vil slette bodtype ' + productTypes[index][c.typ.NAME] +
      '? Merk at en bodtype ikke kan slettes hvis det finnes lagerboder av denne typen.'))
  {
    o = new Array(3);
    p = 0;

    o[p++] = '<form id="deleteProductTypeForm" action="/subscription/html/admin_product_types.php" method="post"><input type="hidden" name="action" value="delete_product_type" /><input type="hidden" name="id" value="';
    o[p++] = String(productTypes[index][c.typ.ID]);
    o[p++] = '" /></form>';
    editProductTypeDialogue.innerHTML = o.join('');
    document.getElementById('deleteProductTypeForm').submit();
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
    alert('Du må ha minst én kategori før du kan opprette bodtyper. Lag en kategori først.');
    return;
  }

  o = new Array((categories.length * 7) + 20);
  p = 0;
  
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = 'Opprett bodtype';
  else
    o[p++] = 'Rediger bodtype';
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editProductTypeForm" action="/subscription/html/admin_product_types.php" method="post"><div class="form-element">';
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_product_type" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_product_type" /><input type="hidden" name="id" value="';
    o[p++] = String(productTypes[index][c.typ.ID]);
    o[p++] = '" />';
  }
  o[p++] = '<label for="productTypeCategoryCombo" class="standard-label">Kategori:</label> <select id="productTypeCategoryCombo" name="category_id" class="long-text" onchange="enableProductTypeSubmitButton();">';
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
  o[p++] = '</select></div><div class="form-element"><label for="productTypeNameEdit" class="standard-label">Navn:</label> <input type="text" id="productTypeNameEdit" name="name" class="long-text" onkeyup="enableProductTypeSubmitButton();" onchange="enableProductTypeSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = productTypes[index][c.typ.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="productTypePriceEdit" class="standard-label">Pris:</label> <input type="text" id="productTypePriceEdit" name="price" class="long-text" onkeyup="enableProductTypeSubmitButton();" onchange="enableProductTypeSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(productTypes[index][c.typ.PRICE]);
    o[p++] = '"';
  }
  o[p++] = ' /></div></form></div><div class="dialogue-footer"><button type="button" id="productTypeSubmitButton" onclick="editProductTypeForm.submit();"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = 'Opprett';
  else
    o[p++] = 'Oppdater';
  o[p++] = '</button> <button type="button" onclick="closeProductTypeDialogue();"><i class="fa-solid fa-xmark"></i> Avbryt</button></div>';

  editProductTypeDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editProductTypeForm', 'productTypeSubmitButton',
    'productTypeCategoryCombo', 'productTypeNameEdit', 'productTypePriceEdit']);

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

function enableProductTypeSubmitButton()
{
  productTypeSubmitButton.disabled = (productTypeCategoryCombo.selectedIndex <= -1) ||
    (productTypeNameEdit.value === '') || (productTypePriceEdit.value === '');
}

// *************************************************************************************************
