<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$template_loader = new Listeo_Core_Template_Loader;

get_header(get_option('header_bar_style','standard') );

$layout = get_option('listeo_single_layout','right-sidebar');
$mobile_layout = get_option('listeo_single_mobile_layout','right-sidebar');

$gallery_style = get_post_meta( $post->ID, '_gallery_style', true );

if(empty($gallery_style)) { $gallery_style = get_option('listeo_gallery_type','top'); }

$count_gallery = listeo_count_gallery_items($post->ID);

if($count_gallery < 4 ){
	$gallery_style = 'content';	
}
if(  $count_gallery == 1 ) {
	$gallery_style = 'none';	
}


$packages_disabled_modules = get_option('listeo_listing_packages_options',array());
if(empty($packages_disabled_modules)) {
	$packages_disabled_modules = array();
}

$user_package = get_post_meta($post->ID,'_user_package_id',true);

if($user_package){
	$package = listeo_core_get_user_package( $user_package );
	
} else {
	$package = false;
}


$load_gallery = false;
if( in_array('option_gallery',$packages_disabled_modules) ){ 
	if( $package && $package->has_listing_gallery() == 1 ){
		$load_gallery = true;
	}
} else {
	$load_gallery = true;
}

$load_video = false;
if( in_array('option_video',$packages_disabled_modules) ){ 
	if( $package && $package->has_listing_video() == 1 ){
		$load_video = true;
	}
} else {
	$load_video = true;
}

$load_reviews = false;
if( in_array('option_reviews',$packages_disabled_modules) ){ 
	if( $package && $package->has_listing_reviews() == 1 ){
		$load_reviews = true;
	}
} else {
	$load_reviews = true;
}

if ( have_posts() ) :

$listing_type = get_post_meta( get_the_ID(), '_listing_type', true);

if( $gallery_style == 'top' && $load_gallery == true) :
	
	$template_loader->get_template_part( 'single-partials/single-listing','gallery' );  
else: ?>
<!-- Gradient-->
<div class="single-listing-page-titlebar"></div>
<?php endif; ?>

<!-- Content
================================================== -->

