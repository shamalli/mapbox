<?php

include_once W2MB_PATH . 'classes/content_fields/fields/content_field_content.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_excerpt.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_address.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_categories.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_tags.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_string.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_textarea.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_number.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_select.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_checkbox.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_radio.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_website.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_email.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_datetime.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_price.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_hours.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_fileupload.php';
include_once W2MB_PATH . 'classes/content_fields/fields/content_field_phone.php';

class w2mb_content_fields {
	public $content_fields_array = array();
	public $content_fields_groups_array = array();
	public $fields_types_names;
	private $map_content_fields = array();
	
	public function __construct() {
		$this->fields_types_names = array(
				'excerpt' => esc_html__('Excerpt', 'W2MB'),
				'content' => esc_html__('Content', 'W2MB'),
				'categories' => esc_html__('Listing categories', 'W2MB'),
				'tags' => esc_html__('Listing tags', 'W2MB'),
				'address' => esc_html__('Listing addresses', 'W2MB'),
				'phone' => esc_html__('Phone number', 'W2MB'),
				'string' => esc_html__('Text string', 'W2MB'),
				'textarea' => esc_html__('Textarea', 'W2MB'),
				'number' => esc_html__('Digital value', 'W2MB'),
				'select' => esc_html__('Select list', 'W2MB'),
				'radio' => esc_html__('Radio buttons', 'W2MB'),
				'checkbox' => esc_html__('Checkboxes', 'W2MB'),
				'website' => esc_html__('Website URL', 'W2MB'),
				'email' => esc_html__('Email', 'W2MB'),
				'datetime' => esc_html__('Date-Time', 'W2MB'),
				'price' => esc_html__('Price', 'W2MB'),
				'hours' => esc_html__('Opening hours', 'W2MB'),
				'fileupload' => esc_html__('File upload', 'W2MB'),
		);

		$this->getContentFieldsFromDB();
	}
	
	public function saveOrder() {
		global $wpdb;

		if (isset($_POST['content_fields_order']) && $_POST['content_fields_order'] && ($order_ids = explode(',', trim($_POST['content_fields_order'])))) {
			$i = 1;
			foreach ($order_ids AS $id) {
				$wpdb->update($wpdb->w2mb_content_fields, array('order_num' => $i), array('id' => $id));
				$i++;
			}
			$this->getContentFieldsFromDB();

			return true;
		}
	}

	public function saveGroupsRelations() {
		global $wpdb;

		foreach ($this->content_fields_array AS $content_field) {
			if (isset($_POST['group_id_'.$content_field->id]))
				$wpdb->update($wpdb->w2mb_content_fields, array('group_id' => $_POST['group_id_'.$content_field->id]), array('id' => $content_field->id));
		}
		$this->getContentFieldsFromDB();

		return true;
	}
	
	public function getContentFieldsFromDB() {
		global $wpdb;

		$this->content_fields_array = array();
		$array = $wpdb->get_results("SELECT * FROM {$wpdb->w2mb_content_fields} ORDER BY order_num, is_core_field", ARRAY_A);
		foreach ($array AS $row) {
			$field_class_name = 'w2mb_content_field_' . $row['type'];
			if (class_exists($field_class_name)) {
				$content_field = new $field_class_name;
				$content_field->buildContentFieldFromArray($row);
				$content_field->convertCategories();
				$content_field->convertOptions();
				$this->content_fields_array[$row['id']] = $content_field;
			}
		}

		$this->content_fields_groups_array = array();
		$array = $wpdb->get_results("SELECT * FROM {$wpdb->w2mb_content_fields_groups}", ARRAY_A);
		foreach ($array AS $row) {
			$content_fields_group = new w2mb_content_fields_group($row);
			$this->content_fields_groups_array[$row['id']] = $content_fields_group;
		}
		
		return true;
	}
	
	public function getContentFieldById($field_id) {
		if (isset($this->content_fields_array[$field_id]))
			return $this->content_fields_array[$field_id];
	}

	public function getContentFieldsGroupById($group_id) {
		if (isset($this->content_fields_groups_array[$group_id]))
			return $this->content_fields_groups_array[$group_id];
	}

	public function getContentFieldBySlug($slug) {
		foreach ($this->content_fields_array AS $content_field) {
			if ($content_field->slug == $slug)
				return $content_field;
		}
	}
	
	public function createContentFieldFromArray($array) {
		if (w2mb_getValue($array, 'type')) {
			$field_class_name = 'w2mb_content_field_' . w2mb_getValue($array, 'type');
			if (class_exists($field_class_name)) {
				$content_field = new $field_class_name;
				if ($content_field->create($array))
					return $this->getContentFieldsFromDB();
			}
		}
		return false;
	}
	
