<?php

use Dompdf\Css\Style;
use Svg\Tag\StyleTag;

$template_loader = new Listeo_Core_Template_Loader; 
  ?>
<!-- Content
================================================== -->
<div class="fs-container">

	<div class="fs-inner-container content listing_container_with_overlay" style="padding-top: 49.9844px;">
		<div id="overlay"></div>
		<div class="fs-content">

			<!-- Search -->

			<?php

				global $wp;
				$thisPage = home_url($wp->request);
				$isSpecificCategory = strpos($thisPage, 'listing-category');

			 ?>

			<section class="search" style="display:flex;">
			<div class="back-button-embed-results" style="display:none; padding: 0px 0px 45px 0;">
                        <a onclick="window.history.back();" style="font-size: auto;line-height:40px;">
                            <i class="fa fa-arrow-left" style="font-family:'Font Awesome Pro';font-size: 20px;" aria-hidden="true"></i>
                        </a>
                    </div>
				<div id="mobileSearchFilterToggler" style="order:2;">
					<div id="topKategoriDisplayer" style="visibility:hidden;flex-direction:row;height:45px;border-bottom:1px solid rgba(0,0,0,.09);">
						<a style="z-index:500;float:left;padding:5px 20px 0px 20px;pointer-events:auto;line-height:40px;" onclick="window.history.back();"><i class="fa fa-chevron-left"></i></a>
						<p  onclick="showChooseCategories()" style="height:100%;text-align:center;width:100%;margin-left:-50px;line-height:45px">
							<?php if($isSpecificCategory) { 

								$pieces = explode('/', $thisPage);
								$last_word = array_pop($pieces);
								$topCategoryOnLoad = get_term_by('slug', $last_word, 'listing_category');

								echo $topCategoryOnLoad->name;

							} else {
								echo "Alle";
							} ?>
						</p>
					</div>
					<div id="filterAndSortSection">
						<a style="line-height:45px;padding:0px 15px 0px 15px;color:#414A4C;" id="mainFiltersToggler"><i class="fa fa-filter"></i>Filter<span style="background-color:#008474;position:absolute;min-width:20px;height:20px;line-height:20px;border:solid white 2px;text-align: center;padding:2px;border-radius:50%;color:white;box-sizing:content-box;z-index: 9999;" id="totalFilters"></span></a>
						<span style="flex-grow:1;"></span>
						<div class="col-md-12" style="padding-left:0px;">
							<div class="fullwidth-filteres ajax-search">
								<div class="sort-by">
									<div class="sort-by-select" style="margin-top:-5px;">
										<select dir="rtl" form="listeo_core-search-form" name="listeo_core_order" class="chosen-select-no-single orderby">
											<option class="hidden" value="sorter">Sorter</option>
											<option value="highest-rated"><?php esc_html_e( 'Highest Rated' , 'listeo_core' ); ?></option>
											<option value="reviewed"><?php esc_html_e( 'Most Reviewed' , 'listeo_core' ); ?></option>
											<option value="date-desc"><?php esc_html_e( 'Newest Listings' , 'listeo_core' ); ?></option>
											<option value="date-asc"><?php esc_html_e( 'Oldest Listings' , 'listeo_core' ); ?></option>
											<option value="featured"><?php esc_html_e( 'Featured' , 'listeo_core' ); ?></option>
											<option value="views"><?php esc_html_e( 'Most Views' , 'listeo_core' ); ?></option>
											<option value="priceasc">Pris lav - høy</option>
											<option value="pricedesc">Pris høy - lav</option>
										</select>
									</div>
								</div>	
							</div>
						</div>
					</div>
				<div id="noscrollUpShowMap" style="display:block;position:fixed;top:20px;z-index:500;width:100%;">
					<a href="#" id="show-map-button" style="color:#707070;background-color:white;padding:5px;width:35%;margin:0 auto;" class="show-map-button" data-enabled="<?php  esc_attr_e('Show Map ','listeo'); ?>" data-disabled="<?php  esc_attr_e('Hide Map ','listeo'); ?>"><?php esc_html_e('Show Map ','listeo')?></a>
				</div>
				</div>
				<a style="background-color:white;padding:5px 10px 0px 10px !important;border-radius:20px;cursor:pointer;height:35px;display: none" onclick="window.history.back();"><i class="fa fa-chevron-left"></i> Gå tilbake</a>
				<div id="mainFilters" class="modal" role="dialog" style="flex:1;">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <div class="modal-content">
				      <div class="modal-header" style="display:flex;width:85%;margin: 5px auto 5px auto;">
				      	<a class="skjulMobilFilters" href="#"><i class="fa fa-chevron-down"></i>Skjul</a>
						<span style="flex-grow:1;"></span>
						<a onclick="clearAllFilters()"><i class="fa fa-trash-alt"></i>Nullstill</a>
				      </div>
				      <div class="modal-body">
				        <div class="col-md-12">
							
							<?php echo do_shortcode('[listeo_search_form source="half" more_custom_class="addedClass"]'); ?>
							<a class="skjulMobilFilters button hidden" id="closeModalBodyBtn" style="font-weight:500;margin:0 auto;width:90%;border-radius:5px;margin-top:7px;text-align:center;margin-top:15px;margin-bottom:15px;">Vis <span id="totalLoadedListings">
								<?php 
									global $wp_query; 
									echo $wp_query->found_posts;
								?></span> treff</a>
						</div>
				      </div>
				    </div>

				  </div>
				</div>
			</section>
			<!-- Search / End -->

			<?php $content_layout = get_option('pp_listings_layout','list'); ?>
			<section class="listings-container">
				<!-- Listings -->
				<div class="row fs-listings">

					<?php 
					
					switch ($content_layout) {
						case 'list':
						case 'grid':
							$container_class = $content_layout.'-layout'; 
							break;
						
						case 'compact':
							$container_class = $content_layout; 
							break;

						default:
							$container_class = 'list-layout'; 
							break;
					} 

					$data = '';
					if($content_layout == 'grid'){
						// if ( $sidebar_side == 'full-width'){
						// 	$data .= 'data-grid_columns="3"';
						// } else {
						//	$data .= 'data-grid_columns="2"';
						//}

					}
					$data .= ' data-region="'.get_query_var( 'region').'" ';
					$data .= ' data-category="'.get_query_var( 'listing_category').'" ';
					$data .= ' data-feature="'.get_query_var( 'listing_feature').'" ';
					$data .= ' data-service-category="'.get_query_var( 'service_category').'" ';
					$data .= ' data-rental-category="'.get_query_var( 'rental_category').'" ';
					$data .= ' data-event-category="'.get_query_var( 'event_category').'" ';
					$orderby_value = isset( $_GET['listeo_core_order'] ) ? (string) $_GET['listeo_core_order']  : get_option( 'listeo_sort_by','date' );
								?>
					<!-- Listings -->
					<span class="listeo_core-unbookmark-it hidden"></span>
					<div data-grid_columns="2" <?php echo $data; ?> data-orderby="<?php echo $orderby_value;  ?>" data-style="<?php echo esc_attr($content_layout) ?>" class="listings-container <?php echo esc_attr($container_class) ?>" id="listeo-listings-container">
						
						<div class="loader-ajax-container" style=""> <div class="loader-ajax"></div> </div>
						<?php
						if ( have_posts() ) : 


							/* Start the Loop */
							while ( have_posts() ) : the_post();

								switch ($content_layout) {
									case 'list':
										$template_loader->get_template_part( 'content-listing' ); 
									break;
									
									case 'grid':
										echo '<div class="col-lg-6 col-md-12"> ';
										$template_loader->get_template_part( 'content-listing-grid' ); 
										echo '</div>';
									break;
									
									case 'compact':
										echo '<div class="col-lg-6 col-md-12"> ';
										$template_loader->get_template_part( 'content-listing-compact' );  
										echo '</div>';
									break;

									default:
										//$template_loader->get_template_part( 'content-listing' );
									break;
								}

							endwhile;


						else :

							$template_loader->get_template_part( 'archive/no-found' ); 

						endif; ?>

						<div class="clearfix"></div>
						<div class="empty" style="padding-bottom: 10px !important;"></div>
					</div>
					<?php $ajax_browsing = get_option('listeo_ajax_browsing'); ?>
					<div class="pagination-container margin-top-45 margin-bottom-60 row  <?php if( isset($ajax_browsing) && $ajax_browsing == 'on' ) { echo esc_attr('ajax-search'); } ?>">
						<nav class="pagination col-md-12">
						<?php
							if($ajax_browsing == 'on') {
									global $wp_query;
     								$pages = $wp_query->max_num_pages;
     								//var_dump($wp_query->found_posts);
									echo listeo_core_ajax_pagination( $pages, 1 );
							} else 
							if(function_exists('wp_pagenavi')) { 
								wp_pagenavi(array(
									'next_text' => '<i class="fa fa-chevron-right"></i>',
									'prev_text' => '<i class="fa fa-chevron-left"></i>',
									'use_pagenavi_css' => false,
									));
							} else {
								the_posts_navigation();	
							}?>
						</nav>
					</div>
					<div class="copyrights margin-top-0"><?php $copyrights = get_option( 'pp_copyrights' , '© Theme by Purethemes.net. All Rights Reserved.' ); 
		
				        if (function_exists('icl_register_string')) {
				            icl_register_string('Copyrights in footer','copyfooter', $copyrights);
				            echo icl_t('Copyrights in footer','copyfooter', $copyrights);
				        } else {
				            echo wp_kses($copyrights,array( 'a' => array('href' => array(),'title' => array()),'br' => array(),'em' => array(),'strong' => array(),));
				        } ?>
				        	
				    </div>
				</div>
			</section>

			<script>
				var topCatOnLoad = "<?php if(!is_null($topCategoryOnLoad) & $topCategoryOnLoad != "") { echo $topCategoryOnLoad->slug; } ?>";
				scrollUpShowMap();
			</script>

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

<?php get_footer('empty');?>
<style>
	@media screen and (max-width: 767px) {
		body #noscrollUpShowMap, body #mobileSearchFilterToggler {
		  display: block!important;
		  background: transparent;
		}	
		body .sort-by {
			display:none;
			position: absolute;
			top: -34px;
			right: 17px;
		}
		body .sort-by-select select {
			line-height: 20px;
		}
	}
</style>