<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs utbetaling </title>
    <meta name="robots" content="noindex,nofollow" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0;" />
    <style type="text/css">
      table{
        width: 100%;
      }
      .page_break{
        page-break-before: always;
      }
    </style>
</head>
<body>
    <!-- Header -->
    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="border-radius: 10px; margin-top: 120px;">

<tr>
    <td>
        <table border="0" cellpadding="0" cellspacing="10" align="center" class="fullPadding">
        <tbody>
    <tr>
        <td align="center">
            <?php $logo =  get_stylesheet_directory_uri() . "/assets/images/logo_mail.png"; ?>
            <img src="<?php echo $logo; ?>" width="150" height="75" alt="logo" border="0" />
            <br>
            <br>
            <br>
            <b>Gibbs AS</b> <br>
            <span style="font-size: 16px; color: #000; font-family: 'Open Sans', sans-serif; line-height: 24px;">
                Org. nr: NO 922 265 739 MVA<br>
                Foretaksregisteret<br>
                Storgata 15, 1767 Halden
            </span>
            <br><br><br>
        </td>
    </tr>
    <tr>
    <td style="font-size: 20px; color: #000; font-family: 'Open Sans', sans-serif; line-height: 24px; vertical-align: top; text-align: center; margin-top: 20px;"> <br>
    <b>Vi har foretatt utbetaling til:</b> <br>
    <span style="font-size: 16px; color: #000; font-family: 'Open Sans', sans-serif; line-height: 24px; white-space: pre-line;">
        <?php echo esc_html__(get_user_meta($user_data->ID, "listeo_core_bank_details", true)); ?>
    </span>
</td>

    </tr>
</tbody>

        </table>
    </td>
</tr>
</table>

<p style="text-align: center; font-size: 14px; font-family: 'Open Sans', sans-serif; margin-top: 50px;">
<b>Totalt utbetalt: </b> <br>
<span style="text-align: center; font-size: 24px; font-family: 'Open Sans', sans-serif; "> <b> <?php echo wc_price($payout['amount']); ?></b></span>
</p>

<!-- Order Details -->
<?php 
$commission_class = new Listeo_Core_Commissions;
$commissions = array();
$commissions_ids = json_decode($payout['orders']);
foreach ($commissions_ids as $id) {
    $commissions[$id] = $commission_class->get_commission($id);
}

