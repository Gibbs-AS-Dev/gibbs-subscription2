
<?php
/**
 * Template Name: Applications List Template
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Listeo
 */
get_header();

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/content', 'page' );

endwhile; // End of the loop.

get_footer();
