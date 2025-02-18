<!-- Section -->
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$field = $data->field;
	$key = $data->key;
	$days = listeo_get_days();


if(isset($field['value']) && is_array($field['value'])) : $i=0;?>
	
<div class="row">
	<div class="col-md-12">
		<table id="section-list-container">
            <?php foreach ($field['value'] as $m_key => $menu) { ?>
                <?php if(isset($menu['menu_title'])) { ?>
					<tr class="section-list-item section-submenu" data-number="<?php echo esc_attr($i); ?>">
						<td>
							<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>
							<div class="fm-input"><input type="text" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_title]" value="<?php echo $menu['menu_title']; ?>" placeholder="<?php esc_html_e('Category Title','listeo_core'); ?>"></div>
							<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
						</td>
					</tr>
					<?php } 
				$z = 0;
                if(isset($menu['menu_section_elements'])) {
				    foreach ($menu['menu_section_elements'] as $el_key => $menu_el) { ?>
                        <tr class="section-list-item <?php if( $z === 0) { echo 'pattern'; } ?>" data-iterator="<?php echo esc_attr($z); ?>">
                            <td>
                                <div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>
                                <div>
                                    <label for ="_section_name">Name</label>
                                    <input type="text" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_section_elements][<?php echo esc_attr($z); ?>][section_name]"  value="<?php echo $menu_el['section_name']; ?>" placeholder="<?php esc_html_e('Title','listeo_core'); ?>" />
                                </div>
                                <div style="flex:1;">
                                    <label style="margin-left:20px" for ="sport">Sport</label>
                                    <div class="fm-input section-sport-options">
                                        <select class = "chosen-select" name="<?php echo esc_attr($key); ?>[0][menu_section_elements][<?php echo esc_attr($z); ?>][sports]" multiple="" id="">
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '1'); ?> value="1"><?php esc_html_e('Håndball','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '2'); ?> value="2"><?php esc_html_e('Football','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '3'); ?> value="3"><?php esc_html_e('basketball','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '4'); ?> value="4"><?php esc_html_e('Volleyball','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '8'); ?> value="8"><?php esc_html_e('Turn','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '9'); ?> value="9"><?php esc_html_e('Friidrett','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '13'); ?> value="13"><?php esc_html_e('Innebandy','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '14'); ?> value="14"><?php esc_html_e('Kampsport','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '16'); ?> value="16"><?php esc_html_e('Friluft','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '17'); ?> value="17"><?php esc_html_e('Hockey','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '18'); ?> value="18"><?php esc_html_e('Ski','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '19'); ?> value="19"><?php esc_html_e('Sykling','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '20'); ?> value="20"><?php esc_html_e('Allidrett','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '21'); ?> value="21"><?php esc_html_e('badminton','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '22'); ?> value="22"><?php esc_html_e('Klatrevegg','listeo_core'); ?></option>
                                            <option  <?php if(isset($menu_el['sports'])) selected( $menu_el['sports'], '23'); ?> value="23"><?php esc_html_e('Stengt','listeo_core'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
                            </td>
                            <td style="padding-left:40px;display:flex;flex-direction:column;">
					<div>
						<h4> Working hours </h4>
                        <div class="checkboxes in-row hours-buttons">
                            <label for="<?php echo esc_attr($key); ?>[0][menu_section_elements][<?php echo esc_attr($z); ?>][same_hrs]">Same Working hours</label>
						    <input type="checkbox"  class="input-checkbox" name="<?php echo esc_attr($key); ?>[0][menu_section_elements][<?php echo esc_attr($z); ?>][same_hrs]" id="<?php echo esc_attr($key); ?>[0][menu_section_elements][<?php echo esc_attr($z); ?>]" <?php if(isset( $menu_el['same_hrs'] )) echo 'checked="checked"'; ?> />
					    </div>
					</div>
					<div>
						<?php

 foreach ($days as $id => $dayname) { 
 		$count = 0;

 		?>
		<div class="row opening-day">
			<div class="row">
			<div class="col-md-2">
				<h5><?php echo esc_html($dayname) ?></h5>
				<span class='day_hours_reset'><?php esc_html_e('Clear Time','listeo_core') ?></span>
			</div>
					<div class="col-md-5">
						<input type="text" class="listeo-flatpickr" name="<?php echo esc_attr($key); ?>[0][menu_section_elements][<?php echo esc_attr($z); ?>][_section_<?php echo esc_attr($id); ?>_opening_hour]" placeholder="<?php esc_html_e('Opening Time','listeo_core'); ?>" value="<?php echo esc_attr($menu_el[_section_.$id._opening_hour]);?>" >
					</div>
					<div class="col-md-5">
						<input type="text" class="listeo-flatpickr" name="<?php echo esc_attr($key); ?>[0][menu_section_elements][<?php echo esc_attr($z); ?>][_section_<?php echo esc_attr($id); ?>_closing_hour]" placeholder="<?php esc_html_e('Closing Time','listeo_core'); ?>" value="<?php echo esc_attr($menu_el[_section_.$id._closing_hour]);?>" >
                        
					</div>	
				</div>	
			
		</div>
<?php } ?>
					</div>
				</td>
                        </tr>
                    <?php 
					$z++;
                    } 
				}
				$i++;
            } ?>
		</table>
		<a href="#" class="button add-section-list-item"><?php esc_html_e('Add Item','listeo_core'); ?></a>
	</div>
