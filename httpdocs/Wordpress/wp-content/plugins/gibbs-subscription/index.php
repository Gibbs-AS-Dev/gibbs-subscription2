<?php
/*
Plugin Name: Gibbs Subscription Stripe
Description: Gibbs Subscription Stripe
Version: 1.0.0
Author: sk
License: GPLv2 or later
Text Domain: gibbs
*/

define( 'GIBBS_STRIPE_VERSION', '1.0.0' );
define( 'GIBBS_STRIPE_URL', plugin_dir_url( __FILE__ ) );
define( 'GIBBS_STRIPE_PATH', dirname( __FILE__ ) . '/' );
define( 'GIBBS_STRIPE_BASENAME', plugin_basename( __FILE__ ) );

// Include the main subscription class
require_once GIBBS_STRIPE_PATH . 'includes/class-gibbs-subscription.php';
add_action( 'init', array( new Class_Gibbs_Subscription(), 'action_init' ) );

// Include the admin class
require_once GIBBS_STRIPE_PATH . 'includes/class-gibbs-subscription-admin.php';

// Initialize the admin class only in the admin area
add_action( 'admin_init', function() {
    new Class_Gibbs_Subscription_Admin();
});
