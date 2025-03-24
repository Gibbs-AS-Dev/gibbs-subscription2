// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var locationsBox, overlay, bookingUrlDialogue, bookingUrlEdit, editLocationDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editLocationForm, submitButton, nameEdit, streetEdit, postcodeEdit, townEdit, countryEdit;

// The sorting object that controls the sorting of the locations table.
var sorting;

// The popup menu for the locations table.
var menu;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['locationsBox', 'overlay', 'bookingUrlDialogue', 'bookingUrlEdit',
    'editLocationDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents, 250);

  // Initialise sorting.
  sorting = new Sorting(locations,
      [
        Sorting.createUiColumn(c.loc.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.loc.STREET, Sorting.SORT_AS_STRING,
          function (location)
          {
            return Utility.getAddress(location);
          }),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayLocations
    );
  // Set the initial sorting. If that didn't cause locations to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayLocations();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert(getText(6, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
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
// Display the spinner. Once visible, display locations.
function displayLocations()
{
  Utility.displaySpinnerThen(doDisplayLocations);
}

// *************************************************************************************************
// Display the list of locations.
function doDisplayLocations()
{
  var o, p, i;
  
  if (locations.length <= 0)
  {
    locationsBox.innerHTML = '<div class="form-element">' +
      getText(14, 'Det er ikke opprettet noen lager enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  o = new Array((locations.length * 11) + 8);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(0, 'Navn'));
  o[p++] = sorting.getTableHeader(1, getText(1, 'Adresse'));
  o[p++] = sorting.getTableHeader(2, getText(20, '&Aring;pningstider'));
  o[p++] = sorting.getTableHeader(3, getText(21, 'Tjenester'));
  o[p++] = sorting.getTableHeader(4, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < locations.length; i++)
  {
    // Location name.
    o[p++] = '<tr><td>';
    o[p++] = locations[i][c.loc.NAME];
    // Address.
    o[p++] = '</td><td>';
    o[p++] = Utility.getAddress(locations[i]);
    // Opening hours.
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(locations[i][c.loc.OPENING_HOURS], 25);
    // Services.
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(locations[i][c.loc.SERVICES], 25);
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  locationsBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, locations))
    return '';
  o = new Array(5);
  p = 0;

  // Edit location button.
  o[p++] = sender.getMenuItem(getText(2, 'Rediger lager'), 'fa-pen-to-square', true,
    'displayEditLocationDialogue(' + String(index) + ');');
  // Delete location button.
  o[p++] = sender.getMenuItem(getText(3, 'Slett lager'), 'fa-trash', true,
    'deleteLocation(' + String(index) + ');');
  // Display products button.
  o[p++] = sender.getMenuItem(getText(4, 'Vis lagerboder'), 'fa-list', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/admin_products.php?location_filter=' +
    String(locations[index][c.loc.ID]) + '\');');
  // Book subscription here.
  o[p++] = sender.getMenuItem(getText(22, 'Opprett abonnement her'), 'fa-plus', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/admin_book_subscription.php?initial_location_id=' +
    String(locations[index][c.loc.ID]) + '\');');
  // Display URL to book from this location as a customer.
  o[p++] = sender.getMenuItem(getText(23, 'URL for bestilling'), 'fa-link', true,
    'displayBookingUrl(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************
// Display the URL that customers would use to open book_subscription with the location with the
// given index in the locations table pre-selected.
function displayBookingUrl(index)
{
  if (Utility.isValidIndex(index, locations))
  {
    bookingUrlEdit.value = bookingUrl + '&location_id=' + String(locations[index][c.loc.ID]);
    Utility.display(overlay);
    Utility.display(bookingUrlDialogue);
  }
}

// *************************************************************************************************

function closeBookingUrlDialogue()
{
  Utility.hide(bookingUrlDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function deleteLocation(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, locations) &&
    confirm(getText(5, 'Er du sikker på at du vil slette lager: $1?', [locations[index][c.loc.NAME]])))
  {
    o = new Array(4);
    p = 0;

    o[p++] = '<form id="deleteLocationForm" action="/subscription/html/admin_locations.php" method="post"><input type="hidden" name="action" value="delete_location" />';
    o[p++] = getPageStateFormElements();
    o[p++] = Utility.getHidden('id', locations[index][c.loc.ID]);
    o[p++] = '</form>';
    editLocationDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteLocationForm'));
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
  o = new Array(58);
  p = 0;
  
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(7, 'Opprett lager');
  else
    o[p++] = getText(8, 'Rediger lager');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editLocationForm" action="/subscription/html/admin_locations.php" method="post"><div class="form-element">';
  o[p++] = getPageStateFormElements();
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_location" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_location" />';
    o[p++] = Utility.getHidden('id', locations[index][c.loc.ID]);
  }
  o[p++] = '<label for="nameEdit" class="standard-label">';
  o[p++] = getText(9, 'Navn:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="nameEdit" name="name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = locations[index][c.loc.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="streetEdit" class="standard-label">';
  o[p++] = getText(10, 'Adresse:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="streetEdit" name="address" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.STREET]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="postcodeEdit" class="standard-label">';
  o[p++] = getText(15, 'Postnr:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="postcodeEdit" name="postcode" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.POSTCODE]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="townEdit" class="standard-label">';
  o[p++] = getText(16, 'Poststed:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="text" id="townEdit" name="town" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = String(locations[index][c.loc.TOWN]);
    o[p++] = '"';
  }
  o[p++] = ' /></div><div class="form-element"><label for="countryEdit" class="standard-label">';
  o[p++] = getText(17, 'Land:');
  o[p++] = Utility.getMandatoryMark();
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
  o[p++] = ' /></div></form></div><div class="dialogue-footer"><button type="button" id="submitButton" onclick="Utility.displaySpinnerThenSubmit(editLocationForm);"><i class="fa-solid fa-check"></i> ';
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
    'postcodeEdit', 'townEdit', 'countryEdit']);

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
    (postcodeEdit.value === '') || (townEdit.value === '') || (countryEdit.value === '');
}

// *************************************************************************************************
