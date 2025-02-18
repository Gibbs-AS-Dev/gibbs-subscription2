<!-- The Modal -->
<?php

$active_group_id = get_user_meta(get_current_user_ID(), '_gibbs_active_group_id', true);
$get_app_fields = get_app_fields($active_group_id);

$field_typess = array("text" => "Tekst", "number" => "Tall", "select" => "Valg alternativ", "checkbox" => "Kryss av boks");
$field_widths = array("col-md-12" => "100% bredde", "col-md-6" => "50% bredde", "col-md-4" => "33% bredde");
$type_selects = array("with_search" => "Dropdown med søk", "without_search" => "Dropdown uten søk");
$yes_no_dropdown = array("0" => "Nei", "1" => "Ja");
$field_positions = array("reservation" => "Reservasjon", "about" => "Om søker", "application" => "Søknad", "dont_show" => "Ikke vis i søknadsskjema");
?>

<div id="fieldsModal<?php echo $get_app_field->name; ?>" class="modal user_group_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php echo __("General", "Gibbs"); ?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <input type="hidden" class="status_field" name="fields[<?php echo $get_app_field->name; ?>][status]" value="<?php echo $get_app_field->status; ?>">
      <div class="field-card">
        <div class="row">
          <div class="form-group col-md-6">
            <label><?php echo __("Field Type", "gibbs"); ?> *</label>
            <select class="field_type" name="fields[<?php echo $get_app_field->name; ?>][field_type]" required>
              <?php foreach ($field_typess as $key_type => $field_typ) { ?>
                <option value="<?php echo $key_type; ?>" <?php if ($get_app_field->type == $key_type) { ?> selected <?php } ?>><?php echo $field_typ; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label><?php echo __("Field label", "gibbs"); ?> *</label>
            <input class="form-control field_label" name="fields[<?php echo $get_app_field->name; ?>][field_label]" type="text" value="<?php echo $get_app_field->label; ?>" placeholder="<?php echo __("Enter field label", "gibbs"); ?>" required>
          </div>
          <div class="form-group col-md-4" style="display: none">
            <label><?php echo __("Field name", "gibbs"); ?> *</label>
            <input class="form-control field_name" name="fields[<?php echo $get_app_field->name; ?>][field_name]" type="text" value="<?php echo $get_app_field->name; ?>" placeholder="<?php echo __("Enter field name", "gibbs"); ?>" required readonly>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-4">
            <label><?php echo __("Field Width", "gibbs"); ?></label>
            <select name="fields[<?php echo $get_app_field->name; ?>][field_width]" required>
              <?php foreach ($field_widths as $key_width => $field_widths) { ?>
                <option value="<?php echo $key_width; ?>" <?php if ($get_app_field->field_width == $key_width) { ?> selected <?php } ?>><?php echo $field_widths; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-4 select_field" <?php if ($get_app_field->type == "select") {
                                                        } else { ?>style="display: none" <?php } ?>>
            <label><?php echo __("Type of dropdown", "gibbs"); ?></label>
            <select name="fields[<?php echo $get_app_field->name; ?>][type_select]" required>
              <?php foreach ($type_selects as $key_type_select => $type_select) { ?>
                <option value="<?php echo $key_type_select; ?>" <?php if ($get_app_field->type_select == $key_type_select) { ?> selected <?php } ?>><?php echo $type_select; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-4 number_field" <?php if ($get_app_field->type == "number") {
                                                        } else { ?>style="display: none" <?php } ?>>
            <label><?php echo __("Enter max amount", "gibbs"); ?></label>
            <input class="form-control" name="fields[<?php echo $get_app_field->name; ?>][max_input_number]" value="<?php echo $get_app_field->max_input_number; ?>" type="text" placeholder="<?php echo __("Enter max amount", "gibbs"); ?>">
          </div>

        </div>
        <div class="row">
          <div class="form-group col-md-12">
            <label><?php echo __("Tooltip", "gibbs"); ?> *</label>
            <textarea class="form-control tooltip" name="fields[<?php echo $get_app_field->name; ?>][tooltip]" placeholder="<?php echo __("Enter tooltip text", "gibbs"); ?>"><?php echo $get_app_field->tooltip; ?></textarea>
          </div>
        </div>
        <div class="row option_fields" <?php if ($get_app_field->type == "checkbox" || $get_app_field->type == "select") {
                                        } else { ?>style="display: none" <?php } ?>>
          <div class="form-group col-md-12">
            <label><?php echo __("Options", "gibbs"); ?> *</label>
            <textarea class="form-control" name="fields[<?php echo $get_app_field->name; ?>][field_options]" type="text" placeholder="<?php echo __("Enter  value with `,` seperated. For ex. blue,red,green etc", "gibbs"); ?>" <?php if ($get_app_field->type == "checkbox" || $get_app_field->type == "select") { ?> required <?php } ?>><?php echo $get_app_field->field_options; ?></textarea>
          </div>
        </div>

      </div>
      <div class="field-card">
        <div class="row">
          <div class="form-group col-md-12">
            <h2><?php echo __("Advanced", "gibbs"); ?></h2>
            <hr style="margin-bottom: 18px;" />
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-6 select_field show_in_calendar" <?php if ($get_app_field->type == "select") {
                                                        } else { ?>style="display: none" <?php } ?>>
            <label><?php echo __("Can this field be selected multiple times?", "gibbs"); ?></label>
            <select class="select_multiple" name="fields[<?php echo $get_app_field->name; ?>][select_multiple]" required>
              <?php foreach ($yes_no_dropdown as $key_select_multiple => $select_multiple) { ?>
                <option value="<?php echo $key_select_multiple; ?>" <?php if ($get_app_field->multiple == $key_select_multiple) { ?> selected <?php } ?>><?php echo $select_multiple; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label><?php echo __("Is this a required field?", "gibbs"); ?></label>
            <select name="fields[<?php echo $get_app_field->name; ?>][field_required]" required>
              <?php foreach ($yes_no_dropdown as $key_field_required => $field_required) { ?>
                <option value="<?php echo $key_field_required; ?>" <?php if ($get_app_field->required == $key_field_required) { ?> selected <?php } ?>><?php echo $field_required; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="row">
          <div class="form-group col-md-6" <?php if ($get_app_field->parent_field != "") { ?> style="display: none;" <?php } ?>>
            <label><?php echo __("Show in booking summery?", "gibbs"); ?></label>
            <select name="fields[<?php echo $get_app_field->name; ?>][show_in_booking_summery]" required>
              <?php foreach ($yes_no_dropdown as $key_show_in_booking_summery => $show_in_booking_summery) { ?>
                <option value="<?php echo $key_show_in_booking_summery; ?>" <?php if ($get_app_field->show_in_booking_summery == $key_show_in_booking_summery) { ?> selected <?php } ?>><?php echo $show_in_booking_summery; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>

      <!--   <div class="form-group col-md-12">
            <label><?php echo __("Velg hvor feltet skal vises", "gibbs"); ?></label>
            <select class="select2-multiple-listings col-md-8" name="fields[<?php echo $get_app_field->name; ?>][listings][]" multiple="multiple">
              <?php
              foreach ($group_listings as  $listing) { ?>
                <option value="<?php echo $listing->ID; ?>" <?php if (in_array($listing->ID, $get_app_field->listings)) { ?> selected <?php } ?>><?php echo $listing->post_title; ?></option>
              <?php } ?>
            </select>
          </div> -->
    
          <div class="form-group col-md-6 show_in_calendar" <?php if ($get_app_field->parent_field != "") { ?> style="display: none;" <?php } ?>>
            <label><?php echo __("Show in calender?", "gibbs"); ?></label>
            <select name="fields[<?php echo $get_app_field->name; ?>][show_in_calender]" required>
              <?php foreach ($yes_no_dropdown as $key_show_in_calender => $show_in_calender) { ?>
                <option value="<?php echo $key_show_in_calender; ?>" <?php if ($get_app_field->show_in_calender == $key_show_in_calender) { ?> selected <?php } ?>><?php echo $show_in_calender; ?></option>
              <?php } ?>
            </select>
          </div>

        </div>
      </div>
      <div class="field-card">
        <div class="row">
          <div class="form-group col-md-12">
            <h2><?php echo __("Seasonal booking", "gibbs"); ?></h2>
            <hr style="margin-bottom: 18px;" />
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-6 show_in_calendar">
            <label><?php echo __("Should this field be a paramater for the algorithm?", "gibbs"); ?></label>
            <select name="fields[<?php echo $get_app_field->name; ?>][param_algo]" class="param_algo" required>
              <?php foreach ($yes_no_dropdown as $key_param_algo => $param_algo) {
                $disabled = "";
                if ($get_app_field->required == 1) {
                  if ($key_param_algo == "1") {
                    $disabled = "disabled";
                  }
                }
              ?>
                <option value="<?php echo $key_param_algo; ?>" <?php if ($get_app_field->param_algo == $key_param_algo) { ?> selected <?php } ?> <?php echo $disabled; ?>><?php echo $param_algo; ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group col-md-6" <?php if ($get_app_field->parent_field != "") { ?> style="display: none;" <?php } ?>>
            <label><?php echo __("where in application form?", "gibbs"); ?></label>
            <select name="fields[<?php echo $get_app_field->name; ?>][field_position]" required>
              <?php foreach ($field_positions as $key_field_position => $field_position) { ?>
                <option value="<?php echo $key_field_position; ?>" <?php if ($get_app_field->field_position == $key_field_position) { ?> selected <?php } ?>><?php echo $field_position; ?></option>
              <?php } ?>
            </select>
          </div>


         

        </div>
      </div>
      <div class="row" style="display:none;">
        <div class="form-group col-md-12">
          <input class="form-control parent_field" name="fields[<?php echo $get_app_field->name; ?>][parent_field]" value="<?php echo $get_app_field->parent_field; ?>" type="hidden" required readonly>
        </div>
      </div>
      <div class="field-card">
        <div class="row">
          <div class="form-group col-md-12 field_btn_submit age_btn_submit_flex justify-content-flex-end">
            <button class="form-control1 close_usergroup close_user close_usergroup_btn" type="button"><?php echo __("Cancel", "Gibbs"); ?></button>
            <button class="btn btn-primary" type="submit"><?php echo __("Save", "Gibbs"); ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script type="text/javascript">
  jQuery(document).on("change", "#fieldsModal<?php echo $get_app_field->name; ?> .field_type", function() {


    if (this.value == "checkbox" || this.value == "select") {
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".option_fields").show();
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".field_options").attr("required", "true");
    } else {
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".option_fields").hide();
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".field_options").removeAttr("required");
    }
    if (this.value == "select") {
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".select_field").show();
    } else {
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".select_field").hide();
    }
    if (this.value == "number") {
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".number_field").show();
    } else {
      jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".number_field").hide();
    }


  })

  jQuery(document).ready(function() {

    jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".select_multiple").change(function() {
      if (this.value == "1") {
        jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".param_algo").find("option[value=0]").prop("selected", true);
        jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".param_algo").find("option[value=1]").prop("disabled", true);
      } else {
        jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".param_algo").find("option[value=1]").prop("disabled", false);
      }
    })
  })
  /*jQuery(document).on("keyup","#fieldsModal<?php echo $get_app_field->name; ?> .field_label",function(){
      var newString = "";
      if(this.value != ""){
          newString  = this.value.replace(/[^A-Z0-9]+/ig, "-");
          newString  = newString.toLowerCase();
      }

     jQuery("#fieldsModal<?php echo $get_app_field->name; ?>").find(".field_name").val(newString);
   
     
  })*/
</script>