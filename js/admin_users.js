// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var usersBox, displayActiveUsersCheckbox, displayInactiveUsersCheckbox;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['usersBox', 'displayActiveUsersCheckbox', 'displayInactiveUsersCheckbox']);

  displayUsers();

  // Display the results of a previous operation, if required.
  if (resultCode >= 0)
    alert('Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode ' +
      String(resultCode) + '.');
}

// *************************************************************************************************

function displayUsers()
{
  var o, p, i, displayActiveUsers, displayInactiveUsers;
  
  displayActiveUsers = displayActiveUsersCheckbox.checked;
  displayInactiveUsers = displayInactiveUsersCheckbox.checked;
  o = new Array((users.length * 11) + 2);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>Navn</th><th>E-post</th><th>Telefonnummer</th><th>Abonnement</th><th>Rediger</th></tr></thead><tbody>';
  for (i = 0; i < users.length; i++)
  {
    if (users[i][c.usr.HAS_ACTIVE_SUBSCRIPTION])
    {
      if (!displayActiveUsers) continue;
    }
    else
    {
      if (!displayInactiveUsers) continue;
    }
    o[p++] = '<tr><td>';
    o[p++] = users[i][c.usr.NAME];
    o[p++] = '</td><td>';
    o[p++] = users[i][c.usr.EMAIL];
    o[p++] = '</td><td>';
    o[p++] = users[i][c.usr.PHONE];
    o[p++] = '</td><td>';
    if (users[i][c.usr.HAS_ACTIVE_SUBSCRIPTION])
      o[p++] = '<span class="status-label status-green">Aktiv</span>';
    else
      o[p++] = '<span class="status-label status-red">Inaktiv</span>';
    o[p++] = '</td><td><a href="/subscription/html/admin_edit_user.php?user_id=';
    o[p++] = users[i][c.usr.ID];
    o[p++] = '" class="button icon-button"><i class="fa-solid fa-repeat"></i></a></td></tr>';
  }
  o[p++] = '</tbody></table>';

  usersBox.innerHTML = o.join('');
}

// *************************************************************************************************
