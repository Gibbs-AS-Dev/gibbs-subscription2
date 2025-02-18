<?php
$filter_template_table = "filter_template";
$filter_template_sql = "SELECT * from $filter_template_table where template_type = 'calender' AND user_id=".$admin_user_id;
$filter_template_data = $wpdb->get_results($filter_template_sql);

if(empty($filter_template_data)){
    Gibbs_Admin_Calendar_Setup::saveCalenderFilter($admin_user_id);
    $filter_template_data = $wpdb->get_results($filter_template_sql);
}

$filter_template_type = "calender";
$template_action = "save_calender_filter_template_mobiscroll";

$template_selected =  get_user_meta($admin_user_id,"template_selected",true);

require_once(get_stylesheet_directory()."/filter-template/modal/template_create_modal.php"); 
require_once(get_stylesheet_directory()."/filter-template/modal/template_modal.php"); 

$template_action = "update_calender_filter_template_mobiscroll";
require_once(get_stylesheet_directory()."/filter-template/modal/edit_template_modal.php"); 

 

?>
