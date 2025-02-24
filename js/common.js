// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Result code constants.
// *************************************************************************************************
// Result codes that are processed differently from the standard handling.
var result =
  {
    PRODUCT_ALREADY_BOOKED: 3,
    MISSING_NUMBER_PLACEHOLDER_IN_PRODUCT_NAME: 4,
    PASSWORD_CHANGED: 12,
    ASYNCHRONOUS_REQUEST_FAILED: 18,
    PAYMENT_FAILED: 19
  };

// *************************************************************************************************
// *** Column constants.
// *************************************************************************************************
// Object for column constants. This is used to avoid polluting the namespace, and prevent duplicate
// column names with different numerical values.
var c = {};

// locations table column indexes. NAME, STREET, POSTAL_CODE, TOWN, COUNTRY, OPENING_HOURS and
// SERVICES are all strings. ACCESS_CODE is a string, but may not be present on all pages. If the
// user does not have at least one active subscription at that location, the value will be an empty
// string.
c.loc =
  {
    ID: 0,
    NAME: 1,
    STREET: 2,
    POSTAL_CODE: 3,
    TOWN: 4,
    COUNTRY: 5,
    OPENING_HOURS: 6,
    SERVICES: 7,
    ACCESS_CODE: 8
  };

// products table column indexes. SUBSCRIPTIONS is a table with its own column indexes; see the
// c.prs constants below. The END_DATE is only present if the status is CANCELLED or
// CANCELLED_BOOKED; otherwise it will be an empty string. The RESERVED_DATE is only present if the
// status is BOOKED, VACATED_BOOKED or CANCELLED_BOOKED; otherwise it will be an empty string.
c.prd =
  {
    ID: 0,
    NAME: 1,
    LOCATION_ID: 2,
    PRODUCT_TYPE_ID: 3,
    SUBSCRIPTIONS: 4,
    STATUS: 5,
    END_DATE: 6,
    RESERVED_DATE: 7
  };

// subscriptions table column indexes for the subscriptions found in the products table. Note that
// these are different from the subscriptions tables used elsewhere.
c.prs =
  {
    ID: 0,
    USER_ID: 1,
    STATUS: 2,
    START_DATE: 3,
    END_DATE: 4
  };

// subscriptions table column indexes. The PAYMENT_HISTORY may be loaded asyhcnronously when
// required. When it has not yet been loaded, the value will be null. Pages using this table format
// may not need to load products or product types, as the names will be included in the table. They
// do need to load the locations, however. Note that the subscriptions included as part of the
// products table use different column indexes.
c.sub =
  {
    ID: 0,
    NAME: 1, // The name of the product.
    LOCATION_ID: 2,
    PRODUCT_TYPE: 3, // The name of the product type.
    STATUS: 4, // The subscription status. Use the st.sub constants.
    START_DATE: 5,
    END_DATE: 6,
    INSURANCE_NAME: 7,
    PRICE_PLANS: 8,
    PAYMENT_HISTORY: 9,
    // Price plan column indexes:
    PLAN_TYPE: 0, // The type of additional product for which the price plan applies, or -1 if the
                  // price plan applies to the rent.
    PLAN_LINES: 1,
    // Price plan line column indexes:
    LINE_START_DATE: 0,
    LINE_PRICE: 1,
    LINE_DESCRIPTION: 2
  };

// *** // Remove PAYMENT_TYPE. Use Nets specific payment method field.
// *** // PAYMENT_METHOD is now a string.
// *** // PAYMENT_STATUS is now an integer.
// *** // Determine STATUS_COLOUR locally. It no longer comes from the server.
// Payment history column indexes. PAYMENT_TYPE is an integer. Use the PAYMENT_TYPE constants.
c.pay =
  {
    ID: 0,
    NAME: 1,
    ORDER_DATE: 2,
    PAY_BY_DATE: 3,
    PAYMENT_METHOD: 4,
    PAYMENT_STATUS: 5,
    PAYMENT_INFO: 6,
    ORDER_LINES: 7,
    OPEN: 8,
    // ORDER_LINES column indexes:
    LINE_ID: 0,
    LINE_TEXT: 1,
    LINE_AMOUNT: 2
  };

// productTypes table column indexes.
c.typ =
  {
    ID: 0,
    NAME: 1,
    PRICE: 2,
    CATEGORY_ID: 3
  };

// categories table column indexes.
c.cat =
  {
    ID: 0,
    NAME: 1
  };

