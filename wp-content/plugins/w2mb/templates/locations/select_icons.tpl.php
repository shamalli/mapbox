		<input type="button" id="reset_icon" class="button button-primary button-large w2mb-btn w2mb-button-primary" value="<?php esc_attr_e('Reset icon image', 'W2MB'); ?>" />

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
			<?php $i = 0; ?>
			<?php foreach ($custom_map_icons AS $theme=>$dir): ?>
				<?php if (is_array($dir) && count($dir)): ?>
				<?php $columns = 1; ?>
				<td align="left" valign="top" width="<?php echo 100/$columns; ?>%">
					<div class="w2mb-icons-theme-block">
						<div class="w2mb-icons-theme-name"><?php echo $theme; ?></div>
						<?php foreach ($dir AS $icon): ?>
							<div class="w2mb-icon" icon_file="<?php echo esc_attr($theme) . '/' . esc_attr($icon); ?>"><img src="<?php echo W2MB_MAP_ICONS_URL . 'icons/' . esc_attr($theme) . '/' . esc_attr($icon); ?>" title="<?php echo esc_attr($theme) . '/' . esc_attr($icon); ?>" /></div>
						<?php endforeach;?>
					</div>
					<div class="w2mb-clearfix"></div>
				</td>
				<?php if ($i++ == $columns-1): ?>
					</tr><tr>
					<?php $i = 0; ?>
				<?php endif;?>
				<?php endif;?>
			<?php endforeach;?>
			</tr>
		</table>