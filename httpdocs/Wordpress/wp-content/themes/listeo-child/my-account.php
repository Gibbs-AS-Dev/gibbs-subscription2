<?php


/* Get user info. */
global $wp_roles;
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift( $roles );

if($_GET['registered']){
 
 $redlink =  get_user_meta( $current_user->ID, 'user_reg_from_link',true);
 wp_redirect($redlink);
 exit();
 
}

if ( isset( $_POST['billing_address_1'] ) ){
		        update_user_meta($current_user->ID, 'billing_address_1', esc_attr( $_POST['billing_address_1'] ) );
		    }
		    if ( isset( $_POST['billing_address_2'] ) ){
		        update_user_meta($current_user->ID, 'billing_address_2', esc_attr( $_POST['billing_address_2'] ) );
		    }
		    if ( isset( $_POST['billing_city'] ) ){
		        update_user_meta($current_user->ID, 'billing_city', esc_attr( $_POST['billing_city'] ) );
		    }
		    if ( isset( $_POST['billing_postcode'] ) ){
		        update_user_meta($current_user->ID, 'billing_postcode', esc_attr( $_POST['billing_postcode'] ) );
		    }

$profile_type = get_user_meta($current_user->ID, 'profile_type',true);	 
if ( isset($_GET['updated']) && $_GET['updated'] == 'true' ) : ?>
	<div class="notification success closeable margin-bottom-35"><p><?php esc_html_e('Your profile has been updated.', 'listeo_core'); ?></p><a class="close" href="#"></a></div>
<?php endif; ?>


<?php if ( !is_user_logged_in() ) : ?>
    <p class="warning">
        <?php esc_html_e('You must be logged in to edit your profile.', 'listeo_core'); ?>
    </p><!-- .warning -->
<?php else : ?>

