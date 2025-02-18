<?php
/**
 * Template Name: Dashboard Page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WPVoyager
 */
 
 if($_GET['login'] == "empty_username,empty_password"){
  wp_redirect(get_site_url().'?login=empty_username,empty_password'.'#login');
    exit();
 }
 else if($_GET['login'] == "empty_password"){
  wp_redirect(get_site_url().'?login=empty_password'.'#login');
    exit();
 }
 else if($_GET['login'] == "empty_username"){
  wp_redirect(get_site_url().'?login=empty_username'.'#login');
    exit();
 }
 else if($_GET['login'] == "invalid_username"){
  wp_redirect(get_site_url().'?login=invalid_username'.'#login');
    exit();
 }
 else if($_GET['login'] == "incorrect_password"){
  wp_redirect(get_site_url().'?login=incorrect_password'.'#login');
    exit();
 }
 else if($_GET['register-errors'] == "email"){
  wp_redirect(get_site_url().'?tab=two&register-errors=email'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "email_exists"){
  wp_redirect(get_site_url().'?tab=two&register-errors=email_exists'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "closed"){
  wp_redirect(get_site_url().'?tab=two&register-errors=closed'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "captcha-no"){
  wp_redirect(get_site_url().'?tab=two&register-errors=captcha-no'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "username_exists"){
  wp_redirect(get_site_url().'?tab=two&register-errors=username_exists'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "captcha-fail"){
  wp_redirect(get_site_url().'?tab=two&register-errors=captcha-fail'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "policy"){
  wp_redirect(get_site_url().'?tab=two&register-errors=policy'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "first_name"){
  wp_redirect(get_site_url().'?tab=two&register-errors=first_name'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "last_name"){
  wp_redirect(get_site_url().'?tab=two&register-errors=last_name'.'#register');
    exit();
 }
 
 else if($_GET['register-errors'] == "password-no"){
  wp_redirect(get_site_url().'?tab=two&register-errors=password-no'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "empty_user_login"){
  wp_redirect(get_site_url().'?tab=two&register-errors=policy'.'#register');
    exit();
 }
 else if($_GET['register-errors'] == "incorrect_password"){
  wp_redirect(get_site_url().'?tab=two&register-errors=incorrect_password'.'#register');
    exit();
 }


if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
// Append the host(domain name, ip) to the URL.
$url .= $_SERVER['HTTP_HOST'];

if ( !is_user_logged_in() ) {

    $errors = array();

    if ( isset( $_REQUEST['login'] ) ) {
        $error_codes = explode( ',', $_REQUEST['login'] );

        foreach ( $error_codes as $code ) {
            switch ( $code ) {
                case 'empty_username':
                    $errors[] = esc_html__( 'You do have an email address, right?', 'listeo' );
                    break;
                case 'empty_password':
                    $errors[] =  esc_html__( 'You need to enter a password to login.', 'listeo' );
                    break;
                case 'invalid_username':
                    $errors[] =  esc_html__(
                        "We don't have any users with that email address. Maybe you used a different one when signing up?",
                        'listeo'
                    );
                    break;
                case 'incorrect_password':
                    $err = __(
                        "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
                        'listeo'
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
                    $errors[] = esc_html__( 'The email address you entered is not valid.', 'listeo' );
                    break;
                case 'email_exists':
                    $errors[] = esc_html__( 'An account exists with this email address.', 'listeo' );
                    break;
                case 'closed':
                    $errors[] = esc_html__( 'Registering new users is currently not allowed.', 'listeo' );
                    break;
                case 'captcha-no':
                    $errors[] = esc_html__( 'Please check reCAPTCHA checbox to register.', 'listeo' );
                    break;
                case 'captcha-fail':
                    $errors[] = esc_html__( "You're a bot, aren't you?.", 'listeo' );
                    break;
                case 'policy-fail':
                    $errors[] = esc_html__( "Please accept the Privacy Policy to register account.", 'listeo' );
                    break;
                case 'first_name':
                    $errors[] = esc_html__( "Please provide your first name", 'listeo' );
                    break;
                case 'last_name':
                    $errors[] = esc_html__( "Please provide your last name", 'listeo' );
                    break;
                case 'empty_user_login':
                    $errors[] = esc_html__( "Please provide your user login", 'listeo' );
                    break;
                case 'password-no':
                    $errors[] = esc_html__( "You have forgot about password.", 'listeo_core', 'listeo' );
                    break;
                case 'incorrect_password':
                    $err = __(
                        "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
                        'listeo'
                    );
                    $errors[] =  sprintf( $err, wp_lostpassword_url() );
                    break;
                default:
                    break;
            }
        }
    }
    get_header();

    $page_top = get_post_meta($post->ID, 'listeo_page_top', TRUE);

    switch ($page_top) {
        case 'titlebar':
            get_template_part( 'template-parts/header','titlebar');
            break;

        case 'parallax':
            get_template_part( 'template-parts/header','parallax');
            break;

        case 'off':

            break;

        default:
            get_template_part( 'template-parts/header','titlebar');
            break;
    }

    $layout = get_post_meta($post->ID, 'listeo_page_layout', true); if(empty($layout)) { $layout = 'right-sidebar'; }
    $class  = ($layout !="full-width") ? "col-lg-9 col-md-8 padding-right-30" : "col-md-12"; ?>
    <div class="container <?php echo esc_attr($layout); ?>">

        <div class="row">

            <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>
                <div class="col-lg-6 col-md-6 col-md-offset-3 sign-in-form style-1 margin-bottom-45">
                    <?php if ( count( $errors ) > 0 ) : ?>
                        <?php foreach ( $errors  as $error ) : ?>
                            <div class="notification error closeable">
                                <p><?php echo ($error); ?></p>
                                <a class="close"></a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ( isset( $_REQUEST['registered'] ) ) : ?>
                        <div class="notification success closeable">
                            <p>
                                <?php
                                $password_field = get_option('listeo_display_password_field');
                                if($password_field) {
                                    printf(
                                        esc_html__( 'You have successfully registered to %s.', 'listeo' ),
                                        '<strong>'.get_bloginfo( 'name' ).'</strong>'
                                    );
                                } else {
                                    printf(
                                        esc_html__( 'You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'listeo' ),
                                        '<strong>'.get_bloginfo( 'name' ).'</strong>'
                                    );
                                }

                                ?>
                            </p></div>
                    <?php endif; ?>
                    <img src="/wp-content/uploads/2021/09/Frame-966.svg" class="loginLogo" />
                    <div id="sign-in-dialog">
                    
                        <?php echo do_shortcode("[gibbs_register_login]"); ?>
                        <script>
                            jQuery(document).ready(function(){
                                jQuery('form.register label[for=privacy_policy]').append(' og <a href="<?php echo esc_url(get_permalink(364)); ?>">Vilkår og betingelser</a>.');
                            });
                        </script>
                    </div>
                </div>
            </article>

            <?php if($layout !="full-width") { ?>
                <div class="col-lg-3 col-md-4">
                    <div class="sidebar right">
                        <?php get_sidebar(); ?>
                    </div>
                </div>
            <?php } ?>

        </div>

    </div>
    <div class="clearfix"></div>
    <?php
    get_footer();

} else { //is logged

    get_header();
    $current_user = wp_get_current_user();
    $user_id = get_current_user_id();
    $roles = $current_user->roles;
    $role = array_shift( $roles );

    ?>

    <!-- Content
    	================================================== -->
    <?php
    $current_user = wp_get_current_user();

    $roles = $current_user->roles;
    $role = array_shift( $roles );
    if(!empty($current_user->user_firstname)){
        $name = $current_user->user_firstname;
    } else {
        $name =  $current_user->display_name;
    }
    ?>

    <!-- Titlebar -->
    <div id="titlebar" style="margin-bottom:0px;">
        <div class="row container" style="margin:auto;">
            <div class="col-md-12 mobile-title" style="display:flex;">

                <?php
                // Adds backbutton to parent page if parent page exists
                if ( $post->post_parent ) { ?>
                    <a href="<?php echo get_permalink( $post->post_parent ); ?>" style="font-size:1.5rem;line-height:40px;" class="margin-right-15" >
                        <i class="fa fa-arrow-left" style="font-family:'Font Awesome Pro'"></i>Tilbake
                    </a>
                <?php } ?>

                <?php
                $is_dashboard_page = get_option('listeo_dashboard_page');
                $is_booking_page = get_option('listeo_bookings_page');
                global $post;
                if( $is_dashboard_page == $post->ID ) { ?>
                    <h2><?php esc_html_e('Howdy,','listeo'); ?> <?php echo esc_html($name); ?> !</h2>
                <?php } else if( $is_booking_page == $post->ID ) {
                    $status = '';
                    if(isset($_GET['status'])){
                        $status = $_GET['status'];
                        switch ($status) {
                            case 'approved': ?>
                                <h1><?php esc_html_e('Approved Bookings','listeo'); ?></h1>
                                <?php
                                break;
                            case 'waiting': ?>
                                <h1><?php esc_html_e('Pending Bookings','listeo'); ?></h1>
                                <?php
                                break;
                            case 'cancelled': ?>
                                <h1><?php esc_html_e('Cancelled Bookings','listeo'); ?></h1>
                                <?php
                                break;

                            default:
                                ?>
                                <h1><?php esc_html_e('Bookings','listeo'); ?></h1>
                                <?php
                                break;
                        }
                    } else  { ?>
                        <h1><?php the_title(); ?></h1>
                    <?php }
                } else { ?>
                    <h1><?php the_title(); ?></h1>
                <?php } ?>
            </div>
        </div>
        <!-- Breadcrumbs -->
        <nav id="breadcrumbs">
            <ul>
                <li><a href="<?php echo get_home_url(); ?>"><?php esc_html_e('Home','listeo'); ?></a></li>
                <?php if ( $post->post_parent ) { ?>
                    <li><a href="<?php echo get_permalink( $post->post_parent ); ?>"><?php echo get_the_title($post->post_parent); ?></a></li>
                <?php } ?>
                <li><?php the_title(); ?></li>
            </ul>
        </nav>
    </div>

    <div class="dashboard-content" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <div id="dashboard-page-content">
            <?php
            while ( have_posts() ) : the_post();
                the_content();
            endwhile; // End of the loop.
            ?>

            <?php
            if ($pagename == 'min-gibbs'){
                $_verified_user = get_user_meta(get_current_user_id(),"_verified_user",true);
                if($_verified_user == "on") {
                    ?>
                        <p style="font-size:2rem;">
                            <a href="javascript:void(0)">
                                <i class="im im-icon-Security-Check margin-right-15"></i>Du er verifisert med BankID!
                            </a>
                            
                        </p>
                    <?php

                }else{
                    ?>

                        <p style="font-size:2rem;">
                            <a href="javascript:void(0)" id="varify_modal_btn">
                                <i class="im im-icon-Security-Check margin-right-15" style="color: #EDD035"></i>Trykk her for å verifisere deg med BankID! 
                            </a>
                        </p>
                    <?php

                }
               /* if (  !get_user_meta( intval($user_id), $verified,  true )) { ?>
                    <p style="font-size: 2rem;"><a href="<?php echo $url ?>/bankid-verification/"><i class="im im-icon-Checked-User margin-right-15"></i>Verifiser din bruker med BankID!</a></p>
                <?php }else{ ?>
                    <p style="font-size: 2rem;"><a href="#" style="pointer-events: none;"><i class="im im-icon-Security-Check margin-right-15"></i>Du er verifisert med BankID!</a></p>
                <?php }*/

                echo "<p style=\"font-size:2rem;\"><a href=\"" . wp_logout_url(get_home_url()) . "\"><i class=\"im im-icon-Power-2 margin-right-15\"></i>Logg ut</a></p>";
            } else if ($pagename == 'dashboard') :

                $bookings_page = get_option('listeo_bookings_page'); ?>
                <p style="font-size:2rem;">
                    <a href="<?php echo esc_url(get_permalink(6428)); ?>">
                        
                            <i class="im im-icon-Credit-Card margin-right-15"></i>Kuponger
                    </a>
                </p>

                <p style="display: none; font-size:2rem;">
                    <?php
                    $args=array(
                        'post_type' => 'listing',
                        'post_status' => 'publish',
                        'order' => 'DESC',
                        'author' => $user_id
                    );

                    $user_posts = get_posts( $args );

                    foreach ($user_posts as $post){
                        if(get_post_meta( $post->ID , '_booking_status',true) == 'on'):
                            $current_user_posts = $post->ID;
                            break;
                        endif;
                    }

                    if(isset($current_user_posts)): ?>
                    <a href="<?php echo get_permalink($current_user_posts) ?>?check_availability=1">
                        <i class="im im-icon-Calendar-4 margin-right-15"></i>Tilgjengelighets kalender
                    </a>
                    <?php endif; ?>
                </p>

                <p style="font-size:2rem;">
                    <a href="<?php echo esc_url(get_permalink(51)); ?>?status=attention">
                        <i class="im im-icon-Paper-Plane margin-right-15"></i>Sendte forespørsler
                    </a>
                    <a href="<?php echo esc_url(get_permalink(51)); ?>">
                        <?php
                        $count_pending = listeo_count_my_bookings_by_status($user_id, 'attention');
                       // $count_pending1 = listeo_count_my_bookings_with_status($user_id,'attention');
                        $sendte_sum_counter = $count_pending;
                        if (isset($sendte_sum_counter) && $sendte_sum_counter > 0): ?><span class="nav-tag" style="background: #008474;"><?php echo esc_html($sendte_sum_counter); ?></span>
                        <?php endif; ?>
                    </a>

                    <!--                        Hide the grey label in sendte elementer-->
                    <!--                        <a href="--><?php //echo esc_url(get_permalink(51)); ?><!--">-->
                    <!--                            --><?php
                    //                            $count_pending = listeo_count_bookings($user_id, 'waiting');
                    //                                if (isset($count_pending)): ?><!--<span class="nav-tag active">--><?php //echo esc_html($count_pending); ?><!--</span>-->
                    <!--                            --><?php //endif; ?>
                    <!--                        </a>-->
                </p>

             

                <?php
                global $wpdb;
                $current_user = wp_get_current_user();
                $numPosts = count(get_posts(['author' => $current_user->ID, 'post_type' => 'listing', 'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')]));
                if($numPosts > 0):
                    ?>

                    <p style="font-size:2rem;">
                        <a href="<?php echo esc_url(get_permalink(69)); ?>?status=waiting">
                            <i class="im im-icon-Mail-Read margin-right-15"></i>Mottatte Forespørsler
                        </a>
                        

                        <a href="<?php echo esc_url(get_permalink($bookings_page)); ?>?status=waiting">
                            <?php
                            $count_pending = listeo_count_bookings($user_id, 'waiting');
                            $count_pending1 = listeo_count_bookings($user_id,'attention');
                            $mottatte_sum_counter = $count_pending + $count_pending1;
                            if (isset($mottatte_sum_counter) && $mottatte_sum_counter > 0): ?><span class="nav-tag" style="background: #008474"><?php echo esc_html($mottatte_sum_counter); ?></span>
                            <?php endif; ?>
                            <!--                        --><?php
                            //                            $count_confirmed = listeo_count_bookings($user_id, 'confirmed');
                            //                            if (isset($count_confirmed) && $count_confirmed > 0): ?><!--<span class="nav-tag" style="background: #008474">--><?php //echo esc_html($count_confirmed); ?><!--</span>-->
                            <!--                        --><?php //endif; ?>
                        </a>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
           
        </div>

    </div>
    </div>
    <div id="varify_modal" class="modal modal_custom">

      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-header">
          <span class="close varify_modal_close">&times;</span>
          <h2><?php  echo __("Verifiser deg med","Gibbs");?></h2>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
          <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
          <form method="post" class="user_update_form" action="javascript:void(0)">
            
              <div class="row">
                <div class="form-group col-sm-12 text-center vipps">
                  <?php echo do_shortcode('[login-with-vipps text="" application="wordpress"]');?>
                </div>
                
                <div class="form-group col-sm-12 text-center miniid" width: 20%;>
                  <a href="https://login.bankid.no/ ">
                  <img src="<?php echo get_stylesheet_directory_uri();?>/mini.png" />
                </a>
                </div>
              </div>
          </form>
        </div>
      </div>

    </div>
    <!-- Dashboard / End -->
    <script type="text/javascript">
        // Get the modal
        //var team_sizeModal = document.getElementById("team_sizeModal");
        var varify_modal = document.getElementById("varify_modal");

        //var team_sizebtn = document.getElementById("team_size");

        // Get the button that opens the modal
        var varify_modal_btn = document.getElementById("varify_modal_btn");

        if(varify_modal_btn != null){

            // Get the <span> element that closes the modal
            //var span = document.getElementsByClassName("close")[0];
            var varify_modal_close = document.getElementsByClassName("varify_modal_close")[0];

            // When the user clicks the button, open the modal 
            /*team_sizebtn.onclick = function() {
              team_sizeModal.style.display = "block";
            }*/
            varify_modal_btn.onclick = function() {
              varify_modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            /*span.onclick = function() {
              team_sizeModal.style.display = "none";
            }*/
            varify_modal_close.onclick = function() {
              varify_modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
              /*if (event.target == team_sizeModal) {
                team_sizeModal.style.display = "none";
              } */
              if (event.target == varify_modal) {
                 varify_modal.style.display = "none";
              }
            }
        }    

    </script>
    <?php
    get_footer();
} ?>
