// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// Note: If you need to sort the list of filter presets, you can do it as follows:
/*
  for (i = 0; i < filterPresets.length; i++)
  {
    if (Array.isArray(filterPresets[i]))
      filterPresets[i].sort(asIntAscending);
  }
*/

// *************************************************************************************************
// *** class FilterTabset
// *************************************************************************************************
// A filter tabset looks like a tabset, but actually just adjusts a filter to alter what is
// displayed in the user interface. As a result, it has no tabs.
class FilterTabset extends TabsetButtons
{
// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************
// Create a new tabset. tabButtonTexts is an array of strings to be displayed in the tab buttons.
// filterPresets is an array which, for each of the tab buttons, holds the filter (or filters) to be
// applied when that tab is active (see documentation below). initialTabIndex is an integer that
// holds the index of the tab button to be selected when the tabset is displayed. initialTabIndex is
// optional. The default value is 0.
constructor (tabButtonTexts, filterPresets, initialTabIndex)
{
  var i;

  // Call inherited constructor.
  super(tabButtonTexts, initialTabIndex);

  // *** Properties. ***
  // Flag that says whether it is possible that no tab is active.
  this._noActiveTabPermitted = true;
  // The array which, for each of the tab buttons, holds the filter (or filters) to be applied when
  // that tab is active. Each entry can be either a single filter, or an object that holds several
  // filters. Each filter is either false (which means there is no preset), null (which means there
  // is no filter - that is, that all items should be displayed), or an array of whatever is
  // meaningful for the filter being implemented. If applicable, the array should be sorted in
  // ascending order.
  this._filterPresets = filterPresets;
  // Array which holds the number of items that match each of the filter presets. The filter tabset
  // does not know how to calculate this, so it must be told. If an entry is false instead of a
  // number, the number will not be displayed.
  this._itemCounts = new Array(filterPresets.length);
  for (i = 0; i < filterPresets.length; i++)
    this._itemCounts[i] = false;
  // Event handler which will be called when the user clicks the configuration button which may be
  // added at the end of the tabset.
  this._onConfigure = null;

  // *** ID properties. ***
  // The ID of the HTML element that will contain the tab buttons. This is assumed to already exist
  // when the tabset is created.
  this._tabButtonAreaId = 'filterTabsetBox';
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Return the filter preset with the given index. The return value is false if the index is not
// valid. Otherwise, it is an array of filter values, or null if the filter tab with the given index
// should result in an unfiltered table.
getFilterPreset(index)
{
  index = parseInt(index, 10);
  if (!Utility.isValidIndex(index, this._filterPresets))
    return false;
  return this._filterPresets[index];
}

// *************************************************************************************************
// Set the number of items that match the filter preset with the given index.
setItemCount(index, count)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, this._filterPresets))
  {
    if (count === false)
      this._itemCounts[index] = false;
    else
    {
      count = parseInt(count, 10);
      if (isFinite(count))
        this._itemCounts[index] = count;
    }
  }
}

