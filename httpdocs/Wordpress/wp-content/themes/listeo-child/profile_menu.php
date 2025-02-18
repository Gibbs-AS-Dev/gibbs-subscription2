<?php

if(is_user_logged_in()){

$post_id = $post->ID;
$active_group_id = 0;
$all_joined_groups_results = array();
$current_user = wp_get_current_user();
$parent_user_id = $current_user->ID;
$sub_users = array();
if ( is_user_logged_in() ) {

    

      $sub_user_ids = get_user_meta( $current_user->ID, 'sub_users',true );

      if (isset($_SESSION['parent_user_id'])) {

        $sub_user_ids = get_user_meta( $_SESSION['parent_user_id'], 'sub_users',true );



        $sub_user_data2 = get_userdata($_SESSION['parent_user_id']);

        $sub_users[$sub_user_data2->ID] = $sub_user_data2->display_name;

      }
      if(!empty($sub_user_ids)){
        
          foreach ($sub_user_ids as $key => $sub_user_id) {
              $sub_user_data = get_userdata($sub_user_id);
              $sub_users[$sub_user_data->ID] = $sub_user_data->display_name;
          }
            

      }   



    
    $all_joined_groups_results = $wpdb->get_results( 
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_groups WHERE id IN (SELECT users_groups_id FROM {$wpdb->prefix}users_and_users_groups WHERE users_id = %d AND role IN (3,4,5) )", $current_user->ID), ARRAY_A
    );
    $all_groups_ids = [];

    foreach ($all_joined_groups_results as $key => $all_joined_groups_resu) {
      $all_groups_ids[] = $all_joined_groups_resu["id"];
    }
   // echo "<pre>"; print_r($all_joined_groups_results ); die;
    $active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );
    if(!empty($active_group_id)){

      if(in_array($active_group_id ,  $all_groups_ids)){

          $groups_results = $wpdb->get_row( 
              $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_groups WHERE id = ". $active_group_id), ARRAY_A
          );

      }else{
          if(get_user_meta( $current_user->ID, '_gibbs_active_group_id',true )){

             delete_user_meta( $current_user->ID, '_gibbs_active_group_id' );
             $active_group_id = "";
          }
      }
    }
   
    
    
}
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$get_the_author_meta = get_the_author_meta('ID');;
$user_email = $current_user->user_email;
$user_display_name = $current_user->display_name;
 $user_avatar_url = "";
if(isset($current_user->listeo_core_avatar_id)){
	$custom_avatar = $current_user->listeo_core_avatar_id;
    $user_avatar_url = wp_get_attachment_url($custom_avatar,array('size' => 40));

}

// $user_avatar_url = get_avatar_url( $get_the_author_meta, array('size' => 40) );
?>  
<style>
    .last-menu-2 .trp-language-switcher{
        width: 18%;
    }
