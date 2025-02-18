<div class="<?php echo $row['class'];?>">
	<div class="form-group div_main_tl">
        <label for="<?php echo $row['name'];?>">
        	<?php echo $row['label'];?><?php if($row['required'] == true){?><span class="see_the_star">*</span><?php } ?> 
        	<span class="tooltip_main">
                <?php if($row['tooltip'] != ""){ ?>
                    <i class="tip_app info-icon"><div class="tip-content"><?php echo $row['tooltip'];?></div></i>
                <?php } ?>
                <?php if($row['important'] != ""){ ?>
                     <i class="tip_app important-icon"><div class="tip-content"><?php echo $row['important'];?></div></i>
                <?php } ?>
            </span>
        </label>
        <input type="tel" class="form-control <?php if($row['required'] == true){?>required<?php } ?>" id="phonenumberForm" name="<?php echo $row['name'];?>"  <?php if($row['required'] == true){ ?> required <?php } ?> <?php if(isset($row['org-name'])){?>org-name="<?php echo $row['org-name'];?>" <?php } ?> value="<?php echo $row['value'];?>">
    </div>
</div>