<?php

class Gibbs_Admin_Calendar_API
{

    public static function action_init()
    {
        add_action('wp_ajax_save_cal_filters', array('Gibbs_Admin_Calendar_API', 'save_cal_filters'));

        add_action('wp_ajax_search_booking_data', array('Gibbs_Admin_Calendar_API', 'search_booking_data'));

        add_action('wp_ajax_nopriv_get_booking_data', array('Gibbs_Admin_Calendar_API', 'get_booking_data'));
        add_action('wp_ajax_get_booking_data', array('Gibbs_Admin_Calendar_API', 'get_booking_data'));

        add_action('wp_ajax_nopriv_wpm_add_record', array('Gibbs_Admin_Calendar_API', 'wpm_add_record'));
        add_action('wp_ajax_wpm_add_record', array('Gibbs_Admin_Calendar_API', 'wpm_add_record'));

        add_action('wp_ajax_nopriv_wpm_update_record', array('Gibbs_Admin_Calendar_API', 'wpm_update_record'));
        add_action('wp_ajax_wpm_update_record', array('Gibbs_Admin_Calendar_API', 'wpm_update_record'));

        add_action('wp_ajax_nopriv_wpm_delete_record', array('Gibbs_Admin_Calendar_API', 'wpm_delete_record'));
        add_action('wp_ajax_wpm_delete_record', array('Gibbs_Admin_Calendar_API', 'wpm_delete_record'));


        add_action('wp_ajax_nopriv_wpm_get_booking_info', array('Gibbs_Admin_Calendar_API', 'get_booking_info'));

        add_action('wp_ajax_wpm_get_booking_info', array('Gibbs_Admin_Calendar_API', 'get_booking_info'));

        add_action('wp_ajax_get_booking_by_user', array('Gibbs_Admin_Calendar_API', 'get_booking_by_user'));

        add_action('wp_ajax_check_customer_email', array('Gibbs_Admin_Calendar_API', 'check_customer_email'));

        add_action('wp_ajax_addEventCustomer', array('Gibbs_Admin_Calendar_API', 'addEventCustomer'));

        add_action('wp_ajax_get_customer_list', array('Gibbs_Admin_Calendar_API', 'get_customer_list'));

        add_action('wp_ajax_get_custom_fields_for_calender_mobiscroll', array('Gibbs_Admin_Calendar_API', 'get_custom_fields_for_calender_mobiscroll'));

        add_action('wp_ajax_edit_field_save_for_calender_mobiscroll', array('Gibbs_Admin_Calendar_API', 'edit_field_save_for_calender_mobiscroll'));

        add_action('wp_ajax_save_listing_filter_template_mobiscroll', array('Gibbs_Admin_Calendar_API', 'save_listing_filter_template_mobiscroll'));

        add_action('wp_ajax_update_calender_filter_template_mobiscroll', array('Gibbs_Admin_Calendar_API', 'update_calender_filter_template_mobiscroll'));

        add_action('wp_ajax_change_template', array('Gibbs_Admin_Calendar_API', 'change_template'));

        add_action('wp_ajax_getEventAjaxData', array('Gibbs_Admin_Calendar_API', 'getEventAjaxData'));
        add_action('wp_ajax_getPayLink', array('Gibbs_Admin_Calendar_API', 'getPayLink'));
        add_action('wp_ajax_check_refund', array('Gibbs_Admin_Calendar_API', 'check_refund'));
        add_action('wp_ajax_get_user_info', array('Gibbs_Admin_Calendar_API', 'get_user_info'));
        add_action('wp_ajax_get_sms_email_info', array('Gibbs_Admin_Calendar_API', 'get_sms_email_info'));

        add_action('wp_ajax_save_template_auto_checkbox', array('Gibbs_Admin_Calendar_API', 'save_template_auto_checkbox'));
    }
    public function get_user_info(){

        global $wpdb;

        $booking_id = $_POST["booking_id"];
        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $record = $wpdb->get_row("SELECT * FROM $booking_table where id=".$booking_id);
        if(isset($record->bookings_author)){

            $user_id = $record->bookings_author;
            $userdata = get_userdata($user_id);

            ob_start();
                require(GIBBS_CALENDAR_PATH."modals/user-form-data.php");
            echo ob_get_clean();

        }
        
        die;
    }
    public function get_sms_email_info(){

        global $wpdb;

        $booking_id = $_POST["booking_id"];
        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $record = $wpdb->get_row("SELECT * FROM $booking_table where id=".$booking_id);
        if(isset($record->bookings_author)){

            $user_id = $record->bookings_author;
            $userdata = get_userdata($user_id);

            ob_start();
                require(GIBBS_CALENDAR_PATH."modals/email-sms-log.php");
            echo ob_get_clean();

        }
        
        die;
    }

    public function check_refund(){

        global $wpdb;

        $data = array();

        if(isset($_POST['order_id'])){


                $exist_refund = false;

                $orderDDDD = wc_get_order( $_POST['order_id'] );


                $refunds = $orderDDDD->get_refunds();

                
                
                $rest_refund_price = 0;
                if(!empty($refunds)){
                        $refund_data = array(); // Store refund data
                        $total_refunded_amount = 0; // To calculate the total refund amount

                        foreach ($refunds as $refund) {
                            if ($refund->get_type() == "shop_order_refund") {
                                // Gather individual refund details
                                $refund_info = array(
                                    "price"        => wc_price($refund->get_total()), // Formatted price
                                    "price_number" => $refund->get_total(), // Refund amount as a number
                                    "date"         => $refund->get_date_created()->format("Y-m-d H:i:s"), // Refund date
                                    "reason"       => $refund->get_reason() ? $refund->get_reason() : 'No reason provided', // Refund reason
                                );

                                // Add refund info to the data array
                                $refund_data[] = $refund_info;

                                // Add to the total refunded amount
                                $total_refunded_amount += $refund->get_total();
                            }
                        }

                        $total_refunded_amount = str_replace("-","",$total_refunded_amount);

                        $charge_id_exist = get_post_meta( $_POST['order_id'], '_dibs_charge_id', true );

                        $order_status  = $orderDDDD->get_status();

                        // if((int) $record->id == 21598){
                        //     echo "<pre>";print_r($record); die;
                        // // }

                        // echo "<pre>";print_r($refund_data); 
                        // echo "<pre>";print_r($total_refunded_amount); 
                        // echo "<pre>";print_r($orderDDDD->get_total()); die;

                        if($charge_id_exist != "" && ($order_status == "completed" || $order_status == "refunded")){
                            
                        
                        

                            if($total_refunded_amount >= $orderDDDD->get_total()){
                                $data["refund_all"] = "true";
                                $data["price"] = wc_price($total_refunded_amount);
                                $data["price_number"] = $total_refunded_amount;
                                $exist_refund = true;

                            }else{
                                
                                $rest_refund_price = $orderDDDD->get_total() - $total_refunded_amount;
                                $data["add_refund"] = "true";
                                $data["price"] = wc_price($rest_refund_price);
                                $data["price_number"] = $rest_refund_price;

                            }
                        }
                        
                     
                }else{

                    $charge_id_exist = get_post_meta( $_POST['order_id'], '_dibs_charge_id', true );

                    $order_status  = $orderDDDD->get_status();

                    // if((int) $record->id == 21598){
                    //     echo "<pre>";print_r($record); die;
                    // }

                    if($charge_id_exist != "" && $order_status == "completed"){
                        $data["refund_new"] = "true";
                    }

                    

                }
                

               
                
            
           // echo "<pre>"; print_r($data); die;

        }
        wp_send_json(array( 'error' => 0,'data' => $data));
        die;
         
    }
    public function getPayLink(){

        global $wpdb;

        $data = array();

        if(isset($_POST['order_id'])){

            $orderDD = wc_get_order( $_POST['order_id'] );

            $data["payment_url"] = $orderDD->get_checkout_payment_url();
           // echo "<pre>"; print_r($data); die;

        }
        wp_send_json(array( 'error' => 0,'data' => $data));
        die;
         
    }
    public function getEventAjaxData(){

        global $wpdb;

        $data = array();

        if(isset($_POST['booking_id'])){

            $booking_table = $wpdb->prefix . 'bookings_calendar';

            $record = $wpdb->get_row("SELECT * FROM $booking_table where id=".$_POST['booking_id']);
            if(isset($record->order_id) && $record->order_id != "" && $record->order_id > 0){
                $access_data_sql = "SELECT access_code from ".$wpdb->prefix."access_management where order_id = " . $record->order_id;
                $access_data = $wpdb->get_row($access_data_sql);
                //echo "<pre>"; print_r($access_data); die;

                if(isset($access_data->access_code) && $access_data->access_code != "" && $access_data->access_code > 0){
                    $data["access_code"] = $access_data->access_code;
                }
                $orderDDDD = wc_get_order( $record->order_id );

                $refunds = $orderDDDD->get_refunds();
                

                if(!empty($refunds)){
                    $refund_order = $refunds[0];


                    if($refund_order->get_type() == "shop_order_refund"){
                        $refund_data["price"] = wc_price($refund_order->get_total());
                        $refund_data["price_number"] = $refund_order->get_total();
                        $refund_data["date"] = $refund_order->get_date_created()->format("Y-m-d H:i:s");
                        $data["refund_data"] = $refund_data;
                    }
                }

                

               
                $charge_id_exist = get_post_meta( $record->order_id, '_dibs_charge_id', true );

                $order_status  = $orderDDDD->get_status();

                // if((int) $record->id == 21598){
                //     echo "<pre>";print_r($record); die;
                // }

                if($charge_id_exist != "" && $order_status == "completed"){
                    $charge_id = $charge_id_exist;
                    $data["charge_id"] = $charge_id;
                }
            }
           // echo "<pre>"; print_r($data); die;

        }
        wp_send_json(array( 'error' => 0,'data' => $data));
        die;
         
    }
    public function save_template_auto_checkbox(){

        global $wpdb;

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        update_user_meta($admin_user_id,"update_template_auto",$_POST['update_template_auto']);
        
        wp_send_json(array( 'error' => 0,'message' => "Lagret!"));
        die;
         
    }
    public function save_listing_filter_template_mobiscroll(){

        global $wpdb;
        $filter_template_table = "filter_template";

        $wpdb->update($filter_template_table, array(
            'json_data'            => json_encode($_POST),
        ),array("id"=>$_POST["template_selected"]));

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        update_user_meta($admin_user_id,"template_selected",$_POST['template_selected']);
        
        wp_send_json(array( 'error' => 0,'message' => "Lagret!"));
        die;
         
    }
    public function update_calender_filter_template_mobiscroll(){

        global $wpdb;
        $filter_template_table = "filter_template";

        $wpdb->update($filter_template_table, array(
            'name'            => $_POST["template_name"],
        ),array("id"=>$_POST["template_selected"]));
        
        wp_send_json(array( 'error' => 0,'message' => "Lagret!"));
        die;
         
    }
    public function change_template(){

        global $wpdb;

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        update_user_meta($admin_user_id,"template_selected",$_POST['template_selected']);

        $template_data = Gibbs_Common_Calendar::get_selected_template_data($_POST['template_selected']);
        $template_data["template_selected"] = $_POST['template_selected'];

        wp_send_json(array( 'error' => 0,'message' => "Lagret!", "template_data" => $template_data));
        die;
         
    }
    public function get_fields_info_for_title($booking_id){

        $booking_id = $booking_id;
        $info_data = false;
        if(isset($_POST["get_type"]) && $_POST["get_type"] == "info_data"){
            $info_data = true;
        }
        global $wpdb;
        if(isset($_POST["cal_type"]) && $_POST["cal_type"] == "view_only"){

            if($_POST["cal_view"] == "algoritme"){
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_algorithm';
            }elseif($_POST["cal_view"]  == "manuelle"){
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_approved';
            }else{
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw';
            }

        }else{
        
            $bookings_table = $wpdb->prefix . 'bookings_calendar';
        }
        $booking = $wpdb->get_row("SELECT id, listing_id, application_id, fields_data FROM $bookings_table WHERE id=".$booking_id);
        $field_datas = array();
        if(isset($booking->fields_data) && $booking->fields_data != ""){

             $field_datas = maybe_unserialize($booking->fields_data);

             if(is_array($field_datas)){

             }else{
                $ff_data = preg_replace_callback('!s:\d+:"(.*?)";!s', 
                    function($m) {
                        return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
                    }, $booking->fields_data
                );
                $field_datas = maybe_unserialize($ff_data);
             }

        }
        $app_field_datas = array();

        $about_fields_data = array();



        if(isset($booking->application_id) && $booking->application_id != ""){


            $application = $wpdb->get_row("SELECT id, application_data_id, app_fields_data FROM `applications` WHERE id=".$booking->application_id);

            if(isset($application->app_fields_data) && $application->app_fields_data != ""){

                 $app_field_datas = maybe_unserialize($application->app_fields_data);

                 if(is_array($app_field_datas)){

                 }else{
                    $app_fields_dataddd = preg_replace_callback('!s:\d+:"(.*?)";!s', 
                        function($m) {
                            return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
                        }, $application->app_fields_data
                    );
                    $app_field_datas = maybe_unserialize($app_fields_dataddd);
                 }

            }
            
            if(isset($application->application_data_id) && $application->application_data_id != ""){

               $application_data = $wpdb->get_row("SELECT about_fields_data FROM `application_data` WHERE id=".$application->application_data_id);
               

                if(isset($application_data->about_fields_data) && $application_data->about_fields_data != ""){

                     $about_fields_data = maybe_unserialize($application_data->about_fields_data);

                     if(is_array($app_field_datas)){

                     }else{
                        $about_fields_datadd = preg_replace_callback('!s:\d+:"(.*?)";!s', 
                            function($m) {
                                return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
                            }, $application_data->about_fields_data
                        );
                        $about_fields_data = maybe_unserialize($about_fields_datadd);
                     }

                }

            }

        }
        
        if($app_field_datas == ""){
            $app_field_datas = array();
        }
        if($field_datas == ""){
            $field_datas = array();
        }
        if($about_fields_data == ""){
            $about_fields_data = array();
        }

       // if(!empty($field_datas)) { 
            ob_start();
             require(GIBBS_CALENDAR_PATH."components/custom_fields.php");
            
            echo ob_get_clean();
       // }

        die;
         
    }

