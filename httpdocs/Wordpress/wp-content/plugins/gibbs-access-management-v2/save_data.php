<?php
class Access_management_v2 {

  public static function action_init()
  {

    add_action('wp_ajax_delete_csv_file', array('Access_management_v2', 'delete_csv_file'));
    add_action('wp_ajax_nopriv_delete_csv_file', array('Access_management_v2', 'delete_csv_file'));

    add_action('wp_ajax_booking_tab', array('Access_management_v2', 'booking_tab'));
    add_action('wp_ajax_nopriv_booking_tab', array('Access_management_v2', 'booking_tab'));

    add_action('wp_ajax_booking_user_group_selected_id', array('Access_management_v2', 'booking_user_group_selected_id'));
    add_action('wp_ajax_nopriv_booking_user_group_selected_id', array('Access_management_v2', 'booking_user_group_selected_id'));

    add_action('wp_ajax_save_active_column', array('Access_management_v2', 'save_active_column'));
    add_action('wp_ajax_nopriv_save_active_column', array('Access_management_v2', 'save_active_column'));

    add_action('wp_ajax_gibbs_access_management_data', array('Access_management_v2', 'gibbs_access_management_data'));
    add_action('wp_ajax_nopriv_gibbs_access_management_data', array('Access_management_v2', 'gibbs_access_management_data'));
    
    add_action('wp_ajax_fetch_accesscard_data', ['Access_management_v2', 'fetch_accesscard_data']);
    add_action('wp_ajax_nopriv_fetch_accesscard_data', ['Access_management_v2', 'fetch_accesscard_data']);

    add_action('wp_ajax_delete_accesscard_data', ['Access_management_v2', 'delete_accesscard_data']);
    add_action('wp_ajax_nopriv_delete_accesscard_data', ['Access_management_v2', 'delete_accesscard_data']);

    
  }


  public function get_columns() {
      // Legg til logikken for å oppdatere "lock_status" hvis den er tom her
      $lock_status = 'Online'; // Sett standardverdi


      return $columns = array(
          "ordre_id" => "Ordre ID",
          "listing_name" => "Annonse",
          "date" => "Dato",
          "name" => "Navn",
          "email" => "E-post",
          "phone_number" => "Telefon",
          "payment_status" => '<span data-toggle="tooltip" title="Din verktøytips-tekst her">Booking status</span>',
          "lock_status" => "SMS Timer", // Dette er den oppdaterte "lock_status"
          "access_code" => "Tilgangskode",
          "sms_time" => "Send sms x min før booking",
          "sms_status" => "Sms status"
      );
  }
  
  public function get_active_columns() {
      return [
          "ordre_id",
          "listing_name",
          "date",
          "name",
          "email",
          "phone_number",
          "payment_status",
          "lock_status",
          "access_code",
          "sms_time",
          "sms_status"
      ];
  }

  public function single_get_columns() {
      return [
          "listing" => "Annonse",
          "booking_id" => "Booking ID",
          "date" => "Dato",
          "price" => "Pris",
          "status" => "Status"
      ];
  }

  public function single_get_active_columns() {
      return ["listing", "booking_id", "date", "price", "status"];
  }



  public function getUserListings(){

      global $wpdb;

      $current_user = wp_get_current_user();
     
      $active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

      if($active_group_id != ""){
        $group_id = $active_group_id;
      }else{
        $group_id = "0";
      }


      $listing_ids = array();

      $group_admin = get_group_admin();

      if($group_admin != ""){

        $posts_table = $wpdb->prefix . 'posts';  // table name
        $posts_table_sql = "select ID from `$posts_table` where post_type='listing' AND post_status = 'publish' AND post_author = $group_admin";
        $posts_data = $wpdb->get_results($posts_table_sql);

        foreach ($posts_data as $key => $posts_da) {
          $listing_ids[] = $posts_da->ID;
        }
        
      }else{
          $posts_table = $wpdb->prefix . 'posts';  // table name
          $posts_table_sql = "select ID from `$posts_table` where post_type='listing' AND post_status = 'publish' AND post_author = ".get_current_user_id();
          $posts_data = $wpdb->get_results($posts_table_sql);



         

          foreach ($posts_data as $key => $posts_da) {
            $listing_ids[] = $posts_da->ID;
          }
      }

      return $listing_ids;

  }


