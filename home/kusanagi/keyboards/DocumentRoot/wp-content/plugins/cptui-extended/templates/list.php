<?php
/**
 * Bullet shortcode template
 *
 * @package CPTUIExtended
 * @author WebDevStudios
 * @license GPLV2
 * @since 1.0.0
 */

/*
 * This file is used for displaying an ordered or unordered list of post links.
 *
 * It is the template file used when the "List" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. List will be array indexes.
 *     amount             // Amount of posts per page to display.
 *     attached_post      // The attached post(s) to display, if specified.
 *     cptui_posttype     // The post type to query for.
 *     cptui_shortcode    // The shortcode to render.
 *     cptui_shortcode_id // Shortcode ID.
 *     excerpt            // Whether or not to show the post excerpt. Will be set to "on" if it should display.
 *     list_type          // The type of HTML list to use. Either `<ul>` or `<ol>`.
 *     order              // Order to display the posts in.
 *     order_by           // Property to order the posts by.
 * }
 */

	/*
	 * Please leave, unless you want to use $attributes as a PHP object instead.
	 */
	$attributes = (array) cptui_shortcode_atts( $attributes );

	/**
	 * Fires before the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_list_before_shortcode', $attributes );

	$custom_query = new WP_Query( cptui_filter_query( $attributes ) );
	$list_type = ! empty( $attributes['list_type'] ) ? esc_attr( $attributes['list_type'] ) : 'ul';

	?>

	<<?php echo esc_attr( $list_type ); ?> class="cptui-shortcode-list">

		<?php while ( $custom_query->have_posts() ) : $custom_query->the_post();

			/**
			 * Fires before the item.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_list_before_item', $attributes, get_the_ID() );
			?>

			<li id="cptui-<?php the_ID(); ?>" <?php cptui_post_class( '', $attributes ); ?>>
				<a href="<?php the_permalink(); ?>" class="cptui-link"><?php the_title(); ?></a>

				<?php if ( isset( $attributes['excerpt'] ) && 'on' === $attributes['excerpt'] ) : ?>
					<div class="cptui-entry-summary">

						<?php
						/**
						 * Fires before the excerpt.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes shortcode atrributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_list_before_excerpt', $attributes, get_the_ID() );

						the_excerpt();

						/**
						 * Fires before the excerpt.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes shortcode atrributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_list_after_excerpt', $attributes, get_the_ID() ); ?>

					</div><!-- .cptui-entry-summary -->
				<?php endif; ?>
			</li><!-- .cptui-xxxxx -->

			<?php
			/**
			 * Fires after the item.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_list_after_item', $attributes, get_the_ID() );

			endwhile; ?>

	</<?php echo $list_type; ?>><!-- .cptui-shortcode-list -->

	<?php
	/**
	 * Fires after the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_list_after_shortcode', $attributes );

	wp_reset_postdata(); // Reset the query.
