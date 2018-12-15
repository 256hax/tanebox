<?php
/**
 * CPTUI Extended Taxonomies.
 *
 * @package CPTUIExtended
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Filter network taxonomies.
 *
 * @internal
 *
 * @param boolean $filter   To filter or not.
 * @param array   $taxonomy Array of registered taxonomies.
 * @param array   $data     Taxonomy form data.
 * @return boolean
 */
function cptui_network_filter_taxonomy_update_save( $filter = false, $taxonomy = array(), $data = array() ) {

	if ( is_multisite() && is_network_admin() ) {
		$network_taxonomy = get_site_option( 'cptui_network_taxonomies' );
		$network_taxonomy[ $data['cpt_custom_tax']['name'] ] = $taxonomy[ $data['cpt_custom_tax']['name'] ];
		update_site_option( 'cptui_network_taxonomies', $network_taxonomy );
		$time = current_time( 'timestamp', 0 );
		update_site_option( 'cptui_network_taxonomy_ts', $time );
		set_site_transient( 'cptui_network_taxonomy_ts', $time, 0 );
		$filter = true;
	} else {
		$taxonomies = get_option( 'cptui_taxonomies' );
		$taxonomies[ $data['cpt_custom_tax']['name'] ] = $taxonomy[ $data['cpt_custom_tax']['name'] ];
		update_option( 'cptui_taxonomies', $taxonomies );
		$filter = true;
	}
	return $filter;
}
add_filter( 'cptui_taxonomy_update_save', 'cptui_network_filter_taxonomy_update_save', 10,3 );

/**
 * Filteres taxonomy delete if in the network.
 *
 * @internal
 *
 * @param boolean $filter     If true filters taxonomy delete.
 * @param array   $taxonomies Site taxonomies.
 * @param array   $data       Current taxonomy data.
 * @return boolean
 */
function cptui_network_filter_taxonomy_update_delete( $filter = false, $taxonomies = array(), $data = array() ) {

	if ( is_multisite() && is_network_admin() && isset( $data['cpt_custom_tax'] ) ) {
		$deleted_tax = get_site_option( 'cptui_network_deleted_taxonomies' ) ? get_site_option( 'cptui_network_deleted_taxonomies' ) : array();
		$deleted_tax[ $data['cpt_custom_tax']['name'] ] = $data['cpt_custom_tax']['name'];
		update_site_option( 'cptui_network_deleted_taxonomies', $deleted_tax );

		update_site_option( 'cptui_network_taxonomies', $taxonomies );
		$time = current_time( 'timestamp', 0 );
		update_site_option( 'cptui_network_taxonomy_ts', $time );
		delete_site_transient( 'cptui_network_taxonomy_ts' );
		$filter = true;
	}
	return $filter;
}
add_filter( 'cptui_taxonomy_delete_tax', 'cptui_network_filter_taxonomy_update_delete', 10, 3 );

/**
 * Filters the data to import for network-level post types.
 *
 * @since 1.4.0
 *
 * @param bool   $filter   Whether or not we intercepted.
 * @param string $postdata JSON-posted taxonomy data to import.
 * @return bool
 */
function cptui_network_filter_taxonomy_import( $filter = false, $postdata = '' ) {
	if ( ! cptui_extended()->is_network_activated() ) {
		return false;
	}

	if ( ! is_network_admin() ) {
		return false;
	}

	$tax_data = stripslashes_deep( trim( $postdata['cptui_tax_import'] ) );
	$settings = json_decode( $tax_data, true );

	$time = current_time( 'timestamp', 0 );
	update_site_option( 'cptui_network_taxonomy_ts', $time );
	set_site_transient( 'cptui_network_taxonomy_ts', $time, 0 );

	return update_site_option( 'cptui_network_taxonomies', $settings );
}

add_filter( 'cptui_taxonomy_import_update_save', 'cptui_network_filter_taxonomy_import', 10, 2 );

/**
 * Checks if network taxonomy exists.
 *
 * @param string $slug Taxonomy slug.
 * @return boolean If taxonomy slug exists
 */
function cptui_network_taxonomy_exists( $slug ) {

	$network_taxonomies = get_site_option( 'cptui_network_taxonomies' );

	if ( array_key_exists( $slug, $network_taxonomies ) ) {
		return true;
	}
	return false;
}

/**
 * Filters taxonomy exists.
 *
 * @internal
 *
 * @param boolean $filter Default boolean.
 * @param string  $slug   Taxonomy slug.
 * @return boolean If taxonomy slug exists
 */
function cptui_filter_taxonomy_exists( $filter, $slug ) {

	if ( is_multisite() && is_network_admin() ) {
		return cptui_network_taxonomy_exists( $slug );
	}
	return $filter;
}
add_filter( 'cptui_taxonomy_slug_exists', 'cptui_filter_taxonomy_exists', 10, 2 );


/**
 * Filter network taxonomies.
 *
 * @internal
 *
 * @param array   $taxonomies      Unfilters taxonomies.
 * @param integer $current_blog_id Current blog id.
 * @return array
 */
