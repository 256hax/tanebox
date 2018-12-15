<?php
/**
 * Default Shortcodes for CPTUI Extended.
 *
 * Registers all of the shortcodes provided by CPTUIExtended out of box.
 *
 * @package CPTUIExtended
 * @author  Pluginize
 * @license GPLV2
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Shortcodes
 *
 * @internal
 *
 * @return void
 */
function cptui_register_core_shortcodes() {

	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	cptui_register_shortcode( 'cptui_default_shortcode' );
	cptui_register_shortcode( 'cptui_single_page_shortcode' );
	cptui_register_shortcode( 'cptui_single_post_shortcode' );
	cptui_register_shortcode( 'cptui_single_post_type_shortcode' );
	cptui_register_shortcode( 'cptui_list_post_shortcode' );
	cptui_register_shortcode( 'cptui_taxononmy_list_shortcode' );
	cptui_register_shortcode( 'cptui_slider_shortcode' );
	cptui_register_shortcode( 'cptui_post_cards_shortcode' );
	cptui_register_shortcode( 'cptui_grid_shortcode' );
	cptui_register_shortcode( 'cptui_grid_with_overlay_shortcode' );
	cptui_register_shortcode( 'cptui_featured_plus_shortcode' );

	/**
	 * Check if WooCommerce is active
	 */
	if ( is_plugin_active( 'woocommerce/woocommerce.php' )
		|| is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
		cptui_register_shortcode( 'cptui_woo_product_shortcode' );
	}

	/**
	 * Check if Easy Digital Downloads is active
	 */
	if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' )
		|| is_plugin_active_for_network( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
		cptui_register_shortcode( 'cptui_edd_download_shortcode' );
	}

}
add_action( 'cptuiext_loaded', 'cptui_register_core_shortcodes', 5 );

/**
 * Default shortcode.
 *
 * @internal
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_default_shortcode() {

	$shortcode = array(
		'id'                => 'default_shortcode',
		'name'              => esc_html__( 'Default', 'cptuiext' ),
		'template'          => 'posts',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'        => esc_html__( 'Featured Image', 'cptuiext' ),
				'description' => esc_html__( 'Would you like a featured image', 'cptuiext' ),
				'id'          => 'featured_image',
				'type'        => 'checkbox',
			),
			array(
				'name'            => esc_html__( 'Choose Posts', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'attached_post',
				'type'            => 'post_search_text',
				'post_type'       => 'post',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'             => esc_html__( 'Order By', 'cptuiext' ),
				'id'               => 'order_by',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => array(
					'date'  => esc_html__( 'Post Date', 'cptuiext' ),
					'id'    => esc_html__( 'Post ID', 'cptuiext' ),
					'title' => esc_html__( 'Title', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Order', 'cptuiext' ),
				'id'      => 'order',
				'type'    => 'radio',
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'cptuiext' ),
					'DESC' => esc_html__( 'Descending', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Taxonomy', 'cptuiext' ),
				'id'      => 'taxonomy',
				'type'    => 'multicheck',
				'desc'    => esc_html__( 'Checking terms will limit posts to those that have chosen terms.', 'cptuiext' ),
				'options' => array(
					'none' => 'None',
				),
				'before'  => 'cptui_taxonomy_set_field_data_attr',
			),
			array(
				'name'        => esc_html__( 'Amount', 'cptuiext' ),
				'description' => esc_html__( 'Enter a number for the amount of posts to list. "Choose Posts" field overides this option.', 'cptuiext' ),
				'id'          => 'amount',
				'type'        => 'text',
			),
		),
	);

	/**
	 * Filters the default shortcode array data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $shortcode Array of data for the default shortcode.
	 */
	return apply_filters( 'cptui_default_shortcode', $shortcode );

}

