<div class="w2mb-row w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->type); ?> w2mb-field-search-block-<?php echo esc_attr($search_form_id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?>_<?php echo esc_attr($search_form_id); ?>">
	<div class="w2mb-col-md-12">
		<label><?php echo $search_field->content_field->name; ?> <?php echo $search_field->content_field->currency_symbol; ?></label>
	</div>
	<div class="w2mb-col-md-12 w2mb-form-group">
		<input type="text" name="field_<?php echo esc_attr($search_field->content_field->slug); ?>" class="w2mb-form-control" value="<?php echo esc_attr($search_field->value); ?>" />
	</div>
</div>