// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** Variables.
// *************************************************************************************************
// Pointers to user interface elements.
var filterToolbar, messageLogBox, overlay, contentDialogue, editDeliveredFilterDialogue;

// Pointers to dynamically generated user interface elements. These will be populated once the HTML
// code to display them has been generated.
var freetextEdit;

// The sorting object that controls the sorting of the message log table.
var sorting;

// The popup menu for the message log table.
var menu;

// The number of displayed messages. This depends on the current filter settings.
var displayedCount = 0;

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function initialise()
{
  var i;

  // Ensure log message contents do not contain encoded line breaks.
  for (i = 0; i < messageLog.length; i++)
    messageLog[i][c.log.CONTENT] = Utility.decodeLineBreaks(messageLog[i][c.log.CONTENT]);

  // Obtain pointers to user interface elements.
  Utility.readPointers(['filterToolbar', 'messageLogBox', 'overlay', 'contentDialogue',
    'editDeliveredFilterDialogue']);

  // Create the popup menu.
  menu = new PopupMenu(getPopupMenuContents);

  // Initialise sorting.
  sorting = new Sorting(messageLog,
      [
        Sorting.createUiColumn(c.log.RECIPIENT, Sorting.SORT_AS_STRING,
          function (message)
          {
            return String(message[c.log.MESSAGE_TYPE]) + ' ' + message[c.log.RECIPIENT];
          }),
        Sorting.createUiColumn(c.log.PRODUCT_NAME, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.log.HEADER, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.log.CONTENT, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.log.TIME_SENT, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(c.log.DELIVERED, Sorting.SORT_AS_BOOLEAN),
        Sorting.createUiColumn(c.log.ERROR_MESSAGE, Sorting.SORT_AS_STRING),
        Sorting.createUiColumn(Sorting.DO_NOT)
      ],
      doDisplayMessageLog
    );
  doDisplayMessageLog();
}

// *************************************************************************************************
// Display the spinner. Once visible, display messages.
function displayMessageLog()
{
  Utility.displaySpinnerThen(doDisplayMessageLog);
}

// *************************************************************************************************
// Display the list of messages.
function doDisplayMessageLog()
{
  var o, p, i;
  
  if (messageLog.length <= 0)
  {
    messageLogBox.innerHTML = '<div class="form-element">' +
      getText(0, 'Det er ikke sendt ut noen meldinger enn&aring;.') + '</div>';
    Utility.hideSpinner();
    return;
  }

  displayedCount = 0;
  o = new Array((messageLog.length * 25) + 11);
  p = 0;
  
  // Header.
  o[p++] = '<table cellspacing="0" cellpadding="0"><thead><tr>';
  o[p++] = sorting.getTableHeader(0, getText(1, 'Mottaker'));
  o[p++] = sorting.getTableHeader(1, getText(2, 'Lagerbod'));
  o[p++] = sorting.getTableHeader(2, getText(3, 'Overskrift'));
  o[p++] = sorting.getTableHeader(3, getText(4, 'Innhold'));
  o[p++] = sorting.getTableHeader(4, getText(5, 'Tidspunkt'));
  o[p++] = sorting.getTableHeader(5, getText(6, 'Levert?'));
  o[p++] = sorting.getTableHeader(6, getText(7, 'Feilmelding'));
  o[p++] = sorting.getTableHeader(7, '&nbsp;');
  o[p++] = '</tr></thead><tbody>';
  for (i = 0; i < messageLog.length; i++)
  {
    if (shouldHide(messageLog[i]))
      continue;
    displayedCount++;

    // Message type icon and recipient (phone number or e-mail, depending on message type).
    o[p++] = '<tr><td>';
    o[p++] = Utility.getMessageTypeIcon(messageLog[i][c.log.MESSAGE_TYPE]);
    o[p++] = ' ';
    o[p++] = messageLog[i][c.log.RECIPIENT];
    // Product name.
    o[p++] = '</td><td>';
    if (messageLog[i][c.log.PRODUCT_NAME] === '')
      o[p++] = '&nbsp;';
    else
      o[p++] = messageLog[i][c.log.PRODUCT_NAME];
    // Message headline, if any.
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(messageLog[i][c.log.HEADER], 50);
    if (messageLog[i][c.log.HEADER].length > 50)
    {
      o[p++] = ' <button type="button" class="icon-button" onclick="displayContent(';
      o[p++] = String(i);
      o[p++] = ');"><i class="fa-solid fa-circle-info"></i></button>';
    }
    // Message.
    o[p++] = '</td><td>';
    o[p++] = Utility.curtail(messageLog[i][c.log.CONTENT], 50);
    o[p++] = ' <button type="button" class="icon-button" onclick="displayContent(';
    o[p++] = String(i);
    o[p++] = ');"><i class="fa-solid fa-circle-info"></i></button>';
    // Timestamp.
    o[p++] = '</td><td>';
    o[p++] = messageLog[i][c.log.TIME_SENT],
    // Delivery status.
    o[p++] = '</td><td>';
    if (messageLog[i][c.log.DELIVERED])
      o[p++] = '<i class="fa-solid fa-check icon-green"></i>';
    else
      o[p++] = '<i class="fa-solid fa-xmark icon-red"></i>';
    // Error message, if any.
    o[p++] = '</td><td>';
    o[p++] = messageLog[i][c.log.ERROR_MESSAGE],
    // Buttons.
    o[p++] = '</td><td>';
    o[p++] = menu.getMenuButton(i);
    o[p++] = '</td></tr>';
  }
  o[p++] = '</tbody></table>';

  messageLogBox.innerHTML = o.join('');
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
  if (!Utility.isValidIndex(index, messageLog))
    return '';
  o = new Array(1);
  p = 0;

  // Display recipient button.
  o[p++] = sender.getMenuItem(getText(8, 'Vis mottaker'), 'fa-up-right-from-square', true,
    'Utility.displaySpinnerThenGoTo(\'/subscription/html/admin_edit_user.php?user_id=' +
    String(messageLog[index][c.log.USER_ID]) + '\');');
  return o.join('');
}

// *************************************************************************************************
// Display a dialogue box with the full text of the header and content fields. These fields may be
// clipped in the table, so we need to be able to display the whole text.
function displayContent(index)
{
  var o, p;
  
  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, messageLog))
    return;
  o = new Array(11);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(4, 'Innhold');
  o[p++] = '</h1></div><div class="dialogue-content">';
  if (messageLog[index][c.log.HEADER] !== '')
  {
    o[p++] = '<div class="form-element">';
    o[p++] = messageLog[index][c.log.HEADER];
    o[p++] = '</div>';
  }
  o[p++] = '<textarea readonly="readonly">';
  o[p++] = messageLog[index][c.log.CONTENT];
  o[p++] = '</textarea></div><div class="dialogue-footer"><button type="button" onclick="closeContentDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(18, 'Lukk');
  o[p++] = '</button></div>';

  contentDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(contentDialogue);
}