    public function get_custom_fields_for_calender_mobiscroll(){

        $booking_id = $_POST["booking_id"];
        $listing_id = $_POST["listing_id"];
        $info_data = false;
        if(isset($_POST["get_type"]) && $_POST["get_type"] == "info_data"){
            $info_data = true;
        }
        global $wpdb;
        if(isset($_POST["cal_type"]) && $_POST["cal_type"] == "view_only"){

            if($_POST["cal_view"] == "algoritme"){
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_algorithm';
            }elseif($_POST["cal_view"]  == "manuelle"){
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_approved';
            }else{
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw';
            }

        }else{
        
            $bookings_table = $wpdb->prefix . 'bookings_calendar';
        }
        $booking = $wpdb->get_row("SELECT id, listing_id, application_id, fields_data FROM $bookings_table WHERE id=".$booking_id);
        $field_datas = array();
        if(isset($booking->fields_data) && $booking->fields_data != ""){

             $field_datas = maybe_unserialize($booking->fields_data);

             if(is_array($field_datas)){

             }else{
                $ff_data = preg_replace_callback('!s:\d+:"(.*?)";!s', 
                    function($m) {
                        return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
                    }, $booking->fields_data
                );
                $field_datas = maybe_unserialize($ff_data);
             }

        }
        $app_field_datas = array();

        $about_fields_data = array();



        if(isset($booking->application_id) && $booking->application_id != ""){


            $application = $wpdb->get_row("SELECT id, application_data_id, app_fields_data FROM `applications` WHERE id=".$booking->application_id);

            if(isset($application->app_fields_data) && $application->app_fields_data != ""){

                 $app_field_datas = maybe_unserialize($application->app_fields_data);

                 if(is_array($app_field_datas)){

                 }else{
                    $app_fields_dataddd = preg_replace_callback('!s:\d+:"(.*?)";!s', 
                        function($m) {
                            return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
                        }, $application->app_fields_data
                    );
                    $app_field_datas = maybe_unserialize($app_fields_dataddd);
                 }

            }
            
            if(isset($application->application_data_id) && $application->application_data_id != ""){

               $application_data = $wpdb->get_row("SELECT about_fields_data FROM `application_data` WHERE id=".$application->application_data_id);
               

                if(isset($application_data->about_fields_data) && $application_data->about_fields_data != ""){

                     $about_fields_data = maybe_unserialize($application_data->about_fields_data);

                     if(is_array($app_field_datas)){

                     }else{
                        $about_fields_datadd = preg_replace_callback('!s:\d+:"(.*?)";!s', 
                            function($m) {
                                return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
                            }, $application_data->about_fields_data
                        );
                        $about_fields_data = maybe_unserialize($about_fields_datadd);
                     }

                }

            }

        }
        
        if($app_field_datas == ""){
            $app_field_datas = array();
        }
        if($field_datas == ""){
            $field_datas = array();
        }
        if($about_fields_data == ""){
            $about_fields_data = array();
        }

       // if(!empty($field_datas)) { 
            ob_start();
             require(GIBBS_CALENDAR_PATH."components/custom_fields.php");
            
            echo ob_get_clean();
       // }

        die;
         
    }


    public function edit_field_save_for_calender_mobiscroll(){
      global $wpdb;

      $data = array();

      if(isset($_POST["fields"])){

          foreach ($_POST["fields"] as $key_res => $res) {
              foreach ($res as $key_index => $from) {
                   $data[$key_index][$key_res] = $res[$key_index];
              }
          }
      }
        if(isset($_POST["cal_type"]) && $_POST["cal_type"] == "view_only"){

            if($_POST["cal_view"] == "algoritme"){
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_algorithm';
            }elseif($_POST["cal_view"]  == "manuelle"){
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_approved';
            }else{
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw';
            }

        }else{
        
            $bookings_table = $wpdb->prefix . 'bookings_calendar';
        }

      $data = maybe_serialize($data);

      $wpdb->update($bookings_table, array(
              'fields_data'            => $data,
        ),array("id"=>$_POST['booking_id']));

      wp_redirect($_SERVER['HTTP_REFERER']);
      exit;

    }


    public function get_booking_info()
    {
        echo do_shortcode("[booking-management-gibbs-v2 type='ajax']");
        wp_styles()->do_items();
        wp_scripts()->do_items();
        die();
    }
    public function get_customer_list()
    {
        $wpm_user_list  = Gibbs_Admin_Calendar_Utility::get_user_list();

        wp_send_json(
            array (
                'customer_list' => $wpm_user_list,
            )
        );
        die;
    }
    public function get_booking_by_user()
    {
        global $wpdb;
        $team_table  = $wpdb->prefix . 'team';
        $user_id = $_POST["id"];
        $team_results_user   = $wpdb->get_results("SELECT id,name,user_id FROM $team_table WHERE user_id = $user_id");
        wp_send_json(
            array (
                'user_teams' => $team_results_user,
            )
        );
    
        die();
    }
    public function check_customer_email()
    {
        global $wpdb;

       
        $exists = email_exists($_POST["email"]);

        $data = array("success" => true, "type" => "", "exist" => false, "message" => "", "user_id" => "");

        if($exists){

                $userr = get_user_by("email",$_POST["email"]);

                $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

                $users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';

                $results = $wpdb->get_results("select users_groups_id from $users_and_users_groups_table where users_id = $admin_user_id");

                $exist_in_group = false;

                foreach ($results as $key => $result) {
                    $result_group = $wpdb->get_results("select users_groups_id from $users_and_users_groups_table where users_groups_id = $result->users_groups_id and users_id = $userr->id");
                    if(!empty($result_group)){
                        $exist_in_group = true;
                    }
                }


                if($exist_in_group == true){

                    $data = array("success" => true, "type" => "already", "exist" => true, "message" => "Eposten/brukeren er allerede lagt til i avdelingen", "user_id" => $userr->id);

                }else{

                    foreach ($results as $key => $result) {
                        $insertArr = array(
                            'users_groups_id'  => $result->users_groups_id,
                            'users_id'  => $userr->id,
                            'role'  => "1",
                        );

                        $insert = $wpdb->insert($users_and_users_groups_table, $insertArr);
                    }

                    $data = array("success" => true, "type" => "added", "exist" => true, "message" => "Fant brukeren, og brukeren er lagt til gruppen", "user_id" => $userr->id);

                }

                
            
        }
        wp_send_json(
            $data
        );
    
        die();
    }
    public function addEventCustomer()
    {
        global $wpdb;

        $return = array("success" => false, "message" => "", "user_id" => "");

        

        $password = wp_generate_password( 12, false );

        $first_name = (isset($_POST['first_name'])) ? sanitize_text_field( $_POST['first_name'] ) : '' ;
        $last_name = (isset($_POST['last_name'])) ? sanitize_text_field( $_POST['last_name'] ) : '' ;
        $email = $_POST['email'];
        $email_arr = explode('@', $email);
        //$user_login = sanitize_user(trim($email_arr[0]), true);
        $user_login = $email;

        $data = array("success" => true, "exist" => false);



        $role =  "owner";

        $user_data = array(
            'user_login'    => $user_login,
            'user_email'    => $email,
            'user_pass'     => $password,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'nickname'      => $first_name,
            'role'          => $role
        );


        $user_id = wp_insert_user( $user_data );

        if ( is_wp_error( $user_id  ) ) {

            $return["success"] = false;
            $return["message"] = $user_id->get_error_message();;

        }elseif($user_id && $user_id > 0){

            $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

            $users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';

            $results = $wpdb->get_results("select users_groups_id from $users_and_users_groups_table where users_id = $admin_user_id");


            if(!empty($results)){
                foreach ($results as $key => $result) {
                    $insertArr = array(
                        'users_groups_id'  => $result->users_groups_id,
                        'users_id'  => $user_id,
                        'role'  => "1",
                    );

                    $insert = $wpdb->insert($users_and_users_groups_table, $insertArr);
                }
            }

            if(isset($_POST["country_code"])){
                update_user_meta( $user_id, 'country_code',$_POST["country_code"] );
            }

            if ( isset( $_POST['phone'] ) ){
                update_user_meta($user_id, 'phone', $_POST['phone'] );
            }   
            if ( isset( $_POST['billing_name'] ) ){
                update_user_meta($user_id, 'billing_first_name', $_POST['billing_name'] );
                update_user_meta($user_id, 'billing_last_name', "" );
            }   
            if ( isset( $_POST['billing_email']  )){
                update_user_meta($user_id, 'billing_email', esc_attr( $_POST['billing_email'] ) );
            } 

            if ( isset( $_POST['billing_address1'] ) ){
                update_user_meta($user_id, 'billing_address_1', esc_attr( $_POST['billing_address1'] ) );
            }
            if ( isset( $_POST['billing_city'] ) ){
                update_user_meta($user_id, 'billing_city', esc_attr( $_POST['billing_city'] ) );
            }
            if ( isset( $_POST['billing_postcode'] ) ){
                update_user_meta($user_id, 'billing_postcode', esc_attr( $_POST['billing_postcode'] ) );
            }
             if ( isset( $_POST['profile_type'] ) ){

                update_user_meta($user_id, 'profile_type', strtolower($_POST['profile_type'])  );
            }
            if ( isset( $_POST['organization_number'] ) ){

                update_user_meta($user_id, 'company_number', $_POST['organization_number']);
            }

            $return["success"] = true;
            $return["message"] = "Successfully Registred";
            $return["user_id"] = $user_id;
            
        }else{
            $return["success"] = false;
            $return["message"] = "User not created! Please change email id";
        }

        
        wp_send_json(
            $return
        );
    
        die();
    }

    public static function save_cal_filters()
    {
        $name   = $_POST['name'];
        $value  = $_POST['value'];

        write_log(array('save_cal_filters', 'name' => $name, 'value' => $value));

        // If $name is array, allow multiple save
        if (is_array($name)) {
            foreach ($name as $key => $meta) {
                write_log(array('meta' => $meta, 'value'  => $value[$key]));
                update_user_meta(get_current_user_ID(), $meta, $value[$key]);
            }
        } else {
            update_user_meta(get_current_user_ID(), $name, $value);
        }

        wp_send_json(array('status' => 200,  'd' => get_current_user_ID()));
    }

    


