<?php
// add_action( 'rest_api_init', function () {
//     register_rest_route( 'booking', '/checkorderswithoutbooking', array(
//         'methods' => 'GET',
//         'callback' => 'checkorderswithoutbooking',
//     ) );
// } );

// function checkorderswithoutbooking(){
//     global $wpdb;

//     // Define the date to filter by
//     $date_filter = '2023-08-27';
    
//     // Prepare the SQL query with placeholders
//     $args = array(
//         'post_type'      => 'shop_order', // The post type for WooCommerce orders
//         'post_status'    => 'wc-completed', // Change as needed (wc-completed, wc-processing, etc.)
//         'posts_per_page' => -1, // To get all orders, change if you want pagination
//         'date_query'     => array(
//             array(
//                 'after'     => '2023-08-27',
//                 'inclusive' => true,
//             ),
//         ),
//         'meta_query'     => array(
//             'relation' => 'AND', // Ensure both conditions are met
//             array(
//                 'key'     => '_dibs_charge_id',
//                 'compare' => 'EXISTS',
//             ),
//             array(
//                 'key'     => 'booking_id',
//                 'compare' => 'NOT EXISTS',
//             ),
//         ),
//     );
    
//     $query = new WP_Query($args);
    
//     echo '<pre>'; print_r($query->posts); die;
    
// }


//************************ Give list of bookings without charge_id*********************
// This gives a list of all bookings that dont have charge id and price is more than 0
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/booking/checkbookingwithnocommissionid

add_action( 'rest_api_init', function () {
    register_rest_route( 'booking', '/checkbookingwithnocommissionid', array(
      'methods' => 'GET',
      'callback' => 'checkbookingwithnocommissionid',
    ) );
  } );

function checkbookingwithnocommissionid(){
    global $wpdb;

    // Define the date to filter by
    $date_filter = '2023-08-27';
    
    // Prepare the SQL query with placeholders
    $query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}bookings_calendar WHERE created_at > %s AND status = 'paid' and price > 0 and order_id != ''",
        $date_filter
    );
    
    // Execute the query and get the results
    $results = $wpdb->get_results($query);

    $bookingss = array();

    if(!empty($results)){
        foreach($results as $result){
            if(isset($result->order_id) && $result->order_id > 0){
                
                $_dibs_payment_id = get_post_meta( $result->order_id, '_dibs_payment_id', true );
                $_dibs_charge_id = get_post_meta( $result->order_id, '_dibs_charge_id', true );

                $results2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeo_core_commissions WHERE order_id = ".$result->order_id);

                if($_dibs_charge_id != "" && empty($results2)){

                    $bookingss[] = $result;
                }
            }
        }
    }

    echo "<pre>"; print_r($bookingss); die;
}
//************************ END Give list of bookings without charge_id*********************

//************************ Give list of bookings without charge_id but Payment id exist *********************
// This gives a list of all bookings that dont have charge id but payment id exist. all bookings filter gretaer then 2023-08-27
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/booking/checkbookingwithpaymentid

add_action( 'rest_api_init', function () {
    register_rest_route( 'booking', '/checkbookingwithpaymentid', array(
      'methods' => 'GET',
      'callback' => 'checkbookingwithpaymentid',
    ) );
  } );

function checkbookingwithpaymentid(){
    global $wpdb;

    // Define the date to filter by
    $date_filter = '2023-08-27';
    
    // Prepare the SQL query with placeholders
    $query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}bookings_calendar WHERE created_at > %s AND status = 'paid' and price > 0 and order_id != ''",
        $date_filter
    );
    
    // Execute the query and get the results
    $results = $wpdb->get_results($query);

    $bookingss = array();

    if(!empty($results)){
        foreach($results as $result){
            if(isset($result->order_id) && $result->order_id > 0){
                
                $_dibs_payment_id = get_post_meta( $result->order_id, '_dibs_payment_id', true );
                $_dibs_charge_id = get_post_meta( $result->order_id, '_dibs_charge_id', true );

                if($_dibs_charge_id == "" && $_dibs_payment_id != ""){

                    $bookingss[] = $result;
                }
            }
        }
    }

}

