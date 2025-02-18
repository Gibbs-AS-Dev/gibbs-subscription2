<?php 

if(isset($data)) :
	$in_rows	 	= (isset($data->in_rows)) ? $data->in_rows : '' ;
	$ajax_browsing  = (isset($data->ajax_browsing)) ? $data->ajax_browsing : get_option('listeo_ajax_browsing');
endif; ?>
<div class="clearfix"></div>
<!-- <?php if(!empty($in_rows)): ?>
	</div>
<?php endif; ?> -->
</div>
<?php if($data->max_num_pages > 1) : ?>
<div class="pagination-container margin-top-20 margin-bottom-20 <?php if( isset($ajax_browsing) && $ajax_browsing == 'on' ) { echo esc_attr('ajax-search'); } ?>">
	<nav class="pagination">
		<?php listeo_core_pagination(  $data->max_num_pages ); ?>
	</nav>
</div>
<?php endif; ?>