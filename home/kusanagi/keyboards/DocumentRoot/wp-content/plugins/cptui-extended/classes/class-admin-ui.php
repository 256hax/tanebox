<?php
/**
 * CPTUIEXT_Admin_UI Class File
 *
 * @package CPTUIExtended
 * @subpackage Admin
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Main initiation class
 *
 * @internal
 *
 * @since  1.0.0
 */
class CPTUIEXT_Admin_UI {
	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 */
	private $key = 'cptui_extended';

	/**
	 * Options page metabox id
	 *
	 * @var string
	 */
	private $metabox_id = 'cptuiext_option_metabox';

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
	 * @var CPTUIEXT_Admin_UI
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
		//add_action( 'cptui_extra_menu_items', array( $this, 'add_options_page' ), 10, 2 );
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
	 * @param string $parent_slug parent plugin menu.
	 * @param string $capability access.
	 */
	public function add_options_page( $parent_slug, $capability ) {

		$this->options_page = add_submenu_page( $parent_slug, __( 'CPTUI Extended', 'cptuiext' ), __( 'CPTUI Extended', 'cptuiext' ), $capability, $this->key, array( $this, 'admin_page_display' ) );

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
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

		$cmb->add_field( array(
		    'name' => 'Copy CPT(s) to Subsite',
		    'desc' => __( 'Add a site to copy CPT', 'cptuiext' ),
		    'type' => 'title',
		    'id'   => $prefix . 'copy_title',
		) );

		$group_field_id = $cmb->add_field( array(
		    'id'          => $prefix . 'copy_cpt_group',
		    'type'        => 'group',
		    'description' => '',
		    // 'repeatable'  => false, // Use false if you want non-repeatable group.
		    'options'     => array(
		        'group_title'   => __( 'Copy {#}', 'cptuiext' ), // Since version 1.1.4, {#} gets replaced by row number.
		        'add_button'    => __( 'Add Another Site', 'cptuiext' ),
		        'remove_button' => __( 'Remove Site', 'cptuiext' ),
		        'sortable'      => false, // Beta.
		        'closed'     => true, // True to have the groups closed by default.
		    ),
		) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$cmb->add_group_field( $group_field_id, array(
			'name' => 'Site ID',
			'desc' => __( 'Enter site ID to copy post types to. Enter 0 for all sites.', 'cptuiext' ),
			'id'   => $prefix . 'site_id',
			'type' => 'text',
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types).
		) );

		$cmb->add_group_field( $group_field_id, array(
			    'name'    => 'Post Type',
			    'desc'    => __( 'Check post types to copy.', 'cptuiext' ),
			    'id'      => $prefix . 'copy_posttype_multicheckbox',
			    'type'    => 'multicheck_inline',
			    'options' => cptui_get_post_types(),
		) );

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
	 * @throws Exception If no valid property provided.
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
 * @return mixed $value Option value.
 */
function cptuiext_get_option( $key = '' ) {
	return cmb2_get_option( 'cptui_extended', $key );
}
