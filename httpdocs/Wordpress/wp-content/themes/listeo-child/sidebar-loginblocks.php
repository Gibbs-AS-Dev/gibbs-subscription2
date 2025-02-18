<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package gibbs
 */

if ( ! is_active_sidebar( 'loginblocks' ) ) {
	return;
}
?>

	<?php dynamic_sidebar( 'loginblocks' ); ?>
