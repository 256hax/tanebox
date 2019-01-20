<?php get_header(); ?>
<div id="left-area">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <h1><?php echo apply_filters('cmtt_glossary_template_before_title', get_option('cmtt_glossaryCustomTemplateBeforeTitle', 'Term: '));?><?php echo get_the_title() ?></h1>
            <?php if (has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                    <?php the_post_thumbnail(); ?>
                </a>
            <?php endif; ?>
            <?php the_content(); ?>
            <?php
        endwhile;
    endif;
    ?>
</div>
<?php get_sidebar(); ?>
<?php
get_footer();
