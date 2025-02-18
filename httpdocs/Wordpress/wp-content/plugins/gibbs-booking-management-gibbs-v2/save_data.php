<?php

function gibbs_bookings_by_status($user_id,$group_id,$status){

  global $wpdb;
  if( $status == 'approved' ) {
    $status_sql = "status IN ('confirmed')";
  }elseif( $status == 'expired' ) {
    $status_sql = "(status IN ('expired','cancelled','declined') Or (status = 'paid' AND (fixed = 1)))";
  }elseif( $status == 'all' ) {
    $status_sql = "status IN ('waiting','approved','expired','cancelled','declined','confirmed','paid','completed')";
  }elseif( $status == 'invoice' ) {
    $status_sql = "status = 'paid' AND (fixed = 2 OR fixed = 3)";
  }elseif( $status == 'invoice_sent' ) {
    $status_sql = "status = 'paid' AND (fixed = 4)";
  } else if( $status == 'paid' ) {
    $status_sql = "status = 'paid' AND (fixed = 0)";
  } else {
    $status_sql = "status='$status'";
  }
  $sql = get_groups_sql($group_id,$user_id);


  $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE $status_sql $sql";
  $results  = $wpdb -> get_results($sql);

  $results = resultFilter($results);

  return $results;

}

function get_groups_sql($group_id,$user_id){
   global $wpdb;
   $sql = "";
   if($group_id != "0"){


      $group_user_ids = array();

      $posts_table = $wpdb->prefix . 'posts';  // table name
      $posts_table_sql = "select ID from `$posts_table` where post_type='listing' AND users_groups_id = $group_id";
      $posts_data = $wpdb->get_results($posts_table_sql);



      $listing_ids = array();

      foreach ($posts_data as $key => $posts_da) {
        $listing_ids[] = $posts_da->ID;
      }

      $group_admin = get_group_admin();

      if($group_admin != ""){

        $posts_table = $wpdb->prefix . 'posts';  // table name
        $posts_table_sql = "select ID from `$posts_table` where post_type='listing' AND post_author = $group_admin";
        $posts_data = $wpdb->get_results($posts_table_sql);

        foreach ($posts_data as $key => $posts_da) {
          $listing_ids[] = $posts_da->ID;
        }

        
      }
      $listing_ids = implode(",", $listing_ids);
      /*$bookingss_table = $wpdb->prefix . 'bookings_calendar';  // table name
      $bookingss_table_sql = "select distinct bookings_author from `$bookingss_table` where listing_id IN ($listing_ids)";
      $bookingss_data = $wpdb->get_results($bookingss_table_sql);
        
      foreach ($bookingss_data as $key => $bookingss_da) {
        $group_user_ids[] = $bookingss_da->bookings_author;
      }
      $group_user_ids = implode(",", $group_user_ids);*/

      //$sql .= " AND owner_id IN (".$group_user_ids.")";
      $sql .= " AND listing_id IN ($listing_ids)";

     
  }else{
        $sql .= " AND owner_id = $user_id";
  }
  return $sql;
}
function get_buyer_groups_sql($group_id,$user_id){
   global $wpdb;
   $sql = "";
   if($group_id != "0"){


      $group_user_ids = array();

      $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
      $users_table_sql = "select users_id from `$users_and_users_groups` where users_groups_id = $group_id";
      $users_data = $wpdb->get_results($users_table_sql);


      $user_ids = array();

     

      $group_admin = get_group_admin();

      if($group_admin != ""){

       $user_ids[] = $group_admin;
        
      }else{
        foreach ($users_data as $key => $users_da) {
           $user_ids[] = $users_da->users_id;
        }
      }

      $user_ids = implode(",", $user_ids);

      /*$bookingss_table = $wpdb->prefix . 'bookings_calendar';  // table name
      $bookingss_table_sql = "select distinct bookings_author from `$bookingss_table` where listing_id IN ($listing_ids)";
      $bookingss_data = $wpdb->get_results($bookingss_table_sql);
        
      foreach ($bookingss_data as $key => $bookingss_da) {
        $group_user_ids[] = $bookingss_da->bookings_author;
      }
      $group_user_ids = implode(",", $group_user_ids);*/

      //$sql .= " AND owner_id IN (".$group_user_ids.")";
      $sql .= " AND bookings_author IN ($user_ids)";

     
  }else{
        $sql .= " AND bookings_author = $user_id";
  }
  return $sql;
}
function get_single_booking_by_id($id,$user_id,$group_id,$page_type,$related = 0){
  global $wpdb;
  
  if($page_type == "buyer"){
     //$sql = " AND bookings_author = $user_id";
     $sql = get_buyer_groups_sql($group_id,$user_id);
  }else{
     $sql = get_groups_sql($group_id,$user_id);
  }


  $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE id=$id $sql";
  $result  = $wpdb ->get_row($sql);
  if($result){


      $date_start = $result->date_start;
      $date_end = $result->date_end;

      if($result->status == "waiting" || $result->status == "approved" || $result->status == "paid" || $result->status == "completed" || $result->status == "confirmed"){
          if($result->conflict_status == "1"){
             $conflict = "true";
          }else if($result->conflict_status == "2"){
            $conflict = "";
          }else{
            //$conflict = checkConflictBooking($date_start,$date_end,$result->listing_id,$result->id);
            $conflict = "";
          }
      }else{
           $conflict = "";
      }
     

      $result->conflict = $conflict;

      $result->message = "";

      $post_data = get_post($result->listing_id);
      $result->listing_name = "";
      if($post_data->post_title){
        $result->listing_name = $post_data->post_title;
      }
      if($result->parent_listing_id && $result->parent_listing_id != ""){
       
        $post_data2 = get_post($result->parent_listing_id );
        if($post_data2->post_title){
          if($related != 1){
            $result->listing_name = $post_data2->post_title;
          }
        }
      }

      $result->discount_group = "";
      $result->tax = "";
      $result->purpose = "";

      if(isset($result->comment) && $result->comment != ""){
        $comment = json_decode($result->comment);
        if(isset($comment->message)){
          $result->message = $comment->message;
        }
        if(isset($comment->discount_type)){
           $result->discount_group = $comment->discount_type;
        }
        if(isset($comment->total_tax)){
           $result->tax = $comment->tax;
        }
        if(isset($comment->purpose)){
          $result->purpose = $comment->purpose;
        }
      }

      $sql1 = "SELECT display_name FROM `" . $wpdb->prefix . "users` WHERE ID=".$result->bookings_author;
      $display_name  = $wpdb->get_var($sql1); 
      $result->bookings_author_name = $display_name;
      $result->display_name = $display_name;

        $sql1 = "SELECT user_email FROM `" . $wpdb->prefix . "users` WHERE ID=".$result->bookings_author;
        $user_email  = $wpdb->get_var($sql1); 
        $result->customer_email = $user_email;

      $first_name = get_user_meta($result->bookings_author,"first_name",true);
      $last_name = get_user_meta($result->bookings_author,"last_name",true);

      $pr_name = "";

      if($first_name != ""){
            $pr_name .=  substr($first_name, 0, 1);
      }
      if($last_name != ""){
            $pr_name .=  substr($last_name, 0, 1);
      }
      if($pr_name == ""){
        $pr_name .=  substr($display_name, 0, 1);
      }
      $result->pr_name = $pr_name;

       $first_event_id = "";



          if($result->first_event_id != ""){

            $sql_event = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE  first_event_id = ".$result->id;
            $results_event  = $wpdb -> get_results($sql_event);

            if(count($results_event) > 1){
              $first_event_id = "true";
            }
          }  
          $result->start_time = date("H:i",strtotime($result->date_start));
          $result->end_time = date("H:i",strtotime($result->date_end));

          $recurrenceRule = "";



          if($result->recurrenceRule != ""){

              $patterns = explode(';', trim($result->recurrenceRule, ';'));
              $rules = [
                  'FREQ' => "",
                  'UNTIL' => "",
                  'BYDAY' => "",
                  'WKST' => "",
              ];

              foreach ($patterns as $pattern) {
                  list($key, $value) = explode('=', $pattern);
                  $rules[$key] = $value;
              }
              

              if(isset($rules["UNTIL"]) && $rules["UNTIL"] != ""){
                  $now = new DateTime($rules["UNTIL"]);
                  $date_end =  $now->format("Y-m-d"); 
                  $result->date_end = $date_end." ".date("H:i:s", strtotime($result->date_end));
              }else{

                $date_start = $result->date_start;

                $date_end = date("Y-m-d", strtotime("2 year", strtotime($date_start)));

                $result->date_end = $date_end." ".date("H:i:s", strtotime($result->date_end));

              }
              $recurrenceRule = "true";

             
          }  

          $payment_method = "";

	       if($result->order_id != "" && $result->order_id != 0){
	        	$payment_title = get_post_meta($result->order_id,"_payment_method_title",true);
	        	$dibs_payment_method = get_post_meta($result->order_id,"dibs_payment_method",true);
	        	if($payment_title != ""){
	        		$payment_method = $payment_title;
	        		if($dibs_payment_method != ""){
	            		$payment_method .= " (".$dibs_payment_method.")";
	            		
	            	}

	        	}
	        }

	        $result->payment_method = $payment_method;


          $result->first_event_id_text = $first_event_id;
          $result->recurrenceRule_text = $recurrenceRule;




          $result->listing_type = get_post_meta($result->listing_id,"_listing_type",true);

          $result->booking_date = date("d M Y H:i",strtotime($result->date_start))." - <br>".date("d M Y H:i",strtotime($result->date_end));
          $result->booking_start = $result->date_start;
          $result->booking_end = $result->date_start;

          $result->booking_created = $result->created;
          $sql12 = "SELECT post_title FROM `" . $wpdb->prefix . "posts` WHERE post_type='listing' AND post_parent=".$result->listing_id;
          $sub_listing_data = $wpdb->get_results($sql12); 

          $sub_listing = array();

          foreach ($sub_listing_data as  $sub_listing_data_value) {
             $sub_listing[] =  $sub_listing_data_value->post_title;
          }

          $sub_listing = implode(", ", $sub_listing);

          
          $result->sub_listing_name = $sub_listing;

          

          $user_meta = get_user_meta($result->bookings_author);


          $result->customer_tlf = get_user_meta($result->bookings_author,"phone",true);
          $country_code = get_user_meta($result->bookings_author,"country_code",true);
          if($country_code != ""){
            $result->customer_tlf = $country_code." ".$result->customer_tlf;
          }
          $result->customer_address = get_user_meta($result->bookings_author,"user_address",true);
          $result->customer_zip = get_user_meta($result->bookings_author,"user_zipcode",true);
          $result->customer_city = get_user_meta($result->bookings_author,"user_city",true);
          $result->billing_name = get_user_meta($result->bookings_author,"billing_first_name",true)." ".get_user_meta($result->bookings_author,"billing_last_name",true);;
          $result->billing_email = get_user_meta($result->bookings_author,"billing_email",true);
          $result->billing_tlf = get_user_meta($result->bookings_author,"billing_phone",true);
          $result->billing_address = get_user_meta($result->bookings_author,"billing_address_1",true)." ".get_user_meta($result->bookings_author,"billing_address_2",true);
          $result->billing_zip = get_user_meta($result->bookings_author,"billing_postcode",true);
          $result->billing_city = get_user_meta($result->bookings_author,"billing_city",true);

          $result->org_number = get_user_meta($result->bookings_author,"org_number",true);
  }else{
    wp_redirect( home_url( '/404page/' ) );
        exit();
  }

  return $result;

}
function gibbs_buyer_bookings_by_status($user_id,$group_id,$status,$page_type=""){

  global $wpdb;
  if( $status == 'approved' ) {
    $status_sql = "status IN ('confirmed')";
  }elseif( $status == 'expired' ) {
    $status_sql = "(status IN ('expired','cancelled','declined') Or (status = 'paid' AND (fixed = 1)))";
  }elseif( $status == 'all' ) {
    $status_sql = "status IN ('waiting','approved','expired','cancelled','declined','confirmed','paid','completed')";
  }else if( $status == 'paid' ) {
    
     if($page_type == "buyer"){
       $status_sql = "status='$status'";
    }else{
       $status_sql = "status = 'paid' AND (fixed = 0)";
    }
  } else {
    $status_sql = "status='$status'";
  }

  $sql = get_buyer_groups_sql($group_id,$user_id);

 // $sql = " AND bookings_author = $user_id";


  $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE $status_sql $sql";
  $results  = $wpdb -> get_results($sql);

  //echo "<pre>"; print_r($results); die;

  $results = resultFilter($results);

  return $results;

}

