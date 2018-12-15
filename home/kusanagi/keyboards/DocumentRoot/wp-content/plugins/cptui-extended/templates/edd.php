<?php
/**
 * Easy Digital Downloads shortcode template
 *
 * @package CPTUIExtended
 * @author WebDevStudios
 * @license GPLV2
 * @since 1.0.0
 */

/*
 * This file is used for displaying EasyDigitalDownloads lists and product content.
 *
 * It is the template file used when the "EDD Download" shortcode has been selected in the shortcode builder.
 * For maximum compatibility, it's best to leave the action and filter hooks in place.
 *
 * This file will have an $attributes array variable available to render various parts of the template. The values in
 * the array will be composed of attributes passed in to the shortcode.
 *
 * array $attributes { // All shortcode attributes from post editor. List will be array indexes.
 *     attached_download  // The attached download(s) to display, if specified.
 *     cptui_posttype     // The post type to query for.
 *     cptui_shortcode    // The shortcode to render.
 *     cptui_shortcode_id // Shortcode ID.
 *     featured_image     // Whether or not to show the featured image. Will be set to "on" if it should display.
 *     order              // Order to display the posts in.
 *     order_by           // Property to order the posts by.
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
	 * @param array $attributes shortcode attributes.
	 */
	do_action( 'template_edd_before_title', $attributes ); ?>

	<?php if ( isset( $attributes['title'] ) && '' !== $attributes['title'] ) : ?>
		<h3 class="cptui-shortcode-title"><?php cptui_shortcode_title( $attributes['title'] ); ?></h3>
	<?php endif; ?>

	<?php $custom_query = new WP_Query( cptui_filter_query( $attributes ) ); ?>

	<div class="cptui-shortcode-list cptui-shortcode-edd-list">

		<?php while ( $custom_query->have_posts() ) : $custom_query->the_post();

			/**
			 * Fires before the item.
			 *
			 * @since 1.1.0
			 *
			 * @param array $attributes Shortcode attributes.
			 * @param int   $value      Current post ID.
			 */
			 do_action( 'template_edd_before_item', $attributes, get_the_ID() ); ?>

				<div itemscope itemtype="http://schema.org/Product" id="cptui-<?php the_ID(); ?>" <?php post_class( 'cptui-entry cptui-product' ); ?>>

					<?php if ( 'on' === $attributes['featured_image'] ) : ?>

						<figure class="cptui-entry-thumbnail">

							<?php
							/**
							 * Fires before the featured image.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes Shortcode attributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_edd_before_featured_image', $attributes, get_the_ID() ); ?>

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
								the_post_thumbnail( apply_filters( 'template_edd_thumbnail_size', 'medium', $attributes, get_the_ID() ) ); ?></a>

							<?php
							/**
							 * Fires after the featured image.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes Shortcode attributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_edd_after_featured_image', $attributes, get_the_ID() ); ?>

						</figure><!-- .cptui-entry-thumbnail -->

					<?php endif; ?>

					<div class="cptui-entry-header">

						<?php
						/**
						 * Fires before the item title.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes Shortcode attributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_edd_before_item_title', $attributes, get_the_ID() ); ?>

						<h4 class="cptui-entry-title">
							<span itemprop="name"><a class="cptui-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
						</h4>

						<?php
						/**
						 * Fires after the item title.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes Shortcode attributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_edd_after_item_title', $attributes, get_the_ID() ); ?>

					</div><!-- .cptui-entry-header -->

					<div class="cptui-entry-summary" itemprop="description">

						<?php
						/**
						 * Fires before the excerpt.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes Shortcode attributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_edd_before_excerpt', $attributes, get_the_ID() );

						the_excerpt();

						/**
						 * Fires after the excerpt.
						 *
						 * @since 1.1.0
						 *
						 * @param array $attributes Shortcode attributes.
						 * @param int   $value      Current post ID.
						 */
						do_action( 'template_edd_after_excerpt', $attributes, get_the_ID() ); ?>

					</div><!-- .cptui-entry-summary -->

					<div class="cptui-entry-footer">
						<?php $product = edd_get_download( get_the_ID() ); ?>

						<div class="cptui-product-price">

							<?php
							/**
							 * Fires before the price.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes Shortcode attributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_edd_before_price', $attributes, get_the_ID() );

							if ( ! edd_has_variable_prices( get_the_ID() ) ) :
								esc_attr_e( 'Price: ', 'cptuiext' );
								edd_price( get_the_ID() );
							endif;

							/**
							 * Fires after the price.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes Shortcode attributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_edd_after_price', $attributes, get_the_ID() ); ?>

						</div><!-- .cptui-product-price -->

						<div class="cptui-product-checkout">

							<?php
							/**
							 * Fires before the checkout.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes Shortcode attributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_edd_before_checkout', $attributes, get_the_ID() );

							echo edd_get_purchase_link( array( 'download_id' => get_the_ID() ) );

							/**
							 * Fires after the checkout.
							 *
							 * @since 1.1.0
							 *
							 * @param array $attributes Shortcode attributes.
							 * @param int   $value      Current post ID.
							 */
							do_action( 'template_edd_after_checkout', $attributes, get_the_ID() ); ?>

						</div><!-- .cptui-product-checkout -->
					</div><!-- .cptui-entry-footer -->

				</div><!-- #cptui-xxxxx .cptui-entry -->

		<?php endwhile;

		/**
		 * Fires before pagination.
		 *
		 * @since 1.1.0
		 *
		 * @param array $attributes Shortcode attributes.
		 */
		do_action( 'template_edd_before_pagination', $attributes );

		cptui_pagination_links( $custom_query, $attributes );

		/**
		 * Fires after the item.
		 *
		 * @since 1.1.0
		 *
		 * @param array $attributes Shortcode attributes.
		 */
		do_action( 'template_edd_after_item', $attributes ); ?>

	</div><!-- .cptui-shortcode-list .cptui-shortcode-edd-list .product-list .unlist -->

	<?php wp_reset_postdata(); // Reset the query.
