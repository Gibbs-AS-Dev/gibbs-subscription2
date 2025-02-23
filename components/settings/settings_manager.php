<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/settings_data_manager.php';

// Class which loads settings, stores them on the session, and makes them available to whoever needs them.
class Settings_Manager
{
  // *******************************************************************************************************************
  // Read the current settings from the session, and return a Settings object. If the object was not found on the
  // session, the returned Settings object will have default values.
  public static function read_settings($access_token)
  {
    // Read settings from the session, if they are already there.
    if (isset($_SESSION['settings']))
    {
      return unserialize($_SESSION['settings']);
    }

    // The settings were not found on the session. Read them from the database and store them on the session.
    $settings_data = new Settings_Data_Manager($access_token);
    $settings = $settings_data->read();
    self::store_settings($settings);
    return $settings;
  }

  // *******************************************************************************************************************
  // Store the given $settings object on the session, replacing any that is already there. $settings may be null, in
  // which case the settings will be removed from the session entirely.
  public static function store_settings($settings)
  {
    if ($settings === null)
    {
      unset($_SESSION['settings']);
    }
    else
    {
      $_SESSION['settings'] = serialize($settings);
    }
  }

  // *******************************************************************************************************************
}
?>