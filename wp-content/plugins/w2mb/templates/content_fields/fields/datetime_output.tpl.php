<?php if ($formatted_date_start || $formatted_date_end): ?>
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
	<span class="w2mb-field-content">
		<?php
		if ($formatted_date_start) {
			echo $formatted_date_start;
		}
		if ($formatted_date_start && $formatted_date_end && $formatted_date_start != $formatted_date_end) {
			echo ' - ';
		}
		if ($formatted_date_end && $formatted_date_start != $formatted_date_end) {
			echo $formatted_date_end;
		}
		if($content_field->is_time) {
			echo ' ' . $content_field->value['hour'] . ':' . $content_field->value['minute'];
		}
		?>
	</span>
</div>
<?php endif; ?>