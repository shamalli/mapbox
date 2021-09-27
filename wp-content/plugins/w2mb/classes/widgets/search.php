<?php

global $w2mb_search_widget_params;
$w2mb_search_widget_params = array(
		array(
				'type' => 'textfield',
				'param_name' => 'uid',
				'heading' => esc_html__("uID", "W2MB"),
				'description' => esc_html__("Enter unique string to connect search form with the map.", "W2MB"),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'columns',
				'value' => array('2', '1'),
				'std' => '2',
				'heading' => esc_html__('Number of columns to arrange search fields', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'advanced_open',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Advanced search panel always open', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'sticky_scroll',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Make search form to be sticky on scroll', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'sticky_scroll_toppadding',
				'value' => 0,
				'heading' => esc_html__('Sticky scroll top padding', 'W2MB'),
				'description' => esc_html__('Sticky scroll top padding in pixels.', 'W2MB'),
				'dependency' => array('element' => 'sticky_scroll', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_keywords_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show keywords search?', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'keywords_ajax_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Enable listings autosuggestions by keywords', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'keywords_search_examples',
				'heading' => esc_html__('Keywords examples', 'W2MB'),
				'description' => esc_html__('Comma-separated list of suggestions to try to search.', 'W2MB'),
		),
		/* array(
				'type' => 'textfield',
				'param_name' => 'what_search',
				'heading' => esc_html__('Default keywords', 'W2MB'),
		), */
		array(
				'type' => 'textfield',
				'param_name' => 'keywords_placeholder',
				'heading' => esc_html__('Keywords placeholder', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_categories_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show categories search?', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'categories_search_level',
				'value' => array('1', '2', '3'),
				'std' => '2',
				'heading' => esc_html__('Categories search depth level', 'W2MB'),
		),
		array(
				'type' => 'categoryfield',
				'param_name' => 'category',
				'heading' => esc_html__('Select certain category', 'W2MB'),
		),
		array(
				'type' => 'categoriesfield',
				'param_name' => 'exact_categories',
				'heading' => esc_html__('List of categories', 'W2MB'),
				'description' => esc_html__('Comma separated string of categories slugs or IDs. Possible to display exact categories.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_locations_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show locations search?', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'locations_search_level',
				'value' => array('1', '2', '3'),
				'std' => '2',
				'heading' => esc_html__('Locations search depth level', 'W2MB'),
		),
		array(
				'type' => 'locationfield',
				'param_name' => 'location',
				'heading' => esc_html__('Select certain location', 'W2MB'),
		),
		array(
				'type' => 'locationsfield',
				'param_name' => 'exact_locations',
				'heading' => esc_html__('List of locations', 'W2MB'),
				'description' => esc_html__('Comma separated string of locations slugs or IDs. Possible to display exact locations.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_address_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show address search?', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'address',
				'heading' => esc_html__('Default address', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'address_placeholder',
				'heading' => esc_html__('Adress placeholder', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_radius_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show locations radius search?', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'radius',
				'heading' => esc_html__('Default radius search', 'W2MB'),
		),
		array(
				'type' => 'contentfields',
				'param_name' => 'search_fields',
				'heading' => esc_html__('Select certain content fields', 'W2MB'),
		),
		array(
				'type' => 'contentfields',
				'param_name' => 'search_fields_advanced',
				'heading' => esc_html__('Select certain content fields in advanced section', 'W2MB'),
		),
		array(
				'type' => 'colorpicker',
				'param_name' => 'search_bg_color',
				'heading' => esc_html__("Background color", "W2MB"),
				'value' => get_option('w2mb_search_bg_color'),
		),
		array(
				'type' => 'colorpicker',
				'param_name' => 'search_text_color',
				'heading' => esc_html__("Text color", "W2MB"),
				'value' => get_option('w2mb_search_text_color'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'search_bg_opacity',
				'heading' => esc_html__("Opacity of search form background, in %", "W2MB"),
				'value' => 100,
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'search_overlay',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show background overlay', 'W2MB'),
				'std' => get_option('w2mb_search_overlay')
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'hide_search_button',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Hide search button', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'on_row_search_button',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Search button on one line with fields', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'scroll_to',
				'value' => array(esc_html__('No scroll', 'W2MB') => '', esc_html__('Listings', 'W2MB') => 'listings', esc_html__('Map', 'W2MB') => 'map'),
				'heading' => esc_html__('Scroll to listings, map or do not scroll after search button was pressed', 'W2MB'),
		),
);

class w2mb_search_widget extends w2mb_widget {

	public function __construct() {
		global $w2mb_instance, $w2mb_search_widget_params;

		parent::__construct(
				'w2mb_search_widget',
				esc_html__('MapBox locator - Search', 'W2MB'),
				esc_html__('Search Form', 'W2MB')
		);

		foreach ($w2mb_instance->search_fields->filter_fields_array AS $filter_field) {
			if (method_exists($filter_field, 'getVCParams') && ($field_params = $filter_field->getVCParams())) {
				$w2mb_search_widget_params = array_merge($w2mb_search_widget_params, $field_params);
			}
		}

		$this->convertParams($w2mb_search_widget_params);
	}
	
	public function render_widget($instance, $args) {
		global $w2mb_instance;

		$title = apply_filters('widget_title', $instance['title']);

		echo $args['before_widget'];
		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="w2mb-content w2mb-widget w2mb-search-widget">';
		$controller = new w2mb_search_controller();
		$controller->init($instance);
		echo $controller->display();
		echo '</div>';
		echo $args['after_widget'];
	}
}
?>