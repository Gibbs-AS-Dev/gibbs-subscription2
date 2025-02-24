<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Location_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************

  // The number of locations that were read from the database during the last call to read.
  protected $location_count = -1;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_location', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_location', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_location', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = 'subscription_product_location';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all locations accessible to the current user from the database. Return them as a string containing a
  // Javascript array declaration. $location_list is the set of locations, as read from the database using the
  // get_location_list method. $location_list is optional.
  public function read($location_list = null)
  {
    $this->location_count = 0;
    if ($location_list === null)
    {
      $location_list = $this->get_location_list();
    }
    $table = "[";
    if (Utility::non_empty_array($location_list))
    {
      $this->location_count = count($location_list);
      foreach ($location_list as $location)
      {
        $table .= "[";
        $table .= $location->id;
        $table .= ", '";
        $table .= $location->name;
        $table .= "', '";
        $table .= $location->address;
        $table .= "', '";
        $table .= $location->zip_code;
        $table .= "', '";
        $table .= $location->city;
        $table .= "', '";
        $table .= $location->country;
        $table .= "', '";
        $table .= $location->opening_hours;
        $table .= "', '";
        $table .= $location->services;
        $table .= "'],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Read all locations accessible to the current user from the database. Return an array of objects, each of which has
  // the following fields:
  //   id, name, address, zip_code, city, country, opening_hours, services
  public function get_location_list()
  {
    global $wpdb;

    return $wpdb->get_results("
      SELECT id, name, address, zip_code, city, country, opening_hours, services
      FROM {$this->database_table}
      WHERE owner_id = {$this->get_user_group_user_id()}
      ORDER BY name;
    ", OBJECT);
  }

  // *******************************************************************************************************************
  // If the given $result_row has a field called "location_id", add the value of that field to the array in the given
  // $item stored under the key "for_locations". If necessary, create the array. The location ID is stored with itself
  // as the key, so it can be found later on. If the $result_row has no location ID, nothing will happen.
  public static function add_location_to_item(&$item, $result_row)
  {
    // Add location, if present.
    if (isset($result_row['location_id']))
    {
      // Add a table for locations, if not already present.
      if (!isset($item['for_locations']))
      {
        $item['for_locations'] = array();
      }
      // Store the location. If we already had it, nothing will have changed.
      $location_id = intval($result_row['location_id']);
      $item['for_locations'][$location_id] = $location_id;
    }
  }

  // *******************************************************************************************************************
  // Read location IDs posted to the server. Return false if insufficient information was posted. Return null if all
  // location IDs were posted. The null value typically signifies that the entity in question applies to all locations.
  // Otherwise, return an array of location IDs. Note that the array might be empty. The method expects the following
  // fields to be posted:
  //   for_all_locations : integer                1 for true, 0 for false. Remaining fields are only present if this
  //                                              value is 0.
  //   location_count : integer                   The total number of locations in existence - not the number of
  //                                              location IDs that were actually posted.
  //   for_location_$1 : integer                  $1 represents a number from 0 to (location_count - 1). Each item may
  //                                              or may not hold a location ID. If posted, it signifies that the
  //                                              location with that ID was selected.
  public static function read_posted_locations()
  {
    // Read the radio button setting.
    if (!Utility::integer_posted('for_all_locations'))
    {
      return false;
    }
    $for_all_locations = Utility::read_posted_integer('for_all_locations');

    // If the entity applies to all locations, return null to signify this.
    if ($for_all_locations === 1)
    {
      return null;
    }

    // If the entity applies to only some locations, check all potential locations, and record the ones that were
    // selected.
    if ($for_all_locations === 0)
    {
      if (!Utility::integer_posted('location_count'))
      {
        return false;
      }
      // The location count is the total number of locations in existence, not the number of posted items.
      $location_count = Utility::read_posted_integer('location_count');
      $for_locations = array();
      for ($i = 0; $i < $location_count; $i++)
      {
        $parameter = 'for_location_' . strval($i);
        if (Utility::integer_posted($parameter))
        {
          $for_locations[] = Utility::read_posted_integer($parameter);
        }
        // If the location was not selected, the value will not be posted. This does not indicate an error.
      }
      // If all locations were posted, return null.
      if (count($for_locations) === $location_count)
      {
        return null;
      }
      return $for_locations;
    }
    return false;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return an array that describes a location, using the information posted to the server. The owner_id field will be
  // set to the current user, and updated_at to the current time. The created_at field will not be set. If either of the
  // fields was not passed from the client, the method will return null.
  protected function get_data_item()
  {
    if (!Utility::strings_posted(array('name', 'address', 'postcode', 'town', 'country', 'opening_hours', 'services')))
    {
      return null;
    }

    $location = array(
      'owner_id' => $this->get_user_group_user_id(),
      'name' => Utility::read_posted_string('name'),
      'address' => Utility::read_posted_string('address'),
      'zip_code' => Utility::read_posted_string('postcode'),
      'city' => Utility::read_posted_string('town'),
      'country' => Utility::read_posted_string('country'),
      'opening_hours' => Utility::read_posted_string('opening_hours'),
      'services' => Utility::read_posted_string('services'),
      'updated_at' => current_time('mysql')
    );
    if (!Utility::non_empty_strings($location, array('name', 'address', 'zip_code', 'city', 'country')))
    {
      return null;
    }

    return $location;
  }

  // *******************************************************************************************************************
  // Return Result::OK if the data item with the given ID can be deleted from the database. If not, return another
  // result code defined in utility.php. Descendants may want to override this method.
/*
  protected function can_delete($id)
  {
      // *** // Check whether the location has any products. If it has, return false so that the user will get a legible
             // error message. The database will prevent the deletion, in any case.
    return Result::OK;
  }
*/

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************
  // Return the $location_count property.
  public function get_location_count()
  {
    return $this->location_count;
  }

  // *******************************************************************************************************************
}
?>