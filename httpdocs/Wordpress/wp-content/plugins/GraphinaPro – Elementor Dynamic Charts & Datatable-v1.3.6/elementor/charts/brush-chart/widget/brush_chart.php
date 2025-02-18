<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;

/**
 * Elementor Blog widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.5.7
 */
class brush_chart extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve heading widget name.
     *
     * @return string Widget name.
     * @since 1.5.7
     * @access public
     *
     */

    public function get_name()
    {
        return 'brush_chart';
    }

    /**
     * Get widget Title.
     *
     * Retrieve heading widget Title.
     *
     * @return string Widget Title.
     * @since 1.5.7
     * @access public
     *
     */

    public function get_title()
    {
        return 'Brush Chart';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the heading widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @return array Widget categories.
     * @since 1.5.7
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
     * @since 1.5.7
     * @access public
     *
     */

    public function get_icon()
    {
        return 'graphina-apex-brush-chart';
    }

    public function get_chart_type()
    {
        return 'brush';
    }


    protected function register_controls()
    {
        $type = $this->get_chart_type();

        graphina_basic_setting($this, $type);

        graphina_chart_data_option_setting($this, $type, 2, true);

        $this->start_controls_section(
            'iq_' . $type . '_section_2',
            [
                'label' => esc_html__('Chart Setting',  'graphina-pro-charts-for-elementor'),
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_type_1',
            [
                'label' => esc_html__('Chart-1 Type',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'area',
                'options' => [
                    'area' => 'area',
                    'line' => 'line',
                    'bar'=>'Column'
                ],

            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_area_curve_1',
            [
                'label' => esc_html__('Area Shape',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_stroke_curve_type(true),
                'options' => graphina_stroke_curve_type(),
                'condition' => [
                    'iq_' . $type . '_chart_type_1' => 'area'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_line_curve_1',
            [
                'label' => esc_html__('Line Shape',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_stroke_curve_type(true),
                'options' => graphina_stroke_curve_type(),
                'condition' => [
                    'iq_' . $type . '_chart_type_1' => 'line'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_is_chart_stroke_width_chart_1',
            [
                'label' => esc_html__('Column Width',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
                'min' => 1,
                'max' => 100,
                'step' => 10,
                'condition' => [
                    'iq_' . $type . '_chart_type_1' => 'bar'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_type_2',
            [
                'label' => esc_html__('Chart-2 Type',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'bar',
                'options' => [
                    'area' => 'area',
                    'line' => 'line',
                    'bar'=>'Column'
                ],

            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_area_curve_2',
            [
                'label' => esc_html__('Area Shape',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_stroke_curve_type(true),
                'options' => graphina_stroke_curve_type(),
                'condition' => [
                    'iq_' . $type . '_chart_type_2' => 'area'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_line_curve_2',
            [
                'label' => esc_html__('Line Shape',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_stroke_curve_type(true),
                'options' => graphina_stroke_curve_type(),
                'condition' => [
                    'iq_' . $type . '_chart_type_2' => 'line'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_is_chart_stroke_width_chart_2',
            [
                'label' => esc_html__('Column Width',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
                'min' => 1,
                'max' => 100,
                'step' => 10,
                'condition' => [
                    'iq_' . $type . '_chart_type_2' => 'bar'
                ]
            ]
        );

        graphina_common_chart_setting($this, $type, false);

        graphina_tooltip($this, $type);

        graphina_animation($this, $type);

        graphina_dropshadow($this, $type);

        $repeater1 = new Repeater();

        $repeater1->add_control(
            'iq_' . $type . '_chart_category',
            [
                'label' => esc_html__('Category Value',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => esc_html__('Add Value',  'graphina-pro-charts-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        /** Chart Category list. */
        $this->add_control(
            'iq_' . $type . '_category_list',
            [
                'label' => esc_html__('Categories',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater1->get_controls(),
                'default' =>$this->brushCatagoryGenerator($type, 25),
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => 'manual',
                ],
                'title_field' => '{{{ iq_' . $type . '_chart_category }}}',
            ]
        );


        $this->end_controls_section();

        graphina_chart_label_setting($this, $type);

        graphina_selection_setting($this,$type);

        graphina_advance_x_axis_setting($this, $type, true, false);

        graphina_advance_y_axis_setting($this, $type);

        graphina_series_2_setting($this, $type, ['tooltip', 'color', 'dash', 'width','brush-1'], true, ['classic', 'gradient', 'pattern'], true, true);

        $type2 = 'brush_2';

        graphina_series_2_setting($this, $type2, ['tooltip', 'color', 'dash', 'width','brush-2'], true, ['classic', 'gradient', 'pattern'], true, true);

        for ($i = 0; $i < graphina_default_setting('max_series_value'); $i++) {
            $this->start_controls_section(
                'iq_' . $type . '_section_3_' . $i,
                [
                    'label' => esc_html__('Element ' . ($i + 1),  'graphina-pro-charts-for-elementor'),
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value')),
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
                'iq_' . $type . '_chart_title_3_' . $i,
                [
                    'label' => 'Title',
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Add Tile',  'graphina-pro-charts-for-elementor'),
                    'default' => 'Element ' . ($i + 1),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater = new Repeater();

            /** Chart value list. */

            $repeater->add_control(
                'iq_' . $type . '_chart_value_3_' . $i,
                [
                    'label' => 'Element Value',
                    'type' => Controls_Manager::NUMBER,
                    'placeholder' => esc_html__('Add Value',  'graphina-pro-charts-for-elementor'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'iq_' . $type . '_value_list_3_1_' . $i,
                [
                    'label' => esc_html__('Values',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' =>$this->brushDataGenerator($type, $i, 25),
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values!' => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_value_3_' . $i . ' }}}',
                ]
            );

            $this->add_control(
                'iq_' . $type . '_value_list_3_2_' . $i,
                [
                    'label' => esc_html__('Values',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' =>$this->brushDataGeneratorNegative($type, $i, 25),
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values' => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_value_3_' . $i . ' }}}',
                ]
            );

            $this->end_controls_section();

        }

        graphina_style_section($this, $type);

        graphina_card_style($this, $type);

        graphina_chart_style($this, $type);

        if (function_exists('graphina_pro_password_style_section')) {
            graphina_pro_password_style_section($this, $type);
        }
    }

    protected function brushDataGenerator($type = '', $i = 0, $count = 20)
    {
        $result = [];
        for ($j = 0; $j < $count; $j++) {
            $result[] = [
                'iq_' . $type . '_chart_value_3_' . $i => rand(10, 200),
            ];
        }
        return $result;
    }

    protected function brushDataGeneratorNegative($type = '', $i = 0, $count = 20)
    {
        $result = [];
        for ($j = 0; $j < $count; $j++) {
            $result[] = [
                'iq_' . $type . '_chart_value_3_' . $i => rand(-200, 200),
            ];
        }
        return $result;
    }

    protected function brushCatagoryGenerator($type,$count){
        $result = [];

        for ($j = 0; $j < $count; $j++) {
            $result[] = [
                'iq_' . $type . '_chart_category' => $j + 1,
            ];
        }
        return $result;
       }

    protected function render()
    {
        $type = $this->get_chart_type();
        $type2 = 'brush_2';
        $settings = $this->get_settings_for_display();
        $mainId = $this->get_id();
        $second_gradient = [];
        $fill_pattern = [];
        $dropShadowSeries = [];
        $tooltipSeries = [];
        $gradient = [];
        $markerSize = [];
        $markerStrokeColor = [];
        $markerStokeWidth = [];
        $markerShape = [];
        $stockWidth = [];
        $stockDashArray = [];
        $data = ['series' => [], 'category' => []];
        $dataLabelPrefix = $dataLabelPostfix = $yLabelPrefix = $yLabelPostfix = $xLabelPrefix = $xLabelPostfix = '';
        $callAjax = false;
        $loadingText = esc_html__((isset($settings['iq_' . $type . '_chart_no_data_text']) ? $settings['iq_' . $type . '_chart_no_data_text'] : ''),  'graphina-pro-charts-for-elementor');
        $chart2Gradient = [];
        $chart2SecondGradient = [];
        $chart2FillPattern = [];
        $chart2StockWidth = [];
        $chart2StocKDash = [];

        $dataTypeOption = $settings['iq_' . $type . '_chart_data_option'] === 'manual' ? 'manual' : $settings['iq_' . $type . '_chart_dynamic_data_option'];

        switch ($dataTypeOption) {
            case "manual" :
                $categoryList = $settings['iq_' . $type . '_category_list'];
                if (gettype($categoryList) === "NULL") {
                    $categoryList = [];
                }
                foreach ($categoryList as $v) {
                    $data["category"][] = (int)graphina_get_dynamic_tag_data($v, 'iq_' . $type . '_chart_category');
                }
                for ($i = 0; $i < $settings['iq_' . $type . '_chart_data_series_count']; $i++) {
                    $valueList = $settings['iq_' . $type . '_value_list_3_' . ($settings['iq_' . $type . '_can_chart_negative_values'] === 'yes' ? 2 : 1) . '_' . $i];
                    $value = [];
                    if (gettype($valueList) === "NULL") {
                        $valueList = [];
                    }
                    foreach ($valueList as $v) {
                        $value[] = (float)graphina_get_dynamic_tag_data($v, 'iq_' . $type . '_chart_value_3_' . $i);
                    }
                    $data['series'][] = [
                        'name' => (string)graphina_get_dynamic_tag_data($settings, 'iq_' . $type . '_chart_title_3_' . $i),
                        'data' => $value,
                    ];
                }
                break;
            case "csv":
                if(!empty($settings['iq_' . $type . '_chart_csv_column_wise_enable']) && $settings['iq_' . $type . '_chart_csv_column_wise_enable'] === 'yes'){
                    $url = $settings['iq_' . $type . '_chart_upload_csv']['url'];
                    $data = graphina_pro_parse_csv_column_wise($url,'area',$settings,$type);
                    if ($settings['iq_' . $type . '_chart_data_option'] !== 'manual') {
                        ?>
                        <script>
                            if (jQuery('body').hasClass("elementor-editor-active")) {
                                setFieldsForCSV(<?php echo json_encode($settings);?>, <?php echo json_encode($data);?>, '<?php echo $type;?>');
                            };
                        </script>
                        <?php
                    }
                }else{
                    $data = graphina_pro_parse_csv($settings, $type, 'area');
                }
                break;
            case "remote-csv" :
            case "google-sheet" :
                if (!empty($settings['iq_' . $type . '_chart_csv_column_wise_enable']) && $settings['iq_' . $type . '_chart_csv_column_wise_enable'] === 'yes') {
                    $url = $dataTypeOption === 'remote-csv' ? $settings['iq_' . $type . '_chart_import_from_url'] : $settings['iq_' . $type . '_chart_import_from_google_sheet'];
                    $data = graphina_pro_parse_csv_column_wise($url, 'area', $settings, $type);
                    if ($settings['iq_' . $type . '_chart_data_option'] !== 'manual') {
                        ?>
                        <script>
                            if (jQuery('body').hasClass("elementor-editor-active")) {
                                setFieldsForCSV(<?php echo json_encode($settings);?>, <?php echo json_encode($data);?>, '<?php echo $type;?>');
                            };
                        </script>
                        <?php
                    }
                } else {
                    $data = graphina_pro_get_data_from_url($type, $settings, $dataTypeOption, $mainId, 'area');
                }
                break;
            case "api":
                $data = graphina_pro_chart_get_data_from_api($type, $settings, 'area',[]);
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                break;
            case "sql-builder":
                $data = graphina_pro_chart_get_data_from_sql_builder($settings, $type,[]);
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                if($settings['iq_' . $type . '_chart_data_option'] !== 'manual' ) {
                    if ( $settings['iq_' . $type . '_chart_dynamic_data_option'] === "sql-builder") {
                        ?>
                        <script>
                            if(jQuery('body').hasClass("elementor-editor-active")) {
                                setFieldsFromSQLStateMent(<?php echo json_encode($settings);?>, <?php echo json_encode($data);?>, '<?php echo $type;?>');
                            };
                        </script>
                        <?php
                    }
                }
                break;
            case 'filter':
                update_post_meta(get_the_ID(),$mainId,$settings['iq_' . $type . '_element_filter_widget_id']);
                $data = apply_filters('graphina_extra_data_option', $data, $type, $settings,$settings['iq_' . $type . '_element_filter_widget_id']);
                break;
        }
        if($settings['iq_' . $type . '_chart_data_option'] === 'firebase') {
            $data = apply_filters('graphina_addons_render_section', $data, $type, $settings);
        }

        if ($settings['iq_' . $type . '_chart_data_option'] === 'forminator' && graphinaForminatorAddonActive()) {
            $data = apply_filters('graphina_forminator_addon_data', $data, $type, $settings);
            ?>
            <script>
                if (jQuery('body').hasClass("elementor-editor-active")) {
                    setFieldsFromForminator(<?php echo json_encode($settings);?>, <?php echo json_encode($data);?>, '<?php echo $type;?>');
                };
            </script>
            <?php
        }

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

        $data['category']=array_map(function ($x){
           return (int)$x;
        },$data['category']);

        if ($settings['iq_' . $type . '_chart_datalabel_show'] === 'yes') {
            $dataLabelPrefix = $settings['iq_' . $type . '_chart_datalabel_prefix'];
            $dataLabelPostfix = $settings['iq_' . $type . '_chart_datalabel_postfix'];
        }

        if ($settings['iq_' . $type . '_chart_xaxis_label_show'] === 'yes') {
            $xLabelPrefix = $settings['iq_' . $type . '_chart_xaxis_label_prefix'];
            $xLabelPostfix = $settings['iq_' . $type . '_chart_xaxis_label_postfix'];
        }

        if ($settings['iq_' . $type . '_chart_yaxis_label_show'] === 'yes') {
            $yLabelPrefix = $settings['iq_' . $type . '_chart_yaxis_label_prefix'];
            $yLabelPostfix = $settings['iq_' . $type . '_chart_yaxis_label_postfix'];
        }

        $seriesCount = isset($settings['iq_' . $type . '_chart_data_series_count']) ? $settings['iq_' . $type . '_chart_data_series_count'] : 0;

        for ($i = 0; $i < $seriesCount; $i++) {
            $dropShadowSeries[] = $i;
            if (!empty($settings['iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i]) && $settings['iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i] === "yes") {
                $tooltipSeries[] = $i;
            }
            $gradient[] = strval($settings['iq_' . $type . '_chart_gradient_1_' . $i]);

            if (strval($settings['iq_' . $type . '_chart_gradient_2_' . $i]) === '') {
                $second_gradient[] = strval($settings['iq_' . $type . '_chart_gradient_1_' . $i]);
            } else {
                $second_gradient[] = strval($settings['iq_' . $type . '_chart_gradient_2_' . $i]);
            }
            if ($settings['iq_' . $type . '_chart_bg_pattern_' . $i] !== '') {
                $fill_pattern[] = $settings['iq_' . $type . '_chart_bg_pattern_' . $i];
            } else {
                $fill_pattern[] = 'verticalLines';
            }
            $stockWidth[] = $settings['iq_' . $type . '_chart_width_3_' . $i] !== '' ? $settings['iq_' . $type . '_chart_width_3_' . $i] : 0;
            $stockDashArray[] = $settings['iq_' . $type . '_chart_dash_3_' . $i] !== '' ? $settings['iq_' . $type . '_chart_dash_3_' . $i] : 0;
            $markerSize[] = $settings['iq_' . $type . '_chart_marker_size_' . $i];
            $markerStrokeColor[] = strval($settings['iq_' . $type . '_chart_marker_stroke_color_' . $i]);
            $markerStokeWidth[] = $settings['iq_' . $type . '_chart_marker_stroke_width_' . $i];
            $markerShape[] = strval($settings['iq_' . $type . '_chart_chart_marker_stroke_shape_' . $i]);

            // chart 2
            $chart2Gradient[] = strval($settings['iq_' . $type2 . '_chart_gradient_1_' . $i]);

            if (strval($settings['iq_' . $type2 . '_chart_gradient_2_' . $i]) === '') {
                $chart2SecondGradient[] = strval($settings['iq_' . $type2 . '_chart_gradient_1_' . $i]);
            } else {
                $chart2SecondGradient[] = strval($settings['iq_' . $type2 . '_chart_gradient_2_' . $i]);
            }
            if ($settings['iq_' . $type2 . '_chart_bg_pattern_' . $i] !== '') {
                $chart2FillPattern[] = $settings['iq_' . $type2 . '_chart_bg_pattern_' . $i];
            } else {
                $chart2FillPattern[] = 'verticalLines';
            }
            $chart2StockWidth[] = $settings['iq_' . $type2 . '_chart_width_3_' . $i] !== '' ? $settings['iq_' . $type2 . '_chart_width_3_' . $i] : 0;
            $chart2StocKDash[] = $settings['iq_' . $type2 . '_chart_dash_3_' . $i] !== '' ? $settings['iq_' . $type2 . '_chart_dash_3_' . $i] : 0;

        }

        $gradient_new = $second_gradient_new = $fill_pattern_new = [];
        $chart2gradient_new = $chart2second_gradient_new = $chart2fill_pattern_new = [];

        $desiredLength = count($data['series']);

        while (count($gradient_new) < $desiredLength) {
            $gradient_new = array_merge($gradient_new, $gradient);
            $second_gradient_new = array_merge($second_gradient_new, $second_gradient);
            $fill_pattern_new = array_merge($fill_pattern_new, $fill_pattern);
            //chart 2
            $chart2gradient_new = array_merge($chart2gradient_new, $chart2Gradient);
            $chart2second_gradient_new = array_merge($chart2second_gradient_new, $chart2SecondGradient);
            $chart2fill_pattern_new = array_merge($chart2fill_pattern_new, $chart2FillPattern);
        }

        $gradient = array_slice($gradient_new, 0, $desiredLength);
        $second_gradient = array_slice($second_gradient_new, 0, $desiredLength);
        $fill_pattern = array_slice($fill_pattern_new, 0, $desiredLength);
        //chart 2
        $chart2Gradient =  array_slice($chart2gradient_new, 0, $desiredLength);
        $chart2SecondGradient = array_slice($chart2second_gradient_new, 0, $desiredLength);
        $chart2FillPattern = array_slice($chart2fill_pattern_new, 0, $desiredLength);


        $stockWidth = implode('_,_', $stockWidth);
        $stockDashArray = implode('_,_', $stockDashArray);
        $markerSize = implode('_,_', $markerSize);
        $markerStrokeColor = implode('_,_', $markerStrokeColor);
        $markerStokeWidth = implode('_,_', $markerStokeWidth);
        $markerShape = implode('_,_', $markerShape);
        $gradient = implode('_,_', $gradient);
        $second_gradient = implode('_,_', $second_gradient);
        $fill_pattern = implode('_,_', $fill_pattern);
        $category = implode('_,_', $data['category']);
        $chartDataJson = json_encode($data['series']);
        $dropShadowSeries = implode(',', $dropShadowSeries);
        $tooltipSeries = implode(',', $tooltipSeries);
        // chart 2
        $chart2StockWidth = implode('_,_', $chart2StockWidth);
        $chart2StocKDash = implode('_,_', $chart2StocKDash);
        $chart2Gradient = implode('_,_',$chart2Gradient );
        $chart2SecondGradient = implode('_,_', $chart2SecondGradient);
        $chart2FillPattern = implode('_,_', $chart2FillPattern);
        $localStringType = graphina_common_setting_get('thousand_seperator');
        require GRAPHINA_PRO_ROOT . '/elementor/charts/brush-chart/render/brush_chart.php';
        if (isRestrictedAccess('brush', $this->get_id(), $settings, false) === false) {
            ?>
            <script>
                if (typeof isInit === 'undefined') {
                    var isInit = {};
                }
                isInit['<?php esc_attr_e($mainId . '-1'); ?>'] = false;

                var options1 = {
                    series: <?php echo $chartDataJson; ?>,
                    chart: {
                        id: "brush-chart-<?php esc_attr_e($this->get_id()); ?>-1",
                        background: '<?php echo strval($settings['iq_' . $type . '_chart_background_color1']); ?>',
                        height: parseInt('<?php echo $settings['iq_' . $type . '_chart_height']; ?>'),
                        type: '<?php echo !empty($settings['iq_' . $type . '_chart_type_1']) ? strval($settings['iq_' . $type . '_chart_type_1']) : 'area'; ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                        locales: [JSON.parse('<?php echo apexChartProLocales(); ?>')],
                        defaultLocale: "en",
                        toolbar: {
                            show: false,
                            autoSelected: 'pan',
                        }
                    },
                    dataLabels: {
                        enabled: '<?php echo $settings['iq_' . $type . '_chart_datalabel_show'] === 'yes'; ?>',
                        style: {
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                            colors: ['<?php echo $settings['iq_' . $type . '_chart_datalabel_background_show'] === "yes" ? strval($settings['iq_' . $type . '_chart_datalabel_font_color_1']) : strval($settings['iq_' . $type . '_chart_datalabel_font_color']); ?>']
                        },
                        background: {
                            enabled: '<?php echo $settings['iq_' . $type . '_chart_datalabel_background_show'] === "yes"; ?>',
                            borderRadius:parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_datalabel_border_radius']) ? $settings['iq_' . $type . '_chart_datalabel_border_radius'] : 0 ?>'),
                            foreColor: ['<?php echo strval($settings['iq_' . $type . '_chart_datalabel_background_color']); ?>'],
                            borderWidth: parseInt('<?php echo $settings['iq_' . $type . '_chart_datalabel_border_width']; ?>'),
                            borderColor: '<?php echo strval($settings['iq_' . $type . '_chart_datalabel_border_color']); ?>'
                        },
                        formatter: function (val, opts) {
                            if(isNaN(val)){
                                return '';
                            }
                            if("<?php  echo  !empty($settings['iq_' . $type . '_chart_number_format_commas']) &&  esc_html($settings['iq_' . $type . '_chart_number_format_commas']) === "yes"; ?>" ){
                                val = graphinNumberWithCommas(val,'<?php echo $localStringType ?>')
                            }else{
                                val  = ("<?php !empty($settings['iq_' . $type . '_chart_label_pointer_for_label']) && esc_html($settings['iq_' . $type . '_chart_label_pointer_for_label']) === 'yes'; ?>"  &&  typeof graphinaAbbrNum  !== "undefined") ? graphinaAbbrNum(val ,  parseInt("<?php esc_html($settings['iq_' . $type . '_chart_label_pointer_number_for_label']); ?>") || 0 ) : val
                            }
                            return '<?php  echo  esc_html($dataLabelPrefix); ?>' + val + '<?php  echo  esc_html($dataLabelPostfix); ?>';
                        },
                    },
                    xaxis: {
                        type:'numeric',
                        categories: '<?php echo $category; ?>'.split('_,_').map(Number),
                        position: '<?php echo   esc_html($settings['iq_' . $type . '_chart_xaxis_datalabel_position']); ?>',
                        labels: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_show'] === "yes"; ?>',
                            rotateAlways: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_auto_rotate'] === "yes"; ?>',
                            rotate: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_rotate']; ?>') || 0,
                            offsetX: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_x']; ?>') || 0,
                            offsetY: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_y']; ?>') || 0,
                            trim: true,
                            style: {
                                colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                                fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                                fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                                fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>'
                            },
                            formatter: function (val) {
                                return '<?php  echo  esc_html($xLabelPrefix); ?>' + Math.round(val) + '<?php echo   esc_html($xLabelPostfix); ?>';
                            }
                        },
                        tooltip: {
                            enabled: "<?php echo !empty($settings['iq_' . $type . '_chart_xaxis_tooltip_show']) && $settings['iq_' . $type . '_chart_xaxis_tooltip_show'] === 'yes';?>"
                        },
                        crosshairs: {
                            show: "<?php echo !empty($settings['iq_' . $type . '_chart_xaxis_crosshairs_show']) && $settings['iq_' . $type . '_chart_xaxis_crosshairs_show'] === 'yes';?>"
                        }
                    },
                    yaxis: {
                        opposite: '<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_position'] === "yes"; ?>',
                        tickAmount: 1,
                        decimalsInFloat: parseInt("<?php  echo   esc_html($settings['iq_' . $type . '_chart_yaxis_datalabel_decimals_in_float']); ?>"),
                        labels: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_show'] === "yes"; ?>',
                            rotate: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_rotate']; ?>') || 0,
                            offsetX: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_x']; ?>') || 0,
                            offsetY: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_y']; ?>') || 0,
                            style: {
                                colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                                fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                                fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                                fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>'
                            }
                        },
                        tooltip: {
                            enabled: "<?php echo !empty($settings['iq_' . $type . '_chart_yaxis_tooltip_show']) && $settings['iq_' . $type . '_chart_yaxis_tooltip_show'] === 'yes';?>"
                        },
                        crosshairs: {
                            show: "<?php echo !empty($settings['iq_' . $type . '_chart_yaxis_crosshairs_show']) && $settings['iq_' . $type . '_chart_yaxis_crosshairs_show'] === 'yes';?>"
                        }
                    },
                    colors: '<?php echo $gradient; ?>'.split('_,_'),
                    fill:{
                        type: '<?php echo $settings['iq_' . $type . '_chart_fill_style_type']; ?>',
                        colors: '<?php echo $gradient; ?>'.split('_,_'),
                        gradient: {
                            gradientToColors: '<?php echo $second_gradient; ?>'.split('_,_'),
                            type: '<?php echo $settings['iq_' . $type . '_chart_gradient_type']; ?>',
                            inverseColors: '<?php echo $settings['iq_' . $type . '_chart_gradient_inversecolor'] === "yes"; ?>',
                            opacityFrom: parseFloat('<?php echo $settings['iq_' . $type . '_chart_gradient_opacityFrom']; ?>'),
                            opacityTo: parseFloat('<?php echo $settings['iq_' . $type . '_chart_gradient_opacityTo']; ?>')
                        }
                    },
                    tooltip: {
                        enabled: '<?php echo $settings['iq_' . $type . '_chart_tooltip'] === "yes"; ?>',
                        shared: '<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_shared']) && $settings['iq_' . $type . '_chart_tooltip_shared']=== "yes"? $settings['iq_' . $type . '_chart_tooltip_shared'] : ''; ?>' ,
                        intersect: !('<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_shared']) && $settings['iq_' . $type . '_chart_tooltip_shared']=== "yes"? $settings['iq_' . $type . '_chart_tooltip_shared'] : ''; ?>' ),
                        theme: '<?php echo $settings['iq_' . $type . '_chart_tooltip_theme']; ?>',
                        style: {
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>'
                        },
    
                    }
                };

                brushDropShadow(options1,<?php $type ?>)
                brushAnimation(options1,<?php $type ?>)
                brushNoData(options1,<?php $type ?>)
                brushLegend(options1,<?php $type ?>);
                brushYaxisFormat(options1)
                brushAxisTitle(options1)
                brushResponsive(options1)


                options1['markers'] = {
                    size: '<?php echo $markerSize; ?>'.split('_,_'),
                    strokeColors: '<?php echo $markerStrokeColor; ?>'.split('_,_'),
                    strokeWidth: '<?php echo $markerStokeWidth; ?>'.split('_,_'),
                    shape: '<?php echo $markerShape; ?>'.split('_,_'),
                    showNullDataPoints: true,
                    hover: {
                        size: 3,
                        sizeOffset: 1
                    }
                };

                if ('<?php echo $settings['iq_' . $type . '_chart_type_1'] === 'area'; ?>' ) {
                    options1.fill.opacity = parseFloat('<?php echo $settings['iq_' . $type . '_chart_fill_opacity']; ?>'),
                        options1.fill.pattern = {
                            style: '<?php echo $fill_pattern; ?>'.split('_,_'),
                            width: 6,
                            height: 6,
                            strokeWidth: 2
                        }
                    options1.stroke = {
                        curve: '<?php echo $settings['iq_' . $type . '_chart_area_curve_1']; ?>',
                    }
                } else if ('<?php echo $settings['iq_' . $type . '_chart_type_1'] === 'line' ;?>' ) {
                    options1.fill.opacity = 1,
                        options1.stroke = {
                            curve: '<?php echo $settings['iq_' . $type . '_chart_line_curve_1']; ?>',
                            width: '<?php echo $stockWidth; ?>'.split('_,_'),
                            dashArray: '<?php echo $stockDashArray; ?>'.split('_,_')
                        }
                } else if ('<?php echo $settings['iq_' . $type . '_chart_type_1'] === 'bar';?>' ) {
                    options1.plotOptions= {
                        bar: {
                            columnWidth: '<?php echo $settings['iq_' . $type . '_is_chart_stroke_width_chart_1'] ?>% ',
                        },
                    },
                    options1.fill.opacity = 1,
                        options1.stroke = {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        }
                    options1.fill.pattern ={
                        style: '<?php echo $fill_pattern ?>'.split('_,_'),
                        width: 6,
                        height: 6,
                        strokeWidth: 2
                    }
                }


                if (typeof initNowGraphina !== "undefined") {
                    initNowGraphina(
                        document.querySelector(".brush-chart-<?php esc_attr_e($this->get_id()); ?>-1"),
                        {
                            ele: document.querySelector(".brush-chart-<?php esc_attr_e($this->get_id()); ?>-1"),
                            options: options1,
                            series: [{name: '', data: []}],
                            animation: true,
                            type:'brush'
                        },
                        '<?php esc_attr_e($mainId . '-1'); ?>'
                    );
                }

                //brush chart 2
                if (typeof isInit === 'undefined') {
                    var isInit = {};
                }

                isInit['<?php esc_attr_e($mainId . '-2'); ?>'] = false;

                var options2 = {
                    series: <?php echo $chartDataJson; ?>,
                    chart: {
                        id: "brush-chart-<?php esc_attr_e($this->get_id()); ?>-2",
                        background: '<?php echo strval($settings['iq_' . $type . '_chart_background_color1']); ?>',
                        foreColor: "#ccc",
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                        locales: [JSON.parse('<?php echo apexChartProLocales(); ?>')],
                        defaultLocale: "en",
                        height: parseInt('<?php echo $settings['iq_' . $type . '_chart_height']; ?>'),
                        type: '<?php echo !empty($settings['iq_' . $type . '_chart_type_2']) ? strval($settings['iq_' . $type . '_chart_type_2']) : 'area'; ?>',
                        brush: {
                            target: "brush-chart-<?php esc_attr_e($this->get_id()); ?>-1",
                            enabled: true
                        },
                        selection: {
                            enabled: true,
                            type:'x',
                            fill: {
                                color: '<?php echo strval($settings['iq_' . $type . '_chart_selection_fill_color']); ?>',
                                opacity: '<?php echo $settings['iq_' . $type . '_chart_selection_fill_opacity'];?>'
                            },
                            stroke: {
                                width:'<?php echo $settings['iq_' . $type . '_chart_selection_stroke_width'];?>',
                                dashArray: '<?php echo $settings['iq_' . $type . '_chart_selection_stroke_dasharray'];?>',
                                color: '<?php echo strval($settings['iq_' . $type . '_chart_selection_stroke_color']);?>',
                                opacity: '<?php echo $settings['iq_' . $type . '_chart_selection_stroke_opacity'];?>'
                            },
                        },
                    },
                    xaxis: {
                        type:'numeric',
                        categories: '<?php echo $category; ?>'.split('_,_').map(Number),
                        position: '<?php  echo  esc_html($settings['iq_' . $type . '_chart_xaxis_datalabel_position']); ?>',
                        labels: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_show'] === "yes"; ?>',
                            rotateAlways: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_auto_rotate'] === "yes"; ?>',
                            rotate: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_rotate']; ?>') || 0,
                            offsetX: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_x']; ?>') || 0,
                            offsetY: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_y']; ?>') || 0,
                            trim: true,
                            style: {
                                colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                                fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                                fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                                fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>'
                            },
                            formatter: function (val) {
                                return '<?php  echo  esc_html($xLabelPrefix); ?>' + Math.round(val) + '<?php  echo  esc_html($xLabelPostfix); ?>';
                            }
                         },
                        crosshairs: {
                            show: "<?php echo !empty($settings['iq_' . $type . '_chart_xaxis_crosshairs_show']) && $settings['iq_' . $type . '_chart_xaxis_crosshairs_show'] === 'yes';?>"
                        }
                    },
                    yaxis: {
                        opposite: '<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_position'] === "yes"; ?>',
                        tickAmount: parseInt("<?php  echo  esc_html($settings['iq_' . $type . '_chart_yaxis_datalabel_tick_amount_2']); ?>"),
                        decimalsInFloat: parseInt("<?php  echo  esc_html($settings['iq_' . $type . '_chart_yaxis_datalabel_decimals_in_float']); ?>"),
                        labels: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_show'] === "yes"; ?>',
                            rotate: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_rotate']; ?>') || 0,
                            offsetX: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_x']; ?>') || 0,
                            offsetY: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_y']; ?>') || 0,
                            style: {
                                colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                                fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                                fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                                fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>'
                            }
                        }
                    },
                    colors: '<?php echo $chart2Gradient; ?>'.split('_,_'),
                    fill:{
                        type: '<?php echo $settings['iq_' . $type2 . '_chart_fill_style_type']; ?>',
                        colors: '<?php echo $chart2Gradient; ?>'.split('_,_'),
                        gradient: {
                            gradientToColors: '<?php echo $chart2SecondGradient; ?>'.split('_,_'),
                            type: '<?php echo $settings['iq_' . $type2 . '_chart_gradient_type']; ?>',
                            inverseColors: '<?php echo $settings['iq_' . $type2 . '_chart_gradient_inversecolor'] === "yes"; ?>',
                            opacityFrom: parseFloat('<?php echo $settings['iq_' . $type2 . '_chart_gradient_opacityFrom']; ?>'),
                            opacityTo: parseFloat('<?php echo $settings['iq_' . $type2 . '_chart_gradient_opacityTo']; ?>')
                        }
                    }
                };

                brushDropShadow(options2,<?php $type ?>)
                brushAnimation(options2,<?php $type ?>)
                brushNoData(options2,<?php $type ?>)
                brushLegend(options2,<?php $type ?>);
                brushYaxisFormat(options2)
                brushAxisTitle(options2)
                brushResponsive(options2)


                if('<?php echo $settings[ 'iq_' . $type . '_chart_selection_xaxis'] == 'yes' ?>'){
                    options2.chart.selection.xaxis = {
                            min: parseInt('<?php echo  $settings['iq_' . $type . '_chart_selection_xaxis_min'] ;?>'),
                            max: parseInt('<?php echo  $settings['iq_' . $type . '_chart_selection_xaxis_max'] ;?>')
                    }
                }

                if('<?php echo !empty($settings['iq_' . $type . '_chart_xaxis_datalabel_tick_amount_dataPoints']) && $settings['iq_' . $type . '_chart_xaxis_datalabel_tick_amount_dataPoints'] == 'yes'; ?>'){
                    options2.xaxis.tickAmount = 'dataPoints'
                }

                if ('<?php echo $settings['iq_' . $type . '_chart_type_2'] === 'area';?>' ) {
                    options2.fill.opacity = parseFloat('<?php echo $settings['iq_' . $type2 . '_chart_fill_opacity']; ?>'),
                        options2.fill.pattern = {
                            style: '<?php echo $chart2FillPattern; ?>'.split('_,_'),
                            width: '<?php echo $chart2StockWidth; ?>'.split('_,_'),
                            height: 6,
                            strokeWidth: 2
                        }
                    options2.stroke = {
                        curve: '<?php echo $settings['iq_' . $type . '_chart_area_curve_2']; ?>',
                    }
                } else if ('<?php echo $settings['iq_' . $type . '_chart_type_2'] === 'line';?>' ) {
                    options2.fill.opacity = 1,
                        options2.stroke = {
                            curve: '<?php echo $settings['iq_' . $type . '_chart_line_curve_2']; ?>',
                            width: '<?php echo $chart2StockWidth; ?>'.split('_,_'),
                            dashArray: '<?php echo $chart2StocKDash; ?>'.split('_,_')
                        }
                } else if ('<?php echo $settings['iq_' . $type . '_chart_type_2'] === 'bar';?>' ) {
                    options2.plotOptions= {
                        bar: {
                            columnWidth: '<?php echo $settings['iq_' . $type . '_is_chart_stroke_width_chart_2'] ?>% ',
                        },
                    },
                    options2.fill.opacity = 1,
                        options2.stroke = {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        }
                        options2.fill.pattern ={
                            style: '<?php echo $chart2FillPattern ?>'.split('_,_'),
                            width: 6,
                            height: 6,
                            strokeWidth: 2
                        }
                }

                if (typeof initNowGraphina !== "undefined") {
                    initNowGraphina(
                        document.querySelector(".brush-chart-<?php esc_attr_e($this->get_id()); ?>-2"),
                        {
                            ele: document.querySelector(".brush-chart-<?php esc_attr_e($this->get_id()); ?>-2"),
                            options: options2,
                            series: [{name: '', data: []}],
                            animation: true,
                            type:'brush'
                        },
                        '<?php esc_attr_e($mainId . '-2'); ?>'
                    );
                }

                //common chartoption

                function brushNoData(options,<?php $type ?>) {
                    return options['noData'] = {
                        text: '<?php echo $loadingText; ?>',
                        align: 'center',
                        verticalAlign: 'middle',
                        style: {
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            color: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>'
                        }
                    }
                }

                function brushLegend(options,<?php $type ?>) {
                    return options['legend'] = {
                        showForSingleSeries: true,
                        show: '<?php echo $settings['iq_' . $type . '_chart_legend_show'] === "yes"; ?>',
                        position: '<?php echo !empty($settings['iq_' . $type . '_chart_legend_position']) ? esc_html($settings['iq_' . $type . '_chart_legend_position']) : 'bottom'; ?>',
                        horizontalAlign: '<?php echo !empty($settings['iq_' . $type . '_chart_legend_horizontal_align']) ? esc_html($settings['iq_' . $type . '_chart_legend_horizontal_align']) : 'center'; ?>',
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                        fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                        labels: {
                            colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>'
                        },
                        tooltipHoverFormatter: function(seriesName, opts) {
                            if('<?php echo !empty($settings['iq_' . $type . '_chart_legend_show_series_value']) && $settings['iq_' . $type . '_chart_legend_show_series_value'] === 'yes' ?>'){
                                return '<div class="legend-info">' + '<span>' + seriesName + '</span>' + ' : '+'<strong>' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + '</strong>' + '</div>'
                            }
                            return seriesName
                        }
                    }
                }

                function brushDropShadow(options,<?php $type ?>) {
                    options.chart.dropShadow = {
                        enabled: '<?php echo($settings['iq_' . $type . '_is_chart_dropshadow'] === "yes"); ?>',
                        enabledOnSeries: [<?php  echo  esc_html($dropShadowSeries); ?>],
                        top: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_top']; ?>'),
                        left: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_left']; ?>'),
                        blur: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_blur']; ?>'),
                        color: '<?php echo strval(isset($settings['iq_' . $type . '_is_chart_dropshadow_color']) ? $settings['iq_' . $type . '_is_chart_dropshadow_color'] : ''); ?>',
                        opacity: parseFloat('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_opacity']; ?>')
                    }
                }

                function brushAnimation(options,<?php $type ?>) {
                     options.chart.animation = {
                        enabled: '<?php echo($settings['iq_' . $type . '_chart_animation'] === "yes"); ?>',
                        speed: parseInt('<?php echo $settings['iq_' . $type . '_chart_animation_speed']; ?>'),
                        delay: parseInt('<?php echo $settings['iq_' . $type . '_chart_animation_delay']; ?>')
                     }
                }

                function brushYaxisFormat(options){
                    if ("<?php  echo  esc_html($settings['iq_' . $type . '_chart_yaxis_label_show']) === "yes"; ?>" ) {
                        options.yaxis.labels.formatter = function (val) {
                            if("<?php  echo  esc_html($settings['iq_' . $type . '_chart_yaxis_format_number']) === "yes"; ?>" ){
                                val = graphinNumberWithCommas(val,'<?php echo $localStringType ?>')
                            }
                            else if("<?php  echo  !empty($settings['iq_' . $type . '_chart_yaxis_label_pointer']) && esc_html($settings['iq_' . $type . '_chart_yaxis_label_pointer'])  === 'yes'; ?>"
                                &&  typeof graphinaAbbrNum  !== "undefined"){      
                            val = graphinaAbbrNum(val ,  parseInt("<?php echo   esc_html($settings['iq_' . $type . '_chart_yaxis_label_pointer_number']); ?>") || 0 );
                        }
                            return '<?php  echo  esc_html($yLabelPrefix); ?>' + val + '<?php echo   esc_html($yLabelPostfix); ?>';
                        }
                    }
                }

                function brushResponsive(option){
                    option.responsive = [{
                        breakpoint: 1024,
                        options: {
                            chart: {
                                height: parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_height_tablet']) ? $settings['iq_' . $type . '_chart_height_tablet'] : $settings['iq_' . $type . '_chart_height'] ; ?>')
                            }
                        }
                    },
                        {
                            breakpoint: 674,
                            options: {
                                chart: {
                                    height: parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_height_mobile']) ? $settings['iq_' . $type . '_chart_height_mobile'] : $settings['iq_' . $type . '_chart_height'] ;  ?>')
                                }
                            }
                        }
                    ]
                }

                function brushAxisTitle(options){

                    if("<?php echo $settings['iq_' . $type . '_chart_xaxis_title_enable'] == 'yes' ;?>"){
                        let style ={
                            color:'<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                        }

                        let title = '<?php echo strval($settings['iq_' . $type . '_chart_xaxis_title']); ?>';
                        let xaxisYoffset ='<?php  echo  esc_html($settings['iq_' . $type . '_chart_xaxis_datalabel_position']) === 'top'; ?>'  ? -95 : 0;
                        if(typeof axisTitle !== "undefined"){
                            axisTitle(options, 'xaxis' ,title, style ,xaxisYoffset);
                        }
                    }

                    if("<?php echo $settings['iq_' . $type . '_chart_yaxis_title_enable'] == 'yes' ; ?>"){
                        let style ={
                            color:'<?php echo strval($settings['iq_' . $type . '_card_yaxis_title_font_color']); ?>',
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                        }
                        let title = '<?php echo strval($settings['iq_' . $type . '_chart_yaxis_title']); ?>';
                        if(typeof axisTitle !== "undefined"){
                            axisTitle(options, 'yaxis' ,title, style );
                        }
                    }

                    if("<?php echo !empty($settings['iq_' . $type . '_chart_opposite_yaxis_title_enable']) && $settings['iq_' . $type . '_chart_opposite_yaxis_title_enable'] == 'yes' ;  ?>"){
                        let style = {
                            color:'<?php echo strval($settings['iq_' . $type . '_card_opposite_yaxis_title_font_color']); ?>',
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                        }
                        options['yaxis'] = [options.yaxis]
                        options.yaxis.push({
                            opposite: '<?php echo $settings['iq_'.$type.'_chart_yaxis_datalabel_position'] === 'yes' ? false : true ; ?>',
                            labels: {
                                show: '<?php echo $settings['iq_' . $type . '_chart_opposite_yaxis_label_show'] === 'yes'; ?>',
                                formatter: function (val) {
                                    if("<?php echo !empty($settings['iq_' . $type . 'chart_opposite_yaxis_format_number']) && $settings['iq' . $type . '_chart_opposite_yaxis_format_number'] === 'yes'; ?>"){
                                        val = graphinNumberWithCommas(val,'<?php echo $localStringType ?>')
                                    }
                                    return '<?php echo $settings['iq_' .$type . '_chart_opposite_yaxis_label_prefix'] ;?>'  + val + '<?php echo $settings['iq_' .$type . '_chart_opposite_yaxis_label_postfix'] ;?>'
                                },
                                style
                            },
                            tickAmount: parseInt('<?php echo $settings['iq_' . $type . '_chart_opposite_yaxis_tick_amount']; ?>'),
                            title: {
                                text: '<?php echo $settings['iq_' .$type . '_chart_opposite_yaxis_title'] ;?>',
                                style
                            }
                        })
                    }

                }

            </script>
            <?php
        }
    }
}

Plugin::instance()->widgets_manager->register(new brush_chart());