<?php echo fieldstable($get_app_fields); ?>

<script type="text/javascript">
	jQuery(document).on("click", ".edit_field", function() {
		var modal = jQuery(this).attr("data-modal");

		jQuery("#" + modal).show();
	})
	jQuery(".close_user").click(function() {
		jQuery(".user_group_modal").hide();
	})
	jQuery(document).ready(function() {

		jQuery(".field_btn_action").change(function(){
			let field_btn_action =  "false";

			if(this.checked == true){
				field_btn_action = "true";
			}

			jQuery.ajax({
		        type: "POST",
		        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
		        data: {action:"save_field_btn_action","field_btn_action":field_btn_action},
		        dataType: 'json',
		        success: function (data) {

		        }
		    });
		})

		var $tabs = jQuery('.table')
		jQuery(".table:first tbody").sortable({
			items: '> tr',
			cursor: 'pointer',
			axis: 'y',
			dropOnEmpty: false,
			start: function(e, ui) {
				ui.item.addClass("selected");
			},
			stop: function(e, ui) {
				ui.item.removeClass("selected");
				jQuery(this).find("tr").each(function(index) {

					if (index > 0) {
						//jQuery(this).find("td").eq(2).html(index);
					}
				});
			}
		});


	});

	jQuery(document).on("click", ".add_field", function() {
		var pr_field = jQuery(this).attr("data-parent");
		jQuery(".create_fields").find(".parent_field").val(pr_field);
		jQuery(".create_fields").find(".parent_exist").hide();
		jQuery(".create_fields").show();
	})
	jQuery(document).on("click", ".delete_field", function() {
		var pr_field = jQuery(this).attr("data-name");
		jQuery(pr_field).find(".status_field").val("0");
		jQuery(".edit_fields_form").submit();
	})
	jQuery(document).on("click", ".show_field", function() {
		var pr_field = jQuery(this).attr("data-name");
		jQuery(pr_field).find(".status_field").val("1");
		jQuery(".edit_fields_form").submit();
	})
</script>