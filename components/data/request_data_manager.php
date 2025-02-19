<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';

// Customers can book on their own - but they can also submit a booking request which will be handled by administrators.
// This class deals with such manual booking requests.
class Request_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The list of unique user IDs found during the last read request.
  protected $user_ids = null;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_request', Utility::ROLE_USER, 'create');
    $this->add_action('update_request', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_request', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = 'subscription_manual_requests';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all requests accessible to the current user from the database. Return them as a string containing a Javascript
  // array declaration. At the same time, populate the object's user_ids array with the unique user IDs found among the
  // requests.
  public function read()
  {
    global $wpdb;

    $query = "
      SELECT
        id,
        user_id,
        location_id,
        category_id,
        start_date,
        comment,
        status
      FROM
        {$this->database_table}
      WHERE
        owner_id = {$this->get_user_group_user_id()}
      ORDER BY
        id;
    ";
    $results = $wpdb->get_results($query, OBJECT);

    $user_ids = array();
    $table = "[";
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $request)
      {
        // Gather the user ID from this request into the list of unique user IDs. Users are stored under their ID.
        // Adding a user that already exists makes no difference.
        $user_id = intval($request->user_id);
        $user_ids[$user_id] = $user_id;

        // Verify values.
        if (isset($request->location_id))
        {
          $location_id = $request->location_id;
        }
        else
        {
          $location_id = 'null';
        }
        if (isset($request->category_id))
        {
          $category_id = $request->category_id;
        }
        else
        {
          $category_id = 'null';
        }
        if (isset($request->start_date))
        {
          $start_date = $request->start_date;
        }
        else
        {
          $start_date = '';
        }

        // Write the request data to the Javascript table.
        $table .= "[";
        $table .= $request->id;
        $table .= ", ";
        $table .= strval($user_id);
        $table .= ", ";
        $table .= $location_id;
        $table .= ", ";
        $table .= $category_id;
        $table .= ", '";
        $table .= $start_date;
        $table .= "', '";
        $table .= $request->comment;
        $table .= "', ";
        $table .= $request->status;
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";

    // Store the list of unique user IDs found. Remove the keys, as they were only needed to avoid duplicates.
    $this->user_ids = array_values($user_ids);
    return $table;
  }

  // *******************************************************************************************************************
  // Read information about each of the users in the object's user_ids array, which is populated whenever requests are
  // read using the read method. Return a Javascript array declaration, where each entry in the array is an array with
  // the following fields:
  //   id, name, eMail, phone, address, postcode, area
  public function read_users()
  {
    if (!isset($this->user_ids))
    {
      return '[]';
    }
    $user_data = new User_Data_Manager($this->access_token);
    return $user_data->get_users($this->user_ids);
  }

  // *******************************************************************************************************************
  // Create an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_create method.
  //
  // The item to be created can be passed as a parameter. If not, it will be read from the request.
  //
  // Override to add the user_id field. This cannot be done in get_data_item, as that would overwrite the user_id every
  // time a request is updated.
  public function create($data_item = null)
  {
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
    $data_item['user_id'] = get_current_user_id();
  
    return parent::create($data_item);
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return an array that describes a request, using the information posted to the server. The owner_id field will be
  // set to the ID of the current user group's dummy user. The user_id field will be set to the current user's ID.
  // updated_at will be set to the current time. The created_at field will not be set. Mandatory fields are:
  //   comment : string
  // Optional fields are:
  //   location_id : integer    Default: null
  //   category_id : integer    Default: null
  //   start_date : date        Default: null
  //   status : integer         Default: Utility::REQ_STATUS_RECEIVED
  // Note that the user_id is not added here, as that would overwrite the user_id with the current user (who is likely
  // an administrator) every time a request is updated.
  protected function get_data_item()
  {
    if (Utility::integer_posted('location_id'))
    {
      $location_id = Utility::read_posted_integer('location_id', null);
    }
    else
    {
      $location_id = null;
    }
    if (Utility::integer_posted('category_id'))
    {
      $category_id = Utility::read_posted_integer('category_id', null);
    }
    else
    {
      $category_id = null;
    }
    if (Utility::date_posted('start_date'))
    {
      $start_date = Utility::read_posted_string('start_date');
    }
    else
    {
      $start_date = null;
    }

    $request = array(
      'owner_id' => $this->get_user_group_user_id(),
      'location_id' => $location_id,
      'category_id' => $category_id,
      'start_date' => $start_date,
      'comment' => Utility::read_posted_string('comment'),
      'status' => Utility::read_posted_integer('status', Utility::REQ_STATUS_RECEIVED),
      'updated_at' => current_time('mysql')
    );

    return $request;
  }

// *******************************************************************************************************************
}
?>