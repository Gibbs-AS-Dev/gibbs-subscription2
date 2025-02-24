// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var filterToolbar, requestsBox, overlay, editRequestDialogue, userNotesDialogue,
  editStatusFilterDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editRequestForm, startDateEdit, openCalendarButton, closeCalendarButton, calendarBox,
  submitButton, userNotesTextArea, freetextEdit;

// The sorting object that controls the sorting of the requests table.
var sorting;

// The popup menu for the requests table.
var menu;

// The calendar component that allows the user to select the desired starting date for his
// subscription.
var calendar;

// The number of displayed requests. This depends on the current filter settings.
var displayedCount = 0;

// Object that holds user notes for various users. The user's ID is used as the index. If a user's
// notes have not been loaded yet, there will be no entry for that user's ID.
var userNotes = {};

// Flag that says whether an alert error message has already been displayed. If so, we should not
// display another.
var errorDisplayed = false;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['filterToolbar', 'requestsBox', 'overlay', 'editRequestDialogue',
    'userNotesDialogue', 'editStatusFilterDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(requests,
      [
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(c.req.USER_ID, Sorting.SORT_AS_STRING,
          function (request)
          {
            var user;

            user = getUser(request[c.req.USER_ID]);
            if (user === null)
              return '';
            return user[c.rqu.NAME];
          }),
        Sorting.createUiColumn(c.req.LOCATION_ID, Sorting.SORT_AS_STRING,
          function (request)
          {
            return Utility.getLocationName(request[c.req.LOCATION_ID]);
          }),
        Sorting.createUiColumn(c.req.CATEGORY_ID, Sorting.SORT_AS_STRING,
          function (request)
          {
            return Utility.getCategoryName(request[c.req.CATEGORY_ID]);
          }),
        Sorting.createUiColumn(c.req.START_DATE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.req.STATUS, Sorting.SORT_AS_STRING,
          function (request)
          {
            return REQUEST_STATUS_TEXTS[request[c.req.STATUS]];
          }),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayRequests
    );
  // Set the initial sorting. If that didn't cause requests to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayRequests();

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

  o = new Array(3);
  p = 0;

  if (statusFilter !== null)
    o[p++] = Utility.getHidden('status_filter', statusFilter.join(','));
  if (freetextFilter !== '')
    o[p++] = Utility.getHidden('freetext_filter', freetextFilter);
  o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// Display the spinner. Once visible, display requests.
function displayRequests()
{
  Utility.displaySpinnerThen(doDisplayRequests);
}

// *************************************************************************************************
// Display the list of requests.
function doDisplayRequests()
{
  var o, p, i, user;
  
  if (requests.length <= 0)
  {
    requestsBox.innerHTML = '<div class="form-element">' +
      getText(1, 'Det er ikke kommet inn noen foresp&oslash;rsler enn&aring;.') + '</div>';
    filterToolbar.innerHTML = '&nbsp;';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((requests.length * 15) + 10);
  p = 0;

  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(2, 'Kommentar'));
  o[p++] = sorting.getTableHeader(1, getText(3, 'Navn'));
  o[p++] = sorting.getTableHeader(2, getText(6, '&Oslash;nsket lager'));
  o[p++] = sorting.getTableHeader(3, getText(7, '&Oslash;nsket kategori'));
  o[p++] = sorting.getTableHeader(4, getText(8, '&Oslash;nsket startdato'));
  o[p++] = sorting.getTableHeader(5, getText(9, 'Status'));
  o[p++] = sorting.getTableHeader(6, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < requests.length; i++)
  {
    if (shouldHide(requests[i])) continue;

    // Find the user that submitted this request.
    user = getUser(requests[i][c.req.USER_ID]);
    // If the user was not found, do not display the request. This should not happen.
    if (user === null)
      continue;
    displayedCount++;

    // Comment.
    o[p++] = '<tr><td>';
    o[p++] = Utility.curtail(requests[i][c.req.COMMENT], 25);
    // Name.
    o[p++] = '</td><td>';
    o[p++] = user[c.rqu.NAME];
    // Location name, if available.
    o[p++] = '</td><td>';
    o[p++] = Utility.getLocationName(requests[i][c.req.LOCATION_ID]);
    // Category name, if available.
    o[p++] = '</td><td>';
    o[p++] = Utility.getCategoryName(requests[i][c.req.CATEGORY_ID]);
    // Start date, if available.
    o[p++] = '</td><td>';
    if (requests[i][c.req.START_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = requests[i][c.req.START_DATE];
    // Status.
    o[p++] = '</td><td>';
    o[p++] = Utility.getStatusLabel(REQUEST_STATUS_TEXTS, st.req.COLOURS,
      requests[i][c.req.STATUS]);
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  requestsBox.innerHTML = o.join('');
  displayFilterToolbar();
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p, user;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, requests))
    return '';
  // Find the user that submitted this request.
  user = getUser(requests[index][c.req.USER_ID]);
  if (user === null)
    return '';
  o = new Array(4);
  p = 0;

  // Edit request button.
  o[p++] = sender.getMenuItem(getText(12, 'Rediger foresp&oslash;rsel'), 'fa-pen-to-square', true,
    'displayEditRequestDialogue(' + String(index) + ');');
  // View notes button.
  o[p++] = sender.getMenuItem(getText(10, 'Se notater'), 'fa-user-pen', true,
    'loadUserNotes(' + user[c.rqu.ID] + ');');
  // Delete request button.
  o[p++] = sender.getMenuItem(getText(38, 'Slett foresp&oslash;rsel'), 'fa-trash', true,
    'deleteRequest(' + String(index) + ');');
  // Book subscription button.
  o[p++] = sender.getMenuItem(getText(39, 'Bestill lagerbod'), 'fa-plus', true,
    'bookSubscription(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************
// Return the user in the requestUsers table with the given user ID, or null if it was not found.
function getUser(id)
{
  var i;

  for (i = 0; i < requestUsers.length; i++)
  {
    if (requestUsers[i][c.rqu.ID] === id)
      return requestUsers[i];
  }
  return null;
}

// *************************************************************************************************

function displayEditRequestDialogue(index)
{
  var o, p, i, user;

  // Find the user that submitted this request.
  user = getUser(requests[index][c.req.USER_ID]);
  // If the user was not found, do not display the edit box. This should not happen.
  if (user === null)
    return;

  o = new Array((locations.length * 7) + (categories.length * 7) +
    (REQUEST_STATUS_TEXTS.length * 7) + 61);
  p = 0;

  // Headline.
  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(12, 'Rediger foresp&oslash;rsel');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editRequestForm" action="/subscription/html/admin_requests.php" method="post"><div class="form-element">';
  o[p++] = getPageStateFormElements();
  o[p++] = '<input type="hidden" name="action" value="update_request" />';
  o[p++] = Utility.getHidden('id', requests[index][c.req.ID]);
  // Description.
  o[p++] = '<label for="descriptionEdit" class="wide-label">';
  o[p++] = getText(4, 'Beskrivelse:');
  o[p++] = '</label><br /><textarea id="descriptionEdit" name="comment">';
  o[p++] = requests[index][c.req.COMMENT];
  // Name.
  o[p++] = '</textarea></div><div class="form-element"><label for="nameEdit" class="wide-label">';
  if (user[c.rqu.ENTITY_TYPE] === ENTITY_TYPE_COMPANY)
    o[p++] = getText(32, 'Selskapets navn:');
  else
    o[p++] = getText(5, 'Navn:');
  o[p++] = '</label> <input type="text" id="nameEdit" class="long-text" readonly="readonly" value="';
  o[p++] = user[c.rqu.NAME];
  // Phone number.
  o[p++] = '" /></div><div class="form-element"><label for="phoneEdit" class="wide-label">';
  o[p++] = getText(13, 'Telefonnr:');
  o[p++] = '</label> <input type="text" id="phoneEdit" class="long-text" readonly="readonly" value="';
  o[p++] = user[c.rqu.PHONE];
  // E-mail.
  o[p++] = '" /></div><div class="form-element"><label for="eMailEdit" class="wide-label">';
  o[p++] = getText(14, 'E-post:');
  o[p++] = '</label> <input type="text" id="eMailEdit" class="long-text" readonly="readonly" value="';
  o[p++] = user[c.rqu.EMAIL];
  // Address.
  o[p++] = '" /></div><div class="form-element"><label for="addressEdit" class="wide-label">';
  o[p++] = getText(15, 'Adresse:');
  o[p++] = '</label> <input type="text" id="addressEdit" class="long-text" readonly="readonly" value="';
  o[p++] = user[c.rqu.ADDRESS];
  // Postcode.
  o[p++] = '" /></div><div class="form-element"><label for="postcodeEdit" class="wide-label">';
  o[p++] = getText(16, 'Postnummer:');
  o[p++] = '</label> <input type="text" id="postcodeEdit" class="long-text" readonly="readonly" value="';
  o[p++] = user[c.rqu.POSTCODE];
  // Area.
  o[p++] = '" /></div><div class="form-element"><label for="areaEdit" class="wide-label">';
  o[p++] = getText(17, 'Poststed:');
  o[p++] = '</label> <input type="text" id="areaEdit" class="long-text" readonly="readonly" value="';
  o[p++] = user[c.rqu.AREA];
  // Desired location.
  o[p++] = '" /></div><div class="form-element"><label for="locationCombo" class="wide-label">';
  o[p++] = getText(18, '&Oslash;nsket lager:');
  o[p++] = '</label> <select id="locationCombo" name="location_id" class="long-text"><option value=""';
  if (requests[index][c.req.LOCATION_ID] === null)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(21, 'Ikke oppgitt');
  o[p++] = '</option>';
  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = locations[i][c.loc.ID];
    o[p++] = '"';
    if (locations[i][c.loc.ID] === requests[index][c.req.LOCATION_ID])
      o[p++] = ' selected="selected"';
    o[p++] = '>';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</option>';
  }
  // Desired category.
  o[p++] = '</select></div><div class="form-element"><label for="categoryCombo" class="wide-label">';
  o[p++] = getText(19, '&Oslash;nsket kategori:');
  o[p++] = '</label> <select id="categoryCombo" name="category_id" class="long-text"><option value=""';
  if (requests[index][c.req.CATEGORY_ID] === null)
    o[p++] = ' selected="selected"';
  o[p++] = '>';
  o[p++] = getText(21, 'Ikke oppgitt');
  o[p++] = '</option>';
  for (i = 0; i < categories.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = categories[i][c.cat.ID];
    o[p++] = '"';
    if (categories[i][c.cat.ID] === requests[index][c.req.CATEGORY_ID])
      o[p++] = ' selected="selected"'
    o[p++] = '>';
    o[p++] = categories[i][c.cat.NAME];
    o[p++] = '</option>';
  }
  // Desired start date.
  o[p++] = '</select></div><div class="form-element"><label for="startDateEdit" class="wide-label">';
  o[p++] = getText(20, '&Oslash;nsket innflyttingsdato:');
  o[p++] = '</label><input type="text" id="startDateEdit" name="start_date" readonly="readonly" class="long-text" value="';
  o[p++] = requests[index][c.req.START_DATE];
  o[p++] = '" /><button type="button" id="openCalendarButton" class="icon-button" onclick="openCalendar();"><i class="fa-solid fa-calendar-days"></i></button><button type="button" id="closeCalendarButton" class="icon-button" style="display: none;" onclick="closeCalendar();"><i class="fa-solid fa-xmark"></i></button><div id="calendarBox" class="calendar-box" style="display: none;">&nbsp;</div></div>';
  // Status combo.
  o[p++] = '<div class="form-element"><label for="statusCombo" class="wide-label">';
  o[p++] = getText(22, 'Status:');
  o[p++] = '</label><select id="statusCombo" name="status" class="long-text">';
  for (i = 0; i <= REQUEST_STATUS_TEXTS.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = String(i);
    o[p++] = '"';
    if (i === requests[index][c.req.STATUS])
      o[p++] = ' selected="selected"';
    o[p++] = '>';
    o[p++] = REQUEST_STATUS_TEXTS[i];
    o[p++] = '</option>';
  }
  o[p++] = '</select></div></form></div><div class="dialogue-footer"><button type="button" id="submitButton" onclick="Utility.displaySpinnerThenSubmit(editRequestForm);"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(23, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeRequestDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(24, 'Avbryt');
  o[p++] = '</button></div>';

  editRequestDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editRequestForm', 'startDateEdit', 'openCalendarButton',
    'closeCalendarButton', 'calendarBox', 'submitButton']);

  // Create calendar component.
  calendar = new Calendar(settings.selectableMonthCount);
  calendar.dayNames = DAY_NAMES;
  calendar.monthNames = MONTH_NAMES;
  calendar.monthNamesInSentence = MONTH_NAMES_IN_SENTENCE;
  calendar.selectedDate = requests[index][c.req.START_DATE];
  calendar.onSelectDate = selectDate;
  calendar.display();

  Utility.display(overlay);
  Utility.display(editRequestDialogue);
  enableSubmitButton();
}

// *************************************************************************************************

function openCalendar()
{
  Utility.hide(openCalendarButton);
  Utility.display(closeCalendarButton);
  Utility.display(calendarBox);
}

// *************************************************************************************************

function closeCalendar()
{
  Utility.hide(closeCalendarButton);
  Utility.display(openCalendarButton);
  Utility.hide(calendarBox);
}

// *************************************************************************************************
// Select the given date as the desired starting date of the user's subscription. selectedDate is a
// string with a date in ISO format - that is, "yyyy-mm-dd".
function selectDate(sender, selectedDate)
{
  startDateEdit.value = selectedDate;
  closeCalendar();
}

// *************************************************************************************************
// Enable or disable the submit button in the edit request dialogue. For a request, all the fields
// are optional, so the button is always enabled.
function enableSubmitButton()
{
  submitButton.disabled = false;
}

// *************************************************************************************************
// Close the edit request dialogue.
function closeRequestDialogue()
{
  Utility.hide(editRequestDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Delete the request with the given index in the requests table.
function deleteRequest(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, requests) &&
    confirm(getText(11, 'Er du sikker på at du vil slette denne forespørselen?')))
  {
    o = new Array(5);
    p = 0;

    o[p++] = '<form id="deleteRequestForm" action="/subscription/html/admin_requests.php" method="post">';
    o[p++] = getPageStateFormElements();
    o[p++] = '<input type="hidden" name="action" value="delete_request" />';
    o[p++] = Utility.getHidden('id', requests[index][c.req.ID]);
    o[p++] = '</form>';
    editRequestDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteRequestForm'));
  }
}

// *************************************************************************************************
// Open the booking page to book a subscription to satisfy the request with the given index, or at
// least to send the buyer an offer.
function bookSubscription(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, requests))
  {
    o = new Array(8);
    p = 0;

    o[p++] = '/subscription/html/admin_book_subscription.php?initial_user_id=';
    o[p++] = String(requests[index][c.req.USER_ID]);
    if (requests[index][c.req.LOCATION_ID] !== null)
    {
      o[p++] = '&initial_location_id=';
      o[p++] = String(requests[index][c.req.LOCATION_ID]);
    }
    if (requests[index][c.req.CATEGORY_ID] !== null)
    {
      o[p++] = '&initial_category_id=';
      o[p++] = String(requests[index][c.req.CATEGORY_ID]);
    }
    if (requests[index][c.req.START_DATE] !== '')
    {
      o[p++] = '&initial_date=';
      o[p++] = requests[index][c.req.START_DATE];
    }
    Utility.displaySpinnerThenGoTo(o.join(''));
  }
}

