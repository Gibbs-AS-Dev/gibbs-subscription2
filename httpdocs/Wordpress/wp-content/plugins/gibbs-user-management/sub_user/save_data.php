<?php 


function sub_user_create_admin()
{
  
  global $wpdb;
  $email = email_exists( $_POST["email"] );
  $username = username_exists( $_POST["email"] );
  $not_user_already =  $_POST["not_user_already"];
  if($email){

    $user = get_user_by("email",$_POST["email"]);

    if(isset($user->ID)){

      $sub_users = get_user_meta($user->ID,"sub_users",true);
      if(!empty($sub_users) && !is_array($sub_users) && $sub_users != ""){
          $sub_users = array($sub_users);
      }

       

      if(is_array($sub_users) && !empty($sub_users)){
          if(!in_array(get_current_user_id(), $sub_users)){
             $sub_users[] = get_current_user_id();
          }
          
      }else{
          $sub_users = array(get_current_user_id());
      }


      update_user_meta($user->ID,"sub_users",$sub_users);
      $response = array("error"=>0,"message"=>__("Successfully add user.","gibbs"));

    }else{
       $response = array("error"=>1,"message"=>__("User not exist!","gibbs"));
    }

   
  }elseif($username){
    $response = array("error"=>1,"message"=>__("Brukernavn er allerede brukt","gibbs"));
  }elseif($not_user_already == ""){
    $response = array("error"=>0,"not_user_already"=> "1" );
  }else{


    $random_password = wp_generate_password();
    $display_name = $_POST["first_name"]." ".$_POST["last_name"];

   

    $users_table = $wpdb->prefix . 'users';  // table name
    $wpdb->insert($users_table, array(
          'user_login'            => $_POST["email"],
          'user_pass'            => $random_password,
          'user_nicename'        => $_POST["first_name"],
          'user_email'            => $_POST["email"],
          'display_name'            => $display_name,
          'user_registered'         => date("Y-m-d H:i:s"),
        )
    );
    $user_id = $wpdb->insert_id;
    $user = new WP_User( $user_id );

    $user->set_role( 'owner' );

    update_user_meta($user_id,"first_name",$_POST["first_name"]);
    update_user_meta($user_id,"last_name",$_POST["last_name"]);
    update_user_meta($user_id,"phone",$_POST["phone"]);

    $sub_users = array(get_current_user_id());


    update_user_meta($user_id,"sub_users",$sub_users);

    /*$users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
    $wpdb->insert($users_and_users_groups, array(
      'users_groups_id'  => $_POST["users_group_id"],
      'users_id'        => $user_id,
      'role' => "4",
    ));
*/
    $response = sendmail_user($_POST,$user_id);

    $response = array("error"=>0,"message"=>__("Successfully add user.","gibbs"));
    

  }
  echo json_encode($response);
  die; 
}
add_action('wp_ajax_sub_user_create_admin', 'sub_user_create_admin', 10);
add_action('wp_ajax_nopriv_sub_user_create_admin', 'sub_user_create_admin', 10);


function remove_subuser()
{
  
  global $wpdb;

    $sub_users = get_user_meta($_POST["user_id"],"sub_users",true);

    if(!empty($sub_users) && !is_array($sub_users) && $sub_users != ""){
          $sub_users = array($sub_users);
    }

    if(!empty($sub_users)){
      if (($key = array_search(get_current_user_id(), $sub_users)) !== false) {
          unset($sub_users[$key]);
      }
      update_user_meta($_POST["user_id"],"sub_users",$sub_users);

    }
  
  $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}
add_action('wp_ajax_remove_subuser', 'remove_subuser', 10);
add_action('wp_ajax_nopriv_remove_subuser', 'remove_subuser', 10);



function new_sub_user_create()
{
  
  global $wpdb;
  $email = email_exists( $_POST["email"] );
  $username = username_exists( $_POST["email"] );
  $not_user_already =  $_POST["not_user_already"];
  if($email){

     $response = array("error"=>1,"message"=>__("User already exist!","gibbs"));

   
  }elseif($username){
    $response = array("error"=>1,"message"=>__("Brukernavn er allerede brukt","gibbs"));
  }else{


    $random_password = wp_generate_password();
    $display_name = $_POST["first_name"]." ".$_POST["last_name"];

   

    $users_table = $wpdb->prefix . 'users';  // table name
    $wpdb->insert($users_table, array(
          'user_login'            => $_POST["email"],
          'user_pass'            => $random_password,
          'user_nicename'        => $_POST["first_name"],
          'user_email'            => $_POST["email"],
          'display_name'            => $display_name,
          'user_registered'         => date("Y-m-d H:i:s"),
        )
    );
    $user_id = $wpdb->insert_id;
    $user = new WP_User( $user_id );

    $user->set_role( 'owner' );

    update_user_meta($user_id,"first_name",$_POST["first_name"]);
    update_user_meta($user_id,"last_name",$_POST["last_name"]);
    update_user_meta($user_id,"phone",$_POST["phone"]);

      $sub_users = get_user_meta(get_current_user_id(),"sub_users",true);
      if(!empty($sub_users) && !is_array($sub_users) && $sub_users != ""){
          $sub_users = array($sub_users);
      }

       

      if(is_array($sub_users) && !empty($sub_users)){
          if(!in_array($user_id, $sub_users)){
             $sub_users[] = $user_id;
          }
          
      }else{
          $sub_users = array($user_id);
      }


      update_user_meta(get_current_user_id(),"sub_users",$sub_users);
    /*$users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
    $wpdb->insert($users_and_users_groups, array(
      'users_groups_id'  => $_POST["users_group_id"],
      'users_id'        => $user_id,
      'role' => "4",
    ));
*/
    $response = sendmail_user($_POST,$user_id);

    $response = array("error"=>0,"message"=>__("Successfully add user.","gibbs"));
    

  }
  echo json_encode($response);
  die; 
}
add_action('wp_ajax_new_sub_user_create', 'new_sub_user_create', 10);
add_action('wp_ajax_nopriv_new_sub_user_create', 'new_sub_user_create', 10);