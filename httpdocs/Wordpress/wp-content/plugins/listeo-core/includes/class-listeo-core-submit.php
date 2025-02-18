<?php
require_once(get_stylesheet_directory()."/vendor/autoload.php");

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//include_once( 'abstracts/abstract-listeo_core-form.php' );

class Listeo_Core_Submit  {

	/**
	 * Form name.
	 *
	 * @var string
	 */
	public $form_name = 'submit-listing';

	/**
	 * Listing ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $listing_id;


	/**
	 * Listing Type
	 *
	 * @var string
	 */
	protected $listing_type;


	/**
	 * Form fields.
	 *
	 * @access protected
	 * @var array
	 */
	protected $fields = array();


	/**
	 * Form errors.
	 *
	 * @access protected
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Form steps.
	 *
	 * @access protected
	 * @var array
	 */
	protected $steps = array();

	/**
	 * Current form step.
	 *
	 * @access protected
	 * @var int
	 */
	protected $step = 0;


	/**
	 * Form action.
	 *
	 * @access protected
	 * @var string
	 */
	protected $action = '';

	/**
	 * Form form_action.
	 *
	 * @access protected
	 * @var string
	 */
	protected $form_action = '';

	private static $package_id      = 0;
	private static $is_user_package = false;

	/**
	 * Stores static instance of class.
	 *
	 * @access protected
	 * @var Listeo_Core_Submit The single instance of the class
	 */
	protected static $_instance = null;

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


	/**
	 * Constructor
	 */
	public function __construct() {

		add_shortcode( 'listeo_submit_listing', array( $this, 'get_form' ) );
		//add_filter( 'ajax_query_attachments_args', array( $this, 'filter_media' ) );
	
		//add_filter( 'the_title', array( $this, 'change_page_title' ), 10, 2 );
		add_filter( 'submit_listing_steps', array( $this, 'enable_paid_listings' ), 30 );

		add_action( 'wp', array( $this, 'process' ) );

		$this->steps  = (array) apply_filters( 'submit_listing_steps', array(

			'type' => array(
				'name'     => __( 'Choose Type ', 'listeo_core' ),
				'view'     => array( $this, 'type' ),
				'handler'  => array( $this, 'type_handler' ),
				'priority' => 9
				),
			'submit' => array(
				'name'     => __( 'Submit Details', 'listeo_core' ),
				'view'     => array( $this, 'submit' ),
				'handler'  => array( $this, 'submit_handler' ),
				'priority' => 10
				),
			'preview' => array(
				'name'     => __( 'Preview', 'listeo_core' ),
				'view'     => array( $this, 'preview' ),
				'handler'  => array( $this, 'preview_handler' ),
				'priority' => 20
			),
			'done' => array(
				'name'     => __( 'Done', 'listeo_core' ),
				'view'     => array( $this, 'done' ),
				'priority' => 30
			)
		) );
		// if(get_option('listeo_new_listing_preview' )) {
		// 	unset($this->steps['preview']);
		// }

		$listing_types = get_option('listeo_listing_types',array( 'service', 'rental', 'event','classifieds' ));
		if(empty($listing_types)){
			unset($this->steps['type']);
		}
		if(is_array($listing_types) && sizeof($listing_types) == 1 ){
			unset($this->steps['type']);	
		}	
		uasort( $this->steps, array( $this, 'sort_by_priority' ) );


		if ( ! empty( $_POST['package'] ) ) {
			if ( is_numeric( $_POST['package'] ) ) {
	
				self::$package_id      = absint( $_POST['package'] );
				self::$is_user_package = false;
			} else {
			
				self::$package_id      = absint( substr( $_POST['package'], 5 ) );
				self::$is_user_package = true;
			}
		} elseif ( ! empty( $_COOKIE['chosen_package_id'] ) ) {
			self::$package_id      = absint( $_COOKIE['chosen_package_id'] );
			self::$is_user_package = absint( $_COOKIE['chosen_package_is_user_package'] ) === 1;
		}

		// Get step/listing
		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}

		$this->listing_id = ! empty( $_REQUEST[ 'listing_id' ] ) ? absint( $_REQUEST[ 'listing_id' ] ) : 0;
		$this->listing_type = ! empty( $_REQUEST[ '_listing_type' ] ) ?  $_REQUEST[ '_listing_type' ]  : false;
		
		if(is_array($listing_types) && sizeof($listing_types) == 1 ){
			$this->listing_type = $listing_types[0];
		}	

		if(!is_admin() && isset($_GET["action"]) && $_GET["action"] == 'edit' ) {
		 	$this->form_action = "editing";
		 	unset($this->steps['type']);
		 	$this->listing_id = ! empty( $_GET[ 'listing_id' ] ) ? absint( $_GET[ 'listing_id' ] ) : 0;
		 	
		 	if(self::$package_id==0){
		 		self::$package_id = get_post_meta($_GET[ 'listing_id' ],'_package_id',true);
		 		if(get_post_meta($_GET[ 'listing_id' ],'_user_package_id',true)){
		 			self::$is_user_package = true;
		 		}
		 		//self::$is_user_package = get_post_meta($_GET[ 'listing_id' ],'_user_package_id',true);

		 	}
		} 
		if(!is_admin() && isset($_GET["action"]) && $_GET["action"] == 'listing_demo' ) {

			add_action( 'init', array( $this, 'submit_listing_demo' ), 10 );

		}
		if(!is_admin() && isset($_GET["action"]) && $_GET["action"] == 'createQR' ) {
		 	$this->form_action = "createQR";
		 	unset($this->steps['type']);
		 	$this->listing_id = ! empty( $_GET[ 'listing_id' ] ) ? absint( $_GET[ 'listing_id' ] ) : 0;

		 	$writer = new PngWriter();

		 	$listing = get_post($this->listing_id);

			// Create QR code
			$qrCode = QrCode::create($_GET[ 'link' ])
			    ->setEncoding(new Encoding('UTF-8'))
			    ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
			    ->setSize(1000)
			    ->setMargin(10)
			    ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
			    ->setForegroundColor(new Color(0, 0, 0))
			  //  ->setForegroundColor(new Color(0, 132, 116))
			    ->setBackgroundColor(new Color(255, 255, 255));
			    //echo "<pre>"; print_r($qrCode); die;

			// Create generic logo
			$logo = Logo::create(get_stylesheet_directory().'/assets/Img/gibbs-qr-logo.png')
			    ->setResizeToWidth(158);

			// Create generic label
			$label = Label::create('')
			    ->setTextColor(new Color(255, 0, 0));

			$result = $writer->write($qrCode, $logo, $label);
		    
		    //echo $result->getDataUri(); die;

		    $data = $result->getDataUri();

			list($type, $data) = explode(';', $data);
			list(, $data)      = explode(',', $data);
			$data = base64_decode($data);

			$qr_name = "QR-".str_replace(" ","-",$listing->post_title);
			$qr_name = preg_replace('/[^A-Za-z0-9\-]/', '', $qr_name);

			file_put_contents(get_stylesheet_directory().'/assets/Img/'.$qr_name.'.png', $data);

			$urlll = get_stylesheet_directory().'/assets/Img/'.$qr_name.'.png';

			  header('Content-Description: File Transfer');
		      header('Content-Type: application/octet-stream');
		      header('Content-Disposition: attachment; filename="' . basename($urlll) . '"');
		      header('Expires: 0');
		      header('Cache-Control: must-revalidate');
		      header('Pragma: public');
		      header('Content-Length: ' . filesize($urlll));
		      flush(); // Flush system output buffer
		      readfile($urlll);

			exit;
		} 
		if(!is_admin() && isset($_GET["action"]) && $_GET["action"] == 'duplicate' ) {

			

		 	$this->form_action = "duplicate";
		 	unset($this->steps['type']);
		 	$this->listing_id = ! empty( $_GET[ 'listing_id' ] ) ? absint( $_GET[ 'listing_id' ] ) : 0;

		 	

		 	add_action( 'init', array( $this, 'duplicate_listings_init' ), 5 );





           
		 	
		} 

		if(isset($_GET["action"]) && $_GET["action"] == 'renew' ) {
		 	$this->form_action = "renew";
		 	unset($this->steps['type']);
		 	$this->listing_id = ! empty( $_GET[ 'listing_id' ] ) ? absint( $_GET[ 'listing_id' ] ) : 0;
		 	if(self::$package_id==0){
		 		self::$package_id = get_post_meta($_GET[ 'listing_id' ],'_package_id',true);
		 		if(get_post_meta($_GET[ 'listing_id' ],'_user_package_id',true)){
		 			self::$is_user_package = true;
		 		}
		 		//self::$is_user_package = get_post_meta($_GET[ 'listing_id' ],'_user_package_id',true);

		 	}
		}

		// if(get_post_meta($this->listing_id, '_listing_type', true)) {
		// 	unset($this->steps['type']);
		// }

