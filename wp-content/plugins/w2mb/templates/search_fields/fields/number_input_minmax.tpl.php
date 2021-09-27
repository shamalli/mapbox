<?php if (count($search_field->min_max_options)): ?>
<?php if ($search_field->content_field->is_integer) $decimals = 0; else $decimals = 2; ?>
<?php if ($columns == 1) $col_md = 12; else $col_md = 6; ?>
<div class="w2mb-row w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->type); ?> w2mb-field-search-block-<?php echo esc_attr($search_form_id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?>_<?php echo esc_attr($search_form_id); ?>">
	<div class="w2mb-col-md-12">
		<label><?php echo $search_field->content_field->name; ?></label>
	</div>

	<div class="w2mb-col-md-<?php echo esc_attr($col_md); ?> w2mb-form-group">
		<select name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_min" class="w2mb-form-control">
		<option value=""><?php esc_html_e('- Select min -', 'W2MB'); ?></option>
		<?php foreach ($search_field->min_max_options AS $item): ?>
			<?php if (is_numeric($item)): ?>
			<option value="<?php echo esc_attr($item); ?>" <?php selected($search_field->min_max_value['min'], $item); ?>><?php echo number_format($item, $decimals, $search_field->content_field->decimal_separator, $search_field->content_field->thousands_separator); ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>
	</div>

	<div class="w2mb-col-md-<?php echo esc_attr($col_md); ?> w2mb-form-group">
		<select name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_max" class="w2mb-form-control">
		<option value=""><?php esc_html_e('- Select max -', 'W2MB'); ?></option>
		<?php foreach ($search_field->min_max_options AS $item): ?>
			<?php if (is_numeric($item)): ?>
			<option value="<?php echo esc_attr($item); ?>" <?php selected($search_field->min_max_value['max'], $item); ?>><?php echo number_format($item, $decimals, $search_field->content_field->decimal_separator, $search_field->content_field->thousands_separator); ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>
	</div>
</div>
<?php endif; ?>