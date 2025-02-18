<!-- Set data-clock-type="24hr" to enable 24 hours clock type -->
<?php $clock_format = get_option('listeo_clock_format','12') ?>
<div class="availability-slots" data-clock-type="<?php echo esc_attr($clock_format); ?>hr">
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
$int = 0;
?>

<?php foreach ($days as $id => $dayname) { 
	?>

	<!-- Single Day Slots -->
	<div class="day-slots">
		<div class="day-slot-headline">
			<?php echo esc_html($dayname); ?>
		</div>


		<!-- Slot For Cloning / Do NOT Remove-->
		<div class="single-slot cloned">
			<div class="single-slot-left">
				<div class="single-slot-time"><?php echo esc_html($dayname); ?></div>
				<button class="remove-slot"><i class="fa fa-close"></i></button>
			</div>

			<div class="single-slot-right">
				<strong><?php  esc_html_e('Slots','listeo_core'); ?></strong>
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
						<div class="single-slot-time"><?php echo esc_html($slot[0]); ?></div>
						<button class="remove-slot"><i class="fa fa-close"></i></button>
					</div>

					<div class="single-slot-right">
						<strong><?php echo esc_html_e('Slots','listeo_core'); ?></strong>
						<div class="plusminus horiz">
							<button disabled=""></button>
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
				<input type="text"  class="time-slot-start slot-time-input"  placeholder="--:--" maxlength="5" size="5" />
				<?php if( $clock_format == '12'){ ?>
				<select class="time-slot-start twelve-hr" id="">
					<option><?php esc_html_e('am','listeo_core'); ?></option>
					<option><?php esc_html_e('pm','listeo_core'); ?></option>
				</select>
				<?php } ?>

				<span>-</span>

				<!-- <input type="time" class="time-slot-end" min="00:00" max="12:59"/> -->
				<input type="text"  class="time-slot-end slot-time-input"  placeholder="--:--" maxlength="5" size="5" />
				<?php if( $clock_format == '12'){ ?>
				<select class="time-slot-end twelve-hr" id="">
					<option><?php esc_html_e('am','listeo_core'); ?></option>
					<option><?php esc_html_e('pm','listeo_core'); ?></option>
				</select>
				<?php } ?>

			</div>
			<div class="add-slot-btn">
				<button><?php esc_html_e('Add','listeo_core'); ?></button>
			</div>
		</div>
	</div>
<?php 
$int++;
} ?>
<input type="hidden" name="_slots" id="_slots" />
</div>