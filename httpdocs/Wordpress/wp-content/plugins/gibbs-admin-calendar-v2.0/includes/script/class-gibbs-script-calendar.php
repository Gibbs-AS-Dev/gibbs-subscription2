<?php

class Gibbs_Script_Calendar
{

    public static function action_init()
    {
        add_action('wp_enqueue_scripts', array('Gibbs_Script_Calendar', 'enqueue_scripts'));
    }

    public static function enqueue_scripts()
    {
        global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }
        wp_enqueue_style('mobiscroll', GIBBS_CALENDAR_URL . 'assets/mobiscroll.custom/css/mobiscroll.jquery.min.css', [],GIBBS_VERSION);
        wp_enqueue_style('calender-style', GIBBS_CALENDAR_URL . 'assets/css/style.css', [],GIBBS_VERSION);
        
        wp_enqueue_script('mobiscroll', GIBBS_CALENDAR_URL . 'assets/mobiscroll.custom/js/mobiscroll.jquery.min.js',[],GIBBS_VERSION, true);
        //wp_enqueue_script('rrule', 'https://jakubroztocil.github.io/rrule/dist/es5/rrule-tz.min.js', array(), GIBBS_CALENDAR_VERSION, true);
        

        wp_enqueue_script('mobiscroll-common', GIBBS_CALENDAR_URL . 'assets/js/common.js', [],GIBBS_VERSION, true);
        // wp_enqueue_script( 'filters-script', GIBBS_CALENDAR_URL . 'assets/js/filters.js', array('custom-script'), null, true );
        // wp_localize_script( 'filters-script', 'WPMCalendarV2Obj', $js_variables );
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
            'template_type'            => "calender",
            'json_data'            => $jsonData,
            ));
        $lastid = $wpdb->insert_id;


        update_user_meta($user_id,"template_selected",$lastid);

    }

    
    
}
