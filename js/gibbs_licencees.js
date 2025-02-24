// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// Role numbers.
var ROLE_ADMIN = 1;
var ROLE_USER = 0;

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var licenceesBox, overlay, createUserGroupDialogue, addLicenceDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var createUserGroupForm, userGroupNameEdit, createUserGroupSubmitButton, addLicenceForm,
  addLicenceSubmitButton, userGroupCombo;

// The sorting object that controls the sorting of the licencees table.
var sorting;

// The popup menu for the licencees table.
var menu;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['licenceesBox', 'overlay', 'createUserGroupDialogue',
    'addLicenceDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(licencees,
      [
        Sorting.createUiColumn(c.lic.USER_GROUP_ID, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.lic.USER_GROUP_USER_ID, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.lic.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.lic.IS_ACTIVE, Sorting.SORT_AS_BOOLEAN),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayLicencees
    );
  // Set the initial sorting. If that didn't cause licencees to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayLicencees();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert('An error occurred. Please inform your pet programmer. Error code: ' +
      String(resultCode) + '. Timestamp: ' + TIMESTAMP);
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
// Display the spinner. Once visible, display licencees.
function displayLicencees()
{
  Utility.displaySpinnerThen(doDisplayLicencees);
}

// *************************************************************************************************
// Display the list of licencees.
function doDisplayLicencees()
{
  var o, p, i;
  
  if (licencees.length <= 0)
  {
    licenceesBox.innerHTML = '<div class="form-element">You haven\'t sold a single licence yet. Get to work, man!</div>';
    Utility.hideSpinner();
    return;
  }

  o = new Array((licencees.length * 11) + 8);
  p = 0;
  
  // Header.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, 'User group ID');
  o[p++] = sorting.getTableHeader(1, 'Dummy user ID');
  o[p++] = sorting.getTableHeader(2, 'User group name');
  o[p++] = sorting.getTableHeader(3, 'Licence status');
  o[p++] = sorting.getTableHeader(4, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < licencees.length; i++)
  {
    // User group ID.
    o[p++] = '<tr><td>';
    o[p++] = String(licencees[i][c.lic.USER_GROUP_ID]);
    // Dummy user ID.
    o[p++] = '</td><td>';
    o[p++] = String(licencees[i][c.lic.USER_GROUP_USER_ID]);
    // User group name.
    o[p++] = '</td><td>';
    o[p++] = licencees[i][c.lic.NAME];
    // Licence status.
    o[p++] = '</td><td>';
    if (licencees[i][c.lic.IS_ACTIVE])
      o[p++] = '<span class="status-label status-green">Active</span>';
    else
      o[p++] = '<span class="status-label status-red">Inactive</span>';
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  licenceesBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, licencees))
    return '';
  o = new Array(4);
  p = 0;

  // Visit as admin button.
  o[p++] = sender.getMenuItem('Go to as admin', 'fa-user-plus', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/set_user_group.php?user_group_id=' +
    String(licencees[index][c.lic.USER_GROUP_ID]) + '&role=' + String(ROLE_ADMIN) + '\');');
  // Visit as user button.
  o[p++] = sender.getMenuItem('Go to as user', 'fa-user-plus', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/set_user_group.php?user_group_id=' +
    String(licencees[index][c.lic.USER_GROUP_ID]) + '&role=' + String(ROLE_USER) + '\');');
  // Toggle licence button.
  o[p++] = sender.getMenuItem('Toggle licence', 'fa-repeat', true,
    'toggleLicence(' + String(index) + ');');
  // Delete licence button.
  o[p++] = sender.getMenuItem('Delete licence', 'fa-trash', true,
    'deleteLicence(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************
// *** User group functions.
// *************************************************************************************************

function displayCreateUserGroupDialogue()
{
  var o, p;

  o = new Array(7);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>Create user group</h1></div><div class="dialogue-content"><form id="createUserGroupForm" action="/subscription/html/gibbs_licencees.php" method="post"><div class="form-element"><input type="hidden" name="action" value="create_user_group" />';
  o[p++] = getPageStateFormElements();
  o[p++] = '<span class="help-text">This will create a user group and its dummy user, and give it an active Gibbs self storage licence.</span></div>';
  o[p++] = '<div class="form-element"><label for="userGroupNameEdit" class="standard-label">Name:';
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="userGroupNameEdit" name="name" class="long-text" onkeyup="enableCreateUserGroupSubmitButton();" onchange="enableCreateUserGroupSubmitButton();" />';
  o[p++] = '</div></form></div><div class="dialogue-footer"><button type="button" id="createUserGroupSubmitButton" onclick="createUserGroupForm.submit();"><i class="fa-solid fa-check"></i> Create</button> <button type="button" onclick="closeCreateUserGroupDialogue();"><i class="fa-solid fa-xmark"></i> Cancel</button></div>';

  createUserGroupDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['createUserGroupForm', 'userGroupNameEdit', 'createUserGroupSubmitButton']);

  Utility.display(overlay);
  Utility.display(createUserGroupDialogue);
  enableCreateUserGroupSubmitButton();
}

