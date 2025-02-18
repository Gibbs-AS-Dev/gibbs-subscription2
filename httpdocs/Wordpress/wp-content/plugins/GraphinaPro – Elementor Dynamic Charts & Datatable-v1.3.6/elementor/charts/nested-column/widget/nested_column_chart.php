<?php

namespace Elementor;
if (!defined('ABSPATH')) exit;

/**
 * Elementor Blog widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.2.4
 */
class Nested_Column_chart extends Widget_Base
{
    private $maxLimit = 15;

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
        return 'nested_column_chart';
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
        return 'Nested Column';
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
        return 'graphina-apex-nestedcolmn-chart';
    }

    public function get_chart_type()
    {
        return 'nested_column';
    }

    protected function register_controls()
    {
        $type = $this->get_chart_type();

        graphina_basic_setting($this, $type);

        graphina_chart_data_option_setting($this, $type, 5);

        $this->start_controls_section(
            'iq_' . $type . '_section_2',
            [
                'label' => esc_html__('Chart Setting',  'graphina-pro-charts-for-elementor')
            ]
        );

        $this->add_control(
            'iq_' . $type . '_can_chart_negative_values',
            [
                'label' => esc_html__('Default Negative',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
                'default' => false,
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => 'manual'
                ]
            ]
        );

        graphina_common_chart_setting($this, $type, true, false);

        $this->add_control(
            'iq_' . $type . '_can_sub_chart_datalabel_show',
            [
                'label' => esc_html__('Show Sub-Chart Labels',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
                'default' => false,
            ]
        );

        $this->end_controls_section();

        graphina_chart_label_setting($this, $type);

        graphina_series_setting($this, $type, ['color'], false, ['classic'], false, false);

        for ($i = 0; $i < graphina_default_setting('max_series_value'); $i++) {
            $this->start_controls_section(
                'iq_' . $type . '_section_3_' . $i,
                [
                    'label' => esc_html__('Element ' . ($i + 1),  'graphina-pro-charts-for-elementor'),
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value')),
                        'iq_' . $type . '_chart_data_option' => 'manual'
                    ],
                ]
            );
            $this->add_control(
                'iq_' . $type . '_chart_title_3_' . $i,
                [
                    'label' => 'Value',
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Add Value',  'graphina-pro-charts-for-elementor'),
                    'default' => 'Element ' . ($i + 1),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater = new Repeater();

            $repeater->add_control(
                'iq_' . $type . '_chart_data_title_3_' . $i,
                [
                    'label' => 'Title',
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Add Value',  'graphina-pro-charts-for-elementor'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater->add_control(
                'iq_' . $type . '_chart_data_value_3_' . $i,
                [
                    'label' => 'Value',
                    'type' => Controls_Manager::NUMBER,
                    'placeholder' => esc_html__('Add Value',  'graphina-pro-charts-for-elementor'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            /** Chart value list. */
            $this->add_control(
                'iq_' . $type . '_value_list_3_1_' . $i,
                [
                    'label' => esc_html__('Sub Data',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 1',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(10, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 2',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(10, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 3',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(10, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 4',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(10, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 5',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(10, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 6',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(10, 200)
                        ]
                    ],
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values!' => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_data_title_3_' . $i . ' }}}',
                ]
            );

            $this->add_control(
                'iq_' . $type . '_value_list_3_2_' . $i,
                [
                    'label' => esc_html__('Sub Data',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 1',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(-200, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 2',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(-200, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 3',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(-200, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 4',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(-200, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 5',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(-200, 200)
                        ],
                        [
                            'iq_' . $type . '_chart_data_title_3_' . $i => 'Data 6',
                            'iq_' . $type . '_chart_data_value_3_' . $i => rand(-200, 200)
                        ]
                    ],
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values' => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_data_title_3_' . $i . ' }}}',
                ]
            );

            $this->end_controls_section();

        }

        graphina_style_section($this, $type);

        graphina_card_style($this, $type);

        graphina_chart_style($this, $type);

        graphina_pro_password_style_section($this, $type);

    }

    protected function render()
    {
        $type = $this->get_chart_type();
        $settings = $this->get_settings_for_display();
        $mainId = $this->get_id();
        $colors = [];
        $data = ['series' => [], 'category' => []];
        $exportFileName = (
            !empty($settings['iq_' . $type . '_can_chart_show_toolbar']) && $settings['iq_' . $type . '_can_chart_show_toolbar'] === 'yes'
            && !empty($settings['iq_' . $type . '_export_filename'])
        ) ? $settings['iq_' . $type . '_export_filename'] : $mainId;

        for ($i = 0; $i < $settings['iq_' . $type . '_chart_data_series_count']; $i++) {
            $colors[] = strval($settings['iq_' . $type . '_chart_gradient_1_' . $i]);
        }

        $dataTypeOption = $settings['iq_' . $type . '_chart_data_option'] === 'manual' ? 'manual' : $settings['iq_' . $type . '_chart_dynamic_data_option'];
        switch ($dataTypeOption) {
            case "manual" :
                for ($i = 0; $i < $settings['iq_' . $type . '_chart_data_series_count']; $i++) {
                    $value = [];
                    $valueList = $settings['iq_' . $type . '_value_list_3_' . ($settings['iq_' . $type . '_can_chart_negative_values'] === 'yes' ? 2 : 1) . '_' . $i];
                    foreach ($valueList as $v) {
                        $value[] = [
                            "x" => (string)graphina_get_dynamic_tag_data($v,'iq_' . $type . '_chart_data_title_3_' . $i),
                            "y" => (float)graphina_get_dynamic_tag_data($v,'iq_' . $type . '_chart_data_value_3_' . $i),
                        ];
                    }
                    $data['series'][] = [
                        "x" => (string)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_chart_title_3_' . $i),
                        "quarters" => $value
                    ];
                }
                break;
            case "csv":
                $data = graphina_pro_parse_csv($settings, $type, 'nested_column');
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                break;
            case "remote-csv" :
            case "google-sheet" :
                $data = graphina_pro_get_data_from_url($type, $settings, $dataTypeOption, $this->get_id(), 'nested_column');
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                break;
            case "api":
                $data = graphina_pro_chart_get_data_from_api($type, $settings, 'nested_column',[]);
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                break;
            case 'filter':
                update_post_meta(get_the_ID(),$mainId,$settings['iq_' . $type . '_element_filter_widget_id']);
                $data = apply_filters('graphina_extra_data_option', $data, $type, $settings,$settings['iq_' . $type . '_element_filter_widget_id']);
                break;
        }

        if($settings['iq_' . $type . '_chart_data_option'] === 'firebase') {
            $data = apply_filters('graphina_addons_render_section', $data, $type, $settings);
        }

        if (isset($data['fail']) && $data['fail'] === 'permission') {
            switch ($dataTypeOption) {
                case "google-sheet" :
                    echo "<pre><b>" . esc_html__('Please check file sharing permission and "Publish As" type is CSV or not. ',  'graphina-pro-charts-for-elementor') . "</b><small><a target='_blank' href='https://youtu.be/Dv8s4QxZlDk'>". esc_html__('Click for reference.',  'graphina-pro-charts-for-elementor') ."</a></small></pre>";
                    return;
                    break;
                case "remote-csv" :
                default:
                    echo "<pre><b>" . (isset($data['errorMessage']) ? $data['errorMessage'] :  esc_html__('Please check file sharing permission.',  'graphina-pro-charts-for-elementor')). "</b></pre>";
                    return;
                    break;
            }
        }

        $gradient_new = [];
        $desiredLength = count($data['series']);
        while (count($gradient_new) < $desiredLength) {
            $gradient_new = array_merge($gradient_new, $colors);
        }

        foreach ($data['series'] as $key => $val) {
            $sum = 0;
            foreach ($val['quarters'] as $k1 => $v1) {
                $sum += (float)$v1['y'];
            }
            $data['series'][$key]['y'] = $sum;
            $data['series'][$key]['color'] = $gradient_new[$key];
        }

        $colors = implode('_,_', array_slice($gradient_new, 0, $desiredLength));
        $chartDataJson = json_encode($data['series']);
        require GRAPHINA_PRO_ROOT . '/elementor/charts/nested-column/render/nested_column_chart.php';
        if( isRestrictedAccess('nested_column',$this->get_id(),$settings,false) === false)
        {
        ?>
        <script>
            var myElement = document.querySelector(".nested_column-chart-one-<?php esc_attr_e($this->get_id()); ?>");
            if (typeof isInit === "undefined") {
                var isInit = {};
            }
            if (typeof chart === "undefined") {
                var chart = {};
            }
            if (typeof options === "undefined") {
                var options = {};
            }
            if (typeof chartQuarter === "undefined") {
                var chartQuarter = {};
                var optionsQuarter = {}
            }

            function updateQuarterChart(sourceChart, destChartIDToUpdate, mainId) {
                if (typeof destChartIDToUpdate[mainId] === "undefined") {
                    return true;
                }
                var series = [];
                var seriesIndex = 0;
                var colors = []
                if (sourceChart.w.globals.selectedDataPoints[0]) {
                    var selectedPoints = sourceChart.w.globals.selectedDataPoints;
                    for (var i = 0; i < selectedPoints[seriesIndex].length; i++) {
                        var selectedIndex = selectedPoints[seriesIndex][i];
                        var yearSeries = sourceChart.w.config.series[seriesIndex];
                        series.push({
                            name: yearSeries.data[selectedIndex].x,
                            data: yearSeries.data[selectedIndex].quarters
                        })
                        colors.push(yearSeries.data[selectedIndex].color)
                    }
                    if (series.length === 0) {
                        series = [{
                            data: []
                        }];
                        colors = ['#ffffff'];
                    }
                    destChartIDToUpdate[mainId].updateSeries(series);
                    destChartIDToUpdate[mainId].updateOptions({
                        colors: colors,
                        fill: {
                            colors: colors
                        }
                    });
                    window.dispatchEvent(new Event('resize'));
                    return true;
                }
            }

            optionsQuarter['<?php esc_attr_e($this->get_id()); ?>'] = {
                series: [{
                    data: []
                }],
                chart: {
                    id: 'barQuarter-<?php esc_attr_e($this->get_id()); ?>',
                    height: parseInt('<?php echo $settings['iq_' . $type . '_chart_height'] ?>'),
                    width: '100%',
                    type: 'bar',
                    fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                    locales: [JSON.parse('<?php echo apexChartProLocales(); ?>')],
                    defaultLocale: "en",
                    stacked: true,
                    toolbar: {
                        show: '<?php echo $settings['iq_' . $type . '_can_chart_show_toolbar'] ?>',
                        export:{
                            csv:{
                                filename:"<?php echo $exportFileName.' Sub Chart'; ?>"
                            },
                            svg:{
                                filename:"<?php echo $exportFileName.' Sub Chart'; ?>"
                            },
                            png:{
                                filename:"<?php echo $exportFileName.' Sub Chart'; ?>"
                            }
                        }
                    },
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%',
                        horizontal: false
                    }
                },
                noData: {
                    text: '<?php echo $settings['iq_' . $type . '_chart_no_data_text'] ?>',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                        color: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>'
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    yaxis: {
                        lines: {
                            show: false,
                        }
                    },
                    xaxis: {
                        lines: {
                            show: true,
                        }
                    }
                },
                yaxis: {
                    labels: {
                        show: false
                    }
                },
                xaxis: {
                    labels: {
                        trim: true,
                        style: {
                            colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>'
                        }
                    }
                },
                dataLabels: {
                    enabled: '<?php echo $settings['iq_' . $type . '_can_sub_chart_datalabel_show']; ?>',
                    textAnchor: 'middle',
                    style: {
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                        fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                        colors: ['<?php echo $settings['iq_' . $type . '_chart_datalabel_background_show'] === "yes" ? strval($settings['iq_' . $type . '_chart_datalabel_font_color_1']) : strval($settings['iq_' . $type . '_chart_datalabel_font_color']); ?>']
                    },
                    background: {
                        enabled: '<?php echo $settings['iq_' . $type . '_chart_datalabel_background_show'] === "yes"; ?>',
                        foreColor: ['<?php echo strval($settings['iq_' . $type . '_chart_datalabel_background_color']); ?>'],
                        borderWidth: parseInt('<?php echo $settings['iq_' . $type . '_chart_datalabel_border_width']; ?>'),
                        borderColor: '<?php echo strval($settings['iq_' . $type . '_chart_datalabel_border_color']); ?>'
                    },
                },
                title: {
                    text: '',
                    offsetX: 10
                },
                tooltip: {
                    x: {
                        formatter: function (val, opts) {
                            return opts.w.globals.seriesNames[opts.seriesIndex]
                        }
                    },
                    y: {
                        title: {
                            formatter: function (val, opts) {
                                return opts.w.globals.labels[opts.dataPointIndex]
                            }
                        }
                    }
                }
            };

            options['<?php esc_attr_e($this->get_id()); ?>'] = {
                series: [{
                    data: <?php echo $chartDataJson ?>
                }],
                chart: {
                    id: 'barYear-<?php esc_attr_e($this->get_id()); ?>',
                    height: parseInt('<?php echo $settings['iq_' . $type . '_chart_height'] ?>'),
                    width: '100%',
                    type: 'bar',
                    fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                    locales: [JSON.parse('<?php echo apexChartProLocales(); ?>')],
                    defaultLocale: "en",
                    toolbar: {
                        offsetX: parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_toolbar_offsetx']) ? $settings['iq_' . $type . '_chart_toolbar_offsetx'] : 0 ;?>') || 0,
                        offsetY: parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_toolbar_offsety']) ? $settings['iq_' . $type . '_chart_toolbar_offsety'] : 0 ;?>')|| 0,
                        show: '<?php echo $settings['iq_' . $type . '_can_chart_show_toolbar'] ?>',
                        export:{
                            csv:{
                                filename:"<?php echo $exportFileName.' Main Chart'; ?>"
                            },
                            svg:{
                                filename:"<?php echo $exportFileName.' Main Chart'; ?>"
                            },
                            png:{
                                filename:"<?php echo $exportFileName.' Main Chart'; ?>"
                            }
                        }
                    },
                    events: {
                        dataPointSelection: function (e, chartEle, opts) {
                            var quarterChartEl = document.querySelector(".nested_column-chart-two-<?php esc_attr_e($this->get_id()); ?>");
                            var yearChartEl = document.querySelector(".nested_column-chart-one-<?php esc_attr_e($this->get_id()); ?>");
                            if (opts.selectedDataPoints[0].length === 1) {
                                if (quarterChartEl.classList.contains("active")) {
                                    updateQuarterChart(chartEle, chartQuarter, '<?php esc_attr_e($this->get_id()); ?>')
                                } else {
                                    yearChartEl.classList.add("chart-quarter-activated")
                                    quarterChartEl.classList.add("active");
                                    updateQuarterChart(chartEle, chartQuarter, '<?php esc_attr_e($this->get_id()); ?>')
                                }
                            } else {
                                updateQuarterChart(chartEle, chartQuarter, '<?php esc_attr_e($this->get_id()); ?>')
                            }

                            if (opts.selectedDataPoints[0].length === 0) {
                                yearChartEl.classList.remove("chart-quarter-activated")
                                quarterChartEl.classList.remove("active");
                            }
                        },
                        updated: function (chartEle, opts) {
                            updateQuarterChart(chartEle, chartQuarter, '<?php esc_attr_e($this->get_id()); ?>');
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        distributed: true,
                        horizontal: true,
                        barHeight: '75%',
                        dataLabels: {
                            position: 'bottom'
                        }
                    }
                },
                noData: {
                    text: '<?php echo $settings['iq_' . $type . '_chart_no_data_text'] ?>',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                        color: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>'
                    }
                },
                dataLabels: {
                    enabled: '<?php echo $settings['iq_' . $type . '_chart_datalabel_show']; ?>',
                    textAnchor: 'start',
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
                    formatter: function (val, opt) {
                        return opt.w.globals.labels[opt.dataPointIndex]
                    },
                    offsetX: 0
                },
                colors: '<?php echo $colors; ?>'.split('_,_'),
                states: {
                    normal: {
                        filter: {
                            type: 'desaturate'
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: true,
                        filter: {
                            type: 'darken',
                            value: 1
                        }
                    }
                },
                tooltip: {
                    x: {
                        show: true
                    },
                    y: {
                        title: {
                            formatter: function (val, opts) {
                                return opts.w.globals.labels[opts.dataPointIndex]
                            }
                        }
                    }
                },
                yaxis: {
                    labels: {
                        show: false
                    }
                },
                xaxis: {
                    labels: {
                        trim: true,
                        style: {
                            colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>'
                        }
                    }
                },
                legend: {
                    showForSingleSeries:true,
                    show: '<?php echo $settings['iq_' . $type . '_chart_legend_show'] ?>',
                    position: '<?php echo !empty($settings['iq_' . $type . '_chart_legend_position']) ? esc_html($settings['iq_' . $type . '_chart_legend_position']) : 'bottom' ; ?>',
                    horizontalAlign: '<?php echo !empty($settings['iq_' . $type . '_chart_legend_horizontal_align']) ? esc_html($settings['iq_' . $type . '_chart_legend_horizontal_align']) : 'center' ; ?>',
                    fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                    fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                    fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight'] ?>',
                    labels: {
                        colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>'
                    }
                },
                responsive: [{
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
            };
            isInit['<?php esc_attr_e($this->get_id()); ?>'] = false;
            if (checkIfIsInViewport(myElement, false) ) {
                 if('<?php esc_attr_e($this->get_id()); ?>' in isInit){
                     if(isInit['<?php esc_attr_e($this->get_id()); ?>'] === false){
                         initNowNestedColumn('<?php esc_attr_e($this->get_id()); ?>', options['<?php esc_attr_e($this->get_id()); ?>'])
                     }
                 }
            }
            document.addEventListener('scroll', function () {
                if (checkIfIsInViewport(myElement, false) ) {
                 if('<?php esc_attr_e($this->get_id()); ?>' in isInit){
                     if(isInit['<?php esc_attr_e($this->get_id()); ?>'] === false){
                         initNowNestedColumn('<?php esc_attr_e($this->get_id()); ?>', options['<?php esc_attr_e($this->get_id()); ?>'])
                     }
                 }
                }
            });

            function checkIfIsInViewport(el, first = false) {
                if('<?php echo graphina_common_setting_get('view_port') === 'on' ?>'){
                    return true;
                }
                const rect = el.getBoundingClientRect();
                let minus = first ? ((window.innerHeight || document.documentElement.clientHeight) * 1.9) : 0;
                return (
                    (
                        (rect.top - ((window.innerHeight || document.documentElement.clientHeight) / 1.2)) >= 0 &&
                        (rect.bottom - minus) <= (window.innerHeight || document.documentElement.clientHeight)
                    )
                    ||
                    (
                        rect.top >= 0 &&
                        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
                    )
                );
            }

            function initNowNestedColumn(id, options) {
                isInit[id] = true;
                chartQuarter[id] = new ApexCharts(document.querySelector(".nested_column-chart-two-" + id), optionsQuarter[id]);
                chartQuarter[id].render();
                chart[id] = new ApexCharts(document.querySelector(".nested_column-chart-one-" + id), options);
                chart[id].render();
                chart[id].addEventListener('dataPointSelection', function (e, chartEle, opts) {
                    window.dispatchEvent(new Event('resize'));
                    var quarterChartEl = document.querySelector(".nested_column-chart-two-" + id);
                    var yearChartEl = document.querySelector(".nested_column-chart-one-" + id);

                    if (opts.selectedDataPoints[0].length === 1) {
                        if (quarterChartEl.classList.contains("active")) {
                            updateQuarterChart(chartEle, chartQuarter, id);
                        } else {
                            yearChartEl.classList.add("chart-quarter-activated")
                            quarterChartEl.classList.add("active");
                            updateQuarterChart(chartEle, chartQuarter, id);
                        }
                    } else {
                        updateQuarterChart(chartEle, chartQuarter, id);
                    }

                    if (opts.selectedDataPoints[0].length === 0) {
                        yearChartEl.classList.remove("chart-quarter-activated")
                        quarterChartEl.classList.remove("active");
                    }
                    window.dispatchEvent(new Event('resize'));
                })
                chart[id].addEventListener('updated', function (chartEle) {
                    updateQuarterChart(chartEle, chartQuarter, id);
                })
            }
        </script>
        <?php
        }
    }
}

Plugin::instance()->widgets_manager->register(new Nested_Column_chart());