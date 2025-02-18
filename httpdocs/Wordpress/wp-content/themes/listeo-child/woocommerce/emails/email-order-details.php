<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

//do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );
?>
<script>
localStorage.removeItem("booking_timer_data");
</script>

<?php 
 global $wpdb;
 $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE order_id=".$order->get_order_number());
//print_r($result);
//echo $result[0]->date_start;

 $details_comment = json_decode($result[0]->comment);
 $coupen_exist = false;
 if(isset($result[0]->booking_extra_data) && $result[0]->booking_extra_data != ""){
    $booking_extra_data = json_decode($result[0]->booking_extra_data);
	if(isset($booking_extra_data->coupon_data)){
        $coupen_exist = true;
	}
 }
 
    $av_days = "";

    if(isset($details_comment->av_days)){
       $av_days = $details_comment->av_days;
    }
   // echo  $av_days; die;
	//// echo '<pre>';
//print_r( $details_comment);
// echo '</pre>';
if(isset($result[0]->listing_id)){
	global $listing_idd;
	$listing_idd = $result[0]->listing_id;

	add_filter('woocommerce_currency', 'custom_currency_symbol_on_order_confirmation', 10, 2);
}
	 $listeo_services=get_post_meta($result[0]->listing_id,'_menu',true);
 //echo '<pre>';
//print_r( $listeo_services[0]['menu_elements'][0]['tax']);
 //echo '</pre>';
 
 
 // $booking_for_date=date(get_option( 'date_format' ), strtotime($result[0]->date_start)).' '.date(get_option( 'time_format' ), strtotime($result[0]->date_start)) .' - '. date(get_option( 'date_format' ), strtotime($result[0]->date_end)).' '.date(get_option( 'time_format' ), strtotime($result[0]->date_end));
 	$startDate = date_i18n(get_option( 'date_format' ), strtotime($result[0]->date_start));
	$startTime = date_i18n(get_option( 'time_format' ), strtotime($result[0]->date_start));

	$endDate = date_i18n(get_option( 'date_format' ), strtotime($result[0]->date_end));
    $endTime = date_i18n(get_option( 'time_format' ), strtotime($result[0]->date_end));

    $booking_for_date = $startDate . " " . $startTime . " - ". $endDate . " " . $endTime;

 // $booking_for_date=date_i18n(get_option( 'date_format' ), strtotime($result[0]->date_start)).' '.date_i18n(get_option( 'time_format' ), strtotime($result[0]->date_start)) .' - '. date_i18n(get_option( 'date_format' ), strtotime($result[0]->date_end)).' '.date_i18n(get_option( 'time_format' ), strtotime($result[0]->date_end));
 if(isset($details_comment->message) && $details_comment->message != ""){
    // Echo the label in bold using the <strong> tag
    echo "<label><strong>Booking kommentar:</strong></label>";
    echo "<p>".$details_comment->message."</p>";
}


 ?>
 <h2>
	<?php
	if ( $sent_to_admin ) {
		$before = '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">';
		$after  = '</a>';
	} else {
		$before = '';
		$after  = '';
	}
	/* translators: %s: Order ID. */
	// echo wp_kses_post( $before . sprintf( __( '[Kvittering for ordre #%s]', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
	echo wp_kses_post( $before . sprintf( __( 'Kvittering for ordre #%s', 'woocommerce' ) . '  ' . $after, $order->get_order_number() ) );


	?>
</h2>

	<p>Dato: <?php echo wp_kses_post( $booking_for_date); ?></p>
					
