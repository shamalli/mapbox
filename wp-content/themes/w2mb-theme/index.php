<?php get_header(); ?>

<?php do_action('wdt_left_sidebar'); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if (have_posts()): ?>

			<?php while (have_posts()) : the_post(); ?>

				<?php get_template_part('template-parts/content'); ?>

			<?php endwhile; ?>

		<?php else: ?>

			<?php get_template_part('template-parts/content', 'none'); ?>

		<?php endif; ?>
		
		</main>
	</div>
	
<?php do_action('wdt_right_sidebar'); ?>

<?php get_footer(); ?>
