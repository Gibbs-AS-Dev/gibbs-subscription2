<?php
/*
 * Plugin Name:       FastBots
 * Plugin URI:        https://wordpress.org/plugins/fastbots/
 * Description: Easily add your FastBots AI chatbot to your WordPress site.
 * Version:           1.0.5
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            FastBots
 * Author URI:        https://fastbots.ai
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       fastbots
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'FASTBOTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once(FASTBOTS_PLUGIN_DIR . 'settings-page.php');

function fastbots_chatbot_to_header() {
    $embed_code = get_option('fastbots_chatbot_embed_code');
	$embed_code_two = get_option('fastbots_chatbot_embed_code_two');
    if (!empty($embed_code)&&!empty($embed_code_two)) {
		$full_embed_code = "<script>
		var scriptElement = document.createElement('script');
	  
		scriptElement.src = '".$embed_code."';
		scriptElement.setAttribute('data-bot-id', '".$embed_code_two."');
		scriptElement.defer = true;
	  
		document.head.appendChild(scriptElement);
	  </script>";
        echo wp_kses(
		    $full_embed_code,
		    array(
		        'script'      => array(
		            'src'  => array(),
		            'data-bot-id' => array(),
		            'defer'  => array(),
		        ),
		    )
		);
    }
}

add_action('wp_head', 'fastbots_chatbot_to_header');
