<?php if (has_term('', W2MB_CATEGORIES_TAX, $listing->post->ID)): ?>
<div class="w2mb-field-output-block w2mb-field-output-block-<?php echo esc_attr($content_field->type); ?> w2mb-field-output-block-<?php echo esc_attr($content_field->id); ?>">
	<?php if ($content_field->icon_image || !$content_field->is_hide_name): ?>
	<span class="w2mb-field-caption <?php w2mb_is_any_field_name_in_group($group); ?>">
		<?php if ($content_field->icon_image): ?>
		<span class="w2mb-field-icon w2mb-fa w2mb-fa-lg <?php echo esc_attr($content_field->icon_image); ?>"></span>
		<?php endif; ?>
		<?php if (!$content_field->is_hide_name): ?>
		<span class="w2mb-field-name"><?php echo $content_field->name; ?>:</span>
		<?php endif; ?>
	</span>
	<?php endif; ?>
	<span class="w2mb-field-content">
	<?php //echo get_the_term_list($listing->post->ID, W2MB_CATEGORIES_TAX, '', ', ', ''); ?>
		<?php
		$terms = get_the_terms($listing->post->ID, W2MB_CATEGORIES_TAX);
		foreach ($terms as $term):?>
			<span class="w2mb-label w2mb-label-primary w2mb-category-label"><?php echo $term->name; ?>&nbsp;&nbsp;<span class="w2mb-glyphicon w2mb-glyphicon-tag"></span></span>
		<?php endforeach; ?>
	</span>
</div>
<?php endif; ?>