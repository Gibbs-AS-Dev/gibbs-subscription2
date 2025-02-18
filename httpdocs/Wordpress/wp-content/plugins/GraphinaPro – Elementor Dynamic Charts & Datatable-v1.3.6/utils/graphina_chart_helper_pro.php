<?php

use Elementor\Controls_Manager;
use Elementor\Plugin;

function graphina_pro_chart_content($settings, $mainId, $type, $structure_type,$selected_item)
{
    $data = ['series' => [], 'category' => []];

    $dataTypeOption = $settings['iq_' . $type . '_chart_dynamic_data_option'];
    switch ($dataTypeOption) {
        case "csv":
            if(!empty($settings['iq_' . $type . '_chart_csv_column_wise_enable']) && $settings['iq_' . $type . '_chart_csv_column_wise_enable'] === 'yes'){
                $url = $settings['iq_' . $type . '_chart_upload_csv']['url'];
                $data = graphina_pro_parse_csv_column_wise($url,$structure_type,$settings,$type);
            }else{
                $data = graphina_pro_parse_csv($settings, $type,$structure_type);
            }
            break;
        case "google-sheet" :
        case "remote-csv" :
            if(!empty($settings['iq_' . $type . '_chart_csv_column_wise_enable']) && $settings['iq_' . $type . '_chart_csv_column_wise_enable'] === 'yes'){
                $url = $dataTypeOption === 'remote-csv' ? $settings['iq_' . $type . '_chart_import_from_url'] : $settings['iq_' . $type . '_chart_import_from_google_sheet'];
                $data = graphina_pro_parse_csv_column_wise($url,$structure_type,$settings,$type);
            }else{
                $data = graphina_pro_get_data_from_url($type, $settings, $dataTypeOption, $mainId, $structure_type);
            }
            break;
        case "api":
            $data = graphina_pro_chart_get_data_from_api($type, $settings, $structure_type,$selected_item);
            break;
        case "sql-builder":
            $data = graphina_pro_chart_get_data_from_sql_builder($settings, $type,$selected_item);
            break;
        case 'filter':
            update_post_meta(get_the_ID(),$mainId,$settings['iq_' . $type . '_element_filter_widget_id']);
            $data = apply_filters('graphina_extra_data_option', $data, $type, $settings,$settings['iq_' . $type . '_element_filter_widget_id']);
            break;
    }

    $data = apply_filters('graphina_addons_render_section', $data, $type, $settings);

    if (isset($data['fail']) && $data['fail'] === 'permission') {
        $dataTypeOption = $settings['iq_' . $type . '_chart_dynamic_data_option'];
        switch ($dataTypeOption) {
            case "google-sheet" :
                $data['fail_message'] = "<pre><b>" . esc_html__('Please check file sharing permission and "Publish As" type is CSV or not. ',  'graphina-pro-charts-for-elementor') . "</b><small><a target='_blank' href='https://youtu.be/Dv8s4QxZlDk'>" . esc_html__('Click for reference.',  'graphina-pro-charts-for-elementor') . "</a></small></pre>";
                break;
            case "remote-csv" :
            default:
                $data['fail_message'] = "<pre><b>" . (isset($data['errorMessage']) ? $data['errorMessage'] :  esc_html__('Please check file sharing permission.',  'graphina-pro-charts-for-elementor')). "</b></pre>";
                break;
        }
    }
    return $data;
}

/****************
 * @param bool $first
 * @return array|string
 */
function graphina_pro_mixed_chart_typeList($first = false, $revese = false)
{
    $charts = [
        "bar" => esc_html__('Column',  'graphina-pro-charts-for-elementor'),
        "line" => esc_html__('Line',  'graphina-pro-charts-for-elementor'),
        "area" => esc_html__('Area',  'graphina-pro-charts-for-elementor'),
    ];
    if ($revese) {
        $charts = array_reverse($charts);
    }
    $keys = array_keys($charts);
    return $first ? (count($keys) > 0 ? $keys[0] : '') : $charts;
}

/****************
 * @return array
 */
function graphina_pro_gradient_type()
{
    return [
        "horizontal" => esc_html__('Horizontal',  'graphina-pro-charts-for-elementor'),
        "vertical" => esc_html__('Vertical',  'graphina-pro-charts-for-elementor'),
        "diagonal1" => esc_html__('Diagonal1',  'graphina-pro-charts-for-elementor'),
        "diagonal2" => esc_html__('Diagonal2',  'graphina-pro-charts-for-elementor')
    ];
}

/****************
 * @param $data
 * @param $i
 * @return mixed
 */
function graphina_pro_get_random_chart_type($data, $i)
{
    $index = $i % count($data);
    $keys = array_keys($data);
    return $keys[$index];
}

/****************
 * @param bool $first
 * @return array|string
 */
function graphina_pro_line_cap_type($first = false)
{
    $options = [
        "square" => esc_html__('Square',  'graphina-pro-charts-for-elementor'),
        "butt" => esc_html__('Butt',  'graphina-pro-charts-for-elementor'),
        "round" => esc_html__('Round',  'graphina-pro-charts-for-elementor')
    ];
    $keys = array_keys($options);
    return $first ? (count($keys) > 0 ? $keys[0] : '') : $options;
}

/****************
 * @param bool $first
 * @return array|string
 */
function graphina_pro_plot_shape_type($first = false)
{
    $options = [
        "flat" => esc_html__('Flat',  'graphina-pro-charts-for-elementor'),
        "rounded" => esc_html__('Rounded',  'graphina-pro-charts-for-elementor')
    ];
    $keys = array_keys($options);
    return $first ? (count($keys) > 0 ? $keys[0] : '') : $options;
}

/***********************
 * @param object $this_ele
 * @param string $type
 * @param string[] $ele_array like ['color','stroke','drop shadow']
 * @param array $fillOptions lke ['classic', 'gradient', 'pattern']
 */