	public function saveContentFieldFromArray($field_id, $array) {
		if ($content_field = $this->getContentFieldById($field_id))
			if ($content_field->save($array))
				return $this->getContentFieldsFromDB();

		return false;
	}
	
	public function deleteContentField($field_id) {
		if ($content_field = $this->getContentFieldById($field_id))
			if ($content_field->delete())
				return $this->getContentFieldsFromDB();
		
		return false;
	}

	public function deleteContentFieldsGroup($group_id) {
		if ($content_fields_group = $this->getContentFieldsGroupById($group_id))
			if ($content_fields_group->delete())
				return $this->getContentFieldsFromDB();
		
		return false;
	}
	
	public function saveListingTabsOption() {
		$vpt_option = get_option('vpt_option');
		
		// remove unnecessary groups from the option
		foreach ($vpt_option['w2mb_listings_tabs_order'] AS $key=>$option) {
			if (strpos($option, 'field-group-tab-') !== FALSE) {
				$fields_group_id = str_replace('field-group-tab-', '', $option);
				$fields_group = $this->getContentFieldsGroupById($fields_group_id);
				if (!$fields_group->on_tab) {
					unset($vpt_option['w2mb_listings_tabs_order'][$key]);
				}
			}
		}
	
		// add new groups in the option
		foreach ($this->content_fields_groups_array AS $fields_group) {
			if ($fields_group->on_tab) {
				if (!in_array('field-group-tab-'.$fields_group->id, $vpt_option['w2mb_listings_tabs_order'])) {
					$vpt_option['w2mb_listings_tabs_order'][] = 'field-group-tab-'.$fields_group->id;
				}
			}
		}
		update_option('vpt_option', $vpt_option);
		update_option('w2mb_listings_tabs_order', $vpt_option['w2mb_listings_tabs_order']);
	}

	public function createContentFieldsGroupFromArray($array) {
		$content_fields_group = new w2mb_content_fields_group;
		if ($content_fields_group->create($array)) {
			$content_fields = $this->getContentFieldsFromDB();
			$this->saveListingTabsOption();
			
			return $content_fields;
		}
		
		return false;
	}
	
	public function saveContentFieldsGroupFromArray($group_id, $array) {
		if ($content_fields_group = $this->getContentFieldsGroupById($group_id)) {
			if ($content_fields_group->save($array)) {
				$content_fields = $this->getContentFieldsFromDB();
				$this->saveListingTabsOption();
				
				return $content_fields;
			}
		}

		return false;
	}

	public function getOrderingContentFields() {
		$fields = array();
		foreach ($this->content_fields_array AS $content_field) {
			if ($content_field->canBeOrdered() && $content_field->is_ordered)
				$fields[] = $content_field;
		}
		return $fields;
	}

	public function getNotCoreContentFields() {
		global $w2mb_instance;
		
		$content_fields = array();
		
		if ($listing = w2mb_getCurrentListingInAdmin()) {
			$content_fields = $listing->content_fields + $w2mb_instance->content_fields->content_fields_array;
		
			// now need to order content fields by their order_num values, because after merge the order is broken
			$order_keys = array_keys($w2mb_instance->content_fields->content_fields_array);
			$ordered_content_fields = array();
			foreach($order_keys as $key) {
				if(array_key_exists($key, $content_fields)) {
					$ordered_content_fields[$key] = $content_fields[$key];
					unset($content_fields[$key]);
				}
			}
			$content_fields = array();
			foreach ($ordered_content_fields AS &$content_field) {
				if ($content_field->is_core_field || !$listing->level->content_fields || in_array($content_field->id, $listing->level->content_fields)) {
					$content_fields[] = $content_field;
				}
			}
		} else {
			$content_fields = $w2mb_instance->content_fields->content_fields_array;
		}
		
		$content_fields = apply_filters('w2mb_content_fields_metabox', $content_fields, $listing);
		
		foreach ($content_fields AS $key=>$content_field) {
			if ($content_field->is_core_field) {
				unset($content_fields[$key]);
			}
		}
		
		return $content_fields;
	}
	
	public function getFieldsByCategoriesIds($categories_ids) {
		/* if ($level_id) {
			global $w2mb_instance;
			$level = $w2mb_instance->levels->getLevelById($level_id);
		} else 
			$level = null;

		$result_fields = array();
		foreach ($this->content_fields_array AS &$content_field) {
			if (
				(!$content_field->isCategories() || $content_field->categories === array() || !is_array($content_field->categories) || array_intersect($content_field->categories, $categories_ids)) &&
				($content_field->is_core_field || !$level || !$level->content_fields || in_array($content_field->id, $level->content_fields))
			)
				$result_fields[$content_field->id] = $content_field;
		}
		return $result_fields; */
		
		return $this->content_fields_array;
	}

