<?php 
$cr_user = get_current_user_id();

$group_admin = get_group_admin();

if($group_admin != ""){
	$cr_user = $group_admin;
	$current_user = get_userdata($cr_user); 

	
}else{
	$current_user = wp_get_current_user();	
}

$user_post_count = count_user_posts( $current_user->ID , 'listing' ); 
$roles = $current_user->roles;
$role = array_shift( $roles ); 

if(!in_array($role,array('administrator','admin','owner','editor','translator'))) :
	$template_loader = new Listeo_Core_Template_Loader; 
	$template_loader->get_template_part( 'account/owner_only'); 
	return;
endif; 

?>

<!-- Notice -->
<!--  -->

<!-- Content -->
<div class="row">
	
	<?php 
	$listings_page = get_option('listeo_listings_page');   

	$user_post_count = count_user_posts( $current_user->ID , 'listing' );

	if($listings_page) : ?>
	<a href="<?php echo esc_url(get_permalink($listings_page)); ?>?status=active">
	<?php endif; ?>
	<!-- Item -->
	<div class="col-lg-6 col-md-6" id="dashboard-active-listing-tile" <?php if($user_post_count == 0 || $user_post_count == ""){ echo 'style="display:none"';}?>>
		<div class="dashboard-stat color-1">
			<div class="dashboard-stat-content"><h4><?php  echo $user_post_count; ?></h4> <span><?php esc_html_e('Active Listings','listeo_core'); ?></span></div>
			<div class="dashboard-stat-icon">
				<svg id="Layer_1" style="enable-background:new 0 0 128 128;" version="1.1" viewBox="0 0 128 128" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M121.8,34.9L96.1,23.2c0,0-0.1,0-0.1,0c-0.1,0-0.1-0.1-0.2-0.1c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0s-0.2,0-0.2,0   c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0.1c0,0-0.1,0-0.1,0l-8.7,3.9c-3.5-5.2-9.5-8.6-16.3-8.6c0,0,0,0,0,0c-5.2,0-10.2,2-13.9,5.8   c-0.9,0.9-1.7,1.8-2.4,2.9l-8.7-3.9c0,0-0.1,0-0.1,0c-0.1,0-0.1-0.1-0.2-0.1c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0   c-0.1,0-0.2,0-0.2,0c-0.1,0-0.1,0-0.2,0c-0.1,0-0.2,0-0.2,0.1c0,0-0.1,0-0.1,0L17.5,34.9c-0.7,0.3-1.1,1-1.1,1.7v65.9   c0,0.6,0.3,1.2,0.9,1.6c0.3,0.2,0.7,0.3,1,0.3c0.3,0,0.5-0.1,0.8-0.2l24.9-11.3l24.9,11.3c0.1,0,0.1,0,0.2,0.1c0,0,0.1,0,0.1,0   c0.2,0,0.3,0.1,0.5,0.1c0.2,0,0.3,0,0.5-0.1c0,0,0.1,0,0.1,0c0.1,0,0.1,0,0.2-0.1l24.9-11.3l24.9,11.3c0.2,0.1,0.5,0.2,0.8,0.2   c0.4,0,0.7-0.1,1-0.3c0.5-0.3,0.9-0.9,0.9-1.6V36.6C122.9,35.9,122.4,35.2,121.8,34.9z M69.6,22.3C69.6,22.3,69.6,22.3,69.6,22.3   c8.8,0,15.9,7.1,15.9,15.9c0,8.3-12.5,25.6-15.9,27.6c-3.4-2-15.9-19.3-15.9-27.6c0-4.2,1.7-8.2,4.7-11.2   C61.4,23.9,65.4,22.3,69.6,22.3z M93.4,89.7l-21.9,9.9V69c6.1-3.8,17.8-22.2,17.8-30.8c0-2.7-0.6-5.4-1.6-7.7l5.8-2.6V89.7z    M67.7,69v30.6l-21.9-9.9V27.8l5.8,2.6c-1,2.4-1.6,5-1.6,7.7C50,46.7,61.7,65.2,67.7,69z M20.1,37.8l21.9-10v61.9l-21.9,9.9V37.8z    M119.1,99.6l-21.9-9.9V27.8l21.9,10V99.6z"/><path d="M57.5,36.8c0,6.7,5.4,12.1,12.1,12.1c6.7,0,12.1-5.4,12.1-12.1c0-6.7-5.4-12.1-12.1-12.1C62.9,24.7,57.5,30.1,57.5,36.8z    M78,36.8c0,4.6-3.7,8.4-8.4,8.4c-4.6,0-8.4-3.7-8.4-8.4c0-4.6,3.7-8.4,8.4-8.4C74.2,28.4,78,32.2,78,36.8z"/></g></svg>
			</div>
		</div>
		
	</div>
	<?php if($listings_page) : ?>
	</a>
	<?php endif; ?>
	<?php $total_views = get_user_meta( $current_user->ID, 'listeo_total_listing_views', true ); ?>
	<!-- Item -->
	<div class="col-lg-6 col-md-6"  id="dashboard-stat-listing-tile" <?php if($total_views == 0 || $total_views == ""){ echo 'style="display:none"';}?>>
		<div class="dashboard-stat color-2" >
			<div class="dashboard-stat-content"><h4><?php echo esc_html($total_views); ?></h4> <span><?php esc_html_e('Total Views','listeo_core'); ?></span></div>
			<div class="dashboard-stat-icon"><svg id="Layer_1" style="enable-background:new 0 0 256 256;" version="1.1" viewBox="0 0 256 256" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M29.4,190.9c2.8,0,5-2.2,5-5v-52.3l30.2-16.2v42.9c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5v-59.6l-50.1,26.9v58.3   C24.4,188.6,26.6,190.9,29.4,190.9z"/><path d="M89.6,153c2.8,0,5-2.2,5-5V59.6l30.2-16.2v143.8c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5V26.7L84.6,53.6V148   C84.6,150.7,86.8,153,89.6,153z"/><path d="M149.8,185.7c2.8,0,5-2.2,5-5V85.4L185,69.2v86.3c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5v-103l-50.1,26.9v101.3   C144.8,183.5,147.1,185.7,149.8,185.7z"/><path d="M250,146.2c-0.9-1.5-2.5-2.5-4.3-2.5h-34.5c-1.8,0-3.4,1-4.3,2.5c-0.9,1.5-0.9,3.4,0,5l6.2,10.7l-27.5,16.7   c-4.2,2.6-8.3,5.1-12.5,7.7c-3,1.9-6,3.7-8.9,5.6c-7.8,5-14.7,9.5-21.1,13.9c-3.3,2.2-6.5,4.5-9.8,6.7l-44.8-43.8L7.7,220.1   c-2.3,1.5-3,4.6-1.5,6.9c1,1.5,2.6,2.3,4.2,2.3c0.9,0,1.8-0.3,2.7-0.8l74-47.1l45.2,44.1l6.1-4.4c3.4-2.4,6.8-4.8,10.3-7.1   c6.3-4.3,13.1-8.7,20.9-13.7c2.9-1.9,5.9-3.7,8.8-5.6c4.1-2.6,8.3-5.1,12.4-7.7l25.3-15.5l2-1.2l6.1,10.6c0.9,1.5,2.5,2.5,4.3,2.5   s3.4-1,4.3-2.5l17.3-29.9C250.9,149.7,250.9,147.8,250,146.2z M228.4,168.6l-8.6-14.9H237L228.4,168.6z"/></g></svg></div>
		</div>
	</div>


	<?php 

	$author_posts_comments_count = listeo_count_user_comments(
	    array(
	        'author_id' => $current_user->ID , // Author ID
	        'author_email' => $current_user->user_email, // Author ID
	        'approved' => 1, // Approved or not Approved
	    )
	);
	 
	?>
	<?php $reviews_page = get_option('listeo_reviews_page');
	if($reviews_page):  ?>
	<!-- Item -->
	<a href="#<?php //echo esc_url(get_permalink($reviews_page)); ?>">
	<?php endif; ?>
	<div class="col-lg-3 col-md-6 d-none" id="dashboard-reviews-listing-tile">
		<div class="dashboard-stat color-3" >
			<div class="dashboard-stat-content"><h4><?php echo esc_html($author_posts_comments_count); ?></h4> <span><?php esc_html_e('Total Reviews','listeo_core'); ?></span></div>
			<div class="dashboard-stat-icon"><svg enable-background="new 0 0 48 48" version="1.1" viewBox="0 0 48 48"  xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Expanded"><g><g><path d="M6,45.414V36H3c-1.654,0-3-1.346-3-3V7c0-1.654,1.346-3,3-3h42c1.654,0,3,1.346,3,3v26c0,1.654-1.346,3-3,3H15.414     L6,45.414z M3,6C2.448,6,2,6.448,2,7v26c0,0.552,0.448,1,1,1h5v6.586L14.586,34H45c0.552,0,1-0.448,1-1V7c0-0.552-0.448-1-1-1H3z     "/></g><g><circle cx="16" cy="20" r="2"/></g><g><circle cx="32" cy="20" r="2"/></g><g><circle cx="24" cy="20" r="2"/></g></g></g></svg></div>
		</div>
	</div>
	<?php if($reviews_page):  ?>
	</a>
