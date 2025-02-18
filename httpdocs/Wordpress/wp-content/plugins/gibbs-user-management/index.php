<?php
/*
Plugin Name: Gibbs User Management
Description: Gibbs User Management shortcode [user-management-gibbs]
Version: 3.0.01
Author: Gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function loadded(){
	wp_enqueue_style( 'user_management-style', plugin_dir_url(__FILE__) . 'css/style.css' ,[],time());
}
add_action("init","loadded");

function get_product_gross_revenue( $order_id ) {
    global $wpdb;
	global $wp_scripts;
	$version = time();

	foreach ( $wp_scripts->registered as &$regScript ) {
		$version = $regScript->ver;
	}

    return (float) $wpdb->get_var( $wpdb->prepare("
        SELECT SUM(o.product_net_revenue) 
        FROM {$wpdb->prefix}wc_order_product_lookup o 
        WHERE  o.order_id = %d
    ",$order_id ) );
}
function get_total_product( $order_id ) {
    global $wpdb;

    return (float) $wpdb->get_var( $wpdb->prepare("
        SELECT count(o.product_id) 
        FROM {$wpdb->prefix}wc_order_product_lookup o 
             WHERE  o.order_id = %d
    ",$order_id ) );
}

function user_management($parms){
	if(is_user_logged_in()){
		 $type = "";

		if(isset($parms["type"])){
	          $type = $parms["type"];
	    }

		
	    wp_enqueue_style( 'user_management-datatable-style', plugin_dir_url(__FILE__) . 'css/datatable.min.css' ,[],time());
	    wp_enqueue_style( 'user_management-datatable-script', plugin_dir_url(__FILE__) . 'js/datatable.min.js' ,[],time());
	    wp_enqueue_script( 'datatable-jquery', plugin_dir_url(__FILE__).'js/jquery.dataTables.min.js',array(),null,true);
         wp_enqueue_script( 'datatable-bootstrap-js', plugin_dir_url(__FILE__).'js/dataTables.bootstrap4.min.js', array( 'jquery' ),null,true);

	    require(__DIR__."/main.php");
	}else{
		echo __("Please login first!","gibbs");
	}
	
}
add_shortcode("user-management-gibbs","user_management");
require(__DIR__."/save_data.php");

function sub_user_shortcode($parms){
	if(is_user_logged_in()){
		 $type = "";

		if(isset($parms["type"])){
	          $type = $parms["type"];
	    }

		wp_enqueue_style( 'user_management-style', plugin_dir_url(__FILE__) . 'css/style.css' ,[],time());
	    wp_enqueue_style( 'user_management-datatable-style', plugin_dir_url(__FILE__) . 'css/datatable.min.css' ,[],time());
	    wp_enqueue_style( 'user_management-datatable-script', plugin_dir_url(__FILE__) . 'js/datatable.min.js' ,[],time());
	    wp_enqueue_script( 'datatable-jquery', plugin_dir_url(__FILE__).'js/jquery.dataTables.min.js',array(),null,true);
         wp_enqueue_script( 'datatable-bootstrap-js', plugin_dir_url(__FILE__).'js/dataTables.bootstrap4.min.js', array( 'jquery' ),null,true);

	    require(__DIR__."/sub_user/main.php");
	}else{
		echo __("Please login first!","gibbs");
	}
	
}
add_shortcode("sub-user-gibbs","sub_user_shortcode");


require(__DIR__."/sub_user/save_data.php");



