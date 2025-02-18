<?php
/*
Plugin Name: Gibbs Find Access Key
Description: This plugin allows guests to enter a booking ID, and receive the access key for a lock.
Version: 1.0
Author: Geir Vidar Kristensen
*/

// Prevent direct access to the plugin. It has to appear on a page.
if (!defined('ABSPATH'))
{
    exit;
}

class FindAccessKey
{
    public function __construct()
    {
        add_action('init', array($this, 'initialise_component'));
        add_action('wp_enqueue_scripts', array($this, 'load_scripts_and_style'));
        global $wp_scripts;
	    $version = time();
	    foreach ( $wp_scripts->registered as &$regScript ) {
		$version = $regScript->ver;
	}
    }

    public function initialise_component()
    {
        define('RESERVATION_ID', 'reservationId');
        define('HTML_PATH', dirname(__FILE__) . '/html/');
        define('SCRIPT_PATH', plugin_dir_url(__FILE__) . 'script/');
        define('STYLE_PATH', plugin_dir_url(__FILE__) . 'css/');
        add_shortcode('gibbs_find_access_key', array($this, 'render_component'));
    }
    
    public function load_scripts_and_style()
    {
        wp_enqueue_script('gibbs-find-access-key-script', SCRIPT_PATH . 'find-access-key.js', [], null, []);
        wp_enqueue_style('access_key_style', STYLE_PATH . 'style.css', [], time());
    }

    public function render_component()
    {
        // Read the reservation ID submitted from the client, if present.
        $reservationId = '';
        $submitButtonStatus = ' disabled';
        if (isset($_POST[RESERVATION_ID]))
        {
            $reservationId = sanitize_text_field($_POST[RESERVATION_ID]);
            $submitButtonStatus = '';
        }

        // Display the form that allows the user to enter the reservation ID.
        require_once HTML_PATH . 'display-form.php';

        // Display search results, if any.
        $this->find_access_key($reservationId);
    }

    // Look for an access key based on the given reservation ID, and display the results. If no reservation ID
    // is given, display nothing.
    public function find_access_key($reservationId)
    {
        global $wpdb;
    
        if (!empty($reservationId))
        {
            $query = "SELECT access_code FROM {$wpdb->prefix}access_management WHERE order_id = %s";

            // Perform the database query.
            $results = $wpdb->get_results($wpdb->prepare($query, $reservationId));
        
            if (empty($results))
            {
                // No results were found.
                require_once HTML_PATH . 'display-access-key-not-found.php';
            }
            else
            {
                // Merge all located keys to a comma separated list. There should only be one, anyway.
                $access_keys = array();
                foreach ($results as $result)
                {
                    $access_keys[] = $result->access_code;
                }
                $access_keys = implode(', ', $access_keys);
                require_once HTML_PATH . 'display-access-key.php';
            }
        }
    }
}

new FindAccessKey;
?>
