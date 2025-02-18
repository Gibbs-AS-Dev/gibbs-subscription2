// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************

// Pointers to user interface elements.
var newIndividualButton, newCompanyButton, individualDataBox, companyDataBox, userInfoBox,
  newUserNameEdit, newFirstNameEdit, newLastNameEdit, newCompanyNameEdit, newCompanyIdEdit,
  newPhoneEdit, newAddressEdit, newPostcodeEdit, newAreaEdit, /*newPasswordEdit,*/ needsBox,
  loginErrorBox, submitButton, overlay, loginDialogue, userNameEdit, passwordEdit, loginButton;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var submitRequestForm, startDateEdit, openCalendarButton, closeCalendarButton, calendarBox;

// The calendar component that allows the user to select the desired starting date for his
// subscription.
var calendar;

// The currently selected entity type. That is, whether the new customer is a company or a private
// individual.
var selectedEntityType = ENTITY_TYPE_INDIVIDUAL;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and displaying the first page of the progress tabset.
function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['newIndividualButton', 'newCompanyButton', 'individualDataBox',
    'companyDataBox', 'userInfoBox', 'newUserNameEdit', 'newFirstNameEdit', 'newLastNameEdit', 
    'newCompanyNameEdit', 'newCompanyIdEdit', 'newPhoneEdit', 'newAddressEdit', 'newPostcodeEdit',
    'newAreaEdit', /*'newPasswordEdit',*/ 'needsBox', 'loginErrorBox', 'submitButton', 'overlay',
    'loginDialogue', 'userNameEdit', 'passwordEdit', 'loginButton']);
  
  displayNeedsBox();
  enableSubmitButton();

  // Display the results of a previous operation, if required.
  if (resultCode === result.OK)
    window.location.href = '/subscription/html/request_submitted.php';
  if (Utility.isError(resultCode))
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Handle a change to the entity type, and update the user interface accordingly. Different fields
// are displayed, depending on whether the new user is a company or a private individual.
function selectEntityType()
{
  setEntityType(parseInt(Utility.getRadioButtonValue('entity_type', -1), 10));
}

// *************************************************************************************************
// Set a new value for the entity type, and update the user interface.
function setEntityType(newValue)
{
  // Validate new value. The existing value will not be updated unless it has changed.
  newValue = parseInt(newValue, 10);
  if (isFinite(newValue) && (newValue >= ENTITY_TYPE_INDIVIDUAL) &&
    (newValue <= ENTITY_TYPE_COMPANY) && (newValue !== selectedEntityType))
  {
    selectedEntityType = newValue;
    if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
      newIndividualButton.checked = true;
    else
      newCompanyButton.checked = true;
    Utility.setDisplayState(individualDataBox, selectedEntityType === ENTITY_TYPE_INDIVIDUAL);
    Utility.setDisplayState(companyDataBox, selectedEntityType === ENTITY_TYPE_COMPANY);
    enableSubmitButton();
  }
}

// *************************************************************************************************
// Enable or disable the submit request button, depending on whether the user has filled in all the
// required information, and checked the "I accept the terms and conditions" checkbox.
function enableSubmitButton()
{
  // If you are logged in, you can always proceed.
  if (isLoggedIn)
    submitButton.disabled = false;
  else
  {
    // The user is not logged in. He has to fill in all the mandatory user information fields. There
    // are different fields, depending on whether the new user is a company or an individual.
    if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
    {
      submitButton.disabled = (newFirstNameEdit.value === '') ||
        (newLastNameEdit.value === '') || !Utility.isValidEMail(newUserNameEdit.value) ||
        (newPhoneEdit.value === '') || (newAddressEdit.value === '') ||
        (newPostcodeEdit.value === '') || (newAreaEdit.value === ''); /*||
        (newPasswordEdit.value.length < PASSWORD_MIN_LENGTH);*/
    }
    else
    {
      submitButton.disabled = (newCompanyNameEdit.value === '') ||
        (newCompanyIdEdit.value === '') || !Utility.isValidEMail(newUserNameEdit.value) ||
        (newPhoneEdit.value === '') || (newAddressEdit.value === '') ||
        (newPostcodeEdit.value === '') || (newAreaEdit.value === ''); /*||
        (newPasswordEdit.value.length < PASSWORD_MIN_LENGTH);*/
    }
  }
}

// *************************************************************************************************

function createUserAndSubmitRequest()
{
  if (isLoggedIn)
    submitRequest();
  else
    createUser();
}

// *************************************************************************************************
// Display the spinner. Once visible, create a new user.
function createUser()
{
  Utility.displaySpinnerThen(doCreateUser);
}

