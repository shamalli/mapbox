<?php


return array(
						array(
								'type' => 'toggle',
								'name' => 'show_keywords_search',
								'label' => esc_html__('Enable keywords search', 'W2MB'),
								'default' => 1
						),
						array(
								'type' => 'toggle',
								'name' => 'keywords_ajax_search',
								'label' => esc_html__('Enable listings autosuggestions by keywords', 'W2MB'),
								'default' => 1
						),
						array(
								'type' => 'textbox',
								'name' => 'keywords_placeholder',
								'label' => esc_html__('Default keywords', 'W2MB'),
								'default' => '',
						),
						array(
								'type' => 'toggle',
								'name' => 'show_categories_search',
								'label' => esc_html__('Enable categories search', 'W2MB'),
								'default' => 1
						),
						array(
								'type' => 'radiobutton',
								'name' => 'categories_search_level',
								'label' => esc_html__('Categories search depth level', 'W2MB'),
								'items' => array(
										array(
												'value' => '1',
												'label' => '1',
										),
										array(
												'value' => '2',
												'label' => '2',
										),
										array(
												'value' => '3',
												'label' => '3',
										),
								),
								'default' => array('1')
						),
						array(
								'type' => 'select',
								'name' => 'category',
								'label' => esc_html__('Select certain category in search', 'W2MB'),
								'items' => w2mb_getMetaboxOptionsTerms(W2MB_CATEGORIES_TAX)
						),
						array(
								'type' => 'multiselect',
								'name' => 'exact_categories',
								'label' => esc_html__('List of categories in search', 'W2MB'),
								'items' => w2mb_getMetaboxOptionsTerms(W2MB_CATEGORIES_TAX)
						),
						array(
								'type' => 'toggle',
								'name' => 'show_locations_search',
								'label' => esc_html__('Enable locations search', 'W2MB'),
								'default' => 1
						),
						array(
								'type' => 'radiobutton',
								'name' => 'locations_search_level',
								'label' => esc_html__('Locations search depth level', 'W2MB'),
								'items' => array(
										array(
												'value' => '1',
												'label' => '1',
										),
										array(
												'value' => '2',
												'label' => '2',
										),
										array(
												'value' => '3',
												'label' => '3',
										),
								),
								'default' => array('1')
						),
						array(
								'type' => 'select',
								'name' => 'location',
								'label' => esc_html__('Select certain location in search', 'W2MB'),
								'items' => w2mb_getMetaboxOptionsTerms(W2MB_LOCATIONS_TAX)
						),
						array(
								'type' => 'multiselect',
								'name' => 'exact_locations',
								'label' => esc_html__('List of locations in search', 'W2MB'),
								'items' => w2mb_getMetaboxOptionsTerms(W2MB_LOCATIONS_TAX)
						),
						array(
								'type' => 'toggle',
								'name' => 'show_address_search',
								'label' => esc_html__('Enable address search', 'W2MB'),
								'default' => 1
						),
						array(
								'type' => 'textbox',
								'name' => 'address_placeholder',
								'label' => esc_html__('Default address', 'W2MB'),
								'default' => '',
						),
						array(
								'type' => 'toggle',
								'name' => 'show_radius_search',
								'label' => esc_html__('Enable radius search', 'W2MB'),
								'default' => 1
						),
				);

?>