<?php if ($content_field->value): ?>
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
	<ul class="w2mb-field-content w2mb-checkboxes-columns-<?php echo $content_field->columns_number; ?>">
	<?php if ($content_field->how_display_items == 'all'): ?>
	<?php foreach ($content_field->selection_items AS $key=>$item): ?>
		<li class="w2mb-field-checkbox-item-<?php echo (in_array($key, $content_field->value) ? "checked" : "unchecked"); ?>">
			<?php if ($content_field->icon_images[$key]): ?>
			<span class="w2mb-field-icon w2mb-fa w2mb-fa-lg w2mb-fa-fw <?php echo esc_attr($content_field->icon_images[$key]); ?>"></span>
			<?php endif; ?>
			<?php echo $item; ?>
		</li>
	<?php endforeach; ?>
	<?php elseif ($content_field->how_display_items == 'checked'): ?>
	<?php foreach ($content_field->value AS $key): ?>
		<?php if (isset($content_field->selection_items[$key])): ?>
		<li class="w2mb-field-checkbox-item-<?php echo (in_array($key, $content_field->value) ? "checked" : "unchecked"); ?>">
			<?php if ($content_field->icon_images[$key]): ?>
			<span class="w2mb-field-icon w2mb-fa w2mb-fa-lg <?php echo esc_attr($content_field->icon_images[$key]); ?>"></span>
			<?php endif; ?>
			<?php echo $content_field->selection_items[$key]; ?>
		</li>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
	</ul>
</div>
<?php endif; ?>