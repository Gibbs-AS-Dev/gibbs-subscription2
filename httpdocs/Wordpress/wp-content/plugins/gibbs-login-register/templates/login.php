<?php
global $wpdb;

$button_bg_colour = "";
$button_text_colour = "";

if(isset($_GET["group_id"])){
    $group_id = $_GET["group_id"]; 

    $query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}users_groups_settings WHERE group_id = %d",
        $group_id
    );

    $results = $wpdb->get_results($query);

    foreach($results as $result){

        if($result->setting_key == "button_bg_colour"){

            $button_bg_colour = $result->setting_id;

        }
        if($result->setting_key == "button_text_colour"){

            $button_text_colour = $result->setting_id;

        }

    }

    
}
if($button_bg_colour != ""){
?>
<style>

    body #wrapper{
        background-color: <?php echo $button_bg_colour;?> !important;
    }
    
</style>
<?php } ?>

<form method="post" class="login-form-gibbs user-form" action="javascript:void(0)">

	<input type="hidden" name="action" value="gibbsajaxlogin">

	<div class="row">
		<div class="col-md-12">
			<div class="form-row email-div">
				<label for="user_login"><b>E-post</b></label>
                <input placeholder="Skriv inn din e-post" type="text" class="form-control" name="username"  value="" required>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-row">
                <div class="psw-div" style="display:none">
                    <label for="user_login"><b>Passord</b></label>
                    <input placeholder="Skriv passord her" class="form-control" type="password" name="password" required>
                </div>
                <div class="code-div" style="display: none;">
                <div style="background-color: #f0f8e8; padding: 8px; margin: 10px 0; border-radius: 5px;">
                    <p style="color: #3c763d; margin: 0; padding: 0;">
                    Koden er sendt til din e-post.
                    </p>
               
                </div>

                    <label for="user_login"><b>Engangskode</b></label>
                    <input placeholder="Tast inn her.." class="form-control" type="text" name="code">
                </div>
                <button type="button" class="btn btn-primary btn-block send-login-code" style="font-weight: 600;<?php echo($button_bg_colour != "")?'background-color:'.$button_bg_colour.'':'';?>;<?php echo($button_text_colour != "")?'color:'.$button_text_colour.'':'';?>">Fortsett</button>
                <button type="button" class="btn btn-primary btn-block login-with-code" style="display:none; font-weight: 600;<?php echo($button_bg_colour != "")?'background-color:'.$button_bg_colour.'':'';?>;<?php echo($button_text_colour != "")?'color:'.$button_text_colour.'':'';?>">Logg inn</button>
                <div class="d-flex w-100 align-items-center justify-content-space-between">
                    <a class="login-code-btn" href="javascript:void(0)" style="display:none;<?php echo($button_bg_colour != "")?'color:'.$button_bg_colour.'':'';?>">Logg inn med engangskode fra e-post</a>
                    <a class="login-password-btn" href="javascript:void(0)" style="<?php echo($button_bg_colour != "")?'color:'.$button_bg_colour.'':'';?>">Bruk passord i stedet</a>
                    <a class=" fr-pw psw-div" href="/wp-login.php?action=lostpassword"style="display:none;<?php echo($button_bg_colour != "")?'color:'.$button_bg_colour.'':'';?>">Mistet Passord?</a>
                </div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="form-row">
				<?php wp_nonce_field( 'listeo-ajax-login-nonce', 'login_security' ); ?>
				<input type="submit" class="btn btn-primary btn-block login-with-password" name="login" value="Logg Inn" style="display:none;<?php echo($button_bg_colour != "")?'background-color:'.$button_bg_colour.'':'';?>;<?php echo($button_text_colour != "")?'color:'.$button_text_colour.'':'';?>">
				
			</div>
		</div>
	</div>

   
   <div class="notification error closeable" style="display: none; margin-top: 20px; margin-bottom: 0px;">
      <p></p>
   </div>

   <div class="social-login-separator"><span>Logg på med</span></div>
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

