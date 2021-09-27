<?php

return array(
						array(
							'type' => 'toggle',
							'name' => 'draw_panel',
							'label' => esc_html__('Enable Draw Panel', 'W2MB'),
							'description' => esc_html__('Very important: MySQL version must be 5.6.1 and higher or MySQL server variable "thread stack" must be 256K and higher. Ask your hoster about it if "Draw Area" does not work.', 'W2MB'),
							'default' => 0
						),
						array(
							'type' => 'toggle',
							'name' => 'enable_full_screen',
							'label' => esc_html__('Enable full screen button', 'W2MB'),
							'default' => 1
						),
						array(
							'type' => 'toggle',
							'name' => 'enable_full_screen_by_default',
							'label' => esc_html__('Map full screen opened by default', 'W2MB'),
							'default' => 0
						),
						array(
							'type' => 'toggle',
							'name' => 'enable_wheel_zoom',
							'label' => esc_html__('Enable zoom by mouse wheel', 'W2MB'),
							'default' => 1
						),
						array(
							'type' => 'toggle',
							'name' => 'enable_dragging_touchscreens',
							'label' => esc_html__('Enable map dragging on touch screen devices', 'W2MB'),
							'default' => 1
						),
						array(
							'type' => 'toggle',
							'name' => 'center_map_onclick',
							'label' => esc_html__('Center map on marker click', 'W2MB'),
							'description' => esc_html__('Does not work on AJAX loading maps', 'W2MB'),
							'default' => 0
						),
						array(
							'type' => 'select',
							'name' => 'zoom_map_onclick',
							'label' => esc_html__('Zoom map on marker click', 'W2MB'),
							'description' => esc_html__('Does not work on AJAX loading maps', 'W2MB'),
							'items' => array(
								array(
									'value' => '0',
									'label' => esc_html__('Auto', 'W2MB'),
								),
								array('value' => '1', 'label' => '1'),
								array('value' => '2', 'label' => '2'),
								array('value' => '3', 'label' => '3'),
								array('value' => '4', 'label' => '4'),
								array('value' => '5', 'label' => '5'),
								array('value' => '6', 'label' => '6'),
								array('value' => '7', 'label' => '7'),
								array('value' => '8', 'label' => '8'),
								array('value' => '9', 'label' => '9'),
								array('value' => '10', 'label' => '10'),
								array('value' => '11', 'label' => '11'),
								array('value' => '12', 'label' => '12'),
								array('value' => '13', 'label' => '13'),
								array('value' => '14', 'label' => '14'),
								array('value' => '15', 'label' => '15'),
								array('value' => '16', 'label' => '16'),
								array('value' => '17', 'label' => '17'),
								array('value' => '18', 'label' => '18'),
								array('value' => '19', 'label' => '19'),
							),
							'default' => array('0'),
						),
						array(
								'type' => 'toggle',
								'name' => 'counter',
								'label' => esc_html__('Show locations counter', 'W2MB'),
								'default' => 1
						),
						array(
								'type' => 'textbox',
								'name' => 'counter_text',
								'label' => esc_html__('Counter text', 'W2MB'),
								'description' => esc_html__('Example: Number of locations %d', 'W2MB'),
								'default' => 'Number of locations %d'
						),
				);

?>