// *************************************************************************************************
// Submit an asynchronous request to create a new user.
function doCreateUser()
{
  var options, requestData;

  requestData = new FormData();
  requestData.append('entity_type', selectedEntityType);
  if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
  {
    requestData.append('first_name', newFirstNameEdit.value);
    requestData.append('last_name', newLastNameEdit.value);
  }
  else
  {
    requestData.append('company_name', newCompanyNameEdit.value);
    requestData.append('company_id_number', newCompanyIdEdit.value);
  }
  requestData.append('user_name', newUserNameEdit.value);
  requestData.append('phone', newPhoneEdit.value);
  requestData.append('address', newAddressEdit.value);
  requestData.append('postcode', newPostcodeEdit.value);
  requestData.append('area', newAreaEdit.value);
  // requestData.append('password', newPasswordEdit.value);
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/create_user.php', options)
    .then(Utility.extractJson)
    .then(confirmUserCreated)
    .catch(logCreateUserError);
}

// *************************************************************************************************
// Receive the response to the request to create a new user. Display an error message, or - if the
// request succeeded - move on to create a subscription.
function confirmUserCreated(data)
{
  var resultCode;

  Utility.hideSpinner();

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Assume something failed.
  resultCode = result.REQUEST_FAILED;
  if (data)
  {
    // If a more specific error code was returned, display that instead.
    if (data.resultCode && Utility.isError(data.resultCode))
    {
      // The server returned an error. Display an error message, if one was available. In case of
      // errors from the server, the user can continue, correct the error, and try again.
      if (data.errorMessage)
      {
        loginErrorBox.innerHTML = data.errorMessage;
        Utility.display(loginErrorBox);
        return;
      }
      resultCode = data.resultCode;
    }
    else
    {
      // The user was successfully logged in. Move on to submit the booking request.
      submitRequest();
      return;
    }
  }
  errorDisplayed = true;
  alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), TIMESTAMP]));
  window.location.href = '/subscription/html/user_dashboard.php';
}

// *************************************************************************************************
// Log an error that occurred while creating a new user.
function logCreateUserError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while creating a user: ' + error);
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED)]));
    window.location.href = '/subscription/html/user_dashboard.php';
  }
}

// *************************************************************************************************

function submitRequest()
{
  Utility.displaySpinnerThenSubmit(submitRequestForm);
}

// *************************************************************************************************
// *** User info box functions.
// *************************************************************************************************
// Display the dialogue which allows existing users to log in.
function displayLoginDialogue()
{
  Utility.display(overlay);
  Utility.display(loginDialogue);
  enableLoginButton();
  userNameEdit.focus();
}

// *************************************************************************************************
// Close the login dialogue.
function closeLoginDialogue()
{
  Utility.hide(loginDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Handle a button press on any input element on the login dialogue. If the user pressed enter, try
// to click the login button.
function handleLoginDialogueKeyPress(event)
{
  if ((event.key === 'Enter') && !loginButton.disabled)
    logIn();
}

// *************************************************************************************************
// Enable or disable the login button, depending on whether the user has filled in both a user name
// and a password.
function enableLoginButton()
{
  loginButton.disabled = !Utility.isValidEMail(userNameEdit.value) || (passwordEdit.value === '');
}

// *************************************************************************************************
// Display the spinner. Once visible, log in.
function logIn()
{
  Utility.displaySpinnerThen(doLogIn);
}

// *************************************************************************************************
// Submit an asynchronous request to log in an existing user.
function doLogIn()
{
  var options, requestData;

  closeLoginDialogue();
  requestData = new FormData();
  requestData.append('user_name', String(userNameEdit.value));
  requestData.append('password', String(passwordEdit.value));
  options =
    {
      method: 'POST',
      body: requestData
    };
  errorDisplayed = false;
  fetch('/subscription/json/log_in.php', options)
    .then(Utility.extractJson)
    .then(confirmUserLoggedIn)
    .catch(logLoginError);
}

// *************************************************************************************************
// Receive the response to the request to log in an existing user. Display an error message, or - if
// the request succeeded - display a success message and remove the user information box.
function confirmUserLoggedIn(data)
{
  var resultCode;

  Utility.hideSpinner();

  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Assume something failed.
  resultCode = result.REQUEST_FAILED;
  if (data)
  {
    // If a more specific error code was returned, display that instead.
    if (data.resultCode && Utility.isError(data.resultCode))
    {
      // The server returned an error. Display an error message, if one was available. In case of
      // errors from the server, the user can continue, correct the error, and try again.
      if (data.errorMessage)
      {
        loginErrorBox.innerHTML = data.errorMessage;
        Utility.display(loginErrorBox);
        return;
      }
      resultCode = data.resultCode;
    }
    else
    {
      // The user was successfully logged in. Replace the user information fields with a success
      // message.
      userInfoBox.innerHTML = '<div class="form-element"><p>' +
        getText(2, 'Innlogging vellykket!') + '</p></div>';
      Utility.hide(loginErrorBox);
      isLoggedIn = true;
      enableSubmitButton();
      return;
    }
  }
  errorDisplayed = true;
  alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
    [String(resultCode), TIMESTAMP]));
  window.location.href = '/subscription/html/user_dashboard.php';
}