	public function saveValues($post_id, $categories_ids, &$errors, $data) {
		$content_fields = $this->getFieldsByCategoriesIds($categories_ids);
		foreach ($content_fields AS $content_field) {
			$local_errors = array();
			if (($validation_results = $content_field->validateValues($local_errors, $data)) !== false && !$local_errors) {
				$content_field->saveValue($post_id, $validation_results);
			} else {
				$errors = array_merge($errors, $local_errors);
			}
		}
	}

	public function loadValues($post_id, $categories_ids) {
		$content_fields = $this->getFieldsByCategoriesIds($categories_ids);
		$result_content_fields = array();
		foreach ($content_fields AS $content_field) {
			$rcontent_field = clone $content_field;
			$rcontent_field->loadValue($post_id);
			$result_content_fields[$content_field->id] = $rcontent_field;
		}
		return $result_content_fields;
	}
	
	public function getOrderParams($defaults = array()) {
		$order_by = w2mb_getValue($_GET, 'order_by', w2mb_getValue($defaults, 'order_by'));

		if ($order_by)
			foreach ($this->content_fields_array AS $content_field) {
				if ($content_field->canBeOrdered() && $content_field->is_ordered && $content_field->slug == $order_by) {
					return $content_field->orderParams();
					break;
				}
			}
		return array();
	}
	
	public function getMapContentFields() {
		if (!$this->map_content_fields) {
			foreach ($this->content_fields_array AS $content_field) {
				if ($content_field->on_map) {
					$this->map_content_fields[$content_field->slug] = clone $content_field;
				}
			}
			
			$this->map_content_fields = apply_filters('w2mb_map_info_window_fields', $this->map_content_fields);
		}
		
		return $this->map_content_fields;
	}

	/**
	 * loops through all content fields and builds special array where items are of content field type
	 * or of content fields group type
	 * 
	 * content fields groups items include related content fields
	 * 
	 * @param array $content_fields_array
	 * @return multitype:multitype: array
	 */
	public function sortContentFieldsByGroups($content_fields_array = null) {
		if (!$content_fields_array)
			$content_fields_array = $this->content_fields_array;

		$result = array();
		foreach ($content_fields_array AS $content_field)
			if ($content_field->group_id && isset($this->content_fields_groups_array[$content_field->group_id])) {
				$content_fields_group = $this->content_fields_groups_array[$content_field->group_id];
				$group_in_array = false;
				foreach ($result AS $item)
					if (is_a($item, 'w2mb_content_fields_group') && $item->id == $content_field->group_id)
						$group_in_array = true;
				if (!$group_in_array) {
					$content_fields_group->setContentFields($content_fields_array);
					$result[] = $content_fields_group;
				}
			} else 
				$result[] = $content_field;
		return $result;
	}
	
	public function renderInputByGroups($post) {
		$content_fields_array = $this->getNotCoreContentFields();
	
		// find out which groups to print and sort them by content fields
		$content_fields_groups = array();
		foreach ($content_fields_array AS $content_field) {
			if ($content_field->group_id && isset($this->content_fields_groups_array[$content_field->group_id])) {
				$group = $this->content_fields_groups_array[$content_field->group_id];
				$group_in_array = false;
				foreach ($content_fields_groups AS $item) {
					if (is_a($item, 'w2mb_content_fields_group') && $item->id == $content_field->group_id) {
						$group_in_array = true;
					}
				}
				if (!$group_in_array) {
					$content_fields_groups[] = $group;
				}
			}
		}
	
		// render fields inside groups
		foreach ($content_fields_groups AS $group) {
			$fields_to_render = array();
			foreach ($content_fields_array AS $content_field) {
				if (
				!$content_field->is_core_field &&
				$group->id == $content_field->group_id &&
				($content_field->filterForAdmins() || $post->post_author == get_current_user_id()) // this content field may be hidden from all users except admins and listing author
				) {
					$fields_to_render[$content_field->id] = $content_field;
				}
			}
			if ($fields_to_render) {
				w2mb_renderTemplate('content_fields/fields_group_input.tpl.php', array('group' => $group, 'content_fields' => $fields_to_render, 'post' => $post));
			}
		}
	
		// now render other fields
		$fields_to_render = array();
		foreach ($content_fields_array AS $content_field) {
			if (
			!$content_field->is_core_field &&
			!$content_field->group_id &&
			($content_field->filterForAdmins() || $post->post_author == get_current_user_id()) // this content field may be hidden from all users except admins and listing author
			) {
				$fields_to_render[$content_field->id] = $content_field;
			}
		}
		if ($fields_to_render) {
			echo '<div class="w2mb-submit-section w2mb-submit-section-content-fields">
					<div class="w2mb-submit-section-inside">';
			w2mb_renderTemplate('content_fields/content_fields_metabox.tpl.php', array('content_fields' => $fields_to_render, 'post' => $post));
			echo '</div>
					</div>';
		}
	}
}

