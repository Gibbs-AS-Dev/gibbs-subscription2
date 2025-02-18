<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Listeo  class.
 */
class Listeo_Core_Post_Types {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.26
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ), 5 );
		add_action( 'manage_listing_posts_custom_column', array( $this, 'custom_columns' ), 2 );
		add_filter( 'manage_edit-listing_columns', array( $this, 'columns' ) );

		add_action( 'pending_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'pending_payment_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'preview_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'draft_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'auto-draft_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'expired_to_publish', array( $this, 'set_expiry' ) );

		add_filter( 'wp_insert_post_data', array( $this, 'default_comments_on' ) );
		add_action( 'save_post', array( $this,'save_availibilty_calendar'), 10, 3 );
		add_action( 'save_post', array( $this,'save_as_product'), 10, 3 );
		add_action( 'save_post', array( $this,'save_event_timestamp'), 10, 3 );
		
		add_action('admin_footer-edit.php',array( $this, 'listeo_status_into_inline_edit'));
		add_filter( 'display_post_states', array( $this, 'listeo_display_status_label' ),10, 2);

		//featured default value

		add_action('save_post_listing', array( $this, 'set_default_featured'));
		add_action('edit_post_listing', array( $this, 'delete_google_reviews'));



		add_action( 'listeo_core_check_for_expired_listings', array( $this, 'check_for_expired' ) );

		add_action( 'admin_init', array( $this, 'approve_listing' ) );
		add_action( 'admin_notices', array( $this, 'action_notices' ) );

		add_action( 'bulk_actions-edit-listing', array( $this, 'add_bulk_actions' ) );
		add_action( 'handle_bulk_actions-edit-listing', array( $this, 'do_bulk_actions' ), 10, 3 );

		add_filter( 'manage_edit-listing_category_columns', array( $this, 'add_icon_column' ) );
		add_filter( 'manage_listing_category_custom_column', array( $this, 'add_icon_column_content' ), 10, 3 );

		add_filter( 'manage_edit-listing_category_columns', array( $this, 'add_assigned_features_column' ) );
		add_filter( 'manage_listing_category_custom_column', array( $this, 'add_assigned_features_content' ), 10, 3 );

		add_action( 'wp_insert_post', array( $this, 'set_default_avg_rating_new_post')) ;
		add_action( 'before_delete_post', array($this, 'remove_product_on_listing_remove' ));
		

		if(get_option('listeo_region_in_links' )) {

			add_action( 'wp_loaded', array( $this, 'add_listings_permastructure' ) );
			add_filter( 'post_type_link', array( $this,'listing_permalinks' ), 10, 2 );
			add_filter( 'term_link', array( $this,'add_term_parents_to_permalinks'), 10, 2 );
			
		}
		add_filter('add_menu_classes', array( $this,'show_pending_number'));
		
	

	}
	function listeo_status_into_inline_edit() { // ultra-simple example

		echo "<script>
		jQuery(document).ready( function() {
			jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"preview\">Preview</option>' );
			jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"expired\">Expired</option>' );
			jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"pending_payment\">Pending Payment</option>' );
		});
		</script>";
	}

	function listeo_display_status_label( $statuses,  $post  ) {
		
			global $post; // we need ivat to check current post status
	
		if ($post && 'listing' == get_post_type( $post->ID ) ) {


			if( get_query_var( 'post_status' ) != 'pending_payment' ){ // not for pages with all posts of this status
				if( $post->post_status == 'pending_payment' ){ // если статус поста - Архив
					return array('Pending Payment'); // returning our status label
				}
			}	
			if( get_query_var( 'post_status' ) != 'expired' ){ // not for pages with all posts of this status
				if( $post->post_status == 'expired' ){ // если статус поста - Архив
					return array('Expired'); // returning our status label
				}
			}	
			if( get_query_var( 'post_status' ) != 'preview' ){ // not for pages with all posts of this status
				if( $post->post_status == 'preview' ){ // если статус поста - Архив
					return array('Preview'); // returning our status label
				}
			}
		}
		
		
		return $statuses; // returning the array with default statuses
	}
	 

	function set_default_featured($post_id) {
	   add_post_meta($post_id, '_featured', '0', true);
	}

	function delete_google_reviews($post_id) {
	   delete_transient( 'listeo_reviews_'.$post_id );
	}

	function show_pending_number($menu) {
	    $types = array("listing");
	    $status = "pending";
	    foreach($types as $type) {
	        $num_posts = wp_count_posts($type, 'readable');
	        $pending_count = 0;
	        if (!empty($num_posts->$status)) $pending_count = $num_posts->$status;
	 
	        if ($type == 'post') {
	            $menu_str = 'edit.php';
	        } else {
	            $menu_str = 'edit.php?post_type=' . $type;
	        }
	 
	        foreach( $menu as $menu_key => $menu_data ) {
	            if( $menu_str != $menu_data[2] )
	                continue;
	            $menu[$menu_key][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>"
	                . number_format_i18n($pending_count)
	                . '</span></span>';
	        }
	    }
	    return $menu;
	}
	/**
	 * Get the permalink settings directly from the option.
	 *
	 * @return array Permalink settings option.
	 */
	public static function get_raw_permalink_settings() {
		/**
		 * Option `wpjm_permalinks` was renamed to match other options in 1.32.0.
		 *
		 * Reference to the old option and support for non-standard plugin updates will be removed in 1.34.0.
		 */
		$legacy_permalink_settings = '[]';
		if ( false !== get_option( 'listeo_permalinks', false ) ) {
			$legacy_permalink_settings = wp_json_encode( get_option( 'listeo_permalinks', array() ) );
			delete_option( 'listeo_permalinks' );
		}

		return (array) json_decode( get_option( 'listeo_core_permalinks', $legacy_permalink_settings ), true );
	}

	/**
	 * Retrieves permalink settings.
	 *
	 * @see https://github.com/woocommerce/woocommerce/blob/3.0.8/includes/wc-core-functions.php#L1573
	 * @since 1.28.0
	 * @return array
	 */
	public static function get_permalink_structure() {
		// Switch to the site's default locale, bypassing the active user's locale.
		if ( function_exists( 'switch_to_locale' ) && did_action( 'admin_init' ) ) {
			switch_to_locale( get_locale() );
		}

		$permalink_settings = self::get_raw_permalink_settings();

		// First-time activations will get this cleared on activation.
		if ( ! array_key_exists( 'listings_archive', $permalink_settings ) ) {
			// Create entry to prevent future checks.
			$permalink_settings['listings_archive'] = '';
			
				// This isn't the first activation and the theme supports it. Set the default to legacy value.
				$permalink_settings['listings_archive'] = _x( 'listings', 'Post type archive slug - resave permalinks after changing this', 'listeo_core' );
			
			update_option( 'listeo_core_permalinks', wp_json_encode( $permalink_settings ) );
		}

		$permalinks         = wp_parse_args(
			$permalink_settings,
			array(
				'listing_base'      => '',
				'category_base' => '',
				'listings_archive'  => '',
			)
		);

		// Ensure rewrite slugs are set. Use legacy translation options if not.
		$permalinks['listing_rewrite_slug']          = untrailingslashit( empty( $permalinks['listing_base'] ) ? _x( 'listing', 'Job permalink - resave permalinks after changing this', 'listeo_core' ) : $permalinks['listing_base'] );
		$permalinks['category_rewrite_slug']     = untrailingslashit( empty( $permalinks['category_base'] ) ? _x( 'listing-category', 'Listing category slug - resave permalinks after changing this', 'listeo_core' ) : $permalinks['category_base'] );
		
		$permalinks['listings_archive_rewrite_slug'] = untrailingslashit( empty( $permalinks['listings_archive'] ) ? 'listings' : $permalinks['listings_archive'] );

		// Restore the original locale.
		if ( function_exists( 'restore_current_locale' ) && did_action( 'admin_init' ) ) {
			restore_current_locale();
		}
		return $permalinks;
	}


	public function remove_product_on_listing_remove($postid) {
		$product_id = get_post_meta($postid,'product_id',true);
		
		wp_delete_post($product_id, true);
	}


	public function remove_gallery_on_listing_remove($postid) {
		$gallery = get_post_meta( $postid, '_gallery', true );

		if(!empty($gallery)) : 
			foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
				wp_delete_attachment($attachment_id);
			}
		endif;
		
	}

	function save_availibilty_calendar( $post_ID, $post, $update ) {
	  	
	
			
			$bookings = new Listeo_Core_Bookings_Calendar;
			
			// set array only with dates when listing is not avalible
			$avaliabity = get_post_meta($post_ID, '_availability', true);
			

			if($avaliabity) {
			 	
					
				$dates = array_filter( explode( "|", $avaliabity['dates'] ) );
				
				if ( ! empty( $dates ) ) $bookings :: update_reservations( $post_ID, $dates );

			// set array only with dates when we have special prices for booking
				$special_prices = json_decode( $avaliabity['price'], true );
		
				if ( ! empty( $special_prices ) ) $bookings :: update_special_prices( $post_ID, $special_prices );
			}
	
	}
	
	function save_event_timestamp( $post_ID, $post, $update ) {
			$post_type = get_post_meta($post_ID, '_listing_type', true);
			

			if($post_type == 'event'){
				$event_date = get_post_meta($post_ID, '_event_date', true);
                 
                if($event_date){
                    $meta_value_date = explode(' ', $event_date,2); 
                    $meta_value_stamp_obj = DateTime::createFromFormat(listeo_date_time_wp_format_php(), $meta_value_date[0]);
                    if($meta_value_stamp_obj){
                    	$meta_value_stamp = $meta_value_stamp_obj->getTimestamp();
                    	update_post_meta($post_ID, '_event_date_timestamp', $meta_value_stamp );    
                    }
                    
                    
                }

                $event_date_end = get_post_meta($post_ID, '_event_date_end', true);
                
                if($event_date_end){
                    $meta_value_date_end = explode(' ', $event_date_end, 2); 
                    $meta_value_stamp_end_obj = DateTime::createFromFormat(listeo_date_time_wp_format_php(), $meta_value_date_end[0]);

                    if($meta_value_stamp_end_obj){
                    	$meta_value_stamp_end = $meta_value_stamp_end_obj->getTimestamp();
                    	update_post_meta( $post_ID, '_event_date_end_timestamp', $meta_value_stamp_end );    
                    }
                    
                }   
			}

	}

	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_types() {
	/*
		if ( post_type_exists( "listing" ) )
			return;*/

		// Custom admin capability
		$admin_capability = 'edit_listings';
		$permalink_structure = self::get_permalink_structure();
				
	
		// Set labels and localize them
	
		$listing_name		= apply_filters( 'listeo_core_taxonomy_listing_name', __( 'Listings', 'listeo_core' ) );
		$listing_singular	= apply_filters( 'listeo_core_taxonomy_listing_singular', __( 'Listing', 'listeo_core' ) );
	
		register_post_type( "listing",
			apply_filters( "register_post_type_listing", array(
				'labels' => array(
					'name'					=> $listing_name,
					'singular_name' 		=> $listing_singular,
					'menu_name'             => esc_html__( 'Listings', 'listeo_core' ),
					'all_items'             => sprintf( esc_html__( 'All %s', 'listeo_core' ), $listing_name ),
					'add_new' 				=> esc_html__( 'Add New', 'listeo_core' ),
					'add_new_item' 			=> sprintf( esc_html__( 'Add %s', 'listeo_core' ), $listing_singular ),
					'edit' 					=> esc_html__( 'Edit', 'listeo_core' ),
					'edit_item' 			=> sprintf( esc_html__( 'Edit %s', 'listeo_core' ), $listing_singular ),
					'new_item' 				=> sprintf( esc_html__( 'New %s', 'listeo_core' ), $listing_singular ),
					'view' 					=> sprintf( esc_html__( 'View %s', 'listeo_core' ), $listing_singular ),
					'view_item' 			=> sprintf( esc_html__( 'View %s', 'listeo_core' ), $listing_singular ),
					'search_items' 			=> sprintf( esc_html__( 'Search %s', 'listeo_core' ), $listing_name ),
					'not_found' 			=> sprintf( esc_html__( 'No %s found', 'listeo_core' ), $listing_name ),
					'not_found_in_trash' 	=> sprintf( esc_html__( 'No %s found in trash', 'listeo_core' ), $listing_name ),
					'parent' 				=> sprintf( esc_html__( 'Parent %s', 'listeo_core' ), $listing_singular ),
				),
				'description' => sprintf( esc_html__( 'This is where you can create and manage %s.', 'listeo_core' ), $listing_name ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'show_in_rest' 			=> true,
				'capability_type' 		=> array( 'listing', 'listings' ),
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'menu_icon'           => 'dashicons-admin-multisite',
				'rewrite' 				=> array(
						'slug'       => $permalink_structure['listing_rewrite_slug'],
						'with_front' => true,
						'feeds'      => true,
						'pages'      => true
					),
				'query_var' 			=> true,
				'supports' 				=> array( 'title', 'author','editor', 'custom-fields', 'publicize', 'thumbnail','comments' ),
				'has_archive' 			=> $permalink_structure['listings_archive_rewrite_slug'],
				'show_in_nav_menus' 	=> true
			) )
		);


		register_post_status( 'preview', array(
			'label'                     => _x( 'Preview', 'post status', 'listeo_core' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Preview <span class="count">(%s)</span>', 'Preview <span class="count">(%s)</span>', 'listeo_core' ),
		) );

		register_post_status( 'expired', array(
			'label'                     => _x( 'Expired', 'post status', 'listeo_core' ),
			'public'                    => false,
			'protected'                 => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'listeo_core' ),
		) );

		register_post_status( 'pending_payment', array(
			'label'                     => _x( 'Pending Payment', 'post status', 'listeo_core' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'listeo_core' ),
		) );


		
		// Register taxonomy "Listing Categry"
		$singular  = __( 'Category', 'listeo_core' );
		$plural    = __( 'Categories', 'listeo_core' );	
		$rewrite   = array(
			'slug'         => $permalink_structure['category_rewrite_slug'],
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "listing_category",
			apply_filters( 'register_taxonomy_listing_category_object_type', array( 'listing' ) ),
       	 	apply_filters( 'register_taxonomy_listing_category_args', array(
	            'hierarchical' 			=> true,
	            /*'update_count_callback' => '_update_post_term_count',*/
	            'label' 				=> $plural,
	            'show_in_rest' => true,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'listeo_core' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'listeo_core' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'listeo_core' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'listeo_core' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'listeo_core' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'listeo_core' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'listeo_core' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'listeo_core' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	            /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );	


// Register taxonomy "Events Categry"
		$singular  = __( 'Event Category', 'listeo_core' );
		$plural    = __( 'Events Categories', 'listeo_core' );	
		$rewrite   = array(
			'slug'         => _x( 'events-category', 'Event Category slug - resave permalinks after changing this', 'listeo_core' ),
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "event_category",
			apply_filters( 'register_taxonomy_event_category_object_type', array( 'listing' ) ),
       	 	apply_filters( 'register_taxonomy_event_category_args', array(
	            'hierarchical' 			=> true,
	            /*'update_count_callback' => '_update_post_term_count',*/
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'listeo_core' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'listeo_core' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'listeo_core' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'listeo_core' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'listeo_core' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'listeo_core' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'listeo_core' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'listeo_core' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_in_rest' => true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	            /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );	
// Register taxonomy "Service Categry"
		$singular  = __( 'Service Category', 'listeo_core' );
		$plural    = __( 'Service Categories', 'listeo_core' );	
		$rewrite   = array(
			'slug'         => _x( 'service-category', 'Service Category slug - resave permalinks after changing this', 'listeo_core' ),
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "service_category",
			apply_filters( 'register_taxonomy_service_category_object_type', array( 'listing' ) ),
       	 	apply_filters( 'register_taxonomy_service_category_args', array(
	            'hierarchical' 			=> true,
	            /*'update_count_callback' => '_update_post_term_count',*/
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'listeo_core' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'listeo_core' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'listeo_core' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'listeo_core' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'listeo_core' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'listeo_core' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'listeo_core' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'listeo_core' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_in_rest' => true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	            /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );	
// Register taxonomy "Rental Categry"
		$singular  = __( 'Rental Category', 'listeo_core' );
		$plural    = __( 'Rentals Categories', 'listeo_core' );	
		$rewrite   = array(
			'slug'         => _x( 'rental-category', 'Rental Category slug - resave permalinks after changing this', 'listeo_core' ),
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "rental_category",
			apply_filters( 'register_taxonomy_rental_category_object_type', array( 'listing' ) ),
       	 	apply_filters( 'register_taxonomy_rental_category_args', array(
	            'hierarchical' 			=> true,
	            /*'update_count_callback' => '_update_post_term_count',*/
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'listeo_core' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'listeo_core' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'listeo_core' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'listeo_core' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'listeo_core' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'listeo_core' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'listeo_core' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'listeo_core' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_in_rest' => true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	            /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );	
// Register taxonomy "classifieds Categry"
		$singular  = __( 'Classifieds Category', 'listeo_core' );
		$plural    = __( 'Classifieds Categories', 'listeo_core' );	
		$rewrite   = array(
			'slug'         => _x( 'classifieds-category', 'Classifieds Category slug - resave permalinks after changing this', 'listeo_core' ),
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "classifieds_category",
			apply_filters( 'register_taxonomy_classifieds_category_object_type', array( 'listing' ) ),
       	 	apply_filters( 'register_taxonomy_classifieds_category_args', array(
	            'hierarchical' 			=> true,
	            /*'update_count_callback' => '_update_post_term_count',*/
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'listeo_core' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'listeo_core' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'listeo_core' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'listeo_core' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'listeo_core' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'listeo_core' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'listeo_core' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'listeo_core' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_in_rest' => true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	            /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );	

	    // Register taxonomy "Features"
		$singular  = __( 'Feature', 'listeo_core' );
		$plural    = __( 'Features', 'listeo_core' );	
		$rewrite   = array(
			'slug'         => _x( 'listing-feature', 'Feature slug - resave permalinks after changing this', 'listeo_core' ),
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "listing_feature",
			apply_filters( 'register_taxonomy_listing_features_object_type', array( 'listing' ) ),
       	 	apply_filters( 'register_taxonomy_listing_features_args', array(
	            'hierarchical' 			=> true,
	            /*'update_count_callback' => '_update_post_term_count',*/
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'listeo_core' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'listeo_core' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'listeo_core' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'listeo_core' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'listeo_core' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'listeo_core' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'listeo_core' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'listeo_core' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_in_rest' => true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	            /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );		

	    // Register taxonomy "Region"
		$singular  = __( 'Region', 'listeo_core' );
		$plural    = __( 'Regions', 'listeo_core' );	
		$rewrite   = array(
			'slug'         => _x( 'region', 'Region slug - resave permalinks after changing this', 'listeo_core' ),
			'with_front'   => true,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "region",
			apply_filters( 'register_taxonomy_region_object_type', array( 'listing' ) ),
       	 	apply_filters( 'register_taxonomy_region_args', array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'listeo_core' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'listeo_core' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'listeo_core' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'listeo_core' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'listeo_core' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'listeo_core' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'listeo_core' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'listeo_core' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_in_rest' => true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	           /* 'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );
			
		
	} /* eof register*/

	/**
	 * Adds columns to admin listing of listing Listings.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			$columns = array();
		}
		
		$columns["listing_type"]     	 	= __( "Type", 'listeo_core');
		$columns["listing_region"]      	= __( "Region", 'listeo_core');
		$columns["listing_address"]      	= __( "Address", 'listeo_core');
		$columns["listing_posted"]          = __( "Posted", 'listeo_core');
		$columns["expires"]           		= __( "Expires", 'listeo_core');
		$columns['featured_listing']        = '<span class="tips" data-tip="' . __( "Featured?", 'listeo_core') . '">' . __( "Featured?", 'listeo_core') . '</span>';
		$columns['listing_actions']         = __( "Actions", 'listeo_core');
		return $columns;
	}

	/**
	 * Displays the content for each custom column on the admin list for listing Listings.
	 *
	 * @param mixed $column
	 */
	public function custom_columns( $column ) {
		global $post;

		switch ( $column ) {
			case "listing_type" :
				$type = get_post_meta($post->ID, '_listing_type', true);
				switch ($type) {
					case 'service':
						echo esc_html_e('Service','listeo_core');
						break;
					case 'rental':
						echo esc_html_e('Rental','listeo_core');
						break;
					case 'event':
						echo esc_html_e('Event','listeo_core');
						break;
					
					default:
						# code...
						break;
				}
			break;
			
			case "listing_address" :
				the_listing_address( $post );
			break;
			case "listing_region" :
				if ( ! $terms = get_the_term_list( $post->ID, 'region', '', ', ', '' ) ) echo '<span class="na">&ndash;</span>'; else echo $terms;
			break;

			case "expires" :
				$expires = get_post_meta($post->ID,'_listing_expires',true);
				if(( is_numeric($expires) && (int)$expires == $expires && (int)$expires>0)){ 
					echo date_i18n( get_option( 'date_format' ), $expires);
				} else {
					echo $expires;
				}

			break;

			case "featured_listing" :
				if ( listeo_core_is_featured( $post->ID ) ) echo '&#10004;'; else echo '&ndash;';
			break;
			case "listing_posted" :
				echo '<strong>' . date_i18n( __( 'M j, Y', 'listeo_core'), strtotime( $post->post_date ) ) . '</strong><span>';
				echo ( empty( $post->post_author ) ? __( 'by a guest', 'listeo_core') : sprintf( __( 'by %s', 'listeo_core'), '<a href="' . esc_url( add_query_arg( 'author', $post->post_author ) ) . '">' . get_the_author() . '</a>' ) ) . '</span>';
			break;
			
			case "listing_actions" :
				echo '<div class="actions">';

				$admin_actions = apply_filters( 'listeo_core_post_row_actions', array(), $post );

				if ( in_array( $post->post_status, array( 'pending', 'preview', 'pending_payment' ) ) && current_user_can ( 'publish_post', $post->ID ) ) {
					$admin_actions['approve']   = array(
						'action'  => 'approve',
						'name'    => __( 'Approve', 'listeo_core'),
						'url'     =>  wp_nonce_url( add_query_arg( 'approve_listing', $post->ID ), 'approve_listing' )
					);
				}
/*				if ( $post->post_status !== 'trash' ) {
					if ( current_user_can( 'read_post', $post->ID ) ) {
						$admin_actions['view']   = array(
							'action'  => 'view',
							'name'    => __( 'View', 'listeo_core'),
							'url'     => get_permalink( $post->ID )
						);
					}
					if ( current_user_can( 'edit_post', $post->ID ) ) {
						$admin_actions['edit']   = array(
							'action'  => 'edit',
							'name'    => __( 'Edit', 'listeo_core'),
							'url'     => get_edit_post_link( $post->ID )
						);
					}
					if ( current_user_can( 'delete_post', $post->ID ) ) {
						$admin_actions['delete'] = array(
							'action'  => 'delete',
							'name'    => __( 'Delete', 'listeo_core'),
							'url'     => get_delete_post_link( $post->ID )
						);
					}
				}*/

				$admin_actions = apply_filters( 'listing_manager_admin_actions', $admin_actions, $post );

				foreach ( $admin_actions as $action ) {
					if ( is_array( $action ) ) {
						printf( '<a class="button button-icon tips icon-%1$s" href="%2$s" data-tip="%3$s">%4$s</a>', $action['action'], esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_html( $action['name'] ) );
					} else {
						echo str_replace( 'class="', 'class="button ', $action );
					}
				}

				echo '</div>';

			break;
		}
	}


	/**
	 * Sets expiry date when status changes.
	 *
	 * @param WP_Post $post
	 */
	public function set_expiry( $post ) {
		if ( $post->post_type !== 'listing' ) {
			return;
		}
		$expires =  get_post_meta( $post->ID, '_listing_expires', true );

		// See if it is already set
		if ( $expires ) {
			
			
			if(( is_numeric($expires) && (int)$expires == $expires && (int)$expires>0)){
				
			} else {
				$expires = CMB2_Utils::get_timestamp_from_value( $expires, 'm/d/Y' ); 
				if ( $expires && $expires < current_time( 'timestamp' ) ) {
					update_post_meta( $post->ID, '_listing_expires', '' );
				} else {
					
					//update_post_meta( $post->ID, '_listing_expires', $expires );
				}
			}		
			
			
		
		}
		

		// See if the user has set the expiry manually:
		if ( ! empty( $_POST[ '_listing_expires' ] ) ) {
			$expires = $_POST[ '_listing_expires' ];
			if(( is_numeric($expires) && (int)$expires == $expires && (int)$expires>0)){
				//
			} else {
				$expires = CMB2_Utils::get_timestamp_from_value( $expires, 'm/d/Y' ); 
			
			}		
			update_post_meta( $post->ID, '_listing_expires',  $expires );
		
		// No manual setting? Lets generate a date if there isn't already one
		} elseif (!$expires ) {
			$expires = calculate_listing_expiry( $post->ID );
			update_post_meta( $post->ID, '_listing_expires', $expires );

			// In case we are saving a post, ensure post data is updated so the field is not overridden
			if ( isset( $_POST[ '_listing_expires' ] ) ) {
				$expires = $_POST[ '_listing_expires' ];
				if(( is_numeric($expires) && (int)$expires == $expires && (int)$expires>0)){
					//
				} else {
					$expires = CMB2_Utils::get_timestamp_from_value( $expires, 'm/d/Y' ); 
				
				}	
				$_POST[ '_listing_expires' ] = $expires;
			}
		}
	}


	/**
	 * Maintenance task to expire listings.
	 */
	public function check_for_expired() {
		global $wpdb;
		$date_format = get_option('date_format');
		// Change status to expired
		$listing_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_listing_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'listing'
		",  current_time( 'timestamp' ) ) );

		if ( $listing_ids ) {
			foreach ( $listing_ids as $listing_id ) {
		
				$listing_data       = array();
				$listing_data['ID'] = $listing_id;
				$listing_data['post_status'] = 'expired';
				wp_update_post( $listing_data );
				do_action('listeo_core_expired_listing',$listing_id);
			}
		}

		// Notifie expiring in 5 days
		$listing_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_listing_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'listing'
		", strtotime( date( $date_format, strtotime('+5 days') ) ) ) );

		if ( $listing_ids ) {
			foreach ( $listing_ids as $listing_id ) {
				$listing_data['ID'] = $listing_id;
				do_action('listeo_core_expiring_soon_listing',$listing_id);
			}
		}
		// Delete old expired listings
		if ( apply_filters( 'listeo_core_delete_expired_listings', false ) ) {
			$listing_ids = $wpdb->get_col( $wpdb->prepare( "
				SELECT posts.ID FROM {$wpdb->posts} as posts
				WHERE posts.post_type = 'listing'
				AND posts.post_modified < %s
				AND posts.post_status = 'expired'
			", strtotime( date( $date_format, strtotime( '-' . apply_filters( 'listeo_delete_expired_listings_days', 30 ) . ' days', current_time( 'timestamp' ) ) ) ) ) );

			if ( $listing_ids ) {
				foreach ( $listing_ids as $listing_id ) {
					wp_trash_post( $listing_id );
				}
			}
		}
	}


	/**
	 * Adds bulk actions to drop downs on Job Listing admin page.
	 *
	 * @param array $bulk_actions
	 * @return array
	 */
	public function add_bulk_actions( $bulk_actions ) {
		global $wp_post_types;

		foreach ( $this->get_bulk_actions() as $key => $bulk_action ) {
			if ( isset( $bulk_action['label'] ) ) {
				$bulk_actions[ $key ] = sprintf( $bulk_action['label'], $wp_post_types['listing']->labels->name );
			}
		}
		return $bulk_actions;
	}


	/**
	 * Performs bulk actions on Job Listing admin page.
	 *
	 * @since 1.27.0
	 *
	 * @param string $redirect_url The redirect URL.
	 * @param string $action       The action being taken.
	 * @param array  $post_ids     The posts to take the action on.
	 */
	public function do_bulk_actions( $redirect_url, $action, $post_ids ) {
		$actions_handled = $this->get_bulk_actions();
		if ( isset ( $actions_handled[ $action ] ) && isset ( $actions_handled[ $action ]['handler'] ) ) {
			$handled_jobs = array();
			if ( ! empty( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					if ( 'listing' === get_post_type( $post_id )
					     && call_user_func( $actions_handled[ $action ]['handler'], $post_id ) ) {
						$handled_jobs[] = $post_id;
					}
				}
				wp_redirect( add_query_arg( 'handled_jobs', $handled_jobs, add_query_arg( 'action_performed', $action, $redirect_url ) ) );
				exit;
			}
		}
	}

	/**
	 * Returns the list of bulk actions that can be performed on job listings.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions_handled = array();
		$actions_handled['approve_listings'] = array(
			'label' => __( 'Approve %s', 'listeo_core' ),
			'notice' => __( '%s approved', 'listeo_core' ),
			'handler' => array( $this, 'bulk_action_handle_approve_listing' ),
		);
		$actions_handled['expire_listings'] = array(
			'label' => __( 'Expire %s', 'listeo_core' ),
			'notice' => __( '%s expired', 'listeo_core' ),
			'handler' => array( $this, 'bulk_action_handle_expire_listing' ),
		);
	

		return apply_filters( 'listeo_core_bulk_actions', $actions_handled );
	}

	/**
	 * Performs bulk action to approve a single job listing.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function bulk_action_handle_approve_listing( $post_id ) {
		$job_data = array(
			'ID'          => $post_id,
			'post_status' => 'publish',
		);
		if ( in_array( get_post_status( $post_id ), array( 'pending', 'pending_payment' ) )
		     && current_user_can( 'publish_post', $post_id )
		     && wp_update_post( $job_data )
		) {
			return true;
		}
		return false;
	}

	/**
	 * Performs bulk action to expire a single job listing.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function bulk_action_handle_expire_listing( $post_id ) {
		$job_data = array(
			'ID'          => $post_id,
			'post_status' => 'expired',
		);
		if ( current_user_can( 'manage_listings', $post_id )
		     && wp_update_post( $job_data )
		) {
			return true;
		}
		return false;
	}


	/**
	 * Approves a single listing.
	 */
	public function approve_listing() {
		if ( ! empty( $_GET['approve_listing'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'approve_listing' ) && current_user_can( 'publish_post', $_GET['approve_listing'] ) ) {
			$post_id = absint( $_GET['approve_listing'] );
			$listing_data = array(
				'ID'          => $post_id,
				'post_status' => 'publish'
			);
			wp_update_post( $listing_data );
			wp_redirect( remove_query_arg( 'approve_listing', add_query_arg( 'handled_listings', $post_id, add_query_arg( 'action_performed', 'approve_listings', admin_url( 'edit.php?post_type=listing' ) ) ) ) );
			exit;
		}
	}

	/**
	 * Shows a notice if we did a bulk action.
	 */
	public function action_notices() {
		global $post_type, $pagenow;

		$handled_jobs = isset ( $_REQUEST['handled_listings'] ) ? $_REQUEST['handled_listings'] : false;
		$action = isset ( $_REQUEST['action_performed'] ) ? $_REQUEST['action_performed'] : false;
		$actions_handled = $this->get_bulk_actions();

		if ( $pagenow == 'edit.php'
			 && $post_type == 'listing'
			 && $action
			 && ! empty( $handled_jobs )
			 && isset ( $actions_handled[ $action ] )
			 && isset ( $actions_handled[ $action ]['notice'] )
		) {
			if ( is_array( $handled_jobs ) ) {
				$handled_jobs = array_map( 'absint', $handled_jobs );
				$titles       = array();
				foreach ( $handled_jobs as $job_id ) {
					$titles[] = listeo_core_get_the_listing_title( $job_id );
				}
				echo '<div class="updated"><p>' . sprintf( $actions_handled[ $action ]['notice'], '&quot;' . implode( '&quot;, &quot;', $titles ) . '&quot;' ) . '</p></div>';
			} else {
				
				echo '<div class="updated"><p>' . sprintf( $actions_handled[ $action ]['notice'], '&quot;' . listeo_core_get_the_listing_title(absint( $handled_jobs )) . '&quot;' ) . '</p></div>';
			}
		}
	}

	
	public function add_icon_column( $columns ) {
		
		$columns['icon'] = __( 'Icon', 'listeo_core' );
		return $columns;
	}


	/**
	 * Adds the Employment Type column content when listing job type terms in WP Admin.
	 *
	 * @param string $content
	 * @param string $column_name
	 * @param int    $term_id
	 * @return string
	 */
	public function add_icon_column_content( $content, $column_name, $term_id ) {
		
		if( 'icon' !== $column_name ){
			return $content;
		}
		
		$term_id = absint( $term_id );
		$icon = get_term_meta($term_id,'icon',true);
		
		if($icon) {
			$content .= '<i style="font-size:30px;" class="'.$icon.'"></i>';	
		}

		return $content;
	}

	public function add_assigned_features_column( $columns ) {
		
		$columns['features'] = __( 'Features', 'listeo_core' );
		return $columns;
	}

	public function add_assigned_features_content( $content, $column_name, $term_id ) {
		if( 'features' !== $column_name ){
			return $content;
		}
		
		$term_id = absint( $term_id );
		$term_meta = get_term_meta($term_id,'listeo_taxonomy_multicheck',true);
		if($term_meta){
			foreach ($term_meta as $feature) {
				$feature_obj = get_term_by('slug', $feature, 'listing_feature'); 
				
				if($feature_obj ){
					$term_link = get_term_link( $feature_obj );
					$content .= '<a href="'. esc_url( $term_link ).'">'.$feature_obj->name.'</a>, ';
				}
				
			}
			$content  = substr($content , 0, -2);
		}
		return $content;
	}

	public function set_default_avg_rating_new_post($post_ID){
		$current_field_value = get_post_meta($post_ID,'listeo-avg-rating',true); //change YOUMETAKEY to a default 
		$default_meta = '0'; //set default value

		if ($current_field_value == '' && !wp_is_post_revision($post_ID)){
		    add_post_meta($post_ID,'listeo-avg-rating',$default_meta,true);
		}
		return $post_ID;
	}



	function add_listings_permastructure() {
		global $wp_rewrite;

		$standard_slug = apply_filters( 'listeo_rewrite_listing_slug', 'listing' );
		$permalinks = Listeo_Core_Post_Types::get_permalink_structure();
		$slug = (isset($permalinks['listing_base']) && !empty($permalinks['listing_base'])) ? $permalinks['listing_base'] : $standard_slug ;
		

		//add_permastruct( 'region', $slug.'/%region%', false );
		//add_permastruct( 'listing_category', $slug.'/%listing_category%', false );
		add_permastruct( 'listing', $slug.'/%region%/%listing_category%/%listing%', false );
	}

	function listing_permalinks( $permalink, $post ) {
		if ( $post->post_type !== 'listing' )
			return $permalink;
		
		$regions = get_the_terms( $post->ID, 'region' );
		if ( ! $regions ) {
			$permalink = str_replace( '%region%/', '-/', $permalink );
		} else {

		$post_regions = array();
		foreach ( $regions as $region )
			$post_regions[] = $region->slug;

		$permalink = str_replace( '%region%', implode( ',', $post_regions ) , $permalink );
		}

		$categories = get_the_terms( $post->ID, 'listing_category' );
		if ( ! $categories ) {
			$permalink = str_replace( '%listing_category%/', '-/', $permalink );
		} else {



		$post_categories = array();
		foreach ( $categories as $category )
			$post_categories[] = $category->slug;
		
		$permalink = str_replace( '%listing_category%', implode( ',', $post_categories ) , $permalink );
		}


		return $permalink;
	}

	// Make sure that all term links include their parents in the permalinks
	
	function add_term_parents_to_permalinks( $permalink, $term ) {
		$term_parents = $this->get_term_parents( $term );
		foreach ( $term_parents as $term_parent )
			$permlink = str_replace( $term->slug, $term_parent->slug . ',' . $term->slug, $permalink );
		return $permalink;
	}

	function get_term_parents( $term, &$parents = array() ) {
		$parent = get_term( $term->parent, $term->taxonomy );
		
		if ( is_wp_error( $parent ) )
			return $parents;
		
		$parents[] = $parent;
		if ( $parent->parent )
			self::get_term_parents( $parent, $parents );
	    return $parents;
	}

	public function default_comments_on( $data ) {
	    if( $data['post_type'] == 'listing' ) {
	        $data['comment_status'] = 'open';
	    }

	    return $data;
	}


	function save_as_product( $post_ID, $post, $update ){
		if(!is_admin()){

			return;
		}
		if(!is_woocommerce_activated()){
			return;
		}

		if ($post->post_type == 'listing') {

			
			$product_id = get_post_meta($post_ID, 'product_id', true);

			$author =  get_current_user_id();

			if($product_id != ""){
				$pr_dt = get_post($product_id);
				if(isset($pr_dt->post_author)){
					$author = $pr_dt->post_author;
				}
			}

			// basic listing informations will be added to listing
			$product = array (
				'post_author' => $author,
				'post_content' => $post->post_content,
				'post_status' => 'publish',
				'post_title' => $post->post_title,
				'post_parent' => '',
				'post_type' => 'product',
			);

				// add product if not exist
			if ( ! $product_id ||  get_post_type( $product_id ) != 'product') {
				
				// insert listing as WooCommerce product
				$product_id = wp_insert_post( $product );
				wp_set_object_terms( $product_id, 'listing_booking', 'product_type' );

			} else {

				// update existing product
				$product['ID'] = $product_id;
				wp_update_post ( $product );

			}

		
		// set product category
			$term = get_term_by( 'name', apply_filters( 'listeo_default_product_category', 'Listeo booking'), 'product_cat', ARRAY_A );

			if ( ! $term ) $term = wp_insert_term(
				apply_filters( 'listeo_default_product_category', 'Listeo booking'),
				'product_cat',
				array(
				  'description'=> __( 'Listings category', 'listeo-core' ),
				  'slug' => str_replace( ' ', '-', apply_filters( 'listeo_default_product_category', 'Listeo booking') )
				)
			  );
		  
			wp_set_object_terms( $product_id, $term['term_id'], 'product_cat');

			update_post_meta($post_ID, 'product_id', $product_id);
		}
	
	}	

}