function resultFilter($results,$related = 0){
  global $wpdb;

  $resultsss = []; 

  $conflict_count = 0;

  foreach ($results as $key => $result) {
      $date_start = $result->date_start;
      $date_end = $result->date_end;
      $conflict = "";

      if($result->status == "waiting" || $result->status == "approved" || $result->status == "paid" || $result->status == "completed"){
          if($result->conflict_status == "1"){
      	     $conflict = "true";
          }else if($result->conflict_status == "2"){
            $conflict = "";
          }else{
            /*$conflict = checkConflictBooking($date_start,$date_end,$result->listing_id,$result->id);

            if($result->recurrenceRule != ""){

                $booking_data_rec = rec_dates($result);

                foreach ($booking_data_rec as $key => $bkk) {
                   if($bkk->conflict == "true"){
                     $conflict = "true";
                   }
                }

                if($conflict == "true"){
                    $booking_table = $wpdb->prefix . "bookings_calendar";
                    $wpdb->update($booking_table, array('conflict_status'=>"1"), array('id'=>$result->id));
                }else{
                    $booking_table = $wpdb->prefix . "bookings_calendar";
                    $wpdb->update($booking_table, array('conflict_status'=>"2"), array('id'=>$result->id));
                }
            } */   
   
          }
      }else{
      	   $conflict = "";
      }
      if($conflict == "true"){
      	$conflict_count++;
      }

     

      $result->conflict = $conflict;

      $sql1 = "SELECT display_name FROM `" . $wpdb->prefix . "users` WHERE ID=".$result->bookings_author;
      $display_name  = $wpdb->get_var($sql1); 
      $result->bookings_author_name = $display_name;
      $result->display_name = $display_name;

      $sql1 = "SELECT user_email FROM `" . $wpdb->prefix . "users` WHERE ID=".$result->bookings_author;
      $user_email  = $wpdb->get_var($sql1); 
      $result->customer_email = $user_email;

      $post_data = get_post($result->listing_id);
      $result->listing_name = "";
      if($post_data->post_title){
        $result->listing_name = $post_data->post_title;
      }
      $result->sub_listing_title = "";
      if($result->parent_listing_id && $result->parent_listing_id != ""){
       
        $post_data2 = get_post($result->parent_listing_id );
        if($post_data2->post_title){
          if($related != 1){
             $result->sub_listing_title = $result->listing_name;
             $result->listing_name = $post_data2->post_title;
             
          }
        }
      }
      
      $result->message = "";
      $result->discount_group = "";
      $result->tax = "";
      $result->purpose = "";

      $result->guest_amount = 1;

      if(isset($result->comment) && $result->comment != ""){
        $comment = json_decode($result->comment);
        if(isset($comment->message)){
          $result->message = $comment->message;
        }
        if(isset($comment->discount_type)){
           $result->discount_group = $comment->discount_type;
        }
        if(isset($comment->total_tax)){
           $result->tax = $comment->tax;
        }
        if(isset($comment->purpose)){
          $result->purpose = $comment->purpose;
        }
        if(isset($comment->adults)){
            $result->guest_amount = $comment->adults;
        }
      }
      
      $result->coupon = "";

      if(isset($result->booking_extra_data) && $result->booking_extra_data != "" && $result->booking_extra_data != null){
        $extra_data = json_decode($result->booking_extra_data);

        if(isset($extra_data->coupon_data)){
          $result->coupon = $extra_data->coupon_data;
        }
      }
      
      $first_event_id = "";
      $hide_list = 0;



      if($result->first_event_id != ""){

        $sql_event = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE  first_event_id = ".$result->id;
        $results_event  = $wpdb -> get_results($sql_event);

        if(count($results_event) > 1){
          $first_event_id = "true";
        }
        if($result->first_event_id != $result->id && $related == 0){

           $hide_list = 1;

        }
      }  
      
      $result->start_time = date("H:i",strtotime($result->date_start));
      $result->end_time = date("H:i",strtotime($result->date_end));

      $recurrenceRule = "";



      if($result->recurrenceRule != ""){

          $patterns = explode(';', trim($result->recurrenceRule, ';'));
          $rules = [
              'FREQ' => "",
              'UNTIL' => "",
              'BYDAY' => "",
              'WKST' => "",
          ];

          foreach ($patterns as $pattern) {
              list($key, $value) = explode('=', $pattern);
              $rules[$key] = $value;
          }
          

          if(isset($rules["UNTIL"]) && $rules["UNTIL"] != ""){
              $now = new DateTime($rules["UNTIL"]);
              $date_end =  $now->format("Y-m-d"); 
              $result->date_end = $date_end." ".date("H:i:s", strtotime($result->date_end));
          }else{

            $date_start = $result->date_start;

            $date_end = date("Y-m-d", strtotime("2 year", strtotime($date_start)));

            $result->date_end = $date_end." ".date("H:i:s", strtotime($result->date_end));

          }
          $recurrenceRule = "true";

         
      }  
      
      
           $payment_method = "";
           $payment_type = "Manuell";

	       if($result->order_id != "" && $result->order_id != 0){
	        	$payment_title = get_post_meta($result->order_id,"_payment_method_title",true);
	        	$dibs_payment_method = get_post_meta($result->order_id,"dibs_payment_method",true);
	        	if($payment_title != ""){
	        		$payment_method = $payment_title;
	        		if($dibs_payment_method != ""){
	            		$payment_method .= " (".$dibs_payment_method.")";
	            		
	            	}

	        	}
            $_dibs_charge_id = get_post_meta( $result->order_id, '_dibs_charge_id', true );

            if($_dibs_charge_id != ""){

                $payment_type = "Vipps/visa/mastercard";
            }
	        }

	        $result->payment_method = $payment_method;
	        $result->payment_type = $payment_type;


      $result->first_event_id_text = $first_event_id;
      $result->recurrenceRule_text = $recurrenceRule;




      $result->listing_type = get_post_meta($result->listing_id,"_listing_type",true);

      $result->booking_date = date("d M Y H:i",strtotime($result->date_start))." - <br>".date("d M Y H:i",strtotime($result->date_end));
      $result->booking_start = $result->date_start;
      $result->booking_end = $result->date_start;

      $result->booking_created = $result->created;
      $sql12 = "SELECT post_title FROM `" . $wpdb->prefix . "posts` WHERE post_type='listing' AND post_parent=".$result->listing_id;
      $sub_listing_data = $wpdb->get_results($sql12); 

      $sub_listing = array();

      foreach ($sub_listing_data as  $sub_listing_data_value) {
         $sub_listing[] =  $sub_listing_data_value->post_title;
      }
      

      $sub_listing = implode(", ", $sub_listing);

      
      $result->sub_listing_name = $sub_listing;


      $user_meta = get_user_meta($result->bookings_author);

      $result->company_number = get_user_meta($result->bookings_author,"company_number",true);
      $result->profile_type = get_user_meta($result->bookings_author,"profile_type",true);

      $result->customer_tlf = get_user_meta($result->bookings_author,"phone",true);
      $country_code = get_user_meta($result->bookings_author,"country_code",true);
      if($country_code != ""){
        $result->customer_tlf = $country_code." ".$result->customer_tlf;
      }
      $result->customer_address = get_user_meta($result->bookings_author,"user_address",true);
      $result->customer_zip = get_user_meta($result->bookings_author,"user_zipcode",true);
      $result->customer_city = get_user_meta($result->bookings_author,"user_city",true);
      $result->billing_name = get_user_meta($result->bookings_author,"billing_first_name",true)." ".get_user_meta($result->bookings_author,"billing_last_name",true);;
      $result->billing_email = get_user_meta($result->bookings_author,"billing_email",true);
      $result->billing_tlf = get_user_meta($result->bookings_author,"billing_phone",true);
      $result->billing_address = get_user_meta($result->bookings_author,"billing_address_1",true)." ".get_user_meta($result->bookings_author,"billing_address_2",true);
      $result->billing_zip = get_user_meta($result->bookings_author,"billing_postcode",true);
      $result->billing_city = get_user_meta($result->bookings_author,"billing_city",true);

      $result->refund = "";
      $result->refund_amount = "";

      $refund_data = array();

      if(isset($result->order_id) && $result->order_id != "" && $result->order_id > 0){
          
          $orderDDDD = wc_get_order( $result->order_id );
          if (is_a($orderDDDD, 'WC_Order')) {
            $refunds = $orderDDDD->get_refunds();
          

            if(!empty($refunds)){
                $refund_order = $refunds[0];
  
  
                if($refund_order->get_type() == "shop_order_refund"){
                    $refund_data["price"] = wc_price($refund_order->get_total());
                    $refund_data["price_number"] = $refund_order->get_total();
                    $refund_data["date"] = $refund_order->get_date_created()->format("Y-m-d H:i:s");
                    $result->refund = wc_price($refund_order->get_total());
                    $result->refund_amount = $refund_order->get_total();
                }
            }
            
          } 

          

      }
      

        // Decode custom fields data, and return it in both HTML and CSV format. Custom field data consists of a number
        // of data sets, each of which has a number of key / value pairs. The display names of keys are found using the
        // active user group.
        $group_id = UserGroups::get_active_group_id();
        
        $field_name_labels = get_lables($group_id);
        $formatted_fields_data = CustomFieldsRenderer::get_custom_field_data_sets($result->fields_data);
        $result->fields_data_html =
            "<table><tbody>" .
            CustomFieldsRenderer::get_data_sets_as_html_table_rows($formatted_fields_data, $field_name_labels) .
            "</tbody></table>";
        $result->fields_data_csv =
            CustomFieldsRenderer::get_data_sets_as_csv($formatted_fields_data, $field_name_labels);
   
      if($hide_list == 0){
         $resultsss[] = $result;
      }


  }


  if($conflict_count > 0){

  	array_multisort(array_column($resultsss, 'conflict'),  SORT_DESC,
                array_column($resultsss, 'id'), SORT_DESC,
                $resultsss);


  }else{
  	
  	$keys = array_column($resultsss, 'id');

	array_multisort($keys, SORT_DESC, $resultsss);

  }

	
 
    return $resultsss;
}
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key => $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}
function booking_tab()
{
  
  update_user_meta(get_current_user_ID(),"booking_tab_".$_POST['page_type'],$_POST['booking_tab']);
  exit();
}
add_action('wp_ajax_booking_tab', 'booking_tab', 10);
add_action('wp_ajax_nopriv_booking_tab', 'booking_tab', 10);


