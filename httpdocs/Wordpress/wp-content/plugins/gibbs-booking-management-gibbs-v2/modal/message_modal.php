<!-- The Modal -->
<?php

?>

<div id="messageModal" class="modal message_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <h2><?php  echo __("Send melding","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <div id="small-dialog" class="zoom-anim-dialog">

          <div class="message-reply margin-top-0">
              <form class="send_message_form" action="" method="post" >
                <input type="hidden" name="recipient" value="" required="">
                <input type="hidden" name="referral" value="" required="">
                <input type="hidden" name="action" value="listeo_send_message">
                  <textarea id="message_textarea"
                          required
                          cols="40" name="message" rows="3" placeholder="<?php esc_attr_e('Your message','listeo_core'); // echo $owner_data->first_name; ?>"></textarea>
                  <button type="submit" class="button">
                      <i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send melding', 'listeo_core'); ?></button>
                 

              </form>

          </div>
      </div>
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".message_modalbtn",function(){
        var recipient = jQuery(this).data("recipient");
        var referral = jQuery(this).data("booking_id");
        jQuery("#messageModal").find("input[name=recipient]").val(recipient);
        jQuery("#messageModal").find("input[name=referral]").val(referral);

        jQuery("#messageModal").show();
})

jQuery(document).on("click",".close_modal",function(){
        jQuery("#messageModal").hide();
})

jQuery(".send_message_form").submit(function(e){
  e.preventDefault();
  var form_data = jQuery(this).serialize();
    jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: form_data,
        dataType: 'json',
        success: function (data) {
          if(data.type == "success"){
            jQuery(".alert_success_message").show();
            jQuery(".alert_success_message").html(data.message);

          }
          setTimeout(function(){
                jQuery(".alert_success_message").hide();
                jQuery(".alert_success_message").html("");
                jQuery(".send_message_form").find("#message_textarea").val("");
                jQuery("#messageModal").hide();
                

          },2000);
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