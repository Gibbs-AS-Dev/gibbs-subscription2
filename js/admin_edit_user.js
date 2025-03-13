// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var userInfoBox, subscriptionsFrame, subscriptionsBox, expiredSubscriptionsCheckbox, overlay,
  userNotesDialogue, pricePlanDialogue, paymentHistoryDialogue, cancelSubscriptionDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var individualDataBox, companyDataBox, firstNameEdit, lastNameEdit, companyNameEdit, companyIdEdit,
  userNameEdit, phoneEdit, passwordEdit, submitButton, cancelSubscriptionForm, standardCancelBox,
  immediateCancelBox, customCancelBox, customCancelResultBox, endDateEdit, openCalendarButton,
  closeCalendarButton, calendarBox, userNotesTextArea, inlineUserNotesTextArea;

// The sorting object that controls the sorting of the subscriptions table. Only present if editing
// an existing user.
var sorting = null;

// The popup menu for the subscriptions table.
var menu;

// The currently selected entity type. That is, whether the new customer is a company or a private
// individual.
var selectedEntityType = ENTITY_TYPE_INDIVIDUAL;

// The user notes for the current user, or null if they have not been loaded yet.
var userNotes = null;

// The calendar component that allows the user to select the end date when cancelling a
// subscription.
var calendar;

// Flag that says whether an alert error message has already been displayed. If so, we should not
// display another.
var errorDisplayed = false;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['userInfoBox', 'subscriptionsFrame', 'subscriptionsBox',
   'expiredSubscriptionsCheckbox', 'overlay', 'userNotesDialogue', 'pricePlanDialogue',
   'paymentHistoryDialogue', 'cancelSubscriptionDialogue', 'inlineUserNotesTextArea', 'notesForm', 'notesTarget']);

  // Add logging for debugging company loading issue
  if (user && user.entityType === ENTITY_TYPE_COMPANY) {
    console.log("Loading company user:", user);
    console.log("Company ID number:", user.companyIdNumber);
  }
  
  // Failsafe to hide spinner after 10 seconds in case of loading issues
  setTimeout(function() {
    console.log("Applying failsafe to hide spinner");
    Utility.hideSpinner();
  }, 10000);

  // Set up form handlers
  if (notesForm) {
    console.log("Setting up form submission handler");
    notesForm.onsubmit = function() {
      // Show loading spinner instead of alert
      var saveButton = document.getElementById('saveNotesButton');
      var spinner = document.getElementById('saveNotesSpinner');
      
      if (saveButton && spinner) {
        saveButton.disabled = true;
        spinner.style.display = 'inline-block';
      }
      
      // Encode line breaks before submission
      return encodeNotesBeforeSubmit();
    };
  } else {
    console.log("Notes form not found");
  }

  // Set up iframe load handler
  if (notesTarget) {
    console.log("Setting up iframe load handler");
    notesTarget.onload = function() {
      // Hide spinner and re-enable button when form is submitted
      var saveButton = document.getElementById('saveNotesButton');
      var spinner = document.getElementById('saveNotesSpinner');
      
      if (saveButton && spinner) {
        saveButton.disabled = false;
        spinner.style.display = 'none';
      }
      
      try {
        var iframeContent = notesTarget.contentDocument || notesTarget.contentWindow.document;
        var responseText = iframeContent.body.innerText;
        if (responseText) {
          console.log("Notes saved successfully! Response: " + responseText);
          
          // Try to parse the JSON response
          try {
            var response = JSON.parse(responseText);
            if (response && response.userNotes) {
              // Decode line breaks and update the textarea
              userNotes = Utility.decodeLineBreaks(response.userNotes);
              inlineUserNotesTextArea.value = userNotes;
              console.log("Notes updated with line breaks preserved");
            }
          } catch (jsonError) {
            console.error("Error parsing JSON response: " + jsonError.message);
          }
        }
      } catch (e) {
        console.error("Error processing iframe response: ", e);
      }
    };
  } else {
    console.log("Notes target iframe not found");
  }

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  displayUserInfo();
  if (isNewUser)
    Utility.hide(subscriptionsFrame);
  else
  {
    Utility.display(subscriptionsFrame);
    selectedEntityType = user.entityType;

    // Initialise sorting.
    sorting = new Sorting(subscriptions,
        [
          Sorting.createUiColumn(c.sub.LOCATION_ID, Sorting.SORT_AS_STRING,
            function (subscription)
            {
              return Utility.getLocationName(subscription[c.sub.LOCATION_ID]) + ' ' +
                subscription[c.sub.PRODUCT_NAME];
            }),
          Sorting.createUiColumn(c.sub.PRODUCT_NAME, Sorting.SORT_AS_STRING),
          Sorting.createUiColumn(c.sub.PRODUCT_TYPE_ID, Sorting.SORT_AS_STRING,
            function (subscription)
            {
              return Utility.getProductTypeName(subscription[c.sub.PRODUCT_TYPE_ID]);
            }),
          Sorting.createUiColumn(c.sub.STATUS, Sorting.SORT_AS_STRING,
            function (subscription)
            {
              return st.sub.TEXTS[subscription[c.sub.STATUS]];
            }),
          Sorting.createUiColumn(c.sub.START_DATE, Sorting.SORT_AS_STRING),
          Sorting.createUiColumn(c.sub.END_DATE, Sorting.SORT_AS_STRING),
          Sorting.createUiColumn(c.sub.INSURANCE_NAME, Sorting.SORT_AS_STRING),
          Sorting.createUiColumn(Sorting.DO_NOT)
        ],
        doDisplaySubscriptions
      );
    // Set the initial sorting. If that didn't cause subscriptions to be displayed, do so now.
    if (!sorting.sortOn(initialUiColumn, initialDirection))
      doDisplaySubscriptions();
  }
  Utility.setDisplayState(individualDataBox, selectedEntityType === ENTITY_TYPE_INDIVIDUAL);
  Utility.setDisplayState(companyDataBox, selectedEntityType === ENTITY_TYPE_COMPANY);
  Utility.hideSpinner();

  // Display the results of a previous operation, if required.
  if (resultCode === result.PASSWORD_CHANGED)
    alert(getText(1, 'Passordet ble endret. Husk å informere kunden.'));
  else
    if (Utility.isError(resultCode))
    {
      alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(resultCode), TIMESTAMP]));
    }

  // Show user notes directly when page loads
  if (!isNewUser) {
    // Make a direct request to get the notes
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/subscription/json/user_notes.php?user_id=' + user.id, true);
    xhr.onload = function() {
      console.log("XHR Status: " + xhr.status);
      console.log("XHR Response: " + xhr.responseText);
      if (xhr.status === 200) {
        try {
          var response = JSON.parse(xhr.responseText);
          if (response && response.userNotes) {
            userNotes = response.userNotes;
            console.log("Loaded user notes: " + userNotes);
            if (inlineUserNotesTextArea) {
              inlineUserNotesTextArea.value = userNotes;
            }
          }
        } catch (e) {
          console.error("Error parsing response: " + e.message);
        }
      }
    };
    xhr.send();
  }
}

