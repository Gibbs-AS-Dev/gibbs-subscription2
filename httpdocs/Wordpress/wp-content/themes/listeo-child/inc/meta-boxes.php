<?php 

	add_action( 'cmb2_admin_init', 'listeo_register_metabox_testimonial' );
	/**
	 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
	 */
	function listeo_register_metabox_testimonial() {
		$prefix = 'listeo_';
		$listeo_testimonials_mb = new_cmb2_box( array(
			'id'            => $prefix . 'testimonial',
			'title'         => esc_html__( 'Additional Informations', 'listeo' ),
			'object_types'  => array( 'testimonial', ), // Post type
			'priority'   => 'high',
		) );
		$listeo_testimonials_mb->add_field( array(
			'name' => esc_html__( 'Company', 'listeo' ),
			'id'   => $prefix . 'pp_company',
			'type' => 'text_medium',
			
		) );	

	}	

	add_action( 'cmb2_admin_init', 'listeo_register_metabox_commingsoon' );
	/**
	 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
	 */
	function listeo_register_metabox_commingsoon() {
			$prefix = 'listeo_comming_soon_';

		$listeo_comming_soon_cmb = new_cmb2_box( array(
			'id'           => 'comming-soon',
			'title'        => esc_html__('Comming Soon Page Options','listeo'),
			'object_types' => array( 'page' ), // post type
			'show_on'      => array( 'key' => 'page-template', 'value' => 'template-comming-soon.php' ),
			'context'      => 'normal', //  'normal', 'advanced', or 'side'
			'priority'     => 'high',  //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
		) );

		$listeo_comming_soon_cmb->add_field( array(
			'name' => esc_html__( 'Background image', 'listeo' ),
			'desc' => esc_html__( 'Set background image for this page', 'listeo' ),
			'id'   => $prefix . 'bg_image',
			'type' => 'file',
		) );
		$listeo_comming_soon_cmb->add_field( array(
			'name' => esc_html__( 'Title', 'listeo' ),
			'desc' => esc_html__( 'Title on the page', 'listeo' ),
			'default' => 'We are launching Listeo soon!',
			'id'   => $prefix . 'title',
			'type' => 'text',
		) );
		$listeo_comming_soon_cmb->add_field( array(
			'name' => esc_html__( 'Date to count to', 'listeo' ),
			'desc' => esc_html__( 'For instructions check documentaiton', 'listeo' ),
			'id'   => $prefix . 'signup_countdown_date',
			'type' => 'text_date',
			// 'timezone_meta_key' => 'wiki_test_timezone',
			'date_format' => 'Y/n/d',
		) );
		$listeo_comming_soon_cmb->add_field( array(
			'name' => esc_html__( 'Sign up form acton', 'listeo' ),
			'desc' => esc_html__( 'For instructions check documentaiton', 'listeo' ),
			'id'   => $prefix . 'signup_form_action',
			'type' => 'text',
		) );		
		$listeo_comming_soon_cmb->add_field( array(
			'name' => esc_html__( 'Sign up hidden input id', 'listeo' ),
			'desc' => esc_html__( 'For instructions check documentaiton', 'listeo' ),
			'id'   => $prefix . 'signup_hidden_id',
			'type' => 'text',
		) );		
		
	}


	add_action( 'cmb2_admin_init', 'listeo_register_metabox_property' );
	function listeo_register_metabox_property() {
		$prefix = 'listeo_';
		
		/* get the registered sidebars */
	    global $wp_registered_sidebars;

	    $sidebars = array();
	    foreach( $wp_registered_sidebars as $id=>$sidebar ) {
	      $sidebars[ $id ] = $sidebar[ 'name' ];
	    }

		/**
		 * Sample metabox to demonstrate each field type included
		 */
		$listeo_property_mb = new_cmb2_box( array(
			'id'            => $prefix . 'property_sb_metabox',
			'title'         => esc_html__( 'Listeo Property Options', 'listeo' ),
			'object_types'  => array( 'property' ), // Post type
			'priority'   => 'high',
		) );

		$listeo_property_mb->add_field( array( 
				'name'    => esc_html__( 'Selected Sidebar', 'listeo' ),
				'id'      => $prefix . 'sidebar_select',
				'type'    => 'select',
				'default' => 'sidebar-property',
				'options' => $sidebars,
			) );
		$listeo_property_mb->add_field( array(
			'name'    => esc_html__( 'Slider Image field', 'listeo' ),
			'desc'    => esc_html__( 'Upload an image that will be used in Properties slider (recomended min 1920px wide). If not set, Post Thumbnail will be used instead.', 'listeo' ),
			'id'      => $prefix . 'slider_property_image',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => false, // Hide the text input for the url
			),

		) );
	}


	add_action( 'cmb2_admin_init', 'listeo_register_metabox_pages' );
	/**
	 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
	 */
	function listeo_register_metabox_pages() {
		$prefix = 'listeo_';

		
		/* get the registered sidebars */
	    global $wp_registered_sidebars;

	    $sidebars = array();
	    foreach( $wp_registered_sidebars as $id=>$sidebar ) {
	      $sidebars[ $id ] = $sidebar[ 'name' ];
	    }

	$listeo_post_mb = new_cmb2_box( array(
			'id'            => $prefix . 'post_metabox',
			'title'         => esc_html__( 'Post Options', 'listeo' ),
			'object_types'  => array( 'post' ), // Post type
			'priority'   => 'high',
		) );

$listeo_post_mb->add_field( array(
			'name'             => esc_html__( 'Post Layout', 'listeo' ),
			'desc'             => esc_html__( 'Select post layout, default is full-width', 'listeo' ),
			'id'               => $prefix . 'page_layout',
			'type'             => 'radio_inline',
			'default'			=> 'right-width',
			'options'          => array(
				'full-width' 		=> esc_html__( 'Full width', 'listeo' ),
				'left-sidebar'   	=> esc_html__( 'Left Sidebar', 'listeo' ),
				'right-sidebar'     => esc_html__( 'Right Sidebar', 'listeo' ),
			),
		) );
		/**
		 * Sample metabox to demonstrate each field type included
		 */
	

	

		
	
	
		
		

	



		global $wpdb;


		/*parallax*/
		$listeo_page_mb->add_field( array(
			'name' => esc_html__( 'Background for header', 'listeo' ),
			'desc' => esc_html__( 'If added, titlebar will use parallax effect', 'listeo' ),
			'id'   => $prefix . 'parallax_image',
			'type' => 'file',
			
		) );
		$listeo_page_mb->add_field( array(
			'name' => esc_html__( 'Overlay color', 'listeo' ),
			'desc' => esc_html__( 'For Parallax or Titlebar section', 'listeo' ),
			'id'   => $prefix . 'parallax_color',
			'type' => 'colorpicker', //
			'default' => '#303133'
			
		) );		


		$listeo_page_mb->add_field( array(
			    'name' 		=> esc_html__( 'Parallax overlay opacity', 'listeo' ),
			    'desc'        => esc_html__( 'Set your value', 'listeo' ),
			    'id'          => $prefix . 'parallax_opacity',
			    'type'        => 'own_slider',
			    'min'         => '0',
			    'max'         => '1',
			    'step'        => '0.01',
			    'default'     => '0.6', // start value
			    'value_label' => 'Opacity Value:',
		) );
/* eof parallax*/

/* video */ 

		


	
		





?>