<form method="post" id="listing_preview" style="display: none">
	<div class="row margin-bottom-30">
		<div class="col-md-12">
			
			<button type="submit" value="edit_listing" name="edit_listing"  class="button border margin-top-20"><i class="fa fa-edit"></i> <?php esc_attr_e( 'Edit listing', 'listeo_core' ); ?></button>
			<!-- <input type="submit" name="continue"> -->
			<button type="submit" value="<?php echo apply_filters( 'submit_listing_step_preview_submit_text', __( 'Submit Listing', 'listeo_core' ) ); ?>" name="continue"  class="button margin-top-20"><i class="fa fa-check"></i> 
				<?php 
				
		if(isset($_GET["action"]) && $_GET["action"] == 'edit' ) { esc_html_e('Save Changes','listeo_core'); } else { echo apply_filters( 'submit_listing_step_preview_submit_text', __( 'Submit Listing', 'listeo_core' ) ); } ?>
	</button>

			<input type="hidden" 	name="listing_id" value="<?php echo esc_attr( $data->listing_id ); ?>" />
			<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />
			<input type="hidden" 	name="listeo_core_form" value="<?php echo $data->form; ?>" />
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery("#listing_preview").find("button[name=continue]").click();
</script>