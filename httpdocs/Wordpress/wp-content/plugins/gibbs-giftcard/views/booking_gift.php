<?php
get_header();
$giftcard_data = get_post($_POST["giftcard_id"]);
$current_user = array();
$email = "";
$phone = "";
$first_name = "";
$last_name = "";
if(is_user_logged_in()){
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;
    $phone = $current_user->phone;
    $first_name = $current_user->first_name;
    $last_name =$current_user->last_name;
}


?>
<?php 
if(isset($_POST["iframe"]) && $_POST["iframe"] == "true"){ ?>
<style>
    
    header {
        display: none;
    }
    .form-group {
        flex-direction: column;
    }
</style>


<?php }
?>
<style>
    .get_your_trial{
        display: none;
    }
</style>
<?php
    // Print WooCommerce notices (e.g., your custom success message)
    if (isset($message_data["error"]) && $message_data["error"] != "") { ?>
        <div class="alert alert-warning"><?php echo $message_data["error"];?></div>
    <?php } ?>
<form method="post" action="" id="giftBooking">
    <div class="gift_booking">
        <div class="container">
            <!-- Contact Information Section -->
                <input type="hidden" name="giftcard_id" value="<?php echo $_POST['giftcard_id'];?>">
                <input type="hidden" name="giftcard_amount" value="<?php echo $_POST['giftcard_amount'];?>">
                <div class="contact-info">
                    <h2>Kontaktinformasjon</h2>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="gift_customer_type" value="privat" checked> Privat
                        </label>
                        <label>
                            <input type="radio" name="gift_customer_type" value="bedrift"> Bedrift
                        </label>
                    </div>
                    <form action="your_processing_script.php" method="POST">
                        <div class="form-group">
                            <div class="form-group-half">
                                <label for="gift_email">E-post*</label>
                                <input class="gift_email_class" type="email" id="gift_email" name="gift_email" value="<?php echo $email;?>" required>
                            </div>
                            <div class="form-group-half">
                                <label for="gift_phone">Telefon*</label>
                                <div style="display: flex;position:relative">
                                    <input type="text" id="gift_phonenumber" name="gift_phone" value="<?php echo $phone;?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group-half">
                                <label for="gift_first_name">Fornavn*</label>
                                <input type="text" id="gift_first_name" name="gift_first_name" value="<?php echo $first_name;?>" required>
                            </div>
                            <div class="form-group-half">
                                <label for="gift_last_name">Etternavn*</label>
                                <input type="text" id="gift_last_name" name="gift_last_name" value="<?php echo $last_name;?>" required>
                            </div>
                        </div>
                        <div class="terms">
                            Ved å gå videre godkjenner du <a href="#">vilkår og betingelser</a> for nettstedet.
                        </div>
                        <button type="button" class="submit-gift-booking submit-btn">Gå videre</button>
                    </form>
                </div>
            

            <!-- Summary Section -->
            <div class="summary">
                <h2>Sammendrag</h2>
                <div class="summary-details">
                    <p><span>Gavekort for</span> <span><?php echo $giftcard_data->post_title;?></span></p>
                    <p><span>Gavekort verdi</span> <span><?php echo wc_price($_POST['giftcard_amount']); ?></span></p>
                    <p class="total"><span>Totalsum</span> <span><?php echo wc_price($_POST['giftcard_amount']); ?></span></p>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
