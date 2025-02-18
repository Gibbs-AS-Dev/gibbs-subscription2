<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listeo Core Widget base
 */
class Listeo_Core_Widget extends WP_Widget {
/**
	 * Widget CSS class
	 *
	 * @access public
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description
	 *
	 * @access public
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget id
	 *
	 * @access public
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name
	 *
	 * @access public
	 * @var string
	 */
	public $widget_name;

	/**
	 * Widget settings
	 *
	 * @access public
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->register();

	}


	/**
	 * Register Widget
	 */
	public function register() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		
	}

	

	/**
	 * get_cached_widget function.
	 */
	public function get_cached_widget( $args ) {
		
		return false;

		$cache = wp_cache_get( $this->widget_id, 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( $this->widget_id, $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 * @return [type]
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_id, 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {
				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;			
				case 'checkbox' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="checkbox" <?php checked( esc_attr( $value ), 'on' ); ?> />
					</p>
					<?php
				break;
				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'dropdown' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>	
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
	
						<?php foreach ($setting['options'] as $key => $option_value) { ?>
							<option <?php selected($value,$key); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($option_value); ?></option>	
						<?php } ?></select>
					
					</p>
					<?php
				break;
			}
		}
	}

	/**
	 * widget function.
	 *
	 * @see    WP_Widget
	 * @access public
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {}
}


/**
 * Featured listings Widget
 */
class Listeo_Core_Featured_Properties extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core widget_featured_listings';
		$this->widget_description = __( 'Display a list of featured listings on your site.', 'listeo_core' );
		$this->widget_id          = 'widget_featured_listings';
		$this->widget_name        =  __( 'Featured Properties', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Featured Properties', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Number of listings to show', 'listeo_core' )
			)
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		

		ob_start();

		extract( $args );

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number = absint( $instance['number'] );
		$listings   = new WP_Query( array(
			'posts_per_page' => $number,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_type' 	 => 'listing',
			'meta_query'     =>  array( 
				array(
					'key'     => '_featured',
					'value'   => 'on',
					'compare' => '=',
				),
				array('key' => '_thumbnail_id')
			)
		) );
	
		$template_loader = new Listeo_Core_Template_Loader;
		if ( $listings->have_posts() ) : ?>

			<?php echo $before_widget; ?>

			<?php if ( $title ) echo $before_title . $title . $after_title; ?>

			 <div class="widget-listing-slider dots-nav" data-slick='{"autoplay": true, "autoplaySpeed":3000}'>
				<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>
					<div class="fw-carousel-item">
                        <?php
                       //     $template_loader->get_template_part( 'content-listing-compact' );  
                            $template_loader->get_template_part( 'content-listing-grid' );  
                        ?>
                    </div>
				<?php endwhile; ?>
			</div>

			<?php echo $after_widget; ?>

		<?php else : ?>

			<?php $template_loader->get_template_part( 'listing-widget','no-content' ); ?>

		<?php endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Save & Print listings Widget
 */
class Listeo_Core_Bookmarks_Share_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core widget_buttons';
		$this->widget_description = __( 'Display a Bookmarks and share buttons.', 'listeo_core' );
		$this->widget_id          = 'widget_buttons_listings';
		$this->widget_name        =  __( 'Listeo Bookmarks & Share', 'listeo_core' );
		$this->settings           = array(
			'bookmarks' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Bookmark button', 'listeo_core' )
			),			
			'share' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Share buttons', 'listeo_core' )
			),
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		extract( $args );

		global $post;
		$share = (isset($instance['share'])) ? $instance['share'] : '' ; 
		$bookmarks = (isset($instance['bookmarks'])) ? $instance['bookmarks'] : '' ; 
		
		echo $before_widget; 
		
		?>
		<div class="listing-share margin-top-40 margin-bottom-40 no-border">

		<?php 
			if(!empty($bookmarks)):
			
				$nonce = wp_create_nonce("listeo_core_bookmark_this_nonce");
		
				$classObj = new Listeo_Core_Bookmarks;
				
				if( $classObj->check_if_added($post->ID) ) { ?>
					<button onclick="window.location.href='<?php echo get_permalink( get_option( 'listeo_bookmarks_page' ))?>'" class="like-button save liked" ><span class="like-icon liked"></span> <?php esc_html_e('Bookmarked','listeo_core') ?>
					</button> 
				<?php } else { 
					if(is_user_logged_in()){ ?>
						<button class="like-button listeo_core-bookmark-it"
							data-post_id="<?php echo esc_attr($post->ID); ?>" 
							data-confirm="<?php esc_html_e('Bookmarked!','listeo_core'); ?>"
							data-nonce="<?php echo esc_attr($nonce); ?>" 
							><span class="like-icon"></span> <?php esc_html_e('Bookmark this listing','listeo_core') ?>
						</button> 
						<?php } else { 
							$popup_login = get_option( 'listeo_popup_login','ajax' ); 
							if($popup_login == 'ajax') { ?>
								<button href="#sign-in-dialog" class="like-button-notlogged sign-in popup-with-zoom-anim"><span class="like-icon"></span> <?php esc_html_e('Login To Bookmark Items','listeo_core') ?></button> 
							<?php } else { 
								$login_page = get_option('listeo_profile_page'); ?>
								<a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="like-button-notlogged"><span class="like-icon"></span> <?php esc_html_e('Login To Bookmark Items','listeo_core') ?></a> 
							<?php } ?>		
					<?php } ?>
					
				<?php }

				$count = get_post_meta($post->ID, 'bookmarks_counter', true); 
				if ( $count ) : 
					if($count < 0) { $count = 0; } ?>
				<span id="bookmarks-counter"><?php printf( _n( '%s person bookmarked this place', '%s people bookmarked this place', $count, 'listeo_core' ), number_format_i18n( $count ) ); ?> </span>
				<?php endif; ?>
			<?php 
			endif;
			if(!empty($share)):  
					$id = $post->ID;
			        $title = urlencode($post->post_title);
			        $url =  urlencode( get_permalink($id) );
			        $summary = urlencode(listeo_string_limit_words($post->post_excerpt,20));
			        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'medium' );
			        if($thumb){
				        $imageurl = urlencode($thumb[0]);	
				    } else {
				    	$imageurl = false;
				    }
			        
			        ?>
			 		<ul class="share-buttons margin-bottom-0">
			          <li><?php echo '<a target="_blank" class="fb-share" href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '"><i class="fa-brands fa-facebook"></i> '.esc_html__('Share','listeo_core').'</a>'; ?></li>
			         <li><?php echo '<a target="_blank" class="twitter-share" href="https://twitter.com/share?url=' . $url . '&amp;text=' . esc_attr($summary ). '" title="' . __( 'Twitter', 'listeo_core' ) . '"><i class="fa fa-twitter"></i> Tweet</a>'; ?></li>
			        <li><?php echo '<a target="_blank"  class="pinterest-share" href="http://pinterest.com/pin/create/button/?url=' . $url . '&amp;description=' . esc_attr($summary) . '&media=' . esc_attr($imageurl) . '" onclick="window.open(this.href); return false;"><i class="fa fa-pinterest-p"></i> Pin It</a>'; ?></li>
			        </ul>
			
					<div class="clearfix"></div>
		
	 	<?php endif;
	 	?>
	 		</div>
	 	<?php
		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Featured listings Widget
 */
class Listeo_Core_Contact_Vendor_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core  boxed-widget message-vendor ';
		$this->widget_description = __( 'Display a Contact form.', 'listeo_core' );
		$this->widget_id          = 'widget_contact_widget_listeo';
		$this->widget_name        =  __( 'Listeo Contact Widget', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Message Vendor', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
				
			'contact' => array(
				'type'  => 'dropdown',
				'std'	=> '',
				'options' => $this->get_forms(),
				'label' => __( 'Choose contact form', 'listeo_core' )
			),			
		);
		$this->register();

		//add_filter( 'wpcf7_mail_components', array( $this, 'set_question_form_recipient' ), 10, 3 );

	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		
		global $post;
		$contact_enabled = get_post_meta( $post->ID, '_email_contact_widget', true );
		
		if( !$contact_enabled ) {
			return; 
		}

		ob_start();

		extract( $args );
	
		echo $before_widget; 
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		?>
		<h3><i class="fa fa-envelope"></i> <?php echo $title ?></h3>
		<div class="row with-forms  margin-top-0">
			<?php
			if(get_post($instance['contact'] )){
			  echo do_shortcode( sprintf( '[contact-form-7 id="%s"]', $instance['contact'] ) );
			} else {
				echo 'Please choose "Contact Owner Widget" form in Appearance  → Widgets  (Single Listing Sidebar  → Listeo Contact Widget)'; echo ' <a href="http://www.docs.purethemes.net/listeo/knowledge-base/how-to-configure-message-vendor-form/">More information.</a>';
			}?>
		</div>

		<!-- Agent Widget / End -->
		<?php
		
		 echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}

	public function get_forms() {
		$forms  = array( 0 => __( 'Please select a form', 'listeo_core' ) );

		$_forms = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'wpcf7_contact_form',
			)
		);

		if ( ! empty( $_forms ) ) {

			foreach ( $_forms as $_form ) {
				$forms[ $_form->ID ] = $_form->post_title;
			}
		}

		return $forms;
	}


}




/**
 * Save & Print listings Widget
 */
class Listeo_Core_Search_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core widget_buttons';
		$this->widget_description = __( 'Display a Advanced Search Form.', 'listeo_core' );
		$this->widget_id          = 'widget_search_form_listings';
		$this->widget_name        =  __( 'Listeo Search Form', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Find New Home', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			'action' => array(
				'type'  => 'dropdown',
				'std'	=> 'archive',
				'options' => array(
					'current_page' => __( 'Redirect to current page', 'listeo_core' ),
					'archive' => __( 'Redirect to listings archive page', 'listeo_core' ),
					),
				'label' => __( 'Choose form action', 'listeo_core' )
			),	
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}


		extract( $args );

		echo $before_widget; 
			$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			if(isset($instance['action'])){
				$action  = apply_filters( 'listeo_core_search_widget_action', $instance['action'], $instance, $this->id_base);	
			}
			
			
			if ( $title ) {
				echo $before_title . $title;
				//if(isset($_GET['keyword_search'])) : echo '<a id="listeo_core_reset_filters" href="#">'.esc_html__('Reset Filters','listeo_core').'</a>'; endif;
			 	echo $after_title; 
			}
			$dynamic =  (get_option('listeo_dynamic_features')=="on") ? "on" : "off";

			if(isset($action) && $action == 'archive') {
				echo do_shortcode('[listeo_search_form dynamic_filters="'.$dynamic.'" 	more_text_open="'.esc_html__('More Filters','listeo_core').'" more_text_close="'.esc_html__('Close Filters','listeo_core').'" ajax_browsing="false" action='.get_post_type_archive_link( 'listing' ).']');
			} else {
				echo do_shortcode('[listeo_search_form  dynamic_filters="'.$dynamic.'" more_text_close="'.esc_html__('Close Filters','listeo_core').'" more_text_open="'.esc_html__('More Filters','listeo_core').'"]');
			}

		echo $after_widget; 

		


	}
}


/**
 * Booking Widget
 */
