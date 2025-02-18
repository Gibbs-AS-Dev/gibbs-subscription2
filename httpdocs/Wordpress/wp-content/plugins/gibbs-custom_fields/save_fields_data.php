<?php
function fields_create_action()
{

	global $wpdb;

	$get_app_fields = get_app_fields($_POST["active_group_id"]);
	$field_group_exist = field_group_exist($_POST["active_group_id"]);

	$fields = $get_app_fields;

	if ($fields == "") {
		$fields = array();
	}



	$_POST["tooltip"] = str_replace('\"', '"', $_POST["tooltip"]);
	$_POST["tooltip"] = str_replace('/"', '"', $_POST["tooltip"]);



	$new_fields = array();

	$new_fields["label"] = $_POST["field_label"];
	$new_fields["type"] = $_POST["field_type"];
	$new_fields["name"] = $_POST["field_name"];
	$new_fields["max_input_number"] = $_POST["max_input_number"];
	$new_fields["status"] = 1;
	$new_fields["required"] = (int) $_POST["field_required"];
	$new_fields["tooltip"] = $_POST["tooltip"];
	$new_fields["parent_field"] = $_POST["parent_field"];
	$new_fields["field_options"] = $_POST["field_options"];
	$new_fields["type_select"] = $_POST["type_select"];
	$new_fields["field_width"] = $_POST["field_width"];
	$new_fields["multiple"] = (int) $_POST["select_multiple"];
	$new_fields["show_in_booking_summery"] = (int) $_POST["show_in_booking_summery"];
	$new_fields["show_in_calender"] = (int) $_POST["show_in_calender"];
	$new_fields["param_algo"] = (int) $_POST["param_algo"];
	$new_fields["field_position"] =  $_POST["field_position"];
	$new_fields["listings"] =  array();


	if ($_POST["parent_field"] && $_POST["parent_field"] != "") {

		$get_parent_field_data = get_parent_field_data($_POST["parent_field"], $_POST["active_group_id"]);
		if (!empty($get_parent_field_data)) {
			$new_fields["show_in_booking_summery"] = (int) $get_parent_field_data["show_in_booking_summery"];
			$new_fields["show_in_calender"] = (int) $get_parent_field_data["show_in_calender"];
			$new_fields["field_position"] =  $get_parent_field_data["field_position"];
		}
	}




	if ($field_group_exist == false) {

		$fields[] = $new_fields;

		$fields_data = maybe_serialize($fields);

		$application_fields = $wpdb->prefix . 'application_fields';  // table name
		$wpdb->insert(
			$application_fields,
			array(
				'group_id'            => $_POST["active_group_id"],
				'json_data'            => $fields_data,
				'created_by'        => get_current_user_id(),
			)
		);
		$field_id = $wpdb->insert_id;
	} else {

		$fields[] = $new_fields;



		$fields_data = maybe_serialize($fields);

		$application_fields = $wpdb->prefix . 'application_fields';  // table name
		$wpdb->update(
			$application_fields,
			array(
				'json_data'            => $fields_data,
			),
			array("group_id" => $_POST["active_group_id"])
		);
		$field_id = $wpdb->insert_id;
	}


	$linkk = $_SERVER['HTTP_REFERER'];

	wp_redirect($linkk);
	exit;
}

add_action('wp_ajax_fields_create_action', 'fields_create_action', 10);
add_action('wp_ajax_nopriv_fields_create_action', 'fields_create_action', 10);

