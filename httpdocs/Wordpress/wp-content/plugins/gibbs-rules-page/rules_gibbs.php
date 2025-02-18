<?php
  global $wpdb;
  

  $current_user_id = get_current_user_ID();
  $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
  $users_sql = "SELECT users_groups_id from `$users_and_users_groups` where users_id = '$current_user_id'";
$user_group_data = $wpdb->get_results($users_sql);

$users_groups_ids = array();

foreach ($user_group_data as $key => $gr_id) {
  $users_groups_ids[] = $gr_id->users_groups_id;
}

 
$users_groups_ids_im = implode(",", $users_groups_ids);
$rules_gibbs_db = $wpdb->prefix . 'users_groups';  // table name
$age_sql = "SELECT id,name FROM `$rules_gibbs_db`  where id IN ($users_groups_ids_im)";
$get_user_group_data = $wpdb->get_results($age_sql);

$cr_user_group = get_user_meta(get_current_user_ID(),"cr_user_group", true);
/*$group_slected_id = "9999999999999999999999";
if($cr_user_group != ""){
  $group_slected_id = $cr_user_group;
}else{
  if(!empty($users_groups_ids)){
       $group_slected_id = $users_groups_ids[0];
  }
}*/
$group_slected_id = "9999999999999999999999";
$current_user = wp_get_current_user();

$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

if($active_group_id != ""){
  $group_slected_id = $active_group_id;
}else{
  $group_slected_id = "0";
}





