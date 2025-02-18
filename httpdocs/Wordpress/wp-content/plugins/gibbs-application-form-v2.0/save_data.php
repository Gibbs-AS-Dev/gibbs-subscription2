<?php
require 'dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function add_new_application_ajax()
{


	$form_path = plugin_dir_path(__FILE__) . "/forms/" . $_GET["form_name"] . ".php";

	$data = array("success" => false, "content" => "");

	if (file_exists($form_path)) {

		require_once($form_path);

		$application_fields = get_application($_GET["index"], $_GET["group_id"], $_GET["season_id"]);


		ob_start();
		//include the specified file
		require_once("single_application.php");

		$content = ob_get_clean();

		$data["success"] = true;
		$data["content"] = $content;
	}

	wp_send_json($data);

	die;
}

add_action('wp_ajax_add_new_application_ajax', 'add_new_application_ajax', 10);
add_action('wp_ajax_nopriv_add_new_application_ajax', 'add_new_application_ajax', 10);

function add_reservation_ajax()
{

	$form_path = plugin_dir_path(__FILE__) . "forms/" . $_POST["form_name"] . ".php";

	$data = array("success" => false, "content" => "");

	if (file_exists($form_path)) {

		require_once($form_path);

		$res_datas = get_days($_POST["application_id"], $_POST["group_id"], $_POST["season_id"], array(), $_POST["index"]);

		$application_id = $_POST["application_id"];


		ob_start();
		//include the specified file
		foreach ($res_datas as  $res_datas_days) {
			foreach ($res_datas_days as  $res_datas_day) {
				include("fields/inner_get_day.php");
			}
		}


		$content = ob_get_clean();

		$data["success"] = true;
		$data["content"] = $content;
	}

	wp_send_json($data);

	die;
}

add_action('wp_ajax_add_reservation_ajax', 'add_reservation_ajax', 10);
add_action('wp_ajax_nopriv_add_reservation_ajax', 'add_reservation_ajax', 10);

function add_fields_ajax()
{

	$form_path = plugin_dir_path(__FILE__) . "forms/" . $_POST["form_name"] . ".php";

	$data = array("success" => false, "content" => "");

	if (file_exists($form_path)) {

		require_once($form_path);

		$res_datas = advanced_fields($_POST["application_id"], $_POST["group_id"], $_POST["res_count"], array(), $_POST["index"]);

		//  echo "<pre>"; print_r($res_datas); die;

		$application_id = $_POST["application_id"];


		ob_start();
		//include the specified file
		include("fields/advanced_fields.php");


		$content = ob_get_clean();

		$data["success"] = true;
		$data["content"] = $content;
	}

	wp_send_json($data);

	die;
}

add_action('wp_ajax_add_fields_ajax', 'add_fields_ajax', 10);
add_action('wp_ajax_nopriv_add_fields_ajax', 'add_fields_ajax', 10);


function get_sub_location_ajax()
{

	$form_path = plugin_dir_path(__FILE__) . "forms/" . $_POST["form_name"] . ".php";

	$data = array("success" => false, "content" => "");

	if (file_exists($form_path)) {

		global $wpdb;

		require_once($form_path);

		$day = $_POST['day'];

		$post_meta_table = $wpdb->prefix . "postmeta";
		$posts_table = $wpdb->prefix . "posts";

		$slots = $wpdb->get_var("SELECT `meta_value` FROM $post_meta_table WHERE `post_id` = " . $_POST["parent_id"] . " AND `meta_key`= '_slots'");
		$opening_hours = $wpdb->get_var("SELECT `meta_value` FROM $post_meta_table WHERE `post_id` = " . $_POST["parent_id"] . " AND `meta_key`= '_{$day}_opening_hour'");
		$closing_hours = $wpdb->get_var("SELECT `meta_value` FROM $post_meta_table WHERE `post_id` = " . $_POST["parent_id"] . " AND `meta_key`= '_{$day}_closing_hour'");




		$listings = get_sub_locations_data($_POST["parent_id"]);
		$times = get_times();

		$opening = "";
		$closing = "";
		// echo $slots;
		if (!empty($slots)) {
			$slots = json_decode($slots);
			$working_array_index = $day - 1;
			$working_hours = $slots[$working_array_index];


			if (!empty($working_hours)) {
				$str = str_replace(" ", "", $working_hours[0]);
				$pattern = "/\|.*$/i";
				preg_match($pattern, $str, $matches);
				$str = str_replace($matches[0], "", $str);
				$working_hours = explode("-", $str);
				$opening = $working_hours[0];
				$closing = $working_hours[1];
			}
			// print_r($slots[$working_array_index]);
		}

		$data["empty_sub"] = false;
		$data["opening"] = $opening;
		$data["closing"] = $closing;

		$from_times = "<option value=''>Velg</option>";
		$to_times = "<option value=''>Velg</option>";

		if ($opening != "" && $closing != "") {


			foreach ($times as $key => $time) {

				if ($time >= $opening && $time <= $closing) {
					$from_times .= "<option value='" . $time . "'>" . $time . "</option>";
					$to_times .= "<option value='" . $time . "'>" . $time . "</option>";
				}
			}
		}





		$options = "<option value=''>Velg</option>";
		foreach ($listings as $key => $listing) {
			$options .= "<option value='" . $key . "'>" . $listing . "</option>";
		}

		if (empty($listings)) {
			$data["empty_sub"] = true;
		}

		$data["success"] = true;
		$data["sub_listings"] = $options;
		$data["from_times"] = $from_times;
		$data["to_times"] = $to_times;
	}

	wp_send_json($data);

	die;
}

add_action('wp_ajax_get_sub_location_ajax', 'get_sub_location_ajax', 10);
add_action('wp_ajax_nopriv_get_sub_location_ajax', 'get_sub_location_ajax', 10);

function get_day_index_form($day)
{
	$days = [
		'monday' => 1,
		'tuesday' => 2,
		'wednesday' => 3,
		'thursday' => 4,
		'friday' => 5,
		'saturday' => 6,
		'sunday' => 7,
	];
	return $days[$day];
}

