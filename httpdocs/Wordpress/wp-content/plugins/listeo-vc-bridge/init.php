<?php 
/*
Plugin Name: Listeo VC Bridge
Plugin URI:
Description: Adds Visual Composer compatibiliy to Listeo theme..
Version: 3.0.01
Author: Purethems.net
Author URI: http://purethemes.net
*/

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }
 
add_action('init', 'run_listeo_vc');

function run_listeo_vc(){
if( defined( 'WPB_VC_VERSION' ) )   require 'vc.php';
}

?>