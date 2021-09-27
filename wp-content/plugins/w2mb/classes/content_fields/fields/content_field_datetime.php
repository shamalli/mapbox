<?php 

class w2mb_content_field_datetime extends w2mb_content_field {
	public $is_time = true;
	
	protected $is_configuration_page = true;
	protected $can_be_searched = true;
	
	public function __construct() {
		// adapted for WPML
		//add_filter('wpml_config_array', array($this, 'wpml_config_array'));
	}
	
	public function isNotEmpty($listing) {
		if ((isset($this->value['date_start']) && $this->value['date_start']) || (isset($this->value['date_end']) && $this->value['date_end'])) {
			return true;
		} if ($this->is_time) {
			if ((isset($this->value['hour']) && $this->value['hour'] != '00') || (isset($this->value['minute']) && $this->value['minute'] != '00')) {
				return true;
			}
		}

		return false;
	}

	public function configure() {
		global $wpdb, $w2mb_instance;

		if (w2mb_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2mb_configure_content_fields_nonce'], W2MB_PATH)) {
			$validation = new w2mb_form_validation();
			$validation->set_rules('is_time', esc_html__('Enable time in field', 'W2MB'), 'is_checked');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2mb_content_fields, array('options' => serialize(array('is_time' => $result['is_time']))), array('id' => $this->id), null, array('%d')))
					w2mb_addMessage(esc_html__('Field configuration was updated successfully!', 'W2MB'));
				
				$w2mb_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->is_time = $validation->result_array('is_time');
				w2mb_addMessage($validation->error_array(), 'error');