//************************  End Give list of bookings without charge_id but Payment id exist *********************

//************************ Create list of bookings without charge_id but Payment id exist *********************
// This gives a Create of all bookings that dont have charge id but payment id exist. all bookings filter gretaer then 2023-08-27
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/booking/chargebooking

add_action( 'rest_api_init', function () {
    register_rest_route( 'booking', '/chargebooking', array(
      'methods' => 'GET',
      'callback' => 'chargebooking',
    ) );
  } );

function chargebooking(){
    global $wpdb;
    $myfile = fopen(ABSPATH."/cronlog.txt", "a");

    $timezone = new DateTimeZone('Europe/Oslo');

    // Create a DateTime object with the current time
    $date = new DateTime('now', $timezone);

    // Format the date and time as desired
    $cr_date =  $date->format('Y-m-d H:i:s');

    $text2 = "Cron started: ".$cr_date." \r\n".PHP_EOL;

    fwrite($myfile, $text2);
    
    

    // Define the date to filter by
    $date_filter = date('Y-m-d H:i:s', strtotime(date() . ' -8 days'));
    
    // Prepare the SQL query with placeholders
    $query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}bookings_calendar WHERE updated_at > %s",
        $date_filter
    );
    
    // Execute the query and get the results
    $results = $wpdb->get_results($query);

    if(!empty($results)){
        foreach($results as $result){
            if(isset($result->order_id) && $result->order_id > 0){
                $_dibs_payment_id = get_post_meta( $result->order_id, '_dibs_payment_id', true );
                $_dibs_charge_id = get_post_meta( $result->order_id, '_dibs_charge_id', true );

                if($_dibs_charge_id == "" && $_dibs_payment_id != ""){

                    $ress = getPaymentData($_dibs_payment_id);
                    
                

                    if(!empty($ress) && isset($ress["charges"]) && !empty($ress["charges"])){
                       // echo "<pre>"; print_r($ress); die;

                        $charge_id = $ress["charges"][0]["chargeId"]; 
                        

                        
                        $order = wc_get_order($result->order_id);

                        if ($order) {

                            $order->update_status('completed', 'Order marked as paid via custom script.');
                        
                            // Optionally, you can add a custom note
                            $order->add_order_note('Order marked as paid programmatically.');

                            

                            update_post_meta($result->order_id,"_dibs_charge_id",$charge_id);
                            
                            $wpdb -> update( $wpdb->prefix.'bookings_calendar', array("status"=>"paid"), array( 'id' => $result->id ));
                            
                        
                        }
                        $text = "/n/t order id: ".$result->order_id." and charge id: ".$charge_id;
                        fwrite($myfile, $text);
                    }
                }
            }
        }
        
    }
    fclose($myfile);

    //echo "<pre>"; print_r($results); die;
}
//************************ END Create list of bookings without charge_id but Payment id exist *********************

//************** Insert charge id Get list of orders without charge_id but Payment id exist *********************
// This gives a Get list of orders without charge_id but Payment id exist and then add charge id. all orders filter gretaer then 2024-11-01
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/order/checkchargeid

add_action( 'rest_api_init', function () {
    register_rest_route( 'order', '/checkchargeid', array(
        'methods' => 'GET',
        'callback' => 'checkchargeid',
    ) );
} );