// users table column indexes.
c.usr =
  {
    ID: 0,
    NAME: 1,
    EMAIL: 2,
    PHONE: 3,
    HAS_ACTIVE_SUBSCRIPTION: 4
    // ROLE_ID: 5 // The ID of the database element that connects the user to the user group.
  };

// Available product types table column indexes.
// - ID is the product type ID.
// - NAME is the product type name.
// - IS_AVAILABLE is a boolean value that says whether that particular product type is available at
//   the selected location, from the selected date.
// - If the product type is not available, ALTERNATIVE_LOCATION_IDS is an array of IDs of the
//   other locations that have available products of this type.
// - If the product type is not available, FIRST_AVAILABLE_DATE is the date, as a string in the
//   format "yyyy-mm-dd", on which the product type becomes available for rent.
// - AVAILABLE_PRODUCT_IDS is an array of IDs of products in this category that are free and can be
//   booked. The user will only need to book one of them, but since somebody else might be using the
//   system, the first product in the list may be unavailable by the time he has finished the
//   booking process. Therefore, a few options are provided, if available. If the product type is
//   unavailable, the array will be empty.
c.apt =
  {
    ID: 0,
    NAME: 1,
    PRICE: 2,
    CATEGORY_ID: 3,
    IS_AVAILABLE: 4,
    ALTERNATIVE_LOCATION_IDS: 5,
    FIRST_AVAILABLE_DATE: 6,
    AVAILABLE_PRODUCT_IDS: 7
  };

// licencees table column indexes.
c.lic =
  {
    ID: 0,          // The ID of the item that connects the user group and the licence.
    NAME: 1,        // The name of the user group.
    USER_GROUP_ID: 2,
    IS_ACTIVE: 3,
    LICENCE_ID: 4   // The ID of the "Minilager" licence entry.
  };

// userGroups table column indexes.
c.ugr =
  {
    ID: 0,          // The ID of the user group.
    NAME: 1         // The name of the user group.
  };

// insurances table column indexes.
//   FOR_PRODUCT_TYPES            Array of IDs from the productTypes table, or null - which means
//                                "all of them".
//   FOR_LOCATIONS                Array of IDs from the locations table, or null - which means "all
//                                of them".
c.ins =
  {
    ID: 0,
    NAME: 1,
    DESCRIPTION: 2,
    PRICE: 3,
    FOR_PRODUCT_TYPES: 4,
    FOR_LOCATIONS: 5
    // MUTUALLY_EXCLUSIVE_WITH: 6
  };

// capacityPriceRules and specialOfferPriceRules table column indexes.
//   PRICE_MODS                   Array of modifications to prices. Each entry is an array with the
//                                items described below. The items differ or capacity and special
//                                offer price rules.
//   FOR_LOCATIONS                Array of location IDs for which this offer should apply, or null
//                                if it applies everywhere. An empty array will apply nowhere.
//   FOR_PRODUCT_TYPES            Array of product type IDs for which this offer should apply, or
//                                null if it applies to all of them. An empty array applies to none.
//   OPEN                         Always false.
//
// For capacity price rules, the PRICE_MODS table has the following columns:
//   PRICE_MOD      The percentage change in prices. -10 is a 10% discount.
//   MIN_CAPACITY   The minimum capacity which will give this price modification. If capacity is
//                  equal to or greater than this number, the modification will apply.
//   MAX_CAPACITY   The maximum capacity which will give this price modification. If capacity is
//                  less than this number, the modification will apply. We don't need to reach
//                  exactly 100%, because then the warehouse is full anyway, and nothing can be
//                  rented.
//
// For special offer price rules, the PRICE_MODS table has the following columns:
//   PRICE_MOD      The percentage change in prices. -10 is a 10% discount.
//   DURATION       The number of months that this change will last, or -1 if the change applies
//                  indefinitely.
c.pru =
{
  ID: 0,
  NAME: 1,
  START_DATE: 2,
  END_DATE: 3,
  PRICE_MODS: 4,
  FOR_PRODUCT_TYPES: 5,
  FOR_LOCATIONS: 6,
  OPEN: 7,
  // PRICE_MODS column indexes:
  PRICE_MOD: 0,
  MIN_CAPACITY: 1,
  MAX_CAPACITY: 2,
  DURATION: 1
}