// *************************************************************************************************

function closeContentDialogue()
{
  Utility.hide(contentDialogue);
  Utility.hide(overlay);
}

// *************************************************************************************************
// Generic filter functions.
// *************************************************************************************************

function displayFilterToolbar()
{
  var o, p;
  
  o = new Array(18);
  p = 0;

  // Clear all filters button.
  o[p++] = getText(9, 'Filter:');
  o[p++] = ' <button type="button" onclick="clearAllFilters();"><i class="fa-solid fa-filter-slash"></i> ';
  o[p++] = getText(10, 'Vis alle');
  o[p++] = '</button>';
  // Delivered filter button.
  o[p++] = '<button type="button" class="filter-button';
  if (deliveredFilter !== null)
    o[p++] = ' filtered';
  else
    o[p++] = ' unfiltered';
  o[p++] = '" onclick="displayDeliveredFilterDialogue();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(11, 'Levert');
  o[p++] = '</button>';
  // Clear delivered filter button.
  if (deliveredFilter !== null)
    o[p++] = '<button type="button" class="icon-button" onclick="clearDeliveredFilter();"><i class="fa-solid fa-xmark"></i></button>';
  // Freetext filter edit.
  o[p++] = '<input type="text" id="freetextEdit" placeholder="';
  o[p++] = getText(17, 'S&oslash;k');
  o[p++] = '" class="freetext-filter-box" value="';
  o[p++] = freetextFilter;
  o[p++] = '" onkeydown="freetextEditKeyDown(event);" /><button type="button" class="freetext-filter-button" onclick="updateFreetextFilter();"><i class="fa-solid fa-search"></i></button>';
  // Display counter box.
  o[p++] = '<span class="counter">';
  if (displayedCount === messageLog.length)
    o[p++] = getText(12, 'Viser $1 meldinger', [String(messageLog.length)]);
  else
    o[p++] = getText(13, 'Viser $1 av $2 meldinger',
      [String(displayedCount), String(messageLog.length)]);
  o[p++] = '</span>';

  filterToolbar.innerHTML = o.join('');

  // Obtain pointers to user interface elements.
  Utility.readPointers(['freetextEdit']);
}