function checkchargeid(){

    $myfile = fopen(ABSPATH."/chargelog.txt", "a");

    $timezone = new DateTimeZone('Europe/Oslo');

    // Create a DateTime object with the current time
    $date = new DateTime('now', $timezone);

    // Format the date and time as desired
    $cr_date =  $date->format('Y-m-d H:i:s');
    
    global $wpdb;
    $sql = "
        SELECT p.ID
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_payment_id ON p.ID = pm_payment_id.post_id AND pm_payment_id.meta_key = %s
        LEFT JOIN {$wpdb->postmeta} pm_charge_id ON p.ID = pm_charge_id.post_id AND pm_charge_id.meta_key = %s
        WHERE p.post_type = 'shop_order'
        AND p.post_status = %s
        AND pm_payment_id.meta_key IS NOT NULL
        AND pm_charge_id.meta_key IS NULL
        AND p.post_date > %s
    ";

    // Prepare the query with the meta keys
    $query = $wpdb->prepare($sql, '_dibs_payment_id', '_dibs_charge_id', 'wc-completed', '2024-11-01 00:00:00');

    $results = $wpdb->get_results($query);

    $tablee = $wpdb->prefix ."bookings_calendar";

    //echo "<pre>"; print_r($results); die;
    foreach($results as $result){

        $_dibs_payment_id = get_post_meta( $result->ID, '_dibs_payment_id', true ); 
        $_dibs_charge_id = get_post_meta( $result->ID, '_dibs_charge_id', true ); 

        if($_dibs_payment_id != "" && $_dibs_charge_id == ""){

            $ress = getPaymentData($_dibs_payment_id);
            if(!empty($ress) && isset($ress["charges"]) && !empty($ress["charges"])){

                $charge_id = $ress["charges"][0]["chargeId"]; 

                if($charge_id != ""){

                    

                    $text2 = "Cron started: ".$cr_date.PHP_EOL;
                    $text2 .= "order id: ".$result->ID." and charge id: ".$charge_id.PHP_EOL.PHP_EOL;

                    fwrite($myfile, $text2);
                    
                    //echo "<pre>"; print_r($charge_id);

                    $booking_id = get_post_meta($result->ID,"booking_id",true);
                    

                    $order = wc_get_order($result->ID);
                        // echo "<pre>"; print_r($order); die;
                        if ($order) {
    
                            update_post_meta($result->ID,"_dibs_charge_id",$charge_id);
                            
                            // Mark order as completed (paid)
                            $order->update_status('completed', 'Order marked as paid via custom script.');
                        
                            // Optionally, you can add a custom note
                            $order->add_order_note('Order marked as paid programmatically.');
                        }

                    $wpdb -> update( $tablee, array("status"=>"paid"), array( 'id' => $booking_id ));
                }

            }
        }

    }

    fclose($myfile);
    
}

//************** END Insert charge id Get list of orders without charge_id but Payment id exist *********************

//************** Change completed status Get list of orders with charge_id and Payment id exist *********************
// This gives a Get list of orders with charge_id and  Payment id exist and then Change completed status. all orders filter gretaer then 2024-11-01
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/order/changePendingPaymentStatus

add_action( 'rest_api_init', function () {
    register_rest_route( 'order', '/changePendingPaymentStatus', array(
        'methods' => 'GET',
        'callback' => 'changePendingPaymentStatus',
    ) );
} );

function changePendingPaymentStatus(){

    $myfile = fopen(ABSPATH."/changestatuslog.txt", "a");

    $timezone = new DateTimeZone('Europe/Oslo');

    // Create a DateTime object with the current time
    $date = new DateTime('now', $timezone);

    // Format the date and time as desired
    $cr_date =  $date->format('Y-m-d H:i:s');
    
    global $wpdb;
    $sql = "
        SELECT p.ID
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_payment_id ON p.ID = pm_payment_id.post_id AND pm_payment_id.meta_key = %s
        LEFT JOIN {$wpdb->postmeta} pm_charge_id ON p.ID = pm_charge_id.post_id AND pm_charge_id.meta_key = %s
        WHERE p.post_type = 'shop_order'
        AND p.post_status = 'wc-pending'
        AND pm_payment_id.meta_key IS NOT NULL
        AND pm_charge_id.meta_key IS NOT NULL
        AND p.post_date > %s
    ";

    // Prepare the query with the meta keys
    $query = $wpdb->prepare($sql, '_dibs_payment_id', '_dibs_charge_id',  '2024-12-01 00:00:00');

    $results = $wpdb->get_results($query);

    $tablee = $wpdb->prefix ."bookings_calendar";

    foreach($results as $result){

        $_dibs_payment_id = get_post_meta( $result->ID, '_dibs_payment_id', true ); 
        $_dibs_charge_id = get_post_meta( $result->ID, '_dibs_charge_id', true ); 

        if($_dibs_payment_id != "" && $_dibs_charge_id != ""){

            $order = wc_get_order($result->ID);
            // echo "<pre>"; print_r($order); die;
            if ($order) {
                
                // Mark order as completed (paid)
                $order->update_status('completed', 'Order marked as paid via custom script.');
            
                // Optionally, you can add a custom note
                $order->add_order_note('Order marked as paid programmatically.');
            }
        }

    }

    fclose($myfile);
    
}

