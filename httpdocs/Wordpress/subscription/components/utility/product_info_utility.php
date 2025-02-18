<?php

class Product_Info_Utility
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // The key of the ptn_postmeta field in which the product readiness status is stored.
  protected const KEY_READY_STATUS = 'ready_status';
  // The key of the ptn_postmeta field in which the product notes are stored.
  protected const KEY_PRODUCT_NOTES = 'product_notes';

  // *******************************************************************************************************************
  // *** Public product readiness status methods.
  // *******************************************************************************************************************
  // Return the product readiness status for the product with the given $product_id. Use the READY_STATUS_ constants. If
  // there is no ready status stored for this product, return -1.
  public static function get_ready_status($product_id)
  {
    global $wpdb;

    $product_id = intval($product_id);
    $query = "
      SELECT
        meta_value AS ready_status
      FROM
        {$wpdb->prefix}postmeta
      WHERE
        post_id = {$product_id} AND
        meta_key = '" . self::KEY_READY_STATUS . "';
    ";
    $result = $wpdb->get_row($query, ARRAY_A);
    if (!$result || !isset($result[self::KEY_READY_STATUS]) || !is_numeric($result[self::KEY_READY_STATUS]))
    {
      return -1;
    }
    return intval($result[self::KEY_READY_STATUS]);
  }

  // *******************************************************************************************************************
  // Update the product readiness status for the product with the given $product_id to $new_status. If it does not
  // already exist in the ptn_postmeta table, set it. Return a result code to say what happened.
  public static function set_or_update_ready_status($product_id, $new_status)
  {
    // See if the ready status already exists in the database.
    $current_status = self::get_ready_status($product_id);
    if ($current_status === -1)
    {
      // The ready status did not exist. Create it.
      return self::set_ready_status($product_id, $new_status);
    }
    if ($current_status !== $new_status)
    {
      // The ready status existed, but was wrong. Update it.
      return self::update_ready_status($product_id, $new_status);
    }
    // The ready status existed, and had the correct value.
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Set the product readiness status for the product with the given $product_id to $new_status. This method assumes
  // that the value does not already exist in the ptn_postmeta table. 
  public static function set_ready_status($product_id, $new_status)
  {
    global $wpdb;

    if (!Utility::is_valid_ready_status($new_status))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $new_status = intval($new_status);
    $product_id = intval($product_id);

    $data_item = array(
      'post_id' => $product_id,
      'meta_key' => self::KEY_READY_STATUS,
      'meta_value' => $new_status
    );
    $result = $wpdb->insert($wpdb->prefix . 'postmeta', $data_item);
    if ($result === false)
    {
      error_log("Error while setting product readiness status for product with ID {$product_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Failed to insert the correct number of rows while setting product readiness status for product with ID {$product_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Delete the product readiness status for the product with the given $product_id. Return a result code to say what
  // happened.
  public static function delete_ready_status($product_id)
  {
    global $wpdb;

    $where = array(
      'post_id' => $product_id,
      'meta_key' => self::KEY_READY_STATUS,
    );
    $result = $wpdb->delete($wpdb->prefix . 'postmeta', $where);
    // If the result was false, it was due to an error. If not, the $result is the number of rows deleted.
    if ($result === false)
    {
      error_log("Error while deleting product readiness status for product with ID {$product_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result === 0)
    {
      // Nothing was deleted. This is not necessarily an error.
      return Result::NO_ACTION_TAKEN;
    }
    if ($result !== 1)
    {
      // If more than one value was deleted, that's curious enough to warrant further investigation.
      error_log("Failed to delete the correct number of rows while deleting product readiness status for product with ID {$product_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Public product notes methods.
  // *******************************************************************************************************************
  // Return a string that holds the product notes for the product with the given $product_id, or null if no notes were
  // stored for that product.
  public static function get_product_notes($product_id)
  {
    global $wpdb;

    $product_id = intval($product_id);
    $query = "
      SELECT
        meta_value AS product_notes
      FROM
        {$wpdb->prefix}postmeta
      WHERE
        post_id = {$product_id} AND
        meta_key = '" . self::KEY_PRODUCT_NOTES . "';
    ";
    $result = $wpdb->get_row($query, ARRAY_A);
    if (!$result || !isset($result[self::KEY_PRODUCT_NOTES]))
    {
      return null;
    }
    return $result[self::KEY_PRODUCT_NOTES];
  }

  // *******************************************************************************************************************
  // Update the product notes for the product with the given $product_id to $new_text. If the field does not already
  // exist in the ptn_postmeta table, set it. Return a result code to say what happened.
  public static function set_or_update_product_notes($product_id, $new_text)
  {
    // See if the product notes already exist in the database.
    $current_text = self::get_product_notes($product_id);
    if ($current_text === null)
    {
      // The product notes did not exist. Create them.
      return self::set_product_notes($product_id, $new_text);
    }
    if ($current_text !== $new_text)
    {
      // The product notes existed, but were different from the new value. Update them.
      return self::update_product_notes($product_id, $new_text);
    }
    // The product notes existed, and had the same value as requested.
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Set the product notes for the product with the given $product_id to $new_text. This method assumes that the value
  // does not already exist in the ptn_postmeta table. 
  public static function set_product_notes($product_id, $new_text)
  {
    global $wpdb;

    $new_text = strval($new_text);
    $product_id = intval($product_id);

    $data_item = array(
      'post_id' => $product_id,
      'meta_key' => self::KEY_PRODUCT_NOTES,
      'meta_value' => $new_text
    );
    $result = $wpdb->insert($wpdb->prefix . 'postmeta', $data_item);
    if ($result === false)
    {
      error_log("Error while setting product notes for product with ID {$product_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Failed to insert the correct number of rows while setting product notes for product with ID {$product_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Delete the product notes for the product with the given $product_id. Return a result code to say what happened.
  public static function delete_product_notes($product_id)
  {
    global $wpdb;

    $where = array(
      'post_id' => $product_id,
      'meta_key' => self::KEY_PRODUCT_NOTES,
    );
    $result = $wpdb->delete($wpdb->prefix . 'postmeta', $where);
    // If the result was false, it was due to an error. If not, the $result is the number of rows deleted.
    if ($result === false)
    {
      error_log("Error while deleting product notes for product with ID {$product_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result === 0)
    {
      // Nothing was deleted. This is not necessarily an error.
      return Result::NO_ACTION_TAKEN;
    }
    if ($result !== 1)
    {
      // If more than one value was deleted, that's curious enough to warrant further investigation.
      error_log("Failed to delete the correct number of rows while deleting product notes for product with ID {$product_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Protected product readiness status methods.
  // *******************************************************************************************************************
  // Update the product readiness status for the product with the given $product_id to $new_status. This method assumes
  // that the value already exists in the ptn_postmeta table. Return a result code to say what happened. The return
  // value will be Result::OK if the status was updated, Result::NO_ACTION_TAKEN if it was not, and another error code
  // if something went wrong.
  protected static function update_ready_status($product_id, $new_status)
  {
    global $wpdb;

    if (!Utility::is_valid_ready_status($new_status))
    {
      return Result::MISSING_INPUT_FIELD;
    }
    $new_status = intval($new_status);
    $product_id = intval($product_id);

    $data_item = array(
      'meta_value' => $new_status
    );
    $where = array(
      'post_id' => $product_id,
      'meta_key' => self::KEY_READY_STATUS,
    );
    $result = $wpdb->update($wpdb->prefix . 'postmeta', $data_item, $where);
    // If the result was false, it was due to an error. If not, the $result is the number of rows affected.
    if ($result === false)
    {
      error_log("Error while updating product readiness status for product with ID {$product_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result === 0)
    {
      // 0 rows affected is not necessarily an error. It just means that the item remained unchanged.
      return Result::NO_ACTION_TAKEN;
    }
    if ($result !== 1)
    {
      // If more than one value was affected, that's a mysterious and inexplicable result.
      error_log("Failed to modify the correct number of rows while updating product readiness status for product with ID {$product_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Protected product notes methods.
  // *******************************************************************************************************************
  // Update the product notes for the product with the given $product_id to $new_text. This method assumes that the
  // value already exists in the ptn_postmeta table. Return a result code to say what happened. The return value will be
  // Result::OK if the status was updated, Result::NO_ACTION_TAKEN if it was not, and another error code if something
  // went wrong.
  protected static function update_product_notes($product_id, $new_text)
  {
    global $wpdb;

    $new_text = strval($new_text);
    $product_id = intval($product_id);

    $data_item = array(
      'meta_value' => $new_text
    );
    $where = array(
      'post_id' => $product_id,
      'meta_key' => self::KEY_PRODUCT_NOTES,
    );
    $result = $wpdb->update($wpdb->prefix . 'postmeta', $data_item, $where);
    // If the result was false, it was due to an error. If not, the $result is the number of rows affected.
    if ($result === false)
    {
      error_log("Error while updating product notes for product with ID {$product_id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result === 0)
    {
      // 0 rows affected is not necessarily an error. It just means that the item remained unchanged.
      return Result::NO_ACTION_TAKEN;
    }
    if ($result !== 1)
    {
      // If more than one value was affected, that's a mysterious and inexplicable result.
      error_log("Failed to modify the correct number of rows while updating product notes for product with ID {$product_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
}
?>