// *************************************************************************************************
// User notes functions.
// *************************************************************************************************

function loadUserNotes(userId)
{
  // If the user notes have already been loaded, display them.
  if (typeof userNotes[userId] !== 'undefined')
    displayUserNotes(userId);
  else
  {
    // Fetch the user notes from the server, then store and display them.
    userNotesDialogue.innerHTML = '<p>' +
      getText(34, 'Laster notater. Vennligst vent...') + '</p>';
    Utility.display(overlay);
    Utility.display(userNotesDialogue);
    errorDisplayed = false;
    fetch('/subscription/json/user_notes.php?user_id=' + String(userId))
      .then(Utility.extractJson)
      .then(storeUserNotes)
      .catch(logUserNotesError);
  }
}

// *************************************************************************************************
// Log an error that occurred while fetching user notes from the server.
function logUserNotesError(error)
{
  console.error('Error fetching or updating user notes: ' + error);
  closeUserNotesDialogue();
}

// *************************************************************************************************
// Store and display the user notes for the user being edited. These are the administrator's private
// notes concerning that user.
function storeUserNotes(data)
{
  // See if the request has already failed.
  if (errorDisplayed)
    return;

  if (data && data.resultCode)
  {
    if (Utility.isError(data.resultCode))
    {
      console.error('Error fetching or updating user notes: result code: ' +
        String(data.resultCode));
      errorDisplayed = true;
      alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(data.resultCode), Utility.getTimestamp()]));
      closeUserNotesDialogue();
    }
    else
    {
      if ((typeof data.userNotes !== 'undefined') && (typeof data.userId !== 'undefined') &&
        (data.userId >= 0))
      {
        userNotes[data.userId] = Utility.decodeLineBreaks(data.userNotes);
        // If the result was OK, that means the user notes were stored. Close the dialogue.
        // Otherwise, display it.
        if (data.resultCode === result.OK)
          closeUserNotesDialogue();
        else
          displayUserNotes(data.userId);
      }
      else
      {
        console.error('Error fetching or updating user notes: userNotes or userId field missing.');
        closeUserNotesDialogue();
      }
    }
  }
  else
  {
    console.error('Error fetching or updating user notes: data object or result code missing.');
    closeUserNotesDialogue();
  }
}

