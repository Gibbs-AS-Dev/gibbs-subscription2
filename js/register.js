// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var newUserForm, newIndividualButton, newCompanyButton, individualDataBox, companyDataBox,
  userNameEdit, firstNameEdit, lastNameEdit, newCompanyNameEdit, newCompanyIdEdit, phoneEdit,
  passwordEdit, submitButton;

// The currently selected entity type. That is, whether the new customer is a company or a private
// individual.
var selectedEntityType = ENTITY_TYPE_INDIVIDUAL;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['newUserForm', 'newIndividualButton', 'newCompanyButton',
    'individualDataBox', 'companyDataBox', 'userNameEdit', 'firstNameEdit', 'lastNameEdit',
    'newCompanyNameEdit', 'newCompanyIdEdit', 'phoneEdit', 'passwordEdit', 'submitButton']);

  enableSubmitButton();
  userNameEdit.focus();
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
  // The user is not logged in. He has to fill in all the mandatory user information fields. There
  // are different fields, depending on whether the new user is a company or an individual.
  if (selectedEntityType === ENTITY_TYPE_INDIVIDUAL)
  {
    submitButton.disabled = (firstNameEdit.value === '') || (lastNameEdit.value === '') ||
      (userNameEdit.value === '') || !Utility.isValidEMail(userNameEdit.value) ||
      (phoneEdit.value === '') || (addressEdit.value === '') || (postcodeEdit.value === '') ||
      (areaEdit.value === '') || (passwordEdit.value.length < PASSWORD_MIN_LENGTH);
  }
  else
  {
    submitButton.disabled = (newCompanyNameEdit.value === '') || (newCompanyIdEdit.value === '') ||
      (userNameEdit.value === '') || !Utility.isValidEMail(userNameEdit.value) ||
      (phoneEdit.value === '') || (addressEdit.value === '') || (postcodeEdit.value === '') ||
      (areaEdit.value === '') || (passwordEdit.value.length < PASSWORD_MIN_LENGTH);
  }
}

// *************************************************************************************************
