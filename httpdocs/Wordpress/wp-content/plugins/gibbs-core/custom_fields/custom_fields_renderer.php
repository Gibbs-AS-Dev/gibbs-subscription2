<?php

class CustomFieldsRenderer
{
    // Return an array that holds custom field data sets based on the given custom field data string. The string may be
    // serialised. If the custom field data does not contain data, the resulting array will be empty.
    public static function get_custom_field_data_sets($custom_field_data)
    {
        if (isset($custom_field_data) && $custom_field_data !== "")
        {
            return maybe_unserialize($custom_field_data);
        }
        return [];
    }

    // If the given field name label lookup table holds a display name for the given data item key, return the display
    // name. Otherwise, pretty up the key so it can be displayed.
    public static function get_field_name_label($data_item_key, $field_name_labels)
    {
        if (isset($field_name_labels[$data_item_key]))
        {
            return $field_name_labels[$data_item_key];
        }
        $data_item_key = str_replace("_", " ", $data_item_key);
        $data_item_key = str_replace("-", " ", $data_item_key);
        return $data_item_key;
    }

    // Return a number of table rows to display the given custom field data sets as HTML. Each row will hold all the
    // data in one data set. Each data set is expected to have a number of data item key and value pairs.
    // $field_name_labels is a lookup table that holds the display names of each data item key.
    public static function get_data_sets_as_html_table_rows($custom_field_data_sets, $field_name_labels)
    {
        $result = '';
        foreach ($custom_field_data_sets as $data_set)
        {
            $result .= '<tr>';
            foreach ($data_set as $data_item_key => $data_item_value)
            {
                $result .= '<td>' . CustomFieldsRenderer::get_field_name_label($data_item_key, $field_name_labels) .
                    ':</td><td><div class="outer-table-data"><span>' . $data_item_value . '</span></div></td>';
            }
            $result .= '</tr>';
        }
        return $result;
    }

    // Return the given custom field data sets as CSV. Each data set is expected to have a number of data item key and
    // value pairs. A single table cell will hold all the data, with each key value pair in the format "<key>: <value>".
    // Each data set will end with a pipe ("|") character. $field_name_labels is a lookup table that holds the display
    // names of each data item key.
    public static function get_data_sets_as_csv($custom_field_data_sets, $field_name_labels)
    {
        $result = '';
        foreach ($custom_field_data_sets as $data_set)
        {
            foreach ($data_set as $data_item_key => $data_item_value)
            {
                $result .= CustomFieldsRenderer::get_field_name_label($data_item_key, $field_name_labels) . ': ' .
                    $data_item_value . '; ';
            }
            $result .= '| ';
        }
        return $result;
    }
}

?>