<?php

class Config
{
  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read and parse the config file that contains the configuration for the Gibbs administrator monthly actions.
  // Return a config object that is a two dimensional array, or null if the file could not be loaded.
  public static function read_config_file()
  {
    $ini_file = dirname($_SERVER['DOCUMENT_ROOT']) . '/gibbs_admin.ini';
    if (!file_exists($ini_file))
    {
      $ini_file = dirname(dirname($_SERVER['DOCUMENT_ROOT'])) . '/gibbs_admin.ini';
    }
    if (file_exists($ini_file))
    {
      $config = parse_ini_file($ini_file, true);
      if ($config === false)
      {
        error_log('Failed to read Gibbs administrator config file.');
        return null;
      }
      return $config;
    }
    error_log('Failed to read Gibbs administrator config file. File not found: ' . $ini_file);
    return null;
  }

  // *******************************************************************************************************************
  // Return the Gibbs administrator user name from the given config file, as returned by read_config_file, or an empty
  // string if the user name was not found.
  public static function get_gibbs_admin_user_name($config)
  {
    $user_name = $config['login']['user_name'];
    if (!$user_name)
    {
      return '';
    }
    return $user_name;
  }

  // *******************************************************************************************************************
  // Return the Gibbs administrator password from the given config file, as returned by read_config_file, or an empty
  // string if the password was not found.
  public static function get_gibbs_admin_password($config)
  {
    $password = $config['login']['password'];
    if (!$password)
    {
      return '';
    }
    return $password;
  }

  // *******************************************************************************************************************
  // Return the Nets webhook user name from the given $config file.
  public static function get_nets_webhook_user_name($config)
  {
    return $config['webhooks']['nets_webhook_user_name'];
  }

  // *******************************************************************************************************************
  // Return the Nets webhook password from the given $config file.
  public static function get_nets_webhook_password($config)
  {
    return $config['webhooks']['nets_webhook_password'];
  }

  // *******************************************************************************************************************
  // Return the access code for the Nets webhook from the given config file, as returned by read_config_file, or an
  // empty string if the password was not found.
  public static function get_nets_webhook_authorization($config)
  {
    $user_name = $config['webhooks']['nets_webhook_user_name'];
    $password = $config['webhooks']['nets_webhook_password'];
    if (!$user_name || !$password)
    {
      return '';
    }
    return 'Basic ' . base64_encode($user_name . ':' . $password);
  }

  // *******************************************************************************************************************
  // Return the Gibbs terms and conditions URL for the given $language code from the given $config file, as returned by
  // read_config_file, or null if it was not found.
  public static function get_gibbs_terms_url($language, $config)
  {
    return $config['terms'][$language];
  }

  // *******************************************************************************************************************
}