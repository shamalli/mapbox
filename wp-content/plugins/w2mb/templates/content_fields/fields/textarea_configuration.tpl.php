<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure textarea field', 'W2MB'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Max length', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="max_length"
						type="text"
						size="2"
						value="<?php echo esc_attr($content_field->max_length); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('HTML editor enabled', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="html_editor"
						type="checkbox"
						value="1"
						<?php checked(1, $content_field->html_editor, true)?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Run shortcodes', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="do_shortcodes"
						type="checkbox"
						value="1"
						<?php checked(1, $content_field->do_shortcodes, true)?> />
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>