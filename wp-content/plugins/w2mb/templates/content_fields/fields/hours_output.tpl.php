<?php if (array_filter($content_field->value)): ?>
<div class="w2mb-field w2mb-field-output-block w2mb-field-output-block-<?php echo esc_attr($content_field->type); ?> w2mb-field-output-block-<?php echo esc_attr($content_field->id); ?>">
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
	<?php if ($strings = $content_field->processStrings()): ?>
	<div class="w2mb-field-content w2mb-hours-field">
		<?php foreach ($strings AS $string): ?>
		<div><?php echo $string; ?></div>
		<?php endforeach; ?>
		<div class="w2mb-clearfix"></div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>