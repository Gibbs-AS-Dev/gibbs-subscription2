<?php
/*
Plugin Name: Gibbs Access Management v2
Description: Gibbs Access Management shortcode [gibbs-access-management-v2]
Version: 3.0.01
Author: gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;



function access_management_v2(){
	if(is_user_logged_in()){

		wp_enqueue_style( 'access_management-style', plugin_dir_url(__FILE__) . 'css/style.css' ,[],time());
	   // wp_enqueue_style( 'booking_management-datatable-style', plugin_dir_url(__FILE__) . 'css/datatable.min.css' ,[],time());
	    wp_enqueue_style( 'access_management-jquery-datatable-style', plugin_dir_url(__FILE__) . 'css/jquery.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'access_management-rowReorder-datatable-style', plugin_dir_url(__FILE__) . 'css/rowReorder.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'access_management-responsive-datatable-style', plugin_dir_url(__FILE__) . 'css/responsive.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'access_management-datatable-script', plugin_dir_url(__FILE__) . 'js/datatable.min.js' ,[],time());
	   
	    wp_enqueue_script( 'datatable-jquery', plugin_dir_url(__FILE__).'js/jquery.dataTables.min.js',array(),null,true);
        wp_enqueue_script( 'datatable-bootstrap-js', plugin_dir_url(__FILE__).'js/dataTables.bootstrap4.min.js', array( 'jquery' ),null,true);
        wp_enqueue_script( 'access_management-responsive-datatable-script', plugin_dir_url(__FILE__) . 'js/dataTables.responsive.min.js' ,[],time());
	    wp_enqueue_script( 'access_management-rowReorder-datatable-script', plugin_dir_url(__FILE__) . 'js/dataTables.rowReorder.min.js' ,[],time());


	    ob_start();
		
		require(__DIR__."/main.php");

	    require(__DIR__."/script.php");
        return ob_get_clean();

		

	    
	}else{
		echo __("Please login first!","gibbs");
	}
	
}
add_shortcode("gibbs-access-management-v2","access_management_v2");

require(__DIR__."/save_data.php");

add_action( 'init', array( 'Access_management_v2', 'action_init' ) );


/*require(__DIR__."/save_data.php");

require(__DIR__."/get_data.php");*/

