<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listeo_Core_Admin_Add_Package class.
 */
class Listeo_Core_Admin_Edit_Listing_Package {

	private $package_id;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->listing_id = isset( $_REQUEST['listing_id'] ) ? absint( $_REQUEST['listing_id'] ) : 0;
		$this->package_id = isset( $_REQUEST['package_id'] ) ? absint( $_REQUEST['package_id'] ) : 0;

		if ( ! empty( $_POST['save_listing_package'] ) && ! empty( $_POST['listeo_core_paid_listings_package_editor_nonce'] ) && wp_verify_nonce( $_POST['listeo_core_paid_listings_package_editor_nonce'], 'save' ) ) {
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

		
		?>
		<table class="form-table">
			<h2><?php 
				$post_author_id = get_post_field( 'post_author', $this->listing_id );
		    	$user_package = get_post_meta($this->listing_id,'_user_package_id',true);
		    	//echo $user_package;
		    	//$user_packages = listeo_core_available_packages($post_author_id,$user_package);
		    	
		    		$package = listeo_core_get_package_by_id($this->package_id);	
		    		//var_dump($package);
		    		if($package && $package->product_id){
		    			echo "Your are editing \""; echo get_the_title($this->listing_id); echo "\".<br><br>"; 
		    			echo "Currently this listing is assigned to package: "; echo get_the_title($package->product_id);
		    		};
		    		//return $package->get_title();
		    	
		     ?>
			</h2>

			<tr>
				<th>
					<label for="_user_package_id"><?php _e( 'Assigned to Listing Package', 'listeo_core' ); ?></label><br>
					
				</th>
				<td>
					<select name="_user_package_id">
					<?php echo listeo_core_available_packages($post_author_id,$user_package); ?>	
					</select>
					<small>Changing package will increase limit of used listings in the package</small>
				
				</td>
			</tr>
			<tr>
				<th>
					<label for="_user_package_decrease">Decrease previous package count on package change</label>
				</th>
				<td>
					<input type="checkbox" name="_user_package_decrease">
				</td>
			</tr>
			
			
		</table>
		<p class="submit">
			<input type="hidden" name="package_id" value="<?php echo esc_attr( $this->package_id ); ?>" />
			<input type="hidden" name="listing_id" value="<?php echo esc_attr( $this->listing_id ); ?>" />
			<input type="submit" class="button button-primary" name="save_listing_package" value="<?php _e( 'Save Package', 'listeo_core' ); ?>" />
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
			
			$listing_id    = absint( $_POST['listing_id'] );
			$current_package_id    = get_post_meta($listing_id,'_user_package_id',true);
			$new_package_id    = absint( $_POST['_user_package_id'] );
			$decrease    = isset( $_POST['_user_package_decrease'] ) ? 1 : 0;

			if ( $current_package_id != $new_package_id) {
					$post_author_id = get_post_field( 'post_author', $listing_id );
					update_post_meta($listing_id,'_user_package_id',$new_package_id);
					listeo_core_increase_package_count($post_author_id, $new_package_id);
					if($decrease == 1) {
					
						listeo_core_decrease_package_count($post_author_id, $current_package_id);
					}
				echo sprintf( '<div class="updated"><p>%s</p></div>', __( 'Package successfully changed', 'listeo_core' ) );

			} else {
			echo sprintf( '<div class="updated"><p>%s</p></div>', __( 'You haven\'t changed package', 'listeo_core' ) );
			}// End if().

			

		} catch ( Exception $e ) {
			echo sprintf( '<div class="error"><p>%s</p></div>', $e->getMessage() );
		}// End try().
	}
}
