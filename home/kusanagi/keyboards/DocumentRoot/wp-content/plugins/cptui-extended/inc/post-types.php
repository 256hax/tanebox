<?php
/**
 * CPTUI Extended Post Types
 *
 * @package CPTUIExtended
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Filters cptui save to network options.
 *
 * @internal
 *
 * @param boolean $filter to filter or not.
 * @param array   $post_types post types data.
 * @param array   $data current post type data.
 * @return boolean
 */
function cptui_network_filter_post_type_update_save( $filter = false, $post_types = array(), $data = array() ) {

	if ( is_multisite() && is_network_admin() && isset( $data['cpt_custom_post_type'] ) && '1' === $data['cpt_custom_post_type']['network_wide'] ) {
		$network_post_types = get_site_option( 'cptui_network_post_types' );
		$network_post_types[ $data['cpt_custom_post_type']['name'] ] = $post_types[ $data['cpt_custom_post_type']['name'] ];

		if ( isset( $data['cpt_to_post_query'] ) && '1' === $data['cpt_to_post_query'][0] ) {
			$network_post_types[ $data['cpt_custom_post_type']['name'] ]['cpt_to_post_query'] = $data['cpt_to_post_query'];
		}

		if ( isset( $data['cpt_to_category_archive'] ) && '1' === $data['cpt_to_category_archive'][0] ) {
			$network_post_types[ $data['cpt_custom_post_type']['name'] ]['cpt_to_category_archive'] = $data['cpt_to_category_archive'];
		}

		if ( isset( $data['cpt_to_tag_archive'] ) && '1' === $data['cpt_to_tag_archive'][0] ) {
			$network_post_types[ $data['cpt_custom_post_type']['name'] ]['cpt_to_tag_archive'] = $data['cpt_to_tag_archive'];
		}

		if ( isset( $data['google_amp_support'] ) && '1' === $data['google_amp_support'][0] ) {
			$network_post_types[ $data['cpt_custom_post_type']['name'] ]['google_amp_support'] = $data['google_amp_support'];
		}

		update_site_option( 'cptui_network_post_types', $network_post_types );
		cptui_trash_deleted_post_type( $data['cpt_custom_post_type']['name'] );

		$time = current_time( 'timestamp', 0 );
		update_site_option( 'cptui_network_post_type_ts', $time );
		set_site_transient( 'cptui_network_post_type_ts', $time, 0 );
		$filter = true;
	} else {
		$saved_post_types = get_option( 'cptui_post_types' );
		$saved_post_types[ $data['cpt_custom_post_type']['name'] ] = $post_types[ $data['cpt_custom_post_type']['name'] ];

		if ( isset( $data['cpt_to_post_query'] ) && '1' === $data['cpt_to_post_query'][0] ) {
			$saved_post_types[ $data['cpt_custom_post_type']['name'] ]['cpt_to_post_query'] = $data['cpt_to_post_query'];
		}

		if ( isset( $data['cpt_to_category_archive'] ) && '1' === $data['cpt_to_category_archive'][0] ) {
			$saved_post_types[ $data['cpt_custom_post_type']['name'] ]['cpt_to_category_archive'] = $data['cpt_to_category_archive'];
		}

		if ( isset( $data['cpt_to_tag_archive'] ) && '1' === $data['cpt_to_tag_archive'][0] ) {
			$saved_post_types[ $data['cpt_custom_post_type']['name'] ]['cpt_to_tag_archive'] = $data['cpt_to_tag_archive'];
		}

		if ( isset( $data['google_amp_support'] ) && '1' === $data['google_amp_support'][0] ) {
			$saved_post_types[ $data['cpt_custom_post_type']['name'] ]['google_amp_support'] = $data['google_amp_support'];
		}

		if ( isset( $data['divi_builder_support'] ) && '1' === $data['divi_builder_support'][0] ) {
			$saved_post_types[ $data['cpt_custom_post_type']['name'] ]['divi_builder_support'] = $data['divi_builder_support'];
		}

		if ( isset( $data['rss_support'] ) && '1' === $data['rss_support'][0] ) {
			$saved_post_types[ $data['cpt_custom_post_type']['name'] ]['rss_support'] = $data['rss_support'];
		}

		update_option( 'cptui_post_types', $saved_post_types );
		$filter = true;
	}

	return $filter;
}
add_filter( 'cptui_post_type_update_save', 'cptui_network_filter_post_type_update_save', 10,3 );

