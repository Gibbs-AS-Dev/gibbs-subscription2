<?php /* Template Name: halfmap */ 
get_header('split');?>
<?php 
$template_loader = new Listeo_Core_Template_Loader; 
  ?>
<!-- Content
================================================== -->
<div class="fs-container">

	<div class="fs-inner-container content">
		<div class="fs-content">

			<!-- Search -->

			<section class="search">
<a href="#" id="show-map-button" class="show-map-button" data-enabled="<?php  esc_attr_e('Show Map ','listeo'); ?>" data-disabled="<?php  esc_attr_e('Hide Map ','listeo'); ?>"><?php esc_html_e('Show Map ','listeo') ?></a>
				<div class="row">
					<div class="col-md-12">

							<?php echo do_shortcode('[listeo_search_form source="half" more_custom_class="margin-bottom-30"]'); ?>

					</div>
				</div>

			</section>
			<!-- Search / End -->

			<?php $content_layout = get_option('pp_listings_layout','list'); ?>
			<section class="listings-container margin-top-45">
				<!-- Sorting / Layout Switcher -->
				<div class="row fs-switcher">

					<!-- <div class="col-md-6">
						Showing Results
						<p class="showing-results">14 Results Found </p>
					</div> -->

					<?php $top_buttons = get_option('listeo_listings_top_buttons');
						
					if($top_buttons=='enable'){
						$top_buttons_conf = get_option('listeo_listings_top_buttons_conf');
						if(is_array($top_buttons_conf) && !empty($top_buttons_conf)){
					
							if (($key = array_search('radius', $top_buttons_conf)) !== false) {
							    unset($top_buttons_conf[$key]);
							}
							if (($key = array_search('filters', $top_buttons_conf)) !== false) {
							    unset($top_buttons_conf[$key]);
							}
							$list_top_buttons = implode("|", $top_buttons_conf);
						}  else {
							$list_top_buttons = '';
						}
						?>
							
						<?php do_action( 'listeo_before_archive', $content_layout, $list_top_buttons ); ?>
						
						<?php 
					} ?>

				</div>


				<!-- Listings -->
				<div class="row fs-listings">

					
				</div>
			</section>

		</div>
	</div>
	<div class="fs-inner-container map-fixed">

		<!-- Map -->
		<div id="map-container" class="">
		    <div id="map" class="split-map" data-map-zoom="<?php echo get_option('listeo_map_zoom_global',9); ?>" data-map-scroll="true">
		        <!-- map goes here -->
		    </div>
		    
		</div>
		
	</div>
</div>

<div class="clearfix"></div>
