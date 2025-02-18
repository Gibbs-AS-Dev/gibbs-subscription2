<?php

$messages = get_messages($data->id);
if(isset($_GET["task"]) && $_GET["task"] == "cancel"){
	$message_cancel = "Jeg ønsker å kansellere denne bookingen fordi..";
}

if($page_type == "buyer"){
    $recipient = $data->owner_id;
}else{
	$recipient = $data->bookings_author;
}

$core_messages = new Listeo_Core_Messages();
?>

<div class="messages-container margin-top-0">
   <div class="messages-headline">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
   </div>
   <div class="messages-container-inner">
      <!-- Message Content -->
      <div class="message-content">

      	<?php
      	foreach ($messages as $key => $message) { 
      		if($message->conversation_id){
      			$core_messages->mark_as_read($message->conversation_id);	
      		}
			$msg_user = get_user_by( 'ID', $message->sender_id );

      		?>
         <div class="message-bubbles">
            <div class="message-bubble <?php if($message->sender_id == get_current_user_ID()){ echo 'me';}?>">
               <div class="message-avatar">
					<a href="<?php echo esc_url(get_author_posts_url($message->sender_id)); ?>"><?php echo get_avatar($message->sender_id, '70') ?></a>
					<b><?php echo $msg_user->display_name;?></b>
				</div>
               <div class="message-text" style="display:block">
                  <p><?php echo wpautop(esc_html($message->message)) ?></p>
               </div>
            </div>
         </div>
        <?php } ?> 
         <img style="display: none; " src="/wp-content/themes/listeo-child/images/loader.gif" alt="" class="loading">
         <!-- Reply Area -->
         <div class="clearfix"></div>
         <div class="message-reply">
            <form class="send_message_form" action="" method="post" >
                   <input type="hidden" name="recipient" value="<?php echo $recipient;?>" required="">
                   <input type="hidden" name="referral" value="booking_<?php echo $data->id;?>" required="">
                   <input type="hidden" name="action" value="listeo_send_message">
	               <textarea cols="40" id="message_textarea" name="message" required="" rows="3" placeholder="Din beskjed"><?php echo $message_cancel;?></textarea>
	               <button  type="submit" class="button">Send melding</button>
            </form>
         </div>
      </div>
      <!-- Message Content -->
   </div>
</div>

<script type="text/javascript">
	jQuery(".send_message_form").submit(function(e){
	  e.preventDefault();
	  var form_data = jQuery(this).serialize();
	    jQuery.ajax({
	        type: "POST",
	        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
	        data: form_data,
	        dataType: 'json',
	        success: function (data) {
	          if(data.type == "error"){
	            jQuery(".alert_error_message").show();
	            jQuery(".alert_error_message").html(data.message);

	          }else{
	          	var hreffff = window.location.href;
	          	hreffff = hreffff.replace("&task=cancel","");
	          	window.location.href = hreffff;
	          }
	          setTimeout(function(){
	                jQuery(".alert_success_message").hide();
	                jQuery(".alert_success_message").html("");
	                jQuery(".send_message_form").find("#message_textarea").val("");

	          },2000);
	          /*setTimeout(function(){
	              jQuery(".alert_error_message").hide();
	              jQuery(".alert_error_message").html("");
	          },4000);*/
	        }
	    });
	})

</script>