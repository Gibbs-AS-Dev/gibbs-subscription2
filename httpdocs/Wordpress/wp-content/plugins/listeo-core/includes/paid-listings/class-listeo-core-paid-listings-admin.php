<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin
 */
class Listeo_Core_Paid_Listings_Admin {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
		add_filter( 'job_manager_admin_screen_ids', array( $this, 'add_screen_ids' ) );
		
		add_filter( 'parse_query', array( $this, 'parse_query' ) );
	}

	/**
	 * Screen IDS
	 *
	 * @param  array $ids
	 * @return array
	 */
	public function add_screen_ids( $ids ) {
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
		return array_merge( $ids, array(
			'users_page_listeo_core_paid_listings_packages'
		) );
	}

	

	/**
	 * Add menu items
	 */
	public function admin_menu() {
		add_submenu_page( 'listeo-fields-and-form', __( 'Packages Manager', 'listeo_core' ), __( 'Packages Manager', 'listeo_core' ), 'manage_options', 'listeo_core_paid_listings_packages' , array( $this, 'packages_page' ) );
	}

	/**
	 * Manage Packages
	 */
	public function packages_page() {
		global $wpdb;

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';

		if ( 'delete' === $action && ! empty( $_GET['delete_nonce'] ) && wp_verify_nonce( $_GET['delete_nonce'], 'delete' ) ) {
			$package_id = absint( $_REQUEST['package_id'] );
			$wpdb->delete( "{$wpdb->prefix}listeo_core_user_packages", array(
				'id' => $package_id,
			) );
			$wpdb->delete( $wpdb->postmeta, array(
				'meta_key' => '_user_package_id',
				'meta_value' => $package_id,
			) );
			echo sprintf( '<div class="updated"><p>%s</p></div>', __( 'Package successfully deleted', 'listeo_core' ) );
		}

		if ( 'add' === $action || 'edit' === $action ) {
			$this->add_package_page();
		} else {
			include_once( dirname( __FILE__ ) . '/class-listeo-core-paid-listings-admin-packages.php' );
			$table = new Listeo_Core_Admin_Packages();
			$table->prepare_items();
			?>
			<div class="woocommerce wrap">
				<h2><?php _e( 'Listing Packages', 'listeo_core' ); ?> <a href="<?php echo esc_url( add_query_arg( 'action', 'add', admin_url( 'admin.php?page=listeo_core_paid_listings_packages' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add User Package', 'listeo_core' ); ?></a></h2>
				<form id="package-management" method="get">
					<input type="hidden" name="page" value="listeo_core_paid_listings_packages" />
					<?php $table->display() ?>
					<?php wp_nonce_field( 'save', 'listeo_core_paid_listings_packages_nonce' ); ?>
				</form>
			</div>
			<?php
		}
	}

	/**
	 * Add package
	 */
	public function add_package_page() {
		include_once( dirname( __FILE__ ) . '/class-listeo-core-paid-listings-admin-add-package.php' );
		$add_package = new Listeo_Core_Admin_Add_Package();
		?>
		<div class="woocommerce wrap">
			<h2><?php _e( 'Add User Package', 'listeo_core' ); ?></h2>
			<form id="package-add-form" method="post">
				<input type="hidden" name="page" value="listeo_core_paid_listings_packages" />
				<?php $add_package->form() ?>
				<?php wp_nonce_field( 'save', 'listeo_core_paid_listings_packages_nonce' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Filters and sorting handler
	 *
	 * @param  WP_Query $query
	 * @return WP_Query
	 */
	public function parse_query( $query ) {
		global $typenow;

		if ( 'listing' === $typenow  ) {
			if ( isset( $_GET['package'] ) ) {
				$query->query_vars['meta_key']   = '_user_package_id';
				$query->query_vars['meta_value'] = absint( $_GET['package'] );
			}
		}

		return $query;
	}
}
Listeo_Core_Paid_Listings_Admin::get_instance();