/**
 * Single page shortcode.
 *
 * @internal
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_single_page_shortcode() {

	$shortcode = array(
		'id'                => 'single_page_shortcode',
		'name'              => esc_html__( 'Single Page', 'cptuiext' ),
		'template'          => 'page',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'page',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode.', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'             => esc_html__( 'Page', 'cptuiext' ),
				'description'      => esc_html__( 'select page.', 'cptuiext' ),
				'id'               => 'page_id',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => cptui_get_pages(),
			),
			array(
				'name'        => esc_html__( 'Featured Image', 'cptuiext' ),
				'description' => esc_html__( 'Would you like a featured image', 'cptuiext' ),
				'id'          => 'featured_image',
				'type'        => 'checkbox',
			),
		),
	);

	return apply_filters( 'single_page_shortcode', $shortcode );

}

/**
 * Single post shortcode.
 *
 * @internal
 *
 * @return array Shortcode data including CMB2 fields.
 */
function cptui_single_post_shortcode() {

	$shortcode = array(
		'id'                => 'single_post_shortcode',
		'name'              => esc_html__( 'Single Post', 'cptuiext' ),
		'template'          => 'single',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'post',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above post', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'            => esc_html__( 'Choose Post', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'post_id',
				'type'            => 'post_search_text',
				'post_type'       => 'post',
				'select_type'     => 'radio',
				'select_behavior' => 'add',
			),
			array(
				'name'        => esc_html__( 'Featured Image', 'cptuiext' ),
				'description' => esc_html__( 'Would you like a featured image', 'cptuiext' ),
				'id'          => 'featured_image',
				'type'        => 'checkbox',
			),
		),
	);

	return apply_filters( 'single_post_shortcode', $shortcode );

}

/**
 * Single post type shortcode.
 *
 * @internal
 * @return array Shortcode data including CMB2 fields.
 */
function cptui_single_post_type_shortcode() {

	$shortcode = array(
		'id'                => 'single_post_type_shortcode',
		'name'              => esc_html__( 'Single Post Type', 'cptuiext' ),
		'template'          => 'post-type',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above post', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'            => esc_html__( 'Choose Post', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'post_id',
				'type'            => 'post_search_text',
				'post_type'       => cptuiext_get_post_type_slugs(),
				'select_type'     => 'radio',
				'select_behavior' => 'add',
			),
			array(
				'name'        => esc_html__( 'Featured Image', 'cptuiext' ),
				'description' => esc_html__( 'Would you like a featured image', 'cptuiext' ),
				'id'          => 'featured_image',
				'type'        => 'checkbox',
			),
		),
	);

	return apply_filters( 'single_post_type_shortcode', $shortcode );

}

