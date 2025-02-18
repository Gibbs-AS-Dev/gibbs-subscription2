<!-- Set data-clock-type="24hr" to enable 24 hours clock type -->
<?php $clock_format = get_option('listeo_clock_format','12') ?>
<?php
$enable_slot_price = "";
if(isset($_GET["listing_id"])){
   $_booking_system_service = get_post_meta($_GET["listing_id"],"_booking_system_service",true);

   $_booking_slots = get_post_meta($_GET["listing_id"],"_booking_slots",true);

   $slots_type = get_post_meta($_GET["listing_id"],"slots_type",true);

   $enable_slot_price = get_post_meta($_GET["listing_id"],"enable_slot_price",true);

   $all_slot_price_label = get_post_meta($_GET["listing_id"],"all_slot_price_label",true);
   $slot_price_label = get_post_meta($_GET["listing_id"],"slot_price_label",true);

}
?>
<div class="availability-slots-new" data-clock-type="<?php echo esc_attr($clock_format); ?>hr" <?php if($_booking_system_service != "on"){ echo "style='display:none'";}?>>
        <div class="slot_option">
        	<div class="slot-fillter">
				<label>Velg type tidslukesystem</label>
				<select name="slots_type" class="slots_type">
					<option value="standard"  <?php if($slots_type == "standard"){ echo "selected";}?>>Standard</option>
					<option value="advanced" <?php if($slots_type == "advanced"){ echo "selected";}?>>Avansert</option>
				</select>
			</div>
		   <div class="activate-price-fillter" style="width: 20%;">
			   	<label>Aktiver pris per stk <i class="tip" data-tip-content="Slå på kun hvis du leier ut mer enn 1 stk av noe  "></i></label> 
			   	<div class="switch_box box_1">
					<input type="checkbox" class="input-checkbox switch_1" name="enable_slot_price" id="enable_slot_price" placeholder="" value="on"  maxlength="" <?php if($enable_slot_price == "on"){ echo "checked";}?>>
				</div>
		   </div>
		   <div class="price_label-fillter" style="display: none;">
			   	<label>Pris for hele label <i class="tip" data-tip-content="Pris for hele label"></i></label> 
			   	<div class="select-input disabled-first-option">
					<i class="data-unit"></i><input type="text" class="input-text" name="all_slot_price_label" id="all_slot_price_label" value="<?php echo $all_slot_price_label;?>"  placeholder="" maxlength="" limitchar="" data-unit="">
				</div>
		   </div>
		   <div class="price_label-fillter" style="display: none;">
			   	<label>Pris per stk label <i class="tip" data-tip-content="Pris per stk label"></i></label> 
			   	<div class="select-input disabled-first-option">
					<i class="data-unit"></i><input type="text" class="input-text" name="slot_price_label" id="slot_price_label" value="<?php echo $slot_price_label;?>"  placeholder="" maxlength="" limitchar="" data-unit="">
				</div>
		   </div>
		</div>
		<div class="standard_div" <?php if($slots_type != "standard" && $slots_type != ""){ ?> style="display: none;" <?php } ?>>
            <?php require_once("standard_slots.php");?>
		</div>
		<div class="advanced_div" <?php if($slots_type != "advanced" || $slots_type == ""){ ?> style="display: none;" <?php } ?>>
			<div class="slots_div">
				<?php 

				//echo "<pre>"; print_r($_booking_slots); die;

				if($_booking_slots && !empty($_booking_slots)){?>
					<?php foreach ($_booking_slots as $key_slot => $_booking_slot) {

						$slott = explode("|", $_booking_slot);

						$from_day = $slott[0];
						$from_time = $slott[1];
						$to_day = $slott[2];
						$to_time = $slott[3];
						$slot_price = $slott[4];
						$slots = $slott[5];
						$inc = (isset($slott[6]))?$slott[6]:"";
						$closed = (isset($slott[7]))?$slott[7]:"";
						$all_slot_price = (isset($slott[8]))?$slott[8]:"";

						?>
						<div class="row">
						<div class="col-md-2">
						<span style="">
							<label>
							Fra dag
							</label>
							<select name="from_day[]" id="from_day">
								<option value="">Velg</option>
									<option value="1" <?php if($from_day == "1"){ echo 'selected';}?>>Mandag</option>
									<option value="2" <?php if($from_day == "2"){ echo 'selected';}?>>Tirsdag</option>
									<option value="3" <?php if($from_day == "3"){ echo 'selected';}?>>Onsdag</option>
									<option value="4" <?php if($from_day == "4"){ echo 'selected';}?>>Torsdag</option>
									<option value="5" <?php if($from_day == "5"){ echo 'selected';}?>>Fredag</option>
									<option value="6" <?php if($from_day == "6"){ echo 'selected';}?>>Lørdag</option>
									<option value="7" <?php if($from_day == "7"){ echo 'selected';}?>>Søndag</option>
									
							</select>
						</span>
						</div>
						<div class="col-md-1">
						<span style="">
							<label>
							Fra tid
							</label>
							<select name="from_time[]" id="from_time">
								<option value="">Velg</option>
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
										<option value="<?php echo $fr_tm;?>" <?php if($from_time == $fr_tm){ echo 'selected';}?>><?php echo $fr_tm;?></option>
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
							Til dag
							</label>
							<select name="to_day[]" id="to_day">
								<option value="0">Velg</option>
								<option value="1" <?php if($to_day == "1"){ echo 'selected';}?>>Mandag</option>
								<option value="2" <?php if($to_day == "2"){ echo 'selected';}?>>Tirsdag</option>
								<option value="3" <?php if($to_day == "3"){ echo 'selected';}?>>Onsdag</option>
								<option value="4" <?php if($to_day == "4"){ echo 'selected';}?>>Torsdag</option>
								<option value="5" <?php if($to_day == "5"){ echo 'selected';}?>>Fredag</option>
								<option value="6" <?php if($to_day == "6"){ echo 'selected';}?>>Lørdag</option>
								<option value="7" <?php if($to_day == "7"){ echo 'selected';}?>>Søndag</option>
									
									
							</select>
						</span>
						</div>
						<div class="col-md-1">
						<span style="">
							<label>
							Til tid
							</label>
							<select name="to_time[]" id="to_time">
								<option value="">Velg</option>
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
										<option value="<?php echo $fr_tm;?>" <?php if($to_time == $fr_tm){ echo 'selected';}?>><?php echo $fr_tm;?></option>
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
							Pris for hele  <i class="tip" data-tip-content="Prisen for å bestille alle/hele objektet eller alle tilgjengelige tidsluker "></i>
							</label>
							<div class="select-input disabled-first-option">
								<i class="data-unit"></i><input type="number" class="input-text" name="all_slot_price[]" id="all_slot_price" value="<?php echo $all_slot_price;?>" step="any" placeholder="" maxlength="" limitchar="" data-unit="">
							</div>
						</span>
						</div>
						<div class="col-md-2 slot_price_div" style="display:none">
							<span style="">
								<label>
								Pris per stk
								</label>
								<div class="select-input disabled-first-option">
									<i class="data-unit"></i><input type="number" class="input-text" name="slot_price[]" id="slot_price" value="<?php echo $slot_price;?>" step="any" placeholder="" maxlength="" limitchar="" data-unit="">
								</div>
							</span>
						</div>
						
						<div class="col-md-1 custom_class_slots_amount">
						<span style="">
							<label>
							Antall <i class="tip" data-tip-content="Velg antall tidsluker som er tilgjengelig for valgt tid"></i>
							</label>
							<div class="select-input disabled-first-option">
								<?php if($slots == ""){ $slots = 1;} ?>
								<i class="data-unit"></i><input type="number" min="1" class="input-text" name="slots[]" id="slots" value="<?php echo $slots;?>" step="any" placeholder=""  maxlength="" limitchar="" data-unit="">
							</div>
						</span>
						</div>

						<div >
						<span class="close_div" <?php if($key_slot == 0){ echo 'style="display: none"';}?>>
							<label style="visibility: hidden;">
							Lukk
							</label>
							<div class="select-input disabled-first-option delete_slot">
								<i class="fa fa-close" style="font-size:16px;color:red"></i>
							</div>
						</span>
						</div>
					</div>
					<?php  } ?>
				<?php }else{ ?>
					<div class="row">
						<div class="col-md-2">
						<span style="">
							<label>
							Fra dag 
							</label>
							<select name="from_day[]" id="from_day">
								<option value="">Velg</option>
									<option value="1">Mandag</option>
									<option value="2">Tirsdag</option>
									<option value="3">Onsdag</option>
									<option value="4">Torsdag</option>
									<option value="5">Fredag</option>
									<option value="6">Lørdag</option>
									<option value="7">Søndag</option>
									
							</select>
						</span>
						</div>
						<div class="col-md-1">
						<span style="">
							<label>
							Fra tid
							</label>
							<select name="from_time[]" id="from_time">
								<option value="">Velg</option>
								<option>00:00</option>
								<option>01:00</option>
								<option>02:00</option>
								<option>03:00</option>
								<option>04:00</option>
								<option>05:00</option>
								<option>06:00</option>
								<option>07:00</option>
								<option>08:00</option>
								<option>09:00</option>
								<option>10:00</option>
								<option>11:00</option>
								<option>12:00</option>
								<option>13:00</option>
								<option>14:00</option>
								<option>15:00</option>
								<option>16:00</option>
								<option>17:00</option>
								<option>18:00</option>
								<option>19:00</option>
								<option>20:00</option>
								<option>21:00</option>
								<option>22:00</option>
								<option>23:00</option>
							</select>
						</span>
						</div>
						<div class="col-md-2">
						<span style="">
							<label>
							Til dag
							</label>
							<select name="to_day[]" id="to_day">
								<option value="0">Velg</option>
									<option value="1">Mandag</option>
									<option value="2">Tirsdag</option>
									<option value="3">Onsdag</option>
									<option value="4">Torsdag</option>
									<option value="5">Fredag</option>
									<option value="6">Lørdag</option>
									<option value="7">Søndag</option>
									
							</select>
						</span>
						</div>
						<div class="col-md-1">
						<span style="">
							<label>
							Til tid
							</label>
							<select name="to_time[]" id="to_time">
								<option value="">Velg</option>
								<option>00:00</option>
								<option>01:00</option>
								<option>02:00</option>
								<option>03:00</option>
								<option>04:00</option>
								<option>05:00</option>
								<option>06:00</option>
								<option>07:00</option>
								<option>08:00</option>
								<option>09:00</option>
								<option>10:00</option>
								<option>11:00</option>
								<option>12:00</option>
								<option>13:00</option>
								<option>14:00</option>
								<option>15:00</option>
								<option>16:00</option>
								<option>17:00</option>
								<option>18:00</option>
								<option>19:00</option>
								<option>20:00</option>
								<option>21:00</option>
								<option>22:00</option>
								<option>23:00</option>
							</select>
						</span>
						</div>

						<div class="col-md-2">
						<span style="">
							<label>
							Pris for hele<i class="tip" data-tip-content="Prisen for å bestille alle/hele objektet eller alle tilgjengelige tidsluker "></i>
							</label>
							<div class="select-input disabled-first-option">
								<i class="data-unit"></i><input type="number" class="input-text" name="all_slot_price[]" id="all_slot_price" step="any" placeholder="" maxlength="" limitchar="" data-unit="">
							</div>
						</span>
						</div>
						<div class="col-md-2">
						<span style="">
							<label>
							Pris per stk 
							</label>
							<div class="select-input disabled-first-option">
								<i class="data-unit"></i><input type="number" class="input-text" name="slot_price[]" id="slot_price" step="any" placeholder="" maxlength="" limitchar="" data-unit="">
							</div>
						</span>
						</div>
						
						<div class="col-md-1">
						<span style="">
							<label>
							Antall <i class="tip" data-tip-content="Velg antall tidsluker som er tilgjengelig for valgt tid"></i>
							</label>
							<div class="select-input disabled-first-option">
								<i class="data-unit"></i><input type="number" class="input-text" name="slots[]" id="slots" step="any" placeholder=""  maxlength="" limitchar="" data-unit="">
							</div>
						</span>
						</div>
						<div class="col-md-1">
						<span class="close_div" style="display: none">
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
					<button type="button" style="margin-bottom: 25px" class="btn btn-primary addTimeSlot button">Legg til flere dager </button>
				</div>
			</div>
		</div>	
		
</div>
<div class="availability-slots" data-clock-type="<?php echo esc_attr($clock_format); ?>hr" <?php if($_booking_system_service == "on"){ echo "style='display:none'";}?>>
<?php $days = array(
'monday'	=> __('Monday','listeo_core'),
'tuesday' 	=> __('Tuesday','listeo_core'),
'wednesday' => __('Wednesday','listeo_core'),
'thursday' 	=> __('Thursday','listeo_core'),
'friday' 	=> __('Friday','listeo_core'),
'saturday' 	=> __('Saturday','listeo_core'),
'sunday' 	=> __('Sunday','listeo_core'),
); 



if ( isset( $data->field['value'] ) ) $field = json_decode( $data->field['value'] );

require("opening_slots.php");
$int = 0;
?>
<?php foreach ($days as $id => $dayname) { 
	?>
	<!-- Single Day Slots -->
	<div class="day-slots" style="display:none">
		<div class="day-slot-headline">
			<?php echo esc_html($dayname); ?>
		</div>


		<!-- Slot For Cloning / Do NOT Remove-->
		<div class="single-slot cloned">
			<div class="single-slot-left">
				<div class="col-md-6 single-slot-time "><?php echo esc_html($dayname); ?></div>
				<button class="remove-slot"><i class="fa fa-close"></i></button>
			</div>

			<div class="single-slot-right">
				<strong><?php echo esc_html('Slots','listeo_core'); ?></strong>
				<div class="plusminus horiz">
					<button></button>
					<input type="number" name="slot-qty" id="slot-qty" value="1" min="1" max="99">
					<button></button> 
				</div>
			</div>
		</div>		
		<!-- Slot For Cloning / Do NOT Remove-->

<?php if (!isset( $field[$int][0]) ) { ?>
		<!-- No slots -->
		<div class="no-slots"><?php esc_html_e('No slots added','listeo_core'); ?></div>
<?php } ?>
		<!-- Slots Container -->
		<div class="slots-container">


	<!-- Slots from database loop -->
	<?php if ( isset( $field ) && is_array( $field[$int] ) ) foreach ( $field[$int] as $slot ) { // slots loop
			$slot = explode( '|', $slot);?>	
				<div class="single-slot ui-sortable-handle">
					<div class="single-slot-left">
						<div class="col-md-6 single-slot-time"><?php echo esc_html($slot[0]); ?></div>
						<button class="remove-slot" style="float:right"><i class="fa fa-close"></i></button>
					</div>

					<div class="single-slot-right">
						<strong><?php echo esc_html('Slots','listeo_core'); ?></strong>
						<div class="plusminus horiz">
							<button></button>
							<input type="number" name="slot-qty" id="slot-qty" value="<?php echo esc_html($slot[1]); ?>" min="1" max="99">
							<button></button> 
						</div>
					</div>
				</div>
		<?php } ?>			
		<!-- Slots from database / End -->

		</div>
		<!-- Slots Container / End -->
		<!-- Add Slot -->
		<div class="add-slot">
			<div class="add-slot-inputs">
				<!-- <input type="time" class="time-slot-start" min="00:00" max="12:59"/> -->
<!--				<input type="text"  class="time-slot-start slot-time-input"  placeholder="--:--" maxlength="5" size="5" />-->
<!--				--><?php //if( $clock_format == '12'){ ?>
<!--				<select class="time-slot-start twelve-hr" id="">-->
<!--					<option>--><?php //esc_html_e('am','listeo_core'); ?><!--</option>-->
<!--					<option>--><?php //esc_html_e('pm','listeo_core'); ?><!--</option>-->
<!--				</select>-->
<!--				--><?php //} ?>
<!---->
<!--				<span>-</span>-->
<!---->
<!--				 <input type="time" class="time-slot-end" min="00:00" max="12:59"/> -->
<!--				<input type="text"  class="time-slot-end slot-time-input"  placeholder="--:--" maxlength="5" size="5" />-->
<!--				--><?php //if( $clock_format == '12'){ ?>
<!--				<select class="time-slot-end twelve-hr" id="">-->
<!--					<option>--><?php //esc_html_e('am','listeo_core'); ?><!--</option>-->
<!--					<option>--><?php //esc_html_e('pm','listeo_core'); ?><!--</option>-->
<!--				</select>-->
<!--				--><?php //} ?>
                <div class="col-md-6">
                    <input type="text" class="listeo-flatpickr time-slot-start slot-time-input" placeholder='--|--' value="">
                </div>
                <div class="col-md-6">
                    <input type="text" class="listeo-flatpickr time-slot-end slot-time-input" placeholder='--|--' value="">
                </div>

			</div>
			<div class="add-slot-btn">
				<button><?php esc_html_e('Add','listeo_core'); ?></button>
			</div>
		</div>
        <div class="availableHours" style="text-align: center;padding: 5px 0 0 0;">
            <input class="checkboxHours" type="checkbox" style="width: auto;height: auto;">
            <span> Tilgjengelig 24 timer</span>
        </div>
	</div>
<?php 
$int++;
} ?>
<input type="hidden" name="_slots" id="_slots" />
</div>
<style type="text/css">
	.single-slot.ui-sortable-handle{
		display: flex !important;
	}
	.single-slot-time {
	   width: 56%;
	}
	.plusminus.horiz input[type="number"] {
	    padding: 0 !important;
	}
	.single-slot {
	    padding-bottom: 6px;
	}
	/*.single-slot .single-slot-left{
		display: none !important;
	}*/
</style>
<script type="text/javascript">
/*jQuery(".plusminus").find("input").next().on("click",function(){
   var innttt = jQuery(this).prev().val();
   innttt = parseInt(innttt) + 1;
   jQuery(this).prev().val(innttt);
});
jQuery(".plusminus").find("input").prev().on("click",function(){
   var innttt = jQuery(this).next().val();
   innttt = parseInt(innttt) - 1;
   jQuery(this).next().val(innttt);
});*/

jQuery(".addTimeSlot").on("click",function(e){
	e.preventDefault();
	let clone_div = jQuery(".slots_div").find(".row").first().clone();
	jQuery(".slots_div").append(clone_div);
	jQuery(clone_div).find(".close_div").show();
})
jQuery(document).on("click",".delete_slot",function(e){
	jQuery(this).parent().parent().parent().remove();
})
jQuery(document).on("change",".slots_type",function(e){
	if(jQuery(this).val() == "standard"){
         jQuery(".standard_div").show();
		 jQuery(".advanced_div").hide();
	}else{
		jQuery(".standard_div").hide();
		jQuery(".advanced_div").show();

	}
})
jQuery(document).on("change","#enable_slot_price",function(e){
	if(this.checked == true){
          jQuery(".slot_price_div").show();
          jQuery(".price_label-fillter").show();
	}else{
           jQuery(".slot_price_div").hide();
           jQuery(".price_label-fillter").hide();
	}
})
jQuery("#enable_slot_price").change();
</script>