function booking_user_group_selected_id()
{
  
  update_user_meta(get_current_user_ID(),"booking_user_group_selected_id",$_POST['booking_user_group_selected_id']);
  exit();
}
add_action('wp_ajax_booking_user_group_selected_id', 'booking_user_group_selected_id', 10);
add_action('wp_ajax_nopriv_booking_user_group_selected_id', 'booking_user_group_selected_id', 10);

function save_active_column()
{

  delete_user_meta(get_current_user_ID(),"active_column");

  foreach ($_POST["active_column"] as $key => $active_column) {
     add_user_meta(get_current_user_ID(),"active_column",$active_column);
  }
  $data = array("sucess"=>"true");

  wp_send_json($data);
  
  exit();
}
add_action('wp_ajax_save_active_column', 'save_active_column', 10);
add_action('wp_ajax_nopriv_save_active_column', 'save_active_column', 10);

function save_single_booking()
{

    global $wpdb;

    $statuss = "";

    $order_id_exist = $_POST["order_id"];

    $booking_data = $wpdb -> get_row( 'SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar` WHERE `id`=' . esc_sql( $_POST["booking_id"] ), 'ARRAY_A' );

    if(isset($booking_data["status"])){
        $statuss = $booking_data["status"];
    }

   


   


    if(isset($_POST["billing_name"])){
      $billing_name  = explode(" ", $_POST["billing_name"]);

      if(isset($name[0])){
          update_user_meta($_POST["booking_author"],"billing_first_name",$name[0],true);
      }
      if(isset($name[1])){
          update_user_meta($_POST["booking_author"],"billing_first_name",$name[1],true);
      }
    }

    update_user_meta($_POST["booking_author"],"billing_email",$_POST["billing_email"],true);
    update_user_meta($_POST["booking_author"],"billing_phone",$_POST["billing_tlf"],true);
    update_user_meta($_POST["booking_author"],"billing_address_1",$_POST["billing_address"],true);
    update_user_meta($_POST["booking_author"],"billing_postcode",$_POST["billing_zip"],true);
    update_user_meta($_POST["booking_author"],"billing_city",$_POST["billing_city"],true);

   /* if($statuss == "confirmed"){
          $wpdb->update( 
              $wpdb->prefix . "bookings_calendar", 
              array( 
                  'price' => $_POST["price"] ,  // string
                  'order_id' => "" ,  // string
                  'status' => "waiting",
                  'description' => $_POST["description"]   // integer (number) 
              ), 
              array( 'id' => $_POST["booking_id"] )
          );

        wp_send_json_success(Listeo_Core_Bookings_Calendar::set_booking_status( $_POST['booking_id'], 'confirmed'));
    }else{
        $wpdb->update( 
          $wpdb->prefix . "bookings_calendar", 
          array( 
              'price' => $_POST["price"] , 
              'description' => $_POST["description"]   // integer (number) 
          ), 
          array( 'id' => $_POST["booking_id"] )
       );

    }*/
    
       $wpdb->update( 
          $wpdb->prefix . "bookings_calendar", 
          array( 
            //  'price' => $_POST["price"] , 
              'description' => $_POST["description"]   // integer (number) 
          ), 
          array( 'id' => $_POST["booking_id"] )
       );

       if($booking_data["booking_author"] != "" && $booking_data["owner_id"] != ""){
          $log_args = array(
              'action' => "booking_updated",
              'related_to_id' => $booking_data["owner_id"],
              'user_id' => $booking_data["booking_author"],
              'post_id' => $$booking_data["id"]
          );
          listeo_insert_log($log_args);

      }
    
   
    $data = array("sucess"=>true);

    wp_send_json($data);


  
    exit();
}
add_action('wp_ajax_save_single_booking', 'save_single_booking', 10);
add_action('wp_ajax_nopriv_save_single_booking', 'save_single_booking', 10);

