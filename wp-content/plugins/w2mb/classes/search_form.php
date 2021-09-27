<?php

class w2mb_search_form {
	public $uid;
	public $controller;
	public $args = array();
	public $search_fields_array = array();
	public $search_fields_array_advanced = array();
	public $search_fields_array_all = array();
	public $is_advanced_search_panel = false;
	public $search_form_id;
	public $advanced_open = false;
	
	public function __construct($uid = null, $controller = 'maps_controller', $args = array()) {
		global $w2mb_instance;
		
		// random ID needed because there may be more than 1 search form on one page
		$this->search_form_id = w2mb_generateRandomVal();
		
		$w2mb_instance->search_fields->load_search_fields();

		$this->uid = $uid;
		$this->controller = $controller;
		
		$this->args = array_merge(array(
				'redirect' =>'',
				'columns' => 2,
				'show_categories_search' => 1,
				'categories_search_level' => 1,
				'category' => 0,
				'exact_categories' => array(),
				'show_keywords_search' => 1,
				'keywords_ajax_search' => 1,
				'keywords_search_examples' => '',
				'what_search' => '',
				'show_radius_search' => 1,
				'radius' => 0,
				'show_locations_search' => 1,
				'locations_search_level' => 1,
				'show_address_search' => 1,
				'address' => '',
				'location' => 0,
				'exact_locations' => array(),
				'search_fields' => '',
				'search_fields_advanced' => '',
				'search_bg_color' => '',
				'search_bg_opacity' => 100,
				'search_text_color' => '',
				'hide_search_button' => 0,
				'on_row_search_button' => 0,
				'sticky_scroll' => 0,
				'sticky_scroll_toppadding' => 0,
				'scroll_to' => '',
				'include_get_params' => 1,
		), $args);
		
		if (isset($this->args['exact_categories']) && !is_array($this->args['exact_categories'])) {
			if ($categories = array_filter(explode(',', $this->args['exact_categories']), 'trim')) {
				$this->args['exact_categories'] = $categories;
			}
		}

		if (isset($this->args['exact_locations']) && !is_array($this->args['exact_locations'])) {
			if ($locations = array_filter(explode(',', $this->args['exact_locations']), 'trim')) {
				$this->args['exact_locations'] = $locations;
			}
		}

		if ((isset($this->args['search_fields']) && $this->args['search_fields'] && $this->args['search_fields'] != -1) || (isset($this->args['search_fields_advanced']) && $this->args['search_fields_advanced'] && $this->args['search_fields_advanced'] != -1)) {
			$search_fields_ids = explode(',', $this->args['search_fields']);
			$search_fields_ids_advanced = explode(',', $this->args['search_fields_advanced']);
			$search_fields_ids_all = array_filter(array_merge($search_fields_ids, $search_fields_ids_advanced));
			
			foreach ($search_fields_ids_all AS $id) {
				if ($search_field = $w2mb_instance->search_fields->getSearchFieldById($id)) {
					if (in_array($id, $search_fields_ids))
						$this->search_fields_array[$id] = $search_field;
					elseif (in_array($id, $search_fields_ids_advanced))
						$this->search_fields_array_advanced[$id] = $search_field;
				}
			}
		} else {
			foreach ($w2mb_instance->search_fields->search_fields_array AS $id=>$search_field)
				if ($search_field->content_field->advanced_search_form && (!isset($this->args['search_fields_advanced']) || $this->args['search_fields_advanced'] != -1)) {
					$this->search_fields_array_advanced[$id] = $search_field;
				} elseif (!isset($this->args['search_fields']) || $this->args['search_fields'] != -1) {
					$this->search_fields_array[$id] = $search_field;
				}
		}

		$search_fields_array_all = $this->search_fields_array + $this->search_fields_array_advanced;
		
		// safely copy all fields into $this->search_fields_array_all, this array needs to manage hidden fields_in_categories[] = []
		foreach ($search_fields_array_all AS $key=>$search_field) {
			$this->search_fields_array_all[$key] = clone $search_field;
			$this->search_fields_array_all[$key]->resetValue();
		}
		
		// load search values in constructor before search form starts render
		foreach ($this->search_fields_array AS $key=>$search_field) {
			$this->search_fields_array[$key]->loadValue($this->args, $this->args['include_get_params']);
		}
		foreach ($this->search_fields_array_advanced AS $key=>$search_field) {
			$this->search_fields_array_advanced[$key]->loadValue($this->args, $this->args['include_get_params']);
		}
		
		if ($this->search_fields_array_advanced) {
			$this->is_advanced_search_panel = true;
		}

		if ((isset($_REQUEST['use_advanced']) && ($_REQUEST['use_advanced'] == 1)) || !empty($this->args['advanced_open'])) {
			$this->advanced_open = true;
		}
	}
	
