<?php
$team_level_sql = "SELECT team_level.name, team_levels_priorities_and_override_rules.* FROM `team_level` left join team_levels_priorities_and_override_rules ON team_level.id = team_levels_priorities_and_override_rules.team_level_id where team_level.users_groups_id ='$group_slected_id'";
$team_level_data = $wpdb->get_results($team_level_sql);

$duration_override_sql = "SELECT * FROM `duration_override_rules`  where users_groups_id ='$group_slected_id'";
$duration_override_data = $wpdb->get_results($duration_override_sql);
?>
<form method="POST" class="gender_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
      <input type="hidden" name="action" value="update_league">
      <div class="row panel panel-default">
          <div class="panel-heading">
             <div class="row">
                <div class="col-md-10">
                     <h3><?php  echo __("Nivå overstyring","Gibbs");?><br>
                        <small><?php  echo __("Disse reglene kan overstyrer poengsummen til søker. Eksempelvis hvis en søker må ha 6 timer, velger du 6 timer. Da vil søker få 6 timer uansett deres poengsum.","Gibbs");?></small>
                     </h3>
                </div>
                <div class="col-md-2 btn-plus">
                      <?php if(count($team_level_data) > 0){ ?>
                        <button class="btn btn-primary"><?php  echo __("Save","Gibbs");?></button>
                      <?php } ?>
                      <?php if(count($get_user_group_data) > 0){ ?>
                        <button type="button" class="btn btn-info btn-add" id="league"><i class="fa fa-plus"></i></button>
                      <?php } ?>
                </div>
              </div>
          </div>

          <?php foreach ($team_level_data as $key => $team_level_value) { ?>  
            
          
            <div class="form-group col-sm-4 level_div_<?php echo $team_level_value->id;?>">
              <div class="label_div1">
                <label class="rules_name1"><?php echo $team_level_value->name;?></i></label>
              </div>
              <input type="hidden" name="league[<?php echo $team_level_value->id;?>][id]" value="<?php echo $team_level_value->id;?>">

              <select class="form-control" name="league[<?php echo $team_level_value->id;?>][duration_override_rule_id]">

                <option value=""><?php  echo __("Select","Gibbs");?></option>

                <?php foreach ($duration_override_data as $duration_override_data_value) { ?>
                   <option value="<?php echo $duration_override_data_value->id;?>" <?php if($team_level_value->duration_override_rule_id == $duration_override_data_value->id){ echo 'selected';}?>><?php echo $duration_override_data_value->name;?> </option>
                <?php } ?>
                
              </select>
             
            
            </div>
          <?php } ?>
        
      </div>
  </form> 

<!-- Aga group modal -->
<!-- The Modal -->
<div id="leagueModal" class="modal rule_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_league">&times;</span>
      <h2><?php  echo __("League regler","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <form method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
          <input type="hidden" name="action" value="save_league">
          <div class="row">
            <div class="form-group col-sm-12">
              <label><?php  echo __("Name","Gibbs");?></label>
              <input class="form-control" name="name" type="text" required="">
              <input class="form-control" name="users_groups_id" type="hidden" value="<?php echo $group_slected_id;?>" required="">
            </div>
            <div class="form-group col-sm-6">
                <label><?php  echo __("Select rule","Gibbs");?></label>
                <select class="form-control" name="rule">
                  <option value="e"><?php  echo __("Must have","Gibbs");?></option>
                  <option value="g"><?php  echo __("No less than","Gibbs");?></option>
                  <option value="l"><?php  echo __("No more than","Gibbs");?></option>
                </select>

            </div>
            
            <div class="form-group col-sm-6">
              <label><?php  echo __("Select amount of minutes","Gibbs");?></label>
              <select class="form-control" name="value">
                  <?php for($i = 1;$i <=100;$i++ ){ ?>
                    <option value="<?php echo ($i*60);?>"><?php echo ($i*60);?> <?php  echo __("Minutes","Gibbs");?></option>
                  <?php } ?>
              </select>
            </div>
            <div class="form-group col-sm-12 age_btn_submit">
              <input class="form-control" type="submit" value="<?php  echo __("Submit","Gibbs");?>">
            </div>
          </div>
      </form>
    </div>
  </div>

</div>
<!-- end -->
<script type="text/javascript">
  // Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
var leagueModal = document.getElementById("leagueModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var league = document.getElementById("league");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_league = document.getElementsByClassName("close_league")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
league.onclick = function() {
  leagueModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
/*span.onclick = function() {
  team_sizeModal.style.display = "none";
}*/
close_league.onclick = function() {
  leagueModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  /*if (event.target == team_sizeModal) {
    team_sizeModal.style.display = "none";
  } */
  if (event.target == leagueModal) {
    leagueModal.style.display = "none";
  }
}
jQuery(".delete_gender").click(function(){
  var delete_c1 = confirm("Want to delete?");
  if (delete_c1) {
     
    jQuery(".delete_form").remove();
    

    var f = document.createElement("form");
    f.setAttribute('class',"delete_form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

    var i = document.createElement("input"); //input element, text
    i.setAttribute('type',"text");
    i.setAttribute('name',"gender_id");
    i.setAttribute('value',jQuery(this).attr("gender_id"));
    f.appendChild(i);

    var i2 = document.createElement("input"); //input element, text
    i2.setAttribute('type',"text");
    i2.setAttribute('name',"gender_priorities_id");
    i2.setAttribute('value',jQuery(this).attr("gender_priorities_id"));
    f.appendChild(i2);

    var a_i = document.createElement("input"); //input element, text
    a_i.setAttribute('type',"hidden");
    a_i.setAttribute('name',"action");
    a_i.setAttribute('value',"delete_gender");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
  }  
})
</script>