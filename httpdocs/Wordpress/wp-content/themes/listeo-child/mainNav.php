<?php
$logoutOrlogIn = '<ul id="%1$s" class="%2$s">%3$s<!--li class="profile-icon';
if(is_user_logged_in()){

     global $post;
     
     $ancestors = get_post_ancestors($post->ID);
     if(in_array(get_page_by_title('Min Gibbs')->ID, $ancestors) || get_page_by_title('Min Gibbs')->ID == $post->ID){
        $logoutOrlogIn .= " current-page-ancestor";
     }
     $logoutOrlogIn .= '"><a href="' . get_permalink(get_page_by_title('Min Gibbs')) . '">Min gibbs</a></li--></ul>';

     wp_nav_menu( array(
     	'theme_location' => 'main-nav',
     	'container_class' => $navWrapper,
          'items_wrap' => $logoutOrlogIn));

} else {

     $isTopMenu = ($navWrapper == 'main-nav');

     $navWrapper = "mobile-header";

     $enable_header = get_field('show_outlogged_header');

     if($enable_header != ""){
     

          $outlogoutOrlogIn = '<div class="menu-toggle2"><i class="fa fa-bars"></i></div><nav><ul id="%1$s" class="%2$s outlogged-menu">%3$s</ul><i class="fa fa-times close-tgl"></i></nav>';

          wp_nav_menu( array(
               'theme_location' => 'outlogged-nav',
               'container_class' => $navWrapper,
               'items_wrap' => $outlogoutOrlogIn));
     }

     //echo "<pre>"; print_r($languages = trp_custom_language_switcher()); die;

     ?>
     

	<div class="main-nav" <?php if($isTopMenu)  ?> >
		<ul id="menu-new-main-menu" class="menu">
			<?php
		         global $post;
                   $ancestors = get_post_ancestors($post->ID);
                   $homeShouldBeActive = (is_home() || is_front_page());
                   $profileShouldBeActive = (in_array(get_page_by_title('Min Gibbs')->ID, $ancestors) || get_page_by_title('Min Gibbs')->ID == $post->ID);
			?>
               <!-- <li <?php if($isTopMenu) { echo 'style="text-align:left;"'; } ?> class="home-icon <?php if($homeShouldBeActive) {echo ' current-page-ancestor';} ?>"><a <?php if($isTopMenu) {echo 'style="padding-left:25px;" '; } ?> href="<?php echo get_home_url(); ?>">Utforsk</a></li> -->
               <li class="get_your_trial <?php if($homeShouldBeActive)  ?>" <?php if($isTopMenu) { echo 'style="text-align:left;"'; } ?>> <a <?php if($isTopMenu) ?> href="/kom-i-gang-med-gibbs-no/">Prøv gratis</a></li>
		     <!-- <li <?php if($isTopMenu) { echo 'style="text-align:left;"'; } ?>   <?php if($profileShouldBeActive) {echo ' current-page-ancestor';} ?>><a <?php if($isTopMenu) ?>  href="/logg-inn"> </i>  Logg inn </a></li> -->
               <li><?php echo do_shortcode('[language-switcher]');?></li>
		</ul>
	</div>

     <style>
          /* #header-new .trp-language-switcher > div > a:hover {
               padding: 7px 12px !important;
          }
          #header-new .trp-ls-shortcode-disabled-language.trp-ls-disabled-language {
          width: 100% !important;
          }
          .trp-ls-shortcode-language.trp-ls-clicked {
               min-width: 147px !important;
               top: 10px !important;
          } */
     </style>

<?php } ?>

<script>
jQuery(document).ready(function(){
     jQuery(document).on("click",'.close-tgl',function(){
          jQuery(".mobile-header").find('nav').removeClass('active');
     })
})
</script>