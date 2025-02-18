<?php
/**
 * listing Submission Form
 */

if ( ! defined( 'ABSPATH' ) ) exit;
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift( $roles ); 
if(!in_array($role,array('administrator','admin','owner','editor','translator'))) :
	$template_loader = new Listeo_Core_Template_Loader; 
	$template_loader->get_template_part( 'account/owner_only'); 
	return;
endif;

$fields = array();
if(isset($data)) :
	$fields	 	= (isset($data->fields)) ? $data->fields : '' ;
endif;
if(isset($_GET["action"])) {
	$form_type = $_GET["action"];
} else {
	$form_type = 'submit';
}

$packages = $data->packages;
$user_packages = $data->user_packages;

global $woocommerce;
$woocommerce->cart->empty_cart();


?>
<form method="post" id="package_selection">
<?php if ( $packages || $user_packages ) :
	$checked = 1;
	?>
	
		<?php if ( $user_packages ) : ?>
             <h4 class="headline centered margin-bottom-20"><strong><?php _e( 'Choose your Package', 'listeo_core' ); ?></strong></h4> 
			<ul class="products user-packages">
				<?php 
				foreach ( $user_packages as $key => $package ) :
					$package = listeo_core_get_package( $package );
					?>
					<li class="user-job-package">
					<input type="radio" <?php checked( $checked, 1 ); ?> name="package" value="user-<?php echo $key; ?>" id="user-package-<?php echo $package->get_id(); ?>" />
					<label for="user-package-<?php echo $package->get_id(); ?>"><?php echo $package->get_title(); ?>					<p>
						<?php
						if ( $package->get_limit() ) {
							printf( _n( 'You have %1$s listings posted out of %2$d', 'You have %1$s listings posted out of %2$d', $package->get_count(), 'listeo_core' ), $package->get_count(), $package->get_limit() );
						} else {
							printf( _n( 'You have %s listings posted', 'You have %s listings posted', $package->get_count(), 'listeo_core' ), $package->get_count() );
						}

						if ( $package->get_duration() && $package->get_duration() > 1 ) {
							printf( ', ' . _n( 'listed for %s day', 'listed for %s days', $package->get_duration(), 'listeo_core' ), $package->get_duration() );
						}

						$checked = 0;
					?>
					</p></label>

				</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $packages ) : ?>


			<h4 class="headline centered margin-bottom-25"><strong>
				<?php 
				if ( $user_packages ) : 
					esc_html_e('Or Purchase New Package','listeo_core'); 
				else:  
					esc_html_e( 'Choose Package', 'listeo_core' ); ?>
				<?php endif; ?>
			</strong></h4>
			<div class="clearfix"></div>
			<div class="pricing-container margin-top-30">
				
			<?php
			$counter = 0;
			$single_buy_products = get_option('listeo_buy_only_once');
			foreach ( $packages as $key => $package ) :
				
				$product = wc_get_product( $package );
				if ( ! $product->is_type( array( 'listing_package','listing_package_subscription' ) ) || ! $product->is_purchasable() ) {
					continue;
				}
				if($single_buy_products) {
					$user = wp_get_current_user();
					if ( in_array( $product->get_id(), $single_buy_products )  && wc_customer_bought_product( $user->user_email, $user->ID, $product->get_id() ) ) {
					        continue;
					}
				}
				$user_id = get_current_user_id();
				if (  $product->is_type( array( 'listing_package_subscription' ) ) && wcs_is_product_limited_for_user( $product, $user_id ) ) {
					continue;
				}
				?>

				
				<div class="plan <?php echo ($product->is_featured()) ? 'featured' : '' ; ?>">
					<?php if( $product->is_featured() ) : ?>
						<div class="listing-badge">
							<span class="featured"><?php esc_html_e('Featured','listeo_core'); ?></span>
						</div>
					<?php endif; ?>

	                <div class="plan-price">

	                    <h3><?php echo $product->get_title();?></h3>
	                   	<span class="value"> <?php echo $product->get_price_html(); ?></span>
						<?php if($product->get_short_description() ) { ?><span class="period"><?php echo $product->get_short_description(); ?></span><?php } ?>
	                </div>

                <div class="plan-features">
                    <ul class="plan-features-auto-wc">
                        <?php 
                        $listingslimit = $product->get_limit();
                        if(!$listingslimit){
                            echo "<li>";
                             esc_html_e('Unlimited number of listings','listeo_core'); 
                             echo "</li>";
                        } else { ?>
                            <li>
                                <?php esc_html_e('This plan includes ','listeo_core'); printf( _n( '%d listing', '%s listings', $listingslimit, 'listeo_core' ) . ' ', $listingslimit ); ?>
                            </li>
                        <?php }
                        $duration = $product->get_duration();
                        if($duration > 0 ): ?>
                        <li>
                            <?php esc_html_e('Listings are visible ','listeo_core'); printf( _n( 'for %s day', 'for %s days', $product->get_duration(), 'listeo_core' ), $product->get_duration() ); ?>
                        </li>
                        <?php else : ?>
                        	<li>
	                            <?php esc_html_e('Unlimited availability of listings','listeo_core');  ?>
	                        </li>
                        <?php endif; ?>
                        <?php if(get_option('listeo_populate_listing_package_options')): ?>
	                        <?php  
	                        $bookingOptions = $product->has_listing_booking(); 
	                        if($bookingOptions) : ?>
	                        	<li>
		                            <?php esc_html_e('Booking Module enabled','listeo_core');  ?>
		                        </li>
	                        <?php endif; ?> 

	             
	                        <?php  
	                        $reviewsOptions = $product->has_listing_reviews(); 
	                        if($reviewsOptions) : ?>
	                        	<li>
		                            <?php esc_html_e('Reviews Module enabled','listeo_core');  ?>
		                        </li>
	                        <?php endif; ?>

	                        <?php  
	                        $sociallinksOptions = $product->has_listing_social_links(); 
	                        if($sociallinksOptions) : ?>
	                        	<li>
		                            <?php esc_html_e('Social Links Module enabled','listeo_core');  ?>
		                        </li>
	                        <?php endif; ?>

	                        <?php  
	                        $openinghoursOptions = $product->has_listing_opening_hours(); 
	                        if($openinghoursOptions) : ?>
	                        	<li>
		                            <?php esc_html_e('Opening Hours Module enabled','listeo_core');  ?>
		                        </li>
	                        <?php endif; ?>

	                        <?php  
	                        $vidosOptions = $product->has_listing_video(); 
	                        if($vidosOptions) : ?>
	                        	<li>
		                            <?php esc_html_e('Video option enabled','listeo_core');  ?>
		                        </li>
	                        <?php endif; ?> 

	                        <?php  
	                        $couponsOptions = $product->has_listing_coupons(); 
	                        if($couponsOptions == 'yes') : ?>
	                        	<li>
		                            <?php esc_html_e('Coupons option enabled','listeo_core');  ?>
		                        </li>
	                        <?php endif; ?>

	                        <?php  
	                        $galleryOptions = $product->has_listing_gallery(); 
	                        if($galleryOptions == 'yes') : ?>
	                        	<li>
		                            <?php esc_html_e('Gallery Module enabled','listeo_core');  ?>
		                        </li>
	                        <?php endif; ?>
	                        <?php  
	                        $gallery_limitOptions = $product->get_option_gallery_limit(); 
	                        if($gallery_limitOptions) : ?>
	                        	<li>
		                            <?php printf( esc_html__( 'Maximum  %s images in gallery', 'listeo_core' ), $product->get_option_gallery_limit() );  ?>
		                        </li>
	                        <?php endif; ?>
                        <?php endif; ?>

                    </ul>
                    <?php 
                       
                    	echo $product->get_description();
                   
                    ?>
                    <div class="clearfix"></div>
                    <input type="radio" <?php if( !$user_packages && $counter==0) : ?> checked="checked" <?php endif; ?> name="package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />
                  	<label for="package-<?php echo $product->get_id(); ?>"><?php ($product->get_price()) ? esc_html_e('Buy this package','listeo_core') : esc_html_e('Choose this package','listeo_core');  ?></label>
                    
                </div>
            </div>
         

			<?php $counter++;
			endforeach; ?>
			</div>
		<?php endif; ?>
	</ul>
<?php else : ?>

	<p><?php _e( 'No packages found', 'listeo_core' ); ?></p>

<?php endif; ?>

<div class="submit-page">

	<p>
		<input type="hidden" 	name="listeo_core_form" value="<?php echo $data->form; ?>" />
		<input type="hidden" 	name="listing_id" value="<?php echo esc_attr( $data->listing_id ); ?>" />
		<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />
		<button type="submit" name="continue"  class="button"><?php echo esc_attr( $data->submit_button_text ); ?> <i class="fa fa-circle-arrow-right"></i></button>

		
	</p>

</form>
</div>