function save_org_user()
{



	global $wpdb;
	$email = email_exists($_POST["email"]);
	$username = username_exists($_POST["email"]);
	$not_user_already =  $_POST["not_user_already"];
	if ($email) {
		$response = array("error" => 1, "message" => __("Email already_exist!", "gibbs"));
	} elseif ($username) {
		$response = array("error" => 1, "message" => __("Brukernavn er allerede brukt", "gibbs"));
	} else {

		$current_user_id = get_current_user_ID();



		$random_password = wp_generate_password();
		$display_name = $_POST["first_name"] . " " . $_POST["last_name"];



		$users_table = $wpdb->prefix . 'users';  // table name
		$wpdb->insert(
			$users_table,
			array(
				'user_login'            => $_POST["email"],
				'user_pass'            => $random_password,
				'user_nicename'        => $_POST["first_name"],
				'user_email'            => $_POST["email"],
				'display_name'            => $display_name,
				'user_registered'         => date("Y-m-d H:i:s"),
			)
		);
		$user_id = $wpdb->insert_id;


		$user = new WP_User($user_id);

		$user->set_role('owner');

		update_user_meta($user_id, "first_name", $_POST["first_name"]);
		update_user_meta($user_id, "profile_type", "company");
		update_user_meta($user_id, "company_number", $_POST["company_number"]);
		update_user_meta($user_id, "phone", $_POST["phone"]);

		$sub_users = get_user_meta($current_user_id, "sub_users", true);
		if (!empty($sub_users) && !is_array($sub_users) && $sub_users != "") {
			$sub_users = array($sub_users);
		}



		if (is_array($sub_users) && !empty($sub_users)) {
			if (!in_array($user_id, $sub_users)) {
				$sub_users[] = $user_id;
			}
		} else {
			$sub_users = array($user_id);
		}


		update_user_meta($current_user_id, "sub_users", $sub_users);

		if (is_user_logged_in()) {
			clean_user_cache(get_current_user_id());
			wp_clear_auth_cookie();
		}





		$user = get_user_by('id', $user_id);

		if ($user) {
			$user_id = $user->ID;
			wp_set_current_user($user_id, $user->user_login);
			wp_set_auth_cookie($user_id);
			update_user_caches($user);
		}
		if (isset($_SESSION['parent_user_id']) && $_SESSION['parent_user_id'] != "") {
		} else {
			$_SESSION['parent_user_id'] = $current_user_id;
		}

		$response = array("error" => 0, "message" => __("Successfully add org user.", "gibbs"));
		/*$redirect_page_link = get_permalink($_GET['post_id']);
        if ( !empty($redirect_page_link) ) {
            if( wp_redirect($redirect_page_link) ) { exit; }
        }*/
	}
	echo json_encode($response);
	die;
}

add_action('wp_ajax_save_org_user', 'save_org_user', 10);
add_action('wp_ajax_nopriv_save_org_user', 'save_org_user', 10);


function deleteApplicationTableData($app_data_id)
{
	global $wpdb;

	$bookings_raw_table = $wpdb->prefix . 'bookings_calendar_raw';
	$applications_table = 'applications';
	$team_table = $wpdb->prefix . 'team';

	$app_sql = "select * from `$applications_table` where application_data_id = " . $app_data_id;

	$applications_table_data = $wpdb->get_results($app_sql);

	$team_ids = array();

	foreach ($applications_table_data as $key => $data_app) {

		$team_ids[] = $data_app->team_id;

		$wpdb->delete(
			$bookings_raw_table,
			array(
				'application_id'          => $data_app->id,
			),
		);
	}


	$wpdb->delete(
		$applications_table,
		array(
			'application_data_id'  => $app_data_id,
		),
	);

	foreach ($team_ids as $key => $teamid) {
		$wpdb->delete(
			$team_table,
			array(
				'id'          => (int) $teamid,
			),
		);
	}


	return true;
}


