<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/config.php';

class Monthly_Payments_Utility
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************

  // Mode constants.
  public const MODE_UNKNOWN = -1;
  public const MODE_NORMAL = 0;
  public const MODE_SIMULATION = 1;

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return the month for which orders or payments should be created. If the value was passed as a parameter, use that
  // if it is valid. Return an empty string if it was not valid. If no parameter was passed, return the provided default
  // value. The month will be returned as a string in the format "yyyy-mm".
  public static function get_selected_month($default_value)
  {
    // See if the parameter was passed at all, and was a valid month value.
    if (Utility::string_passed('month'))
    {
      if (Utility::month_passed('month'))
      {
        $month = Utility::read_passed_string('month');
        // Use the month value if it was this month or later. Months in the "yyyy-mm" format can be compared
        // alphabetically (which is the beauty of the ISO format).
        if ($month >= Utility::get_this_month())
        {
          return $month;
        }
      }
      // The month was passed, but was not a valid value.
      return '';
    }

    // Return next month.
    return $default_value;
  }

  // *******************************************************************************************************************
  // Return an array of user groups for which orders or payments should be created. If a list of IDs was passed in the
  // user_group_ids parameter, use that to select user groups. If the user_group_ids parameter was not passed, return a
  // list of all user groups. The returned array will only contain existing user groups that have a licence for the
  // Gibbs self storage application. However, the licence may not be active, which should be checked by the caller.
  // Also, the returned array may be empty.
  public static function get_user_groups($access_token)
  {
    // Read the complete list of user groups.
    $licencee_data = new Licencee_Data_Manager($access_token);
    $licencees = $licencee_data->get_licencees();

    // If a list of IDs was passed, ensure they are all valid IDs that exist in the complete list of user groups.
    if (Utility::string_passed('user_group_ids'))
    {
      $user_groups = array();
      $id_string_table = explode(',', Utility::read_passed_string('user_group_ids'));
      foreach ($id_string_table as $id_string)
      {
        $user_group = self::get_user_group($id_string, $licencees);
        if ($user_group !== null)
        {
          // The ID was valid and corresponded to an actual user group with a Gibbs self storage licence. Add it to the
          // list of user groups that will be processed.
          $user_groups[] = $user_group;
        }
      }
      return $user_groups;
    }

    // No list of IDs was passed. Return the complete list of licenced user groups.
    return $licencees;
  }

  // *******************************************************************************************************************
  // Return the operating mode. Use the MODE_ constants in this class. Valid values are MODE_NORMAL and MODE_SIMULATION.
  // The parameter is optional; the default value is MODE_NORMAL.
  public static function get_mode()
  {
    // Read the parameter, and use a default value if it was not passed. Then cap the value to 0 or 1.
    return min(max(Utility::read_passed_integer('mode', self::MODE_NORMAL), self::MODE_NORMAL), self::MODE_SIMULATION);
  }

  // *******************************************************************************************************************
  // If the user is logged in, ensure he is a Gibbs administrator. If not, attempt to log in as a Gibbs administrator
  // using the credentials found in the config file. Update the given $config, $access_token and $result_code fields
  // accordingly. The config file must be loaded successfully, regardless of whether the credentials were used.
  public static function log_in_as_gibbs_admin(&$config, &$access_token, &$result_code)
  {
    $access_token = null;
    // Read the config file with user information.
    $config = Config::read_config_file();
    if ($config !== null)
    {
      // If the user is already logged in, ensure he is a Gibbs administrator.
      if (is_user_logged_in())
      {
        $access_token = User::verify_is_gibbs_admin(false);
        $result_code = Result::NO_ACTION_TAKEN;
      }
      else
      {
        // Attempt to log in using the user name and password found in the config file. If the user was successfully
        // logged in, ensure he is a Gibbs administrator.
        $result_code = User::log_in_with(Config::get_gibbs_admin_user_name($config),
          Config::get_gibbs_admin_password($config), false);
        if ($result_code === Result::OK)
        {
          $access_token = User::verify_is_gibbs_admin(false);
        }
      }

      if ($access_token !== null)
      {
        $result_code = $access_token->get_result_code();
      }
    }
    else
    {
      $result_code = Result::CONFIG_FILE_ERROR;
    }
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return the user group with the given $id from the given array of user groups. Each user group is expected to be an
  // array, as returned by the get_licencees method of the Licencee_Data_Manager. If $id is a string, it will be
  // converted to a number. If $id was invalid, or was not found in the list of user groups, the function will return
  // null.
  protected static function get_user_group($id, $user_groups)
  {
    // Ensure the given ID is a valid number.
    $id = trim($id);
    if (!is_numeric($id))
    {
      return null;
    }
    $id = intval($id);
    if ($id < 0)
    {
      return null;
    }

    // It is. Look for the user group with that ID.
    foreach ($user_groups as $user_group)
    {
      if ($user_group->user_group_id === $id)
      {
        return $user_group;
      }
    }
    return null;
  }

  // *******************************************************************************************************************
}
?>