		$this->listing_edit = false;
		if ( ! isset( $_GET[ 'new' ] ) && ( ! $this->listing_id ) && ! empty( $_COOKIE['listeo-submitting-listing-id'] ) && ! empty( $_COOKIE['listeo-submitting-listing-key'] ) ) {
			$listing_id     = absint( $_COOKIE['listeo-submitting-listing-id'] );
			$listing_status = get_post_status( $listing_id );

			if ( ( 'preview' === $listing_status || 'pending_payment' === $listing_status ) && get_post_meta( $listing_id, '_submitting_key', true ) === $_COOKIE['listeo-submitting-listing-key'] ) {
				$this->listing_id = $listing_id;
				$this->listing_edit = get_post_meta( $listing_id, '_submitting_key', true );
				
			}
		}
		// Load listing details
/*		if ( $this->listing_id ) {
			$listing_status = get_post_status( $this->listing_id );
			//whats that for?
			if ( ! in_array( $listing_status, apply_filters( 'listeo_core_valid_submit_listing_statuses', array( 'preview','pending_payment' ) ) ) ) {
				$this->listing_id = 0;
				$this->step   = 0;
			}
		}*/
		// We should make sure new jobs are pending payment and not published or pending.
		add_filter( 'submit_listing_post_status', array( $this, 'submit_listing_post_status' ), 10, 2 );

	}

	public function duplicate_listings_init(){



		$this->duplicateListing($this->listing_id,"duplicate");
            
		 	
        header("Location: ".$_GET[ 'current_page' ]);
		exit;

	}
	public function submit_listing_demo(){

		if(isset($_POST["listing_id"]) && isset($_POST["title"])){

			//$listing_id = ! empty( $_GET[ 'listing_id' ] ) ? absint( $_GET[ 'listing_id' ] ) : 0;


			$userr = new WP_User( get_current_user_id() );

			if(isset($userr->roles)){
				if(!in_array("administrator", $userr->roles) && !in_array("editor", $userr->roles) ){

					if(in_array("owner", $userr->roles) ){
						$userr->remove_role( 'owner' );
						
					}
					$userr->add_role( 'editor' );
					

				}
			}

			$this->duplicateListing($_POST["listing_id"],"listing_demo",$_POST);
		}
		
		header("Location: ".$_GET[ 'current_page' ]);
		exit;
	}

	public function saveGalleryPost($oldpost){
		    $oldpost_id = $oldpost["ID"];
            if(isset($oldpost["ID"])){
            	unset($oldpost["ID"]);
            }
            if(isset($oldpost["filter"])){
            	unset($oldpost["filter"]);
            }
            $file = get_attached_file($oldpost_id);

            $last_var = end( explode( ".", $file ));

            $file2 = str_replace(".".$last_var, "-kopi.".$last_var, $file);

         //   $filename = basename( $file );
      //  $file2 = $path . '/' . wp_unique_filename( $path, $filename );

            $datat_return = [];

            if ( @copy( $file, $file2 ) ) {
	            // Copy the attachment (post) data.
	            $att = get_post( $oldpost_id, ARRAY_A );

	            $att["post_title"] = $att["post_title"]."-kopi";
	            $att["post_name"] = $att["post_name"]."-kopi";
	            $att["guid"] = str_replace(".".$last_var, "-kopi.".$last_var, $att["guid"]);
	            unset( $att['ID'] );
	            $att_id2 = wp_insert_attachment( $att, $file2 );

	            // Copy the attachment's meta data. (no thumbnails)
	            $data = wp_get_attachment_metadata($oldpost_id);
	            $data['file'] = str_replace(".".$last_var, "-kopi.".$last_var, $data['file']);
	            $data['sizes'] = [];
	            wp_update_attachment_metadata( $att_id2, $data );

	            
			    $datat_return[$att_id2] = basename($file2);
			   
			    return $datat_return;

	        }else{
	        	return $datat_return;
	        }
/*
            global $wpdb;
            $title = $oldpost["post_title"];

        	$oldpost["post_title"] = $oldpost["post_title"]." Kopi";
        	$oldpost["post_name"] = $oldpost["post_name"]."-kopi";
        	$oldpost["post_date"] = date("Y-m-d H:i:s");
        	$oldpost["post_date_gmt"] = gmdate("Y-m-d H:i:s");
        	$oldpost["post_modified"] = date("Y-m-d H:i:s");
        	$oldpost["post_modified_gmt"] = gmdate("Y-m-d H:i:s");
        	$wpdb->insert($wpdb->prefix.'posts', $oldpost);

        	$lastid = $wpdb->insert_id;

        	$data_old = get_post_custom($oldpost_id);



        	foreach ( $data_old as $key => $values) {
		      foreach ($values as $value) {
		        add_post_meta( $lastid, $key, maybe_unserialize( $value ) );// it is important to unserialize data to avoid conflicts.
		      }
		    }*/
		   
	}

	public function duplicateListing($listing_id,$type,$demo_data = array()){


        $this->listing_id = $listing_id;
		$title   = get_the_title($this->listing_id);
            $oldpost = get_post($this->listing_id);

            $oldpost_id =  $oldpost->ID;

            $oldpost = (array) $oldpost;

            if(isset($oldpost["ID"])){
            	unset($oldpost["ID"]);
            }
            if(isset($oldpost["guid"])){
            	unset($oldpost["guid"]);
            }
            if(isset($oldpost["filter"])){
            	unset($oldpost["filter"]);
            }
            $args1 = array(
			    'post_parent' => $oldpost_id,
			    'post_type' => "listing",
			);
			$sub_listings = get_children($args1);


            
            if(!empty($oldpost)){
            	global $wpdb;
            	$copyy = "";
            	$copyy_url = "";
            	if($type == "listing_demo"){
                    $copyy = " Demo";
                    $copyy_url = "-demo";
            	}else{
            		$copyy = " Kopi";
            		$copyy_url = "-kopi";

            	}

            	


            	/*if(isset($demo_data["title"])){
                   $oldpost["post_title"] = $demo_data["title"];
                   $oldpost["post_name"] = str_replace("","-", $demo_data["title"]);
            	}else{
                   $oldpost["post_title"] = $oldpost["post_title"].$copyy;
                   $oldpost["post_name"] = $oldpost["post_name"].$copyy_url;
            	}*/
            	$oldpost["post_title"] = $oldpost["post_title"].$copyy;
                $oldpost["post_name"] = $oldpost["post_name"].$copyy_url;

                $sql_post = "select * from ".$wpdb->prefix."posts where post_title = '".$oldpost["post_title"]."'";

                $post_copy_check = $wpdb->get_results($sql_post);

                if(!empty($post_copy_check)){
                	$count_post = count($post_copy_check);

                	$oldpost["post_name"] = $oldpost["post_name"]."-".$count_post;
                }

                /*echo "<pre>"; print_r( $post_copy_check); 
                echo "<pre>"; print_r( $oldpost); 
                die;*/


            	/*if(isset($demo_data["description"])){
                   $oldpost["post_content"] = $demo_data["description"];
            	}*/
            	$categories_data = get_the_terms( $oldpost_id, 'service_category' );
            	$listing_feature_data = get_the_terms( $oldpost_id, 'listing_feature' );

            	$catss = array();

            	foreach ($categories_data as $key => $categories_d) {
            		$catss[] = $categories_d->term_id;
            	}
            	$featuress = array();

            	foreach ($listing_feature_data as $key => $listing_feature_d) {
            		$featuress[] = $listing_feature_d->term_id;
            	}
            	
            	
            	$oldpost["post_date"] = date("Y-m-d H:i:s");
            	$oldpost["post_date_gmt"] = gmdate("Y-m-d H:i:s");
            	$oldpost["post_modified"] = date("Y-m-d H:i:s");
            	$oldpost["post_modified_gmt"] = gmdate("Y-m-d H:i:s");
            	

            	//echo get_current_user_id(); die;
            	if($type == "listing_demo"){
            		//$oldpost["post_status"] = 'publish';

            		$listing_user = get_current_user_id();

					$group_admin = get_group_admin();

					if($group_admin != ""){
						$listing_user = $group_admin;
					}
	            	$oldpost["post_author"] = $listing_user;
	            }
	            $oldpost["post_status"] = 'draft';



            	if(isset($oldpost["users_groups_id"]) && ($oldpost["users_groups_id"] == "0" || $oldpost["users_groups_id"] == "")){
                    unset($oldpost["users_groups_id"]);     
            	}

            	$group_admin = get_group_admin();

            	if($group_admin == ""){
	            	$group_admin = get_current_user_id();
	            }




            	$product_id = self::save_as_product($oldpost["post_title"],$oldpost["post_content"],false,$group_admin);



            	$wpdb->insert($wpdb->prefix.'posts', $oldpost);

            	$lastid = $wpdb->insert_id;

            	if($lastid != ""){

            		update_post_meta($lastid,"product_id",$product_id);

            		if(!empty($catss)){
            			wp_set_post_terms( $lastid, $catss, "service_category" );
            		}
            		if(!empty($featuress)){
            			wp_set_post_terms( $lastid, $featuress, "listing_feature" );
            		}

            		


	            	$data_old = get_post_custom($oldpost_id);

	            	$_gallery = get_post_meta($oldpost_id,"_gallery",true);

		            if(is_array($_gallery)){

		            	$kk = 0;

		            	$data_gallery = array();

		            	foreach ($_gallery as $key_gallery => $gallery) {
		                     
		                    $post_gallery  = (array) get_post($key_gallery);
		                    $post_gallery_data = $this->saveGalleryPost($post_gallery);


		                    if(is_array($post_gallery_data) && !empty($post_gallery_data)){
	                    		foreach ($post_gallery_data as $key_dd => $post_gallery) {
	                    			$data_gallery[$key_dd] = $post_gallery;
	                    			if($kk == 0){
				                        add_post_meta( $lastid, "_thumbnail_id", $key_dd );
				                    }
	                    		}

	                    	}
		                    $kk++;
		            		 
		            	}
		            	add_post_meta( $lastid, "_gallery", $data_gallery );
		            }


	            	foreach ( $data_old as $key => $values) {
	            		if($key != "_thumbnail_id"){
	            			if($key != "_gallery"){
						      foreach ($values as $value) {
						        add_post_meta( $lastid, $key, maybe_unserialize( $value ) );// it is important to unserialize data to avoid conflicts.
						      }
						    }  
					    }  
				    }
				}
				if(!empty( $sub_listings )){
					foreach ($sub_listings as $key => $sub_listing) {
						$this->subListingDuplicate($sub_listing,$lastid);
					}
				}
			   
		 	
            }


	}
	public function subListingDuplicate($oldpost,$parentId){

		    $oldpost = (array) $oldpost;
		    $oldpost_id = $oldpost["ID"];
            
            if(isset($oldpost["ID"])){
            	unset($oldpost["ID"]);
            }
            if(isset($oldpost["guid"])){
            	unset($oldpost["guid"]);
            }
            if(isset($oldpost["filter"])){
            	unset($oldpost["filter"]);
            }
            global $wpdb;
            $title = $oldpost["post_title"];

        	$oldpost["post_title"] = $oldpost["post_title"]." Kopi";
        	$oldpost["post_name"] = $oldpost["post_name"]."-kopi";
        	$oldpost["post_parent"] = $parentId;
        	$oldpost["post_date"] = date("Y-m-d H:i:s");
        	$oldpost["post_date_gmt"] = gmdate("Y-m-d H:i:s");
        	$oldpost["post_modified"] = date("Y-m-d H:i:s");
        	$oldpost["post_modified_gmt"] = gmdate("Y-m-d H:i:s");
        	$wpdb->insert($wpdb->prefix.'posts', $oldpost);

        	$lastid = $wpdb->insert_id;

        	$data_old = get_post_custom($oldpost_id);



        	foreach ( $data_old as $key => $values) {
		      foreach ($values as $value) {
		        add_post_meta( $lastid, $key, maybe_unserialize( $value ) );// it is important to unserialize data to avoid conflicts.
		      }
		    }
		    $datat_return = [];
		    $datat_return[$lastid] = $title;
		   
		    return $datat_return;
	}


	/**
	 * Processes the form result and can also change view if step is complete.
	 */
	public function process() {

		// reset cookie
		if (
			isset( $_GET[ 'new' ] ) &&
			isset( $_COOKIE[ 'listeo-submitting-listing-id' ] ) &&
			isset( $_COOKIE[ 'listeo-submitting-listing-key' ] ) &&
			get_post_meta( $_COOKIE[ 'listeo-submitting-listing-id' ], '_submitting_key', true ) == $_COOKIE['listeo-submitting-listing-key']
		) {
			delete_post_meta( $_COOKIE[ 'listeo-submitting-listing-id' ], '_submitting_key' );
			setcookie( 'listeo-submitting-listing-id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
			setcookie( 'listeo-submitting-listing-key', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );

			wp_redirect( remove_query_arg( array( 'new', 'key' ), $_SERVER[ 'REQUEST_URI' ] ) );

		}

		$step_key = $this->get_step_key( $this->step );

		if(isset( $_POST[ 'listeo_core_form' ] )) {
		

			if ( $step_key && isset( $this->steps[ $step_key ]['handler']) && is_callable( $this->steps[ $step_key ]['handler'] ) ) {
				call_user_func( $this->steps[ $step_key ]['handler'] );
			}
		}
		$next_step_key = $this->get_step_key( $this->step );
		
		// if the step changed, but the next step has no 'view', call the next handler in sequence.
		if ( $next_step_key && $step_key !== $next_step_key && ! is_callable( $this->steps[ $next_step_key ]['view'] ) ) {
			$this->process();
		}

	}

	/**
	 * Initializes the fields used in the form.
	 */
	public function init_fields() {
		
		if ( $this->fields ) {
			return;
		}

		$scale = get_option( 'scale', 'sq ft' );
		
		$currency_abbr = get_option( 'listeo_currency' );
  		
 		$currency = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
		
		$this->fields =  array(
			'basic_info' => array(
				'title' 	=> __('Basic Information','listeo_core'),
				'class' 	=> '',
				'icon' 		=> 'sl sl-icon-doc',
				'fields' 	=> array(
						'listing_title' => array(
							'label'       => __( 'Listing Title', 'listeo_core' ),
							'type'        => 'text',
							'name'       => 'listing_title',
							'tooltip'	  => __( 'Type title that will also contains an unique feature of your listing (e.g. renovated, air contidioned)', 'listeo_core' ),
							'required'    => true,
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							
						),
						'listing_category' => array(
							'label'       => __( 'Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'listing_category',
							'taxonomy'	  => 'listing_category',
							'tooltip'	  => __( 'This is main listings category', 'listeo_core' ),
							'priority'    => 10,
							'default'	  => '',
							'render_row_col' => '4',
							'multi'    	  => true,
							'required'    => false,
						),
						'event_category' => array(
							'label'       => __( 'Event Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'event_category',
							'taxonomy'	  => 'event_category',
							'tooltip'	  => __( 'Those are categories related to your listing type', 'listeo_core' ),
							'priority'    => 10,
							'before_row'  => '',
							'after_row'   => '',
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'for_type'	  => 'event'
						),
						'service_category' => array(
							'label'       => __( 'Service Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'service_category',
							'taxonomy'	  => 'service_category',
							'priority'    => 10,
							'before_row'  => '',
							'after_row'   => '',
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'for_type'	  => 'service'
						),
						'rental_category' => array(
							'label'       => __( 'Rental Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'rental_category',
							'taxonomy'	  => 'rental_category',
							'priority'    => 10,
							'before_row'  => '',
							'after_row'   => '',
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'for_type'	  => 'rental'
						),
						'classifieds_category' => array(
							'label'       => __( 'Classifieds Category', 'listeo_core' ),
							'type'        => 'term-select',
							'placeholder' => '',
							'name'        => 'classifieds_category',
							'taxonomy'	  => 'classifieds_category',
							'priority'    => 10,
							'before_row'  => '',
							'after_row'   => '',
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
							'for_type'	  => 'classifieds'
						),
						'keywords' => array(
							'label'       => __( 'Keywords', 'listeo_core' ),
							'type'        => 'text',
							'tooltip'	  => __( 'Maximum of 15 keywords related with your business, separated by coma' , 'listeo_core' ),
							'placeholder' => '',
							'name'        => 'keywords',
							
							'priority'    => 10,
							
							'default'	  => '',
							'render_row_col' => '4',
							'required'    => false,
						),
						
						'listing_feature' => array(
							'label'       	=> __( 'Other Features', 'listeo_core' ),
							'type'        	=> 'term-checkboxes',
							'taxonomy'		=> 'listing_feature',
							'name'			=> 'listing_feature',
							'class'		  	 => 'select2-single',
							'default'    	 => '',
							'priority'    	 => 2,
							'required'    => false,
						),
						
				),
			),
			'location' =>  array(
				'title' 	=> __('Location','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'icon' 		=> 'sl sl-icon-location',
				'fields' 	=> array(
					
					'_address' => array(
						'label'       => __( 'Address', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'name'        => '_address',
						'placeholder' => '',
						'class'		  => '',
						
						'priority'    => 7,
						'render_row_col' => '6'
					),				
					'_friendly_address' => array(
						'label'       => __( 'Friendly Address', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'name'        => '_friendly_address',
						'placeholder' => '',
						'tooltip'	  => __('Human readable address, if not set, the Google address will be used', 'listeo_core'),
						'class'		  => '',
						
						'priority'    => 8,
						'render_row_col' => '6'
					),	
					'region' => array(
						'label'       => __( 'Region', 'listeo_core' ),
						'type'        => 'term-select',
						'required'    => false,
						'name'        => 'region',
						'taxonomy'        => 'region',
						'placeholder' => '',
						'class'		  => '',
						
						'priority'    => 8,
						'render_row_col' => '3'
					),				
					'_place_id' => array(
						'label'       => __( 'Google Maps Place ID', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => '',
						'name'        => '_place_id',
						'class'		  => '',
						'tooltip'	=> 'Provide your Google Place ID to show Google Reviews',
						'priority'    => 9,
						'render_row_col' => '3'
					),	
					'_geolocation_long' => array(
						'label'       => __( 'Longitude', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => '',
						'name'        => '_geolocation_long',
						'class'		  => '',
						
						'priority'    => 9,
						'render_row_col' => '3'
					),				
					'_geolocation_lat' => array(
						'label'       => __( 'Latitude', 'listeo_core' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => '',
						'name'        => '_geolocation_lat',
						'class'		  => '',
						'priority'    => 10,
						
						'render_row_col' => '3'
					),
				),
			),
			'gallery' => array(
				'title' 	=> __('Gallery','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'icon' 		=> 'sl sl-icon-picture',
				'fields' 	=> array(
						'_gallery' => array(
							'label'       => __( 'Gallery', 'listeo_core' ),
							'name'       => '_gallery',
							'type'        => 'files',
							'description' => __( 'By selecting (clicking on a photo) one of the uploaded photos you will set it as Featured Image for this listing (marked by icon with star). Drag and drop thumbnails to re-order images in gallery.', 'listeo_core' ),
							'placeholder' => 'Upload images',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						),				
					
				),
			),
			'details' => array(
				'title' 	=> __('Details','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'icon' 		=> 'sl sl-icon-docs',
				'fields' 	=> array(
						'listing_description' => array(
							'label'       => __( 'Description', 'listeo_core' ),
							'name'       => 'listing_description',
							'type'        => 'wp-editor',
							'description' => '',
							'placeholder' => 'Description',
							'class'		  => '',
							'priority'    => 1,
							'required'    => true,
						),				
						'_video' => array(
							'label'       => __( 'Video', 'listeo_core' ),
							'type'        => 'text',
							'name'        => '_video',
							'required'    => false,
							'placeholder' => __( 'URL to oEmbed supported service', 'listeo_core' ),
							'class'		  => '',
							'priority'    => 5,
						),

						'_phone' => array(
							'label'       => __( 'Phone', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_phone',
							'class'		  => '',
							'priority'    => 9,
							'render_row_col' => '3'
						),		
						'_website' => array(
							'label'       => __( 'Website', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_website',
							'class'		  => '',
							
							'priority'    => 9,
							'render_row_col' => '3'
						),
						'_email' => array(
							'label'       => __( 'E-mail', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_email',
							
							'class'		  => '',
							'priority'    => 10,
							'render_row_col' => '3'
						),
						'_email_contact_widget' => array(
							'label'       => __( 'Enable Contact Widget', 'listeo_core' ),
							'type'        => 'checkbox',
							'tooltip'	  => __('With this option enabled listing will display Contact Form Widget that will send emails to this address', 'listeo_core'),
							'required'    => false,
							
							'placeholder' => '',
							'name'        => '_email_contact_widget',
							'class'		  => '',
							'priority'    => 10,
							'priority'    => 9,
							'render_row_col' => '3'
						),				
						
						'_facebook' => array(
							'label'       => __( '<i class="fa fa-facebook-square"></i> Facebook', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_facebook',
							'class'		  => 'fb-input',
							
							'priority'    => 9,
							'render_row_col' => '4'
						),	
						'_twitter' => array(
							'label'       => __( '<i class="fa fa-twitter-square"></i> Twitter', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_twitter',
							'class'		  => 'twitter-input',
							
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_youtube' => array(
							'label'       => __( '<i class="fa fa-youtube-square"></i> YouTube', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_youtube',
							'class'		  => 'youtube-input',
							
							'priority'    => 9,
							'render_row_col' => '4'
						),				
						'_instagram' => array(
							'label'       => __( '<i class="fa fa-instagram"></i> Instagram', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_instagram',
							'class'		  => 'instagram-input',
							'priority'    => 10,
							
							'render_row_col' => '4'
						),
						'_whatsapp' => array(
							'label'       => __( '<i class="fa fa-whatsapp"></i> WhatsApp', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_whatsapp',
							'class'		  => 'whatsapp-input',
							'priority'    => 10,
							'render_row_col' => '4'
						),
						'_skype' => array(
							'label'       => __( '<i class="fa fa-skype"></i> Skype', 'listeo_core' ),
							'type'        => 'text',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_skype',
							'class'		  => 'skype-input',
							'priority'    => 10,
							'render_row_col' => '4'
						),
						
						'_price_min' => array(
							'label'       => __( 'Minimum Price Range', 'listeo_core' ),
							'type'        => 'number',
							'required'    => false,
							'placeholder' => '',
							'name'        => '_price_min',
							'tooltip'	  => __('Set only minimum price to show "Prices starts from " instead of range', 'listeo_core'),
							'class'		  => '',
							'priority'    => 9,
							'render_row_col' => '6',
							'atts' => array(
								'step' => 0.1,
								'min'  => 0,
							),
						),
						'_price_max' => array(
							'label'       => __( 'Maximum Price Range', 'listeo_core' ),
							'type'        => 'number',
							'required'    => false,
							'placeholder' => '',
							'tooltip'	  => __('Set the maximum price for your service, used on filters in search form', 'listeo_core'),
							'name'        => '_price_max',
							'class'		  => '',
							'priority'    => 9,
							'render_row_col' => '6',
							'atts' => array(
								'step' => 0.1,
								'min'  => 0,
							),
						),

				),
			),
			
			'opening_hours' => array(
				'title' 	=> __('Opening Hours','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'onoff'		=> true,
				'icon' 		=> 'sl sl-icon-clock',
				'fields' 	=> array(
						'_opening_hours_status' => array(
								'label'       => __( 'Opening Hours status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => '_opening_hours_status',
						),
						
						
						'_opening_hours' => array(
							'label'       => __( 'Opening Hours', 'listeo_core' ),
							'name'       => '_opening_hours',
							'type'        => 'hours',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						),	
						'_monday_opening_hour' => array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_monday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_monday_closing_hour' => array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_monday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),	
						'_tuesday_opening_hour' => array(
							'label'       => __( 'Tuesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_tuesday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_tuesday_closing_hour' => array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_tuesday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),
	
						'_wednesday_opening_hour' => array(
							'label'       => __( 'Wednesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_wednesday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_wednesday_closing_hour' => array(
							'label'       => __( 'Wednesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_wednesday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),		
						'_thursday_opening_hour' => array(
							'label'       => __( 'Thursday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_thursday_opening_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_thursday_closing_hour' => array(
							'label'       => __( 'Thursday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_thursday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),						
						'_friday_opening_hour' => array(
							'label'       => __( 'Friday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_friday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_friday_closing_hour' => array(
							'label'       => __( 'Friday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_friday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),												
						'_saturday_opening_hour' => array(
							'label'       => __( 'saturday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_saturday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_saturday_closing_hour' => array(
							'label'       => __( 'saturday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_saturday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),														
						'_sunday_opening_hour' => array(
							'label'       => __( 'sunday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_sunday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_sunday_closing_hour' => array(
							'label'       => __( 'sunday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_sunday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),				
						'_listing_timezone' => array(
								'label'       => __( 'Listing Timezone', 'listeo_core' ),
								'type'        => 'timezone',
								'required'    => false,
								'name'        => '_listing_timezone',
						),
						
				),
			),
			'section_opening_hours' => array(
				'title' 	=> __('Section Opening Hours','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'onoff'		=> true,
				'icon' 		=> 'sl sl-icon-clock',
				'fields' 	=> array(
						'section_opening_hours_status' => array(
								'label'       => __( 'Opening Hours status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => 'section_opening_hours_status',
						),
						
						
						'section_opening_hours' => array(
							'label'       => __( 'Opening Hours', 'listeo_core' ),
							'name'       => 'section_opening_hours',
							'type'        => 'hours',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						),	
						'section_monday_opening_hour' => array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_monday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'section_monday_closing_hour' => array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_monday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),	
						'section_tuesday_opening_hour' => array(
							'label'       => __( 'Tuesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_tuesday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'section_tuesday_closing_hour' => array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_tuesday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),
	
						'section_wednesday_opening_hour' => array(
							'label'       => __( 'Wednesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_wednesday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'section_wednesday_closing_hour' => array(
							'label'       => __( 'Wednesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_wednesday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),		
						'section_thursday_opening_hour' => array(
							'label'       => __( 'Thursday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_thursday_opening_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'section_thursday_closing_hour' => array(
							'label'       => __( 'Thursday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_thursday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),						
						'section_friday_opening_hour' => array(
							'label'       => __( 'Friday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_friday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_friday_closing_hour' => array(
							'label'       => __( 'Friday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_friday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						),												
						'_saturday_opening_hour' => array(
							'label'       => __( 'saturday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_saturday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_saturday_closing_hour' => array(
							'label'       => __( 'saturday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_saturday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),														
						'_sunday_opening_hour' => array(
							'label'       => __( 'sunday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_sunday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),
						'_sunday_closing_hour' => array(
							'label'       => __( 'sunday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => 'section_sunday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						),				
						'_listing_timezone' => array(
								'label'       => __( 'Listing Timezone', 'listeo_core' ),
								'type'        => 'timezone',
								'required'    => false,
								'name'        => '_listing_timezone',
						),
						
				),
			),
			'event' => array(
				'title'		=> __( 'Event Date', 'listeo_core' ),
				//'class'		=> 'margin-top-40',
				'icon'		=> 'fa fa-money',
				'fields'	=> array(
					'_event_date' => array(
						'label'       => __( 'Event Date', 'listeo_core' ),
						'tooltip'	  => __('Select date when even will start', 'listeo_core'),
						'type'        => 'text',						
						'required'    => false,
						'name'        => '_event_date',
						'class'		  => 'input-datetime',
						'placeholder' => '',
						'priority'    => 9,
						'render_row_col' => '6'
					),
					'_event_date_end' => array(
						'label'       => __( 'Event Date End', 'listeo_core' ),
						'tooltip'	  => __('Select date when even will end', 'listeo_core'),
						'type'        => 'text',
						'required'    => false,
						'name'        => '_event_date_end',
						'class'		  => 'input-datetime',
						'placeholder' => '',
						'priority'    => 9,
						'render_row_col' => '6'
					),
					
				)
			),
			'classifieds' => array(
				'title'		=> __( 'Classifieds', 'listeo_core' ),
				//'class'		=> 'margin-top-40',
				'icon'		=> 'fa fa-bullhorn',
				'fields'	=> array(
					'_classifieds_price' => array(
						'label'       => __( 'Price ', 'listeo_core' ),
						'tooltip'	  => __('Select condition of item for sale', 'listeo_core'),
						'type'        => 'text',						
						'required'    => false,
						'name'        => '_classifieds_price',
						'class'		  => '',
						'placeholder' => '',
					
						'priority'    => 9,
						'render_row_col' => '6'
					),
						'_classifieds_condition' => array(
						'label'       => __( 'Condition ', 'listeo_core' ),
						'tooltip'	  => __('Select condition of item for sale', 'listeo_core'),
						'type'        => 'select',						
						'required'    => false,
						'name'        => '_classifieds_condition',
						'class'		  => '',
						'placeholder' => '',
						'options'   => array(
							'new' => __( 'New', 'listeo_core' ),
			    			'used' => __( 'Used', 'listeo_core' ),
			    			
						),
						'priority'    => 9,
						'render_row_col' => '6'
					),
					
					
					
				)
			),
			'menu' => array(
				'title' 	=> __('Pricing & Bookable Services','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'onoff'		=> true,
				'icon' 		=> 'sl sl-icon-book-open',
				'fields' 	=> array(
						'_menu_status' => array(
								'label'       => __( 'Menu status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => '_menu_status',
						),
						'_menu' => array(
							'label'       => __( 'Pricing', 'listeo_core' ),
							'name'       => '_menu',
							'type'        => 'pricing',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						),
						'_menu_section' => array(
							'label'       => __( 'Section', 'listeo_core' ),
							'name'       => '_menu_section',
							'type'        => 'section',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						),
				),
			),
			'booking' => array(
				'title' 	=> __('Booking','listeo_core'),
				'class' 	=> 'booking-enable',
				'onoff'		=> true,
				//'onoff_state' => 'on',
				'icon' 		=> 'fa fa-calendar-check',
				'fields' 	=> array(
					'_booking_status' => array(
							'label'       => __( 'Booking status', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_booking_status',
							
					),
				)
			),
			'slots' => array(
				'title' 	=> __('Availability','listeo_core'),
				//'class' 	=> 'margin-top-40',
				'onoff'		=> true,
				'icon' 		=> 'fa fa-calendar-check',
				'fields' 	=> array(
						'_slots_status' => array(
								'label'       => __( 'Booking status', 'listeo_core' ),
								'type'        => 'skipped',
								'required'    => false,
								'name'        => '_slots_status',
						),
						'_slots' => array(
							'label'       => __( 'Availability Slots', 'listeo_core' ),
							'name'       => '_slots',
							'type'        => 'slots',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						),				
						
				),
			),
			

			'basic_prices' => array(
				'title'		=> __('Booking prices and settings','listeo_core'),
				//'class'		=> 'margin-top-40',
				'icon'		=> 'fa fa-money',
				'fields'	=> array(
					
					'_event_tickets' => array(
						'label'       => __( 'Available Tickets', 'listeo_core' ),
						'tooltip'	  => __('How many ticekts you have to offer', 'listeo_core'),
						'type'        => 'number',
						'required'    => false,
						'name'        => '_event_tickets',
						'class'		  => '',
						'placeholder' => '',
						'priority'    => 9,
						'render_row_col' => '6'
					),

					'_normal_price' => array(
						'label'       => __( 'Regular Price', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Default price for booking on Monday - Friday', 'listeo_core'),
						'required'    => false,
						'default'           => '0',
						'placeholder' => '',
						'unit'		  => $currency,
						'name'        => '_normal_price',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6'
						
					),	

					'_weekday_price' => array(
						'label'       => __( 'Weekend Price', 'listeo_core' ),
						'type'        => 'number',
						'required'    => false,
						'tooltip'	  => __('Default price for booking on weekend', 'listeo_core'),
						'placeholder' => '',
						'name'        => '_weekday_price',
						'unit'		  => $currency,
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6'
					),	
					'_reservation_price' => array(
						'label'       => __( 'Reservation Fee', 'listeo_core' ),
						'type'        => 'number',
						'required'    => false,
						'name'        => '_reservation_price',
						'tooltip'	  => __('One time fee for booking', 'listeo_core'),
						'placeholder' => '',
						'unit'		  => $currency,
						'default'           => '0',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6'
						
					),				
					'_expired_after' => array(
						'label'       => __( 'Reservation expires after', 'listeo_core' ),
						'tooltip'	  => __('How many hours you can wait for clients payment', 'listeo_core'),
						'type'        => 'number',
						'default'     => '48',
						'required'    => false,
						'name'        => '_expired_after',
						'placeholder' => '',
						'class'		  => '',
						'unit'		  => __( 'hours', 'listeo_core' ),
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '6'
					),
						
					'_instant_booking' => array(
						'label'       => __( 'Enable Instant Booking', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('With this option enabled booking request will be immediately approved ', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_instant_booking',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3'
					),
					'_count_per_guest' => array(
						'label'       => __( 'Enable Price per Guest', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('With this option enabled regular price and weekend price will be multiplied by number of guests to estimate total cost', 'listeo_core'),
						'required'    => false,
						
						'placeholder' => '',
						'name'        => '_count_per_guest',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3'
					),	
					'_end_hour' => array(
						'label'       => __( 'Enable End Hour time-picker', 'listeo_core' ),
						'type'        => 'checkbox',
						'tooltip'	  => __('If you are not using slots, you can allow guest to set end time for booking by enabling that option ', 'listeo_core'),
						'required'    => false,
						
						'placeholder' => '',
						'name'        => '_end_hour',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => 'service'
					),	
					'_min_days' => array(
						'label'       => __( 'Minimum  stay', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Set minimum number of days for reservation', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_min_days',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3',
						'for_type'	  => 'rental'
					),
					'_max_guests' => array(
						'label'       => __( 'Maximum number of guests', 'listeo_core' ),
						'type'        => 'number',
						'tooltip'	  => __('Set maximum number of guests per reservation', 'listeo_core'),
						'required'    => false,
						'placeholder' => '',
						'name'        => '_max_guests',
						'class'		  => '',
						'priority'    => 10,
						'priority'    => 9,
						'render_row_col' => '3'
					),	
					
						
				),
			),

			'availability_calendar' => array(
				'title' 	=> __('Availability Calendar','listeo_core'),
				//'class' 	=> 'margin-top-40',
				//'onoff'		=> true,
				'icon' 		=> 'fa fa-calendar-check',
				'fields' 	=> array(

						'_availability' => array(
							'label'       => __( 'Click day in calendar to mark it as unavailable', 'listeo_core' ),
						
							'name'       => '_availability_calendar',
							'type'        => 'calendar',
							'placeholder' => '',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						),				
						
				),
			),

		);

		$this->fields = apply_filters('submit_listing_form_fields', $this->fields);
		// get listing type

		if ( ! $this->listing_type)
		{
			$listing_type_array = get_post_meta( $this->listing_id, '_listing_type' );
			$this->listing_type = $listing_type_array[0];
		}
		
		// disable opening hours everywhere outside services
		if ( $this->listing_type != 'service' && apply_filters('disable_opening_hours', true) ) 
			unset( $this->fields['opening_hours'] );

		// disable slots everywhere outside services
		if ( $this->listing_type != 'service' && apply_filters('disable_slots', true) ) 
			unset( $this->fields['slots'] );

		// disable availability calendar outside rent
		if ( $this->listing_type == 'event' && apply_filters('disable_availability_calendar', true) ) 
			unset( $this->fields['availability_calendar'] );
		

		if ( $this->listing_type != 'classifieds' ) 
			unset( $this->fields['classifieds'] );

		// disable event date calendar outside events
		if ( $this->listing_type != 'event' ) 
		{
			unset( $this->fields['event']);
			unset( $this->fields['basic_prices']['fields']['_event_tickets'] );
		} else {
			// disable fields for events
			//unset( $this->fields['basic_prices']['fields']['_normal_price'] );
			unset( $this->fields['basic_prices']['fields']['_weekday_price'] );
			unset( $this->fields['basic_prices']['fields']['_count_per_guest'] );
			unset( $this->fields['basic_prices']['fields']['_max_guests'] );

			$this->fields['basic_prices']['fields']['_event_tickets']['render_row_col'] = 3;
			$this->fields['basic_prices']['fields']['_normal_price']['render_row_col'] = 3;
			$this->fields['basic_prices']['fields']['_normal_price']['label'] = esc_html__('Ticket Price','listeo_core');
			$this->fields['basic_prices']['fields']['_reservation_price']['render_row_col'] = 3;
			$this->fields['basic_prices']['fields']['_expired_after']['render_row_col'] = 3;
		}

		//
		/* if(isset( $this->fields['menu']['fields']['_menu'])){
			 $this->fields['menu']['fields']['_hide_pricing_if_bookable'] = array(
					'type'        => 'checkboxes',
					'required'    => false,
					'placeholder' => '',
					'name'        => '_hide_pricing_if_bookable',
					'label'       => '',
					'placeholder' => '',
					'class'		  => '',
					'options'	=> array(
						'hide' => __('Hide pricing table on listing page but show bookable services in booking widget', 'listeo_core' )
					),
			);	
		} */
		//add coupon fields

		if(!get_option('listeo_remove_coupons')) {

				//get user coupons

					$current_user = wp_get_current_user();
					

					$args = array(
						'author'        	=>  $current_user->ID,
					    'posts_per_page'   => -1,
					    'orderby'          => 'title',
					    'order'            => 'asc',
					    'post_type'        => 'shop_coupon',
					    'post_status'      => 'publish',
					);
			    	$coupon_options = array();
					$coupons = get_posts( $args );
					if($coupons){
						$coupon_options[0] = esc_html__('Select coupon','listeo_core');
					}
					foreach ($coupons as $coupon) {
						$coupon_options[$coupon->ID] = $coupon->post_title;
					}

				
				
				$this->fields['coupon_section'] = array(
					'title' 	=> __('Coupon Widget Settings','listeo_core'),
					//'class' 	=> 'margin-top-40',
					'onoff'		=> true,
					'icon' 		=> 'fa fa-barcode',
					'fields' 	=> array(
							'_coupon_section_status' => array(
									'label'       => __( 'Coupon Widget status', 'listeo_core' ),
									'type'        => 'skipped',
									'required'    => false,
									'name'        => '_coupon_section_status',
							),
							'_coupon_for_widget' => array(
								'label'       => __( 'Select one of your coupons to display in sidebar in this listing view', 'listeo_core' ),
								'name'       => '_coupon_for_widget',
								'type'        => 'select',
								'placeholder' => '',
								'class'		  => '',
								'priority'    => 1,
								'options'	 => $coupon_options,
								'required'    => false,
							),				
							
					),
				);

		}
		if(isset( $this->fields['opening_hours']['fields']['_opening_hours'])){
			 $this->fields['opening_hours']['fields']['_monday_opening_hour'] = array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_monday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			 $this->fields['opening_hours']['fields']['_monday_closing_hour'] = array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_monday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);	
			$this->fields['opening_hours']['fields']['_tuesday_opening_hour'] = array(
							'label'       => __( 'Tuesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_tuesday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_tuesday_closing_hour'] = array(
							'label'       => __( 'Monday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_tuesday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_wednesday_opening_hour'] = array(
							'label'       => __( 'Wednesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_wednesday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_wednesday_closing_hour'] = array(			
							'label'       => __( 'Wednesday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_wednesday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_thursday_opening_hour'] = array(				
							'label'       => __( 'Thursday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_thursday_opening_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_thursday_closing_hour'] = array(			
							'label'       => __( 'Thursday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_thursday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						);	
			$this->fields['opening_hours']['fields']['_friday_opening_hour'] = array(							
							'label'       => __( 'Friday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_friday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_friday_closing_hour'] = array(			
							'label'       => __( 'Friday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_friday_closing_hour',
							'before_row' 	 => '',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_saturday_opening_hour'] = array(												
							'label'       => __( 'saturday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_saturday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_saturday_closing_hour'] = array(			
							'label'       => __( 'saturday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_saturday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_sunday_opening_hour'] = array(													
							'label'       => __( 'sunday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_sunday_opening_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
			$this->fields['opening_hours']['fields']['_sunday_closing_hour'] = array(			
							'label'       => __( 'sunday Opening Hour', 'listeo_core' ),
							'type'        => 'skipped',
							'required'    => false,
							'name'        => '_sunday_closing_hour',
							'priority'    => 9,
							'render_row_col' => '4'
						);
		}



		$this->fields['basic_info']['fields']['product_id'] = array(
							'name'        => 'product_id',
							'type'        => 'hidden',							
							'required'    => false,
						);

		$this->fields['gallery']['fields']['_thumbnail_id'] = array(
							'label'       => __( 'Thumbnail ID', 'listeo_core' ),
							'type'        => 'hidden',
							'name'        => '_thumbnail_id',
							'class'		  => '',
							'priority'    => 1,
							'required'    => false,
						);
		// remove parts for booking

		$packages_disabled_modules = get_option('listeo_listing_packages_options',array());
		if(empty($packages_disabled_modules)) {
			$packages_disabled_modules = array();
		}

			$package = false;

			if(!empty(self::$package_id)){
							
				if(self::$is_user_package){
					if(get_post_meta($this->listing_id,'_user_package_id',true)){
						$package = listeo_core_get_user_package( get_post_meta($this->listing_id,'_user_package_id',true) );
					} else {
						$package = listeo_core_get_user_package( self::$package_id );
					}
					
					//$package      = wc_get_product( $user_package->get_product_id() );

				} else {
					$package = wc_get_product(self::$package_id );

				}
				
			} 
		
// has_listing_booking
// has_listing_reviews
// has_listing_social_links
// has_listing_opening_hours
// has_listing_video
// has_listing_coupons
// has_listing_gallery

			global $wpdb;


			

			foreach ( $this->fields as $group_key => $group_fields ) {
				foreach ( $group_fields['fields']  as $key => $field ) {

					if($key == '_user_groups_id') {


						$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true ); 

						if($active_group_id != "" && $active_group_id != 0){

                            $users_and_users_groups_licence = $wpdb->prefix . 'users_and_users_groups_licence';  // table name
                            $users_and_users_groups_licence_sql = "select *  from `$users_and_users_groups_licence` where users_groups_id = $active_group_id AND licence_id = 1 AND licence_is_active = '1'";
                            $licence_data = $wpdb->get_results($users_and_users_groups_licence_sql);

                            if(empty($licence_data)){
                               unset( $this->fields[$group_key]['fields'][$key] );		
                            }
                        } 
					}
					

					if(in_array('option_booking',$packages_disabled_modules)){
						if($key == '_booking_status') {
							if($package && $package->has_listing_booking() == 1){

							} else {
							  unset( $this->fields[$group_key] );	
							}
						}
					}
					if(in_array('option_social_links',$packages_disabled_modules)){
						if( in_array ( $key, array('_facebook','_twitter','_youtube','_instagram','_whatsapp','_skype','_website') ) ) {
								if($package && $package->has_listing_social_links() == 1){

								} else {
									unset( $this->fields[$group_key]['fields'][$key] );		
								}
							
						}
					}
					if(in_array('option_opening_hours',$packages_disabled_modules)){

						if($key == '_opening_hours') {
							if($package && $package->has_listing_opening_hours() == 1){

							} else {
								unset( $this->fields[$group_key] );
							}
						}
					}

					if($key == '_gallery') {
						if(!empty(self::$package_id)){
								//	$gallery_limit = self::$package_id;
							
							
							if($package->get_option_gallery_limit()){
								$gallery_limit = $package->get_option_gallery_limit();
							} else {
								$gallery_limit = get_option('listeo_max_files',10);
							}
							
						} else {
							$gallery_limit = get_option('listeo_max_files',10);
						}
						$this->fields[$group_key]['fields'][$key]['max_files'] = $gallery_limit;
					}
				
					if(in_array('option_gallery',$packages_disabled_modules)){
						if($key == '_gallery') {
							if($package && $package->has_listing_gallery() == 1){

							} else {
								unset( $this->fields[$group_key] );
							}
						}
					}

					if(in_array('option_video',$packages_disabled_modules)){

						if($key == '_video') {
							if($package && $package->has_listing_video() == 1){

							} else {
								unset( $this->fields[$group_key]['fields'][$key] );
							}
						}
					}
					if(in_array('option_coupons',$packages_disabled_modules)){
						if($key == '_coupon_section_status') {
							if($package && $package->has_listing_coupons() == 1){

							} else {
							unset( $this->fields['coupon_section'] );
							}
						}
					}
				}
			}


       
		switch ( $this->listing_type) {
			case 'event':
				foreach ( $this->fields as $group_key => $group_fields ) {

					if( $group_key == "coupon_section"){

                        if(isset($this->fields["coupons"])){

                            if(isset($this->fields["coupons"]["title"])){
                            	$this->fields[$group_key]["title"] = $this->fields["coupons"]["title"];
                            }

                            if(isset($this->fields["coupons"]["onoff"])){
                            	$this->fields[$group_key]["onoff"] = $this->fields["coupons"]["onoff"];
                            }

                            if(isset($this->fields["coupons"]["onoff_state"])){
                            	$this->fields[$group_key]["onoff_state"] = $this->fields["coupons"]["onoff_state"];
                            }

                            if(isset($this->fields["coupons"]["icon"]) && $this->fields["coupons"]["icon"] != ""){
                            	$this->fields[$group_key]["icon"] = $this->fields["coupons"]["icon"];
                            }

                            if(isset($this->fields["coupons"]["for_type"])){
                            	$this->fields[$group_key]["for_type"] = $this->fields["coupons"]["for_type"];
                            }

                            if(isset($this->fields["coupons"]["selected_cats"])){
                            	$this->fields[$group_key]["selected_cats"] = $this->fields["coupons"]["selected_cats"];
                            }

                            if(isset($this->fields["coupons"]["show_field_if_booking_enable"])){
                            	$this->fields[$group_key]["show_field_if_booking_enable"] = $this->fields["coupons"]["show_field_if_booking_enable"];
                            }

                        }else{
                           unset($this->fields[$group_key]);
                        }

						
					}

					if(isset($group_fields["show_field_if_group_active"]) && $group_fields["show_field_if_group_active"] == 1){

						$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

						if($active_group_id == "" || $active_group_id == null || $active_group_id < 1){
                           unset($this->fields[$group_key]);
						}

					}

					if(isset($group_fields["selected_cats"]) && !empty($group_fields["selected_cats"])){

						$selected_cat_top = "";

						if(isset($_GET["listing_id"]) && $_GET['listing_id'] != null){
    
						    $listing_type = get_post_meta( $_GET['listing_id'], '_listing_type', true );

						    if($listing_type == "service"){
						    	$texnomy = "service_category";
						    }elseif($listing_type == "event"){
						    	$texnomy = "event_category";
						    }elseif($listing_type == "rental"){
						    	$texnomy = "rental_category";
						    }else{
						    	$texnomy = "classifieds_category";
						    }

							$listing_top_categories = wp_get_object_terms($_GET['listing_id'], "service_category");

						    

							foreach ($listing_top_categories as $key => $listing_top_cat) {
								$all_selected_categores[] = $listing_top_cat->term_id;
								if($listing_top_cat->parent == 0){
									$selected_cat_top = $listing_top_cat->term_id;
								}
							}
						}else{
							$selected_cat_top = $_POST['listing_top_category'];
						}



						if($selected_cat_top != ""){
							$term_name = get_term( $selected_cat_top )->slug;
							if(!in_array($term_name, $group_fields["selected_cats"])){
								unset($this->fields[$group_key]);
							}
						}
						
					}
					
                    if(isset($group_fields["for_type"])){
                    	if(!in_array("event", $group_fields["for_type"])){
                            unset($this->fields[$group_key]);
                    	}
                    }
					foreach ( $group_fields['fields']  as $key => $field ) {



						if(isset($field["hide_title"]) && $field["hide_title"] == "1"){
							unset($this->fields[$group_key]['fields'][$key]["label"]);
							unset($this->fields[$group_key]['fields'][$key]["tooltip"]);
						}
						if(isset($field["selected_cats"]) && !empty($field["selected_cats"])){

							if(isset($_POST['listing_top_category'])){
								$term_name1 = get_term( $_POST['listing_top_category'] )->slug;
								if(!in_array($term_name1, $field["selected_cats"])){
									unset($this->fields[$group_key]['fields'][$key]);
								}
							}
							
						}
						if(isset($field["show_field_if_group_active"]) && $field["show_field_if_group_active"] == 1){

							$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

							if($active_group_id == "" || $active_group_id == null || $active_group_id < 1){
	                          unset($this->fields[$group_key]['fields'][$key]);
							}

						}
						if ( !empty($field['for_type'])){

							if(is_array($field['for_type'])){

								if (  !in_array("event", $field['for_type'] ) ) {

									unset($this->fields[$group_key]['fields'][$key]);
								}


							}else{
								unset($this->fields[$group_key]['fields'][$key]);
							}
							
						}
					}
				}


		// 		//unset( $this->fields['fields']['event_category']);
		// 		unset( $this->fields['basic_info']['fields']['service_category']);
		// 		unset( $this->fields['basic_info']['fields']['rental_category']);
			break;
			case 'service':

			//echo "<pre>"; print_r($this->fields); die;


				foreach ( $this->fields as $group_key => $group_fields ) {










					if( $group_key == "coupon_section"){

                        if(isset($this->fields["coupons"])){

                            if(isset($this->fields["coupons"]["title"])){
                            	$this->fields[$group_key]["title"] = $this->fields["coupons"]["title"];
                            }

                            if(isset($this->fields["coupons"]["onoff"])){
                            	$this->fields[$group_key]["onoff"] = $this->fields["coupons"]["onoff"];
                            }

                            if(isset($this->fields["coupons"]["onoff_state"])){
                            	$this->fields[$group_key]["onoff_state"] = $this->fields["coupons"]["onoff_state"];
                            }

                            if(isset($this->fields["coupons"]["icon"]) && $this->fields["coupons"]["icon"] != ""){
                            	$this->fields[$group_key]["icon"] = $this->fields["coupons"]["icon"];
                            }

                            if(isset($this->fields["coupons"]["for_type"])){
                            	$this->fields[$group_key]["for_type"] = $this->fields["coupons"]["for_type"];
                            }

                            if(isset($this->fields["coupons"]["selected_cats"])){
                            	$this->fields[$group_key]["selected_cats"] = $this->fields["coupons"]["selected_cats"];
                            }

                            if(isset($this->fields["coupons"]["show_field_if_booking_enable"])){
                            	$this->fields[$group_key]["show_field_if_booking_enable"] = $this->fields["coupons"]["show_field_if_booking_enable"];
                            }

                        }else{
                           unset($this->fields[$group_key]);
                        }

						
					}


					if(isset($group_fields["selected_cats"]) && !empty($group_fields["selected_cats"])){

						$selected_cat_top = "";

						if(isset($_GET["listing_id"]) && $_GET['listing_id'] != null){
    
						    $listing_type = get_post_meta( $_GET['listing_id'], '_listing_type', true );

						    if($listing_type == "service"){
						    	$texnomy = "service_category";
						    }elseif($listing_type == "event"){
						    	$texnomy = "event_category";
						    }elseif($listing_type == "rental"){
						    	$texnomy = "rental_category";
						    }else{
						    	$texnomy = "classifieds_category";
						    }

							$listing_top_categories = wp_get_object_terms($_GET['listing_id'], "service_category");

						    

							foreach ($listing_top_categories as $key => $listing_top_cat) {
								$all_selected_categores[] = $listing_top_cat->term_id;
								if($listing_top_cat->parent == 0){
									$selected_cat_top = $listing_top_cat->term_id;
								}
							}
						}else{
							$selected_cat_top = $_POST['listing_top_category'];
						}



						if($selected_cat_top != ""){
							$term_name = get_term( $selected_cat_top )->slug;
							if(!in_array($term_name, $group_fields["selected_cats"])){
								unset($this->fields[$group_key]);
							}
						}
						
					}

					if(isset($group_fields["show_field_if_group_active"]) && $group_fields["show_field_if_group_active"] == 1){



						$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

						if($active_group_id == "" || $active_group_id == null || $active_group_id < 1){
                           unset($this->fields[$group_key]);
						}

					}




					if(isset($group_fields["for_type"])){
                    	if(!in_array("service", $group_fields["for_type"])){
                            unset($this->fields[$group_key]);
                    	}
                    }
                    

					foreach ( $group_fields['fields']  as $key => $field ) {


						if(isset($field["hide_title"]) && $field["hide_title"] == "1"){
							unset($this->fields[$group_key]['fields'][$key]["label"]);
							unset($this->fields[$group_key]['fields'][$key]["tooltip"]);
						}
						if(isset($field["selected_cats"]) && !empty($field["selected_cats"])){

							if(isset($_POST['listing_top_category'])){
								$term_name1 = get_term( $_POST['listing_top_category'] )->slug;
								if(!in_array($term_name1, $field["selected_cats"])){
									unset($this->fields[$group_key]['fields'][$key]);
								}
							}
							
						}

						if(isset($field["show_field_if_group_active"]) && $field["show_field_if_group_active"] == 1){



							$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );



							if($active_group_id == "" || $active_group_id == null || $active_group_id < 1){

							  //	echo "<pre>"; print_r($this->fields[$group_key]['fields'][$key]); die;
	                          unset($this->fields[$group_key]['fields'][$key]);
							}

						}
						if ( !empty($field['for_type'])){

							if(is_array($field['for_type'])){

								if (  !in_array("service", $field['for_type'] ) ) {

									unset($this->fields[$group_key]['fields'][$key]);
								}


							}else{
								unset($this->fields[$group_key]['fields'][$key]);
							}

							

							
						}
					}
				}
		// 		unset( $this->fields['basic_info']['fields']['event_category']);
		// 		//unset( $this->fields['fields']['service_category']);
		// 		unset( $this->fields['basic_info']['fields']['rental_category']);
			break;
			case 'rental':



			    
				foreach ( $this->fields as $group_key => $group_fields ) {

					if( $group_key == "coupon_section"){

                        if(isset($this->fields["coupons"])){

                            if(isset($this->fields["coupons"]["title"])){
                            	$this->fields[$group_key]["title"] = $this->fields["coupons"]["title"];
                            }

                            if(isset($this->fields["coupons"]["onoff"])){
                            	$this->fields[$group_key]["onoff"] = $this->fields["coupons"]["onoff"];
                            }

                            if(isset($this->fields["coupons"]["onoff_state"])){
                            	$this->fields[$group_key]["onoff_state"] = $this->fields["coupons"]["onoff_state"];
                            }

                            if(isset($this->fields["coupons"]["icon"]) && $this->fields["coupons"]["icon"] != ""){
                            	$this->fields[$group_key]["icon"] = $this->fields["coupons"]["icon"];
                            }

                            if(isset($this->fields["coupons"]["for_type"])){
                            	$this->fields[$group_key]["for_type"] = $this->fields["coupons"]["for_type"];
                            }

                            if(isset($this->fields["coupons"]["selected_cats"])){
                            	$this->fields[$group_key]["selected_cats"] = $this->fields["coupons"]["selected_cats"];
                            }

                            if(isset($this->fields["coupons"]["show_field_if_booking_enable"])){
                            	$this->fields[$group_key]["show_field_if_booking_enable"] = $this->fields["coupons"]["show_field_if_booking_enable"];
                            }

                        }else{
                           unset($this->fields[$group_key]);
                        }

						
					}

					if(isset($group_fields["selected_cats"]) && !empty($group_fields["selected_cats"])){

						$selected_cat_top = "";

						if(isset($_GET["listing_id"]) && $_GET['listing_id'] != null){
    
						    $listing_type = get_post_meta( $_GET['listing_id'], '_listing_type', true );

						    if($listing_type == "service"){
						    	$texnomy = "service_category";
						    }elseif($listing_type == "event"){
						    	$texnomy = "event_category";
						    }elseif($listing_type == "rental"){
						    	$texnomy = "rental_category";
						    }else{
						    	$texnomy = "classifieds_category";
						    }

							$listing_top_categories = wp_get_object_terms($_GET['listing_id'], "service_category");

						    

							foreach ($listing_top_categories as $key => $listing_top_cat) {
								$all_selected_categores[] = $listing_top_cat->term_id;
								if($listing_top_cat->parent == 0){
									$selected_cat_top = $listing_top_cat->term_id;
								}
							}
						}else{
							$selected_cat_top = $_POST['listing_top_category'];
						}



						if($selected_cat_top != ""){
							$term_name = get_term( $selected_cat_top )->slug;
							if(!in_array($term_name, $group_fields["selected_cats"])){
								unset($this->fields[$group_key]);
							}
						}
						
					}

					if(isset($group_fields["show_field_if_group_active"]) && $group_fields["show_field_if_group_active"] == 1){

						$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

						if($active_group_id == "" || $active_group_id == null || $active_group_id < 1){
                           unset($this->fields[$group_key]);
						}

					}



					if(isset($group_fields["for_type"])){
	                	if(!in_array("rental", $group_fields["for_type"])){
	                        unset($this->fields[$group_key]);
	                	}
	                }
					foreach ( $group_fields['fields']  as $key => $field ) {
						if(isset($field["hide_title"]) && $field["hide_title"] == "1"){
							unset($this->fields[$group_key]['fields'][$key]["label"]);
							unset($this->fields[$group_key]['fields'][$key]["tooltip"]);
						}

						if(isset($field["selected_cats"]) && !empty($field["selected_cats"])){

							if(isset($_POST['listing_top_category'])){
								$term_name1 = get_term( $_POST['listing_top_category'] )->slug;
								if(!in_array($term_name1, $field["selected_cats"])){
									unset($this->fields[$group_key]['fields'][$key]);
								}
							}
							
						}
						if(isset($field["show_field_if_group_active"]) && $field["show_field_if_group_active"] == 1){

							$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

							if($active_group_id == "" || $active_group_id == null || $active_group_id < 1){
	                          unset($this->fields[$group_key]['fields'][$key]);
							}

						}
						/*if ( !empty($field['for_type']) && in_array($field['for_type'],array('event','service','classifieds') ) ) {
							unset($this->fields[$group_key]['fields'][$key]);
						}*/
						if ( !empty($field['for_type'])){

							if(is_array($field['for_type'])){

								if (  !in_array("rental", $field['for_type'] ) ) {

									unset($this->fields[$group_key]['fields'][$key]);
								}


							}else{
								unset($this->fields[$group_key]['fields'][$key]);
							}

							

							
						}
					}
				}
			break;
			case 'classifieds':
				foreach ( $this->fields as $group_key => $group_fields ) {
					foreach ( $group_fields['fields']  as $key => $field ) {
						if(isset($field["hide_title"]) && $field["hide_title"] == "1"){
							unset($this->fields[$group_key]['fields'][$key]["label"]);
							unset($this->fields[$group_key]['fields'][$key]["tooltip"]);
						}
						if ( !empty($field['for_type']) && in_array($field['for_type'],array('event','service','rental') ) ) {
							unset($this->fields[$group_key]['fields'][$key]);
						}
					}
				}
				unset( $this->fields['booking'] );
				unset( $this->fields['slots'] );
				unset( $this->fields['basic_prices'] );
				unset( $this->fields['availability_calendar'] );
				unset( $this->fields['coupon_section'] );
				unset( $this->fields['menu'] );
		// 		unset( $this->fields['basic_info']['fields']['event_category']);
		// 		unset( $this->fields['basic_info']['fields']['service_category']);
		// 		//unset( $this->fields['fields']['rental_category']);
		 	break;
			
		 	default:
		// 		unset( $this->fields['basic_info']['fields']['event_category']);
		// 		unset( $this->fields['basic_info']['fields']['service_category']);
		// 		unset( $this->fields['basic_info']['fields']['rental_category']);
		 		break;
		 }

		 unset($this->fields["coupons"]);

		if(get_option('listeo_bookings_disabled')){
			unset( $this->fields['booking'] );
			unset( $this->fields['slots'] );
			unset( $this->fields['basic_prices'] );
			unset( $this->fields['availability_calendar'] );
		}
		//echo "<pre>";print_r($this->fields); die;
		
	}

	/**
	 * Validates the posted fields.
	 *
	 * @param array $values
	 * @throws Exception Uploaded file is not a valid mime-type or other validation error
	 * @return bool|WP_Error True on success, WP_Error on failure
	 */
	protected function validate_fields( $values ) {

		foreach ( $this->fields as $group_key => $group_fields ) {
			
			foreach ( $group_fields['fields']  as $key => $field ) {

				if ( $field['type'] != 'header' && isset($field['required']) && $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {
					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'listeo_core' ), $field['label'] ) );
				}
				if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], array( 'term-checkboxes', 'term-select', 'term-multiselect' ) ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = $values[ $group_key ][ $key ];
					} else {
						$check_value = empty( $values[ $group_key ][ $key ] ) ? array() : array( $values[ $group_key ][ $key ] );
					}

					foreach ( $check_value as $term ) {
						if ( (int) $term != -1 ){

							if ( ! term_exists( (int) $term, $field['taxonomy'] ) ) {

								return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'listeo_core' ), $field['label'] ) );
							}
						}
					}
				}

				// if ( 'file' === $field['type'] && ! empty( $field['allowed_mime_types'] ) ) {
				// 	if ( is_array( $values[ $group_key ][ $key ] ) ) {
				// 		$check_value = array_filter( $values[ $group_key ][ $key ] );
				// 	} else {
				// 		$check_value = array_filter( array( $values[ $group_key ][ $key ] ) );
				// 	}
					
				// 	if ( ! empty( $check_value ) ) {
				// 		foreach ( $check_value as $file_url ) {
				// 			$file_url  = current( explode( '?', $file_url ) );
				// 			$file_info = wp_check_filetype( $file_url );

				// 			if ( ! is_numeric( $file_url ) && $file_info && ! in_array( $file_info['type'], $field['allowed_mime_types'] ) ) {
				// 				throw new Exception( sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s', 'listeo_core' ), $field['label'], $file_info['ext'], implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) );
				// 			}
				// 		}
				// 	}
				// }
			}
		}
	
		return apply_filters( 'submit_listing_form_validate_fields', true, $this->fields, $values );
	}



	/**
	 * Displays the form.
	 */
	public function submit() {

		$this->init_fields();
		$template_loader = new Listeo_Core_Template_Loader;
		if ( ! is_user_logged_in() ) {
			$template_loader->get_template_part( 'listing-sign-in' );
			$template_loader->get_template_part( 'account/login' ); 
		} else {


		if ( is_user_logged_in() && $this->listing_id ) {
			$listing = get_post( $this->listing_id );
			
			//basic_info/fields/listing_title
			if($listing){

				foreach ( $this->fields as $group_key => $group_fields ) {
					foreach ( $group_fields['fields'] as $key => $field ) {
					
						switch ( $key ) {
							case 'listing_title' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] = $listing->post_title;
							break;
							case 'listing_description' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] = $listing->post_content;
							break;
							case 'listing_feature' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] =  wp_get_object_terms( $listing->ID, 'listing_feature', array( 'fields' => 'ids' ) ) ;
							break;
							case 'listing_category' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] =  wp_get_object_terms( $listing->ID, 'listing_category', array( 'fields' => 'ids' ) ) ;
							break;
							case 'service_category' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] =  wp_get_object_terms( $listing->ID, 'service_category', array( 'fields' => 'ids' ) ) ;
							break;
							case 'rental_category' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] =  wp_get_object_terms( $listing->ID, 'rental_category', array( 'fields' => 'ids' ) ) ;
							break;
							case 'event_category' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] =  wp_get_object_terms( $listing->ID, 'event_category', array( 'fields' => 'ids' ) ) ;
							break;
							
							case (substr($key,0,4) == 'tax-') :
								$tax = substr($key, 4);
								$this->fields[ $group_key ]['fields'][ $key ]['value'] =  wp_get_object_terms( $listing->ID, $tax, array( 'fields' => 'ids' ) ) ;
								
							break;
							
							case 'section_opening_hours' :

								$days = listeo_get_days();
								$section_opening_hours = array();
								foreach ($days as $d_key => $value) {
									$value_day = get_post_meta( $listing->ID, 'section_'.$d_key.'_opening_hour', true );
									if($value_day){
										$section_opening_hours[$d_key.'_opening'] = $value_day;
									}
									$value_day = get_post_meta( $listing->ID, 'section_'.$d_key.'_closing_hour', true );
									if($value_day){
										$section_opening_hours[$d_key.'_closing'] = $value_day;
									}
								
									
								}
								
								$this->fields[ $group_key ]['fields'][ $key ]['value'] = $section_opening_hours;
							break;

							case '_opening_hours' :

								$days = listeo_get_days();
								$opening_hours = array();
								foreach ($days as $d_key => $value) {
									$value_day = get_post_meta( $listing->ID, '_'.$d_key.'_opening_hour', true );
									if($value_day){
										$opening_hours[$d_key.'_opening'] = $value_day;
									}
									$value_day = get_post_meta( $listing->ID, '_'.$d_key.'_closing_hour', true );
									if($value_day){
										$opening_hours[$d_key.'_closing'] = $value_day;
									}
								
									
								}
								
								$this->fields[ $group_key ]['fields'][ $key ]['value'] = $opening_hours;
							break;
							case 'region' :
								$this->fields[ $group_key ]['fields'][ $key ]['value'] = wp_get_object_terms( $listing->ID, 'region', array( 'fields' => 'ids' ) );
							break;
					
							default:
							
								if(isset($this->fields[ $group_key ]['fields'][ $key ]['multi']) && $this->fields[ $group_key ]['fields'][ $key ]['multi']) {
									$this->fields[ $group_key ]['fields'][ $key ]['value'] = get_post_meta( $listing->ID, $key, false );
								} else {
									$this->fields[ $group_key ]['fields'][ $key ]['value'] = get_post_meta( $listing->ID, $key, true );
								}
								//$this->fields[ $group_key ]['fields'][ $key ]['value'] = get_post_meta( $listing->ID, $key, true );
								if($this->fields[ $group_key ]['fields'][ $key ]['type'] == 'checkboxes'){
								    $this->fields[ $group_key ]['fields'][ $key ]['value'] = get_post_meta( $listing->ID, $key, false );
								}
							break;
						}
					
					}
				}
			}
			
		}  elseif ( is_user_logged_in() && empty( $_POST['submit_listing'] ) ) {
			$this->fields = apply_filters( 'submit_listing_form_fields_get_user_data', $this->fields, get_current_user_id() );
		}
		// ini_set('xdebug.var_display_max_depth', '10');
		// ini_set('xdebug.var_display_max_children', '256');
		// ini_set('xdebug.var_display_max_data', '1024');
		// var_dump($this->fields);
		
		$template_loader->set_template_data( 
			array( 
				'action' 		=> $this->get_action(),
				'fields' 		=> $this->fields,
				'form'      	=> $this->form_name,
				'listing_edit' => $this->listing_edit,
				'listing_id'   => $this->get_listing_id(),
				'step'      	=> $this->get_step(),
				'submit_button_text' => apply_filters( 'submit_listing_form_submit_button_text', __( 'Preview', 'listeo_core' ) )
				) 
			)->get_template_part( 'listing-submit' );
		}
	} 
	

	/**
	 * Handles the submission of form data.
	 */
	public function submit_handler() {
		
		// Posted Data

		$meta = get_post_meta(6817);

		



       
        



        if(isset($_POST["tax-service_category"])){

        	$cattt = $_POST["tax-service_category"];
        	$_POST["tax-service_category"] = array();

        	if(is_array($cattt)){
                foreach ($cattt as $key => $caet) {
            		$_POST["tax-service_category"][] = $caet;
            	}
        	}else{
        		if($cattt != ""){
        		
	        		$_POST["tax-service_category"][] = $cattt;
	        	}

        	}
        	
        	
        	
            if(isset($_POST["subcats"])){
            	foreach ($_POST["subcats"] as $key => $subcats) {
            		
            		if($subcats != ""){
            			$_POST["tax-service_category"][] = $subcats;
            		}
            	}
            }

            if(isset($_POST["subcats2"])){
            	foreach ($_POST["subcats2"] as $key => $subcats2) {
            		if($subcats2 != ""){
            			$_POST["tax-service_category"][] = $subcats2;
            		}
            		
            	}
            }

            if(isset($_POST["listing_top_category"]) && $_POST["listing_top_category"] != ""){
            		$_POST["tax-service_category"][] = $_POST["listing_top_category"];
            }
        }

        if(isset($_POST["tax-rental_category"])){
        	$cattt = $_POST["tax-rental_category"];
        	/*echo "<pre>"; print_r($_POST); die;*/

        	$_POST["tax-rental_category"] = array();
        	//echo "<pre>"; print_r($_POST); die;

        	if(is_array($cattt)){

                foreach ($cattt as $key => $caet) {
            		$_POST["tax-rental_category"][] = $caet;
            	}
        	}else{
        		if($cattt != ""){
        		
	        		$_POST["tax-rental_category"][] = $cattt;
	        	}

        	}
            if(isset($_POST["subcats"])){
            	foreach ($_POST["subcats"] as $key => $subcats) {
            		
            		if($subcats != ""){
            			$_POST["tax-rental_category"][] = $subcats;
            		}
            	}
            }
            if(isset($_POST["subcats2"])){
            	foreach ($_POST["subcats2"] as $key => $subcats2) {
            		if($subcats2 != ""){
            			$_POST["tax-rental_category"][] = $subcats2;
            		}
            		
            	}
            }

            if(isset($_POST["listing_top_category"]) && $_POST["listing_top_category"] != ""){
            		$_POST["tax-rental_category"][] = $_POST["listing_top_category"];
            }
        }

        if(isset($_POST["tax-event_category"])){
        	$cattt = $_POST["tax-event_category"];
        	$_POST["tax-event_category"] = array();

        	if(is_array($cattt)){
                foreach ($cattt as $key => $caet) {
            		$_POST["tax-event_category"][] = $caet;
            	}
        	}else{
        		if($cattt != ""){
        		
	        		$_POST["tax-event_category"][] = $cattt;
	        	}

        	}
            if(isset($_POST["subcats"])){
            	foreach ($_POST["subcats"] as $key => $subcats) {
            		
            		if($subcats != ""){
            			$_POST["tax-event_category"][] = $subcats;
            		}
            	}
            }
            if(isset($_POST["subcats2"])){
            	foreach ($_POST["subcats2"] as $key => $subcats2) {
            		if($subcats2 != ""){
            			$_POST["tax-event_category"][] = $subcats2;
            		}
            		
            	}
            }

            if(isset($_POST["listing_top_category"]) && $_POST["listing_top_category"] != ""){
            		$_POST["tax-event_category"][] = $_POST["listing_top_category"];
            }
        }

        if(isset($_POST["tax-classifieds_category"])){
        	$cattt = $_POST["tax-classifieds_category"];
        	$_POST["tax-classifieds_category"] = array();

        	if(is_array($cattt)){
                foreach ($cattt as $key => $caet) {
            		$_POST["tax-classifieds_category"][] = $caet;
            	}
        	}else{
        		if($cattt != ""){
        		
	        		$_POST["tax-classifieds_category"][] = $cattt;
	        	}

        	}
            if(isset($_POST["subcats"])){
            	foreach ($_POST["subcats"] as $key => $subcats) {
            		if($subcats != ""){
            			$_POST["tax-classifieds_category"][] = $subcats;
            		}
            		
            	}
            }
            if(isset($_POST["subcats2"])){
            	foreach ($_POST["subcats2"] as $key => $subcats2) {
            		if($subcats2 != ""){
            			$_POST["tax-classifieds_category"][] = $subcats2;
            		}
            		
            	}
            }

            if(isset($_POST["listing_top_category"]) && $_POST["listing_top_category"] != ""){
            		$_POST["tax-classifieds_category"][] = $_POST["listing_top_category"];
            }
        }

        if(isset($_POST["region"]) && $_POST["region"] != ""){


        	$cattt_region = $_POST["region"];
        	$_POST["region"] = array();

        	if(is_array($cattt_region)){
                foreach ($cattt_region as $key => $cattt_reg) {
            		$_POST["region"][] = $cattt_reg;
            	}
        	}else{

        		if($cattt_region != ""){
        		
	        		$_POST["region"][] = $cattt_region;
	        	}

        	}
        	if(isset($_POST["subregion"])){
            	foreach ($_POST["subregion"] as $key => $subregion) {
            		if($subregion != ""){
            			$_POST["region"][] = $subregion;
            		}
            		
            	}
            }
           
        }
        $discount_user = array();

        if(isset($_POST["discount_user"]) && count($_POST["discount_user"]) > 0){
        	
        	$kk = 0;
        	foreach ($_POST["discount_user"] as $key_dis => $value_dis) {
        		 $discount_user[$kk]["discount_name"] = $value_dis;
        		 $discount_user[$kk]["discount_value"] = $_POST["discount_user_percentage"][$kk];
        		 $kk++;
        	}
        }





		try {
			// Init fields
			$this->init_fields();


			

			

			

			if(isset($_POST["_booking_system_rental"]) && $_POST["_booking_system_rental"] == "on"){
				$_POST["_slots"] = "[[\"00:00 - 24:00|1\"],[\"00:00 - 24:00|1\"],[\"00:00 - 24:00|1\"],[\"00:00 - 24:00|1\"],[\"00:00 - 24:00|1\"],[\"00:00 - 24:00|1\"],[\"00:00 - 24:00|1\"]]";
				$_POST["_listing_type"] = "rental";
			}else{
				$_POST["_listing_type"] = "service";
			}

			// if(isset($_POST["_booking_system_service"]) && $_POST["_booking_system_service"] == "on"){
			// 	$_POST["_listing_type"] = "service";
			// }

			// if(isset($_POST["_booking_system_weekly_view"]) && $_POST["_booking_system_weekly_view"] == "on"){
			// 	$_POST["_listing_type"] = "service";
			// }

			if(isset($_POST["_tax"]) && $_POST["_tax"] == ""){
				$_POST["_tax"] = 0;
			}

		


			// Get posted values
			$values = $this->get_posted_fields();

			$userr = new WP_User( get_current_user_id() );

			if(isset($userr->roles)){
				if(!in_array("administrator", $userr->roles) && !in_array("editor", $userr->roles) ){

					if(in_array("owner", $userr->roles) ){
						$userr->remove_role( 'owner' );
						$userr->add_role( 'editor' );
					}
					

				}
			}

			

			// $slotss = array();

			
			$slotss = array();

			$standard_patern = array();

			

			if(isset($_POST["slots_type"]) && $_POST["slots_type"] == "standard"){
				if(isset($_POST["standard_from_day"]) && !empty($_POST["standard_from_day"])){
					$kinc = 0;

					for ($ik=0; $ik < count($_POST["standard_from_day"]) ; $ik++) { 

						$standard_from_day = $_POST["standard_from_day"][$ik];
						$standard_from_time = $_POST["standard_from_time"][$ik].":00";
						$standard_to_time = $_POST["standard_to_time"][$ik].":00";
						$standard_closed = $_POST["standard_closed"][$ik];
						$standard_duration = $_POST["standard_duration"][$ik];
						$standard_slot_price = $_POST["standard_slot_price"][$ik];
						$standard_slots = $_POST["standard_slots"][$ik];
						$standard_all_slot_price = $_POST["standard_all_slot_price"][$ik];

						

						if($standard_closed == ""){
							$standard_closed = "0";
						}
						$standard_patern[$ik]["standard_from_day"] = $_POST["standard_from_day"][$ik];
						$standard_patern[$ik]["standard_from_time"] = $_POST["standard_from_time"][$ik];
						$standard_patern[$ik]["standard_to_time"] = $_POST["standard_to_time"][$ik];
						$standard_patern[$ik]["standard_closed"] = $standard_closed;
						$standard_patern[$ik]["standard_duration"] = $_POST["standard_duration"][$ik];
						$standard_patern[$ik]["standard_slot_price"] = $_POST["standard_slot_price"][$ik];
						$standard_patern[$ik]["standard_slots"] = $_POST["standard_slots"][$ik];
						$standard_patern[$ik]["standard_all_slot_price"] = $_POST["standard_all_slot_price"][$ik];

						// $datetime_1 = '1970-01-01 '.$standard_from_time; 
						// $datetime_2 = '1970-01-01 '.$standard_to_time; 

						
						// $from_time = strtotime($datetime_1); 
						// $to_time = strtotime($datetime_2); 
						// $diff_minutes = round(abs($from_time - $to_time) / 60,2). " minutes"; 

						// if((int) $standard_duration > $diff_minutes){
						// 	continue;
						// }

						// $startTime = new DateTime($standard_from_time);
						// $endTime = new DateTime($standard_to_time);

						// $current = clone $startTime;

						// $kinc = 0;
						

						// while ($current <= $endTime) {

						// 	$fr_tm = $current->format('H:i');

						// 	$current->add(new DateInterval('PT'.$standard_duration.'M'));

						// 	$to_tm = $current->format('H:i');

						// 	$startTime2 = new DateTime($fr_tm);
						//     $endTime2 = new DateTime($standard_to_time);

						// 	if($startTime2 < $endTime2 ){
						// 		if($kinc > 0 && $to_tm == "00:00"){
						// 			$to_tm = "23:59";
						// 		}

						// 		$slotss[] =  $standard_from_day."|".$fr_tm."|".$standard_from_day."|".$to_tm."|".$standard_slot_price."|".$standard_slots."|".$kinc."|".$standard_closed;
								
						// 	}

						// 	$kinc++;

						// }

						// Set the interval duration in minutes
						$intervalMinutes = $standard_duration;

						// Set the start and end times
						$startTime = strtotime("2024-01-25 ".$standard_from_time); // Replace with your desired start time
						$endTime = strtotime("2024-01-25 ".$standard_to_time);   // Replace with your desired end time

						// Get the current timestamp
						$currentTimestamp = $startTime;

						// Create an array to store the intervals
						$intervals = [];

						// Generate the intervals
						while ($currentTimestamp < $endTime) {
							// Calculate the end time of the interval
							$endTimeInterval = $currentTimestamp + ($intervalMinutes * 60);

							

							// Check if the calculated end time is within the specified end time
							if ($endTimeInterval > $endTime) {
								if(date("H:i",$endTimeInterval) == "00:00"){
									$endTimeInterval = $endTime;
								}else{
                                    break; // Skip the last interval
								}
								
							}

							// Format the start and end times as desired (e.g., using date())
							$startFormatted = date("H:i", $currentTimestamp);
							$endFormatted = date("H:i", $endTimeInterval);

							// Store the interval in the array
							$intervals[] = [
								'start' => $startFormatted,
								'end' => $endFormatted,
							];

							// Move to the next interval
							$currentTimestamp = $endTimeInterval;
						}
						if(!empty($intervals)){
							foreach($intervals as $interval){

								$fr_tm = $interval["start"];
								$to_tm = $interval["end"];
								$slotss[] =  $standard_from_day."|".$fr_tm."|".$standard_from_day."|".$to_tm."|".$standard_slot_price."|".$standard_slots."|".$kinc."|".$standard_closed."|".$standard_all_slot_price;
								$kinc++;

							}
							
						}
						//echo "<pre>"; print_r($intervals); die;
	
						 
						 
					}
				}
			}else{
				if(isset($_POST["from_day"]) && !empty($_POST["from_day"])){

					for ($i=0; $i < count($_POST["from_day"]) ; $i++) { 

						 $slotss[] =  $_POST["from_day"][$i]."|".$_POST["from_time"][$i]."|".$_POST["to_day"][$i]."|".$_POST["to_time"][$i]."|".$_POST["slot_price"][$i]."|".$_POST["slots"][$i]."|".$i."|0|".$_POST["all_slot_price"][$i];
						
					}
				}
			}
			// echo "<pre>"; print_r($_POST); 
			// echo "<pre>"; print_r($slotss); die;
			//echo "<pre>"; print_r($standard_patern); die;

			


// Print or use the generated intervals as needed
//print_r($intervals);

			//echo "<pre>"; print_r($_POST); die;


	
			if ( empty( $_POST['submit_listing'] ) ) {
				return;
			}

			// Validate required
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}


			if ( ! is_user_logged_in() ) {
				throw new Exception( __( 'You must be signed in to post a new listing.', 'listeo_core' ) );
			}

		
			$post_title = $values['basic_info']['listing_title'];
			$post_content = $values['details']['listing_description'];
			$product_id = (isset($values['basic_info']['product_id']) && $values['basic_info']['product_id'] != "0") ? $values['basic_info']['product_id'] : '' ;

            $group_admin = get_group_admin();

            if($group_admin == ""){
            	$group_admin = get_current_user_id();
            }
			
			// Add or update listing as a WoCommerce product and save product id to values
			if(is_woocommerce_activated()){
				$values['basic_info']['product_id'] = $this -> save_as_product($post_title,$post_content,$product_id,$group_admin);	
			}

	
			
			$content = '';

			//locate listing_description
			foreach ($values as $section => $s_fields) {
				foreach ($s_fields as $key => $value) {
					if($key == 'listing_description') {
						$content = $value;
					}
				}
				
			}

			$statuss = "";

			if(isset($_POST["listing_status"]) && $_POST["listing_status"] != ""){
				$statuss = $_POST["listing_status"];
			}

			

			//Update the listing
			$this->save_listing( $values['basic_info']['listing_title'], $content, $statuss, $values );
			

			$this->update_listing_data( $values );
			if(isset($_POST["_booking_system_contact_form"])){
				update_post_meta($this->listing_id,"_booking_system_contact_form",$_POST["_booking_system_contact_form"]);
			}

			if(isset($_POST["enable_slot_price"]) && $_POST["enable_slot_price"] == "on"){
				update_post_meta($this->listing_id,"enable_slot_price",$_POST["enable_slot_price"]);
			}else{
				update_post_meta($this->listing_id,"enable_slot_price","");
			}
			if(isset($_POST["slot_price_label"]) && $_POST["slot_price_label"] != ""){
				update_post_meta($this->listing_id,"slot_price_label",$_POST["slot_price_label"]);
			}else{
				update_post_meta($this->listing_id,"slot_price_label","");
			}
			if(isset($_POST["all_slot_price_label"]) && $_POST["all_slot_price_label"] != ""){
				update_post_meta($this->listing_id,"all_slot_price_label",$_POST["all_slot_price_label"]);
			}else{
				update_post_meta($this->listing_id,"all_slot_price_label","");
			}

			if(isset($_POST["slots_type"])){
				update_post_meta($this->listing_id,"slots_type",$_POST["slots_type"]);
				update_post_meta($this->listing_id,"standard_patern",$standard_patern);
			}
			if(isset($_POST["slots_type"])){
				update_post_meta($this->listing_id,"slots_type",$_POST["slots_type"]);
			}

			if(!empty($discount_user)){

				update_post_meta($this->listing_id,"_discounts_user",$discount_user);
			}else{
				update_post_meta($this->listing_id,"_discounts_user","");
			}

			update_post_meta($this->listing_id,"_slots_status","On");



			if(isset($_POST["_booking_status"])){
				update_post_meta($this->listing_id,"_booking_status",$_POST["_booking_status"]);
			}else{
				update_post_meta($this->listing_id,"_booking_status","");
			}

			if(!empty($slotss)){

				update_post_meta($this->listing_id,"_booking_slots",$slotss);
				
			}else{
				delete_post_meta($this->listing_id,"_booking_slots");
			}

			if(isset($_POST["_tax"])){
				update_post_meta($this->listing_id,"_tax",$_POST["_tax"]);
			}
			
			if(isset($_POST["additional_service_label_name"])){
				update_post_meta($this->listing_id,"additional_service_label_name",$_POST["additional_service_label_name"]);
			}


			$org_id = $this->listing_id;

			
			if(isset($_POST["_user_groups_id"])){
				$_user_groups_id = $_POST["_user_groups_id"];
				update_post_meta($this->listing_id,"_user_groups_id",$_user_groups_id);
			}else{
				$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true ); 

				if($active_group_id != "" && $active_group_id != 0){

					update_post_meta($this->listing_id,"_user_groups_id",$active_group_id);
				    $_user_groups_id = $active_group_id;

				}else{
					update_post_meta($this->listing_id,"_user_groups_id","");
				    $_user_groups_id = null;
				}
				
			}

				
			delete_post_meta($this->listing_id,"_listing_sports");
			if(isset($_POST["_listing_sports"])){

				foreach ($_POST["_listing_sports"] as  $listing_sports) {
					add_post_meta($this->listing_id,"_listing_sports",$listing_sports);
				}

			}
			if(isset($_POST["_listing_type"])){

				update_post_meta($this->listing_id,"_listing_type",$_POST["_listing_type"]);

			}

			


			if(isset($_POST["sub_listing"]) && !empty($_POST["sub_listing"])){

				foreach ($_POST["sub_listing"] as $key_sub_listing => $sub_listing) {
					$this->listing_id = $key_sub_listing;
					$this->save_listing( $sub_listing['title'], $content, $statuss, $values );

					if(isset($_POST["_listing_type"])){

						update_post_meta($this->listing_id,"_listing_type",$_POST["_listing_type"]);

					}

					$cr_listing = get_post($org_id);

					if(isset($cr_listing->post_author)){
						$sub_author = $cr_listing->post_author;
					}else{
						$sub_author = get_current_user_id();
					}


					$sub_listing_data = array(
						'post_status'      => 'publish',
						'post_author' => $sub_author,
						'post_parent' => $org_id,
						'users_groups_id' => $_user_groups_id,
						'ID' => $this->listing_id,
					);
					wp_update_post( $sub_listing_data );

					$this->update_listing_data( $values );

					if(isset($_POST["_booking_system_contact_form"])){
						update_post_meta($this->listing_id,"_booking_system_contact_form",$_POST["_booking_system_contact_form"]);
					}
					if(isset($sub_listing["percentage_full_price"])){
						update_post_meta($this->listing_id,"percentage_full_price",$sub_listing["percentage_full_price"]);
					}

					if(!empty($discount_user)){

						update_post_meta($this->listing_id,"_discounts_user",$discount_user);
					}else{
						update_post_meta($this->listing_id,"_discounts_user","");
					}

					update_post_meta($this->listing_id,"_slots_status","On");


					if(isset($_POST["_booking_status"])){
						update_post_meta($this->listing_id,"_booking_status",$_POST["_booking_status"]);
					}else{
						update_post_meta($this->listing_id,"_booking_status","");
					}

					delete_post_meta($this->listing_id,"_listing_sports");

					update_post_meta($this->listing_id,"_user_groups_id",$_user_groups_id);

					update_post_meta($this->listing_id,"listeo_hide_listing_from_search_result","on");

					foreach ($sub_listing["_listing_sports"] as  $listing_sports) {
						add_post_meta($this->listing_id,"_listing_sports",$listing_sports);
					}


				}
			}

			
			
			

			$this->listing_id = $org_id;

			if(isset($_POST["_user_groups_id"]) && ($_POST["_user_groups_id"] == "" || $_POST["_user_groups_id"] == "0")){


				if($this->listing_id && $this->listing_id != ""){
                     delete_post_meta($this->listing_id,"_listing_available_for");
				}

				
			}


			// Successful, show next step
			$this->step++;


		} catch ( Exception $e ) {

			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Handles the preview step form response.
	 */
	public function preview_handler() {
			
		
		if ( ! $_POST ) {
			return;
		}

		
		if ( ! is_user_logged_in() ) {
			throw new Exception( __( 'You must be signed in to post a new listing.', 'listeo_core' ) );
		}

		// Edit = show submit form again
		if ( ! empty( $_POST['edit_listing'] ) ) {
			$this->step--;
		}

		// Continue = change listing status then show next screen
		if ( ! empty( $_POST['continue'] ) ) {

			$listing = get_post( $this->listing_id );

			if ( in_array( $listing->post_status, array( 'preview', 'expired' ) ) ) {
				// Reset expiry
				delete_post_meta( $listing->ID, '_listing_expires' );

				// Update listing listing
				$update_listing                  = array();
				$update_listing['ID']            = $listing->ID;
				if( $this->form_action == "editing" ) {
					//$update_listing['post_status'] = $listing->post_status;
			
					$update_listing['post_status']   = apply_filters( 'edit_listing_post_status', get_option( 'listeo_edit_listing_requires_approval' ) ? 'pending' : $listing->post_status, $listing );

				} else {
					//$update_listing['post_status']   = 'pending';
					$update_listing['post_status']   = apply_filters( 'submit_listing_post_status', get_option( 'listeo_new_listing_requires_approval' ) ? 'pending' : 'publish', $listing );
				}
			
				$update_listing['post_date']     = current_time( 'mysql' );
				$update_listing['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_listing['post_author']   = get_current_user_id();
				
				wp_update_post( $update_listing );
			}

			$this->step++;
		}
	}

	/**
	 * Displays the final screen after a listing listing has been submitted.
	 */
	public function done() {

		
		do_action( 'listeo_core_listing_submitted', $this->listing_id );
		if( $this->form_action == "editing" ) {
			if(get_option( 'listeo_edit_listing_requires_approval' )){
				 wp_update_post(array(
			        'ID'    => $this->listing_id,
			        'post_status'   =>  'pending'
		        ));
			}
			
		}
		do_action( 'listeo_core_listing_edited', $this->listing_id );
		if( $this->listing_id ){
			  $group_admin = get_group_admin();

              if($group_admin == ""){
                 $group_admin = get_current_user_id();
              }

              	global $wpdb;
			    $table_name = $wpdb->prefix . 'posts';
			    $where = array('ID' => $this->listing_id);
			    $data = array(
			        'post_author' => $group_admin,
			    );
			    $wpdb->update($table_name, $data, $where);
				update_postt($this->listing_id);

			    //echo "<pre>"; print_r($wpdb); die;

              	/*wp_update_post(array(
			        'ID'    => $this->listing_id,
			        'post_author'   =>  $group_admin
		        ));*/

              
		}
		$template_loader = new Listeo_Core_Template_Loader;
		$template_loader->set_template_data( 
			array( 
				'listing' 	=>  get_post( $this->listing_id ),
				'id' 		=> 	$this->listing_id,
				) 
			)->get_template_part( 'listing-submitted' );

	}


	public function type( $atts = array() ) {

	$template_loader = new Listeo_Core_Template_Loader;
		if ( ! is_user_logged_in() ) {
			$template_loader->get_template_part( 'listing-sign-in' );
			$template_loader->get_template_part( 'account/login' ); 
		} else {
			
			$template_loader->set_template_data( 
				array( 
					
					'form'      	=> $this->form_name,
					'action' 		=> $this->get_action(),
					'listing_id'   => $this->get_listing_id(),
					'step'      	=> $this->get_step(),
					'submit_button_text' => __( 'Submit Listing', 'listeo_core' ),
					) 
				)->get_template_part( 'listing-submit-type' );
		}
	}


	public function type_handler() {

		// Process the package unless we're doing this before a job is submitted
		
			$this->next_step();
	
	}


	public function choose_package( $atts = array() ) {
	$template_loader = new Listeo_Core_Template_Loader;
		if ( ! is_user_logged_in() ) {
			$template_loader->get_template_part( 'listing-sign-in' );
			$template_loader->get_template_part( 'account/login' ); 
		} else {
			$packages      = self::get_packages(  );
			
			$user_packages = listeo_core_user_packages( get_current_user_id() );
			
			$template_loader->set_template_data( 
				array( 
					'packages' 		=> $packages,
					'user_packages' => $user_packages,
					'form'      	=> $this->form_name,
					'action' 		=> $this->get_action(),
					'listing_id'   => $this->get_listing_id(),
					'step'      	=> $this->get_step(),
					'submit_button_text' => __( 'Submit Listing', 'listeo_core' ),
					) 
				)->get_template_part( 'listing-submit-package' );
		}
	}

	public function choose_package_handler() {

		// Validate Selected Package
		$validation = self::validate_package( self::$package_id, self::$is_user_package );

		// Error? Go back to choose package step.
		if ( is_wp_error( $validation ) ) {
			$this->add_error( $validation->get_error_message() );
			$this->set_step( array_search( 'package', array_keys( $this->get_steps() ) ) );
			return false;
		}

		// Store selection in cookie
		wc_setcookie( 'chosen_package_id', self::$package_id );
		wc_setcookie( 'chosen_package_is_user_package', self::$is_user_package ? 1 : 0 );

		// Process the package unless we're doing this before a job is submitted
		if ( 'process-package' === $this->get_step_key() ) {
			// Product the package
			if ( self::process_package( self::$package_id, self::$is_user_package, $this->get_listing_id() ) ) {
				$this->next_step();
			}
		} else {
			$this->next_step();
		}
	}

	/**
	 * Validate package
	 *
	 * @param  int  $package_id
	 * @param  bool $is_user_package
	 * @return bool|WP_Error
	 */
	private static function validate_package( $package_id, $is_user_package ) {
		if ( empty( $package_id ) ) {
			return new WP_Error( 'error', __( 'Invalid Package', 'listeo_core' ) );
		} elseif ( $is_user_package ) {
			if ( ! listeo_core_package_is_valid( get_current_user_id(), $package_id ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'listeo_core' ) );
			}
		} else {
			$package = wc_get_product( $package_id );

			if ( ! $package->is_type( 'listing_package' ) && ! $package->is_type( 'listing_package_subscription' ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'listeo_core' ) );
			}

		}
		return true;
	}


	/**
	 * Purchase a job package
	 *
	 * @param  int|string $package_id
	 * @param  bool       $is_user_package
	 * @param  int        $listing_id
	 * @return bool Did it work or not?
	 */
	private static function process_package( $package_id, $is_user_package, $listing_id ) {
		// Make sure the job has the correct status
		
		if ( 'preview' === get_post_status( $listing_id ) ) {
			// Update job listing
			$update_job                  = array();
			$update_job['ID']            = $listing_id;
			$update_job['post_status']   = 'pending_payment';
			$update_job['post_date']     = current_time( 'mysql' );
			$update_job['post_date_gmt'] = current_time( 'mysql', 1 );
			$update_job['post_author']   = get_current_user_id();
		
			wp_update_post( $update_job );
		}

		if ( $is_user_package ) {
			$user_package = listeo_core_get_user_package( $package_id );
			$package      = wc_get_product( $user_package->get_product_id() );

			// Give listing the package attributes
			update_post_meta( $listing_id, '_duration', $user_package->get_duration() );
			update_post_meta( $listing_id, '_featured', $user_package->is_featured() ?  'on' : 0  );
			update_post_meta( $listing_id, '_package_id', $user_package->get_product_id() );
			update_post_meta( $listing_id, '_user_package_id', $package_id );
			

			// Approve the listing
			if ( in_array( get_post_status( $listing_id ), array( 'pending_payment', 'expired' ) ) ) {
				listeo_core_approve_listing_with_package( $listing_id, get_current_user_id(), $package_id );
			}
			if(isset($_GET["action"]) && $_GET["action"] == 'renew' ){
				$post_types_expiry = new Listeo_Core_Post_Types;
				$post_types_expiry->set_expiry(get_post($listing_id));
			}

			return true;
		} elseif ( $package_id ) {
			$package = wc_get_product( $package_id );

			
			$is_featured = $package->is_listing_featured();
			

			// Give job the package attributes
			update_post_meta( $listing_id, '_duration', $package->get_duration() );
			update_post_meta( $listing_id, '_featured', $is_featured ? 'on' : 0 );
			update_post_meta( $listing_id, '_package_id', $package_id );
			delete_post_meta( $listing_id, '_user_package_id' );
			if(isset($_GET["action"]) && $_GET["action"] == 'renew' ){
				update_post_meta( $listing_id, '_package_change', $package_id );
			}
			// Clear cookie
			wc_setcookie( 'chosen_package_id', '', time() - HOUR_IN_SECONDS );
			wc_setcookie( 'chosen_package_is_user_package', '', time() - HOUR_IN_SECONDS );


			// Add package to the cart
			WC()->cart->add_to_cart( $package_id, 1, '', '', array(
				'listing_id' => $listing_id,
			) );

			wc_add_to_cart_message( $package_id );


			// Redirect to checkout page
			wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
			exit;
		}// End if().
	}


	/**
	 * Adds an error.
	 *
	 * @param string $error The error message.
	 */
	public function add_error( $error ) {
		$this->errors[] = $error;
	}

	/**
	 * Gets post data for fields.
	 *
	 * @return array of data
	 */
	protected function get_posted_fields() {
		$this->init_fields();

		$values = array();

		foreach ( $this->fields as $group_key => $group_fields ) {
		
			foreach ( $group_fields['fields'] as $key => $field ) {
				// Get the value
				$field_type = str_replace( '-', '_', $field['type'] );
				
				if ( $handler = apply_filters( "listeo_core_get_posted_{$field_type}_field", false ) ) {
					
					$values[ $group_key ][ $key ] = call_user_func( $handler, $key, $field );
				} elseif ( method_exists( $this, "get_posted_{$field_type}_field" ) ) {
					
					$values[ $group_key ][ $key ] = call_user_func( array( $this, "get_posted_{$field_type}_field" ), $key, $field );
				} else {
					
					$values[ $group_key ][ $key ] = $this->get_posted_field( $key, $field );
				}
				
				// Set fields value

				$this->fields[ $group_key ]['fields'][ $key ]['value'] = $values[ $group_key ][ $key ];
			}
		}


		return $values;
	}


	/**
	 * Gets the value of a posted field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string|array
	 */
	protected function get_posted_field( $key, $field ) {
		
		return isset( $_POST[ $key ] ) ? $this->sanitize_posted_field( $_POST[ $key ] ) : '';
	}

	/**
	 * Navigates through an array and sanitizes the field.
	 *
	 * @param array|string $value The array or string to be sanitized.
	 * @return array|string $value The sanitized array (or string from the callback).
	 */
	protected function sanitize_posted_field( $value ) {
		// Santize value
		$value = is_array( $value ) ? array_map( array( $this, 'sanitize_posted_field' ), $value ) : sanitize_text_field( stripslashes( trim( $value ) ) );

		return $value;
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_textarea_field( $key, $field ) {
		return isset( $_POST[ $key ] ) ? wp_kses_post( trim( stripslashes( $_POST[ $key ] ) ) ) : '';
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_wp_editor_field( $key, $field ) {
		return $this->get_posted_textarea_field( $key, $field );
	}



	protected function get_posted_file_field( $key, $field ) {
		
		$file = $this->upload_file( $key, $field );
		
		

		if ( ! $file ) {
			$file = $this->get_posted_field( 'current_' . $key, $field );
		} elseif ( is_array( $file ) ) {
			$file = array_filter( array_merge( $file, (array) $this->get_posted_field( 'current_' . $key, $field ) ) );
		}

		return $file;
	}

	/**
	 * Updates or creates a listing listing from posted data.
	 *
	 * @param  string $post_title
	 * @param  string $post_content
	 * @param  string $status
	 * @param  array  $values
	 * @param  bool   $update_slug
	 */
	protected function save_listing( $post_title, $post_content, $status = 'preview', $values = array(), $update_slug = true ) {

		global $wpdb;

		$_user_groups_id = "";

		if(isset($values['basic_info']['_user_groups_id'])){
			$_user_groups_id = $values['basic_info']['_user_groups_id'];
			$listing_data = array(
				'post_title'     => $post_title,
				'post_content'   => $post_content,
				'post_type'      => 'listing',
				'comment_status' => 'open',
				'users_groups_id' => $_user_groups_id,
			);
		}else{
			$listing_data = array(
				'post_title'     => $post_title,
				'post_content'   => $post_content,
				'post_type'      => 'listing',
				'comment_status' => 'open',
			);

			
		}
		
        
		if ( $update_slug ) {
			$listing_slug   = array();

			$listing_slug[]            = $post_title;
			$listing_data['post_name'] = sanitize_title( implode( '-', $listing_slug ) );
		}

		//if ( $status && $this->form_action != "editing") {
		if ( $status) {
			$listing_data['post_status'] = $status;
		}

		


		$listing_data = apply_filters( 'submit_listing_form_save_listing_data', $listing_data, $post_title, $post_content, $status, $values );


		if ( $this->listing_id ) {
			$listing_data['ID'] = $this->listing_id;
			wp_update_post( $listing_data );
		} else {
			$this->listing_id = wp_insert_post( $listing_data );

			if ( ! headers_sent() ) {
				$submitting_key = uniqid();

				setcookie( 'listeo_core-submitting-listing-id', $this->listing_id, false, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'listeo_core-submitting-listing-key', $submitting_key, false, COOKIEPATH, COOKIE_DOMAIN, false );

				update_post_meta( $this->listing_id, '_submitting_key', $submitting_key );
			}
		}
		$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true ); 

		if($active_group_id != "" && $active_group_id != 0){

			if($_user_groups_id != ""){
				$active_group_id = $_user_groups_id;
			}

			$result = $wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->posts} 
					 SET users_groups_id = %s 
					 WHERE ID = %d",
					$active_group_id,
					$this->listing_id
				)
			);

		}
	}
/**
	 * Sets listing meta and terms based on posted values.
	 *
	 * @param  array $values
	 */
	protected function update_listing_data( $values ) {
		// Set defaults

		$maybe_attach = array();
// Check if not availability dates are sended and then set them as booking reservations
		if (! empty( $values['availability_calendar']['_availability'] ) ) {

			$bookings = new Listeo_Core_Bookings_Calendar;
			
			// set array only with dates when listing is not avalible
			$dates = array_filter( explode( "|", $values['availability_calendar']['_availability']['dates'] ) );

			if ( ! empty( $dates ) ) $bookings :: update_reservations( $this->listing_id, $dates );

			// set array only with dates when we have special prices for booking
			$special_prices = json_decode( $values['availability_calendar']['_availability']['price'], true );
			
			if ( ! empty( $special_prices ) ) $bookings :: update_special_prices( $this->listing_id, $special_prices );

		}
		// Loop fields and save meta and term data
		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields['fields'] as $key => $field ) {

				// Save opening hours to array in post meta
				if ( $key == '_opening_hours') {
					$open_hours = $this->posted_hours_to_array( $key, $field);

					if ( $open_hours ) update_post_meta( $this->listing_id,  '_opening_hours', json_encode( $open_hours ) );
					else update_post_meta( $this->listing_id,  '_opening_hours', json_encode( false ) );
					continue;
				}

				// Save taxonomies
				if ( ! empty( $field['taxonomy'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$new_tax_array = array_map('intval', $values[ $group_key ][ $key ] );
						/*TODO - fix the damn region string*/
						wp_set_object_terms( $this->listing_id, $new_tax_array, $field['taxonomy'], false );
					} else {
						wp_set_object_terms( $this->listing_id, array( intval($values[ $group_key ][ $key ]) ), $field['taxonomy'], false );
					}

				//  logo is a featured image
				} elseif ( 'thumbnail' === $key ) {
					$attachment_id = is_numeric( $values[ $group_key ][ $key ] );
					if ( empty( $attachment_id ) ) {
						delete_post_thumbnail( $this->listing_id );
					} else {
						set_post_thumbnail( $this->listing_id, $attachment_id );
					}
					
				} else {
					
					if( isset($field['multi']) && $field['multi'] == true || $field['type'] == 'checkboxes') {
						
						delete_post_meta($this->listing_id, $key); 
						
						if ( is_array( $values[ $group_key ][ $key ] ) ) {
							foreach( $values[ $group_key ][ $key ] as $value ) {
								add_post_meta( $this->listing_id, $key, $value );
							}
						} else {
							if(!empty($values[ $group_key ][ $key ])){
								add_post_meta( $this->listing_id, $key, $values[ $group_key ][ $key ] );	
							}
							
						}
					} else {
						
						update_post_meta( $this->listing_id, $key, $values[ $group_key ][ $key ] );
					}

					//update_post_meta( $this->listing_id, $key, $values[ $group_key ][ $key ] );	
					
				
					// Handle attachments
					if ( 'file' === $field['type'] ) {
						
						$attachment_id = is_numeric( $values[ $group_key ][ $key ] ) ? absint( $values[ $group_key ][ $key ] ) : $this->create_attachment( $values[ $group_key ][ $key ] );
				
						update_post_meta( $this->listing_id, $key.'_id', $attachment_id  );
						
						// if ( is_array( $values[ $group_key ][ $key ] ) ) {
						// 	foreach ( $values[ $group_key ][ $key ] as $file_url ) {
						// 		$maybe_attach[] = $file_url;
						// 	}
						// } else {
						// 	$maybe_attach[] = $values[ $group_key ][ $key ];
						// }
					}
				}
			}
		}
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			$medtitle = $_POST['med_title'];
			$med_name = $_POST['med_name'];
				$ed_link = $_POST['ed_link'];
				$ed_oldimge = $_POST['ed_oldimge'];
				$files = $_FILES["ed_image"];
			$trdata = array();
			for($i=0;$i<count($medtitle);$i++){
				
				$trdata[$i]['title'] = $medtitle[$i];
				for($ij=0;$ij<count($med_name[$i]);$ij++){
					 $attachment_id = ''; 
					  if ($files['name'][$i][$ij]) {
						   $file = array(
							'name' => $files['name'][$i][$ij],
							'type' => $files['type'][$i][$ij],
							'tmp_name' => $files['tmp_name'][$i][$ij],
							'error' => $files['error'][$i][$ij],
							'size' => $files['size'][$i][$ij]
						);

						$_FILES = array ("upload_file" => $file); 
						$attachment_id = media_handle_upload("upload_file", 0);
					}else{
						$attachment_id = $ed_oldimge[$i][$ij];
					}  
					$trdata[$i]['artist'][] = array(
						'title' => $med_name[$i][$ij],
						'link' => $ed_link[$i][$ij],
						'image' => $attachment_id,
					);
				}
				
			}
			$sstitle = array();
			$stitle = $_POST['title'];
			$scopyright = $_POST['copyright'];
			for($i=0;$i<count($stitle);$i++){
				$sstitle[] = array(
					'title' => $stitle[$i],
					'copyright' => $scopyright[$i],
				);
			}
		update_post_meta( $this->listing_id, 'gallery_text', serialize($sstitle) );
		update_post_meta( $this->listing_id, '_listing_core', serialize($trdata) );
		// save listing type
		update_post_meta( $this->listing_id, '_listing_type', $this->listing_type );

		// $maybe_attach = array_filter( $maybe_attach );

		// // Handle attachments
		// if ( sizeof( $maybe_attach ) && apply_filters( 'listeo_core_attach_uploaded_files', true ) ) {
		// 	// Get attachments
		// 	$attachments     = get_posts( 'post_parent=' . $this->listing_id . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1' );
		// 	$attachment_urls = array();

		// 	// Loop attachments already attached to the listing
		// 	foreach ( $attachments as $attachment_id ) {
		// 		$attachment_urls[] = wp_get_attachment_url( $attachment_id );
		// 	}

		// 	foreach ( $maybe_attach as $attachment_url ) {
		// 		if ( ! in_array( $attachment_url, $attachment_urls ) ) {
		// 			$this->create_attachment( $attachment_url );
		// 		}
		// 	}
		// }

		// And user meta to save time in future
		

		do_action( 'listeo_core_update_listing_data', $this->listing_id, $values );
	}
	/**
	 * Displays preview of listing Listing.
	 */
	public function preview() {
		global $post, $listing_preview;
		
		if ( $this->listing_id ) {
			$listing_preview       = true;
			$post              = get_post( $this->listing_id );
			$post->post_status = 'preview';

			setup_postdata( $post );
			
			$template_loader = new Listeo_Core_Template_Loader;
			$template_loader->set_template_data( 
			array( 
				'action' 		=> $this->get_action(),
				'fields' 		=> $this->fields,
				'form'      	=> $this->form_name,
				'post'      	=> $post,
				'listing_id'   => $this->get_listing_id(),
				'step'      	=> $this->get_step(),
				'submit_button_text' => apply_filters( 'submit_listing_form_preview_button_text', __( 'Submit', 'listeo_core' ) )
				) 
			)->get_template_part( 'listing-preview' );

			wp_reset_postdata();
		}
	}


	protected function get_posted_hours_field( $key, $field ) {
		
		$values = array();
		if($key == '_opening_hours'){
			$days = listeo_get_days();
			foreach ($days as $d_key => $value) {
				if ( isset( $_POST[ 'opening_hours_'.$d_key ] ) ) {
					$values['_opening_hours_'.$d_key] =  $_POST[ 'opening_hours_'.$d_key ];
				}
			}
		}
		
		return $values;
	}

	
	protected function posted_hours_to_array( $key, $field ) {
		
		$values = array();
		if($key == '_opening_hours'){

			$days = listeo_get_days();
			$int = 0;
			$is_empty = true;

			foreach ($days as $d_key => $value) {
				if(isset($_POST[ '_' . $d_key . '_opening_hour' ])){
					$values[$int]['opening'] =  $_POST[ '_' . $d_key . '_opening_hour' ];
					$values[$int]['closing'] =  $_POST[ '_' . $d_key . '_closing_hour' ];
					$int++;

					// check if there are opened days
					if ( $_POST[ '_' . $d_key . '_opening_hour' ] != 'Closed' &&
					$_POST[ '_' . $d_key . '_closing_hour' ] != 'Closed' ) $is_empty = false;
				}
				
			}
		}
		
		// return false if all days is closed
		if ($is_empty) return false;

		return $values;

	}

	protected function get_posted_term_checkboxes_field( $key, $field ) {

		if ( isset( $_POST[ 'tax_input' ] ) && isset( $_POST[ 'tax_input' ][ $field['taxonomy'] ] ) ) {
			return array_map( 'absint', $_POST[ 'tax_input' ][ $field['taxonomy'] ] );
		} else {
			return array();
		}
	}


	function enable_paid_listings($steps){
 
		if(get_option('listeo_new_listing_requires_purchase' ) && !isset($_GET["action"]) || isset($_GET["action"]) && $_GET["action"] == 'renew' ){

		/*
		if(get_option('listeo_core_listing_submit_option', 'listeo_core_new_listing_requires_purchase' ) && !isset($_GET["action"])){*/
			$steps['package'] = array(
					'name'     => __( 'Choose a package', 'listeo_core' ),
					'view'     => array( $this, 'choose_package' ),
					'handler'  => array(  $this, 'choose_package_handler' ),
					'priority' => 5,
				);
			$steps['process-package'] = array(
					'name'     => '',
					'view'     => false,
					'handler'  => array( $this, 'choose_package_handler' ),
					'priority' => 25,
			);
		}
		return $steps;
	}

	/**
	 * Gets step key from outside of the class.
	 *
	 * @since 1.24.0
	 * @param string|int $step
	 * @return string
	 */
	public function get_step_key( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}
		$keys = array_keys( $this->steps );
		return isset( $keys[ $step ] ) ? $keys[ $step ] : '';
	}


	/**
	 * Gets steps from outside of the class.
	 *
	 * @since 1.24.0
	 */
	public function get_steps() {
		return $this->steps;
	}

	/**
	 * Gets step from outside of the class.
	 */
	public function get_step() {
		return $this->step;
	}


	/**
	 * Decreases step from outside of the class.
	 */
	public function previous_step() {
		$this->step --;
	}

	/**
	 * Sets step from outside of the class.
	 *
	 * @since 1.24.0
	 * @param int $step
	 */
	public function set_step( $step ) {
		$this->step = absint( $step );
	}

	/**
	 * Increases step from outside of the class.
	 */
	public function next_step() {
		$this->step ++;
	}

	/**
	 * Displays errors.
	 */
	public function show_errors() {
		foreach ( $this->errors as $error ) {
			echo '<div class="notification closeable error listing-manager-error">' . wpautop( $error, true ) . '<a class="close"></a></div>';
		}
	}


	/**
	 * Gets the action (URL for forms to post to).
	 * As of 1.22.2 this defaults to the current page permalink.
	 *
	 * @return string
	 */
	public function get_action() {
		return esc_url_raw( $this->action ? $this->action : wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	/**
	 * Gets the submitted listing ID.
	 *
	 * @return int
	 */
	public function get_listing_id() {
		return absint( $this->listing_id );
	}

	/**
	 * Sorts array by priority value.
	 *
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected function sort_by_priority( $a, $b ) {
	    if ( $a['priority'] == $b['priority'] ) {
	        return 0;
	    }
	    return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}

	/**
	 * Calls the view handler if set, otherwise call the next handler.
	 *
	 * @param array $atts Attributes to use in the view handler.
	 */
	public function output( $atts = array() ) {
		$step_key = $this->get_step_key( $this->step );
		$this->show_errors();

		if ( $step_key && is_callable( $this->steps[ $step_key ]['view'] ) ) {
			call_user_func( $this->steps[ $step_key ]['view'], $atts );
		}
	}

	/**
	 * Returns the form content.
	 *
	 * @param string $form_name
	 * @param array  $atts Optional passed attributes
	 * @return string|null
	 */
	public function get_form( $atts = array() ) {
		
			ob_start();
			$this->output( $atts );
			return ob_get_clean();
		
	}
	
	/**
	 * This filter insures users only see their own media
	 */
	function filter_media( $query ) {
		// admins get to see everything
		if ( ! current_user_can( 'manage_options' ) )
			$query['author'] = get_current_user_id();
		return $query;
	}

	function change_page_title( $title, $id = null ) {

	    if ( is_page( get_option( 'submit_listing_page' ) ) && in_the_loop()) {
	       if($this->form_action == "editing") {
	       	$title = esc_html__('Edit Listing', 'listeo_core');
	       };
	    }

	    return $title;
	}


	/**
	 * Creates a file attachment.
	 *
	 * @param  string $attachment_url
	 * @return int attachment id
	 */
	protected function create_attachment( $attachment_url ) {
		include_once( ABSPATH . 'wp-admin/includes/image.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );

		$upload_dir     = wp_upload_dir();
		$attachment_url = str_replace( array( $upload_dir['baseurl'], WP_CONTENT_URL, site_url( '/' ) ), array( $upload_dir['basedir'], WP_CONTENT_DIR, ABSPATH ), $attachment_url );

		if ( empty( $attachment_url ) || ! is_string( $attachment_url ) ) {
			return 0;
		}

		$attachment     = array(
			'post_title'   => get_the_title( $this->listing_id ),
			'post_content' => '',
			'post_status'  => 'inherit',
			'post_parent'  => $this->listing_id,
			'guid'         => $attachment_url
		);

		if ( $info = wp_check_filetype( $attachment_url ) ) {
			$attachment['post_mime_type'] = $info['type'];
		}

		$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->listing_id );

		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
			return $attachment_id;
		}

		return 0;
	}

	/**
	 * Return packages
	 *
	 * @param array $post__in
	 * @return array
	 */
	public static function get_packages( $post__in = array() ) {
		return get_posts( array(
			'post_type'        => 'product',
			'posts_per_page'   => -1,
			'post__in'         => $post__in,
			'order'            => 'asc',
			'orderby'          => 'date',
			'suppress_filters' => false,
			'tax_query'        => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'listing_package','listing_package_subscription'),
					'operator' => 'IN',
				),
			),
			//'meta_query'       => WC()->query->get_meta_query(),
		)  );
	}

	/**
	 * Change initial job status
	 *
	 * @param string  $status
	 * @param WP_Post $job
	 * @return string
	 */
	public static function submit_listing_post_status( $status, $listing ) {
		if(get_option('listeo_new_listing_requires_purchase' )){
			switch ( $listing->post_status ) {
				case 'preview' :
					return 'pending_payment';
				break;
				case 'expired' :
					return 'expired';
				break;
				default :
					return $status;
				break;
			}
		} else {
			return $status;
		}

	}

	/**
	 * Save or update current listing as WooCommerce product
    *
	* @return int $product_id number with product id associated with listing
	*
	 */
	private function save_as_product($post_title,$post_content,$product_id, $author){



	//	$values = $this->get_posted_fields();
	
	//	$product_id = $values['basic_info']['product_id'];

		// basic listing informations will be added to listing

		if($post_content == ""){
			$post_content = ".";
		}
		$product = array (
			'post_author' => $author,
			'post_content' => $post_content,
			'post_status' => 'publish',
			'post_title' => $post_title,
			'post_parent' => '',
			'post_type' => 'product',
		);




		// add product if not exist
		if ( ! $product_id ||  get_post_type( $product_id ) != 'product') {
			
			// insert listing as WooCommerce product
			$product_id = wp_insert_post( $product );
			wp_set_object_terms( $product_id, 'listing_booking', 'product_type' );

		} else {

			// update existing product
			$product['ID'] = $product_id;
			wp_update_post ( $product );

		}



		if($product_id == "" || $product_id == "0"){
			$this->add_error( "Product generated faild!" );
			die;
			return;
		}
		
		// set product category
		$term = get_term_by( 'name', apply_filters( 'listeo_default_product_category', 'Listeo booking'), 'product_cat', ARRAY_A );

        try{
		    if ( ! $term ) { 
		    	$term = wp_insert_term(
						apply_filters( 'listeo_default_product_category', 'Listeo booking'),
						'product_cat',
						array(
						  'description'=> __( 'Listings category', 'listeo-core' ),
						  'slug' => str_replace( ' ', '-', apply_filters( 'listeo_default_product_category', 'Listeo booking') )
						)
					  ); 
		    	if(!isset($term->errors)){
		    		wp_set_object_terms( $product_id, $term['term_id'], 'product_cat');
		    	}
		    	
		    }
		} catch (\Exception $ex) {
		    //echo $ex->getMessage();
		}

		

		return $product_id;
	}	

	
	/**
	 * Handles the uploading of files.
	 *
	 * @param string $field_key
	 * @param array  $field
	 * @throws Exception When file upload failed
	 * @return  string|array
	 */
	protected function upload_file( $field_key, $field ) {
		if ( isset( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ]['name'] ) ) {
			if ( ! empty( $field['allowed_mime_types'] ) ) {
				$allowed_mime_types = $field['allowed_mime_types'];
			} else {
				$allowed_mime_types = listeo_get_allowed_mime_types();
			}

			$file_urls       = array();
			$files_to_upload = listeo_prepare_uploaded_files( $_FILES[ $field_key ] );

			foreach ( $files_to_upload as $file_to_upload ) {
				$uploaded_file = listeo_upload_file( $file_to_upload, array(
					'file_key'           => $field_key,
					'allowed_mime_types' => $allowed_mime_types,
					) );

				if ( is_wp_error( $uploaded_file ) ) {
					throw new Exception( $uploaded_file->get_error_message() );
				} else {
					$file_urls[] = $uploaded_file->url;
				}
			}

			if ( ! empty( $field['multiple'] ) ) {
				return $file_urls;
			} else {
				return current( $file_urls );
			}
		}
	}

}
