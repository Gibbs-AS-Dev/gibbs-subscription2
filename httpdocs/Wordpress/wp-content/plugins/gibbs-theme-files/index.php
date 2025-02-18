<?php
/*
Plugin Name: Gibbs theme files
Description: Gibbs theme css/js
Version: 3.0.01
Author: gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Enqueue scripts and styles.
 */
function listeo_scripts_plugins() {

	
	$my_theme = wp_get_theme();
	//$ver_num = $my_theme->get( 'Version' );
	$ver_num = 2.5;

	/* wp_register_style( 'gb-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap-grid.css' );
	wp_register_style( 'gb-listeo-woocommerce', plugin_dir_url(__FILE__) . 'css/woocommerce.min.css' );
    wp_register_style( 'gb-listeo-iconsmind', plugin_dir_url(__FILE__) . 'css/icons.css' );
    wp_register_style( 'gb-simple-line-icons', plugin_dir_url(__FILE__) . 'css/simple-line-icons.css' );
    wp_register_style( 'gb-font-awesome-5', plugin_dir_url(__FILE__) . 'css/all.css' );
    wp_register_style( 'gb-font-awesome-5-shims', plugin_dir_url(__FILE__) . 'css/v4-shims.min.css' );
     wp_enqueue_style('gb-parent-style', plugin_dir_url(__FILE__) . 'css/style.css', array('gb-bootstrap', 'gb-listeo-iconsmind', 'gb-listeo-woocommerce'));
    wp_enqueue_style('gb-listeocore-child-style', plugin_dir_url(__FILE__) . 'css/custom.css?l11', array('gb-parent-style'));
    wp_enqueue_script('gb-listeocore-child-script', plugin_dir_url(__FILE__) . 'js/custom.js', array('gb-listeo-custom'));

    wp_dequeue_script('gb-listeo-custom');
    wp_enqueue_script( 'gb-chosen-min', plugin_dir_url(__FILE__) . 'js/chosen.min.js', array( 'jquery' )); */
    /* wp_enqueue_script('jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js', array( 'jquery' ));
    wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js', array( 'jquery' )); */
/*     wp_enqueue_script('gb-listeo-custom', plugin_dir_url(__FILE__) . 'js/parent-custom.js', array('jquery'), '20170821', true);
    wp_enqueue_script('gb-listeocore-child-script', plugin_dir_url(__FILE__) . 'js/custom.js', array('gb-listeo-custom')); */
  //  wp_enqueue_script('listeocore-custom-script', get_template_directory_uri() . '/js/custom.js', array('listeo-custom'));
/*     wp_enqueue_script('gb-listeocore-group-custom-fe-script', plugin_dir_url(__FILE__) . '/js/group-custom-fe.js', array('gb-listeo-custom')); */

	/* changes for gibbs plugins */



/* 	wp_enqueue_style( 'gb-listeo-style', get_stylesheet_uri(), array('gb-bootstrap','gb-font-awesome-5','gb-font-awesome-5-shims','gb-simple-line-icons','gb-listeo-woocommerce'), $ver_num );
	if(get_option('listeo_iconsmind')!='hide'){
		wp_enqueue_style( 'listeo-iconsmind');
	} */
	/* wp_register_style( 'listeo-dark', get_template_directory_uri(). '/css/dark-mode.css' );
	if(get_option('listeo_dark_mode')){
		wp_enqueue_style( 'listeo-dark', get_template_directory_uri(). '/css/dark-mode.css' ,array('listeo-style'));
	} */
	//wp_register_script( 'chosen-min', get_template_directory_uri() . '/js/chosen.min.js', array( 'jquery' ), $ver_num );
	/* wp_register_script( 'gb-select2-min', plugin_dir_url(__FILE__) . 'js/select2.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-counterup-min', plugin_dir_url(__FILE__) . 'js/counterup.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-jquery-scrollto', plugin_dir_url(__FILE__) . 'js/jquery.scrollto.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-datedropper', plugin_dir_url(__FILE__) . 'js/datedropper.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-dropzone', plugin_dir_url(__FILE__) . 'js/dropzone.js', array( 'jquery' ), $ver_num ); 
	
	wp_register_script( 'gb-isotope-min', plugin_dir_url(__FILE__) . 'js/isotope.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-jquery-counterdown-min', plugin_dir_url(__FILE__) . 'js/jquery.countdown.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-magnific-popup-min', plugin_dir_url(__FILE__) . 'js/magnific-popup.min.js', array( 'jquery' ), $ver_num );

	
	wp_register_script( 'gb-quantityButtons', plugin_dir_url(__FILE__) . 'js/quantityButtons.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-rangeslider-min', plugin_dir_url(__FILE__) . 'js/rangeslider.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-timedropper', plugin_dir_url(__FILE__) . 'js/timedropper.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-tooltips-min', plugin_dir_url(__FILE__) . 'js/tooltips.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-waypoints-min', plugin_dir_url(__FILE__) . 'js/waypoints.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-slick-min', plugin_dir_url(__FILE__) . 'js/slick.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-mmenu-min', plugin_dir_url(__FILE__) . 'js/mmenu.min.js', array( 'jquery' ), $ver_num );
	
	wp_register_script( 'gb-moment', plugin_dir_url(__FILE__) . 'js/moment.min.js', array( 'jquery' ), $ver_num );
	wp_register_script( 'gb-daterangerpicker', plugin_dir_url(__FILE__) . 'js/daterangepicker.js', array( 'jquery','moment' ), $ver_num );
 	wp_register_script( 'gb-flatpickr', plugin_dir_url(__FILE__) . 'js/flatpickr.js', array( 'jquery' ), $ver_num );
 	wp_register_script( 'gb-bootstrap-slider-min', plugin_dir_url(__FILE__) . 'js/bootstrap-slider.min.js', array( 'jquery' ), $ver_num ); */

	//wp_enqueue_script( 'chosen-min' );
	/* wp_enqueue_script( 'gb-select2-min' );
	wp_enqueue_script( 'gb-counterup-min' );
	wp_enqueue_script( 'gb-datedropper' );
	wp_enqueue_script( 'gb-dropzone' );
	 */
	
	if ( is_page_template( 'template-comming-soon.php' ) ) {
		wp_enqueue_script( 'jquery-counterdown-min' );
	}
	wp_enqueue_script( 'magnific-popup-min' );

	
/* 	
	wp_enqueue_script( 'mmenu-min' );
	wp_enqueue_script( 'slick-min' );
	wp_enqueue_script( 'quantityButtons' );
	wp_enqueue_script( 'rangeslider-min' );
	wp_enqueue_script( 'timedropper' );
	wp_enqueue_script( 'jquery-scrollto' );
	wp_enqueue_script( 'tooltips-min' );
	wp_enqueue_script( 'waypoints-min' );
	wp_enqueue_script( 'moment' );
	wp_enqueue_script( 'daterangerpicker' );
	wp_enqueue_script( 'bootstrap-slider-min' );
	wp_enqueue_script( 'flatpickr' );
	wp_enqueue_script( 'listeo-custom', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'), '20170821', true ); */


	$open_sans_args = array(
		'family' => 'Open+Sans:500,600,700' // Change this font to whatever font you'd like
	);
	wp_register_style( 'google-fonts-open-sans', add_query_arg( $open_sans_args, "//fonts.googleapis.com/css" ), array(), null );

	$raleway_args = array(
		'family' => 'Source Sans Pro:300,400,500,600,700' // Change this font to whatever font you'd like
	);
	wp_register_style( 'google-fonts-raleway', add_query_arg( $raleway_args, "//fonts.googleapis.com/css" ), array(), null );
	
	wp_enqueue_style( 'google-fonts-raleway' );
	wp_enqueue_style( 'google-fonts-open-sans' );
	
	$convertedData = listeo_date_time_wp_format();

	// add converented format date to javascript
	wp_localize_script( 'listeo-custom', 'wordpress_date_format', $convertedData );


	$ajax_url = admin_url( 'admin-ajax.php', 'relative' );
	wp_localize_script( 'listeo-custom', 'listeo',
    array(
        'ajaxurl' 				=> $ajax_url,
        'theme_url'				=> get_template_directory_uri(),
        )
    );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'init', 'listeo_scripts_plugins' );
