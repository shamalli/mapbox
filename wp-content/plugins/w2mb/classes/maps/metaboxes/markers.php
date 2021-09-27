<?php

$map_params = array(
		array(
				'type' => 'multiselect',
				'name' => 'categories',
				'label' => esc_html__('Select specific categories to display on the map', 'W2MB'),
				'items' => w2mb_getMetaboxOptionsTerms(W2MB_CATEGORIES_TAX)
		),
		array(
				'type' => 'multiselect',
				'name' => 'locations',
				'label' => esc_html__('Select specific locations to display on the map', 'W2MB'),
				'items' => w2mb_getMetaboxOptionsTerms(W2MB_LOCATIONS_TAX)
		),
		array(
				'type' => 'toggle',
				'name' => 'include_categories_children',
				'label' => esc_html__('Include categories and locations children', 'W2MB'),
				'description' => esc_html__('Include markers of all children of selected categories and locations, not only selected items', 'W2MB'),
				'default' => 0
		),
		array(
				'type' => 'textbox',
				'name' => 'author',
				'label' => esc_html__('Enter ID of author', 'W2MB'),
				'default' => ''
		),
		array(
				'type' => 'textbox',
				'name' => 'post__in',
				'label' => esc_html__('Exact listings', 'W2MB'),
				'description' => esc_html__('Comma separated string of listings IDs. Possible to display exact listings', 'W2MB'),
				'default' => ''
		),
);

global $w2mb_instance;

foreach ($w2mb_instance->search_fields->filter_fields_array AS $filter_field) {
	if (method_exists($filter_field, 'getMapManagerParams') && ($field_params = $filter_field->getMapManagerParams())) {
		$map_params = array_merge($map_params, $field_params);
	}
}

return $map_params;

?>