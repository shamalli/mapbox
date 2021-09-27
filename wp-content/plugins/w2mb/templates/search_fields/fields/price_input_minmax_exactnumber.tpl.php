<?php if ($columns == 1) $col_md = 12; else $col_md = 6; ?>
<div class="w2mb-row w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->type); ?> w2mb-field-search-block-<?php echo esc_attr($search_form_id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?>_<?php echo esc_attr($search_form_id); ?>">
	<div class="w2mb-col-md-12">
		<label><?php echo $search_field->content_field->name; ?> <?php echo $search_field->content_field->currency_symbol; ?></label>
	</div>

	<div class="w2mb-col-md-<?php echo esc_attr($col_md); ?> w2mb-form-group">
		<input name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_min" class="w2mb-form-control" value="<?php echo esc_attr($search_field->min_max_value['min']); ?>" placeholder="<?php printf(esc_attr__('Min %s', 'W2MB'), $search_field->content_field->name); ?>">
	</div>

	<div class="w2mb-col-md-<?php echo esc_attr($col_md); ?> w2mb-form-group">
		<input name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_max" class="w2mb-form-control" value="<?php echo esc_attr($search_field->min_max_value['max']); ?>" placeholder="<?php printf(esc_attr__('Max %s', 'W2MB'), $search_field->content_field->name); ?>">
	</div>
</div>