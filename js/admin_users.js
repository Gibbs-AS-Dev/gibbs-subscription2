// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var filterToolbar, usersBox, overlay, editEntityTypeFilterDialogue, editActiveFilterDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var freetextEdit;

// The sorting object that controls the sorting of the users table.
var sorting;

// The popup menu for the users table.
var menu;

// The number of displayed users. This depends on the current filter settings.
var displayedCount = 0;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['filterToolbar', 'usersBox', 'overlay', 'editEntityTypeFilterDialogue',
    'editActiveFilterDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(users,
      [
        Sorting.createUiColumn(c.usr.ENTITY_TYPE, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.usr.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.usr.EMAIL, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.usr.PHONE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.usr.HAS_ACTIVE_SUBSCRIPTION, Sorting.SORT_AS_BOOLEAN),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayUsers
    );
  doDisplayUsers();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Display the spinner. Once visible, display users.
function displayUsers()
{
  Utility.displaySpinnerThen(doDisplayUsers);
}

// *************************************************************************************************
// Display the list of users.
function doDisplayUsers()
{
  var o, p, i;
  
  if (users.length <= 0)
  {
    usersBox.innerHTML = '<div class="form-element">' +
      getText(8, 'Det er ikke opprettet noen kunder enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((users.length * 15) + 9);
  p = 0;
  
  // Header.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(9, 'Bedrift'));
  o[p++] = sorting.getTableHeader(1, getText(1, 'Navn'));
  o[p++] = sorting.getTableHeader(2, getText(2, 'E-post'));
  o[p++] = sorting.getTableHeader(3, getText(3, 'Telefonnummer'));
  o[p++] = sorting.getTableHeader(4, getText(4, 'Abonnement'));
  o[p++] = sorting.getTableHeader(5, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < users.length; i++)
  {
    if (shouldHide(users[i]))
      continue;
    displayedCount++;

    // Company flag.
    o[p++] = '<tr><td>';
    if (users[i][c.usr.ENTITY_TYPE] === ENTITY_TYPE_COMPANY)
      o[p++] = '<i class="fa-solid fa-check icon-green"></i>';
    else
      o[p++] = '&nbsp;';
    // Name.
    o[p++] = '</td><td>';
    o[p++] = users[i][c.usr.NAME];
    // E-mail.
    o[p++] = '</td><td>';
    o[p++] = users[i][c.usr.EMAIL];
    // Phone number.
    o[p++] = '</td><td>';
    o[p++] = users[i][c.usr.PHONE];
    // Subscription status.
    o[p++] = '</td><td>';
    if (users[i][c.usr.HAS_ACTIVE_SUBSCRIPTION])
    {
      o[p++] = '<span class="status-label status-green">';
      o[p++] = getText(6, 'Aktiv');
      o[p++] = '</span>';
    }
    else
    {
      o[p++] = '<span class="status-label status-red">';
      o[p++] = getText(7, 'Inaktiv');
      o[p++] = '</span>';
    }
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  usersBox.innerHTML = o.join('');
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
  if (!Utility.isValidIndex(index, users))
    return '';
  o = new Array(1);
  p = 0;

  // Edit user button.
  o[p++] = sender.getMenuItem(getText(5, 'Vis kundekort'), 'fa-up-right-from-square', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/admin_edit_user.php?user_id=' +
    String(users[index][c.usr.ID]) + '\');');
  return o.join('');
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
  o[p++] = getText(10, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(11, 'Vis alle');
  o[p++] = '</button>';
  // Entity type filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (entityTypeFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayEntityTypeFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(12, 'Kundetype');
  o[p++] = '</button>';
  // Clear entity type filter button.
  if (entityTypeFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearEntityTypeFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Active filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (activeFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayActiveFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(6, 'Aktiv');
  o[p++] = '</button>';
  // Clear active filter button.
  if (activeFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearActiveFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Freetext filter edit.
  o[p++] = '<input type="text" id="freetextEdit" placeholder="';
  o[p++] = getText(13, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Display counter box.
  o[p++] = '<span class="counter">';
  if (displayedCount === users.length)
    o[p++] = getText(14, 'Viser $1 kunder', [String(users.length)]);
  else
    o[p++] = getText(15, 'Viser $1 av $2 kunder',
      [String(displayedCount), String(users.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['freetextEdit']);
}

// *************************************************************************************************
// Return true if the list of users should not include the given user.
function shouldHide(user)
{
  var activeStatus;

  activeStatus = (user[c.usr.HAS_ACTIVE_SUBSCRIPTION] ? 1 : 0);
  return ((entityTypeFilter !== null) && !entityTypeFilter.includes(user[c.usr.ENTITY_TYPE])) ||
    ((activeFilter !== null) && !activeFilter.includes(activeStatus)) ||
    ((freetextFilter !== '') && !matchesFreetextFilter(user));
}

// *************************************************************************************************

function clearAllFilters()
{
  entityTypeFilter = null;
  activeFilter = null;
  freetextFilter = '';
  freetextEdit.value = '';
  displayUsers();
}

// *************************************************************************************************
// Entity type filter functions.
// *************************************************************************************************
// Return true if the user list is currently filtered on entity type, and the filter includes the
// given entity type.
function inEntityTypeFilter(entityType)
{
  return (entityTypeFilter !== null) && entityTypeFilter.includes(entityType);
}

// *************************************************************************************************

function displayEntityTypeFilterDialogue()
{
  var o, p, i;
  
  o = new Array((ENTITY_TYPE_TEXTS.length * 9) + 8);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(16, 'Velg hvilke kundetyper som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < ENTITY_TYPE_TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="entityType';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inEntityTypeFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> <label for="entityType';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = ENTITY_TYPE_TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" onclick="updateEntityTypeFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeEntityTypeFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editEntityTypeFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editEntityTypeFilterDialogue);
};

// *************************************************************************************************

function clearEntityTypeFilter()
{
  entityTypeFilter = null;
  displayUsers();
}

// *************************************************************************************************

function updateEntityTypeFilter()
{
  var i, checkbox;

  entityTypeFilter = [];
  for (i = 0; i < ENTITY_TYPE_TEXTS.length; i++)
  {
    checkbox = document.getElementById('entityType' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      entityTypeFilter.push(i);
  }
  // If the user unchecks all entity types, instead of displaying nothing, clear the filter. If the
  // user checks all entity types, also clear the filter.
  if ((entityTypeFilter.length === 0) || (entityTypeFilter.length === ENTITY_TYPE_TEXTS.length))
    entityTypeFilter = null;
  closeEntityTypeFilterDialogue();
  displayUsers();
}

// *************************************************************************************************

function closeEntityTypeFilterDialogue()
{
  Utility.hide(editEntityTypeFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Active filter functions.
// *************************************************************************************************
// Return true if the user list is currently filtered on whether or not users have an active
// subscription, and the filter includes the given type (0 for inactive, 1 for active).
function inActiveFilter(isActive)
{
  return (activeFilter !== null) && activeFilter.includes(isActive);
}

// *************************************************************************************************

function displayActiveFilterDialogue()
{
  var o, p, i;
  
  o = new Array((ACTIVE_TEXTS.length * 9) + 8);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(16, 'Velg hvilke kundetyper som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < ACTIVE_TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="active';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inActiveFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> <label for="active';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = ACTIVE_TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" onclick="updateActiveFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeActiveFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editActiveFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editActiveFilterDialogue);
};

// *************************************************************************************************

function clearActiveFilter()
{
  activeFilter = null;
  displayUsers();
}

// *************************************************************************************************

function updateActiveFilter()
{
  var i, checkbox;

  activeFilter = [];
  for (i = 0; i < ACTIVE_TEXTS.length; i++)
  {
    checkbox = document.getElementById('active' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      activeFilter.push(i);
  }
  // If the user unchecks both active or inactive users, instead of displaying nothing, clear the
  // filter. If the user checks all types, also clear the filter.
  if ((activeFilter.length === 0) || (activeFilter.length === ACTIVE_TEXTS.length))
    activeFilter = null;
  closeActiveFilterDialogue();
  displayUsers();
}

// *************************************************************************************************

function closeActiveFilterDialogue()
{
  Utility.hide(editActiveFilterDialogue);
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
  displayUsers();
}

// *************************************************************************************************
// Return true if the given user matches the current freetext filter.
function matchesFreetextFilter(user)
{
  var filter;

  filter = freetextFilter.toLowerCase();
  // If there is no filter (or no user), everything matches. Otherwise, return a match if the user's
  // name, e-mail or phone number fields contain the filter text.
  return (user === null) || (filter === '') ||
    (user[c.usr.NAME].toLowerCase().indexOf(filter) >= 0) ||
    (user[c.usr.EMAIL].toLowerCase().indexOf(filter) >= 0) ||
    (user[c.usr.PHONE].toLowerCase().indexOf(filter) >= 0);
}

// *************************************************************************************************