?>
<section class="rule_main">
 <!--  <div class="container panel-group">
    <div class="row">
      <div class="col-md-6">
        <h5><?php echo __("User group","Gibbs");?></h5>
        <select name="users_groups_data_id" class="users_groups_data_id">
          <?php foreach ($get_user_group_data as $key => $gr_dd) { ?>
            <option value="<?php echo $gr_dd->id;?>" <?php if($gr_dd->id == $group_slected_id){ echo "selected";}?>><?php echo $gr_dd->name;?></option>
          <?php } ?>
        </select>
      </div>
    </div>
  </div>
 -->
  <div class="container panel-group">
      <!-- seasons section -->
      <?php require("seasons.php"); ?>
      <!-- seasons end section -->

      <!-- seasons section -->
      <?php require("team_size.php"); ?>
      <!-- seasons end section -->
    	 
 
  <?php
    
    $rules_gibbs_db = $wpdb->prefix . 'rules_gibbs';  // table name
    $age_sql = "SELECT age_group.name,age_group_priorities.* FROM `age_group` left join age_group_priorities ON age_group.id = age_group_priorities.age_group_id where age_group.users_groups_id ='$group_slected_id' AND age_group_priorities.id !=''";
    $age_group_data = $wpdb->get_results($age_sql);

   



  ?>

   


      <form method="POST" class="age_group" action="<?php echo admin_url( 'admin-ajax.php' );?>">
          <input type="hidden" name="action" value="update_age_group">
          <div class="row panel panel-default">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-md-10">
                       <h3><?php  echo __("Prioritering av aldersgrupper","Gibbs");?><br>
                          <small><?php  echo __("Ranger viktigheten fra 1-10. Høyere tall, betyr høyere prioritering.","Gibbs");?></small>
                       </h3>
                  </div>
                  <div class="col-md-2 btn-plus">
                      <?php if(count($age_group_data) > 0){ ?>
                        <button class="btn btn-primary"><?php  echo __("Save","Gibbs");?></button>
                      <?php } ?>
                      <?php if(count($get_user_group_data) > 0){ ?>
                        <button type="button" class="btn btn-info btn-add" id="agegroup"><i class="fa fa-plus"></i></button>
                      <?php } ?>
                  </div>
                </div>  
              </div>

              <?php foreach ($age_group_data as $key => $age_group_value) { ?>  
                
              
                <div class="form-group col-sm-4 rule_div_<?php echo $age_group_value->id;?>">
                  <div class="label_div">
                    <label class="rules_name"><?php echo $age_group_value->name;?> <i class="fa fa-edit"></i></label>
                    <!-- <i class="fa fa-trash delete_age_group" age_group_id="<?php echo $age_group_value->age_group_id;?>" age_group_priorities_id="<?php echo $age_group_value->id;?>"></i> -->
                  </div>
                  <input class="rules_name_input" type="hidden" name="age_group[<?php echo $age_group_value->id;?>][name]" value="<?php echo $age_group_value->name;?>">
                  <input type="hidden" name="age_group[<?php echo $age_group_value->id;?>][age_group_id]" value="<?php echo $age_group_value->age_group_id;?>">
                  <input type="hidden" name="age_group[<?php echo $age_group_value->id;?>][id]" value="<?php echo $age_group_value->id;?>">
                  <div class="range-wrap">
                     <div class="range-value rangeV"></div>
                     <input class="form-control range"  name="age_group[<?php echo $age_group_value->id;?>][age_group_priority]" type="range" min="1" max="10" value="<?php echo $age_group_value->age_group_priority;?>">
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



  

  <!-- Aga group modal -->
    <!-- The Modal -->
    <div id="AgegroupModal" class="modal rule_modal">

      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-header">
          <span class="close close_agegroup">&times;</span>
          <h2><?php  echo __("Prioritering av aldersgrupper","Gibbs");?></h2>
        </div>
        <div class="modal-body">
          <form method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
              <input type="hidden" name="action" value="save_age_group">
              <div class="row">
                <div class="form-group col-sm-12">
                  <label><?php  echo __("Age group name","Gibbs");?></label>
                  <input class="form-control" name="name" type="text" required="">
                   <input class="form-control" name="users_groups_id" type="hidden" value="<?php echo $group_slected_id;?>" required="">
                </div>
                
                <div class="form-group col-sm-12">
                  <label><?php  echo __("Priority","Gibbs");?></label>
                  <div class="range-wrap">
                    <div class="range-value rangeV"></div>
                    <input class="form-control range" name="age_group_priority" type="range" min="1" max="10" value="2">
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- end -->

    <!-- gender_priority section -->
     <?php require("gender_priority.php"); ?>
    <!-- gender_priority end section -->

    <!-- level_priority section -->
     <?php require("level_priority.php"); ?>
    <!-- level_priority end section -->
  
    <!-- sport_priority section -->
     <?php require("sport_priority.php"); ?>
    <!-- sport_priority end section -->
<br></br>
<br></br>
<h1>Avansert</h1>
      <!-- duration_score section -->
      <?php require("duration_score.php"); ?>
    <!-- duration_score end section -->

    <!-- league section -->
     <?php require("league.php"); ?>
    <!-- league end section -->

    <!-- advanced section -->
     <?php require("advanced.php"); ?>
    <!-- advanced end section -->

  </div>

</section>

<script>

jQuery(".range").each(function(){
    var range =  jQuery(this);
    var rangeV =  jQuery(this).parent().find(".rangeV");
    setValue(range,rangeV);
})  

jQuery(".range").change(function(){
    var range =  jQuery(this);
    var rangeV =  jQuery(this).parent().find(".rangeV");
    setValue(range,rangeV);
    jQuery(this).parent().find("span").fadeIn();
    var that;
    that  = this;
    setTimeout(function(){
       jQuery(that).parent().find("span").fadeOut();
    },1000);
})
function setValue(range,rangeV){
  var range = range[0];
  var rangeV = rangeV[0];
  const
    newValue = Number( (range.value - range.min) * 100 / (range.max - range.min) ),
    newPosition = 10 - (newValue * 0.2);
  rangeV.innerHTML = `<span>${range.value}</span>`;
  rangeV.style.left = `calc(${newValue}% + (${newPosition}px))`;
}
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
var agegroupModal = document.getElementById("AgegroupModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var agegroup = document.getElementById("agegroup");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_agegroup = document.getElementsByClassName("close_agegroup")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
agegroup.onclick = function() {
  agegroupModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
/*span.onclick = function() {
  team_sizeModal.style.display = "none";
}*/
close_agegroup.onclick = function() {
  agegroupModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  /*if (event.target == team_sizeModal) {
    team_sizeModal.style.display = "none";
  } */
  if (event.target == agegroupModal) {
    agegroupModal.style.display = "none";
  }
}
jQuery("input[type=range]").change(function(){
   jQuery(this).parent().find(".value_range").text("value: "+jQuery(this).val());
})
jQuery(".rules_name").click(function(){
  if(jQuery(this).find("input").length == 0){
     var rule_name = jQuery(this).parent().parent().find(".rules_name_input").val();
     jQuery(this).html("<input type='text' class='rules_append_input' value='"+rule_name+"'>");
  } 
})

jQuery(document).on("keyup",".rules_append_input",function(){
   jQuery(this).parent().parent().parent().find(".rules_name_input").val(jQuery(this).val());
})
jQuery(document).on("change",".rules_append_input",function(){
   var in_val = jQuery(this).val();
   jQuery(this).parent().html(in_val+' <i class="fa fa-edit"></i>');
})
jQuery(document).on('click', function (e) {
    if (jQuery(e.target).closest(".rules_name").length === 0 && jQuery(e.target).closest(".fa-edit").length === 0) {
        jQuery("body").find(".rules_append_input").each(function(){
           var in_val = jQuery(this).val();
           jQuery(this).parent().html(in_val+' <i class="fa fa-edit"></i>');
        });
    }
});
jQuery(".delete_rule i").click(function(){
    var rule_id;
    rule_idd = jQuery(this).attr("rule_id");
    jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {
          action: 'delete_rule',
          rule_id: rule_idd
        },
        success: function (response) {
           var rule_div = ".rule_div_"+rule_idd;
           jQuery("body").find(rule_div).remove();
        }
    });

});

jQuery(".delete_age_group").click(function(){
  var delete_c = confirm("Want to delete?");
  if (delete_c) {
     
    jQuery(".delete_form").remove();
    

    var f = document.createElement("form");
    f.setAttribute('class',"delete_form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

    var i = document.createElement("input"); //input element, text
    i.setAttribute('type',"text");
    i.setAttribute('name',"age_group_id");
    i.setAttribute('value',jQuery(this).attr("age_group_id"));
    f.appendChild(i);

    var i2 = document.createElement("input"); //input element, text
    i2.setAttribute('type',"text");
    i2.setAttribute('name',"age_group_priorities_id");
    i2.setAttribute('value',jQuery(this).attr("age_group_priorities_id"));
    f.appendChild(i2);

    var a_i = document.createElement("input"); //input element, text
    a_i.setAttribute('type',"hidden");
    a_i.setAttribute('name',"action");
    a_i.setAttribute('value',"delete_age_group");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
  }  
})
jQuery(".users_groups_data_id").change(function(){

    jQuery(".delete_form").remove();
    

    var f = document.createElement("form");
    f.setAttribute('class',"delete_form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

    var i = document.createElement("input"); //input element, text
    i.setAttribute('type',"text");
    i.setAttribute('name',"users_groups_id");
    i.setAttribute('value',"<?php echo $group_slected_id;?>");
    f.appendChild(i);

    var a_i = document.createElement("input"); //input element, text
    a_i.setAttribute('type',"hidden");
    a_i.setAttribute('name',"action");
    a_i.setAttribute('value',"save_user_group");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
})

jQuery("#teamsizegroup").click(function(){

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
    a_i.setAttribute('value',"add_team_size");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
})
</script>