function savenote()
{

    global $wpdb;

    $wpdb->update( 
        $wpdb->prefix . "bookings_calendar", 
        array( 
            'description' => $_POST["description"]   // integer (number) 
        ), 
        array( 'id' => $_POST["booking_id"] )
    );

    $data = array("sucess"=>true);

    wp_send_json($data);


  
    exit();
}
add_action('wp_ajax_savenote', 'savenote', 10);
add_action('wp_ajax_nopriv_savenote', 'savenote', 10);

function get_rec_html()
{

      $order = wc_get_order( $_POST["order_id"] );


      ob_start();
      //include the specified file
      require (get_stylesheet_directory()."/woocommerce/checkout/thankyou.php");

      $order_content = ob_get_clean();

      echo $order_content;
      
    

    die;


  
    exit();
}
add_action('wp_ajax_get_rec_html', 'get_rec_html', 10);
add_action('wp_ajax_nopriv_get_rec_html', 'get_rec_html', 10);


function gibbs_booking_data()
{

  global $wpdb;
  $page_type = $_POST["page_type"];
  $page = ! empty( $_POST['page'] ) ? (int) $_POST['page'] : 1;

  $active = $_POST["status"];

  $pagination_data = array();

  if(isset($_POST["search_text"]) && $_POST["search_text"] != ""){

  }else{
    if(isset($_POST["export_booking_csv"]) && $_POST["export_booking_csv"] != ""){

    }else{
      $pagination_data = array("page"=>$page,"totalCount"=>$_POST["totalCount"]);
    }
     
  }

  $has_page_data = array();

  if(!empty($pagination_data)){
    $results = resultData($_POST["status"],$pagination_data);
    
    $has_page_data["total"] = $results["total"];
    $has_page_data["total_pages"] = $results["total_pages"];
    $results = $results["data"];
    $count_result = (int) $has_page_data["total"]; 

    

    
  }else{
    $results = resultData($_POST["status"]);
    $count_result = count($results);
  }


  $count_waiting = count(resultData('waiting'));

  /*$count_waiting = count(resultData('waiting'));
  $count_approved = count(resultData('approved'));
  $count_expired = count(resultData('expired'));
  $count_all = count(resultData('all'));
  $count_invoice =count(resultData('invoice'));
  $count_invoice_sent =count(resultData('invoice_sent'));
  $count_paid = count(resultData('paid'));*/
  //echo count($results); die;

  $columns  = get_columns();

  $active_columns  = get_active_columns();
  

  $related = 0;

  if(isset($_POST["export_booking_csv"]) && $_POST["export_booking_csv"] != ""){
     $related = 1;
  }
 



  $booking_data = resultFilter($results,$related);

  


  $url_csv = "";
  $export_csv = "";
  if(isset($_POST["export_booking_csv"]) && $_POST["export_booking_csv"] != ""){
    delete_csv_file();
    //$url_csv =  $export_booking_csv  = export_booking_csv($booking_data,$active_columns,$active);
    $export_csv = "true";
    $_POST["totalCount"] = "1000000000000000";

   
  }

  if(isset($_POST["search_text"]) && $_POST["search_text"] != ""){

    $search_text = $_POST["search_text"];


    $search_booking_data = array_filter($booking_data, function ($value) use ($search_text) {

        if (strpos(strtolower($value->listing_name), strtolower($search_text)) !== false) {
           return $value;
        }else if (strpos(strtolower($value->bookings_author_name), strtolower($search_text)) !== false) {
          return $value;
        }else if (strpos(strtolower($value->display_name), strtolower($search_text)) !== false) {
          return $value;
        }else if (strpos(strtolower($value->customer_email), strtolower($search_text)) !== false) {
          return $value;
        }else if (strpos(strtolower($value->id), strtolower($search_text)) !== false) {
          return $value;
        }else if (strpos(strtolower($value->order_id), strtolower($search_text)) !== false) {
          return $value;
        }

    });

    $booking_data = $search_booking_data;
  }


  if(isset($has_page_data["total"])){

    $total = $has_page_data["total"];
    $limit = $_POST["totalCount"];
    $totalPages = $has_page_data["total_pages"];
    $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
    $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
    $offset = ($page - 1) * $limit;
    if( $offset < 0 ) $offset = 0;


  }else{

    $total = count( $booking_data ); //total items in array    
    $limit = $_POST["totalCount"]; //per page    
    $totalPages = ceil( $total/ $limit ); //calculate total pages
    $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
    $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
    $offset = ($page - 1) * $limit;
    if( $offset < 0 ) $offset = 0;
  
    $booking_data = array_slice( $booking_data, $offset, $limit );

  }

 

  






  ob_start();
  //include the specified file
  require (__DIR__."/booking_table.php");

  $content = ob_get_clean();

  ob_start();
  //include the specified file
  require (__DIR__."/modules/bulk_action.php");

  $bulk_action = ob_get_clean();

  $csv_file_name = get_current_user_ID().time()."_export_booking_csv";


  $data = array("content"=>$content,"bulk_action"=>$bulk_action, "count"=>$count_result,"url_csv"=>$url_csv,"export_csv"=>$export_csv,"booking_data"=>$booking_data,"count_waiting"=>$count_waiting,"count_approved"=>$count_approved,"count_expired"=>$count_expired,"count_all"=>$count_all,"count_invoice"=>$count_invoice,"count_invoice_sent"=>$count_invoice_sent,"count_paid"=>$count_paid, "csv_file_name"=> $csv_file_name);

  wp_send_json($data);


  

  exit();
}
add_action('wp_ajax_gibbs_booking_data', 'gibbs_booking_data', 10);
add_action('wp_ajax_nopriv_gibbs_booking_data', 'gibbs_booking_data', 10);