// *************************************************************************************************
// *** Subscription and product status onstants.
// *************************************************************************************************
// Object for status constants. This is used to avoid polluting the namespace, and prevent duplicate
// constants with different numerical values. It should be called "status", but that is already
// defined by... something, and doesn't work.
var st = {};

// Individual subscription status constants.
//                              Start date                    End date
// - Finished                   Before today's date           Exists; before today's date
// - Ongoing                    Before today's date           Does not exist
// - Cancelled                  Before today's date           Exists; after today's date
// - Booked                     After today's date            Who cares?
st.sub =
  {
    EXPIRED: 0,
    ONGOING: 1,
    CANCELLED: 2,
    BOOKED: 3
  };

st.sub.COLOURS =
  [
    'status-red',
    'status-green',
    'status-blue',
    'status-green'
  ];

// Product status constants.
st.prod =
  {
    NEW: 0,
    VACATED: 1,
    BOOKED: 2,
    VACATED_BOOKED: 3,
    RENTED: 4,
    CANCELLED: 5,
    CANCELLED_BOOKED: 6
  };

st.prod.COLOURS =
  [
    'status-red',
    'status-red',
    'status-green',
    'status-green',
    'status-green',
    'status-blue',
    'status-green'
  ];

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// The minimum number of characters in a password.
var PASSWORD_MIN_LENGTH = 8;

// The colours to use for various payment statuses.
var PAYMENT_STATUS_COLOURS =
  [
    'status-blue',
    'status-red', 
    'status-green', 
    'status-blue',
    'status-red', 
    'status-red', 
    'status-red', 
    'status-red', 
    'status-green', 
    'status-red', 
    'status-green', 
    'status-red', 
    'status-red', 
    'status-green', 
    'status-red'
  ];

// Additional product type constants.
var ADDITIONAL_PRODUCT_INSURANCE = 1;

// Application role constants.
var APP_ROLE_PRODUCTION = 'production';
var APP_ROLE_EVALUATION = 'evaluation';
var APP_ROLE_TEST = 'test';

// *************************************************************************************************
// *** Fields.
// *************************************************************************************************
// Component instances are stored in a registry, accessible through a global variable. This is so
// that event handlers can access their parent component. Use Utility.registerInstance to add a
// component instance to the registry and receive the index that can be used to access it. Use
// Utility.getInstance to retrieve it.
var _instanceRegistry = [];

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Return a text from the texts array that should be provided by the server. If the text was not
// found, return the defaultValue instead. Any occurrences of $1, $2, ... $n in the string will be
// replaced with any additional data provided to this function. data is an array where the first
// entry corresponds to $1, the second to $2, and so on.
function getText(index, defaultValue, data)
{
  var text;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, texts))
    text = texts[index];
  else
    text = defaultValue;

  if (data)
    return Utility.expandText(text, data);
  return text;
}

// *************************************************************************************************

function toggleCurrentUserMenu()
{
  var userMenu;
  
  userMenu = document.getElementById('currentUserMenu');
  if (userMenu)
  {
    if (userMenu.style.height === '260px')
    {
      userMenu.style.height = '0';
      setTimeout(function ()
        {
          Utility.hide(userMenu);
        }, 320);
    }
    else
    {
      userMenu.style.height = '0';
      Utility.display(userMenu);
      setTimeout(function ()
        {
          userMenu.style.height = '260px';
        }, 10)
    }
  }
}

// *************************************************************************************************

function submitLanguageSelection()
{
  var form;

  form = document.getElementById('selectLanguageForm');
  if (form)
    form.submit();
}

// *************************************************************************************************
// Switch to the user group with the given ID. If id is invalid, nothing will happen.
function setUserGroup(id)
{
  id = parseInt(id, 10);
  id = Utility.getValidInteger(id, -1);
  if (id === -2)
    window.location.href = '/subscription/html/set_user_group.php';
  else
    if (id >= 0)
      window.location.href = '/subscription/html/set_user_group.php?role_id=' + String(id);
}

// *************************************************************************************************
// *** class Utility
// *************************************************************************************************

