<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WP_listing_Manager_Content class.
 */
class Listeo_Core_Coupons {

		/**
	 * Dashboard message.
	 *
	 * @access private
	 * @var string
	 */
	private $dashboard_message = '';


	public function __construct() {

		add_shortcode( 'listeo_coupons', array( $this, 'listeo_coupons' ) );
		// /add_action( 'init', array( $this, 'process' ) );
		add_action( 'wp', array( $this, 'dashboard_coupons_action_handler' ) );

		
		

	}



	/**
	 * User bookmarks shortcode
	 */
	public function listeo_coupons( $atts ) {
		
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your coupons.', 'listeo_core' );
		}

		extract( shortcode_atts( array(
			'posts_per_page' => '25',
		), $atts ) );
		$page = 1;
		ob_start();
		$template_loader = new Listeo_Core_Template_Loader;

		if(isset($_GET['add_new_coupon'])) {
			$template_loader->set_template_data( 
				array( 
					'message' => $this->dashboard_message 
				) )->get_template_part( 'account/coupon-submit' ); 
		} else if(isset($_GET['action']) && $_GET['action'] == 'coupon_edit') {
				$template_loader->set_template_data( 
				array( 
					'coupon_data' => (isset($_GET['coupon_id'])) ? get_post($_GET['coupon_id']) : '' ,
					'coupon_edit' => 'on' ,
					'message' => $this->dashboard_message 
				) )->get_template_part( 'account/coupon-submit' ); 
		} else {
			$template_loader->set_template_data( array( 
				'ids' => $this->get_user_coupons($page,10),
				'message' => $this->dashboard_message
			) )->get_template_part( 'account/coupons' ); 
		}

