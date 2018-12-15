<?php
/**
 * CPTUI Extended Loader
 *
 * @package CPTUIExtended
 * @subpackage Loader
 * @author Pluginize
 * @since 1.0.0
 * @license GPL-2.0+
 */

/**
 * Plugin Name: Custom Post Type UI Extended
 * Plugin URI: http://pluginize.com
 * Description: Extra Features for Custom Post Type UI
 * Version: 1.5.2
 * Author: Pluginize
 * Author URI: http://pluginize.com
 * License: GPL-2.0+
 * Text Domain: cptuiext
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2016 WebDevStudios (email : contact@pluginize.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Autoloads files with classes when needed.
 *
 * @internal
 *
 * @since 1.0.0
 *
 * @param string $class_name Name of the class being requested.
 * @return void
 */
function cptui_extended_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'CPTUIEXT_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'CPTUIEXT_' ) )
	) );

	CPTUI_Extended::include_file( $filename );
}
spl_autoload_register( 'cptui_extended_autoload_classes' );

/**
 * Main initiation class.
 *
 * @internal
 *
 * @since 1.0.0
 */
class CPTUI_Extended {

	/**
	 * Current version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const VERSION = '1.5.2';

	/**
	 * URL of plugin directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $path = '';

	/**
	 * Plugin name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $plugin_name = '';

	/**
	 * Plugin basename.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $basename = '';

	/**
	 * Plugin's store URL.
	 *
	 * @since 1.3.3
	 * @var string
	 */
	public $store_url = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @since 1.0.0
	 * @var CPTUI_Extended
	 */
	protected static $single_instance = null;

	/**
	 * Database version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $db_version_raw = '';

	/**
	 * Object holding CPTUIEXT_Shortcode instance.
	 *
	 * @since 1.0.0
	 * @var null
	 */
	protected $shortcode = null;

	/**
	 * Object holding CPTUIEXT_Shortcode_Admin instance.
	 *
	 * @since 1.0.0
	 * @var null
	 */
	protected $shortcode_admin = null;

	/**
	 * Object holding CPTUIEXT_Shortcode_Widget instance.
	 *
	 * @since 1.0.0
	 * @var null
	 */
	protected $shortcode_widget = null;

	/**
	 * Object holding CPTUIEXT_Admin_UI instance.
	 *
	 * @since 1.0.0
	 * @var null
	 */
	protected $admin_ui = null;

	/**
	 * Object holding CPTUIEXT_Network_Admin_UI instance.
	 *
	 * @since 1.0.0
	 * @var null
	 */
	protected $network_admin_ui = null;


	/**
	 * Object holding CPTUIEXT_Admin_About instance.
	 *
	 * @since 1.0.0
	 * @var null
	 */
	protected $admin_about = null;

	/**
	 * Object holding CPTUIEXT_Customizer instance.
	 *
	 * @since 1.3.0
	 * @var null
	 */
	protected $customizer = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object CPTUI_Extended A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->plugin_name = __( 'Custom Post Type UI Extended', 'cptuiext' );

		$this->basename  = plugin_basename( __FILE__ );
		$this->url       = plugin_dir_url( __FILE__ );
		$this->path      = plugin_dir_path( __FILE__ );
		$this->version   = self::VERSION;
		$this->store_url = 'https://pluginize.com';
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since 1.0.0
	 */
	public function plugin_classes() {

		if ( class_exists( 'WDS_Shortcodes_Base' ) ) {

			$this->shortcode = new CPTUIEXT_Shortcode();
			$this->shortcode_admin = new CPTUIEXT_Shortcode_Admin(
				$this->shortcode->shortcode,
				self::VERSION,
				$this->shortcode->atts_defaults
			);
			$this->shortcode_admin->hooks();

			$this->shortcode_widget = new CPTUIEXT_Shortcode_Widget();
		}

		$this->admin_ui = new CPTUIEXT_Admin_UI( $this );

		if ( $this->is_network_activated() ) {
			if ( is_multisite() && is_network_admin() ) {
				$this->network_admin_ui = new CPTUIEXT_Network_Admin_UI( $this );
			}
		}

		$this->admin_about = new CPTUIEXT_Admin_About();
		$this->customizer = new CPTUIEXT_Customizer();
		$this->admin_about->hooks();
	}

