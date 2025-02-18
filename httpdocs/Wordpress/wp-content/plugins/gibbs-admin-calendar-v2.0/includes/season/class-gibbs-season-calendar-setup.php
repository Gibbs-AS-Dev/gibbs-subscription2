<?php

class Gibbs_Season_Calendar_Setup
{

    public static function action_init()
    {
        add_action('wp_enqueue_scripts', array("Gibbs_Season_Calendar_Setup", 'enqueue_scripts'));
       

        add_shortcode('schedule-calendar-season-v2', array("Gibbs_Season_Calendar_Setup", 'render_season_calendar'));
    }

    public static function enqueue_scripts()
    {
       
    }
    public static function get_season_view()
    {
       $season_view = get_user_meta(get_current_user_ID(), "season_view", true);

       if($season_view == "" || $season_view == null){
          $season_view = "forespurte";
       }
       return $season_view;
    }
    public static function get_select_season()
    {

       $selected_season =  get_user_meta(get_current_user_ID(), "selected_season", true);

       $seasons_data = Gibbs_Season_Calendar_Setup::get_season_data();

       if($selected_season == ""){
            if(!empty($seasons_data)){
                $selected_season = $seasons_data[0]->id;
            }
        }

        return $selected_season;
    }
    public static function render_season_tv()
    {
        global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }
        

        global $wpdb;
        wp_enqueue_script('custom-script', GIBBS_CALENDAR_URL . 'assets/js/wpm-season-tv.js', array(), $version, true);

    
        if (function_exists("run_jwt_auth")) {
            wp_localize_script('custom-script', 'script_vars', array(
                'current_user_jwt'  => Jwt_Auth_Public::generate_token_loggedin_user()
            ));

        }

        $language = Gibbs_Common_Calendar::get_language();

        $resources = Gibbs_Common_Calendar::get_gym_resources();

        if(isset($resources["listings"][0]["owner_id"])){
            $owner_id = $resources["listings"][0]["owner_id"];
        }else{
            exit;
        }

        $listings = explode(',', esc_sql($_GET['listings']));

        $refresh = isset($_GET['refresh']) && is_numeric($_GET['refresh']) && $_GET['refresh'] >= 15 ? $_GET['refresh'] : 15;
        $show_header = (isset($_GET['header']) && $_GET['header'] == "true") ? 1 : 0;

        $additional_info = "";

        if(isset($_GET['additional-info-tv']) && $_GET['additional-info-tv'] != ""){
            $additional_info = explode(",",$_GET['additional-info-tv']);
        }
        $show_fields_info = "";

        if(isset($_GET['fields-info-tv']) && $_GET['fields-info-tv'] != ""){
            $show_fields_info = explode(",",$_GET['fields-info-tv']);
        }

        $admin_icon_show_tv = "";

        if(isset($_GET['admin_icon_show_tv']) && $_GET['admin_icon_show_tv'] != ""){
            $admin_icon_show_tv = explode(",",$_GET['admin_icon_show_tv']);
        }

        

        wp_set_current_user($owner_id);

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        $selected_season = $_GET["season-type-tv"];

        $type_of_form = Gibbs_Season_Calendar_Setup::check_form_data_tv($selected_season);
        
        $season_view = $_GET["cal-type-tv"];


        $season_start = "";
        $season_end = "";

        
        if($selected_season != ""){

            $seasons_sql_another = "SELECT * from seasons where id = $selected_season";

            $seasons_data_another = $wpdb->get_row($seasons_sql_another);

            if(isset($seasons_data_another->season_start) && isset($seasons_data_another->season_end)){
                $season_start = $seasons_data_another->season_start;
                $season_end = $seasons_data_another->season_end;
            }

        }

        $template_data = Gibbs_Common_Calendar::get_selected_template_data();

       
        $update_season_template_auto = "no";
        $template_data["cal_start_day"] = "1";
        $template_data["cal_end_day"] = "0";
        $template_data["cal_show_week_nos"] = "true";
        $template_data["cal_show_daily_summery_weak"] = "false";

       

