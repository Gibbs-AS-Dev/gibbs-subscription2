// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var resultLogBox;

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
  Utility.readPointers(['resultLogBox']);
}

// *************************************************************************************************
// Ask the server to log in as a Gibbs administrator, and report whether the operation succeeded.
function testWebhookAccess()
{
  var options;

  // Error check.
  if ((userName === '') || (password === ''))
  {
    alert('User name or password not found.');
  }

  // Compose request to test webhook access, and parse the response.
  options =
    {
      method: 'GET',
      headers:
        {
          'Authorization': 'Basic ' + btoa(userName + ':' + password)
        }
    };
  errorDisplayed = false;
  fetch('/subscription/webhooks/test_access.php', options)
    .then(Utility.extractJson)
    .then(displayLog)
    .catch(logError);
}

// *************************************************************************************************
// Display the given Javascript data object in the result log box.
function displayLog(data)
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
function logError(error)
{
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    alert('Error during asynchronous request to test_access.php: ' + error);
    window.location.href = '/subscription/html/gibbs_dashboard.php';
  }
}

// *************************************************************************************************