add_action("wp_footer", "add_overlay");
function add_overlay(){ ?>
   <div class="overlay" style="display: none;">
        <div class="overlay__inner">
            <div class="overlay__content"><span class="spinner"></span></div>
        </div>
    </div>
<?php }
?>
<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/intlTelInput.js?ver=5.7.3"></script>
<script>

        var phoneLengthMapping = {
            "af": { min: 9, max: 9 },
            "al": { min: 9, max: 9 },
            "dz": { min: 9, max: 9 },
            "ad": { min: 6, max: 6 },
            "ao": { min: 9, max: 9 },
            "ar": { min: 10, max: 10 },
            "au": { min: 9, max: 9 },
            "at": { min: 10, max: 13 },
            "bd": { min: 10, max: 10 },
            "be": { min: 8, max: 9 },
            "br": { min: 10, max: 11 },
            "ca": { min: 10, max: 10 },
            "cn": { min: 11, max: 11 },
            "dk": { min: 8, max: 8 },
            "eg": { min: 10, max: 10 },
            "fi": { min: 7, max: 12 },
            "fr": { min: 9, max: 9 },
            "de": { min: 7, max: 15 },
            "in": { min: 10, max: 10 },
            "id": { min: 9, max: 12 },
            "it": { min: 9, max: 10 },
            "jp": { min: 10, max: 11 },
            "mx": { min: 10, max: 10 },
            "nl": { min: 9, max: 9 },
            "no": { min: 8, max: 8 },
            "pk": { min: 10, max: 10 },
            "pl": { min: 9, max: 9 },
            "ru": { min: 10, max: 10 },
            "sa": { min: 9, max: 9 },
            "za": { min: 9, max: 9 },
            "es": { min: 9, max: 9 },
            "se": { min: 7, max: 13 },
            "ch": { min: 9, max: 9 },
            "tr": { min: 10, max: 10 },
            "gb": { min: 10, max: 10 },
            "us": { min: 10, max: 10 },
            "vn": { min: 9, max: 11 }
        };
    let input_gift_phonenumber = document.querySelector("#gift_phonenumber");
    let iti = null;
    if(input_gift_phonenumber != null){

        var lang_code = "no"
       if(jQuery("html").attr("lang") != undefined){
          var langg = jQuery("html").attr("lang");
          lang_code = langg.split("-")[1];

          if(lang_code != ""){
            lang_code = lang_code.toLowerCase();
          }
       }
        iti = window.intlTelInput(input_gift_phonenumber, {
                initialCountry: lang_code,
                allowExtensions: true,
                formatOnDisplay: true,
                autoFormat: true,
                numberType: "MOBILE",
                preventInvalidNumbers: true,
                separateDialCode: true,
        utilsScript: "<?php echo get_stylesheet_directory_uri();?>/assets/js/utils.js?ver=5.7.2",
        });

            // input_gift_phonenumber.addEventListener("keypress", function (e) {
            //     var countryData = iti.getSelectedCountryData();
            //     var countryCode = countryData.iso2;
            //     var maxLength = phoneLengthMapping[countryCode]?.max || 15; // Default to 15 if no mapping exists

            //     // Get current input value length
            //     var phoneNumber = input_gift_phonenumber.value.replace(/\s/g, ""); // Remove whitespace

            //     if (phoneNumber.length >= maxLength && !e.metaKey && !e.ctrlKey) {
            //         e.preventDefault(); // Prevent further input
            //     }
            // });

            // Validate on input
            input_gift_phonenumber.addEventListener("input", function () {
                var countryData = iti.getSelectedCountryData();
                var countryCode = countryData.iso2;
                var phoneNumber = input_gift_phonenumber.value.replace(/\s/g, ""); // Remove whitespace

                if (isValidPhoneNumber(phoneNumber, countryCode, iti)) {
                    input_gift_phonenumber.setCustomValidity(""); // Valid number
                } else {
                    input_gift_phonenumber.setCustomValidity("Invalid phone number for " + countryData.name);
                }
            });
            input_gift_phonenumber.addEventListener("change", function () {
                input_gift_phonenumber.reportValidity();
            });

            // Utility function to validate phone number length based on country
            function isValidPhoneNumber(phoneNumber, countryCode, itiInstance) {
                // Check if the number is valid based on intlTelInput's isValidNumber()
                if (!itiInstance.isValidNumber()) return false;

                // Get the length requirements from the mapping
                var lengthData = phoneLengthMapping[countryCode];
                if (lengthData) {
                    var nationalNumber = phoneNumber.replace(/\D/g, ""); // Remove non-digit characters
                    return nationalNumber.length >= lengthData.min && nationalNumber.length <= lengthData.max;
                }

                // Default fallback for countries not in the mapping
                return true;
            }
    } 

    // Validation function
    function validateForm() {
        // Clear previous error messages
        jQuery(".error-message").remove();

        let isValid = true;

        // Retrieve form field values
        let email = jQuery("#gift_email");
        let phone = jQuery("#gift_phonenumber");
        let firstName = jQuery("#gift_first_name");
        let lastName = jQuery("#gift_last_name");

        // Email validation pattern
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validate email
        if (!email.val() || !emailPattern.test(email.val())) {
            showError(email, "Please enter a valid email address.");
            isValid = false;
        }

        // Validate phone
        if (!phone.val()) {
            showError(phone, "Please enter a phone number.");
            isValid = false;
        }

        // Validate first name
        if (!firstName.val()) {
            showError(firstName, "Please enter your first name.");
            isValid = false;
        }

        // Validate last name
        if (!lastName.val()) {
            showError(lastName, "Please enter your last name.");
            isValid = false;
        }

        return isValid;
    }

    // Display error message and focus on input
    function showError(inputField, message) {
        inputField.after(`<span class="error-message" style="color: red;">${message}</span>`);
        if (inputField.is(":focusable")) {
            inputField.focus();
        }
    }

    jQuery(".submit-gift-booking").click(function() {
        // Run form validation
        if (!validateForm()) {
            return; // Prevent form submission if validation fails
        }
        var phone_val = jQuery("#giftBooking").find("#gift_phonenumber").val();
        

        // Proceed with original submission logic if validation passes
        if (iti) {
                var country_code = iti.getSelectedCountryData().dialCode;
                var countryCode = iti.getSelectedCountryData().iso2;
                var maxLength = phoneLengthMapping[countryCode]?.max || 15; // Default to 15 if no mapping exists

                // Get current input value length
                var phoneNumber = phone_val.replace(/\s/g, ""); // Remove whitespace

                if (phoneNumber.length > maxLength) {
					input_gift_phonenumber.setCustomValidity("Incorrect number of digit only allow " + maxLength +" digit");
					input_gift_phonenumber.reportValidity();
                    return;
                }else{
                   input_gift_phonenumber.setCustomValidity("");
                }
        } else {
            var country_code = "";
        }

        if(country_code && country_code != ""){

            

            var allow_phone = jQuery("#giftBooking").find(".allow_phone").val();

            if (phone_val.startsWith(country_code) && allow_phone != "true") {
                // Show warning if phone number starts with the dial code
                var phoneField = jQuery("#giftBooking").find("#gift_phonenumber");
                if (phoneField.siblings('.phone-warning').length === 0) {
                    phoneField.focus();
                    phoneField.parent().after(`
                        <div class="phone-warning" style="margin-top: 54px;position:absolute;padding: 10px;font-weight: 600;background-color: #FFF8DD;border-radius: 5px;color: #333;display: flex;justify-content: space-between;align-items: center;">
                            Er nummeret riktig?
                            <button class="btn btn-warning btn-sm close-warning warning_phone" style="background:#008474;color:#fff;font-size: 14px;">Ja, det er riktig</button>
                        </div>
                    `);
                }
                return;
            }
        }

        jQuery("#giftBooking").append("<input type='hidden' name='save_gift_booking' value='true'>");
        jQuery("#giftBooking").append("<input type='hidden' name='gift_booking' value='true'>");
        jQuery("#giftBooking").append("<input type='hidden' name='country_code' value='" + country_code + "'>");
        jQuery("#giftBooking").submit();
    });
    jQuery(document).on("click","#giftBooking .warning_phone",function(){

        jQuery("#giftBooking").append("<input type='hidden' class='allow_phone' value='true'>");

        jQuery(".phone-warning").remove();

    })

    jQuery(".gift_email_class").change(function(){
        if(this.value != ""){
            jQuery(".overlay").show();
            ajax_data = {
                'action': 'email_user_data',
                'email': this.value,
            };
            jQuery.ajax({
                type: 'POST',
                url: listeo.ajaxurl,
                data: ajax_data,
                success: function(response) {
                        jQuery(".overlay").hide();
                    if(response.success){
                        iti.destroy();
                        let datats = response.data;
                        let phone = datats.phone;

                        if(datats.country_code && datats.country_code != ""){
                            phone = datats.country_code + datats.phone;
                        }
                        jQuery("#gift_phonenumber").val(phone);
                        iti._init();

                        jQuery("input[name=gift_first_name]").val(datats.first_name);
                        jQuery("input[name=gift_last_name]").val(datats.last_name);
                    }
                }
            });
        }
    });
</script>
<?php
get_footer();
?>