function saveDayReservation($reservation, $application, $application_id, $team_id, $comment, $ap_user_id)
{
	global $wpdb;
	$bookings_raw_table = $wpdb->prefix . 'bookings_calendar_raw';
	$applications_table = 'applications';
	$team_table = $wpdb->prefix . 'team';

	$from_time = "2021-02-0" . $reservation["day"] . " " . $reservation["from-time"];
	$to_time = "2021-02-0" . $reservation["day"] . " " . $reservation["to-time"];


	$fields_data = array();

	if (isset($reservation["custom_fields"])) {
		$fields_data = $reservation["custom_fields"];
	}

	if (!empty($fields_data)) {
		$fields_data = maybe_serialize($fields_data);
	} else {
		$fields_data = null;
	}



	if ($reservation["sub-location"] != "") {

		$sub_locations = explode(",", $reservation["sub-location"]);

		$first_event_id = 0;

		$sub_locations_ids = array();

		$sub_count = 0;

		foreach ($sub_locations as $key => $sub_location) {

			$wpdb->insert(
				$bookings_raw_table,
				array(
					'application_id' => $application_id,
					'team_id'        => $team_id,
					'listing_id'     => $sub_location,
					'parent_listing_id' => $reservation['location'],
					'date_start'     => date($from_time),
					'date_end'       => date($to_time),
					'day'            => $reservation["day"],
					'comment'        => json_encode($comment),
					'bookings_author' => $ap_user_id,
					'fields_data' => $fields_data,
				),
			);
			$bk_id = $wpdb->insert_id;

			$sub_locations_ids[] = $bk_id;

			if ($sub_count == 0) {
				$first_event_id = $bk_id;
			}

			$sub_count++;
		}

		foreach ($sub_locations_ids as $key => $sub_locations_id) {
			$wpdb->update($bookings_raw_table, array('first_event_id' => $first_event_id), array('id' => $sub_locations_id));
		}
	} else {

		$wpdb->insert(
			$bookings_raw_table,
			array(
				'application_id' => $application_id,
				'team_id'        => $team_id,
				'listing_id'     => $reservation['location'],
				'date_start'     => date($from_time),
				'date_end'       => date($to_time),
				'day'            => $reservation["day"],
				'comment'        => json_encode($comment),
				///'description'        => $application['comments'],
				'bookings_author' => $ap_user_id,
				'fields_data' => $fields_data,
			),
		);
		$firstEventID = $wpdb->insert_id;
		$wpdb->update($bookings_raw_table, array('first_event_id' => $firstEventID), array('id' => $firstEventID));
	}
}
function saveDateReservation($reservation, $application, $application_id, $team_id, $comment, $ap_user_id)
{
	global $wpdb;
	$bookings_raw_table = $wpdb->prefix . 'bookings_calendar_raw';
	$applications_table = 'applications';
	$team_table = $wpdb->prefix . 'team';

	$from_date = $reservation["from_date"] . " " . $reservation["from-time"];
	$to_date =  $reservation["to_date"] . " " . $reservation["to-time"];

	$locations = explode(",", $reservation["location"]);

	$fields_data = array();

	if (isset($reservation["custom_fields"])) {
		$fields_data = $reservation["custom_fields"];
	}

	if (!empty($fields_data)) {
		$fields_data = maybe_serialize($fields_data);
	} else {
		$fields_data = null;
	}

	$first_event_id = 0;

	$locations_ids = array();

	$sub_count = 0;

	foreach ($locations as $key => $location) {

		$wpdb->insert(
			$bookings_raw_table,
			array(
				'application_id' => $application_id,
				'team_id'        => $team_id,
				'listing_id'     => $location,
				'date_start'     => date($from_date),
				'date_end'       => date($to_date),
				'comment'        => json_encode($comment),
				'bookings_author' => $ap_user_id,
				'fields_data' => $fields_data,
			),
		);
		$bk_id = $wpdb->insert_id;

		$locations_ids[] = $bk_id;

		if ($sub_count == 0) {
			$first_event_id = $bk_id;
		}

		$sub_count++;
	}

	foreach ($locations_ids as $key => $locations_id) {
		$wpdb->update($bookings_raw_table, array('first_event_id' => $first_event_id), array('id' => $locations_id));
	}



	/*
		$wpdb->insert(
            $bookings_raw_table,
            array(
                'application_id' => $application_id,
                'team_id'        => $team_id,
                'listing_id'     => $reservation['location'],
                'date_start'     => date($from_date),
                'date_end'       => date($to_date),
                'comment'        => json_encode($comment),
                'description'        => $application['comments'],
                'bookings_author'=> get_current_user_ID(),
            ),
        );
        $firstEventID = $wpdb->insert_id;
        $wpdb->update($bookings_raw_table, array('first_event_id' => $firstEventID), array('id' => $firstEventID) );*/
}
function saveTableData($app_data_id, $json_data)
{

	global $wpdb;

	$bookings_raw_table = $wpdb->prefix . 'bookings_calendar_raw';
	$applications_table = 'applications';
	$team_table = $wpdb->prefix . 'team';

	//echo "<pre>"; print_r($json_data["application"]); die;
	$ap_user_id = get_current_user_ID();

	if (isset($json_data["user_id"])) {
		$ap_user_id = $json_data["user_id"];
	}


	foreach ($json_data["application"] as $key => $application) {

		$app_fields_data = array();

		if (isset($application["application_fields"])) {
			$app_fields_data = $application["application_fields"];
		}


		if (!empty($app_fields_data)) {
			$app_fields_data = maybe_serialize($app_fields_data);
		} else {
			$app_fields_data = null;
		}

		$wpdb->insert(
			$team_table,
			array(
				'name'          => $application['team-name'],
				'published_at' => date("Y-m-d H:i:s"),
				'user_id' => $ap_user_id,
				'created_by' => $ap_user_id,
				'updated_by' => get_current_user_ID()
			),
		);
		$team_id = $wpdb->insert_id;

		if ($team_id != "") {
			$wpdb->insert(
				$applications_table,
				array(
					'user_id'       => $ap_user_id,
					'name'          => $application['team-name'],
					'team_id'       => $team_id,
					'members'       => $application['member_count'],
					'age_group_id'  => $application['age'],
					'team_level_id' => $application['level'],
					'sport_id'      => $application['sports'],
					'preferred_listing1_id' => $application['pri-1'],
					'preferred_listing2_id' => $application['pri-2'],
					'preferred_listing3_id' => $application['pri-3'],
					'comment'        => $application['comments'],
					'season_id'      => $json_data['season_id'],
					'type_id'        => $json_data['applicant_type'],
					'application_data_id'        => $app_data_id,
					'app_fields_data'        => $app_fields_data,
				)
			);


			$application_id = $wpdb->insert_id;

			if ($application_id != "" && isset($application["reservations"]) && !empty($application["reservations"])) {

				//$comment = array('message' => $application['comments']);

				$comment =  get_comment_data_application($ap_user_id,$application['comments']);

				foreach ($application["reservations"] as $key => $reservation) {


					if (isset($reservation["from_date"])) {
						saveDateReservation($reservation, $application, $application_id, $team_id, $comment, $ap_user_id);
					} else {
						saveDayReservation($reservation, $application, $application_id, $team_id, $comment, $ap_user_id);
					}
				}
			}
		}
	}
}

function get_comment_data_application($user_id,$message){

    $userData = get_userdata($user_id);

    

    $comment= array(
                    'first_name'    => $userData->first_name,
                    'last_name'     => $userData->last_name,
                    'email'         => $userData->user_email,
                    'phone'         => get_user_meta($user_id,"phone",true),
                    'message'       => $message,
                    'billing_address_1' => get_user_meta($user_id,"billing_address_1",true),
                    'billing_postcode'  => get_user_meta($user_id,"billing_postcode",true),
                    'billing_city'      => get_user_meta($user_id,"billing_city",true),
                    'billing_country'   => get_user_meta($user_id,"billing_country",true)
                );
    return $comment;

}

