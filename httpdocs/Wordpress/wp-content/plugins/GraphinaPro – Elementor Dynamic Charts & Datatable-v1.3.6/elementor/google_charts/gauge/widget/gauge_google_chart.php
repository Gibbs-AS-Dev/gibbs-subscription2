<?php

namespace Elementor;

Use Elementor\Core\Schemes\Typography as Scheme_Typography;

class Gauge_google_chart extends Widget_Base{

    public $defaultLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan1', 'Feb1', 'Mar1', 'Apr1', 'May1','Jun1', 'July1', 'Aug1', 'Sep1', 'Oct1', 'Nov1', 'Dec1', 'Jan2', 'Feb2', 'Mar2', 'Apr2', 'May2', 'Jun2', 'July2', 'Aug2',];

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
        return 'gauge_google_chart';
    }

    public function get_title()
    {
        return 'Gauge';
    }

    public function get_icon()
    {
        return 'graphina-google-gauge-chart';
//        return 'fas fa-tachometer-alt';
    }

    public function get_categories()
    {
        return ['iq-graphina-google-charts'];
    }

    public function get_chart_type()
    {
      return 'gauge_google';
    }

    public function register_controls()
    {
        $type = $this->get_chart_type();
        $this->color = graphina_colors('color');
        $this->gradientColor = graphina_colors('gradientColor');
  
        graphina_basic_setting($this, $type);
  
        graphina_chart_data_option_setting($this, $type, 2, true);
  
        $this->start_controls_section(
          'iq_' . $type . '_section_2',
          [
              'label' => esc_html__('Chart Setting', 'graphina-pro-charts-for-elementor'),
          ]
       );

       $this->add_control(
        'iq_' . $type . '_chart_title_3_element_setting',
        [
            'label' => esc_html__('Title','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => esc_html__('Add Tile', 'graphina-pro-charts-for-elementor'),
            'default' => 'Element',
            'dynamic' => [
                'active' => true,
            ],
        ]
    );
  
       $this->add_control(
          'iq_' . $type . '_google_chart_meter_width',
          [
              'label' => esc_html__('Width','graphina-pro-charts-for-elementor'),
              'type' => Controls_Manager::NUMBER,
              'step' => 10,
              'min' => 0
          ]
       );
  
       $this->add_control(
        'iq_' . $type . '_google_chart_meter_height',
        [
            'label' => esc_html__('Height','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 350,
            'min' => 0
        ]
     );

        $this->add_control(
            'iq_' . $type . '_chart_hr_ticks_prefix_1',
            [
                'type' => Controls_Manager::DIVIDER
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_value_prefix',
            [
                'label' => esc_html__('Value Prefix','graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_value_postfix',
            [
                'label' => esc_html__('Value Postfix','graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_value_decimal',
            [
                'label' => esc_html__('Decimal in float','graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'step' => 1,
                'min' => 0
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_hr_ticks_prefix_2',
            [
                'type' => Controls_Manager::DIVIDER
            ]
        );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_min_value',
        [
            'label' => esc_html__('Min Value','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_max_value',
        [
            'label' => esc_html__('Max Value','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 200,
            'min' => 0
        ]
        );

     $this->add_control(
        'iq_' . $type . '_chart_hr_ticks_color',
        [
            'type' => Controls_Manager::DIVIDER
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_ticks_color',
        [
            'label' => esc_html__('Ticks Color From To', 'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::HEADING
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_green_from',
        [
            'label' => esc_html__('Green From','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_green_to',
        [
            'label' => esc_html__('Green To','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 50,
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_yellow_from',
        [
            'label' => esc_html__('Yellow From','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 50,
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_yellow_to',
        [
            'label' => esc_html__('Yellow To','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 150,
            'min' => 0
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_red_from',
        [
            'label' => esc_html__('Red From','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 150,
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_meter_red_to',
        [
            'label' => esc_html__('Red To','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 200,
        ]
     );

     $this->add_control(
        'iq_' . $type . '_chart_ticks_color_divider',
        [
            'type' => Controls_Manager::DIVIDER
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_ticks_color_hr',
        [
            'label' => esc_html__('Ticks Color', 'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::HEADING
        ]
     );

     $this->add_control(
         'iq_' . $type . '_chart_ticks_green_color',
         [
            'label' => esc_html__('Green Color', 'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#109618',
         ]
     );

     $this->add_control(
        'iq_' . $type . '_chart_ticks_yellow_color',
        [
           'label' => esc_html__('Yellow Color', 'graphina-pro-charts-for-elementor'),
           'type' => Controls_Manager::COLOR,
           'default' => '#FF9900',
        ]
    );

    $this->add_control(
        'iq_' . $type . '_chart_ticks_red_color',
        [
           'label' => esc_html__('Red Color', 'graphina-pro-charts-for-elementor'),
           'type' => Controls_Manager::COLOR,
           'default' => '#DC3912',
        ]
    );

     $this->add_control(
        'iq_' . $type . '_chart_hr_ticks_setting',
        [
            'type' => Controls_Manager::DIVIDER
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_ticks_settings',
        [
            'label' => esc_html__('Ticks Settings', 'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::HEADING
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_minor_ticks',
        [
            'label' => esc_html__('Minor Ticks', 'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 5,
            'min' => 0
        ]
     );

     $this->add_control(
        'iq_' . $type . '_google_chart_major_ticks_show',
        [
            'label' => esc_html__('Major Ticks Show', 'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Hide', 'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('Show', 'graphina-pro-charts-for-elementor'),
            'default' => 'yes'
        ]
    );

     $repeater = new Repeater();

    $repeater->add_control(
       'iq_' . $type . '_google_chart_major_ticks_value',
       [
           'label' => esc_html__('Major Ticks', 'graphina-pro-charts-for-elementor'),
           'type' => Controls_Manager::NUMBER,
           'dynamic' => [
               'active' => true,
           ],

       ]
    );

    $this->add_control(
        'iq_' . $type . '_google_chart_major_ticks',
        [
            'label' => esc_html__('Ticks', 'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['iq_' . $type . '_google_chart_major_ticks_value' => 0],
                ['iq_' . $type . '_google_chart_major_ticks_value' => 50],
                ['iq_' . $type . '_google_chart_major_ticks_value' => 100],
                ['iq_' . $type . '_google_chart_major_ticks_value' => 150],
                ['iq_' . $type . '_google_chart_major_ticks_value' => 200],
            ],
            'condition' => [
                'iq_' . $type . '_google_chart_major_ticks_show' => 'yes'
            ]
        ]
    );

        $this->add_control(
            'iq_' . $type . '_chart_hr_needle_setting',
            [
                'type' => Controls_Manager::DIVIDER
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_needle_color',
            [
                'label' => esc_html__('Needle Color', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default'=> '#c63310'
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_round_ball_color',
            [
                'label' => esc_html__('Round Ball Color', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default'=> '#4684ee'
            ]
        );

        $this->add_control(
            'iq_' . $type . '_google_chart_inner_circle_color',
            [
                'label' => esc_html__('Inner Circle Color', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default'=> '#f7f7f7'
            ]
        );


        $this->add_control(
            'iq_' . $type . '_google_chart_outer_circle_color',
            [
                'label' => esc_html__('Outer Circle Color', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default'=> '#cccccc'
            ]
        );
    //    graphina_animation($this, $type);

       $this->end_controls_section();

       for($i = 0; $i <= graphina_default_setting('max_series_value'); $i++)
       {
            $this->start_controls_section(
                'iq_' . $type . '_chart_element_section_'.$i,
                [
                    'label' => esc_html__('Element'. ($i + 1),'graphina-pro-charts-for-elementor'),
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range($i + 1, graphina_default_setting('max_series_value')),
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
                'iq_' . $type . '_chart_element_setting_title_'.$i,
                [
                    'label' => esc_html__('Label','graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'default' => $this->defaultLabel[$i],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
        
            $this->add_control(
                'iq_' . $type . '_chart_element_setting_value_'.$i,
                [
                    'label' => esc_html__('Value','graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => rand(5,200),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->end_controls_section();
        }
  
  
        $this->start_controls_section(
            'iq_' . $type . '_style_section',
            [
                'label' => esc_html__('Style Section', 'graphina-pro-charts-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'iq_' . $type . '_chart_card_show' => 'yes'
                ],
            ]
        );
        $this->add_control(
            'iq_' . $type . '_title_options',
            [
                'label' => esc_html__('Title', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => ['iq_' . $type . '_is_card_heading_show' => 'yes'],
            ]
        );
        /** Header typography. */
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'iq_' . $type . '_card_title_typography',
                'label' => esc_html__('Typography', 'graphina-pro-charts-for-elementor'),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .graphina-chart-heading',
                'condition' => ['iq_' . $type . '_is_card_heading_show' => 'yes']
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_title_align',
            [
                'label' => esc_html__('Alignment', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Font Color', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_title_margin',
            [
                'label' => esc_html__('Margin', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Padding', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Description', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => ['iq_' . $type . '_is_card_desc_show' => 'yes']
            ]
        );
    
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'iq_' . $type . '_subtitle_typography',
                'label' => esc_html__('Typography', 'graphina-pro-charts-for-elementor'),
                'scheme' => Scheme_Typography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .graphina-chart-sub-heading',
                'condition' => ['iq_' . $type . '_is_card_desc_show' => 'yes']
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_subtitle_align',
            [
                'label' => esc_html__('Alignment', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Font Color', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_subtitle_margin',
            [
                'label' => esc_html__('Margin', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Padding', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Card Style', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Background', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Box Shadow', 'graphina-pro-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .chart-card',
                'condition' => ['iq_' . $type . '_chart_card_show' => 'yes']
            ]
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'iq_' . $type . '_card_border',
                'label' => esc_html__('Border', 'graphina-pro-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .chart-card',
                'condition' => ['iq_' . $type . '_chart_card_show' => 'yes']
            ]
        );
    
        $this->add_control(
            'iq_' . $type . '_card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'graphina-pro-charts-for-elementor'),
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
                'label' => esc_html__('Chart Style', 'graphina-pro-charts-for-elementor'),
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
        $seriesListCount = $settings['iq_' . $type . '_chart_data_series_count'];
        $columnData = $elementTitleArray = [];

        $majorticks = $settings['iq_' . $type . '_google_chart_major_ticks'];
        if(!empty($majorticks)){
            $majorticksvalue = [];
            foreach($majorticks as $key1 => $value1){
                array_push($majorticksvalue, strval($value1['iq_' . $type . '_google_chart_major_ticks_value']));
            }
            $majorticksvalue = json_encode($majorticksvalue);
        }
        
        $columnDataElementCount = $settings['iq_' . $type . '_chart_data_series_count'];

        for ($j = 0; $j < $columnDataElementCount; $j++) {

            // $data['title'][] = $settings['iq_' . $type . '_chart_element_setting_title_'.$j];
            // $data['value'][] = $settings['iq_' . $type . '_chart_element_setting_value_'.$j];

            $elementTitle = $settings['iq_' . $type . '_chart_element_setting_title_' . $j];
            array_push( $elementTitleArray, $elementTitle );

//            $new_list =[
//                $settings['iq_' . $type . '_chart_element_setting_title_'.$j],
//                $settings['iq_' . $type . '_chart_element_setting_value_'.$j]
//            ];
//            array_push( $columnData, !empty($new_list) ? $new_list : ['a']);
        }

        $data = ['series' => [], 'category' => []];
        $dataTypeOption = $settings['iq_' . $type . '_chart_data_option'] === 'manual' ? 'manual' : $settings['iq_' . $type . '_chart_dynamic_data_option'];
        if($settings['iq_' . $type . '_chart_data_option'] !== 'manual'){
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
            if(!empty($data['category'])){
                foreach ($data['category'] as $key => $va){
                    $new_list = [
                        $va,
                        $data['series'][$key],
                    ];
                    array_push( $columnData, $new_list);
                }
            }
        }else{
            for ($j = 0; $j < $columnDataElementCount; $j++) {
                $new_list = [
                    $settings['iq_' . $type . '_chart_element_setting_title_' . $j],
                    $settings['iq_' . $type . '_chart_element_setting_value_' . $j]
                ];
                array_push($columnData, !empty($new_list) ? $new_list : ['a']);
            }
        }
        $elementTitleArray = json_encode($elementTitleArray);
        $columnData = json_encode($columnData);
        require GRAPHINA_PRO_ROOT . '/elementor/google_charts/gauge/render/gauge_google_chart.php';
        if( isRestrictedAccess($type,$this->get_id(),$settings,false) === false)
        {
        ?>
        <html>
            <head>
                <script type="text/javascript">
                    (function($) {
                        'use strict';
                        if(parent.document.querySelector('.elementor-editor-active') !== null){
                            if (typeof isInit === 'undefined') {
                                var isInit = {};
                            }
                            isInit['<?php esc_attr_e($mainId); ?>'] = false;
                            google.charts.load('current', {'packages':['gauge'],});
                            google.charts.setOnLoadCallback(drawRegionsMap);
                        }
                        document.addEventListener('readystatechange', event => {
                            // When window loaded ( external resources are loaded too- `css`,`src`, etc...)
                            if (event.target.readyState === "complete") {
                                if (typeof isInit === 'undefined') {
                                    var isInit = {};
                                }
                                isInit['<?php esc_attr_e($mainId); ?>'] = false;
                                google.charts.load('current', {'packages':['gauge'],});
                                google.charts.setOnLoadCallback(drawRegionsMap);
                            }
                        })

                        function drawRegionsMap() {
                            var areaseriesListCount = parseInt('<?php $seriesListCount?>');
                            var elementTitleArray = <?php print_r($elementTitleArray); ?>;
                            var data = new google.visualization.DataTable();
                            data.addColumn('string','<?php echo strval($settings['iq_' . $type . '_chart_title_3_element_setting']); ?>')
                            data.addColumn('number','Meter')

                            data.addRows(<?php print_r($columnData); ?>);

                            var formatter = new google.visualization.NumberFormat({
                                prefix: '<?php echo esc_html($settings['iq_' . $type . '_google_chart_value_prefix']);?>',
                                suffix: '<?php echo esc_html($settings['iq_' . $type . '_google_chart_value_postfix']);?>',
                                fractionDigits:'<?php echo esc_html($settings['iq_' . $type . '_google_chart_value_decimal']);?>'
                            });
                            formatter.format(data, 1);

                            var options = {
                                forceIFrame: false,
                                width: parseInt('<?php echo !empty($settings['iq_' . $type . '_google_chart_meter_width']) ? $settings['iq_' . $type . '_google_chart_meter_width'] : '';?>'),
                                height: parseInt('<?php echo !empty($settings['iq_' . $type . '_google_chart_meter_height']) ? $settings['iq_' . $type . '_google_chart_meter_height'] : '';?>'),
                                redFrom: parseInt('<?php echo  $settings['iq_' . $type . '_google_chart_meter_red_from'] ;?>'),
                                redTo: parseInt('<?php echo !empty($settings['iq_' . $type . '_google_chart_meter_red_to']) ? $settings['iq_' . $type . '_google_chart_meter_red_to'] : '';?>'),
                                redColor : '<?php echo !empty($settings['iq_' . $type . '_chart_ticks_red_color']) ? $settings['iq_' . $type . '_chart_ticks_red_color'] : '';?>',
                                yellowFrom: parseInt('<?php echo  $settings['iq_' . $type . '_google_chart_meter_yellow_from'] ;?>'),
                                yellowTo: parseInt('<?php echo  $settings['iq_' . $type . '_google_chart_meter_yellow_to'] ;?>'),
                                yellowColor : '<?php echo !empty($settings['iq_' . $type . '_chart_ticks_yellow_color']) ? $settings['iq_' . $type . '_chart_ticks_yellow_color'] : '';?>',
                                minorTicks: parseInt('<?php echo !empty($settings['iq_' . $type . '_google_chart_minor_ticks']) ? $settings['iq_' . $type . '_google_chart_minor_ticks'] : '';?>'),
                                // animation:{
                                //     duration: parseInt('<?php //echo $settings['iq_' . $type . '_chart_animation_speed']; ?>'),
                                //     easing: '<?php //echo $settings['iq_' . $type . '_chart_animation_easing']; ?>'
                                // },
                                min: parseInt('<?php echo  $settings['iq_' . $type . '_google_chart_meter_min_value'];?>'),
                                max: parseInt('<?php echo !empty($settings['iq_' . $type . '_google_chart_meter_max_value']) ? $settings['iq_' . $type . '_google_chart_meter_max_value'] : '';?>'),
                                greenFrom : parseInt('<?php echo  $settings['iq_' . $type . '_google_chart_meter_green_from'];?>'),
                                greenTo : parseInt('<?php echo !empty($settings['iq_' . $type . '_google_chart_meter_green_to']) ? $settings['iq_' . $type . '_google_chart_meter_green_to'] : '';?>'),
                                greenColor : '<?php echo !empty($settings['iq_' . $type . '_chart_ticks_green_color']) ? $settings['iq_' . $type . '_chart_ticks_green_color'] : '';?>',
                            };

                            if('<?php echo !empty($settings['iq_' . $type . '_google_chart_major_ticks_show']) && $settings['iq_' . $type . '_google_chart_major_ticks_show'] == 'yes' ?>'){
                                options.majorTicks = <?php print_r(!empty($majorticksvalue) ? $majorticksvalue : []) ; ?>;
                            }

                            if (typeof graphinaGoogleChartInit !== "undefined") {
                                graphinaGoogleChartInit(
                                    document.getElementById('gauge_google_chart<?php esc_attr_e($this->get_id()); ?>'),
                                    {
                                        ele:document.getElementById('gauge_google_chart<?php esc_attr_e($this->get_id()); ?>'),
                                        options: options,
                                        series: data,
                                        animation: true,
                                        renderType:'Gauge',
                                        ballColor:'<?php echo strval($settings['iq_' . $type . '_google_chart_round_ball_color']) ;?>',
                                        innerCircleColor:'<?php echo strval($settings['iq_' . $type . '_google_chart_inner_circle_color']) ;?>',
                                        outerCircleColor:'<?php echo strval($settings['iq_' . $type . '_google_chart_outer_circle_color']) ;?>',
                                        needleColor:'<?php echo strval($settings['iq_' . $type . '_google_chart_needle_color']) ;?>'
                                    },
                                    '<?php esc_attr_e($mainId); ?>',
                                    '<?php echo $this->get_chart_type(); ?>',
                                );
                            }
                        }

                    }).apply(this, [jQuery]);
                </script>
            </head>
        </html>

        <?php
        }
    }
}


Plugin::instance()->widgets_manager->register(new Gauge_google_chart());