    public static function get_booking_data($get_type = "")
    {
        global $wpdb;

        $tv = false;

        if(isset($_POST["calender_type"]) && $_POST["calender_type"] == "tv"){
           $tv = true;
        }

      

        $cal_view       = "";
        $get_ajax_data  = 0;

        $not_rejected_showing = "0";

        $ajax_data = array();

     

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $ajax_data["cal_view"] = $cal_view;

        $users_table = $wpdb->prefix . 'users';
        $users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';
        $team_table  = $wpdb->prefix.'team';
        $club_table  = 'club';
        $gym_table   = 'gym';
        $sport_table   = 'sport';
        $author_id = get_current_user_id();
        $current_language = Gibbs_Common_Calendar::get_language();

        $ajax_data["current_language"] = $current_language;
        $ajax_data["cal_type"] = "";

        //$filter_location = get_user_meta(get_current_user_ID(), "filter_location", true);

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        $template_selected =  get_user_meta($admin_user_id,"template_selected",true);

        $template_data = Gibbs_Common_Calendar::get_selected_template_data($template_selected);


        $additional_info = array();

        if(isset($_POST["additional_info"]) && !empty($_POST["additional_info"])){
            $additional_info = $_POST["additional_info"];
        }


        
        // settings options
        $ajax_data["cal_starttime"] = $template_data["cal_starttime"];
        $ajax_data["cal_endtime"] = $template_data["cal_endtime"];
        $ajax_data["show_full_day"] = $show_full_day;
        $ajax_data["cell_width"] = $cell_width;
        $ajax_data["filter_location"] = $template_data["filter_location"];
        $ajax_data["filter_search"] = $filter_search;
        $ajax_data["filter_group"] = $filter_group;
        $ajax_data["calendar_view"] = $template_data["calendar_view"];
        $ajax_data["show_fields_info"] = $template_data["show_fields_info"];
        $ajax_data["show_bk_payment_failed"] = $template_data["show_bk_payment_failed"];
        $ajax_data["show_bk_pay_to_confirm"] = $template_data["show_bk_pay_to_confirm"];
        $show_fields_info = $template_data["show_fields_info"];

        if(isset($_POST["show_fields_info"]) && $_POST["show_fields_info"] != ""){
            $show_fields_info = $_POST["show_fields_info"];
        }

       
        // end settings options

        // for faster load whilve developing
        $current_user_id = get_current_user_ID();
        $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
        $users_groups = $wpdb->prefix . 'users_groups';  // table name
        $users_sql = "SELECT users_groups_id from `$users_and_users_groups` where users_id = '$current_user_id'";
        $user_group_data = $wpdb->get_results($users_sql);

        $users1_sql = "SELECT users_groups_id, name as group_name from `$users_and_users_groups` uug JOIN `$users_groups`ug ON uug.users_groups_id = ug.id where users_id = '$current_user_id'";
        $user1_group_data = $wpdb->get_results($users1_sql);

        $users_groups_ids = array();

        foreach ($user_group_data as $key => $gr_id) {
            $users_groups_ids[] = $gr_id->users_groups_id;
        }

        $booking_where = "";

        /*if ($ajax_data["filter_location"] !== "" && !empty($ajax_data["filter_location"])) {
            $booking_where = "WHERE listing_id IN ( " . implode(',', $ajax_data["filter_location"]) . " )";
        }*/
        $filter_listings = array();
        if (is_array($_POST["listing"]) && !empty($_POST["listing"])) {
            $booking_where = "WHERE listing_id IN ( " . implode(',', $_POST["listing"]) . " )";
            $filter_listings = $_POST["listing"];
        }else{
            $resources = Gibbs_Admin_Calendar_Utility::get_gym_resources();
            if(isset($resources["listings"])){
                $listings = array();

                foreach ($resources["listings"] as $listingssss) {
                    $listings[] = $listingssss["id"];
                }
                if(!empty($listings)){
                    $booking_where = "WHERE listing_id IN ( " . implode(',', $listings) . " )";
                }
            }
        }
        $where_add = "";
        if($booking_where == ""){
            $where_add = "Where";
        }else{
            $where_add = "AND";
        }
        if(isset($_POST["mobiscroll_view"]) && $_POST["mobiscroll_view"] != "schedule_year" && $_POST["mobiscroll_view"] != "agenda" ){
            if(isset($_POST["cal_date"]) && $_POST["cal_date"] != ""){
                $yearr= date("Y-m-d",strtotime($_POST["cal_date"]));
               // $last_date= date("Y-m-d",strtotime($_POST["last_date"]));
                $start_month = date('Y-m-d H:i:s', strtotime("-30 days", strtotime($yearr)));
                $end_month = date('Y-m-d H:i:s', strtotime("+60 days", strtotime($yearr)));

                // if($_POST["mobiscroll_view"] == "timeline_month" || $_POST["mobiscroll_view"] == "schedule_month"){
                //     $end_month = date('Y-m-d H:i:s', strtotime("+2 days", strtotime($last_date)));
                // }

                $booking_where .= " $where_add ((date_start >= '$start_month' AND date_end <= '$end_month') OR recurrenceRule != '' OR DATEDIFF(date_end, date_start) > 3)";
            }
        }
        if($tv){

            $booking_where .= " $where_add (status != 'cancelled' AND status != 'Cancelled' AND status != 'canceled')";

        }

        //$booking_where .= " AND (status != 'deleted' AND status != 'payment_failed')";
        $booking_where .= " AND (status != 'deleted')";


        if($ajax_data["show_bk_payment_failed"] != "true"){
            $booking_where .= " AND (status != 'payment_failed')";
        }
        if($ajax_data["show_bk_pay_to_confirm"] != "true"){
            $booking_where .= " AND (status != 'pay_to_confirm')";
        }


        //echo $booking_where; die;

        
            


        $booking_results = $wpdb->get_results("SELECT * FROM $booking_table $booking_where");


        if($get_type == "customer"){
            return $booking_results;
        }
      
       

        $ajax_data["not_rejected_showing"] = $not_rejected_showing;

        $team_results   = $wpdb->get_results("SELECT * FROM $team_table");
        //$club_results   = $wpdb->get_results("SELECT * FROM $club_table");
        $user_results   = $wpdb->get_results("SELECT * FROM $users_table");
        $filter_location = $wpdb->get_results("SELECT * FROM  $gym_table");
        $spotrs_filter  = $wpdb->get_results("SELECT * FROM  $sport_table");
        //$working_hours = $wpdb->get_results("SELECT working_hours FROM gym_section");
        $working_hours = array();
        $filter_sports_id = array();
        //$filter_sports_id = $wpdb->get_results("SELECT * FROM gym_listings_sports");
        $user_groups_id = $wpdb->get_results("SELECT `users_groups_id` FROM `ptn_users_and_users_groups` WHERE users_id=$author_id;");

        //$gyms_sections_check   = $wpdb->get_results("SELECT * from $gym_section_table");
        $gyms_sections_check   = array();
        $get_id = $wpdb->get_row("SHOW TABLE STATUS LIKE '$booking_table'");
        $bc_schema_id = $get_id->Auto_increment;

        if (empty($user_groups_id)) { //user is related to no users_group 
            $inUserGroupsSQL = "";
        } else {
            foreach ($user_groups_id as $item) {
                $user_groups_id_arr[] = $item->users_groups_id;
            }

            $inUserGroupsSQL = " OR users_groups_id IN (" . implode(',', (array)$user_groups_id_arr) . ") ";
        }

        $users_groups_ids = implode(",", $users_groups_ids);


        // spotrs_filter here 
        foreach ($spotrs_filter as $sportfilters) {
            if (!empty($sportfilters->name)) {
                $sportss_filter[] = array($sportfilters->name, $sportfilters->id);
            }
        }

        if(in_array("sport",$additional_info)){

          $ajax_data["sportsList"] = array('sport' => $sportss_filter, 'test' => $bc_schema_id);
        }

        // filter location here 
        foreach ($filter_location as $filters) {
            if (!empty($filters->name) || $filters->name !== "Auto Draft" || !empty($filters->id)) {
                $location_filter[] = array($filters->name, $filters->id);
            }
        }

        $ajax_data["filter_locations"] = array('filter_location' => $location_filter);

        // fetching filter sports ids     
        foreach ($filter_sports_id as $sport_ids) {
            if (!empty($sport_ids->gym_section_id) || $sport_ids->sport_id) {
                $sports_id_filter[] = array('id' => $sport_ids->gym_section_id, 'sport_id' => $sport_ids->sport_id);
            }
        }

        $ajax_data["filter_sports_id"] = array('filter_sports_id' => $sports_id_filter);


        $ajax_data["hourss"] = array('gym_section_table' => $working_hours);


        $listings_working_hours_raw = array();

        $ajax_data["gym_resources"] = Gibbs_Common_Calendar::get_gym_resources($filter_listings);

        $show_extra_info = get_user_meta(get_current_user_ID(), "show_extra_info", true);

        $ajax_data["show_extra_info"] = $show_extra_info;

        //Loading Resources of Booking Calendar
        $records = [];


        //echo "<pre>"; print_r($ajax_data); die;



        foreach ($booking_results as $record) {

            $bookings_calendar_raw = $wpdb->prefix . 'bookings_calendar_raw';
            $bookings_calendar_raw_sql = "SELECT * from $bookings_calendar_raw where id =" . $record->id;
            $bookings_calendar_raw_dd = $wpdb->get_row($bookings_calendar_raw_sql);

            if ($record->application_id != "") {
                $sum_desired_hours = Gibbs_Season_Calendar_Setup::sum_desired_hours($record->application_id);
                $sum_received_hours = Gibbs_Season_Calendar_Setup::sum_received_hours($record->application_id);
                $sum_algo_hours = Gibbs_Season_Calendar_Setup::sum_algo_hours($record->application_id);
                $score = Gibbs_Season_Calendar_Setup::score($record->application_id);

                $app_data = array();
                $app_data["sum_desired_hours"] = $sum_desired_hours;
                $app_data["sum_received_hours"] = $sum_received_hours;
                $app_data["sum_algo_hours"] = $sum_algo_hours;
                $app_data["score"] = $score;
            } else {
                $sum_desired_hours = 0;
                $sum_received_hours = 0;
                $sum_algo_hours = 0;
                $score = "";
                $app_data = array();
                $app_data["sum_desired_hours"] = $sum_desired_hours;
                $app_data["sum_received_hours"] = $sum_received_hours;
                $app_data["sum_algo_hours"] = $sum_algo_hours;
                $app_data["score"] = $score;
            }


            if ($bookings_calendar_raw_dd) {

                $org_data = array();
                $org_start_d = date("H:i", strtotime($bookings_calendar_raw_dd->date_start));
                $org_end_d = date("H:i", strtotime($bookings_calendar_raw_dd->date_end));
                $org_data["name"] = get_the_title($bookings_calendar_raw_dd->listing_id);
                $org_data["day"] = date("l", strtotime($bookings_calendar_raw_dd->date_start));
                $org_data["time"] = $org_start_d . " - " . $org_end_d;
            } else {
                $org_data = array();
                $org_data["name"] = "";
                $org_data["day"] = "";
                $org_data["time"] = "";
            }

            if ($record->status == "pay_to_confirm") {
               // continue;
            }

            $team_title = $club_name = $user_name = '';

            $customer_email = "";
            $phone_number = "";

            foreach ($team_results as $team) {
                if ($team->club_id == $record->team_id) {
                    $team_title = $team->name;
                }
            }

            // foreach ($club_results as $club) {
            //     if ($club->id == $record->bookings_author) {
            //         $club_name = $club->company_name;
            //     }
            // };

            foreach ($user_results as $user) {
                if ($user->ID == $record->bookings_author) {
                    $user_name = $user->display_name;
                    $customer_email = $user->user_email;
                    $phone_number = get_user_meta($user->ID,"phone",true); 
                }
            }
            if($phone_number == "" && $record->application_id != ""){
                $application_dd = $wpdb->get_row("SELECT application_data_id FROM `applications` WHERE id=".$record->application_id);
                if(isset($application_dd->application_data_id)){
                    $application_data_dd = $wpdb->get_row("SELECT json_data FROM `application_data` WHERE id=".$application_dd->application_data_id);
                    if(isset($application_data_dd->json_data)){
                        $json_data2 = maybe_unserialize($application_data_dd->json_data);

                        if(isset($json_data2["phone"])){
                            $phone_number = $json_data2["phone"];
                        }

                    }
                }
               
            }
            if ($record->status == "paid" && $record->fixed == "1") {
                $record->status = "closed";
            }
            if ($record->status == "paid" && $record->fixed == "2") {
                $record->status = "sesongbooking";
            }
            if ($record->status == "paid" && $record->fixed == "3") {
                $record->status = "manual_invoice";
            }
            $org_status = $record->status;
            $status_text = "";
            if ($current_language  == "nb-NO") {
                if ($record->status == "paid" || $record->status == "Paid") {
                    $status_text = "Betalt";
                } else if ($record->status == "waiting" || $record->status == "Waiting") {
                    $status_text = "Godkjent";
                } else if ($record->status == "confirmed" || $record->status == "Confirmed") {
                    $status_text = "Godkjenn";
                } else if ($record->status == "pay_to_confirm") {
                    $status_text = "Reservert, venter betaling";
                } else if ($record->status == "expired" || $record->status == "Expired") {
                    $status_text = "Utløpt booking";
                } else if ($record->status == "cancelled" || $record->status == "Canceled") {
                    $status_text = "Kansellert";
                } else if ($record->status == "closed" || $record->status == "Closed") {
                    $status_text = "Stengt";
                } else if ($record->status == "sesongbooking" || $record->status == "sesongbooking") {
                    $status_text = "Sesongbooking";
                } else if ($record->status == "manual_invoice" || $record->status == "manual_invoice") {
                        $status_text = "Manuel faktura yo";
                } else if ($record->status == "unpaid" || $record->status == "Unpaid") {
                    $status_text = "ubetalt";
                } else if ($record->status == "Pending" || $record->status == "pending") {
                    $status_text = "Avventer";
                } else {
                    $status_text = $record->status;


                   




                }
            } else {
                $status_text = $record->status;
            }
            $manuale_val = "0";

            if ($cal_type == "view_only") {
                if ($cal_view == "algoritme") {
                    if ($record->rejected == "1") {
                        $record->status = "rejected";
                    } else {
                        $record->status = "algo_done";
                    }
                } else {
                    $record->status = "done";
                }

                if ($cal_view == "manuelle") {

                    if ($record->rejected == "1" && $record->modified == "1") {
                        $record->status = "rejected_modified";
                    } elseif ($record->rejected == "1" && $record->modified == "0") {
                        $record->status = "rejected_notmodified";
                    } elseif ($record->rejected == "0" && $record->modified == "1") {
                        $record->status = "notrejected_modified";
                    } else {
                        $record->status = "notrejected_notmodified";
                    }
                    if ($record->rejected == "1") {
                        $status_text = "Avslått";
                    } else {
                        $status_text = "Godkjent";
                    }
                }

                $comment = $record->comment;
            }
            $comment = $record->comment;

            $start_d = date("l H:i", strtotime($record->date_start));
            $end_d = date("l H:i", strtotime($record->date_end));
            if ($current_language == "nb-NO") {
                $start_d = str_replace("Monday", "Mandag", $start_d);
                $start_d = str_replace("Tuesday", "Tirsdag", $start_d);
                $start_d = str_replace("Wednesday", "Onsdag", $start_d);
                $start_d = str_replace("Thursday", "Torsdag", $start_d);
                $start_d = str_replace("Friday", "Fredag", $start_d);
                $start_d = str_replace("Saturday", "Lørdag", $start_d);
                $start_d = str_replace("Sunday", "Søndag", $start_d);

                $end_d = str_replace("Monday", "Mandag", $end_d);
                $end_d = str_replace("Tuesday", "Tirsdag", $end_d);
                $end_d = str_replace("Wednesday", "Onsdag", $end_d);
                $end_d = str_replace("Thursday", "Torsdag", $end_d);
                $end_d = str_replace("Friday", "Fredag", $end_d);
                $end_d = str_replace("Saturday", "Lørdag", $end_d);
                $end_d = str_replace("Sunday", "Søndag", $end_d);
            }

            if ($record->rejected == "") {
                $record->rejected = 0;
            }

            $extra_info = array();

            if ($record->application_id != "") {

                $applications_db = 'applications';  // table name
                $applications_sql = "SELECT * from $applications_db where id = " . $record->application_id;
                $applications_dd = $wpdb->get_row($applications_sql);

                if (isset($applications_dd->age_group_id) && $applications_dd->age_group_id != "" && in_array("age_group", $additional_info)) {
                    $age_group_db = 'age_group';  // table name
                    $age_group_sql = "SELECT name from $age_group_db where id =" . $applications_dd->age_group_id;
                    $age_group_dd = $wpdb->get_results($age_group_sql);

                    $age_group_names = array();

                    foreach ($age_group_dd as $key => $age_group_dd_val) {
                        $age_group_names[] = $age_group_dd_val->name;
                    }
                    $extra_info["age_group"] = implode(", ", $age_group_names);
                }
                if (isset($applications_dd->team_level_id) && $applications_dd->team_level_id != ""  && in_array("level", $additional_info)) {
                    $team_level_db = 'team_level';  // table name
                    $team_level_sql = "SELECT name from $team_level_db where id =" . $applications_dd->team_level_id;
                    $team_level_dd = $wpdb->get_results($team_level_sql);

                    $team_level_names = array();

                    foreach ($team_level_dd as $key => $team_level_dd_val) {
                        $team_level_names[] = $team_level_dd_val->name;
                    }
                    $extra_info["team_level"] = implode(", ", $team_level_names);
                } 
                if (isset($applications_dd->type_id) && $applications_dd->type_id != ""  && in_array("type", $additional_info)) {
                    $type_db = 'type';  // table name
                    $type_sql = "SELECT name from $type_db where id =" . $applications_dd->type_id;
                    $type_dd = $wpdb->get_results($type_sql);

                    $type_names = array();

                    foreach ($type_dd as $key => $type_dd_val) {
                        $type_names[] = $type_dd_val->name;
                    }
                    $extra_info["type"] = implode(", ", $type_names);
                }
                if (isset($applications_dd->sport_id) && $applications_dd->sport_id != ""  && in_array("sport", $additional_info)) {
                    $sport_db = 'sport';  // table name
                    $sport_sql = "SELECT name from $sport_db where id =" . $applications_dd->sport_id;
                    $sport_dd = $wpdb->get_results($sport_sql);

                    $sport_names = array();

                    foreach ($sport_dd as $key => $sport_dd_val) {
                        $sport_names[] = $sport_dd_val->name;
                    }
                    $extra_info["sport"] = implode(", ", $sport_names);
                } 
                if (isset($applications_dd->members) && $applications_dd->members != ""  && in_array("members", $additional_info)) {

                    $extra_info["members"] = $applications_dd->members;
                }
                if (isset($applications_dd->team_id) && $applications_dd->team_id != ""  && in_array("team_name", $additional_info)) {
                    $team_db = $wpdb->prefix . 'team';  // table name
                    $team_sql = "SELECT name from $team_db where id =" . $applications_dd->team_id;
                    $team_dd = $wpdb->get_results($team_sql);

                    $team_names = array();

                    foreach ($team_dd as $key => $team_dd_val) {
                        $team_names[] = $team_dd_val->name;
                    }
                    $extra_info["team_name"] = implode(", ", $team_names);
                }
            }


            $listings_ids = [];

            if($record->first_event_id != ""){
                $bookings_calendar_sql = "SELECT * from $booking_table where first_event_id =" . $record->first_event_id;
                $first_event_data = $wpdb->get_results($bookings_calendar_sql);

                foreach ($first_event_data as $key => $first_event_d) {
                    $listings_ids[] = $first_event_d->listing_id;
                }

                $listings_ids = array_unique($listings_ids);

            }
            $fields_info_data = array();
            $custom_fields = false;
            $incc = 0;
            if($record->fields_data != ""){

                $data_fields = maybe_unserialize($record->fields_data);

                if(is_array($data_fields) && !empty($data_fields)){

                    if(isset($data_fields[0])){

                        $fields_infos = $data_fields[0];

                        foreach ($show_fields_info as $key => $show_fie) {
                            if(isset($fields_infos[$show_fie])){
                                $fields_info_data[$incc]["label"] = str_replace("-"," ",ucfirst($show_fie));
                                $fields_info_data[$incc]["value"] = $fields_infos[$show_fie];
                                $incc++;
                            }
                            # code...
                        }
                    }
                    $custom_fields = true;
                }

            }
            
            $payment_url = "";
            $charge_id = "";

            // if(isset($record->order_id) && $record->order_id != "" && $record->order_id > 0 && $record->price > 0 && $record->status != "paid"){
            //     $orderDD = wc_get_order( $record->order_id );

            //     $payment_url = $orderDD->get_checkout_payment_url();
            // }
            $access_code = "";

            $refund_data = array();

            if(isset($record->order_id) && $record->order_id != "" ){

                if(isset($record->refund_data) && $record->refund_data && $record->refund_data != ""){
                    $refund_data_json = json_decode($record->refund_data);
                    if(is_array($refund_data_json) && !empty($refund_data_json)){

                        $kkkk = 0;

                        foreach($refund_data_json as $refund_data_js_data){

                            $refund_data[$kkkk]["price"] = $refund_data_js_data->price;
                            $refund_data[$kkkk]["price_number"] = $refund_data_js_data->price_number;
                            $refund_data[$kkkk]["date"] = $refund_data_js_data->date;

                            $kkkk++;

                        }

                    }else if(isset($refund_data_json->price)){
                        
                        $refund_data[0]["price"] = $refund_data_json->price;
                        $refund_data[0]["price_number"] = $refund_data_json->price_number;
                        $refund_data[0]["date"] = $refund_data_json->date;
                    }
                    //echo "<pre>";print_r($refund_data); die;
                }
                // $access_data_sql = "SELECT access_code from ".$wpdb->prefix."access_management where order_id = " . $record->order_id;
                // $access_data = $wpdb->get_row($access_data_sql);

                // if(isset($access_data->access_code) && $access_data->access_code != "" && $access_data->access_code > 0){
                //     $access_code = $access_data->access_code;
                // }
                // $orderDDDD = wc_get_order( $record->order_id );

                // $refunds = $orderDDDD->get_refunds();
                

                // if(!empty($refunds)){
                //     $refund_order = $refunds[0];


                //     if($refund_order->get_type() == "shop_order_refund"){
                //         $refund_data["price"] = wc_price($refund_order->get_total());
                //         $refund_data["price_number"] = $refund_order->get_total();
                //         $refund_data["date"] = $refund_order->get_date_created()->format("Y-m-d H:i:s");
                //     }
                // }

                

               
                $charge_id_exist = get_post_meta( $record->order_id, '_dibs_charge_id', true );

                // $order_status  = $orderDDDD->get_status();

                // // if((int) $record->id == 21598){
                // //     echo "<pre>";print_r($record); die;
                // // }

                // if($charge_id_exist != "" && $order_status == "completed"){
                //     $charge_id = $charge_id_exist;
                // }
                if($charge_id_exist != ""){
                    $charge_id = $charge_id_exist;
                }
            }

            $amount_guest = 1;

            $service_data = array();

            if($comment != "" && self::json_validator($comment)){
                $comment_data = json_decode($comment);

                //if($record->id == 37011){
                    if(isset($comment_data->service) && is_array($comment_data->service) && !empty($comment_data->service)){
                        $service_data = $comment_data->service;
                    }
               // }
               
                if(isset($comment_data->adults)){
                    $amount_guest = $comment_data->adults;
                    //echo "<pre>";print_r($comment_data); die;
                }
                //
            }

            //  $bookings_calendar_meta_sql = "SELECT * from bookings_calendar_meta where meta_key = 'number_of_guests'  AND booking_id = " . $record->id;
            // $bookings_calendar_meta_data = $wpdb->get_row($bookings_calendar_meta_sql);

            // if(isset($bookings_calendar_meta_data->meta_value) && $bookings_calendar_meta_data->meta_value != ""){
            //     $amount_guest = $bookings_calendar_meta_data->meta_value; 
            // }else{
            //      if($comment != "" && self::json_validator($comment)){
            //         $comment_data = json_decode($comment);

            //         if(isset($comment_data->adults)){
            //             $amount_guest = $comment_data->adults;
            //             //echo "<pre>";print_r($comment_data); die;
            //         }
            //         //
            //     }
            // }   
            
            // $bookings_calendar_meta_google_cal_id_sql = "SELECT * from bookings_calendar_meta where meta_key = 'google_cal_id'  AND booking_id = " . $record->id;
            // $bookings_calendar_meta_google_cal_id_data = $wpdb->get_row($bookings_calendar_meta_google_cal_id_sql);
            

            $google_cal_data = [];
            // if(isset($bookings_calendar_meta_google_cal_id_data->meta_value) && $bookings_calendar_meta_google_cal_id_data->meta_value != ""){
            //     $google_cal_data["google_cal_id"] = $bookings_calendar_meta_google_cal_id_data->meta_value;
            //     $google_cal_data["googleEventId"] = $record->googleEventId;
            // }

            // $bookings_calendar_meta_outlook_cal_id_sql = "SELECT * from bookings_calendar_meta where meta_key = 'outlook_cal_id'  AND booking_id = " . $record->id;
            // $bookings_calendar_meta_outlook_cal_id_data = $wpdb->get_row($bookings_calendar_meta_outlook_cal_id_sql);
            

            $outlook_cal_data = [];
            // if(isset($bookings_calendar_meta_outlook_cal_id_data->meta_value) && $bookings_calendar_meta_outlook_cal_id_data->meta_value != ""){
            //     $outlook_cal_data["outlook_cal_id"] = $bookings_calendar_meta_outlook_cal_id_data->meta_value;
            //     $outlook_cal_data["outlookEventId"] = $record->outlookEventId;
            // }


            

            write_log(array('status' => $record->status));


            $record_data = array(

                'id'                  => $record->id,
                'first_event_id'      => $record->first_event_id,
                'unlink_first_event_id'      => $record->unlink_first_event_id,
                'title'               => $record->title,
                'customer'            => $user_name,
                'amount_guest'            => $amount_guest,
                'customer_email'            => $customer_email,
                'phone_number'            => $phone_number,
                'charge_id'            => $charge_id,
                'refund_data'          => $refund_data,
                'access_code'         => $access_code,
                'listing'            => $listings_ids,
                'price'            => $record->price,
                'order_id'            => $record->order_id,
                'payment_url'            => $payment_url,
                'description'         => $record->description,
                'end'                 => $record->date_end,
                'end_d'               => $end_d,
                'start'               => $record->date_start,
                'start_d'             => $start_d,
                'endTimezone'         => '',
                'image'               => '',
                'isAllDay'            => '',
                'rejected'            => $record->rejected,
                'org_status'          => $org_status,
                'comment'             =>  $comment,
                'extra_info'          =>  $extra_info,
                'status'              => $record->status, // ['text'=>$status_text,'value'=>$record->status],
                'status_manuale'      => ['text' => $status_text, 'value' => $record->rejected],
                'team'                => ['text' => is_null($record->team_id) ? "Select" : $team_title, 'value' => is_null($record->team_id) ? "Select" : $record->team_id],
                'client'              => ['text' => $user_name, 'value' => $record->bookings_author],
                'gymSectionId'        => $record->listing_id,
                'resource'        => $record->listing_id,
                'org_data'        => $org_data,
                'app_data'        => $app_data,
                'custom_fields'        => $custom_fields,
                'fields_info_data'        => $fields_info_data,
                'created_date'        => $record->created_at,
                'google_cal_data'        => $google_cal_data,
                'outlook_cal_data'        => $outlook_cal_data,
                'service_data'        => $service_data,
            );
            $record_data['recurrenceId']        = $record->recurrenceID;
            if ($record->recurrenceRule != '') {

                $rules = $record->recurrenceRule;

                $rules = explode(";", $rules);

                $rulesss = array();

                foreach ($rules as $key => $rule) {
                    if($rule != ""){
                        $rule = explode("=", $rule);
                        $rulesss[$rule[0]] = $rule[1];
                    }    
                }
                

                if(isset($rulesss["UNTIL"]) && $rulesss["UNTIL"] != ""){
                    $rulesss["UNTIL"] = date("Y-m-d", strtotime($rulesss["UNTIL"]));
                }else if(isset($rulesss["COUNT"]) && $rulesss["COUNT"] != ""){
                    $rulesss["COUNT"] = $rulesss["COUNT"];
                }else{
                    $rulesss["UNTIL"] = date('Y-m-d', strtotime("+2 years", strtotime($record->date_start)));
                }

                $rulesss_data = array();

                foreach ($rulesss as $key_rule => $rull) {
                   $rulesss_data[] = $key_rule."=".$rull;
                }

                $record->recurrenceRule = implode(";",$rulesss_data);

                $exp_dates = array();

                $recurrenceException = $record->recurrenceException;

                if ($recurrenceException != "") {

                  $json_data = json_decode($recurrenceException);
                  if (json_last_error() === JSON_ERROR_NONE) {
                      $exp_dates = $json_data;

                      
                  }else{

                    $recurrenceException = explode(",", $recurrenceException);

                    foreach ($recurrenceException as $key => $rec_exooo) {
                      //$date = str_replace("T"," ",$rec_exooo); 
                      $datee = Date("Y-m-d", strtotime($rec_exooo));

                      $exp_dates[] = $datee;
                    }

                  }
                
                }





                $record_data['rrule'] = str_replace('\\n', '\n', $record->recurrenceRule);
                $record_data['recurrenceRule']      = $record->recurrenceRule;
                $record_data['recurring'] = $record->recurrenceRule;
                $record_data['recurrenceException'] = $exp_dates;
                $record_data['recurringException']  = $exp_dates;

                //echo "<pre>"; print_r($record_data); die;

                
                $datetime1 = new DateTime($record->date_start);
                $datetime2 = new DateTime($record->date_end);
                $interval = $datetime1->diff($datetime2);
                $record_data['duration'] = $interval->format('%H') . ":" . $interval->format('%I');
            }

            $records[] = $record_data;
        }
        $ajax_data["schedular_tasks"] = $records;

        wp_send_json($ajax_data);
    }

