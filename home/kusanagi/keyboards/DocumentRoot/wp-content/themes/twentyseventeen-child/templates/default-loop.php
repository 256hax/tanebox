<div class="su-posts su-posts-default-loop">

	<?php if ( $posts->have_posts() ) : ?>

		<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>

			<div id="su-post-<?php the_ID(); ?>" class="su-post">

				<div class="su-post-meta">
					<?php the_time( get_option( 'date_format' ) ); ?>
					<h2 class="su-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				</div>

			</div>

		<?php endwhile; ?>

	<?php else : ?>
		<h4><?php _e( 'Posts not found', 'shortcodes-ultimate' ); ?></h4>
	<?php endif; ?>

</div>