/**
 * Cptui_list_post_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_list_post_shortcode() {

	$shortcode = array(
		'id'                => 'list_shortcode',
		'name'              => esc_html__( 'List', 'cptuiext' ),
		'template'          => 'list',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Excerpt', 'cptuiext' ),
				'description' => esc_html__( 'Would you like to show the post excerpt?', 'cptuiext' ),
				'id'          => 'excerpt',
				'type'        => 'checkbox',
			),
			array(
				'name'    => esc_html__( 'List Type', 'cptuiext' ),
				'id'      => 'list_type',
				'type'    => 'radio',
				'options' => array(
					'ul' => esc_html__( 'Unordered - Display bullet points next to each item', 'cptuiext' ),
					'ol' => esc_html__( 'Ordered - Display numbers 1, 2, 3, etc next to each item', 'cptuiext' ),
				),
			),
			array(
				'name'            => esc_html__( 'Choose Posts', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'attached_post',
				'type'            => 'post_search_text',
				'post_type'       => 'post',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'             => esc_html__( 'Order By', 'cptuiext' ),
				'id'               => 'order_by',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => array(
					'date'  => esc_html__( 'Post Date', 'cptuiext' ),
					'id'    => esc_html__( 'Post ID', 'cptuiext' ),
					'title' => esc_html__( 'Title', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Order', 'cptuiext' ),
				'id'      => 'order',
				'type'    => 'radio',
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'cptuiext' ),
					'DESC' => esc_html__( 'Descending', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Taxonomy', 'cptuiext' ),
				'id'      => 'taxonomy',
				'type'    => 'multicheck',
				'desc'    => esc_html__( 'Checking terms will limit posts to those that have chosen terms.', 'cptuiext' ),
				'options' => array(
					'none' => 'None',
				),
				'before'  => 'cptui_taxonomy_set_field_data_attr',
			),
			array(
				'name'        => esc_html__( 'Amount', 'cptuiext' ),
				'description' => esc_html__( 'Enter a number for the amount of posts to list. "Choose Posts" field overides this option.', 'cptuiext' ),
				'id'          => 'amount',
				'type'        => 'text',
			),
		),
	);

	return apply_filters( 'list_post_shortcode', $shortcode );

}

/**
 * Woo_product_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_woo_product_shortcode() {

	$shortcode = array(
		'id'                => 'woo_product_shortcode',
		'name'              => esc_html__( 'Woo Product', 'cptuiext' ),
		'template'          => 'woo',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'product',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'        => esc_html__( 'Featured Image', 'cptuiext' ),
				'description' => esc_html__( 'Would you like a featured image', 'cptuiext' ),
				'id'          => 'featured_image',
				'type'        => 'checkbox',
			),
			array(
				'name'            => esc_html__( 'Choose Product', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated product id(s) or click search icon. Default is all products.', 'cptuiext' ),
				'id'              => 'attached_product',
				'type'            => 'post_search_text',
				'post_type'       => 'product',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'             => esc_html__( 'Order By', 'cptuiext' ),
				'id'               => 'order_by',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => array(
					'date'  => esc_html__( 'Post Date', 'cptuiext' ),
					'id'    => esc_html__( 'Post ID', 'cptuiext' ),
					'title' => esc_html__( 'Title', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Order', 'cptuiext' ),
				'id'      => 'order',
				'type'    => 'radio',
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'cptuiext' ),
					'DESC' => esc_html__( 'Descending', 'cptuiext' ),
				),
			),
		),
	);

	return apply_filters( 'woo_product_shortcode', $shortcode );
}

/**
 * Edd_download_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_edd_download_shortcode() {

	$shortcode = array(
		'id'                => 'edd_download_shortcode',
		'name'              => esc_html__( 'EDD Download', 'cptuiext' ),
		'template'          => 'edd',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'download',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'        => esc_html__( 'Featured Image', 'cptuiext' ),
				'description' => esc_html__( 'Would you like a featured image', 'cptuiext' ),
				'id'          => 'featured_image',
				'type'        => 'checkbox',
			),
			array(
				'name'            => esc_html__( 'Choose Download', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated download id(s) or click search icon. Default is all downlaods.', 'cptuiext' ),
				'id'              => 'attached_download',
				'type'            => 'post_search_text',
				'post_type'       => 'download',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'             => esc_html__( 'Order By', 'cptuiext' ),
				'id'               => 'order_by',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => array(
					'date'  => esc_html__( 'Post Date', 'cptuiext' ),
					'id'    => esc_html__( 'Post ID', 'cptuiext' ),
					'title' => esc_html__( 'Title', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Order', 'cptuiext' ),
				'id'      => 'order',
				'type'    => 'radio',
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'cptuiext' ),
					'DESC' => esc_html__( 'Descending', 'cptuiext' ),
				),
			),
		),
	);

	return apply_filters( 'edd_download_shortcode', $shortcode );
}

/**
 * Cptui_taxononmy_list_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_taxononmy_list_shortcode() {

	$shortcode = array(
		'id'                => 'taxonomy_list_shortcode',
		'name'              => esc_html__( 'Taxonomy List', 'cptuiext' ),
		'template'          => 'taxonomy-list',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'    => esc_html__( 'List Type', 'cptuiext' ),
				'id'      => 'list_type',
				'type'    => 'radio',
				'options' => array(
					'ul' => esc_html__( 'Unordered - Display bullet points next to each item', 'cptuiext' ),
					'ol' => esc_html__( 'Ordered - Display numbers 1, 2, 3, etc next to each item', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Taxonomy', 'cptuiext' ),
				'id'      => 'taxonomy',
				'type'    => 'multicheck',
				'desc'    => esc_html__( 'Checking terms will limit posts to those that have chosen terms.', 'cptuiext' ),
				'options' => array(
					'none' => 'None',
				),
				'before'  => 'cptui_taxonomy_set_field_data_attr',
			),
		),
	);

	return apply_filters( 'cptui_taxononmy_list_shortcode', $shortcode );

}

/**
 * Cptui_slider_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_slider_shortcode() {

	$shortcode = array(
		'id'                => 'slider_shortcode',
		'name'              => esc_html__( 'Post Slider', 'cptuiext' ),
		'template'          => 'slider',
		'template_location' => cptui_template_path(),
		'style'             => array( 'cptui_default_css', 'cptui_slick_slider_css', 'cptui_slick_slider_theme' ),
		'script'            => array( 'cptui_slick_slider_js', 'cptui_slider_js' ),
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'            => esc_html__( 'Choose Posts', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'attached_post',
				'type'            => 'post_search_text',
				'post_type'       => 'post',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'        => esc_html__( 'Slider Height', 'cptuiext' ),
				'description' => esc_html__( 'Enter pixel height for slider. Example: 300. Default is 300 pixels tall.', 'cptuiext' ),
				'id'          => 'height',
				'type'        => 'text',
			),
			array(
				'name'        => esc_html__( 'Post Title', 'cptuiext' ),
				'description' => esc_html__( 'Show post title below slider image.', 'cptuiext' ),
				'id'          => 'show_title',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Slider Bullets', 'cptuiext' ),
				'description' => esc_html__( 'Show bullets below slider.', 'cptuiext' ),
				'id'          => 'show_bullets',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Auto Play', 'cptuiext' ),
				'description' => esc_html__( 'Auto Play the slides.', 'cptuiext' ),
				'id'          => 'autoplay',
				'type'        => 'checkbox',
			),
		),
	);

	return apply_filters( 'cptui_slider_shortcode', $shortcode );

}

/**
 * Cptui_post_cards_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_post_cards_shortcode() {

	$shortcode = array(
		'id'                => 'post_cards_shortcode',
		'name'              => esc_html__( 'Post Cards', 'cptuiext' ),
		'template'          => 'post-cards',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'             => 'Layout',
				'id'               => 'card_layout',
				'type'             => 'radio',
				'show_option_none' => false,
				'options'          => array(
					'option_1' => esc_html__( 'Option 1', 'cptuiext' ),
					'option_2' => esc_html__( 'Option 2', 'cptuiext' ),
					'option_3' => esc_html__( 'Option 3', 'cptuiext' ),
				),
			),
			array(
				'name'        => esc_html__( 'Show Thumbnail', 'cptuiext' ),
				'description' => esc_html__( 'Show post featured image thumbnail.', 'cptuiext' ),
				'id'          => 'show_thumbnail',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Title', 'cptuiext' ),
				'description' => esc_html__( 'Show post title.', 'cptuiext' ),
				'id'          => 'show_title',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Categories', 'cptuiext' ),
				'description' => esc_html__( 'Show post categories.', 'cptuiext' ),
				'id'          => 'show_categories',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Excerpt', 'cptuiext' ),
				'description' => esc_html__( 'Show post excerpt.', 'cptuiext' ),
				'id'          => 'show_excerpt',
				'type'        => 'checkbox',
			),
			array(
				'name'            => esc_html__( 'Choose Posts', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'attached_post',
				'type'            => 'post_search_text',
				'post_type'       => 'post',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'             => esc_html__( 'Order By', 'cptuiext' ),
				'id'               => 'order_by',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => array(
					'date'  => esc_html__( 'Post Date', 'cptuiext' ),
					'id'    => esc_html__( 'Post ID', 'cptuiext' ),
					'title' => esc_html__( 'Title', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Order', 'cptuiext' ),
				'id'      => 'order',
				'type'    => 'radio',
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'cptuiext' ),
					'DESC' => esc_html__( 'Descending', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Taxonomy', 'cptuiext' ),
				'id'      => 'taxonomy',
				'type'    => 'multicheck',
				'desc'    => esc_html__( 'Checking terms will limit posts to those that have chosen terms.', 'cptuiext' ),
				'options' => array(
					'none' => esc_html__( 'None', 'cptuiext' ),
				),
				'before'  => 'cptui_taxonomy_set_field_data_attr',
			),
			array(
				'name'        => esc_html__( 'Amount', 'cptuiext' ),
				'description' => esc_html__( 'Enter a number for the amount of posts to list. "Choose Posts" field overides this option.', 'cptuiext' ),
				'id'          => 'amount',
				'type'        => 'text',
			),
		),
	);

	return apply_filters( 'cptui_post_cards_shortcode', $shortcode );

}

/**
 * Cptui_grid_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_grid_shortcode() {

	$shortcode = array(
		'id'                => 'grid_shortcode',
		'name'              => esc_html__( 'Grid', 'cptuiext' ),
		'template'          => 'grid',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'        => esc_html__( 'Show Thumbnail', 'cptuiext' ),
				'description' => esc_html__( 'Show post featured image thumbnail.', 'cptuiext' ),
				'id'          => 'show_thumbnail',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Title', 'cptuiext' ),
				'description' => esc_html__( 'Show post title.', 'cptuiext' ),
				'id'          => 'show_title',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Categories', 'cptuiext' ),
				'description' => esc_html__( 'Show post categories.', 'cptuiext' ),
				'id'          => 'show_categories',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Excerpt', 'cptuiext' ),
				'description' => esc_html__( 'Show post excerpt.', 'cptuiext' ),
				'id'          => 'show_excerpt',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Read More', 'cptuiext' ),
				'description' => esc_html__( 'Show post read more link.', 'cptuiext' ),
				'id'          => 'show_read_more',
				'type'        => 'checkbox',
			),
			array(
				'name'            => esc_html__( 'Choose Posts', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'attached_post',
				'type'            => 'post_search_text',
				'post_type'       => 'post',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'             => esc_html__( 'Order By', 'cptuiext' ),
				'id'               => 'order_by',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => array(
					'date'  => esc_html__( 'Post Date', 'cptuiext' ),
					'id'    => esc_html__( 'Post ID', 'cptuiext' ),
					'title' => esc_html__( 'Title', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Order', 'cptuiext' ),
				'id'      => 'order',
				'type'    => 'radio',
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'cptuiext' ),
					'DESC' => esc_html__( 'Descending', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Taxonomy', 'cptuiext' ),
				'id'      => 'taxonomy',
				'type'    => 'multicheck',
				'desc'    => esc_html__( 'Checking terms will limit posts to those that have chosen terms.', 'cptuiext' ),
				'options' => array(
					'none' => 'None',
				),
				'before'  => 'cptui_taxonomy_set_field_data_attr',
			),
			array(
				'name'        => esc_html__( 'Amount', 'cptuiext' ),
				'description' => esc_html__( 'Enter a number for the amount of posts to list. "Choose Posts" field overides this option.', 'cptuiext' ),
				'id'          => 'amount',
				'type'        => 'text',
			),
		),
	);

	return apply_filters( 'cptui_grid_shortcode', $shortcode );

}

/**
 * Cptui_grid_with_overlay_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_grid_with_overlay_shortcode() {

	$shortcode = array(
		'id'                => 'grid_with_overlay_shortcode',
		'name'              => esc_html__( 'Grid with Overlay', 'cptuiext' ),
		'template'          => 'grid-with-overlay',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'        => esc_html__( 'Show Title', 'cptuiext' ),
				'description' => esc_html__( 'Show post title.', 'cptuiext' ),
				'id'          => 'show_title',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Post Date', 'cptuiext' ),
				'description' => esc_html__( 'Show post post date.', 'cptuiext' ),
				'id'          => 'show_post_date',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Excerpt', 'cptuiext' ),
				'description' => esc_html__( 'Show post excerpt.', 'cptuiext' ),
				'id'          => 'show_excerpt',
				'type'        => 'checkbox',
			),
			array(
				'name'        => esc_html__( 'Show Read More', 'cptuiext' ),
				'description' => esc_html__( 'Show post read more.', 'cptuiext' ),
				'id'          => 'show_read_more',
				'type'        => 'checkbox',
			),
			array(
				'name'            => esc_html__( 'Choose Posts', 'cptuiext' ),
				'description'     => esc_html__( 'Enter comma seperated post id(s) or click search icon. Default is all posts.', 'cptuiext' ),
				'id'              => 'attached_post',
				'type'            => 'post_search_text',
				'post_type'       => 'post',
				'select_type'     => 'checkbox',
				'select_behavior' => 'add',
			),
			array(
				'name'             => esc_html__( 'Order By', 'cptuiext' ),
				'id'               => 'order_by',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => array(
					'date'  => esc_html__( 'Post Date', 'cptuiext' ),
					'id'    => esc_html__( 'Post ID', 'cptuiext' ),
					'title' => esc_html__( 'Title', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Order', 'cptuiext' ),
				'id'      => 'order',
				'type'    => 'radio',
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'cptuiext' ),
					'DESC' => esc_html__( 'Descending', 'cptuiext' ),
				),
			),
			array(
				'name'    => esc_html__( 'Taxonomy', 'cptuiext' ),
				'id'      => 'taxonomy',
				'type'    => 'multicheck',
				'desc'    => esc_html__( 'Checking terms will limit posts to those that have chosen terms.', 'cptuiext' ),
				'options' => array(
					'none' => 'None',
				),
				'before'  => 'cptui_taxonomy_set_field_data_attr',
			),
			array(
				'name'        => esc_html__( 'Amount', 'cptuiext' ),
				'description' => esc_html__( 'Enter a number for the amount of posts to list. "Choose Posts" field overides this option.', 'cptuiext' ),
				'id'          => 'amount',
				'type'        => 'text',
			),
		),
	);

	return apply_filters( 'cptui_grid_with_overlay_shortcode', $shortcode );
}

/**
 * Cptui_featured_plus_shortcode shortcode
 *
 * @return array Shortcode data including CMB2 fields
 */
