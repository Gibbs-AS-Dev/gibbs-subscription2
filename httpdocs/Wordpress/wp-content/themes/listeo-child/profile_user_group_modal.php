<div id="usergroupModal2" class="modal user_group_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_usergroup">&times;</span>
      <h2 class="chaange_group_title"><?php 
      echo __("Opprett avdeling","Gibbs"); ?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form class="user_groupp_form2" method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
          <input type="hidden" name="action" value="save_user_group_data">
          <input type="hidden" name="no_group" value="true">
          <div class="row">
            <div class="form-group col-sm-12">
              <label >Navn</label>
              <input type="hidden" class="users_group_id" name="users_group_id" value="">
              <input class="form-control users_group_name" name="name" type="text" value="" placeholder="" required="">
            </div>
            <?php //if($group_admin_email != ""){ ?>
              <div class="form-group col-sm-12 group_admin_email_div">
                <label > E-post (alle varsler for denne brukergruppen blir sendt til denne eposten)</label>
                 <input type="hidden" class="group_admin" name="group_admin" value="<?php echo $group_admin;?>">
                <input class="form-control group_admin_email" name="group_admin_email" type="email" value="" placeholder="" required="">
              </div>
            <?php //} ?>
            <div class="form-group col-sm-12 age_btn_submit age_btn_submit_flex">
              <input class="form-control" type="submit" value="<?php echo __("Save","Gibbs");?>">
              <button class="form-control1 close_usergroup close_usergroup_btn" type="button" ><?php  echo __("Cancel","Gibbs");?></button>
            </div>
          </div>
      </form>
    </div>
  </div>

</div>

<script type="text/javascript">
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
var usergroupModal2 = document.getElementById("usergroupModal2");

//var team_sizebtn = document.getElementById("team_size");


  // Get the <span> element that closes the modal
  //var span = document.getElementsByClassName("close")[0];
  var close_usergroup = document.getElementsByClassName("close_usergroup")[0];

  jQuery(".close_usergroup").click(function(){
    jQuery("#usergroupModal2").hide();
  })


  // When the user clicks on <span> (x), close the modal
  /*span.onclick = function() {
    team_sizeModal.style.display = "none";
  }*/
  close_usergroup.onclick = function() {
    usergroupModal2.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == usergroupModal2) {
      usergroupModal2.style.display = "none";
    }
  }


jQuery(".user_groupp_form2").submit(function(e){

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