class w2mb_content_fields_group {
	public $id;
	public $name;
	public $on_tab;
	public $hide_anonymous;
	public $content_fields_array = array();

	public function __construct($row = null) {
		if ($row) {
			$this->id = $row['id'];
			$this->name = $row['name'];
			$this->on_tab = $row['on_tab'];
			$this->hide_anonymous = $row['hide_anonymous'];
		}
	}
	
	public function validation() {
		$validation = new w2mb_form_validation();
		$validation->set_rules('name', esc_html__('Content field name', 'W2MB'), 'required');
		$validation->set_rules('on_tab', esc_html__('On tab', 'W2MB'), 'is_checked');
		$validation->set_rules('hide_anonymous', esc_html__('Hide from anonymous', 'W2MB'), 'is_checked');
		return $validation;
	}
	
	public function create($array) {
		global $wpdb;
	
		$insert_update_args = array(
				'name' => w2mb_getValue($array, 'name'),
				'on_tab' => w2mb_getValue($array, 'on_tab'),
				'hide_anonymous' => w2mb_getValue($array, 'hide_anonymous'),
		);
		
		$insert_update_args = apply_filters('w2mb_content_field_group_create_edit_args', $insert_update_args, $this, $array);

		if ($wpdb->insert($wpdb->w2mb_content_fields_groups, $insert_update_args)) {
			$new_content_field_group_id = $wpdb->insert_id;
				
			do_action('w2mb_update_content_field_group', $new_content_field_group_id, $this, $insert_update_args);
			
			return true;
		}
	}
	
	public function save($array) {
		global $wpdb, $w2mb_instance;

		$insert_update_args = array(
				'name' => w2mb_getValue($array, 'name'),
				'on_tab' => w2mb_getValue($array, 'on_tab'),
				'hide_anonymous' => w2mb_getValue($array, 'hide_anonymous'),
		);
		
		$insert_update_args = apply_filters('w2mb_content_field_group_create_edit_args', $insert_update_args, $this, $array);

		if ($wpdb->update($wpdb->w2mb_content_fields_groups, $insert_update_args, array('id' => $this->id), null, array('%d')) !== false) {
			do_action('w2mb_update_content_field_group', $this->id, $this, $insert_update_args);
				
			return true;
		}
	}
	
	public function delete() {
		global $wpdb;

		$wpdb->delete($wpdb->w2mb_content_fields_groups, array('id' => $this->id));
		$wpdb->update($wpdb->w2mb_content_fields, array('group_id' => 0), array('group_id' => $this->id));
		return true;
	}
	
	public function setContentFields($content_fields_array) {
		foreach ($content_fields_array AS $content_field) {
			if ($this->id == $content_field->group_id)
				$this->content_fields_array[$content_field->id] = $content_field;
		}
	}
	
	public function renderOutput($listing, $is_single = true) {
		if ($this->content_fields_array) {
			w2mb_renderTemplate('content_fields/fields_group_output.tpl.php', array('content_fields_group' => $this, 'listing' => $listing, 'is_single' => $is_single));
		}
	}
}

class w2mb_content_field {
	public $id;
	public $is_core_field = 0;
	public $order_num;
	public $name;
	public $slug;
	public $description;
	public $type;
	public $icon_image;
	public $is_required = 0;
	public $is_ordered;
	public $is_hide_name;
	public $for_admin_only;
	public $on_listing_sidebar = 1;
	public $on_listing_page = 1;
	public $on_map;
	public $categories = array();
	public $options;
	public $search_options;
	public $group_id;
	public $value;
	
	protected $can_be_required = true;
	protected $can_be_ordered = true;
	protected $is_categories = true;
	protected $is_slug = true;
	
	protected $is_configuration_page = false;

	protected $can_be_searched = false;
	protected $is_search_configuration_page = false;
	public $on_search_form = false;
	public $advanced_search_form = false;


