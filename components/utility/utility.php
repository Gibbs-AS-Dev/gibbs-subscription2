<?php
// *********************************************************************************************************************
// *** Bugs.
// *********************************************************************************************************************
// - Bug: Items were inserted into the database twice. How on earth did that happen? Did the user
//   click twice? Try disabling the submit button by displaying an overlay whenver a submit button
//   is clicked? The button should not be a submit button, and should run a function when clicked.
//   CTRL U (used to display code) will cause the page URL to be reloaded. Is this what happened?
// - Bug: The user was logged out while doing... something with locations and products?

// *********************************************************************************************************************
// *** To do.
// *********************************************************************************************************************
// - In the Tabset, add a clickMode property to say how clicks should be handled. All; before current step; previous
//   step.
// - In the Tabset, document all the properties.
// - In the Tabset, add an event handler to respond to current tab changes. Same as Calendar.
// - Add book_subscription.js SELECTABLE_MONTH_COUNT to settings.
// - See if payment history code can be extracted into a separate component. Just pass a table of translations, like we
//   do with the calendar.
// - In the calendar, perform error checking on all dates. Add Utility method, if we don't have one.
// - Ensure the order of product types and locations is the same for insurance and price rules.

// - When editing price mods, do not rewrite the table when edit box values change. Otherwise, focus will be lost. On
//   the other hand, rewrite the table when mods are moved, deleted, added or sorted.
// - Create Price_Rules_Data_Manager.
// - Read price rule data from the server.
// - Create price rules.
// - Update price rules.
// - Delete price rules.
// - Add status column to price rule tables. Test today's date against start and end dates on the server, and set a
//   boolean value. Completed, ongoing, not started.
// - When editing special offer price rules, add a check box that says whether to display completed campaigns (where the
//   end date is before today's date).
// - Use the price rules to create price plans.
// - Display discounts.

// - Implement bulk charge operation. Trigger it from the Gibbs admin interface to begin with.
// - Ensure the payment error handling catches everything. Ensure subscriptions and orders are always deleted. Display
//   suitable error messages.
// - If the user has paid for both the current month and the next, and then tries to cancel, cancel the subscription at
//   the end of next month. Update the user interface prompts to reflect the date. This only applies when the
//   subscription has just been booked.
// - The code to calculate prices should take into account the subscription end date, if present. Both on the server and
//   on the client.
// - In the payment history, write the price for each order line as a button which displays the price plan dialogue.
//   Is there a way to highlight the line which was used? Return to the payment history when closed.
// - In data managers, use $this->id_db_name wherever appropriate. Don't hard code the ID in the SQL.
// - Subscription_Data_Manager.create_subscription_from_list will return Result::PRODUCT_ALREADY_BOOKED, even if the
//   products could not be booked due to other errors. Store the other result codes. If they are all the same, return
//   that result code instead.
// - When displaying the "please wait" text for payment history, include the dialogue box header and footer.
// - Update payment history dummy data, to ensure it works with the new format.
// - Find payment method for Nets payments. Display in payment history for users and admins.
// - Use constants for payment status. When a new value is set, ensure it is valid.
// - How do we mark an order as overdue? Run code once a day?
// - Allow the user to switch to a different insurance type from the dashboard?
// - Allow the administrator to change the insurance from the admin_edit_user page.
// - Insurance products should include an order integer, so an admin can ajust the sorting.
// - Display the tabset on the payment page. At the moment it disappears when you get that far.
// - Use $wpdb->prepare for all queries where user-supplied information is used. The sanitisation of input parameters
//   may not be enough to prevent SQL hacks.
// - Add a <form> around all buttons. For instance in book_subscription.php, tabset buttons. Which attributes have to be
//   present? 
// - Use different dialogue boxes for creating and editing products, to have different maximum heights.
// - Move admin_ files to admin directory. Remove prefix.
// - Move Gibbs admin files to gibbs directory. Remove prefix.
// - Purge all role numbers from the code. Use the ROLE_ constants instead. Convert wherever the
//   database is manipulated.
// - Ensure data managers convert tinyints and other odd stuff to numbers before returning them, so
//   users don't have to deal with unexpected strings.
// - When calculating subscription price on the client, get the current date from the server, so we
//   know the calculation is correct.
// - Allow company admins to select which currency to use.
// - Theoretically support several payment providers.
// - Add application version constant in PHP. Pass it as a parameter when loading CSS and JS files.
// - Implement storing user information on the admin_edit_user page.
// - Implement creating new users on the admin_edit_user page.
// - Implement creating subscriptions on behalf of users on the admin_edit_user page. How to
//   integrate with payment?
// - Add database tables for blacklists.
// - Implement admin page for blacklisted users.
// - Display information about credit checks somewhere. Where? Booking summary?
// - Allow users to book without registering first. Registration should then be a page in the
//   tabset.
// - Search for an address, and sort the locations according to distance.
// - Sort locations according to distance when displaying alternate locations during booking.
// - Display all locations in a single map when booking subscriptions.
// - Implement the varous widgets on the admin dashboard page.
// - Display error message when login fails. Don't redirect to the login page with the error as a
//   parameter. This only happens when the error comes from Wordpress.
// - When creating a test subscription, use the Role_Data_Manager to asynchronously load the list of
//   eligible users. Have the user select from the list, rather than just specify an ID.
// - Always have access control when using a data manager. Even when reading, or calling methods
//   directly. Pass an action to perform_action?
// - Once we have complete access control in data managers: Verify that access control works on
//   index.php. Can we read the list of customers without being logged in?
// - In the book_subscription tabset, have a separate line to display what has been selected.
// - In the sales summary, display checkbox for accepting terms and conditions? Link to a separate
//   page for each storage company?
// - Call can_create in Product_Data_Manager.create_multiple.
// - Allow an admin to search for products that are free in a given time interval?

