<?php
global $wpdb;

$current_user = wp_get_current_user();

// Table name


// Fetch listing data
$listing_data = get_post($_POST["listing_id"]);

$sms_content = $_POST["sms_content"];

$sms_content = str_replace(["\r\n", "\n", "\r"], '\\n', $sms_content);

$ptn_users_groups = $wpdb->prefix . 'users_groups';

$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

$row = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM $ptn_users_groups WHERE id = %d", // %d is the placeholder for integers
        $active_group_id
    )
);

$owner_id = "";

if ($row && isset($row->id)) {

    if(isset($row->superadmin) && $row->superadmin != ""){
        $owner_id = $row->superadmin;
    }else{
        $owner_id = $row->group_admin;
    }

   
}


// Prepare data array
$data = [
    'provider' => sanitize_text_field($_POST["provider"]),
    'listing_id' => intval($_POST["listing_id"]),
    'listing_name' => sanitize_text_field($listing_data->post_title),
    'timezone' => "Europe/Oslo",
    'lock_id' => isset($_POST["lock_id"]) ? sanitize_text_field($_POST["lock_id"]) : "",
    'jwt' => isset($_POST["jwt"]) ? sanitize_text_field($_POST["jwt"]) : "",
    'server_address' => isset($_POST["server_address"]) ? sanitize_text_field($_POST["server_address"]) : "",
    'type' => isset($_POST["type"]) ? sanitize_text_field($_POST["type"]) : "",
    'timezone_add_time_before' => isset($_POST["timezone_add_time_before"]) ? intval($_POST["timezone_add_time_before"]) : 0,
    'timezone_add_time_after' => isset($_POST["timezone_add_time_after"]) ? intval($_POST["timezone_add_time_after"]) : 0,
    'sms_content' => isset($sms_content) ? $sms_content : "",
    'sms_time' => isset($_POST["sms_time"]) ? intval($_POST["sms_time"]) : 0,
    'project_id' => isset($_POST["project_id"]) ? sanitize_text_field($_POST["project_id"]) : "",
];

if($owner_id != ""){
    $data["owner_id"] = $owner_id;
}


$table_name = $wpdb->prefix . 'access_management_match';

// Check if it's an edit or insert operation
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Update operation
    $id = intval($_POST["id"]);
    $updated = $wpdb->update(
        $table_name,
        $data, // Data to update
        ['id' => $id], // Where clause
        array_fill(0, count($data), '%s'), // Data format
        ['%d'] // Where clause format
    );

    if ($updated !== false) {
        set_transient('flash_message', 'Lagret!', 30);
        set_transient('flash_type', 'success', 30);
    } else {
        set_transient('flash_message', 'Feilet', 30);
        set_transient('flash_type', 'danger', 30);
    }
} else {
    // Insert operation
    $inserted = $wpdb->insert(
        $table_name,
        $data,
        array_fill(0, count($data), '%s') // Data format
    );

    if ($inserted) {
        set_transient('flash_message', 'Lagret!', 30);
        set_transient('flash_type', 'success', 30);
    } else {
        set_transient('flash_message', 'Feilet', 30);
        set_transient('flash_type', 'danger', 30);
    }
}

// Redirect to prevent form resubmission
$referer_url = $_SERVER['HTTP_REFERER'];

// Parse the URL to extract its components
$parsed_url = parse_url($referer_url);

// Rebuild the URL without the query string and append 'success=true'
$base_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'];

// Redirect with the new query string
wp_redirect($base_url . '?success=true');
exit; // Always exit after wp_redirect to ensure the redirect happens
?>
