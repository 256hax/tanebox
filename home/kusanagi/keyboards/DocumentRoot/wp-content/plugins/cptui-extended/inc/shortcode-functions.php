<?php
/**
 * CPTUI Extended Shortcode Functions.
 *
 * @package CPTUIExtended
 * @subpackage Loader
 * @author Pluginize
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cptui_add_shortcode_field.
 *
 * @internal
 *
 * @param array $fields array of cmb fields.
 * @return array Arra of fields.
 */
function cptui_add_shortcode_field( $fields = array() ) {

	$fields[] = array(
		'name'             => __( 'Choose a Layout', 'cptuiext' ),
		'desc'             => __( 'Select a shortcode and fill in options', 'cptuiext' ),
		'id'               => 'cptui_shortcode',
		'type'             => 'select',
		'show_option_none' => true,
		'default'          => 'none',
	);

	$fields[] = array(
		'name'    => __( 'Shortcode ID', 'cptuiext' ),
		'id'      => 'cptui_shortcode_id',
		'type'    => 'hidden',
		'default' => mt_rand( 100, 5000 ),
	);

	return $fields;

}
add_filter( 'cptui_fields', 'cptui_add_shortcode_field' );


/**
 * Cptui_proccess_shorcode_button_data.
 *
 * @internal
 *
 * @param array $fields            CMB2 fields data.
 * @param array $unmodified_fields Unmodified CMB2 fields.
 * @return array
 */
function cptui_process_shortcode_button_data( $fields, $unmodified_fields = array() ) {

	$modified_array = array();

	foreach ( $fields as $field_key => $field ) {
		if ( is_array( $field ) ) {
			foreach ( $field[0] as $item_key => $item_value ) {

				if ( is_array( $item_value ) ) {
					foreach ( $item_value as $key => $val ) {
						$modified_array[ $key ] = $val;
					}
				} else {
					if ( 'title' === $item_key ) {
						$item_value = urlencode( $item_value );
					}
					$modified_array[ $item_key ] = $item_value;
				}
			}
		} else {
			$modified_array[ $field_key ] = $field;
		}
	}

	/**
	 * Filters the shortcode button data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $modified_array Array of modified data.
	 * @param array $fields         CMB2 fields data.
	 */
	return apply_filters( 'cptui_shortcode_modified_fields', $modified_array, $fields );

}
add_filter( 'cptui_shortcode_fields', 'cptui_process_shortcode_button_data', 10, 2 );

/**
 * Register a shortcode with CPTUI Extended.
 *
 * @param string  $location_callback Callback string.
 * @param integer $priority          Shortcode callback priority.
 * @return string
 */
function cptui_register_shortcode( $location_callback = '', $priority = 10 ) {

	// Bail if no location, or function/method is not callable.
	if ( empty( $location_callback ) || ! is_callable( $location_callback ) ) {
		return false;
	}

	// Add location callback to template stack.
	return add_filter( 'cptui_shortcode_stack', $location_callback, (int) $priority );
}

/**
 * Fetch a requested shortcode.
 *
 * @internal
 * @return array
 */
function cptui_get_shortcode() {
	global $wp_filter, $merged_filters, $wp_current_filter;

	$tag  = 'cptui_shortcode_stack';
	$args = $stack = array();

	// Add 'cptui_shortcode_stack' to the current filter array.
	$wp_current_filter[] = $tag;

	// Sort.
	if ( class_exists( 'WP_Hook' ) ) {
		$filter = $wp_filter[ $tag ]->callbacks;
	} else {
		$filter = &$wp_filter[ $tag ];

		if ( ! isset( $merged_filters[ $tag ] ) ) {
			ksort( $filter );
			$merged_filters[ $tag ] = true;
		}
	}

	// Ensure we're always at the beginning of the filter array.
	reset( $filter );

	// Loop through 'spb_template_stack' filters, and call callback functions.
	do {
		foreach ( (array) current( $filter ) as $the_ ) {
			if ( ! is_null( $the_['function'] ) ) {
				$args[1] = $stack;
				$stack[] = call_user_func_array( $the_['function'], array_slice( $args, 1, (int) $the_['accepted_args'] ) );
			}
		}
	} while ( next( $filter ) !== false );

	// Remove 'cptui_shortcode_stack' from the current filter array.
	array_pop( $wp_current_filter );

	// Remove empties and duplicates
	// $stack = array_unique( array_filter( $stack ) );.

	/**
	 * Filters the "shortcode stack" list of registered directories.
	 *
	 * @since 1.0.0
	 *
	 * @param array $stack Array of registered directories for template locations.
	 */
	return (array) apply_filters( 'cptui_get_shortcode', $stack );
}

