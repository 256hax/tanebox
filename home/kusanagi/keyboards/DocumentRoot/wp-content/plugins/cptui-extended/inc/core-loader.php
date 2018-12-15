<?php
/**
 * CPTUI Extended Network.
 *
 * @package CPTUIExtended
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Redirects activation to welcom page.
 *
 * @internal
 */
function cptui_do_activation_redirect() {

	if ( ! get_transient( '_cptui_activation_redirect' ) ) {
		return;
	}

	delete_transient( '_cptui_activation_redirect' );

	// Bail if activating from network, or bulk.
	if ( isset( $_GET['activate-multi'] ) ) {
		return;
	}

	$query_args = array( 'page' => 'cptui_about' );

	// Redirect to CPTUI Extended about page.
	wp_safe_redirect( add_query_arg( $query_args, cptui_get_admin_url( 'index.php' ) ) );
}
add_action( 'admin_init', 'cptui_do_activation_redirect', 1 );

/**
 * Get single or network admin url.
 *
 * @param string $path Requested path.
 * @return string
 */
function cptui_get_admin_url( $path = '' ) {

	$url = admin_url( $path );

	if ( cptui_is_network_activated() ) {
		$url = network_admin_url( $path );
	}

	return $url;
}

/**
 * Boolean if plugin network activated.
 *
 * @return boolean is plugin network activated
 */
function cptui_is_network_activated() {

	// Default to is_multisite().
	$retval  = is_multisite();

	// Check the sitewide plugins array.
	$base    = cptui_extended()->basename;
	$plugins = get_site_option( 'active_sitewide_plugins' );

	// Override is_multisite() if not network activated.
	if ( ! is_array( $plugins ) || ! isset( $plugins[ $base ] ) ) {
		$retval = false;
	}

	/**
	 * Filters whether or not we're active at the network level.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $retval Whether or not we're network activated.
	 */
	return (bool) apply_filters( 'cptui_is_network_activated', $retval );
}

/**
 * Display footer links and plugin credits.
 *
 * @since 1.5.0
 * @internal
 *
 * @param string $original Original footer content. Optional. Default empty string.
 * @return string $value HTML for footer.
 */
function cptui_extended_footer( $original = '' ) {

	$screen = get_current_screen();

	if ( ! is_object( $screen ) || 'cptui_main_menu' !== $screen->parent_base ) {
		return $original;
	}
	$cptuiext = cptui_extended();
	return sprintf(
			__( '%s version %s by %s with CPTUI-Extended version %s by %s', 'custom-post-type-ui' ),
			__( 'Custom Post Type UI', 'custom-post-type-ui' ),
			CPTUI_VERSION,
			'<a href="https://webdevstudios.com" target="_blank">WebDevStudios</a>',
			$cptuiext::VERSION,
			'<a href="https://pluginize.com" target="_blank">Pluginize</a>'
		) . ' - ' .
		sprintf(
			'<a href="http://wordpress.org/support/plugin/custom-post-type-ui" target="_blank">%s</a>',
			__( 'Support forums', 'custom-post-type-ui' )
		) . ' - ' .
		sprintf(
			'<a href="https://wordpress.org/plugins/custom-post-type-ui/reviews/" target="_blank">%s</a>',
			__( 'Review CPTUI', 'custom-post-type-ui' )
		) . ' - ' .
		__( 'Follow on Twitter:', 'custom-post-type-ui' ) .
		sprintf(
			' %s %s',
			'<a href="https://twitter.com/webdevstudios" target="_blank">WebDevStudios</a>',
			'<a href="https://twitter.com/pluginize" target="_blank">Pluginize</a>'
		);
}
add_filter( 'admin_footer_text', 'cptui_extended_footer', 11 );
