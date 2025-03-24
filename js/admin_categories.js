// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var categoriesBox, editCategoryDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editCategoryForm, categorySubmitButton, categoryNameEdit;

// The sorting object that controls the sorting of the categories table.
var sorting;

// The popup menu for the categories table.
var menu;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['categoriesBox', 'editCategoryDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(categories,
      [
        Sorting.createUiColumn(c.cat.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayCategories
    );
  // Set the initial categories sorting. If that didn't cause categories to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayCategories();

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
// *** Category functions.
// *************************************************************************************************
// Display the spinner. Once visible, display categories.
function displayCategories()
{
  Utility.displaySpinnerThen(doDisplayCategories);
}

// *************************************************************************************************
// Display the list of categories.
function doDisplayCategories()
{
  var o, p, i;
  
  if (categories.length <= 0)
  {
    categoriesBox.innerHTML = '<div class="form-element">' +
      getText(1, 'Det er ikke opprettet noen kategorier enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  o = new Array((categories.length * 5) + 5);
  p = 0;
  
  // Header.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(3, 'Navn'));
  o[p++] = sorting.getTableHeader(1, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < categories.length; i++)
  {
    // Category name.
    o[p++] = '<tr><td>';
    o[p++] = categories[i][c.cat.NAME];
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  categoriesBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, categories))
    return '';
  o = new Array(2);
  p = 0;

  // Edit category button.
  o[p++] = sender.getMenuItem(getText(4, 'Rediger kategori'), 'fa-pen-to-square', true,
    'displayEditCategoryDialogue(' + String(index) + ');');
  // Delete category button.
  o[p++] = sender.getMenuItem(getText(5, 'Slett kategori'), 'fa-trash', true,
    'deleteCategory(' + String(index) + ');');
  return o.join('');
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
    alert(getText(6, 'Denne kategorien kan ikke slettes, fordi det finnes bodtyper som bruker den.'));
    return;
  }

  if (confirm(getText(7, 'Er du sikker på at du vil slette kategori: $1?', [categories[index][c.cat.NAME]])))
  {
    o = new Array(4);
    p = 0;

    o[p++] = '<form id="deleteCategoryForm" action="/subscription/html/admin_categories.php" method="post"><input type="hidden" name="action" value="delete_category" />';
    o[p++] = getPageStateFormElements();
    o[p++] = Utility.getHidden('id', categories[index][c.cat.ID]);
    o[p++] = '</form>';
    editCategoryDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteCategoryForm'));
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
  o = new Array(18);
  p = 0;
  
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(8, 'Opprett kategori');
  else
    o[p++] = getText(4, 'Rediger kategori');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editCategoryForm" action="/subscription/html/admin_categories.php" method="post"><div class="form-element">';
  o[p++] = getPageStateFormElements();
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_category" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_category" />';
    o[p++] = Utility.getHidden('id', categories[index][c.cat.ID]);
  }
  o[p++] = '<label for="categoryNameEdit" class="standard-label">';
  o[p++] = getText(10, 'Navn:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="categoryNameEdit" name="name" class="long-text" onkeyup="enableCategorySubmitButton();" onchange="enableCategorySubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = categories[index][c.cat.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div></form></div><div class="dialogue-footer"><button type="button" id="categorySubmitButton" onclick="Utility.displaySpinnerThenSubmit(editCategoryForm);"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(11, 'Opprett');
  else
    o[p++] = getText(12, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeCategoryDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(13, 'Avbryt');
  o[p++] = '</button></div>';

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
