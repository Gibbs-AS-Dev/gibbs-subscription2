<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/category_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $categories = Test_Data_Manager::CATEGORIES;
    $product_types = Test_Data_Manager::PRODUCT_TYPES;
  }
  else
  {
    $category_data = new Category_Data_Manager($access_token);
    $product_type_data = new Product_Type_Data_Manager($access_token);
    // Handle create, update and delete operations. We can't do operations on both categories and
    // product types at the same time. If we attempted to modify categories, we don't even need to
    // try the product types.
    $result_code = $category_data->perform_action();
    if ($result_code === Result::NO_ACTION_TAKEN)
    {
      $result_code = $product_type_data->perform_action();
    }
    // Read categories and product types to be displayed to the user.
    $categories = $category_data->read();
    $product_types = $product_type_data->read();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - bodtyper</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/admin_product_types.js"></script>
    <script type="text/javascript">

var resultCode = <?= $result_code ?>;
var categories = <?= $categories ?>;
var productTypes = <?= $product_types ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_expanding_sidebar() ?>
    <?= Header::get_header_with_user_info('Kategorier og bodtyper', 'fa-box-check') ?>
    <div class="content">
      <div class="toolbar">
        <h3>Kategorier</h3>
        <br />
        <button type="button" class="wide-button" onclick="displayEditCategoryDialogue(-1);"><i class="fa-solid fa-box-check"></i> Lag ny kategori</button>
      </div>
      <div id="categoriesBox">
        &nbsp;
      </div>
    </div>
    <div class="content">
      <div class="toolbar">
        <h3>Bodtyper</h3>
        <br />
        <button type="button" class="wide-button" onclick="displayEditProductTypeDialogue(-1);"><i class="fa-solid fa-box-check"></i> Lag ny bodtype</button>
      </div>
      <div id="productTypesBox">
        &nbsp;
      </div>
    </div>

    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editCategoryDialogue" class="dialogue edit-category-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editProductTypeDialogue" class="dialogue edit-product-type-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
