<?php
function save_user_group_data()
{
 
      global $wpdb;

      if(!is_user_logged_in()){
        $response = array("error"=>1,"message"=>__("User not logged in!","gibbs"));
        echo json_encode($response);
        exit();
      }

      

      if($_POST["users_group_id"] != ""){



          $users_groups = $wpdb->prefix . 'users_groups';  // table name
          $wpdb->update($users_groups, array(
                'name'            => $_POST['name'],
              ),array("id"=>$_POST["users_group_id"])
          );



          if(isset($_POST["group_admin"]) && $_POST["group_admin"] != ""){

            $users__table = $wpdb->prefix . 'users';  // table name

            if(isset($_POST["group_admin_email"])){

              $gr_email = $_POST["group_admin_email"];

              //$res = $wpdb->get_row("select * from $users__table where `user_email` = '$gr_email'  && ID !=".$_POST["group_admin"]);

              // if(isset($res->ID)){

              //   // $wpdb->update($users_groups, array(
              //   //       'group_admin'            => $res->ID,
              //   //     ),array("id"=>$_POST["users_group_id"])
              //   // );

                

              // }else{
                $wpdb->update($users__table, array(
                      'user_email'            => $_POST["group_admin_email"],
                    ),array("id"=>$_POST["group_admin"])
                );
              //}
            }

            

          }

      }else{

            

            $users__table = $wpdb->prefix . 'users';  // table name

            $group_email = $_POST["group_admin_email"];

            $no_grp = false;

            if(isset($_POST["no_group"]) && $_POST["no_group"] == true){
              $group_email = "admin_".$group_email;
              $no_grp = true;

              $res = $wpdb->get_row("select * from $users__table where `user_email` = '$group_email'");

              if(isset($res->ID)){

                $group_email = "admin2_".$group_email;

              }
            }else{

              $res = $wpdb->get_row("select * from $users__table where `user_email` = '$group_email'");
            

              if(isset($res->ID)){

                  $response = array("error"=>1,"message"=>__("Email already exist","gibbs"));
                  echo json_encode($response);
                  exit();

              }

            }

            


          $users_groups = $wpdb->prefix . 'users_groups';  // table name
          $wpdb->insert($users_groups, array(
            'name'            => $_POST['name'],
            'created_by' => get_current_user_ID(),
            'superadmin' => get_current_user_ID(),
            'published_at' => date("Y-m-d H:i:s"),
            'updated_by' => get_current_user_ID(),
            
          ));
          $group_id = $wpdb->insert_id;

          $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
          $wpdb->insert($users_and_users_groups, array(
            'users_groups_id'  => $group_id,
            'users_id'        => get_current_user_ID(),
            'role' => "3",
          ));

          $license_status = get_user_meta(get_current_user_ID(),"license_status",true);

          $grp_lcs_status = 0;
          if($license_status == "active"){
            $grp_lcs_status = 1;
          }

          $users_and_users_groups_licence = $wpdb->prefix . 'users_and_users_groups_licence';  // table name
          $wpdb->insert($users_and_users_groups_licence, array(
            'users_groups_id'  => $group_id,
            'licence_id'        => 10,
            'licence_is_active' => $grp_lcs_status,
          ));

          $group_email = str_replace("admin_","admin".$group_id."_",$group_email);
          $group_email = str_replace("admin2_","admin".$group_id."_",$group_email);

          //$group_email = "group_admin_".$group_id."@gibbs.no";

            $users_table = $wpdb->prefix . 'users';  // table name
            $wpdb->insert($users_table, array(
                  'user_login'            => $group_email,
                  'user_pass'            => $group_email,
                  'user_nicename'        => $_POST['name'],
                  'user_email'            => $group_email,
                  'display_name'            => $_POST['name'],
                  'user_registered'         => date("Y-m-d H:i:s"),
                )
            );
            $user_id = $wpdb->insert_id;
            $user = new WP_User( $user_id );

            $user->set_role( 'editor' );

             $wpdb->update($users_groups, array(
              
              'group_admin'        => $user_id,
            ),array('id'  => $group_id));

            $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
            $wpdb->insert($users_and_users_groups, array(
              'users_groups_id'  => $group_id,
              'users_id'        => $user_id,
              'role' => "5",
            ));

            $current_user = wp_get_current_user();
            $active_group_id = $group_id;
            update_user_meta( $current_user->ID, '_gibbs_active_group_id', $active_group_id );

      }  

      $response = array("error"=>0,"message"=>__("Successfully saved!","gibbs"));
      echo json_encode($response);
      exit();

     /* $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();*/
}
add_action('wp_ajax_save_user_group_data', 'save_user_group_data', 10);
add_action('wp_ajax_nopriv_save_user_group_data', 'save_user_group_data', 10);

