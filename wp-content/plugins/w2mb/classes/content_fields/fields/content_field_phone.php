<?php 

class w2mb_content_field_phone extends w2mb_content_field_string {
	public $max_length = 255;
	public $regex;
	public $phone_mode = 'phone';
	
	protected $can_be_searched = true;
	protected $is_configuration_page = true;
	protected $is_search_configuration_page = true;

	public function configure() {
		global $wpdb, $w2mb_instance;

		if (w2mb_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2mb_configure_content_fields_nonce'], W2MB_PATH)) {
			$validation = new w2mb_form_validation();
			$validation->set_rules('max_length', esc_html__('Max length', 'W2MB'), 'required|is_natural_no_zero');
			$validation->set_rules('regex', esc_html__('PHP RegEx template', 'W2MB'));
			$validation->set_rules('phone_mode', esc_html__('Phone mode', 'W2MB'));
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2mb_content_fields, array('options' => serialize(array('max_length' => $result['max_length'], 'regex' => $result['regex'], 'phone_mode' => $result['phone_mode']))), array('id' => $this->id), null, array('%d'))) {
					w2mb_addMessage(esc_html__('Field configuration was updated successfully!', 'W2MB'));
				}
				
				$w2mb_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->max_length = $validation->result_array('max_length');
				$this->regex = $validation->result_array('regex');
				$this->phone_mode = $validation->result_array('phone_mode');
				w2mb_addMessage($validation->error_array(), 'error');

				w2mb_renderTemplate('content_fields/fields/phone_configuration.tpl.php', array('content_field' => $this));
			}
		} else
			w2mb_renderTemplate('content_fields/fields/phone_configuration.tpl.php', array('content_field' => $this));
	}
	
	public function buildOptions() {
		if (isset($this->options['max_length'])) {
			$this->max_length = $this->options['max_length'];
		}

		if (isset($this->options['regex'])) {
			$this->regex = $this->options['regex'];
		}
		
		if (isset($this->options['phone_mode'])) {
			$this->phone_mode = $this->options['phone_mode'];
		}
	}
	
	public function renderInput() {
		if (!($template = w2mb_isTemplate('content_fields/fields/phone_input_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/phone_input.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_input_template', $template, $this);
			
		w2mb_renderTemplate($template, array('content_field' => $this));
	}
	
	public function renderOutput($listing, $group = null) {
		if (!($template = w2mb_isTemplate('content_fields/fields/phone_output_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/phone_output.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
			
		w2mb_renderTemplate($template, array('content_field' => $this, 'listing' => $listing, 'group' => $group));
	}
	
	public function renderOutputForMap($location, $listing) {
		if ($this->value) {
			if ($this->phone_mode == 'phone') {
				return '<a href="tel:' . $this->value . '">' . antispambot($this->value) . '</a>';
			} elseif ($this->phone_mode == 'viber') {
				return '<a href="viber://chat?number=' . $this->value . '">' . antispambot($this->value) . '</a>';
			} elseif ($this->phone_mode == 'whatsapp') {
				return '<a href="https://wa.me/' . $this->value . '">' . antispambot($this->value) . '</a>';
			} elseif ($this->phone_mode == 'telegram') {
				return '<a href="tg://resolve?domain=' . $this->value . '">' . antispambot($this->value) . '</a>';
			}
		}
	}
}
?>