<?php
/**
 * Plugin Name: Gibbs Addon
 * Plugin URI: http://www.gibbs.no
 * Description: A Gibbs plugin
 * Version: 3.0.01
 * Author: Gibbs team
 * License: Commercial
 */
ob_start();
function custom_module_function() {	
	/* session_start();
	//print_r($_SESSION);
			if(is_singular('listing') && $_SESSION["justvisit"] == 'no')
		{
			//$_SESSION["justvisitr"]='nooo';
			//sleep(3);
			//wp_redirect(get_permalink(5869));echo 'HY';
		} */
	/********************Check for payment completion if the user leave the page URL then the booking got deleted (Updated)*********************/
/* 	if ( is_checkout() ) {
	 $base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
 		$url = explode('order-pay/',$base_url . $_SERVER["REQUEST_URI"]);
		$url2=explode('/',$url[1]);
		$_SESSION["favcolor"]=$url2[0];
		$_SESSION["justvisit"]='yes';
	}
	else if(is_page('82')){}
	else
	{

		
		if($_SESSION["justvisit"] == 'yes'){
			global $wpdb,$post,$woocommerce;
			$_user_id = get_current_user_id();
			$order = wc_get_order( $_SESSION["favcolor"] );
			if($order){
				$order_data = $order->get_data();
				update_option('tranculizer',$_SESSION["id"]);
				$order_status = $order_data['status'];
				if( $order_status == 'wc-processing'){$_SESSION["justvisit"]='no';}
				else{
					wp_delete_post($_SESSION["favcolor"],true);
					$_SESSION["justvisit"]='no';			
					$table = $wpdb->prefix.'bookings_calendar';
					$table1 = $wpdb->prefix.'actionscheduler_actions';
					$wpdb->delete($table,array('order_id' => $_SESSION["favcolor"]));
					$wpdb->delete($table1,array('args' => '['.$_SESSION["favcolor"].']'));
					delete_transient( 'listeo_last_booking'.$_user_id );
					wp_cache_flush();
						unset($_COOKIE['listeo_rental_startdate']);
						unset($_COOKIE['listeo_rental_enddate']);
					set_transient( 'listeo_last_booking'.$_user_id, $_SESSION["id"] . ' ' . date('Y-m-d',strtotime("-6 Months")). ' ' . date('Y-m-d',strtotime("-5 Months")), 60 * 15 );
					if(is_singular('listing')){
						unset($_COOKIE['listeo_rental_startdate']);
						unset($_COOKIE['listeo_rental_enddate']);
					set_transient( 'listeo_last_booking'.$_user_id, $_SESSION["id"] . ' ' . date('Y-m-d',strtotime("-6 Months")). ' ' . date('Y-m-d',strtotime("-5 Months")), 60 * 15 );
					sleep(3);	
					wp_redirect(get_permalink($_SESSION["id"]));
					}else{ */
					//set_transient( 'listeo_last_booking'.$_user_id, get_the_ID(). ' ', 60 * 15 );
			/* 			unset($_COOKIE['listeo_rental_startdate']);
						unset($_COOKIE['listeo_rental_enddate']);
					set_transient( 'listeo_last_booking'.$_user_id, $_SESSION["id"] . ' ' . date('Y-m-d',strtotime("-6 Months")). ' ' . date('Y-m-d',strtotime("-5 Months")), 60 * 15 );	
					$protocol = ((!emptyempty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";  
					$urlw = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					wp_redirect($urlw);
					}
				}
			}
		}
		else{$_SESSION["justvisit"]='no';}
	} */
	/********************Check for payment completion if the user leave the page URL then the booking got deleted (Updated)*********************/
	/********************Contact Form Auto Phone Number Complete*********************/
	if ( is_user_logged_in() ) {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.wpcf7-validates-as-tel').val('<?php echo get_user_meta( get_current_user_id(),'phone',true);?>');
	});
	</script>
	<?php
	}
	/********************Contact Form Auto Phone Number Complete*********************/
	
/*********************Header Login Link For the Mobile***************************/ 	
	?>
<script type="text/javascript">
	jQuery(document).ready(function(){	
		let prevyul=jQuery('.xoo-el-login-tgr').attr('href');
		jQuery('.right-side-searchbar').prepend('<a href="'+prevyul+'" class="mokenmu xoo-el-login-tgr">&nbsp;</a>');
	});
</script>
<style type="text/css">
.sign-in-form label i{font-style: normal;}
	
</style>	
<?php
/*********************Header Login Link For the Mobile***************************/ 
if ( is_singular( 'listing' ) ) {
    //echo get_post_meta(get_queried_object_id(),'_tax',true);
}

/***********Choose Header Translation Based on the given option name***********************/
	if(( is_page_template('template-home-search.php') || is_page_template('template-home-search-video.php') || is_page_template('template-home-search-splash.php')) && get_option('listeo_home_typed_status','enable') == 'enable') {
	$current_language = get_locale();	
    if( $current_language == 'en_US' ){
	$typed = preg_replace('/\s+/','',trim(get_option('listeo_home_typed_text_english')));
    }else{	
	$typed = preg_replace('/\s+/','',trim(get_option('listeo_home_typed_text')));
	}
	$typed_array = explode(',',$typed);
	?>
						<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
						<script>
						var typed = new Typed('.typed-words-new', {
						strings: <?php echo json_encode($typed_array); ?>,
						typeSpeed: 80,
						backSpeed: 80,
						backDelay: 4000,
						startDelay: 1000,
						loop: true,
						showCursor: true
						});
						</script>
					<?php } 
	/***********Choose Header Translation Based on the given option name***********************/			
}
add_action( 'wp_footer', 'custom_module_function' );
/***********************Header English Text Translition*********************/
function register_fields()
{
    register_setting('general', 'listeo_home_typed_text_english', 'esc_attr');
    add_settings_field('listeo_home_typed_text_english', '<label for="listeo_home_typed_text_english">'.__('English Text Separeted by comma:' , 'listeo_home_typed_text_english' ).'</label>' , 'print_first_field', 'general');
}
add_filter('admin_init', 'register_fields');
function print_first_field()
{
    $value = get_option( 'listeo_home_typed_text_english', '' );
    echo '<input type="text" class="regular-text" id="listeo_home_typed_text_english" name="listeo_home_typed_text_english" value="' . $value . '" />';
}
/***********************Header English Text Translition*********************/