function get_parent_field_data($name, $group_id)
{
	$get_app_fields = get_app_fields($group_id);

	$field_data = array();

	foreach ($get_app_fields as $key => $get_app_field) {
		if ($get_app_field["name"] == $name) {
			$field_data = $get_app_field;
		}
	}

	return $field_data;
}
function fields_update_action()
{


	global $wpdb;

	$fields = array();

	foreach ($_POST['fields'] as $key => $field) {

		$new_fields = array();

		$field["tooltip"] = str_replace('\"', '"', $field["tooltip"]);
		$field["tooltip"] = str_replace('/"', '"', $field["tooltip"]);

		$new_fields["label"] = $field["field_label"];
		$new_fields["type"] = $field["field_type"];
		$new_fields["name"] = $field["field_name"];
		$new_fields["max_input_number"] = $field["max_input_number"];
		$new_fields["required"] = (int) $field["field_required"];
		$new_fields["tooltip"] = $field["tooltip"];
		$new_fields["parent_field"] = $field["parent_field"];
		$new_fields["field_options"] = $field["field_options"];
		$new_fields["type_select"] = $field["type_select"];
		$new_fields["field_width"] = $field["field_width"];
		$new_fields["multiple"] = (int) $field["select_multiple"];
		$new_fields["show_in_booking_summery"] = (int) $field["show_in_booking_summery"];
		$new_fields["show_in_calender"] = (int) $field["show_in_calender"];
		$new_fields["param_algo"] = (int) $field["param_algo"];
		$new_fields["field_position"] =  $field["field_position"];

		$new_fields["listings"] = array();
		if ($field["status"] == "") {
			$field["status"] = 1;
		}
		$new_fields["status"] =  (int) $field["status"];

		$fields[] = $new_fields;
	}



	$fields_data = maybe_serialize($fields);

	$application_fields = $wpdb->prefix . 'application_fields';  // table name
	$wpdb->update(
		$application_fields,
		array(
			'json_data'            => $fields_data,
		),
		array("group_id" => $_POST["active_group_id"])
	);

	update_childfields_data($_POST["active_group_id"]);

	$linkk = $_SERVER['HTTP_REFERER'];

	wp_redirect($linkk);
	exit;
}

add_action('wp_ajax_fields_update_action', 'fields_update_action', 10);
add_action('wp_ajax_nopriv_fields_update_action', 'fields_update_action', 10);

function update_childfields_data($group_id)
{
	global $wpdb;

	$get_app_fields = get_app_fields($group_id);

	$data = array();



	foreach ($get_app_fields as  $get_app_field) {
		if ($get_app_field["parent_field"] != "") {
			$get_parent_field_data = get_parent_field_data($get_app_field["parent_field"], $group_id);

			if (!empty($get_parent_field_data)) {
				$get_app_field["show_in_booking_summery"] = (int) $get_parent_field_data["show_in_booking_summery"];
				$get_app_field["show_in_calender"] = (int) $get_parent_field_data["show_in_calender"];
				$get_app_field["field_position"] =  $get_parent_field_data["field_position"];
			}

			$data[] = $get_app_field;
		} else {
			$data[] = $get_app_field;
		}
	}

	$fields_data = maybe_serialize($data);

	$application_fields = $wpdb->prefix . 'application_fields';  // table name
	$wpdb->update(
		$application_fields,
		array(
			'json_data'            => $fields_data,
		),
		array("group_id" => $group_id)
	);
}

function get_app_fields($group_id)
{

	global $wpdb;

	$application_fields_table = $wpdb->prefix . 'application_fields';  // table name
	$sqlfields = "select json_data from `$application_fields_table` where group_id = " . $group_id;
	$application_fields = $wpdb->get_row($sqlfields);



	if (isset($application_fields->json_data)) {

		$dd =  maybe_unserialize($application_fields->json_data);



		if(is_array($dd)){

			return $dd;

		}else{

			$ff_data = preg_replace_callback('!s:\d+:"(.*?)";!s', 
	            function($m) {
	                return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; 
	            }, $application_fields->json_data
	        );
	        return $field_datas = maybe_unserialize($ff_data);

		}

		
		
	} else {
		return array();
	}
}

function field_group_exist($group_id)
{

	global $wpdb;

	$application_fields_table = $wpdb->prefix . 'application_fields';  // table name
	$sqlfields = "select json_data from `$application_fields_table` where group_id = " . $group_id;
	$application_fields = $wpdb->get_row($sqlfields);

	if (isset($application_fields->json_data)) {
		return true;
	} else {
		return false;
	}
}

