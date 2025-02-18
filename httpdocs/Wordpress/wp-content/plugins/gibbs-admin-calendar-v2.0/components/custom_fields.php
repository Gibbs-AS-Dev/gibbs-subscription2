<?php
    $fields_rows = array();

    $exist_fields = true;

    if(function_exists('advanced_fields')){
        global $wpdb;
        $listings_table =$wpdb->prefix. 'posts';
        $listings = $wpdb->get_row("SELECT users_groups_id FROM $listings_table WHERE ID=".$booking->listing_id);
        $group_id = $listings->users_groups_id; 
        if($group_id != ""){
          foreach ($field_datas as $key_index => $field_data) {
            $fields_rows[] = advanced_fields(0,$group_id,0,$field_data,$key_index,true,"calender");
          }
        }
    }
    
    if(empty($fields_rows)){

        $fields_rowssss = advanced_fields(0,$group_id,0,array(),0,true,"calender");

        if(!empty($fields_rowssss)){

           $fields_rows[] = $fields_rowssss;

        }else{
           $exist_fields = false;
        }
    }
    $lables = get_lables($group_id);

    if(!empty($field_datas) || $exist_fields == true){

        if(isset($season_view_exist) && $season_view_exist == true){
             $exist_fields = false;
        }


$group_admin = get_group_admin();
if($group_admin == ""){
    $group_admin = get_current_user_ID();
}
$field_btn_action = get_user_meta($group_admin,"field_btn_action",true);
if($field_btn_action == "false" || $field_btn_action == ""){
?>
<style type="text/css">
    .delete_field_div, .add_field_btn{
        display: none !important;
    }
</style>
<?php } ?>   
<div class="row">
    <div class="col-md-12">
        <div class="outer-form-tabs">
            <?php if($info_data == false){ ?>
                <div class="header_title">
                    <h4>Annen informasjon</h4>
                    <?php if($exist_fields == true ) { ?>
                        <div class="right-div">
                            <div class="edit_field_icon edit_fieldmodal_btn<?php echo $booking->id;?>"">
                                
                                    <i class="fa fa-edit"></i> 

                            </div>
                            <div class="calender_fields_modal"><?php require("edit_fieldmodal.php");?> </div> 
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="row fields_divv">

                <?php foreach ($field_datas as $key => $field_data) { ?>
                    <div class="col-md-12 fields_divv_inner">
                        <?php foreach ($field_data as $key_fields => $field) { 
                            

                            if(isset($lables[$key_fields])){
                                 $key_fields = $lables[$key_fields];
                            }else{

                               $key_fields = str_replace("_", " ", $key_fields);
                               $key_fields = str_replace("-", " ", $key_fields);

                            }

                            ?>
                                <div class="inner_fieldd"><div class="inner-inner-div"><b><?php echo $key_fields;?>:</b> <span> <?php echo $field;?></span></div></div>
                        <?php } ?>
                    </div>
                <?php } ?>
                
                
            </div>
          </div>
    </div>

</div>
<?php }
if(!empty($about_fields_data)){

 ?>

<div class="row">
    <div class="col-md-12">

        <div class="outer-form-tabs">
            <div class="header_title">
                <h4>About section fields</h4>
            </div>
            <div class="row fields_divv">

                <div class="col-md-12 fields_divv_inner">

                    <?php foreach ($about_fields_data as $key_about_fields => $field_about) {
                        $key_about_fields = str_replace("_", " ", $key_about_fields);
                        $key_about_fields = str_replace("-", " ", $key_about_fields);
                     ?>
                       
                        <div class="inner_fieldd"><div class="inner-inner-div"><b><?php echo $key_about_fields;?>:</b> <span> <?php echo $field_about;?></span></div></div>
                        
                    <?php } ?>
                </div>
                
                
            </div>
        </div>
    </div>

</div> 
<?php } ?>
<?php 
if(!empty($app_field_datas)){
 ?>

<div class="row">
    <div class="col-md-12">
        <div class="outer-form-tabs">
            <div class="header_title">
                <h4>Søker informasjon</h4>
            </div>
            <div class="row fields_divv">

                <div class="col-md-12 fields_divv_inner">

                    <?php foreach ($app_field_datas as $key_app_fields => $field_app) { 
                        $key_app_fields = str_replace("_", " ", $key_app_fields);
                        $key_app_fields = str_replace("-", " ", $key_app_fields);
                     ?>
                       
                        <div class="inner_fieldd"><div class="inner-inner-div"><b><?php echo $key_app_fields;?>:</b> <span> <?php echo $field_app;?></span></div></div>
                        
                    <?php } ?>
                </div>
                
                
            </div>
        </div>
    </div>

</div> 
<?php } ?>