<div class="row">

		<!-- Profile -->
		<div class="col-lg-12 col-md-12">
			    <form method="post" id="edit_user" action="<?php the_permalink(); ?>">
			    <div class="dashboard-list-box margin-top-0">
				    <h4 class="gray"><?php esc_html_e('Profile Details','listeo_core') ?></h4>
				
				    <div class="dashboard-list-box-static">

							<?php
							$custom_avatar = $current_user->listeo_core_avatar_id;
							$custom_avatar = wp_get_attachment_url($custom_avatar);
							?>
							<div <?php if(!empty($custom_avatar)) { ?> 
								data-photo="<?php echo $custom_avatar; ?>"
							    data-name="<?php esc_html_e('Your Avatar', 'listeo_core'); ?>"
							   data-size="<?php echo filesize( get_attached_file( $current_user->listeo_core_avatar_id ) ); ?>" <?php } ?>
							  class="edit-profile-photo">

								<div id="avatar-uploader" class="dropzone">
									<div class="dz-message" data-dz-message><span><?php esc_html_e('Upload Avatar', 'listeo_core'); ?></span></div>
								</div>
								<input class="hidden" name="listeo_core_avatar_id" type="text" id="avatar-uploader-id" value="<?php echo $current_user->listeo_core_avatar_id; ?>" />
							</div>

					<!-- Details -->
						<div class="my-profile ww">

									<?php if(get_option('listeo_profile_allow_role_change')): ?>
										<?php if(in_array($role, array('owner','guest'))): ?>
											<label for="role"><?php esc_html_e('Change your role', 'listeo_core'); ?></label>
											<select name="role" id="role">
												<option <?php selected($role,'guest'); ?> value="guest"><?php esc_html_e('Guest','listeo_core') ?></option>
												<option <?php selected($role,'owner'); ?> value="owner"><?php esc_html_e('Owner','listeo_core') ?></option>
											</select>
										<?php endif; ?>
									<?php endif; ?>

									<div class="main-cotrol">
										<div class="form-cotrol-div">
											 <input type="radio" id="personal" name="profile_type" value="personal" <?php if($profile_type == "personal" || $profile_type == ""){ echo "checked";}?>>
										    <label for="personal">Privatperson</label><br>
										</div>
			                            <div class="form-cotrol-div">
											 <input type="radio" id="company" name="profile_type" value="company" <?php if($profile_type == "company"){ echo "checked";}?>>
											 <label for="company">Organisasjon/bedrift</label>
										</div>
										
									</div>
									  

								  	<label for="first-name" class="for_company" <?php if($profile_type == "personal" || $profile_type == ""){ ?> style="display: none" <?php  } ?> ><?php esc_html_e('Company First Name', 'listeo_core'); ?></label>
								  	<label for="first-name" class="for_personal" <?php if($profile_type == "company"){ ?> style="display: none" <?php  } ?> ><?php esc_html_e('First Name', 'listeo_core'); ?></label>
					                <input class="text-input" name="first-name" type="text" id="first-name" value="<?php  echo $current_user->user_firstname; ?>" />

					                
					                	<div class="last_name_div" <?php if($profile_type == "company"){ ?> style="display: none" <?php  } ?>    >

										  	<label for="last-name"><?php esc_html_e('Last Name', 'listeo_core'); ?></label>
							                <input class="text-input" name="last-name" type="text" id="last-name" value="<?php echo $current_user->user_lastname; ?>" />
						                </div>
										<div class="company_div" <?php if($profile_type != "company"){ ?> style="display: none" <?php  } ?>    >
										<label for="company_number"><?php esc_html_e('Company Number', 'listeo_core'); ?></label>
			                            <input class="text-input" name="company_number" type="text" id="company_number" value="<?php the_author_meta( 'company_number', $current_user->ID ); ?>" />
			                        </div>   
						            

									<label for="for_company" class="for_company" <?php if($profile_type == "personal"  || $profile_type == ""){ ?> style="display: none" <?php  } ?> ><?php esc_html_e('Company Phone', 'listeo_core'); ?></label>
									<label for="for_personal" class="for_personal" <?php if($profile_type == "company"){ ?> style="display: none" <?php  } ?> ><?php esc_html_e('Phone', 'listeo_core'); ?></label>
									<input class="text-input" name="phone" type="text" id="pphone" value="<?php echo $current_user->country_code;?><?php echo $current_user->phone; ?>" type="text">
									<input type="hidden" id="country_code" name="country_code" value="<?php echo $current_user->country_code;?>">

									<?php  if ( isset($_GET['user_err_pass']) && !empty($_GET['user_err_pass'])  ) : ?>
									<div class="notification error closeable margin-top-35"><p>
										<?php
										switch ($_GET['user_err_pass']) {
										 	case 'error_1':
										 		echo esc_html_e('The Email you entered is not valid or empty. Please try again.','listeo_core');
										 		break;
										 	case 'error_2':
										 		echo esc_html_e('This email is already used by another user, please try a different one.','listeo_core');
										 		break;


										 	default:
										 		# code...
										 		break;
										 }  ?>

										</p><a class="close" href="#"></a>
									</div>
									<?php endif; ?>
									<label for="email" class="for_company" <?php if($profile_type == "personal"  || $profile_type == ""){ ?> style="display: none" <?php  } ?>><?php esc_html_e('Company E-mail', 'listeo_core'); ?></label>
									<label for="email" class="for_personal" <?php if($profile_type == "company"){ ?> style="display: none" <?php  } ?>><?php esc_html_e('E-mail', 'listeo_core'); ?></label>
					                <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />

                                    
						                 
									<label for="description"  class="for_company" <?php if($profile_type == "personal" || $profile_type == ""){ ?> style="display: none" <?php  } ?>><?php esc_html_e('Company About me', 'listeo_core'); ?></label>
									<label for="description"  class="for_personal" <?php if($profile_type == "company"){ ?> style="display: none" <?php  } ?>><?php esc_html_e('About me', 'listeo_core'); ?></label>
					               	<?php
										$user_desc = get_the_author_meta( 'description' , $current_user->ID);
										$user_desc_stripped = strip_tags($user_desc, '<p>'); //replace <p> and <a> with whatever tags you want to keep after the strip
									?>
					                <textarea name="description" id="description" cols="30" rows="10"><?php echo $user_desc_stripped; ?></textarea>

									<input type="hidden" name="my-account-submission" value="1" />

								
						</div>
					</div>
				</div>
				<div class="dashboard-list-box margin-top-0">
				    <h4 class="gray"><?php esc_html_e('Customer billing address','listeo_core') ?></h4>
				
				    <div class="dashboard-list-box-static">

							
					<!-- Details -->
						<div class="my-profile ww">
                                       

								  	<label for="first-name"><?php esc_html_e('First Name', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_first_name" type="text" id="billing_first_name" value="<?php the_author_meta( 'billing_first_name', $current_user->ID ); ?>" />

								  	<label for="last-name"><?php esc_html_e('Last Name', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_last_name" type="text" id="billing_last_name" value="<?php the_author_meta( 'billing_last_name', $current_user->ID ); ?>" />

									<label for="phone"><?php esc_html_e('Phone', 'listeo_core'); ?></label>
									<input class="text-input" name="billing_phone" type="text" id="billing_phone" value="<?php the_author_meta( 'billing_phone', $current_user->ID ); ?>" type="text">

									<label for="email"><?php esc_html_e('E-mail', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_email" type="text" id="billing_email" value="<?php the_author_meta( 'billing_email', $current_user->ID ); ?>" />

					                <label for="email"><?php esc_html_e('Company', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_company" type="text" id="billing_company" value="<?php the_author_meta( 'billing_company', $current_user->ID ); ?>" />

		                            <label for="address"><?php esc_html_e('Address 1', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_address_1" type="text" id="billing_address_1" value="<?php the_author_meta( 'billing_address_1', $current_user->ID ); ?>" />
					                 
									<label for="address"><?php esc_html_e('Address 2', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_address_2" type="text" id="billing_address_2" value="<?php the_author_meta( 'billing_address_2', $current_user->ID ); ?>" />
					                 
									<label for="address"><?php esc_html_e('City', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_city" type="text" id="billing_city" value="<?php the_author_meta( 'billing_city', $current_user->ID ); ?>" />
					                 
									<label for="address"><?php esc_html_e('Postcode', 'listeo_core'); ?></label>
					                <input class="text-input" name="billing_postcode" type="text" id="billing_postcode" value="<?php the_author_meta( 'billing_postcode', $current_user->ID ); ?>" />
								

									<button type="submit" form="edit_user" value="<?php esc_html_e( 'Submit', 'listeo_core' ); ?>" class="button margin-top-20 margin-bottom-20"><?php esc_html_e('Lagre profil endringer', 'listeo_core'); ?></button>

						</div>
					</div>
				</div>
			    </form>
			</div>

			<?php if ( class_exists( 'plugin_delete_me' ) ) : ?>
				<div class="col-lg-12 col-md-12 delete-account-section margin-top-40">
					<div class="dashboard-list-box margin-top-0">
						<h4 class="gray"><?php esc_html_e('Delete Your Account','listeo_core') ?></h4>
						<div class="dashboard-list-box-static">
							<?php echo do_shortcode( '[plugin_delete_me /]' ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

		</div>
<?php endif; ?>		
<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/intlTelInput.js?ver=5.7.2"></script>
<script type="text/javascript">
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

	jQuery("input[name=profile_type]").change(function(){
		if(this.value == "company"){
           jQuery(".last_name_div").hide();
           jQuery(".company_div").show();
           jQuery(".for_company").show();
           jQuery(".for_personal").hide();
		}else{
			jQuery(".last_name_div").show();
			jQuery(".company_div").hide();
			jQuery(".for_company").hide();
            jQuery(".for_personal").show();

		}
	})

	var input = document.querySelector("#pphone");
     if(input != null){
        var iti = window.intlTelInput(input, {
                initialCountry: "no",
			    allowExtensions: true,
                formatOnDisplay: true,
                autoFormat: true,
                numberType: "MOBILE",
                preventInvalidNumbers: true,
                separateDialCode: true,
        utilsScript: "<?php echo get_stylesheet_directory_uri();?>/assets/js/utils.js?ver=5.7.2",
        });

		// input.addEventListener("keypress", function (e) {
        //         var countryData = iti.getSelectedCountryData();
        //         var countryCode = countryData.iso2;
        //         var maxLength = phoneLengthMapping[countryCode]?.max || 15; // Default to 15 if no mapping exists

        //         // Get current input value length
        //         var phoneNumber = input.value.replace(/\s/g, ""); // Remove whitespace

        //         if (phoneNumber.length >= maxLength && !e.metaKey && !e.ctrlKey) {
		// 			input.setCustomValidity("Incorrect number of digit only allow " + maxLength);
		// 			input.reportValidity();
        //         }else{
		// 			input.setCustomValidity("");
		// 		}
        //     });

            // Validate on input
            input.addEventListener("input", function () {
                var countryData = iti.getSelectedCountryData();
                var countryCode = countryData.iso2;
                var phoneNumber = input.value.replace(/\s/g, ""); // Remove whitespace

                if (isValidPhoneNumber(phoneNumber, countryCode, iti)) {
                    input.setCustomValidity(""); // Valid number
                } else {
                    input.setCustomValidity("Invalid phone number for " + countryData.name);
                }
            });
            input.addEventListener("change", function () {
                input.reportValidity();
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


		jQuery("#pphone").keyup(function(){
			
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},500)
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},50)
		})
		jQuery("#pphone").on("countrychange", function () {
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},500)
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},50)
		});
		jQuery("#edit_user").submit(function(e){
			let pnumber = iti.getSelectedCountryData();
			let country_code = "+"+pnumber["dialCode"];
			jQuery("#country_code").val(country_code)

			var dail_code = jQuery(this).find("#pphone").parent().parent().find(".iti__selected-dial-code");

			var countryCode = iti.getSelectedCountryData().iso2;
			var maxLength = phoneLengthMapping[countryCode]?.max || 15; // Default to 15 if no mapping exists

			// Get current input value length
			var phoneNumber = input.value.replace(/\s/g, ""); // Remove whitespace


			if (phoneNumber.length > maxLength) {
				input.setCustomValidity("Incorrect number of digit only allow " + maxLength +" digit");
				input.reportValidity();
				e.preventDefault();
					return false;
			}else{
				input.setCustomValidity("");
			}

			if(dail_code && dail_code.length > 0){
				dail_code = dail_code.html();
				dail_code = dail_code.replace("+","");
				phone_val = jQuery(this).find("#pphone").val();

				var allow_phone = jQuery(this).find(".allow_phone").val();

                if (phone_val.startsWith(dail_code) && allow_phone != "true") {
					var phoneField = jQuery(this).find("#pphone");
					if (phoneField.siblings('.phone-warning').length === 0) {
						phoneField.focus();
						phoneField.parent().after(`
							<div class="phone-warning" style="margin-top: 0px;padding: 10px;background-color: #ffe7a6;border: 1px solid #ffcc00;border-radius: 5px;color: #333;display: flex;justify-content: space-between;align-items: center;">
								Er nummeret riktig?
								<button class="btn btn-warning btn-sm close-warning warning_phone" style="background:#008474;color:#fff;font-size: 14px;">Ja, det er riktig</button>
							</div>
						`);
					}
					e.preventDefault();
					return false;
				}
			}
		})
		jQuery(document).on("click","#edit_user .warning_phone",function(){

			jQuery("#edit_user").append("<input type='hidden' class='allow_phone' value='true'>");

			jQuery(".phone-warning").remove();

		})
    } 

	
</script>

		<!-- Change Password -->
		<br>
		<div class="row">
		<div class="col-lg-12 col-md-12">
				<div class="dashboard-list-box margin-top-0">
					<h4 class="gray"><?php esc_html_e('Change Password','listeo_core') ?></h4>
					<div class="dashboard-list-box-static">

						<!-- Change Password -->
						<div class="my-profile ee">
							
								<div class="col-md-12">
									<div class="notification notice margin-top-0 margin-bottom-0">
										<p><?php esc_html_e('Your password should be at least 12 random characters long to be safe','listeo_core') ?></p>
									</div>
								</div>
							</div>
							<?php if ( isset($_GET['updated_pass']) && $_GET['updated_pass'] == 'true' ) : ?>
								<div class="notification success closeable margin-bottom-35"><p><?php esc_html_e('Your password has been updated.', 'listeo_core'); ?></p><a class="close" href="#"></a></div>
							<?php endif; ?>

							<?php  if ( isset($_GET['err_pass']) && !empty($_GET['err_pass'])  ) : ?>
							<div class="notification error closeable margin-bottom-35"><p>
								<?php
								switch ($_GET['err_pass']) {
								 	case 'error_1':
								 		echo esc_html_e('Your current password does not match. Please retry.','listeo_core');
								 		break;
								 	case 'error_2':
								 		echo esc_html_e('The passwords do not match. Please retry..','listeo_core');
								 		break;
								 	case 'error_3':
								 		echo esc_html_e('A bit short as a password, don\'t you think?','listeo_core');
								 		break;
								 	case 'error_4':
								 		echo esc_html_e('Password may not contain the character "\\" (backslash).','listeo_core');
								 		break;
								 	case 'error_5':
								 		echo esc_html_e('An error occurred while updating your profile. Please retry.','listeo_core');
								 		break;

								 	default:
								 		# code...
								 		break;
								 }  ?>

								</p><a class="close" href="#"></a>
							</div>
							<?php endif; ?>
							<form name="resetpasswordform" action="" method="post">
								<label><?php esc_html_e('Current Password','listeo_core'); ?></label>
								<input type="password" name="current_pass">

								<label for="pass1"><?php esc_html_e('New Password','listeo_core'); ?></label>
								<input name="pass1" type="password">

								<label for="pass2"><?php esc_html_e('Confirm New Password','listeo_core'); ?></label>
								<input name="pass2" type="password">

								<input type="submit" name="wp-submit" id="wp-submit" class="margin-top-20 button" value="<?php esc_html_e('Lagre passord','listeo_core'); ?>" />

								<input type="hidden" name="listeo_core-password-change" value="1" />
							</form>

						</div>

					</div>
				</div>
			</div>