<?php
$sports_sql = "SELECT sport.name,sports_priorities.* FROM `sport` left join sports_priorities ON sport.id = sports_priorities.sport_id where sport.users_groups_id ='$group_slected_id' AND sports_priorities.id !=''";
$sports_data = $wpdb->get_results($sports_sql);
?>
<form method="POST" class="sports_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
      <input type="hidden" name="action" value="update_sports">
      <div class="row panel panel-default">
          <div class="panel-heading">
             <div class="row">
                <div class="col-md-10">
                     <h3><?php  echo __("Prioritering av type idrett","Gibbs");?><br>
                        <small><?php  echo __("Ranger viktigheten fra 1-10. Høyere tall, betyr høyere prioritering.","Gibbs");?></small>
                     </h3>
                </div>
                <div class="col-md-2 btn-plus">
                      <?php if(count($sports_data) > 0){ ?>
                        <button class="btn btn-primary"><?php  echo __("Save","Gibbs");?></button>
                      <?php } ?>
                      <?php if(count($get_user_group_data) > 0){ ?>
                        <button type="button" class="btn btn-info btn-add" id="sports"><i class="fa fa-plus"></i></button>
                      <?php } ?>
                </div>
              </div>
          </div>

          <?php foreach ($sports_data as $key => $sports_value) { ?>  
            
          
            <div class="form-group col-sm-4 sports_div_<?php echo $sports_value->id;?>">
              <div class="label_div">
                <label class="rules_name"><?php echo $sports_value->name;?> <i class="fa fa-edit"></i></label>
                <!-- <i class="fa fa-trash delete_sports" sport_id="<?php echo $sports_value->sport_id;?>" sport_priority_id="<?php echo $sports_value->id;?>"></i> -->
              </div>
              <input class="rules_name_input" type="hidden" name="sports[<?php echo $sports_value->id;?>][name]" value="<?php echo $sports_value->name;?>">
              <input type="hidden" name="sports[<?php echo $sports_value->id;?>][sport_id]" value="<?php echo $sports_value->sport_id;?>">
              <input type="hidden" name="sports[<?php echo $sports_value->id;?>][id]" value="<?php echo $sports_value->id;?>">
              <div class="range-wrap">
                 <div class="range-value rangeV"></div>
                 <input class="form-control range"  name="sports[<?php echo $sports_value->id;?>][sport_priority]" type="range" min="1" max="10" value="<?php echo $sports_value->sport_priority;?>">
              </div>
             
            
            </div>
          <?php } ?>  
        
      </div>
  </form> 

<!-- Aga group modal -->
<!-- The Modal -->
<div id="sportsModal" class="modal rule_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_sports">&times;</span>
      <h2><?php  echo __("Prioritering av type idrett","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <form method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
          <input type="hidden" name="action" value="save_sports">
          <div class="row">
            <div class="form-group col-sm-12">
              <label><?php  echo __("Name","Gibbs");?></label>
              <input class="form-control" name="name" type="text" required="">
               <input class="form-control" name="users_groups_id" type="hidden" value="<?php echo $group_slected_id;?>" required="">
            </div>
            
            <div class="form-group col-sm-12">
              <label><?php  echo __("Priority","Gibbs");?></label>
              <div class="range-wrap">
                <div class="range-value rangeV"></div>
                <input class="form-control range" name="sport_priority" type="range" min="1" max="10" value="2">
              </div>

            </div>
            <div class="form-group col-sm-12 age_btn_submit">
              <input class="form-control" type="submit" value="<?php echo __("Submit","Gibbs");?>">
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
var sportsModal = document.getElementById("sportsModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var sports = document.getElementById("sports");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_sports = document.getElementsByClassName("close_sports")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
sports.onclick = function() {
  sportsModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
/*span.onclick = function() {
  team_sizeModal.style.display = "none";
}*/
close_sports.onclick = function() {
  sportsModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  /*if (event.target == team_sizeModal) {
    team_sizeModal.style.display = "none";
  } */
  if (event.target == sportsModal) {
    sportsModal.style.display = "none";
  }
}
jQuery(".delete_sports").click(function(){
  var delete_c1 = confirm("Want to delete?");
  if (delete_c1) {
     
    jQuery(".delete_form").remove();
    

    var f = document.createElement("form");
    f.setAttribute('class',"delete_form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

    var i = document.createElement("input"); //input element, text
    i.setAttribute('type',"text");
    i.setAttribute('name',"sport_id");
    i.setAttribute('value',jQuery(this).attr("sport_id"));
    f.appendChild(i);

    var i2 = document.createElement("input"); //input element, text
    i2.setAttribute('type',"text");
    i2.setAttribute('name',"sport_priority_id");
    i2.setAttribute('value',jQuery(this).attr("sport_priority_id"));
    f.appendChild(i2);

    var a_i = document.createElement("input"); //input element, text
    a_i.setAttribute('type',"hidden");
    a_i.setAttribute('name',"action");
    a_i.setAttribute('value',"delete_sports");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
  }  
})
</script>