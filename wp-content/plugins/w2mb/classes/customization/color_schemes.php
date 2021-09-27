<?php 

$w2mb_color_schemes = array(
		'default' => array(
				'w2mb_primary_color' => '#2393ba',
				'w2mb_secondary_color' => '#1f82a5',
				'w2mb_links_color' => '#2393ba',
				'w2mb_links_hover_color' => '#2a6496',
				'w2mb_button_1_color' => '#2393ba',
				'w2mb_button_2_color' => '#1f82a5',
				'w2mb_button_text_color' => '#FFFFFF',
				'w2mb_search_bg_color' => '#6bc8c8',
				'w2mb_search_text_color' => '#FFFFFF',
				'w2mb_jquery_ui_schemas' => 'redmond',
		),
		'blue' => array(
				'w2mb_primary_color' => '#194df2',
				'w2mb_secondary_color' => '#8895a2',
				'w2mb_links_color' => '#96a1ad',
				'w2mb_links_hover_color' => '#2a6496',
				'w2mb_button_1_color' => '#96a1ad',
				'w2mb_button_2_color' => '#8895a2',
				'w2mb_button_text_color' => '#FFFFFF',
				'w2mb_search_bg_color' => '#499df5',
				'w2mb_search_text_color' => '#FFFFFF',
				'w2mb_jquery_ui_schemas' => 'start',
		),
		'gray' => array(
				'w2mb_primary_color' => '#acc7a6',
				'w2mb_secondary_color' => '#2d8ab7',
				'w2mb_links_color' => '#3299cb',
				'w2mb_links_hover_color' => '#236b8e',
				'w2mb_button_1_color' => '#3299cb',
				'w2mb_button_2_color' => '#2d8ab7',
				'w2mb_button_text_color' => '#FFFFFF',
				'w2mb_search_bg_color' => '#cfdbc5',
				'w2mb_search_text_color' => '#FFFFFF',
				'w2mb_jquery_ui_schemas' => 'overcast',
		),
		'green' => array(
				'w2mb_primary_color' => '#6cc150',
				'w2mb_secondary_color' => '#64933d',
				'w2mb_links_color' => '#5b9d30',
				'w2mb_links_hover_color' => '#64933d',
				'w2mb_button_1_color' => '#5b9d30',
				'w2mb_button_2_color' => '#64933d',
				'w2mb_button_text_color' => '#FFFFFF',
				'w2mb_search_bg_color' => '#c3ff88',
				'w2mb_search_text_color' => '#575757',
				'w2mb_jquery_ui_schemas' => 'le-frog',
		),
		'orange' => array(
				'w2mb_primary_color' => '#ff6600',
				'w2mb_secondary_color' => '#404040',
				'w2mb_links_color' => '#4d4d4d',
				'w2mb_links_hover_color' => '#000000',
				'w2mb_button_1_color' => '#4d4d4d',
				'w2mb_button_2_color' => '#404040',
				'w2mb_button_text_color' => '#FFFFFF',
				'w2mb_search_bg_color' => '#ff8000',
				'w2mb_search_text_color' => '#FFFFFF',
				'w2mb_jquery_ui_schemas' => 'ui-lightness',
		),
		'yellow' => array(
				'w2mb_primary_color' => '#a99d1a',
				'w2mb_secondary_color' => '#868600',
				'w2mb_links_color' => '#b8b900',
				'w2mb_links_hover_color' => '#868600',
				'w2mb_button_1_color' => '#b8b900',
				'w2mb_button_2_color' => '#868600',
				'w2mb_button_text_color' => '#FFFFFF',
				'w2mb_search_bg_color' => '#ffff8d',
				'w2mb_search_text_color' => '#575757',
				'w2mb_jquery_ui_schemas' => 'sunny',
		),
		'red' => array(
				'w2mb_primary_color' => '#679acd',
				'w2mb_secondary_color' => '#cb4862',
				'w2mb_links_color' => '#ed4e6e',
				'w2mb_links_hover_color' => '#cb4862',
				'w2mb_button_1_color' => '#ed4e6e',
				'w2mb_button_2_color' => '#cb4862',
				'w2mb_button_text_color' => '#FFFFFF',
				'w2mb_search_bg_color' => '#476583',
				'w2mb_search_text_color' => '#FFFFFF',
				'w2mb_jquery_ui_schemas' => 'blitzer',
		),
);
global $w2mb_color_schemes;

function w2mb_affect_setting_w2mb_links_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_links_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_links_color');

function w2mb_affect_setting_w2mb_links_hover_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_links_hover_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_links_hover_color');

function w2mb_affect_setting_w2mb_button_1_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_button_1_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_button_1_color');

function w2mb_affect_setting_w2mb_button_2_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_button_2_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_button_2_color');

function w2mb_affect_setting_w2mb_button_text_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_button_text_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_button_text_color');

function w2mb_affect_setting_w2mb_search_bg_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_search_bg_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_search_bg_color');

function w2mb_affect_setting_w2mb_search_text_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_search_text_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_search_text_color');

function w2mb_affect_setting_w2mb_primary_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_primary_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_primary_color');

function w2mb_affect_setting_w2mb_secandary_color($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_secandary_color'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_secandary_color');

function w2mb_affect_setting_w2mb_jquery_ui_schemas($scheme) {
	global $w2mb_color_schemes;
	return $w2mb_color_schemes[$scheme]['w2mb_jquery_ui_schemas'];
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_affect_setting_w2mb_jquery_ui_schemas');

function w2mb_get_dynamic_option($option_name) {
	global $w2mb_color_schemes;

	if (isset($_COOKIE['w2mb_compare_palettes']) && $_COOKIE['w2mb_compare_palettes']) {
		$scheme = $_COOKIE['w2mb_compare_palettes'];
		if (isset($w2mb_color_schemes[$scheme][$option_name]))
			return $w2mb_color_schemes[$scheme][$option_name];
		else 
			return get_option($option_name);
	} else
		return get_option($option_name);
}

?>