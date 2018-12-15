<?php
/**
 * CPTUIEXT_Network_Admin_UI Class File
 *
 * @package CPTUIExtended
 * @subpackage Admin
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Main initiation class
 *
 * @since  1.0.0
 */
class CPTUIEXT_Network_Admin_UI {
	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 */
	private $key = 'cptui_network_extended';

	/**
	 * Options page metabox id
	 *
	 * @var string
	 */
	private $metabox_id = 'cptuiext_network_option_metabox';

	/**
	 * Options Page title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since NEXT
	 */
	protected $plugin = null;

	/**
	 * Holds an instance of the object
	 *
	 * @var CPTUIEXT_Network_Admin_UI
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param CPTUI_Extended $plugin this class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
		$this->title = __( 'CPTUI Extended', 'cptuiext' );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		if ( function_exists( 'cptui_plugin_menu' ) ) {
			add_action( 'network_admin_menu', 'cptui_plugin_menu' );
		}
		add_action( 'network_admin_menu', array( $this, 'rename_menu_items' ) );
		add_action( 'network_admin_menu', array( $this, 'remove_menu_items' ), 999 );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_about_link' ), 15 );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since  1.0.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * [add_options_page description]
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		$this->options_page = add_menu_page( __( 'CPTUI Extended', 'cptuiext' ), __( 'CPTUI Extended', 'cptuiext' ), 'manage_options', $this->key, array( $this, 'admin_page_display' ), cptui_menu_icon() );

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Rename_menu_items
	 *
	 * @return void
	 */
	public function rename_menu_items() {
		global $menu;
		foreach ( $menu as $key => $value ) {
			if ( 'CPT UI' === $menu[ $key ][0] ) {
				$menu[ $key ][0] = $this->title;
				break;
			}
		}
	}

	/**
	 * Remove_menu_items
	 *
	 * @return void
	 */
	public function remove_menu_items() {
		remove_submenu_page( 'cptui_main_menu', 'cptui_support' );
		remove_submenu_page( 'cptui_main_menu', 'cptui_main_menu' );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since  1.0.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add a link to CPTUI Extended About page to the admin bar.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Admin_Bar $wp_admin_bar As passed to 'admin_bar_menu'.
	 */
	public function admin_bar_about_link( $wp_admin_bar ) {

		if ( is_user_logged_in() ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'wp-logo',
				'id'     => 'cptui_about',
				'title'  => esc_html__( 'About CPTUI Extended', 'cptuiext' ),
				'href'   => add_query_arg( array( 'page' => 'cptui_about' ), cptui_get_admin_url( 'index.php' ) ),
			) );
		}
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 *
	 * @since  1.0.0
	 */
	function add_options_page_metabox() {

		$prefix = 'cptuiext_';

		// Hook in our save notices.
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
			// These are important, don't remove.
			'key'   => 'options-page',
			'value' => array( $this->key ),
			),
		) );

		// Set our CMB2 fields.
		$cmb->add_field( array(
			'name' => __( 'Master Site ID', 'cptuiext' ),
			'desc' => __( 'Enter blog site ID that controls Network Wide CPT creation.', 'cptuiext' ),
			'id'   => $prefix . 'master_site_id',
			'type'             => 'text_small',
			'default' => '1',
			'sanitization_cb' => array( $this, 'validate_integer' ),
		) );

	}

	/**
	 * [validate_integer description]
	 *
	 * @param  string $field Raw cmb2 field data.
	 * @return string validated string.
	 */
	public function validate_integer( $field ) {

		$field = trim( str_replace( ' ', '', $field ) );

		if ( ! is_numeric( $field ) ) {
			$field = '';
		}
		return $field;

	}

	/**
	 * Register settings notices for display
	 *
	 * @since  1.0.0
	 * @param  int   $object_id Option key.
	 * @param  array $updated Array of updated fields.
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'cptuiext' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If no valid field property provided.
	 *
	 * @param string $field Field to retrieve.
	 * @return mixed Field value or exception is thrown.
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}
}

/**
 * Wrapper function around cmb2_get_option
 *
 * @since 1.0.0
 *
 * @param string $key Options array key.
 * @return mixed $value Option value
 */
function cptuiext_get_network_option( $key = '' ) {
	return cmb2_get_option( 'cptui_network_extended', $key );
}
