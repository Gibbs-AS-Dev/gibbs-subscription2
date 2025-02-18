<!-- The Modal -->
<?php
$user_id = $users_table_d2->ID;
$user_data = get_userdata($user_id);
$first_name = get_user_meta($user_id,"first_name",true);
$last_name = get_user_meta($user_id,"last_name",true);
$phone = get_user_meta($user_id,"phone",true);
$user_zipcode = get_user_meta($user_id,"user_zipcode",true);
$user_city = get_user_meta($user_id,"user_city",true);
$org_pers_num = get_user_meta($user_id,"org_pers_num",true);
$user_address = get_user_meta($user_id,"user_address",true);
$user_email = $user_data->user_email;
?>

<div id="user_edit_modal_<?php echo $user_id;?>" class="modal user_group_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close user_edit_modal_close<?php echo $user_id;?>">&times;</span>
      <h2><?php  echo __("Edit new user","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form method="post" class="user_update_form" action="javascript:void(0)">
          <input type="hidden" name="action" value="user_management_update_user_role">
          <input type="hidden"  name="users_group_id" value="<?php echo $user_management_group_id;?>">
          <input type="hidden"  name="user_id" value="<?php echo $user_id;?>">
          <div class="row">
            <div class="form-group col-sm-6 select_manage">
              <label>Velg rolle</label>
              
              <select class="role" name="role">
                <option value="">Velg</option>
                <option value="1" <?php if($users_table_d2->role == "1"){echo 'selected';}?>>Kunde</option>
                <option value="2" <?php if($users_table_d2->role == "2"){echo 'selected';}?>>Ansatt</option>
                <option value="3" <?php if($users_table_d2->role == "3"){echo 'selected';}?>>Administrator</option>
                <option value="4" <?php if($users_table_d2->role == "3"){echo 'selected';}?>>Saksbehandler</option>
              </select>
            </div>
            
            <div class="form-group col-sm-12 age_btn_submit edit-user-gr-btns">
              <input class="form-control btn submit-btn" type="submit" value="<?php echo __("Save","Gibbs");?>">
              <button type="button" class="btn btn-danger remove_user_from_user_group" user_id="<?php echo $user_id;?>" users_group_id="<?php echo $user_management_group_id;?>">Fjern fra avdelingen</button>
            </div>
          </div>
      </form>
    </div>
  </div>

</div>

<script type="text/javascript">
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
var user_edit_modal<?php echo $user_id;?> = document.getElementById("user_edit_modal_<?php echo $user_id;?>");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var user_edit_modal_btn<?php echo $user_id;?> = document.getElementById("user_edit_modal_btn<?php echo $user_id;?>");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var user_edit_modal_close<?php echo $user_id;?> = document.getElementsByClassName("user_edit_modal_close<?php echo $user_id;?>")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
if(user_edit_modal_btn<?php echo $user_id;?> && typeof user_edit_modal_btn<?php echo $user_id;?> != "undefined"){
  
  user_edit_modal_btn<?php echo $user_id;?>.onclick = function() {
    user_edit_modal<?php echo $user_id;?>.style.display = "block";
  }

  // When the user clicks on <span> (x), close the modal
  /*span.onclick = function() {
    team_sizeModal.style.display = "none";
  }*/
  user_edit_modal_close<?php echo $user_id;?>.onclick = function() {
    user_edit_modal<?php echo $user_id;?>.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == user_edit_modal<?php echo $user_id;?>) {
      user_edit_modal<?php echo $user_id;?>.style.display = "none";
    }
  }
}


jQuery(".user_update_form").submit(function(e){

    e.preventDefault();
    jQuery(".user_update_form").find("button").prop("disabled",true);

    var formdata = jQuery(this).serialize();

    jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: formdata,
        dataType: 'json',
        success: function (data) {
          
          if(data.error == 1){
             jQuery(".user_update_form").find("button").prop("disabled",false);
             jQuery(".alert_error_message").show();
             jQuery(".alert_error_message").html(data.message);

          }else{
            jQuery(".alert_success_message").show();
            jQuery(".alert_success_message").html(data.message);

            setTimeout(function(){
                jQuery(".alert_success_message").hide();
                jQuery(".alert_success_message").html("");
                window.location.reload();
            },2000);

          }
          setTimeout(function(){
              jQuery(".alert_error_message").hide();
              jQuery(".alert_error_message").html("");
          },4000);
        }
    });
})

</script>