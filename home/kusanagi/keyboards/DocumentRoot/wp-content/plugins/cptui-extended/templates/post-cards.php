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
 * This file is used for displaying an ordered or unordered post_cards of post links.
 *
 * It is the template file used when the "post_cards" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. post_cards will be array indexes.
 *     amount             // Amount of posts per page to display.
 *     attached_post      // The attached post(s) to display, if specified.
 *     cptui_posttype     // The post type to query for.
 *     cptui_shortcode    // The shortcode to render.
 *     cptui_shortcode_id // Shortcode ID.
 *     excerpt            // Whether or not to show the post excerpt. Will be set to "on" if it should display.
 *     post_cards_type          // The type of HTML post_cards to use. Either `<ul>` or `<ol>`.
 *     order              // Order to display the posts in.
 *     order_by           // Property to order the posts by.
 * }
 */


	/*
	 * Please leave, unless you want to use $attributes as a PHP object instead.
	 */
	$attributes = (array) cptui_shortcode_atts( $attributes );

	// Set default if users forget to set an option.
	$layout_type = 'option-1';

	// Add helper class to style containers.
	if ( 'option_1' === $attributes['card_layout'] ) {
		$layout_type = ' option-1';
	} elseif ( 'option_2' === $attributes['card_layout'] ) {
		$layout_type = ' option-2';
	} elseif ( 'option_3' === $attributes['card_layout'] ) {
		$layout_type = ' option-3';
	}

	 /**
	 * Fires before the title.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes Shortcode atrributes.
	 */
	do_action( 'template_post_cards_before_title', $attributes ); ?>

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
	do_action( 'template_post_cards_after_title', $attributes );

	/**
	 * Fires before the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_post_cards_before_shortcode', $attributes );

	$custom_query = new WP_Query( cptui_filter_query( $attributes ) );

	?>

	<ul class="cptui-shortcode-post_cards<?php echo esc_attr( $layout_type ); ?>">

		<?php while ( $custom_query->have_posts() ) : $custom_query->the_post();

			/**
			 * Fires before the item.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_post_cards_before_item', $attributes, get_the_ID() );
			?>

			<li id="cptui-<?php the_ID(); ?>" <?php cptui_post_class( '', $attributes ); ?>>

				<?php
					if ( isset( $attributes['show_thumbnail'] ) && 'on' === $attributes['show_thumbnail'] && has_post_thumbnail() ) :

						/**
						 * Fires before the excerpt.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes shortcode atrributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_post_cards_before_post_thumbnail', $attributes, get_the_ID() );
					?>

						<a href="<?php the_permalink(); ?>" class="cptui-link thumbnail-container">
							<?php the_post_thumbnail(); ?>
 						</a>

					<?php
						/**
						 * Fires before the excerpt.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes shortcode atrributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_post_cards_after_post_thumbnail', $attributes, get_the_ID() );
					endif;
				?>

				<div class="content-container">

					<?php
						// Show always if we option 1 or option 3 is selected.
						if ( ( 'option_2' === $attributes['card_layout'] && isset( $attributes['show_title'] ) && 'on' === $attributes['show_title'] ) || ( 'option_1' === $attributes['card_layout'] || 'option_3' === $attributes['card_layout'] ) ) :
							/**
							 * Fires before the excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_post_cards_before_post_title', $attributes, get_the_ID() );
						?>

							<a href="<?php the_permalink(); ?>" class="cptui-link">
								<h3 class="cptui-title title-container">
									<?php the_title(); ?>
								</h3>
							</a>

						<?php
							/**
							 * Fires before the excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_post_cards_after_post_title', $attributes, get_the_ID() );
						endif;
					?>

					<?php
						$categories = get_the_category();
						// Show always if we option 1 or option 3 is selected, but only if we have categories.
						if ( ( 'option_2' === $attributes['card_layout'] && isset( $attributes['show_categories'] ) && 'on' === $attributes['show_categories'] && ! empty( $categories ) ) || ( ( 'option_1' === $attributes['card_layout'] || 'option_3' === $attributes['card_layout'] ) && ! empty( $categories ) ) ) :

							/**
							 * Fires before the excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_post_cards_before_post_categories', $attributes, get_the_ID() );
						?>

							<div class="cptui-category-list">
								<?php the_category(', '); ?>
							</div>

						<?php
							/**
							 * Fires before the excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_post_cards_after_post_categories', $attributes, get_the_ID() );
						endif;
					?>

					<?php
						// Show always if we option 1 or option 3 is selected.
						if ( ( 'option_2' === $attributes['card_layout'] && isset( $attributes['show_excerpt'] ) && 'on' === $attributes['show_excerpt'] ) || ( 'option_1' === $attributes['card_layout'] || 'option_3' === $attributes['card_layout'] ) ) :

							/**
							 * Fires before the excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_post_cards_before_post_excerpt', $attributes, get_the_ID() );
						?>

							<div class="cptui-excerpt">
								<?php
								echo cptuiext_get_the_excerpt( array(
									'length' => '15',
									) );
								?>
							</div>

						<?php
							/**
							 * Fires before the excerpt.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes shortcode atrributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_post_cards_after_post_excerpt', $attributes, get_the_ID() );
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
			do_action( 'template_post_cards_after_item', $attributes, get_the_ID() );

			endwhile; ?>

		</ul><!-- .cptui-shortcode-post_cards -->

	<?php
	/**
	 * Fires after the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_post_cards_after_shortcode', $attributes );

	wp_reset_postdata(); // Reset the query.
