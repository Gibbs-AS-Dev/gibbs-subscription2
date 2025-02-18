<tr>	
    <?php foreach ($active_columns as $key => $active_column) { ?>		

        
        <?php if($active_column == "ordre_id") { ?>                
            <td><?php echo $data->order_id;?></td>
        <?php }elseif($active_column == "listing_id") { ?>                
            <td><?php echo $data->listing_id;?></td>
        <?php }elseif($active_column == "listing_name") { ?>                
            <td data-sort="<?php echo $data->listing_name;?>"><?php echo ucfirst($data->listing_name);?> </td>
        <?php }elseif($active_column == "date") { ?>                
            <td width="30%" data-sort="<?php echo $data->start_datetime;?>">
                <?php if(isset($data->start_datetime) && $data->start_datetime != ""){ 
                    echo date("d, M Y H:i",strtotime($data->start_datetime))." - <br>".date("d, M Y H:i",strtotime($data->end_datetime));
                }
                ?>
            </td>
        <?php }elseif($active_column == "name") { ?>                
           <td><?php echo ucfirst($data->name);?> </td>
        <?php  }elseif($active_column == "email") { ?>                
            <td data-sort="<?php echo $data->email;?>"><?php echo ucfirst($data->email);?> </td>
        <?php }elseif($active_column == "phone_number") { ?>                
            <td style="word-break: break-all;"><?php echo $data->country_code;?> <?php echo $data->phone_number;?></td>
        <?php }elseif($active_column == "booking_status") { ?>                
            <td  width="20%" data-sort="<?php echo $data->booking_status;?>">
               
                <?php if($data->booking_status == "waiting"){ ?>
                           <span class="yellow-btn"><?php echo __($data->booking_status,"gibbs");?></span>
                    <?php }elseif($data->booking_status == "confirmed"){ ?>
                           <span class="approved-btn"><?php echo __($data->booking_status,"gibbs");?></span>
                    <?php }elseif($data->booking_status == "paid"){ ?>
                           <span class="paid-btn"><?php echo __($data->booking_status,"gibbs");?></span>
                    <?php }elseif($data->booking_status == "expired" || $data->booking_status == "cancelled" || $data->booking_status == "declined"){ ?>
                           <span class="cancelled-btn"><?php echo __($data->booking_status,"gibbs");?></span>
                    <?php }else{ ?>
                           <span class="red-btn"><?php echo __($data->booking_status,"gibbs");?></span>
                    <?php } ?>  
            </td>
        <?php }elseif($active_column == "payment_status") { ?>                
            <td  width="20%" data-sort="<?php echo $data->payment_status;?>">
               
                <?php if($data->payment_status == "waiting"){ ?>
                           <span class="yellow-btn"><?php echo __($data->payment_status,"gibbs");?></span>
                    <?php }elseif($data->payment_status == "confirmed"){ ?>
                           <span class="approved-btn"><?php echo __($data->payment_status,"gibbs");?></span>
                    <?php }elseif($data->payment_status == "paid"){ ?>
                           <span class="paid-btn"><?php echo __($data->payment_status,"gibbs");?></span>
                    <?php }elseif($data->payment_status == "expired" || $data->payment_status == "cancelled" || $data->payment_status == "declined"){ ?>
                           <span class="cancelled-btn"><?php echo __($data->payment_status,"gibbs");?></span>
                    <?php }else{ ?>
                           <span class="red-btn"><?php echo __($data->payment_status,"gibbs");?></span>
                    <?php } ?>  
            </td>
        <?php }elseif($active_column == "lock_status") { ?>                
            <td  width="20%" data-sort="<?php echo $data->lock_status;?>">
               
                <?php if($data->lock_status == ""){ ?>
                           <span class="paid-btn"><?php echo __("Online","gibbs");?></span>
                    <?php }else{ ?>
                        <span class="paid-btn"><?php echo __("Online","gibbs");?></span>
                    <?php } ?>  
            </td>
        <?php }elseif($active_column == "access_code") { ?>                
            <td><?php echo $data->access_code;?></td>
        <?php }elseif($active_column == "sms_time") { ?>                
            <td><?php echo $data->sms_time;?></td>
        <?php }elseif($active_column == "sms_status") { ?>                
            <td>
                <?php  

                if (strpos($data->sms_status, "accepted_at") !== false) {
                    echo __("Sent","gibbs");
                }else{
                    echo __("Waiting","gibbs");
                }
                ?>
            </td>
        <?php }else{ ?>
            <td></td>
       <?php } ?>
    
    <?php } ?>    
    

</tr>

