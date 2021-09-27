<?php 

class w2mb_content_field_number_search extends w2mb_content_field_search {
	public $mode = 'exact_number';
	public $min_max_options = array();
	public $min_max_value;
	public $slider_step_1_min = 0;
	public $slider_step_1_max = 100;

	public function searchConfigure() {
		global $wpdb, $w2mb_instance;

		if (w2mb_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2mb_configure_content_fields_nonce'], W2MB_PATH)) {
			$validation = new w2mb_form_validation();
			$validation->set_rules('mode', esc_html__('Search mode', 'W2MB'), 'required|alpha_dash');
			$validation->set_rules('min_max_options[]', esc_html__('Min-Max options', 'W2MB'), 'numeric');
			$validation->set_rules('slider_step_1_min', esc_html__('From option', 'W2MB'), 'integer');
			$validation->set_rules('slider_step_1_max', esc_html__('To option', 'W2MB'), 'integer');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2mb_content_fields, array('search_options' => serialize(array('mode' => $result['mode'], 'min_max_options' => $result['min_max_options[]'], 'slider_step_1_min' => $result['slider_step_1_min'], 'slider_step_1_max' => $result['slider_step_1_max']))), array('id' => $this->content_field->id), null, array('%d')))
					w2mb_addMessage(esc_html__('Search field configuration was updated successfully!', 'W2MB'));
				
				$w2mb_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->mode = $validation->result_array('mode');
				$this->min_max_options = $validation->result_array('min_max_options[]');
				$this->slider_step_1_min = $validation->result_array('slider_step_1_min');
				$this->slider_step_1_max = $validation->result_array('slider_step_1_max');
				w2mb_addMessage($validation->error_array(), 'error');

