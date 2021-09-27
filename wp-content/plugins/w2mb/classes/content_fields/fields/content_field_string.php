<?php 

class w2mb_content_field_string extends w2mb_content_field {
	public $max_length = 255;
	public $regex;
	
	protected $can_be_searched = true;
	protected $is_configuration_page = true;
	protected $is_search_configuration_page = true;
	
	public function isNotEmpty($listing) {
		if ($this->value)
			return true;
		else
			return false;
	}

	public function configure() {
		global $wpdb, $w2mb_instance;

		if (w2mb_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2mb_configure_content_fields_nonce'], W2MB_PATH)) {
			$validation = new w2mb_form_validation();
			$validation->set_rules('max_length', esc_html__('Max length', 'W2MB'), 'required|is_natural_no_zero');
			$validation->set_rules('regex', esc_html__('PHP RegEx template', 'W2MB'));
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2mb_content_fields, array('options' => serialize(array('max_length' => $result['max_length'], 'regex' => $result['regex']))), array('id' => $this->id), null, array('%d'))) {
					w2mb_addMessage(esc_html__('Field configuration was updated successfully!', 'W2MB'));
				}
				
				$w2mb_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->max_length = $validation->result_array('max_length');
				$this->regex = $validation->result_array('regex');
				w2mb_addMessage($validation->error_array(), 'error');

				w2mb_renderTemplate('content_fields/fields/string_configuration.tpl.php', array('content_field' => $this));
			}
		} else
			w2mb_renderTemplate('content_fields/fields/string_configuration.tpl.php', array('content_field' => $this));
	}
	
	public function buildOptions() {
		if (isset($this->options['max_length'])) {
			$this->max_length = $this->options['max_length'];
		}

		if (isset($this->options['regex'])) {
			$this->regex = $this->options['regex'];
		}
	}
	
	public function renderInput() {
		if (!($template = w2mb_isTemplate('content_fields/fields/string_input_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/string_input.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_input_template', $template, $this);
			
		w2mb_renderTemplate($template, array('content_field' => $this));
	}
	
	public function validateValues(&$errors, $data) {
		$field_index = 'w2mb-field-input-' . $this->id;
		
		if (isset($_POST[$field_index]) && $_POST[$field_index] && $this->regex)
			if (@!preg_match('/^' . $this->regex . '$/', $_POST[$field_index]))
				$errors[] = sprintf(esc_html__("Field %s doesn't match template!", 'W2MB'), $this->name);

		$validation = new w2mb_form_validation();
		$rules = 'max_length[' . $this->max_length . ']';
		if ($this->canBeRequired() && $this->is_required)
			$rules .= '|required';
		$validation->set_rules($field_index, $this->name, $rules);
		if (!$validation->run())
			$errors[] = $validation->error_array();

		return $validation->result_array($field_index);
	}
	
	public function saveValue($post_id, $validation_results) {
		return update_post_meta($post_id, '_content_field_' . $this->id, $validation_results);
	}
	
	public function loadValue($post_id) {
		$this->value = get_post_meta($post_id, '_content_field_' . $this->id, true);
		
		$this->value = apply_filters('w2mb_content_field_load', $this->value, $this, $post_id);
		return $this->value;
	}
	
	public function renderOutput($listing, $group = null) {
		if (!($template = w2mb_isTemplate('content_fields/fields/string_output_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/string_output.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
			
		w2mb_renderTemplate($template, array('content_field' => $this, 'listing' => $listing, 'group' => $group));
	}
	
	public function orderParams() {
		$order_params = array('orderby' => 'meta_value', 'meta_key' => '_content_field_' . $this->id);
		if (get_option('w2mb_orderby_exclude_null'))
			$order_params['meta_query'] = array(
				array(
					'key' => '_content_field_' . $this->id,
					'value'   => array(''),
					'compare' => 'NOT IN'
				)
			);
		return $order_params;
	}

	public function validateCsvValues($value, &$errors) {
		if (!is_string($value))
			$errors[] = sprintf(esc_html__('Field %s must be a string!', 'W2MB'), $this->name);
		elseif ($this->regex && @!preg_match('/^' . $this->regex . '$/', $value))
			$errors[] = sprintf(esc_html__("Field %s doesn't match template!", 'W2MB'), $this->name);
		elseif (strlen($value) > $this->max_length)
			$errors[] = sprintf(esc_html__('The %s field can not exceed %s characters in length.', 'W2MB'), $this->name, $this->max_length);
		else
			return $value;
	}
	
	public function renderOutputForMap($location, $listing) {
		return $this->value;
	}
}
?>