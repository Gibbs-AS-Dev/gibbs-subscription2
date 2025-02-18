<?php

class Gibbs_Season_Calendar_API
{

    public static function action_init()
    {
        add_action('wp_ajax_save_season_cal_filters', array('Gibbs_Season_Calendar_API', 'save_cal_filters'));

        add_action('wp_ajax_search_booking_data', array('Gibbs_Season_Calendar_API', 'search_booking_data'));

        add_action('wp_ajax_nopriv_get_season_booking_data', array('Gibbs_Season_Calendar_API', 'get_season_booking_data'));
        add_action('wp_ajax_get_season_booking_data', array('Gibbs_Season_Calendar_API', 'get_season_booking_data'));

        add_action('wp_ajax_nopriv_wpm_season_add_record', array('Gibbs_Season_Calendar_API', 'wpm_season_add_record'));
        add_action('wp_ajax_wpm_season_add_record', array('Gibbs_Season_Calendar_API', 'wpm_season_add_record'));

        add_action('wp_ajax_nopriv_wpm_season_update_record', array('Gibbs_Season_Calendar_API', 'wpm_season_update_record'));
        add_action('wp_ajax_wpm_season_update_record', array('Gibbs_Season_Calendar_API', 'wpm_season_update_record'));

        /*add_action('wp_ajax_nopriv_wpm_delete_record', array('Gibbs_Season_Calendar_API', 'wpm_delete_record'));
        add_action('wp_ajax_wpm_delete_record', array('Gibbs_Season_Calendar_API', 'wpm_delete_record'));*/


        add_action('wp_ajax_nopriv_wpm_get_season_booking_info', array('Gibbs_Season_Calendar_API', 'get_booking_info'));

        add_action('wp_ajax_wpm_get_season_booking_info', array('Gibbs_Season_Calendar_API', 'get_booking_info'));

        add_action('wp_ajax_get_season_booking_by_user', array('Gibbs_Season_Calendar_API', 'get_booking_by_user'));

        //add_action('wp_ajax_check_customer_email', array('Gibbs_Season_Calendar_API', 'check_customer_email'));

        //add_action('wp_ajax_addEventCustomer', array('Gibbs_Season_Calendar_API', 'addEventCustomer'));

        //add_action('wp_ajax_get_customer_list', array('Gibbs_Season_Calendar_API', 'get_customer_list'));

        add_action('wp_ajax_get_season_custom_fields_for_calender_mobiscroll', array('Gibbs_Season_Calendar_API', 'get_season_custom_fields_for_calender_mobiscroll'));

        add_action('wp_ajax_edit_field_save_for_calender_mobiscroll', array('Gibbs_Season_Calendar_API', 'edit_field_save_for_calender_mobiscroll'));

        add_action('wp_ajax_save_season_listing_filter_template_mobiscroll', array('Gibbs_Season_Calendar_API', 'save_season_listing_filter_template_mobiscroll'));

        add_action('wp_ajax_update_calender_filter_template_mobiscroll', array('Gibbs_Season_Calendar_API', 'update_calender_filter_template_mobiscroll'));

        add_action('wp_ajax_season_change_template', array('Gibbs_Season_Calendar_API', 'season_change_template'));

        add_action('wp_ajax_save_season_view_mobiscroll', array('Gibbs_Season_Calendar_API', 'save_season_view_mobiscroll'));

        add_action('wp_ajax_save_selected_season_mobiscroll', array('Gibbs_Season_Calendar_API', 'save_selected_season_mobiscroll'));

        add_action('wp_ajax_move_raw_booking_mobiscroll', array('Gibbs_Season_Calendar_API', 'move_raw_booking_mobiscroll'));

        add_action('wp_ajax_move_algo_booking_mobiscroll', array('Gibbs_Season_Calendar_API', 'move_algo_booking_mobiscroll'));

        add_action('wp_ajax_export_booking_mobiscroll', array('Gibbs_Season_Calendar_API', 'export_booking_mobiscroll'));

        add_action('wp_ajax_save_season_template_auto_checkbox', array('Gibbs_Season_Calendar_API', 'save_season_template_auto_checkbox'));
    }
    public function save_season_template_auto_checkbox(){

        global $wpdb;

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        update_user_meta($admin_user_id,"update_season_template_auto",$_POST['update_season_template_auto']);
        
        wp_send_json(array( 'error' => 0,'message' => "Lagret!"));
        die;
         
    }