<div class="container <?php echo esc_attr($listing_type); ?>">
	<div class="row sticky-wrapper">
		<!-- Sidebar
		================================================== -->
		<!-- " -->

		<?php if($layout=="left-sidebar" || ($layout== 'right-sidebar' && $mobile_layout=="left-sidebar")) : ?>
			<div class="col-lg-4 col-md-4 <?php if($layout== 'right-sidebar' && $mobile_layout=="left-sidebar") echo "col-lg-push-8"; ?> margin-top-75 sticky"> 

					<?php if($listing_type == 'classifieds'){ 
						$currency_abbr = get_option( 'listeo_currency' );
						$currency_postion = get_option( 'listeo_currency_postion' );
						$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

						?>
						<span id="classifieds_price"><?php if($currency_postion == "before") { echo $currency_symbol;} echo get_post_meta($post->ID, '_classifieds_price', true);  if($currency_postion == "after") { echo $currency_symbol;}?></span>
					<?php } ?>
					
					<?php if($listing_type != 'classifieds'){ ?>


					<?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?>
						<!-- Verified Badge -->
						<div class="verified-badge with-tip" data-tip-content="<?php esc_html_e('Listing has been verified and belongs to the business owner or manager.','listeo_core'); ?>">
							<i class="sl sl-icon-check"></i> <?php esc_html_e('Verified Listing','listeo_core') ?>
						</div>
					<?php else:

						
						if(get_option('listeo_claim_page_button')){
						$claim_page = get_option('listeo_claim_page');?>
						<div class="claim-badge with-tip" data-tip-content="<?php esc_html_e('Click to claim this listing.','listeo_core'); ?>">
							<?php 
							$link =  add_query_arg ('subject', get_permalink(), get_permalink($claim_page)) ; ?>

							<a href="<?php echo $link; ?>"><i class="sl sl-icon-question"></i> <?php esc_html_e('Not verified. Claim this listing!','listeo_core') ?></a>
						</div>
						<?php }

						endif; ?>
					<?php 	} ?>
					<?php get_sidebar('listing'); ?>
			</div>
			<!-- Sidebar / End -->
		<?php endif; ?>

		<?php while ( have_posts() ) : the_post();  ?>
			<!--  -->
		<div class="col-lg-8 col-md-8 <?php if($layout== 'right-sidebar' && $mobile_layout=="left-sidebar") { echo "col-lg-pull-4"; }  ?> padding-right-30">
			
			<!-- Titlebar -->
			<div id="titlebar" class="listing-titlebar">
				<div class="listing-titlebar-title">
				    <div class="listing-titlebar-tags">
					<?php
					$terms = get_the_terms( get_the_ID(), 'listing_category' );
					if ( $terms && ! is_wp_error( $terms ) ) : 
					    $categories = array();
					    foreach ( $terms as $term ) {
					        
					        $categories[] = sprintf( '<a href="%1$s">%2$s</a>',
                    			esc_url( get_term_link( $term->slug, 'listing_category' ) ),
                    			esc_html( $term->name )
                			);
					    }

					    $categories_list = join( ", ", $categories );
					    ?>
					    <span class="listing-tag">
					        <?php  echo ( $categories_list ) ?>
					    </span>
					<?php endif; ?>
					<?php
					switch ($listing_type) {
					 	case 'service':
					 		$type_terms = get_the_terms( get_the_ID(), 'service_category' );
					 		$taxonomy_name = 'service_category';
					 		break;
					 	case 'rental':
					 		$type_terms = get_the_terms( get_the_ID(), 'rental_category' );
					 		$taxonomy_name = 'rental_category';
					 		break;
					 	case 'event':
					 		$type_terms = get_the_terms( get_the_ID(), 'event_category' );
					 		$taxonomy_name = 'event_category';
					 		break;
					 	case 'classifieds':
					 		$type_terms = get_the_terms( get_the_ID(), 'classifieds_category' );
					 		$taxonomy_name = 'classifieds_category';
					 		break;
					 	
					 	default:
					 		# code...
					 		break;
					 } 
					 if( isset($type_terms) ) {
					 	if ( $type_terms && ! is_wp_error( $type_terms ) ) : 
					    $categories = array();
					    foreach ( $type_terms as $term ) {
					        $categories[] = sprintf( '<a href="%1$s">%2$s</a>',
                    			esc_url( get_term_link( $term->slug, $taxonomy_name ) ),
                    			esc_html( $term->name )
                			);
					    }

					    $categories_list = join( ", ", $categories );
					    ?>
					    <span class="listing-tag">
					        <?php  echo ( $categories_list ) ?>
					    </span>
						<?php endif;
					 }
					 ?>
					 <?php if(get_the_listing_price_range()): ?>
				                <span class="listing-pricing-tag"><i class="fa fa-<?php echo esc_attr(get_option('listeo_price_filter_icon','tag')); ?>"></i><?php echo get_the_listing_price_range(); ?></span>
				    <?php endif; ?>
				    </div>
					<h2><?php the_title(); ?></h2>
					<?php if(get_the_listing_address()): ?>
						<span>
							<a href="#listing-location" class="listing-address">
								<i class="fa fa-map-marker"></i>
								<?php the_listing_address(); ?>
							</a>
						</span> <br>
					<?php endif; ?>
					<?php
				if(!get_option('listeo_disable_reviews')){
					 $rating = get_post_meta($post->ID, 'listeo-avg-rating', true); 
						if(isset($rating) && $rating > 0 ) : 
							$rating_type = get_option('listeo_rating_type','star');
							if($rating_type == 'numerical') { ?>
								<div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
							<?php } else { ?>
								<div class="star-rating" data-rating="<?php echo $rating; ?>">
							<?php } ?>
							<?php $number = listeo_get_reviews_number($post->ID);  ?>
							<?php $rating_value = get_post_meta($post->ID, 'listeo-avg-rating', true);  ?>
							<div class="rating-counter"><a href="#listing-reviews"><strong><?php esc_attr(round($rating_value,1)); printf("%0.1f",$rating_value);  ?></strong> (<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</a></div>
						</div>
					<?php endif; 
				}?>

				</div>
				
			</div>
			<?php 
			if($listing_type=='classifieds'){
				$load_reviews = false;
			}
			?>
			
			<!-- Content
			================================================== -->
			<?php 
			if($gallery_style == 'none'  && $load_gallery == true ) :
				$gallery = get_post_meta( $post->ID, '_gallery', true );
				if(!empty($gallery)) : 

					foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
						$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
						echo '<img src="'.esc_url($image[0]).'" class="single-gallery margin-bottom-40" style="margin-top:-30px;"></a>';
					}
					
				 endif;
			 endif; ?>

			<!-- Listing Nav -->
			<div id="listing-nav" class="listing-nav-container">
				<ul class="listing-nav">
					<li><a href="#listing-overview" class="active"><?php esc_html_e('Overview','listeo_core'); ?></a></li>
					<?php if($count_gallery > 0 && $gallery_style == 'content'  && $load_gallery == true) : ?><li><a href="#listing-gallery"><?php esc_html_e('Gallery','listeo_core'); ?></a></li>
					<?php endif; 
					$_menu = get_post_meta( get_the_ID(), '_menu_status', 1 ); 

					if(!empty($_menu)) {
						$_bookable_show_menu =  get_post_meta(get_the_ID(), '_hide_pricing_if_bookable',true);
						if(!$_bookable_show_menu){ ?>
							<li><a href="#listing-pricing-list"><?php esc_html_e('Pricing','listeo_core'); ?></a></li>
						<?php } ?>
						
					<?php } 

					$video = get_post_meta( $post->ID, '_video', true ); 
					if($load_video && !empty($video)) :  ?>
						<li><a href="#listing-video"><?php esc_html_e('Video','listeo_core'); ?></a></li>
					<?php endif;
					$latitude = get_post_meta( $post->ID, '_geolocation_lat', true ); 
					if(!empty($latitude)) :  ?>
					<li><a href="#listing-location"><?php esc_html_e('Location','listeo_core'); ?></a></li>
					<?php 
					endif;
					if($listing_type != 'classifieds') {
						if($load_reviews && !get_option('listeo_disable_reviews')){
						$reviews = get_comments(array(
						    'post_id' => $post->ID,
						    'status' => 'approve' //Change this to the type of comments to be displayed
						)); 
						if ( $reviews ) : ?>
						<li><a href="#listing-reviews"><?php esc_html_e('Reviews','listeo_core'); ?></a></li>
						<?php endif; ?>
						<?php 
						$usercomment = false;
    					if(is_user_logged_in()) {
							$usercomment = get_comments( array (
					            'user_id' => get_current_user_id(),
					            'post_id' => $post->ID,
					    	) );
					    }
					    //TODO if open comments
					    if ( !$usercomment ) { ?>
						<li><a href="#add-review"><?php esc_html_e('Add Review','listeo_core'); ?></a></li>
						<?php } ?>
					<?php } 
					}?>
				
				</ul>
			</div>