// - Do not cancel a subscription if the subscription already has and end date, or if the start date
//   is after today's date.
// - The ROLE_ constants do not use the same numbers as in the database. Fix that? Use the constants
//   in Role_Data_Manager and calls to it.
// - Should the Role_Data_Manager provide actions? Or are they not needed?
// - Implement payment history on the admin_edit_user page.
// - Mark "most wanted response" button with colour #008474.
// - When a user logs in, set the language based on the locale setting in his user information.
// - When a user switches languages, write the setting to the locale property.
// - When a new user has been registered, if a group_id were passed, use that to determine the
//   initial page. We can't have a customer registering with company B, an be taken to the home page
//   of company A.
// - Find the proper way to identify a Gibbs admin in user_has_role.
// - Test the changes to the user menu. Select a user group.
// - Create a User_Group_Data_Manager. Move the get_user_groups method from User_Data_Manager.
// - Use constants for role numbers.
// - Put an overlay under the drop-down user menu. Close it when the user clicks elsewhere.
// - Ensure that verify_is_user is called on all pages that require login. What do we do in
//   set_language.php? Should verify_is_user return true if the user is an admin? Or should we have
//   a user_has_permissions method?
// - Read the initial page for a user from user_metadata.
// - Store the initial page for a user to user_metadata when he switches using the drop-down menu.
// - Allow the user to pass a user group and role when logging in, in case the previous initial page
//   is no longer accessible. That way, the user can be redirected to the appropriate page
//   immediately.
// - Store all asynchronously loaded test data in the Test_Data_Manager class. Do not load from
//   static files. Instead use PHP with a reference to Test_Data_Manager.
// - When adding or editing locations, fill in the town automatically, if it is empty and the postal
//   code returns a hit. Set "Norge" as default as well.
// - Edit the postal code in a shorter box. Place the town edit box next to it.
// - Read filter parameters properly. The sanitize_ function is probably not enough to stop code
//   injection.
// - Centralise column constant declarations in common.js.
// - A subscription can have several statuses: booked but not started, running, running but
//   cancelled from some future date, stopped. The "active" field in the database cannot represent
//   all of these. Nor does our user interface consider it. We cannot determine the status on the
//   client, as we don't know whether the time and date is set correctly. Therefore, determine it
//   on the server and pass it to the client. Drop the "active" column from the database.
// - When creating test subscriptions, ensure that the dates do not overlap existing subscriptions.
// - When creating test subscriptions, have a checkbox to decide whether the end date field is
//   available.
// - When creating test subscriptions, use a calendar to select start and end dates.
// - Settings page that clients can access. Set things like how long in advance you can book, how
//   many characters a password need to have, etc. Also set the CSS file to use (and allow the
//   client to upload and edit it). Choose which file names to use for each file, so that we can
//   customise individual files for each client.
// - Ensure dialogue boxes always appear in the centre of the screen, even if the page behind is
//   scrolled.
// - Translate all remaining pages.
// - Add page titles to translations.
// - When an administrator creates a new user, don't immediately log in as the new user.
// - On pages with several dialogue boxes, ensure IDs of form elements are unique.
// - Add a button to show that the current user menu can be opened and closed.
// - Make the language icons work.
// - When adding a subscription on the admin_edit_user page, return to the admin interface, not the
//   user dashboard. Ensure the sidebar navigation is visible. Use same JS, but different HTML?
//   Or just implement it in PHP.
// - Image gallery for locations. Carousel. View images in full size when clicked.
// - Polish the size of all dialogue boxes.
// - When the user clicks a disabled menu item in the main menu, display an alert box to say what he
//   needs to do to get to that page.
// - Fix all // *** // notes.
// - Add more commments wherever required.
// - Display which fields are mandatory.
// - When the user clicks a disabled button, display an alert that says why he could not click?
// - Green check mark next to each box, to say whether it's valid or not. If it isn't, display a
//   text that says why.
// - Validate phone numbers: "+" <digits> <space> <parentheses>
// - When creating a new location, display an error if an existing location has the same name.
// - When creating products, give an error message after the fact if one or more new locations had
//   the same name as an existing location. List the locations that were not created.
// - When creating locations, if you have selected the batch options, disable the submit button if
//   you enter a text in the numeric edit boxes, or if the numbers are outside the allowed range.
//   Also disable the submit button if the numeric edit boxes are empty.
// - Whenever a request to the server is submitted, display an overlay and a progress bar, to let
//   the user know something is happening.
// - Select countries from a list. Have one list in Norwegian, another with the same countries in
//   English, and store the index in the list, rather than the country itself. Filter function when
//   selecting.
// - When adding an ellipsis to a long string in Utility.curtail, count character entities as a
//   single character. Don't break the string in the middle of a character entity.
// - Add a means of allowing the user to translate services and opening hours into different
//   languages.
// - Add a list of services, and corresponding icons. When specifying services for a location,
//   choose from the list. Allow the user to translate the texts.

