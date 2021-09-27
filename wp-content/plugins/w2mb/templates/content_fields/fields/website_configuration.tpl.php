<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure website field', 'W2MB'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Open link in new window', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="is_blank"
						type="checkbox"
						class="regular-text"
						value="1"
						<?php if($content_field->is_blank) echo 'checked'; ?>/>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Add nofollow attribute', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="is_nofollow"
						type="checkbox"
						class="regular-text"
						value="1"
						<?php if($content_field->is_nofollow) echo 'checked'; ?>/>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Enable link text field', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="use_link_text"
						type="checkbox"
						class="regular-text"
						value="1"
						<?php if($content_field->use_link_text) echo 'checked'; ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Use default link text when empty', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="use_default_link_text"
						type="checkbox"
						class="regular-text"
						value="1"
						<?php if($content_field->use_default_link_text) echo 'checked'; ?> />
						<p class="description"><?php esc_html_e('In other case the URL will be displayed as link text'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Default link text', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="default_link_text"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->default_link_text); ?>" />
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>