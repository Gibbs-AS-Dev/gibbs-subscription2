<?php

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) )

	exit;

class ExtendedListeoListings extends Listeo_Core_Listing {

	public static function get_real_listings($args ) {



		global $wpdb;



		global $paged;

		

		if(isset($args['listeo_orderby'])) {

			$ordering_args = ExtendedListeoListings::get_listings_ordering_args($args['listeo_orderby']);

			

		} else {

			$ordering_args = ExtendedListeoListings::get_listings_ordering_args( );	

		}

		

		

		

		if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }

		elseif ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }

		else { $paged = 1; }

	

		$search_radius_var = get_query_var( 'search_radius' );

		if(!empty($search_radius_var)) { $args['search_radius'] = $search_radius_var;	}



		$radius_type_var = get_query_var( 'radius_type' );

		if(!empty($radius_type_var)) {	$args['radius_type'] = $radius_type_var;	}

				

		$keyword_var = get_query_var( 'keyword_search' );

		if(!empty($keyword_var)) {	$args['keyword'] = $keyword_var;	}

		

		$location_var = get_query_var( 'location_search' );

		if(!empty($location_var)) {	$args['location'] = $location_var;	}

		

		$query_args = array(

			'query_label' 			 => 'listeo_get_listing_query',

			'post_type'              => 'listing',

			'post_status'            => 'publish',

			'ignore_sticky_posts'    => 1,

			'paged' 		 		 => $paged,

			'posts_per_page'         => intval( $args['posts_per_page'] ),

			'orderby'                => $ordering_args['orderby'],

			'order'                  => $ordering_args['order'],

			'tax_query'              => array(),

			'meta_query'             => array(),

		);





		if(isset($args['offset'])){

			$query_args['offset'] = $args['offset'];

		}

	    if(isset($ordering_args['meta_type']) ){

			$query_args['meta_type'] = $ordering_args['meta_type'];

		}

		if(isset($ordering_args['meta_key']) && $ordering_args['meta_key'] != '_featured' ){

			$query_args['meta_key'] = $ordering_args['meta_key'];

		}

		$keywords_post_ids = array();

		$location_post_ids = array();



		if ( isset($args['keyword']) && !empty($args['keyword']) ) {

			$keywords = array_map( 'trim', explode( ',', $args['keyword'] ) );



				// Setup SQL



				$posts_keywords_sql    = array();

				$postmeta_keywords_sql = array();



				$postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql( $keywords[0] ) . "%' ";

				// Create post title and content SQL

				$posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql( $keywords[0] ) . "%' OR post_content LIKE '%" . esc_sql(  $keywords[0] ) . "%' ";

					

				// Get post IDs from post meta search



				$post_ids = $wpdb->get_col( "

				    SELECT DISTINCT post_id FROM {$wpdb->postmeta}

				    WHERE " . implode( ' OR ', $postmeta_keywords_sql ) . "

				" );



				// Merge with post IDs from post title and content search



				$keywords_post_ids = array_merge( $post_ids, $wpdb->get_col( "

				    SELECT ID FROM {$wpdb->posts}

				    WHERE ( " . implode( ' OR ', $posts_keywords_sql ) . " )

				    AND post_type = 'listing'

				   

				" ), array( 0 ) );

				/* array( 0 ) is set to return no result when no keyword was found */

		}

		

		if ( isset($args['location']) && !empty($args['location']) ) {

			$radius = $args['search_radius'];



			// if(empty($radius)){

				   //      		$radius =  get_option('listeo_maps_default_radius');

				   //      	}

							$radius_type = get_option('listeo_radius_unit','km');

							$radius_api_key = get_option( 'listeo_maps_api_server' );

						

							if(!empty($args['location']) && !empty($radius) && !empty($radius_api_key)) {

								//search by google

								

								$latlng = listeo_core_geocode($args['location']);

								

								$nearbyposts = listeo_core_get_nearby_listings($latlng[0], $latlng[1], $radius, $radius_type ); 



								listeo_core_array_sort_by_column($nearbyposts,'distance');

								$location_post_ids = array_unique(array_column($nearbyposts, 'post_id'));



								if(empty($location_post_ids)) {

									$location_post_ids = array(0);

								}



							} else {

								

								$locations = array_map( 'trim', explode( ',', $args['location'] ) );



								// Setup SQL



								$posts_locations_sql    = array();

								$postmeta_locations_sql = array();



								if(get_option('listeo_search_only_address','off') == 'on') {

									$postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql( $locations[0] ) . "%'  AND meta_key = '_address'" ;

									$postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql( $locations[0] ) . "%'  AND meta_key = '_friendly_address'" ;

								} else {

									$postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql( $locations[0] ) . "%' ";

									// Create post title and content SQL

									$posts_locations_sql[]    = " post_title LIKE '%" . esc_sql( $locations[0] ) . "%' OR post_content LIKE '%" . esc_sql(  $locations[0] ) . "%' ";

								}

									

								// Get post IDs from post meta search



								$post_ids = $wpdb->get_col( "

								    SELECT DISTINCT post_id FROM {$wpdb->postmeta}

								    WHERE " . implode( ' OR ', $postmeta_locations_sql ) . "



								" );



								// Merge with post IDs from post title and content search

								if(get_option('listeo_search_only_address','off') == 'on') {

									$location_post_ids = array_merge( $post_ids,array( 0 ) );

								} else {

									$location_post_ids = array_merge( $post_ids, $wpdb->get_col( "

									    SELECT ID FROM {$wpdb->posts}

									    WHERE ( " . implode( ' OR ', $posts_locations_sql ) . " )

									    AND post_type = 'listing'

									    AND post_status = 'publish'

									   

									" ), array( 0 ) );

								}

									

								

							}

						}

					

						if ( sizeof( $keywords_post_ids ) != 0 && sizeof( $location_post_ids ) != 0 ) {

							$post_ids = array_intersect($keywords_post_ids, $location_post_ids);

							if(!empty($post_ids)){

								$query_args['post__in'] = $post_ids;	

							} else {

							    

							    $query_args['post__in'] = array(0);

							}

							

						} else if (sizeof( $keywords_post_ids ) != 0 && sizeof( $location_post_ids ) == 0) {

							$query_args['post__in'] = $keywords_post_ids;

						} else if (sizeof( $keywords_post_ids ) == 0 && sizeof( $location_post_ids ) != 0) {

							$query_args['post__in'] = $location_post_ids;

						}

						if(isset($args['date_start']) && !empty($args['date_start'])) {



								// $date_start =  str_replace("/", "-",	$args['date_start']);

						  //       $date_end =  str_replace("/", "-", 	$args['date_end']);

							 	$date_start =   $args['date_start'];

						        $date_end =   	$args['date_end'];

						        

						        $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );

					            $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );

					            

								$posts_not_ids = 

								$wpdb->get_col( 

										$wpdb->prepare( 

											"SELECT listing_id 

							            	FROM {$wpdb->prefix}bookings_calendar 

							            	WHERE 

								            	( %s >= date_start AND %s <= date_end ) 

								            	OR 

								            	( %s >= date_start AND %s <= date_end ) 

								            	OR 

								            	( date_start >= %s AND date_end <= %s )

								            GROUP BY listing_id 

							            	",

							            	$date_start, 

							            	$date_start,

							            	$date_end, 

							            	$date_end, 

							            	$date_start, 

							            	$date_end

							        )

					        	);

				 

								$query_args['post__not_in'] = $posts_not_ids;

							



						}

					



						$query_args['tax_query'] = array(

					        'relation' => 'AND',

					    );

						$taxonomy_objects = get_object_taxonomies( 'listing', 'objects' );





				        foreach ($taxonomy_objects as $tax) {

				        	

				        	

							$get_tax = false;

							if((isset($_GET['tax-'.$tax->name]) && !empty($_GET['tax-'.$tax->name]))) {

								$get_tax = $_GET['tax-'.$tax->name];

							} else {

								if(isset($args['tax-'.$tax->name])){

									$get_tax = $args['tax-'.$tax->name] ;



								}

								

							}



				        	if(is_array($get_tax)){

				        		

				        		$query_args['tax_query'][$tax->name] = array('relation'=> get_option('listeo_taxonomy_or_and','OR'));

				        		foreach ($get_tax as $key => $value) {

						    		array_push($query_args['tax_query'][$tax->name], array(

							           'taxonomy' =>   $tax->name,

							           'field'    =>   'slug',

							           'terms'    =>   $value,

							           

							        ));

						    	}

						    	

				        	} else {



				            	if( $get_tax ){

				            		if(is_numeric($get_tax)){

										$term = get_term_by('slug', $get_tax, $tax->name);

								    	if($term){

									    	array_push($query_args['tax_query'], array(

									           'taxonomy' =>  $tax->name,

									           'field'    =>  'slug',

									           'terms'    =>  $term->slug,

									           'operator' =>  'IN'

									        ));	

								    	}

				            		} else {

				            			$get_tax_array = explode(',',$get_tax);

				            			//$query_args['tax_query'][$tax->name] = array('relation'=> 'OR');

				            			array_push($query_args['tax_query'], array(

									           'taxonomy' =>  $tax->name,

									           'field'    =>  'slug',

									           'terms'    =>  $get_tax_array,

									           

									        ));	

				            		}


							    }

						 	}

						 	

				        }

				     	

						$available_query_vars = Listeo_Core_Search::build_available_query_vars();

						$meta_queries = array();

						if(!$args['featured']) {

							$available_query_vars[] = 'featured';

						}



							
						foreach ($available_query_vars as $key => $meta_key) {

							if( substr($meta_key,0, 4) == "tax-") {

								continue;

							}

							if( $meta_key == '_price_range'){

								continue;

							}

						
							if($meta_key == '_price'){

									

									$meta = false;

									if(!empty(get_query_var( '_price_range' ))){

										$meta = get_query_var( '_price_range' );

									} else if(isset($args['_price_range'])){

										$meta = $args['_price_range'];

									}

									if(!empty($meta)){

										$range = array_map( 'absint', explode( ',', $meta ) );
										
										$query_args['meta_query'][] = array(

										 	'relation' => 'OR',

										        array(

										            'relation' => 'OR',

										            array(

						                                'key' => '_price_min',

						                                'value' => $range,

						                                'compare' => 'BETWEEN',

						                                'type' => 'NUMERIC',

						                            ),

						                            array(

						                                'key' => '_price_max',

						                                'value' => $range,

						                                'compare' => 'BETWEEN',

						                                'type' => 'NUMERIC',

						                            ),

										 

										        ),

										        array(

										            'relation' => 'AND',

										            array(

						                                'key' => '_price_min',

						                                'value' => $range[0],

						                                'compare' => '<=',

						                                'type' => 'NUMERIC',

						                            ),

						                            array(

						                                'key' => '_price_max',

						                                'value' => $range[1],

						                                'compare' => '>=',

						                                'type' => 'NUMERIC',

						                            ),

										 

										        ),

								        );

								       

							        }

							} else {

								if (substr($meta_key, -4) == "_min" || substr($meta_key, -4) == "_max") { continue; }

								$meta = false;

								

								

								if(!empty(get_query_var( $meta_key ))){

									$meta = get_query_var( $meta_key );

								} else if(isset($args[$meta_key] )){

									

									$meta = $args[$meta_key];

								}



								if ( $meta ) {

									

									if($meta === 'featured') {

										$query_args['meta_query'][] = array(

							                'key'     => '_featured',

											'value'   => 'on',

											'compare' => '='

							            );	

									} else {

										

										$query_args['meta_query'][] = array(

							                'key'     => $meta_key,

							                'value'   => $meta, 

							            );	

									}

									

								}

								

							}
						


						}

						if($args['featured'] == true) {

							$query_args['meta_query'][] = array(

					                'key'     => '_featured',

									'value'   => 'on',

									'compare' => '='

					            );	

						}

						

				    	if( isset($ordering_args['meta_key']) && $ordering_args['meta_key'] == '_featured' ){



							$query_args['meta_query'][] = array(

								'relation' => 'OR',

								'featured_clause' => array(

									'key'     => '_featured',

									'value'   => 'on',

									'compare' => '='

								),

								'notexistfeatured_clause' => array(

									'key'     => '_featured',

									'compare' => 'NOT EXISTS'

								),

								'notfeatured_clause' => array(

									'key'     => '_featured',

									'value'   => 'on',

									'compare' => '!='

								),

								

							);

							$query_args['order'] = 'DESC';

							$query_args['orderby'] = array( 'featured_clause' => 'DESC');

						}



						if ( empty( $query_args['meta_query'] ) )

							unset( $query_args['meta_query'] );



						

						

						$query_args = apply_filters( 'realto_get_listings', $query_args, $args );

						

						$result = new WP_Query( $query_args );

						

						return $result;

	}


	public static function get_listing_price( $post ) {


		// Use global post ID if not defined



		if ( ! $post ) {

			$post = get_the_ID();

		} else {

			$post = $post->ID;

		}



		$price = get_post_meta( $post, '_price', true );

		if (is_numeric($price)) {

		    $price_raw = number_format_i18n($price);

		} else {

		    return $price;

		}



		$price_output = '';

		if ( !empty( $price_raw ) ) :

			

 			$currency_abbr = get_option( 'listeo_currency' );

			$currency_postion = get_option( 'listeo_currency_postion' );

			$currency_symbol = ExtendedListeoListings::get_currency_symbol($currency_abbr);



			if($currency_postion == 'after') {

				$price_output = $price_raw . $currency_symbol;

			} else {

				$price_output = $currency_symbol.$price_raw;

			}



		endif;

		// Return listing price

		return apply_filters( 'get_listing_price', $price_output, $post );

	}

	public static function get_listing_price_range( $post ){
		
		if ( ! $post ) {

			$post = get_the_ID();

		} else {

			$post = $post->ID;

		}



		$price_output = '';



		$price_min = get_post_meta( $post, '_price_min', true );

		$price_max = get_post_meta( $post, '_price_max', true );

		

		if(!empty($price_min) || !empty($price_max)) {

			if (is_numeric($price_min)) {

			    $price_min_raw = number_format_i18n($price_min);

			} 

			if (is_numeric($price_max)) {

			    $price_max_raw = number_format_i18n($price_max);

			} 

			$currency_abbr = get_option( 'listeo_currency' );

			$currency_postion = get_option( 'listeo_currency_postion' );

			$currency_symbol = ExtendedListeoListings::get_currency_symbol($currency_abbr);

			if($currency_postion == 'after') {

				if(!empty($price_min_raw) && !empty($price_max_raw)){

					$price_output .=  $price_min_raw . $currency_symbol;

					$price_output .=  ' - ';

					$price_output .=  $price_max_raw . $currency_symbol;	

				} else 

				if(!empty($price_min_raw) && empty($price_max_raw)) {

					$price_output .=  esc_html__('Starts from ','listeo_core') .$price_min_raw . $currency_symbol;

				} else {

					$price_output .=  esc_html__('Up to ','listeo_core') .$price_max_raw . $currency_symbol;

				}

				

			} else {

				if(!empty($price_min_raw) && !empty($price_max_raw)){

					$price_output .=  $currency_symbol . $price_min_raw;

					$price_output .=  ' - ';

					$price_output .=  $currency_symbol . $price_max_raw;	

				} else 

				if(!empty($price_min_raw) && empty($price_max_raw)) {

					$price_output .=  esc_html__('Starts from ','listeo_core') .$currency_symbol .$price_min_raw;

				} else {

					$price_output .=  esc_html__('Up to ','listeo_core'). $currency_symbol .$price_max_raw ;

				}







			}

		}



		return apply_filters( 'listing_price_range', $price_output, $post );

	}



	public static function get_listing_price_per_scale( $post ) {

		if ( ! $post )

			$post = get_the_ID();



		$price_raw 		= get_post_meta( $post, '_price', true );

		if(empty($price_raw) || !is_numeric($price_raw)){

			return;

		}

		$area 			= get_post_meta( $post, '_area', true );



		$price_per_raw 	= get_post_meta( $post, '_price_per', true );

		$output 		= '';

		$currency_abbr = get_option( 'currency' );

		$currency_postion = get_option( 'currency_postion' );

		$currency_symbol = ExtendedListeoListings::get_currency_symbol($currency_abbr);



		if(empty($price_per_raw) && !empty($area)){

			$output = intval($price_raw/$area,10);

		} else {

			if(empty($price_per_raw)) {

				$output = '';

				

			} else {

				$output = $price_per_raw;

			}

			

		}



		$offer_type = get_the_listing_offer_type();



		if($currency_postion == 'after') {

			$output = $output . $currency_symbol;

		} else {

			$output = $currency_symbol . $output;

		}



		if($offer_type == 'rent') {

			$periods = listeo_core_get_rental_period();

			

			$current_selection = get_post_meta( $post, '_rental_period', true );

		

			if(!empty($current_selection) && isset($periods[$current_selection])) {

				

					$output = $periods[$current_selection];	

				

			} else {

				$output = '';

			}

			

		} else {

			if(get_option( 'listeo_core_hide_price_per_scale' )) {

				$output = '';

			} else {

				$scale = get_option( 'scale', 'sq ft' );

				$output .= ' / '.apply_filters('listeo_core_scale',$scale);		

			}

			

		}

		





		return apply_filters( 'get_listing_price', $output, $post );

	}

	public static function get_listings_ordering_args( $orderby = '', $order = '' ) {

		// Get ordering from query string unless defined

		if ( $orderby )	
			$orderby_value = $orderby;
		else
			$orderby_value = isset( $_GET['listeo_core_order'] ) ? (string) $_GET['listeo_core_order']  : get_option( 'listeo_sort_by','date' );

		// Get order + orderby args from string

		$orderby_value = explode( '-', $orderby_value );

		$orderby       = esc_attr( $orderby_value[0] );

		$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;

		$args    = array();

		// default - menu_order

		$args['orderby']  = 'date ID'; //featured

		$args['order']    = ( 'desc' === $order ) ? 'DESC' : 'ASC';

		$args['meta_key'] = '';

		switch ( $orderby ) {

			case 'rand' :

				$args['orderby']  = 'rand';

				break;

			case 'featured' :

				$args['orderby']  = 'meta_value_num date';

				$args['meta_key']  = '_featured';				

				break;

			case 'date' :

				$args['orderby']  = 'date';

				$args['order']    = ( 'asc' === $order ) ? 'ASC' : 'DESC';

				break;

			case 'highest' :

				$args['orderby']  = 'meta_value_num';

				$args['order']  = 'DESC';

				$args['meta_type'] = 'NUMERIC';

				$args['meta_key']  = 'listeo-avg-rating';

				break;

			case 'views' :

				$args['orderby']  = 'meta_value_num';

				$args['order']  = 'DESC';

				$args['meta_type'] = 'NUMERIC';

				$args['meta_key']  = '_listing_views_count';

				break;

			case 'reviewed' :

				$args['orderby']  = 'comment_count';

				$args['order']  = 'DESC';

				break;			

			case 'title' :

				$args['orderby'] = 'title';

				$args['order']   = ( 'desc' === $order ) ? 'DESC' : 'ASC';

				break;

			case 'priceasc' :

				$args['orderby']  = 'meta_value_num';

				$args['order']  = 'asc';

				$args['meta_type'] = 'NUMERIC';

				$args['meta_key']  = '_price_min';

				break;

			case 'pricedesc' :

				$args['orderby']  = 'meta_value_num';

				$args['order']  = 'desc';

				$args['meta_type'] = 'NUMERIC';

				$args['meta_key']  = '_price_max';

				break;

			default:

				$args['orderby']  = 'date ID';

				$args['order']    = ( 'ASC' === $order ) ? 'ASC' : 'DESC';

				break;

		}

		

		return apply_filters( 'listeo_core_get_listings_ordering_args', $args );

	}

}

new ExtendedListeoListings();

?>