// *************************************************************************************************
// Return true if the message log should not include the given message.
function shouldHide(message)
{
  var delivered;

  delivered = (message[c.log.DELIVERED] ? 1 : 0);
  return ((deliveredFilter !== null) && !deliveredFilter.includes(delivered)) ||
    ((freetextFilter !== '') && !matchesFreetextFilter(message));
}

// *************************************************************************************************

function clearAllFilters()
{
  deliveredFilter = null;
  freetextFilter = '';
  freetextEdit.value = '';
  displayMessageLog();
}

// *************************************************************************************************
// Delivered filter functions.
// *************************************************************************************************
// Return true if the message log is currently filtered on the delivered field, and the filter
// includes the given state (true or false). 
function inDeliveredFilter(delivered)
{
  delivered = (delivered ? 1 : 0);
  return (deliveredFilter !== null) && deliveredFilter.includes(delivered);
}

// *************************************************************************************************

function displayDeliveredFilterDialogue()
{
  var o, p, i;
  
  o = new Array((DELIVERED_TEXTS.length * 9) + 8);
  p = 0;

  o[p++] = '<div class="dialogue-header"><h1>'
  o[p++] = getText(14, 'Velg leveransestatus som skal vises');
  o[p++] = '</h1></div><div class="dialogue-content"><form action="#">';
  for (i = 0; i < DELIVERED_TEXTS.length; i++)
  {
    o[p++] = '<div class="form-element"><input type="checkbox" id="delivered';
    o[p++] = String(i);
    o[p++] = 'Checkbox" ';
    if (inDeliveredFilter(i))
      o[p++] = 'checked="checked" ';
    o[p++] = '/> <label for="delivered';
    o[p++] = String(i);
    o[p++] = 'Checkbox">';
    o[p++] = DELIVERED_TEXTS[i];
    o[p++] = '</label></div>';
  }
  o[p++] = '</form></div><div class="dialogue-footer"><button type="button" onclick="updateDeliveredFilter();"><i class="fa-solid fa-filter"></i> ';
  o[p++] = getText(15, 'Oppdater');
  o[p++] = '</button> <button type="button" onclick="closeDeliveredFilterDialogue();"><i class="fa-solid fa-xmark"></i> ';
  o[p++] = getText(16, 'Avbryt');
  o[p++] = '</button></div>';

  editDeliveredFilterDialogue.innerHTML = o.join('');
  Utility.display(overlay);
  Utility.display(editDeliveredFilterDialogue);
};

// *************************************************************************************************

function clearDeliveredFilter()
{
  deliveredFilter = null;
  displayMessageLog();
}

// *************************************************************************************************

function updateDeliveredFilter()
{
  var i, checkbox;

  deliveredFilter = [];
  for (i = 0; i < DELIVERED_TEXTS.length; i++)
  {
    checkbox = document.getElementById('delivered' + String(i) + 'Checkbox');
    if (checkbox && checkbox.checked)
      deliveredFilter.push(i);
  }
  // If the user unchecks all delivery types, instead of displaying nothing, clear the filter. If
  // the user checks all delivery types, also clear the filter.
  if ((deliveredFilter.length === 0) || (deliveredFilter.length === DELIVERED_TEXTS.length))
    deliveredFilter = null;
  closeDeliveredFilterDialogue();
  displayMessageLog();
}

// *************************************************************************************************

function closeDeliveredFilterDialogue()
{
  Utility.hide(editDeliveredFilterDialogue);
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
  displayMessageLog();
}

// *************************************************************************************************
// Return true if the given message matches the current freetext filter.
function matchesFreetextFilter(message)
{
  var filter;

  filter = freetextFilter.toLowerCase();
  // If there is no filter (or no message), everything matches. Otherwise, return a match if the
  // message's recipient, product_name, header, content or error_message fields contain the filter
  // text.
  return (message === null) || (filter === '') ||
    (message[c.log.RECIPIENT].toLowerCase().indexOf(filter) >= 0) ||
    (message[c.log.PRODUCT_NAME].toLowerCase().indexOf(filter) >= 0) ||
    (message[c.log.HEADER].toLowerCase().indexOf(filter) >= 0) ||
    (message[c.log.CONTENT].toLowerCase().indexOf(filter) >= 0) ||
    (message[c.log.ERROR_MESSAGE].toLowerCase().indexOf(filter) >= 0);
}

// *************************************************************************************************
