// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** class TabsetButtons
// *************************************************************************************************
// A tabset is an area of the screen that has multiple purposes. Tabset buttons allow the user to
// choose between these purposes. This class represents the buttons to choose, but not the tabs that
// hold content.
class TabsetButtons
{

// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************
// Create a new set of tabset buttons. tabButtonTexts is an array of strings to be displayed in the
// tab buttons. This also determines the number of tabs in the tabset. initialTabIndex is an integer
// that holds the index of the tab to be visible when the tabset is displayed. initialTabIndex is
// optional. The default value is 0.
constructor (tabButtonTexts, initialTabIndex)
{
  // *** Properties. ***
  // The index in the tabs list of the active tab. A value of -1 indicates that no tab is currently
  // active. The noActiveTabPermitted property says whether or not this value is permitted.
  this._activeTabIndex = Utility.getPositiveInteger(initialTabIndex, 0);
  // Flag that says whether it is possible that no tab is active.
  this._noActiveTabPermitted = false;
  // Pointer to the HTML element that holds the tab buttons.
  this._tabButtonArea = null;
  // Pointer to the event handler that will be called when the active tab changes.
  this._onChangeTab = null;
  // The texts that appear in the tab buttons. The size of this array also determines the number of
  // tabs in the tabset.
  if (Array.isArray(tabButtonTexts))
    this._tabButtonTexts = tabButtonTexts;
  else
    this._tabButtonTexts = [];

  // *** ID properties. ***
  // The ID of the HTML element that will contain the tab buttons. This is assumed to already exist
  // when the tabset is created.
  this._tabButtonAreaId = 'tabButtonArea';

  // *** Style properties. ***
  // The CSS class name of the HTML element that is added to the tab button area, in order to
  // contain the tab buttons.
  this._tabButtonContentClass = 'tab-button-content';
  // The CSS class name of the tab button of the currently displayed tab.
  this._activeTabClass = 'tab-button active-tab-button';
  // The CSS class name of all tab buttons for tabs that are not currently displayed.
  this._inactiveTabClass = 'tab-button not-active-tab-button';

  // Register the object in the instance registry. This is required for event handlers to be able to
  // talk to their parent object.
  this._registryIndex = Utility.registerInstance(this);
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Write HTML code for the tab buttons to the document.
display()
{
  this._displayTabButtons();
}

// *************************************************************************************************
// Select the next tab, if one exists. Return true if the tab index was changed.
displayNextTab()
{
  if (this._activeTabIndex < (this.tabCount - 1))
  {
    this.activeTabIndex = this._activeTabIndex + 1;
    return true;
  }
  return false;
}

// *************************************************************************************************
// Select the previous tab, if one exists. Return true if the tab index was changed.
displayPreviousTab()
{
  if (this._activeTabIndex > 0)
  {
    this.activeTabIndex = this._activeTabIndex - 1;
    return true;
  }
  return false;
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the tabCount property, which is the number of tabs in the tabset.
get tabCount()
{
  return this._tabButtonTexts.length;
}

// *************************************************************************************************
// Return the value of the activeTabIndex property.
get activeTabIndex()
{
  return this._activeTabIndex;
}

// *************************************************************************************************
// Set the activeTabIndex property. This will display or hide tabs as appropriate (if there are any)
// and regenerate the tab buttons to reflect the change. Finally, the onChangeTab event will be
// triggered.
set activeTabIndex(newValue)
{
  if (!(this._noActiveTabPermitted && (newValue === -1)))
    newValue = Utility.getPositiveInteger(newValue, this._activeTabIndex);
  if (newValue !== this._activeTabIndex)
  {
    this._activeTabIndex = newValue;
    this.display();
    if (this._onChangeTab)
      this._onChangeTab(this, this._activeTabIndex);
  }
}

// *************************************************************************************************
// Return the noActiveTabPermitted property.
get noActiveTabPermitted()
{
  return this._noActiveTabPermitted;
}

// *************************************************************************************************
// Set the noActiveTabPermitted property.
set noActiveTabPermitted(newValue)
{
  this._noActiveTabPermitted = !!newValue;
  if (!this._noActiveTabPermitted && (this.activeTabIndex < 0))
    this.activeTabIndex = 0;
}

// *************************************************************************************************
// Return the HTML element that holds the tab buttons.
get tabButtonArea()
{
  if (!this._tabButtonArea)
    this._tabButtonArea = document.getElementById(this._tabButtonAreaId);
  return this._tabButtonArea;
}

// *************************************************************************************************
// Set the event handler function that will be called when the tabset switches tabs. Event handler
// signature:
//   function(sender, activeTabIndex)
// sender is a pointer to this tabset. activeTabIndex is the index of the currently selected tab.
set onChangeTab(newEventHandler)
{
  this._onChangeTab = newEventHandler;
}

// *************************************************************************************************
// Return the array of tab button captions.
get tabButtonTexts()
{
  return this._tabButtonTexts;
}

// *************************************************************************************************
// Return the ID of the HTML element that will contain the tab buttons.
get tabButtonAreaId()
{
  return this._tabButtonAreaId;
}

// *************************************************************************************************
// Set the ID of the HTML element that will contain the tab buttons. Note that this will not
// regenerate the tab buttons.
set tabButtonAreaId(newValue)
{
  this._tabButtonAreaId = Utility.getValidString(newValue, this._tabButtonAreaId);
  this._tabButtonArea = null;
}

// *************************************************************************************************
// Return the CSS class name of the HTML element that is added to the tab button area, in order to
// contain the tab buttons.
get tabButtonContentClass()
{
  return this._tabButtonContentClass;
}

// *************************************************************************************************
// Set the CSS class name of the HTML element that is added to the tab button area, in order to
// contain the tab buttons. Note that setting this value will not cause the tab buttons to be
// regenerated.
set tabButtonContentClass(newValue)
{
  this._tabButtonContentClass = Utility.getValidString(newValue, this._tabButtonContentClass);
}

// *************************************************************************************************
// Return the CSS class name of the tab button of the currently displayed tab.
get activeTabClass()
{
  return this._activeTabClass;
}

// *************************************************************************************************
// Set the CSS class name of the tab button of the currently displayed tab. Note that setting this
// value will not cause the tab buttons to be regenerated.
set activeTabClass(newValue)
{
  this._activeTabClass = Utility.getValidString(newValue, this._activeTabClass);
}

// *************************************************************************************************
// Return the CSS class name of all tab buttons for tabs that are not currently displayed.
get inactiveTabClass()
{
  return this._inactiveTabClass;
}

// *************************************************************************************************
// Set the CSS class name of all tab buttons for tabs that are not currently displayed. Note that
// setting this value will not cause the tab buttons to be regenerated.
set inactiveTabClass(newValue)
{
  this._inactiveTabClass = Utility.getValidString(newValue, this._inactiveTabClass);
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Event handler that will be called when the user clicks the tab button with the given index. This
// may or may not change the current tab, depending on the current tabset configuration.
_clickTab(index)
{
  this.activeTabIndex = index;
}

// *************************************************************************************************
// Generate HTML for the tab buttons of this tabset, and display them in the tab button area.
_displayTabButtons()
{
  var tabButtonArea, o, p, i;

  tabButtonArea = this.tabButtonArea;
  if (tabButtonArea)
  {
    o = new Array(this.tabCount + 4);
    p = 0;
  
    o[p++] = '<table cellspacing="0" cellpadding="0" class="';
    o[p++] = this._tabButtonContentClass;
    o[p++] = '"><tbody><tr>';
    // Write tab buttons.
    for (i = 0; i < this.tabCount; i++)
    {
      o[p++] = this._getTabButton(i);
    }
    o[p++] = '</tr></tbody></table>';

    tabButtonArea.innerHTML = o.join('');
  }
}

// *************************************************************************************************
// Return HTML code for the table cell with the tab button with the given index.
_getTabButton(index)
{
  var o, p;

  o = new Array(11);
  p = 0;

  o[p++] = '<td colspan="2" class="';
  if (index === this.activeTabIndex)
    o[p++] = this._activeTabClass;
  else
    o[p++] = this._inactiveTabClass;
  o[p++] = '" style="width: calc(';
  o[p++] = String(Math.round(100.0 / Math.max(this.tabCount, 1)));
  o[p++] = '% - 20px);" onclick="Utility.getInstance(';
  o[p++] = String(this._registryIndex);
  o[p++] = ')._clickTab(';
  o[p++] = String(index);
  o[p++] = ');">';
  o[p++] = this._tabButtonTexts[index];
  o[p++] = '</td>';

  return o.join('');
}

// *************************************************************************************************

}

// *************************************************************************************************
// *** class Tabset
// *************************************************************************************************
// A tabset is an area of the screen that has multiple purposes. The area can display different user
// interfaces, called tabs. The area in which the tabs are displayed is called the tab area. Only
// one tab can be displayed at the same time. This tabset assumes that the tabs already exist. It
// will manage these tabs by hiding or displaying them, as appropriate.
//
// Outside of the tab area, usually above it, is a tab button area. The tabset assumes that the tab
// button area already exists, but will generate HTML code for, and display, a set of tab buttons in
// the tab button area. These tab buttons are used to switch between tabs. If the tabset is used in
// a wizard-style user interface, the tab button area may also include a progress bar.
//
// This tabset allows the user to specify the IDs of pre-existing elements that the tabset will use,
// and also the CSS class names of various elements, so that the tabset style can be changed at
// will. The class supports multiple instances on the same page.
class Tabset extends TabsetButtons
{
// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************
// Create a new tabset. tabButtonTexts is an array of strings to be displayed in the tab buttons.
// This also determines the number of tabs in the tabset. initialTabIndex is an integer that holds
// the index of the tab to be visible when the tabset is displayed. initialTabIndex is optional. The
// default value is 0.
constructor (tabButtonTexts, initialTabIndex)
{
  super(tabButtonTexts, initialTabIndex);

  // *** Properties. ***
  // Array of pointers to the HTML elements that represent the tabs contained in this tabset. These
  // are typically div tags. Read-only. Note that the array will not be populated until first use.
  this._tabs = null;

  // *** ID properties. ***
  // The template for the IDs of the various tabs. These must all have the same id, except for the
  // $1, which will be replaced by the index of the tab. The tabButtonTexts array determines how
  // many tabs exist.
  this._tabId = 'tab_$1';
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Write HTML code for the tab buttons to the document, and make sure the correct tab is displayed.
display()
{
  super.display();
  this._displayTabs();
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the array of tabs managed by this tabset. The tabs are the HTML elements that will be
// displayed or hidden as the active tab changes, and should not be confused with the tab buttons.
get tabs()
{
  var i;

  if (!Array.isArray(this._tabs))
  {
    this._tabs = new Array(this.tabCount);
    for (i = 0; i < this.tabCount; i++)
      this._tabs[i] = document.getElementById(Utility.expandText(this._tabId, [String(i)]));
  }
  return this._tabs;
}

// *************************************************************************************************
// Return the ID template for the HTML elements that contain the tabset's tabs. The $1 marker will
// be replaced with the index of the tab.
get tabId()
{
  return this._tabId;
}

// *************************************************************************************************
// Set the ID of the HTML element that will contain the tabset's tabs. The ID must contain a $1
// marker for the tab index. Note that setting this property will not regenerate the tab buttons,
// nor display or hide the tabs.
set tabId(newValue)
{
  newValue = Utility.getValidString(newValue, this._tabId);
  if (newValue.indexOf('$1') >= 0)
  {
    this._tabId = newValue;
    this._tabs = null;
  }
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Display or hide the tabset's tabs as appropriate, depending on the setting of the activeTabIndex
// property.
_displayTabs()
{
  var i;
  
  for (i = 0; i < this.tabCount; i++)
  {
    if (i === this.activeTabIndex)
      Utility.display(this.tabs[i]);
    else
      Utility.hide(this.tabs[i]);
  }
}

// *************************************************************************************************

}
