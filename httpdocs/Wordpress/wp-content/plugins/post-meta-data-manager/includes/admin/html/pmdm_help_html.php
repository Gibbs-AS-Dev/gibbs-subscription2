<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


$content = esc_html__(sprintf("Looking for expert assistance with your WordPress sites?  You’ve come to the right place. Whether it’s a one-off customization project or a long-term partnership that can grow with you, we can help you get the job done right."), 'pmdm_wp');
?>
<div class="wrap">
    <h1><?php esc_html_e('Post Metadata Manager Help', 'pmdm_wp'); ?></h1>
    <h3 class=""></h3>
    <footer class="tribe-events-admin-cta">
		

		<div class="tribe-events-admin-cta__content">
			<h2 class="tribe-events-admin-cta__content-title">
                <?php echo $content; ?>
			</h2>

			<div class="tribe-events-admin-cta__content-description">
				<a href="<?php echo PMDM_HELP_LINK; ?>">
					<?php esc_html_e( 'Contact us for help', 'pmdm_wp' ); ?>
				</a>
			</div>
		</div>
	</footer>
</div>