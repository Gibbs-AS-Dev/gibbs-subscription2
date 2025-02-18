<?php

namespace Elementor;
if (!defined('ABSPATH')) exit;


/**
 * Graphina AdvanceDataTable widget.
 *
 * Graphina widget that displays an eye-catching counter.
 *
 * @since 1.2.4
 */
class Counter extends Widget_Base
{
    /**
     * Get widget name.
     *
     * Retrieve heading widget name.
     *
     * @return string Widget name.
     * @since 1.2.4
     * @access public
     *
     */

    public function get_name()
    {
        return 'counter_chart';
    }

    /**
     * Get widget Title.
     *
     * Retrieve heading widget Title.
     *
     * @return string Widget Title.
     * @since 1.2.4
     * @access public
     *
     */

    public function get_title()
    {
        return 'Counter';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the heading widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @return array Widget categories.
     * @since 1.2.4
     * @access public
     *
     */


    public function get_categories()
    {
        return ['iq-graphina-charts'];
    }


    /**
     * Get widget icon.
     *
     * Retrieve heading widget icon.
     *
     * @return string Widget icon.
     * @since 1.2.4
     * @access public
     *
     */

    public function get_icon()
    {
        return 'graphina-apex-counter-chart';
    }

    public function get_chart_type()
    {
        return 'counter';
    }

    protected function register_controls()
    {
        $type = $this->get_chart_type();
        $colors = graphina_colors('color');
        $gradientColor = graphina_colors('gradientColor');

        $this->start_controls_section(
            'iq_' . $type . '_section_5_2',
            [
                'label' => esc_html__('Counter Data Options',  'graphina-pro-charts-for-elementor')
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_layout_option',
            [
                'label' => esc_html__('Layout',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_element_data_enter_options('counter_layout', true),
                'options' => graphina_pro_element_data_enter_options('counter_layout')
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_data_option',
            [
                'label' => esc_html__('Type',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_element_data_enter_options('main_type', true),
                'options' => graphina_pro_element_data_enter_options('main_type',false,false,$type)
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_show_chart',
            [
                'label' => esc_html__('Has chart?',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('Show',  'graphina-pro-charts-for-elementor'),
                'default' => false
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_use_chart_data',
            [
                'label' => esc_html__('Use chart data?',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('Show',  'graphina-pro-charts-for-elementor'),
                'default' => false,
                'condition' => [
                    'iq_' . $type . '_element_show_chart' => 'yes',
                    'iq_' . $type . '_element_data_option' => 'manual'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_dynamic_data_option',
            [
                'label' => esc_html__('Get Data From',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_element_data_enter_options($type, true),
                'options' => graphina_pro_element_data_enter_options($type),
                'condition' => [
                    'iq_' . $type . '_element_data_option' => 'dynamic'
                ]
            ]
        );

        graphina_pro_element_data_option_setting($this, $type, null);

        $this->add_control(
            'iq_' . $type . '_title',
            [
                'label' => esc_html__('Title',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Title',  'graphina-pro-charts-for-elementor'),
                'label_block' => true,
                'default' => 'Title',
                'condition' => [
                    'iq_' . $type . '_element_data_option' => 'manual'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_description',
            [
                'label' => esc_html__('Description',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'placeholder' => esc_html__('Description',  'graphina-pro-charts-for-elementor'),
                'label_block' => true,
                'default' => 'Description',
                'condition' => [
                    'iq_' . $type . '_element_data_option' => 'manual'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_counter_icon',
            [
                'label' => esc_html__('Icon',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'iq_' . $type . '_element_layout_option!' => ['layout_2', 'layout_3', 'layout_4']
                ]
            ]
        );

        $this->end_controls_section();

        do_action('graphina_forminator_addon_control_section', $this, $type);

        do_action('graphina_addons_control_section', $this, $type);

        graphina_restriction_content_options($this, $type);

        $this->start_controls_section(
            'iq_' . $type . '_section_data_options',
            [
                'label' => esc_html__('Counter Settings',  'graphina-pro-charts-for-elementor'),
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_from_count',
            [
                'label' => esc_html__('Start From',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => esc_html__('0',  'graphina-pro-charts-for-elementor'),
                'default' => 0,
                'min' => 0,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_show_chart',
                                    'operator' => '===',
                                    'value' => 'yes'
                                ], [
                                    'name' => 'iq_' . $type . '_element_use_chart_data',
                                    'operator' => '===',
                                    'value' => 'yes'
                                ]
                            ]
                        ], [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_data_option',
                                    'operator' => '===',
                                    'value' => 'manual'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_to_count',
            [
                'label' => esc_html__('End At',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => esc_html__('1000',  'graphina-pro-charts-for-elementor'),
                'default' => 100,
                'min' => 0,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_show_chart',
                                    'operator' => '===',
                                    'value' => 'yes'
                                ], [
                                    'name' => 'iq_' . $type . '_element_use_chart_data',
                                    'operator' => '===',
                                    'value' => 'yes'
                                ]
                            ]
                        ], [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_data_option',
                                    'operator' => '===',
                                    'value' => 'manual'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_counter_operation',
            [
                'label' => esc_html__('Operation',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'description' => esc_html__("PLease select which operation needs to be performed on the selected column. If selected none, last value from selected column will be considered",  'graphina-pro-charts-for-elementor'),
                'default' => graphina_pro_element_data_enter_options('graphina_counter_operations', true),
                'options' => graphina_pro_element_data_enter_options('graphina_counter_operations'),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_show_chart',
                                    'operator' => '===',
                                    'value' => 'yes'
                                ], [
                                    'name' => 'iq_' . $type . '_element_use_chart_data',
                                    'operator' => '===',
                                    'value' => 'yes'
                                ]
                            ]
                        ], [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_data_option',
                                    'operator' => '!==',
                                    'value' => 'manual'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_column_no',
            [
                'label' => esc_html__('Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'placeholder' => esc_html__('1',  'graphina-pro-charts-for-elementor'),
                'description' => esc_html__('Enter the column letter from which data should be referenced',  'graphina-pro-charts-for-elementor'),
                'default' => 1,
                'min' => 1,
                'options' => graphina_pro_get_alphabet(),
                'condition' => [
                    'iq_' . $type . '_element_data_option' => 'dynamic',
                    'iq_' . $type . '_element_dynamic_data_option' => ['csv', 'remote-csv', 'google-sheet', 'database']
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_api_object_no',
            [
                'label' => esc_html__('Object Number',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => esc_html__('1',  'graphina-pro-charts-for-elementor'),
                'default' => 1,
                'min' => 1,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_data_option',
                                    'operator' => '===',
                                    'value' => 'dynamic'
                                ], [
                                    'name' => 'iq_' . $type . '_element_dynamic_data_option',
                                    'operator' => '===',
                                    'value' => 'api'
                                ]
                            ]
                        ], [
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_element_data_option',
                                    'operator' => '===',
                                    'value' => 'firebase'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_counter_speed',
            [
                'label' => esc_html__('Speed',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => esc_html__('1000',  'graphina-pro-charts-for-elementor'),
                'default' => 100,
                'min' => 1,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_prefix',
            [
                'label' => esc_html__('Prefix',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('prefix',  'graphina-pro-charts-for-elementor'),
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_postfix',
            [
                'label' => esc_html__('Postfix',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('postfix',  'graphina-pro-charts-for-elementor'),
                'default' => '',
                'condition' => ['iq_' . $type . '_element_counter_operation!' => 'percentage'],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_postfix_percentage',
            [
                'label' => esc_html__('Counter Postfix',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('postfix',  'graphina-pro-charts-for-elementor'),
                'default' => '%',
                'condition' => ['iq_' . $type . '_element_counter_operation' => 'percentage']
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_separator',
            [
                'label' => esc_html__('Number Separator',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('separator',  'graphina-pro-charts-for-elementor'),
                'default' => ''
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_color_condition_based',
            [
                'label' => esc_html__('Condition Based Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,

            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_heading_color_condition_based',
            [
                'label' => esc_html__('Heading Condition Based Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'condition' =>[
                    'iq_' . $type . '_counter_color_condition_based' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_subheading_color_condition_based',
            [
                'label' => esc_html__('Subheading Condition Based Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'condition' =>[
                    'iq_' . $type . '_counter_color_condition_based' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_min_value',
            [
                'label' => esc_html__('Min Value',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'condition' =>[
                    'iq_' . $type . '_counter_color_condition_based' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_min_value_color',
            [
                'label' => esc_html__('Min Value Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' =>[
                    'iq_' . $type . '_counter_color_condition_based' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_max_value',
            [
                'label' => esc_html__('Max Value',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1000,
                'condition' =>[
                    'iq_' . $type . '_counter_color_condition_based' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_max_value_color',
            [
                'label' => esc_html__('Max Value Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' =>[
                    'iq_' . $type . '_counter_color_condition_based' => 'yes'
                ]
            ]
        );
        $this->end_controls_section();

        /***************************
         * Chart Options
         *************************/

        $this->start_controls_section(
            'iq_' . $type . '_section_chart_options',
            [
                'label' => esc_html__('Chart Options',  'graphina-pro-charts-for-elementor'),
                'condition' => [
                    'iq_' . $type . '_element_show_chart' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_type',
            [
                'label' => esc_html__('Type',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_mixed_chart_typeList(true, true),
                'options' => graphina_pro_mixed_chart_typeList(false, true)
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_height',
            [
                'label' => esc_html__('Height',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 1000,
                'default' => 70
            ]
        );

        $this->add_control(
            'hr_1_01',
            [
                'type' => Controls_Manager::DIVIDER
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stroke_title',
            [
                'label' => esc_html__('Stroke Setting',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stroke_width',
            [
                'label' => esc_html__('Width',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'default' => 2
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stroke_dash',
            [
                'label' => esc_html__('Dash Space',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'default' => 0
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stroke_color',
            [
                'label' => esc_html__('Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => $colors[0],
                'condition' => [
                    'iq_' . $type . '_chart_type!' => 'line'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stroke_curve',
            [
                'label' => esc_html__('Curve',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_stroke_curve_type(true),
                'options' => graphina_stroke_curve_type(),
                'condition' => [
                    'iq_' . $type . '_chart_type!' => 'bar'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stroke_line_cap',
            [
                'label' => esc_html__('Line Cap',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_line_cap_type(true),
                'options' => graphina_pro_line_cap_type(),
                'condition' => [
                    'iq_' . $type . '_chart_type!' => 'bar'
                ]
            ]
        );

        $this->add_control(
            'hr_1_02',
            [
                'type' => Controls_Manager::DIVIDER,
                'condition' => [
                    'iq_' . $type . '_chart_type' => 'bar'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_title',
            [
                'label' => esc_html__('Plot Setting',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'iq_' . $type . '_chart_type' => 'bar'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_end_shape',
            [
                'label' => esc_html__('Ending Shape',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'flat',
                'options' => [
                    'flat' => esc_html__('Flat',  'graphina-pro-charts-for-elementor'),
                    'rounded' => esc_html__('Rounded',  'graphina-pro-charts-for-elementor'),
                ],
                'condition' => [
                    'iq_' . $type . '_chart_type' => 'bar'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_width',
            [
                'label' => esc_html__('Width',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'description' => esc_html__('Note: Set 0 for auto setting.',  'graphina-pro-charts-for-elementor'),
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 70,
                ],
                'condition' => [
                    'iq_' . $type . '_chart_type' => 'bar'
                ]
            ]
        );

        $this->add_control(
            'hr_1_03',
            [
                'type' => Controls_Manager::DIVIDER
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_fill_title',
            [
                'label' => esc_html__('Fill Setting',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_fill_style_type',
            [
                'label' => esc_html__('Style',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => graphina_fill_style_type(['classic', 'gradient', 'pattern'], true),
                'options' => graphina_fill_style_type(['classic', 'gradient', 'pattern']),
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_fill_opacity',
            [
                'label' => esc_html__('Opacity',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0.6,
                'min' => 0.00,
                'max' => 1,
                'step' => 0.05,
                'condition' => ['iq_' . $type . '_chart_fill_style_type!' => 'gradient']
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_gradient_1',
            [
                'label' => esc_html__('Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => $colors[0],
            ]
        );
        $this->add_control(
            'iq_' . $type . '_chart_gradient_2',
            [
                'label' => esc_html__('Second Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => $gradientColor[0],
                'condition' => ['iq_' . $type . '_chart_fill_style_type' => 'gradient']
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_pattern',
            [
                'label' => esc_html__('Fill Pattern',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_get_fill_patterns(true),
                'options' => graphina_get_fill_patterns(),
                'condition' => [
                    'iq_' . $type . '_chart_type!' => 'line',
                    'iq_' . $type . '_chart_fill_style_type' => 'pattern',
                ]
            ]
        );

        graphina_gradient_setting($this, $type, true, true);

        $this->end_controls_section();

        /***************************
         * Chart Data
         *************************/

        $this->start_controls_section(
            'iq_' . $type . '_section_chart_data',
            [
                'label' => esc_html__('Chart Data',  'graphina-pro-charts-for-elementor'),
                'condition' => [
                    'iq_' . $type . '_element_show_chart' => 'yes',
                    'iq_' . $type . '_element_data_option' => 'manual'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_series_title',
            [
                'label' => esc_html__('Title',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Add Tile',  'graphina-pro-charts-for-elementor'),
                'default' => 'Element',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'iq_' . $type . '_chart_value',
            [
                'label' => esc_html__('Series Value',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => esc_html__('Add Value',  'graphina-pro-charts-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        /** Chart value list. */
        $this->add_control(
            'iq_' . $type . '_value_list',
            [
                'label' => esc_html__('Values List',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['iq_' . $type . '_chart_value' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value' => rand(10, 200)],
                ],
                'title_field' => '{{{ iq_' . $type . '_chart_value }}}'
            ]
        );

        $this->end_controls_section();

        /***************************
         * Card Style
         *************************/
        graphina_pro_card_style_section($this, $type, true);

        /***************************
         * Icon Style
         ***************************/
        $this->start_controls_section('iq_' . $type . '_icon_style_section',
            [
                'label' => esc_html__('Icon Style',  'graphina-pro-charts-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'iq_' . $type . '_element_layout_option' => graphina_pro_get_array_diff(graphina_pro_element_data_enter_options('counter_layout', false, true), ['layout_2', 'layout_3', 'layout_4'])
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_count_icon_font_color',
            [
                'label' => esc_html__('Font Color',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter .counter-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_element_counter_icon_size',
            [
                'label' => esc_html__('Size', 'plugin-domain'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ]
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 75,
                ],
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter .counter-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_count_icon_horizontal_alignment',
            [
                'label' => esc_html__('Alignment',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter .counter-icon' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'iq_' . $type . '_element_layout_option' => graphina_pro_get_array_diff(graphina_pro_element_data_enter_options('counter_layout', false, true), ['layout_5', 'layout_6'])
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_count_icon_horizontal_position',
            [
                'label' => esc_html__('Alignment',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => esc_html__('Left',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'row-reverse' => [
                        'title' => esc_html__('Right',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'row',
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter' => 'flex-direction: {{VALUE}};',
                ],
                'condition' => [
                    'iq_' . $type . '_element_layout_option' => ['layout_5', 'layout_6']
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_count_icon_margin',
            [
                'label' => esc_html__('Margin',  'graphina-pro-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter .counter-icon i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_count_icon_padding',
            [
                'label' => esc_html__('Padding',  'graphina-pro-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter .counter-icon i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'iq_' . $type . '_counter_icon_border',
                'label' => esc_html__('Border',  'graphina-pro-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .graphina-card.counter .counter-icon i',
            ]
        );

        $this->add_control(
            'iq_' . $type . '_counter_icon_border_radius',
            [
                'label' => esc_html__('Border Radius',  'graphina-pro-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter .counter-icon i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
                ],
            ]
        );

        $this->end_controls_section();

        /***************************
         * Counter Style
         ***************************/
        graphina_pro_counter_style_section($this, $type, 'counter', '.graphina-card.counter .myGraphinaCounter');

        /***************************
         * Title Style
         ***************************/
        graphina_pro_counter_style_section($this, $type, 'title', '.graphina-card.counter .title');

        /***************************
         * Title Style
         ***************************/
        graphina_pro_counter_style_section($this, $type, 'description', '.graphina-card.counter .description');

        graphina_pro_password_style_section($this, $type);

    }

    protected function render()
    {
        $type = $this->get_chart_type();
        $settings = $this->get_settings_for_display();
        $widgetId = $this->get_id();
        $sign = '';
        if(isRestrictedAccess('counter',$this->get_id(),$settings,true)) {
            if($settings['iq_counter_restriction_content_type'] ==='password'){
                return true;
            }
            echo html_entity_decode($settings['iq_counter_restriction_content_template']);
            return true;
        }
        $counters = [];
        $counter = [
            'multi' => [],
            'end' => 0
        ];
        $prefix = $settings['iq_' . $type . '_counter_prefix'];
        $postfix = ($settings['iq_' . $type . '_element_counter_operation'] === 'percentage') ? $settings['iq_' . $type . '_counter_postfix_percentage'] : $settings['iq_' . $type . '_counter_postfix'];
        $separator = $settings['iq_' . $type . '_counter_separator'];
        $title = !empty($settings['iq_' . $type . '_chart_series_title']) ? (string)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_chart_series_title') : '';

        $dataOption = $settings['iq_' . $type . '_element_data_option'] === 'manual' ? 'manual' : $settings['iq_' . $type . '_element_dynamic_data_option'];
        switch ($dataOption) {
            case 'manual' :
                $counter = [
                    'title' => (string)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_title'),
                    'start' => (float)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_element_from_count'),
                    'end' => (float)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_element_to_count'),
                    'speed' => (float)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_element_counter_speed'),
                    'description' => (string)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_description'),
                    'multi' => []
                ];
                break;
            case 'csv' :
                $counters = graphina_pro_element_parse_csv($this, $type, 'counter');
                break;
            case "remote-csv":
                $counters = graphina_pro_element_data_remote_csv($settings['iq_' . $type . '_element_import_from_url'], 'counter', $type, $this);
                break;
            case "google-sheet":
                $counters = graphina_pro_element_data_remote_csv($settings['iq_' . $type . '_element_import_from_google_sheet'], 'counter', $type, $this);
                break;
            case "api":
                $counters = graphina_pro_element_get_data_from_api($settings['iq_' . $type . '_element_import_from_api'], $type, $this);
                break;
            case "database":
                $counters = graphina_pro_element_get_data_from_database($type, $settings['iq_' . $type . '_element_import_from_database'], $settings['iq_' . $type . '_element_import_from_table'], $settings['iq_' . $type . '_element_import_from_query'],'','','',$settings );
                break;
            case 'filter':
                update_post_meta(get_the_ID(),$this->get_id(),$settings['iq_' . $type . '_element_filter_widget_id']);
                $counters = apply_filters('graphina_extra_data_option', [ ], $type, $settings,$settings['iq_' . $type . '_element_filter_widget_id']);
                break;
        }

        if($settings['iq_' . $type . '_element_data_option'] === 'firebase'){
            $counters = apply_filters('graphina_addons_render_section', $counters, $type, $settings);
        }

        if (!empty($counters['fail']) && $counters['fail'] === 'permission') {
            switch ($dataOption) {
                case "google-sheet" :
                    echo sprintf("<pre><b>%s</b><small><a target='_blank' href='https://youtu.be/Dv8s4QxZlDk'>%s</a></small></pre>", esc_html__('Please check file sharing permission and "Publish As" type is CSV or not. ',  'graphina-pro-charts-for-elementor'), esc_html__('Click for reference.',  'graphina-pro-charts-for-elementor'));
                    return;
                case "remote-csv" :
                default:
                    echo "<pre><b>" . (isset($counters['errorMessage']) ? $counters['errorMessage'] :  esc_html__('Please check file sharing permission.',  'graphina-pro-charts-for-elementor')). "</b></pre>";
                    return;
                    break;
            }
        }

        if($settings['iq_' . $type . '_element_data_option'] === 'firebase')
        {
            $count =  $settings['iq_' . $type . '_element_api_object_no'];
            if ($count <= count($counters)) {
                $counter = $counters[$count - 1];
                $title = !empty($counter['title']) ? $counter['title'] : '';
            }
        }

        if(!empty($settings['iq_' . $type . '_element_dynamic_data_option']) && $settings['iq_' . $type . '_element_dynamic_data_option'] == 'filter'){
            if(is_array($counters) && count($counters) > 0){
                $counter = $counters[0];
                $title = !empty($counter['title']) ? $counter['title'] : '';
            }
        }

        if (in_array($dataOption, ['csv', 'remote-csv', 'google-sheet', 'api', 'database'])) {
            $count = $dataOption === 'api' ? $settings['iq_' . $type . '_element_api_object_no'] : $settings['iq_' . $type . '_element_column_no'];
            if ($count <= count($counters)) {
                $counter = $counters[$count - 1];
                $title = !empty($counter['title']) ? $counter['title'] : '';
            }
        }

        if (!empty($settings['iq_' . $type . '_element_show_chart']) && $settings['iq_' . $type . '_element_show_chart'] === "yes" && !empty($settings['iq_' . $type . '_element_data_option']) && $settings['iq_' . $type . '_element_data_option'] === 'manual') {
            foreach ($settings['iq_' . $type . '_value_list'] as $v) {
                $counter['multi'][] = !empty($v['iq_' . $type . '_chart_value']) ? (float)$v['iq_' . $type . '_chart_value'] : 0;
                if (!empty($settings['iq_' . $type . '_element_use_chart_data']) && $settings['iq_' . $type . '_element_use_chart_data'] === 'yes') {
                    $counter['end'] = !empty($v['iq_' . $type . '_chart_value']) ? (float)$v['iq_' . $type . '_chart_value'] : 0;
                }
            }
        }

        if(empty($counter['multi'])){
            $counter['multi']=!empty($counter['end']) ? [$counter['end']] : [];
        }
        if(empty($counter['speed'])){
            $counter['speed'] = !empty($counter['speed']) ? $counter['speed'] : (!empty($settings['iq_' . $type . '_element_counter_speed']) ? (float)$settings['iq_' . $type . '_element_counter_speed'] : 0);
        }

        if ($dataOption !== 'manual' && count($counter) > 0 || (!empty($settings['iq_' . $type . '_element_show_chart']) && $settings['iq_' . $type . '_element_show_chart'] === "yes")) {
            switch ($settings['iq_' . $type . '_element_counter_operation']) {
                case 'sum':
                    if (count($counter['multi']) > 0) {
                        $counter['end'] = array_sum($counter['multi']);
                    }
                    break;
                case 'avg':
                    if (count($counter['multi']) > 0) {
                        $counter['end'] = number_format((float)(array_sum($counter['multi']) / count($counter['multi'])), 2, '.', '');
                    }
                    break;
                case 'percentage':
                    if (count($counter['multi']) > 0) {
                        $sum = array_sum($counter['multi']);
                        $counter['end'] = number_format((float)(($sum * 100) / pow(10, strlen($sum))), 2, '.', '');
                    } else {
                        $postfix = '';
                    }
                    break;
            }
        }
        if (count($counter) > 0) {
            $color = '';
            $headingColor='';
            $subHeadingColor='';
            if( isset($counter['end'])){
                if(!empty($settings['iq_' . $type . '_counter_color_condition_based']) && $settings['iq_' . $type . '_counter_color_condition_based'] === 'yes'){
                    if(!empty($settings['iq_' . $type . '_counter_min_value']) && $counter['end'] < (float)$settings['iq_' . $type . '_counter_min_value'] ){
                        $color = strval(!empty($settings['iq_' . $type . '_counter_min_value_color']) ? $settings['iq_' . $type . '_counter_min_value_color'] : '');
                    }
                    if(!empty($settings['iq_' . $type . '_counter_max_value']) && $counter['end'] > (float)$settings['iq_' . $type . '_counter_max_value'] ){
                        $color = strval(!empty($settings['iq_' . $type . '_counter_max_value_color']) ? $settings['iq_' . $type . '_counter_max_value_color'] : '');
                    }

                    if(!empty($settings['iq_' . $type . '_counter_heading_color_condition_based']) && $settings['iq_' . $type . '_counter_heading_color_condition_based'] === 'yes' ){
                        $headingColor = $color;
                    }
                    if(!empty($settings['iq_' . $type . '_counter_subheading_color_condition_based']) && $settings['iq_' . $type . '_counter_subheading_color_condition_based'] === 'yes' ){
                        $subHeadingColor = $color;
                    }
                }
                if($counter['end'] < 0){
                    $counter['end'] =abs($counter['end']);
                    $sign = '-';
                }
            }
            switch ($settings['iq_' . $type . '_element_layout_option']) {
                case 'layout_1':
                    ?>
                    <div class="graphina-card counter layout_1">
                        <div class="counter-icon">
                            <?php Icons_Manager::render_icon($settings['iq_' . $type . '_element_counter_icon'], ['aria-hidden' => 'true']); ?>
                        </div>
                        <h2 style="color:<?php echo $color ?>;" class="myGraphinaCounter counter-<?php esc_attr_e($widgetId); ?> text-center"
                            data-start="<?php echo isset($counter['start']) ? $counter['start'] : 0; ?>"
                            data-end="<?php echo isset($counter['end']) ? $counter['end'] : 0; ?>"
                            data-speed="<?php echo isset($counter['speed']) ? $counter['speed'] : 0; ?>"
                        >
                            0
                        </h2>
                        <?php if (!empty($counter['title'])) { ?>
                            <h2 class="text-center title" style="color:<?php echo $headingColor ?>;">
                                <?php echo html_entity_decode($counter['title']);?>
                            </h2>
                        <?php }
                        if (!empty($counter['description'])) {
                            ?>
                            <p class="text-center description" style="color:<?php echo $subHeadingColor ?>;">
                                <?php echo html_entity_decode($counter['description']);?>
                            </p>
                            <?php
                        }
                        if ($settings['iq_' . $type . '_element_show_chart'] === 'yes') {
                            ?>
                            <div class="graphina-counter-chart-<?php esc_attr_e($widgetId); ?>"></div>
                        <?php } ?>
                    </div>
                    <?php break;
                case 'layout_2':
                    ?>
                    <div class="graphina-card counter layout_2">
                        <h2 style="color:<?php echo $color ?>;" class="myGraphinaCounter counter-<?php esc_attr_e($widgetId); ?> text-center"
                            data-start="<?php echo isset($counter['start']) ? $counter['start'] : 0; ?>"
                            data-end="<?php echo isset($counter['end']) ? $counter['end'] : 0; ?>"
                            data-speed="<?php echo isset($counter['speed']) ? $counter['speed'] : 0; ?>"
                        >
                            0
                        </h2>
                        <?php if (!empty($counter['title'])) { ?>
                            <h2 class="text-center title" style="color:<?php echo $headingColor ?>;">
                                <?php echo html_entity_decode($counter['title']);?>
                            </h2>
                        <?php }
                        if (!empty($counter['description'])) {
                            ?>
                            <p class="text-center description" style="color:<?php echo $subHeadingColor ?>;">
                                <?php echo html_entity_decode($counter['description']);?>
                            </p>
                            <?php
                        }
                        if ($settings['iq_' . $type . '_element_show_chart'] === 'yes') {
                            ?>
                            <div class="graphina-counter-chart-<?php esc_attr_e($widgetId); ?>"></div>
                        <?php } ?>
                    </div>
                    <?php break;
                case 'layout_3':
                    ?>
                    <div class="graphina-card counter layout_3">
                        <?php if (!empty($counter['title'])) { ?>
                            <h2 class="text-center title" style="color:<?php echo $headingColor ?>;">
                                <?php echo html_entity_decode($counter['title']);?>
                            </h2>
                        <?php } ?>
                        <h2 style="color:<?php echo $color ?>;" class="myGraphinaCounter counter-<?php esc_attr_e($widgetId); ?> text-center"
                            data-start="<?php echo isset($counter['start']) ? $counter['start'] : 0; ?>"
                            data-end="<?php echo isset($counter['end']) ? $counter['end'] : 0; ?>"
                            data-speed="<?php echo isset($counter['speed']) ? $counter['speed'] : 0; ?>"
                        >
                            0
                        </h2>
                        <?php
                        if (!empty($counter['description'])) {
                            ?>
                            <p class="text-center description" style="color:<?php echo $subHeadingColor ?>;">
                                <?php echo html_entity_decode($counter['description']);?>
                            </p>
                            <?php
                        }
                        if ($settings['iq_' . $type . '_element_show_chart'] === 'yes') {
                            ?>
                            <div class="graphina-counter-chart-<?php esc_attr_e($widgetId); ?>"></div>
                        <?php } ?>
                    </div>
                    <?php break;
                case 'layout_4':
                    ?>
                    <div class="graphina-card counter layout_4">
                        <?php if (!empty($counter['description'])) {
                            ?>
                            <p class="text-center description" style="color:<?php echo $subHeadingColor ?>;" >
                                <?php echo html_entity_decode($counter['description']);?>
                            </p>
                        <?php } ?>

                        <h2 style="color:<?php echo $color ?>;" class="myGraphinaCounter counter-<?php esc_attr_e($widgetId); ?> text-center"
                            data-start="<?php echo isset($counter['start']) ? $counter['start'] : 0; ?>"
                            data-end="<?php echo isset($counter['end']) ? $counter['end'] : 0; ?>"
                            data-speed="<?php echo isset($counter['speed']) ? $counter['speed'] : 0; ?>"
                        >
                            0
                        </h2>
                        <?php if (!empty($counter['title'])) { ?>
                            <h2 class="text-center title" style="color:<?php echo $headingColor ?>;">
                                <?php echo html_entity_decode($counter['title']);?>
                            </h2>
                            <?php
                        }
                        if ($settings['iq_' . $type . '_element_show_chart'] === 'yes') {
                            ?>
                            <div class="graphina-counter-chart-<?php esc_attr_e($widgetId); ?>"></div>
                        <?php } ?>
                    </div>
                    <?php break;
                case 'layout_5':
                    ?>
                    <div class="graphina-card counter layout_5">
                        <div class="main-counter">
                            <div class="counter-icon part-1">
                                <?php Icons_Manager::render_icon($settings['iq_' . $type . '_element_counter_icon'], ['aria-hidden' => 'true']); ?>
                            </div>
                            <div class="part-2">
                                <h2 style="color:<?php echo $color ?>;" class="myGraphinaCounter counter-<?php esc_attr_e($widgetId); ?> text-center"
                                    data-start="<?php echo isset($counter['start']) ? $counter['start'] : 0; ?>"
                                    data-end="<?php echo isset($counter['end']) ? $counter['end'] : 0; ?>"
                                    data-speed="<?php echo isset($counter['speed']) ? $counter['speed'] : 0; ?>"
                                >
                                    0
                                </h2>
                                <?php if (!empty($counter['title'])) { ?>
                                    <h2 class="text-center title" style="color:<?php echo $headingColor ?>;">
                                        <?php echo html_entity_decode($counter['title']);?>
                                    </h2>
                                <?php }
                                if (!empty($counter['description'])) {
                                    ?>
                                    <p class="text-center description" style="color:<?php echo $subHeadingColor ?>;">
                                        <?php echo html_entity_decode($counter['description']);?>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                        if ($settings['iq_' . $type . '_element_show_chart'] === 'yes') {
                            ?>
                            <div class="graphina-counter-chart-<?php esc_attr_e($widgetId); ?>"></div>
                        <?php } ?>
                    </div>
                    <?php break;
                case 'layout_6':
                    ?>
                    <div class="graphina-card counter layout_6">
                        <div class="main-counter">
                            <div class="counter-icon part-1">
                                <?php Icons_Manager::render_icon($settings['iq_' . $type . '_element_counter_icon'], ['aria-hidden' => 'true']); ?>
                            </div>
                            <div class="part-2">
                                <?php if (!empty($counter['title'])) { ?>
                                    <h2 class="text-center title" style="color:<?php echo $headingColor ?>;">
                                        <?php echo html_entity_decode($counter['title']);?>
                                    </h2>
                                <?php } ?>
                                <h2 style="color:<?php echo $color ?>;" class="myGraphinaCounter counter-<?php esc_attr_e($widgetId); ?> text-center"
                                    data-start="<?php echo isset($counter['start']) ? $counter['start'] : 0; ?>"
                                    data-end="<?php echo isset($counter['end']) ? $counter['end'] : 0; ?>"
                                    data-speed="<?php echo isset($counter['speed']) ? $counter['speed'] : 0; ?>"
                                >
                                    0
                                </h2>
                                <?php if (!empty($counter['description'])) {
                                    ?>
                                    <p class="text-center description" style="color:<?php echo $subHeadingColor ?>;">
                                        <?php echo html_entity_decode($counter['description']);?>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                        if ($settings['iq_' . $type . '_element_show_chart'] === 'yes') {
                            ?>
                            <div class="graphina-counter-chart-<?php esc_attr_e($widgetId); ?>"></div>
                        <?php } ?>
                    </div>
                    <?php break;
            }
        } else { ?>
            <div class="graphina-card counter "><span
                        class="graphina-no-data"><?php  echo   esc_html('No Data Available', 'graphina-pro-charts-for-elementor'); ?></span></div>
            <?php
        }
        $chartDataJson = json_encode([['name' => $title, 'data' => !empty($counter['multi']) ? $counter['multi'] :[]]]);
        $stokeColor = $settings['iq_' . $type . '_chart_type'] !== 'line' ? json_encode([strval($settings['iq_' . $type . '_chart_stroke_color'])]) : 'undefined';

        if( isRestrictedAccess('counter',$this->get_id(),$settings,false) === false)
        {
        ?>
        <script>
            if (typeof counterInit === "undefined") {
                var counterInit = {};
            }
            if (typeof myCounterInterval === "undefined") {
                var myCounterInterval = {};
            }
            if (typeof myCounter === "undefined") {
                var myCounter = {};
            }
            var counterId = '<?php esc_attr_e($widgetId); ?>';
            var element = document.querySelector('.myGraphinaCounter.counter-<?php esc_attr_e($widgetId); ?>');

            if (typeof isInit === 'undefined') {
                var isInit = {};
            }
            isInit['<?php esc_attr_e($widgetId); ?>'] = false;

            var myElement = document.querySelector(".graphina-counter-chart-<?php esc_attr_e($widgetId); ?>");

            var counterChartOptions = {
                series:<?php echo $chartDataJson ?>,
                chart: {
                    type: "<?php echo $settings['iq_' . $type . '_chart_type']; ?>",
                    height: parseInt("<?php echo $settings['iq_' . $type . '_chart_height']; ?>"),
                    locales: [JSON.parse('<?php echo apexChartProLocales(); ?>')],
                    defaultLocale: "en",
                    sparkline: {
                        enabled: true
                    },
                },
                stroke: {
                    curve: "<?php echo $settings['iq_' . $type . '_chart_stroke_curve']; ?>",
                    lineCap: "<?php echo $settings['iq_' . $type . '_chart_stroke_line_cap']; ?>",
                    width: parseInt("<?php echo $settings['iq_' . $type . '_chart_stroke_width'] ?>"),
                    dashArray: parseInt("<?php echo $settings['iq_' . $type . '_chart_stroke_dash'] ?>"),
                    colors:<?php echo $stokeColor ?>,
                },
                fill: {
                    type: '<?php echo $settings['iq_' . $type . '_chart_fill_style_type']; ?>',
                    opacity: parseFloat("<?php echo $settings['iq_' . $type . '_chart_fill_opacity']; ?>"),
                    colors: ["<?php echo strval($settings['iq_' . $type . '_chart_gradient_1']) ?>"],
                    gradient: {
                        inverseColors: '<?php echo $settings['iq_' . $type . '_chart_gradient_inversecolor'] === "yes"; ?>',
                        gradientToColors: ['<?php echo strval($settings['iq_' . $type . '_chart_gradient_2']); ?>'],
                        type: '<?php echo $settings['iq_' . $type . '_chart_gradient_type']; ?>',
                        opacityFrom: parseFloat("<?php echo $settings['iq_' . $type . '_chart_gradient_opacityFrom']; ?>"),
                        opacityTo: parseFloat("<?php echo $settings['iq_' . $type . '_chart_gradient_opacityTo']; ?>")
                    },
                    pattern: {
                        style: '<?php echo $settings['iq_' . $type . '_chart_pattern']; ?>',
                        width: 6,
                        height: 6,
                        strokeWidth: 2
                    }
                },
                plotOptions: {
                    bar: {
                        endingShape: "<?php echo $settings['iq_' . $type . '_chart_plot_end_shape']; ?>",
                        columnWidth: "<?php echo !empty($settings['iq_' . $type . '_chart_plot_width']) && !empty($settings['iq_' . $type . '_chart_plot_width']['size']) ? $settings['iq_' . $type . '_chart_plot_width']['size'] : 70; ?>" + '%',
                    }
                },
                tooltip: {
                    enabled: true,
                    x: {
                        show: false
                    }
                },
                yaxis: {
                    min: 0
                },
                xaxis: {
                    crosshairs: false
                },
                colors: ["<?php echo strval($settings['iq_' . $type . '_chart_gradient_1']) ?>"]
            };

            if ("<?php echo $settings['iq_' . $type . '_element_show_chart']; ?>" === "yes") {
                initNowGraphina(
                    myElement,
                    {
                        ele: document.querySelector(".graphina-counter-chart-<?php esc_attr_e($widgetId); ?>"),
                        options: counterChartOptions
                    },
                    '<?php esc_attr_e($widgetId); ?>'
                );
            }

            if (element) {
                var end = parseFloat(element.getAttribute('data-end'));
                var decimal = end.toString().split('.');
                var decimalPoint = decimal[decimal.length - 1].length;
                var steps = decimal.length > 1 ? (end / 1000).toFixed(decimalPoint) : parseInt((end / 1000).toString());
                let stepsMatch = Math.sign(steps)>=0 ? parseInt(Math.ceil(steps).toString()) : parseInt(Math.floor(steps).toString());
                myCounter[counterId] = {
                    id: counterId,
                    ele: element,
                    start: parseFloat(element.getAttribute('data-start')),
                    end: end,
                    steps: stepsMatch === 0 ? 1 : parseFloat(steps),
                    points: decimalPoint,
                    isDecimal: decimal.length > 1,
                    separator: '<?php  echo  esc_html($separator); ?>',
                    prefix: '<?php  echo  esc_html($prefix); ?>',
                    postfix: '<?php  echo   esc_html($postfix); ?>',
                    speed: parseInt(element.getAttribute('data-speed')),
                    decimal: end.toString().split('.'),
                    count: 0
                }

                counterInit[counterId] = false;
                myCounterInterval[counterId] = 0;
                 // old code

                //if (isInViewport(myCounter['<?php //esc_attr_e($widgetId); ?>//'].ele) && ('<?php //esc_attr_e($widgetId); ?>//' in counterInit) && counterInit['<?php //esc_attr_e($widgetId); ?>//'] === false) {
                //    counterInit['<?php //esc_attr_e($widgetId); ?>//'] = true;
                //    myCounterInterval['<?php //esc_attr_e($widgetId); ?>//'] = setInterval(function () {
                //        myCounter['<?php //esc_attr_e($widgetId); ?>//'].count++;
                //        updateCounter(myCounter['<?php //esc_attr_e($widgetId); ?>//'].ele, myCounter['<?php //esc_attr_e($widgetId); ?>//'], '<?php //esc_attr_e($widgetId); ?>//');
                //    }, parseInt(5000 / (myCounter['<?php //esc_attr_e($widgetId); ?>//'].speed > 0 ? myCounter['<?php //esc_attr_e($widgetId); ?>//'].speed : 1)));
                //}
                //document.addEventListener('scroll', function () {
                //    if (isInViewport(myCounter['<?php //esc_attr_e($widgetId); ?>//'].ele) && ('<?php //esc_attr_e($widgetId); ?>//' in counterInit) && counterInit['<?php //esc_attr_e($widgetId); ?>//'] === false) {
                //        counterInit['<?php //esc_attr_e($widgetId); ?>//'] = true;
                //        myCounterInterval['<?php //esc_attr_e($widgetId); ?>//'] = setInterval(function () {
                //            myCounter['<?php //esc_attr_e($widgetId); ?>//'].count++;
                //            updateCounter(myCounter['<?php //esc_attr_e($widgetId); ?>//'].ele, myCounter['<?php //esc_attr_e($widgetId); ?>//'], '<?php //esc_attr_e($widgetId); ?>//');
                //        }, parseInt(5000 / (myCounter['<?php //esc_attr_e($widgetId); ?>//'].speed > 0 ? myCounter['<?php //esc_attr_e($widgetId); ?>//'].speed : 1)));
                //    }
                //});

                if(('<?php esc_attr_e($widgetId); ?>' in counterInit) && counterInit['<?php esc_attr_e($widgetId); ?>'] === false){
                    if('<?php echo graphina_common_setting_get('view_port') === 'on' ?>'){
                        counterInit['<?php esc_attr_e($widgetId); ?>'] = true;
                        myCounterInterval['<?php esc_attr_e($widgetId); ?>'] = setInterval(function () {
                            myCounter['<?php esc_attr_e($widgetId); ?>'].count++;
                            updateCounter(myCounter['<?php esc_attr_e($widgetId); ?>'].ele, myCounter['<?php esc_attr_e($widgetId); ?>'], '<?php esc_attr_e($widgetId); ?>');
                        }, parseInt(5000 / (myCounter['<?php esc_attr_e($widgetId); ?>'].speed > 0 ? myCounter['<?php esc_attr_e($widgetId); ?>'].speed : 1)));
                    }else{
                        const observer = new IntersectionObserver(enteries => {
                            enteries.forEach(entry =>{
                                if(entry.isIntersecting){
                                    counterInit['<?php esc_attr_e($widgetId); ?>'] = true;
                                    myCounterInterval['<?php esc_attr_e($widgetId); ?>'] = setInterval(function () {
                                        myCounter['<?php esc_attr_e($widgetId); ?>'].count++;
                                        updateCounter(myCounter['<?php esc_attr_e($widgetId); ?>'].ele, myCounter['<?php esc_attr_e($widgetId); ?>'], '<?php esc_attr_e($widgetId); ?>');
                                    }, parseInt(5000 / (myCounter['<?php esc_attr_e($widgetId); ?>'].speed > 0 ? myCounter['<?php esc_attr_e($widgetId); ?>'].speed : 1)));
                                }
                            })
                        })
                        observer.observe(myCounter['<?php esc_attr_e($widgetId); ?>'].ele);
                    }
                }
            }

            function numberWithCommas(x, separator) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
            }

            function updateCounter(ele, options, id) {
                if (ele == null) {
                    clearInterval(myCounterInterval[id]);
                }
                let start = options.start + ((options.steps * options.count) + (Math.random() * (options.steps / 1.9)));
                let val = options.isDecimal ? parseFloat(start).toFixed(options.points) : parseInt(start);
                if (start >= options.end) {
                    val = options.end;
                    ele.textContent = (options.prefix !== '' ? options.prefix : '') + numberWithCommas(val, options.separator) + (options.postfix !== '' ? options.postfix : '');
                    clearInterval(myCounterInterval);
                }
                ele.textContent = (options.prefix !== '' ? options.prefix : '') + '<?php echo $sign ;?>' +numberWithCommas(val, options.separator) + (options.postfix !== '' ? options.postfix : '');
            }

        </script>
        <?php
        }
    }

}

Plugin::instance()->widgets_manager->register(new Counter());