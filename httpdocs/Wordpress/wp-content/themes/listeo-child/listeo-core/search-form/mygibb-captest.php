<?php 
$flag_enabled1 = false;

$flag_enabled7 = false;

global $wpdb;

$standingmax = $wpdb->get_results(
	"SELECT max( cast( meta_value as UNSIGNED ) ) AS standing FROM ptn_postmeta WHERE meta_key='_standing'"
);
$standingmin = $wpdb->get_results(
	"SELECT min( cast( meta_value as UNSIGNED ) ) AS standing FROM ptn_postmeta WHERE meta_key='_standing'"
);

// $coronaresmax = $wpdb->get_results(
// 	"SELECT max( cast( meta_value as UNSIGNED ) ) AS coronares FROM ptn_postmeta WHERE meta_key='_coronares'"
// );
// $coronaresmin = $wpdb->get_results(
// 	"SELECT min( cast( meta_value as UNSIGNED ) ) AS coronares FROM ptn_postmeta WHERE meta_key='_coronares'"
// );



$standingmax = $standingmax[0]->standing;
$standingmin = $standingmin[0]->standing;

// $coronaresmax = $coronaresmax[0]->coronares;
// $coronaresmin = $coronaresmin[0]->coronares;



if(!empty($standingmax)){
	$flag_enabled1 = true;
}


// if(!empty($coronaresmax)){
// 	$flag_enabled7 = true;
// }

?>

<!-- Range Slider --> 

<div class="row capacityClass " >
	<!-- Range Slider -->
	<div class="range-slider-container col-md-12 <?php if($flag_enabled1) { echo 'no-to-disable'; } ?>">
		<span class="range-slider-headline">Maks kapasitet </span>
		<input  id="_standing"  name="_standing"  class="bootstrap-range-slider" type="text" value="" data-slider-currency="s" data-slider-min="<?php echo $standingmin;?>" data-slider-max="<?php echo $standingmax;?>" data-slider-step="1" data-slider-value="[<?php echo $standingmin.','.$standingmax;?>]"/>
    </div>
	<span class="slider-disable hidden" data-disable="<?php esc_html_e('Disable','listeo_core');?><?php echo esc_html($data->placeholder) ?> " data-enable="<?php esc_html_e('Enable','listeo_core');?> <?php echo esc_html($data->placeholder) ?> "><?php esc_html_e('Enable','listeo_core');?> <?php echo esc_html($data->placeholder) ?></span>
</div>