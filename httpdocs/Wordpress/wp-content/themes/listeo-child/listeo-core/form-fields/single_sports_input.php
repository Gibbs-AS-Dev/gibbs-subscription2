<?php 
$sport_id_data = get_query_var( 'sport_id_data' );


foreach ($sport_id_data as $key => $sports) {
    if(in_array($sports->id, $selected_sports)){
        $checked = "checked";
    }else{
       $checked = ""; 
    }
    echo '<input value="'.$sports->id.'" id="listing_sports_'.$sports->id.'"  type="checkbox" name="_listing_sports[]" '.$checked.'><label id="label-in-listing_sports-'.$sports->id.'" for="listing_sports_'.$sports->id.'">'.$sports->name.'</label>';
}
?>