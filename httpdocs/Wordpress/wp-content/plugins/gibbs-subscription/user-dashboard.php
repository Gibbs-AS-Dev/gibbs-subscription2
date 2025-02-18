<?php
if(!is_user_logged_in()){
	wp_redirect(home_url());
	exit;
}
global $wpdb;
$all_joined_groups_results = array();
$current_user = wp_get_current_user();
$all_joined_groups_results = $wpdb->get_results( 
	$wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_groups WHERE id IN (SELECT users_groups_id FROM {$wpdb->prefix}users_and_users_groups WHERE users_id = %d AND role IN (3,4,5) )", $current_user->ID), ARRAY_A
);
?>
<div class="user-dashboard-page-content container mt-5">
            
<!-- Notice -->
<!--  -->

<!-- Content -->
<div class="row">
		<!-- Item -->
	<div class="col-lg-6 col-md-6">
		<div class="dashboard-stat-user color-1">
			<div class="dashboard-stat-content-user"><h4>0</h4> <span>Aktive annonser</span></div>
			<div class="dashboard-stat-icon-user">
				<svg id="Layer_1" style="enable-background:new 0 0 128 128;" version="1.1" viewBox="0 0 128 128" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M121.8,34.9L96.1,23.2c0,0-0.1,0-0.1,0c-0.1,0-0.1-0.1-0.2-0.1c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0s-0.2,0-0.2,0   c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0.1c0,0-0.1,0-0.1,0l-8.7,3.9c-3.5-5.2-9.5-8.6-16.3-8.6c0,0,0,0,0,0c-5.2,0-10.2,2-13.9,5.8   c-0.9,0.9-1.7,1.8-2.4,2.9l-8.7-3.9c0,0-0.1,0-0.1,0c-0.1,0-0.1-0.1-0.2-0.1c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0   c-0.1,0-0.2,0-0.2,0c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0.1c0,0-0.1,0-0.1,0L17.5,34.9c-0.7,0.3-1.1,1-1.1,1.7v65.9   c0,0.6,0.3,1.2,0.9,1.6c0.3,0.2,0.7,0.3,1,0.3c0.3,0,0.5-0.1,0.8-0.2l24.9-11.3l24.9,11.3c0.1,0,0.1,0,0.2,0.1c0,0,0.1,0,0.1,0   c0.2,0,0.3,0.1,0.5,0.1c0.2,0,0.3,0,0.5-0.1c0,0,0.1,0,0.1,0c0.1,0,0.1,0,0.2-0.1l24.9-11.3l24.9,11.3c0.2,0.1,0.5,0.2,0.8,0.2   c0.4,0,0.7-0.1,1-0.3c0.5-0.3,0.9-0.9,0.9-1.6V36.6C122.9,35.9,122.4,35.2,121.8,34.9z M69.6,22.3C69.6,22.3,69.6,22.3,69.6,22.3   c8.8,0,15.9,7.1,15.9,15.9c0,8.3-12.5,25.6-15.9,27.6c-3.4-2-15.9-19.3-15.9-27.6c0-4.2,1.7-8.2,4.7-11.2   C61.4,23.9,65.4,22.3,69.6,22.3z M93.4,89.7l-21.9,9.9V69c6.1-3.8,17.8-22.2,17.8-30.8c0-2.7-0.6-5.4-1.6-7.7l5.8-2.6V89.7z    M67.7,69v30.6l-21.9-9.9V27.8l5.8,2.6c-1,2.4-1.6,5-1.6,7.7C50,46.7,61.7,65.2,67.7,69z M20.1,37.8l21.9-10v61.9l-21.9,9.9V37.8z    M119.1,99.6l-21.9-9.9V27.8l21.9,10V99.6z"></path><path d="M57.5,36.8c0,6.7,5.4,12.1,12.1,12.1c6.7,0,12.1-5.4,12.1-12.1c0-6.7-5.4-12.1-12.1-12.1C62.9,24.7,57.5,30.1,57.5,36.8z    M78,36.8c0,4.6-3.7,8.4-8.4,8.4c-4.6,0-8.4-3.7-8.4-8.4c0-4.6,3.7-8.4,8.4-8.4C74.2,28.4,78,32.2,78,36.8z"></path></g></svg>
			</div>
		</div>
		
	</div>
		</a>
			<!-- Item -->
	<div class="col-lg-6 col-md-6" >
		<div class="dashboard-stat-user color-2">
			<div class="dashboard-stat-content-user"><h4>0</h4> <span>Totalt antall visninger</span></div>
			<div class="dashboard-stat-icon-user"><svg id="Layer_1" style="enable-background:new 0 0 256 256;" version="1.1" viewBox="0 0 256 256" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M29.4,190.9c2.8,0,5-2.2,5-5v-52.3l30.2-16.2v42.9c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5v-59.6l-50.1,26.9v58.3   C24.4,188.6,26.6,190.9,29.4,190.9z"></path><path d="M89.6,153c2.8,0,5-2.2,5-5V59.6l30.2-16.2v143.8c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5V26.7L84.6,53.6V148   C84.6,150.7,86.8,153,89.6,153z"></path><path d="M149.8,185.7c2.8,0,5-2.2,5-5V85.4L185,69.2v86.3c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5v-103l-50.1,26.9v101.3   C144.8,183.5,147.1,185.7,149.8,185.7z"></path><path d="M250,146.2c-0.9-1.5-2.5-2.5-4.3-2.5h-34.5c-1.8,0-3.4,1-4.3,2.5c-0.9,1.5-0.9,3.4,0,5l6.2,10.7l-27.5,16.7   c-4.2,2.6-8.3,5.1-12.5,7.7c-3,1.9-6,3.7-8.9,5.6c-7.8,5-14.7,9.5-21.1,13.9c-3.3,2.2-6.5,4.5-9.8,6.7l-44.8-43.8L7.7,220.1   c-2.3,1.5-3,4.6-1.5,6.9c1,1.5,2.6,2.3,4.2,2.3c0.9,0,1.8-0.3,2.7-0.8l74-47.1l45.2,44.1l6.1-4.4c3.4-2.4,6.8-4.8,10.3-7.1   c6.3-4.3,13.1-8.7,20.9-13.7c2.9-1.9,5.9-3.7,8.8-5.6c4.1-2.6,8.3-5.1,12.4-7.7l25.3-15.5l2-1.2l6.1,10.6c0.9,1.5,2.5,2.5,4.3,2.5   s3.4-1,4.3-2.5l17.3-29.9C250.9,149.7,250.9,147.8,250,146.2z M228.4,168.6l-8.6-14.9H237L228.4,168.6z"></path></g></svg></div>
		</div>
	</div>