function resultData($status, $pagination_data = array()){

  global $wpdb;


  $user_id = get_current_user_ID();
  if( $status == 'approved' ) {
    $status_sql = "status IN ('confirmed')";
  }elseif( $status == 'expired' ) {
    $status_sql = "(status IN ('expired','cancelled','declined') Or (status = 'paid' AND (fixed = 1)))";
  }elseif( $status == 'all' ) {
    $status_sql = "status IN ('waiting','approved','expired','cancelled','declined','confirmed','paid','completed')";
  }elseif( $status == 'invoice' ) {
    $status_sql = "status = 'paid' AND (fixed = 2 OR fixed = 3)";
  }elseif( $status == 'invoice_sent' ) {
    $status_sql = "status = 'paid' AND (fixed = 4)";
  }else if( $status == 'paid' ) {
    
     if($_POST["page_type"] == "buyer"){
       $status_sql = "status='$status'";
    }else{
       $status_sql = "status = 'paid' AND (fixed = 0)";
    }
  } else {
    $status_sql = "status='$status'";
  }
  $sql = '';

  $user_idss = array();

  $check_user_ids = "";

  if(isset($_POST['listing_ids']) && !empty($_POST['listing_ids'])){

      $listing_ids = implode(",", $_POST['listing_ids']);

      $sql .= " AND listing_id IN (".$listing_ids.")";
  }

  if(isset($_POST['search_text']) && !empty($_POST['search_text'])){




      //$sql .= " AND listing_id IN (".$listing_ids.")";
  }
  if(isset($_POST['customer_ids']) && !empty($_POST['customer_ids'])){

      
      $customer_ids_data = implode(",",$_POST['customer_ids']); 
      $sql .= " AND bookings_author IN (".$customer_ids_data.")";
  }
  if(isset($_POST['order_number_checkbox']) && $_POST['order_number_checkbox'] == "true"){

      $sql .= " AND (order_id != '' OR order_id != '0') ";
  }

  if($_POST["page_type"] == "buyer"){

   // $sql .= " AND bookings_author = $user_id";

    $sql .= get_buyer_groups_sql($_POST["group_id"],$user_id);

  }else{

    $sql .= get_groups_sql($_POST["group_id"],$user_id);

   /* if($_POST["group_id"] != "0"){

        $group_id = $_POST["group_id"];


        $group_user_ids = array();

        $posts_table = $wpdb->prefix . 'posts';  // table name
        $posts_table_sql = "select ID from `$posts_table` where users_groups_id = $group_id";
        $posts_data = $wpdb->get_results($posts_table_sql);

        $listing_ids = array();

        foreach ($posts_data as $key => $posts_da) {
          $listing_ids[] = $posts_da->ID;
        }
        $group_admin = get_group_admin();

        if($group_admin != ""){

          $posts_table = $wpdb->prefix . 'posts';  // table name
          $posts_table_sql = "select ID from `$posts_table` where post_author = $group_admin";
          $posts_data = $wpdb->get_results($posts_table_sql);

          foreach ($posts_data as $key => $posts_da) {
            $listing_ids[] = $posts_da->ID;
          }

          
        }
        $listing_ids = implode(",", $listing_ids);
        $sql .= " AND listing_id IN ($listing_ids)";

       
    }else{
        $sql .= " AND owner_id = $user_id";
    }*/
  }
  
  

  
  if(isset($_POST['startDataSql']) && $_POST['startDataSql'] != "" && isset($_POST['endDataSql']) && $_POST['endDataSql'] != ""){

    if($_POST['date_close'] != "true"){

      $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $_POST['startDataSql'] ) ) ) );
      $date_end = esc_sql ( date( "Y-m-d"." 23:59:59", strtotime( $wpdb->esc_like( $_POST['endDataSql'] ) ) ) );

      $sql .= " AND ((`date_start` >= '$date_start' AND `date_end` <= '$date_end') OR (`date_start` <= '$date_start' AND `recurrenceRule` != ''))";

    }

        
  }

  if(isset($_POST['startCreatedDataSql']) && $_POST['startCreatedDataSql'] != "" && isset($_POST['endCreatedDataSql']) && $_POST['endCreatedDataSql'] != ""){

    if($_POST['date_close2'] != "true"){

      $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $_POST['startCreatedDataSql'] ) ) ) );
      $date_end = esc_sql ( date( "Y-m-d"." 23:59:59", strtotime( $wpdb->esc_like( $_POST['endCreatedDataSql'] ) ) ) );

      $sql .= " AND (`created_at` >= '$date_start' AND `created_at` <= '$date_end')";

    }

        
  }
 

  $sql_query = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE $status_sql $sql order by created_at desc"; 
  
  if(!empty( $pagination_data )){
    $records_per_page = $pagination_data["totalCount"];
    $current_page = $pagination_data["page"];
    $sql2_count = "SELECT COUNT(*) FROM `" . $wpdb->prefix . "bookings_calendar` WHERE $status_sql $sql";
    $total_records = $wpdb->get_var($sql2_count);
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    $sql_query .= " limit ".$offset.", ".$records_per_page; 
    $results  = $wpdb -> get_results($sql_query);
    $results = resultFilter($results,0);

    $dataa = array("total" => $total_records, "total_pages" =>$total_pages,"data" => $results);

    return $dataa;
  }
  $results  = $wpdb -> get_results($sql_query);

  $related = 0;

  if(isset($_POST["export_booking_csv"]) && $_POST["export_booking_csv"] != ""){
     $related = 1;
  }

  

 
  $results = resultFilter($results,$related);

  return $results;
}


