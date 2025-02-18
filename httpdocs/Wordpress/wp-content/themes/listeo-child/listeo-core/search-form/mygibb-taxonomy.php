<?php
	$topLvCategories_data = get_terms( array(
	          'taxonomy' => $data->taxonomy,
	          'hide_empty'  => false, 
    ));
    $topLvCategories = array();
    foreach ($topLvCategories_data as $key => $dd) {
    	if($dd->parent == 0){
           $topLvCategories[] = $dd;
    	}
    }


?>
<div style="width:auto;order:1;" id="<?php echo esc_attr( $data->name ); ?>_main">
	<div class="panel-dropdown wide" id="<?php echo esc_attr( $data->name ); ?>">
		<?php printf('<a href="#" style="border:none;"><i class="fa fa-check-circle" style="font-family:font awesome pro!important;font-weight:100;line-height:25px;"></i>%s<span class="greenThenWhite" style="padding-left:3px;"></span></a>', $data->placeholder); ?>

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
					<div onclick="window.open(`${window.location.origin}/<?php echo str_replace("_", "-", $data->taxonomy);?>/<?php echo esc_html($value->slug); ?>/`,`_self`)" class="panel-checkbox-wrap category-<?php echo $value->term_id;?>">
						<input <?php if ( array_key_exists ($value->slug, $topCatChosenSelect)) { echo 'checked="checked"'; } ?> value="<?php echo esc_html($value->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value->slug).']'; ?>">
						<label for="<?php echo $data->name.'['.esc_html($value->slug).']'; ?>"><?php echo esc_html($value->name) ?></label>	
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

	<!-- dfdf -->

	<?php
	if(get_query_var($data->taxonomy) != ""){ 

		$st_placeholder  = ucfirst(get_query_var($data->taxonomy));

		?>
          <div class="panel-dropdown wide" id="<?php echo esc_attr( $data->name ); ?>-child">
		<?php printf('<a href="#" style="border:none;"><i class="fa fa-check-circle" style="font-family:font awesome pro!important;font-weight:100;line-height:25px;"></i>%s<span class="greenThenWhite" style="padding-left:3px;"></span></a>', $st_placeholder." sub category"); ?>

		<?php 

		    $term = get_term_by('slug', get_query_var($data->taxonomy), $data->taxonomy);

		    

		    $subLvCategories_data = get_terms( array(
			          'taxonomy' => $data->taxonomy,
			          'hide_empty'  => false,
			          'parent' =>$term->term_id,
		    ));

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
			
				<div class="checkboxes one-in-row <?php if(isset($data->class)) { echo esc_attr($data->class); }?>" id="<?php echo esc_attr($data->name); ?>-child-panel">

					<?php if(isset($data->dynamic) && $data->dynamic=='yes'){ ?>
						<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
					<?php } else {


						if(isset($_GET[$data->name])) {
							$selected = array($_GET[$data->name]);
						} else {
							$selected = array();
						} 
						

						if(isset($data->taxonomy) && !empty($data->taxonomy)) {
							$data->options = listeo_core_get_options_array('taxonomy',$data->taxonomy);
							if(is_tax($data->taxonomy)){
								$selected[get_query_var($data->taxonomy)] = 'on';
							}	
							foreach ($subLvCategories_data as $key => $value) { ?>
                                <span class="main_span">
									<input <?php if ( array_key_exists ($value->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value->slug).']'; ?>" data-child="child-<?php echo $value->slug;?>">
									<label for="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value->name) ?></label>
								</span>

								<?php
									$subLvCategories_data1 = get_terms( array(
									          'taxonomy' => $data->taxonomy,
									          'hide_empty'  => false,
									          'parent' =>$value->term_id,
								    ));

								    foreach ($subLvCategories_data1 as $key => $value1) { ?>
							            <span class="child_span1 child-<?php echo $value->slug;?>" style="display:none">
								            <input <?php if ( array_key_exists ($value1->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value1->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value1->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value1->slug).']'; ?>" data-child="child-<?php echo $value1->slug;?>">
									        <label for="<?php echo esc_html($value1->slug) ?>-<?php echo esc_attr($data->name); ?>" style="margin-left: 20px;"><?php echo esc_html($value1->name) ?></label>
									    </spa>

								        <?php
											$subLvCategories_data2 = get_terms( array(
											          'taxonomy' => $data->taxonomy,
											          'hide_empty'  => false,
											          'parent' =>$value1->term_id,
										    ));

										    foreach ($subLvCategories_data2 as $key => $value2) { ?>
									            <span class="child_span2 child-<?php echo $value1->slug;?>" style="display:none">
										            <input <?php if ( array_key_exists ($value2->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value2->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value2->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value2->slug).']'; ?>">
											        <label for="<?php echo esc_html($value2->slug) ?>-<?php echo esc_attr($data->name); ?>" style="margin-left: 40px;"><?php echo esc_html($value2->name) ?></label>
											    </span>

									
								        <?php } ?>

							
						        <?php } ?>


							
						<?php } 
						}

					}
					?>


					</div>
					
			</div>
			<?php 
			}
			?>
			</div>
			<div class="brukFilterKnappWrapper"><a class="brukFilterKnapp button" onclick="brukFilter(this)">Bruk</a></div>
		</div>
	</div>

		

	<?php } ?>


	<!-- dssdend -->
</div>

<script type="text/javascript">

	jQuery("#<?php echo $data->name;?>").find(".greenThenWhite").html(jQuery("#<?php echo $data->name;?>").find("input:checked").val());
	
    jQuery("#<?php echo $data->name;?>-child").find("input").change(function(){
    	var htmll = "";
    	var le_ch = jQuery("#<?php echo $data->name;?>-child").find("input:checked").length;
    	if(le_ch > 0){
    		htmll += "("+le_ch+") "; 
    	}

    	jQuery("#<?php echo $data->name;?>-child").find("input:checked").each(function(){
             htmll += jQuery(this).val()+", ";
    	});

    	jQuery("#<?php echo $data->name;?>-child").find(".greenThenWhite").html(htmll);
    	
    })
</script>

<script type="text/javascript">
  jQuery(document).on("click","#<?php echo esc_attr($data->name); ?>-child-panel input",function(){
  	 //jQuery("#<?php echo esc_attr($data->name); ?>").find("input").not(this).prop("checked",false);

  	 var dd_child = jQuery(this).data("child");
  	 if(jQuery(this).prop("checked") == true){
         
          jQuery("."+dd_child).show();

  	 }else{
          jQuery("."+dd_child).hide();
          jQuery("."+dd_child).find("input").prop("checked",false);
  	 }
  
  });
</script>


