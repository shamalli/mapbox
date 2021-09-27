<?php

global $w2mb_map_widget_params;
$w2mb_map_widget_params = array(
		array(
				'type' => 'textfield',
				'param_name' => 'id',
				'value' => '',
				'heading' => esc_html__('This is ID of the map created in the Maps Manager.', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'uid',
				'value' => '',
				'heading' => esc_html__('uID. Enter unique string to connect this shortcode with another shortcodes.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'draw_panel',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Enable Draw Panel', 'W2MB'),
				'description' => esc_html__('Very important: MySQL version must be 5.6.1 and higher or MySQL server variable "thread stack" must be 256K and higher. Ask your hoster about it if "Draw Area" does not work.', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'num',
				'value' => -1,
				'heading' => esc_html__('Number of markers', 'W2MB'),
				'description' => esc_html__('Number of markers to display on map (-1 gives all markers).', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'width',
				'heading' => esc_html__('Width', 'W2MB'),
				'description' => esc_html__('Set map width in pixels. With empty field the map will take all possible width.', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'height',
				'value' => 400,
				'heading' => esc_html__('Height', 'W2MB'),
				'description' => esc_html__('Set map height in pixels, also possible to set 100% value.', 'W2MB'),
		),
		array(
				'type' => 'mapstyle',
				'param_name' => 'map_style',
				'heading' => esc_html__('Maps style', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'custom_map_style',
				'heading' => esc_html__('Custom map style', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'sticky_scroll',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Make map to be sticky on scroll', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'sticky_scroll_toppadding',
				'value' => 0,
				'heading' => esc_html__('Sticky scroll top padding', 'W2MB'),
				'description' => esc_html__('Top padding in pixels.', 'W2MB'),
				'dependency' => array('element' => 'sticky_scroll', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_directions_button',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Show summary button', 'W2MB'),
				'description' => esc_html__('Show summary button in InfoWindow.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_readmore_button',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show readmore button', 'W2MB'),
				'description' => esc_html__('Show read more button in InfoWindow.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'directions_sidebar_open',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1',),
				'heading' => esc_html__('Directions sidebar opened by default.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'geolocation',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('GeoLocation', 'W2MB'),
				'description' => esc_html__('Geolocate user and center map. Requires https.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'ajax_loading',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('AJAX loading', 'W2MB'),
				'description' => esc_html__('When map contains lots of markers - this may slow down map markers loading. Select AJAX to speed up loading. Requires Starting Address or Starting Point coordinates Latitude and Longitude.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'ajax_markers_loading',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Maps info window AJAX loading', 'W2MB'),
				'description' => esc_html__('This may additionaly speed up loading.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'use_ajax_loader',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Show spinner on AJAX requests.', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'start_address',
				'heading' => esc_html__('Starting Address', 'W2MB'),
				'description' => esc_html__('When map markers load by AJAX - it should have starting point and starting zoom. Enter start address or select latitude and longitude (recommended). Example: 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'start_latitude',
				'heading' => esc_html__('Starting Point Latitude', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'start_longitude',
				'heading' => esc_html__('Starting Point Longitude', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'start_zoom',
				'heading' => esc_html__('Default zoom', 'W2MB'),
				'value' => array(esc_html__("Auto", "W2MB") => '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19'),
				'std' => '0',
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'counter',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show locations counter', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'counter_text',
				'heading' => esc_html__('Counter text', 'W2MB'),
				'description' => esc_html__('Example: Number of locations %d', 'W2MB'),
				'std' => esc_html__('Number of locations %d', 'W2MB'),
		),
		array(
				'type' => 'ordering',
				'param_name' => 'order_by',
				'heading' => esc_html__('Order by', 'W2MB'),
				'description' => esc_html__('Order listings by any of these parameter.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'order',
				'value' => array(esc_html__('Ascending', 'W2MB') => 'ASC', esc_html__('Descending', 'W2MB') => 'DESC'),
				'description' => esc_html__('Direction of sorting.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'search_on_map',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Show search form and listings panel on the map', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'search_on_map_open',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Search form open by default', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_keywords_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show keywords search?', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'keywords_ajax_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Enable listings autosuggestions by keywords', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		/* array(
				'type' => 'textfield',
				'param_name' => 'what_search',
				'heading' => esc_html__('Default keywords', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		), */
		array(
				'type' => 'textfield',
				'param_name' => 'keywords_placeholder',
				'heading' => esc_html__('Keywords placeholder', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_categories_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show categories search?', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'categories_search_level',
				'value' => array('1', '2', '3'),
				'std' => '2',
				'heading' => esc_html__('Categories search depth level in search', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'categoryfield',
				'param_name' => 'category',
				'heading' => esc_html__('Select certain category in search', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'categoriesfield',
				'param_name' => 'exact_categories',
				'heading' => esc_html__('List of categories in search', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_locations_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show locations search?', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'locations_search_level',
				'value' => array('1', '2', '3'),
				'std' => '2',
				'heading' => esc_html__('Locations search depth level', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'locationfield',
				'param_name' => 'location',
				'heading' => esc_html__('Select certain location on the search form.', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'locationsfield',
				'param_name' => 'exact_locations',
				'heading' => esc_html__('List of locations on the search form.', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_address_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show address search', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'address',
				'heading' => esc_html__('Default address in the search form, recommended to set default radius.', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'address_placeholder',
				'heading' => esc_html__('Adress placeholder', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'show_radius_search',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show locations radius search', 'W2MB'),
				'dependency' => array('element' => 'search_on_map', 'value' => '1'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'radius',
				'heading' => esc_html__('Default radius search. Display listings near provided address within this radius in miles or kilometers.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'radius_circle',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Show radius circle', 'W2MB'),
				'description' => esc_html__('Display radius circle on map when radius filter provided.', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'clusters',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Group map markers in clusters', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'enable_full_screen',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Enable full screen button', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'enable_full_screen_by_default',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Map full screen opened by default', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'enable_wheel_zoom',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Enable zoom by mouse wheel', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'enable_dragging_touchscreens',
				'value' => array(esc_html__('Yes', 'W2MB') => '1', esc_html__('No', 'W2MB') => '0'),
				'heading' => esc_html__('Enable map dragging on touch screen devices', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'center_map_onclick',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Center map on marker click', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'zoom_map_onclick',
				'heading' => esc_html__('Zoom map on marker click', 'W2MB'),
				'description' => esc_html__('Does not work on AJAX loading maps', 'W2MB'),
				'value' => array(esc_html__("Auto", "W2MB") => '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19'),
				'std' => '0',
		),
		array(
				'type' => 'textfield',
				'param_name' => 'author',
				'heading' => esc_html__('Author', 'W2MB'),
				'description' => esc_html__('Enter ID of author', 'W2MB'),
		),
		array(
				'type' => 'categoriesfield',
				'param_name' => 'categories',
				'heading' => esc_html__('Select listings categories to display on map', 'W2MB'),
		),
		array(
				'type' => 'locationsfield',
				'param_name' => 'locations',
				'heading' => esc_html__('Select listings locations to display on map', 'W2MB'),
		),
		array(
				'type' => 'dropdown',
				'param_name' => 'include_categories_children',
				'value' => array(esc_html__('No', 'W2MB') => '0', esc_html__('Yes', 'W2MB') => '1'),
				'heading' => esc_html__('Include children of selected categories and locations', 'W2MB'),
				'description' => esc_html__('When enabled - any subcategories or sublocations will be included as well.', 'W2MB'),
		),
		array(
				'type' => 'textfield',
				'param_name' => 'post__in',
				'heading' => esc_html__('Exact listings', 'W2MB'),
				'description' => esc_html__('Comma separated string of listings IDs. Possible to display exact listings.', 'W2MB'),
		),
);

class w2mb_map_widget extends w2mb_widget {

	public function __construct() {
		global $w2mb_instance, $w2mb_map_widget_params;

		parent::__construct(
				'w2mb_map_widget',
				esc_html__('MapBox locator - Map', 'W2MB'),
				esc_html__('Map', 'W2MB')
		);

		foreach ($w2mb_instance->search_fields->filter_fields_array AS $filter_field) {
			if (method_exists($filter_field, 'getVCParams') && ($field_params = $filter_field->getVCParams())) {
				$w2mb_map_widget_params = array_merge($w2mb_map_widget_params, $field_params);
			}
		}

		$this->convertParams($w2mb_map_widget_params);
	}
	
	public function render_widget($instance, $args) {
		global $w2mb_instance;
		
		$instance['include_get_params'] = 0;
	
		$title = apply_filters('widget_title', $instance['title']);
	
		echo $args['before_widget'];
		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="w2mb-content w2mb-widget w2mb-map-widget">';
		$controller = new w2mb_map_controller();
		$controller->init($instance);
		echo $controller->display();
		echo '</div>';
		echo $args['after_widget'];
	}
}
?>