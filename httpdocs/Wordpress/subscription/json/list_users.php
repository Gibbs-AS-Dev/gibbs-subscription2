<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/role_data_manager.php';

  // See if the user is logged in as a company administrator.
  $access_token = User::verify_is_admin(false);

  if ($access_token->is_error())
  {
    $result_code = $access_token->get_result_code();
    $users = 'null';
  }
  else
  {
    // Read data.
    $settings = Settings_Manager::read_settings($access_token);
    if ($settings->get_use_test_data())
    {
      $result_code = Result::NO_ACTION_TAKEN;
      $users = Test_Data_Manager::USERS_SIMPLE;
    }
    else
    {
      $user_data = new Role_Data_Manager($access_token);
      $user_data->set_role_filter(Utility::ROLE_NUMBER_USER);
      $result_code = Result::NO_ACTION_TAKEN;
      $users = $user_data->read_simple();
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "users": <?= $users ?>
}