	public function validation() {
		global $w2mb_instance;

		// core fields can't change type
		if (!$this->is_core_field) {
			if (isset($_POST['type']) && $_POST['type']) {
				// load dummy content field by its new type from $_POST
				$field_class_name = 'w2mb_content_field_' . $_POST['type'];
				if (class_exists($field_class_name)) {
					$process_content_field = new $field_class_name;
				} else {
					w2mb_addMessage('This type of content field does not exist!', 'error');
					$process_content_field = $this;
				}
			} else {
				$process_content_field = $this;
			}
		} else
			$process_content_field = $this;
		
		$validation = new w2mb_form_validation();
		$validation->set_rules('name', esc_html__('Content field name', 'W2MB'), 'required');
		if ($process_content_field->isSlug())
			$validation->set_rules('slug', esc_html__('Content field slug', 'W2MB'), 'required|alpha_dash');
		$validation->set_rules('description', esc_html__('Content field description', 'W2MB'));
		$validation->set_rules('icon_image', esc_html__('Icon image', 'W2MB'));
		if ($process_content_field->canBeRequired())
			$validation->set_rules('is_required', esc_html__('Content field required', 'W2MB'), 'is_checked');
		if ($process_content_field->canBeOrdered())
			$validation->set_rules('is_ordered', esc_html__('Order by field', 'W2MB'), 'is_checked');
		$validation->set_rules('is_hide_name', esc_html__('Hide name', 'W2MB'), 'is_checked');
		$validation->set_rules('for_admin_only', esc_html__('For admin only', 'W2MB'), 'is_checked');
		$validation->set_rules('on_listing_sidebar', esc_html__('On listings sidebar', 'W2MB'), 'is_checked');
		$validation->set_rules('on_listing_page', esc_html__('On listing page', 'W2MB'), 'is_checked');
		$validation->set_rules('on_map', esc_html__('In map marker InfoWindow', 'W2MB'), 'is_checked');
		// core fields can't change type
		if (!$this->is_core_field)
			$validation->set_rules('type', esc_html__('Content field type', 'W2MB'), 'required');
		if ($process_content_field->isCategories())
			$validation->set_rules('categories', esc_html__('Assigned categories', 'W2MB'));
		if ($process_content_field->canBeSearched()) {
			$validation->set_rules('on_search_form', esc_html__('On search form', 'W2MB'), 'is_checked');
			$validation->set_rules('advanced_search_form', esc_html__('On advanced search panel', 'W2MB'), 'is_checked');
		}

		$validation = apply_filters('w2mb_content_field_validation', $validation, $process_content_field);

		if ($process_content_field->isSlug()) {
			global $wpdb;

			if ($wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->w2mb_content_fields} WHERE slug=%s AND id!=%d", $_POST['slug'], $this->id), ARRAY_A)
				|| $_POST['slug'] == 'post_title'
				|| $_POST['slug'] == 'post_name'
				|| $_POST['slug'] == 'post_date'
				|| $_POST['slug'] == 'title'
				|| $_POST['slug'] == 'categories_list'
				|| $_POST['slug'] == 'address'
				|| $_POST['slug'] == 'address_line_1'
				|| $_POST['slug'] == 'address_line_2'
				|| $_POST['slug'] == 'map_coords_1'
				|| $_POST['slug'] == 'map_coords_2'
				|| $_POST['slug'] == 'map_icon_file'
				|| $_POST['slug'] == 'content'
				|| $_POST['slug'] == 'excerpt'
				|| $_POST['slug'] == 'listing_tags'
				|| $_POST['slug'] == 'distance'
				|| $_POST['slug'] == 'user'
				|| $_POST['slug'] == 'zip_or_postal_index'
				|| $_POST['slug'] == 'place_id'
				|| $_POST['slug'] == 'keywords'
			)
				$validation->setError('slug', esc_attr__("Can't use this slug", 'W2MB'));
		}

