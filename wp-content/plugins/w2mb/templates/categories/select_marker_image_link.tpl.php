<div>
	<img class="w2mb-marker-image-png-tag w2mb-field-icon <?php if (!$image_png_name): ?>w2mb-display-none<?php endif; ?>" src="<?php if ($image_png_name) echo esc_url(W2MB_MAP_ICONS_URL . 'icons/' . $image_png_name); ?>" />
	<input type="hidden" name="marker_png_image" class="marker_png_image" value="<?php if ($image_png_name) echo esc_attr($image_png_name); ?>">
	<input type="hidden" name="category_id" class="category_id" value="<?php echo esc_attr($term_id); ?>">
	<a class="select_marker_png_image" href="javascript: void(0);"><?php esc_html_e('Select image', 'W2MB'); ?></a>
</div>