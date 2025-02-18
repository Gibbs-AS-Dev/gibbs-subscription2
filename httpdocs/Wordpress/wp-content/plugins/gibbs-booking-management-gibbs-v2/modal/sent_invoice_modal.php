<!-- The Modal -->
<?php

?>

<div id="sent_invoice_modal<?php echo $data->id;?>" class="modal message_modal action_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <div class="row top_div" >
          <div class="col-xs-12 col-md-12">
              <i class="fa fa-exclamation-circle"></i>
          </div>
          <div class="col-xs-12 col-md-12">
              <span>Er du sikker?</span>
          </div>
      </div>
    </div>
    <div class="modal-body">
      <form class="sent_invoice_modal_form<?php echo $data->id;?>" action="javascript:void(0)" method="post" >

        <input type="hidden" name="fixed"  required="">
        <input type="hidden" name="booking_id"  value="<?php echo $data->id;?>" required>
       
        <div id="action_modal_div">
          
          
          <div class="row" style="background: white;">
              <div class="col-xs-6 col-md-6 left_btn">
                 <button type="submit"  class="button yesbtn">Ja</button>
              </div>
              <div class="col-xs-6 col-md-6 right_btn">
                 <button type="button"  class="button nobtn close_modal">Nei</button>
              </div>
          </div>
      </div>
    </form>
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".sent_invoice_modalbtn<?php echo $data->id;?>",function(){

        var fixed = jQuery(this).data("value");

        jQuery(".sent_invoice_modal_form<?php echo $data->id;?>").find("input[name=fixed]").val(fixed);

        jQuery("#sent_invoice_modal<?php echo $data->id;?>").show();
})

jQuery(document).on("click",".close_modal",function(){
        jQuery("#sent_invoice_modal<?php echo $data->id;?>").hide();
})

jQuery(document).on('submit','.sent_invoice_modal_form<?php echo $data->id;?>',function(e) {
    e.preventDefault();

    jQuery(".booking_datatable").addClass("loading_class");

    let booking_id = jQuery(this).find("input[name=booking_id]").val();
    let fixed = jQuery(this).find("input[name=fixed]").val();
    var ajax_data = {
          'action': 'change_fixed',
          'booking_id' : booking_id,
          'fixed' : fixed,
          //'nonce': nonce
      };
    jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: ajax_data,
        success: function(data){
           window.location.reload();
        }
    });
});




</script>