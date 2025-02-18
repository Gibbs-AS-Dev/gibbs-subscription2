<?php 
$contacts = false;
$phone = get_post_meta( get_the_ID(), '_phone', true );
$mail = get_post_meta( get_the_ID(), '_email', true );
$website = get_post_meta( get_the_ID(), '_website', true );
if($phone || $mail || $website ) {
	$contacts = true;
}

$socials = false;
$facebook = get_post_meta( get_the_ID(), '_facebook', true );
$youtube = get_post_meta( get_the_ID(), '_youtube', true );
$twitter = get_post_meta( get_the_ID(), '_twitter', true );
$instagram = get_post_meta( get_the_ID(), '_instagram', true );
$skype = get_post_meta( get_the_ID(), '_skype', true );
$whatsapp = get_post_meta( get_the_ID(), '_whatsapp', true );
$linkedin = get_post_meta( get_the_ID(), '_linkedin', true );
$soundcloud = get_post_meta( get_the_ID(), '_soundcloud', true );
if($facebook || $youtube || $twitter || $instagram || $skype || $whatsapp || $soundcloud ) {
	$socials = true;
}

if($socials || $contacts) :
?>

<div class="listing-links-container">
	<?php 
	$visibility_setting = get_option('listeo_user_contact_details_visibility'); // hide_all, show_all, show_logged, show_booked,  
	if($visibility_setting == 'hide_all') {
		$show_details = false;
	} elseif ($visibility_setting == 'show_all') {
		$show_details = true;
	} else {
		if(is_user_logged_in() ){
			if($visibility_setting == 'show_logged'){
				$show_details = true;
			} else {
				$show_details = false;
			}
		} else {
			$show_details = false;
		}
	}	
	
		
	if($contacts) : 
		
		if($show_details){ ?>
				
			<ul class="listing-links contact-links">
				<?php if(isset($phone) && !empty($phone)): ?>
				<li><a href="tel:<?php echo esc_attr($phone); ?>" class="listing-links"><i class="fa fa-phone"></i> <?php echo esc_html($phone); ?></a></li>
				<?php endif; ?>
				<?php if(isset($mail) && !empty($mail)): ?>
				<li><a href="mailto:<?php echo esc_attr($mail); ?>" class="listing-links"><i class="fa fa-envelope"></i> <?php echo esc_html($mail); ?></a>
				</li>
				<?php endif; ?>
				<?php if(isset($website) && !empty($website)):
				$url =  wp_parse_url($website); ?>
				<li><a rel=nofollow href="<?php echo esc_url($website) ?>" target="_blank"  class="listing-links"><i class="fa fa-link"></i> <?php
				if(isset($url['host'])) { echo esc_html($url['host']); } else { esc_html_e('Visit website', 'listeo_core'); } ?></a></li>
				<?php endif; ?>
			</ul>
			<div class="clearfix"></div>
			<?php 
		} else {
		if($visibility_setting != 'hide_all') {?>
			<p><?php printf( esc_html__( 'Please %s sign %s in to see contact details.', 'listeo_core' ), '<a href="#sign-in-dialog" class="sign-in popup-with-zoom-anim">', '</a>' ) ?></p>
		<?php } ?>
		<?php }
	endif; ?>

	​<?php if($show_details && $socials) : ?>
	<ul class="listing-links">
		<?php if(isset($facebook) && !empty($facebook)): ?>
		<li><a href="<?php echo esc_url($facebook); ?>" target="_blank" class="listing-links-fb"><i class="fa fa-facebook-square"></i> Facebook</a></li>
		<?php endif; ?>
		<?php if(isset($youtube) && !empty($youtube)): ?>
		<li><a href="<?php echo esc_url($youtube); ?>" target="_blank" class="listing-links-yt"><i class="fa fa-youtube-play"></i> YouTube</a></li>
		<?php endif; ?>
		<?php if(isset($instagram) && !empty($instagram)): ?>
		<li><a href="<?php echo esc_url($instagram); ?>" target="_blank" class="listing-links-ig"><i class="fa fa-instagram"></i> Instagram</a></li>
		<?php endif; ?>
		<?php if(isset($twitter) && !empty($twitter)): ?>
		<li><a href="<?php echo esc_url($twitter); ?>" target="_blank" class="listing-links-tt"><i class="fa fa-twitter"></i> Twitter</a></li>
		<?php endif; ?>
		<?php if(isset($linkedin) && !empty($linkedin)): ?>
		<li><a href="<?php echo esc_url($linkedin); ?>" target="_blank" class="listing-links-linkedit"><i class="fa fa-linkedin"></i> LinkedIn</a></li>
		<?php endif; ?>
		<?php if(isset($skype) && !empty($skype)): ?>
		<li><a href="<?php if(strpos($skype, 'http') === 0) { echo esc_url($skype); } else { echo "skype:+".$skype."?call"; } ?>" target="_blank" class="listing-links-skype"><i class="fa fa-skype"></i> Skype</a></li>
		<?php endif; ?>
		<?php if(isset($whatsapp) && !empty($whatsapp)): ?>
		<li><a href="<?php if(strpos($whatsapp, 'http') === 0) { echo esc_url($whatsapp); } else { echo "https://wa.me/".$whatsapp; } ?>" target="_blank" class="listing-links-whatsapp"><i class="fa fa-whatsapp"></i> WhatsApp</a></li>
		<?php endif; ?>	
		<?php if(isset($soundcloud) && !empty($soundcloud)): ?>
		<li><a href="<?php if(strpos($soundcloud, 'http') === 0) { echo esc_url($soundcloud); } else { echo "https://soundcloud.com/".$soundcloud; } ?>" target="_blank" class="listing-links-soundcloud"><i class="fa fa-soundcloud"></i> Soundcloud</a></li>
		<?php endif; ?>
	</ul>
	<div class="clearfix"></div>
	<?php endif; ?>

</div>
<div class="clearfix"></div>
<?php endif; ?>