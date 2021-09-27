<?php

return array(
						array(
								'type' => 'toggle',
								'name' => 'search_on_map',
								'label' => esc_html__('Enable search and listings sidebar', 'W2MB'),
								'default' => 1
						),
						array(
								'type' => 'toggle',
								'name' => 'search_on_map_open',
								'label' => esc_html__('Listings sidebar open by default', 'W2MB'),
								'default' => 0
						),
						array(
								'type' => 'select',
								'name' => 'order_by',
								'label' => esc_html__('Listings order by', 'W2MB'),
								'items' => w2mb_orderingItems(),
								'default' => array('post_date'),
						),
						array(
								'type' => 'select',
								'name' => 'order',
								'label' => esc_html__('Order direction', 'W2MB'),
								'items' => array(
										array(
												'value' => 'ASC',
												'label' => esc_html__('Ascending', 'W2MB'),
										),
										array(
												'value' => 'DESC',
												'label' => esc_html__('Descending', 'W2MB'),
										),
								),
								'default' => array('DESC'),
						),
				);

?>