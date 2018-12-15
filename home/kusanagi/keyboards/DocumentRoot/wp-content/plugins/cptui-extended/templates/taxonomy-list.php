<?php
/**
 * Taxonomy List shortcode template
 *
 * @package CPTUIExtended
 * @author WebDevStudios
 * @license GPLV2
 * @since 1.0.0
 */

/*
 * This file is used for displaying a list of posts by term taxonomy.
 *
 * It is the template file used when the "Taxonomy List" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. List will be array indexes.
 *     cptui_posttype     // The post type to query for.
 *     cptui_shortcode    // The shortcode to render.
 *     cptui_shortcode_id // Shortcode ID.
 *     list_type          // The type of HTML list to use. Either `<ul>` or `<ol>`.
 *     tax_query          // WP_Query-ready tax_query parameter. Will be array of arrays for field, taxonomy, taxonomy term, and relation.
 *     taxonomy           // Array of arrays for for the taxonomy and specified taxonomy term.
 * }
 */

	/*
	 * Please leave, unless you want to use a PHP object instead.
	 */
	$attributes = (array) cptui_shortcode_atts( $attributes );

	/**
	 * Fires before the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_taxonomy_list_before_shortcode', $attributes ); ?>

	<?php
		$taxonomies = isset( $attributes['taxonomy'] ) ? $attributes['taxonomy'] : array();
		$list_type = isset( $attributes['list_type'] ) && '' !== $attributes['list_type'] ? esc_attr( $attributes['list_type'] ) : 'ul';

		foreach ( $taxonomies as $taxonomy => $value ) :

			$taxon       = get_taxonomy( $taxonomy );
			$taxon_label = isset( $taxon ) ? $taxon->labels->name : '';
			?>

			<h3 class="cptui-taxonomy-title"><?php echo esc_attr( $taxon->labels->name ); ?></h3>

			<<?php echo $list_type; ?> class="cptui-shortcode-list">

				<?php foreach ( $value as $data ) : ?>

					<?php
					/**
					 * Fires before the item.
					 *
					 * @since 1.1.0
					 *
					 * @param array  $attributes Shortcode atrributes.
					 * @param string $data       Current term name.
					 */
					 do_action( 'template_taxonomy_list_before_item', $attributes, $data ); ?>

					<?php
					$term_link = get_term_link( $data, $taxonomy );
					$term      = get_term_by( 'slug', $data, $taxonomy );
					$term_name = ( ! is_wp_error( $term ) ) ? $term->name : $data;
					?>

					<li class="cptui-entry">
						<?php if ( is_wp_error( $term_link ) ) { ?>
							<?php echo esc_attr( $data ); ?>
						<?php } else { ?>
							<a href="<?php echo esc_url( $term_link ); ?>" class="cptui-link"><?php echo esc_html( $term_name ); ?></a>
						<?php
						}
						?>
					</li><!-- .cptui-entry -->

					<?php
					/**
					 * Fires after the item.
					 *
					 * @since 1.1.0
					 *
					 * @param array  $attributes Shortcode atrributes.
					 * @param string $data       Current term name.
					 */
					 do_action( 'template_taxonomy_list_after_item', $attributes, $data ); ?>
				<?php endforeach; ?>

			 </<?php echo $list_type; ?>><!-- .cptui-shortcode-list -->

		<?php
		endforeach; ?>

	<?php
	/**
	 * Fires after the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_taxonomy_list_after_shortcode', $attributes );
