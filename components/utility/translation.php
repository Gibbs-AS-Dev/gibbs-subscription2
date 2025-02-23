<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

// Class which loads a set of texts from file, called a language file, and provides text to the application when
// required. Each page will have its own set of language files. When the page is displayed, we load a different language
// file for each supported language. Also, if the application is used for different purposes, there might be a separate
// set of language files for each purpose, thus allowing the application to be customised.
//
// Language files are loaded from a subdirectory of the translations directory, where the subdirectory name is the page
// name. The file name is "<page_name>__<subset>__<language>.json".
class Translation
{
  // *******************************************************************************************************************
  // *** Properties.
  // *******************************************************************************************************************

  protected $texts = [];

  protected $is_valid = false;

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Create a Text instance by loading a set of texts from a language file. Pass an empty string for $page_name in order
  // to use the name of the current page. When used in an imported PHP file, this will not work. Pass an empty string
  // for $language to use the currently selected language, as stored on the session.
  public function __construct($page_name, $subset, $language)
  {
    // Find the page name, if not provided.
    if (!isset($page_name) || !is_string($page_name) || (strlen($page_name) <= 0))
    {
      $page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
    }

    // Use the currently selected language, if not provided.
    if (!Utility::is_valid_language($language))
    {
      $language = Utility::get_current_language();
    }

    // Find the location of the requested language file.
    $file_name = $_SERVER['DOCUMENT_ROOT'] . '/subscription/translations/' . $page_name . '/' .
      $page_name . '__' . $subset . '__' . $language . '.json';

    // Read the language file if it exists.
    if (file_exists($file_name))
    {
      $file_contents = file_get_contents($file_name);
  
      // Decode the JSON data into a PHP array, and verify that the decoding was successful.
      $json_data = json_decode($file_contents, true);
      if (isset($json_data) && (json_last_error() !== JSON_ERROR_NONE))
      {
        error_log('Error while loading language file (' . $file_name . '): JSON parsing error: ' .
          json_last_error_msg());
      }
      else
      {
        // Texts were loaded successfully. Store them.
        $this->texts = $json_data;
        $this->is_valid = true;
      }
    }
    else
    {
      error_log('Error while loading language file (' . $file_name . '): file not found');
    }
  }

  // *******************************************************************************************************************
  // Return a text that can be displayed in the user interface. $string_index is a number, starting at 0, that gets a
  // particular text from the language file. If it is not found, $default_value will be used. $data is an optional array
  // of strings. If present, placeholders in the text will be replaced with the corresponding strings from $data. For
  // each <key> in $data, the placeholder $<key> will be replaced with the value from $data.
  public function get($string_index, $default_value, $data = null)
  {
    if ($this->is_valid && isset($string_index) && is_int($string_index) &&
      ($string_index >= 0) && ($string_index < count($this->texts['php_texts'])))
    {
      $result = $this->texts['php_texts'][$string_index];
    }
    else
    {
      $result = $default_value;
    }

    if (is_array($data))
    {
      $search_for = array();
      $replace_with = array();
      foreach ($data as $key => $value)
      {
        $search_for[] = '$' . $key;
        $replace_with[] = $value;
      }
      return str_replace($search_for, $replace_with, $result);
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Return a Javascript array declaration that holds all the strings that are intended to be available to Javascript
  // code. The array will be named "text".
  public function get_js_strings()
  {
    return 'var texts = ' . json_encode($this->texts['js_texts']) . ';';
  }

  // *******************************************************************************************************************
}
?>