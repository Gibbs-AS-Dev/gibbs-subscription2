// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** class WizardTabset
// *************************************************************************************************
// The WizardTabset is a tabset that also displays a progress bar underneath the tab buttons. This
// is useful when the tabs represent a set of steps to be performed, as in a wizard.
class WizardTabset extends Tabset
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

  // *** Style properties. ***
  // Override several default CSS class names, to style the progress bar.
  this._tabButtonContentClass = 'progress-bar';
  this._activeTabClass = 'step current-step';
  this._inactiveTabClass = 'step not-current-step';
  // The CSS class name of progress bar elements that represent completed steps.
  this._completedProgressClass = 'progress done';
  // The CSS class name of progress bar elements that represent steps that have yet to be completed.
  this._incompleteProgressClass = 'progress not-done';
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the CSS class name of progress bar elements that represent completed steps.
get completedProgressClass()
{
  return this._completedProgressClass;
}

// *************************************************************************************************
// Set the CSS class name of progress bar elements that represent completed steps. Note that setting
// this value will not cause the tab buttons to be regenerated.
set completedProgressClass(newValue)
{
  this._completedProgressClass = Utility.getValidString(newValue, this._completedProgressClass);
}

// *************************************************************************************************
// Return the CSS class name of progress bar elements that represent steps that have yet to be
// completed.
get incompleteProgressClass()
{
  return this._incompleteProgressClass;
}

// *************************************************************************************************
// Set the CSS class name of progress bar elements that represent steps that have yet to be
// completed. Note that setting this value will not cause the tab buttons to be regenerated.
set incompleteProgressClass(newValue)
{
  this._incompleteProgressClass = Utility.getValidString(newValue, this._incompleteProgressClass);
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Event handler that will be called when the user clicks the tab button with the given index.
// Override to do nothing at all, as wizard tab buttons cannot be clicked.
_clickTab(index)
{
  // Doing nothing in particular.
}

// *************************************************************************************************
// Generate HTML for the tab buttons of this tabset, and display them in the tab button area.
//
// Override to also display a progress bar.
_displayTabButtons()
{
  var tabButtonArea, o, p, i;

  tabButtonArea = this.tabButtonArea;
  if (tabButtonArea)
  {
    o = new Array(this.tabCount + 6);
    p = 0;
  
    o[p++] = '<table cellspacing="0" cellpadding="0" class="';
    o[p++] = this._tabButtonContentClass;
    o[p++] = '"><tbody><tr>';
    // Write tab buttons.
    for (i = 0; i < this.tabCount; i++)
    {
      o[p++] = this._getTabButton(i);
    }
    o[p++] = '</tr>';
    // Write progress bar.
    o[p++] = this._getProgressBar();
    o[p++] = '</tbody></table>';

    tabButtonArea.innerHTML = o.join('');
  }
}

// *************************************************************************************************
// Return HTML code to display the progress bar underneath the tab buttons. For each tab button,
// write two progress bar segments, so that the progress bar can stop right underneath the active
// tab button.
_getProgressBar()
{
  var o, p, i;

  o = new Array((this.tabCount * 5) + 2);
  p = 0;

  o[p++] = '<tr>';
  for (i = 0; i < this.tabCount; i++)
  {
    o[p++] = '<td class="';
    if (i <= this.activeTabIndex)
      o[p++] = this._completedProgressClass;
    else
      o[p++] = this._incompleteProgressClass;
    o[p++] = '">&nbsp;</td><td class="';
    if (i < this.activeTabIndex)
      o[p++] = this._completedProgressClass;
    else
      o[p++] = this._incompleteProgressClass;
    o[p++] = '">&nbsp;</td>';
  }
  o[p++] = '</tr>';

  return o.join('');
}

// *************************************************************************************************

}
