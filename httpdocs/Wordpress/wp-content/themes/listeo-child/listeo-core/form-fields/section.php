<!-- Section -->
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$field = $data->field;
	$key = $data->key;

if(isset($field['value']) && is_array($field['value'])) :
$i=0;


?>
	
<div class="row">
<div class="col-md-12">
		<table id="section-list-container">		
			<tr class="section-list-item pattern" data-iterator="0">
				<td>
					<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>
					<div>
						<label for ="name">Name</label>
						<div class="fm-input pricing-name"><input type="text" placeholder="<?php esc_html_e('Title','listeo_core'); ?>" name="_menu[0][menu_elements][0][name]"/></div>
					</div>
					<div>
						<label for ="sports">Sports</label>
						<div class="fm-input pricing-bookable-options">
							<select name="<?php echo esc_attr($key); ?>[0][menu_elements][0][bookable_options]" id="">
								<option value="1"><?php esc_html_e('Football','listeo_core'); ?></option>
								<option value="2"><?php esc_html_e('basketball','listeo_core'); ?></option>
							</select>
						</div>
					</div>
					<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
				</td>
				<td>
					<h3>Working hours</h3>
				</td>
			</tr>
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
						<label for ="name">Name</label>
						<div class="fm-input pricing-name"><input type="text" placeholder="<?php esc_html_e('Title','listeo_core'); ?>" name="_menu[0][menu_elements][0][name]"/></div>
					</div>
					<div>
						<label for ="sports">Sports</label>
						<div class="fm-input pricing-bookable-options">
							<select name="<?php echo esc_attr($key); ?>[0][menu_elements][0][bookable_options]" id="">
								<option value="1"><?php esc_html_e('Football','listeo_core'); ?></option>
								<option value="2"><?php esc_html_e('basketball','listeo_core'); ?></option>
							</select>
						</div>
					</div>
					<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
				</td>
				<td>
					<h3>Working hours</h3>
				</td>
			</tr>
		</table>
		<a href="#" class="button add-section-list-item"><?php esc_html_e('Add Item','listeo_core'); ?></a>
	</div>
</div>
<?php endif; ?>
