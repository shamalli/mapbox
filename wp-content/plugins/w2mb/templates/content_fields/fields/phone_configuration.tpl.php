<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php printf(esc_html__('Configure %s field', 'W2MB'), $w2mb_instance->content_fields->fields_types_names[$content_field->type]); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Max length',  'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
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
					<label><?php esc_html_e('PHP RegEx template',  'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="regex"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->regex); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Phone mode',  'W2MB'); ?></label>
					<p class="description"><?php esc_html_e("for mobile devices adds special tag to call directly from phone or open needed app", 'W2MB'); ?></p>
				</th>
				<td>
					<input
						id="phone_mode_phone"
						name="phone_mode"
						type="radio"
						value="phone"
						<?php checked('phone', $content_field->phone_mode); ?> /> <label for="phone_mode_phone"><?php esc_html_e('Phone call', 'W2MB'); ?></label>
					</br>
					<input
						id="phone_mode_viber"
						name="phone_mode"
						type="radio"
						value="viber"
						<?php checked('viber', $content_field->phone_mode); ?> /> <label for="phone_mode_viber"><?php esc_html_e('Viber chat', 'W2MB'); ?></label>
					</br>
					<input
						id="phone_mode_whatsapp"
						name="phone_mode"
						type="radio"
						value="whatsapp"
						<?php checked('whatsapp', $content_field->phone_mode); ?> /> <label for="phone_mode_whatsapp"><?php esc_html_e('WhatsApp chat', 'W2MB'); ?></label>
					</br>
					<input
						id="phone_mode_telegram"
						name="phone_mode"
						type="radio"
						value="telegram"
						<?php checked('telegram', $content_field->phone_mode); ?> /> <label for="phone_mode_telegram"><?php esc_html_e('Telegram chat', 'W2MB'); ?></label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>