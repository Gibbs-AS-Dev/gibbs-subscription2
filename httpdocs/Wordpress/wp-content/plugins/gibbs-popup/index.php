<?php
/*
Plugin Name: Gibbs Popup
Description: Gibbs User Management shortcode [gibbs-popup]
Version: 3.0.01
Author: Gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;




add_action('wp_enqueue_scripts','gibbs_popup_script');

function gibbs_popup_script() {
	$random = rand(0,10000);

    wp_enqueue_script( 'gibbs-popup', plugin_dir_url(__FILE__).'js/script.js?v='.$random,array(),null,true);
    wp_enqueue_style( 'gibbs-popup-css', plugin_dir_url(__FILE__).'css/gibbs_popup.css?v='.$random,array(),null,false);

    wp_localize_script( 'gibbs-popup', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

function gibbs_popup_func($parms){

	$page_id = $parms["page_id"];

	require(__DIR__."/main.php");
	
}
add_shortcode("gibbs-popup","gibbs_popup_func");

function get_popup(){
  echo do_shortcode("[gibbs-popup page_id='".$_POST['page_id']."']");
    exit();
}

add_action('wp_ajax_get_popup', 'get_popup', 10);
add_action('wp_ajax_nopriv_get_popup', 'get_popup', 10);




