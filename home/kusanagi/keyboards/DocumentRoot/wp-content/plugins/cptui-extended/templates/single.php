<?php
/**
 * Single Post shortcode template
 *
 * @package CPTUIExtended
 * @author WebDevStudios
 * @license GPLV2
 * @since 1.0.0
 */

/*
 * This file is used for displaying a single, selected, post item.
 *
 * It is the template file used when the "Single Post" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. List will be array indexes.
 *     cptui_posttype     // The post type to query for.
 *     cptui_shortcode    // The shortcode to render.
 *     cptui_shortcode_id // Shortcode ID.
 *     featured_image     // Whether or not to show the featured image. Will be set to "on" if it should display.
 *     post_id            // ID of the page to display.
 *     title              // Title to display for the shortcode instance.
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
 * @param array $attributes shortcode atrributes.
 */
do_action( 'template_single_before_title', $attributes ); ?>

<?php if ( isset( $attributes['title'] ) && '' !== $attributes['title'] ) : ?>
	<h3 class="cptui-shortcode-title"><?php cptui_shortcode_title( $attributes['title'] ); ?></h3>
<?php endif; ?>

<?php
/**
 * Fires after the title.
 *
 * @since 1.1.0
 *
 * @param array $attributes shortcode atrributes.
 */
do_action( 'template_single_after_title', $attributes );

$custom_query = new WP_Query( cptui_filter_query( $attributes ) );

while ( $custom_query->have_posts() ) : $custom_query->the_post();

	/**
	 * Fires before the item.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes Shortcode atrributes.
	 * @param int   $value      Current post ID.
	 */
	 do_action( 'template_single_before_item', $attributes, get_the_ID() ); ?>

	<div id="cptui-<?php the_ID(); ?>" <?php cptui_post_class( 'cptui-entry', $attributes ); ?>>

		<?php if ( 'on' === $attributes['featured_image'] ) : ?>
			<figure class="cptui-entry-thumbnail">

				<?php
				/**
				 * Fires before the featured image.
				 *
				 * @since 1.1.0
				 *
				 * @param array $attributes Shortcode atrributes.
				 * @param int   $value      Current post ID.
				 */
				do_action( 'template_single_before_featured_image', $attributes, get_the_ID() ); ?>

				<a class="cptui-link" href="<?php the_permalink(); ?>"><?php
					/**
					 * Filters the thumbnail size to use for template file.
					 *
					 * @since 1.3.0
					 *
					 * @param string $value      Thumbnail size to use. Default 'thumbnail'.
					 * @param array  $attributes Shortcode attributes.
					 * @param int    $value      Current post ID.
					 */
					the_post_thumbnail( apply_filters( 'template_single_thumbnail_size', 'medium', $attributes, get_the_ID() ) ); ?></a>

				<?php
				/**
				 * Fires after the featured image.
				 *
				 * @since 1.1.0
				 *
				 * @param array $attributes Shortcode atrributes.
				 * @param int   $value      Current post ID.
				 */
				do_action( 'template_single_after_featured_image', $attributes, get_the_ID() ); ?>

			</figure><!-- .cptui-entry-thumbnail -->
		<?php endif; ?>

		<div class="cptui-entry-header">

			<?php
			/**
			 * Fires before the item title.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes Shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_single_before_item_title', $attributes, get_the_ID() ); ?>

			<h4 class="cptui-entry-title"><a class="cptui-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

			<?php
			/**
			 * Fires after the item title.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes Shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_single_after_item_title', $attributes, get_the_ID() ); ?>

		</div><!-- .cptui-entry-header -->

		<div class="cptui-entry-summary">

			<?php
			/**
			 * Fires before the excerpt.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes Shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_single_before_excerpt', $attributes, get_the_ID() ); ?>

			<?php the_excerpt(); ?>

			<?php
			/**
			 * Fires after the excerpt.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes Shortcode atrributes.
			 * @param int   $value      Current post ID.
			 */
			do_action( 'template_single_after_excerpt', $attributes, get_the_ID() ); ?>

		</div><!-- .cptui-entry-summary -->

	</div><!-- #cptui-xxxxx .cptui-entry -->

	<?php
	/**
	 * Fires after the item.
	 *
	 * @since 1.1.0
	 *
	 * @param array $attributes Shortcode atrributes.
	 * @param int   $value      Current post ID.
	 */
	do_action( 'template_single_after_item', $attributes, get_the_ID() ); ?>

<?php
endwhile;

wp_reset_postdata(); // Reset the query.
