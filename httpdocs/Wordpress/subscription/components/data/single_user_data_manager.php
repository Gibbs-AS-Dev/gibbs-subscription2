<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Single_User_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The ID of the user whose data will be manipulated. The value may be left at -1, in which case the ID of the
  // currently logged-in user will be used instead.
  protected $user_id = -1;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_user_id()
  {
    if ($this->user_id === -1)
    {
      return get_current_user_id();
    }
    return $this->user_id;
  }

  // *******************************************************************************************************************

  public function set_user_id($new_value)
  {
    if (is_numeric($new_value))
    {
      $new_value = intval($new_value);
      if ($new_value >= -1)
      {
        $this->user_id = $new_value;
      }
    }
  }

  // *******************************************************************************************************************
}