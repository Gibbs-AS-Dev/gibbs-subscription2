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
  This file allows you to read and store product notes asynchronously. Read product notes by passing a "product_id"
  parameter. Store product notes by posting the action "set_product_notes", as well as "product_id" and "product_notes"
  parameters. When you store product notes, the notes will be read back from the database and returned.

  The notes are returned as a JSON object declaration, with the following fields:
    resultCode : integer          A result code to say whether the request succeeded. Note that this will say
                                  whether authentication or storing product notes failed. However, if reading the
                                  product notes was unsuccessful, notes will be returned as an empty string without an
                                  error code.
    productId : integer           The ID of the product for which notes are supplied, or -1 if no notes are available.
    productNotes : string         The product notes for the specified product. May be an empty string, but not null.
*/

  $product_id = -1;
  $product_notes = '';

  // See if the user is logged in as a company administrator.
  $access_token = User::verify_is_admin(false);

  if ($access_token->is_error())
  {
    $result_code = $access_token->get_result_code();
  }
  else
  {
    // Read product_id parameter.
    if (!Utility::integer_posted('product_id'))
    {
      $result_code = Result::MISSING_INPUT_FIELD;
    }
    else
    {
      $product_id = Utility::read_posted_integer('product_id');
      // Read and write data.
      $settings = Settings_Manager::read_settings($access_token);
      if ($settings->get_use_test_data())
      {
        $result_code = Result::NO_ACTION_TAKEN;
        $product_id = 1006;
        $product_notes = Test_Data_Manager::PRODUCT_NOTES;
      }
      else
      {
        $product_data = new Product_Data_Manager($access_token);
        // Store product notes, if requested.
        $result_code = $product_data->perform_action();
        // Read product notes.
        $product_notes = Product_Info_Utility::get_product_notes($product_id);
        if ($product_notes === null)
        {
          $product_notes = '';
        }
      }
    }
  }

  header('Content-Type: text/json');
?>
{
  "resultCode": <?= $result_code ?>,
  "productId": <?= $product_id ?>,
  "productNotes": "<?= $product_notes ?>"
}