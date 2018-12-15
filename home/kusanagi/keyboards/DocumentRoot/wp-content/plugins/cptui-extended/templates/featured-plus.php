<?php
/**
 * Featured Plus shortcode template
 *
 * @package CPTUIExtended
 * @author WebDevStudios
 * @license GPLV2
 * @since 1.0.0
 */

/*
 * This file is used for displaying an ordered or unordered grid of post links.
 *
 * It is the template file used when the "grid" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. grid will be array indexes.
 *     amount             // Amount of posts per page to display.
 * }
 */


	/*
	 * Please leave, unless you want to use $attributes as a PHP object instead.
	 */
	$attributes = (array) cptui_shortcode_atts( $attributes );

	/*
	 * Attribute fields.
	 */
	$featured_type = ' ' . $attributes['featured_plus_type'];
	?>

	<section class="cptui-shortcode-featured-plus">

		<?php
		/**
		 * Fires before the title.
		 *
		 * @since 1.1.0
		 *
		 * @param array $attributes Shortcode atrributes.
		 */
		do_action( 'template_featured_plus_before_title', $attributes ); ?>


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
		do_action( 'template_featured_plus_after_title', $attributes );

		/**
		 * Fires before the shortcode.
		 *
		 * @since 1.1.0
		 *
		 * @param array $attributes shortcode atrributes.
		 */
		do_action( 'template_featured_plus_before_shortcode', $attributes );

		$custom_query = new WP_Query( cptui_filter_query( $attributes ) );
		?>

			<?php
			// Start count.
			$count = 0;
			?>

			<div class="featured-plus<?php echo esc_attr( $featured_type ); ?>">

				<?php while ( $custom_query->have_posts() ) : $custom_query->the_post();

					$count_posts = $custom_query->post_count;
					$single      = 1 === $count_posts ? ' single-post' : '';

					/**
					 * Fires before the item.
					 *
					 * @since 1.1.0
					 *
					 * @param array $attributes shortcode atrributes.
					 * @param int   $value      Current post ID.
					 */
					do_action( 'template_featured_plus_before_item', $attributes, get_the_ID() );
					?>

						<?php if ( 0 == $count ) : ?>
							<div class="featured-post<?php echo esc_attr( $single ); ?>">
						<?php elseif ( 1 === $count ) : ?>
							<div class="remaining-posts">
						<?php endif; ?>

							<div class="content-inner">

							<?php
							if ( has_post_thumbnail() ) :

								/**
								 * Fires before the excerpt.
								 *
								 * @since 1.1.0
								 *
								 * @param array $attributes shortcode atrributes.
								 * @param int   $value      Current post ID.
								 */
								do_action( 'template_featured_plus_before_post_thumbnail', $attributes, get_the_ID() );
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
								do_action( 'template_featured_plus_after_post_thumbnail', $attributes, get_the_ID() );
							endif;
						?>

								<?php if ( 1 <= $count ) : ?>
									<div class="left-split-container">
								<?php endif; ?>

										<?php
										/**
										 * Fires before the title.
										 *
										 * @since 1.1.0
										 *
										 * @param array $attributes shortcode atrributes.
										 * @param int   $value      Current post ID.
										 */
										do_action( 'template_featured_plus_before_title', $attributes, get_the_ID() );
										?>

										<h3><?php the_title(); ?></h3>

										<?php
										/**
										 * Fires after the title.
										 *
										 * @since 1.1.0
										 *
										 * @param array $attributes shortcode atrributes.
										 * @param int   $value      Current post ID.
										 */
										do_action( 'template_featured_plus_after_title', $attributes, get_the_ID() );
										?>

									<?php if ( 'left' === $attributes['featured_plus_type'] && 0 !== $count ) : ?>

											<?php
											/**
											 * Fires before the date.
											 *
											 * @since 1.1.0
											 *
											 * @param array $attributes shortcode atrributes.
											 * @param int   $value      Current post ID.
											 */
											do_action( 'template_featured_plus_before_date', $attributes, get_the_ID() );
											?>

											<span class="cptui-date">
												<?php echo get_the_date(); ?>
											</span>

											<?php
											/**
											 * Fires after the date.
											 *
											 * @since 1.1.0
											 *
											 * @param array $attributes shortcode atrributes.
											 * @param int   $value      Current post ID.
											 */
											do_action( 'template_featured_plus_after_date', $attributes, get_the_ID() );
											?>

									<?php endif; ?>
								<?php if ( 'left' === $attributes['featured_plus_type'] && 1 <= $count ) : ?>
									</div>
								<?php endif; ?>

								<?php if ( 'top' === $attributes['featured_plus_type'] || 0 === $count ) : ?>

									<?php
									/**
									 * Fires before the excerpt..
									 *
									 * @since 1.1.0
									 *
									 * @param array $attributes shortcode atrributes.
									 * @param int   $value      Current post ID.
									 */
									do_action( 'template_featured_plus_before_the_excerpt', $attributes, get_the_ID() );
									?>

									<p>
										<?php echo cptuiext_get_the_excerpt( array( 'length' => '13' ) ); ?>
									</p>

									<?php
									/**
									 * Fires after the excerpt.
									 *
									 * @since 1.1.0
									 *
									 * @param array $attributes shortcode atrributes.
									 * @param int   $value      Current post ID.
									 */
									do_action( 'template_featured_plus_after_the_excerpt', $attributes, get_the_ID() );
									?>

								<?php endif; ?>

								<?php if ( 'top' === $attributes['featured_plus_type'] && 1 <= $count ) : ?>
									</div>
								<?php endif; ?>

								<?php if ( 0 === $count ) : ?>
								<?php
									/**
									 * Fires before the excerpt.
									 *
									 * @since 1.1.0
									 *
									 * @param array $attributes shortcode atrributes.
									 * @param int   $value      Current post ID.
									 */
									do_action( 'template_featured_plus_before_post_read_more', $attributes, get_the_ID() );
								?>

									<a href="<?php the_permalink(); ?>" class="button cptui-link">
										<?php esc_html_e( 'Read More', 'cptuiext' ); ?>
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
									do_action( 'template_featured_plus_after_post_read_more', $attributes, get_the_ID() );
								?>
								<?php endif; ?>
							</div>

						<?php if ( 0 == $count ) : ?>
							</div>
						<?php elseif ( $count_posts === $count ) : ?>
							</div>
						<?php endif; ?>

					<?php
					/**
					 * Fires after the item.
					 *
					 * @since 1.1.0
					 *
					 * @param array $attributes shortcode atrributes.
					 * @param int   $value      Current post ID.
					 */
					do_action( 'template_featured_plus_after_item', $attributes, get_the_ID() );

					$count ++;

				endwhile; ?>

			</div>
		<?php
		/**
		 * Fires after the shortcode.
		 *
		 * @since 1.1.0
		 *
		 * @param array $attributes shortcode atrributes.
		 */
		do_action( 'template_featured_plus_after_shortcode', $attributes );
		?>

	</section><!-- .cptui-shortcode-grid -->

	<?php
	wp_reset_postdata(); // Reset the query.
