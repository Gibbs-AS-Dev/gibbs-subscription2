<form class="gibbs-register user-form" action="javascript:void(0)" method="post">

    <input type="hidden" name="action" value="gibbsajaxregister">

   
    <div class="row">
        <div class="col-md-6 custom-gap-right">
            <div class="form-row">
                <label for="first-name"><b><?php esc_html_e('First Name', 'listeo_core'); ?></b></label>
                <input  class="form-control" placeholder="Fornavn" type="text" name="first_name" id="first-name" required="">
            </div>
        </div>
        <div class="col-md-6 custom-gap-left">
            <div class="form-row">
                <label for="last-name"><b><?php esc_html_e('Last Name', 'listeo_core'); ?></b></label>
                <input  class="form-control" placeholder="Etternavn" type="text" name="last_name" id="last-name" required="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-row">
                <label for="email"><b><?php esc_html_e('Email Address', 'listeo_core'); ?></b></label>
                <input type="email" placeholder="Epostadresse"  class="form-control" name="email" id="email" value="" required="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-row">
                <label for="password1"><b><?php esc_html_e('Password', 'listeo_core'); ?></b></label>
                <input placeholder="Passord"  class="form-control" type="password" name="password" id="password1" minlength="8" required="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-row" style="position: relative;">
                <label for="phone"><b><?php esc_html_e('Phone', 'listeo_core'); ?></b></label>
                <input type="text" pattern="\d*" placeholder="<?php esc_html_e('Phone', 'listeo_core'); ?>" class="form-control gibbs_phonenumber" name="phone"  value="" minlength="7" maxlength="12" />
                            <input type="hidden" class="country_code" name="country_code" value="47">
            </div>
        </div>
    </div>

    
    <?php
    $privacy_policy_status = get_option('listeo_privacy_policy');

    if ($privacy_policy_status && function_exists('the_privacy_policy_link')) { ?>
        <p class="form-row margin-top-10 margin-bottom-10 sdsd">
            <label for="privacy_policy"><input type="checkbox" id="privacy_policy" name="privacy_policy" required><span><?php esc_html_e('I agree to the', 'listeo_core'); ?> <a href="<?php echo get_privacy_policy_url(); ?>"><?php esc_html_e('Privacy Policy', 'listeo_core'); ?></a></span></label>

        </p>

    <?php } ?>
    <?php wp_nonce_field('listeo-ajax-login-nonce', 'register_security'); ?>
    <input type="submit" class="btn btn-primary btn-block" name="register" value="<?php esc_html_e('Register', 'listeo_core'); ?>" />

    <div class="notification error closeable" style="display: none;">
        <p></p>
    </div>


   <div class="social-login-separator"><span>Registrer med</span></div>
   <div class="row">
       <div class="col-md-12">
      <?php 
                    global $wp;
                    $cr_url =  home_url( $wp->request );

                    if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != ""){
                    	$cr_url = $cr_url."?".$_SERVER["QUERY_STRING"];
                    }


                    $parms = "";

                    if (str_contains($cr_url, '?')) { 
					    $parms = "&vipps=true";
					}else{
						$parms = "?vipps=true";
					}
					$vp_url = urlencode($cr_url.$parms); 
                  echo $button = "<button type='button' class=\"vipps_login_button\" onclick=\"window.location.href='".home_url()."?option=oauthredirect&app_name=Vipps&redirect_url=".$vp_url."';\">Vipps</button>";

                 // echo do_shortcode('[miniorange_custom_login_button appname="Vipps"]'.$button.'[/miniorange_custom_login_button]');?>

                  <?php 

                  $cr_url = urlencode($cr_url); 


                  echo $button2 = "<button type='button' class=\"microsoft_login_button\" onclick=\"window.location.href='".home_url()."/?option=oauthredirect&app_name=Microsoft2&redirect_url=".$cr_url."';\">Microsoft </button>";

                 // echo do_shortcode('[miniorange_custom_login_button appname="Microsoft2"]'.$button2.'[/miniorange_custom_login_button]');?>
                  
                  <?php 

                 echo $button3 = "<button type='button' class=\"google_login_button\" onclick=\"window.location.href='".home_url()."?option=oauthredirect&app_name=Google&redirect_url=".$cr_url."';\">Google </button>";
                   
                  
         ?>  
        </div>                        
   </div>
</form>
<style>
   
</style>
<?php
wp_enqueue_script( 'intelnew-js', get_stylesheet_directory_uri() . '/assets/js/intlTelInput.js?ver=5.7.2', array( 'jquery' ), '', false );

// Enqueue phone.js and localize it with mySiteData
wp_enqueue_script( 'phone-js', get_stylesheet_directory_uri() . '/assets/js/phone.js?'.time(), array( 'jquery', 'intelnew-js' ), '', false );

wp_localize_script( 'phone-js', 'mySiteData', array(
    'stylesheet_uri' => get_stylesheet_directory_uri(),
));
?>

<script type="text/javascript">
    jQuery('.gibbs-register').on('submit', function(e){
        var redirecturl = "<?php echo home_url();?>";
        if("<?php echo $redirect;?>" != ""){
            redirecturl = "<?php echo $redirect;?>";
        }
        debugger;
        redirecturl = redirecturl.replaceAll("<?php echo get_option('siteurl');?>","");

        redirecturl = "<?php echo get_option('siteurl');?>"+redirecturl;

        var dail_code = jQuery(this).find(".gibbs_phonenumber").parent().parent().find(".iti__selected-dial-code");

        if(dail_code && dail_code.length > 0){
            dail_code = dail_code.html();
            dail_code = dail_code.replace("+","");
            phone_val = jQuery(this).find(".gibbs_phonenumber").val();
            var allow_phone = jQuery(this).find(".allow_phone").val();


            if (phone_val.startsWith(dail_code) && allow_phone != "true") {
                var phoneField = jQuery(this).find(".gibbs_phonenumber");
                // Show warning if phone number starts with the dial code
                //alert("Warning: The phone number should not start with the country code (" + dail_code + ").");
                if (phoneField.siblings('.phone-warning').length === 0) {
                    phoneField.parent().after(`
                        <div class="phone-warning" style="margin-top: 80px;position:absolute;font-weight: 600;padding: 10px;background-color: #FFF8DD;border-radius: 5px;color: #333;display: flex;justify-content: space-between;align-items: center;">
                            Er nummeret riktig?
                            <button class="btn btn-warning btn-sm close-warning warning_phone" style="background:#008474;color:#fff;">Ja, det er riktig</button>
                        </div>
                    `);
                }
                return false;
            }
        }
        
        jQuery('.gibbs-register .notification').removeClass('error').addClass('notice').show().text("Loading...")
        e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                data: jQuery(this).serialize(),
             
                }).done( function( data ) {

                    if (data.registered == true){

                        window.location.href = redirecturl;

                        jQuery('.gibbs-register .notification').show().removeClass('error').removeClass('notice').addClass('success').text(data.message);
                        success = true;
                    } else {
                        jQuery('.gibbs-register .notification').show().addClass('error').removeClass('notice').removeClass('success').text(data.message);
                    }
            } )
            .fail( function( reason ) {
                // Handles errors only
                console.debug( 'reason'+reason );
            } ); 
    });

    jQuery(document).on("click",".gibbs-register .warning_phone",function(){

        jQuery(".gibbs-register").append("<input type='hidden' class='allow_phone' value='true'>");

        jQuery(".phone-warning").remove();

    })

    
</script>