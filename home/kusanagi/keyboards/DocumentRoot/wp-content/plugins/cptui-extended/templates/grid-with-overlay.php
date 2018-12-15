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
 * This file is used for displaying an ordered or unordered grid_with_overlay of post links.
 *
 * It is the template file used when the "grid_with_overlay" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. grid_with_overlay will be array indexes.
 *     amount             // Amount of posts per page to display.
 *     attached_post      // The attached post(s) to display, if specified.
 *     cptui_posttype     // The post type to query for.
 *     cptui_shortcode    // The shortcode to render.
 *     cptui_shortcode_id // Shortcode ID.
 *     excerpt            // Whether or not to show the post excerpt. Will be set to "on" if it should display.
 *     grid_with_overlay_type          // The type of HTML grid_with_overlay to use. Either `<ul>` or `<ol>`.
 *     order              // Order to display the posts in.
 *     order_by           // Property to order the posts by.
 * }
 */


	/*
	 * Please leave, unless you want to use $attributes as a PHP object instead.
	 */
	$attributes = (array) cptui_shortcode_atts( $attributes );

	 /**
	 * Fires before the title.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes Shortcode atrributes.
	 */
	do_action( 'template_grid_with_overlay_before_title', $attributes ); ?>

	<?php if ( isset( $attributes['title'] ) && '' !== $attributes['title'] ) : ?>
		<h2 class="cptui-shortcode-title"><?php cptui_shortcode_title( $attributes['title'] ); ?></h2>
	<?php endif;

	/**
	 * Fires after the title.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes Shortcode atrributes.
	 */
	do_action( 'template_grid_with_overlay_after_title', $attributes );

	/**
	 * Fires before the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_grid_with_overlay_before_shortcode', $attributes );

	$custom_query = new WP_Query( cptui_filter_query( $attributes ) );

	?>

	<ul class="cptui-shortcode-grid-with-overlay">

		<?php while ( $custom_query->have_posts() ) : $custom_query->the_post();

			$count_posts = $custom_query->post_count;
			$single      = 1 === $count_posts ? 'single-post' : '';

			/**
			 * Fires before the item.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_grid_with_overlay_before_item', $attributes, get_the_ID() );

			// Setup style var.
			$set_background = '';

			if ( has_post_thumbnail() ) {
				$set_background = 'style="background-image: url(' . get_the_post_thumbnail_url() . '); background-size: cover; background-position: 50% 50%;"';
			}
			?>

			<li id="cptui-<?php the_ID(); ?>" <?php cptui_post_class( $single, $attributes ); ?> <?php echo wp_kses_post( $set_background ); ?>>

				<div class="content-container">

					<?php
						// Show always if we option 1 or option 3 is selected.
						if ( isset( $attributes['show_post_date'] ) && 'on' === $attributes['show_post_date'] ) :
							/**
							 * Fires before the post date.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_before_show_post_date', $attributes, get_the_ID() );
						?>

							<span class="cptui-post-date post-date-container">
								<?php echo get_the_date(); ?>
							</span>

						<?php
							/**
							 * Fires after the post date.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_after_show_post_date', $attributes, get_the_ID() );
						endif;

						// Show always if we option 1 or option 3 is selected.
						if ( isset( $attributes['show_title'] ) && 'on' === $attributes['show_title'] ) :
							/**
							 * Fires before the title.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_before_post_title', $attributes, get_the_ID() );
						?>

							<a href="<?php the_permalink(); ?>" class="cptui-link">
								<h3 class="cptui-title title-container">
									<?php the_title(); ?>
								</h3>
							</a>

						<?php
							/**
							 * Fires after the title.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_after_post_title', $attributes, get_the_ID() );
						endif;

						// Show always if we option 1 or option 3 is selected.
						if ( isset( $attributes['show_excerpt'] ) && 'on' === $attributes['show_excerpt'] ) :

							/**
							 * Fires before the excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_before_post_excerpt', $attributes, get_the_ID() );
						?>

							<div class="cptui-excerpt">
								<?php
								echo cptuiext_get_the_excerpt( array(
									'length' => '7',
									) );
								?>
							</div>

						<?php
							/**
							 * Fires after the read more excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_after_post_excerpt', $attributes, get_the_ID() );
						endif;

						if ( isset( $attributes['show_read_more'] ) && 'on' === $attributes['show_read_more'] ) :

							/**
							 * Fires before the read more button.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_before_post_read_more', $attributes, get_the_ID() );
						?>

							<a href="<?php the_permalink(); ?>" class="button cptui-link read-more-container">
								<?php esc_html_e( 'Read More', 'cptuiext' ); ?>
							</a>

						<?php
							/**
							 * Fires after the read more button.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_grid_with_overlay_wafter_post_read_more', $attributes, get_the_ID() );
						endif;
					?>
				</div><!-- .content-container -->

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
			do_action( 'template_grid_with_overlay_after_item', $attributes, get_the_ID() );

			endwhile; ?>

		</ul><!-- .cptui-shortcode-grid_with_overlay -->

	<?php
	/**
	 * Fires after the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_grid_with_overlay_after_shortcode', $attributes );

	wp_reset_postdata(); // Reset the query.
