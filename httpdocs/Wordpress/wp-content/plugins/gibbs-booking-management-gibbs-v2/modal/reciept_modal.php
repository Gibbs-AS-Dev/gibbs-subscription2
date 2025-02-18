<!-- The Modal -->
<?php

?>

<div id="reciept_modal<?php echo $data->id;?>" class="modal message_modal rec_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <h2></h2>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12 reciept_body">
          
        </div>
      </div>
             
    </div>
  </div>

</div>
<style type="text/css">
  .email-headline{
    display: none;
  }
  .rec_modal .modal-content {
    width: 65% !important;
  }
  .rec_modal .close {
      margin: -11px !important;
  }
</style>

<script type="text/javascript">


jQuery(document).on("click",".reciept_modalbtn<?php echo $data->id;?>",function(){

        jQuery("#reciept_modal<?php echo $data->id;?>").find(".reciept_body").html("")

        var order_id = jQuery(this).data("order_id");

        ajax_data = {
            'action': 'get_rec_html',
            'order_id': order_id
        };
        //var form_data = jQuery(this).serialize();
          jQuery.ajax({
              type: "POST",
              url: "<?php echo admin_url( 'admin-ajax.php' );?>",
              data: ajax_data,
              success: function (data) {
                      jQuery("#reciept_modal<?php echo $data->id;?>").find(".reciept_body").html(data);
                      jQuery("#reciept_modal<?php echo $data->id;?>").show();
              }
          });

        
})

jQuery(document).on("click",".close_modal",function(){
        jQuery("#reciept_modal<?php echo $data->id;?>").hide();
})




</script>