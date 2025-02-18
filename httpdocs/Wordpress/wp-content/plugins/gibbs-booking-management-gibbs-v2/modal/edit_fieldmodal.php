<!-- The Modal -->
<?php
//global $wpdb;

/*$fields_rows = array();

if(function_exists('advanced_fields')){
    $listings_table =$wpdb->prefix. 'posts';
    $listings = $wpdb->get_row("SELECT users_groups_id FROM $listings_table WHERE ID=".$booking->listing_id);
    $group_id = $listings->users_groups_id; 
    if($group_id != ""){
      foreach ($custom_field_data_sets as $key_index => $field_data) {
        $fields_rows[] = advanced_fields(0,$group_id,0,$field_data,$key_index,true);
      }
    }
}*/
//echo "<pre>"; print_r($fields_rows); die;
//
?>

<div id="edit_fieldmodal<?php echo $data->id; ?>" class="modal message_modal edit_fieldmodal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <!-- <h2><?php echo __("Rask redigering", "Gibbs"); ?></h2> -->
    </div>
    <div class="modal-body">
      <form method="post" class="fields_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
        <input type="hidden" name="action" value="edit_field_save">
        <input type="hidden" name="booking_id" value="<?php echo $data->id; ?>">
        <div id="edit_form_div">

          <div class="row" style="padding: 15px 15px 5px 15px;background: white;">

            <?php
            if (!empty($fields_rows)) {
              echo repeated_fields($fields_rows, $group_id, $data->listing_id);
            }
            ?>



          </div>

          <div class="row" style="background: white;">
            <div class="col-xs-6 col-md-6" style="padding:20px; text-align: center;">

            </div>
            <div class="col-xs-6 col-md-6" style="padding: 20px; text-align: center;">
              <button type="button" class="button closebtn">Lukk</button>
              <button type="button" class="button fields_save">Bruk</button>
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>

</div>

<script type="text/javascript">
  jQuery(document).on("click", ".edit_fieldmodal_btn<?php echo $data->id; ?>", function() {

    jQuery("#edit_fieldmodal<?php echo $data->id; ?>").show();
  })

  jQuery(document).on("click", ".close_modal,.closebtn", function() {
    jQuery("#edit_fieldmodal<?php echo $data->id; ?>").hide();
  })

  jQuery(document).on('submit', '.edit_form<?php echo $data->id; ?>', function(e) {
    e.preventDefault();

    jQuery(".booking_datatable").addClass("loading_class");

    var formdata = jQuery(this).serialize();


    jQuery.ajax({
      type: 'POST',
      dataType: 'json',
      url: "<?php echo admin_url( 'admin-ajax.php' );?>",
      data: formdata,
      success: function(data) {
        window.location.reload()
      }
    });
  });

  jQuery(".fields_save").click(function(e) {
    e.preventDefault();

    jQuery(".empty_div").removeClass("empty_div");

    let error = false;
    jQuery(".fields_form").find(".required").each(function() {

      if (this.value == "") {
        jQuery(this).focus();
        jQuery(this).addClass("empty_div");
        jQuery(this).parent().find(".select2-container").addClass("empty_div");
        error = true;

      }
    })
    jQuery(".fields_form").find("input[type=checkbox]").each(function() {

      if (jQuery(this).hasClass("required")) {
        if (this.checked == false) {
          jQuery(this).focus();
          jQuery(this).addClass("empty_div_checkbox");
          error = true;
          return false;
        }
      }
    })

    if (error == false) {
      jQuery(".fields_form").submit();
    }
  })
</script>