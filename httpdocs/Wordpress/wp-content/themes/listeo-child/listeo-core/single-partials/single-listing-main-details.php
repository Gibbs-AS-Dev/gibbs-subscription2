<!-- Main Details -->
<?php 

$type = get_post_meta($post->ID, '_listing_type',true);
$details_list = array();
switch ($type) {
	case 'service':
		$details_list = Listeo_Core_Meta_Boxes::meta_boxes_service(); 
		break;	
	case 'rental':
		$details_list = Listeo_Core_Meta_Boxes::meta_boxes_rental(); 
		break;	
	case 'event':
		$details_list = Listeo_Core_Meta_Boxes::meta_boxes_event(); 
		break;
	case 'classifieds':
		$details_list = Listeo_Core_Meta_Boxes::meta_boxes_classifieds(); 
		if(!empty($details_list)){
			unset($details_list['fields']['_classifieds_price']);	
		}
		
		break;
	
	default:
//		$details_list = Listeo_Core_Meta_Boxes::meta_boxes_main_details_service(); 

		break;
}


$class = (isset($data->class)) ? $data->class : 'listing-details' ;
$output = '';

//echo "<pre>"; print_r($details_list['fields']); die;
?>


<?php 
if(isset($details_list['fields'])) :
	foreach ($details_list['fields'] as $detail => $value) {

		if($value["id"] != "_event_date" &&  $value["id"] != "_event_date_end"){

			if(isset($value['icon']) && !empty($value['icon'])){
					$check_if_im = substr($value['icon'], 0, 3);
	                if($check_if_im == 'im ') {
	                   $icon = ' <i class="'.esc_attr($value['icon']).'"></i>'; 
	                } else {
	                  $icon = ' <i class="fa '.esc_attr($value['icon']).'"></i>'; 
	                }
				
			} else {
				$icon = '<i class="fas fa-check"></i>';
			}

			if( in_array($value['type'], array('select_multiple','multicheck_split','multicheck')) ) {
				$meta_value = get_post_meta($post->ID, $value['id'],false);	
			} else {
				$meta_value = get_post_meta($post->ID, $value['id'],true);	
			};
		
			if($meta_value == 'check_on' || $meta_value == 'on') {
				$output .= '<li class="checkboxed single-property-detail-'.$value['id'].'"><div class="single-property-detail-label-'.$value['id'].'">'. $value['name'].'</div></li>';	
			} else {
				if(!empty($meta_value)){
					if($value['type'] == 'datetime' || in_array($value['id'], array('_event_date','_event_date_end')) ){
						
							$meta_value_date = explode(' ', $meta_value,2); 
							// if(!in_array($date_format,array('F j, Y','Y-m-d','m/d/Y','d/m/Y'))) {
							// 	$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
							// }
							$date_format = get_option( 'date_format' );
						
							//$meta_value = date_i18n(get_option( 'date_format' ), strtotime($meta_value_date[0])); 
							// $meta_value_stamp_obj = DateTime::createFromFormat(listeo_date_time_wp_format_php(), $meta_value_date[0]);
							// if($meta_value_stamp_obj){
							// 	$meta_value_stamp = $meta_value_stamp_obj->getTimestamp();
							// }
							
							
	//						$meta_value = date_i18n(get_option( 'date_format' ),$meta_value_stamp);
							$meta_value_ = DateTime::createFromFormat(listeo_date_time_wp_format_php(), $meta_value_date[0]);
							//var_dump($meta_value);
							if(!is_string($meta_value)) {
							    $meta_value_stamp = $meta_value_->getTimestamp();
							    $meta_value = date_i18n(get_option( 'date_format' ),$meta_value_stamp);
							} else {
							    $meta_value = $meta_value_date[0];
							}
													
							//echo strtotime(end($meta_value_date));
							//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
							if( isset($meta_value_date[1]) ) { 
								$time = str_replace('-','',$meta_value_date[1]);
								$meta_value .= esc_html__(' at ','listeo_core'); 
								$meta_value .= date_i18n(get_option( 'time_format' ), strtotime($time));

							}
						// $convertedData = listeo_date_time_wp_format_php();
						// $clock_format = get_option('listeo_clock_format');
						// if($clock_format == "24") {
						// 	$dateformated = DateTime::createFromFormat($convertedData.' H:i', $meta_value);	
						// } else {
						// 	$dateformated = DateTime::createFromFormat($convertedData.' h:i A', $meta_value);	
						// }
						
						
						// if($dateformated){
						// 	$date_format = get_option( 'date_format' );
						// 	$time_format = get_option( 'time_format' );
						// 	$meta_value = $dateformated->format($date_format);
						// 	$meta_value .= ' - '. $dateformated->format($time_format);
						// }

					}
				}
				if(in_array($value['id'], array('_id','_ID','_Id'))){
					$meta_value = apply_filters('listeo_listing_id',$post->ID);
				}
			
				if(!empty($meta_value)) {
					//echo "tu jestesmy ".$value['id'].' '.$value['type'].' <br>';
					if($value['id'] == '_area'){
						$scale = get_option( 'listeo_scale', 'sq ft' );			
						if( isset($value['invert']) && $value['invert'] == true ) {		

							$output .= '<li class="main-detail-'.$value['id'].'">'.$icon. apply_filters('listeo_scale',$scale).' <span>'.$meta_value.'</span> </li>';
						} else {
							$output .= '<li class="main-detail-'.$value['id'].'">'.$icon.'<span>'.$meta_value.'</span> '.apply_filters('listeo_scale',$scale).' </li>';	
						}
						
					} else if($details_list['fields'][$detail]['type']=='file') {
						$output .= '<li class="main-detail-'.$value['id'].' listeo-download-detail"> <a href="'.$meta_value.'" /> '.esc_html__('Download','listeo_core').' '.wp_basename($meta_value).' </a></li>';
					} else {
	// 					echo "<pre>";
	// var_dump();
	// echo "</pre>";
						if(isset($details_list['fields'][$detail]['options']) && !empty($details_list['fields'][$detail]['options'])) {

							if(is_array($meta_value) && !empty($meta_value)) {
								
										
								if( isset($value['invert']) && $value['invert'] == true ) {		
										$output .= '<li class="main-detail-'.$value['id'].'">'.$icon.'<span>';
										$i=0;
										$last = count($meta_value);

										
										foreach ($meta_value as $key => $saved_value) {	
											$i++;
											if(isset($details_list['fields'][$detail]['options'][$saved_value]))
												$output .= $details_list['fields'][$detail]['options'][$saved_value];
												if($i >= 1 && $i < $last) : $output .= ", "; endif;
											
											
										}
										$output .= '</span> <div class="single-property-detail-label-'.$value['id'].'">'. $value['name'].'</div> </li>';			
									
								} else {						
										
									$output .= '<li class="main-detail-'.$value['id'].'">'.$icon.'<div class="single-property-detail-label-'.$value['id'].'">'. $value['name'].'</div> <span>';
										
										$i=0;
										
									
										// if(!empty($meta_value) && $details_list['fields'][$detail]['type'] == 'select_multiple') {
										// 	$meta_value = $meta_value[0];
										
										// }
										$last = count($meta_value);
										
										foreach ($meta_value as $key => $saved_value) {	
											$i++;
											if($details_list['fields'][$detail]['type'] == 'select_multiple') {

												if(isset($details_list['fields'][$detail]['options'][$saved_value]))
													$output .= $details_list['fields'][$detail]['options'][$saved_value];
													if($i >= 0 && $i < $last) : $output .= ", "; 
												endif;
											
											} else {
											
												if(isset($details_list['fields'][$detail]['options'][$saved_value]))
													$output .= $details_list['fields'][$detail]['options'][$saved_value];
													if($i >= 0 && $i < $last) : $output .= ", "; 
												endif;	
											}
											
											
										}
									$output .= '</span></li>';
								}
								

							} else {

								if( isset($value['invert']) && $value['invert'] == true ) {	
									if(isset($details_list['fields'][$detail]['options'][$meta_value])){
										$output .= '<li class="main-detail-'.$value['id'].'">'.$icon.'<span>'.$details_list['fields'][$detail]['options'][$meta_value].'</span> <div class="single-property-detail-label-'.$value['id'].'">'. $value['name'].'</div> </li>';			
									}	
								} else {
									$output .= '<li class="main-detail-'.$value['id'].'">'.$icon.'<div class="single-property-detail-label-'.$value['id'].'">'. $value['name'].'</div> <span>'.$details_list['fields'][$detail]['options'][$meta_value].'</span></li>';
								}

							}
							
							
						} else {

							if( isset($value['invert']) && $value['invert'] == true ) {	
								$output .= '<li class="main-detail-'.$value['id'].'">'.$icon.'<div class="single-property-detail-label-'.$value['id'].'">'. $value['name'].'</div> <span>';
								$output .= (is_array($meta_value)) ? implode(",", $meta_value) : $meta_value ;
								$output .= '</span></li>';	
							} else {
								$output .= '<li class="main-detail-'.$value['id'].'">'.$icon.'<span>';
								$output .= (is_array($meta_value)) ? implode(",", $meta_value) : $meta_value ;
								$output .= '</span> <div class="single-property-detail-label-'.$value['id'].'">'. $value['name'].'</div> </li>';		
							}
							
						}
						
					}
					
				}
			}
		}
	}
endif;
if(!empty($output)) : ?>
<ul class="<?php esc_attr_e($class); ?>">
	<?php echo $output; ?>
</ul>
<?php endif; ?>