/**
 * Cptui_locate_template
 *
 * @internal
 * @param array   $template_names array of template names.
 * @param boolean $load to load template file.
 * @param boolean $require_once require the file once.
 * @return boolean
 */
function cptui_locate_template_stack( $template_names, $load = false, $require_once = true ) {

	// No file found yet.
	$located            = false;
	$template_locations = array();

	// Try to find a template file.
	foreach ( (array) $template_names as $template_name ) {

		$template_name = explode( '/', $template_name );

		if ( empty( $template_name ) ) {
			continue;
		}

		// Trim off any slashes from the template name.
		$template_name  = ltrim( end( $template_name ), '/' );

		foreach ( $template_locations as $template_location ) {

			if ( empty( $template_location ) ) {
				continue;
			}

			// Check child theme first.
			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'cptui/' . $template_name ) ) {
				$located = trailingslashit( get_stylesheet_directory() ) . 'cptui/' . $template_name;
				break 2;

				// Check parent theme next.
			} elseif ( file_exists( trailingslashit( get_template_directory() ) . 'cptui/' . $template_name ) ) {
				$located = trailingslashit( get_template_directory() ) . 'cptui/' . $template_name;
				break 2;

				// Check template stack last.
			} elseif ( file_exists( trailingslashit( $template_location ) . $template_name ) ) {
				$located = trailingslashit( $template_location ) . $template_name;
				break 2;
			}
		}
	}

	/**
	 * Fires after a CPTUI Extended template file has been located.
	 *
	 * @since 1.0.0
	 *
	 * @param string|bool $located            Path to the located template to load. False if none found.
	 * @param string      $template_name      Name of the template file found.
	 * @param array       $template_names     Array of provided template names.
	 * @param array       $template_locations Array of template locations to check in.
	 * @param bool        $load               Whether or not to load the template.
	 * @param bool        $require_once       Whether or not to use require_once.
	 */
	do_action( 'cptui_locate_template', $located, $template_name, $template_names, $template_locations, $load, $require_once );

	// Maybe load the template if one was located.
	$use_themes = defined( 'WP_USE_THEMES' ) && WP_USE_THEMES;
	$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
	if ( ( $use_themes || $doing_ajax ) && ( true === $load ) && ! empty( $located ) ) {
		load_template( $located, $require_once );
	}

	return $located;
}

/**
 * Cptui_process_shortcode_fields
 *
 * @internal
 *
 * @param array $fields array of shortcode data.
 * @return array
 */
function cptui_process_shortcode_fields( $fields ) {

	$defaults = array(
		'id'          => '10_posts',
		'type'        => 'group',
		'repeatable'  => false,
		'description' => ' ',
		'options'     => array(
			'group_title' => ' ',
		),
		'fields'      => array(),
	);

	$shortcodes = cptui_get_shortcode();

	foreach ( $shortcodes as $shortcode ) {

		$shortcode['options']['group_title'] = $shortcode['name'];
		unset( $shortcode['name'] );
		$fields[] = wp_parse_args( $shortcode, $defaults );
	}

	return $fields;

}
add_filter( 'cptui_fields', 'cptui_process_shortcode_fields' );

/**
 * Cptui_get_shortcode_ids
 *
 * @return array
 */
function cptui_get_shortcode_ids() {

	$shortcodes = cptui_get_shortcode();

	$ids = array();
	foreach ( $shortcodes as $key => $value ) {
		$ids[ $value['id'] ] = $value['name'];
	}

	return $ids;

}

/**
 * Cptui_get_shortcode_data
 *
 * @return array
 */
function cptui_get_shortcode_data() {

	$shortcodes = cptui_get_shortcode();

	$ids = array();
	foreach ( $shortcodes as $key => $value ) {
		$ids[ $value['post_type'] ][ $value['id'] ] = $value['name'];
	}

	return $ids;

}

/**
 * Returns string of shortcode classes.
 *
 * @param string $shortcode shortcode id.
 * @return string shortcode classes
 */
