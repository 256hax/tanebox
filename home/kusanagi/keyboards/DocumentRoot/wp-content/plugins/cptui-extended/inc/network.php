<?php
/**
 * CPTUI Extended Network
 *
 * @package CPTUIExtended
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Add Extended options to network admin CPTUI screen.
 *
 * @internal
 * @param object|string $ui A cptui_admin_ui instance.
 */
function cptui_network_add_extended_options( $ui = '' ) {

	$tab = ( ! empty( $_GET ) && ! empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) ? 'edit' : 'new';

	if ( 'edit' === $tab ) {
		$post_types         = cptui_get_post_type_data();
		$selected_post_type = cptui_get_current_post_type();

		if ( $selected_post_type ) {
			if ( array_key_exists( $selected_post_type, $post_types ) ) {
				$current = $post_types[ $selected_post_type ];
			}
		}
	} ?>
	<div class="cptui-section postbox">
		<button type="button" class="handlediv button-link" aria-expanded="true">
			<span class="screen-reader-text" ><?php esc_html_e( 'Toggle panel: Extended', 'cptuiext' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		<h2 class="hndle">
			<span><?php esc_html_e( 'Extended', 'cptuiext' ); ?></span>
		</h2>
		<div class="inside">
			<div class="main">
				<?php
				if ( isset( $current['description'] ) ) {
					$current['description'] = stripslashes_deep( $current['description'] );
				}
				?>
				<table class="form-table cptui-table">
				<?php

				echo $ui->get_tr_start() . $ui->get_th_start() . __( 'Add to default category archive', 'cptuiext' ) . $ui->get_th_end() . $ui->get_td_start();
				echo $ui->get_check_input( array(
					'checkvalue' => '1',
					'checked'    => ( isset( $current['cpt_to_category_archive'] ) ) ? 'true' : 'false',
					'name'       => 'cpt_to_category_archive',
					'namearray'  => 'cpt_to_category_archive',
					'textvalue'  => '1',
					'labeltext'  => esc_html__( 'Add the post entries from this post type to the default category archive.', 'cptuiext' ),
					'helptext'   => esc_attr__( 'Add the post entries from this post type to the default category archive.', 'cptuiext' ),
					'default'    => true,
					'wrap'       => false,
				) );
				echo $ui->get_td_end() . $ui->get_tr_end();

				echo $ui->get_tr_start() . $ui->get_th_start() . __( 'Add to default tag archive', 'cptuiext' ) . $ui->get_th_end() . $ui->get_td_start();
				echo $ui->get_check_input( array(
					'checkvalue' => '1',
					'checked'    => ( isset( $current['cpt_to_tag_archive'] ) ) ? 'true' : 'false',
					'name'       => 'cpt_to_tag_archive',
					'namearray'  => 'cpt_to_tag_archive',
					'textvalue'  => '1',
					'labeltext'  => esc_html__( 'Add the post entries from this post type to the default tag archive.', 'cptuiext' ),
					'helptext'   => esc_attr__( 'Add the post entries from this post type to the default tag archive.', 'cptuiext' ),
					'default'    => true,
					'wrap'       => false,
				) );
				echo $ui->get_td_end() . $ui->get_tr_end();

				echo $ui->get_tr_start() . $ui->get_th_start() . __( 'Add to default post query', 'cptuiext' ) . $ui->get_th_end() . $ui->get_td_start();

				echo $ui->get_check_input(
					array(
						'checkvalue' => '1',
						'checked'    => ( isset( $current['cpt_to_post_query'] ) ) ? 'true' : 'false',
						'name'       => 'cpt_to_post_query',
						'namearray'  => 'cpt_to_post_query',
						'textvalue'  => '1',
						'labeltext'  => esc_html__( 'Add the post entries from this CPT to the default post query (i.e. your blog)', 'cptuiext' ),
						'helptext'   => esc_attr__( 'Add the post entries from this CPT to the default post query (i.e. your blog)', 'cptuiext' ),
						'default'    => true,
						'wrap'       => false,
					)
				);

				echo $ui->get_td_end() . $ui->get_tr_end();

				echo $ui->get_tr_start() . $ui->get_th_start() . __( 'Add Google AMP support via AMP plugin.', 'cptuiext' ) . $ui->get_th_end() . $ui->get_td_start();

				echo $ui->get_check_input(
					array(
						'checkvalue' => '1',
						'checked'    => ( isset( $current['google_amp_support'] ) ) ? 'true' : 'false',
						'name'       => 'google_amp_support',
						'namearray'  => 'google_amp_support',
						'textvalue'  => '1',
						'labeltext'  => esc_html__( 'Add Google AMP to the post type. If you have AMP 0.6.0 or higher, please use their dedicated settings page.', 'cptuiext' ),
						'default'    => true,
						'wrap'       => false,
					)
				);

				echo $ui->get_td_end() . $ui->get_tr_end();

				echo $ui->get_tr_start() . $ui->get_th_start() . __( 'Add Divi Builder support.', 'cptuiext' ) . $ui->get_th_end() . $ui->get_td_start();

				echo $ui->get_check_input(
					array(
						'checkvalue' => '1',
						'checked'    => ( isset( $current['divi_builder_support'] ) ) ? 'true' : 'false',
						'name'       => 'divi_builder_support',
						'namearray'  => 'divi_builder_support',
						'textvalue'  => '1',
						'labeltext'  => esc_html__( 'Add Divi Builder to the post type.', 'cptuiext' ),
						'helptext'   => esc_attr__( 'Add Divi Builder to the post type.', 'cptuiext' ),
						'default'    => true,
						'wrap'       => false,
					)
				);

				echo $ui->get_td_end() . $ui->get_tr_end();

				echo $ui->get_tr_start() . $ui->get_th_start() . __( 'Add RSS support.', 'cptuiext' ) . $ui->get_th_end() . $ui->get_td_start();

				echo $ui->get_check_input(
					array(
						'checkvalue' => '1',
						'checked'    => ( isset( $current['rss_support'] ) ) ? 'true' : 'false',
						'name'       => 'rss_support',
						'namearray'  => 'rss_support',
						'labeltext'  => esc_html__( 'Add RSS feed support to the post type.', 'cptuiext' ),
						'helptext'   => esc_attr__( 'Add RSS feed support to the post type.', 'cptuiext' ),
						'default'    => true,
						'wrap'       => false,
					)
				);

				echo $ui->get_td_end() . $ui->get_tr_end();
				?>
				</table>
				<?php if ( is_multisite() && is_network_admin() ) {
					echo '<input type="hidden" id="network_wide" name="cpt_custom_post_type[network_wide]" value="1" aria-required="false">';
				} ?>
			</div>
		</div>
	</div>
<?php
}
add_action( 'cptui_post_type_after_fieldsets', 'cptui_network_add_extended_options', 10, 1 );

/**
 * Filters CPTUI page title on network admin page.
 *
 * @internal
 *
 * @param  array $tabs Unmodified page tabs array.
 * @return array Modified page tabs array.
 */
function cptui_network_page_title_filter( $tabs = array() ) {
	if ( is_multisite() && is_network_admin() ) {
		$tabs['page_title'] = __( 'Manage Network Wide Post Types', 'cptuiext' );

		if ( isset( $_GET['page'] ) ) {
			if ( 'cptui_manage_taxonomies' === $_GET['page'] ) {
				$tabs['page_title'] = __( 'Manage Network Wide Taxonomies', 'cptuiext' );
			}

			if ( 'cptui_tools' === $_GET['page'] ) {
				$tabs['page_title'] = __( 'Import Network Wide Post Types and Taxonomies', 'cptuiext' );
			}
		}
	}
	return $tabs;
}
add_filter( 'cptui_get_tabs', 'cptui_network_page_title_filter', 999 );

/**
 * Adds selected CPTs to post query.
 *
 * @internal
 *
 * @param object $query WP_Query object.
 * @return object $query
 */
function cptui_add_cpt_to_query( $query ) {

	if ( ! is_admin() && $query->is_main_query() && $query->is_home() ) {

		$cpts = get_option( 'cptui_post_types' );

		if ( ! empty( $cpts ) ) {
			$types = array( 'post' );
			foreach ( $cpts as $cpt ) {
				if ( isset( $cpt['cpt_to_post_query'] ) ) {
					$types[] = $cpt['name'];
				}
			}

			/**
			 * Filters the post types to return to the main, is_home query.
			 *
			 * @since 1.0.0
			 *
			 * @param array $types Array of post types to include in the query.
			 */
			$types = apply_filters( 'cptui_add_cpt_to_query', $types );
			$query->set( 'post_type', $types );
		}
	}
	return $query;
}
add_action( 'pre_get_posts', 'cptui_add_cpt_to_query' );

/**
 * Flush rewrite after network tax/cpt edits.
 *
 * @internal
 */
function cptui_flush_rewrite() {

	if ( false !== ( $network_flush_ts = get_site_transient( 'cptui_rewrite_refresh' ) ) ) {

		$flush_ts = get_option( 'cptui_rewrite_refresh' );

		if ( $flush_ts !== $network_flush_ts ) {
			update_option( 'cptui_rewrite_refresh', $network_flush_ts );

			flush_rewrite_rules();
		}
	}
}
add_action( 'cptui_post_register_post_types', 'cptui_flush_rewrite', 999 );

/**
 * Adds selected CPTs to category/tag archives query.
 *
 * @since 1.2.0
 *
 * @internal
 *
 * @param object $query WP_Query object.
 * @return object $query
 */
function cptui_add_cpt_to_cat_tag_archives( $query ) {

	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// Prevent some unneeded queries when in this callback.
	if ( ! $query->is_category() && ! $query->is_tag() ) {
		return;
	}

	$cpts = get_option( 'cptui_post_types' );

	if ( ! empty( $cpts ) ) {
		$types = array( 'post' );
		if ( $query->is_category() && empty( $query->query_vars['suppress_filters'] ) ) {
			foreach ( $cpts as $cpt ) {
				if ( isset( $cpt['cpt_to_category_archive'] ) ) {
					$types[] = $cpt['name'];
				}
			}

			/**
			 * Filters the post types to pass in to the category archive query.
			 *
			 * @since 1.3.0
			 *
			 * @param array $types Array of post types.
			 */
			$types = apply_filters( 'cptui_add_cpt_to_category_archive', $types );
		}

		if ( $query->is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
			foreach ( $cpts as $cpt ) {
				if ( isset( $cpt['cpt_to_tag_archive'] ) ) {
					$types[] = $cpt['name'];
				}
			}

			/**
			 * Filters the post types to pass in to the tag archive query.
			 *
			 * @since 1.3.0
			 *
			 * @param array $types Array of post types.
			 */
			$types = apply_filters( 'cptui_add_cpt_to_tag_archive', $types );

		}
		$query->set( 'post_type', $types );
	}
}
add_action( 'pre_get_posts', 'cptui_add_cpt_to_cat_tag_archives' );

/**
 * Register AMP support for specified post types.
 *
 * @since 1.3.0
 */
function cptui_amp_support() {
	$cpts = get_option( 'cptui_post_types', array() );

	foreach ( $cpts as $type ) {
		if ( array_key_exists( 'google_amp_support', $type ) && '1' === $type['google_amp_support'][0] ) {
			add_post_type_support( $type['name'], AMP_QUERY_VAR );
		}
	}
}
add_action( 'amp_init', 'cptui_amp_support' );

function cptuiext_divi_add_post_types( $post_types ) {
	$cptui = cptui_get_post_type_data();
	foreach ( $cptui as $post_type => $data ) {
		if (
			! in_array( $post_type, $post_types ) &&
			post_type_supports( $post_type, 'editor' ) &&
			array_key_exists( 'divi_builder_support', $data ) &&
			'1' === $data['divi_builder_support'][0] ) {
				$post_types[] = $post_type;
		}
	}

	return $post_types;
}
add_filter( 'et_builder_post_types', 'cptuiext_divi_add_post_types' );

/* Add Divi Custom Post Settings box */
function cptuiext_divi_add_meta_boxes() {
	$cptui = cptui_get_post_type_data();
	foreach ( $cptui as $post_type => $data ) {
		if (
			post_type_supports( $post_type, 'editor' ) &&
			function_exists( 'et_single_settings_meta_box' ) &&
			array_key_exists( 'divi_builder_support', $data ) &&
			'1' === $data['divi_builder_support'][0] ) {
				add_meta_box( 'et_settings_meta_box', __( 'Divi Custom Post Type Settings', 'cptuiext' ), 'et_single_settings_meta_box', $post_type, 'side', 'high' );
		}
	}
}
add_action( 'add_meta_boxes', 'cptuiext_divi_add_meta_boxes' );

/* Ensure Divi Builder appears in correct location */
function cptuiext_divi_admin_js() {
	$screen = get_current_screen();
	if (
		! empty( $screen->post_type ) &&
		'page' !== $screen->post_type &&
		'post' !== $screen->post_type ) {
		?>
		<script>
			jQuery(function ($) {
				$('#et_pb_layout').insertAfter($('#et_pb_main_editor_wrap'));
			});
		</script>
		<style>
			#et_pb_layout {
				margin-top: 20px;
				margin-bottom: 0px
			}
		</style>
		<?php
	}
}
add_action( 'admin_head', 'cptuiext_divi_admin_js' );

