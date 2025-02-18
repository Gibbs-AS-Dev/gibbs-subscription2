<?php

namespace Elementor;
if (!defined('ABSPATH')) exit;


/**
 * Graphina AdvanceDataTable widget.
 *
 * Graphina widget that displays an eye-catching Table.
 *
 * @since 1.2.4
 */
class AdvanceDataTable extends Widget_Base
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
        return 'advance-datatable';
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
        return 'Advance DataTable';
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
        return 'graphina-apex-datatable-chart';
    }

    protected function register_controls()
    {
        $type = $this->get_name();
        $jsonData = json_encode([
            'header' => array_fill(0, 5, ''),
            'body' => array_fill(0, 5, array_fill(0, 5, ''))
        ]);
        graphina_pro_element_data_option_setting($this, $type, $jsonData);

        do_action('graphina_forminator_addon_control_section', $this, $type);

        do_action('graphina_addons_control_section', $this, $type);

        graphina_restriction_content_options($this, $type);

        graphina_pro_table_header_section($this, $type);

        graphina_pro_table_options_section($this, $type);

        graphina_pro_element_style_section($this, $type);

        graphina_pro_table_style_section($this, $type);

        graphina_pro_table_header_style_section($this, $type);

        graphina_pro_table_body_style_section($this, $type);

        graphina_pro_table_filter_style_section($this, $type);

        graphina_pro_password_style_section($this, $type );

    }

    protected function render()
    {
        $type = $this->get_name();
        $settings = $this->get_settings_for_display();
        $data = ['body' => [], 'header' => []];
        $tableOptions = [
            'search' => $settings['iq_' . $type . '_can_show_filter'] === 'yes',
            'row' => (int)$settings['iq_' . $type . '_element_rows'],
            'columns' => (int)$settings['iq_' . $type . '_element_columns'],
            'index' => $settings['iq_' . $type . '_can_show_index'] === 'yes'
        ];

        $dataOption = $settings['iq_' . $type . '_element_data_option'] === 'manual' ? 'manual' : $settings['iq_' . $type . '_element_dynamic_data_option'];
        switch ($dataOption) {
            case "manual":
                $data = json_decode($settings['iq_' . $type . '_element_data_json'], true);
                break;
            case "csv":
                $data = graphina_pro_element_parse_csv($this, $type, 'table');
                break;
            case "remote-csv":
                $data = graphina_pro_element_data_remote_csv($settings['iq_' . $type . '_element_import_from_url'], 'table', $type, $this);
                break;
            case "google-sheet":
                $data = graphina_pro_element_data_remote_csv($settings['iq_' . $type . '_element_import_from_google_sheet'], 'table', $type, $this);
                break;
            case "database":
                $data = graphina_pro_element_get_data_from_database($type, $settings['iq_' . $type . '_element_import_from_database'], $settings['iq_' . $type . '_element_import_from_table'], $settings['iq_' . $type . '_element_import_from_query'],'','','',$settings);
                break;
            case "api":
                $data = graphina_pro_element_get_data_from_api($settings['iq_' . $type . '_element_import_from_api'], $type, $this);
                break;
            case 'filter':
                update_post_meta(get_the_ID(),$this->get_id(),$settings['iq_' . $type . '_element_filter_widget_id']);
                $data = apply_filters('graphina_extra_data_option', $data, $type, $settings,$settings['iq_' . $type . '_element_filter_widget_id']);
                break;
        }

        if($settings['iq_' . $type . '_element_data_option'] === 'firebase'){
            $data = apply_filters('graphina_addons_render_section', $data, $type, $settings);
        }

        if ($settings['iq_' . $type . '_element_data_option'] === 'forminator' && graphinaForminatorAddonActive()) {
            $data = apply_filters('graphina_forminator_addon_data', $data, $type, $settings);
        }

        if (isset($data['fail']) && $data['fail'] === 'permission') {
            switch ($dataOption) {
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

        /**********************
         * Column Adjustment
         */
        $desiredColumns = $tableOptions['columns'];
        if ($settings['iq_' . $type . '_element_data_option'] === 'manual' && count($data['header']) > 0) {
            $empty_array = array_fill(0, $desiredColumns, '');
            while (count($data['header']) <= $desiredColumns) {
                $data['header'] = array_merge($data['header'], $empty_array);
                $body = [];
                foreach ($data['body'] as $b) {
                    $body[] = array_merge($b, $empty_array);
                }
                $data['body'] = $body;
            }
            $data['header'] = array_slice($data['header'], 0, $desiredColumns);
            $body = [];
            foreach ($data['body'] as $b) {
                $body[] = array_slice($b, 0, $desiredColumns);
            }
            $data['body'] = $body;
        }

        /*************************
         * Row Adjustment
         */
        if ($settings['iq_' . $type . '_element_data_option'] === 'manual' && count($data['body']) > 0) {
            $desiredRow = $tableOptions['row'];
            $empty_array = array_fill(0, $desiredRow, array_fill(0, $desiredColumns, ''));
            while (count($data['body']) <= $desiredRow) {
                $data['body'] = array_merge($data['body'], $empty_array);
            }
            $data['body'] = array_slice($data['body'], 0, $desiredRow);
        }
        /***********************
         * Column Row Adjustment Finish
         */

        if ($settings['iq_' . $type . '_can_show_index'] === 'yes' && !(\Elementor\Plugin::$instance->editor->is_edit_mode())) {
            $tableOptions['columns']++;
            $data['header'] = array_merge([strip_tags($settings['iq_' . $type . '_index_title'])], $data['header']);
            foreach ($data['body'] as $index => $body) {
                switch ($settings['iq_' . $type . '_index_value_type']) {
                    case 'roman' :
                        $val = graphina_pro_integer_to_roman($index + 1);
                        break;
                    default:
                        $val = $index + 1;
                        break;
                }

                $data['body'][$index] = array_merge([$val], $body);
            }
        }

        if ($settings['iq_' . $type . '_can_include_in_body'] === 'yes') {
            $tableOptions['row']++;
            $data['body'] = array_merge([$data['header']], $data['body']);
            $data['header'] = [];
        }
        if ($settings['iq_' . $type . '_can_show_header'] !== 'yes') {
            $data['header'] = [];
        }
        $dataJson = json_encode($data);
        $tableOptions = json_encode($tableOptions);
        require GRAPHINA_PRO_ROOT . '/elementor/tables/advance-datatable/render/advance-datatable.php';
        if( isRestrictedAccess('advance-datatable',$this->get_id(),$settings,false) === false)
        {
        ?>
        <script>
            var dataMain = <?php echo $dataJson ?>;
            var tableOptions = <?php echo $tableOptions ?>;
            var myElement = document.querySelector('.graphina-table-<?php esc_attr_e($this->get_id()); ?>');
            if (tableOptions['search'] === true) {
                document.querySelector('#table-filter-<?php esc_attr_e($this->get_id()); ?>').addEventListener('keyup', function () {
                    let data = {
                        header: dataMain.header,
                        body: []
                    };
                    let filterText = this.value;
                    data.body = dataMain.body.filter(function (res) {
                        return res.filter(function (val) {
                            return val.toString().toLowerCase().includes(filterText.toString().toLowerCase());
                        }).length > 0;
                    });
                    setTableData(data);
                });
            }

            function setTableData(data) {
                var mainElement = document.querySelector('.graphina-table-<?php esc_attr_e($this->get_id()); ?>');
                if(mainElement) {
                    mainElement.innerHTML = '';
                    var tableElement = document.createElement('table');
                    tableElement.classList.add('graphina-table-base', 'table-bordered', 'table-padding-left', 'datatable-<?php esc_attr_e($this->get_id()); ?>');
                    if (data.header.length > 0) {
                        var headerElement = document.createElement('thead');
                        headerElement.classList.add('graphina-table-header');
                        for (let i = 0; i < tableOptions.columns; i++) {
                            let thElement = document.createElement('th');
                            thElement.classList.add('graphina-table-cell');
                            <?php if( \Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['iq_' . $type . '_element_data_option'] === 'manual' ) { ?>
                            let inputEle = document.createElement('input');
                            inputEle.type = 'text';
                            inputEle.setAttribute('placeholder', 'Header ' + (i + 1));
                            inputEle.value = data.header[i] ? data.header[i] : '';
                            thElement.append(inputEle);
                            <?php } else { ?>
                            thElement.innerHTML = data.header[i];
                            <?php } ?>

                            headerElement.append(thElement);
                        }
                        tableElement.append(headerElement);
                    }
                    if (data.body.length > 0) {
                        var bodyElement = document.createElement('tbody');
                        bodyElement.classList.add('graphina-table-body');
                        let count = 1;
                        for (let i = 0; i < tableOptions.row; i++) {
                            let tr = data.body[i] ? data.body[i] : [];
                            if (tr.length > 0) {
                                let trElement = document.createElement('tr');
                                for (let j = 0; j < tableOptions.columns; j++) {
                                    let tdElement = document.createElement('td');
                                    tdElement.classList.add('graphina-table-cell');
                                    <?php if( \Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['iq_' . $type . '_element_data_option'] === 'manual' ) { ?>
                                    let inputEle = document.createElement('input');
                                    inputEle.type = 'text';
                                    inputEle.setAttribute('placeholder', 'Value ' + count);
                                    count++;
                                    inputEle.value = tr[j] ? tr[j] : '';
                                    tdElement.append(inputEle);
                                    <?php } else { ?>
                                    tdElement.innerHTML = tr[j];
                                    <?php } ?>
                                    trElement.append(tdElement);
                                }
                                bodyElement.append(trElement);
                            }
                        }
                        tableElement.append(bodyElement);
                    } else {
                        var bodyElement = document.createElement('tbody');
                        bodyElement.classList.add('graphina-table-body');
                        let trElement = document.createElement('tr');
                        let tdElement = document.createElement('td');
                        tdElement.setAttribute('colspan', tableOptions.columns);
                        tdElement.classList.add('graphina-table-no-data');
                        tdElement.classList.add('graphina-table-cell');
                        tdElement.innerText = '<?php esc_attr_e("No Data Available") ?>';
                        trElement.append(tdElement);
                        bodyElement.append(trElement);
                        tableElement.append(bodyElement);
                    }
                    if (data.header.length === 0) {
                        if(data.body.length === 0){
                            let spanEle = document.createElement('span');
                            spanEle.classList.add('graphina-no-data');
                            spanEle.innerText = '<?php esc_attr_e("No Data Available") ?>'
                            mainElement.append(spanEle);
                        }
                    } else {
                        mainElement.append(tableElement);
                    }
                }
            }

            setTableData(dataMain);

            <?php if( \Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['iq_' . $type . '_element_data_option'] === 'manual' ) { ?>

            function getJson(tableParent) {
                let header = [];
                let body = [];
                let divEle = document.querySelector(tableParent + ' table');

                let thEle = divEle.getElementsByTagName('th');
                if (thEle.length > 0) {
                    let th = Object.keys(thEle);
                    th.forEach((item, index) => {
                        let value = thEle[index].getElementsByTagName('input')[0].value;
                        header.push(value);
                    });
                }
                let trEle = divEle.getElementsByTagName('tr');
                if (trEle.length > 0) {
                    let tr = Object.keys(trEle);
                    tr.forEach((item, index) => {
                        let tdData = [];
                        let tdEle = trEle[index].getElementsByTagName('td');
                        if (tdEle.length > 0) {
                            let td = Object.keys(tdEle);
                            td.forEach((item, index) => {
                                let value = tdEle[index].getElementsByTagName('input')[0].value;
                                tdData.push(value);
                            });
                        }
                        body.push(tdData);
                    });
                }

                return JSON.stringify({'header': header, 'body': body});
            }

            if (document.querySelector('.graphina-table-<?php esc_attr_e($this->get_id()); ?> table')) {
                document.querySelector('.graphina-table-<?php esc_attr_e($this->get_id()); ?> table').addEventListener("change", function () {
                    let info = getJson('.graphina-table-<?php esc_attr_e($this->get_id()); ?>');
                    let jsonDataElement = parent.document.querySelector('input[data-setting="iq_advance-datatable_element_data_json"]');
                    if(jsonDataElement) {
                        jsonDataElement.value = info;
                        let event = document.createEvent("HTMLEvents");
                        event.initEvent("input", true, true);
                        event.eventName = "input";
                        jsonDataElement.dispatchEvent(event);
                    }
                });
            }

            <?php } ?>

        </script>
        <?php
        }
    }
}

Plugin::instance()->widgets_manager->register(new AdvanceDataTable());