<?php 

class w2mb_content_field_categories extends w2mb_content_field {
	protected $can_be_required = true;
	protected $can_be_ordered = false;
	protected $is_categories = false;
	protected $is_slug = false;
	
	public function isNotEmpty($listing) {
		if (has_term('', W2MB_CATEGORIES_TAX, $listing->post->ID))
			return true;
		else
			return false;
	}

	public function renderOutput($listing, $group = null) {
		if (!($template = w2mb_isTemplate('content_fields/fields/categories_output_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/categories_output.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
			
		w2mb_renderTemplate($template, array('content_field' => $this, 'listing' => $listing, 'group' => $group));
	}
	
	public function renderOutputForMap($location, $listing) {
		return w2mb_renderTemplate('content_fields/fields/categories_output_map.tpl.php', array('content_field' => $this, 'listing' => $listing), true);
	}
}
?>