//************** END Insert charge id Get list of orders without charge_id but Payment id exist *********************

//************************ Create list of gift bookings without charge_id but Payment id exist *********************
// This gives a Create of all gift bookings that dont have charge id but payment id exist. all bookings filter gretaer then 2024-11-27
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/booking/giftchargebooking


add_action( 'rest_api_init', function () {
    register_rest_route( 'booking', '/giftchargebooking', array(
      'methods' => 'GET',
      'callback' => 'giftchargebooking',
    ) );
  } );


function giftchargebooking(){
    global $wpdb;
 
    $date_filter = date('Y-m-d H:i:s', strtotime(date() . ' -8 days'));
    
    // Prepare the SQL query with placeholders
    $query = $wpdb->prepare(
        "SELECT ID,post_date FROM {$wpdb->prefix}posts WHERE post_date > %s AND post_type = 'giftcard_booking'",
        $date_filter
    );

    $results = $wpdb->get_results($query);
    
 
    foreach ($results as $key => $result) {
            
            $created_date = $result->post_date;
            $created_date = strtotime("+30 minutes", strtotime($created_date));
            $created_date = date("Y-m-d H:i:s", $created_date);
            //$cr_date = date("Y-m-d H:i:s");
             $timezone = new DateTimeZone('Europe/Oslo');
 
             // Create a DateTime object with the current time
             $date = new DateTime('now', $timezone);
 
             // Format the date and time as desired
             $cr_date =  $date->format('Y-m-d H:i:s');
       // $cr_date = "2023-03-11 17:34:50";
 
        if( strtotime($cr_date) > strtotime($created_date)){

            $order_id = get_post_meta($result->ID,"order_id",true);
            
 
        
             if(isset($order_id) && $order_id > 0){
                $_dibs_payment_id = get_post_meta( $order_id, '_dibs_payment_id', true ); 
                $_dibs_charge_id = get_post_meta( $order_id, '_dibs_charge_id', true ); 

                
        
                if($_dibs_payment_id != "" && $_dibs_charge_id == ""){
 
                    $ress = getPaymentData($_dibs_payment_id);
                    //echo "<pre>"; print_r($ress); 
                 
 
                    if(!empty($ress) && isset($ress["charges"]) && !empty($ress["charges"])){
 
                        $charge_id = $ress["charges"][0]["chargeId"]; 
                        
                        $order = wc_get_order($order_id);
                        // echo "<pre>"; print_r($order); die;
                        if ($order) {
    
                            update_post_meta($order_id,"_dibs_charge_id",$charge_id);
                            
                            // Mark order as completed (paid)
                            $order->update_status('completed', 'Order marked as paid via custom script.');
                        
                            // Optionally, you can add a custom note
                            $order->add_order_note('Order marked as paid programmatically.');
                        }
                        //die;
                        //$wpdb -> update( $tablee, array("status"=>"paid"), array( 'id' => $result->id ));
                        continue;
                    }
                } 
 
             }
            
            // $wpdb -> update( $tablee, array("status"=>"payment_failed"), array( 'id' => $result->id ));
            // echo "deleted: $result->id <br>";
        }
    }
    return true;
}

