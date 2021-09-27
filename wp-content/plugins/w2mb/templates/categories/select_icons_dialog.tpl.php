		<input type="button" id="reset_icon" class="w2mb-btn w2mb-btn-primary" value="<?php esc_attr_e('Reset icon image', 'W2MB'); ?>" />

		<div class="w2mb-icons-theme-block">
		<?php foreach ($categories_icons AS $icon): ?>
			<div class="w2mb-icon" icon_file="<?php echo esc_attr($icon); ?>"><img src="<?php echo W2MB_CATEGORIES_ICONS_URL . esc_attr($icon); ?>" title="<?php echo esc_attr($icon); ?>" /></div>
		<?php endforeach;?>
		</div>
		<div class="w2mb-clearfix"></div>