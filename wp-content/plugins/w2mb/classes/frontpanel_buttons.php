<?php

class w2mb_frontpanel_buttons {
	public $args = array();
	public $hide_button_text = false;
	public $buttons = false;
	public $listing = false;
	
	public function __construct($args = array()) {
		global $w2mb_instance;
		
		$this->args = array_merge(array(
				'hide_button_text' => false,
				'buttons' => 'submit,claim,edit', // also 'logout' possible
				'listing_id' => 0,
		), $args);
		
		if ($this->args['buttons']) {
			if (is_array($this->args['buttons'])) {
				$this->buttons = $this->args['buttons'];
			} else {
				$this->buttons = array_filter(explode(',', $this->args['buttons']), 'trim');
			}
		}
		
		$this->listing = new w2mb_listing();
		$this->listing->loadListingFromPost($this->args['listing_id']);

		$this->hide_button_text = apply_filters('w2mb_frontpanel_buttons_hide_text', $this->args['hide_button_text'], $this);
	}

	public function isButton($button) {
		return (in_array($button, $this->buttons));
	}

	public function isEditButton() {
		return ($this->isListing() && $this->isButton('edit') && w2mb_show_edit_button($this->getListingId()));
	}

	public function isPrintButton() {
		return ($this->isListing() && $this->isButton('print') && get_option('w2mb_print_button'));
	}

	public function isPdfButton() {
		return ($this->isListing() && $this->isButton('pdf') && get_option('w2mb_pdf_button'));
	}

	public function isListing() {
		return (bool)($this->listing);
	}

	public function getListingId() {
		if ($this->listing) {
			return $this->listing->post->ID;
		}
	}
	
	public function tooltipMeta($text, $return = false) {
		if ($this->hide_button_text) {
			$out = 'data-toggle="w2mb-tooltip" data-placement="top" data-original-title="' . esc_attr($text) . '"';;
			if ($return) {
				return $out;
			} else {
				echo $out;
			}
		}
	}
	
	public function display($return = false) {
		return w2mb_renderTemplate('frontend/frontpanel_buttons.tpl.php', array('frontpanel_buttons' => $this), $return);
	}
}
?>