				w2mb_renderTemplate('content_fields/fields/datetime_configuration.tpl.php', array('content_field' => $this));
			}
		} else
			w2mb_renderTemplate('content_fields/fields/datetime_configuration.tpl.php', array('content_field' => $this));
	}
	
	public function buildOptions() {
		if (isset($this->options['is_time']))
			$this->is_time = $this->options['is_time'];
	}
	
	public function delete() {
		global $wpdb;
	
		$wpdb->delete($wpdb->postmeta, array('meta_key' => '_content_field_' . $this->id . '_date'));
		$wpdb->delete($wpdb->postmeta, array('meta_key' => '_content_field_' . $this->id . '_date_start'));
		$wpdb->delete($wpdb->postmeta, array('meta_key' => '_content_field_' . $this->id . '_date_end'));
		$wpdb->delete($wpdb->postmeta, array('meta_key' => '_content_field_' . $this->id . '_hour'));
		$wpdb->delete($wpdb->postmeta, array('meta_key' => '_content_field_' . $this->id . '_minute'));
	
		$wpdb->delete($wpdb->w2mb_content_fields, array('id' => $this->id));
		return true;
	}
	
	public function renderInput() {
		wp_enqueue_script('jquery-ui-datepicker');

		if ($i18n_file = w2mb_getDatePickerLangFile(get_locale())) {
			wp_register_script('datepicker-i18n', $i18n_file, array('jquery-ui-datepicker'));
			wp_enqueue_script('datepicker-i18n');
		}

		if (!($template = w2mb_isTemplate('content_fields/fields/datetime_input_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/datetime_input.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_input_template', $template, $this);
			
		w2mb_renderTemplate($template, array('content_field' => $this, 'dateformat' => w2mb_getDatePickerFormat()));
	}
	
	public function validateValues(&$errors, $data) {
		$field_index_date_start = 'w2mb-field-input-' . $this->id . '-start';
		$field_index_date_end = 'w2mb-field-input-' . $this->id . '-end';
		$field_index_hour = 'w2mb-field-input-hour_' . $this->id;
		$field_index_minute = 'w2mb-field-input-minute_' . $this->id;

		$validation = new w2mb_form_validation();
		$rules = 'valid_date';
		if ($this->canBeRequired() && $this->is_required)
			$rules .= 'required|is_natural_no_zero';
		$validation->set_rules($field_index_date_start, $this->name, $rules);
		$validation->set_rules($field_index_date_end, $this->name, $rules);
		$validation->set_rules($field_index_hour, $this->name);
		$validation->set_rules($field_index_minute, $this->name);
		if (!$validation->run()) {
			$errors[] = $validation->error_array();
		}

		return array(
				'date_start' => $validation->result_array($field_index_date_start),
				'date_end' => $validation->result_array($field_index_date_end),
				'hour' => $validation->result_array($field_index_hour),
				'minute' => $validation->result_array($field_index_minute)
		);
	}
	
	public function saveValue($post_id, $validation_results) {
		if ($validation_results && is_array($validation_results)) {
			update_post_meta($post_id, '_content_field_' . $this->id . '_date_start', $validation_results['date_start']);
			update_post_meta($post_id, '_content_field_' . $this->id . '_date_end', $validation_results['date_end']);
			update_post_meta($post_id, '_content_field_' . $this->id . '_hour', $validation_results['hour']);
			update_post_meta($post_id, '_content_field_' . $this->id . '_minute', $validation_results['minute']);
			return true;
		}
	}
	
	public function loadValue($post_id) {
		$this->value = array(
			'date_start' => 0,
			'date_end' => 0,
			'hour' => 0,
			'minute' => 0,
		);
		$date_start = 0;
		$date_end = 0;
		if (get_post_meta($post_id, '_content_field_' . $this->id . '_date_start', true)) {
			$date_start = get_post_meta($post_id, '_content_field_' . $this->id . '_date_start', true);
		}
		if (get_post_meta($post_id, '_content_field_' . $this->id . '_date_end', true)) {
			$date_end = get_post_meta($post_id, '_content_field_' . $this->id . '_date_end', true);
		}
		$hour = (get_post_meta($post_id, '_content_field_' . $this->id . '_hour', true) ? get_post_meta($post_id, '_content_field_' . $this->id . '_hour', true) : '00');
		$minute = (get_post_meta($post_id, '_content_field_' . $this->id . '_minute', true) ? get_post_meta($post_id, '_content_field_' . $this->id . '_minute', true) : '00');
		
		$validation = new w2mb_form_validation();
		if ($validation->valid_date($date_start) && $validation->valid_date($date_end)) {
			$this->value = array(
				'date_start' => $date_start,
				'date_end' => $date_end,
				'hour' => $hour,
				'minute' => $minute,
			);
			
			$this->value = apply_filters('w2mb_content_field_load', $this->value, $this, $post_id);
		}

		return $this->value;
	}
	
	public function renderOutput($listing, $group = null) {
		if ($this->value['date_start'] || $this->value['date_end']) {
			$formatted_date_start = ($this->value['date_start']) ? mysql2date(get_option('date_format'), date('Y-m-d H:i:s', $this->value['date_start'])) : false;
			$formatted_date_end = ($this->value['date_end']) ? mysql2date(get_option('date_format'), date('Y-m-d H:i:s', $this->value['date_end'])) : false;

			if (!($template = w2mb_isTemplate('content_fields/fields/datetime_output_'.$this->id.'.tpl.php'))) {
				$template = 'content_fields/fields/datetime_output.tpl.php';
			}
			
			$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
				
			w2mb_renderTemplate($template, array('content_field' => $this, 'formatted_date_start' => $formatted_date_start, 'formatted_date_end' => $formatted_date_end, 'listing' => $listing, 'group' => $group));
		}
	}
	
	public function orderParams() {
		$order_params = array('orderby' => 'meta_value_num', 'meta_key' => '_content_field_' . $this->id . '_date_start');
		if (get_option('w2mb_orderby_exclude_null'))
			$order_params['meta_query'] = array(
				array(
					'key' => '_content_field_' . $this->id . '_date_start',
					'value'   => array(''),
					'compare' => 'NOT IN'
				)
			);
		return $order_params;
	}
	
	/*
	 * Possible formats:
	 * dd.mm.yyyy
	 * dd.mm.yyyy HH:MM
	 * dd.mm.yyyy HH:MM - dd.mm.yyyy HH:MM
	 * dd.mm.yyyy - dd.mm.yyyy
	 * dd.mm.yyyy HH:MM - dd.mm.yyyy
	 */
	public function validateCsvValues($value, &$errors) {
		$output = array();
		
		$start_end = explode('-', $value);
		if (isset($start_end[0])) {
			$start_value = $start_end[0];
			if ($tmstmp = strtotime($start_value)) {
				$output['minute'] = date('i', $tmstmp);
				$output['hour'] = date('H', $tmstmp);
				$output['date_start'] = $tmstmp - 3600*$output['hour'] - 60*$output['minute'];
				
				if (isset($start_end[1])) {
					$end_value = $start_end[1];
					if ($tmstmp = strtotime($end_value)) {
						$output['date_end'] = $tmstmp - 3600*$output['hour'] - 60*$output['minute'];
					} else {
						$errors[] = esc_html__("End Date-Time field is invalid", "W2MB");
					}
				} else {
					$output['date_end'] = $output['date_start'];
				}
			} else {
				$errors[] = esc_html__("Start Date-Time field is invalid", "W2MB");
			}
		}

		return $output;
	}
	
	public function exportCSV() {
		if ($this->value['date_start'] && $this->value['date_end']) {
			return date('d.m.Y H:i', $this->value['date_start']) . ' - ' . date('d.m.Y H:i', $this->value['date_end']);
		}
	}
	
	public function renderOutputForMap($location, $listing) {
		if ($this->value['date_start'] || $this->value['date_end']) {
			$formatted_date_start = mysql2date(get_option('date_format'), date('Y-m-d H:i:s', $this->value['date_start']));
			$formatted_date_end = mysql2date(get_option('date_format'), date('Y-m-d H:i:s', $this->value['date_end']));
	
			return w2mb_renderTemplate('content_fields/fields/datetime_output_map.tpl.php', array('content_field' => $this, 'formatted_date_start' => $formatted_date_start, 'formatted_date_end' => $formatted_date_end, 'listing' => $listing), true);
		}
	}
	
	// adapted for WPML
	/* public function wpml_config_array($config_all) {
		$config_all['wpml-config']['custom-fields']['custom-field'][] = array(
				'value' => '_content_field_' . $this->id . '_date',
				'attr' => array('action' => 'copy')
		);
		$config_all['wpml-config']['custom-fields']['custom-field'][] = array(
				'value' => '_content_field_' . $this->id . '_hour',
				'attr' => array('action' => 'copy')
		);
		$config_all['wpml-config']['custom-fields']['custom-field'][] = array(
				'value' => '_content_field_' . $this->id . '_minute',
				'attr' => array('action' => 'copy')
		);

		return $config_all;
	} */
}
?>