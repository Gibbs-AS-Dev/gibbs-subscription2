<?php if($page_type == "buyer"){ ?>

<div class="mobileDropdown">All <span class="count_all">(<?php echo $count_all;?>)</span> <svg class="svg-inline--fa fa-chevron-down" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path></svg></div>

<ul class="tab-ul">

<li class="tablinks <?php if($active == 'approved'){ echo 'active';}?>" data-id="approved" data-page_type="buyer" class="count_approved">Trenger din oppmerksomhet <span>(<?php echo $count_approved;?>)</span></li>

<li class="tablinks <?php if($active == 'waiting'){ echo 'active';}?>" data-id="waiting" data-page_type="buyer" class="count_waiting">Venter godkjenning <span>(<?php echo $count_waiting;?>)</span></li>


<li class="tablinks <?php if($active == 'paid'){ echo 'active';}?>" data-id="paid" data-page_type="buyer" class="count_paid">Godkjent/Betalt <span>(<?php echo $count_paid;?>)</span></li>

<li class="tablinks <?php if($active == 'expired'){ echo 'active';}?>" data-id="expired"  data-page_type="buyer" class="count_expired">Avslått/Utløpt <span>(<?php echo $count_expired;?>)</span></li>

<li class="tablinks <?php if($active == 'all'){ echo 'active';}?>" data-id="all" data-page_type="buyer" class="count_all">Alle <span>(<?php echo $count_all;?>)</span></li>

</ul>

<?php }else{ ?>

<div class="mobileDropdown">All <span class="count_all">(<?php echo $count_all;?>)</span> <svg class="svg-inline--fa fa-chevron-down" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path></svg></div>

<ul class="tab-ul">

<li class="tablinks <?php if($active == 'waiting'){ echo 'active';}?>" data-id="waiting" data-page_type="owner">Trenger din oppmerksomhet <span class="count_waiting"><?php if($count_waiting != ""){ ?>(<?php echo $count_waiting;?>)<?php } ?></span></li>

<li class="tablinks <?php if($active == 'approved'){ echo 'active';}?>" data-id="approved" data-page_type="owner">Godkjent <span class="count_approved"><?php if($count_approved != ""){ ?>(<?php echo $count_approved;?>)<?php } ?></span></li>

<li class="tablinks <?php if($active == 'invoice'){ echo 'active';}?>" data-id="invoice" data-page_type="owner">Usendt faktura <span class="count_invoice"><?php if($count_invoice != ""){ ?>(<?php echo $count_invoice;?>)<?php } ?></span></li>

<li class="tablinks <?php if($active == 'invoice_sent'){ echo 'active';}?>" data-id="invoice_sent" data-page_type="owner">Sendt faktura <span class="count_invoice_sent"><?php if($count_invoice_sent != ""){ ?>(<?php echo $count_invoice_sent;?>)<?php } ?></span></li>

<li class="tablinks <?php if($active == 'paid'){ echo 'active';}?>" data-id="paid" data-page_type="owner">Betalt <span class="count_paid"><?php if($count_paid != ""){ ?>(<?php echo $count_paid;?>)<?php } ?></span></li>

<!--  <li class="tablinks <?php if($active == 'completed'){ echo 'active';}?>" data-id="completed">Completed <?php echo $count_completed;?></li> -->
<li class="tablinks <?php if($active == 'expired'){ echo 'active';}?>" data-id="expired" data-page_type="owner">Avslått/Utløpt <span class="count_expired"><?php if($count_expired != ""){ ?>(<?php echo $count_expired;?>)<?php } ?></span></li>

<li class="tablinks <?php if($active == 'all'){ echo 'active';}?>" data-id="all" data-page_type="owner">Alle <span class="count_all"><?php if($count_all != ""){ ?>(<?php echo $count_all;?>)<?php } ?></span></li>
</ul>
<?php } ?>
<?php 

$columns  = get_columns();

$active_columns  = get_active_columns($page_type);

?>
<div class="row-data-filter">

<!-- <div class="filterPop">Filter <svg class="svg-inline--fa fa-chevron-down" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path></svg></div>

<div class="left_filter">

<div class="filterHeader">
	Filter <span class="closeButt">X</span>
</div> -->