function application_submit()
{

	global $wpdb;

	$application_data_table = 'application_data';  // table name

	$current_user_id = get_current_user_ID();

	$group_id = $_POST["group_id"];
	$season_id = $_POST["season_id"];

	$sql_application_data_season_check = "select *  from `$application_data_table` where group_id = $group_id AND season_id = $season_id AND user_id = $current_user_id";

	$season_check_data = $wpdb->get_row($sql_application_data_season_check);

	if (isset($season_check_data->id)) {

		deleteApplicationTableData($season_check_data->id);

		$wpdb->delete($application_data_table, array("id" => $season_check_data->id));
	}

	$users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
	$users_table_sql = "select users_id from `$users_and_users_groups` where users_groups_id = $group_id AND users_id = $current_user_id";
	$users_data = $wpdb->get_results($users_table_sql);

	if (empty($users_data)) {

		$wpdb->insert($users_and_users_groups, array(
			'users_groups_id'            => $group_id,
			'users_id'            => $current_user_id,
			'role'            => "1",
		));
	}





	$json_data = json_data($_POST);

	$app_id = save_application_data($json_data, $_POST);

	if (isset($_POST["form_type"]) && $_POST["form_type"] == "draft") {

		$linkk = $_POST['redirect_draft'];
	} else {
		saveTableData($app_id, $json_data);
		$linkk = $_POST['redirect'] . "?success=true&application_id=" . $app_id;
		if (isset($_POST["from_admin"]) && $_POST["from_admin"] == "true") {
			$linkk .= "&admin=true";
		} else {
			sendMailForm($json_data, $app_id);
		}
	}

	wp_redirect($linkk);
	exit;
}

add_action('wp_ajax_application_submit', 'application_submit', 10);
add_action('wp_ajax_nopriv_application_submit', 'application_submit', 10);

function save_application_data($json_data, $post)
{
	global $wpdb;




	$seasons_table = 'seasons';  // table name
	$sql_season = "select name, season_end from `$seasons_table` where id = " . $post["season_id"];
	$season_data = $wpdb->get_row($sql_season);

	$season_name = $season_data->name;
	$season_end = $season_data->season_end;

	if (isset($post["form_type"]) && $post["form_type"] == "draft") {
		$status = "0";
	} else {
		$status = "1";
	}


	$application_data_table = "application_data";  // table name
	$about_fields_data = array();

	if (isset($json_data["about_fields"])) {
		$about_fields_data = $json_data["about_fields"];
	}



	if (!empty($about_fields_data)) {
		$about_fields_data = maybe_serialize($about_fields_data);
	} else {
		$about_fields_data = null;
	}

	if ($about_fields_data == "") {
		$about_fields_data = array();
	}


	$wpdb->insert($application_data_table, array(
		'user_id'            => get_current_user_id(),
		'group_id'            => $post["group_id"],
		'season_id'            => $post["season_id"],
		'deadline'            => $season_end,
		'json_data'            => maybe_serialize($json_data),
		'about_fields_data'    => $about_fields_data,
		'status'        => $status,
	));

	return $wpdb->insert_id;
}

function application_update()
{



	if (isset($_POST["from_admin"]) && $_POST["from_admin"] == "true") {

		global $wpdb;
		$table = "application_data";
		$idd = $_POST["application_id"];
		$ap_data = "select *  from `$table` where id = $idd";

		$ap_data_full = $wpdb->get_row($ap_data);
		if (isset($ap_data_full->user_id)) {
			$_POST["user_id"] = $ap_data_full->user_id;
		}
	}
	$json_data = json_data($_POST);

	//echo "<pre>"; print_r($json_data); die;




	update_application_data($json_data, $_POST);




	if (isset($_POST["form_type"]) && $_POST["form_type"] == "draft") {

		$linkk = $_POST['redirect_draft'];
	} else {

		deleteApplicationTableData($_POST['application_id']);


		saveTableData($_POST['application_id'], $json_data);

		$linkk = $_POST['redirect'] . "?success=true&application_id=" . $_POST['application_id'];

		if (isset($_POST["from_admin"]) && $_POST["from_admin"] == "true") {
			$linkk .= "&admin=true";
		} else {
			sendMailForm($json_data, $_POST['application_id']);
		}
	}


	wp_redirect($linkk);
	exit;
}

add_action('wp_ajax_application_update', 'application_update', 10);
add_action('wp_ajax_nopriv_application_update', 'application_update', 10);

function update_application_data($json_data, $post)
{
	global $wpdb;

	if (isset($post["form_type"]) && $post["form_type"] == "draft") {
		$status = "0";
	} else {
		$status = "1";
	}

	$application_data_table = "application_data";  // table name


	$about_fields_data = array();

	if (isset($json_data["about_fields"])) {
		$about_fields_data = $json_data["about_fields"];
	}



	if (!empty($about_fields_data)) {
		$about_fields_data = maybe_serialize($about_fields_data);
	} else {
		$about_fields_data = null;
	}

	if ($about_fields_data == "") {
		$about_fields_data = array();
	}




	$wpdb->update($application_data_table, array(
		'json_data'            => maybe_serialize($json_data),
		'about_fields_data'    => $about_fields_data,
		'status'        => $status,
	), array("id" => $_POST['application_id']));
}

function json_data($post)
{

	$json_data = $post;


	$application = array();

	$key_app = 1;

	foreach ($json_data["application"] as $key => $data) {

		$data_extra = array();

		if (isset($data["reservations"])) {

			foreach ($data["reservations"] as $key_res => $res) {

				/*foreach ($res as $key_day => $day) {

					 
		             $data_extra[$key_day]["day"] = $day;
		             $data_extra[$key_day]["location"] =  $data["location"][$key_day];
		             $data_extra[$key_day]["sub-location"] = $data["sub-location"][$key_day];
		             $data_extra[$key_day]["from-time"] = $data["from-time"][$key_day];
		             $data_extra[$key_day]["to-time"] = $data["to-time"][$key_day];

				}
				break;*/
				foreach ($res as $key_from_date => $from_date) {

					foreach ($data["reservations"] as $key_res_t => $res_t) {



						if ($key_res_t == "custom_fields") {

							$custom_fields_data = array();



							if (isset($res_t[$key_from_date])) {


								foreach ($res_t[$key_from_date] as $key_fieldd_inner => $c_field_inner) {

									foreach ($c_field_inner as $key_fieldd_inner_inner => $c_field_inner_inner) {

										$custom_fields_data[$key_fieldd_inner_inner][$key_fieldd_inner] = $c_field_inner[$key_fieldd_inner_inner];
									}
								}
							}


							//$data_extra[$key_from_date][$key_res_t] = $custom_fields_data;
							$data_extra[$key_from_date][$key_res_t] = $custom_fields_data;
						} else {
							$data_extra[$key_from_date][$key_res_t] = $res_t[$key_from_date];
						}
						/*$data_extra[$key_from_date]["to_date"] = $data["to_date"][$key_from_date];
			             $data_extra[$key_from_date]["location"] =  $data["location"][$key_from_date];
			             $data_extra[$key_from_date]["from-time"] = $data["from-time"][$key_from_date];
			             $data_extra[$key_from_date]["to-time"] = $data["to-time"][$key_from_date];*/
					}
				}
				break;
			}
		}
		/*echo "<pre>"; print_r($data); 
		echo "<pre>"; print_r($data_extra); die;*/
		$data["reservations"] = $data_extra;

		/*unset($data["from_date"]);
	    unset($data["to_date"]);
	    unset($data["day"]);
	    unset($data["location"]);
	    unset($data["sub-location"]);
	    unset($data["from-time"]);
	    unset($data["to-time"]);*/

		$data["comments"] = stripslashes($data["comments"]);

		// echo "<pre>"; print_r($data);  die;

		$application[$key_app] = $data;
		$key_app++;
	}


	$json_data["application"] = $application;


	return $json_data;
}

