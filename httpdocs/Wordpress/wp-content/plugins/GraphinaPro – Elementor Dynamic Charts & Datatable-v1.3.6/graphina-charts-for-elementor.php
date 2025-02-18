<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin line. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://iqonic.design
 * @since             1.2.4
 * @package           Graphina_Pro_Charts_For_Elementor
 *
 * @wordpress-plugin
 * Plugin Name:       GraphinaPro – Elementor Dynamic Charts & Datatable
 * Plugin URI:        https://iqonicthemes.com
 * Description:       Your ultimate charts and graphs solution to enhance visual effects. Create versatile, advanced and interactive charts on your website.
 * Version: 3.0.01
 * Author:            Iqonic Design
 * Author URI:        https://iqonic.design/
 * Text Domain:       graphina-pro-charts-for-elementor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if (!defined('GRAPHINA_PRO_ROOT'))
    define('GRAPHINA_PRO_ROOT', plugin_dir_path(__FILE__));

if (!defined('GRAPHINA_PRO_URL'))
    define('GRAPHINA_PRO_URL', plugins_url('', __FILE__));

if (!defined('GRAPHINA_PRO_BASE_PATH'))
    define('GRAPHINA_PRO_BASE_PATH', plugin_basename(__FILE__));

if (!defined('GRAPHINA_LITE_CURRENT_VERSION'))
    define('GRAPHINA_LITE_CURRENT_VERSION', '1.7.5');

/**
 * Currently plugin version.
 * Start at version 1.2.4 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('GRAPHINA_PRO_CHARTS_FOR_ELEMENTOR_VERSION', '1.3.6');

// Require once the Composer Autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
} else {
    die('Something went wrong');
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-graphina-pro-charts-for-elementor-activator.php
 */
function Graphina_Pro_Charts_For_Elementor_activate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-graphina-pro-charts-for-elementor-activator.php';
    Graphina_Pro_Charts_For_Elementor_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-graphina-pro-charts-for-elementor-deactivator.php
 */
function Graphina_Pro_Charts_For_Elementor_deactivate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-graphina-pro-charts-for-elementor-deactivator.php';
    Graphina_Pro_Charts_For_Elementor_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'Graphina_Pro_Charts_For_Elementor_activate');
register_deactivation_hook(__FILE__, 'Graphina_Pro_Charts_For_Elementor_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-graphina-pro-charts-for-elementor.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.2.4
 */
function Graphina_Pro_Charts_For_Elementor_run()
{
    $plugin = new Graphina_Pro_Charts_For_Elementor();
    $plugin->run();
}
Graphina_Pro_Charts_For_Elementor_run();

/**
 * Notice
 */

add_action('admin_notices', function () {
    if (function_exists('graphina_pro_if_failed_load')) {
        graphina_pro_if_failed_load();
    }
});

add_action('init',function(){
    if (!function_exists('get_plugins')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $status = false ;
    $plugins = get_plugins();
    $basename = '';
    foreach ($plugins as $key => $value) {
        if($value['TextDomain'] === 'graphina-charts-for-elementor') {
            $basename = $key;
            if(is_plugin_active($key)) {
                $status  = true ;
            }
        }
    }
    if(!$status){
        deactivate_plugins(  GRAPHINA_PRO_BASE_PATH);
    }
    if (!defined('GRAPHINA_LITE_URL'))
        define('GRAPHINA_LITE_URL', plugin_dir_url($basename));
});