// *************************************************************************************************

function displayUserNotes(userId)
{
  var o, p, notes;

  notes = userNotes[userId];
  if ((typeof notes === 'undefined') || (notes === null))
    notes = '';
  o = new Array(15);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(35, 'Notater');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><div class="form-element help-text">';
  o[p++] = getText(36, 'Deres private notater om denne kunden. Kunden vil ikke f&aring tilgang til disse.');
  o[p++] = '</div><textarea id="userNotesTextArea">';
  o[p++] = notes;
  o[p++] = '</textarea></div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="saveUserNotes(';
  o[p++] = String(userId);
  o[p++] = ');"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(37, 'Lagre');
  o[p++] = '</button> <button type="button" onclick="closeUserNotesDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(24, 'Avbryt');
  o[p++] = '</button></div></form>';

  userNotesDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['userNotesTextArea']);

  Utility.display(overlay);
  Utility.display(userNotesDialogue);
}

// *************************************************************************************************

function saveUserNotes(userId)
{
  Utility.displaySpinnerThen(
      function ()
      {
        doSaveUserNotes(userId);
      }
    );
}

// *************************************************************************************************

function doSaveUserNotes(userId)
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('action', 'set_user_notes');
  requestData.append('user_id', String(userId));
  requestData.append('user_notes', Utility.encodeLineBreaks(userNotesTextArea.value));
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/user_notes.php', options)
    .then(Utility.extractJson)
    .then(storeUserNotes)
    .catch(logUserNotesError);
}