function cptui_network_taxonomy_dropdown_switch_data( $taxonomies = array(), $current_blog_id ) {

	if ( cptui_extended()->is_network_activated() ) {

		if ( is_multisite() && is_network_admin() ) {
			$taxonomies = get_site_option( 'cptui_network_taxonomies', array() );
		} else {

			$network_taxonomies = get_site_option( 'cptui_network_taxonomies', array() );
			$taxonomies = get_option( 'cptui_taxonomies', array() );

			foreach ( $network_taxonomies as $key ) {
				unset( $taxonomies[ $key['name'] ] );
			}
		}
	}

	return $taxonomies;
}
add_filter( 'cptui_get_taxonomy_data', 'cptui_network_taxonomy_dropdown_switch_data', 10, 2 );

/**
 * Filters taxaonomy post types.
 *
 * @internal
 *
 * @param array $post_types Unfiltered array of post types.
 * @return array filterd array of post types
 */
function cptui_network_taxonomy_post_type_switch_data( $post_types ) {

	if ( cptui_extended()->is_network_activated() ) {

		$network_post_types = get_site_option( 'cptui_network_post_types', array() );

		if ( is_multisite() && is_network_admin() ) {
			$post_types = array();
			// Convert array to object.
			$the_post_types = json_decode( wp_json_encode( $network_post_types ), false );
			foreach ( array( 'post', 'page', 'attachment' ) as $type ) {
				$post_types[ $type ] = get_post_type_object( $type );
			}

			foreach( $the_post_types as $type => $object ) {
				$post_types[ $type ] = get_post_type_object( $type );
			}
		} else {
			foreach ( $network_post_types as $key ) {
				unset( $post_types[ $key['name'] ] );
			}
		}
	}

	/**
	 * Filters the taxonomies to add to the editor screen dropdown for the network CPTUI editors.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post_types Array of post types to add to the drowdown.
	 */
	return apply_filters( 'cptui_network_taxonomy_post_type_switch_data', $post_types );
}
add_filter( 'cptui_get_post_types_for_taxonomies', 'cptui_network_taxonomy_post_type_switch_data', 10, 2 );



/**
 * Add network taxonimes to single site on admin init.
 *
 * @internal
 *
 * @return void
 */
function cptui_network_single_site_taxonomy_init() {

	if ( cptui_is_taxonomy_updated() && is_multisite() && is_super_admin() && ! is_network_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

		$deleted_tax        = get_site_option( 'cptui_network_deleted_taxonomies' );
		$network_taxonomies = get_site_option( 'cptui_network_taxonomies', array() );
		$taxonomies         = cptui_get_taxonomy_data() ? cptui_get_taxonomy_data() : array();

		$diff = array_diff_key( $network_taxonomies, $taxonomies );

		if ( ! empty( $diff ) ) {
			foreach ( $diff as $key => $value ) {
				$taxonomies[ $diff[ $key ]['name'] ] = $diff[ $key ];
			}
		}

		// Remove deleted network cpts from options array.
		if ( ! empty( $deleted_tax ) ) {
			foreach ( $deleted_tax as $tax ) {
				unset( $taxonomies[ $tax ] );
			}
		}

		/**
		 * Filters whether or not network taxonomies were saved successfully.
		 *
		 * @since 1.0.0
		 *
		 * @param bool  $value      Whether or not someone else saved successfully. Default false.
		 * @param array $taxonomies Array of our updated taxonomies data.
		 * @param array $data       Array of submitted taxonomy to update.
		 */
		if ( false === ( $success = apply_filters( 'cptui_network_single_site_taxonomy_init', false, $taxonomies ) ) ) {
			update_option( 'cptui_taxonomies', $taxonomies );
			$network_ts = get_site_option( 'cptui_network_taxonomy_ts' );
			update_option( 'cptui_taxonomy_ts', $network_ts );
		}
		$time = current_time( 'timestamp', 0 );
		set_site_transient( 'cptui_rewrite_refresh', $time, 0 );
	}
}
add_action( 'init', 'cptui_network_single_site_taxonomy_init', 8 );

/**
 * Checks if network taxonomies have been updated
 *
 * @return boolean
 */
function cptui_is_taxonomy_updated() {

	if ( ! cptui_is_network_activated() ) {
		return false;
	}

	if ( false === ( $network_ts = get_site_transient( 'cptui_network_taxonomy_ts' ) ) ) {
		$network_ts = get_site_option( 'cptui_network_taxonomy_ts', current_time( 'timestamp' ) );
		set_site_transient( 'cptui_network_taxonomy_ts', $network_ts, 0 );
	}
	$site_ts = get_option( 'cptui_taxonomy_ts', 0 );

	if ( absint( $site_ts ) !==  absint( $network_ts ) ) {
		return true;
	}
	return false;
}

/**
 * Returns boolean based on if taxonomy slug in saved options array
 *
 * @param  string $slug taxonomy slug.
 * @return boolean
 */
function cptui_is_taxonomy_deleted( $slug ) {

	$deleted_tax = get_site_option( 'cptui_network_deleted_taxonomies', array() );

	if ( array_key_exists( $slug, $deleted_tax ) ) {
		return true;
	}
	return false;
}

/**
 * Removes taxonomy slug from saved options array
 *
 * @param  string $slug taxonomy slug.
 * @return void
 */
function cptui_trash_deleted_taxonomy( $slug ) {

	$deleted_tax = get_site_option( 'cptui_network_deleted_taxonomies', array() );

	if ( array_key_exists( $slug, $deleted_tax ) ) {
		unset( $deleted_tax[ $slug ] );
		update_site_option( 'cptui_network_deleted_taxonomies', $deleted_tax );
	}

}
