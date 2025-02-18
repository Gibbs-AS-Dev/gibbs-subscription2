<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

}

$field = $data->field;

$key = $data->key;

$multi = false;

if(isset($field['multi']) && $field['multi']) {

	$multi = true;

}

$selected = '';

// Get selected value

if ( isset( $field['value'] ) ) {

	$selected = $field['value'];

} elseif ( isset( $field['default']) && is_int( $field['default'] ) ) {

	$selected = $field['default'];

} elseif ( ! empty( $field['default'] ) && ( $term = get_term_by( 'slug', $field['default'], $field['taxonomy'] ) ) ) {

	$selected = $term->term_id;

}
$all_selected_categores = array();

$list_id = "";

$listing_top_category = '';
$listing_second_to_last_category = '';
if(isset($_POST['listing_top_category']) && $_POST['listing_top_category'] != null){
	$listing_top_category = $_POST['listing_top_category'];
} else if(isset($_GET['listing_id']) && $_GET['listing_id'] != null){
	$listingID = $_GET['listing_id'];
    $list_id = $_GET['listing_id'];

	$listing_top_categories = wp_get_object_terms($listingID, $field['taxonomy']);

    
	

	$listing_top_category = "";

	foreach ($listing_top_categories as $key => $listing_top_cat) {
		$all_selected_categores[] = $listing_top_cat->term_id;
		if($listing_top_cat->parent == 0){
			$listing_top_category = $listing_top_cat->term_id;
		}
	}


}

$multi_select = get_term_meta( $listing_top_category, 'mutiselect_category', true );


if($multi_select == "on"){
	$multi_select = 1;
}else{
	$multi_select = 0;
}


// Select only supports 1 value

if ( is_array( $selected ) && $multi == false ) {

	$selected = current( $selected );

}


$taxonomy = get_taxonomy($field['taxonomy']);

