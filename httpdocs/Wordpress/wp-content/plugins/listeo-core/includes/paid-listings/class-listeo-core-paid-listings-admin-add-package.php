<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listeo_Core_Admin_Add_Package class.
 */
class Listeo_Core_Admin_Add_Package {

	private $package_id;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->package_id = isset( $_REQUEST['package_id'] ) ? absint( $_REQUEST['package_id'] ) : 0;

		if ( ! empty( $_POST['save_package'] ) && ! empty( $_POST['listeo_core_paid_listings_packages_nonce'] ) && wp_verify_nonce( $_POST['listeo_core_paid_listings_packages_nonce'], 'save' ) ) {
			$this->save();
		}
	}

	/**
	 * Output the form
	 */
	public function form() {
		global $wpdb;

		$user_string = '';
		$user_id     = '';

		if ( $this->package_id && ( $package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}listeo_core_user_packages WHERE id = %d;", $this->package_id ) ) ) ) {
			
			$package_limit    = $package->package_limit;
			$package_count    = $package->package_count;
			$package_duration = $package->package_duration;
			$package_featured = $package->package_featured;
			$package_option_booking = $package->package_option_booking;
			$package_option_reviews = $package->package_option_reviews;
			$package_option_social_links = $package->package_option_social_links;
			$package_option_opening_hours = $package->package_option_opening_hours;
			$package_option_video = $package->package_option_video;
			$package_option_coupons = $package->package_option_coupons;
			$package_option_gallery = $package->package_option_gallery;
			$package_option_gallery_limit = $package->package_option_gallery_limit;
			$user_id          = $package->user_id ? $package->user_id : '';
			$product_id       = $package->product_id;
			$order_id         = $package->order_id;

			if ( ! empty( $user_id ) ) {
				$user        = get_user_by( 'id', $user_id );
				$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
			}
		} else {
			
			$package_limit    = '';
			$package_count    = '';
			$package_duration = '';
			$package_featured = '';
			$product_id       = '';
			$order_id         = '';
			$package_option_booking  = '';
			$package_option_reviews  = '';
			$package_option_social_links = '';
			$package_option_opening_hours = '';
			$package_option_video = '';
			$package_option_coupons = '';
			$package_option_gallery = '';
			$package_option_gallery_limit = '';
		}
		?>
		<table class="form-table">
			


			<tr>
				<th>
					<label for="package_limit"><?php _e( 'Listing Limit', 'listeo_core' ); ?></label>
					<img class="help_tip tips" data-tip="<?php _e( 'How many listings should this package allow the user to post?', 'listeo_core' ); ?>" src="<?php echo WC()->plugin_url() ?>/assets/images/help.png" height="16" width="16">
				</th>
				<td>
					<input type="number" step="1" name="package_limit" id="package_limit" class="input-text regular-text" placeholder="<?php _e( 'Unlimited', 'listeo_core' ); ?>" value="<?php echo esc_attr( $package_limit ); ?>" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_count"><?php _e( 'Listing Count', 'listeo_core' ); ?></label>
					<img class="help_tip tips" data-tip="<?php _e( 'How many listings has the user already posted with this package?', 'listeo_core' ); ?>" src="<?php echo WC()->plugin_url() ?>/assets/images/help.png" height="16" width="16">
				</th>
				<td>
					<input type="number" step="1" name="package_count" id="package_count" value="<?php echo esc_attr( $package_count ); ?>" class="input-text regular-text" placeholder="0" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_duration"><?php _e( 'Listing Duration', 'listeo_core' ); ?></label>
					<img class="help_tip tips" data-tip="<?php _e( 'How many days should listings posted with this package be active?', 'listeo_core' ); ?>" src="<?php echo WC()->plugin_url() ?>/assets/images/help.png" height="16" width="16">
				</th>
				<td>
					<input type="number" step="1" name="package_duration" id="package_duration" value="<?php echo esc_attr( $package_duration ); ?>" class="input-text regular-text" placeholder="<?php _e( 'Default', 'listeo_core' ); ?>" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_featured"><?php _e( 'Feature Listings?', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_featured" id="package_featured" class="input-text" <?php checked( $package_featured, '1' ); ?> />
				</td>
			</tr>	
			<tr>
				<th>
					<label for="package_booking_module"><?php _e( 'Allow Booking Module', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_option_booking" id="package_option_booking" class="input-text" <?php checked( $package_option_booking, '1' ); ?> />
				</td>
			</tr>
		
		
			<tr>
				<th>
					<label for="package_option_reviews"><?php _e( 'Allow Reviews Module', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_option_reviews" id="package_option_reviews" class="input-text" <?php checked( $package_option_reviews, '1' ); ?> />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_option_social_links"><?php _e( 'Allow Social Links Module', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_option_social_links" id="package_option_social_links" class="input-text" <?php checked( $package_option_social_links, '1' ); ?> />
				</td>
			</tr>	
			<tr>
				<th>
					<label for="package_option_opening_hours"><?php _e( 'Allow Opening Hours Module', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_option_opening_hours" id="package_option_opening_hours" class="input-text" <?php checked( $package_option_opening_hours, '1' ); ?> />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_option_video"><?php _e( 'Allow Video', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_option_video" id="package_option_video" class="input-text" <?php checked( $package_option_video, '1' ); ?> />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_option_coupons"><?php _e( 'Allow Coupons', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_option_coupons" id="package_option_coupons" class="input-text" <?php checked( $package_option_coupons, '1' ); ?> />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_option_gallery"><?php _e( 'Allow Gallery', 'listeo_core' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="package_option_gallery" id="package_option_gallery" class="input-text" <?php checked( $package_option_gallery, '1' ); ?> />
				</td>
			</tr>
			<tr>
				<th>
					<label for="package_option_gallery_limit"><?php _e( 'Allow Gallery', 'listeo_core' ); ?></label>
				</th>
				<td>
					<?php 
					if(empty($package_option_gallery_limit)) { $package_option_gallery_limit = get_option('listeo_max_files',10); } ?>
					<input type="text" name="package_option_gallery_limit" id="package_option_gallery_limit" class="input-text" value="<?php echo $package_option_gallery_limit ;?>" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="user_id"><?php _e( 'User', 'listeo_core' ); ?></label>
				</th>
				<td>
					<?php
					wp_dropdown_users(
						array( 
							'name' => 'user_id',
							'role__in' => array('owner','administrator' ),
							'selected' => $user_id
						) 
					);
						
					
					?>
				</td>
			</tr>
			<tr>
				<th>
					<label for="product_id"><?php _e( 'Product', 'listeo_core' ); ?></label>
					<img class="help_tip tips" data-tip="<?php _e( 'Optionally link this package to a product.', 'listeo_core' ); ?>" src="<?php echo WC()->plugin_url() ?>/assets/images/help.png" height="16" width="16">
				</th>
				<td>
					<select name="product_id" class="wc-enhanced-select" data-allow_clear="true" data-placeholder="<?php _e( 'Choose a product&hellip;', 'listeo_core' ) ?>" style="width:25em">
						<?php
						echo '<option value=""></option>';
						$find_terms                  = array();
						$listing_package                 = get_term_by( 'slug', 'listing_package', 'product_type' );
						$listing_package_subscription    = get_term_by( 'slug', 'listing_package_subscription', 'product_type' );
						$find_terms[]                = $listing_package->term_id;
						$find_terms[]                = $listing_package_subscription->term_id;
						$posts_in                    = array_unique( (array) get_objects_in_term( $find_terms, 'product_type' ) );
						$args                        = array(
							'post_type'      => 'product',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'order'          => 'ASC',
							'orderby'        => 'title',
							'include'        => $posts_in,
						);

						$products = get_posts( $args );

						if ( $products ) {
							foreach ( $products as $product ) {
								echo '<option value="' . absint( $product->ID ) . '" ' . selected( $product_id, $product->ID ) . '>' . esc_html( $product->post_title ) . '</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="order_id"><?php _e( 'Order ID', 'listeo_core' ); ?></label>
					<img class="help_tip tips" data-tip="<?php _e( 'Optionally link this package to an order.', 'listeo_core' ); ?>" src="<?php echo WC()->plugin_url() ?>/assets/images/help.png" height="16" width="16">
				</th>
				<td>
					<input type="number" step="1" name="order_id" id="order_id" value="<?php echo esc_attr( $order_id ); ?>" class="input-text regular-text" placeholder="<?php _e( 'N/A', 'listeo_core' ); ?>" />
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="hidden" name="package_id" value="<?php echo esc_attr( $this->package_id ); ?>" />
			<input type="submit" class="button button-primary" name="save_package" value="<?php _e( 'Save Package', 'listeo_core' ); ?>" />
		</p>
		<?php
	}

	/**
	 * Save the new key
	 */
	public function save() {
		global $wpdb;

		try {
		//	$package_type     = wc_clean( $_POST['package_type'] );
			$package_limit    = absint( $_POST['package_limit'] );
			$package_count    = absint( $_POST['package_count'] );
			$package_duration = absint( $_POST['package_duration'] );
			$package_featured = isset( $_POST['package_featured'] ) ? 1 : 0;
			$user_id          = absint( $_POST['user_id'] );
			$product_id       = absint( $_POST['product_id'] );
			$order_id         = absint( $_POST['order_id'] );
			$package_option_booking 		= isset( $_POST['package_option_booking'] ) ? 1 : 0;
			$package_option_reviews 		= isset( $_POST['package_option_reviews'] ) ? 1 : 0;
			$package_option_social_links 	= isset( $_POST['package_option_social_links'] ) ? 1 : 0;
			$package_option_opening_hours 	= isset( $_POST['package_option_opening_hours'] ) ? 1 : 0;
			$package_option_video 			= isset( $_POST['package_option_video'] ) ? 1 : 0;
			$package_option_coupons 		= isset( $_POST['package_option_coupons'] ) ? 1 : 0;
			$package_option_gallery 		= isset( $_POST['package_option_gallery'] ) ? 1 : 0;
			$package_option_gallery_limit 	= absint( $_POST['package_option_gallery_limit'] );

			if ( $this->package_id ) {
				$wpdb->update(
					"{$wpdb->prefix}listeo_core_user_packages",
					array(
						'user_id'          => $user_id,
						'product_id'       => $product_id,
						'order_id'         => $order_id,
						'package_count'    => $package_count,
						'package_duration' => $package_duration ? $package_duration : '',
						'package_limit'    => $package_limit,
						'package_featured' => $package_featured,
						'package_option_booking' 		=> $package_option_booking,
						'package_option_reviews' 		=> $package_option_reviews,
						'package_option_social_links' 	=> $package_option_social_links,
						'package_option_opening_hours' 	=> $package_option_opening_hours,
						'package_option_video' 			=> $package_option_video,
						'package_option_coupons' 		=> $package_option_coupons,
						'package_option_gallery' 		=> $package_option_gallery,
						'package_option_gallery_limit' 	=> $package_option_gallery_limit,
					),
					array(
						'id' => $this->package_id,
					)
				);

				do_action( 'listeo_core_admin_updated_package', $this->package_id );
			} else {
				$wpdb->insert(
					"{$wpdb->prefix}listeo_core_user_packages",
					array(
						'user_id'          => $user_id,
						'product_id'       => $product_id,
						'order_id'         => $order_id,
						'package_count'    => $package_count,
						'package_duration' => $package_duration ? $package_duration : '',
						'package_limit'    => $package_limit,
						'package_featured' => $package_featured,
						'package_option_booking' 		=> $package_option_booking,
						'package_option_reviews' 		=> $package_option_reviews,
						'package_option_social_links' 	=> $package_option_social_links,
						'package_option_opening_hours' 	=> $package_option_opening_hours,
						'package_option_video' 			=> $package_option_video,
						'package_option_coupons' 		=> $package_option_coupons,
						'package_option_gallery' 		=> $package_option_gallery,
						'package_option_gallery_limit' 	=> $package_option_gallery_limit,
					)
				);

				$this->package_id = $wpdb->insert_id;

				do_action( 'listeo_core_admin_created_package', $this->package_id );
			}// End if().

			echo sprintf( '<div class="updated"><p>%s</p></div>', __( 'Package successfully saved', 'listeo_core' ) );

		} catch ( Exception $e ) {
			echo sprintf( '<div class="error"><p>%s</p></div>', $e->getMessage() );
		}// End try().
	}
}
