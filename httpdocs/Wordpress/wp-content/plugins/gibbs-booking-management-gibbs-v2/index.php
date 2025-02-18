<?php
/*
Plugin Name: Gibbs Booking Management v2
Description: Gibbs Booking Management shortcode [booking-management-gibbs-v2]
Version: 3.0.01
Author: gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;



function booking_management_v2($parms){
	if(is_user_logged_in()){

		wp_enqueue_style( 'booking_management-style', plugin_dir_url(__FILE__) . 'css/style.css' ,[],time());
	   // wp_enqueue_style( 'booking_management-datatable-style', plugin_dir_url(__FILE__) . 'css/datatable.min.css' ,[],time());
	    wp_enqueue_style( 'booking_management-jquery-datatable-style', plugin_dir_url(__FILE__) . 'css/jquery.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'booking_management-rowReorder-datatable-style', plugin_dir_url(__FILE__) . 'css/rowReorder.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'booking_management-responsive-datatable-style', plugin_dir_url(__FILE__) . 'css/responsive.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'booking_management-datatable-script', plugin_dir_url(__FILE__) . 'js/datatable.min.js' ,[],time());
	   
	     wp_enqueue_script( 'datatable-jquery', plugin_dir_url(__FILE__).'js/jquery.dataTables.min.js',array(),null,true);
         wp_enqueue_script( 'datatable-bootstrap-js', plugin_dir_url(__FILE__).'js/dataTables.bootstrap4.min.js', array( 'jquery' ),null,true);
         wp_enqueue_script( 'booking_management-responsive-datatable-script', plugin_dir_url(__FILE__) . 'js/dataTables.responsive.min.js' ,[],time());
	     wp_enqueue_script( 'booking_management-rowReorder-datatable-script', plugin_dir_url(__FILE__) . 'js/dataTables.rowReorder.min.js' ,[],time());


	    $page_type = "";
	    $booking_type = "";


	    if(isset($_GET["booking_id"])){

	    	if(isset($parms["type"]) && $parms["type"] == "buyer"){
	    		$page_type = "buyer";
	            require(__DIR__."/single-main.php");  
			}else{
				
		    	$page_type = "owner";

		    	require(__DIR__."/single-main.php");  
			}


	    }else{
	    	if(isset($parms["type"]) && $parms["type"] == "buyer"){
	    		$page_type = "buyer";
	            require(__DIR__."/buyer-main.php");  
			}else{
				$page_type = "owner";
				require(__DIR__."/main.php");
			}
	    }

	    require(__DIR__."/script.php");

		

	    
	}else{

		
		ob_start();
		require(__DIR__."/login.php");
		$string = ob_get_contents();
		ob_end_clean();

		return $string;
	}
	
}
add_shortcode("booking-management-gibbs-v2","booking_management_v2");

require(__DIR__."/save_data.php");

require(__DIR__."/get_data.php");

