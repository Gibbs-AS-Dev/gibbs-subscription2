// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var capacitiesBox;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['capacitiesBox']);

  displayCapacities();
}

// *************************************************************************************************
// Display the spinner. Once visible, display capacities.
function displayCapacities()
{
  Utility.displaySpinnerThen(doDisplayCapacities);
}

// *************************************************************************************************
// Display the table of capacities.
function doDisplayCapacities()
{
  var o, p, i;
  
  if (capacities.length <= 0)
  {
    capacitiesBox.innerHTML = '<div class="form-element">' +
      getText(-1, 'Det er ikke opprettet noen lager enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  o = new Array((capacities.length * 18) + 20);
  p = 0;

  o[p++] = '<div class="toolbar"><h3>';
  o[p++] = getText(-1, 'Kapasitet');
  o[p++] = '</h3><p class="help-text">';
  o[p++] = getText(-1, 'Denne tabellen vil du etter hvert finne under et menypunkt for &quot;Statistikk&quot;. Her p&aring; dashboardet vil vi vise informasjonen i en graf i stedet.');
  o[p++] = '</p></div><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(-1, 'Navn');
  o[p++] = '</th><th class="number">';
  o[p++] = getText(-1, 'Bestilt');
  o[p++] = '</th><th class="number">';
  o[p++] = getText(-1, 'L&oslash;pende');
  o[p++] = '</th><th class="number">';
  o[p++] = getText(-1, 'Sagt opp');
  o[p++] = '</th><th class="number">';
  o[p++] = getText(-1, 'Opptatt + ledig');
  o[p++] = '</th><th class="number">';
  o[p++] = getText(-1, 'Totalt');
  o[p++] = '</th><th class="number">';
  o[p++] = getText(-1, 'Utnyttet');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < capacities.length; i++)
  {
    // Name.
    o[p++] = '<tr><td>';
    o[p++] = capacities[i][c.cap.NAME];
    // Booked count.
    o[p++] = '</td><td class="number">';
    o[p++] = String(capacities[i][c.cap.BOOKED_COUNT]);
    // Ongoing count.
    o[p++] = '</td><td class="number">';
    o[p++] = String(capacities[i][c.cap.ONGOING_COUNT]);
    // Cancelled count.
    o[p++] = '</td><td class="number">';
    o[p++] = String(capacities[i][c.cap.CANCELLED_COUNT]);
    // Occupied and free count.
    o[p++] = '</td><td class="number">';
    o[p++] = String(capacities[i][c.cap.OCCUPIED_COUNT]);
    o[p++] = ' + ';
    o[p++] = String(capacities[i][c.cap.FREE_COUNT]);
    // Total count.
    o[p++] = '</td><td class="number">';
    o[p++] = String(capacities[i][c.cap.TOTAL_COUNT]);
    // Used capacity.
    o[p++] = '</td><td class="number">';
    if (capacities[i][c.cap.USED_CAPACITY] < 0)
      o[p++] = '&nbsp;';
    else
    {
      o[p++] = (100.0 * capacities[i][c.cap.USED_CAPACITY]).toFixed(1);
      o[p++] = ' %';
    }
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  capacitiesBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
