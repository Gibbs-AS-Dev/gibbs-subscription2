<?php

class Gibbs_Register_Login
{

    public static function action_init()
    {

        add_action('wp_enqueue_scripts', array('Gibbs_Register_Login', 'enqueue_scripts'));

        add_action('wp_ajax_gibbsajaxlogin', array('Gibbs_Register_Login', 'gibbsajaxlogin'));
        add_action('wp_ajax_nopriv_gibbsajaxlogin', array('Gibbs_Register_Login', 'gibbsajaxlogin'));


        add_action('wp_ajax_gibbsajaxregister', array('Gibbs_Register_Login', 'gibbsajaxregister'));
        add_action('wp_ajax_nopriv_gibbsajaxregister', array('Gibbs_Register_Login', 'gibbsajaxregister'));

        add_action('wp_ajax_send_login_code', array('Gibbs_Register_Login', 'send_login_code'));
        add_action('wp_ajax_nopriv_send_login_code', array('Gibbs_Register_Login', 'send_login_code'));

        add_action('wp_ajax_login_with_code', array('Gibbs_Register_Login', 'login_with_code'));
        add_action('wp_ajax_nopriv_login_with_code', array('Gibbs_Register_Login', 'login_with_code'));



    }

    public function login_with_code(){

        // Get user input
        $username_or_email = sanitize_text_field($_POST['input']);
        $login_code = sanitize_text_field($_POST['login_code']);

        // Check if it's email or username
        if (is_email($username_or_email)) {
            $user = get_user_by('email', $username_or_email);
        } else {
            $user = get_user_by('login', $username_or_email);
        }

        if (!$user) {
            wp_send_json_error(['message' => __('Vi fant ingen bruker registrert med den e-posten. Vennligst prøv en annen e-postadresse, eller opprett en ny konto', 'textdomain')]);
        }

        // Get stored code and expiration
        $stored_code = get_user_meta($user->ID, 'login_code', true);
        $expiration = get_user_meta($user->ID, 'login_code_expiration', true);

        if (!$stored_code || time() > $expiration) {
            wp_send_json_error(['message' => __('Engangskoden har utløpt. Vennligst send ny kode', 'textdomain')]);
        }

        if ((int)$login_code !== (int)$stored_code) {
            wp_send_json_error([
                'message' => __(
                    'Ugyldig engangskode. <a href="#" onclick="location.reload();" style="font-weight: bold; text-decoration: underline;"><br>Trykk her for å prøve på nytt</a>', 
                )
            ]);
        }
        
        
        

        // Authenticate and log in the user
        wp_set_current_user($user->ID); // set the current wp user
        wp_set_auth_cookie($user->ID);
        wp_send_json_success(['message' => __('Vellykket!', 'textdomain')]);

    }
    public function send_login_code(){

        $input = sanitize_text_field($_POST['input']);

        if (empty($input)) {
            wp_send_json_error(['message' => __('Please provide an email or username.', 'textdomain')]);
        }

        // Find the user by email or username
        if (is_email($input)) {
            $user = get_user_by('email', $input);
        } else {
            $user = get_user_by('login', $input);
        }

        if (!$user) {
            wp_send_json_error(['message' => __('Vi fant ingen bruker registrert med den e-posten. Vennligst prøv en annen e-postadresse, eller opprett en ny konto', 'textdomain')]);
        }


        // Generate a random login code
        $login_code = wp_rand(100000, 999999);

        // Store the code in user meta with a short expiration time (5 minutes)
        update_user_meta($user->ID, 'login_code', $login_code);
        update_user_meta($user->ID, 'login_code_expiration', time() + 300);


        $subject = __('Din engangskode', 'textdomain');
        $message = sprintf(
            __(
                "<div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>Hei %s,</h2>
                    <p>Her er din engangskode:</p>
                    <p style='font-size: 18px; font-weight: bold; color: #008474;'>%s</p>
                    <p>Koden er gyldig i 5 minutter.</p>
                    <p>Vennlig hilsen,<br>Gibbs-teamet</p>
                </div>",
                'textdomain'
            ),
            $user->display_name,
            $login_code
        );
        $headers = ['Content-Type: text/html; charset=UTF-8'];



        if (wp_mail($user->user_email, $subject, $message, $headers)) {
            wp_send_json_success(['message' => __('Login code sent successfully.', 'textdomain')]);
        } else {
            wp_send_json_error(['message' => __('Failed to send the email. Please try again.', 'textdomain')]);
        }

    }

    public static function enqueue_scripts()
    {
        global $wp_scripts;
        $version = time();

        foreach ( $wp_scripts->registered as &$regScript ) {
            $version = $regScript->ver;
        }
        wp_enqueue_style('gibbs-login-register', GIBBS_PLUGIN_URL . 'css/login-register.css', [],$version);
        wp_enqueue_style('gibbs-login-register-jss', GIBBS_PLUGIN_URL . 'js/login-register.js', [],$version);
        
     
    }

