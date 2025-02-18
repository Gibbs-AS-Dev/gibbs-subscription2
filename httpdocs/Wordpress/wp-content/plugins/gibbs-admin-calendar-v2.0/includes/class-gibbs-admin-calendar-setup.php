<?php

class Gibbs_Admin_Calendar_Setup
{

    public static function action_init()
    {
        add_action('wp_enqueue_scripts', array('Gibbs_Admin_Calendar_Setup', 'enqueue_scripts'));

        add_shortcode('schedule-calendar-v2', array('Gibbs_Admin_Calendar_Setup', 'render_calendar'));


        add_shortcode("schedule-calendar-new", array('Gibbs_Admin_Calendar_Setup', 'render_tv'));
    }

    public static function enqueue_scripts()
    {
       
    }


    public static function render_tv()
    {
        if(isset($_GET["cal-type-tv"]) && $_GET["cal-type-tv"] != ""){
            Gibbs_Season_Calendar_Setup::render_season_tv();
            return;
        }
        global $wpdb;
        global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }



        $listings = explode(',', esc_sql($_GET['listings']));
        $booking_table = $wpdb->prefix . 'bookings_calendar';
        $owner = $wpdb->get_results("SELECT * FROM $booking_table WHERE listing_id = $listings[0] LIMIT 1");

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

        wp_set_current_user($owner[0]->owner_id);

        wp_enqueue_script('tv-script', GIBBS_CALENDAR_URL . 'assets/js/tv.js', array(), GIBBS_VERSION, true);

        $language = Gibbs_Common_Calendar::get_language();

        $show_book_now = "no";

        if(isset($_GET["show_book_now"]) && $_GET["show_book_now"] == "yes"){
            $show_book_now = "yes";
        }

        $js_variables = array(
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'plugin_url'    => GIBBS_CALENDAR_URL,
            'translations'  => self::get_translations($language),
            'calendar_view'      => $_GET['view'] ? $_GET['view'] : 'timeline_week',
            'cal_starttime' => isset($_GET['start_hour']) ? $_GET['start_hour'] : '09:00',
            'cal_endtime' => isset($_GET['end_hour']) ? $_GET['end_hour'] : '17:00',
            'listings'      => $listings,
            'gym_resources'     => Gibbs_Common_Calendar::get_gym_resources($listings),
            'current_language'  => $language,
            'refresh'   => $refresh,
            'header' => $show_header,
            'show_book_now' => $show_book_now,
            'additional_info'   => $additional_info,
            'show_fields_info'   => $show_fields_info,
        );


        wp_localize_script('tv-script', 'WPMCalendarV2Obj', $js_variables);

        $cal_type       = ''; // 'view_only';
        $cal_view       = get_user_meta(get_current_user_ID(), "cal_view", true);
        $translations   = self::get_translations(Gibbs_Common_Calendar::get_language());
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



        <div mbsc-page class="wpm-recurring-event-add-edit-dialog outlogged calender-tv">
            <div style="height:<?= $height; ?>; width:<?= $width; ?>;margin:0 auto;">
                <div id="loader" class="loader-box-services" style="display:none"></div>
                <div id="toast-container" class="bottom-right">
                    <?php require_once GIBBS_CALENDAR_PATH . 'components/calendar-notification.php'; ?>
                </div>
                <div class="filter_overlay"></div>
                <div id="scheduler"></div>
                <?php require_once GIBBS_CALENDAR_PATH . 'components/calendar-outlogged-footer.php'; ?>

            </div>
        </div>
        <div style="display: none;">
            <?php
            require_once GIBBS_CALENDAR_PATH . 'modals/edit-link-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/info-event.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-settings.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-resource-tooltip.php';
            ?>
        </div>
        <style>
            .calender-tv .mbsc-timeline-resource, .calender-tv .mbsc-timeline-row {
                height: max-content !important;
            }
        </style>