if ($field['taxonomy'] == 'listing_category' || $field['taxonomy'] == 'service_category' || $field['taxonomy'] == 'rental_category' || $field['taxonomy'] == 'event_category') {

	$nivaa = 2;

	if(get_term_by('id', $listing_top_category, $field['taxonomy'])->slug == "utstyr")
		$nivaa = 3;	

	$firstLvMap = array();

	$parentCat = 0;

	if($listing_top_category != null && $listing_top_category != ''){

		$parentCat = $listing_top_category;

		$firstLv = get_terms($field['taxonomy'],array(
			'hide_empty' 	=> 0,
		    'parent' 		=> $parentCat,
		));

		if(empty($firstLv)){
		?>
		<style type="text/css">
		.form-field-tax-service_category-container,.form-field-tax-rental_category-container,.form-field-tax-event_category-container{
			display: none;
		}
		</style>
		<?php	

		}



		/*echo "<select multiple ";
		echo ($nivaa==3) ? "onchange=changedFirstSelect(this) " : "onchange=showLastCat(this) ";
		echo "id=\"firstLv\"><option value=\"-1\">Velg Kategori</option>";

		foreach($firstLv as $category){

			$term_children = get_term_children($category->term_id, $field['taxonomy']);

			echo "<option value=\"".$category->term_id."\"";
			if(!empty($term_children) && !is_wp_error($term_children)){
				echo " class=\"hasChildCategories\" ";	
			}
			echo ">". $category->name ."</option>";
			$childrenOfFirstLv = get_terms($field['taxonomy'], array(
				'hide_empty' 	=> 0,
			    'parent' 		=> $category->term_id,
			));
			$firstLvMap[$category->term_id] = array();
			foreach($childrenOfFirstLv as $subCat){
				array_push($firstLvMap[$category->term_id], $subCat->term_id);
			}
		}

		echo "</select>";*/

		//echo "<pre>"; print_r($field); die;

		//echo "<div id=lastCatWrapper><label>Subkategori</label>";
		wp_dropdown_categories( apply_filters('listeo_core_term_select_field_wp_dropdown_categories_args', array(
				'taxonomy'         	=> $field['taxonomy'],
				'hierarchical'     	=> 1,
				'child_of'			=> $listing_top_category,
				'parent'			=> $listing_top_category,
				'multiple'   	   	=> $multi_select,
				'required'   	   	=> $field["required"],
				'show_option_all'  	=> false,
				'show_option_none' 	=>  __('Choose  category','listeo_core'),
				'option_none_value' => '',
				'name'             	=> (isset( $field['name'] ) ? $field['name'] : $key),
				'orderby'          	=> 'name',
				'selected'         	=> $all_selected_categores,
				'class'			   	=> 'chosen-select-no-single',
				'id'			   	=> 'list_category',
				'hide_empty'       	=> false,
				'walker'  			=> new Willy_Walker_CategoryDropdown()
			), $key, $field ));
		echo "<div id=subcat>";
		echo "</div>"; 

		$secondLvHasLoadedBefore = false;

		?>

		<script>

			var selectedFeaturesOnLoad = [];

			<?php if(isset($_GET['listing_id']) && $_GET['listing_id'] != null) {
			
				$featuresOfListing = wp_get_object_terms( $_GET['listing_id'], 'listing_feature', array( 'fields' => 'ids' ));

				foreach($featuresOfListing as $feature){ ?>

					selectedFeaturesOnLoad.push(<?php echo $feature; ?>);

				<?php } ?>


			<?php } ?>

			function catidsFeature(){
				
				var checked_ids = [];

				jQuery('.listeo_core-term-checklist-listing_feature').find("input:checked").each(function(){
                    checked_ids.push(jQuery(this).val());
				})
				var cat_ids = [];

				cat_ids.push(jQuery("input[name=listing_top_category]").val()); 
				
				jQuery("#list_category").find("option:selected").each(function(){
                    cat_ids.push(jQuery(this).val()); 
				});
				jQuery("#subcategories").find("option:selected").each(function(){
                    cat_ids.push(jQuery(this).val()); 
				});
				jQuery("#subcategories2").find("option:selected").each(function(){
                    cat_ids.push(jQuery(this).val()); 
				});
				
				if(jQuery('.listeo_core-term-checklist-listing_feature').hasClass("required")){
					var required = "required";
				}else{
					var required = "";
				}
			    jQuery.ajax({
		          type: 'POST', 
		          dataType: 'json',
		          url: listeo.ajaxurl,
		          data: { 
		              'action': 'get_features_ids_from_category_ids', 
		              'cat_ids' : cat_ids,
		              'taxonomy' :"<?php echo $field['taxonomy'];?>",
		              'panel': false,
		              'required':required,
		              'list_id' : "<?php echo $list_id;?>",
		              'checked_ids' : checked_ids,
		              //'nonce': nonce
		            },
		          success: function(data){
		          	jQuery('.listeo_core-term-checklist-listing_feature').html("");
		            jQuery('.form-field-listing_feature-container .checkboxes').removeClass('loading');
		            jQuery('.listeo_core-term-checklist-listing_feature').html(data['output']).removeClass('loading');
		            if(data['success']){
		              jQuery('.form-field-listing_feature-container .panel-buttons').show();
		            }

		          }            
		        });
			}

			jQuery(document).ready(function(){
				catidsFeature();


				jQuery("#list_category").change(function(){

					jQuery('#subcat').html("");
                    if(jQuery(this).prop('multiple')){
				        var cat_ids;
				        cat_ids = jQuery(this).val();
				    } else {
				        var cat_ids = [];
				        cat_ids.push(jQuery(this).val());  
				    }




				    jQuery.ajax({
			          type: 'POST', 
			          dataType: 'json',
			          url: listeo.ajaxurl,
			          data: { 
			              'action': 'listeo_get_sub_category', 
			              'cat_ids' : cat_ids,
			              'taxonomy' :"<?php echo $field['taxonomy'];?>",
			              'list_id' : "<?php echo $list_id;?>",
			              'multiselected' : "<?php echo $multi_select;?>",
			              'panel' : false,
			              //'nonce': nonce
			             },
			          success: function(data){
			          	if(data["output"] != ""){
			          		jQuery('#subcat').html(data["output"]);
			          		jQuery(".chosen-select-no-single").chosen();

			          		jQuery("#subcategories").change();
			          	}
			          	catidsFeature();
			            
			          }            
			        });
                       




				});


                jQuery(document).on("change","#subcategories",function(){
					
                    catidsFeature();

                    if(jQuery(this).prop('multiple')){
				        var cat_ids;
				        cat_ids = jQuery(this).val();
				    } else {
				        var cat_ids = [];
				        cat_ids.push(jQuery(this).val());  
				    }




				    jQuery.ajax({
			          type: 'POST', 
			          dataType: 'json',
			          url: listeo.ajaxurl,
			          data: { 
			              'action': 'listeo_get_sub_sub_category', 
			              'cat_ids' : cat_ids,
			              'taxonomy' :"<?php echo $field['taxonomy'];?>",
			              'list_id' : "<?php echo $list_id;?>",
			              'multiselected' : "<?php echo $multi_select;?>",
			              'panel' : false,
			              //'nonce': nonce
			             },
			          success: function(data){
			          	if(data["output"] != ""){
			          		jQuery(".subsubcat").remove();
			          		jQuery('#subcat').append("<div class='subsubcat'>"+data["output"]+"</div>");
			          		jQuery(".chosen-select-no-single").chosen();

			          		jQuery("#subcategories2").change();
			          	}
			          	catidsFeature();
			            
			          }            
			        });

				});

				jQuery(document).on("change","#subcategories2",function(){
					catidsFeature();
				});


				if(jQuery('#listing_category').val() != null && jQuery('#listing_category').val().length > 0){
					if(jQuery('#listing_category :selected').hasClass('level-0')){
						jQuery('#firstLv').val(jQuery('#listing_category :selected').attr('value'));
						removeLastSelector(false);
					} else if(<?php echo ($nivaa == 2) ? 'true' : 'false'; ?> && jQuery('#listing_category :selected').hasClass('level-1')){
						jQuery('#firstLv').val(<?php echo $listing_second_to_last_category; ?>);
						showLastCat(jQuery('#firstLv'), false);
					} else if(<?php echo ($nivaa == 3) ? 'true' : 'false'; ?> && jQuery('#listing_category :selected').hasClass('level-1')){
						var selectedSecondLv = jQuery('#listing_category :selected').val();
						removeLastSelector(false);
						jQuery('#firstLv').val(<?php echo $listing_second_to_last_category; ?>);
						changedFirstSelect(jQuery('#firstLv'), false);
						jQuery('#secondLv').val(selectedSecondLv);
					} else if(<?php echo ($nivaa == 3) ? 'true' : 'false'; ?> && jQuery('#listing_category :selected').hasClass('level-2')){
						jQuery('#firstLv').val(<?php echo $listing_second_to_last_category; ?>);
						changedFirstSelect(jQuery('#firstLv'), false);
						jQuery('#secondLv').val(<?php echo $listing_third_to_last_category; ?>);
						showLastCat(jQuery('#secondLv'), false);
					}
				}

				jQuery('#listing_category').on('chosen:showing_dropdown', function(evt, params){
				    showCorrectFinalCategories();
				});

			});		

			function showCorrectFinalCategories(){

				var parentCatLevel = (jQuery('#secondLv').length > 0) ? '#secondLv' : '#firstLv';

				var parentCatChosen = jQuery(parentCatLevel + ' option[value="'+jQuery(parentCatLevel).val()+'"]').html();

				// If check for mobile version
				if(jQuery('#listing_category_chosen').length == 0){

					var copy = jQuery('.form-field-listing_category-container #listing_category').clone();

					copy.find('option:contains("' + parentCatChosen + '")').prevAll().andSelf().each(function(){
					    jQuery(this).remove();
					});

					var lvDepth = parseInt(<?php echo $nivaa; ?>) - 1;

					var firstRemoved = false;

					copy.find('option:first-child').nextAll().each(function(){

						if (firstRemoved){
							jQuery(this).hide();
							return;
						}

						if(!jQuery(this).hasClass('level-' + lvDepth)){
							jQuery(this).hide();
							firstRemoved = true;
						}
					});

					copy.find('option').each(function(){
						jQuery(this).html(jQuery(this).html().replace(/&nbsp;/g, ''));
					});

					jQuery('.form-field-listing_category-container #listing_category').html(copy.html());

				} 
				// If desktop version
				else {

					var copy = jQuery('.form-field-listing_category-container #listing_category_chosen ul.chosen-results').clone();

					copy.find('li:contains("' + parentCatChosen + '")').prevAll().andSelf().each(function(){
					    jQuery(this).remove();
					});

					var lvDepth = parseInt(<?php echo $nivaa; ?>) - 1;

					var firstRemoved = false;

					copy.find('li:first-child').nextAll().each(function(){

						if (firstRemoved){
							jQuery(this).remove();
							return;
						}

						if(!jQuery(this).hasClass('level-' + lvDepth)){
							jQuery(this).remove();
							firstRemoved = true;
						}
					});

					copy.find('li').each(function(){
						jQuery(this).html(jQuery(this).html().replace(/&nbsp;/g, ''));
						if(jQuery(this).hasClass('result-selected') && jQuery('.search-choice a[data-option-array-index="'+jQuery(this).attr('data-option-array-index')+'"]').length <= 0)
							jQuery(this).removeClass('result-selected').addClass('active-result');						
					});

					jQuery('.form-field-listing_category-container #listing_category_chosen .chosen-results').html(copy.html());

				}
			};

			function changedFirstSelect(selectObject, updateCats = true){

				// If no valid category chosen, remove the second category (and third)
				if(jQuery('#' + jQuery(selectObject).attr('id')).val() == null || jQuery('#' + jQuery(selectObject).attr('id')).val() == -1){
					jQuery('#labelForSubCategory').remove();
					jQuery('#secondLv').remove();
					removeLastSelector();
					jQuery('#listing_category').val(-1).change();
				} else {

					// Create Dom if not exists
					if(jQuery('#secondLv').length == 0){ <?php 

						$output = "<label id=\"labelForSubCategory\">Underkategori</label><select onchange=\"showLastCat(this)\" id=\"secondLv\"><option value=\"-1\">Velg Kategori</option>";

						foreach(array_keys($firstLvMap) as $key){

							$firstLv = get_terms('listing_category',array(
								'hide_empty' 	=> 0,
							    'parent' 		=> $key,
							));

							foreach($firstLv as $category){

								$term_children_s = get_term_children($category->term_id, 'listing_category');

								$output .= "<option ";
								if(is_array($selected)){
									if(!$secondLvHasLoadedBefore && in_array($category->term_id, $selected)){
										$selectedSecondLv = $category->term_id;
										$output .= " selected ";
									}
								}
								$output .= " value=\"".$category->term_id."\" class=\"".$key;
								if(!empty($term_children_s) && !is_wp_error($term_children_s)){
									$output .= " hasChildCategories ";
								}
								$output .= "\">". $category->name ."</option>";
							}
						}

						$output .= "</select>"; ?>

						jQuery('#' + jQuery(selectObject).attr('id')).after('<?php echo $output; ?>');

						<?php if(!$secondLvHasLoadedBefore && isset($selectedSecondLv) && !is_null($selectedSecondLv)) { ?>
							jQuery('#secondLv').val(<?php echo $selectedSecondLv; ?>).change();	
						<?php } 
						$secondLvHasLoadedBefore = true; ?>
					}
				
					if(updateCats){
						displayCorrectSubCategories(jQuery('#' + jQuery(selectObject).attr('id')).val(), jQuery('#firstLv option[value="'+ jQuery('#' + jQuery(selectObject).attr('id')).val() + '"]'));
					}

					removeLastSelector(false);
				}
			}

			// Second cat select
			function displayCorrectSubCategories(chosenCategoryNr, chosenOption){

				if(chosenOption.hasClass('hasChildCategories')){

					jQuery('#secondLv').val("-1").change();
					jQuery('#secondLv option').each(function(){
						(jQuery(this).hasClass(chosenCategoryNr) || jQuery(this).val() == -1) ? jQuery(this).show() : jQuery(this).hide();
					});

				} else {

					jQuery('#labelForSubCategory').remove();
					jQuery('#secondLv').remove();

					var prevCatChosen = parseInt(jQuery('#firstLv').val());
					jQuery('#listing_category').val(prevCatChosen).change();

				}

				jQuery('#lastCatWrapper').hide();
			}

			function showLastCat(selectObject, removeLast = true){

				if(removeLast)
					removeLastSelector();

				// If valid select-value chosen
				if(jQuery('#' + jQuery(selectObject).attr('id')).val() != -1 && jQuery('#' + jQuery(selectObject).attr('id')).val() != null){

					var prevCatChosen = parseInt(jQuery('#' + jQuery(selectObject).attr('id')).val());

					// If has child categories
					if(jQuery('#' + jQuery(selectObject).attr('id') + ' option[value="' + prevCatChosen + '"]').hasClass('hasChildCategories')){

						if(jQuery('#listing_category_chosen').length <= 0){
							showCorrectFinalCategories();
						}
						jQuery('#lastCatWrapper').show();
						if(removeLast)
							jQuery('#listing_category').val(-1).change();
					} 

					// If does not have child categories, the prev chosen is the select
					else {
						jQuery('#listing_category').val(prevCatChosen).change();
					}
				} else {
					jQuery('#listing_category').val(-1).change();
				}
			}

			function removeLastSelector(nullifyCategories = true){

				// Hide the last selector UI
				jQuery('#lastCatWrapper').hide();

				// Nullify the previous values
				if(nullifyCategories){
					jQuery('#listing_category_chosen li.search-choice').remove();
					jQuery('#listing_category').val([]);
				}

				// Update the UI for future use
				//jQuery('#listing_category_chosen li.result-selected').each(function(){ jQuery(this).removeClass('result-selected').addClass('active-result'); });
			}

		</script> 

	<?php }

} else if($field['taxonomy'] == 'region') {



	if($listing_top_category != null && $listing_top_category != ''){

		//if($multi_select == 1){
			 $multiselect_region_text_checkbox = get_term_meta( $listing_top_category, 'multiselect_region_text_checkbox', true );
			if($multiselect_region_text_checkbox == "on"){
				$multiselect_region_text = get_term_meta( $listing_top_category, 'multiselect_region_text', true );
				if($multiselect_region_text != ""){

		?>
				   <script type="text/javascript">
				   	  //jQuery(document).ready(function(){
                          jQuery(".label-region").html("<?php echo $multiselect_region_text;?>");
				   	 // });
				   	  
				   </script>
		<?php
		        }
		    }	
		//}

		$parentCat = $listing_top_category;

		if($multiselect_region_text_checkbox == "on"){
			$multi_ = "multiple";
		}else{
			$multi_ = "";
		}

		$fromLokaler = get_term_by('id', $parentCat, 'listing_category')->term_id == 63;

		$firstLv = get_terms('region',array(
				'hide_empty' 	=> 0,
			    'parent' 		=> 0,
			));
		$secondLvReg = array();
		if($field["required"] == 1){
			$requi = "required";
		}else{
			$requi = "";
		}
		$all_selected_categores1 = array();
	    if($list_id != ""){
	        $listing_region_categories = wp_get_object_terms($list_id, $field['taxonomy']);
	        foreach ($listing_region_categories as $key => $listing_region_cat) {
	            $all_selected_categores1[] = $listing_region_cat->term_id;
	            
	        }
	    }

		//if(!$fromLokaler)
		//	echo "<a class='button' style=\"display:none;\" id='chooseAllRegions'>Lever til hele Norge</a>";

		echo "<select ".$multi_." class=\" chosen-select-no-single\" name='".$field['name']."' ".$requi." id=\"catRegion\">";
			

		foreach($firstLv as $region){

			$term_children = get_term_children($region->term_id, 'region');

			echo "<option value=\"".$region->term_id."\"";
			if(!empty($term_children) && !is_wp_error($term_children)){
				echo " class=\"hasChildRegions\" ";	
			}
			if(!empty($all_selected_categores1)){
                if(in_array($region->term_id, $all_selected_categores1)){
                    echo " selected "; 
                }
            }
			echo ">". $region->name ."</option>";
			$childrenOfFirstLv = get_terms('region', array(
				'hide_empty' 	=> 0,
			    'parent' 		=> $region->term_id,
			));
			$secondLvReg[$region->term_id] = array();
			foreach($childrenOfFirstLv as $subReg){
				array_push($secondLvReg[$region->term_id], $subReg);
			}
		}

		echo "</select>";

		echo "<div id='subregion'></div>";

		/*if(!$fromLokaler){

			echo "<select multiple=\"multiple\" class=\"region\" id=\"secondLvRegion\">";	

			foreach($secondLvReg as $regionGroupByFylke){
				foreach($regionGroupByFylke as $region)
					echo "<option class=\"parent-".$region->parent."\" value=\"".$region->term_id."\">". $region->name ."</option>";
			}

			echo "</select>";

		}*/

		//if(!$fromLokaler){	?>

		    <script type="text/javascript">
		      jQuery(document).ready(function(){
		      	setTimeout(function(){
                     //jQuery("#catRegion").change();
                     subCatFunc();
		      	},1000)
		      	
		      	/*jQuery("#catRegion").change(function(){

					jQuery('#subregion').html("");
                    if(jQuery(this).prop('multiple')){
				        var cat_ids;
				        cat_ids = jQuery(this).val();
				    } else {
				        var cat_ids = [];
				        cat_ids.push(jQuery(this).val());  
				    }*/

                function subCatFunc(){


				    jQuery.ajax({
			          type: 'POST', 
			          dataType: 'json',
			          url: listeo.ajaxurl,
			          data: { 
			              'action': 'listeo_get_sub_region_category', 
			              //'cat_ids' : cat_ids,
			              'taxonomy' :"<?php echo $field['taxonomy'];?>",
			              'list_id' : "<?php echo $list_id;?>",
			              'multiselected':"<?php echo $multi_;?>",
			              'panel' : false,
			              //'nonce': nonce
			             },
			          success: function(data){
			          	if(data["output"] != ""){
			          		jQuery('#subregion').html(data["output"]);
			          		jQuery(".chosen-select-no-single").chosen();

			          		jQuery("#subregionselect").change();
			          	}
			          	catidsFeature();
			            
			          }            
			        });
			    }    

			    jQuery(document).on("change","#subregionselect",function(){




					if(jQuery(this).find("option:selected").attr("parent_id") != undefined){
						var pr_id = jQuery(this).find("option:selected").attr("parent_id");

						//;

						


						jQuery("#catRegion").find("option").prop("selected",false);
						jQuery("#catRegion").find("option").removeAttr("selected");
						jQuery("#catRegion").find("option[value='"+pr_id+"']").prop("selected",true);
						jQuery("#catRegion").find("option[value='"+pr_id+"']").attr("selected","");

						jQuery('#catRegion').chosen('destroy');


						jQuery('#catRegion').trigger('chosen:updated');

						jQuery("#catRegion").change();
					}
				});    
                       




				/*});*/
		      })
		    </script>

			<?php /*wp_dropdown_categories( apply_filters( 'listeo_core_term_select_field_wp_dropdown_categories_args', array(

				'taxonomy'         => $field['taxonomy'],

				'hierarchical'     => 1,

				'multiple'   	   => $multi,

				'show_option_all'  => false,

				'show_option_none' => (isset($field['required']) && $field['required'] == true) ? '' : __('Choose ','listeo_core'). $taxonomy->labels->singular_name,

				'name'             => (isset( $field['name'] ) ? $field['name'] : $key),

				'orderby'          => 'name',

				'selected'         => $selected,

				'class'			   => 'chosen-select-no-single',

				'hide_empty'       => false,

				'option_none_value' => -100,

				'walker'  => new Willy_Walker_CategoryDropdown()

			), $key, $field ) );*/	?>

			<script>

				var leverHeleNorgeTekst = 'Lever til hele Norge';

				var fylkerChosen = [], isMobileUI;

				jQuery(document).ready(function(){

					jQuery('.label-region').unbind('click');

					jQuery('#region option[value="-100"]').text(leverHeleNorgeTekst);

					fylkerChosen = jQuery('#firstLvRegion').val();

					jQuery('#firstLvRegion').chosen({
    					placeholder_text_multiple: "Velg fylker",
    					width:'100%'
					});

					jQuery('#secondLvRegion').chosen({
						placeholder_text_multiple: "Velg kommuner",
    					width:'100%'
					});

					jQuery('#secondLvRegion').on('chosen:showing_dropdown', function(evt, params){
					    displayCorrectRegionWalkerDropdown();
					});
					jQuery('#secondLvRegion').on('change', function(evt, params){
					    displayCorrectRegionWalkerDropdown();
					});

					isMobileUI = jQuery('#region_chosen').length <= 0;

					// onload, if it is mobile version hide the first select box
					if(isMobileUI){
						jQuery('#firstLvRegion').remove();
						jQuery('#secondLvRegion').remove();
					} else {
						jQuery('#region').chosen('destroy');
						jQuery('#region').hide();
						onloadSetCorrectRegions();
						jQuery('#chooseAllRegions').show();
					}

					jQuery('#firstLvRegion').change(function(){
						updateRegions();
					});
					jQuery('#secondLvRegion').change(function(){
						updateRegions();
					});

					jQuery('#chooseAllRegions').click(function(){
						selectAllRegionsDesktop();
						jQuery('#firstLvRegion').trigger('chosen:updated');
						jQuery('#secondLvRegion').trigger('chosen:updated');
					});

					if(isMobileUI){
						jQuery('#region').change(function(){

							if(jQuery.inArray( "-100" , jQuery(this).val()) != -1){

								var allRegions = [];
							    jQuery('#region option:not([value="-100"])').each(function(){
							    	allRegions.push(jQuery(this).attr('value'));
							    });
							    jQuery('#region').val(allRegions).change();

							} else {

								var fylkerToAdd = [], totalChosen;
								jQuery('#region .level-1:selected').each(function(){
									var fylke = jQuery(this).prev();
									while(!fylke.hasClass('level-0'))
										fylke = fylke.prev();
									
									fylkerToAdd.push(fylke.attr('value'));
								});
								var prev = jQuery('#region').val();
								for(var i = 0; i < fylkerToAdd.length; i++)
									prev.push(fylkerToAdd[i]);
								
								jQuery('#region').val(prev);
							}

						});
					}
				});
				function displayCorrectRegionWalkerDropdown(){

				    fylkerchosen = jQuery('#firstLvRegion').val();

				    if(fylkerchosen != null && fylkerchosen.length > 0){		
				    	jQuery('#secondLvRegion_chosen .chosen-drop li').hide();
				    	for(var i = 0; i < fylkerchosen.length; i++)
				    		jQuery('#secondLvRegion_chosen .chosen-drop li.parent-' + fylkerchosen[i]).show();
				    	
				    } else if (fylkerchosen != null && fylkerchosen[0] == -100) 
				    	jQuery('#secondLvRegion_chosen .chosen-drop li').show();
				    else 
				  		jQuery('#secondLvRegion_chosen .chosen-drop li').hide();
				}
				function updateRegions(hasValidKommunes = false){

					var combinedValues  = [];
					var fylkeValues = jQuery('#firstLvRegion').val(), kommuneValues = jQuery('#secondLvRegion').val();

					var validKommunes = [];

					// Loop through all 'kommune' to ensure they all belong to 'fylke', if not then remove the kommunes
					if(fylkeValues == null)
						fylkeValues = [];
					
					if(!hasValidKommunes){
						jQuery('#secondLvRegion :selected').each(function(){
						    if(jQuery.inArray(this.className.slice('parent-'.length), fylkeValues) !== -1)
						    	validKommunes.push(this.value);
						});

						jQuery('#secondLvRegion').val(validKommunes);
						jQuery('#secondLvRegion').trigger('chosen:updated');

						updateRegions(true);
					}

					if(kommuneValues == null)
						combinedValues = fylkeValues;
					else
						combinedValues = jQuery.merge(fylkeValues, kommuneValues);
					
					jQuery('#region').val(combinedValues).change();
				}
				function selectAllRegionsDesktop(){
					jQuery('#firstLvRegion option').prop('selected', true);
					jQuery('#firstLvRegion').change();
					jQuery('#secondLvRegion option').prop('selected', true);
					jQuery('#secondLvRegion').change();
				}
				function onloadSetCorrectRegions(){
					var onloadRegions = jQuery('#region').val();

					if(onloadRegions != null){
						for(var i = 0; i < onloadRegions.length; i++){
							jQuery('#firstLvRegion option[value="' + onloadRegions[i] + '"]').prop('selected', true);
							jQuery('#secondLvRegion option[value="' + onloadRegions[i] + '"]').prop('selected', true);
						}

						jQuery('#firstLvRegion').change();
						jQuery('#secondLvRegion').change();
					}

					jQuery('#firstLvRegion').trigger('chosen:updated');
					jQuery('#secondLvRegion').trigger('chosen:updated');
				}
			</script>

		<?php //} else {

			/*wp_dropdown_categories( apply_filters( 'listeo_core_term_select_field_wp_dropdown_categories_args', array(

				'taxonomy'         => $field['taxonomy'],

				'hierarchical'     => 1,

				'multiple'   	   => false,

				'show_option_all'  => false,

				'show_option_none' => (isset($field['required']) && $field['required'] == true) ? '' : __('Choose ','listeo_core'). $taxonomy->labels->singular_name,

				'name'             => (isset( $field['name'] ) ? $field['name'] : $key),

				'orderby'          => 'name',

				'selected'         => $selected,

				'class'			   => 'hidden',

				'hide_empty'       => false,

				'walker'  => new Willy_Walker_CategoryDropdown()

			), $key, $field ) );

			echo "<select style=\"display:none;\" id=\"secondLvRegion\"><option value=\"-1\">Velg Kommune</option>";

			foreach($secondLvReg as $firstLvRegion){
				foreach($firstLvRegion as $secondLvRegion)
					echo "<option class=\"parent-" . $secondLvRegion->parent . "\" value=\"". $secondLvRegion->term_id."\">" . $secondLvRegion->name . "</option>";
			}*/

			//echo "</select>"; ?>

			<script>

				jQuery(document).ready(function(){

					// onload, if region exists from before (editing listing) display the correct region-info
					if(jQuery('#region :selected').val() != "-1"){

						var kommunensFylke = jQuery('#region :selected').val();

						// If kommune chosen
						if(jQuery('#region :selected').hasClass('level-1')){
							jQuery('#secondLvRegion').show();
							jQuery('#secondLvRegion').val(jQuery('#region :selected').val()).change();
							kommunensFylke = jQuery('#secondLvRegion :selected').attr('class').slice('parent-'.length);
							jQuery('#secondLvRegion option').each(function(){
								if(jQuery(this).hasClass('parent-' + kommunensFylke))
									jQuery(this).show();
								else
									jQuery(this).hide();
							});
						}

						jQuery('#firstLvRegion').val(kommunensFylke).change();
					}

					jQuery('#firstLvRegion').change(function(){

						jQuery('#secondLvRegion').val(-1).change();

						// If chosen fylke does not have child regions, hide the kommune select picker
						if(!jQuery('#firstLvRegion :selected').hasClass('hasChildRegions')){
							jQuery('#secondLvRegion').hide();
							jQuery('#region').val(jQuery('#firstLvRegion :selected').val()).change();
						}
						
						// If chosen fylke does have child regions, display the relevant kommunes
						else {

							var chosenFylke = jQuery(this).val();
							jQuery('#secondLvRegion').show();
							jQuery('#secondLvRegion option:not(:first)').each(function(){
								if(jQuery(this).hasClass('parent-' + chosenFylke))
									jQuery(this).show();
								else
									jQuery(this).hide();
							});

						}
					});

					jQuery('#secondLvRegion').change(function(){
						jQuery('#region').val(jQuery('#secondLvRegion :selected').val()).change();						
					});

				});
			</script>

			<?php 
		//}

	} else {
		
		wp_dropdown_categories( apply_filters( 'listeo_core_term_select_field_wp_dropdown_categories_args', array(

			'taxonomy'         => $field['taxonomy'],

			'hierarchical'     => 1,

			'multiple'   	   => $multi,

			'show_option_all'  => false,

			'show_option_none' => (isset($field['required']) && $field['required'] == true) ? '' : __('Choose ','listeo_core'). $taxonomy->labels->singular_name,

			'name'             => (isset( $field['name'] ) ? $field['name'] : $key),

			'orderby'          => 'name',

			'selected'         => $selected,

			'class'			   => 'chosen-select-no-single',

			'hide_empty'       => false,

			 'walker'  => new Willy_Walker_CategoryDropdown()

		), $key, $field ) );

	}


} else {

	wp_dropdown_categories( apply_filters( 'listeo_core_term_select_field_wp_dropdown_categories_args', array(

		'taxonomy'         => $field['taxonomy'],

		'hierarchical'     => 1,

		'multiple'   	   => $multi,

		'show_option_all'  => false,

		'show_option_none' => (isset($field['required']) && $field['required'] == true) ? '' : __('Choose ','listeo_core'). $taxonomy->labels->singular_name,

		'name'             => (isset( $field['name'] ) ? $field['name'] : $key),

		'orderby'          => 'name',

		'selected'         => $selected,

		'class'			   => 'chosen-select-no-single',

		'hide_empty'       => false,

		 'walker'  => new Willy_Walker_CategoryDropdown()

	), $key, $field ) );

}

 ?>
 <?php if(isset($_GET['listing_id']) && $_GET['listing_id'] != null) { ?>
  <script type="text/javascript">
    jQuery(document).ready(function(){
    	setTimeout(function(){
    		jQuery("#list_category").change();
    		
    	},100)
    	
    })
  </script>
   
<?php } ?>

<style type="text/css">
#region_chosen{
	display: none;
}
</style>