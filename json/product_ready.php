<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/product_info_utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

/*
  This file allows you to set or retrieve a product's readiness status. It accepts the following
  parameters:

    product_id : integer          The ID of the product to be modified or read.
    ready_status : integer        The new value of the readiness status. This parameter is optional.
                                  If not present, the file simply returns the current ready status.
                                  Use the READY_STATUS_ constants.

  The file accepts both POST and GET requests. For consistency, you may wish to make a GET request
  to read the current ready status and a POST request to update it, but this is not enforced.
*/

  $product_id = -1;
  $ready_status = -1;
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

      // Read the ready status parameter, if present. The value will be -1 if not passed.
      $ready_status = Utility::read_passed_integer('ready_status');
      if (!Utility::is_valid_ready_status($ready_status))
      {
        $ready_status = -1;
      }

      $settings = Settings_Manager::read_settings($access_token);
      if ($settings->get_use_test_data())
      {
        // Use dummy data. Pretend we read the value and it was READY_STATUS_YES, or that we wrote
        // the value and it now has the value that was passed.
        if ($ready_status < 0)
        {
          $result_code = Result::NO_ACTION_TAKEN;
          $ready_status = Utility::READY_STATUS_YES;
        }
        else
        {
          $result_code = Result::OK;
          $products = Test_Data_Manager::PRODUCTS;
        }
      }
      else
      {
        if ($ready_status < 0)
        {
          // Read the ready status from the database.
          $ready_status = Product_Info_Utility::get_ready_status($product_id);
          if ($ready_status < 0)
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
          // Write the ready status to the database.
          $result_code = Product_Info_Utility::set_or_update_ready_status($product_id, $ready_status);
          // Read products. These might change due to the change to the ready status.
          $product_data = new Product_Data_Manager($access_token);
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
  "readyStatus": <?= $ready_status ?>,
  "productsText": "<?= $products ?>"
}