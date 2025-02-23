<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';

// The settings data manager works on a single user group. The value is found in the access token supplied to the data
// manager when it is created, and can be read using the get_user_group_id method.
class Settings_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    global $wpdb;

    parent::__construct($new_access_token);
    // Whenever settings are updated, they're all deleted and recreated. Therefore, it makes no sense to have a separate
    // create action.
    $this->add_action('update_settings', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_settings', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = $wpdb->prefix . 'users_groups_settings';
    $this->id_db_name = 'group_id';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all settings stored for the current user group from the database. Return them as a Settings object.
  public function read()
  {
    global $wpdb;

    $results = $wpdb->get_results("
      SELECT
        setting_key AS `key`,
        setting_id AS `value`
      FROM
        {$this->database_table}
      WHERE
        {$this->id_db_name} = {$this->get_user_group_id()};
    ", ARRAY_A);

    $settings = new Settings();
    $settings->read_from_array($results);
    return $settings;
  }

  // *******************************************************************************************************************
  // Update all settings for a user group in the database. All current settings will be deleted, and the provided
  // settings will be stored. Return an integer result code that can be used to inform the user of the result of the
  // operation:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_update method.
  //
  // The ID of the user group for which settings should be updated will be fetched from the access token. You may pass
  // an $id parameter, but it will be ignored. The $data_item should be a Settings object.
  public function update($id = null, $data_item = null)
  {
    global $wpdb;

    // Sanitise input data, and ensure the ID is available as well.
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
    // Get the user group ID from the access token.
    $id = $this->get_user_group_id();

    // Ensure settings for the user group with that ID can be updated.
    $result = $this->can_update($id, $data_item);
    if ($result !== Result::OK)
    {
      return $result;
    }

    // Delete all current settings.
    $wpdb->query('START TRANSACTION');
    $result = $this->delete();
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Add new settings, and report the result.
    $values = $data_item->as_database_value_string($id);
    $result = $wpdb->query("
      INSERT INTO
        {$this->database_table} ({$this->id_db_name}, setting_key, setting_id)
      VALUES
        {$values};
    ");
    if ($result === false)
    {
      error_log("Error while adding settings for user group {$id}: {$wpdb->last_error}. Tried to insert settings: {$values}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== $data_item->get_item_count())
    {
      error_log("Failed to insert the correct number of settings for user group {$id}. Expected: {$data_item->get_item_count()}. Actual: {$result}. Tried to insert settings: {$values}.");
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // All operations succeeded.
    $wpdb->query('COMMIT');
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Delete all settings for a user group from the database. Return an integer result code that can be used to inform
  // the user of the result of the operation:
  //   OK                             The operation was successful.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_delete method.
  //
  // The ID of the user group for which settings should be deleted will be fetched from the access token. You may pass
  // an $id parameter, but it will be ignored.
  public function delete($id = null)
  {
    global $wpdb;

    // Get the user group ID from the access token.
    $id = $this->get_user_group_id();

    // Ensure settings from that user group can be deleted.
    $result = $this->can_delete($id);
    if ($result !== Result::OK)
    {
      return $result;
    }
    // Delete the data item, and report the result.
    $result = $wpdb->query("DELETE FROM {$this->database_table} WHERE {$this->id_db_name} = {$id};");
    if ($result === false)
    {
      error_log("Error while deleting settings for user group {$id}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return a Settings object, created using the information posted to the server. If any of the fields was not passed
  // from the client, the returned object will contain default settings values.
  protected function get_data_item()
  {
    $data_item = new Settings();
    $data_item->read_from_posted_data();
    return $data_item;
  }

  // *******************************************************************************************************************
}
?>