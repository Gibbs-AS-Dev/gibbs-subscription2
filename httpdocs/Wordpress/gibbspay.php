<?php
/**
 * Filename: gibbspay.php
 * 
 * Version history:
 * 
 * Version 1.0.1 - 2024-08-30
 * -fixed pay_to_confirm
 */

// Your PHP code starts here

?>


<?php
ob_start();

// Send headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Your content goes here

ob_end_flush();
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/dibs-easy-for-woocommerce/dibs-easy-for-woocommerce.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/dibs-easy-for-woocommerce/includes/nets-easy-functions.php';

  $redirect_url = home_url();
  if(isset($_GET["paymentId"])){

    $ress = getPaymentData($_GET["paymentId"]);
    if(!empty($ress) && isset($ress["charges"]) && !empty($ress["charges"])){

      $meta_key = '_dibs_payment_id';
      $meta_value = $_GET["paymentId"];
      
      // Query to get the post ID(s)
      $query = $wpdb->prepare("
          SELECT post_id 
          FROM $wpdb->postmeta 
          WHERE meta_key = %s 
          AND meta_value = %s 
          LIMIT 1
      ", $meta_key, $meta_value);

      $post_id = $wpdb->get_var($query);
      if($post_id){
        $order = wc_get_order($post_id);

        if ($order) {
            // Generate the receipt URL
            $receipt_url = $order->get_checkout_order_received_url();
            wp_redirect($receipt_url);
          
          exit;

        } 
      }
    }else{
       wp_redirect(home_url());
       exit;
    }
    
    
    
  }
  unset($_SESSION["return_url"]);

  if(isset($_GET["order_id"]) && $_GET["order_id"] != ""){
      $order_id = base64_decode($_GET["order_id"]); 

      $order = wc_get_order( $order_id );

      global $wpdb;

      $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE order_id = $order_id";

      $data = $wpdb->get_row($sql);

      if(isset($data->listing_id)){
        $redirect_url = get_the_permalink($data->listing_id);
      }

      



			
      $payment_id = get_post_meta( $order_id, '_dibs_payment_id', true );
      $return_url = get_post_meta( $order_id, 'return_url', true );

      $_SESSION["return_url"] = $return_url;

      if($return_url == ""){
        die;
      }
  }else{
    die("");
  }
  function init_assets($payment_id){

      $settings = get_option( 'woocommerce_dibs_easy_settings' );

      $test_mode  = $settings['test_mode'];
      $script_version = "";

     
      $src = WC_DIBS__URL . '/assets/js/nets-easy-for-woocommerce' . $script_version . '.js';

      $standard_woo_checkout_fields = array(
        'billing_first_name',
        'billing_last_name',
        'billing_address_1',
        'billing_address_2',
        'billing_postcode',
        'billing_city',
        'billing_phone',
        'billing_email',
        'billing_state',
        'billing_country',
        'billing_company',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_postcode',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_company',
        'terms',
        'account_username',
        'account_password',
      );


      wp_register_script(
        'checkout',
        $src,
        array(
          'jquery',
          'dibs-script',
        ),
        WC_DIBS_EASY_VERSION,
        false
      );
      $private_key = 'yes' === $test_mode ? $settings['dibs_test_checkout_key'] : $settings['dibs_checkout_key'];


      $data = array(
          'dibs_payment_id'                  => $payment_id,
          'checkoutInitiated'                => "yes",
          'standard_woo_checkout_fields'     => $standard_woo_checkout_fields,
          'dibs_process_order_text'          => __( 'Please wait while we process your order...', 'dibs-easy-for-woocommerce' ),
          'required_fields_text'             => __( 'Please fill in all required checkout fields.', 'dibs-easy-for-woocommerce' ),
          'customer_address_updated_url'     => WC_AJAX::get_endpoint( 'customer_address_updated' ),
          'get_order_data_url'               => WC_AJAX::get_endpoint( 'get_order_data' ),
          'submitOrder'                      => WC_AJAX::get_endpoint( 'checkout' ),
          'dibs_add_customer_order_note_url' => WC_AJAX::get_endpoint( 'dibs_add_customer_order_note' ),
          'change_payment_method_url'        => WC_AJAX::get_endpoint( 'change_payment_method' ),
          'log_to_file_url'                  => WC_AJAX::get_endpoint( 'dibs_easy_wc_log_js' ),
          'log_to_file_nonce'                => wp_create_nonce( 'dibs_easy_wc_log_js' ),
          'nets_checkout_nonce'              => wp_create_nonce( 'nets_checkout' ),
          'privateKey'                       => $private_key,
          'locale'                           => wc_dibs_get_locale(),
        );
      wp_localize_script(
        'checkout',
        'wcDibsEasy',
        $data
      );
      wp_enqueue_script( 'checkout' );
      if ( 'yes' === $settings['test_mode'] ) {
         $script_url =  'https://test.checkout.dibspayment.eu/v1/checkout.js?v=1';
      }else{
         $script_url =  'https://checkout.dibspayment.eu/v1/checkout.js?v=1';
      }
      
      wp_enqueue_script( 'dibs-script', $script_url, array( 'jquery' ), WC_DIBS_EASY_VERSION, true );
  }

  init_assets($payment_id);

    
     
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php wp_head();?>
  </head>
  <body onload="initialise()">
    <div id="paymentBox"></div>
  </body>  
  <?php wp_footer();?>
  <script type="text/javascript">
  var myNewURL = "<?php echo home_url();?>/gibbspay.php";//the new URL
  window.history.pushState("", "Pay", myNewURL );
    function initialise()
    {
      var parameters, paymentId, paymentOptions, payment, paymentBox;

      paymentOptions =
        {
          checkoutKey: wcDibsEasy.privateKey,
          paymentId: wcDibsEasy.dibs_payment_id,
          containerId: 'paymentBox'
        };
      payment = new Dibs.Checkout(paymentOptions);
      payment.on('payment-completed', paymentComplete);
    }

  function paymentComplete(response)
  {
    window.location.href="<?php echo $return_url;?>&payment_id="+wcDibsEasy.dibs_payment_id
  }

  setTimeout(function(){
    window.location.href="<?php echo $redirect_url;?>";
  },15 * 60 * 1000)

  </script>
</html>
