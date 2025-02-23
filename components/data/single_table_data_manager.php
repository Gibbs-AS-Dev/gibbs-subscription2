<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/live_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Single_Table_Data_Manager extends Live_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The name of the database table in which data items are stored. Descendants must specify this value.
  protected $database_table = '';

  // The name of the field used by the client to post an item's ID to the server. Descendants may update this value.
  protected $id_posted_name = 'id';
  // The name of the ID field in the database. Descendants may update this value.
  protected $id_db_name = 'id';

  // The ID of the item that was last inserted into the database using the create function, or similar functions that
  // also insert things into the database. The value is -1 if no item has been created.
  protected $created_item_id = -1;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Create an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_create method.
  //
  // The item to be created can be passed as a parameter. If not, it will be read from the request.
  public function create($data_item = null)
  {
    global $wpdb;

    // Sanitise input data.
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
    // Ensure the item can be created.
    $result = $this->can_create($data_item);
    if ($result !== Result::OK)
    {
      return $result;
    }
    // Insert a new record, and report the result. The created_at field will be set to the current time by default.
    $result = $wpdb->insert($this->database_table, $data_item);
    if ($result === false)
    {
      error_log("Error while creating item in {$this->database_table}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result === 0)
    {
      error_log("Failed to insert item in {$this->database_table}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    // Store the ID of the last inserted item.
    $this->created_item_id = $wpdb->insert_id;
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Update an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_update method.
  //
  // The item to be updated, and its ID, can be passed as parameters. If not, they will be read from the request.
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
    if (!isset($id))
    {
      if (!Utility::integer_posted($this->id_posted_name))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      $id = Utility::read_posted_integer($this->id_posted_name);
    }

    // Ensure the item with that ID can be updated.
    $result = $this->can_update($id, $data_item);
    if ($result !== Result::OK)
    {
      return $result;
    }
    // Update the data item, and report the result.
    $result = $wpdb->update($this->database_table, $data_item, array($this->id_db_name => $id));
    // If the result was false, it was due to an error. If not, the $result is the number of rows affected, and 0 rows
    // affected is not necessarily an error. It just means that the item remained unchanged.
    if ($result === false)
    {
      error_log("Error while updating item {$id} in {$this->database_table}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Delete an item from the database. Return an integer result code that can be used to inform the user of the result
  // of these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_delete method.
  //
  // The ID of the item to be deleted can be passed as a parameter. If not, it will be read from the request.
  public function delete($id = null)
  {
    global $wpdb;

    // Ensure the ID is available.
    if (!isset($id))
    {
      if (!Utility::integer_posted($this->id_posted_name))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      $id = Utility::read_posted_integer($this->id_posted_name);
    }
    // Ensure the data item with that ID can be deleted.
    $result = $this->can_delete($id);
    if ($result !== Result::OK)
    {
      return $result;
    }
    // Delete the data item, and report the result.
    $result = $wpdb->delete($this->database_table, array($this->id_db_name => $id));
    if ($result === false)
    {
      error_log("Error while deleting item {$id} from {$this->database_table}: {$wpdb->last_error}.");
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return an array that describes a data item, using the information posted to the server. If an owner_id field is
  // present, it should be set to the current user, and if an updated_at field is present, it should be set to the
  // current time. If a created_at field is present, it should not be set. If any required field was not passed from the
  // client, the method should return null.
  //
  // Descendants must implement this method.
  protected function get_data_item()
  {
    return null;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the given data item can be added to the database. If not, return another result code defined
  // in utility.php. Descendants may want to override this method.
  protected function can_create($data_item)
  {
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the given data item can be used to modify the item with the given ID in the database. If not,
  // return another result code defined in utility.php. Descendants may want to override this method.
  protected function can_update($id, $data_item)
  {
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the data item with the given ID can be deleted from the database. If not, return another
  // result code defined in utility.php. Descendants may want to override this method.
  protected function can_delete($id)
  {
    return Result::OK;
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_created_item_id()
  {
    return $this->created_item_id;
  }

  // *******************************************************************************************************************
}
?>