// *************************************************************************************************
// Log an error that occurred while logging in an existing user.
function logLoginError(error)
{
  Utility.hideSpinner();
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    console.error('Error while logging in: ' + error);
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(result.REQUEST_FAILED)]));
  }
}

// *************************************************************************************************
// *** Needs box functions.
// *************************************************************************************************

function displayNeedsBox()
{
  var o, p, i;
  
  if (initialStartDate === '')
    initialStartDate = Utility.getCurrentIsoDate();
  o = new Array((locations.length * 7) + (categories.length * 7) + 23);
  p = 0;

  // Headline.
  o[p++] = '<div class="separator-box"><h2>';
  o[p++] = getText(3, 'Ditt behov');
  o[p++] = '</h2></div><form id="submitRequestForm" action="/subscription/html/submit_request.php" method="post"><input type="hidden" name="action" value="create_request">';

  // Location combo.
  o[p++] = '<label for="locationCombo">';
  o[p++] = getText(4, 'Ønsket lager:');
  o[p++] = '</label><select id="locationCombo" name="location_id" class="long-text" onchange="enableSubmitButton();"><option value="" selected>';
  o[p++] = getText(5, 'Ikke oppgitt');
  o[p++] = '</option>';
  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = locations[i][c.loc.ID];
    o[p++] = '"';
    if (initialLocationId === locations[i][c.loc.ID])
      o[p++] = ' selected';
    o[p++] = '>';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</option>';
  }
  o[p++] = '</select>';

  // Category combo.
  o[p++] = '<label for="categoryCombo">';
  o[p++] = getText(6, 'Ønsket kategori:');
  o[p++] = '</label><select id="categoryCombo" name="category_id" class="long-text" onchange="enableSubmitButton();"><option value="" selected>';
  o[p++] = getText(5, 'Ikke oppgitt');
  o[p++] = '</option>';
  for (i = 0; i < categories.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = categories[i][c.cat.ID];
    o[p++] = '"';
    if (initialCategoryId === categories[i][c.cat.ID])
      o[p++] = ' selected';
    o[p++] = '>';
    o[p++] = categories[i][c.cat.NAME];
    o[p++] = '</option>';
  }
  o[p++] = '</select>';

  // Start date edit and calendar.
  o[p++] = '<label for="startDateEdit">';
  o[p++] = getText(7, 'Ønsket innflyttingsdato:');
  o[p++] = '</label><input type="text" id="startDateEdit" name="start_date" readonly value="';
  o[p++] = initialStartDate;
  o[p++] = '" class="selected-date-edit" onfocus="this.blur();" onclick="toggleCalendar();" /><button type="button" id="openCalendarButton" class="date-editor-button" onclick="openCalendar();"><i class="fa-solid fa-calendar-days"></i></button><button type="button" id="closeCalendarButton" class="date-editor-button" style="display: none;" onclick="closeCalendar();"><i class="fa-solid fa-xmark"></i></button><div id="calendarBox" class="calendar-box" style="display: none;"></div>';

  // Comment edit.
  o[p++] = '<label for="commentEdit">';
  o[p++] = getText(8, 'Beskrivelse:');
  o[p++] = '</label><textarea id="commentEdit" name="comment" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"></textarea><br></form>';

  needsBox.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['submitRequestForm', 'startDateEdit', 'openCalendarButton', 'closeCalendarButton',
    'calendarBox']);

  // Create calendar component.
  calendar = new Calendar(settings.selectableMonthCount);
  calendar.dayNames = DAY_NAMES;
  calendar.monthNames = MONTH_NAMES;
  calendar.monthNamesInSentence = MONTH_NAMES_IN_SENTENCE;
  calendar.selectedDate = initialStartDate;
  calendar.onSelectDate = selectDate;
  calendar.display();
}

// *************************************************************************************************

function toggleCalendar()
{
  if (Utility.displayed(calendarBox))
    closeCalendar();
  else
    openCalendar();
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
// Select the given date as the starting date of the user's subscription. dateString is a string
// with a date in ISO format - that is, "yyyy-mm-dd". Update the calendar to display the date as
// selected. We know the date is visible, as you can only select dates within the currently
// displayed month.
function selectDate(sender, selectedDate)
{
  startDateEdit.value = selectedDate;
  closeCalendar();
}

// *************************************************************************************************