<?php if($page_type != "buyer"){ ?>

	<!-- <div class="listing-outer bulk_action">
			<?php require_once("modules/bulk_action.php");?>
	</div> -->

<?php } ?>

<div class="search-box-inner notes-drop search_div">
	<div class="dropdown">
	  <button class="dropbtn"><i class="fa fa-search" aria-hidden="true"></i> <span class="filter_text">Søk</span> <span class="count_filter"></span></button>
	  <div id="listingDropdown" class="dropdown-content">
		<div class="outer-drop-btn">
			
			<h3>Søk <i class="fas fa-times close_filter_btn"></i></h3>
			<hr class="row-marg">
			<input class="search_in" type="text" placeholder="Søk på ordre, booking id.." name="">
		</div>
	  </div>
	</div>
</div>
<?php if($page_type != "buyer"){ ?>

	<div class="search-box-inner notes-drop customer_div">
		<div class="dropdown">
		  <button class="dropbtn"><span class="filter_text">Kunde</span> <span class="count_filter"></span><i class="fa fa-chevron-down"></i></button>
		  <div id="listingDropdown" class="dropdown-content">
			<div class="outer-drop-btn sdsd">
				
				<h3>Filtrer på kunde <i class="fas fa-times close_filter_btn"></i></h3>
				<hr class="row-marg">

				<select name="customer" id="customer" multiple="">
					<?php foreach ($gibbs_owner_customer as $key => $customer) { ?>
							<option value="<?php echo $customer->ID;?>"><?php echo $customer->display_name;?></option>
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
<?php } ?>
	
<div class="search-box-inner notes-drop listing_div">
	<div class="dropdown">
	  <button class="dropbtn"><span class="filter_text">Annonse</span> <span class="count_filter"></span><i class="fa fa-chevron-down"></i></button>
	  <div id="listingDropdown" class="dropdown-content">
		<div class="outer-drop-btn">
			
			<h3>Filtrer på annonse <i class="fas fa-times close_filter_btn"></i></h3>
			<hr class="row-marg">

			<select name="listing" id="listing" multiple="">
				<?php foreach ($gibbs_owner_listings as $key => $listings) { ?>
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
	<!-- </div> -->
    </div>



<?php if($page_type != "buyer"){ ?>

<!-- <div class="search-box-inner notes-drop customer_div">
	<div class="dropdown">
	  <button class="dropbtn"><span class="filter_text">User group</span> <i class="fa fa-chevron-down"></i></button>
	  <div id="listingDropdown" class="dropdown-content">
		<div class="outer-drop-btn">
			
			<h3>Filter by User group <i class="fas fa-times"></i></h3>
			<hr class="row-marg">

			<select name="usergroup" id="usergroup">
				<?php foreach ($user_group_data as $key => $groups) { ?>
						<option value="<?php echo $groups->id;?>" <?php if($groups->id == $booking_user_group_selected_id){ echo 'selected';}?>><?php echo $groups->name;?></option>
				<?php } ?>
			</select>
			
			<div class="btns-outers">
				<a href="javascript:void(0)" class="close-btn btn-modal close_filter">Close</a>
				<a href="javascript:void(0)" class="save-btn btn-modal">Filter</a>
			</div>
		</div>
	  </div>
	</div>
</div> -->
<?php } ?>
<!-- <div class="search-box-inner notes-drop order_num_filter">
	<div class="dropdown">
	  <button class="dropbtn"><span class="filter_text">Filter</span> <span class="count_filter"></span> <i class="fa fa-chevron-down"></i></button>
	  <div id="listingDropdown" class="dropdown-content">
		<div class="outer-drop-btn">
			
			<h3>Filter <i class="fas fa-times"></i></h3>
			<hr class="row-marg">

			<div class="dynamic checkboxes-left checkboxes-booking in-row">  
				   <input id="order_number_checkbox"  type="checkbox" name="order_number_checkbox" value="true"><label  for="order_number_checkbox">Bare vis bookinger med ordre</label>
			</div>
			
		</div>
	  </div>
	</div>
</div> -->

<!-- <div class="mobileFilterClose btn">Use</div> -->
</div>
<div class="listing-outer">
	<div class="search-box-inner daterange_pick2">
		<span class="user_icon"></span> 
		<div class="dropdown">
			<button id="booking-created-date-range-enabler2" class="dropbtn">
				<span class="filter_text"><?php esc_html_e('Opprettet','listeo_core'); ?></span> <i class="fa fa-chevron-down"></i>
			</button>

			<button id="booking-created-date-range" style="display: none" class="dropbtn">
				<span class="filter_text"><?php esc_html_e('Opprettet','listeo_core'); ?></span>
				<b class="date_close2" style="display: none"><i class="fa fa-times close_filter"></i></b>
			</button>
		</div>
	<!-- </div> -->
    </div>
</div>	
<div class="right_filter">
<div class="listing-outer">
	
	<div class="search-box-inner notes-drop">
		<div class="dropdown">
		  <button class="dropbtn"><span class="filter_text"></span> <i class="fa fa-cog" aria-hidden="true"></i></button>
		  <div id="listingDropdown" class="dropdown-content">
			<div class="outer-drop-btn">
				
				<h3>Innstillinger <i class="fas fa-times close_filter_btn"></i></h3>
				
				<div class="outer-actions1 columns">
					<div class="start1 columns_div_main">
						<p>Velg kolonner <i class="fa fa-columns"></i></p>
						<div class="inner_filter_div columns_div_inner">
							<!-- <h3>Vis standard kolonner</h3> -->
							<div class="outer-actions1">
								<?php foreach ($columns as $column_key => $column) { ?>
									<p>
										<div class="dynamic checkboxes-left checkboxes-booking column_checkbox in-row">  
											   <input id="column_<?php echo $column_key;?>" value="<?php echo $column_key;?>"  type="checkbox" name="column_checkbox"  <?php if(in_array($column_key,$active_columns)){ echo "checked";}?>><label  for="column_<?php echo $column_key;?>"><?php echo  $column;?></label>
										</div>
									</p>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="start2 columns_div_main">
						<p><span class="booking_show_main">Vis 10 bookinger</span> <i class="fa fa-list"></i></p>
						<div class="inner_filter_div columns_div_inner">
							<div class="outer-actions1 show_booking_column">
								<p data-value="10">Vis 10 bookinger</p>
								<p data-value="20">Vis 20 bookinger</p>
								<p data-value="50">Vis 50 bookinger</p>
								<p data-value="1000000">Vis alle</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		  </div>
		</div>
	</div>
</div>

<div class="listing-outer">
	
	<div class="search-box-inner notes-drop download-csv">
		<div class="dropdown ">
		  <button class="dropbtn"><span class="filter_text"></span> <i class="fa fa-download" aria-hidden="true"></i></button>
		  <div id="listingDropdown" class="dropdown-content">
			<div class="outer-drop-btn">
				
				<h3>Last ned <i class="fas fa-times close_filter_btn"></i></h3>
				<div class="outer-actions1">
					<p class="export_booking_pdf" style="display: none">PDF <i class="fa fa-file-pdf"></i></p>
					<p class="export_booking_csv">CSV <i class="fa fa-file-csv"></i></p>
				</div>
			</div>
		  </div>
		</div>
	</div>
</div>
<?php if($page_type != "buyer"){ ?>

	<!-- <div class="listing-outer">
		
		<div class="search-box-inner notes-drop">
			<div class="dropdown">
			  <button class="dropbtn"><span class="filter_text"></span> <i class="fa fa-solid fa-file-export" aria-hidden="true"></i></button>
			  <div id="listingDropdown" class="dropdown-content">
				<div class="outer-drop-btn">
					
					<h3>Eksport <i class="fas fa-times"></i></h3>
					<div class="outer-actions1">
						<p class="export_booking_csv" style="margin-bottom: 10px">Eksporter til Visma </p>
						<p class="export_booking_csv" style="margin-bottom: 5px">Eksporter til Websak </p>
					</div>
				</div>
			  </div>
			</div>
		</div>
	</div> -->
<?php } ?>

</div>	
</div>
<?php
$booking_data = array();
?>
<div class="tab-main-content">
<div  class="tabcontent active booking_table_main" style="display: block;">
<?php /* No need to include this here, as it will be replaced when the Ajax request comes back: require(__DIR__."/booking_table.php"); */ ?>

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

</script>