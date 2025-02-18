<?php
/*
Plugin Name: Gibbs rules
Description: Gibbs rules shortcode [rules-gibbs]
Version: 1.2
Author: Gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function wpm_roles_gibbs_stylesheet(){
    wp_enqueue_style( 'rules-style', plugin_dir_url(__FILE__) . 'css/style.css' ,[],time());
}
add_action( 'wp_enqueue_scripts', 'wpm_roles_gibbs_stylesheet' );

function all_rules(){
    require("rules_gibbs.php");
}
add_shortcode("rules-gibbs","all_rules");

function save_rule()
{
      global $wpdb;
      $rules_gibbs_db = $wpdb->prefix . 'rules_gibbs';  // table name
      $wpdb->insert($rules_gibbs_db, array(
        'rule_type'            => $_POST['rule_type'],
        'rule_name'            => $_POST['rule_name'],
        'rule_value'            => $_POST['rule_value'],
        
      ));
      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_rule', 'save_rule', 10);
add_action('wp_ajax_nopriv_save_rule', 'save_rule', 10);

function save_age_group()
{
    
      global $wpdb;
      $age_group_db = 'age_group';  // table name
      $wpdb->insert($age_group_db, array(
        'name'            => $_POST['name'],
        'users_groups_id' => $_POST['users_groups_id'],
        'created_by' => get_current_user_ID(),
        'published_at' => date("Y-m-d H:i:s"),
        'updated_by' => get_current_user_ID(),
        
      ));
      $group_id = $wpdb->insert_id;
      $age_group_priorities_db = 'age_group_priorities';  // table name
      $wpdb->insert($age_group_priorities_db, array(
        'age_group_priority'  => $_POST['age_group_priority'],
        'age_group_id'        => $group_id,
        'users_groups_id' => $_POST['users_groups_id'],
        'published_at' => date("Y-m-d H:i:s"),
        'created_by' => get_current_user_ID(),
        'updated_by' => get_current_user_ID(),
      ));

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_age_group', 'save_age_group', 10);
add_action('wp_ajax_nopriv_save_age_group', 'save_age_group', 10);

function save_user_group()
{
    
      global $wpdb;
      update_user_meta(get_current_user_ID(),"cr_user_group",$_POST["users_groups_id"]);

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_user_group', 'save_user_group', 10);
add_action('wp_ajax_nopriv_save_user_group', 'save_user_group', 10);

function update_age_group()
{
    if(isset($_POST["age_group"]) && !empty($_POST["age_group"])){
      global $wpdb;
      foreach ($_POST["age_group"] as $key => $groups) {
        $age_group_db = 'age_group';  // table name
        $wpdb->update($age_group_db, array(
          'name'            => $groups["name"],
          ),array("id"=>$groups["age_group_id"])
        );

        $age_group_priorities = 'age_group_priorities';  // table name
        $wpdb->update($age_group_priorities, array(
          'age_group_priority'   => $groups["age_group_priority"],
          ),array("id"=>$groups["id"])
        );
      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_add_team_size', 'add_team_size', 10);
add_action('wp_ajax_nopriv_add_team_size', 'add_team_size', 10);
function add_team_size()
{
    global $wpdb;
      $group_slected_id = $_POST['users_groups_id'];
      $members_count_groups_sql = "SELECT * from `members_count_groups` where users_groups_id ='$group_slected_id'";
      $members_count_groups_data = $wpdb->get_results($members_count_groups_sql);

      $members_count_groups = 'members_count_groups';  // table name
      if(empty($members_count_groups_data)){

          $wpdb->insert($members_count_groups, array(
                  'members_less_10' => "1",
                  'members_10_20' => "1",
                  'members_20_30' => "1",
                  'members_30_40' => "1",
                  'members_more_40' => "1",
                  'users_groups_id' => $_POST['users_groups_id'],
                  'created_by' => get_current_user_ID(),
                  'published_at' => date("Y-m-d H:i:s"),
                  'updated_by' => get_current_user_ID(),
                  
                ));
      }
  
    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}

add_action('wp_ajax_update_team_size', 'update_team_size', 10);
add_action('wp_ajax_nopriv_update_team_size', 'update_team_size', 10);



function update_team_size()
{
    if(isset($_POST["team_size"]) && !empty($_POST["team_size"])){
      global $wpdb;
      foreach ($_POST["team_size"] as $key => $team_size) {
        $members_count_groups_db = 'members_count_groups';  // table name
        $wpdb->update($members_count_groups_db, array(
          $key            => $team_size,
          ),array("users_groups_id"=>$_POST["users_groups_id"])
        );
      }
      

    }
  
    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}

add_action('wp_ajax_update_age_group', 'update_age_group', 10);
add_action('wp_ajax_nopriv_update_age_group', 'update_age_group', 10);



function update_rule()
{
    if(isset($_POST["rules"]) && !empty($_POST["rules"])){
      global $wpdb;
      foreach ($_POST["rules"] as $key => $rule) {
        $rules_gibbs_db = $wpdb->prefix . 'rules_gibbs';  // table name
        $wpdb->update($rules_gibbs_db, array(
          'rule_name'            => $rule["rule_name"],
          'rule_value'            => $rule["rule_value"],
          
          ),array("id"=>$rule["rule_id"])
        );
      }
      

    }
  
    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}
add_action('wp_ajax_update_rule', 'update_rule', 10);
add_action('wp_ajax_nopriv_update_rule', 'update_rule', 10);

function delete_age_group()
{
  
  if(isset($_POST["age_group_id"]) && isset($_POST["age_group_priorities_id"])){

    global $wpdb;
    $age_group_priorities = 'age_group_priorities';  // table name
    $wpdb->delete($age_group_priorities, array("id"=>$_POST["age_group_priorities_id"]));
    

    $age_group = 'age_group';  // table name
    $wpdb->delete($age_group, array("id"=>$_POST["age_group_id"]));



   
  }

  
    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}
add_action('wp_ajax_delete_age_group', 'delete_age_group', 10);
add_action('wp_ajax_nopriv_delete_age_group', 'delete_age_group', 10);

/*gender*/
function save_gender()
{
    
      global $wpdb;
      $type = 'type';  // table name
      $wpdb->insert($type, array(
        'name'            => $_POST['name'],
        'users_groups_id' => $_POST['users_groups_id'],
        'created_by' => get_current_user_ID(),
        'published_at' => date("Y-m-d H:i:s"),
        'updated_by' => get_current_user_ID(),
        
      ));
      $gender_id = $wpdb->insert_id;
      $type_priorities = 'type_priorities';  // table name
      $wpdb->insert($type_priorities, array(
        'gender_priority'  => $_POST['gender_priority'],
        'gender_id'        => $gender_id,
        'users_groups_id' => $_POST['users_groups_id'],
        'published_at' => date("Y-m-d H:i:s"),
        'created_by' => get_current_user_ID(),
        'updated_by' => get_current_user_ID(),
      ));

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_gender', 'save_gender', 10);
add_action('wp_ajax_nopriv_save_gender', 'save_gender', 10);

function update_gender()
{

    if(isset($_POST["gender"]) && !empty($_POST["gender"])){
      global $wpdb;
      foreach ($_POST["gender"] as $key => $groups) {
        $type = 'type';  // table name
        $wpdb->update($type, array(
          'name'            => $groups["name"],
          ),array("id"=>$groups["gender_id"])
        );

        $type_priorities = 'type_priorities';  // table name
        $wpdb->update($type_priorities, array(
          'gender_priority'   => $groups["gender_priority"],
          ),array("id"=>$groups["id"])
        );
      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_update_gender', 'update_gender', 10);
add_action('wp_ajax_nopriv_update_gender', 'update_gender', 10);

function delete_gender()
{
  
  if(isset($_POST["gender_id"]) && isset($_POST["gender_priorities_id"])){

    global $wpdb;
    $type_priorities = 'type_priorities';  // table name
    $wpdb->delete($type_priorities, array("id"=>$_POST["gender_priorities_id"]));
    

    $type = 'type';  // table name
    $wpdb->delete($type, array("id"=>$_POST["gender_id"]));
   
  }

  
    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}
add_action('wp_ajax_delete_gender', 'delete_gender', 10);
add_action('wp_ajax_nopriv_delete_gender', 'delete_gender', 10);
/*end*/

/*level*/
function save_level()
{
      global $wpdb;
      $team_level = 'team_level';  // table name
      $wpdb->insert($team_level, array(
        'name'            => $_POST['name'],
        'users_groups_id' => $_POST['users_groups_id'],
        'created_by' => get_current_user_ID(),
        'published_at' => date("Y-m-d H:i:s"),
        'updated_by' => get_current_user_ID(),
        
      ));
      $level_id = $wpdb->insert_id;
      $team_levels_priorities_and_override_rules = 'team_levels_priorities_and_override_rules';  // table name
      $wpdb->insert($team_levels_priorities_and_override_rules, array(
        'team_level_priority'  => $_POST['team_level_priority'],
        'team_level_id'        => $level_id,
        'users_groups_id' => $_POST['users_groups_id'],
        'published_at' => date("Y-m-d H:i:s"),
        'created_by' => get_current_user_ID(),
        'updated_by' => get_current_user_ID(),
      ));

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_level', 'save_level', 10);
add_action('wp_ajax_nopriv_save_level', 'save_level', 10);

function update_level()
{
    if(isset($_POST["level"]) && !empty($_POST["level"])){
      global $wpdb;
      foreach ($_POST["level"] as $key => $groups) {
        $team_level = 'team_level';  // table name
        $wpdb->update($team_level, array(
          'name'            => $groups["name"],
          ),array("id"=>$groups["team_level_id"])
        );

        $team_levels_priorities_and_override_rules = 'team_levels_priorities_and_override_rules';  // table name
        $wpdb->update($team_levels_priorities_and_override_rules, array(
          'team_level_priority'   => $groups["team_level_priority"],
          ),array("id"=>$groups["id"])
        );
      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_update_level', 'update_level', 10);
add_action('wp_ajax_nopriv_update_level', 'update_level', 10);

function delete_level()
{

  
  if(isset($_POST["team_level_id"]) && isset($_POST["team_levels_priorities_id"])){

    global $wpdb;
    $team_levels_priorities_and_override_rules = 'team_levels_priorities_and_override_rules';  // table name
    $wpdb->delete($team_levels_priorities_and_override_rules, array("id"=>$_POST["team_levels_priorities_id"]));
    

    $team_level = 'team_level';  // table name
    $wpdb->delete($team_level, array("id"=>$_POST["team_level_id"]));
   
  }

  
    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}
add_action('wp_ajax_delete_level', 'delete_level', 10);
add_action('wp_ajax_nopriv_delete_level', 'delete_level', 10);
/*end*/

/*level*/
function save_sports()
{
      global $wpdb;
      $sport = 'sport';  // table name
      $wpdb->insert($sport, array(
        'name'            => $_POST['name'],
        'users_groups_id' => $_POST['users_groups_id'],
        'created_by' => get_current_user_ID(),
        'published_at' => date("Y-m-d H:i:s"),
        'updated_by' => get_current_user_ID(),
        
      ));
      $sport_id = $wpdb->insert_id;
      $sports_priorities = 'sports_priorities';  // table name
      $wpdb->insert($sports_priorities, array(
        'sport_priority'  => $_POST['sport_priority'],
        'sport_id'        => $sport_id,
        'users_groups_id' => $_POST['users_groups_id'],
        'published_at' => date("Y-m-d H:i:s"),
        'created_by' => get_current_user_ID(),
        'updated_by' => get_current_user_ID(),
      ));

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_sports', 'save_sports', 10);
add_action('wp_ajax_nopriv_save_sports', 'save_sports', 10);

function update_sports()
{
    if(isset($_POST["sports"]) && !empty($_POST["sports"])){
      global $wpdb;
      foreach ($_POST["sports"] as $key => $groups) {
        $sport = 'sport';  // table name
        $wpdb->update($sport, array(
          'name'            => $groups["name"],
          ),array("id"=>$groups["sport_id"])
        );

        $sports_priorities = 'sports_priorities';  // table name
        $wpdb->update($sports_priorities, array(
          'sport_priority'   => $groups["sport_priority"],
          ),array("id"=>$groups["id"])
        );
      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_update_sports', 'update_sports', 10);
add_action('wp_ajax_nopriv_update_sports', 'update_sports', 10);

function delete_sports()
{

  if(isset($_POST["sport_id"]) && isset($_POST["sport_priority_id"])){

    global $wpdb;
    $sports_priorities = 'sports_priorities';  // table name
    $wpdb->delete($sports_priorities, array("id"=>$_POST["sport_priority_id"]));
    

    $sport = 'sport';  // table name
    $wpdb->delete($sport, array("id"=>$_POST["sport_id"]));
   
  }

  
    $location = $_SERVER['HTTP_REFERER'];
    wp_safe_redirect($location);
    exit();
}
add_action('wp_ajax_delete_sports', 'delete_sports', 10);
add_action('wp_ajax_nopriv_delete_sports', 'delete_sports', 10);
/*end*/
/* league start*/
/*level*/
function save_league()
{
      global $wpdb;
      $duration_override_rules = 'duration_override_rules';  // table name
      $wpdb->insert($duration_override_rules, array(
        'name'            => $_POST['name'],
        'users_groups_id' => $_POST['users_groups_id'],
        'value' => $_POST['value'],
        'rule' => $_POST['rule'],
        'created_by' => get_current_user_ID(),
        'published_at' => date("Y-m-d H:i:s"),
        'updated_by' => get_current_user_ID(),
        
      ));
     

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_league', 'save_league', 10);
add_action('wp_ajax_nopriv_save_league', 'save_league', 10);

function update_league()
{
    if(isset($_POST["league"]) && !empty($_POST["league"])){
      


      foreach ($_POST["league"] as $key => $groups) {
        global $wpdb;
        $team_levels_priorities_and_override_rules = 'team_levels_priorities_and_override_rules';  // table name
        $wpdb->update($team_levels_priorities_and_override_rules, array(
          'duration_override_rule_id' => $groups["duration_override_rule_id"],
          ),array("id"=>$groups["id"])
        );

      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_update_league', 'update_league', 10);
add_action('wp_ajax_nopriv_update_league', 'update_league', 10);
/* end league*/
/*level*/
function save_advanced()
{

      global $wpdb;
      $group_slected_id = $_POST['users_groups_id'];
      $scores_weights_sql = "SELECT * from `scores_weights` where users_groups_id ='$group_slected_id'";
      $scores_weights_data = $wpdb->get_results($scores_weights_sql);

      $scores_weights = 'scores_weights';  // table name
      if(!empty($scores_weights_data)){

        if(isset($_POST["advanced_column"]) && !empty($_POST["advanced_column"])){
          foreach ($_POST["advanced_column"] as $key => $column_value) {
            $wpdb->update($scores_weights, array(
                $column_value  => $_POST['value'],
                'updated_by' => get_current_user_ID(),
                
                ),array("users_groups_id"=>$_POST['users_groups_id'])
            );
          }
        }

        
        
        
      }else{

        if(isset($_POST["advanced_column"]) && !empty($_POST["advanced_column"])){
          foreach ($_POST["advanced_column"] as $key => $column_value) {
              $scores_weights_sql = "SELECT * from `scores_weights` where users_groups_id ='$group_slected_id'";
              $scores_weights_data = $wpdb->get_results($scores_weights_sql);
              if(!empty($scores_weights_data)){

                  $wpdb->update($scores_weights, array(
                      $column_value  => $_POST['value'],
                      'updated_by' => get_current_user_ID(),
                      
                      ),array("users_groups_id"=>$_POST['users_groups_id'])
                  );
              }else{
                $wpdb->insert($scores_weights, array(
                  $column_value  => $_POST['value'],
                  'users_groups_id' => $_POST['users_groups_id'],
                  'created_by' => get_current_user_ID(),
                  'published_at' => date("Y-m-d H:i:s"),
                  'updated_by' => get_current_user_ID(),
                  
                ));

              }    
            
          }
        }

     
        

      }
      
     

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}

function add_advanced()
{

      global $wpdb;
      $group_slected_id = $_POST['users_groups_id'];
      $scores_weights_sql = "SELECT * from `scores_weights` where users_groups_id ='$group_slected_id'";
      $scores_weights_data = $wpdb->get_results($scores_weights_sql);

      $scores_weights = 'scores_weights';  // table name
      if(empty($scores_weights_data)){

          $wpdb->insert($scores_weights, array(
                  "members_count_w_dur_score"  => "1",
                  "gender_w_dur_score"  => "1",
                  "age_group_w_dur_score"  => "1",
                  "team_level_w_dur_score"  => "1",
                  "members_count_w_pri_score"  => "1",
                  "gender_w_pri_score"  => "1",
                  "age_group_w_pri_score"  => "1",
                  "team_level_w_pri_score"  => "1",
                  "sport_priority_w_pri_score"  => "1",
                  'users_groups_id' => $_POST['users_groups_id'],
                  'created_by' => get_current_user_ID(),
                  'published_at' => date("Y-m-d H:i:s"),
                  'updated_by' => get_current_user_ID(),
                  
                ));
      }
      
     

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_add_advanced', 'add_advanced', 10);
add_action('wp_ajax_nopriv_add_advanced', 'add_advanced', 10);

function update_advanced()
{
    if(isset($_POST["advanced"]) && !empty($_POST["advanced"])){
      global $wpdb;
      foreach ($_POST["advanced"] as $key_adav => $adav) {
        $scores_weights = 'scores_weights';  // table name
        $wpdb->update($scores_weights, array(
          $key_adav  => $adav,
          ),array("users_groups_id"=>$_POST["users_groups_id"])
        );

      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_update_advanced', 'update_advanced', 10);
add_action('wp_ajax_nopriv_update_advanced', 'update_advanced', 10);
/* end league*/


/*level*/
function add_duration()
{

      global $wpdb;
      $group_slected_id = $_POST['users_groups_id'];
      $duration_score_rules_sql = "SELECT * from `duration_score_rules` where users_groups_id ='$group_slected_id'";
      $duration_score_rules_data = $wpdb->get_results($duration_score_rules_sql);

      $duration_score_rules = 'duration_score_rules';  // table name
      if(empty($duration_score_rules_data)){

          $wpdb->insert($duration_score_rules, array(
                  'score_less_10' => "6000",
                  'score_10_20' => "6000",
                  'score_20_30' => "6000",
                  'score_30_40' => "6000",
                  'score_40_50' => "6000",
                  'score_50_60' => "6000",
                  'score_60_70' => "6000",
                  'score_70_80' => "6000",
                  'score_80_90' => "6000",
                  'score_more_90' => "6000",
                  'users_groups_id' => $_POST['users_groups_id'],
                  'created_by' => get_current_user_ID(),
                  'published_at' => date("Y-m-d H:i:s"),
                  'updated_by' => get_current_user_ID(),
                  
                ));
      }
      
     

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_add_duration', 'add_duration', 10);
add_action('wp_ajax_nopriv_add_duration', 'add_duration', 10);

function update_duration()
{
    if(isset($_POST["duration"]) && !empty($_POST["duration"])){
      global $wpdb;
      foreach ($_POST["duration"] as $key_adav => $adav) {
        $duration_score_rules = 'duration_score_rules';  // table name
        $wpdb->update($duration_score_rules, array(
          $key_adav  => $adav,
          ),array("users_groups_id"=>$_POST["users_groups_id"])
        );

      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_update_duration', 'update_duration', 10);
add_action('wp_ajax_nopriv_update_duration', 'update_duration', 10);
/* end league*/
/* start season*/
function save_season()
{
      global $wpdb;
    
        $activate = "0";
        $seasons = 'seasons';  // table name
        $wpdb->insert($seasons, array(
          "name"  => $_POST["name"],
          "season_start"  => $_POST["season_start"],
          "season_end"  => $_POST["season_end"],
          "season_deadline"  => $_POST["season_deadline"],
          "users_groups_id"  => $_POST["users_groups_id"],
          "season_end"  => $_POST["season_end"],
          "on/off"  => $activate,
          'created_by' => get_current_user_ID(),
          'published_at' => date("Y-m-d H:i:s"),
          'updated_by' => get_current_user_ID(),
        ));

    

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_save_season', 'save_season', 10);
add_action('wp_ajax_nopriv_save_season', 'save_season', 10);
function update_season()
{
    if(isset($_POST["season"]) && !empty($_POST["season"])){
      global $wpdb;
      foreach ($_POST["season"] as $key_season => $season_value) {
        if(isset($season_value["on/off"])){
           $activate = "1";
        }else{
          $activate = "0";
        }
        $seasons = 'seasons';  // table name
        $wpdb->update($seasons, array(
          "name"  => $season_value["name"],
          "season_start"  => $season_value["season_start"],
          "season_end"  => $season_value["season_end"],
          "season_deadline"  => $season_value["season_deadline"],
          "on/off"  => $activate,
          ),array("id"=>$season_value["id"])
        );

      }
      

    }

      $location = $_SERVER['HTTP_REFERER'];
      wp_safe_redirect($location);
      exit();
}
add_action('wp_ajax_update_season', 'update_season', 10);
add_action('wp_ajax_nopriv_update_season', 'update_season', 10);
/* end season*/