/**
 * Filter cptui options when in network.
 *
 * @internal
 *
 * @param boolean $filter if should filter.
 * @param array   $post_types posts types.
 * @param array   $data current post type data.
 * @return boolean
 */
function cptui_network_filter_post_type_update_delete( $filter = false, $post_types = array(), $data = array() ) {

	if ( is_multisite() && is_network_admin() && isset( $data['cpt_custom_post_type'] ) ) {

		$deleted_cpts = get_site_option( 'cptui_network_deleted_post_types' ) ? get_site_option( 'cptui_network_deleted_post_types' ) : array();
		$deleted_cpts[ $data['cpt_custom_post_type']['name'] ] = $data['cpt_custom_post_type']['name'];
		update_site_option( 'cptui_network_deleted_post_types', $deleted_cpts );

		update_site_option( 'cptui_network_post_types', $post_types );
		$time = current_time( 'timestamp', 0 );
		update_site_option( 'cptui_network_post_type_ts', $time );
		delete_site_transient( 'cptui_network_post_type_ts' );
		return true;
	}
	return $filter;

}
add_filter( 'cptui_post_type_delete_type', 'cptui_network_filter_post_type_update_delete', 10, 3 );

/**
 * Filters the data to import for network-level post types.
 *
 * @since 1.4.0
 *
 * @param bool   $filter   Whether or not we intercepted.
 * @param string $postdata JSON-posted post type data to import.
 * @return bool
 */
function cptui_network_filter_post_type_import( $filter = false, $postdata = '' ) {
	if ( ! cptui_extended()->is_network_activated() ) {
		return false;
	}

	if ( ! is_network_admin() ) {
		return false;
	}

	$cpt_data = stripslashes_deep( trim( $postdata['cptui_post_import'] ) );
	$settings = json_decode( $cpt_data, true );

	$time = current_time( 'timestamp', 0 );
	update_site_option( 'cptui_network_post_type_ts', $time );
	set_site_transient( 'cptui_network_post_type_ts', $time, 0 );

	return update_site_option( 'cptui_network_post_types', $settings );
}
add_filter( 'cptui_post_type_import_update_save', 'cptui_network_filter_post_type_import', 10, 2 );

/**
 * Checks if network post type exists.
 *
 * @since unknown
 *
 * @param string $slug post type slug.
 * @return boolean
 */
function cptui_network_post_type_exists( $slug ) {

	$network_post_types = get_site_option( 'cptui_network_post_types' );

	if ( array_key_exists( $slug, $network_post_types ) ) {
		return true;
	}
	return false;
}

/**
 * Filters if network post type exists.
 *
 * @internal
 *
 * @param boolean $filter     To filter or not (this is not a question).
 * @param array   $data       Post type data.
 * @param array   $post_types Array of post types.
 * @return boolean if post type exist then filter
 */
function cptui_filter_post_type_exists( $filter = false, $data = array(), $post_types = array() ) {

	if ( is_multisite() && is_network_admin() ) {
		if ( cptui_network_post_type_exists( $data['cpt_custom_post_type']['name'] ) ) {
			return true;
		}
	}
	return $filter;
}
add_filter( 'cptui_get_post_type_exists', 'cptui_filter_post_type_exists', 100, 3 );

/**
 * Filters the drop down CPT select in network admin.
 *
 * @internal
 *
 * @param array   $post_types       Saved cpts.
 * @param integer $current_blog_id  Current blog id.
 * @return array network cpts.
 */
function cptui_network_post_type_dropdown_switch_data( $post_types = array(), $current_blog_id ) {

	if ( cptui_extended()->is_network_activated() ) {

		if ( is_multisite() && is_network_admin() ) {
			$post_types = get_site_option( 'cptui_network_post_types', array() );
		} else {

			$network_post_types = get_site_option( 'cptui_network_post_types', array() );
			$post_types = get_option( 'cptui_post_types', array() );

			foreach ( $network_post_types as $key ) {
				unset( $post_types[ $key['name'] ] );
			}
		}
	}

	/**
	 * Filters the post types to add to the editor screen dropdown for the network CPTUI editors.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post_types Array of post types to add to the drowdown.
	 */
	return apply_filters( 'cptui_network_post_type_dropdown_switch_data', $post_types );
}
add_filter( 'cptui_get_post_type_data', 'cptui_network_post_type_dropdown_switch_data', 10, 2 );

