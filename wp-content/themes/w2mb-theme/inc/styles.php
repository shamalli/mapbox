<?php 

function wdt_generate_custom_styles() {
	$upload_dir = wp_upload_dir();
	$filename = trailingslashit($upload_dir['basedir']) . 'wdt-theme.css';

	ob_start();
	include WDT_PATH . '/inc/dynamic_css.php';
	$dynamic_css = ob_get_contents();
	ob_get_clean();

	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once(ABSPATH .'/wp-admin/includes/file.php');
		WP_Filesystem();
	}

	if ($wp_filesystem) {
		$wp_filesystem->put_contents(
				$filename,
				$dynamic_css,
				FS_CHMOD_FILE // predefined mode settings for WP files
		);
	}
}
add_action('customize_save_after', 'wdt_generate_custom_styles');

function wdt_include_google_fonts() {
	global $google_fonts;

	$fonts_settings = array(
			'body_font',
			'nav_menu_font',
			'headings_font',
	);

	$include_fonts = array();
	foreach ($fonts_settings AS $setting) {
		if (in_array(wdt_get_option($setting), $google_fonts)) {
			$include_fonts[] = wdt_get_option($setting) . ':300,400,600,700';
		}
	}

	if ($include_fonts) {
		$fonts_string = implode('|', array_unique($include_fonts));
		wp_register_style('wdt_google_fonts', '//fonts.googleapis.com/css?family=' . $fonts_string);
		wp_enqueue_style('wdt_google_fonts');
	}
}
add_action('wp_enqueue_scripts', 'wdt_include_google_fonts');

?>