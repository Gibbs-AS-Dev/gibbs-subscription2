<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Listeo_Special_Editor {
/**
     * Stores static instance of class.
     *
     * @access protected
     * @var Listeo_Submit The single instance of the class
     */
    protected static $_instance = null;

      /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin

    protected $fields = array();
    /**
     * Returns static instance of class.
     *
     * @return self
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct($version = '1.0.0') {
  
       	add_action( 'admin_menu', array( $this, 'add_options_page' ) ); //create tab pages

       

       	add_action('wp_ajax_save_feature_cats', array($this, 'save_feature_cats'));
        add_action('wp_ajax_nopriv_save_feature_cats', array($this, 'save_feature_cats'));


       	$this->file = __FILE__;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

        $this->assets_url = str_replace("includes/", "", $this->assets_url);
      	

    }

   

 
   

     /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
         add_submenu_page( 'listeo-fields-and-form', 'Special field editor', 'Special field editor', 'manage_options', 'listeo-special-editor', array( $this, 'output' )); 
    }
    public function save_feature_cats(){

    	if(!empty($_POST["cat_feature"])){
    		$features = array();
    		foreach ($_POST["cat_feature"] as $key1 => $value1) {
    			$features[] = $value1;
    		}
    		$features = json_encode($features);
    		update_post_meta($_POST["feature_id"],"cat_feature",$features,false);

    	}
    }


    public function output(){

    		wp_enqueue_script('listeo-jquery-script', esc_url( $this->assets_url ) . 'js/jquery.min.js', array(), $this->_version);
	    	wp_enqueue_script('listeo-bootstrap-script', esc_url( $this->assets_url ) . 'js/bootstrap.min.js', array(), $this->_version);
		    wp_enqueue_style( 'listeo-bootstrap-styles', esc_url( $this->assets_url ) . 'css/bootstrap.min.css', array(), $this->_version );
	    	wp_enqueue_style( 'listeo-custom-styles', esc_url( $this->assets_url ) . 'css/custom.css', array(), $this->_version );

		    if(!empty($_POST) && $_POST["form_type"] == "insert_feature"){

		    	$new = array(
				    'post_title' => $_POST["feature_name"],
				    'post_content' => $_POST["feature_name"],
				    'post_type' => "special_featured",
				    'post_status' => 'publish'
				);

				$post_id = wp_insert_post( $new );

				if( $post_id ){
					if($_POST["activate_full_row"] == "1"){
						$_POST["activate_full_row"] = "1";
					}else{
						$_POST["activate_full_row"] = "0";
					}
					add_post_meta($post_id,"feature_type_for",$_POST["feature_type_for"],false);
					add_post_meta($post_id,"_icon_svg",$_POST["_icon_svg"],false);
					add_post_meta($post_id,"order_number",$_POST["order_number"],false);
					add_post_meta($post_id,"activate_full_row",$_POST["activate_full_row"],false);
				    echo '<div class="alert alert-primary" role="alert">Feature successfully saved!</div>';
				} else {
				    echo '<div class="alert alert-primary" role="alert">Something went wrong, try again.</div>';
				}


		    }

		    if(!empty($_POST) && $_POST["form_type"] == "update_feature"){

		    	
		    	
				$post_feature = get_post( $_POST["feature_id"] );

				$post_feature->post_title = $_POST["feature_name"];
				$post_feature->post_content = $_POST["feature_name"];

				$post_id =  wp_update_post( $post_feature );

				if( $post_id ){

					if($_POST["activate_full_row"] == "1"){
						$_POST["activate_full_row"] = "1";
					}else{
						$_POST["activate_full_row"] = "0";
					}
					update_post_meta($post_id,"feature_type_for",$_POST["feature_type_for"],false);
					update_post_meta($post_id,"_icon_svg",$_POST["_icon_svg"],false);
					update_post_meta($post_id,"order_number",$_POST["order_number"],false);
					update_post_meta($post_id,"activate_full_row",$_POST["activate_full_row"],false);
				    echo '<div class="alert alert-primary" role="alert">Feature successfully updated!</div>';
				} else {
				    echo '<div class="alert alert-primary" role="alert">Something went wrong, try again.</div>';
				}


		    }
		    //wp_delete_post(7032, true);
		    /*global $wpdb;
		    $id = 7033;
		    $table = 'wp_posts';
		    $wpdb->delete( $table, array( 'id' => $id ) );*/

		    //die;

		    	

		    ?>
		    <div class="container" style="padding: 17px 0px;">
			    <div class="row">
			    	<div class="col-md-12">
			    		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#feature_modal">Add Feature</button>
			    	</div>
			    </div>
		    </div>


		    <div class="modal fade" id="feature_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow: auto;">
			  <div class="modal-dialog" role="document" style="max-width: 600px;overflow: auto;">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title">Add Feature</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <form method="post">
			       <div class="modal-body">
					    
						  <div class="card-body feature_body">
						  	<div class="form-group">
							    <label for="exampleInputEmail1">Feature name</label>
							    <input type="text" name="feature_name" required>
							</div>
							<div class="form-group">
							    <label for="exampleInputEmail1">Tags:</label>
							    <span>{price_from} {price_to} {address} {event_start_date} {event_start_time} {event_end_date} {event_end_time} {capacity} {event_ticket} {_listing_only_for_group} {_listing_only_for_group} </span>
							</div>
							<div class="form-group">
							    <label for="exampleInputEmail1">Feature Type for</label>
							    <select name="feature_type_for" required>
							    	<option value="">Select type</option>
							    	<option value="price">Price</option>
							    	<option value="capacity">Capacity</option>
							    	<option value="address">Address</option>
							    	<option value="Event_date">Event date</option>
							    	<option value="Event_time">Event time</option>
							    	<option value="Event_tickets">Event tickets</option>
							    	<option value="instant_booking">Instant Booking</option>
							    	<option value="internal_booking_only">Internal Booking Only</option>
							    </select>
							</div>
							<div class="form-group">
					            <label for="_cover"><?php esc_html_e( 'Custom Icon (SVG files only)', 'listeo_core' ); ?></label>
					            <input style="width:100px" type="text" name="_icon_svg" id="_icon_svg">
					            <input type='button' class="feature_upload_icon button-primary" value="<?php _e( 'Upload SVG Icon', 'listeo_core' ); ?>" id="feature_upload_icon"/><br />
					        </div>
							
							<div class="form-group">
							    <label for="exampleInputEmail1">Order number</label>
							    <input type="number" name="order_number">
							</div>
							<div class="form-group">
							    <label for="exampleInputEmail1">Activate Full row</label>
							    <input type="checkbox" name="activate_full_row" value="1">
							</div>
						  	
						  </div>
					</div>	  
					    
			      <div class="modal-footer">
			      	<input type="hidden" name="form_type" value="insert_feature">
			        <button type="submit" class="btn btn-primary">Save changes</button>
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			      </div>
			      </form>
			    </div>
			  </div>
			</div>
		    
			<?php

			global $wpdb;
			$args = array(
			    'post_type'=> 'special_featured',
			    'meta_key' => 'order_number',
		        'orderby'   => 'meta_value',
		        'order' => 'ASC',
			);              

			$the_query = new WP_Query( $args );

			$results = $the_query->posts;

			$catsss = array("service_category","event_category","rental_category");
			$all_cats = array();

		    foreach ($catsss as $key => $cats) {
		    	$listing_category_terms_all = get_terms( array(
					          'taxonomy' => $cats,
					          'hide_empty'  => false,
					          'orderby' => 'term_order',
			        ));

		    	foreach ($listing_category_terms_all as $key => $parent) {
		        	if($parent->parent == "0"){
		        		$all_cats[] = $parent;
		        	}
		        	
		        }
		    }
		    
			?>

			<div class="row feature_row">
				<?php
				foreach ($results as $key => $result) {
					    $icon_svg_id = get_post_meta($result->ID,"_icon_svg",true); 

		                if($icon_svg_id != ""){
		                    $_icon_svg_image = wp_get_attachment_url($icon_svg_id,'medium'); 

		                    $icon_svg = "<img style='width:40px' src='".$_icon_svg_image."' />";
		                }else{
		                	$icon_svg = "";
		                }
		                $cat_feature = get_post_meta($result->ID,"cat_feature",true); 
                        $featuress = array();
		                if($cat_feature != ""){
		                	$featuress = json_decode($cat_feature);
		                }

				?>	

					<div class="col-md-6">

						<div class="card" style="padding: 0;">
						    <div class="card-header">
						    	<div class="row">
						    		<div class="col-md-8"><?php echo $result->post_title;?></div>
						    		<div class="col-md-2"><?php echo $icon_svg;?></div>
						    		<div class="col-md-2"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#feature_modal_update_<?php echo $result->ID;?>">Edit</button></div>
						    	</div>
						     	
						    </div>
							 <div class="card-body feature_cats">
							 	<label>Select what categories will show feature in cards</label>
							 	<div class="catss">

								 	<?php 
								 	foreach ($all_cats as $key => $cat) {
								 	?>	
								 		<span class="checkboxess">
				    						<input type="checkbox" name="texnomy"  class="feature_cat" feature_id="<?php echo $result->ID;?>" value="<?php echo $cat->term_id;?>" <?php if(in_array($cat->term_id, $featuress)){ echo "checked";}?>> <?php echo $cat->name;?>
				    					</span>
								 	<?php
								     }
								 	?>
							 	</div>

							  	
							  </div>
						</div>
						<!-- modal -->
						<div class="modal fade" id="feature_modal_update_<?php echo $result->ID;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  style="overflow: auto;">
						  <div class="modal-dialog" role="document" style="max-width: 600px;">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title">Update Feature</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <form method="post">
						       <div class="modal-body">

						       	    <?php
							       	$feature_type_for = get_post_meta($result->ID,"feature_type_for",true);
							       	$order_number = get_post_meta($result->ID,"order_number",true);
							       	$activate_full_row = get_post_meta($result->ID,"activate_full_row",true);
							       	?>
								    
									  <div class="card-body feature_body">
									  	<div class="form-group">
									  		<input type="hidden" name="feature_id" value="<?php echo $result->ID;?>">
										    <label for="exampleInputEmail1">Feature name</label>
										    <input type="text" name="feature_name" value="<?php echo $result->post_title;?>" required>
										</div>
										<div class="form-group">
										    <label for="exampleInputEmail1">Tags:</label>
										    <span>{price_from} {price_to} {address} {event_start_date} {event_start_time} {event_end_date} {event_end_time} {capacity} {event_ticket} {_listing_only_for_group}</span>
										</div>
										<div class="form-group">
										    <label for="exampleInputEmail1">Feature Type for</label>
										    <select name="feature_type_for" required>
										    	<option value="">Select type</option>
										    	<option value="price"  <?php if($feature_type_for == "price"){echo "selected";}?>>Price</option>
										    	<option value="capacity" <?php if($feature_type_for == "capacity"){echo "selected";}?>>Capacity</option>
										    	<option value="address" <?php if($feature_type_for == "address"){echo "selected";}?>>Address</option>
										    	<option value="Event_date" <?php if($feature_type_for == "Event_date"){echo "selected";}?>>Event date</option>
										    	<option value="Event_time" <?php if($feature_type_for == "Event_time"){echo "selected";}?>>Event time</option>
							    	            <option value="Event_tickets" <?php if($feature_type_for == "Event_tickets"){echo "selected";}?>>Event tickets</option>
										    	<option value="instant_booking" <?php if($feature_type_for == "instant_booking"){echo "selected";}?>>Instant Booking</option>
										    	<option value="internal_booking_only" <?php if($feature_type_for == "internal_booking_only"){echo "selected";}?>>Internal Booking Only</option>
										    </select>
										</div>
										<div class="form-group">
								            <label for="_cover"><?php esc_html_e( 'Custom Icon (SVG files only)', 'listeo_core' ); ?></label>
								            <input style="width:100px" type="text" name="_icon_svg" id="_icon_svg" value="<?php echo $icon_svg_id;?>">
								            <input type='button' class="feature_upload_icon button-primary" value="<?php _e( 'Upload SVG Icon', 'listeo_core' ); ?>" id="feature_upload_icon"/><br />
								            <?php echo $icon_svg;?>
								        </div>
										
										<div class="form-group">
										    <label for="exampleInputEmail1">Order number</label>
										    <input type="number" name="order_number" value="<?php echo $order_number;?>">
										</div>
										<div class="form-group">
										    <label for="exampleInputEmail1">Activate Full row</label>
										    <input type="checkbox" name="activate_full_row" value="1" <?php if($activate_full_row == "1"){echo "checked";}?>>
										</div>
									  	
									  </div>
								</div>	  
								    
						      <div class="modal-footer">
						      	<input type="hidden" name="form_type" value="update_feature">
						        <button type="submit" class="btn btn-primary">Save changes</button>
						        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						      </div>
						      </form>
						    </div>
						  </div>
						</div>
						<!-- Modal end -->

				    </div>

				    

		        <?php 
				}
				?>
				
			</div>



				<script type="text/javascript">
				    var file_frame;
				    jQuery('.feature_upload_icon').on('click', function( event ){
				        var to;
				        to = jQuery(this);
				        event.preventDefault();


				        // If the media frame already exists, reopen it.
				       /* if ( file_frame ) {
				          file_frame.open();
				          return;
				        }*/

				        // Create the media frame.
				        file_frame = wp.media.frames.file_frame = wp.media({
				          title: jQuery( this ).data( 'uploader_title' ),
				          button: {
				            text: jQuery( this ).data( 'uploader_button_text' ),
				          },
				          multiple: false  // Set to true to allow multiple files to be selected
				        });

				        // When an image is selected, run a callback.
				        file_frame.on( 'select', function() {
				          // We set multiple to false so only get one image from the uploader
				          var attachment = file_frame.state().get('selection').first().toJSON();


				          to.prev().val(attachment.id);
				          // Do something with attachment.id and/or attachment.url here
				        });

				        // Finally, open the modal
				        file_frame.open();
				    });
				    jQuery(".feature_cat").click(function(){
				    	var cat_feature = [];
				    	jQuery(this).parent().parent().find("input:checked").each(function(){
                            cat_feature.push(this.value);
				    	})
				    	var feature_id = jQuery(this).attr("feature_id");
				    	jQuery.ajax({
					        type : "POST",
					        url : "<?php echo home_url();?>/wp-admin/admin-ajax.php",
					        data : {action: "save_feature_cats",'feature_id':feature_id,cat_feature:cat_feature},
					        success: function(response) {
				                   
						              
					         }
					    }); 
				    })
				</script> 


    <?php


    }

   



}
?>
