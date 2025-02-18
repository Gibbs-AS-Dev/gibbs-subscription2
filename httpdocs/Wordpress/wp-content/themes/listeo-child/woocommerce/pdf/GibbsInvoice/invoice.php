<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); 
$user = wp_get_current_user();
$user_name = get_user_meta( intval($user), 'billing_first_name',true);
$company_name = get_user_meta( intval($user), 'billing_company',true);
$user_address = get_user_meta(intval($user), 'billing_address_1',true);
$user_personal_number = get_user_meta(intval($user),'personal_number',true);
$user_company_number = get_user_meta(intval($user),'company_number',true);

$type = get_user_meta(intval($user),'cptype',true);
?>

<table class="head container">
	<tr>
		<td class="header">
			<h1 class="document-type-label">
				Faktura utkast
			</h1>
		</td>
		<td class="shop-info" id="top-right">
		</td>
	</tr>
</table>



<?php do_action( 'wpo_wcpdf_after_document_label', $this->type, $this->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			
		</td>
		<td class="address shipping-address">
			<h3><?php _e( 'Til:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
			<table>
			
			<?php 
				if($type == "person"){
				?>
					<tr>
						<td><?php echo 'Navn: '?></th>
						<td><?php  echo $user_name; ?></td>
					</tr>
					<tr>
						<td><?php echo 'Personnummer: '?></th>
						<td><?php  echo $user_personal_number; ?></td>
					</tr>
					<tr>
						<td><?php echo 'Addresse: '?></th>
						<td><?php  echo $user_address; ?></td>
					</tr>
				<?php
				}else{
				?>
					<tr>
						<td><?php echo 'Organisasjons navn: '?></th>
						<td><?php  echo $company_name; ?></td>
					</tr>
					<tr>
						<td><?php echo 'Org nr: '?></th>
						<td><?php  echo $user_company_number; ?></td>
					</tr>
					<tr>
						<td><?php echo 'Addresse: '?></th>
						<td><?php  echo $user_address; ?></td>
					</tr>
				<?php
				}
			?>
			
			</table>
		</td>
		<td class="order-data">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $this->type, $this->order ); ?>
				
				<?php if ( isset($this->settings['display_date']) ) { ?>
				<tr class="invoice-date">
					<th><?php _e( 'Faktura dato:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->invoice_date(); ?></td>
				</tr>
				<?php } ?>
				<tr class="order-date">
					<th><?php _e( 'Ordre dato:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->order_date(); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $this->type, $this->order ); ?>
			</table>			
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->type, $this->order ); ?>

<table class="order-details">
	<thead>
		<tr>
			<th class="product"><?php _e('Beskrivelse', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="quantity"><?php _e('Antall personer', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price"><?php _e('Pris', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			
		</tr>
	</thead>
	<tbody>
		<?php $items = $this->get_order_items(); if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?>
		<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
			<td class="product">
				<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
				<span class="item-name"><?php echo $item['name']; ?></span>
				<?php do_action( 'wpo_wcpdf_before_item_meta', $this->type, $item, $this->order  ); ?>
				<span class="item-meta"><?php echo $item['meta']; ?></span>
				<span class="item-meta"><?php
				// $date1="30-12-1899 9:25:52 AM";
				// $format = 'd-m-Y H:i:s A';
				// $date = DateTime::createFromFormat($format, $date1);
				// echo $date->format('H:i:s A') . "\n";
				echo $this->invoice_date();?></span>

				<dl class="meta">
					<?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
					<?php if( !empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
					<?php if( !empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; ?>
				</dl>
				<?php do_action( 'wpo_wcpdf_after_item_meta', $this->type, $item, $this->order  ); ?>
			</td>
			<td class="quantity"><?php echo $item['quantity']; ?></td>
			<td class="price"><?php echo $item['order_price']; ?></td>
		</tr>
		<?php endforeach; endif; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="document-notes">
					<?php do_action( 'wpo_wcpdf_before_document_notes', $this->type, $this->order ); ?>
					<?php if ( $this->get_document_notes() ) : ?>
						<h3><?php _e( 'Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->document_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_document_notes', $this->type, $this->order ); ?>
				</div>
				<div class="customer-notes">
					<?php do_action( 'wpo_wcpdf_before_customer_notes', $this->type, $this->order ); ?>
					<?php if ( $this->get_shipping_notes() ) : ?>
						<h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_customer_notes', $this->type, $this->order ); ?>
				</div>				
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<?php foreach( $this->get_woocommerce_totals() as $key => $total ) : ?>
						<tr class="<?php echo $key; ?>">
							<td class="no-borders"></td>
							<th class="description"><?php if($total['label'] == 'Subtotal'){ $total['label'] = 'Delsum';}echo $total['label']; ?></th>
							<td class="price"><span class="totals-price"><?php echo $total['value']; ?></span></td>
						</tr>
						<?php endforeach; ?>
					</tfoot>
				</table>
			</td>
		</tr>
	</tfoot>
</table>

<div class="bottom-spacer"></div>

<?php do_action( 'wpo_wcpdf_after_order_details', $this->type, $this->order ); ?>

<?php if ( $this->get_footer() ): ?>
<div id="footer">
	<?php $this->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wpo_wcpdf_after_document', $this->type, $this->order ); ?>
