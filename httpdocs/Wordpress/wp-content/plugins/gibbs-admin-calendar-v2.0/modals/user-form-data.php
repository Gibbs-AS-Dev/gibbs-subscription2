<?php
  $profile_type = get_user_meta($current_user->ID, 'profile_type',true);	
?>
<div class="form-container">
    <div class="radio-group">
        <label>
            <input type="radio" name="customer-type" <?php if($profile_type == "personal" || $profile_type == ""){ echo "checked";}?> readonly>
            Privatperson
        </label>
        <label>
            <input type="radio" name="customer-type" <?php if($profile_type == "company"){ echo "checked";}?> readonly>
            Organisasjon/bedrift
        </label>
    </div>
    <form>
        <div class="form-row">
            <div class="form-field">
                <label for="first-name">Fornavn</label>
                <input type="text" id="first-name" placeholder="" value="<?php the_author_meta( 'first_name', $userdata->ID ); ?>" readonly>
            </div>
            <div class="form-field">
                <label for="last-name">Etternavn</label>
                <input type="text" id="last-name" placeholder="Hansen" value="<?php the_author_meta( 'last_name', $userdata->ID ); ?>" readonly>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label for="phone">Tlf *</label>
                <div class="phone-field">
                    <input type="text" id="booking_phone"  value="<?php the_author_meta( 'country_code', $userdata->ID ); ?><?php the_author_meta( 'phone', $userdata->ID ); ?>" readonly>
                </div>
            </div>
            <div class="form-field">
                <label for="email">E-post</label>
                <input type="email" id="email"  value="<?php the_author_meta( 'user_email', $userdata->ID ); ?>" readonly>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label for="address">Faktura adresse</label>
                <input type="text" id="address" value="<?php the_author_meta( 'billing_address_1', $userdata->ID ); ?>" readonly>
            </div>
            <div class="form-field">
                <label for="city">Poststed</label>
                <input type="text" id="city" value="<?php the_author_meta( 'billing_city', $userdata->ID ); ?>" readonly>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label for="zip-code">Post nr</label>
                <input type="text" id="zip-code" value="<?php the_author_meta( 'billing_postcode', $userdata->ID ); ?>" readonly>
            </div>
        </div>
        
    </form>
</div>

<script>

    window.booking_phone_init = function() {

        let input_booking_phone = document.querySelector("#booking_phone");

        let iti = null;
        if(input_booking_phone != null){

            var lang_code = "no"
            if(jQuery("html").attr("lang") != undefined){
                var langg = jQuery("html").attr("lang");
                lang_code = langg.split("-")[1];

                if(lang_code != ""){
                    lang_code = lang_code.toLowerCase();
                }
            }
            iti = window.intlTelInput(input_booking_phone, {
                    initialCountry: lang_code,
                    allowExtensions: true,
                    formatOnDisplay: true,
                    autoFormat: true,
                    numberType: "MOBILE",
                    preventInvalidNumbers: true,
                    separateDialCode: true,
            utilsScript: "<?php echo get_stylesheet_directory_uri();?>/assets/js/utils.js?ver=5.7.2",
            });

            
        } 

    }

</script>