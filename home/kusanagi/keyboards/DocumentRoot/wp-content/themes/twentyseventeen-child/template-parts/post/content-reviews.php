<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen_Child
 * @since 1.0
 * @version 1.2
 */

?>

<?php
// ACF
$product_main_informations = get_field('product_main_informations');
$product_images = get_field('product_images');
//$dye_sub_printing = $keycaps['dye_sub_printing'] == 1 ? '(昇華印刷)' : '';
$ec_main_informations = get_field('ec_main_informations');
$ec_sites = get_field('ec_sites');
$product_cutomize_images = get_field('product_cutomize_images');
$customize_main_informations = get_field('customize_main_informations');
$customize_ec_sites = get_field('customize_ec_sites');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( is_sticky() && is_home() ) :
		echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
	endif;
	?>
	<header class="entry-header">
		<?php
		if ( 'post' === get_post_type() ) {
			echo '<div class="entry-meta">';
				if ( is_single() ) {
					twentyseventeen_posted_on();
				} else {
					echo twentyseventeen_time_link();
					twentyseventeen_edit_link();
				};
			echo '</div><!-- .entry-meta -->';
		};

		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} elseif ( is_front_page() && is_home() ) {
			the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
		} else {
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		?>
	</header><!-- .entry-header -->

	<?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
			</a>
		</div><!-- .post-thumbnail -->
	<?php endif; ?>

	<div class="entry-content">
		<?php
		/* translators: %s: Name of current post */
		the_content( sprintf(
			__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ),
			get_the_title()
		) );

		wp_link_pages( array(
			'before'      => '<div class="page-links">' . __( 'Pages:', 'twentyseventeen' ),
			'after'       => '</div>',
			'link_before' => '<span class="page-number">',
			'link_after'  => '</span>',
		) );
		?>

		<!-- product main informations -->
		<div class="contents-container">
			<img src="<?php echo $product_main_informations['picture']['url']; ?>" width="640">
			<h2><?php echo $product_main_informations['heading_descriptions']; ?></h2>
			<?php echo $product_main_informations['descriptions']; ?>
		</div>
		<!-- /product main informations -->

		<!-- product images pictures -->
		<?php if( $product_images['pictures'] ): ?>
			<div class="embed-container">
				<?php echo $product_images['pictures']; ?>
			</div>
		<?php endif; ?>
		<!-- /product images pictures -->

		<!-- product images videos -->
		<?php if( $product_images['videos'] ): ?>
			<h2>Videos</h2>
			<div class="embed-container">
				<?php echo $product_images['videos']; ?>
			</div>
		<?php endif; ?>
		<!-- /product images videos -->

		<!-- specs -->
		<?php	if( have_rows('specs') ):	?>
			<h2>Specs</h2>

			<?php while ( have_rows('specs') ) : the_row(); ?>

				<!-- keys -->
				<?php if( get_row_layout() == 'keys' ): ?>
					<div class="contents-container">
						<ul class="list">
							<!-- keys count -->
							<?php $count = get_sub_field('count'); if( $count ): ?>
								<li>キー数<?php echo $count; ?></li>
							<?php endif; ?>
							<!-- /keys count -->
							<!-- keys layout -->
							<?php $layout = get_sub_field('coulayoutnt'); if( layout ): ?>
								<li><?php the_sub_field('layout'); ?></li>
							<?php endif; ?>
							<!-- /keys layout -->
						</ul>
					</div>
				<?php endif; ?>
				<!-- /keys -->

				<!-- keycaps -->
				<?php if( get_row_layout() == 'keycaps' ): ?>
					<div class="contents-container">
						<ul class="list">
							<!-- materials -->
							<?php $materials = get_sub_field('materials'); if( $materials ): ?>
								<li><?php the_sub_field('materials'); ?> <?php the_sub_field('injection_molding'); ?></li>
							<?php endif; ?>
							<!-- /materials -->
							<!-- profiles -->
							<?php $profiles = get_sub_field('profiles'); if( $profiles ): ?>
								<li><?php the_sub_field('profiles'); ?>プロファイル</li>
							<?php endif; ?>
							<!-- /profiles -->
							<!-- switches -->
							<?php $switches = get_sub_field('switches'); if( $switches ): ?>
								<li><?php the_sub_field('switches'); ?>軸</li>
							<?php endif; ?>
							<!-- /switches -->
						</ul>
					</div>
				<?php endif; ?>
				<!-- /keycaps -->

	    <?php endwhile; ?>
		<?php endif; ?>
		<!-- /specs -->

		<!-- ec sites -->
		<?php if( $ec_main_informations ): ?>
			<div class="contents-container">
				<h2><?php echo $ec_main_informations['heading_descriptions']; ?></h2>
				<?php echo $ec_main_informations['descriptions']; ?>
				<?php if( $ec_sites ): ?>
					<ul class="list">
						<?php foreach ($ec_sites as $ec_site) { ?>
							<li><a href="<?php echo $ec_site['site_link']['url']; ?>" target="_blank" rel="nofollow"><?php echo $ec_site['site_link']['title']; ?></a></li>
						<?php } ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<!-- /ec sites -->

		<h2>Customize</h2>
		<!-- product_cutomize_images pictures -->
		<?php if( $product_cutomize_images['pictures'] ): ?>
			<div class="embed-container">
				<?php echo $product_cutomize_images['pictures']; ?>
			</div>
		<?php endif; ?>
		<!-- /product_cutomize_images pictures -->

		<!-- product_cutomize_images videos -->
		<?php if( $product_cutomize_images['videos'] ): ?>
			<h2>Videos</h2>
			<div class="embed-container">
				<?php echo $product_cutomize_images['videos']; ?>
			</div>
		<?php endif; ?>
		<!-- /product_cutomize_images videos -->

		<!-- customize main informations  -->
		<?php if( $customize_main_informations ): ?>
			<div class="contents-container">
				<h2><?php echo $customize_main_informations['heading_descriptions']; ?></h2>
				<?php echo $customize_main_informations['descriptions']; ?>
			</div>
		<?php endif; ?>
		<!-- /customize main informations -->

		<!-- customize ec sites -->
		<?php if( $customize_ec_sites ): ?>
			<div class="contents-container">
					<ul class="list">
						<?php foreach ($customize_ec_sites as $customize_ec_site) { ?>
							<li><a href="<?php echo $customize_ec_site['site_link']['url']; ?>" target="_blank" rel="nofollow"><?php echo $customize_ec_site['site_link']['title']; ?></a></li>
						<?php } ?>
					</ul>
			</div>
		<?php endif; ?>
		<!-- /customize ec sites -->

	</div><!-- .entry-content -->

	<?php
	if ( is_single() ) {
		twentyseventeen_entry_footer();
	}
	?>

</article><!-- #post-## -->
