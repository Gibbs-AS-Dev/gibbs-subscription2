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

  // If the user is not logged in as an ordinary user, return an access token with an error code.
  $access_token = User::verify_is_user(false);
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
      $available_product_types = Test_Data_Manager::AVAILABLE_PRODUCT_TYPES;
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