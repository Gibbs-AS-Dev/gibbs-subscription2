<?php
$group_admin = get_group_admin();
if ($group_admin == "") {
    $group_admin = get_current_user_ID();
}

$user_data = get_userdata($group_admin);

if (isset($_POST["action"]) && $_POST["action"] == "access_form") {
    require(__DIR__ . "/postAccess.php");
}
$message = get_transient('flash_message');

if (isset($_GET["edit"])) {
    $id = intval($_GET["edit"]); // Sanitize the edit ID
    global $wpdb;

    // Fetch data for the given ID
    $edit_data = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}access_management_match WHERE id = %d", $id),
        ARRAY_A
    );
} else {
    $edit_data = []; // Default to an empty array if not editing
}
?>

<div class="access_form">
    <?php if ($message) {
        $type = get_transient('flash_type') ?? 'info';
        ?>
        <div class="alert alert-<?php echo $type; ?>" role="alert">
            <?php echo $message; ?>
        </div>     
    <?php 
        delete_transient('flash_message'); 
        delete_transient('flash_type');
    } ?> 
    <form action="" method="post" class="">
        <input type="hidden" value="access_form" name="action">
        <?php if (isset($edit_data['id'])): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($edit_data['id']); ?>">
        <?php endif; ?>
        <div class="form-field-provider">
            <label for="provider">Leverandør:</label>
            <select id="provider" name="provider" class="dropdown" required>
                <option value="">Velg leverandør</option>
                <option value="locky" <?php selected($edit_data['provider'] ?? '', 'locky'); ?>>locky tech</option>
                <option value="shelly" <?php selected($edit_data['provider'] ?? '', 'shelly'); ?>>Shelly</option>
                <option value="unloc" <?php selected($edit_data['provider'] ?? '', 'unloc'); ?>>Unloc</option>
                <option value="igloohome" <?php selected($edit_data['provider'] ?? '', 'igloohome'); ?>>Igloohome</option>
            </select>
        </div>
        <div class="form-field" data-field-provider="locky,shelly,unloc,igloohome">
            <label for="listing_id">Annonse:</label>
            <select id="listing_id" name="listing_id" data-name="listing_id" class="dropdown" required>
                <option value="">Velg annonse </option>
                <?php 
                $args = array(
                    'author' => $user_data->ID,
                    'posts_per_page' => -1,
                    'post_type' => 'listing',
                    'post_status' => array('publish', 'draft')
                ); 

                $posts = get_posts($args);
                foreach ($posts as $post): ?>
                    <option value="<?php echo esc_attr($post->ID); ?>" <?php selected($edit_data['listing_id'] ?? '', $post->ID); ?>>
                        <?php echo esc_html($post->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>    
        <div class="form-field" data-field-provider="locky,shelly,unloc,igloohome" required>
            <label for="lock_id">Enhets ID:</label>
            <input type="text" id="lock_id" name="lock_id" data-name="lock_id" class="text_field" placeholder="Skriv her" value="<?php echo esc_attr($edit_data['lock_id'] ?? ''); ?>">
        </div>
        <div class="form-field" data-field-provider="locky,shelly,unloc">
            <label for="jwt">Token:</label>
            <input type="text" id="jwt" name="jwt" data-name="jwt" class="text_field" placeholder="Skriv her" value="<?php echo esc_attr($edit_data['jwt'] ?? ''); ?>">
        </div>
    
        <div class="form-field" data-field-provider="locky,shelly,unloc">
            <label for="server_address">Server adresse:</label>
            <input type="text" id="server_address" name="server_address" data-name="server_address" class="text_field" placeholder="Skriv her" value="<?php echo esc_attr($edit_data['server_address'] ?? ''); ?>">
        </div> 
        <div class="form-field" data-field-provider="locky">
            <label for="type">Åpningsmetode:</label>
            <select id="type" name="type" data-name="type" class="dropdown">
                <option value="">Velg</option>
                <option value="code" <?php selected($edit_data['type'] ?? '', 'code'); ?>>Kode</option>
                <option value="forcedOpen" <?php selected($edit_data['type'] ?? '', 'forcedOpen'); ?>>Stå åpen</option>
            </select>
        </div> 
        <div class="form-field" data-field-provider="locky,shelly,unloc,igloohome">
            <label for="timezone_add_time_before">Buffertid før booking:</label>
            <select id="timezone_add_time_before" name="timezone_add_time_before" data-name="timezone_add_time_before" class="dropdown">
    <?php 
    for ($i = 0; $i <= 300; $i += 30): ?>
        <option value="<?php echo $i; ?>" <?php selected($edit_data['timezone_add_time_before'] ?? '', $i); ?>>
            <?php echo $i . ' min'; ?>
        </option>
    <?php endfor; ?>
</select>

        </div>   
        <div class="form-field" data-field-provider="locky,shelly,unloc,igloohome">
            <label for="timezone_after">Buffertid etter booking:</label>
            <select id="timezone_after" name="timezone_add_time_after" data-name="timezone_add_time_after" class="dropdown">
        <?php 
        for ($i = 0; $i <= 300; $i += 30): ?>
            <option value="<?php echo $i; ?>" <?php selected($edit_data['timezone_add_time_after'] ?? '', $i); ?>>
                <?php echo $i . ' min'; ?>
            </option>
        <?php endfor; ?>
            </select>

        </div>    
        <div class="form-field" data-field-provider="locky,shelly,unloc,igloohome">
            <label for="sms_content">Epost/SMS - Mulige tags {time} {access_code}:</label>
            <?php
            // Fetch SMS content from the edit data or default to an empty string
            $sms_content = $edit_data['sms_content'] ?? '';

            // Replace escaped `\n` with actual newline characters
            $sms_content = str_replace(['\\n', '\n'], "\n", $sms_content);

            // Output the content inside a textarea
            ?>
            <textarea id="sms_content" name="sms_content" data-name="sms_content" class="text_field" placeholder="Skriv innhold her.." rows="6"><?php echo esc_textarea($sms_content); ?></textarea>
        </div>
        <div class="form-field" data-field-provider="locky,shelly,unloc,igloohome">
            <label for="sms_time"> Utsendingstid for epost/SMS:</label>
            <select id="sms_time" name="sms_time" data-name="sms_time" class="dropdown">
    <?php 
    for ($i = 0; $i <= 300; $i += 30): 
        // Determine the label for each option
        $label = $i === 0 ? 'Umiddelbart etter bestilling' : "$i min før booking starter";
    ?>
        <option value="<?php echo $i; ?>" <?php selected($edit_data['sms_time'] ?? '', $i); ?>>
            <?php echo $label; ?>
        </option>
    <?php endfor; ?>
</select>

        </div>
        <div class="form-field" data-field-provider="unloc">
            <label for="project_id">Prosjekt ID:</label>
            <input type="text" id="project_id" name="project_id" data-name="project_id" class="text_field" placeholder="Skriv her" value="<?php echo esc_attr($edit_data['project_id'] ?? ''); ?>">
        </div>

        <button type="submit" class="submit_button btn btn-primary">Lagre</button>
    </form>
</div>

<script>
jQuery(document).ready(function ($) {
    // Listen for changes on the provider select box
    $('#provider').on('change', function () {
        var selectedProvider = $(this).val(); // Get the selected provider value

        // Iterate over each form field with the data-field-provider attribute
        $('.form-field').each(function () {
            var fieldProviders = $(this).data('field-provider');

            if (fieldProviders) {
                // Convert the comma-separated list to an array
                fieldProviders = fieldProviders.split(',');

                // Check if the selected provider is in the array
                if (fieldProviders.includes(selectedProvider)) {
                    $(this).show(); // Show the field if the provider matches
                    $(this).find('[name_empty]').each(function () {
                        $(this).attr('name', $(this).attr('name_empty')); // Restore name
                        $(this).removeAttr('name_empty');
                    });
                } else {
                    $(this).hide(); // Hide the field if the provider does not match
                    $(this).find('[name]').each(function () {
                        $(this).attr('name_empty', $(this).attr('name')); // Save name in name_empty
                        $(this).removeAttr('name'); // Remove the name attribute
                    });
                }
            } else {
                // Show the field if no data-field-provider attribute is defined
                $(this).show();
                $(this).find('[name_empty]').each(function () {
                    $(this).attr('name', $(this).attr('name_empty')); // Restore name
                    $(this).removeAttr('name_empty');
                });
            }
        });
    });

    // Trigger change on page load to set the initial state
    $('#provider').trigger('change');
});

</script>