		return $validation;
	}
	
	public function create($array) {
		global $wpdb;

		$insert_update_args = array(
				'name' => w2mb_getValue($array, 'name'),
				'description' => w2mb_getValue($array, 'description'),
				'type' => w2mb_getValue($array, 'type'),
				'icon_image' => w2mb_getValue($array, 'icon_image'),
				'is_configuration_page' => $this->is_configuration_page,
				'is_search_configuration_page' => $this->is_search_configuration_page,
				'is_hide_name' => w2mb_getValue($array, 'is_hide_name'),
				'for_admin_only' => w2mb_getValue($array, 'for_admin_only'),
				'on_listing_sidebar' => w2mb_getValue($array, 'on_listing_sidebar'),
				'on_listing_page' => w2mb_getValue($array, 'on_listing_page'),
				'on_map' => w2mb_getValue($array, 'on_map'),
		);
		if ($this->isSlug())
			$insert_update_args['slug'] = w2mb_getValue($array, 'slug');
		if ($this->canBeRequired())
			$insert_update_args['is_required'] = w2mb_getValue($array, 'is_required');
		if ($this->canBeOrdered())
			$insert_update_args['is_ordered'] = w2mb_getValue($array, 'is_ordered');
		if ($this->isCategories())
			$insert_update_args['categories'] = serialize(w2mb_getValue($array, 'categories', array()));
		if ($this->canBeSearched()) {
			$insert_update_args['on_search_form'] = w2mb_getValue($array, 'on_search_form');
			$insert_update_args['advanced_search_form'] = w2mb_getValue($array, 'advanced_search_form');
		} else {
			$insert_update_args['on_search_form'] = 0;
			$insert_update_args['advanced_search_form'] = 0;
			$insert_update_args['search_options'] = '';
		}

		$insert_update_args = apply_filters('w2mb_content_field_create_edit_args', $insert_update_args, $this, $array);
		
		if ($wpdb->insert($wpdb->w2mb_content_fields, $insert_update_args)) {
			$new_content_field_id = $wpdb->insert_id;
				
			do_action('w2mb_update_content_field', $new_content_field_id, $this, $array);
			
			return true;
		}
	}
	
	public function save($array) {
		global $wpdb, $w2mb_instance;
		
		// core fields can't change type
		if (!$this->is_core_field) {
			if (isset($_POST['type']) && $_POST['type']) {
				// load dummy content field by its new type from $_POST
				$field_class_name = 'w2mb_content_field_' . $_POST['type'];
				if (class_exists($field_class_name)) {
					$process_content_field = new $field_class_name;
				} else {
					w2mb_addMessage('This type of content field does not exist!', 'error');
					$process_content_field = $this;
				}
			} else {
				$process_content_field = $this;
			}
		} else
			$process_content_field = $this;
		
		$insert_update_args = array(
				'name' => w2mb_getValue($array, 'name'),
				'description' => w2mb_getValue($array, 'description'),
				'icon_image' => w2mb_getValue($array, 'icon_image'),
				'is_hide_name' => w2mb_getValue($array, 'is_hide_name'),
				'for_admin_only' => w2mb_getValue($array, 'for_admin_only'),
				'on_listing_sidebar' => w2mb_getValue($array, 'on_listing_sidebar'),
				'on_listing_page' => w2mb_getValue($array, 'on_listing_page'),
				'on_map' => w2mb_getValue($array, 'on_map'),
		);
		// core fields can't change type
		if (!$this->is_core_field)
			$insert_update_args['type'] = w2mb_getValue($array, 'type');
		if ($process_content_field->isSlug())
			$insert_update_args['slug'] = w2mb_getValue($array, 'slug');

		if ($process_content_field->canBeRequired())
			$insert_update_args['is_required'] = w2mb_getValue($array, 'is_required');
		else
			$insert_update_args['is_required'] = 0;

		if ($process_content_field->canBeOrdered())
			$insert_update_args['is_ordered'] = w2mb_getValue($array, 'is_ordered');
		else
			$insert_update_args['is_ordered'] = 0;

		if ($process_content_field->isCategories())
			$insert_update_args['categories'] = serialize(w2mb_getValue($array, 'categories', array()));
		else
			$insert_update_args['categories'] = '';
		
		if ($process_content_field->isConfigurationPage())
			$insert_update_args['is_configuration_page'] = 1;
		else
			$insert_update_args['is_configuration_page'] = 0;

		if ($process_content_field->isSearchConfigurationPage())
			$insert_update_args['is_search_configuration_page'] = 1;
		else
			$insert_update_args['is_search_configuration_page'] = 0;

		if ($this->canBeSearched()) {
			$insert_update_args['on_search_form'] = w2mb_getValue($array, 'on_search_form');
			$insert_update_args['advanced_search_form'] = w2mb_getValue($array, 'advanced_search_form');
		} else {
			$insert_update_args['on_search_form'] = 0;
			$insert_update_args['advanced_search_form'] = 0;
			$insert_update_args['search_options'] = '';
		}

		$insert_update_args = apply_filters('w2mb_content_field_create_edit_args', $insert_update_args, $process_content_field, $array);

		if ($wpdb->update($wpdb->w2mb_content_fields, $insert_update_args, array('id' => $this->id), null, array('%d')) !== false) {
			do_action('w2mb_update_content_field', $this->id, $process_content_field, $array);
			return true;
		}
	}
	
	public function delete() {
		global $wpdb;

		$wpdb->delete($wpdb->postmeta, array('meta_key' => '_content_field_' . $this->id));

		$wpdb->delete($wpdb->w2mb_content_fields, array('id' => $this->id));
		return true;
	}

	public function buildContentFieldFromArray($array) {
		$this->id = w2mb_getValue($array, 'id');
		$this->is_core_field = w2mb_getValue($array, 'is_core_field');
		$this->order_num = w2mb_getValue($array, 'order_num');
		$this->name = w2mb_getValue($array, 'name');
		$this->slug = w2mb_getValue($array, 'slug');
		$this->description = w2mb_getValue($array, 'description');
		$this->type = w2mb_getValue($array, 'type');
		$this->icon_image = w2mb_getValue($array, 'icon_image');
		$this->is_required = w2mb_getValue($array, 'is_required');
		$this->is_configuration_page = w2mb_getValue($array, 'is_configuration_page');
		$this->is_search_configuration_page = w2mb_getValue($array, 'is_search_configuration_page');
		$this->on_search_form = w2mb_getValue($array, 'on_search_form');
		$this->advanced_search_form = w2mb_getValue($array, 'advanced_search_form');
		$this->is_ordered = w2mb_getValue($array, 'is_ordered');
		$this->is_hide_name = w2mb_getValue($array, 'is_hide_name');
		$this->for_admin_only = w2mb_getValue($array, 'for_admin_only');
		$this->on_listing_sidebar = w2mb_getValue($array, 'on_listing_sidebar');
		$this->on_listing_page = w2mb_getValue($array, 'on_listing_page');
		$this->on_map = w2mb_getValue($array, 'on_map');
		$this->categories = w2mb_getValue($array, 'categories');
		$this->options = w2mb_getValue($array, 'options');
		$this->search_options = w2mb_getValue($array, 'search_options');
		$this->group_id = w2mb_getValue($array, 'group_id');
	}
	
	public function convertCategories() {
		if ($this->categories) {
			$unserialized_categories = maybe_unserialize($this->categories);
			if ((is_array($unserialized_categories) && count($unserialized_categories) > 1) || $unserialized_categories != array('')) {
				$this->categories = $unserialized_categories;
			} else {
				$this->categories = array();
			}
		} else { 
			$this->categories = array();
		}
		return $this->categories;
	}

	public function convertOptions() {
		if ($this->options) {
			$unserialized_options = maybe_unserialize($this->options);
			if ((is_array($unserialized_options) && count($unserialized_options) > 1) || $unserialized_options != array('')) {
				$this->options = $unserialized_options;
				if (method_exists($this, 'buildOptions')) {
					$this->buildOptions();
				}
				return $this->options;
			}
		}
		return array();
	}
	
	public function canBeRequired() {
		return $this->can_be_required;
	}

	public function canBeOrdered() {
		return $this->can_be_ordered;
	}

	public function isSlug() {
		return $this->is_slug;
	}

	public function isCategories() {
		return $this->is_categories;
	}

	public function isConfigurationPage() {
		return $this->is_configuration_page;
	}

	public function isSearchConfigurationPage() {
		return $this->is_search_configuration_page;
	}

	public function canBeSearched() {
		return $this->can_be_searched;
	}
	
	public function validateValues(&$errors, $data) {
		return true;
	}

	public function validateCsvValues($value, &$errors) {
		return true;
	}
	
	public function exportCSV() {
		if ($this->value) {
			return $this->value;
		}
	}

	public function saveValue($post_id, $validation_results) {
		return true;
	}

	public function loadValue($post_id) {
		return true;
	}
	
	public function renderOutput($listing, $group = null) {
		return true;
	}

	public function renderOutputForMap($location, $string) {
		return true;
	}

	public function isEmpty($listing) {
		if ($this->value)
			return false;
		else 
			return true;
	}
	
	public function filterForAdmins($listing = null) {
		if (!$this->for_admin_only || current_user_can("manage_options")) {
			return true;
		}
	}
}

