// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var submitButton, userNameEdit, passwordEdit;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['submitButton', 'userNameEdit', 'passwordEdit']);

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