/**
 * Add network CPTs to single site.
 *
 * @internal
 */
function cptui_network_single_site_post_type_init() {

	if ( ! is_admin() ) {
		return;
	}

	if ( cptui_is_post_type_updated() && is_multisite() && is_super_admin() && ! is_network_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

		$deleted_cpts       = get_site_option( 'cptui_network_deleted_post_types' );
		$network_post_types = get_site_option( 'cptui_network_post_types', array() );
		$post_types         = cptui_get_post_type_data() ? cptui_get_post_type_data() : array();
		$diff               = array_diff_key( $network_post_types, $post_types );

		// Add network cpts to options array.
		if ( ! empty( $diff ) ) {
			foreach ( $diff as $key => $value ) {
				$post_types[ $diff[ $key ]['name'] ] = $diff[ $key ];
			}
		}

		// Remove deleted network cpts from options array.
		if ( ! empty( $deleted_cpts ) ) {
			foreach ( $deleted_cpts as $cpt ) {
				unset( $post_types[ $cpt ] );
			}
		}

		/**
		 * Filters whether or not network cpts were saved successfully.
		 *
		 * @since 1.0.0
		 *
		 * @param bool  $value      Whether or not someone else saved successfully. Default false.
		 * @param array $post_types Array of our updated post types data.
		 */
		if ( false === ( $success = apply_filters( 'cptui_network_single_site_post_type_init', false, $post_types ) ) ) {
			update_option( 'cptui_post_types', $post_types );
			update_option( 'cptui_post_type_ts', get_site_option( 'cptui_network_post_type_ts' ) );
		}
		// The current_time() is a WP function, not from PHP core.
		$time = current_time( 'timestamp' );
		set_site_transient( 'cptui_rewrite_refresh', $time, 0 );
	}
}
add_action( 'init', 'cptui_network_single_site_post_type_init', 8 );

/**
 * Returns boolean based on if post type slug in saved options array.
 *
 * @return boolean
 */
function cptui_is_post_type_updated() {

	if ( ! cptui_is_network_activated() ) {
		return false;
	}

	if ( false === ( $network_ts = get_site_transient( 'cptui_network_post_type_ts' ) ) ) {

		$network_ts   = current_time( 'timestamp', 0 );
		$post_type_ts = get_site_option( 'cptui_network_post_type_ts' );

		if ( $post_type_ts ) {
			$network_ts = $post_type_ts;
		}

		set_site_transient( 'cptui_network_post_type_ts', $network_ts, 0 );
	}
	$site_ts = get_option( 'cptui_post_type_ts', 0 );

	if ( absint( $site_ts ) !==  absint( $network_ts ) ) {
		return true;
	}
	return false;
}

/**
 * Is post type set for deletion.
 *
 * @param string $slug post type splug.
 * @return boolean  if post type slug in array
 */
function cptui_is_post_type_deleted( $slug = '' ) {

	$deleted_cpts = get_site_option( 'cptui_network_deleted_post_types', array() );

	if ( array_key_exists( $slug, $deleted_cpts ) ) {
		return true;
	}
	return false;
}

/**
 * Removes post type slug from saved options array.
 *
 * @param string $slug post type slug.
 */
function cptui_trash_deleted_post_type( $slug ) {

	$deleted_cpts = get_site_option( 'cptui_network_deleted_post_types', array() );

	if ( array_key_exists( $slug, $deleted_cpts ) ) {
		unset( $deleted_cpts[ $slug ] );
		update_site_option( 'cptui_network_deleted_post_types', $deleted_cpts );
	}

}

/**
 * Add network cpts to reserved so they cant be added to single site.
 *
 * @internal
 *
 * @param array $cpts CPT slugs.
 * @return array $cpts Newly merged CPT array.
 */
function cptui_add_network_cpts_to_reserved( $cpts ) {
	if ( is_multisite() && ! is_network_admin() ) {
		$network_post_types = get_site_option( 'cptui_network_post_types', array() );
		$network_cpts = array();
		foreach ( $network_post_types as $post_type ) {
			$network_cpts[ $post_type['name'] ] = $post_type['name'];
		}
		$cpts = array_merge( $cpts, $network_cpts );
	}
	return $cpts;
}
add_filter( 'cptui_reserved_post_types', 'cptui_add_network_cpts_to_reserved' );
