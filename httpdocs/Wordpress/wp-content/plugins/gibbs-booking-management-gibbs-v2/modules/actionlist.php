<div class="outer-actions1">

    <?php

    $data->status = strtolower($data->status);

     if($page_type == "buyer"){ ?>
        <?php if($data->status == "confirmed" && isset($data->order_id) && !empty($data->order_id) && $data->order_id != 0){
            $order = wc_get_order( $data->order_id );
            if($order) {
                $payment_url = $order->get_checkout_payment_url();
            

        ?>
                <p data-link="<?php echo $payment_url; ?>" class="open_all_link payBtn">Betal <i class="fa fa-check"></i></p>
              <?php } ?>
        <?php } ?>
        <?php if(!isset($_GET["booking_id"])) { ?>
          <p data-link="?booking_id=<?php echo $data->id;?>" class="open_link_booking" new_tab="true">Åpne <i class="fas fa-book-open"></i></p>
        <?php } ?> 
        <p data-link="?booking_id=<?php echo $data->id;?>&tab=message" class="open_link">Send melding <i class="fas fa-envelope"></i></p>
        <?php if($data->status == "waiting" || $data->status == "confirmed" ||$data->status == "paid") { ?>
            <p data-link="?booking_id=<?php echo $data->id;?>&tab=message&task=cancel" class="open_link cancelBtn">Be om kansellering <i class="fa fa-envelope"></i></p>
        <?php } ?>
        <?php if($data->status == "paid" && isset($data->order_id) && $data->order_id != "" && $data->order_id != 0) { ?>
             <p class="reciept_modalbtn<?php echo $data->id;?>" data-order_id="<?php echo $data->order_id; ?>">Last ned kvittering <i class="fa fa-receipt"></i></p>
        <?php } ?>
        
    <?php }else{ ?>
        <?php if(!isset($_GET["booking_id"])) { ?>
           <p data-link="?booking_id=<?php echo $data->id;?>" class="open_link_booking" new_tab="true">Åpne <i class="fas fa-book-open"></i></p>
        <?php } ?> 
       
        <p data-link="<?php echo home_url();?>/kalender" class="open_all_link" new_tab="true">Vis egne felt <i class="fa fa-calendar"></i></p>
        <?php if($data->status == "paid" && ($data->fixed == "2" || $data->fixed == "3")) { ?>
            <p class="sent_invoice_modalbtn_css sent_invoice_modalbtn<?php echo $data->id;?>"  data-value="4" data-booking_id="<?php echo $data->id; ?>">Faktura er sendt <i class="fa fa-check"></i></p>
        <?php } ?>
        <?php if($data->status == "waiting") { ?>
            <p class="accept_decline_modalbtn_css accept_decline_modalbtn<?php echo $data->id;?>"  data-value="confirmed" data-booking_id="<?php echo $data->id; ?>">Godkjenn <i class="fa fa-check"></i></p>
            
            <!-- <p class="message_modalbtn"  data-recipient="<?php echo esc_attr($data->bookings_author); ?>" data-booking_id="booking_<?php echo $data->id; ?>">Send Message <i class="fas fa-envelope"></i></p> -->
        <?php } ?>
        <?php if($data->status == "waiting" || $data->status == "confirmed" || ($data->status == "paid" && ($data->fixed == "2" || $data->fixed == "3") ) || $data->status == "paid" || $data->status == "expired" || $data->status == "cancelled" || $data->status == "declined") { ?>
            <p data-link="?booking_id=<?php echo $data->id;?>&tab=message" class="open_link">Send melding <i class="fas fa-envelope"></i></p>
        <?php } ?>
        
        
        <?php if($data->status == "waiting" || $data->status == "confirmed" || $data->status == "paid" && ($data->fixed == "2" || $data->fixed == "3")  ) { ?>
           <!--  <p class="edit_modalbtn<?php echo $data->id;?>"   data-booking_id="<?php echo $data->id; ?>">Rask redigering <i class="fa fa-edit"></i></p> -->
           
        <?php } ?>
        <?php if($data->status == "waiting" || $data->status == "confirmed" || ($data->status == "paid" && ($data->fixed == "2" || $data->fixed == "3" || $data->order_id == "" || $data->order_id == 0) )) { ?>
             <p class="accept_decline_modalbtn_css accept_decline_modalbtn<?php echo $data->id;?> cancelBtn"  data-value="cancelled" data-booking_id="<?php echo $data->id; ?>">Avslå <i class="fa fa-remove"></i></p>
        <?php } ?>
        <?php if($data->status == "paid" && $data->fixed == "0" && isset($data->order_id) && $data->order_id != "" && $data->order_id != 0) { ?>
             <p class="reciept_modalbtn<?php echo $data->id;?>" data-order_id="<?php echo $data->order_id; ?>">Last ned kvittering <i class="fa fa-receipt"></i></p>
             <p class="accept_decline_modalbtn_css accept_decline_modalbtn<?php echo $data->id;?> cancelBtn"  data-value="cancelled" data-booking_id="<?php echo $data->id; ?>">Kanseller <i class="fa fa-remove"></i></p>
        <?php } ?>
    <?php } ?>
</div>