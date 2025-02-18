<?php
/*
Plugin Name: Gibbs Login/Register
Plugin URI: http://gibbs.no
Description: Custom login and registration forms with shortcodes. (Custom class for popup "login_reg_popup")
Version: 1.0
Author: Your Name
Author URI: http://gibbs.no
*/

define( 'GIBBS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GIBBS_PLUGIN_PATH', dirname( __FILE__ ) . '/' );
define( 'GIBBS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );


function gibbs_login_form_shortcode($atts) {
    
    $redirect = "";
    if(isset($atts["redirect"])){
        $redirect = $atts["redirect"];
    }
    if(is_user_logged_in() && !is_admin() && !defined('REST_REQUEST') || !REST_REQUEST ){
        wp_redirect($redirect);
        exit;
        return;
    }

    // Start output buffering
    ob_start();
    include "templates/login.php";
    // Return the content
    return ob_get_clean();
}
add_shortcode('gibbs_login', 'gibbs_login_form_shortcode');

function gibbs_register_form_shortcode($atts) {
    if(is_user_logged_in()){
        return;
    }
    $redirect = "";
    if(isset($atts["redirect"])){
        $redirect = $atts["redirect"];
    }

    // Start output buffering
    ob_start();
    include "templates/register.php";
    // Return the content
    return ob_get_clean();
}
add_shortcode('gibbs_register', 'gibbs_register_form_shortcode');

function gibbs_reg_login_form_shortcode($atts) {
    if(is_user_logged_in()){
        return;
    }

    $popup =false;

    if(isset($atts["popup"]) && $atts["popup"] == "true"){

        $popup = true;

    }
    $redirect = "";
    if(isset($atts["redirect"])){
        $redirect = $atts["redirect"];
    }

    // Start output buffering
    ob_start();
    if($popup == true){
        include "templates/reg-login-popup.php";
    }else{
        include "templates/reg-login.php";
    }
    // Return the content
    return ob_get_clean();
}
add_shortcode('gibbs_register_login', 'gibbs_reg_login_form_shortcode');

require_once GIBBS_PLUGIN_PATH.'includes/gibbs-reg-login.php';

add_action( 'init', array( 'Gibbs_Register_Login', 'action_init' ) );


