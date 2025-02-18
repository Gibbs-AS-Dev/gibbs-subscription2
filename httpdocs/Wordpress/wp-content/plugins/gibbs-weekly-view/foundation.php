<?php
/*
Plugin Name:  Gibbs weekly view 
Version: 1.3
Description: Booking system for single listing in weekly view format. Activating by adding widget in single listing
Author: Gibbs team
Author URI: https://www.gibbs.no
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gibbs custom code
*/


if ( ! defined( 'ABSPATH' ) ) exit;

define( 'GIBBS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/lib/class-gamajo-template-loader.php';
}
include( plugin_dir_path(__FILE__) .'/includes/class_gibbs_templates.php' );
include( plugin_dir_path(__FILE__) .'/includes/booking_cal.php' );

add_action( 'widgets_init', 'cal_widgets_init' );

function cal_widgets_init() {
    include( plugin_dir_path(__FILE__) .'/includes/class-gibbs-core-widgets.php' );
}
function enque_cstm(){
    global $wpdb;
    wp_dequeue_script( 'listeo_core-bookings' );
    wp_dequeue_script( 'momentjs' );
    
    
    //wp_dequeue_script( 'kendo-culture-js-js' );
    if(is_singular( 'listing' ))
    {
      //  wp_dequeue_script( 'flatpickr' );
        global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }
        
        $jquery_dependencies = array('jquery', 'jquery-ui-core','jquery-ui-widget','jquery-ui-position', 'jquery-ui-selectmenu', 'jquery-ui-tooltip' ,'moment');
        wp_enqueue_script( 'dattime', plugin_dir_url( __FILE__ ) .'js/jquery.datetimepicker.js', $jquery_dependencies,$version );
       
        wp_dequeue_script( 'custom-script' );
        //wp_enqueue_script( 'listeo-cal-plug', plugin_dir_url( __FILE__ ) . 'assets/plugins/global/plugins.bundle.js', array('jquery'), '1.1' );
        wp_enqueue_script( 'listeo-cal', plugin_dir_url( __FILE__ ) . 'assets/plugins/custom/fullcalendar/fullcalendar.bundle.js', $jquery_dependencies, $version );
       // wp_enqueue_script( 'date_format', plugin_dir_url( __FILE__ ) . 'assets/plugins/global/plugins.bundle.js', array('jquery'), '1.1' );
        //wp_enqueue_script( 'listeo-cal-plug', plugin_dir_url( __FILE__ ) . 'assets/plugins/global/plugins.bundle.js', array('jquery'), '1.1' );
        wp_enqueue_script( 'listeo-poppr', plugin_dir_url( __FILE__ ) . 'js/popper.js', array('jquery'), $version );
        wp_enqueue_script( 'listeo-bts', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array('jquery'), $version,true );
        wp_enqueue_script( 'listeo-multisel', plugin_dir_url( __FILE__ ) . 'js/bootstrap-multiselect.js', array('jquery'), $version );
        wp_enqueue_script( 'listeo-rrule', plugin_dir_url( __FILE__ ) . 'js/rrule-tz.min.js', array('jquery'), $version);
        wp_enqueue_script( 'jquery-uijs', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.min.js', array('jquery'), $version);
        wp_enqueue_script( 'listeo-custom3', plugin_dir_url( __FILE__ ) . 'js/book_custom.js', array('listeo-cal'), $version, true );
        wp_enqueue_style( 'listseo-cal-2',  plugin_dir_url( __FILE__ ) . "assets/plugins/custom/fullcalendar/fullcalendar.bundle.css", array(), $version);
        wp_enqueue_style( 'cal_custom_style',  plugin_dir_url( __FILE__ ) . "/css/cal_style.css", array(), $version);
        wp_enqueue_style( 'multi_styl',  plugin_dir_url( __FILE__ ) . "/css/style.css", array(), $version);
        wp_enqueue_style( 'dattim',  plugin_dir_url( __FILE__ ) . "/css/jquery.datetimepicker.css", array(), $version);
        wp_enqueue_style( 'jquery-uicss',  plugin_dir_url( __FILE__ ) . "/css/jquery-ui.css", array(), $version);
        /*
        $post_info = get_queried_object();
        $id = $post_info->ID;
        $_currDate = date("m/d/Y");
        $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `listing_id` = '$id'");
        $unavailableResults = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "r` WHERE `listing_id` = '$id' AND `status` = 'unavailable'");
        $waiting = array();
        $approved = array();
        $rejected = array();
        $_currDate = date("m/d/Y");




        $count_equipment = array();

        $_booking_system_equipment = get_post_meta( $id, '_booking_system_equipment', true );
        if($_booking_system_equipment == 0){
            $_booking_system_equipment = "";
        }
        $_max_guests = get_post_meta( $id, '_max_guests', true );

        foreach ($results as $key11 => $item) {
            if ($_currDate < $item->date_start) {

                $start = date_format(date_create($item->date_start), "m/d/Y");
                $end = date_format(date_create($item->date_end), "m/d/Y");
                $stHour = date_format(date_create($item->date_start), "H");
                $enHour = date_format(date_create($item->date_end), "H");

                if ($item->status == 'waiting' || $item->status == 'attention') {
                    $waiting[] = "{$start}|{$end}|{$stHour}|{$enHour}";
                } elseif ($item->status == 'confirmed' || $item->status == 'paid') {

                    
                    if($_booking_system_equipment != ""){

                        $adults_data = json_decode($item->comment);






                        // convert the strings to unix timestamps
                        $a = strtotime($stHour.":00");
                        $b = strtotime($enHour.":00");

                        if($b > $a){

                            // loop over every hour (3600sec) between the two timestamps
                            for($i = 0; $i < $b - $a; $i += 3600) {
                                // add the current iteration and echo it
                                $hour =  date('H', $a + $i);
                                $count_equipment[$start][$hour]["end_date"]  = $end;
                                $count_equipment[$start][$hour]["adults"]  += $adults_data->adults;
                                $count_equipment[$start][$hour]["_max_guests"]  = $_max_guests;

                            }
                            $count_equipment[$start][$enHour]["end_date"]  = $end;
                            $count_equipment[$start][$enHour]["adults"]  += $adults_data->adults;
                            $count_equipment[$start][$enHour]["_max_guests"]  = $_max_guests;

                        }
                        
                        // $count_equipment[] = "{$start}|{$end}|{$stHour}|{$enHour}|{$adults_data->adults}|{$_max_guests}";
                    }else{
                        $approved[] = "{$start}|{$end}|{$stHour}|{$enHour}";
    
    
                        $recurrenceRule = $item->recurrenceRule;
                        if($recurrenceRule!='' && $recurrenceRule!=null){
                            $recurrenceRules = explode(';', $recurrenceRule);
                            $rulex = 0;
                            foreach ($recurrenceRules as $Rule) {
                                    $Rules = explode('=', $Rule);
                                    if($Rules[0] == 'COUNT'){
                                        $RuleTotalDays = $Rules[1]*7;
                                        $rulex = 1;
                                    }elseif($Rules[0] == 'BYDAY'){
                                        $RuleWeekDays = explode(',', $Rules[1]);
                                    }elseif($Rules[0] == 'UNTIL'){
                                        $yearTotal = $Rules[1][0].$Rules[1][1].$Rules[1][2].$Rules[1][3];
                                        $monthTotal = $Rules[1][4].$Rules[1][5];
                                        $dayTotal = $Rules[1][6].$Rules[1][7];

                                        $datetime1 = date_create($start);
                                        $datetime2 = date_create($monthTotal.'/'.$dayTotal.'/'.$yearTotal);
                                        $interval = date_diff($datetime1, $datetime2);
                                        $RuleTotalDays = (int)$interval->format("%R%a");
                                        $rulex = 2;
                                    }
                            }

                            for($i=1; $i<=$RuleTotalDays; $i++){
                                    $week_day = strtoupper(substr(date("l", strtotime(date("m/d/Y", strtotime($start)) . " +$i day")), 0, 2));
                                    if(in_array($week_day, $RuleWeekDays)){
                                    $start_end = date("m/d/Y", strtotime(date("m/d/Y", strtotime($start)) . " +$i day"));
                                    $approved[] = "{$start_end}|{$start_end}|{$stHour}|{$enHour}";
                                    }
                                }

                                $approved = array_unique($approved);
                        }

                    }
                } else {
                    $rejected[] = "{$start}|{$end}|{$stHour}|{$enHour}";
                }
            }
        }


        $count_equipment = array("0"=>$count_equipment);
        $waitingLength = count($waiting);
        $approvedLength = count($approved);
        $rejectedLength = count($rejected);

        $unavailable = array();
        foreach ($unavailableResults as $item) {
            $date_startconverted = date($item->date_start);
            if ($_currDate < $date_startconverted) {
                $unavailable[] =  "{$item->date_start}|{$item->date_end}|{$item->hour_start}|{$item->hour_end}";
            }
        }
        $unavailableLength = count($unavailable);
        */
     //   wp_localize_script( 'listeo-custom2', 'listing_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'listing_id' => $post_info->ID, 'waitingLength' => $waitingLength, 'waiting' => json_encode($waiting), 'approvedLength' => $approvedLength, 'approved' => json_encode($approved), 'count_equipment' => json_encode($count_equipment), 'rejectedLength' => $rejectedLength, 'rejected' => json_encode($rejected), 'unavailableLength' => $unavailableLength, 'unavailable' => json_encode($unavailable)) );
    }
    wp_localize_script( 'listeocore-group-custom-fe-script', 'ajaxurl',
             admin_url( 'admin-ajax.php' ) );
    
}
add_action( 'wp_enqueue_scripts', 'enque_cstm', 999999 );
add_filter( 'woocommerce_order_item_get_name', 'filter_order_item_get_name', 10, 2 );
function filter_order_item_get_name( $item_name, $order_item ) {
    global $wpdb, $post;
    $order_id = $post->ID;
    $results_bookings = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '".$order_id."' ");
    $sub = array();
    $prt = '';
    foreach($results_bookings as $book)
    {
        if(!empty($book->parent_listing_id))
        {
            $lnk = get_the_permalink($book->listing_id);
            $link_b = get_the_permalink($book->parent_listing_id);
            $sub[] = "<a target='_blank' href='$lnk'>".get_the_title($book->listing_id)."</a>";
            $prt = "<a target='_blank' href='$link_b'>".get_the_title($book->parent_listing_id)."</a>";
        }
        else
        {
            $sub[] = "<a target='_blank' href='$lnk'>".get_the_title($book->listing_id)."</a>";
        }
    }
    if ( is_admin() && $order_item->is_type('line_item') ) {
        $product = $order_item->get_product();
       /* $product = $order_item->get_product();
        if(!empty($sub))
        {
            $item_name = '<strong>Parent Listing: </strong> &nbsp;'.$prt .'<br><strong>Sub Listing </strong> &nbsp;';
            $item_name .= implode(", ", $sub);
        }
        else
        {
            $item_name = $product->get_name();
        }*/
        $item_name = $product->get_name();
    }
    return $item_name;
}
