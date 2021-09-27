<?php 

class w2mb_content_field_radio extends w2mb_content_field_select {
	protected $can_be_searched = true;
	protected $is_search_configuration_page = true;

	public function renderInput() {
		if (!($template = w2mb_isTemplate('content_fields/fields/radio_input_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/radio_input.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_input_template', $template, $this);
			
		w2mb_renderTemplate($template, array('content_field' => $this));
	}
}
?>