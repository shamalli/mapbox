<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php
	if ($locations_level_id)
		esc_html_e('Edit locations level', 'W2MB');
	else
		esc_html_e('Create new locations level', 'W2MB');
	?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_locations_levels_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Level name', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="name"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($locations_level->name); ?>" />
					<?php w2mb_wpmlTranslationCompleteNotice(); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('In address line', 'W2MB'); ?></label>
				</th>
				<td>
					<input type="checkbox" value="1" name="in_address_line" <?php if ($locations_level->in_address_line) echo 'checked'; ?> />
					<p class="description"><?php esc_html_e("Render locations of this level in address line", 'W2MB'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Allow add term', 'W2MB'); ?></label>
				</th>
				<td>
					<input type="checkbox" value="1" name="allow_add_term" <?php if ($locations_level->allow_add_term) echo 'checked'; ?> />
					<p class="description"><?php esc_html_e("Users able to add new location from the frontend", 'W2MB'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php
	if ($locations_level_id)
		submit_button(esc_html__('Save changes', 'W2MB'));
	else
		submit_button(esc_html__('Create locations level', 'W2MB'));
	?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>