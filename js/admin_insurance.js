// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var insuranceProductsBox, overlay, editInsuranceProductDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editInsuranceProductForm, nameEdit, priceEdit, descriptionEdit, productTypesBox, locationsBox,
  submitButton;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['insuranceProductsBox', 'overlay', 'editInsuranceProductDialogue']);

  displayInsuranceProducts();

  // Display the results of a previous operation, if required.
  if (resultCode >= 0)
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1.',
      [String(resultCode)]));
}

// *************************************************************************************************

function displayInsuranceProducts()
{
  var o, p, i;
  
  if (insuranceProducts.length <= 0)
  {
    insuranceProductsBox.innerHTML = '<div class="form-element">' +
      getText(1, 'Det er ikke opprettet noen forsikringer enn&aring;.') + '</div>';
    return;
  }

  o = new Array((insuranceProducts.length * 15) + 16);
  p = 0;
  
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(2, 'Navn');
  o[p++] = '</th><th>';
  o[p++] = getText(3, 'Beskrivelse');
  o[p++] = '</th><th>';
  o[p++] = getText(4, 'Pris');
  o[p++] = '</th><th>';
  o[p++] = getText(5, 'For bodtyper');
  o[p++] = '</th><th>';
  o[p++] = getText(6, 'For lager');
  o[p++] = '</th><th>';
  o[p++] = getText(7, 'Rediger');
  o[p++] = '</th><th>';
  o[p++] = getText(8, 'Slett');
  o[p++] = '</th></tr></thead><tbody>';
  for (i = 0; i < insuranceProducts.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = insuranceProducts[i][c.ins.NAME];
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(insuranceProducts[i][c.ins.DESCRIPTION], 50);
    o[p++] = '</td><td>';
    o[p++] = String(insuranceProducts[i][c.ins.PRICE]);
    o[p++] = ',-</td><td>';
    if (insuranceProducts[i][c.ins.FOR_PRODUCT_TYPES] === null)
      o[p++] = getText(9, 'Alle bodtyper');
    else
      o[p++] = getText(10, '$1 av $2 bodtyper',
        [
          String(insuranceProducts[i][c.ins.FOR_PRODUCT_TYPES].length),
          String(productTypes.length)
        ]);
    o[p++] = '</td><td>';
    if (insuranceProducts[i][c.ins.FOR_LOCATIONS] === null)
      o[p++] = getText(11, 'Alle lager');
    else
      o[p++] = getText(12, '$1 av $2 lager',
        [
          String(insuranceProducts[i][c.ins.FOR_LOCATIONS].length),
          String(locations.length)
        ]);
    o[p++] = '</td><td><button type="button" class="icon-button" onclick="displayEditInsuranceProductDialogue(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-pen-to-square"></i></button></td><td><button type="button" class="icon-button" onclick="deleteInsuranceProduct(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-trash"></i></button></td></tr>';
  }
  o[p++] = '</tbody></table>';

  insuranceProductsBox.innerHTML = o.join('');
}

// *************************************************************************************************

function deleteInsuranceProduct(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, insuranceProducts) &&
    confirm(getText(13, 'Er du sikker på at du vil slette forsikring: $1?',
      [insuranceProducts[index][c.ins.NAME]])))
  {
    o = new Array(3);
    p = 0;

    o[p++] = '<form id="deleteInsuranceProductForm" action="/subscription/html/admin_insurance.php" method="post"><input type="hidden" name="action" value="delete_insurance_product" /><input type="hidden" name="id" value="';
    o[p++] = String(insuranceProducts[index][c.ins.ID]);
    o[p++] = '" /></form>';
    editInsuranceProductDialogue.innerHTML = o.join('');
    document.getElementById('deleteInsuranceProductForm').submit();
  }
}

// *************************************************************************************************

