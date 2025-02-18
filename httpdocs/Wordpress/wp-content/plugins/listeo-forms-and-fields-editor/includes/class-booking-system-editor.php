<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Listeo_BookingSystem_Editor {
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

       

       	add_action('wp_ajax_save_contact_form_cat', array($this, 'save_contact_form_cat'));
        add_action('wp_ajax_nopriv_save_contact_form_cat', array($this, 'save_contact_form_cat'));


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
         add_submenu_page( 'listeo-fields-and-form', 'booking systems editor', 'booking systems editor', 'manage_options', 'listeo-booking-system-editor', array( $this, 'output' )); 
    }
    public function save_contact_form_cat(){
       
    		if(isset($_POST["contact_form_cat"]) && $_POST["contact_form_cat"] != ""){
	    		$cats = array();
	    		foreach ($_POST["contact_form_cat"] as $key1 => $value1) {
	    			$cats[] = $value1;
	    		}
	    		$cats = json_encode($cats);
	    		update_option("contact_form_cat",$cats);
	    	}else{
	    		update_option("contact_form_cat","");
	    	}
    		
    		die;
    }


    public function output(){

    		wp_enqueue_script('listeo-jquery-script', esc_url( $this->assets_url ) . 'js/jquery.min.js', array(), $this->_version);
	    	wp_enqueue_script('listeo-bootstrap-script', esc_url( $this->assets_url ) . 'js/bootstrap.min.js', array(), $this->_version);
		    wp_enqueue_style( 'listeo-bootstrap-styles', esc_url( $this->assets_url ) . 'css/bootstrap.min.css', array(), $this->_version );
	    	wp_enqueue_style( 'listeo-custom-styles', esc_url( $this->assets_url ) . 'css/custom.css', array(), $this->_version );
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
		    $contact_form_cats_json = get_option("contact_form_cat");

		    $contact_form_cats = array();
		    if($contact_form_cats_json != ""){
		    	$contact_form_cats = json_decode($contact_form_cats_json);
		    }

		    if(isset($_POST["label_submit"])){

		    	update_option( "instant_booking_label", $_REQUEST[ "instant_booking_label" ]  ); 
		    	update_option( "non_instant_booking_label", $_REQUEST[ "non_instant_booking_label" ]  ); 
		    	update_option( "contact_form_booking_label", $_REQUEST[ "contact_form_booking_label" ]  ); 
		    	update_option( "external_booking_label", $_REQUEST[ "external_booking_label" ]  ); 
		    }
		    $instant_booking_label = get_option("instant_booking_label");
		    $non_instant_booking_label = get_option("non_instant_booking_label");
		    $contact_form_booking_label = get_option("contact_form_booking_label");
		    $external_booking_label = get_option("external_booking_label");

	?>	    
	<div class="row feature_row">
		<form method="post">
	     	<div class="col-md-12">

				<div class="card" style="padding: 0;max-width:95%">
				    
					 <div class="card-body feature_cats">
					 	<label>Default booking system (contact form)</label>
					 	<div class="catss row">
					 		

						 	<?php 
						 	foreach ($all_cats as $key => $cat) {
						 	?>	
						 	<div class="col-md-4">
						 		<span class="checkboxess">
		    						<input type="checkbox" name="contact_form_cat"  class="contact_form_cat"  value="<?php echo $cat->term_id;?>" <?php if(in_array($cat->term_id, $contact_form_cats)){ echo "checked";}?>> <?php echo $cat->name;?>
		    					</span>
		    				</div>	
						 	<?php
						     }
						 	?>
						    
					 	</div>

					  	
					  </div>
				</div>
				

		    </div>
		</form>    

	</div>

	<div class="row label_booking">
		<form method="post" action="">
	     	<div class="col-md-12">

				<div class="card" style="padding: 0;max-width:100%">
				    
					 <div class="card-body">
					 	<table style="width: 100%;">
						 	<tr>
						 		<td style="width: 240px;;padding-bottom:40px"><b>Instant Booking label</b></td>
						 		<td style="padding-bottom:40px"><input type='text' name="instant_booking_label" value="<?php echo $instant_booking_label;?>" required style="width: 100%;"></td>
						 	</tr>
						 	<tr>
						 		<td style="width: 240px;padding-bottom:40px"><b>Non-Instant Booking label</b></td>
						 		<td style="padding-bottom:40px"><input type='text' name="non_instant_booking_label" value="<?php echo $non_instant_booking_label;?>" required  style="width: 100%"></td>
						 	</tr>
						 	<tr>
						 		<td style="width: 240px;padding-bottom:40px"><b>Contact form mobile single listing label</b></td>
						 		<td style="padding-bottom:40px"><input type='text' name="contact_form_booking_label" value="<?php echo $contact_form_booking_label;?>" required  style="width: 100%;"></td>
						 	</tr>
						 	<!-- <tr>
						 		<td style="width: 240px;"><b>External booking mobile single listing label</b></td>
						 		<td><input type='text' name="external_booking_label" value="<?php echo $external_booking_label;?>" required  style="width: 100%;"></td>
						 	</tr> -->
						 	<tr>
						 		<td style="width: 227px;padding-top: 40px;"><button name="label_submit"  class="btn btn-primary">Save</button></td>
						 	</tr>
						 </table>
					  	
					  </div>
				</div>
		    </div>
		</form>    

	</div>
	<script type="text/javascript">

	                jQuery(".contact_form_cat").click(function(){
				    	var contact_form_cat = [];
				    	jQuery(this).parent().parent().parent().find("input:checked").each(function(){
                            contact_form_cat.push(this.value);
				    	})
				    	//debugger;
				    	jQuery.ajax({
					        type : "POST",
					        url : "<?php echo admin_url( 'admin-ajax.php' );?>",
					        data : {action: "save_contact_form_cat",contact_form_cat:contact_form_cat},
					        success: function(response) {
				                   
						              
					         }
					    }); 
				    })
	</script>

    <?php


    }

   



}
?>
