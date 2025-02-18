<?php
   $enable_header = get_field('pf_enable_header');
   $enable_sidebar = get_field('pf_enable_sidebar');
   $enable_searchbar = get_field('pf_enable_searchbar');
   $pf_enable_gray_background = get_field('pf_enable_gray_background');
   
   $enable_footer = get_field('pf_enable_footer');
   $pf_enable_must_logged = get_field('pf_enable_must_logged');
   
   $pf_enable_title = get_field('pf_enable_title');
   
   $query_strings = "";
   
   if($_SERVER['QUERY_STRING'] != ""){
      $query_strings = "?".$_SERVER['QUERY_STRING'];
   }
   
   $current_page_url = add_query_arg( [], home_url( $wp->request ) ).$query_strings; 
   if($pf_enable_must_logged == "1"){
   	if(!is_user_logged_in()){
   		$pf_enable_login_page_redirect = get_field('pf_enable_login_page_redirect');
   
   		if($pf_enable_login_page_redirect){
   			 $link_redirect = get_permalink($pf_enable_login_page_redirect);
   			 $_SESSION["current_page_url"] = $current_page_url;
   			 wp_redirect($link_redirect);
   			 exit;
   		}
   
   	}
   }
   
   global $wpdb;
   $groups_ids = array();
   if(is_user_logged_in()){
   
   		$current_user_id = get_current_user_id();
   		$users_groups = $wpdb->prefix . 'users_groups';  // table name
   		$users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
   		$sql_user_group = "select *  from `$users_and_users_groups` where users_id = $current_user_id";
   		$user_group_data_all = $wpdb->get_results($sql_user_group);
   
   
   
   		foreach ($user_group_data_all as $key => $user_group_data) {
   			$groups_ids[] = $user_group_data->role;
   		}
   
   
   		//echo "<pre>"; print_r($groups_ids); die;
   }
   $active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );
   $pf_which_group_role_can_see_this_page = get_field('pf_which_group_role_can_see_this_page');
   
   if($active_group_id != "" && $active_group_id != 0){
   
   	if(is_array($pf_which_group_role_can_see_this_page) && !empty($pf_which_group_role_can_see_this_page)){
   		if(is_user_logged_in()){
   
   			$exist_group_role = 0;
   
   	                $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
   	                $sql_user_group = "select *  from `$users_and_users_groups` where users_id = ".get_current_user_id()." AND users_groups_id = $active_group_id";
   	                $user_group_data_row = $wpdb->get_row($sql_user_group);
   
   	                if(isset($user_group_data_row->role)){
   	                    if(in_array($user_group_data_row->role, $pf_which_group_role_can_see_this_page)){
   	                         $exist_group_role = 1;
   	                    }
   	                }
   
   	                
   		    if($exist_group_role == 0){
   
   		    	$pf_group_role_redirection = get_field('pf_group_role_redirection');
   
   				if($pf_group_role_redirection){
   					 $link_redirect = get_permalink($pf_group_role_redirection);
   
   					 $_SESSION["current_page_url"] = $current_page_url;
   					 wp_redirect($link_redirect);
   					 exit;
   				}
   
   		    }
   			//echo "<pre>"; print_r($groups_ids); die;
   		}
   	  // echo "<pre>"; print_r($pf_which_group_role_can_see_this_page); die;
   	}
   
   	
   }else{
   	$pf_enable_restrict_with_user_role = get_field('pf_enable_restrict_with_user_role');
   
   		if(is_array($pf_enable_restrict_with_user_role) && !empty($pf_enable_restrict_with_user_role)){
   		    $user_meta = get_userdata(get_current_user_id());
   		    $user_roles = $user_meta->roles;
   
   		    $exist_admin_role = 0;
   
   		    foreach ($user_roles as $key => $role) {
   		    	if(in_array($role, $pf_enable_restrict_with_user_role)){
   		             $exist_admin_role = 1;
   		    	}
   		    }
   		    if($exist_admin_role == 0){
   
   		    	$pf_enable_restrict_user_role_redirect_page = get_field('pf_enable_restrict_user_role_redirect_page');
   
   				if($pf_enable_restrict_user_role_redirect_page){
   					 $link_redirect = get_permalink($pf_enable_restrict_user_role_redirect_page);
   					 $_SESSION["current_page_url"] = $current_page_url;
   					 wp_redirect($link_redirect);
   					 exit;
   				}
   
   		    }
   		}
   }
   
   $pf_group_licenses = get_field('pf_group_licenses');
   
   if(is_array($pf_group_licenses) && !empty($pf_group_licenses)){
   	if(is_user_logged_in()){
   		global $wpdb;
   
   		if(!empty($groups_ids)){
   			$groups_ids  = implode(",", $groups_ids);
   		}
   
   		$pf_group_licenses  = implode(",", $pf_group_licenses);
   
   		$exist_lic = 0;
   
   		if($active_group_id != "" && $active_group_id != 0){
   
   			$users_and_users_groups_licence = $wpdb->prefix . 'users_and_users_groups_licence';  // table name
   		    $users_and_users_groups_licence_sql = "select *  from `$users_and_users_groups_licence` where users_groups_id =  $active_group_id AND licence_id in ($pf_group_licenses) AND licence_is_active = '1'";
   			$licence_data = $wpdb->get_results($users_and_users_groups_licence_sql);
   
   
   			
   
   		    if(count($licence_data) > 0){
   	           $exist_lic = 1;
   			}
   		}
   
   	    if($exist_lic == 0){
   
   	    	$pf_group_license_redirection = get_field('pf_group_license_redirection');
   
   			if($pf_group_license_redirection){
   				 $link_redirect = get_permalink($pf_group_license_redirection);
   				 $_SESSION["current_page_url"] = $current_page_url;
   				 wp_redirect($link_redirect);
   				 exit;
   			}
   
   	    }
   		//echo "<pre>"; print_r($groups_ids); die;
   	}
     // echo "<pre>"; print_r($pf_which_group_role_can_see_this_page); die;
   }

   $show_if_author_has_booking = get_field('show_if_author_has_booking'); 

   if($show_if_author_has_booking){
      $group_admin = get_group_admin();

      if($group_admin != ""){
            $owner_id = $group_admin;
      }else{
            $owner_id = get_current_user_id();
      }
      $booking_table = $wpdb->prefix . "bookings_calendar";

      $booking_count = $wpdb->get_var(
            $wpdb->prepare(
               "SELECT COUNT(*) FROM {$booking_table} WHERE owner_id = %d OR bookings_author = %d",
               $owner_id,$owner_id
            )
      );
      if($booking_count == 0){
         wp_redirect(home_url()."/dashboard");
         exit;
      }

   }
   
   
   $body_custom_class = "gibbs_metronic_enable_header-$enable_header gibbs_metronic_enable_sidebar-$enable_sidebar gibbs_metronic_enable_footer-$enable_footer";
   $src_cls = "";
   if(isset($search_class) && $search_class != ""){
   	$src_cls = $search_class;
   }
   ?>
