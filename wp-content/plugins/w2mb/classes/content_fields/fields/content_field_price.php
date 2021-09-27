<?php 

class w2mb_content_field_price extends w2mb_content_field {
	public $is_integer = false; // required for the search function
	public $currency_symbol = '$';
	public $decimal_separator = ',';
	public $thousands_separator = ' ';
	public $symbol_position = 1;
	public $hide_decimals = 0;

	protected $is_configuration_page = true;
	protected $is_search_configuration_page = true;
	protected $can_be_searched = true;
	
	public function isNotEmpty($listing) {
		if ($this->value) {
			return true;
		} else {
			return false;
		}
	}

	public function configure() {
		global $wpdb, $w2mb_instance;

		if (w2mb_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2mb_configure_content_fields_nonce'], W2MB_PATH)) {
			$validation = new w2mb_form_validation();
			$validation->set_rules('currency_symbol', esc_html__('Currency symbol', 'W2MB'), 'required');
			$validation->set_rules('decimal_separator', esc_html__('Decimal separator', 'W2MB'), 'required|max_length[1]');
			$validation->set_rules('thousands_separator', esc_html__('Thousands separator', 'W2MB'), 'max_length[1]');
			$validation->set_rules('symbol_position', esc_html__('Currency symbol position', 'W2MB'), 'integer');
			$validation->set_rules('hide_decimals', esc_html__('Hide decimals', 'W2MB'), 'required');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2mb_content_fields, array('options' => serialize(
						array(
								'currency_symbol' => $result['currency_symbol'],
								'decimal_separator' => $result['decimal_separator'],
								'thousands_separator' => $result['thousands_separator'],
								'symbol_position' => $result['symbol_position'],
								'hide_decimals' => $result['hide_decimals'],
						)
					)), array('id' => $this->id), null, array('%d'))) {
						w2mb_addMessage(esc_html__('Field configuration was updated successfully!', 'W2MB'));
				}
				
				$w2mb_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->currency_symbol = $validation->result_array('currency_symbol');
				$this->decimal_separator = $validation->result_array('decimal_separator');
				$this->thousands_separator = $validation->result_array('thousands_separator');
				$this->symbol_position = $validation->result_array('symbol_position');
				$this->hide_decimals = $validation->result_array('hide_decimals');
				w2mb_addMessage($validation->error_array(), 'error');

				w2mb_renderTemplate('content_fields/fields/price_configuration.tpl.php', array('content_field' => $this));
			}
		} else
			w2mb_renderTemplate('content_fields/fields/price_configuration.tpl.php', array('content_field' => $this));
	}
	
	public function buildOptions() {
		if (isset($this->options['currency_symbol'])) {
			$this->currency_symbol = $this->options['currency_symbol'];
		}
		if (isset($this->options['decimal_separator'])) {
			$this->decimal_separator = $this->options['decimal_separator'];
		}
		if (isset($this->options['thousands_separator'])) {
			$this->thousands_separator = $this->options['thousands_separator'];
		}
		if (isset($this->options['symbol_position'])) {
			$this->symbol_position = $this->options['symbol_position'];
		}
		if (isset($this->options['hide_decimals'])) {
			$this->hide_decimals = $this->options['hide_decimals'];
		}
	}
	
	public function renderInput() {
		if (!($template = w2mb_isTemplate('content_fields/fields/price_input_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/price_input.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_input_template', $template, $this);
			
		w2mb_renderTemplate($template, array('content_field' => $this));
	}
	
	public function validateValues(&$errors, $data) {
		$field_index = 'w2mb-field-input-' . $this->id;
	
		$validation = new w2mb_form_validation();
		$rules = 'numeric';
		if ($this->canBeRequired() && $this->is_required)
			$rules .= '|required';
		$validation->set_rules($field_index, $this->name, $rules);
		if (!$validation->run()) {
			$errors[] = $validation->error_array();
		}

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
		if (is_numeric($this->value)) {
			if (!($template = w2mb_isTemplate('content_fields/fields/price_output_'.$this->id.'.tpl.php'))) {
				$template = 'content_fields/fields/price_output.tpl.php';
			}
			
			$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
				
			w2mb_renderTemplate($template, array('content_field' => $this, 'listing' => $listing, 'group' => $group));
		}
	}
	
	public function orderParams() {
		$order_params = array('orderby' => 'meta_value_num', 'meta_key' => '_content_field_' . $this->id);
		if (get_option('w2mb_orderby_exclude_null')) {
			$order_params['meta_query'] = array(
				array(
					'key' => '_content_field_' . $this->id,
					'value'   => array(''),
					'compare' => 'NOT IN'
				)
			);
		}
		return $order_params;
	}
	
	public function validateCsvValues($value, &$errors) {
		if (!is_numeric($value)) {
			$errors[] = sprintf(esc_html__('The %s field must contain only numbers.', 'W2MB'), $this->name);
		}

		return $value;
	}
	
	public function renderOutputForMap($location, $listing) {
		if (is_numeric($this->value)) {
			return $this->formatPrice();
		}
	}
	
	public function formatPrice($value = null) {
		if (is_null($value)) {
			$value = $this->value;
		}
		if ($this->hide_decimals) {
			$decimals = 0;
		} else {
			$decimals = 2;
		}
		$formatted_price = number_format($value, $decimals, $this->decimal_separator, $this->thousands_separator);

		$out = $formatted_price;
		switch ($this->symbol_position) {
			case 1:
				$out = $this->currency_symbol . $out;
				break;
			case 2:
				$out = $this->currency_symbol . ' ' . $out;
				break;
			case 3:
				$out = $out . $this->currency_symbol;
				break;
			case 4:
				$out = $out . ' ' . $this->currency_symbol;
				break;
		}
		return $out;
	}
}
?>