// *************************************************************************************************
// Set the index of the active tab to the first index where the given filter matches the filter
// preset stored in this object. If no entry matches, the active tab will be set to -1, or 0 if -1
// is not permitted. If the given filter is an array, it must be sorted in the same order as the
// filter presets. The presets and the given filter may also be objects, where each entry contains a
// filter. Note that the onChangeTab event handler will not be triggered by this method. Return true
// if the tab index was changed.
setActiveTabFromFilter(filter)
{
  var i, noTab;

  // See if any filter preset matches the actual filter.
  for (i = 0; i < this._filterPresets.length; i++)
  {
    if (this._filtersMatch(this._filterPresets[i], filter))
    {
      // This preset matched the given filter. See if the tab is already selected.
      if (this.activeTabIndex !== i)
      {
        this._activeTabIndex = i;
        return true;
      }
      return false;
    }
  }
  // No match was found. Ensure that no tab is selected.
  if (this._noActiveTabPermitted)
    noTab = -1;
  else
    noTab = 0;
  if (this.activeTabIndex !== noTab)
  {
    this._activeTabIndex = noTab;
    return true;
  }
  return false;
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Set the event handler function which will be called when the user clicks the configuration button
// which may be added at the end of the tabset. Event handler signature:
//   function(sender)
// sender is a pointer to this tabset.
set onConfigure(newEventHandler)
{
  this._onConfigure = newEventHandler;
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Return true if the given preset and filter match. The values may be either an object that holds a
// set of filters, a filter array or null.
_filtersMatch(preset, filter)
{
  var presetKeys, filterKeys, i;

  // See if both items are objects that contain several filters. If so, compare those filters.
  if ((typeof preset === 'object') && (preset !== null) &&
    (typeof filter === 'object') && (filter !== null))
  {
    // Find the elements contained in both objects.
    presetKeys = Object.keys(preset).sort();
    filterKeys = Object.keys(filter).sort();
    // Ensure both items hold the same number of keys.
    if ((presetKeys.length <= 0) || (presetKeys.length !== filterKeys.length))
      return false;
    // Ensure both items hold the same keys. If so, compare the filters in each key.
    for (i = 0; i < presetKeys.length; i++)
    {
      if (presetKeys[i] !== filterKeys[i])
        return false;
      if (!this._filterArraysMatch(preset[presetKeys[i]], filter[filterKeys[i]]))
        return false;
    }
    // All elements matched.
    return true;
  }

  // The items are not objects, which means they are either an array or null. They match if the
  // arrays hold the same elements, or if they are both null.
  return this._filterArraysMatch(preset, filter);
}

// *************************************************************************************************
// Return true if the given preset and filter match. Both values must be either an array or null.
// They match if the arrays hold the same elements, or if both values are null.
_filterArraysMatch(preset, filter)
{
  return ((Array.isArray(preset) && Utility.arraysEqual(preset, filter)) ||
    ((preset === null) && (filter === null)));
}

// *************************************************************************************************
// Generate HTML for the tab buttons of this filter tabset, and display them in the tab button area.
_displayTabButtons()
{
  var o, p, i, tabButtonArea;

  tabButtonArea = this.tabButtonArea;
  if (tabButtonArea)
  {
    o = new Array(this.tabCount + 7);
    p = 0;
  
    // Write tab buttons.
    for (i = 0; i < this.tabCount; i++)
    {
      o[p++] = this._getTabButton(i);
    }
    // Write customise button.
    o[p++] = '<div class="';
    o[p++] = this._inactiveTabClass;
    o[p++] = '"><button type="button" class="icon-button" onclick="Utility.getInstance(';
    o[p++] = String(this._registryIndex);
    o[p++] = ')._clickConfigure();">&hellip;</button></div>';
    tabButtonArea.innerHTML = o.join('');
  }
}

// *************************************************************************************************
// Return HTML code for the tab button with the given index.
_getTabButton(index)
{
  var o, p;

  o = new Array(12);
  p = 0;

  o[p++] = '<div class="';
  if (index === this.activeTabIndex)
    o[p++] = this._activeTabClass;
  else
    o[p++] = this._inactiveTabClass;
  o[p++] = '" onclick="Utility.getInstance(';
  o[p++] = String(this._registryIndex);
  o[p++] = ')._clickTab(';
  o[p++] = String(index);
  o[p++] = ');">';
  o[p++] = this._tabButtonTexts[index];
  if (this._itemCounts[index] !== false)
  {
    o[p++] = ' <span class="item-count">(';
    o[p++] = String(this._itemCounts[index]);
    o[p++] = ')</span>';
  }
  o[p++] = '</div>';
  return o.join('');
}

// *************************************************************************************************
// Handle a click on the configuration button that may be appended at the end of the tabset.
_clickConfigure()
{
  if (this._onConfigure)
    this._onConfigure(this);
}

// *************************************************************************************************

}
