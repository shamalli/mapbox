<?php 

class w2mb_locations_manager {
	
	public function __construct() {
		add_action('add_meta_boxes', array($this, 'removeLocationsMetabox'));
		add_action('add_meta_boxes', array($this, 'addLocationsMetabox'), 300);
		
		add_action('wp_ajax_w2mb_tax_dropdowns_hook', 'w2mb_tax_dropdowns_updateterms');
		add_action('wp_ajax_nopriv_w2mb_tax_dropdowns_hook', 'w2mb_tax_dropdowns_updateterms');

		add_action('wp_ajax_w2mb_add_location_in_metabox', array($this, 'add_location_in_metabox'));
		add_action('wp_ajax_nopriv_w2mb_add_location_in_metabox', array($this, 'add_location_in_metabox'));

		add_action('wp_ajax_w2mb_select_map_icon', array($this, 'select_map_icon'));
		add_action('wp_ajax_nopriv_w2mb_select_map_icon', array($this, 'select_map_icon'));

		if (w2mb_isListingEditPageInAdmin()) {
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'));
		}
		if (w2mb_isLocationsEditPageInAdmin()) {
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_location_edit_scripts'));
		}

		add_filter('manage_' . W2MB_LOCATIONS_TAX . '_custom_column', array($this, 'taxonomy_rows'), 15, 3);
		add_filter('manage_edit-' . W2MB_LOCATIONS_TAX . '_columns',  array($this, 'taxonomy_columns'));
		add_action(W2MB_LOCATIONS_TAX . '_edit_form_fields', array($this, 'edit_tag_form'));

		add_action('wp_ajax_w2mb_select_location_icon_dialog', array($this, 'select_location_icon_dialog'));
		add_action('wp_ajax_w2mb_select_location_icon', array($this, 'select_location_icon'));
	}
	
	// remove native locations taxonomy metabox from sidebar
	public function removeLocationsMetabox() {
		remove_meta_box(W2MB_LOCATIONS_TAX . 'div', W2MB_POST_TYPE, 'side');
	}
	
	public function addLocationsMetabox($post_type) {
		if ($post_type == W2MB_POST_TYPE && ($level = w2mb_getCurrentListingInAdmin()->level) && $level->locations_number > 0) {
			add_meta_box('w2mb_locations',
					esc_html__('Listing locations', 'W2MB'),
					array($this, 'listingLocationsMetabox'),
					W2MB_POST_TYPE,
					'normal',
					'high');
		}
	}

	public function listingLocationsMetabox($post) {
		global $w2mb_instance;
			
		$listing = w2mb_getCurrentListingInAdmin();
		$locations_levels = $w2mb_instance->locations_levels;
		w2mb_renderTemplate('locations/locations_metabox.tpl.php', array('listing' => $listing, 'locations_levels' => $locations_levels));
	}
	
