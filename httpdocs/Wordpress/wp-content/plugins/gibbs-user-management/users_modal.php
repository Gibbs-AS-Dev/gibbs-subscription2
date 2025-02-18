<!-- The Modal -->
<?php
if($user_management_group_id == ""){
  return;
}

?>

<div id="userModal" class="modal user_group_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php  echo __("Legg til bruker","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form method="post" class="user_register_form" action="javascript:void(0)">
          <input type="hidden" name="action" value="user_management_create_user">
          <input type="hidden"  name="users_group_id" value="<?php echo $user_management_group_id;?>">
          <div class="row">
            <div class="form-group col-sm-6 user_divs">
              <label><?php echo __("First name","gibbs");?></label>
              
              <input class="form-control" name="first_name" type="text" placeholder="<?php echo __("","gibbs");?>">
            </div>
            <div class="form-group col-sm-6 user_divs">
              <label><?php echo __("Last name","gibbs");?></label>
              <input class="form-control" name="last_name" type="text" placeholder="<?php echo __("","gibbs");?>">
            </div>
            <div class="form-group col-sm-6">
              <label><?php echo __("Epost","gibbs");?></label>
              <input class="form-control" name="email" type="email" placeholder="<?php echo __("","gibbs");?>" required="">
            </div>
            <div class="form-group col-sm-6 user_divs">
              <label><?php echo __("Phone","gibbs");?></label>
              <input class="form-control" name="phone" type="text" placeholder="<?php echo __("","gibbs");?>">
            </div>
            <div class="form-group col-sm-12 age_btn_submit age_btn_submit_flex">
              <input type="hidden" name="not_user_already">
              <input class="form-control" type="submit" value="<?php echo __("Lagre","Gibbs");?>">
              <button class="form-control1 close_usergroup close_user close_usergroup_btn" type="button" ><?php  echo __("Lukk","Gibbs");?></button>
            </div>
          </div>
      </form>
    </div>
  </div>

</div>

<script type="text/javascript">
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
var userModal = document.getElementById("userModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal
var user_modalbtn = document.getElementById("user_modalbtn");


if(user_modalbtn != null){

  // Get the <span> element that closes the modal
  //var span = document.getElementsByClassName("close")[0];
  var close_user = document.getElementsByClassName("close_user")[0];

  // When the user clicks the button, open the modal 
  /*team_sizebtn.onclick = function() {
    team_sizeModal.style.display = "block";
  }*/
  user_modalbtn.onclick = function() {
    userModal.style.display = "block";
    jQuery(".user_divs").hide();
    jQuery(".user_divs").find("input").removeAttr("required");
    jQuery("input[name=not_user_already]").val("");
  }
  jQuery(".close_user").click(function(){
    jQuery("#userModal").hide();
  })
  // When the user clicks on <span> (x), close the modal
  /*span.onclick = function() {
    team_sizeModal.style.display = "none";
  }*/
  close_user.onclick = function() {
    userModal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == userModal) {
      userModal.style.display = "none";
    }
  }
}
jQuery(".user_register_form").submit(function(e){

    e.preventDefault();
    jQuery(".user_register_form").find("button").prop("disabled",true);

    var formdata = jQuery(this).serialize();

    jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: formdata,
        dataType: 'json',
        success: function (data) {
          if(data.error == 1){
             jQuery(".user_register_form").find("button").prop("disabled",false);
             jQuery(".alert_error_message").show();
             jQuery(".alert_error_message").html(data.message);

          }else{

            if(data.not_user_already != undefined){
                 
                 jQuery("input[name=not_user_already]").val("1");

                 jQuery(".user_divs").show();
                 jQuery(".user_divs").find("input").attr("required",true)



            }else{
              jQuery(".alert_success_message").show();
              jQuery(".alert_success_message").html(data.message);

              setTimeout(function(){
                  jQuery(".alert_error_message").hide();
                  jQuery(".alert_error_message").html("");
                  window.location.reload();
              },4000);

            }
            

          }
          /*setTimeout(function(){
              jQuery(".alert_error_message").hide();
              jQuery(".alert_error_message").html("");
          },4000);*/
        }
    });
})

jQuery(document).on("click",".add_into_user_group",function(){
    var user_email = jQuery(this).attr("user_email");
    var users_group_id = jQuery(this).attr("users_group_id");
    jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {action:'add_into_user_group',user_email:user_email,users_group_id:users_group_id},
        dataType: 'json',
        success: function (data) {
          if(data.error == 1){

             jQuery(".user_register_form").find("button").prop("disabled",false);
             jQuery(".alert_error_message").show();
             jQuery(".alert_error_message").html(data.message);


          }else{
            jQuery(".alert_success_message").show();
            jQuery(".alert_success_message").html(data.message);

            setTimeout(function(){
                jQuery(".alert_error_message").hide();
                jQuery(".alert_error_message").html("");
                window.location.reload();
            },2000);

          }
          /*setTimeout(function(){
              jQuery(".alert_error_message").hide();
              jQuery(".alert_error_message").html("");
          },4000);*/
        }
    });
});
</script>