  public function gibbs_access_management_data()
  {


    global $wpdb;
    
    $booking_data = Access_management_v2::resultData();

    $columns  = Access_management_v2::get_columns();

    $active_columns  = Access_management_v2::get_active_columns();

    

    $page = ! empty( $_POST['page'] ) ? (int) $_POST['page'] : 1;
    $total = count( $booking_data ); //total items in array    
    $limit = 10; //per page    
    $totalPages = ceil( $total/ $limit ); //calculate total pages
    $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
    $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
    $offset = ($page - 1) * $limit;
    if( $offset < 0 ) $offset = 0;

    $booking_data = array_slice( $booking_data, $offset, $limit );

    



    ob_start();
    //include the specified file
    require (__DIR__."/booking_table.php");

    $content = ob_get_clean();

    if(empty($booking_data)){

        $content  = "<div class='empty_div_new'><div class='inner_empty'>No data</div></div>";

    }


    $data = array("content"=>$content, "booking_data"=>$booking_data);

    wp_send_json($data);
    

    exit();
  }

  public function resultData(){
    

        global $wpdb;

        $sql = '';

        $user_idss = array();

        $check_user_ids = "";

        if(isset($_POST['listing_ids']) && !empty($_POST['listing_ids'])){

            $listing_ids = implode(",", $_POST['listing_ids']);

            $sql .= " AND listing_id IN (".$listing_ids.")";
        }

        if(isset($_POST['search_text']) && !empty($_POST['search_text'])){

          $search_text = $_POST['search_text'];




            $sql .= " AND (order_id = '$search_text' OR listing_id = '$search_text' OR listing_name LIKE '%$search_text%' OR name LIKE '%$search_text%' OR email LIKE '%$search_text%' OR access_code LIKE '%$search_text%' OR phone_number LIKE '%$search_text%')";
        }

        
        if(isset($_POST['startDataSql']) && $_POST['startDataSql'] != "" && isset($_POST['endDataSql']) && $_POST['endDataSql'] != ""){

          if($_POST['date_close'] != "true"){

            $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $_POST['startDataSql'] ) ) ) );
            $date_end = esc_sql ( date( "Y-m-d"." 23:59:59", strtotime( $wpdb->esc_like( $_POST['endDataSql'] ) ) ) );

            $sql .= " AND (`start_datetime` >= '$date_start' AND `end_datetime` <= '$date_end')";

          }

              
        }
        $get_user_listings = Access_management_v2::getUserListings();

        $listing_ids = implode(",", $get_user_listings);

        $sql .= " AND listing_id IN ($listing_ids)";

        $sql .= " ORDER BY id DESC";
        

        $sql = "SELECT * FROM `" . $wpdb->prefix . "access_management` WHERE listing_id != '' $sql"; 
        $results  = $wpdb->get_results($sql);

        //echo "<pre>"; print_r($results); die;

        

      return $results;
  }
  public function get_column_definitions() {
      return [
          'locky' => [
              'id' => 'ID', 
              'provider' => 'Leverandør', 
              'listing_name' => 'Annonse', 
              'lock_id' => 'Enhets ID', 
              'jwt' => 'JWT', 
              'type' => 'Locky tech', 
              'sms_time' => 'SMS Time', 
              'server_address' => 'Server Address',
              'timezone_add_time_before' => 'Timezone Add Time Before', 
              'timezone_add_time_after' => 'Timezone Add Time After', 
              'sms_content' => 'SMS Content', 
              'actions' => 'Actions',
          ],
          'igloohome' => [
              'id' => 'ID', 
              'provider' => 'Leverandør', 
              'listing_name' => 'Listing Name', 
              'lock_id' => 'Lock ID', 
              'timezone_add_time_before' => 'Timezone Add Time Before', 
              'timezone_add_time_after' => 'Timezone Add Time After', 
              'sms_content' => 'SMS Content', 
              'sms_time' => 'SMS Time', 
              'actions' => 'Actions',
          ],
          'shelly' => [
              'id' => 'ID', 
              'provider' => 'Provider', 
              'listing_name' => 'Listing Name', 
              'lock_id' => 'Lock ID', 
              'server_address' => 'Server Address', 
              'timezone_add_time_before' => 'Timezone Add Time Before', 
              'timezone_add_time_after' => 'Timezone Add Time After', 
              'sms_content' => 'SMS Content', 
              'sms_time' => 'SMS Time', 
              'jwt' => 'JWT', 
              'actions' => 'Actions',
          ],
          'unloc' => [
              'id' => 'ID', 
              'provider' => 'Provider', 
              'listing_name' => 'Listing Name', 
              'lock_id' => 'Lock ID', 
              'server_address' => 'Server Address', 
              'timezone_add_time_before' => 'Timezone Add Time Before', 
              'timezone_add_time_after' => 'Timezone Add Time After', 
              'sms_content' => 'SMS Content', 
              'sms_time' => 'SMS Time', 
              'jwt' => 'JWT', 
              'project_id' => 'Project ID',
              'actions' => 'Actions',
          ],
          'default' => [
              'id' => 'ID', 
              'provider' => 'Leverandør', 
              'listing_name' => 'Annonse', 
              'lock_id' => 'Enhets ID', 
              'timezone_add_time_before' => 'Buffertid før booking', 
              'timezone_add_time_after' => 'Buffertid etter booking',
              'sms_content' => 'Epost/SMS innhold', 
              'sms_time' => 'Utsendingstid for epost/SMS',
              'actions' => 'Handling',
          ]
      ];
  }

  public function fetch_accesscard_data() {
      global $wpdb;

      $draw = $_POST['draw'];
      $start = $_POST['start'];
      $length = $_POST['length'];
      $searchValue = $_POST['searchData'];
      $provider = $_POST['provider'];

      $get_column_definitions = Access_management_v2::get_column_definitions();
      $columns = $get_column_definitions[$provider];

      $columns = array_keys($get_column_definitions["default"]);

      $orderColumnIndex = $_POST['order'][0]['column'];
      $orderColumn = $columns[$orderColumnIndex];
      $orderDir = $_POST['order'][0]['dir'];

      $table_name = $wpdb->prefix . 'access_management_match';

      $ptn_users_groups = $wpdb->prefix . 'users_groups';

        $active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $ptn_users_groups WHERE id = %d", // %d is the placeholder for integers
                $active_group_id
            )
        );

        $owner_ids = [];

        if ($row && isset($row->id)) {

            if(isset($row->superadmin) && $row->superadmin != ""){
                $owner_ids[] = $row->superadmin;
            }
            if(isset($row->group_admin) && $row->group_admin != ""){
                $owner_ids[] = $row->group_admin;
            }
        }
        if(!empty($owner_ids)){
            $owner_ids = implode(",",$owner_ids);
        }else{
            $owner_ids = 999999999999;
        }

      // Base query
      $query = "SELECT * FROM $table_name WHERE 1=1 AND owner_id in ($owner_ids)";
      $totalRecordsQuery = "SELECT COUNT(*) FROM $table_name WHERE 1=1 AND owner_id in ($owner_ids)";

      // Search functionality
      if (!empty($searchValue)) {
          $searchQuery = " AND (provider LIKE '%s' OR listing_name LIKE '%s' OR lock_id LIKE '%s' OR jwt LIKE '%s' OR server_address LIKE '%s' OR sms_content LIKE '%s' OR project_id LIKE '%s')";
          $searchTerm = '%' . $wpdb->esc_like($searchValue) . '%';
          $query .= $wpdb->prepare($searchQuery, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
          $totalRecordsQuery .= $wpdb->prepare($searchQuery, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
      }

      // Add ordering
      if (in_array($orderColumn, $columns)) {
          $query .= " ORDER BY $orderColumn $orderDir";
      }

      // Add pagination
      $query .= $wpdb->prepare(" LIMIT %d, %d", $start, $length);

      // Execute queries
      $data = $wpdb->get_results($query, ARRAY_A);
      $totalRecords = $wpdb->get_var($totalRecordsQuery);

      // Prepare response
      $response = [
          "draw" => intval($draw),
          "recordsTotal" => intval($totalRecords),
          "recordsFiltered" => intval($totalRecords),
          "data" => $data
      ];

      echo json_encode($response);
      wp_die();
  }

  public function delete_accesscard_data() {
      global $wpdb;

      // Validate the ID
      $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
      if (!$id) {
          wp_send_json_error(['message' => 'Invalid ID provided.']);
      }

      // Delete the record
      $table_name = $wpdb->prefix . 'access_management_match';
      $deleted = $wpdb->delete($table_name, ['id' => $id], ['%d']);

      if ($deleted) {
          wp_send_json_success(['message' => 'Access card deleted successfully.']);
      } else {
          wp_send_json_error(['message' => 'Failed to delete the access card.']);
      }
  }



}
