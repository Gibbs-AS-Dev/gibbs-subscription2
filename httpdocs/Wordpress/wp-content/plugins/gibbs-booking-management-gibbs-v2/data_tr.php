<?php

$get_author_id = $data->bookings_author;
$get_author_gravatar = get_avatar_url($get_author_id, array('size' => 450)); 

$user_data = get_userdata($get_author_id);

$first_name = get_user_meta($get_author_id,"first_name",true);
$last_name = get_user_meta($get_author_id,"last_name",true);
$phone = get_user_meta($get_author_id,"phone",true);

$email = $user_data->user_email;
$data->status = strtolower($data->status);
?>                            
<tr>	
	<?php if($page_type == "owner" && $booking_type != "rec"){ ?>
	  <!--  <td width="5%" class="booking_check_div">
	        <div class="dynamic checkboxes-booking in-row">
	         <input id="booking_check_<?php echo $data->id;?>"  type="checkbox" name="booking_check"><label  for="booking_check_<?php echo $data->id;?>"></label>
	        </div> 
	    </td> -->
	<?php } ?>
    <?php foreach ($active_columns as $key => $active_column) { ?>		

        <?php  if($active_column == "name") { ?>            	
            <td width="20%" data-sort="<?php echo $data->bookings_author_name;?>">
            	<div class="pr_tdd">
                    	<b>
                            <?php if($data->order_id != "" && $data->order_id != 0){ 
                                echo "#".$data->order_id;
                            } ?>    

                            <?php //echo $data->bookings_author_name;?>
                            <?php echo substr(strip_tags( $data->bookings_author_name), 0, 20); ?>
                        </b>
                        <?php if($data->message != ""){ ?>
                            <i class="far fa-comment" title="<?php echo $data->message;?>"></i>
                        <?php  } ?>
                    	<!-- <p style="word-break: break-all;"><?php echo substr(strip_tags( $data->message), 0, 30); ?></p> -->
                </div>
            </td>
        <?php }elseif($active_column == "listing") { ?>                
            <td class="listing_name_td" width="10%" data-sort="<?php echo $data->listing_name;?>">
                <span><?php echo substr(strip_tags(ucfirst($data->listing_name)), 0, 50); ?></span> 
            </td>
        <?php }elseif($active_column == "booking_id") { ?>                
            <td width="10%" data-sort="<?php echo $data->id;?>">
                <?php echo ucfirst($data->id);?>
                <?php if($page_type == "owner"){ ?>
                    <?php if($data->first_event_id_text == "true" && $booking_type != "rec"){ ?>
                      <i class="fa fa-link" title="This is connected booking. Please open the booking to see more details."></i>
                    <?php } ?>
                   <!--  <?php if($data->conflict == "true"){ ?>
                      <i class="fa fa-exclamation-circle" title="Conflict booking. Please open the booking to see more details." style="color: red;"></i>
                    <?php } ?> -->
                <?php } ?>
                    
            </td>
        <?php }elseif($active_column == "date") { ?>                
            <td width="30%" data-sort="<?php echo $data->booking_start;?>">
                <?php if(isset($data->rec_date) && $data->rec_date != ""){ 
                    echo date("d M Y",strtotime($data->rec_date))." ".$data->start_time." - <br>".date("d M Y",strtotime($data->rec_date))." ".$data->end_time;
                }else{ ?>
                    <?php echo $data->booking_date;?>
                    <?php if($data->recurrenceRule_text == "true"){ ?>
                      <i class="fa fa-rotate" title="This is repeated booking. Please open the booking to see more details."></i>
                   <?php } ?>
               <?php } ?>
            </td>
        <?php }elseif($active_column == "price") { ?>                
            <td width="10%" data-sort="<?php echo $data->price;?>">
                <?php if($data->price == "" || $data->price == 0){ ?>    
                   <?php echo __("Gratis","gibbs");?>
                <?php }else{ ?>
                    <?php echo get_woocommerce_currency_symbol();?> <?php echo $data->price;?>
                <?php } ?>
            </td> 
        <?php  }elseif($active_column == "status") { ?>                
            <td  width="20%" data-sort="<?php echo $data->status;?>">
               
                <?php if(isset($booking_type) && $booking_type == "rec"){ ?>

                    <?php

                        if(isset($data->rec_exp) && $data->rec_exp != ""){ 

                            $exp_rule = array();

                            if($data->recurrenceException != ""){
                                $exp_rule = explode(",", $data->recurrenceException);
                            }

                            if(in_array($data->rec_exp, $exp_rule)){
                                $data->status = "declined";
                            }
                        ?>
                    <?php } ?>   

                         <?php if($data->status == "waiting"){ ?>
                               <span class="yellow-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php }elseif($data->status == "confirmed"){ ?>
                               <span class="approved-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php }elseif($data->status == "paid"){ ?>
                               <span class="paid-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php }elseif($data->status == "expired" || $data->status == "cancelled" || $data->status == "declined"){ ?>
                               <span class="cancelled-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php }else{ ?>
                               <span class="red-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php } ?>
                <?php }else{ ?>
                    <?php if($active == "invoice" && $data->fixed == "2"){ ?>
                        <span class="season-btn"><?php echo __("Sesongbooking");?></span>
                    <?php }elseif($active == "invoice" && $data->fixed == "3"){ ?>
                        <span class="season-btn"><?php echo __("Usendt faktura");?></span>
                    <?php }elseif($active == "invoice_sent" && $data->fixed == "4"){ ?>
                        <span class="season-btn"><?php echo __("Sendt faktura");?></span>
                    <?php }else{ ?>
                        <?php if($data->status == "waiting"){ ?>
                               <span class="yellow-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php }elseif($data->status == "confirmed"){ ?>
                               <span class="approved-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php }elseif($data->status == "paid"){ ?>
                                <?php if($data->fixed == "2"){ ?>
                                    <span class="season-btn"><?php echo __("Sesongbooking");?></span>
                                <?php }elseif($data->fixed == "3"){ ?>
                                    <span class="season-btn"><?php echo __("Usendt faktura");?></span>
                                <?php }elseif($data->fixed == "4"){ ?>
                                    <span class="season-btn"><?php echo __("Sendt faktura");?></span>
                                <?php }elseif($data->fixed == "1"){ ?>
                                    <span class="cancelled-btn"><?php echo __("Stengt");?></span>
                                <?php }else{ ?>
                                     <span class="paid-btn"><?php echo __($data->status,"gibbs");?></span>
                                <?php } ?>
                        <?php }elseif($data->status == "expired" || $data->status == "cancelled" || $data->status == "declined"){ ?>
                               <span class="cancelled-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php }else{ ?>
                               <span class="yellow-btn"><?php echo __($data->status,"gibbs");?></span>
                        <?php } ?>
                       
                    <?php } ?>  
                <?php } ?>    
            </td>
        <?php }elseif($active_column == "purpose") { ?>                
            <td><?php echo $data->purpose;?></td>
        <?php }elseif($active_column == "discount_group") { ?>                
            <td><?php echo $data->discount_group;?></td>
        <?php }elseif($active_column == "created_at") { ?>                
            <td data-sort="<?php echo $data->created;?>"><?php echo ucfirst($data->created);?> 
        <?php }elseif($active_column == "last_updated") { ?>                
            <td data-sort="<?php echo $data->updated_at;?>"><?php echo ucfirst($data->updated_at);?> 
        <?php }elseif($active_column == "listing_name") { ?>                
            <td data-sort="<?php echo $data->listing_name;?>"><?php echo ucfirst($data->listing_name);?> 
        <?php }elseif($active_column == "sub_listing_name") { ?>                
            <td data-sort="<?php echo $data->sub_listing_name;?>"><?php echo ucfirst($data->sub_listing_name);?> 
        <?php }elseif($active_column == "booking_message") { ?>                
            <td style="word-break: break-all;"><?php echo $data->message;?></td>
        <?php }elseif($active_column == "order_number") { ?>                
            <td><?php echo $data->order_id;?></td>
        <?php }elseif($active_column == "customer_name") { ?>                
            <td><?php echo $data->display_name;?></td>
        <?php }elseif($active_column == "customer_email") { ?>                
            <td><?php echo $data->customer_email;?></td>
        <?php }elseif($active_column == "customer_tlf") { ?>                
            <td><?php echo $data->customer_tlf;?></td>
        <?php }elseif($active_column == "customer_address") { ?>                
            <td><?php echo $data->customer_address;?></td>
        <?php }elseif($active_column == "customer_zip") { ?>                
            <td><?php echo $data->customer_zip;?></td>
        <?php }elseif($active_column == "customer_city") { ?>                
            <td><?php echo $data->customer_city;?></td>
        <?php }elseif($active_column == "billing_name") { ?>                
            <td><?php echo $data->billing_name;?></td>
        <?php }elseif($active_column == "billing_email") { ?>                
            <td><?php echo $data->billing_email;?></td>
        <?php }elseif($active_column == "billing_tlf") { ?>                
            <td><?php echo $data->billing_tlf;?></td>
        <?php }elseif($active_column == "billing_address") { ?>                
            <td><?php echo $data->billing_address;?></td>
        <?php }elseif($active_column == "billing_zip") { ?>                
            <td><?php echo $data->billing_zip;?></td>
        <?php }elseif($active_column == "billing_city") { ?>                
            <td><?php echo $data->billing_city;?></td>
        <?php }elseif($active_column == "coupon") { ?>                
            <td><?php echo $data->coupon;?></td>
        <?php }elseif($active_column == "payment_type") { ?>                
            <td><?php echo $data->payment_type;?></td>
        <?php }elseif($active_column == "guest_amount") { ?>                
            <td><?php echo $data->guest_amount;?></td>
        <?php }elseif($active_column == "refund") { ?>                
            <td><?php echo $data->refund;?></td>
        <?php }elseif($active_column == "csv") { ?>                
            <td><?php //echo $data->coupon;?></td>
        <?php }elseif($active_column == "custom_fields") { ?>                
            <td><div class="outer-form-tabs"><?php echo $data->fields_data_html;?></div></td>
        <?php }else{ ?>
            <td></td>
       <?php } ?>
        
           
        
       
       
        
        <!-- <td width="10%" data-sort="<?php echo $data->booking_created;?>"><?php echo date("d M Y H:i",strtotime($data->booking_created));?></td> -->
           
       
    <?php } ?>    
    <?php if($page_type == "buyer" && $booking_type == "rec"){ ?>
    <?php }else{ ?>
        <td width="10%">
                <?php if(isset($booking_type) && $booking_type == "rec"){ ?>
                    <?php
                    if(isset($data->rec_exp) && $data->rec_exp != ""){ 

                        $exp_rule = array();

                        if($data->recurrenceException != ""){
                            $exp_rule = explode(",", $data->recurrenceException);
                        }

                        if(in_array($data->rec_exp, $exp_rule)){ ?>

                            <button class="bt btn-warning un_delete_rec"  data-rec_exp="<?php echo $data->rec_exp;?>" data-booking_id="<?php echo $data->id;?>">
                                Gjenopprett
                            </button>

                        <?php }else{?>
                            <button class="bt btn-danger delete_rec"  data-rec_exp="<?php echo $data->rec_exp;?>" data-booking_id="<?php echo $data->id;?>">
                                Avslå
                            </button>
                        <?php } ?>
                    <?php } ?>    
                <?php }else{ ?>
                    <div class="search-box-inner action-modal1 action_btns">
                        <div class="dropdown">
                          <button class="dropbtn">  <i class="fa-solid fa-ellipsis"></i> </button>
                          <div id="listingDropdown" class="dropdown-content">
                              <?php require(__DIR__."/modules/actionlist.php");?>
                          </div>
                        </div>
                    </div>
                    <?php require(__DIR__."/modal/sent_invoice_modal.php");?>
                    <?php require(__DIR__."/modal/accept_decline_modal.php");?>
                    <?php require(__DIR__."/modal/edit_modal.php");?>
                    <?php require(__DIR__."/modal/reciept_modal.php");?>
                <?php }?>
        </td>
    <?php } ?>

</tr>