class Utility
{

// *************************************************************************************************
// *** Static methods.
// *************************************************************************************************
// Render the given HTML element visible. You can pass the ID of the element, or the element itself.
static display(element)
{
  if (typeof element === 'string')
    element = document.getElementById(element);
  if (element)
    element.style.display = '';
}

// *************************************************************************************************
// Render the given HTML element invisible. You can pass the ID of the element, or the element
// itself.
static hide(element)
{
  if (typeof element === 'string')
    element = document.getElementById(element);
  if (element)
    element.style.display = 'none';
}

// *************************************************************************************************
// Toggle the visibility of the given HTML element.
static toggle(element)
{
  if (element)
  {
    if (element.style.display === 'none')
      element.style.display = '';
    else
      element.style.display = 'none';
  }
}

// *************************************************************************************************
// Locate the HTML elements given in the ids table, and store them in global variables with the same
// names. ids should be an array of strings. If it contains ['itemA', 'itemB'], the method will look
// for HTML elements with IDs "itemA" and "itemB", and store them in global variables "itemA" and
// "itemB".
static readPointers(ids)
{
  var i;

  if (Array.isArray(ids))
  {
    for (i = 0; i < ids.length; i++)
      window[ids[i]] = document.getElementById(ids[i]);
  }
}

// *************************************************************************************************
// Store the given component instance in the registry, and return the index, which can be used to
// access it later, using the getInstance method. Return -1 if the component instance could not be
// registered.
static registerInstance(instance)
{
  var index;

  if (instance)
  {
    index = _instanceRegistry.length;
    _instanceRegistry[index] = instance;
    return index;
  }
  return -1;
}

// *************************************************************************************************
// Return the component instance with the given index in the component instance registry, or null if
// it could not be found.
static getInstance(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, _instanceRegistry))
    return _instanceRegistry[index];
  return null;
}

// *************************************************************************************************
// Extract JSON data from the given HTTP response object. Return a promise to provide the JSON once
// it has been parsed. You can either do "await" to pause until the JSON data is available, or add a
// callback using the "then" method.
static extractJson(response)
{
  if (!response.ok)
    throw new Error('Error parsing HTTP response: ' + response.status + ' ' + response.statusText);
  return response.json();
}

// *************************************************************************************************

static expandText(text, data)
{
  var regExp, getReplacementValue;

  // Use a regular expression to match placeholders like $1, $2, etc.
  regExp = /\$(\d+)/g;
  // Use a function that parses the matched "$n" strings to determine the index of the text to
  // insert.
  getReplacementValue = function (match, captureGroup)
    {
      var index;

      index = parseInt(captureGroup, 10);
      if (isFinite(index))
      {
        index--;
        if ((index >= 0) && (index < data.length))
          return data[index];
      }
      return match;
    };

  // Replace each placeholder with the corresponding data value.
  return String(text).replace(regExp, getReplacementValue);
}

// *************************************************************************************************
// Return today's date as an ISO date string, in the format "yyyy-mm-dd".
static getCurrentIsoDate()
{
  return Utility.getIsoDate(new Date());
}

// *************************************************************************************************
// Return a date as an ISO date string, in the format "yyyy-mm-dd". You can either pass a single
// Javascript Date object, or integers for year, month and day. In the latter case, the month should
// be in the Javascript format, which is zero based.
static getIsoDate(year, month, day)
{
  var date;

  // Convert from a Javascript Date to year, month and day figures, if required.
  if (year && !month && !day)
  {
    date = year;
    year = date.getFullYear();
    month = date.getMonth();
    day = date.getDate();
  }

  // Format the date.
  return Utility.pad(year, 4) + '-' + Utility.pad(month + 1, 2) + '-' + Utility.pad(day, 2);
}

// *************************************************************************************************
// Given a date, in the format "yyyy-mm-dd", return the previous day in the same format.
static getDayBefore(date)
{
  date = new Date(date);
  date.setDate(date.getDate() - 1);
  return Utility.getIsoDate(date);
}

// *************************************************************************************************
// Return a date, in the format "yyyy-mm-dd", that represents the last day of the given year and
// month. You can either pass a single Javascript Date object, or integers for year and month. In
// the latter case, the month should be in the Javascript format, which is zero based.
static getLastDay(year, month)
{
  var date, nextMonth;

  // Convert from a Javascript Date to year, month and day figures, if required.
  if (year && !month)
  {
    date = year;
    year = date.getFullYear();
    month = date.getMonth();
  }

  nextMonth = new Date(year, month + 1, 1);
  return Utility.getIsoDate(new Date(nextMonth - 1));
}

// *************************************************************************************************
// Pad the given number with leading zeroes, to the given length. Return the padded number as a
// string.
static pad(number, length)
{
  number = String(number);
  while (number.length < length)
    number = '0' + number;
  return number;
}

