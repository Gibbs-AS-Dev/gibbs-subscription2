// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var monthEdit, simulationCheckbox, userGroupIdEdit, resultLogBox;

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
  Utility.readPointers(['monthEdit', 'simulationCheckbox', 'userGroupIdEdit', 'resultLogBox']);
}

// *************************************************************************************************
// Compose request to the monthly orders file, using the credentials given by the server, then
// display the results.
function createMonthlyOrders()
{
  var month, monthRegex, mode, parameters, options;

  // Error check.
  if ((userName === '') || (password === ''))
  {
    alert('User name or password not found.');
  }

  // Read information from the user interface. The month must have the format "yyyy-mm".
  month = monthEdit.value;
  monthRegex = /^\d{4}-(0[1-9]|1[0-2])$/;
  if (!monthRegex.test(month))
  {
    alert('Invalid month.');
    return;
  }
  if (simulationCheckbox.checked)
    mode = MODE_SIMULATION;
  else
    mode = MODE_NORMAL;

  // Compose request to create monthly orders, and parse the response.
  parameters = new URLSearchParams(
    {
      month: month,
      mode: mode
    });
  if (userGroupIdEdit.value !== '')
    parameters.append('user_group_ids', userGroupIdEdit.value);
  options =
    {
      method: 'GET',
      headers:
        {
          'Authorization': 'Basic ' + btoa(userName + ':' + password)
        }
    };
  errorDisplayed = false;
  fetch('/subscription/webhooks/create_monthly_orders.php?' + parameters.toString(), options)
    .then(Utility.extractJson)
    .then(displayCreateOrdersLog)
    .catch(logCreateOrdersError);
}

// *************************************************************************************************
// Display the given Javascript data object in the result log box.
function displayCreateOrdersLog(data)
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
function logCreateOrdersError(error)
{
  if (!errorDisplayed)
  {
    errorDisplayed = true;
    alert('Error during asynchronous request to create_monthly_orders.php: ' + error);
    window.location.href = '/subscription/html/gibbs_dashboard.php';
  }
}

// *************************************************************************************************