<script type="text/javascript">
    jQuery('.login-form-gibbs').on('submit', function(e){
        var redirecturl = jQuery('input[name=_wp_http_referer]').val();
        if("<?php echo $redirect;?>" != ""){
            redirecturl = "<?php echo $redirect;?>";
        }

        redirecturl = redirecturl.replaceAll("<?php echo get_option('siteurl');?>","");

        redirecturl = "<?php echo get_option('siteurl');?>"+redirecturl;
    
    	
    	//jQuery('.login-form-gibbs .notification').hide()
        jQuery('.login-form-gibbs .notification').removeClass('error').addClass('notice').show().text("Logger inn...")
        e.preventDefault();
          //  debugger;
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                data: jQuery(this).serialize(),
             
                }).done( function( data ) {

                    if (data.loggedin == true){
                        // console.log(data.role.editor);
                        // return false;
                        if(data.role.editor){
                            role_editor = true;
                            window.location.href = '/kalender/';
                            return false;
                        }
                         else {
                            window.location.href = redirecturl;
                        }
                        jQuery('.login-form-gibbs .notification').show().removeClass('error').removeClass('notice').addClass('success').text(data.message);
                        success = true;
                    } else {
                        jQuery('.login-form-gibbs .notification').show().addClass('error').removeClass('notice').removeClass('success').text(data.message);
                    }
            } )
            .fail( function( reason ) {
                jQuery('.login-form-gibbs .notification').hide()
                // Handles errors only
                console.debug( 'reason'+reason );
            } ); 
    });
    jQuery(".login-code-btn").click(function(){
        jQuery(this).hide();
        jQuery(".login-password-btn").show();
        jQuery(".login-with-password").hide();
        jQuery(".send-login-code").show();
        jQuery(".psw-div").hide();
        jQuery(".login-form-gibbs .notification").hide();
        jQuery(".code-div").hide();
        jQuery(".login-with-code").hide();
    })
    jQuery(".login-password-btn").click(function(){
        jQuery(this).hide();
        jQuery(".login-code-btn").show();
        jQuery(".send-login-code").hide();
        jQuery(".psw-div").hide();
        jQuery(".login-with-password").show();
        jQuery(".psw-div").show();
        jQuery(".login-form-gibbs .notification").hide();
        jQuery(".code-div").hide();
        jQuery(".login-with-code").hide();
    })
    jQuery(".send-login-code").click(function (e) {
        e.preventDefault();
        jQuery(".login-with-code").hide();

        // Extract the input value
        const emailOrUsername = jQuery(this)
            .closest(".login-form-gibbs")
            .find("input[name=username]")
            .val();

        // Ensure input is not empty
        if (!emailOrUsername) {
            jQuery(".login-form-gibbs .notification").html("<p style='color: red;'>Skriv inn din e-post</p>");
            return;
        }
        jQuery('.login-form-gibbs .notification').removeClass('error').addClass('notice').show().text("Sender e-post")

        // AJAX request
        jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                data: {
                    action: "send_login_code",
                    input: emailOrUsername
                },
            }).done(function (response) {
                jQuery('.login-form-gibbs .notification').hide();
                
                
                if (response.success) {

                    jQuery(".code-div").show();
                    jQuery(".login-with-code").show();
                    jQuery(".send-login-code").hide();
                    
                    
                } else {
                    jQuery('.login-form-gibbs .notification').show().addClass('error').removeClass('notice').removeClass('success').html("<p style='color: red;'>" + response.data.message + "</p>")
                }
            }).fail(function (xhr, textStatus, errorThrown) {
                jQuery('.login-form-gibbs .notification').show().addClass('error').removeClass('notice').removeClass('success').html("<p style='color: red;'>An error occurred. Please try again later.</p>")
               // console.error("Error:", textStatus, errorThrown);
            });
    });
    jQuery(".login-with-code").click(function (e) {
        e.preventDefault();

        const emailOrUsername = jQuery(this)
            .closest(".login-form-gibbs")
            .find("input[name=username]")
            .val();

        // Extract the input value
        const login_code = jQuery(this).closest(".login-form-gibbs").find("input[name=code]").val();

        // Ensure input is not empty
        if (!login_code) {
            jQuery(".login-form-gibbs .notification").html("<p style='color: red;'>Please enter code.</p>");
            jQuery(this).closest(".login-form-gibbs").find("input[name=code]").focus();
            return;
        }
        jQuery('.login-form-gibbs .notification').removeClass('error').addClass('notice').show().text("Laster...")

        // AJAX request
        jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                data: {
                    action: "login_with_code",
                    input: emailOrUsername,
                    login_code: login_code,
                },
            }).done(function (response) {

                jQuery('.login-form-gibbs .notification').hide();
                
                
                if (response.success) {

                    var redirecturl = jQuery('input[name=_wp_http_referer]').val();
                    if("<?php echo $redirect;?>" != ""){
                        redirecturl = "<?php echo $redirect;?>";
                    }

                    redirecturl = redirecturl.replaceAll("<?php echo get_option('siteurl');?>","");

                    redirecturl = "<?php echo get_option('siteurl');?>"+redirecturl;

                    window.location.href = redirecturl;
                    jQuery('.login-form-gibbs .notification').show().removeClass('error').removeClass('notice').addClass('success').html(response.data.message);
                    
                    
                } else {
                    jQuery('.login-form-gibbs .notification').show().addClass('error').removeClass('notice').removeClass('success').html("<p style='color: red;'>" + response.data.message + "</p>")
                }
            }).fail(function (xhr, textStatus, errorThrown) {
                jQuery('.login-form-gibbs .notification').show().addClass('error').removeClass('notice').removeClass('success').html("<p style='color: red;'>An error occurred. Please try again later.</p>")
               // console.error("Error:", textStatus, errorThrown);
            });
    });
</script><script type="text/javascript">
    // Lytt etter Enter-tast i e-postfeltet
    jQuery(document).ready(function () {
        jQuery("input[name='username']").on("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault(); // Forhindre at formen sendes
                jQuery(".send-login-code").click(); // Simuler klikk på "Fortsett"-knappen
            }
        });
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function () {
        // Lytt etter Enter-tast i engangskode-feltet
        jQuery("input[name='code']").on("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault(); // Forhindre standard handling
                jQuery(".login-with-code").click(); // Simuler klikk på "Logg inn"-knappen
            }
        });
    });
</script>