// Gibbs administrator interface:
// - Display inactive subscriptions. Option to delete them.
// - Add an association between a user and a user group. Assign a role. Create Gibbs admin UI to do
//   so. Do it from the licencees page? Or create a subpage with list of users for a particular
//   user group?
// - Once we have a UI to change roles, disable creating administrators through the registration
//   page.
// - Add a left menu for Gibbs admin.
// - Create test data for the Gibbs admin pages.
// - On the Gibbs admin pages, we need to write a link to the edit_user and set_language pages, but
//   we don't have a group ID. Ensure that this works.
// - In gibbs_licencees.php, create a user group that has the "Minilager" licence. Also create a
//   dummy user to go with it. Can we use the dummy user's meta table to store the settings? Such as
//   application role and whether to use hard coded data?
// - Translate the Gibbs admin interface.

// In PHP:
// - When creating, updating or deleting items, submit an asynchronous request. Update the local
//   data table based on the server response, to avoid having to reload the entire page.
// - When a form is read, and found to contain errors, fill in the relevant data fields with the
//   data that was posted, so the user won't have to fill it all in again. Don't fill in passwords,
//   though.
// - Test the code that rejects a new password if it contains illegal characters.
// - admin_dashboard.js is not currently used. Delete it? We'll need it later.
// - Add the currently logged in user's information to the top right drop-down menu.
// - Implement PHP to provide and receive data everywhere.
// - Test that the server-side validation of e-mail addresses works.

// On the admin_edit_user page:
// - Decide whether to call it "user name" or "e-mail". Don't use both.
// - When a new password is set, also allow the admin to specify that the customer must change it
//   the next time he logs in.

