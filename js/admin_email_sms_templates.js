// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var templateBox, filterToolbar, overlay, editTemplateDialogue, editMessageTypeFilterDialogue,
  editTriggerTypeFilterDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var editTemplateForm, nameEdit, delayEdit, triggerTypeCombo, triggerDescriptionBox, emailOnlyBox,
  copyToEdit, headerEdit, contentBox, submitButton, freetextEdit;

// The sorting object that controls the sorting of the templates table.
var sorting;

// The popup menu for the templates table.
var menu;

// The number of displayed templates. This depends on the current filter settings.
var displayedCount = 0;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  var i;

  // Ensure templates do not contain encoded line breaks.
  for (i = 0; i < templates.length; i++)
    templates[i][c.tpl.CONTENT] = Utility.decodeLineBreaks(templates[i][c.tpl.CONTENT]);

  // Obtain pointers to user interface elements.
  Utility.readPointers(['templateBox', 'filterToolbar', 'overlay', 'editTemplateDialogue',
    'editMessageTypeFilterDialogue', 'editTriggerTypeFilterDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(templates,
      [
        Sorting.createUiColumn(c.tpl.NAME, Sorting.SORT_AS_STRING,
          function (template)
          {
            return String(template[c.tpl.MESSAGE_TYPE]) + ' ' + template[c.tpl.NAME];
          }),
        Sorting.createUiColumn(c.tpl.HEADER, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.tpl.CONTENT, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.tpl.TRIGGER_TYPE, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.tpl.DELAY, Sorting.SORT_AS_INTEGER),
        Sorting.createUiColumn(c.tpl.ACTIVE, Sorting.SORT_AS_BOOLEAN),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayTemplates
    );
  // Set the initial sorting. If that didn't cause templates to be displayed, do so now.
  if (!sorting.sortOn(initialUiColumn, initialDirection))
    doDisplayTemplates();

  // Display the results of a previous operation, if required.
  if (Utility.isError(resultCode))
    alert(getText(0, 'Det oppstod en feil. Vennligst kontakt kundeservice og oppgi feilkode $1. Tidspunkt: $2.',
      [String(resultCode), TIMESTAMP]));
}

// *************************************************************************************************
// Return hidden form elements that specify the current state of the page, including sorting, search
// and filter settings. These should be included whenever a request is submitted to the current
// page, so that the state is maintained when the page is reloaded.
function getPageStateFormElements()
{
  var o, p;

  o = new Array(4);
  p = 0;

  if (messageTypeFilter !== null)
    o[p++] = Utility.getHidden('message_type_filter', messageTypeFilter.join(','));
  if (triggerTypeFilter !== null)
    o[p++] = Utility.getHidden('trigger_type_filter', triggerTypeFilter.join(','));
  if (freetextFilter !== '')
    o[p++] = Utility.getHidden('freetext_filter', freetextFilter);
  o[p++] = sorting.getPageStateFormElements();
  return o.join('');
}

// *************************************************************************************************
// Display the spinner. Once visible, display templates.
function displayTemplates()
{
  Utility.displaySpinnerThen(doDisplayTemplates);
}

// *************************************************************************************************
// Display the list of templates.
function doDisplayTemplates()
{
  var o, p, i;
  
  if (templates.length <= 0)
  {
    templateBox.innerHTML = '<div class="form-element">' +
      getText(1, 'Det er ikke opprettet noen maler enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((templates.length * 17) + 10);
  p = 0;
  
  // Header.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(2, 'Navn'));
  o[p++] = sorting.getTableHeader(1, getText(4, 'Overskrift'));
  o[p++] = sorting.getTableHeader(2, getText(5, 'Innhold'));
  o[p++] = sorting.getTableHeader(3, getText(6, 'Hendelse'));
  o[p++] = sorting.getTableHeader(4, getText(7, 'Forsinkelse [min]'));
  o[p++] = sorting.getTableHeader(5, getText(8, 'Aktiv?'));
  o[p++] = sorting.getTableHeader(6,'&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < templates.length; i++)
  {
    if (shouldHide(templates[i]))
      continue;
    displayedCount++;

    // Message type icon and name.
    o[p++] = '<tr><td>';
    o[p++] = Utility.getMessageTypeIcon(templates[i][c.tpl.MESSAGE_TYPE]);
    o[p++] = ' ';
    o[p++] = templates[i][c.tpl.NAME];
    // Headline, if any.
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(templates[i][c.tpl.HEADER], 50);
    // Message.
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(templates[i][c.tpl.CONTENT], 50);
    // Trigger type.
    o[p++] = '</td><td>';
    o[p++] = TRIGGER_HEADLINES[templates[i][c.tpl.TRIGGER_TYPE]],
    // Delay.
    o[p++] = '</td><td>';
    o[p++] = String(templates[i][c.tpl.DELAY]);
    // Active flag.
    o[p++] = '</td><td>';
    if (templates[i][c.tpl.ACTIVE])
      o[p++] = '<i class="fa-solid fa-check icon-green"></i>';
    else
      o[p++] = '<i class="fa-solid fa-xmark icon-red"></i>';
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  templateBox.innerHTML = o.join('');
  displayFilterToolbar();
  Utility.hideSpinner();
}

// *************************************************************************************************
// Return HTML for the contents of the popup menu for the item with the given index. This function
// will be called when one of the menu buttons is clicked.
function getPopupMenuContents(sender, index)
{
  var o, p;

  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, templates))
    return '';
  o = new Array(2);
  p = 0;

  // Edit template button.
  o[p++] = sender.getMenuItem(getText(10, 'Rediger mal'), 'fa-pen-to-square', true,
    'displayEditTemplateDialogue(' + String(index) + ');');
  // Delete template button.
  o[p++] = sender.getMenuItem(getText(3, 'Slett mal'), 'fa-trash', true,
    'deleteTemplate(' + String(index) + ');');
  return o.join('');
}

// *************************************************************************************************

function deleteTemplate(index)
{
  var o, p;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, templates) &&
    confirm(getText(25, 'Er du sikker på at du vil slette mal: $1?', [templates[index][c.tpl.NAME]])))
  {
    o = new Array(5);
    p = 0;

    o[p++] = '<form id="deleteTemplateForm" action="/subscription/html/admin_email_sms_templates.php" method="post">';
    o[p++] = getPageStateFormElements();
    o[p++] = '<input type="hidden" name="action" value="delete_template" />';
    o[p++] = Utility.getHidden('id', templates[index][c.tpl.ID]);
    o[p++] = '</form>';
    editTemplateDialogue.innerHTML = o.join('');
    Utility.displaySpinnerThenSubmit(document.getElementById('deleteTemplateForm'));
  }
}

// *************************************************************************************************
// Create and edit template functions.
// *************************************************************************************************
// Display the dialogue box to edit a template. index is the index in the templates table of the
// template to be edited. Pass -1 in order to create a new template.
function displayEditTemplateDialogue(index)
{
  var o, p, i, isNew;
  
  index = parseInt(index, 10);
  isNew = index === -1;
  if (!(isNew || Utility.isValidIndex(index, templates)))
    return;
  o = new Array((TRIGGER_HEADLINES.length * 7) + 61);
  p = 0;

  // Header.
  o[p++] = '<div class="dialogue-header"><h1>';
  if (isNew)
    o[p++] = getText(9, 'Opprett mal');
  else
    o[p++] = getText(10, 'Rediger mal');
  o[p++] = '</h1></div><div class="dialogue-content"><form id="editTemplateForm" action="/subscription/html/admin_email_sms_templates.php" method="post">';
  o[p++] = getPageStateFormElements();
  if (isNew)
    o[p++] = '<input type="hidden" name="action" value="create_template" />';
  else
  {
    o[p++] = '<input type="hidden" name="action" value="update_template" />';
    o[p++] = Utility.getHidden('id', templates[index][c.tpl.ID]);
  }
  // Name.
  o[p++] = Utility.getEditBox('nameEdit', 'name', getText(11, 'Navn:'),
    (isNew ? null : templates[index][c.tpl.NAME]));
  // Active. For a new template, active is checked by default.
  o[p++] = '<div class="form-element"><label class="standard-label">&nbsp;</label><label><input type="checkbox" name="active"';
  if (isNew || templates[index][c.tpl.ACTIVE])
    o[p++] = ' checked="checked"';
  o[p++] = ' /> ';
  o[p++] = getText(17, 'Aktiv');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label></div>';
  // Message type. For a new template, SMS is selected by default.
  o[p++] = '<div class="form-element"><label class="standard-label">';
  o[p++] = getText(21, 'Type:');
  o[p++] = '</label><label><input type="radio" name="message_type" value="';
  o[p++] = String(MESSAGE_TYPE_SMS);
  o[p++] = '"';
  if (isNew || (templates[index][c.tpl.MESSAGE_TYPE] === MESSAGE_TYPE_SMS))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="displayEmailOnlyBox();"> <i class="fa-solid fa-message-sms icon-blue"></i>&nbsp;&nbsp;';
  o[p++] = MESSAGE_TYPE_TEXTS[MESSAGE_TYPE_SMS];
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <label><input type="radio" name="message_type" value="';
  o[p++] = String(MESSAGE_TYPE_EMAIL);
  o[p++] = '"';
  if (!isNew && (templates[index][c.tpl.MESSAGE_TYPE] === MESSAGE_TYPE_EMAIL))
    o[p++] = ' checked="checked"';
  o[p++] = ' onchange="displayEmailOnlyBox();"> <i class="fa-solid fa-envelope icon-purple"></i>&nbsp;&nbsp;';
  o[p++] = MESSAGE_TYPE_TEXTS[MESSAGE_TYPE_EMAIL];
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label></div>'
  // Delay.
  o[p++] = '<div class="form-element"><label for="delayEdit" class="standard-label">';
  o[p++] = getText(18, 'Forsinkelse:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <input type="number" id="delayEdit" name="delay" min="0" value="';
  if (isNew)
    o[p++] = '0';
  else
    o[p++] = String(templates[index][c.tpl.DELAY]);
  o[p++] = '" class="numeric" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" /> ';
  o[p++] = getText(19, 'minutter');
  o[p++] = '</div><div class="form-element"><span class="help-text">';
  o[p++] = getText(20, 'Tiden det tar fra en hendelse inntreffer til meldingen blir sendt.');
  o[p++] = '</span></div>';
  // Trigger. The trigger can only be selected when the template is created.
  o[p++] = '<div class="form-element"><label for="triggerTypeCombo" class="standard-label">';
  o[p++] = getText(22, 'Hendelse:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <select id="triggerTypeCombo" name="trigger_type" ';
  if (!isNew)
    o[p++] = 'disabled="disabled">';
  else
    o[p++] = 'onchange="displayTriggerDescription();">';
  for (i = 0; i < TRIGGER_HEADLINES.length; i++)
  {
    o[p++] = '<option value="';
    o[p++] = String(i);
    o[p++] = '"';
    if (!isNew && (templates[index][c.tpl.TRIGGER_TYPE] === i))
      o[p++] = ' selected="selected"';
    o[p++] = '>';
    o[p++] = TRIGGER_HEADLINES[i];
    o[p++] = '</option>';
  }
  o[p++] = '</select></div><div class="form-element"><span id="triggerDescriptionBox" class="help-text">&nbsp;</span></div>';
  // Copy to.
  o[p++] = '<div id="emailOnlyBox">';
  o[p++] = Utility.getEditBox('copyToEdit', 'copy_to', getText(-1, 'Kopi til:'),
    (isNew ? null : templates[index][c.tpl.COPY_TO]), null, null, null, false);
  // Header.
  o[p++] = Utility.getEditBox('headerEdit', 'header', getText(23, 'Overskrift:'),
    (isNew ? null : templates[index][c.tpl.HEADER]));
  o[p++] = '</div>';
  // Content.
  o[p++] = '<div class="form-element"><label for="contentBox" class="standard-label">';
  o[p++] = getText(24, 'Innhold:');
  o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label> <textarea id="contentBox" name="content" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();">';
  if (!isNew)
    o[p++] = templates[index][c.tpl.CONTENT];
  o[p++] = '</textarea></div>';

  // Footer.
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" id="submitButton" onclick="storeTemplate();"><i class="fa-solid fa-check"></i> ';
  if (isNew)
    o[p++] = getText(14, 'Opprett');
  else
    o[p++] = getText(15, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeTemplateDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(16, 'Avbryt');
  o[p++] = '</button></div>';

  editTemplateDialogue.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['editTemplateForm', 'nameEdit', 'delayEdit', 'triggerTypeCombo',
    'triggerDescriptionBox', 'emailOnlyBox', 'copyToEdit', 'headerEdit', 'contentBox',
    'submitButton']);

  Utility.display(overlay);
  Utility.display(editTemplateDialogue);
  displayEmailOnlyBox();
  displayTriggerDescription();
  enableSubmitButton();
}

// *************************************************************************************************

function storeTemplate()
{
  contentBox.value = Utility.encodeLineBreaks(contentBox.value);
  Utility.displaySpinnerThenSubmit(editTemplateForm);
}

// *************************************************************************************************
// Return the current selection of the message type radio button set. Use the MESSAGE_TYPE_
// constants. Return -1 if the selection is invalid.
function getSelectedMessageType()
{
  var selectedType;

  selectedType = parseInt(Utility.getRadioButtonValue('message_type', MESSAGE_TYPE_SMS), 10);
  if (!isFinite(selectedType) || (selectedType < 0) || (selectedType >= MESSAGE_TYPE_TEXTS.length))
    return -1;
  return selectedType;
}

// *************************************************************************************************
// Display or hide the e-mail only box depending on the selected message type, and enable or disable
// the submit button. This method should be called when the message type is modified. The e-mail
// only box contains content that is only applicable to e-mails, and which should not be displayed
// for SMS templates.
function displayEmailOnlyBox()
{
  Utility.setDisplayState(emailOnlyBox, getSelectedMessageType() === MESSAGE_TYPE_EMAIL);
  enableSubmitButton();
}

// *************************************************************************************************
// Display the trigger description that corresponds to the selected trigger, and enable or disable
// the submit button. This method should be called when a trigger has been selected.
function displayTriggerDescription()
{
  var selectedTrigger;

  selectedTrigger = triggerTypeCombo.selectedIndex;
  if ((selectedTrigger < 0) || (selectedTrigger >= TRIGGER_DESCRIPTIONS.length))
    triggerDescriptionBox.innerHTML = '&nbsp;';
  else
    triggerDescriptionBox.innerHTML = TRIGGER_DESCRIPTIONS[selectedTrigger];
  enableSubmitButton();
}

// *************************************************************************************************

function closeTemplateDialogue()
{
  Utility.hide(editTemplateDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************

function enableSubmitButton()
{
  var delay, messageType;

  messageType = getSelectedMessageType();
  delay = parseInt(delayEdit.value, 10);
  delay = isFinite(delay) && (delay >= 0);

  submitButton.disabled = (nameEdit.value === '') || (messageType < 0) || !delay ||
    (triggerTypeCombo.selectedIndex < 0) ||
    ((messageType === MESSAGE_TYPE_EMAIL) && (headerEdit.value === '')) ||
    (contentBox.value === '');
}

// *************************************************************************************************
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  o = new Array(24);
  p = 0;

  o[p++] = getText(26, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(27, 'Vis alle');
  o[p++] = '</button>';
  // Message type filter.
  o[p++] = '<button type="button" class="filter-button';
  if (messageTypeFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayMessageTypeFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(28, 'Meldingstype');
  o[p++] = '</button>';
  if (messageTypeFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearMessageTypeFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Trigger type filter.
  o[p++] = '<button type="button" class="filter-button';
  if (triggerTypeFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayTriggerTypeFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(30, 'Hendelse');
  o[p++] = '</button>';
  if (triggerTypeFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearTriggerTypeFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Freetext filter.
  o[p++] = '<input type="text" id="freetextEdit" placeholder="';
  o[p++] = getText(34, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Counter.
  o[p++] = '<span class="counter">';
  if (displayedCount === templates.length)
    o[p++] = getText(12, 'Viser $1 maler', [String(templates.length)]);
  else
    o[p++] = getText(13, 'Viser $1 av $2 maler',
      [String(displayedCount), String(templates.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['freetextEdit']);
}

// *************************************************************************************************
// Return true if the list of templates should not include the given template.
function shouldHide(template)
{
  return ((messageTypeFilter !== null) &&
    !messageTypeFilter.includes(template[c.tpl.MESSAGE_TYPE])) ||
    ((triggerTypeFilter !== null) && !triggerTypeFilter.includes(template[c.tpl.TRIGGER_TYPE])) ||
    ((freetextFilter !== '') && !matchesFreetextFilter(template));
}

// *************************************************************************************************

function clearAllFilters()
{
  messageTypeFilter = null;
  triggerTypeFilter = null;
  freetextFilter = '';
  freetextEdit.value = '';
  displayTemplates();
}

// *************************************************************************************************
// Message type filter functions.
// *************************************************************************************************
// Return true if the list of templates is currently filtered on message type, and the filter
// includes the given messageType. 
function inMessageTypeFilter(messageType)
{
  return (messageTypeFilter !== null) && messageTypeFilter.includes(messageType);
}

// *************************************************************************************************

function displayMessageTypeFilterDialogue()
{
  var o, p, i;
  
  o = new Array((MESSAGE_TYPE_TEXTS.length * 9) + 8);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(29, 'Velg hvilke meldingstyper som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < MESSAGE_TYPE_TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="messageType';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inMessageTypeFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> <label for="messageType';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = MESSAGE_TYPE_TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" onclick="updateMessageTypeFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(15, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeMessageTypeFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(16, 'Avbryt');
  o[p++] = '</button></div>';

  editMessageTypeFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editMessageTypeFilterDialogue);
};

// *************************************************************************************************

function clearMessageTypeFilter()
{
  messageTypeFilter = null;
  displayTemplates();
}

// *************************************************************************************************

function updateMessageTypeFilter()
{
  var i, checkbox;

  messageTypeFilter = [];
  for (i = 0; i < MESSAGE_TYPE_TEXTS.length; i++)
  {
    checkbox = document.getElementById('messageType' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      messageTypeFilter.push(i);
  }
  // If the user unchecks all message types, instead of displaying nothing, clear the filter. If the
  // user checks all message types, also clear the filter.
  if ((messageTypeFilter.length === 0) || (messageTypeFilter.length === MESSAGE_TYPE_TEXTS.length))
    messageTypeFilter = null;
  closeMessageTypeFilterDialogue();
  displayTemplates();
}

// *************************************************************************************************

function closeMessageTypeFilterDialogue()
{
  Utility.hide(editMessageTypeFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Trigger type filter functions.
// *************************************************************************************************
// Return true if the list of templates is currently filtered on trigger type, and the filter
// includes the given triggerType. 
function inTriggerTypeFilter(triggerType)
{
  return (triggerTypeFilter !== null) && triggerTypeFilter.includes(triggerType);
}

// *************************************************************************************************

function displayTriggerTypeFilterDialogue()
{
  var o, p, i;
  
  o = new Array((TRIGGER_HEADLINES.length * 9) + 12);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(31, 'Velg hvilke hendelser som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < TRIGGER_HEADLINES.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="triggerType';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inTriggerTypeFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> <label for="triggerType';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = TRIGGER_HEADLINES[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><div class="dialogue-footer-button-group"><button type="button" onclick="setAllTriggerTypesTo(true);"><i class="fa-solid fa-check-double"></i>&nbsp;&nbsp;';
  o[p++] = getText(32, 'Alle');
  o[p++] = '</button><button type="button" onclick="setAllTriggerTypesTo(false);"><i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;';
  o[p++] = getText(33, 'Ingen');
  o[p++] = '</button></div> <button type="button" onclick="updateTriggerTypeFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(15, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeTriggerTypeFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(16, 'Avbryt');
  o[p++] = '</button></div>';

  editTriggerTypeFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editTriggerTypeFilterDialogue);
};

// *************************************************************************************************
// Check or uncheck all the trigger type checkboxes in the trigger type filter dialogue, depending
// on checked, which should be a boolean.
function setAllTriggerTypesTo(checked)
{
  var i, checkbox;

  checked = !!checked;
  for (i = 0; i < TRIGGER_HEADLINES.length; i++)
  {
    checkbox = document.getElementById('triggerType' + String(i) + 'Checkbox');
    if (checkbox)
      checkbox.checked = checked;
  }
}

// *************************************************************************************************

function clearTriggerTypeFilter()
{
  triggerTypeFilter = null;
  displayTemplates();
}

// *************************************************************************************************

function updateTriggerTypeFilter()
{
  var i, checkbox;

  triggerTypeFilter = [];
  for (i = 0; i < TRIGGER_HEADLINES.length; i++)
  {
    checkbox = document.getElementById('triggerType' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      triggerTypeFilter.push(i);
  }
  // If the user unchecks all trigger types, instead of displaying nothing, clear the filter. If the
  // user checks all trigger types, also clear the filter.
  if ((triggerTypeFilter.length === 0) || (triggerTypeFilter.length === TRIGGER_HEADLINES.length))
    triggerTypeFilter = null;
  closeTriggerTypeFilterDialogue();
  displayTemplates();
}

// *************************************************************************************************

function closeTriggerTypeFilterDialogue()
{
  Utility.hide(editTriggerTypeFilterDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Freetext filter functions.
// *************************************************************************************************
// Monitor key presses in the freetext edit box. If <enter> is pressed, update the filter.
function freetextEditKeyDown(event)
{
  if (event.key === 'Enter')
    updateFreetextFilter();
}

// *************************************************************************************************

function updateFreetextFilter()
{
  freetextFilter = freetextEdit.value;
  displayTemplates();
}

// *************************************************************************************************
// Return true if the given template matches the current freetext filter.
function matchesFreetextFilter(template)
{
  var filter;

  filter = freetextFilter.toLowerCase();
  // If there is no filter (or no template), everything matches. Otherwise, return a match if the
  // template's title, header or content fields contain the filter text.
  return (template === null) || (filter === '') ||
    (template[c.tpl.NAME].toLowerCase().indexOf(filter) >= 0) ||
    (template[c.tpl.HEADER].toLowerCase().indexOf(filter) >= 0) ||
    (template[c.tpl.CONTENT].toLowerCase().indexOf(filter) >= 0);
}

// *************************************************************************************************
