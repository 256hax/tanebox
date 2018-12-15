<div class="su-posts su-posts-default-loop">
	<dl>
	    <?php
	        $args = array(
	            'post_type' =>array('post', 'blog','review'), // set Custom Posty Type
	            'posts_per_page' => 10,
	        );
	        $myposts = get_posts($args);
	        if ($myposts) : foreach ($myposts as $post) :
	    ?>
			<dt class="entry-meta"><?php the_time( get_option( 'date_format' ) ); ?></dt>
	    <dd><a href="<?php the_permalink();?>"><?php the_title(); ?></a></dd>
	    <?php endforeach; endif; ?>
	</dl>
</div><!-- .su-posts -->