//************** END Create list of gift bookings without charge_id but Payment id exist *********************

//************************ Create list of  bookings without charge_id but Payment id exist  *********************
// This gives a Create of all bookings that dont have charge id but payment id exist after 30 minute from payment created. 
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/booking/checkstatusbooking


add_action( 'rest_api_init', function () {
  register_rest_route( 'booking', '/checkstatus', array(
    'methods' => 'GET',
    'callback' => 'checkstatusbooking',
  ) );
} );

function checkstatusbooking(){
   global $wpdb;

   $tablee = $wpdb->prefix ."bookings_calendar";

   $sqll = "select * from $tablee where status = 'pay_to_confirm' OR status = 'payment_failed'";

   $results = $wpdb->get_results($sqll);
   

   foreach ($results as $key => $result) {
           
           $created_date = $result->created_at;
           $created_date = strtotime("+30 minutes", strtotime($created_date));
           $created_date = date("Y-m-d H:i:s", $created_date);
           //$cr_date = date("Y-m-d H:i:s");
            $timezone = new DateTimeZone('Europe/Oslo');

            // Create a DateTime object with the current time
            $date = new DateTime('now', $timezone);

            // Format the date and time as desired
            $cr_date =  $date->format('Y-m-d H:i:s');
      // $cr_date = "2023-03-11 17:34:50";

       if( strtotime($cr_date) > strtotime($created_date)){

       
            if(isset($result->order_id) && $result->order_id > 0){
                $payment_id = get_post_meta( $result->order_id, '_dibs_payment_id', true ); 

                $ress = getPaymentData($payment_id);
                //echo "<pre>"; print_r($ress); 
                

                if(!empty($ress) && isset($ress["charges"]) && !empty($ress["charges"])){

                    $charge_id = $ress["charges"][0]["chargeId"]; 
                    

                    
                    $order = wc_get_order($result->order_id);
                   // echo "<pre>"; print_r($order); die;
                    if ($order) {

                        
                       
                        // Mark order as completed (paid)
                        $order->update_status('completed', 'Order marked as paid via custom script.');
                    
                        // Optionally, you can add a custom note
                        $order->add_order_note('Order marked as paid programmatically.');

                        update_post_meta($result->order_id,"_dibs_charge_id",$charge_id);
                    }
                    //die;
                    $wpdb -> update( $tablee, array("status"=>"paid"), array( 'id' => $result->id ));
                    continue;
                }

            }
           
            $wpdb -> update( $tablee, array("status"=>"payment_failed"), array( 'id' => $result->id ));
            echo "deleted: $result->id <br>";
       }
   }
   return true;
}
//************** END Create list of  bookings without charge_id but Payment id exist *********************

//************************ Create list of  bookings without charge_id but Payment id exist  *********************
// This gives a Create of all bookings that dont have charge id but payment id exist after 30 minute from payment created. 
// open in this linkhttp://staging5.dev.gibbs.no/wp-json/booking/checkstatusbooking


// get payment data

function getPaymentData($payment_id){
   // Your Nets Easy API key
   $settings = get_option( 'woocommerce_dibs_easy_settings' );

    if($settings["test_mode"] == "yes"){
        $apiKey = str_replace("test-secret-key-","",$settings["dibs_test_key"]);
        $url = "https://test.api.dibspayment.eu/v1/payments/".$payment_id;
    }else{
        $apiKey = str_replace("live-secret-key-","",$settings["dibs_live_key"]);
        $url = "https://api.dibspayment.eu/v1/payments/".$payment_id;
    }
    



    $curl = curl_init();

    curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "Authorization: ".$apiKey,
    ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    

    $data_return = [];

    if ($err) {
    } else {
    if($response){
        $data = json_decode($response,true);
       // echo "<pre>"; print_r($data); die;
        if(isset($data["payment"]) && isset($data["payment"]["charges"]) && !empty($data["payment"]["charges"])){
            $data_return["charges"] = $data["payment"]["charges"];
        }
    }
    }
    return $data_return;
      

}