function graphina_pro_mixed_series_setting($this_ele, $type = 'chart_id', $ele_array = [], $fillOptions = [])
{
    $colors = graphina_colors('color');
    $gradientColor = graphina_colors('gradientColor');
    $this_ele->start_controls_section(
        'iq_' . $type . '_section_11',
        [
            'label' => esc_html__('Elements Setting',  'graphina-pro-charts-for-elementor'),
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_marker_setting_pro_divider',
        [
            'type' => Controls_Manager::DIVIDER,

        ]
    );

    for ($i = 0; $i < graphina_default_setting('max_series_value'); $i++) {
        $condition = [
            'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
        ];

        if ($i !== 0) {
            $this_ele->add_control(
                'iq_' . $type . '_chart_hr_series_element_setting_1_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_hr_series_element_setting_2_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );
        }

        $this_ele->add_control(
            'iq_' . $type . '_chart_series_element_setting_title_' . $i,
            [
                'label' => esc_html__('Element ' . ($i + 1),  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => $condition
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_type_3_' . $i,
            [
                'label' => esc_html__('Type',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_get_random_chart_type(graphina_pro_mixed_chart_typeList(), $i),
                'options' => graphina_pro_mixed_chart_typeList(),
                'condition' => $condition
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_datalabel_show_3_' . $i,
            [
                'label' => esc_html__('Show Data Labels',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('Show',  'graphina-pro-charts-for-elementor'),
                'default' => 'yes',
                'condition' => array_merge(['iq_' . $type . '_chart_datalabel_show' => 'yes'], $condition)
            ]
        );

        $this_ele->add_control(
            'hr_4_01_' . $i,
            [
                'type' => Controls_Manager::DIVIDER,
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes'], $condition)
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_yaxis_setting_title_3_' . $i,
            [
                'label' => esc_html__('Y-Axis Setting',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes'], $condition)
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_yaxis_show_3_' . $i,
            [
                'label' => esc_html__('Show Axis With Title',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('Show',  'graphina-pro-charts-for-elementor'),
                'default' => '',
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes'], $condition)
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_yaxis_title_3_'. $i,
            [
                'label' => esc_html__('Yaxis Title',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'iq_' . $type . '_chart_yaxis_show_3_' . $i => 'yes'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );


        $this_ele->add_control(
            'iq_' . $type . '_chart_yaxis_opposite_3_' . $i,
            [
                'label' => esc_html__('Position',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => graphina_position_type('horizontal_boolean', true),
                'options' => graphina_position_type('horizontal_boolean'),
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes', 'iq_' . $type . '_chart_yaxis_show_4_' . $i => 'yes'], $condition)
            ]
        );

        if (in_array('color', $ele_array)) {

            $this_ele->add_control(
                'iq_' . $type . '_chart_hr_fill_setting_3_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            graphina_fill_style_setting($this_ele, $type, $fillOptions, true, $i, $condition, true);

            $this_ele->add_control(
                'iq_' . $type . '_chart_gradient_3_1_' . $i,
                [
                    'label' => esc_html__('Color',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => $colors[$i],
                    'condition' => $condition
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_gradient_3_2_' . $i,
                [
                    'label' => esc_html__('Second Color',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => $gradientColor[$i],
                    'condition' => array_merge(['iq_' . $type . '_chart_fill_style_type_' . $i => 'gradient'], $condition)
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_pattern_3_' . $i,
                [
                    'label' => esc_html__('Fill Pattern',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => graphina_get_fill_patterns(true),
                    'options' => graphina_get_fill_patterns(),
                    'condition' => array_merge([
                        'iq_' . $type . '_chart_type_3_' . $i . '!' => 'line',
                        'iq_' . $type . '_chart_fill_style_type_' . $i => 'pattern',
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
                    ], $condition)
                ]
            );

            if (function_exists('graphina_marker_setting')) {
                graphina_marker_setting($this_ele, $type, $i);
            }

            graphina_gradient_setting($this_ele, $type, false, true, $i, $condition);
        }

        if (in_array('stroke', $ele_array)) {

            $this_ele->add_control(
                'hr_4_03_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_setting_title_3_' . $i,
                [
                    'label' => esc_html__('Stroke Setting',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_curve_3_' . $i,
                [
                    'label' => esc_html__('Curve',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => graphina_stroke_curve_type(true),
                    'options' => graphina_stroke_curve_type(),
                    'condition' => array_merge(['iq_' . $type . '_chart_type_3_' . $i => ['line', 'area']], $condition)
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_dash_3_' . $i,
                [
                    'label' => 'Dash',
                    'type' => Controls_Manager::NUMBER,
                    'default' => 0,
                    'min' => 0,
                    'max' => 100,
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
                    ]
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_width_3_' . $i,
                [
                    'label' => 'Stroke Width',
                    'type' => Controls_Manager::NUMBER,
                    'default' => 5,
                    'min' => 1,
                    'max' => 20,
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
                    ]
                ]
            );
        }

        if (in_array('drop-shadow', $ele_array)) {
            $this_ele->add_control(
                'hr_4_04_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_drop_shadow_setting_title_3_' . $i,
                [
                    'label' => esc_html__('Drop Shadow Setting',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_drop_shadow_enabled_3_' . $i,
                [
                    'label' => esc_html__('Enabled',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('True',  'graphina-pro-charts-for-elementor'),
                    'label_off' => esc_html__('False',  'graphina-pro-charts-for-elementor'),
                    'default' => '',
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_drop_shadow_color_3_' . $i,
                [
                    'label' => esc_html__('Color',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#FFFFFF00',
                    'condition' => array_merge(['iq_' . $type . '_chart_drop_shadow_enabled_3_' . $i => 'yes'], $condition)
                ]
            );
        }
        if (in_array('tooltip', $ele_array)) {
            $condition = array_merge($condition, ['iq_' . $type . '_chart_tooltip' => 'yes', 'iq_' . $type . '_chart_tooltip_shared' => 'yes']);

            $this_ele->add_control(
                'hr_4_06_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_tooltip_setting_title_3_' . $i,
                [
                    'label' => esc_html__('Tooltip Setting',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i,
                [
                    'label' => esc_html__('Enabled',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
                    'label_off' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
                    'default' => 'yes',
                    'condition' => $condition
                ]
            );
        }
    }
    $this_ele->end_controls_section();
}


function  graphina_pro_parse_csv_column_wise($url = '', $areaType = 'area', $settings = [],$type='')
{
    $seriesCount = !empty($settings['iq_' . $type . '_chart_data_series_count']) ? $settings['iq_' . $type . '_chart_data_series_count'] : ($type === 'geo_google' ? 5 : 0);
    $result = [];
    $values = [];
    $category = [];
    $total = 0;
    $title = [];
    if ($url === '') {
        return ["series" => $result, "category" => $category, 'total' => $total];
    }
    
    if(@file_get_contents($url) === false) {
        return ["series" => $result, "category" => $category, "fail" => 'permission'];
    }

    $file = file_get_contents($url);
    if (strpos($file, '<!DOCTYPE html>') !== false || strpos($file, '<html>') !== false || strpos($file, '</html>') !== false) {
        return ["series" => $result, "category" => $category, 'total' => $total, 'fail' => 'permission'];
    }
    $file = str_replace("\r\n", "\n", $file);
    $arr = explode("\n", $file);
    $csv_seperator = graphina_common_setting_get('csv_seperator');
    $data = [];
    switch ($areaType) {
        case "area" :
        case 'circle':
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    if ($i === 0){
                        $v =  str_getcsv($a,$csv_seperator);
                        $title = filter_var_array($v, FILTER_SANITIZE_STRING);

                        if(is_array($title)){
                            $title =  array_map('strtolower', $title);
                        }
                    }else{
                        $v =  str_getcsv($a,$csv_seperator);
                        $data[] = $v;
                    }
                }
            }
        if(!empty($settings['iq_' . $type . '_chart_csv_y_columns'])
            &&  !empty($settings['iq_' . $type . '_chart_csv_x_columns'])){
            $x_columns = strtolower($settings['iq_' . $type . '_chart_csv_x_columns']);
            $y_columns = $settings['iq_' . $type . '_chart_csv_y_columns'];
            if($areaType === 'area'){
                $y_columns = is_object($y_columns) ? (array)$y_columns : $y_columns;
                $y_columns = !is_array($y_columns) ?  [$y_columns] : $y_columns;

                $y_columns = array_map('strtolower',$y_columns);
                foreach ($title as $key2 => $value2){
                    if( $value2 == $x_columns){
                        $category = array_column($data, $key2);
                    }else if(in_array($value2,$y_columns)){
                        $temp = array_column($data, $key2);
                        $result[] = [
                            'name' => $value2,
                            'data' => array_values(array_map(function ($d) {
                                return empty($d) && $d != '0' ? null : (float)$d;
                            }, $temp))
                        ];
                    }
                }
            }
            if($areaType === 'circle'){
                foreach ($title as $key2 => $value2){
                    if( $value2 == strtolower($x_columns)){
                        $category = array_column($data, $key2);
                    }else if($value2 == strtolower($y_columns)){
                        $temp = array_column($data, $key2);
                        $result = array_values(array_map(function ($d) {
                            return (float)$d;
                        }, $temp));
                        $total = array_sum($result);
                    }
                }
                $category = array_slice($category,0,$seriesCount);
            }
        }
            break;
        case "gantt_google" :
            $seriesCount = 0;
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    if ($i === 0){
                        $v =  str_getcsv($a,$csv_seperator);
                        $title = filter_var_array($v, FILTER_SANITIZE_STRING);

                        if(is_array($title)){
                            $title =  array_map('strtolower', $title);
                            $category = $title;
                        }
                    }else{
                        $v =  str_getcsv($a,$csv_seperator);
                        $result[] = array_combine($title,$v);
                        $seriesCount++;
                    }
                }
            }
            break;
    }
    return ["series" => array_slice($result,0,$seriesCount), "category" => array_values($category), 'total' => $total,'column' => count($title) > 0 ? array_merge([ __('Please select column','graphina-pro-charts-for-elementor')],$title) : $title];
}
/*********************
 * @param $settings
 * @param string $type
 * @param string $areaType
 * @return array[]
 */
function graphina_pro_parse_csv($settings, $type = 'chart_id', $areaType = "area")
{
    $seriesCount = !empty($settings['iq_' . $type . '_chart_data_series_count']) ? $settings['iq_' . $type . '_chart_data_series_count'] : 0;
    $data = [];
    $category = [];
    $total = 0;

    $response = wp_remote_get(
        $settings['iq_' . $type . '_chart_upload_csv']['url'],
        [
            'sslverify' => false,
        ]
    );

    if ('' == $settings['iq_' . $type . '_chart_upload_csv']['url'] || is_wp_error($response) || 200 != $response['response']['code'] || '.csv' !== substr($settings['iq_' . $type . '_chart_upload_csv']['url'], -4)) {
        return ['series' => $data, 'category' => $category, 'total' => $total , 'error' => $response];
    }

    $file = $settings['iq_' . $type . '_chart_upload_csv']['url'];
   
    try {
      

        $opts=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $_file = fopen($file, 'r', false, stream_context_create($opts));

        if (!$_file) {
            return ['series' => $data, 'category' => $category, 'total' => $total];
        }
        $lineNumber = 1;
        $series_start = 0;
        $csv_seperator = graphina_common_setting_get('csv_seperator');
        switch ($areaType) {
            case "mixed":
            case "area" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return empty($d) && $d != '0' ? null : (float)$d;
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case 'org_google':
            case "org":
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);

                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => $row[0]
                        ];
                        unset($row[0]);
                        $file_data['data'] = array_values($row);
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "bubble" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);;
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 3);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? (float)$d[0] : 0,
                                'y' => isset($d[1]) ? (float)$d[1] : 0,
                                'z' => isset($d[2]) ? (float)$d[2] : 0
                            ];
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "nested_column" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);
                    if ($lineNumber !== 1) {
                        $file_data = [
                            'x' => $row[0],
                            'quarters' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 2);
                        $file_data['quarters'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? $d[0] : '',
                                'y' => isset($d[1]) ? (float)$d[1] : 0
                            ];
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "candle" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);
                    if ($lineNumber !== 1) {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 5);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? strtotime(strval($d[0])) * 1000 : 0,
                                'y' => [
                                    isset($d[1]) ? (float)$d[1] : 0,
                                    isset($d[2]) ? (float)$d[2] : 0,
                                    isset($d[3]) ? (float)$d[3] : 0,
                                    isset($d[4]) ? (float)$d[4] : 0
                                ]
                            ];
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "timeline" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                        $category = array_filter($category, function ($v) {
                            return isset($v) && $v !== '';
                        });
                    } else if ($lineNumber > 2) {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 2);
                        $file_data['data'] = array_values(array_map(function ($d, $c) {
                            return [
                                'x' => isset($c) ? $c : 0,
                                'y' => [
                                    isset($d[0]) ? strtotime($d[0]) * 1000 : 0,
                                    isset($d[1]) ? strtotime($d[1]) * 1000 : 0
                                ]
                            ];
                        }, $row, $category));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "geo_google":
            case "circle" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);
                    } else {
                        $data = array_values(array_map(function ($d) {
                            return (float)$d;
                        }, $row));
                        $total = array_sum($data);
                    }
                    $lineNumber++;
                }
                $category = array_slice($category,0,$seriesCount);
                break;
            case "gantt_google" :
                // $series_start = NULL;
                $series_start = 0;
                $seriesCount = 0;
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string,$csv_seperator);
                    $title = $row[0];
                    unset($row[0]);
                    $category[] = $title;
                    $data[$title] = $row;
                    $seriesCount++;
                    $lineNumber++;

                }
                break;
        }
        fclose($_file);
//    }
        // return $data;
        
        return ['series' => array_slice($data,$series_start,$seriesCount), 'category' => array_values($category), 'total' => $total];
    } catch (Exception $e) {
        return ['series' => [], 'category' => [], 'total' => 0];
    }
}

/******************
 * @param $ele_type
 * @param $settings
 * @param $type
 * @param $mainId
 * @param string $from_type
 * @return array[]
 *******************/
function graphina_pro_get_data_from_url($ele_type, $settings, $type, $mainId, $from_type = 'area')
{
    $data = ['series' => [], 'category' => [], 'total' => 0];
    $import_from = $type === 'remote-csv' ? 'iq_' . $ele_type . '_chart_import_from_url' : 'iq_' . $ele_type . '_chart_import_from_google_sheet';
    $val = graphina_get_dynamic_tag_data($settings, $import_from);
    $val = htmlspecialchars_decode($val);
    if ($val !== '') {
        if (Plugin::$instance->editor->is_edit_mode() && $settings['iq_' . $ele_type . '_can_use_cache_development'] === "yes") {
            $data = get_transient('iq_' . $ele_type . '_' . $mainId);
            if (false === $data || count($data['series']) === 0) {
                $data = graphina_pro_get_data_from_remote_csv($val, $from_type,$settings,$ele_type);
                set_transient('iq_' . $ele_type . '_' . $mainId, $data, HOUR_IN_SECONDS);
            }
        } else {
            $data = graphina_pro_get_data_from_remote_csv($val, $from_type,$settings,$ele_type);
        }
    }
    return $data;
}

/*********************
 * @param string $url
 * @param string $areaType
 * @return array[]
 */
function graphina_pro_get_data_from_remote_csv($url = '', $areaType = 'area',$settings=[],$type='')
{
    $seriesCount = !empty($settings['iq_' . $type . '_chart_data_series_count']) ? $settings['iq_' . $type . '_chart_data_series_count'] : 0;
    $result = [];
    $category = [];
    $total = 0;
    if ($url === '') {
        return ["series" => $result, "category" => $category, 'total' => $total];
    }

    if(@file_get_contents($url) == false) {
        return ["series"=> $result, "category" => $category, "fail" => 'permission'];
    }

    $file = file_get_contents($url);
    if (strpos($file, '<!DOCTYPE html>') !== false || strpos($file, '<html>') !== false || strpos($file, '</html>') !== false) {
        return ["series" => $result, "category" => $category, 'total' => $total, 'fail' => 'permission'];
    }
    $file = str_replace("\r\n", "\n", $file);
    $arr = explode("\n", $file);
    $csv_seperator = graphina_common_setting_get('csv_seperator');
    switch ($areaType) {
        case "area" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    if ($i !== 0) {
                        $v = str_getcsv($a,$csv_seperator);
                        $name = $v[0];
                        unset($v[0]);
                        $v = array_map(function ($d) {
                            return empty($d) && $d != '0' ? 'null' : (float)$d;
                        }, $v);
                        $result[] = [
                            "name" => filter_var($name, FILTER_SANITIZE_STRING),
                            "data" => array_values($v)
                        ];
                    } else {
                        $category = filter_var_array( str_getcsv($a,$csv_seperator), FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    }
                }
            }
            break;
        case "bubble" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v =  str_getcsv($a,$csv_seperator);
                    if ($i === 0) {
                        $category = filter_var_array($v, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'data' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 3);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? (float)$d[0] : 0,
                                'y' => isset($d[1]) ? (float)$d[1] : 0,
                                'z' => isset($d[2]) ? (float)$d[2] : 0
                            ];
                        }, $v));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "nested_column" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v =  str_getcsv($a,$csv_seperator);;
                    if ($i !== 0) {
                        $file_data = [
                            'x' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'quarters' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 2);
                        $file_data['quarters'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? filter_var($d[0], FILTER_SANITIZE_STRING) : '',
                                'y' => isset($d[1]) ? (float)$d[1] : 0
                            ];
                        }, $v));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "candle":
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v =  str_getcsv($a,$csv_seperator);;
                    if ($i !== 0) {
                        $file_data = [
                            'name' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'data' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 5);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? strtotime(strval($d[0])) * 1000 : 0,
                                'y' => [
                                    isset($d[1]) ? (float)$d[1] : 0,
                                    isset($d[2]) ? (float)$d[2] : 0,
                                    isset($d[3]) ? (float)$d[3] : 0,
                                    isset($d[4]) ? (float)$d[4] : 0
                                ]
                            ];
                        }, $v));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "timeline" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v = str_getcsv($a,$csv_seperator);;
                    if ($i === 0) {
                        $category = filter_var_array($v, FILTER_SANITIZE_STRING);;
                        unset($category[0]);
                        $category = array_filter($category, function ($v) {
                            return isset($v) && $v !== '';
                        });
                    } else if ($i > 1) {
                        $file_data = [
                            'name' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'data' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 2);
                        $file_data['data'] = array_values(array_map(function ($d, $c) {
                            return [
                                'x' => isset($c) ? $c : 0,
                                'y' => [
                                    isset($d[0]) ? strtotime($d[0]) * 1000 : 0,
                                    isset($d[1]) ? strtotime($d[1]) * 1000 : 0
                                ]
                            ];
                        }, $v, $category));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "circle" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v =  str_getcsv($a,$csv_seperator);;
                    if ($i === 0) {
                        $category = filter_var_array($v, FILTER_SANITIZE_STRING);;
                    } else {
                        $result = array_values(array_map(function ($d) {
                            return (float)$d;
                        }, $v));
                        $total = array_sum($result);
                    }
                }
            }
            $category = array_slice($category,0,$seriesCount);
            break;
        case 'org_google':
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v =  str_getcsv($a,$csv_seperator);
                    if ($i === 0) {
                        $category = filter_var_array($v, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => $v[0]
                        ];
                        unset($v[0]);
                        $file_data['data'] = array_values($v);
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "gantt_google" :
            // $series_start = NULL;
            $series_start = 0;
            $seriesCount = 0;

            $columns = [];

            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v =  str_getcsv($a,$csv_seperator);
                    $title = $v[0];
                    unset($v[0]);
                    $category[] = $title;
                    $result[$title] = $v;
                    $seriesCount++;
                }
                
            }
            break;
    }
    return ["series" => array_slice($result,0,$seriesCount), "category" => array_values($category),"column"=> array_values($columns),'total' => $total];
}

/*********************
 * @param string $api_url
 * @param string $type
 * @return array
 */
function graphina_pro_chart_get_data_from_api($mainType, $settings, $type = '',$selected_item=[])
{
    $seriesCount = !empty($settings['iq_' . $mainType . '_chart_data_series_count']) ? $settings['iq_' . $mainType . '_chart_data_series_count'] : 0;
    $api_url = $settings['iq_' . $mainType . '_chart_import_from_api'];
    $result = ['series' => [], 'category' => [], 'total' => 0];
    if ($api_url === '') {
        return $result;
    }

    $api_url = graphina_replace_dynamic_key($mainType,'api',$settings,$api_url);

    $api_url = graphinaReplaceDynamicFilterKeyChange($settings,$mainType,$selected_item,$api_url);

    $args = [];
    if(isset($settings['iq_'.$mainType.'_authrization_token']) 
        && $settings['iq_'.$mainType.'_authrization_token'] == 'yes') {
        $args['headers'] = [];
        $args['headers'][trim($settings['iq_'.$mainType.'_header_key'])] = $settings['iq_'.$mainType.'_header_token'];
    }
    $response = wp_remote_get($api_url, $args );

    if (is_array($response) && !is_wp_error($response)) {
        $res_body = $response['body']; // use the content
        $res_body = json_decode($res_body, true);
        if (!empty($res_body['data']) && gettype($res_body['data']) === 'array') {
            switch ($type) {
                case 'area' :
                case 'circle':
                case 'org_google':
                    $result['series'] = $res_body['data'];
                    $result['category'] = $res_body['category'];
                    $result['total'] = array_sum($res_body['data']);
                    break;
                case 'bubble':
                case 'nested_column':
                case 'candle':
                    $result['series'] = $res_body['data'];
                    break;
                case 'timeline':
                $result['series'] = array_map(function($v){
                    $v['data'] = array_map(function($v1){
                        $v1['y'] = array_map(function($v2){
                            if(gettype($v2) === 'string'){
                                $v2 = strtotime($v2) * 1000;
                            }
                            return $v2;
                        },$v1['y']);
                        return $v1;
                    },$v['data']);
                    return $v;
                },$res_body['data']);
                    break;
                case 'gantt_google':
                    $seriesCount = count($res_body['data']);
                    $result['series'] = $res_body['data'];
                    $result['category'] = $res_body['category'];
                    break;
            }
        }
    }
    $result['series'] = array_slice($result['series'],0,$seriesCount);
    $result['category'] = $type === 'circle' ? array_slice($result['category'],0,$seriesCount) : $result['category'];
    return $result;
}

/*********************
 * @param array $settings
 * @param string $type
 * @return array
 */
function graphina_pro_chart_get_data_from_sql_builder($settings, $type = '',$selected_item=[])
{

    global $wpdb;
    $seriesCount = !empty($settings['iq_' . $type . '_chart_data_series_count']) ? $settings['iq_' . $type . '_chart_data_series_count'] : 0;
    $chart_array = ['mixed','brush','pie_google','donut_google','line_google','area_google','bar_google','column_google','gauge_google','gantt_google','geo_google'];
    try {
        
        if($settings['iq_' . $type . '_element_import_from_database'] == 'table'){
            if($settings['iq_' . $type . '_element_import_from_table'] == ''){
                $fields = ['not_found' => 'Not found'];
                return ['series' => [], 'category' => ['col 1', 'col 2', 'col 3', 'col 4', 'col 5'], 'total' => 0, 'db_column' => $fields, 'sql_fail' => ''];
             }
             $table_limit = !empty($settings['iq_' . $type . '_element_import_from_table_limit']) && $settings['iq_' . $type . '_element_import_from_table_limit'] > 0 ? ' limit '.$settings['iq_' . $type . '_element_import_from_table_limit'] : ' ';
             $table_offset = !empty($settings['iq_' . $type . '_element_import_from_table_offset']) && $settings['iq_' . $type . '_element_import_from_table_offset'] >= 0 ? ' OFFSET ' . $settings['iq_' . $type . '_element_import_from_table_offset'] : ' ';
             $where_condition = !empty($settings['iq_' . $type . '_chart_sql_where_condition']) ? stripslashes(strip_tags(trim($settings['iq_' . $type . '_chart_sql_where_condition']))) : '';
             $where_condition =stripslashes(strip_tags(trim($where_condition)));
             $where_condition = graphinaReplaceDynamicFilterKeyChange($settings,$type,$selected_item,$where_condition);
             $where_condition = graphina_replace_dynamic_key($type,'table',$settings,$where_condition);
             $query = "SELECT * FROM {$settings['iq_' . $type . '_element_import_from_table']} {$where_condition}  {$table_limit}  {$table_offset} ";
             $result_data = $wpdb->get_results($query);
        }elseif ($settings['iq_' . $type . '_element_import_from_database'] == 'external_database'){
            if(!empty($settings['iq_' . $type . '_element_import_from_external']) && $settings['iq_' . $type . '_element_import_from_external'] != '' ){
                $seleted_database = $settings['iq_' . $type . '_element_import_from_external'];
                if(empty($settings['iq_' . $type . '_element_import_from_table_'.$seleted_database])){
                    $fields = ['not_found' => 'Not found'];
                    return ['series' => [], 'category' => ['col 1', 'col 2', 'col 3', 'col 4', 'col 5'], 'total' => 0, 'db_column' => $fields, 'sql_fail' => __('Please Select Table','graphina-pro-charts-for-elementor')];
                }
                if(graphina_check_external_database('status')){
                    $data = graphina_check_external_database('value');
                    if(array_key_exists($seleted_database,$data)){
                        $seleted_database_value = $data[$seleted_database];
                        $mydb = new wpdb( $seleted_database_value['user_name'],$seleted_database_value['pass'],$seleted_database_value['db_name'],$seleted_database_value['host']);
                        $table_limit = !empty($settings['iq_' . $type . '_element_import_from_table_limit']) && $settings['iq_' . $type . '_element_import_from_table_limit'] > 0 ? ' limit '.$settings['iq_' . $type . '_element_import_from_table_limit'] : ' ';
                        $table_offset = !empty($settings['iq_' . $type . '_element_import_from_table_offset']) && $settings['iq_' . $type . '_element_import_from_table_offset'] >= 0 ? ' OFFSET ' . $settings['iq_' . $type . '_element_import_from_table_offset'] : ' ';
                        $where_condition = !empty($settings['iq_' . $type . '_chart_sql_where_condition']) ? stripslashes(strip_tags(trim($settings['iq_' . $type . '_chart_sql_where_condition']))) : '';
                        $where_condition = stripslashes(strip_tags(trim($where_condition)));
                        $where_condition = graphinaReplaceDynamicFilterKeyChange($settings,$type,$selected_item,$where_condition);
                        $where_condition = graphina_replace_dynamic_key($type,'table',$settings,$where_condition);
                        $query = "SELECT * FROM {$settings['iq_' . $type . '_element_import_from_table_'.$seleted_database]} {$where_condition} {$table_limit}  {$table_offset} ";
                        $result_data = $mydb->get_results($query);
                    }
                }else{
                    if(in_array($type,$chart_array)){
                        return ['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('Selected Database Not found.',  'graphina-pro-charts-for-elementor')]];
                    }
                    wp_send_json(['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('Selected Database Not found.',  'graphina-pro-charts-for-elementor')]]);
                }
            }else{
                if(in_array($type,$chart_array)){
                    return ['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No Database found, Please check your sql statement.',  'graphina-pro-charts-for-elementor')]];
                }
                wp_send_json(['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No Database found, Please check your sql statement.',  'graphina-pro-charts-for-elementor')]]);
            }
        }
        else{
            $sql_custom_query = strip_tags(trim($settings['iq_' . $type . '_chart_sql_builder']));
            
            if ($sql_custom_query === null) return [];
            $sql_custom_query = stripslashes($sql_custom_query);
            $sql_custom_query = graphinaReplaceDynamicFilterKeyChange($settings,$type,$selected_item,$sql_custom_query);
            $sql_custom_query = graphina_replace_dynamic_key($type,'table',$settings,$sql_custom_query);
            $result_data = $wpdb->get_results($sql_custom_query);
            
        }
        $result = ['series' => [], 'category' => [], 'total' => 0];
       
        if (!empty($result_data) && count($result_data)) {

            $fields = [];

            if(in_array($type, ['gantt_google'])){
                
                
                if((isset($settings['iq_' . $type . '_element_import_from_database']) && $settings['iq_' . $type . '_element_import_from_database'] == "table") ||
                   (isset($settings['iq_' . $type . '_chart_sql_builder']) && $settings['iq_' . $type . '_chart_sql_builder'] != null)){
                    
                    $result = ['series' => [], 'category' => ['col 1', 'col 2', 'col 3', 'col 4', 'col 5'], 'total' => 0, 'db_column' => [], 'sql_fail' => ''];
                       
                   
                    foreach ($result_data[0] as $key => $value) {
                        
                        if(!empty($settings['iq_' . $type . '_element_import_from_database_lowercase_column']) && $settings['iq_' . $type . '_element_import_from_database_lowercase_column'] === 'yes'){
                            $key = strtolower($key);
                        }
                        $fields[] = $key;
                    }
                    $result['category'] = $fields;
                    $fields = count($fields) > 0 ? array_merge([ __('Please select column','graphina-pro-charts-for-elementor')],$fields) : $fields;
                    $result['db_column'] = $fields;
                  
                    if($fields > 0){
                        $series = [];
                        foreach($result_data as $key => $value){
                            $value = json_decode(json_encode($value), true);
                            $series[] = $value;
                        }
                    }  
                    
                    $result['series'] = $series;

                    return $result;
                }
            }else{


                $values = [];
                
                // dynamic column name
                $x_columns = $settings['iq_' . $type . '_chart_sql_builder_x_columns'];   
                $y_columns = $settings['iq_' . $type . '_chart_sql_builder_y_columns'];

                if(!is_array($y_columns)) {
                    $y_columns = [$y_columns] ;
                }

                foreach ($result_data[0] as $key => $value) {
                    if(!empty($settings['iq_' . $type . '_element_import_from_database_lowercase_column']) && $settings['iq_' . $type . '_element_import_from_database_lowercase_column'] === 'yes'){
                        $key = strtolower($key);
                    }
                    $fields[] = $key;
                }
                $fields = count($fields) > 0 ? array_merge([ __('Please select column','graphina-pro-charts-for-elementor')],$fields) : $fields;
                $category = [];

                $series = ['series' => [], 'category' => ['col 1', 'col 2', 'col 3', 'col 4', 'col 5'], 'total' => 0, 'db_column' => $fields, 'sql_fail' => ''];

                if(is_array($y_columns) && $x_columns !== '' && $x_columns !== null){
                    array_push($y_columns,$x_columns);
                }  
                
                if (count($y_columns) > 0) {

                    foreach ($y_columns as $key => $column) {

                        foreach ($result_data as $key => $value) {
                            if(!empty($settings['iq_' . $type . '_element_import_from_database_lowercase_column']) && $settings['iq_' . $type . '_element_import_from_database_lowercase_column'] === 'yes'){
                                $value = (object)array_change_key_case((array)$value,CASE_LOWER);
                            }
                            // value casting to int
                            if (in_array($type, ['pie' , 'donut' , 'radial' , 'polar','pie_google','donut_google','gauge_google','gantt_google','geo_google'])) {
                                $values[$column][] = $column == $x_columns ? $value->$column : (int)$value->$column;
                            } else {
                                $values[$column][] = $value->$column;
                            }
                        }
    
                        if ($column != $x_columns){
                            $temp_element = [
                                'name' => $column,
                                'data' => $values[$column]
                            ];
                            if (in_array($type, ['pie' , 'donut' , 'radial' , 'polar','pie_google','donut_google','gauge_google','gantt_google','geo_google'])) {
                                $series['series'] = $values[$column];
                            } else {
                                $series['series'][] = $temp_element;
                            }
                        }
                    }
                    
                }

                if ($x_columns !== '' && $x_columns !== null && !empty($values)
                    && !empty($values[$x_columns]) && count($values[$x_columns]) > 0){
                    $series['category'] = $values[$x_columns];
                    unset($values[$x_columns]);
                }
                $series['x_axis_value'] = $x_columns ;

                if (!graphina_is_preview_mode()) {
                    if (empty($result_data) || count($result_data) === 0) {
                        $series['sql_fail'] = esc_attr__('No data found, Please check your sql statement.',  'graphina-pro-charts-for-elementor');
                    }
                }

                $series['category'] = in_array($type, ['pie' , 'donut' , 'radial' , 'polar','pie_google','donut_google','gauge_google','geo_google'])
                    ? array_slice($series['category'],0,$seriesCount) : $series['category'];
                $series['series'] = array_slice($series['series'],0 ,$seriesCount);
                return $series;

            }

            

        } else {
            if(in_array($type,$chart_array)){
                return ['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No data found, Please check your sql statement.',  'graphina-pro-charts-for-elementor')]];
            }
            wp_send_json(['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No data found, Please check your sql statement.',  'graphina-pro-charts-for-elementor')]]);
        }

    } catch (Exception $exception) {
        if(in_array($type,$chart_array)){
            return ['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No data found, Please check your sql statement.',  'graphina-pro-charts-for-elementor')]];
        }
        wp_send_json(['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No data found, Please check your sql statement.',  'graphina-pro-charts-for-elementor')]]);
    }

}

