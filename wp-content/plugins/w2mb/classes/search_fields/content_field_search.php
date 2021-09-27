<?php 

class w2mb_content_field_search {
	public $value;
	public $value_loaded = false;

	public $content_field;
	
	public function assignContentField($content_field) {
		$this->content_field = $content_field;
	}
	
	public function convertSearchOptions() {
		if ($this->content_field->search_options) {
			if (is_string($this->content_field->search_options)) {
				$unserialized_options = unserialize($this->content_field->search_options);
			} elseif (is_array($this->content_field->search_options)) {
				$unserialized_options = $this->content_field->search_options;
			}
			if (count($unserialized_options) > 1 || $unserialized_options != array('')) {
				$this->content_field->search_options = $unserialized_options;
				if (method_exists($this, 'buildSearchOptions')) {
					$this->buildSearchOptions();
				}
				return $this->content_field->search_options;
			}
		}
		return array();
	}
	
	public function getBaseUrlArgs(&$args) {
		$field_index = 'field_' . $this->content_field->slug;
		
		if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index]) {
			$args[$field_index] = $_REQUEST[$field_index];
		}
	}
	
	public function getVCParams() {
		return array();
	}
	
	public function isParamOfThisField($param) {
		if ($param == 'field_' . $this->content_field->slug) {
			return true;
		}
	}
	
	public function loadValue($defaults = array(), $include_GET_params = true) {
		$field_index = 'field_' . $this->content_field->slug;
	
		if ($include_GET_params) {
				$this->value = ((w2mb_getValue($_REQUEST, $field_index, false) !== false) ? w2mb_getValue($_REQUEST, $field_index) : w2mb_getValue($defaults, $field_index));
		}
	}
	
	public function resetValue() {
		$this->value = null;
	}
}
?>