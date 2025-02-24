<?php
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

  // The number of bookable products to return to the client when the client submits a search for available products.
  // The user only needs to book one. However, if somebody else is using the system, the first bookable product might be
  // already booked by the time he has finished. The server will return several options, if they exist. If the value is
  // -1, all available products will be returned. Valid values must be integers in the range 1 to 100, or -1. The
  // default value is 10. In the database, the numbers are stored as strings.
  protected $bookable_product_count = 10;

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

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all settings from the given $source array. $source might have been loaded from a database. It is assumed to
  // hold a set of arrays, each of which has "key" and "value" fields. If any of the settings are not found in $source,
  // the current values will remain unchanged.
  public function read_from_array($source)
  {
    $this->set_use_test_data(self::get_value_from_array('use_test_data', $source));
    $this->set_application_role(self::get_value_from_array('application_role', $source));
    $this->set_bookable_product_count(self::get_value_from_array('bookable_product_count', $source));
    $this->set_few_available_count(self::get_value_from_array('few_available_count', $source));
    $this->set_nets_secret_key(self::get_value_from_array('nets_secret_key', $source));
    $this->set_nets_checkout_key(self::get_value_from_array('nets_checkout_key', $source));
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
  }

  // *******************************************************************************************************************
  // Return the settings in this object as a Javascript object declaration.
  public function as_javascript($include_secrets = false)
  {
    $js = "{";
    $js .= self::get_boolean_key_value_pair('useTestData', $this->get_use_test_data());
    $js .= self::get_string_key_value_pair('applicationRole', $this->get_application_role());
    $js .= self::get_integer_key_value_pair('bookableProductCount', $this->get_bookable_product_count());
    $js .= self::get_integer_key_value_pair('fewAvailableCount', $this->get_few_available_count());
    if ($include_secrets)
    {
      $js .= self::get_string_key_value_pair('netsSecretKey', $this->get_nets_secret_key());
    }
    $js .= self::get_string_key_value_pair('netsCheckoutKey', $this->get_nets_checkout_key(), false);
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
      'bookable_product_count' => strval($this->get_bookable_product_count()),
      'few_available_count' => strval($this->get_few_available_count()),
      'nets_secret_key' => $this->get_nets_secret_key(),
      'nets_checkout_key' => $this->get_nets_checkout_key()
    );

    return Utility::get_key_value_data_string($group_id, $items);
  }

  // *******************************************************************************************************************
  // Return the number of settings in this class.
  public function get_item_count()
  {
    return 6;
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
      if (($value === -1) || (($value >= 1) && ($value <= 100)))
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
}
?>