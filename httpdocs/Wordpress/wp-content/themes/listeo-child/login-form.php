<div id="sign-in-dialog">
                    
    <?php do_action('listeo_login_form'); ?>
    <script>
        jQuery(document).ready(function(){
            jQuery(".xoo-el-modal").remove();
            jQuery(".zoom-anim-dialog").remove();

            var redr = "<?php echo $redirect;?>";

            if( redr != ""){
                jQuery('input[name=_wp_http_referer]').val(redr);
            }
        })
    </script>
</div>