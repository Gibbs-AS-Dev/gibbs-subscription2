<?php
// Exit if accessed directly
// https://github.com/jarkkolaine/personalize-login-tutorial-part-3
if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Listeo_Core_Listing class
 */
class Listeo_Core_Users {

	/**
	 * Dashboard message.
	 *
	 * @access private
	 * @var string
	 */
	private $dashboard_message = '';

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;
	/**
	 * Constructor
	 */
	public function __construct() {

		//$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		
		//add_action( 'user_contactmethods', array( $this, 'modify_contact_methods' ), 10 );
		add_action( 'init', array( $this, 'submit_my_account_form' ), 10 );
		add_action( 'init', array( $this, 'submit_change_password_form' ), 10 );
		//add_action( 'init',  array( $this, 'remove_filter_lostpassword' ), 10 );

		add_action( 'register_form', array( $this, 'listeo_register_form' ) );

		add_action( 'wp', array( $this, 'dashboard_action_handler' ) );
		add_filter( 'pre_get_posts',  array( $this,  'author_archive_listings' ));
 
		add_action( 'listeo_login_form', array( $this, 'show_login_form'));

		add_action( 'show_user_profile', array( $this, 'extra_profile_fields' ), 10 );
		add_action( 'edit_user_profile', array( $this, 'extra_profile_fields' ), 10 );
		add_action( 'personal_options_update', array( $this, 'save_extra_profile_fields' ));
		add_action( 'edit_user_profile_update', array( $this, 'save_extra_profile_fields' ));
		 
		add_filter( 'wpua_is_author_or_above', '__return_true' ); /*fix to apply for agents only*/

		add_shortcode( 'listeo_my_listings', array( $this, 'listeo_core_my_listings' ) );
		// add_shortcode( 'listeo_core_my_packages', array( $this, 'listeo_core_my_packages' ) );
		add_shortcode( 'listeo_my_account', array( $this, 'my_account' ) );
		add_shortcode( 'listeo_dashboard', array( $this, 'listeo_dashboard' ) );
		add_shortcode( 'listeo_change_password', array( $this, 'change_password' ) );
		add_shortcode( 'listeo_lost_password', array( $this, 'lost_password' ) );
		add_shortcode( 'listeo_reset_password', array( $this, 'reset_password' ) );
		

		add_shortcode( 'listeo_my_orders', array( $this, 'my_orders' ) );


		

		$front_login = get_option('listeo_front_end_login' );

		if($front_login == 'on') {

			add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_woocommerce' ) ,10, 2);
			add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
			add_filter( 'login_redirect', array( $this, 'redirect_after_login' ), 10, 3 );
			
			//add_action( 'login_form_lostpassword', array( $this, 'redirect_to_custom_lostpassword' ) );

			add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
			add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );

			add_action( 'login_form_register', array( $this, 'redirect_to_custom_register' ) );

			add_action( 'login_form_rp', array( $this, 'do_password_reset' ) );
			add_action( 'login_form_resetpass', array( $this, 'do_password_reset' ) );
			//add_action( 'login_form_lostpassword', array( $this, 'do_password_lost' ) );

		}
		
		add_action( 'login_form_register', array( $this, 'do_register_user' ) );
	
		$popup_login = get_option( 'listeo_popup_login','ajax' ); 
		
		
		add_filter( 'authenticate', array( $this, 'maybe_redirect_at_authenticate' ), 101, 3 );	
		
		
		add_filter('get_avatar', array( $this, 'listeo_core_gravatar_filter' ), 10, 6);
		//add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
		//add_filter( 'woocommerce_disable_admin_bar', '__return_false' );
		//
		

		// Ajax login
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		add_action( 'wp_ajax_nopriv_listeoajaxlogin', array( $this, 'ajax_login' ) );
		// Ajax registration
		add_action( 'wp_ajax_nopriv_listeoajaxregister', array( $this, 'ajax_register' ) );

		add_action( 'wp_ajax_nopriv_get_logged_header', array( $this, 'ajax_get_header_part' ) );
		add_action( 'wp_ajax_get_logged_header', array( $this, 'ajax_get_header_part' ) );
	
		add_action( 'wp_ajax_nopriv_get_booking_button', array( $this, 'ajax_get_booking_button' ) );
		add_action( 'wp_ajax_get_booking_button', array( $this, 'ajax_get_booking_button' ) );
	

		add_filter( 'registration_errors',array( $this,  'listeo_wp_admin_registration_errors'), 10, 3 );
		add_action( 'user_register', array( $this, 'listeo_wp_admin_user_register') );


		add_action('wp_ajax_listeo_get_custom_registration_fields', array($this, 'get_custom_registration_fields'));
		add_action('wp_ajax_nopriv_listeo_get_custom_registration_fields', array($this, 'get_custom_registration_fields'));

