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
					<label><?php esc_html_e('Enable file title field', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="use_text"
						type="checkbox"
						class="regular-text"
						value="1"
						<?php if($content_field->use_text) echo 'checked'; ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Use default file title text when empty', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="use_default_text"
						type="checkbox"
						class="regular-text"
						value="1"
						<?php if($content_field->use_default_text) echo 'checked'; ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Default file title text', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="default_text"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->default_text); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Allowed file types', 'W2MB'); ?></label>
				</th>
				<td>
					<?php foreach ($content_field->get_mime_types() AS $type=>$label): ?>
					<label>
						<input
							name="allowed_mime_types[]"
							type="checkbox"
							class="regular-text"
							value="<?php echo esc_attr($type); ?>"
							<?php if (in_array($type, $content_field->allowed_mime_types)) echo 'checked'; ?> /> <?php echo $label['label']; ?> (<?php echo $type; ?>) <br />
					</label>
					<?php endforeach; ?>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>