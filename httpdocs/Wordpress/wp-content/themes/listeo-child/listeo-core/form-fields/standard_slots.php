<div class="slots_div2 standard_slots_main" style="display: block;width: 100%;">
    <?php 

    $days = array(
        '1'	=> __('Monday','listeo_core'),
        '2' 	=> __('Tuesday','listeo_core'),
        '3' => __('Wednesday','listeo_core'),
        '4' 	=> __('Thursday','listeo_core'),
        '5' 	=> __('Friday','listeo_core'),
        '6' 	=> __('Saturday','listeo_core'),
        '7' 	=> __('Sunday','listeo_core'),
    ); 

    $standard_patern = get_post_meta($_GET["listing_id"],"standard_patern",true);

    if(empty($standard_patern)){
        $standard_patern = array();
        $standard_patern[0]["standard_from_day"] = "1";
        $standard_patern[0]["standard_from_time"] = "00:00";
        $standard_patern[0]["standard_to_time"] = "23:59";
        $standard_patern[0]["standard_closed"] = "1";
        $standard_patern[0]["standard_duration"] = "30";
        $standard_patern[0]["standard_slot_price"] = "";
        $standard_patern[0]["standard_slots"] = "1";
        $standard_patern[0]["standard_all_slot_price"] = "";
    }

    foreach($standard_patern as $key_st => $standard_pat){

    ?>
        <div class="row standard_slots_container">
            <div class="col-md-2">
                <span style="">
                    <label>
                    Fra dag 
                    </label>
                    <select name="standard_from_day[]">
                        <?php foreach($days as $key_day => $day){ ?>
                            <option value="<?php echo $key_day;?>" <?php if($standard_pat["standard_from_day"] == $key_day){ echo 'selected';}?>><?php echo $day;?></option>
                        <?php } ?>    
                        
                </select>
                </span>
            </div>
            <div class="col-md-1">
                    <span style="">
                        <label>
                        Fra tid
                        </label>
                        <select name="standard_from_time[]" class="standard_from_time <?php echo $gray_dis;?>">
                                <?php 
                                    $startTime = new DateTime("00:00");
                                    $endTime = new DateTime("24:00");

                                    $current = clone $startTime;

                                    $kinc = 0;
                                    

                                    while ($current <= $endTime) {

                                        $fr_tm = $current->format('H:i');
                                        if($kinc > 0 && $fr_tm == "00:00"){
                                            $fr_tm = "23:59";
                                        }
                                    ?> 
                                        <option value="<?php echo $fr_tm;?>" <?php if($standard_pat["standard_from_time"] == $fr_tm){ echo 'selected';}?>><?php echo $fr_tm;?></option>
                                    <?php

                                        $current->add(new DateInterval('PT15M'));

                                        $kinc++;

                                    }
                                ?>
                                
                        </select>    
                
                </span>
            </div>
            <div class="col-md-1">
                <span style="">
                    <label>
                    Til tid
                    </label>
                    <select name="standard_to_time[]" class="standard_to_time <?php echo $gray_dis;?>">
                            <?php 
                                $startTime = new DateTime("00:00");
                                $endTime = new DateTime("24:00");

                                $current = clone $startTime;

                                $kinc = 0;
                                

                                while ($current <= $endTime) {

                                    $fr_tm = $current->format('H:i');
                                    if($kinc > 0 && $fr_tm == "00:00"){
                                        $fr_tm = "23:59";
                                    }
                                ?> 
                                    <option value="<?php echo $fr_tm;?>" <?php if($standard_pat["standard_to_time"] == $fr_tm){ echo 'selected';}?>><?php echo $fr_tm;?></option>
                                <?php

                                    $current->add(new DateInterval('PT15M'));

                                    $kinc++;

                                }
                            ?>
                                
                    </select> 
                </span>
            </div>
            <div class="col-md-2">
                <span style="">
                    <label>
                        Intervall <i class="tip" data-tip-content="Bestem hvor lang økten(ene) skal være i bestemt tidsrom "></i>
                    </label>
                    <select name="standard_duration[]">
                        <?php for($iii = 15;$iii <= 600; $iii += 15){ ?>
                            <option value="<?php echo $iii;?>" <?php if($standard_pat["standard_duration"] == $iii){ echo 'selected';}?>><?php echo $iii;?>min</option>
                        <?php } ?>
                    </select>
                </span>
            </div>
        
            
            <div class="col-md-2">
                <span style="">
                    <label>
                    Pris for hele <i class="tip" data-tip-content="Prisen for å bestille alle/hele objektet eller alle tilgjengelige tidsluker"></i>
                    </label>
                    <div class="select-input disabled-first-option">
                        <i class="data-unit"></i><input type="number" class="input-text" name="standard_all_slot_price[]"  value="<?php echo $standard_pat["standard_all_slot_price"];?>" step="any" placeholder="" maxlength="" limitchar="" data-unit="">
                    </div>
                </span>
            </div>
            <div class="col-md-2 slot_price_div" style="display:none">
                <span style="">
                    <label>
                    Pris per stk 
                    </label>
                    <div class="select-input disabled-first-option">
                        <i class="data-unit"></i><input type="number" class="input-text" name="standard_slot_price[]"  value="<?php echo $standard_pat["standard_slot_price"];?>" step="any" placeholder="" maxlength="" limitchar="" data-unit="">
                    </div>
                </span>
            </div>

            <div class="col-md-1 custom_class_slots_amount">
                <span style="">
                    <label style="display: flex;">
                    Antall <i class="tip" data-tip-content="Velg antall tidsluker som er tilgjengelig for valgt tid"></i>
                    </label>
                    <div class="select-input disabled-first-option">
                    <?php if($slots == ""){ $slots = 1;} ?>
                        <i class="data-unit"></i><input type="number" min="1" class="input-text" name="standard_slots[]"  value="<?php echo $standard_pat["standard_slots"];?>" step="any" placeholder=""  maxlength="" limitchar="" data-unit="">
                    </div>
                </span>
            </div>
          
            <div class="col-md-1">
                <span class="close_div" <?php if($key_st == 0){ echo 'style="display: none"';}?>>
                    <label style="visibility: hidden;">
                    Lukk
                    </label>
                    <div class="select-input disabled-first-option delete_slot">
                        <i class="fa fa-close" style="font-size:16px;color:red"></i>
                    </div>
                </span>
            </div>
        </div>
    <?php } ?>    

</div>	
<div class="row">
    <div class="col-md-12">
        <button type="button" style="margin-bottom: 25px" class="btn btn-primary addStandardSlot button">Legg til flere dager </button>
    </div>
</div>
<script>
    jQuery(".addStandardSlot").on("click",function(e){
        e.preventDefault();
        let clone_div = jQuery(".standard_slots_main").find(".row").first().clone();
        jQuery(clone_div).find(".close_div").show();
        jQuery(".standard_slots_main").append(clone_div);
        
    })
    jQuery(".check_stand").change(function(){
        var st_fr_time = jQuery(this).parent().parent().parent().parent().find(".standard_from_time");
        var st_to_time = jQuery(this).parent().parent().parent().parent().find(".standard_to_time");
        if(this.checked == true){
            jQuery(this).parent().find(".check_stand_val").val("1");
            st_fr_time.val("00:00").addClass("gray_dis");
            st_to_time.val("00:00").addClass("gray_dis");
        }else{
            jQuery(this).parent().find(".check_stand_val").val("0");
            st_fr_time.val("00:00").removeClass("gray_dis");
            st_to_time.val("23:59").removeClass("gray_dis");
        }
        st_fr_time.change();
        st_to_time.change();
    })
</script>