function displayEditInsuranceProductDialogue(index)
{
  var o, p, isNew, i, forProductTypes, forAllProductTypes, forLocations, forAllLocations;
  
  index = parseInt(index, 10);
  isNew = index === -1;
  if (!(isNew || Utility.isValidIndex(index, insuranceProducts)))
    return;
  o = new Array((productTypes.length * 13) + (locations.length * 13) + 57);
  p = 0;
  
  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(14, 'Opprett forsikring');
  else
    o[p++] = getText(15, 'Rediger forsikring');
  o[p++] = '</h1></div>';
  
  // Content.
  o[p++] = '<div class="dialogue-content"><form id="editInsuranceProductForm" action="/subscription/html/admin_insurance.php" method="post"><div class="form-element"><input type="hidden" name="product_type_count" value="';
  o[p++] = String(productTypes.length);
  o[p++] = '" /><input type="hidden" name="location_count" value="';
  o[p++] = String(locations.length);
  o[p++] = '" />';
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_insurance_product" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_insurance_product" /><input type="hidden" name="id" value="';
    o[p++] = String(insuranceProducts[index][c.ins.ID]);
    o[p++] = '" />';
  }
  // Name.
  o[p++] = '<label for="nameEdit" class="standard-label">';
  o[p++] = getText(19, 'Navn:');
  o[p++] = '</label> <input type="text" id="nameEdit" name="name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = insuranceProducts[index][c.ins.NAME];
    o[p++] = '"';
  }
  // Price.
  o[p++] = ' /></div><div class="form-element"><label for="priceEdit" class="standard-label">';
  o[p++] = getText(26, 'Pris pr mnd:');
  o[p++] = '</label> <input type="number" id="priceEdit" name="price" min="0" class="numeric" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = insuranceProducts[index][c.ins.PRICE];
    o[p++] = '"';
  }
  // Description.
  o[p++] = ' /></div><div class="form-element"><label for="descriptionEdit" class="standard-label">';
  o[p++] = getText(20, 'Beskrivelse:');
  o[p++] = '</label><textarea id="descriptionEdit" name="description" rows="10" cols="80" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">';
  if (!isNew)
    o[p++] = insuranceProducts[index][c.ins.DESCRIPTION];
  // For product types.
  o[p++] = '</textarea></div><div class="form-element">';
  o[p++] = getText(23, 'Forsikringen tilbys:');
  o[p++] = '</div><div class="column-container"><div class="column for-product-types-column"><div class="form-element"><input type="radio" id="allProductTypesRadio" name="for_all_product_types" value="1" onchange="toggleProductTypesBox(this);"';
  if (isNew)
    forProductTypes = null;
  else
    forProductTypes = insuranceProducts[index][c.ins.FOR_PRODUCT_TYPES];
  forAllProductTypes = forProductTypes === null;
  if (forAllProductTypes)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="allProductTypesRadio">';
  o[p++] = getText(21, 'For alle bodtyper');
  o[p++] = '</label></div><div class="form-element"><input type="radio" id="someProductTypesRadio" name="for_all_product_types" value="0" onchange="toggleProductTypesBox(this);"';
  if (!forAllProductTypes)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="someProductTypesRadio">';
  o[p++] = getText(22, 'For noen bodtyper');
  o[p++] = '</label><div id="productTypesBox" class="indented-box"';
  if (forAllProductTypes)
    o[p++] = ' style="display: none;"';
  o[p++] = '><ul class="checkbox-list">';
  for (i = 0; i < productTypes.length; i++)
  {
    o[p++] = '<li><input type="checkbox" id="productType';
    o[p++] = String(i);
    o[p++] = '" name="for_product_type_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = String(productTypes[i][c.typ.ID]);
    o[p++] = '"';
    if (forAllProductTypes || Utility.valueInArray(productTypes[i][c.typ.ID], forProductTypes))
      o[p++] = ' checked="checked"';
    o[p++] = ' /> <label for="productType';
    o[p++] = String(i);
    o[p++] = '">';
    o[p++] = productTypes[i][c.typ.NAME];
    o[p++] = '</label></li>';
  }
  // For locations.
  o[p++] = '</ul></div></div></div><div class="column for-locations-column"><div class="form-element"><input type="radio" id="allLocationsRadio" name="for_all_locations" value="1" onchange="toggleLocationsBox(this);"';
  if (isNew)
    forLocations = null;
  else
    forLocations = insuranceProducts[index][c.ins.FOR_LOCATIONS];
  forAllLocations = forLocations === null;
  if (forAllLocations)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="allLocationsRadio">';
  o[p++] = getText(24, 'Ved alle lager');
  o[p++] = '</label></div><div class="form-element"><input type="radio" id="someLocationsRadio" name="for_all_locations" value="0" onchange="toggleLocationsBox(this);"';
  if (!forAllLocations)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="someLocationsRadio">';
  o[p++] = getText(25, 'Ved noen lager');
  o[p++] = '</label><div id="locationsBox" class="indented-box"';
  if (forAllLocations)
    o[p++] = ' style="display: none;"';
  o[p++] = '><ul class="checkbox-list">';
  for (i = 0; i < locations.length; i++)
  {
    o[p++] = '<li><input type="checkbox" id="location';
    o[p++] = String(i);
    o[p++] = '" name="for_location_';
    o[p++] = String(i);
    o[p++] = '" value="';
    o[p++] = String(locations[i][c.loc.ID]);
    o[p++] = '"';
    if (forAllLocations || Utility.valueInArray(locations[i][c.loc.ID], forLocations))
      o[p++] = ' checked="checked"';
    o[p++] = ' /> <label for="location';
    o[p++] = String(i);
    o[p++] = '">';
    o[p++] = locations[i][c.loc.NAME];
    o[p++] = '</label></li>';
  }
  o[p++] = '</ul></div></div></div></div></form></div>';
  
  // Footer.
  o[p++] = '<div class="dialogue-footer"><button type="button" id="submitButton" onclick="editInsuranceProductForm.submit();"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(16, 'Opprett');
  else
    o[p++] = getText(17, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeInsuranceProductDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Avbryt');
  o[p++] = '</button></div>';

  editInsuranceProductDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editInsuranceProductForm', 'nameEdit', 'priceEdit', 'descriptionEdit',
    'productTypesBox', 'locationsBox', 'submitButton']);

  Utility.display(overlay);
  Utility.display(editInsuranceProductDialogue);
  enableSubmitButton();
}

// *************************************************************************************************

function closeInsuranceProductDialogue()
{
  Utility.hide(editInsuranceProductDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableSubmitButton()
{
  var price;
  
  price = parseInt(priceEdit.value, 10);
  submitButton.disabled = (nameEdit.value === '') || !isFinite(price) || (price < 0) ||
    (descriptionEdit.value === '');
}

// *************************************************************************************************

function toggleProductTypesBox(radioButton)
{
  if (radioButton.value === '1')
    Utility.hide(productTypesBox);
  else
    Utility.display(productTypesBox);
  enableSubmitButton();
}

// *************************************************************************************************

function toggleLocationsBox(radioButton)
{
  if (radioButton.value === '1')
    Utility.hide(locationsBox);
  else
    Utility.display(locationsBox);
  enableSubmitButton();
}

// *************************************************************************************************
