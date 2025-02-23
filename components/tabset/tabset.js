// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** class Tabset
// *************************************************************************************************

class Tabset
{
// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************
// Create a new tabset.
// * tabButtonTexts is an array of strings to be displayed in the tab buttons. This also determines
//   the number of tabs in the tabset.
// * displayProgressBar is a boolean that says whether to also display a progress bar underneath
//   the tabs. This is useful when the tabs represent a set of steps to be performed, as in a
//   wizard.
// * tabsetId is the ID of the container in which the tab buttons will be displayed. If the value
//   passed is not a non-empty string, the default value "tabset" will be used instead.
// * tabId is the ID of individual tabs, one of which will be displayed at a time. The index of the
//   tab can be inserted with $1. Therefore, if the tabs are named "tab_0", "tab_1" and "tab_2",
//   pass "tab_$1" as the tabId. This is also the default value.
// * activeTabClass is a string with the class name of the tab button of the currently displayed
//   tab. The default value is "tab-button active-tab-button" if displayProgressBar is
//   false, and "step current-step" if it is true.
// * inactiveTabClass is a string with the class name of all tab buttons for tabs that are not
//   currently displayed. The default is "tab-button not-active-tab-button" if displayProgressBar is
//   false, and "step not-current-step" if it is true.
// * completedProgressClass is a string with the class name of progress bar elements that represent
//   completed steps. The default is "progress done".
// * incompleteProgressClass is a string with the class name of progress bar elements that represent
//   steps that have yet to be completed. The default is "progress not-done".
constructor (tabButtonTexts, displayProgressBar, tabsetId, tabId, tabsetClass, activeTabClass,
  inactiveTabClass, completedProgressClass, incompleteProgressClass)
{
  this._activeTab = -1;
  this._tabs = null;
  this._tabset = null;
  if (Array.isArray(tabButtonTexts))
    this._tabButtonTexts = tabButtonTexts;
  else
    this._tabButtonTexts = [];
  this._tabsetId = Utility.getValidString(tabsetId, 'tabset');
  this._tabId = Utility.getValidString(tabId, 'tab_$1');
  this._displayProgressBar = !!displayProgressBar;
  if (this._displayProgressBar)
    this._tabsetClass = Utility.getValidString(tabsetClass, 'progress-bar');
  else
    this._tabsetClass = Utility.getValidString(tabsetClass, 'tabset');
  if (this._displayProgressBar)
  {
    this._activeTabClass = Utility.getValidString(activeTabClass, 'step current-step');
    this._inactiveTabClass = Utility.getValidString(inactiveTabClass, 'step not-current-step');
  }
  else
  {
    this._activeTabClass = Utility.getValidString(activeTabClass, 'tab-button active-tab-button');
    this._inactiveTabClass = Utility.getValidString(inactiveTabClass,
      'tab-button not-active-tab-button');
  }
  this._completedProgressClass = Utility.getValidString(completedProgressClass, 'progress done');
  this._incompleteProgressClass = Utility.getValidString(incompleteProgressClass,
    'progress not-done');
  this._registryIndex = Utility.registerInstance(this);
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the number of tabs in the tabset.
get tabCount()
{
  return this._tabButtonTexts.length;
}

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
// *** // Rename to tabButtons or something?
get tabset()
{
  if (!this._tabset)
    this._tabset = document.getElementById(this._tabsetId);
  return this._tabset;
}

// *************************************************************************************************

get activeTab()
{
  return this._activeTab;
}

// *************************************************************************************************

set activeTab(newValue)
{
  this._activeTab = Utility.getPositiveInteger(newValue, this._activeTab);
  this._displayTabButtons();
  this._displayTabs();
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Event handler that will be called when the user clicks the tab button with the given index. This
// may or may not change the current tab, depending on the current tabset configuration.
_clickTab(index)
{
  if (!this._displayProgressBar)
    this.activeTab = index;
}

// *************************************************************************************************

_displayTabButtons()
{
  var tabset, o, p, i;

  tabset = this.tabset;
  if (tabset)
  {
    o = new Array((this.tabCount * 14) + 7);
    p = 0;
  
    o[p++] = '<table cellspacing="0" cellpadding="0" class="';
    o[p++] = this._tabsetClass;
    o[p++] = '"><tbody><tr>';
    // Write tab buttons.
    for (i = 0; i < this.tabCount; i++)
    {
      o[p++] = '<td colspan="2" class="';
      if (i === this.activeTab)
        o[p++] = this._activeTabClass;
      else
        o[p++] = this._inactiveTabClass;
      o[p++] = '" onclick="Utility.getInstance(';
      o[p++] = String(this._registryIndex);
      o[p++] = ')._clickTab(';
      o[p++] = String(i);
      o[p++] = ');">';
      o[p++] = this._tabButtonTexts[i];
      o[p++] = '</td>';
    }
    o[p++] = '</tr>';
    // Write progress bar. For each tab button, write two progress bar segments, so that the
    // progress bar can stop right underneath the active tab button.
    if (this._displayProgressBar)
    {
      o[p++] = '<tr>';
      for (i = 0; i < this.tabCount; i++)
      {
        o[p++] = '<td class="';
        if (i <= this.activeTab)
          o[p++] = this._completedProgressClass;
        else
          o[p++] = this._incompleteProgressClass;
        o[p++] = '">&nbsp;</td><td class="';
        if (i < this.activeTab)
          o[p++] = this._completedProgressClass;
        else
          o[p++] = this._incompleteProgressClass;
        o[p++] = '">&nbsp;</td>';
      }
      o[p++] = '</tr>';
    }
    o[p++] = '</tbody></table>';

    tabset.innerHTML = o.join('');
  }
}

// *************************************************************************************************

_displayTabs()
{
  var i;
  
  for (i = 0; i < this.tabCount; i++)
  {
    if (i === this.activeTab)
      Utility.display(this.tabs[i]);
    else
      Utility.hide(this.tabs[i]);
  }
}

// *************************************************************************************************

}
