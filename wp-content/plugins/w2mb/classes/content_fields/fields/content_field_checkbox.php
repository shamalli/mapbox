<?php 

class w2mb_content_field_checkbox extends w2mb_content_field_select {
	public $how_display_items = 'checked';
	public $columns_number = 3;
	public $value = array();
	public $icon_images = array();
	
	protected $can_be_searched = true;
	protected $is_search_configuration_page = true;
	
	public function configure() {
		global $wpdb, $w2mb_instance;
		
		wp_enqueue_script('jquery-ui-sortable');
	
		if (w2mb_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2mb_configure_content_fields_nonce'], W2MB_PATH)) {
			$validation = new w2mb_form_validation();
			$validation->set_rules('selection_items[]', esc_html__('Selection items', 'W2MB'), 'required');
			$validation->set_rules('icon_images[]', esc_html__('Icon images', 'W2MB'));
			$validation->set_rules('how_display_items', esc_html__('How to display items', 'W2MB'), 'required');
			$validation->set_rules('columns_number', esc_html__('Columns number', 'W2MB'), 'required|integer');
			if ($validation->run()) {
				$result = $validation->result_array();
	
				$insert_update_args['selection_items'] = $result['selection_items[]'];
				$insert_update_args['icon_images'] = $result['icon_images[]'];
				$insert_update_args['how_display_items'] = $result['how_display_items'];
				$insert_update_args['columns_number'] = $result['columns_number'];
	
				$insert_update_args = apply_filters('w2mb_selection_items_update_args', $insert_update_args, $this, $result);
	
				if ($insert_update_args) {
					$wpdb->update($wpdb->w2mb_content_fields, array('options' => serialize($insert_update_args)), array('id' => $this->id), null, array('%d'));
				}
	
				w2mb_addMessage(esc_html__('Field configuration was updated successfully!', 'W2MB'));
	
				do_action('w2mb_update_selection_items', $result['selection_items[]'], $this);
	
				$w2mb_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->selection_items = $validation->result_array('selection_items[]');
				$this->icon_images = $validation->result_array('icon_images[]');
				$this->how_display_items = $validation->result_array('how_display_items');
				$this->columns_number = $validation->result_array('columns_number');
				w2mb_addMessage($validation->error_array(), 'error');
	
				w2mb_renderTemplate('content_fields/fields/checkboxes_configuration.tpl.php', array('content_field' => $this));
			}
		} else {
			w2mb_renderTemplate('content_fields/fields/checkboxes_configuration.tpl.php', array('content_field' => $this));
		}
	}
	
	public function buildOptions() {
		if (isset($this->options['selection_items'])) {
			$this->selection_items = $this->options['selection_items'];
		}
		if (isset($this->options['icon_images'])) {
			$this->icon_images = $this->options['icon_images'];
		} else {
			foreach ($this->selection_items AS $key=>$item) {
				$this->icon_images[$key] = '';
			}
		}
		if (isset($this->options['how_display_items'])) {
			$this->how_display_items = $this->options['how_display_items'];
		}
		if (isset($this->options['columns_number'])) {
			$this->columns_number = $this->options['columns_number'];
		}
	}

	public function renderInput() {
		if (!($template = w2mb_isTemplate('content_fields/fields/checkbox_input_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/checkbox_input.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_input_template', $template, $this);
			
		w2mb_renderTemplate($template, array('content_field' => $this));
	}
	
	public function validateValues(&$errors, $data) {
		$field_index = 'w2mb-field-input-' . $this->id . '[]';

		$validation = new w2mb_form_validation();
		$validation->set_rules($field_index, $this->name);
		if (!$validation->run())
			$errors[] = $validation->error_array();
		elseif ($selected_items_array = $validation->result_array($field_index)) {
			foreach ($selected_items_array AS $selected_item) {
				if (!in_array($selected_item, array_keys($this->selection_items)))
					$errors[] = sprintf(esc_html__("This selection option index \"%d\" doesn't exist", 'W2MB'), $selected_item);
			}
	
			return $selected_items_array;
		} elseif ($this->canBeRequired() && $this->is_required)
			$errors[] = sprintf(esc_html__('At least one option must be selected in "%s" content field', 'W2MB'), $this->name);
	}
	
	public function saveValue($post_id, $validation_results) {
		delete_post_meta($post_id, '_content_field_' . $this->id);
		if ($validation_results && is_array($validation_results)) {
			foreach ($validation_results AS $value)
				add_post_meta($post_id, '_content_field_' . $this->id, $value);
		}
		return true;
	}
	
	public function loadValue($post_id) {
		if (!($this->value = get_post_meta($post_id, '_content_field_' . $this->id)) || $this->value[0] == '')
			$this->value = array();
		else {
			$result = array();
			foreach ($this->selection_items AS $key=>$value) {
				if (array_search($key, $this->value) !== FALSE)
					$result[] = $key;
			}
			$this->value = $result;
		}
		
		$this->value = apply_filters('w2mb_content_field_load', $this->value, $this, $post_id);
		return $this->value;
	}
	
	public function renderOutput($listing, $group = null) {
		if (!($template = w2mb_isTemplate('content_fields/fields/checkbox_output_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/checkbox_output.tpl.php';
		}

		$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
			
		w2mb_renderTemplate($template, array('content_field' => $this, 'listing' => $listing, 'group' => $group));
	}
	
	public function validateCsvValues($value, &$errors) {
		if ($value) {
			$output = array();
			foreach ((array) $value AS $key=>$selected_item) {
				if (array_key_exists($selected_item, $this->selection_items)) {
					$output[] = $selected_item;
					continue;
				}

				if (!in_array($selected_item, $this->selection_items))
					$errors[] = sprintf(esc_html__("This selection option \"%s\" doesn't exist", 'W2MB'), $selected_item);
				else
					$output[] = array_search($selected_item, $this->selection_items);
			}
			return $output;
		} else 
			return '';
	}
	
	public function exportCSV() {
		return implode(';', $this->value);
	}
	
	public function renderOutputForMap($location, $listing) {
		return w2mb_renderTemplate('content_fields/fields/checkbox_output_map.tpl.php', array('content_field' => $this, 'listing' => $listing), true);
	}
}
?>