// On the admin_products page:
// - When you deselect all items from the filter, treat that as if the user wants to delete the
//   filter.
// - Add a scrolling div to the location and product type filter dialogues. Ensure they use the
//   entire box, and add a scroll bar when required.
// - Perform error checking on filter parameters, to ensure that only valid IDs are passed. Don't
//   require the brackets to be passed as part of the parameter.
// - Display counter that says how many products satisfy the current filter criteria, and how many
//   are currently selected.
// - When the batch create function is selected, perform validation before enabling the submit
//   button.
// - Download the entire subscription history.
// - Determine current status and dates on the client based on the subscription history.
// - Option to display the entire subscription history in a dialogue box, with links to customers.
// - Buttons and dialogue boxes to filter on name.
// - Add the display customer button icons to our FontAwesome kit.
// - In the edit filter dialogue, ensure the elements are properly adjusted vertically.

// On the admin_product_types page:
// - Before deleting a product type, ask the server if any products of that type exist. If so,
//   inform the user that the operation cannot be performed.
// - When creating a product type, disable the submit button if a product type with the given name
//   already exists? Can we have duplicates? Verify that the price is a float. Or an integer?

// On the user_dashboard page:

// On the book_subscription page:
// - Configuration option to select only category, or category and product.
// - Distinguish between storage rooms that are simply busy, and ones that don't exist at all at
//   the selected location.
// - For non-available products, add a button to change the date or location of the search.
// - Click on a finished step to go to that step.
// - Add indication of where disabled products might be found.
// - Mark Norwegian bank holidays in red.
// - Display locations as a list, like the list of products? Have a scrolling box, in case there are
//   too many locations.
// - Make the month headline into a combo box? No need, since there aren't that many months.

// On the admin_locations page:
// - Display and edit extra information about the location: opening hours, services.
// - Select icons for each service (somehow). Display when booking subscriptions. Display on the
//   user dashboard.

// *********************************************************************************************************************
// *** Done.
// *********************************************************************************************************************
// - Fix description and parameters of Utility.getLastDay.
// - Ensure the session_start() call is in all files that load wp-load.php.

// *********************************************************************************************************************

// Load WordPress core.
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

// Class to hold result codes and errors that might result from various operations, such as posting data to the server.
class Result
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // If these are changed, also modify the corresponding constants in common.js.
  public const NO_ACTION_TAKEN = -2;
  public const OK = -1;
  public const MISSING_INPUT_FIELD = 0;
  public const DATABASE_QUERY_FAILED = 1;
  public const NOT_IMPLEMENTED = 2;
  public const PRODUCT_ALREADY_BOOKED = 3;
  public const MISSING_NUMBER_PLACEHOLDER_IN_PRODUCT_NAME = 4;
  public const DATABASE_QUERY_FAILED_PARTIALLY = 5;
  public const WORDPRESS_ERROR = 6;
  public const INVALID_PASSWORD = 7;
  public const PASSWORD_TOO_SHORT = 8;
  public const INVALID_EMAIL = 9;
  public const EMAIL_EXISTS = 10;
  public const USER_NOT_FOUND = 11;
  public const PASSWORD_CHANGED = 12;
  public const INVALID_ACTION_HANDLER = 13;
  public const INCORRECT_APPLICATION_ROLE = 14;
  public const ACCESS_DENIED = 15;
  public const LICENCE_EXPIRED = 16;
  public const PRODUCT_NOT_FOUND = 17;
  public const ASYNCHRONOUS_REQUEST_FAILED = 18;
  public const PAYMENT_FAILED = 19;
  public const UNABLE_TO_CREATE_ORDER = 20;

  // *******************************************************************************************************************
  // *** Static methods.
  // *******************************************************************************************************************

  public static function is_error($result_code)
  {
    return $result_code >= 0;
  }

  // *******************************************************************************************************************
}