// *************************************************************************************************
// Return hidden form elements that specify the current state of the page, including sorting, search
// and filter settings. These should be included whenever a request is submitted to the current
// page, so that the state is maintained when the page is reloaded.
function getPageStateFormElements()
{
  var o, p;

  o = new Array(2);
  p = 0;

  o[p++] = Utility.getHidden('user_id', user.id);
  if (sorting)
    o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// User info functions.
// *************************************************************************************************

function displayUserInfo()
{
  var o, p;

  o = new Array(70); // Increased array size for more HTML
  p = 0;

  // Create a flex container for the two columns
  o[p++] = '<div style="display: flex; gap: 20px; width: 100%;">';
  
  // ********** LEFT SIDE - User Information (Read-Only) **********
  o[p++] = '<div style="flex: 1; border: 1px solid #ddd; border-radius: 5px; padding: 15px; background-color: #f9f9f9;">';
  o[p++] = '<div class="toolbar"><h3>' + getText(200, 'Bruker opplysninger') + '</h3></div>';
  
  // Entity type display
  o[p++] = '<div class="form-element">';
  if (user.entityType === ENTITY_TYPE_COMPANY)
    o[p++] = getText(47, 'Bedrift');
  else
    o[p++] = getText(46, 'Privatperson');
  o[p++] = '</div>';

  // Display first and last name for individuals
  if (user.entityType === ENTITY_TYPE_INDIVIDUAL)
  {
    o[p++] = '<div class="form-element">';
    o[p++] = '<label class="standard-label">' + getText(4, 'Fornavn:') + '</label>';
    o[p++] = '<div class="read-only-field">' + (user.orig_first_name || user.firstName || '') + '</div>';
    o[p++] = '</div>';
    
    o[p++] = '<div class="form-element">';
    o[p++] = '<label class="standard-label">' + getText(5, 'Etternavn:') + '</label>';
    o[p++] = '<div class="read-only-field">' + (user.orig_last_name || user.lastName || '') + '</div>';
    o[p++] = '</div>';
  }

  // Display company name and ID for companies
  if (user.entityType === ENTITY_TYPE_COMPANY)
  {
    o[p++] = '<div class="form-element">';
    o[p++] = '<label class="standard-label">' + getText(48, 'Navn:') + '</label>';
    o[p++] = '<div class="read-only-field">' + (user.name || '') + '</div>';
    o[p++] = '</div>';
    
    o[p++] = '<div class="form-element">';
    o[p++] = '<label class="standard-label">' + getText(49, 'Org. nr:') + '</label>';
    o[p++] = '<div class="read-only-field">' + (user.companyIdNumber || '') + '</div>';
    o[p++] = '</div>';
    
    // Log company data for debugging
    console.log("Setting up company edit fields:");
    console.log("Company name:", user.name);
    console.log("Company ID:", user.companyIdNumber);
  }

  // Email (original)
  o[p++] = '<div class="form-element">';
  o[p++] = '<label class="standard-label">' + getText(6, 'E-post:') + '</label>';
  o[p++] = '<div class="read-only-field">' + (user.eMail || '') + '</div>';
  o[p++] = '</div>';

  // Phone (original)
  o[p++] = '<div class="form-element">';
  o[p++] = '<label class="standard-label">' + getText(7, 'Telefonnr:') + '</label>';
  o[p++] = '<div class="read-only-field">' + (user.orig_phone || user.phone || '') + '</div>';
  o[p++] = '</div>';
  
  // End left side column
  o[p++] = '</div>';

  // ********** RIGHT SIDE - Billing Information (Editable) **********
  o[p++] = '<div style="flex: 1; border: 1px solid #ddd; border-radius: 5px; padding: 15px; background-color: #fff;">';
  o[p++] = '<div class="toolbar"><h3>' + getText(201, 'Faktura opplysninger') + '</h3></div>';
  
  // Form starts here
  o[p++] = '<form action="/subscription/html/admin_edit_user.php" method="post">';
  if (!isNewUser)
    o[p++] = getPageStateFormElements();

  // Add action parameter for existing users
  if (!isNewUser)
    o[p++] = '<input type="hidden" name="action" value="update_user" />';

  // First and last name for individuals (billing)
  if (user.entityType === ENTITY_TYPE_INDIVIDUAL)
  {
    o[p++] = '<div id="individualDataBox">';
    o[p++] = Utility.getEditBox('firstNameEdit', 'billing_first_name', getText(4, 'Fornavn:'),
      user.billing_first_name || user.firstName || '');
    o[p++] = Utility.getEditBox('lastNameEdit', 'billing_last_name', getText(5, 'Etternavn:'),
      user.billing_last_name || user.lastName || '');
    o[p++] = '</div>';
  }

  // Name and ID number for companies (billing)
  if (user.entityType === ENTITY_TYPE_COMPANY)
  {
    o[p++] = '<div id="companyDataBox">';
    // Make sure to stringify these values and handle undefined/null cases
    var companyName = user.name || '';
    var companyIdNumber = user.companyIdNumber || '';
    
    o[p++] = Utility.getEditBox('companyNameEdit', 'company_name', getText(48, 'Navn:'), companyName);
    o[p++] = Utility.getEditBox('companyIdEdit', 'company_id_number', getText(49, 'Org. nr:'), companyIdNumber);
    
    // Log company data for debugging
    console.log("Setting up company edit fields:");
    console.log("Company name:", companyName);
    console.log("Company ID:", companyIdNumber);
    
    o[p++] = '</div>';
  }

  // Email (billing)
  o[p++] = Utility.getEditBox('billingEmailEdit', 'billing_email', getText(6, 'E-post:'), 
    user.billingEmail || user.eMail || '');
  
  // Country code
  o[p++] = Utility.getEditBox('countryCodeEdit', 'country_code', 'Landkode:', user.countryCode || '+47');
  
  // Phone (billing)
  o[p++] = Utility.getEditBox('phoneEdit', 'billing_phone', getText(7, 'Telefonnr:'), 
    user.billing_phone || user.phone || '');
  
  // Address (billing)
  o[p++] = Utility.getEditBox('addressEdit', 'billing_address_1', getText(42, 'Adresse:'), 
    user.billing_address || user.address || '');
  
  // Postcode (billing)
  o[p++] = Utility.getEditBox('postcodeEdit', 'billing_postcode', getText(44, 'Postnr:'),
    user.billing_postcode || user.postcode || '');
  
  // Area (billing)
  o[p++] = Utility.getEditBox('areaEdit', 'billing_city', getText(45, 'Poststed:'), 
    user.billing_city || user.area || '');
  
  // Submit button
  o[p++] = '<div class="button-container fixed-width-container"><button type="submit" id="submitButton" class="wide-button"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(12, 'Lagre endringer');
  o[p++] = '</button></div>';
  
  o[p++] = '</form>';
  
  // End right side column
  o[p++] = '</div>';
  
  // End flex container
  o[p++] = '</div>';

  // Add CSS for read-only fields
  o[p++] = '<style>.read-only-field { padding: 6px 0; font-weight: normal; }</style>';

  userInfoBox.innerHTML = o.join('');

  // Obtain pointers to user interface elements
  Utility.readPointers(['individualDataBox', 'companyDataBox', 'firstNameEdit', 'lastNameEdit',
    'companyNameEdit', 'companyIdEdit', 'billingEmailEdit', 'countryCodeEdit', 'phoneEdit',
    'submitButton']);

  enableSubmitButton();
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

function enableSubmitButton()
{
  var invalid;
  
  // The form cannot be submitted if the main edit boxes are empty.
  if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL) {
    invalid = ((firstNameEdit.value === '') && (lastNameEdit.value === '')) ||
      (billingEmailEdit.value === '') || (phoneEdit.value === '');
  } else {
    invalid = (companyNameEdit.value === '') ||
      (billingEmailEdit.value === '') || (phoneEdit.value === '');
  }
  
  if (isNewUser)
  {
    // For a new customer, the user also has to fill in the password, and it has to contain at least
    // the minimum number of characters.
    invalid = invalid || (passwordEdit.value === '') ||
      (passwordEdit.value.length < PASSWORD_MIN_LENGTH);
  }

  submitButton.disabled = invalid;
}

// *************************************************************************************************

function changePassword()
{
  var newPassword, o, p;
  
  newPassword = prompt(getText(13, 'Nytt passord:'));
  if (newPassword === null)
    return;
  if (newPassword.length < PASSWORD_MIN_LENGTH)
  {
    alert(getText(14, 'Passordet er for kort. Bruk et annet passord.'));
    return;
  }

  o = new Array(4);
  p = 0;

  o[p++] = '<form id="changePasswordForm" action="/subscription/html/admin_edit_user.php" method="post"><input type="hidden" name="action" value="change_password" />';
  o[p++] = getPageStateFormElements();
  o[p++] = Utility.getHidden('new_password', newPassword);
  o[p++] = '</form>';
  paymentHistoryDialogue.innerHTML = o.join('');
  Utility.displaySpinnerThenSubmit(document.getElementById('changePasswordForm'));
}

// *************************************************************************************************
// User notes functions.
// *************************************************************************************************

function loadUserNotes()
{
  // If the user notes have already been loaded, populate the field
  if (userNotes !== null) {
    console.log("Using cached user notes");
    inlineUserNotesTextArea.value = userNotes;
  } else {
    // Fetch the user notes from the server, then store and display them
    console.log("Fetching user notes for user ID: " + user.id);
    Utility.displaySpinner();
    errorDisplayed = false;
    fetch('/subscription/json/user_notes.php?user_id=' + String(user.id))
      .then(function(response) {
        console.log("Notes fetch response status: " + response.status);
        // Check if response is OK
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.status);
        }
        // Use Utility.extractJson if available, otherwise use response.json()
        if (typeof Utility !== 'undefined' && typeof Utility.extractJson === 'function') {
          return Utility.extractJson(response);
        } else {
          return response.json();
        }
      })
      .then(storeUserNotesInline)
      .catch(logUserNotesError);
  }
}

