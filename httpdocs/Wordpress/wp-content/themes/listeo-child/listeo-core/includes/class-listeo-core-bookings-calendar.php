<?php

session_start();

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Listeo_Core_Bookings class.
 */
class Listeo_Core_Bookings_Calendar {

    public function __construct() {

        // for booking widget
        add_action('wp_ajax_check_avaliabity', array($this, 'ajax_check_avaliabity'));
        add_action('wp_ajax_nopriv_check_avaliabity', array($this, 'ajax_check_avaliabity'));

        add_action('wp_ajax_calculate_price', array($this, 'ajax_calculate_price'));
        add_action('wp_ajax_nopriv_calculate_price', array($this, 'ajax_calculate_price'));

        add_action('wp_ajax_update_slots', array($this, 'ajax_update_slots'));
        add_action('wp_ajax_nopriv_update_slots', array($this, 'ajax_update_slots'));


        // for bookings dashboard
        add_action('wp_ajax_listeo_bookings_manage', array($this, 'ajax_listeo_bookings_manage'));

        // booking page shortcode and post handling
        add_shortcode( 'listeo_booking_confirmation', array( $this, 'listeo_core_booking' ) );
        add_shortcode( 'listeo_bookings', array( $this, 'listeo_core_dashboard_bookings' ) );
        add_shortcode( 'listeo_my_bookings', array( $this, 'listeo_core_dashboard_my_bookings' ) );

        // when woocoommerce is paid trigger function to change booking status
        add_action( 'woocommerce_order_status_completed', array( $this, 'booking_paid' ), 9, 3 );
       add_action( 'woocommerce_thankyou', array( $this, 'setbookingstatus' ), 10, 1 );
        // remove listeo booking products from shop
        add_action( 'woocommerce_product_query', array($this,'listeo_wc_pre_get_posts_query' ));

        add_action( 'listeo_core_check_for_expired_bookings', array( $this, 'check_for_expired_booking' ) );

    }


    /**
     * WP Kraken #w785816
     */
    public static function wpk_change_booking_hours( $date_start, $date_end ) {

        $start_date_time = new DateTime( $date_start );
        $end_date_time = new DateTime( $date_end );

        $is_the_same_date = $start_date_time->format( 'Y-m-d' ) == $end_date_time->format( 'Y-m-d' );

        // single day bookings are not alowed, this is owner reservation
        // set end of this date as the next day
        if ( $is_the_same_date ) {
            $end_date_time->add( DateInterval::createfromdatestring('+1 day') );
        }

        $start_date_time->setTime( 12, 0 );
        $end_date_time->setTime( 11, 59, 59 );

        return array(
            'date_start'    => $start_date_time->format( 'Y-m-d H:i:s' ),
            'date_end'      => $end_date_time->format( 'Y-m-d H:i:s' )
        );

    }