<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, Roboto;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Produkt', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;<?php if($order->get_total() < 1){ ?> display: none<?php } ?>"><?php esc_html_e( 'Pris', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$product_tax_prcentage=get_post_meta($result[0]->listing_id,'_tax',true);
			echo wc_get_email_order_items( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$order,
				array(
					'show_sku'      => $sent_to_admin,
					'show_image'    => false,
					'image_size'    => array( 32, 32 ),
					'plain_text'    => $plain_text,
					'sent_to_admin' => $sent_to_admin,
					'av_days' => $av_days,
				)
			);
			?>
		</tbody>
		<tfoot <?php if($order->get_total() < 1){ ?> style="display: none"<?php } ?>>
			<?php
			$item_totals = $order->get_order_item_totals();


			if ( $item_totals ) {
			$p = 0;	
						if($result){ 
						
	// $listeo_services[0]['menu_elements'][$p]['tax'];				
	 $details_comment = json_decode($result[0]->comment); 




			
    $booking_id = get_post_meta($order->get_id(),'booking_id',true);
    if($booking_id != ""){
    	$discount_type=get_post_meta($booking_id,'discount-type',true);
    }

	$extra_prices = 0;

	


	if($discount_type != ""){
	?>
	<tr>
		<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;">Valgt målgruppe</th>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo $discount_type; ?></td>
	</tr>
                    	
	<?php	
	}


        if(is_numeric($discount_percentage) && $discount_percentage > 100000000000000000){

			//$percentInDecimal = intval($discount_percentage) / 100;
			$percentInDecimal = 0;
            $normal_price = $result[0]->price - ($result[0]->price * $percentInDecimal);
			$product_tax_price=$normal_price *($product_tax_prcentage/100);
			?>
			
			        <tr>
						<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;">Målgruppe </th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( $discount_percentage).'%'; ?></td>
					</tr>
                    
                    <tr>
						<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;">Pris etter rabatt</th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $normal_price)); ?></td>
					</tr>	
		<?php    }else{
			  $percentInDecimal = intval(0) / 100;
			 $normal_price = $result[0]->price - ($result[0]->price * $percentInDecimal);
			 $product_tax_price=$normal_price *($product_tax_prcentage/100);
	    }
		
          	?>
    
   
                    
                     
                    
                    <tr>
						<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;">Delsum</th>
						<td class="td product_price" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $normal_price)); ?></td>
                        
					</tr>
	
		
 <?php 
 $total_addtional_price=0;
 $total_addtional_tax=0;


 

 if($details_comment->service){
				 foreach($details_comment->service as $service_price){
					 $countable=$service_price->countable;
					 $quatity_price=$countable * $service_price->service->price;
					// $normal_price= $normal_price + $service_price->service->price;
					 $total_addtional_price= $total_addtional_price + $quatity_price;
					 $addtional_servic_tax=$listeo_services[0]['menu_elements'][$p]['tax'];	
					 $tax_deducted_price=$quatity_price *($addtional_servic_tax/100);
					 $total_addtional_tax =$total_addtional_tax + $tax_deducted_price;
					 $p++;
					 $additional_service_label_name = get_post_meta($result[0]->listing_id, 'additional_service_label_name', true);
					if( $p===1){?>
	                <tr>
							<th class="td" scope="row" colspan="4" style="text-align:<?php echo esc_attr( $text_align ); ?>; border-top-width: 4px;">
								<?php 
									if($additional_service_label_name != ""){
										echo $additional_service_label_name;
									}else{
										esc_html_e('Additional Services','listeo_core');
									}
								?> 
							</th>
	                        </tr>
					<tr>
	                <?php }?>
	                		<th class="td" scope="row" colspan="" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( $service_price->service->name ); ?> (<?php echo $countable; ?>)</th>
							<!-- <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo $countable; ?></td> -->
							 
	                        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
								<?php if(!$coupen_exist){ ?>
									<?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $quatity_price)); ?>
								<?php }else{ ?>
									--
								<?php } ?>
								
							</td>
						</tr>	
					
				<?php 	} 

				
				
				$sum_product_services=$normal_price + $total_addtional_price;
				$sum_product_services_tax=$product_tax_price + $total_addtional_tax;
				$total_amount=$sum_product_services + $sum_product_services_tax;
				$extra_prices += $total_addtional_price;
				?>
				
				<tr>
						<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;">Delsum</th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
						    <?php if(!$coupen_exist){ ?>
							   <?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $total_addtional_price )); ?>
							<?php }else{ ?>
								--
							<?php } ?>
						</td>
                        
					</tr>
				
			<?php 	}else{
				
				$sum_product_services=$normal_price;
				$sum_product_services_tax=$product_tax_price;
				//$total_amount=$sum_product_services + $sum_product_services_tax;
				$total_amount=$sum_product_services;
				//$extra_prices += $total_amount;
				
				}}

				//echo '<pre>'; print_r($item_totals); die;
				
				
				if($result){
					
					
					$i = 0;	
				foreach ( $item_totals as $total ) {
					$i++;
					
					 /*?> if($total['label']== 'Delsum:' || $total['label']== 'Totalt:'){ }else{
					?>
					<tr>
						<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php  echo wp_kses_post( $total['label'] ); ?></th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i  ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] );?></td>
					</tr>
					<?php
				} <?php */?>
				
				<?php }

                    // $extra_prices += $details_comment->total_tax;
					$extra_prices += $details_comment->total_tax;
					
					
					?>   
					    <?php if(!$coupen_exist){ ?>
							<tr>
								<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;">Herav mva</th>
								<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;" colspan="1"><?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $details_comment->total_tax )); ?></td>
		                       
		                        
							</tr>
                        <?php } ?>

						<?php
						// Refund Calculation Logic
						$refund_amount = 0;
						if ($order->get_total_refunded() > 0) {
							$refund_amount = $order->get_total_refunded();
							$remaining_total = $order->get_total() - $refund_amount;

							?>
							<tr>
								<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;">Refund Amount</th>
								<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo get_woocommerce_currency_symbol().' '.number_format($refund_amount, 2); ?></td>
							</tr>
							<?php
						} else {
							$remaining_total = $order->get_total();
						}
						?>
						
							<tr>
								<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;border-top-width: 4px;">Sum</th>
								<?php if($total_amount < 1){ ?>
									<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; border-top-width: 4px;">GRATIS</td>

								<?php }else{ ?>
									<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; border-top-width: 4px;"><?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $remaining_total )); ?></td>
						     	<?php } ?>
		                        
							</tr>
							<?php 
							if(isset($details_comment->total_tax) && $details_comment->total_tax != "" && $details_comment->total_tax > 0){
							?>	
		                    

							<?php } ?>
                    
					
				<?php  }else{
				
				$i = 0;	
				foreach ( $item_totals as $total ) {
					$i++;
					
					
					?>
					<tr>
						<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php  echo wp_kses_post( $total['label'] ); ?></th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i  ) ? 'border-top-width: 4px;' : ''; ?>"><?php if($result){ if($total['label']== 'Delsum:' || $total['label']== 'Totalt:') {echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $normal_price )); }else {echo wp_kses_post( $total['value'] );}}else {echo wp_kses_post( $total['value'] );} ?></td>
					</tr>
					<?php
				} }
					
			}
			if ( $order->get_customer_note() ) {
				?>
				<tr>
					<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
				<?php
			}
			?>
		</tfoot>
	</table>
</div>
<?php


if($extra_prices > 0 && $order->get_total() >= $extra_prices){
  $product_price = $order->get_total() - $extra_prices; 
  //$product_price = get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $product_price ));
  ?>

    <script>
	  document.querySelector(".woocommerce-Price-amount").innerHTML = "<?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $product_price ));?>";
	  document.querySelector(".product_price").innerHTML = "<?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $product_price ));?>";
	</script>
  <?php
}
?>
<?php if($coupen_exist){ 
	$product_price = $order->get_total(); 
	?>
	<script>
	  document.querySelector(".woocommerce-Price-amount").innerHTML = "<?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $product_price ));?>";
	  document.querySelector(".product_price").innerHTML = "<?php echo get_woocommerce_currency_symbol().' '; echo sprintf('%.2f',wp_kses_post( $product_price ));?>";
	</script>
<?php } ?>
<style>
	#chat-widget-push-to-talk, #welcomeMessages {
		display: none;
	}
</style>

<?php
// do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); 

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>


