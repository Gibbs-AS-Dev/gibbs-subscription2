<?php
/**
 * Awesomesauce class.
 *
 * @category   Class
 * @package    ElementorAwesomesauce
 * @subpackage WordPress
 * @author     Ben Marshall <me@benmarshall.me>
 * @copyright  2020 Ben Marshall
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       link(https://www.benmarshall.me/build-custom-elementor-widgets/,
 *             Build Custom Elementor Widgets)
 * @since      1.0.0
 * php version 7.3.9
 */

namespace ElementorListeo\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Awesomesauce widget class.
 *
 * @since 1.0.0
 */
class HomeBanner extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'listeo-homebanner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Home Banner', 'listeo_elementor' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-palette';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'listeo' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
 // 'title' 		=> 'Service Title',
	// 	    'url' 			=> '',
	// 	    'url_title' 	=> '',

	// 	   	'icon'          => 'im im-icon-Office',
	// 	    'type'			=> 'box-1', // 'box-1, box-1 rounded, box-2, box-3, box-4'
	// 	    'with_line' 	=> 'yes',


		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'listeo_elementor' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'listeo_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Find Nearby Attractions', 'listeo_elementor' ),
			)
		);	
		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Subtitle', 'listeo_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Explore top-rated attractions, activities and more!', 'listeo_elementor' ),
			)
		);	
		$this->add_control(
			'search_form_style',
			[
				'label' => __( 'Search form style ', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'wide',
				'options' => [
					'wide' => __( 'Wide', 'listeo_elementor' ),
					'boxed' => __( 'Boxed', 'listeo_elementor' ),
					
				],
			]
		);			

		$this->add_control(
			'header_style',
			[
				'label' => __( 'Header style ', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'transparent-header',
				'options' => [
					'transparent-header' => __( 'Transparent', 'listeo_elementor' ),
					'solid-header' => __( 'Solid', 'listeo_elementor' ),
					
				],
			]
		);	

		$this->add_control(
			'home_banner_text_align',
			[
				'label' => __( 'Text alignment ', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'center' => __( 'Center', 'listeo_elementor' ),
					'left' 	 => __( 'Left', 'listeo_elementor' ),
					
				],
				'selectors' => [
				    '{{WRAPPER}} .main-search-inner' => 'text-align: {{VALUE}}'
				],
				'condition' => [
						'search_form_style' => 'wide',
				],
							
			]
		);
		$this->add_control(
			'background',
			[
				'label' => __( 'Choose Background Image', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				
			]
		);
		$this->add_control(
			'video',
			[
				'label' => __( 'Choose Video', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				
			]
		);
		$this->add_control(
			'video_poster',
			[
				'label' => __( 'Choose Video Poster Image', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				]
			]
		);

		
		$this->add_control(
			'solid_background',
			[
				'label' => __( 'Solid Background Color', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'solid' => __( 'Solid color', 'listeo_elementor' ),
					'gradient' 	 => __( 'Gradient', 'listeo_elementor' ),
					
				],
				'condition' => [
					'header_style' => 'transparent-header'
				]
							
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => __( 'Overlay on homepage search banner', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'alpha' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .main-search-container:before' => 'background: linear-gradient(to right, {{VALUE}}F2 20%, {{VALUE}}B3 70%, {{VALUE}}00 95%)',
				],
			]
		);	


		// $this->add_control(
		// 	'home_full_screen',
		// 	[
		// 		'label' => __( 'Full screen banner', 'listeo_elementor'  ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label_on' => __( 'Show', 'listeo_elementor' ),
		// 		'label_off' => __( 'Hide', 'listeo_elementor' ),
		// 		'return_value' => 'yes',
		// 		'default' => 'no',
		// 	]
		// );
		$this->add_control(
			'featured_categories_status',
			[
				'label' => __( 'Show Featured Categories', 'listeo_elementor'  ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'listeo_elementor' ),
				'label_off' => __( 'Hide', 'listeo_elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);


		$this->add_control(
			'tax-listing_category',
			[
				'label' => __( 'Show in Featured Categories this terms', 'listeo_elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => [],
				'options' => $this->get_terms('listing_category'),
				'condition' => [
						'featured_categories_status' => 'yes',
				],
			]
		);				



		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'subtitle', 'none' );

		if(!empty($settings['background']['url'])){

			$background_image = $settings['background']['url'];
		} else {
			$background_image = get_option( 'listeo_search_bg'); 
		}

		$video = false;

		if(isset($settings['video']['url']) && !empty($settings['video']['url'])){
			$video = $settings['video']['url'];
		}

		$classes = array();

		if($settings['solid_background'] == 'solid'){
			$classes[] = 'solid-bg-home-banner';
		}
		if( $settings['search_form_style'] == 'boxed') { 
			$classes[] = 'alt-search-box centered';
		}
		if($settings['home_full_screen'] == 'yes') {
			$classes[] = 'full-height';
		}
		if($video) {
			$classes[] = 'dark-overlay';	
		}

		?>

	<div class="main-search-container  <?php implode(" ",$classes); ?>" data-background-image="<?php echo $background_image ?>">
		<div class="main-search-inner">


			<?php if($settings['search_form_style'] == 'wide') : ?>
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						
						<h2><?php echo $settings['title']; ?>  <span class="typed-words"></span></h2>
						<?php if(!empty( $settings['subtitle'] )) { ?><h4><?php echo $settings['subtitle']; ?></h4><?php } ?>
						
						<?php echo do_shortcode('[listeo_search_form action='.get_post_type_archive_link( 'listing' ).' source="home" custom_class="main-search-form"]') ?>
			
					</div>
				</div>

				<?php
				if($settings['featured_categories_status']=='yes') :

					if(isset($settings['tax-listing_category'])) :
            				$category = is_array( $settings['tax-listing_category'] ) ? $settings['tax-listing_category'] : array_filter( array_map( 'trim', explode( ',', $settings['tax-listing_category'] ) ) );
    
					
							if(!empty($category)) : ?>
							<div class="row">
								<div class="col-md-12">
									<h5 class="highlighted-categories-headline"><?php esc_html_e('Or browse featured categories:','listeo') ?></h5>
									
										  
									<div class="highlighted-categories">
										
										<?php

										foreach ($category as $value) {

											$term = get_term($value,'listing_category');

											if( $term && ! is_wp_error( $term ) ) {
												$icon = get_term_meta($value,'icon',true); 
												$_icon_svg = get_term_meta($value,'_icon_svg',true);
												?>
												<!-- Box -->
												<a href="<?php echo get_term_link($term->slug, 'listing_category'); ?>" class="highlighted-category">
													<?php if (!empty($_icon_svg)) { ?>
													<i>
														<?php echo listeo_render_svg_icon($_icon_svg); ?>
													</i>
											    	<?php } else if($icon && $icon != 'empty')  : ?><i class="<?php echo esc_attr($icon); ?>"></i><?php endif; ?>
													<h4><?php echo esc_html($term->name) ?></h4>
												</a>	

										<?php }
										} ?>
								
									</div>
									
								</div>
							</div>
				<?php 	
						endif;
					endif;
				endif; ?>

				
			</div>
		
			<?php else : ?>

		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<div class="main-search-input">

						<div class="main-search-input-headline">
							<h2><?php echo $settings['title']; ?>  <span class="typed-words"></span></h2>
							<?php if(!empty( $settings['subtitle'] )) { ?><h4><?php echo $settings['subtitle']; ?></h4><?php } ?>
					
						</div>
						
						<?php echo do_shortcode('[listeo_search_form action='.get_post_type_archive_link( 'listing' ).' source="homebox" custom_class="main-search-form"]') ?>

						
					</div>
				</div>
			</div>
			
		</div>
			<?php endif; ?>
		<?php if($video) { 

			if(isset($settings['video_poster']['url']) && !empty(isset($settings['video_poster']['url']))){
			$video_poster = $settings['video_poster']['url'];
		}
		?>
		<!-- Video -->
		<div class="video-container">

			<video <?php if(isset($video_poster)) ?> poster="<?php echo $video_poster ?>" loop autoplay muted>
				<source src="<?php echo $video ?>" type="video/mp4">
				
			</video>
		</div>
		<?php } ?>
		</div>
	</div>
	
		<?php
		if(!empty($settings['url']['url'])){
			echo "</a>";
		}
	}

	protected function get_terms($taxonomy) {
			$taxonomies = get_terms( array( 'taxonomy' =>$taxonomy,'hide_empty' => false) );

			$options = [ '' => '' ];
			
			if ( !empty($taxonomies) ) :
				foreach ( $taxonomies as $taxonomy ) {
					$options[ $taxonomy->term_id ] = $taxonomy->name;
				}
			endif;

			return $options;
		}
	
}