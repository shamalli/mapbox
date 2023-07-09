<?php

class w2mb_csv_manager {
	public $menu_page_hook;
	
	public $test_mode = false;
	
	public $log = array('errors' => array(), 'messages' => array());
	public $header_columns = array();
	public $rows = array();
	public $collated_fields = array();
	
	public $csv_file_name;
	public $images_dir;
	public $import_type = 'create';
	public $columns_separator;
	public $values_separator;
	public $if_term_not_found;
	public $selected_user;
	public $do_geocode;
	public $is_claimable;
	
	public $collation_fields;
	
	public function __construct() {
		// Export
		if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'w2mb_csv_import' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'export_settings' && isset($_REQUEST['csv_export'])) {
			add_action('admin_init', array($this, 'csvExport'));
		}
		// Export Images
		if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'w2mb_csv_import' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'export_settings' && isset($_REQUEST['export_images'])) {
			add_action('admin_init', array($this, 'exportImages'));
		}

		add_action('admin_menu', array($this, 'menu'));
	}
	
	public function menu() {
		if (defined('W2MB_DEMO') && W2MB_DEMO) {
			$capability = 'publish_posts';
		} else {
			$capability = 'manage_options';
		}

		$this->menu_page_hook = add_submenu_page('w2mb_settings',
			esc_html__('CSV Import/Export', 'W2MB'),
			esc_html__('CSV Import/Export', 'W2MB'),
			$capability,
			'w2mb_csv_import',
			array($this, 'w2mb_csv_import')
		);
	}
	
	public function buildCollationColumns() {
		global $w2mb_instance;
		
		$this->collation_fields = array(
				'post_id' => esc_html__('Post ID* (existing listing)', 'W2MB'),
				'title' => esc_html__('Title*', 'W2MB'),
				'user' => esc_html__('Author', 'W2MB'),
				'status' => esc_html__('Status (active, expired, unpaid)', 'W2MB'),
				'categories_list' => esc_html__('Categories', 'W2MB'),
				'listing_tags' => esc_html__('Tags', 'W2MB'),
				'content' => esc_html__('Description', 'W2MB'),
				'excerpt' => esc_html__('Summary', 'W2MB'),
				'locations_list' => esc_html__('Locations (existing or new)', 'W2MB'),
				'address_line_1' => esc_html__('Address line 1', 'W2MB'),
				'address_line_2' => esc_html__('Address line 2', 'W2MB'),
				'zip' => esc_html__('Zip code or postal index', 'W2MB'),
				'latitude' => esc_html__('Latitude', 'W2MB'),
				'longitude' => esc_html__('Longitude', 'W2MB'),
				'map_icon_file' => esc_html__('Map icon file', 'W2MB'),
				'additional_address_info' => __('Additional info for map marker', 'W2MB'),
				'images' => esc_html__('Images files', 'W2MB'),
				'videos' => esc_html__('YouTube or Vimeo videos', 'W2MB'),
				'expiration_date' => esc_html__('Listing expiration date', 'W2MB'),
				'contact_email' => esc_html__('Listing contact email', 'W2MB'),
				'claimable' => esc_html__('Make listing claimable', 'W2MB'),
		);
		
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$this->collation_fields['wpml_translation_source_id'] = esc_html__('WPML translation source ID', 'W2MB');
		}
		
		$this->collation_fields = apply_filters('w2mb_csv_collation_fields_list', $this->collation_fields);
		
		foreach ($w2mb_instance->content_fields->content_fields_array AS $field) {
			if (!$field->is_core_field) {
				$this->collation_fields[$field->slug] = $field->name . ' ' . esc_html__("(content field)", "W2MB");
			}
		}
	}
	
	public function w2mb_csv_import() {
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'import_settings') {
			// 2nd Step
			$this->csvCollateColumns();
		} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'import_collate' && isset($_REQUEST['csv_file_name'])) {
			// 3rd Step
			$this->csvImport();
		} elseif (!isset($_REQUEST['action'])) {
			// 1st Step
			$this->csvImportSettings();
		}
	}
	
	// 1st Step
	public function csvImportSettings($vars = array()) {

		w2mb_renderTemplate('csv_manager/import_settings.tpl.php', $vars);
	}

	// 2nd Step
	public function csvCollateColumns() {
		$this->buildCollationColumns();
		$users = get_users(array('orderby' => 'ID', 'fields' => array('ID', 'user_login')));
		
		// test geolocation
		$w2mb_locationGeoname = new w2mb_locationGeoname();
		$geolocation_response = $w2mb_locationGeoname->geocodeRequest('1600 Amphitheatre Parkway Mountain View, CA 94043', 'test');
		if (is_wp_error($geolocation_response)) {
			$debug_page = '<a href="'.admin_url('admin.php?page=w2mb_debug').'">' . esc_html__('More info at the debug page.', 'W2MB') . '</a>';
			w2mb_addMessage(esc_html__('Geolocation service does not work. If you are going to import addresses in CSV file, they should have geo coordinates. Otherwise map markers will not be created and the search will not work!', 'W2MB') . ' ' . $debug_page, 'error');
		}

		if ((w2mb_getValue($_POST, 'submit') || w2mb_getValue($_POST, 'goback')) && wp_verify_nonce($_POST['w2mb_csv_import_nonce'], W2MB_PATH) && (!defined('W2MB_DEMO') || !W2MB_DEMO)) {
			$errors = false;

			$validation = new w2mb_form_validation();
			$validation->set_rules('import_type', esc_html__('Import type', 'W2MB'), 'required');
			$validation->set_rules('columns_separator', esc_html__('Columns separator', 'W2MB'), 'required');
			$validation->set_rules('values_separator', esc_html__('categories separator', 'W2MB'), 'required');

			// GoBack button places on import results page
			if (w2mb_getValue($_POST, 'goback')) {
				$validation->set_rules('csv_file_name', esc_html__('CSV file name', 'W2MB'), 'required');
				$validation->set_rules('images_dir', esc_html__('Images directory', 'W2MB'));
				$validation->set_rules('if_term_not_found', esc_html__('Category not found', 'W2MB'), 'required');
				$validation->set_rules('listings_author', esc_html__('Listings author', 'W2MB'), 'required|numeric');
				$validation->set_rules('do_geocode', esc_html__('Geocode imported listings', 'W2MB'));
				if (get_option('w2mb_fsubmit_addon') && get_option('w2mb_claim_functionality'))
					$validation->set_rules('is_claimable', esc_html__('Configure imported listings as claimable', 'W2MB'));
				$validation->set_rules('fields[]', esc_html__('Listings fields', 'W2MB'));
			}

			if ($validation->run()) {
				$this->import_type = $validation->result_array('import_type');
				$this->columns_separator = $validation->result_array('columns_separator');
				$this->values_separator = $validation->result_array('values_separator');
				
				// GoBack button places on import results page
				if (w2mb_getValue($_POST, 'goback')) {
					$this->csv_file_name = $validation->result_array('csv_file_name');
					$this->images_dir = $validation->result_array('images_dir');
					$this->if_term_not_found = $validation->result_array('if_term_not_found');
					$this->selected_user = $validation->result_array('listings_author');
					$this->do_geocode = $validation->result_array('do_geocode');
					if (get_option('w2mb_fsubmit_addon') && get_option('w2mb_claim_functionality'))
						$this->is_claimable = $validation->result_array('is_claimable');
					$this->collated_fields = $validation->result_array('fields[]');
				}

				// GoBack button places on import results page
				if (w2mb_getValue($_POST, 'goback')) {
					$csv_file_name = $this->csv_file_name;

					if (!is_file($csv_file_name)) {
						w2mb_addMessage(esc_attr__("CSV temp file doesn't exist", 'W2MB'));
						return $this->csvImportSettings($validation->result_array());
					}

					if ($this->images_dir && !is_dir($this->images_dir)) {
						w2mb_addMessage(esc_attr__("Images temp directory doesn't exist", 'W2MB'));
						return $this->csvImportSettings($validation->result_array());
					}
				} else {
					$csv_file = $_FILES['csv_file'];

					if ($csv_file['error'] || !is_uploaded_file($csv_file['tmp_name'])) {
						w2mb_addMessage(esc_html__('There was a problem trying to upload CSV file', 'W2MB'), 'error');
						return $this->csvImportSettings($validation->result_array());
					}
	
					if (strtolower(pathinfo($csv_file['name'], PATHINFO_EXTENSION)) != 'csv' && $csv_file['type'] != 'text/csv') {
						w2mb_addMessage(esc_html__('This is not CSV file', 'W2MB'), 'error');
						return $this->csvImportSettings($validation->result_array());
					}
					
					if (function_exists('mb_detect_encoding') && !mb_detect_encoding(file_get_contents($csv_file['tmp_name']), 'UTF-8', true)) {
						w2mb_addMessage(esc_html__("CSV file must be in UTF-8", 'W2MB'), 'error');
						return $this->csvImportSettings($validation->result_array());
					}
					
					$upload_dir = wp_upload_dir();
					$csv_file_name = $upload_dir['path'] . '/' . $csv_file["name"];
					move_uploaded_file($csv_file['tmp_name'], $csv_file_name);

					if ($_FILES['images_file']['tmp_name']) {
						$images_file = $_FILES['images_file'];
						
						if ($images_file['error'] || !is_uploaded_file($images_file['tmp_name'])) {
							w2mb_addMessage(esc_html__('There was a problem trying to upload ZIP images file', 'W2MB'), 'error');
							return $this->csvImportSettings($validation->result_array());
						}
	
						if (!$this->extractImages($images_file['tmp_name'])) {
							w2mb_addMessage(esc_html__('There was a problem trying to unpack ZIP images file', 'W2MB'), 'error');
							return $this->csvImportSettings($validation->result_array());
						}
					}
				}
				
				$this->extractCsv($csv_file_name);

				if ($this->log['errors']) {
					foreach ($this->log['errors'] AS $message)
						w2mb_addMessage($message, 'error');

					return $this->csvImportSettings($validation->result_array());
				}
				
				if ($this->import_type == 'create') {
					unset($this->collation_fields['post_id']);
				}

				w2mb_renderTemplate('csv_manager/collate_columns.tpl.php', array(
						'collation_fields' => $this->collation_fields,
						'collated_fields' => $this->collated_fields,
						'headers' => $this->header_columns,
						'rows' => $this->rows,
						'import_type' => $this->import_type,
						'columns_separator' => $this->columns_separator,
						'values_separator' => $this->values_separator,
						'csv_file_name' => $csv_file_name,
						'images_dir' => $this->images_dir,
						'users' => $users,
						'if_term_not_found' => $this->if_term_not_found,
						'listings_author' => $this->selected_user,
						'do_geocode' => $this->do_geocode,
						'is_claimable' => $this->is_claimable,
				));
			} else {
				w2mb_addMessage($validation->error_array(), 'error');
				
				return $this->csvImportSettings($validation->result_array());
			}
		} else
			return $this->csvImportSettings();
	}
	
	// 3rd Step
	public function csvImport() {
		$this->buildCollationColumns();

		if ((w2mb_getValue($_POST, 'submit') || w2mb_getValue($_POST, 'tsubmit')) && wp_verify_nonce($_POST['w2mb_csv_import_nonce'], W2MB_PATH) && (!defined('W2MB_DEMO') || !W2MB_DEMO)) {
			if (w2mb_getValue($_POST, 'tsubmit'))
				$this->test_mode = true;

			$errors = false;

			$validation = new w2mb_form_validation();
			$validation->set_rules('import_type', esc_html__('Import type', 'W2MB'), 'required');
			$validation->set_rules('csv_file_name', esc_html__('CSV file name', 'W2MB'), 'required');
			$validation->set_rules('images_dir', esc_html__('Images directory', 'W2MB'));
			$validation->set_rules('columns_separator', esc_html__('Columns separator', 'W2MB'), 'required');
			$validation->set_rules('values_separator', esc_html__('categories separator', 'W2MB'), 'required');
			$validation->set_rules('if_term_not_found', esc_html__('Category not found', 'W2MB'), 'required');
			$validation->set_rules('listings_author', esc_html__('Listings author', 'W2MB'), 'required|numeric');
			$validation->set_rules('do_geocode', esc_html__('Geocode imported listings', 'W2MB'), 'is_checked');
			if (get_option('w2mb_fsubmit_addon') && get_option('w2mb_claim_functionality'))
				$validation->set_rules('is_claimable', esc_html__('Configure imported listings as claimable', 'W2MB'), 'is_checked');
			$validation->set_rules('fields[]', esc_html__('Listings fields', 'W2MB'));
				
			if ($validation->run()) {
				$this->import_type = $validation->result_array('import_type');
				$this->csv_file_name = $validation->result_array('csv_file_name');
				$this->images_dir = $validation->result_array('images_dir');
				$this->columns_separator = $validation->result_array('columns_separator');
				$this->values_separator = $validation->result_array('values_separator');
				$this->if_term_not_found = $validation->result_array('if_term_not_found');
				$this->selected_user = $validation->result_array('listings_author');
				$this->do_geocode = $validation->result_array('do_geocode');
				if (get_option('w2mb_fsubmit_addon') && get_option('w2mb_claim_functionality'))
					$this->is_claimable = $validation->result_array('is_claimable');
				$this->collated_fields = $validation->result_array('fields[]');
				
				if (!is_file($this->csv_file_name)) {
					$this->log['errors'][] = esc_attr__("CSV temp file doesn't exist", 'W2MB');
				}

				if ($this->images_dir && !is_dir($this->images_dir)) {
					$this->log['errors'][] = esc_attr__("Images temp directory doesn't exist", 'W2MB');
				}
				
				if ($this->import_type == 'update' && !in_array('post_id', $this->collated_fields)) {
					$this->log['errors'][] = esc_attr__("Post ID field wasn't collated", 'W2MB');
				}
				
				if ($this->import_type == 'create' && !in_array('title', $this->collated_fields)) {
					$this->log['errors'][] = esc_attr__("Title field wasn't collated", 'W2MB');
				}

				if ($this->import_type == 'create' && $this->selected_user != 0 && !get_userdata($this->selected_user)) {
					$this->log['errors'][] = esc_attr__("There isn't author user you selected", 'W2MB');
				}
				if ($this->import_type == 'create' && $this->selected_user == 0 && !in_array('user', $this->collated_fields)) {
					$this->log['errors'][] = esc_attr__("Author field wasn't collated and default author wasn't selected", 'W2MB');
				}

				$this->extractCsv($this->csv_file_name);
				
				ob_implicit_flush(true);
				w2mb_renderTemplate('admin_header.tpl.php');
				
				echo "<h2>" . esc_html__('CSV Import', 'W2MB') . "</h2>";
				echo "<h3>" . esc_html__('Import results', 'W2MB') . "</h3>";

				if (!$this->log['errors']) {
					$this->processCSV();
	
					if (!$this->log['errors'] && !$this->test_mode) {
						unlink($this->csv_file_name);
						if ($this->images_dir)
							$this->removeImagesDir($this->images_dir);
					}
				} else {
					foreach ($this->log['errors'] AS $error) {
						echo '<p>'.$error.'</p>';
					}
				}
				
				w2mb_renderTemplate('csv_manager/import_results.tpl.php', array(
						'log' => $this->log,
						'test_mode' => $this->test_mode,
						'fields' => $this->collated_fields,
						'import_type' => $this->import_type,
						'columns_separator' => $this->columns_separator,
						'values_separator' => $this->values_separator,
						'csv_file_name' => $this->csv_file_name,
						'images_dir' => $this->images_dir,
						'if_term_not_found' => $this->if_term_not_found,
						'listings_author' => $this->selected_user,
						'do_geocode' => $this->do_geocode,
						'is_claimable' => $this->is_claimable,
				));
			} else {
				w2mb_addMessage($validation->error_array(), 'error');
				
				return $this->csvImportSettings($validation->result_array());
			}
		}
	}
	
	public function extractCsv($csv_file) {
		ini_set('auto_detect_line_endings', true);

		if ($fp = fopen($csv_file, 'r')) {
			$n = 0;
			
			$this->log = array('errors' => array(), 'messages' => array());
			$this->header_columns = array();
			$this->rows = array();
			
			while (($line_columns = @fgetcsv($fp, 0, $this->columns_separator)) !== FALSE) {
				if ($line_columns) {
					if (!$this->header_columns) {
						$this->header_columns = $line_columns;
						foreach ($this->header_columns as &$column)
							$column = trim($column);
					} else {
						if (count($line_columns) > count($this->header_columns))
							$this->log['errors'][] = sprintf(esc_html__('Line %d has too many columns', 'W2MB'), $n+1);
						elseif (count($line_columns) < count($this->header_columns))
							$this->log['errors'][] = sprintf(esc_html__('Line %d has less columns than header line', 'W2MB'), $n+1);
						else
							$this->rows[] = $line_columns;
					}
				}
				$n++;
			}
			@fclose($fp);
		} else {
			$this->log['errors'][] = esc_attr__("Can't open CSV file", 'W2MB');
			return false;
		}
	}
	
	public function extractImages($zip_file) {
		$dir = trailingslashit(get_temp_dir() . 'w2mb_' . time());
		
		require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
		
		$zip = new PclZip($zip_file);
		if ($files = $zip->extract(PCLZIP_OPT_PATH, $dir, PCLZIP_OPT_REMOVE_ALL_PATH)) {
			$this->images_dir = $dir;
			return true;
		}

		return false;
	}
	
	public function removeImagesDir($dir) {
		if (!isset($GLOBALS['wp_filesystem']) || !is_object($GLOBALS['wp_filesystem'])) {
			WP_Filesystem();
		}

		$wp_file = new WP_Filesystem_Direct($dir);
		return $wp_file->rmdir($dir, true);
	}

	public function processCSV() {
		global $wpdb, $w2mb_instance;
		
		printf(esc_html__('Import started, number of available rows in file: %d', 'W2MB'), count($this->rows));
		echo "<br />";
		if ($this->test_mode) {
			esc_html_e('Test mode enabled', 'W2MB');
			echo "<br />";
		}

		$users_logins = array();
		$users_emails = array();
		$users_ids = array();
		$users = get_users(array('fields' => array('ID', 'user_login', 'user_email')));
		foreach ($users AS $user) {
			$users_logins[] = $user->user_login;
			$users_emails[] = $user->user_email;
			$users_ids[] = $user->ID;
		}
		
		$total_rejected_lines = 0;
		foreach ($this->rows as $line=>$row) {
			$n = $line+1;
			printf(esc_html__('Importing line %d...', 'W2MB'), $n);
			echo "<br />";
			$error_on_line = false;
			$listing_data = array();
			foreach ($this->collated_fields as $i=>$field) {
				$value = htmlspecialchars_decode(trim($row[$i])); // htmlspecialchars_decode() needed due to &amp; symbols in import files, ';' symbols can break import
				
				if ($field == 'post_id' && $this->import_type == 'update') {
					if (($post = get_post($value)) && ($listing = w2mb_getListing($post))) {
						$listing_data['existing_listing'] = $listing;
						$listing_data['post_id'] = $value;
					} else {
						$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Listing with ID \"%d\" doesn't exist", 'W2MB'), $n, $value);
						$error_on_line = $this->setErrorOnLine($error);
					}
				}

				if ($field == 'title') {
					$listing_data['title'] = $value;
					printf(esc_html__('Listing title: %s', 'W2MB'), $value);
					echo "<br />";
				} elseif ($field == 'user') {
					if (!$this->selected_user) {
						if ((($key = array_search($value, $users_logins)) !== FALSE) || (($key = array_search($value, $users_emails)) !== FALSE) || (($key = array_search($value, $users_ids))) !== FALSE)
							$listing_data['user_id'] = $users_ids[$key];
						else {
							$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("User \"%s\" doesn't exist", 'W2MB'), $n, $value);
							$error_on_line = $this->setErrorOnLine($error);
						}
					} else {
						$listing_data['user_id'] = $this->selected_user;
					}
				} elseif ($field == 'content') {
					$listing_data['content'] = $value;
				} elseif ($field == 'excerpt') {
					$listing_data['excerpt'] = $value;
				} elseif ($field == 'categories_list') {
					$listing_data['categories'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
				} elseif ($field == 'listing_tags') {
					$listing_data['tags'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
				} elseif ($field == 'locations_list') {
					$listing_data['locations'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'address_line_1') {
					$listing_data['address_line_1'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'address_line_2') {
					$listing_data['address_line_2'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'zip') {
					$listing_data['zip'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'latitude') {
					$listing_data['latitude'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'longitude') {
					$listing_data['longitude'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'map_icon_file') {
					$listing_data['map_icon_file'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'additional_address_info') {
					$listing_data['additional_address_info'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'videos') {
					$listing_data['videos'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
				} elseif ($field == 'images') {
					if ($this->images_dir) {
						$listing_data['images'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
					} else {
						$images_value = array_filter(array_map('trim', explode($this->values_separator, $value)));
						$validation = new w2mb_form_validation();
						$this_is_import_by_URL = false;
						foreach ($images_value AS $image_url) {
							if ($validation->valid_url($image_url, false)) {
								$listing_data['images'][] = $image_url;
								$this_is_import_by_URL = true;
							} else {
								$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . sprintf(esc_attr__("Incorrect image URL %s", 'W2MB'), $image_url), $n);
								$error_on_line = $this->setErrorOnLine($error);
							}
						}
						if (!$this_is_import_by_URL) {
							$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Images column was specified, but ZIP archive wasn't upload", 'W2MB'), $n);
							$error_on_line = $this->setErrorOnLine($error);
						}
					}
				} elseif ($content_field = $w2mb_instance->content_fields->getContentFieldBySlug($field)) {
					if (is_a($content_field, 'w2mb_content_field_checkbox')) {
						if ($value = array_map('trim', explode($this->values_separator, $value)))
							if (count($value) == 1)
								$value = array_shift($value);
					} elseif (is_string($value)) {
						$value = trim($value);
					}

					if ($value !== false && $value !== "") {
						$errors = array();
						$listing_data['content_fields'][$field] = $content_field->validateCsvValues($value, $errors);
						foreach ($errors AS $_error) {
							$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . $_error, $n);
							$error_on_line = $this->setErrorOnLine($error);
						}
					}
				} elseif ($field == 'expiration_date') {
					if (!($timestamp = strtotime($value))) {
						$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Expiration date value is incorrect", 'W2MB'), $n);
						$error_on_line = $this->setErrorOnLine($error);
					} else
						$listing_data['expiration_date'] = $timestamp;
				} elseif ($field == 'contact_email') {
					if ($value) {
						if (!is_email($value)) {
							$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Contact email is incorrect", 'W2MB'), $n);
							$error_on_line = $this->setErrorOnLine($error);
						} else {
							$listing_data['contact_email'] = $value;
						}
					}
				} elseif (get_option('w2mb_fsubmit_addon') && get_option('w2mb_claim_functionality') && (($field == 'claimable' && $value) || $this->is_claimable)) {
					$listing_data['claimable'] = true;
				} elseif ($field == 'wpml_translation_source_id') {
					$listing_data['wpml_translation_source_id'] = $value;
				} elseif ($field == 'status') {
					if (in_array($value, array("active", "expired", "unpaid", "stopped"))) {
						$listing_data['status'] = $value;
					} else {
						$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Listing status must be one of the following: active, expired, unpaid or stopped", 'W2MB'), $n);
						$error_on_line = $this->setErrorOnLine($error);
					}
				}
				
				$listing_data = apply_filters('w2mb_csv_process_fields', $listing_data, $field, $value);
			}

			if (!$error_on_line) {
				if (!$this->test_mode) {
					if ($this->import_type == 'create') {
						$listing_data_level = new w2mb_level();
	
						$new_post_args = array(
								'post_title' => $listing_data['title'],
								'post_type' => W2MB_POST_TYPE,
								'post_author' => (isset($listing_data['user_id'])) ? $listing_data['user_id'] : $this->selected_user,
								'post_status' => 'publish',
								'post_content' => (isset($listing_data['content']) ? $listing_data['content'] : ''),
								'post_excerpt' => (isset($listing_data['excerpt']) ? $listing_data['excerpt'] : ''),
						);
						$new_post_id = wp_insert_post($new_post_args);
						
						add_post_meta($new_post_id, '_listing_created', true);
						add_post_meta($new_post_id, '_order_date', time());
						if (isset($listing_data['status'])) {
							$status = $listing_data['status'];
						} else {
							$status = 'active';
						}
						add_post_meta($new_post_id, '_listing_status', $status);
						
						if (!$listing_data_level->eternal_active_period) {
							$expiration_date = w2mb_calcExpirationDate(current_time('timestamp'), $listing_data_level);
							add_post_meta($new_post_id, '_expiration_date', $expiration_date);
						}
						
						if (isset($listing_data['locations'])) {
							$this->processMapsLocations($listing_data, $n);
						}
	
						if (isset($listing_data['locations_ids']) || isset($listing_data['address_line_1']) || (isset($listing_data['latitude']) && isset($listing_data['longitude']))) {
							$this->processListingLocations($new_post_id, $listing_data, $listing_data_level, $n);
						}
	
						if (isset($listing_data['categories'])) {
							$this->processCategories($new_post_id, $listing_data, $n);
						}
		
						if (isset($listing_data['tags'])) {
							$this->processTags($new_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['content_fields'])) {
							$this->processContentFields($new_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['images'])) {
							$this->processImages($new_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['videos'])) {
							$this->processVideos($new_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['expiration_date'])) {
							update_post_meta($new_post_id, '_expiration_date', $listing_data['expiration_date']);
						}
	
						if (isset($listing_data['contact_email'])) {
							add_post_meta($new_post_id, '_contact_email', $listing_data['contact_email']);
						}
	
						if (isset($listing_data['claimable'])) {
							add_post_meta($new_post_id, '_is_claimable', true);
						}
						
						if (isset($listing_data['wpml_translation_source_id'])) {
							$this->assignTranslation($new_post_id, $listing_data, $n);
						}

						do_action('w2mb_csv_create_listing', $new_post_id, $listing_data);
					} elseif ($this->import_type == 'update') {
// -------------------- Update existing listing by ID ------------------------------------------------------------------------------------------------------------------
						$existing_post_id = $listing_data['post_id'];
						
						$listing_data_level = new w2mb_level();
						
						if (isset($listing_data['status'])) {
							update_post_meta($existing_post_id, '_listing_status', $listing_data['status']);
						}
						
						$existing_post_args = array(
								'ID' => $existing_post_id,
						);
						if (isset($listing_data['user_id']) || $this->selected_user) {
							$existing_post_args['post_author'] = (isset($listing_data['user_id'])) ? $listing_data['user_id'] : $this->selected_user;
						}
						if (isset($listing_data['title'])) {
							$existing_post_args['post_title'] = $listing_data['title'];
						}
						if (isset($listing_data['content'])) {
							$existing_post_args['post_content'] = $listing_data['content'];
						}
						if (isset($listing_data['excerpt'])) {
							$existing_post_args['post_excerpt'] = $listing_data['excerpt'];
						}
						wp_update_post($existing_post_args);

						if (isset($listing_data['locations'])) {
							$this->processMapsLocations($listing_data, $n);
						}
						
						if (isset($listing_data['locations_ids']) || isset($listing_data['address_line_1']) || (isset($listing_data['latitude']) && isset($listing_data['longitude']))) {
							$this->processListingLocations($existing_post_id, $listing_data, $listing_data_level, $n);
						}
						
						if (isset($listing_data['categories'])) {
							wp_set_object_terms($existing_post_id, array(), W2MB_CATEGORIES_TAX);

							$this->processCategories($existing_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['tags'])) {
							wp_set_object_terms($existing_post_id, array(), W2MB_TAGS_TAX);

							$this->processTags($existing_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['content_fields'])) {
							$this->processContentFields($existing_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['images'])) {
							wp_delete_attachment($existing_post_id);
							delete_post_meta($existing_post_id, '_attached_image');

							$this->processImages($existing_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['videos'])) {
							delete_post_meta($existing_post_id, '_attached_video_id');
							
							$this->processVideos($existing_post_id, $listing_data, $n);
						}
						
						if (isset($listing_data['expiration_date'])) {
							delete_post_meta($existing_post_id, '_expiration_date');
							
							update_post_meta($existing_post_id, '_expiration_date', $listing_data['expiration_date']);
						}
						
						if (isset($listing_data['contact_email'])) {
							delete_post_meta($existing_post_id, '_contact_email');

							add_post_meta($existing_post_id, '_contact_email', $listing_data['contact_email']);
						}
						
						if (isset($listing_data['claimable'])) {
							if ($listing_data['claimable'])
								add_post_meta($existing_post_id, '_is_claimable', true);
							else
								add_post_meta($existing_post_id, '_is_claimable', false);
						}
						
						if (isset($listing_data['wpml_translation_source_id'])) {
							$this->assignTranslation($existing_post_id, $listing_data, $n);
						}
						
						do_action('w2mb_csv_update_listing', $existing_post_id, $listing_data);
					}
					wp_cache_flush();
				}
			} else {
				$total_rejected_lines++;
			}
		}

		printf(esc_html__('Import finished, number of errors: %d, total rejected lines: %d', 'W2MB'), count($this->log['errors']), $total_rejected_lines);
		echo "<br />";
		echo "<br />";
	}
	
	public function setErrorOnLine($error) {
		$this->log['errors'][] = $error;
		echo "<span style='color: red'>" . $error . "</span>";
		echo "<br />";
		return true;
	}
	
	public function processMapsLocations(&$listing_data, $line_n) {
		foreach ($listing_data['locations'] as $location_item) {
			if (!is_numeric($location_item)) {
				$locations_chain = array_filter(array_map('trim', explode('>', $location_item)));
				$listing_term_id = 0;
				foreach ($locations_chain as $key => $location_name) {
					if (is_numeric($location_name)) {
						$location_name = intval($location_name);
					}
					if ($term = term_exists(htmlspecialchars($location_name), W2MB_LOCATIONS_TAX, $listing_term_id)) { // htmlspecialchars() needed due to &amp; symbols in import files
						$term_id = intval($term['term_id']);
						$listing_term_id = $term_id;
					} else {
						if ($this->if_term_not_found == 'create') {
							if ($newterm = wp_insert_term($location_name, W2MB_LOCATIONS_TAX, array('parent' => $listing_term_id))) {
								if (!is_wp_error($newterm)) {
									$term_id = intval($newterm['term_id']);
									$listing_term_id = $term_id;
								} else {
									$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_html__('Something went wrong with maps location "%s"', 'W2MB'), $line_n, $location_name);
									$error_on_line = $this->setErrorOnLine($error);
								}
							}
						} else {
							$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Maps location \"%s\" wasn't found, was skipped", 'W2MB'), $line_n, $location_name);
							$error_on_line = $this->setErrorOnLine($error);
						}
					}
				}
				if ($listing_term_id)
					$listing_data['locations_ids'][] = $listing_term_id;
			} elseif (get_term($location_item, W2MB_LOCATIONS_TAX)) {
				$listing_data['locations_ids'][] = $location_item;
			} else {
				$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Maps location with ID \"%d\" wasn't found", 'W2MB'), $line_n, $location_item);
				$error_on_line = $this->setErrorOnLine($error);
			}
		}
	}
	
	public function processListingLocations($post_id, &$listing_data, $listing_data_level, $line_n) {
		global $w2mb_instance;

		if (!empty($listing_data['locations_ids'])) {
			$locations_items = $listing_data['locations_ids'];
		} elseif (!empty($listing_data['address_line_1'])) {
			$locations_items = $listing_data['address_line_1'];
		} elseif (!empty($listing_data['longitude']) && !empty($listing_data['latitude'])) {
			$locations_items = $listing_data['longitude'];
		}
		
		$locations_args = array();
		foreach ($locations_items AS $key=>$location_item) {
			if ($this->do_geocode) {
				$location_string = '';
				if (isset($listing_data['locations_ids'][$key])) {
					$chain = array();
					$parent_id = $listing_data['locations_ids'][$key];
					while ($parent_id != 0) {
						if ($term = get_term($parent_id, W2MB_LOCATIONS_TAX)) {
							$chain[] = $term->name;
							$parent_id = $term->parent;
						} else
							$parent_id = 0;
					}
					$location_string = implode(', ', $chain);
				}
				if (isset($listing_data['address_line_1'][$key]))
					$location_string = $listing_data['address_line_1'][$key] . ' ' . $location_string;
				if (isset($listing_data['address_line_2'][$key]))
					$location_string = $listing_data['address_line_2'][$key] . ', ' . $location_string;
				if (isset($listing_data['zip'][$key]))
					$location_string = $location_string . ' ' . $listing_data['zip'][$key];
				if (get_option('w2mb_default_geocoding_location'))
					$location_string = $location_string . ' ' . get_option('w2mb_default_geocoding_location');
				
				$location_string = trim($location_string);
				
				printf(__('Geocoding address: %s', 'W2MB'), $location_string);
				echo "<br />";
				
				$geoname = new w2mb_locationGeoname ;
				$result = $geoname->geocodeRequest($location_string, 'coordinates');
				if (!is_wp_error($result) && is_array($result)) {
					$listing_data['longitude'][$key] = $result[0];
					$listing_data['latitude'][$key] = $result[1];
				} else {
					printf(esc_html__('Following address can not be geocoded: %s. Status: %s, error: %s', 'W2MB'), $location_string, $geoname->getLastStatus(), $geoname->getLastError());
					echo "<br />";
				}
				
				$locations_args['manual_coords[]'][] = 0;
			} else {
				if (isset($listing_data['latitude'][$key]) && isset($listing_data['longitude'][$key])) {
					$locations_args['manual_coords[]'][] = 1;
				} else {
					$locations_args['manual_coords[]'][] = 0;
				}
			}
		
			$locations_args['w2mb_location[]'][] = 1;
			$locations_args['selected_tax[]'][] = (isset($listing_data['locations_ids'][$key]) ? $listing_data['locations_ids'][$key] : 0);
			$locations_args['address_line_1[]'][] = (isset($listing_data['address_line_1'][$key]) ? $listing_data['address_line_1'][$key] : '');
			$locations_args['address_line_2[]'][] = (isset($listing_data['address_line_2'][$key]) ? $listing_data['address_line_2'][$key] : '');
			$locations_args['zip_or_postal_index[]'][] = (isset($listing_data['zip'][$key]) ? $listing_data['zip'][$key] : '');
			
			$locations_args['map_coords_1[]'][] = (isset($listing_data['latitude'][$key]) ? $listing_data['latitude'][$key] : '');
			$locations_args['map_coords_2[]'][] = (isset($listing_data['longitude'][$key]) ? $listing_data['longitude'][$key] : '');
			$locations_args['map_zoom'] = get_option('w2mb_default_map_zoom');
			$locations_args['map_icon_file[]'][] = (isset($listing_data['map_icon_file'][$key]) ? $listing_data['map_icon_file'][$key] : '');
			$locations_args['additional_info[]'][] = (isset($listing_data['additional_address_info'][$key]) ? $listing_data['additional_address_info'][$key] : '');
		}
		$args = apply_filters('w2mb_csv_save_location_args', $locations_args, $post_id, $listing_data);
		
		$w2mb_instance->locations_manager->saveLocations($listing_data_level, $post_id, $locations_args);
	}
	
	public function processCategories($post_id, &$listing_data, $line_n) {
		foreach ($listing_data['categories'] as $category_item) {
			$categories_chain = array_filter(array_map('trim', explode('>', $category_item)));
			$listing_term_id = 0;
			foreach ($categories_chain as $key => $category_name) {
				if (is_numeric($category_name)) {
					$category_name = intval($category_name);
				}
				if ($term = term_exists(htmlspecialchars($category_name), W2MB_CATEGORIES_TAX, $listing_term_id)) { // htmlspecialchars() needed due to &amp; symbols in import files
					$term_id = intval($term['term_id']);
					$listing_term_id = $term_id;
				} else {
					if ($this->if_term_not_found == 'create') {
						if ($newterm = wp_insert_term($category_name, W2MB_CATEGORIES_TAX, array('parent' => $listing_term_id)))
						if (!is_wp_error($newterm)) {
							$term_id = intval($newterm['term_id']);
							$listing_term_id = $term_id;
						} else {
							$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_html__('Something went wrong with maps category "%s"', 'W2MB'), $line_n, $category_name);
							$error_on_line = $this->setErrorOnLine($error);
						}
					} else {
						$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Maps category \"%s\" wasn't found, was skipped", 'W2MB'), $line_n, $category_name);
						$error_on_line = $this->setErrorOnLine($error);
					}
				}
			}
			if ($listing_term_id)
				$listing_data['categories_ids'][] = $listing_term_id;
		}
		if (isset($listing_data['categories_ids']))
			wp_set_object_terms($post_id, $listing_data['categories_ids'], W2MB_CATEGORIES_TAX);
	}
	
	public function processTags($post_id, &$listing_data, $line_n) {
		foreach ($listing_data['tags'] as $tag_name) {
			if (is_numeric($tag_name)) {
				$tag_name = intval($tag_name);
			}
			if ($term = term_exists(htmlspecialchars($tag_name), W2MB_TAGS_TAX)) { // htmlspecialchars() needed due to &amp; symbols in import files
				$listing_data['tags_ids'][] = intval($term['term_id']);
			} else {
				if ($this->if_term_not_found == 'create') {
					if ($newterm = wp_insert_term($tag_name, W2MB_TAGS_TAX))
					if (!is_wp_error($newterm))
						$listing_data['tags_ids'][] = intval($newterm['term_id']);
					else {
						$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_html__('Something went wrong with maps tag "%s"', 'W2MB'), $line_n, $tag_name);
						$error_on_line = $this->setErrorOnLine($error);
					}
				} else {
					$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("Maps tag \"%s\" wasn't found, was skipped", 'W2MB'), $line_n, $tag_name);
					$error_on_line = $this->setErrorOnLine($error);
				}
			}
		}
		if (isset($listing_data['tags_ids']))
			wp_set_object_terms($post_id, $listing_data['tags_ids'], W2MB_TAGS_TAX);
	}
	
	public function processContentFields($post_id, &$listing_data, $line_n) {
		global $w2mb_instance;

		foreach ($listing_data['content_fields'] AS $field=>$values) {
			$content_field = $w2mb_instance->content_fields->getContentFieldBySlug($field);
			$content_field->saveValue($post_id, $values);
		}
	}
	
	public function processImages($post_id, &$item_data, $line_n) {
		$_thumbnail_id_inserted = false;
		
		foreach ($item_data['images'] AS $image_item) {
			$value = explode('>', $image_item);
			// import images from ZIP file or by URLs
			if ($this->images_dir) {
				$image_file_name = $value[0];
				
				$subdir = w2mb_getValue(wp_upload_dir(null, false, true), 'subdir', '');
				
				$url_to_search = trim($subdir, '/') . '/' . $image_file_name;
				
				// check if this attachment already exists
				$attachment_id = attachment_url_to_postid($url_to_search);
				if ($attachment_id) {
					// insert attachment ID to the post meta
					add_post_meta($post_id, '_attached_image', $attachment_id);
				
					// first image is the logo
					if (!$_thumbnail_id_inserted) {
						update_post_meta($post_id, '_thumbnail_id', $attachment_id);
						$_thumbnail_id_inserted = true;
					}
				} else {
					$image_title = (isset($value[1]) ? $value[1] : '');
					if (file_exists($this->images_dir . $image_file_name)) {
						$filepath = $this->images_dir . $image_file_name;
				
						$file = array('name' => basename($filepath),
								'tmp_name' => $filepath,
								'error' => 0,
								'size' => filesize($filepath)
						);
				
						copy($filepath, $filepath . '.backup');
						$image = wp_handle_sideload($file, array('test_form' => FALSE));
						rename($filepath . '.backup', $filepath);
						
						$this->insertAttachment($post_id, $image_file_name, $image, $image_title, $filepath, $_thumbnail_id_inserted);
					} else {
						$error = sprintf(esc_html__("There isn't specified image file \"%s\" inside ZIP file. Or temp folder wasn't created: \"%s\"", 'W2MB'), $image_file_name, $this->images_dir);
						$error_on_line = $this->setErrorOnLine($error);
					}
				}
			} else {
				$image_url = $value[0];
				$image_file_name = basename($image_url);
				$image_title = (isset($value[1]) ? $value[1] : '');
				
				$uploaddir = wp_upload_dir();
				$uploadfile = $uploaddir['path'] . '/' . $image_file_name;
				
				$contents = file_get_contents($image_url);
				$savefile = @fopen($uploadfile, 'w');
				@fwrite($savefile, $contents);
				@fclose($savefile);
				
				$file = array('name' => $image_file_name,
						'tmp_name' => $uploadfile,
						'error' => 0,
						'size' => filesize($uploadfile)
				);
				$image = wp_handle_sideload($file, array('test_form' => FALSE));
				
				$this->insertAttachment($post_id, $image_file_name, $image, $image_title, $uploadfile, $_thumbnail_id_inserted);
			}
		}
	}
	
	public function insertAttachment($post_id, $image_file_name, $image, $image_title, $filepath, &$_thumbnail_id_inserted) {
		if (!isset($image['error'])) {
			$attachment = array(
					'post_mime_type' => $image['type'],
					'post_title' => $image_title,
					'post_content' => '',
					'post_status' => 'inherit'
			);
			if ($attach_id = wp_insert_attachment($attachment, $image['file'], $post_id)) {
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$attach_data = wp_generate_attachment_metadata($attach_id, $image['file']);
				wp_update_attachment_metadata($attach_id, $attach_data);
					
				// insert attachment ID to the post meta
				add_post_meta($post_id, '_attached_image', $attach_id);
			
				// first image is the logo
				if (!$_thumbnail_id_inserted) {
					update_post_meta($post_id, '_thumbnail_id', $attach_id);
					$_thumbnail_id_inserted = true;
				}
			} else {
				$error = sprintf(esc_html__('Image file "%s" could not be inserted.', 'W2MB'), $image_file_name);
				$error_on_line = $this->setErrorOnLine($error);
			}
		} else {
			$error = sprintf(esc_html__("Image file \"%s\" wasn't attached. Full path: \"%s\". Error: %s", 'W2MB'), $image_file_name, $filepath, $image['error']);
			$error_on_line = $this->setErrorOnLine($error);
		}
	}
	
	public function processVideos($post_id, &$listing_data, $line_n) {
		$validation = new w2mb_form_validation();
		foreach ($listing_data['videos'] AS $video_item) {
			$video_id = null;
			if ($validation->valid_url($video_item, false)) {
				preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video_item, $matches_youtube);
				preg_match("#(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([вЂЊвЂ‹0-9]{6,11})[?]?.*#", $video_item, $matches_vimeo);
				if (isset($matches_youtube[0]) && strlen($matches_youtube[0]) == 11)
					$video_id = $matches_youtube[0];
				elseif (isset($matches_vimeo[5]) && strlen($matches_vimeo[5]) == 9) {
					$video_id = $matches_vimeo[5];
				} else {
					$error = sprintf(esc_html__('Error on line %d: ', 'W2MB') . esc_attr__("YouTube or Vimeo video URL is incorrect", 'W2MB'), $line_n);
					$error_on_line = $this->setErrorOnLine($error);
				}
			} else
				$video_id = $video_item;
			if ($video_id)
				add_post_meta($post_id, '_attached_video_id', $video_id);
		}
	}
	
	public function assignTranslation($post_id, &$listing_data, $line_n) {
		// adapted for WPML
		global $sitepress, $wpdb;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$source_post_id = $listing_data['wpml_translation_source_id'];
			$lang = ICL_LANGUAGE_CODE;

			if ($trid = $sitepress->get_element_trid($source_post_id, 'post_w2mb_listing')) {
				$sql = "DELETE FROM wp_icl_translations
						WHERE trid = '{$trid}' AND
						language_code = '{$lang}' AND
						element_type='post_w2mb_listing'";
				$wpdb->query($sql);
				
				$sql = "UPDATE wp_icl_translations SET
						trid = '{$trid}',
						language_code = '{$lang}'
						WHERE element_id = '{$post_id}' AND
						element_type='post_w2mb_listing'";
				$wpdb->query($sql);
			}
		}
	}
	
	public function csvExport() {
		global $w2mb_instance;
		
		$number = 1000;
		$offset = 0;
		
		$validation = new w2mb_form_validation();
		$validation->set_rules('number', esc_html__('Listings number', 'W2MB'), 'integer');
		$validation->set_rules('offset', esc_html__('Listings offset', 'W2MB'), 'integer');
		if ($validation->run()) {
			if ($validation->result_array('number')) {
				$number = $validation->result_array('number');
			}
			if ($validation->result_array('offset')) {
				$offset = $validation->result_array('offset');
			}
		}

		$csv_columns = array(
				'post_id',
				'title',
				'status',
				'user',
				'description',
				'excerpt',
				'categories',
				'locations',
				'address_line_1',
				'address_line_2',
				'zip',
				'latitude',
				'longitude',
				'map_icon_file',
				'additional_info',
				'tags',
				'images',
				'videos',
				'expiration_date',
				'contact_email',
				'claimable',
		);
		
		foreach ($w2mb_instance->content_fields->content_fields_array AS $field)
			if (!$field->is_core_field)
				$csv_columns[] = $field->slug;

		$csv_output[] = $csv_columns;
		
		$args = array(
				'post_type' => W2MB_POST_TYPE,
				'orderby' => 'ID',
				'order' => 'ASC',
				'post_status' => 'publish,private,draft,pending',
				'posts_per_page' => $number,
				'offset' => $offset,
		);
		
		$args = apply_filters("w2mb_csv_export_args", $args);
		
		$query = new WP_Query($args);
		$count = 0;
		while ($query->have_posts()) {
			$count++;
			$query->the_post();
			$post = get_post();
			$listing = w2mb_getListing($post);
			
			$listing_id = $listing->post->ID;
			
			$categories = array();
			$categories_objects = wp_get_object_terms($listing_id, W2MB_CATEGORIES_TAX);
			foreach ($categories_objects AS $category) {
				$listing_categories = w2mb_get_term_parents($category->term_id, W2MB_CATEGORIES_TAX, false, true);
				$categories[] = implode(">", $listing_categories);
			}

			$tags = array();
			$tags_objects = wp_get_object_terms($listing_id, W2MB_TAGS_TAX);
			foreach ($tags_objects AS $tag) {
				$tags[] = $tag->name;
			}
			
			$selected_location = array();
			$address_line_1 = array();
			$address_line_2 = array();
			$zip = array();
			$map_coords_1 = array();
			$map_coords_2 = array();
			$map_icon_file = array();
			$additional_info = array();
			foreach ($listing->locations AS $location) {
				if ($location->selected_location) {
					$listing_locations = w2mb_get_term_parents($location->selected_location, W2MB_LOCATIONS_TAX, false, true);
					$listing_locations = $listing_locations;
					$selected_location[] = implode(">", $listing_locations);
				} else {
					$selected_location[] = '';
				}
				
				$address_line_1[] = ($location->address_line_1) ? $location->address_line_1 : '';
				$address_line_2[] = ($location->address_line_2) ? $location->address_line_2 : '';
				$zip[] = ($location->zip_or_postal_index) ? $location->zip_or_postal_index : '';
				$map_coords_1[] = ($location->map_coords_1) ? $location->map_coords_1 : '';
				$map_coords_2[] = ($location->map_coords_2) ? $location->map_coords_2 : '';
				$map_icon_file[] = ($location->map_icon_file) ? $location->map_icon_file : '';
				$additional_info[] = ($location->additional_info) ? $location->additional_info : '';
			}
			
			$images = array();
			foreach ($listing->images AS $attachment_id=>$image) {
				$image_src = wp_get_attachment_image_src($attachment_id, 'full');
				$image_item = basename($image_src[0]);
				if ($image['post_title']) {
					$image_item .= ">" . $image['post_title'];
				}
				$images[] = $image_item;
			}
			
			$videos = array();
			foreach ($listing->videos AS $video) {
				$videos[] = $video['id'];
			}

			$row = array(
					$listing_id,
					$listing->title(),
					$listing->status,
					$listing->post->post_author,
					$listing->post->post_content,
					$listing->post->post_excerpt,
					implode(';', $categories),
					implode(';', $selected_location),
					implode(';', $address_line_1),
					implode(';', $address_line_2),
					implode(';', $zip),
					implode(';', $map_coords_1),
					implode(';', $map_coords_2),
					implode(';', $map_icon_file),
					implode(';', $additional_info),
					implode(';', $tags),
					implode(';', $images),
					implode(';', $videos),
					((!$listing->level->eternal_active_period && $listing->expiration_date) ?  date('d.m.Y H:i', $listing->expiration_date) : ''),
					get_post_meta($listing_id, '_contact_email', true),
					get_post_meta($listing_id, '_is_claimable', true),
			);
			
			foreach ($w2mb_instance->content_fields->content_fields_array AS $field) {
				if (!$field->is_core_field) {
					if (isset($listing->content_fields[$field->id])) {
						$row[] = $listing->content_fields[$field->id]->exportCSV();
					} else {
						$row[] = '';
					}
				}
			}

			$csv_output[] = $row;
		}
		
		$csv_output = apply_filters("w2mb_csv_export_output", $csv_output);
		
		$csv_file_name = 'w2mb-listings--' . date('Y-m-d_H_i_s') . '--' . $count . '.csv';

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"" . $csv_file_name . "\";" );
		header("Content-Transfer-Encoding: binary");

		$outputBuffer = fopen("php://output", 'w');
		foreach($csv_output as $val) {
			fputcsv($outputBuffer, $val);
		}
		fclose($outputBuffer);
		
		exit;
	}
	
	public function exportImages() {
		$images = array();
		$upload_dir = wp_upload_dir();
		$upload_dir_path = trailingslashit($upload_dir['basedir']);

		$args = array(
				'post_type' => W2MB_POST_TYPE,
				'post_status' => 'publish,private,draft,pending',
				'posts_per_page' => -1
		);
		$query = new WP_Query($args);
		while ($query->have_posts()) {
			$query->the_post();
			$post = get_post();
			$listing = w2mb_getListing($post);

			foreach ($listing->images AS $attachment_id=>$image) {
				if ($image_file = wp_get_attachment_metadata($attachment_id, true)) {
					$file_path = $upload_dir_path . $image_file['file'];
					if (file_exists($file_path))
						$images[] = $file_path;
				}
			}
		}
		$images = array_unique($images);
		
		$images = apply_filters("w2mb_csv_export_images", $images);

		if ($images) {
			$zip_file = trailingslashit(get_temp_dir()) . 'w2mb_images.zip';
			
			require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

			$zip = new PclZip($zip_file);
			$path = $zip->create(implode(',', $images), PCLZIP_OPT_REMOVE_ALL_PATH);
			if (!$path)
				die('Error : ' . $zip->errorInfo(true));

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename="w2mb_images.zip"');
			header('Content-Length: ' . filesize($zip_file));
			flush();
			readfile($zip_file);

			register_shutdown_function('unlink', $zip_file);
		}

		exit;
	}
}

?>