function get_columns(){
  return $columns = array("name"=>"Kunde","listing"=>"Annonse","booking_id"=>"Booking ID","date"=>"Dato","price"=>"Pris","status"=>"Status","purpose"=>"Hensikt","discount_group"=>"Målgrupperabatt","created_at"=>"Opprettet","last_updated"=>"Sist endret","listing_name"=>"Annonse navn","sub_listing_name"=>"Bane/inndeling","booking_message"=>"Booking kommentar","order_number"=>"Ordre","customer_name"=>"Kunde: Navn","customer_email"=>"Kunde: E-post","customer_tlf"=>"Kunde: Tlf","customer_address"=>"Kunde: Adresse","customer_zip"=>"Kunde: Postnr","customer_city"=>"Kunde: By","billing_name"=>"Faktura: Navn","billing_email"=>"Faktura: E-post","billing_tlf"=>"Faktura: Tlf","billing_address"=>"Faktura: Adresse","billing_zip"=>"Faktura: Postnr","billing_city"=>"Faktura: By","coupon"=>"Kupong", "refund"=>"Refund",  "custom_fields" => "Annen informasjon","payment_type" => "Payment type","guest_amount" => "Antall");
}
function get_active_columns($page_type = ""){
  $active_column = get_user_meta(get_current_user_ID(),"active_column");
  if($page_type == "buyer"){
    if (($key = array_search("booking_notes", $active_column)) !== false) {
        unset($active_column[$key]);
    }
  }

  //echo "<pre>"; print_r($active_column); die;
  if(empty($active_column)){
      return array("name","listing","booking_id","date","price","status");
  }else{
      return $active_column;
  }
  
}
function single_get_columns(){
  return $columns = array("listing"=>"Annonse","booking_id"=>"Booking ID","date"=>"Dato","price"=>"Pris","status"=>"Status");
}
function single_get_active_columns(){
  return $active_column = array("listing","booking_id","date","price","status");
  
}

function export_booking_csv($booking_data,$active_columns,$active,$page_type = "",$csv_file_name = ""){

      if($csv_file_name != ""){
        $file_name = $csv_file_name;
      }else{
        $file_name = get_current_user_ID()."_export_booking_csv";
      }

      

      header('Content-Type: text/csv; charset=utf-8');  
      header('Content-Disposition: attachment; filename='.$file_name.'.csv');  

      
      $f = fopen(plugin_dir_path(__FILE__).'csv/'.$file_name.'.csv', 'a');

      $fieldss = array("id"=>"OrdreID","booking_id"=>"Booking ID","main_listing"=>"Annonse","date"=>"Dato","tax"=>"Mva","price_tax"=>"Pris ink. Mva","purpose"=>"Hensikt","selected_discount"=>"Målgruppe rabatt","created_at"=>"Opprettet","updated_at"=>"Sist endret","refund"=>"Refund","status"=>"Booking status","payment_method"=>"Betalings metode","booking_message"=>"Booking kommentar","booking_notes"=>"Booking notater","profile_type"=>"Profile type","org_number"=>"Org nummer","customer_name"=>"Kunde: Navn","customer_email"=>"Kunde: E-post","customer_tlf"=>"Kunde: Tlf","customer_address"=>"Kunde: Adresse","customer_zip"=>"Kunde: Postnr","customer_city"=>"Kunde: By","billing_name"=>"Faktura: Navn","billing_email"=>"Faktura: E-post","billing_tlf"=>"Faktura: Tlf","billing_address"=>"Faktura: Adresse","billing_zip"=>"Faktura: Postnr","billing_city"=>"Faktura: Navn", "fields_data_csv" => "Annen informasjon");

      if($page_type == "buyer"){
          unset($fieldss["booking_notes"]);
      }
      fputcsv($f, $fieldss);

      global $wpdb;





      foreach ($booking_data as $key_v => $data) {
            $name = $data->bookings_author_name;
            $listing = ucfirst($data->listing_name);

            if(isset($data->sub_listing_title) && $data->sub_listing_title != ""){
               $listing = $listing." (".$data->sub_listing_title.")";
            }
            $booking_id = $data->id;
            $booking_date = date("d M Y H:i",strtotime($data->date_start))." - ".date("d M Y H:i",strtotime($data->date_end));
            if($data->price == "" || $data->price == 0){   
                $price =  __("Free","gibbs");
            }else{
                $price = $data->price;
            }

            if($active == "invoice" && $data->fixed == "2"){ 
                $status = "Sesongbooking";
            }elseif($active == "invoice" && $data->fixed == "3"){ 
              $status = "Usendt faktura";
            }elseif($active == "invoice" && $data->fixed == "4"){ 
              $status = "Sendt faktura";
            }else{ 
              $status =  $data->status;
            } 
            $created_at = $data->created;

            $sql12 = "SELECT post_title FROM `" . $wpdb->prefix . "posts` WHERE post_type='listing' AND parent_listing_id=".$data->listing_id;
            $sub_listing_data = $wpdb->get_results($sql12); 

            $sub_listing = array();

            foreach ($sub_listing_data as  $sub_listing_data_value) {
               $sub_listing[] =  $sub_listing_data_value->post_title;
            }
            $age_group = "";

            $sub_listing = implode(", ", $sub_listing);
            if($data->application_id != '' && $data->application_id != null){
               $sql12 = "SELECT age_group.name FROM `applications` JOIN age_group ON age_group.id = applications.age_group_id WHERE applications.id=".$data->application_id;
               $select_agae_groupppp = $wpdb->get_row($sql12); 


               if(isset($select_agae_groupppp->name)){
                  $age_group = $select_agae_groupppp->name;
               }
            }  
            $data->purpose = "";

            if(isset($data->comment) && $data->comment != ""){
              $comment = json_decode($data->comment);
              if(isset($comment->purpose)){
                $data->purpose = $comment->purpose;
              }
            }

            if($page_type == "buyer"){
               $dataa = array($data->order_id, $data->id, $listing, $booking_date,$data->tax,$price,$data->purpose,$data->discount_group,$created_at,$data->updated_at,$data->refund_amount,$status,$data->payment_method,$data->message, $data->profile_type,$data->company_number,$data->display_name,$data->customer_email,$data->customer_tlf,$data->customer_address,$data->customer_zip,$data->customer_city,$data->billing_name,$data->billing_email,$data->billing_tlf,$data->billing_address,$data->billing_zip,$data->billing_city, $data->fields_data_csv);
            }else{
               $dataa = array($data->order_id, $data->id, $listing, $booking_date,$data->tax,$price,$data->purpose,$data->discount_group,$created_at,$data->updated_at,$data->refund_amount,$status,$data->payment_method,$data->message,$data->description, $data->profile_type,$data->company_number,$data->display_name,$data->customer_email,$data->customer_tlf,$data->customer_address,$data->customer_zip,$data->customer_city,$data->billing_name,$data->billing_email,$data->billing_tlf,$data->billing_address,$data->billing_zip,$data->billing_city, $data->fields_data_csv);
            }

           

            //$listing_name = ucfirst($data->listing_name);
           
          fputcsv($f, $dataa);  
      }
      fclose($f);

     return  $url_csv =  plugin_dir_url(__FILE__)."csv/".$file_name.".csv"; 
}