		//add_action( 'admin_init', array($this,'wpse66094_no_admin_access'), 100 );
	}


	function listeo_wp_admin_registration_errors( $errors, $sanitized_user_login, $user_email ) {
		$role_status  = get_option('listeo_registration_hide_role');
		
		if(!$role_status) {
		    if ( empty( $_POST['role'] ) || ! empty( $_POST['role'] ) && trim( $_POST['role'] ) == '' ) {
		         $errors->add( 'role_error', __( '<strong>ERROR</strong>: You must include a role.', 'listeo_core' ) );
		    }
		}

	    return $errors;
	}

	function get_custom_registration_fields(){
		$role = $_REQUEST['role'];
		
		$result['type'] = 'error';
		$result['output'] = '';
		if($role){
			$result['type'] = 'success';
			$result['output'] = listeo_get_extra_registration_fields($role);	
		}
		
		wp_send_json($result);
		die();
		
	}

	//3. Finally, save our extra registration user meta.
	
	function listeo_wp_admin_user_register( $user_id ) {

	   $user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $_POST['user_role'] ) );

	   //fix owners dropdown
	    //  $ownerusers = get_users( 'role=owner' );
        //     foreach ( $ownerusers as $user ) {
        //         $user->add_cap('level_1');
        //     }
	}

	function listeo_register_form() {
		$role_status  = get_option('listeo_registration_hide_role');
		
		if(!$role_status) {
		    global $wp_roles;
		    echo '<label for="role">'.esc_html__('I want to register as','listeo_core').'</label>';
		    echo '<select name="role" id="role" class="input chosen-select">';
		    
			    echo '<option value="guest">'.esc_html__("Guest","listeo_core").'</option>';
			    
			    echo '<option value="owner">'.esc_html__("Owner","listeo_core").'</option>';
			    
		    echo '</select>';
	    }
	}


	function wpse66094_no_admin_access() {
	    $redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : home_url( '/' );
	    global $current_user;
	    $user_roles = $current_user->roles;
	    $user_role = array_shift($user_roles);
	    
	    if( in_array($user_role,array('owner','guest')) ){
	        exit( wp_redirect( $redirect ) );
	    }
	 }

	function show_login_form(){
		
			$template_loader = new Listeo_Core_Template_Loader;
			$template_loader->get_template_part( 'account/login-form' ); 
	}
	function enqueue_scripts(){
		if (!is_user_logged_in()) {
			
			$popup_login = get_option( 'listeo_popup_login','ajax' ); 
			
			if($popup_login == 'ajax') {
				wp_register_script( 'listeo_core-ajax-login', esc_url( LISTEO_CORE_ASSETS_URL ) . '/js/ajax-login-script.js', array('jquery'), '1.0'  );
	  			wp_enqueue_script('listeo_core-ajax-login');
	  			wp_localize_script( 'listeo_core-ajax-login', 'listeo_login', array( 
			        'ajaxurl' => admin_url( 'admin-ajax.php' ),
			        'redirecturl' => home_url(),
			        'loadingmessage' => __('Logger inn...','listeo_core')
			    ));
	  		}
		}
	}

	function ajax_login(){

	    // First check the nonce, if it fails the function will break
	    //check_ajax_referer( 'ajax-login-nonce', 'security' );
/* 		if( !check_ajax_referer( 'listeo-ajax-login-nonce', 'login_security', false) ) :
            echo json_encode(
            	array(
            		'loggedin'=>false, 
            		'message'=> __('Session token has expired, please reload the page and try again', 'listeo_core')
            	)
            );
            die();
        endif; */

        // Nonce is checked, get the POST data and sign user on
	    $info = array();
	    $info['user_login'] = sanitize_text_field(trim($_POST['username']));
	    $info['user_password'] = sanitize_text_field(trim($_POST['password']));
	    $info['remember'] = isset($_POST['remember-me']) ? true : false;

	    if(empty($info['user_login'])) {
	    	 echo json_encode(
	    	 	array(
	    	 		'loggedin'=>false, 
	    	 		'message'=> esc_html__( 'Vennligst skriv inn din epost', 'listeo_core' )
	    	 	)
	    	 );
	    	 die();
	    }
	    if(empty($info['user_password'])) {
	    	 echo json_encode(array('loggedin'=>false, 'message'=> esc_html__( 'Vennligst skriv inn et passord', 'listeo_core' )));
	    	 die();
	    }



	  //  $user_signon = wp_signon( $info, is_ssl() );
	    $user_signon = wp_signon( $info, false );
	    if ( is_wp_error($user_signon) ){
	    	
	        echo json_encode(
	        	array(
	        		'loggedin'=>false, 
	        		'message'=>esc_html__('Feil epost/brukernavn eller passord','listeo_core')
	        	)
	        );

	    } else {
		    // $userdata = get_user_by('login', $info['user_login']);
		    $userMeta = get_user_meta ( $user_signon->ID, 'ptn_capabilities', true);
	    	// $result = wp_check_password($info['user_password'], $userdata->data->user_pass, $userdata->data->ID);
            // auto_login( $userdata );
	    	// $result = wp_check_password($info['user_password'], $userdata->data->user_pass, $userdata->data->ID);
	    	// wp_clear_auth_cookie();
      //       wp_set_current_user($user_signon->ID);
      //       // wp_set_auth_cookie($user_signon->ID, true);
      //      	do_action('wp_login', $user_signon->ID);
	    	// $user_id = $user_signon->ID;
    	// 	clean_user_cache($user_id);
		  	// wp_clear_auth_cookie();
		  	// wp_set_current_user($user_id);
		  	// // wp_set_auth_cookie($user_id, true, false);

		  	// $user = get_user_by('id', $user_id);
		  	// update_user_caches($user);
	        
	        echo json_encode(

	        	array(
	        		'loggedin'	=>	true, 
	        		'message'	=>	esc_html__('Vellykket. Logger inn :)','listeo_core'),
	        		'userData' => $user_signon,
	        		'role' => $userMeta,
	        	)

	        );
	    }



	    die();
	}

	function ajax_get_header_part(){
		
			ob_start();

			$template_loader = new Listeo_Core_Template_Loader;		
			$template_loader->get_template_part( 'account/logged_section' ); 

			$output = ob_get_clean();
			wp_send_json_success(
	        	array(
	        		'output' 	=> 	$output
	        	)
	        );
	        die();

	}
	function ajax_get_booking_button(){
			$post_id = $_POST['post_id'];
			$owner_widget_id = $_POST['owner_widget_id'];
			$freeplaces = $_POST['freeplaces'];
			
			$_listing_type = get_post_meta($post_id,"_listing_type",true); 
			ob_start(); ?>
			<a href="#" data-freeplaces="<?php echo esc_attr($freeplaces); ?>" class="button book-now fullwidth margin-top-5">
				<div class="loadingspinner"></div>
				<span class="book-now-text">
					<?php if ($_listing_type == 'event') { 
						esc_html_e('Make a Reservation','listeo_core'); 
					} else { 
						if(get_post_meta($post_id,'_instant_booking', true)){
							esc_html_e('Book Now','listeo_core'); 	
						} else {
							esc_html_e('Request Booking','listeo_core'); 	
						}
					}  ?>
				</span>			
			</a>
			<?php

			$booking_btn = ob_get_clean();

			ob_start(); 

				$nonce = wp_create_nonce("listeo_core_bookmark_this_nonce");
		
				$classObj = new Listeo_Core_Bookmarks;
				
				if( $classObj->check_if_added($post_id) ) { ?>
					<button onclick="window.location.href='<?php echo get_permalink( get_option( 'listeo_bookmarks_page' ))?>'" class="like-button save liked" ><span class="like-icon liked"></span> <?php esc_html_e('Bookmarked','listeo_core') ?></button> 
				<?php } else {  ?>
					
						<button class="like-button listeo_core-bookmark-it"
							data-post_id="<?php echo esc_attr($post_id); ?>" 
							data-confirm="<?php esc_html_e('Bookmarked!','listeo_core'); ?>"
							data-nonce="<?php echo esc_attr($nonce); ?>" 
							><span class="like-icon"></span> <?php esc_html_e('Bookmark this listing','listeo_core') ?></button> 
				<?php }

			$bookmark_btn = ob_get_clean();
			

			ob_start(); 

			if($owner_widget_id){

				$widget_id = explode('-',$owner_widget_id);
				$widget_instances = get_option('widget_widget_listing_owner');
				$widget_options = $widget_instances[$widget_id[1]];
				
				$show_email = (isset($widget_options['email']) && !empty($widget_options['email'])) ? true : false ;
				$show_phone = (isset($widget_options['phone']) && !empty($widget_options['phone'])) ? true : false ;

				$owner_id = get_post_field ('post_author', $post_id);
			
				if(!$owner_id) {
					return;
				}
				$owner_data = get_userdata( $owner_id );
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
			}
			$owner_data = ob_get_clean();




			wp_send_json_success(
	        	array(
	        		'booking_btn' 	=> 	$booking_btn,
	        		'bookmark_btn' 	=> $bookmark_btn,
	        		'owner_data' 	=> $owner_data
	        	)
	        );
	        die();

	}

	function ajax_register(){
		
		
		if ( !get_option('users_can_register') ) :
	            echo json_encode(
				array(
					'registered'=>false, 
					'message'=> esc_html__( 'Registration is disabled', 'listeo_core' ),
				)
			);
    		die();
	    endif;

/*   		if( !check_ajax_referer( 'listeo-ajax-login-nonce', 'register_security', false) ) :
            echo json_encode(
            	array(
            		'registered'=>false, 
            		'message'=> __('Session token has expired, please reload the page and try again', 'listeo_core')
            	)
            );
            die();
        endif; */

        //get email
        $email = sanitize_email($_POST['email']);
		if ( !$email ) {
 			echo json_encode(
            	array(
            		'registered'=>false, 
            		'message'=> __('Please fill email address', 'listeo_core')
            	)
            );
            die();
        }		

        if ( !is_email($email)  ) {
 			echo json_encode(
            	array(
            		'registered'=>false, 
            		'message'=> __('This is not valid email address', 'listeo_core')
            	)
            );
            die();
        }

        $user_login = false;
        // get/create username

	    if ( get_option('listeo_registration_hide_username') ) {
  			$email_arr = explode('@', $email);
            $user_login = sanitize_user(trim($email), true);
        } else {
        	
 			$user_login = sanitize_user(trim($_POST['username']));
 			
        }

        if(empty($user_login)) {
			echo json_encode(
				array(
					'registered'=>false, 
					'message'=> esc_html__( 'Please provide your username', 'listeo_core' )
				)
			);
    		die();
    	}   	  	

	
        if (get_option('listeo_display_first_last_name') ) {

            $first_name = sanitize_text_field( $_POST['first_name'] );
            $last_name  = ! empty($_POST['last_name']) ? sanitize_text_field( $_POST['last_name'] ) : '';

        } else {

        	$first_name = '';
        	$last_name = '';

        }

           if ( get_option('listeo_display_password_field') ) :
	    	$password = sanitize_text_field(trim($_POST['password']));
			if(empty($password)) {
				echo json_encode(
					array(
						'registered'=>false, 
						'message'=> esc_html__( 'Please provide password', 'listeo_core' )
					)
				);
				die();
			} 
		endif; 

        if ( get_option('listeo_privacy_policy') ) :
	    	$privacy_policy = $_POST['privacy_policy'];
			if(empty($privacy_policy)) {
				echo json_encode(
					array(
						'registered'=>false, 
						'message'=> esc_html__( 'Please accept Privacy Policy', 'listeo_core' )
					)
				);
				die();
			} 
		endif; 	       
		if ( get_option('listeo_terms_and_conditions_req') ) :
	    	$terms_and_conditions = $_POST['terms_and_conditions'];
			if(empty($terms_and_conditions)) {
				echo json_encode(
					array(
						'registered'=>false, 
						'message'=> esc_html__( 'Please accept Terms and Conditions', 'listeo_core' )
					)
				);
				die();
			} 
		endif; 	
		if(isset($_POST['user_role'])){
			$role = sanitize_text_field( $_POST['user_role'] );	
		} else {
			$role = get_option('default_role');
		}

    	
    	if (!in_array($role, array('owner', 'guest'))) {
    		$role = get_option('default_role');
    	}

    	if($role=='owner') {
			$fields = get_option('listeo_owner_registration_form');
        } else {
        	$fields = get_option('listeo_guest_registration_form');
        }
        	
       	 $custom_registration_fields = array();
            if(!empty($fields)){
            	//get fields for registration

            	foreach ($fields as $key => $field) {

            		// array(16) { ["type"]=> string(8) "checkbox" ["name"]=> string(9) "_checkbox" ["placeholder"]=> string(8) "Checkbox" ["class"]=> string(0) "" ["css_class"]=> string(9) "col-md-12" ["default"]=> string(0) "" ["multi"]=> bool(false) ["priority"]=> int(1) ["icon"]=> string(5) "empty" ["place"]=> string(4) "main" ["taxonomy"]=> string(0) "" ["max"]=> string(0) "" ["min"]=> string(0) "" ["options_source"]=> string(0) "" ["options_cb"]=> string(0) "" ["options"]=> array(0) { } }
            		
            		$field_type = str_replace( '-', '_', $field['type'] );
		
					if ( $handler = apply_filters( "listeo_core_get_posted_{$field_type}_field", false ) ) {
						
						$value = call_user_func( $handler, $key, $field );
					} elseif ( method_exists( $this, "get_posted_{$field_type}_field" ) ) {
						
						$value = call_user_func( array( $this, "get_posted_{$field_type}_field" ), $key, $field );
					} else {
						
						$value = $this->get_posted_field( $key, $field );
					}
					
					// Set fields value

					


            		if(isset($field['required']) && !empty($field['required'])) {
            		
            			if(!$value){
            				$redirect_url = add_query_arg( 'register-errors', 'required-field', $redirect_url );
            				wp_redirect( $redirect_url );
    						exit;
            			} else {
            				$field['value'] = $value; 
            				$custom_registration_fields[] = $field;
            			}

            		} else {
            			
            			$field['value'] = $value;
            			
            			$custom_registration_fields[] = $field;
            		}

            	}
            }
		
		$recaptcha_status = get_option('listeo_recaptcha');
		$recaptcha_version = get_option('listeo_recaptcha_version');
	            
        if($recaptcha_status && $recaptcha_version=="v2") {
        	if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
		        //your site secret key
		        $secret = get_option('listeo_recaptcha_secretkey');
		        //get verify response data
		        //$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
		        $verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
				$responseData_w = wp_remote_retrieve_body( $verifyResponse );
		        $responseData = json_decode($responseData_w);
				if( $responseData->success ):
					//passed captcha, proceed to register
		            // 
	        	else:
	        		echo json_encode(
						array(
							'registered'=>false, 
							'message'=> esc_html__( 'Wrong reCAPTCHA', 'listeo_core' )
						)
					);die();
        		endif;
        	else:
        		echo json_encode(
					array(
						'registered'=>false, 
						'message'=> esc_html__( 'You forgot about reCAPTCHA', 'listeo_core' )
					)
				);die();
    		endif;
        } 

        if($recaptcha_status && $recaptcha_version=="v3") {
	        	if(isset($_POST['token']) && !empty($_POST['token'])):
			        //your site secret key
			        $secret = get_option('listeo_recaptcha_secretkey3');
			        //get verify response data
			        $verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['token']);
			        $responseData_w = wp_remote_retrieve_body( $verifyResponse );
		        	$responseData = json_decode($responseData_w);
		        	
					if($responseData->success == '1' && $responseData->action == 'login' && $responseData->score >= 0.5) :

						//passed captcha, proceed to register
			            // 
		        	else:
		        		echo json_encode(
							array(
								'registered'=>false, 
								'message'=> esc_html__( 'reCAPTCHA was not validated', 'listeo_core' )
							)
						);
						die();
	        		endif;
	        	else:
	        		echo json_encode(
						array(
							'registered'=>false, 
							'message'=> esc_html__( 'You forgot about reCAPTCHA', 'listeo_core' )
						)
					);die();
	    		endif;
	        } 
    	
    	if ( get_option('listeo_display_password_field') ) :
	    	$result = $this->register_user( $email, $user_login, $first_name, $last_name, $role, $password,$custom_registration_fields  );
	    else :
    		$result = $this->register_user( $email, $user_login, $first_name, $last_name, $role, null,$custom_registration_fields );
    	endif;


		if ( is_wp_error($result) ){
			  echo json_encode(array('registered'=>false, 'message'=> $result->get_error_message()));
	    } else {

	    	if ( get_option('listeo_autologin') ) {
	    		if ( get_option('listeo_display_password_field') ) :
		        	echo json_encode(array('registered'=>true, 'message'=>esc_html__('Velkommen til gibbs! Du vil nå bli innlogget','listeo_core')));
		        else :
		        	echo json_encode(array('registered'=>true, 'message'=>esc_html__('You have been successfully registered,  you will be logged in a moment. Please check your email for the password.','listeo_core')));
		        endif;	
	    	} else {
		    	if ( get_option('listeo_display_password_field') ) :
		        	echo json_encode(array('registered'=>true, 'message'=>esc_html__('You have been successfully registered, you can login now.','listeo_core')));
		        else :
		        	echo json_encode(array('registered'=>true, 'message'=>esc_html__('You have been successfully registered. Please check your email for the password.','listeo_core')));
		        endif;	
	    	}
	    	
	    }
		die();
	}

	function listeo_core_gravatar_filter($avatar, $id_or_email, $size, $default, $alt, $args) {
		
		if(is_object($id_or_email)) {
	      // Checks if comment author is registered user by user ID
	      
	      if($id_or_email->user_id != 0) {
	        $email = $id_or_email->user_id;
	      // Checks that comment author isn't anonymous
	      } elseif(!empty($id_or_email->comment_author_email)) {
	        // Checks if comment author is registered user by e-mail address
	        $user = get_user_by('email', $id_or_email->comment_author_email);
	        // Get registered user info from profile, otherwise e-mail address should be value
	        $email = !empty($user) ? $user->ID : $id_or_email->comment_author_email;
	      }
	      $alt = $id_or_email->comment_author;
	    } else {
	      if(!empty($id_or_email)) {
	        // Find user by ID or e-mail address
	        $user = is_numeric($id_or_email) ? get_user_by('id', $id_or_email) : get_user_by('email', $id_or_email);
	      } else {
	        // Find author's name if id_or_email is empty
	        $author_name = get_query_var('author_name');
	        if(is_author()) {
	          // On author page, get user by page slug
	          $user = get_user_by('slug', $author_name);
	        } else {
	          // On post, get user by author meta
	          $user_id = get_the_author_meta('ID');
	          $user = get_user_by('id', $user_id);
	        }
	      }
	      // Set user's ID and name
	      if(!empty($user)) {
	        $email = $user->ID;
	        $alt = $user->display_name;
	      }
	    }
		if( is_email( $email ) && ! email_exists( $email ) ) {
			return $avatar;
		}else {
			  $email = false;
		  }
	

		$class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

		if ( ! $args['found_avatar'] || $args['force_default'] ) {
			$class[] = 'avatar-default';
		}

		if ( $args['class'] ) {
			if ( is_array( $args['class'] ) ) {
				$class = array_merge( $class, $args['class'] );
			} else {
				$class[] = $args['class'];
			}
		}
		if(isset($user)){
			$custom_avatar_id = get_user_meta($user->ID, 'listeo_core_avatar_id', true); 
		
			$custom_avatar = wp_get_attachment_image_src($custom_avatar_id,'listeo_core-avatar');
			if ($custom_avatar)  {
				$return = '<img src="'.$custom_avatar[0].'" class="'.esc_attr( join( ' ', $class ) ).'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';
			} elseif ($avatar) {
				$return = $avatar;
			} else {
				$return = '<img src="'.$default.'" class="'.esc_attr( join( ' ', $class ) ).'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';
			}
		} else {
			$return = $avatar;
		}
		
		return $return;
		
	}
	
	/**
	 * Actions in dashboard
	 */
	public function dashboard_action_handler() {
		global $post;

		if ( is_page(get_option( 'listeo_listings_page' ) ) ) {
			
			if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'listeo_core_my_listings_actions' ) ) {

			$action = sanitize_title( $_REQUEST['action'] );
			$listing_id = absint( $_REQUEST['listing_id'] );
			
			$cr_user = get_current_user_id();

			$group_admin = get_group_admin();

			if($group_admin != ""){
				$cr_user = $group_admin;
				$current_user = get_userdata($cr_user); 

				
			}else{
				$current_user = wp_get_current_user(); 
			}
			
    		
			$listing         = get_post( $listing_id );

			try {
				// Get Job
				$listing    = get_post( $listing_id );
				$listing_data = get_post( $listing );
				if ( ! $listing_data || 'listing' !== $listing_data->post_type ) {
					$title = false;
				} else {
					$title = esc_html( get_the_title( $listing_data ) );	
				}



				
				switch ( $action ) {
					
					case 'delete' :
						// Trash it
						if($current_user->ID == $listing->post_author && 'listing' == $listing_data->post_type ){

							$product_id = get_post_meta($listing_id,"product_id",true); 


							wp_trash_post( $listing_id );

							if($product_id && $product_id != ""){
								wp_trash_post( $product_id );
							}



						// Message
							$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( '%s har blitt slettet', 'listeo_core' ), $title ) . '</p><a class="close" href="#"></a></div>';
	
						} else {
							$this->dashboard_message =  '<div class="notification closeable error"><p>' . __( 'Du prøver å slette noe du ikke eier', 'listeo_core' ). '</p><a class="close" href="#"></a></div>';
						}
						
						break;
					case 'unpublish':
// Messages
						if($current_user->ID == $listing->post_author && 'listing' == $listing_data->post_type ){
							 wp_update_post(array(
						        'ID'    =>  $listing_id,
						        'post_status'   =>  'draft'
					        ));
							$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( '%s has been unpublished', 'listeo_core' ), $title ) . '</p><a class="close" href="#"></a></div>';
						}
					break;
					case 'renew':
							if(!get_option('listeo_new_listing_requires_purchase')){
							if($current_user->ID == $listing->post_author && 'listing' == $listing_data->post_type ){
								wp_update_post(array(
							        'ID'    =>  $listing_id,
							        'post_status'   =>  'publish'
						        ));
						        
						        	$post_types_expiry = new Listeo_Core_Post_Types;
									$post_types_expiry->set_expiry(get_post($listing_id));
								$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( '%s has been renewed', 'listeo_core' ), $title ) . '</p><a class="close" href="#"></a></div>';
								}
							}
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
		}
	}


	function author_archive_listings( $query ) {
	  
		if ($query->is_main_query() && $query->is_author() && $query->is_archive() ) {
			
	        $query->set( 'post_type', array('listing') );
	    }
		 
		return $query;

	}

	function modify_contact_methods($profile_fields) {

		// Add new fields
		$profile_fields['phone'] 	= esc_html__('Phone','listeo_core');
		$profile_fields['twitter'] 	= esc_html__('Twitter ','listeo_core');
		$profile_fields['facebook'] = esc_html__('Facebook URL','listeo_core');
		
		$profile_fields['linkedin'] = esc_html__('Linkedin','listeo_core');
		$profile_fields['instagram'] = esc_html__('Instagram','listeo_core');
		$profile_fields['youtube'] = esc_html__('YouTube','listeo_core');
		$profile_fields['skype'] = esc_html__('Skype','listeo_core');
		$profile_fields['whatsapp'] = esc_html__('WhatsApp','listeo_core');

		return $profile_fields;
	}
	
	protected function get_posted_field( $key, $field ) {
		
		return isset( $_POST[ $key ] ) ? $this->sanitize_posted_field( $_POST[ $key ] ) : '';
	}

	
	protected function get_posted_file_field( $key, $field ) {

		$file = $this->upload_file( $key, $field );
		
		if ( ! $file ) {
			$file = $this->get_posted_field( 'current_' . $key, $field );
		} elseif ( is_array( $file ) ) {
			$file = array_filter( array_merge( $file, (array) $this->get_posted_field( 'current_' . $key, $field ) ) );
		}

		return $file;
	}
	/**
	 * Handles the uploading of files.
	 *
	 * @param string $field_key
	 * @param array  $field
	 * @throws Exception When file upload failed
	 * @return  string|array
	 */
	protected function upload_file( $field_key, $field ) {
		if ( isset( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ]['name'] ) ) {
			if ( ! empty( $field['allowed_mime_types'] ) ) {
				$allowed_mime_types = $field['allowed_mime_types'];
			} else {
				$allowed_mime_types = listeo_get_allowed_mime_types();
			}

			$file_urls       = array();
			$files_to_upload = listeo_prepare_uploaded_files( $_FILES[ $field_key ] );

			foreach ( $files_to_upload as $file_to_upload ) {
				$uploaded_file = listeo_upload_file( $file_to_upload, array(
					'file_key'           => $field_key,
					'allowed_mime_types' => $allowed_mime_types,
					) );

				if ( is_wp_error( $uploaded_file ) ) {
					throw new Exception( $uploaded_file->get_error_message() );
				} else {
					$file_urls[] = $uploaded_file->url;
				}
			}

			if ( ! empty( $field['multiple'] ) ) {
				return $file_urls;
			} else {
				return current( $file_urls );
			}
		}
	}
	/**
	 * Navigates through an array and sanitizes the field.
	 *
	 * @param array|string $value The array or string to be sanitized.
	 * @return array|string $value The sanitized array (or string from the callback).
	 */
	protected function sanitize_posted_field( $value ) {
		// Santize value
		$value = is_array( $value ) ? array_map( array( $this, 'sanitize_posted_field' ), $value ) : sanitize_text_field( stripslashes( trim( $value ) ) );

		return $value;
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_textarea_field( $key, $field ) {
		return isset( $_POST[ $key ] ) ? wp_kses_post( trim( stripslashes( $_POST[ $key ] ) ) ) : '';
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_wp_editor_field( $key, $field ) {
		return $this->get_posted_textarea_field( $key, $field );
	}

	protected function create_attachment( $attachment_url ) {
		include_once( ABSPATH . 'wp-admin/includes/image.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );

		$upload_dir     = wp_upload_dir();
		$attachment_url = str_replace( array( $upload_dir['baseurl'], WP_CONTENT_URL, site_url( '/' ) ), array( $upload_dir['basedir'], WP_CONTENT_DIR, ABSPATH ), $attachment_url );

		if ( empty( $attachment_url ) || ! is_string( $attachment_url ) ) {
			return 0;
		}

		$attachment     = array(
			'post_title'   =>  wp_generate_password( 8, false ),
			'post_content' => '',
			'post_status'  => 'inherit',
			'guid'         => $attachment_url
		);

		if ( $info = wp_check_filetype( $attachment_url ) ) {
			$attachment['post_mime_type'] = $info['type'];
		}

		$attachment_id = wp_insert_attachment( $attachment, $attachment_url );

		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
			return $attachment_id;
		}

		return 0;
	}

	function submit_my_account_form() {
		global $blog_id, $wpdb;
		if ( isset( $_POST['my-account-submission'] ) && '1' == $_POST['my-account-submission'] ) {
			$current_user = wp_get_current_user();
			if(isset($_POST['bank_detail_user_id']) && $_POST['bank_detail_user_id'] != ""){
                $current_user = get_userdata($_POST['bank_detail_user_id']); 
			}
			$error = array();  

			if ( !empty( $_POST['url'] ) ) {
		       	wp_update_user( array ('ID' => $current_user->ID, 'user_url' => esc_attr( $_POST['url'] )));
			}

			if ( isset( $_POST['role'] ) ) {
//				$u = new WP_User( 3 );

				if($_POST['role'] == 'owner'){
					$current_user->set_role( 'owner' );	
				}
				if($_POST['role'] == 'guest'){
					$current_user->set_role( 'guest' );	
				}
				
			}

		    if ( isset( $_POST['email'] ) ){

		        if (!is_email(esc_attr( $_POST['email'] ))) {
		            $error = 'error_1'; // __('The Email you entered is not valid.  please try again.', 'profile');
		        	
		        } else {
		        	if(email_exists(esc_attr( $_POST['email'] ) ) ) {
		        		if(email_exists(esc_attr( $_POST['email'] ) ) != $current_user->ID) {
		        			$error = 'error_2'; // __('This email is already used by another user.  try a different one.', 'profile');	
		        		}
		            	
		        	} else {
		            $user_id = wp_update_user( 
		            	array (
		            		'ID' => $current_user->ID, 
		            		'user_email' => esc_attr( $_POST['email'] )
		            	)
		            );
		            }
		        }
		    }

			//echo "<pre>"; print_r($_POST); die;


		    if ( isset( $_POST['first-name'] ) ) {
		        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
		            wp_update_user( 
		            	array (
		            		'ID' => $current_user->ID, 
		            		'display_name' => esc_attr( $_POST['first-name'] )
		            	)
		            );
		    }
			
		    
		    if ( isset( $_POST['last-name'] ) ){
		        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
		    }		
			
			if ( isset( $_POST['country_code'] ) ){
		        update_user_meta($current_user->ID, 'country_code', esc_attr( $_POST['country_code'] ) );
		    }

		    
		    if ( isset( $_POST['phone'] ) ){
		        update_user_meta($current_user->ID, 'phone', esc_attr( $_POST['phone'] ) );
		    }	
			
		    if ( isset( $_POST['billing_first_name'] ) ){
		        update_user_meta($current_user->ID, 'billing_first_name', esc_attr( $_POST['billing_first_name'] ) );
		    }	
		    if ( isset( $_POST['billing_last_name'] ) ){
		        update_user_meta($current_user->ID, 'billing_last_name', esc_attr( $_POST['billing_last_name'] ) );
		    }

		    if ( isset( $_POST['billing_phone'] ) ){
		        update_user_meta($current_user->ID, 'billing_phone', esc_attr( $_POST['billing_phone'] ) );
		    }  
		    if ( isset( $_POST['billing_email'] ) ){
		        update_user_meta($current_user->ID, 'billing_email', esc_attr( $_POST['billing_email'] ) );
		    } 
		    if ( isset( $_POST['billing_company'] ) ){
		        update_user_meta($current_user->ID, 'billing_company', esc_attr( $_POST['billing_company'] ) );
		    }    				    


	    	if ( isset( $_POST['billing_address_1'] ) ){
		        update_user_meta($current_user->ID, 'billing_address_1', esc_attr( $_POST['billing_address_1'] ) );
		    }
		    if ( isset( $_POST['billing_address_2'] ) ){
		        update_user_meta($current_user->ID, 'billing_address_2', esc_attr( $_POST['billing_address_2'] ) );
		    }
		    if ( isset( $_POST['billing_city'] ) ){
		        update_user_meta($current_user->ID, 'billing_city', esc_attr( $_POST['billing_city'] ) );
		    }
		    if ( isset( $_POST['billing_postcode'] ) ){
		        update_user_meta($current_user->ID, 'billing_postcode', esc_attr( $_POST['billing_postcode'] ) );
		    }
		     if ( isset( $_POST['profile_type'] ) ){

		        update_user_meta($current_user->ID, 'profile_type', $_POST['profile_type']  );
		    }
		    if ( isset( $_POST['company_number'] ) ){

		        update_user_meta($current_user->ID, 'company_number', $_POST['company_number']  );
		    }
		    
		    
		    if ( isset( $_POST['display_name'] ) ) {
		        wp_update_user(array('ID' => $current_user->ID, 'display_name' => esc_attr( $_POST['display_name'] )));
		     	update_user_meta($current_user->ID, 'display_name' , esc_attr( $_POST['display_name'] ));
		    }
		    if ( !empty( $_POST['description'] ) ) {
		        update_user_meta( $current_user->ID, 'description', sanitize_textarea_field( $_POST['description'] ) );
		    }

		    if ( isset( $_POST['listeo_core_avatar_id'] ) ) {
		        update_user_meta( $current_user->ID, 'listeo_core_avatar_id', esc_attr( $_POST['listeo_core_avatar_id'] ) );
		    }

		    
		   //bank details
		    if ( isset( $_POST['payment_type'] ) ) {
		        update_user_meta( $current_user->ID, 'listeo_core_payment_type', esc_attr( $_POST['payment_type'] ) );
		    }
		    if ( isset( $_POST['ppemail'] ) ) {
		        update_user_meta( $current_user->ID, 'listeo_core_ppemail', esc_attr( $_POST['ppemail'] ) );
		    }
			
		    if ( isset( $_POST['bank_details'] ) ) {
		        update_user_meta( $current_user->ID, 'listeo_core_bank_details', esc_attr( $_POST['bank_details'] ) );
		    }
			if ( isset( $_POST['bank_details'] ) ) {
		        update_user_meta( $current_user->ID, 'listeo_core_bank_details', esc_attr( $_POST['bank_details'] ) );
		    }
			if ( isset( $_POST['currency'] ) ) {
				$group_admin = get_group_admin();

				if($group_admin != ""){
					$currency_user_id = $group_admin;
				}else{
					$currency_user_id = get_current_user_id();
				}
		        update_user_meta( $currency_user_id, 'currency', esc_attr( $_POST['currency'] ) );
		    }

		    $roles = $current_user->roles;
			$role = array_shift( $roles ); 
			switch ($role) {
	          	case 'owner':
	          		$fields = Listeo_Core_Meta_Boxes::meta_boxes_user_owner();
	          		break;
	          	case 'guest':
	          		$fields = Listeo_Core_Meta_Boxes::meta_boxes_user_guest();
	          		break;              	
	          	default:
	          		$fields = $fields = Listeo_Core_Meta_Boxes::meta_boxes_user_owner();
	          	break;
	          }
				        
			// foreach ( $fields as $key => $field ) : 
				
			// 	if ( isset($_POST[$field['id']]) && !empty( $_POST[$field['id']] ) ){

			//         update_user_meta($current_user->ID, $field['id'], esc_attr( $_POST[$field['id']] ) );
			//     }
			// endforeach;



			$values = array();

		
			foreach ( $fields as $key => $field ) {
				// Get the value
				$field_type = str_replace( '-', '_', $field['type'] );
				
				if ( $handler = apply_filters( "listeo_core_get_posted_{$field_type}_field", false ) ) {
					
					$values[ $key ] = call_user_func( $handler, $key, $field );
				} elseif ( method_exists( $this, "get_posted_{$field_type}_field" ) ) {
					
					$values[ $key ] = call_user_func( array( $this, "get_posted_{$field_type}_field" ), $key, $field );
				} else {
					
					$values[ $key ] = $this->get_posted_field( $key, $field );
				}
				
				// Set fields value

				$fields[ $key ]['value'] = $values[ $key ];

			}
			
			
			foreach ( $fields as $key => $field ) : 
			
				if( $field['type'] == 'select_multiple' || $field['type'] == 'multicheck_split') {
					
					delete_user_meta($current_user->ID, $key); 
					
					if ( is_array( $values[ $key ] ) ) {
					
							update_user_meta($current_user->ID, $key, $values[ $key ] );	
					}
				} else {
					
					update_user_meta( $current_user->ID, $key, $values[$key ] );
				}

				//update_post_meta( $this->listing_id, $key, $values[ $group_key ][ $key ] );	
				
			
				// // Handle attachments
				if ( 'file' === $field['type'] ) {
				
					//$attachment_id = is_numeric( $values[ $key ] ) ? $values[ $key ]  : $this->create_attachment( $values[ $key ] );
					$attachment_id = $this->create_attachment( $values[ $key ] );
			
					update_user_meta( $current_user->ID, $key.'_id', $attachment_id  );
				
				}
				
			endforeach;
			




			if ( count($error) == 0 ) {
		        //action hook for plugins and extra fields saving
		        //do_action('edit_user_profile_update', $current_user->ID);
		        wp_redirect( get_permalink().'?updated=true' ); 
		        exit;
		    } else {
				wp_redirect( get_permalink().'?user_err_pass='.$error ); 
				exit;
				 
			} 
		} // end if

	} // end 
	
	public function submit_change_password_form(){
		$error = false;
		if ( isset( $_POST['listeo_core-password-change'] ) && '1' == $_POST['listeo_core-password-change'] ) {
			$current_user = wp_get_current_user();
			if ( !empty($_POST['current_pass']) && !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {

				if ( !wp_check_password( $_POST['current_pass'], $current_user->user_pass, $current_user->ID) ) {
					/*$error = 'Your current password does not match. Please retry.';*/
					$error = 'error_1';
				} elseif ( $_POST['pass1'] != $_POST['pass2'] ) {
					/*$error = 'The passwords do not match. Please retry.';*/
					$error = 'error_2';
				} elseif ( strlen($_POST['pass1']) < 4 ) {
					/*$error = 'A bit short as a password, don\'t you think?';*/
					$error = 'error_3';
				} elseif ( false !== strpos( wp_unslash($_POST['pass1']), "\\" ) ) {
					/*$error = 'Password may not contain the character "\\" (backslash).';*/
					$error = 'error_4';
				} else {
					$user_id  = wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
					
					if ( is_wp_error( $user_id ) ) {
						/*$error = 'An error occurred while updating your profile. Please retry.';*/
						$error = 'error_5';
					} else {
						$error = false;
						do_action('edit_user_profile_update', $current_user->ID);
				        wp_redirect( get_permalink().'?updated_pass=true' ); 
				        exit;
					}
				}
			
				if ( $error ) {
					do_action('edit_user_profile_update', $current_user->ID);
			        wp_redirect( get_permalink().'?updated_pass=true' ); 
			        exit;
				} else {
					wp_redirect( get_permalink().'?err_pass='.$error ); 
					exit;
					 
				}
				
			}
		} // end if
	}

	public function  extra_profile_fields( $user ) { ?>
		 
		<h3><?php esc_html_e('Listeo_Core Avatar' , 'listeo_core' ); ?></h3>
		 <?php wp_enqueue_media(); ?>
		<table class="form-table">

		 
		<tr>
		<th><label for="image">Agent Avatar</label></th>
		 
		<td>
			<?php 
				$custom_avatar_id = get_the_author_meta( 'listeo_core_avatar_id', $user->ID ) ;
				$custom_avatar = wp_get_attachment_image_src($custom_avatar_id,'listeo-avatar');
				if ($custom_avatar)  {
					echo '<img src="'.$custom_avatar[0].'" style="width:100px;height: auto;"/><br>';
				} 
			?>
		<input type="text" name="listeo_core_avatar_id" id="agent-avatar" value="<?php echo esc_attr( get_the_author_meta( 'listeo_core_avatar_id', $user->ID ) ); ?>" class="regular-text" />
		<input type='button' class="realteo-additional-user-image button-primary" value="<?php _e( 'Upload Image','listeo_core' ); ?>" id="uploadimage"/><br />
		<span class="description"><?php esc_html_e('This avatar will be displayed instead of default one','listeo_core'); ?></span>
		</td>
		</tr>
		 
		</table>

		<h3><?php esc_html_e('Extra profile information' , 'listeo_core' ); ?></h3>
		 
		<table class="form-table">

		 
<!-- 		<tr>
		<th><label for="image">Title</label></th>
		 
		<td>
		<input type="text" name="agent_title" id="agent-title" value="<?php echo esc_attr( get_the_author_meta( 'agent_title', $user->ID ) ); ?>" class="regular-text" />
		<span class="description"><?php esc_html_e('This text will be displayed below your Name on Agents list','listeo_core'); ?></span>
		</td>
		</tr> -->

		<?php 
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		$role = array_shift( $roles ); 
		switch ($role) {
          	case 'owner':
          		$fields_user = Listeo_Core_Meta_Boxes::meta_boxes_user_owner();
          		break;
          	case 'guest':
          		$fields_user = Listeo_Core_Meta_Boxes::meta_boxes_user_guest();
          		break;              	
          	default:
          		$fields_user = $fields = Listeo_Core_Meta_Boxes::meta_boxes_user_owner();
          	break;
      	}
		
		
			
			foreach ( $fields_user as $key => $field ) : 
			
				
			?>
		<tr>
			<th><label for="image"><?php echo $field['name']; ?></label></th>	 
			<td>

				<?php
				
				 switch ($field['type']) {
					case 'text':

					$field_value = esc_attr( get_the_author_meta( $field['id'], $user->ID ) );


					if($key == "phone"){
						if (strpos($field_value, "+") != true) {
							 $field_value = get_the_author_meta( "country_code", $user->ID,true ).$field_value;
						}
					}	
						?>
						<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo $field_value; ?>" class="regular-text" />
				
						<?php
						break;
					case 'radio':
					?>
					<div class="listeo-radios in-row margin-bottom-20">
						<?php $value = get_the_author_meta( $field['id'], $user->ID ); ?>
						<?php foreach ( $field['options'] as $slug => $name ) : ?>
						<div>
							<input id="<?php echo esc_html($slug) ?>" type="radio" name="<?php echo $key; ?>"
							<?php  checked($value,$slug) ?> value="<?php echo esc_html($slug); ?>"
							>
							<label for="<?php echo esc_html($slug) ?>"><?php echo esc_html($name) ?></label>
						</div>
						<?php endforeach; ?>

					</div>
					<?php
					break;
					case 'wp-editor':

							$editor = apply_filters( 'listeo_core_user_data_wp_editor_args', array(
								'textarea_name' => isset( $field['id'] ) ? $field['id'] : '',
								'media_buttons' => false,
								'textarea_rows' => 8,
								'quicktags'     => false,
								'tinymce'       => array(
									'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
									'paste_as_text'                 => true,
									'paste_auto_cleanup_on_paste'   => true,
									'paste_remove_spans'            => true,
									'paste_remove_styles'           => true,
									'paste_remove_styles_if_webkit' => true,
									'paste_strip_class_attributes'  => true,
									'toolbar1'                      => 'bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
									'toolbar2'                      => '',
									'toolbar3'                      => '',
									'toolbar4'                      => ''
								),
							) );
							$field['value'] = get_the_author_meta( $field['id'], $user->ID );
							wp_editor( isset( $field['value'] ) ? wp_kses_post( $field['value'] ) : '', $key, $editor );
					break;
					case 'select':
						$field['value'] = get_the_author_meta( $field['id'], $user->ID );
						?>
						<select name="<?php echo esc_attr( isset( $field['id'] ) ? $field['id'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>"><?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
								<option value=""><?php echo esc_attr($field['placeholder']);?></option>
							<?php endif ?>
							<?php foreach ( $field['options'] as $key => $value ) : ?>	
							<option value="<?php echo esc_attr( $key ); ?>" <?php 
								if ( isset( $field['value'] ) || isset( $field['default'] ) ) 
									if(isset( $field['value']) && is_array($field['value'])){
										if( in_array($key,$field['value']) ) {
											echo "selected='selected'";
										}
									} else {
										selected( isset( $field['value'] ) ? $field['value'] : $field['default'], $key );
									}
									 ?> >
								<?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php 
					break;

					case 'select_multiple':
					$field['value'] = get_the_author_meta( $field['id'], $user->ID );
					if(isset( $field['options_cb'] ) && !empty($field['options_cb'])){
							switch ($field['options_cb']) {
								case 'realteo_get_offer_types_flat':
									$field['options'] = realteo_get_offer_types_flat(false);
									break;

								case 'realteo_get_property_types':
									$field['options'] = realteo_get_property_types();
									break;

								case 'realteo_get_rental_period':
									$field['options'] = realteo_get_rental_period();
									break;
								
								default:
									# code...
									break;
							}	
						} ?>
							<select multiple name="<?php echo esc_attr($field['id']);?>[]" id="<?php echo esc_attr( $key ); ?>"><?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
									<option value=""><?php echo esc_attr($field['placeholder']);?></option>
								<?php endif ?>
								<?php foreach ( $field['options'] as $key => $value ) : ?>	
								<option value="<?php echo esc_attr( $key ); ?>" <?php 
									if ( isset( $field['value'] ) || isset( $field['default'] ) ) 
										if(isset( $field['value']) && is_array($field['value'])){
											if( in_array($key,$field['value']) ) {
												echo "selected='selected'";
											}
										} else {
											selected( isset( $field['value'] ) ? $field['value'] : $field['default'], $key );
										}
										 ?> >
									<?php echo esc_html( $value ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php 
					break;

					case 'checkbox':
					$field['value'] = get_the_author_meta( $field['id'], $user->ID );?>

						<input type="checkbox" name="<?php echo $field['id'] ?>"  value="on" <?php if( isset( $field['value'])&& !empty($field['value'] ) ){ echo "checked"; }  ?>>
						
						
					<?php
					break;
					case 'file':
					$field['value'] = get_the_author_meta( $field['id'], $user->ID );?>

						<ul>
							<li>Attachment ID: <input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo esc_attr( get_the_author_meta( $field['id'], $user->ID ) ); ?>" class="regular-text" /></li>
							<?php if($field['value']): ?><li><a href="<?php echo wp_get_attachment_url($field['value']);?>" target="_blank">Download this file &darr;</a></li><?php endif; ?>
						</ul>
				
						
					<?php
					break;

					case 'multicheck_split':
						$value = get_the_author_meta( $field['id'], $user->ID );
						
						foreach ( $field['options'] as $slug => $name ) : ?>

							<input id="<?php echo esc_html($slug) ?>" type="checkbox" name="<?php echo $key.'[]'; ?>"
							<?php  if(is_array($value) && in_array($slug,$value))  : ?> checked="checked" <?php endif; ?> value="<?php echo esc_html($slug); ?>"
							>
							<label for="<?php echo esc_html($slug) ?>"><?php echo esc_html($name) ?></label>
						
						<?php endforeach;
					break;

					default:
					break;
				} ?>
				
			</td>
		</tr>
			<?php endforeach;
		?>
		 
		</table>
	<?php }


	function save_extra_profile_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) )
		return false;


		if(isset($_POST['listeo_core_avatar_id'])) {
			update_user_meta( $user_id, 'listeo_core_avatar_id', $_POST['listeo_core_avatar_id'] );	
		}
		$current_user = wp_get_current_user();
			
		
		    $roles = $current_user->roles;
			$role = array_shift( $roles ); 
			switch ($role) {
	          	case 'owner':
	          		$fields = Listeo_Core_Meta_Boxes::meta_boxes_user_owner();
	          		break;
	          	case 'guest':
	          		$fields = Listeo_Core_Meta_Boxes::meta_boxes_user_guest();
	          		break;              	
	          	default:
	          		$fields = Listeo_Core_Meta_Boxes::meta_boxes_user_owner();
	          	break;
	          }
				        
			// foreach ( $fields as $key => $field ) : 
				
			// 	if ( isset($_POST[$field['id']]) && !empty( $_POST[$field['id']] ) ){

			//         update_user_meta($current_user->ID, $field['id'], esc_attr( $_POST[$field['id']] ) );
			//     }
			// endforeach;



			$values = array();

		
			foreach ( $fields as $key => $field ) {
				// Get the value
				$field_type = str_replace( '-', '_', $field['type'] );
				
				if($field_type == 'file') { $field_type = 'text'; } 

				if ( $handler = apply_filters( "listeo_core_get_posted_{$field_type}_field", false ) ) {
					
					$values[ $key ] = call_user_func( $handler, $key, $field );
				} elseif ( method_exists( $this, "get_posted_{$field_type}_field" ) ) {
					
					$values[ $key ] = call_user_func( array( $this, "get_posted_{$field_type}_field" ), $key, $field );
				} else {
					
					$values[ $key ] = $this->get_posted_field( $key, $field );
				}
				
				// Set fields value

				$fields[ $key ]['value'] = $values[ $key ];

			}

			listeo_write_log($values);
			
			foreach ( $fields as $key => $field ) : 
			
				if( $field['type'] == 'select_multiple' || $field['type'] == 'multicheck_split') {
					
					delete_user_meta($current_user->ID, $key); 
					
					if ( is_array( $values[ $key ] ) ) {
					
						update_user_meta($user_id, $key, $values[ $key ] );	
					}
				} else {
					
					update_user_meta( $user_id, $key, $values[$key ] );
				}

				//update_post_meta( $this->listing_id, $key, $values[ $group_key ][ $key ] );	
				
			
				// // Handle attachments
				// if ( 'file' === $field['type'] ) {
				
				// 	//$attachment_id = is_numeric( $values[ $key ] ) ? $values[ $key ]  : $this->create_attachment( $values[ $key ] );
				// 	$attachment_id = $this->create_attachment( $values[ $key ] );
			
				// 	update_user_meta( $user_id, $key.'_id', $attachment_id  );
				
				// }
				
			endforeach;
			



	}

	public function my_account( $atts = array() ) {
		$template_loader = new Listeo_Core_Template_Loader;
		ob_start();
		if ( is_user_logged_in() ) : 
		$template_loader->get_template_part( 'my-account' ); 
		else :
		$template_loader->get_template_part( 'account/login' ); 
		endif;
		return ob_get_clean();
	}	


	public function change_password( $atts = array() ) {
		$template_loader = new Listeo_Core_Template_Loader;
		ob_start();
		$template_loader->set_template_data( array( 'current' => 'password' ) )->get_template_part( 'account/navigation' );
		$template_loader->get_template_part( 'account/change_password' ); 
		return ob_get_clean();
	}	

	public function lost_password( $atts = array() ) {
		$template_loader = new Listeo_Core_Template_Loader;
		$errors = array();
		if ( isset( $_REQUEST['errors'] ) ) {
			$error_codes = explode( ',', $_REQUEST['errors'] );
			foreach ( $error_codes as $error_code ) {
				$errors[]= $this->get_error_message( $error_code );
			}
		} 
		ob_start();
		$template_loader->set_template_data( array( 'errors' => $errors ) )->get_template_part( 'account/lost_password' ); 
		return ob_get_clean();
	}


	public function reset_password( $atts = array() ) {
		$template_loader = new Listeo_Core_Template_Loader;
		$attributes = array();
		if ( is_user_logged_in() ) {
			return '<div class="notification success closeable">
							<p>'. __( 'You are already signed in.', 'listeo_core' ).'</p>
					</div>';
			
		} else {
			if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
				$attributes['login'] = $_REQUEST['login'];
				$attributes['key'] = $_REQUEST['key'];
				// Error messages
				$errors = array();
				if ( isset( $_REQUEST['error'] ) ) {
					$error_codes = explode( ',', $_REQUEST['error'] );
					foreach ( $error_codes as $code ) {
						$errors []= $this->get_error_message( $code );
					}
				}
				$attributes['errors'] = $errors;
				ob_start();
				$template_loader->set_template_data( array( 'attributes' => $attributes ) )->get_template_part( 'account/reset_password' ); 
				return ob_get_clean();
			} else if(isset( $_GET['password'] ) ) {
				return '<div class="notification success closeable">
							'. __( 'Password has been changed.', 'listeo_core' ).'
						</div>';
				
			} else if(isset( $_GET['checkemail'] ) ) {

				return '<div class="notification success closeable">'
							.__( 'A confirmation link has been sent to your email address.', 'listeo_core' ).'
						</div>';

			} else {
				return '<div class="notification success closeable">'
							.__( 'Invalid password reset link.', 'listeo_core' ).'
						</div>';
			}
		}
		
	}

	/**
	 * User dashboard
	 */
	public function listeo_dashboard( $atts ) {

		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to access your listings.', 'listeo_core' );
		}

		extract( shortcode_atts( array(
			//'posts_per_page' => '25',
		), $atts ) );

		ob_start();

		$template_loader = new Listeo_Core_Template_Loader;		
		$template_loader->set_template_data( 
			array( 
				'message' => $this->dashboard_message, 

			) )->get_template_part( 'account/dashboard' ); 


		return ob_get_clean();
	}	

	/**
	 * User listings shortcode
	 */
	public function listeo_core_my_listings( $atts ) {
		
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your listings.', 'listeo_core' );
		}

		
		$page = (isset($_GET['listings_paged'])) ? $_GET['listings_paged'] : 1;
		
		if(isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
			$status = $_REQUEST['status'];
		} else {
			$status = '';
		}
		ob_start();
		$template_loader = new Listeo_Core_Template_Loader;
		
		//$status = isset($_GET['status']) ? $_GET['status'] : '' ;
		$search = isset($_GET['search']) ? $_GET['search'] : '' ;
		
		$template_loader->set_template_data( 
			array( 
				'message' => $this->dashboard_message, 
				'ids' => $this->get_agent_listings($status,$page,10,$search),
				'status' => $status, 
			) )->get_template_part( 'account/my_listings' ); 


		return ob_get_clean();
	}	

	/**
	 * User listings shortcode
	 */
	public function listeo_core_my_packages( $atts ) {
		
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your packages.', 'listeo_core' );
		}

		extract( shortcode_atts( array(
			'posts_per_page' => '25',
		), $atts ) );

		ob_start();
		$template_loader = new Listeo_Core_Template_Loader;

		$template_loader->set_template_data( array( 'current' => 'my_packages' ) )->get_template_part( 'account/navigation' ); 
		$template_loader->get_template_part( 'account/my_packages' ); 


		return ob_get_clean();
	}


	public function my_orders(){
		wc_get_template( 'myaccount/my-orders.php' );
	}


	/**
	 * Function to get ids added by the user/agent
	 * @return array array of listing ids
	 */
	public function get_agent_listings($status,$page,$per_page,$search = false){
		$current_user = wp_get_current_user();
		global $wpdb;
		
		switch ($status) {
			case 'pending':
				$post_status = array('pending_payment','draft','pending');
				break;
			
			case 'active':
				$post_status = array('publish');
				break;

			case 'expired':
				$post_status = array('expired');
				break;
			
			default:
				$post_status = array('publish','pending_payment','expired','draft','pending');
				break;
		}

		$listing_user = $current_user->ID;

		$group_admin = get_group_admin();

		if($group_admin != ""){
			$listing_user = $group_admin;
		}
		$postArray = array(
						'author'        	=>  $listing_user,
					    'fields'          	=> 'ids', // Only get post IDs
					    'posts_per_page'  	=> $per_page,
					    'post_type'		  	=> 'listing',
					    'post_parent'		=> "0",
					    'paged'				=> $page,
					    's'					=> $search,
					    'post_status'	  	=> $post_status,
					);

		if(isset($_GET['listing_ids']) && $_GET['listing_ids'] != ""){
			$listing_idsss = explode(",",$_GET['listing_ids']);
			$postArray["post__in"] = $listing_idsss;

		}
		

		$q = new WP_Query($postArray);
		return $q;
	}
	public function get_user_all_listings(){
		$current_user = wp_get_current_user();
		global $wpdb;
		
		

		$listing_user = $current_user->ID;

		$group_admin = get_group_admin();

		if($group_admin != ""){
			$listing_user = $group_admin;
		}
		//echo $listing_user; die;
		$postArray = array(
						'author'        	=>  $listing_user,
					    'fields'          	=> 'ids', // Only get post IDs
					    'post_type'		  	=> 'listing',
					    'post_parent'		=> "0",
					);

		

		$q = new WP_Query($postArray);
		// echo "<pre>"; print_r($q); die;
		return $q->posts;
	}

	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user( $redirect_to = null ) {
	    $user = wp_get_current_user();
	    if ( user_can( $user, 'manage_options' ) ) {
	        if ( $redirect_to ) {
	            wp_safe_redirect( $redirect_to );
	        } else {
	            wp_redirect( admin_url() );
	        }
	    } else {
	        wp_redirect( home_url( get_permalink(get_option( 'listeo_profile_page' )) ) );
	    }
	}

	public function redirect_woocommerce( $redirect_to, $user ) {
	
		// $redirect = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : wp_get_raw_referer();
		//$redirect_to = get_permalink(get_option( 'listeo_profile_page' ));
	   	// $redirect_to = wp_get_raw_referer()() ? wp_get_raw_referer()() : home_url();
		// 
		$role = $user->roles[0];

		if($role == 'owner') {
						                	
				$redirect_page_id = get_option('listeo_owner_login_redirect');
				if($redirect_page_id){
					$redirect_to = get_permalink($redirect_page_id);
				} else {
						$org_ref = wp_get_referer();
				        if($org_ref){
				        	$redirect_to = $org_ref;
				        } else {
				        	$redirect_to = get_permalink(get_option( 'listeo_profile_page' ));
				        }
				}
				
			} else if($role == 'guest') {

				$redirect_page_id = get_option('listeo_guest_login_redirect');
				if($redirect_page_id){
					$redirect_to = get_permalink($redirect_page_id);
				} else {
					  	$org_ref = wp_get_referer();
				        if($org_ref){
				        	$redirect_to = $org_ref;
				        } else {
				        	$redirect_to = get_permalink(get_option( 'listeo_profile_page' ));
				        }
				}

			} else {
				$org_ref = wp_get_referer();
		        if($org_ref){
		        	$redirect_to = $org_ref;
		        } else {
		        	$redirect_to = get_permalink(get_option( 'listeo_profile_page' ));
		        }
			}
    	return $redirect_to;
	}
	
	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 */
	function redirect_to_custom_login() {
	    if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
	        $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
	     
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user( $redirect_to );
	            exit;
	        }
	 
	        // The rest are redirected to the login page
	        $login_url = get_permalink(get_option( 'listeo_profile_page' ));
	        if ( ! empty( $redirect_to ) ) {
	            $login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
	        }
	 
	        wp_redirect( $login_url );
	        exit;
	    }
	}

	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of
	 * wp-login.php?action=lostpassword.
	 */
	public function redirect_to_custom_lostpassword() {

	    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user();
	            exit;
	        }
	 
	 		$lost_password_page = get_option( 'listeo_lost_password_page' );
	 		if(!empty($lost_password_page)) {
	 			wp_redirect(get_permalink($lost_password_page ));	
	 		} else {
	 			esc_html_e("Please set a Lost Password Page in Listeo_Core Options -> Pages",'listeo_core');
	 		}
	        
	        exit;
	    }
	}

	/**
	 * Initiates password reset.
	 */
	public function do_password_lost() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$errors = retrieve_password();
			if ( is_wp_error( $errors ) ) {
				// Errors found
				$redirect_url = get_permalink(get_option( 'listeo_reset_password_page' ));
				$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
			} else {
				// Email sent
				$redirect_url = get_permalink(get_option( 'listeo_reset_password_page' ));
				$redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
				if ( ! empty( $_REQUEST['redirect_to'] ) ) {
					$redirect_url = $_REQUEST['redirect_to'];
				}
			}
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Redirects to the custom password reset page, or the login page
	 * if there are errors.
	 */
	public function redirect_to_custom_password_reset() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			// Verify key / login combo
			$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( get_permalink(get_option( 'listeo_lost_password_page' )).'?login=expiredkey' );
				} else {
					wp_redirect( get_permalink(get_option( 'listeo_lost_password_page' )).'?login=invalidkey');
				}
				exit;
			}
			$redirect_url = get_permalink(get_option( 'listeo_reset_password_page' ));
			$redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
			$redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		}
	}


	/**
	 * Redirects the user to the custom registration page instead
	 * of wp-login.php?action=register.
	 */
	public function redirect_to_custom_register() {
	    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user();
	        } else {
	            wp_redirect( get_permalink(get_option( 'listeo_profile_page' )) );
	        }
	        exit;
	    }
	}

	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
	 * @param string            $username   The user name used to log in.
	 * @param string            $password   The password used to log in.
	 *
	 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
	 */
	function maybe_redirect_at_authenticate( $user, $username, $password ) {
	    // Check if the earlier authenticate filter (most likely, 
	    // the default WordPress authentication) functions have found errors
	    
	    if( isset($_POST['action']) && ($_POST['action'] == 'listeoajaxlogin' || $_POST['action'] == "gibbsajaxlogin"))  {
			return $user;
	    }
	    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	        if ( is_wp_error( $user ) ) {
	            $error_codes = join( ',', $user->get_error_codes() );
	 
	            	$login_url = get_permalink(get_option( 'listeo_dashboard_page' ));
	            	$login_url = add_query_arg( 'login', $error_codes, $login_url );
	 
	            wp_redirect( $login_url );
	            exit;
	        }
	    }
	 
	    return $user;
	}


	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message( $error_code ) {
	    switch ( $error_code ) {
	        case 'email_exists':
	            return __( 'Denne eposten er allerede brukt. Vennligst trykk på glemt passord', 'listeo_core' );
	  		break;
	  		case 'username_exists':
	            return __( 'Dette brukernavnet er allerede brukt. Vennligt bruk et annet. ', 'listeo_core' );
	 		break;
	 		case 'empty_username':
	            return __( 'Vennligst skriv inn epost.', 'listeo_core' );
	 		break;
	        case 'empty_password':
	            return __( 'Vennligst skriv inn passord', 'listeo_core' );
	 		break;
	        case 'invalid_username':
	            return __(
	                "We don't have any users with that email address. Maybe you used a different one when signing up?", 'listeo_core' );
	 		break;
	        case 'incorrect_password':
	            $err = __(
	                "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
	                'listeo_core'
	            );
	            return sprintf( $err, wp_lostpassword_url() );
	 		break;
	        default:
	            break;
	    }
	     
	    return __( 'An unknown error occurred. Please try again later.', 'listeo_core' );
	}


	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
	    $redirect_url = home_url();

	    if ( ! isset( $user->ID ) ) {
	        return $redirect_url;
	    }
	 
	    if ( user_can( $user, 'manage_options' ) ) {
	        // Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
	        if ( $requested_redirect_to == '' ) {
	            $redirect_url = admin_url();
	        } else {
	            $redirect_url = $requested_redirect_to;
	        }
	    } else {
	        // Non-admin users always go to their account page after login
	        $user_data = get_userdata( $user->ID  );
        	$roles = $user_data->roles;

			$role = array_shift( $roles ); 

			if($role == 'owner') {
						                	
				$redirect_page_id = get_option('listeo_owner_login_redirect');
				if($redirect_page_id){
					$redirect_url = get_permalink($redirect_page_id);
				} else {
						$org_ref = wp_get_referer();
				        if($org_ref){
				        	$redirect_url = $org_ref;
				        } else {
				        	$redirect_url = get_permalink(get_option( 'listeo_profile_page' ));
				        }
				}
				
			} else if($role == 'guest') {

				$redirect_page_id = get_option('listeo_guest_login_redirect');
				if($redirect_page_id){
					$redirect_url = get_permalink($redirect_page_id);
				} else {
					  	$org_ref = wp_get_referer();
				        if($org_ref){
				        	$redirect_url = $org_ref;
				        } else {
				        	$redirect_url = get_permalink(get_option( 'listeo_profile_page' ));
				        }
				}

			} else {
				$org_ref = wp_get_referer();
		        if($org_ref){
		        	$redirect_url = $org_ref;
		        } else {
		        	$redirect_url = get_permalink(get_option( 'listeo_profile_page' ));
		        }
			}
	
	    }
	 
	    return wp_validate_redirect( $redirect_url, home_url() );
	}

	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param string $email         The new user's email address
	 * @param string $first_name    The new user's first name
	 * @param string $last_name     The new user's last name
	 *
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 */
	private function register_user( $email, $user_login, $first_name, $last_name, $role, $password, $custom_registration_fields ) {
	    $errors = new WP_Error();


	 
	    // Email address is used as both username and email. It is also the only
	    // parameter we need to validate
	    if ( ! is_email( $email ) ) {
	        $errors->add( 'email', $this->get_error_message( 'email' ) );
	        return $errors;
	    }
	 
	    if ( email_exists( $email ) ) {
	        $errors->add( 'email_exists', $this->get_error_message( 'email_exists') );
	        return $errors;
	    }

	    if ( username_exists( $user_login ) ) {
	        $errors->add( 'username_exists', $this->get_error_message( 'username_exists') );
	        return $errors;
	    }
	 
	    // Generate the password so that the subscriber will have to check email...
	    if(!$password) {  
		    $password = wp_generate_password( 12, false );
		}

	    $user_data = array(
	        'user_login'    => $user_login,
	        'user_email'    => $email,
	        'user_pass'     => $password,
	        'first_name'    => $first_name,
	        'last_name'     => $last_name,
	        'nickname'      => $first_name,
	        'role'			=> $role
	    );
	 
	    $user_id = wp_insert_user( $user_data );
	    
        

        if(isset($_POST["country_code"])){
	    	update_user_meta( $user_id, 'country_code',$_POST["country_code"] );
	    }
		foreach ( $custom_registration_fields as $key => $field ) : 
			
				if( $field['type'] == 'select_multiple' || $field['type'] == 'multicheck_split') {
					
					if ( is_array( $field[ 'value' ] ) ) {
					
							update_user_meta( $user_id, $field['name'], $field[ 'value' ] );	
					}
				} else {
					if( $field['type'] == 'checkbox' ){

						if(!empty($field['value'])) {
							update_user_meta( $user_id, $field['name'], $field[ 'value' ] );	
						}

					} else {

						update_user_meta( $user_id, $field['name'], $field[ 'value' ] );	
					
					}
					
				}

				//update_post_meta( $this->listing_id, $key, $values[ $group_key ][ $key ] );	
				
			
				// // Handle attachments
				if ( 'file' === $field['type'] ) {
				
					//$attachment_id = is_numeric( $values[ $key ] ) ? $values[ $key ]  : $this->create_attachment( $values[ $key ] );
					$attachment_id = $this->create_attachment( $field[ 'value' ] );
			
					update_user_meta(  $user_id,$field['name'], $attachment_id  );
				
				}
				
			endforeach;
			


	    // if(isset($custom_registration_fields) && !empty($custom_registration_fields)){
	    // 	foreach ($custom_registration_fields as $key => $field) {




	    // 		listeo_write_log($field);
	    // 		update_user_meta( $user_id, $field['name'], $field['value'] );
	    // 	}
	    // }
	    if ( ! is_wp_error( $user_id ) ) {
			wp_new_user_notification( $user_id, $password,'both' );
			if(get_option('listeo_autologin')){
				wp_set_current_user($user_id); // set the current wp user
	    		wp_set_auth_cookie($user_id); 	
			}
			
		}
	    
	 
	    return $user_id;
	}


	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
	public function do_register_user() {

	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	        $redirect_url = get_permalink(get_option( 'listeo_profile_page' )).'#tab2';
	 		
	        if ( ! get_option( 'users_can_register' ) ) {
	            // Registration closed, display error
	            $redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
	        } else {
	            $email = $_POST['email'];
	            $first_name = (isset($_POST['first_name'])) ? sanitize_text_field( $_POST['first_name'] ) : '' ;
	            $last_name = (isset($_POST['last_name'])) ? sanitize_text_field( $_POST['last_name'] ) : '' ;
	            // get/create username

			    if ( get_option('listeo_registration_hide_username') ) {
		  			$email_arr = explode('@', $email);
		            $user_login = sanitize_user(trim($email_arr[0]), true);
		        } else {
		 			$user_login = sanitize_user(trim($_POST['username']));
		        }
		        

	            $role =  (isset($_POST['user_role'])) ? sanitize_text_field( $_POST['user_role'] ) : get_option('default_role');
		        //$role = sanitize_text_field($_POST['role']);
		        if (!in_array($role, array('owner', 'guest'))) {
					$role = get_option('default_role');
				}

	            $password = (!empty($_POST['password'])) ? sanitize_text_field( $_POST['password'] ) : false ;
	            if($role=='owner') {
					$fields = get_option('listeo_owner_registration_form');
	            } else {
	            	$fields = get_option('listeo_guest_registration_form');
	            }
	            	
	           	 $custom_registration_fields = array();
		            if(!empty($fields)){
		            	//get fields for registration

		            	foreach ($fields as $key => $field) {

		            	
		            		$field_type = str_replace( '-', '_', $field['type'] );
				
							if ( $handler = apply_filters( "listeo_core_get_posted_{$field_type}_field", false ) ) {
								
								$value = call_user_func( $handler, $key, $field );
							} elseif ( method_exists( $this, "get_posted_{$field_type}_field" ) ) {
								
								$value = call_user_func( array( $this, "get_posted_{$field_type}_field" ), $key, $field );
							} else {
								
								$value = $this->get_posted_field( $key, $field );
							}
							
							// Set fields value

							


		            		if(isset($field['required']) && !empty($field['required'])) {
		            		
		            			if(!$value){
		            				$redirect_url = add_query_arg( 'register-errors', 'required-field', $redirect_url );
		            				wp_redirect( $redirect_url );
	        						exit;
		            			} else {
		            				$field['value'] = $value;
		            				$custom_registration_fields[] = $field;
		            			}

		            		} else {
		            			
		            			$field['value'] = $value;
		            			
		            			$custom_registration_fields[] = $field;
		            		}

		            	}
		            }
				
	            $recaptcha_status = get_option('listeo_recaptcha');
	             
		        $recaptcha_version = get_option('listeo_recaptcha_version');

	            
	             if(get_option('listeo_display_password_field')) {
	            	if(!$password) {
	            		
	            		$redirect_url = add_query_arg( 'register-errors', 'password-no', $redirect_url );
	            		wp_redirect( $redirect_url );
	        			exit;
	            	}
	            }

            	// get custom field
        		switch ($role) {
	              	case 'owner':
	              		$fields = get_option('listeo_owner_registration_form');
	              		break;
	              	case 'guest':
	              		$fields = get_option('listeo_guest_registration_form');
	              		break;              	
	            }


	            $recaptcha_pass = true;
	            if($recaptcha_status) {
	            	$recaptcha_pass = false;

	            	if($recaptcha_version=="v2" && isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
						$secret = get_option('listeo_recaptcha_secretkey');
				        //get verify response data
				
				        $verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
				        $responseData = json_decode($verifyResponse['body']);
						if( $responseData->success ):
							//passed captcha, proceed to register
				            $recaptcha_pass = true;
			        	else:
			        		$redirect_url = add_query_arg( 'register-errors', 'captcha-fail', $redirect_url );
		        		endif;
		        	else:
		        		$redirect_url = add_query_arg( 'register-errors', 'captcha-no', $redirect_url );
	        		endif;



					if($recaptcha_version=="v3" && isset($_POST['token']) && !empty($_POST['token'])):
				        //your site secret key
				        $secret = get_option('listeo_recaptcha_secretkey3');
				        //get verify response data
				        $verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['token']);
						$responseData_w = wp_remote_retrieve_body( $verifyResponse );
				        $responseData = json_decode($responseData_w);

						if($responseData->success == '1' && $responseData->action == 'login' && $responseData->score >= 0.5) :
							//passed captcha, proceed to register
				             $recaptcha_pass = true;
			        	else:
			        		$redirect_url = add_query_arg( 'register-errors', 'captcha-fail', $redirect_url );
		        		endif;
		        	else:
		        		$redirect_url = add_query_arg( 'register-errors', 'captcha-fail', $redirect_url );
	        		endif;

	        		if($recaptcha_pass == false){
	        			wp_redirect( $redirect_url );
	        		}
	            }

	            $privacy_policy_status = get_option('listeo_privacy_policy');
	            $privacy_policy_pass = true;
	            if($privacy_policy_status) {
	            	$privacy_policy_pass = false;
	            	if(isset($_POST['privacy_policy']) && !empty($_POST['privacy_policy'])):
	            		$privacy_policy_pass = true;
	            	else :
	            		$redirect_url = add_query_arg( 'register-errors', 'policy-fail', $redirect_url );
	            	endif;
	            }

	    	
	    		$terms_and_conditions_status =  get_option('listeo_terms_and_conditions_req');
	            $terms_and_conditions_pass = true;
	            if($terms_and_conditions_status) {
	            	$terms_and_conditions_pass = false;
	            	if(isset($_POST['terms_and_conditions']) && !empty($_POST['terms_and_conditions'])):
	            		$terms_and_conditions_pass = true;
	            	else :
	            		$redirect_url = add_query_arg( 'register-errors', 'terms-fail', $redirect_url );
	            	endif;
	            }

	            if($recaptcha_pass && $privacy_policy_pass && $terms_and_conditions_pass){

		            $result = $this->register_user( $email, $user_login, $first_name, $last_name, $role, $password, $custom_registration_fields );
						 
		            if ( is_wp_error( $result ) ) {
		                // Parse errors into a string and append as parameter to redirect
		                $errors = join( ',', $result->get_error_codes() );
		                $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
		            } else {
		                // Success, redirect to login page.
		                
		                if($role == 'owner') {
			                	
			                	$redirect_page_id = get_option('listeo_owner_registration_redirect');
			                	
			                	if($redirect_page_id){
								
									$redirect_url = get_permalink($redirect_page_id);
			                	
			                	} else {
			                	
			                		$redirect_url = get_permalink(get_option( 'listeo_profile_page' ));
			                	
			                	}
			                	
			                } else if($role == 'guest') {

			                	$redirect_page_id = get_option('listeo_guest_registration_redirect');
			                	if($redirect_page_id){
									$redirect_url = get_permalink($redirect_page_id);
			                	} else {
			                		$redirect_url = get_permalink(get_option( 'listeo_profile_page' ));
			                	}

			                } else {
			                	$redirect_url = get_permalink(get_option( 'listeo_profile_page' ));	
			                }
		                $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
		            }

				}
	 		}
	        wp_redirect( $redirect_url );
	        exit;
	    }
	}

	/**
	 * Resets the user's password if the password reset form was submitted.
	 */
	public function do_password_reset() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$rp_key = $_REQUEST['rp_key'];
			$rp_login = $_REQUEST['rp_login'];
			$user = check_password_reset_key( $rp_key, $rp_login );
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( 'member-login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( 'member-login?login=invalidkey' ) );
				}
				exit;
			}
			if ( isset( $_POST['pass1'] ) ) {
				if ( $_POST['pass1'] != $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = get_permalink(get_option( 'listeo_reset_password_page' ));
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );
					wp_redirect( $redirect_url );
					exit;
				}
				if ( empty( $_POST['pass1'] ) ) {
					// Password is empty
					$redirect_url = get_permalink(get_option( 'listeo_reset_password_page' ));
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );
					wp_redirect( $redirect_url );
					exit;
				}
				// Parameter checks OK, reset password
				reset_password( $user, $_POST['pass1'] );
				$redirect_url = get_permalink(get_option( 'listeo_reset_password_page' ));
				$redirect_url = add_query_arg( 'password', 'changed', $redirect_url );
				wp_redirect(  $redirect_url );
			} else {
				echo "Invalid request.";
			}
			exit;
		}
	}

	function remove_filter_lostpassword() {
	  remove_filter( 'lostpassword_url', 'wc_lostpassword_url', 10 );
	}





}


