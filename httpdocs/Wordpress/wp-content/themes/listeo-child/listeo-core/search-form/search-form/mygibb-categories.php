<?php
	$catsArray = array(63,129,89,130,93,162,360);
	$topLvCategories = get_terms( array(
	          'taxonomy' => 'listing_category',
	          'include' => $catsArray,
	          'hide_empty'  => false, 
	          'orderby'  => 'include',
    ));
?>
<div style="width:auto;order:1;">
	<div class="panel-dropdown wide" id="<?php echo esc_attr( $data->name ); ?>">
		<?php printf('<a href="#" style="border:none;"><i class="fa fa-check-circle" style="font-family:font awesome pro!important;font-weight:100;line-height:25px;"></i>%s<span class="greenThenWhite" style="padding-left:3px;"></span></a>', 'Hva ser du etter? ' ); ?>

		<?php 

			if(isset($_GET[$data->name])) {
				$topCatChosenSelect = $_GET[$data->name];
			} else {
				$topCatChosenSelect = array();
			} 

			if(isset($data->taxonomy) && !empty($data->taxonomy)) {
				$data->options = listeo_core_get_options_array('taxonomy',$data->taxonomy);
				$groups = array_chunk($data->options, 4, true);
				if(is_tax($data->taxonomy))
					$topCatChosenSelect[get_query_var($data->taxonomy)] = 'on';
				
				if(!is_array($topCatChosenSelect)){

					$topCatChosenSelect_arr = array();
					$topCatChosenSelect_arr[$topCatChosenSelect] = 'on';
					$topCatChosenSelect = $topCatChosenSelect_arr;
				}
			}

		?>

		<div class="panel-dropdown-content checkboxes">

			<div style="display:flex;flex-direction:row;width:100%;margin-bottom:20px;height:30px;">
                <a href="#" style="all:unset;position:absolute;right:0;padding:20px;top:0px;cursor:pointer;border-radius:0px!important;"><i class="fa fa-times"></i></a>
                <p>Tjeneste: </p>
            </div>

			<div class="row <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?> ">
			<?php if(isset($data->dynamic) && $data->dynamic=='yes'){ ?>
				<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
			<?php } else {
			?>

			<div class="panel-checkboxes-container">
				<?php  $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
				?>
			
				<?php foreach ($topLvCategories as $value) {?>
					<!-- filter by category -->
					<div onclick="window.open(`${window.location.origin}/listing-category/<?php echo esc_html($value->slug); ?>/`,`_self`)" class="panel-checkbox-wrap category-<?php echo $value->term_id;?>">
						<input <?php if ( array_key_exists ($value->slug, $topCatChosenSelect)) { echo 'checked="checked"'; } ?> value="<?php echo esc_html($value->slug) ?>" type="checkbox" name="<?php /*echo $data->name.'['.esc_html($value->slug).']';*/ ?>">
						<label for="<?php /*echo esc_html($value->slug)*/ ?>-<?php /*echo esc_attr($data->name);*/ ?>"><?php echo esc_html($value->name) ?></label>	
					</div>
					
				<?php } ?>
					
			</div>
			<?php 
			}
			?>
			</div>
			<div class="brukFilterKnappWrapper"><a class="brukFilterKnapp button" onclick="brukFilter(this)">Bruk</a></div>
		</div>
	</div>
</div>

