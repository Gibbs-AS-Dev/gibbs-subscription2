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
class HomeCustomGrid extends Widget_Base {

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
		return 'listeo-homecustom-grid';
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
		return __( 'Home Custom Grid', 'listeo_elementor' );
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
		return 'fa fa-th-large';
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
		/*$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'listeo_elementor' ),
			)
		);*/
        $this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'listeo_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_title', [
				'label' => __( 'Title', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'List Title' , 'listeo_elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'list_content', [
				'label' => __( 'Content', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'List Content' , 'listeo_elementor' ),
				'show_label' => false,
			]
		);
        $repeater->add_control(
			'icon', [
				'label' => __( 'Icon', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => __( 'Icon' , 'listeo_elementor' ),
				'show_label' => true,
			]
		);
        $repeater->add_control(
			'hover_image', [
				'label' => __( 'Hover image', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => __( 'Hover image' , 'listeo_elementor' ),
				'show_label' => true,
			]
		);

		$repeater->add_control(
			'list_color',
			[
				'label' => __( 'Color', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}'
				],
			]
		);

		$repeater->start_controls_section(
			'menu_section1',
			[
				'label' => __( 'Menu #1', 'listeo_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $repeater->add_control(
			'menu_title1',
			[
				'label' => __( 'Menu title', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Menu title' , 'listeo_elementor' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'menu_link1',
			[
				'label' => __( 'Menu Link', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'plugin-domain' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);
		$repeater->end_controls_section();

		$repeater->start_controls_section(
			'menu_section2',
			[
				'label' => __( 'Menu #2', 'listeo_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $repeater->add_control(
			'menu_title2',
			[
				'label' => __( 'Menu title', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Menu title' , 'listeo_elementor' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'menu_link2',
			[
				'label' => __( 'Menu Link', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'plugin-domain' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);
		$repeater->end_controls_section();

		$repeater->start_controls_section(
			'menu_section3',
			[
				'label' => __( 'Menu #3', 'listeo_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $repeater->add_control(
			'menu_title3',
			[
				'label' => __( 'Menu title', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Menu title' , 'listeo_elementor' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'menu_link3',
			[
				'label' => __( 'Menu Link', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'plugin-domain' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);
		$repeater->end_controls_section();


		$this->add_control(
			'homelist',
			[
				'label' => __( 'List', 'listeo_elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_title' => __( 'Title #1', 'plugin-domain' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'plugin-domain' ),
					],
					[
						'list_title' => __( 'Title #2', 'plugin-domain' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'plugin-domain' ),
					],
				],
				'title_field' => '{{{ list_title }}}',
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

		
		
		$home_lists = $settings['homelist'];

		//echo "<pre>"; print_r($home_lists); die;

       	?>

		<div class="homecutom-boxes-container<?php if($settings['style']=='alt'){ echo "-alt"; }?> customhomebox margin-top-5 margin-bottom-30">
			
			<!-- Item -->
			<?php 
      		foreach ( $home_lists as $list ) { 
		        $t_id = $term->term_id;

		        $title = $list["list_title"];
		        $content = $list["list_content"];
		        if(isset($list["icon"]["id"])){
		        	$_icon_svg_id = $list["icon"]["id"];
		        }else{
		        	$_icon_svg_id = "";
		        }
		        if(isset($list["hover_image"]["url"])){
		        	$hover_image = $list["hover_image"]["url"];
		        }else{
		        	$hover_image = "";
		        }
		        
		        if(empty($_icon_svg_id)) {
					$icon = 'fa fa-globe' ;
		        }
		   
		        ?>
		        <div class="outer-custom-home" style='background-image: url(<?php echo $hover_image;?>)''>
						<div  class="homecustom-small-box<?php if($settings['style']=='alt'){ echo "-alt"; }?>">
							<?php if (!empty($_icon_svg_id)) { ?>
								<i class="listeo-svg-icon-box-grid">
									<?php echo listeo_render_svg_icon($_icon_svg_id); ?>
								</i>
			          		<?php } else { 
			          			if($icon != 'emtpy') {
			          				$check_if_im = substr($icon, 0, 3);
				                    if($check_if_im == 'im ') {
				                       echo' <i class="'.esc_attr($icon).'"></i>'; 
				                    } else {
				                       echo ' <i class="fa '.esc_attr($icon).'"></i>'; 
				                    }
			          			}
			          		} ?>
			          		<?php
			          		if(isset($list["hover_image"]["url"])){ ?>
			          			<div class="hover_image">
			          			  <img src='<?php echo $list["hover_image"]["url"];?>'>
			          			</div>
					        <?php }
					        ?>
			          		<div class="under_span">
								<h4><?php echo $title; ?></h4>
								<p><?php echo $content; ?></p>
							</div>
							<div class="menu_div">
								<ul>
									<?php if($list["menu_title1"] != ""){ ?>
			                               <li><a class="href_link" href="<?php echo $list["menu_link1"]["url"];?>"><?php echo $list["menu_title1"];?></a></li>
									<?php } ?>
									<?php if($list["menu_title2"] != ""){ ?>
			                               <li><a class="href_link" href="<?php echo $list["menu_link2"]["url"];?>"><?php echo $list["menu_title2"];?></a></li>
									<?php } ?>
									<?php if($list["menu_title3"] != ""){ ?>
			                               <li><a class="href_link" href="<?php echo $list["menu_link3"]["url"];?>"><?php echo $list["menu_title3"];?></a></li>
									<?php } ?>
									
								</ul>
							</div>
							
						</div>
			        </div>
 
			<?php } ?>
		</div>
		<script type="text/javascript">

			jQuery(".outer-custom-home").click(function(event){
				if(jQuery(event.target).hasClass("href_link") == false){

					var li_a = jQuery(this).find(".href_link:first").attr("href");
					window.location.href = li_a;
				}
			})
		</script>

 		<?php

	}

	
	protected function get_taxonomies() {
		$taxonomies = get_object_taxonomies( 'listing', 'objects' );

		$options = [ '' => '' ];

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
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