    protected function sanitize_posted_field( $value ) {
        // Santize value
        $value = is_array( $value ) ? array_map( array( "Gibbs_Register_Login", 'sanitize_posted_field' ), $value ) : sanitize_text_field( stripslashes( trim( $value ) ) );

        return $value;
    }

    protected function get_posted_field( $key, $field ) {
        
        return isset( $_POST[ $key ] ) ? Gibbs_Register_Login::sanitize_posted_field( $_POST[ $key ] ) : '';
    }

    public function gibbsajaxregister()
    {

        if ( !get_option('users_can_register') ) :
                        echo json_encode(
                        array(
                            'registered'=>false, 
                            'message'=> esc_html__( 'Registration is disabled', 'listeo_core' ),
                        )
                    );
                    die();
        endif;

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

        $email_arr = explode('@', $email);
        $user_login = $email;

        if(empty($user_login)) {
            echo json_encode(
                array(
                    'registered'=>false, 
                    'message'=> esc_html__( 'Please provide your username', 'listeo_core' )
                )
            );
            die();
        }           

        if(isset($_POST["country_code"]) && $_POST["country_code"] != ""){
            $_POST["country_code"] = "+".$_POST["country_code"];
        }
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name  = ! empty($_POST['last_name']) ? sanitize_text_field( $_POST['last_name'] ) : '';



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
         
        $role = get_option('default_role');

        
        if (!in_array($role, array('owner', 'guest'))) {
            $role = get_option('default_role');
        }

        
       
        $result = Gibbs_Register_Login::register_user( $email, $user_login, $first_name, $last_name, $role, $password );

        if ( is_wp_error($result) ){
              echo json_encode(array('registered'=>false, 'message'=> $result->get_error_message()));
        } else {

            echo json_encode(array('registered'=>true, 'message'=>esc_html__('Velkommen til gibbs! Du vil nå bli innlogget','listeo_core')));
            
        }
        die();
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

    private function register_user( $email, $user_login, $first_name, $last_name, $role, $password) {
        $errors = new WP_Error();


     
        // Email address is used as both username and email. It is also the only
        // parameter we need to validate
        if ( ! is_email( $email ) ) {
            $errors->add( 'email', Gibbs_Register_Login::get_error_message( 'email' ) );
            return $errors;
        }
     
        if ( email_exists( $email ) ) {
            $errors->add( 'email_exists', Gibbs_Register_Login::get_error_message( 'email_exists') );
            return $errors;
        }

        if ( username_exists( $user_login ) ) {
            $errors->add( 'username_exists', Gibbs_Register_Login::get_error_message( 'username_exists') );
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
            'role'          => $role
        );
     
        $user_id = wp_insert_user( $user_data );
        
        

        if(isset($_POST["country_code"])){
            update_user_meta( $user_id, 'country_code',$_POST["country_code"] );
        }
        if(isset($_POST["phone"])){
            update_user_meta( $user_id, 'phone',$_POST["phone"] );
        }
        


        // if(isset($custom_registration_fields) && !empty($custom_registration_fields)){
        //  foreach ($custom_registration_fields as $key => $field) {




        //      listeo_write_log($field);
        //      update_user_meta( $user_id, $field['name'], $field['value'] );
        //  }
        // }
        if ( ! is_wp_error( $user_id ) ) {
            wp_new_user_notification( $user_id, $password,'both' );
            wp_set_current_user($user_id); // set the current wp user
            wp_set_auth_cookie($user_id);
            
        }
        
     
        return $user_id;
    }

    public function gibbsajaxlogin()
    {

            // Nonce is checked, get the POST data and sign user on
            $creds = array();
            $creds['user_login']    = !empty( $_POST['username'] ) ? $_POST['username'] : null;
            $creds['user_password'] = !empty( $_POST['password'] ) ? $_POST['password'] : null;
            $creds['remember']      = !empty( $_POST['remember-me'] ) ? $_POST['remember-me'] : null;

            if(empty($creds['user_login'])) {
                 echo json_encode(
                    array(
                        'loggedin'=>false, 
                        'message'=> esc_html__( 'Vennligst skriv inn din epost', 'listeo_core' )
                    )
                 );
                 die();
            }
            if(empty($creds['user_password'])) {
                 echo json_encode(array('loggedin'=>false, 'message'=> esc_html__( 'Vennligst skriv inn et passord', 'listeo_core' )));
                 die();
            }

            $user_signon = wp_signon( $creds, false );
            if ( is_wp_error($user_signon) ){
                
                echo json_encode(
                    array(
                        'loggedin'=>false, 
                        'message'=>esc_html__('Feil epost/brukernavn eller passord','listeo_core')
                    )
                );

            } else {
                $userMeta = get_user_meta ( $user_signon->ID, 'ptn_capabilities', true);
                
                echo json_encode(

                    array(
                        'loggedin'  =>  true, 
                        'message'   =>  esc_html__('Vellykket :)','listeo_core'),
                        'userData' => $user_signon,
                        'role' => $userMeta,
                    )

                );
            }



            die();
    }



}    