<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Listeo_Core_Bookings class.
 */
require plugin_dir_path( __FILE__ ).'vendor/autoload.php';
use RRule\RRule;
class Gibbs_Booking_Calendar {
    


    public function __construct() {

        add_action('wp_loaded', array($this, 'gibbs_override'));
        // for booking widget
        add_action('wp_ajax_check_avaliabity_custom', array($this, 'ajax_check_avaliabity'));
        add_action('wp_ajax_nopriv_check_avaliabity_custom', array($this, 'ajax_check_avaliabity'));  

        add_action('wp_ajax_calculate_price', array($this, 'ajax_calculate_price'));
        add_action('wp_ajax_nopriv_calculate_price', array($this, 'ajax_calculate_price'));

        add_action('wp_ajax_gibbs_get_hours', array($this, 'ajax_calculate_hours'));
        add_action('wp_ajax_nopriv_gibbs_get_hours', array($this, 'ajax_calculate_hours'));

        add_action('wp_ajax_listeo_validate_coupon', array($this, 'ajax_validate_coupon'));
        add_action('wp_ajax_nopriv_listeo_validate_coupon', array($this, 'ajax_validate_coupon'));

        add_action('wp_ajax_check_cal_availability', array($this, 'check_cal_availability'));
        add_action('wp_ajax_nopriv_check_cal_availability', array($this, 'check_cal_availability'));
        
        
        add_action('wp_ajax_listeo_calculate_booking_form_price', array($this, 'ajax_calculate_booking_form_price'));
        add_action('wp_ajax_nopriv_listeo_calculate_booking_form_price', array($this, 'ajax_calculate_booking_form_price'));

        

        add_action('wp_ajax_update_slots', array($this, 'ajax_update_slots'));
        add_action('wp_ajax_nopriv_update_slots', array($this, 'ajax_update_slots'));       
        
       // add_action('wp_ajax_listeo_apply_coupon', array($this, 'ajax_widget_apply_coupon'));
       // add_action('wp_ajax_nopriv_listeo_apply_coupon', array($this, 'ajax_widget_apply_coupon'));  

        // for bookings dashboard
        add_action('wp_ajax_listeo_bookings_manage', array($this, 'ajax_listeo_bookings_manage'));
        add_action('wp_ajax_nopriv_listeo_bookings_manage', array($this, 'ajax_listeo_bookings_manage'));  
        add_action('wp_ajax_listeo_bookings_renew_booking', array($this, 'ajax_listeo_bookings_renew_booking'));

        // booking page shortcode and post handling
        //remove_shortcode('listeo_booking_confirmation');
       
        add_shortcode( 'listeo_bookings', array( $this, 'listeo_core_dashboard_bookings' ) );
        add_shortcode( 'listeo_my_bookings', array( $this, 'listeo_core_dashboard_my_bookings' ) );

        // when woocoommerce is paid trigger function to change booking status
        add_action( 'woocommerce_order_status_completed', array( $this, 'booking_paid' ), 9, 3 );
        add_action( 'woocommerce_payment_complete', array( $this, 'booking_paid' ),  9, 3 ); 
        add_action( 'woocommerce_payment_complete_order_status', array( $this, 'booking_paid_processing' ),  9, 3  ); 
        // remove listeo booking products from shop
        add_action( 'woocommerce_product_query', array($this,'listeo_wc_pre_get_posts_query' ));  

        add_action( 'listeo_core_check_for_expired_bookings', array( $this, 'check_for_expired_booking' ) );

        add_action( 'woocommerce_before_pay_action', array( $this, 'before_pay_action' ), 101 );

       

        
        
    }
    public function get_bk_template(){
        ob_start();
        $templatee = str_replace("/includes","",plugin_dir_path( __FILE__ ));
        echo $templatee."templates/booking-old.php";
        die;
        return ob_get_clean();
        exit;
    }
    public function encryptString($string)
    {
        $encryption_iv = '134245454576879980554';
        $encryption_key = 'gibbssk';
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering); 