function cptui_shortcode_classes( $shortcode = '' ) {

	$shortcode_class = strtolower( str_replace(
		'_', '-',
		$shortcode
	) );

	$classes = array( 'cptui-shortcode', $shortcode_class );
	$classes = implode( ' ', $classes );

	/**
	 * Filters the classes to apply to the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes   Classes to apply to the shortcode output.
	 * @param string $shortcode Shortcode ID.
	 */
	return apply_filters( 'cptui_shortcode_classes', $classes, $shortcode );

}

/**
 * Returns registered css string id of shortcode.
 *
 * @param string $shortcode shortcode id.
 * @return string registered css id
 */
function cptui_get_shortcode_style( $shortcode = '' ) {

	$shortcodes = cptui_get_shortcode();
	$shortcodes = json_decode( json_encode( $shortcodes ) );

	foreach ( $shortcodes as $key => $value  ) {

		if ( $shortcode === $value->id ) {
			if ( isset( $value->style ) ) {
				return $value->style;
			}
		}
	}
	return '';
}

/**
 * Returns registered script string id of shortcode.
 *
 * @param string $shortcode shortcode id.
 * @return string registered script id
 */
function cptui_get_shortcode_script( $shortcode = '' ) {

	$shortcodes = cptui_get_shortcode();
	$shortcodes = json_decode( json_encode( $shortcodes ) );

	foreach ( $shortcodes as $key => $value  ) {

		if ( $shortcode === $value->id ) {
			if ( isset( $value->script ) ) {
				return $value->script;
			}
		}
	}
	return '';
}

/**
 * Returns array of taxonomies.
 *
 * @return array post type taxonomies
 */
function cptui_get_shortcode_taxonomies() {

	$post_types = cptui_get_post_types(); // Includes built in post types.
	$taxonomies = array();

	/**
	 * Provide filter to ignore taxonomies from consideration for post type edit screens.
	 *
	 * @since 1.5.2
	 *
	 * @param array $value      Array of taxonomies to ignore.
	 * @param array $post_types Found post types for a given install.
	 */
	$ignored_taxonomies = apply_filters( 'cptui_shortcode_ignored_taxonomies', array(), $post_types );
	foreach ( $post_types as $post_type ) {

		$taxonomy_objects = get_object_taxonomies( $post_type, 'objects' );

		foreach ( $taxonomy_objects as $taxonomy ) {
			if ( is_array( $ignored_taxonomies ) && in_array( $taxonomy->name, $ignored_taxonomies ) ) {
				continue;
			}
			$terms = get_terms( $taxonomy->name, array(
				'orderby'    => 'name',
				'hide_empty' => 0,
			) );
			if ( ! empty( $terms ) ) {
				$taxonomies[ $post_type ][ $taxonomy->labels->name ] = $terms;
			}
		}
	}

	return $taxonomies;
}

/**
 * Locates and loads the determined template file.
 *
 * @param string $shortcode_id shortcode id.
 * @return string
 */
function cptui_locate_template( $shortcode_id = '' ) {

	$shortcodes = cptui_get_shortcode();

	foreach ( $shortcodes as $key => $value ) {
		if ( $shortcode_id === $shortcodes[ $key ]['id'] ) {
			$template = trailingslashit( $shortcodes[ $key ]['template_location'] ) . $shortcodes[ $key ]['template'] . '.php';
			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return trailingslashit( $shortcodes[ $key ]['template_location'] ) . 'posts.php';
			}
		}
	}
	return trailingslashit( cptui_extended()->dir() . 'templates' ) . 'error.php';
}


/**
 * Returns the template directory path with a trailing slash.
 *
 * @return string path to templates folder
 */
function cptui_template_path() {
	return trailingslashit( cptui_extended()->dir() . 'templates' );
}

/**
 * Unset post Types.
 *
 * @internal
 *
 * @return array registered post types
 */
function cptui_get_post_types() {

	$post_types = get_post_types( array( 'public' => true ) );

	$reserved = array(
		'attachment',
		'revision',
		'nav_menu_item',
		'action',
		'order',
		'theme',
	);

	foreach ( $reserved as $post_type ) {
		unset( $post_types[ $post_type ] );
	}

	/**
	 * Filters the post types to use.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post_types Array of available post types.
	 */
	return apply_filters( 'cptui_get_post_types', $post_types );

}

