<?php


$current_user_id = get_current_user_ID();

/* user groups and users_and_users_groups*/
global $wpdb;

$get_app_fields = get_app_fields($active_group_id);





$get_app_fields = fieldstree($get_app_fields);


if (!$group_listings) {
	$group_listings = $wpdb->get_results("SELECT ID,post_title FROM {$wpdb->posts} WHERE post_type = 'listing' AND post_status='publish' AND users_groups_id=" . $active_group_id);
}



/* display row checkbox */
?>
<section class="fields_main">
	<div class="container main_user_manage">
		<div class="main_user_manage_row">
			<form method="post" class="edit_fields_form" action="<?php echo admin_url( 'admin-ajax.php' );?>">
				<input type="hidden" name="action" value="fields_update_action">
				<input type="hidden" name="active_group_id" value="<?php echo $active_group_id; ?>">
				<div class="row-data-filter user_cols_main">



					<div class="listing-outer">
						<div class="search-box-inner field-inner">
							<div class="dropdown">
								<button class="dropbtn" type="button" id="fields_modalbtn"><span class="filter_text"><?php echo __("Add new field", "gibbs"); ?></span></button>
								<button class="dropbtn" type="submit"><span class="filter_text"><?php echo __("Apply changes", "gibbs"); ?></span></button>
							</div>
							<?php
							$group_admin = get_group_admin();
							if($group_admin == ""){
								$group_admin = get_current_user_ID();
							}
							$field_btn_action = get_user_meta($group_admin,"field_btn_action",true);
							?>
							<div class="toggle">
								<b>Tillatt å legge til/fjerne felt  </b>
								<label class="switch">
								  <input type="checkbox" class="field_btn_action" <?php if($field_btn_action == "true"){ echo "checked";}?>>
								  <span class="slider round"></span>
								</label>
							</div>
						</div>
					</div>

					<!-- <div class="listing-outer_first">
					<div class="search-box-inner">
						<span class="user_icon"><i class="fa fa-user" aria-hidden="true"></i></span> <div class="dropdown">
							 <?php foreach ($user_group_data as $key => $groups1) { ?>
							 	<?php if ($groups1->id == $user_management_group_id) { ?>
	                                <button class="dropbtn"><?php echo $groups1->name; ?></button>
	                            <?php } ?>
	                        <?php } ?>
						  <div id="groupDropdown" class="dropdown-content">
						    <input type="text" placeholder="Search.." onkeyup="filterFunction(this)">
						    <?php foreach ($user_group_data as $key => $groups) { ?>
						    	<a class="group_drp <?php if ($groups->id == $user_management_group_id) {
														echo 'active';
													} ?>" href="javascript:void(0)" data-id="<?php echo $groups->id; ?>"><?php echo $groups->name; ?></a>
						    <?php } ?>
						  </div>
						</div>
					</div>
				</div> -->






				</div>
				<div class="user_table_content">
					<div class="table_div">

						<?php require("table.php"); ?>

					</div>
				</div>
			</form>
		</div>
		<?php
		require(__DIR__ . "/fields_modal.php");
		?>


		<script>
			jQuery(".select2-multiple-listings").select2({
					width: 'resolve'
				}

			)
		</script>
	</div>
</section>