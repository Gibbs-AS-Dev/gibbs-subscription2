<?php
/*
Plugin Name: Gibbs Admin Calendar v 2.6.8
Description: Gibbs Admin calendar. Each calendar version has a separate shortcode
Version: 3.0.02
Author: gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

define( 'GIBBS_CALENDAR_VERSION', GIBBS_VERSION );
define( 'GIBBS_CALENDAR_URL', plugin_dir_url( __FILE__ ) );
define( 'GIBBS_CALENDAR_PATH', dirname( __FILE__ ) . '/' );
define( 'GIBBS_CALENDAR_BASENAME', plugin_basename( __FILE__ ) );

require_once GIBBS_CALENDAR_PATH . 'includes/script/class-gibbs-script-calendar.php';
require_once GIBBS_CALENDAR_PATH . 'includes/common/class-gibbs-common-calendar.php';

require_once GIBBS_CALENDAR_PATH . 'includes/class-gibbs-admin-calendar-logger.php';
require_once GIBBS_CALENDAR_PATH . 'includes/class-gibbs-admin-calendar-utility.php';
require_once GIBBS_CALENDAR_PATH . 'includes/class-gibbs-admin-calendar-setup.php';
require_once GIBBS_CALENDAR_PATH . 'includes/class-gibbs-admin-calendar-api.php';

require_once GIBBS_CALENDAR_PATH . 'includes/season/class-gibbs-season-calendar-setup.php';
require_once GIBBS_CALENDAR_PATH . 'includes/season/class-gibbs-season-calendar-utility.php';
require_once GIBBS_CALENDAR_PATH . 'includes/season/class-gibbs-season-calendar-api.php';

// Setup the app
add_action( 'init', array( 'Gibbs_Script_Calendar', 'action_init' ) );
add_action( 'init', array( 'Gibbs_Admin_Calendar_Setup', 'action_init' ) );
add_action( 'init', array( 'Gibbs_Season_Calendar_Setup', 'action_init' ) );

// Init the APIs
add_action( 'init', array( 'Gibbs_Admin_Calendar_API', 'action_init' ) );
add_action( 'init', array( 'Gibbs_Season_Calendar_API', 'action_init' ) );

if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}


