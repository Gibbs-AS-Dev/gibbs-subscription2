<div class="slots_div2" style="display: block;width: 100%;">
    <?php 

    
    $kkinc = 0;
    foreach($days as $key_day => $day){ 

        $from_time = ""; 
        $to_time = ""; 
        $closed = ""; 

        if(isset($field[$kkinc])){
           
            foreach($field[$kkinc] as $slot){

                $slot = explode( '|', $slot);

                if(isset($slot[0])){

                    $slot_time = explode( '-', $slot[0]);

                    $from_time = trim($slot_time[0]);
                    $to_time = trim($slot_time[1]);
                    
                }
                if(isset($slot[1])){

                   // $closed = $slot[1];
                    
                }
                if(isset($slot[2])){

                    $closed = $slot[2];
                    
                }

            } 
        }

        
        ?>

        <div class="row opening_slots_container">
            <div class="col-md-2">
                <span style="">
                    <label>
                       Fra dag
                    </label>
                    <select  class="op_from_day">
                        <option value="<?php echo $key_day;?>">
                            <?php echo $day;?>
                        </option>
                            
                    </select>
                </span>
            </div>
            <div class="col-md-2">
                <span style="">
                    <label>
                    Fra klokken
                    </label>
                <select class="op_from_time">
                        <option <?php if($from_time == "00:00"){ echo 'selected';}?>>00:00</option>
                        <option <?php if($from_time == "01:00"){ echo 'selected';}?>>01:00</option>
                        <option <?php if($from_time == "02:00"){ echo 'selected';}?>>02:00</option>
                        <option <?php if($from_time == "03:00"){ echo 'selected';}?>>03:00</option>
                        <option <?php if($from_time == "04:00"){ echo 'selected';}?>>04:00</option>
                        <option <?php if($from_time == "05:00"){ echo 'selected';}?>>05:00</option>
                        <option <?php if($from_time == "06:00"){ echo 'selected';}?>>06:00</option>
                        <option <?php if($from_time == "07:00"){ echo 'selected';}?>>07:00</option>
                        <option <?php if($from_time == "08:00"){ echo 'selected';}?>>08:00</option>
                        <option <?php if($from_time == "09:00"){ echo 'selected';}?>>09:00</option>
                        <option <?php if($from_time == "10:00"){ echo 'selected';}?>>10:00</option>
                        <option <?php if($from_time == "11:00"){ echo 'selected';}?>>11:00</option>
                        <option <?php if($from_time == "12:00"){ echo 'selected';}?>>12:00</option>
                        <option <?php if($from_time == "13:00"){ echo 'selected';}?>>13:00</option>
                        <option <?php if($from_time == "14:00"){ echo 'selected';}?>>14:00</option>
                        <option <?php if($from_time == "15:00"){ echo 'selected';}?>>15:00</option>
                        <option <?php if($from_time == "16:00"){ echo 'selected';}?>>16:00</option>
                        <option <?php if($from_time == "17:00"){ echo 'selected';}?>>17:00</option>
                        <option <?php if($from_time == "18:00"){ echo 'selected';}?>>18:00</option>
                        <option <?php if($from_time == "19:00"){ echo 'selected';}?>>19:00</option>
                        <option <?php if($from_time == "20:00"){ echo 'selected';}?>>20:00</option>
                        <option <?php if($from_time == "21:00"){ echo 'selected';}?>>21:00</option>
                        <option <?php if($from_time == "22:00"){ echo 'selected';}?>>22:00</option>
                        <option <?php if($from_time == "23:00"){ echo 'selected';}?>>23:00</option>
                        <option <?php if($from_time == "23:59"){ echo 'selected';}?>>23:59</option>
                </select>
                </span>
            </div>
            <div class="col-md-2">
                <span style="">
                    <label>
                    Til klokken
                    </label>
                <select class="op_to_time">
                        <option value="">Velg</option>
                        <option <?php if($to_time == "00:00"){ echo 'selected';}?>>00:00</option>
                        <option <?php if($to_time == "01:00"){ echo 'selected';}?>>01:00</option>
                        <option <?php if($to_time == "02:00"){ echo 'selected';}?>>02:00</option>
                        <option <?php if($to_time == "03:00"){ echo 'selected';}?>>03:00</option>
                        <option <?php if($to_time == "04:00"){ echo 'selected';}?>>04:00</option>
                        <option <?php if($to_time == "05:00"){ echo 'selected';}?>>05:00</option>
                        <option <?php if($to_time == "06:00"){ echo 'selected';}?>>06:00</option>
                        <option <?php if($to_time == "07:00"){ echo 'selected';}?>>07:00</option>
                        <option <?php if($to_time == "08:00"){ echo 'selected';}?>>08:00</option>
                        <option <?php if($to_time == "09:00"){ echo 'selected';}?>>09:00</option>
                        <option <?php if($to_time == "10:00"){ echo 'selected';}?>>10:00</option>
                        <option <?php if($to_time == "11:00"){ echo 'selected';}?>>11:00</option>
                        <option <?php if($to_time == "12:00"){ echo 'selected';}?>>12:00</option>
                        <option <?php if($to_time == "13:00"){ echo 'selected';}?>>13:00</option>
                        <option <?php if($to_time == "14:00"){ echo 'selected';}?>>14:00</option>
                        <option <?php if($to_time == "15:00"){ echo 'selected';}?>>15:00</option>
                        <option <?php if($to_time == "16:00"){ echo 'selected';}?>>16:00</option>
                        <option <?php if($to_time == "17:00"){ echo 'selected';}?>>17:00</option>
                        <option <?php if($to_time == "18:00"){ echo 'selected';}?>>18:00</option>
                        <option <?php if($to_time == "19:00"){ echo 'selected';}?>>19:00</option>
                        <option <?php if($to_time == "20:00"){ echo 'selected';}?>>20:00</option>
                        <option <?php if($to_time == "21:00"){ echo 'selected';}?>>21:00</option>
                        <option <?php if($to_time == "22:00"){ echo 'selected';}?>>22:00</option>
                        <option <?php if($to_time == "23:00"){ echo 'selected';}?>>23:00</option>
                        <option <?php if($to_time == "23:59" || $to_time == "24:00" || $to_time == ""){ echo 'selected';}?>>23:59</option>
                </select>
                </span>
            </div>
            <div class="col-md-2">
                <span style="">
                    <label>
                      Stengt <input type="checkbox" class="op_closed" <?php if($closed == "1"){ echo "checked";}?>>
                    </label>
                </span>
            </div>
        </div>    
        
    <?php
       
       $kkinc++;

    } ?>

</div>	
<script>
    jQuery(".op_closed").change(function(){
        var fr_time = jQuery(this).parent().parent().parent().parent().find(".op_from_time");
        var to_time = jQuery(this).parent().parent().parent().parent().find(".op_to_time");
        if(this.checked == true){
            fr_time.val("00:00").addClass("gray_dis");
            to_time.val("00:00").addClass("gray_dis");
        }else{
            fr_time.val("00:00").removeClass("gray_dis");
            to_time.val("23:59").removeClass("gray_dis");
        }
        fr_time.change();
        to_time.change();
    })
</script>