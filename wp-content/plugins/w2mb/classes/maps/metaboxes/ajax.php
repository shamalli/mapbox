<?php

return array(
		array(
				'type' => 'toggle',
				'name' => 'ajax_loading',
				'label' => esc_html__('AJAX loading', 'W2MB'),
				'description' => esc_html__('When map contains lots of markers - this may slow down map markers loading. Select AJAX to speed up loading. Requires Starting Point coordinates Latitude and Longitude', 'W2MB'),
				'default' => 0
		),
		array(
				'type' => 'toggle',
				'name' => 'ajax_markers_loading',
				'label' => esc_html__('Maps info window AJAX loading', 'W2MB'),
				'description' => esc_html__('This may additionaly speed up loading', 'W2MB'),
				'default' => 0
		),
		array(
				'type' => 'toggle',
				'name' => 'optimize_ajax_loading',
				'label' => esc_html__('Speed up loading on AJAX', 'W2MB'),
				'description' => esc_html__('Custom markers icons will be disabled in this mode. Only default icons will be used. No images at the listings sidebar.', 'W2MB'),
				'default' => 0
		),
		array(
				'type' => 'toggle',
				'name' => 'use_ajax_loader',
				'label' => esc_html__('Show spinner on AJAX requests', 'W2MB'),
				'default' => 0
		),
);

?>