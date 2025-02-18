<?php 

class ExtendedSearch extends Listeo_Core_Search {

    public function __construct() {

        add_shortcode( 'listeo_search_form', array($this, 'output_search_form'), 8);

        add_action( 'wp_ajax_nopriv_listeo_get_features_from_category', array( $this, 'ajax_get_features_from_category' ), 9 );
        add_action( 'wp_ajax_listeo_get_features_from_category', array( $this, 'ajax_get_features_from_category' ), 9 );

    }

    public function output_search_form( $atts = array() ){

        extract( $atts = shortcode_atts( apply_filters( 'listeo_core_output_defaults', array(
            'source'            => 'sidebar', // home/sidebar/split
            'wrap_with_form'    => 'yes',
            'custom_class'      => '',
            'action'            => '',
            'more_trigger'      => 'yes',
            'more_text_open'    => __('Additional Features','listeo_core'),
            'more_text_close'   => __('Additional Features','listeo_core'),
            'more_custom_class' => ' margin-bottom-10 margin-top-30',
            'more_trigger_style' => 'relative',
            'ajax_browsing'     => get_option('listeo_ajax_browsing'),
            'dynamic_filters'   => (get_option('listeo_dynamic_features')=="on") ? "on" : "off",

        ) ), $atts ));

        switch ($source) {

            case 'home':
                $search_fields = $this->get_search_fields_home();
                //fix for panel slider for search
                if(isset($search_fields['_price'])){
                    $search_fields['_price']['place'] = 'panel';
                }
                
                if(isset($search_fields['search_radius'])){
                    $search_fields['search_radius']['place'] = 'panel';
                }
                break;

            case 'sidebar':
                $search_fields = $this->get_search_fields();

                break;

            case 'half':
                $search_fields = $this->get_search_fields_half();
                
                break;

            case 'homebox':
                $search_fields = $this->get_search_fields_home_box();

                break;

            default:
                $search_fields = $this->get_search_fields_home();
                break;

        }


        if(isset($search_fields['tax-listing_feature'])){
            $search_fields['tax-listing_feature']['dynamic'] = (get_option('listeo_dynamic_features')=="on") ? "yes" : "no";
        }
        if(isset($search_fields['features'])){
            $search_fields['features']['dynamic'] = (get_option('listeo_dynamic_features')=="on") ? "yes" : "no";
        }
        $ajax = ($ajax_browsing == 'on') ? 'ajax-search' : get_option('listeo_ajax_browsing') ;
        if($ajax_browsing == 'on'){
            if(isset($search_fields['submit'])){
                unset($search_fields['submit']);
            }
        }

        if(!get_option('listeo_maps_api_server')){
                unset($search_fields['radius']);
                unset($search_fields['search_radius']);
        }
        if($source == 'home'){
            foreach ($search_fields as $key => $value) {
                if( in_array( $value['type'], array('multi-checkbox','multi-checkbox-row') ) ) {
                    $search_fields[$key]['place'] = 'panel';
                }
            }
        }
        $template_loader = new Listeo_Core_Template_Loader;

        if(is_author()) {
            $author = get_queried_object();
            $author_id = $author->ID;
            $action = get_author_posts_url($author_id);
        }

        ob_start();
        if($wrap_with_form == 'yes') { ?>
        <form action="<?php echo $action; ?>" id="listeo_core-search-form" class="<?php if($dynamic_filters == 'on') { echo esc_attr('dynamic'); }  ?> <?php echo esc_attr($custom_class) ?> <?php echo esc_attr($ajax) ?>" method="GET">
            <?php 
            if(isset($_GET['user']) && is_array($_GET['user'])){
                foreach ($_GET['user'] as  $user_get) {
                     echo '<input type="text" name="user[]" value="'.$user_get.'" style="display: none">';
                }
               
            }
            if(isset($_GET['header'])){
                     echo '<input type="text" name="header" value="'.$_GET['header'].'" style="display: none">';
               
            }
            if(isset($_GET['filter'])){
                     echo '<input type="text" name="filter" value="'.$_GET['filter'].'" style="display: none">';
            }
            ?>
            
        <?php }
        if( in_array($source, array('home')) ) { ?>
            <div class="main-search-input">
        <?php }

            $more_trigger = false;
            $panel_trigger = false;
            foreach ($search_fields as $key => $value) {
                if( (isset($value['place']) && $value['place'] == 'adv'))  {
                    $more_trigger = 'yes';
                }
                if( (isset($value['place']) && $value['place'] == 'panel'))  {
                    $panel_trigger = 'yes';
                }
            }
            //count main fields
            $count = 0;
            foreach ($search_fields as $key => $value) {
                if(isset($value['place']) && $value['place'] == 'main') {
                    $count++;
                }
            }
            $temp_count = 0;
            foreach ($search_fields as $key => $value) {
                if( in_array($source, array('home','homebox')) && $value['type']!='hidden') { ?>
                    <div class="main-search-input-item <?php echo ($value['type']=='slider') ? 'slider_type' : esc_attr($value['type']); ?>">
                <?php }

                if(isset($value['place']) && $value['place'] == 'main') {

                    //displays search form

                    if($source == 'half') {
                        $temp_count++;
                        //$template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);
                        
                        
                    } else {
                        if($source == 'sidebar') { echo '<div class="row with-forms">'; }
                            $template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);
                        if($source == 'sidebar') { echo '</div>'; }
                    }
                    if($value['type'] == 'radius') { ?>
                            <div class="col-md-12">
                                <span class="panel-disable" data-disable="<?php echo esc_attr_e( 'Disable Radius', 'listeo_core' ); ?>" data-enable="<?php echo esc_attr_e( 'Enable Radius', 'listeo_core' ); ?>"><?php esc_html_e('Disable Radius', 'listeo_core'); ?></span>
                            </div>
                    <?php }
                }

                if( in_array($source, array('home','homebox'))  ) {
                    //fix for price on home search
                    if(isset($value['place']) && $value['place'] == 'panel') {
                        ?>
                        <?php if( isset($value['type']) && $value['type'] != 'submit' ) {

                            

                         ?>
                            <!-- Panel Dropdown -->
                            <div class="panel-dropdown <?php if( $value['type'] == 'multi-checkbox-row') { echo "wide"; } if($value['type'] == 'radius') { echo 'radius-dropdown'; } ?> " id="<?php echo esc_attr( $value['name']); ?>-panel">
                                <a href="#"><?php echo esc_html($value['placeholder']); ?><i class="fa fa-times" onclick="clearFiltersFor('#<?php echo esc_attr( $value['name']); ?>-panel')"></i></a>
                                <div class="panel-dropdown-content <?php if( $value['type'] == 'multi-checkbox-row') { echo "checkboxes"; } ?> <?php if(isset($value['dynamic']) && $value['dynamic']=='yes'){ echo esc_attr('dynamic'); }?>">
                                    <div style="display:flex;flex-direction:row;width:100%;margin-bottom:20px;height:30px;">
                                        <a href="#" style="all:unset;position:absolute;right:0;padding:20px;top:0px;cursor:pointer;border-radius:0px!important;"><i class="fa fa-times"></i></a>
                                        <p><?php echo $value['placeholder']; ?></p>
                                    </div>
                            <?php }

                            $template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);

                            if( isset($value['type']) && $value['type'] != 'submit' ) { ?>
                            <!-- Panel Dropdown -->
                                    <div class="panel-buttons">
                                        <?php if($value['type'] == 'radius') { ?>
                                            <span class="panel-disable" data-disable="<?php echo esc_attr_e( 'Disable', 'listeo_core' ); ?>" data-enable="<?php echo esc_attr_e( 'Enable', 'listeo_core' ); ?>"><?php esc_html_e('Disable','listeo_core'); ?></span>
                                        <?php } else { ?>
                                            <span class="panel-cancel"><?php esc_html_e('Close', 'listeo_core'); ?></span>
                                        <?php } ?>

                                        <button class="panel-apply"><?php esc_html_e('Apply', 'listeo_core'); ?></button>
                                    </div>
                                    <div class="brukFilterKnappWrapper active"><a class="brukFilterKnapp button" onclick="brukFilter(this)">Bruk</a></div>
                                </div>
                            </div>
                        <?php }

                    }
                }
                if( in_array($source, array('home','homebox'))  && $value['type']!='hidden') { ?>
                    </div>
                <?php }
            }
            ?>
            <?php if(isset($_GET['authorid'])): ?>
            <input type="hidden" value="<?php echo $_GET['authorid'] ?>" name="authorid">
            <?php endif ?>
            <?php if($more_trigger == 'yes') : ?>
                <!-- More Search Options -->
                <a href="#" class="more-search-options-trigger <?php echo esc_attr($more_custom_class) ?>" data-open-title="<?php echo esc_attr($more_text_open) ?>" data-close-title="<?php echo esc_attr($more_text_close) ?>"></a>
                <?php if($more_trigger_style == "over") : ?>
                <div class="more-search-options ">
                    <div class="more-search-options-container">
                <?php else: ?>
                    <div class="more-search-options relative">
                <?php endif; ?>

                        <?php foreach ($search_fields as $key => $value) {
                        if($value['place'] == 'adv') {

                            $template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);
                        }
                        } ?>
                    <?php if($more_trigger_style == "over") : ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if( $source!='home' && $panel_trigger == 'yes' ) { ?>
                <?php echo ($source=='half') ? '' : '<div class="col-md-12  panel-wrapper">' ; {  ?>
                    <?php

                   // echo "<pre>"; print_r($search_fields); die;
                   
                    foreach ($search_fields as $key => $value) {
                        if($source != 'home' && isset($value['place']) && $value['place'] == 'panel') {

                            if(isset($value['_icon_svg']) & $value['_icon_svg'] != ""){
                                $_icon_svg = $value['_icon_svg'];
                                $_icon_svg_image = wp_get_attachment_image_src($_icon_svg,'medium'); 

                                $icon_svg = listeo_render_svg_icon($_icon_svg);
                            }else{
                                $icon_svg = listeo_render_svg_icon($_icon_svg);
                            }

                        	/*if($value["type"] == "select-taxonomy" && $value["taxonomy"] != "region"){
                                 $template_loader->set_template_data( $value )->get_template_part( 'search-form/mygibb-taxonomy');
                        	}else{*/

                            $selected_cats = "";    

                            if(isset($value["selected_cats"]) && $value["selected_cats"] != "" && !empty($value["selected_cats"]))   {
                                $selected_cats = implode(",", $value["selected_cats"]);
                            } 
                        	
                        ?>

                            <?php if( isset($value['type']) && !in_array($value['type'], array('submit','sortby')) ) { ?>
                            <!-- Panel Dropdown -->
                            <div class="panel-dropdown <?php if( $value['type'] == 'multi-checkbox-row') { echo "wide"; } if($value['type'] == 'radius') { echo 'radius-dropdown'; } ?> " id="<?php echo esc_attr( $value['name']); ?>-panel" <?php if($selected_cats != ""){ echo "style='display:none;' has_cat='true'";}?> catss='<?php echo $selected_cats;?>'>
                                <a href="#" class="empty_white">
                                    <span class="filter_text"><?php echo $icon_svg;?>
                                        <span class="text_inner"><?php
                                            if($value['name'] === '_standing1'){
                                                ?><i style="font-style: normal;color: #008474;"class="fas fa-users"></i> <?php echo 'Kapasitet:';
                                            }else{
                                                echo esc_html($value['placeholder']); 
                                            }
                                            ?>
                                        </span>
                                    </span>
                                <?php
                                
                             
                                if($value['name'] !== '_standing1'){
                                    ?><i class="fa fa-times" onclick="clearFilter('#<?php echo esc_attr( $value['name']); ?>-panel')" ></i>
                                    <span class="greenThenWhite" style="padding-left:3px;"></span>
                                    </a><?php
                                }else{
                                    ?><!-- <span class="greenThenWhite" id="capacitySpan" style="color:#008474"></span> -->
                                    <i style='visibility:hidden;padding: 6px;font-weight: bold;float: right;' class="fa fa-times capacityDisable" ></i>
                                    <span class="greenThenWhite" style="padding-left:3px;"></span>
                                    </a><?php
                                }
                               ?>
                               
                               
                                <div class="panel-dropdown-content <?php if( $value['type'] == 'multi-checkbox-row') { echo "checkboxes"; } ?> <?php if(isset($value['dynamic']) && $value['dynamic']=='yes'){ echo esc_attr('dynamic'); }?>">
                                    <div style="display:flex;flex-direction:row;width:100%;margin-bottom:20px;height:30px;">
                                        <a href="#" style="all:unset;position:absolute;right:0;padding:20px;top:0px;cursor:pointer;border-radius:0px!important;"><i class="fa fa-times"></i></a>
                                        <p><?php 
                                        if($value['name'] === '_standing'){
                                            echo $value['placeholder']; 
                                        }else{
                                            echo $value['placeholder']; 
                                        }
                                        ?></p>
                                    </div>
                            <?php }
                            if($value['name'] === '_standing'){
                                //$value['type'] = 'mygibb-captest';
                            }

                            if($key == "_listing_type"){
                        		$template_loader->set_template_data( $value )->get_template_part( 'search-form/mygibb-listing-type');
                        	}elseif($value["taxonomy"] == "region" || $value["type"] == "select-taxonomy"){
                                $template_loader->set_template_data( $value )->get_template_part( 'search-form/mygibb-taxnomy-final');
                        	}else{
                                $template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);
                        	}


                            if( isset($value['type']) && !in_array($value['type'], array('submit','sortby')) ) { ?>
                            <!-- Panel Dropdown -->
                                    <div class="panel-buttons">
                                        <?php if($value['type'] == 'radius') { ?>
                                            <span class="panel-disable" data-disable="<?php echo esc_attr_e( 'Disable', 'listeo_core' ); ?>" data-enable="<?php echo esc_attr_e( 'Enable', 'listeo_core' ); ?>"><?php esc_html_e('Disable', 'listeo_core'); ?></span>
                                        <?php } else { ?>
                                            <span class="panel-cancel"><?php esc_html_e('Close', 'listeo_core'); ?></span>
                                        <?php } ?>

                                        <button class="panel-apply"><?php esc_html_e('Apply', 'listeo_core'); ?></button>
                                    </div>
                                    <div class="brukFilterKnappWrapper active"><a class="brukFilterKnapp button" onclick="brukFilter(this)">Bruk</a></div>
                                </div>
                            </div>
                        <?php }
                           // }
                        }
                    } ?>

                <?php echo ($source=='half') ? '' : '</div>' ?>
            <?php }
            } ?>
            <input type="hidden" name="action" value="listeo_get_listings" />
            <!-- More Search Options / End -->
            <?php if($source == 'sidebar' && $ajax_browsing != 'on') {  ?>
                <button class="button fullwidth margin-top-30"><?php esc_html_e('Search','listeo_core') ?></button>
            <?php } ?>

            <?php if(in_array($source, array('home','homebox')) ) { ?>
                <button class="button"><?php esc_html_e('Search', 'listeo_core') ?></button>
            </div>
            <?php } ?>
        <?php if($wrap_with_form == 'yes') { ?>
        </form>
        <script type="text/javascript">

            jQuery(".panel-dropdown").find("a").click(function(){
                var that;
                that = this;
                if(jQuery(that).parent().hasClass("active")){
                    
                    setTimeout(function(){
                        
                            jQuery(that).parent().removeClass("active");
                       
                    },200);
                }
                
            })
            //jQuery("#listeo_core-search-form").find("input").change();
            
                
           

            function getUrlVars_listings()
            {
                var vars = [], hash;
                var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                for(var i = 0; i < hashes.length; i++)
                {
                    hash = hashes[i].split('=');
                    vars.push(hash[0]);
                    vars[hash[0]] = hash[1];
                }
                return vars;
            }

            jQuery(document).ready(function(){
                jQuery("#listeo_core-search-form").find("input").change();
                    var vars;
                    vars = getUrlVars_listings();

                    jQuery(document).on( 'change',"#listeo_core-search-form input",function(){ 





                        var valuess = [];
                        var ctasss = [];

                        jQuery("#listeo_core-search-form").find("input:checked").each(function(){
                            valuess.push(jQuery(this).attr("name")+"="+jQuery(this).val());

                            ctasss.push(jQuery(this).val());

                            var valll;
                            valll = jQuery(this).val();

                        });




                        jQuery("#listeo_core-search-form").find(".panel-dropdown").each(function(){

                            var hass_catt = "";

                            if(jQuery(this).attr("catss") != undefined && jQuery(this).attr("catss") != ""){
                                var ss = jQuery(this).attr("catss").split(",");
                                jQuery.each( ss, function( index, value ){
                                   if(jQuery.inArray(value, ctasss) !== -1){
                                        hass_catt = "true";
                                   }
                                });

                                if(hass_catt != ""){
                                    jQuery(this).show();
                                }else{
                                    jQuery(this).hide();
                                }
                            }

                            

                            /*jQuery.each( valuess, function( index, value ){
                                sum += value;
                            });
         
                            if(jQuery(this).attr("has_cat") != undefined){
                               if(jQuery(this).attr(valll) != undefined){
                                  jQuery(this).show();
                               }else{
                                  jQuery(this).hide();
                               }
                            }*/
                        });

                        jQuery("#listeo_core-search-form").find("input[type=text]").each(function(){
                            if(jQuery(this).val() != ""){
                                valuess.push(jQuery(this).attr("name")+"="+jQuery(this).val());
                            }
                            
                        });

                        var vv = valuess.join('&');
                        if(vv != ""){

                            if(vars["page"] != undefined && vars["page"] != ""){
                                var pagg = "&page="+vars["page"];
                                setTimeout(function(){
                                     vars["page"] = "";
                                },4000);

                            }else{
                                var pagg = "";
                            }
                            var oldURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
                             var newUrl = oldURL + "?"+vv+"&action=listeo_get_listings"+pagg;
                             if (window.history != 'undefined' && window.history.pushState != 'undefined') {
                                 window.history.pushState({ path: newUrl }, '', newUrl);
                            }
                            
                        }else{

                            if(vars["page"] != undefined && vars["page"] != ""){
                                var pagg = "?page="+vars["page"];
                                setTimeout(function(){
                                     vars["page"] = "";
                                },4000);

                            }else{
                                var pagg = "";
                            }

                            var oldURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            var newUrl = oldURL+pagg;
                             if (window.history != 'undefined' && window.history.pushState != 'undefined') {
                                 window.history.pushState({ path: newUrl }, '', newUrl);
                            }

                        }
                        if(jQuery("#listeo_core-search-form").find("#keyword_search-panel").find("input").val() != ""){
                            count_search = 1;
                        }else{
                            count_search = 0;
                        }
                        var lenn = jQuery("#listeo_core-search-form").find("input:checked").length + count_search;
                        if(lenn > 0){
                             jQuery("#totalFilters").html(lenn);
                        }else{
                             jQuery("#totalFilters").html("");
                        }
                        
                        
                    })
            });

            function clearFilter(filter){

               if(filter == "#keyword_search-panel"){
                    jQuery(filter).find("input").val("");
               }
               
               jQuery(filter).find("input[type=text]").val("");

               jQuery(filter).find("input").prop("checked",false);
               
               
               jQuery(filter).find("input:first").change();
               
               jQuery(filter).find(".fa-times").hide();
               jQuery(filter).find(".greenThenWhite").html("");

               setTimeout(function(){
                  jQuery("#listeo_core-search-form").find("input:first").change();
                 // alert("dfkdfj")
               },500);

               if(filter == "#_price-panel"){
                    //debugger;
               }
               
              
            }
        </script>

        <?php }
        //if ajax

        $output = ob_get_clean();
        echo $output;

    }

    public function ajax_get_features_from_category(){

        $categories  = (isset($_REQUEST['cat_ids'])) ? $_REQUEST['cat_ids'] : '' ;

        $panel  =  (isset($_REQUEST['panel'])) ? $_REQUEST['panel'] : '' ;
        $success = true;
        ob_start();
        
        if($categories){
            $features = array();
            
            foreach ($categories as $category) {
                if(is_numeric($category)) {
                    $cat_object = get_term_by('id', $category, 'listing_category');
                } else {
                    $cat_object = get_term_by('slug', $category, 'listing_category');
                }
                if($cat_object){
                    $features_temp = get_term_meta($cat_object->term_id,'listeo_taxonomy_multicheck',true);
                    if($features_temp) {
                        $features = array_merge($features,$features_temp);
                    }
                    $features = array_unique($features);

                }
            }


            if($features){
                if($panel != 'false'){ ?>
                    <div class="panel-checkboxes-container">
                    <?php
                        $groups = array_chunk($features, 4, true);

                        foreach ($groups as $group) { ?>

                            <?php foreach ($group as $feature) {
                                $feature_obj = get_term_by('slug', $feature, 'listing_feature');
                                if( !$feature_obj ){
                                    continue;
                                }
                                ?>
                                <div class="panel-checkbox-wrap">
                                    <input form="listeo_core-search-form" id="<?php echo esc_html($feature) ?>" value="<?php echo esc_html($feature) ?>" type="checkbox" name="tax-listing_feature[<?php echo esc_html($feature); ?>]">
                                    <label for="<?php echo esc_html($feature) ?>"><?php echo $feature_obj->name; ?></label>
                                </div>
                            <?php } ?>


                        <?php } ?>

                    </div>
                <?php } else {

                    foreach ($features as $feature) {
                        $feature_obj = get_term_by('slug', $feature, 'listing_feature');
                        if( !$feature_obj ){
                            continue;
                        }?>
                        <input form="listeo_core-search-form" id="<?php echo esc_html($feature) ?>" value="<?php echo esc_html($feature) ?>" type="checkbox" name="tax-listing_feature[<?php echo esc_html($feature); ?>]">
                        <label for="<?php echo esc_html($feature) ?>"><?php echo $feature_obj->name; ?></label>
                    <?php }
                }
            } else {
                if( $cat_object && isset($cat_object->name)) {
                    $success = false; ?>
                <div class="notification notice <?php if($panel){ echo "col-md-12"; } ?>">
                    <p>
                    <?php printf( __( 'Category "%s" doesn\'t have any additional filters', 'listeo_core' ), $cat_object->name )  ?>

                    </p>
                </div>
                <?php } else {
                    $success = false; ?>
                <div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
            <?php }
                }
            } else {
            $success = false; ?>
            <div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
        <?php }

        $result['output'] = ob_get_clean();
        $result['success'] = $success;
        wp_send_json($result);
    }

}
new ExtendedSearch();

?>