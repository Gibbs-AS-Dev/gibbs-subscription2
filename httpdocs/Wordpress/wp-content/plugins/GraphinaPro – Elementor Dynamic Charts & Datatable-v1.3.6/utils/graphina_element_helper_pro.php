<?php
//namespace Elementor;

/**********************
 * @param string $type
 * @param bool $first
 * @param bool $keys
 * @return array|mixed
 */

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

function graphina_pro_element_data_enter_options($type = '', $first = false, $keys = false,$widgetType = 'advance-datatable')
{
    switch ($type) {
        case 'main_type':
            $options = [
                'manual' => esc_html__('Manual',  'graphina-pro-charts-for-elementor'),
                'dynamic' => esc_html__('Dynamic',  'graphina-pro-charts-for-elementor'),
            ];
            if (get_option('graphina_firebase_addons') === '1' ) {
                $options['firebase'] = esc_html__('Firebase',  'graphina-pro-charts-for-elementor');
            }
            if(function_exists('graphinaForminatorAddonActive')){
                if(graphinaForminatorAddonActive()){
                    if($widgetType === 'advance-datatable'){
                        $options['forminator'] = esc_html__('Forminator Addon', 'graphina-pro-charts-for-elementor');
                    }
                }
            }
            break;
        case 'counter':
            $options = [
                'csv' => esc_html__('CSV',  'graphina-pro-charts-for-elementor'),
                'remote-csv' => esc_html__('Remote CSV',  'graphina-pro-charts-for-elementor'),
                'google-sheet' => esc_html__('Google Sheet',  'graphina-pro-charts-for-elementor'),
                'api' => esc_html__('API',  'graphina-pro-charts-for-elementor'),
                'database' => esc_html__('Database',  'graphina-pro-charts-for-elementor'),
            ];
            if(isGraphinaPro()){
                $options['filter'] = esc_html__('Data From Filter', 'graphina-charts-for-elementor');
            }
            break;
        case 'graphina_counter_operations':
            $options = [
                '' => esc_html__('None',  'graphina-pro-charts-for-elementor'),
                'sum' => esc_html__('Sum',  'graphina-pro-charts-for-elementor'),
                'avg' => esc_html__('Average',  'graphina-pro-charts-for-elementor'),
                'percentage' => esc_html__('Percentage',  'graphina-pro-charts-for-elementor'),
            ];
            break;
        case 'index_type':
            $options = [
                'number' => esc_html__('Number',  'graphina-pro-charts-for-elementor'),
                'roman' => esc_html__('Roman Number',  'graphina-pro-charts-for-elementor')
            ];
            break;
        case 'counter_layout':
            $options = [
                'layout_1' => esc_html__('Layout 1',  'graphina-pro-charts-for-elementor'),
                'layout_2' => esc_html__('Layout 2',  'graphina-pro-charts-for-elementor'),
                'layout_3' => esc_html__('Layout 3',  'graphina-pro-charts-for-elementor'),
                'layout_4' => esc_html__('Layout 4',  'graphina-pro-charts-for-elementor'),
                'layout_5' => esc_html__('Layout 5',  'graphina-pro-charts-for-elementor'),
                'layout_6' => esc_html__('Layout 6',  'graphina-pro-charts-for-elementor'),
            ];
            break;
        default :
            $options = [
                'csv' => esc_html__('CSV',  'graphina-pro-charts-for-elementor'),
                'remote-csv' => esc_html__('Remote CSV',  'graphina-pro-charts-for-elementor'),
                'google-sheet' => esc_html__('Google Sheet',  'graphina-pro-charts-for-elementor'),
                'database' => esc_html__('Database',  'graphina-pro-charts-for-elementor'),
                'api' => esc_html__('API',  'graphina-pro-charts-for-elementor'),
            ];
            if(isGraphinaPro()){
                $options['filter'] = esc_html__('Data From Filter', 'graphina-charts-for-elementor');
            }
            break;
    }
    if ($first) {
        return array_keys($options)[0];
    }
    if ($keys) {
        return array_keys($options);
    }
    return $options;
}

/*******************
 * @param bool $first
 * @return array|mixed
 */
function graphina_pro_element_database_data_from($first = false)
{
    $options = [
        'table' => esc_html__('Table',  'graphina-pro-charts-for-elementor'),
        'mysql-query' => esc_html__('MySQL Query',  'graphina-pro-charts-for-elementor')
    ];
    if(graphina_check_external_database('status')){
        $options['external_database'] =esc_html__('External',  'graphina-pro-charts-for-elementor');
    }
    if ($first) {
        return array_keys($options)[0];
    }
    return $options;
}

/*******************
 * @param $this_ele
 * @param string $type
 * @param null $jsonData
 */
