// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var loginForm, submitButton, userNameEdit, passwordEdit;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['loginForm', 'submitButton', 'userNameEdit', 'passwordEdit']);

  document.addEventListener('keypress', handleKeyPress);
  enableSubmitButton();
  userNameEdit.focus();
}

// *************************************************************************************************

function enableSubmitButton()
{
  // We do not check the minimum length of the password here. Let the user try to log in as he
  // pleases.
  submitButton.disabled = (userNameEdit.value === '') || (passwordEdit.value === '');
}

// *************************************************************************************************
// If the user presses the <enter> key and the submit button is enabled, submit the form.
function handleKeyPress(event)
{
  if (event.key === 'Enter')
  {
    event.preventDefault();
    enableSubmitButton();
    if (!submitButton.disabled)
      Utility.displaySpinnerThenSubmit(loginForm);
  }
}

// *************************************************************************************************