        return $encryption = openssl_encrypt($string, $ciphering, $encryption_key, 0, $encryption_iv); die;

       
    }
    public function decryptString($encryption)
    {
        $encryption_iv = '134245454576879980554';
        $encryption_key = 'gibbssk';
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);


        // Using openssl_decrypt() function to decrypt the data
        return $decryption = openssl_decrypt($encryption, $ciphering, $encryption_key, 0, $encryption_iv);

       
    }
    public function gibbs_override()
    {
        remove_shortcode('listeo_booking_confirmation');
        add_shortcode( 'listeo_booking_confirmation', array( $this, 'listeo_core_booking' ) );
    }
    function custom_woocommerce_auto_complete_order2($order_id){
      die("dfdfk");
    }

    public function get_dates_slots($slots,$cur_day,$gen_date){

        $inc = $cur_day;

        $dates = array();
        //echo "<pre>"; print_r($slots); die;

        for($ii = 1;$ii < 30; $ii++){


            $slot = $slots[$inc][0];
            $cur_slot_cs = explode("|", $slot);
            $from_arr = explode("-", $cur_slot_cs[0]);
            $from_time = trim($from_arr[0]);
            $to_time = trim($from_arr[1]);

            

            if($ii == 1){
                $from_time = "00:00";
            }
            $from_date = $gen_date->format('Y-m-d')." ".$from_time;
            $to_date = $gen_date->format('Y-m-d')." ".$to_time;

            if(($to_time == "23:59" || $to_time == "24:00") && $from_time == "00:00"){

                $dates[$ii]["from_date"] = $from_date;
                $dates[$ii]["to_date"] = $to_date;

                $gen_date = date('Y-m-d', strtotime($from_date . ' +1 day')); 
                $gen_date = new DateTime($gen_date);
 
            }else{

                if($from_time == "00:00"){
                    $dates[$ii]["from_date"] = $from_date;
                    $dates[$ii]["to_date"] = $to_date;
                }
                
               
                break;
            }
            if($inc == 6){
                $inc = -1;
            }
            $inc++;

        }
        return $dates;
    }

    public function checkBooking($date_startt,$date_enddd,$listing_id){
        $results = self :: get_bookings( $date_startt, $date_enddd, array( 'listing_id' => $listing_id, 'type' => 'reservation' ) );

        $earliestDate = "";

        if(!empty($results)){
            $dateStarts = array_column($results, 'date_start');

            // Find the earliest date
            $earliestDate = min($dateStarts);
        }
        return $earliestDate;
    }
    public function ajax_calculate_hours()
    {
        $listing_id = $_POST['listing_id'];
        $_min_hours = get_post_meta($listing_id, '_min_hours', true);
        $slots_old = get_post_meta($listing_id, '_slots', true);
        $slots_old = json_decode($slots_old, true);
        
        $slots = array();
        foreach($slots_old as $day=>$slot)
        {
            $new_d = null;
            if($day==0)
            {
                $new_d = 1;
            }
            elseif($day==1)
            {
                $new_d = 2;
            }
            elseif($day==2)
            {
                $new_d = 3;
            }
            elseif($day==3)
            {
                $new_d = 4;
            }
            elseif($day==4)
            {
                $new_d = 5;
            }
            elseif($day==5)
            {
                $new_d = 6;
            }
            elseif($day==6)
            {
                $new_d = 0;
            }
            $slots[$new_d] = $slot;
        }
        


        $start = $_POST['date_start'];
        $gen_date = new DateTime($_POST['date_start']);
        $st = new DateTime($start);
        $cur_day = $st->format("w");

        $get_dates_slots = self::get_dates_slots($slots,$cur_day,$gen_date);
        $cur_slot = $slots[$cur_day];
        
        $cur_slot_cs = explode("|", $cur_slot[0]);
        $slot_now = $cur_slot_cs[0];
        $free_places = $slot_now[1];
        $from_arr = explode("-", $slot_now);
        $from_time = trim($from_arr[0]);
        $to_time = trim($from_arr[1]);

        
        $sdcs = $gen_date->format('d-m-Y');
        if($to_time=='24:00' || $to_time=='23:59')
        {
            if(!empty($get_dates_slots)){
                $get_dates_slots = end($get_dates_slots);
                if(isset($get_dates_slots["to_date"])){

                    $to_gen_date = new DateTime($get_dates_slots["to_date"]);

                }
            }else{
                $to_gen_date = new DateTime($sdcs);
                $to_gen_date->modify('+1 month');
            }
            
            $to_gen_date_sdcs = $to_gen_date->format('d-m-Y');
            $to_fr = '23:59';
            $to_interval = new DateTime($to_gen_date_sdcs.' '.$to_fr);
            //echo $to_interval; die;
        }
        else
        {
            $to_fr = $to_time;
            $to_interval = new DateTime($sdcs.' '.$to_fr);
        }
        $from_interval = new DateTime($sdcs.' '.$from_time);

        $date_startt = date("Y-m-d H:i:s",strtotime($_POST["date_start"]));
        $date_enddd = $to_interval->format("Y-m-d H:i:s");

        $date_end_time = self::checkBooking($date_startt,$date_enddd,$listing_id);

        if($date_end_time != ""){
            $to_interval = new DateTime($date_end_time);
        }

        

        $last_min_hours = "";


        

        if($_min_hours != ""){
            $_min_hours = (int) $_min_hours;
            if($_min_hours > 0){

                $last_min_hours = date("Y-m-d H:i:s", strtotime('+'.$_min_hours.' hours', strtotime($gen_date->format('Y-m-d H:i:s')))); 

            }
        }


       /* if($last_min_hours != ""){

            $interval_min = DateInterval::createFromDateString('30 minutes');

            $from_interval_min = new DateTime($gen_date->format('Y-m-d H:i:s'));
            $to_interval_min = new DateTime($last_min_hours);

            $periods_min = new \DatePeriod($from_interval_min, $interval_min, $to_interval_min);

            echo "<pre>"; print_r($periods_min); die;

        }*/

       // echo "<pre>"; print_r($last_min_hours); die;


        $interval = DateInterval::createFromDateString('30 minutes');
        if($to_time=="24:00")
        {
            $new_dt = $st;
            $new_dt->modify('+1 day');
            $cur_day_dt = $new_dt->format("w");
            $cur_slot_dt = $slots[$cur_day_dt];
            if(!empty($cur_slot_dt))
            {
                $cur_slot_cs_dt = explode("|", $cur_slot_dt[0]);
                $slot_now_dt = $cur_slot_cs_dt[0];
                $from_arr_dt = explode("-", $slot_now_dt);
                $from_time_dt = trim($from_arr_dt[0]);
                $to_time_dt = trim($from_arr_dt[1]);
                if(($from_time_dt=="00:00") && ($to_time_dt=="24:00"))
                {
                    $new_dt = $new_dt->modify('+27 day');
                    $cur_day_dt = $new_dt->format("w");
                    $cur_slot_dt = $slots[$cur_day_dt];
                    $cur_slot_cs_dt = explode("|", $cur_slot_dt[0]);
                    $slot_now_dt = $cur_slot_cs_dt[0];
                    $from_arr_dt = explode("-", $slot_now_dt);
                    $from_time_dt = trim($from_arr_dt[0]);
                    $to_time_dt = trim($from_arr_dt[1]);
                    $sdcs_dt = $new_dt->format('d-m-Y');
                   // $to_interval = new DateTime($sdcs_dt.' '.$to_time_dt);
                    if(($from_time_dt=="00:00") && ($to_time_dt=="24:00"))
                    {
                        $new_dt = $new_dt->modify('+1 day');
                        $cur_day_dt = $new_dt->format("w");
                        $cur_slot_dt = $slots[$cur_day_dt];
                        $cur_slot_cs_dt = explode("|", $cur_slot_dt[0]);
                        $slot_now_dt = $cur_slot_cs_dt[0];
                        $from_arr_dt = explode("-", $slot_now_dt);
                        $from_time_dt = trim($from_arr_dt[0]);
                        $to_time_dt = trim($from_arr_dt[1]);
                        $sdcs_dt = $new_dt->format('d-m-Y');
                        $to_interval = new DateTime($sdcs_dt.' '.$to_time_dt);
                    }
                    else
                    {
                        $to_interval = new DateTime($sdcs_dt.' '.$to_time_dt);
                    }
                }
            }
            

        }
       // echo "<pre>"; print_r($from_interval);
       // echo "<pre>"; print_r($to_interval); die;
        $periods = new \DatePeriod($from_interval, $interval, $to_interval);
        $dts = array();
        $dts[] = "<option value=''>Velg</option>";
        foreach ( $periods as $current_day ) {
            $dpp = $gen_date->format("Y-m-d H:i");
            $cmpp = $current_day->format("Y-m-d H:i");
            $selb = '';
            $day_start = $st->format('j');
            $day_now = $current_day->format('j');
            //echo $cmpp;
            if($dpp==$cmpp)
            {
                $selb = "selected='selected'";
            }
            $cmp_new = $current_day->format("j.M. H:i");
       
            $dts[] = "<option ".$selb." value='".$cmpp."'>".$cmp_new."</option>";
        }


   
       
        $minutes_to_add = 30;
        $ds = array();
        $st->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $to_interval->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $fromdk = $gen_date->format('d-m-Y');
        $from_in_new = new DateTime($fromdk.' '.$from_time);
       // echo $from_in_new->format('d-m-Y H:i:s');
       // echo $to_interval->format('d-m-Y H:i:s');
       // die;
       $from_in_new->add(new DateInterval('PT' . $minutes_to_add . 'M'));
       $to_interval->sub(new DateInterval('PT' . $minutes_to_add . 'M'));
        $periods = new \DatePeriod($from_in_new, $interval, $to_interval->modify( '+1 second' ));
        $ds[] = "<option value=''>Velg</option>";

        //echo "<pre>"; print_r($periods); die;
        foreach ( $periods as $current_day ) {
            //$dpp = $st->format("d-m-Y H:i");
            if($current_day > $gen_date){
                $sm = '';
                $spk = $st->format("Y-m-d H:i");
                $cmp = $current_day->format("Y-m-d H:i");
                $day_start = $st->format('j');
                $day_now = $current_day->format('j');

                $now_datee = $current_day->format('Y-m-d H:i:s');

                if($last_min_hours != ""){

                    if(strtotime($now_datee) < strtotime($last_min_hours)){
                        continue;
                    }

                }

                


                /*
                if($spk==$cmp)
                {
                    $sm = 'selected="selected"';
                }
                if($day_now>$day_start)
                {
                    $ds[] = "<option value='".$st->format("Y-m-d 24:00")."'>".$st->format("j.M. 24:00")."</option>";
                }
                else
                {
                    if($spk<=$cmp)
                    {
                        */
                        $ds[] = "<option $sm value='".$cmp."'>".$current_day->format("j.M. H:i")."</option>";
                    /*}
                }
                */
            }
          
           
        }
        //echo "<pre>"; print_r($ds); die;
        if($to_time=="24:00")
        {
             $ds[] = "<option value='".$to_interval->format("Y-m-d 23:59")."'>".$to_interval->format("j.M. 23:59")."</option>";
        }
        $data['from'] = $dts;
        $data['to'] = $ds;

        ///echo "<pre>"; print_r($data); die;
        wp_send_json($data);
    }
    public function check_cal_availability()
    {
        wp_send_json($_POST);
    }
    public function before_pay_action(){
        
        global $wpdb;
        if(isset($_POST["order_id"])){
            $results_bookings = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '".$_POST["order_id"]."' ");

            if(isset($results_bookings[0]->id)){
                $date_start = $results_bookings[0]->date_start;
                $date_end = $results_bookings[0]->date_end;

                $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` where date_start = '".$date_start."' AND date_end = '".$date_end."' AND listing_id = ".$listing_id." AND  status = 'paid'");

                if(count($result) > 0){
                    wc_clear_notices();
                    wc_add_notice( __( 'Sorry, it looks like already made that reservation.', 'listeo_core' ), 'error' );
                    return;
                   
                    die;
                }


            }
        }
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
       // $end_date_time->add( DateInterval::createfromdatestring('-1 day') );
        $start_date_time->setTime( 12, 0 );
        $end_date_time->setTime( 12, 00, 00 );
      //  echo "<pre>"; print_r($end_date_time); die;

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
    public static function get_bookings( $date_start, $date_end, $args = '', $by = 'booking_date', $limit = '', $offset = '' ,$all = '', $listing_type = '')  {



        global $wpdb;
        $result = false;
        // if(strlen($date_start)<10){
        //     if($date_start) { $date_start = $date_start.' 00:00:00'; }
        //     if($date_end) { $date_end = $date_end.' 23:59:59'; }
        // }
        
        // setting dates to MySQL style
        $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );
        $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );
        
        //TODO to powinno byc tylko dla rentals!!
          // WP Kraken
        if($listing_type == 'rental'){   
            $booking_hours = self::wpk_change_booking_hours( $date_start, $date_end );
            $date_start = $booking_hours[ 'date_start' ];
            $date_end = $booking_hours[ 'date_end' ];
        }
        
        // filter by parameters from args
        $WHERE = '';
        $WHERE .= " AND NOT status='cancelled' AND NOT status='deleted' AND NOT status='expired' AND NOT status='pay_to_confirm' AND NOT status='waiting' AND NOT status='payment_failed' ";

        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value ) 
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){ 
                    $WHERE .= " AND ( (`$index` = 'confirmed') OR (`$index` = 'paid') )";
                }elseif ( $value == 'attention' ) { 
                    $WHERE .= " AND ( (`$index` = 'confirmed') OR (`$index` = 'waiting') OR (`$index` = 'approved') OR (`$index` = 'pay_to_confirm') )";
                } elseif ( $value == 'icalimports' ) { 

                } else {
                    $WHERE .= " AND (`$index` = '$value')";  
                } 
                if( $value == 'cancelled' || $value == 'special_price'){
                    $FILTER_CANCELLED = '';
                }
                if( $value == 'icalimports'){
                    $FILTER_CANCELLED = "AND NOT status='icalimports' AND NOT status='icalimports' ";
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
/*
                echo $date_start."<br>";
                echo $date_end."<br>";*/
                $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE ((' $date_start' >= `date_start` AND ' $date_start' < `date_end`) OR ('$date_end' > `date_start` AND '$date_end' <= `date_end`) OR (`date_start` >= ' $date_start' AND `date_end` < '$date_end')) $WHERE $FILTER $FILTER_CANCELLED $limit $offset";



                $result  = $wpdb -> get_results($sql , "ARRAY_A" );

               // echo "<pre>"; print_r($result); die;
               
             break;

                
            case 'created_date' :
                // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (' $date_start' <= `created` AND ' $date_end' >= `created`) AND (`status` IS NOT NULL)  $WHERE $FILTER_CANCELLED $limit $offset", "ARRAY_A" );
                break;
            
        }
       // echo "<pre>"; print_r($result); die;

      
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
        $slot_booking = "";
        $slot_booking_confirm = "";

         if($all == 'slot_booking'){
             $slot_booking = "true";
         }
         if($all == 'slot_booking_confirm'){
             $slot_booking_confirm = "true";
         }

        if ( $limit != '' ) $limit = " LIMIT " . esc_sql($limit);
        
        if ( is_numeric($offset)) $offset = " OFFSET " . esc_sql($offset);
        switch ($by)
        {

            case 'booking_date' :
                if($slot_booking == "true"){

                    //   $WHERE .= " AND ( (`status` = 'confirmed') OR (`status` = 'paid') )";

                    // $sqll = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (`date_start` >= '$date_start') $WHERE $FILTER $FILTER_CANCELLED $limit $offset";
                    $WHERE .= " AND ( (`status` = 'confirmed') OR (`status` = 'paid') OR (`status` = 'pay_to_confirm') OR (`status` = 'waiting') )";

                    $date_start_only = date("Y-m-d 00:00:00",strtotime($date_start));
                    $date_end_only = date("Y-m-d 23:59:59",strtotime($date_end));  

                   $sqll = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE ((`date_start` >= '$date_start_only' AND `date_start` <= '$date_end_only') || (`date_end` >= '$date_start_only' AND `date_end` <= '$date_end_only') || (`date_start` >= '$date_start_only' AND `date_end` <= '$date_end_only') || ('$date_start_only' >= `date_start` AND '$date_start_only' <= `date_end` ) ) $WHERE $FILTER $FILTER_CANCELLED AND `status` != 'payment_failed' $limit $offset";

                   // if($date_start == "2024-04-23 14:00:00"){
                        $results  = $wpdb -> get_results( $sqll, "ARRAY_A" );

                        //echo "<pre>"; print_r($results); die;

                        $data_ret = [];

                        foreach($results as $result){
                            // $db_start_date = "2024-04-23 10:00:00";
                            // $db_end_date = "2024-04-23 18:00:00";
                            // $date_start =  "2024-04-23 14:00:00";
                            // $date_end = "2024-04-23 15:00:00";

                            $db_start_date = $result["date_start"];
                            $db_end_date = $result["date_end"];

                            $db_start_date = strtotime(trim($db_start_date));
                            $db_end_date = strtotime(trim($db_end_date));
                            $date_start_in = strtotime(trim($date_start));
                            $date_end_in = strtotime(trim($date_end));

                            if (($date_start_in >= $db_start_date && $date_end_in < $db_end_date) || ($date_start_in >= $db_start_date && $date_start_in < $db_end_date) || ($date_start_in <= $db_start_date && $date_end_in > $db_start_date)) {
                                $data_ret[] = $result;
                            }
                           
                            // if (($db_start_date >= $date_start && $db_start_date < $date_end) || ($db_end_date > $date_start && $db_end_date <= $date_end) || ($db_start_date >= $date_start && $db_end_date <= $date_end)) {
                            //     echo "<pre>"; print_r($result); die;
                            // }
                        }

                        $result = $data_ret;
                        break;
                }else if($slot_booking_confirm == "true"){

                    $WHERE .= " AND ( (`status` = 'confirmed') OR (`status` = 'paid') OR (`status` = 'waiting') )";

                    $sqll = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE ((`date_start` >= '$date_start' AND `date_start` < '$date_end') || (`date_end` > '$date_start' AND `date_end` <= '$date_end') || (`date_start` >= '$date_start' AND `date_end` <= '$date_end')  ) $WHERE $FILTER $FILTER_CANCELLED AND `status` != 'payment_failed' $limit $offset";
                    $result  = $wpdb -> get_results( $sqll, "ARRAY_A" );
                    break;
                }else{

                    $sqll = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (('$date_start' = `date_start` AND '$date_end' = `date_end`)) $WHERE $FILTER $FILTER_CANCELLED AND `status` != 'payment_failed' $limit $offset";
                    $result  = $wpdb -> get_results( $sqll, "ARRAY_A" );
                    break;
                }

               
                
               

                
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
                }elseif ( $value == 'attention' ) { 
                    $WHERE .= " AND ( (`$index` = 'confirmed') OR (`$index` = 'waiting') OR (`$index` = 'approved') OR (`$index` = 'pay_to_confirm') )";
                }  else {
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
                    $WHERE .= " AND status IN ('confirmed','paid','approved')";
                   
                }elseif ( $value == 'attention' ){ 
                    $WHERE .= " AND status IN ('waiting','pay_to_confirm','approved','confirmed')";

                } else if ( $value == 'waiting' ){ 
                    $WHERE .= " AND status IN ('waiting','pay_to_confirm','approved')";
                    
                } else {
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

    public static function count_free_places_new( $listing_id, $date_start, $date_end )  {
        $slots_old = get_post_meta($listing_id, '_slots', true);
        $slots_old = json_decode($slots_old, true);
        $slots = array();
        foreach($slots_old as $day=>$slot)
        {
            $new_d = null;
            if($day==0)
            {
                $new_d = 1;
            }
            elseif($day==1)
            {
                $new_d = 2;
            }
            elseif($day==2)
            {
                $new_d = 3;
            }
            elseif($day==3)
            {
                $new_d = 4;
            }
            elseif($day==4)
            {
                $new_d = 5;
            }
            elseif($day==5)
            {
                $new_d = 6;
            }
            elseif($day==6)
            {
                $new_d = 0;
            }
            $slots[$new_d] = $slot;
        }
        
        $free_places = 0;
        $date_str1 = strtotime($date_start);
        $date_str2 = strtotime($date_end);
        $from_day = date('w', $date_str1);
        $to_day = date('w', $date_str2);
        $from_slot = $slots[$from_day];
        $to_slot = $slots[$to_day];
        $from_dt = date('d-m-Y', $date_str1);
        $to_dt = date('d-m-Y', $date_str2);
        if($from_dt==$to_dt)
        {
            if(!empty($from_slot))
            {
                $slot_from = $from_slot[0];
                $slot_to = $to_slot[0];
                $from_arr = explode('|', $slot_from); 
                $from_slot_kd = $from_arr[0];
                $from_free_places = $from_arr[1];
                $is_available = false;
                //$from_hour = date('H', $date_str1);
                //$from_minutes = date('i', $date_str1);
                $current_time = date('H:i', $date_str1);
                $date1 = DateTime::createFromFormat('H:i', $current_time);
                $from_s_arr = explode(" - ", $from_slot_kd);
                $date2 = DateTime::createFromFormat('H:i', $from_s_arr[0]);
                $date3 = DateTime::createFromFormat('H:i', $from_s_arr[1]);
                if ($date1 >= $date2 && $date1 <= $date3)
                {
                    $is_available = true;
                }
                if($is_available)
                {
                    $to_arr = explode('|', $slot_from);
                    $to_slot_kd = $to_arr[0];
                    $to_slot_places = $to_arr[1];
                    $current_time2 = date('H:i', $date_str2);
                    $date1_new = DateTime::createFromFormat('H:i', $current_time2);
                    $to_s_arr = explode(" - ", $to_slot_kd);
                    $date2_new = DateTime::createFromFormat('H:i', $to_s_arr[0]);
                    $date3_new = DateTime::createFromFormat('H:i', $to_s_arr[1]);
                    if ($date1_new >= $date2_new && $date1_new <= $date3_new)
                    {
                        $free_places = $to_slot_places;
                    }

                }
            }
        }
		else
		{
				$slot_from = $from_slot[0];
                $slot_to = $to_slot[0];
                $from_arr = explode('|', $slot_from); 
                $from_slot_kd = $from_arr[0];
                $from_free_places = $from_arr[1];
                $is_available = false;
				if($from_free_places>0)
				{
					$is_available = true;
					$to_arr = explode('|', $slot_to); 
					$to_free_places = $to_arr[1];
					if($to_free_places>0)
					{
						$free_places = $to_free_places;
					}
				}
			//echo "test";
			//die;
		}




      /*  $listing_type = get_post_meta ( $listing_id, '_listing_type', true );
        //echo $free_places;
       // die;
        if($listing_type == 'rental'){   
            $booking_hours = self::wpk_change_booking_hours( $date_start, $date_end );
            $date_start = $booking_hours[ 'date_start' ];
            $date_end = $booking_hours[ 'date_end' ];
        }*/
        $pk = new DateTime($date_start);
        $pnw = new DateTime($date_end);
        //echo $pk->format('Y-m-d H:i:s'); 
       // echo $pnw->format('Y-m-d H:i:s');
        $result = self :: get_bookings( $pk->format('Y-m-d H:i:s'), $pnw->format('Y-m-d H:i:s'), array( 'listing_id' => $listing_id, 'type' => 'reservation' ) );

       // echo $pk->format('Y-m-d H:i:s')."<br>";
       // echo $listing_id."<br>";
       // echo $pnw->format('Y-m-d H:i:s');
       // echo print_r($result, true);
       // die;
            if(!empty($result)) {
                return 0; 
            } else {
                return $free_places;
            }
            //return 1;
        //return $free_places;
       // echo $date_start;
       // echo $date_end;
       // die;
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
    public static function count_free_places( $listing_id, $date_start, $date_end, $slot = 0, $ref = "" )  {

         // get slots
         /*$_slots = self :: get_slots_from_meta ( $listing_id );
         $slots_status = get_post_meta ( $listing_id, '_slots_status', true );

         if(isset($slots_status) && !empty($slots_status)) {
            $_slots = self :: get_slots_from_meta ( $listing_id );
         } else {
            $_slots = false;
         }*/
        $_booking_system_service = get_post_meta($listing_id,"_booking_system_service",true);
        $_booking_slots = get_post_meta($listing_id,"_booking_slots",true);

        if($_booking_system_service == "on" && !empty($_booking_slots)){

            $_slots = $_booking_slots;

        }else{
            $_slots = array();
        }


        // get listing type
        $listing_type = get_post_meta ( $listing_id, '_listing_type', true );
     

         // default we have one free place
         $free_places = 1;

         // check if this is service type of listing and slots are added, then checking slots
         if ( $listing_type == 'service' && !empty($_slots) ) 
         {
            $slott = explode("|", $slot);

            $from_day = $slott[0];
            $from_time = $slott[1];
            $to_day = $slott[2];
            $to_time = $slott[3];
            $slot_price = $slott[4];
            $slots = $slott[5];
            $slot_id = $slott[6];

            $hour_start = date( "H:i:s", strtotime( $from_time ) );
            $hour_end = date( "H:i:s", strtotime( $to_time ) );
            

            $dayofweek = date('w', strtotime($date_start));
            $ts = strtotime($date_start);
            // find the year (ISO-8601 year number) and the current week
            $year = date('o', $ts);
            $week = date('W', $ts);

            $days = array();
            // print week for the current date
            for($i = 1; $i <= 7; $i++) {
                // timestamp from ISO week date format
                $ts = strtotime($year.'W'.$week.$i);
                $days[$i] = date("Y-m-d", $ts);
            }

            foreach ($days as $key_day => $day) {
                if($key_day == $from_day){
                    $date_start = $day." ".$hour_start;
                }
                if($key_day == $to_day){
                    $date_end = $day." ".$hour_end;
                }
            }

    
            $free_places = $slots;



 
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
         if($listing_type == 'rental' ) {
            $result = self :: get_bookings( 
                $date_start, 
                $date_end, 
                array( 'listing_id' => $listing_id, 'type' => 'reservation', 'status' => 'paid'), 
                $by = 'booking_date', 
                $limit = '', 
                $offset = '',
                $all = '', 
                $listing_type = 'rental' 
            );
          
         } else {
                if($listing_type == 'service' && !empty($_slots) ){


                    if( $ref == "confirm_step"){
                          $result = self ::  get_slots_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'reservation' ),'booking_date','','','slot_booking_confirm'  );
                    }else{
                         $result = self ::  get_slots_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'reservation' ),'booking_date','','','slot_booking'  );
                    }
                   
                } else {
                    $result = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'reservation' ), $by = 'booking_date', $limit = '', $offset = '',$all = '', $listing_type = 'service' );   
                }
             
         }

        

         // count how many reservations we have already for this slot
         $reservetions_amount = count( $result );   
        
         // minus temp reservations for this time
         // $free_places -= self :: temp_reservation_aval( array( 'listing_id' => $listing_id,
         // 'date_start' => $date_start, 'date_end' => $date_end) );

        // minus reservations from database
        $free_places -= $reservetions_amount;
        return $free_places;

    }
    public static function slotPriceType($slots,$type){
        $has_slot = false;
        foreach($slots as $slot){
            $slott = explode("|", $slot);

            $from_day = $slott[0];
            $from_time = $slott[1];
            $to_day = $slott[2];
            $to_time = $slott[3];
            $slot_price = $slott[4];
            $slots = $slott[5];
            $slot_id = $slott[6];
            $closed = (isset($slott[7]))?$slott[7]:"0";
            $all_slot_price = (isset($slott[8]))?$slott[8]:0;

            if($type == "slot_price" && $slot_price != ""){
                $has_slot = true;
            }
            if($type == "all_slot_price" && $all_slot_price != ""){
                $has_slot = true;
            }
        }
        return $has_slot;
    }

     public static function checkConflictBookingSlots($date_start,$date_end,$rec_exp,$listing_id,$post,$booking_id=""){

        global $wpdb;

        $data = array();


       $date_start = date("Y-m-d H:i:s", strtotime($date_start));
       $date_end = date("Y-m-d H:i:s", strtotime($date_end)); 

        $start_date_time = explode(" ", $start_date_time);
        $end_date_time = explode(" ", $end_date_time);

        $start_date =  $start_date_time[0];
        $end_date =  $end_date_time[0];


        $booking_table = $wpdb->prefix . "bookings_calendar";

        


        if(isset($post["sub_listings"]) && !empty($post["sub_listings"])){
            
            $list_query = "parent_listing_id =".$listing_id; 

        }else{
            $list_query = "listing_id =".$listing_id; 
        }
       // $query = "select *  from `$booking_table` WHERE ((`date_start` >= '$date_start' AND `date_start` < '$date_end') OR (`date_end` > '$date_start' AND `date_end` <= '$date_end') OR (`date_start` >= '$date_start' AND `date_end` <= '$date_end') OR (  '$date_start' >= `date_start` AND '$date_end' <= `date_end` )  ) AND $list_query"; 

        $query = "select *  from `$booking_table` WHERE ((`date_start` > '$date_start' AND `date_start` < '$date_end') OR (`date_end` > '$date_start' AND `date_end` < '$date_end') OR (`date_start` >= '$date_start' AND `date_end` < '$date_end') OR (  '$date_start' >= `date_start` AND '$date_end' < `date_end` )  ) AND $list_query"; 
        $results  = $wpdb -> get_results($query);

       
/*
     
        $conflict = "";

        if(count($results) > 0){

            $cnnn = false;


            foreach ($results as $key => $value) {
                if($value->recurrenceRule != ""){

                    $rules = $value->recurrenceRule;

                    $rules = explode(";", $rules);

                    $rulesss = array();

                    foreach ($rules as $key => $rule) {
                        $rule = explode("=", $rule);
                        $rulesss[$rule[0]] = $rule[1];
                    }

                    $rrule = new RRule($rulesss);

                    foreach ($rrule as $occurrence ) {
                        
                        echo $occurrence->format('Y-m-d'),"\n";
                    }

                }else{
                    $cnnn == true;
                }
            }
            die;

           if($cnnn){

               $conflict = "true";
               $conflict_val = 1;
               $data = array("date_start" => $date_start, "date_end" => $date_end, "rec_exp" => $rec_exp);
           }
           
           
        }else{
           $conflict_val = 2;
        }*/




     
        $conflict = "";

        if(count($results) > 0){

           $conflict = "true";
           $conflict_val = 1;
           
           
           
        }else{
           $conflict_val = 2;
        }

        if($conflict == ""){

            $query2 = "select *  from `$booking_table` WHERE  (recurrenceRule != '' OR recurrenceRule != NULL) AND $list_query";   
            $results2  = $wpdb -> get_results($query2);

            //echo "<pre>"; print_r($results2); die;


            if(!empty($results2)){
                foreach ($results2 as $key => $res) {
                    $rules = $res->recurrenceRule;

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
                    }

                    $rrule = new RRule($rulesss);

                    $exp_dates = array();

                    $recurrenceException = $res->recurrenceException;

                    if ($recurrenceException != "") {

                      $json_data = json_decode($recurrenceException);
                      if (json_last_error() === JSON_ERROR_NONE) {
                          $exp_dates = $json_data;

                          // echo "<pre>"; print_r($exp_dates); die;

                          
                      }else{

                        $recurrenceException = explode(",", $recurrenceException);

                        foreach ($recurrenceException as $key => $rec_exooo) {
                          //$date = str_replace("T"," ",$rec_exooo); 
                          $datee = Date("Y-m-d", strtotime($rec_exooo));

                          $exp_dates[] = $datee;
                        }

                      }


                    
                    }



                    foreach ($rrule as $occurrence ) {

                        $item_start = $occurrence->format('Y-m-d')." ".date("H:i:s",strtotime($res->date_start));
                        $item_end = $occurrence->format('Y-m-d')." ".date("H:i:s",strtotime($res->date_end));
                       


                        if(!in_array($occurrence->format('Y-m-d'), $exp_dates)){

                            $date_start_to_time = strtotime($date_start);
                            $date_end_to_time = strtotime($date_end);

                            $item_start_to_time = strtotime($item_start);
                            $item_end_to_time = strtotime($item_end);
                            if(
                                ($item_start_to_time >= $date_start_to_time && $item_start_to_time < $date_end_to_time)
                              || ($item_end_to_time > $date_start_to_time  && $item_end_to_time <= $date_end_to_time)
                              || ($item_start_to_time >= $date_start_to_time  && $item_end_to_time <= $date_end_to_time) 
                              || (  $date_start_to_time >= $item_start_to_time  && $date_end_to_time <= $item_end_to_time )
                            )  
                            {

                                $conflict = "true";
                               
                              

                            }
                            
                        } 
                    }
                }
            }

            

        }

        if($conflict == "true"){
             $data = array("date_start" => $date_start, "date_end" => $date_end, "rec_exp" => $rec_exp);
        }

        

        if($booking_id != ""){
            $wpdb->update($booking_table, array('conflict_status'=>$conflict_val), array('id'=>$booking_id));
        }
        return $data;
    }

    public function check_recent_booking($post_id,$REMOTE_ADDR) {
        // Retrieve the stored booking time from post meta
        $booking_time = get_post_meta($post_id, '_booking_time', true);
        $booking_server = get_post_meta($post_id, '_booking_server', true);
    
        // Check if a booking time exists
        if (!$booking_time) {
            return false; // No booking time found, no warning needed
        }
        if($booking_server == $REMOTE_ADDR){
            return false;
        }

    
        // Get the current time
        $current_time = current_time('timestamp'); // WordPress localized time in UNIX timestamp
    
        // Calculate the difference in minutes
        $time_difference = ($current_time - $booking_time) / 60; // Difference in minutes
    
        // Check if the booking was made within the last 5 minutes
        if ($time_difference <= 2) {
            return true; // A booking exists within the last 5 minutes
        }
    
        return false; // No recent booking found
    }
    public function check_recent_booking_slot($post_id,$REMOTE_ADDR) {
        // Retrieve the stored booking time from post meta
        $booking_time = get_post_meta($post_id, '_booking_time', true);
        $booking_server = get_post_meta($post_id, '_booking_server', true);
    
        // Check if a booking time exists
        if (!$booking_time) {
            return false; // No booking time found, no warning needed
        }
        // if($booking_server == $REMOTE_ADDR){
        //     return false;
        // }

    
        // Get the current time
        $current_time = current_time('timestamp'); // WordPress localized time in UNIX timestamp
    
        // Calculate the difference in minutes
        $time_difference = ($current_time - $booking_time) / 60; // Difference in minutes
    
        // Check if the booking was made within the last 5 minutes
        if ($time_difference <= 2) {
            return true; // A booking exists within the last 5 minutes
        }
    
        return false; // No recent booking found
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
  

        $listing_type = get_post_meta ( $_POST['listing_id'], '_listing_type', true );
        $_booking_system_weekly_view = get_post_meta ( $_POST['listing_id'], '_booking_system_weekly_view', true );


        if($_booking_system_weekly_view == "0"){
            $_booking_system_weekly_view = "";
        }

        if($listing_type == "service"){
          
                        if(!isset($_POST['slot'])){
                            $slot = false;
                        } else {
                            $slot = $_POST['slot'];
                        }
                        if(isset($_POST['hour'])){
                            $ajax_out['free_places'] = 1;
                        } else {
                            $sb = $_POST['sub_listings'];
                            $sd = 0;
                            if(!empty($sb))
                            {
                                foreach($sb as $sl)
                                {
                                    $free_places = self :: count_free_places_new( $sl, $_POST['date_start'], $_POST['date_end'] );
                                }
                                $sd+=$free_places;
                                $ajax_out['free_places'] = $sd;
                            }
                            else
                            {
                                if($slot){
                                    $ajax_out['free_places'] = self :: count_free_places( $_POST['listing_id'], $_POST['date_start'], $_POST['date_end'],$slot, "confirm_step");
                                }else{
                                    $ajax_out['free_places'] = self :: count_free_places_new( $_POST['listing_id'], $_POST['date_start'], $_POST['date_end'] );

                                }
                            }
                           
                          //  $ajax_out['free_places'] = self :: count_free_places_new( $_POST['listing_id'], $_POST['date_start'], $_POST['date_end'], );
                        }
                        
                        //die;
                        $multiply = 1;

                        $count_per_guest = get_post_meta($_POST['listing_id'], "_count_per_guest" , true );
                        if($count_per_guest){
                            if(isset($_POST['adults'])) $multiply = $_POST['adults'];
                        }
                        
                        
                        if(isset($_POST['tickets'])) $multiply = $_POST['tickets'];

                        $services = (isset($_POST['services'])) ? $_POST['services'] : false ;
                        // calculate price for all
                        $discount_percentage = 0;
                        if(isset($_POST["discount"]) && $_POST["discount"] != ""){
                            $discountss = get_post_meta($_POST['listing_id'],"_discounts_user",true); 

                            if($discountss && is_array($discountss) && count($discountss) > 0){

                                foreach ($discountss as $key => $discount) {
                                    if($discount["discount_name"] == $_POST["discount"]){
                                        $discount_percentage = $discount["discount_value"];
                                        break;
                                    }
                                }
                            }

                        }
                        $discount_percentage =  round($discount_percentage,2); 

                        $percentInDecimal = intval($discount_percentage) / 100;
                       // $_SESSION['test'] = $_POST['totalPrice'];
                       $ajax_out['services_price'] = self :: calculate_price_services( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, $_POST['totalPrice'], $_POST['totalDays']  );
                        $ajax_out['normal_price'] = self :: calculate_normal_price( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, $_POST['totalPrice'], $_POST['totalDays'], $_POST['sub_listings'] );
                        $ajax_out['multiply'] = $multiply;
                       // $_SESSION['discount_price'] = "";
                       // $_SESSION['price_sale'] = "";

                         //echo "<pre>"; print_r($ajax_out); die;

                       // die;
                        if(is_numeric($discount_percentage)){

                            $ajax_out['discount_price'] = $ajax_out['normal_price'] * $percentInDecimal;

                            $_SESSION['discount_price'] = $ajax_out['discount_price'];

                            $ajax_out['post_price'] = $ajax_out['normal_price'];

                            $ajax_out['normal_price'] = $ajax_out['normal_price'] - $ajax_out['discount_price'];

                        }

                        $taxPercentage = get_post_meta ( $_POST['listing_id'], '_tax', true);


                        
                        $ajax_out['price'] = self :: calculate_price_old( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply,0, $ajax_out['normal_price'], $_POST['totalDays']  );
                        /*echo $multiply.'<br>';
                        echo $ajax_out['normal_price'].'<br>';
                       echo $ajax_out['price'];
                        die;*/

                        $ajax_out['services_tax_price'] = self :: calculate_tax_services( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, $_POST['totalPrice'], $_POST['totalDays']  );


                        




                        if(isset($_POST["rec"]) && $_POST["rec"] != "" && isset($_POST["count_rec"]) && $_POST["count_rec"] > 0 ){

                            

                            $get_rec_price = 0;

                            $conflicts_dates = array();
                            $av_dates = array();

                            $icc = 0;
                            $exp_dates = array();

                            if(isset($_POST["expp"]) && $_POST["expp"] > 0){
                               foreach ($_POST["expp"] as $keyy => $expp) {
                                  $exp_dates[] = $expp;
                               }
                            }

                            $av_rec = 0;
                            $conflict_rec = array();

                            $dta_post = $_POST;




                            if(isset($_POST["rec_bookings"]) && $_POST["rec_bookings"] > 0){


                                foreach ($_POST["rec_bookings"] as $keyy => $rec_booking) {

                                    
                                        $dt_start = explode(" ", $rec_booking["date_start"])[0]." ".explode(" ", $_POST['date_start'])[1];
                                        $dt_end = explode(" ", $rec_booking["date_end"])[0]." ".explode(" ", $_POST['date_end'])[1];
                                        $rec_exp = $rec_booking["rec_exp"];
                                          
                                        $conflicts_data = array();
                                        $conflicts_data = self::checkConflictBookingSlots( $dt_start, $dt_end, $rec_exp, $_POST['listing_id'], $dta_post );

                                        if(!empty($conflicts_data)){

                                               $conflicts_dates[$icc]["date_start"] = $conflicts_data["date_start"];
                                               $conflicts_dates[$icc]["date_end"] = $conflicts_data["date_end"];
                                               $conflicts_dates[$icc]["rec_exp"] = $conflicts_data["rec_exp"];
                                               $conflict_rec[] = $conflicts_data["rec_exp"];
                                               $exp_dates[] = $conflicts_data["rec_exp"];
                                               $icc++;
                                            
                                        }else{

                                            if(!in_array($rec_booking["rec_exp"], $exp_dates)){

                                                //if($_POST['date_start'] != $dt_start){
                                                    $get_rec_price += self :: calculate_normal_price( $_POST['listing_id'],  $dt_start, $dt_end,$multiply, $services);
                                               // } 
                                                
                                                $av_rec++;
                                            }   
                                        }
                                        
                                    

                                }
                               //  $get_rec_price = $get_rec_price - 
                            }
                            //echo "<pre>"; print_r($conflicts_dates); die;

                            $rec_dates = array();

                            foreach ($_POST["rec_bookings"] as $key_rec => $recc) {
                                if(!in_array($recc["rec_exp"], $conflict_rec)){
                                    $rec_dates[] = $recc;
                                }
                            }

                           // echo "<pre>"; print_r($conflict_rec); die;

                          

                           // echo  $get_rec_price; die;
                           
                            $ajax_out['price'] = $ajax_out['services_price'];
                          //  $ajax_out['price'] = $ajax_out['price'] + $ajax_out['services_price'];


                            $ajax_out['price'] = $ajax_out['price'] + $get_rec_price;

                            $tax = ($taxPercentage / 100) * round($ajax_out['price']);

                            $ajax_out['taxprice'] = round($tax);


                            $ajax_out['price'] = $ajax_out['price'] + $tax;


                            $ajax_out['price'] = round($ajax_out['price']);


                            $ajax_out['av_days'] = $av_rec;
                            $ajax_out['av_dates'] = $rec_dates;
                            $ajax_out['conflicts_dates'] = $conflicts_dates;
                            $ajax_out['with_coupon_rec_price'] = $ajax_out["price"];
                            $ajax_out['rec'] = $_POST["rec"];
                            $ajax_out['endrecdate'] = $_POST["endrecdate"];
                            $ajax_out['exp_dates'] = $exp_dates;

                           // echo "<pre>"; print_r($ajax_out); die;


                        }else{


                            $tax = ($taxPercentage / 100) * round($ajax_out['normal_price']);
                            $ajax_out['taxprice'] = round($tax);

                             $ajax_out['price'] = $ajax_out['price'] + $tax + $ajax_out['services_price'];

                               

                            $ajax_out["price"] = round($ajax_out["price"]);
                        }

                        $gift_price = 0;

                        if($_POST["coupon"] != ""){
                            $coupon = (isset($_POST['coupon'])) ? $_POST['coupon'] : false ;

                            
                            if($coupon) {
                                $total_price1 = -1;
                                $coupons = explode(',',$coupon);
                               // echo "<pre>"; print_r($coupon); die;
                                foreach ($coupons as $key => $new_coupon) {
                                    if(class_exists("Class_Gibbs_Giftcard")){

                                        $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;
                            
                                        $data = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);
                            
                                        if($data && isset($data["id"])){

                                            $gift_price += $data["remaining_saldo"];
                                            continue;

                                        }
                                    }   
                                    $total_price1 = self::apply_coupon_to_price($ajax_out['price'],$new_coupon);
                                }

                                if($total_price1 >= 0){
                                    $ajax_out['coupon_price'] = round($ajax_out['price'] - $total_price1);
                                    // $ajax_out['price'] = number_format_i18n($total_price1,$decimals);
                                    $ajax_out['price'] = round($total_price1);
                                }
                                
                            }
                        }

                      

                        
                        if(isset( $ajax_out['rec']) &&  $ajax_out['rec'] != ""){
                            $_SESSION['rec_price'] = $ajax_out['price'];
                        }else{
                            $_SESSION['rec_price'] = "";
                        }

                        //if($ajax_out['price'] > 0){
                            $_SESSION['discountedprice'] = $ajax_out['price'];
                      //  }

                        if($gift_price > 0){

                            $price_ajax = $ajax_out['price'];
                            
                            if($price_ajax > 0){
                                
                                if($gift_price > $price_ajax){
                                    $ajax_out['remaining_saldo'] = $gift_price - $price_ajax;
                                    $price_ajax = 0;
                                }else{
                                    $ajax_out['remaining_saldo'] = 0;
                                    $price_ajax = $price_ajax - $gift_price;
                                }
                                $ajax_out['price']= $price_ajax;
                                
                            }else{
                                $ajax_out['remaining_saldo'] = $gift_price;
                            }
                        }

                        $simple_string = "6666.66";    

                        $encKey = self::encryptString($ajax_out['price']); 

  
                        

                        $ajax_out['price_algo'] = $encKey;     



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
                        $results = $wpdb->get_results("SELECT comment FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `listing_id` = '$listing_id' AND (('{$a}' BETWEEN `date_start` AND `date_end`) OR ('{$b}' BETWEEN `date_start` AND `date_end`) OR ('{$a}' <= `date_start` AND '{$b}' >= `date_end`))");
                       
                        $if_equipment = get_post_meta ( $_POST['listing_id'], '_category', true);
                        $interval = DateInterval::createFromDateString('30 minutes');
                        $start = new \DateTime($_POST['date_start']);
                        $end = new \DateTime($_POST['date_end']); 
                        $dt_start = new DateTime($start->format("d-m-Y 00:00"));
                        $dt_end = new DateTime($start->format("d-m-Y 24:00"));
                        $periods = new \DatePeriod($dt_start, $interval, $dt_end);
                        $dts = array();
                        foreach ( $periods as $current_day ) {
                            $dpp = $start->format("d-m-Y H:i");
                            $cmpp = $current_day->format("d-m-Y H:i");
                            $selb = false;
                            // if($dpp==$cmpp)
                            // {
                            //     $selb = "selected='selected'";
                            // }
                            // $dts[] = "<option ".$selb." value='".$cmpp."'>".$current_day->format("j.M. H:i")."</option>";
                            if($dpp==$cmpp)
                            {
                                $selb = true;
                            }

                            $dts[] = array("selected" => $selb, "value" => $cmpp, "text" => $current_day->format("j.M. H:i"));
                        }
                        $new_end = new DateTime($start->format("d-m-Y 24:00"));
                        $new_periods = new \DatePeriod($end, $interval, $new_end);
                        $dks = array();
                        $minutes_to_add = 30;
                        $start->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                        foreach ( $periods as $current_day ) {
                            $sdm = $start->format("d-m-Y H:i");
                            $dp = $end->format("d-m-Y H:i");
                            $cmp = $current_day->format("d-m-Y H:i");
                            if($sdm<=$cmp)
                            {
                                $sel = false;
                                // if($dp==$cmp)
                                // {
                                //     $sel = "selected='selected'";
                                // }
                                // $dks[] = "<option ".$sel." value='".$cmp."'>".$current_day->format("j.M. H:i")."</option>";
                                if($dp==$cmp)
                                {
                                    $sel = true;
                                }
                                
                                $dks[] = array("selected" => $sel, "value" => $cmp, "text" => $current_day->format("j.M. H:i"));
                            }
                        }
                        //echo print_r($dts, true);
                        //die;
                        if(!empty($dts))
                        {
                            $ajax_out['from_dates'] = $dts;
                        }
                        if(!empty($dks))
                        {
                            $ajax_out['to_dates'] = $dks;
                        }

                        $_booking_system_service = get_post_meta($listing_id,"_booking_system_service",true);
                        $_booking_slots = get_post_meta($listing_id,"_booking_slots",true);

                        if($_booking_system_service == "on" && !empty($_booking_slots)){
                            if(!$slot){
                                $ajax_out = array();
                            }
                        }

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
        }else{
                if(!isset($_POST['slot'])){
                    $slot = false;
                } else {
                    $slot = sanitize_text_field($_POST['slot']);
                }
                if(isset($_POST['hour'])){

                    $_opening_hours_status = get_post_meta($_POST['listing_id'], '_opening_hours_status',true);
                    $ajax_out['free_places'] = 1;
                    //check opening times
                    if($_opening_hours_status){
                        $currentTime = $_POST['hour'];
                        $date = $_POST['date_start'];
                        $timestamp = strtotime($date);
                        $day = strtolower(date('l', $timestamp));
                        //get opening hours for this day
                        

                        if(!empty($currentTime) && is_numeric(substr($currentTime, 0, 1)) ) {
                            if(substr($currentTime, -1)=='M'){
                                $currentTime = DateTime::createFromFormat('h:i A', $currentTime);
                                if($currentTime){
                                    $currentTime = $currentTime->format('Hi');            
                                }

                                //
                            } else {
                                $currentTime = DateTime::createFromFormat('H:i', $currentTime);
                                if($currentTime){
                                    $currentTime = $currentTime->format('Hi');
                                }
                            }
                            
                        } 

                        $opening_hours = get_post_meta( $_POST['listing_id'], '_'.$day.'_opening_hour', true);
                        $closing_hours = get_post_meta( $_POST['listing_id'], '_'.$day.'_closing_hour', true);
                        $ajax_out['free_places'] = 0;
                        if(empty($opening_hours) && empty($closing_hours)){
                            $ajax_out['free_places'] = 0;
                        } else {
                            $storeSchedule = array(
                                'opens' => $opening_hours,
                                'closes' => $closing_hours
                            );
                            
                            $startTime = $storeSchedule['opens'];
                            $endTime = $storeSchedule['closes'];
                            if(is_array($storeSchedule['opens'])){
                                    foreach ($storeSchedule['opens'] as $key => $start_time) {
                                        # code...
                                        $end_time = $endTime[$key];
                                       
                                        if(!empty($start_time) && is_numeric(substr($start_time, 0, 1)) ) {
                                            if(substr($start_time, -1)=='M'){
                                                $start_time = DateTime::createFromFormat('h:i A', $start_time);
                                                if($start_time){
                                                    $start_time = $start_time->format('Hi');            
                                                }
             
                                                //
                                            } else {
                                                $start_time = DateTime::createFromFormat('H:i', $start_time);
                                                if($start_time){
                                                    $start_time = $start_time->format('Hi');
                                                }
                                            }
                                            

                                        } 
                                           //create time objects from start/end times and format as string (24hr AM/PM)
                                        if(!empty($end_time)  && is_numeric(substr($end_time, 0, 1))){
                                            if(substr($end_time, -1)=='M'){
                                                $end_time = DateTime::createFromFormat('h:i A', $end_time);         
                                                if($end_time){
                                                    $end_time = $end_time->format('Hi');
                                                }
                                            } else {
                                                $end_time = DateTime::createFromFormat('H:i', $end_time);
                                                if($end_time){
                                                    $end_time = $end_time->format('Hi');
                                                }
                                            }
                                        } 
                                       
                                        if($end_time == '0000'){
                                            $end_time = 2400;
                                        }

                                        if((int)$start_time > (int)$end_time ) {
                                            // midnight situation
                                            $end_time = 2400 + (int)$end_time;
                                        }

                                       
                                        // check if current time is within the range
                                        if (((int)$start_time <= (int)$currentTime) && ((int)$currentTime <= (int)$end_time)) {
                                             $ajax_out['free_places'] = 1;
                                        } 
                                        
                                    }
                            } else {
                                 $ajax_out['free_places'] = 0;
                            }   
                        } 
                    }
                    
                    
                    
                  

                } else {


                      $listing_type = get_post_meta ( $_POST['listing_id'], '_listing_type', true );
                        //echo $free_places;
                       // die;
                        if($listing_type == 'rental'){   
                            $booking_hours = self::wpk_change_booking_hours( $_POST['date_start'],$_POST['date_end'] );
                            $_POST['date_start']= $booking_hours[ 'date_start' ];
                            $_POST['date_end'] = $booking_hours[ 'date_end' ];
                        } 
                    $ajax_out['free_places'] = self :: count_free_places_new( $_POST['listing_id'], $_POST['date_start'], $_POST['date_end'] );    
                }

                //echo "<pre>"; print_r($ajax_out); die;

               
                $multiply = 1;
                if(isset($_POST['adults'])) $multiply = $_POST['adults']; 
                if(isset($_POST['tickets'])) $multiply = $_POST['tickets'];
                
                
                $coupon = (isset($_POST['coupon'])) ? $_POST['coupon'] : false ;
                $services = (isset($_POST['services'])) ? $_POST['services'] : false ;
                // calculate price for all
                $decimals = get_option('listeo_number_decimals',2);

                $discount_percentage = 0;
                if(isset($_POST["discount"]) && $_POST["discount"] != ""){
                    $discountss = get_post_meta($_POST['listing_id'],"_discounts_user",true); 

                    if($discountss && is_array($discountss) && count($discountss) > 0){

                        foreach ($discountss as $key => $discount) {
                            if($discount["discount_name"] == $_POST["discount"]){
                                $discount_percentage = $discount["discount_value"];
                                break;
                            }
                        }
                    }

                }
                $_SESSION["taxprice"] = "";
                $_SESSION["normalprice"] = "";

                $tax = 0;

                $taxPercentage = get_post_meta ( $_POST['listing_id'], '_tax', true);

                if($taxPercentage && $taxPercentage > 0){

                    $tax = $taxPercentage;

                }

                
                $price = self :: calculate_price( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, '',$discount_percentage, $tax  );

            

                $ajax_out['price'] = round($price);

                $gift_price = 0;

                if(!empty($coupon)){
                    $price_discount = self :: calculate_price( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'],$multiply, $services, $coupon,$discount_percentage, $tax );
                    $ajax_out['price_discount'] = round($price_discount);
                     $ajax_out['price'] = $price_discount;
                    $coupons = explode(',',$coupon);
                    // echo "<pre>"; print_r($coupon); die;
                    foreach ($coupons as $key => $new_coupon) {
                        if(class_exists("Class_Gibbs_Giftcard")){

                            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;
                
                            $data = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);
                
                            if($data && isset($data["id"])){

                                $gift_price += $data["remaining_saldo"];
                                continue;

                            }
                        }   
                    }
                }

                if($gift_price > 0){

                    $price_ajax = $ajax_out['price'];
                    
                    if($price_ajax > 0){
                        
                        if($gift_price > $price_ajax){
                            $ajax_out['remaining_saldo'] = $gift_price - $price_ajax;
                            $price_ajax = 0;
                        }else{
                            $ajax_out['remaining_saldo'] = 0;
                            $price_ajax = $price_ajax - $gift_price;
                        }

                        //$ajax_out['price']= $price_ajax;
                        $ajax_out['price_discount']= $price_ajax;
                        
                    }else{
                        $ajax_out['remaining_saldo'] = $gift_price;
                    }
                }
                

                //echo "<pre>"; print_r($ajax_out); die;

                if($_SESSION["taxprice"] != ""){
                    $ajax_out['taxprice'] = $_SESSION["taxprice"];
                    $ajax_out['normalprice'] = $_SESSION["normalprice"];
                }



                wp_send_json_success( $ajax_out );
        }        
       
    }


    public function check_if_coupon_exists($coupon){
            global $wpdb;
            $title = sanitize_text_field($coupon);
            $sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1;", $title );
            //check if coupon with that code exits
            $coupon_id = $wpdb->get_var( $sql );
            
            return ($coupon_id) ? true : false ;
    }
    public function check_gifts_code($coupon,$listing_id){
        

        if(class_exists("Class_Gibbs_Giftcard")){

            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

            $data = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($coupon);

            if($data && isset($data["id"])){

                $giftcard_id = get_post_meta($data["id"], 'giftcard_id', true);

                $listing_ids = get_post_meta($giftcard_id, 'listing_ids', true);
                $remaining_saldo = get_post_meta($data["id"], 'remaining_saldo', true);

                if(!in_array($listing_id,$listing_ids)){
                    $ajax_out['error'] = true;
                    $ajax_out['error_type'] = 'coupon_wrong_listing';
                    $ajax_out['message'] =  esc_html__('Denne kupongen gjelder ikke for denne annonsen','listeo_core');
                    wp_send_json( $ajax_out );
                }

                $expire_date = $data["expire_date"]; 

                if($expire_date && $expire_date != ""){


                    $cr_date = date("Y-m-d")." 00:00:00";
                    $cp_expire_date =  $expire_date." 00:00:00";

                    if(  strtotime($cr_date) > strtotime($cp_expire_date)  ){
                        $ajax_out['error'] = true;
                        $ajax_out['error_type'] = 'coupon_expired';
                        $ajax_out['message'] = esc_html__( 'Denne kupongen har utgått.', 'listeo_core' );
                        wp_send_json( $ajax_out );
                    }

                }

                if($remaining_saldo < 1){
                    
                    $ajax_out['error'] = true;
                    $ajax_out['error_type'] = 'coupon_limit_used';
                    $ajax_out['message'] = esc_html__( 'Denne kupongen har utgått.', 'listeo_core' );
                    wp_send_json( $ajax_out );

                }

                $ajax_out['success'] = true;
                $ajax_out['coupon'] = $coupon;
                wp_send_json( $ajax_out );

                
                
            }



        }

        

    }

    public static function ajax_validate_coupon(){
        $listing_id = $_POST['listing_id'];
        $coupon = $_POST['coupon'];
        $coupons = $_POST['coupons'];
        $price = $_POST['price'];

        if(empty($coupon)){
            $ajax_out['error'] = true;
            $ajax_out['error_type'] = 'no_coupon';
            $ajax_out['message'] = esc_html__('Coupon was not provided','listeo_core');
            wp_send_json( $ajax_out );
        }

        $check_gifts_code = self::check_gifts_code($coupon,$listing_id);

        if(! self::check_if_coupon_exists($coupon) ){
            $ajax_out['error'] = true;
            $ajax_out['error_type'] = 'no_coupon_exists';
            $ajax_out['message'] = esc_html__('This coupon does not exist','listeo_core');
            wp_send_json( $ajax_out );
        }
        $wc_coupon = new WC_Coupon($coupon);
        
        //check price 
        if($wc_coupon->get_individual_use()){
            
            if(isset($coupons) && is_array($coupons) && count($coupons) > 1){
                $ajax_out['error'] = true;
                $ajax_out['error_type'] = 'coupon_used_once';
                $ajax_out['message'] =  __( 'This coupons can\'t be used with others.', 'listeo_core' );
                wp_send_json( $ajax_out );
            }
        }


        if($wc_coupon->get_minimum_amount() > 0 && $wc_coupon->get_minimum_amount() > $price ) {
            $ajax_out['error'] = true;
            $ajax_out['error_type'] = 'coupon_minimum_spend';
            $ajax_out['message'] = sprintf( __( 'The minimum spend for this coupon is %s.', 'listeo_core' ), wc_price( $wc_coupon->get_maximum_amount() ) );
            wp_send_json( $ajax_out );
        }  

        if($wc_coupon->get_maximum_amount() > 0 && $wc_coupon->get_maximum_amount() < $price ) {
            $ajax_out['error'] = true;
            $ajax_out['error_type'] = 'coupon_maximum_spend';
            $ajax_out['message'] = sprintf( __( 'The maximum spend for this coupon is %s.', 'listeo_core' ), wc_price( $wc_coupon->get_maximum_amount() ) );
            wp_send_json( $ajax_out );
        }

        //validate_coupon_user_usage_limit
        $user_id = get_current_user_id();
        if($wc_coupon->get_usage_limit_per_user() && $user_id){
            $data_store  = $wc_coupon->get_data_store();
            $usage_count = $data_store->get_usage_by_user_id( $wc_coupon, $user_id );
            
            if ( $usage_count >= $wc_coupon->get_usage_limit_per_user() ) {
               $ajax_out['error'] = true;
                $ajax_out['error_type'] = 'coupon_limit_used';
                $ajax_out['message'] = sprintf( __( 'Coupon usage limit has been reached', 'listeo_core' ), wc_price( $wc_coupon->get_maximum_amount() ) );
                wp_send_json( $ajax_out );
            }   
        }
       
        if ( $wc_coupon->get_date_expires() &&  time() > $wc_coupon->get_date_expires()->getTimestamp()  ) {
                $ajax_out['error'] = true;
                $ajax_out['error_type'] = 'coupon_expired';
                $ajax_out['message'] = sprintf( __( 'This coupon has expired.', 'listeo_core' ), wc_price( $wc_coupon->get_maximum_amount() ) );
                 wp_send_json( $ajax_out );
        }

        //check author of coupon, check if he is admin
        $author_ID = get_post_field( 'post_author', $wc_coupon->get_ID() );
        $authorData = get_userdata( $author_ID );
        if (in_array( 'administrator', $authorData->roles)):
            $admins_coupon = true;
        else:
            $admins_coupon = false;
        endif;
        if($wc_coupon->get_usage_limit()>0) {

             $usage_left = $wc_coupon->get_usage_limit() - $wc_coupon->get_usage_count();

            if ($usage_left > 0) {

                if($admins_coupon){
                        $ajax_out['success'] = true;
                        $ajax_out['coupon'] = $coupon;
                        wp_send_json( $ajax_out );
                } else {
                   $available_listings = $wc_coupon->get_meta('listing_ids');
                    $available_listings_array = explode(',',$available_listings);
                    if(in_array($listing_id,$available_listings_array)) {
                        $ajax_out['success'] = true;
                        $ajax_out['coupon'] = $coupon;
                        wp_send_json( $ajax_out );
                    } else {
                        $ajax_out['error'] = true;
                        $ajax_out['error_type'] = 'coupon_wrong_listing';
                        $ajax_out['message'] =  esc_html__('This coupon is not applicable for this listing','listeo_core');
                        wp_send_json( $ajax_out );
                    } 
                }

                
                
            } 
            else {
                $ajax_out['error'] = true;
                $ajax_out['error_type'] = 'coupon_limit_used';
                $ajax_out['message'] =  esc_html__('Coupon usage limit has been reached','listeo_core');
                wp_send_json( $ajax_out );
            }  
             
        } else {

            if($admins_coupon){
                    $ajax_out['success'] = true;
                    $ajax_out['coupon'] = $coupon;
                    wp_send_json( $ajax_out );
            } else {
                $available_listings = $wc_coupon->get_meta('listing_ids');
                $available_listings_array = explode(',',$available_listings);
                if(in_array($listing_id,$available_listings_array)) {
                    $ajax_out['success'] = true;
                    $ajax_out['coupon'] = $coupon;
                    $ajax_out['message'] =  esc_html__('This coupon is not applicable for this listing','listeo_core');
                    wp_send_json( $ajax_out );
                } else {
                    $ajax_out['error'] = true;
                    $ajax_out['error_type'] = 'coupon_wrong_listing';
                    $ajax_out['message'] =  esc_html__('This coupon is not applicable for this listing','listeo_core');
                    wp_send_json( $ajax_out );
                }
            }

            
        }
       
    }


    public static function ajax_calculate_booking_form_price(){
        
        
        $price          = sanitize_text_field($_POST['price']);
        $coupon         = sanitize_text_field($_POST['coupon']);

        if(!empty($coupon)) {
            $coupons = explode(',',$coupon);
            foreach ($coupons as $key => $new_coupon) {
                $price = self::apply_coupon_to_price($price,$new_coupon);
            }    
        }
        
        if($price != $_POST['price']){
            $ajax_out['price'] = $price;
            wp_send_json( $ajax_out );
        } else {
            wp_send_json_success();
        }
    }

    public static function ajax_calculate_price( ) {
        $listing_id = $_POST['listing_id'];
        $tickets = isset($_POST['tickets']) ? $_POST['tickets'] : 1 ;

         
        
        $normal_price       = (float) get_post_meta ( $listing_id, '_normal_price', true);
        $reservation_price  =  (float) get_post_meta ( $listing_id, '_reservation_price', true);
        $services_price     = 0;

        if(isset($_POST['services'])){
            $services = $_POST['services'];
        
            if(isset($services) && !empty($services)){

                $bookable_services = listeo_get_bookable_services($listing_id);
                $countable = array_column($services,'value');
        
                $i = 0;
                foreach ($bookable_services as $key => $service) {
                    
                    if(in_array(sanitize_title($service['name']),array_column($services,'service'))) { 
                        //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);
                        $services_price +=  listeo_calculate_service_price($service, $tickets, 1, $countable[$i] );
                       
                       $i++;
                    }
                   
                
                } 
            }
          
        }


        $total_price = ($normal_price * $tickets) + $reservation_price + $services_price;
        $decimals = get_option('listeo_number_decimals',2);
        $ajax_out['price'] = round($total_price);
        //check if there's coupon
        $coupon = (isset($_POST['coupon'])) ? $_POST['coupon'] : false ;

        if($coupon) {
            $sale_price = $total_price;
            $coupons = explode(',',$coupon);
            foreach ($coupons as $key => $new_coupon) {
                $total_price = self::apply_coupon_to_price($total_price,$new_coupon);
            }
            $ajax_out['price_discount'] = round($total_price);
        }

        

      
        wp_send_json_success( $ajax_out );
    }


    public static function apply_coupon_to_price($price, $coupon_code){

            if($price == 0) {
                return 0;
            }
            if(!$coupon_code) {
                return $price;
            }




        // Sanitize coupon code.
            $coupon_code = wc_format_coupon_code( $coupon_code );

            // Get the coupon.
            $the_coupon = new WC_Coupon( $coupon_code );
            if($the_coupon) {

               

                $amount = $the_coupon->get_amount();

               


                if($the_coupon->get_discount_type() == 'fixed_product'){
                    if($amount > $price){
                        return 0;
                    }else{
                        return $price - $amount;
                    }
                    
                } else {
                    return $price - ($price *  ($amount / 100) ) ;
                }    
            } else {
                return $price;
            }
            

    }
    public static function getSlotDates($slots) {

        foreach ($slots as $key => $_slot) {

            

        }

    }
    

    public static function ajax_update_slots( ) {


        

        global $wpdb;

        // get slots
         $listing_id = $_POST['listing_id'];
         $listing_data = get_post($listing_id);
         $user_currency = "NOK";
         if(isset($listing_data->post_author) && $listing_data->post_author != ""){
            $user_currency_data = get_user_meta( $listing_data->post_author, 'currency', true );
            if($user_currency_data != ""){
                $user_currency = $user_currency_data;
            }
         }
         $date_start = $_POST['date_start'];
         $date_end = $_POST['date_end'];

         $_hide_price_div = get_post_meta($listing_id,'_hide_price_div',true);
         
         $dayofweek = date('w', strtotime($date_start));
         
         $un_slots = get_post_meta( $listing_id, '_slots', true );
         
         $_slots = self :: get_slots_from_meta ( $listing_id );

         $_booking_system_service = get_post_meta($listing_id,"_booking_system_service",true);
         $_booking_slots = get_post_meta($listing_id,"_booking_slots",true);

         if($_booking_system_service == "on" && !empty($_booking_slots)){

             $_slots = $_booking_slots;

         }else{
             $_slots = array();
         }

         //sloty na dany dzien:
         if($dayofweek == 0){
             $actual_day = 6;    
         } else {
             $actual_day = $dayofweek-1;    
         }
         $actual_day = $actual_day + 1;

         //echo $actual_day; die;

         $_slots_for_days = array();

         



         if(is_array($_slots) && !empty($_slots)){
             foreach ($_slots as $key => $_slot) {
                 $slott = explode("|", $_slot);

                 $from_day = $slott[0];
                 $from_time = $slott[1];
                 $to_day = $slott[2];
                 $to_time = $slott[3];
                 $slot_price = $slott[4];
                 $slots = $slott[5];
                 $slot_id = $slott[6];





                 if($actual_day >= $from_day && $actual_day <= $to_day){
                     $_slots_for_days[] = $_slot;
                    // echo "<pre>"; print_r($_slot); 
                 }
             }
           // $_slots_for_day = $_slots[$actual_day];
         } else {
            $_slots_for_day = false;
         }
         
         $ajax_out = false;
         $new_slots = array();

         $empty_slot = true;

         

         if(is_array($_slots_for_days) && !empty($_slots_for_days)){

             ob_start();
             ?>
             <input id="slot" type="hidden" name="slot" value="" />
             <input id="listing_id" type="hidden" name="listing_id" value="<?php echo $listing_id; ?>" >
             <?php

             $timezone_string =  get_option('timezone_string');    

             date_default_timezone_set($timezone_string);

             
            
             

             

             
             
             foreach ($_slots_for_days as $key => $_slot) {
                 //$slot = json_decode( wp_unslash($slot) );
                 
                 $slott = explode("|", $_slot);

                 $from_day = $slott[0];
                 $from_time = $slott[1];
                 $to_day = $slott[2];
                 $to_time = $slott[3];
                 $slot_price = $slott[4];
                 $slots = $slott[5];
                 $slot_id = $slott[6];
                 $closed = (isset($slott[7]))?$slott[7]:"0";
                 $all_slot_price = (isset($slott[8]))?$slott[8]:0;
                 //echo "<pre>"; print_r($slott); die;

                 if($_POST["slot_price_type"] == "all_slot_price"){
                     $slot_price = $all_slot_price;
                     if($all_slot_price == ""){
                         continue;
                         $all_slot_price = 0;
                     }
                 }else{
                     if($slot_price == ""){
                         continue;
                         $slot_price = 0;
                     }

                 }

                 

                 
                 

                 $total_slots = $slots;
                 $free_places = $slots;

                 $slot_calculate_date = Gibbs_Booking_Calendar::slot_calculate_date( $date_start,$_slot);


                 $date_start = $slot_calculate_date["date_start"];
                 $date_end = $slot_calculate_date["date_end"];

                 $cr_dd = date("Y-m-d H:i:s");

                 $cr_dd_date_only = date("Y-m-d");

                 $slot_date_only = date("Y-m-d",strtotime($date_start));

                 if($cr_dd_date_only == $slot_date_only){
                     if($cr_dd > $date_end){
                         continue;
                     }
                 }

                

                 

                 


                // echo "<pre>"; print_r($cr_dd); die;

                 $result = self ::  get_slots_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'reservation' ),'booking_date','','','slot_booking'  );

                 // if($date_start == "2024-04-23 17:00:00"){
                 //     echo "<pre>"; print_r($date_start);
                 //     echo "<pre>"; print_r($date_end);
                 //     echo "<pre>"; print_r($result);die;
                 // }
                 

                 
                 $count_slot = 0;


                 $guest_slot = get_post_meta($listing_id,"_guest_slot",true);

                 $isguest = 1;
                 

                 if($guest_slot == "no"){
                     $isguest = 0;
                 }

                 
                 foreach ($result as $res) {
                     $dataaa = $wpdb->get_row("select * from bookings_calendar_meta where meta_key = 'number_of_guests' AND  booking_id = ".$res["id"]);

                     if(isset($dataaa->meta_value) && $dataaa->meta_value != "" && $isguest){
                         $count_slot += (int) $dataaa->meta_value;
                     }else{
                         $count_slot += 1;
                     }
                 }
                 

                 // if(!empty($result)){
                 //     echo "<pre>"; print_r($date_start); echo "</pre>";
                 //     echo "<pre>"; print_r($date_end); echo "</pre>";
                 //     echo "<pre>"; print_r($free_places); echo "</pre>";
                 //     echo "<pre>"; print_r($count_slot); echo "</pre>";die;
                 // }

                
                 
                 //$reservations_amount = count( $result ); 
                 $reservations_amount = $count_slot; 

                 $free_places -= $reservations_amount;
                 

                 if($_POST["slot_price_type"] == "all_slot_price" && $free_places != $slots){
                     continue;
                 }

                 

                 

                 

                 $taxPercentage = get_post_meta ( $listing_id, '_tax', true);

                 $tax = ($taxPercentage / 100) * round($slot_price);

                 $slot_price = $slot_price + $tax;


                 


                 //get hours and date to check reservation
                /* $hours = explode( ' - ', $places[0] );

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
                 }*/
             
             ?>

             <?php 
             $days_list = array(
                     1   => __('Monday','listeo_core'),
                     2   => __('Tuesday','listeo_core'),
                     3   => __('Wednesday','listeo_core'),
                     4   => __('Thursday','listeo_core'),
                     5   => __('Friday','listeo_core'),
                     6   => __('Saturday','listeo_core'),
                     7   => __('Sunday','listeo_core'),
             ); 
               if($free_places > 0){
                 $empty_slot = false;
                 $currency_symbol = get_woocommerce_currency_symbol($user_currency);
             ?>
               
                 <div class="time-slot" day="<?php echo $from_day; ?>">
                     <input type="radio" name="time-slot" id="slot_<?php echo $slot_id; ?>" value="<?php echo $_slot; ?>">
                     <label for="slot_<?php echo $slot_id; ?>">
                         <p class="day"><?php //echo $days_list[$day]; ?></p>
                         <strong><?php echo $days_list[$from_day]." ".$from_time." - ".$days_list[$to_day]." ".$to_time; ?></strong>
                         <?php  if($_hide_price_div != "on"){ ?>
                             <div class="price"><?php echo $slot_price; ?><?php echo $currency_symbol;?></div>
                         <?php } ?>    
                         <?php if(($free_places != "1" || $slots > 1) && $_POST["slot_price_type"] != "all_slot_price"){ ?>
                              <span><?php echo $free_places."/".$total_slots; esc_html_e(' slots available','listeo_core') ?></span>
                              
                         <?php } ?>
                         <input type="hidden" class="slot_avv" value="<?php echo $free_places;?>">
                         <input type="hidden" class="guest_slot" value="<?php echo $guest_slot;?>">
                     </label>
                 </div>
                 <?php 
                }
             } 
            // die;
            if($empty_slot == true){
              $data = ob_get_clean();
              $ajax_out = false;
            }else{
              $ajax_out = ob_get_clean();
            }
             
         } else {
             //no slots for today
         }
         wp_send_json_success( $ajax_out );
         
 }



    public static function ajax_listeo_bookings_renew_booking() {
        
        //check if booking can be renewed
        $booking_data =  self :: get_booking(sanitize_text_field($_POST['booking_id']));

      
        if($booking_data['status'] == 'expired') {
            $listing_type = get_post_meta ( $booking_data['listing_id'], '_listing_type', true );
            if( $listing_type == 'rental'){
                $has_free = self :: count_free_places( $booking_data['listing_id'], $booking_data['date_start'], $booking_data['date_end'] );   
                listeo_write_log($has_free);
                if($has_free <= 1){
                     wp_send_json_success( self :: set_booking_status( sanitize_text_field($_POST['booking_id']), 'confirmed') );             
                } else {
                    wp_send_json_error( );
                }
            } else {

                  $result = self :: get_bookings( $booking_data['date_start'], $booking_data['date_end'], array( 'listing_id' => $booking_data['listing_id'], 'type' => 'reservation' ) );
                  if(!empty($result)){
                    wp_send_json_error( );
                } else {
                    wp_send_json_success( self :: set_booking_status( sanitize_text_field($_POST['booking_id']), 'confirmed') );  
                }
                    
            } 

        }
                
            
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
          
                wp_send_json_success( self :: set_booking_status( sanitize_text_field($_POST['booking_id']), sanitize_text_field($_POST['status'])) );              
           
            
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
        if ( isset( $_POST['dashboard_type']) && $_POST['dashboard_type'] == 'user' ) {
            unset( $args['owner_id'] );
            //unset($args['status']);
            unset($args['listing_id']);
            
            $args['bookings_author'] = get_current_user_id();
            if($_POST['date_start']==''){
                $ajax_out = self :: get_newest_bookings( $args, $limit, $offset ); 
                $bookings_max_number = listeo_count_my_bookings_by_status(get_current_user_id(),$args['status']);    
            } else {
                $ajax_out = self :: get_bookings( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date', $limit, $offset, 'users' );    
                $bookings_max_number = self :: get_bookings_max( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date');
            }

        }

        $result = array();
        $template_loader = new Listeo_Core_Template_Loader;
        $max_number_pages = round($bookings_max_number/$limit);
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

    }


    /**
    * Insert booking with args
    *
    * @param  array $args list of parameters
    *
    */
    public static function insert_booking( $args )  {

        global $wpdb;

        if(isset($args['fields_data'])){
            $fields_data = $args['fields_data'];
        }else{
            $fields_data = null;
        }
        $author = $args['bookings_author'] ?? get_current_user_id();
       //  echo "<pre>"; print_r($args); die;
        $insert_data = array(
            'bookings_author' => $author,
            'owner_id' => $args['owner_id'],
            'listing_id' => $args['listing_id'],
            'date_start' => date( "Y-m-d H:i:s", strtotime( $args['date_start'] ) ),
            'date_end' => date( "Y-m-d H:i:s", strtotime( $args['date_end'] ) ),
            'recurrenceRule' =>  $args['recurrenceRule'],
            'recurrenceException' =>  $args['recurrenceException'],
            'comment' =>  $args['comment'],
            'type' =>  $args['type'],
            'fields_data' =>  $fields_data,
            'created' => current_time('mysql')
        );
        if ( isset( $args['parent_listing_id'] ) ) $insert_data['parent_listing_id'] = $args['parent_listing_id'];
        if ( isset( $args['order_id'] ) ) $insert_data['order_id'] = $args['order_id'];
        if ( isset( $args['expiring'] ) ) $insert_data['expiring'] = $args['expiring'];
        if ( isset( $args['status'] ) ) $insert_data['status'] = $args['status'];
        if ( isset( $args['price'] ) ) $insert_data['price'] = $args['price'];
        if ( isset( $args['booking_extra_data'] ) ) $insert_data['booking_extra_data'] = $args['booking_extra_data'];

        $wpdb -> insert( $wpdb->prefix . 'bookings_calendar', $insert_data );

        $bk_iddd = $wpdb->insert_id;

        $log_args = array(
            'action' => "booking_created",
            'related_to_id' => $args['owner_id'],
            'user_id' => $author,
            'post_id' => $bk_iddd
        );
        listeo_insert_log($log_args);

        self::addToGroup($args["listing_id"],$author);


        return  $bk_iddd;

    }
    public function setfid($booking_id, $res_id)
    {
        global $wpdb;
        $update_values = array();
        $update_values['first_event_id'] = $res_id;
        return $wpdb -> update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'id' => $booking_id ) );
    }
    /**
    * Set booking status - we changing booking status only by this function
    *
    * @param  array $args list of parameters
    *
    * @return number of deleted records
    */
    public static function set_booking_status( $booking_id, $status, $send_mail = true ) {

        

        global $wpdb;

        $booking_id = sanitize_text_field($booking_id);
        $status = sanitize_text_field($status);
        $booking_data = $wpdb -> get_row( 'SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar` WHERE `id`=' . esc_sql( $booking_id ), 'ARRAY_A' );
        if(!$booking_data){
            return;
        }

        $user_id = $booking_data['bookings_author']; 
        $owner_id = $booking_data['owner_id'];
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

                $update_values['status'] = 'waiting';

                // mail for user
                $mail_to_user_args = array(
                    'email' => $user_info->user_email,
                    'booking'  => $booking_data,
                    'mail_to_user'  => "buyer",
                );
                if($send_mail != false){
                    do_action('listeo_mail_to_user_waiting_approval',$mail_to_user_args);
                
                    // wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your reservation waiting for be approved by owner!', 'listeo_core' ) );
                    
                    // mail for owner
                    $mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        'booking'  => $booking_data,
                        'mail_to_user'  => "owner",
                    );
                    
                    do_action('listeo_mail_to_owner_new_reservation',$mail_to_owner_args);
                }
                // wp_mail( $owner_info->user_email, __( 'Welcome owner', 'listeo_core' ), __( 'In your panel waiting new reservation to be accepted!', 'listeo_core' ) );

            break;

            // this is status when listing is confirmed by owner and waiting to payment
            case 'pay_to_confirm':
            case 'confirmed' :

                $cash_payment = false;
                $owner_action = false;


                if(isset($_POST["owner_action"]) && $_POST["owner_action"] == true){

                    $_manual_invoice_payment = get_post_meta($booking_data['listing_id'], '_manual_invoice_payment', true);

                    if($_manual_invoice_payment =="only_show_invoice"){

                        $cash_payment = true;

                    }

                    $owner_action = true;

                }

                // get woocommerce product id
                $product_id = get_post_meta( $booking_data['listing_id'], 'product_id', true);

                // calculate when listing will be expired when will bo not pays
                $expired_after = get_post_meta( $booking_data['listing_id'], '_expired_after', true);
                $default_booking_expiration_time = get_option('listeo_default_booking_expiration_time');
                if(empty($expired_after)) {
                    $expired_after = $default_booking_expiration_time;
                }
                if(!empty($expired_after) && $expired_after > 0){
                    define( 'MY_TIMEZONE', (get_option( 'timezone_string' ) ? get_option( 'timezone_string' ) : date_default_timezone_get() ) );
                    date_default_timezone_set( MY_TIMEZONE );
                    $expiring_date = date( "Y-m-d H:i:s", strtotime('+'.$expired_after.' hours') );    
                }
                


                $instant_booking = get_post_meta( $booking_data['listing_id'], '_instant_booking', true);

                if($instant_booking) {

                    if($send_mail != false){

                        $mail_to_user_args = array(
                            'email' => $user_info->user_email,
                            'booking'  => $booking_data,
                             'mail_to_user'  => "buyer",
                        ); 
                        do_action('listeo_mail_to_user_instant_approval',$mail_to_user_args);
                        
                        // mail for owner
                        $mail_to_owner_args = array(
                            'email'     => $owner_info->user_email,
                            'booking'  => $booking_data,
                             'mail_to_user'  => "owner",
                        );
                        
                        do_action('listeo_mail_to_owner_new_instant_reservation',$mail_to_owner_args);
                    }    


                }
                $order_id = "";

                


               

                // for free listings
                if ( $booking_data['price'] == 0 || $cash_payment == true)
                {

                    // mail for user
                    //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), __( 'Your is paid!', 'listeo_core' ) );
                    $mail_args = array(
                    'email'     => $user_info->user_email,
                    'booking'  => $booking_data,
                    'mail_to_user'  => "buyer",
                    );
                    if($send_mail != false){
                         do_action('listeo_mail_to_user_free_confirmed',$mail_args);
                    }     

                    if($instant_booking) {
                        $update_values['status'] = 'paid';
                    }else{
                        $update_values['status'] = 'confirmed';
                    }

                    
                    $update_values['expiring'] = '';

                    //break;
                    
                }

                $first_name = (isset($comment->first_name) && !empty($comment->first_name)) ? $comment->first_name : get_user_meta( $user_id, "billing_first_name", true) ;
                
                $last_name = (isset($comment->last_name) && !empty($comment->last_name)) ? $comment->last_name : get_user_meta( $user_id, "billing_last_name", true) ;
                
                $phone = (isset($comment->phone) && !empty($comment->phone)) ? $comment->phone : get_user_meta( $user_id, "billing_phone", true) ;
                
                $email = (isset($comment->email) && !empty($comment->email)) ? $comment->email : get_user_meta( $user_id, "user_email", true) ;
                
                $billing_address_1 = (isset($comment->billing_address_1) && !empty($comment->billing_address_1)) ? $comment->billing_address_1 : '';
                
                $billing_city = (isset($comment->billing_city) && !empty($comment->billing_city)) ? $comment->billing_city : '';
                
                $billing_postcode = (isset($comment->billing_postcode) && !empty($comment->billing_postcode)) ? $comment->billing_postcode : '';
                
                $billing_country = (isset($comment->billing_country) && !empty($comment->billing_country)) ? $comment->billing_country : ''; 

                $coupon = (isset($comment->coupon) && !empty($comment->coupon)) ? $comment->coupon : false;

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



                if(empty($booking_data['order_id'])){

                    

                    global $woocommerce;


                
                // creating woocommerce order
                    $order = wc_create_order();




                    $comment = json_decode($booking_data['comment']);
                    
                    $price_before_coupons = (isset($comment->price) && !empty($comment->price)) ? $comment->price : $booking_data['price'];

                    if(isset($comment->total_tax)){

                        if(isset($comment->wihout_coupen_price) && $comment->wihout_coupen_price != ""){
                           //  $price_before_coupons = $comment->wihout_coupen_price;
                        }
                        $price_before_coupons = $price_before_coupons - $comment->total_tax;
                    }

                    $args['totals']['subtotal'] = $price_before_coupons;
                    $args['totals']['total'] = $price_before_coupons;

                    // echo "<pre>"; print_r($comment);
                    // echo "<pre>"; print_r($args); die;

                    
                    
                    
                    $order->add_product( wc_get_product( $product_id ), 1, $args );
                    if($coupon){
                        $coupons = explode(',',$coupon);
                        foreach ($coupons as $key => $new_coupon) {
                             
                              $order->apply_coupon( sanitize_text_field( $new_coupon ));
                        
                        }
                    }
                    
                   
                    $order->set_address( $address, 'billing' );
                    $order->set_address( $address, 'shipping' );
                    $order->set_billing_phone( $phone );
                    $order->set_customer_id($user_id);
                    $order->set_billing_email( $email );

                    $calculate_tax_for = array(
                        'country' => $billing_country, 
                        'state' => '', 
                        'postcode' => $billing_postcode, 
                        'city' => $billing_city
                    );
                    $cp_exist = false;
                    if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                        $cp_exist = true;
                    }
                    if(isset($comment->total_tax)){
                        
                        $item_fee = new WC_Order_Item_Fee();

                        $item_fee->set_name( "Total mva" ); // Generic fee name
                        $item_fee->set_amount( $comment->total_tax ); // Fee amount
                        $item_fee->set_tax_class( '' ); // default for ''
                        $item_fee->set_tax_status( 'taxable' ); // or 'none'
                        $item_fee->set_total( $comment->total_tax ); // Fee amount

                        // Calculating Fee taxes
                        $item_fee->calculate_taxes( $calculate_tax_for );

                        // Add Fee item to the order
                        $order->add_item( $item_fee );
                    }
                    $post_info = get_post($booking_data['listing_id']);
                    if(isset($post_info->post_author) && $post_info->post_author != ""){
                        $user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
                        if($user_currency_data != ""){
                            $order->set_currency($user_currency_data);
                        }
                    }

                    // if(isset($expiring_date)){
                    //     $order->set_date_paid( strtotime( $expiring_date ) );    
                    // }
                    
                    //TODO IF RENEWAL


                    $payment_url = $order->get_checkout_payment_url();
                    
                    //$order->apply_coupon($coupon_code);
                    
                    
                    $order->calculate_totals();
                    $order->save();
                    
                    $order->update_meta_data('booking_id', $booking_id);

                    $order->update_meta_data('owner_id', $owner_id);



                    
                    
                    

                    //$order->update_meta_data('billing_phone', $phone);
                    $order->update_meta_data('listing_id', $booking_data['listing_id']);

                    
                    if(isset($comment->service)){
                        
                        $order->update_meta_data('listeo_services', $comment->service);
                    }

                    $order->save_meta_data();



                   
                   
                    $update_values['order_id'] = $order->get_order_number();

                    $order_id = $order->id;
                
                }
                if(isset($expiring_date)){
                        $update_values['expiring'] = $expiring_date;
                }



                

                if ( ($booking_data['price'] == 0 || $cash_payment == true)  && isset($order->id))
                {
                    
                    $order =  new WC_Order( $order->id );


                    if($booking_id == ""){
                      $booking_id = get_post_meta( $order->id, 'booking_id', true );
                    }
                    $order =  new WC_Order( $order->id );

                    $update_values['order_id'] = $order->id;

                    if($cash_payment == true){
                        $update_values['fixed'] = "3";
                    }
                    $status = "paid";

                    $wpdb->update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'id' => $booking_id ) );

                    $order->update_status( 'completed' );
                    
                    if($owner_action == false){
                        $order_received_url =  $order->get_checkout_order_received_url();
                        $order_received_url = str_replace("/en", "", $order_received_url);
                        wp_redirect( $order_received_url );
                        exit;
                    }    
                }

                
                $update_values['status'] = $status;
                
                 // mail for user
                 //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'listeo_core' ), sprintf( __( 'Your reservation waiting for payment! Ple ase do it before %s hours. Here is link: %s', 'listeo_core' ), $expired_after, $payment_url  ) );
                if($cash_payment == false){
                     $mail_args = array(
                        'email'         => $user_info->user_email,
                        'booking'       => $booking_data,
                        'expiration'    => $expiring_date,
                        'payment_url'   => $payment_url,
                        'order_id'   => $order_id,
                         'mail_to_user'  => "buyer",
                        );
                    if($send_mail != false){

                        if($booking_data['price'] > 0){
                            do_action('listeo_mail_to_user_pay',$mail_args);
                        }
                       
                    }   
                 }    
                         
            break;





            // this is status when listing is confirmed by owner and already paid
            case 'paid' :

                // mail for owner
                //wp_mail( $owner_info->user_email, __( 'Welcome owner', 'listeo_core' ), __( 'Your client paid!', 'listeo_core' ) );

                if($send_mail != false){
                    $mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        'booking'  => $booking_data,
                         'mail_to_user'  => "owner",
                    );



                    do_action('listeo_mail_to_owner_paid',$mail_to_owner_args);

                    $mail_to_user_args = array(
                        'email'     => $user_info->user_email,
                        'booking'   => $booking_data,
                         'mail_to_user'  => "buyer",
                    );

                    
                    do_action('listeo_mail_to_user_paid',$mail_to_user_args);

                }    
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
                     'mail_to_user'  => "buyer",
                );
                if($send_mail != false){
                     do_action('listeo_mail_to_user_canceled',$mail_to_user_args);
                }     
                // delete order if exist
                if ( $booking_data['order_id'] )
                {
                    $order =  new WC_Order( $booking_data['order_id'] );
                    $order->update_status( 'cancelled', __( 'Order is cancelled.', 'listeo_core' ) );
                }
                $comment = json_decode($booking_data['comment']);
                if(isset( $comment->tickets )){
                       $tickets_from_order = $comment->tickets;
                
                        $sold_tickets = (int) get_post_meta( $booking_data['listing_id'],"_event_tickets_sold",true); 
                        
                        update_post_meta( $booking_data['listing_id'],"_event_tickets_sold",$sold_tickets-$tickets_from_order); 

                }
             
                $update_values['status'] = 'cancelled';
                $update_values['expiring'] = '';  

            break;
             // this is status when listing is confirmed by owner and already paid
            case 'deleted' :

               
               if($owner_id == $current_user_id || $user_id == $current_user_id  ){


                    if ( $booking_data['order_id'] )
                    {
                        $order =  new WC_Order( $booking_data['order_id'] );
                        //$order->update_status( 'cancelled', __( 'Order is cancelled.', 'listeo_core' ) );
                    }
                    return $wpdb->update( $wpdb->prefix . 'bookings_calendar', array("status" => "deleted", "status" => "expired"), array( 'id' => $booking_id ));
               
                   // return $wpdb -> delete( $wpdb->prefix . 'bookings_calendar', array( 'id' => $booking_id ) );
                }

            break;

             case 'expired' :

              

                 $update_values['status'] = 'expired';
                                             
                

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

        return $wpdb->update( $wpdb->prefix . 'bookings_calendar', array("status"=>"deleted"), $args);

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
           
            $date_now = strtotime("-1 days");
            $date_format    = strtotime($date);
    
            if($date_format>=$date_now) {
                
                self :: insert_booking( array(
                    'listing_id' => $listing_id,  
                    'type' => 'reservation',
                    'owner_id' => get_current_user_id(),
                    'date_start' => $date,
                    'date_end' => date( 'Y-m-d H:i:s', strtotime('+23 hours +59 minutes +59 seconds', strtotime($date) ) ),
                    'comment' =>  'owner reservations',
                    'order_id' => NULL,
                    'status' => 'owner_reservations'
                )); 
            }
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
    public static function calculate_price_old( $listing_id, $date_start, $date_end, $multiply, $services, $totalPrice = 1, $totalDays = 1 ) {
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
        $lastDay = new DateTime( $date_end ) ;

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

            if(isset($_SESSION['discountedprice']) && $_SESSION['discountedprice'] != ""){
                $totalPrice =   $_SESSION['discountedprice'] - $services_price;
            }else if(isset($_POST['price_algo']) && $_POST['price_algo'] != ""){

                $price_str = self::decryptString($_POST['price_algo']);

                $totalPrice =   $price_str - $services_price; 

            }else{
                return "null";
            }
            
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
		return $endprice;
        //return apply_filters('listeo_booking_price_calc',$endprice, $listing_id, $date_start, $date_end, $multiply , $services);
		//echo "test";
		//die;
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
    public static function calculate_price( $listing_id, $date_start, $date_end, $multiply = 1, $services, $coupon,$discount = 0,$tax = 0 ) {

        // get all special prices between two dates from listeo settings special prices
        $special_prices_results = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'special_price' ) );

        $listing_type = get_post_meta( $listing_id, '_listing_type', true);

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


        
        if($listing_type == 'event'){
            if(isset($services) && !empty($services)){
                $bookable_services = listeo_get_bookable_services($listing_id);
                $countable = array_column($services,'value');
              
                $i = 0;
                foreach ($bookable_services as $key => $service) {
                    
                    if(in_array(sanitize_title($service['name']),array_column($services,'service'))) { 
                        //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);
                        $sr_price = listeo_calculate_service_price($service, $multiply, 1, $countable[$i] );
                        if(isset($service['tax']) && $service['tax'] > 0){
                            $sr_price += (($service['tax']/100) * $sr_price);
                        }

                        $services_price +=  $sr_price;
                        
                       $i++;
                    }
                   
                
                } 
            }
            $price = $services_price+$reservation_price+$normal_price*$multiply;
            //coupon
            if(isset($coupon) && !empty($coupon)){
                $wc_coupon = new WC_Coupon($coupon);
                
                $coupons = explode(',',$coupon);
                foreach ($coupons as $key => $new_coupon) {
                    
                    $price = self::apply_coupon_to_price($price,$new_coupon);
                }
                
            }
            return $price;
        }
        // prepare dates for loop
        // TODO CHECK THIS
    // $format = "d/m/Y  H:i:s";
    //     $firstDay =  DateTime::createFromFormat($format, $date_start. '00:00:01' );
    //     $lastDay =  DateTime::createFromFormat($format, $date_end. '23:59:59')
    //     ;
    //
     
     
        // listeo_write_log('$date_start');
        // listeo_write_log($date_start);
        // listeo_write_log('$date_end');
        // listeo_write_log($date_end);
        if($listing_type != 'rental') {
            $firstDay = new DateTime( $date_start );
            $lastDay = new DateTime( $date_end . '23:59:59') ;
         
        } else {
            $firstDay = new DateTime( $date_start );
            $lastDay = new DateTime( $date_end );
            if(get_option('listeo_count_last_day_booking')){
                $lastDay = $lastDay->modify('+1 day');     
            }
            
        }
        $days_between = $lastDay->diff($firstDay)->format("%a");
        $days_count = ($days_between == 0) ? 1 : $days_between ;
        //fix for not calculating last day of leaving
        //if ( $date_start != $date_end ) $lastDay -> modify('-1 day');
        
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
                    $sr_price = listeo_calculate_service_price($service, $multiply, $days_count, $countable[$i] );
                    if(isset($service['tax']) && $service['tax'] > 0){
                        $sr_price += (($service['tax']/100) * $sr_price);
                    }
                    $services_price +=  $sr_price;
                    
                   $i++;
                }
               
            
            } 
        }
        
        $price += $reservation_price + $services_price;
        
        $without_service_price = $price - $services_price;
        
        


        //coupon
       


        



        if($discount > 0){
            $discount =  round($discount,2);

            $percentInDecimal = intval($discount) / 100;



            if(is_numeric($discount)){

                $_SESSION['discount_price'] = $without_service_price * $percentInDecimal;

                $_SESSION['post_price_without_service'] = $without_service_price;

                $without_service_price = $without_service_price - $_SESSION['discount_price'];

                $_SESSION['post_final_price_without_service'] = $without_service_price;

                $price = $without_service_price + $services_price;

                

            }
        }


        if($tax > 0){

            $without_service_price = $price - $services_price;

            $tax_value = ($tax / 100) * round($without_service_price);
            $taxprice = round($tax_value);
            $without_service_price = round($without_service_price + $tax_value);

            $price = $without_service_price + $services_price;

            $_SESSION["taxprice"] = $taxprice;
            $_SESSION["normalprice"] = $price - $taxprice;


        }

        if(isset($coupon) && !empty($coupon)){
            $wc_coupon = new WC_Coupon($coupon);
            
            $coupons = explode(',',$coupon);
            foreach ($coupons as $key => $new_coupon) {
                
                $price = self::apply_coupon_to_price($price,$new_coupon);
            }
            
        }

        $_SESSION['final_price'] = $price;


        
        
       // $endprice = round($price,2);

        $decimals = get_option('listeo_number_decimals',2);
        $endprice = round($price);

        return apply_filters('listeo_booking_price_calc',$price, $listing_id, $date_start, $date_end, $multiply , $services);

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
                'comment' =>  'owner reservations',
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
    public  function listeo_core_booking_old($post) {
        $_POST = $post;
        global $wpdb;



      




        if(!isset($_POST['value'])){
            
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
            
         


            if($_POST["billing_postcode"] == "" || $_POST["billing_postcode"] == "0000"){
                $_POST["billing_postcode"] = "1111";
            };

            

           

            

            

            
            if(!is_user_logged_in()){
                $user_dd = self::registerUser($_POST);

                if(isset($user_dd["success"]) && $user_dd["user_id"] > 0){
                    wp_set_current_user($user_dd["user_id"]);
                }else{
                    $error = true;
                    $message = __('User issue!', 'listeo_core');

                    return;
                }


            }
            
            

            if(get_current_user_id() != "" && get_current_user_id() > 0){

            }else{
                $error = true;
                $message = __('User issue!', 'listeo_core');

                return;
            }

            

            $profile_type = get_user_meta(get_current_user_id(), "profile_type",true);

            $has_profile_type = false;

            if($profile_type && $profile_type != ""){
                $has_profile_type = true;
            }

            $organization_number = get_user_meta(get_current_user_id(), "organization_number",true);

            $has_organization_number = false;

            if($organization_number && $organization_number != ""){
                $has_organization_number = true;
            }
            

            $lastname = get_user_meta(get_current_user_id(), "lastname",true);

            $has_lastname = false;

            if($lastname && $lastname != ""){
                $has_lastname = true;
            }



            




            if ( isset( $_POST['personalorcompany'] ) && $has_profile_type == false){

                if($_POST['personalorcompany'] == "Personal"){
                     update_user_meta(get_current_user_id(), 'profile_type', "personal"  );
                }else{
                      update_user_meta(get_current_user_id(), 'profile_type', "company"  );
                }

               
            }
            if ( isset( $_POST['organization_number'] ) && $_POST['organization_number']  != "" && $has_profile_type == false ){

                update_user_meta(get_current_user_id(), 'company_number', $_POST['organization_number']  );
            }

            if(isset($_POST["personalorcompany"]) && strtolower($_POST["personalorcompany"]) == "company"){

                $last_namee = get_user_meta(get_current_user_id(), 'last_name', true);

                if($last_namee == ""){
                    $_POST["lastname"] = "company";
                    update_user_meta(get_current_user_id(), 'last_name', "company");
                    update_user_meta(get_current_user_id(), 'billing_last_name', "company" );
                }
                
            }



            $_user_id = get_current_user_id();

            $data = json_decode( wp_unslash(htmlspecialchars_decode(wp_unslash($_POST['value']))), true );



           


             $fields_data = array();

              if(isset($_POST["fields"])){

                  foreach ($_POST["fields"] as $key_res => $res) {
                      foreach ($res as $key_index => $from) {
                           $fields_data[$key_index][$key_res] = $res[$key_index];
                      }
                  }
              }

              $fields_data = maybe_serialize($fields_data);

              


      



            if(isset($data["date_start"]) && isset($data["slot"])  ){

                $_booking_system_service = get_post_meta($data["listing_id"],"_booking_system_service",true);
                $_booking_slots = get_post_meta($data["listing_id"],"_booking_slots",true);

                if($_booking_system_service == "on" && !empty($_booking_slots)){

                	$slot_calculate_date = self::slot_calculate_date( $data['date_start'], $data['slot']);

    	            if(isset($slot_calculate_date["date_start"])){
    	            	$data['date_start'] =  $slot_calculate_date["date_start"];
    	                $data['date_end'] =  $slot_calculate_date["date_end"];
    	            }
                }    

            }
            if(isset($_POST["lastname"]) && $has_lastname == false){
                update_user_meta(get_current_user_id(), "last_name",$_POST["lastname"]);
            }

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
                $lastDay = new DateTime( $data['date_start'] ) ;

                $days_between = $lastDay->diff($firstDay)->format("%a");
                $days_count = ($days_between == 0) ? 1 : $days_between ;

                //since 1.3 change comment_service to json
                $countable = array_column($services,'value');
                $count_per_guest = get_post_meta($data['listing_id'],"_count_per_guest",true);
                //check count_per_guest

                //  if($count_per_guest || $data['listing_type'] == 'event' ){

                $guests = 1;
                if($count_per_guest){ 
                    if(isset($data['adults'])){
                        $guests = $data['adults'];
                    } else if(isset($data['tickets'])){
                        $guests = $data['tickets'];
                    } else {
                        $guests = 1;
                    }
                }    
                $i = 0;
                foreach ($bookable_services as $key => $service) {

                    if(in_array(sanitize_title($service['name']),array_column($services,'service'))) {
                        //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);
                        $sr_priceee = listeo_calculate_service_price($service, $data['adults'], $totalDays, $countable[$i] );

                        $sr_priceee += (($service['tax']/100) * $sr_priceee);

                        $comment_services[] =  array(
                            'service' => $service,
                            'guests' => $guests,
                            'days' => $days_count,
                            'countable' =>  $countable[$i],
                            'price' => $sr_priceee
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
            $fev = array();

            

            $country_code = "";

            if(isset($_POST["country_code"])){
                $country_code = $_POST["country_code"];
            }
            switch ( $listing_meta['_listing_type'][0] )
            {
                case 'event' :
                    

                    $comment= array(
                        'first_name'    => $_POST['firstname'],
                        'last_name'     => $_POST['lastname'],
                        'email'         => $_POST['email'],
                        'country_code'  => $country_code,
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
                    $booking_extra_data = null;

                    if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                        $coupon_data = $_POST["coupon_code"];

                        $booking_extra_data = array("coupon_data" => $coupon_data);
                       // $booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                        $booking_extra_data = json_encode($booking_extra_data);
                    }
                    

                    $booking_id = self :: insert_booking ( array (
                        'owner_id'      => $listing_owner,
                        'listing_id'    => $data['listing_id'],
                        'date_start'    => $data['date_start'],
                        'date_end'      => $data['date_start'],
                        'comment'       =>  json_encode ( $comment ),
                        'type'          =>  'reservation',
                        'fields_data'          =>  $fields_data,
                        'booking_extra_data'          =>  $booking_extra_data,
                        'price'         => self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'],$data['tickets'], $services ),
                    ));
                    $booking_from_info = array(

                        "booking_from" => "customer",
                        "booking_type" => "insert",
                        "date" => date("Y-m-d H:i:s"),
    
                    );
    
                    $wpdb->insert("bookings_calendar_meta",
                                array(
                                    "booking_id" => $booking_id,
                                    "meta_key" => "booking_from",
                                    "meta_value" => json_encode($booking_from_info)
                                )
                            );  

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
                        $booking_extra_data = null;

                        if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                            $coupon_data = $_POST["coupon_code"];

                            $booking_extra_data = array("coupon_data" => $coupon_data);
                                

                            $gift_data = array(); 
                            $gift_price = 0;                           
                            if($_POST["coupon_code"]) {
                                $coupons = explode(',',$_POST["coupon_code"]);
                                $gift_i = 0;
                                // echo "<pre>"; print_r($coupon); die;
                                foreach ($coupons as $key => $new_coupon) {
                                    if(class_exists("Class_Gibbs_Giftcard")){

                                        $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                        $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                        if($data2 && isset($data2["id"])){

                                            $gift_data[$gift_i]["code"] = $new_coupon;
                                            $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                            $gift_data[$gift_i]["booking_price"] = $price;

                                            $gift_price += $data2["remaining_saldo"];
                                            $gift_i++;
                                            continue;

                                        }
                                    }   
                                }
                                
                            }
                            if($gift_price > 0){


                                $price_ajax = $price;
                                
                                if($price_ajax > 0){
                                    
                                    if($gift_price > $price_ajax){
                                        $data->remaining_saldo = $gift_price - $price_ajax;
                                        $price_ajax = 0;
                                    }else{
                                        $price_ajax = $price_ajax - $gift_price;
                                    }

                                    $price = $price_ajax;
                                    
                                }

                                $booking_extra_data["gift_data"] = $gift_data;



                            }

                            
                            //$booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                            $booking_extra_data = json_encode($booking_extra_data);
                        }

                        $wihout_coupen_price = "";

                        if(isset($_POST["total_prices"])){
                            $wihout_coupen_price = $_POST["total_prices"];
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
                                'country_code'  => $country_code,
                                'phone' => $_POST['phone'],
                                'message'       => $_POST['message'],
                                //'childrens' => $data['childrens'],
                                'adults' => $data['adults'],
                                'service'       => $comment_services,
                                'billing_address_1' => $billing_address_1,
                                'billing_postcode'  => $billing_postcode,
                                'billing_city'      => $billing_city,
                                'billing_country'   => $billing_country,
                                'wihout_coupen_price'   => $wihout_coupen_price,
                                // 'tickets' => $data['tickets']
                            )),
                            'type' =>  'reservation',
                            'fields_data' =>  $fields_data,
                            'booking_extra_data' =>  $booking_extra_data,
                            'price' => $price,
                        ));
                        $booking_from_info = array(

                            "booking_from" => "customer",
                            "booking_type" => "insert",
                            "date" => date("Y-m-d H:i:s"),
        
                        );
        
                        $wpdb->insert("bookings_calendar_meta",
                                    array(
                                        "booking_id" => $booking_id,
                                        "meta_key" => "booking_from",
                                        "meta_value" => json_encode($booking_from_info)
                                    )
                                );  

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

                    $_POST["owner_action"] = true;
                    $status = apply_filters( 'listeo_service_default_status', 'waiting');
                    if($instant_booking == 'check_on' || $instant_booking == 'on') { 
                        $status = 'confirmed'; 

                        if(get_option('listeo_instant_booking_require_payment')){
                            $status = "pay_to_confirm";
                        }
                    }
                    $recurrenceException = "";

                    if(isset($data["expp"])  && !empty($data["expp"])){
                        $recurrenceException = implode(",", $data["expp"]);
                    }



                    // time picker booking
                    if($data['slot']=='[" - NaN:00","undefined|0"]')
                    {
                        unset($data['slot']);
                    }

                   


          



                  
                    if ( ! isset( $data['slot'] ) )
                    {
                        $data['normal_price'] = self :: calculate_normal_price( $data['listing_id'],  $data['date_start'], $data['date_end'],$multiply, $services);
                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest" , true );
                        //check count_per_guest
                        $taxPercentage = get_post_meta ( $data['listing_id'], '_tax', true);
                        $tax = ($taxPercentage / 100) * round($data['normal_price']) ;
                        $data['taxprice'] = round($tax);
                        $multiply = 1;
                        if($count_per_guest){ 
                            if(isset($data['adults'])) $multiply = $data['adults'];
                        }
                        if($multiply>1){

                          

                           // $price = self :: calculate_price_old( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply , $services  );
                           $price = self :: calculate_price_old( $data['listing_id'],  $data['date_start'], $data['date_end'],$multiply,0, $data['normal_price']+$data['taxprice'], $data['totalDays']  );
                        } else {
                            $price = self :: calculate_price_old( $data['listing_id'], $data['date_start'], $data['date_end'] ,1, $services );
                        }

                        if($price === "null"){

                            $error = true;
                            $message = __('Please contact to administrator.', 'listeo_core');
                            break;

                        }



                        $rec = "";
                        $av_days = "";
                        $endrecdate = "";
                        

                        if(isset($data["rec"]) && $data["rec"] != "" && isset($data["av_days"]) && $data["av_days"] > 0 ){
                            $av_days = $data["av_days"];
                            $rec = $data["rec"];
                            $endrecdate = $data["endrecdate"];
                        }
                        $purpose = "";
                        if(isset($data["purpose"]) && $data["purpose"] != ""){
                             $purpose = $data["purpose"];
                        }

                        

                       


                        



                       
                       // die;

                       

                        $hour_end = ( isset($data['_hour_end']) && !empty($data['_hour_end']) ) ? $data['_hour_end'] : $data['_hour'] ;
                        $ip = 0;
                        if(!empty($data['sub']))
                        {
                            $booking_extra_data = null;

                            if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                                $coupon_data = $_POST["coupon_code"];

                                $booking_extra_data = array("coupon_data" => $coupon_data);
                                    

                                $gift_data = array(); 
                                $gift_price = 0;                           
                                if($_POST["coupon_code"]) {
                                    $coupons = explode(',',$_POST["coupon_code"]);
                                    $gift_i = 0;
                                    // echo "<pre>"; print_r($coupon); die;
                                    foreach ($coupons as $key => $new_coupon) {
                                        if(class_exists("Class_Gibbs_Giftcard")){

                                            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                            $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                            if($data2 && isset($data2["id"])){

                                                $gift_data[$gift_i]["code"] = $new_coupon;
                                                $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                                $gift_data[$gift_i]["booking_price"] = $price;

                                                $gift_price += $data2["remaining_saldo"];
                                                $gift_i++;
                                                continue;

                                            }
                                        }   
                                    }
                                    
                                }
                                if($gift_price > 0){


                                    $price_ajax = $price;
                                    
                                    if($price_ajax > 0){
                                        
                                        if($gift_price > $price_ajax){
                                            $data->remaining_saldo = $gift_price - $price_ajax;
                                            $price_ajax = 0;
                                        }else{
                                            $price_ajax = $price_ajax - $gift_price;
                                        }

                                        $price = $price_ajax;
                                        
                                    }

                                    $booking_extra_data["gift_data"] = $gift_data;



                                }

                                
                                //$booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                                $booking_extra_data = json_encode($booking_extra_data);
                            }
                            foreach($data['sub'] as $sb)
                            {
                                $liid = $sb['listing_id'];

                                if($liid == ""){
                                    continue;
                                }
                                $wihout_coupen_price = "";

                                if(isset($_POST["total_prices"])){
                                    $wihout_coupen_price = $_POST["total_prices"];
                                }
                                $sddp = array (
                                    'owner_id' => $listing_owner,
                                    'listing_id' => $liid,
                                    'parent_listing_id' => $data['listing_id'],
                                    'date_start' => $data['date_start'],
                                    'date_end' => $data['date_end'],
                                    'recurrenceRule' => $rec,
                                    'recurrenceException' => $recurrenceException,
                                    'comment' =>  json_encode( 
                                        array( 
                                            'first_name' => $_POST['firstname'],
                                            'last_name' => $_POST['lastname'],
                                            'email' => $_POST['email'],
                                            'country_code'  => $country_code,
                                            'phone' => $_POST['phone'],
                                            'adults' => $data['adults'],
                                            'message'       => $_POST['message'],
                                            'service'       => $comment_services,
                                            'billing_address_1' => $billing_address_1,
                                            'billing_postcode'  => $billing_postcode,
                                            'billing_city'      => $billing_city,
                                            'billing_country'   => $billing_country,
                                            'total_tax'         => $_POST['taxPrice'],
                                            'sub_listing'       => $data['sub'],
                                            'av_days'       => $av_days,
                                            'endrecdate'       => $endrecdate,
                                            'purpose'       => $purpose,
                                            'wihout_coupen_price'       => $wihout_coupen_price,
                                            )),
                                    'type' =>  'reservation',
                                    'fields_data'  =>  $fields_data,
                                    'booking_extra_data'  =>  $booking_extra_data,
                                    'price' => $price,
                                );
                              //  echo print_r($sddp);
                              //  die;
                                $bkid = self :: insert_booking ( $sddp );
                                if($ip>0)
                                {
                                    $update_values = array();
                                    $bking = self::get_booking($fev[0]);

                                   // echo "<pre>";print_r($bking); die;
                                    $up_oid = $bking['order_id'];
                                    $update_values['order_id'] = $up_oid;
                                    $update_values['status'] = $bking['status'];
                                    $update_values['fixed'] = $bking['fixed'];
                                    $wpdb -> update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'id' => $bkid ) );
                                    //$changed_status = self :: set_booking_status ( $bkid, $status, false );
                                }
                                else
                                {
                                    $changed_status = self :: set_booking_status ( $bkid, $status );
                                }

                                update_post_meta($bkid, 'discount-type',  $_POST['discount-type']);
                                $fev[] = $bkid;
                                $ip++;
                            }
                            foreach($fev as $fv)
                            {

                                self :: setfid ( $fv, $fev[0] );
                            }
                            //echo "<pre>"; print_r($fev); die;

                          //  echo "<pre>"; print_r($fev); die;
                            $booking_id = $fev[0];

                            $booking_from_info = array(

                                "booking_from" => "customer",
                                "booking_type" => "insert",
                                "date" => date("Y-m-d H:i:s"),
            
                            );
            
                            $wpdb->insert("bookings_calendar_meta",
                                        array(
                                            "booking_id" => $booking_id,
                                            "meta_key" => "booking_from",
                                            "meta_value" => json_encode($booking_from_info)
                                        )
                                    );  
                        }
                        else
                        {
                            $booking_extra_data = null;

                            if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                                $coupon_data = $_POST["coupon_code"];

                                $booking_extra_data = array("coupon_data" => $coupon_data);
                                

                                $gift_data = array(); 
                                $gift_price = 0;                           
                                if($_POST["coupon_code"]) {
                                    $coupons = explode(',',$_POST["coupon_code"]);
                                    $gift_i = 0;
                                    // echo "<pre>"; print_r($coupon); die;
                                    foreach ($coupons as $key => $new_coupon) {
                                        if(class_exists("Class_Gibbs_Giftcard")){

                                            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                            $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                            if($data2 && isset($data2["id"])){

                                                $gift_data[$gift_i]["code"] = $new_coupon;
                                                $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                                $gift_data[$gift_i]["booking_price"] = $price;

                                                $gift_price += $data2["remaining_saldo"];
                                                $gift_i++;
                                                continue;

                                            }
                                        }   
                                    }
                                    
                                }
                                if($gift_price > 0){


                                    $price_ajax = $price;
                                    
                                    if($price_ajax > 0){
                                        
                                        if($gift_price > $price_ajax){
                                            $data->remaining_saldo = $gift_price - $price_ajax;
                                            $price_ajax = 0;
                                        }else{
                                            $price_ajax = $price_ajax - $gift_price;
                                        }

                                        $price = $price_ajax;
                                        
                                    }

                                    $booking_extra_data["gift_data"] = $gift_data;



                                }

                                
                                //$booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                                $booking_extra_data = json_encode($booking_extra_data);
                            }
                            $wihout_coupen_price = "";

                            if(isset($_POST["total_prices"])){
                                $wihout_coupen_price = $_POST["total_prices"];
                            }
                            

                                $booking_id = self :: insert_booking ( array (
                                    'owner_id' => $listing_owner,
                                    'listing_id' => $data['listing_id'],
                                    'date_start' => $data['date_start'],
                                    'date_end' => $data['date_end'],
                                    'recurrenceRule' => $rec,
                                    'recurrenceException' => $recurrenceException,
                                    'comment' =>  json_encode ( array( 'first_name' => $_POST['firstname'],
                                        'last_name' => $_POST['lastname'],
                                        'email' => $_POST['email'],
                                        'country_code'  => $country_code,
                                        'phone' => $_POST['phone'],
                                        'adults' => $data['adults'],
                                        'message'       => $_POST['message'],
                                        'service'       => $comment_services,
                                        'billing_address_1' => $billing_address_1,
                                        'billing_postcode'  => $billing_postcode,
                                        'billing_city'      => $billing_city,
                                        'billing_country'   => $billing_country,
                                        'total_tax'         => $_POST['taxPrice'],
                                        'av_days'       => $av_days,
                                        'endrecdate'       => $endrecdate,
                                        'purpose'       => $purpose,
                                        'wihout_coupen_price'       => $wihout_coupen_price,

                                    )),
                                    'type' =>  'reservation',
                                    'fields_data'    =>  $fields_data,
                                    'booking_extra_data'    =>  $booking_extra_data,
                                    'price' => $price,
                                ));
                                $booking_from_info = array(

                                    "booking_from" => "customer",
                                    "booking_type" => "insert",
                                    "date" => date("Y-m-d H:i:s"),
                
                                );
                
                                $wpdb->insert("bookings_calendar_meta",
                                            array(
                                                "booking_id" => $booking_id,
                                                "meta_key" => "booking_from",
                                                "meta_value" => json_encode($booking_from_info)
                                            )
                                        );  

                                $changed_status = self :: set_booking_status ( $booking_id, $status );

                                update_post_meta($booking_id, 'discount-type',  $_POST['discount-type']);
                        }

                    } else {
                        


                        // here when we have enabled slots

                        $free_places = self :: count_free_places( $data['listing_id'], $data['date_start'], $data['date_end'], $data['slot'], "confirm_step" );



                       
                      
                      




                        if ( $free_places > 0 )
                        {

                            /*slot = json_decode( wp_unslash($data['slot']) );



                        

                            // converent hours to mysql format
                            $hours = explode( ' - ', $slot[0] );
                            $hour_start = date( "H:i:s", strtotime( $hours[0] ) );
                            $hour_end = date( "H:i:s", strtotime( $hours[1] ) );*/

                            $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest" , true );
                            //check count_per_guest
                            $services = (isset($data['services'])) ? $data['services'] : false ;
                            if($count_per_guest){

                                $multiply = 1;
                                if(isset($data['adults'])) $multiply = $data['adults'];

                               // $price = self :: calculate_price_old( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services  );
                                $price = self :: calculate_price_old( $data['listing_id'],  $data['date_start'], $data['date_end'],$multiply,$services  );
                            } else {
                                $price = self :: calculate_price_old( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services );
                            }

                            if($price === "null"){

                                $error = true;
                                $message = __('Please contact to administrator.', 'listeo_core');
                                break;

                            }
                            

                            //echo "<pre>";print_r($data); 

                           // echo $price; die;



                            $rec = "";
                            $av_days = "";
                            $endrecdate = "";

                            if(isset($data["rec"]) && $data["rec"] != "" && isset($data["av_days"]) && $data["av_days"] > 0 ){
                               // $price = $price * $data["av_days"];
                                $av_days = $data["av_days"];
                                $rec = $data["rec"];
                                $endrecdate = $data["endrecdate"];
                            }
                            $purpose = "";
                            if(isset($data["purpose"]) && $data["purpose"] != ""){
                                 $purpose = $data["purpose"];
                            }
                            $booking_extra_data = null;

                            if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){

                                $coupon_data = $_POST["coupon_code"];

                                $booking_extra_data = array("coupon_data" => $coupon_data);
                                

                                $gift_data = array(); 
                                $gift_price = 0;                           
                                if($_POST["coupon_code"]) {
                                    $coupons = explode(',',$_POST["coupon_code"]);
                                    $gift_i = 0;
                                    // echo "<pre>"; print_r($coupon); die;
                                    foreach ($coupons as $key => $new_coupon) {
                                        if(class_exists("Class_Gibbs_Giftcard")){

                                            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                            $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                            if($data2 && isset($data2["id"])){

                                                $gift_data[$gift_i]["code"] = $new_coupon;
                                                $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                                $gift_data[$gift_i]["booking_price"] = $price;

                                                $gift_price += $data2["remaining_saldo"];
                                                $gift_i++;
                                                continue;

                                            }
                                        }   
                                    }
                                    
                                }
                                if($gift_price > 0){

                                   


                                    $price_ajax = $price;
                                    
                                    if($price_ajax > 0){
                                        
                                        if($gift_price > $price_ajax){
                                            $data->remaining_saldo = $gift_price - $price_ajax;
                                            $price_ajax = 0;
                                        }else{
                                            $price_ajax = $price_ajax - $gift_price;
                                        }

                                        $price = $price_ajax;
                                        
                                    }

                                    $booking_extra_data["gift_data"] = $gift_data;

                                    



                                }

                                
                                //$booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                                
                                $booking_extra_data = json_encode($booking_extra_data);
                            }
                            $wihout_coupen_price = "";

                            if(isset($_POST["total_prices"])){
                                $wihout_coupen_price = $_POST["total_prices"];
                            }


                            $booking_id = self :: insert_booking ( array (
                                'owner_id' => $listing_owner,
                                'listing_id' => $data['listing_id'],
                                'date_start' => $data['date_start'],
                                'date_end' => $data['date_end'],
                                'recurrenceRule' => $rec,
                                'recurrenceException' => $recurrenceException,
                                'comment' =>  json_encode ( array( 'first_name' => $_POST['firstname'],
                                    'last_name' => $_POST['lastname'],
                                    'email' => $_POST['email'],
                                    'country_code'  => $country_code,
                                    'phone' => $_POST['phone'],
                                    //'childrens' => $data['childrens'],
                                    'adults' => $data['adults'],
                                    'message'       => $_POST['message'],
                                    'service'       => $comment_services,
                                    'billing_address_1' => $billing_address_1,
                                    'billing_postcode'  => $billing_postcode,
                                    'billing_city'      => $billing_city,
                                    'billing_country'   => $billing_country,
                                    'total_tax'         => $_POST['taxPrice'],
                                    'av_days'       => $av_days,
                                    'endrecdate'       => $endrecdate,
                                    'purpose'       => $purpose,
                                    'wihout_coupen_price'       => $wihout_coupen_price,

                                )),
                                'type' =>  'reservation',
                                'fields_data'    =>  $fields_data,
                                'booking_extra_data'    =>  $booking_extra_data,
                                'price' => $price,
                            ));

                            $wpdb->insert("bookings_calendar_meta",
                                            array(
                                              "booking_id" => $booking_id,
                                              "meta_key" => "number_of_guests",
                                              "meta_value" => $data['adults']
                                            )
                                        );

                            $booking_from_info = array(

                                "booking_from" => "customer",
                                "booking_type" => "insert",
                                "date" => date("Y-m-d H:i:s"),
            
                            );
            
                            $wpdb->insert("bookings_calendar_meta",
                                        array(
                                            "booking_id" => $booking_id,
                                            "meta_key" => "booking_from",
                                            "meta_value" => json_encode($booking_from_info)
                                        )
                                    );            




                            //discount tyoe for mottate
                            update_post_meta($booking_id, 'discount-type',  $_POST['discount-type']);
                            $status = apply_filters( 'listeo_service_slots_default_status', 'waiting');
                            if($instant_booking == 'check_on' || $instant_booking == 'on') {
                                $status = 'confirmed'; 
                                if(get_option('listeo_instant_booking_require_payment')){
                                    $status = "pay_to_confirm";
                                }
                            }

                            $changed_status = self :: set_booking_status ( $booking_id, $status );

                            
                        } else
                        {

                            $error = true;
                            $message = __('Those dates are not available.', 'listeo_core');

                        }

                    }

                    break;
            }

            // if($booking_id != ""){
            //     $result_bkkk = $wpdb->get_row("SELECT order_id FROM `" . $wpdb->prefix . "bookings_calendar` where id=".$booking_id);

            //     if(isset($result_bkkk->order_id) && $result_bkkk->order_id != "" && $result_bkkk->order_id != 0){
            //         echo "<pre>"; print_r($result_bkkk);die;
            //     }

            // }

            // when we have database problem with statuses
            if ( ! isset($changed_status) )
            {
                $message = __( 'We have some technical problem, please try again later or contact administrator.', 'listeo_core' );
                $error = true;
            }

            switch ( $status )  {

                case 'waiting' :

                    $message = esc_html__( 'Your booking is waiting for confirmation.', 'listeo_core' );
                    $submessage = ' ';
                    break;

                case 'confirmed' :

                    if(isset($price) && $price > 0){
                        $message = esc_html__( 'We are waiting for your payment.', 'listeo_core' );
                        $submessage = ' ';
                    } else {
                        $message = esc_html__( ' ', 'listeo_core' );
                        $submessage = '';
                    }

                   
                    break;
                 case 'pending' :

                    $message = esc_html__( 'We are waiting for your payment. We take you to payment page', 'listeo_core' );
                    $submessage = esc_html__( 'Payment Pending.', 'listeo_core' );

                    break;

                case 'cancelled' :

                    $message = esc_html__( 'Your booking was cancelled', 'listeo_core' );
                    $submessage = ' ';    
                    break;
            }

            
            $template_loader = new Listeo_Core_Template_Loader;
            if(isset($booking_id)){
                $booking_data =  self :: get_booking($booking_id);
                $order_id = $booking_data['order_id'];
                $order_id = (isset($booking_data['order_id'])) ? $booking_data['order_id'] : false ;

                if($order_id && $order_id != "" && $order_id != 0){
                    $order_data = wc_get_order($order_id);
                    $order_status_d  = $order_data->get_status();
                    if($order_status_d == "completed"){

                        $order_received_url =  $order_data->get_checkout_order_received_url();
                        $order_received_url = str_replace("/en", "", $order_received_url);
                        wp_redirect( $order_received_url );
                        exit;

                    }
                }
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
        if ( isset( $data['slot']) && isset( $data['date_start']) && $data['slot'] != "")
        {

            $_booking_system_service = get_post_meta($data["listing_id"],"_booking_system_service",true);
            $_booking_slots = get_post_meta($data["listing_id"],"_booking_slots",true);

            if($_booking_system_service == "on" && !empty($_booking_slots)){
            
                $slot_calculate_date = self::slot_calculate_date( $data['date_start'], $data['slot']);

                if(isset($slot_calculate_date["date_start"])){
                	$data['date_start'] =  $slot_calculate_date["date_start"];
                    $data['date_end'] =  $slot_calculate_date["date_end"];
                }
            }    


        } else if ( isset( $data['_hour'] ) ) {
            $hour = $data['_hour'];
            if(isset($data['_hour_end'])) {
                $hour_end = $data['_hour_end'];
            }
        }





        $template_loader = new Listeo_Gibbs_Template_Loader;

        // prepare some data to template
        $data['submitteddata'] = htmlspecialchars($_POST['value']);

        //check listin type
        $count_per_guest = get_post_meta($data['listing_id'],"_count_per_guest",true);
        //check count_per_guest

        //  if($count_per_guest || $data['listing_type'] == 'event' ){

        $multiply = 1;
        if($count_per_guest){ 
            if(isset($data['adults'])) $multiply = $data['adults'];
        }
        if(isset($data['tickets'])) $multiply = $data['tickets'];

     //   $data['price'] = self :: calculate_price_old( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services);

        // } else {

        //     $data['price'] = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services  );
        // }

        if(isset($hour)){
            $data['_hour'] = $hour;
        }
        if(isset($hour_end)){
            $data['_hour_end'] = $hour_end;
        }
        $sb = array();
        foreach($data['sub'] as $sls)
        {
            $sb[] = $sls['listing_id'];
        }
        if(isset($_POST["rec"]) && $_POST["rec"] != "" && isset($_POST["av_days"]) && $_POST["av_days"] > 0 ){
            $data['date_end'] = $data["endrecdate"];
        }
 
        $data['normal_price'] = self :: calculate_normal_price( $data['listing_id'],  $data['date_start'], $data['date_end'],$multiply, $services,1,1, $sb);
        $data['services_price'] = self :: calculate_price_services($data['listing_id'],  $data['date_start'], $data['date_end'],$multiply, $services, $totalPrice = 1, $totalDays = 1 );

        

        

        $discount_percentage = 0;
        if(isset($_POST["discount"]) && $_POST["discount"] != ""){
            $discountss = get_post_meta($data['listing_id'],"_discounts_user",true); 

            if($discountss && is_array($discountss) && count($discountss) > 0){

                foreach ($discountss as $key => $discount) {
                    if($discount["discount_name"] == $_POST["discount"]){
                        $discount_percentage = $discount["discount_value"];
                        break;
                    }
                }
            }

        }
        $discount_percentage =  round($discount_percentage,2);

        
        $percentInDecimal = intval($discount_percentage) / 100;

        if(is_numeric($discount_percentage)){
            $data['discount_price'] = $data['normal_price'] * $percentInDecimal;

            $data['post_price'] = $data['normal_price'];

            $data['normal_price'] = $data['normal_price'] - ($data['normal_price'] * $percentInDecimal);
        }

        $taxPercentage = get_post_meta ( $data['listing_id'], '_tax', true);
       
        $data['price'] = self :: calculate_price_old( $data['listing_id'],  $data['date_start'], $data['date_end'],$multiply,0, $data['normal_price'], $data['totalDays']  );

        $data['price'] = $data['price'] + $data['services_price'];

        if(isset($_POST["rec"]) && $_POST["rec"] != "" && isset($_POST["av_days"]) && $_POST["av_days"] > 0 ){
            

            
            
            $data['price'] = $data['price'] * $_POST["av_days"];



            $tax = ($taxPercentage / 100) * round($data['price']) ;
            $data['taxprice'] = round($tax);

            $data['price'] = $data['price'] + $data['taxprice'];



            

            if(isset($_SESSION['rec_price']) && $_SESSION['rec_price'] != ""){
                $data['price'] = $_SESSION['rec_price'];
            }
            $tax = ($taxPercentage / 100) * round($data['price']) ;
            $data['taxprice'] = round($tax);

            $data['post_price'] = $data['price'] - $data['taxprice'] ;
            $data['normal_price'] = $data['price'] -  $data['taxprice'] ;




            $data['av_days'] = $_POST["av_days"];
            $data['rec'] = $data["rec"];
            $data['endrecdate'] = $data["endrecdate"];
            $data['date_end'] = $data["endrecdate"];

        }else{

            $tax = ($taxPercentage / 100) * round($data['normal_price']) ;
            $data['taxprice'] = round($tax);
            $data['price'] = $data['price'] + $data['taxprice'];
            $ajax_out['price'] = round($ajax_out['price'] + $tax);
        }


        if($_POST["coupon_code"] != ""){
            $coupon = (isset($_POST['coupon_code'])) ? $_POST['coupon_code'] : false ;
            if($coupon) {
                $total_price1 = -1;
                $coupons = explode(',',$coupon);
                foreach ($coupons as $key => $new_coupon) {
                    $total_price1 = self::apply_coupon_to_price($data['price'],$new_coupon);
                }
                if($total_price1 >= 0){
                    $data['rec_price'] = $data['price'];
                    $data['coupon_price'] = ($data['price'] - $total_price1);
                    // $ajax_out['price'] = number_format_i18n($total_price1,$decimals);
                    $data['price'] = $total_price1;
                }
                
            }
        }
       



       //echo "<pre>"; print_r($data); die;
       // die;

       
       
        $template_loader->set_template_data( $data )->get_template_part( 'booking-old' );

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


    public static function slot_calculate_date($date_start,$slot) {

            $slott = explode("|", $slot);

            $from_day = $slott[0];
            $from_time = $slott[1];
            $to_day = $slott[2];
            $to_time = $slott[3];
            $slot_price = $slott[4];
            $slots = $slott[5];
            $slot_id = $slott[6];

            $days_list = array(
                        1   => __('Monday','listeo_core'),
                        2   => __('Tuesday','listeo_core'),
                        3   => __('Wednesday','listeo_core'),
                        4   => __('Thursday','listeo_core'),
                        5   => __('Friday','listeo_core'),
                        6   => __('Saturday','listeo_core'),
                        7   => __('Sunday','listeo_core')
                    );
            $hour_start = date( "H:i:s", strtotime( $from_time ) );
            $hour_end = date( "H:i:s", strtotime( $to_time ) );
            

            $dayofweek = date('w', strtotime($date_start));
            $ts = strtotime($date_start);
            // find the year (ISO-8601 year number) and the current week
            $year = date('o', $ts);
            $week = date('W', $ts);

            $days = array();
            // print week for the current date
            for($i = 1; $i <= 7; $i++) {
                // timestamp from ISO week date format
                $ts = strtotime($year.'W'.$week.$i);
                $days[$i] = date("Y-m-d", $ts);
            }



            foreach ($days as $key_day => $day) {
                if($key_day == $from_day){
                    $date_start = $day." ".$hour_start;
                }
                if($key_day == $to_day){
                    $date_end = $day." ".$hour_end;
                }
            }

           /* $hour_start = date( "H:i:s", strtotime( $from_time ) );

            $hour_end = date( "H:i:s", strtotime( $to_time ) );
            

            $dayofweek = date('w', strtotime($date_start));

            $ts = strtotime($date_start);
           
            $year = date('o', $ts);
            $week = date('W', $ts);

            $ts2 = strtotime($year.'W'.$week.$dayofweek);

            $date_start_d = date("Y-m-d", $ts2);

            $days = array();

            $date_cr = $date_start_d;

            $days[1] = $date_cr;

            if($from_day > $to_day){
                $next_arrays =  [];
                $next_arrays[1] = 8; 
                $next_arrays[2] = 9; 
                $next_arrays[3] = 10; 
                $next_arrays[4] = 11; 
                $next_arrays[5] = 12; 
                $next_arrays[6] = 13; 
                $next_arrays[7] = 14; 
                $to_day = $next_arrays[$to_day];

                for($i = 2; $i <= 14; $i++) {

                    $date_cr =  date('Y-m-d', strtotime($date_cr. ' + 1 days'));

                    $days[$i] = $date_cr;


                }

            }else{

                for($i = 2; $i <= 7; $i++) {

                    $date_cr =  date('Y-m-d', strtotime($date_cr. ' + 1 days'));

                    $days[$i] = $date_cr;

                }


            }

*/

           
            // print week for the current date

            

            


            



            foreach ($days as $key_day => $day) {
                if($key_day == $from_day){
                    $date_start = $day." ".$hour_start;
                }
                if($key_day == $to_day){
                    $date_end = $day." ".$hour_end;
                }
            }

           // echo "<pre>"; print_r($to_day); die;

            return array("date_start"=>$date_start,"date_end"=>$date_end);
    }

    /**
     * User booking shortcode
    * 
    * 
     */
    public static function listeo_core_booking() {
        

        global $wpdb;
       
      
        if(!isset($_POST['value'])){
            return wp_redirect(home_url());
            esc_html_e("You shouldn't be here1 :)",'listeo_core');
            return;
        }


       



        $data = json_decode( htmlspecialchars_decode(wp_unslash($_POST['value'])), true );
        if(!$data){
            $data = json_decode( wp_unslash(htmlspecialchars_decode(wp_unslash($_POST['value']))), true );
        }

        $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];

        if($REMOTE_ADDR != ""){

            $Gibbs_Booking_Calendar = new Gibbs_Booking_Calendar;

            $slot = false;

            // if(isset($_POST["slot"]) && $_POST["slot"] != ""){
            //     $slot = true;
            //     if($Gibbs_Booking_Calendar->check_recent_booking_slot($_POST['listing_id'],$REMOTE_ADDR,$data)){

            //     }else{
            //         $data_booking_slot = array();
            //         $data_booking_slot["booking_time"] = current_time('timestamp');
            //         $data_booking_slot["booking_server"] = $REMOTE_ADDR;
            //         $data_booking_slot["booking_date"] = $data["date_end"];
            //         update_post_meta($_POST['listing_id'], '_booking_slots_details', json_encode($data_booking_slot));
            //     }
            // }else{

                if ($Gibbs_Booking_Calendar->check_recent_booking($_POST['listing_id'],$REMOTE_ADDR)) { ?>
                    <div class="main_check_recent_bk" style="margin-top:10px;">
                        <div class="alert alert-info" role="alert" style="color:#fff;background: #FFC800;">
                        Someone is booking this time right now. Please try another time or try again in few minutes.
                        </div>
                        <button 
                            class="btn btn-primary" 
                            style="font-size:17px" 
                            onclick="window.location.href='<?php echo esc_url(wp_get_referer() ?: home_url()); ?>';">
                            Try again
                        </button>
                    </div>
                <?php    
                    return;
                } else {
                    // Proceed with booking
                    update_post_meta($_POST['listing_id'], '_booking_time', current_time('timestamp'));
                    update_post_meta($_POST['listing_id'], '_booking_server', $REMOTE_ADDR);
               }
           // }

        }

        if(!isset($_POST['listing_id'])){
            $_POST['listing_id'] = $data["listing_id"];
        }


        $listing_type =  get_post_meta ( $_POST['listing_id'], '_listing_type', true ); 
        $_booking_system_weekly_view = get_post_meta ( $_POST['listing_id'], '_booking_system_weekly_view', true );


        if($_booking_system_weekly_view == "0"){
            $_booking_system_weekly_view = "";
        }

        



        if($listing_type == "service"){

        // here we adding booking into database
           echo self::listeo_core_booking_old($_POST);

        }else{    
                if ( isset($_POST['confirmed']) )
                {
                    if($_POST["billing_postcode"] == "" || $_POST["billing_postcode"] == "0000"){
                        $_POST["billing_postcode"] = "1111";
                    }
                   
        

                    

                    if(!is_user_logged_in()){
                        $user_dd = self::registerUser($_POST);

                        if(isset($user_dd["success"]) && $user_dd["user_id"] > 0){
                            wp_set_current_user($user_dd["user_id"]);
                        }else{
                            $error = true;
                            $message = __('User issue!', 'listeo_core');

                            return;
                        }


                    }

                    


                    $fields_data = array();

                      if(isset($_POST["fields"])){

                          foreach ($_POST["fields"] as $key_res => $res) {
                              foreach ($res as $key_index => $from) {
                                   $fields_data[$key_index][$key_res] = $res[$key_index];
                              }
                          }
                      }

                      $fields_data = maybe_serialize($fields_data);



                  


                    $_user_id = get_current_user_id();

                    $data = json_decode( wp_unslash(htmlspecialchars_decode(wp_unslash($_POST['value']))), true );

                    if(isset($_POST["personalorcompany"]) && strtolower($_POST["personalorcompany"]) == "company"){

                        $last_namee = get_user_meta(get_current_user_id(), 'last_name', true);

                        if($last_namee == ""){
                            $_POST["lastname"] = "company";
                            update_user_meta(get_current_user_id(), 'last_name', "company");
                            update_user_meta(get_current_user_id(), 'billing_last_name', "company" );
                        }
                        
                    }


                    
                    $error = false;
                    
                    $listing_type =  get_post_meta ( $data['listing_id'], '_listing_type', true );
                    
                    $_booking_system_weekly_view = get_post_meta ( $data['listing_id'], '_booking_system_weekly_view', true );


                    if($_booking_system_weekly_view == "0"){
                        $_booking_system_weekly_view = "";
                    }

                    if($listing_type == "service"  && $_booking_system_weekly_view != ""){

                         echo self::listeo_core_booking_old($_POST);

                    }else{    

                            $services = (isset($data['services'])) ? $data['services'] : false ;
                            $comment_services = false;


                            if(!empty($services)){
                                $currency_abbr = get_option( 'listeo_currency' );
                                $currency_postion = get_option( 'listeo_currency_postion' );
                                $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
                                //$comment_services = '<ul>';
                                $comment_services = array();
                                $bookable_services = listeo_get_bookable_services( $data['listing_id'] );
                                
                                if ( $listing_type == 'rental' ) {

                                    $firstDay = new DateTime( $data['date_start'] );
                                    $lastDay = new DateTime( $data['date_end'] . '23:59:59') ;

                                    $days_between = $lastDay->diff($firstDay)->format("%a");
                                    $days_count = ($days_between == 0) ? 1 : $days_between ;
                                    
                                } else {
                                    
                                    $days_count = 1;
                                
                                }
                                
                                //since 1.3 change comment_service to json
                                $countable = array_column($services,'value');

                                $count_per_guest = get_post_meta($data['listing_id'],"_count_per_guest",true);
                                //check count_per_guest

                                //  if($count_per_guest || $data['listing_type'] == 'event' ){

                                $guests = 1;
                                if($count_per_guest){ 
                                   if(isset($data['adults'])){
                                        $guests = $data['adults'];
                                    } else if(isset($data['tickets'])){
                                        $guests = $data['tickets'];
                                    } else {
                                        $guests = 1;
                                    }
                                }
                                

                          
                                $i = 0;
                                foreach ($bookable_services as $key => $service) {
                                    
                                    if(in_array(sanitize_title($service['name']),array_column($services,'service'))) { 

                                        $sr_priceee = listeo_calculate_service_price($service, $guests, $days_count, $countable[$i] );

                                        $sr_priceee += (($service['tax']/100) * $sr_priceee);
                                     
                                     
                                        $comment_services[] =  array(
                                            'service' => $service, 
                                            'guests' => $guests, 
                                            'days' => $days_count, 
                                            'countable' =>  $countable[$i],
                                            'price' => $sr_priceee 
                                        );
                                        
                                       $i++;
                                    
                                    }
                                   
                                
                                }                  
                            } //eof if services

                            $listing_meta = get_post_meta ( $data['listing_id'], '', true );
                            // detect if website was refreshed
                            $instant_booking = get_post_meta(  $data['listing_id'], '_instant_booking', true );
                            
                            
                            if ( get_transient('listeo_last_booking'.$_user_id) == $data['listing_id'] . ' ' . $data['date_start']. ' ' . $data['date_end'] )
                            {

                                if($listing_meta['_listing_type'][0] == 'rental'){

                                    $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` where date_start = '".$data['date_start']." 12:00:00' AND date_end = '".$data['date_end']." 12:00:00' AND bookings_author = ".$_user_id." AND status =='paid'");
                                    if(count($result) > 0){
                                        $template_loader = new Listeo_Core_Template_Loader;
                                
                                        $template_loader->set_template_data( 
                                            array( 
                                                'error' => true,
                                                'message' => __('Sorry, it looks like you\'ve already made that reservation', 'listeo_core')
                                            ) )->get_template_part( 'booking-success' ); 
                                        
                                        return; 
                                    }
                                }else{
                                   $template_loader = new Listeo_Core_Template_Loader;
                                
                                    $template_loader->set_template_data( 
                                        array( 
                                            'error' => true,
                                            'message' => __('Sorry, it looks like you\'ve already made that reservation', 'listeo_core')
                                        ) )->get_template_part( 'booking-success' ); 
                                    
                                    return; 
                                }


                                
                            }

                            set_transient( 'listeo_last_booking'.$_user_id, $data['listing_id'] . ' ' . $data['date_start']. ' ' . $data['date_end'], 60 * 15 );
                            
                            // because we have to be sure about listing type
                            $listing_meta = get_post_meta ( $data['listing_id'], '', true );

                            $listing_owner = get_post_field( 'post_author', $data['listing_id'] );

                            $billing_address_1 = (isset($_POST['billing_address_1'])) ? sanitize_text_field($_POST['billing_address_1']) : false ;
                            $billing_postcode = (isset($_POST['billing_postcode'])) ? sanitize_text_field($_POST['billing_postcode']) : false ;
                            $billing_city = (isset($_POST['billing_city'])) ? sanitize_text_field($_POST['billing_city']) : false ;
                            $billing_country = (isset($_POST['billing_country'])) ? sanitize_text_field($_POST['billing_country']) : false ;
                            $coupon = (isset($_POST['coupon_code'])) ? sanitize_text_field($_POST['coupon_code']) : false ;

                            $country_code = "";

                            if(isset($_POST["country_code"])){
                                $country_code = $_POST["country_code"];
                            }

                            

                           
                           
                           
                            switch ( $listing_meta['_listing_type'][0] ) 
                            {
                                case 'event' :
                                    

                                    $comment= array( 
                                        'first_name'    => sanitize_text_field($_POST['firstname']),
                                        'last_name'     => sanitize_text_field($_POST['lastname']),
                                        'email'         => sanitize_email($_POST['email']),
                                        'country_code'  => $country_code,
                                        'phone'         => sanitize_text_field($_POST['phone']),
                                        'message'       => sanitize_textarea_field($_POST['message']),
                                        'tickets'       => sanitize_text_field($data['tickets']),
                                        'service'       => $comment_services,
                                        'billing_address_1' => $billing_address_1,
                                        'billing_postcode'  => $billing_postcode,
                                        'billing_city'      => $billing_city,
                                        'billing_country'   => $billing_country,
                                        'coupon'        => $coupon,
                                        'price'         => self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'],$data['tickets'], $services, '' )
                                    );

                                    $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'],$data['tickets'], $services, $coupon );

                                    $booking_extra_data = null;

                                    if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                                        $coupon_data = $_POST["coupon_code"];

                                        $booking_extra_data = array("coupon_data" => $coupon_data);
                                

                                        $gift_data = array(); 
                                        $gift_price = 0;                           
                                        if($_POST["coupon_code"]) {
                                            $coupons = explode(',',$_POST["coupon_code"]);
                                            $gift_i = 0;
                                            // echo "<pre>"; print_r($coupon); die;
                                            foreach ($coupons as $key => $new_coupon) {
                                                if(class_exists("Class_Gibbs_Giftcard")){

                                                    $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                                    $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                                    if($data2 && isset($data2["id"])){

                                                        $gift_data[$gift_i]["code"] = $new_coupon;
                                                        $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                                        $gift_data[$gift_i]["booking_price"] = $price;

                                                        $gift_price += $data2["remaining_saldo"];
                                                        $gift_i++;
                                                        continue;

                                                    }
                                                }   
                                            }
                                            
                                        }
                                        if($gift_price > 0){


                                            $price_ajax = $price;
                                            
                                            if($price_ajax > 0){
                                                
                                                if($gift_price > $price_ajax){
                                                    $data->remaining_saldo = $gift_price - $price_ajax;
                                                    $price_ajax = 0;
                                                }else{
                                                    $price_ajax = $price_ajax - $gift_price;
                                                }

                                                $price = $price_ajax;
                                                
                                            }

                                            $booking_extra_data["gift_data"] = $gift_data;



                                        }

                                        
                                        //$booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                                        $booking_extra_data = json_encode($booking_extra_data);


                                    }


                                    
                                    $booking_id = self :: insert_booking ( array (
                                        'owner_id'      => $listing_owner,
                                        'listing_id'    => $data['listing_id'],
                                        'date_start'    => $data['date_start'],
                                        'date_end'      => $data['date_start'],
                                        'comment'       =>  json_encode ( $comment ),
                                        'booking_extra_data'  =>  $booking_extra_data,
                                        'type'          =>  'reservation',
                                        'fields_data'    =>  $fields_data,
                                        'price'         => $price,
                                    ));
                                    $booking_from_info = array(

                                        "booking_from" => "customer",
                                        "booking_type" => "insert",
                                        "date" => date("Y-m-d H:i:s"),
                    
                                    );
                    
                                    $wpdb->insert("bookings_calendar_meta",
                                                array(
                                                    "booking_id" => $booking_id,
                                                    "meta_key" => "booking_from",
                                                    "meta_value" => json_encode($booking_from_info)
                                                )
                                            );  

                                    $already_sold_tickets = (int) get_post_meta($data['listing_id'],'_event_tickets_sold',true);
                                    $sold_now = $already_sold_tickets + $data['tickets'];
                                    update_post_meta($data['listing_id'],'_event_tickets_sold',$sold_now);

                                    $status = apply_filters( 'listeo_event_default_status', 'waiting');
                                    if($instant_booking == 'check_on' || $instant_booking == 'on') { 
                                        $status = 'confirmed'; 
                                        if(get_option('listeo_instant_booking_require_payment')){
                                            $status = "pay_to_confirm";
                                        }
                                    }
                                    
                                    $changed_status = self :: set_booking_status ( $booking_id, $status );

                                break;

                                case 'rental' :

                                    // get default status
                                    $status = apply_filters( 'listeo_rental_default_status', 'waiting');
                                    
                                    $booking_hours = self::wpk_change_booking_hours(  $data['date_start'], $data['date_end'] );
                                    $date_start = $booking_hours[ 'date_start' ];
                                    $date_end = $booking_hours[ 'date_end' ];
                                
                                    // count free places
                                    $free_places = self :: count_free_places( $data['listing_id'], $data['date_start'], $data['date_end'] );


                    

                                    if ( $free_places > 0 ) 
                                    {

                                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest" , true ); 
                                        //check count_per_guest


                                            $multiply = 1;
                                            if($count_per_guest){ 
                                               if(isset($data['adults'])) $multiply = $data['adults'];
                                            }

                                            $discount_percentage = 0;
                                            if(isset($_POST["discount"]) && $_POST["discount"] != ""){
                                                $discountss = get_post_meta($data['listing_id'],"_discounts_user",true); 

                                                if($discountss && is_array($discountss) && count($discountss) > 0){

                                                    foreach ($discountss as $key => $discount) {
                                                        if($discount["discount_name"] == $_POST["discount"]){
                                                            $discount_percentage = $discount["discount_value"];
                                                            break;
                                                        }
                                                    }
                                                }

                                            }

                                            $tax = 0;

                                            $taxPercentage = get_post_meta ( $data['listing_id'], '_tax', true);

                                            if($taxPercentage && $taxPercentage > 0){

                                                $tax = $taxPercentage;

                                            }


                                            $price = self :: calculate_price( $data['listing_id'],  $data['date_start'], $data['date_end'], $multiply, $services, $coupon, $discount_percentage,$tax );


                                          
                                            
                                            $price_before_coupons = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services, '', $discount_percentage,$tax   );

                                            if($_SESSION["taxprice"] != ""){
                                                $taxPrice = $_SESSION["taxprice"];
                                            }else{
                                                $taxPrice = "";
                                            }
                                            $booking_extra_data = null;

                                            if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                                                $coupon_data = $_POST["coupon_code"];
        
                                                $booking_extra_data = array("coupon_data" => $coupon_data);
                                

                                                $gift_data = array(); 
                                                $gift_price = 0;                           
                                                if($_POST["coupon_code"]) {
                                                    $coupons = explode(',',$_POST["coupon_code"]);
                                                    $gift_i = 0;
                                                    // echo "<pre>"; print_r($coupon); die;
                                                    foreach ($coupons as $key => $new_coupon) {
                                                        if(class_exists("Class_Gibbs_Giftcard")){

                                                            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                                            $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                                            if($data2 && isset($data2["id"])){

                                                                $gift_data[$gift_i]["code"] = $new_coupon;
                                                                $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                                                $gift_data[$gift_i]["booking_price"] = $price;

                                                                $gift_price += $data2["remaining_saldo"];
                                                                $gift_i++;
                                                                continue;

                                                            }
                                                        }   
                                                    }
                                                    
                                                }
                                                if($gift_price > 0){


                                                    $price_ajax = $price;
                                                    
                                                    if($price_ajax > 0){
                                                        
                                                        if($gift_price > $price_ajax){
                                                            $data->remaining_saldo = $gift_price - $price_ajax;
                                                            $price_ajax = 0;
                                                        }else{
                                                            $price_ajax = $price_ajax - $gift_price;
                                                        }

                                                        $price = $price_ajax;
                                                        
                                                    }

                                                    $booking_extra_data["gift_data"] = $gift_data;



                                                }

                                                
                                               // $booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                                                $booking_extra_data = json_encode($booking_extra_data);
                                            }
                                            $wihout_coupen_price = "";

                                            if(isset($_POST["total_prices"])){
                                                $wihout_coupen_price = $_POST["total_prices"];
                                            }
   
                                        $booking_id = self :: insert_booking ( array (
                                            'owner_id' => $listing_owner,
                                            'listing_id' => $data['listing_id'],
                                            'date_start' => $data['date_start']." 12:00:00",
                                            'date_end' => $data['date_end']." 12:00:00",
                                            'comment' =>  json_encode ( array( 
                                                'first_name'    => sanitize_text_field($_POST['firstname']),
                                                'last_name'     => sanitize_text_field($_POST['lastname']),
                                                'email'         => sanitize_email($_POST['email']),
                                                'country_code'  => $country_code,
                                                'phone'         => sanitize_text_field($_POST['phone']),
                                                'message'       => sanitize_textarea_field($_POST['message']),
                                                //'childrens' => $data['childrens'],
                                                'adults'            => sanitize_text_field($data['adults']),
                                                'service'           => $comment_services,
                                                'billing_address_1' => $billing_address_1,
                                                'billing_postcode'  => $billing_postcode,
                                                'billing_city'      => $billing_city,
                                                'billing_country'   => $billing_country,
                                                'coupon'            => $coupon,
                                                'price'             => $price,
                                                'discount_price'    => $_SESSION['discount_price'],
                                                'total_tax'         => $taxPrice,
                                                'wihout_coupen_price'         => $wihout_coupen_price,
                                               // 'tickets' => $data['tickets']
                                            )),
                                            'type' =>  'reservation',
                                            'fields_data'    =>  $fields_data,
                                            'booking_extra_data'    =>  $booking_extra_data,
                                            'price' => $price,
                                        ));
                                        $booking_from_info = array(

                                            "booking_from" => "customer",
                                            "booking_type" => "insert",
                                            "date" => date("Y-m-d H:i:s"),
                        
                                        );
                        
                                        $wpdb->insert("bookings_calendar_meta",
                                                    array(
                                                        "booking_id" => $booking_id,
                                                        "meta_key" => "booking_from",
                                                        "meta_value" => json_encode($booking_from_info)
                                                    )
                                                );  


                    
                                        $status = apply_filters( 'listeo_event_default_status', 'waiting');
                                        if($instant_booking == 'check_on' || $instant_booking == 'on') {
                                            $status = 'confirmed'; 
                                            if(get_option('listeo_instant_booking_require_payment')){
                                                $status = "pay_to_confirm";
                                            }
                                        }else{

                                            // if($price == 0){
                                            //     $status = "confirmed";
                                            // }

                                        }
                                        
                                        update_post_meta($booking_id, 'discount-type',  $_POST['discount-type']);
                                        $changed_status = self :: set_booking_status ( $booking_id, $status );
                                        
                                    } else
                                    {

                                        $error = true;
                                        $message = __('Unfortunately those dates are not available anymore.', 'listeo_core');

                                    }

                                    break;

                                case 'service' :


                                    $discount_percentage = 0;
                                    if(isset($_POST["discount"]) && $_POST["discount"] != ""){
                                        $discountss = get_post_meta($data['listing_id'],"_discounts_user",true); 

                                        if($discountss && is_array($discountss) && count($discountss) > 0){

                                            foreach ($discountss as $key => $discount) {
                                                if($discount["discount_name"] == $_POST["discount"]){
                                                    $discount_percentage = $discount["discount_value"];
                                                    break;
                                                }
                                            }
                                        }

                                    }



                                    $status = apply_filters( 'listeo_service_default_status', 'waiting');
                                    if($instant_booking == 'check_on' || $instant_booking == 'on') { $status = 'confirmed'; 
                                        if(get_option('listeo_instant_booking_require_payment')){
                                            $status = "pay_to_confirm";
                                        }
                                    }
                                   
                                    // time picker booking
                                    if ( ! isset( $data['slot'] ) ) 
                                    {
                                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest" , true ); 
                                        //check count_per_guest

                                        if($count_per_guest){

                                            $multiply = 1;
                                            if(isset($data['adults'])) $multiply = $data['adults'];

                                            $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply , $services, $coupon,$discount_percentage  );
                                            $price_before_coupons = self :: calculate_price( $data['listing_id'],  $data['date_start'], $data['date_end'], $multiply, $services, '',$discount_percentage   );
                                        } else {
                                            $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'] ,1, $services, $coupon ,$discount_percentage);
                                            $price_before_coupons = self :: calculate_price( $data['listing_id'],  $data['date_start'], $data['date_end'], 1, $services, '',$discount_percentage   );
                                        }
                                
                                        $hour_end = ( isset($data['_hour_end']) && !empty($data['_hour_end']) ) ? $data['_hour_end'] : $data['_hour'] ;
                                        $booking_extra_data = null;

                                        if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                                            $coupon_data = $_POST["coupon_code"];
    
                                            $booking_extra_data = array("coupon_data" => $coupon_data);
                                

                                            $gift_data = array(); 
                                            $gift_price = 0;                           
                                            if($_POST["coupon_code"]) {
                                                $coupons = explode(',',$_POST["coupon_code"]);
                                                $gift_i = 0;
                                                // echo "<pre>"; print_r($coupon); die;
                                                foreach ($coupons as $key => $new_coupon) {
                                                    if(class_exists("Class_Gibbs_Giftcard")){

                                                        $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                                        $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                                        if($data2 && isset($data2["id"])){

                                                            $gift_data[$gift_i]["code"] = $new_coupon;
                                                            $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                                            $gift_data[$gift_i]["booking_price"] = $price;

                                                            $gift_price += $data2["remaining_saldo"];
                                                            $gift_i++;
                                                            continue;

                                                        }
                                                    }   
                                                }
                                                
                                            }
                                            if($gift_price > 0){


                                                $price_ajax = $price;
                                                
                                                if($price_ajax > 0){
                                                    
                                                    if($gift_price > $price_ajax){
                                                        $data->remaining_saldo = $gift_price - $price_ajax;
                                                        $price_ajax = 0;
                                                    }else{
                                                        $price_ajax = $price_ajax - $gift_price;
                                                    }

                                                    $price = $price_ajax;
                                                    
                                                }

                                                $booking_extra_data["gift_data"] = $gift_data;



                                            }

                                            
                                            //$booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                                            $booking_extra_data = json_encode($booking_extra_data);
                                        }
                                        $wihout_coupen_price = "";

                                        if(isset($_POST["total_prices"])){
                                            $wihout_coupen_price = $_POST["total_prices"];
                                        }

                                        $booking_id = self :: insert_booking ( array (
                                            'owner_id' => $listing_owner,
                                            'listing_id' => $data['listing_id'],
                                            'date_start' => $data['date_start'] . ' ' . $data['_hour'] . ':00',
                                            'date_end' => $data['date_end'] . ' ' . $hour_end . ':00',
                                            'comment' =>  json_encode ( array( 
                                                'first_name'    => sanitize_text_field($_POST['firstname']),
                                                'last_name'     => sanitize_text_field($_POST['lastname']),
                                                'email'         => sanitize_email($_POST['email']),
                                                'country_code'  => $country_code,
                                                'phone'         => sanitize_text_field($_POST['phone']),
                                                'message'       => sanitize_text_field($_POST['message']),
                                                'adults'        => sanitize_text_field($data['adults']),
                                                'message'       => sanitize_textarea_field($_POST['message']),
                                                'service'       => $comment_services,
                                                'billing_address_1' => $billing_address_1,
                                                'billing_postcode'  => $billing_postcode,
                                                'billing_city'      => $billing_city,
                                                'billing_country'   => $billing_country,
                                                'coupon'   => $coupon,
                                                'price'         => $price,
                                                'discount_price'    => $_SESSION['discount_price'],
                                                'wihout_coupen_price'    => $wihout_coupen_price,
                                               
                                            )),
                                            'type' =>  'reservation',
                                            'fields_data'    =>  $fields_data,
                                            'booking_extra_data'    =>  $booking_extra_data,
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

                                                $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services, $coupon,$discount_percentage   );
                                                $price_before_coupons = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services, '',$discount_percentage   );

                                            } else {
                                                $price = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services,  $coupon,$discount_percentage );
                                                $price_before_coupons = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services, '',$discount_percentage   );
                                            }

                                            $booking_extra_data = null;

                                            if(isset($_POST["coupon_code"]) && $_POST["coupon_code"] != ""){
                                                $coupon_data = $_POST["coupon_code"];
        
                                                $booking_extra_data = array("coupon_data" => $coupon_data);
                                

                                                $gift_data = array(); 
                                                $gift_price = 0;                           
                                                if($_POST["coupon_code"]) {
                                                    $coupons = explode(',',$_POST["coupon_code"]);
                                                    $gift_i = 0;
                                                    // echo "<pre>"; print_r($coupon); die;
                                                    foreach ($coupons as $key => $new_coupon) {
                                                        if(class_exists("Class_Gibbs_Giftcard")){

                                                            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                                                            $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

                                                            if($data2 && isset($data2["id"])){

                                                                $gift_data[$gift_i]["code"] = $new_coupon;
                                                                $gift_data[$gift_i]["coupon_balance"] = $data2["remaining_saldo"];
                                                                $gift_data[$gift_i]["booking_price"] = $price;

                                                                $gift_price += $data2["remaining_saldo"];
                                                                $gift_i++;
                                                                continue;

                                                            }
                                                        }   
                                                    }
                                                    
                                                }
                                                if($gift_price > 0){


                                                    $price_ajax = $price;
                                                    
                                                    if($price_ajax > 0){
                                                        
                                                        if($gift_price > $price_ajax){
                                                            $data->remaining_saldo = $gift_price - $price_ajax;
                                                            $price_ajax = 0;
                                                        }else{
                                                            $price_ajax = $price_ajax - $gift_price;
                                                        }

                                                        $price = $price_ajax;
                                                        
                                                    }

                                                    $booking_extra_data["gift_data"] = $gift_data;



                                                }

                                                
                                                //$booking_extra_data = array_map('utf8_encode', $booking_extra_data);
                                                $booking_extra_data = json_encode($booking_extra_data);
                                            }
                                            $wihout_coupen_price = "";

                                            if(isset($_POST["total_prices"])){
                                                $wihout_coupen_price = $_POST["total_prices"];
                                            }

                                            $booking_id = self :: insert_booking ( array (
                                                'owner_id' => $listing_owner,
                                                'listing_id' => $data['listing_id'],
                                                'date_start' => $data['date_start'] . ' ' . $hour_start,
                                                'date_end' => $data['date_end'] . ' ' . $hour_end,
                                                'comment' =>  json_encode ( array( 'first_name' => $_POST['firstname'],
                                                    'last_name'     => sanitize_text_field($_POST['lastname']),
                                                    'email'         => sanitize_email($_POST['email']),
                                                    'country_code'  => $country_code,
                                                    'phone'         => sanitize_text_field($_POST['phone']),
                                                    //'childrens' => $data['childrens'],
                                                    'adults'        => sanitize_text_field($data['adults']),
                                                    'message'       => sanitize_textarea_field($_POST['message']),
                                                    'service'       => $comment_services,
                                                    'billing_address_1' => $billing_address_1,
                                                    'billing_postcode'  => $billing_postcode,
                                                    'billing_city'      => $billing_city,
                                                    'billing_country'   => $billing_country,
                                                    'coupon'   => $coupon,
                                                    'price'         => $price,
                                                    'discount_price'    => $_SESSION['discount_price'],
                                                    'wihout_coupen_price'    => $wihout_coupen_price,
                                                   
                                                )),
                                                'type' =>  'reservation',
                                                'fields_data'    =>  $fields_data,
                                                'booking_extra_data'    =>  $booking_extra_data,
                                                'price' => $price,
                                            ));

                      
                                            $status = apply_filters( 'listeo_service_slots_default_status', 'waiting');
                                            if($instant_booking == 'check_on' || $instant_booking == 'on') { $status = 'confirmed'; 
                                         if(get_option('listeo_instant_booking_require_payment')){
                                            $status = "pay_to_confirm";
                                        }}
                                            
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

                                    $message = esc_html__( 'Your booking is waiting for confirmation.', 'listeo_core' );

                                    break;

                                case 'confirmed' :
                                    if($price > 0){
                                        $message = esc_html__( 'We are waiting for your payment.', 'listeo_core' );
                                    } else {

                                    }
                                    

                                    break;

                                case 'pay_to_confirm':

                                    $message = '';
                                break;

                                case 'cancelled' :

                                    $message = esc_html__( 'Your booking was cancelled', 'listeo_core' );

                                    break;
                            }
                            $submessage = '';



                            
                            $template_loader = new Listeo_Core_Template_Loader;
                            if(isset($booking_id)){
                                $booking_data =  self :: get_booking($booking_id);
                                $order_id = $booking_data['order_id'];
                                $order_id = (isset($booking_data['order_id'])) ? $booking_data['order_id'] : false ;
                                if(isset($booking_data["status"]) && $booking_data["status"]== "paid"){
                                    $status = "paid";
                                    $message = esc_html__( 'Your booking has been confirmed', 'listeo_core' );
                                    $submessage = ' ';
                                }
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
                
                if( isset($data['coupon']) && !empty($data['coupon'])){
                    $coupon = $data['coupon'];
                } else {
                    $coupon = false;
                }
                $template_loader = new Listeo_Core_Template_Loader;

                // prepare some data to template
                $data['submitteddata'] = htmlspecialchars($_POST['value']);

                //check listin type
                $count_per_guest = get_post_meta($data['listing_id'],"_count_per_guest",true); 
                //check count_per_guest

              //  if($count_per_guest || $data['listing_type'] == 'event' ){

                    $multiply = 1;

                    if($count_per_guest){

                        if(isset($data['adults'])) $multiply = $data['adults'];
                    }

                    if(isset($data['tickets'])) $multiply = $data['tickets'];

                    $discount_percentage = 0;
                    if(isset($_POST["discount"]) && $_POST["discount"] != ""){
                        $discountss = get_post_meta($data['listing_id'],"_discounts_user",true); 

                        if($discountss && is_array($discountss) && count($discountss) > 0){

                            foreach ($discountss as $key => $discount) {
                                if($discount["discount_name"] == $_POST["discount"]){
                                    $discount_percentage = $discount["discount_value"];
                                    break;
                                }
                            }
                        }

                    }

                    $tax = 0;

                    $taxPercentage = get_post_meta ( $data['listing_id'], '_tax', true);

                    if($taxPercentage && $taxPercentage > 0){

                        $tax = $taxPercentage;

                    }
                    if($data['date_start'] == $data['date_end']){

                    	$listing_type = get_post_meta($data['listing_id'], '_listing_type', true);

                    	if($listing_type == "rental"){
                    		$end_date_time = new DateTime( $data['date_end'] );

				        
					        $end_date_time->add( DateInterval::createfromdatestring('+1 day') );
					       
	                        $data['date_end'] = $end_date_time->format( 'Y-m-d' );

	                       // die("here");
                    	}
                    	
				        
                    }

                   // echo "<pre>"; print_r($data); die;



                    $data['price'] = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services, '', $discount_percentage,$tax);  



                    
                    if(!empty($coupon)){
                       $data['price_sale'] = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services, $coupon, $discount_percentage,$tax );   
  
                    }



                    if($_SESSION["taxprice"] != ""){
                        $data['taxprice'] = $_SESSION["taxprice"];
                        $data['normalprice'] = $_SESSION["normalprice"];
                    }
                    
                // } else {
                    
                //     $data['price'] = self :: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'], 1, $services  );
                // }

                if(isset($hour)){
                    $data['_hour'] = $hour;
                }
                if(isset($hour_end)){
                    $data['_hour_end'] = $hour_end;
                }

                $template_loader = new Listeo_Gibbs_Template_Loader;

                $template_loader->set_template_data( $data )->get_template_part( 'booking-old' ); 
                    

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
            $pages = round($booking_max/$limit);
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
        $args_default = array (
            'bookings_author' => get_current_user_id(),
            'type' => 'reservation'
        );
        $args =  apply_filters( 'listeo_core_my_bookings_args', $args_default);

     
        $limit =  get_option('posts_per_page');

        
        $booking_max = listeo_count_my_bookings(get_current_user_id());
        $pages = round($booking_max/$limit);
        if(isset($_GET['status']) ){
            $booking_max = listeo_count_my_bookings_by_status(get_current_user_id(),$_GET['status']); 
            $pages = round($booking_max/$limit);
            $args['status'] = $_GET['status'];
        }
        $bookings = self :: get_newest_bookings($args,$limit );

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

    /**
    * Booking Paid
    *
    * @param number $order_id with id of order
    * 
     */
    public static function booking_paid( $order_id ) {
        global $wpdb;

        $send_mail = true;

        $disable_order_mail = get_post_meta($order->ID,"disable_order_mail",true);

        if($disable_order_mail == "true"){
            $send_mail = false;
        }

        $booking_data = $wpdb -> get_results( 'SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar` WHERE `order_id`=' . esc_sql( $order_id ), 'ARRAY_A' );
        $fid = 0;
        foreach($booking_data as $bd)
        {
            $fe_id = $bd['first_event_id'];
            if(!empty($fe_id))
            {
                $fid = $fe_id;
            }
            $bid = $bd['id'];
            if($bid){
                          self :: set_booking_status( $bid, 'paid', $send_mail );
                          $wpdb -> update( $wpdb->prefix . 'bookings_calendar', array("is_logged"=>"0"), array( 'id' => $bid ) );
                  }
        }
        if($fid>0)
        {
            $update_values = array();
            $update_values['first_event_id'] = $fid;
            $wpdb -> update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'order_id' => $order_id ) );
        }
       // $order = wc_get_order( $order_id );
        
       //$booking_id = get_post_meta( $order_id, 'booking_id', true );
      //  if($booking_id){
      //          self :: set_booking_status( $booking_id, 'paid' );
       // }
    }
    /**
    * Booking Paid
    *
    * @param number $order_id with id of order
    * 
     */
    public static function booking_paid_processing( $status,$order_id) {
       
    /*
        $order = wc_get_order( $order_id );
        
         $booking_id = get_post_meta( $order_id, 'booking_id', true );
        if($booking_id){
            if($status == "processing"){
                
                self :: set_booking_status( $booking_id, 'paid' );
               
            }
        } */
        global $wpdb;
        $send_mail = true;

        $disable_order_mail = get_post_meta($order->ID,"disable_order_mail",true);

        if($disable_order_mail == "true"){
            $send_mail = false;
        }

        $booking_data = $wpdb -> get_results( 'SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar` WHERE `order_id`=' . esc_sql( $order_id ), 'ARRAY_A' );
        $fid = 0;
        foreach($booking_data as $bd)
        {
            $fe_id = $bd['first_event_id'];
            if(!empty($fe_id))
            {
                $fid = $fe_id;
            }
            $bid = $bd['id'];
            if($bid){
                    if($status == "processing"){
                            self :: set_booking_status( $bid, 'paid', $send_mail );
                            $wpdb -> update( $wpdb->prefix . 'bookings_calendar', array("is_logged"=>"0"), array( 'id' => $bid ) );
                    }
                  }
        }
        if($fid>0)
        {
            $update_values = array();
            $update_values['first_event_id'] = $fid;
            $wpdb -> update( $wpdb->prefix . 'bookings_calendar', $update_values, array( 'order_id' => $order_id ) );
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
    public static function is_booking_external( $booking_status ): bool {
        $external = false;
        if ( 0 === strpos( $booking_status, 'external' ) ) {
            $external = true;
        }

        return $external;
    }
    public function check_for_expired_booking(){
        
        global $wpdb;
        $date_format = 'Y-m-d H:i:s';
        // Change status to expired
        $table_name = $wpdb->prefix . 'bookings_calendar';
        $bookings_ids = $wpdb->get_col( $wpdb->prepare( "
            SELECT ID FROM {$table_name}
            WHERE status not in ('paid','owner_reservations','icalimports','cancelled')      
            AND expiring < %s
            
        ", date( $date_format, current_time( 'timestamp' ) ) ));

        if ( $bookings_ids ) {
            foreach ( $bookings_ids as $booking ) {
                  // delecting old reservations
                self :: set_booking_status ( $booking, 'expired' );
                do_action('listeo_expire_booking',$booking);
            }
        }
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
        $lastDay = new DateTime( $date_end ) ;

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

    /**
     * Calculate only normal price
     *
     * @param  number $listing_id post id of current listing
     * @param  date  $date_start since we checking
     * @param  date  $date_end to we checking
     *
     * @return number $price of all booking at all
     */
    public static function calculate_normal_price( $listing_id, $date_start, $date_end, $multiply, $services, $totalPrice = 1, $totalDays = 1, $sub_listings=array() ) {
        // get all special prices between two dates from listeo settings special prices
        $special_prices_results = self :: get_bookings( $date_start, $date_end, array( 'listing_id' => $listing_id, 'type' => 'special_price' ) );
       // echo print_r($special_prices_results);
       // die;
        // prepare special prices to nice array
        foreach ($special_prices_results as $result)
        {
            $special_prices[ $result['date_start'] ] = $result['comment'];
        }


        // get normal prices from listeo listing settings
        $normal_price = (float) get_post_meta ( $listing_id, '_normal_price', true);
        $weekend_price = (float)  get_post_meta ( $listing_id, '_weekday_price', true);
        $hour_price = (float)  get_post_meta ( $listing_id, '_hour_price', true);
       /* $is_equip = get_post_meta ( $listing_id, '_booking_system_equipment', true);
        if($is_equip)
        {
            $hour_price = $normal_price;
        }*/
        if(empty($weekend_price)){
            $weekend_price = $normal_price;
        }

        $reservation_price  =  (float) get_post_meta ( $listing_id, '_reservation_price', true);
        $_count_per_guest  = get_post_meta ( $listing_id, '_count_per_guest', true);
        $services_price = 0;
        $listing_type = get_post_meta( $listing_id, '_listing_type', true);
       // echo $date_start ."<br>";
        //echo $date_end;
        if(isset($_POST["slot"]) && $_POST["slot"] != ""){
            $date_end = date("Y-m-d",strtotime($date_end)). ' 23:59:59';
            if($normal_price != "" && $normal_price != 0){
                $hour_price = $normal_price;
            }
        }





         

        $firstDay = new DateTime( $date_start );

        $lastDay = new DateTime( $date_end ) ;
        $hourdiff = round((strtotime($date_end) - strtotime($date_start))/3600, 1);

       /* echo "<pre>"; print_r($firstDay);
        echo "<pre>"; print_r($lastDay);
        die;*/


		if($hourdiff<=24)
		{
            if(!empty($sub_listings))
            {
                $sub = $sub_listings;
                foreach($sub as $su)
                {
                    $percent = get_post_meta($su, 'percentage_full_price', true);
                    $sub_ttl = ($percent/100)*$hour_price;
                    $sub_ttl = $sub_ttl*$hourdiff;
                    $sub_tl += $sub_ttl;
                }
                if($sub_tl<$normal_price)
                {
                    $totalPrice = $sub_tl;
                }
                else
                {
                    $totalPrice = $normal_price;
                }
            }
            else
            {
                $totalPrice = $hour_price*$hourdiff;
                $start_of_week = intval( get_option( 'start_of_week' ) );
                $day = $firstDay->format("N");
                if($start_of_week == 0 ) {
                    if ( isset( $weekend_price ) && $day == 5 || $day == 6) {
                        $normal_price = $weekend_price;
                    } // else { $price += $normal_price; }
                } else {
                    if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                        $normal_price = $weekend_price;
                    }  //else { $price += $normal_price; }
                }
                if($normal_price != "" && $normal_price != 0){

                     if($totalPrice>$normal_price)
                        {
                            $totalPrice = $normal_price;
                        }

                }
               



            }
		}
		else
		{
            $days_between = $lastDay->diff($firstDay)->format("%a");
            $days_count = ($days_between == 0) ? 1 : $days_between ;
            //fix for not calculating last day of leaving
            if ( $date_start != $date_end ) $lastDay -> modify('-1 day');
            $interval = DateInterval::createFromDateString('1 day');

            $period = new DatePeriod( $firstDay, $interval, $lastDay );

            $price = 0;
            //echo print_r($period, true);
            //die;
            foreach ( $period as $current_day ) {

                // get current date in sql format
                $date = $current_day->format("Y-m-d H:i:s");
                $day = $current_day->format("N");
              
                $start_of_week = intval( get_option( 'start_of_week' ) ); // 0 - sunday, 1- monday
                // when we have weekends
                if($start_of_week == 0 ) {
                    if ( isset( $weekend_price ) && $day == 5 || $day == 6) {
                        $price += $weekend_price;
                    }  else {
                       $price += $normal_price; 
                    }
                } else {
                    if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                        $price += $weekend_price;
                    }  else { 
                        $price += $normal_price;
                    }
                }


            }
            // echo $date_end; die;

            
            $date_start_hour = new DateTime( $date_end );
            $date_start_hour->setTime(00,00);
            $date_end_hour = new DateTime( $date_end );

            $hourdiff = $date_end_hour->diff($date_start_hour)->format("%h");

            
              
            $totalPrice = $hour_price*$hourdiff;
            $start_of_week = intval( get_option( 'start_of_week' ) );
            $day = $date_start_hour->format("N");
            if($start_of_week == 0 ) {
                if ( isset( $weekend_price ) && $day == 5 || $day == 6) {
                    $normal_price = $weekend_price;
                } // else { $price += $normal_price; }
            } else {
                if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                    $normal_price = $weekend_price;
                }  //else { $price += $normal_price; }
            }
            if($totalPrice>$normal_price)
            {
                $totalPrice = $normal_price;
            }
            
            $totalPrice = $price + $totalPrice;

			//echo $normal_price."<br>".$rem_amt;
			//die;
		}

		$_booking_system_service = get_post_meta($listing_id,"_booking_system_service",true);
        $_booking_slots = get_post_meta($listing_id,"_booking_slots",true);

        if($_booking_system_service == "on" && !empty($_booking_slots)){

            $_slots = $_booking_slots;

             if(isset($_POST["slot"]) && $_POST["slot"] != ""){
             	    $slott = explode("|", $_POST["slot"]);

                    $from_day = $slott[0];
                    $from_time = $slott[1];
                    $to_day = $slott[2];
                    $to_time = $slott[3];
                    $slot_price = $slott[4];
                    $slots = $slott[5];
                    $slot_id = $slott[6];
                    $closed = (isset($slott[7]))?$slott[7]:"0";
					$all_slot_price = (isset($slott[8]))?$slott[8]:0;

                    if(isset($_POST["slot_price_type"]) && $_POST["slot_price_type"] == "all_slot_price"){
                        $slot_price = $all_slot_price;
                    }

                    $totalPrice = $slot_price;
             }

        }

        if($multiply && $multiply > 1){
            if(isset($_POST["slot_price_type"]) && $_POST["slot_price_type"] == "all_slot_price"){
            }else{
                $totalPrice = $totalPrice * $multiply;
            }
        }

        /*
        die;
        $days_between = $lastDay->diff($firstDay)->format("%a");
        $days_count = ($days_between == 0) ? 1 : $days_between ;
        //fix for not calculating last day of leaving
        if ( $date_start != $date_end ) $lastDay -> modify('-1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod( $firstDay, $interval, $lastDay );
		
        // at start we have reservation price
        $price = 0;
        //echo print_r($period, true);
        //die;
        foreach ( $period as $current_day ) {

            // get current date in sql format
            $date = $current_day->format("Y-m-d H:i:s");
            $day = $current_day->format("N");
            echo $date;
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
        */
        return round(intval($totalPrice),2);
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
        $lastDay = new DateTime( $date_end ) ;

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

    public static function registerUser($data){

        $_POST = $data;

        $return = array("success" => 0, "user_id" => 0);

        if ( email_exists( $_POST["email"] ) ) {
            $user = get_user_by( 'email', $_POST["email"] );
            $return["success"] = 1;
            $return["user_id"] = $user->ID;
            return $return;
        }

        $password = wp_generate_password( 12, false );

        $first_name = (isset($_POST['firstname'])) ? sanitize_text_field( $_POST['firstname'] ) : '' ;
        $last_name = (isset($_POST['lastname'])) ? sanitize_text_field( $_POST['lastname'] ) : '' ;
        $email = $_POST['email'];
        $email_arr = explode('@', $email);
        $user_login = $email;

        $role =  "owner";

        //echo "<pre>"; print_r($_POST); die;

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
            if(isset($_POST["country_code"])){
                update_user_meta( $user_id, 'country_code',$_POST["country_code"] );
            }

            if ( isset( $_POST['phone'] ) ){
                update_user_meta($user_id, 'phone', $_POST['phone'] );
            }   
            if ( isset( $_POST['firstname'] ) ){
                update_user_meta($user_id, 'first_name', $_POST['firstname'] );
                update_user_meta($user_id, 'billing_first_name', $_POST['firstname'] );
            }   
            if ( isset( $_POST['lastname'] ) ){
                update_user_meta($user_id, 'last_name', $_POST['lastname'] );
                update_user_meta($user_id, 'billing_last_name', esc_attr( $_POST['lastname'] ) );
            }
            if ( isset( $_POST['email']  )){
                update_user_meta($user_id, 'billing_email', esc_attr( $_POST['email'] ) );
            } 

            if ( isset( $_POST['billing_address_1'] ) ){
                update_user_meta($user_id, 'billing_address_1', esc_attr( $_POST['billing_address_1'] ) );
            }
            if ( isset( $_POST['billing_address_2'] ) ){
                update_user_meta($user_id, 'billing_address_2', esc_attr( $_POST['billing_address_2'] ) );
            }
            if ( isset( $_POST['billing_city'] ) ){
                update_user_meta($user_id, 'billing_city', esc_attr( $_POST['billing_city'] ) );
            }
            if ( isset( $_POST['billing_postcode'] ) ){
                update_user_meta($user_id, 'billing_postcode', esc_attr( $_POST['billing_postcode'] ) );
            }
             if ( isset( $_POST['billing_country'] ) ){
                update_user_meta($user_id, 'billing_country', esc_attr( $_POST['billing_country'] ) );
            }
             if ( isset( $_POST['personalorcompany'] ) ){

                update_user_meta($user_id, 'profile_type', $_POST['personalorcompany']  );
            }
            if ( isset( $_POST['organization_number'] ) ){

                update_user_meta($user_id, 'company_number', $_POST['organization_number']);
            }

            $return["success"] = 1;
            $return["user_id"] = $user_id;
            
        }

        return $return;


    }

    protected function addToGroup($listing_id,$author){
        global $wpdb;
        $sql = 'SELECT * FROM `'  . $wpdb->prefix .  'posts` WHERE `ID`=' . esc_sql( $listing_id );
        $list_data = $wpdb -> get_row( $sql, 'ARRAY_A' );
        //echo "<pre>"; print_r($list_data); die;
        if(isset($list_data["users_groups_id"]) && $list_data["users_groups_id"] != ""){
            $user_id = $author;
            $sql2 = 'SELECT * FROM `'  . $wpdb->prefix .  'users_and_users_groups` WHERE `users_groups_id`=' . esc_sql( $list_data["users_groups_id"] ).' AND users_id = '.$user_id;
            $gr_data = $wpdb -> get_results( $sql2, 'ARRAY_A' );
           // echo "<pre>"; print_r($gr_data); die;
            if(empty($gr_data)){
                $insert_data = array(
                    'users_groups_id' => $list_data["users_groups_id"],
                    'users_id' => $user_id,
                    'role' => "1",
                );
        
                $wpdb -> insert( $wpdb->prefix . 'users_and_users_groups', $insert_data );
            }
        }

    }


}

?>