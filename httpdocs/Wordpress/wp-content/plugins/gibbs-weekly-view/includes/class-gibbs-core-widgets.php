<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listeo Core Widget base
 */
class Custom_Gibbs_Widget extends WP_Widget {
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
		add_action( 'wp_footer', 'your_function', 100 );

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
		add_action( 'wp_footer', array( $this, 'add_popup' ),100 );

		
	}

	public function add_popup() {
	    	?>
	    	<div class="modal_div">
				<div class="add_event_modal" style="display: none;">
                        <div class="event_innr template-container">
                            <h3>Velg tid</h3>
                            <a class="cloz_btn" href="#">&times;</a>
                        <!--     <div class="row mb-20px">
                            	<div class="col-md-12">
                            		 <label>Formål</label>
	                                <?php /*<input readonly value="2014-03-15T05:06" class="date_time_sel from_tm" type="text" name="cst_from"/> */ ?>
	                                <select name="purpose" class="purpose" >
	                                	<option value=""></option>
	                                	<option value="Trening">Trening</option>
	                                	<option value="Kamp">Kamp</option>
	                                	<option value="Arrangement">Arrangement</option>
										<option value="Selskap">Selskap</option>
	                                	<option value="Møte">Møte</option>
	                                </select>
                            	</div>
                            	
                            </div>  --> 
                            <div class="row mb-20px">
                            	<div class="col-md-6">
                            		 <label>Fra</label>
	                                <?php /*<input readonly value="2014-03-15T05:06" class="date_time_sel from_tm" type="text" name="cst_from"/> */ ?>
	                                <select name="time_from" class="cst_dt_from"></select>
                            	</div>
                            	<div class="col-md-6">
                            		 <label>Til</label>
	                                <?php /*<input readonly value="2014-03-15T05:06" class="date_time_sel from_tm" type="text" name="cst_from"/> */ ?>
	                                <select name="time_to" class="cst_dt_to"></select>
                            	</div>
                            </div>  
                            <div class="row toggleInfo">
                            	<div class="col-md-6">
                            		<label>Ønsker du å repetere bookingen?</label>
                            	</div>
                            	<div class="col-md-6">
                            		<label class="switch">
									  <input type="checkbox" class="repeated_check">
									  <span class="slider round"></span>
									</label>
                            	</div>
                            </div>
                            <div class="row repeating_div" style="display: none">
                            	
		                           <div data-bind="value:recurrenceRule" title="RecurrenceRule control" name="recurrenceRule" data-role="recurrenceeditor">
                                        <div class="k-recur-view">
                                            <div class="mb-20px">
                                                <label>Hvilke dager du vil repetere?</label>
                                                <div class="k-button-group-stretched k-recur-weekday-buttons k-widget k-button-group mainSelect" title="Gjenta på:" data-role="buttongroup" role="group" tabindex="0">
                                                    <span data-value="MO" onclick="selectClassAdd(this)" aria-label="Gjenta på: mandag" aria-pressed="false" role="button" class="k-button">Man</span>
                                                    <span data-value="TU" onclick="selectClassAdd(this)" aria-label="Gjenta på: tirsdag" aria-pressed="false" role="button" class="k-button">Tir</span>
                                                    <span data-value="WE" onclick="selectClassAdd(this)" aria-label="Gjenta på: onsdag" aria-pressed="false" role="button" class="k-button">Ons</span>
                                                    <span data-value="TH" onclick="selectClassAdd(this)" aria-label="Gjenta på: torsdag" aria-pressed="true" role="button" class="k-button">Tor</span>
                                                    <span data-value="FR" onclick="selectClassAdd(this)" aria-label="Gjenta på: fredag" aria-pressed="false"role="button" class="k-button">Fre</span>
                                                    <span data-value="SA" onclick="selectClassAdd(this)" aria-label="Gjenta på: lørdag" aria-pressed="false"role="button" class="k-button">Lør</span>
                                                    <span data-value="SU" onclick="selectClassAdd(this)" aria-label="Gjenta på: søndag" aria-pressed="false"role="button" class="k-button">Søn</span>
                                                </div>
                                            </div>
                                            <div class="row mb-20px">
                                            	<div class="col-md-12">
				                            		    <div class="d-flex-oin end_repeat_main">
                                                            <label>Slutt repetering på</label>
                                                            <input type="date" class="end_repeat">
                                                            <i class="fa fa-calendar icon_rp"></i>
                                                        </div>
				                            	</div>
				                            	<!-- <div class="col-md-6">
				                            		<div class="d-flex-oin">
                                                        <label>Repeter hver</label>
                                                        <div class="week_bk">
                                                        	<input type="number" value="1" min="1" class="week_day"> <span>Uke(er)</span>
                                                        </div>
                                                    </div>
				                            	</div> -->
                                            </div>
                                        </div>
                                    </div>
                                    
                            </div>

                          
                            
                      
                         <div class="row footerSub">
                            	<div class="col-md-12 justify-content-items">

                            		<button class="cloz_btn closeBtn button" onclick="jQuery('.add_event_modal').hide();">Lukk</button>
                            		<button class="add_evnt button">Velg</button>
                            	</div>
                            </div>

                            </div>
				</div>

				<div class="weeklymodal" style="display: none;">
                        <div class="event_innr template-container">
                            <h3 class="title"></h3>
                            <a class="cloz_btn" href="#">&times;</a>
                            <div class="row mb-20px">
                            	<div class="col-md-12 weeklybody">
                            		
                            	</div>
                            	
                            </div>  


                            <div class="row footerSub">
                            	<div class="col-md-12 justify-content-items">

                            		<button class="cloz_btn closeBtn button" onclick="jQuery('.weeklymodal').hide();">Lukk</button>
                            		<button class="apply_conflict button">Velg</button>
                            	</div>
                            </div>
                            

                          

                        </div>
				</div>
			</div>


	    	<?php
	    	require("repeat_script.php");
	        
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
 * Booking Widget
 */
class Custom_Gibbs_Booking_Widget extends Custom_Gibbs_Widget {

    public function __construct() {

		// create object responsible for bookings
		$this->bookings = new Gibbs_Booking_Calendar;

		$this->widget_cssclass    = 'listeo_core boxed-widget booking-widget margin-bottom-35 gibbs_cal';
		$this->widget_description = __( 'Shows Booking Form.', 'listeo_core' );
		$this->widget_id          = 'widget_cust_booking_listings';
		$this->widget_name        =  __( 'Custom Booking Form', 'listeo_core' );
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
		
        global $wpdb;

		//$daytest;

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
        $parent_listing_id = $queried_object->ID;
        //$users_groups_id = $_POST['users_groups_id'];
        $users_groups_id = $post_dd->users_groups_id;
        if($parent_listing_id != "" && $parent_listing_id != 0){
            $post_db = $wpdb->prefix . 'posts';  // table name
                
            $query = "SELECT id,post_title FROM $post_db WHERE post_parent = $parent_listing_id AND post_type='listing'";
            $sub_data = $wpdb->get_results($query);

        }else{
            $sub_data = array(); 
        }
        $sub_html = '';
        
        if($offer_type!='rental')
        {
            if(!empty($sub_data) || (!empty($_GET['sub'])))
            {
                $sub_furl = $_GET['sub'];
                $sub_sel_arr = explode(",", $sub_furl);
                if(empty($_GET['sub']))
                {
                //  $before_title .= '<div class="book_cal_cst">';
                    //$after_title .= '</div>';
                    ?>
                    <style>.gibbs_cal{ display:none !important; }</style>
                    <?php
                }
                ?>
    
        <div class="sub_listing_box">
            <h3 class="widget-title margin-bottom-35">Velg kalender</h3>
            <div class="shadowBox">
                <div class="left_ara">
                
                <select multiple class="sub_selector" name="sub_selected[]">
                    <?php
                    foreach($sub_data as $sd)
                    {
                        ?>
                        <option <?php if(in_array($sd->id, $sub_sel_arr)){ ?>selected="selected" <?php } ?> value="<?php echo $sd->id; ?>"><?php echo $sd->post_title; ?></option>
                        <?php 
                    }
                    ?>
                </select>
                </div>
                <div class="rt_ara">
                <button type="button" class="button apply_sub">Velg</button>
                   </div>
                </div>
            </div>
            <?php
            }
        }
        echo $before_widget;
        $after_title .= '<a class="today_ic" href="#">Idag</a>';
        if ($title) {
            echo $before_title . '' . $title  . $after_title;
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
/*
        $_booking_system_service = get_post_meta($post_info->ID,"_booking_system_service",true);
        $_booking_slots = get_post_meta($post_info->ID,"_booking_slots",true);

        if($_booking_system_service == "on" && !empty($_booking_slots)){
            $slots = $_booking_slots;
        }else{
        	$slots = false;
        }*/

		global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }

        
        // get opening hours
        if (isset($post_meta['_opening_hours'][0])) {
            $opening_hours = json_decode($post_meta['_opening_hours'][0], true);
        }
        $__booking_system_service = "";
        if(isset($post_meta['_booking_system_service'][0]) && $post_meta['_booking_system_service'][0]){
        	$__booking_system_service = "true";
        }

        $_booking_system_rental = "";
        if(isset($post_meta['_booking_system_rental'][0]) && $post_meta['_booking_system_rental'][0]){
        	$_booking_system_rental = "true";
        }

        $_booking_system_weekly_view = "";
        if(isset($post_meta['_booking_system_weekly_view'][0]) && $post_meta['_booking_system_weekly_view'][0]){
        	$_booking_system_weekly_view = "true";
        }
        $_booking_system_equipment = "";
        if($post_meta['_booking_system_equipment'][0]=='on'){
        	$_booking_system_equipment = "true";
        }


       

        if ($post_meta['_listing_type'][0] == 'service' && ($_booking_system_weekly_view != "" || $_booking_system_equipment != "")){
        	$removeTranslateUrl = str_replace("/en","",home_url());
        	
        	// wp_enqueue_script('listeo-custom2', home_url() . '/wp-content/plugins/listeo-core/assets/js/bookings--old.js');
    		wp_enqueue_script('listeo-custom2', $removeTranslateUrl . '/wp-content/plugins/listeo-core/assets/js/bookings--old.js', array(), $version);

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
	                    //'owner'
	                );
	            }

                $_min_book_days = get_post_meta($post_info->ID,"_min_book_days",true);
		        $_max_book_days = get_post_meta($post_info->ID,"_max_book_days",true); ?>

		        <script>
		        	let _max_book_days = "<?php echo $_max_book_days;?>";
		        	let _min_book_days = "<?php echo $_min_book_days;?>";
		        </script>
		        <?php 



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

        <?php
    
        //echo $_booking_system_equipment;
        //die;
        if($_booking_system_equipment != ""){
        	$_max_guests = get_post_meta( $post_info->ID, '_max_guests', true );
        	if(!$_max_guests){
        		$_max_guests = 0;
        	}
        ?>
        <div class="equipment_noti" style="background: #e3f4fc;padding: 13px;text-align: center;color: #4b85a9;">
        	<div class="span_not">Select time to see availability</div>
        	<div class="max_av_div">Maximum available: <span class="max_av"><?php echo $_max_guests;?></span></div>
        </div>
        
        <?php	

        }
        ?>
        <script type="text/javascript">
                        if(jQuery('.sub_selector').length)
                        {
                            jQuery(".sub_selector").multiselect({
                                includeSelectAllOption: true,
                                nonSelectedText: 'Vennligst velg',
                                nSelectedText: 'Valgt',
                                allSelectedText: 'Alle er valgt',
                                /*
                                onDropdownHide: function(event) {
                                    red_listing();
                                } */
                            });
                        }
                        jQuery(document).on('change', '.sub_selector', function(){
                           // red_listing();
                        });
                        function red_listing()
                           {
                                var kl = jQuery('.sub_selector').val();
                                var url = location.protocol + '//' + location.host + location.pathname

                                url = url+"?sub="+kl;
                                var url_new = window.location.href;
                                console.log(url);
                                console.log(url_new);
                                if(url_new!=url)
                                {
                                    if(kl!='')
                                {
                                    location.href = url;
                                }
                                else
                                {
                                    url = url.split('?')[0];
                                    location.href = url;
                                }
                                }
                               
                           }
         jQuery(document).ready(function(){
         		jQuery("#listeo_core-bookings-js").remove();
         		/* toHours Dropdown on without s elect border red 
			jQuery(document).on('change','#toHours', function(){
				if(jQuery("#toHours option[value='Select time']").length > 0){
				    jQuery('#toHours').css('border-color', 'red');
				    jQuery('#toHours').css('color', 'red');
				    console.log('value changes to be red is ', this.value)
				} else {
				    jQuery('#toHours').css('border-color', 'white');
				    jQuery('#toHours').css('color', '#888');
				    console.log('value changes to be white is ', this.value)
				}
			});
			/* toHours Dropdown on without s elect border red */
         })
        </script>


<div class="timer-loader-new lds-roller" style="position: absolute; top: 200px; left: 138px; z-index: 10; display: none;">
                                	<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                                </div>
        <div class="row with-forms  margin-top-0"  id="booking-widget-anchor">
        	<?php
        	$extra_params = "";
        	if(isset($_GET['hide']) && $_GET['hide'] == true){
               $extra_params = "?hide=true";
        	}
        	?>
            <form id="form-booking" data-post_id="<?php echo $post_info->ID; ?>" class="form-booking-<?php echo $post_meta['_listing_type'][0]; ?>" action="<?php echo esc_url(get_permalink(get_option('listeo_booking_confirmation_page'))).$extra_params; ?>" method="post">


                <?php if ($post_meta['_listing_type'][0] != 'event') {


                    $minspan = get_post_meta($post_info->ID, '_min_days', true); ?>
                    <?php
                    //WP Kraken
                    // If minimub booking days are not set, set to 2 by default
                    if (!$minspan && $post_meta['_listing_type'][0] == 'rental') {
                        $minspan = 2;
                    }
                    ?>
                    <!-- Date Range Picker sdsd - docs: http://www.daterangepicker.com/ -->
                    <div class="col-lg-12" style="display:none;">
                        <input type="text" data-minspan="<?php echo ($minspan) ? $minspan : '0'; ?>" id="date-picker" readonly="readonly" class="date-picker-listing-<?php echo esc_attr($post_meta['_listing_type'][0]); ?>" autocomplete="off" placeholder="<?php esc_attr_e('Date', 'listeo_core'); ?>" value="" listing_type="<?php echo $post_meta['_listing_type'][0]; ?>" />
                    </div>
                    <div class="col-lg-12 notification notice notifitest" style="font-size:13.5px;text-align:center;margin: 15px 0 0 0;">
                                Vennligst trykk på ledig tid <br> i kalenderen for å booke
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
														<input type="hidden" class="slot_avv" value="<?php echo $slot[1];?>">								
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-12" style="padding: 0; margin: 0;">
	                        <div class="sel_cls" style="width:100%;">
							<span>Jump To</span>
	                        <select class="select_month form-control">
	                       
	                        </select>
	                        </div>
	    
	                        <div id="kt_docs_fullcalendar_populated"></div>


                        </div>
                        <?php
                        $_booking_system_equipment = get_post_meta( $post_info->ID, '_booking_system_equipment', true );
                        if($_booking_system_equipment == 0){
                        	$_booking_system_equipment = "";
                        }
                        ?>
                        <div class="col-xs-12 col-md-12 poraka" style="display:none; text-align: center; margin: 10px 0 0 0; padding: 10px; font-size: 15px; background: #ffebeb;color:#d83838"><span></span></div>
                        <div class="row col-lg-12 cstm_blk" style="margin: 15px 0px 10px 16px;color: black;font-size:14px;">
                            <div class="row">
                                <div class="col-xs-6 col-md-6" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #1D9781;border-radius: 50%;display: inline-block;"></span><span> Valgt tid</span></div>
                                <div class="col-xs-6 col-md-6" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #DA697A;border-radius: 50%;display: inline-block;"></span><span> 
                                	<?php if($_booking_system_equipment != ""){ echo "Fully booked";}else{ echo  "Booket";}?>
                                	</span></div>
                                <?php  $autobook = get_post_meta(get_the_ID(),'_instant_booking',true); 
                           if(!$autobook == 'on'){
                           ?>
                                <div class="col-xs-6 col-md-6 cstm_blk" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #FF9900;border-radius: 50%;display: inline-block;"></span><span> <?php if($_booking_system_equipment != ""){ echo "Partially booked";}else{ echo  "Reservert";}?></span></div>
                           <?php } ?>
                                <?php  $autobook = get_post_meta(get_the_ID(),'_instant_booking',true); 
                                 if($autobook != 'on'){
                           ?>
                                <!-- <div class="col-xs-4 col-md-4" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #FF9900;border-radius: 50%;display: inline-block;"></span><span> Reservert</span></div> -->
                           <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 col-md-6" style="padding: 0;"><span style="border: black 1px solid;height: 11px;width: 11px;background-color: #FFFFFF;border-radius: 50%;display: inline-block;"></span><span> Ledig</span></div>
                                <div class="col-xs-6 col-md-6" style="padding: 0;"><span style="height: 11px;width: 11px;background-color: #C1C1C1;border-radius: 50%;display: inline-block;"></span><span> Ikke tilgjengelig</span></div>
                            </div>
                        </div>

                        <div class="row fratil" style="margin: 10px 0px 0px 0; font-size: 10px;">
                            <div class="col-xs-5" style="padding-left: 16px;">FRA</div>
                            <div class="col-xs-2"></div>
                            <div class="col-xs-5" style="padding:0">TIL</div>
                        </div>
                        <div class="row timeSpan" style="display:none;margin: 0px 10px 0px 0px;text-align: center;">
                            <div class="col-xs-5" style="padding: 0px;">
                                <input readonly id="timeSpanFrom" style="text-align:center;pointer-events:none;margin-left: 10px;font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: Roboto; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;">
                            </div>
                            <div class="col-xs-2"></div>
                            <div class="col-xs-5" style="padding: 0px;">
                                <input readonly id="timeSpanTo" style="text-align:center;pointer-events:none;font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: Roboto; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;">
                            </div>
                        </div>
                        <div class="row timenotifi" style="margin: 0px 10px 0px 0px;text-align: center;">
                            <div class="col-xs-5" style="padding: 0px;">
                                <select style="margin-left: 10px;font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: Roboto; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;" name="fromH" id="fromHours">
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
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
                                <select style="font-size: 16px;font-weight:600;color:#888; padding: 0 0 0 12px; font-family: Roboto; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;" name="toH" id="toHours">
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>

                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
                                    <option></option>
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
                                // jQuery('.timer-loader').show();
                               // jQuery('.timer-loader-new').show();
                                jQuery('.tabela').css('opacity', '0.1');
                                jQuery('.tabela').css('pointer-events', 'none');
                                setTimeout(function() {
                                    // jQuery('.timer-loader').hide();
                                    jQuery('.timer-loader-new').hide();
                                    jQuery('.tabela').css('opacity', '1');
                                    jQuery('.tabela').css('pointer-events', 'all');
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
                            let already_booked = [];
                            let waiting_dt = [];
                            let mt = '';
                            let me = '';
                            let mkl = '';
                            let bkl = '';
                            const f5 = "YYYY-MM-DDTHH:mm:ss";
                            let mstt = '';
                            let mend = '';
                            let ks = [];
                            let approved_bookingss = [];
                            let waiting_bookingss = [];
                            <?php
                            global $wpdb;
                            $id = $post_info->ID;
                            $_currDate = date("m/d/Y");
                            if(!empty(@$_GET['sub']))
                            {
                                $sd = $_GET['sub'];
                              $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `listing_id` IN (" . $sd . ")");    
                            }
                            else
                            {
                                $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `listing_id` = '$id'");
                            }
                            foreach ($results as $key => $result) {
                            	$result->purpose = "";
                            	if($result->comment && $result->comment != ""){
                            		$purpose_commnet = json_decode($result->comment);
                            		if(isset($purpose_commnet->purpose)){
                            			$result->purpose = $purpose_commnet->purpose;
                            		}
                            	}
                                $post_data = get_post($result->listing_id);
								$result->listing_name = "";
								if($post_data->post_title){
								  $result->listing_name = $post_data->post_title;
								}
                            }

                            $results_parent = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `parent_listing_id` = '$id'");
                            foreach ($results_parent as $key => $results_par) {
                            	$results_par->purpose = "";
                            	if($results_par->comment && $results_par->comment != ""){
                            		$purpose_commnet2 = json_decode($results_par->comment);
                            		if(isset($purpose_commnet2->purpose)){
                            			$results_par->purpose = $purpose_commnet2->purpose;
                            		}
                            	}
                            	$post_data2 = get_post($results_par->listing_id);
								$results_par->listing_name = "";
								if($post_data2->post_title){
								  $results_par->listing_name = $post_data2->post_title;
								}
                            }
                            $unavailableResults = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "r` WHERE `listing_id` = '$id' AND `status` = 'unavailable'");
                            $waiting = array();
                            $approved = array();
                            $rejected = array();
                            $_currDate = date("m/d/Y");
                            $fev = array();
                            foreach($results_parent as $rsp)
                            {
                                if(!empty($rsp->first_event_id))
                                {
                                    $fev[] = $rsp->first_event_id;
                                }
                            }
                            $fev = array_unique($fev);
                            $fex =  implode(', ', $fev);
                            //$results_extra = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `id` IN (".$fex.")");
                            $count_equipment = array();

                            $_booking_system_equipment = get_post_meta( $id, '_booking_system_equipment', true );
                            $instant_booking = get_post_meta($id, '_instant_booking', true);
                            $color = "#DA697A";
                            if(empty($instant_booking))
                            {
                                $color = "#DA697A";
                            }
                            if($_booking_system_equipment == 0){
                            	$_booking_system_equipment = "";
                            }
                            $_max_guests = get_post_meta( $id, '_max_guests', true );
                            //echo print_r($fex, true);
                            //echo print_r($results_extra, true); die;
                            foreach($results_extra as $rt_ex)
                            {
                                $results[] = $rt_ex;
                            }

                            foreach ($results as $key11 => $item) {
                                if ($_currDate < $item->date_start) {

                                    $start = date_format(date_create($item->date_start), "m/d/Y");
                                    $end = date_format(date_create($item->date_end), "m/d/Y");
                                    $stHour = date_format(date_create($item->date_start), "H");
                                    $enHour = date_format(date_create($item->date_end), "H");

                                    if ($item->status == 'waiting' || $item->status == 'attention') {
                                    	$color = "#FF9900";
                                        //$waiting[] = "{$start}|{$end}|{$stHour}|{$enHour}";
                                        $dpk = array(
                                            'start' => $item->date_start,
                                            'end' => $item->date_end,
                                            'color' => $color,
                                            'purpose' => $item->purpose,
                                            'listing_name' => $item->listing_name,
                                        );
                                        $waiting[] = $dpk;
                                        ?>
                                       /* mt = new Date("<?php echo $item->date_start; ?>");
                                        me = new Date("<?php echo $item->date_end; ?>");
                                        
                                        mstt = moment(mt).format(f5);
                                        mend = moment(me).format(f5);
                                        let bs = [];
                                        bs["start"] = mstt;
                                        bs["end"] = mend;
                                        waiting_dt.push(bs); */
                                        <?php

                                            $purpose =  $item->purpose;
                                            $listing_name =  $item->listing_name;
                                            
                                            $recurrenceRule = $item->recurrenceRule;
		                                    if($recurrenceRule!='' && $recurrenceRule!=null){

		                                    	$recurrenceRule = explode(';', $recurrenceRule);

	                                    		$rulesss = array();

											    foreach ($recurrenceRule as $key => $rule) {
											        if($rule != ""){
											          $rule = explode("=", $rule);
											          if($rule[1] != ""){
														$rulesss[$rule[0]] = $rule[1];
													  }
											        }
											    }

											    if(isset($rulesss["UNTIL"]) && $rulesss["UNTIL"] != ""){
											      $rulesss["UNTIL"] = date("Ymd\T", strtotime($rulesss["UNTIL"]))."235959";
											    }

											    $final_rules = array();

											    foreach ($rulesss as $key_rulee => $ruleeee) {
											        $final_rules[] = $key_rulee."=".$ruleeee;
											    }


											    $recurrenceRule = implode(";",$final_rules);

												    


                                                     
                                                ?>
                                               
                                                   var recBooking = new rrule.RRule.fromString("<?php echo $recurrenceRule;?>");
                                                    if(recBooking.options.until == null){
									                    var dddd = new Date(recBooking.options.dtstart);
									                    dddd.setDate(dddd.getDate() + 100);
									                    recBooking.options.until = dddd;
									                }

									                var dt_startt = new Date("<?php echo $item->date_start;?>");
									                dt_startt.setDate(dt_startt.getDate());
									                var dt_end = new Date("<?php echo $item->date_end;?>");
									                recBooking.options.dtstart = dt_startt;

									                function isJsonString(str) {
													    try {
													        JSON.parse(str);
													    } catch (e) {
													        return false;
													    }
													    return true;
													}

									                function isDeleted(orignalItem, booking) {
									                	var bookingDate = moment.utc(booking).format("YYYY-MM-DD");

									                	var rec_datess = [];

									                	if(isJsonString(orignalItem)){
                                                            
                                                            rec_datess = JSON.parse(orignalItem);

									                	}else{

									                		var allRecArr = orignalItem.split(",");

									                		

									                		allRecArr.forEach(function(rec_dd){

									                			rec_datess.push(moment.utc(rec_dd).format("YYYY-MM-DD"));
               
									                		})

									                	}

									                	if (rec_datess.find(item => item == bookingDate)) {
									                        return true;
									                    } else {
									                        return false;
									                    }
									                    
									                }
									                function libRecExp(currentItem, eventItem) {
									                    var eventTime = new Date(eventItem.start);//object
									                    var eventHours = eventTime.getHours();
									                    var eventMin = eventTime.getMinutes();
									                    currentItem.setHours(eventHours, eventMin, 0, 0);
									                    return currentItem.toISOString();


									                }

									                recBooking = recBooking.all();



									               

									                if (recBooking.length > 0) {
									                    recBooking.forEach(function (item) { //Bookings List

									                        var month = item.getMonth() + 1;
															var dateee = item.getDate();

															if(month < 10){
													           month = "0"+month;
															}
															if(dateee < 10){
													           dateee = "0"+dateee;
															}

															<!-- var tempRecExp = libRecExp(item, booking); -->

									                       var itemss = {};
									                       itemss["start"] = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+dt_startt.getHours()).slice(-2)+":"+("0"+dt_startt.getMinutes()).slice(-2)+":"+("0"+dt_startt.getSeconds()).slice(-2);

									                       itemss["end"] = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+dt_end.getHours()).slice(-2)+":"+("0"+dt_end.getMinutes()).slice(-2)+":"+("0"+dt_end.getSeconds()).slice(-2);

									                       var tempRecExp = libRecExp(item, itemss);

									                       itemss["color"] = "<?php echo $color;?>";
									                       itemss["purpose"] = "<?php echo $purpose;?>";
									                       itemss["listing_name"] = "<?php echo $listing_name;?>";

									                        var cr_date = new Date();
                                                            cr_date.setDate(cr_date.getDate() - 15);

                                                            var item_start_date = new Date(itemss["start"]);

                                                            if(item_start_date >= cr_date){


										                        if (`<?php echo $item->recurrenceException;?>` != "") {
	                                                                var isDel = isDeleted(`<?php echo $item->recurrenceException;?>`, tempRecExp);
																	if (!isDel) {
																		waiting_bookingss.push(itemss);
																	}
											                    }else{
	                                                               waiting_bookingss.push(itemss);
											                    }
											                }
									                       

									                    });

									                }
                                                <?php
			                                    $recurrenceRules = explode(';', $recurrenceRule);
			                                    
                                                     //   $approved[] = array_unique($approved);
		                                	}

                                    } elseif ($item->status == 'confirmed' || $item->status == 'paid') {
                                    	$color = "#DA697A";

                                    	

                                    	
                                    	if($_booking_system_equipment != ""){

                                    	  $adults_data = json_decode($item->comment);

											// convert the strings to unix timestamps
											$a = strtotime($stHour.":00");
											$b = strtotime($enHour.":00");

											if($b > $a){

												// loop over every hour (3600sec) between the two timestamps
												for($i = 0; $i < $b - $a; $i += 3600) {
												  // add the current iteration and echo it
												  $hour =  date('H', $a + $i);
												  $count_equipment[$start][$hour]["end_date"]  = $end;
												  $count_equipment[$start][$hour]["adults"]  += $adults_data->adults;
												  $count_equipment[$start][$hour]["_max_guests"]  = $_max_guests;

												}
												$count_equipment[$start][$enHour]["end_date"]  = $end;
												$count_equipment[$start][$enHour]["adults"]  += $adults_data->adults;
												$count_equipment[$start][$enHour]["_max_guests"]  = $_max_guests;

											}
											
                                         // $count_equipment[] = "{$start}|{$end}|{$stHour}|{$enHour}|{$adults_data->adults}|{$_max_guests}";
                                        }else{
                                            ?>
                                           // mkl = new Date("<?php echo $item->date_start; ?>");
                                            //mend = new Date("<?php echo $item->date_end; ?>");
                                            
                                       /*     mstt = moment(new Date("<?php echo $item->date_start; ?>")).format(f5);
                                            mend = moment(new Date("<?php echo $item->date_end; ?>")).format(f5);
                                            
                                            ks["start"] = mstt;
                                            ks["end"] = mend;
                                            already_booked.push(ks); */
                                            <?php
                                            
                                            $pd = array(
                                                "start" => $item->date_start,
                                                "end" => $item->date_end,
                                                'color' => $color,
                                                'purpose' => $item->purpose,
                                                'listing_name' => $item->listing_name,

                                            );
                                            $cr_date = date("Y-m-d H:i:s");

                                            $cr_date = date('Y-m-d H:i:s', strtotime('15 days', strtotime($cr_date)));

                                            $approved[] = $pd;
                                            $purpose =  $item->purpose;
                                            $listing_name =  $item->listing_name;
                                           //$approved[] = "{$start}|{$end}|{$stHour}|{$enHour}";
						
						/*Code by karimmughal1 for $recurrenceRule*/
		                                    $recurrenceRule = $item->recurrenceRule;
		                                    if($recurrenceRule!='' && $recurrenceRule !=null){

		                                    		$recurrenceRule = explode(';', $recurrenceRule);

		                                    		$rulesss = array();

												    foreach ($recurrenceRule as $key => $rule) {
												        if($rule != ""){
												          $rule = explode("=", $rule);
												          if($rule[1] != ""){
                                                            $rulesss[$rule[0]] = $rule[1];
														  }
												        }
												    }

												    if(isset($rulesss["UNTIL"]) && $rulesss["UNTIL"] != ""){
												      $rulesss["UNTIL"] = date("Ymd\T", strtotime($rulesss["UNTIL"]))."235959";
												    }

												    $final_rules = array();

												    foreach ($rulesss as $key_rulee => $ruleeee) {
												        $final_rules[] = $key_rulee."=".$ruleeee;
												    }


												    $recurrenceRule = implode(";",$final_rules);

												    


		                                    		

		                                    	
                                                     
                                                ?>

												var recBooking = null;
                                               
											    try {
													recBooking = new rrule.RRule.fromString("<?php echo $recurrenceRule;?>");
                                                   
												console.log(recBooking);
												} catch (error) {
													//recBooking = null;
												}
												if(recBooking){
                                                    if(recBooking.options.until == null){
									                    var dddd = new Date(recBooking.options.dtstart);
									                    dddd.setDate(dddd.getDate() + 100);
									                    recBooking.options.until = dddd;
									                }

									                var dt_startt = new Date("<?php echo $item->date_start;?>");
									                dt_startt.setDate(dt_startt.getDate());
									                var dt_end = new Date("<?php echo $item->date_end;?>");
									                recBooking.options.dtstart = dt_startt;

									                function isJsonString(str) {
													    try {
													        JSON.parse(str);
													    } catch (e) {
													        return false;
													    }
													    return true;
													}

									                function isDeleted(orignalItem, booking) {
									                	var bookingDate = moment.utc(booking).format("YYYY-MM-DD");

									                	var rec_datess = [];

									                	if(isJsonString(orignalItem)){
                                                            
                                                            rec_datess = JSON.parse(orignalItem);

									                	}else{

									                		var allRecArr = orignalItem.split(",");

									                		

									                		allRecArr.forEach(function(rec_dd){

									                			rec_datess.push(moment.utc(rec_dd).format("YYYY-MM-DD"));
               
									                		})

									                	}

									                	if (rec_datess.find(item => item == bookingDate)) {
									                        return true;
									                    } else {
									                        return false;
									                    }
									                    
									                }
									                function libRecExp(currentItem, eventItem) {

									                    var eventTime = new Date(eventItem.start);//object
									                    var eventHours = eventTime.getHours();
									                    var eventMin = eventTime.getMinutes();
									                    currentItem.setHours(eventHours, eventMin, 0, 0);
									                    return currentItem.toISOString();


									                }

									                recBooking = recBooking.all();






									               

									                if (recBooking.length > 0) {
									                    recBooking.forEach(function (item) { //Bookings List

									                        var month = item.getMonth() + 1;
															var dateee = item.getDate();

															if(month < 10){
													           month = "0"+month;
															}
															if(dateee < 10){
													           dateee = "0"+dateee;
															}

									                       var itemss = {};
									                       itemss["start"] = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+dt_startt.getHours()).slice(-2)+":"+("0"+dt_startt.getMinutes()).slice(-2)+":"+("0"+dt_startt.getSeconds()).slice(-2);

									                       itemss["end"] = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+dt_end.getHours()).slice(-2)+":"+("0"+dt_end.getMinutes()).slice(-2)+":"+("0"+dt_end.getSeconds()).slice(-2);

									                       var tempRecExp = libRecExp(item, itemss);

									                       itemss["color"] = "<?php echo $color;?>";
									                       itemss["purpose"] = "<?php echo $purpose;?>";
									                       itemss["listing_name"] = "<?php echo $listing_name;?>";

									                        var cr_date = new Date();
                                                            cr_date.setDate(cr_date.getDate() - 15);

                                                            var item_start_date = new Date(itemss["start"]);

                                                            if(item_start_date >= cr_date){


									                      
										                        if (`<?php echo $item->recurrenceException;?>` != "") {
	                                                                var isDel = isDeleted(`<?php echo $item->recurrenceException;?>`, tempRecExp);
																	if (!isDel) {
																		approved_bookingss.push(itemss);
																	}
											                    }else{
	                                                               approved_bookingss.push(itemss);
											                    }
										                    }

									                    });

									                }
												}	
                                                <?php
				                                    
                                                     //   $approved[] = array_unique($approved);
		                                	}

		                                	/*Code by karimmughal1 for $recurrenceRule*/
                                        }
                                    } else {
                                        $rejected[] = "{$start}|{$end}|{$stHour}|{$enHour}";
                                    }
                                }
                            }
                            // echo "<pre>"; print_r($approved);die;

                            $count_equipment = array("0"=>$count_equipment);
                            
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
                            //echo print_r($approved, true);
                            $approved = array_map("unserialize", array_unique(array_map("serialize", $approved)));
                            $approved = array_values($approved);
                            //echo print_r($approved, true);
                            //die;
                            $unavailableLength = count($unavailable);
                            ?>
                            already_booked = <?php echo json_encode($approved); ?>;
                            console.log("approved",already_booked);
                            console.log("approved_bookingss",approved_bookingss);
                            already_booked = already_booked.concat(approved_bookingss);

                            
                            waiting_dt = <?php echo json_encode($waiting); ?>;
                            waiting_dt = waiting_dt.concat(waiting_bookingss);
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
                            let count_equipment = '<?php echo json_encode($count_equipment); ?>';
                            let rejectedLength = '<?php echo $rejectedLength; ?>';
                            let rejected = '<?php echo json_encode($rejected); ?>';
                            let unavailableLength = '<?php echo $unavailableLength; ?>';
                            let unavailable = '<?php echo json_encode($unavailable); ?>';

                            count_equipment = jQuery.parseJSON(count_equipment);
                            

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


                            waiting_dt.sort(function(a, b) {
							  var keyA = new Date(a.start),
							    keyB = new Date(b.start);
							  // Compare the 2 dates
							  if (keyA < keyB) return -1;
							  if (keyA > keyB) return 1;
							  return 0;
							});


							already_booked.sort(function(a, b) {
							  var keyA = new Date(a.start),
							    keyB = new Date(b.start);
							  // Compare the 2 dates
							  if (keyA < keyB) return -1;
							  if (keyA > keyB) return 1;
							  return 0;
							});

							// function getDataBeforeDays(array, days) {
							// 	const currentDate = new Date();
							// 	currentDate.setDate(currentDate.getDate() - days); // Subtracting days from the current date

							// 	const currentDateStr = currentDate.toISOString().slice(0, 10); // Get the date string in "YYYY-MM-DD" format

							// 	for (let i = 0; i < array.length; i++) {
							// 		var datee = array[i].date_start;
							// 		debugger;
							// 		if ( array[i].date_start > currentDateStr) {
							// 			return array.slice(0, i); // Return data before the index of the current date
							// 			debugger;
							// 			break;
							// 		}
							// 	}

							// 	return []; // Return an empty array if current date is not found in the array
							// }

							// const days = 20;
                            // const dataBefore20Days = getDataBeforeDays(already_booked, days);
                            
							// debugger;


                            localStorage.setItem("waiting_dt", JSON.stringify(waiting_dt));
                            localStorage.setItem("already_booked", JSON.stringify(already_booked));


                    	  //jQuery('.timer-loader').show();
                          //  jQuery('.timer-loader-new').show();
                            jQuery('.tabela').css('opacity', '0.1');
                            var cou = 0;
                            var firstinput;
                            var secondinput;
                            var v = jQuery('.btn1').val();
                            var ifSunday = new Date();

                            window.setTimeout(function() {
                                jQuery('#slot').val(v);
                                jQuery('.timer-loader').hide(); 
                               // jQuery('.timer-loader-new').hide();
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
                            let tmarr = [];
                            let tft = '';
                            let tet = '';
                            <?php 
                           // echo print_r($slots, true);
                           // die;
                            $d_slots = array();
                            foreach ($slots as $day => $day_slots) {
                                $new_day = null;
                                if($day==0)
                                {
                                    $new_day = 1;
                                }
                                elseif($day==1)
                                {
                                    $new_day = 2;
                                }
                                elseif($day==2)
                                {
                                    $new_day = 3;
                                }
                                elseif($day==3)
                                {
                                    $new_day = 4;
                                }
                                elseif($day==4)
                                {
                                    $new_day = 5;
                                }
                                elseif($day==5)
                                {
                                    $new_day = 6;
                                }
                                elseif($day==6)
                                {
                                    $new_day = 0;
                                }
                                
                                $sl = $day_slots[0];
                                if(empty($sl))
                                {
                                    $d_slots[$new_day] = '';
                                }
                                else
                                {
                                    $slot = explode('|', $sl);
                                    $from_time_ft = explode(" - ", $slot[0]);
                                    $ds = array('start' => $from_time_ft[0], 'end' => $from_time_ft[1]);
                                    $d_slots[$new_day] = $ds;
                                }
                                /*
                                if (empty($day_slots)) continue;
                            ?>
                                <?php foreach ($day_slots as $number => $slot) {
                                    $slot = explode('|', $slot); ?>
                                    day = "<?php echo $day; ?>";
                                    dayArray.push(day);
                                    time = "<?php echo $slot[0]; ?>";
                                    <?php
                                        $from_time_ft = explode(" - ", $slot[0]);
                                        $ds = array('start' => $from_time_ft[0], 'end' => $from_time_ft[1]);
                                        $d_slots[$day] = $ds;
                                    ?>
                                    tft =  "<?php echo $from_time_ft[0]; ?>";
                                  //  console.log(tft);
                                    tet = "<?php echo $from_time_ft[1]; ?>";
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
                                    timeFromAr.push(tft);
                                    if (limit == 0) {
                                        limit = 23;
                                    }
                                    timeToAr.push(tet);
									

                                <?php } ?>
                            <?php */ } 
                           //  echo print_r($d_slots, true);
                            // die;
                            ?>
                            tmarr = <?php echo json_encode($d_slots); ?>;
                            console.log(tmarr);
                            var time_zone = '<?php echo wp_timezone_string(); ?>';
                            /*console.log(timeToAr);*/
                            const booked = [];
							jQuery(document).ready(function($){
                               
								var curr = new Date; // get current date
								let indexesAr = [];
								
								let mindate = '';
                                
								for(po=0;po<3;po++)
								{
									var firstday = '';
									let xxx = new Date();
									firstday = new Date(xxx.setDate(xxx.getDate() + po));
									let kd = firstday.getDay();
									let from_tm = timeFromAr[kd];
									let to_tm = timeToAr[kd];
									const f2 = "YYYY-MM-DD";
									let lmt = moment(firstday).format(f2);
									//indexesAr.push(firstday);
                                    let bs = [];
                                   /* bs["start"] = lmt+'T'+from_tm+':00';
                                    bs["end"] = lmt+'T'+to_tm+':00'; */
                                    bs["start"] = lmt+'T00:00:00';
                                    bs["end"] = lmt+'T'+from_tm+':00';

                                    bs["start_new"] = lmt+'T'+to_tm+':00';
                                    bs["end_new"] = lmt+'T24:00:00';
                                    booked.push(bs);
                                    /*
									calendar.addEvent({
											groupId: 'available_events',
											start: lmt+'T'+from_tm+'00:00',
											end: lmt+'T'+to_tm+'00:00',
											display: 'inverse-background'
										});
                                        */
								}
                                let allowed_time = [];
                               
							});
							</script>

                    <?php //echo "<pre>".print_r($results, true); die; ?>
                    <?php } else if ($post_meta['_listing_type'][0] == 'service') {
                           
                     ?>
                       
	                     <style type="text/css">
							#widget_booking_listings-2{
								display: none !important;
							}
						</style>
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
					$additional_service_label_name = get_post_meta($post_info->ID, 'additional_service_label_name', true);

                    if (!empty($bookable_services)) : ?>

                        <!-- Panel Dropdown -->
                        <div class="col-lg-12 cstm_blk bkk_service hide_bk">
                            <div class="panel-dropdown booking-services  <?php if (!is_user_logged_in()) {echo 'xoo-el-login-tgr';} ?>">
                                <a href="#">
									<?php 
										if($additional_service_label_name != ""){
											echo $additional_service_label_name;
										}else{
											esc_html_e('Extra Services', 'listeo_core');
										}
									?> 
									<span class="services-counter">0</span>
								</a>
                                <div class="panel-dropdown-content padding-reset">
                                    <div class="panel-dropdown-scrollable">

                                        <!-- Bookable Services -->
                                        <div class="bookable-services">
                                            <?php
                                            $i = 0;
                                            $currency_abbr = get_option('listeo_currency');
                                            $currency_postion = get_option('listeo_currency_postion');
                                            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
											if(isset($post_info->post_author) && $post_info->post_author != ""){
												$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
												if($user_currency_data != ""){
													$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
												}
											}
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
					$_max_amount_guests = get_post_meta($post_info->ID,"_max_amount_guests",true); 
					$_min_amount_guests = get_post_meta($post_info->ID,"_min_amount_guests",true);
                    $max_guests = get_post_meta($post_info->ID, "_max_guests", true);
                    $count_per_guest = get_post_meta($post_info->ID, "_count_per_guest", true);
                    if (get_option('listeo_remove_guests')) {
                        $max_guests = 1;
                    }
                    $_show_hide_amount = get_post_meta($post_info->ID, "_show_hide_amount", true);

                    
                    ?>
                    <!-- Panel Dropdown -->

                    <div class="col-lg-12 cstm_blk bkk_service hide_bk" <?php if ($max_guests == 1  || $_show_hide_amount == "on") {
                                                echo 'style="display:none;"';
                                            } ?>>
                        <div class="panel-dropdown <?php if (!is_user_logged_in()) {echo 'xoo-el-login-tgr';} ?>">
                        	<?php 
                        	$_booking_system_equipment = get_post_meta( $post_info->ID, '_booking_system_equipment', true );

                        	?>
                            <a href="#" ><?php if($_booking_system_equipment != ""){ esc_html_e('Amount', 'listeo_core'); }else{ esc_html_e('Antall', 'listeo_core');} ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
                            <div class="panel-dropdown-content" style="width: 269px;">
                                <!-- Quantity Buttons -->
                                <div class="qtyButtons sdsdd">
                                    <div class="qtyTitle"><?php esc_html_e('Antall', 'listeo_core') ?></div>
                                    <input type="text" name="qtyInput" data-guest_max="<?php echo esc_attr($_max_amount_guests); ?>" data-guest_min="<?php echo esc_attr($_min_amount_guests); ?>" class="adults <?php if ($count_per_guest) echo 'count_per_guest'; ?>" value="1">
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
                    <div class="col-lg-12 bkk_service hide_bk">
                        <div class="panel-dropdown">
                            <a href="#"><?php esc_html_e('Tickets', 'listeo_core') ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
                            <div class="panel-dropdown-content" style="width: 269px;">
                                <!-- Quantity Buttons -->
                                <div class="qtyButtons 333">
                                    <div class="qtyTitle"><?php esc_html_e('Tickets', 'listeo_core') ?></div>
                                    <input type="text" name="qtyInput" <?php if ($max_tickets > 0) { ?>data-max="<?php echo esc_attr($av_tickets); ?>" <?php } ?> id="tickets" value="1">
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php $bookable_services = listeo_get_bookable_services($post_info->ID);
					$additional_service_label_name = get_post_meta($post_info->ID, 'additional_service_label_name', true);

                    if (!empty($bookable_services)) : ?>

                        <!-- Panel Dropdown -->
                        <div class="col-lg-12 bkk_service hide_bk">
                            <div class="panel-dropdown booking-services   <?php if (!is_user_logged_in()) {echo 'xoo-el-login-tgr';} ?>">
                                <a href="#">
								    <?php 
										if($additional_service_label_name != ""){
											echo $additional_service_label_name;
										}else{
											esc_html_e('Extra Services', 'listeo_core');
										}
									?> 
								    <span class="services-counter">0</span>
								</a>
                                <div class="panel-dropdown-content padding-reset">
                                    <div class="panel-dropdown-scrollable">

                                        <!-- Bookable Services -->
                                        <div class="bookable-services">
                                            <?php
                                            $i = 0;
                                            $currency_abbr = get_option('listeo_currency');
                                            $currency_postion = get_option('listeo_currency_postion');
                                            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
											if(isset($post_info->post_author) && $post_info->post_author != ""){
												$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
												if($user_currency_data != ""){
													$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
												}
											}
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
                <?php if(!get_option('listeo_remove_coupons')): ?>
                    <?php //if($_booking_system_weekly_view == ""){ ?>
					<div class="col-lg-12 coupon-widget-wrapper bkk_service hide_bk">
					<a id="listeo-coupon-link" href="#"><?php esc_html_e('Har du en kupong eller et gavekort?','listeo_core'); ?></a>
						<div class="coupon-form">
								 
								<input type="text" name="apply_new_coupon" class="input-text" id="apply_new_coupon" value="" placeholder="<?php esc_html_e('Tast inn koden din her','listeo_core'); ?>"> 
								<a href="#" class="button listeo-booking-widget-apply_new_coupon"><div class="loadingspinner"></div><span class="apply-coupon-text"><?php esc_html_e('Apply','listeo_core'); ?></span></a>

						</div>
						<?php if(class_exists("Class_Gibbs_Giftcard")){

							$Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;
							$check_giftcard_page_id = $Class_Gibbs_Giftcard->get_page_id_by_shortcode("check_giftcard");
						?>	

							<?php if($check_giftcard_page_id){ ?>
								<a href="<?php echo get_permalink($check_giftcard_page_id);?>" class="check-saldo" target="_blank"><!-- Check giftcard saldo? --></a>
							<?php } ?>

						<?php } ?>
						<div id="coupon-widget-wrapper-output">
							<div  class="notification error closeable" ></div>
							<div  class="notification success closeable" id="coupon_added"><?php esc_html_e('Suksess','listeo_core'); ?></div>
						</div>
						<div id="coupon-widget-wrapper-applied-coupons">
							
						</div>
					</div>

					<input type="hidden" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_html_e('Coupon code','listeo_core'); ?>"> 
					<?php //} ?>
				<?php endif; ?>
     
		        <!-- Book Now -->
		        <input type="hidden" id="listing_type" value="<?php echo $post_meta['_listing_type'][0]; ?>" />
		        <input type="hidden" id="listing_id" value="<?php echo $post_info->ID; ?>" />
		        <input id="booking" type="hidden" name="value" value="booking_form" />
		        <?php if (get_post_meta($post_info->ID, '_discount', true) == 'on') { 
		            $discountss = get_post_meta($post_info->ID,"_discounts_user",true); 

		            if($discountss && is_array($discountss) && count($discountss) > 0){
		       	?>
		            <div class="col-lg-12 discount-dropdown bkk_service hide_bk">
		                <div class="panel-dropdown booking-services booking-discount-drop <?php if (!is_user_logged_in()) {echo ' xoo-el-login-tgr';} ?>">
		                    <a href="#">Målgruppe <span class="services-counter-discount" style="display:none"></span></a>
		                    <div class="panel-dropdown-content padding-reset" >
		                        <div class="panel-dropdown-scrollable">
		                            <div class="bookable-services" data-class="booking-discount-drop">
		                                <?php
		                               // $users = ['Barn', 'Funksjonshemmede', 'Senior', 'Idrettslag', 'Ungdom', 'Medlem', 'Lag og foreninger', 'Trening (for organiserte)', 'Kamp (for organiserte)', 'Private', 'Bedrifter', 'Ansatte'];
		                                
		                                foreach ($discountss as $user) {

		                                	$data_id = str_replace(" ", "", $user['discount_name']);


		                                     ?>
		                                    <input class="discount-input" style="float:left;" type="radio" name="discount" data-id="<?php echo $data_id; ?>" value="<?php echo $user['discount_name']; ?>"><label style="padding: 0px 0px 0px 15px; position: relative; bottom: 5px; font-size: 17px; overflow: hidden;" for="<?php echo $data_id;?>"><?php echo $user['discount_name'];?></label></input>
		                                <?php  }

		                                ?>
		                                <input class="discount-input" style="float:left;" type="radio" name="discount" data-id="none" value="none"><label style="padding: 0px 0px 0px 15px; position: relative; bottom: 5px; font-size: 17px; overflow: hidden;" for="none">None</label></input>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
			    <?php }
			    } ?>


		        <?php 

		        $book_with_login = get_post_meta($post_info->ID,"book_with_login",true);
				$verify_listing = get_post_meta($post_info->ID,"_verify_listing",true); 

				if($book_with_login == "on"){
			        $book_with_login = "";
			    }elseif($verify_listing == "on" && !is_user_logged_in()){
			        $book_with_login = "";
			    }else{
			        $book_with_login = "true";
			    }

				
			    
		        if($book_with_login){ ?>
		        	<script type="text/javascript">
		        		jQuery(document).ready(function(){
		        			jQuery("body").removeClass("user_not_logged_in")
							jQuery("body").find(".user_not_logged_in").removeClass("user_not_logged_in")
							jQuery("body").find(".xoo-el-login-tgr").removeClass("xoo-el-login-tgr")
		        		})
		        	</script>

		        <?php }

				if($verify_listing == "on" && is_user_logged_in()){ 

					$_verified_user = get_user_meta(get_current_user_id(),"_verified_user",true); 

					if($_verified_user == "on") {
					}else{
					?>

					<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery("body").addClass("book_with_verify")
						})
					</script>

				<?php }
				}

				if(is_user_logged_in() || $book_with_login ) :

		            if ($post_meta['_listing_type'][0] == 'event') {
		                $book_btn = esc_html__('Make a Reservation', 'listeo_core');
		            } else {
		                if (get_post_meta($post_info->ID, '_instant_booking', true)) {
		                    $book_btn = esc_html__('Book Now', 'listeo_core');
		                } else {
		                    $book_btn = esc_html__('Request Booking', 'listeo_core');
		                }
		            } 
		            if (get_post_meta($post_info->ID, '_instant_booking', true)) {
		                $book_btn_text = get_option("instant_booking_label");
		            } else {
		                $book_btn_text = get_option("non_instant_booking_label");
		            }
		            if($book_btn_text != ""){
		            	$book_btn = $book_btn_text;
		            }

		            

		             ?>
		            <div class="col-lg-12 conflict_div">

		            </div> 

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
		        <?php
				$_hide_price_div = get_post_meta($post_info->ID,'_hide_price_div',true);

                if($_hide_price_div != "on"){ ?>

		      <!--      <p style="text-align:center;" class="show_charged">Du blir ikke belastet ennå</p> -->
		        <?php } ?>  

		        <?php if ($post_meta['_listing_type'][0] == 'event' && isset($post_meta['_event_date'][0])) { ?>
		            <div class="booking-event-date">
		                <strong>Event date: </strong>
		                <span><?php

		                        $_event_datetime = $post_meta['_event_date'][0];
		                        $_event_date = list($_event_datetime) = explode(' -', $_event_datetime);

		                        echo $_event_date[0]; ?></span>
		            </div>
		        <?php } ?>
		        <div class="divvv" <?php if($_hide_price_div == "on"){ ?> style="display: none" <?php } ?>>
			        <div class="av_days price_div" style="display:none;">
			            <strong><?php esc_html_e('Totalt antall dager', 'listeo_core'); ?></strong>
			            <span></span>
			            <input type="hidden" name="av_days">
			            <input type="hidden" name="endrecdate">
			            <input type="hidden" name="rec">
			        </div>

			        <div class="booking-post-price price_div" style="display:none;">
			            <?php
			            $currency_abbr = get_option('listeo_currency');
			            $currency_postion = get_option('listeo_currency_postion');
			            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
						if(isset($post_info->post_author) && $post_info->post_author != ""){
							$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
							if($user_currency_data != ""){
								$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
							}
						}
			            ?>
			            <strong><?php esc_html_e('Opprinnelig Valgt tid (ink. mva)', 'listeo_core'); ?></strong>
			            <span>
			                <?php if ($currency_postion == 'before') {
			                    echo $currency_symbol;
			                } ?>
			                <?php if ($currency_postion == 'after') {
			                    echo $currency_symbol;
			                } ?>
			            </span>
			        </div>
			        <div class="booking-discount-price price_div" style="display:none;">
			            <?php
			            $currency_abbr = get_option('listeo_currency');
			            $currency_postion = get_option('listeo_currency_postion');
			            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
						if(isset($post_info->post_author) && $post_info->post_author != ""){
							$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
							if($user_currency_data != ""){
								$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
							}
						}
			            ?>
			            <strong><?php esc_html_e('Rabatt', 'listeo_core'); ?></strong>
			            <span>
			                <?php if ($currency_postion == 'before') {
			                    echo $currency_symbol;
			                } ?>
			                <?php if ($currency_postion == 'after') {
			                    echo $currency_symbol;
			                } ?>
			            </span>
			        </div>

			        <div class="booking-normal-price" style="display:none;">
			            <?php
			            $currency_abbr = get_option('listeo_currency');
			            $currency_postion = get_option('listeo_currency_postion');
			            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
						if(isset($post_info->post_author) && $post_info->post_author != ""){
							$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
							if($user_currency_data != ""){
								$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
							}
						}
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
						if(isset($post_info->post_author) && $post_info->post_author != ""){
							$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
							if($user_currency_data != ""){
								$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
							}
						}
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

			        <!-- <div class="booking-estimated-cost our" <?php //if ($post_meta['_listing_type'][0] != 'event') { ?>style="display: none;" <?php //} ?>>
			            <strong class="asd">Total mva</strong>
			            <div class="tax-span"></div>
			        </div> -->

			        <div class="booking-estimated-cost 1" <?php if ($post_meta['_listing_type'][0] != 'event') { ?>style="display: none;" <?php } ?>>
			            <?php
			            $currency_abbr = get_option('listeo_currency');
			            $currency_postion = get_option('listeo_currency_postion');
			            $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
						if(isset($post_info->post_author) && $post_info->post_author != ""){
							$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
							if($user_currency_data != ""){
								$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
							}
						}
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

			        	<div class="booking-estimated-discount-cost" style="display: none;">
					
						<strong><?php esc_html_e('Final Cost','listeo_core'); ?></strong>
						<span>
							<?php if($currency_postion == 'before') { echo $currency_symbol; } ?>
							
							<?php if($currency_postion == 'after') { echo $currency_symbol; } ?>
						</span>
					</div>

			        <div class="free-booking" <?php if ($post_meta['_listing_type'][0] != 'event') { ?>style="display: none;" <?php } ?>>
			            <strong><?php esc_html_e('Total Cost', 'listeo_core'); ?></strong>
			            <span>GRATIS</span>
			        </div>
		        </div>
		        <div class="booking-error-message" style="display: none;">
		            <?php esc_html_e('Unfortunately this request can\'t be processed. Try different dates please.', 'listeo_core'); ?>
		        </div>
		    </div>
        </form>
        <?php

        echo $after_widget;

        $content = ob_get_clean();

        echo $content;

        
       }else{
       	   $content = ob_get_clean();

       	   echo $this->otherbookings($args, $instance,$__booking_system_service,$_booking_system_rental);
       }

       $this->cache_widget($args, $content);
	}

	public function otherbookings($args, $instance,$__booking_system_service,$_booking_system_rental){




		
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

			$offer_type = get_post_meta( $post_id, '_listing_type', true );
		}
		
		if( in_array('option_booking',$packages_disabled_modules) ){ 
				
			if( isset($package) && $package->has_listing_booking() != 1 ){
				return;
			}
		}
		if ( $queried_object ) {
		     $post_id = $queried_object->ID;
			$_booking_status = get_post_meta($post_id, '_booking_status',true);{
				if(!$_booking_status) {
					return;
				}
			}
		}
		echo $before_widget;
		if ( $title ) {		
			echo $before_title.'<i class="fa fa-calendar-check"></i> ' . $title . $after_title; 
		} 
        $_min_book_days = get_post_meta($post_id,"_min_book_days",true);
		$_max_book_days = get_post_meta($post_id,"_max_book_days",true); ?>

        <script>
        	let _max_book_days = "<?php echo $_max_book_days;?>";
        	let _min_book_days = "<?php echo $_min_book_days;?>";
        </script>
        <?php 

		$days_list = array(
			0	=> __('Monday','listeo_core'),
			1 	=> __('Tuesday','listeo_core'),
			2	=> __('Wednesday','listeo_core'),
			3 	=> __('Thursday','listeo_core'),
			4 	=> __('Friday','listeo_core'),
			5 	=> __('Saturday','listeo_core'),
			6 	=> __('Sunday','listeo_core'),
		); 

		// get post meta and save slots to var
		$post_info = get_queried_object();

		$post_meta = get_post_meta( $post_info->ID );
		
		// get slots and check if not empty
		
		if ( isset( $post_meta['_slots_status'][0] ) && !empty( $post_meta['_slots_status'][0] ) ) {
			if ( isset( $post_meta['_slots'][0] ) ) {
				$slots = json_decode( $post_meta['_slots'][0] );
				if ( strpos( $post_meta['_slots'][0], '-' ) == false ) $slots = false;
			} else {
				$slots = false;	
			}
		} else {
			$slots = false;
		}

		$_booking_system_service = get_post_meta($post_info->ID,"_booking_system_service",true);
        $_booking_slots = get_post_meta($post_info->ID,"_booking_slots",true);

        if($_booking_system_service == "on" && !empty($_booking_slots)){
            $slots = $_booking_slots;
        }else{
        	$slots = false;
        }
		// get opening hours
		if ( isset( $post_meta['_opening_hours'][0] ))
		{
			$opening_hours = json_decode( $post_meta['_opening_hours'][0], true );
		}

		if ( $post_meta['_listing_type'][0] == 'rental' || $post_meta['_listing_type'][0] == 'service' ) {




			if($__booking_system_service == "" && $_booking_system_rental == ""){
				
				$after_widget .= '<style type="text/css">
					.gibbs_cal{
						display: none !important;
					}
				</style>';

				echo $after_widget; 

				$content = ob_get_clean();

				return $content;
			}
			/*if($_booking_system_rental){
				$post_meta['_listing_type'][0] = "rental";
			}*/


			// get reservations for next 10 years to make unable to set it in datapicker
			if( $post_meta['_listing_type'][0] == 'rental' ) {
				$records = $this->bookings->get_bookings( 
					date('Y-m-d H:i:s', strtotime('-10 day')), 
					date('Y-m-d H:i:s', strtotime('+3 years')), 
					array( 'listing_id' => $post_info->ID, 'type' => 'reservation', 'status' => 'approved'),
					$by = 'booking_date', $limit = '', $offset = '',$all = '',
					$listing_type = 'rental' 
				);
				
	
			} else {

				$records = $this->bookings->get_bookings( 
					date('Y-m-d H:i:s'),  
					date('Y-m-d H:i:s', strtotime('+3 years')), 
					array( 'listing_id' => $post_info->ID, 'type' => 'reservation', 'status' => 'approved' ),
					'booking_date',
					$limit = '', $offset = '','owner' );	
				
			}
			

			// store start and end dates to display it in the widget
			$wpk_start_dates = array();
			$wpk_end_dates = array();
			
			if(!empty($records)) {
				foreach ($records as $record)
				{

					if( $post_meta['_listing_type'][0] == 'rental' ) {
					// when we have one day reservation
						if ($record['date_start'] == $record['date_end'])
						{
							$wpk_start_dates[] = date('Y-m-d', strtotime($record['date_start']));
							$wpk_end_dates[] = date('Y-m-d', strtotime($record['date_start'] . ' + 1 day'));
						} else {
							/**
							 * Set the date_start and date_end dates and fill days in between as disabled
							 */
							$wpk_start_dates[] = date('Y-m-d', strtotime($record['date_start']));
							$wpk_end_dates[] = date('Y-m-d', strtotime($record['date_end']));

							$period = new DatePeriod(
								new DateTime( date( 'Y-m-d', strtotime( $record['date_start'] . ' + 1 day') ) ),
								new DateInterval( 'P1D' ),
								new DateTime( date( 'Y-m-d', strtotime( $record['date_end'] ) ) )//. ' +1 day') ) )
							);

							foreach ($period as $day_number => $value) {
								$disabled_dates[] = $value->format('Y-m-d');  
							}
							
							/*Code by karimmughal1 for $recurrenceRule*/
							$dateXstart = date('Y-m-d', strtotime($record['date_start']));
		                                    $recurrenceRule = $record['recurrenceRule'];
		                                    if($recurrenceRule!='' && $recurrenceRule!=null){
			                                    $recurrenceRules = explode(';', $recurrenceRule);

			                                    $rulesss = array();

											    foreach ($recurrenceRules as $key => $rule) {
											        if($rule != ""){
											          $rule = explode("=", $rule);
											          if($rule[1] != ""){
														$rulesss[$rule[0]] = $rule[1];
													  }
											        }
											    }

											    if(isset($rulesss["UNTIL"]) && $rulesss["UNTIL"] != ""){
											      $rulesss["UNTIL"] = date("Ymd\T", strtotime($rulesss["UNTIL"]))."235959";
											    }

											    $final_rules = array();

											    foreach ($rulesss as $key_rulee => $ruleeee) {
											        $final_rules[] = $key_rulee."=".$ruleeee;
											    }


											    $recurrenceRules = $final_rules; 

											    // echo "<pre>"; print_r($recurrenceRules); die;

			                                    $rulex = 0;
				                                foreach ($recurrenceRules as $Rule) {
				                                    	$Rules = explode('=', $Rule);
				                                    	if($Rules[0] == 'COUNT'){
				                                    		$RuleTotalDays = $Rules[1]*7;
				                                    		$rulex = 1;
					                                   	}elseif($Rules[0] == 'BYDAY'){
					                                    		$RuleWeekDays = explode(',', $Rules[1]);
					                                   	}elseif($Rules[0] == 'UNTIL'){
					                                   			$yearTotal = $Rules[1][0].$Rules[1][1].$Rules[1][2].$Rules[1][3];
					                                   			$monthTotal = $Rules[1][4].$Rules[1][5];
					                                   			$dayTotal = $Rules[1][6].$Rules[1][7];

					                                   			$datetime1 = date_create($dateXstart);
					                                   			$datetime2 = date_create($yearTotal.'-'.$monthTotal.'-'.$dayTotal);
					                                   			$interval = date_diff($datetime1, $datetime2);
					                                   			$RuleTotalDays = (int)$interval->format("%R%a");
					                                   			$rulex = 1;
					                                   	}
				                                }


				                           }

				                           

				                           if($rulex == 1){
			                                    $recurrenceRules = explode(';', $recurrenceRule);

			                                    foreach($RuleWeekDays as $key=>$RuleWeekDay){
			                                    	$day_formula[$RuleWeekDay];
			                                    }
			                                    $start_endx = null;
				                                for($i=1; $i<=$RuleTotalDays+1; $i++){
				                                    	$week_day = strtoupper(substr(date("l", strtotime(date("Y-m-d", strtotime($start)) . " +$i day")), 0, 2));
				                                    	if(in_array($week_day, $RuleWeekDays)){

				                                    	$start_endx[] = $start_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dateXstart)) . " +$i day"));
				                                    	}
				                                    }
				                              $runit = 0;
				                              $this_day = 0;
				                              $next_date = 0;
				                              $next_day = 0;
				                              foreach($start_endx as $key=>$startx){
				                              	
				                              	$this_day = $start_endx[$key];
				                              	$next_day = $start_endx[$key+1];

				                              	$prev_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($this_day)) . " -1 day"));
				                              	$next_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($this_day)) . " +1 day"));
				                              	
				                              	if($runit == 0){
				                              		$wpk_start_dates[] = $prev_date;
				                              		$runit = 1;
				                              	}

				                              	if($next_date == $next_day){
				                              		$disabled_dates[] = $this_day;
				                              	}else{
				                              		$wpk_end_dates[] = $this_day;
				                              		$runit = 0;
				                              	}

				                              }
				                             // echo "<pre>"; print_r($disabled_dates); die;


				                                    $disabled_dates = array_unique($disabled_dates);
		                                	}

		                                	/*Code by karimmughal1 for $recurrenceRule*/

						} 
					} else {
								// when we have one day reservation
						if ($record['date_start'] == $record['date_end'])
						{
							$disabled_dates[] = date('Y-m-d', strtotime($record['date_start']));
						} else {
							
							// if we have many dats reservations we have to add every date between this days
							$period = new DatePeriod(
								new DateTime( date( 'Y-m-d', strtotime( $record['date_start']) ) ),
								new DateInterval( 'P1D' ),
								new DateTime( date( 'Y-m-d', strtotime( $record['date_end'] . ' +1 day') ) )
							);

							foreach ($period as $day_number => $value) {
								$disabled_dates[] = $value->format('Y-m-d');  
							}

						}
					}

				}
			}
			
				if ( isset( $wpk_start_dates ) )
				{
					?>
					<script>
						var wpkStartDates = <?php echo json_encode( $wpk_start_dates ); ?>;
						var wpkEndDates = <?php echo json_encode( $wpk_end_dates ); ?>;
					</script>
					<?php
				}
				if ( isset( $disabled_dates ) )	
				{
					?>
					<script>
						var disabledDates = <?php echo json_encode($disabled_dates); ?>;
					</script>
					<?php
				}
		} // end if rental/service

		if(is_array($slots) && !empty($slots)){

			global $wpdb;
			

			$date_start_dt = date('Y-m-d',strtotime("-15 days"));
			$date_end_dt = date('Y-m-d',strtotime("+365 days"));

			$result_dd = Gibbs_Booking_Calendar::get_slots_bookings( $date_start_dt, $date_end_dt, array( 'listing_id' => $post_info->ID, 'type' => 'reservation' ),'booking_date','','','slot_booking' );

			//echo "<pre>"; print_r($result_dd); die;


			$booking_data = array();
            

            $kkk = 0;

            //echo "<pre>"; print_r($result_dd); die;

			$guest_slot = get_post_meta($post_info->ID,"_guest_slot",true);

			$isguest = 1;
			

			if($guest_slot == "no"){
				$isguest = 0;
			}

			
            

            foreach ($result_dd as $key => $result_d) {
				  if (strpos($result_d["date_start"], "1970") !== false) {
					continue;
				  }
				  if (strpos($result_d["date_end"], "1970") !== false) {
					  continue;
					}
				$count_slot = 0;	
				$dataaa = $wpdb->get_row("select * from bookings_calendar_meta where meta_key = 'number_of_guests' AND  booking_id = ".$result_d["id"]);

				//echo "<pre>"; print_r($isguest); die;

				if(isset($dataaa->meta_value) && $dataaa->meta_value != "" && $isguest){
					$count_slot += (int) $dataaa->meta_value;
				}else{
					$count_slot += 1;
				}	
            	$booking_data[$kkk]["date_start"] = $result_d["date_start"];
            	$booking_data[$kkk]["date_end"] = $result_d["date_end"];
            	$booking_data[$kkk]["count_slot"] = $count_slot;
				$kkk++;
            }
			//echo "<pre>"; print_r($booking_data); die;




			//$slots_dates = Gibbs_Booking_Calendar::getSlotDates($slots);

		    ?>
	        	     <script>
	                    var slots_booking = true;
	                    var slots_strings = <?php echo json_encode($slots);?>;
	                    var booking_data = <?php echo json_encode($booking_data);?>;
	                </script>

        <?php  }
		

		if ( $post_meta['_listing_type'][0] == 'event') { 
			$max_tickets = (int) get_post_meta($post_info->ID,"_event_tickets",true);
			$sold_tickets = (int) get_post_meta($post_info->ID,"_event_tickets_sold",true); 
			$av_tickets = $max_tickets-$sold_tickets; 
			
			if($av_tickets<=0){?>
				<p id="sold-out"><?php esc_html_e('The tickets have sold out','listeo_core') ?></p></div>
			<?php
			return; }
			
		}
		?>
		
		<div class="row with-forms  margin-top-0 slot-bookingg" 111 id="booking-widget-anchor" >	
	    	<?php
        	$extra_params = "";
        	if(isset($_GET['hide']) && $_GET['hide'] == true){
               $extra_params = "?hide=true";
        	}
        	?>	
			<form  autocomplete="off" id="form-booking" data-post_id="<?php echo $post_info->ID; ?>" class="form-booking-<?php echo $post_meta['_listing_type'][0];?>" action="<?php echo esc_url(get_permalink(get_option( 'listeo_booking_confirmation_page' ))).$extra_params; ?>" method="post">

					
					<?php if ( $post_meta['_listing_type'][0] != 'event') { 
							$minspan = get_post_meta($post_info->ID,'_min_days',true); 
							//WP Kraken
							// If minimub booking days are not set, set to 2 by default
							if ( ! $minspan && $post_meta['_listing_type'][0] == 'rental' ) {
								$minspan = 2;
							}
						?>
					<?php if ( $post_meta['_listing_type'][0] == 'service' &&   is_array( $slots )  ) {

						$Gibbs_Booking_Calendar = new Gibbs_Booking_Calendar;
						
						$slot_price_check = $Gibbs_Booking_Calendar->slotPriceType($slots,"slot_price");
						$all_slot_price_check = $Gibbs_Booking_Calendar->slotPriceType($slots,"all_slot_price");

						$dnonslt = "";

						if($slot_price_check && !$all_slot_price_check){
							$dnonslt = "style='display:none'";
						}
						if(!$slot_price_check && $all_slot_price_check){
							$dnonslt = "style='display:none'";
						}

						$slot_price_label = get_post_meta($post_info->ID,"slot_price_label",true);
						$all_slot_price_label = get_post_meta($post_info->ID,"all_slot_price_label",true);
						
						// echo "<pre>g"; print_r($slot_price_check); 
						// echo "<pre>h"; print_r($all_slot_price_check); die;
						
						?>
						<div class="slot_price_radio">
							<div class="inner_slot_price_radio" <?php echo $dnonslt;?>>
							    <?php if($slot_price_check){ 
									$checkedd1 = "";
									if((isset($_GET["slot_price_type"]) && $_GET["slot_price_type"] == "slot_price") || !isset($_GET["slot_price_type"])){
										$checkedd1 = "checked";
									}

									if(!$all_slot_price_check){ 
                                        $checkedd1 = "checked";
									}
									?>
									<input type="radio"
										id="slot_price"
										name="slot_price_type"
										value="slot_price" <?php echo $checkedd1; ?>>
									<label for="slot_price"><?php echo ($slot_price_label != "")?$slot_price_label:"Drop in";?></label>
								<?php } ?>
							</div>
							<div class="inner_slot_price_radio" <?php echo $dnonslt;?>>
							    <?php if($all_slot_price_check){ 
									
									
									$checkedd2 = "checked";
									if(isset($_GET["slot_price_type"]) && $_GET["slot_price_type"] == "all_slot_price"){ 
										$checkedd2 = "checked";
									}


									if(!$slot_price_check){ 
                                        $checkedd2 = "checked";
									}
									
									?>
									<input type="radio"
										id="all_slot_price"
										name="slot_price_type"
										value="all_slot_price" <?php echo $checkedd2;?>>
									<label for="all_slot_price"><?php echo ($all_slot_price_label != "")?$all_slot_price_label:"Privat";?></label>
								<?php } ?>
							</div>
						</div>
						<!-- <select name="slot_price_type" class="slot_price_type" required <?php echo $dnonslt;?>>
							<?php if($slot_price_check){ ?>
								<option value="slot_price" <?php if(isset($_GET["slot_price_type"]) && $_GET["slot_price_type"] == "slot_price"){ echo "selected";}?>>Drop in</option>
							<?php } ?>
							<?php if($all_slot_price_check){ ?>
								<option value="all_slot_price" <?php if(isset($_GET["slot_price_type"]) && $_GET["slot_price_type"] == "all_slot_price"){ echo "selected";}?>>Privat</option>
							<?php } ?>
						</select> -->
					<?php } ?>	
					<!-- Date Range Picker sdsdsdsd - docs: http://www.daterangepicker.com/ -->
					<div class="col-lg-12">
						<input 
						type="text" 
						data-minspan="<?php echo ($minspan) ? $minspan : '0' ; ?>"
						id="date-picker" 
						readonly="readonly" 
						class="date-picker-listing-<?php echo esc_attr($post_meta['_listing_type'][0]); ?>" 
						autocomplete="off" 
						placeholder="<?php esc_attr_e('Select day','listeo_core'); ?>" 
						value="" 
						data-listing_type="<?php echo $post_meta['_listing_type'][0]; ?>" 
						<?php if( is_array( $slots )){?> data-slot="true" <?php } ?>

						/>
					</div>

					<!-- Panel Dropdown -->
					<?php if ( $post_meta['_listing_type'][0] == 'service' &&   is_array( $slots )  ) { ?>
					<div class="col-lg-12 bkk_service hide_bk">
						<div class="panel-dropdown time-slots-dropdown">
							<a href="#" placeholder="<?php esc_html_e('Time Slots','listeo_core') ?>"><?php esc_html_e('Time Slots','listeo_core') ?></a>

							<div class="panel-dropdown-content padding-reset">
								<div class="no-slots-information"><?php esc_html_e('No slots for this day','listeo_core') ?></div>
								<div class="panel-dropdown-scrollable">
									<input id="slot" type="hidden" name="slot" value="" />
									<input id="listing_id" type="hidden" name="listing_id" value="<?php echo $post_info->ID; ?>" />
									<?php foreach( $slots as $day => $day_slots) { 
										if ( empty( $day_slots )) continue;
										?>

										<?php foreach( $day_slots as $number => $slot) { 
										$slot = explode('|' , $slot); ?>
										<!-- Time Slot -->
										<div class="time-slot" day="<?php echo $day; ?>">
											<input type="radio" name="time-slot" id="<?php echo $day.'|'.$number; ?>" value="<?php echo $day.'|'.$number; ?>">
											<label for="<?php echo $day.'|'.$number; ?>">
												<p class="day"><?php echo $days_list[$day]; ?></p>
												<strong><?php echo $slot[0]; ?></strong>
												<span><?php echo $slot[1]; esc_html_e(' slots available','listeo_core') ?></span>
											</label>
										</div>
										<?php } ?>	

									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php } else if ( $post_meta['_listing_type'][0] == 'service'  ) { ?>
					<div class="col-lg-12 bkk_service hide_bk">
						<input type="text" class="time-picker flatpickr-input active" placeholder="<?php esc_html_e('Time','listeo_core') ?>" id="_hour" name="_hour" readonly="readonly">
					</div>
					<?php if(get_post_meta($post_id,'_end_hour',true)) : ?>
						<div class="col-lg-12 bkk_service hide_bk">
							<input type="text" class="time-picker flatpickr-input active" placeholder="<?php esc_html_e('End Time','listeo_core') ?>" id="_hour_end" name="_hour_end" readonly="readonly">
						</div>
						<?php 
					endif;
					$_opening_hours_status = get_post_meta($post_id, '_opening_hours_status',true);
					$_opening_hours_status = '';
					?>
						<script>
							var availableDays = <?php if($_opening_hours_status){ echo json_encode( $opening_hours, true ); } else { echo json_encode( '', true); }?>;
						</script>
					
					<?php } ?>
					
					<?php $bookable_services = listeo_get_bookable_services($post_info->ID); 
					$additional_service_label_name = get_post_meta($post_info->ID, 'additional_service_label_name', true);

					if(!empty($bookable_services)) : ?>
						
						<!-- Panel Dropdown -->
						<div class="col-lg-12 bkk_service hide_bk">
							<div class="panel-dropdown booking-services">
							    <a href="#">
								    <?php 
										if($additional_service_label_name != ""){
											echo $additional_service_label_name;
										}else{
											esc_html_e('Extra Services', 'listeo_core');
										}
									?> 
								    <span class="services-counter">0</span>
								</a>
								<div class="panel-dropdown-content padding-reset">
									<div class="panel-dropdown-scrollable">
									
									<!-- Bookable Services -->
									<div class="bookable-services">
										<?php 
										$i = 0;
										$currency_abbr = get_option( 'listeo_currency' );
										$currency_postion = get_option( 'listeo_currency_postion' );
										$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr); 
										if(isset($post_info->post_author) && $post_info->post_author != ""){
											$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
											if($user_currency_data != ""){
												$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
											}
										}
										foreach ($bookable_services as $key => $service) { $i++; ?>
											<div class="single-service <?php if(isset($service['bookable_quantity'])) : ?>with-qty-btns<?php endif; ?>"> 

												<input type="checkbox" autocomplete="off" class="bookable-service-checkbox" name="_service[<?php echo sanitize_title($service['name']); ?>]" value="<?php echo sanitize_title($service['name']); ?>" id="tag<?php echo esc_attr($i); ?>"/>
												
												<label for="tag<?php echo esc_attr($i); ?>">
													<h5><?php echo esc_html($service['name']); ?></h5>
													<span class="single-service-price"> <?php 
													if(empty($service['price']) || $service['price'] == 0) {
														esc_html_e('Free','listeo_core');
													} else {
													 	if($currency_postion == 'before') { echo $currency_symbol.' '; } 
															$price = $service['price'];
															if(isset($service['tax']) && $service['tax'] > 0){
																$price += (($service['tax']/100) * $price);
															}
															if(is_numeric($price)){
																$decimals = get_option('listeo_number_decimals',2);
																echo number_format_i18n($price, $decimals);
															} else {
																echo esc_html($price); 	
															}
														if($currency_postion == 'after') { echo ' '.$currency_symbol; } 
													}
													?></span>
												</label>

												<?php if(isset($service['bookable_quantity'])) : ?>
												<div class="qtyButtons">
													<input type="text" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]"  value="1">
												</div>
												<?php else: ?>
													<input type="hidden" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]"  value="1">
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
					$_max_amount_guests = get_post_meta($post_info->ID,"_max_amount_guests",true); 
					$_min_amount_guests = get_post_meta($post_info->ID,"_min_amount_guests",true); 
					$max_guests = get_post_meta($post_info->ID,"_max_guests",true); 
					$count_per_guest = get_post_meta($post_info->ID,"_count_per_guest",true); 
					if(get_option('listeo_remove_guests')){
						$max_guests = 1;
					}
					 $_show_hide_amount = get_post_meta($post_info->ID, "_show_hide_amount", true);
					 //die("dfjdkfj")
					?>
					<!-- Panel Dropdown -->
					<div class="col-lg-12 bkk_service guests_drp  hide_bk <?php if($_show_hide_amount == "on"){ echo 'hide_guest'; } ?>" <?php if($max_guests == 1 || $_show_hide_amount == "on"){ echo 'style="display:none;"'; } ?>>
						<div class="panel-dropdown">
							<a href="#"><?php esc_html_e('Antall','listeo_core') ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
							<div class="panel-dropdown-content" style="width: 269px;">
								<!-- Quantity Buttons -->
								<div class="qtyButtons 66">
									<div class="qtyTitle"><?php esc_html_e('Antall','listeo_core') ?></div>
									<input type="text" name="qtyInput" data-guest_max="<?php echo esc_attr($_max_amount_guests); ?>" data-guest_min="<?php echo esc_attr($_min_amount_guests); ?>" class="adults <?php if($count_per_guest) echo 'count_per_guest'; ?>" value="1">
								</div>
								
							</div>
						</div>
					</div>
					<!-- Panel Dropdown / End -->

			<?php } //eof !if event ?>

			<?php if ( $post_meta['_listing_type'][0] == 'event') { 
				$max_guests 	= (int) get_post_meta($post_info->ID,"_max_guests",true); 
				$max_tickets 	= (int) get_post_meta($post_info->ID,"_event_tickets",true);
				$sold_tickets 	= (int) get_post_meta($post_info->ID,"_event_tickets_sold",true); 
				$av_tickets 	= $max_tickets-$sold_tickets;
				if($av_tickets > $max_guests){
					$av_tickets = $max_guests;
				} 

				?><input 
						type="hidden" 
						id="date-picker" 
						readonly="readonly" 
						class="date-picker-listing-<?php echo esc_attr($post_meta['_listing_type'][0]); ?>" 
						autocomplete="off" 
						placeholder="<?php esc_attr_e('Date','listeo_core'); ?>" 
						value="<?php echo $post_meta['_event_date'][0]; ?>" 
						listing_type="<?php echo $post_meta['_listing_type'][0]; ?>" />
					<div class="col-lg-12 tickets-panel-dropdown bkk_service hide_bk">
						<div class="panel-dropdown">
							<a href="#"><?php esc_html_e('Tickets','listeo_core') ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
							<div class="panel-dropdown-content" style="width: 269px;">
								<!-- Quantity Buttons -->
								<div class="qtyButtons">
									<div class="qtyTitle"><?php esc_html_e('Tickets','listeo_core') ?></div>
									<input type="text" name="qtyInput" <?php if($max_tickets>0){ ?>data-max="<?php echo esc_attr($av_tickets); ?>" <?php } ?>
									id="tickets" value="1">
								</div>
								
							</div>
						</div>
					</div>
					<?php $bookable_services = listeo_get_bookable_services($post_info->ID); 
					$additional_service_label_name = get_post_meta($post_info->ID, 'additional_service_label_name', true);

					if(!empty($bookable_services)) : ?>
						
						<!-- Panel Dropdown -->
						<div class="col-lg-12">
							<div class="panel-dropdown booking-services bkk_service hide_bk">
							    <a href="#">
								    <?php 
										if($additional_service_label_name != ""){
											echo $additional_service_label_name;
										}else{
											esc_html_e('Extra Services', 'listeo_core');
										}
									?> 
								    <span class="services-counter">0</span>
								</a>
								<div class="panel-dropdown-content padding-reset">
									<div class="panel-dropdown-scrollable">
									
									<!-- Bookable Services -->
									<div class="bookable-services">
										<?php 
										$i = 0;
										$currency_abbr = get_option( 'listeo_currency' );
										$currency_postion = get_option( 'listeo_currency_postion' );
										$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr); 
										if(isset($post_info->post_author) && $post_info->post_author != ""){
											$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
											if($user_currency_data != ""){
												$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
											}
										}
										foreach ($bookable_services as $key => $service) { $i++; ?>
											<div class="single-service">
												<input type="checkbox" class="bookable-service-checkbox" name="_service[<?php echo sanitize_title($service['name']); ?>]" value="<?php echo sanitize_title($service['name']); ?>" id="tag<?php echo esc_attr($i); ?>"/>
												
												<label for="tag<?php echo esc_attr($i); ?>">
													<h5><?php echo esc_html($service['name']); ?></h5>
													<span class="single-service-price"> <?php 
													if(empty($service['price']) || $service['price'] == 0) {
														esc_html_e('Free','listeo_core');
													} else {
													 	if($currency_postion == 'before') { echo $currency_symbol.' '; } 
														echo esc_html($service['price']); 
														if($currency_postion == 'after') { echo ' '.$currency_symbol; } 
													}
													?></span>
												</label>

												<?php if(isset($service['bookable_quantity'])) : ?>
												<div class="qtyButtons">
													<input type="text" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]" data-max="" class="" value="1">
												</div>
												<?php else: ?>
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
					
				<?php if(!get_option('listeo_remove_coupons')): ?>
					<div class="col-lg-12 coupon-widget-wrapper bkk_service hide_bk">
					<a id="listeo-coupon-link" href="#"><?php esc_html_e('Har du en kupong eller et gavekort?','listeo_core'); ?></a>
						<div class="coupon-form">
								 
								<input type="text" name="apply_new_coupon" class="input-text" id="apply_new_coupon" value="" placeholder="<?php esc_html_e('Tast inn koden din her','listeo_core'); ?>"> 
								<a href="#" class="button listeo-booking-widget-apply_new_coupon"><div class="loadingspinner"></div><span class="apply-coupon-text"><?php esc_html_e('Apply','listeo_core'); ?></span></a>

						</div>
						<?php if(class_exists("Class_Gibbs_Giftcard")){

							$Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;
							$check_giftcard_page_id = $Class_Gibbs_Giftcard->get_page_id_by_shortcode("check_giftcard");
						?>	

							<?php if($check_giftcard_page_id){ ?>
								<a href="<?php echo get_permalink($check_giftcard_page_id);?>" class="check-saldo" target="_blank"><!-- Check giftcard saldo? --></a>
							<?php } ?>

						<?php } ?>
						<div id="coupon-widget-wrapper-output">
							<div  class="notification error closeable" ></div>
							<div  class="notification success closeable" id="coupon_added"><?php esc_html_e('Suksess','listeo_core'); ?></div>
						</div>
						<div id="coupon-widget-wrapper-applied-coupons">
							
						</div>
					</div>

					<input type="hidden" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_html_e('Coupon code','listeo_core'); ?>"> 
				<?php endif; ?>
				
				
				<!-- Book Now -->
				<input type="hidden" id="listing_type" value="<?php echo $post_meta['_listing_type'][0]; ?>" />
				<input type="hidden" id="listing_id" value="<?php echo $post_info->ID; ?>" />
				<input id="booking" type="hidden" name="value" value="booking_form" />

				<?php if (($post_meta['_listing_type'][0] == 'rental' || $post_meta['_listing_type'][0] == 'service') &&  get_post_meta($post_info->ID, '_discount', true) == 'on') { 
		            $discountss = get_post_meta($post_info->ID,"_discounts_user",true); 

		            if($discountss && is_array($discountss) && count($discountss) > 0){
		       	?>
		            <div class="col-lg-12 discount-dropdown bkk_service hide_bk">
		                <div class="panel-dropdown  booking-discount-drop <?php if (!is_user_logged_in()) {echo ' xoo-el-login-tgr';} ?>">
		                    <a href="#">Målgruppe <span class="services-counter-discount" style="display:none"></span></a>
		                    <div class="panel-dropdown-content padding-reset" >
		                        <div class="panel-dropdown-scrollable">
		                            <div class="bookable-services1" data-class="booking-discount-drop">
		                                <?php
		                               // $users = ['Barn', 'Funksjonshemmede', 'Senior', 'Idrettslag', 'Ungdom', 'Medlem', 'Lag og foreninger', 'Trening (for organiserte)', 'Kamp (for organiserte)', 'Private', 'Bedrifter', 'Ansatte'];
		                                
		                                foreach ($discountss as $user) {

		                                	$data_id = str_replace(" ", "", $user['discount_name']);


		                                     ?>
		                                    <input class="discount-input" style="float:left;" type="radio" name="discount" data-id="<?php echo $data_id; ?>" value="<?php echo $user['discount_name']; ?>"><label style="padding: 0px 0px 0px 15px; position: relative; bottom: 5px; font-size: 17px; overflow: hidden;" for="<?php echo $data_id;?>"><?php echo $user['discount_name']; ?></label></input>
		                                <?php  }

		                                ?>
		                                <input class="discount-input" style="float:left;" type="radio" name="discount" data-id="none" value="none"><label style="padding: 0px 0px 0px 15px; position: relative; bottom: 5px; font-size: 17px; overflow: hidden;" for="none">None</label></input>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
			    <?php }
			    } ?>

				
				<?php 

				$book_with_login = get_post_meta($post_info->ID,"book_with_login",true);
			    $verify_listing = get_post_meta($post_info->ID,"_verify_listing",true); 

				if($book_with_login == "on"){
			        $book_with_login = "";
				}elseif($verify_listing == "on" && !is_user_logged_in()){
			        $book_with_login = "";
			    }else{
			        $book_with_login = "true";
			    }

				if($verify_listing == "on" && is_user_logged_in()){ 

					//echo get_current_user_id(); die("sdkshjdhskjd");

					$_verified_user = get_user_meta(get_current_user_id(),"_verified_user",true);

					//echo "<pre>"; print_r($_verified_user); die("jhjkh");



					if($_verified_user == "on") {
						
					}else{
					?>
				
					<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery("body").addClass("book_with_verify")
						})
					</script>

				<?php }
				}


				if(is_user_logged_in() || $book_with_login ) :
					
					if ($post_meta['_listing_type'][0] == 'event') { 
						$book_btn = esc_html__('Make a Reservation','listeo_core'); 
					} else { 
						if(get_post_meta($post_info->ID,'_instant_booking', true)){
							$book_btn = esc_html__('Book Now','listeo_core'); 	
						} else {
							$book_btn = esc_html__('Request Booking','listeo_core'); 	
						}
						
					}  

					if (get_post_meta($post_info->ID, '_instant_booking', true)) {
		                $book_btn_text = get_option("instant_booking_label");
		            } else {
		                $book_btn_text = get_option("non_instant_booking_label");
		            }
		            if($book_btn_text != ""){
		            	$book_btn = $book_btn_text;
		            }

					?>

					

					<div class="col-lg-12 bkk_service hide_bk">
						<a href="javascript:void(0)" class="button book-now fullwidth margin-top-5"><div class="loadingspinner"></div><span class="book-now-text"><?php echo $book_btn; ?></span>
						</a>
						<?php
						$_hide_price_div = get_post_meta($post_info->ID,'_hide_price_div',true);

	                    if($_hide_price_div != "on"){ ?>
	                		<!-- <p style="text-align: center;" class="show_charged">Du blir ikke belastet ennå</p> -->
	                	<?php } ?>	
					</div>




					
				<?php else : 
					$popup_login = get_option( 'listeo_popup_login','ajax' ); 
					if($popup_login == 'ajax') { ?>

						<a href="#sign-in-dialog" class="button fullwidth margin-top-5 popup-with-zoom-anim book-now-notloggedin"><div class="loadingspinner"></div><span class="book-now-text"><?php esc_html_e('Login to Book','listeo_core') ?></span></a>

					<?php } else { 

						$login_page = get_option('listeo_profile_page'); ?>
						<a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="button fullwidth margin-top-5 book-now-notloggedin"><div class="loadingspinner"></div><span class="book-now-text"><?php esc_html_e('Login To Book','listeo_core') ?></span></a> 
					<?php } ?>
					
				<?php endif; ?>
	
				<?php if ($post_meta['_listing_type'][0] == 'event' && isset($post_meta['_event_date'][0])) { ?>
				<div class="booking-event-date">
					<strong><?php esc_html_e( 'Event date', 'listeo_core' ); ?></strong>
					<span><?php 
					
					$_event_datetime = $post_meta['_event_date'][0];
               		$_event_date = list($_event_datetime) = explode(' -', $_event_datetime);
 					
					echo $_event_date[0]; ?></span>
				</div>
				<?php } ?>	
				
				<?php 
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr); 
					if(isset($post_info->post_author) && $post_info->post_author != ""){
						$user_currency_data = get_user_meta( $post_info->post_author, 'currency', true );
						if($user_currency_data != ""){
							$currency_symbol = get_woocommerce_currency_symbol($user_currency_data);
						}
					}
				?>
				<?php
					$_hide_price_div = get_post_meta($post_info->ID,'_hide_price_div',true);

                     ?>
				<div class="divvvv" <?php if($_hide_price_div == "on"){ ?> style="display: none" <?php } ?>>
					<div class="booking-normal-price" style="display: none;">
			            <strong class="asd">Valgt tid (ink. mva)</strong>
			            <span></span>
			        </div>
					<div class="booking-estimated-cost-tax our" style="display: none;">
			            <strong class="asd">Total mva</strong>
			            <div class="tax-span"></div>
			        </div>
					<div class="booking-estimated-cost 2" <?php if ($post_meta['_listing_type'][0] != 'event' ) { ?>style="display: none;"<?php } ?>>
						<?php if ($post_meta['_listing_type'][0] == 'event') {
								$reservation_fee = (float) get_post_meta($post_info->ID,'_reservation_price',true);
								$normal_price = (float) get_post_meta($post_info->ID,'_normal_price',true);
								
								$event_default_price = $reservation_fee+$normal_price;
							}  ?>
						<strong><?php esc_html_e('Total Cost','listeo_core'); ?></strong>
						<span data-price="<?php if(isset($event_default_price)) { echo esc_attr($event_default_price); } ?>">
							<?php if($currency_postion == 'before') { echo $currency_symbol; } ?>
							<?php 
							if ($post_meta['_listing_type'][0] == 'event') {
							
								echo $event_default_price;
							} else echo '0'; ?>
							<?php if($currency_postion == 'after') { echo $currency_symbol; } ?>
						</span>
					</div>

					<div class="booking-estimated-discount-cost" style="display: none;">
						
						<strong><?php esc_html_e('Final Cost','listeo_core'); ?></strong>
						<span>
							<?php if($currency_postion == 'before') { echo $currency_symbol; } ?>
							
							<?php if($currency_postion == 'after') { echo $currency_symbol; } ?>
						</span>
					</div>
				</div>	
				<div class="booking-error-message" style="display: none;">
					<?php if($post_meta['_listing_type'][0] == 'service' && !$slots) {
						esc_html_e('Unfortunately we are closed at selected hours. Try different please.','listeo_core'); 
					} else {
						esc_html_e('Unfortunately this request can\'t be processed. Try different dates please.','listeo_core'); 
					} ?>
				</div>
		</form>
		</div>
		<?php

		echo $after_widget; 

		$content = ob_get_clean();

		return $content;

	}

}

register_widget( 'Custom_Gibbs_Booking_Widget' );