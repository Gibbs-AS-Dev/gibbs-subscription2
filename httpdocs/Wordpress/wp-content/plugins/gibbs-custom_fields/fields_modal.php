<!-- The Modal -->
<?php
$get_app_fields = get_app_fields($active_group_id);

$field_widths = array("col-md-12" => "100% bredde", "col-md-6" => "50% bredde", "col-md-4" => "33% bredde");
$type_selects = array("with_search" => "Dropdown med søk", "without_search" => "Dropdown uten søk");
$yes_no_dropdown = array("0" => "Nei", "1" => "Ja");
$field_positions = array("reservation" => "Reservasjon","about" => "Om søker", "application" => "Søknad",  "dont_show" => "Ikke vis i skjema");
?>

<div id="fieldsModal" class="modal user_group_modal create_fields">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php echo __("Create custom field", "Gibbs"); ?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form method="post" class="fields_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
        <input type="hidden" name="action" value="fields_create_action">
        <input type="hidden" name="active_group_id" value="<?php echo $active_group_id; ?>">

        <div class="field-card">
          <div class="row">
            <div class="form-group col-md-4">
              <label><?php echo __("Field Type", "gibbs"); ?> *</label>
              <select class="field_type" name="field_type" required>
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="select">Select</option>
                <option value="checkbox">Checkbox</option>
                <!-- <option value="radio">Radio</option> -->
              </select>
            </div>
            <div class="form-group col-md-4">
              <label><?php echo __("Field label", "gibbs"); ?> *</label>
              <input class="form-control field_label" name="field_label" type="text" placeholder="<?php echo __("Enter field label", "gibbs"); ?>" required>
            </div>
            <div class="form-group col-md-4">
              <label><?php echo __("Field name", "gibbs"); ?> *</label>
              <input class="form-control field_name" name="field_name" type="text" placeholder="<?php echo __("Enter field name", "gibbs"); ?>" required readonly>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <label><?php echo __("Field Width", "gibbs"); ?></label>
              <select name="field_width" required>
                <?php foreach ($field_widths as $key_width => $field_widths) { ?>
                  <option value="<?php echo $key_width; ?>"><?php echo $field_widths; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-md-4 select_field" style="display: none">
              <label><?php echo __("Type of dropdown", "gibbs"); ?></label>
              <select name="type_select" required>
                <?php foreach ($type_selects as $key_type_select => $type_select) { ?>
                  <option value="<?php echo $key_type_select; ?>"><?php echo $type_select; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-md-4 number_field" style="display: none">
              <label><?php echo __("Enter max amount", "gibbs"); ?></label>
              <input class="form-control" name="max_input_number" type="text" placeholder="<?php echo __("Enter max amount", "gibbs"); ?>">
            </div>

          </div>
          <div class="row">
            <div class="form-group col-md-12">
              <label><?php echo __("Tooltip", "gibbs"); ?> *</label>
              <textarea class="form-control tooltip" name="tooltip" placeholder="<?php echo __("Enter tooltip text", "gibbs"); ?>"></textarea>
            </div>
          </div>
          <div class="row option_fields" style="display: none">
            <div class="form-group col-md-12">
              <label><?php echo __("Options", "gibbs"); ?> *</label>
              <textarea class="form-control field_options" name="field_options" type="text" placeholder="<?php echo __("Enter  value with `,` seperated. For ex. blue,red,green etc", "gibbs"); ?>"></textarea>
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
            <div class="form-group col-md-6 select_field show_in_calendar" style="display: none">
              <label><?php echo __("Can this field be selected multiple times?", "gibbs"); ?></label>
              <select class="select_multiple" name="select_multiple" required>
                <?php foreach ($yes_no_dropdown as $key_yes_no_drop => $yes_no_drop) { ?>
                  <option value="<?php echo $key_yes_no_drop; ?>"><?php echo $yes_no_drop; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label><?php echo __("Is this a required field?", "gibbs"); ?></label>
              <select name="field_required" required>
                <?php foreach ($yes_no_dropdown as $key_yes_no_drop => $yes_no_drop) { ?>
                  <option value="<?php echo $key_yes_no_drop; ?>" <?php if ($key_yes_no_drop == "1") {
                                                                    echo "selected";
                                                                  } ?>><?php echo $yes_no_drop; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-md-6 parent_exist">
              <label><?php echo __("Show in booking summery?", "gibbs"); ?></label>
              <select name="show_in_booking_summery" required>
                <?php foreach ($yes_no_dropdown as $key_yes_no_drop => $yes_no_drop) { ?>
                  <option value="<?php echo $key_yes_no_drop; ?>" <?php if ($key_yes_no_drop == "1") {
                                                                    echo "selected";
                                                                  } ?>><?php echo $yes_no_drop; ?></option>
                <?php } ?>
              </select>
            </div>

          </div>
          <div class="row">

          <!-- <div class="form-group col-md-12">
              <label><?php echo __("Velg hvor feltet skal vises", "gibbs"); ?></label>
              <select class="select2-multiple-listings col-md-8" name="listings[]" multiple="multiple">
                <?php
                foreach ($group_listings as  $listing) { ?>
                  <option value="<?php echo $listing->ID; ?>" selected><?php echo $listing->post_title; ?></option>
                <?php } ?>
              </select>
            </div> -->

            <div class="form-group col-md-6 parent_exist show_in_calendar">
              <label><?php echo __("Show in calender?", "gibbs"); ?></label>
              <select name="show_in_calender" required>
                <?php foreach ($yes_no_dropdown as $key_yes_no_drop => $yes_no_drop) { ?>
                  <option value="<?php echo $key_yes_no_drop; ?>" <?php if ($key_yes_no_drop == "1") {
                                                                    echo "selected";
                                                                  } ?>><?php echo $yes_no_drop; ?></option>
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
              <select class="param_algo" name="param_algo" required>
                <?php foreach ($yes_no_dropdown as $key_yes_no_drop => $yes_no_drop) { ?>
                  <option value="<?php echo $key_yes_no_drop; ?>" <?php if ($key_yes_no_drop == "1") {
                                                                    echo "selected";
                                                                  } ?>><?php echo $yes_no_drop; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-md-6 parent_exist">
              <label><?php echo __("where in application form?", "gibbs"); ?></label>
              <select name="field_position" required>
                <?php foreach ($field_positions as $key_field_position => $field_position) { ?>
                  <option value="<?php echo $key_field_position; ?>"><?php echo $field_position; ?></option>
                <?php } ?>
              </select>
            </div>

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
  <!--       <div class="field-card">
          <div class="row">
            <div class="form-group col-md-12">
              <h2><?php echo __("Booking summery", "gibbs"); ?></h2>
              <hr style="margin-bottom: 18px;" />
            </div>
          </div>
          <div class="row">
 -->
         

       


          </div>
        </div>
        <div class="row" style="display:none;">
          <div class="form-group col-md-12">
            <input class="form-control parent_field" name="parent_field" type="hidden" required readonly>
          </div>
        </div>
      
      </form>
    </div>
  </div>

</div>

<script type="text/javascript">
  // Get the modal
  //var team_sizeModal = document.getElementById("team_sizeModal");
  var fieldsModal = document.getElementById("fieldsModal");

  //var team_sizebtn = document.getElementById("team_size");

  // Get the button that opens the modal
  var fields_modalbtn = document.getElementById("fields_modalbtn");

  // Get the <span> element that closes the modal
  //var span = document.getElementsByClassName("close")[0];
  var close_user = document.getElementsByClassName("close_user")[0];

  // When the user clicks the button, open the modal 
  /*team_sizebtn.onclick = function() {
    team_sizeModal.style.display = "block";
  }*/
  fields_modalbtn.onclick = function() {
    jQuery(".create_fields").find(".parent_field").val("");
    jQuery(".create_fields").find(".parent_exist").show();
    jQuery(".create_fields").find(".select_multiple").change();
    fieldsModal.style.display = "block";
  }
  jQuery(".close_user").click(function() {
    jQuery("#fieldsModal").hide();
  })
  // When the user clicks on <span> (x), close the modal
  /*span.onclick = function() {
    team_sizeModal.style.display = "none";
  }*/
  close_user.onclick = function() {
    fieldsModal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == fieldsModal) {
      fieldsModal.style.display = "none";
    }
  }
  jQuery(document).on("change", ".field_type", function() {

    if (this.value == "checkbox" || this.value == "select") {
      jQuery(".create_fields").find(".option_fields").show();
      jQuery(".create_fields").find(".field_options").attr("required", "true");


    } else {
      jQuery(".create_fields").find(".option_fields").hide();
      jQuery(".create_fields").find(".field_options").removeAttr("required");
    }

    if (this.value == "select") {
      jQuery(".create_fields").find(".select_field").show();
    } else {
      jQuery(".create_fields").find(".select_field").hide();
    }
    if (this.value == "number") {
      jQuery(".create_fields").find(".number_field").show();
    } else {
      jQuery(".create_fields").find(".number_field").hide();
    }

  })
  jQuery(document).on("keyup", ".field_label", function() {
    var newString = "";
    if (this.value != "") {
      newString = this.value.replace(/[^A-Z0-9]+/ig, "-");
      newString = newString.toLowerCase();
    }

    jQuery(".create_fields").find(".field_name").val(newString);


  })

  jQuery(".create_fields").find(".select_multiple").change(function() {
    if (this.value == "1") {
      jQuery(".create_fields").find(".param_algo").find("option[value=0]").prop("selected", true);
      jQuery(".create_fields").find(".param_algo").find("option[value=1]").prop("disabled", true);
    } else {
      jQuery(".create_fields").find(".param_algo").find("option[value=1]").prop("disabled", false);
    }
  })
  /*
  jQuery(".user_register_form").submit(function(e){

      e.preventDefault();
      jQuery(".user_register_form").find("button").prop("disabled",true);

      var formdata = jQuery(this).serialize();

      jQuery.ajax({
          type: "POST",
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
          data: formdata,
          dataType: 'json',
          success: function (data) {
            if(data.error == 1){
               jQuery(".user_register_form").find("button").prop("disabled",false);
               jQuery(".alert_error_message").show();
               jQuery(".alert_error_message").html(data.message);

            }else{

              if(data.not_user_already != undefined){
                   
                   jQuery("input[name=not_user_already]").val("1");

                   jQuery(".user_divs").show();
                   jQuery(".user_divs").find("input").attr("required",true)



              }else{
                jQuery(".alert_success_message").show();
                jQuery(".alert_success_message").html(data.message);

                setTimeout(function(){
                    jQuery(".alert_error_message").hide();
                    jQuery(".alert_error_message").html("");
                    window.location.reload();
                },4000);

              }
              

            }
          }
      });
  })*/
</script>