<body <?php if(get_option('listeo_dark_mode')){ echo 'id="dark-mode"';} ?> <?php body_class($body_custom_class); ?>>
   <?php wp_body_open(); ?>
   <!-- Wrapper -->
   <div id="wrapper" class="wrapper <?php echo $src_cls;?>">
   <?php if($pf_enable_gray_background == "1"){ ?>
   <style type="text/css">
      #wrapper, html {
      background-color: #F2F3F7 !important;
      }
   </style>
   <?php } ?>
   <?php 
      //echo $pf_enable_title; die;
      if($pf_enable_title != "1"){ ?>
   <style type="text/css">
      #titlebar h2 {
      display:none !important;
      }
   </style>
   <?php } ?>
   <?php
      do_action('listeo_after_wrapper'); ?>
   <?php
      $header_layout = get_option('listeo_header_layout') ;
      
      $sticky = get_option('listeo_sticky_header') ;
      
      if(is_singular()){
      
      	$header_layout_single = get_post_meta($post->ID, 'listeo_header_layout', TRUE);
      
      	switch ($header_layout_single) {
      		case 'on':
      		case 'enable':
      			$header_layout = 'fullwidth';
      			break;
      
      		case 'disable':
      			$header_layout = false;
      			break;
      
      		case 'use_global':
      			$header_layout = get_option('listeo_header_layout');
      			break;
      
      		default:
      			$header_layout = get_option('listeo_header_layout');
      			break;
      	}
      
      
      	$sticky_single = get_post_meta($post->ID, 'listeo_sticky_header', TRUE);
      	switch ($sticky_single) {
      		case 'on':
      		case 'enable':
      			$sticky = true;
      			break;
      
      		case 'disable':
      			$sticky = false;
      			break;
      
      		case 'use_global':
      			$sticky = get_option('listeo_sticky_header');
      			break;
      
      		default:
      			$sticky = get_option('listeo_sticky_header');
      			break;
      	}
      	if(is_singular('listing')){
      		$sticky = false;
      	}
      
      }
      
      
      $header_layout = apply_filters('listeo_header_layout_filter',$header_layout);
      $sticky = apply_filters('listeo_sticky_header_filter',$sticky);
      
      $is_sidebar = false;
      if(is_user_logged_in()){
      	// Get the user object.
      	$user = get_userdata( get_current_user_ID() );
      
      	// Get all the user roles as an array.
      	$user_roles = $user->roles;
      
      	global $wp_roles;
      
          $all_roles = $wp_roles->roles;
      
          if ( in_array( 'administrator', $user_roles, true ) ||  in_array( 'editor', $user_roles, true )) {
      	    // Do something.
      	   $is_sidebar = true;
      	}
      
      	$disable_sidebar = get_post_meta(get_the_ID(), 'listeo_disable_sidebar', TRUE);
      	if($disable_sidebar){
      		$is_sidebar = false;
      	}
      
      	if(is_singular('listing')) {
      		$listeo_disable_sidebar_single_listing = get_option("listeo_disable_sidebar_single_listing",true);
      
      		if($listeo_disable_sidebar_single_listing){
      			$is_sidebar = false;
      		}
      	}
      
      
      }
      
      
      
      if($enable_sidebar == "1" || wp_is_mobile()){
      if(is_user_logged_in()){
      	
      ?>
   <style type="text/css">
      @media only screen and (min-width: 991px)  {
      #logo{
      display: none !important;
      }
      }
      @media only screen and (max-width: 991px)  {
      .main-nav{
      display: none !important;
      }
      .head-title-bar{
      display: none !important
      }
      }
   </style>
   <script type="text/javascript">
      if(localStorage.getItem("colspad") == "1"){
      	jQuery(".wrapper").addClass("active");
      }
   </script>
   <!-- <link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets/css/styles.css"> -->
   <div class="main_container">
   <div class="sidebar">
      <div class="sidebar__inner">
         <div class="profile">
            <?php
               $logo = get_option( 'pp_logo_upload', '' );
               $logo_transparent = get_option( 'pp_dashboard_logo_upload', '' );
               
               $logo_retina = get_option( 'pp_retina_logo_upload', '' );
                          	if($logo) {
                          	?>
            <div class="img logoo">
               <a href="<?php echo home_url();?>/dashbord">
                  <img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>">
                  <svg width="40" height="40" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M361.13 310.821C373.799 357.381 345.48 405.171 297.879 417.563C250.277 429.955 201.418 402.256 188.749 355.696C176.08 309.136 227.338 345.65 274.939 333.258C322.541 320.866 348.461 264.261 361.13 310.821Z" fill="#008474"/>
                     <path d="M120.968 274.245C120.968 250.155 140.934 230.626 165.564 230.626C190.193 230.626 241.371 280.689 241.371 304.779C241.371 328.87 201.235 269.883 165.564 317.865C140.934 317.865 120.968 298.336 120.968 274.245Z" fill="#008474"/>
                     <path d="M185.703 78.0917C201.956 45.6407 222.557 192.895 322.029 162.842C355.206 178.739 368.926 217.933 352.674 250.384C336.421 282.835 296.351 296.255 263.173 280.358C213.277 256.449 169.45 110.543 185.703 78.0917Z" fill="#008474"/>
                  </svg>
            </div>
            </a>
            <?php
               }
               
               ?>
            <div class="profile_info">
               <div class="hamburger">
                  <div class="hamburger__inner">
                     <!--div class="one"></div>
                        <div class="two"></div>
                        <div class="three"></div-->
                     <i id="Layer_1" class="fas fa-chevron-left"></i>
                     
                     <i id="Layer_2" class="fas fa-chevron-right"></i>
                     
                  </div>
               </div>
            </div>
         </div>
         <?php
            echo wp_nav_menu( array(
              'theme_location' => 'editor-dashboard',
              'menu_class' => 'ul_menu',
                    'menu' => 'dfdfdf',
                    'depth' => 2,
            ) );
            ?>
      </div>
   </div>
   <div class="content_div">
   <?php
      }
      }
      if(($enable_header != "2" || wp_is_mobile()) && $src_cls == ""){
      
      
      ?>
   <!-- Header Container
      ================================================== -->
   <header id="header-container" class="<?php echo esc_attr(($sticky == true || $sticky == 1) ? "sticky-header" : ''); ?> <?php echo esc_attr($header_layout); ?>">
      <div class="popup" style="display:none; box-shadow: grey 71px 31px 10000px 600px; position:fixed; z-index:1000; width:auto; height:auto; background:white; top: 30%; left: 35%">
         <div class="row" style="background: #F7F7F7;">
            <div class="col-xs-6 col-md-11" style=" font-size: 20px;padding: 20px; color: black;">
               <span>Send melding</span>
            </div>
            <div class="col-xs-6 col-md-1" style=" font-size: 20px;padding: 20px 20px 20px 0;">
               <i class="fa fa-times closepopup" aria-hidden="true" style="background: #bfbfbf; padding: 6px; border-radius: 20px;"></i>
            </div>
         </div>
         <div class="row" style="padding: 15px 15px 5px 15px;background: white;">
            <div class="col-xs-12 col-md-12">
               <div style=" padding: 0; ">
                  <textarea class="_message" style="height: 25px;margin: 1px;" placeholder="Din melding"></textarea>
               </div>
            </div>
         </div>
         <div class="row" style="background: white;">
            <div class="col-xs-6 col-md-6" style="padding:20px; text-align: center;">
               <div class="col-xs-5 col-md-5" style="padding: 0; text-align: center;">
                  <span style="font-size: 13px;">Ny pris<span>
               </div>
               <div class="col-xs-7 col-md-7" style=" padding: 0; ">
                  <input class="_price" type="number" style="height: 25px;margin: 1px;"/>
               </div>
            </div>
            <div class="col-xs-6 col-md-6" style = "padding: 20px; text-align: center;">
               <a  class="button gray sendoffer"><i class="fa fa-paper-plane"></i> Gi nytt tilbud</a>
            </div>
         </div>
      </div>
      <!-- Header -->
      <div id="header-new" style="padding-bottom:18px;">
         <div class="container-fluid">
            <?php
               $logo = get_option( 'pp_logo_upload', '' );
               $logo_transparent = get_option( 'pp_dashboard_logo_upload', '' );
               ?>
            <!-- Left Side Content -->
            <div class="left-side" >
               <?php //if(($enable_sidebar != "2")){ ?>
               <div id="logo" data-logo-transparent="<?php echo esc_attr($logo_transparent); ?>" data-logo="<?php echo esc_attr($logo); ?>" >
                  <?php
                     $logo = get_option( 'pp_logo_upload', '' );
                     if(( is_page_template('template-home-search.php') || is_page_template('template-home-search-splash.php') )  && (get_option('listeo_home_transparent_header') == 'enable')){
                     	$logo = get_option( 'pp_dashboard_logo_upload', '' );
                     }
                     $logo_retina = get_option( 'pp_retina_logo_upload', '' );
                     if($logo) {
                         if(is_front_page()){ ?>
                  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
                  <?php } else { ?>
                  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
                  <?php }
                     } else {
                         if(is_front_page()) { ?>
                  <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                  <?php } else { ?>
                  <h2><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
                  <?php }
                     }
                     ?>
                  <?php if($enable_searchbar != "2"){ ?>        
                  <!-- <svg class="mobileSearch" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352c79.5 0 144-64.5 144-144s-64.5-144-144-144S64 128.5 64 208s64.5 144 144 144z"></path></svg> -->
                  <?php } ?>
               </div>
               <?php // }else{
                  $uri=explode("/", $_SERVER['REQUEST_URI']);
                  if($uri[1]!='dashboard'){
                  ?>
               <?php } //}?>
               <?php
                  if($pf_enable_title == "1"){ ?>
               <div class="head-title-bar">
                  <?php
                     // Adds backbutton to parent page if parent page exists
                     if ( $post->post_parent ) { ?>
                  <a href="<?php echo get_permalink( $post->post_parent ); ?>" style="font-size: 15px;border-radius: 8px;background-color: #f0f0f0; color: #666;padding: 8px 22px;font-weight: 500;border-radius: 8px; margin-right: 16px;" class="margin-right-15 back_button_gibbs" >
                  <i class="fa-solid fa-arrow-left"></i> Tilbake
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
               <?php } ?>
               <?php
                  if ( has_nav_menu( 'main-nav' ) ) {
                  
                  	$text_placeholder = array('Prøv “gymsal” eller “hoppeslott”','Prøv “selskapslokale” eller “DJ”','Prøv “kano” eller “telt”','Prøv “foodtruck” eller “klovn”');
                  
                  	shuffle($text_placeholder);
                  
                  
                  	?>
               <div class="right-side-searchbar">
                  <?php if($enable_searchbar != "2"){ ?>
                  <!--  <form action="<?php get_home_url(); ?>/listings/" id="listeo_core-search-form" class="dynamic main-search-form ajax-search margin-left-25 margin-right-25" method="GET">
                     <div class="main-search-input margin-top-0" style="padding:0px;">
                        <div class="main-search-input-item text margin-top-0 margin-bottom-0">
                              <input style="height:45px;font-size:16px;" autocomplete="off" name="keyword_search" id="keyword_search" class="keyword_search" type="text" placeholder="<?php echo $text_placeholder[0];?>" value="">
                        </div>
                        <input type="hidden" name="action" value="listeo_get_listings">
                         More Search Options / End 
                        <button class="button" style="background-color:white;"><i class="fa fa-search" style="color:#7d7d7d;display:inline-block;"></i></button>
                     </div>
                     </form> -->
                  <!--   <i class="fa fa-times"></i> -->
                  <?php } ?>    
               </div>
               <?php
	                  if($enable_sidebar != "1"){
	                  
		                  $navWrapper = 'main-nav';
		                  require_once(get_stylesheet_directory().'/mainNav.php');
	                  }
                  }
                  
                  include(get_stylesheet_directory().'/profile_menu.php');
                  ?>
               <div class="clearfix"></div>
               <!-- Main Navigation / End -->
            </div>
            <!-- Left Side Content / End -->
            <?php
               $my_account_display = get_option('listeo_my_account_display', true );
               $submit_display = get_option('listeo_submit_display', true );
               
                       ?>
         </div>
      </div>
      <!-- Header / End -->
   </header>
   <?php
      if( true == $my_account_display && !is_page_template( 'template-dashboard.php' ) ) : ?>
   <!-- Sign In Popup -->
   <div id="sign-in-dialog" class="zoom-anim-dialog mfp-hide">
      <div class="small-dialog-header">
         <h3><?php esc_html_e('Sign In','listeo'); ?></h3>
      </div>
      <!--Tabs -->
      <div class="sign-in-form style-1">
         <?php do_action('listeo_login_form'); ?>
      </div>
   </div>
   <!-- Sign In Popup / End -->
   <?php endif; ?>
   <div class="clearfix"></div>
   <!-- Header Container / End -->
   <div class="mobile-menu">
      <div class="dashboard_logo_holder">
         <div class="dashboard_logo"></div>
      </div>
      <?php
         /*	$navWrapper = 'mobile-main-nav';
                        	include('mainNav.php');*/
         ?>
      <?php
         echo wp_nav_menu( array(
           'theme_location' => 'editor-dashboard',
           'menu_class' => 'ul_menu',
                 'menu' => 'dfdfdf',
                 'depth' => 2,
         ) );
         ?>
   </div>
   <script>
      function toggleSearchBar() {
      	var topNavSearchBar = document.querySelector(".right-side-searchbar");
      	console.log('HERE MOBVILE');
      	// Search bar from not visible to visible
      	if (topNavSearchBar.classList.contains("expandedNavbar")) {
      		topNavSearchBar.classList.remove("expandedNavbar");
      	} else {
      		topNavSearchBar.classList.add("expandedNavbar");
      		topNavSearchBar.querySelector("input").focus();
      	}
      }
      
         window.dataLayer = window.dataLayer || [];
         function gtag(){dataLayer.push(arguments);}
         gtag('js', new Date());
      
         gtag('config', 'G-K621E60SQH');
   </script>
   <!-- Font awesome kit -->
   <?php } ?>
   <!-- <script src="https://kit.fontawesome.com/c8ca6754b9.js" crossorigin="anonymous"></script>  -->