<?php 


// 		$d = DateTime::createFromFormat('d-m-Y', $expires);
// 		echo $d->getTimestamp(); 
		?>
			<!-- Overview -->
			<div id="listing-overview" class="listing-section">
				<?php $template_loader->get_template_part( 'single-partials/single-listing','main-details' );  ?>
				
				<!-- Description -->
	
				<?php the_content(); ?>
				<?php 
				if( in_array('option_social_links',$packages_disabled_modules) ){ 
					if( $package && $package->has_listing_social_links() == 1 ){
						$template_loader->get_template_part( 'single-partials/single-listing','socials' );
					}
				}  else {
					$template_loader->get_template_part( 'single-partials/single-listing','socials' );
				}
				?>
				<?php $template_loader->get_template_part( 'single-partials/single-listing','features' );  ?>
			</div>

			<?php
			
			
			if( $count_gallery > 0 && $gallery_style == 'content'  && $load_gallery == true) : $template_loader->get_template_part( 'single-partials/single-listing','gallery-content' ); endif; ?>
			<?php $template_loader->get_template_part( 'single-partials/single-listing','pricing' );  ?>
			<?php $template_loader->get_template_part( 'single-partials/single-listing','opening' );  ?>
			<?php if($load_video) { $template_loader->get_template_part( 'single-partials/single-listing','video' ); } ?>
			<?php $template_loader->get_template_part( 'single-partials/single-listing','location' );  ?>
			<?php $template_loader->get_template_part( 'single-partials/single-listing','google-reviews' ); ?>
			<?php if($load_reviews && !get_option('listeo_disable_reviews')){ 
					
					$template_loader->get_template_part( 'single-partials/single-listing','reviews' );
			} ?>
			
		</div>
		<?php endwhile; // End of the loop. ?>
		
		<?php
		
		 if($layout=="right-sidebar" && $mobile_layout!="left-sidebar") : ?>
			<div class="col-lg-4 col-md-4  margin-top-75 sticky">
				<?php if($listing_type == 'classifieds'){ 
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

					?>
					<span id="classifieds_price"><?php if($currency_postion == "before") { echo $currency_symbol;} echo get_post_meta($post->ID, '_classifieds_price', true);  if($currency_postion == "after") { echo $currency_symbol;}?></span>
				<?php } ?>
				<?php if($listing_type != 'classifieds'){ ?>

					<?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?>
						<!-- Verified Badge -->
						<div class="verified-badge with-tip" data-tip-content="<?php esc_html_e('Listing has been verified and belongs to the business owner or manager.','listeo_core'); ?>">
							<i class="sl sl-icon-check"></i> <?php esc_html_e('Verified Listing','listeo_core') ?>
						</div>
					<?php else:
						if(get_option('listeo_claim_page_button')){
						$claim_page = get_option('listeo_claim_page');?>
						<div class="claim-badge with-tip" data-tip-content="<?php esc_html_e('Click to claim this listing.','listeo_core'); ?>">
							<?php 
							$link =  add_query_arg ('subject', get_permalink(), get_permalink($claim_page)) ; ?>

							<a href="<?php echo $link; ?>"><i class="sl sl-icon-question"></i> <?php esc_html_e('Not verified. Claim this listing!','listeo_core') ?></a>
						</div>
						<?php }

						endif; ?>
					<?php } ?>
					<?php get_sidebar('listing'); ?>
			</div>
			<!-- Sidebar / End -->
		<?php endif; ?>
	</div>
</div>



<?php else : ?>

<?php get_template_part( 'content', 'none' ); ?>

<?php endif; ?>


<?php get_footer(); ?>