function checkConflictBooking($date_start,$date_end,$listing_id,$booking_id=""){

    global $wpdb;


    $start_date_time = date("Y-m-d H:i", strtotime($date_start));
    $end_date_time = date("Y-m-d H:i", strtotime($date_end));

    $start_date_time = explode(" ", $start_date_time);
    $end_date_time = explode(" ", $end_date_time);

    $start_date =  $start_date_time[0];
    $end_date =  $end_date_time[0];


    $booking_table = $wpdb->prefix . "bookings_calendar";

    

    $list_query = "listing_id =".$listing_id; 

   // $query = "select *  from `$booking_table` WHERE (`date_start` > '".$date_start."'  ) AND (`date_start`  < '".$date_end."' ) AND listing_id =".$listing_id;
    $query = "select *  from `$booking_table` WHERE ((`date_start` > '$date_start' AND `date_start` < '$date_end') OR (`date_end` > '$date_start' AND `date_end` < '$date_end') OR (`date_start` >= '$date_start' AND `date_end` < '$date_end') OR (  '$date_start' >= `date_start` AND '$date_end' < `date_end` )  ) AND (status in ('confirmed','paid','waiting','completed')) AND $list_query"; 

    $results  = $wpdb -> get_results($query);

 
    $conflict = "";

    if(count($results) > 0){
       $conflict = "true";
       $conflict_val = 1;
       
       
    }else{
       $conflict_val = 2;
    }

    if($booking_id != ""){
        $wpdb->update($booking_table, array('conflict_status'=>$conflict_val), array('id'=>$booking_id));
    }
    return $conflict;
}


function get_related_booking_data($booking){
    global $wpdb;

    $bookings = array();

      //if($booking->first_event_id != ""){

        $sql_event = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE  first_event_id = ".$booking->id;
        $results_event  = $wpdb -> get_results($sql_event);


       

       

        if(count($results_event) > 0){

          foreach ($results_event as $key => $results_e) {
            if($results_e->id != $booking->id){
              $bookings[] = $results_e;
            }
            
          }
        }
     // } 
    $booking_data = resultFilter($bookings,1);  
     


    return $booking_data;


}
function rec_dates($booking,$exp_time = ""){

    $rec_booking_data = array();

    $rules = $booking->recurrenceRule;

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
    $rulesss["DTSTART"] = date("Y-m-d H:i:s",strtotime($booking->date_start));
   // echo "<pre>"; print_r($rulesss); die;

    
    if(!class_exists("RRule\RRule")){
       $path = plugin_dir_path(__FILE__)."vendor/autoload.php";

       include($path);
    }

    

    $rrules = new RRule\RRule($rulesss);

   
    $exp_dates = array();

    $recurrenceException = $booking->recurrenceException;

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
    
    $kk = 0;





    foreach ($rrules as $occurrence ) {

        $conflict = "";
        $exp = "";

        $item_start = $occurrence->format('Y-m-d')." ".date("H:i:s",strtotime($booking->date_start));
        $item_end = $occurrence->format('Y-m-d')." ".date("H:i:s",strtotime($booking->date_end));




         


        if(!in_array($occurrence->format('Y-m-d'), $exp_dates)){ 
          

            $datetime = new DateTime($item_start);

            $tempRecExp =  $datetime->format(DATE_ATOM);

            $tempRecExp =  str_replace('+00:00', 'Z', $tempRecExp);

            if($exp_time != ""){
               $exp_time = date("H:i:s",strtotime($exp_time));
               $item_start_exp = date("Y-m-d",strtotime($item_start))." ".$exp_time;

              $datetime = new DateTime($item_start_exp);

              $tempRecExp =  $datetime->format(DATE_ATOM);

              $tempRecExp =  str_replace('+00:00', 'Z', $tempRecExp);

              $tempRecExp = date("Y-m-d", strtotime($tempRecExp));
            }




       


          /*  $date_start_to_time = strtotime($booking->date_start);

            $date_end_to_time = strtotime($booking->date_end);

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
            }*/
            if($booking->status == "waiting" || $booking->status == "approved" || $booking->status == "paid" || $booking->status == "completed" || $booking->status == "confirmed"){
              if($booking->conflict_status == "1" && ($booking->date_start >= $item_start && $booking->date_end >= $item_end)){
                $conflict_data = "true";
              }else if($result->conflict_status == "2" && ($booking->date_start >= $item_start && $booking->date_end <= $item_end)){
                $conflict_data = "";
              }else{
                //$conflict_data = checkConflictBooking($item_start,$item_end,$booking->listing_id);
                $conflict_data = "";
              }
              

              if($conflict_data == "true"){
                 

                  if(!in_array(date("Y-m-d",strtotime($item_start)), $exp_dates)){

                     $conflict = "true";
                  }   
              }  
            }
    /*
            $rec_booking_data[$kk]["date_start"] = $item_start;
            $rec_booking_data[$kk]["date_end"] = $item_end;
            $rec_booking_data[$kk]["conflict"] = $conflict;
            $rec_booking_data[$kk]["exp"] = $exp;
            $rec_booking_data[$kk]["exp_date"] = $exp;*/
             $data_new = (array) $booking;
             $data_new["date_start"] =  $item_start;
             $data_new["date_end"] =  $item_end;
             $data_new["rec_date"] =  $item_start;
             $data_new["rec_exp"] =   $tempRecExp;
             $data_new["conflict"] =  $conflict;
             $rec_booking_data[$kk] = (object) $data_new;
             $kk++;
       }
    }

    return $rec_booking_data;
}
function rec_booking(){
  $booking_type = "rec";
  $page_type = $_POST["page_type"];
  $exp_time = $_POST["exp_time"];
  $booking = (array) get_single_booking_by_id($_POST["booking_id"],$_POST["user_id"],$_POST["group_id"],$_POST["page_type"],1);

  $booking_ann = (object) $booking;

  $booking_data_related = get_related_booking_data($booking_ann);

  $booking_data = array();

  $conflict_count = 0;

  $kkkk = 0;

  if(isset($booking_ann->recurrenceRule) && $booking_ann->recurrenceRule != ""){

    $booking_data_rec = rec_dates($booking_ann,$exp_time);





    foreach ($booking_data_rec as $key => $bkk) {
       $booking_data[$kkkk] = $bkk;

       if($bkk->conflict == "true"){
          $conflict_count++;
       }

       $kkkk++;
    }
   
    
  }

  foreach ($booking_data_related as $key => $booking_rel) {

      if(isset($booking_rel->recurrenceRule) && $booking_rel->recurrenceRule != ""){

        $booking_data_rec = rec_dates($booking_rel,$exp_time);

        foreach ($booking_data_rec as $key => $bkk) {
           $booking_data[$kkkk] = $bkk;

           if($bkk->conflict == "true"){
              $conflict_count++;
           }

           $kkkk++;
        }
       
        
      }else{
        $booking_data[$kkkk] = $booking_rel;
        $kkkk++;
      }
    
  }

/*
  foreach ($booking_data_related as $key => $booking_data_rel) {
     $booking_data[$kkkk] = $booking_data_rel;
  }

  $conflict_count = 0;

  if(isset($_POST["bookings_data"])){
    foreach ($_POST["bookings_data"] as $key => $bookings_d) {


       $date_start = date("Y-m-d",strtotime($bookings_d["rec_date"]))." ".date("H:i:s",strtotime($booking["date_start"]));
     
       $date_end = date("Y-m-d",strtotime($bookings_d["rec_date"]))." ".date("H:i:s",strtotime($booking["date_end"]));
        if($bookings_d["conflict_status"] == "1"){
           $conflict = "true";
        }else if($bookings_d["conflict_status"] == "2"){
          $conflict = "";
        }else{
          $conflict = checkConflictBooking($date_start,$date_end,$booking["listing_id"],$booking["id"]);
        }
       

       if($conflict == "true"){
          $conflict_count++;
       }

        


       $data_new = $booking;
       $data_new["rec_date"] =  $bookings_d["rec_date"];
       $data_new["rec_exp"] =  $bookings_d["rec_exp"];
       $data_new["conflict"] =  $conflict;
       $booking_data[$i] = (object) $data_new;
       $i++;
    }
  }  */

  if($conflict_count > 0){

    array_multisort(array_column($booking_data, 'conflict'),  SORT_DESC,
                array_column($booking_data, 'id'), SORT_DESC,
                $booking_data);


  }else{
    
     $keys = array_column($booking_data, 'id');

     array_multisort($keys, SORT_DESC, $booking_data);

  }

  $page = ! empty( $_POST['page'] ) ? (int) $_POST['page'] : 1;
  $total = count( $booking_data ); //total items in array    
  $limit = $_POST["totalCount"]; //per page    
  $totalPages = ceil( $total/ $limit ); //calculate total pages
  $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
  $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
  $offset = ($page - 1) * $limit;
  if( $offset < 0 ) $offset = 0;

  $booking_data = array_slice( $booking_data, $offset, $limit );

  $columns = array("listing"=>"Annonse","booking_id"=>"Booking ID","date"=>"Dato","status"=>"Status");

  $active_columns  = array("listing","booking_id","date","status");

  ob_start();
  //include the specified file
  require (__DIR__."/booking_table.php");

  $content = ob_get_clean();



  $data = array("content"=>$content,"conflict_count"=>$conflict_count);

  wp_send_json($data);
  
 
  exit();
}
add_action('wp_ajax_rec_booking', 'rec_booking', 10);
add_action('wp_ajax_nopriv_rec_booking', 'rec_booking', 10);

