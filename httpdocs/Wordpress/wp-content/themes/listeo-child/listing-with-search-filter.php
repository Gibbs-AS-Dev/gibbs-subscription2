<?php
/**
 * Template Name: Listing with search filter
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Listeo
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets/css/intlTelInput.css?ver=5.7.2">

<?php wp_head(); ?>
<!--script src="https://kit.fontawesome.com/c8ca6754b9.js" crossorigin="anonymous"></script-->

</head>
<body>
<!-- Banner
================================================== -->
<section class="search widget_search" style="display:flex;">
	<div class="back-button-embed-results" style="display:none; padding: 0px 0px 45px 0;">
                <a onclick="window.history.back();" style="font-size: auto;line-height:40px;">
                    <i class="fa fa-arrow-left" style="font-family:'Font Awesome Pro';font-size: 20px;" aria-hidden="true"></i>
                </a>
            </div>
		<div id="mobileSearchFilterToggler" style="order:2;">
			<div id="topKategoriDisplayer" style="flex-direction:row;height:45px;border-bottom:1px solid rgba(0,0,0,.09);">
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
		<div id="noscrollUpShowMap" style="display:block;position:fixed;top:50px;z-index:500;width:100%;">
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
				<a onclick="clearAllFilters()"><i class="fa fa-trash-alt"></i> Nullstill</a>
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


<?php while ( have_posts() ) : the_post(); ?>

	<!-- 960 Container -->
	<div class="container page-container home-page-container widget_listing">
	    <article <?php post_class(); ?>>
	        <?php the_content(); ?>
	    </article>
	</div>

<?php endwhile; // end of the loop.
echo "<div style='display:none'>";
get_footer(); 
echo "</div>";
?>
<style type="text/css">
	.sort-by .chosen-container.chosen-with-drop .chosen-drop, .sort-by .chosen-container .chosen-drop {
	    left: -123px;
	}
	.widget_search{
		display: flex;
	    position: fixed;
	    z-index: 999;
	    /* padding: 10px 0px; */
	    width: 100%;
	    margin-left: -3px;
	}
	.widget_listing article {
	    margin-top: 40px !important;
	}
	section.search {
	    background-color: #ffffff !important;
	}
	.greenThenWhite {
      max-width: 136px;
    }
	
</style>
</body>
</html>