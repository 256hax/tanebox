<?php
/**
 * Displays content for front page
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'twentyseventeen-panel ' ); ?> >

	<?php
	  $args = array(
	    'posts_per_page' => 20 // Show articles number
	  );
	  $posts = get_posts( $args );
	  foreach ( $posts as $post ): // Start WP loop
	  setup_postdata( $post ); // Get articles data
	?>
	<header class="entry-header">
		<?php
			echo '<div class="entry-meta">';
				twentyseventeen_posted_on();
			echo '</div><!-- .entry-meta -->';

			the_title( '<div class="entry-title top"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></div>' );
		?>
	</header><!-- .entry-header -->
	<?php
		endforeach; // End WP loop
	  wp_reset_postdata(); // Recovery WP Query
	?>

</article><!-- #post-<?php the_ID(); ?> -->