add_action('wp_ajax_delete_formm', 'delete_formm', 10);
add_action('wp_ajax_nopriv_delete_formm', 'delete_formm', 10);

function delete_formm()
{
	global $wpdb;

	$application_data_table = "application_data";  // table name

	if (isset($_POST["from_admin"]) && $_POST["from_admin"] == "true") {
		$delete_where = array("id" => $_POST['application_id']);
	} else {
		$delete_where = array("id" => $_POST['application_id'], "user_id" => get_current_user_id());
	}
	deleteApplicationTableData($_POST['application_id']);

	$wpdb->delete($application_data_table, $delete_where);



	$response = array("success" => true);


	echo json_encode($response);
	die;
}
function sendMailForm($json_data, $application_id,$direct_pdf = false)
{






	ob_start();

	$get_age = get_age($json_data["group_id"]);
	$get_levels = get_levels($json_data["group_id"]);
	$get_sports = get_sports($json_data["group_id"]);
	$get_locations_data = get_locations_data($json_data["group_id"]);
	$get_days = get_days_form();



	$user_group_check = user_group_form_check($json_data["group_id"]);

	$user_form = $user_group_check;

	$form_name = "form-" . $user_group_check;

	$form_path = plugin_dir_path(__FILE__) . "/forms/" . $form_name . ".php";

	if (file_exists($form_path)) {
		//include the specified file
		require($form_path);
		$pdftext = pdftext();
		require(plugin_dir_path(__FILE__) . "/pdfTemplate/pdf-form.php");

		$html = ob_get_clean();

		//$json_data["email"] = "sk81930@gmail.com";





		$pdff = generatePdf($html, $application_id, $direct_pdf);

		if($direct_pdf == true){

		}else{
			$admin_email = get_option('admin_email');

			$headers = 'From: Gibbs <' . $admin_email . '>' . "\r\n";

			$html = "Takk for din søknad.<br>"; 

			// Your custom description
			$description = "I linken under kan du se hva du har søkt og hva du har fått av tider når tildelingen er ferdig.";

			// Append the description to the HTML content
			$html .= "<p>" . $description . "</p>";

			// Generate the link and append it to the HTML content
			$html .= "Link : " . home_url() . "/wp-json/v1/generateapp?application_id=" . $application_id;

			// Set the content type of the email to HTML
			add_filter('wp_mail_content_type', function( $content_type ) {
				return 'text/html';
			});

			// Send the email
			wp_mail($json_data["email"], 'Takk for din søknad!', $html, $headers);


			// if (isset($pdff["pdf_path"]) && $pdff["pdf_path"] != "") {
			// 	$attachments = array($pdff["pdf_path"]);
			// 	// echo wp_mail("sk81930@gmail.com", 'Season form submission' , 'Please check attachment to see form submission',$headers,$attachments);
			// 	$html = "Takk for din søknad', 'Vennligst se vedlegg for å se din søknad.<br>";

			// 	$html .="Link : ".home_url()."/wp-json/v1/generateapp?application_id=".$application_id;
			// 	wp_mail($json_data["email"], $html, $headers, $attachments);
			// }
		}

		//die("dfdjfk");



		
	}
}

function generatePdf($html, $application_id, $direct_pdf = false)
{
	global $wpdb;

	$options = new Options();
	$options->set('isRemoteEnabled', true);

	$dompdf = new Dompdf($options);

	$dompdf->loadHtml($html);

	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('A4', 'portrait');

	// Render the HTML as PDF
	$dompdf->render();

	// Output the generated PDF to Browser
	/*$dompdf->stream();
	die;  */
	$data = array();
	$data["pdf_path"] = plugin_dir_path(__FILE__) . "pdfs/form" . $application_id . ".pdf";
	$data["pdf_url"] = plugin_dir_url(__FILE__) . "pdfs/form" . $application_id . ".pdf";
	file_put_contents($data["pdf_path"], $dompdf->output());
	

	$application_data_table = "application_data";  // table name

	$wpdb->update($application_data_table, array(
		'pdf_link'            => $data["pdf_url"],
	), array("id" => $application_id));

	if($direct_pdf == true){
		$dompdf->stream("form" . $application_id . ".pdf", array("Attachment" => true));
		// header('Content-type: application/pdf');
		// header('Content-Disposition: inline; filename="form' . $application_id . '.pdf"');
		// header('Content-Transfer-Encoding: binary');
		// header('Content-Length: ' . filesize($data["pdf_path"]));
		// header('Accept-Ranges: bytes');
		// readfile($data["pdf_path"]);
		exit;
	}

	return $data;
}
function generate_pdf_application(){
	global $wpdb;
	$table = 'application_data';

	$data = $wpdb->get_row("SELECT * FROM $table WHERE id = ".$_POST["app_id"]);


	if(isset($data->json_data)){
		$app_data = maybe_unserialize($data->json_data);
		sendMailForm($app_data,$_POST["app_id"],true);
	}
	exit;
}

add_action('wp_ajax_generate_pdf_application', 'generate_pdf_application', 10);
add_action('wp_ajax_nopriv_generate_pdf_application', 'generate_pdf_application', 10);