    public function json_validator($data) { 
        if (!empty($data)) { 
            return is_string($data) &&  
              is_array(json_decode($data, true)) ? true : false; 
        } 
        return false; 
    } 

    public static function search_booking_data()
    {
        global $wpdb;

        $booking_table = $wpdb->prefix . 'bookings_calendar';
        $search = $_POST['q'];

        $filter_location = get_user_meta(get_current_user_ID(), "filter_location", true);

        $booking_where = "WHERE title like '%$search%'";

        if ($filter_location !== "") {
            $booking_where .= " AND listing_id IN ( " . implode(',', $filter_location) . " )";
        }

        $booking_results = $wpdb->get_results("SELECT * FROM $booking_table $booking_where ");

        wp_send_json(array('bookings' => $booking_results));
    }

    public static function wpm_add_record()
    {



        global $wpdb;
        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $count_query = "select count(*) from $booking_table";
        $num = $wpdb->get_var($count_query);

        if ($num == NULL) {
            $numbers = 1;
        } else {
            $numbers = $num + 1;
        }



        if ($_POST['status'] == "closed") {
            $list_id = $_POST['gymSectionId'];
            $list_table = $wpdb->prefix . 'posts';
            $user_groups_id = $wpdb->get_var("select users_groups_id from $list_table where id=$list_id");
            $closed_user_id = get_current_user_ID();
        } else {
            $closed_user_id = '';
            $user_groups_id  = '';
        }

        $closed = false;

        if ($_POST['status'] == "closed") {
            $_POST['status'] = "paid";
            $fixed = "1";
            $closed = true;
        } elseif ($_POST['status'] == "sesongbooking") {
            $_POST['status'] = "paid";
            $fixed = "2";
        } elseif ($_POST['status'] == "manual_invoice") {
            $_POST['status'] = "paid";
            $fixed = "3";
        }
          else {
            $fixed = "0";
        }

        $comment = null;


        if($_POST["wpm_client"] == "" || $_POST["wpm_client"] == 0){
            $_POST["wpm_client"] = Gibbs_Common_Calendar::get_current_admin_user();
        }

        

        if(isset($_POST["wpm_client"]) && $_POST["wpm_client"] != ""){
           $comment_data =  self::get_comment_data($_POST["wpm_client"]);

           if(isset($_POST["guest"]) && $_POST["guest"] != ""){
             $comment_data["adults"] = $_POST["guest"];
           }else{
             $comment_data["adults"] = 1;
           }
           $comment = json_encode($comment_data);
        }
        


        if(isset($_POST['recurrenceRule']) && $_POST['recurrenceRule'] != ""){


            $_POST['recurrenceRule'] = self::updateRecRule($_POST['recurrenceRule']);

        }

        if(isset($_POST["googleEventId"]) && $_POST["googleEventId"] != "" && !empty($_POST['listings'])){
            $listt_id = $_POST['listings'][0];
            $bookings_calendar_meta_eevnt_sql = "SELECT * from $booking_table where googleEventId='".$_POST["googleEventId"]."' AND listing_id = ".$listt_id;
            $bookings_calendar_meta_event_data = $wpdb->get_results($bookings_calendar_meta_eevnt_sql);
            if(!empty($bookings_calendar_meta_event_data)){
                $_POST["id"] = $bookings_calendar_meta_event_data[0]->id;
                self::wpm_update_record();
                exit;
            }
            $_POST['status'] = "paid";
            $fixed = "4";

        }
        if(isset($_POST["outlookEventId"]) && $_POST["outlookEventId"] != "" && !empty($_POST['listings'])){
            $listt_id = $_POST['listings'][0];
            $bookings_calendar_meta_eevnt_sql = "SELECT * from $booking_table where outlookEventId='".$_POST["outlookEventId"]."' AND listing_id = ".$listt_id;
            $bookings_calendar_meta_event_data = $wpdb->get_results($bookings_calendar_meta_eevnt_sql);
            if(!empty($bookings_calendar_meta_event_data)){
                $_POST["id"] = $bookings_calendar_meta_event_data[0]->id;
                self::wpm_update_record();
                exit;
            }
            $_POST['status'] = "paid";
            $fixed = "4";

        }

        


       

        $wpm_client     = $_POST['wpm_client'];
        $start          = $_POST['start'];
        $end            = $_POST['end'];
        $description    = $_POST['description'];
        $repert         = $_POST['repert'];
        $status         = $_POST['status'];
        $title          = $_POST['title'];
        $gymId          = $_POST['gymId'];
        $recurrenceRule = $_POST['recurrenceRule'];
        $recurrenceId   = $_POST['recurrenceId'];
        $gymSectionId   = $_POST['gymSectionId'];

        $price = 0;

        if(isset($_POST["price"]) && $_POST["price"] != "" && $_POST["price"] > 0){
            $price = $_POST["price"];
        }



        $_POST["disable_order_mail"] = true;

        if(isset($_POST["sendmail"]) && $_POST["sendmail"] == "true"){
            $_POST["disable_order_mail"] = false;

        }



        
        
        

        if(isset($_POST['listings']) && is_array($_POST['listings'])){
            $_POST['listings'] = array_unique($_POST['listings']);

            $first_event_id = "";

            $booking_ids = [];

            foreach ($_POST['listings'] as $key => $listing) {

                $owner_id = $wpdb->get_var("select post_author from ptn_posts where id=$listing");

                $insertArr = array(
                    'bookings_author'       => $wpm_client,
                    'date_start'            => date("Y/m/d H:i", strtotime($start)),
                    'date_end'              => date("Y/m/d H:i", strtotime($end)),
                    'description'           => $description,
                    'repert'                => $repert,
                    'status'                => $status,
                    'price'                => $price,
                    'title'                 => $title,
                    'type'                  => 'reservation',
                    'fixed'                 => $fixed,
                    'closed_user_id'        => $closed_user_id,
                    'closed_user_group_id'  => $user_groups_id,
                    'recurrenceRule'        => $recurrenceRule,
                    'recurrenceID'          => $recurrenceId,
                    'listing_id'            => $listing,
                    'owner_id'              => $owner_id,
                    'comment'               => $comment
                );
                if(isset($_POST["googleEventId"]) && $_POST["googleEventId"] != ""){

                    $insertArr["googleEventId"] = $_POST["googleEventId"];
        
                }
                if(isset($_POST["outlookEventId"]) && $_POST["outlookEventId"] != ""){

                    $insertArr["outlookEventId"] = $_POST["outlookEventId"];
        
                }


                $insert = $wpdb->insert($booking_table, $insertArr);

                $bk_id = $wpdb->insert_id;

                
                

                if(isset($_POST["guest"]) && $_POST["guest"] > 0){

                    $wpdb->insert("bookings_calendar_meta",
                            array(
                                "meta_key" => "number_of_guests",
                                "meta_value" => $_POST["guest"],
                                "booking_id" => $bk_id
                            )
                        );
        
                }
                if(isset($_POST["google_cal_id"]) && $_POST["google_cal_id"] != ""){

                    $wpdb->insert("bookings_calendar_meta",
                            array(
                                "meta_key" => "google_cal_id",
                                "meta_value" => $_POST["google_cal_id"],
                                "booking_id" => $bk_id
                            )
                        );
        
                }
                if(isset($_POST["outlook_cal_id"]) && $_POST["outlook_cal_id"] != ""){

                    $wpdb->insert("bookings_calendar_meta",
                            array(
                                "meta_key" => "outlook_cal_id",
                                "meta_value" => $_POST["outlook_cal_id"],
                                "booking_id" => $bk_id
                            )
                        );
        
                }
                

                $booking_ids[] = $bk_id;

                if ($key == 0) {
                    $first_event_id = $bk_id;

                    $booking_from_info = array(

                        "booking_from" => "admin_cal",
                        "booking_type" => "insert",
                        "date" => date("Y-m-d H:i:s"),
    
                    );
    
                    $wpdb->insert("bookings_calendar_meta",
                                array(
                                    "booking_id" => $bk_id,
                                    "meta_key" => "booking_from",
                                    "meta_value" => json_encode($booking_from_info)
                                )
                            );

                            
                    $log_args = array(
                        'action' => "booking_created",
                        'related_to_id' => $owner_id,
                        'user_id' => $wpm_client,
                        'post_id' => $first_event_id
                    );
                    listeo_insert_log($log_args);
                }
            }


            if(count($_POST['listings']) > 1){
                foreach ($booking_ids as $key => $booking_id) {
                   $wpdb->update($booking_table, array('first_event_id' => $first_event_id), array('id' => $booking_id));  
                }
            }



            if($first_event_id != "" && ($_POST["status"] == "confirmed" || $_POST["status"] == "paid") && $closed == false){

                $booking_idd = $first_event_id;
                $post = $_POST;
                

                $order_idd = Gibbs_Admin_Calendar_API::createBookingOrder($booking_idd,$post,$price);

                 


                if($order_idd != "" && $order_idd > 0){

                    foreach ($booking_ids as $booking_id) {
                       $wpdb->update($booking_table, array('order_id' => $order_idd), array('id' => $booking_id));  
                    }

                }



            }
            

            


        }


        wp_send_json(array('status' => 200, 'client' => $wpm_client, 'post' => $_POST));
    }

