// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var bulkIdEdit, userGroupIdEdit, resultLogBox;

// Flag that says whether an alert error message has already been displayed. If so, we should not
// display another.
var errorDisplayed = false;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by obtaining pointers.
function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['bulkIdEdit', 'userGroupIdEdit', 'resultLogBox']);
}

// *************************************************************************************************
// Ask the server to provide the status of a Nets bulk payment.
function readBulkPaymentStatus()
{
  var bulkId, userGroupId, parameters, options;

  // Error check.
  if ((userName === '') || (password === ''))
  {
    alert('User name or password not found.');
  }

  // Read information from the user interface.
  bulkId = bulkIdEdit.value;
  if (bulkId === '')
  {
    alert('You must supply a bulk ID.')
    return;
  }
  userGroupId = userGroupIdEdit.value;
  if (userGroupId === '')
  {
    alert('You must supply a user group ID.')
    return;
  }
  
  // Compose request to get bulk payment status, and parse the response.
  parameters = new URLSearchParams(
    {
      bulk_id: bulkId,
      user_group_id: userGroupId
    });
  options =
    {
      method: 'GET',
      headers:
        {
          'Authorization': 'Basic ' + btoa(userName + ':' + password)
        }
    };
  errorDisplayed = false;
  fetch('/subscription/webhooks/get_payment_status.php?' + parameters.toString(), options)
    .then(Utility.extractJson)
    .then(displayBulkPaymentStatusLog)
    .catch(logBulkPaymentStatusError);
}

// *************************************************************************************************
// Display the given Javascript data object in the result log box.
function displayBulkPaymentStatusLog(data)
{
  // See if the request has already failed.
  if (errorDisplayed)
    return;

  // Display the log in the user interface.
  if (data)
    resultLogBox.innerHTML = JSON.stringify(data, null, 2);
  else
    resultLogBox.innerHTML = '<p>No content returned</p>';
}

// *************************************************************************************************
// Let the user know an asynchronous request failed.
function logBulkPaymentStatusError(error)
{
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    alert('Error during asynchronous request to get_payment_status.php: ' + error);
    window.location.href = '/subscription/html/gibbs_dashboard.php';
  }
}

// *************************************************************************************************
