<!-- The Modal -->
<?php
$users_groups_table = $wpdb->prefix . 'users_groups';  // table name
$sql_user_group_modal = "select * from `$users_groups_table` where id = $user_management_group_id";
$user_group_data_modal = $wpdb->get_row($sql_user_group_modal);

$users_group_id = "";
$users_group_name = "";
$group_admin = "";
$group_admin_email = "";
if(isset($user_group_data_modal->id) && isset($user_group_data_modal->name)){
   $users_group_id = $user_group_data_modal->id;
   $users_group_name = $user_group_data_modal->name;
   $users_table = $wpdb->prefix . 'users'; 

   if($user_group_data_modal->group_admin != ""){

      $group_admin = $user_group_data_modal->group_admin;

      $user_data_sql = "select user_email from `$users_table` where id = $group_admin";

      $user_data = $wpdb->get_row($user_data_sql);

      if(isset($user_data->user_email)){
         $group_admin_email = $user_data->user_email;
      }

   }

   


}


?>

<div id="usergroupModal" class="modal user_group_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_usergroup">&times;</span>
      <h2 class="chaange_group_title"><?php 
      if($users_group_id != ""){
          echo __("Rediger avdeling","Gibbs");
      } else{
        echo __("Opprett avdeling","Gibbs");
      } ?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form class="user_groupp_form" method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
          <input type="hidden" name="action" value="save_user_group_data">
          <div class="row">
            <div class="form-group col-sm-12">
              <label >Navn</label>
              <input type="hidden" class="users_group_id" name="users_group_id" value="<?php echo $users_group_id;?>">
              <input class="form-control users_group_name" name="name" type="text" value="<?php echo $users_group_name;?>" placeholder="" required="">
            </div>
            <?php //if($group_admin_email != ""){ ?>
              <div class="form-group col-sm-12 group_admin_email_div">
                <label > E-post (alle varsler for denne brukergruppen blir sendt til denne eposten)</label>
                 <input type="hidden" class="group_admin" name="group_admin" value="<?php echo $group_admin;?>">
                <input class="form-control group_admin_email" name="group_admin_email" type="email" value="<?php echo $group_admin_email;?>" placeholder="" required="">
              </div>
            <?php //} ?>
            <div class="form-group col-sm-12 age_btn_submit age_btn_submit_flex">
              <input class="form-control" type="submit" value="<?php echo __("Lagre");?>">
              <button class="form-control1 close_usergroup close_usergroup_btn" type="button" ><?php  echo __("Avbryt");?></button>
            </div>
          </div>
      </form>
    </div>
  </div>

</div>

<script type="text/javascript">
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
var usergroupModal = document.getElementById("usergroupModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var usergroup_modalbtn = document.getElementById("usergroup_modalbtn");

if(usergroup_modalbtn != null){

  // Get the <span> element that closes the modal
  //var span = document.getElementsByClassName("close")[0];
  var close_usergroup = document.getElementsByClassName("close_usergroup")[0];

  jQuery(".close_usergroup").click(function(){
    jQuery("#usergroupModal").hide();
  })

  // When the user clicks the button, open the modal 
  /*team_sizebtn.onclick = function() {
    team_sizeModal.style.display = "block";
  }*/
  usergroup_modalbtn.onclick = function() {
    usergroupModal.style.display = "block";
  }

  // When the user clicks on <span> (x), close the modal
  /*span.onclick = function() {
    team_sizeModal.style.display = "none";
  }*/
  close_usergroup.onclick = function() {
    usergroupModal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == usergroupModal) {
      usergroupModal.style.display = "none";
    }
  }
}

jQuery("#usergroup_addnew").click(function(){
  
  jQuery("#usergroup_modalbtn").click();
  jQuery(".chaange_group_title").html("<?php  echo __("Opprett avdeling","Gibbs");;?>")
  //jQuery(".group_admin_email_div").hide();
  setTimeout(function(){
    jQuery(".users_group_id").val("");
    jQuery(".users_group_name").val("");
    jQuery(".group_admin_email").val("");
  },100);

});
jQuery("#usergroup_modalbtn").click(function(){
  jQuery(".users_group_id").val("<?php echo $users_group_id;?>");
  jQuery(".users_group_name").val("<?php echo $users_group_name;?>");
  jQuery(".group_admin_email").val("<?php echo $group_admin_email;?>");
  jQuery(".chaange_group_title").html("<?php  echo __("Rediger avdeling","Gibbs");;?>")
 // jQuery(".group_admin_email_div").show();

})

jQuery(".user_groupp_form").submit(function(e){

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