function get_applicant_type($group_id)
{

	global $wpdb;
	$user_type_table = 'type';

	/*$cuser_id = get_current_user_id();

	$user_type_table = 'type';

	$users_and_users_group_table = $wpdb->prefix . 'users_and_users_groups';

	$uaug = $wpdb->get_var("SELECT `users_groups_id` FROM $users_and_users_group_table WHERE `users_id`=$cuser_id");*/

	$user_types = $wpdb->get_results("SELECT id,`name` FROM $user_type_table WHERE users_groups_id = $group_id");

	$typess = array();

	foreach ($user_types as $key => $user_type) {
		$typess[$user_type->id] = $user_type->name;
	}
	return $typess;
}

function get_age($group_id)
{

	global $wpdb;

	$age_group_table = 'age_group';


	$age_groups = $wpdb->get_results("SELECT id,`name` FROM $age_group_table where users_groups_id = $group_id");

	$age_groupsss = array();

	foreach ($age_groups as $key => $age_group) {
		$age_groupsss[$age_group->id] = $age_group->name;
	}
	return $age_groupsss;
}

function get_levels($group_id)
{

	global $wpdb;

	$team_level_table = 'team_level';

	$team_levels = $wpdb->get_results("SELECT id,`name` FROM $team_level_table where users_groups_id = $group_id");

	$team_levelssss = array();

	foreach ($team_levels as $key => $team_level) {
		$team_levelssss[$team_level->id] = $team_level->name;
	}
	return $team_levelssss;
}

function get_sports($group_id)
{

	global $wpdb;

	$sport_table = 'sport';

	$sports = $wpdb->get_results("SELECT id,`name` FROM $sport_table where users_groups_id = $group_id");

	$sportssss = array();

	foreach ($sports as $key => $sport) {
		$sportssss[$sport->id] = $sport->name;
	}
	return $sportssss;
}

function get_locations_data($group_id)
{

	global $wpdb;
	$listings_table = $wpdb->prefix . 'posts';

	/*$cuser_id = get_current_user_id();

	$listings_table =$wpdb->prefix. 'posts';
	$users_and_users_group_table = $wpdb->prefix . 'users_and_users_groups';

	$uaug = $wpdb->get_var("SELECT `users_groups_id` FROM $users_and_users_group_table WHERE `users_id`=$cuser_id");

	$active_group_id = get_user_meta( $cuser_id, '_gibbs_active_group_id',true );

	if($active_group_id != ""){
		$group_id = $active_group_id;
	}else{
		$group_id = $uaug;
	}*/

	$listings = $wpdb->get_results("SELECT id,post_title FROM $listings_table WHERE `post_type`='listing' AND `post_parent` = 0 AND `users_groups_id` = $group_id");



	$locations = array();

	foreach ($listings as $key => $listing) {
		$locations[$listing->id] = ucfirst($listing->post_title);
	}
	return $locations;
}
function get_sub_locations_data($parent_listing_id)
{

	global $wpdb;

	$posts_table = $wpdb->prefix . "posts";

	$sublisting = $wpdb->get_results("SELECT id,`post_title` FROM $posts_table WHERE `post_type`='listing' AND `post_parent` = $parent_listing_id");



	$sub_locations = array();

	foreach ($sublisting as $key => $listing) {
		$sub_locations[$listing->id] = ucfirst($listing->post_title);
	}
	return $sub_locations;
}

function get_times($match = '')
{

	$start = '00:00';
	$end = '24:00';
	$date_end = date_create($end);

	$times = array();

	for ($date = date_create($start); $date <= $date_end; $date->modify('+30 Minutes')) {

		$times[$date->format('H:i')] = $date->format('H:i');
	}

	return $times;
}

function get_days_form()
{
	$dayss = array(
		"1" => "Mandag",
		"2" => "Tirsdag",
		"3" => "Onsdag",
		"4" => "Torsdag",
		"5" => "Fredag",
		"6" => "Lørdag",
		"7" => "Søndag",
	);
	return $dayss;
}
function get_sub_location_name($listing_id)
{

	global $wpdb;

	$posts_table = $wpdb->prefix . "posts";

	$sublisting = $wpdb->get_row("SELECT `post_title` FROM $posts_table WHERE `ID`=" . $listing_id);

	if (isset($sublisting->post_title)) {
		return $sublisting->post_title;
	} else {
		return "";
	}
}


function advanced_fields_func($rows, $class = "")
{

	global $current_listing_id;
	//echo "<pre>"; print_r($rows); die;
	if ($class == "") {
?>
		<div class="row reservation_fields">
		<?php } ?>
		<?php
		foreach ($rows as $key_daya => $row) { ?>
			<?php
			$row = (array) $row;

			if (isset($row['listings']) && !empty($row['listings']))
				if (!in_array($current_listing_id, $row['listings'])) continue;


			if ($row['type'] == "text") {

				include("fields/text.php");
			} elseif ($row['type'] == "date") {

				include("fields/date.php");
			} elseif ($row['type'] == "tel") {

				include("fields/tel.php");
			} else if ($row['type'] == "email") {

				include("fields/email.php");
			} else if ($row['type'] == "number") {

				include("fields/number.php");
			} else if ($row['type'] == "select") {

				include("fields/select.php");
			} else if ($row['type'] == "custom_text") {

				include("fields/custom_text.php");
			}
			if (isset($row["children"]) && !empty($row["children"])) {
				advanced_fields_func($row["children"], "inner_div");
			}
			?>
		<?php } ?>
		<?php if ($class == "") {	 ?>
		</div>
	<?php } ?>
	<?php

}
function about_fields_func($rows, $class = "")
{
	//echo "<pre>"; print_r($rows); die;
	if ($class == "") {
	?>
		<div class="row about_fields">
		<?php } ?>
		<?php
		foreach ($rows as $key_daya => $row) { ?>
			<?php
			$row = (array) $row;

			if ($row['type'] == "text") {

				include("fields/text.php");
			} elseif ($row['type'] == "date") {

				include("fields/date.php");
			} elseif ($row['type'] == "tel") {

				include("fields/tel.php");
			} else if ($row['type'] == "email") {

				include("fields/email.php");
			} else if ($row['type'] == "number") {

				include("fields/number.php");
			} else if ($row['type'] == "select") {

				include("fields/select.php");
			} else if ($row['type'] == "custom_text") {

				include("fields/custom_text.php");
			}
			if (isset($row["children"]) && !empty($row["children"])) {
				about_fields_func($row["children"], "about_inner_div");
			}
			?>
		<?php } ?>
		<?php if ($class == "") {	 ?>
		</div>
	<?php } ?>
	<?php

}
function app_fields_func($rows, $class = "")
{
	//echo "<pre>"; print_r($rows); die;
	foreach ($rows as $key_daya => $row) { ?>
		<?php
		$row = (array) $row;

		if ($row['type'] == "text") {

			include("fields/text.php");
		} elseif ($row['type'] == "date") {

			include("fields/date.php");
		} elseif ($row['type'] == "tel") {

			include("fields/tel.php");
		} else if ($row['type'] == "email") {

			include("fields/email.php");
		} else if ($row['type'] == "number") {

			include("fields/number.php");
		} else if ($row['type'] == "select") {

			include("fields/select.php");
		} else if ($row['type'] == "custom_text") {

			include("fields/custom_text.php");
		}
		if (isset($row["children"]) && !empty($row["children"])) {
			about_fields_func($row["children"], "about_inner_div");
		}
		?>
	<?php } ?>
<?php

}



