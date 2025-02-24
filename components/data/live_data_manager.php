<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Live_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The access token that holds the ID of the user group for which data is being manipulated, and the role of the user
  // using this class. Some data managers may restrict access to certain actions to certain roles.
  protected $access_token = null;

  // Data always belongs to a user group, rather than a single user. However, each user group has a dummy user, whose ID
  // is used as the owner of the group's database rows. This is the ID of that dummy user, if available. The ID may be
  // retrieved from the database, but this is not done until the value is required. Once it has been retrieved, it will
  // be stored here and re-used.
  protected $user_group_user_id = -1;

  // The actions supported by this data manager. The action is a string submitted by the client, to tell the data
  // manager what to do. This is the list of supported actions. Each entry is an array with the following values:
  //   name:        The name of the action, obviously.
  //   role:        The role that the user must have in order to perform the action.
  //   handler:     The name of the method to be called when the action is triggered. The method is expected to be part
  //                of the data manager class.
  //
  // Descendants must add (or remove) actions to the table. This is most commonly done in the constructor. Actions can
  // be added using the add_action method.
  //
  // Note that the actions table is only used for actions requested by the client. The data manager may offer more
  // functionality through methods that are called directly, such as a "read" operation.
  protected $actions = array();

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    if (isset($new_access_token) && ($new_access_token instanceof Access_Token))
    {
      $this->access_token = $new_access_token;
    }
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return the action parameter that was passed to this page, or null if nothing was passed.
  public function get_requested_action()
  {
    if (Utility::string_passed('action'))
    {
      return Utility::read_passed_string('action');
    }
    return null;
  }

  // *******************************************************************************************************************
  // Perform any action specified by the HTTP request. This may include updating the database by creating, updating or
  // deleting items, as requested. The request must be a POST, and it must have an action parameter that specifies what
  // to do. Return an integer result code that can be used to inform the user of the result of any action taken, using
  // the Result constants declared in utility.php.
  public function perform_action()
  {
    $requested_action = $this->get_requested_action();
    if (!empty($requested_action))
    {
      foreach ($this->actions as $action)
      {
        // Each action is for a single role. We can have several actions with the same name, tailored to different roles.
        // If the action name matched, but the role did not, keep searching.
        if (($requested_action === $action['name']) && ($this->access_token->get_role() === $action['role']))
        {
          // Verify that the action handler exists and can be called, then do so.
          $handler = array($this, $action['handler']);
          if (is_callable($handler))
          {
            return call_user_func($handler);
          }
          return Result::INVALID_ACTION_HANDLER;
        }
      }
    }
    return Result::NO_ACTION_TAKEN;
  }

  // *******************************************************************************************************************
  // Read all items owned by the current user from the database. Return them as a string containing a Javascript array
  // declaration. Descendants must override this method.
  public function read()
  {
    return '[]';
  }

  // *******************************************************************************************************************
  // Create an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations. Descendants must override this method.
  public function create()
  {
    return Result::NOT_IMPLEMENTED;
  }

  // *******************************************************************************************************************
  // Update an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations. Descendants must override this method.
  public function update()
  {
    return Result::NOT_IMPLEMENTED;
  }

  // *******************************************************************************************************************
  // Delete an item from the database. Return an integer result code that can be used to inform the user of the result
  // of these operations. Descendants must override this method.
  public function delete()
  {
    return Result::NOT_IMPLEMENTED;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************

  protected function add_action($action_name, $role, $handler)
  {
    if (!empty($action_name) && Utility::is_valid_role($role) && !empty($handler))
    {
      $this->actions[] = array(
        'name' => $action_name,
        'role' => $role,
        'handler' => strval($handler)
      );
    }
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_user_group_id()
  {
    if (isset($this->access_token))
    {
      return $this->access_token->get_user_group_id();
    }
    return -1;
  }

  // *******************************************************************************************************************
  // Return the user group's dummy user ID. This value is always an integer.
  public function get_user_group_user_id()
  {
    global $wpdb;

    // Read the user group's dummy user ID from the database, if required.
    $user_group_id = $this->get_user_group_id();
    if (($this->user_group_user_id === -1) && ($user_group_id !== -1))
    {
      $results = $wpdb->get_results(
        "SELECT group_admin FROM {$wpdb->prefix}users_groups WHERE id = {$user_group_id};", OBJECT);
      if (Utility::non_empty_array($results) && is_numeric($results[0]->group_admin))
      {
        $this->user_group_user_id = intval($results[0]->group_admin);
      }
    }
    return $this->user_group_user_id;
  }

  // *******************************************************************************************************************
/*
  public function set_user_group_user_id($new_value)
  {
    if (is_numeric($new_value))
    {
      $new_value = intval($new_value);
      if ($new_value >= 0)
      {
        $this->user_group_user_id = $new_value;
      }
    }
  }
*/
  // *******************************************************************************************************************

  public function get_role()
  {
    if (isset($this->access_token))
    {
      return $this->access_token->get_role();
    }
    return -1;
  }

  // *******************************************************************************************************************
}
?>