<div class="sort-by">
	<div class="sort-by-select">
		<?php $default = isset( $_GET['listeo_core_order'] ) ? (string) $_GET['listeo_core_order']  : get_option( 'listeo_sort_by','date' ); ?>
		<select form="listeo_core-search-form" name="listeo_core_order" data-placeholder="<?php esc_attr_e('Default order', 'listeo_core'); ?>" class="select2-single orderby" >
			<option <?php selected($default,'default'); ?> value="default"><?php esc_html_e( 'Default Order' , 'listeo_core' ); ?></option>	
			<option <?php selected($default,'highest-rated'); ?> value="highest-rated"><?php esc_html_e( 'Highest Rated' , 'listeo_core' ); ?></option>
			<option <?php selected($default,'reviewed'); ?> value="reviewed"><?php esc_html_e( 'Most Reviewed' , 'listeo_core' ); ?></option>
			<option <?php selected($default,'date-desc'); ?> value="date-desc"><?php esc_html_e( 'Newest Listings' , 'listeo_core' ); ?></option>
			<option <?php selected($default,'date-asc'); ?> value="date-asc"><?php esc_html_e( 'Oldest Listings' , 'listeo_core' ); ?></option>

			<option <?php selected($default,'featured'); ?> value="featured"><?php esc_html_e( 'Featured' , 'listeo_core' ); ?></option>
			<option <?php selected($default,'views'); ?> value="views"><?php esc_html_e( 'Most Views' , 'listeo_core' ); ?></option>
			<option <?php selected($default,'rand'); ?> value="rand"><?php esc_html_e( 'Random' , 'listeo_core' ); ?></option>
		</select>
	</div>
</div>