<?php 
$template_loader = new Listeo_Core_Template_Loader;

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="row" style="text-align: center; background: white; height: 550px;">
    <div class="col-md-12 success-head" style="padding:20px">
        <i class="fa fa-check-circle" style="font-size:200px; color:#23B35F;"></i>
    </div>
    
    <div class="col-md-12 success-body">
        <h2 class="margin-top-30" style="font-weight: 700;"><?php esc_html_e('Your invoice is on its way!','listeo_core'); ?></h2>
    </div>
    
</div>