function cptui_featured_plus_shortcode() {
	$shortcode = array(
		'id'                => 'featured_plus_shortcode',
		'name'              => esc_html__( 'Featured Plus', 'cptuiext' ),
		'template'          => 'featured-plus',
		'template_location' => cptui_template_path(),
		'style'             => 'cptui_default_css',
		'post_type'         => 'all',
		'description'       => ' ',
		'fields'            => array(
			array(
				'name'        => esc_html__( 'Title', 'cptuiext' ),
				'description' => esc_html__( 'Title to display above shortcode', 'cptuiext' ),
				'id'          => 'title',
				'type'        => 'text',
			),
			array(
				'name'        => esc_html__( 'Featured Plus Type', 'cptuiext' ),
				'id'          => 'featured_plus_type',
				'type'        => 'radio',
				'default'     => 'left',
				'options'     => array(
					'left' => 'Featured Left',
					'top'  => 'Featured Top',
				),
				'description' => esc_html__( 'Will display the most recent post at either the left or the top.', 'cptuiext' ),
			),
			array(
				'name'        => esc_html__( 'Number of posts', 'cptuiext' ),
				'description' => esc_html__( 'Enter a number for the amount of posts to list. "Choose Posts" field overides this option.', 'cptuiext' ),
				'default'     => 5,
				'id'          => 'amount',
				'type'        => 'text',
			),
			array(
				'name'    => esc_html__( 'Taxonomy', 'cptuiext' ),
				'id'      => 'taxonomy',
				'type'    => 'multicheck',
				'desc'    => esc_html__( 'Checking terms will limit posts to those that have chosen terms.', 'cptuiext' ),
				'options' => array(
					'none' => 'None',
				),
				'before'  => 'cptui_taxonomy_set_field_data_attr',
			),
		),
	);
	return apply_filters( 'cptui_featured_plus_shortcode', $shortcode );
}

/**
 * Cptui_taxonomy_set_field_data_attr
 *
 * @param array $args  cmb2 field arguments.
 * @param array $field cmb2 field data.
 */
function cptui_taxonomy_set_field_data_attr( $args, $field ) {
	$field->args['attributes']['data-field'] = $args['id'];
}

/**
 * Register CPTUI default scripts.
 *
 * @internal
 */
function cptui_register_shortcode_scripts() {
	wp_register_style( 'cptui_default_css', cptui_extended()->url() . 'assets/css/style.css' );
	wp_register_style( 'cptui_slick_slider_css', cptui_extended()->url() . 'assets/js/slick/slick.css' );
	wp_register_style( 'cptui_slick_slider_theme', cptui_extended()->url() . 'assets/js/slick/slick-theme.css' );
	wp_register_script( 'cptui_slick_slider_js', cptui_extended()->url() . 'assets/js/slick/slick.min.js' );
	wp_register_script( 'cptui_slider_js', cptui_extended()->url() . 'assets/js/slider.js' );
}
add_action( 'wp_enqueue_scripts', 'cptui_register_shortcode_scripts' );