</div>

<?php else : ?>
<div class="row">
	<div class="col-md-12">
		<table id="section-list-container">		
			<tr class="section-list-item pattern" data-iterator="0">
				<td>
					<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>
					<div>
						<label for ="_section_name">Name</label>
						<div class="fm-input section-name"><input type="text" placeholder="<?php esc_html_e('Title','listeo_core'); ?>" name="_menu_section[0][menu_section_elements][0][section_name]"/></div>
					</div>
					<div style="flex:1;">
						<label style="margin-left:20px" for ="sport">Sport</label>
						<div class="fm-input section-sport-options">
							<select class = "chosen-select" name="<?php echo esc_attr($key); ?>[0][menu_section_elements][0][sports]" multiple="" id = "">
								<option value="1"><?php esc_html_e('Håndball','listeo_core'); ?></option>
								<option value="2"><?php esc_html_e('Football','listeo_core'); ?></option>
								<option value="3"><?php esc_html_e('basketball','listeo_core'); ?></option>
								<option value="4"><?php esc_html_e('Volleyball','listeo_core'); ?></option>
								<option value="8"><?php esc_html_e('Turn','listeo_core'); ?></option>
								<option value="9"><?php esc_html_e('Friidrett','listeo_core'); ?></option>
								<option value="13"><?php esc_html_e('Innebandy','listeo_core'); ?></option>
								<option value="14"><?php esc_html_e('Kampsport','listeo_core'); ?></option>
								<option value="16"><?php esc_html_e('Friluft','listeo_core'); ?></option>
								<option value="17"><?php esc_html_e('Hockey','listeo_core'); ?></option>
								<option value="18"><?php esc_html_e('Ski','listeo_core'); ?></option>
								<option value="19"><?php esc_html_e('Sykling','listeo_core'); ?></option>
								<option value="20"><?php esc_html_e('Allidrett','listeo_core'); ?></option>
								<option value="21"><?php esc_html_e('badminton','listeo_core'); ?></option>
								<option value="22"><?php esc_html_e('Klatrevegg','listeo_core'); ?></option>
								<option value="23"><?php esc_html_e('Stengt','listeo_core'); ?></option>
							</select>
						</div>
					</div>
					<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
				</td>
				<td style="padding-left:40px;display:flex;flex-direction:column;">
					<div>
						<h4> Working hours</h4>
					</div>
                    <div class="checkboxes in-row hours-buttons">
                        <label for="_menu_section[0][menu_section_elements][0][same_hrs]">Same Working hours</label>
						<input type="checkbox"  class="input-checkbox" name="_menu_section[0][menu_section_elements][0][same_hrs]" id="_menu_section[0][menu_elements_elements][0][same_hrs]" />
					</div>
					<div>
						<?php

 foreach ($days as $id => $dayname) { 
 		$count = 0;

 		?>
		<div class="row opening-day" name="_menu_section[0][menu_section_elements][0][working_hours]">
			<div class="row">
			<div class="col-md-2">
				<h5><?php echo esc_html($dayname) ?></h5>
				<span class='day_hours_reset'><?php esc_html_e('Clear Time','listeo_core') ?></span>
			</div>
					<div class="col-md-5">
						<input type="text" class="listeo-flatpickr" name="<?php echo esc_attr($key); ?>[0][menu_section_elements][0][_section_<?php echo esc_attr($id); ?>_opening_hour]" placeholder="<?php esc_html_e('Opening Time','listeo_core'); ?>" value = "">
							
					</div>
					<div class="col-md-5">
						<input type="text" class="listeo-flatpickr" name="<?php echo esc_attr($key); ?>[0][menu_section_elements][0][_section_<?php echo esc_attr($id); ?>_closing_hour]" placeholder="<?php esc_html_e('Closing Time','listeo_core'); ?>" value = "">
						
					</div>	
				</div>	
			
		</div>
<?php } ?>
					</div>
				</td>
			</tr>
		</table>
		<a href="#" data-count="<?php echo $count; ?>" data-id="<?php echo $id; ?>" data-days="<?php $days; ?>" class="button add-section-list-item"><?php esc_html_e('Add Item','listeo_core'); ?></a>
	</div>
</div>
<?php endif; ?>