    public function createBookingOrder($booking_id,$data,$price){

        global $wpdb;

        $order_idd = "";

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $booking_where = "WHERE id = ".$booking_id;

        $booking_data = $wpdb->get_row("SELECT * FROM $booking_table $booking_where");

       



        if(isset($booking_data->id)){

            if(isset($data["wpm_client"]) && $data["wpm_client"] != ""){

                $product_id = get_post_meta( $booking_data->listing_id, 'product_id', true); 

                $pr_data = wc_get_product( $product_id ); 


                if($product_id != "" && $product_id > 0 && $pr_data != ""){

                    $userData = get_userdata($data["wpm_client"]);







                    $address= array(
                                'first_name'    => $userData->first_name,
                                'last_name'     => $userData->last_name,
                                'email'         => $userData->user_email,
                                'address_1' => get_user_meta($user_id,"billing_address_1",true),
                                'postcode'  => get_user_meta($user_id,"billing_postcode",true),
                                'city'      => get_user_meta($user_id,"billing_city",true),
                                'country'   => get_user_meta($user_id,"billing_country",true)
                            );

                    $order = wc_create_order();



                    $args['totals']['subtotal'] = (int) $price;
                    $args['totals']['total'] = (int) $price;




                    $order->add_product( wc_get_product( $product_id ), 1, $args );

                    $order->set_address( $address, 'billing' );
                    $order->set_address( $address, 'shipping' );
                    //$order->set_billing_phone( $phone );
                    $order->set_customer_id($userData->ID);
                    $order->set_billing_email($userData->user_email);

                    $payment_url = $order->get_checkout_payment_url();

                    


                        
                    //$order->apply_coupon($coupon_code);
                    
                    
                    $order->calculate_totals();
                    $order->save();
                    
                    $order->update_meta_data('booking_id', $booking_data->id);

                    $order->update_meta_data('owner_id', $booking_data->owner_id);
                    
                    

                    //$order->update_meta_data('billing_phone', $phone);
                    $order->update_meta_data('listing_id', $booking_data->listing_id);

                    if(isset($data["disable_order_mail"]) && $data["disable_order_mail"] == true){

                       $order->update_meta_data('disable_order_mail', "true");
                    }   

                    $order->save_meta_data();

                    $order = wc_get_order( $order->id );

                    if($price < 1 || $data["status"] == "paid"){


                        $booking_id = $booking_data->id;

                        $order = wc_get_order( $order->id );

                        $update_values['order_id'] = $order->id;

                        $wpdb->update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'id' => $booking_id ) );

                        $order->update_status( 'completed' );
                        
                    }else{
                        if(isset($data["disable_order_mail"]) && $data["disable_order_mail"] == true){
                          
                        }else{
                            Gibbs_Admin_Calendar_API::send_mail_booking($booking_table,$booking_id,$data["status"],$order,$payment_url);
                        }
                    }

                    $order_idd =  $order->id;
                }    
            }

        }

        return $order_idd;


    }
    public function send_mail_booking($booking_table,$booking_id,$status, $order = array(), $payment_url = ""){
        global $wpdb;
        $order_id = "";
        $booking_data = $wpdb -> get_row( 'SELECT * FROM `'  . $booking_table.'` WHERE `id`=' . esc_sql( $booking_id ), 'ARRAY_A' );
        if($booking_data){


            $user_id = $booking_data['bookings_author'];
            $owner_id = $booking_data['owner_id'];
            $startDate = $booking_data['date_start'];
            $current_user_id = get_current_user_id();

            // get information about users
            $user_info = get_userdata( $user_id );

            $owner_info = get_userdata( $owner_id );
            $comment = json_decode($booking_data['comment']);
            
            switch ( $status )
            {
                case 'waiting' :

                    /*$mail_to_user_args = array(
                        'email' => $user_info->user_email,
                        'booking'  => $booking_data,
                        'mail_to_user'  => "buyer",
                    );
                    do_action('listeo_mail_to_user_waiting_approval',$mail_to_user_args);

                    $mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        'booking'  => $booking_data,
                        'mail_to_user'  => "owner",
                    );

                    do_action('listeo_mail_to_owner_new_reservation',$mail_to_owner_args);

                    break;*/
                case 'confirmed' :

                    //
                    $instant_booking = get_post_meta( $booking_data['listing_id'], '_instant_booking', true);

                    if($instant_booking) {

                        $mail_to_user_args = array(
                            'email' => $user_info->user_email,
                            //'email' => "performgood1202@gmail.com",
                            'booking'  => $booking_data,
                            'mail_to_user'  => "buyer",
                            'force_send_mail' => true
                        );
                        do_action('listeo_mail_to_user_instant_approval',$mail_to_user_args);
                        // wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your reservation waiting for be approved by owner!', 'listeo_core' ) );

                        // mail for owner
                        $mail_to_owner_args = array(
                            'email'     => $owner_info->user_email,
                           //'email' => "performgood1202@gmail.com",
                            'booking'  => $booking_data,
                            'mail_to_user'  => "owner",
                            'force_send_mail' => true
                        );

                        do_action('listeo_mail_to_owner_new_instant_reservation',$mail_to_owner_args);

                    }


                        // for free listings
                    if ( $booking_data['price'] == 0 || $booking_data['price'] == "")
                    {

                        // mail for user
                        //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your is paid!', 'listeo_core' ) );
                        $mail_args = array(
                            'email'     => $user_info->user_email,
                            //'email' => "performgood1202@gmail.com",
                            'booking'  => $booking_data,
                            'mail_to_user'  => "buyer",
                            'force_send_mail' => true
                        );
                        do_action('listeo_mail_to_user_free_confirmed',$mail_args);

                        break;

                    }
                    if(isset($order->id)){
                        $order_id =  $order->id;
                    }else{
                        $order_id =  "";
                    }
                    
                       

                        // mail for user
                        //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), sprintf( __( 'Your reservation waiting for payment! Please do it before %s hours. Here is link: %s', 'listeo_core' ), $expired_after, $payment_url  ) );
                        $mail_args = array(
                           'email'         => $user_info->user_email,
                           // 'email' => "performgood1202@gmail.com",
                            'booking'       => $booking_data,
                            'expiration'    => "",
                            'payment_url'   => $payment_url,
                            'order_id'   => $order_id,
                             'mail_to_user'  => "buyer",
                            'force_send_mail' => true
                        );

                        //echo "<pre>"; print_r($mail_args); die;

                        do_action('listeo_mail_to_user_pay',$mail_args);
                        
                        break;
                case 'paid' :
                    /*$mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        //'email' => "performgood1202@gmail.com",
                        'booking'  => $booking_data,
                         'mail_to_user'  => "owner",
                    );
                    do_action('listeo_mail_to_owner_paid',$mail_to_owner_args);*/
     
                    break;
                /* case 'cancelled' : */
                case 'cancelled' :
                    $mail_to_user_args = array(
                        'email'     => $user_info->user_email,
                        //'email' => "performgood1202@gmail.com",
                        'booking'  => $booking_data,
                        'mail_to_user'  => "buyer",
                        'force_send_mail' => true 
                    );


                    do_action('listeo_mail_to_user_canceled',$mail_to_user_args);
     
                    break;

            }
               

        }

        return $order_id;

    }
    public static function get_comment_data($user_id){

        $userData = get_userdata($user_id);

        

        $comment= array(
                        'first_name'    => $userData->first_name,
                        'last_name'     => $userData->last_name,
                        'email'         => $userData->user_email,
                        'phone'         => get_user_meta($user_id,"phone",true),
                        'country_code'  => get_user_meta($user_id,"country_code",true),
                        'message'       => "",
                        'billing_address_1' => get_user_meta($user_id,"billing_address_1",true),
                        'billing_postcode'  => get_user_meta($user_id,"billing_postcode",true),
                        'billing_city'      => get_user_meta($user_id,"billing_city",true),
                        'billing_country'   => get_user_meta($user_id,"billing_country",true)
                    );
        return $comment;

    }

    public function updateEventsFinal($updatedata, $booking_id){

        global $wpdb;

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $wpdb->update(
            $booking_table,
            $updatedata,
            array('id' => $booking_id)
        );

    }

    public static function deleteLinkEvents($data){

        global $wpdb;

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $id = $data['id'];

        $booking_data = $wpdb->get_row('SELECT * FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($id), 'ARRAY_A');

        $first_event_id = $booking_data["first_event_id"];

        if($first_event_id && $first_event_id != ""){

            if($data['linkEditMode'] == "all"){

                $booking_data_all = $wpdb->get_results('SELECT * FROM `'  . $booking_table . '` WHERE `first_event_id`=' . esc_sql($first_event_id), 'ARRAY_A');


                foreach ($booking_data_all as $key => $booking_d) {
                    $wpdb->update( $booking_table, array("status"=>"deleted"), array( 'id' => $booking_d["id"] ));
                    //$wpdb->delete($booking_table, array('id' => $booking_d["id"]));
                }


            }else if($data['linkEditMode'] == "current"){
                $wpdb->update( $booking_table, array("status"=>"deleted"), array( 'id' => $booking_data["id"] ));
                //$wpdb->delete($booking_table, array('id' => $booking_data["id"]));

            }

        }

        wp_send_json(array('status' => 200));

    }
    public static function updateLinkEvents($data){

        global $wpdb;

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $comment = "";

        if(isset($data["wpm_client"])){
           $comment_data =  self::get_comment_data($data["wpm_client"]);
           $comment = json_encode($comment_data);
        }

        $booking_id  = $data['id'];

        $status = $data['status'];

        if ($status == "closed") {
            $list_id = $data['gymSectionId'];
            $list_table = $wpdb->prefix . 'posts';
            $user_groups_id = $wpdb->get_var("select users_groups_id from $list_table where id=$list_id");
            $closed_user_id = get_current_user_ID();
        } else {
            $closed_user_id = '';
            $user_groups_id = '';
        }

        if ($status == "closed") {
            $status = "paid";
            $fixed = "1";
        } elseif ($status == "sesongbooking") {
            $status = "paid";
            $fixed = "2";
        } elseif ($status == "manual_invoice") {
            $status = "paid";
            $fixed = "3";
        }
         else {
            $fixed = "0";
        }

        $id = $wpm_client = $team = $start = $end = $description = $repert = $recurrenceRule = $recurrenceException = $recurrenceId = $gymSectionId = "";
        $id             = $data['id'];
        $wpm_client     = $data['wpm_client'];
        $start          = $data['start'];
        $end            = $data['end'];
        $description    = $data['description'];
        $repert         = $data['repert'];
        $title          = $data['title'];
        $recurrenceRule = $data['recurrenceRule'];
        $recurrenceException = isset($data['recurrenceException']) ? json_encode($data['recurrenceException']) : '';
        $recurrenceId        = $data['recurrenceId'];
        $gymSectionId   = $data['gymSectionId'];
        $owner_id = $wpdb->get_var("select post_author from ptn_posts where id=$gymSectionId");

        $date_start = date("Y/m/d H:i", strtotime($start));
        $date_end   = date("Y/m/d H:i", strtotime($end));

        $booking_data = $wpdb->get_row('SELECT * FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($id), 'ARRAY_A');

        $first_event_id = $booking_data["first_event_id"];


        $updatedata = array(
                'bookings_author'       => $wpm_client,
                'date_start'            => $date_start,
                'date_end'              => $date_end,
                'description'           => $description,
                'repert'                => $repert,
                'status'                => $status,
                'fixed'                 => $fixed,
                'title'                 => $title,
                'order_id'              => $order_id,
                'closed_user_id'        => $closed_user_id,
                'closed_user_group_id'  => $user_groups_id,
                'recurrenceRule'        => $recurrenceRule,
                'recurrenceID'          => $recurrenceId,
                'recurrenceException'   => $recurrenceException,
                'comment'              => $comment,
            );



        if($first_event_id && $first_event_id != ""){

            if($data['linkEditMode'] == "all"){

                $booking_data_all = $wpdb->get_results('SELECT * FROM `'  . $booking_table . '` WHERE `first_event_id`=' . esc_sql($first_event_id), 'ARRAY_A');

                //echo "<pre>"; print_r($booking_data_all); die;


                foreach ($booking_data_all as $key => $booking_d) {
                    self::updateEventsFinal($updatedata, $booking_d["id"]);
                }


            }else if($data['linkEditMode'] == "current"){

                $updatedata["first_event_id"] = null;
                $updatedata["unlink_first_event_id"] = $first_event_id;

                self::updateEventsFinal($updatedata, $booking_data["id"]);

            }

        }

        wp_send_json(array('status' => 200));



    }

    public static function first_event_id_data($data){

        global $wpdb;

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $id = $data['id'];

        $booking_data = $wpdb->get_row('SELECT * FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($id), 'ARRAY_A');

        $first_event_id = $booking_data["first_event_id"];

        $return_data = array();

        if($first_event_id && $first_event_id != ""){
                $booking_data_all = $wpdb->get_results('SELECT * FROM `'  . $booking_table . '` WHERE `first_event_id`=' . esc_sql($first_event_id), 'ARRAY_A');

                foreach ($booking_data_all as $key => $booking_d) {
                    $return_data[] = $booking_d["id"];
                }
        }
        return $return_data;


    }
    public function updateBookingOrder($booking_id,$data,$price){

        global $wpdb;

        $order_idd = "";

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $booking_where = "WHERE id = ".$booking_id;

        $booking_data = $wpdb->get_row("SELECT * FROM $booking_table $booking_where");

        if(isset($booking_data->order_id) && $booking_data->order_id != "" && $booking_data->order_id > 0){

        }else{
            if($data["status"] == "confirmed" || $data["status"] == "paid" || $data["status"] == "manual_invoice"){

                $order_idd = Gibbs_Admin_Calendar_API::createBookingOrder($booking_id,$data,$price);

                if($order_idd != "" && $order_idd > 0){

                    $wpdb->update($booking_table, array('order_id' => $order_idd), array('id' => $booking_id));

                }
                return $order_idd;

                exit;

            }
        }


        if(isset($booking_data->order_id) && $booking_data->order_id != "" && $booking_data->order_id > 0 && $booking_data->status != "paid"){

            $order = wc_get_order($booking_data->order_id);




            foreach( $order->get_items() as $item_id => $item ){
                $product = $item->get_product();
                $product_price = $price; // A static replacement product price
                $new_quantity = 1; // product Quantity
                
                // The new line item price
                $new_line_item_price = $product_price * $new_quantity;
                
                // Set the new price
                $item->set_quantity(1);
                $item->set_subtotal( $new_line_item_price ); 
                $item->set_total( $new_line_item_price );

                // Make new taxes calculations
                $item->calculate_taxes();

                $item->save(); // Save line item data
            }
            // Make the calculations  for the order and SAVE
            if(isset($data["disable_order_mail"]) && $data["disable_order_mail"] == true){

               $order->update_meta_data('disable_order_mail', "true");
            } 
            $order->calculate_totals();
            $order->save();

            if($price < 1){


                $booking_id = $booking_data->id;

                $order = wc_get_order( $order->id );

                $update_values['order_id'] = $order->id;
                $update_values['status'] = "paid";

                $wpdb->update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'id' => $booking_id ) );

                $order->update_status( 'completed' );
                
            }    

            $order_idd = $order->id;

        }
        return $order_idd;


    }

    public function refundAmount($bkdata){

        global $wpdb;
        
        $order = wc_get_order( $bkdata->order_id );

        $commm = new Listeo_Core_Commissions;
        $commm = $commm->listeo_wallet(array(),true);

        $balance = 0;

        

        if(isset($commm["commissions"])){

            foreach ($commm["commissions"] as $commission) { 
                if($commission['status'] == "unpaid") :
                    
                    $order_ddd = wc_get_order( $commission['order_id'] );
                    $bk_idd = get_post_meta($order_ddd->id,"booking_id",true);
                    if($order_ddd->get_type() != "shop_order_refund"){
                        if($bk_idd != ""){

                            $dibs_payment_id = get_post_meta($order_ddd->id,"_dibs_charge_id",true);
                            if($dibs_payment_id != ""){
                                
                            }else{
                                continue;
                            }
                            
                        }else{
                            continue;
                        }
                        $total = $order_ddd->get_total();
                        
                        $earning = (float) $total - $commission['amount'];
                        $balance = $balance + $earning;	
                    }else{

                        $total = $order_ddd->get_total();
                    
                        $balance = $balance + $total; 

                        $balance = $balance + $commission['amount'];

                        //echo "<pre>"; print_r($commission);

                    }
                    
                endif;
            }
        } 
       // echo $balance; die; 
        


        // wp_send_json(array( 'error' => 1,'message' => "refund"));
        //     die;

        if (!$order) {
            wp_send_json(array( 'error' => 1,'message' => "Order not found!"));
            die;
        }
        $charge_id_exist = get_post_meta( $bkdata->order_id, '_dibs_charge_id', true );

        $order_status  = $order->get_status();

        if($charge_id_exist != "" && $order_status == "completed"){
            $charge_id = $charge_id_exist;

            $refund_total = $order->get_total(); // This gets the total order amount. Modify as needed for partial refunds.
           
            if($balance <= $refund_total){
                   wp_send_json(array( 'error' => 1,'message' => "refund", 'error_type' => 'refund'));
                   die;
            }
            if($_POST["refund"] > $refund_total){
                    wp_send_json(array( 'error' => 1,'message' => "refund", 'error_type' => 'refund'));
                    die;
            }

            if (isset($_POST["refund"])) {
                // Sanitize and convert to a float
                $refund_price = floatval(sanitize_text_field($_POST["refund"]));
                // Format to two decimal places
                $refund_price = number_format($refund_price, 2, '.', '');
            } else {
                $refund_price = '0.00'; // Default value
            }

           

            $commm_sql2 = "SELECT * from ".$wpdb->prefix."listeo_core_commissions where order_id = " . $bkdata->order_id;
            $commm_sql2_data = $wpdb->get_row($commm_sql2);

            $data_comm = array();

            if(isset($commm_sql2_data->id)){
                $data_comm["user_id"] = $commm_sql2_data->user_id;
                $data_comm["amount"] = $commm_sql2_data->amount;
                $data_comm["rate"] = $commm_sql2_data->rate;
                $data_comm["status"] = "unpaid";
                $data_comm["date"] = date("Y-m-d H:i:s");
                $data_comm["type"] = $commm_sql2_data->type;
                $data_comm["booking_id"] = $commm_sql2_data->booking_id;
                $data_comm["listing_id"] = $commm_sql2_data->listing_id;
            }

            $line_items = array();
            $total_refund_allocated = 0;

            foreach ($order->get_items() as $item_id => $item) {
                $item_total = $item->get_total(); // Total price of the item

                // Calculate refunded total manually if get_total_refunded() doesn't work
                $refunded_total = 0;
                foreach ($order->get_refunds() as $refund) {
                    foreach ($refund->get_items() as $refunded_item) {
                        if ($refunded_item->get_meta('_refunded_item_id') == $item_id) {
                            $refunded_total += $refunded_item->get_total();
                        }
                    }
                }

                $refundable_amount = $item_total - $refunded_total; // Calculate refundable amount

                if ($refundable_amount > 0) {
                    $refund_amount = min($refundable_amount, $refund_price - $total_refund_allocated); // Allocate refund

                    $line_items[$item_id] = array(
                        'qty'          => $item->get_quantity(),
                        'refund_total' => $refund_amount,
                    );

                    $total_refund_allocated += $refund_amount;

                    if ($total_refund_allocated >= $refund_price) {
                        break;
                    }
                }
            }

            // Create the refund
            $refund = wc_create_refund(array(
                'amount'         => $refund_price,
                'reason'         => 'Refund manually initiated by admin',
                'order_id'       => $bkdata->order_id,
                'line_items'     => $line_items,
                'refund_payment' => true, // Trigger refund via the payment gateway
                'restock_items'  => false, // Don't restock refunded items
            ));

            
            

            if (is_wp_error($refund)) {
                wp_send_json(array( 'error' => 1,'message' => $refund->get_error_message(), 'error_type' => 'refund_woo'));
                wp_die('Refund failed: ' . $refund->get_error_message());
            }

            
        
            // Add a custom order note
            $order->add_order_note('Refund of ' . wc_price($refund_price) . ' processed manually.');
        
            // Optionally, change order status to refunded
            //$order->update_status('refunded', 'Order has been manually refunded.');

            //echo "<pre>"; print_r($data_comm); die;
            


            if(!empty($data_comm)){
                $refund_id = $refund->get_id();
                $data_comm["order_id"] = $refund_id;

                $wpdb->insert($wpdb->prefix."listeo_core_commissions", $data_comm );

                $refund_data = array();

                $refunds = $order->get_refunds();


                foreach ($refunds as $refund) {
                    if ($refund->get_type() == "shop_order_refund") {
                        // Gather individual refund details
                        $refund_info = array(
                            "price"        => wc_price($refund->get_total()), // Formatted price
                            "price_number" => $refund->get_total(), // Refund amount as a number
                            "date"         => $refund->get_date_created()->format("Y-m-d H:i:s"), // Refund date
                            "reason"       => $refund->get_reason() ? $refund->get_reason() : 'No reason provided', // Refund reason
                        );

                        // Add refund info to the data array
                        $refund_data[] = $refund_info;
                    }
                }

                // $refund_data = array();

                // $refund_data["price"] = wc_price($refund->get_total());
                // $refund_data["price_number"] = $refund->get_total();
                // $refund_data["date"] = $refund->get_date_created()->format("Y-m-d H:i:s");

                if(!empty($refund_data)){

                    $data_ref = array("refund_data"=>json_encode($refund_data));

                    $wpdb->update($wpdb->prefix."bookings_calendar", $data_ref, array("id" => $bkdata->id) );
                    //echo "<pre>"; print_r($wpdb);die;
                }

            }

            

        }
    }

    public static function wpm_update_record()
    {


        

        global $wpdb;

        

        $booking_table = $wpdb->prefix . 'bookings_calendar';

        $comment = "";

        $comment_data = array();

        //echo "<pre>"; print_r($_POST);

        if(isset($_POST["wpm_client"])){
           $comment_data =  self::get_comment_data($_POST["wpm_client"]);
           if(isset($_POST["guest"]) && $_POST["guest"] != ""){
             $comment_data["adults"] = $_POST["guest"];
           }else{
            if(isset($_POST["amount_guest"])){
                $comment_data["adults"] = $_POST["amount_guest"];
                $_POST["guest"] = $_POST["amount_guest"];
            }else{
                $comment_data["adults"] = 1;
            }
             
           }
           
           
        }
        //echo "<pre>"; print_r($_POST); die;

        

        $bookings_calendar_sql2 = "SELECT * from ".$wpdb->prefix."bookings_calendar where id = " . $_POST["id"];
        $bookings_calendar_data2 = $wpdb->get_row($bookings_calendar_sql2);

        if(isset($_POST["refund"]) && $_POST["refund"] != "" && isset($bookings_calendar_data2->id)){
            self::refundAmount($bookings_calendar_data2);
        }


        $is_logged = false;

        if($_POST["start"] != $bookings_calendar_data2->date_start){
            $is_logged = true;
        }

        if($_POST["end"] != $bookings_calendar_data2->date_end){
            $is_logged = true;
        }
        if($_POST["wpm_client"] != $bookings_calendar_data2->bookings_author){
            $is_logged = true;
        }

        

        if(isset($bookings_calendar_data2->comment) && $bookings_calendar_data2->comment != ""){
            $cm_data = json_decode($bookings_calendar_data2->comment,true);
            $com_another_data = $cm_data;
            if(isset($comment_data["first_name"]) && $comment_data["first_name"] != ""){
                $com_another_data["first_name"] = $comment_data["first_name"];
            }
            if(isset($comment_data["last_name"]) && $comment_data["last_name"] != ""){
                $com_another_data["last_name"] = $comment_data["last_name"];
            }
            if(isset($comment_data["email"]) && $comment_data["email"] != ""){
                $com_another_data["email"] = $comment_data["email"];
            }
            if(isset($comment_data["phone"]) && $comment_data["phone"] != ""){
                $com_another_data["phone"] = $comment_data["phone"];
            }
            if(isset($comment_data["country_code"]) && $comment_data["country_code"] != ""){
                $com_another_data["country_code"] = $comment_data["country_code"];
            }
            if(isset($comment_data["message"]) && $comment_data["message"] != ""){
                $com_another_data["message"] = $comment_data["message"];
            }
            if(isset($comment_data["billing_address_1"]) && $comment_data["billing_address_1"] != ""){
                $com_another_data["billing_address_1"] = $comment_data["billing_address_1"];
            }
            if(isset($comment_data["billing_postcode"]) && $comment_data["billing_postcode"] != ""){
                $com_another_data["billing_postcode"] = $comment_data["billing_postcode"];
            }
            if(isset($comment_data["billing_city"]) && $comment_data["billing_city"] != ""){
                $com_another_data["billing_city"] = $comment_data["billing_city"];
            }
            if(isset($comment_data["billing_country"]) && $comment_data["billing_country"] != ""){
                $com_another_data["billing_country"] = $comment_data["billing_country"];
            }
            if(isset($comment_data["adults"]) && $comment_data["adults"] != ""){
                $com_another_data["adults"] = $comment_data["adults"];
            }

            $comment_data = $com_another_data;


        }

        
        //echo "<pre>"; print_r($comment_data); die;

        $comment = json_encode($comment_data);

        

        $bookings_calendar_meta_sql = "SELECT * from bookings_calendar_meta where meta_key = 'number_of_guests'  AND booking_id = " . $_POST["id"];
        $bookings_calendar_meta_data = $wpdb->get_row($bookings_calendar_meta_sql);

       // echo "<pre>"; print_r($bookings_calendar_meta_data); die;

        if(isset($bookings_calendar_meta_data->meta_value) && $bookings_calendar_meta_data->meta_value != ""){
            $amount_guest = $bookings_calendar_meta_data->meta_value;
            
            if(isset($_POST["guest"]) && $_POST["guest"] != ""){
                $wpdb->update("bookings_calendar_meta",
                    array(
                        "meta_value" => $_POST["guest"]
                    ),
                    array(
                        "id" =>$bookings_calendar_meta_data->id
                    )
                );
            }
            
        }else if(isset($_POST["guest"]) && $_POST["guest"] > 0){

            $wpdb->insert("bookings_calendar_meta",
                    array(
                        "meta_key" => "number_of_guests",
                        "meta_value" => $_POST["guest"],
                        "booking_id" => $_POST["id"]
                    )
                );

        }

        //echo "<pre>"; print_r($wpdb); die;

        if(isset($_POST["editLinkEvent"]) && $_POST["editLinkEvent"] == true){

            if($_POST["linkEditType"] == "delete"){

                self::deleteLinkEvents($_POST);

            }else{

                self::updateLinkEvents($_POST);

            }

            exit();

        }
        $closed == false;

        $first_event_id_data = self::first_event_id_data($_POST);

        $price = 0;

        if(isset($_POST["price"]) && $_POST["price"] != "" && $_POST["price"] > 0){
            $price = $_POST["price"];
        }

        $_POST["disable_order_mail"] = true;

        if(isset($_POST["sendmail"]) && $_POST["sendmail"] == "true"){
            $_POST["disable_order_mail"] = false;

        }


        if(isset($_POST['recurrenceRule']) && $_POST['recurrenceRule'] != ""){


            $_POST['recurrenceRule'] = self::updateRecRule($_POST['recurrenceRule']);

        }

        


       

        



        $booking_id             = $_POST['id'];
        $newRecurrenceException = isset($_POST['recurrenceException']) ? $_POST['recurrenceException'] : [];

        Gibbs_Admin_Calendar_Logger::write_log($newRecurrenceException);

        if (is_array($newRecurrenceException) && count($newRecurrenceException) > 0) {

            $listing_id = $_POST['resource'];

            $booking_updated = $wpdb->query("UPDATE $booking_table SET recurrenceException='" . json_encode($newRecurrenceException) . "' WHERE id=" . $_POST['id']);

            // Create another event for the exception
            $start      = $_POST['start'];
            $end        = $_POST['end'];
            $date_start = date("Y/m/d H:i", strtotime($start));
            $date_end   = date("Y/m/d H:i", strtotime($end));
            $status     = $_POST['status'];

            $booking_data = $wpdb->get_row('SELECT `bookings_author`, `team_id`, `description`, `repert`, `status`, `title`, `type`, `fixed`, `closed_user_id`, `closed_user_group_id`, `recurrenceID`, `listing_id`, `owner_id` FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($booking_id), 'ARRAY_A');
            $booking_data['date_start']     = $date_start;
            $booking_data['date_end']       = $date_end;
            $booking_data['listing_id']     = $listing_id;
            $booking_data['recurrenceID']   = $booking_id;
            $booking_data['status']         = $status;
            $booking_data['price']         = $price;
            $booking_data['comment']         = $comment;
            if(!empty($first_event_id_data) && $_POST["recurrenceEditMode"] == "current"){
                $booking_data["first_event_id"] = null;
                $booking_data["unlink_first_event_id"] = $booking_data["first_event_id"];
            }

            if (isset($_POST['newEvent'])) {
                foreach ($_POST['newEvent'] as $k => $v) {
                    if (isset($booking_data[$k])) $booking_data[$k] = $v;
                }
            }

            $is_deleted = isset($_POST['event_action']) && $_POST['event_action'] === 'delete';

            if (!$is_deleted) {
                $insert = $wpdb->insert($booking_table, $booking_data);
            }
        } else {
            $status = $_POST['status'];

            if ($status == "closed") {
                $list_id = $_POST['gymSectionId'];
                $list_table = $wpdb->prefix . 'posts';
                $user_groups_id = $wpdb->get_var("select users_groups_id from $list_table where id=$list_id");
                $closed_user_id = get_current_user_ID();
                $closed == true;
            } else {
                $closed_user_id = '';
                $user_groups_id = '';
            }

            if ($status == "closed") {
                $status = "paid";
                $fixed = "1";
                $closed == true;
            } elseif ($status == "sesongbooking") {
                $status = "paid";
                $fixed = "2";
            } elseif ($status == "manual_invoice") {
                $status = "paid";
                $fixed = "3";
            }
             else {
                $fixed = "0";
            }

            if(isset($bookings_calendar_data2->googleEventId) && $bookings_calendar_data2->googleEventId != ""){
                $status = "paid";
                $fixed = "4";
    
            }
            if(isset($bookings_calendar_data2->outlookEventId) && $bookings_calendar_data2->outlookEventId != ""){
                $status = "paid";
                $fixed = "4";
    
            }

            $id = $wpm_client = $team = $start = $end = $description = $repert = $recurrenceRule = $recurrenceException = $recurrenceId = $gymSectionId = "";
            $id             = $_POST['id'];
            $wpm_client     = $_POST['wpm_client'];
            $team           = $_POST['team'];
            $start          = $_POST['start'];
            $end            = $_POST['end'];
            $description    = $_POST['description'];
            $repert         = $_POST['repert'];
            $title          = $_POST['title'];
            $recurrenceRule = $_POST['recurrenceRule'];
            $recurrenceException = isset($_POST['recurrenceException']) ? json_encode($_POST['recurrenceException']) : '';
            $recurrenceId        = $_POST['recurrenceId'];
            $gymSectionId   = $_POST['gymSectionId'];
            $owner_id = $wpdb->get_var("select post_author from ptn_posts where id=$gymSectionId");


            $booking_data = $wpdb->get_row('SELECT * FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($id), 'ARRAY_A');

            $order_id = "";

            $send_mail = 0;
            if ($booking_data) {
                if ($booking_data['status'] != $status) {

                    $order_id =  Gibbs_Admin_Calendar_Utility::send_mail_booking($booking_table, $id, $status, $_POST["disable_order_mail"]);
                    
                }else{
                    $order_id = $booking_data->order_id;
                }
            }

            $wpdb->show_errors = true;

            if (isset($_POST['recurrenceEditMode']) && $_POST['recurrenceEditMode'] === 'following') {
                // Set current event to expire if not a delete action
                if (isset($_POST['event_action']) && $_POST['event_action'] === 'delete') {
                    $followingRecurrenceRule = $_POST['recurrenceRule'];
                } else {
                    $recurrenceRuleParts = explode(';', $booking_data['recurrenceRule']);

                    // Remove recurrence UNTIL key if it exists for the current booking
                    // Will be replaced with the start date of the new event 
                    foreach ($recurrenceRuleParts as $key => $part) {
                        // Remove UNTIL key or any empty string
                        if (empty($part) || (!empty($part) && strpos($part, 'UNTIL') !== false)) {
                            unset($recurrenceRuleParts[$key]);
                            break;
                        }
                    }

                    // Set the current date to run until the start date of the new booking
                    $recurrenceRuleParts[] = 'UNTIL=' . date("Y-m-d", strtotime($start));

                    $followingRecurrenceRule = implode(';', $recurrenceRuleParts);

                    // Remove any double semi colons
                    // @todo These are due to spaces that should be removed before foreach loop
                    $followingRecurrenceRule = str_replace(';;', ';', $followingRecurrenceRule);
                }

                $insert_Data =  array(
                                    'bookings_author'       => $wpm_client,
                                    'team_id'               => $team,
                                    'date_start'            => date("Y/m/d H:i", strtotime($start)),
                                    'date_end'              => date("Y/m/d H:i", strtotime($end)),
                                    'description'           => $description,
                                    'repert'                => $repert,
                                    'status'                => $status,
                                    'price'                 => $price,
                                    'title'                 => $title,
                                    'type'                  => 'reservation',
                                    'fixed'                 => $fixed,
                                    'closed_user_id'        => $closed_user_id,
                                    'closed_user_group_id'  => $user_groups_id,
                                    'recurrenceRule'        => $recurrenceRule,
                                    'recurrenceID'          => $recurrenceId,
                                    'listing_id'            => $gymSectionId,
                                    'owner_id'              => $owner_id,
                                    'comment'              => $comment,
                                );

                if($order_id != ""){
                    $insert_Data["order_id"] = $order_id;
                }

                if(!empty($first_event_id_data)){

                        $first_event_id_new = "";

                        $booking_ids = [];

                        foreach ($first_event_id_data as $key_bkk => $booking_idd) {
                            
                            $wpdb->query("UPDATE $booking_table SET recurrenceRule='$followingRecurrenceRule' WHERE id=" . $booking_idd);

                            if (isset($_POST['create_new_event']) && (int) $_POST['create_new_event'] === 1) {
                                $cr_booking_data = $wpdb->get_row('SELECT * FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($booking_idd), 'ARRAY_A');

                                if(isset($cr_booking_data["id"])){

                                    $insert_Data["listing_id"] = $cr_booking_data["listing_id"];
                                    $insert_Data["owner_id"] = $cr_booking_data["owner_id"];
                                    //$insert_Data["first_event_id"] = $cr_booking_data["first_event_id"];
                                    //$insert_Data["unlink_first_event_id"] = $cr_booking_data["unlink_first_event_id"];

                                    $wpdb->insert($booking_table, $insert_Data);

                                    $bk_id = $wpdb->insert_id;

                                    $booking_ids[] = $bk_id;

                                    if ($key_bkk == 0) {
                                        $first_event_id_new = $bk_id;
                                    }

                                }

                                
                            }
                        }

                        //if(count($_POST['listings']) > 1){
                            foreach ($booking_ids as $key => $booking_iddd) {
                               $wpdb->update($booking_table, array('first_event_id' => $first_event_id_new), array('id' => $booking_iddd));  
                            }
                        //}

                }else{

                    $wpdb->query("UPDATE $booking_table SET recurrenceRule='$followingRecurrenceRule' WHERE id=" . $_POST['id']);

                    // Create new recurrence if set on mobiscroll
                    if (isset($_POST['create_new_event']) && (int) $_POST['create_new_event'] === 1) {
                        $wpdb->insert($booking_table, $insert_Data);
                    }

                }

                
            }  else {

                $date_start = date("Y/m/d H:i", strtotime($start));
                $date_end   = date("Y/m/d H:i", strtotime($end));

                $updatedata = array(
                        'bookings_author'       => $wpm_client,
                        'team_id'               => $team,
                        'date_start'            => $date_start,
                        'date_end'              => $date_end,
                        'description'           => $description,
                        'repert'                => $repert,
                        'status'                => $status,
                        'price'                => $price,
                        'fixed'                 => $fixed,
                        'title'                 => $title,
                        'closed_user_id'        => $closed_user_id,
                        'closed_user_group_id'  => $user_groups_id,
                        'recurrenceRule'        => $recurrenceRule,
                        'recurrenceID'          => $recurrenceId,
                        'listing_id'            => $gymSectionId,
                        'recurrenceException'   => $recurrenceException,
                        'owner_id'              => $owner_id,
                        'comment'              => $comment,
                    );

                if($order_id != ""){
                    $updatedata["order_id"] = $order_id;
                }
                if($is_logged == true){
                    $updatedata["is_logged"] = "0";
                }

                if(!empty($first_event_id_data)){
                    unset($updatedata["listing_id"]);
                    unset($updatedata["owner_id"]);
                }

                if (isset($_POST['recurrenceEditMode']) && $_POST['recurrenceEditMode'] === 'all') {
                    $updatedata["date_start"] = date("Y/m/d H:i", strtotime(date("Y/m/d", strtotime($booking_data['date_start'])) . " " . date("H:i", strtotime($start))));
                    $updatedata["date_end"] = date("Y/m/d H:i", strtotime(date("Y/m/d", strtotime($booking_data['date_end'])) . " " . date("H:i", strtotime($end))));
                    if(!empty($first_event_id_data)){
                        foreach ($first_event_id_data as $key => $booking_idd) {
                            self::updateEventsFinal($updatedata, $booking_idd);
                        }
                    }else{
                        self::updateEventsFinal($updatedata, $id);
                    }
                }else{

                    if(!empty($first_event_id_data)){
                        $updatedata["first_event_id"] = null;
                        $updatedata["unlink_first_event_id"] = $booking_data["first_event_id"];
                    }

                    self::updateEventsFinal($updatedata, $id);
                }

                
            }
        }
        if(($_POST["status"] == "confirmed" || $_POST["status"] == "paid" || $_POST["status"] == "manual_invoice") && $closed == false){
            self::updateBookingOrder($_POST['id'],$_POST,$price);
        }else{
            if($_POST["disable_order_mail"] == false){
              Gibbs_Admin_Calendar_API::send_mail_booking($booking_table,$_POST['id'],$_POST["status"]);
            }
        }

        if($owner_id != "" && $wpm_client != ""){
            $log_args = array(
                'action' => "booking_updated",
                'related_to_id' => $owner_id,
                'user_id' => $wpm_client,
                'post_id' => $_POST["id"]
            );
            listeo_insert_log($log_args);
        }

        $booking_from_info = array(

            "booking_from" => "admin_cal",
            "booking_type" => "update",
            "date" => date("Y-m-d H:i:s"),

        );

        $wpdb->insert("bookings_calendar_meta",
                    array(
                        "booking_id" => $_POST["id"],
                        "meta_key" => "booking_from",
                        "meta_value" => json_encode($booking_from_info)
                    )
                );

        

        
        

        wp_send_json(array('status' => 200));
    }

    public static function wpm_delete_record()
    {


        global $wpdb;

        $id = $_POST['id'];
        $record_table = $wpdb->prefix . 'bookings_calendar';
        if(isset($_POST["recurrenceEditMode"]) && $_POST["recurrenceEditMode"] == "all"){
            $first_event_id_data = self::first_event_id_data($_POST);

            if(!empty($first_event_id_data)){

                foreach ($first_event_id_data as $key => $bk_idd) {
                    $result = $wpdb->update( $record_table, array("status"=>"deleted"), array( 'id' => $bk_idd ));
                   // $wpdb->delete($record_table, array('id' => $bk_idd));
                    
                }

            }else{
                $result = $wpdb->update( $record_table, array("status"=>"deleted"), array( 'id' => $id ));
                //$wpdb->delete($record_table, array('id' => $id));
            }
        }else{
            $result = $wpdb->update( $record_table, array("status"=>"deleted"), array( 'id' => $id ));
            //$wpdb->delete($record_table, array('id' => $id));
        }
        if ($result === false) { // Check if query failed
            if (!empty($wpdb->last_error)) {
                wp_send_json_error(array(
                    'message' => $wpdb->last_error,
                    'code'    => 'invalid_nonce'
                ), 200);
                exit;
            } else {
                wp_send_json_error(array(
                    'message' => "Query failed, but no error message is available.",
                    'code'    => 'invalid_nonce'
                ), 200);
                exit;
            }
        } else {
            if ($result === 0) {
                wp_send_json_error(array(
                    'message' => "No rows were updated.",
                    'code'    => 'invalid_nonce'
                ), 200);
                exit;
            }
        }
        

        wp_send_json_success(array('status' => 200));
    }

    public function updateRecRule($recc){
            $rules = explode(";", $recc);

                $rulesss = array();

                foreach ($rules as $key => $rule) {
                    if($rule != ""){
                        $rule = explode("=", $rule);
                        $rulesss[$rule[0]] = $rule[1];
                    }    
                }
                
                if(isset($rulesss["UNTIL"]) && $rulesss["UNTIL"] == "undefined"){
                  $rulesss["UNTIL"] = date('Y-m-d', strtotime(' +1 years'));
                }

                $rulesss_data = array();

                foreach ($rulesss as $key_rule => $rull) {
                   $rulesss_data[] = $key_rule."=".$rull;
                }

            return $recurrenceRuless = implode(";",$rulesss_data);
    }
}