class Listeo_Core_Booking_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		// create object responsible for bookings
		$this->bookings = new Listeo_Core_Bookings_Calendar;

		$this->widget_cssclass    = 'listeo_core boxed-widget booking-widget margin-bottom-35';
		$this->widget_description = __( 'Shows Booking Form.', 'listeo_core' );
		$this->widget_id          = 'widget_booking_listings';
		$this->widget_name        =  __( 'Listeo Booking Form', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Booking', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		


		$daytest;

        ob_start();

        extract($args);
        $title  = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
        $queried_object = get_queried_object();

        if ($queried_object) {
            $post_id = $queried_object->ID;
            $offer_type = get_post_meta($post_id, '_listing_type', true);
        }
        $_booking_status = get_post_meta($post_id, '_booking_status', true); {
            if (!$_booking_status) {
                return;
            }
        }
        echo $before_widget;
        if ($title) {
            echo $before_title . '<i class="fa fa-calendar-check-o "></i>' . $title . '<span style="top:5px;font-size:16px;position: relative;float:right;"><i class="fa fa-trash-o" aria-hidden="true" style="float:right;font-size:18px;margin-left:5px;"></i>Nullstill</span>' . $after_title;
        }

        $days_list = array(
            0    => __('Monday', 'listeo_core'),
            1     => __('Tuesday', 'listeo_core'),
            2    => __('Wednesday', 'listeo_core'),
            3     => __('Thursday', 'listeo_core'),
            4     => __('Friday', 'listeo_core'),
            5     => __('Saturday', 'listeo_core'),
            6     => __('Sunday', 'listeo_core'),
        );

        // get post meta and save slots to var
        $post_info = get_queried_object();

        $post_meta = get_post_meta($post_info->ID);

        // get slots and check if not empty

        if (isset($post_meta['_slots_status'][0]) && !empty($post_meta['_slots_status'][0])) {
            if (isset($post_meta['_slots'][0])) {
                $slots = json_decode($post_meta['_slots'][0]);
                if (strpos($post_meta['_slots'][0], '-') == false) $slots = false;
            } else {
                $slots = false;
            }
        } else {
            $slots = false;
        }
        // get opening hours
        if (isset($post_meta['_opening_hours'][0])) {
            $opening_hours = json_decode($post_meta['_opening_hours'][0], true);
        }

        if ($post_meta['_listing_type'][0] == 'rental' || $post_meta['_listing_type'][0] == 'service') {


            // get reservations for next 10 years to make unable to set it in datapicker
            if ($post_meta['_listing_type'][0] == 'rental') {
                $records = $this->bookings->get_bookings(date('Y-m-d H:i:s'),  date('Y-m-d H:i:s', strtotime('+3 years')), array('listing_id' => $post_info->ID, 'type' => 'reservation'));
            } else {

                $records = $this->bookings->get_bookings(
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s', strtotime('+3 years')),
                    array('listing_id' => $post_info->ID, 'type' => 'reservation'),
                    'booking_date',
                    $limit = '',
                    $offset = '',
                    'owner'
                );
            }


            // store start and end dates to display it in the widget
            $wpk_start_dates = array();
            $wpk_end_dates = array();
            if (!empty($records)) {
                foreach ($records as $record) {

                    if ($post_meta['_listing_type'][0] == 'rental') {
                        // when we have one day reservation
                        if ($record['date_start'] == $record['date_end']) {
                            $wpk_start_dates[] = date('Y-m-d', strtotime($record['date_start']));
                            $wpk_end_dates[] = date('Y-m-d', strtotime($record['date_start'] . ' + 1 day'));
                        } else {
                            /**
                             * Set the date_start and date_end dates and fill days in between as disabled
                             */
                            $wpk_start_dates[] = date('Y-m-d', strtotime($record['date_start']));
                            $wpk_end_dates[] = date('Y-m-d', strtotime($record['date_end']));

                            $period = new DatePeriod(
                                new DateTime(date('Y-m-d', strtotime($record['date_start'] . ' + 1 day'))),
                                new DateInterval('P1D'),
                                new DateTime(date('Y-m-d', strtotime($record['date_end']))) //. ' +1 day') ) )
                            );

                            foreach ($period as $day_number => $value) {
                                $disabled_dates[] = $value->format('Y-m-d');
                            }
                        }
                    } else {
                        // when we have one day reservation
                        if ($record['date_start'] == $record['date_end']) {
                            $disabled_dates[] = date('Y-m-d', strtotime($record['date_start']));
                        } else {

                            // if we have many dats reservations we have to add every date between this days
                            $period = new DatePeriod(
                                new DateTime(date('Y-m-d', strtotime($record['date_start']))),
                                new DateInterval('P1D'),
                                new DateTime(date('Y-m-d', strtotime($record['date_end'] . ' +1 day')))
                            );

                            foreach ($period as $day_number => $value) {
                                $disabled_dates[] = $value->format('Y-m-d');
                            }
                        }
                    }
                }
            }

            if (isset($wpk_start_dates)) {
        ?>
                <script>
                    var wpkStartDates = <?php echo json_encode($wpk_start_dates); ?>;
                    var wpkEndDates = <?php echo json_encode($wpk_end_dates); ?>;
                </script>
            <?php
            }
            if (isset($disabled_dates)) {
            ?>
                <script>
                    var disabledDates = <?php echo json_encode($disabled_dates); ?>;
                </script>
            <?php
            }
        } // end if rental/service


        if ($post_meta['_listing_type'][0] == 'event') {
            $max_tickets = (int) get_post_meta($post_info->ID, "_event_tickets", true);
            $sold_tickets = (int) get_post_meta($post_info->ID, "_event_tickets_sold", true);
            $av_tickets = $max_tickets - $sold_tickets;

            if ($av_tickets <= 0) { ?>
                <p id="sold-out"><?php esc_html_e('The tickets have sold out', 'listeo_core') ?></p>
                </div>
        <?php
                return;
            }
        }
        ?>

        <div class="row with-forms  margin-top-0" id="booking-widget-anchor">
            <form id="form-booking" data-post_id="<?php echo $post_info->ID; ?>" class="form-booking-<?php echo $post_meta['_listing_type'][0]; ?>" action="<?php echo esc_url(get_permalink(get_option('listeo_booking_confirmation_page'))); ?>" method="post">


                <?php if ($post_meta['_listing_type'][0] != 'event') {


                    $minspan = get_post_meta($post_info->ID, '_min_days', true); ?>
                    <?php
                    //WP Kraken
                    // If minimub booking days are not set, set to 2 by default
                    if (!$minspan && $post_meta['_listing_type'][0] == 'rental') {
                        $minspan = 2;
                    }
                    ?>
                    <!-- Date Range Picker - docs: http://www.daterangepicker.com/ -->
                    <div class="col-lg-12" style="display:none;">
                        <input type="text" data-minspan="<?php echo ($minspan) ? $minspan : '0'; ?>" id="date-picker" readonly="readonly" class="date-picker-listing-<?php echo esc_attr($post_meta['_listing_type'][0]); ?>" autocomplete="off" placeholder="<?php esc_attr_e('Date', 'listeo_core'); ?>" value="" listing_type="<?php echo $post_meta['_listing_type'][0]; ?>" />
                    </div>


                    <?php if ($post_meta['_listing_type'][0] == 'service' &&   is_array($slots)) { ?>
                        <div class="col-lg-12" style="display:none;">
                            <div class="panel-dropdown time-slots-dropdown">
                                <a href="#" placeholder="<?php esc_html_e('Time Slots', 'listeo_core') ?>"><?php esc_html_e('Time Slots', 'listeo_core') ?></a>

                                <div class="panel-dropdown-content padding-reset">
                                    <div class="no-slots-information"><?php esc_html_e('No slots for this day', 'listeo_core') ?></div>
                                    <div class="panel-dropdown-scrollable">
                                        <input id="slot" type="hidden" name="slot" value="" />
                                        <input id="listing_id" type="hidden" name="listing_id" value="<?php echo $post_info->ID; ?>" />
                                        <?php foreach ($slots as $day => $day_slots) {
                                            if (empty($day_slots)) continue;
                                        ?>
                                            <?php foreach ($day_slots as $number => $slot) {
                                                $slot = explode('|', $slot); ?>
                                                <!-- Time Slot -->
                                                <div class="time-slot" day="<?php echo $day; ?>">
                                                    <input type="radio" name="time-slot" id="<?php echo $day . '|' . $number; ?>" value="<?php echo $day . '|' . $number; ?>">
                                                    <label for="<?php echo $day . '|' . $number; ?>">
                                                        <p class="day"><?php echo $days_list[$day]; ?></p>
                                                        <div style="display: none;" class="tests" data="helo"><?php echo $slot[0]; ?></div>
                                                        <span style="display: none;"><?php echo $slot[1];
                                                                                        esc_html_e(' slots available', 'listeo_core') ?></span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-12" style="padding: 0; margin: 0 0 0 5px;">
                            <table class="tabela" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="firstArrow"><input style="background: #F9F9F9; color:black; width: 35px;height: 35px;margin: auto;padding: inherit;" value="<" type='button' class="previousbtn"></th>
                                        <th></th>
                                        <th colspan="2">
                                            <div class="col-md-12" style="padding: 0px;">
                                                <select id="getMonthWidget" style="background: #F9F9F9;float:right; text-align-last: center;margin: 0;height: 30px;font-size: small;border:0;padding: 1px 15px 1px 0px;appearance: none;">
                                                    <option value='1'>Januar</option>
                                                    <option value='2'>Februar</option>
                                                    <option value='3'>Mars</option>
                                                    <option value='4'>April</option>
                                                    <option value='5'>Mai</option>
                                                    <option value='6'>Juni</option>
                                                    <option value='7'>Juli</option>
                                                    <option value='8'>August</option>
                                                    <option value='9'>September</option>
                                                    <option value='10'>Oktober</option>
                                                    <option value='11'>November</option>
                                                    <option value='12'>Desember</option>
                                                </select><i style="position: absolute; right:  5%; top: 1em; font-size: x-small;" class="fa fa-chevron-down" aria-hidden="true"></i>
                                            </div>
                                        </th>
                                        <th colspan="2">

                                            <!--                                    <div class="col-lg-12 months-dropdown" style="padding:0px;">-->
                                            <!--                                        <div class="panel-dropdown booking-services">-->
                                            <!--                                            <a href="#">Januar </a>-->
                                            <!--                                            <div class="panel-dropdown-content padding-reset" style="width: 100px;">-->
                                            <!--                                                <div class="panel-dropdown-scrollable">-->
                                            <!--                                                    <div class="bookable-services">-->
                                            <!--                                                        <option value='1'>Januar</option>-->
                                            <!--                                                        <option value='2'>Februar</option>-->
                                            <!--                                                        <option value='3'>Mars</option>-->
                                            <!--                                                        <option value='4'>April</option>-->
                                            <!--                                                        <option value='5'>Mai</option>-->
                                            <!--                                                        <option value='6'>Juni</option>-->
                                            <!--                                                        <option value='7'>Juli</option>-->
                                            <!--                                                        <option value='8'>August</option>-->
                                            <!--                                                        <option value='9'>September</option>-->
                                            <!--                                                        <option value='10'>Oktober</option>-->
                                            <!--                                                        <option value='11'>November</option>-->
                                            <!--                                                        <option value='12'>Desember</option>-->
                                            <!--                                                    </div>-->
                                            <!--                                                </div>-->
                                            <!--                                            </div>-->
                                            <!--                                        </div>-->
                                            <!--                                    </div>-->
                                            <div class="col-md-12" style="padding: 0px;">
                                                <select id="getYearWidget" style="background: #F9F9F9; float:right; text-align-last: center;margin: 0;height: 30px;font-size: small;border:0;padding: 1px 15px 1px 0px;appearance: none;"></select>
                                                <i style="position: absolute; right: 5%; top: 1em; font-size: x-small;" class="fa fa-chevron-down" aria-hidden="true"></i>
                                            </div>
                                        </th>

                                        <th></th>
                                        <th class="secondArrow"><input style="background: #F9F9F9; color:black; width: 35px;height: 35px;margin: auto;padding: inherit;" value=">" type="button" class="btn btn-warning nextbtn"></th>
                                    </tr>
                                    <tr style="font-size:10px">
                                        <th style="padding-left:13px;">UKE</th>
                                        <th id="0">MAN</th>
                                        <th id="1">TIR</th>
                                        <th id="2">ONS</th>
                                        <th id="3">TOR</th>
                                        <th id="4">FRE</th>
                                        <th id="5">LØR</th>
                                        <th id="6">SØN</th>
                                    </tr>
                                    <tr style="font-size:12px">
                                        <th id="monthOver" style="padding-left:13px;color:black; padding-bottom: 9px;"></th>
                                        <th class="halfborderright halfborderleft" style="padding-bottom: 9px;"><span id="dateOverHours0"></span></th>
                                        <th class="halfborderright halfborderleft" style="padding-bottom: 9px;"><span id="dateOverHours1"></span></th>
                                        <th class="halfborderright halfborderleft" style="padding-bottom: 9px;"><span id="dateOverHours2"></span></th>
                                        <th class="halfborderright halfborderleft" style="padding-bottom: 9px;"><span id="dateOverHours3"></span></th>
                                        <th class="halfborderright halfborderleft" style="padding-bottom: 9px;"><span id="dateOverHours4"></span></th>
                                        <th class="halfborderright halfborderleft" style="padding-bottom: 9px;"><span id="dateOverHours5"></span></th>
                                        <th class="halfborderright halfborderleft" style="padding-bottom: 9px;"><span id="dateOverHours6"></span></th>
                                    </tr>
                                </thead>
                                <span class="timer-loader" style="position: relative; top: 200px; left: 138px; Z-INDEX: 10;">Loading&#8230;</span>
                                <tbody id="time-slot-table" class="<?php if (!is_user_logged_in()) {echo 'xoo-el-login-tgr';} ?>">
                                    <?php
                                    $saat = 0;
                                    $daysArray = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
                                    for ($i = 0; $i < 24; $i++) {
                                    ?><tr class="<?php echo $i; ?>">
                                            <?php if ($i < 10) {
                                            ?>
                                                <th class='halfright'><span class='halfright1'>0<?php echo $i; ?>:00</span></th>
                                            <?php
                                            } else { ?>
                                                <th class='halfright'><span class='halfright1' <?php if ($i == 23) : ?> style="left: 26px;" <?php endif; ?>> <?php echo $i; ?>:00</span></th>
                                            <?php
                                            }
                                            for ($j = 0; $j < 7; $j++) {
                                            ?>
                                                <td class="<?php echo $j . ' ' . $i . $daysArray[$j] ?>"></td>


                                            <?php
                                            }
                                            ?>
                                        </tr><?php
                                            }
                                                ?>
                                    <span id="divtoshow" class="notification notice" style="text-align:center;width: 100px;position:absolute; display:none;padding:5px;font-size:12px;z-index: 10;">Velg slutt-tid!</span>
                                </tbody>
                            </table>
                            <div class="col-lg-12 notification notice notifitest" style="font-size:13.5px;text-align:center;margin: 15px 0 0 0;">
                                Trykk på ledig tid for å gå videre!
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 poraka" style="display:none; text-align: center; margin: 10px 0 0 0; padding: 10px; font-size: 15px; background: #ffebeb;color:#d83838"><span></span></div>
                        <div class="row col-lg-12" style="margin: 15px 0px 10px 16px;color: black;font-size:14px;">
                            <div class="row">
                                <div class="col-xs-4 col-md-4" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #1D9781;border-radius: 50%;display: inline-block;"></span><span> Valgt tid (ink. mva)</span></div>
                                <div class="col-xs-4 col-md-4" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #DA697A;border-radius: 50%;display: inline-block;"></span><span> Booket</span></div>
                                <?php  $autobook = get_post_meta(get_the_ID(),'_instant_booking',true); 
                           if(!$autobook == 'on'){
                           ?>
                                <div class="col-xs-4 col-md-4" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #FF9900;border-radius: 50%;display: inline-block;"></span><span> Reservert</span></div>
                           <?php } ?>
                                <?php  $autobook = get_post_meta(get_the_ID(),'_instant_booking',true); 
                                 if(!$autobook == 'on'){
                           ?>
                                <div class="col-xs-4 col-md-4" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #FF9900;border-radius: 50%;display: inline-block;"></span><span> Reservert</span></div>
                           <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col-xs-4 col-md-4" style="padding: 0;"><span style="border: black 1px solid;height: 11px;width: 11px;background-color: #FFFFFF;border-radius: 50%;display: inline-block;"></span><span> Ledig</span></div>
                                <div class="col-xs-6 col-md-4" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #C1C1C1;border-radius: 50%;display: inline-block;"></span><span> Ikke tilgjengelig</span></div>
                            </div>
                        </div>

                        <div class="row fratil" style="margin: 10px 0px 0px 0; font-size: 10px;">
                            <div class="col-xs-5" style="padding-left: 16px;">FRA</div>
                            <div class="col-xs-2"></div>
                            <div class="col-xs-5" style="padding:0">TIL</div>
                        </div>
                        <div class="row timeSpan" style="display:none;margin: 0px 10px 0px 0px;text-align: center;">
                            <div class="col-xs-5" style="padding: 0px;">
                                <input id="timeSpanFrom" style="text-align:center;pointer-events:none;margin-left: 10px;font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: source sans pro; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;">
                            </div>
                            <div class="col-xs-2"></div>
                            <div class="col-xs-5" style="padding: 0px;">
                                <input id="timeSpanTo" style="text-align:center;pointer-events:none;font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: source sans pro; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;">
                            </div>
                        </div>
                        <div class="row timenotifi" style="margin: 0px 10px 0px 0px;text-align: center;">
                            <div class="col-xs-5" style="padding: 0px;">
                                <select style="margin-left: 10px;font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: source sans pro; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;" name="fromH" id="fromHours">
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                </select>
                            </div>
                            <div class="col-xs-2"></div>
                            <div class="col-xs-5" style="padding: 0px;">
                                <select style="font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: source sans pro; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;" name="toH" id="toHours">
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <script type="text/javascript">
                            //get Week function

                            Date.prototype.getWeek = function() {
                                var date = new Date(this.getTime());
                                date.setHours(0, 0, 0, 0);
                                date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
                                var week1 = new Date(date.getFullYear(), 0, 4);
                                return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 -
                                    3 + (week1.getDay() + 6) % 7) / 7);
                            }

                            function startOfWeek(date) {
                                var diff = date.getDate() - date.getDay() + (date.getDay() === 0 ? -6 : 1);
                                return new Date(date.setDate(diff));
                            }

                            function endOfWeek(date) {
                                var lastday = date.getDate() - (date.getDay() - 1) + 6;
                                return new Date(date.setDate(lastday));
                            }

                            function loading(seconds) {
                                jQuery('.timer-loader').show();
                                jQuery('.tabela').css('opacity', '0.1');
                                setTimeout(function() {
                                    jQuery('.timer-loader').hide();
                                    jQuery('.tabela').css('opacity', '1');
                                }, seconds);
                            }

                            let listingCategory = '<?php echo get_post_meta($post_id, '_category', true); ?>';
                            window.mobileCheck = function() {
                                let check = false;
                                (function(a) {
                                    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
                                })(navigator.userAgent || navigator.vendor || window.opera);
                                return check;
                            };

                            <?php
                            global $wpdb;
                            $id = $post_info->ID;
                            $_currDate = date("m/d/Y");
                            $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `listing_id` = '$id'");
                            $unavailableResults = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "r` WHERE `listing_id` = '$id' AND `status` = 'unavailable'");
                            $waiting = array();
                            $approved = array();
                            $rejected = array();
                            $_currDate = date("m/d/Y");
                            foreach ($results as $item) {
                                if ($_currDate < $item->date_start) {
                                    $start = date_format(date_create($item->date_start), "m/d/Y");
                                    $end = date_format(date_create($item->date_end), "m/d/Y");
                                    $stHour = date_format(date_create($item->date_start), "H");
                                    $enHour = date_format(date_create($item->date_end), "H");

                                    if ($item->status == 'waiting' || $item->status == 'attention') {
                                        $waiting[] = "{$start}|{$end}|{$stHour}|{$enHour}";
                                    } elseif ($item->status == 'confirmed' || $item->status == 'paid') {
                                        $approved[] = "{$start}|{$end}|{$stHour}|{$enHour}";
                                    } else {
                                        $rejected[] = "{$start}|{$end}|{$stHour}|{$enHour}";
                                    }
                                }
                            }
                            $waitingLength = count($waiting);
                            $approvedLength = count($approved);
                            $rejectedLength = count($rejected);

                            $unavailable = array();
                            foreach ($unavailableResults as $item) {
                                $date_startconverted = date($item->date_start);
                                if ($_currDate < $date_startconverted) {
                                    $unavailable[] =  "{$item->date_start}|{$item->date_end}|{$item->hour_start}|{$item->hour_end}";
                                }
                            }
                            $unavailableLength = count($unavailable);
                            ?>

                            var month = new Array();
                            month[0] = "Jan";
                            month[1] = "Feb";
                            month[2] = "Mar";
                            month[3] = "Apr";
                            month[4] = "Mai";
                            month[5] = "Jun";
                            month[6] = "Jul";
                            month[7] = "Aug";
                            month[8] = "Sep";
                            month[9] = "Okt";
                            month[10] = "Nov";
                            month[11] = "Des";

                            let waitingLength = '<?php echo $waitingLength; ?>';
                            let waiting = '<?php echo json_encode($waiting); ?>';
                            let approvedLength = '<?php echo $approvedLength; ?>';
                            let approved = '<?php echo json_encode($approved); ?>';
                            let rejectedLength = '<?php echo $rejectedLength; ?>';
                            let rejected = '<?php echo json_encode($rejected); ?>';
                            let unavailableLength = '<?php echo $unavailableLength; ?>';
                            let unavailable = '<?php echo json_encode($unavailable); ?>';

                            waiting = waiting.slice(0, -1);
                            waiting = waiting.substr(1);
                            waiting = waiting.split(",");

                            approved = approved.slice(0, -1);
                            approved = approved.substr(1);
                            approved = approved.split(",");

                            rejected = rejected.slice(0, -1);
                            rejected = rejected.substr(1);
                            rejected = rejected.split(",");

                            unavailable = unavailable.slice(0, -1);
                            unavailable = unavailable.substr(1);
                            unavailable = unavailable.split(",");

                            jQuery('.timer-loader').show();
                            jQuery('.tabela').css('opacity', '0.1');
                            var cou = 0;
                            var firstinput;
                            var secondinput;
                            var v = jQuery('.btn1').val();
                            var ifSunday = new Date();

                            window.setTimeout(function() {
                                jQuery('#slot').val(v);
                                jQuery('.timer-loader').hide();
                                jQuery('.tabela').css('opacity', '1');
                                goToNextWeek = false;
                                goToNextWeek2 = false;
                                if (ifSunday.getDay() == 0) {
                                    window.setTimeout(function() {
                                        jQuery('.tabela .nextbtn').click();
                                        var goToNextWeek = false;
                                        var goToNextWeek2 = false;
                                    }, 2000)
                                }
                            }, 3000);

                            var goToNextWeek = false;
                            var goToNextWeek2 = false;

                            var day;
                            let dayArray = [];
                            var time;
                            var availableSlots;
                            var timeFrom;
                            var timeTo;
                            let days = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
                            let timeFromAr = [];
                            let timeToAr = [];

                            <?php foreach ($slots as $day => $day_slots) {
                                if (empty($day_slots)) continue;
                            ?>
                                <?php foreach ($day_slots as $number => $slot) {
                                    $slot = explode('|', $slot); ?>
                                    day = "<?php echo $day; ?>";
                                    dayArray.push(day);
                                    time = "<?php echo $slot[0]; ?>";
                                    availableSlots = "<?php echo $slot[1];
                                                        esc_html_e(' slots available', 'listeo_core') ?>";
                                    timeFrom = time.substring(0, 2);
                                    timeTo = time.substring(time.indexOf("-") + 2);
                                    timeTo = timeTo.substring(0, timeTo.indexOf(":"));

                            

                                    var tf = parseInt(timeFrom);
                                    if (tf < 10) {
                                        timeFrom = time.substring(1, 2);
                                    } else {
                                        timeFrom = time.substring(0, 2);
                                    }

                                    var limit = parseInt(timeTo);
                                    timeFromAr.push(timeFrom);
                                    if (limit == 0) {
                                        limit = 23;
                                    }
                                    timeToAr.push(limit);

                                    for (var i = timeFrom; i <= limit; i++) {
                                        if (jQuery(`.tabela .${day}`).hasClass(i + days[day])) {
                                            jQuery(`.tabela .${i}${days[day]}`).addClass('available');
                                            jQuery(`.tabela .${i}${days[day]}`).css('background', 'white');
                                        }
                                    }

                                <?php } ?>
                            <?php } ?>

                            var smallest = Math.min.apply(null, timeFromAr);
                            var biggest = Math.max.apply(null, timeToAr);

                            for (var s = 0; s < smallest; s++) {
                                jQuery(`.tabela tr.${s}`).hide();
                            }
                            for (var s = biggest + 1; s <= 24; s++) {
                                jQuery(`.tabela tr.${s}`).hide();
                            }

                            //append last hour
                            if (biggest == 24) {
                                jQuery(`.tabela .${biggest-1} .halfright`).append(`<span class="halfbottom">24:00</span>`);
                            } else if (biggest > 9) {
                                jQuery(`.tabela .${biggest} .halfright`).append(`<span class="halfbottom" >${biggest+1}:00</span>`);

                            } else {
                                jQuery(`.tabela .${biggest} .halfright`).append(`<span class="halfbottom" >0${biggest+1}:00</span>`);
                            }
                            //css for 2nd last
                            if (biggest < 23) {
                                jQuery(`.tabela .${biggest} .halfright .halfright1`).css('left', '20.5px');
                            }

                            let indexesAr = [];
                            var as = 0;
                            for (var i = smallest; i <= biggest; i++) {
                                indexesAr[i] = as;
                                as++;
                            }

                            var dateOver = new Date();
                            jQuery('.tabela #monthOver').html(`${dateOver.getWeek()}`);

                            var startDateOver = new Date();
                            startDateOver = startOfWeek(startDateOver);
                            var endDateOver = new Date();
                            endDateOver = endOfWeek(endDateOver);

                            // DISPLAY MONTH
                            // jQuery('#displayMonth').attr('colspan','4');
                            // jQuery('#displayMonth').html(`${startDateOver.getDate()}. ${month[startDateOver.getMonth()]} - ${endDateOver.getDate()}. ${month[endDateOver.getMonth()]}`);
                            // jQuery('#displayMonth').css("text-align","center");

                            var a = startOfWeek(dateOver);
                            for (let z = 0; z < 7; z++) {
                                jQuery(`.tabela #dateOverHours${z}`).html(`${a.getDate()}`);
                                jQuery(`.tabela #dateOverHours${z}`).attr('over-date', `${a}`);
                                a = new Date(a.setDate(a.getDate() + 1));
                            }

                            var curr = new Date; // get current date
                            var asdf = new Date();
                            var first = curr.getDate() - curr.getDay() + 1; // First day is the day of the month - the day of the week
                            var last = first + 6; // last day is the first day + 6
                            var firstday = new Date(curr.setDate(first)).toUTCString();
                            var lastday = new Date(curr.setDate(last)).toUTCString();
                            var a;
                            var additionalSlot;
                            var notG;
                            for (var g = 0; g < timeFromAr.length; g++) {
                                notG = parseInt(dayArray[g]);
                                var slot = jQuery(`.time-slot:eq(${g})`);
                                if (notG == 6) {
                                    asdf = new Date();
                                }
                                let xxx = new Date(jQuery('.tabela #dateOverHours0').attr('over-date'));
                                firstday = new Date(xxx.setDate(xxx.getDate() + notG));
                                const f2 = "MM/DD/YYYY";
                                firstday = moment(firstday).format(f2);
                                for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {

                                    a = slot.clone();
                                    additionalSlot = slot.clone();
                                    a.find(".tests").parent().attr("for", `${g}|${i}`);
                                    a.find(".tests").addClass(`${i}:00`);
                                    a.find(".tests").html(`${i}:00`);
                                    a.find("input").attr("id", `${g}|${i}`);
                                    a.find(".tests").attr("date", `${firstday}`);
                                    a.appendTo(`.tabela .${i}${days[notG]}`);
                                }
                            }
                            additionalSlot.appendTo('.additionalSlot');
                            additionalSlot.find(".tests").addClass(`11:00`);
                            additionalSlot.find(".tests").html(`11:00`);
                            additionalSlot.find('.tests').attr("date", "08/12/2022");

                            var currentDate = new Date();
                            var getD = currentDate.getDay();


                            for (var b = 0; b < 7; b++) {
                                for (var i = 0; i < 24; i++) {
                                    jQuery(`.tabela .${b}.${i}${days[b]} .tests`).filter(function() {
                                        if (jQuery(this).text() === `${i}:00`) {
                                            var atrdate = new Date(jQuery(this).attr('date'));
                                            if (atrdate < currentDate) {

                                                   var cur_date = "<?php echo date('m/d/Y');?>";
                                                   
                                                    if(jQuery(this).attr('date') == cur_date){
                                                        var cur_time = parseInt(currentDate.getHours());
                                                        var slot_time = parseInt(`${i}`);
                                                        if(cur_time > slot_time){

                                                            jQuery(this).parent().css("pointer-events", 'none');
                                                            jQuery(this).parent().css("opacity", '0');
                                                            jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                            jQuery(this).parent().parent().parent().css("border-bottom-color", '#9BA1A3');

                                                        }else{
                                                            jQuery(this).parent().css("opacity", '1');
                                                        }
                                                        
                                                        
                                                    }else{
                                                        jQuery(this).parent().css("pointer-events", 'none');
                                                        jQuery(this).parent().css("opacity", '0');
                                                        jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", '#9BA1A3');
                                                    }    

                                            } else {
                                                jQuery(this).parent().css("opacity", '1');
                                            }
                                        }
                                    });
                                }
                            }

                            //Here start new task for slots
                            var supportDate = new Date();

                            switch (supportDate.getDay()) {
                                case 0:
                                    supportDate = new Date(supportDate.setDate(supportDate.getDate() - 6));
                                    break;
                                case 1:
                                    supportDate = new Date(supportDate.setDate(supportDate.getDate()));
                                    break;
                                case 2:
                                    supportDate = new Date(supportDate.setDate(supportDate.getDate() - 1));
                                    break;
                                case 3:
                                    supportDate = new Date(supportDate.setDate(supportDate.getDate() - 2));
                                    break;
                                case 4:
                                    supportDate = new Date(supportDate.setDate(supportDate.getDate() - 3));
                                    break;
                                case 5:
                                    supportDate = new Date(supportDate.setDate(supportDate.getDate() - 4));
                                    break;
                                case 6:
                                    supportDate = new Date(supportDate.setDate(supportDate.getDate() - 5));
                                    break;
                            }


                            const f2 = "MM/DD/YYYY";
                            formatirana = null;
                            let nextDate = new Date();
                            var asdf = new Date();
                            jQuery('.tabela .nextbtn').click(function() {
                                loading(500);
                                jQuery('#divtoshow').hide();
                                jQuery('.tabela .available .time-slot label').each(function() {
                                    var a = jQuery(this).css("background-color");
                                    if (a == 'rgb(0, 132, 116)') {
                                        jQuery('.timenotifi').show();
                                        jQuery('.timeSpan').hide();
                                        jQuery('.fratil').show();
                                    } else {
                                        if (jQuery('.endDate').length > 0) {
                                            jQuery('.timenotifi').hide();
                                            if (jQuery('.bsf-left h4').is(':visible')) {
                                                jQuery('.timeSpan').hide();
                                            } else {
                                                jQuery('.timeSpan').show();
                                            }
                                        }
                                        jQuery('.fratil').hide();
                                    }
                                });
                                if (goToNextWeek) {
                                    goToNextWeek2 = true;
                                }
                                goToNextWeek = false;
                                switch (nextDate.getDay()) {
                                    case 0:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() + 1));
                                        break;
                                    case 1:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() + 7));
                                        break;
                                    case 2:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() + 6));
                                        break;
                                    case 3:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() + 5));
                                        break;
                                    case 4:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() + 4));
                                        break;
                                    case 5:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() + 3));
                                        break;
                                    case 6:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() + 2));
                                        break;
                                }

                                let selectedYear = nextDate.getFullYear();
                                let selectedMonth = nextDate.getMonth();
                                jQuery(`.tabela #getMonthWidget option:eq(${selectedMonth})`).prop("selected", true);
                                jQuery(`.tabela #getYearWidget option:contains(${selectedYear})`).prop("selected", true);

                                jQuery('.tabela #monthOver').html(`${nextDate.getWeek()}`);

                                var overHourDate = nextDate;
                                for (let z = 0; z < 7; z++) {
                                    jQuery(`.tabela #dateOverHours${z}`).html(`${overHourDate.getDate()}`);
                                    jQuery(`.tabela #dateOverHours${z}`).attr('over-date', `${overHourDate}`);
                                    overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() + 1));
                                }

                                let slotIte = 1;
                                let slot = null;
                                let trId = 3;
                                let next = new Date(nextDate.setDate(nextDate.getDate() - 1));

                                var asdedas = nextDate;
                                var curr = nextDate; // get current date
                                var a;
                                var notG;
                                for (var g = 0; g < timeFromAr.length; g++) {
                                    notG = parseInt(dayArray[g]);
                                    firstday = new Date(asdedas.setDate(curr.getDate() + notG));
                                    const f2 = "MM/DD/YYYY";
                                    firstday = moment(firstday).format(f2);
                                    for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                                        var slot1 = jQuery(`.${g}.${i}${days[g]} .tests`);
                                        a = slot1;
                                        a.attr("date", `${firstday}`);
                                    }
                                    asdedas.setDate(curr.getDate() - notG);
                                }


                                jQuery('.tabela .time-slot label').css('background', 'white');
                                jQuery('.tabela .time-slot label').css('pointer-events', '');
                                jQuery('.tabela .time-slot label').removeClass('booked');
                                jQuery('table.tabela tr td.available').css("background", 'white');
                                jQuery('table.tabela tr td.available').css("border-bottom-color", '#C0C3C3');
                                jQuery('.equipment-availability-table thead th.nextArrowEquipment').click();

                                if (listingCategory != 'utstr') {
                                    for (var i = 0; i < waitingLength; i++) {
                                        var startDate = waiting[i][0];
                                        var endDate = waiting[i][1];
                                        var startHour = waiting[i][2];
                                        var endHour = waiting[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
                                        }
                                    }
                                    for (var i = 0; i < approvedLength; i++) {
                                        var startDate = approved[i][0];
                                        var endDate = approved[i][1];
                                        var startHour = approved[i][2];
                                        var endHour = approved[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
                                        }
                                    }

                                    for (var i = 0; i < unavailableLength; i++) {
                                        var startDate = unavailable[i][0];
                                        var endDate = unavailable[i][1];
                                        var startHour = unavailable[i][2];
                                        var endHour = unavailable[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                        }
                                    }

                                }

                                var currentDate = new Date();
                                var getD = currentDate.getDay();

                                for (var b = 0; b < 7; b++) {
                                    for (var i = 0; i < 24; i++) {
                                        jQuery(`.tabela .${b}.${i}${days[b]} .tests`).filter(function() {
                                            if (jQuery(this).text() === `${i}:00`) {
                                                var atrdate = new Date(jQuery(this).attr('date'));
                                                if (atrdate < currentDate) {
                                                    var cur_date = "<?php echo date('m/d/Y');?>";
                                                    if(jQuery(this).attr('date') == cur_date){
                                                        var cur_time = parseInt(currentDate.getHours());
                                                        var slot_time = parseInt(`${i}`);
                                                        if(cur_time > slot_time){

                                                            jQuery(this).parent().css("pointer-events", 'none');
                                                            jQuery(this).parent().css("opacity", '0');
                                                            jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                            jQuery(this).parent().parent().parent().css("border-bottom-color", '#9BA1A3');

                                                        }else{
                                                            jQuery(this).parent().css("opacity", '1');
                                                        }
                                                    }else{
                                                        jQuery(this).parent().css("pointer-events", 'none');
                                                        jQuery(this).parent().css("opacity", '0');
                                                        jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", '#9BA1A3');
                                                    }    
                                                } else {
                                                    jQuery(this).parent().css("opacity", '1');
                                                }
                                            }
                                        });
                                    }
                                }


                                // //DISPLAY MONTHS
                                // var _nextDate = nextDate;
                                // var _startDateOver = startOfWeek(_nextDate);
                                // var _endDateOver = endOfWeek(_nextDate);
                                //
                                // jQuery('#displayMonth').attr('colspan','4');
                                // jQuery('#displayMonth').html(`${_startDateOver.getDate()}. ${month[_startDateOver.getMonth()]} - ${_endDateOver.getDate()}. ${month[_endDateOver.getMonth()]}`);
                                // jQuery('#displayMonth').css("text-align","center");
                                // //END

                                //White after bgColor green
                                jQuery('.tabela .available .time-slot label').each(function() {
                                    var a = jQuery(this).css("background-color");
                                    if (a == 'rgb(0, 132, 116)') {
                                        jQuery(this).parent().parent().css('background', `white`);
                                        jQuery(this).parent().parent().css('border-bottom-color', `#C0C3C3`);
                                    }
                                });

                                setTimeout(() => {
                                    jQuery('.tabela .available .time-slot label').each(function() {
                                        var a = jQuery(this).css("background-color");
                                        if (a == 'rgb(255, 153, 0)') {
                                            jQuery(this).parent().parent().css('background', `#FF9900`);
                                            jQuery(this).parent().parent().css('border-bottom-color', `#FF9900`);
                                        }
                                        if (a == 'rgb(218, 105, 122)') {
                                            jQuery(this).parent().parent().css('background', `#da697a`);
                                            jQuery(this).parent().parent().css('border-bottom-color', `#da697a`);
                                        }
                                    });
                                }, 500);

                            });

                            jQuery('.tabela .previousbtn').click(function() {
                                loading(500);
                                jQuery('#divtoshow').hide();
                                jQuery('.tabela .available .time-slot label').each(function() {
                                    var a = jQuery(this).css("background-color");
                                    if (a == 'rgb(0, 132, 116)') {
                                        jQuery('.timenotifi').show();
                                        jQuery('.timeSpan').hide();
                                        jQuery('.fratil').show();
                                    } else {
                                        if (jQuery('.endDate').length > 0) {
                                            jQuery('.timenotifi').hide();
                                            if (jQuery('.bsf-left h4').is(':visible')) {
                                                jQuery('.timeSpan').hide();
                                            } else {
                                                jQuery('.timeSpan').show();
                                            }
                                        }
                                        jQuery('.fratil').hide();
                                    }
                                });
                                goToNextWeek2 = false;
                                switch (nextDate.getDay()) {
                                    case 0:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() - 7));
                                        break;
                                    case 1:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() - 1));
                                        break;
                                    case 2:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() - 2));
                                        break;
                                    case 3:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() - 3));
                                        break;
                                    case 4:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() - 4));
                                        break;
                                    case 5:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() - 5));
                                        break;
                                    case 6:
                                        nextDate = new Date(nextDate.setDate(nextDate.getDate() - 6));
                                        break;
                                }

                                let selectedYear = nextDate.getFullYear();
                                let selectedMonth = nextDate.getMonth();
                                jQuery(`.tabela #getMonthWidget option:eq(${selectedMonth})`).prop("selected", true);
                                jQuery(`.tabela #getYearWidget option:contains(${selectedYear})`).prop("selected", true);

                                jQuery('.tabela #monthOver').html(`${nextDate.getWeek()}`);

                                let overHourDate = nextDate;
                                for (let z = 6; z > -1; z--) {
                                    jQuery(`.tabela #dateOverHours${z}`).html(`${overHourDate.getDate()}`);
                                    overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() - 1));
                                }

                                let slotIte = 1;
                                let slot = null;
                                let trId = 3;
                                let next = new Date(nextDate.setDate(nextDate.getDate() + 1));

                                var asdedas = nextDate;
                                var curr = nextDate; // get current date
                                startOfWeek(nextDate);
                                var a;
                                var notG;
                                for (var g = 0; g < timeFromAr.length; g++) {
                                    notG = parseInt(dayArray[g]);
                                    firstday = new Date(asdedas.setDate(curr.getDate() + notG));
                                    const f2 = "MM/DD/YYYY";
                                    firstday = moment(firstday).format(f2);
                                    for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                                        var slot1 = jQuery(`.tabela .${g}.${i}${days[g]} .tests`);
                                        a = slot1;
                                        a.attr("date", `${firstday}`);
                                    }
                                    asdedas.setDate(curr.getDate() - notG);
                                }

                                jQuery('.tabela .time-slot label').css('background', 'white');
                                jQuery('.tabela .time-slot label').css('pointer-events', '');
                                jQuery('.tabela .time-slot label').removeClass('booked');
                                jQuery('.equipment-availability-table thead th.prevArrowEquipment').click();

                                if (listingCategory != 'utstr') {
                                    for (var i = 0; i < waitingLength; i++) {

                                        var startDate = waiting[i][0];
                                        var endDate = waiting[i][1];
                                        var startHour = waiting[i][2];
                                        var endHour = waiting[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
                                        }
                                    }
                                    for (var i = 0; i < approvedLength; i++) {

                                        var startDate = approved[i][0];
                                        var endDate = approved[i][1];
                                        var startHour = approved[i][2];
                                        var endHour = approved[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
                                        }
                                    }

                                    for (var i = 0; i < unavailableLength; i++) {
                                        var startDate = unavailable[i][0];
                                        var endDate = unavailable[i][1];
                                        var startHour = unavailable[i][2];
                                        var endHour = unavailable[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                        }
                                    }
                                }

                                var currentDate = new Date();
                                jQuery('.tabela .available .time-slot label').each(function() {
                                    var a = jQuery(this).css("background-color");
                                    if (a == 'rgb(0, 132, 116)') {
                                        jQuery(this).parent().parent().css('background', `white`);
                                        jQuery(this).parent().parent().css('border-bottom-color', `#C0C3C3`);
                                    }
                                });

                                for (var b = 0; b < 7; b++) {
                                    for (var i = 0; i < 24; i++) {
                                        jQuery(`.tabela .${b}.${i}${days[b]} .tests`).filter(function() {
                                            if (jQuery(this).text() === `${i}:00`) {
                                                var atrdate = new Date(jQuery(this).attr('date'));
                                                if (atrdate < currentDate) {
                                                    var cur_date = "<?php echo date('m/d/Y');?>";
                                                    if(jQuery(this).attr('date') == cur_date){
                                                        var cur_time = parseInt(currentDate.getHours());
                                                        var slot_time = parseInt(`${i}`);
                                                        if(cur_time > slot_time){

                                                            jQuery(this).parent().css("pointer-events", 'none');
                                                            jQuery(this).parent().css("opacity", '0');
                                                            jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                            jQuery(this).parent().parent().parent().css("border-bottom-color", '#9BA1A3');

                                                        }else{
                                                            jQuery(this).parent().css("opacity", '1');
                                                        }
                                                    }else{
                                                        jQuery(this).parent().css("pointer-events", 'none');
                                                        jQuery(this).parent().css("opacity", '0');
                                                        jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", '#9BA1A3');
                                                    }
                                                    
                                                } else {
                                                    jQuery(this).parent().css("opacity", '1');
                                                }
                                            }
                                        });
                                    }
                                }

                                // //OVER MONTHS
                                // var _overDate = nextDate;
                                // var __startDateOver = startOfWeek(_overDate);
                                // var __endDateOver = endOfWeek(_overDate);
                                //
                                // jQuery('#displayMonth').attr('colspan','4');
                                // jQuery('#displayMonth').html(`${__startDateOver.getDate()}. ${month[__startDateOver.getMonth()]} - ${__endDateOver.getDate()}. ${month[__endDateOver.getMonth()]}`);
                                // jQuery('#displayMonth').css("text-align","center");
                                // //END

                                setTimeout(() => {
                                    jQuery('.tabela .available .time-slot label').each(function() {
                                        var a = jQuery(this).css("background-color");
                                        var b = jQuery(this).parent().parent().css("background-color");

                                        if (a == 'rgb(255, 153, 0)') {
                                            jQuery(this).parent().parent().css('background', `#FF9900`);
                                            jQuery(this).parent().parent().css('border-bottom-color', `#FF9900`);
                                        }
                                        if (a == 'rgb(218, 105, 122)') {
                                            jQuery(this).parent().parent().css('background', `#da697a`);
                                            jQuery(this).parent().parent().css('border-bottom-color', `#da697a`);
                                        }
                                        if (a == 'rgb(255, 255, 255)' && b == 'rgb(255, 153, 0)') {
                                            jQuery(this).parent().parent().css('background', `white`);
                                            jQuery(this).parent().parent().css('border-bottom-color', `#C0C3C3`);
                                        }
                                        if (a == 'rgb(255, 255, 255)' && b == 'rgb(218, 105, 122)') {
                                            jQuery(this).parent().parent().css('background', `white`);
                                            jQuery(this).parent().parent().css('border-bottom-color', `#C0C3C3`);
                                        }
                                    })
                                }, 500);



                            });


                            let firstSec;
                            let second;
                            let clicks = 0;
                            let flag = true;
                            let fromToArray = [];
                            let currDate;
                            let currHour;
                            let currDateHour;
                            let pushDate;
                            let pushHour;
                            let pushDateHour;
                            let changeTimeFrom;
                            let fromId = '';
                            let toId = '';
                            if (mobileCheck()) {
                                fromId = 'mobFromHours';
                                toId = 'mobToHours';
                            } else {
                                fromId = 'fromHours';
                                toId = 'toHours';
                            }
                            jQuery('.tabela .time-slot').click(function() {
                                if (jQuery("tbody#time-slot-table").hasClass("xoo-el-login-tgr")) {
                                    jQuery('div.timenotifi').hide();
                                    return 0;
                                }
                                // if(jQuery('.startDate').length > 0 && jQuery('.endDate').length > 0 && (jQuery(this).children('label').css('background-color') == 'rgb(0, 132, 116)')){
                                // 	currDateHour = '';
                                //     changeTimeFrom = false;
                                //     currDate = jQuery(this).children('label').children('div').attr('date');
                                //     currHour = jQuery(this).children('label').children('div').text();
                                //     currDateHour = currDate + ' ' +currHour;
                                //     fromToArray = [];
                                //     jQuery('.time-slot label').each(function(){
                                //         if(jQuery(this).css('background-color') == 'rgb(0, 132, 116)'){
                                //             pushDate = jQuery(this).children('div').attr('date');
                                //             pushHour = jQuery(this).children('div').text();
                                //             pushDateHour = pushDate +' '+ pushHour;
                                //             fromToArray.push(pushDateHour);
                                //         }
                                //     })

                                //     for(let i = 0 ;i < fromToArray.length / 2 ; i++){
                                //         if(fromToArray[i] == currDateHour){
                                // 			changeTimeFrom = true;
                                //         }
                                //     }

                                //     if(changeTimeFrom){
                                //         jQuery('.startDate').removeClass('startDate');
                                //         let secondClick = jQuery('.endDate');
                                //         jQuery('.tests').each(function(){
                                //             if(jQuery(this).attr('date') == currDate && jQuery(this).text() == currHour){
                                //                 setTimeout(function(){
                                // 					jQuery(this).click();
                                // 					console.log('prv KLIK');
                                // 				},1000)
                                //             }
                                //         })
                                //         setTimeout(function(){
                                //             secondClick.click();
                                // 			console.log('vtor KLIK');
                                //         },2000)
                                //     }else{
                                //         let firstClick = jQuery('.startDate');
                                //         let secondClick;
                                //         jQuery('.tests').each(function(){
                                //             if(jQuery(this).attr('date') == currDate && jQuery(this).text() == currHour){
                                //                 secondClick = jQuery(this);
                                //             }
                                //         })
                                // 		jQuery('.endDate').removeClass('endDate');
                                // 		setTimeout(function(){
                                // 			firstClick.click();
                                //         },1000)
                                //         setTimeout(function(){
                                // 			secondClick.click();
                                //         },3000)
                                //     }

                                // }else{

                                if (clicks > 3) {
                                    loading(2000);
                                    if (jQuery('span:contains(Nullstill)').hasClass('nextButtonIsClicked')) {
                                        jQuery('span:contains(Nullstill)').removeClass('nextButtonIsClicked');
                                    }
                                    firstSec.removeClass('startDate');
                                    second.removeClass('endDate');
                                    clicks = 0;
                                    firstSec.parent().css('background', 'white');
                                    second.parent().css('background', 'white');
                                    jQuery('.time-slot label').css('background', 'white');
                                    if (jQuery('.time-slot label').hasClass('available')) {
                                        jQuery(this).css('pointer-events', '');
                                    }
                                    flag = true;
                                    jQuery('.notifitest').show();

                                    if (listingCategory != 'utstr') {
                                        for (var i = 0; i < waitingLength; i++) {

                                            var startDate = waiting[i][0];
                                            var endDate = waiting[i][1];
                                            var startHour = waiting[i][2];
                                            var endHour = waiting[i][3];
                                            if (startDate === endDate) {
                                                paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
                                            } else if (startDate < endDate) {
                                                paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
                                            }
                                        }

                                        for (var i = 0; i < approvedLength; i++) {

                                            var startDate = approved[i][0];
                                            var endDate = approved[i][1];
                                            var startHour = approved[i][2];
                                            var endHour = approved[i][3];
                                            if (startDate === endDate) {
                                                paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
                                            } else if (startDate < endDate) {
                                                paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
                                            }
                                        }

                                        for (var i = 0; i < unavailableLength; i++) {
                                            var startDate = unavailable[i][0];
                                            var endDate = unavailable[i][1];
                                            var startHour = unavailable[i][2];
                                            var endHour = unavailable[i][3];
                                            if (startDate === endDate) {
                                                paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                            } else if (startDate < endDate) {
                                                paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                            }
                                        }
                                    }

                                    jQuery('.tabela .available .time-slot label').each(function() {
                                        var a = jQuery(this).css("background-color");
                                        if (a == 'rgb(0, 132, 116)') {
                                            jQuery(this).parent().parent().css('background', `white`);
                                            jQuery(this).parent().parent().css('border-bottom-color', `#C0C3C3`);

                                        }
                                    });

                                    setTimeout(() => {
                                        jQuery('.tabela .available .time-slot label').each(function() {
                                            var a = jQuery(this).css("background-color");
                                            if (a == 'rgb(255, 153, 0)') {
                                                jQuery(this).parent().parent().css('background', `#FF9900`);
                                                jQuery(this).parent().parent().css('border-bottom-color', `rgb(255, 153, 0)`);
                                            }
                                            if (a == 'rgb(218, 105, 122)') {
                                                jQuery(this).parent().parent().css('background', `#da697a`);
                                                jQuery(this).parent().parent().css('border-bottom-color', `#da697a`);
                                            }
                                        });
                                    }, 1000);

                                }
                                if (clicks == 3) {
                                    loading(2000);
                                    jQuery('#divtoshow').hide();
                                    jQuery("span:contains(Nullstill)").show();
                                    jQuery('span:contains(Nullstill)').css("pointer-events", "auto");
                                    second = jQuery(this).find('.tests');
                                    second.addClass('endDate');
                                    t = second.attr('class');
                                    d = new Date(second.attr('date'));
                                    m = d.getMonth();
                                    d = moment(d).format('DD');
                                    t = t.substring(t.indexOf(' '), t.length);
                                    t = t.substring(0, t.indexOf('e'));
                                    t = parseInt(t) + 1;
                                    var tChanged = t.toString();
                                    var tInt = parseInt(t);

                                    if (tInt < 10) {
                                        jQuery(`#timeSpanTo`).val(`${d}.${month[m]}. 0${tInt}:00`);
                                    } else {
                                        jQuery(`#timeSpanTo`).val(`${d}.${month[m]}. ${tInt}:00`);
                                    }



                                    //dropdown to
                                    jQuery(`#${fromId} option`).each(function() {
                                        jQuery(this).show();
                                        jQuery(this)[0].text = "";
                                    });

                                    jQuery(`#${toId} option`).each(function() {
                                        jQuery(this).show();
                                        jQuery(this)[0].text = "";
                                        jQuery(this).removeAttr('value');
                                        jQuery(this).removeAttr("data-cl");
                                        jQuery(this).removeAttr("selected");
                                    });

                                    var fsa = second.attr('date');
                                    var tinc = 0;
                                    var fsaDate = new Date(fsa);
                                    fsaDate = startOfWeek(fsaDate);

                                    for (let t = 0; t < 7; t++) {

                                        var fsaMonth = fsaDate.getMonth();
                                        var fsaDay = fsaDate.getDay();
                                        if (fsaDay == 0) {
                                            fsaDay = 6;
                                        } else {
                                            fsaDay -= 1;
                                        }
                                        for (let f = 0; f < 24; f++) {
                                            jQuery(`.${f}${days[fsaDay]}.available .tests`).filter(function() {
                                                if (jQuery(this).parent().parent().parent().css('background-color') != 'rgb(155, 161, 163)' &&
                                                    jQuery(this).parent().css('background-color') != "rgb(218, 105, 122)") {
                                                    var saat = jQuery(this).text();
                                                    saat = saat.substring(0, saat.indexOf(':'));
                                                    saat = parseInt(saat);
                                                    if (f == saat) {
                                                        if (f < 10) {
                                                            jQuery(`#${fromId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. 0${f}:00`);
                                                            jQuery(`#${fromId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. 0${f}:00`);
                                                            var data = jQuery(this).parent().parent().parent().attr('class');
                                                            jQuery(`#${fromId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                        } else {
                                                            jQuery(`#${fromId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. ${f}:00`);
                                                            jQuery(`#${fromId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. ${f}:00`);
                                                            var data = jQuery(this).parent().parent().parent().attr('class');
                                                            jQuery(`#${fromId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                        }
                                                        tinc++;
                                                    }
                                                }
                                            });
                                        }
                                        var setFsa = fsaDate;
                                        fsaDate = new Date(setFsa.setDate(setFsa.getDate() + 1));
                                    }

                                    fsa = jQuery('.startDate').attr('date');
                                    fsaDate = new Date(fsa);
                                    fsaDate = startOfWeek(fsaDate);
                                    tinc = 0;
                                    var tgforh = parseInt(jQuery('.startDate').text().substr(0, jQuery('.startDate').text().indexOf(":")));

                                    for (let t = 0; t < 7; t++) {

                                        var fsaMonth = fsaDate.getMonth();
                                        var fsaDay = fsaDate.getDay();
                                        if (fsaDay == 0) {
                                            fsaDay = 6;
                                        } else {
                                            fsaDay -= 1;
                                        }

                                        for (let f = 0; f < 24; f++) {
                                            jQuery(`.${f}${days[fsaDay]}.available .tests`).filter(function() {
                                                if (jQuery(this).parent().parent().parent().css('background-color') != 'rgb(155, 161, 163)' &&
                                                    jQuery(this).parent().css('background-color') != "rgb(218, 105, 122)") {
                                                    if (jQuery(this).attr('date') == fsa) {
                                                        var saat = jQuery(this).text();
                                                        saat = saat.substring(0, saat.indexOf(':'));
                                                        saat = parseInt(saat);
                                                        if (f == saat && f >= tgforh) {
                                                            var g = f + 1;
                                                            if (g < 10) {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            } else {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            }
                                                            tinc++;
                                                        }
                                                    } else if (jQuery(this).attr('date') > fsa) {
                                                        var saat = jQuery(this).text();
                                                        saat = saat.substring(0, saat.indexOf(':'));
                                                        saat = parseInt(saat);
                                                        if (saat == f) {
                                                            var g = f + 1
                                                            if (g < 10) {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            } else {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            }
                                                            tinc++;
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                        var setFsa = fsaDate;
                                        fsaDate = new Date(setFsa.setDate(setFsa.getDate() + 1));
                                    }

                                    jQuery(`#${fromId} option`).each(function() {
                                        let x = jQuery(this)[0];
                                        if (x.text == "") {
                                            jQuery(this).hide();
                                        }
                                    });

                                    jQuery(`#${toId} option`).each(function() {
                                        let x = jQuery(this)[0];
                                        if (x.text == "") {
                                            jQuery(this).hide();
                                        }
                                    });

                                    setTimeout(function() {
                                        jQuery(`#${toId} option`).each(function() {
                                            if (jQuery('.endDate').length > 0) {
                                                let x = jQuery('.endDate').parent().parent().parent().attr('class');
                                                if (jQuery(this).attr('data-cl') == x) {
                                                    jQuery(this).prop("selected", true);
                                                }
                                            }
                                        });
                                    }, 500);

                                    second.parent().css("background", "#008474");
                                    second.parent().parent().parent().css("background", "#008474");
                                    second.parent().parent().parent().css("border-bottom-color", '#008474');
                                    jQuery('.notifitest').hide();

                                    firstProp = localStorage.getItem('firstDate');
                                    secondProp = second.attr("date");

                                    firstHour = parseInt(firstSec.attr('class').split(' ')[1].substring(0, 2));
                                    secondHour = parseInt(second.attr('class').split(' ')[1].substring(0, 2));

                                    let nextHour = firstHour;
                                    var calendarAlert = false;
                                    var calendarAlertSupporter = false;
                                    var newCalendarUnavailableAlert = false;

                                    if (goToNextWeek2) {
                                        jQuery('span:contains(Nullstill)').addClass('nextButtonIsClicked');
                                        goToNextWeek2 = false;
                                        secondPropDay = new Date(moment(secondProp).format('MM/DD/YYYY'));
                                        let preEndDay;
                                        if (secondPropDay.getDay() == 0) {
                                            preEndDay = 6;
                                        } else {
                                            preEndDay = secondPropDay.getDay() - 1;
                                        }
                                        for (let y = 0; y < preEndDay; y++) {
                                            let pom = 0;
                                            while (pom < 24) {
                                                jQuery(`.tabela .${y}.${pom}${days[y]} .tests`).filter(function() {
                                                    if (jQuery(this).text() === `${pom}:00`) {
                                                        if (jQuery(`.${y}.${pom}${days[y]} .booked`).length > 0 || jQuery(`.${y}.${pom}${days[y]}`).hasClass('unavailable')) {
                                                            if (jQuery(`.${y}.${pom}${days[y]}`).hasClass('unavailable')) {
                                                                newCalendarUnavailableAlert = true;
                                                            }
                                                            calendarAlert = true;
                                                        } else {
                                                            jQuery(this).parent().css("background", '#008474');
                                                            jQuery(this).parent().parent().parent().css("background", '#008474');
                                                            jQuery(this).parent().parent().parent().css("border-bottom-color", '#008474');

                                                        }
                                                    }
                                                });
                                                pom++;
                                            }
                                        }
                                        nextHour = 0;
                                        while (nextHour < secondHour) {
                                            jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]} .tests`).filter(function() {
                                                if (jQuery(this).text() === `${nextHour}:00`) {
                                                    if (jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]} .booked`).length > 0 || jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]}`).hasClass('unavailable')) {
                                                        if (jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]}`).hasClass('unavailable')) {
                                                            newCalendarUnavailableAlert = true;
                                                        }
                                                        calendarAlert = true;
                                                    } else {
                                                        jQuery(this).parent().css("background", '#008474');
                                                        jQuery(this).parent().parent().parent().css("background", '#008474');
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", '#008474');
                                                    }
                                                }
                                            });
                                            nextHour++;
                                        }
                                    } else {
                                        if (firstProp == secondProp) {
                                            if (firstHour < secondHour) {
                                                while (nextHour < secondHour) {
                                                    let firstLastLabel;
                                                    let checkIfFirst = 1;
                                                    firstPropDay = new Date(moment(firstProp).format('MM/DD/YYYY'));
                                                    if (firstPropDay.getDay() == 0) {
                                                        jQuery(`.6.${nextHour}sun .tests`).filter(function() {
                                                            if (jQuery(this).text() == `${nextHour}:00`) {
                                                                if (jQuery(`.6.${nextHour}sun .booked`).length > 0 || jQuery(`.6.${nextHour}sun`).hasClass('unavailable')) {
                                                                    if (jQuery(`.6.${nextHour}sun`).hasClass('unavailable')) {
                                                                        newCalendarUnavailableAlert = true;
                                                                    }
                                                                    calendarAlert = true;
                                                                } else {
                                                                    jQuery(this).parent().css("background", '#008474');
                                                                    jQuery(this).parent().parent().parent().css("background", '#008474');
                                                                    jQuery(this).parent().parent().parent().css("border-bottom-color", '#008474');

                                                                }
                                                            }
                                                        });
                                                    } else {
                                                        jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[firstPropDay.getDay() - 1]} .tests`).filter(function() {
                                                            if (jQuery(this).text() == `${nextHour}:00`) {

                                                                if (jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[firstPropDay.getDay() - 1]} .booked`).length > 0 || jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[firstPropDay.getDay() - 1]}`).hasClass('unavailable')) {
                                                                    if (jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[firstPropDay.getDay() - 1]}`).hasClass('unavailable')) {
                                                                        newCalendarUnavailableAlert = true;
                                                                    }
                                                                    calendarAlert = true;
                                                                } else {
                                                                    jQuery(this).parent().css("background", '#008474');
                                                                    jQuery(this).parent().parent().parent().css("background", '#008474');
                                                                    jQuery(this).parent().parent().parent().css("border-bottom-color", '#008474');

                                                                }
                                                            }
                                                        });
                                                    }
                                                    nextHour++;
                                                }
                                            } else if (firstHour == secondHour) {
                                                firstPropDay = new Date(moment(firstProp).format('MM/DD/YYYY'));
                                                if (firstPropDay.getDay() == 0) {
                                                    jQuery(`.6.${nextHour}sun .tests`).filter(function() {
                                                        if (jQuery(this).text() == `${nextHour}:00`) {
                                                            if (jQuery(`.6.${nextHour}sun .booked`).length > 0 || jQuery(`.6.${nextHour}sun`).hasClass('unavailable')) {
                                                                if (jQuery(`.6.${nextHour}sun`).hasClass('unavailable')) {
                                                                    newCalendarUnavailableAlert = true;
                                                                }
                                                                calendarAlert = true;
                                                            } else {
                                                                jQuery(this).parent().css("background", '#008474');
                                                            }
                                                        }
                                                    });
                                                } else {
                                                    jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[firstPropDay.getDay() - 1]} .tests`).filter(function() {
                                                        if (jQuery(this).text() == `${nextHour}:00`) {
                                                            if (jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}sun .booked`).length > 0 || jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}sun`).hasClass('unavailable')) {
                                                                if (jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}sun`).hasClass('unavailable')) {
                                                                    newCalendarUnavailableAlert = true;
                                                                }
                                                                calendarAlert = true;
                                                            } else {
                                                                jQuery(this).parent().css("background", '#008474');
                                                            }
                                                        }
                                                    });
                                                }
                                            } else {
                                                calendarAlert = true;
                                                calendarAlertSupporter = true;
                                            }
                                        } else if (firstProp < secondProp) {
                                            firstPropDay = new Date(moment(firstProp).format('MM/DD/YYYY'));
                                            secondPropDay = new Date(moment(secondProp).format('MM/DD/YYYY'));
                                            var asd = firstPropDay.getDay();
                                            asd--;
                                            let br = 1;
                                            var checkIfFirst = 1;
                                            var checkIfFirst2 = 1;
                                            while (nextHour <= 23) {
                                                jQuery(`.${asd}.${nextHour}${days[asd]} .tests`).filter(function() {
                                                    if (jQuery(this).text() === `${nextHour}:00`) {
                                                        if (jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[asd]} .booked`).length > 0 || jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[asd]}`).hasClass('unavailable')) {
                                                            if (jQuery(`.${firstPropDay.getDay() - 1}.${nextHour}${days[asd]}`).hasClass('unavailable')) {
                                                                newCalendarUnavailableAlert = true;
                                                            }
                                                            calendarAlert = true;
                                                        } else if (calendarAlert == false) {
                                                            var __par = jQuery(this).parent().parent().parent();
                                                            jQuery(this).parent().css("background", '#008474');
                                                            __par.css("background", '#008474');
                                                            __par.css("border-bottom-color", '#008474');
                                                        }
                                                    }
                                                });
                                                nextHour++;
                                            }


                                            let preEndDay;
                                            if (secondPropDay.getDay() == 0) {
                                                preEndDay = 6;
                                            } else {
                                                preEndDay = secondPropDay.getDay() - 1;
                                            }
                                            for (let y = firstPropDay.getDay(); y < preEndDay; y++) {
                                                let pom = 0;
                                                var checkIfFirst1 = 1;
                                                while (pom <= 24) {
                                                    jQuery(`.${y}.${pom}${days[y]} .tests`).filter(function() {
                                                        if (jQuery(this).text() === `${pom}:00`) {
                                                            if (jQuery(`.${y}.${pom}${days[y]} .booked`).length > 0) {
                                                                if (jQuery(`.${y}.${pom}${days[y]}`).hasClass('unavailable')) {
                                                                    newCalendarUnavailableAlert = true;
                                                                }
                                                                calendarAlert = true;
                                                            } else if (calendarAlert == false) {
                                                                var _par = jQuery(this).parent().parent().parent();
                                                                jQuery(this).parent().css("background", '#008474');
                                                                _par.css("background", '#008474');
                                                                _par.css("border-bottom-color", '#008474');


                                                            }


                                                        }
                                                    });
                                                    pom++;
                                                }

                                            }

                                            nextHour = 0;
                                            while (nextHour < secondHour) {
                                                jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]} .tests`).filter(function() {
                                                    if (jQuery(this).text() === `${nextHour}:00`) {
                                                        if (jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]} .booked`).length > 0 || jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]}`).hasClass('unavailable')) {
                                                            if (jQuery(`.${preEndDay}.${nextHour}${days[preEndDay]}`).hasClass('unavailable')) {
                                                                newCalendarUnavailableAlert = true;
                                                            }
                                                            calendarAlert = true;
                                                        } else if (calendarAlert == false) {
                                                            var par = jQuery(this).parent().parent().parent();
                                                            jQuery(this).parent().css("background", '#008474');
                                                            par.css("background", '#008474');
                                                            par.css("border-bottom-color", '#008474');
                                                        }
                                                    }
                                                });
                                                nextHour++;
                                            }
                                        } else if (firstProp > secondProp) {
                                            calendarAlert = true;
                                            calendarAlertSupporter = true;
                                        }
                                    }

                                    if (newCalendarUnavailableAlert) {
                                        var q = '<div class="col-xs-12 col-md-12 notAvailableUn" style="display:none;position:absolute; top:50%;z-index:10;text-align: center; margin: 10px 0 10px 0; padding: 10px; font-size: 15px; background: #ffebeb;color:#d83838"><span>Your request might be declined</span></div>';
                                        jQuery('.tabela tbody').append(q);
                                        jQuery('.notAvailableUn').fadeIn(2000);
                                        jQuery('.notAvailableUn').fadeOut(2000);
                                        setTimeout(() => {
                                            jQuery('.notAvailableUn').remove();
                                        }, 3000);
                                    } else if (calendarAlert) {
                                        var p = '<div class="col-xs-12 col-md-12 notAvailable" style="display:none;position:absolute; top:50%;z-index:10;text-align: center; margin: 10px 0 10px 0; padding: 10px; font-size: 15px; background: #ffebeb;color:#d83838"><span>Selected times are not available</span></div>'
                                        jQuery('.startDate').removeClass('startDate');
                                        jQuery('.tabela tbody').append(p);
                                        if (!calendarAlertSupporter) {
                                            jQuery('.notAvailable').fadeIn(2000);
                                            jQuery('.notAvailable').fadeOut(2000);
                                            setTimeout(() => {
                                                jQuery('.notAvailable').remove();
                                            }, 3000);
                                        }

                                        setTimeout(function() {
                                            jQuery('.endDate').parent()[0].click();
                                        }, 500);
                                    }
                                    goToNextWeek = false;

                                    if (jQuery('span:contains(Nullstill)').hasClass('nextButtonIsClicked')) {
                                        if (jQuery('.booking-sticky-footer').is(':visible')) {
                                            jQuery('.timenotifi').hide();
                                            jQuery('.timeSpan').show();
                                            jQuery('#timeSpanFrom').parent().addClass('col-xs-12').removeClass('col-xs-5');
                                            jQuery('#timeSpanTo').parent().addClass('col-xs-12').removeClass('col-xs-5')
                                            jQuery('.timeSpan .col-xs-12').css('max-height', '30px');
                                            jQuery('.timeSpan').css('bottom', '10px');
                                            jQuery('.timeSpan').css('position', 'relative');
                                            if (jQuery('.prepspanfrom').is(':visible')) {
                                                jQuery('.prepspanfrom').show();
                                            } else {
                                                jQuery('#timeSpanFrom').parent().prepend('<span class="col-xs-3 prepspanfrom" style="padding: 0;float: left;font-size: 12px;font-weight: bold;top: 13px;">From:</span>');
                                            }
                                            jQuery('#timeSpanFrom').addClass('col-xs-8');
                                            jQuery('#timeSpanFrom').addClass('mobileFromHours');
                                            if (jQuery('.prepspanto').is(':visible')) {
                                                jQuery('.prepspanto').show();
                                            } else {
                                                jQuery('#timeSpanTo').parent().prepend('<span class="col-xs-3 prepspanto" style="position:relative;right: 9px;padding: 0;float: left;font-size: 12px;font-weight: bold;top: 13px;">To:</span>');
                                            }
                                            jQuery('#timeSpanTo').addClass('col-xs-8');
                                            jQuery('#timeSpanTo').addClass('mobileFromHours');
                                            jQuery(".timeSpan").appendTo('.booking-sticky-footer .bsf-left');
                                        } else {
                                            jQuery('.timeSpan').show();
                                            jQuery('.timtoHoursenotifi').hide();
                                        }
                                    } else {
                                        jQuery('.timeSpan').hide();
                                        if (!mobileCheck()) {
                                            jQuery('.timenotifi').show();
                                        }
                                    }

                                    var totalHoursSelected = 0;
                                    var minDays = parseInt(jQuery('.minDays span').attr('data-min'));
                                    var minHours = parseInt(jQuery('.minHours span').attr('data-min'));
                                    setTimeout(() => {
                                        jQuery('.available label').filter(function() {
                                            if (jQuery(this).css('background-color') == "rgb(0, 132, 116)") {
                                                totalHoursSelected++;
                                            }
                                        });

                                        var sdText = jQuery('.startDate').text();
                                        var edText = jQuery('.endDate').text()
                                        var str1 = firstProp + ' ' + sdText;
                                        var str2 = secondProp + ' ' + edText;

                                        var dateOneObj = new Date(str1);
                                        var dateTwoObj = new Date(str2);


                                        var milliseconds = Math.abs(dateTwoObj - dateOneObj);
                                        var hours = milliseconds / 36e5;
                                        var totalDays = hours + 1;
                                        totalDays = Math.floor(totalDays / 24);

                                        if (minHours > 0) {
                                            if (minHours > totalHoursSelected) {
                                                jQuery('.poraka span').text(`Vennligst velg ${minHours} eller flere timer!`);

                                                jQuery('.poraka').fadeIn();
                                                setTimeout(() => {
                                                    jQuery('.book-now').fadeOut();
                                                }, 300);

                                            } else {
                                                jQuery('.poraka').fadeOut();
                                                jQuery('.book-now').fadeIn();
                                            }
                                        } else {
                                            if (minDays > 0) {
                                                if (minDays > totalDays) {
                                                    jQuery('.poraka span').text(`Vennligst velg ${minDays} eller flere dager!`);
                                                    jQuery('.poraka').fadeIn();
                                                    setTimeout(() => {
                                                        jQuery('.book-now').fadeOut();
                                                    }, 300);
                                                } else {
                                                    jQuery('.poraka').fadeOut();
                                                    jQuery('.book-now').fadeIn();
                                                }
                                            }
                                        }
                                    }, 500);

                                    setTimeout(() => {
                                        jQuery('.tabela .available .time-slot label').each(function() {
                                            var a = jQuery(this).css("background-color");
                                            if (a == 'rgb(255, 153, 0)') {
                                                jQuery(this).parent().parent().css('background', `#FF9900`);
                                                jQuery(this).parent().parent().css('border-bottom-color', `rgb(255, 153, 0)`);
                                            }
                                            if (a == 'rgb(218, 105, 122)') {
                                                jQuery(this).parent().parent().css('background', `#da697a`);
                                                jQuery(this).parent().parent().css('border-bottom-color', `#da697a`);
                                            }
                                        });
                                    }, 600);

                                } else if (clicks == 0) {
                                    loading(2000);
                                    if (listingCategory != 'utstr') {
                                        for (var i = 0; i < waitingLength; i++) {
                                            var startDate = waiting[i][0];
                                            var endDate = waiting[i][1];
                                            var startHour = waiting[i][2];
                                            var endHour = waiting[i][3];
                                            if (startDate === endDate) {
                                                paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
                                            } else if (startDate < endDate) {
                                                paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
                                            }
                                        }

                                        for (var i = 0; i < approvedLength; i++) {
                                            var startDate = approved[i][0];
                                            var endDate = approved[i][1];
                                            var startHour = approved[i][2];
                                            var endHour = approved[i][3];
                                            if (startDate === endDate) {
                                                paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
                                            } else if (startDate < endDate) {
                                                paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
                                            }
                                        }

                                        for (var i = 0; i < unavailableLength; i++) {
                                            var startDate = unavailable[i][0];
                                            var endDate = unavailable[i][1];
                                            var startHour = unavailable[i][2];
                                            var endHour = unavailable[i][3];
                                            if (startDate === endDate) {
                                                paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                            } else if (startDate < endDate) {
                                                paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                            }
                                        }
                                    }
                                    if (mobileCheck()) {
                                        setTimeout(function() {
                                            if (jQuery('.bsf-left .timenotifi').is(':visible') == false) {
                                                //jQuery('.booking-sticky-footer .bsf-right a').text('Book now');
                                                jQuery(".bsf-leftDropdown").show();
                                                //jQuery(".timenotifi").appendTo('.booking-sticky-footer .bsf-left');
                                                jQuery('.booking-sticky-footer .bsf-left').hide();
                                                jQuery('.booking-sticky-footer .bsf-right').css('flex', '');
                                                //jQuery('#mobFromHours').parent().addClass('col-xs-12').removeClass('col-xs-3');
                                                //jQuery('#mobToHours').parent().addClass('col-xs-12').removeClass('col-xs-3')
                                                // jQuery('.timenotifi .col-xs-12').css('max-height','30px');
                                                // jQuery('.timenotifi').css('bottom','10px');
                                                // jQuery('.timenotifi').css('position','relative');

                                                //jQuery('#fromHours').parent().prepend('<span class="col-xs-3" style="padding: 0;float: left;font-size: 12px;font-weight: bold;top: 13px;">From:</span>');
                                                //jQuery('#fromHours').addClass('col-xs-8');
                                                //jQuery('#fromHours').addClass('mobileFromHours');
                                                //
                                                // jQuery('#toHours').parent().prepend('<span class="col-xs-3" style="position:relative;right: 9px;padding: 0;float: left;font-size: 12px;font-weight: bold;top: 13px;">To:</span>');
                                                // jQuery('#toHours').addClass('col-xs-8');
                                                // jQuery('#toHours').addClass('mobileFromHours');
                                            } else {
                                                jQuery('.booking-sticky-footer .bsf-left h4').hide();
                                            }

                                        }, 300);

                                    }
                                    jQuery('.notifitest').hide();
                                    jQuery('.timeSpan').hide();
                                    goToNextWeek = true;
                                    firstSec = jQuery(this).find('.tests');
                                    firstSec.addClass('startDate');

                                    var topp = jQuery('.startDate').parent().parent().parent()[0];
                                    jQuery('#divtoshow').css({
                                        top: topp.offsetTop - 33,
                                        left: topp.offsetLeft - 27
                                    }).show();

                                    var t = firstSec.attr('class');
                                    var d = new Date(firstSec.attr('date'));
                                    let m = d.getMonth();
                                    d = moment(d).format('DD');
                                    t = t.substring(t.indexOf(' '), t.length);
                                    t = t.substring(0, t.indexOf('s'));

                                    var tg = t.substring(0, t.indexOf(':'));
                                    var tch = t.substr(1);
                                    tg = parseInt(tg);

                                    if (tg < 10) {
                                        jQuery(`#timeSpanFrom`).val(`${d}.${month[m]}. 0${tg}:00`);
                                    } else {
                                        jQuery(`#timeSpanFrom`).val(`${d}.${month[m]}. ${tg}:00`);
                                    }

                                    jQuery(`#timeSpanTo`).val(`velg slutt-tid`);

                                    console.log(fromId);
                                    console.log(toId);

                                    //dropdown from
                                    jQuery(`#${fromId} option`).each(function() {
                                        jQuery(this).show();
                                        jQuery(this)[0].text = "";
                                        jQuery(this).removeAttr('value');
                                        jQuery(this).removeAttr("data-cl");
                                        jQuery(this).removeAttr("selected");
                                    });

                                    jQuery(`#${toId} option`).each(function() {
                                        jQuery(this).show();
                                        jQuery(this)[0].text = "";
                                    });

                                    var fsa = firstSec.attr('date');
                                    var tgforh = tg;
                                    var tinc = 0;
                                    var fsaDate = new Date(fsa);
                                    fsaDate = startOfWeek(fsaDate);

                                    for (let t = 0; t < 7; t++) {

                                        var fsaMonth = fsaDate.getMonth();
                                        var fsaDay = fsaDate.getDay();
                                        if (fsaDay == 0) {
                                            fsaDay = 6;
                                        } else {
                                            fsaDay -= 1;
                                        }
                                        for (let f = 0; f < 24; f++) {
                                            jQuery(`.${f}${days[fsaDay]}.available .tests`).filter(function() {
                                                if (jQuery(this).parent().parent().parent().css('background-color') != 'rgb(155, 161, 163)' &&
                                                    jQuery(this).parent().parent().parent().css('background-color') != "rgb(218, 105, 122)") {
                                                    console.log(jQuery(this).parent().parent().parent().css('background-color'));
                                                    if (jQuery(this).attr('date') == fsa) {
                                                        var saat = jQuery(this).text();
                                                        saat = saat.substring(0, saat.indexOf(':'));
                                                        saat = parseInt(saat);
                                                        if (f == saat && f <= tgforh) {
                                                            if (f < 10) {
                                                                jQuery(`#${fromId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. 0${f}:00`);
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. 0${f}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            } else {
                                                                jQuery(`#${fromId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. ${f}:00`);
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. ${f}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            }
                                                            if (f == tgforh) {
                                                                jQuery(`#${fromId} option:eq(${tinc})`).prop("selected", true);
                                                                jQuery(`#${fromId} option:eq(${tinc})`).css("display", "none");
                                                            }
                                                            tinc++;
                                                        }
                                                    } else if (jQuery(this).attr('date') < fsa) {
                                                        var saat = jQuery(this).text();
                                                        saat = saat.substring(0, saat.indexOf(':'));
                                                        saat = parseInt(saat);
                                                        if (saat == f) {
                                                            if (f < 10) {
                                                                jQuery(`#${fromId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. 0${f}:00`);
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. 0${f}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            } else {
                                                                jQuery(`#${fromId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. ${f}:00`);
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. ${f}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${fromId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            }
                                                            tinc++;
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                        var setFsa = fsaDate;
                                        fsaDate = new Date(setFsa.setDate(setFsa.getDate() + 1));
                                    }

                                    fsaDate = new Date(fsa);
                                    tinc = 0;
                                    for (let t = 0; t < 7; t++) {
                                        var fsaMonth = fsaDate.getMonth();
                                        var fsaDay = fsaDate.getDay();
                                        if (fsaDay == 0) {
                                            fsaDay = 6;
                                        } else {
                                            fsaDay -= 1;
                                        }

                                        for (let f = 0; f < 24; f++) {
                                            jQuery(`.${f}${days[fsaDay]}.available .tests`).filter(function() {
                                                if (jQuery(this).parent().parent().parent().css('background-color') != 'rgb(155, 161, 163)' &&
                                                    jQuery(this).parent().css('background-color') != "rgb(218, 105, 122)") {
                                                    if (jQuery(this).attr('date') == fsa) {
                                                        var saat = jQuery(this).text();
                                                        saat = saat.substring(0, saat.indexOf(':'));
                                                        saat = parseInt(saat);
                                                        if (f == saat && f >= tgforh) {
                                                            g = f + 1;
                                                            if (g < 10) {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            } else {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            }
                                                            tinc++;
                                                        }
                                                    } else if (jQuery(this).attr('date') > fsa) {
                                                        var saat = jQuery(this).text();
                                                        saat = saat.substring(0, saat.indexOf(':'));
                                                        saat = parseInt(saat);
                                                        if (saat == f) {
                                                            g = f + 1;
                                                            if (g < 10) {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. 0${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            } else {
                                                                jQuery(`#${toId} option:eq(${tinc})`).text(`${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('value', `${fsaDate.getDate()}.${month[fsaMonth]}. ${g}:00`);
                                                                var data = jQuery(this).parent().parent().parent().attr('class');
                                                                jQuery(`#${toId} option:eq(${tinc})`).attr('data-cl', `${data}`);
                                                            }
                                                            tinc++;
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                        var setFsa = fsaDate;
                                        fsaDate = new Date(setFsa.setDate(setFsa.getDate() + 1));
                                    }

                                    jQuery(`#${fromId} option`).each(function() {
                                        let x = jQuery(this)[0];
                                        if (x.text == "") {
                                            jQuery(this).hide();
                                        }
                                    });

                                    jQuery(`#${toId} option`).each(function() {
                                        let x = jQuery(this)[0];
                                        if (x.text == "") {
                                            jQuery(this).hide();
                                        }
                                    });

                                    jQuery(`#${toId} option:eq(${tinc})`).text(`velg slutt-tid`);
                                    jQuery(`#${toId} option:eq(${tinc})`).attr('value', `Select time`);
                                    jQuery(`#${toId} option:eq(${tinc})`).attr("selected", "selected");

                                    firstSec.parent().css('background', '#008474');
                                    firstSec.parent().parent().parent().css('background', '#008474');
                                    jQuery('.notifitest').addClass('nclicked');
                                    if (flag) {
                                        prv = firstSec.attr('date');
                                        localStorage.setItem('firstDate', prv);
                                    }
                                    flag = false;

                                }

                                clicks += 1;

                            });

                            jQuery(".tabela tr td label").css("background", "white");
                            jQuery(".tabela tr td").css("text-align", "center");
                            jQuery(".tabela tr th").css("text-align", "center");
                            jQuery(".tabela tr td").css("width", "40px");
                            jQuery(".tabela tbody tr th").css("width", "40px");
                            if (listingCategory != 'utstr') {
                                for (var i = 0; i < waitingLength; i++) {
                                    waiting[i] = waiting[i].slice(0, -1);
                                    waiting[i] = waiting[i].substr(1);
                                    waiting[i] = waiting[i].split("|");
                                    var startDate = waiting[i][0];
                                    var endDate = waiting[i][1];
                                    var startHour = waiting[i][2];
                                    var endHour = waiting[i][3];
                                    if (startDate === endDate) {
                                        paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
                                    } else if (startDate < endDate) {
                                        paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
                                    }
                                }
                                for (var i = 0; i < approvedLength; i++) {
                                    approved[i] = approved[i].slice(0, -1);
                                    approved[i] = approved[i].substr(1);
                                    approved[i] = approved[i].split("|");
                                    var startDate = approved[i][0];
                                    var endDate = approved[i][1];
                                    var startHour = approved[i][2];
                                    var endHour = approved[i][3];
                                    if (startDate === endDate) {
                                        paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
                                    } else if (startDate < endDate) {
                                        paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
                                    }
                                }

                                for (var i = 0; i < unavailableLength; i++) {
                                    unavailable[i] = unavailable[i].slice(0, -1);
                                    unavailable[i] = unavailable[i].substr(1);
                                    unavailable[i] = unavailable[i].split("|");
                                    var startDate = unavailable[i][0];
                                    var endDate = unavailable[i][1];
                                    var startHour = unavailable[i][2];
                                    var endHour = unavailable[i][3];
                                    if (startDate === endDate) {
                                        paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                    } else if (startDate < endDate) {
                                        paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                    }
                                }
                            }

                            function paintOneRow(startDate, startHour, endHour, color, clickable) {
                                
                                let nextHour = parseInt(startHour);
                                startHour = parseInt(startHour);
                                endHour = parseInt(endHour);
                                if (startHour < endHour) {
                                    while (nextHour < endHour) {
                                        firstPropDay = new Date(moment(startDate).format('MM/DD/YYYY'));
                                        if (firstPropDay.getDay() == 0) {
                                            jQuery(`.6 .tests`).filter(function() {
                                                if (jQuery(this).attr('date') == startDate) {
                                                    if (jQuery(this).text() == `${nextHour}:00`) {
                                                        jQuery(this).parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                        if (clickable) {
                                                            if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                                jQuery(this).parent().parent().parent().addClass('unavailable');
                                                            } else {
                                                                jQuery(this).parent().css('pointer-events', 'none');
                                                                jQuery(this).parent().addClass('booked');
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        } else {
                                            jQuery(`.${firstPropDay.getDay() - 1} .tests`).filter(function() {
                                                if (jQuery(this).attr('date') == startDate) {
                                                    if (jQuery(this).text() == `${nextHour}:00`) {
                                                        jQuery(this).parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                        if (clickable) {
                                                            if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                                jQuery(this).parent().parent().parent().addClass('unavailable');
                                                            } else {
                                                                jQuery(this).parent().css('pointer-events', 'none');
                                                                jQuery(this).parent().addClass('booked');
                                                            }
                                                        }

                                                    }
                                                }
                                            });
                                        }
                                        nextHour++;
                                    }
                                } else if (startHour == endHour) {
                                    firstPropDay = new Date(moment(startDate).format('MM/DD/YYYY'));
                                    if (firstPropDay.getDay() == 0) {
                                        jQuery(`.6 .tests`).filter(function() {
                                            if (jQuery(this).attr('date') == startDate) {
                                                if (jQuery(this).text() == `${startHour}:00`) {
                                                    jQuery(this).parent().css("background", `${color}`);
                                                    jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                    jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                    if (clickable) {
                                                        if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                            jQuery(this).parent().parent().parent().addClass('unavailable');
                                                        } else {
                                                            jQuery(this).parent().css('pointer-events', 'none');
                                                            jQuery(this).parent().addClass('booked');
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        jQuery(`.${firstPropDay.getDay() - 1} .tests`).filter(function() {
                                            if (jQuery(this).attr('date') == startDate) {
                                                if (jQuery(this).text() == `${startHour}:00`) {
                                                    jQuery(this).parent().css("background", `${color}`);
                                                    jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                    jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                    if (clickable) {
                                                        if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                            jQuery(this).parent().parent().parent().addClass('unavailable');
                                                        } else {
                                                            jQuery(this).parent().css('pointer-events', 'none');
                                                            jQuery(this).parent().addClass('booked');
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }


                            }

                            function paintMoreRows(startDate, endDate, startHour, endHour, color, clickable) {
                                let nextHour = parseInt(startHour);
                                startHour = parseInt(startHour);
                                endHour = parseInt(endHour);

                                var firstPropDay = new Date(moment(startDate).format('MM/DD/YYYY'));
                                var secondPropDay = new Date(moment(endDate).format('MM/DD/YYYY'));

                                var _stdt = firstPropDay.getWeek();
                                var _endt = secondPropDay.getWeek();


                                while (nextHour < 24) {
                                    jQuery(`.${firstPropDay.getDay() - 1} .tests`).filter(function() {
                                        if (jQuery(this).attr('date') == startDate) {
                                            if (jQuery(this).text() == `${nextHour}:00`) {
                                                jQuery(this).parent().css("background", `${color}`);
                                                jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                if (clickable) {
                                                    if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                        jQuery(this).parent().parent().parent().addClass('unavailable');
                                                    } else {
                                                        jQuery(this).parent().css('pointer-events', 'none');
                                                        jQuery(this).parent().addClass('booked');
                                                    }
                                                }
                                            }
                                        }
                                    });
                                    nextHour++;
                                }

                                let preEndDay;
                                if (secondPropDay.getDay() == 0) {
                                    preEndDay = 6;
                                } else {
                                    preEndDay = secondPropDay.getDay() - 1;
                                }

                                if (_stdt == _endt) {
                                    let preEndDay;
                                    if (secondPropDay.getDay() == 0) {
                                        preEndDay = 6;
                                    } else {
                                        preEndDay = secondPropDay.getDay() - 1;
                                    }

                                    let fpd = firstPropDay;

                                    for (let y = firstPropDay.getDay(); y < preEndDay; y++) {
                                        let dddd = new Date();
                                        dddd = moment(fpd).format('MM/DD/YYYY');
                                        let pom = 0;

                                        while (pom < 24) {
                                            jQuery(`.${y}.${pom}${days[y]} .tests`).filter(function() {
                                                let thiss = jQuery(this).attr('date');
                                                var increased = new Date(dddd);
                                                increased.setDate(increased.getDate() + 1);
                                                increased = moment(increased).format('MM/DD/YYYY');

                                                if (jQuery(this).attr('date') == increased) {
                                                    if (jQuery(this).text() === `${pom}:00`) {
                                                        jQuery(this).parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                        if (clickable) {
                                                            if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                                jQuery(this).parent().parent().parent().addClass('unavailable');
                                                            } else {
                                                                jQuery(this).parent().css('pointer-events', 'none');
                                                                jQuery(this).parent().addClass('booked');
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                            pom++;
                                        }
                                        fpd = new Date(fpd.setDate(fpd.getDate() + 1));
                                    }
                                } else {
                                    let preEndDay;
                                    if (secondPropDay.getDay() == 0) {
                                        preEndDay = 6;
                                    } else {
                                        preEndDay = secondPropDay.getDay() - 1;
                                    }

                                    let fpd = firstPropDay;
                                    for (let y = firstPropDay.getDay(); y < 7; y++) {
                                        let dddd = new Date();
                                        dddd = moment(fpd).format('MM/DD/YYYY');
                                        let pom = 0;

                                        while (pom < 24) {
                                            jQuery(`.${y}.${pom}${days[y]} .tests`).filter(function() {
                                                let thiss = jQuery(this).attr('date');
                                                var increased = new Date(dddd);
                                                increased.setDate(increased.getDate() + 1);
                                                increased = moment(increased).format('MM/DD/YYYY');

                                                if (jQuery(this).attr('date') == increased) {
                                                    if (jQuery(this).text() === `${pom}:00`) {
                                                        jQuery(this).parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                        if (clickable) {
                                                            if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                                jQuery(this).parent().parent().parent().addClass('unavailable');
                                                            } else {
                                                                jQuery(this).parent().css('pointer-events', 'none');
                                                                jQuery(this).parent().addClass('booked');
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                            pom++;
                                        }
                                        fpd = new Date(fpd.setDate(fpd.getDate() + 1));
                                    }

                                    for (let y = 0; y < preEndDay; y++) {
                                        let dddd = new Date();
                                        dddd = moment(fpd).format('MM/DD/YYYY');
                                        let pom = 0;
                                        while (pom < 24) {
                                            jQuery(`.${y}.${pom}${days[y]} .tests`).filter(function() {
                                                let thiss = jQuery(this).attr('date');
                                                var increased = new Date(dddd);
                                                increased.setDate(increased.getDate() + 1);
                                                increased = moment(increased).format('MM/DD/YYYY');

                                                if (jQuery(this).attr('date') == increased) {
                                                    if (jQuery(this).text() === `${pom}:00`) {
                                                        jQuery(this).parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                        jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                        if (clickable) {
                                                            if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                                jQuery(this).parent().parent().parent().addClass('unavailable');
                                                            } else {
                                                                jQuery(this).parent().css('pointer-events', 'none');
                                                                jQuery(this).parent().addClass('booked');
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                            pom++;
                                        }
                                        fpd = new Date(fpd.setDate(fpd.getDate() + 1));
                                    }
                                }

                                nextHour = 0;
                                while (nextHour < endHour - 1) {
                                    jQuery(`.${preEndDay} .tests`).filter(function() {
                                        if (jQuery(this).attr('date') == endDate) {
                                            if (jQuery(this).text() == `${nextHour}:00`) {
                                                jQuery(this).parent().css("background", `${color}`);
                                                jQuery(this).parent().parent().parent().css("background", `${color}`);
                                                jQuery(this).parent().parent().parent().css("border-bottom-color", `${color}`);
                                                if (clickable) {
                                                    if (jQuery(this).parent().parent().parent().css("background-color") == 'rgb(155, 161, 163)') {
                                                        jQuery(this).parent().parent().parent().addClass('unavailable');
                                                    } else {
                                                        jQuery(this).parent().css('pointer-events', 'none');
                                                        jQuery(this).parent().addClass('booked');
                                                    }
                                                }
                                            }
                                        }
                                    });
                                    nextHour++;
                                }
                            }

                            jQuery(".tabela td").each(function() {
                                if (jQuery(this).hasClass('available') == false) {
                                    jQuery(this).css('background', '#9BA1A3');
                                    jQuery(this).css('border-bottom-color', '#9BA1A3');
                                }
                            });
                        </script>
                        <script type='text/javascript'>
                            // Store the current highest year
                            let highestYear = new Date().getFullYear();
                            // Add the current year to the list
                            jQuery('#getYearWidget').append('<option>' + highestYear + '</option>');
                            // Increment the years and add them to the list
                            for (var i = 1; i <= 10; i++) {
                                // Append the values (and increment the current highest year)
                                jQuery('#getYearWidget').append('<option>' + (++highestYear) + '</option>');
                            }

                            let selectMonth = new Date().getMonth();
                            jQuery(`#getMonthWidget option:eq(${selectMonth})`).prop("selected", "selected");
                            // for (let i = 0; i < selectMonth; i++) {
                            //     jQuery(`#getMonth option:eq(${i})`).hide();
                            // }


                            jQuery('#getYearWidget').on('change', function() {
                                let firstDay = parseInt(jQuery('#dateOverHours0').text());
                                let selectedYear = parseInt(jQuery('#getYearWidget option:selected').text());
                                let selectedMonth = jQuery('#getMonthWidget option:selected').val();
                                nextDate = new Date(selectedYear, selectedMonth - 1, firstDay);
                                nextDate = startOfWeek(nextDate);
                                selectedMonth = nextDate.getMonth();
                                jQuery(`#getMonthWidget option:eq(${selectedMonth})`).prop("selected", "selected");
                                jQuery('#monthOver').html(`${nextDate.getWeek()}`);

                                var overHourDate = nextDate;
                                for (let z = 0; z < 7; z++) {
                                    jQuery(`#dateOverHours${z}`).html(`${overHourDate.getDate()}`);
                                    jQuery(`#dateOverHours${z}`).attr('over-date', `${overHourDate}`);
                                    overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() + 1));
                                }

                                let next = new Date(nextDate.setDate(nextDate.getDate() - 1));

                                var asdedas = nextDate;
                                var curr = nextDate; // get current date
                                var a;
                                var notG;
                                for (var g = 0; g < timeFromAr.length; g++) {
                                    notG = parseInt(dayArray[g]);
                                    firstday = new Date(asdedas.setDate(curr.getDate() + notG));
                                    const f2 = "MM/DD/YYYY";
                                    firstday = moment(firstday).format(f2);
                                    for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                                        var slot1 = jQuery(`.${g}.${i}${days[g]} .tests`);
                                        a = slot1;
                                        a.attr("date", `${firstday}`);
                                    }
                                    asdedas.setDate(curr.getDate() - notG);
                                }

                                jQuery('.tabela .time-slot label').css('background', 'white');
                                jQuery('.tabela .time-slot label').css('pointer-events', '');
                                jQuery('.tabela .time-slot label').removeClass('booked');
                                jQuery('table.tabela tr td.available').css("background", 'white');
                                jQuery('table.tabela tr td.available').css("border-bottom-color", '#C0C3C3');
                                if (listingCategory != 'utstr') {
                                    for (var i = 0; i < waitingLength; i++) {
                                        var startDate = waiting[i][0];
                                        var endDate = waiting[i][1];
                                        var startHour = waiting[i][2];
                                        var endHour = waiting[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
                                        }
                                    }
                                    for (var i = 0; i < approvedLength; i++) {
                                        var startDate = approved[i][0];
                                        var endDate = approved[i][1];
                                        var startHour = approved[i][2];
                                        var endHour = approved[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
                                        }
                                    }
                                    for (var i = 0; i < unavailableLength; i++) {
                                        var startDate = unavailable[i][0];
                                        var endDate = unavailable[i][1];
                                        var startHour = unavailable[i][2];
                                        var endHour = unavailable[i][3];
                                        if (startDate === endDate) {
                                            paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                        } else if (startDate < endDate) {
                                            paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', true);
                                        }
                                    }
                                }
                                var currentDate = new Date();

                                for (var b = 0; b < 7; b++) {
                                    for (var i = 0; i < 24; i++) {
                                        jQuery(`.${b}.${i}${days[b]} .tests`).filter(function() {
                                            if (jQuery(this).text() === `${i}:00`) {
                                                let atrdate = new Date(jQuery(this).attr('date'));
                                                if (atrdate < currentDate) {
                                                    var cur_date = "<?php echo date('m/d/Y');?>";
                                                    if(jQuery(this).attr('date') == cur_date){
                                                        var cur_time = parseInt(currentDate.getHours());
                                                        var slot_time = parseInt(`${i}`);
                                                        if(cur_time > slot_time){

                                                            jQuery(this).parent().css("pointer-events", 'none');
                                                            jQuery(this).parent().css("opacity", '0');
                                                            jQuery(this).parent().parent().parent().css("background", '#9BA1A3');

                                                        }else{
                                                            jQuery(this).parent().css("opacity", '1');
                                                        }
                                                    }else{
                                                        jQuery(this).parent().css("pointer-events", 'none');
                                                        jQuery(this).parent().css("opacity", '0');
                                                        jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                    }    
                                                } else {
                                                    jQuery(this).parent().css("opacity", '1');
                                                }
                                            }
                                        });
                                    }
                                }

                                //White after bgColor green
                                jQuery('.available .time-slot label').each(function() {
                                    var a = jQuery(this).css("background-color");
                                    if (a == 'rgb(0, 132, 116)') {
                                        jQuery(this).parent().parent().css('background', `white`);
                                    }
                                });

                                setTimeout(() => {
                                    jQuery('.available .time-slot label').each(function() {
                                        var a = jQuery(this).css("background-color");
                                        if (a == 'rgb(255, 153, 0)') {
                                            jQuery(this).parent().parent().css('background', `#FF9900`);
                                        }
                                        if (a == 'rgb(218, 105, 122)') {
                                            jQuery(this).parent().parent().css('background', `#da697a`);
                                        }
                                    });
                                }, 500);
                                jQuery(".check-availability-table tr td").css("border", "1px solid black");
                            });

                            jQuery('#getMonthWidget').on('change', function() {
                                let firstDay = parseInt(jQuery('#dateOverHours0').text());
                                let selectedYear = parseInt(jQuery('#getYearWidget option:selected').text());
                                let selectedMonth = jQuery('#getMonthWidget option:selected').val();
                                nextDate = new Date(selectedYear, selectedMonth - 1, firstDay);
                                nextDate = startOfWeek(nextDate);
                                selectedMonth = nextDate.getMonth();
                                jQuery(`#getMonthWidget option:eq(${selectedMonth})`).prop("selected", "selected");
                                jQuery('#monthOver').html(`${nextDate.getWeek()}`);

                                var overHourDate = nextDate;
                                for (let z = 0; z < 7; z++) {
                                    jQuery(`#dateOverHours${z}`).html(`${overHourDate.getDate()}`);
                                    jQuery(`#dateOverHours${z}`).attr('over-date', `${overHourDate}`);
                                    overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() + 1));
                                }

                                let next = new Date(nextDate.setDate(nextDate.getDate() - 1));

                                var asdedas = nextDate;
                                var curr = nextDate; // get current date
                                var a;
                                var notG;
                                for (var g = 0; g < timeFromAr.length; g++) {
                                    notG = parseInt(dayArray[g]);
                                    firstday = new Date(asdedas.setDate(curr.getDate() + notG));
                                    const f2 = "MM/DD/YYYY";
                                    firstday = moment(firstday).format(f2);
                                    for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                                        var slot1 = jQuery(`.${g}.${i}${days[g]} .tests`);
                                        a = slot1;
                                        a.attr("date", `${firstday}`);
                                    }
                                    asdedas.setDate(curr.getDate() - notG);
                                }

                                jQuery('.tabela .time-slot label').css('background', 'white');
                                jQuery('.tabela .time-slot label').css('pointer-events', '');
                                jQuery('.tabela .time-slot label').removeClass('booked');
                                jQuery('table.tabela tr td.available').css("background", 'white');
                                jQuery('.tabela table tr td.available').css("border-bottom-color", '#C0C3C3');

                                for (var i = 0; i < waitingLength; i++) {
                                    var startDate = waiting[i][0];
                                    var endDate = waiting[i][1];
                                    var startHour = waiting[i][2];
                                    var endHour = waiting[i][3];
                                    if (startDate === endDate) {
                                        paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
                                    } else if (startDate < endDate) {
                                        paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
                                    }
                                }
                                for (var i = 0; i < approvedLength; i++) {
                                    var startDate = approved[i][0];
                                    var endDate = approved[i][1];
                                    var startHour = approved[i][2];
                                    var endHour = approved[i][3];
                                    if (startDate === endDate) {
                                        paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
                                    } else if (startDate < endDate) {
                                        paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
                                    }
                                }

                                for (var i = 0; i < unavailableLength; i++) {
                                    var startDate = unavailable[i][0];
                                    var endDate = unavailable[i][1];
                                    var startHour = unavailable[i][2];
                                    var endHour = unavailable[i][3];
                                    if (startDate === endDate) {
                                        paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
                                    } else if (startDate < endDate) {
                                        paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
                                    }
                                }

                                var currentDate = new Date();


                                for (var b = 0; b < 7; b++) {
                                    for (var i = 0; i < 24; i++) {
                                        jQuery(`.${b}.${i}${days[b]} .tests`).filter(function() {
                                            if (jQuery(this).text() === `${i}:00`) {
                                                let atrdate = new Date(jQuery(this).attr('date'));
                                                if (atrdate < currentDate) {
                                                    var cur_date = "<?php echo date('m/d/Y');?>";
                                                    if(jQuery(this).attr('date') == cur_date){
                                                        var cur_time = parseInt(currentDate.getHours());
                                                        var slot_time = parseInt(`${i}`);
                                                        if(cur_time > slot_time){

                                                            jQuery(this).parent().css("pointer-events", 'none');
                                                            jQuery(this).parent().css("opacity", '0');
                                                            jQuery(this).parent().parent().parent().css("background", '#9BA1A3');

                                                        }else{
                                                            jQuery(this).parent().css("opacity", '1');
                                                        }
                                                       
                                                    }else{
                                                        jQuery(this).parent().css("pointer-events", 'none');
                                                        jQuery(this).parent().css("opacity", '0');
                                                        jQuery(this).parent().parent().parent().css("background", '#9BA1A3');
                                                    }    
                                                } else {
                                                    jQuery(this).parent().css("opacity", '1');
                                                }
                                            }
                                        });
                                    }
                                }

                                //White after bgColor green
                                jQuery('.available .time-slot label').each(function() {
                                    var a = jQuery(this).css("background-color");
                                    if (a == 'rgb(0, 132, 116)') {
                                        jQuery(this).parent().parent().css('background', `white`);
                                    }
                                });

                                setTimeout(() => {
                                    jQuery('.available .time-slot label').each(function() {
                                        var a = jQuery(this).css("background-color");
                                        if (a == 'rgb(255, 153, 0)') {
                                            jQuery(this).parent().parent().css('background', `#FF9900`);
                                        }
                                        if (a == 'rgb(218, 105, 122)') {
                                            jQuery(this).parent().parent().css('background', `#da697a`);
                                        }
                                    });
                                }, 500);
                                jQuery(".check-availability-table tr td").css("border", "1px solid black");

                            });
                        </script>

                    <?php } else if ($post_meta['_listing_type'][0] == 'service') { ?>
                        <div class="col-lg-12">
                            <input type="text" class="time-picker flatpickr-input active" placeholder="<?php esc_html_e('Time', 'listeo_core') ?>" id="_hour" name="_hour" readonly="readonly">
                        </div>
                        <?php if (get_post_meta($post_id, '_end_hour', true)) : ?>
                            <div class="col-lg-12">
                                <input type="text" class="time-picker flatpickr-input active" placeholder="<?php esc_html_e('End Time', 'listeo_core') ?>" id="_hour_end" name="_hour_end" readonly="readonly">
                            </div>
                        <?php
                        endif;
                        $_opening_hours_status = get_post_meta($post_id, '_opening_hours_status', true);
                        $_opening_hours_status = '';
                        ?>
                        <script>
                            var availableDays = <?php if ($_opening_hours_status) {
                                                    echo json_encode($opening_hours, true);
                                                } else {
                                                    echo json_encode('', true);
                                                } ?>;
                        </script>

                    <?php } ?>

                    <?php $bookable_services = listeo_get_bookable_services($post_info->ID);

                    if (!empty($bookable_services)) : ?>

                        <!-- Panel Dropdown -->
                        <div class="col-lg-12">
                            <div class="panel-dropdown booking-services  <?php if (!is_user_logged_in()) {echo 'xoo-el-login-tgr';} ?>">
                                <a href="#"><?php esc_html_e('Extra Services', 'listeo_core'); ?> <span class="services-counter">0</span></a>
                                <div class="panel-dropdown-content padding-reset">
                                    <div class="panel-dropdown-scrollable">

                                        <!-- Bookable Services -->
                                        <div class="bookable-services">
                                            <?php
                                            $i = 0;
                                            $currency_abbr = get_option('listeo_currency');
                                            $currency_postion = get_option('listeo_currency_postion');
                                            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
                                            foreach ($bookable_services as $key => $service) {
                                                $i++; ?>
                                                <div class="single-service <?php if (isset($service['bookable_quantity'])) : ?>with-qty-btns<?php endif; ?>">

                                                    <input type="checkbox" class="bookable-service-checkbox" name="_service[<?php echo sanitize_title($service['name']); ?>]" value="<?php echo sanitize_title($service['name']); ?>" id="tag<?php echo esc_attr($i); ?>" />

                                                    <label for="tag<?php echo esc_attr($i); ?>">
                                                        <h5><?php echo esc_html($service['name']); ?></h5>
                                                        <span class="single-service-price"> <?php
                                                                                            if (empty($service['price']) || $service['price'] == 0) {
                                                                                                esc_html_e('Free', 'listeo_core');
                                                                                            } else {
                                                                                                $service['price'] +=  (intval($service['tax']) / 100) * intval($service['price']);
                                                                                                if ($currency_postion == 'before') {
                                                                                                    echo $currency_symbol . ' ';
                                                                                                }
                                                                                                echo esc_html($service['price']);
                                                                                                if ($currency_postion == 'after') {
                                                                                                    echo ' ' . $currency_symbol;
                                                                                                }
                                                                                            }
                                                                                            ?> (ink. mva)</span>
                                                    </label>

                                                    <?php if (isset($service['bookable_quantity'])) : ?>
                                                        <div class="qtyButtons">
                                                            <input type="text" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]" data-max="" class="" value="1">
                                                        </div>
                                                    <?php else : ?>
                                                        <input type="hidden" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]" data-max="" class="" value="1">
                                                    <?php endif; ?>

                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <!-- Bookable Services -->


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Panel Dropdown / End -->
                    <?php
                    endif;
                    $max_guests = get_post_meta($post_info->ID, "_max_guests", true);
                    $count_per_guest = get_post_meta($post_info->ID, "_count_per_guest", true);
                    if (get_option('listeo_remove_guests')) {
                        $max_guests = 1;
                    }
                    ?>
                    <!-- Panel Dropdown -->

                    <div class="col-lg-12" <?php if ($max_guests == 1) {
                                                echo 'style="display:none;"';
                                            } ?>>
                        <div class="panel-dropdown <?php if (!is_user_logged_in()) {echo 'xoo-el-login-tgr';} ?>">
                            <a href="#" ><?php esc_html_e('Guests', 'listeo_core') ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
                            <div class="panel-dropdown-content" style="width: 269px;">
                                <!-- Quantity Buttons -->
                                <div class="qtyButtons">
                                    <div class="qtyTitle"><?php esc_html_e('Antall', 'listeo_core') ?></div>
                                    <input type="text" name="qtyInput" data-max="<?php echo esc_attr($max_guests); ?>" class="adults <?php if ($count_per_guest) echo 'count_per_guest'; ?>" value="1">
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Panel Dropdown / End -->

                <?php } //eof if event 
                ?>

                <?php if ($post_meta['_listing_type'][0] == 'event') {
                    $max_tickets = (int) get_post_meta($post_info->ID, "_event_tickets", true);
                    $sold_tickets = (int) get_post_meta($post_info->ID, "_event_tickets_sold", true);
                    $av_tickets = $max_tickets - $sold_tickets;

                ?><input type="hidden" id="date-picker" readonly="readonly" class="date-picker-listing-<?php echo esc_attr($post_meta['_listing_type'][0]); ?>" autocomplete="off" placeholder="<?php esc_attr_e('Date', 'listeo_core'); ?>" value="<?php echo $post_meta['_event_date'][0]; ?>" listing_type="<?php echo $post_meta['_listing_type'][0]; ?>" />
                    <div class="col-lg-12">
                        <div class="panel-dropdown">
                            <a href="#"><?php esc_html_e('Tickets', 'listeo_core') ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
                            <div class="panel-dropdown-content" style="width: 269px;">
                                <!-- Quantity Buttons -->
                                <div class="qtyButtons">
                                    <div class="qtyTitle"><?php esc_html_e('Tickets', 'listeo_core') ?></div>
                                    <input type="text" name="qtyInput" <?php if ($max_tickets > 0) { ?>data-max="<?php echo esc_attr($av_tickets); ?>" <?php } ?> id="tickets" value="1">
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php $bookable_services = listeo_get_bookable_services($post_info->ID);

                    if (!empty($bookable_services)) : ?>

                        <!-- Panel Dropdown -->
                        <div class="col-lg-12">
                            <div class="panel-dropdown booking-services   <?php if (!is_user_logged_in()) {echo 'xoo-el-login-tgr';} ?>">
                                <a href="#"><?php esc_html_e('Extra Services', 'listeo_core'); ?> <span class="services-counter">0</span></a>
                                <div class="panel-dropdown-content padding-reset">
                                    <div class="panel-dropdown-scrollable">

                                        <!-- Bookable Services -->
                                        <div class="bookable-services">
                                            <?php
                                            $i = 0;
                                            $currency_abbr = get_option('listeo_currency');
                                            $currency_postion = get_option('listeo_currency_postion');
                                            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
                                            foreach ($bookable_services as $key => $service) {
                                                $i++; ?>
                                                <div class="single-service">
                                                    <input type="checkbox" class="bookable-service-checkbox" name="_service[<?php echo sanitize_title($service['name']); ?>]" value="<?php echo sanitize_title($service['name']); ?>" id="tag<?php echo esc_attr($i); ?>" />

                                                    <label for="tag<?php echo esc_attr($i); ?>">
                                                        <h5><?php echo esc_html($service['name']); ?></h5>
                                                        <span class="single-service-price"> <?php
                                                                                            if (empty($service['price']) || $service['price'] == 0) {
                                                                                                esc_html_e('Free', 'listeo_core');
                                                                                            } else {
                                                                                                if ($currency_postion == 'before') {
                                                                                                    echo $currency_symbol . ' ';
                                                                                                }
                                                                                                echo esc_html($service['price']);
                                                                                                if ($currency_postion == 'after') {
                                                                                                    echo ' ' . $currency_symbol;
                                                                                                }
                                                                                            }
                                                                                            ?></span>
                                                    </label>

                                                    <?php if (isset($service['bookable_quantity'])) : ?>
                                                        <div class="qtyButtons">
                                                            <input type="text" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]" data-max="" class="" value="1">
                                                        </div>
                                                    <?php else : ?>
                                                        <input type="hidden" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]" data-max="" class="" value="1">
                                                    <?php endif; ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <!-- Bookable Services -->


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Panel Dropdown / End -->
                    <?php
                    endif; ?>
                    <!-- Panel Dropdown / End -->
                <?php } ?>
        </div>
        <!-- Book Now -->
        <input type="hidden" id="listing_type" value="<?php echo $post_meta['_listing_type'][0]; ?>" />
        <input type="hidden" id="listing_id" value="<?php echo $post_info->ID; ?>" />
        <input id="booking" type="hidden" name="value" value="booking_form" />
        <?php if (get_post_meta($post_info->ID, '_discount', true) == 'on') { ?>
            <div class="col-lg-12 discount-dropdown" style="padding:0px;">
                <div class="panel-dropdown booking-services <?php if (!is_user_logged_in()) {echo ' xoo-el-login-tgr';} ?>">
                    <a href="#">Målgruppe <span class="services-counter-discount">%</span></a>
                    <div class="panel-dropdown-content padding-reset" style="width: 100px;">
                        <div class="panel-dropdown-scrollable">
                            <div class="bookable-services">
                                <?php
                                $users = ['Barn', 'Funksjonshemmede', 'Senior', 'Idrettslag', 'Ungdom', 'Medlem', 'Lag og foreninger', 'Trening (for organiserte)', 'Kamp (for organiserte)', 'Private', 'Bedrifter', 'Ansatte'];
                                foreach ($users as $user) {

                                    $userdata = get_userdata($user->ID);
                                    if((get_post_meta($post_info->ID, $user))[0] != ""){

                                     ?>
                                    <input class="discount-input" style="float:left;" type="radio" name="discount" data-id="<?php echo $user; ?>" value="<?php echo $user; ?>"><label style="padding: 0px 0px 0px 15px; position: relative; bottom: 5px; font-size: 17px; overflow: hidden;" for="<?php echo $user; ?>"><?php echo $user;
                                                                                                                                                                                                                                                                                                                        echo ' (' . (get_post_meta($post_info->ID, $user))[0] . '%)'; ?></label></input>
                                <?php } }

                                ?>
                                <input class="discount-input" style="float:left;" type="radio" name="discount" data-id="none" value="none"><label style="padding: 0px 0px 0px 15px; position: relative; bottom: 5px; font-size: 17px; overflow: hidden;" for="none">None (0%)</label></input>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php if (is_user_logged_in()) :

            if ($post_meta['_listing_type'][0] == 'event') {
                $book_btn = esc_html__('Make a Reservation', 'listeo_core');
            } else {
                if (get_post_meta($post_info->ID, '_instant_booking', true)) {
                    $book_btn = esc_html__('Book Now', 'listeo_core');
                } else {
                    $book_btn = esc_html__('Request Booking', 'listeo_core');
                }
            }  ?>

            <a href="javascript:void(0)" class="button book-now fullwidth margin-top-5">
                <div class="loadingspinner"></div><span class="book-now-text"><?php echo $book_btn; ?></span>
            </a>
            <?php else :
            $popup_login = get_option('listeo_popup_login', 'ajax');
            if ($popup_login == 'ajax') { ?>

                <a href="" class="xoo-el-login-tgr button fullwidth margin-top-5 popup-with-zoom-anim book-now-notloggedin">
                    <div class="loadingspinner"></div><span class="book-now-text"><?php esc_html_e('Login to Book', 'listeo_core') ?></span>
                </a>

            <?php } else {

                $login_page = get_option('listeo_profile_page'); ?>
                <a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="button fullwidth margin-top-5 book-now-notloggedin">
                    <div class="loadingspinner"></div><span class="book-now-text"><?php esc_html_e('Login To Book', 'listeo_core') ?></span>
                </a>
            <?php } ?>

        <?php endif; ?>

      <!--   <p style="text-align:center;">Du blir ikke belastet ennå</p> -->

        <?php if ($post_meta['_listing_type'][0] == 'event' && isset($post_meta['_event_date'][0])) { ?>
            <div class="booking-event-date">
                <strong>Event date: </strong>
                <span><?php

                        $_event_datetime = $post_meta['_event_date'][0];
                        $_event_date = list($_event_datetime) = explode(' -', $_event_datetime);

                        echo $_event_date[0]; ?></span>
            </div>
        <?php } ?>

        <div class="booking-normal-price" style="display:none;">
            <?php
            $currency_abbr = get_option('listeo_currency');
            $currency_postion = get_option('listeo_currency_postion');
            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
            ?>
            <strong><?php esc_html_e('Valgt tid (ink. mva)', 'listeo_core'); ?></strong>
            <span>
                <?php if ($currency_postion == 'before') {
                    echo $currency_symbol;
                } ?>
                <?php if ($currency_postion == 'after') {
                    echo $currency_symbol;
                } ?>
            </span>
        </div>

        <div class="booking-services-cost" style="display:none;">
            <?php
            $currency_abbr = get_option('listeo_currency');
            $currency_postion = get_option('listeo_currency_postion');
            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
            ?>
            <strong><?php esc_html_e('Tilleggstjenester (ink. mva)', 'listeo_core'); ?></strong>
            <span>
                <?php if ($currency_postion == 'before') {
                    echo $currency_symbol;
                } ?>
                <?php if ($currency_postion == 'after') {
                    echo $currency_symbol;
                } ?>
            </span>
        </div>

        <div class="booking-estimated-cost our" <?php if ($post_meta['_listing_type'][0] != 'event') { ?>style="display: none;" <?php } ?>>
            <strong class="asd">Total mva</strong>
            <div class="tax-span"></div>
        </div>

        <div class="booking-estimated-cost" <?php if ($post_meta['_listing_type'][0] != 'event') { ?>style="display: none;" <?php } ?>>
            <?php
            $currency_abbr = get_option('listeo_currency');
            $currency_postion = get_option('listeo_currency_postion');
            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
            ?>
            <strong><?php esc_html_e('Totalsum (ink. mva)', 'listeo_core'); ?></strong>
            <span>
                <?php if ($currency_postion == 'before') {
                    echo $currency_symbol;
                } ?>
                <?php
                if ($post_meta['_listing_type'][0] == 'event') {
                    $reservation_fee = (float) get_post_meta($post_info->ID, '_reservation_price', true);
                    $normal_price = (float) get_post_meta($post_info->ID, '_normal_price', true);

                    echo $reservation_fee + $normal_price;
                } else echo '0' ?>
                <?php if ($currency_postion == 'after') {
                    echo $currency_symbol;
                } ?></span>
        </div>

        <div class="free-booking" <?php if ($post_meta['_listing_type'][0] != 'event') { ?>style="display: none;" <?php } ?>>
            <strong><?php esc_html_e('Total Cost', 'listeo_core'); ?></strong>
            <span>GRATIS</span>
        </div>
        <div class="booking-error-message" style="display: none;">
            <?php esc_html_e('Unfortunately this request can\'t be processed. Try different dates please.', 'listeo_core'); ?>
        </div>
        </form>
        <?php

        echo $after_widget;

        $content = ob_get_clean();

        echo $content;

        $this->cache_widget($args, $content);
	}
}

/**
 * Booking Widget
 */
class Listeo_Core_Opening_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->widget_cssclass    = 'listeo_core boxed-widget opening-hours margin-bottom-35';
		$this->widget_description = __( 'Shows Opening Hours.', 'listeo_core' );
		$this->widget_id          = 'widget_opening_hours';
		$this->widget_name        =  __( 'Listeo Opening Hours', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Opening Hours', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		

		ob_start();
		
		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
		$packages_disabled_modules = get_option('listeo_listing_packages_options',array());
			
		if ( $queried_object ) {
		    $post_id = $queried_object->ID;

			
			if(empty($packages_disabled_modules)) {
				$packages_disabled_modules = array();
			}

			$user_package = get_post_meta( $post_id,'_user_package_id',true );
			if($user_package){
				$package = listeo_core_get_user_package( $user_package );
			}
			$listing_type = get_post_meta( $post_id, '_listing_type', true );
		}

		if( !$listing_type  == 'service') {  
			return;
		}

		if( in_array('option_opening_hours',$packages_disabled_modules) ){ 
				
			if( isset($package) && $package->has_listing_opening_hours() != 1 ){
				return;
			}
		}
		$_opening_hours_status = get_post_meta($post_id, '_opening_hours_status',true);
		if(!$_opening_hours_status){
			return;
		}
		$has_hours = false;
		//check if has any horus saved
		$days = listeo_get_days(); 
		foreach ($days as $d_key => $value) {
				$opening_day = get_post_meta( $post_id, '_'.$d_key.'_opening_hour', true ); 
				$closing_day = get_post_meta( $post_id, '_'.$d_key.'_closing_hour', true ); 

				if( (!empty($opening_day) && $opening_day != "Closed")  || ( !empty($closing_day) && $closing_day != "Closed")) { 
					$has_hours = true;
				}
			}
		if(!$has_hours) {
			return;
		}
		echo $before_widget;
            if( listeo_check_if_open() ){ ?>
                <div class="listing-badge now-open"><?php esc_html_e('Now Open','listeo_core'); ?></div>
            <?php } else { ?>
                <div class="listing-badge now-closed"><?php esc_html_e('Now Closed','listeo_core'); ?></div>
        <?php 
        } 
		if ( $title ) {		
			echo $before_title.'<i class="sl sl-icon-clock"></i> ' . $title . $after_title; 
		} 
		?>
		<ul>
			<?php
			$clock_format = get_option('listeo_clock_format');

			foreach ($days as $d_key => $value) {
				$opening_day = get_post_meta( $post_id, '_'.$d_key.'_opening_hour', true ); 
				$closing_day = get_post_meta( $post_id, '_'.$d_key.'_closing_hour', true ); 

				?>
					
					<?php 

					if(is_array($opening_day)){	
						if(!empty($opening_day[0])) :

							echo '<li>'; echo esc_html($value); 
						
							echo '<span>';
							foreach ($opening_day as $key => $opening) {
								if(!empty($opening)){


									$closing = $closing_day[$key];
									
									if( $clock_format == 12 ){
										if(substr($opening, -1) !='M' && $opening != 'Closed'){
											$opening = DateTime::createFromFormat('H:i', $opening);
											if($opening){
												$opening = $opening->format('h:i A');
											}			
										}

										if(substr($closing, -1)!='M' && $closing != 'Closed'){
											
											$closing = DateTime::createFromFormat('H:i', $closing);
											if($closing){
												$closing = $closing->format('h:i A');
											}
											if($closing == '00:00') { $closing = '24:00'; }
										}
									} 
								
								?>
								
									<?php echo esc_html($opening); ?> 
									- 
									<?php  
									if( $clock_format == 12 && $closing == '12:00 AM'){
										echo  '12:00 PM';
									} else if ($clock_format != 12 && $closing == '00:00'){
										echo  '24:00';
									} else {
										echo esc_html($closing); 	
									}
									echo '<br>';
									?>
							<?php }
						}

							echo ' </span></li>';
						else: ?>
							<li><?php echo $value; ?><span><?php esc_html_e('Closed','listeo_core') ?></span>
						<?php endif;

					} else {

						//not array, old listings
						if(!empty($opening_day) && !empty($closing_day)) {
						echo '<li>'; echo esc_html($value); 
							if( $clock_format == 12 ){
								if(substr($opening_day, -1) !='M' && $opening_day != 'Closed'){
									$opening_day = DateTime::createFromFormat('H:i', $opening_day)->format('h:i A');			
								}

								if(substr($closing_day, -1)!='M' && $closing_day != 'Closed'){

									$closing_day = DateTime::createFromFormat('H:i', $closing_day)->format('h:i A');

									if($closing_day == '00:00') { $closing_day = '24:00'; }
								}
							} ?>
							<span>
								<?php echo esc_html($opening_day); ?> 
								- 
								<?php  
								if( $clock_format == 12 && $closing_day == '12:00 AM'){
									echo  '12:00 PM';
								} else if ($clock_format != 12 && $closing_day == '00:00'){
									echo  '24:00';
								} else {
									echo esc_html($closing_day); 	
								}
								
								?> </span>
						<?php } else { ?>
							<li><?php echo $value; ?><span><?php esc_html_e('Closed','listeo_core') ?></span>
						<?php } ?>

						</li>
					<?php } 
						 ?>
				

			<?php } //end foreach ?>
		</ul>
				
		<?php
		

		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		
	}
}


class Listeo_Core_Classified_Owner_Widget extends Listeo_Core_Widget {

	public function __construct() {

		$this->widget_cssclass    = 'listeo_core widget_listing_classified_owner boxed-widget margin-bottom-35';
		$this->widget_description = __( 'Shows Listing Owner info on Classified ad.', 'listeo_core' );
		$this->widget_id          = 'widget_classified_listing_owner';
		$this->widget_name        =  __( 'Listeo Classified Owner Widget', 'listeo_core' );
		$this->settings           = array(
			
			'phone' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Phone number', 'listeo_core' )
			),
			'loggedin' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Show Phone to logged in users only', 'listeo_core' )
			),
	
			'contact' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Show Send message button', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}




	public function widget( $args, $instance ) {
		// if ( $this->get_cached_widget( $args ) ) {
		// 	return;
		// }

		ob_start();

		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
		if(!$queried_object){
			return;
		}
		$owner_id = $queried_object->post_author;
	
		if(!$owner_id) {
			return;
		}
		$owner_data = get_userdata( $owner_id );
		if ( $queried_object ) {
		    $post_id = $queried_object->ID;
			$listing_type = get_post_meta( $post_id, '_listing_type', true );
		}
		
		if($listing_type != 'classifieds'){
			return;
		}
		echo $before_widget;
          
          
		$show_phone = (isset($instance['phone']) && !empty($instance['phone'])) ? true : false ;
		$show_loggedin = (isset($instance['loggedin']) && !empty($instance['loggedin'])) ? true : false ;
		
		$visibility_setting = get_option('listeo_user_contact_details_visibility'); // hide_all, show_all, show_logged, 
		if($visibility_setting == 'hide_all') {
			$show_phone = false;
		} elseif ($visibility_setting == 'show_all') {
			$show_phone = true;
		} else {
			if(is_user_logged_in() ){
				if($visibility_setting == 'show_logged'){
					$show_phone = true;
				} else {
					$show_phone = false;
				}
			} else {
				$show_phone = false;
			}
		}	

		if($show_loggedin){
			if(is_user_logged_in() ){
				$show_phone = true;
			} else {
				$show_phone = false;
			}
		}

		$registered_date = get_the_author_meta( 'user_registered', $owner_id );
		?>
		


		<div class="classifieds-widget">
		 	<div class="classifieds-user">
		 		<div class="classifieds-user-avatar"><a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>"><?php echo get_avatar( $owner_id, 56 );  ?></a></div>
		 		<div class="classifieds-user-details">	
		 			<h3><?php echo listeo_get_users_name($owner_id); ?></h3>
		 			<span><?php esc_html_e('User since '); echo date_i18n(  get_option( 'date_format' ), strtotime($registered_date)); ?> </span>
		 			<a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>"><?php esc_html_e('More ads from this user '); ?> <i class="fa fa-chevron-right"></i></a>
		 		</div>
		 	</div>
		 

			<div class="classifieds-widget-buttons">
					
				<?php 
					

			 	if($show_phone) { 
					
					if(isset($owner_data->phone) && !empty($owner_data->phone)): ?>
					<a class="call-btn" href="tel:<?php echo esc_attr($owner_data->phone); ?>"><?php esc_html_e('Call', 'listeo_core'); ?></a>
				<?php endif; 
				} else { ?>
					<a class="call-btn sign-in popup-with-zoom-anim" href="#sign-in-dialog"><?php esc_html_e('Login to Call', 'listeo_core'); ?></a>
				<?php }
					if(is_user_logged_in()) {
						if((isset($instance['contact']) && !empty($instance['contact']))) : ?>
						<!-- Reply to review popup -->
						<div id="small-dialog" class="zoom-anim-dialog mfp-hide">
							<div class="small-dialog-header">
								<h3><?php esc_html_e('Send Message', 'listeo_core'); ?></h3>
							</div>
							<div class="message-reply margin-top-0">
								<form action="" id="send-message-from-widget" data-listingid="<?php echo esc_attr($post_id); ?>">
									<textarea 
									required
									data-recipient="<?php echo esc_attr($owner_id); ?>"  
									data-referral="listing_<?php echo esc_attr($post_id); ?>"  
									cols="40" id="contact-message" name="message" rows="3" placeholder="<?php esc_attr_e('Your message to ','listeo_core'); echo $owner_data->first_name; ?>"></textarea>
									<button class="button">
									<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send Message', 'listeo_core'); ?></button>	
									<div class="notification closeable success margin-top-20"></div>

								</form>
								
							</div>
						</div>


						<a href="#small-dialog" class="send-message-to-owner button  popup-with-zoom-anim"><?php esc_html_e('Send Message', 'listeo_core'); ?></a>
						<?php endif;
					} else { ?>
						<a href="#sign-in-dialog" class="sign-in button  popup-with-zoom-anim"><?php esc_html_e('Send Message', 'listeo_core'); ?></a>
					<?php }; ?>
				
		
			</div>
			
			
		
		</div>
			<?php 
		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}


	
	////

}

//
// 
// 


/**
 * Booking Widget
 */
class Listeo_Core_Owner_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->widget_cssclass    = 'listeo_core widget_listing_owner boxed-widget margin-bottom-35';
		$this->widget_description = __( 'Shows Listing Owner box.', 'listeo_core' );
		$this->widget_id          = 'widget_listing_owner';
		$this->widget_name        =  __( 'Listeo Owner Widget', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Hosted By', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			'phone' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Phone number', 'listeo_core' )
			),
			'email' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Email', 'listeo_core' )
			),
			'bio' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Biographical info', 'listeo_core' )
			),
			'social' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Social Sites profiles', 'listeo_core' )
			),
			'contact' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Show Send message button', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		// if ( $this->get_cached_widget( $args ) ) {
		// 	return;
		// }

		ob_start();

		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
		if(!$queried_object){
			return;
		}
		$owner_id = $queried_object->post_author;
	
		if(!$owner_id) {
			return;
		}
		$owner_data = get_userdata( $owner_id );
		if ( $queried_object ) {
		     $post_id = $queried_object->ID;
			$listing_type = get_post_meta( $post_id, '_listing_type', true );
		}
		
		if( $listing_type == 'classifieds' ){
			return;
		}
		
		echo $before_widget;
            
		if ( $title ) {	?>
			<div class="hosted-by-title">
				<h4><span><?php echo $title; ?></span> <a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>">
					<?php echo listeo_get_users_name($owner_id); ?></a></h4>
				<a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>" class="hosted-by-avatar"><?php echo get_avatar( $owner_id, 56 );  ?></a>
			</div>
			
		<?php } 
		$show_bio = (isset($instance['bio']) && !empty($instance['bio'])) ? true : false ;

		if( $show_bio && !empty($owner_data->user_description) ){
			?>
			<div class="hosted-by-bio">
				<?php echo wpautop(esc_html($owner_data->user_description)); ?>	
			</div>
			

			<?php
		}
		
		$show_email = (isset($instance['email']) && !empty($instance['email'])) ? true : false ;
		$show_phone = (isset($instance['phone']) && !empty($instance['phone'])) ? true : false ;
		$show_social = (isset($instance['social']) && !empty($instance['social'])) ? true : false ;
		$visibility_setting = get_option('listeo_user_contact_details_visibility'); // hide_all, show_all, show_logged, show_booked,  
		if($visibility_setting == 'hide_all') {
			$show_details = false;
		} elseif ($visibility_setting == 'show_all') {
			$show_details = true;
		} else {
			if(is_user_logged_in() ){
				if($visibility_setting == 'show_logged'){
					$show_details = true;
				} else {
					$show_details = false;
				}
			} else {
				$show_details = false;
			}
		}	
		if($show_details){
			if(  $show_email || $show_phone ) {  ?>
				<ul class="listing-details-sidebar">
					<?php if($show_phone) {  ?>
						<?php if(isset($owner_data->phone) && !empty($owner_data->phone)): ?>
							<li><i class="sl sl-icon-phone"></i> <?php echo esc_html($owner_data->phone); ?></li>
						<?php endif; 
					} 
					if($show_email) { 	
						if(isset($owner_data->user_email)): $email = $owner_data->user_email; ?>
							<li><i class="fa fa-envelope"></i><a href="mailto:<?php echo esc_attr($email);?>"><?php echo esc_html($email);?></a></li>
						<?php endif; ?>
					<?php } ?>
					
				</ul>
			<?php }
		} else { 
			if($visibility_setting != 'hide_all') { ?>
			<p id="owner-widget-not-logged-in"><?php printf( esc_html__( 'Please %s sign %s in to see contact details.', 'listeo_core' ), '<a href="#sign-in-dialog" class="sign-in popup-with-zoom-anim">', '</a>' ) ?></p>
		<?php } 
		}?>
		<?php if($show_details && $show_social){ ?>
			<ul class="listing-details-sidebar social-profiles">
				<?php if(isset($owner_data->twitter) && !empty($owner_data->twitter)) : ?><li><a href="<?php echo esc_url($owner_data->twitter) ?>" class="twitter-profile"><i class="fa fa-twitter"></i> Twitter</a></li><?php endif; ?>
				<?php if(isset($owner_data->facebook) && !empty($owner_data->facebook)) : ?><li><a href="<?php echo esc_url($owner_data->facebook) ?>" class="facebook-profile"><i class="fa fa-facebook-square"></i> Facebook</a></li><?php endif; ?>
				<?php if(isset($owner_data->instagram) && !empty($owner_data->instagram)) : ?><li><a href="<?php echo esc_url($owner_data->instagram) ?>" class="instagram-profile"><i class="fa fa-instagram"></i> Instagram</a></li><?php endif; ?>
				<?php if(isset($owner_data->linkedin) && !empty($owner_data->linkedin)) : ?><li><a href="<?php echo esc_url($owner_data->linkedin) ?>" class="linkedin-profile"><i class="fa fa-linkedin"></i> LinkedIn</a></li><?php endif; ?>
				<?php if(isset($owner_data->youtube) && !empty($owner_data->youtube)) : ?><li><a href="<?php echo esc_url($owner_data->youtube) ?>" class="youtube-profile"><i class="fa fa-youtube"></i> YouTube</a></li><?php endif; ?>
				<?php if(isset($owner_data->whatsapp) && !empty($owner_data->whatsapp)) : ?><li><a href="<?php if(strpos($owner_data->whatsapp, 'http') === 0) { echo esc_url($owner_data->whatsapp); } else { echo "https://wa.me/".esc_attr($owner_data->whatsapp); } ?>" class="whatsapp-profile"><i class="fa fa-whatsapp"></i> WhatsApp</a></li><?php endif; ?>
				<?php if(isset($owner_data->skype) && !empty($owner_data->skype)) : ?><li>
					<a href="<?php if(strpos($owner_data->skype, 'http') === 0) { echo esc_url($owner_data->skype); } else { echo "skype:+".$owner_data->skype."?call"; } ?>" class="skype-profile"><i class="fa fa-skype"></i> Skype</a></li><?php endif; ?>
				
				<!-- <li><a href="#" class="gplus-profile"><i class="fa fa-google-plus"></i> Google Plus</a></li> -->
			</ul>
		<?php } ?>
			<?php 
			if(is_user_logged_in()):
				if((isset($instance['contact']) && !empty($instance['contact']))) : ?>
				<!-- Reply to review popup -->
				<div id="small-dialog" class="zoom-anim-dialog mfp-hide">
					<div class="small-dialog-header">
						<h3><?php esc_html_e('Send Message', 'listeo_core'); ?></h3>
					</div>
					<div class="message-reply margin-top-0">
						<form action="" id="send-message-from-widget" data-listingid="<?php echo esc_attr($post_id); ?>">
							<textarea 
							required
							data-recipient="<?php echo esc_attr($owner_id); ?>"  
							data-referral="listing_<?php echo esc_attr($post_id); ?>"  
							cols="40" id="contact-message" name="message" rows="3" placeholder="<?php esc_attr_e('Your message to ','listeo_core'); echo $owner_data->first_name; ?>"></textarea>
							<button class="button">
							<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send Message', 'listeo_core'); ?></button>	
							<div class="notification closeable success margin-top-20"></div>

						</form>
						
					</div>
				</div>


				<a href="#small-dialog" class="send-message-to-owner button popup-with-zoom-anim"><i class="sl sl-icon-envelope-open"></i> <?php esc_html_e('Send Message', 'listeo_core'); ?></a>
				<?php endif; ?>
			<?php endif; ?>
				
		<?php
		

		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Core class used to implement a Recent Posts widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Listeo_Recent_Posts extends WP_Widget {

    /**
     * Sets up a new Recent Posts widget instance.
     *
     * @since 2.8.0
     * @access public
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'listeo_recent_entries',
            'description' => __( 'Your site&#8217;s most recent Posts.','listeo' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'listeo-recent-posts', __( 'Listeo Recent Posts','listeo' ), $widget_ops );
        $this->alt_option_name = 'listeo_recent_entries';
    }

    /**
     * Outputs the content for the current Recent Posts widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Recent Posts widget instance.
     */
    public function widget( $args, $instance ) {
        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts','listeo' );

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number )
            $number = 5;
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        /**
         * Filters the arguments for the Recent Posts widget.
         *
         * @since 3.4.0
         *
         * @see WP_Query::get_posts()
         *
         * @param array $args An array of arguments used to retrieve the recent posts.
         */
        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true
        ) ) );

        if ($r->have_posts()) :
        ?>
        <?php echo $args['before_widget']; ?>
        <?php if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        } ?>
        <ul class="widget-tabs">
        <?php while ( $r->have_posts() ) : $r->the_post(); ?>
            <li>
                <div class="widget-content">
                    <?php if ( has_post_thumbnail() ) { ?>
                    <div class="widget-thumb">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('listeo-post-thumb'); ?></a>
                    </div>
                    <?php } ?>

                    <div class="widget-text">
                        <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                        <span><?php echo get_the_date(); ?></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
        <?php endwhile; ?>
        </ul>
        <?php echo $args['after_widget']; ?>
        <?php
        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();

        endif;
    }

    /**
     * Handles updating the settings for the current Recent Posts widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        return $instance;
    }

    /**
     * Outputs the settings form for the Recent Posts widget.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','listeo' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:','listeo' ); ?></label>
        <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
        <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?','listeo' ); ?></label></p>
<?php
    }
}



/**
 * Booking Widget
 */
class Listeo_Coupon_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->widget_cssclass    = 'listeo_core boxed-widget coupon-widget margin-bottom-35';
		$this->widget_description = __( 'Shows Listing Coupon.', 'listeo_core' );
		$this->widget_id          = 'widget_coupon';
		$this->widget_name        =  __( 'Listeo Coupon Widget ', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Coupon', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		

		ob_start();
		
		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
		
		$packages_disabled_modules = get_option('listeo_listing_packages_options',array());
		
		if ( $queried_object ) {
		    $post_id = $queried_object->ID;
			$listing_type = get_post_meta( $post_id, '_listing_type', true );
			
			if(empty($packages_disabled_modules)) {
				$packages_disabled_modules = array();
			}

			$user_package = get_post_meta( $post_id,'_user_package_id',true );
			if($user_package){
				$package = listeo_core_get_user_package( $user_package );
			}
		}

	   
		if( in_array('option_coupons',$packages_disabled_modules) ){ 
				
			if( isset($package) && $package->has_listing_coupons() != 1 ){
				return;
			}
		}
		$_opening_hours_status = get_post_meta($post_id, '_coupon_section_status',true);
		if(!$_opening_hours_status){
			return;
		}
		//get coupon

		$coupon_id =  get_post_meta($post_id, '_coupon_for_widget',true);
		if(!($coupon_id)){
			return false;
		}

		$coupon_post = get_post($coupon_id);
		//$coupon = new WC_Coupon($coupon_id);
		if(!$coupon_post){
			return;
		}

		if($coupon_post){
			$coupon_data = new WC_Coupon($coupon_id);
		}

		

		//echo $before_widget;
           	$coupon_bg = get_post_meta($coupon_id,'coupon_bg-uploader-id',true);
			$coupon_bg_url = wp_get_attachment_url($coupon_bg); 
	
		?>
				<!-- Coupon Widget -->
			<div class="coupon-widget"  style="<?php if($coupon_bg): ?>background-image: url(<?php echo esc_url($coupon_bg_url); ?>); <?php endif; ?> margin:20px 0px;">
				<a class="coupon-top">
					
					<?php $coupon_amount = wc_format_localized_price( $coupon_data->get_amount());  
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
					
					if( $coupon_data->get_discount_type() == 'fixed_product') { ?>
						<h3><?php echo sprintf( esc_html__('Get %1$s%2$s discount!','listeo_core'),$coupon_amount,$currency_symbol); ?></h3>
					<?php } else { ?>
						<h3><?php echo sprintf( esc_html__('Get %1$s%% discount!','listeo_core'),$coupon_amount); ?></h3>
					<?php } ?>

					
					<?php
					$expiry_date = $coupon_data->get_date_expires();
					if($expiry_date) : ?>
					<div class="coupon-valid-untill"><?php esc_html_e('Expires','listeo_core'); ?> <?php echo esc_html( $expiry_date->date_i18n( 'F j, Y' ) );  ?></div>
					<?php endif; ?>
					<?php if($coupon_data->get_description()) : ?>
						<div class="coupon-how-to-use"><?php echo $coupon_data->get_description(); ?></div>
					<?php endif; ?>
				</a>
				<div class="coupon-bottom">
					<div class="coupon-scissors-icon"></div>
					<div class="coupon-code"><?php echo $coupon_data->get_code(); ?></div>
				</div>
			</div>

		
				
		<?php
		

		//echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		
	}
}

register_widget( 'Listeo_Core_Featured_Properties' );
register_widget( 'Listeo_Core_Bookmarks_Share_Widget' );
register_widget( 'Listeo_Core_Booking_Widget' );
register_widget( 'Listeo_Core_Search_Widget' );
register_widget( 'Listeo_Core_Opening_Widget' );
register_widget( 'Listeo_Core_Owner_Widget' );
register_widget( 'Listeo_Core_Classified_Owner_Widget' );
register_widget( 'Listeo_Core_Contact_Vendor_Widget' );
register_widget( 'Listeo_Recent_Posts' );
register_widget( 'Listeo_Coupon_Widget' );


function custom_get_post_author_email($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	$email = get_post_meta($post_id,'_email',true);
	if(!$email){
		$object = get_post( $post_id );
		//just get the email of the listing author
		$owner_ID = $object->post_author;
		//retrieve the owner user data to get the email
		$owner_info = get_userdata( $owner_ID );
		if ( false !== $owner_info ) {
			$email = $owner_info->user_email;
		}
	}
  	return $email;
}
add_shortcode('CUSTOM_POST_AUTHOR_EMAIL', 'custom_get_post_author_email');
add_shortcode('LISTING_OWNER_EMAIL', 'custom_get_post_author_email');

//_email
function custom_get_post_listing_title($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	if($post_id){
		$value = get_the_title($post_id);
	}
  return $value;
}
add_shortcode('LISTING_TITLE', 'custom_get_post_listing_title');

//_email
function custom_get_post_listing_url($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	if($post_id){
		$value = get_permalink($post_id);
	}
  return $value;
}
add_shortcode('LISTING_URL', 'custom_get_post_listing_url');