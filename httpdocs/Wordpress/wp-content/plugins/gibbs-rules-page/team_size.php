<?php

/* start members_count_groups */

$members_count_groups_db = 'members_count_groups';  // table name
$team_size_data = $wpdb->get_results("select * from $members_count_groups_db where users_groups_id ='$group_slected_id'");
$team_size = array();
if(!empty($team_size_data)){
  $team_size['members_less_10'] = $team_size_data[0]->members_less_10;
  $team_size['members_10_20'] = $team_size_data[0]->members_10_20;
  $team_size['members_20_30'] = $team_size_data[0]->members_20_30;
  $team_size['members_30_40'] = $team_size_data[0]->members_30_40;
  $team_size['members_more_40'] = $team_size_data[0]->members_more_40;

  $team_size_desc['members_less_10'] = ' '.__("Mindre enn 10","Gibbs").' ';
  $team_size_desc['members_10_20'] = ' '.__("Mellom 10 og 20","Gibbs").' ';
  $team_size_desc['members_20_30'] = ' '.__("Mellom 20 og 30","Gibbs").' ';
  $team_size_desc['members_30_40'] = ' '.__("Mellom 30 og 40","Gibbs").' ';
  $team_size_desc['members_more_40'] = ' '.__("Mer enn 40","Gibbs").' ';
}
?>
<form method="POST" class="team_size" action="<?php echo admin_url( 'admin-ajax.php' );?>">
    <input type="hidden" name="action" value="update_team_size">
    <div class="row panel panel-default">
        <div class="panel-heading">
          <div class="row">
            <div class="col-md-10">
                 <h3><?php  echo __("Prioritering av lag størrelse","Gibbs");?><br>
                    <small><?php  echo __("Ranger viktigheten fra 1-10. Høyere tall, betyr høyere prioritering.","Gibbs");?></small>
                 </h3>
            </div>
            <div class="col-md-2 btn-plus">
                <?php if(count($team_size) > 0){ ?>
                  <button class="btn btn-primary"><?php  echo __("Save","Gibbs");?></button>
                <?php } ?>
                <?php if(count($get_user_group_data) > 0){ ?>
                  <button type="button" class="btn btn-info btn-add" id="teamsizegroup"><i class="fa fa-plus"></i></button>
                <?php } ?>
            </div>
          </div> 
        </div>

        <?php foreach ($team_size as $key_1 => $team_size_value) { ?>  
          
        
           <div class="form-group col-sm-4">
              <div class="label_div1">
                <label class="rules_name1"><?php echo $team_size_desc[$key_1];?></label>
                <!-- <i class="fa fa-trash delete_age_group" age_group_id="<?php echo $age_group_value->age_group_id;?>" age_group_priorities_id="<?php echo $age_group_value->id;?>"></i> -->
                <input type="hidden" name="users_groups_id" value="<?php echo $group_slected_id;?>">
              </div>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="team_size[<?php echo $key_1;?>]" type="range" min="1" max="10" value="<?php echo $team_size_value;?>">
              </div>
             
             <!--  <p>
                <span class="value_range">value: <?php echo $age_group_value->age_group_priority;?></span> 
                <span class="delete_rule">
                  <label class="delete_lv">Delete
                    <input type="checkbox" class="delete_age_group_id" value="<?php echo $age_group_value->id;?>">
                    <span class="checkmark"></span>
                  </label>
                </span>
              </p> -->
            </div>
        <?php } ?>  
      
    </div>
</form> 