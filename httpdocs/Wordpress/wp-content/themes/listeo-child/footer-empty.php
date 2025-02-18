<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package listeo
 */

?>

<!-- Footer
================================================== -->
<?php

	if(is_archive('listing-split')):
			$sticky = get_option('listeo_sticky_footer') ;
			$style = get_option('listeo_footer_style') ;

			if(is_singular()){

				$sticky_singular = get_post_meta($post->ID, 'listeo_sticky_footer', TRUE);

				switch ($sticky_singular) {
					case 'on':
					case 'enable':
						$sticky = true;
						break;

					case 'disable':
						$sticky = false;
						break;

					case 'use_global':
						$sticky = get_option('listeo_sticky_footer');
						break;

					default:
						$sticky = get_option('listeo_sticky_footer');
						break;
				}

				$style_singular = get_post_meta($post->ID, 'listeo_footer_style', TRUE);
				switch ($style_singular) {
					case 'light':
						$style = 'light';
						break;

					case 'dark':
						$style = 'dark';
						break;

					case 'use_global':
						$style = get_option('listeo_footer_style');
						break;

					default:
						$sticky = get_option('listeo_footer_style');
						break;
				}
			}

			$sticky = apply_filters('listeo_sticky_footer_filter',$sticky);
			?>

			<?php if(!is_archive() && !is_page_template('template-dashboard.php') && !is_singular('listing')) { ?>
			<div id="footer" class="<?php echo esc_attr($style); echo esc_attr(($sticky == 'on' || $sticky == 1 || $sticky == true) ? " sticky-footer" : ''); ?> ">
				<!-- Main -->
				<div class="container">
					<div class="row">
						<?php
						$footer_layout = get_option( 'pp_footer_widgets','3,3,2,2,2' );

				        $footer_layout_array = explode(',', $footer_layout);
				        $x = 0;
				        foreach ($footer_layout_array as $value) {
				            $x++;
				             ?>
				             <div class="col-md-<?php echo esc_attr($value); ?> col-sm-6 col-xs-12">
				                <?php
								if( is_active_sidebar( 'footer'.$x ) ) {
									dynamic_sidebar( 'footer'.$x );
								}
				                ?>
				            </div>
				        <?php } ?>

					</div>
					<!-- Copyright -->
					<div class="row">
						<div class="col-md-12">
							<div class="copyrights"> <?php $copyrights = get_option( 'pp_copyrights' , '&copy; Theme by Purethemes.net. All Rights Reserved.' );

					            echo wp_kses($copyrights,array( 'a' => array('href' => array(),'title' => array()),'br' => array(),'em' => array(),'strong' => array(),));
					         ?></div>
						</div>
					</div>
				</div>
			</div>

			<?php } ?>


			<!-- Back To Top Button -->
			<div id="backtotop"><a href="#"></a></div>

			</div> <!-- weof wrapper -->
			<?php if(( is_page_template('template-home-search.php') || is_page_template('template-home-search-video.php') || is_page_template('template-home-search-splash.php')) && get_option('listeo_home_typed_status','enable') == 'enable') {
				$typed = get_option('listeo_home_typed_text');
				$typed_array = explode(',',$typed);
				?>
									<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
									<script>
									var typed = new Typed('.typed-words', {
									strings: <?php echo json_encode($typed_array); ?>,
									typeSpeed: 80,
									backSpeed: 80,
									backDelay: 4000,
									startDelay: 1000,
									loop: true,
									showCursor: true
									});
									</script>
								<?php } ?>
			<?php wp_footer(); ?>

			<?php
				if ( has_nav_menu( 'main-nav' ) ) :

					$navWrapper = 'main-nav main-nav-small';
                	include('mainNav.php');

					if(is_archive('listing-split') || is_singular('listing')){ ?>
						<script>
							var homeLinks = document.querySelectorAll(".main-nav .home-icon");
							for (i = 0; i < homeLinks.length; i++) {
								homeLinks[i].classList.add("current-page-ancestor");
							}
						</script>
					<?php } ?>

				<script>
				<?php if(is_user_logged_in()){
					$unreadMsg = listeo_get_unread_counter();

					$user_id = get_current_user_id();
					global $wpdb;
					$result  = $wpdb -> get_results( "SELECT expiring FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (`bookings_author` = '$user_id') AND (`type` = 'reservation') and status in('confirmed')", "ARRAY_A" );

					$bookingsNeedPayment = 0;

					foreach($result as $key => $val) {
						if($val["expiring"] !== null && !empty($val["expiring"])){
							if (new DateTime() < new DateTime($val["expiring"]))
								$bookingsNeedPayment++;
						}
					}
					//$countReceivedPending = listeo_count_bookings($user_id, 'waiting');
					$countReceivedPending = listeo_count_my_bookings_by_status($user_id, 'attention');
					$count_pending = listeo_count_bookings($user_id, 'waiting');
			        $count_pending1 = listeo_count_bookings($user_id,'attention');
			        $bookingsNeedPayment = $count_pending + $count_pending1;


					if($unreadMsg > 0){ ?>
						printNavCount(<?php echo $unreadMsg ?>, ".main-nav .inbox-icon");
					<?php }
					if(($bookingsNeedPayment + $countReceivedPending) > 0) { ?>
						printNavCount(<?php echo ($bookingsNeedPayment + $countReceivedPending) ?>, ".main-nav .overview-icon");
					<?php }
				} else if(is_page_template('template-dashboard.php')){ ?>
					jQuery(document).ready(function(){
						jQuery(document).ajaxSuccess(function(e) {
						   if(jQuery('#sign-in-dialog #tab1 .notification').hasClass('success') || jQuery('#sign-in-dialog #tab2 .notification').hasClass('success')){
						   		<?php echo "window.location = '" . get_permalink(get_page_by_title('my-profile')) . "'"; ?>
						   }
						});
					})
				<?php } ?>

				function printNavCount(nr, selector){
					var messagesLink = document.querySelectorAll(selector);
					for (i = 0; i < messagesLink.length; i++) {
						if(messagesLink[i].parentNode.parentNode.classList.contains('main-nav-small')){
							messagesLink[i].insertAdjacentHTML("afterbegin", "<span style=\"background-color:#008474;position:absolute;left:51%;min-width:20px;height:20px;line-height:16px;top:-8px;border-radius:50px;color:white;z-index:1500;border:solid white 2px;box-sizing:border-box;font-size:11px;padding:0 7px;\">"+nr+"</span>");
						} else {
							messagesLink[i].insertAdjacentHTML("afterbegin", "<span style=\"background-color:#008474;position:absolute;left:23px;min-width:20px;height:20px;line-height:16px;top:-8px;border-radius:50px;color:white;z-index:1500;border:solid white 2px;font-size:11px;box-sizing:border-box;padding:0 7px;\">"+nr+"</span>");
						}
					}
				}

				function toggleSearchBar() {
					var topNavSearchBar = document.querySelector(".right-side-searchbar");

					// Search bar from not visible to visible
					if (topNavSearchBar.classList.contains("expandedNavbar")) {
						topNavSearchBar.classList.remove("expandedNavbar");
					} else {
						topNavSearchBar.classList.add("expandedNavbar");
						topNavSearchBar.querySelector("input").focus();
					}
				}
				</script>

<!--script src="https://kit.fontawesome.com/c8ca6754b9.js" async crossorigin="anonymous"></script-->

	<?php endif; endif;  ?>

</body>
</html>
