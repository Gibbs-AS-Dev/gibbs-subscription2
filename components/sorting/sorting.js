// *************************************************************************************************
// *** Gibbs © 2023-2025
// *************************************************************************************************

// *************************************************************************************************
// *** class Sorting
// *************************************************************************************************

class Sorting
{

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// uiColumn constant to say that the data table is currently sorted in its default order. If the
// data table is sorted on any column, the default sorting will be lost, and cannot be recovered.
static DEFAULT_SORTING = -1;

// Constant to say that this column in the user interface is not related to a column in the data
// table, and that the column in the user interface is not sortable.
static DO_NOT = -1;

// The sorting order is unknown or not specified.
static DIR_UNKNOWN = -1;
// Sort in ascending order.
static DIR_ASCENDING = 0;
// Sort in descending order.
static DIR_DESCENDING = 1;

// Sorting constants for the dataType field in the uiColumns table.
// The column will be sorted as string. This also works on dates in ISO format (yyyy-mm-dd).
static SORT_AS_STRING = 0;
// The column will be sorted as integers. The sorting will not convert the type, so ensure the
// data table actually contains integers.
static SORT_AS_INTEGER = 1;
// The column will be sorted as booleans. In ascending order, false will be sorted before true. The
// sorting will not convert the type, so ensure the data table actually contains boolean.
static SORT_AS_BOOLEAN = 2;

// *************************************************************************************************
// *** Constructors.
// *************************************************************************************************
// Create a new sorting instance to sort the given dataTable, which is expected to be a two
// dimensional array. If the contents of the dataTable change, the sort method must be called to
// keep the table sorted. uiColumns is an array that maps columns displayed in the user interface to
// columns in the data table. uiColumns should have the same number of columns as the user interface,
// which is not necessarily the same as the number of columns in the dataTable. Each entry in
// uiColumns should be an object with two fields:
//   dataTableColumn : integer      The column in the data table which should be sorted when the
//                                  user clicks this column header in the user interface. If -1, the
//                                  column cannot be sorted on.
//   dataType : integer             The data type of the column in the dataTable. Use the SORT_AS_
//                                  constants.
//   getData : function             A function to obtain the data that will be used to compare each
//                                  row in the table to other rows. Optional. If present, the
//                                  dataTableColumn will not be used, but must still be valid.
// Use the createUiColumn method to create an object for the uiColumns table.
constructor(dataTable, uiColumns, handler)
{
  // Properties.
  // The column in the uiColumns table on which the data table is currently sorted. Integer.
  this._uiColumn = Sorting.DEFAULT_SORTING;
  // The direction in which the column in the data table is sorted.
  this._direction = Sorting.DIR_ASCENDING;
  // The name of the variable that holds this object. Used when handling events.
  this._sortingObjectName = 'sorting';
  // Flag that says whether to display a spinner before sorting. This should only be true if the
  // page contains a spinner that can be displayed with Utility.displaySpinnerThen. If true, the
  // spinner will be displayed, but not hidden. The handler is expected to do the hiding.
  this._useSpinner = true;

  // Fields.
    // *** // More error checking.
  // Pointer to the table of data which is being sorted.
  this._dataTable = dataTable;
  if (Array.isArray(uiColumns))
    this._uiColumns = uiColumns;
  else
    this._uiColumns = [];
  this._handler = handler;
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Sort the contents of the data table now, and trigger the handler. If the sorting data was
// invalid, or the selected sorting column cannot be sorted on, do nothing. Return true if the table
// was sorted successfully. Note that if the useSpinner property is set, the method will return
// true, even if the sorting has not happened yet.
sort()
{
  var dataTableColumn, dataType, me;

  // Validate sorting parameters.
  if ((this._uiColumn !== Sorting.DEFAULT_SORTING) && (this._uiColumn < this._uiColumns.length))
  {
    dataTableColumn = this._uiColumns[this._uiColumn].dataTableColumn;
    dataType = this._uiColumns[this._uiColumn].dataType;
    if ((dataTableColumn >= 0) && Sorting.isValidDataType(dataType))
    {
      me = this;
      if (this._useSpinner)
      {
        // Display the spinner before sorting and calling the event handler.
        Utility.displaySpinnerThen(
          function ()
          {
            me._dataTable.sort(me._getSortingFunction());
            setTimeout(
              function ()
              {
                if (me._handler)
                  me._handler();
              },
              10);
          });
      }
      else
      {
        // Sort and call the event handler immediately.
        this._dataTable.sort(this._getSortingFunction());
        setTimeout(
          function ()
          {
            if (me._handler)
              me._handler();
          },
          10);
      }
      return true;
    }
  }
  return false;
}

// *************************************************************************************************
// Sort the data table on the column in the user interface with the index given in uiColumn, and
// trigger the handler. If the table is currently sorted on that column, and no direction is
// specified, reverse the sorting order instead. If uiColumn is not valid, the method will do
// nothing. direction is the direction in which the column in the data table is sorted. Use the DIR_
// constants. direction is optional. If not present, the current sort order will be used.
//
// Note that direction will override the reversal of the sorting order caused by sorting on the
// current uiColumn. If both uiColumn and direction are the same as the current sorting, this method
// will do nothing.
//
// Return true if sorting was performed (and the event handler called).
sortOn(uiColumn, direction)
{
  // Validate input.
  direction = Sorting.validateDirection(direction);
  uiColumn = parseInt(uiColumn, 10);
  if (Utility.isValidIndex(uiColumn, this._uiColumns))
  {
    if (this._uiColumn === uiColumn)
    {
      // This column is already sorted on. If a direction wasn't specified, or if it was different
      // than the current direction, reverse the sorting order.
      if ((direction === Sorting.DIR_UNKNOWN) || (direction !== this._direction))
      {
        this.reverse();
        return true;
      }
    }
    else
    {
      // This column is not currently sorted on. Store the direction, if specified (without
      // sorting on it, as that will be done next), and sort on the new column.
      if (direction !== Sorting.DIR_UNKNOWN)
        this._direction = direction;
      this.uiColumn = uiColumn;
      return true;
    }
  }
  return false;
}

// *************************************************************************************************
// Reverse the sorting order on the data table, and trigger the handler. If the table currently has
// DEFAULT_SORTING, this method will have no effect.
reverse()
{
  this.direction = Sorting._getReverseDirection(this._direction);
}

// *************************************************************************************************
// Return true if the given direction is a valid value.
static isValidDirection(direction)
{
  return (direction === Sorting.DIR_ASCENDING) || (direction === Sorting.DIR_DESCENDING);
}

// *************************************************************************************************
// Return the direction, if the given direction is valid. Otherwise, return DIR_UNKNOWN.
static validateDirection(direction)
{
  direction = parseInt(direction, 10);
  if (Sorting.isValidDirection(direction))
    return direction;
  return Sorting.DIR_UNKNOWN;
}

// *************************************************************************************************
// Return true if the given data type is a valid data type.
static isValidDataType(dataType)
{
  return (dataType === Sorting.SORT_AS_STRING) || (dataType === Sorting.SORT_AS_INTEGER) ||
    (dataType === Sorting.SORT_AS_BOOLEAN);
}

// *************************************************************************************************
// Return HTML code for a table header element with the given text. The header will represent the
// column with the given index in the uiColumns table. The header will have the given headerClass.
// Pass an empty string to not have a class. headerClass is optional. An empty string is the default
// value.
getTableHeader(index, text, headerClass)
{
  var o, p, isSortable, isSorted;

  // Read parameters.
  if (!headerClass)
    headerClass = '';

  // See if this column can be sorted on, and whether the data table is already sorted on this
  // column.
  index = parseInt(index, 10);
  isSortable = Utility.isValidIndex(index, this._uiColumns) &&
    (this._uiColumns[index].dataTableColumn >= 0);
  isSorted = isSortable && (index === this._uiColumn);

  o = new Array(14);
  p = 0;

  // Write the table header tag. We always need this one.
  o[p++] = '<th';
  if (isSortable || (headerClass !== ''))
  {
    o[p++] = ' class="';
    o[p++] = headerClass;
    if (isSorted)
      o[p++] = ' sorted';
    else
    {
      if (isSortable)
        o[p++] = ' sortable';
    }
    o[p++] = '"';
  }
  o[p++] = '>';

  // If this column can be sorted on, write the caption as a link to sort on this column, or reverse
  // the sorting direction.
  if (isSortable)
  {
    o[p++] = '<a href="javascript:void(0);" onclick="';
    o[p++] = this._sortingObjectName;
    o[p++] = '.sortOn(';
    o[p++] = String(index);
    o[p++] = '); return false;">';
    o[p++] = String(text);
    o[p++] = '</a>';
    // If the data table is currently sorted on this column, add a sorting symbol to indicate the
    // direction.
    if (isSorted)
    {
      if (this._direction === Sorting.DIR_ASCENDING)
        o[p++] = '&nbsp;<i class="fa-solid fa-caret-up"></i>';
      else
        o[p++] = '&nbsp;<i class="fa-solid fa-caret-down"></i>';
    }
  }
  else
  {
    // Return a regular table header with no sorting elements.
    o[p++] = String(text);
  }
  o[p++] = '</th>';

  return o.join('');
}

// *************************************************************************************************
// Return an object that can be used in the uiColumns table that is passed to the constructor. If
// dataTableColumn is Sorting.DO_NOT, the dataType and getData values can be omitted. getData is
// optional.
static createUiColumn(dataTableColumn, dataType, getData)
{
  if (!Sorting.isValidDataType(dataType))
    dataType = Sorting.SORT_AS_STRING;
  if (!getData)
    getData = null;
  return {
      dataTableColumn: dataTableColumn,
      dataType: dataType,
      getData: getData
    };
}

// *************************************************************************************************
// Return hidden form elements that specify the sorting. These should be included whenever a request
// to the same page is made, to preserve the sorting state when the page is reloaded. uiColumnName
// and directionName are the names of the parameters passed to specify the uiColumn and direction.
// These are optional. The default values are "sort_on_ui_column" and "sort_direction". The names
// can be changed, to support having more than one sorting on the same page.
getPageStateFormElements(uiColumnName, directionName)
{
  if (!uiColumnName)
    uiColumnName = 'sort_on_ui_column';
  if (!directionName)
    directionName = 'sort_direction';

  return Utility.getHidden(uiColumnName, this._uiColumn) +
    Utility.getHidden(directionName, this._direction);
}

// *************************************************************************************************
// Update the data table using the given newValue, then sort the table according to the current sort
// settings. Return true if the table was sorted and the handler called.
setDataTable(newValue)
{
  this.dataTable = newValue;
  return this.sort();
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Return the opposite sorting direction to that given. Assumes direction is valid.
static _getReverseDirection(direction)
{
  if (direction === Sorting.DIR_ASCENDING)
    return Sorting.DIR_DESCENDING;
  return Sorting.DIR_ASCENDING;
}

// *************************************************************************************************
// Return a function that will sort the data table on the correct column, and in the proper order.
_getSortingFunction()
{
  var me, dataTableColumn, dataType, getData, locale;

  me = this;
  dataTableColumn = this._uiColumns[this._uiColumn].dataTableColumn;
  dataType = this._uiColumns[this._uiColumn].dataType;
  getData = this._uiColumns[this._uiColumn].getData;
  if (dataType === Sorting.SORT_AS_STRING)
  {
    if (currentLanguage === 'nb_NO')
      locale = 'nb';
    else
      locale = 'en-GB';
    if (this._direction === Sorting.DIR_ASCENDING)
    {
      // If a getData function is available, return a function to sort the obtained data
      // alphabetically in ascending order.
      if (getData)
        return function (a, b)
          {
            var aData, bData;

            aData = getData(a, me);
            bData = getData(b, me);
            return aData.localeCompare(bData, locale);
          };
        
      // Return a function to sort alphabetically in ascending order.
      return function (a, b)
        {
          return a[dataTableColumn].localeCompare(b[dataTableColumn], locale);
        };
    }
    // If a getData function is available, return a function to sort the obtained data
    // alphabetically in descending order.
    if (getData)
      return function (a, b)
        {
          var aData, bData;

          aData = getData(a, me);
          bData = getData(b, me);
          return bData.localeCompare(aData, locale);
        };

    // Return a function to sort alphabetically in condescending order.
    return function (a, b)
      {
        return b[dataTableColumn].localeCompare(a[dataTableColumn], locale);
      };
  }
  else
    if (dataType === Sorting.SORT_AS_INTEGER)
    {
      if (this._direction === Sorting.DIR_ASCENDING)
      {
        // If a getData function is available, return a function to sort the obtained data
        // numerically in ascending order.
        if (getData)
          return function (a, b)
            {
              var aData, bData;

              aData = getData(a, me);
              bData = getData(b, me);
              return aData - bData;
            };

        // Return a function to sort numerically in ascending order.
        return function (a, b)
          {
            return a[dataTableColumn] - b[dataTableColumn];
          };
      }
      // If a getData function is available, return a function to sort the obtained data
      // numerically in descending order.
      if (getData)
        return function (a, b)
          {
            var aData, bData;

            aData = getData(a, me);
            bData = getData(b, me);
            return bData - aData;
          };

      // Return a function to sort numerically in descending order.
      return function (a, b)
        {
          return b[dataTableColumn] - a[dataTableColumn];
        };
    }
    else
      if (dataType === Sorting.SORT_AS_BOOLEAN)
      {
        if (this._direction === Sorting.DIR_ASCENDING)
        {
          // If a getData function is available, return a function to sort the obtained data
          // as booleans in ascending order.
          if (getData)
            return function (a, b)
              {
                var aData, bData;

                aData = (getData(a, me) ? 1 : 0);
                bData = (getData(b, me) ? 1 : 0);
                return aData - bData;
              };

          // Return a function to sort numerically in ascending order.
          return function (a, b)
            {
              return (a[dataTableColumn] ? 1 : 0) - (b[dataTableColumn] ? 1 : 0);
            };
        }
        // If a getData function is available, return a function to sort the obtained data
        // numerically in descending order.
        if (getData)
          return function (a, b)
            {
              var aData, bData;

              aData = (getData(a, me) ? 1 : 0);
              bData = (getData(b, me) ? 1 : 0);
              return bData - aData;
            };

        // Return a function to sort numerically in descending order.
        return function (a, b)
          {
            return (b[dataTableColumn] ? 1 : 0) - (a[dataTableColumn] ? 1 : 0);
          };
      }
  // Goodness knows how to sort this stuff. Return a function to assert the equivalence of all
  // things.
  return function (a, b)
  {
    return 0;
  }
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the uiColumn property.
get uiColumn()
{
  return this._uiColumn;
}

// *************************************************************************************************
// Set the uiColumn property.
set uiColumn(newValue)
{
  newValue = parseInt(newValue, 10);
  if (Utility.isValidIndex(newValue, this._uiColumns) && (this._uiColumn !== newValue))
  {
    this._uiColumn = newValue;
    this.sort();
  }
}

// *************************************************************************************************
// Return the direction property.
get direction()
{
  return this._direction;
}

// *************************************************************************************************
// Set the direction property.
set direction(newValue)
{
  if (Sorting.isValidDirection(newValue) && (this._direction !== newValue))
  {
    this._direction = newValue;
    this.sort();
  }
}

// *************************************************************************************************
// Return the sortingObjectName property.
get sortingObjectName()
{
  return this._sortingObjectName;
}

// *************************************************************************************************
// Set the sortingObjectName property.
set sortingObjectName(newValue)
{
  newValue = String(newValue);
  if (newValue !== '')
  {
    this._sortingObjectName = newValue;
  }
}

// *************************************************************************************************
// Return the useSpinner property.
get useSpinner()
{
  return this._useSpinner;
}

// *************************************************************************************************
// Set the useSpinner property.
set useSpinner(newValue)
{
  this._useSpinner = !!newValue;
}

// *************************************************************************************************
// Set the dataTable property. Note that this will not sort the table. If this is desired, call the
// setDataTable method instead.
set dataTable(newValue)
{
  this._dataTable = newValue;
}

// *************************************************************************************************

}
