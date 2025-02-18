<div class="inner-get_day" application_id="<?php echo $application_id;?>">

	<div class="row sections">
		<div class="single_application_title single_reservation_title d-flex justify-content-between">
		   <h3><?php echo $res_datas_day["title"];?></h3>
		   <button class="delete_reserve"> <i class="fa fa-trash delete_reserve"></i>Slett reservasjon </button>
		   
		</div>
		<hr />
	</div>
	<div class="row">

		<?php foreach ($res_datas_day["fields"] as $key_daya => $row) { 

		   
			?>	

			<?php
			if($row['type'] == "text"){ 

			 		include("text.php");

			    }elseif($row['type'] == "date"){ 

			 		include("date.php");

			    }elseif($row['type'] == "tel"){ 

			 		include("tel.php");

			    }else if($row['type'] == "email"){ 

			    	include("email.php");

			    }else if($row['type'] == "number"){ 

			    	include("number.php");

			    }else if($row['type'] == "select"){ 

			    	include("select.php");

			    }else if($row['type'] == "custom_text"){ 

			    	include("custom_text.php");

			    }else if($row['type'] == "custom_button"){ 

			    	include("custom_button.php");

			    }else if($row['type'] == "get_day"){ 

			    	include("get_day.php");

			    }
			?>    
		<?php }  ?>	
	</div>	
	<div class="custom_fields">
		<?php 
		if(isset($res_datas_day["advanced_fields"]) && is_array($res_datas_day["advanced_fields"])){
			foreach ($res_datas_day["advanced_fields"] as $key => $res_datas) {

				require("advanced_fields.php");

			}
		}
		
		?>
	</div>	
    
	    <?php foreach ($res_datas_day["add_fields_button"] as $row) { ?>
	    	<div class="row sections add_fields_button_row" <?php if($row["hide"] == 1){ ?>style="display: none"<?php } ?>>
	    	  <?php  include("custom_button.php"); ?>
	    	</div>
	    <?php } ?>
	
			
</div>	