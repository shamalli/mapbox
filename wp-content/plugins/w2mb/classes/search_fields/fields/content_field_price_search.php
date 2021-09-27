<?php 

class w2mb_content_field_price_search extends w2mb_content_field_number_search {

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
			w2mb_renderTemplate('search_fields/fields/price_input_exactnumber.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		} elseif ($this->mode == 'min_max_exact_number') {
			w2mb_renderTemplate('search_fields/fields/price_input_minmax_exactnumber.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		} elseif ($this->mode == 'min_max') {
			w2mb_renderTemplate('search_fields/fields/price_input_minmax.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		} elseif ($this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			w2mb_renderTemplate('search_fields/fields/price_input_slider.tpl.php', array('search_field' => $this, 'columns' => $columns, 'search_form_id' => $search_form_id));
		}
	}
}
?>