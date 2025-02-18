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

$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

if($active_group_id != ""){
	$group_id = $active_group_id;
}else{
	$group_id = "0";
}

$booking_user_group_selected_id = $group_id;

//$booking_user_group_selected_id = $user_group_data[0]->id;


//echo "<pre>"; print_r($booking_user_group_selected_id); die;



$count_waiting = count(gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'waiting',"buyer"));
$count_approved = count(gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'approved',"buyer"));
$count_expired = count(gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'expired',"buyer"));
$count_all = count(gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'all',"buyer"));
$count_paid = count(gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'paid',"buyer"));

$booking_tab = get_user_meta(get_current_user_ID(),"booking_tab_buyer",true);

if($booking_tab == "approved"){

	$active = "approved";

	$booking_data = gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'approved',"buyer");




}elseif($booking_tab == "expired"){

	$active = "expired";

	$booking_data = gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'expired',"buyer");

	
	
}elseif($booking_tab == "all"){

	$active = "all";

	$booking_data = gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'all',"buyer");

	
	
}elseif($booking_tab == "paid"){

	$active = "paid";

	$booking_data = gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'paid',"buyer");

	
}else{

	$active = "waiting";

	$booking_data = gibbs_buyer_bookings_by_status($user_id, $booking_user_group_selected_id, 'waiting',"buyer");

}

$gibbs_owner_listings = gibbs_owner_listings($user_id,$booking_user_group_selected_id); 

//echo "<pre>"; print_r($booking_data); die;


$gibbs_owner_customer = gibbs_owner_customer($user_id,$booking_user_group_selected_id);


?>
<style type="text/css">
	.daterangepicker {
    left: 20% !important;
    right: auto !important;
}
</style>
<section class="booking_main" style="display: none">
	<div class="container booking_main_start">
		<?php require(__DIR__."/booking_tabs.php");?>
	</div>
</section>
<!-- JS & CSS library of MultiSelect plugin -->
<script src="<?php echo plugin_dir_url(__FILE__);?>js/multiselect.js"></script>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/multiselect.css">