/**
 * Array of published pages on site.
 *
 * @return array
 */
function cptui_get_pages() {
	$args = array(
		'sort_order'   => 'asc',
		'sort_column'  => 'post_title',
		'hierarchical' => 1,
		'exclude'      => '',
		'include'      => '',
		'meta_key'     => '',
		'meta_value'   => '',
		'authors'      => '',
		'child_of'     => 0,
		'parent'       => - 1,
		'exclude_tree' => '',
		'number'       => '',
		'offset'       => 0,
		'post_type'    => 'page',
		'post_status'  => 'publish',
	);

	$pages = new WP_Query( $args );
	$page_data = array();

	foreach ( $pages->posts as $page => $value ) {
		$page_data[ $pages->posts[ $page ]->ID ] = $pages->posts[ $page ]->post_title;
	}

	/**
	 * Filters the array of publisehd pages on the website.
	 *
	 * @since 1.0.0
	 *
	 * @param array $page_data Array of pages that are published.
	 */
	return apply_filters( 'cptui_get_pages', $page_data );
}

/**
 * Returns default shortcode attributes array.
 *
 * @param string $shortcode shortcode id.
 * @return array default attributes array.
 */
function cptui_get_default_atts( $shortcode = '' ) {

	$atts = array();

	if ( $shortcodes = cptui_is_shortcode( $shortcode ) ) {
		foreach ( $shortcodes as $key => $shortcode_value ) {
			if ( $shortcode === $shortcodes[ $key ]['id'] ) {
				foreach ( $shortcodes[ $key ]['fields'] as $field => $value ) {
					$atts[ $value['id'] ] = '';
				}
				break;
			}
		}
	}
	return $atts;
}

/**
 * Checks if shortcode exists.
 *
 * @param string $shortcode shortcode id.
 * @return boolean  if shortcode exists
 */
function cptui_is_shortcode( $shortcode = '' ) {

	$shortcodes = cptui_get_shortcode();

	foreach ( $shortcodes as $key => $value ) {
		if ( $value['id'] === $shortcode ) {
			return $shortcodes;
			break; // Exit the loop.
		}
	}
	return false;

}

/**
 * Returns parsed shortcode attributes array.
 *
 * @param string $attributes shortcode attributes.
 * @return array parsed attributes array.
 */
function cptui_shortcode_atts( $attributes ) {
	/**
	 * Filters shortcode atrributes.
	 *
	 * @param array $attributes shortcode attributes.
	 */
	return apply_filters( 'cptui_shortcode_atts', $attributes );
}

/**
 * Returns parsed post query args.
 *
 * @param string $attributes shortcode attributes.
 * @return array parsed post query.
 */
function cptui_filter_query( $attributes ) {

	/**
	 * Filters WP_Query for a shortrcode.
	 *
	 * @param array $args post query args.
	 * @param array $attributes shortcode attributes.
	 */
	return apply_filters( 'cptui_filter_query', array(), $attributes );
}

/**
 * Filter WP_Query with shortcode data
 *
 * @internal
 * @param  array $args post query arguments.
 * @param  array $attributes shortcode attributes.
 * @return array parsed post query.
 */
