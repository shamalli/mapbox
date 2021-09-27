<?php

return array(
		array(
				'type' => 'textbox',
				'name' => 'uid',
				'label' => esc_html__('uID', 'W2MB'),
				'description' => esc_html__('Enter the same string in [mapbox-search] shortcode to connect both shortcodes', 'W2MB'),
				'default' => ''
		),
		array(
				'type' => 'textbox',
				'name' => 'num',
				'label' => esc_html__('Max number of markers', 'W2MB'),
				'description' => esc_html__('Number of markers to display on map (empty field gives all markers)', 'W2MB'),
				'default' => ''
		),
		array(
				'type' => 'textbox',
				'name' => 'width',
				'label' => esc_html__('Map width', 'W2MB'),
				'description' => esc_html__('Set map width in pixels. With empty field the map will take all possible width', 'W2MB'),
				'default' => ''
		),
		array(
				'type' => 'textbox',
				'name' => 'height',
				'label' => esc_html__('Map height', 'W2MB'),
				'description' => esc_html__('Set map height in pixels, also possible to set 100% value', 'W2MB'),
				'default' => '400'
		),
		array(
				'type' => 'select',
				'name' => 'map_style',
				'label' => esc_html__('MapBox style', 'W2MB'),
				'items' => w2mb_getMetaboxMapsStyles(),
				'default' => array('Streets')
		),
		array(
				'type' => 'textbox',
				'name' => 'custom_map_style',
				'label' => esc_html__('Custom MapBox style', 'W2MB'),
				'description' => esc_html__('Example mapbox://styles/shamalli/cjhrfxqxu3zki2rmkka3a3hkp', 'W2MB'),
				'default' => ''
		),
		array(
				'type' => 'toggle',
				'name' => 'sticky_scroll',
				'label' => esc_html__('Make map to be sticky on scroll', 'W2MB'),
				'default' => 0
		),
		array(
				'type' => 'textbox',
				'name' => 'sticky_scroll_toppadding',
				'label' => esc_html__('Sticky scroll top padding', 'W2MB'),
				'description' => esc_html__('Top padding in pixels.', 'W2MB'),
				'default' => '0'
		),
		array(
				'type' => 'toggle',
				'name' => 'clusters',
				'label' => esc_html__('Group map markers in clusters', 'W2MB'),
				'default' => 0
		),
		array(
				'type' => 'toggle',
				'name' => 'geolocation',
				'label' => esc_html__('Enable automatic user Geolocation', 'W2MB'),
				'description' => esc_html__("Requires https", "W2MB"),
				'default' => 0
		),
		array(
				'type' => 'toggle',
				'name' => 'show_readmore_button',
				'label' => esc_html__('Show "Read more" button', 'W2MB'),
				'default' => 1
		),
		array(
				'type' => 'toggle',
				'name' => 'show_directions_button',
				'label' => esc_html__('Show "Directions" button', 'W2MB'),
				'default' => 1
		),
		array(
				'type' => 'toggle',
				'name' => 'directions_sidebar_open',
				'label' => esc_html__('Directions sidebar opened by default', 'W2MB'),
				'default' => 0
		),
);

?>