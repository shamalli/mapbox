<?php 

class w2mb_content_field_excerpt extends w2mb_content_field {
	protected $can_be_ordered = false;
	protected $is_categories = false;
	protected $is_slug = false;
	
	public function isNotEmpty($listing) {
		if (post_type_supports(W2MB_POST_TYPE, 'excerpt') && ($listing->post->post_excerpt || (get_option('w2mb_cropped_content_as_excerpt') && $listing->post->post_content !== '')))
			return true;
		else
			return false;
	}

	public function validateValues(&$errors, $data) {
		$listing = w2mb_getCurrentListingInAdmin();
		if (post_type_supports(W2MB_POST_TYPE, 'excerpt') && $this->is_required && (!isset($data['post_excerpt']) || !$data['post_excerpt']))
			$errors[] = esc_html__('Listing excerpt is required', 'W2MB');
		else
			return $listing->post->post_excerpt;
	}
	
	public function renderOutput($listing, $group = null) {
		if (!($template = w2mb_isTemplate('content_fields/fields/excerpt_output_'.$this->id.'.tpl.php'))) {
			$template = 'content_fields/fields/excerpt_output.tpl.php';
		}
		
		$template = apply_filters('w2mb_content_field_output_template', $template, $this, $listing, $group);
			
		w2mb_renderTemplate($template, array('content_field' => $this, 'listing' => $listing, 'group' => $group));
	}
	
	public function renderOutputForMap($location, $listing) {
		if (get_option('w2mb_cropped_content_as_excerpt') && $listing->post->post_content !== '') {
			return w2mb_crop_content($listing->post->ID, get_option('w2mb_excerpt_length'), get_option('w2mb_strip_excerpt'));
		} elseif ($listing->post->post_excerpt) {
			return $listing->post->post_excerpt;
		}
	}
}
?>