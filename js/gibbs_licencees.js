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
var licenceesBox, overlay, addLicenceDialogue, editUserGroupDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var addLicenceForm, addLicenceSubmitButton, userGroupCombo;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['licenceesBox', 'overlay', 'addLicenceDialogue', 'editUserGroupDialogue']);

  displayLicencees();

  // Display the results of a previous operation, if required.
  if (resultCode >= 0)
    alert('An error occurred. Please inform your pet programmer. Error code: ' + String(resultCode));
}

// *************************************************************************************************

function displayLicencees()
{
  var o, p, i;
  
  if (licencees.length <= 0)
  {
    licenceesBox.innerHTML = '<div class="form-element">You haven\'t sold a single licence yet. Get to work, man!</div>';
    return;
  }

  o = new Array((licencees.length * 17) + 2);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>User group name</th><th>Status</th><th>Toggle | Delete | Go to as admin / user</th></tr></thead><tbody>';
  for (i = 0; i < licencees.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = licencees[i][c.lic.NAME];
    o[p++] = '</td><td>';
    if (licencees[i][c.lic.IS_ACTIVE])
      o[p++] = '<span class="status-label status-green">Active</span>';
    else
      o[p++] = '<span class="status-label status-red">Inactive</span>';
    o[p++] = '</td><td><button type="button" class="icon-button" onclick="toggleLicence(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-repeat"></i></button> <button type="button" class="icon-button" onclick="deleteLicence(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-trash"></i></button> <a href="/subscription/html/set_user_group.php?user_group_id=';
    o[p++] = String(licencees[i][c.lic.USER_GROUP_ID]);
    o[p++] = '&role=';
    o[p++] = String(ROLE_ADMIN);
    o[p++] = '" class="button icon-button"><i class="fa-solid fa-user-plus"></i></a> <a href="/subscription/html/set_user_group.php?user_group_id=';
    o[p++] = String(licencees[i][c.lic.USER_GROUP_ID]);
    o[p++] = '&role=';
    o[p++] = String(ROLE_USER);
    o[p++] = '" class="button icon-button"><i class="fa-solid fa-user"></i></a></td></tr>';
  }
  o[p++] = '</tbody></table>';

  licenceesBox.innerHTML = o.join('');
}

// *************************************************************************************************

function toggleLicence(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, licencees))
    return;
  o = new Array(9);
  p = 0;

  o[p++] = '<form id="toggleLicenceForm" action="/subscription/html/gibbs_licencees.php" method="post"><input type="hidden" name="action" value="update_licence" /><input type="hidden" name="id" value="';
  o[p++] = String(licencees[index][c.lic.ID]);
  o[p++] = '" /><input type="hidden" name="user_group_id" value="';
  o[p++] = String(licencees[index][c.lic.USER_GROUP_ID]);
  o[p++] = '" /><input type="hidden" name="licence_id" value="';
  o[p++] = String(licencees[index][c.lic.LICENCE_ID]);
  o[p++] = '" /><input type="hidden" name="is_active" value="';
  if (licencees[index][c.lic.IS_ACTIVE])
    o[p++] = 'false';
  else
    o[p++] = 'true';
  o[p++] = '" /></form>';
  addLicenceDialogue.innerHTML = o.join('');
  document.getElementById('toggleLicenceForm').submit();
}

// *************************************************************************************************

function deleteLicence(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, licencees) &&
    confirm('Delete licence for user group ' + licencees[index][c.lic.NAME] + '? Are you sure?'))
  {
    o = new Array(3);
    p = 0;

    o[p++] = '<form id="deleteLicenceForm" action="/subscription/html/gibbs_licencees.php" method="post"><input type="hidden" name="action" value="delete_licence" /><input type="hidden" name="id" value="';
    o[p++] = String(licencees[index][c.lic.ID]);
    o[p++] = '" /></form>';
    addLicenceDialogue.innerHTML = o.join('');
    document.getElementById('deleteLicenceForm').submit();
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

  o = new Array((userGroups.length * 5) + 4);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>Grant licence</h1></div><div class="dialogue-content"><form id="addLicenceForm" action="/subscription/html/gibbs_licencees.php" method="post"><div class="form-element"><input type="hidden" name="action" value="create_licence" /><input type="hidden" name="licence_id" value="';
  o[p++] = String(gibbsAbonnementLicenceId);
  o[p++] = '" /><input type="hidden" name="is_active" value="true" /><label for="userGroupCombo" class="standard-label">User group:</label> <select id="userGroupCombo" name="user_group_id" class="long-text" onchange="enableAddLicenceSubmitButton();">';
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
