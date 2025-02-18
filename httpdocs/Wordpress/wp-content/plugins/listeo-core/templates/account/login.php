<?php 
$errors = array();

if ( isset( $_REQUEST['login'] ) ) {
    $error_codes = explode( ',', $_REQUEST['login'] );
 
    foreach ( $error_codes as $code ) {
       switch ( $code ) {
	        case 'empty_username':
	            $errors[] = esc_html__( 'You do have an email address, right?', 'listeo_core' );
	   		break;
	        case 'empty_password':
	            $errors[] =  esc_html__( 'You need to enter a password to login.', 'listeo_core' );
	   		break;
	        case 'invalid_username':
	            $errors[] =  esc_html__(
	                "We don't have any users with that email address. Maybe you used a different one when signing up?",
	                'listeo_core'
	            );
	   		break;
	        case 'incorrect_password':
	            $err = __(
	                "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
	                'listeo_core'
	            );
	            $errors[] =  sprintf( $err, wp_lostpassword_url() );
	 		break;
	        default:
	            break;
	    }
    }
} 
 // Retrieve possible errors from request parameters
if ( isset( $_REQUEST['register-errors'] ) ) {
    $error_codes = explode( ',', $_REQUEST['register-errors'] );
 
    foreach ( $error_codes as $error_code ) {
 		
         switch ( $error_code ) {
	        case 'email':
			     $errors[] = esc_html__( 'The email address you entered is not valid.', 'listeo_core' );
			   break;
			case 'email_exists':
			     $errors[] = esc_html__( 'An account exists with this email address.', 'listeo_core' );
			 	  break;
			case 'closed':
			     $errors[] = esc_html__( 'Registering new users is currently not allowed.', 'listeo_core' );
			     break;
	 		case 'captcha-no':
			     $errors[] = esc_html__( 'Please check reCAPTCHA checbox to register.', 'listeo_core' );
			     break;
			case 'captcha-fail':
			     $errors[] = esc_html__( "You're a bot, aren't you?.", 'listeo_core' );
			     break;
			case 'password-no':
			     $errors[] = esc_html__( "You have forgot about password.", 'listeo_core' );
			     break;
	 
	        case 'incorrect_password':
	            $err = esc_html__(
	                "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
	                'listeo_core'
	            );
	            $errors[] =  sprintf( $err, wp_lostpassword_url() );
	   			break;
	        default:
	            break;
	    }
    }
} ?>

	<div class="row">
	<div class="col-md-4 col-md-offset-4">
	
	<!--Tab -->
		<div class="my-account style-1 margin-top-5 margin-bottom-40">

				<?php if ( isset( $_REQUEST['registered'] ) ) : ?>
				    <div class="notification success closeable">
				    <p>
				        <?php
				            printf(
				                __( 'You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'listeo_core' ),
				                get_bloginfo( 'name' )
				            );
				        ?>
				    </p></div>
				<?php endif; ?>
					<?php if ( count( $errors ) > 0 ) : ?>
					    <?php foreach ( $errors  as $error ) : ?>
					        <div class="notification error closeable">
								<p><?php echo $error; ?></p>
								<a class="close"></a>
							</div>
					    <?php endforeach; ?>
					<?php endif; ?>
			<ul class="tabs-nav">
				<li class=""><a href="#tab1"><?php esc_html_e('Log In','listeo_core'); ?></a></li>
				<li><a href="#tab2"><?php esc_html_e('Register','listeo_core'); ?></a></li>
			</ul>

			<div class="tabs-container alt">
			
			<!-- Login -->
			<div class="tab-content" id="tab1" style="display: none;">
				<!--Tab -->

		
				<?php
//				require_once(REALTEO_PLUGIN_DIR.'includes/lib/recaptchalib.php');

				/*WPEngine compatibility*/
				if (defined('PWP_NAME')) { ?>
					<form method="post" class="login" action="<?php echo wp_login_url().'?wpe-login=';echo PWP_NAME;?>">
				<?php } else { ?>
					<form method="post" class="login" action="<?php echo wp_login_url(); ?>">
				<?php } ?>

				    <p class="form-row form-row-wide">
							<label for="username">
							<i class="im im-icon-Male"></i>
							<input type="text" placeholder="<?php _e( 'Username', 'listeo_core' ); ?>" class="input-text" name="log" id="user_login" value="" />
						</label>
					</p>
					<p class="form-row form-row-wide">
						<label for="password">
							<i class="im im-icon-Lock-2"></i>
							<input class="input-text" placeholder="<?php _e( 'Password', 'listeo_core' ); ?>" type="password" name="pwd" id="user_pass"/>
						</label>
					</p>
				   <p class="form-row">
						<input type="submit" class="button border margin-top-10" name="login" value="<?php _e( 'Sign In', 'listeo_core' ); ?>" />

						<label for="rememberme" class="rememberme">
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e('Remember Me','listeo_core'); ?></label>
					</p>
				    <p class="lost_password">
						<a href="<?php echo wp_lostpassword_url(); ?>"> <?php esc_html_e('Lost Your Password?','listeo_core'); ?></a>
					</p>
				</form>
	
			</div>

			<!-- Register -->
			<div class="tab-content" id="tab2" style="display: none;">
				<?php 
				if ( is_user_logged_in() ) {
				    esc_html_e( 'You are already signed in.', 'listeo_core' );
				} elseif ( ! get_option( 'users_can_register' ) ) {
				    esc_html_e( 'Registering new users is currently not allowed.', 'listeo_core' );
				} else { ?>
     
		    	<?php
				/*WPEngine compatibility*/
				if (defined('PWP_NAME')) { ?>
					<form id="signupform" action="<?php echo wp_registration_url().'&wpe-login=';echo PWP_NAME; ?>" method="post">
				<?php } else { ?>
					<form id="signupform" action="<?php echo wp_registration_url(); ?>" method="post">
				<?php } ?>
			        <p class="form-row">
			            <label for="email"><?php esc_html_e( 'Email', 'listeo_core' ); ?> <strong>*</strong></label>
			            <input type="text" name="email" id="email">
			        </p>
			 
			        <p class="form-row">
			            <label for="first_name">
			            <input type="text" placeholder="<?php esc_html_e( 'First Name', 'listeo_core' ); ?>" name="first_name" id="first-name">
			        	</label>
			        </p>
			 
			        <p class="form-row">
			            <label for="last_name">
			            <input type="text" placeholder="<?php esc_html_e( 'Last Name', 'listeo_core' ); ?>" name="last_name" id="last-name">
			            </label>
			        </p>
			 
			        <p class="form-row">
			            <?php esc_html_e( 'Note: Your password will be generated automatically and sent to your email address.', 'listeo_core' ); ?>
			        </p>
					<?php 
					$recaptcha_status =  get_option('listeo_recaptcha');
			 		$recaptcha_version = get_option('listeo_recaptcha_version','v2');
	                if($recaptcha && $recaptcha_version == 'v2'){ ?>
	                
	                <p class="form-row captcha_wrapper">
	                    <div class="g-recaptcha" data-sitekey="<?php echo get_option('listeo_recaptcha_sitekey'); ?>"></div>
	                </p>
	                <?php } 

	                if($recaptcha && $recaptcha_version == 'v3'){ ?>
	                    <input type="hidden" id="rc_action" name="rc_action" value="ws_register">
	                    <input type="hidden" id="token" name="token">
	                <?php } ?>

			        <p class="signup-submit">
			            <input type="submit" name="submit" class="register-button"  value="<?php esc_html_e( 'Register', 'listeo_core' ); ?>"/>
			        </p>
			    </form>
			
		    <?php } ?>
			</div>

		</div>
	</div>