<?php
/**
 * Slider shortcode template
 *
 * @package CPTUIExtended
 * @author WebDevStudios
 * @license GPLV2
 * @since 1.0.0
 */

/*
 * This file is used for displaying a dynamic slider composed of selected posts.
 *
 * It is the template file used when the "Post Slider" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. List will be array indexes.
 *     attached_post      // The attached download(s) to display, if specified.
 *     autoplay           // Whether or not to autoplay the slider. Will be set to "on" if it should autoplay.
 *     cptui_posttype     // The post type to query for.
 *     cptui_shortcode    // The shortcode to render.
 *     cptui_shortcode_id // Shortcode ID.
 *     height             // Height to use for the slider.
 *     show_bullets       // Whether or not to display bullets below slider. Will be set to "on" if it should display.
 *     show_title         // Whether or not to display the post title with the slider image.
 * }
 */


	/*
	 * Please leave, unless you want to use $attributes as a PHP object instead.
	 */
	$attributes = (array) cptui_shortcode_atts( $attributes );

	$attributes['height'] = ( isset( $attributes['height'] ) && is_numeric( $attributes['height'] ) ) ? $attributes['height'] : '300';

	/**
	 * Fires before the shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes shortcode atrributes.
	 */
	do_action( 'template_list_before_shortcode', $attributes );

	$custom_query = new WP_Query( cptui_filter_query( $attributes ) );
	?>

	<div class="cptui-shortcode-slider" data-slick='<?php echo cptui_slider_config( $attributes ); ?>'>

		<?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>

			<div class="cptui-<?php the_ID(); ?>">
				<a class="cptui-link" href="<?php the_permalink(); ?>"><div class="cptui-slider-image" style="background: url(<?php the_post_thumbnail_url( 'full' ); ?>) grey; background-size:cover; height:<?php echo esc_attr( $attributes['height'] ); ?>px"></div></a>

				<?php if ( 'on' === $attributes['show_title'] ) : ?>
					<a href="<?php the_permalink(); ?>" class="cptui-link"><?php the_title(); ?></a>
				<?php endif ; ?>

			</div><!-- .cptui-xxxxx -->

		<?php endwhile; ?>

	</div><!-- .cptui-shortcode-list -->

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
