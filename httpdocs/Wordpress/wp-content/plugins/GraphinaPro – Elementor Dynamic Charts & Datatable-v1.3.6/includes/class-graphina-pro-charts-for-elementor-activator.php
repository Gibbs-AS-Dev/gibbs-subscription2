<?php

/**
 * Fired during plugin activation
 *
 * @link       https://iqonic.design
 * @since      1.2.4
 *
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.2.4
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 * @author     Iqonic Design < hello@iqonic.design>
 */
class Graphina_Pro_Charts_For_Elementor_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.2.4
	 */
	public static function activate() {
        update_option('graphina_pro_is_install','1',true);
        update_option('graphina_is_activate','1',true);
        return graphina_pro_make_lite_version();
	}

}
