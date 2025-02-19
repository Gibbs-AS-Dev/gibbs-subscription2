// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** class PopupMenu
// *************************************************************************************************
// Menu that has one or more trigger buttons which, when clicked, open a list of menu items at that
// location. This menu can be used in tables, with a different menu for each table row. That means
// that the menu contents are generated each time the menu is opened. Since the contents are not
// fixed, the class does not store the items. It does, however, store the text and icon of the menu
// button which opens the popup menu.
class PopupMenu
{

// *************************************************************************************************
// *** Constructors.
// *************************************************************************************************
// Create a new PopupMenu instance. getPopupMenuContents is the event handler which generates the
// popup menu when opened.
constructor(getPopupMenuContents, popupMenuWidth)
{
  if (typeof getPopupMenuContents === 'undefined')
    getPopupMenuContents = null;

  // Properties.
  // The text in the menu button, if any. If the menu button should only have an icon, the value
  // should be null.
  this._menuButtonText = '&hellip;';
  // A string which holds the name of the icon in the menu button (such as "fa-trash"), if any. If
  // the menu button should have text only, the value should be null.
  this._menuButtonIcon = null;
  // The class name of the menu button.
  this._menuButtonClass = 'popup-menu-button';
  // Event handler which is called when the popup menu is opened, or null if there is no event
  // handler. Signature:
  //   function (sender, index)
  // The function should return HTML code for the menu contents.
  this._getPopupMenuContents = getPopupMenuContents;
  // The popup menu.
  this._popupMenu = null;
  // The class name of the popup menu.
  this._popupMenuClass = 'popup-menu';
  // The width of the popup menu, in pixels.
  popupMenuWidth = parseInt(popupMenuWidth, 10);
  if (isFinite(popupMenuWidth) && (popupMenuWidth > 0))
    this._popupMenuWidth = popupMenuWidth;
  else
    this._popupMenuWidth = 200;
    // *** // Property for attachment point.

  // Fields.
  // Flag which says whether the popup menu is currently visible.
  this._isOpen = false;
  // The index of the item for which the popup menu is currently visible, or -1 if there is only one
  // menu.
  this._currentIndex = -1;
  // The menu button that opened the popup menu, and to which the popup menu is attached, or null if
  // the menu is currently closed.
  this._currentButton = null;

  // Add event handler to reposition the popup menu when the window is resized.
  window.addEventListener('resize', this._handleWindowResize.bind(this));
  // Add event handler to close the menu if the user clicks anywhere else.
  document.addEventListener('click', this._handleDocumentClick.bind(this));

  // Register the object in the instance registry. This is required for event handlers to be able to
  // talk to their parent object.
  this._registryIndex = Utility.registerInstance(this);
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Return HTML code for the menu button that opens or closes the popup menu. index is an integer
// that holds the index of the data item for which this button applies. It is only used if there are
// several menu buttons that represent different items.
getMenuButton(index)
{
  var o, p, hasIcon, hasText;

  index = this._validateIndex(index);
  hasIcon = this._menuButtonIcon !== null;
  hasText = this._menuButtonText !== null;
  o = new Array(13);
  p = 0;

  // Write button.
  o[p++] = '<button class="';
  o[p++] = this._menuButtonClass;
  o[p++] = '" onclick="Utility.getInstance(';
  o[p++] = String(this._registryIndex);
  o[p++] = ')._click(event.target, ';
  o[p++] = String(index);
  o[p++] = '); event.stopPropagation(); return false;">';
  // Write emptiness.
  if (!hasIcon && !hasText)
    o[p++] = '&nbsp;';
  else
  {
    // Write icon.
    if (hasIcon)
    {
      o[p++] = '<i class="fa-solid ';
      o[p++] = this._menuButtonIcon;
      o[p++] = '"></i></button>';
      if (hasText)
        o[p++] = '&nbsp;&nbsp;';
    }

    // Write text.
    if (hasText)
      o[p++] = this._menuButtonText;
  }
  o[p++] = '</button>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for a single menu item in the popup menu. text is the menu item label. icon is a
// string which holds the name of the icon that goes with the label (such as "fa-trash"), if any.
// enabled is a boolean flag that says whether the button is enabled and can be clicked. handler is
// a string that holds Javascript code that says what to do when the menu item is selected. The
// popup menu will be closed automatically when the menu item is clicked.
getMenuItem(text, icon, enabled, handler)
{
  var o, p;

  o = new Array(11);
  p = 0;

  o[p++] = '<button type="button" class="menu-item" ';
  if (enabled)
  {
    o[p++] = 'onclick="Utility.getInstance(';
    o[p++] = String(this._registryIndex);
    o[p++] = ')._close(); ';
    o[p++] = handler;
    o[p++] = '">';
  }
  else
    o[p++] = 'disabled="disabled">';
  o[p++] = '<span>';
  o[p++] = String(text);
  o[p++] = '</span>';
  if (icon !== '')
  {
    o[p++] = ' <i class="fa-solid ';
    o[p++] = icon;
    o[p++] = '"></i>';
  }
  o[p++] = '</button>';

  return o.join('');
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Handle a click on the given menu button. index is the index of the item that was clicked, or -1
// if there is only one item.
_click(button, index)
{
  index = this._validateIndex(index);
  // If the menu is already open and the user clicked the button again, close the menu. Otherwise,
  // open the menu for the clicked item.
  if (this._isOpen && (index === this._currentIndex))
    this._close();
  else
    this._open(button, index);
}

// *************************************************************************************************
// Open the menu for the item with the given index. index is assumed to be valid. It can be -1 if
// there is only one item. button is the menu button that was clicked, and to which the popup menu
// should be attached.
_open(button, index)
{
  var popupMenu;

  // Set menu content.
  popupMenu = this.popupMenu;
  if (this._getPopupMenuContents === null)
    popupMenu.innerHTML = '&nbsp;';
  else
    popupMenu.innerHTML = this._getPopupMenuContents(this, index);
  popupMenu.style.width = String(this._popupMenuWidth) + 'px';

  // Display the menu.
  Utility.display(popupMenu);
  this._isOpen = true;
  this._currentIndex = index;
  this._currentButton = button;

  // Position the menu next to the button. This cannot be done until the menu is visible.
  this._updatePopupPosition();
}

// *************************************************************************************************
// Place the popup menu next to the button to which it is attached. If the window resizes, this
// method should be called again. The method depends on the popup menu being visible; do not call it
// while the popup menu is hidden.
_updatePopupPosition()
{
  var popupMenu, boundingBox, menuTop, menuBottom, viewportBottom;

  if (this._currentButton === null)
    return;
  popupMenu = this.popupMenu;
  boundingBox = this._currentButton.getBoundingClientRect();
  // Align the right edge of the popup menu with the right edge of the button.
  popupMenu.style.left = String(boundingBox.right - this._popupMenuWidth + window.scrollX) + 'px';

  // See if the bottom of the menu would extend beyond the visible edge of the viewport if placed
  // below the button. If so, put it above the button instead - but never above the top of the
  // window. Add a 5 pixel spacing between the button and the popup menu, to accommodate the shadow.
  menuTop = boundingBox.bottom + window.scrollY + 5;
  menuBottom = menuTop + popupMenu.offsetHeight;
  viewportBottom = window.innerHeight + window.scrollY;
  if (menuBottom > (viewportBottom - 10))
    menuTop = Math.max(window.scrollY + 10,
      boundingBox.top + window.scrollY - (popupMenu.offsetHeight + 5));
  popupMenu.style.top = String(menuTop) + 'px';
}

// *************************************************************************************************
// Close the menu.
_close()
{
  Utility.hide(this.popupMenu);
  this._isOpen = false;
  this._currentIndex = -1;
  this._currentButton = null;
}

// *************************************************************************************************
// Respond to a window resize event by repositioning the popup menu if it is open.
_handleWindowResize()
{
  if (this._isOpen)
    this._updatePopupPosition();
}

// *************************************************************************************************
// If the menu is open, respond to a click anywhere in the document by closing the menu if the click
// happened outside the menu.
_handleDocumentClick(event)
{
  if (this._isOpen && !this._popupMenu.contains(event.target))
    this._close();
}

// *************************************************************************************************
// Return an integer with the given index, or -1 if it was not valid.
_validateIndex(index)
{
  index = parseInt(index, 10);
  if (!isFinite(index) || (index < 0))
    return -1;
  return index;
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the menuButtonText property.
get menuButtonText()
{
  return this._menuButtonText;
}

// *************************************************************************************************
// Set the menuButtonText property.
set menuButtonText(newValue)
{
  if (newValue === null)
    this._menuButtonText = null;
  else
    this._menuButtonText = String(newValue);
}

// *************************************************************************************************
// Return the menuButtonIcon property.
get menuButtonIcon()
{
  return this._menuButtonIcon;
}

// *************************************************************************************************
// Set the menuButtonIcon property.
set menuButtonIcon(newValue)
{
  if (newValue === null)
    this._menuButtonIcon = null;
  else
    this._menuButtonIcon = String(newValue);
}

// *************************************************************************************************
// Return the menuButtonClass property.
get menuButtonClass()
{
  return this._menuButtonClass;
}

// *************************************************************************************************
// Set the menuButtonClass property.
set menuButtonClass(newValue)
{
  this._menuButtonClass = String(newValue);
}

// *************************************************************************************************
// Set the getPopupMenuContents property.
set getPopupMenuContents(newValue)
{
  this._getPopupMenuContents = newValue;
}

// *************************************************************************************************
// Return the popupMenu property.
get popupMenu()
{
  // Return the popup menu if it has already been created.
  if (this._popupMenu !== null)
    return this._popupMenu;

  // It hasn't. Create it, add it to the page and return it.
  this._popupMenu = document.createElement('div');
  this._popupMenu.className = this._popupMenuClass;
  Utility.hide(this._popupMenu);
  document.body.appendChild(this._popupMenu);
  return this._popupMenu;
}

// *************************************************************************************************
// Set the popupMenuClass property.
set popupMenuClass(newValue)
{
  this._popupMenuClass = String(newValue);
}

// *************************************************************************************************
// Return the popupMenuClass property.
get popupMenuClass()
{
  return this._popupMenuClass;
}

// *************************************************************************************************
// Set the popupMenuWidth property.
set popupMenuWidth(newValue)
{
  newValue = parseInt(newValue, 10);
  if (isFinite(newValue) && (newValue > 0))
    this._popupMenuWidth = newValue;
}

// *************************************************************************************************
// Return the popupMenuWidth property.
get popupMenuWidth()
{
  return this._popupMenuWidth;
}

// *************************************************************************************************
// Return the registryIndex property.
get registryIndex()
{
  return this._registryIndex;
}

// *************************************************************************************************

}