	/**
	 * Add hooks and filters.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		$this->init();
		$this->load_libs();

		if ( $this->meets_requirements() ) {
			$this->plugin_classes();
			$this->includes();
		}

		$this->updater();

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'unload_scripts' ), 999 );

		add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'add_social_links' ) );

		/**
		 * Fires after Custom Post Type UI Extended has been loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cptuiext_loaded' );

	}

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 */
	function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
		$this->bump_version();
		cptui_add_activation_redirect();
	}

	/**
	 * Deactivate the plugin.
	 *
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since 1.0.0
	 */
	function _deactivate() {
		// $this->uninstall();
	}

	/**
	 * Init hooks.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_action( 'tgmpa_register', array( $this, 'cptuiext_register_required_plugins' ) );

		if ( is_multisite() && is_network_admin() ) {
			add_filter( 'tgmpa_notice_action_links', array( $this, 'cptui_filter_tgmpa_action_links' ) );
		}

		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'cptuiext', false, dirname( $this->basename ) . '/languages/' );
		}
	}

	/**
	 * Returns true if plugin is netowrk activated.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_network_activated() {

		$retval  = false;

		$base    = cptui_extended()->basename;
		$plugins = get_site_option( 'active_sitewide_plugins' );
		$is_multi = is_multisite();

		if ( ! $is_multi ) {
		    return $retval;
        }

        if ( ! is_array( $plugins ) ) {
		    return $retval;
        }

        // Override is_multisite() if not network activated.
		if ( isset( $plugins[ $base ] ) ) {
			$retval = true;
		}

		return $retval;
	}

	/**
	 * Load libraries
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Missing files exception.
	 */
	public function includes() {

		// Set up our includes.
		$includes = array(
			'core-loader.php',
			'helper-functions.php',
			'network.php', // Load admin setup.
			'post-types.php',
			'taxonomies.php',
			'post-list-columns.php', // Load post list columns.
			'helpscout.php', // Load helpscout config.
			'shortcode-functions.php', // Load shortcode functions.
			'shortcodes.php', // Load default shortcodes.
			'cmb2/cmb2-attached-posts/cmb2-attached-posts-field.php', // Load cmb2 attach posts.
			'cmb2/cmb2-post-search/cmb2-post-search-field.php', // Load cmb2 attach posts.
		);

		try {
			// Loop through each file and include it.
			foreach ( $includes as $include ) {

				// We don't need to do a file_exists check before includeding.
				// If the file doesn't exist, not including it will cause issues
				// either way. But we don't want to whitescreen a user's site,
				// so we'll attempt to include the file, but if it fails
				// we'll thrown an exception and surface that to the user.
				if ( ! include_once( __DIR__ . '/inc/' . $include ) ) {

					// Throw an error saying we were not able to include all files
					// for the plugin.
					throw new Exception( 'include_error' );
				}
			}
		} catch ( Exception $e ) {
			// If we did catch an exception, then surface an admin message to the user.
			add_action( 'all_admin_notices', array( $this, 'error_including_files_admin_notice' ) );
		}

	}

	/**
	 * Displays an admin error message regarding file inclusion error, and then deactivates ourselves.
	 *
	 * @since 1.2.1
	 */
	public function error_including_files_admin_notice() {

		// Surface our error to the user, suggesting that they maybe re-install the plugin.
		echo '<div class="error notice"><p>';
		esc_html_e( 'Error including files for CPTUI Extended. For your safety, CPTUI Extended has been de-activated. Please try re-installing the plugin.', 'cptuiext' );
		echo '</p></div>';

		// Deactivate our plugin.
		$this->deactivate_me();
	}

	/**
	 * Bumb version option.
	 */
	public function bump_version() {

	   	$version = get_option( '_cptui_db_version' );

		if ( self::VERSION !== $version ) {
			update_option( '_cptui_db_version', self::VERSION );
		}
		$this->db_version_raw = self::VERSION ? self::VERSION : 0;
	}

	/**
	 * Unload Scripts.
	 */
	public function unload_scripts() {
		wp_dequeue_style( 'jquery-ui-css' );
	}

	/**
	 * Scripts.
	 */
	public function scripts() {
		global $pagenow;

		if ( function_exists( 'cptui_get_shortcode_data' ) ) {

			if ( isset( $pagenow ) && 'post.php' === $pagenow ||  'post-new.php' === $pagenow ) {
				// Register out javascript file.
				wp_register_script( 'cptui_admin', cptui_extended()->url() . 'assets/js/plugin.js' );

				// Localize the script with data.
				$translation_array = array(
					'shortcodes' => cptui_get_shortcode_data(),
					'taxonomy' => cptui_get_shortcode_taxonomies(),
				);
				wp_localize_script( 'cptui_admin', 'cptui', $translation_array );

				// Enqueued script with localized data.
				wp_enqueue_script( 'cptui_admin' );
			}
		}

		if ( isset( $pagenow ) && 'index.php' === $pagenow ) {
			$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
			if ( 'cptui_about' === $page || 'cptui_credits' === $page ) {
				wp_register_style( 'cptui_about_css', cptui_extended()->url() . 'assets/css/about.css' );
				wp_enqueue_style( 'cptui_about_css' );
			}
		}

		if ( is_network_admin() ) {
			wp_enqueue_script( 'cptui' );
		}

	}

	/**
	 * Load libraries
	 *
	 * @since 1.0.0
	 */
	public function load_libs() {

		// Load cmb2 if we need to.
		if ( ! ( defined( 'CMB2_LOADED' ) && CMB2_LOADED ) ) {
			require_once  __DIR__ . '/vendor/CMB2/init.php';
		}

		// Load shortcodes if we need to.
		if ( ! function_exists( 'wds_shortcodes' ) ) {
			require_once __DIR__ . '/vendor/WDS-Shortcodes/wds-shortcodes.php';
		}

		// Load helpscout widget.
		if ( ! class_exists( 'Helpscout_Customer' ) ) {
			require_once  __DIR__ . '/vendor/helpscout/helpscout-dashboard-widget.php';
		}

		// Load TGMPA class.
		require_once  __DIR__ . '/vendor/tgmpa/class-tgm-plugin-activation.php';
		require_once __DIR__ . '/vendor/gamajo/template-loader/class-gamajo-template-loader.php';

		require_once __DIR__ . '/vendor/edd-updater/license-handler.php';
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean $value Result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {
			return false;
		}

		return true;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since 1.0.0
	 */
	public function deactivate_me() {

		// We do a check for is_plugin_active before calling it, to protect
		// any developers from accidently calling it too early and breaking things.
		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( $this->basename ) ) {
			deactivate_plugins( $this->basename );
		}

	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  1.0.0
	 *
	 * @return boolean $value True if requirements are met.
	 */
	public static function meets_requirements() {

		// Do checks for required classes / functions.
		if ( ! function_exists( 'cptui_create_custom_post_types' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since 1.0.0
	 */
	public function requirements_not_met_notice() {
		// Output our error.
		$error_text = sprintf(
			esc_html__( 'CPTUI Extended is missing requirements and has been %sdeactivated%s. Please make sure all requirements are available.', 'cptuiext' ),
			sprintf(
				'<a href="%s">',
				admin_url( 'plugins.php' )
			),
			'</a>'
		);

		echo '<div id="message" class="error">';
		echo '<p>' . esc_attr( $error_text ) . '</p>';
		echo '</div>';
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws exception if the field is invalid.
	 *
	 * @param string $field Field to get.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  1.0.0
	 * @param  string $filename Name of the file to be included.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( 'classes/class-' . $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  1.0.0
	 *
	 * @param string $path Appended path. Optional.
	 * @return string $value Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  1.0.0
	 *
	 * @param string $path Appended path. Optional.
	 * @return string $value URL and path.
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}

	/**
	 * TGMPA filter action links
	 *
	 * @since 1.0.0
	 *
	 * @param array $action_links links in damin notice.
	 * @return array
	 */
	public function cptui_filter_tgmpa_action_links( $action_links ) {

		foreach ( $action_links as $link => $value ) {

			switch ( $link ) {
				case 'activate':
					$action_links['activate'] = ! empty( $value ) ? '<a href="' . network_admin_url( 'plugins.php' ) . '">Begin activating plugin</a>' : '';
				break;
				case 'install':
					$action_links['install'] = ! empty( $value ) ? '<a href="' . network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=custom-post-type-ui&TB_iframe=true&width=772&height=597' ) . '" class="thickbox">Begin installing plugin</a>' : '';
				break;
			}
		}

		return $action_links;

	}

	/**
	 * TGMPA required plugins
	 *
	 * @since 1.0.0
	 */
	public function cptuiext_register_required_plugins() {

		/**
		 *  Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(

			// Include a Custom Post Type UI from the WordPress Plugin Repository.
			array(
				'name'     => 'Custom Post Type UI',
				'slug'     => 'custom-post-type-ui',
				'required' => true,
			),

		);

		$config = array(
			'id'           => 'cptuiext',              // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php',           // Parent menu slug.
			'capability'   => 'manage_options',        // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => false,                   // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',
			'strings'      => array(
				'menu_title'                     => esc_html__( 'Required Plugins', 'cptuiext' ),
				'notice_can_install_required'    => _n_noop(
					'This plugin requires the following plugin: %1$s.',
					'This plugin requires the following plugins: %1$s.',
					'cptuiext'
				),
				'notice_can_install_recommended' => _n_noop(
					'This plugin recommends the following plugin: %1$s.',
					'This plugin recommends the following plugins: %1$s.',
					'cptuiext'
				),
			),

		);

		if ( function_exists( 'tgmpa' ) ) {
			tgmpa( $plugins, $config );
		}
	}

	/**
	 * Add social media links to plugin screen.
	 *
	 * @since 1.1.1
	 *
	 * @param array $links Plugin action links.
	 * @return array $links Amended array of links to display.
	 */
	public function add_social_links( $links ) {

		$site_link = 'http://pluginize.com/';
		$twitter_status = sprintf( __( 'Check out %s from @pluginize %s', 'cptuiextended' ), $this->plugin_name, 'https://pluginize.com/' );

		array_push( $links, '<a title="' . esc_attr__( 'More plugins for your WordPress site here!', 'cptuiextended' ) . '" href="' . $site_link . '" target="_blank">pluginize.com</a>' );
		array_push( $links, '<a title="' . esc_attr__( 'Spread the word on Facebook!', 'cptuiextended' ) . '" href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $site_link ) . '" target="_blank" class="dashicons-before dashicons-facebook-alt"></a>' );
		array_push( $links, '<a title="' . esc_attr__( 'Spread the word on Twitter!', 'cptuiextended' ) . '" href="https://twitter.com/home?status=' . urlencode( $twitter_status ) . '" target="_blank" class="dashicons-before dashicons-twitter"></a>' );

		return $links;
	}

	/**
	 * Run our updater routine.
	 *
	 * @since 1.4.0
	 */
	public function updater() {
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			require_once $this->path . 'vendor/edd-updater/EDD_SL_Plugin_Updater.php';
		}
		$license_key = trim( get_option( 'cptui_extended_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater( $this->store_url, __FILE__, array(
				'version'   => $this->version,     // Current version number.
				'license'   => $license_key,       // license key (used get_option above to retrieve from DB)
				'item_name' => $this->plugin_name, // name of this plugin
				'author'    => 'Pluginize'         // author of this plugin.
			)
		);
	}
}

/**
 * Grab the CPTUI_Extended object and return it.
 *
 * Wrapper for CPTUI_Extended::get_instance()
 *
 * @since 1.0.0
 *
 * @return CPTUI_Extended Singleton instance of plugin class.
 */
function cptui_extended() {
	return CPTUI_Extended::get_instance();
}

// PHPCS does not like the name of this function,
// so we'll suppress anything for this.
// @codingStandardsIgnoreStart
if ( ! function_exists( 'CPTUIEXT_AME' ) ) {

	/**
	 * Grab the CPTUI_Extended object and pass to updater.
	 *
	 * Wrapper for cptui_extended()
	 *
	 * @since 1.0.0
	 *
	 * @return CPTUI_Extended Singleton instance of plugin class.
	 */
	function CPTUIEXT_AME() {
		return cptui_extended()->updater;
	}
}
// @codingStandardsIgnoreEnd

// Kick it off.
add_action( 'plugins_loaded', array( cptui_extended(), 'hooks' ) );

register_activation_hook( __FILE__, array( cptui_extended(), '_activate' ) );
register_deactivation_hook( __FILE__, array( cptui_extended(), '_deactivate' ) );
define( 'CPTUI_EXTENDED_PLUGIN_DIR', cptui_extended()->path );

/**
 * Dismisses the activation license notice.
 *
 * @since 1.1.0
 */
function cptui_dismiss_activation_notice() {
	if ( isset( $_GET['cptui-extended-dismiss-activation'] ) && 'dismiss' === $_GET['cptui-extended-dismiss-activation'] ) {
		if ( is_admin() || is_network_admin() ) {
			update_option( 'cptuiext_plugin_activated_dismissed', 'dismissed' );
			wp_redirect( remove_query_arg( 'cptui-extended-dismiss-activation', $_SERVER['REQUEST_URI'] ) );
			exit;
		}
	}
}
add_action( 'admin_init', 'cptui_dismiss_activation_notice', 999 );

/**
 * Sets transient for redirects in Multisite.
 *
 * @since 1.0.0
 */
function cptui_add_activation_redirect() {

	// Bail if activating from network, or bulk.
	if ( isset( $_GET['activate-multi'] ) ) {
		return;
	}
	// Add the transient to redirect.
	set_transient( '_cptui_activation_redirect', true, 30 );
}

/**
 * Convert old settings to new. Settings keys changed in 1.2.0.
 *
 * @since 1.2.1
 */
function cptui_extended_options_convert() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	$version = cptui_extended()->version;
	$updated = false;
	if ( version_compare( $version, '1.2.1', '<=' ) ) {
		// Some users may have already deactivated and reactivated.
		$test_option = get_option( 'cptuiext_plugin' );
		if ( ! empty( $test_option ) ) {
			return;
		}

		$map = array(
			// Set up our pairs to switch values to.
			array( 'cptui_extended_plugin', 'cptuiext_plugin' ),
			array( 'cptui_extended_plugin_product_id', 'cptuiext_plugin_product_id' ),
			array( 'cptui_extended_plugin_instance', 'cptuiext_plugin_instance' ),
			array( 'cptui_extended_plugin_deactivate_checkbox', 'cptuiext_plugin_deactivate_checkbox' ),
			array( 'cptui_extended_plugin_activated', 'cptuiext_plugin_activated' ),
			array( 'cptui_extended_plugin_activated_dismissed', 'cptuiext_plugin_activated_dismissed' ),
		);

		foreach ( $map as $option_keys ) {
			$old_option_value = get_option( $option_keys[0], '' );
			if ( '' !== $old_option_value ) {
				$updated = update_option( $option_keys[1], $old_option_value );
			}

			if ( $updated ) {
				// Remove our old settings for safety sake.
				delete_option( $option_keys[0] );
			}
		}
	}

	// Rudamentary upgrade routine to spare re-entry of license when we move everyone to EDD.
	// Let's not remove old options just yet. Once we get people upgraded to latest versions, then
	// we can spend time with some db cleanup.
	if ( version_compare( $version, '1.3.3', '<' ) ) {
		$woo_options = get_option( 'cptuiext_plugin' );
		if ( ! empty( $woo_options['api_key'] ) ) {
			$updated = update_option( 'cptui_extended_license_key', $woo_options['api_key'] );
		}
	}

	if ( $updated ) {
		update_option( 'cptuiext_plugin_activated_dismissed', 'dismissed' );
		// Force away from Ext API page so fields have time to be populated.
		wp_redirect( get_admin_url() );
	}
}
add_action( 'admin_init', 'cptui_extended_options_convert', 5 );

/**
 * Remove ads from CPTUI free version.
 *
 * @since 1.2.1
 */
function cptui_remove_ads() {
	// Remove our ads in Free.
	remove_action( 'cptui_below_post_type_tab_menu', 'cptui_products_sidebar', 10 );
	remove_action( 'cptui_below_taxonomy_tab_menu', 'cptui_products_sidebar', 10 );
	add_action( 'admin_head', 'cptui_no_ad_css' );
}
add_action( 'init', 'cptui_remove_ads' );

function cptui_no_ad_css() {
	?>
<style>.posttypesui, .taxonomiesui { width: 100%; }</style>
<?php
}