    public function export_booking_mobiscroll(){

        global $wpdb;


        $seasons_sql_another = "SELECT * from `seasons` where id = ".$_POST['selected_season'];

        $seasons_data_another = $wpdb->get_row($seasons_sql_another);

        if(isset($seasons_data_another->season_start) && isset($seasons_data_another->season_end)){
            $season_start = $seasons_data_another->season_start;
            $season_end = $seasons_data_another->season_end;
        }

        $bookings_data_orgs = array();

        if(isset($season_start) && $season_start != "" && isset($season_end) && $season_end != ""){
            $data_bookings = self::get_season_booking_data("export_booking");


            foreach ($data_bookings as $key => $data_booking) {

                if($data_booking["rejected"] == "0"){
                    $data_booking["status"] = "sesongbooking";

                    if($_POST["type_of_form"] == "2"){

                        $bookings_data_orgs[] = $data_booking;

                    }else{
                    
                        $booking_start = $data_booking["start"];
                        $start_time = date("H:i:s",strtotime($data_booking['start']));
                        $end_time = date("H:i:s",strtotime($data_booking['end']));

                        $data_booking["start"] = $season_start." ".$start_time;
                        $data_booking["end"] = $season_start." ".$end_time;

                        $until = $season_end;

                        $byday = date("D",strtotime($booking_start));

                        $byday = substr($byday, 0,2);

                        $byday = strtoupper($byday);

                        $data_booking["recurrenceRule"] = "FREQ=WEEKLY;UNTIL=".$until.";BYDAY=".$byday.";WKST=MO;";

                        $bookings_data_orgs[] = $data_booking;
                    }
                }    
                
            }
            
        }


        $bookings_data_orgs_for_emails = array();

        foreach ($bookings_data_orgs as  $bookings_d_email) {
            if(isset($bookings_d_email["application_id"]) && isset($bookings_d_email["customer_email"]) && $bookings_d_email["customer_email"] != ""){
                $bookings_data_orgs_for_emails[$bookings_d_email["customer_email"]] = $bookings_d_email;
            }
        }



        if($_POST["export_type"] == "export"){

            foreach ($bookings_data_orgs as $key_bkk => $bookings_d) {
                if(isset($bookings_d["application_id"])){

                    self::wpm_season_add_record($bookings_d);
                    
                }
                
            }

           // echo "<pre>"; print_r($bookings_data_orgs_for_email_data); die;

            foreach ($bookings_data_orgs_for_emails as $key_bkk_email => $bookings_data_orgs_for_email_data) {
                    if($key_bkk_email != "" && isset($bookings_data_orgs_for_email_data["application_id"])){
                        self::sendEmailExport($bookings_data_orgs_for_email_data["application_id"],$key_bkk_email);
                    }
            }

        }else if($_POST["export_type"] == "export_csv"){
            self::season_export_csv($bookings_data_orgs);
        }
        return true;

    }