// *************************************************************************************************
// Limit the given text to the given maximum length. If the text is longer, the final character will
// be replaced by an ellipsis character (...).
static curtail(text, length)
{
  text = String(text);
  if (text.length > length)
    return text.substring(0, length - 1) + '&hellip;';
  return text;
}

// *************************************************************************************************
// If the given source string is, or can be converted to, a non-empty string, return that string. If
// not, return the default value.
static getValidString(source, defaultValue)
{
  if ((typeof source === 'undefined') || (source === null))
    return defaultValue;
  source = String(source);
  if (source === '')
    return defaultValue;
  return source;
}

// *************************************************************************************************

static getValidInteger(source, defaultValue)
{
  source = parseInt(source, 10);
  if (isFinite(source))
    return source;
  return defaultValue;
}

// *************************************************************************************************

static getPositiveInteger(source, defaultValue)
{
  source = parseInt(source, 10);
  if (isFinite(source) && (source >= 0))
    return source;
  return defaultValue;
}

// *************************************************************************************************

static isValidIndex(index, array)
{
  index = parseInt(index, 10);
  return isFinite(index) && (index >= 0) && Array.isArray(array) && (index < array.length);
}

// *************************************************************************************************

static valueInArray(value, array)
{
  var i;

  if (Array.isArray(array))
  {
    for (i = 0; i < array.length; i++)
    {
      if (array[i] === value)
        return true;
    }
  }
  return false;
}

// *************************************************************************************************
// Make the array entries at the two given indexes switch positions.
static switchArrayEntries(array, index1, index2)
{
  var entry;

  index1 = parseInt(index1, 10);
  index2 = parseInt(index2, 10);
  if (Utility.isValidIndex(index1, array) && Utility.isValidIndex(index2, array))
  {
    entry = array[index1];
    array[index1] = array[index2];
    array[index2] = entry;
  }
}

// *************************************************************************************************

static getAddress(location)
{
  var o, p;

  o = new Array(5);
  p = 0;

  o[p++] = location[c.loc.STREET];
  o[p++] = ', ';
  o[p++] = location[c.loc.POSTAL_CODE];
  o[p++] = ' ';
  o[p++] = location[c.loc.TOWN];
  o[p++] = ', ';
  o[p++] = location[c.loc.COUNTRY];
  return o.join('');
}

// *************************************************************************************************
// Return the name of the location with the given ID, or the given default value if no such location
// was found. defaultValue is optional. If not provided, the function returns a non-breaking space.
// A table named "locations" is assumed to exist.
static getLocationName(id, defaultValue)
{
  var i;
  
  for (i = 0; i < locations.length; i++)
    if (locations[i][c.loc.ID] === id)
      return locations[i][c.loc.NAME];
  if (typeof defaultValue === 'undefined')
    return '&nbsp;';
  return defaultValue;
}

// *************************************************************************************************
// Return the display name of the product type with the given ID, or or a non-breaking space if no
// such product type was found. A table named "productTypes" is assumed to exist.
static getProductTypeName(id)
{
  var i;
  
  for (i = 0; i < productTypes.length; i++)
    if (productTypes[i][c.typ.ID] === id)
      return productTypes[i][c.typ.NAME];
  return '&nbsp;';
}

// *************************************************************************************************
// Return the index in the subscriptions table of the subscription with the given ID, or -1 if it
// was not found. A table named "subscriptions" is assumed to exist.
static getSubscriptionIndex(id)
{
  var i;

  for (i = 0; i < subscriptions.length; i++)
    if (subscriptions[i][c.sub.ID] === id)
      return i;
  return -1;
}

// *************************************************************************************************
// Return the index in the products table of the product with the given ID, or -1 if it was not
// found. A table named "products" is assumed to exist.
static getProductIndex(id)
{
  var i;

  for (i = 0; i < products.length; i++)
    if (products[i][c.prd.ID] === id)
      return i;
  return -1;
}

// *************************************************************************************************
// Return true if the user, if he cancels a subscription right now, will continue the subscription
// until the end of this month only. If the method returns false, he will have to continue the
// subscription until the end of next month.
//
// A subscription can be cancelled at the end of this month if the current date is the 15th of the
// month, or earlier. The method depends on the customer's time being set correctly, so it may be
// wrong. However, the server has the final word on when the subscription will end.
static canCancelThisMonth()
{
  var currentDate;

  currentDate = new Date().getDate();
  return currentDate <= 15;
}

// *************************************************************************************************

}