</div>

<?php $step2 =false; ?>
<div class="row">

	<!-- Recent Activity -->
	<div class="col-lg-12 col-md-12">
		<div class="user-dashboard-card with-icons margin-top-20" style="position: relative;">
          <h4>Get started as rental admin</h4>
          <div class="content-user-dash">
              <div class="box-main">
                <div class="box-inner">
                    <span>1. Create user</span>
					<span><i class="fa-solid fa-circle-check"></i></span>
                </div>
				<div class="box-inner">
                    <span>2. Create usergroup</span>
					<?php if(empty($all_joined_groups_results)){ 
						
						?>
					  <span class="listing_top_div"><button class="button btn btn-primary">Fill out</button></span>
					<?php }else{ 
						$step2 = true;
						?>
					  <span><i class="fa-solid fa-circle-check"></i></span>
					<?php } ?>
                </div>
				<?php
				$active_package = get_user_meta(get_current_user_id(), 'license_status', true);
				?>
				<div class="box-inner">
                    <span>3. Select package</span>
					<?php if($active_package != "active"){ ?>
					  <span><a href="/packages"><button class="btn btn-primary">Select</button></a></span>
					<?php }else{ 
						$step3 = true;
						?>
					    <span><i class="fa-solid fa-circle-check"></i></span>
					<?php } ?>  
                </div>
				<?php
				$Class_Gibbs_Subscription = new Class_Gibbs_Subscription;
				$get_listing_count  = $Class_Gibbs_Subscription->get_listing_count();
				?>
				<div class="box-inner">
                    <span>4. Create your first listing</span>
					<?php if($get_listing_count < 1){ ?>
						<span><a href="/my-listings/"><button class="btn btn-primary">Create listing</button></a></span>
					<?php }else{ 
						$step4 = true;
						?>
					    <span><i class="fa-solid fa-circle-check"></i></span>
					<?php } ?> 
                </div>
				<?php
				$cr_user = get_current_user_id();

				$group_admin = get_group_admin();
				
				if($group_admin != ""){
					$cr_user = $group_admin;
					$current_user = get_userdata($cr_user); 
				
					
				}else{
					$current_user = wp_get_current_user(); 
				}
				$saldo = get_user_meta($current_user->ID, 'listeo_core_bank_details', true);
				?>
				<div class="box-inner">
                    <span>5. Provide payout account number</span>
					<?php if($saldo == ""){ ?>
						<span><a href="/saldo/?popup-saldo=true"><button class="btn btn-primary">Add</button></a></span>
					<?php }else{ 
						$step5 = true;
						?>
					    <span><i class="fa-solid fa-circle-check"></i></span>
					<?php } ?> 
					
                </div>
              </div>
          </div>
		</div>
		
	</div>


                       
</div>
</div>