// *************************************************************************************************

function closeCreateUserGroupDialogue()
{
  Utility.hide(createUserGroupDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableCreateUserGroupSubmitButton()
{
  createUserGroupSubmitButton.disabled = (userGroupNameEdit.value === '') ||
    userGroupExists(userGroupNameEdit.value);
}

// *************************************************************************************************
// Return true if a user group with the given name already exists.
function userGroupExists(name)
{
  var i;

  name = String(name).toLowerCase();
  for (i = 0; i < userGroups.length; i++)
  {
    if (userGroups[i][c.ugr.NAME].toLowerCase() === name)
      return true;
  }
  return false;
}

// *************************************************************************************************
// *** Licence functions.
// *************************************************************************************************

function toggleLicence(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, licencees))
    return;
  o = new Array(6);
  p = 0;

  o[p++] = '<form id="toggleLicenceForm" action="/subscription/html/gibbs_licencees.php" method="post"><input type="hidden" name="action" value="update_licence" />';
  o[p++] = Utility.getHidden('id', licencees[index][c.lic.ID]);
  o[p++] = Utility.getHidden('user_group_id', licencees[index][c.lic.USER_GROUP_ID]);
  o[p++] = Utility.getHidden('licence_id', licencees[index][c.lic.LICENCE_ID]);
  o[p++] = Utility.getHidden('is_active', (licencees[index][c.lic.IS_ACTIVE] ? 'false' : 'true'));
  o[p++] = '</form>';
  addLicenceDialogue.innerHTML = o.join('');
  Utility.displaySpinnerThenSubmit(document.getElementById('toggleLicenceForm'));
}

// *************************************************************************************************

function deleteLicence(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, licencees) &&
    confirm('Delete licence for user group: ' + licencees[index][c.lic.NAME] + '. Are you sure?'))
  {
    o = new Array(3);
    p = 0;

    o[p++] = '<form id="deleteLicenceForm" action="/subscription/html/gibbs_licencees.php" method="post"><input type="hidden" name="action" value="delete_licence" />';
    o[p++] = Utility.getHidden('id', licencees[index][c.lic.ID]);
    o[p++] = '</form>';
    addLicenceDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteLicenceForm'));
  }
}

// *************************************************************************************************
// Return true if the user group with the given ID already has a licence.
function hasLicence(userGroupId)
{
  var i;

  for (i = 0; i < licencees.length; i++)
  {
    if (licencees[i][c.lic.USER_GROUP_ID] === userGroupId)
      return true;
  }
  return false;
}

// *************************************************************************************************
// Ask the user to select a user group that does not currently have a licence, in order to add one.
function displayAddLicenceDialogue()
{
  var o, p, i;

  o = new Array((userGroups.length * 5) + 5);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>Grant licence</h1></div><div class="dialogue-content"><form id="addLicenceForm" action="/subscription/html/gibbs_licencees.php" method="post"><div class="form-element"><input type="hidden" name="action" value="create_licence" />';
  o[p++] = getPageStateFormElements();
  o[p++] = Utility.getHidden('licence_id', gibbsAbonnementLicenceId);
  o[p++] = '<span class="help-text">This will grant an active Gibbs self storage licence to an existing user group.</span></div><div class="form-element"><input type="hidden" name="is_active" value="true" /><label for="userGroupCombo" class="standard-label">User group:</label> <select id="userGroupCombo" name="user_group_id" class="long-text" onchange="enableAddLicenceSubmitButton();">';
  for (i = 0; i < userGroups.length; i++)
  {
    if (!hasLicence(userGroups[i][c.ugr.ID]))
    {
      o[p++] = '<option value="';
      o[p++] = String(userGroups[i][c.ugr.ID]);
      o[p++] = '">';
      o[p++] = userGroups[i][c.ugr.NAME];
      o[p++] = '</option>';
    }
  }
  o[p++] = '</select></div></form></div><div class="dialogue-footer"><button type="button" id="addLicenceSubmitButton" onclick="addLicenceForm.submit();"><i class="fa-solid fa-check"></i> Grant</button> <button type="button" onclick="closeAddLicenceDialogue();"><i class="fa-solid fa-xmark"></i> Cancel</button></div>';

  addLicenceDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['addLicenceForm', 'addLicenceSubmitButton', 'userGroupCombo']);

  Utility.display(overlay);
  Utility.display(addLicenceDialogue);
  enableAddLicenceSubmitButton();
}

// *************************************************************************************************

function closeAddLicenceDialogue()
{
  Utility.hide(addLicenceDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableAddLicenceSubmitButton()
{
  addLicenceSubmitButton.disabled = (userGroupCombo === null) || (userGroupCombo.selectedIndex < 0);
}

// *************************************************************************************************