	public function add_location_in_metabox() {
		global $w2mb_instance;
			
		if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
			$listing = new w2mb_listing();
			$listing->loadListingFromPost($_POST['post_id']);
	
			$locations_levels = $w2mb_instance->locations_levels;
			w2mb_renderTemplate('locations/locations_in_metabox.tpl.php', array('listing' => $listing, 'location' => new w2mb_location, 'locations_levels' => $locations_levels, 'delete_location_link' => true));
		}
		die();
	}
	
	public function select_map_icon() {
		$custom_map_icons = array();
	
		$custom_map_icons_themes = scandir(W2MB_MAP_ICONS_PATH . 'icons/');
		foreach ($custom_map_icons_themes AS $dir) {
			if (is_dir(W2MB_MAP_ICONS_PATH . 'icons/' . $dir) && $dir != '.' && $dir != '..') {
				$custom_map_icons_theme_files = scandir(W2MB_MAP_ICONS_PATH . 'icons/' . $dir);
				foreach ($custom_map_icons_theme_files AS $file) {
					if (is_file(W2MB_MAP_ICONS_PATH . 'icons/' . $dir . '/' . $file) && $file != '.' && $file != '..') {
						$custom_map_icons[$dir][] = $file;
					}
				}
			} elseif (is_file(W2MB_MAP_ICONS_PATH . 'icons/' . $dir) && $dir != '.' && $dir != '..') {
				$custom_map_icons[''][] = $dir;
			}
		}
	
		w2mb_renderTemplate('locations/select_icons.tpl.php', array('custom_map_icons' => $custom_map_icons));
		die();
	}

	public function validateLocations($level, &$errors) {
		global $w2mb_instance;

		$validation = new w2mb_form_validation();
		$validation->set_rules('w2mb_location[]', esc_html__('Listing location', 'W2MB'), 'is_natural');
		$validation->set_rules('selected_tax[]', esc_html__('Selected location', 'W2MB'), 'is_natural');
		$validation->set_rules('address_line_1[]', esc_html__('Address line 1', 'W2MB'));
		$validation->set_rules('address_line_2[]', esc_html__('Address line 2', 'W2MB'));
		$validation->set_rules('zip_or_postal_index[]', esc_html__('Zip code', 'W2MB'));
		$validation->set_rules('additional_info[]', esc_html__('Additional info', 'W2MB'));
		$validation->set_rules('manual_coords[]', esc_html__('Use manual coordinates', 'W2MB'), 'is_checked');
		$validation->set_rules('map_coords_1[]', esc_html__('Latitude', 'W2MB'), 'numeric');
		$validation->set_rules('map_coords_2[]', esc_html__('Longitude', 'W2MB'), 'numeric');
		$validation->set_rules('map_icon_file[]', esc_html__('Map icon file', 'W2MB'));
		$validation->set_rules('map_zoom', esc_html__('Map zoom', 'W2MB'), 'is_natural');
	
		if (!$validation->run()) {
			$errors[] = $validation->error_array();
		} else {
			$passed = true;
			if ($w2mb_instance->content_fields->getContentFieldBySlug('address')->is_required) {
				$address_passed = true;
				if ($validation_results = $validation->result_array()) {
					foreach ($validation_results['w2mb_location[]'] AS $key=>$value) {
						if (!$validation_results['selected_tax[]'][$key] && !$validation_results['address_line_1[]'][$key] && !$validation_results['zip_or_postal_index[]'][$key])
							$address_passed = false;
					}
				}
				if (!$address_passed) {
					$errors[] = esc_html__('Location, address or zip is required!', 'W2MB');
					$passed = false;
				}
			}
				
			$map_passed = false;
			if ($validation_results = $validation->result_array()) {
				foreach ($validation_results['w2mb_location[]'] AS $key=>$value) {
					if ($validation_results['map_coords_1[]'][$key] || $validation_results['map_coords_2[]'][$key])
						$map_passed = true;
				}
			}
			if (!$map_passed) {
				$errors[] = esc_html__('Listing must contain at least one map marker!', 'W2MB');
				$passed = false;
			}

			return $validation->result_array();
		}
	}
	
	public function saveLocations($level, $post_id, $validation_results) {
		global $wpdb;
	
		$this->deleteLocations($post_id);
	
		if (isset($validation_results['w2mb_location[]'])) {
			// remove unauthorized locations
			$validation_results['w2mb_location[]'] = array_slice($validation_results['w2mb_location[]'], 0, $level->locations_number, true);
	
			foreach ($validation_results['w2mb_location[]'] AS $key=>$value) {
				if (
					$validation_results['selected_tax[]'][$key] ||
					$validation_results['address_line_1[]'][$key] ||
					$validation_results['address_line_2[]'][$key] ||
					$validation_results['zip_or_postal_index[]'][$key] ||
					($validation_results['map_coords_1[]'][$key] || $validation_results['map_coords_2[]'][$key])
				) {
					$insert_values = array(
							'post_id' => $post_id,
							'location_id' => esc_sql($validation_results['selected_tax[]'][$key]),
							'address_line_1' => esc_sql($validation_results['address_line_1[]'][$key]),
							'address_line_2' => esc_sql($validation_results['address_line_2[]'][$key]),
							'zip_or_postal_index' => esc_sql($validation_results['zip_or_postal_index[]'][$key]),
							'additional_info' => (isset($validation_results['additional_info[]'][$key]) ? esc_sql($validation_results['additional_info[]'][$key]) : ''),
					);
					if (is_array($validation_results['manual_coords[]'])) {
						if (in_array($key, array_keys($validation_results['manual_coords[]'])) && $validation_results['manual_coords[]'][$key])
							$insert_values['manual_coords'] = 1;
						else
							$insert_values['manual_coords'] = 0;
					} else
						$insert_values['manual_coords'] = 0;
					$insert_values['map_coords_1'] = $validation_results['map_coords_1[]'][$key];
					$insert_values['map_coords_2'] = $validation_results['map_coords_2[]'][$key];
					if ($level->map_markers) {
						$map_icon_file = $validation_results['map_icon_file[]'][$key];
						$map_icon_file = str_replace('w2gm', 'w2mb', $map_icon_file);
						
						$insert_values['map_icon_file'] = esc_sql($map_icon_file);
					}
					$keys = array_keys($insert_values);

					// duplicate locations data in postmeta in order to export it as ordinary wordpress fields
					foreach ($keys AS $key) {
						if ($key != 'post_id') {
							add_post_meta($post_id, '_'.$key, $insert_values[$key]);
						}
					}
					
					array_walk($keys, 'w2mb_wrapKeys');
					array_walk($insert_values, 'w2mb_wrapValues');
					$wpdb->query("INSERT INTO {$wpdb->w2mb_locations_relationships} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $insert_values) . ")");
				}
			}

			if ($validation_results['selected_tax[]']) {
				array_walk($validation_results['selected_tax[]'], 'w2mb_wrapIntVal');
				wp_set_object_terms($post_id, $validation_results['selected_tax[]'], W2MB_LOCATIONS_TAX);
			}
	
			add_post_meta($post_id, '_map_zoom', $validation_results['map_zoom']);
		}
	}

	public function deleteLocations($post_id) {
		global $wpdb;

		$wpdb->delete($wpdb->w2mb_locations_relationships, array('post_id' => $post_id));
		wp_delete_object_term_relationships($post_id, W2MB_LOCATIONS_TAX);
		delete_post_meta($post_id, '_location_id');
		delete_post_meta($post_id, '_address_line_1');
		delete_post_meta($post_id, '_address_line_2');
		delete_post_meta($post_id, '_zip_or_postal_index');
		delete_post_meta($post_id, '_additional_info');
		delete_post_meta($post_id, '_manual_coords');
		delete_post_meta($post_id, '_map_coords_1');
		delete_post_meta($post_id, '_map_coords_2');
		delete_post_meta($post_id, '_map_icon_file');
		delete_post_meta($post_id, '_map_zoom');
	}
	
	public function taxonomy_columns($original_columns) {
		$new_columns = $original_columns;
		array_splice($new_columns, 1);
		$new_columns['w2mb_location_id'] = esc_html__('Location ID', 'W2MB');
		$new_columns['w2mb_location_icon'] = esc_html__('Icon', 'W2MB');
		if (isset($original_columns['description']))
			unset($original_columns['description']);
		return array_merge($new_columns, $original_columns);
	}
	
	public function taxonomy_rows($row, $column_name, $term_id) {
		if ($column_name == 'w2mb_location_id') {
			return $row . $term_id;
		}
		if ($column_name == 'w2mb_location_icon') {
			return $row . $this->choose_icon_link($term_id);
		}
		return $row;
	}
	
	public function edit_tag_form($term) {
		w2mb_renderTemplate('locations/select_icon_form.tpl.php', array('term' => $term));
	}
	
	public function choose_icon_link($term_id) {
		$icon_file = $this->getLocationIconFile($term_id);
		w2mb_renderTemplate('locations/select_icon_link.tpl.php', array('term_id' => $term_id, 'icon_file' => $icon_file));
	}
	
	public function getLocationIconFile($term_id) {
		return w2mb_getLocationIconFile($term_id);
	}
	
	public function select_location_icon_dialog() {
		$locations_icons = array();
	
		$locations_icons_files = scandir(W2MB_LOCATION_ICONS_PATH);
		foreach ((array) $locations_icons_files AS $file)
			if (is_file(W2MB_LOCATION_ICONS_PATH . $file) && $file != '.' && $file != '..')
				$locations_icons[] = $file;
	
		w2mb_renderTemplate('locations/select_icons_dialog.tpl.php', array('locations_icons' => $locations_icons));
		die();
	}
	
	public function select_location_icon() {
		if (isset($_POST['location_id']) && is_numeric($_POST['location_id'])) {
			$location_id = intval($_POST['location_id']);
			$icons = (array) get_option('w2mb_locations_icons');
			if (isset($_POST['icon_file']) && $_POST['icon_file']) {
				$icon_file = $_POST['icon_file'];
				if (is_file(W2MB_LOCATION_ICONS_PATH . $icon_file)) {
					$icons[$location_id] = $icon_file;
					update_option('w2mb_locations_icons', $icons);
					echo $location_id;
				}
			} else {
				if (isset($icons[$location_id]))
					unset($icons[$location_id]);
				update_option('w2mb_locations_icons', $icons);
			}
		}
		die();
	}
	
	public function admin_enqueue_scripts_styles() {
		wp_localize_script(
				'w2mb_js_functions',
				'w2mb_maps_callback',
				array(
						'callback' => 'w2mb_load_maps_api_backend'
				)
		);
	}
	
	public function admin_enqueue_location_edit_scripts() {
		wp_enqueue_script('w2mb_locations_edit_scripts');
		wp_localize_script(
				'w2mb_locations_edit_scripts',
				'locations_icons',
				array(
						'locations_icons_url' => W2MB_LOCATIONS_ICONS_URL,
						'ajax_dialog_title' => esc_html__('Select location icon', 'W2MB')
				)
		);
	}
}

?>