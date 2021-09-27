<?php 

define('WDT_PATH', get_template_directory());
define('WDT_URL', get_template_directory_uri());
define('WDT_FEATURED_SIZE_WIDTH', 1920);
define('WDT_FEATURED_SIZE_HEIGHT', 700);

include_once WDT_PATH . '/inc/customizer/customizer.php';
include_once WDT_PATH . '/inc/customizer/font-dropdown-custom-control.php';
include_once WDT_PATH . '/inc/functions.php';
include_once WDT_PATH . '/inc/styles.php';
include_once WDT_PATH . '/inc/metabox.php';
include_once WDT_PATH . '/inc/breadcrumbs.php';
include_once WDT_PATH . '/inc/w2mb-functions.php';

function wdt_theme_setup() {
	load_theme_textdomain('WDT');

	add_theme_support('automatic-feed-links');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('customize-selective-refresh-widgets');
	add_theme_support('custom-logo');

	register_nav_menus(array(
		'primary'  => esc_html__('Primary Menu', 'WDT'),
		'footer'   => esc_html__('Footer Menu', 'WDT'),
		'notfound' => esc_html__('404 Menu', 'WDT'),
	));

	add_theme_support('html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));
	
	add_image_size('wdt-featured', WDT_FEATURED_SIZE_WIDTH, WDT_FEATURED_SIZE_HEIGHT, true);
}
add_action('after_setup_theme', 'wdt_theme_setup');

function wdt_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__('Left Sidebar', 'WDT'),
		'id'            => 'sidebar-left',
		'description'   => esc_html__( 'Add widgets here to your Left Sidebar.', 'WDT' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__('Right Sidebar', 'WDT'),
		'id'            => 'sidebar-right',
		'description'   => esc_html__('Add widgets here to your Right Sidebar.', 'WDT' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action('widgets_init', 'wdt_widgets_init');

// Include dynamically generated css code if css file does not exist.
function wdt_enqueue_dynamic_css() {
	/* $upload_dir = wp_upload_dir();
	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once(ABSPATH .'/wp-admin/includes/file.php');
		WP_Filesystem();
	}
	ob_start();
	include WDT_PATH . '/inc/dynamic_css.php';
	$dynamic_css = ob_get_contents();
	ob_get_clean();
	echo '<style type="text/css">
	';
	echo $dynamic_css;
	echo '</style>'; */
	
	$upload_dir = wp_upload_dir();
	$filename = trailingslashit(set_url_scheme($upload_dir['baseurl'])) . 'wdt-theme.css';
	$filename_dir = trailingslashit($upload_dir['basedir']) . 'wdt-theme.css';
	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once(ABSPATH .'/wp-admin/includes/file.php');
		WP_Filesystem();
	}
	if (is_customize_preview() || !$wp_filesystem || !trim($wp_filesystem->get_contents($filename_dir))) {
		ob_start();
		include WDT_PATH . '/inc/dynamic_css.php';
		$dynamic_css = ob_get_contents();
		ob_get_clean();
		echo '<style type="text/css">
	';
		echo $dynamic_css;
		echo '</style>';
	}
}
add_action('wp_head', 'wdt_enqueue_dynamic_css', 9999);

function wdt_scripts_styles() {
	wp_enqueue_style('wdt-style', get_stylesheet_uri());
	
	wp_enqueue_script('wdt-main', WDT_URL . '/resources/js/main.js', array('jquery'));
	
	wp_enqueue_style('wdt-font-awesome', WDT_URL . '/resources/font-awesome/css/fontawesome.css');
	
	if (!is_customize_preview()) {
		// Include dynamically generated css file if this file exists
		$upload_dir = wp_upload_dir();
		$filename = trailingslashit(set_url_scheme($upload_dir['baseurl'])) . 'wdt-theme.css';
		$filename_dir = trailingslashit($upload_dir['basedir']) . 'wdt-theme.css';
		global $wp_filesystem;
		if (empty($wp_filesystem)) {
			require_once(ABSPATH .'/wp-admin/includes/file.php');
			WP_Filesystem();
		}
		if ($wp_filesystem && trim($wp_filesystem->get_contents($filename_dir))) { // if css file creation success
			wp_enqueue_style('wdt-theme-dynamic-css', $filename, array(), time());
		}
	}
}
add_action('wp_enqueue_scripts', 'wdt_scripts_styles');

function wdt_title_tag($title, $separator = ' - ') {
	if (is_page()) {
		global $post;
		$title = $post->post_title;
		if ($ancs = get_ancestors($post->ID, 'page')) {
			foreach($ancs as $anc) {
				$title .= $separator . get_page($anc)->post_title;
			}
		}
	}
	return $title;
}
add_filter('pre_get_document_title', 'wdt_title_tag', 16, 2);

?>