</style>
<div class="last-menu-2"><?php echo do_shortcode('[language-switcher]');?></div>
<div class="menu-last">
    <div class="btn-lst menu_drp">
     <div class="left_side_text">
        <?php 
            if($active_group_id){
        ?>
                <span><?php echo substr($user_display_name, 0, 15); ?></span>
        <?php 
            }else{
                ?>
                    <span>Aktiv bruker</span>
            <?php 
            } 
        ?>
        <?php 
            if($active_group_id){
                if(!empty($groups_results) && isset($groups_results["name"])){
                    ?>
                        
                        <span><?php echo substr($groups_results["name"], 0, 20); ?></span>
                    <?php
                }else{
                    ?>
                        <span>Active User</span>
                    <?php
                }
                
            }else{
                ?>
                    <span><?php echo substr($user_display_name, 0, 15); ?></span>
                <?php
            }
        ?>
     </div>
     <div class="right_side_text">
          <div class="cursor-pointer symbol symbol-30px symbol-md-40px">
              <?php
              $user_avatar_html = '';
              if ($user_avatar_url != "") {
                  $user_avatar_html = '<img src="'.$user_avatar_url.'" alt="user" style="width:40px"/>';
              } else {
                  $all_class_arr = array(
                      ' bg-light-danger text-danger',
                      ' bg-light-primary text-primary',
                      ' bg-light-warning text-warning',
                  );
                  $random_key = array_rand( $all_class_arr, 1 );
                  $user_avatar_html = '<div class="symbol-label fs-3 '.$all_class_arr[1].'">'.strtoupper( substr($user_display_name, 0, 1) ).'</div>';
              }
              echo $user_avatar_html;
              ?>                            
          </div>
     </div>
  </div>
    <div id="menudrpcontent" class="dropdown-content1">
    <div class="outer-drop-btn">
      
      <div class="content-top">
          <div class="cursor-pointer symbol symbol-30px symbol-md-40px">
             <?php echo $user_avatar_html; ?>                      
          </div>
          <div class="text-content-top">
             <span><?php echo substr($user_display_name, 0, 15); ?></span>
             <span><?php echo $user_email; ?></span>
            
          </div>
      </div>
      <?php if(!empty($all_joined_groups_results)){ ?>
      <div class="content-top2">
           <a href="#" class="menu-link px-5">
                <span class="menu-title position-relative  flex-column  gibbs_show_content_wrapper">
                   <!--  <span class="gibbs_show_content_menu_content">
                        Brukergruppe
                    </span> -->

                    <div class="inner_grp">
                        <span class="gibbs_show_content_menu_content">
                            Avdeling 
                        </span>
                        <!-- <span data-link="<?php echo home_url();?>/administratorer" class="manage_link"><i class="fa-solid fa-pen-to-square"></i></span> -->
                    </div>
                    <span class="fs-8 rounded bg-light end-0 show_ul gr_divv">
                        <i class="fa fa-users" aria-hidden="true"></i> 
                        <?php 
                        if(!empty($active_group_id)){
                            echo substr($groups_results["name"], 0, 20 ).' <i class="fa fa-chevron-down"></i>';
                            
                            
                        }else{
                            echo "Ingen valgt <i class=\"fa fa-chevron-down\"></i>";

                        } ?>
                    
                    </span>
                </span>
            </a>
            <ul class="groups_menu">

              <?php 
                if(!empty($all_joined_groups_results)){
                    $activeee = 0;
                    foreach($all_joined_groups_results as $ajgrk){

                        $group_id = $ajgrk["id"];
                        $bare_url = get_permalink($post_id);
                        $bare_url = add_query_arg( array( 'group_action'=>'Switch_Group', 'post_id'=>$post_id, 'new_active_group_id'=>$group_id ), $bare_url );
                        $switch_group_url = wp_nonce_url( $bare_url, 'Switch_Group_'.$post_id, 'gibbs_nonce' );
                        if($active_group_id == $group_id){

                             $activeee = 1;

                        }

                        ?>
                        
                        <li class="group_link <?php echo ($active_group_id == $group_id) ? 'active' : 'gibbs-switch-group-link'; ?> "><a href="<?php echo ($active_group_id == $group_id) ? '#' : $switch_group_url; ?>" class="menu-link d-flex px-5 gibbs-active-group <?php echo ($active_group_id == $group_id) ? 'active' : 'gibbs-switch-group-link'; ?> "><span><?php echo $ajgrk["name"]; ?></span></a></li>
                        
                        <?php
                    } 
                    $bare_url = get_permalink($post_id);
                    $bare_url = add_query_arg( array( 'group_action'=>'deselect_group', 'post_id'=>$post_id), $bare_url );
                    $switch_group_url = wp_nonce_url( $bare_url, 'Switch_Group_'.$post_id, 'gibbs_nonce' );
                    ?>
                    
                     <li class= "unselect_group_kamil group_link <?php echo ($activeee == 0) ? 'active' : ''; ?>" ><a href="<?php echo $switch_group_url; ?>" class="menu-link d-flex px-5 gibbs-switch-group-link <?php echo ($activeee == 0) ? 'active' : ''; ?>" "><span>Ingen</span></a></li>
                <?php    
                } ?>

              
            </ul>
      </div>
    <?php  } ?>

    <!-- for sub user -->

    <?php if(!empty($sub_users)){ ?>
      <div class="content-top3">
           <a href="#" class="menu-link px-5">
                <span class="menu-title position-relative  flex-column  gibbs_show_content_wrapper">
                   
                    <div class="inner_grp">
                         <span class="gibbs_show_content_menu_content">
                            Bruker
                        </span>
                        <span data-link="<?php echo home_url();?>/sub-user" class="manage_link"><i class="fa-solid fa-pen-to-square"></i></span>
                    </div>
                    <span class="fs-8 user_divv rounded bg-light end-0 show_ul">
                        <i class="fa fa-users" aria-hidden="true"></i> 
                        <?php 
                        echo substr($current_user->display_name, 0, 20 ); ?> <i class="fa fa-chevron-down"></i>
                    
                    </span>
                </span>
            </a>
            <ul class="groups_menu">

              <?php 
                if(!empty($sub_users)){

                    $activeee = 0;
                    foreach($sub_users as $sub_user_id_key => $sub_user){

                        $user_id = $sub_user_id_key;
                        $bare_url = get_permalink($post_id);
                        $bare_url = add_query_arg( array( 'user_action'=>'Switch_User', 'post_id'=>$post_id, 'new_user_id'=>$user_id, 'parent_user_id'=>$parent_user_id ), $bare_url );
                        $switch_user_url = wp_nonce_url( $bare_url, 'Switch_User_'.$post_id, 'gibbs_nonce' );

                        ?>
                        
                        <li class="group_link <?php echo (get_current_user_id() == $user_id) ? 'active' : ''; ?>"><a href="<?php echo (get_current_user_id() == $user_id) ? '#' : $switch_user_url; ?>" class="menu-link d-flex px-5 gibbs-active-group"><span><?php echo $sub_user; ?></span></a></li>
                        
                        <?php
                    } 
                    ?>
                <?php    
                } ?>

              
            </ul>
      </div>
    <?php  } ?>

    <!-- end -->
      <div class="outer-actions1">
        <ul>
          <li><a href="/min-profil/"><span>Min profil</span></li>
         <!--  <li><a href="<?php echo home_url();?>?auto_login=true"><span>Automatisk innlogging</span></li> -->
         <!--  <li><a href="/bookmarks/"><span>Favoritter</span></li> -->
          <li><a href="#"
            <?php  $_verified_user = get_user_meta(get_current_user_id(),"_verified_user",true);



                if($_verified_user == "on") {
                    ?>
                             href="javascript:void(0)">
                                Verifisert <i class="fas fa-shield-check" style="color: #32A095"></i>
                            </a>
                            
                      
                    <?php

                }else{
                    ?>
                            <a href="javascript:void(0)" id="varify_modal_btn_new">
                            <span> Brukerverifisering<i class="fas fa-shield-exclamation" style="color: #EDD035" ></i> </span></li>
                            </a>
                    <?php

                }
                ?>
          </li>
          <!-- <li><a href="#"><span><?php echo do_shortcode("[language-switcher]");?></span></li> -->
          <li><a href="<?php echo wp_logout_url(home_url());?>"><span>Logg ut</span></a></li>
                   
        </ul>
      </div>
    </div>
  </div>
  