		return ob_get_clean();
	}

	function get_user_id() {
	    global $current_user;
	    $group_admin = get_group_admin();

		if($group_admin != ""){

			$cr_user = $group_admin;
			$current_user = get_userdata($cr_user); 

			
		}else{
			$current_user = wp_get_current_user(); 
		}
	    //wp_get_current_user();
	    return $current_user->ID;
	}

	// function get_user_coupons(){
	// 	$user_id = $this->get_user_id();
	// }
	/**
	 * Function to get ids added by the user/agent
	 * @return array array of listing ids
	 */
	public function get_user_coupons($page,$per_page){
		$cr_user = get_current_user_id();

		$group_admin = get_group_admin();

		if($group_admin != ""){

			$cr_user = $group_admin;
			$current_user = get_userdata($cr_user); 

			
		}else{
			$current_user = wp_get_current_user(); 
		}
		//$current_user = wp_get_current_user();
		

		$args = array(
			'author'        	=>  $current_user->ID,
		    'posts_per_page'   => -1,
		    'orderby'          => 'title',
		    'order'            => 'asc',
		    'post_type'        => 'shop_coupon',
		    'post_status'      => 'publish',
		);
    
		$q = get_posts( $args );


		return $q;
	}
	public function get_all_coupons(){
		
		

		$args = array(
		    'posts_per_page'   => -1,
		    'orderby'          => 'title',
		    'order'            => 'asc',
		    'post_type'        => 'shop_coupon',
		    'post_status'      => 'publish',
		);
    
		$q = get_posts( $args );


		return $q;
	}

	public function coupon_exist_for_listing($post_listing_id){
		
		

		$get_all_coupons = self::get_all_coupons();

		$exist = false;

		$idss = array();

		foreach ($get_all_coupons as $key => $get_all_coupon) {
			$listing_ids = get_post_meta( $get_all_coupon->ID, "listing_ids", true);

			if(!empty($listing_ids)){
				$listing_ids = explode(",", $listing_ids);
				foreach ($listing_ids as $key => $listing_id) {
					$idss[] = $listing_id;
				}
			}

			
		}
	    $idss = array_unique($idss);

	   /* echo "<pre>"; print_r($listing_id); 
	    echo "<pre>"; print_r($idss); die;*/
	    if(in_array($post_listing_id, $idss)){
	    	$exist = true;
	    }


		return $exist;
	}



	public function get_products_ids_by_listing($listings){
		$products = array();
		if(is_array($listings)){
			foreach ($listings as $key => $listing_id) {
				$product_id = get_post_meta($listing_id, 'product_id', true);
				$products[] = $product_id;
			}
			$products = implode(',',$products);
		}
		return $products;
	}


	



	public function dashboard_coupons_action_handler() {

		global $post;

		$group_admin = get_group_admin();

		if($group_admin != ""){
			
			$cr_user = $group_admin;
			$current_user = get_userdata($cr_user); 

			
		}else{
			$current_user = wp_get_current_user(); 
		}



		if ( is_page(get_option( 'listeo_coupons_page' ) ) ) {
			if ( isset( $_POST['listeo-coupon-submission'] ) && '1' == $_POST['listeo-coupon-submission'] ) {

				
				global $wpdb;
				
				$title = sanitize_text_field($_POST['title']);

			    $sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1;", $title );
			    //check if coupon with that code exits
			    $coupon_id = $wpdb->get_var( $sql );

			    if ( empty( $coupon_id ) ) {
					
					$customer_emails = sanitize_text_field($_POST['customer_email']);

					if(isset($_POST['listing_ids']) && is_array($_POST['listing_ids'])){

						$products = $this->get_products_ids_by_listing($_POST['listing_ids']);
						$listings = implode(",",$_POST['listing_ids']);

					} else {

						//global $current_user;     



						$args = array(
						  'author'        =>  $current_user->ID, 
						  'orderby'       =>  'post_date',
						  'order'         =>  'ASC',
						  'fields'        => 'ids',
						  'post_type'      => 'listing',
						  'posts_per_page' => -1 // no limit
						);


						$current_user_posts = "";
						$listings = get_posts( $args );
						$products = $this->get_products_ids_by_listing($listings);
						$listings = implode(",",$listings);
					}

				
				    $data = array(
			            'discount_type'              => sanitize_text_field($_POST['discount_type']),
			            'coupon_amount'              => sanitize_text_field($_POST['coupon_amount']), // value
			            'individual_use'             => (isset($_POST['individual_use'])) ? sanitize_text_field($_POST['individual_use']) : 'no',//'no',
			            'product_ids'                => $products,
			            'listing_ids'                => $listings,
			            //'exclude_product_ids'        => '',
			            'usage_limit'                => sanitize_text_field($_POST['usage_limit']),
			            'usage_limit_per_user'       => sanitize_text_field($_POST['usage_limit_per_user']),//'1',
			            'limit_usage_to_x_items'     => '',
			            'usage_count'                => '',
			            'date_start'                => sanitize_text_field($_POST['date_start']),
			            'expiry_date'                => sanitize_text_field($_POST['expiry_date']),
			            'free_shipping'              => 'no',
			            'product_categories'         => '',
			            'exclude_product_categories' => '',
			            'exclude_sale_items'         => 'no',
			            'minimum_amount'             => sanitize_text_field($_POST['minimum_amount']),
			            'maximum_amount'             => sanitize_text_field($_POST['maximum_amount']),
			            'customer_email'             => $customer_emails,
			            'coupon_bg-uploader-id'		 => sanitize_text_field($_POST['listeo_coupon_bg_id']),
			        );
				  
			        // Save the coupon in the database
			        $coupon = array(
			            'post_title' => $_POST['title'],
			            'post_content' => '',
			            'post_status' => 'publish',
			            'post_author' => $this->get_user_id(),
			            'post_type' => 'shop_coupon'
			        );
			        if(isset($_POST['excerpt']) && $_POST['excerpt'] != ""){
			        	$coupon["post_excerpt"] = $_POST['excerpt'];
			        }
			        $new_coupon_id = wp_insert_post( $coupon, true );
			        if (is_wp_error($new_coupon_id)) {
					    $this->dashboard_message =  '<div class="notification closeable error"><p>' . $new_coupon_id->get_error_message() . '. Please use diffrent coupon name.</p><a class="close" href="#"></a></div>';
					} else {
					    
				        // Write the $data values into postmeta table
				        foreach ($data as $key => $value) {
				            update_post_meta( $new_coupon_id, $key, $value );
				        }
				        $this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( '%s has been added', 'listeo_core' ), $title ) . '</p><a class="close" href="#"></a></div>';
						global $wp;
						wp_redirect(home_url( $wp->request ));
						exit;
				    }
			    } else {
			    	$this->dashboard_message =  '<div class="notification closeable error"><p>' . sprintf( __( 'Coupon with code "%s" already exists', 'listeo_core' ), $title ) . '</p><a class="close" href="#"></a></div>';
			    }
			}

			//delete

			if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'listeo_core_coupons_actions' ) ) {

				$action = sanitize_title( $_REQUEST['action'] );
				$_id = absint( $_REQUEST['coupon_id'] );

				try {
					//Get coupon
					$coupon    = get_post( $_id );
					$coupon_data = get_post( $coupon );
					if ( ! $coupon_data || 'shop_coupon' !== $coupon_data->post_type ) {
						$title = false;
					} else {
						$title = esc_html( get_the_title( $coupon_data ) );	
					}

					
					switch ( $action ) {
						
						case 'delete' :
							// Trash it
							wp_delete_post( $_id );

							// Message
							$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( '%s har blitt slettet', 'listeo_core' ), $title ) . '</p><a class="close" href="#"></a></div>';

							break;
						
						default :
							do_action( 'listeo_core_dashboard_do_action_' . $action );
							break;
					}

					do_action( 'listeo_core_my_listing_do_action', $action, $listing_id );

				} catch ( Exception $e ) {
					$this->dashboard_message = '<div class="notification closeable error">' . $e->getMessage() . '</div>';
				}
			}
			
				if ( isset( $_POST['listeo-coupon-edit'] ) && '1' == $_POST['listeo-coupon-edit'] ) {

					$customer_emails = sanitize_text_field($_POST['customer_email']);

					if(isset($_POST['listing_ids']) && is_array($_POST['listing_ids'])){

						$products = $this->get_products_ids_by_listing($_POST['listing_ids']);
						$listings = implode(",",$_POST['listing_ids']);

					} else {

						//global $current_user;                     

						$args = array(
						  'author'        =>  $current_user->ID, 
						  'orderby'       =>  'post_date',
						  'order'         =>  'ASC',
						  'fields'        => 'ids',
						  'post_type'      => 'listing',
						  'posts_per_page' => -1 // no limit
						);


						$current_user_posts = "";
						$listings = get_posts( $args );
						$products = $this->get_products_ids_by_listing($listings);
						$listings = implode(",",$listings);
					}
					  

					$data = array(
			            'discount_type'              => sanitize_text_field($_POST['discount_type']),
			            'coupon_amount'              => sanitize_text_field($_POST['coupon_amount']), // value
			            'individual_use'             => (isset($_POST['individual_use'])) ? sanitize_text_field($_POST['individual_use']) : 'no',//'no',
			            'product_ids'                => $products,
			            'listing_ids'                => $listings,
			            //'exclude_product_ids'        => '',
			            'usage_limit'                => sanitize_text_field($_POST['usage_limit']),
			            'usage_limit_per_user'       => sanitize_text_field($_POST['usage_limit_per_user']),//'1',
			            'limit_usage_to_x_items'     => '',
			            'usage_count'                => '',
			            'date_start'                => sanitize_text_field($_POST['date_start']),
			           // 'expiry_date'                => sanitize_text_field($_POST['expiry_date']),
			            'free_shipping'              => 'no',
			            'product_categories'         => '',
			            'exclude_product_categories' => '',
			            'exclude_sale_items'         => 'no',
			            'minimum_amount'             => sanitize_text_field($_POST['minimum_amount']),
			            'maximum_amount'             => sanitize_text_field($_POST['maximum_amount']),
			            'customer_email'             => $customer_emails,
			            'coupon_bg-uploader-id'		 => sanitize_text_field($_POST['listeo_coupon_bg_id']),
			        );
				  
			        // Save the coupon in the database
			        $coupon = array(
			        	'ID'           => $_POST['listeo-coupon-id'],
			            'post_title' => $_POST['title'],
			            'post_content' => '',
			            'post_excerpt' => $_POST['excerpt'],
			            'post_status' => 'publish',
			            'post_author' => $this->get_user_id(),
			            'post_type' => 'shop_coupon'
			        );

			        $wc_coupon = new WC_Coupon($_POST['listeo-coupon-id']);
			        $wc_coupon->set_date_expires( $_POST['expiry_date']);
					wp_update_post($coupon);
					foreach ($data as $key => $value) {
			            update_post_meta( $_POST['listeo-coupon-id'], $key, $value );
			        }
			        $wc_coupon->save();
					global $wp;
					wp_redirect(home_url( $wp->request ));
					exit;

			}
		}

	}
}