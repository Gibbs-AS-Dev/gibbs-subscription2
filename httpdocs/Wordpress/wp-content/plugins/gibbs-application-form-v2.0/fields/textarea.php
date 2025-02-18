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
        <textarea  rows="2" class="form-control input_field <?php if($row['required'] == true){?>required<?php } ?>" id="<?php echo $row['name'];?>" <?php if(isset($row['org-name'])){?>org-name="<?php echo $row['org-name'];?>" <?php } ?> name="<?php echo $row['name'];?>"><?php echo $row['value'];?></textarea>
    </div>
</div>