// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// Price rule type constants. Price rules have different types:
//   Capacity           Changes to the base monthly price due to the location being very full or
//                      very empty.
//   Special offer      Time limited offers that modify the price for a number of months, then
//                      revert to the base price.
var RULE_TYPE_CAPACITY = 0;
var RULE_TYPE_SPECIAL_OFFER = 1;

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var capacityPriceRulesBox, specialOfferPriceRulesBox, overlay, editPriceRuleDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editPriceRuleForm, nameEdit, startDateEdit, editStartDateButton, closeStartDateButton,
  startDateCalendarBox, endDateEdit, editEndDateButton, closeEndDateButton, endDateCalendarBox,
  productTypesBox, locationsBox, priceModEditorBox, errorMessageBox, submitButton;

// The tabset that displays different kinds of price rules.
var tabset;

// The sorting object that controls the sorting of the capacityPriceRules table.
var capacityRuleSorting;

// The sorting object that controls the sorting of the specialOfferPriceRules table.
var specialOfferRuleSorting;

// The popup menu for the capacityPriceRules table.
var capacityMenu;

// The popup menu for the specialOfferPriceRules table.
var specialOfferMenu;

// The calendars that allow you to edit dates. These are created when the dialogue box is displayed.
var startDateCalendar = null;
var endDateCalendar = null;

// Flag that says whether the price rule currently being edited is a new price rule.
var isNew = false;

// The price rule that is currently being edited. This is read-only, and should not be modified.
var editedPriceRule = null;

// The type of the edited price rule. Use the RULE_TYPE_ constants.
var editedRuleType = -1;