function advanced_fields($application_id, $group_id, $res_count, $fields_data = array(), $index, $from_booking = false, $comes_from = "")
{

	if (function_exists('get_app_fields')) {
		$get_app_fields = get_app_fields($group_id);



		// $get_app_fields = fieldstree($get_app_fields);

		$ad_fieldss = array();

		foreach ($get_app_fields as $key => $field) {

			$field = (object) $field;

			if ($field->field_position == "reservation" && $field->status != 0) {
				//	if ($class == "reservation_fields" && !in_array($current_listing_id, $row['listings'])) continue;

				$fieldd = array();
				$fieldd["label"] = $field->label;
				$fieldd["type"] = $field->type;
				$fieldd["id"] = $field->name . "_" . $application_id . "_" . $res_count . "_" . $index;
				$fieldd["required"] = (int) $field->required;
				$fieldd["class"] = $field->field_width;
				$fieldd["type_select"] = $field->type_select;
				$fieldd["multiple"] = $field->multiple;
				$fieldd["tooltip"] = $field->tooltip;
				$fieldd["listings"] = $field->listings;

				if ($comes_from == "booking_summery") {
					if ($field->show_in_booking_summery != '1') {
						continue;
					}
				}
				if ($comes_from == "calender") {
					if ($field->show_in_calender != '1') {
						continue;
					}
				}

				if ($from_booking == true) {
					$fieldd_name = "fields[" . $field->name . "][]";
					$parent_fieldd_name = "fields[" . $field->parent_field . "][]";
				} else {
					$fieldd_name = "application[$application_id][reservations][custom_fields][$res_count][" . $field->name . "][]";
					$parent_fieldd_name = "application[$application_id][reservations][custom_fields][$res_count][" . $field->parent_field . "][]";
				}
				$fieldd["name"] = $fieldd_name;

				$fieldd["org-name"] = $field->name;
				if ($field->parent_field == "") {
					$fieldd["parent_field"] = "";
				} else {
					$fieldd["parent_field"] = $parent_fieldd_name;
				}
				$fieldd["max_input_number"] = $field->max_input_number;

				if ($field->type == "select" || $field->type == "checkbox") {

					$opts = explode(",", $field->field_options);

					$options = array();

					foreach ($opts as $key_opt => $opt) {
						$options[$opt] = $opt;
					}
					$fieldd["options"] = $options;

					if (!empty($fields_data)) {

						$fieldd["selected"] = (isset($fields_data[$field->name])) ? $fields_data[$field->name] : "";
					}
				} else {
					if (!empty($fields_data)) {


						$fieldd["value"] = (isset($fields_data[$field->name])) ? $fields_data[$field->name] : "";
					}
				}
				$ad_fieldss[] = (object) $fieldd;
			}
		}


		$get_app_fields = fieldstree($ad_fieldss);


		return $get_app_fields;
	}
}
function additional_application_fields($group_id, $application_id, $fields_data = array())
{

	if (function_exists('get_app_fields')) {
		$get_app_fields = get_app_fields($group_id);

		if (isset($fields_data["application_fields"]) && !empty($fields_data["application_fields"])) {
			$fields_data = $fields_data["application_fields"];
		}



		// $get_app_fields = fieldstree($get_app_fields);

		$ad_fieldss = array();




		foreach ($get_app_fields as $key => $field) {



			$field = (object) $field;

			if ($field->field_position == "application" && $field->status != 0) {


				$fieldd = array();
				$fieldd["label"] = $field->label;
				$fieldd["type"] = $field->type;
				$fieldd["id"] = $field->name;
				$fieldd["required"] = (int) $field->required;
				$fieldd["class"] = $field->field_width;
				$fieldd["tooltip"] = $field->tooltip;

				$fieldd["type_select"] = $field->type_select;
				$fieldd["multiple"] = $field->multiple;

				$fieldd_name = "application[$application_id][application_fields][" . $field->name . "]";
				$parent_fieldd_name = "application[$application_id][application_fields][" . $field->parent_field . "]";

				$fieldd["name"] = $fieldd_name;

				$fieldd["org-name"] = $field->name;
				if ($field->parent_field == "") {
					$fieldd["parent_field"] = "";
				} else {
					$fieldd["parent_field"] = $parent_fieldd_name;
				}
				$fieldd["max_input_number"] = $field->max_input_number;


				if ($field->type == "select" || $field->type == "checkbox") {

					$opts = explode(",", $field->field_options);

					$options = array();

					foreach ($opts as $key_opt => $opt) {
						$options[$opt] = $opt;
					}
					$fieldd["options"] = $options;

					if (!empty($fields_data)) {

						$fieldd["selected"] = (isset($fields_data[$field->name])) ? $fields_data[$field->name] : "";
					}
				} else {
					if (!empty($fields_data)) {


						$fieldd["value"] = (isset($fields_data[$field->name])) ? $fields_data[$field->name] : "";
					}
				}
				$ad_fieldss[] = (object) $fieldd;
			}
		}




		$get_app_fields = fieldstree($ad_fieldss);



		return $get_app_fields;
	} else {
		return array();
	}
}