function graphina_pro_element_data_option_setting($this_ele, $type = 'element_id', $jsonData = null)
{
    if ($type != 'counter') {
        $this_ele->start_controls_section(
            'iq_' . $type . '_section_data_options',
            [
                'label' => esc_html__('Data Options',  'graphina-pro-charts-for-elementor'),
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_element_data_json',
            [
                'label' => esc_html__('Data Json',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::HIDDEN,
                'label_block' => true,
                'default' => $jsonData
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_element_card_show',
            [
                'label' => esc_html__('Show Card',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('Show',  'graphina-pro-charts-for-elementor'),
                'default' => "yes",
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_element_data_option',
            [
                'label' => esc_html__('Type',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_element_data_enter_options('main_type', true),
                'options' => graphina_pro_element_data_enter_options('main_type',false,false,$type)
            ]
        );

        $this_ele->add_control(
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
    }

    $this_ele->add_control(
        'iq_' . $type . '_element_upload_csv',
        [
            'label' => esc_html__('Upload CSV File',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::MEDIA,
            'media_type' => 'text/csv',
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'csv'
            ]
        ]);

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_url',
        [
            'label' => esc_html__('URL',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => esc_html__('Remote File URL',  'graphina-pro-charts-for-elementor'),
            'label_block' => true,
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'remote-csv'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_google_sheet',
        [
            'label' => esc_html__('Enter Google Sheet Published URL',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => ['active' => true],
            'placeholder' => esc_html__('Google Sheet Published URL',  'graphina-pro-charts-for-elementor'),
            'label_block' => true,
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'google-sheet'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_database',
        [
            'label' => esc_html__('Select Mode',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => graphina_pro_element_database_data_from(true),
            'options' => graphina_pro_element_database_data_from(),
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'database'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_table',
        [
            'label' => esc_html__('Select Table',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => graphina_pro_list_db_tables(),
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'database',
                'iq_' . $type . '_element_import_from_database' => 'table'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $external_value=[];
    if(graphina_check_external_database('status')){
        $data = graphina_check_external_database('value');
        $external_option = array_keys($data);
        if(!empty($external_option) && is_array($external_option) && count($external_option) > 0){
            foreach ($external_option as $key => $value){
                $external_value[$value]= $value;
            }
        }
    }

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_external',
        [
            'label' => esc_html__('Select Database',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => $external_value,
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'database',
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
                        'iq_' . $type . '_element_data_option' => 'dynamic',
                        'iq_' . $type . '_element_dynamic_data_option' => 'database',
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
        'iq_' . $type . '_element_import_from_query',
        [
            'label' => esc_html__('Mysql Query',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'database',
                'iq_' . $type . '_element_import_from_database' => 'mysql-query'
            ],
            'description' => esc_html__('Fetch data from customize/raw query builder, Use Double quote( " " ) in condition value ',  'graphina-pro-charts-for-elementor'),
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_column_alias',
        [
            'label' => esc_html__('Select column or provide alias to column  Query',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'database',
                'iq_' . $type . '_element_import_from_database' => ['external_database','table']
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_where_conditions',
        [
            'label' => esc_html__('Where Condition',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'database',
                'iq_' . $type . '_element_import_from_database' => 'external_database'
            ],
            'description' => esc_html__('Where condition for selected table, Use Double quote( " " ) in condition value ',  'graphina-pro-charts-for-elementor'),
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_table_dynamic_key',
        [
            'label' => esc_html__('Dynamic Keys',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Use dynamic key in  Query,it will replace key will dynamic value (example : column_name={{CURRENT_USER_ID}} <strong><a href="https://wordpress.iqonic.design/docs/product/graphina-elementor-charts-and-graphs/use-dynamic-data-in-widgets/dynamic_key/" target="_blank">List of Dynamic key</a></strong>',  'graphina-pro-charts-for-elementor'),
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'database',
                'iq_' . $type . '_element_import_from_database' => ['mysql-query','external_database']
            ],
        ]
    );


    $this_ele->add_control(
        'iq_' . $type . '_element_download_csv_sample_doc',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                    <a style="text-decoration: underline; font-style: italic" href="' . GRAPHINA_PRO_URL . '/elementor/sample-doc/' . $type . '-sample.csv" download>here</a>
                                    to download sample CSV file.                                            
                                </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => ['csv', 'remote-csv']
            ]
        ]
    );

    $explodeValue = explode("_",$type);
    $uniqueID = !empty($explodeValue[1]) && $explodeValue[1] == 'google' ? 'g-'.$explodeValue[0].'-' : 'a-'.$type.'-';
    $id = rand(pow(10, 4-1), pow(10, 4)-1);
    $this_ele->add_control(
        'iq_' . $type . '_element_filter_widget_id',
        [
            'label' => esc_html__('Unquie Widget ID','graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => $uniqueID.$id,
            'label_block' => true,
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => ['filter']
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
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => ['filter']
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_download_google_sheet',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                    <a style="text-decoration: underline; font-style: italic" target="_blank" href="' . graphina_pro_get_element_sheet($type) . '">here</a>
                                    to view the sample format.                                           
                                </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_element_dynamic_data_option' => 'google-sheet'
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_import_from_api',
        [
            'label' => esc_html__('URL',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => esc_html__('URL',  'graphina-pro-charts-for-elementor'),
            'label_block' => true,
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => 'api'
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
                'iq_' . $type . '_element_dynamic_data_option' => 'api'
            ],
        ]
    );


    if($type == 'counter'){

        $this_ele->add_control(
            'iq_' . $type . '_authrization_token',
            [
                'label' => esc_html__('Enable Header Options',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
                'label_off' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
                'default' => false,
                'condition' => [
                    'iq_' . $type . '_element_dynamic_data_option' => 'api'
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
                    'iq_' . $type . '_element_dynamic_data_option' => 'api'
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
                    'iq_' . $type . '_element_dynamic_data_option' => 'api'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

    }

    $this_ele->add_control(
        'iq_element_download_sample_json',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                    <a style="text-decoration: underline; font-style: italic" href="' . GRAPHINA_PRO_URL . '/elementor/sample-json/' . $type . '-sample.json" download>here</a>
                                    to download sample JSON file.                                            
                                </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_element_dynamic_data_option' => 'api'
            ]
        ]
    );

    if ($type != 'counter') {

        $this_ele->add_control(
            'iq_' . $type . '_element_rows',
            [
                'label' => esc_html__('No of Rows',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_element_columns',
            [
                'label' => esc_html__('No of Columns',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1
            ]
        );

        if($type != 'data_table_lite'){
            $this_ele->add_control(
                'iq_' . $type . '_can_show_index',
                [
                    'label' => esc_html__('Show Index',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
                    'label_off' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
                    'default' => "yes",
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_index_title',
                [
                    'label' => esc_html__('Index Header',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Index Title',  'graphina-pro-charts-for-elementor'),
                    'default' => '#',
                    'condition' => [
                        'iq_' . $type . '_can_show_index' => 'yes'
                    ]
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_index_value_type',
                [
                    'label' => esc_html__('Index Value Type',  'graphina-pro-charts-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => graphina_pro_element_data_enter_options('index_type', true),
                    'options' => graphina_pro_element_data_enter_options('index_type'),
                    'condition' => [
                        'iq_' . $type . '_can_show_index' => 'yes'
                    ]
                ]
            );
        }
    }
    $this_ele->add_control(
        'iq_' . $type . '_can_use_cache_development',
        [
            'label' => esc_html__('Use Cache For Development',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
            'description' => esc_html__("It's create temporary cache of your file data and load from their for one hour in editor mode. It doesn't effect preview or live website",  'graphina-pro-charts-for-elementor'),
            'default' => false,
            'condition' => [
                'iq_' . $type . '_element_data_option' => 'dynamic',
                'iq_' . $type . '_element_dynamic_data_option' => ['remote-csv', 'google-sheet']
            ]
        ]
    );

    if ($type != 'counter') {
        $this_ele->end_controls_section();
    }
}

/********************
 * @param $this_ele
 * @param string $type
 */
function graphina_pro_table_header_section($this_ele, $type = "element_id")
{
    $this_ele->start_controls_section(
        'iq_' . $type . '_section_header',
        [
            'label' => esc_html__('Header',  'graphina-pro-charts-for-elementor'),
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_can_show_header',
        [
            'label' => esc_html__('Show',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
            'default' => "yes",
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_can_include_in_body',
        [
            'label' => esc_html__('Includes In Body',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
            'default' => "yes",
            'condition' => [
                'iq_' . $type . '_can_show_header!' => 'yes'
            ]
        ]
    );

    $this_ele->end_controls_section();
}

/**********************
 * @param $this_ele
 * @param string $type
 */
function graphina_pro_table_options_section($this_ele, $type = 'element_id')
{
    $this_ele->start_controls_section(
        'iq_' . $type . '_section_table_options',
        [
            'label' => esc_html__('Table Options',  'graphina-pro-charts-for-elementor'),
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_table_responsive',
        [
            'label' => esc_html__('Responsive',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('No',  'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('Yes',  'graphina-pro-charts-for-elementor'),
            'default' => "yes",
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_can_show_filter',
        [
            'label' => esc_html__('Search',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Hide',  'graphina-pro-charts-for-elementor'),
            'label_off' => esc_html__('Show',  'graphina-pro-charts-for-elementor'),
            'default' => "",
        ]
    );

    $this_ele->end_controls_section();
}

/***********************
 * @param $this_ele
 * @param string $type
 */
function graphina_pro_table_style_section($this_ele, $type = 'element_id')
{
    $this_ele->start_controls_section(
        'iq_' . $type . '_table_style_section',
        [
            'label' => esc_html__('Table Style',  'graphina-pro-charts-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'iq_' . $type . '_table_border',
            'label' => esc_html__('Border',  'graphina-pro-charts-for-elementor'),
            'fields_options' => [
                'border' => [
                    'default' => 'solid',
                ],
                'width' => [
                    'default' => [
                        'top' => '1',
                        'right' => '1',
                        'bottom' => '1',
                        'left' => '1',
                        'isLinked' => true,
                    ],
                ]
            ],
            'selector' => '{{WRAPPER}} .graphina-table-cell',
        ]
    );

    $this_ele->end_controls_section();
}

/*******************
 * @param object $this_ele
 * @param string $type
 */
function graphina_pro_element_style_section($this_ele, $type = 'chart_id')
{
    $this_ele->start_controls_section('iq_' . $type . '_card_style_section',
        [
            'label' => esc_html__('Card Style',  'graphina-pro-charts-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'iq_' . $type . '_element_card_show' => 'yes'
            ]
        ]
    );


    $this_ele->add_group_control(
        Group_Control_Background::get_type(),
        [
            'name' => 'iq_' . $type . '_card_background',
            'label' => esc_html__('Background',  'graphina-pro-charts-for-elementor'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .element-card',
            'condition' => [
                'iq_' . $type . '_element_card_show' => 'yes'
            ]
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Box_Shadow::get_type(),
        [
            'name' => 'iq_' . $type . '_card_box_shadow',
            'label' => esc_html__('Box Shadow',  'graphina-pro-charts-for-elementor'),
            'selector' => '{{WRAPPER}} .element-card',
            'condition' => ['iq_' . $type . '_element_card_show' => 'yes']
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'iq_' . $type . '_card_border',
            'label' => esc_html__('Border',  'graphina-pro-charts-for-elementor'),
            'selector' => '{{WRAPPER}} .element-card',
            'condition' => ['iq_' . $type . '_element_card_show' => 'yes']
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_card_border_radius',
        [
            'label' => esc_html__('Border Radius',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'condition' => [
                'iq_' . $type . '_element_card_show' => 'yes'
            ],
            'selectors' => [
                '{{WRAPPER}} .element-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
            ],
        ]
    );

    $this_ele->end_controls_section();
}

/****************************
 * @param $this_ele
 * @param string $type
 * @param bool $alignItems
 */
function graphina_pro_card_style_section($this_ele, $type = 'element_id', $alignItems = false)
{
    $this_ele->start_controls_section(
        'iq_' . $type . '_card_style_section',
        [
            'label' => esc_html__('Card Style',  'graphina-pro-charts-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_card_border_radius',
        [
            'label' => esc_html__('Border Radius',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .graphina-card.counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
            ],
        ]
    );

    if ($alignItems) {
        $this_ele->add_control(
            'iq_' . $type . '_element_vertical_alignment',
            [
                'label' => esc_html__('Alignment Items',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Top',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-caret-up',
                    ],
                    'center' => [
                        'title' => esc_html__('Middle',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-minus',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Bottom',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-caret-down',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .graphina-card.counter' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'iq_' . $type . '_element_layout_option' => graphina_pro_get_array_diff(graphina_pro_element_data_enter_options('counter_layout', false, true), ['layout_1'])
                ]
            ]
        );
    }

    $this_ele->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'iq_' . $type . '_card_border',
            'label' => esc_html__('Border',  'graphina-pro-charts-for-elementor'),
            'selector' => '{{WRAPPER}} .graphina-card.counter',
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Background::get_type(),
        [
            'name' => 'iq_' . $type . '_card_background',
            'label' => esc_html__('Background',  'graphina-pro-charts-for-elementor'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .graphina-card.counter',
        ]
    );

    $this_ele->end_controls_section();
}

/************************
 * @param $this_ele
 * @param string $type
 */
function graphina_pro_table_header_style_section($this_ele, $type = 'element_id')
{
    $this_ele->start_controls_section('iq_' . $type . '_header_style_section',
        [
            'label' => esc_html__('Header Style',  'graphina-pro-charts-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'iq_' . $type . '_header_typography',
            'label' => esc_html__('Typography',  'graphina-pro-charts-for-elementor'),
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .graphina-table-header',
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_header_font_color',
        [
            'label' => esc_html__('Font Color',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => [
                '{{WRAPPER}} .graphina-table-header' => 'color: {{VALUE}}',
                '{{WRAPPER}} .graphina-table-header input' => 'color: {{VALUE}}',
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_header_horizontal_alignment',
        [
            'label' => esc_html__('Text Alignment',  'graphina-pro-charts-for-elementor'),
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
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .graphina-table-header th' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .graphina-table-header th input' => 'text-align: {{VALUE}};',
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_header_padding',
        [
            'label' => esc_html__('Padding',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .graphina-table-header th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .graphina-table-header th input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Background::get_type(),
        [
            'name' => 'iq_' . $type . '_header_background',
            'label' => esc_html__('Background',  'graphina-pro-charts-for-elementor'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .graphina-table-header th',
        ]
    );

    $this_ele->end_controls_section();
}

/************************
 * @param $this_ele
 * @param string $type
 * @param string $for
 * @param string $class
 */
function graphina_pro_counter_style_section($this_ele, $type = 'element_id', $for = 'title', $class = '')
{
    $this_ele->start_controls_section('iq_' . $type . '_count_style_section_' . $for,
        [
            'label' => esc_html__(ucfirst($for) . ' Style',  'graphina-pro-charts-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE
        ]
    );

    $typography = Scheme_Typography::TYPOGRAPHY_4;
    switch ($for) {
        case 'counter':
            $typography = Scheme_Typography::TYPOGRAPHY_1;
            break;
        case 'title':
            $typography = Scheme_Typography::TYPOGRAPHY_2;
            break;
        case 'description':
            $typography = Scheme_Typography::TYPOGRAPHY_3;
            break;
    }
    $this_ele->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'iq_' . $type . '_count_' . $for . '_typography',
            'label' => esc_html__('Typography',  'graphina-pro-charts-for-elementor'),
            'scheme' => $typography,
            'selector' => '{{WRAPPER}} ' . $class,
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_count_' . $for . '_font_color',
        [
            'label' => esc_html__('Font Color',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => [
                '{{WRAPPER}} ' . $class => 'color: {{VALUE}}',
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_count_' . $for . '_horizontal_alignment',
        [
            'label' => esc_html__('Text Alignment',  'graphina-pro-charts-for-elementor'),
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
                '{{WRAPPER}} ' . $class => 'text-align: {{VALUE}};',
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_count_' . $for . '_margin',
        [
            'label' => esc_html__('Margin',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} ' . $class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_count_' . $for . '_padding',
        [
            'label' => esc_html__('Padding',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} ' . $class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
            ],
        ]
    );

    $this_ele->end_controls_section();
}

/*************************
 * @param $this_ele
 * @param string $type
 */
function graphina_pro_table_body_style_section($this_ele, $type = 'element_id')
{
    $this_ele->start_controls_section('iq_' . $type . '_body_style_section',
        [
            'label' => esc_html__('Body Style',  'graphina-pro-charts-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'iq_' . $type . '_body_typography',
            'label' => esc_html__('Typography',  'graphina-pro-charts-for-elementor'),
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .graphina-table-body tr td',
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_body_font_color',
        [
            'label' => esc_html__('Font Color',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => [
                '{{WRAPPER}} .graphina-table-body tr td' => 'color: {{VALUE}}',
                '{{WRAPPER}} .graphina-table-body tr td input' => 'color: {{VALUE}}',
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_body_horizontal_alignment',
        [
            'label' => esc_html__('Text Alignment',  'graphina-pro-charts-for-elementor'),
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
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .graphina-table-body tr td' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .graphina-table-body tr td input' => 'text-align: {{VALUE}};',
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_body_padding',
        [
            'label' => esc_html__('Padding',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .graphina-table-body tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .graphina-table-body tr td input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this_ele->start_controls_tabs('iq_' . $type . '_body_row_type_tabs');

    $this_ele->start_controls_tab('odd', ['label' => esc_html__('Odd Row',  'graphina-pro-charts-for-elementor')]);

    $this_ele->add_group_control(
        Group_Control_Background::get_type(),
        [
            'name' => 'iq_' . $type . '_body_odd_rows_background',
            'label' => esc_html__('Background',  'graphina-pro-charts-for-elementor'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .graphina-table-body tr:nth-child(odd) td',
        ]
    );

    $this_ele->end_controls_tab();

    $this_ele->start_controls_tab('even', ['label' => esc_html__('Even Row',  'graphina-pro-charts-for-elementor')]);

    $this_ele->add_group_control(
        Group_Control_Background::get_type(),
        [
            'name' => 'iq_' . $type . '_body_even_rows_background',
            'label' => esc_html__('Background',  'graphina-pro-charts-for-elementor'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .graphina-table-body tr:nth-child(even) td',
        ]
    );

    $this_ele->end_controls_tab();

    $this_ele->end_controls_tabs();

    $this_ele->end_controls_section();
}

/*************************
 * @param $this_ele
 * @param string $type
 */
function graphina_pro_table_filter_style_section($this_ele, $type = 'element_id')
{
    $this_ele->start_controls_section('iq_' . $type . '_filter_style_section',
        [
            'label' => esc_html__('Search Style',  'graphina-pro-charts-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'iq_' . $type . '_can_show_filter' => 'yes'
            ]
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'iq_' . $type . '_filter_typography',
            'label' => esc_html__('Typography',  'graphina-pro-charts-for-elementor'),
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .graphina-element input.table-filter',
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_filter_font_color',
        [
            'label' => esc_html__('Font Color',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => [
                '{{WRAPPER}} .graphina-element input.table-filter' => 'color: {{VALUE}}',
                '{{WRAPPER}} .graphina-element input.table-filter::placeholder' => 'color: {{VALUE}} !important;opacity:0.4',
                '{{WRAPPER}} .graphina-element input.table-filter:-ms-input-placeholder' => 'color: {{VALUE}} !important;opacity:0.4',
                '{{WRAPPER}} .graphina-element input.table-filter::-ms-input-placeholder' => 'color: {{VALUE}} !important;opacity:0.4',
            ],
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'iq_' . $type . '_filter_border',
            'label' => esc_html__('Border',  'graphina-pro-charts-for-elementor'),
            'fields_options' => [
                'border' => [
                    'default' => 'solid',
                ],
                'width' => [
                    'default' => [
                        'top' => '1',
                        'right' => '1',
                        'bottom' => '1',
                        'left' => '1',
                        'isLinked' => true,
                    ],
                ]
            ],
            'selector' => '{{WRAPPER}} .graphina-element input.table-filter',
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_filter_horizontal_alignment',
        [
            'label' => esc_html__('Text Alignment',  'graphina-pro-charts-for-elementor'),
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
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .graphina-element input.table-filter' => 'text-align: {{VALUE}};'
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_filter_margin',
        [
            'label' => esc_html__('Margin',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .graphina-element input.table-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_filter_padding',
        [
            'label' => esc_html__('Padding',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .graphina-element input.table-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
        ]
    );

    $this_ele->add_group_control(
        Group_Control_Background::get_type(),
        [
            'name' => 'iq_' . $type . '_filter_background',
            'label' => esc_html__('Background',  'graphina-pro-charts-for-elementor'),
            'types' => ['classic'],
            'selector' => '{{WRAPPER}} .graphina-element input.table-filter',
        ]
    );

    $this_ele->end_controls_section();
}

/********************
 * @param $this_ele
 * @param string $type
 * @param string $areaType
 * @return array|array[]
 */
function graphina_pro_element_parse_csv($this_ele, $type = 'element_id', $areaType = "table")
{
    $data = [];
    $body = [];
    $header = [];
    $settings = $this_ele->get_settings_for_display();

    switch ($type) {
        case 'advance-datatable' :
        case 'data_table_lite':
            $data = ['body' => $body, 'header' => $header];
            break;
    }
    if($type == 'data_table_lite'){
        $settings['iq_' . $type . '_element_upload_csv']['url'] = $settings['iq_' . $type . '_chart_upload_csv']['url'];
    }

    $response = wp_remote_get(
        $settings['iq_' . $type . '_element_upload_csv']['url'],
        [
            'sslverify' => false,
        ]
    );

    if ('' == $settings['iq_' . $type . '_element_upload_csv']['url'] || is_wp_error($response) || 200 != $response['response']['code'] || '.csv' !== substr($settings['iq_' . $type . '_element_upload_csv']['url'], -4)) {
        $data['error'] = $response;
        return $data;
    }

    $file = $settings['iq_' . $type . '_element_upload_csv']['url'];

    try {

        // Attempt to change permissions if not readable.
//    if (!is_readable($file)) {
//        chmod($file, 0744);
//    }
//
        // Check if file is writable, then open it in 'read only' mode.
//    if (is_readable($file)) {

//        $_file = fopen($file, 'r');

        $opts=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $_file = fopen($file, 'r', false, stream_context_create($opts));

        if (!$_file) {
            return ['body' => $body, 'header' => $header];
        }

        // To sum this part up, all it really does is go row by row.
        // Column by column, saving all the data.

        // Get first row in CSV, which is of course the headers.
        $header = fgetcsv($_file);
        $header = filter_var_array($header, FILTER_SANITIZE_STRING);
        $tempCounter = 0;

        switch ($areaType) {
            case "table" :
                while ($row = fgetcsv($_file)) {
                    $file_data = [];
                    if (count($row) > 0) {
                        $file_data = filter_var_array($row, FILTER_SANITIZE_STRING);
                    }
                    $body[] = $file_data;
                    $tempCounter++;
                }
                $data = ['body' => $body, 'header' => $header];
                break;
            case "counter" :
                foreach ($header as $index => $title) {
                    $data[] = [
                        'speed' => (int)$settings['iq_' . $type . '_element_counter_speed'],
                        'multi' => [],
                        'title' => $title, 'start' => 0, 'end' => 0
                    ];
                }
                while ($row = fgetcsv($_file)) {
                    if (count($row) > 0) {
                        foreach ($data as $index => $info) {
                            if (isset($row[$index]) && $row[$index] !== '') {
                                $data[$index]['end'] = (float)$row[$index];
                                $data[$index]['multi'][] = (float)$row[$index];
                            }
                        }
                    }
                }
                break;
//        }
                fclose($_file);

        }
        return $data;
    } catch (Exception $e) {
        switch ($type) {
            case 'advance-datatable' :
            case 'data_table_lite':
                return ['body' => $body, 'header' => $header];
                break;
            default :
                return [];
                break;
        }
    }
}

/*********************
 * @param string $url
 * @param string $areaType
 * @param string $type
 * @param null $this_ele
 * @return array|array[]
 */
function graphina_pro_element_data_remote_csv($url = '', $areaType = 'table', $type = '', $this_ele = null)
{
    $data = [];
    $body = [];
    $header = [];
    $settings = $this_ele->get_settings_for_display();
    switch ($areaType) {
        case 'table':
            $data = ["body" => $body, "header" => $header];
            break;
    }
    if ($url === '') {
        return $data;
    }
    $file = file_get_contents(html_entity_decode($url));
    $file = str_replace("\r", '', $file);
    $arr = explode("\n", $file);
    switch ($areaType) {
        case "table" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    if ($i !== 0) {
                        $v = [];
                        $val = explode(',', $a);
                        for ($j = 0; $j < count($val); $j++) {
                            $v[] = $val[$j];
                        }
                        $body[] = $v;
                    } else {
                        $val = explode(',', $a);
                        for ($j = 0; $j < count($val); $j++) {
                            $header[] = strval($val[$j]);
                        }
                    }
                }
            }
            $data = ["body" => $body, "header" => $header];
            break;
        case "counter":
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $val = explode(',', $a);
                    if ($i === 0) {
                        foreach ($val as $index => $v) {
                            $data[] = [
                                'speed' => (int)$settings['iq_' . $type . '_element_counter_speed'],
                                'multi' => [],
                                'title' => $v, 'start' => 0, 'end' => 0
                            ];
                        }
                    } else {
                        foreach ($data as $index => $v) {
                            if (isset($val[$index]) && $val[$index] !== '') {
                                $data[$index]['end'] = (float)$val[$index];
                                $data[$index]['multi'][] = (float)$val[$index];
                            }
                        }
                    }
                }
            }
            break;
    }
    return $data;
}

/**********************
 * description : get all table from current database
 * @return array
 */
function graphina_pro_list_db_tables($this_ele = null, $type = null)
{
    global $wpdb;

    $result = [];
    $tables = $wpdb->get_results('show tables', ARRAY_N);

    if ($tables) {
        $tables = wp_list_pluck($tables, 0);

        foreach ($tables as $table) {
            $result[$table] = $table;
        }
    }

    return $result;
}

/**********************
 * description : get all table from current database
 * @return array
 */
function graphina_pro_external_table_list($seleted_database)
{
    $result = [];
    $data = graphina_check_external_database('value');
    if(!empty($seleted_database) && array_key_exists($seleted_database,$data)){
        $seleted_database_value = $data[$seleted_database];
        $mydb = new wpdb( $seleted_database_value['user_name'],$seleted_database_value['pass'],$seleted_database_value['db_name'],$seleted_database_value['host']);
        $result = [];
        $tables = $mydb->get_results('show tables', ARRAY_N);

        if ($tables) {
            $tables = wp_list_pluck($tables, 0);

            foreach ($tables as $table) {
                $result[$table] = $table;
            }
        }
    }
    return $result;
}

/**********************
 * @param string $responseType
 * @param string $type
 * @param string $table
 * @param string $query
 * @return array
 */
function graphina_pro_element_get_data_from_database($responseType = '', $type = '', $table = '', $query = '',$where='',$limit='',$offset='',$settings=[])
{
    global $wpdb;
    $results = [];
    $data = [];
    $header = [];
    $body = [];

    switch ($responseType) {
        case 'advance-datatable':
        case 'data_table_lite':
            $data = ['header' => $header, 'body' => $body];
            break;
    }

    $column_alias = '*';
    if(!empty($settings['iq_' . $responseType . '_element_import_from_column_alias']) && trim($settings['iq_' . $responseType . '_element_import_from_column_alias']) != '' ){
        $column_alias = stripslashes(strip_tags(trim($settings['iq_' . $responseType . '_element_import_from_column_alias'])));
        $column_alias = ' '.$column_alias;
    }

    switch ($type) {
        case "table":
            if (!empty($table)) {
                $offset_condition = $limit_condtion = $where_condition = '';
                if($responseType == 'data_table_lite'){
                    if(!empty($offset) && $offset != ''){
                        $offset_condition = ' OFFSET '.$offset;
                    }
                    if(!empty($limit) && $limit != ''){
                        $limit_condtion = ' LIMIT '.$limit;
                    }
                    if(!empty($where) && $where != ''){
                        $where_condition = ' '.$where;
                    }
                }
                $results = $wpdb->get_results("SELECT {$column_alias} FROM " . $table . $where_condition . $limit_condtion . $offset_condition , ARRAY_A);
            }
            break;
        case "mysql-query":
        case 'sqlQuery':
            if (!empty($query)) {
                $query = graphina_replace_dynamic_key($responseType,'table',$settings,$query);
                $results = $wpdb->get_results($query, ARRAY_A);
            }
            break;
        case 'external_database':
            if(!empty($settings['iq_' . $responseType . '_element_import_from_external'])
                && $settings['iq_' . $responseType . '_element_import_from_external'] != ''
                && !empty($settings['iq_' . $responseType . '_element_import_from_external'])){
                $seleted_database = $settings['iq_' . $responseType . '_element_import_from_external'];
                if(graphina_check_external_database('status')){
                    $data = graphina_check_external_database('value');
                    if(array_key_exists($seleted_database,$data) && !empty($settings['iq_' . $responseType . '_element_import_from_table_'.$seleted_database])
                        && $settings['iq_' . $responseType . '_element_import_from_table_'.$seleted_database] != ''){
                        $seleted_database_value = $data[$seleted_database];
                        $mydb = new wpdb( $seleted_database_value['user_name'],$seleted_database_value['pass'],$seleted_database_value['db_name'],$seleted_database_value['host']);
                        $where_condition = ' ';
                        if(!empty($settings['iq_' . $responseType . '_element_import_from_where_conditions']) && $settings['iq_' . $responseType . '_element_import_from_where_conditions'] != '' ){
                            $where_condition = stripslashes(strip_tags(trim($settings['iq_' . $responseType . '_element_import_from_where_conditions'])));
                            $where_condition = ' '.$where_condition;
                            $where_condition = graphina_replace_dynamic_key($responseType,'table',$settings,$where_condition);
                        }
                        $results = $mydb->get_results("SELECT {$column_alias} FROM " . $settings['iq_' . $responseType . '_element_import_from_table_'.$seleted_database].$where_condition,ARRAY_A);
                    }
                }else{
                    return  $data;
                }
            }else{
                return  $data;
            }
            break;
    }
    switch ($responseType) {
        case 'advance-datatable':
        case 'data_table_lite':
            $header = count($results) > 0 ? array_keys($results[0]) : [];
            $body = [];
            foreach ($results as $tr) {
                $body[] = array_values($tr);
            }
            $data = ['header' => $header, 'body' => $body];
            break;
        case 'counter':
            $header = count($results) > 0 ? array_keys($results[0]) : [];
            foreach ($header as $index => $tr) {
                $vals = array_values(array_map(function ($val) use ($tr) {
                    return (float)$val[$tr];
                }, (array)$results));
                $data[] = [
                    'title' => $tr,
                    'end' => $vals[count($vals) - 1],
                    'multi' => $vals
                ];
            }
            break;
    }
    return $data;
}

/**********************
 * @param string $api_url
 * @param string $type
 * @param null $this_ele
 * @return array|array[]|mixed
 */

function graphina_pro_element_get_data_from_api($api_url = '', $type = '', $this_ele = null)
{
    $data = [];
    $settings = $this_ele->get_settings_for_display();
    switch ($type) {
        case 'advance-datatable' :
        case 'data_table_lite':
            $data = ['header' => [], 'body' => []];
            break;
    }

    if ($api_url === '') {
        return $data;
    }

    $api_url = graphina_replace_dynamic_key($type,'api',$settings,$api_url);

    if(in_array($type, ['counter','data_table_lite'])){

        $args = [];

        if(isset($settings['iq_'.$type.'_authrization_token'])
            && $settings['iq_'.$type.'_authrization_token'] == 'yes') {
            $args['headers'] = [];
            $args['headers'][trim($settings['iq_'.$type.'_header_key'])] = $settings['iq_'.$type.'_header_token'];
        }
        $response = wp_remote_get($api_url, $args);
    }
    else{
        $response = wp_remote_get($api_url);
    }
    if (is_array($response) && !is_wp_error($response)) {
        $res_body = $response['body']; // use the content
        $res_body = json_decode($res_body, true);
        if (isset($res_body['data']) && gettype($res_body['data']) === 'array') {
            switch ($type) {
                case 'advance-datatable' :
                case 'data_table_lite':
                    $keys = array_keys($res_body['data']);
                    if (in_array('thead', $keys)) {
                        $data['header'] = $res_body['data']['thead'];
                    }
                    if (in_array('tbody', $keys)) {
                        $data['body'] = $res_body['data']['tbody'];
                    }
                    break;
                case 'counter' :
                    $data = $res_body['data'];
                    foreach ($data as $i => $d) {
                        $data[$i]['speed'] = (int)$settings['iq_' . $type . '_element_counter_speed'];
                    }
                    break;
            }
        }
    }
    return $data;
}

function graphina_pro_integer_to_roman($integer)
{
    // Convert the integer into an integer (just to make sure)
    $integer = intval($integer);
    $result = '';

    // Create a lookup array that contains all of the Roman numerals.
    $lookup = array('M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1);

    foreach ($lookup as $roman => $value) {
        // Determine the number of matches
        $matches = intval($integer / $value);

        // Add the same number of characters to the string
        $result .= str_repeat($roman, $matches);

        // Set the integer to be the remainder of the integer and the value
        $integer = $integer % $value;
    }

    // The Roman numeral should be built, return it
    return $result;
}

/**********************
 * @param $array1
 * @param $array2
 * @return array
 */
function graphina_pro_get_array_diff($array1, $array2)
{
    return array_values(array_diff($array1, $array2));
}

function graphina_pro_get_element_sheet($type = '')
{

    $sheet = '#';
    switch ($type) {

        case 'counter';
            $sheet = 'https://docs.google.com/spreadsheets/d/1ZEtWaHVocV3O2G2CO1iHK38vKcAe1sJjEDj_WUdINIg/edit?usp=sharing';
            break;
        case 'advance-datatable';
        case 'data_table_lite':
            $sheet = 'https://docs.google.com/spreadsheets/d/1NPZwZXIoG0Cgl8mtnvV8U6MqjuJ8_VFplNm3qnj_Wyo/edit?usp=sharing';
            break;
    }

    return $sheet;
}

function graphina_pro_password_style_section($this_ele=null, $type='')
{

    //print_r($this_ele->get_settings_for_display());

    graphina_pro_password_style($this_ele, $type ,'Heading' ,'graphina-password-heading');

    graphina_pro_password_style($this_ele, $type ,'Subheading' ,'graphina-password-message');

    graphina_pro_password_style($this_ele, $type ,'Input' ,'graphina-input');

    graphina_pro_password_style($this_ele, $type ,'Button' ,'graphina-button');

    graphina_pro_password_style($this_ele, $type, 'Error', 'graphina-error');

}


/************************
 * @param $this_ele
 * @param string $type
 * @param string $for
 * @param string $class
 */
function graphina_pro_password_style($this_ele, $type = 'chart_id' , $for='title', $class='')
{
   if($for === 'Error'){
       $this_ele->start_controls_section('iq_' . $type . '_password_form_' .$for. '_style_section_',
           [
               'label' => esc_html__('Password form '.$for,  'graphina-pro-charts-for-elementor'),
               'tab' => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'iq_' . $type . '_restriction_content_type' => 'password',
                   'iq_' . $type . '_password_error_message_show' => 'yes'
               ]
           ]
       );
   }
   else {
       $this_ele->start_controls_section('iq_' . $type . '_password_form_' . $for . '_style_section_',
           [
               'label' => esc_html__('Password form ' . $for,  'graphina-pro-charts-for-elementor'),
               'tab' => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'iq_' . $type . '_restriction_content_type' => 'password'
               ]
           ]
       );
   }
    /**
     * switch case typography
     */
    $typography = Scheme_Typography::TYPOGRAPHY_1;
    switch ($for) {
        case 'Heading':
            $size=$class;
            $align='graphina-password-heading';
            $typography = Scheme_Typography::TYPOGRAPHY_1;
            break;
        case 'Subheading':
            $size=$class;
            $align='graphina-password-message';
            $typography = Scheme_Typography::TYPOGRAPHY_2;
            break;
        case 'Input':
            $size=$class;
            $align='graphina-input-wrapper';
            $typography = Scheme_Typography::TYPOGRAPHY_3;
            break;
        case 'Button':
            $size='graphina-button';
            $class='graphina-button';
            $align='button-box';
            $typography = Scheme_Typography::TYPOGRAPHY_4;
            break;
        case 'Error':
            $size=$class;
            $align='graphina-error';
            break;
    }

    /**
     * Password form  settings.
     */
    $this_ele->add_control(
        'iq_' . $type . '_password_form_' .$for. '_options',
        [
            'label' => esc_html__($for,  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::HEADING,
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password'],
        ]
    );

    if(in_array($for,['Input','Button','Error'])) {
        $this_ele->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'iq_' . $type . '_password_form_' .$for. '_background',
                'label' => esc_html__('Background',  'graphina-pro-charts-for-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .' . $class,
                'condition' => [
                    'iq_' . $type . '_restriction_content_type' => 'password'
                ]
            ]
        );
    }
    /**
     * Password  typography.
     */
    $this_ele->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'iq_' . $type . '_password_form_' .$for. '_typography',
            'label' => esc_html__('Typography',  'graphina-pro-charts-for-elementor'),
            'scheme' => $typography,
            'selector' => '{{WRAPPER}} .' .$class,
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password']
        ]
    );

    /**
     * Password form alignment.
     */
    if($for === 'Error') {
        $this_ele->add_control(
            'iq_' . $type . '_password_form_' . $for . '_align' . $for,
            [
                'label' => esc_html__('Alignment',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                //'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right',  'graphina-pro-charts-for-elementor'),
                        'icon' => 'fa fa-align-right',
                    ]
                ],
                'condition' => [
                    'iq_' . $type . '_restriction_content_type' => 'password'
                ],
                'selectors' => [
                    '{{WRAPPER}} .' . $align => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .graphina-error-div' => 'justify-content: {{VALUE}};',

                ],
            ]
        );
    }else{
        $this_ele->add_control(
            'iq_' . $type . '_password_form_' . $for . '_align' . $for,
            [
                'label' => esc_html__('Alignment',  'graphina-pro-charts-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                //'default' => 'left',
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
                    ]
                ],
                'condition' => [
                    'iq_' . $type . '_restriction_content_type' => 'password'
                ],
                'selectors' => [
                    '{{WRAPPER}} .' . $align => 'text-align: {{VALUE}};',

                ],
            ]
        );
    }


    /**
     * Password form  font color.
     */
    $this_ele->add_control(
        'iq_' . $type . '_password_form_' .$for. '_font_color'.$for,
        [
            'label' => esc_html__('Font Color',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::COLOR,
            //'default' => '#000000',
            'selectors' => [
                '{{WRAPPER}} .'.$class => 'color: {{VALUE}};',
                '{{WRAPPER}} .'.$class.'::placeholder' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'iq_' . $type . '_restriction_content_type' => 'password'
            ],
        ]
    );

    /**
     * Password form  margin.
     */
    $this_ele->add_control(
        'iq_' . $type . '_password_form_' .$for. '_margin',
        [
            'label' => esc_html__('Margin',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'condition' => [
                'iq_' . $type . '_restriction_content_type' => 'password'
            ],
            'selectors' => [
                '{{WRAPPER}} .'.$size => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    /**
     * Password form padding.
     */
    $this_ele->add_control(
        'iq_' . $type . '_password_form_' .$for. '_padding',
        [
            'label' => esc_html__('Padding',  'graphina-pro-charts-for-elementor'),
            'size_units' => ['px', '%', 'em'],
            'type' => Controls_Manager::DIMENSIONS,
            'condition' => [
                'iq_' . $type . '_restriction_content_type' => 'password'
            ],
            'selectors' => [
                '{{WRAPPER}} .'.$size => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    if(in_array($for,['Input','Button','Error'])) {
        $this_ele->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'iq_' . $type . '_password_form_' .$for. '_border_' ,
                'label' => esc_html__('Border',  'graphina-pro-charts-for-elementor'),
                'selector' => '{{WRAPPER}} .' . $size,
                'iq_' . $type . '_restriction_content_type' => 'password',
            ]
        );

        /**
         * Password form Input border radius
         */
        $this_ele->add_control(
            'iq_' . $type . '_password_form_' .$for. '_radius',
            [
                'label' => esc_html__('Border Radius',  'graphina-pro-charts-for-elementor'),
                'size_units' => ['px', '%', 'em'],
                'type' => Controls_Manager::DIMENSIONS,
                'iq_' . $type . '_restriction_content_type' => 'password',
                'selectors' => [
                    '{{WRAPPER}} .' . $class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
                    '{{WRAPPER}} .' . $size => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
                ],
            ]
        );

        $this_ele->add_responsive_control(
            'iq_' . $type . '_password_form_' .$for. '_width',
            [
                'label' => __( 'Width', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .'.$size => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this_ele->add_responsive_control(
            'iq_' . $type . '_password_form_' .$for. '_space',
            [
                'label' => __( 'Max Width', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .'.$size => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this_ele->add_responsive_control(
            'iq_' . $type . '_password_form_' .$for. '_height',
            [
                'label' => __( 'Height', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .'.$size => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

   }
    if(in_array($for,['Button'])) {
        /**
         * //     * Password form Button hover notice descriptions
         * //     */
    $this_ele->add_control(
        'iq_' . $type . '_password_form_button_hover_notice',
        [
            'label' => esc_html__('For hover in Button keep background type classic of button',  'graphina-pro-charts-for-elementor'),
            'type' => Controls_Manager::HEADING,
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password'],
        ]
    );

    /**
     * Password form Button hover
     */
    $this_ele->start_controls_tabs( 'iq_' . $type . 'tabs_button_style' );

    $this_ele->start_controls_tab(
        'iq_' . $type . '_password_form_button_normal',
        [
            'label' => __( 'Normal',  'graphina-pro-charts-for-elementor' ),
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_password_form_button_normal_color',
        [
            'label' => __( 'Text Color',  'graphina-pro-charts-for-elementor' ),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .'.$class => 'fill: {{VALUE}}; color: {{VALUE}};',
            ],
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password'],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_password_form_button_normal_background_color',
        [
            'label' => __( 'Background Color',  'graphina-pro-charts-for-elementor' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .'.$class   => 'background-color: {{VALUE}};',
            ],
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password'],
        ]
    );

    $this_ele->end_controls_tab();

    $this_ele->start_controls_tab(
        'iq_' . $type . '_password_form_button_hover',
        [
            'label' => __( 'Hover',  'graphina-pro-charts-for-elementor' ),
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_password_form_hover_color',
        [
            'label' => __( 'Text Color',  'graphina-pro-charts-for-elementor' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.graphina-button:hover, {{WRAPPER}} .graphina-button:focus' => 'color: {{VALUE}};',
                '{{WRAPPER}} .graphina-button:hover svg, {{WRAPPER}} .graphina-button:focus svg' => 'fill: {{VALUE}};',
            ],
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password'],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_password_form_button_background_hover_color',
        [
            'label' => __( 'Background Color',  'graphina-pro-charts-for-elementor' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .graphina-button:hover, {{WRAPPER}} .graphina-button:focus' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password'],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_password_form_button_hover_border_color',
        [
            'label' => __( 'Border Color',  'graphina-pro-charts-for-elementor' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .graphina-button:hover, {{WRAPPER}} .graphina-button:hover' => 'border-color: {{VALUE}};',
            ],
            'condition' => ['iq_' . $type . '_restriction_content_type' => 'password'],
        ]
    );

    }
    $this_ele->end_controls_section();

}