function fieldstree($itemssss)
{
	$items = array();
	foreach ($itemssss as $it) {
		$it = (array) $it;
		$items[$it["name"]] = (array) $it;
	}




	/* $new = array();
	foreach ($items as $a){
	    $new[$a->parent_field][] = $a;
	}


	echo "<pre>"; print_r($items); die;*/



	$trees = createtree($items);





	$data = array();

	foreach ($trees as $key => $tree) {
		if ($tree->parent_field == "") {
			$data[] = (object) $tree;
		}
	}

	//echo "<pre>"; print_r($data); die;

	return $data;
}
/* Recursive branch extrusion */
function createbranch($parents, $children)
{
	$tree = array();
	foreach ($children as $child) {
		if (isset($parents[$child['name']])) {
			$child['children'] =
				createbranch($parents, $parents[$child['name']]);
		}
		$tree[] = (object) $child;
	}
	return $tree;
}

/* Initialization */
function createtree($flat, $root = "")
{
	$parents = array();
	foreach ($flat as $a) {
		$parents[$a['parent_field']][] = $a;
	}

	return createbranch($parents, $parents[$root]);
}
/* function createtree($list, $parent){
    $tree = array();
    foreach ($parent as $k=>$l){

        if(isset($list[$l->name])){
            $l->children = createtree($list, $list[$l->name]);
        }
        $tree[] = $l;
    } 

    return $tree;
}*/


function fieldstable($fields, $idd = "")
{

	global $wpdb, $group_listings;
	$active_group_id = get_user_meta(get_current_user_ID(), '_gibbs_active_group_id', true);

	if (!$group_listings) {
		$group_listings = $wpdb->get_results("SELECT ID,post_title FROM {$wpdb->posts} WHERE post_type = 'listing' AND post_status='publish' AND users_groups_id=" . $active_group_id);
	}

?>
	<table class="table user-table table-hover align-items-center datatable" style="width:100%;">
		<thead>

		</thead>
		<tbody id="fieldtable<?php echo $idd; ?>">

			<?php foreach ($fields as $key => $get_app_field) { ?>
				<tr id="drag_<?php echo $get_app_field->name; ?>" class="dnd-moved some-handle connectedSortable">
					<td>
						<div class="label_fields">
							<div class="text_fields">
								<b><?php echo $get_app_field->label; ?></b>
								<div class="iconss">
									<i class="fa fa-plus add_field svgs" data-parent="<?php echo $get_app_field->name; ?>"></i>
									<i class="fa fa-edit edit_field svgs" data-modal="fieldsModal<?php echo $get_app_field->name; ?>"></i>
									<?php if ($get_app_field->status == 1) { ?>

										<button class="dropbtn_custom_field delete_field" data-name="#drag_<?php echo $get_app_field->name; ?>"><span class="filter_text">Deaktiver</span></button>


									<?php } else { ?>
										<button class="dropbtn_custom_field show_field" data-name="#drag_<?php echo $get_app_field->name; ?>"><span class="filter_text">Aktiver</span></button>

									<?php } ?>
								</div>

							</div>
							<div class="edit_models">
								<?php require("edit_model.php"); ?>
							</div>
						</div>
						<div class="table_inner">
							<?php if (isset($get_app_field->children) && !empty($get_app_field->children)) {

								echo fieldstable($get_app_field->children, $get_app_field->name);
							} ?>
						</div>
					</td>
				</tr>
			<?php } ?>




		</tbody>
	</table>
<?php

}


function get_lables($group_id){

	$get_app_fields = get_app_fields($group_id);



	$lables = array();
	foreach ($get_app_fields as $key_daya => $row) { 

		$lables[$row["name"]] = $row["label"];


    }

    return $lables;

}
add_action('wp_ajax_save_field_btn_action', 'save_field_btn_action', 10);
add_action('wp_ajax_nopriv_save_field_btn_action', 'save_field_btn_action', 10);

function save_field_btn_action()
{

	$group_admin = get_group_admin();
	if($group_admin == ""){
		$group_admin = get_current_user_ID();
	}
	update_user_meta($group_admin,"field_btn_action",$_POST['field_btn_action']);
	exit;
}