function user_management_save_group_id()
{
     $user_management_group_id = $_POST["user_management_group_id"]; 

    update_user_meta(get_current_user_ID(),"user_management_group_id_".$_POST["type"],$user_management_group_id);
    

    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}
add_action('wp_ajax_user_management_save_group_id', 'user_management_save_group_id', 10);
add_action('wp_ajax_nopriv_user_management_save_group_id', 'user_management_save_group_id', 10);


function sendmail_user($data,$user_id){
   /* if(get_option('listeo_user_registration_by_someone_else_enable') != "on"){
        return;
    }*/
    global $current_user;


    $userData = get_userdata($user_id);  

    $user_login = $userData->user_login;
    $user_email = $userData->user_email;

    $key = get_password_reset_key( $userData );


    $subject   = get_option('listeo_user_registration_by_someone_else_subject');
    $subject   = str_replace("{user_name}", $user_login, $subject);
    $subject   = str_replace("{first_name}", $data["first_name"], $subject);
    $subject   = str_replace("{last_name}", $data["last_name"], $subject);
    $subject   = str_replace("{email}", $user_email, $subject);
    $subject   = str_replace("{register_by_user}", $current_user->display_name, $subject);
    $subject   = str_replace("{register_by_user_email}", $current_user->user_email, $subject);
    $body = get_option('listeo_user_registration_by_someone_else_content');

    $body   = str_replace("{user_name}", $user_login, $body);
    $body   = str_replace("{first_name}", $data["first_name"], $body);
    $body   = str_replace("{last_name}", $data["last_name"], $body);
    $body   = str_replace("{email}", $user_email, $body);
    $body   = str_replace("{register_by_user}", $current_user->display_name, $body);
    $body   = str_replace("{register_by_user_email}", $current_user->user_email, $body);

    $link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

    $link_text  = "<a href='".$link."'>Trykk her for å registrere et passord</a>";

    $subject = html_entity_decode($subject);

    $body   = str_replace("{link}", $link_text, $body);

    $body = nl2br($body);
    $body = htmlspecialchars_decode($body,ENT_QUOTES);

    //$user_email = "performgood1202@gmail.com";


   
    Listeo_Core_Emails::send($user_email,$subject,$body);

   
    return $response = array("error"=>0,"message"=>__("Password link send successfully","gibbs"));
    


}

function user_management_create_user()
{
  
  global $wpdb;
  $email = email_exists( $_POST["email"] );
  $username = username_exists( $_POST["email"] );
  $not_user_already =  $_POST["not_user_already"];
  if($email){
    $htmll = "Fant eposten, ønsker du å legge til brukeren i gruppen? <button type='button' class='add_into_user_group btn btn-primary'  user_email='".$_POST["email"]."' users_group_id='".$_POST["users_group_id"]."'>Legg til</button>";
    $response = array("error"=>1,"message"=>$htmll);
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

    $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
    $wpdb->insert($users_and_users_groups, array(
      'users_groups_id'  => $_POST["users_group_id"],
      'users_id'        => $user_id,
      'role' => "4",
    ));

    $response = sendmail_user($_POST,$user_id);
    

  }
  echo json_encode($response);
  die; 
}
add_action('wp_ajax_user_management_create_user', 'user_management_create_user', 10);
add_action('wp_ajax_nopriv_user_management_create_user', 'user_management_create_user', 10);

