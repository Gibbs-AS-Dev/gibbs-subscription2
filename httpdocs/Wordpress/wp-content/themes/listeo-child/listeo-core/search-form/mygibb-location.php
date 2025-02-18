<div style="width:auto;order:3;">
	<div class="panel-dropdown wide" id="<?php echo esc_attr( $data->name ); ?>-panel">
		
		<?php if ( isset( $data->placeholder ) )  {
			printf('<a href="#" style="border:none;">%s<span class="counterForFilter" style="padding:0px 5px 0px 5px;"></span><span class="greenThenWhite"></span><i class="fa fa-times" onclick="clearFiltersFor(\'#' . esc_attr( $data->name ) . '-panel\')"></i></a>', $data->placeholder );
		} ?>
		<div class="panel-dropdown-content checkboxes">
			<div style="display:flex;flex-direction:row;width:100%;margin-bottom:20px;height:30px;">
                <a href="#" style="all:unset;position:absolute;right:0;padding:20px;top:0px;cursor:pointer;border-radius:0px!important;"><i class="fa fa-times"></i></a>
                <p><?php echo (isset($data->placeholder)) ? $data->placeholder : 'Område' ?></p>
            </div>
            <span id="backtrackSubregion" style="display:none;margin-top:-20px;z-index:600;">
	            <span id="backtrackSubregionBtn" style="
				    padding-left: 28px;
				    padding-right: 28px;
				    width: 0px;
				    cursor: pointer;
				"><i class="fa fa-chevron-left" style="color: #008474;"></i></span>
				<span id="subRegionParentName"></span>
			</span>
			<div class="row">
			<?php if(isset($data->dynamic) && $data->dynamic=='yes'){ ?>
				<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
			<?php } else {


			if(isset($_GET[$data->name])) {
				$selected = $_GET[$data->name];
			} else {
				$selected = array();
			} 

			if(isset($data->taxonomy) && !empty($data->taxonomy)) {
				$data->options = listeo_core_get_options_array('taxonomy',$data->taxonomy);
				$groups = array_chunk($data->options, 4, true);
				if(is_tax($data->taxonomy)){
					$selected[get_query_var($data->taxonomy)] = 'on';
				}	
				?>
				<div class="panel-checkboxes-container">
				<?php
				
				if(!is_array($selected)){
					$selected_arr = array();
					$selected_arr[$selected] = 'on';
					$selected = $selected_arr;
				}

				$fylker = get_terms('region',array(
					'hide_empty' 	=> 0,
				    'parent' 		=> 0,
				));
				$kommuner = array(); ?>

				<div class="fylkeWrapper" style="width:100%;">

					<div class="panel-checkbox-wrap alle">
						<input type="checkbox">
						<label>Alle</label>	
					</div>

				<?php foreach ($fylker as $value) { 

					$fylkenesKommuner = get_terms('region', array(
						'hide_empty' 	=> 0,
					    'parent' 		=> $value->term_id,
					));

					$kommuner[$value->term_id] = array();
					foreach($fylkenesKommuner as $subReg)
						array_push($kommuner[$value->term_id], $subReg);

					?>

					<div class="panel-checkbox-wrap fylke" style="display:flex;">
						<input class="f-<?php echo $value->term_id;?>" <?php if ( array_key_exists ($value->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value->slug).']'; ?>">
						<label style="flex:1;<?php if(count($fylkenesKommuner) > 0){ echo 'margin-right:45px;';  } ?>" class="<?php if(count($fylkenesKommuner) > 0){ echo ' hasChildren '; } ?>" for="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value->name) ?></label>
						<span <?php if(count($fylkenesKommuner) > 0) { echo 'class="showSubRegions" style="position:absolute;right:0px;top:0px;width:calc(100% - 25px);z-index:100;text-align:right;cursor:pointer;" '; } ?> style="width:40px;text-align:right;">
							<?php if(count($fylkenesKommuner) > 0) : ?>
								<i class="fa fa-chevron-right" style="color:#008474!important;font-size:15px!important;"></i>
							<?php endif; ?>
						</span>
					</div>
					
			<?php } ?>

				</div>

				<div class="kommuneWrapper" style="width:100%;margin-left:100%;transition:300ms;">

					<div class="panel-checkbox-wrap alle">
						<input type="checkbox">
						<label>Alle</label>	
					</div>

				<?php foreach ($kommuner as $fylke) { 

					foreach($fylke as $kommune){ ?>

						<div class="panel-checkbox-wrap kommune fylke-<?php echo $kommune->parent; ?>">
							<input <?php if ( array_key_exists ($kommune->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($kommune->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($kommune->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($kommune->slug).']'; ?>">
							<label for="<?php echo esc_html($kommune->slug) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($kommune->name) ?></label>	
						</div>

					<?php }

				} ?>

				</div>

				</div>
			<?php }
			if(isset($data->options_source) && empty($data->taxonomy) ) {
				if(isset($data->options_cb) && !empty($data->options_cb) ){
					switch ($data->options_cb) {
						case 'listeo_core_get_offer_types':
							$data->options = listeo_core_get_offer_types_flat(false);
							break;

						case 'listeo_core_get_listing_types':
							$data->options = listeo_core_get_listing_types();
							break;

						case 'listeo_core_get_rental_period':
							$data->options = listeo_core_get_rental_period();
							break;
					
						default:
							# code...
							break;
					}	
				}

				if($data->options_source == 'custom') {
					$data->options = array_flip($data->options);
				}
				foreach ($data->options as $key => $value) { ?>
					<input <?php if ( array_key_exists ($key, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($key) ?>-<?php echo esc_attr($data->name); ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($key).']'; ?>">
					<label for="<?php echo esc_html($key) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value) ?></label>
				
			<?php } 
			}
			}
			?>
			</div>
			<div class="brukFilterKnappWrapper"><a class="brukFilterKnapp button" onclick="brukFilter(this)">Bruk</a></div>
		</div>
	</div>
</div>