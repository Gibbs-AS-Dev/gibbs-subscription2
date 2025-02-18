<?php /* Template Name: embed */ 
get_header();

$current_user_posts = get_posts( [
    'author'    =>  1, 
    'orderby'       =>  'random',
    'post_per_page' => '1',
    'order'         =>  'ASC' 
] );

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$url_components = parse_url($url); 
  
// Use parse_str() function to parse the 
// string passed via URL 
parse_str($url_components['query'], $params); 
      
// Display result 
//echo ' Hi '.$params['a'];

// Grab the user by `user_nicename`
$author = get_user_by( 'slug', $params['a'] );

$posts = get_posts( 
    array(
        'author' => $author->ID, 
        'posts_per_page' => 5
    )
);
$counter = 0;
$urls = array();
foreach ( $posts as $key => $post ) {
    $counter++;
    $idd = $post->ID;
    $is_featured = listeo_core_is_featured($post->ID);
    $listing_type = get_post_meta( $post->ID,'_listing_type',true );
    $output = include 'listeo-core/t.php'; 
    $gal = get_post_meta( $post->ID,'_gallery', true);
    $str = wp_get_attachment_image_src(array_key_first($gal), 'large')[0]; //get_the_post_thumbnail_url($idd);
}
    

?>