	public function outputHiddenFields() {
		global $w2mb_instance, $wp_rewrite;

		$hidden_fields = array();
		
		$hidden_fields['w2mb_action'] = 'search';

		if ($this->uid) {
			$hidden_fields['hash'] = $this->uid;
		}
		if ($this->controller) {
			$hidden_fields['controller'] = $this->controller;
		}
		
		if (!empty($this->args['include_categories_children'])) {
			$hidden_fields['include_categories_children'] = 1;
		}

		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress)
			if ($sitepress->get_option('language_negotiation_type') == 3)
				$hidden_fields['lang'] =  $sitepress->get_current_language();

		if (empty($this->args['show_categories_search']) && !empty($this->args['categories'])) {
			$hidden_fields['categories'] = $this->args['categories'];
		}
		if (empty($this->args['show_keywords_search']) && !empty($this->args['what_search'])) {
			$hidden_fields['what_search'] = $this->args['what_search'];
		}
		if (empty($this->args['show_locations_search']) && !empty($this->args['locations'])) {
			$hidden_fields['location_id'] = $this->args['locations'];
		}
		if (empty($this->args['show_address_search']) && !empty($this->args['address'])) {
			$hidden_fields['address'] = $this->args['address'];
		}
		if (empty($this->args['show_radius_search']) && !empty($this->args['radius'])) {
			$hidden_fields['radius'] = $this->args['radius'];
		}
		if (!empty($this->args['exact_categories'])) {
			$hidden_fields['exact_categories'] = implode(",", $this->args['exact_categories']);
		}
		if (!empty($this->args['exact_locations'])) {
			$hidden_fields['exact_locations'] = implode(",", $this->args['exact_locations']);
		}

		// output search params of fields, those are not on the search form
		foreach ($this->args AS $arg_name=>$arg_value) {
			if (strpos($arg_name, 'field_') === 0) {
				$is_visible_content_field = false;
				foreach ($this->search_fields_array_all AS $search_field) {
					if ($search_field->isParamOfThisField($arg_name)) {
						$is_visible_content_field = true;
						break;
					}
				}

				if (!$is_visible_content_field)
					$hidden_fields[$arg_name] = $arg_value;
			}
		}
		
