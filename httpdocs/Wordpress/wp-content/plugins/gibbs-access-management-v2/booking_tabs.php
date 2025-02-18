<?php 


$columns  = Access_management_v2::get_columns();

$active_columns  = Access_management_v2::get_active_columns();

$get_user_listings = Access_management_v2::getUserListings();

$gibbs_listings = array();

foreach ($get_user_listings as $key => $list_idd) {
	$gibbs_listings[] = get_post($list_idd);
}

$active_tab = "access_log";
$message = get_transient('flash_message');
if(isset($_GET["addEditAccessCode"]) && $_GET["addEditAccessCode"] == "true"){
	$active_tab = "access_log_settings";
}
if($message || isset($_GET["access_log"])){
	$active_tab = "access_log_settings";
}

?>
<div class="mobileDropdown"> Logg <span class="count_all"></span> <svg class="svg-inline--fa fa-chevron-down" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path></svg></div>

<ul class="tab-ul">

	<li class="tablinks <?php if($active_tab == "access_log"){ echo 'active';}?>" data-id="access_log">Logg</li>

	<li class="tablinks <?php if($active_tab == "access_log_settings"){ echo 'active';}?>" data-id="access_log_settings"> Integrasjoner</li>

</ul>

<?php
$booking_data = array();
?>
<div class="tab-main-content">
<div  class="tabcontent <?php if($active_tab == "access_log"){ echo 'active';}?> " id="access_log" <?php if($active_tab == "access_log"){ ?> style="display: block;" <?php }else{ ?>style="display: none;"<?php } ?>>
	
	<div class="row-data-filter">

	<div class="filterPop">Filter <svg class="svg-inline--fa fa-chevron-down" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path></svg></div>

	<div class="left_filter">

	<div class="filterHeader">
		Filter <span class="closeButt">X</span>
	</div>

	<div class="search-box-inner notes-drop search_div">
		<div class="dropdown">
		<button class="dropbtn"><i class="fa fa-search" aria-hidden="true"></i> <span class="filter_text">Søk</span> <span class="count_filter"></span></button>
		<div id="listingDropdown" class="dropdown-content">
			<div class="outer-drop-btn">
				
				<h3>Søk <i class="fas fa-times"></i></h3>
				<hr class="row-marg">
				<input class="search_in" type="text" placeholder="Søk på ordre, booking id.." name="">
			</div>
		</div>
		</div>
	</div>

		
	<div class="search-box-inner notes-drop listing_div">
		<div class="dropdown">
		<button class="dropbtn"><span class="filter_text">Annonse</span> <span class="count_filter"></span><i class="fa fa-chevron-down"></i></button>
		<div id="listingDropdown" class="dropdown-content">
			<div class="outer-drop-btn">
				
				<h3>Filtrer på annonse <i class="fas fa-times"></i></h3>
				<hr class="row-marg">

				<select name="listing" id="listing" multiple="">
					<?php foreach ($gibbs_listings as $key => $listings) { ?>
							<option value="<?php echo $listings->ID;?>"><?php echo $listings->post_title;?></option>
					<?php } ?>
				</select>
				
				<div class="btns-outers">
					<a href="javascript:void(0)" class="close-btn btn-modal close_filter_btn">Lukk</a>
					<a href="javascript:void(0)" class="save-btn btn-modal">Bruk</a>
				</div>
			</div>
		</div>
		</div>
	</div>

	<div class="listing-outer">
		<div class="search-box-inner daterange_pick">
			<span class="user_icon"></span> 
			<div class="dropdown">
				<button id="booking-date-range-enabler2" class="dropbtn">
					<span class="filter_text"><?php esc_html_e('Date','listeo_core'); ?></span> <i class="fa fa-chevron-down"></i>
				</button>

				<button id="booking-date-range" style="display: none" class="dropbtn">
					<span class="filter_text"><?php esc_html_e('Date','listeo_core'); ?></span>
					<b class="date_close" style="display: none"><i class="fa fa-times close_filter"></i></b>
				</button>
			</div>
		</div>
	</div>

	<div class="mobileFilterClose btn">Use</div>
	</div>


	</div>	
	<div class="booking_table_main">
       <?php  require(__DIR__."/booking_table.php"); ?>
	</div>
</div>
<div  class="tabcontent access_log_settings <?php if($active_tab == "access_log_settings"){ echo 'active';}?>" id="access_log_settings" <?php if($active_tab == "access_log_settings"){ ?> style="display: block;" <?php }else{ ?>style="display: none;"<?php } ?>>
   <?php if(isset($_GET["addEditAccessCode"]) && $_GET["addEditAccessCode"] == "true"){
       require(__DIR__."/addEditAccess.php");
   }else{
	   require(__DIR__."/accessLogTable.php");  
   }
   ?>
</div>
</div>
<script type="text/javascript">
jQuery(".columns_div_main").on("click",function(){
if(jQuery(this).find(".columns_div_inner").hasClass("show")){
	jQuery(this).find(".columns_div_inner").removeClass("show");
}else{
	jQuery(this).find(".columns_div_inner").addClass("show");
}

})
jQuery(document).on('click', function (e) {
if (jQuery(e.target).closest(".columns_div_main").length === 0) {
	jQuery(".columns_div_inner").removeClass("show");
}
});
jQuery(".close_filter").click(function(){
jQuery(".dropdown-content").removeClass("show");
})

jQuery(".access_main").find(".tab-ul .tablinks").click(function(){
	var urlParams = new URLSearchParams(window.location.search);
	if (urlParams.has('addEditAccessCode')) {
        // Redirect to the same URL with ?success=true
        window.location.href = window.location.origin + window.location.pathname + '?access_log=true';
		return;
    }
	jQuery(".access_main").find(".tab-ul .tablinks").removeClass("active");
    jQuery(this).addClass("active");
	var data_id = jQuery(this).attr("data-id");
	jQuery(".access_main").find(".tabcontent").hide();
	jQuery("#"+data_id).show();
	

    // Check if 'addEditAccessCode' exists
    
	jQuery('#provider-select').change();
	jQuery(".booking_main").find(".dropdown-content a").click();

})

</script>