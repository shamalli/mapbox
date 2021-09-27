<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php printf(esc_html__('Configure %s search field', 'W2MB'), $w2mb_instance->content_fields->fields_types_names[$search_field->content_field->type]); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Search input mode', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<label>
						<input
							name="search_input_mode"
							type="radio"
							value="keywords"
							<?php checked($search_field->search_input_mode, 'keywords'); ?> />
						<?php esc_html_e('Search by keywords field', 'W2MB'); ?>
					</label>
					<br />
					<label>
						<input
							name="search_input_mode"
							type="radio"
							value="input"
							<?php checked($search_field->search_input_mode, 'input'); ?> />
							<?php esc_html_e('Render own search field', 'W2MB'); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>