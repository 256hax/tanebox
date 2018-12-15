<?php
/**
 * CPTUI Extended Template Loader
 *
 * @package    CPTUIExtended
 * @subpackage TemplateLoader
 * @author     Pluginize
 * @since      1.4.0
 */

/**
 * Template loader for CPTUI-Extended.
 *
 * Only need to specify class properties here.
 */
class CPTUIEXT_Template_Loader extends Gamajo_Template_Loader {

	/**
	 * Prefix for filter names.
	 *
	 * @since 1.4.0
	 * @var string
	 */
	protected $filter_prefix = 'cptui_extended';

	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * @since 1.4.0
	 * @var string
	 */
	protected $theme_template_directory = 'cptui-extended';

	/**
	 * Reference to the root directory path of this plugin.
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 * In this case, `MEAL_PLANNER_PLUGIN_DIR` would be defined in the root plugin file as:
	 * ~~~
	 * define( 'MEAL_PLANNER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	 * ~~~
	 *
	 * @since 1.4.0
	 * @var string
	 */
	protected $plugin_directory = CPTUI_EXTENDED_PLUGIN_DIR;

	/**
	 * Directory name where templates are found in this plugin.
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 * e.g. 'templates' or 'includes/templates', etc.
	 *
	 * @since 1.4.0
	 * @var string
	 */
	protected $plugin_template_directory = 'templates';

	/**
	 * Copy our template files into current theme directory.
	 *
	 * Borrowed from YARPP.
	 *
	 * @since 1.4.0
	 *
	 * @return bool|mixed
	 */
	public function copy_templates() {
		$templates_dir = trailingslashit( trailingslashit( $this->plugin_directory ) . $this->plugin_template_directory );
		$to = trailingslashit( get_stylesheet_directory() ) . $this->theme_template_directory;

		if ( ! file_exists( $to ) ) {
			wp_mkdir_p( $to );
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem( false, get_stylesheet_directory() );
		global $wp_filesystem;
		if ( 'direct' !== $wp_filesystem->method ) {
			return false;
		}

		return copy_dir( $templates_dir, $to, array( '.svn' ) );
	}

	/**
	 * Checkes whether or not we can write to active theme directory.
	 *
	 * Borrowed from YARPP.
	 *
	 * @since 1.4.0
	 *
	 * @return bool
	 */
	public function can_copy_templates() {
		$theme_dir = get_stylesheet_directory();

		// If we can't write to the theme, return false.
		if ( ! is_dir( $theme_dir ) || ! is_writable( $theme_dir ) ) {
			return false;
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem( false, get_stylesheet_directory() );
		global $wp_filesystem;

		// Direct method is the only method that I've tested so far.
		return ( 'direct' === $wp_filesystem->method );
	}
}
