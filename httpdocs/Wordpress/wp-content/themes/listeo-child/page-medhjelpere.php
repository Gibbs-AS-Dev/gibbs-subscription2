<?php

/*
 * Template Name: Medhjelpere
 *
 * This is the template give assisstant for the current user owned listings to invite and remove users for help
 *
 * @package WPVoyager
 */
 
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
				<div class="col-lg-5 col-md-4 col-md-offset-3 sign-in-form style-1 margin-bottom-45">
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
                    <div id="sign-in-dialog">
                        <?php do_action('listeo_login_form'); ?>
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
    	<div class="dashboard-content" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    				<!-- Titlebar -->
    		<div id="titlebar">

    			<div class="row">
    				<div class="col-md-12" style="display:flex;">

                        <?php
                        // Adds backbutton to parent page if parent page exists
                        if ( $post->post_parent ) { ?>
                            <a href="<?php echo get_permalink( $post->post_parent ); ?>" style="font-size:2.5rem;line-height:40px;" class="margin-right-15" >
                            <i class="fa fa-arrow-left" style="font-family:'Font Awesome Pro'"></i>
                            </a>
                        <?php } ?>

						<h1>Medhjelpere</h1>

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
            <div id="dashboard-page-content">

        		<?php

                if (in_array($role, array(
                    'administrator',
                    'admin',
                    'owner'
                ))) :

                    $bookings_page = get_option('listeo_bookings_page');
                    $isCurrentPageWithUrlParam = (isset($_GET["status"])) ? trim($_GET["status"]) : "";
                    ?>

                   
                <?php endif;

///////////////////////////////////////////////

       $listings = '';
       if(isset($_POST['listings'])) $listings = $_POST['listings'];

         ?> <div class="search-bar-container">

             <form method="POST">

               <div class="drop-down-listings">
                  <select class="listings" name="listings">
                     <option disabled selected value="0"> - velg utleieobjekt - </option>
                     <?php
                       // Get all the options from the database for the 'listings' drop down list.
                       //$my_listings = $wpdb -> get_results( "SELECT term_id, name FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = //'wprm_course') " )  ; //get_the_title($booking_data['listing_id']);
                       	//get_posts( $args );
						/*$args = array(
							'author'        =>  $current_user->ID
						);
						$posts = get_posts($args);
						*/ 
						 //$my_listings =  listeo_core_get_listing_packages_as_options();
						/* echo $current_user->author;
						 $meta_values = get_post_meta( get_the_ID() );

						  var_dump( $meta_values );

					   if (!empty ($meta_values ) ) {
                           foreach ( $meta_values as $meta_values ) {
                               echo '<option value ="' . $meta_values -> listing_id . '">' . $meta_values -> the_title . '</option>';
                            }
                       }*/
                ?>
                   </select>
             </div>

             <input type="submit" value="Go">
          </form>

       </div>


    <?php
/////////////////////////////////////////////////////
$authors = get_users('role=author');

if(isset($authors) && !empty($authors))
{
    echo "<ul>";
    foreach($authors as $author)
    {
        $posts = get_posts(array('author'=>$author->ID));

        //if this author has posts, then include his name in the list otherwise don't
        if(isset($posts) && !empty($posts))
        {
            echo "<li>".$author->user_nicename."</li>";

            echo "<ul>";
            foreach($posts as $post)
            {
                echo "<li>".$post->post_title."</li>";
            }
            echo "</ul>";
        }
    }
    echo "</ul>";
}

?>

<?php
///////////////////////////////////////////////////////

          // Get posts using $course as your selected option here

	/////////////////////////////////////////////
        		while ( have_posts() ) : the_post();
        			the_content();
        		endwhile; // End of the loop. ?>
            </div>
    	</div>
    </div>
    <!-- Dashboard / End -->
    <?php
    get_footer();
} ?>
 	
?>	