// The price mods that are currently being edited. These may have been modified, but not yet stored.
var editedPriceMods = null;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and displaying price rules.
function initialise()
{
  // Obtain pointers to user interface elements.
  Utility.readPointers(['capacityPriceRulesBox', 'specialOfferPriceRulesBox', 'overlay',
    'editPriceRuleDialogue']);

  // Create tabset.
  tabset = new Tabset(
    [
      getText(0, 'Prisregler for kapasitet'),
      getText(1, 'Prisregler for kampanjer og tilbud')
    ], initialTab);
  tabset.display();

  // Create the popup menus.
  capacityMenu = new PopupMenu(getCapacityPopupMenuContents);
  specialOfferMenu = new PopupMenu(getSpecialOfferPopupMenuContents);

  // Initialise capacity price rule sorting.
  capacityRuleSorting = new Sorting(capacityPriceRules,
      [
        Sorting.createUiColumn(c.pru.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.pru.STATUS, Sorting.SORT_AS_STRING,
          function (capacityPriceRule)
          {
            return STATUS_TEXTS[capacityPriceRule[c.pru.STATUS]];
          }),
        Sorting.createUiColumn(c.pru.START_DATE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.pru.END_DATE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayCapacityPriceRules
    );
  capacityRuleSorting.sortingObjectName = 'capacityRuleSorting';
  // Set the initial capacity price rule sorting. If that didn't cause capacity price rules to be
  // displayed, do so now.
  if (!capacityRuleSorting.sortOn(initialCapacityUiColumn, initialCapacityDirection))
    doDisplayCapacityPriceRules();

  // Initialise special offer price rule sorting.
  specialOfferRuleSorting = new Sorting(specialOfferPriceRules,
      [
        Sorting.createUiColumn(c.pru.NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.pru.STATUS, Sorting.SORT_AS_STRING,
          function (capacityPriceRule)
          {
            return STATUS_TEXTS[capacityPriceRule[c.pru.STATUS]];
          }),
        Sorting.createUiColumn(c.pru.START_DATE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.pru.END_DATE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplaySpecialOfferPriceRules
    );
  specialOfferRuleSorting.sortingObjectName = 'specialOfferRuleSorting';
  // Set the initial special offer price rule sorting. If that didn't cause special offer price
  // rules to be displayed, do so now.
  if (!specialOfferRuleSorting.sortOn(initialSpecialOfferUiColumn, initialSpecialOfferDirection))
    doDisplaySpecialOfferPriceRules();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert(getText(20, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Return hidden form elements that specify the current state of the page, including sorting, search
// and filter settings. These should be included whenever a request is submitted to the current
// page, so that the state is maintained when the page is reloaded.
function getPageStateFormElements()
{
  var o, p;

  o = new Array(3);
  p = 0;

  o[p++] = Utility.getHidden('active_tab', tabset.activeTabIndex);
  o[p++] = capacityRuleSorting.getPageStateFormElements(
    'sort_on_capacity_ui_column', 'capacity_sort_direction');
  o[p++] = specialOfferRuleSorting.getPageStateFormElements(
    'sort_on_special_offer_ui_column', 'special_offer_sort_direction');
  return o.join('');
}

// *************************************************************************************************
// Capacity price rules tab functions.
// *************************************************************************************************
// Display the spinner. Once visible, display capacity price rules.
function displayCapacityPriceRules()
{
  Utility.displaySpinnerThen(doDisplayCapacityPriceRules);
}

// *************************************************************************************************
// Display a table of capacity price rules in the appropriate tab. Each price rule can be expanded
// to display the price modifiers.
function doDisplayCapacityPriceRules()
{
  var o, p, i, style;

  if (capacityPriceRules.length <= 0)
  {
    capacityPriceRulesBox.innerHTML = '<div class="form-element">' +
      getText(2, 'Det er ikke opprettet noen prisregler enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  o = new Array((capacityPriceRules.length * 34) + 10);
  p = 0;
  
  // Headline.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>&nbsp;</th>';
  o[p++] = capacityRuleSorting.getTableHeader(0, getText(3, 'Navn'));
  o[p++] = capacityRuleSorting.getTableHeader(1, getText(51, 'Status'));
  o[p++] = capacityRuleSorting.getTableHeader(2, getText(4, 'Fra dato'));
  o[p++] = capacityRuleSorting.getTableHeader(3, getText(5, 'Til dato'));
  o[p++] = capacityRuleSorting.getTableHeader(4, getText(6, 'Lager'));
  o[p++] = capacityRuleSorting.getTableHeader(5, getText(7, 'Bodtyper'));
  o[p++] = capacityRuleSorting.getTableHeader(6, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < capacityPriceRules.length; i++)
  {
    if (capacityPriceRules[i][c.pru.OPEN])
      style = ' class="price-rule-open"';
    else
      style = '';
    // Open / close button.
    o[p++] = '<tr><td';
    o[p++] = style;
    o[p++] = '><button type="button" class="icon-button" onclick="toggleCapacityPriceRule(';
    o[p++] = String(i);
    o[p++] = ');">';
    if (capacityPriceRules[i][c.pru.OPEN])
      o[p++] = '<i class="fa-solid fa-minus"></i>';
    else
      o[p++] = '<i class="fa-solid fa-plus"></i>';
    // Name.
    o[p++] = '</button></td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = capacityPriceRules[i][c.pru.NAME];
    // Status.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = Utility.getStatusLabel(STATUS_TEXTS, st.pru.COLOURS,
      capacityPriceRules[i][c.pru.STATUS]);
    // Start date.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = capacityPriceRules[i][c.pru.START_DATE];
    // End date.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    if (capacityPriceRules[i][c.pru.END_DATE] === null)
      o[p++] = '&nbsp;';
    else
      o[p++] = capacityPriceRules[i][c.pru.END_DATE];
    // For locations.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    if (capacityPriceRules[i][c.pru.FOR_LOCATIONS] === null)
      o[p++] = getText(10, 'Alle lager');
    else
      if (capacityPriceRules[i][c.pru.FOR_LOCATIONS].length === 0)
        o[p++] = getText(11, 'Ingen lager');
      else
        o[p++] = getText(12, '$1 av $2 lager',
          [String(capacityPriceRules[i][c.pru.FOR_LOCATIONS].length), String(locations.length)]);
    // For product types.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    if (capacityPriceRules[i][c.pru.FOR_PRODUCT_TYPES] === null)
      o[p++] = getText(13, 'Alle bodtyper');
    else
      if (capacityPriceRules[i][c.pru.FOR_PRODUCT_TYPES].length === 0)
        o[p++] = getText(14, 'Ingen bodtyper');
      else
        o[p++] = getText(15, '$1 av $2 bodtyper',
          [String(capacityPriceRules[i][c.pru.FOR_PRODUCT_TYPES].length), String(productTypes.length)]);
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = capacityMenu.getMenuButton(i);
    o[p++] = '</td></tr>';

    // Write table of price lines, if the user has opened the box.
    if (capacityPriceRules[i][c.pru.OPEN])
      o[p++] = getCapacityPriceMods(i);
  }
  o[p++] = '</tbody></table>';

  capacityPriceRulesBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML to display a table of capacity price modifiers. The items are only displayed, and
// cannot be edited.
function getCapacityPriceMods(index)
{
  var o, p, i, priceMods;

  priceMods = capacityPriceRules[index][c.pru.PRICE_MODS];
  o = new Array((priceMods.length * 7) + 8);
  p = 0;

  // Headline.
  o[p++] = '<tr class="price-mods"><td colspan="9" class="price-mods"><div class="price-mods"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(16, 'Kapasitet');
  o[p++] = '</th><th>';
  o[p++] = getText(17, 'Prisendring');
  o[p++] = '</th></tr></thead><tbody>';
  // Price modifiers with their corresponding capacities.
  for (i = 0; i < priceMods.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = String(priceMods[i][c.pru.MIN_CAPACITY]);
    o[p++] = '% - ';
    o[p++] = String(priceMods[i][c.pru.MAX_CAPACITY]);
    o[p++] = '%</td><td>';
    o[p++] = String(priceMods[i][c.pru.PRICE_MOD]);
    o[p++] = '%</td></tr>';
  }
  o[p++] = '</tbody></table></div></td></tr>';
  return o.join('');
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getCapacityPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, capacityPriceRules))
    return '';
  o = new Array(2);
  p = 0;

  // Edit price rule button.
  o[p++] = sender.getMenuItem(getText(8, 'Rediger prisregel'), 'fa-pen-to-square', true,
    'editPriceRule(RULE_TYPE_CAPACITY, ' + String(index) + ');');
  // Delete price rule button.
  o[p++] = sender.getMenuItem(getText(9, 'Slett prisregel'), 'fa-trash', true,
    'deleteCapacityPriceRule(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************
// Open or close the capacity price rule with the given index.
function toggleCapacityPriceRule(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, capacityPriceRules))
  {
    capacityPriceRules[index][c.pru.OPEN] = !capacityPriceRules[index][c.pru.OPEN];
    displayCapacityPriceRules();
  }
}

// *************************************************************************************************
// Delete the capacity price rule with the given index.
function deleteCapacityPriceRule(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, capacityPriceRules) &&
    confirm(getText(52, 'Er du sikker på at du vil slette prisregel: $1?',
      [capacityPriceRules[index][c.pru.NAME]])))
  {
    o = new Array(4);
    p = 0;

    o[p++] = '<form id="deletePriceRuleForm" action="/subscription/html/admin_price_rules.php" method="post"><input type="hidden" name="action" value="delete_price_rule" />';
    o[p++] = getPageStateFormElements();
    o[p++] = Utility.getHidden('id', capacityPriceRules[index][c.pru.ID]);
    o[p++] = '</form>';
    editPriceRuleDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deletePriceRuleForm'));
  }
}

// *************************************************************************************************
// Special offer price rules tab functions.
// *************************************************************************************************
// Display the spinner. Once visible, display special offer price rules.
function displaySpecialOfferPriceRules()
{
  Utility.displaySpinnerThen(doDisplaySpecialOfferPriceRules);
}

// *************************************************************************************************
// Display a table of special offer price rules in the appropriate tab. Each price rule can be
// expanded to display the price modifiers.
function doDisplaySpecialOfferPriceRules()
{
  var o, p, i, style;

  if (specialOfferPriceRules.length <= 0)
  {
    specialOfferPriceRulesBox.innerHTML = '<div class="form-element">' +
      getText(2, 'Det er ikke opprettet noen prisregler enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  o = new Array((specialOfferPriceRules.length * 34) + 10);
  p = 0;
  
  // Headline.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr><th>&nbsp;</th>';
  o[p++] = specialOfferRuleSorting.getTableHeader(0, getText(3, 'Navn'));
  o[p++] = specialOfferRuleSorting.getTableHeader(1, getText(51, 'Status'));
  o[p++] = specialOfferRuleSorting.getTableHeader(2, getText(4, 'Fra dato'));
  o[p++] = specialOfferRuleSorting.getTableHeader(3, getText(5, 'Til dato'));
  o[p++] = specialOfferRuleSorting.getTableHeader(4, getText(6, 'Lager'));
  o[p++] = specialOfferRuleSorting.getTableHeader(5, getText(7, 'Bodtyper'));
  o[p++] = specialOfferRuleSorting.getTableHeader(6, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < specialOfferPriceRules.length; i++)
  {
    if (specialOfferPriceRules[i][c.pru.OPEN])
      style = ' class="price-rule-open"';
    else
      style = '';
    // Open / close button.
    o[p++] = '<tr><td';
    o[p++] = style;
    o[p++] = '><button type="button" class="icon-button" onclick="toggleSpecialOfferPriceRule(';
    o[p++] = String(i);
    o[p++] = ');">';
    if (specialOfferPriceRules[i][c.pru.OPEN])
      o[p++] = '<i class="fa-solid fa-minus"></i>';
    else
      o[p++] = '<i class="fa-solid fa-plus"></i>';
    // Name.
    o[p++] = '</button></td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = specialOfferPriceRules[i][c.pru.NAME];
    // Status.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = Utility.getStatusLabel(STATUS_TEXTS, st.pru.COLOURS,
      specialOfferPriceRules[i][c.pru.STATUS]);
    // Start date.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    o[p++] = specialOfferPriceRules[i][c.pru.START_DATE];
    // End date.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    if (specialOfferPriceRules[i][c.pru.END_DATE] === null)
      o[p++] = '&nbsp;';
    else
      o[p++] = specialOfferPriceRules[i][c.pru.END_DATE];
    // For locations.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    if (specialOfferPriceRules[i][c.pru.FOR_LOCATIONS] === null)
      o[p++] = getText(10, 'Alle lager');
    else
      if (specialOfferPriceRules[i][c.pru.FOR_LOCATIONS].length === 0)
        o[p++] = getText(11, 'Ingen lager');
      else
        o[p++] = getText(12, '$1 av $2 lager',
          [String(specialOfferPriceRules[i][c.pru.FOR_LOCATIONS].length), String(locations.length)]);
    // For product types.
    o[p++] = '</td><td';
    o[p++] = style;
    o[p++] = '>';
    if (specialOfferPriceRules[i][c.pru.FOR_PRODUCT_TYPES] === null)
      o[p++] = getText(13, 'Alle bodtyper');
    else
      if (specialOfferPriceRules[i][c.pru.FOR_PRODUCT_TYPES].length === 0)
        o[p++] = getText(14, 'Ingen bodtyper');
      else
        o[p++] = getText(15, '$1 av $2 bodtyper',
          [String(specialOfferPriceRules[i][c.pru.FOR_PRODUCT_TYPES].length), String(productTypes.length)]);
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = specialOfferMenu.getMenuButton(i);
    o[p++] = '</td></tr>';

    // Write table of price lines, if the user has opened the box.
    if (specialOfferPriceRules[i][c.pru.OPEN])
      o[p++] = getSpecialOfferPriceMods(i);
  }
  o[p++] = '</tbody></table>';

  specialOfferPriceRulesBox.innerHTML = o.join('');
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML to display a table of special offer price modifiers. The items are only displayed,
// and cannot be edited.
function getSpecialOfferPriceMods(index)
{
  var o, p, i, priceMods;

  priceMods = specialOfferPriceRules[index][c.pru.PRICE_MODS];
  o = new Array((priceMods.length * 7) + 6);
  p = 0;

  // Headline.
  o[p++] = '<tr class="price-mods"><td colspan="9" class="price-mods"><div class="price-mods"><table cellspacing="0" cellpadding="0"><thead><tr><th>';
  o[p++] = getText(17, 'Prisendring');
  o[p++] = '</th><th>';
  o[p++] = getText(18, 'Varighet');
  o[p++] = '</th></tr></thead><tbody>';
  // Price modifiers with their corresponding duration.
  for (i = 0; i < priceMods.length; i++)
  {
    o[p++] = '<tr><td>';
    o[p++] = String(priceMods[i][c.pru.PRICE_MOD]);
    o[p++] = '%</td><td>';
    if (priceMods[i][c.pru.DURATION] <= 0)
      o[p++] = getText(19, 'Ubegrenset');
    else
    {
      o[p++] = String(priceMods[i][c.pru.DURATION]);
      o[p++] = ' ';
      o[p++] = getText(36, 'm&aring;ned(er)');
    }
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table></div></td></tr>';
  return o.join('');
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getSpecialOfferPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, specialOfferPriceRules))
    return '';
  o = new Array(2);
  p = 0;

  // Edit price rule button.
  o[p++] = sender.getMenuItem(getText(8, 'Rediger prisregel'), 'fa-pen-to-square', true,
    'editPriceRule(RULE_TYPE_SPECIAL_OFFER, ' + String(index) + ');');
  // Delete price rule button.
  o[p++] = sender.getMenuItem(getText(9, 'Slett prisregel'), 'fa-trash', true,
    'deleteSpecialOfferPriceRule(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************
// Open or close the special offer price rule with the given index.
function toggleSpecialOfferPriceRule(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, specialOfferPriceRules))
  {
    specialOfferPriceRules[index][c.pru.OPEN] = !specialOfferPriceRules[index][c.pru.OPEN];
    displaySpecialOfferPriceRules();
  }
}

// *************************************************************************************************
// Delete the special offer price rule with the given index.
function deleteSpecialOfferPriceRule(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, specialOfferPriceRules) &&
    confirm(getText(52, 'Er du sikker på at du vil slette prisregel: $1?',
      [specialOfferPriceRules[index][c.pru.NAME]])))
  {
    o = new Array(4);
    p = 0;

    o[p++] = '<form id="deletePriceRuleForm" action="/subscription/html/admin_price_rules.php" method="post"><input type="hidden" name="action" value="delete_price_rule" />';
    o[p++] = getPageStateFormElements();
    o[p++] = Utility.getHidden('id', specialOfferPriceRules[index][c.pru.ID]);
    o[p++] = '</form>';
    editPriceRuleDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deletePriceRuleForm'));
  }
}

// *************************************************************************************************
// Edit price rule functions.
// *************************************************************************************************
// Find the price rule that we are editing or creating. For the ruleType, use the RULE_TYPE_
// constants. Pass index -1 to create a new price rule of that type. Return true if the parameters
// were valid, and the necessary variables have been filled in. If the method returns false, the
// price rule can not be edited.
function findEditedPriceRule(ruleType, index)
{
  var dataTable;

  // Validate and store the rule type.
  ruleType = parseInt(ruleType, 10);
  if (ruleType === RULE_TYPE_CAPACITY)
    dataTable = capacityPriceRules;
  else
  {
    if (ruleType === RULE_TYPE_SPECIAL_OFFER)
      dataTable = specialOfferPriceRules;
    else
      return false;
  }
  editedRuleType = ruleType;

  // If we are creating a new price rule, the editedPriceRule is null. Otherwise, find it in the
  // appropriate table.
  index = parseInt(index, 10);
  isNew = index === -1;
  if (isNew)
    editedPriceRule = null;
  else
  {
    if (!Utility.isValidIndex(index, dataTable))
      return false;
    editedPriceRule = dataTable[index];
  }
  return true;
}

// *************************************************************************************************
// Fill in a dialogue box that allows the user to edit the price rule with the given index in the
// appropriate data table, as determined by the ruleType (use the RULE_TYPE_ constants). Pass index
// -1 in order to create a new price rule. Note that the ruleType must always be present.
function editPriceRule(ruleType, index)
{
  var o, p;
  
  // Find the price rule that we are editing or creating.
  if (!findEditedPriceRule(ruleType, index))
    return;

  // Generate dialogue box contents.
  o = new Array(3);
  p = 0;

  o[p++] = getEditPriceRuleHeader();
  o[p++] = getEditPriceRuleContent()
  o[p++] = getEditPriceRuleFooter();

  editPriceRuleDialogue.innerHTML = o.join('');

  // Create calendars.
  startDateCalendar = new Calendar(12, 'startDateCalendarBox');
  startDateCalendar.onSelectDate = selectStartDate;
  endDateCalendar = new Calendar(24, 'endDateCalendarBox');
  endDateCalendar.onSelectDate = selectEndDate;

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editPriceRuleForm', 'nameEdit', 'startDateEdit', 'editStartDateButton',
    'closeStartDateButton', 'startDateCalendarBox', 'endDateEdit', 'editEndDateButton',
    'closeEndDateButton', 'endDateCalendarBox', 'productTypesBox', 'locationsBox',
    'priceModEditorBox', 'errorMessageBox', 'submitButton']);

  // Display the dialogue box.
  Utility.display(overlay);
  Utility.display(editPriceRuleDialogue);
  enableSubmitButton();
}

// *************************************************************************************************
// Return HTML code for the header of the dialogue box to edit price rules.
function getEditPriceRuleHeader()
{
  var o, p;

  o = new Array(3);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
  {
    if (editedRuleType === RULE_TYPE_CAPACITY)
      o[p++] = getText(21, 'Opprett prisregel for kapasitet');
    else
      o[p++] = getText(22, 'Opprett prisregel for kampanjer og tilbud');
  }
  else
  {
    if (editedRuleType === RULE_TYPE_CAPACITY)
      o[p++] = getText(23, 'Rediger prisregel for kapasitet');
    else
      o[p++] = getText(24, 'Rediger prisregel for kampanjer og tilbud');
  }
  o[p++] = '</h1></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the contents section of the dialogue box to edit price rules.
function getEditPriceRuleContent()
{
  var o, p;

  o = new Array(25);
  p = 0;

  // Content.
  o[p++] = '<div class="dialogue-content"><form id="editPriceRuleForm" action="/subscription/html/admin_price_rules.php" method="post">';
  o[p++] = getPageStateFormElements();
  o[p++] = Utility.getHidden('type', editedRuleType);
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_price_rule" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_price_rule" />';
    o[p++] = Utility.getHidden('id', editedPriceRule[c.pru.ID]);
  }

  // Name.
  o[p++] = getEditPriceRuleName();

  // Start and end date edit boxes and calendars.
  o[p++] = getEditPriceRuleStartDate();
  o[p++] = getEditPriceRuleEndDate();

  // Columns for choosing product types and locations.
  o[p++] = '<div class="form-element">';
  o[p++] = getText(25, 'Prisregelen gjelder:');
  o[p++] = '</div><div class="column-container">';
  o[p++] = getEditPriceRuleProductTypeColumn();
  o[p++] = getEditPriceRuleLocationColumn();
  o[p++] = '</div>';

  // Price mods. If the price rule that is being edited contains a list of price mods, copy the
  // list, so we don't make changes to the original until the user clicks Save.
  if (isNew)
    editedPriceMods = [];
  else
    editedPriceMods = JSON.parse(JSON.stringify(editedPriceRule[c.pru.PRICE_MODS]));
  o[p++] = '<div class="price-mod-headline"><div>';
  if (editedRuleType === RULE_TYPE_CAPACITY)
    o[p++] = getText(30, 'Opp | Ned | Fra kapasitet | Til kapasitet | Prisendring | Slett');
  else
    o[p++] = getText(31, 'Opp | Ned | Prisendring | Varighet | Slett');
  o[p++] = '</div></div><div id="priceModEditorBox" class="price-mod-editor-box">';
  o[p++] = Utility.getHidden('price_mod_count', editedPriceMods.length);
  for (i = 0; i < editedPriceMods.length; i++)
    o[p++] = getPriceModEditor(i);
  o[p++] = getPriceModToolbar();
  o[p++] = '</div>';
  if (editedRuleType === RULE_TYPE_SPECIAL_OFFER)
  {
    o[p++] = '<div class="form-element help-text">';
    o[p++] = getText(42, 'Negativt tall: Rabatt. Positivt tall: Prisøkning. Sett varigheten til 0 for &aring; gi permanent rabatt.');
    o[p++] = '</div>';
  }

  // End of content.
  o[p++] = '</form></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the name edit box in the dialogue box to edit price rules.
function getEditPriceRuleName()
{
  var o, p;

  o = new Array(8);
  p = 0;

  o[p++] = '<div class="form-element"><label for="nameEdit" class="standard-label">';
  o[p++] = getText(32, 'Navn:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><input type="text" id="nameEdit" name="name" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();"';
  if (!isNew)
  {
    o[p++] = ' value="';
    o[p++] = editedPriceRule[c.pru.NAME];
    o[p++] = '"';
  }
  o[p++] = ' /></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the start date edit box, and its corresponding calendar, in the dialogue box
// to edit price rules.
function getEditPriceRuleStartDate()
{
  var o, p, today;

  today = Utility.getCurrentIsoDate();
  o = new Array(9);
  p = 0;

  o[p++] = '<div class="form-element"><label for="startDateEdit" class="standard-label">';
  o[p++] = getText(33, 'Fra dato:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><input type="text" id="startDateEdit" name="start_date" readonly="readonly" class="long-text" ';
  if (!isNew)
  {
    o[p++] = 'value="';
    o[p++] = editedPriceRule[c.pru.START_DATE];
    o[p++] = '" ';
  }
  o[p++] = '/>';
  // Display calendar to edit the start date if we are creating a new price rule, or if an existing
  // price rule has not yet come into effect.
  if (isNew || (editedPriceRule[c.pru.START_DATE] >= today))
    o[p++] = '<button type="button" id="editStartDateButton" class="icon-button" onclick="editStartDate();"><i class="fa-solid fa-calendar-days"></i></button><button type="button" id="closeStartDateButton" class="icon-button" style="display: none;" onclick="closeStartDate();"><i class="fa-solid fa-xmark"></i></button></div><div id="startDateCalendarBox" class="calendar-box" style="display: none;">&nbsp;</div>';
  else
    o[p++] = '</div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the end date edit box, and its corresponding calendar, in the dialogue box
// to edit price rules.
function getEditPriceRuleEndDate()
{
  var o, p, today;

  today = Utility.getCurrentIsoDate();
  o = new Array(9);
  p = 0;

  o[p++] = '<div class="form-element"><label for="endDateEdit" class="standard-label">';
  o[p++] = getText(34, 'Til dato:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><input type="text" id="endDateEdit" name="end_date" readonly="readonly" class="long-text" ';
  if (!isNew)
  {
    o[p++] = 'value="';
    o[p++] = editedPriceRule[c.pru.END_DATE];
    o[p++] = '" ';
  }
  o[p++] = '/>';
  // Display calendar to edit the end date if we are creating a new price rule, or if an existing
  // price rule has not yet ended.
  if (isNew || (editedPriceRule[c.pru.END_DATE] >= today))
  {
    o[p++] = '<button type="button" id="editEndDateButton" class="icon-button" onclick="editEndDate();"><i class="fa-solid fa-calendar-days"></i></button><button type="button" id="closeEndDateButton" class="icon-button" style="display: none;" onclick="closeEndDate();"><i class="fa-solid fa-xmark"></i></button></div><div id="endDateCalendarBox" class="calendar-box" style="display: none;">&nbsp;</div>';
  }
  else
    o[p++] = '</div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the column that allows the user to select product types, in the dialogue box
// to edit price rules.
function getEditPriceRuleProductTypeColumn()
{
  var o, p, forProductTypes, forAllProductTypes;

  o = new Array((productTypes.length * 13) + 18);
  p = 0;

  // All product types radio button.
  o[p++] = '<div class="column for-product-types-column"><div class="form-element">';
  o[p++] = Utility.getHidden('product_type_count', productTypes.length);
  o[p++] = '<input type="radio" id="allProductTypesRadio" name="for_all_product_types" value="1" onchange="toggleProductTypesBox(this);"';
  if (isNew)
    forProductTypes = null;
  else
    forProductTypes = editedPriceRule[c.pru.FOR_PRODUCT_TYPES];
  forAllProductTypes = forProductTypes === null;
  if (forAllProductTypes)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="allProductTypesRadio">';
  o[p++] = getText(26, 'For alle bodtyper');
  // Some product types radio button.
  o[p++] = '</label></div><div class="form-element"><input type="radio" id="someProductTypesRadio" name="for_all_product_types" value="0" onchange="toggleProductTypesBox(this);"';
  if (!forAllProductTypes)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="someProductTypesRadio">';
  o[p++] = getText(27, 'For noen bodtyper');
  // Select product types box.
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
  o[p++] = '</ul><button type="button" onclick="setAllProductTypesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(53, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllProductTypesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(54, 'Ingen');
  o[p++] = '</button></div></div></div>'; // End of: indented-box, form-element, column

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the column that allows the user to select locations, in the dialogue box to
// edit price rules.
function getEditPriceRuleLocationColumn()
{
  var o, p, forLocations, forAllLocations;

  o = new Array((locations.length * 13) + 18);
  p = 0;

  // All locations radio button.
  o[p++] = '<div class="column for-locations-column"><div class="form-element">';
  o[p++] = Utility.getHidden('location_count', locations.length);
  o[p++] = '<input type="radio" id="allLocationsRadio" name="for_all_locations" value="1" onchange="toggleLocationsBox(this);"';
  if (isNew)
    forLocations = null;
  else
    forLocations = editedPriceRule[c.pru.FOR_LOCATIONS];
  forAllLocations = forLocations === null;
  if (forAllLocations)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="allLocationsRadio">';
  o[p++] = getText(28, 'Ved alle lager');
  // Some locations radio button.
  o[p++] = '</label></div><div class="form-element"><input type="radio" id="someLocationsRadio" name="for_all_locations" value="0" onchange="toggleLocationsBox(this);"';
  if (!forAllLocations)
    o[p++] = ' checked="checked"';
  o[p++] = ' /> <label for="someLocationsRadio">';
  o[p++] = getText(29, 'Ved noen lager');
  // Select locations box.
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
  o[p++] = '</ul><button type="button" onclick="setAllLocationsTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(53, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllLocationsTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(54, 'Ingen');
  o[p++] = '</button></div></div></div>'; // End of: indented-box, form-element, column

  return o.join('');
}

// *************************************************************************************************
// Return HTML for a single line of user interface elements that can be used to edit a price mod. A
// price mod is a percentage price modifier, along with the conditions that state when it will
// apply.
function getPriceModEditor(index)
{
  var o, p;

  o = new Array(17);
  p = 0;

  o[p++] = '<div class="price-mod-editor-frame"><div class="price-mod-editor">';
  o[p++] = String(index + 1);

  // Up button.
  o[p++] = ' <button type="button" class="icon-button spaced" ';
  if (index <= 0)
    o[p++] = 'disabled="disabled"';
  else
  {
    o[p++] = 'onclick="movePriceModUp(';
    o[p++] = String(index);
    o[p++] = ');"';
  }
  o[p++] = '><i class="fa-solid fa-caret-up"></i></button>';

  // Down button.
  o[p++] = '<button type="button" class="icon-button" ';
  if (index >= (editedPriceMods.length - 1))
    o[p++] = 'disabled="disabled"';
  else
  {
    o[p++] = 'onclick="movePriceModDown(';
    o[p++] = String(index);
    o[p++] = ');"';
  }
  o[p++] = '><i class="fa-solid fa-caret-down"></i></button>';

  // Edit boxes.
  if (editedRuleType === RULE_TYPE_CAPACITY)
    o[p++] = getCapacityPriceModEditBoxes(index);
  else
    o[p++] = getSpecialOfferPriceModEditBoxes(index);

  // Delete button.
  o[p++] = '<button type="button" class="icon-button spaced" onclick="deletePriceMod(';
  o[p++] = String(index);
  o[p++] = ');"><i class="fa-solid fa-trash"></i></button>';

  o[p++] = '</div></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML for the edit boxes that allow the user to edit a capacity price modifier. This forms
// part of the price mod editor.
function getCapacityPriceModEditBoxes(index)
{
  var o, p;

  o = new Array(21);
  p = 0;

  // Minimum capacity.
  o[p++] = '<input type="number" id="priceRule';
  o[p++] = String(index);
  o[p++] = '_minCapacity" name="min_capacity_';
  o[p++] = String(index);
  o[p++] = '" class="numeric spaced" min="0" max="100" value="';
  o[p++] = String(editedPriceMods[index][c.pru.MIN_CAPACITY]);
  o[p++] = '" onchange="storeCapacityPriceModChanges();" /> %';

  // Maximum capacity.
  o[p++] = '<input type="number" id="priceRule';
  o[p++] = String(index);
  o[p++] = '_maxCapacity" name="max_capacity_';
  o[p++] = String(index);
  o[p++] = '" class="numeric spaced" min="0" max="100" value="';
  o[p++] = String(editedPriceMods[index][c.pru.MAX_CAPACITY]);
  o[p++] = '" onchange="storeCapacityPriceModChanges();" /> %';

  // Price modifier.
  o[p++] = '<input type="number" id="priceRule';
  o[p++] = String(index);
  o[p++] = '_modifier" name="price_mod_';
  o[p++] = String(index);
  o[p++] = '" class="numeric spaced" min="-1000" max="1000" value="';
  o[p++] = String(editedPriceMods[index][c.pru.PRICE_MOD]);
  o[p++] = '" onchange="storeCapacityPriceModChanges();"/> %';

  return o.join('');
}

// *************************************************************************************************
// Return HTML for the edit boxes that allow the user to edit a special offer price modifier. This
// forms part of the price mod editor.
function getSpecialOfferPriceModEditBoxes(index)
{
  var o, p;

  o = new Array(16);
  p = 0;

  // Price modifier.
  o[p++] = '<input type="number" id="priceRule';
  o[p++] = String(index);
  o[p++] = '_modifier" name="price_mod_';
  o[p++] = String(index);
  o[p++] = '" class="numeric spaced" min="-1000" max="1000" value="';
  o[p++] = String(editedPriceMods[index][c.pru.PRICE_MOD]);
  o[p++] = '" onchange="storeSpecialOfferPriceModChanges();" /> % ';
  o[p++] = getText(35, 'i');

  // Duration. A value of 0 is indefinite.
  o[p++] = ' <input type="number" id="priceRule';
  o[p++] = String(index);
  o[p++] = '_duration" name="duration_';
  o[p++] = String(index);
  o[p++] = '" class="numeric spaced" min="0" max="24" value="';
  o[p++] = String(editedPriceMods[index][c.pru.DURATION]);
  o[p++] = '" onchange="storeSpecialOfferPriceModChanges();" /> ';
  o[p++] = getText(36, 'm&aring;ned(er)');

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the buttons along the bottom of the edit price mod box in the dialogue box
// to edit price rules. These may include a sort button for capacity price mods, and a button to add
// new price modifiers.
function getPriceModToolbar()
{
  var o, p;

  o = new Array(7);
  p = 0;

  o[p++] = '<div class="price-mod-editor-frame">';
  if ((editedRuleType === RULE_TYPE_CAPACITY) && (editedPriceMods.length >= 2))
  {
    o[p++] = '<button type="button" onclick="sortEditedCapacityPriceMods();"><i class="fa-solid fa-arrow-down-1-9"></i> ';
    o[p++] = getText(43, 'Sorter');
    o[p++] = '</button>';
  }
  o[p++] = '<button type="button" class="wide-button" onclick="addPriceMod();"><i class="fa-solid fa-plus"></i> ';
  o[p++] = getText(37, 'Legg til prisendring');
  o[p++] = '</button></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the footer of the dialogue box to edit price rules.
function getEditPriceRuleFooter()
{
  var o, p;

  o = new Array(5);
  p = 0;

  o[p++] = '<div class="dialogue-footer"><div id="errorMessageBox" class="help-text">&nbsp;</div><button type="button" id="submitButton" onclick="submitPriceRule();"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(38, 'Opprett');
  else
    o[p++] = getText(39, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closePriceRuleDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(40, 'Avbryt');
  o[p++] = '</button></div>';

  return o.join('');
}

// *************************************************************************************************
// Open a calendar to select the start date of the price rule being edited.
function editStartDate()
{
  // Close the end date editor. You can only edit one date at a time, to prevent overlapping date
  // ranges.
  closeEndDate();
  startDateCalendar.selectedDate = startDateEdit.value;
  Utility.hide(editStartDateButton);
  Utility.display(closeStartDateButton);
  Utility.display(startDateCalendarBox);
  if (endDateEdit.value === '')
    startDateCalendar.lastSelectableDate = null;
  else
    startDateCalendar.lastSelectableDate = endDateEdit.value;
  startDateCalendar.display();
}

// *************************************************************************************************
// Store the given start date for the price rule being edited.
function selectStartDate(sender, selectedDate)
{
  startDateEdit.value = selectedDate;
  closeStartDate();
  enableSubmitButton();
}

// *************************************************************************************************
// Close the calendar that allows the user to select the start date of the price rule being edited.
// The date will not be modified.
function closeStartDate()
{
  Utility.display(editStartDateButton);
  Utility.hide(closeStartDateButton);
  Utility.hide(startDateCalendarBox);
}

// *************************************************************************************************
// Open a calendar to select the end date of the price rule being edited.
function editEndDate()
{
  // Close the start date editor. You can only edit one date at a time, to prevent overlapping date
  // ranges.
  closeStartDate();
  endDateCalendar.selectedDate = endDateEdit.value;
  Utility.hide(editEndDateButton);
  Utility.display(closeEndDateButton);
  Utility.display(endDateCalendarBox);
  if (startDateEdit.value === '')
    endDateCalendar.firstSelectableDate = null;
  else
    endDateCalendar.firstSelectableDate = startDateEdit.value;
  endDateCalendar.display();
}

// *************************************************************************************************
// Store the given end date for the price rule being edited.
function selectEndDate(sender, selectedDate)
{
  endDateEdit.value = selectedDate;
  closeEndDate();
  enableSubmitButton();
}


// *************************************************************************************************
// Close the calendar that allows the user to select the end date of the price rule being edited.
// The date will not be modified.
function closeEndDate()
{
  Utility.display(editEndDateButton);
  Utility.hide(closeEndDateButton);
  Utility.hide(endDateCalendarBox);
}

// *************************************************************************************************
// Display or hide the box that allows the user to select individual product types, depending on the
// value of the given radio button.
function toggleProductTypesBox(radioButton)
{
  if (radioButton.value === '1')
    Utility.hide(productTypesBox);
  else
    Utility.display(productTypesBox);
  enableSubmitButton();
}

// *************************************************************************************************
// Check or uncheck all the product type checkboxes in the "for product types" filter, depending on
// checked, which should be a boolean.
function setAllProductTypesTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < productTypes.length; i++)
  {
    checkbox = document.getElementById('productType' + String(i));
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************
// Display or hide the box that allows the user to select individual locations, depending on the
// value of the given radio button.
function toggleLocationsBox(radioButton)
{
  if (radioButton.value === '1')
    Utility.hide(locationsBox);
  else
    Utility.display(locationsBox);
  enableSubmitButton();
}

// *************************************************************************************************
// Check or uncheck all the location checkboxes in the "for location" filter, depending on checked,
// which should be a boolean.
function setAllLocationsTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < locations.length; i++)
  {
    checkbox = document.getElementById('location' + String(i));
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************
// Move the price modifier with the given index in the editedPriceMods array up one place. In
// effect, this means it switches places with the item above it.
function movePriceModUp(index)
{
  index = parseInt(index, 10)
  if (Utility.isValidIndex(index, editedPriceMods) && (index > 0))
  {
    Utility.switchArrayEntries(editedPriceMods, index, index - 1);
    updatePriceModTable();
  }
}

// *************************************************************************************************
// Move the price modifier with the given index in the editedPriceMods array down one place. In
// effect, this means it switches places with the item below it.
function movePriceModDown(index)
{
  index = parseInt(index, 10)
  if (Utility.isValidIndex(index, editedPriceMods) && (index < (editedPriceMods.length - 1)))
  {
    Utility.switchArrayEntries(editedPriceMods, index, index + 1);
    updatePriceModTable();
  }
}

// *************************************************************************************************
// Delete the price modifier with the given index in the editedPriceMods array.
function deletePriceMod(index)
{
  if (confirm(getText(41, 'Er du sikker på at du vil slette denne prisendringen?')))
  {
    editedPriceMods.splice(index, 1);
    updatePriceModTable();
    enableSubmitButton();
  }
}

// *************************************************************************************************
// Append a new price modifier to the end of the editedPriceMods array.
function addPriceMod()
{
  if (editedPriceMods.length < 100)
  {
    if (editedRuleType === RULE_TYPE_CAPACITY)
      editedPriceMods.push([0, 0, 1]); // Modifier: 0%. From capacity: 0%. To capacity: 1%.
    else
      editedPriceMods.push([0, 1]); // Modifier: 0%. Duration: 1 month.
    updatePriceModTable();
    enableSubmitButton();
  }
}

// *************************************************************************************************
// Sort the capacity price modifiers in the editedPriceMods array by the minimum capacity at which
// they apply. This only makes sense if the array contains capacity price modifiers.
function sortEditedCapacityPriceMods()
{
  editedPriceMods.sort(compareCapacityPriceMods);
  updatePriceModTable();
}

// *************************************************************************************************
// a and b are capacity price mods. Return a negative number if a should be sorted before b. Return
// 0 if a and b start at the same time (which is not a legal configuration, but possible while the
// price mods are being edited). Return a positive number if b should be sorted before a.
function compareCapacityPriceMods(a, b)
{
  return a[c.pru.MIN_CAPACITY] - b[c.pru.MIN_CAPACITY];
}

// *************************************************************************************************
// Read information from the user interface, and store it in the editedPriceMods array. This assumes
// that capacity price modifiers are being edited.
function storeCapacityPriceModChanges()
{
  var i, activeElement;

  // Read and validate information from the user interface. As a result of validation, the
  // editedPriceMods table may end up containing different data than is displayed in the user
  // interface.
  for (i = 0; i < editedPriceMods.length; i++)
  {
    setEditedPriceModElement(i, c.pru.MIN_CAPACITY, 'minCapacity', 0, 99);
    setEditedPriceModElement(i, c.pru.MAX_CAPACITY, 'maxCapacity', 1, 100);
    setEditedPriceModElement(i, c.pru.PRICE_MOD, 'modifier', -1000, 1000);
  }

  // Store ID of the element that has input focus, regenerate the contents of the price mod box,
  // and focus the element that had focus before.
  updatePriceModTable();
    // *** // Update the contents of the existing edit boxes, rather than rewriting everything. Then the focus issue will go away.
/*
  activeElement = document.activeElement;
  updatePriceModTable();
  if (activeElement && activeElement.focus)
    activeElement.focus();
*/

  enableSubmitButton();
}

// *************************************************************************************************
// Read information from the user interface, and store it in the editedPriceMods array. This assumes
// that special offer price modifiers are being edited.
function storeSpecialOfferPriceModChanges()
{
  var i, activeElement;

  for (i = 0; i < editedPriceMods.length; i++)
  {
    setEditedPriceModElement(i, c.pru.PRICE_MOD, 'modifier', -1000, 1000);
    // A duration of 0 is indefinite.
    setEditedPriceModElement(i, c.pru.DURATION, 'duration', 0, 24);
  }

  // Store ID of the element that has input focus, regenerate the contents of the price mod box,
  // and focus the element that had focus before.
  updatePriceModTable();
    // *** // Update the contents of the existing edit boxes, rather than rewriting everything. Then the focus issue will go away.
/*
  activeElement = document.activeElement;
  updatePriceModTable();
  if (activeElement && activeElement.focus)
    activeElement.focus();
*/

  enableSubmitButton();
}

// *************************************************************************************************
// Update the one value in the element with the given index in the editedPriceMods table by reading
// the value from the user interface. The value is located in the given column. It will be read from
// the edit box with the given label.
function setEditedPriceModElement(index, column, label, minValue, maxValue)
{
  var editBox, newValue;

  editBox = document.getElementById('priceRule' + String(index) + '_' + label);
  newValue = Utility.getValidInteger(editBox.value, editedPriceMods[index][column]);
  if ((newValue >= minValue) && (newValue <= maxValue))
    editedPriceMods[index][column] = newValue;
}

// *************************************************************************************************
// Replace the current contents of the priceModEditorBox with editors to edit the contents of the
// editedPriceMods array.
function updatePriceModTable()
{
  var o, p, i;

  o = new Array(editedPriceMods.length + 2);
  p = 0;

  o[p++] = Utility.getHidden('price_mod_count', editedPriceMods.length);
  for (i = 0; i < editedPriceMods.length; i++)
    o[p++] = getPriceModEditor(i);
  o[p++] = getPriceModToolbar();

  priceModEditorBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Enable or disable the submit button, depending on whether the contents of the dialogue box are
// valid. If they are not, display a help message to say why.
function enableSubmitButton()
{
  var errorMessage; 

  errorMessage = getErrorMessage();
  submitButton.disabled = errorMessage !== '';
  errorMessageBox.innerHTML = errorMessage;
}

// *************************************************************************************************
// Return a string that says why the submit button should be disabled, or an empty string if
// everything is fine and the button should be enabled.
function getErrorMessage()
{
  var startDate, endDate;

  // Check the name.
  if (nameEdit.value === '')
    return getText(44, 'Navn mangler.');

  // Check the start date.
  startDate = startDateEdit.value;
  if (startDate === '')
    return getText(45, 'Startdato mangler.');

  // Check the end date.
  endDate = endDateEdit.value;
  if (endDate === '')
    return getText(46, 'Sluttdato mangler.');

  // The product type and location selectors are always valid. Check the price modifiers.
  if (editedPriceMods.length <= 0)
    return getText(47, 'Prisendringer mangler.');
  if (editedRuleType === RULE_TYPE_CAPACITY)
    return getCapacityPriceModErrorMessage();
  return getSpecialOfferPriceModErrorMessage();
}

// *************************************************************************************************
// Return a string that says why the capacity price mods are not valid, or an empty string if they
// are.
function getCapacityPriceModErrorMessage()
{
  var i, j, stopAt;

  // It is not a problem if the capacity price mods are not sorted, as the server will sort them
  // anyway. The price mods will appear in the correct order next time the price rule is edited.

  // The user interface guarantees that all elements in the editedPriceMods table contain valid
  // numbers at all times.

  // Ensure that all max capacity values are greater than the minimum capacity. The user interface
  // does not prevent this, as it would make editing difficult.
  for (i = 0; i < editedPriceMods.length; i++)
  {
    if (editedPriceMods[i][c.pru.MAX_CAPACITY] <= editedPriceMods[i][c.pru.MIN_CAPACITY])
      return getText(48, 'Prisendring $1: &quot;Til kapasitet&quot; m&aring; være st&oslash;rre enn &quot;fra kapasitet&quot;.',
        [String(i + 1)]);
  }

  // Ensure that none of the capacity ranges overlap. For each price modifier, examine the ranges of
  // all subsequent price modifiers, to ensure any overlap is detected.
  if (editedPriceMods.length >= 2)
  {
    stopAt = editedPriceMods.length - 2;
    for (i = 0; i <= stopAt; i++)
    {
      for (j = i + 1; j < editedPriceMods.length; j++)
      {
        if (capacityRangesOverlap(i, j))
          return getText(49, 'Kapasitetsomr&aring;det for prisendring $1 og $2 er overlappende.',
            [i + 1, j + 1]);
      }
    }
  }

  // It is not a problem if the price modifiers do not cover the entire capacity range of 0 to 100%.
  // If a particular capacity is not covered by a price modifier, the default price will be used.
 
  // Everything appears fine.
  return '';
}

// *************************************************************************************************
// Return true if the two capacity price mods with the given indexes i and j in the editedPriceMods
// table have overlapping capacity ranges. If one range starts where the other ends, that is not
// considered an overlap.
function capacityRangesOverlap(i, j)
{
  return (editedPriceMods[i][c.pru.MAX_CAPACITY] > editedPriceMods[j][c.pru.MIN_CAPACITY]) &&
    (editedPriceMods[j][c.pru.MAX_CAPACITY] > editedPriceMods[i][c.pru.MIN_CAPACITY]);
}

// *************************************************************************************************
// Return a string that says why the special offer price mods are not valid, or an empty string if
// they are.
function getSpecialOfferPriceModErrorMessage()
{
  var stopAt;

  // The user interface guarantees that all elements in the editedPriceMods table contain valid
  // numbers at all times.

  // Ensure that any price mod with infinite duration is the last one in the list. It makes no sense
  // to have another price mod after that.
  stopAt = editedPriceMods.length - 2;
  for (i = 0; i <= stopAt; i++)
  {
    if (editedPriceMods[i][c.pru.DURATION] === 0)
      return getText(50,
        'Prisendring $1: En permanent rabatt m&aring; v&aelig;re den siste i listen.',
        [String(i + 1)]);
  }

  // Everything appears fine.
  return '';
}

// *************************************************************************************************
// Submit the new or updated price rule to the server, and close the dialogue box.
function submitPriceRule()
{
  // Update the price mods from the editedPriceMods array, to ensure valid data is submitted.
  updatePriceModTable();
  Utility.displaySpinnerThenSubmit(editPriceRuleForm);
}

// *************************************************************************************************
// Close the edit price rule dialogue without submitting any changes to the server.
function closePriceRuleDialogue()
{
  Utility.hide(editPriceRuleDialogue);
  Utility.hide(overlay);
  startDateCalendar = null;
  endDateCalendar = null;
}

// *************************************************************************************************
