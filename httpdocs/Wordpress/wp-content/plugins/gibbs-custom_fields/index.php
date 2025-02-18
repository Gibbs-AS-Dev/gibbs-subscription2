<?php
/*
Plugin Name: Gibbs Custom fields 
Description: Gibbs Application Advanced Fields shortcode [application-advanced-fields]
Version: 3.0.01
Author: gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;

global $app_db_version;
$app_db_version = '2.0';

function application_fields_install() {
	global $wpdb;
	global $app_db_version;

	$table_name = $wpdb->prefix . 'application_fields';
	
	$charset_collate = $wpdb->get_charset_collate();
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) 
    {


		$sql = "CREATE TABLE $table_name (
			id bigint(22) NOT NULL AUTO_INCREMENT,
			group_id bigint(22) NOT NULL,
			json_data  LONGTEXT  NOT NULL,
			created_by  bigint(22)  NOT NULL,
			status  ENUM('0', '1') Default '1',
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$dbb = dbDelta( $sql );
		add_option( 'app_db_version', $app_db_version );
	}	
}
register_activation_hook( __FILE__, 'application_fields_install' );


function application_advanced_fields_func($parms){

	wp_enqueue_style( 'application_form-style', plugin_dir_url(__FILE__) . '/css/style.css' ,[],time());


	if(is_user_logged_in()){


		$active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );

		if(!empty($active_group_id) && $active_group_id != ""){

			wp_enqueue_style( 'application_list-jquery-datatable-style', plugin_dir_url(__FILE__) . '/css/jquery.dataTables.min.css' ,[],time());
		    wp_enqueue_style( 'application_list-rowReorder-datatable-style', plugin_dir_url(__FILE__) . '/css/rowReorder.dataTables.min.css' ,[],time());
		    wp_enqueue_style( 'application_list-responsive-datatable-style', plugin_dir_url(__FILE__) . '/css/responsive.dataTables.min.css' ,[],time());
		    wp_enqueue_style( 'application_list-datatable-script', plugin_dir_url(__FILE__) . '/js/datatable.min.js' ,[],time());
		   
		     wp_enqueue_script( 'application_list-datatable-jquery', plugin_dir_url(__FILE__).'/js/jquery.dataTables.min.js',array(),null,true);
		     wp_enqueue_script( 'application_list-datatable-bootstrap-js', plugin_dir_url(__FILE__).'/js/dataTables.bootstrap4.min.js', array( 'jquery' ),null,true);
		     wp_enqueue_script( 'application_list-responsive-datatable-script', plugin_dir_url(__FILE__) . '/js/dataTables.responsive.min.js' ,[],time());
		     wp_enqueue_script( 'application_list-rowReorder-datatable-script', plugin_dir_url(__FILE__) . '/js/dataTables.rowReorder.min.js' ,[],time());

			require(__DIR__."/main.php");

		}else{


            $message =  __("No active group!","gibbs");
    	    require_once("noData.php");

		}



		

	    
	}else{

		$message =  __("Please login!","gibbs");
    	require_once("noData.php");
		
	}
	
}

add_shortcode("application-advanced-fields","application_advanced_fields_func");

require(__DIR__."/save_fields_data.php");