<div class="<?php echo $row['class'];?>">
	<div class="form-group d-flex align-items-center">
        
        <input type="checkbox" class="form-check-input input_field <?php if($row['required'] == true){?>required<?php } ?>"  <?php if(isset($row['org-name'])){?>org-name="<?php echo $row['org-name'];?>" <?php } ?> id="<?php echo $row['name'];?>" name="<?php echo $row['name'];?>"> <label for="<?php echo $row['name'];?>"><?php echo $row['label'];?><?php if($row['required'] == true){?><span class="see_the_star">*</span><?php } ?> </label>
    </div>
</div>