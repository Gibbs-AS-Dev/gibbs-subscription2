<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

class Listeo_Core_Payouts {

    private static $_instance = null;
    /**
     * Main plugin Instance
     *
     * @static
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    public function __construct() {
        
        add_action( 'init', array( $this, 'load_func' ) );   
       // self::download_pdf("","","");
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );    
        
        add_action('wp_ajax_send_payout_email', array($this, 'send_payout_email'));
        add_action('wp_ajax_nopriv_send_payout_email', array($this, 'send_payout_email'));  

        add_action('wp_ajax_payout_detail_save2', array($this, 'payout_detail_save2'));
        add_action('wp_ajax_nopriv_payout_detail_save2', array($this, 'payout_detail_save2'));  
        
    }
    

    public function load_func() {

        if(isset($_GET['download_pdf']) && $_GET['download_pdf'] == true){
            $id = absint( $_GET['view_payout'] ); 
            $payout = Listeo_Core_Payouts::get_payout($id);

            $user_data = get_userdata($payout['user_id']); 
            self::download_pdf($payout,$user_data,$id);
        }

    }


    public function add_menu_item() {
        
            $args = apply_filters( 'listeo_core_commissions_menu_items', array(
                    'page_title' => __( 'Commissions', 'listeo_core' ),
                    'menu_title' => __( 'Commissions', 'listeo_core' ),
                    'capability' => 'edit_products',
                    'menu_slug'  => 'listeo_payouts',
                    'function'   => array( $this, 'commissions_details_page' ),
                    'icon'       => 'dashicons-tickets',
                    'position'   => 58 /* After WC Products */
                )
            );

            extract( $args );

            add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position );
            add_submenu_page( 'listeo_payouts', 'Payouts History', 'Payouts History', 'edit_products', 'listeo_payouts_list', array( $this, 'payouts_details_page' ) );
    }


    public function make_payout(){

        
        
        global $wpdb;
        
        $orders_list = array();  
        $balance = 0;  
        if(isset($_POST['commission']) && !empty($_POST['commission'])){
            if(is_array($_POST['commission'])){
                foreach ($_POST['commission'] as $key => $value) {
                    $orders_list[] = $key;
                    $wpdb->update( $wpdb->prefix . "listeo_core_commissions", array('status'=>'paid'), array( 'id' => $key ) );
                    $order_id = $wpdb->get_var( $wpdb->prepare( 
                        '
                            SELECT order_id 
                            FROM '.$wpdb->prefix.'listeo_core_commissions
                            WHERE id = %s
                        ', 
                        $key
                    ) );
                    $amount = $wpdb->get_var( $wpdb->prepare( 
                        '
                            SELECT amount 
                            FROM '.$wpdb->prefix.'listeo_core_commissions
                            WHERE id = %s
                        ', 
                        $key
                    ) );

                    if($amount == ""){
                        $amount = 0;
                    }
                    
                    //get order_id where id=key

                    if($order_id && $order_id != ""){

                        $order = wc_get_order( $order_id );
                        $total = $order->get_total();
                        $earning = (float) $total - $amount;
                        $balance = $balance + $earning;
                    }
                }
            }
        }
        $orders = json_encode($orders_list);

        //$balance = $commission->calculate_totals( array( 'user_id'=> $_POST['user_id'],'status' => 'unpaid' ) );
        
        $args = array(
            'user_id'         => $_POST['user_id'],
            'status'          => 'paid',
            'orders'          => $orders,
            'date'            => current_time('mysql'),
            'amount'          => $balance,
            'payment_method'  => $_POST['payment_method'],
            'payment_details'  => $_POST['payment_details']

        );        

        $wpdb->insert( $wpdb->prefix . "listeo_core_commissions_payouts", $args );

        if($wpdb->insert_id){
            echo '<div class="updated"><p>'.esc_html__('Payout was created.','listeo_core').'</p></div>';
            $id = $wpdb->insert_id; 
            ?>
            <script>
                window.location.href = "<?php echo home_url();?>/wp-admin/admin.php?page=listeo_payouts_list&view_payout=<?php echo $id;?>"
            </script>
            <?php
            exit;
            $payout = $this->get_payout($id);
            
            $user_data = get_userdata($payout['user_id']); 
            ?>
            <div class="wrap">

                <h2>Payout details</h2>
                <div class="payout-make-box">
                <ul>
                    <li><span><?php esc_html_e( 'Payment for', 'listeo_core' ); ?></span> <?php echo $user_data->display_name; ?></li>
                    <li><span><?php esc_html_e('Payment date','listeo_core'); ?>:</span> <?php echo date(get_option( 'date_format' ), strtotime($payout['date']));  ?></li>
                    <li><span><?php esc_html_e('Payment amount','listeo_core'); ?>:</span>  <?php echo wc_price($payout['amount']); ?></li>
                    <li><span><?php esc_html_e('Payment method','listeo_core'); ?>:</span> <?php echo ($payout['payment_method'] == 'paypal') ? 'PayPal' : 'Bank Transfer' ; ?></li>
                    <li><span><?php esc_html_e('Payment details','listeo_core'); ?>:</span>  
                        <textarea cols="30" rows="10" disabled="disabled"><?php echo ($payout['payment_details']); ?></textarea></li>
                       
                </ul>
                </div>

                <?php 
                $commission_class = new Listeo_Core_Commissions;
                $commissions = array();
                $commissions_ids = json_decode($payout['orders']);
                foreach ($commissions_ids as $id) {
                    $commissions[$id] = $commission_class->get_commission($id);
                }
                $balance = 0;
                ?>
                <?php if($commissions) {?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Listing Title</th>
                                <th>Total Order value</th>
                                <th>Site Fee</th>
                                <th>User Earning</th>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <?php 
                        foreach ($commissions as $commission) { 
                            $gift_data = array();
                            $order = wc_get_order( $commission['order_id'] );

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
                            $total = $order->get_total();
                            if($total && $total < 1){
                                 continue;
                            }
                            $earning = $total - $commission['amount'];
                            $balance = (float) $balance + $earning;
                            $currency = $order->get_currency();
                            ?>
                            <tr>
                                <td>
                                    
                                    <?php if(!empty($gift_data) && isset($gift_data["title"])){ ?>
                                        <?php echo $gift_data["title"]; ?>
                                    <?php }else{ ?>
                                        <?php echo get_the_title($commission['listing_id']) ?>
                                    <?php } ?>
                                </td>
                                <td class="paid"><?php echo  $currency." ".wc_price($total,array('currency' => "",'price_format' => '%2$s')); ?></td>
                                <td class="unpaid"><?php echo $currency." ".wc_price($commission['amount'], array('currency' => "",'price_format' => '%2$s')); ?></td>
                                <td class="paid"> <span><?php echo $currency." ".wc_price($earning, array('currency' => "",'price_format' => '%2$s')); ?></span></td>
                                <td>#<?php echo $commission['order_id']; ?></td>
                                <td><?php echo date(get_option( 'date_format' ), strtotime($commission['date']));  ?></td>
                                <td><?php echo $commission['status']; ?></td>
                                
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
            </div>
            <?php
        } else {
            echo '<div class="updated"><p>Something went wrong</p></div>';
        };

        //for each commission change status to paid
    }

    /**
     * Show the Commissions page
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return void
     *
     */
    public function commissions_details_page() {
        global $wpdb;
        if(isset($_POST['submit_new_payout'])){
               $this->make_payout();
        } else if ( isset( $_GET['make_payout'] ) ) {
            
            $user_id = absint( $_GET['make_payout'] ); 
            $user_data = get_userdata($user_id); 

            ?>
            <div class="wrap">
                <h2><?php esc_html_e( 'New Payment for', 'listeo_core' ); ?> <?php echo $user_data->display_name; ?></h2>
                <div class="payout-make-box">
                    
                
                    <p>By clicking Submit Button you'll mark all his current <strong>Unpaid</strong> commissions as <strong>Paid</strong></p>
                    <p>User's requested Payment Method: 
                        <strong><?php echo ($user_data->listeo_core_payment_type == 'paypal') ? 'PayPal' : 'Bank Transfer' ; ?>
                        </strong> (payment details in next step)
                    </p>
          
                </div>

                <div class="inner-div-payout">

                    <div class="auto-div1" style="width: 42%;">

                        <h2>Payout details</h2>
                        <div class="payout-make-box">
                            <ul>
                                <li><span><?php esc_html_e( 'Payment for', 'listeo_core' ); ?></span> <?php echo $user_data->display_name; ?></li>
                                <li><span><?php esc_html_e( 'Email', 'listeo_core' ); ?></span> <?php echo $user_data->user_email; ?></li>
                                <?php if($user_data->phone != ""){ ?>
                                    <li><span><?php esc_html_e( 'Phone', 'listeo_core' ); ?></span> <?php echo $user_data->phone; ?></li>
                                <?php } ?>
                                <li><span><?php esc_html_e( 'Payment for', 'listeo_core' ); ?></span> <?php echo $user_data->display_name; ?></li>
                                
                                <li><span>Payment details:</span>  
                                    <form method="post" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                                    <input type="hidden" name="action" value="payout_detail_save2">
                                    <textarea cols="30" rows="10" name="listeo_core_bank_details"><?php echo get_user_meta($user_data->ID,"listeo_core_bank_details",true);?></textarea>
                                    <input type="hidden" name="user_id" value="<?php echo $user_data->ID;?>">
                                    <button type="submit" name="payout_detail_save2" class="save_btnn btn btn-primary">Save</button>
                                    </form>
                                    </li>
                                
                            </ul>
                        </div>
                    </div>
                    <div class="auto-div2" style="width: 58%;">
                        <h2>Email</h2>
                        <div class="payout-make-box">
                           
                        <form action="<?php echo admin_url( 'admin-ajax.php' );?>" method="post" enctype="multipart/form-data">
                            <?php if(isset($_GET["success"]) && $_GET["success"] == "true"){ ?>
                                <div class="success_msg">Successfully send email....</div>
                            <?php }
                            ?>
                            <input type="hidden" name="action" value="send_payout_email">
                            <input type="hidden" name="user_id" value="<?php echo $user_data->ID; ?>">
                            <p>
                                <label for="email">
                                    <b>To Email</b>
                                </label>
                                <input name="email" id="email"  size="22" tabindex="1" type="email" value="<?php echo get_user_meta($user_data->ID,'com_email_to',true);?>">
                                    
                            </p>
                            <p>
                                <label for="subject">
                                    <b>Subject</b>
                                </label>
                                <input name="subject" id="subject"  value="<?php echo get_user_meta($user_data->ID,'com_email_subject',true);?>" size="22" tabindex="1" type="text">
                            </p>
                            <p>
                                <label for="email_content">
                                    <b>Email content</b>
                                </label>
                                <textarea name="email_content" id="email_content" cols="100" rows="10" tabindex="4"><?php echo get_user_meta($user_data->ID,'com_email_content',true);?></textarea>

                            </p>
                            <p>
                                <input name="submit" id="submit" tabindex="5" value="Submit" type="submit">
                                <input name="comment_post_ID" value="1" type="hidden">
                            </p>
                            </form>
                            <script>
                                var href = window.location.href;
                                href = href.replace("&success=true","");
                                window.history.pushState({}, document.title, href );
                            </script>
                        </div>
                    </div>
                </div>  
                
                <form method="POST"  id="listeo-make-payout">
                    <input type="submit" name="submit_new_payout" id="submit" class="button button-primary" value="Make Payout">
                    <input type="hidden" name="action" value="listeo_make_payout" />
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />                    
                    <?php if(isset($user_data->listeo_core_payment_type)) { 
                        $payment_method = $user_data->listeo_core_payment_type; 
                    } else {
                        $payment_method = "banktransfer";
                    }?>
                    <input type="hidden" name="payment_method" value="<?php echo esc_attr($payment_method); ?>" />  

                    <?php if($payment_method == 'paypal'): ?>
                        <input type="hidden" name="payment_details" value="<?php echo $user_data->listeo_core_ppemail ?>">
                    <?php endif; ?> 
                    <?php if($payment_method == 'banktransfer'): ?>
                        <input type="hidden" name="payment_details" value="<?php echo esc_attr($user_data->listeo_core_bank_details); ?>">
                    <?php endif; ?>
                    


                <h4>Commissions</h4>
                <?php 
                $commission_class = new Listeo_Core_Commissions;
                $commissions_ids = $commission_class->get_commissions( array( 'user_id'=>$user_id ) );
                $monthsD = array("01" => 'Jan.', "02" => 'Feb.', "03" => 'Mar.', "04" => 'Apr.', "05" => 'May', "06" => 'Jun.', "07" => 'Jul.', "08" => 'Aug.', "09" => 'Sep.', "10" => 'Oct.', "11" => 'Nov.', "12" => 'Dec.');
                $months = array();
                $commissions = array();
                
                foreach ($commissions_ids as $id) {
                    $commissions[$id] = $commission_class->get_commission($id);
                    
                    if(isset($commissions[$id]["date"])){
                        $com = $commissions[$id];
                        $order = wc_get_order( $com['order_id'] );
                             if(!$order){
                                return;
                            }
                            $bk_idd = get_post_meta($order->id,"booking_id",true);
                            if($bk_idd != ""){

                                $dibs_payment_id = get_post_meta($order->id,"_dibs_charge_id",true);
                                if($dibs_payment_id != ""){
                                    
                                }else{
                                    continue;
                                }
                                
                            }else{
                                continue;
                            }
                            // if($order && $order->payment_method == "cod"){
                            //      continue;
                            // }
                            $total = $order->get_total();
                            if($total && $total < 1){
                                 continue;
                            }
                        $dattt = $com["date"];
                        $mmm = date("m",strtotime($dattt));

                        if(isset($_GET["month"]) && $_GET["month"] != ""){

                            $months = $monthsD;

                        }else{

                            foreach($monthsD as $key_d => $montd){
                                if($key_d == $mmm){
                                    $months[$key_d] = $montd;
                                }
                            }

                        }    

                    }
                   // echo "<pre>"; print_r($commissions[$id]); die;
                }
                if(empty($months)){
                    $months = $monthsD;
                }
                $balance = 0;

                $current_year = date('Y'); // Get the current year
                $start_year = 2000; // Define the starting year
                $years = array_reverse(range($start_year, $current_year));
                
                ?>
                <div class="rda is-style-responsive">
                        <div style="display:inline-block; padding:14px 1px">
                            <label for="month_com">Select Month:</label>
                            <select id="month_com">
                                <option value="">–Choose an option–</option>
                                <?php foreach($months as $key_m => $month){ ?>
                                <option value="<?php echo $key_m;?>" <?php if($key_m == $_GET["month"]){echo "selected";}?>><?php echo $month;?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div style="display:inline-block; padding:14px 1px">
                            <label for="year_com">Select Year:</label>
                            <select id="year_com">
                                <option value="">–Choose an option–</option>
                                <?php foreach ($years as $year) { ?>
                                    <option value="<?php echo $year; ?>" <?php if (isset($_GET['year']) && $_GET['year'] == $year) { echo 'selected'; } ?>>
                                        <?php echo $year; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                </div>
                <?php if($commissions) {?>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Listing Title</th>
                                <th>Total Order value</th>
                                <th>Site Fee</th>
                                <th>User Earning</th>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <?php 

                        //echo "<pre>"; print_r($commissions);

                        foreach ($commissions as $commission) { 
                            $gift_data = array();
                            $order = wc_get_order( $commission['order_id'] );
                             if(!$order){
                                return;
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
                            // if($order && $order->payment_method == "cod"){
                            //      continue;
                            // }
                            $total = $order->get_total();
                            if($order->get_type() != "shop_order_refund"){
                                if($total && $total < 1){
                                    continue;
                                }
                            }    
                            $earning = $total - $commission['amount'];
                            $balance = (float) $balance + $earning;
                            $currency = $order->get_currency();
                            ?>
                            <tr>
                                <td><input type="checkbox"  name="commission[<?php echo $commission['id']; ?>]" <?php if(isset($_GET["month"]) && $_GET["month"] != ""){echo "checked";}?>></td>
                                <td>
                                    <?php if(!empty($gift_data) && isset($gift_data["title"])){ ?>
                                        <?php echo $gift_data["title"]; ?>
                                    <?php }else{ ?>
                                        <?php echo get_the_title($commission['listing_id']) ?>
                                    <?php } ?>
                                    
                                </td>
                                <td class="paid"><?php echo $currency." ".wc_price($total, array('currency' => "",'price_format' => '%2$s')); ?></td>
                                <td class="unpaid"><?php echo $currency." ".wc_price($commission['amount'], array('currency' => "",'price_format' => '%2$s')); ?></td>
                                <td class="paid"> <span><?php echo $currency." ".wc_price($earning, array('currency' => "",'price_format' => '%2$s')); ?></span></td>
                                <?php if($order->get_type() == "shop_order_refund"){ ?>
                                    <td>#<?php echo $order->get_parent_id(); ?></td>
                                <?php  }else{ ?>
                                    <td>#<?php echo $commission['order_id']; ?></td>
                                <?php } ?> 
                               
                                <td><?php echo date(get_option( 'date_format' ), strtotime($commission['date']));  ?></td>
                                <?php if($order->get_type() == "shop_order_refund"){ ?>
                                     <td>Refunded</td>
                                <?php  }else{ ?>
                                    <td><?php echo $commission['status']; ?></td>
                                <?php } ?>    
                                
                            </tr>
                        <?php } ?>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right;">Total </td>
                                <td><?php echo wc_price($balance); ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
                <br>
                                   
                <input type="submit" name="submit_new_payout" id="submit" class="button button-primary" value="Make Payout">
                </form>
                <script>
                    jQuery("#month_com").change(function(){

                        

                        var locc ="<?php echo home_url();?>/wp-admin/admin.php?page=listeo_payouts&make_payout=<?php echo $_GET['make_payout'];?>";
                        if(this.value != ""){
                            locc +="&month="+this.value;
                        }
                        if(jQuery("#year_com").val() != ""){
                            locc +="&year="+jQuery("#year_com").val();
                        }
                        window.location.href = locc;
                    })
                    jQuery("#year_com").change(function(){

                        var locc ="<?php echo home_url();?>/wp-admin/admin.php?page=listeo_payouts&make_payout=<?php echo $_GET['make_payout'];?>";
                        if(this.value != ""){
                            locc +="&year="+this.value;
                        }
                        if(jQuery("#month_com").val() != ""){
                            locc +="&month="+jQuery("#month_com").val();
                        }
                        window.location.href = locc;

                    })
                </script>
               
            </div>

        <?php } 
        
        else {
            if ( ! class_exists( 'WP_List_Table' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
            }
           
           
            $balances_table = new Listeo_Balances_List_Table();
            $balances_table->prepare_items();  ?>
            <div class="wrap">

                <h2>Users balances</h2>

                <?php $balances_table->views(); ?>

                <form id="commissions-filter" method="get">
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                    <?php // $commissions_table->add_search_box( __( 'Search commissions', 'listeo_core' ), 's' ); ?>
                    <?php $balances_table->display(); ?>
                </form>

            </div>
            <?php
        }
    }

    public function download_pdf($payout,$user_data,$id,$type = "") {

        $pathh =   plugin_dir_path( __FILE__ ).'vendor/autoload.php';

        $path = str_replace("/includes","", $pathh);
        

        require_once $path;
        ob_start();

        $template_pathh =   get_stylesheet_directory().'/templates/payout-pdf.php';

        $path = str_replace("/includes","", $pathh);

        $options = new Dompdf\Options();

        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);


        // Instantiate and use the dompdf class 
        $dompdf = new Dompdf\Dompdf($options);

        // Load content from html file 
        ob_start();
        include $template_pathh;
        $html = ob_get_clean();
        //echo $html; die;
        $dompdf->load_html($html); 

        
        // Render the HTML as PDF 
        $dompdf->render(); 

        ob_end_clean(); 

        $dd = "";

        if(isset($payout["date"])){
             $dd = date("F");
        }

        $namee = "payout-".$user_data->display_name.".pdf";

        if($type == "mail"){
            $output = $dompdf->output();
            $path = get_stylesheet_directory().'/'.$namee;
            file_put_contents($path, $output);
            return $path;
        }else{
            $dompdf->stream($namee,array('Attachment'=>1));
        }
        
        // Output the generated PDF (1 = download and 0 = preview) 
        

        //$dompdf->stream('my.pdf',array('Attachment'=>0));
        exit;

    }
    public function payout_detail_save2(){

        update_user_meta($_POST["user_id"],"listeo_core_bank_details",$_POST["listeo_core_bank_details"]);

        
        $location = $_SERVER['HTTP_REFERER'];
        wp_safe_redirect($location);
        exit();
    }

    public function send_payout_email(){
        
        if(isset($_POST["user_id"])){

            update_user_meta($_POST["user_id"],"com_email_to",$_POST["email"]);
            update_user_meta($_POST["user_id"],"com_email_subject",$_POST["subject"]);
            update_user_meta($_POST["user_id"],"com_email_content",$_POST["email_content"]);

            $attachments = array();

            if(isset($_POST["payout_id"])){
                $id = absint( $_POST["payout_id"] ); 
                $payout = Listeo_Core_Payouts::get_payout($id);

                $user_data = get_userdata($_POST['user_id']); 
                $path = self::download_pdf($payout,$user_data,$id, "mail");
                if($path != ""){
                    $attachments[] = $path;
                }
            }

            if(isset($_FILES["attachment"]['name']) && $_FILES["attachment"]['name'] != ""){

                if ( ! function_exists( 'wp_handle_upload' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                $upload_overrides = array('test_form' => false);

                $uploadedfile = array(
                    'name'     => $_FILES["attachment"]['name'],
                    'type'     => $_FILES["attachment"]['type'],
                    'tmp_name' => $_FILES["attachment"]['tmp_name'],
                    'error'    => $_FILES["attachment"]['error'],
                    'size'     => $_FILES["attachment"]['size']
                );
                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

                

                if(isset($movefile["file"])){
                    $attachments[] = $movefile["file"];
                }
            }    


            $to = $_POST["email"]; 

            $email_from = get_option('admin_email');

            $email_subject = $_POST["subject"];
            $email_body = $_POST["email_content"];

            $headers = "From: $email_from \r\n";
            $headers .= "Reply-To: $email \r\n"; //$email is a SESSION variable pulled from before
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            //set to html
            add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

            //send
            echo wp_mail( $to, $email_subject, $email_body, $headers, $attachments );
        }
        
        $location = $_SERVER['HTTP_REFERER'];

        $location = str_replace("&success=true","",$location);

        $location = $location."&success=true";
        wp_safe_redirect($location);
        exit();
    }

    public function payouts_details_page() {

        global $wpdb;

        if(isset($_POST["payout_detail_save"])){

           
            update_user_meta($_POST["user_id"],"listeo_core_bank_details",$_POST["listeo_core_bank_details"]);
           
        }

        
        
        if ( isset( $_GET['view_payout'] ) ) {
            
            $id = absint( $_GET['view_payout'] ); 
            $payout = $this->get_payout($id);
            

            //echo "<pre>"; print_r($payout); die;

            
            
            $user_data = get_userdata($payout['user_id']); 

            
            ?>
            <div class="wrap">

                <div class="inner-div-payout">

                    <div class="auto-div1" style="width: 42%;">

                        <h2>Payout details</h2>
                        <div class="payout-make-box">
                            <ul>
                                <li><span><?php esc_html_e( 'Payment for', 'listeo_core' ); ?></span> <?php echo $user_data->display_name; ?></li>
                                <li><span><?php esc_html_e( 'Email', 'listeo_core' ); ?></span> <?php echo $user_data->user_email; ?></li>
                                <?php if($user_data->phone != ""){ ?>
                                    <li><span><?php esc_html_e( 'Phone', 'listeo_core' ); ?></span> <?php echo $user_data->phone; ?></li>
                                <?php } ?>
                                <li><span><?php esc_html_e( 'Payment for', 'listeo_core' ); ?></span> <?php echo $user_data->display_name; ?></li>
                                <li><span>Payment date:</span> <?php echo date(get_option( 'date_format' ), strtotime($payout['date']));  ?></li>
                                <li><span>Payment amount:</span>  <?php echo wc_price($payout['amount']); ?></li>
                                <li><span>Payment method:</span> <?php echo ($payout['payment_method'] == 'paypal') ? 'PayPal' : 'Bank Transfer' ; ?></li>
                                <li><span>Payment details:</span>  
                                    <form method="post">
                                    <textarea cols="30" rows="10" name="listeo_core_bank_details"><?php echo get_user_meta($user_data->ID,"listeo_core_bank_details",true);?></textarea>
                                    <input type="hidden" name="user_id" value="<?php echo $user_data->ID;?>">
                                    <button type="submit" name="payout_detail_save" class="save_btnn btn btn-primary">Save</button>
                                    </form>
                                    </li>
                                
                            </ul>
                            <div class="download_div">
                                <div class="pdf-btn">
                                    <a href="/wp-admin/admin.php?page=listeo_payouts_list&view_payout=<?php echo $_GET['view_payout'];?>&download_pdf=true"><button class="download-pdf-btn btn btn-primary">Download pdf</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="auto-div2" style="width: 58%;">
                        <h2>Email</h2>
                        <div class="payout-make-box">
                           
                        <form action="<?php echo admin_url( 'admin-ajax.php' );?>" method="post" enctype="multipart/form-data">
                            <?php if(isset($_GET["success"]) && $_GET["success"] == "true"){ ?>
                                <div class="success_msg">Successfully send email....</div>
                            <?php }
                            ?>
                            <input type="hidden" name="action" value="send_payout_email">
                            <input type="hidden" name="user_id" value="<?php echo $user_data->ID; ?>">
                            <input type="hidden" name="payout_id" value="<?php echo $_GET["view_payout"]; ?>">
                            <p>
                                <label for="email">
                                    <b>To Email</b>
                                </label>
                                <input style="width: 95%;" name="email" id="email"  size="22" tabindex="1" type="text" value="<?php echo get_user_meta($user_data->ID,'com_email_to',true);?>">
                                <br><small>Please add emails with commas(,) seperated.</small> 
                            </p>
                            <p>
                                <label for="subject">
                                    <b>Subject</b>
                                </label>
                                <input style="width: 95%;"name="subject" id="subject"  value="<?php echo get_user_meta($user_data->ID,'com_email_subject',true);?>" size="22" tabindex="1" type="text">
                            </p>
                            <p>
                                <label for="email_content">
                                    <b>Email content</b>
                                </label>
                                <textarea style="width: 95%;" name="email_content" id="email_content" cols="120" rows="10" tabindex="4"><?php echo get_user_meta($user_data->ID,'com_email_content',true);?></textarea>

                            </p>
                            <p>
                                <label for="attachment">
                                    <b>Email Attachment</b>
                                </label>
                                <input name="attachment" id="attachment" type="file">
                            </p>
                           
                            <p>
                                <input name="submit" id="submit" tabindex="5" value="Submit" type="submit">
                                <input name="comment_post_ID" value="1" type="hidden">
                            </p>
                            </form>
                            <script>
                                var href = window.location.href;
                                href = href.replace("&success=true","");
                                window.history.pushState({}, document.title, href );
                            </script>
                        </div>
                    </div>
                </div>    

                <?php 
                $commission_class = new Listeo_Core_Commissions;
                $commissions = array();
                $commissions_ids = json_decode($payout['orders']);
                foreach ($commissions_ids as $id) {
                    $commissions[$id] = $commission_class->get_commission($id);
                }

                $balance = 0;
                ?>
                <?php if($commissions) {?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Listing Title</th>
                                <th>Total Order value</th>
                                <th>Site Fee</th>
                                <th>User Earning</th>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <?php 
                        foreach ($commissions as $commission) { 
                            $order = wc_get_order( $commission['order_id'] );
                            if(!$order){
                                continue;
                            }
                            $gift_data = array();
        
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
                                $currency = $order->get_currency();
                                
                                ?>
                                <tr>
                                    <td>
                                        <?php if(!empty($gift_data) && isset($gift_data["title"])){ ?>
                                            <?php echo $gift_data["title"]; ?>
                                        <?php }else{ ?>
                                            <?php echo get_the_title($commission['listing_id']) ?>
                                        <?php } ?>
                                    </td>
                                    <td class="paid"><?php echo $currency." ".wc_price($total, array('currency' => "",'price_format' => '%2$s')); ?></td>
                                    <td class="unpaid"><?php echo $currency." ".wc_price($commission['amount'], array('currency' => "",'price_format' => '%2$s')); ?></td>
                                    
                                    <td class="paid"> <span><?php echo $currency." ".wc_price($earning, array('currency' => "",'price_format' => '%2$s')); ?></span></td>
                                    <?php if($order->get_type() == "shop_order_refund"){ ?>
                                        <td>#<?php echo $order->get_parent_id(); ?></td>
                                    <?php  }else{ ?>
                                        <td>#<?php echo $commission['order_id']; ?></td>
                                    <?php } ?> 
                                    <td><?php echo date(get_option( 'date_format' ), strtotime($commission['date']));  ?></td>
                                    <?php if($order->get_type() == "shop_order_refund"){ ?>
                                        <td>Refunded</td>
                                    <?php  }else{ ?>
                                        <td><?php echo $commission['status']; ?></td>
                                    <?php } ?>    
                                    
                                </tr>
                            <?php //} 
                            }?>
                    </table>
                <?php } ?>
            </div>

        <?php } else {

        
            if ( ! class_exists( 'WP_List_Table' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
            }
           
           
            $payouts_table = new Listeo_Payouts_List_Table();
            
            $payouts_table->prepare_items();           

            ?>
            <div class="wrap">

            

                <h2>Payouts History</h2>

                <?php $payouts_table->views(); ?>

                <form id="commissions-filter" method="get">
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                    <?php  $payouts_table->search_box( __( 'Search....', 'listeo_core' ), 's' ); ?>
                    <?php $payouts_table->display(); ?>
                </form>
            </div>
            <?php

        }
        
    }

    function get_payout($id){
              
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ". $wpdb->prefix . "listeo_core_commissions_payouts WHERE ID = %d", $id ), ARRAY_A );  
    }
    function get_payouts($args){

        global $wpdb;

            $default_args = array(
                'order_id'     => 0,
                'user_id'      => 0,
                'status'       => 'unpaid',
                'm'            => false,
                'number'       => '',
                'offset'       => '',
                'paged'        => '',
                'orderby'      => 'date',
                'order'        => 'DESC',
                'fields'       => 'ids',
                'table'        => $wpdb->prefix . "listeo_core_commissions_payouts"
            );

            $args = wp_parse_args( $args, $default_args );

            $table = $args['table'];

        

            // First let's clear some variables
            $where = '';
            $limits = '';
            $join = '';
            $groupby = '';
            $orderby = '';

            // query parts initializating
            $pieces = array( 'where', 'groupby', 'join', 'orderby', 'limits' );

            if(isset($_GET["s"]) && $_GET["s"] != ""){
                $search = $_GET["s"];
                $where .= $wpdb->prepare( " AND (c.orders like '%$search%' OR c.id = '$search' OR c.user_id = '$search' OR usr.display_name = '$search' OR EXISTS (
                    SELECT * 
                      FROM ptn_listeo_core_commissions as com 
                      WHERE JSON_CONTAINS(c.orders,com.id) AND order_id = '$search'
                  ) ) ");
                $join .= $wpdb->prepare( " left join ".$wpdb->prefix."users as usr ON c.user_id = usr.ID ");
                //$join .= $wpdb->prepare( " left join ".$wpdb->prefix."ptn_listeo_core_commissions as cmp ON cmp.id = c.ID ");
                // $join .= $wpdb->prepare( " left join ".$wpdb->prefix."listeo_core_commissions as cmp ON cmp.user_id = usr.ID ");
            }

            
            if ( ! empty( $args['id'] ) ) {
                $where .= $wpdb->prepare( " AND c.order_id = %d", $args['order_id'] );
            }
            if ( ! empty( $args['user_id'] ) ) {
                $where .= $wpdb->prepare( " AND c.user_id = %d", $args['user_id'] );
            }
          
            if ( ! empty( $args['status'] ) && 'all' != $args['status'] ) {
                if ( is_array( $args['status'] ) ) {
                    $args['status'] = implode( "', '", $args['status'] );
                }
                $where .= sprintf( " AND c.status IN ( '%s' )", $args['status'] );
            }

            if ( 'ASC' === strtoupper( $args['order'] ) ) {
                $args['order'] = 'ASC';
            } else {
                $args['order'] = 'DESC';
            }

            // Order by.
            if ( empty( $args['orderby'] ) ) {
                /*
                 * Boolean false or empty array blanks out ORDER BY,
                 * while leaving the value unset or otherwise empty sets the default.
                 */
                if ( isset( $args['orderby'] ) && ( is_array( $args['orderby'] ) || false === $args['orderby'] ) ) {
                    $orderby = '';
                } else {
                    $orderby = "c.ID " . $args['order'];
                }
            } elseif ( 'none' == $args['orderby'] ) {
                $orderby = '';
            } else {
                $orderby_array = array();
                if ( is_array( $args['orderby'] ) ) {
                    foreach ( $args['orderby'] as $_orderby => $order ) {
                        $orderby = addslashes_gpc( urldecode( $_orderby ) );

                        if ( ! is_string( $order ) || empty( $order ) ) {
                            $order = 'DESC';
                        }

                        if ( 'ASC' === strtoupper( $order ) ) {
                            $order = 'ASC';
                        } else {
                            $order = 'DESC';
                        }

                        $orderby_array[] = $orderby . ' ' . $order;
                    }
                    $orderby = implode( ', ', $orderby_array );

                } else {
                    $args['orderby'] = urldecode( $args['orderby'] );
                    $args['orderby'] = addslashes_gpc( $args['orderby'] );

                    foreach ( explode( ' ', $args['orderby'] ) as $i => $orderby ) {
                        $orderby_array[] = $orderby;
                    }
                    $orderby = implode( ' ' . $args['order'] . ', ', $orderby_array );

                    if ( empty( $orderby ) ) {
                        $orderby = "c.ID " . $args['order'];
                    } elseif ( ! empty( $args['order'] ) ) {
                        $orderby .= " {$args['order']}";
                    }
                }
            }

            // Paging
            if ( ! empty($args['paged']) && ! empty($args['number']) ) {
                $page = absint($args['paged']);
                if ( !$page )
                    $page = 1;

                if ( empty( $args['offset'] ) ) {
                    $pgstrt = absint( ( $page - 1 ) * $args['number'] ) . ', ';
                }
                else { // we're ignoring $page and using 'offset'
                    $args['offset'] = absint( $args['offset'] );
                    $pgstrt      = $args['offset'] . ', ';
                }
                $limits = 'LIMIT ' . $pgstrt . $args['number'];
            }

            $clauses = compact( $pieces );

            $where   = isset( $clauses['where'] ) ? $clauses['where'] : '';
            $groupby = isset( $clauses['groupby'] ) ? $clauses['groupby'] : '';
            $join    = isset( $clauses['join'] ) ? $clauses['join'] : '';
            $orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
            $limits  = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

            if ( ! empty($groupby) )
                $groupby = 'GROUP BY ' . $groupby;
            if ( !empty( $orderby ) )
                $orderby = 'ORDER BY ' . $orderby;

            $found_rows = '';
            if ( ! empty( $limits ) ) {
                $found_rows = 'SQL_CALC_FOUND_ROWS';
            }

            $fields = 'c.ID';

            if( 'count' != $args['fields'] && 'ids' != $args['fields'] ){
                if( is_array( $args['fields'] ) ){
                    $fields = implode( ',', $args['fields'] );
                }

                else {
                    $fields = $args['fields'];
                }
            }

            $res = $wpdb->get_col( "SELECT $found_rows DISTINCT $fields FROM $table c $join WHERE 1=1 $where $groupby $orderby $limits" );

            //echo "<pre>"; print_r($wpdb); die;

            // return count
            if ( 'count' == $args['fields'] ) {
                return ! empty( $limits ) ? $wpdb->get_var( 'SELECT FOUND_ROWS()' ) : count( $res );
            }

            return $res;
        }

}


if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


if ( ! class_exists( 'Listeo_Payouts_List_Table' ) ) {
    /**
     *
     *
     * @class class.yith-commissions-list-table
     * @package    Yithemes
     * @since      Version 1.0.0
     * @author     Your Inspiration Themes
     *
     */
    class Listeo_Payouts_List_Table extends WP_List_Table {
    /** Class constructor */
        public function __construct() {

            parent::__construct( [
                'singular' => __( 'Payout', 'listeo-core' ), // singular name of the listed records
                'plural'   => __( 'Payouts', 'listeo-core' ), // plural name of the listed records
                'ajax'     => false // does this table support ajax?
            ] );

        }


        /**
         * Returns columns available in table
         *
         * @return array Array of columns of the table
         * @since 1.0.0
         */
        public function get_columns() {
            $columns = array(
                    'id'             => __( 'ID', 'listeo_core' ),
                    'user_id'        => __( 'User', 'listeo_core' ),
                    //'status'         => __( 'Status', 'listeo_core' ),
                    'orders'         => __( 'Orders number', 'listeo_core' ),
                    'payment_method' => __( 'Payment method', 'listeo_core' ),
                    'amount'         => __( 'Amount', 'listeo_core' ),
                    'date'           => __( 'Date', 'listeo_core' ),
                    'actions'   => __( 'Actions', 'listeo_core' ),
                
            );

            

            return $columns;
        }

         public function prepare_items() {            

                $columns = $this->get_columns();
                $hidden = $this->get_hidden_columns();
                $sortable = $this->get_sortable_columns();
                
                $data = $this->table_data();

                //echo "<pre>"; print_r($data); die;

                //usort( $data, array( &$this, 'sort_data' ) );
                
                $perPage = 8;
                
                $currentPage = $this->get_pagenum();
                $totalItems = count($data);
                
                $this->set_pagination_args( array(
                    'total_items' => $totalItems,
                    'per_page'    => $perPage
                ) );
                
                $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
                
                $this->_column_headers = array($columns, $hidden, $sortable);
                $this->items = $data;


        }
        /**
         * Get the table data
         *
         * @return Array
         */
        private function table_data() {

            $data = array();

            $args = array(
                'status'           => 'all',
                
            );
            $payouts_class = new Listeo_Core_Payouts;
            $payouts = $payouts_class->get_payouts($args);
            foreach ($payouts as $key => $id) {
                $data[] = $payouts_class->get_payout($id);
                
            }
             
            return $data;
        }
         /**
         * Define what data to show on each column of the table
         *
         * @param  Array $item        Data
         * @param  String $column_name - Current column name
         *
         * @return Mixed
         */
        public function column_default( $item, $column_name ){
            switch( $column_name ) {
                case 'id':
                
                    return $item[ $column_name ];
                break;

                case 'user_id':
                    return '<a href="'.esc_url( get_author_posts_url($item['user_id'])).'">'.get_the_author_meta('display_name',$item['user_id']).'</a>';
                break;

                case 'status':
                    echo $item['status'];
                break;

                case 'orders':
                    echo count(json_decode($item['orders']));
                break;
                
                case 'amount':
                    if(function_exists('wc_price')) {
                        echo wc_price($item[ $column_name ]);
                    } else { echo $item[ $column_name ]; };
                break;
                
                case 'date':
                    echo date(get_option( 'date_format' ), strtotime($item['date']));
                break;

                case 'payment_method':
                    
                    echo ($item['payment_method'] == 'paypal') ? 'PayPal' : 'Bank Transfer' ;
                break;

               
                
                case 'actions':
                $url = admin_url( 'admin.php?page=listeo_payouts_list');
                
                $payout_url = esc_url( add_query_arg( 'view_payout', $item['id'], $url ) );
               
                printf( '<a class="button-primary view" href="%1$s" data-tip="%2$s">%2$s</a>', $payout_url, __( 'View Details', 'listeo_core' ) );
                break;
                default:
                    return print_r( $item, true ) ;
            }
        }
        public function get_hidden_columns() {
            return array();
        }
        function no_items() {
            _e( 'No payouts set.','listeo_core' );
        }

    }
}