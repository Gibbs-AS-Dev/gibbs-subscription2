// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var firstNameEdit, lastNameEdit, userNameEdit, phoneEdit, passwordEdit, submitButton;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['firstNameEdit', 'lastNameEdit', 'userNameEdit', 'phoneEdit',
    'passwordEdit', 'submitButton']);

  enableSubmitButton();
  firstNameEdit.focus();
}

// *************************************************************************************************

function enableSubmitButton()
{
  var emailRegexp;

  emailRegexp = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  submitButton.disabled = (firstNameEdit.value === '') || (lastNameEdit.value === '') ||
    (userNameEdit.value === '') || !emailRegexp.test(userNameEdit.value) ||
    (phoneEdit.value === '') || (passwordEdit.value.length < PASSWORD_MIN_LENGTH);
}

// *************************************************************************************************
