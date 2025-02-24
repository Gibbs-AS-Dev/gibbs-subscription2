<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

// This class can be used in its entirety without setting a particular group ID. This class deals with user groups that
// have a licence for the Gibbs self storage application. In order to process all user groups, use the
// User_Group_Data_Manager.
class Licencee_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    global $wpdb;

    parent::__construct($new_access_token);
    $this->add_action('create_licence', Utility::ROLE_GIBBS_ADMIN, 'create');
    $this->add_action('update_licence', Utility::ROLE_GIBBS_ADMIN, 'update');
    $this->add_action('delete_licence', Utility::ROLE_GIBBS_ADMIN, 'delete');
    $this->database_table = $wpdb->prefix . 'users_and_users_groups_licence';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all licencees - that is, all user groups that have a licence for "Minilager". This method does not depend on
  // having the correct group ID. Return null if an error occured, or an array of PHP objects with the following fields:
  //   group_licence_id : integer     The ID of the database entry that links the user group with the licence.
  //   user_group_name : string       The name of the user group.
  //   user_group_id : integer        The ID of the user group.
  //   licence_status : integer       0: inactive, 1: active.
  //   licence_id : integer           The ID of the "Minilager" licence.
  public function get_licencees()
  {
    global $wpdb;

    $query = "
      SELECT
        ugl.id AS group_licence_id,
        ug.name AS user_group_name,
        ug.id AS user_group_id,
        ug.group_admin AS user_group_user_id,
        ugl.licence_is_active AS licence_status,
        ugl.licence_id AS licence_id
      FROM {$wpdb->prefix}users_groups ug
      JOIN {$this->database_table} ugl ON ug.id = ugl.users_groups_id
      JOIN {$wpdb->prefix}users_groups_licence l ON ugl.licence_id = l.id
      WHERE l.licence_name = 'Minilager'
      ORDER BY user_group_name;
    ";
    $results = $wpdb->get_results($query, OBJECT);
    // The data type of various fields might be "string", even though they are stored as numbers in the database.
    // Convert them to an integer.
    if (is_array($results))
    {
      // Note that we do not need to use the & operator to pass by reference. Objects, unlike arrays (crazy language!),
      // are passed by reference by default in PHP.
      foreach ($results as $licencee)
      {
        $licencee->group_licence_id = intval($licencee->group_licence_id);
        $licencee->user_group_id = intval($licencee->user_group_id);
        $licencee->user_group_user_id = intval($licencee->user_group_user_id);
        $licencee->licence_status = intval($licencee->licence_status);
        $licencee->licence_id = intval($licencee->licence_id);
      }
    }

    return $results;
  }

  // *******************************************************************************************************************
  // Read all licencees - that is, all user groups that have a licence for "Minilager". Return them as a string
  // containing a Javascript array declaration. Use the c.lic column constants.
  public function read()
  {
    $results = self::get_licencees();
    $table = "[";
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $licencee)
      {
        $table .= "[";
        $table .= $licencee->group_licence_id;
        $table .= ", '";
        $table .= $licencee->user_group_name;
        $table .= "', ";
        $table .= $licencee->user_group_id;
        $table .= ", ";
        $table .= $licencee->user_group_user_id;
        $table .= ", ";
        $table .= ($licencee->licence_status === 1 ? 'true' : 'false');
        $table .= ", ";
        $table .= $licencee->licence_id;
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Create an active Gibbs self storage licence for the user group with the given ID. Return a result code to say what
  // happened.
  public function create_licence_for_user_group($user_group_id)
  {
    if (is_numeric($user_group_id))
    {
      $licence = array(
        'users_groups_id' => intval($user_group_id),
        'licence_id' => self::get_gibbs_abonnement_licence_id(),
        'licence_is_active' => 1
      );
      return $this->create($licence);
    }
    return Result::MISSING_INPUT_FIELD;
  }

  // *******************************************************************************************************************
  // Return true if the user group with the given $user_group_id has an active 'Minilager' licence. Return false if it
  // has a 'Minilager' licence, but the licence is not active. Return null if the user group does not exist, does not
  // have a 'Minilager' licence at all, or the query failed.
  public static function has_active_licence($user_group_id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT ugl.licence_is_active AS licence_status
        FROM {$wpdb->prefix}users_and_users_groups_licence ugl
        JOIN {$wpdb->prefix}users_groups_licence l ON ugl.licence_id = l.id
        WHERE (ugl.users_groups_id = %d) AND (l.licence_name = 'Minilager');
      ",
      $user_group_id
    );
    $results = $wpdb->get_results($sql, OBJECT);
    if (!Utility::non_empty_array($results))
    {
      return null;
    }
    return intval($results[0]->licence_status) === 1;
  }

  // *******************************************************************************************************************
  // Return the ID of the "Gibbs minilager" licence (named "Minilager"), or -1 if it could not be found.
  public static function get_gibbs_abonnement_licence_id()
  {
    global $wpdb;

    $results = $wpdb->get_results(
      "SELECT id FROM {$wpdb->prefix}users_groups_licence WHERE licence_name = 'Minilager';", OBJECT);
    if (Utility::non_empty_array($results))
    {
      return $results[0]->id;
    }
    return -1;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return an array that describes a location, using the information posted to the server. If any of the fields was not
  // passed from the client, the method will return null.
  protected function get_data_item()
  {
    $is_active = Utility::read_posted_boolean('is_active');
    if (!isset($is_active) || !Utility::integers_posted(array('user_group_id', 'licence_id')))
    {
      return null;
    }
    $licence = array(
      'users_groups_id' => Utility::read_posted_integer('user_group_id'),
      'licence_id' => Utility::read_posted_integer('licence_id'),
      'licence_is_active' => ($is_active ? 1 : 0)
    );

    return $licence;
  }

  // *******************************************************************************************************************
}
?>