// Class which holds information about the user's access level: his role, and the user group in which he has that role.
class Access_Token
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The ID of the user group for which data is being manipulated.
  protected $user_group_id = -1;

  // The role of the user using this class. Some data managers may restrict access to certain actions to certain roles.
  protected $role = Utility::ROLE_USER;

  // A result code that can be used to report any errors encountered when the access token was created. Often, if an
  // error occurs, the server will simply redirect the client to an error page, and this result code will not be used.
  // However, if the client made an asynchronous request, the client must receive a result code, not HTML from an error
  // page. This result code can be used for that purpose.
  protected $result_code = Result::OK;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_user_group_id, $new_role, $result_code = Result::OK)
  {
    if (is_numeric($new_user_group_id))
    {
      $new_user_group_id = intval($new_user_group_id);
      if ($new_user_group_id >= 0)
      {
        $this->user_group_id = $new_user_group_id;
      }
    }

    if (is_numeric($new_role))
    {
      $new_role = intval($new_role);
      if (Utility::is_valid_role($new_role))
      {
        $this->role = $new_role;
      }
    }

    if (is_numeric($result_code))
    {
      $this->result_code = intval($result_code);
    }
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************

  public function is_error()
  {
    return Result::is_error($this->result_code);
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_user_group_id()
  {
    return $this->user_group_id;
  }

  // *******************************************************************************************************************

  public function get_role()
  {
    return $this->role;
  }

  // *******************************************************************************************************************

  public function get_result_code()
  {
    return $this->result_code;
  }

  // *******************************************************************************************************************
}

class Utility
{
  // *******************************************************************************************************************
  // *** Subscription and product status constants.
  // *******************************************************************************************************************
  // Individual subscription status constants.
  //                              Start date                    End date
  // - Finished                   Before today's date           Exists; before today's date
  // - Ongoing                    Before today's date           Does not exist
  // - Cancelled                  Before today's date           Exists; after today's date
  // - Booked                     After today's date            Who cares?
  public const SUB_EXPIRED = 0;
  public const SUB_ONGOING = 1;
  public const SUB_CANCELLED = 2;
  public const SUB_BOOKED = 3;

  // Product status constants.
  public const STATUS_NEW = 0;
  public const STATUS_VACATED = 1;
  public const STATUS_BOOKED = 2;
  public const STATUS_VACATED_BOOKED = 3;
  public const STATUS_RENTED = 4;
  public const STATUS_CANCELLED = 5;
  public const STATUS_CANCELLED_BOOKED = 6;

  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // The location of the Nets payment API when the application_role is "production".
  public const NETS_API_URL_PROD = '';

  // The location of the Nets payment API when the application_role is not "production".
  public const NETS_API_URL_TEST = 'https://test.api.dibspayment.eu/v1/payments';

  // The location of the Javascript file for payment with Nets, when the application_role is "production".
  public const NETS_JS_URL_PROD = '';

  // The location of the Javascript file for payment with Nets, when the application_role is not "production".
  public const NETS_JS_URL_TEST = 'https://test.checkout.dibspayment.eu/v1/checkout.js?v=1';

  // User role constants.
  public const ROLE_NONE = -1;
  public const ROLE_USER = 0;
  public const ROLE_COMPANY_ADMIN = 1;
  public const ROLE_GIBBS_ADMIN = 2;

  // The role numbers stored in the database. Not to be confused with roles.
  public const ROLE_NUMBER_USER = 1;
  public const ROLE_NUMBER_LOCAL_ADMIN = 2;
  public const ROLE_NUMBER_COMPANY_ADMIN = 3;
  public const ROLE_NUMBER_GIBBS_ADMINISTRATOR = 6;

  // The role ID used for a Gibbs administrator dummy role. These roles are not in the database, but the Gibbs
  // administrator has access anyway.
  public const ROLE_ID_GIBBS_ADMINISTRATOR = -2;

  // verify_fields result constants.
  public const ALL_EMPTY = 0;
  public const SOME_PRESENT = 1;
  public const ALL_PRESENT = 2;

  // Supported language constants.
  public const NORWEGIAN = 'nb_NO';
  public const ENGLISH = 'en_GB';
  public const DEFAULT_LANGUAGE  = self::NORWEGIAN;
  // "A locale is a combination of language and territory (region or country) information."
  // Examples of locales are: nb_NO, nn_NO, sv_SE, da_DK, en_GB, en_US, de_DE
  // The language codes are found here: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
  // See also: https://stackoverflow.com/questions/3191664/list-of-all-locales-and-their-short-codes

  // The maximum number of digits that can be used when padding a storage room number. The minimum is 1.
  public const MAX_PADDING_DIGIT_COUNT = 10;

  // The minimum length of a password.
  public const PASSWORD_MIN_LENGTH = 8;

  // Payment method constants.
  public const PAYMENT_METHOD_UNKNOWN = 0;
  public const PAYMENT_METHOD_NETS = 1;

  // Additional product type constants.
  public const ADDITIONAL_PRODUCT_INSURANCE = 1;

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return true if the given candidate is an array, and that array has one or more elements in it. Otherwise, return
  // false - for instance, if it is null, false, an object, or an empty array.
  public static function non_empty_array($candidate)
  {
    return is_array($candidate) && (count($candidate) > 0);
  }

  // *******************************************************************************************************************
  // Return true if the given candidate is an array, and that array has precisely one element in it. Otherwise, return
  // false - for instance, if it is null, false, an object, an empty array, or an array with three items.
  public static function array_with_one($candidate)
  {
    return is_array($candidate) && (count($candidate) === 1);
  }

  // *******************************************************************************************************************
  // Read the list of field names from the POST request, and return an array where those field names are the keys, and
  // posted data is the values, as strings.
  public static function read_posted_strings($field_names)
  {
    $result = array();
    foreach ($field_names as $field_name)
    {
      $result[$field_name] = self::read_posted_string($field_name);
    }
    return $result;
  }

  // *******************************************************************************************************************

  public static function read_posted_string($field_name, $default_value = '')
  {
    if (isset($_POST[$field_name]))
    {
      return sanitize_text_field($_POST[$field_name]);
    }
    return $default_value;
  }

  // *******************************************************************************************************************

  public static function read_passed_string($field_name, $default_value = '')
  {
    if (isset($_REQUEST[$field_name]))
    {
      return sanitize_text_field($_REQUEST[$field_name]);
    }
    return $default_value;
  }

  // *******************************************************************************************************************

  public static function read_posted_integer($field_name, $default_value = -1)
  {
    if (isset($_POST[$field_name]))
    {
      return intval(sanitize_text_field($_POST[$field_name]));
    }
    return $default_value;
  }

  // *******************************************************************************************************************

  public static function read_passed_integer($field_name, $default_value = -1)
  {
    if (isset($_REQUEST[$field_name]))
    {
      return intval(sanitize_text_field($_REQUEST[$field_name]));
    }
    return $default_value;
  }

  // *******************************************************************************************************************
  // Return the number of days in the month represented by the given date.
  public static function get_days_in_month($date)
  {
    return cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));
  }

  // *******************************************************************************************************************
  // Return a string, in the format "yyyy-mm-dd", that represents the last day of the month given in $month, which
  // should be a string in the format "yyyy-mm". However, the method will return proper values even if the $month string
  // does not have leading zeroes.
  public static function get_last_date($month)
  {
    $last_day = date('t', strtotime($month . '-01'));
    // Parse the date string to ensure there are no illegal values, then return a correctly formatted date, with leading
    // zeroes if required.
    return date('Y-m-d', strtotime($month . '-' . strval($last_day)));
  }

  // *******************************************************************************************************************
  // Convert the given DateTime object to a string. The returned string will have the format "yyyy-mm-dd". If the given
  // date is not a valid DateTime object, return the given default value.
  public static function date_to_string($date, $default_value = '')
  {
    if (($date !== null) && ($date instanceof DateTime))
    {
      return $date->format('Y-m-d');
    }
    return $default_value;
  }

  // *******************************************************************************************************************
  // Convert the given $time_string to a DateTime object. The string is expected to have the format "yyyy-mm-dd". Return
  // null if the $time_string was invalid.
  public static function string_to_date($time_string)
  {
    $result = DateTime::createFromFormat('Y-m-d', $time_string);
    if ($result === false)
    {
      return null;
    }
    $result->setTime(0, 0, 0);
    return $result;
  }

  // *******************************************************************************************************************

  public static function read_posted_date($field_name)
  {
    if (isset($_POST[$field_name]))
    {
      return self::string_to_date(sanitize_text_field($_POST[$field_name]));
    }
    return null;
  }

  // *******************************************************************************************************************

  public static function read_passed_date($field_name)
  {
    if (isset($_REQUEST[$field_name]))
    {
      return self::string_to_date(sanitize_text_field($_REQUEST[$field_name]));
    }
    return null;
  }

  // *******************************************************************************************************************

  public static function read_posted_boolean($field_name)
  {
    if (isset($_POST[$field_name]))
    {
      $field_value = sanitize_text_field($_POST[$field_name]);
      if (($field_value === '1') || ($field_value === 'true') || ($field_value === 'on'))
      {
        return true;
      }
      if (($field_value === '0') || ($field_value === 'false'))
      {
        return false;
      }
    }
    return null;
  }


  // *******************************************************************************************************************
  // Check the given array of strings. Return one of the values from the constants section above, to say whether the
  // fields were all empty, whether some but not all were present, or whether all were present.
  public static function verify_fields($fields)
  {
    $allPresent = true;
    $allEmpty = true;

    foreach ($fields as $field)
    {
      if (is_string($field) && !empty($field))
      {
        // At least one non-empty string found.
        $allEmpty = false;
      }
      else
      {
        // At least one empty or non-string item found.
        $allPresent = false;
      }
    }

    if ($allPresent)
    {
      return self::ALL_PRESENT;
    }
    if ($allEmpty)
    {
      return self::ALL_EMPTY;
    }
    return self::SOME_PRESENT;
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were posted to the current page.
  public static function strings_passed($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!isset($_REQUEST[$field_name]))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were posted to the current page.
  public static function strings_posted($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!isset($_POST[$field_name]))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************

  public static function string_passed($field_name)
  {
    return isset($_REQUEST[$field_name]);
  }

  // *******************************************************************************************************************

  public static function string_posted($field_name)
  {
    return isset($_POST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were passed to the current page as integers. That is, they were passed as
  // strings that can be successfully converted to an integer.
  public static function integers_passed($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!self::integer_passed($field_name))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were posted to the current page as integers. That is, they were posted as
  // strings that can be successfully converted to an integer.
  public static function integers_posted($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!self::integer_posted($field_name))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return true if the given field was passed as part of the request to the current page - regardless of whether that
  // request was a GET, POST, or something else - and is a string that can be successfully converted to a number.
  public static function integer_passed($field_name)
  {
    return isset($_REQUEST[$field_name]) && is_numeric($_REQUEST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if the given field was posted to the current page, and is a string that can be successfully converted
  // to a number.
  public static function integer_posted($field_name)
  {
    return isset($_POST[$field_name]) && is_numeric($_POST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if the field with the given name was passed to the current page, and is a string with a valid date in
  // the yyyy-mm-dd format.
  public static function date_passed($field_name)
  {
    if (isset($_REQUEST[$field_name]))
    {
      $field_value = sanitize_text_field($_REQUEST[$field_name]);
      $dateTime = DateTime::createFromFormat('Y-m-d', $field_value);
      return ($dateTime && ($dateTime->format('Y-m-d') === $field_value));
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the field with the given name was posted to the current page, and is a string with a valid date in
  // the yyyy-mm-dd format.
  public static function date_posted($field_name)
  {
    if (isset($_POST[$field_name]))
    {
      $field_value = sanitize_text_field($_POST[$field_name]);
      $dateTime = DateTime::createFromFormat('Y-m-d', $field_value);
      return ($dateTime && ($dateTime->format('Y-m-d') === $field_value));
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the fields with the given field names appear in the given array, and are non-empty strings.
  public static function non_empty_strings($array, $field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!is_string($array[$field_name]) || empty($array[$field_name]))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // If the given error message is a non-empty string, enclose it in a div tag with the class "error-message".
  public static function enclose_in_error_div($error_message)
  {
    if (is_string($error_message) && !empty($error_message))
    {
      return '<div class="error-message">' . $error_message . '</div>';
    }
    return '';
  }

  // *******************************************************************************************************************
  public static function is_valid_email($email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

  // *******************************************************************************************************************

  public static function is_valid_role($role)
  {
    return ($role === self::ROLE_USER) || ($role === self::ROLE_COMPANY_ADMIN) || ($role === self::ROLE_GIBBS_ADMIN);
  }

  // *******************************************************************************************************************
  // Return true if the given $language is valid and supported by the application.
  public static function is_valid_language($language)
  {
    return isset($language) && is_string($language) &&
      (($language === self::NORWEGIAN) || ($language === self::ENGLISH));
  }

  // *******************************************************************************************************************
  // Read the current language from the session. If not set, use the default language and update the session.
  public static function get_current_language()
  {
    // Return the language stored on the session, if any.
    $language = $_SESSION['language'];
    if (self::is_valid_language($language))
    {
      return $language;
    }

    // No language was set. Set the default language.
    self::set_current_language(self::DEFAULT_LANGUAGE);
    return self::DEFAULT_LANGUAGE;
  }

  // *******************************************************************************************************************
  // Store the given language on the session.
  public static function set_current_language($language)
  {
    if (self::is_valid_language($language))
    {
      $_SESSION['language'] = $language;
    }
  }

  // *******************************************************************************************************************
  // Return the terms-and-conditions URL for the given language. $language is optional. If not present, the currently
  // selected language is used.
  public static function get_terms_url($language = null)
  {
    if (!isset($language))
    {
      $language = self::get_current_language();
    }
    $domain = self::get_domain();
    // English.
    if ($language === self::ENGLISH)
    {
      return $domain . '/en/vilkaar/';
    }
    // Norwegian is the default language.
    return $domain . '/vilkaar/';
  }

  // *******************************************************************************************************************
  // Return true if the given integer is an even number.
  public static function is_even($number)
  {
    return $number % 2 == 0;
  }

  // *******************************************************************************************************************
  // Ensure the given value is within the range of min to max, inclusive. Return an integer in the legal range.
  public static function clamp_number($value, $min, $max)
  {
    return max($min, min($max, $value));
  }

  // *******************************************************************************************************************
  // Convert the given integer to a string, and pad it with leading zeroes to the given minimum length. Return the
  // string.
  public static function pad_number($number, $length)
  {
    // Calculate the number of zeroes to add, and add the padding.
    $result = strval($number);
    $paddingCount = max(0, $length - strlen($result));
    return str_repeat('0', $paddingCount) . $result;
  }

  // *******************************************************************************************************************
  // Remove all types of line breaks from the given string.
  public static function remove_line_breaks($string)
  {
    return str_replace(array('\r', '\n', '\r\n'), '', $string);
  }

  // *******************************************************************************************************************
  // Redirect to the given URL with HTTP status code 302, and stop executing the current script.
  public static function redirect_to($url)
  {
    header('HTTP/1.1 302 Found');
    header('Location: ' . $url);
    exit;
  }

  // *******************************************************************************************************************
  // Return the current HTTP protocol and domain, without the trailing backslash. For instance: "https://www.gibbs.no".
  public static function get_domain()
  {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    {
      return 'https://' . $_SERVER['HTTP_HOST'];
    }
    return 'http://' . $_SERVER['HTTP_HOST'];
  }

  // *******************************************************************************************************************
  // Return a string of comma separated value triplets. Each triplet is enclosed in parentheses. The first item is the
  // $id. The second item is the key from the given $values, while the third is the corresponding value.
  public static function get_key_value_data_string($id, $values)
  {
    $result = array();
    foreach ($values as $key => $value)
    {
      $result[] = "({$id}, \"{$key}\", \"{$value}\")";
    }
    return implode(',', $result);
  }

  // *******************************************************************************************************************
  // Return a string of comma separated value pairs. Each pair is enclosed in parentheses. The first item in the pair is
  // the $fixed_value. The second item is drawn from the given table of $variable_values. 
  public static function get_value_data_string($fixed_value, $variable_values)
  {
    $result = array();
    foreach ($variable_values as $item)
    {
      $result[] = "({$fixed_value}, {$item})";
    }
    return implode(',', $result);
  }

  // *******************************************************************************************************************
  // Return the given array of numbers as a string containing a Javascript declaration. The array may be null, in which
  // case the method will return "null".
  public static function get_js_array_of_values($values)
  {
    if (isset($values))
    {
      // Remove the array keys to leave just an array of numbers, then convert to a comma separated string.
      return '[' . implode(',', array_values($values)) . ']';
    }
    return 'null';
  }
  
  // *******************************************************************************************************************
}
?>