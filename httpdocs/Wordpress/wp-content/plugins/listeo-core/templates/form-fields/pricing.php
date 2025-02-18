<!-- Section -->
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$field = $data->field;
	$key = $data->key;
	
$currency_abbr = get_option( 'listeo_currency' );
$currency = Listeo_Core_Listing::get_currency_symbol($currency_abbr);


if(isset($field['value']) && is_array($field['value'])) :
$i=0;


?>
	
<div class="row">
	<div class="col-md-12">
		
			<table id="pricing-list-container">
				<?php foreach ($field['value'] as $m_key => $menu) { ?>
					<?php if(isset($menu['menu_title'])) { ?>
					<tr class="pricing-list-item pricing-submenu" data-number="<?php echo esc_attr($i); ?>">
						<td>
							<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>
							<div class="fm-input"><input type="text" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_title]" value="<?php echo $menu['menu_title']; ?>" placeholder="<?php esc_html_e('Category Title','listeo_core'); ?>"></div>
							<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
						</td>
					</tr>
					<?php } 
					$z = 0;
					if(isset($menu['menu_elements'])) {
						foreach ($menu['menu_elements'] as $el_key => $menu_el) { ?>
							<tr class="pricing-list-item <?php if( $z === 0) { echo 'pattern'; } ?>" data-iterator="<?php echo esc_attr($z); ?>">
								<td>
									<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>
									<div class="fm-input pricing-name">
										<input type="text" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][name]"  value="<?php echo $menu_el['name']; ?>" placeholder="<?php esc_html_e('Title','listeo_core'); ?>" />
									</div>
									<div class="fm-input pricing-ingredients">
										<input type="text" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][description]"  value="<?php echo $menu_el['description']; ?>" placeholder="<?php esc_html_e('Description','listeo_core'); ?>" />
									</div>
									<div class="fm-input pricing-price">
										<input type="number" step="0.01" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][price]" value="<?php echo $menu_el['price']; ?>" placeholder="<?php esc_html_e('Price (optional)','listeo_core'); ?>" data-unit="<?php echo esc_attr($currency) ?>" />
									</div>
									<div class="fm-input pricing-bookable" >
										<div class="switcher-tip" data-tip-content="<?php esc_html_e( 'Click to make this item bookable in booking widget','listeo_core' ); ?>"><input type="checkbox"  class="input-checkbox switch_1" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][bookable]" 
										<?php if(isset( $menu_el['bookable'] )) echo 'checked="checked"'; ?> /></div>
									</div>
									<div class="fm-input pricing-bookable-options">
									<!-- 	<select class="select2-single" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][bookable_options]" id="">
											<option <?php if(isset($menu_el['bookable_options'])) selected( $menu_el['bookable_options'], 'onetime'); ?> value="onetime"><?php esc_html_e('One time fee','listeo_core'); ?></option> -->
								<!-- 			<option <?php if(isset($menu_el['bookable_options'])) selected( $menu_el['bookable_options'], 'byguest'); ?>value="byguest"><?php esc_html_e('Multiply by guests','listeo_core'); ?></option>
											<option <?php if(isset($menu_el['bookable_options'])) selected( $menu_el['bookable_options'], 'bydays'); ?>value="bydays"><?php esc_html_e('Multiply by days','listeo_core'); ?></option>
											<option <?php if(isset($menu_el['bookable_options'])) selected( $menu_el['bookable_options'], 'byguestanddays'); ?> value="byguestanddays"><?php esc_html_e('Multiply by guests & days ','listeo_core'); ?></option> -->
										</select>
										<div class="checkboxes in-row pricing-quanity-buttons">
											<input type="checkbox"  class="input-checkbox" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][bookable_quantity]" id="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][bookable_quantity]" 
											<?php if(isset( $menu_el['bookable_quantity'] )) echo 'checked="checked"'; ?> />
											<label for="<?php echo esc_attr($key); ?>[<?php echo esc_attr($i); ?>][menu_elements][<?php echo esc_attr($z); ?>][bookable_quantity]"><?php esc_html_e('Vis velg antall knapp','listeo_core') ?></label>
										</div>
									</div>
									<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
								</td>
							</tr>
						<?php 
						$z++;
						} 
					} // menu
				$i++;
				} ?>
		</table>
		<a href="#" class="button add-pricing-list-item"><?php esc_html_e('Legg til tjeneste ','listeo_core'); ?></a>
		<a href="#" class="button add-pricing-submenu"><?php esc_html_e('Legg til tittel','listeo_core'); ?></a>
	</div>
</div>

<?php else : ?>
<div class="row">
	<div class="col-md-12">
		<table id="pricing-list-container">
		
			<tr class="pricing-list-item pattern" data-iterator="0">
				<td>
					<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>
					<div class="fm-input pricing-name"><input type="text" placeholder="<?php esc_html_e('Title','listeo_core'); ?>" name="_menu[0][menu_elements][0][name]"/></div>
					<div class="fm-input pricing-ingredients"><input type="text" placeholder="<?php esc_html_e('Description','listeo_core'); ?>" name="_menu[0][menu_elements][0][description]" /></div>
					<div class="fm-input pricing-price"><input type="number" step="0.01" name="_menu[0][menu_elements][0][price]" placeholder="<?php esc_html_e('Price (optional)','listeo_core'); ?>" data-unit="<?php echo esc_attr($currency) ?>" /></div><div class="fm-input pricing-bookable"><div class="switcher-tip" data-tip-content="<?php esc_html_e( 'Click to make this item bookable in booking widget','listeo_core' ); ?>"><input type="checkbox" class="input-checkbox switch_1" name="_menu[0][menu_elements][0][bookable]" /></div></div>
		<!-- 			<div class="fm-input pricing-bookable-options">
						<select name="<?php echo esc_attr($key); ?>[0][menu_elements][0][bookable_options]" id=""> -->
							<!-- <option value="onetime"><?php esc_html_e('One time fee','listeo_core'); ?></option> -->
				<!-- 			<option value="byguest"><?php esc_html_e('Multiply by guests','listeo_core'); ?></option>
							<option value="bydays"><?php esc_html_e('Multiply by days','listeo_core'); ?></option>
							<option value="byguestanddays"><?php esc_html_e('Multiply by guests & days ','listeo_core'); ?></option> -->
						</select>
						<div class="checkboxes in-row pricing-quanity-buttons">
							<input type="checkbox"  class="input-checkbox" name="_menu[0][menu_elements][0][bookable_quantity]" id="_menu[0][menu_elements][0][bookable_quantity]" />
							<label for="_menu[0][menu_elements][0][bookable_quantity]"><?php esc_html_e('Vis velg antall knapp','listeo_core') ?></label>
						</div>
					</div>
					<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
				</td>
			</tr>
		</table>
		<a href="#" class="button add-pricing-list-item"><?php esc_html_e('Legg til tjeneste ','listeo_core'); ?></a>
		<a href="#" class="button add-pricing-submenu"><?php esc_html_e('Legg til tittel','listeo_core'); ?></a>
	</div>
</div>
<?php endif; ?>