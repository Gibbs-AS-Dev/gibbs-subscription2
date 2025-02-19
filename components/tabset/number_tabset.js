// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** class NumberTabset
// *************************************************************************************************
// The NumberTabset is a tabset that displays its tabs as a series of numbered steps.
//
// The NumberTabset does not use the inactiveTabClass. Instead, it has properties incompleteTabClass
// and completedTabClass.
class NumberTabset extends Tabset
{
// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************
// Create a new tabset. tabCount is an integer that specifies the number of steps. initialTabIndex
// is an integer that holds the index of the tab to be visible when the tabset is displayed.
// initialTabIndex is optional. The default value is 0.
constructor (tabCount, initialTabIndex)
{
  var i, tabButtonTexts;

  // Compose tab button texts.
  tabCount = Utility.getValidInteger(tabCount, 1);
  tabButtonTexts = new Array(tabCount);
  for (i = 0; i < tabCount; i++)
    tabButtonTexts[i] = String(i + 1);

  // Call inherited constructor.
  super(tabButtonTexts, initialTabIndex);

  // *** Style properties. ***
  // The CSS class name of all tab buttons for tabs that have been visited - that is, tabs that have
  // a lower index than the active tab.
  this._completedTabClass = 'tab-button completed-tab-button';
  // The CSS class name of all tab buttons for tabs that have not yet been visited - that is, tabs
  // that have a higher index than the active tab.
  this._incompleteTabClass = 'tab-button incomplete-tab-button';
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the CSS class name of all tab buttons for tabs that have been visited - that is, tabs that
// have a lower index than the active tab.
get completedTabClass()
{
  return this._completedTabClass;
}

// *************************************************************************************************
// Set the CSS class name of all tab buttons for tabs that have been visited - that is, tabs that
// have a lower index than the active tab. Note that setting this value will not cause the tab
// buttons to be regenerated.
set completedTabClass(newValue)
{
  this._completedTabClass = Utility.getValidString(newValue, this._completedTabClass);
}

// *************************************************************************************************
// Return the CSS class name of all tab buttons for tabs that have not yet been visited - that is,
// tabs that have a higher index than the active tab.
get incompleteTabClass()
{
  return this._incompleteTabClass;
}

// *************************************************************************************************
// Set the CSS class name of all tab buttons for tabs that have not yet been visited - that is, tabs
// that have a higher index than the active tab. Note that setting this value will not cause the tab
// buttons to be regenerated.
set incompleteTabClass(newValue)
{
  this._incompleteTabClass = Utility.getValidString(newValue, this._incompleteTabClass);
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Event handler that will be called when the user clicks the tab button with the given index.
// Override to do nothing at all, as NumberTabset tab buttons cannot be clicked.
_clickTab(index)
{
  // Doing nothing in particular.
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
  
    // Calculate the width of the button area. The buttons are 30 pixels wide, and have a 20 pixels
    // gap between them.
    o[p++] = '<div class="tab-button-content" style="width: ';
    o[p++] = String((this.tabCount * 50) - 20);
    o[p++] = 'px;">';
    // Write tab buttons.
    for (i = 0; i < this.tabCount; i++)
    {
      o[p++] = this._getTabButton(i);
    }
    o[p++] = '</div>';

    tabButtonArea.innerHTML = o.join('');
  }
}

// *************************************************************************************************
// Return HTML code for the tab button with the given index.
_getTabButton(index)
{
  var o, p;

  o = new Array(5);
  p = 0;

  o[p++] = '<div class="';
  if (index < this.activeTabIndex)
    o[p++] = this._completedTabClass;
  else
  {
    if (index > this.activeTabIndex)
      o[p++] = this._incompleteTabClass;
    else
      o[p++] = this._activeTabClass;
  }
  o[p++] = '">';
  if (index < this.activeTabIndex)
    o[p++] = '<i class="fa-solid fa-check"></i>';
  else
    o[p++] = this._tabButtonTexts[index];
  o[p++] = '</div>';

  return o.join('');
}

// *************************************************************************************************

}
