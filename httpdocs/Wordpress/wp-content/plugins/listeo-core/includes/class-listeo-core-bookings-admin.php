<?php 

if ( ! defined( 'ABSPATH' )) exit; //  Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Bookings_Admin_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Booking', 'listeo_core' ), // singular name of the listed records
			'plural'   => __( 'Bookings', 'listeo_core' ), // plural name of the listed records
			'ajax'     => false // does this table support ajax?
		] );

	}


	/**
	 * Retrieve bookings data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 * @param int $id
	 *
	 * @return mixed
	 */
	public static function get_bookings( $args, $page_number ) {

		global $wpdb;
		if(!$page_number) {
			$page_number = 1;
		}
		
		$sql = "SELECT * FROM {$wpdb->prefix}bookings_calendar";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= ' WHERE `status` IS NOT NULL';
		
		if( isset($args['listing_id']) && !empty($args['listing_id']) ){
			$sql .= ' AND `listing_id` = ' . esc_sql( $args['listing_id'] );
		}

		if( isset($args['owner']) && !empty($args['owner']) ){
			$sql .= ' AND `owner_id` = ' . esc_sql( $args['owner'] );
		}
		if( isset($args['guest']) && !empty($args['guest']) ){
			$sql .= ' AND `bookings_author` = ' . esc_sql( $args['guest'] );
		}

		if ( isset($args['id']) ) 
		{			// for single one
			$sql .= ' AND `ID` = ' . esc_sql( $args['id'] );
		}
			else
		{

			// when we taking all
			$sql .= " LIMIT ". $args['per_page'];
			$sql .= ' OFFSET ' . ( $page_number - 1 ) * $args['per_page'];

		}


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	/**
	 * Delete a booking record.
	 *
	 * @param int $id booking ID
	 */
	public static function delete_booking( $id ) {

		global $wpdb;

		$wpdb->update( $wpdb->prefix . 'bookings_calendar', array("status" => "deleted"), array( 'id' => $id ));


		// $wpdb->delete(
		// 	"{$wpdb->prefix}bookings_calendar",
		// 	[ 'ID' => $id ],
		// 	[ '%d' ]
		// );

	}

	/**
	 * Update a booking record.
	 *
	 * @param array $values to change
	 * 
	 * @return number $records that was changed
	 */
	public static function update_booking( $values ) {

		global $wpdb;

		return $wpdb->update ( "{$wpdb->prefix}bookings_calendar", $values, array('ID' => $values['ID']) );

	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count($args) {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}bookings_calendar";
		$sql .= ' WHERE `status` IS NOT NULL';
		if( isset($args['listing_id']) && !empty($args['listing_id']) ){
			$sql .= ' AND `listing_id` = ' . esc_sql( $args['listing_id'] );
		}

		if( isset($args['owner']) && !empty($args['owner']) ){
			$sql .= ' AND `owner_id` = ' . esc_sql( $args['owner'] );
		}
		if( isset($args['guest']) && !empty($args['guest']) ){
			$sql .= ' AND `bookings_author` = ' . esc_sql( $args['guest'] );
		}

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no booking data is available */
	public function no_items() {
		_e( 'No bookings avaliable.', 'listeo_core' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		
		
		switch ( $column_name ) {
			case 'ID':
			return sprintf($item[ $column_name ].' <a href="?page=%s&action=%s&id=%s"> '. __('View Details', 'listeo_core') . '</a>',$_REQUEST['page'],'view',$item['ID']);
		//	return '<a href="' . get_edit_user_link($user_data->ID) . '" >' . $item[ $column_name ] . '</a>';
			case 'date_start':
			case 'date_end':
			case 'order_id':
			case 'status':
			case 'type':
			case 'price':
			case 'expiring':
			
			case 'created':
				return $item[ $column_name ];

			case 'listing_id':	
				return get_the_title($item[ $column_name ]);

			case 'owner_id':
			if($item[ $column_name ] != 0){
				$avatar = get_avatar( $item[ $column_name ], 32 );
				$user_data = get_userdata( $item[ $column_name ] );
				return '<a href="' . get_edit_user_link($user_data->ID) . '" >' . $user_data->user_login . '</a>';
			} else {
				return esc_html__('iCal import','listeo_core');
			}
				
			case 'bookings_author':
			if($item[ $column_name ] != 0){
				$avatar = get_avatar( $item[ $column_name ], 32 );
				$user_data = get_userdata( $item[ $column_name ] );
				return '<a href="' . get_edit_user_link($user_data->ID) . '" >' . $user_data->user_login . '</a>';
			} else {
				return esc_html__('iCal import','listeo_core');
			}
			
			case 'action' :
				$actions = array(
					'view' => sprintf('<a href="?page=%s&action=%s&id=%s">' . __('View Details', 'listeo_core') . '</a>',$_REQUEST['page'],'edit',$item['ID']),
					'edit' => sprintf('<a href="?page=%s&action=%s&id=%s">' . __('Edit', 'listeo_core') . '</a>',$_REQUEST['page'],'edit',$item['ID']),
					'delete' => sprintf('<a href="?page=%s&action=%s&id=%s">' . __('Delete', 'listeo_core') . '</a>',$_REQUEST['page'],'delete',$item['ID']),
				);
			return sprintf('%1$s %2$s', $item['ID'], $this->row_actions($actions) );
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);

	}
	function author_dropdown($role,$label){
		wp_dropdown_users(array(
	        'show_option_all' => $label,
	        'selected'        => get_query_var($role, 0),
	        'name'            => $role,
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
	 * Displays a dates drop-down for filtering on the Events list table.
	 *
	 * @since 0.16
	 */
	function dates_dropdown( ) {

		$options = array (
			'0' => __( 'All dates' ),
			'upcoming' => __( 'Upcoming bookings', 'listeo_core' ),
			'past' => __( 'Past bookings', 'listeo_core' ),			
			'today' => __( 'Today', 'listeo_core' ),			
			'last7days' => __( 'Last 7 days', 'listeo_core' ),			
		);

		$date = false;
		if ( !empty( $_REQUEST['date'] ) ) {
			$date = $_REQUEST['date'];
		}

		?><label class="screen-reader-text" for="date"><?php
			_e( 'Filter by date', 'wp_theatre' ); 
		?></label>
		<select id="date" name="date"><?php
			foreach( $options as $key => $value ) {
				?><option value="<?php echo $key; ?>" <?php selected( $date, $key, true );?>><?php 
					echo $value;
				?></option><?php				
			}
		?></select><?php
					
	}


	function extra_tablenav( $which ) {
		?><div class="alignleft actions"><?php
			
	        if ( 'top' === $which && !is_singular() ) {
		        
	            ob_start();
	            
	           // $this->dates_dropdown();
	            
	            $this->listings_dropdown();
	            $this->author_dropdown('guest',"Select Client");
	            $this->author_dropdown('owner',"Select Owner");
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


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {

		$columns = [
			'cb'      			=> '<input type="checkbox" />',
			'ID'    			=> __( 'ID', 'listeo_core' ),
			'bookings_author' 	=> __( 'Client', 'listeo_core' ),
			'owner_id'    		=> __( 'Owner', 'listeo_core' ),
			'listing_id' 		=> __( 'Listing', 'listeo_core' ),
			'date_start' 		=> __( 'Start date', 'listeo_core' ),
			'date_end' 			=> __( 'End date', 'listeo_core' ),
			'type' 				=> __( 'Type', 'listeo_core' ),
			'created' 			=> __( 'Created', 'listeo_core' ),
			'price' 			=> __( 'Price', 'listeo_core' ),
			'action' 			=> __( 'Action', 'listeo_core' )
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'ID' 			=> array( 'ID', true ),
			'city' 			=> array( 'city', false ),
			'bookings_author' => array( 'Client', true ),
			'owner_id' 		=> array( 'Owner', true ),
			'listing_id' 	=> array( 'Listing', true ),
			'date_start' 	=> array( 'Start date', true ),
			'date_end' 		=> array( 'End date', true ),
			'type' 			=> array( 'Type', true ),
			'created' 		=> array( 'Created', true ),
			'price' 		=> array( 'Price', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		
	    $columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/** Process bulk action */
		$this->process_bulk_action();


	    if ( ! empty( $_REQUEST['listing_id'] ) ) {
		    $args['listing_id'] = sanitize_text_field( $_REQUEST['listing_id'] );
	    } 
	    if ( ! empty( $_REQUEST['owner'] ) ) {
		    $args['owner'] = sanitize_text_field( $_REQUEST['owner'] );
	    } 
	    if ( ! empty( $_REQUEST['guest'] ) ) {
		    $args['guest'] = sanitize_text_field( $_REQUEST['guest'] );
	    }


		$args['per_page']     = $this->get_items_per_page( 'per_page', 20 );
		
		$current_page 	= $this->get_pagenum();
  		$columns 		= $this->get_columns();

		$total_items  	= self::record_count($args);



		$this->set_pagination_args( [
			'total_items' => $total_items, // WE have to calculate the total number of items
			'per_page'    => $args['per_page'] // WE have to determine how many items to show on a page
		] );

		$this->items = self::get_bookings( $args, $current_page );
	}

	public function process_bulk_action() {

		// Edit action
		


		if ( 'view' === $this->current_action()) { 
			$args['id'] = $_GET['id'];
			$booking = self::get_bookings( $args, NULL);
			
			?>
			<style>
	
			</style>
			<div class="list-box-listing bookings">
		<div class="list-box-listing-img"><a href="<?php echo get_author_posts_url($booking[0]['bookings_author']); ?>"><?php echo get_avatar($booking[0]['bookings_author'], '70') ?></a></div>
		<div class="list-box-listing-content">
			<div class="inner">
				<h3 id="title"><a href="<?php echo get_permalink($booking[0]['listing_id']); ?>"><?php echo get_the_title($booking[0]['listing_id']); ?></a></h3>

				<div class="inner-booking-list">
					<h5><?php esc_html_e('Booking Date:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<?php 
						//get post type to show proper date
						$listing_type = get_post_meta($booking[0]['listing_id'],'_listing_type', true);

						if($listing_type == 'rental') { ?>
							<li class="highlighted" id="date"><?php echo date_i18n(get_option( 'date_format' ), strtotime($booking[0]['date_start'])); ?> - <?php echo date_i18n(get_option( 'date_format' ), strtotime($booking[0]['date_end'])); ?></li>
						
						<?php } 
							else if($listing_type == 'service') { 
						?>
							<li class="highlighted" id="date">
								<?php echo date_i18n(get_option( 'date_format' ), strtotime($booking[0]['date_start'])); ?> <?php esc_html_e('at','listeo_core'); ?> 
								<?php 
									$time_start = date_i18n(get_option( 'time_format' ), strtotime($booking[0]['date_start']));
									$time_end = date_i18n(get_option( 'time_format' ), strtotime($booking[0]['date_end']));?>

								<?php echo $time_start ?> <?php if($time_start != $time_end) echo '- '.$time_end; ?></li>
						
						<?php } else { 
							//event ?>
							<li class="highlighted" id="date">
							<?php 
							$meta_value = get_post_meta($booking[0]['listing_id'],'_event_date',true);
							$meta_value_date = explode(' ', $meta_value,2); 

							$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
							$meta_value = date_i18n(get_option( 'date_format' ), strtotime($meta_value_date[0])); 
							
						
							//echo strtotime(end($meta_value_date));
							//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
							if( isset($meta_value_date[1]) ) { 
								$time = str_replace('-','',$meta_value_date[1]);
								$meta_value .= esc_html__(' at ','listeo_core'); 
								$meta_value .= date_i18n(get_option( 'time_format' ), strtotime($time));

							} echo $meta_value;

							$meta_value = get_post_meta($booking[0]['listing_id'],'_event_date_end',true);
							if(isset($meta_value) && !empty($meta_value))  : 
							
							$meta_value_date = explode(' ', $meta_value,2); 

							$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
							$meta_value = date_i18n(get_option( 'date_format' ), strtotime($meta_value_date[0])); 
							
						
							//echo strtotime(end($meta_value_date));
							//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
							if( isset($meta_value_date[1]) ) { 
								$time = str_replace('-','',$meta_value_date[1]);
								$meta_value .= esc_html__(' at ','listeo_core'); 
								$meta_value .= date_i18n(get_option( 'time_format' ), strtotime($time));

							} echo ' - '.$meta_value; ?>
							<?php endif; ?>
							</li>
						<?php }
						 ?>

					</ul>
				</div>

				<?php $details = json_decode($booking[0]['comment']); 

				
				if (
				 	(isset($details->childrens) && $details->childrens > 0)
				 	||
				 	(isset($details->adults) && $details->adults > 0)
				 	||
				 	(isset($details->tickets) && $details->tickets > 0)
				) { ?>			
				<div class="inner-booking-list">
					<h5><?php esc_html_e('Booking Details:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="details">
						<?php if( isset($details->childrens) && $details->childrens > 0) : ?>
							<?php printf( _n( '%d Child', '%s Children', $details->childrens, 'listeo_core' ), $details->childrens ) ?>
						<?php endif; ?>
						<?php if( isset($details->adults)  && $details->adults > 0) : ?>
							<?php printf( _n( '%d Guest', '%s Guests', $details->adults, 'listeo_core' ), $details->adults ) ?>
						<?php endif; ?>
						<?php if( isset($details->tickets)  && $details->tickets > 0) : ?>
							<?php printf( _n( '%d Ticket', '%s Tickets', $details->tickets, 'listeo_core' ), $details->tickets ) ?>
						<?php endif; ?>
						</li>
					</ul>
				</div>	
				<?php } ?>	
				
				<?php
				$currency_abbr = get_option( 'listeo_currency' );
				$currency_postion = get_option( 'listeo_currency_postion' );
				$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
				$decimals = get_option('listeo_number_decimals',2);

				if($booking[0]['price']): ?>
				<div class="inner-booking-list">
					<h5><?php esc_html_e('Price:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="price">
							<?php if($currency_postion == 'before') { echo $currency_symbol.' '; } 
							?>
							<?php 	
							if(is_numeric($booking[0]['price'])){
							 	echo number_format_i18n($booking[0]['price'],$decimals);
							} else {
								echo esc_html($booking[0]['price']);
							}; ?>
							<?php if($currency_postion == 'after') { echo ' '.$currency_symbol; }  ?>
						</li>
					</ul>
				</div>	
				<?php endif; ?>	
				
				<div class="inner-booking-list">
					
					<h5><?php esc_html_e('Client:', 'listeo_core'); ?></h5>
					<ul class="booking-list" id="client">
						<?php if( isset($details->first_name) || isset($details->last_name) ) : ?>
						<li id="name">
							<a href="<?php echo get_author_posts_url($booking[0]['bookings_author']); ?>"><?php if(isset($details->first_name)) echo esc_html(stripslashes($details->first_name)); ?> <?php if(isset($details->last_name)) echo esc_html(stripslashes($details->last_name)); ?></a></li>
						<?php endif; ?>
						<?php if( isset($details->email)) : ?><li id="email"><a href="mailto:<?php echo esc_attr($details->email) ?>"><?php echo esc_html($details->email); ?></a></li>
						<?php endif; ?>
						<?php if( isset($details->phone)) : ?><li id="phone"><a href="tel:<?php echo esc_attr($details->phone) ?>"><?php echo esc_html($details->phone); ?></a></li>
						<?php endif; ?>
					</ul>
					
				</div>
				<?php if( isset($details->billing_address_1) ) : ?>
				<div class="inner-booking-list">
					
					<h5><?php esc_html_e('Address:', 'listeo_core'); ?></h5>
					<ul class="booking-list" id="client">
		
						<?php if( isset($details->billing_address_1) ) : ?>
							<li id="billing_address_1"><?php echo esc_html(stripslashes($details->billing_address_1)); ?> </li>
						<?php endif; ?>
						<?php if( isset($details->billing_address_1) ) : ?>
							<li id="billing_postcode"><?php echo esc_html(stripslashes($details->billing_postcode)); ?> </li>
						<?php endif; ?>	
						<?php if( isset($details->billing_city) ) : ?>
							<li id="billing_city"><?php echo esc_html(stripslashes($details->billing_city)); ?> </li>
						<?php endif; ?>
						<?php if( isset($details->billing_country) ) : ?>
							<li id="billing_country"><?php echo esc_html(stripslashes($details->billing_country)); ?> </li>
						<?php endif; ?>
						
					</ul>
				</div>
			<?php endif; ?>  
				<?php if( isset($details->service) && !empty($details->service)) : ?>
					<div class="inner-booking-list">
						<h5><?php esc_html_e('Extra Services:', 'listeo_core'); ?></h5>
						<?php echo listeo_get_extra_services_html($details->service); //echo wpautop( $details->service); ?>
					</div>	
				<?php endif; ?>
				<?php if( isset($details->message) && !empty($details->message)) : ?>
					<div class="inner-booking-list">
						<h5><?php esc_html_e('Message:', 'listeo_core'); ?></h5>
						<?php echo wpautop( esc_html(stripslashes($details->message))); ?>
					</div>	
				<?php endif; ?>


				<div class="inner-booking-list">
					<h5><?php esc_html_e('Request sent:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="price">
							<?php echo date_i18n(get_option( 'date_format' ), strtotime($booking[0]['created'])); ?>
							<?php 
								$date_created = explode(' ', $booking[0]['created']); 
									if( isset($date_created[1]) ) { ?>
									<?php esc_html_e('at','listeo_core'); ?>
									
							<?php echo date_i18n(get_option( 'time_format' ), strtotime($date_created[1])); } ?>
						</li>
					</ul>
				</div>	

				<?php if(isset($booking[0]['expiring']) && $booking[0]['expiring'] != '0000-00-00 00:00:00' && $booking[0]['expiring'] != $booking[0]['created']) { ?>
				<div class="inner-booking-list">
					<h5><?php esc_html_e('Payment due:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="price">
							<?php echo date_i18n(get_option( 'date_format' ), strtotime($booking[0]['expiring'])); ?>
							<?php 
								$date_expiring = explode(' ', $booking[0]['expiring']); 
									if( isset($date_expiring[1]) ) { ?>
									<?php esc_html_e('at','listeo_core'); ?>
									
							<?php echo date_i18n(get_option( 'time_format' ), strtotime($date_expiring[1])); } ?>
						</li>
					</ul>
				</div>	
				<?php } ?>

			
			</div>
		</div>
	</div>
		<?php 
		exit();
		}
		// Detect when a bulk action is being triggered
		if ( 'delete' === $this->current_action() ) {


				self::delete_booking( absint( $_GET['id'] ) );

		                //  esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                //  add_query_arg() return the current url
		                // wp_redirect( esc_url_raw(add_query_arg()) );
				// exit;


		}

		//  If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			//  loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_booking( $id );

			}

			//  esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        //  add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}

}


class Bookings_Admin_Plugin {

	//  class instance
	static $instance;

	//  booking WP_List_Table object
	public $bookings_obj;

	//  class constructor
	public function __construct() {

		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );

	}


	public static function set_screen( $status, $option, $value ) {

		return $value;

	}

	public function add_package_page(){

			$args['id'] = $_GET['id'];
		
			if  ( isset($_POST['ID']) )
			{
				
				
				
				$update_data = array(
		            'bookings_author' => $_POST['bookings_author'] ? $_POST['bookings_author']: '',
		            'owner_id' => $_POST['owner_id'] ? $_POST['owner_id']: '',
		            'listing_id' => $_POST['listing_id'] ? $_POST['listing_id']: '',
		            'date_start' => $_POST['date_start'] ? $_POST['date_start']: '',
		            'date_end' => $_POST['date_end'] ? $_POST['date_end']: '',
		         //   'comment' =>  stripslashes(json_encode($_POST['comment'])),
		            'order_id' =>  $_POST['order_id'],
		            'status' =>  $_POST['status'],
		            'expiring' =>  $_POST['expiring'],
		            'price' =>  $_POST['price'],
		            'type' =>  $_POST['type'],
		            'created' => $_POST['created']
		        );


$service = json_decode(stripslashes($_POST['comment']['service']));

		        $update_data['comment'] = json_encode(array(
					'first_name'=> stripslashes($_POST['comment']['first_name']),
					'last_name' =>stripslashes($_POST['comment']['last_name']),
					'email' =>stripslashes($_POST['comment']['email']),
					'phone'=> stripslashes($_POST['comment']['phone']),
					'adults' =>stripslashes($_POST['comment']['adults']),
					'message'=> stripslashes($_POST['comment']['message']),
					'service' => $service,
					'billing_address_1' =>stripslashes($_POST['comment']['billing_address_1']),
					'billing_postcode' =>stripslashes($_POST['comment']['billing_postcode']),
					'billing_city' =>stripslashes($_POST['comment']['billing_city']),
					'billing_country'=> stripslashes($_POST['comment']['billing_country']),
					'coupon'=> stripslashes($_POST['comment']['coupon']),
					'price' =>stripslashes($_POST['comment']['price'])
		        ));

		       
		    	 global $wpdb;
	 			$updated = $wpdb -> update( $wpdb->prefix . 'bookings_calendar', $update_data, array( 'id' => $_POST['ID'] ) );

	 			if ( false === $updated ) {
				    wp_die( __( 'Error while updating', 'listeo_core' ) );
				} else {
				    echo sprintf( '<div class="updated"><p>%s</p></div>', __( 'Booking was successfully changed', 'listeo_core' ) );
				}

				// if ( ! Bookings_Admin_List::update_booking( $_POST ) ) {
				// 	wp_die( __( 'Error while updating', 'listeo_core' ) );
				// } else {
				// 		echo sprintf( '<div class="updated"><p>%s</p></div>', __( 'Package successfully changed', 'listeo_core' ) );
				// } 
				
			}
			$booking = Bookings_Admin_List::get_bookings( $args, NULL);

			?>
			<pre><?php   ?></pre>
			<form action="" method="POST">
				<div class="wrap">     

				<table class="form-table">
				
				<input type="hidden" name="ID" value="<?php echo $booking[0]['ID'] ?>" /> 
				<div class="notice notice-warning">
					<p>Be extra careful while editing booking data.</p>
				</div>
				<tbody>
				
				<tr>
				<th scope="row"><label for="bookings_author"><?php _e( 'User id', 'listeo_core' );  ?></label></th>
				<td>
					<?php 
						wp_dropdown_users(array(	      
	      					'selected'        =>  $booking[0]['bookings_author'],
	        				'name'            => 'bookings_author',
	       					// 'role'			 => $role
	    				)); ?>
	    			</td>
				</tr>

				<tr>
				<th scope="row"><label for="owner_id"><?php _e( 'Owner id', 'listeo_core' );  ?></label></th>
				<td>
				<?php 
						wp_dropdown_users(array(	      
	      					'selected'        =>  $booking[0]['owner_id'],
	        				'name'            => 'owner_id',
	       					// 'role'			 => $role
	    				)); ?></td>
				</tr>

				<tr>
				<th scope="row"><label for="listing_id"><?php _e( 'Listing id', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="listing_id" value="<?php echo $booking[0]['listing_id'] ?>" class="regular-text"></td>
				</tr>

				<tr>
				<th scope="row"><label for="date_start"><?php _e( 'Date start', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="date_start" value="<?php echo $booking[0]['date_start'] ?>" class="regular-text"></td>
				</tr>

				<tr>
				<th scope="row"><label for="date_end"><?php _e( 'Date end', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="date_end" value="<?php echo $booking[0]['date_end'] ?>" class="regular-text"></td>
				</tr>

				<tr>
				<th scope="row"><label for="order_id"><?php _e( 'Order id', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="order_id" value="<?php echo $booking[0]['order_id'] ?>" class="regular-text"></td>
				</tr>

				<tr>
				<th scope="row"><label for="status"><?php _e( 'Status', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="status" value="<?php echo $booking[0]['status'] ?>" class="regular-text"></td>
				</tr>

				<tr>
				<th scope="row"><label for="created"><?php _e( 'Created date', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="created" value="<?php echo $booking[0]['created'] ?>" class="regular-text"></td>
				</tr>
				<tr>
				<th scope="row"><label for="type"><?php _e( 'Type', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="type" value="<?php echo $booking[0]['type'] ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="created"><?php _e( 'Details', 'listeo_core' );  ?></label></th>
					<td>
						<table>
						<?php   $details = json_decode($booking[0]['comment']); 
						foreach($details as $key => $value){?>
							
								
							<tr>
							<?php if(is_array($value)){ ?>
							<th scope="row"><label for="<?php echo $key  ?>"><?php echo $key  ?></label></th>
							<td><textarea name="comment[service]" id="" cols="50" rows="10"><?php echo json_encode($value);?></textarea></td>
							<?php } else { ?>
								<th scope="row"><label for="<?php echo $key  ?>"><?php echo $key  ?></label></th>
								<td><input type="text" name="comment[<?php echo $key; ?>]" value="<?php echo $value ?>" class="regular-text"></
							<?php }?>
							</tr>
							  
						<?php }
						?>
						</table>
					</td>
				</tr>
				<tr>
				<th scope="row"><label for="expiring"><?php _e( 'Expiring date', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="expiring" value="<?php echo $booking[0]['expiring'] ?>" class="regular-text"></td>
				</tr>

				<tr>
				<th scope="row"><label for="price"><?php _e( 'Price', 'listeo_core' );  ?></label></th>
				<td><input type="text" name="price" value="<?php echo $booking[0]['price'] ?>" class="regular-text"></td>
				</tr>

				</tbody></table>
   
			</div>
			<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php _e( 'Save', 'listeo_core' );  ?>"></p>
			</form>
			<?php

			exit();

		
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'Manage bookings',
			'Bookings',
			'manage_options',
			'listeo_bookings_manage',
			[ $this, 'plugin_settings_page' ]
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
	if ( 'add' === $action || 'edit' === $action ) {
			$this->add_package_page();
		} else {
				?>
		<div class="wrap">
			<h2><?php _e('Manage Bookings', 'listeo_core'); ?></h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-3">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable"> 
							<form method="GET">
        						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
								<?php
									if ( 'add' === $action || 'edit' === $action ) {
									$this->add_package_page();
								} else {
									$this->bookings_obj->prepare_items();
									$this->bookings_obj->display();
								} ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php }
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => __( 'Bookings per page', 'listeo_core'),
			'default' => 20,
			'option'  => 'per_page'
		];

		add_screen_option( $option, $args );

		$this->bookings_obj = new Bookings_Admin_List();

	}


	/** Singleton instance */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}


add_action( 'plugins_loaded', function () {

	Bookings_Admin_Plugin::get_instance();

} );