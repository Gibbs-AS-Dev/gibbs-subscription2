<?php
$duration_score_rules_sql = "SELECT * from `duration_score_rules` where users_groups_id ='$group_slected_id'";
$duration_score_rules_data = $wpdb->get_results($duration_score_rules_sql);
$duration_score_rules = array();
if(!empty($duration_score_rules_data)){
  $duration_score_rules['score_less_10'] = $duration_score_rules_data[0]->score_less_10;
  $duration_score_rules['score_10_20'] = $duration_score_rules_data[0]->score_10_20;
  $duration_score_rules['score_20_30'] = $duration_score_rules_data[0]->score_20_30;
  $duration_score_rules['score_30_40'] = $duration_score_rules_data[0]->score_30_40;
  $duration_score_rules['score_40_50'] = $duration_score_rules_data[0]->score_40_50;
  $duration_score_rules['score_50_60'] = $duration_score_rules_data[0]->score_50_60;
  $duration_score_rules['score_60_70'] = $duration_score_rules_data[0]->score_60_70;
  $duration_score_rules['score_70_80'] = $duration_score_rules_data[0]->score_70_80;
  $duration_score_rules['score_80_90'] = $duration_score_rules_data[0]->score_80_90;
  $duration_score_rules['score_more_90'] = $duration_score_rules_data[0]->score_more_90;

  $duration_score_rules_desc['score_less_10'] = ' '.__("Mindre enn 10","Gibbs").' ';
  $duration_score_rules_desc['score_10_20'] = ' '.__("Mellom 10 og 20","Gibbs").' ';
  $duration_score_rules_desc['score_20_30'] = ' '.__("Mellom 20 og 30","Gibbs").' ';
  $duration_score_rules_desc['score_30_40'] = ' '.__("Mellom 30 og 40","Gibbs").' ';
  $duration_score_rules_desc['score_40_50'] = ' '.__("Mellom 40 og 50","Gibbs").' ';
  $duration_score_rules_desc['score_50_60'] = ' '.__("Mellom 50 og 60","Gibbs").' ';
  $duration_score_rules_desc['score_60_70'] = ' '.__("Mellom 60 og 70","Gibbs").' ';
  $duration_score_rules_desc['score_70_80'] = ' '.__("Mellom 70 og 80 ","Gibbs").' ';
  $duration_score_rules_desc['score_80_90'] = ' '.__("Mellom 80 og 90","Gibbs").' ';
  $duration_score_rules_desc['score_more_90'] = ' '.__("Mer enn 90","Gibbs").' ';
}
?>
<form method="POST" class="gender_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
      <input type="hidden" name="action" value="update_duration">
      <input  name="users_groups_id" type="hidden" value="<?php echo $group_slected_id;?>" required="">
      <div class="row panel panel-default">
          <div class="panel-heading">
             <div class="row">
                <div class="col-md-10">
                     <h3><?php  echo __("Tildeling av tider til poenggrupper","Gibbs");?><br>
                        <small><?php  echo __("Bestem hvor mye tid enhver poenggruppe kan få av maksimal treningstid. Intervallene er 30, 60, 90, 120min osv.","Gibbs");?></small>
                     </h3>
                </div>
                <div class="col-md-2 btn-plus">
                      <?php if(count($duration_score_rules) > 0){ ?>
                        <button class="btn btn-primary"><?php  echo __("Save","Gibbs");?></button>
                      <?php } ?>
                      <?php if(count($get_user_group_data) > 0){ ?>
                        <button type="button" class="btn btn-info btn-add" id="duration"><i class="fa fa-plus"></i></button>
                      <?php } ?>
                </div>
              </div>
          </div>
 
          
          <?php  foreach ($duration_score_rules as $duration_score_rules_key => $duration_score_rules_value) { ?>
          
            <div class="form-group col-sm-4">

              <label class="rules_name1"><?php echo $duration_score_rules_desc[$duration_score_rules_key];?></label>
              <select class="form-control" name="duration[<?php echo $duration_score_rules_key;?>]">
                  <option value=""><?php  echo __("Select","Gibbs");?></option>
                  <?php for($i = 1;$i <=200;$i++ ){ ?>
                    <option value="<?php echo ($i*30);?>" <?php if($duration_score_rules_value == ($i*30)){echo 'selected';}?>><?php echo ($i*30);?></option>
                  <?php } ?>
              </select>
              

            </div>

          <?php } ?> 


       
        
      </div>

     
  </form> 


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- end -->
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('.advanced_column').select2();
});

jQuery("#duration").click(function(){

    jQuery(".delete_form").remove();
    

    var f = document.createElement("form");
    f.setAttribute('class',"delete_form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

    var a_i1 = document.createElement("input"); //input element, text
    a_i1.setAttribute('type',"hidden");
    a_i1.setAttribute('name',"users_groups_id");
    a_i1.setAttribute('value',"<?php echo $group_slected_id;?>");
    f.appendChild(a_i1);


    var a_i = document.createElement("input"); //input element, text
    a_i.setAttribute('type',"hidden");
    a_i.setAttribute('name',"action");
    a_i.setAttribute('value',"add_duration");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
})
</script>