        $js_variables = array(
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'plugin_url'    => GIBBS_CALENDAR_URL,
            'translations'  => Gibbs_Common_Calendar::get_translations($language),
            'template_selected'  => $template_selected,
            'cal_start_day' => ($template_data["cal_start_day"] != "")?$template_data["cal_start_day"]:'1',
            'cal_end_day'   => ($template_data["cal_end_day"] != "")?$template_data["cal_end_day"]:'0',
            'cal_starttime' => isset($_GET['start_hour']) ? $_GET['start_hour'] : '09:00',
            'cal_endtime' => isset($_GET['end_hour']) ? $_GET['end_hour'] : '17:00',
            'cal_time_cell_step'    => $template_data["cal_time_cell_step"],
            'cal_time_label_step'   => $template_data["cal_time_label_step"],
            'cal_show_week_nos'   => $template_data["cal_show_week_nos"],
            'additional_info'   => $additional_info,
            'show_admin_icons'   => $admin_icon_show_tv,
            'show_rejected'   => $template_data["show_rejected"],
            'show_full_day' => get_option("show_full_day"),
            'calendar_view'     => $_GET['view'] ? $_GET['view'] : 'timeline_week',
            'gym_resources'     => $resources,
            'current_language'  => $language,
            'selected_season'  => $selected_season,
            'type_of_form'  => $type_of_form,
            'cal_type'  => $_GET["cal-type-tv"],
            'listings'      => $listings,
            'season_view'      => $season_view,
            'season_start'  => $season_start,
            'season_end'  => $season_end,
            'update_season_template_auto'  => $update_season_template_auto,
            'refresh'   => $refresh,
            'header' => $show_header,
        );

        //echo "<pre>"; print_r($js_variables); die;

        wp_localize_script('custom-script', 'WPMCalendarV2Obj', $js_variables);

        if(isset($resources["listings"])){
            $listings = $resources["listings"];
        }else{
            $listings = array();
        }



        $cal_type       = "view_only"; // 'view_only';
        $season_view       = get_user_meta(get_current_user_ID(), "season_view", true);
        $translations   = Gibbs_Common_Calendar::get_translations(Gibbs_Common_Calendar::get_language());
        $wpm_user_list  = Gibbs_Common_Calendar::get_user_list($cal_type);

        $width = $_GET['width'] ? $_GET['width'] . '' : '100%';
        $height = $_GET['height'] ? $_GET['height'] . '' : '100%';

        if ($show_header == 1){}else {
            ?>
            <style>
                .mbsc-calendar-header {
                    display: none;
                }
            </style>

        <?php
        }


        ?>
        <style>
            header {
                display: none;
            }
        </style>
        <div mbsc-page class="wpm-recurring-event-add-edit-dialog season-calender-tv">
            <div style="height:<?= $height; ?>; width:<?= $width; ?>;margin:0 auto;">
                <div id="loader" class="loader-box-services" style="display:none"></div>
                <div id="toast-container" class="bottom-right">
                    <?php require_once GIBBS_CALENDAR_PATH . 'components/calendar-notification.php'; ?>
                </div>
                <div class="filter_overlay"></div>
                <div id="scheduler"></div>
                <?php require_once GIBBS_CALENDAR_PATH . 'components/season/calendar-footer.php'; ?>

