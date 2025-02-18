<?php
if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}
$group_admin = get_group_admin();
if($group_admin == ""){
	$group_admin = get_current_user_ID();
}

$user_data = get_userdata($group_admin);
$edit_mode = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$giftcard = null;
$listing_ids_array = array();

if ($edit_mode) {
    // Fetch the gift card post for editing
    $giftcard = get_post($edit_mode);

    if ($giftcard && $giftcard->post_type === 'giftcard' && $giftcard->post_author == $user_data->ID) {
        // Populate fields if this is an existing gift card
        $title = $giftcard->post_title;
        $description = apply_filters('the_content', $giftcard->post_content); // Ensuring HTML content displays correctly
        $listing_ids_array = get_post_meta($giftcard->ID, 'listing_ids', true) ?: [];
        $giftcard_description = get_post_meta($giftcard->ID, 'giftcard_description', true) ?: "";
        $min_amount = get_post_meta($giftcard->ID, 'min_amount', true) ?: "";
    } else {
        $giftcard = null; // Reset if invalid post
        $giftcard_description = "";
        $min_amount = "";
    }
}
?>

<div class="giftcard_create">
    <!-- Alert Message Container -->
    <div id="giftcardMessage" style="display: none; padding: 10px; margin-bottom: 15px;"></div>

    <!-- General Settings Card -->
    <form method="post" action="<?php echo esc_url(admin_url("admin-ajax.php")); ?>" id="giftcardForm">
        <input type="hidden" name="action" value="save_giftcard">
        
        <?php if ($edit_mode && $giftcard): ?>
            <input type="hidden" name="post_id" value="<?php echo esc_attr($edit_mode); ?>">
        <?php endif; ?>

        <div class="card">
           <!--  <h2>Generelt</h2> -->
            <label for="title">Gavekort tittel</label>
            <input type="text" id="title" name="title" value="<?php echo isset($title) ? esc_attr($title) : ''; ?>" required>
            <label for="description">Gavekort beskrivelse</label>
            <textarea id="description" name="description" rows="5" required><?php echo isset($description) ? esc_textarea($description) : ''; ?></textarea>

            <label for="giftcard_description">Instrukser/gratulasjon etter kjøp</label>
            <textarea id="giftcard_description" name="giftcard_description" rows="3" required><?php echo isset($giftcard_description) ? esc_textarea($giftcard_description) : ''; ?></textarea>
          

            <!-- Listing Selection Card with Multi-select Dropdown and Toggle Button -->
    
            <div class="listing_divvv d-flex">
                <label for="listings">Velg hvor gavekortet er gyldig (Obligatorisk)</label>
                <button type="button" id="toggleSelectAll" class="btn btn-secondary" style="margin-bottom: 10px;">Velg alle</button>
            </div>
            <select class="select2-single" multiple name="listing_ids[]" 
                data-placeholder="<?php esc_html_e('Søk', 'listeo_core'); ?>" 
                id="listingSelect" required tabindex="-1" aria-hidden="true" required>

                <?php 
                $args = array(
                    'author' => $user_data->ID,
                    'posts_per_page' => -1,
                    'post_type' => 'listing',
                    'post_status' => array('publish', 'draft')
                ); 
                
                $posts = get_posts($args);
                foreach ($posts as $post) : ?>
                    <option <?php if (in_array($post->ID, $listing_ids_array)) echo "selected"; ?> value="<?php echo esc_attr($post->ID); ?>">
                        <?php echo esc_html($post->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br />
            <label for="min_amount">Sett minimumsbeløpet for gavekortkjøp </label>
            <input type="number" id="min_amount" name="min_amount" value="<?php echo isset($min_amount) ? esc_attr($min_amount) : ''; ?>">
            <br />
           
        </div>

        <!-- Buttons Card with Spinner -->
        <div class=" buttons d-flex">
            <button class="save_activate save_gift_btn btn btn-primary" type="button" data-val="publish">
                <span class="spinner2" style="display: none;"></span> <?php echo $edit_mode ? 'Lagre og aktiver' : 'Lagre og aktiver'; ?>
            </button>
            <button class="save_deactivate save_gift_btn btn btn-secondary" type="button" data-val="draft">
                <span class="spinner2" style="display: none;"></span> <?php echo $edit_mode ? 'Lagre og deaktiver' : 'Lagre som utkast'; ?>
            </button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize Select2
    $("#listingSelect").select2();


    // Toggle select/deselect all listings with class toggle on button
    let allSelected = false;
    $("#toggleSelectAll").click(function() {
        allSelected = !allSelected;
        $("#listingSelect option").prop("selected", allSelected);
        $("#listingSelect").trigger("change.select2"); // Refresh Select2 display
        
        // Toggle class and update button text based on selection state
        $(this).toggleClass("active", allSelected);
        $(this).text(allSelected ? "Fjern alt" : "Velg alt");
    });

    // Form validation to ensure at least one listing is selected
    $("#giftcardForm").submit(function(e) {
        var selectedListings = $("#listingSelect").val();
        if (!selectedListings || selectedListings.length === 0) {
            e.preventDefault();
            alert("Vennligst velg minst en annonse.");
            return false;
        }
    });

    // Gift card button functionality (unchanged from your original code)
    $(".save_gift_btn").click(function() {
        if (typeof tinyMCE !== "undefined" && tinyMCE.get("description")) {
            tinyMCE.get("description").save();
        }

        $(".save_gift_btn").prop("disabled", true);
        $(this).find(".spinner2").show();

        var gift_status = $(this).attr("data-val");
        $(".gift_status").remove();
        $("#giftcardForm").append("<input type='hidden' class='gift_status' name='gift_status' value='" + gift_status + "'>");

        $("#giftcardMessage").hide().removeClass("error success");

        $.ajax({
            url: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
            type: "POST",
            data: $("#giftcardForm").serialize(),
            success: function(response) {
                if(response.success) {
                    $("#giftcardMessage").addClass("success").html(response.data.message).show();
                    setTimeout(function() {
                        window.location.href = "<?php echo get_permalink($page_id);?>";
                    }, 2000);
                } else {
                    $("#giftcardMessage").addClass("error").html(response.data.errors.join("<br>")).show();
                }
            },
            error: function() {
                $("#giftcardMessage").addClass("error").html("An error occurred while processing the request.").show();
            },
            complete: function() {
                $(".save_gift_btn").prop("disabled", false).find(".spinner2").hide();
            }
        });
    });
});
</script>
