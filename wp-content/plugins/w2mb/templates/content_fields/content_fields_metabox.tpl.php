<script>
		<?php
		foreach ($content_fields AS $content_field): 
			if (!$content_field->isCategories() || $content_field->categories === array()) { ?>
				w2mb_js_objects.fields_in_categories[<?php echo esc_js($content_field->id); ?>] = [];
			<?php } else { ?>
				w2mb_js_objects.fields_in_categories[<?php echo esc_js($content_field->id); ?>] = [<?php echo implode(',', $content_field->categories); ?>];
			<?php } ?>
		<?php endforeach; ?>
</script>

<div class="w2mb-content w2mb-content-fields-metabox">
	<div class="w2mb-form-horizontal">
		<p class="w2mb-description"><?php esc_html_e('Content fields may be dependent on selected categories', 'W2MB'); ?></p>
		<?php
		foreach ($content_fields AS $content_field) {
			if (
				!$content_field->is_core_field &&
				($content_field->filterForAdmins() || $post->post_author == get_current_user_id()) // this content field may be hidden from all users except admins and listing author
			) {
				$content_field->renderInput();
			}
		}
		?>
	</div>
</div>