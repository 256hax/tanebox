<?php
/**
 * CPTUI Misc helper functions.
 *
 * @package CPTUIExtended
 * @author  Pluginize
 * @since   1.4.0
 */

/**
 * Convenience function for changing Tax Query relations.
 *
 * Provides ability to just put the following in functions.php file.
 * `add_filter( 'cptui_tax_query_relation', 'cptuiext_return_or_relation' );`
 *
 * @since 1.4.0
 *
 * @return string
 */
function cptuiext_return_or_relation() {
	return 'OR';
}

/**
 * Limit the excerpt length.
 *
 * @param array $args Parameters include length and more.
 * @return string The shortened excerpt.
 */
function cptuiext_get_the_excerpt( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'length' => 20,
		'more'   => '...',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Trim the excerpt.
	return wp_trim_words( get_the_excerpt(), absint( $args['length'] ), esc_html( $args['more'] ) );
}