</div>


<div class="d-flex align-items-center d-lg-none ms-2 me-n3 mobileViewToggle" title="Show header menu">
                                <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
                                    <!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->
                                    <span  ><i class="fas fa-bars"></i>

                                        <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z" fill="currentColor"></path>
                                            <path opacity="0.3" d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z" fill="currentColor"></path>
                                        </svg> -->
                                    </span>
                                    <!--end::Svg Icon-->
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
                 <?php 
                    global $wp;
                    $cr_url =  add_query_arg( $wp->query_vars, home_url( $wp->request ) );
                    $gibb_url =  home_url()."/verify";
                 //echo $button = "<button type='button' class=\"vipps_login_button\" onclick=\"window.location.href='".home_url()."?option=oauthredirect&app_name=Vipps&redirect_url=".$cr_url."';\">Vipps</button>";
                 echo $button = "<button type='button' onclick=\"window.location.href='".home_url()."/auth.php?redirect=true'\" class=\"criipto_login_button\">BankID</button>";

                 // echo do_shortcode('[miniorange_custom_login_button appname="Vipps"]'.$button.'[/miniorange_custom_login_button]');
                 // echo do_shortcode('[miniorange_custom_login_button appname="Vipps"]'.$button.'[/miniorange_custom_login_button]');
                 
                 ?>

          <!--         <?php 

                  $button2 = "<button type='button' class=\"microsoft_login_button\" onclick=\"window.location.href='".home_url()."/?option=oauthredirect&app_name=Microsoft2&redirect_url=".$cr_url."';\">Microsoft </button>";

                  echo do_shortcode('[miniorange_custom_login_button appname="Microsoft2"]'.$button2.'[/miniorange_custom_login_button]');?>
                  
                  <?php 

                  $button3 = "<button type='button' class=\"google_login_button\" onclick=\"window.location.href='".home_url()."?option=oauthredirect&app_name=Google&redirect_url=".$cr_url."';\">Google </button>";

                  echo do_shortcode('[miniorange_custom_login_button appname="Google"]'.$button3.'[/miniorange_custom_login_button] ');?> 
                  -->
                   
                </div>
                <div>
         
          <p class="verify_notification"><?php  echo __("Ved å verifisere din bruker, vil du kunne booke tjenester som krever verifisering.","Gibbs");?></p>
        </div>
              </div>
          </form>
        </div>
      </div>
