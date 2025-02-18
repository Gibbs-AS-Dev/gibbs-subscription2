
<?php




$user_id = get_current_user_ID();

$gibbs_user_groups = gibbs_user_groups($user_id); 


$user_group_data = array();
$user_group_data[0]->id = 0;
$user_group_data[0]->name = __("Mine","gibbs");
$kkk = 1;

$roless = array();
$users_exits_ids = array();
foreach ($gibbs_user_groups as $key => $user_group_da) {
	if($user_group_da->role == "3"){
		$user_group_data[$kkk]->id = $user_group_da->id;
		$user_group_data[$kkk]->name = $user_group_da->name;
		$roless[] = $user_group_da->role;
		$users_exits_ids[] = $user_group_da->id;
		$kkk++;
	}	
}
$current_user = wp_get_current_user();

$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

if($active_group_id != ""){
	$group_id = $active_group_id;
}else{
	$group_id = "0";
}

$booking_user_group_selected_id = $group_id;

if($booking_user_group_selected_id == ""){
	if(!empty($user_group_data)){
		$booking_user_group_selected_id = $user_group_data[0]->id;
	}
}else{
	if(!in_array($booking_user_group_selected_id, $users_exits_ids)){
        $booking_user_group_selected_id = $user_group_data[0]->id;
	}
}



//echo "<pre>"; print_r($booking_user_group_selected_id); die;



/*$count_waiting = count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'waiting'));
$count_approved = count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'approved'));
$count_completed = count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'completed'));
$count_expired = count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'expired'));
$count_all = count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'all'));
$count_invoice =count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'invoice'));
$count_invoice_sent =count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'invoice_sent'));
$count_paid = count(gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'paid'));*/

$count_waiting = "";
$count_approved = "";
$count_completed = "";
$count_expired = "";
$count_all = "";
$count_invoice ="";
$count_invoice_sent ="";
$count_paid ="";

$booking_tab = get_user_meta(get_current_user_ID(),"booking_tab_owner",true);

if($booking_tab == "approved"){

	$active = "approved";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'approved');




}elseif($booking_tab == "completed"){

	$active = "completed";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'completed');


	
}elseif($booking_tab == "expired"){

	$active = "expired";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'expired');

	
	
}elseif($booking_tab == "all"){

	$active = "all";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'all');

	
	
}elseif($booking_tab == "invoice"){

	$active = "invoice";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'invoice');

	
	
}elseif($booking_tab == "invoice_sent"){

	$active = "invoice_sent";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'invoice_sent');

	
	
}elseif($booking_tab == "paid"){

	$active = "paid";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'paid');

	
}else{

	$active = "waiting";

	//$booking_data = gibbs_bookings_by_status($user_id, $booking_user_group_selected_id, 'waiting');

}
$booking_data = array();

$gibbs_owner_listings = gibbs_owner_listings($user_id,$booking_user_group_selected_id); 

//echo "<pre>"; print_r($booking_data); die;


$gibbs_owner_customer = gibbs_owner_customer($user_id,$booking_user_group_selected_id);


?>
<section class="booking_main" style="display: none">
	<div class="container booking_main_start">
		<?php require(__DIR__."/booking_tabs.php");?>
	</div>
	<?php // require(__DIR__."/modal/message_modal.php");?>
	<?php require(__DIR__."/modal/newoffer_modal.php");?>
	
</section>
<script src="<?php echo plugin_dir_url(__FILE__);?>js/rrule-tz.min.js"></script>
<!-- JS & CSS library of MultiSelect plugin -->
<script src="<?php echo plugin_dir_url(__FILE__);?>js/multiselect.js"></script>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/multiselect.css">


