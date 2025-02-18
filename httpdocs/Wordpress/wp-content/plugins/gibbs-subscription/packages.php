<?php
$super_admin = $this->get_super_admin();

if(!$super_admin){
    wp_redirect(home_url());
}
$args_packages = [
    'post_type' => 'stripe-packages',
    'posts_per_page' => -1, 
    'orderby' => 'date', 
    'order' => 'ASC' 
];
$query = new WP_Query($args_packages);


$packages = $query->posts;


$user_id = $super_admin;

$stripe_customer_id = get_user_meta($user_id, 'stripe_customer_id', true);

$active_subscription_price_id = null;
$active_sub = [];
if ($stripe_customer_id) {
    try {
        $subscriptions = $this->stripe->subscriptions->all(['customer' => $stripe_customer_id]);
        if (count($subscriptions->data) > 0) {
            $active_subscription = $subscriptions->data[0]; // Get the first active subscription
            $active_subscription_price_id = $active_subscription->items->data[0]->price->id; // Get the price ID of the active subscription
            $price_id = $active_subscription->items->data[0]->price->id; // Assuming single price
            $price = $this->stripe->prices->retrieve($price_id);

            $active_subscription->pricer = $price;

            $active_sub =  $active_subscription;

        }
    } catch (Exception $e) {
        error_log('Error fetching subscriptions: ' . $e->getMessage());
    }
}
$disable_stripe_dashboard = false;
if(isset($active_sub->pricer->product) && $active_sub->pricer->product == $this->stripe_custom_plan_product_id){
    $disable_stripe_dashboard = true;
}
//echo "<pre>"; print_r($active_sub); die;
?>
<div class="package-view">
    <?php foreach ($packages as $package): 

        $package = (array) $package;
        $stripe_product_id = get_post_meta($package["ID"], 'stripe_product_id', true);
        $start_price_id = get_post_meta($package["ID"],"start_price_id",true);
        $lock_price = get_post_meta($package["ID"],"lock_price",true);
        $shally_price = get_post_meta($package["ID"],"shally_price",true);

        $first_listing_price = $start_price_id + $lock_price + $shally_price;
        
        $locks = $this->getLocks($super_admin);

        $disable_btn = false;

        if($locks > 0 && $lock_price == ""){
            $disable_btn = true;
        }
        
        $shelly = $this->getShally($super_admin);

        if($shelly > 0 && $shally_price == ""){
            $disable_btn = true;
        }
        


        $data_sub = array();
        if(isset($active_sub->pricer->product) && $active_sub->pricer->product == $stripe_product_id){
            $data_sub = $active_sub;
        }
        $Class_Gibbs_Subscription = new Class_Gibbs_Subscription;
		$get_listing_count  = $Class_Gibbs_Subscription->get_listing_count($super_admin);
        ?>
        <div class="package">
            <div class="top-area">
                <div class="top-right">
                    <h3><?php echo esc_html($package['post_title']); ?> <?php if(isset($data_sub->status)){?> <span class="badge badge-status bd-<?php echo $data_sub->status;?>"><?php esc_html_e($data_sub->status, 'listeo_core'); ?></span><?php } ?></h3>
                    <div class="in-div">
                        <?php if($start_price_id != 0){ ?>
                            <?php if(isset($data_sub->id)){?>
                            
                                    
                                <p>
                                <span>  <strong> </strong>  Antall publiserte annonser. <span class="listing-count"><?php echo $get_listing_count; ?> stk </span> 
                                </p>
                                <?php if ($lock_price != "") { ?>
                                <p>
                                <span>  <strong> </strong> Antall aktive smartlås. </strong>  </span> <span class="listing-count"><?php if ($locks !== "") { ?><?php echo isset($locks) ? $locks : 0; ?><?php } ?> stk </span>   </p>
                                </p>
                                <?php } ?>
                                <?php if ($shally_price != "") { ?>
                                <p>
                                <span>  <strong> </strong>  Antall aktive strømstyringsenheter. </span>  <span class="listing-count"><?php if ($shelly !== "") { ?> <?php echo isset($shelly) ? $shelly : 0; ?><?php } ?> stk </span>   </p>
                                </p>
                                <?php } ?>
                            
                                <p>Gyldig til: : <span class="listing-count"><?php echo esc_html(date('Y-m-d', $data_sub->current_period_end));?> </span>  
                                </p>
                                <p>Total pris per mnd:<span class="listing-count"><?php echo esc_html(number_format($price->unit_amount / 100, 2))?>kr </span>
                                <p>  
                                
                                </p>
                            <?php }else{ ?>
                            <p>From <?php echo esc_html($first_listing_price); ?>kr/mo</p>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="top-right">
                    <?php if($start_price_id != 0){ ?>
                        
                        <?php if(isset($data_sub->id)){?>
                            <span class="load-div">
                                <button class="cancel-subscription" data-subscription-id="<?php echo $data_sub->id; ?>" data-price-id="<?php echo esc_attr($start_price_id); ?>" data-package-id="<?php echo $package["ID"]; ?>">
                                    Avslutt
                                </button>
                                <span class="loading spinner" style="display: none;width:20px;height:20px;"></span>
                            </span>
                        <?php }else{ ?>

                            <span class="load-div">
                                <button class="checkout-button <?php if($disable_btn == true){ echo 'btn-primary.disabled';}?>" data-price-id="<?php echo esc_attr($start_price_id); ?>" data-package-id="<?php echo $package["ID"]; ?>" <?php if($disable_btn == true){ echo "disabled";}?> <?php if(isset($active_sub->pricer->product) && $active_sub->pricer->product == $this->stripe_custom_plan_product_id){  echo "disabled";}?>>
                                    Aktiver
                                </button>
                                <span class="loading spinner" style="display: none;width:20px;height:20px;"></span>
                            </span>
                        <?php } ?>
                    <?php } ?>
                    
                </div>
            </div>
            <div class="bottom-area">
                <p><?php echo wp_trim_words(strip_tags($package['post_content']), 10,"...."); ?></p>
                <p class="gibbs_popup-<?php echo $package['ID']; ?>"> 
                    <strong>
                        <span class="load-div">
                            <a href="javascript:void(0)">Les mer</a>
                            <span class="loading spinner" style="display: none;width:20px;height:20px;"></span>
                        </span>    
                    </strong>
                </p>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- <div class="package">
        <div class="top-area">
            <div class="top-left">
                <h3>Skreddersydd pakke</h3>
                <div class="contt">
                    
                    <p></p>
                </div>
            </div>
            <div class="top-right">
                    <button onclick="location.href='mailto:kontakt@gibbs.no'">
                        Kontakt
                    </button>
            </div>
        </div>
        <div class="bottom-area">
           <p>

            - Tilpasses deres spesifikke behov.
            - Kvanterabatt for store aktører.
            - Volumrabatt ved stort bruk volum.
            - Ofte brukt av kommuner og store organisasjoner
            - Funksjonalitet for nye tilpasninger – Fleksibilitet til å legge til spesialfunksjoner.
            Sesongbooking – Enkel håndtering av sesongbaserte bestillinger.
                        
           
           
           Contact us at kontakt@gibbs.no</p>
        </div>
    </div> -->
    <?php if($stripe_customer_id && !$disable_stripe_dashboard){ ?>
        <div class="stripe-dash-main">
            <div class="top-area">
                <div class="top-left">
                    <h3>Kort og betalingshistorikk</h3>
                </div>
                <form id="stripe-form" method="post" action="<?php echo get_admin_url(); ?>admin-ajax.php" target="_blank">
                        <input type="hidden" name="action" value="stripe_dashboard">
                        <button type="submit" style="cursor: pointer;" class="btn btn-primary">Åpne <i class="fa fa-external-link"></i>  </button>
                    </form>
            </div>
           
        </div>
    <?php } ?>
    
</div>


<script>
    jQuery(".load-div").find("a").click(function(){
        jQuery(this).parent().find(".loading").show();
        var _that = this;
        setTimeout(function(){
            jQuery(_that).parent().find(".loading").hide();
        },3000)
    })
</script>