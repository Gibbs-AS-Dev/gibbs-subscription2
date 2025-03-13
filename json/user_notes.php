<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/role_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

/*
  This file allows you to read and store user notes asynchronously. Read user notes by passing a "user_id" parameter.
  Store user notes by posting the action "set_user_notes", as well as "user_id" and "user_notes" parameters. When you
  store user notes, the notes will be read back from the database and returned.

  The notes are returned as a JSON object declaration, with the following fields:
    resultCode : integer          A result code to say whether the request succeeded. Note that this will say
                                  whether authentication or storing user notes failed. However, if reading the user
                                  notes was unsuccessful, notes will be returned as an empty string without an error
                                  code.
    userId : integer              The ID of the user for which notes are supplied, or -1 if no notes are available.
    userNotes : string            The user notes for the specified user, user group and role. May be an empty
                                  string, but not null.
*/

  $user_id = -1;
  $user_notes = '';

  // See if the user is logged in as a company administrator.
  $access_token = User::verify_is_admin(false);

  if ($access_token->is_error())
  {
    $result_code = $access_token->get_result_code();
  }
  else
  {
    // Read and write data.
    $settings = Settings_Manager::read_settings($access_token);
    if ($settings->get_use_test_data())
    {
      $result_code = Result::NO_ACTION_TAKEN;
      $user_id = 1002;
      $user_notes = Test_Data_Manager::USER_NOTES;
    }
    else
    {
      $user_data = new Role_Data_Manager($access_token);
      $user_data->set_role_filter(Utility::ROLE_NUMBER_USER);
      $user_data->set_user_id(Utility::read_passed_integer('user_id'));
      // Store user notes, if requested.
      $result_code = $user_data->perform_action();
      // Read user notes.
      $user_notes = $user_data->get_user_notes();
      $user_id = $user_data->get_user_id();
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "userId": <?= $user_id ?>,
  "userNotes": <?= json_encode($user_notes) ?>
}