// adapted for WPML
add_action('init', 'w2mb_content_fields_names_into_strings');
function w2mb_content_fields_names_into_strings() {
	global $w2mb_instance, $sitepress;

	if (function_exists('wpml_object_id_filter') && $sitepress) {
		foreach ($w2mb_instance->content_fields->content_fields_array AS &$content_field) {
			$content_field->name = apply_filters('wpml_translate_single_string', $content_field->name, 'MapBox locator', 'The name of content field #' . $content_field->id);
			$content_field->description = apply_filters('wpml_translate_single_string', $content_field->description, 'MapBox locator', 'The description of content field #' . $content_field->id);
		}
		foreach ($w2mb_instance->content_fields->content_fields_groups_array AS &$content_fields_group) {
			$content_fields_group->name = apply_filters('wpml_translate_single_string', $content_fields_group->name, 'MapBox locator', 'The name of content fields group #' . $content_fields_group->id);
		}
	}
}

add_filter('w2mb_content_field_create_edit_args', 'w2mb_filter_content_field_fields', 10, 3);
function w2mb_filter_content_field_fields($insert_update_args, $content_field, $array) {
	global $sitepress, $wpdb;

	if (function_exists('wpml_object_id_filter') && $sitepress) {
		if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE) {
			if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['field_id'])) {
				$content_field_id = $_GET['field_id'];
				if ($name_string_id = icl_st_is_registered_string('MapBox locator', 'The name of content field #' . $content_field_id))
					icl_add_string_translation($name_string_id, ICL_LANGUAGE_CODE, $insert_update_args['name'], ICL_TM_COMPLETE);
				if ($description_string_id = icl_st_is_registered_string('MapBox locator', 'The description of content field #' . $content_field_id))
					icl_add_string_translation($description_string_id, ICL_LANGUAGE_CODE, $insert_update_args['description'], ICL_TM_COMPLETE);
				unset($insert_update_args['name']);
				unset($insert_update_args['description']);
				unset($insert_update_args['slug']);

				unset($insert_update_args['categories']);
			} else {
				$insert_update_args['categories'] = '';
			}
		}
	}
	return $insert_update_args;
}

