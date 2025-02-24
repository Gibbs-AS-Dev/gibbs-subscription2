// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var blockedEmailsBox, blockedPhoneNosBox;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['blockedEmailsBox', 'blockedPhoneNosBox']);

  displayBlockedEmails();
  displayBlockedPhoneNos();

  // Display the results of a previous operation, if required.
  if (resultCode >= 0)
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
      [String(resultCode)]));
}

// *************************************************************************************************

function displayBlockedEmails()
{
  // *** //
}

// *************************************************************************************************

function displayBlockedPhoneNos()
{
  // *** //
}

// *************************************************************************************************
