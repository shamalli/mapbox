<?php 

class w2mb_search_controller extends w2mb_frontend_controller {

	public function init($args = array()) {
		global $w2mb_instance;

		parent::init($args);

		$this->args = array_merge(array(
				'redirect' =>'',
				'columns' => 2,
				'advanced_open' => false,
				'uid' => null,
				'show_categories_search' => 1,
				'categories_search_level' => 2,
				'category' => 0,
				'exact_categories' => array(),
				'show_keywords_search' => 1,
				'keywords_ajax_search' => 1,
				'keywords_search_examples' => '',
				'what_search' => '',
				'keywords_placeholder' => '',
				'show_radius_search' => 1,
				'radius' => 0,
				'show_locations_search' => 1,
				'locations_search_level' => 2,
				'show_address_search' => 1,
				'address' => '',
				'address_placeholder' => '',
				'location' => 0,
				'exact_locations' => array(),
				'search_fields' => '',
				'search_fields_advanced' => '',
				'search_bg_color' => '',
				'search_bg_opacity' => 100,
				'search_text_color' => '',
				'search_overlay' => 1,
				'hide_search_button' => 0,
				'on_row_search_button' => 0,
				'sticky_scroll' => 0,
				'sticky_scroll_toppadding' => 0,
				'scroll_to' => 'listings', // '', 'listings', 'map'
		), $args);

		$hash = false;
		if ($this->args['uid']) {
			$hash = md5($this->args['uid']);
		}

		$this->search_form = new w2mb_search_form($hash, 'maps_controller', $this->args);
		
		apply_filters('w2mb_search_controller_construct', $this);
	}

	public function display() {
		ob_start();
		$this->search_form->display($this->args['columns'], $this->args['advanced_open']);
		$output = ob_get_clean();

		return $output;
	}
}

?>