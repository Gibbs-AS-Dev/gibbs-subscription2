<!-- The Modal -->
<?php

?>

<div id="newofferModal" class="modal message_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <h2><?php  echo __("Send new offer","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form class="send_newoffer_form" action="javascript:void(0)" method="post" >
        <input type="hidden" name="booking_id" value="" required="">
        <input type="hidden" name="first_name" value="" >
        <input type="hidden" name="last_name" value="">
        <input type="hidden" name="email" value="">
        <input type="hidden" name="phone" value="">
        <div id="singleNewOffer">
          
          <div class="row" style="padding: 15px 15px 5px 15px;background: white;" >
              <div class="col-xs-12 col-md-12">
                  <div style=" padding: 0; ">
                      <textarea class="_message1" name="message" style="height: 25px;margin: 1px;" placeholder="Din melding"></textarea>
                  </div>
              </div>
          </div>
          <div class="row" style="background: white;">
              <div class="col-xs-6 col-md-6" style="padding:20px; text-align: center;">
                  <div class="col-xs-5 col-md-3" style="padding: 0; text-align: center;">
                          <span style="font-size: 13px;">Ny pris</span>
                  </div>
                  <div class="col-xs-7 col-md-9" style=" padding: 0; ">
                      <input class="_price1" name="offer_price" type="number"/>
                  </div>
              </div>
              <div class="col-xs-6 col-md-6" style = "padding: 20px; text-align: center;">
                  <button type="submit"  class="button gray singleOffer"><i class="fa fa-paper-plane"></i> Gi nytt tilbud</button>
              </div>
          </div>
      </div>
    </form>
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".newoffer_modalbtn",function(){
        var recipient = jQuery(this).data("recipient");
        var booking_id = jQuery(this).data("booking_id");
        var first_name = jQuery(this).data("first_name");
        var last_name = jQuery(this).data("last_name");
        var email = jQuery(this).data("email");
        var phone = jQuery(this).data("phone");
        var price = jQuery(this).data("price");
        jQuery("#newofferModal").find("input[name=booking_id]").val(booking_id);
        jQuery("#newofferModal").find("input[name=first_name]").val(first_name);
        jQuery("#newofferModal").find("input[name=last_name]").val(last_name);
        jQuery("#newofferModal").find("input[name=email]").val(email);
        jQuery("#newofferModal").find("input[name=phone]").val(phone);
        jQuery("#newofferModal").find("._price1").val(price);

        jQuery("#newofferModal").show();
})

jQuery(document).on("click",".close_modal",function(){
        jQuery("#newofferModal").hide();
})

jQuery(".send_newoffer_form").submit(function(e){
  e.preventDefault();

  var first_name = jQuery(this).find("input[name=first_name]").val();
  var last_name = jQuery(this).find("input[name=last_name]").val();
  var email = jQuery(this).find("input[name=email]").val();
  var phone = jQuery(this).find("input[name=phone]").val();
  var msg = jQuery(this).find("._message1").val();

  var pric = jQuery(this).find("._price1").val();
  var parentIdCut = jQuery(this).find("input[name=booking_id]").val();


  let comment = {"first_name":`${first_name}`,"last_name":`${last_name}`,"email":`${email}`,"phone":`${phone}`,"message":`${msg}`,"billing_address_1":'true'};


  ajax_data = {
      'action': 'new_offer',
      'data_id': parentIdCut,
      'comment' : comment,
      'price' : pric
  };
  //var form_data = jQuery(this).serialize();
    jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: ajax_data,
        dataType: 'json',
        success: function (data) {
        },error: function(xhr, status, error){
          jQuery(".alert_success_message").show();
          jQuery(".alert_success_message").html("Send offer Successfully");
          setTimeout(function(){
            window.location.reload();
                /*jQuery(".alert_success_message").hide();
                jQuery(".alert_success_message").html("");
                jQuery(".send_newoffer_form").find(".message1").val("");
                jQuery(".send_newoffer_form").find("._price1").val("");
                jQuery("#newofferModal").hide();*/
                

          },2000);
        },
    });
})



</script>