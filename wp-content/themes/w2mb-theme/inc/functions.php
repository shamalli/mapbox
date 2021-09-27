<?php 

function wdt_add_preloader() {
	?>
	<div class="preloader">
		<div class="spinner">
			<div class="rect1"></div>
			<div class="rect2"></div>
			<div class="rect3"></div>
			<div class="rect4"></div>
			<div class="rect5"></div>
		</div>
	</div>
	<?php
}
add_action('wdt_body', 'wdt_add_preloader');

function wdt_breadcrumbs() {
	
}

function wdt_primary_navigation_callback() {
	echo '<ul>';
	echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'WDT') . '</a></li>';
	wp_list_pages( array(
		'title_li' => '',
		'depth'    => 1,
		'number'   => 8,
	));
	echo '</ul>';
}

function wdt_get_option($key) {
	$default_options = wdt_get_default_theme_options();

	return get_theme_mod($key, (isset($default_options[$key]) ? $default_options[$key] : false));
}

function wdt_get_default_theme_options() {
	$defaults = array();
	
	$defaults['show_title'] = true;
	$defaults['show_tagline'] = true;
	$defaults['body_font'] = 'Roboto';
	$defaults['body_font_size'] = 14;
	$defaults['nav_menu_font'] = 'Roboto';
	$defaults['nav_menu_font_size'] = 16;
	$defaults['nav_menu_paddings'] = 8;
	$defaults['headings_font'] = 'Roboto';
	$defaults['header_background'] = '#FFFFFF';
	$defaults['header_text_color'] = '#000000';
	$defaults['page_header_paddings'] = '50';
	$defaults['page_header_background'] = '#00A6C9';
	$defaults['page_header_text_color'] = '#FFFFFF';
	$defaults['links_color'] = '#009BC1';
	$defaults['links_color_hover'] = '#00A6C9';
	$defaults['buttons_color'] = '#009BC1';
	$defaults['buttons_color_hover'] = '#00A6C9';
	$defaults['buttons_color_text'] = '#FFFFFF';
	$defaults['site_branding_width'] = 250;
	$defaults['header_default_bg_image'] = '';
	
	return $defaults;
}

function wdt_left_sidebar() {
	if (wdt_is_left_sidebar()) {
		wdt_sidebar_template('sidebar-left');
	}
}
add_action('wdt_left_sidebar', 'wdt_left_sidebar');

function wdt_right_sidebar() {
	if (wdt_is_right_sidebar()) {
		wdt_sidebar_template('sidebar-right');
	}
}
add_action('wdt_right_sidebar', 'wdt_right_sidebar');

function wdt_sidebar_template($sidebar) {
	if (is_active_sidebar($sidebar)) {
		echo '<div id="' . $sidebar . '" class="sidebar widget-area ' . $sidebar . '" role="complementary">';
			dynamic_sidebar($sidebar);
		echo '</div>';
	}
}

function wdt_get_body_classes($classes) {
	$left_sb = wdt_is_left_sidebar();
	$right_sb = wdt_is_right_sidebar();

	if ($left_sb && $right_sb) {
		$classes[] = 'sidebar-page-both';
	} elseif ($left_sb) {
		$classes[] = 'sidebar-page-left';
	} elseif ($right_sb) {
		$classes[] = 'sidebar-page-right';
	}
	
	return $classes;
}
add_filter('body_class', 'wdt_get_body_classes');

function wdt_is_left_sidebar() {
	$left_sidebar = wdt_get_page_setting('left_sidebar');
	if ($left_sidebar === null) {
		return false; // default value
	} else {
		return $left_sidebar;
	}
}
function wdt_is_right_sidebar() {
	$right_sidebar = wdt_get_page_setting('right_sidebar');
	if ($right_sidebar === null) {
		return false; // default value
	} else {
		return $right_sidebar;
	}
}

function wdt_get_page_title($title = '') {
	global $w2mb_instance;
	
	if (!$title) {
		if (is_front_page() && 'posts' === get_option('show_on_front')) {
			$title = bloginfo('name');
		}
		elseif (is_home() && ($blog_page_id = get_option('page_for_posts')) > 0) {
			$title = bloginfo('name');
		}
		elseif (is_singular()) {
			$title = single_post_title('', false);
		}
		elseif (is_archive()) {
			$title = strip_tags(get_the_archive_title());
		}
		elseif (is_search()) {
			$title = sprintf( __('Search Results for: %s', 'WDT'), get_search_query());
		}
		elseif (is_404()) {
			$title = __('404!', 'WDT');
		}
	}

	return $title;
}

function wdt_is_page_full_width() {
	return wdt_get_page_setting('full_width');
}

function wdt_is_page_header() {
	$page_header = wdt_get_page_setting('page_header');
	if ($page_header === null) {
		return true; // default value
	} else {
		return $page_header;
	}
}

function wdt_get_page_setting($setting = '') {
	if (is_singular()) {
		$post_id = get_the_ID();
		$page_settings = get_post_meta($post_id, '_wdt_page_settings', true);
		if (is_array($page_settings)) {
			if ($setting && isset($page_settings[$setting])) {
				return $page_settings[$setting];
			} elseif (!$setting) {
				return $page_settings;
			}
		}
		return null;
	}
}

function wdt_get_page_featured_image() {
	global $w2mb_instance;
	
	$image_url = '';

	
	if (!$image_url) {
		$image_url = get_the_post_thumbnail_url(null, array(WDT_FEATURED_SIZE_WIDTH, WDT_FEATURED_SIZE_HEIGHT));
	}

	if (!$image_url) {
		$image_url = wdt_get_option('header_default_bg_image');
	}
	
	return $image_url;
}

?>