<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	
	<?php do_action('wdt_header_before'); ?>
	
	<?php wp_head(); ?>
	
	<?php do_action('wdt_header_after'); ?>
</head>

<body <?php body_class(); ?>>

	<?php do_action('wdt_body'); ?>

	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'WDT'); ?></a>
	
	<?php get_template_part('template-parts/header/mobile-menu'); ?>
	
	<?php //get_template_part('template-parts/header/contact-menu'); ?>

	<?php if (wdt_get_option('header_fixed_scroll_enable')): ?>
	<div class="header-fixed-clone"></div>
	<?php endif; ?>
	<header id="masthead" class="site-header <?php echo (wdt_get_option('header_fixed_scroll_enable') ? 'header-fixed' : ''); ?>" role="banner">
		<div class="container">
			<div class="site-branding <?php if (has_custom_logo()): ?>site-branding-logo<?php endif; ?>">
				<?php the_custom_logo(); ?>
	
				<?php $show_title = wdt_get_option('show_title'); ?>
				<?php $show_tagline = wdt_get_option('show_tagline'); ?>
				<?php if ($show_title || $show_tagline): ?>
				<div id="site-identity">
					<?php if ($show_title):  ?>
						<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
					<?php endif; ?>
					<?php if ($show_tagline): ?>
						<p class="site-description"><?php bloginfo('description'); ?></p>
					<?php endif; ?>
				</div>
				<?php endif; ?>
		    </div>
		    <?php do_action('wdt_before_navigation'); ?>
		    <div id="main-nav">
		        <nav id="site-navigation" class="main-navigation" role="navigation">
		            <div class="menu-content">
						<?php
						wp_nav_menu(
							array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'fallback_cb'    => 'wdt_primary_navigation_callback',
							)
						);
						?>
					</div>
				</nav>
			</div>
			<?php do_action('wdt_after_navigation'); ?>
		</div>
	</header>

	<?php if (wdt_is_page_header()): ?>
	<div class="page-header" <?php if ($image_url = wdt_get_page_featured_image()) echo 'style="background-image: url(' . $image_url . ');"' ?>>
		<div class="container">
			<h1 class="page-title"><?php echo wdt_get_page_title(); ?></h1>
			<?php wdt_breadcrumbs(); ?>
		</div>
		<?php
		$breadcrumbs = breadcrumb_trail(array(
			'show_browse' => false,
			'show_on_front' => false,
			'echo' => false
		)); ?>
		<?php if ($breadcrumbs): ?>
		<div class="container">
			<?php echo $breadcrumbs; ?> 
		</div>
		<?php endif; ?>
	</div>
	
	<?php endif; ?>
	
	
	<div id="content" class="site-content">
		<div class="container <?php echo (wdt_is_page_full_width()) ? 'container-full-width' : 'container-fixed'; ?>">
			<div class="inner-wrapper">