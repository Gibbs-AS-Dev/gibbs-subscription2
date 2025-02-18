<?php
$scores_weights_sql = "SELECT * from `scores_weights` where users_groups_id ='$group_slected_id'";
$scores_weights_data = $wpdb->get_results($scores_weights_sql);
?>
<form method="POST" class="gender_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
      <input type="hidden" name="action" value="update_advanced">
      <input  name="users_groups_id" type="hidden" value="<?php echo $group_slected_id;?>" required="">
      <div class="row panel panel-default">
          <div class="panel-heading">
             <div class="row">
                <div class="col-md-10">
                     <h3><?php  echo __("Vekter","Gibbs");?><br>
                        <small><?php  echo __("Her kan du bestemme hvilken fordelingsnøkkel som er viktigst. ","Gibbs");?></small>
                     </h3> 
                </div>
                <div class="col-md-2 btn-plus">
                      <?php if(count($scores_weights_data) > 0){ ?>
                        <button class="btn btn-primary"><?php  echo __("Save","Gibbs");?></button>
                      <?php } ?>
                      <?php if(count($get_user_group_data) > 0){ ?>
                        <button type="button" class="btn btn-info btn-add" id="advanced1"><i class="fa fa-plus"></i></button>
                      <?php } ?>
                </div>
              </div>
          </div>
 
          
          <?php  if(isset($scores_weights_data[0]->members_count_w_dur_score) && $scores_weights_data[0]->members_count_w_dur_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Vekt for lag størrelse","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[members_count_w_dur_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->members_count_w_dur_score;?>">
              </div>
            </div>

          <?php } ?> 


          <?php  if(isset($scores_weights_data[0]->gender_w_dur_score) && $scores_weights_data[0]->gender_w_dur_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Vekt for type","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[gender_w_dur_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->gender_w_dur_score;?>">
              </div>
            </div>

          <?php } ?>

          <?php  if(isset($scores_weights_data[0]->age_group_w_dur_score) && $scores_weights_data[0]->age_group_w_dur_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Vekt for aldersgruppe","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[age_group_w_dur_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->age_group_w_dur_score;?>">
              </div>
            </div>

          <?php } ?>

          <?php  if(isset($scores_weights_data[0]->team_level_w_dur_score) && $scores_weights_data[0]->team_level_w_dur_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Vekt for nivå","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[team_level_w_dur_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->team_level_w_dur_score;?>">
              </div>
            </div>

          <?php } ?>
        
      </div>

      <div class="row panel panel-default">
          <div class="panel-heading">
             <div class="row">
                <div class="col-md-10">
                     <h3><?php  echo __("Serverings prioritering","Gibbs");?><br>
                        <small><?php  echo __("Her vil man kunne bestemme hvem som får tider først.","Gibbs");?></small>
                     </h3>
                </div>
              </div>
          </div>
 
          
          <?php  if(isset($scores_weights_data[0]->members_count_w_pri_score) && $scores_weights_data[0]->members_count_w_pri_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Lag størrelse","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[members_count_w_pri_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->members_count_w_pri_score;?>">
              </div>
            </div>

          <?php } ?> 


          <?php  if(isset($scores_weights_data[0]->gender_w_pri_score) && $scores_weights_data[0]->gender_w_pri_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Type søker","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[gender_w_pri_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->gender_w_pri_score;?>">
              </div>
            </div>

          <?php } ?>

          <?php  if(isset($scores_weights_data[0]->age_group_w_pri_score) && $scores_weights_data[0]->age_group_w_pri_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Aldersgruppe","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[age_group_w_pri_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->age_group_w_pri_score;?>">
              </div>
            </div>

          <?php } ?>

          <?php  if(isset($scores_weights_data[0]->team_level_w_pri_score) && $scores_weights_data[0]->team_level_w_pri_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Nivå","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[team_level_w_pri_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->team_level_w_pri_score;?>">
              </div>
            </div>

          <?php } ?>

          <?php  if(isset($scores_weights_data[0]->sport_priority_w_pri_score) && $scores_weights_data[0]->sport_priority_w_pri_score != ""){ ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php  echo __("Idrett","Gibbs");?></label>
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="advanced[sport_priority_w_pri_score]" type="range" min="1" max="10" value="<?php echo $scores_weights_data[0]->sport_priority_w_pri_score;?>">
              </div>
            </div>

          <?php } ?>
        
      </div>
  </form> 

<!-- Aga group modal -->
<!-- The Modal -->
<div id="advancedModal" class="modal rule_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_advanced">&times;</span>
      <h2><?php  echo __("Prioritering av aldersgrupper","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <form method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
          <input type="hidden" name="action" value="save_advanced">
          <div class="row">
            <div class="form-group col-sm-12">
              <label><?php  echo __("Select Column","Gibbs");?></label>
              <select class="form-control advanced_column" name="advanced_column[]" required="" multiple="">
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->members_count_w_dur_score == "")){ ?>
                  <option value="members_count_w_dur_score"><?php  echo __("Vekt for lag størrelse","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data)  && $scores_weights_data[0]->gender_w_dur_score == "")){ ?>
                  <option value="gender_w_dur_score"><?php  echo __("Vekt for type søker","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->age_group_w_dur_score == "")){ ?>
                  <option value="age_group_w_dur_score"><?php  echo __("Vekt for aldersgruppe","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->team_level_w_dur_score == "")){ ?>
                  <option value="team_level_w_dur_score"><?php  echo __("Vekt for nivå","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->members_count_w_pri_score == "")){ ?>
                  <option value="members_count_w_pri_score"><?php  echo __("Lag størrelse","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->gender_w_pri_score == "")){ ?>
                  <option value="gender_w_pri_score"><?php  echo __("Type søker","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->age_group_w_pri_score == "")){ ?>
                  <option value="age_group_w_pri_score"><?php  echo __("Aldersgruppe","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->team_level_w_pri_score == "")){ ?>
                  <option value="team_level_w_pri_score"><?php  echo __("Nivå","Gibbs");?></option>
                <?php } ?>
                <?php if(empty($scores_weights_data) || (!empty($scores_weights_data) && $scores_weights_data[0]->sport_priority_w_pri_score == "")){ ?>
                  <option value="sport_priority_w_pri_score"><?php  echo __("Idrett","Gibbs");?></option>
                <?php } ?>
              </select>
              <input class="form-control" name="users_groups_id" type="hidden" value="<?php echo $group_slected_id;?>" required="">
            </div>
            
            <div class="form-group col-sm-12">
              <label><?php  echo __("Value","Gibbs");?></label>
              <div class="range-wrap">
                <div class="range-value rangeV"></div>
                <input class="form-control range" name="value" type="range" min="1" max="10" value="1">
              </div>

            </div>
            <div class="form-group col-sm-12 age_btn_submit">
              <input class="form-control" type="submit" value="<?php  echo __("Submit","Gibbs");?>">
            </div>
          </div>
      </form>
    </div>
  </div>

</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- end -->
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('.advanced_column').select2();
});
  // Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
/*var advancedModal = document.getElementById("advancedModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var advanced = document.getElementById("advanced");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_advanced = document.getElementsByClassName("close_advanced")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
/*advanced.onclick = function() {
  advancedModal.style.display = "block";
}*/

// When the user clicks on <span> (x), close the modal
/*span.onclick = function() {
  team_sizeModal.style.display = "none";
}*/
/*close_advanced.onclick = function() {
  advancedModal.style.display = "none";
}*/

// When the user clicks anywhere outside of the modal, close it
/*window.onclick = function(event) {
  if (event.target == advancedModal) {
    advancedModal.style.display = "none";
  }
}*/
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

jQuery("#advanced1").click(function(){

    jQuery(".delete_form").remove();
    

    var f = document.createElement("form");
    f.setAttribute('class',"delete_form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

    var a_i1 = document.createElement("input"); //input element, text
    a_i1.setAttribute('type',"hidden");
    a_i1.setAttribute('name',"users_groups_id");
    a_i1.setAttribute('value',"<?php echo $group_slected_id;?>");
    f.appendChild(a_i1);


    var a_i = document.createElement("input"); //input element, text
    a_i.setAttribute('type',"hidden");
    a_i.setAttribute('name',"action");
    a_i.setAttribute('value',"add_advanced");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
})
</script>