    <?php

    }

    public static function render_calendar()
    {

        global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }

        

        global $wpdb;
        wp_enqueue_script('intlTelInput-script', get_stylesheet_directory_uri().'/assets/js/intlTelInput.js?'.time(), array(), GIBBS_VERSION, true);
        wp_enqueue_script('custom-script', GIBBS_CALENDAR_URL . 'assets/js/wpm.js?'.time(), array(), GIBBS_VERSION, true);
        wp_enqueue_style( 'access_management-jquery-datatable-style', GIBBS_CALENDAR_URL. 'assets/css/jquery.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'access_management-responsive-datatable-style', GIBBS_CALENDAR_URL. 'assets/css/responsive.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'access_management-datatable-script', GIBBS_CALENDAR_URL. 'assets/js/datatable.min.js' ,[],time());
	   
	    wp_enqueue_script( 'datatable-jquery', GIBBS_CALENDAR_URL.'assets/js/jquery.dataTables.min.js',array(),null,true);
        wp_enqueue_script( 'access_management-responsive-datatable-script', GIBBS_CALENDAR_URL. 'assets/js/dataTables.responsive.min.js' ,[],time());

        if (is_plugin_active('jwt-authentication-for-wp-rest-api')) {
            wp_localize_script('custom-script', 'script_vars', array(
                'current_user_jwt'  => Jwt_Auth_Public::generate_token_loggedin_user()
            ));
        }

        $language = Gibbs_Common_Calendar::get_language();

        $resources = Gibbs_Common_Calendar::get_gym_resources();



        $admin_user_id = Gibbs_Common_Calendar::get_current_admin_user();

        require_once GIBBS_CALENDAR_PATH . 'components/template-filter.php';

        $template_selected =  get_user_meta($admin_user_id,"template_selected",true);

        $template_data = Gibbs_Common_Calendar::get_selected_template_data($template_selected);

        $update_template_auto =  get_user_meta($admin_user_id,"update_template_auto",true);

        $update_template_auto = "no";

        $is_group_active = Gibbs_Common_Calendar::is_group_active();

        $template_data["cal_start_day"] = "1";
        $template_data["cal_end_day"] = "0";
        $template_data["cal_show_week_nos"] = "true";
        $template_data["cal_show_daily_summery_weak"] = "false";

        //echo "<pre>"; print_r($template_data); die;


        $js_variables = array(
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'plugin_url'    => GIBBS_CALENDAR_URL,
            'translations'  => self::get_translations($language),
            'template_selected'  => $template_selected,
            'cal_start_day' => ($template_data["cal_start_day"] != "")?$template_data["cal_start_day"]:'1',
            'cal_end_day'   => ($template_data["cal_end_day"] != "")?$template_data["cal_end_day"]:'0',
            'cal_starttime' => $template_data["cal_starttime"],
            'cal_endtime'   => $template_data["cal_endtime"],
            'cal_time_cell_step'    => $template_data["cal_time_cell_step"],
            'cal_time_label_step'   => $template_data["cal_time_label_step"],
            'cal_show_week_nos'   => $template_data["cal_show_week_nos"],
            'show_bk_payment_failed'   => $template_data["show_bk_payment_failed"],
            'show_bk_pay_to_confirm'   => $template_data["show_bk_pay_to_confirm"],
            'cal_show_daily_summery_weak'   => $template_data["cal_show_daily_summery_weak"],
            'additional_info'   => $template_data["additional_info"],
            'show_admin_icons'   => $template_data["show_admin_icons"],
            'show_fields_info'   => $template_data["show_fields_info"],
            'show_full_day' => get_option("show_full_day"),
            'cell_width'    => get_user_meta(get_current_user_ID(), "cell_width", true),
            'cal_view'      => get_user_meta(get_current_user_ID(), "cal_view", true),
            'cal_type'      => '', // 'view_only',
            'filter_location'   => $template_data["filter_location"],
            'filter_group'      => get_user_meta(get_current_user_ID(), "filter_group"),
            'filter_search'     => get_user_meta(get_current_user_ID(), "filter_search"),
            'calendar_view'     => $template_data["calendar_view"],
            'gym_resources'     => $resources,
            'current_language'  => $language,
            'update_template_auto'  => "$update_template_auto",
            'is_group_active'  => $is_group_active,
        );

        wp_localize_script('custom-script', 'WPMCalendarV2Obj', $js_variables);

        if(isset($resources["listings"])){
            $listings = $resources["listings"];
        }else{
            $listings = array();
        }



        $cal_type       = ''; // 'view_only';
        $cal_view       = get_user_meta(get_current_user_ID(), "cal_view", true);
        $translations   = self::get_translations(Gibbs_Admin_Calendar_Utility::get_language());
        $wpm_user_list  = Gibbs_Common_Calendar::get_user_list($cal_type, $listings);

        

        

        require_once GIBBS_CALENDAR_PATH . 'calendar_type/admin-calendar.php';
      
    ?>
        

<?php
    }

    public function saveCalenderFilter($user_id){

        global $wpdb;

        $filter_template_table = "filter_template";

        $jsonData = array(
                        "cal_start_day" => "1",
                        "cal_end_day" => "0",
                        "cal_starttime" => "06:00",
                        "cal_endtime" => "23:00",
                        "cal_time_cell_step" => "60",
                        "cal_time_label_step" => "60",
                        "calendarWeekNumbers" => "true",
                        "cal_show_daily_summery_weak" => "false",
                        "filter_location" => null,
                        "calendar_view" => "timeline_day",
                    );
        $jsonData = json_encode($jsonData);
        $wpdb->insert($filter_template_table, array(
            'name'            => "Standard visning",
            'user_id'            => $user_id,
            'template_type'            => "calender",
            'json_data'            => $jsonData,
            ));
        $lastid = $wpdb->insert_id;


        update_user_meta($user_id,"template_selected",$lastid);

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