    public function sendEmailExport($booking_application_id,$email){
            
            global $wpdb;
            $table = 'applications';

            $data = $wpdb->get_row("SELECT * FROM $table WHERE id = ".$booking_application_id);
            if(isset($data->application_data_id) && $data->application_data_id != ""){
                $application_id = $data->application_data_id;
            }else{
                return;
            }
            

            $rec_email = $email;

          

            
            $admin_email = get_option('admin_email');

			$headers = 'From: Gibbs <' . $admin_email . '>' . "\r\n";

			/* $html = "Tildelte tider<br>";  */

			// Your custom description
			$description = "I linken under kan du se hva du har søkt og hva du har fått av tid.";

			// Append the description to the HTML content
			$html .= "<p>" . $description . "</p>";

			// Generate the link and append it to the HTML content
			$html .= "Link : " . home_url() . "/wp-json/v1/generateapp?application_id=" . $application_id;

			// Set the content type of the email to HTML
			add_filter('wp_mail_content_type', function( $content_type ) {
				return 'text/html';
			});

			// Send the email
			wp_mail($rec_email, 'Tildelte tider', $html, $headers);
    }
    public function season_export_csv($bookings){



          header('Content-Type: text/csv; charset=utf-8');  
          header('Content-Disposition: attachment; filename=export_booking.csv');  
          if(file_exists(GIBBS_CALENDAR_PATH.'assets/csv/export_booking.csv')){
            unlink(GIBBS_CALENDAR_PATH.'assets/csv/export_booking.csv');
          }
          $f = fopen(GIBBS_CALENDAR_PATH.'assets/csv/export_booking.csv', 'a');
          fputcsv($f, array('Start date', 'End date', 'Comment', 'Client',"Listing","Team","status","Recurrence rule"));  
          global $wpdb;

          foreach ($bookings as $key_v => $json_value) {
               $team_db = $wpdb->prefix . 'team';  // table name
               $team_id = $json_value["team"]["value"];
               $team_sql = "SELECT name from `$team_db` where id = '$team_id'";
               $team_data = $wpdb->get_row($team_sql);
               if($team_data){
                  $team_name = $team_data->name;
               }else{
                  $team_name = "";
               }
               $listing_db = $wpdb->prefix . 'posts';  // table name
               $listing_id = $json_value["gymSectionId"];
               $listing_sql = "SELECT post_title from `$listing_db` where ID = '$listing_id'";
               $listing_data = $wpdb->get_row($listing_sql);
               if($listing_data){
                  $listing = $listing_data->post_title;
               }else{
                  $listing = "";
               }
              fputcsv($f, array($json_value["start"], $json_value["end"], $json_value["description"], $json_value["client"]["text"],$listing,$team_name,$json_value["org_status"],$json_value["recurrenceRule"]));  
          }
          fclose($f);

          echo GIBBS_CALENDAR_URL."assets/csv/export_booking.csv";

          die;

    }
    public function move_raw_booking_mobiscroll(){

            global $wpdb;
            $selected_season = $_POST["selected_season"];

            $applications_sql = "SELECT id from applications where season_id = '$selected_season'";
            $applications_data = $wpdb->get_results($applications_sql);
            $app_ids = array();
            foreach ($applications_data as  $applications_d) {
               $app_ids[] = $applications_d->id;
            }
            $app_ids = implode(",", $app_ids);


            $bookings_calendar_raw_db = $wpdb->prefix . 'bookings_calendar_raw';  // table name
            $bookings_calendar_raw_sql = "SELECT * from `$bookings_calendar_raw_db` where application_id in ($app_ids)";
            $bookings_calendar_raw_data = $wpdb->get_results($bookings_calendar_raw_sql);

            foreach ($bookings_calendar_raw_data as $key => $value_approved) {

                $id = $value_approved->id;

                $bookings_calendar_raw_approved_db = $wpdb->prefix . 'bookings_calendar_raw_approved';  // table name
                $bookings_calendar_raw_approved_sql = "SELECT * from `$bookings_calendar_raw_approved_db` where id=$id";
                $bookings_calendar_raw_approved_data = $wpdb->get_results($bookings_calendar_raw_approved_sql);
                //die;

                $wpdb->update("applications", array(
                    'score'                    => null,
                    ), array('id'=>$value_approved->application_id)
                );

                if(count($bookings_calendar_raw_approved_data) > 0){
                   $wpdb->update($bookings_calendar_raw_approved_db, array(
                        'team_id'                    => $value_approved->team_id,
                        'repert'                    => $value_approved->repert,
                        'application_id'                    => $value_approved->application_id,
                        'date_start'                    => $value_approved->date_start,
                        'date_end'                    => $value_approved->date_end,
                        'first_event_id'                    => $value_approved->first_event_id,
                        'rejected'                    => 0,
                        'modified'                    => 2,
                        'description'                    => $value_approved->description,
                        'title'                    => $value_approved->title,
                        'gymsal'                    => $value_approved->gymsal,
                        'styrkerom'                    => $value_approved->styrkerom,
                        'published_at'                    => $value_approved->published_at,
                        'created_by'                    => $value_approved->created_by,
                        'updated_by'                    => $value_approved->updated_by,
                        'created_at'                    => $value_approved->created_at,
                        'updated_at'                    => $value_approved->updated_at,
                        'fromweek'                    => $value_approved->fromweek,
                        'toweek'                    => $value_approved->toweek,
                        'bookings_author'                    => $value_approved->bookings_author,
                        'owner_id'                    => $value_approved->owner_id,
                        'listing_id'                    => $value_approved->listing_id,
                        'comment'                    => $value_approved->comment,
                        'order_id'                    => $value_approved->order_id,
                        'status'                    => "paid",
                        'fixed'                    => "2",
                        'type'                    => $value_approved->type,
                        'price'                    => $value_approved->price,
                        'recurrenceRule'                    => $value_approved->recurrenceRule,
                        'recurrenceID'                    => $value_approved->recurrenceID,
                        'recurrenceException'                    => $value_approved->recurrenceException,
                        'fields_data'                    => $value_approved->fields_data,
                        ), array('id'=>$id)
                    );
                }else{
                    $wpdb->insert($bookings_calendar_raw_approved_db, array(
                        'id'                    => $value_approved->id,
                        'team_id'                    => $value_approved->team_id,
                        'repert'                    => $value_approved->repert,
                        'application_id'                    => $value_approved->application_id,
                        'date_start'                    => $value_approved->date_start,
                        'date_end'                    => $value_approved->date_end,
                        'first_event_id'                    => $value_approved->first_event_id,
                        'rejected'                    => 0,
                        'modified'                    => 2,
                        'description'                    => $value_approved->description,
                        'title'                    => $value_approved->title,
                        'fixed'                    => $value_approved->fixed,
                        'gymsal'                    => $value_approved->gymsal,
                        'styrkerom'                    => $value_approved->styrkerom,
                        'published_at'                    => $value_approved->published_at,
                        'created_by'                    => $value_approved->created_by,
                        'updated_by'                    => $value_approved->updated_by,
                        'created_at'                    => $value_approved->created_at,
                        'updated_at'                    => $value_approved->updated_at,
                        'fromweek'                    => $value_approved->fromweek,
                        'toweek'                    => $value_approved->toweek,
                        'bookings_author'                    => $value_approved->bookings_author,
                        'owner_id'                    => $value_approved->owner_id,
                        'listing_id'                    => $value_approved->listing_id,
                        'comment'                    => $value_approved->comment,
                        'order_id'                    => $value_approved->order_id,
                        'status'                    => "paid",
                        'fixed'                    => "2",
                        'type'                    => $value_approved->type,
                        'price'                    => $value_approved->price,
                        'recurrenceRule'                    => $value_approved->recurrenceRule,
                        'recurrenceID'                    => $value_approved->recurrenceID,
                        'recurrenceException'                    => $value_approved->recurrenceException,
                        'fields_data'                    => $value_approved->fields_data,
                        )
                    );
                }    

                //$wpdb->delete($bookings_calendar_raw_algorithm_db,array("id"=>$id));
                 
            }
            die;
         
    }
    public function move_algo_booking_mobiscroll(){

            global $wpdb;
            $selected_season = $_POST["selected_season"];

            $applications_sql = "SELECT id from applications where season_id = '$selected_season'";
            $applications_data = $wpdb->get_results($applications_sql);
            $app_ids = array();
            foreach ($applications_data as  $applications_d) {
               $app_ids[] = $applications_d->id;
            }
            $app_ids = implode(",", $app_ids);


            $bookings_calendar_raw_algorithm_db = $wpdb->prefix . 'bookings_calendar_raw_algorithm';  // table name
            $bookings_calendar_raw_algorithm_sql = "SELECT * from `$bookings_calendar_raw_algorithm_db` where application_id in ($app_ids)";
            $bookings_calendar_raw_algorithm_data = $wpdb->get_results($bookings_calendar_raw_algorithm_sql);

            //echo "<pre>"; print_r($bookings_calendar_raw_algorithm_data); die;

            foreach ($bookings_calendar_raw_algorithm_data as $key => $value_approved) {

                $id = $value_approved->id;

                $bookings_calendar_raw_approved_db = $wpdb->prefix . 'bookings_calendar_raw_approved';  // table name
                $bookings_calendar_raw_approved_sql = "SELECT * from `$bookings_calendar_raw_approved_db` where id=$id";
                $bookings_calendar_raw_approved_data = $wpdb->get_results($bookings_calendar_raw_approved_sql);
                //die;

                $wpdb->update("applications", array(
                    'score'                    => null,
                    ), array('id'=>$value_approved->application_id)
                );

                if(count($bookings_calendar_raw_approved_data) > 0){
                   $wpdb->update($bookings_calendar_raw_approved_db, array(
                        'team_id'                    => $value_approved->team_id,
                            'repert'                    => $value_approved->repert,
                            'application_id'                    => $value_approved->application_id,
                            'date_start'                    => $value_approved->date_start,
                            'date_end'                    => $value_approved->date_end,
                            'first_event_id'                    => $value_approved->first_event_id,
                            'rejected'                    => $value_approved->rejected,
                            'modified'                    => $value_approved->modified,
                            'description'                    => $value_approved->description,
                            'title'                    => $value_approved->title,
                            'gymsal'                    => $value_approved->gymsal,
                            'styrkerom'                    => $value_approved->styrkerom,
                            'published_at'                    => $value_approved->published_at,
                            'created_by'                    => $value_approved->created_by,
                            'updated_by'                    => $value_approved->updated_by,
                            'created_at'                    => $value_approved->created_at,
                            'updated_at'                    => $value_approved->updated_at,
                            'fromweek'                    => $value_approved->fromweek,
                            'toweek'                    => $value_approved->toweek,
                            'bookings_author'                    => $value_approved->bookings_author,
                            'owner_id'                    => $value_approved->owner_id,
                            'listing_id'                    => $value_approved->listing_id,
                            'comment'                    => $value_approved->comment,
                            'order_id'                    => $value_approved->order_id,
                            'status'                    => "paid",
                            'fixed'                    => "2",
                            'type'                    => $value_approved->type,
                            'price'                    => $value_approved->price,
                            'recurrenceRule'                    => $value_approved->recurrenceRule,
                            'recurrenceID'                    => $value_approved->recurrenceID,
                            'recurrenceException'                    => $value_approved->recurrenceException,
                            'fields_data'                    => $value_approved->fields_data,
                        ), array('id'=>$id)
                    );
                }else{
                    $wpdb->insert($bookings_calendar_raw_approved_db, array(
                            'id'                    => $value_approved->id,
                            'team_id'                    => $value_approved->team_id,
                            'repert'                    => $value_approved->repert,
                            'application_id'                    => $value_approved->application_id,
                            'date_start'                    => $value_approved->date_start,
                            'date_end'                    => $value_approved->date_end,
                            'first_event_id'                    => $value_approved->first_event_id,
                            'rejected'                    => $value_approved->rejected,
                            'description'                    => $value_approved->description,
                            'title'                    => $value_approved->title,
                            'fixed'                    => $value_approved->fixed,
                            'gymsal'                    => $value_approved->gymsal,
                            'styrkerom'                    => $value_approved->styrkerom,
                            'published_at'                    => $value_approved->published_at,
                            'created_by'                    => $value_approved->created_by,
                            'updated_by'                    => $value_approved->updated_by,
                            'created_at'                    => $value_approved->created_at,
                            'updated_at'                    => $value_approved->updated_at,
                            'modified'                    => $value_approved->modified,
                            'fromweek'                    => $value_approved->fromweek,
                            'toweek'                    => $value_approved->toweek,
                            'bookings_author'                    => $value_approved->bookings_author,
                            'owner_id'                    => $value_approved->owner_id,
                            'listing_id'                    => $value_approved->listing_id,
                            'comment'                    => $value_approved->comment,
                            'order_id'                    => $value_approved->order_id,
                            'status'                    => "paid",
                            'fixed'                    => "2",
                            'type'                    => $value_approved->type,
                            'price'                    => $value_approved->price,
                            'recurrenceRule'                    => $value_approved->recurrenceRule,
                            'recurrenceID'                    => $value_approved->recurrenceID,
                            'recurrenceException'                    => $value_approved->recurrenceException,
                            'fields_data'                    => $value_approved->fields_data,
                        )
                    );
                }    

                //$wpdb->delete($bookings_calendar_raw_algorithm_db,array("id"=>$id));
                 
            }
            die;
         
    }
    public function save_season_view_mobiscroll(){

       update_user_meta(get_current_user_ID(),"season_view",$_POST["season_view"]);
       die;
         
    }
    public function save_selected_season_mobiscroll(){

       update_user_meta(get_current_user_ID(),"selected_season",$_POST["season_id"]);
       die;
         
    }
    public function save_season_listing_filter_template_mobiscroll(){

        global $wpdb;
        $filter_template_table = "filter_template";

        $wpdb->update($filter_template_table, array(
            'json_data'            => json_encode($_POST),
        ),array("id"=>$_POST["template_selected"]));

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        update_user_meta($admin_user_id,"season_template_selected",$_POST['template_selected']);
        
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
    public function season_change_template(){

        global $wpdb;

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        update_user_meta($admin_user_id,"season_template_selected",$_POST['template_selected']);

        $template_data = Gibbs_Common_Calendar::get_selected_template_data($_POST['template_selected']);
        $template_data["template_selected"] = $_POST['template_selected'];

        wp_send_json(array( 'error' => 0,'message' => "Lagret!", "template_data" => $template_data));
        die;
         
    }

    public function get_season_custom_fields_for_calender_mobiscroll(){

        $booking_id = $_POST["booking_id"];
        $listing_id = $_POST["listing_id"];
        global $wpdb;
        $season_view_exist = true;
        if($_POST["season_view"] == "algoritme"){
            $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_algorithm';
        }elseif($_POST["season_view"]  == "manuelle"){
            $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_approved';
        }else{
            $bookings_table = $wpdb->prefix . 'bookings_calendar_raw';
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

            if($_POST["season_view"] == "algoritme"){
                $bookings_table = $wpdb->prefix . 'bookings_calendar_raw_algorithm';
            }elseif($_POST["season_view"]  == "manuelle"){
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
        $wpm_user_list  = Gibbs_Admin_Calendar_Utility::get_user_list("");

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
        $exists = email_exists($_POST["email"]);

        $data = array("success" => true, "exist" => false);

        if($exists){
            $data = array("success" => true, "exist" => true);
        }
        wp_send_json(
            $data
        );
    
        die();
    }
    public function addEventCustomer()
    {
        global $wpdb;

        $return = array("success" => false, "message" => "");

        

        $password = wp_generate_password( 12, false );

        $first_name = (isset($_POST['first_name'])) ? sanitize_text_field( $_POST['first_name'] ) : '' ;
        $last_name = (isset($_POST['last_name'])) ? sanitize_text_field( $_POST['last_name'] ) : '' ;
        $email = $_POST['email'];
        $email_arr = explode('@', $email);
        $user_login = sanitize_user(trim($email_arr[0]), true);

        $data = array("success" => true, "exist" => false);



        $role =  "customer";

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

        if($user_id && $user_id > 0){

            $admin_user_id = Gibbs_Admin_Calendar_Utility::get_current_admin_user();

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
            if ( isset( $_POST['name'] ) ){
                update_user_meta($user_id, 'billing_first_name', $_POST['name'] );
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

                update_user_meta($user_id, 'profile_type', $_POST['profile_type']  );
            }
            if ( isset( $_POST['organization_number'] ) ){

                update_user_meta($user_id, 'company_number', $_POST['organization_number']);
            }

            $return["success"] = true;
            $return["message"] = "Regustrert!";
            
        }else{
            $return["success"] = false;
            $return["message"] = "Bruker ikke opprettet, bytt epost";
        }

        

        if($exists){
            $data = array("success" => true, "exist" => true);
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

    public function get_selected_template_data($template_selected = ""){

        global $wpdb;



        $data = array();

        $data["cal_start_day"] = "";
        $data["cal_end_day"] = "";
        $data["cal_starttime"] = "";
        $data["cal_endtime"] = "";
        $data["cal_time_cell_step"] = "";
        $data["cal_time_label_step"] = "";
        $data["cal_show_week_nos"] = "";
        $data["filter_location"] = null;
        $data["calendar_view"] = "";



        if($template_selected && $template_selected != ""){
            $filter_template_table = "filter_template";

            $selected_filter_template_sql = "SELECT * from $filter_template_table where id = $template_selected";
            $selected_filter_template_data = $wpdb->get_row($selected_filter_template_sql);

            if(isset($selected_filter_template_data->id)){

                if($selected_filter_template_data->json_data != "" && $selected_filter_template_data->json_data != null){
                    $selected_temp_data = json_decode($selected_filter_template_data->json_data);


                    $data["cal_start_day"] = $selected_temp_data->cal_start_day;
                    $data["cal_end_day"] = $selected_temp_data->cal_end_day;
                    $data["cal_starttime"] = $selected_temp_data->cal_starttime;
                    $data["cal_endtime"] = $selected_temp_data->cal_endtime;
                    $data["cal_time_cell_step"] = $selected_temp_data->cal_time_cell_step;
                    $data["cal_time_label_step"] = $selected_temp_data->cal_time_label_step;
                    $data["cal_show_week_nos"] = $selected_temp_data->cal_show_week_nos;
                    $data["filter_location"] = $selected_temp_data->filter_location;
                    $data["calendar_view"] = $selected_temp_data->calendar_view;

                }

            }

        }

       // echo "<pre>"; print_r($data); die;

        return $data;

    }


    public static function get_season_booking_data($fetch_from = "")
    {
        global $wpdb;

        $tv = false;

        if(isset($_POST["calender_type"]) && $_POST["calender_type"] == "season_tv"){
           $tv = true;
        }

        $post_data = array(
            "cal_type"  => $_POST["cal_type"],
            "season_view"  => $_POST["season_view"],
            "listings" => $_POST['listings'],
        );

        if($tv == true){
            $season_view  = $_POST["cal_type"];
        }else{
            $season_view  = Gibbs_Season_Calendar_Setup::get_season_view();
        }

        

        $get_ajax_data  = 0;

        $not_rejected_showing = "0";

        $ajax_data = array();

        if (is_array($post_data)) {
            $get_ajax_data = 1;
        }

        if ($season_view == "algoritme") {
            $booking_table = $wpdb->prefix . 'bookings_calendar_raw_algorithm';
        } elseif ($season_view == "manuelle") {
            $booking_table = $wpdb->prefix . 'bookings_calendar_raw_approved';
        } else {
            $booking_table = $wpdb->prefix . 'bookings_calendar_raw';
        }

        


        $ajax_data["season_view"] = $season_view;

        $users_table = $wpdb->prefix . 'users';
        $users_and_users_groups_table = $wpdb->prefix . 'ptn_users_and_users_groups';
        $team_table  = 'team';
        $club_table  = 'club';
        $gym_table   = 'gym';
        $sport_table   = 'sport';
        $author_id = get_current_user_id();
        $current_language = Gibbs_Common_Calendar::get_language();

        $ajax_data["current_language"] = $current_language;
        $ajax_data["cal_type"] = $cal_type;

        $filter_location = get_user_meta(get_current_user_ID(), "filter_location", true);

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        $template_selected =  get_user_meta($admin_user_id,"season_template_selected",true);

        $template_data = Gibbs_Common_Calendar::get_selected_template_data($template_selected);

        $additional_info = array();

        if(isset($_POST["additional_info"]) && !empty($_POST["additional_info"])){
            $additional_info = $_POST["additional_info"];
        }
        $showRejected = true;
        if(isset($_POST["show_rejected"]) && $_POST["show_rejected"] == "no"){
            $showRejected = false;
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

        // end settings options

        // for faster load whilve developing
        
        $seasons_data = Gibbs_Season_Calendar_Setup::get_season_data();

       // echo "<pre>"; print_r($seasons_data);

        $ajax_data["seasons_data"] = $seasons_data;
        
        if($tv == true){
            $selected_season = $_POST["selected_season"];
        }else{
            $selected_season = Gibbs_Season_Calendar_Setup::get_select_season();
        }

        

        if (!$selected_season && !empty($seasons_data)) {
            $selected_season = $seasons_data[0]->id;
        }

        $ajax_data["selected_season"] = $selected_season;
        $applications_sql = "SELECT id from applications where season_id = '$selected_season'";
        $applications_data = $wpdb->get_results($applications_sql);
        $app_ids = array();

        foreach ($applications_data as  $applications_d) {
            $app_ids[] = $applications_d->id;
        }

        $app_ids = implode(",", $app_ids);
        

        $booking_where = "";

        if (is_array($_POST["listing"]) && !empty($_POST["listing"])) {
            $booking_where = " AND listing_id IN ( " . implode(',', $_POST["listing"]) . " )";
        }

        if ($showRejected == false) {
            $booking_results = $wpdb->get_results("SELECT * FROM $booking_table where application_id in ($app_ids) AND rejected !='1' $booking_where");
        } else {
            $booking_results = $wpdb->get_results("SELECT * FROM $booking_table where application_id in ($app_ids) $booking_where");
        }

        

        if ($selected_season != "") {
            $seasons_sql1 = "SELECT * from seasons where id = $selected_season";
            $seasons_data1 = $wpdb->get_row($seasons_sql1);
            if ($seasons_data1) {
                $start_end_season = array("season_start" => $seasons_data1->season_start, "season_end" => $seasons_data1->season_end);

                $ajax_data["start_end_season"] = $start_end_season;
            } else {
                $ajax_data["start_end_season"] = array();
            }
        } else {
            $ajax_data["start_end_season"] = array();
        }
        

        $ajax_data["not_rejected_showing"] = $not_rejected_showing;

        $team_results   = $wpdb->get_results("SELECT * FROM $team_table");
        $club_results   = $wpdb->get_results("SELECT * FROM $club_table");
        $user_results   = $wpdb->get_results("SELECT * FROM $users_table");
        $filter_location = $wpdb->get_results("SELECT * FROM  $gym_table");
        $spotrs_filter  = $wpdb->get_results("SELECT * FROM  $sport_table");
        $working_hours = $wpdb->get_results("SELECT working_hours FROM gym_section");
        $filter_sports_id = $wpdb->get_results("SELECT * FROM gym_listings_sports");
        $user_groups_id = $wpdb->get_results("SELECT `users_groups_id` FROM `ptn_users_and_users_groups` WHERE users_id=$author_id;");

        $gyms_sections_check   = $wpdb->get_results("SELECT * from $gym_section_table");
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

        $ajax_data["sportsList"] = array('sport' => $sportss_filter, 'test' => $bc_schema_id);

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

        $ajax_data["gym_resources"] = Gibbs_Common_Calendar::get_gym_resources();

        $show_extra_info = get_user_meta(get_current_user_ID(), "show_extra_info", true);

        $ajax_data["show_extra_info"] = $show_extra_info;



        //Loading Resources of Booking Calendar
        $records = [];

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
                $org_data["date_start"] = date("Y-m-d H:i:s", strtotime($bookings_calendar_raw_dd->date_start));
                $org_data["date_end"] = date("Y-m-d H:i:s", strtotime($bookings_calendar_raw_dd->date_end));
                $org_data["listing_id"] = $bookings_calendar_raw_dd->listing_id;
            } else {
                $org_data = array();
                $org_data["name"] = "";
                $org_data["day"] = "";
                $org_data["time"] = "";
                $org_data["date_start"] = "";
                $org_data["date_end"] = "";
                $org_data["listing_id"] = "";
            }

            if ($record->status == "pay_to_confirm") {
                continue;
            }

             //echo "<pre>"; print_r($records); die;

            $team_title = $club_name = $user_name = '';
            $phone_number = "";
            $customer_email = "";

            foreach ($team_results as $team) {
                if ($team->club_id == $record->team_id) {
                    $team_title = $team->name;
                }
            }

            foreach ($club_results as $club) {
                if ($club->id == $record->bookings_author) {
                    $club_name = $club->company_name;
                }
            };

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
            $org_status = $record->status;
            $status_text = "";
            if ($current_language  == "nb-NO") {
                if ($record->status == "paid" || $record->status == "Paid") {
                    $status_text = "Betalt";
                } else if ($record->status == "waiting" || $record->status == "Waiting") {
                    $status_text = "Reservasjon";
                } else if ($record->status == "confirmed" || $record->status == "Confirmed") {
                    $status_text = "Godkjenn";
                } else if ($record->status == "pay_to_confirm") {
                    $status_text = "Ikke gjennomført betaling";
                } else if ($record->status == "expired" || $record->status == "Expired") {
                    $status_text = "Utløpt booking";
                } else if ($record->status == "cancelled" || $record->status == "Canceled") {
                    $status_text = "Kansellert";
                } else if ($record->status == "closed" || $record->status == "Closed") {
                    $status_text = "Stengt";
                } else if ($record->status == "sesongbooking" || $record->status == "sesongbooking") {
                    $status_text = "Sesongbooking";
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

                if ($season_view == "algoritme") {
                    if ($record->rejected == "1") {
                        $record->status = "rejected";
                    } else {
                        $record->status = "algo_done";
                    }
                } else {
                    $record->status = "done";
                }

                if ($season_view == "manuelle") {

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
            $custom_fields = false;
            if($record->fields_data != ""){

                $data_fields = maybe_unserialize($record->fields_data);

                if(is_array($data_fields)){

                }else{
                    $data_fieldsddd = preg_replace_callback('!s:\d+:"(.*?)";!s', 
                        function($m) {
                            return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
                        }, $record->fields_data
                    );
                    $data_fields = maybe_unserialize($data_fieldsddd);
                }

                if(is_array($data_fields) && !empty($data_fields)){
                    $custom_fields = true;
                }

                

            }

            write_log(array('status' => $record->status));
            $record_data = array(

                'id'                  => $record->id,
                'application_id'      => $record->application_id,
                'first_event_id'      => $record->first_event_id,
                'unlink_first_event_id'  => $record->unlink_first_event_id,
                'title'               => $record->title,
                'customer'            => $user_name,
                'customer_email'            => $customer_email,
                'phone_number'            => $phone_number,
                'listing'            => $listings_ids,
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
                'fields_data'        => $record->fields_data,
            );
            $record_data['recurrenceId']        = $record->recurrenceID;
            if ($record->recurrenceRule != '') {
                $record_data['rrule'] = str_replace('\\n', '\n', $record->recurrenceRule);
                $record_data['recurrenceRule']      = $record->recurrenceRule;
                $record_data['recurring'] = $record->recurrenceRule;
                $record_data['recurrenceException'] = json_decode($record->recurrenceException);
                $record_data['recurringException']  = json_decode($record->recurrenceException);

                $datetime1 = new DateTime($record->date_start);
                $datetime2 = new DateTime($record->date_end);
                $interval = $datetime1->diff($datetime2);
                $record_data['duration'] = $interval->format('%H') . ":" . $interval->format('%I');
            }

            $records[] = $record_data;
        }
       // echo "<pre>"; print_r($records); die;


        $ajax_data["schedular_tasks"] = $records;

        if($fetch_from == "export_booking"){
            return $records;
        }else{
            wp_send_json($ajax_data);
        }

        
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

    public static function wpm_season_add_record($data)
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

        if ($data['status'] == "closed") {
            $list_id = $data['gymSectionId'];
            $list_table = $wpdb->prefix . 'posts';
            $user_groups_id = $wpdb->get_var("select users_groups_id from $list_table where id=$list_id");
            $closed_user_id = get_current_user_ID();
        } else {
            $closed_user_id = '';
            $user_groups_id  = '';
        }

        if ($data['status'] == "closed") {
            $data['status'] = "paid";
            $fixed = "1";
        } elseif ($data['status'] == "sesongbooking") {
            $data['status'] = "paid";
            $fixed = "2";
        } else {
            $fixed = "0";
        }

        $comment = "";
        $wpm_client = "";

        if(isset($data["client"]) && isset($data["client"]["value"]) && $data["client"]["value"] != ""){
           $wpm_client = $data["client"]["value"];
           $comment_data =  self::get_comment_data($data["client"]["value"]);
           $comment = json_encode($comment_data);
        }

        $start          = $data['start'];
        $end            = $data['end'];
        $description    = $data['description'];
        $status         = $data['status'];
        $title          = $data['title'];
        $recurrenceRule = $data['recurrenceRule'];
        $recurrenceId   = $data['recurrenceId'];
        $gymSectionId   = $data['gymSectionId'];
        $fields_data   = $data['fields_data'];

        $owner_id = $wpdb->get_var("select post_author from ptn_posts where id=$gymSectionId");



        $insertArr = array(
                    'bookings_author'       => $wpm_client,
                    'date_start'            => date("Y/m/d H:i", strtotime($start)),
                    'date_end'              => date("Y/m/d H:i", strtotime($end)),
                    'description'           => $description,
                    'status'                => $status,
                    'title'                 => $title,
                    'type'                  => 'reservation',
                    'fixed'                 => $fixed,
                    'closed_user_id'        => $closed_user_id,
                    'closed_user_group_id'  => $user_groups_id,
                    'recurrenceRule'        => $recurrenceRule,
                    'recurrenceID'          => $recurrenceId,
                    'listing_id'            => $gymSectionId,
                    'owner_id'              => $owner_id,
                    'comment'               => $comment,
                    'fields_data'           => $fields_data,
                );

        if(isset($data["application_id"])){
            $insertArr["application_id"] = $data["application_id"];
        }
        
        $insert = $wpdb->insert($booking_table, $insertArr);


        if(!empty($data)){
           return true;
        }else{
            wp_send_json(array('status' => 200, 'client' => $wpm_client, 'post' => $_POST));
        }

        
    }

    public static function get_comment_data($user_id){

        $userData = get_userdata($user_id);

        

        $comment= array(
                        'first_name'    => $userData->first_name,
                        'last_name'     => $userData->last_name,
                        'email'         => $userData->user_email,
                        'phone'         => get_user_meta($user_id,"phone",true),
                        'message'       => "",
                        'billing_address_1' => get_user_meta($user_id,"billing_address_1",true),
                        'billing_postcode'  => get_user_meta($user_id,"billing_postcode",true),
                        'billing_city'      => get_user_meta($user_id,"billing_city",true),
                        'billing_country'   => get_user_meta($user_id,"billing_country",true)
                    );
        return $comment;

    }

    public static function wpm_season_update_record()
    {


        global $wpdb;

        $booking_table = $wpdb->prefix . 'bookings_calendar_raw_approved';



        $booking_id             = $_POST['id'];



        $id = $wpm_client = $team = $start = $end = $description = $repert = $recurrenceRule = $recurrenceException = $recurrenceId = $gymSectionId = "";
        $id             = $_POST['id'];
        $start          = $_POST['start'];
        $end            = $_POST['end'];
        $description    = $_POST['description'];
        $gymSectionId   = $_POST['gymSectionId'];


        $booking_data = $wpdb->get_row('SELECT * FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($id), 'ARRAY_A');

     
        $wpdb->show_errors = true;

        if (isset($_POST["status"]) && $_POST["status"] != "") {
            $rejected = $_POST["status"];
        } else {
            $rejected = "0";
        }


        if($_POST["status"] == "0" || $_POST["status"] == "1"){
        }else{
            $rejected = $booking_data["rejected"];
        }

        $wpdb->update(
            $booking_table,
            array(
                'date_start'            => date("Y/m/d H:i", strtotime($start)),
                'date_end'              => date("Y/m/d H:i", strtotime($end)),
                'description'           => $description,
                'listing_id'            => $gymSectionId,
                'modified'              => "1",
                'rejected'              => $rejected
            ),
            array('id' => $id)
        );

            

        wp_send_json(array('status' => 200));
    }

    public static function wpm_delete_record()
    {
        global $wpdb;

        $id = $_POST['id'];
        $record_table = $wpdb->prefix . 'bookings_calendar';
        $wpdb->delete($record_table, array('id' => $id));

        wp_send_json(array('status' => 200));
    }
}