<?php endif; ?>


	<!-- Item -->
	<?php $total_bookmarks = get_user_meta( $current_user->ID, 'listeo_total_listing_bookmarks', true ); ?>
	<div class="col-lg-3 col-md-6 d-none"  id="dashboard-bookmarks-listing-tile">
		<div class="dashboard-stat color-4">
			<div class="dashboard-stat-content"><h4><?php echo esc_html($total_bookmarks); ?></h4> <span><?php esc_html_e('Times Bookmarked','listeo_core') ?></span></div>
			<div class="dashboard-stat-icon"><svg enable-background="new 0 0 32 32" height="32px" id="Layer_1" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="heart"><path clip-rule="evenodd" d="M29.193,5.265c-3.629-3.596-9.432-3.671-13.191-0.288   C12.242,1.594,6.441,1.669,2.81,5.265c-3.741,3.704-3.741,9.709,0,13.415c1.069,1.059,11.053,10.941,11.053,10.941   c1.183,1.172,3.096,1.172,4.278,0c0,0,10.932-10.822,11.053-10.941C32.936,14.974,32.936,8.969,29.193,5.265z M27.768,17.268   L16.715,28.209c-0.393,0.391-1.034,0.391-1.425,0L4.237,17.268c-2.95-2.92-2.95-7.671,0-10.591   c2.844-2.815,7.416-2.914,10.409-0.222l1.356,1.22l1.355-1.22c2.994-2.692,7.566-2.594,10.41,0.222   C30.717,9.596,30.717,14.347,27.768,17.268z" fill="#333333" fill-rule="evenodd"/><path clip-rule="evenodd" d="M9.253,7.501c-0.002,0-0.002,0.001-0.004,0.001   c-2.345,0.002-4.246,1.903-4.246,4.249l0,0c0,0.276,0.224,0.5,0.5,0.5s0.5-0.224,0.5-0.5V11.75c0-1.794,1.455-3.249,3.249-3.249   h0.001c0.276,0,0.5-0.224,0.5-0.5S9.53,7.501,9.253,7.501z" fill="#333333" fill-rule="evenodd"/></g></svg></div>
		</div>
	</div>

