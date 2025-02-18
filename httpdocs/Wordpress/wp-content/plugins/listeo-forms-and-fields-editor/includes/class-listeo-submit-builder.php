<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Listeo_Submit_Editor {
/**
     * Stores static instance of class.
     *
     * @access protected
     * @var Listeo_Submit The single instance of the class
     */
    protected static $_instance = null;

    protected $fields = array();
    /**
     * Returns static instance of class.
     *
     * @return self
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct($version = '1.0.0') {
  
       	add_action( 'admin_menu', array( $this, 'add_options_page' ) ); //create tab pages
      	add_filter('submit_listing_form_fields', array( $this,'add_listeo_submit_listing_form_fields_form_editor')); 
       
      	add_action('wp_ajax_editor_load_field', array($this, 'editor_load_field'));
      	add_action('wp_ajax_listeo_editor_save_field', array($this, 'editor_save_field'));
      	add_action('wp_ajax_listeo_editor_get_items', array($this, 'editor_get_items'));
      	 /* wp_enqueue_style( 'chosen_styles', 'https://harvesthq.github.io/chosen/chosen.css', false );
		  wp_enqueue_script( 'jquery' );
		  wp_enqueue_script( 'chosen_js', 'https://harvesthq.github.io/chosen/chosen.jquery.js', array('jquery'), null, true );*/
      	//add_action('wp_ajax_listeo_editor_delete_field', array($this, 'editor_delete_field'));


    }

    function add_listeo_submit_listing_form_fields_form_editor($r){
        $fields =  get_option('listeo_submit_form_fields');
        
        if(!empty($fields)) { $r = $fields; }
      
        return $r;
    }

    function init_fields(){
    	if ( $this->fields ) {
			return;
		}

		$scale = get_option( 'scale', 'sq ft' );
		$currency = get_option('listeo_currency');
    	
		$this->fields = array(
			'basic_info' => array(
				'title' 	=> __('Basic Information','listeo_core'),
				'class' 	=> '',
				'icon' 		=> 'sl sl-icon-doc',
				'fields' 	=> array(
					'listing_title' => array(
							'label'       => __( 'Listing Title', 'listeo_core' ),
							'type'        => 'text',
							'name'       => 'listing_title',
							'tooltip'	  => __( 'Type title that will also contains an unique feature of your listing (e.g. renovated, air contidioned)', 'listeo_core' ),
							'required'    => true,
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							
						),
						'listing_category' => array(
							'label'       => __( 'Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'listing_category',
							'taxonomy'	  => 'listing_category',
							'tooltip'	  => __( 'This is main listings category', 'listeo_core' ),
							'priority'    => 10,
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'multi'    => false,
							'for_type'	  => ''
						),
						'event_category' => array(
							'label'       => __( 'Event Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'event_category',
							'taxonomy'	  => 'event_category',
							'tooltip'	  => __( 'Those are categories related to your listing type', 'listeo_core' ),
							'priority'    => 10,
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'multi'    => false,
							'for_type'	  => 'event'
						),
						'service_category' => array(
							'label'       => __( 'Service Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'service_category',
							'taxonomy'	  => 'service_category',
							'priority'    => 10,
							'multi'    => false,
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'for_type'	  => 'service'
						),
						'rental_category' => array(
							'label'       => __( 'Rental Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'rental_category',
							'taxonomy'	  => 'rental_category',
							'priority'    => 10,
						'multi'    => false,
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'for_type'	  => 'rental'
						),
						'keywords' => array(
							'label'       => __( 'Keywords', 'listeo_core' ),
							'type'        => 'text',
							'tooltip'	  => __( 'Maximum of 15 keywords related with your business, separated by coma' , 'listeo_core' ),
							'placeholder' => '',
							'name'        => 'keywords',
							
							'priority'    => 10,
							
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'for_type'	  => ''
						),
						
						'listing_feature' => array(
							'label'       	=> __( 'Other Features', 'listeo_core' ),
							'type'        	=> 'term-checkboxes',
							'taxonomy'		=> 'listing_feature',
							'name'			=> 'listing_feature',
							'class'		  	 => 'chosen-select-no-single',
							'default'    	 => '',
							'priority'    	 => 2,
							'required'    => false,
							'for_type'	  => ''
						),

				),
			),
			
			'location' =>  array(
				'title' 	=> __('Location','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'icon' 		=> 'sl sl-icon-location',
				'fields' 	=> array(
					
					'_address' => array(
						'label'       => __( 'Address', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'name'        => '_address',
						'placeholder' => '',
						'class'		  => '',
						'tooltip'	  => '',
						'priority'    => 7,
						'render_row_col' => '6',
						'for_type'	  => ''
					),				
					'_friendly_address' => array(
						'label'       => __( 'Friendly Address', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'name'        => '_friendly_address',
						'placeholder' => '',
						'tooltip'	  => __('Human readable address, if not set, the Google address will be used', 'listeo_core'),
						'class'		  => '',
						
						'priority'    => 8,
						'render_row_col' => '6',
						'for_type'	  => ''
					),	
					'region' => array(
						'label'       => __( 'Region', 'listeo_core' ),
						'type'        => 'term-select',
						'required'    => false,
						'name'        => 'region',
						'taxonomy'        => 'region',
						'placeholder' => '',
						'class'		  => '',
						'tooltip'	  => '',
						'multi'    => false,
						'priority'    => 8,
						'render_row_col' => '3',
						'for_type'	  => ''
					),	
					'_place_id' => array(
						'label'       => __( 'Google Maps Place ID', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => '',
						'name'        => '_place_id',
						'class'		  => '',
						'tooltip'	=> 'Provide your Google Place ID to show Google Reviews',
						'priority'    => 9,
						'render_row_col' => '3'
					),			
					'_geolocation_long' => array(
						'label'       => __( 'Longitude', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => '',
						'name'        => '_geolocation_long',
						'class'		  => '',
						'tooltip'	  => '',
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => ''
					),				
					'_geolocation_lat' => array(
						'label'       => __( 'Latitude', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => '',
						'name'        => '_geolocation_lat',
						'class'		  => '',
						'priority'    => 10,
						'tooltip'	  => '',
						'render_row_col' => '3',
						'for_type'	  => ''
					),
				),
			),
			'gallery' => array(
				'title' 	=> __('Gallery','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'icon' 		=> 'sl sl-icon-picture',
				'fields' 	=> array(
						'_gallery' => array(
							'label'       => __( 'Gallery', 'listeo_core' ),
							'name'       => '_gallery',
							'type'        => 'files',
							'description' => __( 'By selecting (clicking on a photo) one of the uploaded photos you will set it as Featured Image for this listing (marked by icon with star). Drag and drop thumbnails to re-order images in gallery.', 'listeo_core' ),
							'placeholder' => 'Upload images',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
							'for_type'	  => ''
						),				
						
				),
			),
			'details' => array(
				'title' 	=> __('Details','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'icon' 		=> 'sl sl-icon-docs',
				'fields' 	=> array(
						'listing_description' => array(
							'label'       => __( 'Description', 'listeo_core' ),
							'name'       => 'listing_description',
							'type'        => 'wp-editor',
							'description' => '',
							'placeholder' => 'Upload images',
							'class'		  => '',
							'tooltip'	  => '',
							'priority'    => 1,
							'required'    => true,
							'for_type'	  => ''
						),				
						'_video' => array(
							'label'       => __( 'Video', 'listeo_core' ),
							'type'        => 'text',
							'name'        => '_video',
							'required'    => false,
							'placeholder' => __( 'URL to oEmbed supported service', 'listeo_core' ),
							'class'		  => '',
							'tooltip'	  => '',
							'priority'    => 5,
							'for_type'	  => ''
						),

						'_phone' => array(
							'label'       => __( 'Phone', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_phone',
							'class'		  => '',
							'priority'    => 9,
							'tooltip'	  => '',
							'render_row_col' => '3',
							'for_type'	  => ''
						),		
						'_website' => array(
							'label'       => __( 'Website', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_website',
							'class'		  => '',
							'tooltip'	  => '',
							'priority'    => 9,
							'render_row_col' => '3',
							'for_type'	  => ''
						),
						'_email' => array(
							'label'       => __( 'E-mail', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_email',
							'tooltip'	  => '',
							'class'		  => '',
							'priority'    => 10,
							'render_row_col' => '3',
							'for_type'	  => ''
						),
						'_email_contact_widget' => array(
							'label'       => __( 'Enable Contact Widget', 'listeo_core' ),
							'type'        => 'checkbox',
							'tooltip'	  => __('With this option enabled listing will display Contact Form Widget that will send emails to this address', 'listeo_core'),
							'required'    => false,
							'value'		  => 'on',
							'placeholder' => '',
							'name'        => '_email_contact_widget',
							'class'		  => '',
							'priority'    => 10,
							'priority'    => 9,
							'render_row_col' => '3',
							'for_type'	  => ''
						),				
						
						'_facebook' => array(
							'label'       => __( '<i class="fa fa-facebook-square"></i> Facebook', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_facebook',
							'class'		  => 'fb-input',
							'tooltip'	  => '',
							'priority'    => 9,
							'render_row_col' => '4',
							'for_type'	  => ''
						),	
						'_twitter' => array(
							'label'       => __( '<i class="fa fa-twitter-square"></i> Twitter', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_twitter',
							'class'		  => 'twitter-input',
							'tooltip'	  => '',
							'priority'    => 9,
							'render_row_col' => '4',
							'for_type'	  => ''
						),
						'_youtube' => array(
							'label'       => __( '<i class="fa fa-youtube-square"></i> YouTube', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_youtube',
							'class'		  => 'youtube-input',
							'tooltip'	  => '',
							'priority'    => 9,
							'render_row_col' => '4',
							'for_type'	  => ''
						),				
						'_instagram' => array(
							'label'       => __( '<i class="fa fa-instagram"></i> Instagram', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_instagram',
							'class'		  => 'instagram-input',
							'priority'    => 10,
							'tooltip'	  => '',
							'render_row_col' => '4',
							'for_type'	  => ''
						),
						'_whatsapp' => array(
							'label'       => __( '<i class="fa fa-whatsapp"></i> WhatsApp', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'tooltip'	  => '',
							'placeholder' => '',
							'name'        => '_whatsapp',
							'class'		  => 'whatsapp-input',
							'priority'    => 10,
							'render_row_col' => '4',
							'for_type'	  => ''
						),
						'_skype' => array(
							'label'       => __( '<i class="fa fa-skype"></i> Skype', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'tooltip'	  => '',
							'name'        => '_skype',
							'class'		  => 'skype-input',
							'priority'    => 10,
							'render_row_col' => '4',
							'for_type'	  => ''
						),
						
						'_price_min' => array(
							'label'       => __( 'Minimum Price Range', 'listeo_core' ),
							'type'        => 'number',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_price_min',
							'tooltip'	  => __('Set only minimum price to show "Prices starts from " instead of range', 'listeo_core'),
							'class'		  => '',
							'priority'    => 9,
							'render_row_col' => '6',
							'atts' => array(
								'step' => 0.1,
								'min'  => 0,
							),
							'for_type'	  => ''
						),
						'_price_max' => array(
							'label'       => __( 'Maximum Price Range', 'listeo_core' ),
							'type'        => 'number',
							'required'    => false,
							'placeholder' => '',
							'tooltip'	  => __('Set the maximum price for your service, used on filters in search form', 'listeo_core'),
							'name'        => '_price_max',
							'class'		  => '',
							'priority'    => 9,
							'render_row_col' => '6',
							'atts' => array(
								'step' => 0.1,
								'min'  => 0,
							),
							'for_type'	  => ''
						),

				),
			),
			
			'opening_hours' => array(
				'title' 	=> __('Opening Hours','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'onoff'		=> true,
				'icon' 		=> 'sl sl-icon-clock',
				'fields' 	=> array(
						'_opening_hours_status' => array(
								'label'       => __( 'Opening Hours status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => '_opening_hours_status',
						),
						'_opening_hours' => array(
							'label'       => __( 'Opening Hours', 'listeo_core' ),
							'name'       => '_opening_hours',
							'type'        => 'hours',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
							'for_type'	  => ''
						),	
						'_listing_timezone' => array(
								'label'       => __( 'Listing Timezone', 'listeo_core' ),
								'type'        => 'timezone',
								'required'    => false,
								'name'        => '_listing_timezone',
						),	
						
				),
			),
			'event' => array(
				'title'		=> __( 'Event Date', 'listeo_core' ),
				//'class'		=> 'margin-top-40',
				'icon'		=> 'fa fa-money',
				'fields'	=> array(
					'_event_date' => array(
						'label'       => __( 'Event Date', 'listeo_core' ),
						'tooltip'	  => __('Select date when even will start', 'listeo_core'),
						'type'        => 'text',						
						'required'    => false,
						'name'        => '_event_date',
						'class'		  => 'input-datetime',
						'placeholder' => '',
						'priority'    => 9,

						'render_row_col' => '6',
						'for_type'	  => ''
					),
					'_event_date_end' => array(
						'label'       => __( 'Event Date End', 'listeo_core' ),
						'tooltip'	  => __('Select date when even will end', 'listeo_core'),
						'type'        => 'text',
						'required'    => false,
						'name'        => '_event_date_end',
						'class'		  => 'input-datetime',
						'placeholder' => '',
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
					),
					
				)
			),
			'menu' => array(
				'title' 	=> __('Pricing & Bookable Services','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'onoff'		=> true,
				'icon' 		=> 'sl sl-icon-book-open',
				'fields' 	=> array(
						'_menu_status' => array(
								'label'       => __( 'Menu status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => '_menu_status',
								'for_type'	  => ''
						),
						'_menu' => array(
							'label'       => __( 'Pricing', 'listeo_core' ),
							'name'       => '_menu',
							'type'        => 'pricing',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
							'for_type'	  => ''
						),
						'_menu_section' => array(
								'label'       => __( 'Section', 'listeo_core' ),
								'name'       => '_menu_section',
								'type'        => 'section',
								'placeholder' => '',
								'class'		  => '',
								'priority'    => 1,
								'required'    => false,
								'for_type'	  => ''
						),
					
						
				),
			),
			'booking' => array(
				'title' 	=> __('Booking','listeo_core'),
				'class' 	=> 'margin-top-4000 booking-enable',
				'onoff'		=> true,
				//'onoff_state' => 'on',
				'icon' 		=> 'fa fa-calendar-check',
				'fields' 	=> array(
					'_booking_status' => array(
							'label'       => __( 'Booking status', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_booking_status',
							'for_type'	  => ''
							
					),
				)
			),
			'slots' => array(
				'title' 	=> __('Availability','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'onoff'		=> true,
				'icon' 		=> 'fa fa-calendar-check',
				'fields' 	=> array(
						'_slots_status' => array(
								'label'       => __( 'Booking status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => '_slots_status',
								'for_type'	  => '',
								'tooltip'	  => '',
						),
						'_slots' => array(
							'label'       => __( 'Availability Calendar', 'listeo_core' ),
							'name'       => '_slots',
							'type'        => 'slots',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
							'tooltip'	  => '',
							'for_type'	  => ''
						),				
						
				),
			),
			

			'basic_prices' => array(
				'title'		=> __('Booking prices and settings','listeo_core'),
				//'class'		=> 'margin-top-40',
				'icon'		=> 'fa fa-money',
				'fields'	=> array(
					
					'_event_tickets' => array(
						'label'       => __( 'Available Tickets', 'listeo_core' ),
						'tooltip'	  => __('How many ticekts you have to offer', 'listeo_core'),
						'type'        => 'number',
						'required'    => false,
						'name'        => '_event_tickets',
						'class'		  => '',
						'placeholder' => '',
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
					),

					'_normal_price' => array(
						'label'       => __( 'Regular Price', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Default price for booking on Monday - Friday', 'listeo_core'),
						'required'    => false,
						'default'           => '0',
						'placeholder' => '',
						'unit'		  => $currency,
						'name'        => '_normal_price',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
						
					),	

					'_weekday_price' => array(
						'label'       => __( 'Weekend Price', 'listeo_core' ),
						'type'        => 'number',
						'required'    => false,
						'tooltip'	  => __('Default price for booking on weekend', 'listeo_core'),
						'placeholder' => '',
						'name'        => '_weekday_price',
						'unit'		  => $currency,
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
					),	
					'_reservation_price' => array(
						'label'       => __( 'Reservation Fee', 'listeo_core' ),
						'type'        => 'number',
						'required'    => false,
						'name'        => '_reservation_price',
						'tooltip'	  => __('One time fee for booking', 'listeo_core'),
						'placeholder' => '',
						'unit'		  => $currency,
						'default'           => '0',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
						
					),				
					'_expired_after' => array(
						'label'       => __( 'Reservation expires after', 'listeo_core' ),
						'tooltip'	  => __('How many hours you can wait for clients payment', 'listeo_core'),
						'type'        => 'number',
						'default'     => '48',
						'required'    => false,
						'name'        => '_expired_after',
						'placeholder' => '',
						'class'		  => '',
						'unit'		  => 'hours',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
					),
						
					'_instant_booking' => array(
						'label'       => __( 'Enable Instant Booking', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('With this option enabled booking request will be immediately approved ', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_instant_booking',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => ''
					),
					'_min_days' => array(
						'label'       => __( 'Minimum  stay', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Set minimum number of days for reservation', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_min_days',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => 'rental'
					),
					'_max_guests' => array(
						'label'       => __( 'Maximum number of guests', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Set maximum number of guests per reservation', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_max_guests',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => ''
					),	
					'_count_per_guest' => array(
						'label'       => __( 'Enable Price per Guest', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('With this option enabled regular price and weekend price will be multiplied by number of guests to estimate total cost', 'listeo_core'),
						'required'    => false,
						
						'placeholder' => '',
						'name'        => '_count_per_guest',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => ''
					),	
					'_end_hour' => array(
						'label'       => __( 'Enable End Hour time-picker', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('If you are not using slots, you can allow guest to set end time for booking by enabling that option ', 'listeo_core'),
						'required'    => false,
						
						'placeholder' => '',
						'name'        => '_end_hour',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => 'service'
					),	
						
				),
			),

			'availability_calendar' => array(
				'title' 	=> __('Availability Calendar','listeo_core'),
				//'class' 	=> 'margin-top-40',
				//'onoff'		=> true,
				'icon' 		=> 'fa fa-calendar-check',
				'fields' 	=> array(
						'_availability' => array(
							'label'       => __( 'Click day in calendar to mark it as unavailable', 'listeo_core' ),
							'tooltip'	  => '',
							'name'       => '_availability_calendar',
							'type'        => 'calendar',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
							'for_type'	  => ''
						),				
						
				),
			),


		);
    }

     /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
         add_submenu_page( 'listeo-fields-and-form', 'Add Listing Form', 'Add Listing Form', 'manage_options', 'listeo-submit-builder', array( $this, 'output' )); 
    }


    public function output(){

    	$this->init_fields();

 		if ( ! empty( $_GET['reset-fields'] ) && ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'reset' ) ) {
            delete_option("listeo_submit_form_fields");
            
            echo '<div class="updated"><p>' . __( 'The fields were successfully reset.', 'listeo' ) . '</p></div>';
        }
  


        if ( ! empty( $_POST )) { /* add nonce tu*/
          
            echo $this->form_editor_save(); 
        }
        
    	$saved_fields = get_option("listeo_submit_form_fields");
    	
        $submit_fields = (!empty($saved_fields)) ? get_option("listeo_submit_form_fields") : $this->fields ;
//         ini_set('xdebug.var_display_max_depth', '10');
// ini_set('xdebug.var_display_max_children', '256');
// ini_set('xdebug.var_display_max_data', '1024');
   
        ?>
        <style type="text/css">
        	span.checkboxess {
			    position: relative;
			    display: inline-block;
			    width: 30%;
			}
        </style>


 		<div class="listeo-editor-modal">
            <div class="listeo-editor-modal-content">
            	<form id="listeo-core-fafe-submit">
	                <div class="listeo-editor-modal-header">
	                		<h3 class="listeo-editor-modal-title">Edit Field</h3>
							<a title="Close" href="#" class="listeo-modal-close">
								<span class="dashicons dashicons-no"></span>
							</a>
	                </div>
            	
					<div class="listeo-modal-form">
					</div>
					
					<div class="listeo-editor-modal-footer">
						<button class="button button-large listeo-modal-close " id="listeo-cancel">Cancel</button>
						<button class="button button-primary button-large " id="listeo-save-field">Save Field</button>
					</div>
				</form>
            </div>
        </div>
			
		<h2>Add Listing Form Editor</h2>
		<p id="explanation">
			You can configure fields for selected listing type (Edit field and use "For Type" option). Here you can filter fields to see how form will look for each type of listing.
		</p>
		<ul class="listeo-editor-listing-types">
			<li><a href="#" class="show-fields-type-all active">Show all fields</a></li>
			<li><a href="#" class="show-fields-type-service">Show fields for Services</a></li>
			<li><a href="#" class="show-fields-type-rentals">Show fields for Rentals</a></li>
			<li><a href="#" class="show-fields-type-events">Show fields for Events</a></li>
		</ul>
		<div class="wrap listeo-forms-builder clearfix">
                           
            <form method="post" id="mainform" action="admin.php?page=listeo-submit-builder">

                <div class="form-editor-container" id="listeo-fafe-forms-editor" data-section="<?php echo esc_html('<div class="listeo-fafe-section">
	<h3>
		<input type="text" value="{section_org}" name="{section}[title]">
		<a href="#" class="listeo-fafe-section-edit button"></a>
		<a href="#" class="listeo-fafe-section-remove-section button"></a>
		<ul class="listeo-fafe-section-move">
    		<li><a class="listeo-fafe-section-move-up button" href="#"></a></li>
    		<li><a class="listeo-fafe-section-move-down button" href="#"></a></li>
    	</ul>
	</h3>
	<div class="section_options">
		<table class="form-table">
    		<tr>
    			<td>Custom class <span class="dashicons dashicons-editor-help" title="Option custom class for this section container"></span></td>
    			<td><input type="text" value="" name="{section}[class]"></td>
    		</tr>
    		<tr>
    			<td>Make it switchable <span class="dashicons dashicons-editor-help" title="If this is enabled, the section will be \'turned off\' with the swith button in right corner"></span></td>
    			<td>
    				<input name="{section}[onoff]" class="widefat" type="checkbox" value="" >
    			</td>
    		</tr>
    		<tr>
    			<td>Enabled by default <span class="dashicons dashicons-editor-help" title="If this is enabled, the section will be \'turned off\' with the swith button in right corner"></span></td>
    			<td>
    				<input name="{section}[onoff_state]" class="widefat" type="checkbox" value="" >
    			</td>
    		</tr>
    		<tr>
    			<td>Icon class <span class="dashicons dashicons-editor-help" title="Class used to display optional icon"></span></td>
    			<td><input type="text" value="" name="{section}[icon]"> Available icons list <a href="http://www.vasterad.com/themes/listeo_082019/pages-icons.html">listeo.pro/icons</a></td>

    		</tr>
    	</table>
	</div>
</div>
<div data-section="{section}" class="row-container row-{section}">
<div class="block-add-new"><a href="#" data-section="{section}" class="button primary">Add new field</a></div>
</div>') ?>"> 
                    <?php
                    $index = 0;

                    $catsss = array("service_category","event_category","rental_category");

                    $all_cats = array();

                    foreach ($catsss as $key => $cats) {
                    	$listing_category_terms_all = get_terms( array(
							          'taxonomy' => $cats,
							          'hide_empty'  => false,
							          'orderby' => 'term_order',
					        ));

                    	foreach ($listing_category_terms_all as $key => $parent) {
				        	if($parent->parent == "0"){
				        		$all_cats[] = $parent;
				        	}
				        	
				        }
                    }

                        




                    foreach ( $submit_fields as $field_key => $field ) { 
                    	
                    	$section  = (!empty($field_key)) ? $field_key : 'section';

                    	//var_dump($submit_fields_temp[$section]['fields']['title']);
                    	$title = isset($field['title']) ? $field['title'] : "Title";
                    	?>
                    	<div  class="listeo-fafe-row-section">
	                    	<div class="listeo-fafe-section">
	                    		<h3>
	                    			<input type="text" value="<?php echo stripslashes($title);?>" name="<?php echo $section ?>[title]">
	                    			<a href="#" class="listeo-fafe-section-edit button"></a>

	                    			<?php if($field_key != 'basic_info') { ?><a href="#" class="listeo-fafe-section-remove-section button"></a><?php } ?>
	                    			<ul class="listeo-fafe-section-move">
			                    		<li><a class="listeo-fafe-section-move-up button" href="#"></a></li>
			                    		<li><a class="listeo-fafe-section-move-down button" href="#"></a></li>
			                    	</ul>
	                    		</h3>
	                    		<div class="section_options">
	                    			<table class="form-table">
			                    		<tr>
			                    			<td>Custom class <span class="dashicons dashicons-editor-help" title="Option custom class for this section container"></span></td>
			                    			<td><input type="text" value="<?php echo isset($field['class']) ? $field['class'] : "";;?>" name="<?php echo $section ?>[class]"></td>
			                    		</tr>
			                    		<tr>
			                    			<td>Make it switchable  <span class="dashicons dashicons-editor-help" title="If this is enabled, the section will be \'turned off\' with the swith button in right corner"></span></td>
			                    			<td>
												<?php 
													$value = ( isset( $field['onoff'] ) && !empty( $field['onoff'] ) ) ? true : false;
												 ?>
			                    				<input name="<?php echo $section ?>[onoff]" <?php checked(1,$value) ?> class="widefat" type="checkbox" value="<?php echo $value; ?>" >
			                    			</td>
			                    		</tr>
			                    		<tr>
			                    			<td>Enabled by default  <span class="dashicons dashicons-editor-help" title="If previous option is enabled this will make section enabled by default"></span></td>
			                    			<td>
												<?php 
													$value = ( isset( $field['onoff_state'] ) && !empty( $field['onoff_state'] ) ) ? true : false;
												 ?>
			                    				<input name="<?php echo $section ?>[onoff_state]" <?php checked(1,$value) ?> class="widefat" type="checkbox" value="<?php echo $value; ?>" >
			                    			</td>
			                    		</tr>
			                    		<tr>
			                    			<td>Icon class <span class="dashicons dashicons-editor-help" title="Class used to display optional icon"></span></td>
			                    			<td><input type="text" value="<?php echo isset($field['icon']) ? $field['icon'] : "";;?>" name="<?php echo $section ?>[icon]"> Available icons list <a href="http://www.vasterad.com/themes/listeo_082019/pages-icons.html">listeo.pro/icons</a></td>
			                    		</tr>

			                    		<tr>
			                    			<td>Field for type<span class="dashicons dashicons-editor-help" title="Field for type<"></span></td>
			                    			<td>
			                    				<input type="checkbox" name="<?php echo $section ?>[for_type][]" <?php if(isset($field['for_type']) && in_array('rental',$field['for_type'])){ echo "checked";} ?> value="rental">Rental type
												<input type="checkbox" name="<?php echo $section ?>[for_type][]" <?php if(isset($field['for_type']) && in_array('service',$field['for_type'])){ echo "checked";} ?> value="service">Service type
												<input type="checkbox" name="<?php echo $section ?>[for_type][]" <?php if(isset($field['for_type']) && in_array('event',$field['for_type'])){ echo "checked";} ?> value="event">Event type
												<input type="hidden" name="<?php echo $section ?>[for_type][]" value="">
											</td>
			                    		</tr>

			                    		<tr>
			                    			<td>Show field only if booking enabled<span class="dashicons dashicons-editor-help" title="Show field only if booking enabled"></span></td>
			                    			<td>
			                    				<?php 
			                    				   if(isset( $field['show_field_if_booking_enable'] ) && $field['show_field_if_booking_enable'] == "1"){
			                    				     $checked =  "checked";
			                    				   }else{

			                    				   	$checked =  "";
                     
			                    				   } ?>
			                    				
			                    				<input type="checkbox" name="<?php echo $section ?>[show_field_if_booking_enable]" <?php echo $checked;?> value="1">
											</td>
			                    		</tr>
			                    		<tr>
			                    			<td>Show field only if user group active<span class="dashicons dashicons-editor-help" title="Show field only if booking enabled"></span></td>
			                    			<td>
			                    				<?php 
			                    				   if(isset( $field['show_field_if_group_active'] ) && $field['show_field_if_group_active'] == "1"){
			                    				     $checked =  "checked";
			                    				   }else{

			                    				   	$checked =  "";
                     
			                    				   } ?>
			                    				
			                    				<input type="checkbox" name="<?php echo $section ?>[show_field_if_group_active]" <?php echo $checked;?> value="1">
											</td>
			                    		</tr>
			                    		<tr>
			                    			<td>Opened section<span class="dashicons dashicons-editor-help" title="Opened section"></span></td>
			                    			<td>
			                    				<?php 
			                    				   if(isset( $field['opened_section'] ) && $field['opened_section'] == "1"){
			                    				     $checked_opened =  "checked";
			                    				   }else{

			                    				   	$checked_opened =  "";
                     
			                    				   } ?>
			                    				
			                    				<input type="checkbox" name="<?php echo $section ?>[opened_section]" <?php echo $checked_opened;?> value="1">
											</td>
			                    		</tr>

			                    		<tr>
			                    			<td colspan="2">
			                    				<hr>
			                    				
			                    				<h4>Show field only for selected category (Remember to select type)<span class="dashicons dashicons-editor-help" title="Show field only for selected category (Remember to select type)"></span></h4>

			                    				<?php foreach ($all_cats as $key => $all_cat) { ?>
			                    					<span class="checkboxess">

				                    				   <input type="checkbox" name="<?php echo $section ?>[selected_cats][]" <?php if(isset($field['selected_cats']) && in_array($all_cat->slug,$field['selected_cats'])){ echo "checked"; }?> value="<?php echo $all_cat->slug;?>"> <?php echo $all_cat->name;?>

				                    				</span>
			                    				
			                    				<?php } ?>
			                    				<hr>

			                    				<!-- <select name="<?php echo $section ?>[selected_cats][]" class="choosencat" multiple>

			                    				    <?php foreach ($all_cats as $key => $all_cat) { ?>
			                    					   <option <?php if(isset($field['selected_cats']) && in_array($all_cat->slug,$field['selected_cats'])){ echo "selected"; }?> value="<?php echo $all_cat->slug;?>"><?php echo $all_cat->name;?></option>
			                    					<?php } ?>
			                    				</select> -->

			                    				<!-- 
			                    					<input type="checkbox" name="<?php echo $section ?>[selected_cats][]" <?php if(isset($field['selected_cats']) && in_array($all_cat->slug,$field['selected_cats'])){ echo "checked"; }?> value="<?php echo $all_cat->slug;?>"> <?php echo $all_cat->name;?> -->
			                    				
			                    				
			                    				
											</td>
			                    		</tr>
			                    	</table>
	                    		</div>
		                    	
	                    	</div>
                    	
                    	
              
						<div data-section="<?php echo esc_attr($section); ?>" class="row-container row-<?php echo esc_attr($section); ?><?php if(isset($field['title'])) { sanitize_title($field['title']); } ?>">
	
							<?php 


							foreach ($field['fields'] as $key => $field) { 
								
								if(in_array($key,array('_monday_opening_hour','_monday_closing_hour','_tuesday_opening_hour','_tuesday_closing_hour','_wednesday_opening_hour','_wednesday_closing_hour','_thursday_opening_hour','_thursday_closing_hour','_friday_opening_hour','_friday_closing_hour','_saturday_opening_hour','_saturday_closing_hour','_sunday_opening_hour','_sunday_closing_hour'))) {
									continue;
								}
								
								if(!array_key_exists('class', $field)){
									$field['class'] = '';
										
								}

								$width = $this->get_box_width($field);
								$label = (isset($field['label'])) ? $field['label'] : 'Missing Label';
								?>

								<div class='editor-block block-width-<?php echo $width;?> block-<?php echo sanitize_title($key) ?>' >
									<h5><?php echo stripslashes($label); ?></h5>
									<div class="editor-block-tools">
										<input type="text" class="block-width-input" name="<?php echo $section; ?>[<?php echo $key; ?>][render_row_col]" value="<?php echo $width;?>">
										<input type="hidden" name="section[]" value="<?php echo $section;?>">
										<input type="hidden" name="field[]" value="<?php echo $key;?>">
										
										<ul>
											<li class="block-edit"><a data-section="<?php echo $section; ?>" data-id="<?php echo $key; ?>" href="#" class="button button-primary"></a></li>
											<li class="block-narrower"><a href="#"></a></li>
											<li class="block-wider"><a href="#"></a></li>
											<?php if(!in_array( $key,array('_geolocation_long','_geolocation_lat')) ) { ?>
												<li class="block-delete"><a href="#"></a></li>
											<?php } ?>
										</ul>

										
									</div>
									<div class="editor-block-form-fields">
										<?php 
										
//'tooltip'	  => '',
										if(!empty($section) && !empty($field)){
								    		$this->init_fields();
									    	$options = get_option("listeo_submit_form_fields");
									    	$submit_fields = (!empty($options)) ? get_option("listeo_submit_form_fields") : $this->fields;
									    	$form = $this->generate_form_fields($submit_fields[$section]['fields'][$key],$key,$section);
									    	echo $form;
								    	} 
								    	?>
									</div>
								</div>
								
							<?php } ?>
							<div class="block-add-new"><a href="#" data-section="<?php echo esc_attr($section); ?>" class="button primary">Add new field</a></div>
						</div>
                        </div>
                       
                    <?php }  ?>
                    <div class="droppable-helper"></div>
                    
                </div>
                <div>
                	<a href="#" class="listeo-fafe-new-section button-secondary"><?php _e( 'Add new section', 'listeo' ); ?></a>
                </div>
              	<div class="buttons-wrapper">
              		<input type="submit" name="save_changes" class="save-fields button-primary" value="<?php _e( 'Save Changes', 'listeo' ); ?>" />
              		
                	<a href="<?php echo wp_nonce_url( add_query_arg( 'reset-fields', 1 ), 'reset' ); ?>" class="reset button-secondary"><?php _e( 'Reset to defaults', 'listeo' ); ?></a>
            	</div>
            	<?php wp_nonce_field( 'save-fields' ); ?>
           		<?php wp_nonce_field( 'save'); ?>
    		</form>
		</div>
	
		
        <?php
    }

     /**
     * Save the form fields
     */
    private function form_editor_save() {


      	if(isset($_POST['save_changes'])) {
			      
	      	$options = get_option('listeo_submit_form_fields_temp',array());

	      	$new_fields = array();

	      	

	      	$field_name      = ! empty( $_POST['field'] ) ? array_map( 'sanitize_text_field', $_POST['field'] )   : array();
	        $field_section   = ! empty( $_POST['section'] ) ? array_map( 'sanitize_text_field', $_POST['section'] )   : array();
	        $field_width 	 = ! empty( $_POST['render_row_col'] ) ? array_map( 'sanitize_text_field', $_POST['render_row_col'] )                  : array(); 
	        $field_type 	 = ! empty( $_POST['type'] ) ? array_map( 'sanitize_text_field', $_POST['type'] ) : array();
	        
			$sections = array_unique($field_section);




			
			foreach ($sections as $key => $section) {
				# code...
				if(isset($_POST[$section])) {



					$section_title = isset($_POST[$section]['title']) ? sanitize_text_field(stripslashes_deep($_POST[$section]['title'])) : '';
					
					$new_fields[$section]['title'] = $section_title;
					
					$section_class = isset($_POST[$section]['class']) ? sanitize_text_field($_POST[$section]['class']) : '';
					$new_fields[$section]['class'] = $section_class;

					$section_onoff = isset($_POST[$section]['onoff']) ? true : false;
					$new_fields[$section]['onoff'] = $section_onoff;

					$section_onoff_state = isset($_POST[$section]['onoff_state']) ? 'on' : false;
					$new_fields[$section]['onoff_state'] = $section_onoff_state;

					$section_icon = isset($_POST[$section]['icon']) ? sanitize_text_field($_POST[$section]['icon']) : '';
					$new_fields[$section]['icon'] = $section_icon;

					$section_fortype = isset($_POST[$section]['for_type']) ? $_POST[$section]['for_type'] : array();
					$new_fields[$section]['for_type'] = $section_fortype;

					$section_selected_cats = isset($_POST[$section]['selected_cats']) ? $_POST[$section]['selected_cats'] : array();
					$new_fields[$section]['selected_cats'] = $section_selected_cats;

					$section_show_field_if_booking_enable = isset($_POST[$section]['show_field_if_booking_enable']) ? $_POST[$section]['show_field_if_booking_enable'] : "";
					$new_fields[$section]['show_field_if_booking_enable'] = $section_show_field_if_booking_enable;

					$section_show_field_if_group_active = isset($_POST[$section]['show_field_if_group_active']) ? $_POST[$section]['show_field_if_group_active'] : "";
					$new_fields[$section]['show_field_if_group_active'] = $section_show_field_if_group_active;

					$section_opened_section = isset($_POST[$section]['opened_section']) ? $_POST[$section]['opened_section'] : "";
					$new_fields[$section]['opened_section'] = $section_opened_section;

					

					foreach ($_POST[$section] as $key => $value) {
						if(in_array($key, array('title','class','onoff','onoff_state','icon','for_type','show_field_if_booking_enable','show_field_if_group_active','selected_cats','number_of_char','opened_section'))){
							continue;
						};
						array_map( 'sanitize_text_field',$value);
						
						$new_fields[$section]['fields'][$key] = $value;
						if(!isset($value['required'])){ 
							$new_fields[$section]['fields'][$key]['required'] = 0; 
						}
						if($value['type'] == "term-select" && !isset($value['multi'])){ 
							$new_fields[$section]['fields'][$key]['multi'] = 0; 
						}
						
						// var_dump($new_fields[$section]['fields'][$key]);
					}
				}
			}
		
	
		$result = update_option( "listeo_submit_form_fields", $new_fields);

	    //     foreach ( $field_name as $key => $field ) {
	    //     	$section = $field_section[ $key ];
	    //     	$name = $field_name[ $key ];

	 			// $options[$section]['fields'][$name]['render_row_col'] = $field_width[$key];
	    //     }
	      //var_dump($options);
	    //     $result = update_option( "listeo_submit_form_fields_temp", $option);
	        

	        // if ( true === $result ) {
	        //     echo '<div class="updated"><p>' . __( 'The fields were successfully saved.', 'wp-job-manager-applications' ) . '</p></div>';
	        // }
        }

     	if(isset($_POST['publish_changes'])) {
     		$option = get_option('listeo_submit_form_fields',array());
			$submit_fields = (!empty($options)) ? $options : $this->fields ;
	      	
	      	$new_fields = array();


	      	// get width settings and save them
	      	$field_name      = ! empty( $_POST['field'] ) ? array_map( 'sanitize_text_field', $_POST['field'] )                     : array();
	        $field_section   = ! empty( $_POST['section'] ) ? array_map( 'sanitize_text_field', $_POST['section'] )                 : array();
	        $field_width 	 = ! empty( $_POST['render_row_col'] ) ? array_map( 'sanitize_text_field', $_POST['render_row_col'] )	: array();

	        foreach ( $field_name as $key => $field ) {
	        	$section = $field_section[ $key ];
	        	$name = $field_name[ $key ];
	        	
	 			$submit_fields[$section]['fields'][$name]['render_row_col'] = $field_width[$key];
	        }

	        // get temp option settings and save them

	      	
	        $result = update_option( "listeo_submit_form_fields", $submit_fields);
	        

	        if ( true === $result ) {
	            echo '<div class="updated"><p>' . __( 'The fields were successfully saved.', 'wp-job-manager-applications' ) . '</p></div>';
	        }
     	}
      
    }

    public function get_box_width($field){
    	
    	if(isset($field['render_row_col'])){
    		return $field['render_row_col'];
    	} else {
    		return '12';
    	}
    }

    /**
     * Sanitize a 2d array
     * @param  array $array
     * @return array
     */
    private function sanitize_array( $input ) {
        if ( is_array( $input ) ) {
            foreach ( $input as $k => $v ) {
                $input[ $k ] = $this->sanitize_array( $v );
            }
            return $input;
        } else {
            return sanitize_text_field( $input );
        }
    }

    public function editor_load_field(){
    	
    	$ajax_out = false;

    	$field =  $_POST['field'];
    	$section =  $_POST['section'];
    	if(!empty($section) && !empty($field)){
    		$this->init_fields();
	    	$options = get_option("listeo_submit_form_fields");
	    	$submit_fields = (!empty($options)) ? get_option("listeo_submit_form_fields") : $this->fields;

	    	$form = $this->generate_form_fields($submit_fields[$section]['fields'][$field],$field,$section);

	    	$ajax_out = $form;
    	}
		
    	wp_send_json_success( $ajax_out );

    }

    function editor_get_items(){
    	$section = $_POST['section'];
    	$visual_fields = array(
				'listing_title' => array(
					'label'       => __( 'Listing Title', 'listeo_core' ),
					'type'        => 'text',
					'name'       => 'listing_title',
					'tooltip'	  => __( 'Type title that will also contains an unique feature of your listing (e.g. renovated, air contidioned)', 'listeo_core' ),
					'required'    => true,
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 1,
					
				),
				'keywords' => array(
					'label'       => __( 'Keywords', 'listeo_core' ),
					'type'        => 'text',
					'tooltip'	  => __( 'Maximum of 15 keywords related with your business, separated by coma' , 'listeo_core' ),
					'placeholder' => '',
					'name'        => 'keywords',
					'after_row'   => '</div>',
					'priority'    => 10,
					'before_row'  => '',
					'default'	  => '',
					'render_row_col' => '4',
					'required'    => false,
				),
				'listing_description' => array(
					'label'       => __( 'Description', 'listeo_core' ),
					'name'       => 'listing_description',
					'type'        => 'wp-editor',
					'description' => __( 'By selecting (clicking on a photo) one of the uploaded photos you will set it as Featured Image for this listing (marked by icon with star). Drag and drop thumbnails to re-order images in gallery.', 'listeo_core' ),
					'placeholder' => 'Upload images',
					'class'		  => '',
					'priority'    => 1,
					'required'    => true,
				),	
				'_booking_status' => array(
							'label'       => __( 'Booking status', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_booking_status',
							'for_type'	  => ''
							
				),
				'_booking_link' => array(
						'label'       => __( 'External Booking link', 'listeo_core' ),
						'tooltip'	  => __('Add Link to 3rd party site where the booking is made', 'listeo_core'),
						'type'        => 'text',
						'default'     => '',
						'required'    => false,
						'name'        => '_booking_link',
						'placeholder' => '',
						'class'		  => '',
					
						'render_row_col' => '6',
						'for_type'	  => ''
				),
				'_booking_button_text' => array(
						'label'       => __( 'External Booking button text', 'listeo_core' ),
						'tooltip'	  => __('Add button text to 3rd party site where the booking is made', 'listeo_core'),
						'type'        => 'text',
						'default'     => '',
						'required'    => false,
						'name'        => '_booking_button_text',
						'placeholder' => '',
						'class'		  => '',
					
						'render_row_col' => '6',
						'for_type'	  => ''
				),
				'_opening_hours_status' => array(
					'label'       => __( 'Opening Hours status', 'listeo_core' ),
					'type'        => 'skipped',
					'required'    => false,
					'name'        => '_opening_hours_status',
				),
				'_opening_hours' => array(
					'label'       => __( 'Opening Hours', 'listeo_core' ),
					'name'       => '_opening_hours',
					'type'        => 'hours',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
				),
			
				'_listing_timezone' => array(
								'label'       => __( 'Listing Timezone', 'listeo_core' ),
								'type'        => 'timezone',
								'required'    => false,
								'name'        => '_listing_timezone',
								'for_type'	  => ''
						),	
				'_slots_status' => array(
						'label'       => __( 'Booking status', 'listeo_core' ),
						'type'        => 'skipped',
						'required'    => false,
						'name'        => '_slots_status',
						'for_type'	  => ''
				),
				'_slots' => array(
					'label'       => __( 'Availability Calendar', 'listeo_core' ),
					'name'       => '_slots',
					'type'        => 'slots',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
					'for_type'	  => ''
				),		

				'_availability' => array(
					'label'       => __( 'Click day in calendar to mark it as unavailable', 'listeo_core' ),
				
					'name'       => '_availability_calendar',
					'type'        => 'calendar',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
					'for_type'	  => ''
				),	
				//nowe
				'_gallery' => array(
					'label'       => __( 'Gallery', 'listeo_core' ),
					'name'       => '_gallery',
					'type'        => 'files',
					'description' => __( 'By selecting (clicking on a photo) one of the uploaded photos you will set it as Featured Image for this listing (marked by icon with star). Drag and drop thumbnails to re-order images in gallery.', 'listeo_core' ),
					'placeholder' => 'Upload images',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
					'for_type'	  => ''
				),					
				'_menu_status' => array(
								'label'       => __( 'Menu status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => '_menu_status',
								'for_type'	  => ''
						),
				'_menu' => array(
							'label'       => __( 'Pricing', 'listeo_core' ),
							'name'       => '_menu',
							'type'        => 'pricing',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
							'for_type'	  => ''
						),
				'_menu_section' => array(
							'label'       => __( 'Section', 'listeo_core' ),
							'name'       => '_menu_section',
							'type'        => 'section',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
							'for_type'	  => ''
						),
				'_slots_status' => array(
						'label'       => __( 'Slots status', 'listeo_core' ),
						'type'        => 'skipped',
						'required'    => false,
						'name'        => '_slots_status',
						'for_type'	  => ''
				),
				'_slots' => array(
					'label'       => __( 'Availability Calendar', 'listeo_core' ),
					'name'       => '_slots',
					'type'        => 'slots',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
					'for_type'	  => ''
				),	
				'_event_tickets' => array(
						'label'       => __( 'Available Tickets', 'listeo_core' ),
						'tooltip'	  => __('How many ticekts you have to offer', 'listeo_core'),
						'type'        => 'number',
						'required'    => false,
						'name'        => '_event_tickets',
						'class'		  => '',
						'placeholder' => '',
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
				),

				'_normal_price' => array(
						'label'       => __( 'Regular Price', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Default price for booking on Monday - Friday', 'listeo_core'),
						'required'    => false,
						'default'           => '0',
						'placeholder' => '',
						'unit'		  => $currency,
						'name'        => '_normal_price',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
						
					),	

					'_weekday_price' => array(
						'label'       => __( 'Weekend Price', 'listeo_core' ),
						'type'        => 'number',
						'required'    => false,
						'tooltip'	  => __('Default price for booking on weekend', 'listeo_core'),
						'placeholder' => '',
						'name'        => '_weekday_price',
						'unit'		  => $currency,
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
					),	
					'_reservation_price' => array(
						'label'       => __( 'Reservation Fee', 'listeo_core' ),
						'type'        => 'number',
						'required'    => false,
						'name'        => '_reservation_price',
						'tooltip'	  => __('One time fee for booking', 'listeo_core'),
						'placeholder' => '',
						'unit'		  => $currency,
						'default'           => '0',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
						
					),				
					'_expired_after' => array(
						'label'       => __( 'Reservation expires after', 'listeo_core' ),
						'tooltip'	  => __('How many hours you can wait for clients payment', 'listeo_core'),
						'type'        => 'number',
						'default'     => '48',
						'required'    => false,
						'name'        => '_expired_after',
						'placeholder' => '',
						'class'		  => '',
						'unit'		  => 'hours',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6',
						'for_type'	  => ''
					),
						
					'_instant_booking' => array(
						'label'       => __( 'Enable Instant Booking', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('With this option enabled booking request will be immediately approved ', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_instant_booking',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'value'    	  => 'on',
						'render_row_col' => '6',
						'for_type'	  => ''
					),
					'_max_guests' => array(
						'label'       => __( 'Maximum number of guests', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Set maximum number of guests per reservation', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_max_guests',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => ''
					),	
					'_min_days' => array(
						'label'       => __( 'Minimum stay (in days)', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Set minimum number of days to book', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_min_days',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => ''
					),	
					'_count_per_guest' => array(
						'label'       => __( 'Enable Price per Guest', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('With this option enabled regular price and weekend price will be multiplied by number of guests to estimate total cost', 'listeo_core'),
						'required'    => false,
						
						'placeholder' => '',
						'name'        => '_count_per_guest',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => ''
					),	
		

			
		);


    	$price_fields = Listeo_Core_Meta_Boxes::meta_boxes_prices();
    	$meta_fields = array(
    		Listeo_Core_Meta_Boxes::meta_boxes_prices(),
    		Listeo_Core_Meta_Boxes::meta_boxes_location(),
    		Listeo_Core_Meta_Boxes::meta_boxes_contact(),
    		Listeo_Core_Meta_Boxes::meta_boxes_event(),
    		Listeo_Core_Meta_Boxes::meta_boxes_service(),
    		Listeo_Core_Meta_Boxes::meta_boxes_rental(),
    		Listeo_Core_Meta_Boxes::meta_boxes_video(),
    		Listeo_Core_Meta_Boxes::meta_boxes_custom(),
    		Listeo_Core_Meta_Boxes::meta_boxes_group(),
    	);

    	foreach ($meta_fields as $key) {
    		foreach ($key['fields'] as $key => $field) { 
    			
	        	if(in_array($field['type'], array('select','select_multiple','multicheck_split','multicheck'))){ 
	        		$visual_fields[] = array(
						'label'       => $field['name'],
						'type'        => $field['type'],
						'placeholder' => $field['name'],
						'name'        => $field['id'],
						'tooltip'	  => '',
						'priority'    => 10,
						'default'	  => '',
						'render_row_col' => '4',
						'multi'    	  => false,
						'required'    => false,
						'options' => $field['options'],
						'for_type'	  => ''
					);

	        	} else {
 					$visual_fields[] = array(
						'label'       => $field['name'],
						'type'        => $field['type'],
						'placeholder' => $field['name'],
						'name'        => $field['id'],
						'tooltip'	  => '',
						'priority'    => 10,
						'default'	  => '',
						'render_row_col' => '4',
						'multi'    	  => false,
						'required'    => false,
						'for_type'	  => ''
					);
	        	}
	        	
	        }
    	}
        
    	
		$taxonomy_objects = get_object_taxonomies( 'listing', 'objects' );
        
        foreach ($taxonomy_objects as $tax) {
            $visual_fields[] = array(
				'label'       => $tax->label,
				'type'        => 'term-select',
				'placeholder' => $tax->label,
				'name'        => 'tax-'.$tax->name,
				'taxonomy'	  => $tax->name,
				'tooltip'	  => '',
				'priority'    => 10,
				'default'	  => '',
				'render_row_col' => '4',
				'multi'    	  => false,
				'required'    => false,
				'for_type'	  => ''
			);
            
        }

        $form = $this->generate_new_form_fields($visual_fields,$section);

       

        $response = array(
	        'items'	=> $form
	    );

    	wp_send_json_success(  $response );
    }

    function editor_save_field(){
    	

    	$field =  $_POST['field'];
    	$section =  $_POST['section'];
    	$fields = json_decode( wp_unslash($_POST['fields']) );
    	$field_name = '';
    	$field_section = '';
    	$option = get_option('listeo_submit_form_fields_temp',array());

    	foreach ( $fields as $key => $value ) {
    		
    		if($value->name == 'field'){
    			$field_name = $value->value;
    		} else if( $value->name == 'section' ){
    			$field_section = $value->value;
    		} else {
    			$option[$field_section]['fields'][$field_name][$value->name] = $value->value;
    		}
    	
    	}

    	update_option('listeo_submit_form_fields_temp',$option);
    	
    	$response = array(
	        'message' => 'Saved'
	    );

    	wp_send_json_success(  $response );
    }

    // function editor_delete_field(){

    // 	$field =  $_POST['field'];
    // 	$section =  $_POST['section'];
    // 	$fields_to_delete = get_option('listeo_submit_form_fields_delete_temp',awrray());
    // 	$fields_to_delete[$section] = $field;
    	
    // 	update_option('listeo_submit_form_fields_delete_temp',$fields_to_delete);

    // 	$response = array(
	   //      'message' => 'Removed'
	   //  );

    // 	wp_send_json_success(  $response );
    // }

    public function get_label_nicename($label){
    	switch ($label) {
    		case 'label':
    			$label = __('Label <span class="dashicons dashicons-editor-help" title="Text that is displayed before/next to the input field"></span>','listeo-fafe');
    			break;

    		case 'name':
    			$label = __('Name <span class="dashicons dashicons-editor-help" title="Name attribute on the input field, do not edit if you don\'t know what you are doing :)"></span>','listeo-fafe');
    			break;

    		case 'type':
    			$label = __('Type','listeo-fafe');
    			break;
    		case 'required':
    			$label = __('Required <span class="dashicons dashicons-editor-help" title="Tick the checkbox to make this field required in submit form"></span>','listeo-fafe');
    			break;
    		case 'placeholder':
    			$label = __('Placeholder <span class="dashicons dashicons-editor-help" title="Text that is displayed before/next to the input field"></span>','listeo-fafe');
    			break;	
    		case 'taxonomy':
    			$label = __('Taxonomy','listeo-fafe');
    			break;	
    		case 'priority':
    			$label = __('Priority ','listeo-fafe');
    			break;
    		case 'value':
    			$label = __('Default value ','listeo-fafe');
    			break;
    		case 'default':
    			$label = __('Default value','listeo-fafe');
    			break;	
    		case 'render_row_col':
    			$label = __('Width in form row','listeo-fafe');
    			break;
    		case 'description':
    			$label = __('Description <span class="dashicons dashicons-editor-help" title="Additional option description for the field"></span>','listeo-fafe');
    			break;
    		case 'tooltip':
    			$label = __('Tooltip','listeo-fafe');
    			break;
    		case 'class':
    			$label = __('CSS class','listeo-fafe');
    			break;
    		case 'atts':
    			$label = __('Attributes','listeo-fafe');
    			break;
    		case 'multi':
    			$label = __('Multiple choice','listeo-fafe');
    			break;
    		case 'options':
    			$label = __('Options','listeo-fafe');
    			break;
    		case 'show_field_if_booking_enable':
    			$label = __('Show field only if booking enabled','listeo-fafe');
    			break;
    		case 'show_field_if_group_active':
    			$label = __('Show field only if user group active','listeo-fafe');
    			break;
    		case 'for_type':
    			$label = __('For Type','listeo-fafe');
    			break;
    		case 'selected_cats':
    			$label = __('Show field only for selected category (Remember to select type)','listeo-fafe');
    			break;
    		case 'number_of_char':
    			$label = __('Number of character limit','listeo-fafe');
    			break;

    		
    		default:
    			return $label;
    			break;
    	}
    	return $label;
    }


    public function generate_form_fields($fields,$field,$section){
    	if($fields):
	    	$saved_fields = get_option("listeo_submit_form_fields");
	        $submit_fields = (!empty($saved_fields)) ? get_option("listeo_submit_form_fields") : $this->fields ;
	        
    		$fields = $submit_fields[$section]['fields'][$field];
    		if(!isset($fields['tooltip'])){
    			$fields['tooltip'] = '';
    		}
			if(!isset($fields['value'])){
    			$fields['value'] = '';
    		}

    		if(!isset($fields['class'])){
    			$fields['class'] = '';
    		}

    		ob_start();
    		
    		?>
			
				<table class="listeo-modal-table form-table">
					<tbody>
					
					<?php

					//if($fields["name"] == "tax-service_category" || $fields["name"] == "tax-rental_category" || $fields["name"] == "tax-event_category"){
						if(!isset($fields["for_type"])){
							$fields["for_type"] = "";
						}
						
					//}

					if(!isset($fields["show_field_if_booking_enable"])){
						$fields["show_field_if_booking_enable"] = "";
					}
					if(!isset($fields["show_field_if_group_active"])){
						$fields["show_field_if_group_active"] = "";
					}

					$catsss = array("service_category","event_category","rental_category");

                    $all_cats = array();

                    foreach ($catsss as $key => $cats) {
                    	$listing_category_terms_all = get_terms( array(
							          'taxonomy' => $cats,
							          'hide_empty'  => false,
							          'orderby' => 'term_order',
					        ));

                    	foreach ($listing_category_terms_all as $key => $parent) {
				        	if($parent->parent == "0"){
				        		$all_cats[] = $parent;
				        	}
				        	
				        }
                    }

                    if(!isset($fields["selected_cats"])){
						$fields["selected_cats"] = "";
					}

                    if(!isset($fields["number_of_char"])){
						$fields["number_of_char"] = "";
					}




		
	    			foreach ($fields as $key => $value) { 

	    				
	    				if(isset($fields_temp[$key])) {
	    					$value = $fields_temp[$key];
	    				}


	    				if(in_array($key,array('render_row_col','priority'))){
	    					continue;
	    				}


	    				?>
	    				<tr valign="top" class="field-edit-<?php echo $key;?>" 
	    					<?php  if(in_array($key,array('taxonomy','name'))){ ?>style="display: none;" <?php } ?>>
	    					<?php if($key =="number_of_char"){
	    						    if(isset($fields["type"]) && ($fields["type"] == "text" || $fields["type"] == "number" || $fields["type"] == "wp-editor" )){ ?>

										<th scope="row">
											<label for="field_meta_key">
											<?php echo $this->get_label_nicename($key); ?></label>
										</th>

									<?php }

	    					}else{?>
		    					<th scope="row">
									<label for="field_meta_key">
									<?php echo $this->get_label_nicename($key); ?></label>
								</th>
						    <?php } ?>

							<td>
							<?php 
							
							switch ($key) {

								case 'type':
									?>
									<select name="<?php echo $section.'['.$field.']['.$key.']'; ?>" class="widefat" type="text" ref="config" id="field_<?php echo $key; ?>" >
										<option <?php selected('wp-editor',$value); ?> value="wp-editor">WYSIWG Editor</option>
										<option <?php selected('text',$value); ?> value="text">Text input</option>
										<option <?php selected('select',$value); ?> value="select">Select dropdown</option>
										<option <?php selected('checkboxes',$value); ?> value="checkboxes">Checkboxes list</option>
										<option <?php selected('checkbox',$value); ?> value="checkbox">Single On/Off checkbox</option>
										<option <?php selected('number',$value); ?> value="number">Number input</option>

										<option <?php selected('term-select',$value); ?> value="term-select">Select taxonomy</option>
										<option <?php selected('term-checkboxes',$value); ?> value="term-checkboxes">Taxonomy checkboxes</option>

										<option <?php selected('slots',$value); ?> value="slots">Time slots (booking)</option>
										<option <?php selected('calendar',$value); ?> value="calendar">Availability Calendar</option>
										<option <?php selected('hours',$value); ?> value="hours">Opening hours</option>

										<option <?php selected('skipped',$value); ?> value="skipped">Skipped input</option>
										<option <?php selected('pricing',$value); ?> value="pricing">Pricing (menu)</option>
										
										<!-- added section in the option menu-->
										<option <?php selected('section',$value); ?> value="section">Section (menu)</option>
										
										<option <?php selected('hidden',$value); ?> value="hidden">Hidden input</option>
										<option <?php selected('files',$value); ?> value="files">Files (Gallery)</option>
										<option <?php selected('file',$value); ?> value="file">Single File Upload</option>
										<option <?php selected('timezone',$value); ?> value="timezone">Timezone field</option>
									</select>

									
									<?php
									break;
								case 'number_of_char':
									if(isset($fields["type"]) && ($fields["type"] == "text" || $fields["type"] == "number" || $fields["type"] == "wp-editor" )){ ?>

										<input type="number" name="<?php echo $section.'['.$field.']['.$key.']'; ?>" value="<?php echo esc_attr(stripslashes($value)); ?>" class="widefat" id="field_<?php echo $key; ?>">

									<?php }
								break; 
								case 'multi': 
								case 'required': ?>
									<input name="<?php echo $section.'['.$field.']['.$key.']'; ?>" <?php checked(1,$value) ?> class="widefat" type="checkbox" id="field_<?php echo $key; ?>" value="1" >
								<?php
								break;

								case 'atts':
								case 'options':
									
									echo '<table>';
									foreach ($value as $option_key => $value) { ?>
											<tr>
												<td><?php echo stripslashes($option_key) ?></td>
												<td><input name="<?php echo $section.'['.$field.']['.$key.']['.$option_key.']'; ?>" class="widefat" type="text" ref="config" id="field_<?php echo $option_key; ?>" value="<?php echo esc_attr(stripslashes($value)); ?>" ></td>
											</tr>
									<?php }
									echo '</table>';
								break;

								case 'show_field_if_booking_enable':
								?>

								    <input type="checkbox" name="<?php echo $section.'['.$field.']['.$key.']'; ?>" <?php if($value == "1"){ echo "checked";} ?> value="1" class="field_<?php echo $key; ?>">

								     
                                <?php
								break;
								case 'show_field_if_group_active':
								?>

								    <input type="checkbox" name="<?php echo $section.'['.$field.']['.$key.']'; ?>" <?php if($value == "1"){ echo "checked";} ?> value="1" class="field_<?php echo $key; ?>">

								     
                                <?php
								break;

								case 'selected_cats':
								?>
								   
			                    	<hr>

								    <?php foreach ($all_cats as $key_cat => $all_cat) { ?>
								    	<span class="checkboxess">
                    						<input type="checkbox" name="<?php echo $section.'['.$field.']['.$key.'][]'; ?>" <?php if(in_array($all_cat->slug,$value)){ echo "checked"; }?> value="<?php echo $all_cat->slug;?>" class="field_<?php echo $key; ?>"> <?php echo $all_cat->name;?>
                    					</span>
                    				<?php } ?>
                    				<hr>

								     
                                <?php
								break;
								case 'for_type':
									?>
									<!-- <select name="<?php echo $section.'['.$field.']['.$key.']'; ?>" class="widefat"  type="text" ref="config" id="field_<?php echo $key; ?>" >
										<option value="">All</option>
										<option <?php selected('rental',$value); ?> value="rental">Rental type</option>
										<option <?php selected('service',$value); ?>  value="service">Service type</option>
										<option <?php selected('event',$value); ?> value="event">Event type</option>
										
									</select> -->
									<input type="checkbox" name="<?php echo $section.'['.$field.']['.$key.'][]'; ?>" <?php if(in_array('rental',$value)){ echo "checked";} ?> value="rental" class="field_<?php echo $key; ?>">Rental type
									<input type="checkbox" name="<?php echo $section.'['.$field.']['.$key.'][]'; ?>" <?php if(in_array('service',$value)){ echo "checked";} ?> value="service" class="field_<?php echo $key; ?>">Service type
									<input type="checkbox" name="<?php echo $section.'['.$field.']['.$key.'][]'; ?>" <?php if(in_array('event',$value)){ echo "checked";} ?> value="event"  class="field_<?php echo $key; ?>">Event type
									<input type="hidden" name="<?php echo $section.'['.$field.']['.$key.'][]'; ?>" value="">
									<?php
								break;
								
								
								default:
									?>
									<input name="<?php echo $section.'['.$field.']['.$key.']'; ?>" class="widefat" type="text" ref="config" id="field_<?php echo $key; ?>" value="<?php echo stripslashes(esc_attr($value)); ?>" >
									<?php
									break;
							} ?>
								
								
							</td>
						</tr>
		    		<?php } ?>
		    		</tbody>
				</table>
		<?php 
	    	return ob_get_clean();
	    endif;	

    }

    public function generate_new_form_fields($fields,$section = 'new_field_section'){
    	if($fields):

    		ob_start();
	    			foreach ($fields as $field_key => $field) { ?>
						<div class="listeo-fafe-forms-editor-new-elements-container">
							<a href="#" data-section="<?php echo $section; ?>" class="insert-field button"><?php echo $field['label']; ?></a>
	    				<div style="display:none;" class='editor-block block-width-12 block-<?php echo sanitize_title($field['name']) ?>' >
							<h5><?php echo $field['label']; ?></h5>
							<div class="editor-block-tools">
								<input type="text" class="block-width-input" name="<?php echo $section; ?>[<?php echo $field['name']; ?>][render_row_col]" value="12">
								<input type="hidden" name="section[]" value="<?php echo $section; ?>">
								<input type="hidden" name="field[]" value="<?php echo $field['name']; ?>">
								
								<ul>
									<li class="block-edit"><a data-section="<?php echo $section; ?>" data-id="<?php echo $field['name']; ?>" href="#" class="button button-primary"></a></li>
									<li class="block-narrower"><a href="#"></a></li>
									<li class="block-wider"><a href="#"> </a></li>
									<li class="block-delete"><a href="#"></a></li>
								</ul>

								
							</div>
							<div class="editor-block-form-fields">
							<table class="editor-field-preset listeo-modal-table form-table">
								<tbody>
		    					<?php 
		    						foreach ($field as $key => $value) { ?>
		    					
				    				<tr valign="top">
				    					<th scope="row">
											<label for="field_meta_key">
											<?php echo $this->get_label_nicename($key); ?></label>
										</th>

										<td>
										<?php 
										
										switch ($key) {
											
											case 'type':
												if($value == 'datetime') {
												$value = 'text'; ?>
													<input name="<?php echo $section.'['.$field['name'].'][class]'; ?>" class="widefat" type="text" ref="config" id="field_class" value="input-datetime" >
												<?php }
												if($value == 'multicheck') { $value = 'checkboxes'; }
												if($value == 'select_multiple') { $value = 'select'; }
												?>
												<select name="<?php echo $section.'['.$field['name'].']['.$key.']'; ?>" class="widefat" type="text" ref="config" id="field_<?php echo $key; ?>" >

													<option <?php selected('wp-editor',$value); ?> value="wp-editor">WYSIWG Editor</option>
													<option <?php selected('text',$value); ?> value="text">Text input</option>
													<option <?php selected('select',$value); ?> value="select">Select dropdown</option>
													<option <?php selected('checkboxes',$value); ?> value="checkboxes">Checkboxes list</option>
													<option <?php selected('checkbox',$value); ?> value="checkbox">Single On/Off checkbox</option>
													<option <?php selected('number',$value); ?> value="number">Number input</option>

													<option <?php selected('term-select',$value); ?> value="term-select">Select taxonomy</option>
													<option <?php selected('term-checkboxes',$value); ?> value="term-checkboxes">Taxonomy checkboxes</option>

													<option <?php selected('slots',$value); ?> value="slots">Time slots (booking)</option>
													<option <?php selected('calendar',$value); ?> value="calendar">Availability Calendar</option>
													<option <?php selected('hours',$value); ?> value="hours">Opening hours</option>

													<option <?php selected('skipped',$value); ?> value="skipped">Skipped input</option>
													<option <?php selected('pricing',$value); ?> value="pricing">Pricing (menu)</option>
													
													<option <?php selected('section',$value); ?> value="section">Section (menu)</option>
													
													<option <?php selected('hidden',$value); ?> value="hidden">Hidden input</option>
													<option <?php selected('files',$value); ?> value="files">Files (Gallery)</option>
													<option <?php selected('file',$value); ?> value="file">Single File Upload</option>
													
												</select>
												
												<?php
												break;
											case 'required': ?>
												<input name="<?php echo $section.'['.$field['name'].']['.$key.']'; ?>" <?php checked(1,$value) ?> class="widefat" type="checkbox" id="field_<?php echo $key; ?>" value="<?php echo $value; ?>" >
											<?php
											break;

											case 'atts':
											case 'options':
												
												echo '<table>';
												foreach ($value as $temp_key => $value) { ?>
														<tr>
															<td><?php echo $temp_key ?></td>
															<td><input name="<?php echo $section.'['.$field['name'].']['.$key.']['.$temp_key.']'; ?>" class="widefat" type="text" ref="config" id="field_<?php echo $temp_key; ?>" value="<?php echo esc_attr($value); ?>" ></td>
														</tr>
												<?php }
												echo '</table>';
											break;
											case 'for_type':
												?>
												<select name="<?php echo $section.'['.$field['name'].']['.$key.']'; ?>" class="widefat"  type="text" ref="config" id="field_<?php echo $key; ?>" >
													<option value="">All</option>
													<option <?php selected('rental',$value); ?> value="rental">Rental type</option>
													<option <?php selected('service',$value); ?>  value="service">Service type</option>
													<option <?php selected('event',$value); ?> value="event">Event type</option>
													
												</select>
												<?php
											break;
											
											default:
												?>
												<input name="<?php echo $section.'['.$field['name'].']['.$key.']'; ?>" class="widefat" type="text" ref="config" id="field_<?php echo $key; ?>" value="<?php echo (stripslashes($value)); ?>" >
												<?php
												break;
										} ?>
											
											
										</td>
									</tr>
									
									<?php
		    					} ?>
		    					</tbody>
							</table>

							</div>
						</div>
						</div>
					
	    			<?php } ?>
		    		
		<?php 
	    	return ob_get_clean();
	    endif;	

    }



}
?>