// *************************************************************************************************
// Log an error that occurred while fetching user notes from the server.
function logUserNotesError(error)
{
  console.error('Error fetching user notes: ', error);
  alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice.'));
  Utility.hideSpinner();
}

// *************************************************************************************************
// Store and display the user notes in the inline text area 
function storeUserNotesInline(data)
{
  console.log("Notes fetch response data:", data);
  
  // See if the request has already failed.
  if (errorDisplayed)
    return;

  if (data && data.resultCode !== undefined)
  {
    if (Utility.isError(data.resultCode))
    {
      console.error('Error fetching user notes: result code: ' +
        String(data.resultCode));
      errorDisplayed = true;
      alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        [String(data.resultCode), Utility.getTimestamp()]));
      Utility.hideSpinner();
    }
    else
    {
      if (typeof data.userNotes !== 'undefined')
      {
        console.log("Successfully fetched user notes");
        userNotes = Utility.decodeLineBreaks(data.userNotes);
        inlineUserNotesTextArea.value = userNotes;
        Utility.hideSpinner();
      }
      else
      {
        console.error('Error fetching user notes: userNotes field missing from response.');
        Utility.hideSpinner();
      }
    }
  }
  else
  {
    console.error('Error fetching user notes: data object or resultCode missing.');
    Utility.hideSpinner();
  }
}

// *************************************************************************************************

function displayUserNotes()
{
  var o, p;

  o = new Array(13);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(67, 'Notater');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><div class="form-element help-text">';
  o[p++] = getText(68, 'Deres private notater om denne kunden. Kunden vil ikke f&aring tilgang til disse.');
  o[p++] = '</div><textarea id="userNotesTextArea">';
  o[p++] = userNotes;
  o[p++] = '</textarea></div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="Utility.displaySpinnerThen(saveUserNotes);"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(23, 'Lagre');
  o[p++] = '</button> <button type="button" onclick="closeUserNotesDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(62, 'Avbryt');
  o[p++] = '</button></div></form>';

  userNotesDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['userNotesTextArea']);

  Utility.display(overlay);
  Utility.display(userNotesDialogue);
}

// *************************************************************************************************