function additional_about_fields($group_id, $fields_data = array())
{

	if (function_exists('get_app_fields')) {
		$get_app_fields = get_app_fields($group_id);



		// $get_app_fields = fieldstree($get_app_fields);

		$ad_fieldss = array();



		foreach ($get_app_fields as $key => $field) {



			$field = (object) $field;

			if ($field->field_position == "about" && $field->status != 0) {


				$fieldd = array();
				$fieldd["label"] = $field->label;
				$fieldd["type"] = $field->type;
				$fieldd["id"] = $field->name;
				$fieldd["required"] = (int) $field->required;
				$fieldd["class"] = $field->field_width;
				$fieldd["tooltip"] = $field->tooltip;

				$fieldd["type_select"] = $field->type_select;
				$fieldd["multiple"] = $field->multiple;

				$fieldd_name = "about_fields[" . $field->name . "]";
				$parent_fieldd_name = "about_fields[" . $field->parent_field . "]";

				$fieldd["name"] = $fieldd_name;

				$fieldd["org-name"] = $field->name;
				if ($field->parent_field == "") {
					$fieldd["parent_field"] = "";
				} else {
					$fieldd["parent_field"] = $parent_fieldd_name;
				}

				$fieldd["max_input_number"] = $field->max_input_number;


				if ($field->type == "select" || $field->type == "checkbox") {

					$opts = explode(",", $field->field_options);

					$options = array();

					foreach ($opts as $key_opt => $opt) {
						$options[$opt] = $opt;
					}
					$fieldd["options"] = $options;

					if (!empty($fields_data)) {

						$fieldd["selected"] = (isset($fields_data[$field->name])) ? $fields_data[$field->name] : "";
					}
				} else {
					if (!empty($fields_data)) {


						$fieldd["value"] = (isset($fields_data[$field->name])) ? $fields_data[$field->name] : "";
					}
				}
				$ad_fieldss[] = (object) $fieldd;
			}
		}



		$get_app_fields = fieldstree($ad_fieldss);


		return $get_app_fields;
	} else {
		return array();
	}
}


add_action('wp_ajax_get_single_row_fields', 'get_single_row_fields', 10);
add_action('wp_ajax_nopriv_get_single_row_fields', 'get_single_row_fields', 10);

function get_single_row_fields()
{
	echo $data = get_single_field($_POST['group_id']);
	die;
}
function row_data($fields_row)
{ ?>
	<div class="inner-advanced_fields">

		<div class="delete_field_div">
			<i class="fa fa-trash delete_field delete_field_booking"></i>
		</div>


		<?php echo advanced_fields_func($fields_row); ?>


	</div>
<?php }

$current_listing_id = false;
function repeated_fields($rows, $group_id, $listing_id = false)
{

	global $current_listing_id;
	$current_listing_id = $listing_id;
?>
	<div class="row">
		<div class="col-md-12 custom_fields_booking">

			<?php foreach ($rows as $key_row => $fields_row) {
				echo row_data($fields_row);
			} ?>
		</div>
		<div class="col-md-12">
			<div class="">
				<buottn class="btn btn-primary add_field_btn" type="button">Legg til</button>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function select2funcfield() {
			jQuery(".select2_field").each(function() {
				jQuery(this).select2({
					placeholder: 'Velg',
					/*
					                    width: 'resolve',
					                    dropdownAutoWidth: 'true',
					                    allowClear: 'true'*/
				});
			})


			changeslectfield();
		}

		function changeslectfield() {

			jQuery(".custom_fields_booking").find("select").each(function() {

				if (this.multiple == true) {


					let optionss = [];

					jQuery(this).find("option:selected").each(function() {
						optionss.push(this.value);
					})

					/*if(optionss > 0 ){

					  jQuery(this).parent().find(".select2-container").find(".selection").html("Selected ("+optionss.length+")");
					}else{
					  jQuery(this).parent().find(".select2-selection--multiple").html("");
					}*/
					var uldiv = jQuery(this).siblings('span.select2').find('ul')
					var count = jQuery(this).select2('data').length
					if (count == 0) {
						uldiv.html("")
						jQuery(this).siblings('span.select2').find(".select2-search").show();
					} else {
						jQuery(this).siblings('span.select2').find(".select2-search").hide();
						uldiv.html("<li>Valgt (" + count + ")</li>")
					}

					let data = optionss.join(",");
					jQuery(this).parent().find("input").val(data);
				}
			})

		}
		jQuery(document).ready(function() {
			select2funcfield();
			jQuery(document).on("change", "select", function() {
				changeslectfield();
			});
		})
		let incc = 0;
		jQuery(document).on("click", ".add_field_btn", function() {
			let datas = {
				"action": "get_single_row_fields",
				"group_id": "<?php echo $group_id; ?>",
			}
			jQuery.ajax({
				type: "POST",
				url: "<?php echo home_url(); ?>/wp-admin/admin-ajax.php",
				data: datas,
				success: function(resultData) {
					jQuery(".custom_fields_booking").append(resultData);
					jQuery(".custom_fields_booking .inner-advanced_fields").last().find("select").each(function() {
						var namme = jQuery(this).attr("org-name");
						jQuery(this).attr("id", namme + incc);
						incc++;
					})

					select2funcfield();
				}
			});
		})
		jQuery(document).on("focus", ".empty_div", function() {
			jQuery(this).removeClass("empty_div");
		})

		jQuery(document).on("change", ".empty_div", function() {
			jQuery(this).removeClass("empty_div");
		})
		jQuery(document).on("click", ".delete_field_booking", function() {
			jQuery(this).parent().parent().remove();
		})
	</script>

<?php }

function get_single_field($group_id)
{
	$advanced_field = advanced_fields(0, $group_id, 0, array(), 0, true);

	if (!empty($advanced_field)) {
		return row_data($advanced_field);;
	} else {
		return "";
	}
}
