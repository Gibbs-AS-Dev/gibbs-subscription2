<?php
/*
Plugin Name: Gibbs Giftcard
Description: Gibbs Giftcard (shortcodes [giftcard_create])
Version: 1.0.0
Author: Gibbs team
License: GPLv2 or later
Text Domain: gibbs
*/

define( 'GIBBS_GIFT_VERSION', '1.0.0' );
define( 'GIBBS_GIFT_URL', plugin_dir_url( __FILE__ ) );
define( 'GIBBS_GIFT_PATH', dirname( __FILE__ ) . '/' );
define( 'GIBBS_GIFT_BASENAME', plugin_basename( __FILE__ ) );

// Include the main giftcard class
require_once GIBBS_GIFT_PATH . 'includes/class-gibbs-giftcard.php';
add_action( 'init', array( new Class_Gibbs_Giftcard(), 'action_init' ) );

// Include the admin class
require_once GIBBS_GIFT_PATH . 'includes/class-gibbs-giftcard-admin.php';

// Initialize the admin class only in the admin area
add_action( 'admin_init', function() {
    new Class_Gibbs_Giftcard_Admin();
});
