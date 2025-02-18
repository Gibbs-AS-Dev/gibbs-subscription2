<?php
get_header();

?>
<link rel='stylesheet' id='simple-line-icons-css' href='<?php echo home_url();?>/wp-content/themes/listeo/css/simple-line-icons.css?ver=3.5.75' type='text/css' media='all' />
<style>
    .card-page .col-md-12{
        display: flex;
        justify-content: center;
        margin-top: 64px;

    }
    .card-page .card{
       padding: 43px;
       width: 45%;
    }
</style>
<div class="container card-page">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content">

                    <?php 

                        $_verified_user = get_user_meta(get_current_user_id(),"_verified_user",true);



                        if($_verified_user == "on") { ?>

                            <div class="verified-badge-page">
                                <i class="sl sl-icon-check"></i> <?php esc_html_e('You are verified','listeo_core') ?>
                            </div>
                                           
                    <?php }else{ ?>
                        <div class="crad-header">
                        <h4 class="d-flex justify-content-center"><?php  echo __("Verifiser deg med","Gibbs");?></h4>
                        </div>
                        <form method="post" class="user_update_form" action="javascript:void(0)">
                            
                            <div class="row">
                                    
                                        <?php

                                        if(!is_user_logged_in()){

                                            echo $button = "<button type='button'  class=\"criipto_login_button login_click\">BankID</button>";

                                        }else{
                                            global $wp;
                                            $cr_url =  add_query_arg( $wp->query_vars, home_url( $wp->request ) );
                                            $gibb_url =  home_url()."/verify";
                                            //echo $button = "<button type='button' class=\"vipps_login_button\" onclick=\"window.location.href='".home_url()."?option=oauthredirect&app_name=Vipps&redirect_url=".$cr_url."';\">Vipps</button>";
                                            echo $button = "<button type='button' onclick=\"window.location.href='".home_url()."/auth.php?redirect=true'\" class=\"criipto_login_button\">BankID</button>";
                                        }
                                        ?>
                                    
                                
                                    <p class="verify_notification"><?php  echo __("Ved å verifisere din bruker, vil du kunne booke tjenester som krever verifisering.","Gibbs");?></p>
                                </div>
                            </div>
                        </form>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script>
    jQuery(".login_click").click(function(){
        jQuery("#lg_reg_modal").show();
    })
</script>