function add_rec_exp(){
  global $wpdb;
  $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE id=".$_POST["booking_id"];
  $result  = $wpdb -> get_row($sql);
  $recurrenceException = $result->recurrenceException;
  $exp = "";

  $exp_dates = [];


  if($recurrenceException == ""){
     $exp_dates[] = $_POST["rec_exp"];
  }else{

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
      $exp_dates[] = $_POST["rec_exp"];

  }

  $data = json_encode($exp_dates);


    $wpdb->update( 
        $wpdb->prefix . "bookings_calendar", 
        array( 
            'recurrenceException' => $data, 
        ), 
        array( 'id' => $_POST["booking_id"] )
    );


  wp_send_json($data);
  
 
  exit();
}
add_action('wp_ajax_add_rec_exp', 'add_rec_exp', 10);
add_action('wp_ajax_nopriv_add_rec_exp', 'add_rec_exp', 10);

function remove_rec_exp(){
  global $wpdb;
  $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE id=".$_POST["booking_id"];
  $result  = $wpdb -> get_row($sql);
  $recurrenceException = $result->recurrenceException;
  
  $exp = str_replace(",".$_POST["rec_exp"], "", $recurrenceException);
  $exp = str_replace($_POST["rec_exp"], "", $exp);

    $wpdb->update( 
        $wpdb->prefix . "bookings_calendar", 
        array( 
            'recurrenceException' => $exp, 
        ), 
        array( 'id' => $_POST["booking_id"] )
    );


  wp_send_json($data);
  
 
  exit();
}
add_action('wp_ajax_remove_rec_exp', 'remove_rec_exp', 10);
add_action('wp_ajax_nopriv_remove_rec_exp', 'remove_rec_exp', 10);


function export_boooking_csv(){

  global $wpdb;

  $booking_data = array();

  foreach ($_POST["bookings"] as $key => $booking_d) {

      $booking =  get_booking_by_id($booking_d["booking_id"],1);



      if(isset($booking_d["rec_date"]) && $booking_d["rec_date"] != ""){

        if(isset($booking_d["count_booking"])){
            $bk_price = ($booking->price / $booking_d["count_booking"]);

            $booking->price = round($bk_price,2);
        }



         $booking->date_start = date("Y-m-d",strtotime($booking_d["rec_date"]))." ".date("H:i:s",strtotime($booking->date_start));
         $booking->date_end = date("Y-m-d",strtotime($booking_d["rec_date"]))." ".date("H:i:s",strtotime($booking->date_end));

         $booking_data[] = $booking;

      }else{

         $booking_data[] = $booking;

      }  
    
  }


  $urlcsv = export_booking_csv($booking_data,array(),"", $_POST["page_type"],$_POST["csv_file_name"]);

  $data = array("url"=>$urlcsv);

  wp_send_json($data);
  
 
  exit();
}
add_action('wp_ajax_export_boooking_csv', 'export_boooking_csv', 10);
add_action('wp_ajax_nopriv_export_boooking_csv', 'export_boooking_csv', 10);


function get_booking_by_id($id,$related = 0){
  global $wpdb;
  $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE id=$id";
  $results  = $wpdb ->get_results($sql);




  $results = resultFilter($results, $related);



  $result_data = (object) array();

  foreach ($results as $key => $result) {
     $result_data = $result;
     break;
  }
  return  $result_data;
}

add_action('wp_ajax_delete_csv_file', 'delete_csv_file', 10);
add_action('wp_ajax_nopriv_delete_csv_file', 'delete_csv_file', 10);


function delete_csv_file(){
  if(isset($_POST["csv_file_name"])){
    $file_name = $_POST["csv_file_name"];
  }else{
    $file_name = get_current_user_ID()."_export_booking_csv";
  }
  
  if(file_exists(plugin_dir_path(__FILE__).'csv/'.$file_name.'.csv')){
     unlink(plugin_dir_path(__FILE__).'csv/'.$file_name.'.csv');
  }
}

add_action('wp_ajax_change_status_first_event', 'change_status_first_event', 10);
add_action('wp_ajax_nopriv_change_status_first_event', 'change_status_first_event', 10);

function change_status_first_event(){
  global $wpdb;
  $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE first_event_id=".$_POST["booking_id"];
  $results  = $wpdb ->get_results($sql);

  foreach ($results as $key => $result) {
     if($_POST["booking_id"] != $result->id){
        $data = array( 
                  'status' => $_POST["status"], 
                );
        if(isset($_POST["fixed"]) && $_POST["fixed"] != ""){
          $data["fixed"] = $_POST["fixed"];
        }
        if(isset($_POST["order_id"]) && $_POST["order_id"] != ""){
          $data["order_id"] = $_POST["order_id"];
        }

        $wpdb->update( 
            $wpdb->prefix . "bookings_calendar", 
            $data, 
            array( 'id' => $result->id )
        );
        if($result->booking_author != "" && $result->owner_id != ""){
          $log_args = array(
              'action' => "booking_updated",
              'related_to_id' => $result->owner_id,
              'user_id' => $result->booking_author,
              'post_id' => $result->id
          );
          listeo_insert_log($log_args);
        }
     }
  }
  $data = array("sucess"=>true);

  wp_send_json($data);
 

}

add_action('wp_ajax_edit_field_save', 'edit_field_save', 10);
add_action('wp_ajax_nopriv_edit_field_save', 'edit_field_save', 10);

function edit_field_save(){
  global $wpdb;

  $data = array();

  if(isset($_POST["fields"])){

      foreach ($_POST["fields"] as $key_res => $res) {
          foreach ($res as $key_index => $from) {
               $data[$key_index][$key_res] = $res[$key_index];
          }
      }
  }
  $bookings_table = $wpdb->prefix . 'bookings_calendar';

  $data = maybe_serialize($data);

  $wpdb->update($bookings_table, array(
          'fields_data'            => $data,
    ),array("id"=>$_POST['booking_id']));

  wp_redirect($_SERVER['HTTP_REFERER']);
  exit;

}

add_action('wp_ajax_change_fixed', 'change_fixed', 10);
add_action('wp_ajax_nopriv_change_fixed', 'change_fixed', 10);

function change_fixed(){
  global $wpdb;

 
  $bookings_table = $wpdb->prefix . 'bookings_calendar';

  $wpdb->update($bookings_table, array(
          'fixed'            => $_POST["fixed"],
    ),array("id"=>$_POST['booking_id']));

  $data = array();

  wp_send_json($data);
  
 
  exit();

}
function utf8_converter_dd($array)
{
    array_walk_recursive($array, function (&$item, $key) {
        if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
        }
    });

    return $array;
}