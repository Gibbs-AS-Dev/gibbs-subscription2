<?php
require "wp-load.php";

session_start();

$redirect_url =  $_SESSION['redirect_url_cripto']; 

unset($_SESSION["redirect_url_cripto"]);

session_destroy();


global $wpdb;

if(isset($_POST["verify_token"]) &&  $_POST["verify_token"] != "" && is_user_logged_in()){
    update_user_meta(get_current_user_id(),"_verified_user","on");
}
if(isset($redirect_url) &&  $redirect_url != ""){
   wp_redirect($redirect_url); exit;
}else{
    wp_redirect(home_url()); exit;
}