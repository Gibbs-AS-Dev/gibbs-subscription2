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

/*
  This file allows you to set or retrieve a product's enabled flag. It accepts the following
  parameters:

    product_id : integer          The ID of the product to be modified or read.
    enabled : boolean             The new value of the enabled flag. This parameter is optional. If
                                  not present, the file simply returns the current value of the
                                  flag. Valid values for true are "1", "true" and "on". Valid values
                                  for false are "0" and "false".

  The file accepts both POST and GET requests. For consistency, you may wish to make a GET request
  to read the current value of the flag, and a POST request to update it, but this is not enforced.
*/

  $product_id = -1;
  $enabled = 'null';
  $products = 'null';

  // If the user is not logged in as an administrator, return an error code.
  $access_token = User::verify_is_admin(false);
  if ($access_token->is_error())
  {
    $result_code = $access_token->get_result_code();
  }
  else
  {
    // Read the product_id parameter. If not present, return an error.
    if (!Utility::integer_passed('product_id'))
    {
      $result_code = Result::MISSING_INPUT_FIELD;
    }
    else
    {
      $product_id = Utility::read_passed_integer('product_id');

      // Read the enabled parameter, if present. The value will be null if not passed.
      $enabled = Utility::read_passed_boolean('enabled');

      $settings = Settings_Manager::read_settings($access_token);
      if ($settings->get_use_test_data())
      {
        // Use dummy data. Pretend we read the flag and it was true, or that we wrote the flag and
        // it now has the value that was passed.
        if ($enabled === null)
        {
          $result_code = Result::NO_ACTION_TAKEN;
          $enabled = true;
        }
        else
        {
          $result_code = Result::OK;
          $products = Test_Data_Manager::PRODUCTS;
        }
      }
      else
      {
        $product_data = new Product_Data_Manager($access_token);
        if ($enabled === null)
        {
          // Read the enabled flag from the database.
          $enabled = $product_data->get_enabled($product_id);
          if ($enabled === null)
          {
            $result_code = Result::DATABASE_QUERY_FAILED;
          }
          else
          {
            $result_code = Result::OK;
          }
        }
        else
        {
          // Write the enabled flag to the database.
          $result_code = $product_data->set_enabled($product_id, $enabled);
          // Read products. These might change due to the change to the enabled flag.
          $products = $product_data->read();
        }
      }
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "productId": <?= $product_id ?>,
  "enabled": <?= var_export($enabled, true) ?>,
  "productsText": "<?= $products ?>"
}