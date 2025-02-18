function setFieldsForCSVpro(request_fields, response, type) {



    let element_x = parent.document.querySelector('[data-setting="iq_' + type + '_chart_csv_x_columns"]');
    console.log(element_x);

    let element_y = parent.document.querySelector('[data-setting="iq_' + type + '_chart_csv_y_columns"]');

    if (element_x == null || element_y == null) {

        return;
    }

    let manualCsv = ['mixed', 'brush', 'nested_column', 'pie_google', 'donut_google', 'line_google', 'area_google', 'bar_google', 'column_google', 'gauge_google', 'gantt_google', 'geo_google', 'org_google'];

    console.log(manualCsv.includes(type));

    let csv_columns = manualCsv.includes(type) ? response.column : response.extra.column;
    x_option_tag = '';
    y_option_tag = '';

    console.log('x_option_tag');
    console.log(x_option_tag);
    console.log('y_option_tag');
    console.log(y_option_tag);


    if (csv_columns !== undefined && csv_columns.length !== undefined && csv_columns.length > 0) {

        csv_columns.forEach(function(currentValue, index, arr) {

            x_axis_selected_field = '';
            y_axis_selected_field = '';
            if (request_fields['iq_' + type + '_chart_csv_x_columns'].includes(currentValue)) {
                x_axis_selected_field = 'selected';
            }
            if (request_fields['iq_' + type + '_chart_csv_y_columns'].includes(currentValue)) {
                y_axis_selected_field = 'selected';
            }
            x_option_tag += '<option value="' + currentValue.toLowerCase() + '" ' + x_axis_selected_field + ' > ' + currentValue + '</option>';

            y_option_tag += '<option value="' + currentValue.toLowerCase() + '" ' + y_axis_selected_field + ' > ' + currentValue + '</option>';
        });
    }


    element_x.innerHTML = x_option_tag;
    element_y.innerHTML = y_option_tag;

}