</div>

<?php 
global $wpdb;
$all_joined_groups_results = array();
$Class_Gibbs_Subscription = new Class_Gibbs_Subscription;
$user_id = $Class_Gibbs_Subscription->get_super_admin();
$all_joined_groups_results = array();

$current_user = wp_get_current_user();
$all_joined_groups_results = $wpdb->get_results( 
	$wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_groups WHERE id IN (SELECT users_groups_id FROM {$wpdb->prefix}users_and_users_groups WHERE users_id = %d AND role IN (3,4,5) )", $current_user->ID), ARRAY_A
);

$ative_steps = false;
$step2 =false; 
$step3 =false; 
$step4 =false; 
?>
<div class="row">

	<!-- Recent Activity -->
	<div class="col-lg-12 col-md-12">
		<div class="user-dashboard-card with-icons margin-top-20" style="position: relative;">
			<div class="d-flex justify-content-space-between inner-ur">
			    <h4>Kom igang som utleier</h4>
				<div class="slide-tab"><i class="fa fa-chevron-up up-arrow" ></i><i class="fa fa-chevron-down down-arrow" style="display: none;"></i></div>
			</div>
          
          <div class="content-user-dash active">
              <div class="box-main">
                <div class="box-inner">
				<div>
					<h4>1. Register deg på gibbs.no</h4> 
                    <p> Du har allerede gjort et stort skrit frem til automatiske bookinger</p>
					</div>
					<span><i class="fa-solid fa-circle-check"></i></span>
                </div>
				<div class="box-inner">
				<div>
					<h4>2. Opprett avdeling</h4> 
                    <p>Sett opp hvor bookingvarsler skal sendes, og håndter brukertilganger</p>
					</div>
					<?php if(empty($all_joined_groups_results)){ 
						$ative_steps = true;
						?>
					  <span class="listing_top_div"><button class="button btn btn-primary">Opprett avdeling</button></span>
					<?php }else{ 
						$step2 = true;
						?>
					  <span><i class="fa-solid fa-circle-check"></i></span>
					<?php } ?>
                </div>
				<?php
				$active_package = get_user_meta($user_id, 'license_status', true);
				?>
				<div class="box-inner">
				<div>
					<h4>3. Velg pakke</h4> 
                    <p>Hva ønsker du å bruke sytemet til? Kun automatiske bookinger,og eller automatisk adgang og varme/lys styring?</p>
					</div>
					<?php if($active_package != "active"){
						$ative_steps = true;
						 ?>
					  <span><button onclick="location.href='/packages'" class="button btn btn-primary" <?php if($step2 == false){ echo "disabled";}?>>Velg</button></span>
					<?php }else{ 
						$step3 = true;
						?>
					    <span><i class="fa-solid fa-circle-check"></i></span>
					<?php } ?>  
                </div>
				<?php
				$Class_Gibbs_Subscription = new Class_Gibbs_Subscription;
				$get_listing_count  = $Class_Gibbs_Subscription->get_listing_count($user_id);
				?>
				<div class="box-inner">
					<div>
					<h4>4. Publiser din første annone/utleieobjekt</h4> 
                    <p>Så snart den er publisert, er du klar for å ta imot bookinger</p>
					</div>
					
					<?php if($get_listing_count < 1){ 
						$ative_steps = true;
						?>
						<span><button onclick="location.href='/my-listings/add-listings/'" class="button btn btn-primary" <?php if($step3 == false){ echo "disabled";}?>>Opprett</button></span>
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
				<div>
				<h4><strong>5. Legg til utbetalings informasjon</strong></h4>

					<p>Legg til ditt konto nummer for å ta imot penger av dine bookinger</p>
				</div>
					
						<?php if($saldo == ""){ 
							$ative_steps = true;
						?>
							<span>
									<button onclick="location.href='/saldo/?popup-saldo=true'" class="button btn btn-primary" <?php if($step4 == false){ echo "disabled";}?>>Legg til</button>
								
							</span>
						<?php } else { 
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

<script>
	jQuery(document).ready(function($) {
		$('.slide-tab').parent().click(function() {
			$(this).parent().find('.content-user-dash').toggleClass('active'); // Toggle the class

			if ($(this).parent().find('.content-user-dash').hasClass('active')) {
				$(this).find('.up-arrow').show();
				$(this).find('.down-arrow').hide();
				
			} else {
				$(this).find('.up-arrow').hide();
				$(this).find('.down-arrow').show();
				
			}
		});
	});
</script>

<?php if($ative_steps == false){ ?>
	<script>
		jQuery(document).ready(function($) {
			$('.content-user-dash').parent().hide();
			setTimeout(function(){
				$("body").find('.up-arrow').hide();
				$("body").find('.down-arrow').show();
				$('.content-user-dash').removeClass("active")
			},500)
			
		});
	</script>
<?php } ?>


<div class="row">

	<!-- Recent Activity -->
	<div class="col-lg-12 col-md-12">
		<div class="dashboard-list-box with-icons margin-top-20" style="position: relative;">
		<div style="display: flex; justify-content: space-between; background-color: #fff;   border-bottom: 1px solid #eaeaea; align-items: center;">
    <h4 style= "border-bottom: 0px solid ;"><?php esc_html_e('Recent Activities', 'listeo_core'); ?></h4>
	<?php
	$jwt_approve = get_user_meta(get_current_user_id(),"jwt_approve", true);
	if($jwt_approve == "true"){

		$user_data = get_userdata(get_current_user_id());

		
		$jwt_token = get_user_meta(get_current_user_id(),"jwt_token", true);

		$current_url = home_url(remove_query_arg(array_keys($_GET), $_SERVER['REQUEST_URI']));

		$jwt_link = $current_url."?jwt_login=true&jwt_token=".$jwt_token."&success=true&show_message=true"; ?>
        <a class="auto_link" href="<?php echo $jwt_link;?>" style="color: #white; background: #fff; text-decoration: none; font-size: 16px; padding:25px 30px;"><?php if(isset($_GET["jwt_token"])){?>Snarvei klar 🚀<?php }else{ ?>Slå på snarvei <i class="fa-solid fa-arrow-up-right-from-square"></i><?php } ?> </a>
		<div class="gibbs-small-loader dashloader" style="display:none"><div class="loader"></div></div>
    <?php }else{ ?>
		<a href="<?php echo home_url();?>?auto_login=true" style="color: #white; background: #fff; text-decoration: none; font-size: 16px; padding:25px 30px;">Gibbs som snarvei?🚀 </a>
	<?php } ?>

	
</div>

			<!-- <a href="#" id="listeo-clear-activities" class="clear-all-activities" data-nonce="<?php echo wp_create_nonce( 'delete_activities' ); ?>"><?php esc_html_e('Clear All','listeo_core') ?></a> -->
			<?php echo do_shortcode( '[listeo_activities]' ); ?>
		
	</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
if(isset($_GET["show_message"]) && $_GET["show_message"] == "true"){
?>	
<script>
    Swal.fire({
        title: "Snarvei er klar til bruk!",
        html: "Du kan nå lagre Gibbs som et bokmerke eller som en app på din telefon. <br> <a href='https://support.gibbs.no/index.php/knowledge-base/automatisk-innlogging/' style='color: #008474; text-decoration: none;' target='_blank' rel='noopener noreferrer'>Se hvordan her!</a>",
        icon: "success"
    });
</script>


<?php } ?>
<script>
	function removeQueryParam(param) {
		const url = new URL(window.location.href);
		url.searchParams.delete(param); // Remove the query parameter
		window.history.replaceState(null, '', url); // Update the browser's address bar without reloading
	}
	removeQueryParam('show_message');
	jQuery(".auto_link").click(function(){
		jQuery(this).hide();
		jQuery(".dashloader").show();
	})
</script>