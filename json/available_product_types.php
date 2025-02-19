<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

  // See if the user is logged in as a company administrator.
  $access_token = User::verify_is_admin(false);
  if ($access_token->get_result_code() === Result::ACCESS_DENIED)
  {
    // See if the user is logged in as an ordinary user.
    $access_token = User::verify_is_user(false);
    // If the user was not logged in as an administrator or user, he can still see available product types as an
    // anonymous user. We need to know which user group to use, however. The user group ID should be found on the
    // session.
    if ($access_token->get_result_code() === Result::ACCESS_DENIED)
    {
      $user_group_id = User::get_user_group_id();
      if ($user_group_id >= 0)
      {
        $access_token = User::use_anonymously($user_group_id, false);
      }
    }
  }

  if ($access_token->is_error())
  {
    $result_code = $access_token->get_result_code();
    $available_product_types = 'null';
  }
  else
  {
    // Read data.
    $settings = Settings_Manager::read_settings($access_token);
    if ($settings->get_use_test_data())
    {
      $result_code = Result::OK;
      if (Utility::read_passed_string('action') === 'get_available_product')
      {
        $available_product_types = Test_Data_Manager::AVAILABLE_PRODUCT_TYPES_SINGLE;
      }
      else
      {
        $available_product_types = Test_Data_Manager::AVAILABLE_PRODUCT_TYPES;
      }
    }
    else
    {
      $product_data = new Product_Data_Manager($access_token);

      $result = $product_data->perform_action();
      if (is_int($result))
      {
        $result_code = $result;
        $available_product_types = 'null';
      }
      else
      {
        $result_code = Result::OK;
        $available_product_types = $result;
      }
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "availableProductTypes": <?= $available_product_types ?>
}