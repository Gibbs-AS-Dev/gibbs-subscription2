<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package listeo
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header_remove("X-Frame-Options");
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets_gibbs/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets/css/intlTelInput.css?ver=5.7.3">
<!-- <link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets/css/intlTelInput.css?ver=5.7.2"> -->
<!-- our project just needs Font Awesome Solid + Brands -->
 <link href="<?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/css/fontawesome.min.css" rel="stylesheet">
  <!-- <link href="<?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/css/brands.min.css" rel="stylesheet"> -->
  <link href="<?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/css/solid.min.css" rel="stylesheet">
  <link href="<?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/css/svg-with-js.css" rel="stylesheet"> 
  <!-- <script type="text/javascript" src="?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/js/fontawesome.min.js"></script> -->
 <!--  <script defer src="<?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/js/brands.min.js"></script> -->
  <script defer src="<?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/js/solid.min.js"></script>
  <script defer src="<?php echo get_stylesheet_directory_uri();?>/assets/fontawesome-pro/js/fontawesome.min.js"></script>

<?php wp_head(); ?>
<!-- 
<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/style.css">
<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets/css/custom.css"> -->
<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets_gibbs/css/gibbs-style.css?ver=<?php echo GIBBS_VERSION;?>">
<!-- <script async src="https://imgbb.com/upload.js" data-sibling="#send-message-from-widget .button" data-auto-insert="viewer-links" data-sibling-pos="before"></script> -->




</head>
<?php
include_once("templates/gibbs-header.php"); 
echo do_shortcode("[gibbs_register_login popup=true]");