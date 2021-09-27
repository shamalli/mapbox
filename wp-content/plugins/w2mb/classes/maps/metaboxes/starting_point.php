<?php

return array(
						array(
							'type' => 'toggle',
							'name' => 'use_starting_point',
							'label' => esc_html__('Use map starting point and zoom', 'W2MB'),
							'description' => esc_html__('Use map starting point or uncheck to fit map bounds showing all markers on load', 'W2MB'),
							'default' => 0,
						),
						array(
							'type' => 'textbox',
							'name' => 'start_latitude',
							'label' => esc_html__('Starting Point Latitude', 'W2MB'),
						),
						array(
							'type' => 'textbox',
							'name' => 'start_longitude',
							'label' => esc_html__('Starting Point Longitude', 'W2MB'),
						),
						array(
							'type' => 'select',
							'name' => 'start_zoom',
							'label' => esc_html__('Starting zoom', 'W2MB'),
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
								'type' => 'textbox',
								'name' => 'radius',
								'label' => sprintf(esc_html__('Show markers in radius (%s)', 'W2MB'), get_option('w2mb_miles_kilometers_in_search')),
								'description' => esc_html__('This will display ONLY markers around starting point', 'W2MB'),
								'default' => '10'
						),
						array(
								'type' => 'toggle',
								'name' => 'radius_circle',
								'label' => esc_html__('Show radius circle', 'W2MB'),
								'description' => esc_html__('Display radius circle on map when radius filter provided', 'W2MB'),
								'default' => 1
						),
				);

?>