$balance = 0;
if(!empty($commissions)){
    $dates = array_column($commissions, 'date');
    $minDate = min($dates);
    $maxDate = max($dates);
?>
<p style="text-align: center; font-size: 14px; font-family: 'Open Sans', sans-serif; margin-top: 50px;">
<span style="text-align: center; font-size: 20px; font-family: 'Open Sans', sans-serif; "> <b> <?php echo date("Y-m-d",strtotime($minDate)); ?> - <?php echo date("Y-m-d",strtotime($maxDate)); ?></b></span>
</p>

<?php } ?>
<br><br><br><br>
<p style="text-align: center; ">Se utregning på neste side </p>


    <!-- /Header -->
    
        <?php if($commissions) {
            
            ?>
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding page_break">
                                <tbody>
                                    <tr>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 10px 7px 0;" width="30%" align="left">
                                            Produkt
                                        </th>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="left">
                                            <small>Order ID</small>
                                        </th>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="center">
                                        Ordre sum
                                        </th>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="right">
                                            Transaksjonsavgift*
                                        </th>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="right">
                                        Utbetalt
                                        </th>
                                    </tr>
                                    <tr>
                                       <td height="1" style="background: #bebebe;" colspan="5"></td>
                                    </tr>
                                    <tr>
                                       <td height="1" colspan="4"></td>
                                    </tr>
                                    <?php 
                                    $total_order_value = 0;
                                    $site_fee_v = 0;
                                    $payout_v = 0;
                                    foreach ($commissions as $commission) { 
                                        $order = wc_get_order( $commission['order_id'] );
                                        $gift_data = array();
                                        if(!$order){
                                            continue;
                                        }

                    
                                        if($order->get_type() != "shop_order_refund"){
                                            $bk_idd = get_post_meta($order->id,"booking_id",true);
                                            $gift_booking_id = get_post_meta($order->id,"gift_booking_id",true);
                                            if($bk_idd != ""){

                                                $dibs_payment_id = get_post_meta($order->id,"_dibs_charge_id",true);
                                                if($dibs_payment_id != ""){
                                                    
                                                }else{
                                                    continue;
                                                }
                                                
                                            }elseif($gift_booking_id != ""){

                                                if(class_exists("Class_Gibbs_Giftcard")){
                    
                                                    $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;
                                        
                                                    $data = $Class_Gibbs_Giftcard->getGiftDataByBookingId($gift_booking_id);
                                        
                                                    if($data && isset($data["id"])){
                    
                                                        $data["title"] = get_the_title($data["id"]);
                    
                                                        $gift_data = $data;
                    
                                                    }
                                                } 
                    
                    
                    
                                                $dibs_payment_id = get_post_meta($order->id,"_dibs_charge_id",true);
                                                if($dibs_payment_id != ""){
                                                    
                                                }else{
                                                    continue;
                                                }
                            
                                            }else{
                                                continue;
                                            }
                                        }    
                                    // if($order && $order->payment_method != "cod") {



                                            $total = $order->get_total();
                                            if($order->get_type() != "shop_order_refund"){
                                                if($total && $total < 1){
                                                    continue;
                                                }
                                            }    
                                            $earning = $total - $commission['amount'];
                                            $balance = (float) $balance + $earning;

                                            $total_order_value += $total;
                                            $site_fee_v += $commission['amount'];
                                            $payout_v += $earning;
                                            // if($order->get_type() == "shop_order_refund"){
                                            //     $commission['amount'] = "-".$commission['amount'];
                                            // }
                                            $currency = $order->get_currency();
                                            
                                    ?>
                                    <tr>
                                        
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" class="article">
                                            <?php if(!empty($gift_data) && isset($gift_data["title"])){ ?>
                                                <?php echo $gift_data["title"]; ?>
                                            <?php }else{ ?>
                                                <?php echo get_the_title($commission['listing_id']) ?>
                                            <?php } ?>
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;"><small><?php echo $order->id;?></small></td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="center">
                                           <?php echo wc_price($total,array('currency' => $currency)); ?>
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right">
                                        <?php echo wc_price($commission['amount'],array('currency' => $currency)); ?>
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right">
                                          <?php echo wc_price($earning,array('currency' => $currency)); ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                       <td height="1" style="background: #bebebe;" colspan="5"></td>
                                    </tr>
                                    
                                    <tr>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" class="article">
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;"><small></small></td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="center">
                                        <?php echo wc_price($total_order_value,array('currency' => $currency)); ?>
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right">
                                        <?php echo wc_price($site_fee_v,array('currency' => $currency)); ?>
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right">
                                        <?php echo wc_price($payout_v,array('currency' => $currency)); ?>
                                        </td>
                                    </tr>
                                </tbody>
                                </table>
                
                <!DOCTYPE html>
<html>
<head>
<style>
  .container {
    display: flex;
  }

  .left-column {
    background-color: #fff;
    color: #000;
    padding: 10px;
    flex: 1;
  }

  .right-column {
    flex: 2;
    padding: 10px;
  }

  .highlighted {
    background-color: lightyellow;
  }
</style>
</head>
<body>

  <div class="right-column">
  <p>*Transaksjonsavgift gjelder for "Vipps/Visa/Mastercard" </p>
  <p>Vedrørende spørsmål angående utbetaling, send e-post til <a href="mailto:kontakt@gibbs.no">kontakt@gibbs.no</a></p>
  
  <!--   <p>Gibbs-teamet takker for samarbeidet!</p> -->
  </div>
</div>

</body>
</html>

        <?php } ?>        
    </body>
</html>