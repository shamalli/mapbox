<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>

	<footer class="entry-footer">
		<?php edit_post_link('Edit', '<span class="edit-link">', '</span>' ); ?>
	</footer>
</article>

