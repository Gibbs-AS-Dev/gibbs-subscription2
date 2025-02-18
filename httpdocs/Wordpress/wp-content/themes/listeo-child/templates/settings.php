<?php
global $wpdb;
$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

if($active_group_id != ""){
    $user_management_group_id = $active_group_id;
}else{
    $user_management_group_id = "0";
}
?>

<div class="settingsg">
    <form method="post" action="">
        <input type="hidden" name="action" value="save_settings">
        <div class="section">
            <div class="header">
            <button type="button" class="change-department" id="usergroup_addnew_st"><?php echo __("Opprett ny avdeling","gibbs");?></button>
            <button style="display: none;" id="usergroup_modalbtn"></button>
            </div>
        </div>
        <?php if($user_management_group_id != "0"){ 

            $users_groups_table = $wpdb->prefix . 'users_groups';  // table name
            $sql_user_group_modal = "select * from `$users_groups_table` where id = $user_management_group_id";
            $user_group_data_modal = $wpdb->get_row($sql_user_group_modal);

            $users_group_id = "";
            $users_group_name = "";
            $group_admin = "";
            $group_admin_email = "";
            if(isset($user_group_data_modal->id) && isset($user_group_data_modal->name)){

                $users_group_id = $user_group_data_modal->id;
                $users_group_name = $user_group_data_modal->name;
                $users_table = $wpdb->prefix . 'users'; 

                if($user_group_data_modal->group_admin != ""){

                    $group_admin = $user_group_data_modal->group_admin;

                    $user_data_sql = "select user_email from `$users_table` where id = $group_admin";

                    $user_data = $wpdb->get_row($user_data_sql);

                    if(isset($user_data->user_email)){
                        $group_admin_email = $user_data->user_email;
                    }

                }
           
            
            ?>
                <input type="hidden" class="users_group_id" name="users_group_id" value="<?php echo $users_group_id;?>">
                <div class="section ">
                    <div class="header2 ">
                        <h2>Avdeling informasjon</h2>
                        <button type="button" class="change-department" id="group_sidebar">Bytt avdeling <i class="fa fa-chevron-down"></i></button>
                    </div>
                    <div class="content">
                        <div class="form-group">
                            <label for="gr_name">Navn</label>
                            <input type="text" id="gr_name" name="gr_name" value="<?php echo $users_group_name;?>" />
                        </div>
                        <div class="form-group">
                            <label for="gr_email">E-post (alle varsler for denne brukergruppen blir sendt til denne eposten)</label>
                            <input type="email" id="gr_email" name="gr_email" value="<?php echo $group_admin_email;?>" />
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="section ">
            <div class="header2 ">
                    <h2>Valuta</h2>
                    </div>
                    <div class="form-group">
                        <label for="currency">
                        Velg hvilken valuta du ønsker å motta betalinger i.</label>
                        <select id="currency" name="currency">
                        <?php
                            $group_admin = get_group_admin();

                            if($group_admin != ""){
                                $currency_user_id = $group_admin;
                            }else{
                                $currency_user_id = get_current_user_id();
                            }
                            $user_currency = get_user_meta( $currency_user_id, 'currency', true );
                            if($user_currency == ""){
                                $user_currency = "NOK";
                            }

                            $currencies = get_woocommerce_currencies();
                            
                            foreach ($currencies as $currency_code => $currency_name) {
                                if(strtolower($currency_code) != "usd" && strtolower($currency_code) != "nok" && strtolower($currency_code) != "dkk"){
                                    continue;
                                }
                                // Get the currency symbol for each currency code
                                $currency_symbol = get_woocommerce_currency_symbol($currency_code);
                        ?>
                                
                                <option value="<?php echo $currency_code;?>" <?php if($user_currency == $currency_code){?>selected<?php }?>><?php echo $currency_name."(".$currency_symbol.")";?></option>
                                
                        <?php	
                            }
                        ?>
                        </select>
                    </div>
                
            </div>
            <button type="submit" class="btn btn-primary">Lagre</button>
        <?php } ?>
    </form>
    <?php 
       
        require(WP_PLUGIN_DIR."/gibbs-user-management/user_group_modal.php");
    ?>
</div>
<script>
    jQuery("#usergroup_addnew_st").click(function(){
  
        jQuery("#usergroupModal").show();
        jQuery(".chaange_group_title").html("<?php  echo __("Opprett avdeling","Gibbs");;?>")
        //jQuery(".group_admin_email_div").hide();
        setTimeout(function(){
            jQuery(".users_group_id").val("");
            jQuery(".users_group_name").val("");
            jQuery(".group_admin_email").val("");
        },100);

    });
    jQuery(document).on("click","#group_sidebar",function(){
        setTimeout(function(){
            jQuery("#menudrpcontent").addClass("show");
            jQuery(".gr_divv").addClass("focus_div")
        },200)
    })

</script>
<style>
    .settingsg .user_group_modal:before {
        height: 20%;
    }
</style>