function saveUserNotes()
{
  var options, requestData;

  // Show spinner while saving
  Utility.displaySpinner();
  
  // Prepare the form data
  requestData = new FormData();
  requestData.append('action', 'set_user_notes');
  requestData.append('user_id', String(user.id));
  requestData.append('user_notes', Utility.encodeLineBreaks(userNotesTextArea.value));
  
  options = {
    method: 'POST',
    body: requestData
  };
  
  errorDisplayed = false;
  
  // Send the request
  fetch('/subscription/json/user_notes.php', options)
    .then(function(response) {
      console.log("Response status: " + response.status);
      // Check if response is OK
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.status);
      }
      // Use Utility.extractJson if available, otherwise use response.json()
      if (typeof Utility !== 'undefined' && typeof Utility.extractJson === 'function') {
        return Utility.extractJson(response);
      } else {
        return response.json();
      }
    })
    .then(function(data) {
      console.log("Response data:", data);
      
      if (data && data.resultCode !== undefined) {
        if (Utility.isError(data.resultCode)) {
          // Handle error
          console.error('Error saving user notes: result code: ' + String(data.resultCode));
          errorDisplayed = true;
          alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
            [String(data.resultCode), Utility.getTimestamp()]));
        } else {
          // Success
          if (typeof data.userNotes !== 'undefined') {
            userNotes = Utility.decodeLineBreaks(data.userNotes);
            userNotesTextArea.value = userNotes;
            // Don't show alert, just close the dialog
            closeUserNotesDialogue();
          } else {
            console.error('Response missing userNotes field:', data);
            alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice.'));
          }
        }
      } else {
        // Handle missing data
        console.error('Invalid response data:', data);
        alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
          ['?', Utility.getTimestamp()]));
      }
      Utility.hideSpinner();
    })
    .catch(function(error) {
      // Handle network or other errors
      console.error('Error saving user notes: ' + error);
      alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
        ['?', Utility.getTimestamp()]));
      Utility.hideSpinner();
    });
}

// *************************************************************************************************

function closeUserNotesDialogue()
{
  Utility.hide(userNotesDialogue);
  Utility.hide(overlay);
  Utility.hideSpinner();
}

// *************************************************************************************************
// Subscription functions.
// *************************************************************************************************
// Display the spinner. Once visible, display subscriptions.
function displaySubscriptions()
{
  Utility.displaySpinnerThen(doDisplaySubscriptions);
}

