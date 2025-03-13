<?php
// Note: when changing default colours in this file, also modify the colours assigned when pressing the "revert to
// default" button in admin_settings.js, function resetToDefaultColours.

// Note: when adding settings, remember to update the number of settings returned by the get_item_count method.

// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

// Class which holds data values that can be different for each user group.
class Settings
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // Application role constants.
  public const APP_ROLE_PRODUCTION = 'production';
  public const APP_ROLE_EVALUATION = 'evaluation';
  public const APP_ROLE_TEST = 'test';

  // Booking type constants. When changing, also modify common.js BOOKING_TYPE_ constants.
  public const BOOKING_TYPE_SELF_SERVICE = 0;
  public const BOOKING_TYPE_REQUEST = 1;
  public const BOOKING_TYPE_BOTH = 2;
  public const BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS = 3;

  // When full mode constants. When changing, also modify common.js FULL_MODE_ constants.
  public const FULL_MODE_ALTERNATIVES = 0;
  public const FULL_MODE_REQUEST = 1;
  public const FULL_MODE_REQUEST_AT_SOME_LOCATIONS = 2;

  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // Boolean flag that says whether to return hard coded dummy data to the client, rather than contact the database. The
  // default value is false. In the database, this setting is stored using the strings "true" and "false". If the
  // application_role is "production", this value cannot be true.
  protected $use_test_data = false;

  // The role for which the application is being used. Valid values are: "production", "evaluation", "test". The latter
  // is used for automated testing. The default value is "evaluation". Use the APP_ROLE_ constants defined in this
  // class.
  protected $application_role = self::APP_ROLE_EVALUATION;

  // The types of booking available to the customer. Use the BOOKING_TYPE_ constants.
  protected $booking_type = self::BOOKING_TYPE_SELF_SERVICE;

  // When booking type is BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS, this field holds an array of integers, which are the
  // IDs of the locations at which a request will be sent - that is, locations at which self service booking is not
  // available.
  protected $booking_type_locations = array();

  // How a product type is displayed when there are no free products. Use the FULL_MODE_ constants.
  //   FULL_MODE_ALTERNATIVES                 Tell the customer that the product type is not available. If the product
  //                                          type will be available later, display the date. If the product type is
  //                                          available at another location, display a list of locations.
  //   FULL_MODE_REQUEST                      The user interface will not show that the product type is unavailable.
  //                                          When selected, a request will be sent instead.
  //   FULL_MODE_REQUEST_AT_SOME_LOCATIONS    Requests will be sent for locations given in $full_mode_locations.
  //                                          Otherwise, alternatives will be displayed.
  protected $full_mode = self::FULL_MODE_ALTERNATIVES;

  // When full mode is FULL_MODE_REQUEST_AT_SOME_LOCATIONS, this field holds an array of integers, which are the IDs of
  // the locations at which a request will be sent.
  protected $full_mode_locations = array();

  // The types of payment available to customers who are private individuals. Array of numbers, each of which represents
  // an eligible payment method. If more than one payment method is available, the user will be given a choice. For the
  // contents of the array, use the Utility::PAYMENT_METHOD_ constants.
  protected $payment_methods_private = array(Utility::PAYMENT_METHOD_NETS, Utility::PAYMENT_METHOD_INVOICE);

  // The types of payment available to customers who are companies. Array of numbers, each of which represents an
  // eligible payment method. If more than one payment method is available, the user will be given a choice. For the
  // contents of the array, use the Utility::PAYMENT_METHOD_ constants.
  protected $payment_methods_company = array(Utility::PAYMENT_METHOD_NETS, Utility::PAYMENT_METHOD_INVOICE);

  // Flag that says whether to set the product readiness state to "requires check" when a subscription is cancelled. If
  // the ready state requires a check, the product cannot be booked until the check has been performed and the ready
  // state changed back to "ready".
  protected $require_check_after_cancel = true;

  // The number of months from which subscription start dates can be selected. The first month is
  // always the current one. Valid values are in the range 2 to 24.
  protected $selectable_month_count = 6;

  // The number of bookable products to return to the client when the client submits a search for available products.
  // The user only needs to book one. However, if somebody else is using the system, the first bookable product might be
  // already booked by the time he has finished. The server will return several options, if they exist. If the value is
  // -1, all available products will be returned. Valid values must be integers in the range 1 to 1000, or -1. The
  // default value is 100. In the database, the numbers are stored as strings.
  protected $bookable_product_count = 100;

  // The number of bookable products that will trigger a "hurry, there are only a few left" message. If there are this
  // many, or fewer, bookable products left, the message will be displayed. The value must be an integer between 0 and
  // 100. If the value is 0, the message will never be displayed. The default value is 3. In the database, the numbers
  // are stored as strings.
    // *** // Does this value need to be smaller than the bookable_product_count? We know the total number on the server, after all.
  protected $few_available_count = 3;

  // The client-specific secret key for payment with Nets. The default value is an empty string.
  protected $nets_secret_key = '';

  // The client-specific public checkout key for payment with Nets. The default value is an empty string.
  protected $nets_checkout_key = '';

  // The terms and conditions URLs for this user group. A string that holds a number of language code / URL sets. Each
  // set is separated by a pipe character ("|"). Each language code is separated from the URL by a space. For instance:
  //   "en_GB https://fun.com/en/toc|nb_NO https://fun.com/no/toc".
  protected $terms_urls = '';

  // *** Button style fields. ***
  // Background colour.
  protected $bg_colour = '#fff';

  // Button background colour.
  protected $button_bg_colour = '#299583';

  // Button text colour.
  protected $button_text_colour = '#fff';

  // Button background colour on hover.
  protected $button_hover_bg_colour = '#007969';

  // Button text colour on hover.
  protected $button_hover_text_colour = '#fff';

  // *** Tabset style fields. ***
  // The background colour of a tab button left of the current step.
  protected $completed_step_bg_colour = '#eaf4f3';

  // The text colour of a tab button left of the current step.
  protected $completed_step_text_colour = '#299583';

  // The background colour of the tab button with the current step.
  protected $active_step_bg_colour = '#299583';

  // The text colour of the tab button with the current step.
  protected $active_step_text_colour = '#fff';

  // The background colour of a tab button right of the current step.
  protected $incomplete_step_bg_colour = '#f3f3f3';

  // The text colour of a tab button right of the current step.
  protected $incomplete_step_text_colour = '#9d9d9d';

  // *** Other style fields. ***
  // The background colour of the line that shows the total amount when booking.
  protected $sum_bg_colour = '#b3dad5';

  // The text colour of the line that shows the total amount when booking.
  protected $sum_text_colour = '#1e1e2d';

  // The name that appears in the e-mail when Gibbs self storage sends e-mail on behalf of the self storage company.
  protected $from_email_name = '';

  // The e-mail address that appears to be the sender, when Gibbs self storage sends e-mail on behalf of the self
  // storage company.
  protected $from_email = '';

  // The e-mail address to which replies will be directed, when Gibbs self storage sends e-mail on behalf of the self
  // storage company.
  protected $reply_to_email = '';

  // The name of the self storage company. // *** // How is it used?
  protected $company_name = '';

  // The street address of the self storage company. // *** // How is it used?
  protected $company_address = '';

  // The self storage company postcode. // *** // How is it used?
  protected $company_postcode = '';

  // The self storage company area. // *** // How is it used?
  protected $company_area = '';

  // The self storage company country. // *** // How is it used?
  protected $company_country = '';

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return true if, when booking at the location with the given ID, the customer should submit a request, rather than
  // use the self service booking.
  public function submit_request_when_booking_at($location_id)
  {
    // The customer submits a request if that is the selected booking type, or if booking type is specified based on
    // locations and the given $location_id is found in the list of locations for which requests are to be sent.
    return ($this->get_booking_type() === self::BOOKING_TYPE_REQUEST) ||
      (($this->get_booking_type() === self::BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS) &&
      in_array($location_id, $this->get_booking_type_locations()));
  }

  // *******************************************************************************************************************
  // Return the URL to be used when contacting the Nets payment API. This varies depending on the application role.
  public function get_nets_payment_url($bulk = false)
  {
    // Production.
    if ($this->get_application_role() === Settings::APP_ROLE_PRODUCTION)
    {
      if ($bulk)
      {
        return Utility::NETS_BULK_CHARGE_URL_PROD;
      }
      return Utility::NETS_PAYMENT_URL_PROD;
    }

    // Test.
    if ($bulk)
    {
      return Utility::NETS_BULK_CHARGE_URL_TEST;
    }
    return Utility::NETS_PAYMENT_URL_TEST;
  }

  // *******************************************************************************************************************
  // Return the various terms and conditions URLs for different languages as an array. The language code is the key,
  // whereas the URL is the value.
  public function get_terms_url_table()
  {
      $url_table = array();
      // Split the string into "<language_code> <url>" pairs.
      $pairs = explode('|', $this->get_terms_urls());
      foreach ($pairs as $pair)
      {
        $pair = explode(' ', $pair);
        if (count($pair) === 2)
        {
          $url_table[$pair[0]] = $pair[1];
        }
      }
      return $url_table;
  }

  // *******************************************************************************************************************
  // Return the terms and conditions URL for the given $language, or an empty string if it was not found. $language is
  // optional. If not present, return the URL for the currently selected language.
  public function get_terms_url_for_language($language = null)
  {
    if (!isset($language))
    {
      $language = Utility::get_current_language();
    }

    $terms_url_table = $this->get_terms_url_table();
    if (!isset($terms_url_table[$language]))
    {
      return '';
    }
    return $terms_url_table[$language];
  }

  // *******************************************************************************************************************
  // Read all settings from the given $source array. $source might have been loaded from a database. It is assumed to
  // hold a set of arrays, each of which has "key" and "value" fields. If any of the settings are not found in $source,
  // the current values will remain unchanged.
  public function read_from_array($source)
  {
    $this->set_use_test_data(self::get_value_from_array('use_test_data', $source));
    $this->set_application_role(self::get_value_from_array('application_role', $source));
    $this->set_booking_type(self::get_value_from_array('booking_type', $source));
    $this->set_booking_type_locations(array_map('intval', explode(',', self::get_value_from_array('booking_type_locations', $source))));
    $this->set_full_mode(self::get_value_from_array('full_mode', $source));
    $this->set_full_mode_locations(array_map('intval', explode(',', self::get_value_from_array('full_mode_locations', $source))));
    $this->set_payment_methods_private(array_map('intval', explode(',', self::get_value_from_array('payment_methods_private', $source))));
    $this->set_payment_methods_company(array_map('intval', explode(',', self::get_value_from_array('payment_methods_company', $source))));
    $this->set_require_check_after_cancel(self::get_value_from_array('require_check_after_cancel', $source));
    $this->set_selectable_month_count(self::get_value_from_array('selectable_month_count', $source));
    $this->set_bookable_product_count(self::get_value_from_array('bookable_product_count', $source));
    $this->set_few_available_count(self::get_value_from_array('few_available_count', $source));
    $this->set_nets_secret_key(self::get_value_from_array('nets_secret_key', $source));
    $this->set_nets_checkout_key(self::get_value_from_array('nets_checkout_key', $source));
    $this->set_terms_urls(self::get_value_from_array('terms_urls', $source));
    $this->set_bg_colour(self::get_value_from_array('bg_colour', $source));
    $this->set_button_bg_colour(self::get_value_from_array('button_bg_colour', $source));
    $this->set_button_text_colour(self::get_value_from_array('button_text_colour', $source));
    $this->set_button_hover_bg_colour(self::get_value_from_array('button_hover_bg_colour', $source));
    $this->set_button_hover_text_colour(self::get_value_from_array('button_hover_text_colour', $source));
    $this->set_completed_step_bg_colour(self::get_value_from_array('completed_step_bg_colour', $source));
    $this->set_completed_step_text_colour(self::get_value_from_array('completed_step_text_colour', $source));
    $this->set_active_step_bg_colour(self::get_value_from_array('active_step_bg_colour', $source));
    $this->set_active_step_text_colour(self::get_value_from_array('active_step_text_colour', $source));
    $this->set_incomplete_step_bg_colour(self::get_value_from_array('incomplete_step_bg_colour', $source));
    $this->set_incomplete_step_text_colour(self::get_value_from_array('incomplete_step_text_colour', $source));
    $this->set_sum_bg_colour(self::get_value_from_array('sum_bg_colour', $source));
    $this->set_sum_text_colour(self::get_value_from_array('sum_text_colour', $source));
    $this->set_from_email_name(self::get_value_from_array('from_email_name', $source));
    $this->set_from_email(self::get_value_from_array('from_email', $source));
    $this->set_reply_to_email(self::get_value_from_array('reply_to_email', $source));
    $this->set_company_name(self::get_value_from_array('company_name', $source));
    $this->set_company_address(self::get_value_from_array('company_address', $source));
    $this->set_company_postcode(self::get_value_from_array('company_postcode', $source));
    $this->set_company_area(self::get_value_from_array('company_area', $source));
    $this->set_company_country(self::get_value_from_array('company_country', $source));
  }

  // *******************************************************************************************************************
  // Read all settings from information posted to the server. If any of the settings are not provided, the current
  // values will remain unchanged.
  public function read_from_posted_data()
  {
    // If the use_test_data value is posted from a form, there will be no value if the checkbox is unchecked, and the
    // read_posted_boolean method will return null. Do the comparison in order to always get a boolean value.
    $this->set_use_test_data(Utility::read_posted_boolean('use_test_data') === true);

    if (Utility::string_posted('application_role'))
    {
      $this->set_application_role(Utility::read_posted_string('application_role'));
    }

    if (Utility::integer_posted('booking_type'))
    {
      $this->set_booking_type(Utility::read_posted_integer('booking_type'));
    }

    // If the booking type is BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS, read the list of locations. Otherwise, store an
    // empty array. The client will post a location_count field that holds the number of locations that could possibly
    // be selected. Each location will have a checkbox. If the checkbox is checked, the location ID should be posted as
    // the value. Otherwise, nothing will be posted for that location.
    $booking_type_locations = array();
    if ($this->get_booking_type() === self::BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS)
    {
      $location_count = Utility::read_posted_integer('location_count');
      if ($location_count > 0)
      {
        for ($i = 0; $i < $location_count; $i++)
        {
          $location_id = Utility::read_posted_integer('booking_type_location_' . strval($i));
          if ($location_id >= 0)
          {
            $booking_type_locations[] = $location_id;
          }
        }
      }
    }
    $this->set_booking_type_locations($booking_type_locations);

    if (Utility::integer_posted('full_mode'))
    {
      $this->set_full_mode(Utility::read_posted_integer('full_mode'));
    }

    // If the full mode is FULL_MODE_REQUEST_AT_SOME_LOCATIONS, read the list of locations. Otherwise, store an empty
    // array. The client will post a location_count field that holds the number of locations that could possibly be
    // selected. Each location will have a checkbox. If the checkbox is checked, the location ID should be posted as
    // the value. Otherwise, nothing will be posted for that location.
    $full_mode_locations = array();
    if ($this->get_full_mode() === self::FULL_MODE_REQUEST_AT_SOME_LOCATIONS)
    {
      $location_count = Utility::read_posted_integer('location_count');
      if ($location_count > 0)
      {
        for ($i = 0; $i < $location_count; $i++)
        {
          $location_id = Utility::read_posted_integer('full_mode_location_' . strval($i));
          if ($location_id >= 0)
          {
            $full_mode_locations[] = $location_id;
          }
        }
      }
    }
    $this->set_full_mode_locations($full_mode_locations);

    // Read individual boolean values for all supported payment types for both private individuals and companies, and
    // compose an array of selected types. If selected, add the payment method to the array.
    $payment_methods_private = array();
    $payment_methods_company = array();
    for ($i = Utility::PAYMENT_METHOD_NETS; $i <= Utility::PAYMENT_METHOD_NETS_THEN_INVOICE; $i++)
    {
      if (Utility::read_posted_boolean('payment_method_private_' . $i) === true)
      {
        $payment_methods_private[] = $i;
      }
      if (Utility::read_posted_boolean('payment_method_company_' . $i) === true)
      {
        $payment_methods_company[] = $i;
      }
    }
    $this->set_payment_methods_private($payment_methods_private);
    $this->set_payment_methods_company($payment_methods_company);

    // If the use_test_data value is posted from a form, there will be no value if the checkbox is unchecked, and the
    // read_posted_boolean method will return null. Do the comparison in order to always get a boolean value.
    $this->set_require_check_after_cancel(Utility::read_posted_boolean('require_check_after_cancel') === true);

    if (Utility::integer_posted('selectable_month_count'))
    {
      $this->set_selectable_month_count(Utility::read_posted_integer('selectable_month_count'));
    }

    if (Utility::integer_posted('bookable_product_count'))
    {
      $this->set_bookable_product_count(Utility::read_posted_integer('bookable_product_count'));
    }

    if (Utility::integer_posted('few_available_count'))
    {
      $this->set_few_available_count(Utility::read_posted_integer('few_available_count'));
    }

    if (Utility::string_posted('nets_secret_key'))
    {
      $this->set_nets_secret_key(Utility::read_posted_string('nets_secret_key'));
    }

    if (Utility::string_posted('nets_checkout_key'))
    {
      $this->set_nets_checkout_key(Utility::read_posted_string('nets_checkout_key'));
    }

    if (Utility::string_posted('terms_urls'))
    {
      $this->set_terms_urls(Utility::read_posted_string('terms_urls'));
    }

    if (Utility::colour_posted('bg_colour'))
    {
      $this->set_bg_colour(Utility::read_posted_string('bg_colour'));
    }

    if (Utility::colour_posted('button_bg_colour'))
    {
      $this->set_button_bg_colour(Utility::read_posted_string('button_bg_colour'));
    }

    if (Utility::colour_posted('button_text_colour'))
    {
      $this->set_button_text_colour(Utility::read_posted_string('button_text_colour'));
    }

    if (Utility::colour_posted('button_hover_bg_colour'))
    {
      $this->set_button_hover_bg_colour(Utility::read_posted_string('button_hover_bg_colour'));
    }

    if (Utility::colour_posted('button_hover_text_colour'))
    {
      $this->set_button_hover_text_colour(Utility::read_posted_string('button_hover_text_colour'));
    }

    if (Utility::colour_posted('completed_step_bg_colour'))
    {
      $this->set_completed_step_bg_colour(Utility::read_posted_string('completed_step_bg_colour'));
    }

    if (Utility::colour_posted('completed_step_text_colour'))
    {
      $this->set_completed_step_text_colour(Utility::read_posted_string('completed_step_text_colour'));
    }

    if (Utility::colour_posted('active_step_bg_colour'))
    {
      $this->set_active_step_bg_colour(Utility::read_posted_string('active_step_bg_colour'));
    }

    if (Utility::colour_posted('active_step_text_colour'))
    {
      $this->set_active_step_text_colour(Utility::read_posted_string('active_step_text_colour'));
    }

    if (Utility::colour_posted('incomplete_step_bg_colour'))
    {
      $this->set_incomplete_step_bg_colour(Utility::read_posted_string('incomplete_step_bg_colour'));
    }

    if (Utility::colour_posted('incomplete_step_text_colour'))
    {
      $this->set_incomplete_step_text_colour(Utility::read_posted_string('incomplete_step_text_colour'));
    }

    if (Utility::colour_posted('sum_bg_colour'))
    {
      $this->set_sum_bg_colour(Utility::read_posted_string('sum_bg_colour'));
    }

    if (Utility::colour_posted('sum_text_colour'))
    {
      $this->set_sum_text_colour(Utility::read_posted_string('sum_text_colour'));
    }

    if (Utility::string_posted('from_email_name'))
    {
      $this->set_from_email_name(Utility::read_posted_string('from_email_name'));
    }

    if (Utility::string_posted('from_email'))
    {
      $this->set_from_email(Utility::read_posted_string('from_email'));
    }

    if (Utility::string_posted('reply_to_email'))
    {
      $this->set_reply_to_email(Utility::read_posted_string('reply_to_email'));
    }

    if (Utility::string_posted('company_name'))
    {
      $this->set_company_name(Utility::read_posted_string('company_name'));
    }

    if (Utility::string_posted('company_address'))
    {
      $this->set_company_address(Utility::read_posted_string('company_address'));
    }

    if (Utility::string_posted('company_postcode'))
    {
      $this->set_company_postcode(Utility::read_posted_string('company_postcode'));
    }

    if (Utility::string_posted('company_area'))
    {
      $this->set_company_area(Utility::read_posted_string('company_area'));
    }

    if (Utility::string_posted('company_country'))
    {
      $this->set_company_country(Utility::read_posted_string('company_country'));
    }
  }

  // *******************************************************************************************************************
  // Return the settings in this object as a Javascript object declaration.
  public function as_javascript($include_secrets = false)
  {
    $terms_url_table = $this->get_terms_url_table();
    if (empty($terms_url_table))
    {
      // Encode an empty array as an empty object. The client always expects an object.
      $terms_url_string = json_encode((object) $terms_url_table);
    }
    else
    {
      // Encode an associative array with contents as an object.
      $terms_url_string = json_encode($terms_url_table);
    }

    $js = "{";
    $js .= self::get_boolean_key_value_pair('useTestData', $this->get_use_test_data());
    $js .= self::get_string_key_value_pair('applicationRole', $this->get_application_role());
    $js .= self::get_integer_key_value_pair('bookingType', $this->get_booking_type());
    $js .= self::get_array_key_value_pair('bookingTypeLocations', $this->get_booking_type_locations());
    $js .= self::get_integer_key_value_pair('fullMode', $this->get_full_mode());
    $js .= self::get_array_key_value_pair('fullModeLocations', $this->get_full_mode_locations());
    $js .= self::get_array_key_value_pair('paymentMethodsPrivate', $this->get_payment_methods_private());
    $js .= self::get_array_key_value_pair('paymentMethodsCompany', $this->get_payment_methods_company());
    $js .= self::get_boolean_key_value_pair('requireCheckAfterCancel', $this->get_require_check_after_cancel());
    $js .= self::get_integer_key_value_pair('selectableMonthCount', $this->get_selectable_month_count());
    $js .= self::get_integer_key_value_pair('bookableProductCount', $this->get_bookable_product_count());
    $js .= self::get_integer_key_value_pair('fewAvailableCount', $this->get_few_available_count());
    if ($include_secrets)
    {
      $js .= self::get_string_key_value_pair('netsSecretKey', $this->get_nets_secret_key());
    }
    $js .= self::get_string_key_value_pair('netsCheckoutKey', $this->get_nets_checkout_key());
    $js .= 'termsUrls: ' . $terms_url_string . ',';
    $js .= self::get_string_key_value_pair('bgColour', $this->get_bg_colour());
    $js .= self::get_string_key_value_pair('buttonBgColour', $this->get_button_bg_colour());
    $js .= self::get_string_key_value_pair('buttonTextColour', $this->get_button_text_colour());
    $js .= self::get_string_key_value_pair('buttonHoverBgColour', $this->get_button_hover_bg_colour());
    $js .= self::get_string_key_value_pair('buttonHoverTextColour', $this->get_button_hover_text_colour());
    $js .= self::get_string_key_value_pair('completedStepBgColour', $this->get_completed_step_bg_colour());
    $js .= self::get_string_key_value_pair('completedStepTextColour', $this->get_completed_step_text_colour());
    $js .= self::get_string_key_value_pair('activeStepBgColour', $this->get_active_step_bg_colour());
    $js .= self::get_string_key_value_pair('activeStepTextColour', $this->get_active_step_text_colour());
    $js .= self::get_string_key_value_pair('incompleteStepBgColour', $this->get_incomplete_step_bg_colour());
    $js .= self::get_string_key_value_pair('incompleteStepTextColour', $this->get_incomplete_step_text_colour());
    $js .= self::get_string_key_value_pair('sumBgColour', $this->get_sum_bg_colour());
    $js .= self::get_string_key_value_pair('sumTextColour', $this->get_sum_text_colour());
    $js .= self::get_string_key_value_pair('fromEmailName', $this->get_from_email_name());
    $js .= self::get_string_key_value_pair('fromEmail', $this->get_from_email());
    $js .= self::get_string_key_value_pair('replyToEmail', $this->get_reply_to_email());
    $js .= self::get_string_key_value_pair('companyName', $this->get_company_name());
    $js .= self::get_string_key_value_pair('companyAddress', $this->get_company_address());
    $js .= self::get_string_key_value_pair('companyPostcode', $this->get_company_postcode());
    $js .= self::get_string_key_value_pair('companyArea', $this->get_company_area());
    $js .= self::get_string_key_value_pair('companyCountry', $this->get_company_country(), false);
    $js .= "}";
    return $js;
  }

  // *******************************************************************************************************************
  // Return the settings in this object as a string containing the values required to store the settings in the
  // database. Each setting is enclosed in parentheses, and contains the given $group_id, the key and the value.
  // Parentheses are separated by commas. The pattern is: "(group_id, key, value), (group_id, key, value), ..."
  public function as_database_value_string($group_id)
  {
    $items = array(
      'use_test_data' => ($this->get_use_test_data() ? 'true' : 'false'),
      'application_role' => $this->get_application_role(),
      'booking_type' => strval($this->get_booking_type()),
      'booking_type_locations' => implode(',', $this->get_booking_type_locations()),
      'full_mode' => strval($this->get_full_mode()),
      'full_mode_locations' => implode(',', $this->get_full_mode_locations()),
      'payment_methods_private' => implode(',', $this->get_payment_methods_private()),
      'payment_methods_company' => implode(',', $this->get_payment_methods_company()),
      'require_check_after_cancel' => ($this->get_require_check_after_cancel() ? 'true' : 'false'),
      'selectable_month_count' => strval($this->get_selectable_month_count()),
      'bookable_product_count' => strval($this->get_bookable_product_count()),
      'few_available_count' => strval($this->get_few_available_count()),
      'nets_secret_key' => $this->get_nets_secret_key(),
      'nets_checkout_key' => $this->get_nets_checkout_key(),
      'terms_urls' => $this->get_terms_urls(),
      'bg_colour' => $this->get_bg_colour(),
      'button_bg_colour' => $this->get_button_bg_colour(),
      'button_text_colour' => $this->get_button_text_colour(),
      'button_hover_bg_colour' => $this->get_button_hover_bg_colour(),
      'button_hover_text_colour' => $this->get_button_hover_text_colour(),
      'completed_step_bg_colour' => $this->get_completed_step_bg_colour(),
      'completed_step_text_colour' => $this->get_completed_step_text_colour(),
      'active_step_bg_colour' => $this->get_active_step_bg_colour(),
      'active_step_text_colour' => $this->get_active_step_text_colour(),
      'incomplete_step_bg_colour' => $this->get_incomplete_step_bg_colour(),
      'incomplete_step_text_colour' => $this->get_incomplete_step_text_colour(),
      'sum_bg_colour' => $this->get_sum_bg_colour(),
      'sum_text_colour' => $this->get_sum_text_colour(),
      'from_email_name' => $this->get_from_email_name(),
      'from_email' => $this->get_from_email(),
      'reply_to_email' => $this->get_reply_to_email(),
      'company_name' => $this->get_company_name(),
      'company_address' => $this->get_company_address(),
      'company_postcode' => $this->get_company_postcode(),
      'company_area' => $this->get_company_area(),
      'company_country' => $this->get_company_country()
    );

    return Utility::get_key_value_data_string($group_id, $items);
  }

  // *******************************************************************************************************************
  // Return the number of settings in this class.
  public function get_item_count()
  {
    return 36;
  }

  // *******************************************************************************************************************
  // Return the potential payment methods for users with the given entity type (private individual or company). Use the
  // ENTITY_TYPE_ constants.
  public function get_payment_methods_for_entity_type($entity_type)
  {
    if ($entity_type === Utility::ENTITY_TYPE_COMPANY)
    {
      return $this->get_payment_methods_company();
    }
    return $this->get_payment_methods_private();
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return the value of the given $key from the given $source array. $source is assumed to hold a set of arrays, each
  // of which has "key" and "value" fields.
  protected static function get_value_from_array($key, $source)
  {
    foreach ($source as $pair)
    {
      if ($pair['key'] === $key)
      {
        return $pair['value'];
      }
    }
    return '';
  }

  // *******************************************************************************************************************
  // Return a key / value pair that can form part of a Javascript object declaration. $value should be a boolean. The
  // resulting declaration will be terminated with a comma, unless $with_comma is false.
  protected static function get_boolean_key_value_pair($key, $value, $with_comma = true)
  {
    $js = $key . ": " . ($value ? 'true' : 'false');
    if ($with_comma)
    {
      $js .= ",";
    }
    return $js;
  }

  // *******************************************************************************************************************
  // Return a key / value pair that can form part of a Javascript object declaration. $value should be an integer. The
  // resulting declaration will be terminated with a comma, unless $with_comma is false.
  protected static function get_integer_key_value_pair($key, $value, $with_comma = true)
  {
    $js = $key . ": " . strval($value);
    if ($with_comma)
    {
      $js .= ",";
    }
    return $js;
  }

  // *******************************************************************************************************************
  // Return a key / value pair that can form part of a Javascript object declaration. $value should be a string. The
  // resulting declaration will be terminated with a comma, unless $with_comma is false.
  protected static function get_string_key_value_pair($key, $value, $with_comma = true)
  {
    $js = $key . ": '" . $value . "'";
    if ($with_comma)
    {
      $js .= ",";
    }
    return $js;
  }

  // *******************************************************************************************************************
  // Return a key / value pair that can form part of a Javascript object declaration. $value should be an array without
  // keys. The resulting declaration will be terminated with a comma, unless $with_comma is false.
  protected static function get_array_key_value_pair($key, $value, $with_comma = true)
  {
    $js = $key . ": " . json_encode($value);
    if ($with_comma)
    {
      $js .= ",";
    }
    return $js;
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************
  // Return the $use_test_data property.
  public function get_use_test_data()
  {
    return $this->use_test_data;
  }

  // *******************************************************************************************************************
  // Set the $use_test_data property.
  public function set_use_test_data($value)
  {
    // Convert the value from a string to a boolean, if required. This method accepts 'true' or 'false'.
    if ($value === 'true')
    {
      $value = true;
    }
    elseif ($value === 'false')
    {
      $value = false;
    }

    // Store the value, if the value is valid.
    if ($value === true)
    {
      if ($this->get_application_role() !== self::APP_ROLE_PRODUCTION)
      {
        $this->use_test_data = true;
      }
    }
    elseif ($value === false)
    {
      $this->use_test_data = false;
    }
  }

  // *******************************************************************************************************************
  // Return the $application_role property.
  public function get_application_role()
  {
    return $this->application_role;
  }

  // *******************************************************************************************************************
  // Set the $application_role property.
  public function set_application_role($value)
  {
    if ($value === self::APP_ROLE_PRODUCTION)
    {
      if (!$this->get_use_test_data())
      {
        $this->application_role = $value;
      }
    }
    elseif (($value === self::APP_ROLE_EVALUATION) || ($value === self::APP_ROLE_TEST))
    {
      $this->application_role = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $booking_type property.
  public function get_booking_type()
  {
    return $this->booking_type;
  }

  // *******************************************************************************************************************
  // Set the $booking_type property.
  public function set_booking_type($value)
  {
    if (is_numeric($value))
    {
      $value = intval($value);
      if (($value >= self::BOOKING_TYPE_SELF_SERVICE) && ($value <= self::BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS))
      {
        $this->booking_type = $value;
      }
    }
  }

  // *******************************************************************************************************************
  // Return the $booking_type_locations property.
  public function get_booking_type_locations()
  {
    return $this->booking_type_locations;
  }

  // *******************************************************************************************************************
  // Set the $booking_type_locations property.
  public function set_booking_type_locations($value)
  {
    if (is_array($value))
    {
      // The value is an array. Verify each element of the array.
      foreach ($value as $id)
      {
        if (!is_numeric($id))
        {
          return;
        }
      }
      array_map('intval', $value);
      foreach ($value as $id)
      {
        if ($id < 0)
        {
          return;
        }
      }
      $this->booking_type_locations = $value;
    }
    elseif(is_numeric($value))
    {
      $value = intval($value);
      if ($value < 0)
      {
        return;
      }
      // The value is an ID. Add it as the sole member of an array.
      $this->booking_type_locations = array($value);
    }
  }

  // *******************************************************************************************************************
  // Return the $full_mode property.
  public function get_full_mode()
  {
    return $this->full_mode;
  }

  // *******************************************************************************************************************
  // Set the $full_mode property.
  public function set_full_mode($value)
  {
    if (is_numeric($value))
    {
      $value = intval($value);
      if (($value >= self::FULL_MODE_ALTERNATIVES) && ($value <= self::FULL_MODE_REQUEST_AT_SOME_LOCATIONS))
      {
        $this->full_mode = $value;
      }
    }
  }

  // *******************************************************************************************************************
  // Return the $full_mode_locations property.
  public function get_full_mode_locations()
  {
    return $this->full_mode_locations;
  }

  // *******************************************************************************************************************
  // Set the $full_mode_locations property.
  public function set_full_mode_locations($value)
  {
    if (is_array($value))
    {
      // The value is an array. Verify each element of the array.
      foreach ($value as $id)
      {
        if (!is_numeric($id))
        {
          return;
        }
      }
      array_map('intval', $value);
      foreach ($value as $id)
      {
        if ($id < 0)
        {
          return;
        }
      }
      $this->full_mode_locations = $value;
    }
    elseif(is_numeric($value))
    {
      $value = intval($value);
      if ($value < 0)
      {
        return;
      }
      // The value is an ID. Add it as the sole member of an array.
      $this->full_mode_locations = array($value);
    }
  }

  // *******************************************************************************************************************
  // Return the $payment_methods_private property.
  public function get_payment_methods_private()
  {
    return $this->payment_methods_private;
  }

  // *******************************************************************************************************************
  // Set the $payment_methods_private property. You can pass either an array of payment methods, or a single payment
  // method. Use the PAYMENT_METHOD_ constants.
  public function set_payment_methods_private($value)
  {
    if (is_array($value))
    {
      // The value is an array. Verify each element of the array.
      foreach ($value as $payment_method)
      {
        if (!Utility::is_valid_payment_method($payment_method))
        {
          return;
        }
      }
      $this->payment_methods_private = $value;
    }
    elseif(Utility::is_valid_payment_method($value))
    {
      // The value is a payment method. Add it as the sole member of an array.
      $this->payment_methods_private = array(intval($value));
    }
  }

  // *******************************************************************************************************************
  // Return the $payment_methods_company property.
  public function get_payment_methods_company()
  {
    return $this->payment_methods_company;
  }

  // *******************************************************************************************************************
  // Set the $payment_methods_company property. You can pass either an array of payment methods, or a single payment method.
  // Use the PAYMENT_METHOD_ constants.
  public function set_payment_methods_company($value)
  {
    if (is_array($value))
    {
      // The value is an array. Verify each element of the array.
      foreach ($value as $payment_method)
      {
        if (!Utility::is_valid_payment_method($payment_method))
        {
          return;
        }
      }
      $this->payment_methods_company = $value;
    }
    elseif(Utility::is_valid_payment_method($value))
    {
      // The value is a payment method. Add it as the sole member of an array.
      $this->payment_methods_company = array(intval($value));
    }
  }

  // *******************************************************************************************************************
  // Return the $require_check_after_cancel property.
  public function get_require_check_after_cancel()
  {
    return $this->require_check_after_cancel;
  }

  // *******************************************************************************************************************
  // Set the $require_check_after_cancel property.
  public function set_require_check_after_cancel($value)
  {
    // Convert the value from a string to a boolean, if required. This method accepts 'true' or 'false'.
    if ($value === 'true')
    {
      $value = true;
    }
    elseif ($value === 'false')
    {
      $value = false;
    }

    if ($value === true)
    {
      $this->require_check_after_cancel = true;
    }
    elseif ($value === false)
    {
      $this->require_check_after_cancel = false;
    }
  }

  // *******************************************************************************************************************
  // Return the $selectable_month_count property.
  public function get_selectable_month_count()
  {
    return $this->selectable_month_count;
  }

  // *******************************************************************************************************************
  // Set the $selectable_month_count property.
  public function set_selectable_month_count($value)
  {
    if (is_numeric($value))
    {
      $value = intval($value);
      if (($value >= 2) && ($value <= 24))
      {
        $this->selectable_month_count = $value;
      }
    }
  }

  // *******************************************************************************************************************
  // Return the $bookable_product_count property.
  public function get_bookable_product_count()
  {
    return $this->bookable_product_count;
  }

  // *******************************************************************************************************************
  // Set the $bookable_product_count property.
  public function set_bookable_product_count($value)
  {
    if (is_numeric($value))
    {
      $value = intval($value);
      if (($value === -1) || (($value >= 1) && ($value <= 1000)))
      {
        $this->bookable_product_count = $value;
      }
    }
  }

  // *******************************************************************************************************************
  // Return the $few_available_count property.
  public function get_few_available_count()
  {
    return $this->few_available_count;
  }

  // *******************************************************************************************************************
  // Set the $few_available_count property.
  public function set_few_available_count($value)
  {
    if (is_numeric($value))
    {
      $value = intval($value);
      if (($value >= 0) && ($value <= 100))
      {
        $this->few_available_count = $value;
      }
    }
  }

  // *******************************************************************************************************************
  // Return the $nets_secret_key property.
  public function get_nets_secret_key()
  {
    return $this->nets_secret_key;
  }

  // *******************************************************************************************************************
  // Set the $nets_secret_key property.
  public function set_nets_secret_key($value)
  {
    $this->nets_secret_key = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $nets_checkout_key property.
  public function get_nets_checkout_key()
  {
    return $this->nets_checkout_key;
  }

  // *******************************************************************************************************************
  // Set the $nets_checkout_key property.
  public function set_nets_checkout_key($value)
  {
    $this->nets_checkout_key = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $terms_urls property.
  public function get_terms_urls()
  {
    return $this->terms_urls;
  }

  // *******************************************************************************************************************
  // Set the $terms_urls property.
  public function set_terms_urls($value)
  {
    $this->terms_urls = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $bg_colour property.
  public function get_bg_colour()
  {
    return $this->bg_colour;
  }

  // *******************************************************************************************************************
  // Set the $bg_colour property.
  public function set_bg_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->bg_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $button_bg_colour property.
  public function get_button_bg_colour()
  {
    return $this->button_bg_colour;
  }

  // *******************************************************************************************************************
  // Set the $button_bg_colour property.
  public function set_button_bg_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->button_bg_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $button_text_colour property.
  public function get_button_text_colour()
  {
    return $this->button_text_colour;
  }

  // *******************************************************************************************************************
  // Set the $button_text_colour property.
  public function set_button_text_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->button_text_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $button_hover_bg_colour property.
  public function get_button_hover_bg_colour()
  {
    return $this->button_hover_bg_colour;
  }

  // *******************************************************************************************************************
  // Set the $button_hover_bg_colour property.
  public function set_button_hover_bg_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->button_hover_bg_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $button_hover_text_colour property.
  public function get_button_hover_text_colour()
  {
    return $this->button_hover_text_colour;
  }

  // *******************************************************************************************************************
  // Set the $button_hover_text_colour property.
  public function set_button_hover_text_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->button_hover_text_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $completed_step_bg_colour property.
  public function get_completed_step_bg_colour()
  {
    return $this->completed_step_bg_colour;
  }

  // *******************************************************************************************************************
  // Set the $completed_step_bg_colour property.
  public function set_completed_step_bg_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->completed_step_bg_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $completed_step_text_colour property.
  public function get_completed_step_text_colour()
  {
    return $this->completed_step_text_colour;
  }

  // *******************************************************************************************************************
  // Set the $completed_step_text_colour property.
  public function set_completed_step_text_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->completed_step_text_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $active_step_bg_colour property.
  public function get_active_step_bg_colour()
  {
    return $this->active_step_bg_colour;
  }

  // *******************************************************************************************************************
  // Set the $active_step_bg_colour property.
  public function set_active_step_bg_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->active_step_bg_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $active_step_text_colour property.
  public function get_active_step_text_colour()
  {
    return $this->active_step_text_colour;
  }

  // *******************************************************************************************************************
  // Set the $active_step_text_colour property.
  public function set_active_step_text_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->active_step_text_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $incomplete_step_bg_colour property.
  public function get_incomplete_step_bg_colour()
  {
    return $this->incomplete_step_bg_colour;
  }

  // *******************************************************************************************************************
  // Set the $incomplete_step_bg_colour property.
  public function set_incomplete_step_bg_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->incomplete_step_bg_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $incomplete_step_text_colour property.
  public function get_incomplete_step_text_colour()
  {
    return $this->incomplete_step_text_colour;
  }

  // *******************************************************************************************************************
  // Set the $incomplete_step_text_colour property.
  public function set_incomplete_step_text_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->incomplete_step_text_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $sum_bg_colour property.
  public function get_sum_bg_colour()
  {
    return $this->sum_bg_colour;
  }

  // *******************************************************************************************************************
  // Set the $sum_bg_colour property.
  public function set_sum_bg_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->sum_bg_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $sum_text_colour property.
  public function get_sum_text_colour()
  {
    return $this->sum_text_colour;
  }

  // *******************************************************************************************************************
  // Set the $sum_text_colour property.
  public function set_sum_text_colour($value)
  {
    if (Utility::is_valid_colour($value))
    {
      $this->sum_text_colour = $value;
    }
  }

  // *******************************************************************************************************************
  // Return the $from_email_name property.
  public function get_from_email_name()
  {
    return $this->from_email_name;
  }

  // *******************************************************************************************************************
  // Set the $from_email_name property.
  public function set_from_email_name($value)
  {
    $this->from_email_name = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $from_email property.
  public function get_from_email()
  {
    return $this->from_email;
  }

  // *******************************************************************************************************************
  // Set the $from_email property.
  public function set_from_email($value)
  {
    $this->from_email = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $reply_to_email property.
  public function get_reply_to_email()
  {
    return $this->reply_to_email;
  }

  // *******************************************************************************************************************
  // Set the $reply_to_email property.
  public function set_reply_to_email($value)
  {
    $this->reply_to_email = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $company_name property.
  public function get_company_name()
  {
    return $this->company_name;
  }

  // *******************************************************************************************************************
  // Set the $company_name property.
  public function set_company_name($value)
  {
    $this->company_name = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $company_address property.
  public function get_company_address()
  {
    return $this->company_address;
  }

  // *******************************************************************************************************************
  // Set the $company_address property.
  public function set_company_address($value)
  {
    $this->company_address = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $company_postcode property.
  public function get_company_postcode()
  {
    return $this->company_postcode;
  }

  // *******************************************************************************************************************
  // Set the $company_postcode property.
  public function set_company_postcode($value)
  {
    $this->company_postcode = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $company_area property.
  public function get_company_area()
  {
    return $this->company_area;
  }

  // *******************************************************************************************************************
  // Set the $company_area property.
  public function set_company_area($value)
  {
    $this->company_area = strval($value);
  }

  // *******************************************************************************************************************
  // Return the $company_country property.
  public function get_company_country()
  {
    return $this->company_country;
  }

  // *******************************************************************************************************************
  // Set the $company_country property.
  public function set_company_country($value)
  {
    $this->company_country = strval($value);
  }

  // *******************************************************************************************************************
}
?>