<?php

add_action('vc_before_init', 'w2mb_vc_init');

function w2mb_vc_init() {
	global $w2mb_instance, $w2mb_fsubmit_instance;
	
	if (!isset($w2mb_instance->content_fields)) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		return ;
	}

	if (!function_exists('w2mb_ordering_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		vc_add_shortcode_param('ordering', 'w2mb_ordering_param');
		function w2mb_ordering_param($settings, $value) {
			$ordering = w2mb_orderingItems();

			$out = '<select id="' . esc_attr($settings['param_name']) . '" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value">';
			foreach ($ordering AS $ordering_item) {
				$out .= '<option value="' . esc_attr($ordering_item['value']) . '" ' . selected($value, $ordering_item['value'], false) . '>' . esc_attr($ordering_item['label']) . '</option>';
			}
			$out .= '</select>';
	
			return $out;
		}
	}

	if (!function_exists('w2mb_mapstyle_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		vc_add_shortcode_param('mapstyle', 'w2mb_mapstyle_param');
		function w2mb_mapstyle_param($settings, $value) {
			$out = '<select id="' . esc_attr($settings['param_name']) . '" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value">';
			$out .= '<option value="0" ' . ((!$value) ? 'selected' : 0) . '>' . esc_html__('Default', 'W2MB') . '</option>';
			$map_styles = array('default' => '');
			foreach (w2mb_getAllMapStyles() AS $name=>$style) {
				$out .= '<option value="' . $name . '" ' . selected($value, $name, false) . '>' . $name . '</option>';
			}
			$out .= '</select>';
	
			return $out;
		}
	}

	if (!function_exists('w2mb_categories_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		vc_add_shortcode_param('categoriesfield', 'w2mb_categories_param');
		function w2mb_categories_param($settings, $value) {
			$out = "<script>
				function updateTagChecked_categories() { jQuery('#" . $settings['param_name'] . "').val(jQuery('#" . $settings['param_name'] . "_select').val()); }
		
				jQuery(function() {
					jQuery(document).on('click', '#" . $settings['param_name'] . "_select option', updateTagChecked_categories);
					updateTagChecked_categories();
				});
			</script>";
		
			$out .= '<select multiple="multiple" id="' . esc_attr($settings['param_name']) . '_select" name="' . esc_attr($settings['param_name']) . '_select" class="w2mb-select-height-300">';
			$out .= '<option value="" ' . ((!$value) ? 'selected' : '') . '>' . esc_html__('- Select All -', 'W2MB') . '</option>';
			ob_start();
			w2mb_renderOptionsTerms(W2MB_CATEGORIES_TAX, 0, explode(',', $value));
			$out .= ob_get_clean();
			$out .= '</select>';
			$out .= '<input type="hidden" id="' . esc_attr($settings['param_name']) . '" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value" value="' . $value . '" />';
		
			return $out;
		}
	}

	if (!function_exists('w2mb_category_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		vc_add_shortcode_param('categoryfield', 'w2mb_category_param');
		function w2mb_category_param($settings, $value) {
			$out = '<select id="' . esc_attr($settings['param_name']) . '" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value">';
			$out .= '<option value="" ' . ((!$value) ? 'selected' : '') . '>' . esc_html__('- No category selected -', 'W2MB') . '</option>';
			ob_start();
			w2mb_renderOptionsTerms(W2MB_CATEGORIES_TAX, 0, array($value));
			$out .= ob_get_clean();
			$out .= '</select>';
		
			return $out;
		}
	}

	if (!function_exists('w2mb_locations_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		vc_add_shortcode_param('locationsfield', 'w2mb_locations_param');
		function w2mb_locations_param($settings, $value) {
			$out = "<script>
				function updateTagChecked_locations() { jQuery('#" . $settings['param_name'] . "').val(jQuery('#" . $settings['param_name'] . "_select').val()); }
		
				jQuery(function() {
					jQuery(document).on('click', '#" . $settings['param_name'] . "_select option', updateTagChecked_locations);
					updateTagChecked_locations();
				});
			</script>";
		
			$out .= '<select multiple="multiple" id="' . esc_attr($settings['param_name']) . '_select" name="' . esc_attr($settings['param_name']) . '_select" class="w2mb-select-height-300">';
			$out .= '<option value="" ' . ((!$value) ? 'selected' : '') . '>' . esc_html__('- Select All -', 'W2MB') . '</option>';
			ob_start();
			w2mb_renderOptionsTerms(W2MB_LOCATIONS_TAX, 0, explode(',', $value));
			$out .= ob_get_clean();
			$out .= '</select>';
			$out .= '<input type="hidden" id="' . esc_attr($settings['param_name']) . '" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value" value="' . $value . '" />';
		
			return $out;
		}
	}

	if (!function_exists('w2mb_location_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		vc_add_shortcode_param('locationfield', 'w2mb_location_param');
		function w2mb_location_param($settings, $value) {
			$out = '<select id="' . esc_attr($settings['param_name']) . '" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value">';
			$out .= '<option value="" ' . ((!$value) ? 'selected' : '') . '>' . esc_html__('- No location selected -', 'W2MB') . '</option>';
			ob_start();
			w2mb_renderOptionsTerms(W2MB_LOCATIONS_TAX, 0, array($value));
			$out .= ob_get_clean();
			$out .= '</select>';
		
			return $out;
		}
	}

	if (!function_exists('w2mb_content_fields_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		vc_add_shortcode_param('contentfields', 'w2mb_content_fields_param');
		function w2mb_content_fields_param($settings, $value) {
			global $w2mb_instance;
			$out = "<script>
				function updateTagChecked_content_fields() { jQuery('#" . $settings['param_name'] . "').val(jQuery('#" . $settings['param_name'] . "_select').val()); }
		
				jQuery(function() {
					jQuery(document).on('click', '#" . $settings['param_name'] . "_select option', updateTagChecked_content_fields);
					updateTagChecked_content_fields();
				});
			</script>";

			$content_fields_ids = explode(',', $value);
			$out .= '<select multiple="multiple" id="' . esc_attr($settings['param_name']) . '_select" name="' . esc_attr($settings['param_name']) . '_select" class="w2mb-select-height-300">';
			$out .= '<option value="" ' . ((!$value) ? 'selected' : '') . '>' . esc_html__('- All content fields -', 'W2MB') . '</option>';
			$out .= '<option value="" ' . (($value == -1) ? 'selected' : '') . '>' . esc_html__('- No content fields -', 'W2MB') . '</option>';
			foreach ($w2mb_instance->search_fields->search_fields_array AS $search_field)
				$out .= '<option value="' . $search_field->content_field->id . '" ' . (in_array($search_field->content_field->id, $content_fields_ids) ? 'selected' : '') . '>' . $search_field->content_field->name . '</option>';
			$out .= '</select>';
			$out .= '<input type="hidden" id="' . esc_attr($settings['param_name']) . '" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value" value="' . $value . '" />';
		
			return $out;
		}
	}
	
	global $w2mb_levels_table_widget_params;
	if ($w2mb_fsubmit_instance) {
		$vc_submit_args = array(
			'name'                    => esc_html__('Listings submit', 'W2MB'),
			'description'             => esc_html__('Listings submission pages', 'W2MB'),
			'base'                    => 'mapbox-submit',
			'icon'                    => W2MB_RESOURCES_URL . 'images/mapbox.png',
			'show_settings_on_create' => false,
			'category'                => esc_html__('Maps Content', 'W2MB'),
			'params'                  => $w2mb_levels_table_widget_params
		);
		vc_map($vc_submit_args);

		vc_map( array(
			'name'                    => esc_html__('Users Dashboard', 'W2MB'),
			'description'             => esc_html__('Maps frontend dashboard', 'W2MB'),
			'base'                    => 'mapbox-dashboard',
			'icon'                    => W2MB_RESOURCES_URL . 'images/mapbox.png',
			'show_settings_on_create' => false,
			'category'                => esc_html__('Maps Content', 'W2MB'),
		));
		
		vc_map( array(
			'name'                    => esc_html__('Submit button', 'W2MB'),
			'description'             => esc_html__('Renders "Submit new listing" button', 'W2MB'),
			'base'                    => 'mapbox-submit-button',
			'icon'                    => W2MB_RESOURCES_URL . 'images/mapbox.png',
			'show_settings_on_create' => false,
			'category'                => esc_html__('Maps Content', 'W2MB'),
		));
	}

	global $w2mb_map_widget_params;
	$vc_maps_args = array(
			'name'                    => esc_html__('MapBox Map', 'W2MB'),
			'description'             => esc_html__('MapBox map and markers', 'W2MB'),
			'base'                    => 'mapbox',
			'icon'                    => W2MB_RESOURCES_URL . 'images/mapbox.png',
			'show_settings_on_create' => true,
			'category'                => esc_html__('Maps Content', 'W2MB'),
			'params'                  => $w2mb_map_widget_params
	);
	vc_map($vc_maps_args);

	global $w2mb_search_widget_params;
	$vc_search_args = array(
		'name'                    => esc_html__('Search form', 'W2MB'),
		'description'             => esc_html__('Maps listings search form', 'W2MB'),
		'base'                    => 'mapbox-search',
		'icon'                    => W2MB_RESOURCES_URL . 'images/mapbox.png',
		'show_settings_on_create' => false,
		'category'                => esc_html__('Maps Content', 'W2MB'),
		'params'                  => $w2mb_search_widget_params
	);
	vc_map($vc_search_args);

}

?>