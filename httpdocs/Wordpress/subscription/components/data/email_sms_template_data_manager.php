<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Email_Sms_Template_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    global $wpdb;

    parent::__construct($new_access_token);
    $this->add_action('create_template', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_template', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_template', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = $wpdb->prefix . 'gibbs_template';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all e-mail and SMS templates owned by the current user group from the database. Return them as a string
  // containing a Javascript array declaration. Use the c.tpl constants.
  public function read()
  {
    global $wpdb;

    // Compose and submit request to get templates.
    $sql = "
      SELECT
        id AS id,
        template_name AS `name`,
        template_header AS header,
        template_content AS content,
        delay AS delay,
        `on/off` AS active,
        type AS message_type,
        trigger_type AS trigger_type
      FROM
        {$this->database_table}
      WHERE
        owner_id = {$this->get_user_group_user_id()}
      ORDER BY
        template_name;
    ";
    $results = $wpdb->get_results($sql, ARRAY_A);

    // Verify the result. Ensure that each item has the correct data type.
    if (!Utility::non_empty_array($results))
    {
      return '[]';
    }
    foreach ($results as &$template2)
    {
      $template2['id'] = intval($template2['id']);
      $template2['delay'] = intval($template2['delay']);
      $template2['active'] = (is_numeric($template2['active']) && (intval($template2['active']) === 1));
      $template2['message_type'] = intval($template2['message_type']) - 1;
      $template2['trigger_type'] = intval($template2['trigger_type']) - 1;
    }

    // Write the Javascript table.
    $table = "[";
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $template)
      {
        $table .= "[";
        $table .= $template['id'];
        $table .= ", '";
        $table .= $template['name'];
        $table .= "', '";
        $table .= ''; // ** // $template['copy_to'];
        $table .= "', '";
        $table .= $template['header'];
        $table .= "', '";
        $table .= $template['content'];
        $table .= "', ";
        $table .= $template['delay'];
        $table .= ", ";
        $table .= var_export($template['active'], true);
        $table .= ", ";
        $table .= $template['message_type'];
        $table .= ", ";
        $table .= $template['trigger_type'];
        $table .= "],";
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

  // *** // This code is a huge security breach! Do not use! Delete this when search is implemented on the client.
  // Fortunately, it's also completely unnecessary. Do this on the client. We already have all the data there.
/*
  public function get_template_list()
  {
    global $wpdb;
    $where = "";

    if(isset($_GET["search"]) && $_GET["search"] != ""){
      $search = $_GET["search"];
      $where .= " AND ((template_name like '%$search%') OR (template_header like '%$search%'))"; 
    }
    $sql = "
      SELECT *
      FROM {$this->database_table}
      WHERE owner_id = {$this->get_user_group_user_id()} {$where}
      ORDER BY template_name;
    ";
    return $wpdb->get_results($sql, OBJECT);
  }
*/
  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return an array that describes a template, using the information posted to the server.The method expects the
  // following fields to be posted:
  //   name : string            Mandatory.
  //   header : string          Mandatory if message_type is MESSAGE_TYPE_EMAIL.
  //   content : string         Mandatory.
  //   delay : integer          Mandatory.
  //   active : boolean         Pass "1", "true" or "on" for true. If not passed, or if any other value is passed, it
  //                            will be interpreted as false.
  //   message_type : integer   Mandatory. Use the MESSAGE_TYPE_ constants.
  //   trigger_type : integer   Mandatory. Use the TRIGGER_TYPE_ constants.
  //
  // If any of the fields was not passed from the client, the method will return null.
  protected function get_data_item()
  {
    if (!Utility::strings_posted(array('name', 'content')) ||
      !Utility::integers_posted(array('message_type', 'delay', 'trigger_type')))
    {
      return null;
    }
    // Read active flag.
    $active = Utility::read_posted_boolean('active') === true;
    // Read message type, and header, if required.
    $message_type = Utility::read_posted_integer('message_type');
    if ($message_type === Utility::MESSAGE_TYPE_EMAIL)
    {
      if (!Utility::strings_posted(array('copy_to', 'header')))
      {
        return null;
      }
      $copy_to = Utility::read_posted_string('copy_to');
      if (empty($copy_to))
      {
        return null;
      }
      $header = Utility::read_posted_string('header');
      if (empty($header))
      {
        return null;
      }
    }
    else
    {
      $copy_to = '';
      $header = '';
    }

    $template = array(
      'owner_id' => $this->get_user_group_user_id(),
      'template_name' => Utility::read_posted_string('name'),
      'template_header' => $header,
      'template_content' => Utility::read_posted_string('content'),
      // *** // 'email_cc' => $copy_to,
      'delay' => Utility::read_posted_integer('delay'),
      'on/off' => ($active ? 1 : 0),
      'type' => $message_type + 1,
      'trigger_type' => Utility::read_posted_integer('trigger_type') + 1,
      'updated_at' => current_time('mysql')
    );
    if (!Utility::non_empty_strings($template, array('template_name', 'template_content')))
    {
      return null;
    }
    return $template;
  }

  // *******************************************************************************************************************
}
?>