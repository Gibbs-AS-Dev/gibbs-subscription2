<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Listeo_Core_Admin_Packages class.
 *
 * @extends WP_List_Table
 */
class Listeo_Core_Admin_Packages extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'package',
			'plural'   => 'packages',
			'ajax'     => false,
		) );
	}


	/**
	 * Get column default
	 *
	 * @param object $item
	 * @param string $column_name
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		global $wpdb;

		switch ( $column_name ) {
			case 'product_id' :
				$product = wc_get_product( $item->product_id );

				return $product ? '<a href="' . admin_url( 'post.php?post=' . absint( $product->get_id() ) . '&action=edit' ) . '">' . esc_html( $product->get_title() ) . '</a>' : __( 'n/a', 'listeo_core' );
			case 'user_id' :
				$user = get_user_by( 'id', $item->user_id );

				if ( $item->user_id && $user ) {
					return '<a href="' . admin_url( 'user-edit.php?user_id=' . absint( $item->user_id ) ) . '">' . esc_attr( $user->display_name ) . '</a><br/><span class="description">' . esc_html( $user->user_email ) . '</span>';
				} else {
					return __( 'n/a', 'listeo_core' );
				}
			case 'order_id' :
				return $item->order_id > 0 ? '<a href="' . admin_url( 'post.php?post=' . absint( $item->order_id ) . '&action=edit' ) . '">#' . absint( $item->order_id ) . ' &rarr;</a>' : __( 'n/a', 'listeo_core' );
			case 'featured_listing' :
				return $item->package_featured ? '&#10004;' : '&ndash;';
			case 'duration' :
				return $item->package_duration ? sprintf( __( '%d Days', 'listeo_core' ), absint( $item->package_duration ) ) : '&ndash;';
			case 'limit' :
				return '<a href="' . esc_url( admin_url( 'edit.php?post_type=listing&package=' . absint( $item->id ) ) ) . '">' . ( $item->package_limit ? sprintf( __( '%s Posted', 'listeo_core' ), absint( $item->package_count ) . ' / ' . absint( $item->package_limit ) ) : __( 'Unlimited', 'listeo_core' ) ) . '</a>';
			case 'has_listing_booking' :
				return $item->package_option_booking ? '&#10004;' : '&ndash;';
			case 'has_listing_reviews' :
				return $item->package_option_reviews ? '&#10004;' : '&ndash;';
			case 'has_listing_social_links' :
				return $item->package_option_social_links ? '&#10004;' : '&ndash;';
			case 'has_listing_opening_hours' :
				return $item->package_option_opening_hours ? '&#10004;' : '&ndash;';
			case 'has_listing_video' :
				return $item->package_option_video ? '&#10004;' : '&ndash;';
			case 'has_listing_coupons' :
				return $item->package_option_coupons ? '&#10004;' : '&ndash;';
			case 'has_listing_gallery' :
				return $item->package_option_gallery ? '&#10004;' : '&ndash;';
			case 'gallery_limit' :
				return $item->package_option_gallery_limit ? $item->package_option_gallery_limit : get_option('listeo_max_files');


			case 'listing_actions' :
				$edit_url = esc_url( add_query_arg( array(
					'action' => 'edit',
					'package_id' => $item->id,
				), admin_url( 'admin.php?page=listeo_core_paid_listings_packages' ) ) );
				$delete_url = wp_nonce_url( add_query_arg( array(
						'action' => 'delete',
						'package_id' => $item->id,
				), admin_url( 'admin.php?page=listeo_core_paid_listings_packages' ) ), 'delete', 'delete_nonce' );

				return '<div class="actions">' .
					'<a class="button button-icon icon-edit" href="' . $edit_url . '">' . __( 'Edit', 'listeo_core' ) . '</a>' .
					'<a class="button button-icon icon-delete" href="' . $delete_url . '">' . __( 'Delete', 'listeo_core' ) . '</a></div>' .
					'</div>';
		}// End switch().
	}
	public function display_tablenav( $which ) {
		
		parent::display_tablenav( $which );
		

	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'user_id'      => __( 'User', 'listeo_core' ),
			'product_id'   => __( 'Product', 'listeo_core' ),
			'limit'        => __( 'Limit', 'listeo_core' ),
			'duration'     => __( 'Duration', 'listeo_core' ),
			'featured_listing' => '<span class="tips" data-tip="' . __( 'Featured?', 'listeo_core' ) . '">' . __( 'Featured?', 'listeo_core' ) . '</span>',
			'has_listing_booking'     => __( 'Booking Module', 'listeo_core' ),
			'has_listing_reviews'     => __( 'Reviews  Module', 'listeo_core' ),
			'has_listing_social_links'     => __( 'Social Links Module', 'listeo_core' ),
			'has_listing_opening_hours'     => __( 'Opening Hours Module', 'listeo_core' ),
			'has_listing_video'     => __( 'Video Module', 'listeo_core' ),
			'has_listing_coupons'     => __( 'Coupons Module', 'listeo_core' ),
			'has_listing_gallery'     => __( 'Gallery Module', 'listeo_core' ),
			'gallery_limit'     => __( 'Images Limit', 'listeo_core' ),
			'order_id'     => __( 'Order ID', 'listeo_core' ),
			'listing_actions'  => __( 'Actions', 'listeo_core' ),
		);
		return $columns;

	
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'order_id'     => array( 'order_id', false ),
			'user_id'      => array( 'user_id', true ),
			'product_id'   => array( 'product_id', false ),
			'package_type' => array( 'package_type', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which
	 */
	
	function extra_tablenav( $which ) {
		?><div class="alignleft actions"><?php
			
	        if ( 'top' === $which && !is_singular() ) {
		        
	            ob_start();
	            
	           // $this->dates_dropdown();
	            
	           // $this->listings_dropdown();
	          
	            $this->author_dropdown('owner',"Select User");
	            /**
	             * Fires before the Filter button on the Productions list table.
	             *
	             * Syntax resembles 'restrict_manage_posts' filter in 'wp-admin/includes/class-wp-posts-list-table.php'.
	             *
	             * @since 0.15.17
	             *
	             * @param string $post_type The post type slug.
	             * @param string $which     The location of the extra table nav markup:
	             *                          'top' or 'bottom'.
	             */
	            do_action( 'restrict_manage_productions', $this->screen->post_type, $which );
	 
	            $output = ob_get_clean();
	 
	            if ( ! empty( $output ) ) {
	                echo $output;
	                submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
	            }
	            
	        }
        
        	if ( isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] === 'trash' ) {
				submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
			}
			
		?></div><?php
		do_action( 'manage_posts_extra_tablenav', $which );
	}

	function author_dropdown($role,$label){
		wp_dropdown_users(array(
	        'show_option_all' => $label,
	       // 'selected'        => get_query_var($role, 0),
	        'name'            => 'user_id',
	       // 'role'			 => $role
	    ));
	}

	function listings_dropdown( ) {


		$string = '<select name="listing_id">
            <option  value="" selected>Select Listing</option>';
 
		$args = array( 'numberposts' => '-1', 'post_status' => 'publish', 'post_type' => 'listing' );
		 
		$recent_posts = wp_get_recent_posts($args);
		    foreach( $recent_posts as $recent ){
		    	$selected_id = empty( $_REQUEST['listing_id'] ) ? '' :  $_REQUEST['listing_id'];
		    	
		    	if($recent['ID'] == $selected_id ) {

		    		$selected = 'selected';
		    	} else {
		    		$selected = '';
		    	}
		        
		        $string .= '<option '.$selected.' value="' .$recent["ID"] . '">' .   $recent["post_title"].'</option> ';
		    }
		 
		$string .= '</select>';

		
		echo '<label class="screen-reader-text" for="cat">' . __( 'Filter by category' ) . '</label>';
		echo $string;
		
	}	
	/**
	 * Prepares the list of items for displaying.
	 *
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @access public
	 */
	public function prepare_items() {
		global $wpdb;

		$current_page          = $this->get_pagenum();
		$per_page              = 30;
		$orderby               = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'user_id';
		$order                 = empty( $_REQUEST['order'] ) || $_REQUEST['order'] === 'asc' ? 'ASC' : 'DESC';
		$order_id              = ! empty( $_REQUEST['order_id'] ) ? absint( $_REQUEST['order_id'] ) : '';
		$user_id               = ! empty( $_REQUEST['user_id'] ) ? absint( $_REQUEST['user_id'] ) : '';
		$product_id            = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : '';
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$where                 = array( 'WHERE 1=1' );

		if ( $order_id ) {
			$where[] = 'AND order_id=' . $order_id;
		}
		if ( $user_id ) {
			$where[] = 'AND user_id=' . $user_id;
		}
		if ( $product_id ) {
			$where[] = 'AND product_id=' . $product_id;
		}

		$where       = implode( ' ', $where );
		$max         = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}listeo_core_user_packages $where;" );
		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}listeo_core_user_packages $where ORDER BY `{$orderby}` {$order} LIMIT %d, %d", ( $current_page - 1 ) * $per_page, $per_page ) );

		$this->set_pagination_args( array(
			'total_items' => $max,
			'per_page'    => $per_page,
			'total_pages' => ceil( $max / $per_page ),
		) );
	}
}
