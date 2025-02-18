<?php

namespace Elementor;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

class Geo_google_chart extends Widget_Base{

    public function __construct($data = [], $args = null)
    {
        wp_register_script('googlecharts-min', GRAPHINA_LITE_URL.'/elementor/js/gstatic/loader.js', [], GRAPHINA_LITE_CURRENT_VERSION, true);
        parent::__construct($data, $args);
    }

    public function get_script_depends() {
        return [
            'googlecharts-min'
        ];
    }

    public function get_name()
    {
        return 'geo_google_chart';
    }

    public function get_title()
    {
        return 'Geo';
    }

    public function get_icon()
    {
//        return 'fas fa-globe-asia';
        return 'graphina-google-geo-chart';
    }

    public function get_categories()
    {
        return ['iq-graphina-google-charts'];
    }

    public function get_chart_type()
    {
        return 'geo_google';
    }

    public function register_controls()
    {
        $type = $this->get_chart_type();
        $this->color = graphina_colors('color');
        $this->gradientColor = graphina_colors('gradientColor');

        graphina_basic_setting($this, $type);

        graphina_chart_data_option_setting($this, $type, 0, true);

        $this->start_controls_section(
            'iq_' . $type . '_section_2',
            [
                'label' => esc_html__('Chart Setting', 'graphina-pro-charts-for-elementor'),
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_settings_heading',
            [
                'label' => esc_html__('Chart Configuration','graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        // $this->add_control(
        //     'iq_' . $type . '_google_chart_width',
        //     [
        //         'label' => esc_html__('Width','graphina-pro-charts-for-elementor'),
        //         'type' => Controls_Manager::NUMBER,
        //         'min' => 0,
        //         'default' => 560
        //     ]
        // );

        $this->add_control(
            'iq_' . $type . '_google_chart_height',
            [
                'label' => esc_html__('Height','graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'default' => 360
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_region_show',
            [
                'label' => esc_html__('Show Region','graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-charts-for-elementor'),
                'label_off' => esc_html__('Show', 'graphina-charts-for-elementor'),
                'description' => __('Note: Enable it to highlight the region of the particular country, Click <strong><a href="https://developers.google.com/chart/interactive/docs/gallery/geochart#regions-mode-format" target="_blank">here</a></strong> for more information','graphina-charts-for-elementor')
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_label_text',
            [
                'label' => esc_html__('label','graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Latitude','graphina-pro-charts-for-elementor')
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_region',
            [
                'label' => esc_html__('Region','graphina-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'iq_' . $type . '_google_chart_region_show' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_geo_background',
            [
                'label' => esc_html__('Background', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#81d4fa'
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_background_stroke_color',
            [
                'label' => esc_html__('Stroke Color', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::COLOR
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_background_stroke_width',
            [
                'label' => esc_html__('Stroke Width','graphina-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'default' => 0
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_geo_default_color',
            [
                'label' => esc_html__('Geo Default Color', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::COLOR
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_geo_data_less_color',
            [
                'label' => esc_html__('Geo No Data Region', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fbffee'
            ]
        );

        graphina_tooltip($this, $type);

        $this->add_control(
            'iq_' . $type . '_chart_hr_category_listing',
            [
                'type' => Controls_Manager::DIVIDER,
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => 'manual'
                ],
            ]
        );


        $repeater = new Repeater();

        $repeater->add_control(
            'iq_' . $type . '_chart_category',
            [
                'label' => esc_html__('Category Value', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Add Value', 'graphina-charts-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
                'description' => esc_html__('Note: For multiline text seperate Text by comma(,) ','graphina-charts-for-elementor')
            ]
        );

        /** Chart value list. */
        $this->add_control(
            'iq_' . $type . '_category_list',
            [
                'label' => esc_html__('Categories', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['iq_' . $type . '_chart_category' => 'Germany'],
                    ['iq_' . $type . '_chart_category' => 'Japan'],
                    ['iq_' . $type . '_chart_category' => 'Mexico'],
                    ['iq_' . $type . '_chart_category' => 'India'],
                    ['iq_' . $type . '_chart_category' => 'South Africa'],
                    ['iq_' . $type . '_chart_category' => 'Russia'],
                ],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => 'manual'
                ],
                'title_field' => '{{{ iq_' . $type . '_chart_category }}}',
            ]
        );

        $this->end_controls_section();

        graphina_advance_legend_setting($this, $type);

        $this->start_controls_section(
            'iq_' . $type . '_section_color_axis',
            [
                'label' => esc_html__('Color Axis', 'graphina-charts-for-elementor'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'iq_' . $type . '_chart_color_axis_index',
            [
                'label' => esc_html__('Color Axis', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_color_axis',
            [
                'label' => esc_html__('Color Axis', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['iq_' . $type . '_chart_color_axis_index' => '#f8bbd0'],
                    ['iq_' . $type . '_chart_color_axis_index' => '#00853f'],
                    ['iq_' . $type . '_chart_color_axis_index' => '#e31b23']
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'iq_' . $type . '_chart_color_axis_number',
            [
                'label' => esc_html__('Color Axis Value', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_color_axis_value',
            [
                'label' => esc_html__('Color Axis Value', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['iq_' . $type . '_chart_color_axis_number' => 0],
                    ['iq_' . $type . '_chart_color_axis_number' => 10],
                    ['iq_' . $type . '_chart_color_axis_number' => 20]
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'iq_' . $type . '_section_3_element_setting',
            [
                'label' => esc_html__('Element Settings', 'graphina-charts-for-elementor'),
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => 'manual'
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_chart_is_pro',
                                    'operator' => '==',
                                    'value' => 'false'
                                ],
                                [
                                    'name' => 'iq_' . $type . '_chart_data_option',
                                    'operator' => '==',
                                    'value' => 'manual'
                                ]
                            ]
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_chart_is_pro',
                                    'operator' => '==',
                                    'value' => 'true'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_title_3_element_setting',
            [
                'label' => esc_html__('Title','graphina-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Add Tile', 'graphina-charts-for-elementor'),
                'default' => 'Element',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'iq_' . $type . '_chart_value_3_element_setting',
            [
                'label' => esc_html__('Element Value','graphina-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => esc_html__('Add Value', 'graphina-charts-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        /** Chart value list. */
        $this->add_control(
            'iq_' . $type . '_value_list_3_1_element_setting',
            [
                'label' => esc_html__('Values', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['iq_' . $type . '_chart_value_3_element_setting' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value_3_element_setting' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value_3_element_setting' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value_3_element_setting' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value_3_element_setting' => rand(10, 200)],
                    ['iq_' . $type . '_chart_value_3_element_setting' => rand(10, 200)]
                ],
                'title_field' => '{{{ iq_' . $type . '_chart_value_3_element_setting }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'iq_' . $type . '_style_section',
            [
                'label' => esc_html__('Style Section', 'graphina-charts-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'iq_' . $type . '_chart_card_show' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'iq_' . $type . '_title_options',
            [
                'label' => esc_html__('Title', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => ['iq_' . $type . '_is_card_heading_show' => 'yes'],
            ]
        );
        /** Header typography. */
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'iq_' . $type . '_card_title_typography',
                'label' => esc_html__('Typography', 'graphina-charts-for-elementor'),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .graphina-chart-heading',
                'condition' => ['iq_' . $type . '_is_card_heading_show' => 'yes']
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_title_align',
            [
                'label' => esc_html__('Alignment', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'graphina-charts-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'graphina-charts-for-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'graphina-charts-for-elementor'),
                        'icon' => 'fa fa-align-right',
                    ]
                ],
                'condition' => [
                    'iq_' . $type . '_is_card_heading_show' => 'yes'
                ]
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_title_font_color',
            [
                'label' => esc_html__('Font Color', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_title_margin',
            [
                'label' => esc_html__('Margin', 'graphina-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [
                    'iq_' . $type . '_is_card_heading_show' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .graphina-chart-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_title_padding',
            [
                'label' => esc_html__('Padding', 'graphina-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [
                    'iq_' . $type . '_is_card_heading_show' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .graphina-chart-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_subtitle_options',
            [
                'label' => esc_html__('Description', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => ['iq_' . $type . '_is_card_desc_show' => 'yes']
            ]
        );
    
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'iq_' . $type . '_subtitle_typography',
                'label' => esc_html__('Typography', 'graphina-charts-for-elementor'),
                'scheme' => Scheme_Typography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .graphina-chart-sub-heading',
                'condition' => ['iq_' . $type . '_is_card_desc_show' => 'yes']
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_subtitle_align',
            [
                'label' => esc_html__('Alignment', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'graphina-charts-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'graphina-charts-for-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'graphina-charts-for-elementor'),
                        'icon' => 'fa fa-align-right',
                    ]
                ],
                'condition' => [
                    'iq_' . $type . '_is_card_heading_show' => 'yes'
                ]
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_subtitle_font_color',
            [
                'label' => esc_html__('Font Color', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_subtitle_margin',
            [
                'label' => esc_html__('Margin', 'graphina-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [
                    'iq_' . $type . '_is_card_heading_show' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .graphina-chart-sub-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_subtitle_padding',
            [
                'label' => esc_html__('Padding', 'graphina-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [
                    'iq_' . $type . '_is_card_heading_show' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .graphina-chart-sub-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();


        $this->start_controls_section(
            'iq_' . $type . '_card_style_section',
            [
                'label' => esc_html__('Card Style', 'graphina-charts-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'iq_' . $type . '_chart_card_show' => 'yes'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'iq_' . $type . '_card_background',
                'label' => esc_html__('Background', 'graphina-charts-for-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .chart-card',
                'condition' => [
                    'iq_' . $type . '_chart_card_show' => 'yes'
                ]
            ]
        );
    
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'iq_' . $type . '_card_box_shadow',
                'label' => esc_html__('Box Shadow', 'graphina-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .chart-card',
                'condition' => ['iq_' . $type . '_chart_card_show' => 'yes']
            ]
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'iq_' . $type . '_card_border',
                'label' => esc_html__('Border', 'graphina-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .chart-card',
                'condition' => ['iq_' . $type . '_chart_card_show' => 'yes']
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'graphina-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [
                    'iq_' . $type . '_chart_card_show' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .chart-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'iq_' . $type . '_chart_style_section',
            [
                'label' => esc_html__('Chart Style', 'graphina-charts-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_border_show',
            [
                'label' => esc_html__('Chart Box', 'graphina-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-charts-for-elementor'),
                'label_off' => esc_html__('Show', 'graphina-charts-for-elementor'),
                'default' => 'yes'
            ]
        );
    
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'iq_' . $type . '_chart_background',
                'label' => esc_html__('Background', 'graphina-charts-for-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .chart-box',
                'condition' => [
                    'iq_' . $type . '_chart_border_show' => 'yes'
                ]
            ]
        );
    
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'iq_' . $type . '_chart_box_shadow',
                'label' => esc_html__('Box Shadow', 'graphina-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .chart-box',
                'condition' => ['iq_' . $type . '_chart_border_show' => 'yes']
            ]
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'iq_' . $type . '_chart_border',
                'label' => esc_html__('Border', 'graphina-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .chart-box',
                'condition' => ['iq_' . $type . '_chart_border_show' => 'yes']
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_chart_border_radius',
            [
                'label' => esc_html__('Border Radius', 'graphina-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [
                    'iq_' . $type . '_chart_border_show' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .chart-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
                ],
            ]
        );
  
        $this->end_controls_section();
    }

    public function render()
    {

        $type = $this->get_chart_type();
        $settings = $this->get_settings_for_display();
        $mainId = $this->get_id();
        $columnData = $valueList = $elementTitleArray = [];
        $elementTitle = $settings['iq_' . $type . '_chart_title_3_element_setting'];
        array_push( $elementTitleArray, $elementTitle );

        $colorAxis = !empty($settings['iq_' . $type . '_chart_color_axis']) ? $settings['iq_' . $type . '_chart_color_axis'] : [''];
                $colorAxisCount = count($colorAxis);
        
                $colorAxisNum = !empty($settings['iq_' . $type . '_chart_color_axis_value']) ? $settings['iq_' . $type . '_chart_color_axis_value'] : [''];
                $colorAxisvalueNum = count($colorAxisNum);
        
                if($colorAxisCount == $colorAxisvalueNum || $colorAxisCount >= $colorAxisvalueNum ){
        
                    $colorAxisData = [];
                    foreach ($colorAxis as $key1 => $value1){
                        array_push($colorAxisData, $value1['iq_' . $type . '_chart_color_axis_index']);
                    }
        
                    $colorAxisValue = [];
                    foreach ($colorAxisNum as $key2 => $value2){
                        array_push($colorAxisValue, $value2['iq_' . $type . '_chart_color_axis_number']);
                    }
                }

        $data = ['series' => [], 'category' => []];
        $dataTypeOption = $settings['iq_' . $type . '_chart_data_option'] === 'manual' ? 'manual' : $settings['iq_' . $type . '_chart_dynamic_data_option'];
        if($settings['iq_' . $type . '_chart_data_option'] === 'manual'){
            $categoryList = $settings['iq_' . $type . '_category_list'];
            $categoryListCount = count($categoryList);
            foreach ($categoryList as $key => $value) {
                $temp = [];
                array_push($temp, $value['iq_' . $type . '_chart_category']);
                array_push($columnData, $temp);
            }
            $columnDataElementCount = count($settings['iq_' . $type . '_category_list']);
            $valueList = $settings['iq_' . $type . '_value_list_3_1_element_setting'];
            $valueListCount = count($valueList);
            if($valueListCount < $categoryListCount){
                $diff = $categoryListCount - $valueListCount;
                for ($k = 0; $k < $diff; $k++) {
                    $random_array = [];
                    array_push( $valueList, $random_array );
                }
            }
            foreach ($valueList as $key => $value) {
                array_push( $columnData[$key], $value['iq_'.$type.'_chart_value_3_element_setting']);
            }
        }else{
            $data = graphinaGoogleChartDynamicData($this, $data);
            if (isset($data['fail']) && $data['fail'] === 'permission') {
                switch ($dataTypeOption) {
                    case "google-sheet" :
                        echo "<pre><b>" . esc_html__('Please check file sharing permission and "Publish As" type is CSV or not. ',  'graphina-pro-charts-for-elementor') . "</b><small><a target='_blank' href='https://youtu.be/Dv8s4QxZlDk'>" . esc_html__('Click for reference.',  'graphina-pro-charts-for-elementor') . "</a></small></pre>";
                        return;
                        break;
                    case "remote-csv" :
                    default:
                        echo "<pre><b>" . (isset($data['errorMessage']) ? $data['errorMessage'] :  esc_html__('Please check file sharing permission.',  'graphina-pro-charts-for-elementor')). "</b></pre>";
                        return;
                        break;
                }
            }
            if(!empty($data['series']) && count($data['series']) > 0 && !empty($data['category']) && count($data['category']) > 0){
                $seriesListCount = count($data['series']);
                $columnData = [];
                $datas = [];
                foreach ($data['series'] as $key3 => $value3){
                    $datas[$key3] = [$data['category'][$key3]];
                    array_push($datas[$key3],$value3);
                }
                $columnData = $datas;
            }
        }
        
        $colorAxisData = json_encode($colorAxisData);
        $colorAxisValue = json_encode($colorAxisValue);
        $elementTitleArray =  json_encode($elementTitleArray);
        $columnData = json_encode($columnData);
        require GRAPHINA_PRO_ROOT . '/elementor/google_charts/geo/render/geo_google_chart.php';
        if( isRestrictedAccess($type,$this->get_id(),$settings,false) === false)
        {
        ?>
        <script type="text/javascript">

            (function($) {
                'use strict';
                if(parent.document.querySelector('.elementor-editor-active') !== null){
                    if (typeof isInit === 'undefined') {
                        var isInit = {};
                    }
                    isInit['<?php esc_attr_e($mainId); ?>'] = false;
                    google.charts.load('current', {'packages': ['geochart']});
                    google.charts.setOnLoadCallback(drawRegionsMap);
                }
                document.addEventListener('readystatechange', event => {
                    // When window loaded ( external resources are loaded too- `css`,`src`, etc...)
                    if (event.target.readyState === "complete") {
                        if (typeof isInit === 'undefined') {
                            var isInit = {};
                        }
                        isInit['<?php esc_attr_e($mainId); ?>'] = false;
                        google.charts.load('current', {'packages': ['geochart']});
                        google.charts.setOnLoadCallback(drawRegionsMap);
                    }
                })

                function drawRegionsMap() {
                    var elementTitleArray = <?php print_r($elementTitleArray); ?>;
                    var elementColorAxis = <?php echo !empty($colorAxisData) ? $colorAxisData : ''; ?>;
                    var elementColorValue = <?php echo !empty($colorAxisValue) ? $colorAxisValue : ''; ?>;

                    var data = new google.visualization.DataTable();
                    data.addColumn('string', '<?php echo strval($settings['iq_' . $type . '_chart_title_3_element_setting']); ?>')
                    data.addColumn('number', '<?php echo strval($settings['iq_' . $type . '_google_chart_label_text']); ?>')

                    data.addRows(<?php print_r($columnData); ?>);

                    var options = {
                        <?php
                        if( !empty($settings['iq_' . $type . '_google_chart_region'])){
                        ?>
                        region: '<?php echo !empty($settings['iq_' . $type . '_google_chart_region']) ? $settings['iq_' . $type . '_google_chart_region'] : '' ;?>',
                        displayMode : 'region',
                        resolution: 'provinces',
                        <?php
                        }
                        ?>
                        // forceIFrame: true,
                        // width: '<?php //echo !empty($settings['iq_' . $type . '_google_chart_width']) ? $settings['iq_' . $type . '_google_chart_width'] : '';?>',
                        width: '100%',
                        height: '<?php echo !empty($settings['iq_' . $type . '_google_chart_height']) ? $settings['iq_' . $type . '_google_chart_height'] : '';?>',
                        enableRegionInteractivity: '<?php echo empty($settings['iq_' . $type . '_chart_tooltip_show']) ? 'false' : 'true';?>',
                        colorAxis: {
                            colors: elementColorAxis,
                            values: elementColorValue
                        },
                        backgroundColor : {
                            fill: '<?php echo !empty($settings['iq_' . $type . '_google_geo_background']) ? $settings['iq_' . $type . '_google_geo_background'] : '';?>',
                            stroke: '<?php echo !empty($settings['iq_' . $type . '_google_background_stroke_color']) ? $settings['iq_' . $type . '_google_background_stroke_color'] : '';?>',
                            strokeWidth: '<?php echo !empty($settings['iq_' . $type . '_google_background_stroke_width']) ? $settings['iq_' . $type . '_google_background_stroke_width'] : '';?>'
                        },
                        defaultColor : '<?php echo !empty($settings['iq_' . $type . '_chart_geo_default_color']) ? $settings['iq_' . $type . '_chart_geo_default_color'] : '';?>',
                        datalessRegionColor : '<?php echo !empty($settings['iq_' . $type . '_chart_geo_data_less_color']) ? $settings['iq_' . $type . '_chart_geo_data_less_color'] : '';?>',
                        tooltip:{
                            textStyle: {
                                fontName: '<?php echo !empty($settings['iq_' . $type . '_legend_typography_font_family']) ? $settings['iq_' . $type . '_legend_typography_font_family'] : '';?>',
                                color: '<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_color']) ? $settings['iq_' . $type . '_chart_tooltip_color'] : '#000000';?>',
                                fontSize: '<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_font_size']) ? $settings['iq_' . $type . '_chart_tooltip_font_size'] : '';?>',
                                bold: '<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_bold']) && $settings['iq_' . $type . '_chart_tooltip_bold'] == 'yes' ? 'true' : 'false';?>',
                                italic: '<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_italic']) && $settings['iq_' . $type . '_chart_tooltip_italic'] == 'yes' ? 'true' : 'false';?>'
                            },
                            trigger: '<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_trigger']) ? $settings['iq_' . $type . '_chart_tooltip_trigger'] : '';?>'
                        }
                    };

                    if('<?php echo $settings['iq_' . $type . '_google_chart_legend_show'] === 'yes' ?>'){
                        options.legend = {
                            textStyle: {
                                fontName: '<?php echo !empty($settings['iq_' . $type . '_legend_typography_font_family']) ? $settings['iq_' . $type . '_legend_typography_font_family'] : '';?>',
                                color: '<?php echo !empty($settings['iq_' . $type . '_google_legend_color']) ? $settings['iq_' . $type . '_google_legend_color'] : '#000000';?>',
                                fontSize: '<?php echo !empty($settings['iq_' . $type . '_google_legend_size']) ? $settings['iq_' . $type . '_google_legend_size'] : '';?>',
                                bold: '<?php echo !empty($settings['iq_' . $type . '_google_legend_bold']) && $settings['iq_' . $type . '_google_legend_bold'] == 'yes' ? 'true' : 'false';?>',
                                italic: '<?php echo !empty($settings['iq_' . $type . '_google_legend_italic']) && $settings['iq_' . $type . '_google_legend_italic'] == 'yes' ? 'true' : 'false';?>'
                            },
                            numberFormat: '<?php echo !empty($settings['iq_' . $type . '_google_legend_format']) && $settings['iq_' . $type . '_google_legend_format'] == 'yes' ? '.###' : '';?>'
                        }
                    }else{
                        options.legend = 'none';
                    }
                    if (typeof graphinaGoogleChartInit !== "undefined") {
                        graphinaGoogleChartInit(
                            document.getElementById('geo_google_chart<?php esc_attr_e($this->get_id()); ?>'),
                            {
                                ele:document.getElementById('geo_google_chart<?php esc_attr_e($this->get_id()); ?>'),
                                options: options,
                                series: data,
                                animation: true,
                                renderType:'GeoChart'
                            },
                            '<?php esc_attr_e($mainId); ?>',
                            '<?php echo $this->get_chart_type(); ?>',
                        );
                    }
                }

            }).apply(this, [jQuery]);
        </script>

        <?php
        }
    }
}


Plugin::instance()->widgets_manager->register(new Geo_google_chart());