function cptui_filter_shortcode_query( $args, $attributes ) {

	$paged = isset( $_GET[ $attributes['cptui_shortcode_id'] . '-page' ] ) ? esc_attr( $_GET[ $attributes['cptui_shortcode_id'] . '-page' ] ) : 1;

	switch ( $attributes['cptui_shortcode'] ) {
		case 'default_shortcode':

			$args['post_type'] = isset( $attributes['cptui_posttype'] ) ? $attributes['cptui_posttype'] : 'post';

			if ( isset( $attributes['attached_post'] ) && '' !== $attributes['attached_post'] ) {
				$args['post__in'] = explode( ',', $attributes['attached_post'] );
			}

			$args['posts_per_page'] = isset( $attributes['amount'] ) ? esc_attr( $attributes['amount'] ) : '';
			$args['paged'] = $paged;

			break;
		case 'grid_with_overlay_shortcode':

			$args['post_type'] = isset( $attributes['cptui_posttype'] ) ? $attributes['cptui_posttype'] : 'post';

			if ( isset( $attributes['attached_post'] ) && '' !== $attributes['attached_post'] ) {
				$args['post__in'] = explode( ',', $attributes['attached_post'] );
			}

			// Have grid with overlay layout default to 5 instead of WP_Query default of Settings > Reading or 10.
			$args['posts_per_page'] = isset( $attributes['amount'] ) ? esc_attr( $attributes['amount'] ) : 5;

			break;
		case 'single_post_shortcode':

			if ( isset( $attributes['post_id'] ) ) {
				$args['p'] = $attributes['post_id'];
			}

			break;
		case 'single_page_shortcode':

			if ( isset( $attributes['page_id'] ) ) {
				$args['page_id'] = $attributes['page_id'];
			}

			break;
		case 'featured_plus_shortcode':

			$args['post_type'] = isset( $attributes['cptui_posttype'] ) ? $attributes['cptui_posttype'] : 'post';

			if ( isset( $attributes['featured_plus_type'] ) && 'top' === $attributes['featured_plus_type'] ) {
				$args['posts_per_page'] = isset( $attributes['amount'] ) ? esc_attr( $attributes['amount'] ) : 5;
			} else {
				$args['posts_per_page'] = '4';
			}

			$args['paged'] = $paged;

			break;
		case 'single_post_type_shortcode':

			if ( isset( $attributes['post_id'] ) ) {
				$args['p'] = $attributes['post_id'];
			}

			if ( isset( $attributes['cptui_posttype'] ) ) {
				$args['post_type'] = $attributes['cptui_posttype'];
			}

			break;
		case 'list_shortcode':

			$args['post_type'] = isset( $attributes['cptui_posttype'] ) ? $attributes['cptui_posttype'] : 'post';

			if ( isset( $attributes['attached_post'] ) && '' !== $attributes['attached_post'] ) {
				$args['post__in'] = explode( ',', $attributes['attached_post'] );
			}

			if ( isset( $attributes['amount'] ) && '' !== $attributes['amount'] && isset( $attributes['attached_post'] ) && '' === $attributes['attached_post'] ) {
				$args['posts_per_page'] = $attributes['amount'];
			}

			break;
		case 'grid_shortcode':

			$args['post_type'] = isset( $attributes['cptui_posttype'] ) ? $attributes['cptui_posttype'] : 'post';
			$args['posts_per_page'] = isset( $attributes['amount'] ) ? esc_attr( $attributes['amount'] ) : '';

			if ( isset( $attributes['attached_post'] ) && '' !== $attributes['attached_post'] ) {
				$args['post__in'] = explode( ',', $attributes['attached_post'] );
			}

			$args['paged'] = $paged;

			break;
		case 'woo_product_shortcode':

			if ( isset( $attributes['attached_product'] ) ) {
				$args['post_type'] = 'product';

				if ( isset( $attributes['attached_product'] ) && '' !== $attributes['attached_product'] ) {
					$args['post__in'] = explode( ',', $attributes['attached_product'] );
				}
			}

			$args['paged'] = $paged;

			break;
		case 'post_cards_shortcode':

			$args['post_type'] = isset( $attributes['cptui_posttype'] ) ? $attributes['cptui_posttype'] : 'post';
			$args['posts_per_page'] = isset( $attributes['amount'] ) ? esc_attr( $attributes['amount'] ) : '';

			if ( isset( $attributes['attached_post'] ) && '' !== $attributes['attached_post'] ) {
				$args['post__in'] = explode( ',', $attributes['attached_post'] );
			}

			$args['paged'] = $paged;

			break;
		case 'edd_download_shortcode':

			if ( isset( $attributes['attached_download'] ) ) {
				$args['post_type'] = 'download';

				if ( '' !== $attributes['attached_download'] ) {
					$args['post__in'] = explode( ',', $attributes['attached_download'] );
				}
			}

			$args['paged'] = $paged;

			break;
		case 'slider_shortcode':

			$args['post_type'] = isset( $attributes['cptui_posttype'] ) ? $attributes['cptui_posttype'] : 'post';

			if ( isset( $attributes['attached_post'] ) ) {
				if ( '' !== $attributes['attached_post'] ) {
					$args['post__in'] = explode( ',', $attributes['attached_post'] );
				}
			}

			$args['posts_per_page'] = isset( $attributes['amount'] ) ? esc_attr( $attributes['amount'] ) : '';

			break;
	}

	$args['ignore_sticky_posts'] = true;

	if ( isset( $attributes['order_by'] ) && 'none' !== $attributes['order_by'] ) {
		$args['orderby'] = $attributes['order_by'];
	}
	if ( isset( $attributes['order'] ) && 'DESC' !== $attributes['order'] ) {
		$args['order'] = $attributes['order'];
	}

	if ( isset( $attributes['tax_query'] ) && '' !== $attributes['tax_query'] ) {
		$args['tax_query'] = $attributes['tax_query'];
	}

	if ( isset( $attributes['tax_query'] ) && '' !== $attributes['tax_query'] ) {
		$args['tax_query'] = $attributes['tax_query'];
	}

	return $args;
}
add_filter( 'cptui_filter_query', 'cptui_filter_shortcode_query', 10, 2 );

