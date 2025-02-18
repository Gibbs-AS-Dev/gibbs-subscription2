<div class="row section_col">
    <?php
     $sub_listing_id = get_query_var( 'sub_listing_id' );
     $selected_sports = get_query_var( 'selected_sports' );
     if($selected_sports == ""){
        $selected_sports = array();
     }
     $title = get_query_var( 'title' );
     $percentage_full_price = get_query_var( 'percentage_full_price' );

    ?> 
    <div class="col-md-3">
        <label>Tittel <span class="delete_sub_listing" sub_listing_id="<?php echo $sub_listing_id;?>"><i class="fa fa-trash"></i></span></label>
        <input type="text"  name="sub_listing[<?php echo $sub_listing_id;?>][title]" value="<?php echo $title;?>" required>
        <div>
            <label>Prosentandel av fullpris</label>
            <select name="sub_listing[<?php echo $sub_listing_id;?>][percentage_full_price]">
                <option value="100" <?php if($percentage_full_price == "100"){ echo 'selected';}?>>100% (1/1)</option>
                <option value="80" <?php if($percentage_full_price == "16.66"){ echo 'selected';}?>>80% (4/5)</option>
                <option value="75" <?php if($percentage_full_price == "16.66"){ echo 'selected';}?>>75% (3/4)</option>
                <option value="66" <?php if($percentage_full_price == "16.66"){ echo 'selected';}?>>66% (2/3)</option>
                <option value="50" <?php if($percentage_full_price == "50"){ echo 'selected';}?>>50% (1/2)</option>
                <option value="33" <?php if($percentage_full_price == "33"){ echo 'selected';}?>>33% (1/3)</option>
                <option value="25" <?php if($percentage_full_price == "25"){ echo 'selected';}?>>25% (1/4)</option>
                <option value="20" <?php if($percentage_full_price == "20"){ echo 'selected';}?>>20% (1/5)</option>
                <option value="16.66" <?php if($percentage_full_price == "16.66"){ echo 'selected';}?>>16,66% (1/6)</option>
                <option value="14.1428" <?php if($percentage_full_price == "16.66"){ echo 'selected';}?>>14,29% (1/7)</option>
            </select>
        </div>
    </div>
    <div class="col-md-9">
        <label>Egnet for</label>
        <div class="dynamic checkboxes in-row listeo_core-sports_list listeo_core-sports_list">  
            <?php 
            $sport_id_data = get_query_var( 'sport_id_data' );
           

            foreach ($sport_id_data as $key => $sports) {
                if(in_array($sports->id, $selected_sports)){
                    $checked = "checked";
                }else{
                   $checked = ""; 
                }
                echo '<input value="'.$sports->id.'" id="listing_sports_'.$sports->id.'_'.$sub_listing_id.'"  type="checkbox" name="sub_listing['.$sub_listing_id.'][_listing_sports][]" '.$checked.'><label id="label-in-listing_sports-'.$sports->id.'" for="listing_sports_'.$sports->id.'_'.$sub_listing_id.'">'.$sports->name.'</label>';
            }
            ?>
        </div>
    </div>
    
    
</div>
<style type="text/css">
    .form-field-_listing_sports-container{
        display: none;
    }
</style>