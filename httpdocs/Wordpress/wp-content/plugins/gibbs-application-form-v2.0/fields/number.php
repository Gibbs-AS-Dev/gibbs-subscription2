<div class="<?php echo $row['class'];?>">
	<div class="form-group div_main_tl">
        <label for="<?php echo $row['name'];?>">
        	<?php echo $row['label'];?><?php if($row['required'] == true){?><span class="see_the_star">*</span><?php } ?> 
        	<span class="tooltip_main">
	        	<?php if($row['tooltip'] != ""){ ?>
			        <i class="tip_app info-icon">
			        	<div class="tip-content">
			        		<?php echo $row['tooltip'];?>
			        		<div class="close-tip"><i class="fa fa-close"></i></div>
			        	</div></i>
			    <?php } ?>
			    <?php if($row['important'] != ""){ ?>
			         <i class="tip_app important-icon"><div class="tip-content"><?php echo $row['important'];?></div></i>
			    <?php } ?>
	        </span>
        </label>
        <input type="number" class="form-control input_field <?php if($row['required'] == true){?>required<?php } ?>" <?php if(isset($row['org-name'])){?>org-name="<?php echo $row['org-name'];?>" <?php } ?> id="<?php echo $row['name'];?>" name="<?php echo $row['name'];?>"  value="<?php echo $row['value'];?>" <?php if(isset($row['max_input_number']) && $row['max_input_number'] != ""){?>maxlength="<?php echo $row['max_input_number'];?>" oninput="javascript: if (this.value > this.maxLength) this.value = this.value.substring(0, this.value.length-1);" <?php } ?>>
    </div>
</div>