<?php
$seasons_sql = "SELECT * from seasons where users_groups_id ='$group_slected_id'";
$seasons_data = $wpdb->get_results($seasons_sql);
?>
<form method="POST" class="gender_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
      <input type="hidden" name="action" value="update_season">
      <div class="row panel panel-default">
          <div class="panel-heading">
              <div class="row">
                <div class="col-md-10">
                     <h3><?php  echo __("Seasons","Gibbs");?><br>
                        <small></small>
                     </h3>
                </div>
                <div class="col-md-2 btn-plus">
                    <?php if(count($seasons_data) > 0){ ?>
                      <button class="btn btn-primary"><?php  echo __("Save","Gibbs");?></button>
                    <?php } ?>
                    <?php if(count($get_user_group_data) > 0){ ?>
                      <button type="button" class="btn btn-info btn-add" id="season"><i class="fa fa-plus"></i></button>
                    <?php } ?>
                </div>
              </div>
          </div>

          <?php foreach ($seasons_data as $key => $seasons_value) { ?>  
            
          
            <div class="form-group col-sm-2">
              <label><?php  echo __("Name","Gibbs");?></label>
              <input type="text" name="season[<?php echo $seasons_value->id;?>][name]" value="<?php echo $seasons_value->name;?>">
              <input type="hidden" name="season[<?php echo $seasons_value->id;?>][id]" value="<?php echo $seasons_value->id;?>">
            </div>
            <div class="form-group col-sm-2">
              <label><?php  echo __("Season start","Gibbs");?></label>
              <input  type="date" name="season[<?php echo $seasons_value->id;?>][season_start]" value="<?php echo $seasons_value->season_start;?>">
            </div>
            <div class="form-group col-sm-2">
              <label><?php  echo __("Season end","Gibbs");?></label>
              <input  type="date" name="season[<?php echo $seasons_value->id;?>][season_end]" value="<?php echo $seasons_value->season_end;?>">
            </div>
            <div class="form-group col-sm-3">
              <label><?php  echo __("Frist for å søke","Gibbs");?></label>
              <input  type="date" name="season[<?php echo $seasons_value->id;?>][season_deadline]" value="<?php echo $seasons_value->season_deadline;?>">
            </div>
            <div class="form-group col-sm-2 season_act">
              <label><?php  echo __("Activate season","Gibbs");?></label>
              <label class="gibbs_checkbox">
                <input type="checkbox" name="season[<?php echo $seasons_value->id;?>][on/off]" class="season_checkbox"  <?php if($seasons_value->{'on/off'} == "1"){echo "checked";}?>>
                <span class="checkmark"></span>
              </label>
              
            </div>
             <div class="form-group col-sm-1">
              <label style="visibility: hidden;"></label>
              <?php if($seasons_value->{'on/off'} == "1"){  ?><a href="<?php echo home_url();?>/application?group_id=<?php echo $group_slected_id;?>&season_id=<?php echo $seasons_value->id;?>" target="_blank"><button type="button" class="btn btn-primary">Link</button></a><?php } ?>
            </div>
            <div class="form-group col-sm-12" style="margin: 0;">
              <hr>
            </div>
            
          <?php } ?>  
          
      </div>
  </form> 

<!-- Aga group modal -->
<!-- The Modal -->
<div id="seasonModal" class="modal rule_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_season">&times;</span>
      <h2><?php  echo __("Legg til sesong","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <form method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
          <input type="hidden" name="action" value="save_season">
          <div class="row">
              <div class="form-group col-sm-12">
                <label><?php  echo __("Name","Gibbs");?></i></label>
                <input type="text" name="name" required="">
                <input class="form-control" name="users_groups_id" type="hidden" value="<?php echo $group_slected_id;?>" required="">
              </div>
              <div class="form-group col-sm-4">
                <label><?php  echo __("Season start","Gibbs");?></i></label>
                <input  type="date" name="season_start" required="">
              </div>
              <div class="form-group col-sm-4">
                <label><?php  echo __("Season end","Gibbs");?></i></label>
                <input  type="date" name="season_end" required>
              </div>
              <div class="form-group col-sm-4">
                <label><?php  echo __("Frist for å søke","Gibbs");?></label>
                <input  type="date" name="season_deadline" required>
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
var seasonModal = document.getElementById("seasonModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var season = document.getElementById("season");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_season = document.getElementsByClassName("close_season")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
season.onclick = function() {
  seasonModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
/*span.onclick = function() {
  team_sizeModal.style.display = "none";
}*/
close_season.onclick = function() {
  seasonModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  /*if (event.target == team_sizeModal) {
    team_sizeModal.style.display = "none";
  } */
  if (event.target == seasonModal) {
    seasonModal.style.display = "none";
  }
}
jQuery(".season_checkbox").on("click",function(){
   var checked = false;
   if(jQuery(this).prop("checked") == false){
      checked = true;
   }
   jQuery(".season_checkbox").prop("checked",false);
   if(checked == false){
     jQuery(this).prop("checked",true);
   }
})
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