<?php

function wdt_add_theme_meta_box() {
	$apply_metabox_post_types = array('post', 'page');

	foreach ($apply_metabox_post_types as $key => $type) {
		add_meta_box(
			'page_settings',
			esc_html__('Page Settings', 'WDT'),
			'wdt_page_settings_metabox',
			$type,
			'side',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'wdt_add_theme_meta_box');

function wdt_page_settings_metabox($post, $metabox) {
	wp_nonce_field(basename( __FILE__ ), 'wdt_page_settings_nonce');

	$page_settings = get_post_meta($post->ID, '_wdt_page_settings', true);
	if (is_array($page_settings)) {
		$page_header = !empty($page_settings['page_header']) ? true : false;
		$page_footer = !empty($page_settings['page_footer']) ? true : false;
		$right_sidebar = !empty($page_settings['right_sidebar']) ? true : false;
		$left_sidebar = !empty($page_settings['left_sidebar']) ? true : false;
		$full_width = !empty($page_settings['full_width']) ? true : false;
	} else {
		$page_header = true;
		$page_footer = true;
		$right_sidebar = false;
		$left_sidebar = false;
		$full_width = false;
	}
	?>
	<ul>
		<li>
			<label for="wdt_page_header">
				<input name="wdt_page_header" id="wdt_page_header" type="checkbox" <?php checked($page_header, true, true)?>>
				<?php _e("Show page header", "WDT"); ?>
			</label>
		</li>
		<li>
			<label for="wdt_page_footer">
				<input name="wdt_page_footer" id="wdt_page_footer" type="checkbox" <?php checked($page_footer, true, true)?>>
				<?php _e("Show page footer", "WDT"); ?>
			</label>
		</li>
		<li>
			<label for="wdt_right_sidebar">
				<input name="wdt_right_sidebar" id="wdt_right_sidebar" type="checkbox" <?php checked($right_sidebar, true, true)?>>
				<?php _e("Show right sidebar", "WDT"); ?>
			</label>
		</li>
		<li>
			<label for="wdt_left_sidebar">
				<input name="wdt_left_sidebar" id="wdt_left_sidebar" type="checkbox" <?php checked($left_sidebar, true, true)?>>
				<?php _e("Show left sidebar", "WDT"); ?>
			</label>
		</li>
		<li>
			<label for="wdt_full_width">
				<input name="wdt_full_width" id="wdt_full_width" type="checkbox" <?php checked($full_width, true, true)?>>
				<?php _e("Full width", "WDT"); ?>
			</label>
		</li>
	</ul>
    <?php
}

function wdt_save_page_settings($post_id, $post) {
	if (!(isset($_POST['wdt_page_settings_nonce']) && wp_verify_nonce(sanitize_key($_POST['wdt_page_settings_nonce']), basename(__FILE__)))) {
		return;
	}

	if (defined('DOING_AUTOSAVE') || wp_is_post_revision($post) !== false || wp_is_post_autosave($post) !== false) {
		return;
	}

	if (in_array($post->post_type, array('post', 'page'))) {
		$page_settings['page_header'] = (bool)!empty($_POST['wdt_page_header']);
		$page_settings['page_footer'] = (bool)!empty($_POST['wdt_page_footer']);
		$page_settings['right_sidebar'] = (bool)!empty($_POST['wdt_right_sidebar']);
		$page_settings['left_sidebar'] = (bool)!empty($_POST['wdt_left_sidebar']);
		$page_settings['full_width'] = (bool)!empty($_POST['wdt_full_width']);
		
		update_post_meta($post_id, '_wdt_page_settings', $page_settings);
	}
}
add_action('save_post', 'wdt_save_page_settings', 10, 2);