/* TODO: move it to the class*/
function listeo_core_avatar_filter() {

  // Add to edit_user_avatar hook
  add_action('edit_user_avatar', array('wp_user_avatar', 'wpua_action_show_user_profile'));
  add_action('edit_user_avatar', array('wp_user_avatar', 'wpua_media_upload_scripts'));
}

// Loads only outside of administration panel
if(!is_admin()) {
  add_action('init','listeo_core_avatar_filter');
}

// Redefine user notification function
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
        $user = new WP_User($user_id);
 
        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
		$user_data = get_userdata( $user_id );
		// Get all the user roles as an array.
		$user_roles = $user_data->roles;
		// Check if the role you're interested in, is present in the array.
		$user_role = '';
		if ( in_array( 'owner', $user_roles, true ) ) {
			$user_role =  esc_html__('owner','listeo_core');
		}
		if ( in_array( 'guest', $user_roles, true ) ) {
			$user_role =  esc_html__('guest','listeo_core');
		}
        $message  = sprintf(__('New user registration on your site %s:','listeo_core'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s','listeo_core'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s','listeo_core'), $user_email) . "\r\n";
        $message .= sprintf(__('Role: %s','listeo_core'), $user_role) . "\r\n";
 
        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration','listeo_core'), get_option('blogname')), $message);
 		
        if ( empty($plaintext_pass) )
            return;

       
		
		if( function_exists('listeo_core_get_option') && get_option('listeo_profile_page',true) ) {
		 	$login_url = get_permalink( listeo_core_get_option( 'listeo_profile_page' ) );
		} else {
		 	$login_url = wp_login_url();
		}
     
 		$user = get_user_by( 'id', $user_id );
 		$mail_args = array(
	        'email'         => $user_email,
	        'login'         => $user_login,
	        'password'      => $plaintext_pass,
	        'first_name' 	=> $user->first_name,
	        'last_name' 	=> $user->last_name,
	        'display_name' 	=> $user->display_name,
	        'login_url' 	=> $login_url,
	        );
	    do_action('listeo_welcome_mail',$mail_args);

       // wp_mail($user_email, sprintf(__('[%s] Your username and password','listeo_core'), get_option('blogname')), $message);
 
    }


}