// *************************************************************************************************
// Display the list of subscriptions.
function doDisplaySubscriptions()
{
  var o, p, i;
  
  o = new Array((subscriptions.length * 19) + 9);
  p = 0;

  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(15, 'Lager'));
  o[p++] = sorting.getTableHeader(1, getText(16, 'Lagerbod'));
  o[p++] = sorting.getTableHeader(2, getText(17, 'Bodtype'));
  o[p++] = sorting.getTableHeader(3, getText(18, 'Status'));
  o[p++] = sorting.getTableHeader(4, getText(19, 'Fra dato'));
  o[p++] = sorting.getTableHeader(5, getText(20, 'Til dato'));
  o[p++] = sorting.getTableHeader(6, getText(21, 'Forsikring'));
  o[p++] = sorting.getTableHeader(7, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < subscriptions.length; i++)
  {
    if (!displayExpiredSubscriptions && (subscriptions[i][c.sub.STATUS] === st.sub.EXPIRED))
      continue;

    // Location name.
    o[p++] = '<tr><td>';
    o[p++] = Utility.getLocationName(subscriptions[i][c.sub.LOCATION_ID]);
    // Product name and price.
    o[p++] = '</td><td>';
    o[p++] = subscriptions[i][c.sub.PRODUCT_NAME];
    o[p++] = getPriceButton(i, PricePlan.getProductPricePlan(subscriptions, i));
    // Product type name.
    o[p++] = '</td><td>';
    o[p++] = Utility.getProductTypeName(subscriptions[i][c.sub.PRODUCT_TYPE_ID]);
    // Status.
    o[p++] = '</td><td>';
    o[p++] = Utility.getStatusLabel(st.sub.TEXTS, st.sub.COLOURS, subscriptions[i][c.sub.STATUS]);
    // Start date.
    o[p++] = '</td><td>';
    o[p++] = subscriptions[i][c.sub.START_DATE];
    // End date.
    o[p++] = '</td><td>';
    if (subscriptions[i][c.sub.END_DATE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = subscriptions[i][c.sub.END_DATE];
    // Insurance name and price.
    o[p++] = '</td><td>';
    if (subscriptions[i][c.sub.INSURANCE_NAME] === '')
      o[p++] = '&nbsp;';
    else
    {
      o[p++] = subscriptions[i][c.sub.INSURANCE_NAME];
      o[p++] = getPriceButton(i, PricePlan.getInsurancePricePlan(subscriptions, i));
    }
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';
  
  subscriptionsBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return '';
  o = new Array(2);
  p = 0;

  // Payment history button.
  o[p++] = sender.getMenuItem(getText(22, 'Vis ordrehistorikk'), 'fa-file-invoice-dollar', true,
    'loadPaymentHistory(' + String(index) + ');');
  // Cancel subscription button. Disabled if the subscription is not ongoing.
  o[p++] = sender.getMenuItem(getText(54, 'Si opp abonnement'), 'fa-hand-wave',
    subscriptions[index][c.sub.STATUS] === st.sub.ONGOING,
    'displayCancelSubscriptionDialogue(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************

function toggleExpiredSubscriptions()
{
  displayExpiredSubscriptions = expiredSubscriptionsCheckbox.checked;
  displaySubscriptions();
}

// *************************************************************************************************
// *** Cancel subscription functions.
// *************************************************************************************************
// Display the dialogue box to cancel the subscription with the given index in the subscriptions
// table.
function displayCancelSubscriptionDialogue(index)
{
  var o, p, today;

  today = Utility.getCurrentIsoDate();
  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return;

  o = new Array(41);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(54, 'Si opp abonnement');
  o[p++] = '</h1></div>';
  // Content.
  o[p++] = '<div class="dialogue-content"><form id="cancelSubscriptionForm" action="/subscription/html/admin_edit_user.php" method="post">';
  o[p++] = getPageStateFormElements();
  o[p++] = '<input type="hidden" name="action" value="cancel_subscription" />';
  o[p++] = Utility.getHidden('id', String(subscriptions[index][c.sub.ID]));
  // Confirmation caption.
  o[p++] = '<div class="form-element"><p>';
  o[p++] = getText(50, 'Si opp $1 p&aring; vegne av kunden?', [subscriptions[index][c.sub.PRODUCT_NAME]]);
  o[p++] = '</p></div>';
  // Standard cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="normalCancelButton" name="cancel_type" value="0" checked="checked" onChange="switchCancelType();" /> <label for="normalCancelButton">';
  o[p++] = getText(55, 'Vanlig oppsigelse');
  o[p++] = '</label></div>';
  // Standard cancellation message.
  o[p++] = '<div id="standardCancelBox" class="radio-indent-box"><span class="help-text">';
  if (Utility.canCancelThisMonth())
    o[p++] = getText(24, 'Kunden beholder lagerboden til og med siste dag i innev&aelig;rende m&aring;ned.');
  else
    o[p++] = getText(25, 'Kunden trekkes for neste m&aring;ned, og beholder lagerboden til og med siste dag neste m&aring;ned.');
  o[p++] = '</span></div>';
  // Immediate cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="immediateCancelButton" name="cancel_type" value="1" onChange="switchCancelType();" /> <label for="immediateCancelButton">';
  o[p++] = getText(56, 'Si opp umiddelbart');
  o[p++] = '</label></div>';
  // Immediate cancellation message.
  o[p++] = '<div id="immediateCancelBox" class="radio-indent-box" style="display: none;"><div class="custom-cancel-result-box">';
  o[p++] = getImmediateCancelResultText(Utility.getDayBefore(today));
  o[p++] = '</div></div>';
  // Custom cancellation radio button.
  o[p++] = '<div class="form-element"><input type="radio" id="customCancelButton" name="cancel_type" value="2" onChange="switchCancelType();" /> <label for="customCancelButton">';
  o[p++] = getText(57, 'Velg sluttdato');
  o[p++] = '</label></div>';
  // Custom cancel box.
  o[p++] = '<div id="customCancelBox" class="radio-indent-box" style="display: none;">';
  // End date.
  o[p++] = '<div><label for="endDateEdit" class="standard-label">';
  o[p++] = getText(58, 'Siste dag:');
  o[p++] = '</label><input type="text" id="endDateEdit" name="end_date" readonly="readonly" value="';
  o[p++] = today;
  o[p++] = '" /><button type="button" id="openCalendarButton" class="icon-button" onclick="openCalendar();"><i class="fa-solid fa-calendar-days"></i></button><button type="button" id="closeCalendarButton" class="icon-button" style="display: none;" onclick="closeCalendar();"><i class="fa-solid fa-xmark"></i></button><div id="calendarBox" class="calendar-box" style="display: none;">&nbsp;</div></div>';
  // Result caption.
  o[p++] = '<div id="customCancelResultBox" class="custom-cancel-result-box">';
  o[p++] = getCustomCancelResultText(today);
  o[p++] = '</div>';
  // End of custom cancel box.
  o[p++] = '</div>';
  // End of content.
  o[p++] = '</form></div>';
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" onclick="Utility.displaySpinnerThenSubmit(cancelSubscriptionForm);"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(59, 'Si opp');
  o[p++] = '</button> <button type="button" onclick="closeCancelSubscriptionDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(62, 'Avbryt');
  o[p++] = '</button></div>';

  cancelSubscriptionDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['cancelSubscriptionForm', 'standardCancelBox', 'immediateCancelBox',
    'customCancelBox', 'customCancelResultBox', 'endDateEdit', 'openCalendarButton',
    'closeCalendarButton', 'calendarBox']);

  // Create calendar component.
  calendar = new Calendar(24);
  calendar.dayNames = DAY_NAMES;
  calendar.monthNames = MONTH_NAMES;
  calendar.monthNamesInSentence = MONTH_NAMES_IN_SENTENCE;
  calendar.selectedDate = today;
  calendar.onSelectDate = selectDate;
  calendar.display();

  Utility.display(overlay);
  Utility.display(cancelSubscriptionDialogue);
}

// *************************************************************************************************
// Return a text that explains what happens if a customer's subscription is ended on the given
// endDateIso, which is assumed to be before today's date. The date must be a string in "yyyy-mm-dd"
// format.
  // *** // If today is the first day of the month, the buyer might already have been charged for
         // the coming month. In that case, he will lose all of it. Include a bullet point about that?
function getImmediateCancelResultText(endDateIso)
{
  var o, p, endDate, lastDayOfMonth, lostDayCount;

  endDate = new Date(endDateIso);
  lastDayOfMonth = Utility.getLastDay(endDate);
  o = new Array(8);
  p = 0;

  o[p++] = getText(63, 'Kunden');
  o[p++] = '<ul><li>';
  o[p++] = getText(60, 'Mister tilgang umiddelbart.');
  o[p++] = '</li>';
  if (endDateIso !== lastDayOfMonth)
  {
    o[p++] = '<li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(61, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(52, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li>';
  }
  o[p++] = '</ul>';
  return o.join('');
}

// *************************************************************************************************
// Return a text that explains what happens if a customer's subscription is ended on the given
// endDateIso. The date must be a string in "yyyy-mm-dd" format.
  // *** // The calculation will be wrong if the buyer just booked, and paid for more than the first month.
function getCustomCancelResultText(endDateIso)
{
  var o, p, today, todayIso, endDate, lastDayOfMonth, lostDayCount;

  today = new Date();
  todayIso = Utility.getIsoDate(today);
  lastDayOfMonth = Utility.getLastDay(today);
  endDate = new Date(endDateIso);
  o = new Array(9);
  p = 0;
  o[p++] = getText(63, 'Kunden');
  o[p++] = '<ul>';

  // The end date cannot be before today's date, but catch the case here.
  if (endDateIso <= todayIso)
  {
    // The end date is today.
    o[p++] = '<li>';
    o[p++] = getText(64, 'Beholder lagerboden fram til midnatt.');
    o[p++] = '</li><li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(today, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(61, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(52, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li></ul>';
    return o.join('');
  }

  if (endDateIso === lastDayOfMonth)
  {
    // The end date is the last day of the current month.
    o[p++] = '<li>';
    o[p++] = getText(51, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li></ul>';
    return o.join('');
  }

  if (endDateIso < lastDayOfMonth)
  {
    // The end date is later this month, but before the last day of the month.
    o[p++] = '<li>';
    o[p++] = getText(51, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li><li><span class="warning-text">';
    lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
    if (lostDayCount === 1)
      o[p++] = getText(61, 'Mister &eacute;n dags leie.');
    else
      o[p++] = getText(52, 'Mister $1 dagers leie.', [String(lostDayCount)]);
    o[p++] = '</span></li></ul>';
    return o.join('');
  }

  lastDayOfMonth = Utility.getLastDay(endDate);
  if (endDateIso === lastDayOfMonth)
  {
    // The end date is the last day of a future month.
    o[p++] = '<li>';
    o[p++] = getText(53, 'Trekkes som vanlig til og med $1 $2.',
      [MONTH_NAMES_IN_SENTENCE[endDate.getMonth()], String(endDate.getFullYear())]);
    o[p++] = '</li><li>';
    o[p++] = getText(51, 'Beholder lagerboden til og med $1.', [endDateIso]);
    o[p++] = '</li></ul>';
    return o.join('');
  }

  // The end date is any other day of a future month.
  o[p++] = '<li>';
  o[p++] = getText(53, 'Trekkes som vanlig til og med $1 $2.',
    [MONTH_NAMES_IN_SENTENCE[endDate.getMonth()], String(endDate.getFullYear())]);
  o[p++] = '</li><li>';
  o[p++] = getText(51, 'Beholder lagerboden til og med $1.', [endDateIso]);
  o[p++] = '</li><li><span class="warning-text">';
  lostDayCount = Utility.getDaysBetween(endDate, lastDayOfMonth);
  if (lostDayCount === 1)
    o[p++] = getText(61, 'Mister &eacute;n dags leie.');
  else
    o[p++] = getText(52, 'Mister $1 dagers leie.', [String(lostDayCount)]);
  o[p++] = '</span></li></ul>';
  return o.join('');
}

// *************************************************************************************************
// Event handler called when the user chooses a different way to cancel a subscription.
function switchCancelType()
{
  var value;

  value = parseInt(Utility.getRadioButtonValue('cancel_type', 0), 10);
  Utility.setDisplayState(standardCancelBox, value === 0);
  Utility.setDisplayState(immediateCancelBox, value === 1);
  Utility.setDisplayState(customCancelBox, value === 2);
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
// Select the given date as the end date of the subscription. selectedDate is a string with a date
// in ISO format - that is, "yyyy-mm-dd".
function selectDate(sender, selectedDate)
{
  endDateEdit.value = selectedDate;
  customCancelResultBox.innerHTML = getCustomCancelResultText(selectedDate);
  closeCalendar();
}

// *************************************************************************************************

function closeCancelSubscriptionDialogue()
{
  Utility.hide(cancelSubscriptionDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// *** Price plan functions.
// *************************************************************************************************
// Return HTML code for a button to open a price plan window for the price plan with the given
// pricePlanIndex, for the subscription with the given index. The button displays the current price.
// If the price is -1, it will not be displayed.
function getPriceButton(index, pricePlanIndex)
{
  var o, p, price;

  if (pricePlanIndex < 0)
    return '';
  o = new Array(8);
  p = 0;

  o[p++] = '<button type="button" class="table-button" onclick="displayPricePlan(';
  o[p++] = String(index);
  o[p++] = ', ';
  o[p++] = String(pricePlanIndex);
  o[p++] = ');">';
  price = PricePlan.getPriceFromPricePlan(subscriptions, index, pricePlanIndex);
  if (price >= 0)
  {
    o[p++] = String(price);
    o[p++] = ',-';
  }
  else
    o[p++] = getText(26, 'Pris');
  o[p++] = '</button>';
  return o.join('');
}

// *************************************************************************************************
// Open a dialogue box that displays the price plan with the given pricePlanIndex, for the
// subscription with the given index.
function displayPricePlan(index, pricePlanIndex)
{
  var o, p, i, planType, planLines, subscriptionEndDate;

  planLines = PricePlan.getPricePlanLines(subscriptions, index, pricePlanIndex);
  if (planLines === null)
    return;
  o = new Array((planLines.length * 11) + 14);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  planType = subscriptions[index][c.sub.PRICE_PLANS][pricePlanIndex][c.sub.PLAN_TYPE];
  if (planType < 0)
    o[p++] = getText(27, 'Prishistorikk, leie');
  else
    o[p++] = getText(28, 'Prishistorikk, $1', [ADDITIONAL_PRODUCT_TEXTS[planType]]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(19, 'Fra dato');
  o[p++] = '</th><th>';
  o[p++] = getText(20, 'Til dato');
  o[p++] = '</th><th>';
  o[p++] = getText(26, 'Pris');
  o[p++] = '</th><th>';
  o[p++] = getText(29, 'Grunn');
  o[p++] = '</th><th>';
  o[p++] = getText(30, 'Beskrivelse');
  o[p++] = '</th></tr></thead><tbody>';
  subscriptionEndDate = subscriptions[index][c.sub.END_DATE];
  for (i = 0; i < planLines.length; i++)
  {
    // Do not display this line in the price plan if the subscription ends before this line comes
    // into effect.
    if ((subscriptionEndDate !== '') && (planLines[i][c.sub.LINE_START_DATE] > subscriptionEndDate))
      continue;
    o[p++] = '<tr><td>';
    o[p++] = planLines[i][c.sub.LINE_START_DATE];
    o[p++] = '</td><td>';
    o[p++] = getEndDate(planLines, i, subscriptionEndDate);
    o[p++] = '</td><td>';
    o[p++] = String(planLines[i][c.sub.LINE_PRICE]);
    o[p++] = ',-</td><td>';
    if (planLines[i][c.sub.LINE_CAUSE] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = planLines[i][c.sub.LINE_CAUSE];
    o[p++] = '</td><td>';
    if (planLines[i][c.sub.LINE_DESCRIPTION] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = planLines[i][c.sub.LINE_DESCRIPTION];
    o[p++] = '</td></tr>';
  }
  if (subscriptions[index][c.sub.END_DATE] !== '')
  {
    o[p++] = '<tr><td>';
    o[p++] = subscriptions[index][c.sub.END_DATE];
    o[p++] = '</td><td>&nbsp;</td><td>&nbsp;</td><td>';
    o[p++] = getText(31, 'Abonnementet avsluttet');
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table></div><div class="dialogue-footer"><button type="button" onclick="closePricePlanDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(32, 'Lukk');
  o[p++] = '</button></div></form>';

  pricePlanDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(pricePlanDialogue);
}

// *************************************************************************************************
// Return the end date of the price plan with the given index, in the given array of price plan
// lines. The end date is the day before the start date of the next line. If there is no next line,
// the price will apply until further notice, unless the subscription itself has an end date - as
// given in subscriptionEndDate. If the subscription has no end date, subscriptionEndDate should be
// an empty string.
function getEndDate(planLines, index, subscriptionEndDate)
{
  var endDate;

  if (index >= (planLines.length - 1))
  {
    // This is the last element in the price plan. If the subscription will end, that's the end
    // date. Otherwise, the price applies until further notice.
    if (subscriptionEndDate !== '')
      return subscriptionEndDate;
    return getText(33, 'Inntil videre');
  }
  // The price ends the day before the next price in the price plan takes effect. However, if the
  // subscription ends before that, that's the end date.
  endDate = Utility.getDayBefore(planLines[index + 1][c.sub.LINE_START_DATE]);
  if ((subscriptionEndDate !== '') && (subscriptionEndDate < endDate))
    return subscriptionEndDate;
  return endDate;
}

// *************************************************************************************************
// Close the price plan dialogue box.
function closePricePlanDialogue()
{
  Utility.hide(pricePlanDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// *** Payment history functions.
// *************************************************************************************************
// Display the payment history for the subscription with the given index in the subscriptions table.
// If the payment history has already been loaded, display it straight away. Otherwise, load it from
// the server and display it when received.
function loadPaymentHistory(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, subscriptions))
  {
    // See if the payment history for this subscription is already available. If so, display it.
    if (subscriptions[index][c.sub.PAYMENT_HISTORY] !== null)
      displayPaymentHistory(index);
    else
    {
      // Fetch the payment history from the server, then store and display it.
      paymentHistoryDialogue.innerHTML = '<p>' +
        getText(34, 'Laster ordrehistorikk. Vennligst vent...') + '</p>';
      Utility.display(overlay);
      Utility.display(paymentHistoryDialogue);
      fetch('/subscription/json/admin_payment_history.php?subscription_id=' +
        String(subscriptions[index][c.sub.ID]) + '&user_id=' + String(user.id))
        .then(Utility.extractJson)
        .then(storePaymentHistory)
        .catch(logPaymentHistoryError);
    }
  }
}

// *************************************************************************************************
// Log an error that occurred while fetching the payment history from the server.
function logPaymentHistoryError(error)
{
  console.error('Error fetching payment history: ' + error);
  closePaymentHistoryDialogue();
}

// *************************************************************************************************
// Store payment history for a single subscription in the subscriptions table, then display it.
function storePaymentHistory(data)
{
  var index;

  if (data && data.resultCode)
  {
    if (Utility.isError(data.resultCode))
    {
      console.error('Error fetching payment history: result code: ' + String(data.resultCode));
      closePaymentHistoryDialogue();
    }
    else
    {
      if (data.subscriptionId && data.paymentHistory)
      {
        index = Utility.getSubscriptionIndex(data.subscriptionId);
        if (index < 0)
        {
          console.error('Error fetching payment history: subscription ID ' +
            String(data.subscriptionId) + ' not found in the table.');
          closePaymentHistoryDialogue();
        }
        else
        {
          // All the information was present and valid. Store and display the payment history.
          subscriptions[index][c.sub.PAYMENT_HISTORY] = data.paymentHistory;
          displayPaymentHistory(index);
        }
      }
      else
      {
        console.error('Error fetching payment history: subscription ID or payment history missing.');
        closePaymentHistoryDialogue();
      }
    }
  }
  else
  {
    console.error('Error fetching payment history: data object or result code missing.');
    closePaymentHistoryDialogue();
  }
}

// *************************************************************************************************
// Display the payment history for the subscription with the given index in the subscriptions table.
// This method assumes that the payment history is available in the subscriptions table.
function displayPaymentHistory(index)
{
  var o, p, i, paymentHistory, style, amount;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, subscriptions))
    return;
  paymentHistory = subscriptions[index][c.sub.PAYMENT_HISTORY];
  o = new Array((paymentHistory.length * 36) + 20);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  o[p++] = getText(35, 'Ordrehistorikk for $1, $2',
    [subscriptions[index][c.sub.PRODUCT_NAME], Utility.getLocationName(subscriptions[index][c.sub.LOCATION_ID])]);
  o[p++] = '</h1></div><div class="dialogue-content"><table cellspacing="0" cellpadding="0"><thead><tr><th>&nbsp;</th><th>';
  o[p++] = getText(36, 'Type');
  o[p++] = '</th><th>';
  o[p++] = getText(37, 'Fakturanr');
  o[p++] = '</th><th>';
  o[p++] = getText(38, 'Betalingsm&aring;te');
  o[p++] = '</th><th>';
  o[p++] = getText(39, 'Utstedt');
  o[p++] = '</th><th>';
  o[p++] = getText(40, 'Forfallsdato');
  o[p++] = '</th><th>';
  o[p++] = getText(18, 'Status');
  o[p++] = '</th><th>';
  o[p++] = getText(41, 'Sum');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < paymentHistory.length; i++)
  {
    if (paymentHistory[i][c.pay.OPEN])
      style = ' class="payment-details-open"';
    else
      style = '';
    o[p++] = '<tr><td';
    o[p++] = style;
    o[p++] = '><button type="button" class="icon-button" onclick="togglePaymentLine(';
    o[p++] = String(index);
    o[p++] = ', ';
    o[p++] = String(i);
    o[p++] = ');">';
    if (paymentHistory[i][c.pay.OPEN])
      o[p++] = '<i class="fa-solid fa-minus"></i>';
    else
      o[p++] = '<i class="fa-solid fa-plus"></i>';
    o[p++] = '</button></td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.NAME];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.ID];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = PAYMENT_METHOD_TEXTS[paymentHistory[i][c.pay.PAYMENT_METHOD]];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.ORDER_DATE];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = paymentHistory[i][c.pay.PAY_BY_DATE];
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = Utility.getStatusLabel(PAYMENT_STATUS_TEXTS, PAYMENT_STATUS_COLOURS,
      paymentHistory[i][c.pay.PAYMENT_STATUS]);
    o[p++] = '</td><td class="currency">';
    amount = getOrderAmount(index, i);
    o[p++] = String(amount);
    o[p++] = ',-</td></tr>';
    // Write table of order lines, if the user has opened the box.
    if (paymentHistory[i][c.pay.OPEN])
      o[p++] = getOrderLines(paymentHistory, i, amount);
  }
  o[p++] = '</tbody></table></div><div class="dialogue-footer"><button type="button" onclick="closePaymentHistoryDialogue();"><i class="fa-solid fa-check"></i> ';
  o[p++] = getText(32, 'Lukk');
  o[p++] = '</button></div></form>';

  paymentHistoryDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(paymentHistoryDialogue);
}

// *************************************************************************************************
// Return HTML code for a table line that contains a box that displays order lines for the order
// with the given index in the given payment history. amount is the total amount for the order.
function getOrderLines(paymentHistory, orderIndex, amount)
{
  var o, p, i, orderLines;

  orderLines = paymentHistory[orderIndex][c.pay.ORDER_LINES];
  o = new Array((orderLines.length * 7) + 12);
  p = 0;

  // Headline.
  o[p++] = '<tr class="payment-details"><td colspan="8" class="payment-details"><div class="payment-details"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(30, 'Beskrivelse');
  o[p++] = '</th><th>';
  o[p++] = getText(11, 'Produkt-ID');
  o[p++] = '</th><th>';
  o[p++] = getText(43, 'Bel&oslash;p');
  o[p++] = '</th></tr></thead><tbody>';
  // Order lines.
  for (i = 0; i < orderLines.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = orderLines[i][c.pay.LINE_TEXT];
    o[p++] = '</td><td>';
    o[p++] = String(orderLines[i][c.pay.LINE_ID]);
    o[p++] = '</td><td class="currency">';
    o[p++] = String(orderLines[i][c.pay.LINE_AMOUNT]);
    o[p++] = ',-</td></tr>';
  }
  // Sum.
  o[p++] = '<tr><td colspan="2" class="sum">';
  o[p++] = getText(41, 'Sum');
  o[p++] = '</td><td class="sum currency">';
  o[p++] = String(amount);
  o[p++] = ',-</td></tr></tbody></table></div></td></tr>';
  return o.join('');
}

// *************************************************************************************************
// Return the total amount paid for an order. The order is specified by the given index into the
// subscription table, and then the given orderIndex for that subscription.
function getOrderAmount(subscriptionIndex, orderIndex)
{
  var paymentHistory, orderLines, i, amount;

  amount = 0;
  subscriptionIndex = parseInt(subscriptionIndex, 10);
  if (Utility.isValidIndex(subscriptionIndex, subscriptions))
  {
    paymentHistory = subscriptions[subscriptionIndex][c.sub.PAYMENT_HISTORY];

    orderIndex = parseInt(orderIndex, 10);
    if (Utility.isValidIndex(orderIndex, paymentHistory))
    {
      orderLines = paymentHistory[orderIndex][c.pay.ORDER_LINES];
      for (i = 0; i < orderLines.length; i++)
        amount += orderLines[i][c.pay.LINE_AMOUNT];
    }
  }
  return amount;
}

// *************************************************************************************************

function togglePaymentLine(subscriptionIndex, orderIndex)
{
  var paymentHistory;

  subscriptionIndex = parseInt(subscriptionIndex, 10);
  if (Utility.isValidIndex(subscriptionIndex, subscriptions))
  {
    paymentHistory = subscriptions[subscriptionIndex][c.sub.PAYMENT_HISTORY];

    orderIndex = parseInt(orderIndex, 10);
    if (Utility.isValidIndex(orderIndex, paymentHistory))
    {
      paymentHistory[orderIndex][c.pay.OPEN] = !paymentHistory[orderIndex][c.pay.OPEN];
      displayPaymentHistory(subscriptionIndex);
    }
  }
}

// *************************************************************************************************
// Close the payment history dialogue.
function closePaymentHistoryDialogue()
{
  Utility.hide(paymentHistoryDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Encode line breaks in the notes textarea before form submission
function encodeNotesBeforeSubmit() {
  try {
    var notesTextarea = document.getElementById('inlineUserNotesTextArea');
    var encodedField = document.getElementById('encodedUserNotes');
    
    if (notesTextarea && encodedField) {
      // Store the original value in the user_notes field for display
      var originalValue = notesTextarea.value;
      
      // Encode line breaks and store in the hidden field
      encodedField.value = Utility.encodeLineBreaks(originalValue);
      
      // Update the form to use the encoded value
      notesTextarea.name = 'original_user_notes'; // Rename original field
      
      console.log("Notes encoded for submission");
      return true;
    } else {
      console.error("Could not find required form elements");
      return false;
    }
  } catch (e) {
    console.error("Error encoding notes: " + e.message);
    return false;
  }
}

// *************************************************************************************************
// Add a simple backup save function with minimal code for testing
function saveInlineUserNotes()
{
  try {
    // Show loading spinner
    var saveButton = document.getElementById('saveNotesButton');
    var spinner = document.getElementById('saveNotesSpinner');
    
    if (saveButton && spinner) {
      saveButton.disabled = true;
      spinner.style.display = 'inline-block';
    }
    
    var formData = new FormData();
    formData.append('action', 'set_user_notes');
    formData.append('user_id', String(user.id));
    formData.append('user_notes', Utility.encodeLineBreaks(inlineUserNotesTextArea.value));
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/subscription/json/user_notes.php', true);
    
    xhr.onload = function() {
      // Hide spinner and re-enable button
      if (saveButton && spinner) {
        saveButton.disabled = false;
        spinner.style.display = 'none';
      }
      
      if (xhr.status === 200) {
        console.log("Notes saved successfully!");
        try {
          var response = JSON.parse(xhr.responseText);
          if (response && response.userNotes) {
            userNotes = Utility.decodeLineBreaks(response.userNotes);
            inlineUserNotesTextArea.value = userNotes;
            console.log("Notes updated: " + userNotes);
          }
        } catch (e) {
          console.error("Response parsing error: " + e.message);
        }
      } else {
        console.error("Server returned status: " + xhr.status);
      }
    };
    
    xhr.onerror = function() {
      // Hide spinner and re-enable button on error
      if (saveButton && spinner) {
        saveButton.disabled = false;
        spinner.style.display = 'none';
      }
      console.error("Request failed");
    };
    
    xhr.send(formData);
    console.log("Request sent");
  } catch (e) {
    // Hide spinner and re-enable button on error
    if (saveButton && spinner) {
      saveButton.disabled = false;
      spinner.style.display = 'none';
    }
    console.error("Error in save function: " + e.message);
  }
}

// *************************************************************************************************

