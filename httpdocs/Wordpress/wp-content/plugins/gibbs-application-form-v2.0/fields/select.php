<?php
if(isset($row['select_and_hide']) && $row['select_and_hide'] == true){
    if(count($row['options']) == 1){

          $row["hide"] = true;
          $row["type_select"] = "without_search";
          if($row['selected'] == ""){
               foreach ($row['options'] as $key_opt => $optionnnn) {
                  $row['selected'] =  $key_opt;
                  break;
               }
               
          }
         
    }
}
?>
<div class="<?php echo $row['class'];?>" <?php if(isset($row['hide']) && $row['hide'] == true){?> style="display:none" <?php } ?>>
	<div class="form-group div_main_tl" >
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

        <?php 

            $type_select = false;
            $multiple = false;
            if(isset($row["type_select"]) && $row["type_select"] == "with_search"){
                $type_select = true;
            }
            if(isset($row["multiple"]) && $row["multiple"] == "1"){
                $multiple = true;
                $type_select = true;
            }

            $selectedData = array();
            
            if(str_contains($row['selected'], ",")){


            	$selectedData = explode(",", $row['selected']);

            }else{
            	$selectedData[] = $row['selected'];
            }

            $required = "";

            if($row['required'] == true){
                $required = "required";
            }

            if (str_contains($row["name"], 'sub-location')) { 
                if($row["hide"] == true){
                    if($row['required'] == true){
                        $required = "required1";
                    }
                }
            }
           




            if(isset($row["multiple"]) && $row["multiple"] == true){

        	    
        	    $name = '';
        	    ?>
        	    <input type="hidden" clsss="input_field" <?php if(isset($row['org-name'])){?>org-name="<?php echo $row['org-name'];?>" <?php } ?> name="<?php echo $row['name'];?>" value="<?php echo $row['selected'];?>">
        	    <?php


	        }else{

	        	$name = 'name="'.$row['name'].'"';
	        }

	    ?>
         <select class="form-select input_field <?php if($type_select == true){ echo 'select2_field';}?> <?php echo $required;?>" <?php if(isset($row['org-name'])){?>org-name="<?php echo $row['org-name'];?>" <?php } ?> id="<?php echo $row['id'];?>" <?php echo $name;?> <?php if(isset($row['attribute'])){ echo $row['attribute']; } ?> <?php if($multiple == true){ echo "multiple";}?>>
		   <option  value="">Velg</option>
		   <?php foreach ($row['options'] as $key_option => $option) { ?>

		   	  <?php if(in_array($key_option,$selectedData)){
		   	  	        $selected = "selected";
				   	}else{
                         $selected = "";
				   	}
				?>
		   	  <option value="<?php echo $key_option;?>" <?php echo $selected;?>><?php echo $option;?></option>
		   <?php } ?>
		</select>
    </div>
</div>