/**
 * Filters cmb2 search posts field query for published content.
 *
 * @param  object $query pre get post object.
 * @return void
 */
function cptui_filter_search_post_query( $query ) {
	$query->set( 'post_status', array( 'publish' ) );
}
add_action( 'search_set_post_type', 'cptui_filter_search_post_query' );

/**
 * Displays paginated links.
 *
 * @param mixed $query post object.
 * @param mixed $attributes shortcode attributes.
 * @return void
 */
function cptui_pagination_links( $query, $attributes ) {
	echo cptui_get_pagination_links( $query, $attributes );
}

/**
 * Returns paginated links.
 *
 * @param mixed $query post object.
 * @param mixed $attributes shortcode attributes.
 * @return string
 */
function cptui_get_pagination_links( $query, $attributes ) {

	$paged = ( isset( $_GET[ $attributes['cptui_shortcode_id'] . '-page' ] ) ) ? $_GET[ $attributes['cptui_shortcode_id'] . '-page' ] : 1;

	$pag_args = array(
		$attributes['cptui_shortcode_id'] . '-page' => '%#%',
	);

	if ( defined( 'DOING_AJAX' ) && true === (bool) DOING_AJAX ) {
		$base = remove_query_arg( 's', wp_get_referer() );
	} else {
		$base = '';
	}

	$paginate = paginate_links( array(
		'base'      => add_query_arg( $pag_args, $base ),
		'format'    => '',
		'total'     => ceil( (int) $query->found_posts / (int) $query->query_vars['posts_per_page'] ),
		'current'   => $paged,
		'prev_text' => _x( '&larr;', 'cptui shortcode pagination previous text', 'cptuiext' ),
		'next_text' => _x( '&rarr;', 'cptui shortcode pagination next text', 'cptui' ),
	) );

	if ( $paginate ) {
		return '<div class="pagination">' . $paginate . '</div>';
	}
	return '';
}

/**
 * Wrapper for post_class to filter each shorcode classes.
 *
 * @param string $class post class.
 * @param array  $attributes shortcode attributes.
 * @return void
 */
function cptui_post_class( $class = '', $attributes = array() ) {
	$post_class = implode( ' ', get_post_class( array( $class ) ) );
	/**
	 * Filters get_post_class for each shortrcode.
	 *
	 * @param string $post_class post class.
	 * @param array $attributes shortcode attributes.
	 */
	echo 'class="' . esc_attr( apply_filters( 'cptui_post_class', $post_class, $attributes ) ) . '"';
}

/**
 * Escaping of shortcode title attribute
 *
 * @param string $title title attribute.
 */
function cptui_shortcode_title( $title = '' ) {
	echo esc_html( urldecode( $title ) );
}

/**
 * Cptui_slider_config description.
 *
 * @param array $attributes shortcode attributes.
 * @return string slick slider data attributes
 */
function cptui_slider_config( $attributes = array() ) {
	$dots = ( isset( $attributes['show_bullets'] ) && 'on' === $attributes['show_bullets'] ) ? true : false;
	$autoplay = ( isset( $attributes['autoplay'] ) && 'on' === $attributes['autoplay'] ) ? true : false;

	$args = array(
		'dots' => $dots,
		'autoplay' => $autoplay,
	);

	return wp_json_encode( $args );
}

/**
 * Return array of custom post type slugs for non-built in public post types.
 *
 * @since 1.3.0
 *
 * @return array
 */
function cptuiext_get_post_type_slugs() {
	return array_values( get_post_types( array( '_builtin' => false, 'public' => true ) ) );
}
