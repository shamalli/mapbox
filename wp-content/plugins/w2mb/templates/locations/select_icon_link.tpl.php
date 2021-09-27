<div>
	<img class="icon_image_tag w2mb-field-icon <?php if (!$icon_file): ?>w2mb-display-none<?php endif; ?>" src="<?php if ($icon_file) echo W2MB_LOCATIONS_ICONS_URL . esc_attr($icon_file); ?>" />
	<input type="hidden" name="icon_image" class="icon_image" value="<?php if ($icon_file) echo esc_attr($icon_file); ?>">
	<input type="hidden" name="location_id" class="location_id" value="<?php echo esc_attr($term_id); ?>">
	<a class="select_icon_image" href="javascript: void(0);"><?php esc_html_e('Select icon', 'W2MB'); ?></a>
</div>