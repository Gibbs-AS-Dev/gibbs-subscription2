<div id="season-template-popup" class="template-popup">

    <div class="mbsc-form-group">

    	<div class="header_div">

    		<div class="top_temp">
                <!-- <div class="temp1 create_template_cal"><i class="fa fa-plus-circle "></i></div> -->
                <div class="temp2"><h4>Velg visning</h4></div>
                <div class="temp3 close_template"><i class="fa fa-close close_tmp"></i></div>
            </div>
    		
    	</div>

    	<ul class="template_divv">
    		<?php foreach ($filter_template_data as $key => $filter_template) {
                    $seleted = "";
                    if($template_selected == $filter_template->id){

                      $seleted = "selected";

                    }
                    ?>
                    <li class="template_li" data-id="<?php echo $filter_template->id;?>">
		    			<div class="row">
		    				<div class="col-md-8 title_divs d1 <?php echo $seleted;?>" data-id="<?php echo $filter_template->id;?>"><span><?php echo $filter_template->name;?></span></div>
		    				<div class="col-md-2 d1 edit_template" data-id="<?php echo $filter_template->id;?>" data-name="<?php echo $filter_template->name;?>"><i class="fa fa-edit"></i></div>
		    				<div class="col-md-2 d1 delete_template_form" data-id="<?php echo $filter_template->id;?>"><i class="fa fa-trash" ></i></div>
		    			</div>
		    		</li>
            <?php } ?>        
    	</ul>
        <div class="last_tmp_div">
    		<button type="button" class="btn btn-primary gibbs-btn create_template_cal"><i class="fa fa-circle-plus"></i> Opprett ny visning</button>
    	</div> 
        <!-- <div class="row row-bottom">
            <label for="update_season_template_auto" class="update_template_auto_main">
               <input mbsc-switch data-label="Oppdater visning automatisk" type="checkbox" id="update_season_template_auto" name="update_season_template_auto" <?php if($season_template_auto == "yes") { echo "checked"; }?>>
            </label>
        </div> -->

        
    </div>

</div>

<script type="text/javascript">
    
    
    
</script>