/**
 * Handles importing for network-level post types and taxonomies.
 *
 * @since 1.4.0
 *
 * @internal
 */
function cptui_network_do_import_types_taxes() {
	if ( ! is_network_admin() ) {
		return;
	}

	if ( ! empty( $_POST ) &&
	     ( ! empty( $_POST['cptui_post_import'] ) && isset( $_POST['cptui_post_import'] ) ) ||
	     ( ! empty( $_POST['cptui_tax_import'] ) && isset( $_POST['cptui_tax_import'] ) )
	) {
		$success = cptui_import_types_taxes_settings( $_POST );
		add_action( 'network_admin_notices', "cptui_{$success}_admin_notice" );
	}
}
add_action( 'init', 'cptui_network_do_import_types_taxes', 8 );

function cptui_add_cpt_to_rss( $query ) {

	if ( ! $query->is_feed() ) {
		return;
	}

	$cpts = get_option( 'cptui_post_types' );

	if ( ! empty( $cpts ) ) {
		$types = array( 'post' );
		foreach( $cpts as $cpt ) {
			if ( isset( $cpt['rss_support'] ) ) {
				$types[] = $cpt['name'];
			}
		}

		/**
		 * Filters the post types to pass in to the category archive query.
		 *
		 * @since 1.5.0
		 *
		 * @param array $types Array of post types.
		 */
		$types = apply_filters( 'cptui_add_cpt_to_rss', $types );
		$query->set( 'post_type', $types );
	}
}
add_action( 'pre_get_posts', 'cptui_add_cpt_to_rss' );