                <div style="display: none;">
                    <?php
                   // require_once GIBBS_CALENDAR_PATH . 'modals/tv-view-season.php';
                   // require_once GIBBS_CALENDAR_PATH . 'modals/season-add-edit-event.php';
                  //  require_once GIBBS_CALENDAR_PATH . 'modals/add-customer.php';
                   // require_once GIBBS_CALENDAR_PATH . 'modals/edit-event.php';
                   // require_once GIBBS_CALENDAR_PATH . 'modals/edit-recurrence.php';
                   // require_once GIBBS_CALENDAR_PATH . 'modals/season-calendar-event-tooltip-popup.php';
                  //  require_once GIBBS_CALENDAR_PATH . 'modals/toast-tamplate.php';
                  //  require_once GIBBS_CALENDAR_PATH . 'modals/algo-popup.php';
                    require_once GIBBS_CALENDAR_PATH . 'components/calendar-header.php';
                 //   require_once GIBBS_CALENDAR_PATH . 'components/season/calendar-template-season.php';
                    require_once GIBBS_CALENDAR_PATH . 'components/season/calendar-settings-season.php';
                  //  require_once GIBBS_CALENDAR_PATH . 'components/calendar-resource-tooltip.php';
                    ?>
                </div>
                <style>
                    .season-calender-tv .mbsc-timeline-resource, .season-calender-tv .mbsc-timeline-row {
                        min-height: 150px !important;
                        height: max-content !important;
                    }
                </style>
            </div>
        </div>
    <?php
      
    }

    public static function render_season_calendar()
    {
        global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }
        

        global $wpdb;
        wp_enqueue_script('custom-script', GIBBS_CALENDAR_URL . 'assets/js/wpm-season.js', array(), $version, true);

    
        if (function_exists("run_jwt_auth")) {
            wp_localize_script('custom-script', 'script_vars', array(
                'current_user_jwt'  => Jwt_Auth_Public::generate_token_loggedin_user()
            ));

        }

        $language = Gibbs_Common_Calendar::get_language();

        $resources = Gibbs_Common_Calendar::get_gym_resources();

       

        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        require_once GIBBS_CALENDAR_PATH . 'components/season/template-filter.php';

        $template_selected =  get_user_meta($admin_user_id,"season_template_selected",true);

        $template_data = Gibbs_Common_Calendar::get_selected_template_data($template_selected);

        $seasons_data = Gibbs_Season_Calendar_Setup::get_season_data();

        $selected_season = Gibbs_Season_Calendar_Setup::get_select_season();

        $type_of_form = Gibbs_Season_Calendar_Setup::check_form_data();
        
        $season_view = Gibbs_Season_Calendar_Setup::get_season_view();


        $season_start = "";
        $season_end = "";

        
        if($selected_season != ""){

            $seasons_sql_another = "SELECT * from seasons where id = $selected_season";

            $seasons_data_another = $wpdb->get_row($seasons_sql_another);

            if(isset($seasons_data_another->season_start) && isset($seasons_data_another->season_end)){
                $season_start = $seasons_data_another->season_start;
                $season_end = $seasons_data_another->season_end;
            }

        }

        $update_season_template_auto =  get_user_meta($admin_user_id,"update_season_template_auto",true);

       
        $update_season_template_auto = "no";
        $template_data["cal_start_day"] = "1";
        $template_data["cal_end_day"] = "0";
        $template_data["cal_show_week_nos"] = "true";
        $template_data["cal_show_daily_summery_weak"] = "false";

       

        $js_variables = array(
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'plugin_url'    => GIBBS_CALENDAR_URL,
            'translations'  => Gibbs_Common_Calendar::get_translations($language),
            'template_selected'  => $template_selected,
            'cal_start_day' => ($template_data["cal_start_day"] != "")?$template_data["cal_start_day"]:'1',
            'cal_end_day'   => ($template_data["cal_end_day"] != "")?$template_data["cal_end_day"]:'0',
            'cal_starttime' => $template_data["cal_starttime"],
            'cal_endtime'   => $template_data["cal_endtime"],
            'cal_time_cell_step'    => $template_data["cal_time_cell_step"],
            'cal_time_label_step'   => $template_data["cal_time_label_step"],
            'cal_show_week_nos'   => $template_data["cal_show_week_nos"],
            'additional_info'   => $template_data["additional_info"],
            'show_admin_icons'   => $template_data["show_admin_icons"],
            'show_rejected'   => $template_data["show_rejected"],
            'show_full_day' => get_option("show_full_day"),
            'cell_width'    => get_user_meta(get_current_user_ID(), "cell_width", true),
            'filter_location'   => $template_data["filter_location"],
            'filter_group'      => get_user_meta(get_current_user_ID(), "filter_group"),
            'filter_search'     => get_user_meta(get_current_user_ID(), "filter_search"),
            'calendar_view'     => $template_data["calendar_view"],
            'gym_resources'     => $resources,
            'current_language'  => $language,
            'seasons_data'  => $seasons_data,
            'selected_season'  => $selected_season,
            'type_of_form'  => $type_of_form,
            'season_view'      => $season_view,
            'season_start'  => $season_start,
            'season_end'  => $season_end,
            'update_season_template_auto'  => $update_season_template_auto,
        );

        //echo "<pre>"; print_r($js_variables); die;

        wp_localize_script('custom-script', 'WPMCalendarV2Obj', $js_variables);

        if(isset($resources["listings"])){
            $listings = $resources["listings"];
        }else{
            $listings = array();
        }



        $cal_type       = "view_only"; // 'view_only';
        $season_view       = get_user_meta(get_current_user_ID(), "season_view", true);
        $translations   = Gibbs_Common_Calendar::get_translations(Gibbs_Common_Calendar::get_language());
        $wpm_user_list  = Gibbs_Common_Calendar::get_user_list($cal_type);
        
        require_once GIBBS_CALENDAR_PATH . 'calendar_type/seasonalbooking-calendar.php';
      
    }

    public function saveCalenderFilter($user_id){

        global $wpdb;

        $filter_template_table = "filter_template";

        $jsonData = array(
                        "cal_start_day" => null,
                        "cal_end_day" => null,
                        "cal_starttime" => "06:00",
                        "cal_endtime" => "23:00",
                        "cal_time_cell_step" => "60",
                        "cal_time_label_step" => "60",
                        "calendarWeekNumbers" => "true",
                        "filter_location" => null,
                        "calendar_view" => "timeline_day",
                    );
        $jsonData = json_encode($jsonData);
        $wpdb->insert($filter_template_table, array(
            'name'            => "Standard visning",
            'user_id'            => $user_id,
            'template_type'            => "calender-season",
            'json_data'            => $jsonData,
            ));
        $lastid = $wpdb->insert_id;


        update_user_meta($user_id,"season_template_selected",$lastid);

    }

    public function sum_desired_hours($app_id){
            global $wpdb;
                $bookings_calendar_raw = $wpdb->prefix . 'bookings_calendar_raw';
                $sql = "select id,date_start,date_end from $bookings_calendar_raw WHERE application_id=".$app_id;

                $bk_data = $wpdb->get_results($sql);

                

                    $sum_desired_hours = "";
                    foreach ($bk_data as $key => $bk_da) {
                        if($bk_da->date_start != ""){
                            $date_start = $bk_da->date_start; 
                            $date_end = $bk_da->date_end; 
                            /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                            $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                            $datetime1 = new DateTime($date_start);
                            $datetime2 = new DateTime($date_end);

                            $interval = $datetime1->diff($datetime2);
                            if($interval->format('%h') < 10){
                                $hour = "0".$interval->format('%h');
                            }else{
                                $hour = (int) $interval->format('%h');
                            }
                            if($interval->format('%i') < 10){
                                $minute = "0".$interval->format('%i');
                            }else{
                                $minute = (int) $interval->format('%i');
                            }
                            $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                            if($sum_desired_hours != ""){
                              $time_c = explode(":", $sum_desired_hours);  

                              $sum_desired_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                           }else{

                              $sum_desired_hours = date("H:i",strtotime($dattee)); 
                           }
                        }else{
                             $sum_desired_hours = "00:00";   
                        }   


                    }

                    if($sum_desired_hours == "" || $sum_desired_hours == "00:00"){
                        $sum_desired_hours = 0;
                    }else{
                       // echo $sum_desired_hours; die;
                        $detec = explode(":", $sum_desired_hours);

                        $dddd = array("01","02","03","04","05","06","07","08","09");
                        if(in_array($detec[0], $dddd)){
                            $detec[0] = str_replace("0", "", $detec[0]);
                        }

                        $org_d = $detec[0].",".$detec[1]/60; 
                        $sum_desired_hours = str_replace("0.","",$org_d);
                        $sum_desired_hours = str_replace(",0","",$sum_desired_hours);
                    }
                return $sum_desired_hours;    
    }

    public function sum_received_hours($app_id){
                    global $wpdb;

                    $bookings_calendar_raw_approved_table =$wpdb->prefix .'bookings_calendar_raw_approved';

                   $sql2 = "select id,date_start,date_end from $bookings_calendar_raw_approved_table WHERE `rejected` != 1 AND application_id=".$app_id;    


                    $bk_data2 = $wpdb->get_results($sql2);

                    


                    $sum_received_hours = "";


                    foreach ($bk_data2 as $key => $bk_da2) {

                        if($bk_da2->date_start != ""){
                        
                            $date_start = $bk_da2->date_start;
                            $date_end = $bk_da2->date_end; 
                            /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                            $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                            $datetime1 = new DateTime($date_start);
                            $datetime2 = new DateTime($date_end);
                            $interval = $datetime1->diff($datetime2);
                            if($interval->format('%h') < 10){
                                $hour = "0".$interval->format('%h');
                            }else{
                                $hour = (int) $interval->format('%h');
                            }
                            if($interval->format('%i') < 10){
                                $minute = "0".$interval->format('%i');
                            }else{
                                $minute = (int) $interval->format('%i');
                            }
                            $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                            if($sum_received_hours != ""){
                              $time_c = explode(":", $sum_received_hours);  

                              $sum_received_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                            }else{

                              $sum_received_hours = date("H:i",strtotime($dattee)); 
                            }
                        }else{
                             $sum_received_hours = "00:00";   
                        }    
                    }
                    if($sum_received_hours == "" || $sum_received_hours == "00:00"){
                        $sum_received_hours = 0;
                    }else{
                        $detec = explode(":", $sum_received_hours);
                        $dddd = array("01","02","03","04","05","06","07","08","09");
                        if(in_array($detec[0], $dddd)){
                            $detec[0] = str_replace("0", "", $detec[0]);
                        }

                        $org_d = $detec[0].",".$detec[1]/60; 
                        $sum_received_hours = str_replace("0.","",$org_d);
                        $sum_received_hours = str_replace(",0","",$sum_received_hours);

                    }

                return $sum_received_hours;    
    }
    public function sum_algo_hours($app_id){
                    global $wpdb;

                    $bookings_calendar_raw_algorithm_table =$wpdb->prefix .'bookings_calendar_raw_algorithm';

                   $sql3 = "select id,date_start,date_end from $bookings_calendar_raw_algorithm_table WHERE `rejected` != 1 AND application_id=".$app_id;

                    $bk_data3 = $wpdb->get_results($sql3);


                    $sum_algo_hours = "";


                    foreach ($bk_data3 as $key => $bk_da3) {

                        if($bk_da3->date_start != ""){
                        
                            $date_start = $bk_da3->date_start;
                            $date_end = $bk_da3->date_end; 
                            /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                            $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                            $datetime1 = new DateTime($date_start);
                            $datetime2 = new DateTime($date_end);
                            $interval = $datetime1->diff($datetime2);
                            if($interval->format('%h') < 10){
                                $hour = "0".$interval->format('%h');
                            }else{
                                $hour = (int) $interval->format('%h');
                            }
                            if($interval->format('%i') < 10){
                                $minute = "0".$interval->format('%i');
                            }else{
                                $minute = (int) $interval->format('%i');
                            }
                            $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                            if($sum_algo_hours != ""){
                              $time_c = explode(":", $sum_algo_hours);  

                              $sum_algo_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                           }else{

                              $sum_algo_hours = date("H:i",strtotime($dattee)); 
                           }
                        }else{
                             $sum_algo_hours = "00:00";   
                        }    
                    }
                    if($sum_algo_hours == "" || $sum_algo_hours == "00:00"){
                        $sum_algo_hours = 0;
                    }else{
                        $detec = explode(":", $sum_algo_hours);
                        $dddd = array("01","02","03","04","05","06","07","08","09");
                        if(in_array($detec[0], $dddd)){
                            $detec[0] = str_replace("0", "", $detec[0]);
                        }

                        $org_d = $detec[0].",".$detec[1]/60; 
                        $sum_algo_hours = str_replace("0.","",$org_d);
                        $sum_algo_hours = str_replace(",0","",$sum_algo_hours);
                    }

                return $sum_algo_hours;    
    }

    public function score($app_id){
                    global $wpdb;

                    $applications_table = 'applications';

                    $sql3 = "select * from $applications_table WHERE id=".$app_id;

                    $bk_data3 = $wpdb->get_row($sql3);

                    if(isset($bk_data3->score)){
                        $score = $bk_data3->score;
                    }else{
                        $score = "";
                    }

                return $score;    
    }

    public function get_season_data(){

        global $wpdb;
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


        $users_groups_ids = implode(",", $users_groups_ids);
        $seasons_sql = "SELECT id, name from seasons where users_groups_id in ($users_groups_ids)";
        $seasons_data = $wpdb->get_results($seasons_sql);

        return $seasons_data;
    }

    public function check_form_data(){

        global $wpdb;

        $cr_user = Gibbs_Common_Calendar::get_current_admin_user();

        $active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

        if($active_group_id != ""){
            $group_id = $active_group_id;
        }else{
            $group_id = "0";
        }


        $type_of_form = "";
        
        $selected_season = get_user_meta($cr_user,"selected_season",true); 
        
        $seasons_sql = "SELECT users_groups_id from `seasons` where users_groups_id = $group_id AND id=".$selected_season;
        $seasons_data = $wpdb->get_row($seasons_sql);

        if(isset($seasons_data->users_groups_id)){
            $table_group = $wpdb->prefix."users_groups";
            $group_sql = "SELECT type_of_form from $table_group where id=".$seasons_data->users_groups_id;
            $group_data = $wpdb->get_row($group_sql);
            if(isset($group_data->type_of_form)){
               $type_of_form =  $group_data->type_of_form;
            }
        }
        return $type_of_form;
    }
    public function check_form_data_tv($selected_season){

        global $wpdb;

        


        $type_of_form = "";
        
        
        $seasons_sql = "SELECT users_groups_id from `seasons` where id=".$selected_season;
        $seasons_data = $wpdb->get_row($seasons_sql);

        if(isset($seasons_data->users_groups_id)){
            $table_group = $wpdb->prefix."users_groups";
            $group_sql = "SELECT type_of_form from $table_group where id=".$seasons_data->users_groups_id;
            $group_data = $wpdb->get_row($group_sql);
            if(isset($group_data->type_of_form)){
               $type_of_form =  $group_data->type_of_form;
            }
        }
        return $type_of_form;
    }

    public static function get_translations($language)
    {

        $gibbs = [
            "week" => __("Week- ", "Gibbs"),
            "Location" => __("Location", "Gibbs"),
            "Passer for" => __("Passer for", "Gibbs"),
            "USE" => __("USE", "Gibbs"),
            "Club/Client" => __("Club/Client", "Gibbs"),
            "No teams found for selected user" => __("No teams found for selected user", "Gibbs"),
            "No Reservation Found!" => __("No Reservation Found!", "Gibbs"),
            "Username" => __("Username", "Gibbs"),
            "FirstName" => __("FirstName", "Gibbs"),
            "LastName" => __("LastName", "Gibbs"),
            "Description" => __("Description", "Gibbs"),
            "Email" => __("Email", "Gibbs"),
            "Phone" => __("Phone", "Gibbs"),
            "Filter" => __("Filter", "Gibbs"),
            "Select Location" => __("Select Location", "Gibbs"),
            "Setting" => __("Settings", "Gibbs"),
            "Calendar Setting" => __("Calendar Setting", "Gibbs"),
            "View calendar from" => __("Vis kalender fra ", "Gibbs"),
            "View calendar to" => __("Vis kalender til ", "Gibbs"),
            "View calendar from 00:00 - 23:59" => __("Vis kalender fra 00:00-23:59 ", "Gibbs"),
            "Start" => __("Start", "Gibbs"),
            "Select" => __("Select", "Gibbs"),
            "End" => __("End", "Gibbs"),
            "Show full day" => __("Show full day", "Gibbs"),
            "Export to PDF" => __("Export to PDF", "Gibbs"),
            "Search" => __("Search", "Gibbs"),
            "Client" => __("Client", "Gibbs"),
            "Team" => __("Team", "Gibbs"),
            "Comment" => __("Comment", "Gibbs"),
            "Repeat" => __("Repeat", "Gibbs"),
            "Related booking" => __("Related booking", "Gibbs"),
            "Message" => __("Message", "Gibbs"),
            "Booking details" => __("Booking details", "Gibbs"),
            "Customer info" => __("Customer info", "Gibbs"),
            "Client Name" => __("Client Name", "Gibbs"),
            "Date" => __("Date", "Gibbs"),
            "Time" => __("Time", "Gibbs"),
            "ALL RESERVATION" => __("ALL RESERVATION", "Gibbs"),
            "CLIENT INFO" => __("CLIENT INFO", "Gibbs"),
            "Client Name" => __("Client Name", "Gibbs"),
            "Date" => __("Date", "Gibbs"),
            "Time" => __("Time", "Gibbs"),
            "Select Group" => __("Select Group", "Select Group"),
            "Listing" => __("Listing", "Gibbs"),
            "Status" => __("Status", "Gibbs"),
            "Action" => __("Action", "Gibbs"),
            "No Data" => __("No Data", "Gibbs"),
            "No Data Found in Team" => __("No Data Found in Team", "Gibbs"),
            "loading" => __("Loading", "Gibbs"),
            "No Title" => __("No Title", "Gibbs"),
            "Fra" => __("Fra", "Gibbs"),
            "Till" => __("Till", "Gibbs"),
            "Group" => __("Group", "Group"),
            "Teams" => __("Teams", "Gibbs"),
            "export_in_csv" => __("Eksporter til csv", "Gibbs"),
            "export_in_database" => __("Til admin kalender", "Gibbs"),
            "select_export_option" => __("Eksporter", "Gibbs"),
            "Velg" => __("Velg", "Gibbs"),
            "cell_width" => __("Cellebredde ", "Gibbs"),
            "timeline_cell_width" => __("Timeline Cell width", "Gibbs"),
            "timeline_week_cell_width" => __("Timeline week Cell width", "Gibbs"),
            "small" => __("Liten", "Gibbs"),
            "medium" => __("Medium", "Gibbs"),
            "big" => __("Stor", "Gibbs"),
            "show_extra_info" => __("Nøkkelinfo ", "Gibbs"),
            "age_group" => __("Age group", "Gibbs"),
            "level" => __("Level", "Gibbs"),
            "type" => __("Type", "Gibbs"),
            "sport" => __("Sport", "Gibbs"),
            "members" => __("Members", "Gibbs"),
            "team_name" => __("Team name", "Gibbs"),
        ];

        return $gibbs;
    }
}