// *************************************************************************************************

function closeUserNotesDialogue()
{
  Utility.hide(userNotesDialogue);
  Utility.hide(overlay);
  Utility.hideSpinner();
}

// *************************************************************************************************
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  o = new Array(18);
  p = 0;

  // Clear all filters button.
  o[p++] = getText(25, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(26, 'Vis alle');
  o[p++] = '</button>';
  // Status filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (statusFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayStatusFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(9, 'Status');
  o[p++] = '</button>';
  // Clear status filter button.
  if (statusFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearStatusFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Freetext filter edit.
  o[p++] = '<input type="text" id="freetextEdit" placeholder="';
  o[p++] = getText(33, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Display counter box.
  o[p++] = '<span class="counter">';
  if (displayedCount === requests.length)
    o[p++] = getText(27, 'Viser $1 foresp&oslash;rsler', [String(requests.length)]);
  else
    o[p++] = getText(28, 'Viser $1 av $2 foresp&oslash;rsler',
      [String(displayedCount), String(requests.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['freetextEdit']);
}

// *************************************************************************************************
// Return true if the list of requests should not include the given request.
function shouldHide(request)
{
  return ((statusFilter !== null) && !statusFilter.includes(request[c.req.STATUS])) ||
    ((freetextFilter !== '') && !matchesFreetextFilter(request));
}

// *************************************************************************************************

function clearAllFilters()
{
  statusFilter = null;
  freetextFilter = '';
  freetextEdit.value = '';
  displayRequests();
}

// *************************************************************************************************
// Status filter functions.
// *************************************************************************************************
// Return true if the list of products is currently filtered on status, and the filter includes the
// given status. 
function inStatusFilter(status)
{
  return (statusFilter !== null) && statusFilter.includes(status);
}

// *************************************************************************************************

function displayStatusFilterDialogue()
{
  var o, p, i;
  
  o = new Array((REQUEST_STATUS_TEXTS.length * 10) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(29, 'Velg hvilke statusverdier som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < REQUEST_STATUS_TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inStatusFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> ';
    o[p++] = '<label for="status';
    o[p++] = String(i);
    o[p++] = 'Checkbox"> ';
    o[p++] = REQUEST_STATUS_TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllStatusesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(30, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllStatusesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(31, 'Ingen');
  o[p++] = '</button></div><button type="button" onclick="updateStatusFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(23, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeStatusFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(24, 'Avbryt');
  o[p++] = '</button></div>';

  editStatusFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editStatusFilterDialogue);
}

// *************************************************************************************************
// Check or uncheck all the status checkboxes in the status filter dialogue, depending on checked,
// which should be a boolean.
function setAllStatusesTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < REQUEST_STATUS_TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function clearStatusFilter()
{
  statusFilter = null;
  displayRequests();
}

// *************************************************************************************************

function updateStatusFilter()
{
  var i, checkbox;

  statusFilter = [];
  for (i = 0; i < REQUEST_STATUS_TEXTS.length; i++)
  {
    checkbox = document.getElementById('status' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      statusFilter.push(i);
  }
  // If the user unchecks all statuses, instead of displaying nothing, clear the filter. If the
  // user checks all statuses, also clear the filter.
  if ((statusFilter.length === 0) || (statusFilter.length === REQUEST_STATUS_TEXTS.length))
    statusFilter = null;
  closeStatusFilterDialogue();
  displayRequests();
}

// *************************************************************************************************

function closeStatusFilterDialogue()
{
  Utility.hide(editStatusFilterDialogue);
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
  displayRequests();
}

// *************************************************************************************************
// Return true if the given request matches the current freetext filter.
function matchesFreetextFilter(request)
{
  var filter, user;

  filter = freetextFilter.toLowerCase();
  user = getUser(request[c.req.USER_ID]);
  if (user === null)
      return false;
  // If there is no filter (or no request), everything matches. Otherwise, return a match if the
  // request's comment, user name, location name, category name or start date fields contain the
  // filter text.
  return (request === null) || (filter === '') ||
    (request[c.req.COMMENT].toLowerCase().indexOf(filter) >= 0) ||
    (user[c.rqu.NAME].toLowerCase().indexOf(filter) >= 0) ||
    (Utility.getLocationName(request[c.req.LOCATION_ID]).toLowerCase().indexOf(filter) >= 0) ||
    (Utility.getCategoryName(request[c.req.CATEGORY_ID]).toLowerCase().indexOf(filter) >= 0) ||
    (request[c.req.START_DATE].indexOf(filter) >= 0);
}

// *************************************************************************************************
