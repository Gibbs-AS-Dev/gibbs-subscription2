<?php
if(is_user_logged_in()){
  wp_redirect("/dashboard");
  exit;
}
$gibbs_reg_shortcode = "[gibbs_register]";
if($redirect != ""){
  $gibbs_reg_shortcode = "[gibbs_register redirect='".$redirect."']";
}
?>
<div class="sign-up-form-gibbs"> 
    <div class="sign-up-form-gibbs-inner">
        <div id="register-gib" class="register-gib">
        <?php echo do_shortcode($gibbs_reg_shortcode);?>
        </div>
    </div>
</div>