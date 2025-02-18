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

if(isset( $field['options_cb'] ) && !empty($field['options_cb'])){
	switch ($field['options_cb']) {
		case 'listeo_core_get_offer_types_flat':
			$field['options'] = listeo_core_get_offer_types_flat(false);
			break;

		case 'listeo_core_get_listing_types':
			$field['options'] = listeo_core_get_listing_types();
			break;

		case 'listeo_core_get_rental_period':
			$field['options'] = listeo_core_get_rental_period();
			break;

		// case 'timezone':
		// 	$default = CMB2_Utils::timezone_string();
		// 	$field['options'] = wp_timezone_choice($default);
		// 	break;
		
		default:
			# code...
			break;
	}	
}

if(isset($field['class']) && $field['class'] == "booking_system_select"){
	$booking_systems = get_booking_systems();

	$current_bk = "";

	if(isset($_GET['listing_id'])){

		$current_bk = get_post_meta($_GET['listing_id'],"_booking_system",true);

	}else{
		$current_bk = $_POST['_booking_system'];
	}


?>
    <div class="listing_demo_div select_bk_div dashboard-list-box">
            <ul>
            <?php
              foreach ($booking_systems as $booking_system) {

              	if($booking_system["name"] == $current_bk){

              ?>  

                  
	                <li>
	                  <div class="list-box-listing">
	                     <div class="list-box-listing-img">
	                                    <a href="javascript::void(0)"><?php
	                                             $image_url = $booking_system["image_path"];
	                                            ?>
	                                            <img src="<?php echo esc_attr($image_url); ?>" alt="">

	                                        <!-- <i class="direct_icon fa-solid fa-arrow-up-right-from-square"></i> -->
	                                    </a>

	                                </div>
	                    <div class="list-box-listing-content">
	                      <div class="inner">
	                        <h3><?php echo $booking_system["title"]; //echo listeo_core_get_post_status($listing_id) ?></h3>
	                        <p><?php echo $booking_system["description"]; //echo listeo_core_get_post_status($listing_id) ?></p>
	                      </div>
	                    </div>
	                  </div>
	     			    <div class="buttons-to-right">
	                      <!--          <button type="button" data-booking_system="<?php echo $booking_system["name"];?>" class="button selected gray">
	                      <i class="fa-regular fa-file-lines"></i> <?php echo 'Valgt'; ?></button> -->
	                      <br>
	                      <button type='button' class='select_booking_system_btn btn-primary'>Bytt bookingystem</button>
	                  </div>
	                </li>
	              
	        <?php } 
           }
        ?>
        </ul>
       </div>

<?php

	//echo "<button type='button' class='select_booking_system_btn btn-primary'>Bytt booking system</button>";
	echo "<div class='booking_system_main_div' style=\"display:none\">";
}

?>

<select <?php if($multi) echo "multiple"; ?> class="<?php if($multi) echo "chosen-select-no-single"; ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : $key ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key );  if($multi) echo "[]"; ?>" id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>>
	
	<?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
		<option value=""><?php echo esc_attr($field['placeholder']);?></option>
	<?php endif ?>

	<?php foreach ( $field['options'] as $key => $value ) : ?>

		<option value="<?php echo esc_attr( $key ); ?>" <?php

			if( isset( $field['value']) && is_array( $field['value'] ) ) {
				
				if(isset($field['value'][0]) && !empty($field['value'][0])){
				
						if(in_array($key, $field['value'])){
						echo 'selected="selected"';
				
					}	
				}

			} else {

				if ( isset( $field['value'] ) || isset( $field['default'] ) ) selected( isset( $field['value'] ) ? 
					$field['value'] : $field['default'], $key ); 
			}
			
			?>><?php echo esc_html( $value ); ?></option>

	<?php endforeach; ?>

</select>

<?php
	if(isset($field['class']) && $field['class'] == "booking_system_select"){
		echo "</div>";
	}
?>