    /**
     * Get bookings between dates filtred by arguments
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_bookings( $date_start, $date_end, $args = '', $by = 'booking_date', $limit = '', $offset = '' ,$all = '')  {

        global $wpdb;
        $result = false;
        // if(strlen($date_start)<10){
        //     if($date_start) { $date_start = $date_start.' 00:00:00'; }
        //     if($date_end) { $date_end = $date_end.' 23:59:59'; }
        // }

        // setting dates to MySQL style
        $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );
        $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );

        // WP Kraken
        //  $booking_hours = self::wpk_change_booking_hours( $date_start, $date_end );
        //    $date_start = $booking_hours[ 'date_start' ];
        //  $date_end = $booking_hours[ 'date_end' ];


        // filter by parameters from args
        $WHERE = '';
        $FILTER_CANCELLED = "AND NOT status='cancelled' ";

        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){
                    $WHERE .= " AND ( (`$index` = 'confirmed') OR (`$index` = 'paid') )";
                } elseif ( $value == 'icalimports' ) {

                } else {
                    $WHERE .= " AND (`$index` = '$value')";
                }
                if( $value == 'cancelled' || $value == 'special_price'){
                    $FILTER_CANCELLED = '';
                }
                if( $value == 'icalimports'){
                    $FILTER_CANCELLED = "AND NOT status='icalimports' ";
                }

            }
        }

        if($all == 'users'){
            $FILTER = "AND NOT comment='owner reservations'";
        } else if( $all == 'owner') {
            $FILTER = "AND comment='owner reservations'";
        } else {
            $FILTER = '';
        }


        if ( $limit != '' ) $limit = " LIMIT " . esc_sql($limit);

        if ( is_numeric($offset)) $offset = " OFFSET " . esc_sql($offset);

        switch ($by)
        {

            case 'booking_date' :
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE ((' $date_start' >= `date_start` AND ' $date_start' <= `date_end`) OR ('$date_end' >= `date_start` AND '$date_end' <= `date_end`) OR (`date_start` >= ' $date_start' AND `date_end` <= '$date_end')) $WHERE $FILTER $FILTER_CANCELLED $limit $offset", "ARRAY_A" );

                break;


            case 'created_date' :
                // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (' $date_start' <= `created` AND ' $date_end' >= `created`) AND (`status` IS NOT NULL)  $WHERE $FILTER_CANCELLED $limit $offset", "ARRAY_A" );
                break;

        }

        return $result;

    }

    public static function get_slots_bookings( $date_start, $date_end, $args = '', $by = 'booking_date', $limit = '', $offset = '' ,$all = '')  {

        global $wpdb;

        // if(strlen($date_start)<10){
        //     if($date_start) { $date_start = $date_start.' 00:00:00'; }
        //     if($date_end) { $date_end = $date_end.' 23:59:59'; }
        // }

        // setting dates to MySQL style
        $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );
        $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );

        // filter by parameters from args
        $WHERE = '';
        $FILTER_CANCELLED = "AND NOT status='cancelled' ";
        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){
                    $WHERE .= " AND ( (`$index` = 'confirmed') OR (`$index` = 'paid') )";
                } else {
                    $WHERE .= " AND (`$index` = '$value')";
                }
                if( $value == 'cancelled' ){
                    $FILTER_CANCELLED = '';
                }

            }
        }
        if($all == 'users'){
            $FILTER = "AND NOT comment='owner reservations'";
        } else {
            $FILTER = '';
        }

        if ( $limit != '' ) $limit = " LIMIT " . esc_sql($limit);

        if ( is_numeric($offset)) $offset = " OFFSET " . esc_sql($offset);
        switch ($by)
        {

            case 'booking_date' :
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (('$date_start' = `date_start` AND '$date_end' = `date_end`)) $WHERE $FILTER $FILTER_CANCELLED $limit $offset", "ARRAY_A" );
                break;


            case 'created_date' :
                // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (' $date_start' = `created` AND ' $date_end' = `created`) AND (`status` IS NOT NULL)  $WHERE $FILTER_CANCELLED $limit $offset", "ARRAY_A" );
                break;

        }

        return $result;

    }

    /**
     * Get maximum number of bookings between dates filtred by arguments, used for pagination
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_bookings_max( $date_start, $date_end, $args = '', $by = 'booking_date' )  {

        global $wpdb;

        // setting dates to MySQL style
        $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );
        $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );

        // filter by parameters from args
        $WHERE = '';
        $FILTER_CANCELLED = "AND NOT status='cancelled' ";
        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){
                    $WHERE .= " AND (`$index` = 'confirmed') OR (`$index` = 'paid')";
                } else {
                    $WHERE .= " AND (`$index` = '$value')";
                }
                if( $value == 'cancelled' ){
                    $FILTER_CANCELLED = '';
                }

            }
        }

        switch ($by)
        {

            case 'booking_date' :
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE ((' $date_start' >= `date_start` AND ' $date_start' <= `date_end`) OR ('$date_end' >= `date_start` AND '$date_end' <= `date_end`) OR (`date_start` >= ' $date_start' AND `date_end` <= '$date_end')) AND NOT comment='owner reservations' $WHERE $FILTER_CANCELLED", "ARRAY_A" );
                break;


            case 'created_date' :
                // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (' $date_start' <= `created` AND ' $date_end' >= `created`) AND (`status` IS NOT NULL) AND  NOT comment = 'owner reservations' $WHERE $FILTER_CANCELLED", "ARRAY_A" );
                break;

        }

        return $wpdb->num_rows;

    }

    /**
     * Get latest bookings number of bookings between dates filtred by arguments, used for pagination
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_newest_bookings( $args = '', $limit, $offset = 0 )  {

        global $wpdb;

        // setting dates to MySQL style

        // filter by parameters from args
        $WHERE = '';

        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){
                    $WHERE .= " AND status IN ('confirmed','paid')";
                } else if( $value == 'waiting'){
                    $WHERE .= "AND status IN ('waiting','attention')";
                }else{
                    $WHERE .= " AND (`$index` = '$value')";
                }

            }
        }
        if ( $limit != '' ) $limit = " LIMIT " . esc_sql($limit);
        //if(isset($args['status']) && $args['status'])
        $offset = " OFFSET " . esc_sql($offset);

        // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
        $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE  NOT comment = 'owner reservations' $WHERE ORDER BY `" . $wpdb->prefix . "bookings_calendar`.`created` DESC $limit $offset", "ARRAY_A" );


        return $result;

    }

    /**
     * Get latest bookings number of bookings between dates filtred by arguments, used for pagination
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_newest_my_bookings( $args = '', $limit, $offset = 0 )  {

        global $wpdb;

        // setting dates to MySQL style

        // filter by parameters from args
        $WHERE = '';

        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'waiting' ){
                    $WHERE .= " AND ((status IN ('attention','waiting')) OR (status IN ('confirmed')) AND `expiring` > NOW())";
                } elseif($value == 'cancelled'){
                    $WHERE .= " AND status IN ('confirmed','$value') AND `expiring` < NOW()";
                } else{
                    $WHERE .= " AND (`$index` = '$value')";
                }

            }
        }
        if ( $limit != '' ) $limit = " LIMIT " . esc_sql($limit);
        //if(isset($args['status']) && $args['status'])
        $offset = " OFFSET " . esc_sql($offset);

        // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
        $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE  NOT comment = 'owner reservations' $WHERE ORDER BY `" . $wpdb->prefix . "bookings_calendar`.`created` DESC $limit $offset", "ARRAY_A" );


        return $result;

    }

    /**
     * Check gow may free places we have
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args
     *
     * @return number $free_places that we have this time
     */
    public static function count_free_places( $listing_id, $date_start, $date_end, $slot = 0 )  {

        // get slots
        $_slots = self :: get_slots_from_meta ( $listing_id );
        $slots_status = get_post_meta ( $listing_id, '_slots_status', true );

        if(isset($slots_status) && !empty($slots_status)) {
            $_slots = self :: get_slots_from_meta ( $listing_id );
        } else {
            $_slots = false;
        }
        // get listing type
        $listing_type = get_post_meta ( $listing_id, '_listing_type', true );


        // default we have one free place
        $free_places = 1;

        // check if this is service type of listing and slots are added, then checking slots
        if ( $listing_type == 'service' && $_slots )
        {
            $slot = json_decode( wp_unslash($slot) );

            // converent hours to mysql format
            $hours = explode( ' - ', $slot[0] );
            $hour_start = date( "H:i:s", strtotime( $hours[0] ) );
            $hour_end = date( "H:i:s", strtotime( $hours[1] ) );

            // add hours to dates
            $date_start .= ' ' . $hour_start;
            $date_end .= ' ' . $hour_end;

            // get day and number of slot
            $day_and_number = explode( '|', $slot[1] );
            $slot_day = $day_and_number[0];
            $slot_number =  $day_and_number[1];


            // get amount of slots
            $slots_amount = explode( '|', $_slots[$slot_day][$slot_number] );
            $slots_amount = $slots_amount[1];

            $free_places = $slots_amount;

        } else if ( $listing_type == 'service' && ! $_slots )  {

            // if there are no slots then always is free place and owner menage himself

            // check for imported icals
            $result = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'reservation' ) );
            if(!empty($result)) {
                return 0;
            } else {
                return 1;
            }


        }

        if ( $listing_type == 'event' ) {

            // if its event then always is free place and owner menage himself
            $ticket_number = get_post_meta ( $listing_id, '_event_tickets', true );
            $ticket_number_sold = get_post_meta ( $listing_id, '_event_tickets_sold', true );
            return $ticket_number - $ticket_number_sold;


        }

        // get reservations to this slot and calculace amount
        $result = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'reservation' ) );

        // count how many reservations we have already for this slot
        $reservetions_amount = count( $result );

        // minus temp reservations for this time
        // $free_places -= self :: temp_reservation_aval( array( 'listing_id' => $listing_id,
        // 'date_start' => $date_start, 'date_end' => $date_end) );

        // minus reservations from database
        $free_places -= $reservetions_amount;
        return $free_places;

    }

    /**
     * Ajax check avaliabity
     *
     * @return number $ajax_out['free_places'] amount or zero if not
     *
     * @return number $ajax_out['price'] calculated from database prices
     *
     */
    public static function ajax_check_avaliabity(  )  {
        if(!isset($_POST['slot'])){
            $slot = false;
        } else {
            $slot = $_POST['slot'];
        }
        if(isset($_POST['hour'])){
            $ajax_out['free_places'] = 1;
        } else {
            $ajax_out['free_places'] = self :: count_free_places( $_POST['listing_id'], $_POST['date_start'], $_POST['date_end'], $slot );
        }
        $multiply = 1;
        if(isset($_POST['adults'])) $multiply = $_POST['adults'];
        if(isset($_POST['tickets'])) $multiply = $_POST['tickets'];

        $services = (isset($_POST['services'])) ? $_POST['services'] : false ;
        // calculate price for all
        $discount_percentage = get_post_meta($_POST['listing_id'],$_POST['discount'],true);  
        $percentInDecimal = intval($discount_percentage) / 100;
        $_SESSION['test'] = $_POST['totalPrice'];
        $ajax_out['services_price'] = self :: calculate_price_services( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, $_POST['totalPrice'], $_POST['totalDays']  );
        $ajax_out['normal_price'] = self :: calculate_normal_price( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, $_POST['totalPrice'], $_POST['totalDays']  );
        $ajax_out['multiply'] = $multiply;

        
        if(is_numeric($discount_percentage)){
            $ajax_out['normal_price'] = $ajax_out['normal_price'] - ($ajax_out['normal_price'] * $percentInDecimal);

        }
        $ajax_out['price'] = self :: calculate_price( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply,0, $ajax_out['normal_price'], $_POST['totalDays']  );

        $taxPercentage = get_post_meta ( $_POST['listing_id'], '_tax', true);
        $tax = ($taxPercentage / 100) * ceil($ajax_out['normal_price']);
        $ajax_out['taxprice'] = ceil($tax);

        $ajax_out['services_tax_price'] = self :: calculate_tax_services( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, $_POST['totalPrice'], $_POST['totalDays']  );
        $ajax_out['price'] = ceil($ajax_out['price'] + $tax + $ajax_out['services_price']);

        $_SESSION['discountedprice'] = $ajax_out['price'];

        $arr = explode(",", $_POST['slot']);
        $arr1 = explode("-",$arr[0]);
        $arr1[0] = str_replace(array('[','"',' ',''), '',$arr1[0]);
        $arr1[0] = substr($arr1[0], 1); 
        $arr1[1] = str_replace(array('[',' ','\"'), '',$arr1[1]);

       
        $first_hour = $arr1[0];
        $second_hour = $arr1[1];
        $listing_id = $_POST['listing_id'];
        $date_start = $_POST['date_start'];
        $date_start = date_create("{$date_start} {$first_hour}");
        $date_start = date_format($date_start,"Y-m-d H:i:s");

        $end_date = $_POST['date_end'];
        $end_date = date_create("{$end_date} {$second_hour}");
        $end_date = date_format($end_date,"Y-m-d H:i:s");
        $a = "{$date_start}";
        $b = "{$end_date}";

        $max_amount = intval(get_post_meta($_POST['listing_id'],'_max_guests',true));
        $selected_amount = intval($_POST['adults']);
        
        $error = new WP_Error( '001', 'No user information was retrieved.', 'Some information' );

        global $wpdb;   
        $results = $wpdb->get_results("SELECT comment FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `listing_id` = '$listing_id' AND ('{$a}' BETWEEN `date_start` AND `date_end`) OR ('{$b}' BETWEEN `date_start` AND `date_end`) OR ('{$a}' <= `date_start` AND '{$b}' >= `date_end`)");
       
        $if_equipment = get_post_meta ( $_POST['listing_id'], '_category', true);
        
        if($if_equipment == 'utstr'){
            if(!empty($results)){
                foreach ($results as $result) {
                    $a = $result->comment;
                    $a = json_decode($a);
                    $selected = intval($a->adults);
                    if($max_amount > $selected ){
                        $max_amount = $max_amount - $selected;
                    }else{
                        wp_send_json_error( $error );
                    }
                }  
                if($max_amount >= $selected_amount){
                    wp_send_json_success( $ajax_out );
                }else{
                    wp_send_json_error( $error );
                    
                }
            }else{
                if($selected_amount <= $max_amount){
                    wp_send_json_success( $ajax_out );
    
                }else{    
                    wp_send_json_error( $error );
                    
                }
            }
    
        }else{
            wp_send_json_success( $ajax_out );
        }
        
        // $error = new WP_Error( '001', 'No user information was retrieved.', 'Some information' );
        // wp_send_json_error( $error );
        // wp_send_json_success( $ajax_out );

    }

    public static function ajax_calculate_price( ) {
        $listing_id = $_POST['listing_id'];
        $tickets = isset($_POST['tickets']) ? $_POST['tickets'] : 1 ;

        $normal_price = (float) get_post_meta ( $listing_id, '_normal_price', true);
        $reservation_price  =  (float) get_post_meta ( $listing_id, '_reservation_price', true);
        $services_price = 0;
        if(isset($_POST['services'])){
            $services = $_POST['services'];
            if(isset($services) && !empty($services)){

                $bookable_services = listeo_get_bookable_services($listing_id);
                $countable = array_column($services,'value');

                $i = 0;
                foreach ($bookable_services as $key => $service) {

                    if(in_array(sanitize_title($service['name']),array_column($services,'service'))) {
                        //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);

                        $services_price +=  listeo_calculate_service_price($service, $tickets, $totalDays, $countable[$i] );

                        $i++;
                    }


                }
            }
            // $bookable_services = listeo_get_bookable_services($listing_id);
            //  $i = 0;
            //  foreach ($bookable_services as $key => $service) {
            //     $i++;
            //     if(in_array('service_'.$i,$services)) {
            //       $services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);

            //     }
            //  }
        }
        $ajax_out['price'] =5;
        

        wp_send_json_success( $ajax_out );
    }


    public static function ajax_update_slots( ) {
        // get slots

        $listing_id = $_POST['listing_id'];
        $date_end = $_POST['date_start'];
        $date_start = $_POST['date_end'];

        $dayofweek = date('w', strtotime($date_start));

        $un_slots = get_post_meta( $listing_id, '_slots', true );

        $_slots = self :: get_slots_from_meta ( $listing_id );

        //sloty na dany dzien:
        if($dayofweek == 0){
            $actual_day = 6;
        } else {
            $actual_day = $dayofweek-1;
        }


        $_slots_for_day = $_slots[$actual_day];
        $ajax_out = false;
        $new_slots = array();

        if(is_array($_slots_for_day) && !empty($_slots_for_day)){

            foreach ($_slots_for_day as $key => $slot) {
                //$slot = json_decode( wp_unslash($slot) );

                $places = explode( '|', $slot );
                $free_places = $places[1];


                //get hours and date to check reservation
                $hours = explode( ' - ', $places[0] );
                $hour_start = date( "H:i:s", strtotime( $hours[0] ) );
                $hour_end = date( "H:i:s", strtotime( $hours[1] ) );

                // add hours to dates
                $date_start = $_POST['date_start']. ' ' . $hour_start;
                $date_end = $_POST['date_end']. ' ' . $hour_end;


                $result = self ::  get_slots_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'reservation' ) );
                $reservations_amount = count( $result );


                // $free_places -= self :: temp_reservation_aval( array( 'listing_id' => $listing_id, 'date_start' => $date_start, 'date_end' => $date_end) );

                $free_places -= $reservations_amount;
                if($free_places>0){


                    $new_slots[] = $places[0].'|'.$free_places;
                }
            }

            ?>

            <?php
            $days_list = array(
                0   => __('Monday','listeo_core'),
                1   => __('Tuesday','listeo_core'),
                2   => __('Wednesday','listeo_core'),
                3   => __('Thursday','listeo_core'),
                4   => __('Friday','listeo_core'),
                5   => __('Saturday','listeo_core'),
                6   => __('Sunday','listeo_core'),
            );
            ob_start();?><input id="slot" type="hidden" name="slot" value="" />
            <input id="listing_id" type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"
            <?php
            foreach( $new_slots as $number => $slot) {
                $slot = explode('|' , $slot); ?>
                <!-- Time Slot -->
                <div class="time-slot" day="<?php echo $actual_day; ?>">
                    <input type="radio" name="time-slot" id="<?php echo $actual_day.'|'.$number; ?>" value="<?php echo $actual_day.'|'.$number; ?>">
                    <label for="<?php echo $actual_day.'|'.$number; ?>">
                        <p class="day"><?php //echo $days_list[$day]; ?></p>
                        <strong><?php echo $slot[0]; ?></strong>
                        <span><?php echo $slot[1]; esc_html_e(' slots available','listeo_core') ?></span>
                    </label>
                </div>
            <?php }
            $ajax_out = ob_get_clean();
        } else {
            //no slots for today
        }
        wp_send_json_success( $ajax_out );

    }

    /**
     * Ajax bookings dashboard
     *
     *
     */
    public static function ajax_listeo_bookings_manage(  )  {
        $current_user_id = get_current_user_id();
        // when we only changing status
        if ( isset( $_POST['status']) ) {

            // changing status only for owner and admin
            //if ( $current_user_id != $owner_id && ! is_admin() ) return;

            wp_send_json_success( self :: set_booking_status( $_POST['booking_id'], $_POST['status']) );
        }

        $args = array (
            'owner_id' => get_current_user_id(),
            'type' => 'reservation'
        );
        $offset = ( absint( $_POST['page'] ) - 1 ) * absint( get_option('posts_per_page') );
        $limit =  get_option('posts_per_page');

        if ( isset($_POST['listing_id']) &&  $_POST['listing_id'] != 'show_all'  ) $args['listing_id'] = $_POST['listing_id'];
        if ( isset($_POST['listing_status']) && $_POST['listing_status'] != 'show_all'  ) $args['status'] = $_POST['listing_status'];


        if ( $_POST['dashboard_type'] != 'user' ){
            if($_POST['date_start']==''){
                $ajax_out = self :: get_newest_bookings( $args, $limit, $offset );
                $bookings_max_number = listeo_count_bookings(get_current_user_id(),$args['status']);
            } else {
                $ajax_out = self :: get_bookings( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date', $limit, $offset,'users' );
                $bookings_max_number = self :: get_bookings_max( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date');

            }
        }


        //        if user dont have listings show his reservations
        if ( $_POST['dashboard_type'] == 'user' ) {
            unset( $args['owner_id'] );
            unset($args['status']);
            unset($args['listing_id']);

            $args['bookings_author'] = get_current_user_id();
            if($_POST['date_start']==''){
                $ajax_out = self :: get_newest_bookings( $args, $limit, $offset );
                $bookings_max_number = listeo_count_my_bookings(get_current_user_id(),$args['status']);
            } else {
                $ajax_out = self :: get_bookings( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date', $limit, $offset, 'users' );
                $bookings_max_number = self :: get_bookings_max( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date');
            }

        }

        $result = array();
        $template_loader = new Listeo_Core_Template_Loader;
        $max_number_pages = ceil($bookings_max_number/$limit);
        ob_start();
        if($ajax_out){

            foreach ($ajax_out as $key => $value) {
                if ( isset($_POST['dashboard_type']) && $_POST['dashboard_type'] == 'user' ) {
                    $template_loader->set_template_data( $value )->get_template_part( 'booking/content-user-booking' );
                } else {
                    $template_loader->set_template_data( $value )->get_template_part( 'booking/content-booking' );
                }

            }
        }

        $result['pagination'] = listeo_core_ajax_pagination( $max_number_pages, absint( $_POST['page'] ) );
        $result['html'] = ob_get_clean();
        wp_send_json_success( $result );

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https://";
            $urlCompare = "https://";
        }else {
            $url = "http://";
            $urlCompare = "http://";
        }
        // Append the host(domain name, ip) to the URL.
        $url .= $_SERVER['HTTP_HOST'];
        $urlCompare .= $_SERVER['HTTP_HOST'];
        // Append the requested resource location to the URL
        $url .= $_SERVER['REQUEST_URI'];
        $urlCompare .= "/?status=waiting";
        if($url == $urlCompare) {
            header("Location: " . $url);
        }

    }


    /**
     * Insert booking with args
     *
     * @param  array $args list of parameters
     *
     */
    public static function insert_booking( $args )  {

        global $wpdb;

        $insert_data = array(
            'bookings_author' => get_current_user_id(),
            'owner_id' => $args['owner_id'],
            'listing_id' => $args['listing_id'],
            'date_start' => date( "Y-m-d H:i:s", strtotime( $args['date_start'] ) ),
            'date_end' => date( "Y-m-d H:i:s", strtotime( $args['date_end'] ) ),
            'comment' =>  $args['comment'],
            'type' =>  $args['type'],
            'created' => current_time('mysql')
        );

        if ( isset( $args['order_id'] ) ) $insert_data['order_id'] = $args['order_id'];
        if ( isset( $args['expiring'] ) ) $insert_data['expiring'] = $args['expiring'];
        if ( isset( $args['status'] ) ) $insert_data['status'] = $args['status'];
        if ( isset( $args['price'] ) ) $insert_data['price'] = $args['price'];

        $wpdb -> insert( $wpdb->prefix . 'bookings_calendar', $insert_data );

        return  $wpdb -> insert_id;

    }

    /**
     * Set booking status - we changing booking status only by this function
     *
     * @param  array $args list of parameters
     *
     * @return number of deleted records
     */
    public static function set_booking_status( $booking_id, $status ) {

        global $wpdb;

        $booking_data = $wpdb -> get_row( 'SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar` WHERE `id`=' . esc_sql( $booking_id ), 'ARRAY_A' );
        if(!$booking_data){
            return;
        }

        $user_id = $booking_data['bookings_author'];
        $owner_id = $booking_data['owner_id'];
        $startDate = $booking_data['date_start'];
        $current_user_id = get_current_user_id();

        // get information about users
        $user_info = get_userdata( $user_id );

        $owner_info = get_userdata( $owner_id );
        $comment = json_decode($booking_data['comment']);

        // only one time clicking blocking
        if ( $booking_data['status'] == $status ) return;


        switch ( $status )
        {

            // this is status when listing waiting for approval by owner
            case 'waiting' :

                if($startDate == '1970-01-01 00:00:00'){
                    $update_values['status'] = 'attention';
                }else{
                    $update_values['status'] = 'waiting';
                }

                // mail for user
                $mail_to_user_args = array(
                    'email' => $user_info->user_email,
                    'booking'  => $booking_data,
                );
                do_action('listeo_mail_to_user_waiting_approval',$mail_to_user_args);
                // wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your reservation waiting for be approved by owner!', 'listeo_core' ) );

                // mail for owner
                $mail_to_owner_args = array(
                    'email'     => $owner_info->user_email,
                    'booking'  => $booking_data,
                );

                do_action('listeo_mail_to_owner_new_reservation',$mail_to_owner_args);

                // mail for gibbs.no
                $mail_to_gibbs_args = array(
                    'email'     => 'booking@gibbs.no',
                    'booking'  => $booking_data,
                );

                do_action('listeo_mail_to_owner_new_reservation',$mail_to_gibbs_args);

                // wp_mail( $owner_info->user_email, __( 'Welcome owner', 'listeo_core' ), __( 'In your panel waiting new reservation to be accepted!', 'listeo_core' ) );

                break;

            // this is status when listing is confirmed by owner and waiting to payment
            case 'confirmed' :

                // get woocommerce product id
                $product_id = get_post_meta( $booking_data['listing_id'], 'product_id', true);

                // calculate when listing will be expired when will bo not pays
                $expired_after = get_post_meta( $booking_data['listing_id'], '_expired_after', true);
                if(empty($expired_after)) {
                    $expired_after = 48;
                }
                if(!empty($expired_after) && $expired_after > 0){
                    $expiring_date = date( "Y-m-d H:i:s", strtotime('+'.$expired_after.' hours') );
                }


                //
                $instant_booking = get_post_meta( $booking_data['listing_id'], '_instant_booking', true);

                if($instant_booking) {

                    $mail_to_user_args = array(
                        'email' => $user_info->user_email,
                        'booking'  => $booking_data,
                    );
                    do_action('listeo_mail_to_user_instant_approval',$mail_to_user_args);
                    // wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your reservation waiting for be approved by owner!', 'listeo_core' ) );

                    // mail for owner
                    $mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        'booking'  => $booking_data,
                    );

                    do_action('listeo_mail_to_owner_new_intant_reservation',$mail_to_owner_args);

                }


                // for free listings
                if ( $booking_data['price'] == 0 )
                {

                    // mail for user
                    //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your is paid!', 'listeo_core' ) );
                    $mail_args = array(
                        'email'     => $user_info->user_email,
                        'booking'  => $booking_data,
                    );
                    do_action('listeo_mail_to_user_free_confirmed',$mail_args);

                    $update_values['status'] = 'paid';
                    $update_values['expiring'] = '';

                    break;

                }

                $first_name = (isset($comment->first_name) && !empty($comment->first_name)) ? $comment->first_name : get_user_meta( $user_id, "billing_first_name", true) ;

                $last_name = (isset($comment->last_name) && !empty($comment->last_name)) ? $comment->last_name : get_user_meta( $user_id, "billing_last_name", true) ;

                $phone = (isset($comment->phone) && !empty($comment->phone)) ? $comment->phone : get_user_meta( $user_id, "billing_phone", true) ;

                $email = (isset($comment->email) && !empty($comment->email)) ? $comment->email : get_user_meta( $user_id, "user_email", true) ;

                $billing_address_1 = (isset($comment->billing_address_1) && !empty($comment->billing_address_1)) ? $comment->billing_address_1 : '';

                $billing_city = (isset($comment->billing_city) && !empty($comment->billing_city)) ? $comment->billing_city : '';

                $billing_postcode = (isset($comment->billing_postcode) && !empty($comment->billing_postcode)) ? $comment->billing_postcode : '';

                $billing_country = (isset($comment->billing_country) && !empty($comment->billing_country)) ? $comment->billing_country : '';

                $address = array(
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'address_1' => $billing_address_1,
                    //billing_address_2
                    'city' => $billing_city,
                    //'billing_state'
                    'postcode'  => $billing_postcode,
                    'country'   => $billing_country,

                );

                // creating woocommerce order
                $order = wc_create_order();

                $args['totals']['subtotal'] = $booking_data['price'];
                $args['totals']['total'] = $booking_data['price'];
                

                $comment = json_decode($booking_data['comment']);

                $order->add_product( wc_get_product( $product_id ), 1, $args );
                $order->set_address( $address, 'billing' );
                $order->set_address( $address, 'shipping' );
                $order->set_customer_id($user_id);
                $order->set_billing_email( $email );
                if(isset($expiring_date)){
                    $order->set_date_paid( strtotime( $expiring_date ) );
                }




                $payment_url = $order->get_checkout_payment_url();



                $order->calculate_totals();
                $order->save();

                $order->update_meta_data('booking_id', $booking_id);
                $order->update_meta_data('owner_id', $owner_id);
                $order->update_meta_data('listing_id', $booking_data['listing_id']);
                if(isset($comment->service)){

                    $order->update_meta_data('listeo_services', $comment->service);
                }

                $order->save_meta_data();

                $update_values['status'] = 'confirmed';
                if(isset($expiring_date)){
                    $update_values['expiring'] = $expiring_date;
                }
                $update_values['order_id'] = $order->get_order_number();


                // mail for user
                //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), sprintf( __( 'Your reservation waiting for payment! Please do it before %s hours. Here is link: %s', 'listeo_core' ), $expired_after, $payment_url  ) );
                $mail_args = array(
                    'email'         => $user_info->user_email,
                    'booking'       => $booking_data,
                    'expiration'    => $expiring_date,
                    'payment_url'   => $payment_url
                );

                do_action('listeo_mail_to_user_pay',$mail_args);


                break;

            // this is status when listing is confirmed by owner and already paid
			
			case 'pending' :

                // get woocommerce product id
                $product_id = get_post_meta( $booking_data['listing_id'], 'product_id', true);

                // calculate when listing will be expired when will bo not pays
                $expired_after = get_post_meta( $booking_data['listing_id'], '_expired_after', true);
                if(empty($expired_after)) {
                    $expired_after = 48;
                }
                if(!empty($expired_after) && $expired_after > 0){
                    $expiring_date = date( "Y-m-d H:i:s", strtotime('+'.$expired_after.' hours') );
                }


                //
                $instant_booking = get_post_meta( $booking_data['listing_id'], '_instant_booking', true);

                if($instant_booking) {

                    $mail_to_user_args = array(
                        'email' => $user_info->user_email,
                        'booking'  => $booking_data,
                    );
                    do_action('listeo_mail_to_user_instant_approval',$mail_to_user_args);
                    // wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your reservation waiting for be approved by owner!', 'listeo_core' ) );

                    // mail for owner
                    $mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        'booking'  => $booking_data,
                    );

                    do_action('listeo_mail_to_owner_new_intant_reservation',$mail_to_owner_args);

                }


                // for free listings
                if ( $booking_data['price'] == 0 )
                {

                    // mail for user
                    //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your is paid!', 'listeo_core' ) );
                    $mail_args = array(
                        'email'     => $user_info->user_email,
                        'booking'  => $booking_data,
                    );
                    do_action('listeo_mail_to_user_free_confirmed',$mail_args);

                    $update_values['status'] = 'paid';
                    $update_values['expiring'] = '';

                    break;

                }

                $first_name = (isset($comment->first_name) && !empty($comment->first_name)) ? $comment->first_name : get_user_meta( $user_id, "billing_first_name", true) ;

                $last_name = (isset($comment->last_name) && !empty($comment->last_name)) ? $comment->last_name : get_user_meta( $user_id, "billing_last_name", true) ;

                $phone = (isset($comment->phone) && !empty($comment->phone)) ? $comment->phone : get_user_meta( $user_id, "billing_phone", true) ;

                $email = (isset($comment->email) && !empty($comment->email)) ? $comment->email : get_user_meta( $user_id, "user_email", true) ;

                $billing_address_1 = (isset($comment->billing_address_1) && !empty($comment->billing_address_1)) ? $comment->billing_address_1 : '';

                $billing_city = (isset($comment->billing_city) && !empty($comment->billing_city)) ? $comment->billing_city : '';

                $billing_postcode = (isset($comment->billing_postcode) && !empty($comment->billing_postcode)) ? $comment->billing_postcode : '';

                $billing_country = (isset($comment->billing_country) && !empty($comment->billing_country)) ? $comment->billing_country : '';

                $address = array(
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'address_1' => $billing_address_1,
                    //billing_address_2
                    'city' => $billing_city,
                    //'billing_state'
                    'postcode'  => $billing_postcode,
                    'country'   => $billing_country,

                );

                // creating woocommerce order
                $order = wc_create_order();

                $args['totals']['subtotal'] = $booking_data['price'];
                $args['totals']['total'] = $booking_data['price'];
                

                $comment = json_decode($booking_data['comment']);

                $order->add_product( wc_get_product( $product_id ), 1, $args );
                $order->set_address( $address, 'billing' );
                $order->set_address( $address, 'shipping' );
                $order->set_customer_id($user_id);
                $order->set_billing_email( $email );
                if(isset($expiring_date)){
                    $order->set_date_paid( strtotime( $expiring_date ) );
                }




                $payment_url = $order->get_checkout_payment_url();



                $order->calculate_totals();
                $order->save();

                $order->update_meta_data('booking_id', $booking_id);
                $order->update_meta_data('owner_id', $owner_id);
                $order->update_meta_data('listing_id', $booking_data['listing_id']);
                if(isset($comment->service)){

                    $order->update_meta_data('listeo_services', $comment->service);
                }

                $order->save_meta_data();

                $update_values['status'] = 'pending';
                if(isset($expiring_date)){
                    $update_values['expiring'] = $expiring_date;
                }
                $update_values['order_id'] = $order->get_order_number();


                // mail for user
                //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), sprintf( __( 'Your reservation waiting for payment! Please do it before %s hours. Here is link: %s', 'listeo_core' ), $expired_after, $payment_url  ) );
                $mail_args = array(
                    'email'         => $user_info->user_email,
                    'booking'       => $booking_data,
                    'expiration'    => $expiring_date,
                    'payment_url'   => $payment_url
                );

                do_action('listeo_mail_to_user_pay',$mail_args);


                break;

            // this is status when listing is confirmed by owner and already paid
            case 'paid' :

                // mail for owner
                //wp_mail( $owner_info->user_email, __( 'Welcome owner', 'listeo_core' ), __( 'Your client paid!', 'listeo_core' ) );
                $mail_to_owner_args = array(
                    'email'     => $owner_info->user_email,
                    'booking'  => $booking_data,
                );
                do_action('listeo_mail_to_owner_paid',$mail_to_owner_args);
                // mail for user
                // wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your is paid!', 'listeo_core' ) );

                $update_values['status'] = 'paid';
                $update_values['expiring'] = '';


                break;

            // this is status when listing is confirmed by owner and already paid
            case 'cancelled' :

                // mail for user
                //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your reservation was cancelled by owner', 'listeo_core' ) );
                $mail_to_user_args = array(
                    'email'     => $user_info->user_email,
                    'booking'  => $booking_data,
                );
                do_action('listeo_mail_to_user_canceled',$mail_to_user_args);
                // delete order if exist
                if ( $booking_data['order_id'] )
                {
                    $order = wc_get_order( $booking_data['order_id'] );
                    $order->update_status( 'cancelled', __( 'Order is cancelled.', 'listeo_core' ) );
                }
                $comment = json_decode($booking_data['comment']);
                $tickets_from_order = $comment->tickets;

                $sold_tickets = (int) get_post_meta( $booking_data['listing_id'],"_event_tickets_sold",true);

                update_post_meta( $booking_data['listing_id'],"_event_tickets_sold",$sold_tickets-$tickets_from_order);

                $update_values['status'] = 'cancelled';
                $update_values['expiring'] = '';

                break;
            // this is status when listing is confirmed by owner and already paid
            case 'deleted' :


                if ( $booking_data['order_id'] )
                {
                    $order = wc_get_order( $booking_data['order_id'] );
                    //$order->update_status( 'cancelled', __( 'Order is cancelled.', 'listeo_core' ) );
                }
                return $wpdb->update( $wpdb->prefix . 'bookings_calendar', array("status" => "deleted", "status" => "expired"), array( 'id' => $booking_id ));


                //return $wpdb -> delete( $wpdb->prefix . 'bookings_calendar', array( 'id' => $booking_id ) );

                break;
        }

        return $wpdb -> update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'id' => $booking_id ) );

    }

    /**
     * Delete all booking wih parameters
     *
     * @param  array $args list of parameters
     *
     * @return number of deleted records
     */
    public static function delete_bookings( $args )  {

        global $wpdb;

        return $wpdb->update( $wpdb->prefix . 'bookings_calendar', array("status"=>"deleted"),  $args);

        //return $wpdb -> delete( $wpdb->prefix . 'bookings_calendar', $args );

    }

    /**
     * Update owner reservation list by delecting old one and add new ones
     *
     * @param  number $listing_id post id of current listing
     *
     * @return string $dates array with two dates
     */
    public static function update_reservations( $listing_id, $dates ) {

        // delecting old reservations
        self :: delete_bookings ( array(
            'listing_id' => $listing_id,
            'owner_id' => get_current_user_id(),
            'type' => 'reservation',
            'comment' => 'owner reservations') );

        // update by new one reservations
        foreach ( $dates as $date) {

            self :: insert_booking( array(
                'listing_id' => $listing_id,
                'type' => 'reservation',
                'owner_id' => get_current_user_id(),
                'date_start' => $date,
                'date_end' => date( 'Y-m-d H:i:s', strtotime('+23 hours +59 minutes +59 seconds', strtotime($date) ) ),
                'comment' =>  __('owner reservations', 'listeo_core'),
                'order_id' => NULL,
                'status' => 'owner_reservations'
            ));

        }


    }

    /**
     * Update listing special prices
     *
     * @param  number $listing_id post id of current listing
     * @param  array $prices with dates and prices
     *
     * @return string $prices array with special prices
     */
    public static function update_special_prices( $listing_id, $prices ) {

        // delecting old special prices
        self :: delete_bookings ( array(
            'listing_id' => $listing_id,
            'owner_id' => get_current_user_id(),
            'type' => 'special_price') );

        // update by new one special prices
        foreach ( $prices as $date => $price) {

            self :: insert_booking( array(
                'listing_id' => $listing_id,
                'type' => 'special_price',
                'owner_id' => get_current_user_id(),
                'date_start' => $date,
                'date_end' => $date,
                'comment' =>  $price,
                'order_id' => NULL,
                'status' => NULL
            ));

        }

    }


    /**
     * Calculate price
     *
     * @param  number $listing_id post id of current listing
     * @param  date  $date_start since we checking
     * @param  date  $date_end to we checking
     *
     * @return number $price of all booking at all
     */
    public static function calculate_price( $listing_id, $date_start, $date_end, $multiply, $services, $totalPrice = 1, $totalDays = 1 ) {
        // get all special prices between two dates from listeo settings special prices
        $special_prices_results = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'special_price' ) );

        // prepare special prices to nice array
        foreach ($special_prices_results as $result)
        {
            $special_prices[ $result['date_start'] ] = $result['comment'];
        }


        // get normal prices from listeo listing settings
        $normal_price = (float) get_post_meta ( $listing_id, '_normal_price', true);
        $weekend_price = (float)  get_post_meta ( $listing_id, '_weekday_price', true);
        if(empty($weekend_price)){
            $weekend_price = $normal_price;
        }
        $reservation_price  =  (float) get_post_meta ( $listing_id, '_reservation_price', true);
        $_count_per_guest  = get_post_meta ( $listing_id, '_count_per_guest', true);
        $services_price = 0;
        $listing_type = get_post_meta( $listing_id, '_listing_type', true);
        if($listing_type == 'event'){
            if(isset($services) && !empty($services)){
                $bookable_services = listeo_get_bookable_services($listing_id);
                $countable = array_column($services,'value');

                $i = 0;
                foreach ($bookable_services as $key => $service) {

                    if(in_array(sanitize_title($service['name']),array_column($services,'service'))) {
                        //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);
                        $services_price +=  listeo_calculate_service_price($service, $multiply, $totalDays, $countable[$i] );

                        $i++;
                    }


                }
            }
            //PRICE HERE
            // SESSION PROBLEM HERE
            $d = $totalDays;

            return $services_price+($d*$reservation_price+$normal_price*$multiply);

        }
        // prepare dates for loop
        // TODO CHECK THIS
        // $format = "d/m/Y  H:i:s";
        //     $firstDay =  DateTime::createFromFormat($format, $date_start. '00:00:01' );
        //     $lastDay =  DateTime::createFromFormat($format, $date_end. '23:59:59');
        $firstDay = new DateTime( $date_start );
        $lastDay = new DateTime( $date_end . '23:59:59') ;

        $days_between = $lastDay->diff($firstDay)->format("%a");
        $days_count = ($days_between == 0) ? 1 : $days_between ;
        //fix for not calculating last day of leaving
        if ( $date_start != $date_end ) $lastDay -> modify('-1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod( $firstDay, $interval, $lastDay );

        // at start we have reservation price
        $price = 0;
        foreach ( $period as $current_day ) {

            // get current date in sql format
            $date = $current_day->format("Y-m-d 00:00:00");
            $day = $current_day->format("N");

            if ( isset( $special_prices[$date] ) )
            {
                $price += $special_prices[$date];
            }
            else {
                $start_of_week = intval( get_option( 'start_of_week' ) ); // 0 - sunday, 1- monday
                // when we have weekends
                if($start_of_week == 0 ) {
                    if ( isset( $weekend_price ) && $day == 5 || $day == 6) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                } else {
                    if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                }

            }

        }
        if($_count_per_guest){
            $price = $price * (int) $multiply;
        }
        $services_price = 0;
        if(isset($services) && !empty($services)){
            $bookable_services = listeo_get_bookable_services($listing_id);
            $countable = array_column($services,'value');

            $i = 0;
            foreach ($bookable_services as $key => $service) {

                if(in_array(sanitize_title($service['name']),array_column($services,'service'))) {
                    //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);
                    $services_price +=  listeo_calculate_service_price($service, $multiply, $totalDays, $countable[$i] );

                    $i++;
                }


            }
        }
        if($totalPrice == 1){
            $totalPrice =   $_SESSION['discountedprice'] - $services_price;
        }
        
        $if_equipment = get_post_meta ( $listing_id, '_category', true);
        $number_of_guests = $multiply;

        if($if_equipment == 'utstr'){
            if(isset($_POST['adults'])) $number_of_guests = $_POST['adults'];
            $price = ($totalPrice * $number_of_guests) + $services_price;
        }else{
            $price = ($totalPrice) + $services_price;
        }
        //apply_filters('listeo_booking_price_calc',$endprice, $listing_id, $date_start, $date_end, $multiply , $services);

        $endprice = round($price,2);

        return apply_filters('listeo_booking_price_calc',$endprice, $listing_id, $date_start, $date_end, $multiply , $services);
    }

    /**
     * Calculate services price
     *
     * @param  number $listing_id post id of current listing
     * @param  date  $date_start since we checking
     * @param  date  $date_end to we checking
     *
     * @return number $price of all booking at all
     */
    public static function calculate_price_services( $listing_id, $date_start, $date_end, $multiply, $services, $totalPrice = 1, $totalDays = 1 ) {
        // get all special prices between two dates from listeo settings special prices
        $special_prices_results = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'special_price' ) );

        // prepare special prices to nice array
        foreach ($special_prices_results as $result)
        {
            $special_prices[ $result['date_start'] ] = $result['comment'];
        }

        // get normal prices from listeo listing settings
        $normal_price = (float) get_post_meta ( $listing_id, '_normal_price', true);
        $weekend_price = (float)  get_post_meta ( $listing_id, '_weekday_price', true);
        if(empty($weekend_price)){
            $weekend_price = $normal_price;
        }
        $reservation_price  =  (float) get_post_meta ( $listing_id, '_reservation_price', true);
        $_count_per_guest  = get_post_meta ( $listing_id, '_count_per_guest', true);
        $services_price = 0;
        $listing_type = get_post_meta( $listing_id, '_listing_type', true);

        $firstDay = new DateTime( $date_start );
        $lastDay = new DateTime( $date_end . '23:59:59') ;

        $days_between = $lastDay->diff($firstDay)->format("%a");
        $days_count = ($days_between == 0) ? 1 : $days_between ;
        //fix for not calculating last day of leaving
        if ( $date_start != $date_end ) $lastDay -> modify('-1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod( $firstDay, $interval, $lastDay );

        // at start we have reservation price
        $price = 0;
        foreach ( $period as $current_day ) {

            // get current date in sql format
            $date = $current_day->format("Y-m-d 00:00:00");
            $day = $current_day->format("N");

            if ( isset( $special_prices[$date] ) )
            {
                $price += $special_prices[$date];
            }
            else {
                $start_of_week = intval( get_option( 'start_of_week' ) ); // 0 - sunday, 1- monday
                // when we have weekends
                if($start_of_week == 0 ) {
                    if ( isset( $weekend_price ) && $day == 5 || $day == 6) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                } else {
                    if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                }

            }

        }
        if($_count_per_guest){
            $price = $price * (int) $multiply;
        }
        $services_price = 0;
        if(isset($services) && !empty($services)){
            $bookable_services = listeo_get_bookable_services($listing_id);
            $countable = array_column($services,'value');

            $i = 0;
            foreach ($bookable_services as $key => $service) {

                if(in_array(sanitize_title($service['name']),array_column($services,'service'))) {
                    //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);                 
                    $single_service_price = listeo_calculate_service_price($service, $multiply, $totalDays, $countable[$i] );
                    $sstax = (intval($service['tax']) / 100) * intval($single_service_price);
                   
                    $services_price +=  $single_service_price + $sstax;

                    $i++;
                }
            }
        }
        return $services_price;
    }

    public static function calculate_tax_services( $listing_id, $date_start, $date_end, $multiply, $services, $totalPrice = 1, $totalDays = 1 ) {
        // get all special prices between two dates from listeo settings special prices
        $special_prices_results = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'special_price' ) );

        // prepare special prices to nice array
        foreach ($special_prices_results as $result)
        {
            $special_prices[ $result['date_start'] ] = $result['comment'];
        }

        // get normal prices from listeo listing settings
        $normal_price = (float) get_post_meta ( $listing_id, '_normal_price', true);
        $weekend_price = (float)  get_post_meta ( $listing_id, '_weekday_price', true);
        if(empty($weekend_price)){
            $weekend_price = $normal_price;
        }
        $reservation_price  =  (float) get_post_meta ( $listing_id, '_reservation_price', true);
        $_count_per_guest  = get_post_meta ( $listing_id, '_count_per_guest', true);
        $services_price = 0;
        $listing_type = get_post_meta( $listing_id, '_listing_type', true);

        $firstDay = new DateTime( $date_start );
        $lastDay = new DateTime( $date_end . '23:59:59') ;

        $days_between = $lastDay->diff($firstDay)->format("%a");
        $days_count = ($days_between == 0) ? 1 : $days_between ;
        //fix for not calculating last day of leaving
        if ( $date_start != $date_end ) $lastDay -> modify('-1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod( $firstDay, $interval, $lastDay );

        // at start we have reservation price
        $price = 0;
        foreach ( $period as $current_day ) {

            // get current date in sql format
            $date = $current_day->format("Y-m-d 00:00:00");
            $day = $current_day->format("N");

            if ( isset( $special_prices[$date] ) )
            {
                $price += $special_prices[$date];
            }
            else {
                $start_of_week = intval( get_option( 'start_of_week' ) ); // 0 - sunday, 1- monday
                // when we have weekends
                if($start_of_week == 0 ) {
                    if ( isset( $weekend_price ) && $day == 5 || $day == 6) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                } else {
                    if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                }

            }

        }
        if($_count_per_guest){
            $price = $price * (int) $multiply;
        }
        $services_price = 0;
        if(isset($services) && !empty($services)){
            $bookable_services = listeo_get_bookable_services($listing_id);
            $countable = array_column($services,'value');

            $i = 0;
            foreach ($bookable_services as $key => $service) {

                if(in_array(sanitize_title($service['name']),array_column($services,'service'))) {
                    //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);                 
                    $single_service_price = listeo_calculate_service_price($service, $multiply, $totalDays, $countable[$i] );
                    $sstax = (intval($service['tax']) / 100) * intval($single_service_price);
                    $services_price += $sstax;

                    $i++;
                }
            }
        }
        return $services_price;
    }

    /**
     * Calculate only normal price
     *
     * @param  number $listing_id post id of current listing
     * @param  date  $date_start since we checking
     * @param  date  $date_end to we checking
     *
     * @return number $price of all booking at all
     */
    public static function calculate_normal_price( $listing_id, $date_start, $date_end, $multiply, $services, $totalPrice = 1, $totalDays = 1 ) {
        // get all special prices between two dates from listeo settings special prices
        $special_prices_results = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'special_price' ) );

        // prepare special prices to nice array
        foreach ($special_prices_results as $result)
        {
            $special_prices[ $result['date_start'] ] = $result['comment'];
        }


        // get normal prices from listeo listing settings
        $normal_price = (float) get_post_meta ( $listing_id, '_normal_price', true);
        $weekend_price = (float)  get_post_meta ( $listing_id, '_weekday_price', true);
        if(empty($weekend_price)){
            $weekend_price = $normal_price;
        }
        $reservation_price  =  (float) get_post_meta ( $listing_id, '_reservation_price', true);
        $_count_per_guest  = get_post_meta ( $listing_id, '_count_per_guest', true);
        $services_price = 0;
        $listing_type = get_post_meta( $listing_id, '_listing_type', true);
        $firstDay = new DateTime( $date_start );
        $lastDay = new DateTime( $date_end . '23:59:59') ;

        $days_between = $lastDay->diff($firstDay)->format("%a");
        $days_count = ($days_between == 0) ? 1 : $days_between ;
        //fix for not calculating last day of leaving
        if ( $date_start != $date_end ) $lastDay -> modify('-1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod( $firstDay, $interval, $lastDay );

        // at start we have reservation price
        $price = 0;
        foreach ( $period as $current_day ) {

            // get current date in sql format
            $date = $current_day->format("Y-m-d 00:00:00");
            $day = $current_day->format("N");

            if ( isset( $special_prices[$date] ) )
            {
                $price += $special_prices[$date];
            }
            else {
                $start_of_week = intval( get_option( 'start_of_week' ) ); // 0 - sunday, 1- monday
                // when we have weekends
                if($start_of_week == 0 ) {
                    if ( isset( $weekend_price ) && $day == 5 || $day == 6) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                } else {
                    if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                        $price += $weekend_price;
                    }  else { $price += $normal_price; }
                }

            }

        }
        if($_count_per_guest){
            $price = $price * (int) $multiply;
        }

        if($totalPrice == 1){
            $totalPrice =  $_SESSION['test'];
        }

        return round(intval($totalPrice),2);
    }


    /**
     * Get all reservation of one listing
     *
     * @param  number $listing_id post id of current listing
     * @param  array $dates
     *
     */
    public static function get_reservations( $listing_id, $dates ) {

        // delecting old reservations
        self :: delete_bookings ( array(
            'listing_id' => $listing_id,
            'owner_id' => get_current_user_id(),
            'type' => 'reservation') );

        // update by new one reservations
        foreach ( $dates as $date) {

            self :: insert_booking( array(
                'listing_id' => $listing_id,
                'type' => 'reservation',
                'owner_id' => get_current_user_id(),
                'date_start' => $date,
                'date_end' => $date,
                'comment' =>  __('owner reservations', 'listeo_core'),
                'order_id' => NULL,
                'status' => NULL
            ));

        }

    }

    public static function get_slots_from_meta( $listing_id ) {

        $_slots = get_post_meta( $listing_id, '_slots', true );

        // when we dont have slots
        if ( strpos( $_slots, '-' ) == false ) return false;

        // when we have slots
        $_slots = json_decode( $_slots );
        return $_slots;
    }

    /**
     * User booking shortcode
     *
     *
     */
    // BOOKING DISABLED
    public static function listeo_core_booking( ) {

        if(!isset($_POST['value'])){
            global $wpdb;
            $_listing_id = intval($_SESSION['_listingid']);
            $post_author_id = get_post_field( 'post_author', $post_id );
            $idd = $wpdb->get_results("SELECT post_author FROM wp_posts WHERE ID = $_listing_id ");
            $idd = json_encode($idd);
            $str = $idd;
            $str = strstr($str, ':');
            $str2 = substr($str, 2);
            $str2 = substr($str2, 0, -3);
            $_owner_id = intval($str2);
            $dsa = array(
                'bookings_author' => get_current_user_id(),
                'owner_id' => $_owner_id,
                'listing_id' => $_listing_id,
                'disabled' => 'true'

            );
            ?><script>
                setTimeout(() => {
                    jQuery('.listing-item-container').parent().css('display','none');
                    localStorage.noBooking = 'true';
                    jQuery('#booking_message').prop('required',true);
                    jQuery('#booking_message').attr('placeholder','En kort melding angående henvendelsen eller noe du lurer på');
                }, 100);
            </script><?php
            $_POST['value'] = json_encode($dsa);
        }
        // here we adding booking into database



        if ( isset($_POST['confirmed']) )
        {
            $_user_id = get_current_user_id();

            $data = json_decode( wp_unslash(htmlspecialchars_decode(wp_unslash($_POST['value']))), true );

            $error = false;

            $services = (isset($data['services'])) ? $data['services'] : false ;
            $comment_services = false;
            if(!empty($services)){
                $currency_abbr = get_option( 'listeo_currency' );
                $currency_postion = get_option( 'listeo_currency_postion' );
                $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
                //$comment_services = '<ul>';
                $comment_services = array();
                $bookable_services = listeo_get_bookable_services( $data['listing_id'] );

                $firstDay = new DateTime( $data['date_start'] );
                $lastDay = new DateTime( $data['date_start'] . '23:59:59') ;

                $days_between = $lastDay->diff($firstDay)->format("%a");
                $days_count = ($days_between == 0) ? 1 : $days_between ;

                //since 1.3 change comment_service to json
                $countable = array_column($services,'value');
                if(isset($data['adults'])){
                    $guests = $data['adults'];
                } else if(isset($data['tickets'])){
                    $guests = $data['tickets'];
                } else {
                    $guests = 1;
                }
                $i = 0;
                foreach ($bookable_services as $key => $service) {

                    if(in_array(sanitize_title($service['name']),array_column($services,'service'))) {
                        //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);
                        $comment_services[] =  array(
                            'service' => $service,
                            'guests' => $guests,
                            'days' => $days_count,
                            'countable' =>  $countable[$i],
                            'price' => listeo_calculate_service_price($service, $data['adults'], $totalDays, $countable[$i] )
                        );

                        $i++;
                    }


                }

                // $i++;
                // if(in_array('service_'.$i,$services)) {
                //     $comment_services .= '<li>'.$service['name'].'<span class="services-list-price-tag">';
                //     if(empty($service['price']) || $service['price'] == 0) {
                //         $comment_services .= esc_html__('Free','listeo_core');
                //     } else {
                //         if($currency_postion == 'before') {  $comment_services .= $currency_symbol.' '; }
                //         $comment_services .= $service['price'];
                //         if($currency_postion == 'after') { $comment_services .= ' '.$currency_symbol; }
                //     }
                //     $comment_services .= '</span></li>';

                // }

                //$comment_services .= '</ul>';
            }
            $listing_meta = get_post_meta ( $data['listing_id'], '', true );
            // detect if website was refreshed
            $instant_booking = get_post_meta(  $data['listing_id'], '_instant_booking', true );


            // if ( get_transient('listeo_last_booking'.$_user_id) == $data['listing_id'] . ' ' . $data['date_start']. ' ' . $data['date_end'] )
            // {
            //     $template_loader = new Listeo_Core_Template_Loader;

            //     $template_loader->set_template_data( 
            //         array( 
            //             'error' => true,
            //             'message' => __('Sorry, it looks like you\'ve already made that reservation', 'listeo_core')
            //         ) )->get_template_part( 'booking-success' ); 

            //     return;
            // }

            set_transient( 'listeo_last_booking'.$_user_id, $data['listing_id'] . ' ' . $data['date_start']. ' ' . $data['date_end'], 60 * 15 );

            // because we have to be sure about listing type
            $listing_meta = get_post_meta ( $data['listing_id'], '', true );

            $listing_owner = get_post_field( 'post_author', $data['listing_id'] );

            $billing_address_1 = (isset($_POST['billing_address_1'])) ? $_POST['billing_address_1'] : false ;
            $billing_postcode = (isset($_POST['billing_postcode'])) ? $_POST['billing_postcode'] : false ;
            $billing_city = (isset($_POST['billing_city'])) ? $_POST['billing_city'] : false ;
            $billing_country = (isset($_POST['billing_country'])) ? $_POST['billing_country'] : false ;

            switch ( $listing_meta['_listing_type'][0] )
            {
                case 'event' :

                    $comment= array(
                        'first_name'    => $_POST['firstname'],
                        'last_name'     => $_POST['lastname'],
                        'email'         => $_POST['email'],
                        'phone'         => $_POST['phone'],
                        'message'       => $_POST['message'],
                        'tickets'       => $data['tickets'],
                        'service'       => $comment_services,
                        'billing_address_1' => $billing_address_1,
                        'billing_postcode'  => $billing_postcode,
                        'billing_city'      => $billing_city,
                        'billing_country'   => $billing_country,
                        'total_tax'         => $_POST['taxPrice']
                    );

                    $booking_id = self :: insert_booking ( array (
                        'owner_id'      => $listing_owner,
                        'listing_id'    => $data['listing_id'],
                        'date_start'    => $data['date_start'],
                        'date_end'      => $data['date_start'],
                        'comment'       =>  json_encode ( $comment ),
                        'type'          =>  'reservation',
                        'price'         => self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'],$data['tickets'], $services ),
                    ));

                    $already_sold_tickets = (int) get_post_meta($data['listing_id'],'_event_tickets_sold',true);
                    $sold_now = $already_sold_tickets + $data['tickets'];
                    update_post_meta($data['listing_id'],'_event_tickets_sold',$sold_now);

                    $status = apply_filters( 'listeo_event_default_status', 'waiting');
                    if($instant_booking == 'check_on' || $instant_booking == 'on') { $status = 'confirmed'; }

                    $changed_status = self :: set_booking_status ( $booking_id, $status );

                    break;

                case 'rental' :

                    // get default status
                    $status = apply_filters( 'listeo_rental_default_status', 'waiting');

                    // count free places
                    $free_places = self :: count_free_places( $data['listing_id'], $data['date_start'], $data['date_end'] );

                    if ( $free_places > 0 )
                    {

                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest" , true );
                        //check count_per_guest

                        if($count_per_guest){

                            $multiply = 1;
                            if(isset($data['adults'])) $multiply = $data['adults'];

                            $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services   );
                        } else {
                            $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services  );
                        }

                        $booking_id = self :: insert_booking ( array (
                            'owner_id' => $listing_owner,
                            'listing_id' => $data['listing_id'],
                            'date_start' => $data['date_start'],
                            'date_end' => $data['date_end'],
                            'comment' =>  json_encode ( array(
                                'first_name' => $_POST['firstname'],
                                'last_name' => $_POST['lastname'],
                                'email' => $_POST['email'],
                                'phone' => $_POST['phone'],
                                'message'       => $_POST['message'],
                                //'childrens' => $data['childrens'],
                                'adults' => $data['adults'],
                                'service'       => $comment_services,
                                'billing_address_1' => $billing_address_1,
                                'billing_postcode'  => $billing_postcode,
                                'billing_city'      => $billing_city,
                                'billing_country'   => $billing_country
                                // 'tickets' => $data['tickets']
                            )),
                            'type' =>  'reservation',
                            'price' => $price,
                        ));

                        $status = apply_filters( 'listeo_event_default_status', 'waiting');
                        if($instant_booking == 'check_on' || $instant_booking == 'on') { $status = 'confirmed'; }
                        $changed_status = self :: set_booking_status ( $booking_id, $status );

                    } else
                    {

                        $error = true;
                        $message = __('Unfortunately those dates are not available anymore.', 'listeo_core');

                    }

                    break;

                case 'service' :
                    $status = apply_filters( 'listeo_service_default_status', 'waiting');
                    if($instant_booking == 'check_on' || $instant_booking == 'on') { $status = 'pending'; }
                    // time picker booking
                    if ( ! isset( $data['slot'] ) )
                    {
                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest" , true );
                        //check count_per_guest

                        if($count_per_guest){

                            $multiply = 1;
                            if(isset($data['adults'])) $multiply = $data['adults'];

                            $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply , $services  );
                        } else {
                            $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'] ,1, $services );
                        }

                        $hour_end = ( isset($data['_hour_end']) && !empty($data['_hour_end']) ) ? $data['_hour_end'] : $data['_hour'] ;

                        $booking_id = self :: insert_booking ( array (
                            'owner_id' => $listing_owner,
                            'listing_id' => $data['listing_id'],
                            'date_start' => $data['date_start'] . ' ' . $data['_hour'] . ':00',
                            'date_end' => $data['date_end'] . ' ' . $hour_end . ':00',
                            'comment' =>  json_encode ( array( 'first_name' => $_POST['firstname'],
                                'last_name' => $_POST['lastname'],
                                'email' => $_POST['email'],
                                'phone' => $_POST['phone'],
                                'adults' => $data['adults'],
                                'message'       => $_POST['message'],
                                'service'       => $comment_services,
                                'billing_address_1' => $billing_address_1,
                                'billing_postcode'  => $billing_postcode,
                                'billing_city'      => $billing_city,
                                'billing_country'   => $billing_country,
                                'total_tax'         => $_POST['taxPrice']

                            )),
                            'type' =>  'reservation',
                            'price' => $price,
                        ));

                        $changed_status = self :: set_booking_status ( $booking_id, $status );

                    } else {

                        // here when we have enabled slots

                        $free_places = self :: count_free_places( $data['listing_id'], $data['date_start'], $data['date_end'], $data['slot'] );

                        if ( $free_places > 0 )
                        {

                            $slot = json_decode( wp_unslash($data['slot']) );

                            // converent hours to mysql format
                            $hours = explode( ' - ', $slot[0] );
                            $hour_start = date( "H:i:s", strtotime( $hours[0] ) );
                            $hour_end = date( "H:i:s", strtotime( $hours[1] ) );

                            $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest" , true );
                            //check count_per_guest
                            $services = (isset($data['services'])) ? $data['services'] : false ;
                            if($count_per_guest){

                                $multiply = 1;
                                if(isset($data['adults'])) $multiply = $data['adults'];

                                $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services  );
                            } else {
                                $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services );
                            }

                            $booking_id = self :: insert_booking ( array (
                                'owner_id' => $listing_owner,
                                'listing_id' => $data['listing_id'],
                                'date_start' => $data['date_start'] . ' ' . $hour_start,
                                'date_end' => $data['date_end'] . ' ' . $hour_end,
                                'comment' =>  json_encode ( array( 'first_name' => $_POST['firstname'],
                                    'last_name' => $_POST['lastname'],
                                    'email' => $_POST['email'],
                                    'phone' => $_POST['phone'],
                                    //'childrens' => $data['childrens'],
                                    'adults' => $data['adults'],
                                    'message'       => $_POST['message'],
                                    'service'       => $comment_services,
                                    'billing_address_1' => $billing_address_1,
                                    'billing_postcode'  => $billing_postcode,
                                    'billing_city'      => $billing_city,
                                    'billing_country'   => $billing_country,
                                    'total_tax'         => $_POST['taxPrice']

                                )),
                                'type' =>  'reservation',
                                'price' => $price,
                            ));
                            //discount tyoe for mottate
                            update_post_meta($booking_id, 'discount-type',  $_POST['discount-type']);
                            $status = apply_filters( 'listeo_service_slots_default_status', 'waiting');
                            if($instant_booking == 'check_on' || $instant_booking == 'on') { $status = 'pending'; }

                            $changed_status = self :: set_booking_status ( $booking_id, $status );
                            
                        } else
                        {

                            $error = true;
                            $message = __('Those dates are not available.', 'listeo_core');

                        }

                    }

                    break;
            }

            // when we have database problem with statuses
            if ( ! isset($changed_status) )
            {
                $message = __( 'We have some technical problem, please try again later or contact administrator.', 'listeo_core' );
                $error = true;
            }

            switch ( $status )  {

                case 'waiting' :

                    $message = esc_html__( 'Bestillingen din venter på bekreftelse', 'listeo_core' );
					/* $submessage = ' '; */
                    break;

                case 'confirmed' :

                    $message = esc_html__( 'Du videre sendes til betaling', 'listeo_core' );
					/* $submessage = ' '; */
                    break;
				 case 'pending' :

                    $message = esc_html__( 'Du videre sendes til betaling', 'listeo_core' );
                    /* $submessage = esc_html__( 'Payment Pending.', 'listeo_core' ); */

                    break;

                case 'cancelled' :

                    $message = esc_html__( 'Your booking was cancelled', 'listeo_core' );
					/* $submessage = ' ';	 */
                    break;
            }

			
            $template_loader = new Listeo_Core_Template_Loader;
            if(isset($booking_id)){
                $booking_data =  self :: get_booking($booking_id);
                $order_id = $booking_data['order_id'];
                $order_id = (isset($booking_data['order_id'])) ? $booking_data['order_id'] : false ;
            }
            $template_loader->set_template_data(
                array(
                    'status' => $status,
                    'message' => $message,
                    'submessage' => $submessage,
                    'error' => $error,
                    'booking_id' => (isset($booking_id)) ? $booking_id : 0,
                    'order_id' => (isset($order_id)) ? $order_id : 0,
                ) )->get_template_part( 'booking-success' );

            return;
        }

        // not confirmed yet


        // extra services
        $data = json_decode( wp_unslash( $_POST['value'] ), true );

        if(isset($data['services'])){
            $services =  $data['services'];
        } else {
            $services = false;
        }

        // for slots get hours
        if ( isset( $data['slot']) )
        {
            $slot = json_decode( wp_unslash( $data['slot'] ) );
            $hour = $slot[0];

        } else if ( isset( $data['_hour'] ) ) {
            $hour = $data['_hour'];
            if(isset($data['_hour_end'])) {
                $hour_end = $data['_hour_end'];
            }
        }

        $template_loader = new Listeo_Core_Template_Loader;

        // prepare some data to template
        $data['submitteddata'] = htmlspecialchars($_POST['value']);

        //check listin type
        $count_per_guest = get_post_meta($data['listing_id'],"_count_per_guest",true);
        //check count_per_guest

        //  if($count_per_guest || $data['listing_type'] == 'event' ){

        $multiply = 1;
        if(isset($data['adults'])) $multiply = $data['adults'];
        if(isset($data['tickets'])) $multiply = $data['tickets'];

        $data['price'] = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services);

        // } else {

        //     $data['price'] = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services  );
        // }

        if(isset($hour)){
            $data['_hour'] = $hour;
        }
        if(isset($hour_end)){
            $data['_hour_end'] = $hour_end;
        }

        $data['normal_price'] = self :: calculate_normal_price( $data['listing_id'],  $data['date_start'], $data['date_end'],$multiply, $services);
        $data['services_price'] = self :: calculate_price_services($data['listing_id'],  $data['date_start'], $data['date_end'],$multiply, $services, $totalPrice = 1, $totalDays = 1 );

        $discount_percentage = get_post_meta($_POST['listing_id'],$_POST['discount'],true);
        $percentInDecimal = intval($discount_percentage) / 100;
        if(is_numeric($discount_percentage)){
            $data['normal_price'] = $data['normal_price'] - ($data['normal_price'] * $percentInDecimal);
        }

        $taxPercentage = get_post_meta ( $data['listing_id'], '_tax', true);
        $tax = ($taxPercentage / 100) * ceil($data['normal_price']) ;
        $data['taxprice'] = ceil($tax);
        $template_loader->set_template_data( $data )->get_template_part( 'booking' );


        // if slots are sended change them into good form
        if ( isset( $data['slot'] ) ) {

            // converent hours to mysql format
            $hours = explode( ' - ', $slot[0] );
            $hour_start = date( "H:i:s", strtotime( $hours[0] ) );
            $hour_end = date( "H:i:s", strtotime( $hours[1] ) );

            // add hours to dates
            $data['date_start'] .= ' ' . $hour_start;
            $data['date_end'] .= ' ' . $hour_end;

        } else if ( isset( $data['_hour'] ) ) {

            // when we dealing with normal hour from input we have to add second to make it real date format
            $hour_start = date( "H:i:s", strtotime( $hour ) );
            $data['date_start'] .= ' ' . $hour . ':00';
            $data['date_end'] .= ' ' . $hour . ':00';

        }

        // make temp reservation for short time
        //self :: save_temp_reservation( $data );

    }

    /**
     * Save temp reservation
     *
     * @param array $atts with 'date_start', 'date_end' and 'listing_id'
     *
     * @return array $temp_reservations with all reservations for this id, also expired if will be
     *
     */
    public static function save_temp_reservation( $atts ) {

        // get temp reservations for current listing
        $temp_reservations = get_transient( $atts['listing_id'] );

        // get current date + time setted as temp booking time
        $expired_date = date( 'Y-m-d H:i:s', strtotime( '+' . apply_filters( 'listeo_expiration_booking_minutes', 15) . ' minutes', time() ) );

        // set array for current temp reservations
        $reservation_data = array(
            'user_id' => get_current_user_id(),
            'date_start' => $atts['date_start'],
            'date_end' => $atts['date_end'],
            'expired_date' => $expired_date
        );

        // add reservation to end of array with all reservations for this listing
        $temp_reservations[] = $reservation_data;

        // set transistence on time setted as temp booking time
        set_transient( $atts['listing_id'], $temp_reservations, apply_filters( 'listeo_expiration_minutes', 15) * 60 );

        // return all temp reservations for this id
        return $temp_reservations;

    }

    /**
     * Temp reservation aval
     *
     * @param array $atts with 'date_start', 'date_end' and 'listing_id'
     *
     * @return number $reservation_amount of all temp reservations form tranistenc fittid this id and time
     *
     */
    public static function temp_reservation_aval( $args ) {

        // get temp reservations for current listing
        $temp_reservations = get_transient( $args['listing_id'] );

        // loop where we will count only reservations fitting to time and user id
        $reservation_amount = 0;

        if ( is_array($temp_reservations) )
        {
            foreach ( $temp_reservations as $reservation) {

                // if user id is this same then not count
                if ( $reservation['user_id'] == get_current_user_id() )
                {
                    continue;
                }

                // when its too old and expired also not count, it will be deleted automaticly with wordpress transistend
                if ( date( 'Y-m-d H:i:s', strtotime( $reservation['expired_date'] ) ) < date( 'Y-m-d H:i:s', time() ) )
                {
                    continue;
                }

                // now we converenting strings into dates
                $args['date_start'] = date( 'Y-m-d H:i:s', strtotime( $args['date_start']  ) );
                $args['date_end'] = date( 'Y-m-d H:i:s', strtotime( $args['date_end']  ) );
                $reservations['date_start'] = date( 'Y-m-d H:i:s', strtotime( $reservations['date_start']  ) );
                $reservations['date_end'] = date( 'Y-m-d H:i:s', strtotime( $reservations['date_end']  ) );

                // and compating dates
                if ( ! ( ($args['date_start'] >= $reservation['date_start'] AND $args['date_start'] <= $reservation['date_end'])
                    OR ($args['date_end'] >= $reservation['date_start'] AND $args['date_end'] <= $reservation['date_end'])
                    OR ($reservation['date_start'] >= $args['date_start'] AND $reservation['date_end'] <= $args['date_end']) ) )
                {
                    continue;
                }

                $reservation_amount++;

            }
        }

        return $reservation_amount;

    }


    /**
     * Owner booking menage shortcode
     *
     *
     */
    public static function listeo_core_dashboard_bookings( ) {

        $users = new Listeo_Core_Users;

        $listings = $users->get_agent_listings('',0,-1);
        $args = array (
            'owner_id' => get_current_user_id(),
            'type' => 'reservation',

        );

        $limit =  get_option('posts_per_page');
        $pages = '';
        if(isset($_GET['status']) ){
            $booking_max = listeo_count_bookings(get_current_user_id(),$_GET['status']);
            $pages = ceil($booking_max/$limit);
            $args['status'] = $_GET['status'];
        }
        $bookings = self :: get_newest_bookings($args,$limit );

        $template_loader = new Listeo_Core_Template_Loader;
        $template_loader->set_template_data(
            array(
                'message' => '',
                'bookings' => $bookings,
                'pages' => $pages,
                'listings' => $listings->posts,
            ) )->get_template_part( 'dashboard-bookings' );

        return;

    }

    public static function listeo_core_dashboard_my_bookings( ) {

        $users = new Listeo_Core_Users;

        $args = array (
            'bookings_author' => get_current_user_id(),
            'type' => 'reservation'
        );
        $limit =  get_option('posts_per_page');

        if(isset($_GET['status']) ){
            $args['status'] = $_GET['status'];
            $booking_max = listeo_count_my_bookings_with_status(get_current_user_id(),$_GET['status']);
            $pages = ceil($booking_max/$limit);
        }else{
            $booking_max = listeo_count_my_bookings(get_current_user_id());
            $pages = ceil($booking_max/$limit);
        }

        $bookings = self :: get_newest_my_bookings($args,$limit );

        $template_loader = new Listeo_Core_Template_Loader;
        $template_loader->set_template_data(
            array(
                'message' => '',
                'type'    => 'user_booking',
                'bookings' => $bookings,
                'pages' => $pages,
            ) )->get_template_part( 'dashboard-bookings' );

        return;

    }
	public static function setbookingstatus($order_id) {

		$order = new WC_Order( $order_id );
		 $booking_id = get_post_meta( $order_id, 'booking_id', true ); 
			if($booking_id){
				self :: set_booking_status( $booking_id, 'paid' );
			}
	}
    /**
     * Booking Paid
     *
     * @param number $order_id with id of order
     *
     */
    public static function booking_paid( $order_id ) {

        $order = wc_get_order( $order_id );

        $booking_id = get_post_meta( $order_id, 'booking_id', true );
        if($booking_id){
            self :: set_booking_status( $booking_id, 'paid' );
        }
    }

    public function listeo_wc_pre_get_posts_query( $q ) {

        $tax_query = (array) $q->get( 'tax_query' );

        $tax_query[] = array(
            'taxonomy' => 'product_type',
            'field' => 'slug',
            'terms' => array( 'listing_booking' ), //
            'operator' => 'NOT IN'
        );


        $q->set( 'tax_query', $tax_query );

    }

    public static function get_booking($id){
        global $wpdb;
        return $wpdb -> get_row( 'SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar` WHERE `id`=' . esc_sql( $id ), 'ARRAY_A' );
    }

    public function check_for_expired_booking(){

        global $wpdb;
        $date_format = get_option('date_format');
        // Change status to expired
        $table_name = $wpdb->prefix . 'bookings_calendar';
        $bookings_ids = $wpdb->get_col( $wpdb->prepare( "
            SELECT ID FROM {$table_name}
            WHERE status = 'waiting'
            AND expiring > %s
            
        ", strtotime(date( $date_format, current_time( 'timestamp' ) ) ) ) );

        if ( $bookings_ids ) {
            foreach ( $bookings_ids as $booking ) {
                // delecting old reservations
                self :: delete_bookings ( array(
                        'ID' => $booking )
                );
                do_action('listeo_delete_single_booking',$booking);
            }
        }
    }
}

?>