add_action('w2mb_update_content_field', 'w2mb_save_content_field', 10, 3);
function w2mb_save_content_field($content_field_id, $content_field, $array) {
	global $sitepress;

	if (function_exists('wpml_object_id_filter') && $sitepress) {
		if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE && $content_field->isCategories()) {
			update_option('w2mb_wpml_content_field_categories_'.$content_field_id.'_'.ICL_LANGUAGE_CODE, w2mb_getValue($array, 'categories'));
		}
		
		if ($sitepress->get_default_language() == ICL_LANGUAGE_CODE) {
			do_action('wpml_register_single_string', 'MapBox locator', 'The name of content field #' . $content_field_id, w2mb_getValue($array, 'name'));
			do_action('wpml_register_single_string', 'MapBox locator', 'The description of content field #' . $content_field_id, w2mb_getValue($array, 'description'));
		}
	}
}

add_filter('w2mb_content_field_group_create_edit_args', 'w2mb_filter_content_field_group_fields', 10, 3);
function w2mb_filter_content_field_group_fields($insert_update_args, $content_field_group, $array) {
	global $sitepress, $wpdb;

	if (function_exists('wpml_object_id_filter') && $sitepress) {
		if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE) {
			if (isset($_GET['action']) && $_GET['action'] == 'edit_group' && isset($_GET['group_id'])) {
				$content_field_group_id = $_GET['group_id'];
				if ($name_string_id = icl_st_is_registered_string('MapBox locator', 'The name of content fields group #' . $content_field_group_id))
					icl_add_string_translation($name_string_id, ICL_LANGUAGE_CODE, $insert_update_args['name'], ICL_TM_COMPLETE);
				unset($insert_update_args['name']);
			}
		}
	}
	return $insert_update_args;
}

add_action('w2mb_update_content_field_group', 'w2mb_save_content_field_group', 10, 3);
function w2mb_save_content_field_group($content_field_group_id, $content_field_group, $array) {
	global $sitepress;
	
	if (function_exists('wpml_object_id_filter') && $sitepress) {
		if ($sitepress->get_default_language() == ICL_LANGUAGE_CODE) {
			do_action('wpml_register_single_string', 'MapBox locator', 'The name of content fields group #' . $content_field_group_id, w2mb_getValue($array, 'name'));
		}
	}
}

add_action('init', 'w2mb_load_content_fields_categories');
function w2mb_load_content_fields_categories() {
	global $w2mb_instance, $sitepress;

	if (function_exists('wpml_object_id_filter') && $sitepress) {
		if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE) {
			foreach ($w2mb_instance->content_fields->content_fields_array AS &$content_field) {
				if ($content_field->isCategories()) {
					$_categories = get_option('w2mb_wpml_content_field_categories_'.$content_field->id.'_'.ICL_LANGUAGE_CODE);
					if ($_categories && (count($_categories) > 1 || $_categories != array('')))
						$content_field->categories = $_categories;
					else
						$content_field->categories = array();
				}
			}
		}
	}
}

?>