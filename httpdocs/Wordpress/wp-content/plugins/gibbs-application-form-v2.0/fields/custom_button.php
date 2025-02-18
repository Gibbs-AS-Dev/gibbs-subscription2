<div class="<?php echo $row['class'];?>">
	<?php if(isset($row['link']) && $row['link'] != ""){ ?>
	   <a href="<?php echo $row['link'];?>"><button class="btn btn-primary"><?php echo $row["label"];?></button></a>
	<?php }else{ ?>
		<button class="btn btn-primary" type="button" <?php if(isset($row['attribute'])){ echo $row['attribute']; } ?>><?php echo $row["label"];?></button>
	<?php } ?>   
</div>
