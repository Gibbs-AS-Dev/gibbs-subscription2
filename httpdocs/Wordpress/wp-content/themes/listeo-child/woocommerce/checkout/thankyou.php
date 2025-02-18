<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
$order->update_status( 'completed' );
WC()->session->set( 'dibs_payment_id', "" );
WC()->session->set( 'dibs_return_url', "" );


$gift_code = "";
?>
<style type="text/css">
    .hs-cookie-notification-position-bottom{
        display: none;
    }
	header{
		display: none;
	}
</style>
<div class="woocommerce-order">

	<?php
	if ( $order ) :

		    $gift_booking_id = get_post_meta($order->get_id(),"gift_booking_id",true);

			if($gift_booking_id != ""){
				if(class_exists("Class_Gibbs_Giftcard")){

					$Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

					$gift_booking_data = $Class_Gibbs_Giftcard->getGiftDataByBookingId($gift_booking_id);

					if(isset($gift_booking_data["code"])){
						$gift_code = $gift_booking_data["code"];
					}
				}	
			}

		

		

			do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

			<?php if ( $order->has_status( 'failed' ) ) : ?>

				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
					<?php endif; ?>
				</p>

			<?php else : ?>




						<div id="thankyou-pdf" >
							<h1 class="email-headline">
								<?php if($gift_code == ""){ ?>
								   <?php esc_html_e( 'Takk for din reservasjon', 'woocommerce' ); ?>
								<?php } ?>   
							</h1>

							<div class="thankyou-pdf-body">

								<div>
								<?php
									//wc_get_template( 'emails/customer-completed-order.php', array( 'order' => $order ) );

									wc_get_template(
										'emails/email-order-details.php',
										array(
											'order'         => $order,
											'sent_to_admin' => $sent_to_admin,
											'plain_text'    => $plain_text,
											'email'         => $email,
										)
									);
								?>
								</div>

								<?php	

								    if($order->get_total() > 0){


										wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
									}
								?>
							</div>
						</div>

				

			<div class="genPdfBlock">
				<a href="#" id="generatePDF">
					<?php esc_html_e( 'Klikk her for å laste ned som PDF', 'woocommerce' ); ?>
					<i class="fa fa-file" aria-hidden="true"></i>
				</a>
			</div>
			<?php if($gift_code == ""){ ?>
			<div class="genIcsBlock">
				<a href="<?php echo get_home_url(); ?>?download_ics=<?php echo $order->get_id(); ?>" target="_blank">
					<?php esc_html_e( 'Legg til i kalender (.ics fil)', 'woocommerce' ); ?>
					<i class="fa fa-file" aria-hidden="true"></i>
				</a>
			</div>
			<?php }else{ ?>

				<form method="post" action="<?php echo admin_url('admin-ajax.php');?>" style="display: flex;justify-content: center;">
				    <input type="hidden" name="action" value="downloadGiftPDF">
				    <input type="hidden" name="giftcode" value="<?php echo $gift_code;?>">
					<div style="text-align: center;">
					<button type="submit" class="btn btn-primary">Last ned gavekort</button>
					<p style="margin-top: 10px;">Gavekort sendes også på epost</p>
					</div>
				</form>

			<?php } ?>
		<?php endif; ?>

		<?php //do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php //do_action( 'woocommerce_thankyou', $order->get_id() ); ?>



	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>     
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