		foreach ($hidden_fields AS $name=>$value) {
			if (is_array($value)) {
				foreach ($value AS $val) {
					echo '<input type="hidden" name="' . esc_attr($name) . '[]" value="' . esc_attr($val) . '" />';
				}
			} else {
				echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" />';
			}
		}
	}
	
	public function isDefaultSearchFields() {
		if (
			((!empty($this->args['show_categories_search']) && w2mb_is_anyone_in_taxonomy(W2MB_CATEGORIES_TAX)) || !empty($this->args['show_keywords_search'])) ||
			((!empty($this->args['show_locations_search']) && w2mb_is_anyone_in_taxonomy(W2MB_LOCATIONS_TAX)) || !empty($this->args['show_address_search']))
		) {
			return true;
		}
	}

	public function isCategories() {
		if (!empty($this->args['show_categories_search']) && w2mb_is_anyone_in_taxonomy(W2MB_CATEGORIES_TAX)) {
			return true;
		}
	}

	public function isKeywords() {
		if (!empty($this->args['show_keywords_search'])) {
			return true;
		}
	}

	public function isKeywordsAJAX() {
		if (!empty($this->args['keywords_ajax_search'])) {
			return true;
		}
	}

	public function isCategoriesOrKeywords() {
		if ($this->isCategories() || $this->isKeywords()) {
			return true;
		}
	}
	
	public function isLocations() {
		if (!empty($this->args['show_locations_search']) && w2mb_is_anyone_in_taxonomy(W2MB_LOCATIONS_TAX)) {
			return true;
		}
	}

	public function isAddress() {
		if (!empty($this->args['show_address_search'])) {
			return true;
		}
	}

	public function isLocationsOrAddress() {
		if ($this->isLocations() || $this->isAddress()) {
			return true;
		}
	}

	public function isRadius() {
		if (!empty($this->args['show_radius_search'])) {
			return true;
		}
	}

	public function getKeywordValue() {
		return stripslashes(w2mb_getValue($_GET, 'what_search', w2mb_getValue($this->args, 'keywords_placeholder')));
	}

	public function isKeywordsExamples() {
		if (!empty($this->args['keywords_search_examples'])) {
			return true;
		}
	}
	
	public function wrapKeywordsExamples($example) {
		$example = trim($example);
		return "<a href=\"javascript:void(0);\">{$example}</a>";
	}

	public function getKeywordsExamples() {
		$examples = explode(',', $this->args['keywords_search_examples']);
		$wrapped = array_map(
				array($this, "wrapKeywordsExamples"),
				$examples
		);
		return implode(', ', $wrapped);
	}

	public function getAddressValue() {
		return stripslashes(w2mb_getValue($_GET, 'address', w2mb_getValue($this->args, 'address_placeholder')));
	}

	public function getRadiusValue() {
		if (!($radius = w2mb_getValue($_GET, 'radius', w2mb_getValue($this->args, 'radius')))) {
			$radius = 0;
		} else {
			$radius = w2mb_getValue($_GET, 'radius', w2mb_getValue($this->args, 'radius'));
		}
		return $radius;
	}
	
	public function getCategoriesDropdownsMenuParams($placeholder_category, $placeholder_category_keywords) {
		$term_id = w2mb_getSearchTermID('category-w2mb', 'categories', w2mb_getValue($this->args, 'category'));
			
		$params = array(
				'tax' => W2MB_CATEGORIES_TAX,
				'field_name' => 'categories',
				'depth' => $this->args['categories_search_level'],
				'term_id' => $term_id,
				'count' => get_option('w2mb_show_category_count_in_search'),
				'uID' => null,
				'exact_terms' => $this->args['exact_categories'],
				'hide_empty' => get_option('w2mb_hide_empty_categories'),
				'placeholder' => $placeholder_category,
		);
		if ($this->isKeywords()) {
			$params['placeholder'] = $placeholder_category_keywords;
			$params['autocomplete_field'] = 'what_search';
			$params['autocomplete_field_value'] = $this->getKeywordValue();
			$params['autocomplete_ajax'] = $this->isKeywordsAJAX();
		}
		
		return $params;
	}

	public function getLocationsDropdownsMenuParams($placeholder_location, $placeholder_locations_address) {
		$term_id = w2mb_getSearchTermID('location-w2mb', 'location_id', w2mb_getValue($this->args, 'location'));

		$params = array(
				'tax' => W2MB_LOCATIONS_TAX,
				'field_name' => 'location_id',
				'depth' => $this->args['locations_search_level'],
				'term_id' => $term_id,
				'count' => get_option('w2mb_show_location_count_in_search'),
				'uID' => null,
				'exact_terms' => $this->args['exact_locations'],
				'hide_empty' => get_option('w2mb_hide_empty_locations'),
				'placeholder' => $placeholder_location,
		);
		if ($this->isAddress()) {
			$params['placeholder'] = $placeholder_locations_address;
			$params['autocomplete_field'] = 'address';
			$params['autocomplete_field_value'] = $this->getAddressValue();
		}

		return $params;
	}

	public function getColMd() {
		if (
			(empty($this->args['columns']) || $this->args['columns'] == 2) &&
			(($this->args['show_categories_search'] && w2mb_is_anyone_in_taxonomy(W2MB_CATEGORIES_TAX)) || $this->args['show_keywords_search']) &&
			(($this->args['show_locations_search'] && w2mb_is_anyone_in_taxonomy(W2MB_LOCATIONS_TAX)) || $this->args['show_address_search'])
		) {
			$col_md = 6;

			if ($this->args['on_row_search_button'] && !$this->args['hide_search_button']) {
				$col_md = $col_md - 1;
			}
		} else {
			$col_md = 12;
			
			if ($this->args['on_row_search_button'] && !$this->args['hide_search_button']) {
				$col_md = $col_md - 2;
			}
		}
		
		return $col_md;
	}
	
	public function getSearchFormStyles() {
		if ($this->args['search_bg_color'] || $this->args['search_bg_opacity'] || $this->args['search_text_color'] || $this->args['search_overlay']) {
			$search_form_id = "#w2mb-search-form-" . $this->search_form_id;
			echo "<style type=\"text/css\">";
			if ($this->args['search_bg_color']) {
				echo "
				$search_form_id {
					background: " . w2mb_hex2rgba($this->args['search_bg_color'], (int)$this->args['search_bg_opacity']/100) . ";
				}";
			}
			if ($this->args['search_text_color']) {
				echo "
				$search_form_id,
				$search_form_id a,
				$search_form_id a:hover,
				$search_form_id a:visited,
				$search_form_id a:focus,
				$search_form_id a.w2mb-advanced-search-label,
				$search_form_id a.w2mb-advanced-search-label:hover,
				$search_form_id a.w2mb-advanced-search-label:visited,
				$search_form_id a.w2mb-advanced-search-label:focus {
					color: " . $this->args['search_text_color'] . ";
				}";
			}
			if (!$this->args['search_overlay']) {
				echo "
				$search_form_id .w2mb-search-overlay {
					background: none;
				}";
			}
			echo "</style>";
		}
	}
	
	public function displaySearchButton($on_row_search_button = false) {
		if ($on_row_search_button) {
			$classes = "w2mb-on-row-button";
		} else {
			$classes = "w2mb-col-md-6 w2mb-pull-right w2mb-text-right";
		}
		echo '<div class="w2mb-search-form-button ' . esc_attr($classes) . '">
				<button type="submit" name="submit" class="w2mb-btn w2mb-btn-primary ' . (($this->args['hide_search_button']) ? 'w2mb-submit-button-hidden' : '') . '">' . esc_html__('Search', 'W2MB') . '</button>
			</div>';
	}
	
	public function getFormAttributes() {
		$attributes = array();
	
		if ($this->args['redirect']) {
			$search_url = $this->args['redirect'];
				
			$attributes['action'] = $search_url;
		}
	
		$attributes['class'] = "w2mb-content w2mb-search-form w2mb-search-form-submit" . ($this->args['sticky_scroll'] ? " w2mb-sticky-scroll" : "");
	
		$attributes['id'] = "w2mb-search-form-" . $this->search_form_id;
		$attributes['data-id'] = $this->search_form_id;
	
		if ($this->args['sticky_scroll_toppadding']) {
			$attributes['data-toppadding'] = $this->args['sticky_scroll_toppadding'];
		}
		if ($this->args['scroll_to']) {
			$attributes['data-w2mb-scroll-to'] = $this->args['scroll_to'];
		}
	
		$attrs_str = '';
		foreach ($attributes AS $name=>$value) {
			$attrs_str .= $name . ' = "' . esc_attr($value) . '" ';
		}
	
		return $attrs_str;
	}

	public function display() {
		w2mb_renderTemplate('search_form.tpl.php',
			array(
				'search_form_id' => esc_attr($this->search_form_id),
				'is_advanced_search_panel' => esc_attr($this->is_advanced_search_panel),
				'advanced_open' => esc_attr($this->advanced_open),
				'args' => $this->args,
				'search_form' => $this
			)
		);
	}
}
?>