<script type="text/javascript">
  jQuery(".menu_drp").click(function(){
    jQuery(this).parent().find(".dropdown-content1").toggleClass("show");
  })

 jQuery(document).ready(function($){
    $('div#kt_header_menu_mobile_toggle').on('click',function(eve){

                $('.mobile-menu').animate({
                width: "toggle"
            });
    })
    $('.content-top2 a .show_ul').on('click',function(eve){

          $(this).parent().parent().parent().find(".groups_menu").toggleClass("show");
    })
    $('.content-top3 a .show_ul').on('click',function(eve){

          $(this).parent().parent().parent().find(".groups_menu").toggleClass("show");
    })
   
    $(".mobile-menu").on('click',function(eve){

        if (jQuery(eve.target).closest(".show_menu_icon").length === 0) {
            $('.mobile-menu').animate({
                    width: "toggle"
                });
        }

                
    })
    jQuery(document).on('click', function (e) {
        if (jQuery(e.target).parent().closest(".menu-last").length === 0 && jQuery(e.target).closest("#same_user").length === 0) {
            jQuery(".user_divv").removeClass("focus_div");
            jQuery(".menu-last").find(".dropdown-content1").removeClass("show");
        }
        
    });
    jQuery(".user_divv").click(function(){
      jQuery(this).removeClass("focus_div")
    })
    jQuery(document).on('click', function (e) {
        if (jQuery(e.target).parent().closest(".content-top2 a").length === 0) {
            $(".content-top2").find(".groups_menu").removeClass("show");
        }
        if (jQuery(e.target).parent().closest(".content-top3 a").length === 0) {
            $(".content-top3").find(".groups_menu").removeClass("show");
        }
        
    });
    $('.group_link').on('click',function(eve){
        if( $(this).find("a").length > 0){
            var linkk = $(this).find("a").attr("href");
            window.location.href = linkk;
        }
    })

  })
</script>
 <script type="text/javascript">
        // Get the modal
        //var team_sizeModal = document.getElementById("team_sizeModal");
        var varify_modal = document.getElementById("varify_modal");


        //var team_sizebtn = document.getElementById("team_size");

        // Get the button that opens the modal
        var varify_modal_btn = document.getElementById("varify_modal_btn_new");

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

        jQuery(".manage_link").on("click",function(){
           var linkk = jQuery(this).data("link");
           window.location.href = linkk;
           setTimeout(function(){
            jQuery(".groups_menu").removeClass("show");
          },500)
           
        })

    </script>
<?php } 
//echo "<pre>"; print_r($all_joined_groups_results); die;
add_action("wp_footer", "add_script");
if($active_group_id == "" || $active_group_id < 1){
    require("profile_user_group_modal.php");
    if(empty($all_joined_groups_results)){

        function add_script(){ ?>
            <script>
                jQuery(".btn-listing-demo").addClass("btn-listing-demo1").removeClass("btn-listing-demo")
                jQuery(".listing_top_div").find(".button").on("click",function(e){
                    e.preventDefault();
                    jQuery("#usergroupModal2").show();

                    //debugger;
                });
                jQuery(".btn-listing-demo1").on("click",function(e){
                    e.preventDefault();
                    jQuery("#usergroupModal2").show();

                    //debugger;
                });
            </script>
        <?php   
        }
    }else{

        function add_script(){ ?>
            <script>
                jQuery(".listing_top_div").find(".button").on("click",function(e){
                    e.preventDefault();
                    setTimeout(function(){
                        jQuery("#menudrpcontent").addClass("show");
                         jQuery(".gr_divv").addClass("focus_div");
                    },100)
                    
                    //debugger;
                });
            </script>
        <?php   
        }
    }  

}?>

<script>
        jQuery(document).on("click",".criipto_login_button",function(){
           
           
               let datas = {
                  "action" : "criipto_login_action",
               }  
               jQuery.ajax({
                         type: "POST",
                         url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                         data: datas,
                         dataType: "json",
                         success: function(resultData){

                          

                            //  jQuery(".main_get_day_"+application_id).append(resultData.content);
                         }
                   });
       })
</script>
