<?php
session_start();
if(!class_exists('wc_create_order')){
   include_once(ABSPATH."/wp-content/plugins/woocommerce/woocommerce.php");
}
global $woocommerce;

if(isset($data->order_id) && $data->order_id ) {
	$order = new WC_Order( $data->order_id );
	
	$payment_url = $order->get_checkout_payment_url();
	?>
	<style type="text/css">
	     header#header-container {
	        display: none;
	    }
	</style> 
	<?php
}

$data->error = false;
//echo '<div class="notification closeable success">' . $data->message . '</div>';
if(isset($data->error) && $data->error == true){  ?>
	<div class="booking-confirmation-page booking-confrimation-error">
	<i class="fa fa-exclamation-circle"></i>
	<h2 class="margin-top-30"><?php esc_html_e('Oops, we have some problem.','listeo_core'); ?></h2>
	<p><?php echo  $data->message  ?></p>
	<p>data ->error<?php echo  $data->error  ?></p>
</div>

<?php } else { ?>
<div class="booking-confirmation-page" <?php if(isset($payment_url) && $payment_url != "") { ?> style="display: none;" <?php } ?>>


<?php
global $wpdb;
$resultss = $wpdb->get_results("SELECT ID FROM ptn_bookings_calendar");
$status = $_SESSION['status'];
$ds = $_SESSION['date_start'];
$de = $_SESSION['date_end'];
$listing_id = $_SESSION['id'];
$reservation_id = end($resultss)->ID;

$time = $_SESSION['time'];
$startHour = strtok($time, ':');
$endHour = strstr($time, '-');
$endHour = strstr($endHour, ' ');
$endHour = strtok($endHour, ':');



db_insert($reservation_id,$status,$ds,$de,$startHour,$endHour,$listing_id);

?>


<?php 
	global $wpdb;
	$resultss = $wpdb->get_results("SELECT ID FROM ptn_bookings_calendar");
?>

		
		<?php if($data->status == "pending"): ?>
			<style>.booking-confirmation-page .loading{ position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 9999; background: #a09595cc; opacity: 1; } .booking-confirmation-page .loading .timer-loader{ position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); }</style>
			<div class="loading">
				<span class="timer-loader">Loading…</span>
			</div>
		<?php else: ?>
			<!-- <i class="fa fa-check-circle"></i> -->
			<h2 class="margin-top-30"><?php echo  $data->message  ?></h2>
			<p><?php echo  $data->submessage  ?></p>
		<?php endif; ?>

	<?php 
	if(isset($payment_url)) { 
		if(!get_option('listeo_disable_payments')){
	
			?>
			<script>window.location='<?php echo $payment_url; ?>'</script>
		<a href="<?php echo esc_url($payment_url); ?>" class="button color"><?php esc_html_e('Pay now','listeo_core'); ?></a>
	<?php } 
	}?>

	<?php $user_bookings_page = get_option('listeo_user_bookings_page');  
	if( $user_bookings_page ) : ?>
	<a href="<?php echo esc_url(get_permalink($user_bookings_page)); ?>" class="button"><?php esc_html_e('Go to My Bookings','listeo_core'); ?></a>
	<?php endif; ?>





</div>

<?php } ?>
<style type="text/css">
.booking_formm{
	display: none;
}
</style>