<div style="width:auto;order:1;">
	<div class="panel-dropdown wide" id="<?php echo esc_attr( $data->name ); ?>-panel">
		
		<?php if ( isset( $data->placeholder ) )  {
			printf('<a href="#" style="border:none;"><i class="fa fa-list" style="font-family:font awesome pro!important;font-weight:100;line-height:25px;"></i>%s<span class="counterForFilter" style="padding:0px 5px 0px 5px;"></span><span class="greenThenWhite"></span><i class="fa fa-times" onclick="clearFiltersFor(\'#' . esc_attr( $data->name ) . '-panel\')"></i></a>', $data->placeholder );
		} ?>
		<div class="panel-dropdown-content checkboxes">
			<div style="display:flex;flex-direction:row;width:100%;margin-bottom:20px;height:30px;">
                <a href="#" style="all:unset;position:absolute;right:0;padding:20px;top:0px;cursor:pointer;border-radius:0px!important;"><i class="fa fa-times"></i></a>
                <p><?php echo (isset($data->placeholder)) ? $data->placeholder : 'Kategori' ?></p>
            </div>
            <span id="backtrackSubcategory" style="display:none;margin-top:-20px;z-index:600;">
	            <span id="backtrackSubcategoryBtn" style="
				    padding-left: 28px;
				    padding-right: 28px;
				    width: 0px;
				    cursor: pointer;
				"><i class="fa fa-chevron-left" style="color: #008474;"></i></span>
				<span id="subCategoryParentName"></span>
			</span>
			<div class="row <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?> ">
			<?php if(isset($data->dynamic) && $data->dynamic=='yes'){ ?>
				<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
			<?php } else { ?>

			<?php

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
					<?php
					
					if(!is_array($selected)){

						$selected_arr = array();
						$selected_arr[$selected] = 'on';
						$selected = $selected_arr;
					}

					?>
				<?php }

				$arrOfDepths = array();
				$currentDepth = array();
				$parentCats = $topLvCategories;
				$firstLoop = true;

				while(count($currentDepth) > 0 || $firstLoop){
					$firstLoop = false;
					$currentDepth = array();

					foreach($parentCats as $topLvCategory){

						$subcats = get_terms( array(
							'taxonomy' => 'listing_category',
							'hide_empty'  => false, 
							'parent' => $topLvCategory->term_id
						));

						array_push($currentDepth, ...$subcats);
					}

					array_push($arrOfDepths, $currentDepth);

					$parentCats = $currentDepth;

				} ?>

				<div class="hidden" id="topLvCategories">

				<?php foreach ($topLvCategories as $value) {?>

					<div class="panel-checkbox-wrap category-<?php echo $value->term_id;?>">
						<input <?php if ( array_key_exists ($value->slug, $topCatChosenSelect)) { echo 'checked="checked"'; } ?> value="<?php echo esc_html($value->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value->slug).']'; ?>">
						<label for="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value->name) ?></label>	
					</div>
				<?php } ?>

				</div>

				<?php for ($x = 0; $x < count($arrOfDepths)-1; $x++) { ?>

					<?php 
					global $wpdb;
					$value = $arrOfDepths[$x][0];
					$ancestor = get_ancestors(get_term_by('slug', $value->slug, 'listing_category')->term_id, 'listing_category'); ?>

					<div class="cat-depth category-depth-<?php echo $x; ?>" style="margin-left:<?php echo ($x * 100); ?>%;">

						<div class="panel-checkbox-wrap alle">
							<input type="checkbox">
							<label>Alle</label>	
						</div>

					<?php for($i = 0; $i < count($arrOfDepths[$x]); $i++) {

						$value = $arrOfDepths[$x][$i]; 
						$hasTermChildren = count(get_term_children($value->term_id, 'listing_category')) > 0;

						?>

							<div id="cat-<?php echo $value->term_id; ?>" class="panel-checkbox-wrap parent-<?php echo $value->parent ?>">
								<input <?php if ( array_key_exists ($value->slug, $selected) && ($ancestor != null)) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value->slug).']'; ?>">
								<label style="flex:1;<?php if($hasTermChildren){ echo 'margin-right:45px;';  } ?>" for="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value->name) ?>
								<?php if($hasTermChildren) : ?>
									<span class="counterForSubFilter"></span>
									<span style="color:#008474;" class="subDisplayText"></span>
								<?php endif; ?>
								<?php if($x <= 0) : ?>
								<span style="float:right;">
									<?php 
										$thisCat = get_term_by('slug', $value->slug, 'listing_category');

										$res = $wpdb->get_results("SELECT COUNT(DISTINCT p.ID) as 'listings' FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id) INNER JOIN $wpdb->term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) INNER JOIN $wpdb->terms AS t ON (tt.term_taxonomy_id = t.term_id) LEFT JOIN $wpdb->term_taxonomy as ttParent ON (tt.parent = ttParent.term_taxonomy_id) WHERE p.post_type = 'listing' and (t.term_id = '$thisCat->term_id' or tt.parent = '$thisCat->term_id' or ttParent.parent = '$thisCat->term_id') and tt.taxonomy = 'listing_category' and p.post_status = 'publish' " );

										echo $res[0]->listings;

									?>
								</span> <!-- HERE -->
								<?php endif; ?>
								</label>
								<span <?php if($hasTermChildren) { echo 'class="showSubCategories" style="height:100%;position:absolute;right:0px;top:0px;width:calc(100% - 25px);z-index:100;text-align:right;cursor:pointer;"'; } else {
									echo " style=\"width:40px;text-align:right;\" ";
								} ?>>
									<?php if($hasTermChildren) : ?>
										<i class="fa fa-chevron-right" style="color:#008474!important;"></i>
									<?php endif; ?>
								</span>
							</div>

					<?php } ?>

					</div>

				<?php }
				
			 ?>

			</div>

			<?php 

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
			<div class="brukFilterKnappWrapper"><a class="brukFilterKnapp button" onclick="brukFilter(this)">Bruk</a></div>
		</div>
	</div>
</div>

