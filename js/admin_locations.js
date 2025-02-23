// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var locationsBox, overlay, editLocationDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editLocationForm, submitButton, nameEdit, streetEdit, postalCodeEdit, townEdit, countryEdit;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['locationsBox', 'overlay', 'editLocationDialogue']);

  displayLocations();

  // Display the results of a previous operation, if required.
  if (resultCode >= 0)
    alert(getText(6, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
      [String(resultCode)]));
}

// *************************************************************************************************

function displayLocations()
{
  var o, p, i;
  
  if (locations.length <= 0)
  {
    locationsBox.innerHTML = '<div class="form-element">' +
      getText(14, 'Det er ikke opprettet noen lager enn&aring;.') + '</div>';
    return;
  }

  o = new Array((locations.length * 15) + 16);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(0, 'Navn');
  o[p++] = '</th><th>';
  o[p++] = getText(1, 'Adresse');
  o[p++] = '</th><th>';
  o[p++] = getText(20, '&Aring;pningstider');
  o[p++] = '</th><th>';
  o[p++] = getText(21, 'Tjenester');
  o[p++] = '</th><th>';
  o[p++] = getText(2, 'Rediger');
  o[p++] = '</th><th>';
  o[p++] = getText(3, 'Slett');
  o[p++] = '</th><th>';
  o[p++] = getText(4, 'Se lagerboder');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</td><td>';
    o[p++] = Utility.getAddress(locations[i]);
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(locations[i][c.loc.OPENING_HOURS], 25);
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(locations[i][c.loc.SERVICES], 25);
    o[p++] = '</td><td><button type="button" class="icon-button" onclick="displayEditLocationDialogue(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-pen-to-square"></i></button></td><td><button type="button" class="icon-button" onclick="deleteLocation(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-trash"></i></button></td><td><a href="/subscription/html/admin_products.php?location_filter=[';
    o[p++] = locations[i][c.loc.ID];
    o[p++] = ']" class="button icon-button"><i class="fa-solid fa-list"></i></a></td></tr>';
  }
  o[p++] = '</tbody></table>';

  locationsBox.innerHTML = o.join('');
}

// *************************************************************************************************

function deleteLocation(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, locations) &&
    confirm(getText(5, 'Er du sikker på at du vil slette lager: $1?', [locations[index][c.loc.NAME]])))
  {
    o = new Array(3);
    p = 0;

    o[p++] = '<form id="deleteLocationForm" action="/subscription/html/admin_locations.php" method="post"><input type="hidden" name="action" value="delete_location" /><input type="hidden" name="id" value="';
    o[p++] = String(locations[index][c.loc.ID]);
    o[p++] = '" /></form>';
    editLocationDialogue.innerHTML = o.join('');
    document.getElementById('deleteLocationForm').submit();
  }
}

// *************************************************************************************************

function displayEditLocationDialogue(index)
{
  var o, p, isNew;
  
  index = parseInt(index, 10);
  isNew = index === -1;
  if (!(isNew || Utility.isValidIndex(index, locations)))
    return;
  o = new Array(53);
  p = 0;
  
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(7, 'Opprett lager');
  else
    o[p++] = getText(8, 'Rediger lager');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editLocationForm" action="/subscription/html/admin_locations.php" method="post"><div class="form-element">';
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_location" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_location" /><input type="hidden" name="id" value="';
    o[p++] = String(locations[index][c.loc.ID]);
    o[p++] = '" />';
  }
  o[p++] = '<label for="nameEdit" class="standard-label">';
  o[p++] = getText(9, 'Navn:');
  o[p++] = '</label> <input type="text" id="nameEdit" name="name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = locations[index][c.loc.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="streetEdit" class="standard-label">';
  o[p++] = getText(10, 'Adresse:');
  o[p++] = '</label> <input type="text" id="streetEdit" name="address" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.STREET]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="postalCodeEdit" class="standard-label">';
  o[p++] = getText(15, 'Postnummer:');
  o[p++] = '</label> <input type="text" id="postalCodeEdit" name="postal_code" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.POSTAL_CODE]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="townEdit" class="standard-label">';
  o[p++] = getText(16, 'Poststed:');
  o[p++] = '</label> <input type="text" id="townEdit" name="town" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.TOWN]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="countryEdit" class="standard-label">';
  o[p++] = getText(17, 'Land:');
  o[p++] = '</label> <input type="text" id="countryEdit" name="country" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.COUNTRY]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="openingHoursEdit" class="standard-label">';
  o[p++] = getText(18, '&Aring;pningstider:');
  o[p++] = '</label> <input type="text" id="openingHoursEdit" name="opening_hours" class="long-text"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.OPENING_HOURS]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="servicesEdit" class="standard-label">';
  o[p++] = getText(19, 'Tjenester:');
  o[p++] = '</label> <input type="text" id="servicesEdit" name="services" class="long-text"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.SERVICES]);
    o[p++] = '"';
  }
  o[p++] = ' /></div></form></div><div class="dialogue-footer"><button type="button" id="submitButton" onclick="editLocationForm.submit();"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(11, 'Opprett');
  else
    o[p++] = getText(12, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeLocationDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(13, 'Avbryt');
  o[p++] = '</button></div>';

  editLocationDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editLocationForm', 'submitButton', 'nameEdit', 'streetEdit',
    'postalCodeEdit', 'townEdit', 'countryEdit']);

  Utility.display(overlay);
  Utility.display(editLocationDialogue);
  enableSubmitButton();
}

// *************************************************************************************************

function closeLocationDialogue()
{
  Utility.hide(editLocationDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableSubmitButton()
{
  submitButton.disabled = (nameEdit.value === '') || (streetEdit.value === '') ||
    (postalCodeEdit.value === '') || (townEdit.value === '') || (countryEdit.value === '');
}

// *************************************************************************************************
