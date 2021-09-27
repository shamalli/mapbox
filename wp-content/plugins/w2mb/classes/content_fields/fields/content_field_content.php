<?php 

class w2mb_content_field_content extends w2mb_content_field {
	protected $can_be_ordered = false;
	protected $is_categories = false;
	protected $is_slug = false;
	
	public function isNotEmpty($listing) {
		if (post_type_supports(W2MB_POST_TYPE, 'editor') && !empty($listing->post->post_content))
			return true;
		else
			return false;
	}

	public function validateValues(&$errors, $data) {
		$listing = w2mb_getCurrentListingInAdmin();
		if (post_type_supports(W2MB_POST_TYPE, 'editor') && $this->is_required && (!isset($data['post_content']) || !$data['post_content']))
			$errors[] = esc_html__('Listing content is required', 'W2MB');
		else
			return $listing->post->post_content;
	}
	
	public function renderOutput($listing, $group = null) {
		if (!($template = w2mb_isTemplate('content_fields/fields/content_output_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/content_output.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
			
		w2mb_renderTemplate($template, array('content_field' => $this, 'listing' => $listing, 'group' => $group));
	}
	
	public function renderOutputForMap($location, $listing) {
		return wpautop($listing->post->post_content);
	}
}
?>