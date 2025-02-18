// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** class CalendarMonth
// *************************************************************************************************
// This class specifies a single month that can be displayed in the calendar.
class CalendarMonth
{
// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************
// Create a new CalendarMonth object. year and month are integers. The month should be
// zero-based, like Javascript dates.
constructor (year, month, monthNames, monthNamesInSentence)
{
  this._year = year;
  this._month = month;
  this._monthName = null;
  this._monthNameInSentence = null;
  this._displayName = null;
  this._displayNameInSentence = null;
  this._monthNames = monthNames;
  this._monthNamesInSentence = monthNamesInSentence;
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Return a new CalendarMonth object that represents the next month from this one, keeping in
// mind that it might be next year.
getNextMonth()
{
  var month, year;

  month = this._month + 1;
  if (month >= 12)
  {
    year = this._year + 1;
    month = 0;
  }
  else
    year = this._year;
  return new CalendarMonth(year, month, this._monthNames, this._monthNamesInSentence);
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the year property as an integer.
get year()
{
  return this._year;
}

// *************************************************************************************************
// Return the month property as an integer. The number will be in Javascript format, which is zero
// based.
get month()
{
  return this._month;
}

// *************************************************************************************************
// Return the name of this month. The returned text does not include the year, and is intended for
// use as a headline, or at the start of a sentence.
get monthName()
{
  // Set the value, if it hasn't been read before.
  if (!this['_displayName'])
    this._monthName = this._monthNames[this._month];

  return this._monthName;
}

// *************************************************************************************************
// Return the name of this month. The returned text does not include the year, and is intended for
// use as part of a sentence.
get monthNameInSentence()
{
  // Set the value, if it hasn't been read before.
  if (!this['_monthNameInSentence'])
    this._monthNameInSentence = this._monthNamesInSentence[this._month];

  return this._monthNameInSentence;
}

// *************************************************************************************************
// Return the display name of this month. The returned text includes both the month and the year,
// and is intended for use as a headline, or at the start of a sentence.
get displayName()
{
  // Set the value, if it hasn't been read before.
  if (!this['_displayName'])
    this._displayName = this.monthName + ' ' + Utility.pad(this._year, 4);

  return this._displayName;
}

// *************************************************************************************************
// Return the display name of this month. The returned text includes both the month and the year,
// and is intended for use as part of a sentence.
get displayNameInSentence()
{
  // Set the value, if it hasn't been read before.
  if (!this['_displayNameInSentence'])
    this._displayNameInSentence = this.monthNameInSentence + ' ' + Utility.pad(this._year, 4);

  return this._displayNameInSentence;
}

// *************************************************************************************************

}

// *************************************************************************************************
// *** class Calendar
// *************************************************************************************************
// A calendar that can be displayed in HTML. It allows the user to select a date from among a set
// of displayable months. The calendar displays one month at a time, as well as a user interface to
// move between months. The selected date can be today or later, but no earlier.
//
// Note that the calendar will not write its HTML code until you call the display method. After
// that, the calendar will automatically keep its HTML code up-to-date based on changes.
class Calendar
{
// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************
// Create a new calendar. selectableMonthCount is the number of months, including the current month,
// that the user can move between when selecting the date. selectableMonthCount is optional; the
// default value is 6. calendarBoxId is the ID of the div tag into which the calendar will be
// rendered. calendarBoxId is optional; the default value is "calendarBox".
constructor (selectableMonthCount, calendarBoxId)
{
  this._selectedDate = null;
  this._onSelectDate = null;
  // The first selectable date, or null if the first selectable date should be today.
  this._firstSelectableDate = null;
  // The last selectable date, or null if all dates should be selectable, until the end of the
  // selectable month count.
  this._lastSelectableDate = null;
  // Flag that says whether the component has written its HTML code to the user interface. Once this
  // is done, the component will keep itself up-to-date.
  this._rendered = false;
  this._displayedMonthIndex = 0;
  this._calendarBoxId = Utility.getValidString(calendarBoxId, 'calendarBox');
  this._dayNames = ['', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  this._monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
    'September', 'October', 'November', 'December'];
  this._monthNamesInSentence = ['January', 'February', 'March', 'April', 'May', 'June', 'July',
    'August', 'September', 'October', 'November', 'December'];
  this._registryIndex = Utility.registerInstance(this);
  // Setting the selectable month count will generate the selectable months.
  this.selectableMonthCount = Utility.getValidInteger(selectableMonthCount, 6);
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Update the calendar to display the previous month, if available.
displayPreviousMonth()
{
  this._displayedMonthIndex = Math.max(this._displayedMonthIndex - 1, 0);
  this._render();
}

// *************************************************************************************************
// Update the calendar to display the next month, if available.
displayNextMonth()
{
  this._displayedMonthIndex = Math.min(this._displayedMonthIndex + 1,
    this._selectableMonths.length - 1);
  this._render();
}

// *************************************************************************************************
// Have the calendar write its HTML code to the user interface.
display()
{
  if (!this._rendered)
  {
    this._rendered = true;
    this._render();
  }
}

// *************************************************************************************************
// Return the first date that can be selected, as a string in ISO format ("yyyy-mm-dd"). If today is
// the first date that can be selected, return today's date. If the first selectable date is earlier
// than today, then return today's date as well.
getActualFirstSelectableDate()
{
  var today;

  today = Utility.getCurrentIsoDate();
  if ((this._firstSelectableDate === null) || (this._firstSelectableDate < today))
    return today;
  return this._firstSelectableDate;
}

// *************************************************************************************************
// Return the first date that can be selected, as a string in ISO format ("yyyy-mm-dd"). This is
// either determined by the _lastSelectableDate field, or the last day in the last selectable month.
getActualLastSelectableDate()
{
  var lastSelectableMonth;

  if (this._lastSelectableDate === null)
  {
    lastSelectableMonth = this._selectableMonths[this._selectableMonths.length - 1];
    return Utility.getLastDay(lastSelectableMonth.year, lastSelectableMonth.month);
  }
  return this._lastSelectableDate;
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Fill in the selectableMonths array, using the number of months supplied in
// this._selectableMonthCount.
_generateSelectableMonths()
{
  var now, i;

  // Create array to hold the selectable months. The array must always have at least one entry.
  this._selectableMonths = new Array(this._selectableMonthCount);

  // Create the first selectable month.
  now = new Date();
  this._selectableMonths[0] = new CalendarMonth(now.getFullYear(), now.getMonth(),
    this._monthNames, this._monthNamesInSentence);

  // Fill in the subsequent months, if there are any.
  for (i = 1; i < this._selectableMonthCount; i++)
    this._selectableMonths[i] = this._selectableMonths[i - 1].getNextMonth();
}

// *************************************************************************************************
// Display the calendar. Today's date is indicated with a red square (or whatever the CSS says). The
// selected date, if any, is highlighted. Also, weekdays and weekends have different colours. Monday
// is the first day, and Sunday is the last day of the week.
_render()
{
  var o, p, i, j, today, firstSelectableDate, lastSelectableDate, year, month, daysInMonth,
    firstDay, dayCounter, isoDate, calendarBox, isToday, isSelected;

  // If the component has not yet written itself to the user interface, do not do so now.
  if (!this._rendered)
    return;

  // Get the box into which the calendar should be written.
  calendarBox = document.getElementById(this._calendarBoxId);
  if (calendarBox === null)
    return;

  // Get the current date.
  today = Utility.getCurrentIsoDate();

  // Get the selectable date range.
  firstSelectableDate = this.getActualFirstSelectableDate();
  lastSelectableDate = this.getActualLastSelectableDate();

  // Get the year and month to be displayed.
  year = this._selectableMonths[this._displayedMonthIndex].year;
  month = this._selectableMonths[this._displayedMonthIndex].month;

  // Get the number of days in the month.
  daysInMonth = new Date(year, month + 1, 0).getDate();

  // Get the first day of the month. The number ranges from 0 (Sunday) to 6 (Saturday).
  firstDay = new Date(year, month, 1).getDay();
  // Translate to the range 1 (Monday) to 7 (Sunday).
  if (firstDay === 0)
    firstDay = 7;

  o = new Array(415); // (6 * ((7 * 9) + 2)) + (7 * 3) + 4 = 415
  p = 0;

  o[p++] = this._getCalendarHeadline();

  // Create a row for the days of the week.
  o[p++] = '<table><thead><tr>';
  for (i = 1; i < this._dayNames.length; i++)
  {
    o[p++] = '<th>';
    o[p++] = this._dayNames[i];
    o[p++] = '</th>';
  }
  o[p++] = '</tr></thead><tbody>';

  // Create rows for each week in the month.
  dayCounter = 1;
  for (i = 0; i < 6; i++)
  {
    o[p++] = '<tr>';
    // Create cells for each day in the week.
    for (j = 1; j <= 7; j++)
    {
      // See if the day is outside the area of the currently selected month.
      if (((i === 0) && (j < firstDay)) || (dayCounter > daysInMonth))
      {
        o[p++] = '<td class="';
        o[p++] = this._getStyle(true, false, false, j);
        o[p++] = '">&nbsp;</td>';
      }
      else
      {
        isoDate = Utility.getIsoDate(year, month, dayCounter);
        isToday = isoDate === today;
        // See if the day is outside the selectable date range.
        if ((isoDate < firstSelectableDate) || (isoDate > lastSelectableDate))
        {
          o[p++] = '<td class="';
          o[p++] = this._getStyle(true, false, isToday, j);
          o[p++] = '">';
          o[p++] = String(dayCounter);
          o[p++] = '</td>';
        }
        else
        {
          // The date is selectable unless already selected.
          isSelected = isoDate === this._selectedDate;
          o[p++] = '<td class="';
          o[p++] = this._getStyle(false, isSelected, isToday, j);
          if (isSelected)
            o[p++] = '">';
          else
          {
            o[p++] = '" onclick="Utility.getInstance(';
            o[p++] = String(this._registryIndex);
            o[p++] = ').selectedDate = \'';
            o[p++] = isoDate;
            o[p++] = '\';">';
          }
          o[p++] = String(dayCounter);
          o[p++] = '</td>';
        }
        dayCounter++;
      }
    }
    o[p++] = '</tr>';
    if (dayCounter > daysInMonth) break;
  }
  o[p++] = '</tbody></table>';

  calendarBox.innerHTML = o.join('');
}

// *************************************************************************************************
// Return the CSS classes that describe a square in the calendar. day is an integer that holds the
// number of the day in question, with 1 representing Monday and 7 representing Sunday. The other
// parameters are boolean values. The method may use the following classes:
//   weekend, disabled, holiday, selectable, selected, today
_getStyle(isDisabled, isSelected, isToday, day)
{
  var o, p, isWeekend, isHoliday;

  isWeekend = day >= 6;
    // *** // Consider bank holidays for each country.
  isHoliday = day === 7;

  o = new Array(4);
  p = 0;

  // A square is either disabled, selectable or selected.
  if (isDisabled)
    o[p++] = 'disabled';
  else
    if (isSelected)
      o[p++] = 'selected';
    else
      o[p++] = 'selectable';
  // Holidays also get the weekend class, in order to set the background.
  if (isHoliday || isWeekend)
    o[p++] = 'weekend';
  // Holidays only get the holiday class if the date is not disabled, as it makes the text colour
  // look clickable.
  if (isHoliday && !isDisabled)
    o[p++] = 'holiday';
  if (isToday)
    o[p++] = 'today';

  return o.join(' ');
}

// *************************************************************************************************
// Return HTML code to display the calendar headline, which includes the name of the month, the year
// and the buttons to move to the previous and next months.
_getCalendarHeadline()
{
  var o, p;

  o = new Array(11);
  p = 0;

  o[p++] = '<div class="calendar-header"><div class="month-scroll-button">';
  if (this._displayedMonthIndex > 0)
  {
    o[p++] = '<button type="button" class="icon-button" onclick="Utility.getInstance(';
    o[p++] = String(this._registryIndex);
    o[p++] = ').displayPreviousMonth();"><i class="fa-solid fa-chevron-left"></i></button> ';
  }
  else
    o[p++] = '&nbsp;';
  o[p++] = '</div><div class="month-headline">';
  o[p++] = this._selectableMonths[this._displayedMonthIndex].displayName;
  o[p++] = '</div><div class="month-scroll-button">';
  if (this._displayedMonthIndex < (this._selectableMonthCount - 1))
  {
    o[p++] = ' <button type="button" class="icon-button" onclick="Utility.getInstance(';
    o[p++] = String(this._registryIndex);
    o[p++] = ').displayNextMonth();"><i class="fa-solid fa-chevron-right"></i></button>';
  }
  else
    o[p++] = '&nbsp;';
  o[p++] = '</div></div>';

  return o.join('');
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the selectable month count - that is, the number of months, starting with the month that
// holds today's date, from which a date can be selected.
get selectableMonthCount()
{
  return this._selectableMonthCount;
}

// *************************************************************************************************
// Set the selectable month count - that is, the number of months, starting with the month that
// holds today's date, from which a date can be selected. Setting this value will redraw the
// calendar, if it has already been drawn.
set selectableMonthCount(newValue)
{
  newValue = parseInt(newValue, 10);
  if (isFinite(newValue) && (newValue >= 1))
  {
    this._selectableMonthCount = newValue;
    if (this._displayedMonthIndex >= this._selectableMonthCount)
      this._displayedMonthIndex = this._selectableMonthCount - 1;
    this._generateSelectableMonths();
    this._render();
  }
}

// *************************************************************************************************
// Return the array of names of days. Array indexes 1 to 7 are used for Monday to Sunday, whereas
// index 0 is an empty string.
get dayNames()
{
  return this._dayNames;
}

// *************************************************************************************************
// Set the array of names of days. Array indexes 1 to 7 should hold the names of Monday to Sunday,
// whereas index 0 should be an empty string.
set dayNames(newValue)
{
  if (Array.isArray(newValue) && (newValue.length >= 8))
  {
    this._dayNames = newValue;
    this._render();
  }
}

// *************************************************************************************************
// Return the array of names of months. Index 0 is January and index 11 is December. These names are
// meant to be used as headlines, or at the start of a sentence.
get monthNames()
{
  return this._monthNames;
}

// *************************************************************************************************
// Set the array of names of months. Index 0 should be January and index 11 should be December.
// These names are meant to be used as headlines, or at the start of a sentence.
set monthNames(newValue)
{
  if (Array.isArray(newValue) && (newValue.length >= 12))
  {
    this._monthNames = newValue;
    this._generateSelectableMonths();
    this._render();
  }
}

// *************************************************************************************************
// Return the array of names of months. Index 0 is January and index 11 is December. These names are
// meant to be used as part of a sentence.
get monthNamesInSentence()
{
  return this._monthNamesInSentence;
}

// *************************************************************************************************
// Set the array of names of months. Index 0 should be January and index 11 should be December.
// These names are meant to be used as part of a sentence.
set monthNamesInSentence(newValue)
{
  if (Array.isArray(newValue) && (newValue.length >= 12))
  {
    this._monthNamesInSentence = newValue;
    this._generateSelectableMonths();
    this._render();
  }
}

// *************************************************************************************************
// Return the currently selected date as a string in ISO format ("yyyy-mm-dd"), or null if no date
// is selected.
get selectedDate()
{
  return this._selectedDate;
}

// *************************************************************************************************
// Select the given date. newValue is a string with a date in ISO format ("yyyy-mm-dd"). Update the
// calendar to display the date as selected. Note that the onSelectDate event handler will be
// called. Pass null or an empty string in order to deselect the currently selected date.
set selectedDate(newValue)
{
  if (newValue === '')
    newValue = null;
  if (newValue !== this._selectedDate)
  {
    this._selectedDate = newValue;
    // When used as an event handler, we know the date is currently visible, as you can only select
    // dates within the currently displayed month. Otherwise, redrawing the calendar might not be
    // necessary (but we still do).
    this._render();
    if (this._onSelectDate)
      this._onSelectDate(this, this._selectedDate);
  }
}

// *************************************************************************************************
// Set the event handler function that will be called when a date is selected. Event handler
// signature:
//   function(sender, selectedDate)
// sender is a pointer to this calendar. selectedDate is the selected date as a string in ISO
// format ("yyyy-mm-dd").
set onSelectDate(newEventHandler)
{
  this._onSelectDate = newEventHandler;
}

// *************************************************************************************************
// Return the first date that can be selected as a string in ISO format ("yyyy-mm-dd"), or null if
// today's date is the first date that can be selected.
get firstSelectableDate()
{
  return this._firstSelectableDate;
}

// *************************************************************************************************
// Set the first selectable date. Pass null if today should be the first selectable date. If the
// currently selected date is before the new date, it will be moved.
set firstSelectableDate(newValue)
{
  var actualFirstDate;

  this._firstSelectableDate = newValue;
  actualFirstDate = this.getActualFirstSelectableDate();
  if (this._selectedDate < actualFirstDate)
    this.selectedDate = actualFirstDate;
  else
    this._render();
}

// *************************************************************************************************
// Return the last date that can be selected as a string in ISO format ("yyyy-mm-dd"), or null if
// all dates until the end of the selectable month count can be selected.
get lastSelectableDate()
{
  return this._lastSelectableDate;
}

// *************************************************************************************************
// Set the last selectable date. Pass null if all dates until the end of the selectable month count
// should be selectable. If the currently selected date is after the new date, it will be moved.
set lastSelectableDate(newValue)
{
  var actualLastDate;

  this._lastSelectableDate = newValue;
  actualLastDate = this.getActualLastSelectableDate();
  if (this._selectedDate > actualLastDate)
    this.selectedDate = actualLastDate;
  else
    this._render();
}

// *************************************************************************************************

}
