<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php
	if ($group_id)
		esc_html_e('Edit content fields group', 'W2MB');
	else
		esc_html_e('Create new content fields group', 'W2MB');
	?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Fields Group name', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="name"
						id="content_fields_group_name"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_fields_group->name); ?>" />
						<?php w2mb_wpmlTranslationCompleteNotice(); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('On tab', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="on_tab"
						type="checkbox"
						value="1"
						<?php checked($content_fields_group->on_tab); ?> />
					<p class="description"><?php esc_html_e("Place this group on separate tab on single listings pages", 'W2MB'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Hide from anonymous users', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="hide_anonymous"
						type="checkbox"
						value="1"
						<?php checked($content_fields_group->hide_anonymous); ?> />
					<p class="description"><?php esc_html_e("This group of fields will be shown only for registered users", 'W2MB'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php
	if ($group_id)
		submit_button(esc_html__('Save changes', 'W2MB'));
	else
		submit_button(esc_html__('Create content fields group', 'W2MB'));
	?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>