function user_management_update_user_role()
{

    global $wpdb;
    $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
    $users_group_id = $_POST["users_group_id"];
    $user_id = $_POST["user_id"];

    $users_and_users_groups_table_sql = "select users_id from `$users_and_users_groups` where users_groups_id = ".$users_group_id." AND users_id=".$user_id;
    $users_and_users_groups_data = $wpdb->get_results($users_and_users_groups_table_sql);
    if(count($users_and_users_groups_data) > 0){
        $wpdb->update($users_and_users_groups, array(
            'role' => $_POST["role"],
          ),
          array('users_groups_id'  => $_POST["users_group_id"],
          'users_id'        => $_POST["user_id"]
          )
        );

    }else{
      $wpdb->insert($users_and_users_groups, array(
        'users_groups_id'  => $_POST["users_group_id"],
        'users_id'        => $_POST["user_id"],
        'role' => $_POST["role"],
      ));
    }

    $response = array("error"=>0,"message"=>__("Vellykket","gibbs"));

  

  echo json_encode($response);
  die; 
}
add_action('wp_ajax_user_management_update_user_role', 'user_management_update_user_role', 10);
add_action('wp_ajax_nopriv_user_management_update_user_role', 'user_management_update_user_role', 10);


function remove_user_from_user_group(){
  global $wpdb;
  $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
  $wpdb->delete($users_and_users_groups, 
          array('users_groups_id'  => $_POST["users_group_id"],
          'users_id'        => $_POST["user_id"]
          )
        );
  //echo "<pre>"; print_r($wpdb); die;
  $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}

add_action('wp_ajax_remove_user_from_user_group', 'remove_user_from_user_group', 10);
add_action('wp_ajax_nopriv_remove_user_from_user_group', 'remove_user_from_user_group', 10);

function display_row_checkbox(){
   update_user_meta(get_current_user_ID(),"display_row_checkbox",json_encode($_POST["checkboxs"]));
   die;
}

add_action('wp_ajax_display_row_checkbox', 'display_row_checkbox', 10);
add_action('wp_ajax_nopriv_display_row_checkbox', 'display_row_checkbox', 10);


function add_into_user_group(){
  global $wpdb;
  $user_email = $_POST['user_email'];
  $users_table = $wpdb->prefix . 'users';  // table name
  $users_table_sql = "select ID from `$users_table` where user_email = '".$user_email."'";

  $users_table_data = $wpdb->get_row($users_table_sql);
  if(isset($users_table_data->ID)){

    $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
    $users_group_id = $_POST["users_group_id"];
    $user_id = $users_table_data->ID;

    $users_and_users_groups_table_sql = "select users_id from `$users_and_users_groups` where users_groups_id = ".$users_group_id." AND users_id=".$user_id;
    $users_and_users_groups_data = $wpdb->get_results($users_and_users_groups_table_sql);

    if(count($users_and_users_groups_data) < 1){
        $wpdb->insert($users_and_users_groups, array(
          'users_groups_id'  => $_POST["users_group_id"],
          'users_id'        => $user_id,
          'role' => "4",
        ));

    }else{

      foreach ($users_and_users_groups_data as $key => $users_and_users_gro) {
           $wpdb->update($users_and_users_groups, array(
              'role' => "4",
            ),
            array('users_groups_id'  => $users_group_id,
            'users_id'        => $users_and_users_gro->users_id
            )
          );
      }

    }

    $response = array("error"=>0,"message"=>__("Lagt til!","gibbs"));


  }else{
    $response = array("error"=>1,"message"=>__("Eposten finnes ikke!","gibbs"));
  }
  echo json_encode($response);
  die; 
  
}

add_action('wp_ajax_add_into_user_group', 'add_into_user_group', 10);
add_action('wp_ajax_nopriv_add_into_user_group', 'add_into_user_group', 10);