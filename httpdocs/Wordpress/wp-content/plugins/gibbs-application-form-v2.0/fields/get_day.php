<?php
//echo "<pre>"; print_r($json_data); die;
$res_datas = $row["values"];
$application_id = $row["application_id"];

?>
<div class="main_get_day_app main_get_day_<?php echo $application_id;?> col-md-12">
	<?php  
	    foreach ($res_datas as  $res_datas_days) {



		    foreach ($res_datas_days as  $res_datas_day) {

		    	

		    	$fieldss = $res_datas_day["fields"];

		    	include("inner_get_day.php");	 
		    }
		}
	?>

	
</div>	