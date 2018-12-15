<?php
/**
 * CPTUI Extended Post List Columns
 *
 * @package CPTUIExtended
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Filter custom colums to each cptui cpt.
 *
 * @internal
 */
function cptui_add_post_columns_filter() {

	/**
	 * Filters the CPTUI post types option before being used for post columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value Array of CPTUI-registered post types.
	 */
	$post_types = apply_filters( 'cptui_add_post_columns_filter', get_option( 'cptui_post_types' ) );

	if ( ! empty( $post_types ) ) {
		foreach ( $post_types as $type ) {
			add_filter( 'manage_' . $type['name'] . '_posts_columns', 'cptui_set_custom_edit_columns' );

			if ( in_array( 'thumbnail', $post_types[ $type['name'] ]['supports'], true ) ) {
				add_filter( 'manage_' . $type['name'] . '_posts_columns', 'cptui_set_custom_featured_columns' );
			}
		}
	}
}
add_action( 'admin_init', 'cptui_add_post_columns_filter' );

/**
 * Add coulmns to each cptui cpt.
 *
 * @internal
 *
 * @param array $columns post list columns.
 * @return array $columns Array of columns to add.
 */
function cptui_set_custom_edit_columns( $columns ) {

	$columns['id']     = __( 'ID', 'cptuiext' );
	$columns['author'] = __( 'Author', 'cptuiext' );

	return $columns;
}

/**
 * Add featured image columns to each cptui cpt.
 *
 * @internal
 *
 * @param array $columns post list columns.
 * @return array $columns Array of columns to add.
 */
function cptui_set_custom_featured_columns( $columns ) {

	$columns['featured_image'] = __( 'Featured Image', 'cptuiext' );

	return $columns;
}

/**
 * Content of custom cptui post columns.
 *
 * @internal
 *
 * @param string  $column  Column title.
 * @param integer $post_id Post id of post item.
 */
function cptui_custom_columns( $column, $post_id ) {

	switch ( $column ) {
		case 'id':
			esc_attr_e( $post_id );
		break;
		case 'author':
			esc_attr_e( get_post_field( 'post_author', $post_id ) );
		break;
		case 'featured_image':
			if ( has_post_thumbnail( $post_id ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'single-post-thumbnail' );
				echo '<div id="custom-bg" style="background-image: url('. esc_url( $image[0] ) .'); width: 75px; height: 75px; background-size: cover;">';
			}
		break;
	}
}
add_action( 'manage_posts_custom_column' , 'cptui_custom_columns', 10, 2 );
