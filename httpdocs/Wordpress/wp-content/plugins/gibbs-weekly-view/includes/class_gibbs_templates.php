<?php
/**
 * Template loader for PW Sample Plugin.
 *
 * Only need to specify class listings here.
 *
 */

class Listeo_Gibbs_Template_Loader extends Gamajo_Template_Loader {

	protected $filter_prefix = 'listeo_core';

	protected $theme_template_directory = 'listeo-core-custom';

	protected $plugin_directory = GIBBS_PLUGIN_DIR;

	protected $plugin_template_directory = 'templates';
 	
}


?>