/**********************
 * @param string $this_ele
 * @param string $type
 */
function graphina_pro_get_dynamic_options($this_ele = '', $type = '')
{
    $option = [
        'sqlQuery' => esc_html__('SQL Query',  'graphina-pro-charts-for-elementor'),
        'table' => esc_html__('Table',  'graphina-pro-charts-for-elementor'),
    ];
    $external_value=[];
    if(graphina_check_external_database('status')){
        $data = graphina_check_external_database('value');
        $option['external_database'] =esc_html__('External',  'graphina-pro-charts-for-elementor');
        $external_option = array_keys($data);
        if(!empty($external_option) && is_array($external_option) && count($external_option) > 0){
            foreach ($external_option as $key => $value){
                $external_value[$value]= $value;
            }
        }
    }
    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_database',
        [
            'label' => esc_html__('Select Mode',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'sqlQuery',
            'options' => $option,
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_external',
        [
            'label' => esc_html__('Select Database',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => $external_value,
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                'iq_' . $type . '_element_import_from_database' => 'external_database'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    if(graphina_check_external_database('status') && !empty($external_value) && is_array($external_value) && count($external_value) > 0){
        foreach ($external_value as $key => $table){
            $this_ele->add_control(
                'iq_' . $type . '_element_import_from_table_'.$table,
                [
                    'label' => esc_html__('Select Table',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => graphina_pro_external_table_list($table),
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                        'iq_' . $type . '_element_import_from_database' => 'external_database',
                        'iq_' . $type . '_element_import_from_external' => $table
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );
        }
    }

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_table',
        [
            'label' => esc_html__('Select Table',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => graphina_pro_list_db_tables(),
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                'iq_' . $type . '_element_import_from_database' => 'table'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_database_lowercase_column',
        [
            'label' => esc_html__('Uppercase Columns Name',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
            ],
            'description' => esc_html__('Note: If selected table have uppercase in any column name enable this option','graphina-pro-charts-for-elementor')
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_sql_where_condition',
        [
            'label' => esc_html__('Condition Query',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'dynamic' => ['active' => true],
            'placeholder' => esc_html__('Where column_name = column_value',  'graphina-pro-charts-for-elementor'),
            'description' => esc_html__('Where condition for selected table, Use Double quote( " " ) in condition value ',  'graphina-pro-charts-for-elementor'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                'iq_' . $type . '_element_import_from_database' => ['table','external_database']
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_table_offset',
        [
            'label' => esc_html__('Offset',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                'iq_' . $type . '_element_import_from_database' => ['table','external_database']
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_table_limit',
        [
            'label' => esc_html__('Limit',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::NUMBER,
            'default' => 15,
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                'iq_' . $type . '_element_import_from_database' => ['table','external_database']
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    
    $this_ele->add_control(
        'iq_' . $type . '_chart_sql_builder',
        [
            'label' => esc_html__('SQL Raw Query',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'dynamic' => ['active' => true],
            'placeholder' => esc_html__('SQL Builder',  'graphina-pro-charts-for-elementor'),
            'description' => esc_html__('Fetch data from customize/raw query builder, Use Double quote( " " ) in condition value ',  'graphina-pro-charts-for-elementor'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                'iq_' . $type . '_element_import_from_database' => 'sqlQuery'
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_table_dynamic_key',
        [
            'label' => esc_html__('Dynamic Keys',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Use dynamic key in  Query,  it will replace key will dynamic value (example : colum_name={{CURRENT_USER_ID}} <strong><a href="https://wordpress.iqonic.design/docs/product/graphina-elementor-charts-and-graphs/use-dynamic-data-in-widgets/dynamic_key/" target="_blank">List of Dynamic key</a></strong>',  'graphina-pro-charts-for-elementor'),
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
            ],
        ]
    );

    if($type != 'data_table_lite'){

        $this_ele->add_control(
            'iq_' . $type . '_chart_sql_builder_refresh',
            [
                'label' => esc_html__('Refresh', 'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'refresh',
                'options' => [
                    "refresh" =>[
                        'title' => esc_html__('Classic', 'graphina-pro-charts-for-elementor'),
                        'icon' => 'fas fa-sync'
                    ]
                ],
                'description' => esc_html__('Click if x/y-axis column list is showing empty', 'graphina-pro-charts-for-elementor'),
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                ]
            ]
        );


        if(in_array($type,['gantt_google'])){
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_id_columns',
                [
                'label' =>esc_html__('ID Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_name_columns',
                [
                'label' =>esc_html__('Name Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_resource_columns',
                [
                'label' =>esc_html__('Resource Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_start_date_columns',
                [
                'label' =>esc_html__('Start Date Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_end_date_columns',
                [
                'label' =>esc_html__('End Date Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_duration_columns',
                [
                'label' =>esc_html__('Duration Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_percent_columns',
                [
                'label' =>esc_html__('Percent Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_dependencies_columns',
                [
                'label' =>esc_html__('Dependencies Column',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' =>['not_found' => 'Not found'],
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    
                    'iq_' . $type . '_chart_dynamic_data_option' => ['sql-builder']
                ],
                ]
            );
        }else{
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_builder_x_columns',
                [
                    'label' => esc_html__('X-Axis Columns',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' => ['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                    ]
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_sql_builder_y_columns',
                [
                    'label' => esc_html__('Y-Axis Columns',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' => ['not_found' => 'Not found'],
                    'multiple' =>  !in_array($type, ['pie' , 'donut' , 'radial' , 'polar','distributed_column','pie_google','donut_google','gauge_google','geo_google']),
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
                    ],
                    'description' => in_array($type,['geo_google']) ? esc_html__('Note: X-axis column should contain country/region name only', 'graphina-pro-charts-for-elementor') : '',
                ]
            );
        }

    }


    $this_ele->add_control(
        'iq_' . $type . '_chart_upload_csv',
        [
            'label' => esc_html__('Upload CSV',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::MEDIA,
            'dynamic' => ['active' => true],
            'media_type' => 'text/csv',
            'default' => [
                'id' => '',
                'url' => '',
            ],
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'csv'
            ]
        ]);
        $this_ele->add_control(
            'iq_' . $type . '_chart_import_from_url',
            [
                'label' => esc_html__('URL',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Remote File URL',  'graphina-pro-charts-for-elementor'),
                'description' => esc_html__('This URL is used to fetch CSV from remote server',  'graphina-pro-charts-for-elementor'),
                'label_block' => true,
                'default' => '',
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    'iq_' . $type . '_chart_dynamic_data_option' => 'remote-csv'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
    
        $this_ele->add_control(
            'iq_' . $type . '_chart_import_from_google_sheet',
            [
                'label' => esc_html__('Enter Google Sheet Published URL',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Google Sheet Published URL',  'graphina-pro-charts-for-elementor'),
                'label_block' => true,
                'default' => '',
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => ['dynamic'],
                    'iq_' . $type . '_chart_dynamic_data_option' => 'google-sheet'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        if(in_array($type,['line', 'column', 'area', 'pie', 'donut', 'radial', 'radar', 'polar','distributed_column','scatter','mixed','brush', 'heatmap','pie_google','donut_google','line_google','area_google','column_google','bar_google','gauge_google','gantt_google','geo_google'])){

            $this_ele->add_control(
                'iq_' . $type . '_chart_csv_column_wise_enable',
                [
                    'label' => esc_html__('Data Column Wise',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv'],
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_csv_column_wise_refresh',
                [
                    'label' => esc_html__('Refresh', 'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'default' => 'refresh',
                    'options' => [
                        "refresh" =>[
                            'title' => esc_html__('Classic', 'graphina-pro-charts-for-elementor'),
                            'icon' => 'fas fa-sync'
                        ]
                    ],
                    'description' => esc_html__('Click if x/y-axis column list is showing empty', 'graphina-pro-charts-for-elementor'),
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv'],
                    ]
                ]
            );

            if(in_array($type,['gantt_google'])){
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_id_columns',
                    [
                    'label' =>esc_html__('ID Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_name_columns',
                    [
                    'label' =>esc_html__('Name Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_resource_columns',
                    [
                    'label' =>esc_html__('Resource Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_start_date_columns',
                    [
                    'label' =>esc_html__('Start Date Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_end_date_columns',
                    [
                    'label' =>esc_html__('End Date Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_duration_columns',
                    [
                    'label' =>esc_html__('Duration Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_percent_columns',
                    [
                    'label' =>esc_html__('Percent Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_dependencies_columns',
                    [
                    'label' =>esc_html__('Dependencies Column',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','remote-csv']
                    ],
                    ]
                );
            }else{
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_x_columns',
                    [
                    'label' =>esc_html__('X-Axis Columns',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => '',
                    'options' =>['not_found' => 'Not found'],
                    'condition' => [
                        'iq_' . $type . '_chart_data_option' => ['dynamic'],
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','google-sheet','remote-csv']
                    ],
                    ]
                );
    
                $this_ele->add_control(
                    'iq_' . $type . '_chart_csv_y_columns',
                    [
                        'label' => esc_html__('Y-Axis Columns',  'graphina-pro-charts-for-elementor'),
                        'type' => Controls_Manager::SELECT2,
                        'default' => '',
                        'options' =>   ['not_found' => 'Not found'],
                        'multiple' =>  !in_array($type, ['pie' , 'donut' , 'radial' , 'polar','distributed_column','pie_google','donut_google','gauge_google','geo_google']),
                        'condition' => [
                            'iq_' . $type . '_chart_data_option' => ['dynamic'],
                            'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                            'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet','google-sheet','remote-csv'],
                        ]
                    ]
                );

            }

            $this_ele->add_control(
                'iq_' . $type . '_element_download_csv_column_wise_sample_doc',
                [
                    'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                            <a style="text-decoration: underline; font-style: italic" href="' . GRAPHINA_PRO_URL . '/elementor/sample-doc/column-wise-csv/' . $type . '-chart-sample.csv" download>here</a>
                                            to download sample CSV file.                                            
                                        </div>',
                    'type' => Controls_Manager::RAW_HTML,
                    'condition' => [
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => ['csv','google-sheet', 'remote-csv']
                    ]
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_element_download_column_wise_google_sheet',
                [
                    'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                            <a style="text-decoration: underline; font-style: italic" target="_blank" href="' . graphina_pro_get_spreadsheet_column_wise($type) . '">here</a>
                                            to view the sample format.                                           
                                        </div>',
                    'type' => Controls_Manager::RAW_HTML,
                    'condition' => [
                        'iq_' . $type . '_chart_csv_column_wise_enable' => 'yes',
                        'iq_' . $type . '_chart_dynamic_data_option' => 'google-sheet'
                    ]
                ]
            );

        }    


    $this_ele->add_control(
        'iq_' . $type . '_chart_import_from_api',
        [
            'label' => esc_html__('URL',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => esc_html__('URL',  'graphina-pro-charts-for-elementor'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_api_dynamic_key',
        [
            'label' => esc_html__('Dynamic Keys',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Use Dynamic key in Api url , it will replace key will dynamic value (example : &user_id={{CURRENT_USER_ID}} <strong><a href="https://wordpress.iqonic.design/docs/product/graphina-elementor-charts-and-graphs/use-dynamic-data-in-widgets/dynamic_key/" target="_blank">List of Dynamic key</a></strong>',  'graphina-pro-charts-for-elementor'),
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_can_use_cache_development',
        [
            'label' => esc_html__('Use Cache For Development',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
            'description' => esc_html__("This feature is used to cache the CSV file for 1 hour only in editor mode. It will not generate cache for live site or preview",  'graphina-pro-charts-for-elementor'),
            'default' => false,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => ['remote-csv', 'google-sheet']
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_authrization_token',
        [
            'label' => esc_html__('Enable Header Options',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
            'default' => false,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_header_key',
        [
            'label' => esc_html__('Header Key',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'iq_' . $type . '_authrization_token' => 'yes',
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_header_token',
        [
            'label' => esc_html__('Header Token',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'iq_' . $type . '_authrization_token' => 'yes',
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_download_csv_sample_doc',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                        <a style="text-decoration: underline; font-style: italic" href="' . GRAPHINA_PRO_URL . '/elementor/sample-doc/' . $type . '-chart-sample.csv" download>here</a>
                                        to download sample CSV file.                                            
                                    </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_chart_csv_column_wise_enable!' => 'yes',
                'iq_' . $type . '_chart_dynamic_data_option' => ['csv', 'remote-csv']
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_download_google_sheet',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                        <a style="text-decoration: underline; font-style: italic" target="_blank" href="' . graphina_pro_get_sheet($type) . '">here</a>
                                        to view the sample format.                                           
                                    </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_chart_csv_column_wise_enable!' => 'yes',
                'iq_' . $type . '_chart_dynamic_data_option' => 'google-sheet'
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_download_sample_json',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                        <a style="text-decoration: underline; font-style: italic" href="' . GRAPHINA_PRO_URL . '/elementor/sample-json/' . $type . '-chart-sample.json" download>here</a>
                                        to download sample JSON file.                                            
                                    </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ]
        ]
    );

    $explodeValue = explode("_",$type);
    $uniqueID = !empty($explodeValue[1]) && $explodeValue[1] == 'google' ? 'g-'.$explodeValue[0].'-' : 'a-'.$type.'-';
    $id = rand(pow(10, 4-1), pow(10, 4)-1);
    $this_ele->add_control(
        'iq_' . $type . '_element_filter_widget_id',
        [
            'label' => esc_html__('Unquie Widget ID ','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => $uniqueID.$id,
            'label_block'=>true,
            'condition' => [
                'iq_' . $type . '_chart_data_option' => 'dynamic',
                'iq_' . $type . '_chart_dynamic_data_option' => ['filter']
            ],
            'description' => esc_html__('Note:Use this Widget id to Wordpress filter and identify the chart/counter/datatable during development to ensure right data goes into right widget.  ','graphina-pro-charts-for-elementor')
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_filter_refers_link',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click Here For
                                    <a style="text-decoration: underline; font-style: italic" href="https://wordpress.iqonic.design/docs/product/graphina-elementor-charts-and-graphs/use-dynamic-data-in-widgets/data-from-wordpress-filter" target="_blank">Documentation For add filter</a>
                                    </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_chart_data_option' => 'dynamic',
                'iq_' . $type . '_chart_dynamic_data_option' => ['filter']
            ]
        ]
    );

}

/**********************
 * @param string $type
 * @return string
 */
function graphina_pro_get_sheet($type = 'area')
{

    $sheet = 'https://docs.google.com/spreadsheets/d/1zCIRmobXye0BSgUnY4vMG4Sn6LEp6We_4RjJdPS4CYg/edit?usp=sharing';

    switch ($type) {

        case 'donut':
        case 'pie':
        case 'polar':
        case 'radial':
        case 'gauge_google':
        case 'pie_google':
        case 'donut_google':
            $sheet = 'https://docs.google.com/spreadsheets/d/1v2v1W61vZahN2qhbCL2Z79CEnzPOwJLyQqswIOzjxmU/edit?usp=sharing';
            break;
        case 'bubble':
            $sheet = 'https://docs.google.com/spreadsheets/d/1Wqv3095LVzkKG_1uwNWWiSovyWwnC0EdqVgIkm746Yo/edit?usp=sharing';
            break;
        case 'timeline':
            $sheet = 'https://docs.google.com/spreadsheets/d/1trOwuavFpWMXEG-53pjRLAA_fhgNqOvBWfuQ6S6JNDM/edit?usp=sharing';
            break;
        case 'nested_column':
            $sheet = 'https://docs.google.com/spreadsheets/d/1drqaZ3CbRseRXJekNHBSnW6v-S91opSfh0QhBRZbjJ8/edit?usp=sharing';
            break;
        case 'counter':
            $sheet = 'https://docs.google.com/spreadsheets/d/1ZEtWaHVocV3O2G2CO1iHK38vKcAe1sJjEDj_WUdINIg/edit?usp=sharing';
            break;
        case 'advance-datatable':
        case 'data_table_lite':
            $sheet = 'https://docs.google.com/spreadsheets/d/1NPZwZXIoG0Cgl8mtnvV8U6MqjuJ8_VFplNm3qnj_Wyo/edit?usp=sharing';
            break;
        case 'brush':
            $sheet = 'https://docs.google.com/spreadsheets/d/1fBY8WPnx8PssjQ-NFX8DsBdfoFatVrJ8aE3hKlE9Aa4/edit?usp=sharing';
            break;
        case 'candle':
            $sheet = 'https://docs.google.com/spreadsheets/d/13JrMfu51H26FDi9AUtb6LLD-Vi5qDdD9uq5yXtIA3gg/edit?usp=sharing';
            break;
        case 'heatmap':
            $sheet = 'https://docs.google.com/spreadsheets/d/1tcW_dpmfOgCFizuyMQnVIkVuD9eTZFs9pr2Z3PJGo7I/edit?usp=sharing';
            break;
        case 'mixed':
            $sheet = 'https://docs.google.com/spreadsheets/d/15cqsdg5yxl3n-U7IBYFSVESQJCQXayJ4Y_oZxqTxfK0/edit?usp=sharing';
            break;
        case 'distributed_column':
            $sheet = 'https://docs.google.com/spreadsheets/d/18imjZycqMfUTEy8SEXCJDLLZlfSGH8L3VL_JavfALPU/edit?usp=sharing';
            break;
        case 'geo_google':
            $sheet = 'https://docs.google.com/spreadsheets/d/1No5I7PTQBid4mbilUwSlteVFo1v5TMKPIqGkPEzvELA/edit?usp=sharing';
            break;
        case 'org_google':
            $sheet = 'https://docs.google.com/spreadsheets/d/1mnmeqnM33ZkPBMgVgXfCZz8FtviUG-gQmgHz0UCwIU0/edit?usp=sharing';
            break;
        case 'gantt_google':
            $sheet = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTY5GL8lCf90c9_k3HKILLAaL4uOO4W--pYa3iQ-QdhLQTXHr-fGzagv5xehe-WwNukJg-eFL_ZUoqK/pub?output=csv';
            break;
    }

    return $sheet;
}

function graphina_pro_get_spreadsheet_column_wise($type = 'area')
{

    $sheet = 'https://docs.google.com/spreadsheets/d/11oF-ibtQzwLs8TH_9RcpPBorDmRgSrksFpoV_PLCtYw/edit?usp=sharing';

    switch ($type) {

        case 'donut':
        case 'pie':
        case 'polar':
        case 'radial':
        case 'gauge_google':
        case 'pie_google':
        case 'donut_google':
            $sheet = 'https://docs.google.com/spreadsheets/d/1Jji8Fjg270uSatxiFnxdXgy_Yxsx4rP7RsTv5I1qn3k/edit?usp=sharing';
            break;
        case 'brush':
            $sheet = 'https://docs.google.com/spreadsheets/d/1hqU7y4neCt1GAKEubhvNF_wrbLYUEhGZphLPpUAfrKw/edit?usp=sharing';
            break;
        case 'geo_google':
            $sheet ='https://docs.google.com/spreadsheets/d/1No5I7PTQBid4mbilUwSlteVFo1v5TMKPIqGkPEzvELA/edit?usp=sharing';
            break;

    }

    return $sheet;
}

function graphina_get_data_for_key($settings, $key, $type)
{
    $val = graphina_get_dynamic_tag_data($settings, $key);
    if (empty($val)) {
        $val = $settings[$val];
    }
    switch ($type) {
        case "array_string":
            $val = array_map(function ($val) {
                return (string)$val;
            }, $val);
            break;
        case "array_float":
            $val = array_map(function ($val) {
                return (float)$val;
            }, $val);
            break;
        case "int":
            $val = (int)$val;
            break;
        case "float":
            $val = (float)$val;
            break;
        default :
            $val = (string)$val;
            break;
    }
    return $val;
}

/*******************************
 * @param $settings
 * @param $type
 * @param $mainId
 * @return array[]
 */
function graphina_pro_get_chart_responsive_data($settings, $type, $mainId = null)
{

    $responsive = [];
    $responsive_element = ['tablet', 'mobile'];
    $current_data = [];
    $default_data = [
        'dataLabels_enabled' => graphina_get_data_for_key($settings, 'iq_' . $type . '_chart_datalabel_show', 'string')
    ];
    $options = [];

    foreach ($responsive_element as $ele) {
        $current_data = [
            'dataLabels_enabled' => graphina_get_data_for_key($settings, 'iq_' . $type . '_chart_datalabel_show_'.$ele, 'string')
        ];
        $options[$ele] = [
            "options" => [
                "chart" => [
                    'height' => (int)$settings['iq_' . $type . '_chart_height_'.$ele]
                ],
                "dataLabels" => [
                    "enabled" => !empty($settings['iq_' . $type . '_chart_datalabel_show_'.$ele]) &&  $settings['iq_' . $type . '_chart_datalabel_show_'.$ele] === 'yes'
                ]
            ]
        ];
    }


    /***********************************
     *        Responsive 1024
     ***********************************/

    $responsive['1024'] = array_merge([
        "breakpoint" => 1024
    ],$options['tablet']);

    /***********************************
     *        Responsive 767
     ***********************************/

    $responsive['767'] = array_merge([
        "breakpoint" => 767
    ],$options['mobile']);

    return array_values($responsive);
}

function apexChartProLocales(){
    $data = [
        'name' => 'en',
        'options' => [
            'toolbar'=> [
                'download'=> esc_html__('Download SVG', 'graphina-pro-charts-for-elementor'),
                'selection'=> esc_html__('Selection', 'graphina-pro-charts-for-elementor'),
                'selectionZoom'=> esc_html__('Selection Zoom', 'graphina-pro-charts-for-elementor' ),
                'zoomIn'=> esc_html__('Zoom In', 'graphina-pro-charts-for-elementor'),
                'zoomOut'=> esc_html__('Zoom Out', 'graphina-pro-charts-for-elementor'),
                'pan'=> esc_html__('Panning', 'graphina-pro-charts-for-elementor'),
                'reset'=> esc_html__('Reset Zoom', 'graphina-pro-charts-for-elementor'),
                'menu' => esc_html__('Menu', 'graphina-pro-charts-for-elementor'),
                "exportToSVG"=>esc_html__('Download SVG', 'graphina-pro-charts-for-elementor'),
                "exportToPNG"=>esc_html__('Download PNG', 'graphina-pro-charts-for-elementor'),
                "exportToCSV"=>esc_html__('Download CSV', 'graphina-pro-charts-for-elementor'),
            ]
        ]

    ];

    return json_encode($data);
}

function graphina_pro_datatable_content($this_ele,$settings, $type)
{
    $data = ['header' => [], 'body' => []];

    $dataTypeOption = $settings['iq_' . $type . '_chart_dynamic_data_option'];

    switch ($dataTypeOption) {
        case "csv":
            $data = graphina_pro_element_parse_csv($this_ele, $type, 'table');
            break;
        case "remote-csv":
        case "google-sheet":
            $data = graphina_pro_element_data_remote_csv($dataTypeOption == "remote-csv" ? $settings['iq_' . $type . '_chart_import_from_url'] : $settings['iq_' . $type . '_chart_import_from_google_sheet'], 'table', $type, $this_ele);
            break;
        case "sql-builder":
            $data = graphina_pro_element_get_data_from_database($type, $settings['iq_' . $type . '_element_import_from_database'], $settings['iq_' . $type . '_element_import_from_table'], $settings['iq_' . $type . '_chart_sql_builder'],$settings['iq_' . $type . '_chart_sql_where_condition'],$settings['iq_' . $type . '_element_import_from_table_limit'],$settings['iq_' . $type . '_element_import_from_table_offset'],$settings );
            break;
        case "api":
            $data = graphina_pro_element_get_data_from_api($settings['iq_' . $type . '_chart_import_from_api'], $type, $this_ele);
            break;
        case 'filter':
            update_post_meta(get_the_ID(),$this_ele->get_id(),$settings['iq_' . $type . '_element_filter_widget_id']);
            $data = apply_filters('graphina_extra_data_option', $data, $type, $settings,$settings['iq_' . $type . '_element_filter_widget_id']);
            break;
    }

    $data = apply_filters('graphina_addons_render_section', $data, $type, $settings);

    if (isset($data['fail']) && $data['fail'] === 'permission') {
        $dataTypeOption = $settings['iq_' . $type . '_chart_dynamic_data_option'];
        switch ($dataTypeOption) {
            case "google-sheet" :
                $data['fail_message'] = "<pre><b>" . esc_html__('Please check file sharing permission and "Publish As" type is CSV or not. ',  'graphina-pro-charts-for-elementor') . "</b><small><a target='_blank' href='https://youtu.be/Dv8s4QxZlDk'>" . esc_html__('Click for reference.',  'graphina-pro-charts-for-elementor') . "</a></small></pre>";
                break;
            case "remote-csv" :
            default:
                $data['fail_message'] = "<pre><b>" . (isset($data['errorMessage']) ? $data['errorMessage'] :  esc_html__('Please check file sharing permission.',  'graphina-pro-charts-for-elementor')). "</b></pre>";
                break;
        }
    }
    return $data;
}

function graphinaReplaceDynamicFilterKeyChange($settings,$type,$selected_item,$sql_custom_query){
    if (!empty($settings['iq_'.$type.'_chart_filter_enable']) && $settings['iq_'.$type.'_chart_filter_enable'] == 'yes' && !empty($selected_item) && is_array($selected_item) ) {
        foreach ($settings['iq_'.$type.'_chart_filter_list'] as $key => $value) {
            if (!empty($value['iq_'.$type.'_chart_filter_value_key']) && !empty($selected_item[$key])) {
                if (!empty($settings['iq_' . $type . '_chart_dynamic_data_option']) && $settings['iq_' . $type . '_chart_dynamic_data_option'] === 'sql-builder' && strstr($sql_custom_query, trim($selected_item[$key])) && count($selected_item) === count(array_unique($selected_item))) {
                    $sql_custom_query = str_replace(array("'".$value['iq_'.$type.'_chart_filter_value_key']."'",'"'.$value['iq_'.$type.'_chart_filter_value_key'].'"'),$selected_item[$key], $sql_custom_query);
                }else{
                    $sql_custom_query = str_replace($value['iq_'.$type.'_chart_filter_value_key'],trim($selected_item[$key]), $sql_custom_query);
                }
            }
        }
    }
    return $sql_custom_query;
}

function graphinajj($value){
    global $wpdb;
    if(!empty($value) ){
        return [ $value => $value];
    }
    return ['value' => 'value'];
}