				w2mb_renderTemplate('search_fields/fields/number_price_configuration.tpl.php', array('search_field' => $this));
			}
		} else
			w2mb_renderTemplate('search_fields/fields/number_price_configuration.tpl.php', array('search_field' => $this));
	}
	
	public function buildSearchOptions() {
		if (isset($this->content_field->search_options['mode']))
			$this->mode = $this->content_field->search_options['mode'];
		if (isset($this->content_field->search_options['min_max_options']))
			$this->min_max_options = $this->content_field->search_options['min_max_options'];
		if (isset($this->content_field->search_options['slider_step_1_min']))
			$this->slider_step_1_min = $this->content_field->search_options['slider_step_1_min'];
		if (isset($this->content_field->search_options['slider_step_1_max']))
			$this->slider_step_1_max = $this->content_field->search_options['slider_step_1_max'];

		// set up Search range slider with step 1 when there aren't enough min-max options
		if ($this->mode == 'min_max_slider' && count($this->min_max_options) < 2)
			$this->mode = 'range_slider';
			
	}
	
	public function isParamOfThisField($param) {
		if ($this->mode == 'exact_number') {
			if ($param == 'field_' . $this->content_field->slug)
				return true;
		} elseif ($this->mode == 'min_max_exact_number' || $this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			if ($param == 'field_' . $this->content_field->slug . '_min' || $param == 'field_' . $this->content_field->slug . '_max')
				return true;
		}
	}
	
	public function renderSearch($search_form_id, $columns = 2, $defaults = array()) {
		if ($this->mode == 'exact_number') {
			if (is_null($this->value)) {
				if (isset($defaults['field_' . $this->content_field->slug])) {
					$this->value = $defaults['field_' . $this->content_field->slug];
				}
			}
		} elseif ($this->mode == 'min_max_exact_number' || $this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			if (is_null($this->min_max_value)) {
				$this->min_max_value['min'] = false;
				$this->min_max_value['max'] = false;
				
				if (isset($defaults['field_' . $this->content_field->slug . '_min'])) {
					$this->min_max_value['min'] = $defaults['field_' . $this->content_field->slug . '_min'];
				}
				if (isset($defaults['field_' . $this->content_field->slug . '_max'])) {
					$this->min_max_value['max'] = $defaults['field_' . $this->content_field->slug . '_max'];
				}
			}
		}

		if ($this->mode == 'exact_number') {
			w2mb_renderTemplate('search_fields/fields/number_input_exactnumber.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		} elseif ($this->mode == 'min_max_exact_number') {
			w2mb_renderTemplate('search_fields/fields/number_input_minmax_exactnumber.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		} elseif ($this->mode == 'min_max') {
			w2mb_renderTemplate('search_fields/fields/number_input_minmax.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		} elseif ($this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			w2mb_renderTemplate('search_fields/fields/number_input_slider.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		}
	}
	
	public function loadValue($defaults = array(), $include_GET_params = true) {
		if ($this->mode == 'exact_number') {
			$field_index = 'field_' . $this->content_field->slug;
		
			if ($include_GET_params) {
				$this->value = ((w2mb_getValue($_REQUEST, $field_index, false) !== false) ? w2mb_getValue($_REQUEST, $field_index) : w2mb_getValue($defaults, $field_index));
			} else {
				$this->value = w2mb_getValue($defaults, $field_index, false);
			}
		} elseif ($this->mode == 'min_max_exact_number' || $this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			$field_index = 'field_' . $this->content_field->slug . '_min';
				
			if ($include_GET_params) {
				$this->min_max_value['min'] = ((w2mb_getValue($_REQUEST, $field_index, false) !== false) ? w2mb_getValue($_REQUEST, $field_index) : w2mb_getValue($defaults, $field_index));
			} else {
				$this->min_max_value['min'] = w2mb_getValue($defaults, $field_index, false);
			}
		
			$field_index = 'field_' . $this->content_field->slug . '_max';
				
			if ($include_GET_params) {
				$this->min_max_value['max'] = ((w2mb_getValue($_REQUEST, $field_index, false) !== false) ? w2mb_getValue($_REQUEST, $field_index) : w2mb_getValue($defaults, $field_index));
			} else {
				$this->min_max_value['max'] = w2mb_getValue($defaults, $field_index, false);
			}
		}
		
		$this->value_loaded = true;
	}
	
	public function validateSearch(&$args, $defaults = array(), $include_GET_params = true) {
		if (!$this->value_loaded) {
			$this->loadValue($defaults, $include_GET_params);
		}
		
		$meta_query_type = 'numeric';
		if (!$this->content_field->is_integer) {
			$meta_query_type = 'decimal';
		}
		
		if ($this->mode == 'exact_number') {
			if ($this->value !== false && is_numeric($this->value)) {
				$args['meta_query']['relation'] = 'AND';
				$args['meta_query'][] = array(
						'key' => '_content_field_' . $this->content_field->id,
						'value' => $this->value,
						'type' => $meta_query_type
				);
			}
		} elseif ($this->mode == 'min_max_exact_number' || $this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			if ($this->min_max_value['min'] !== false && is_numeric($this->min_max_value['min'])) {
				$args['meta_query']['relation'] = 'AND';
				$args['meta_query'][] = array(
						'key' => '_content_field_' . $this->content_field->id,
						'value' => $this->min_max_value['min'],
						'type' => $meta_query_type,
						'compare' => '>='
				);
			}

			if ($this->min_max_value['max'] !== false && is_numeric($this->min_max_value['max'])) {
				$args['meta_query']['relation'] = 'AND';
				$args['meta_query'][] = array(
						'key' => '_content_field_' . $this->content_field->id,
						'value' => $this->min_max_value['max'],
						'type' => $meta_query_type,
						'compare' => '<='
				);
			}
		}
	}
	
	public function getBaseUrlArgs(&$args) {
		if ($this->mode == 'exact_number') {
			parent::getBaseUrlArgs($args);
		} elseif ($this->mode == 'min_max_exact_number' || $this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			$field_index = 'field_' . $this->content_field->slug . '_min';
			if (isset($_REQUEST[$field_index]) && is_numeric($_REQUEST[$field_index])) {
				$args[$field_index] = $_REQUEST[$field_index];
			}
				
			$field_index = 'field_' . $this->content_field->slug . '_max';
			if (isset($_REQUEST[$field_index]) && is_numeric($_REQUEST[$field_index])) {
				$args[$field_index] = $_REQUEST[$field_index];
			}
		}
	}
	
	public function getVCParams() {
		if ($this->mode == 'exact_number') {
			return array(
					array(
							'type' => 'textfield',
							'param_name' => 'field_' . $this->content_field->slug,
							'heading' => $this->content_field->name,
					),
			);
		} elseif ($this->mode == 'min_max_exact_number' || $this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			return array(
					array(
							'type' => 'textfield',
							'param_name' => 'field_' . $this->content_field->slug . '_min',
							'heading' => $this->content_field->name . ' ' . esc_html__('Min', 'W2MB'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'field_' . $this->content_field->slug . '_max',
							'heading' => $this->content_field->name . ' ' . esc_html__('Max', 'W2MB'),
					),
			);
		}
	}
	
	public function getMapManagerParams() {
		if ($this->mode == 'exact_number') {
			return array(
					array(
							'type' => 'textbox',
							'name' => 'field_' . $this->content_field->slug,
							'label' => $this->content_field->name,
					)
			);
		} elseif ($this->mode == 'min_max_exact_number' || $this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			return array(
					array(
							'type' => 'textbox',
							'name' => 'field_' . $this->content_field->slug . '_min',
							'label' => $this->content_field->name . ' ' . esc_html__('Min', 'W2MB'),
					),
					array(
							'type' => 'textbox',
							'name' => 'field_' . $this->content_field->slug . '_max',
							'label' => $this->content_field->name . ' ' . esc_html__('Max', 'W2MB'),
					)
			);
		}
	}
	
	public function resetValue() {
		$this->min_max_value = array('min' => '', 'max' => '');
	}
}
?>