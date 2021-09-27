<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure number field', 'W2MB'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Is integer or decimal', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="is_integer"
						type="radio"
						value="1"
						<?php if($content_field->is_integer) echo 'checked'; ?> />
					<?php esc_html_e('integer', 'W2MB')?>
					&nbsp;&nbsp;
					<input
						name="is_integer"
						type="radio"
						value="0"
						<?php if(!$content_field->is_integer) echo 'checked'; ?> />
					<?php esc_html_e('decimal', 'W2MB')?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Decimal separator', 'W2MB'); ?></label>
				</th>
				<td>
					<select name="decimal_separator">
						<option value="." <?php if($content_field->decimal_separator == '.') echo 'selected'; ?>><?php esc_html_e('dot', 'W2MB')?></option>
						<option value="," <?php if($content_field->decimal_separator == ',') echo 'selected'; ?>><?php esc_html_e('comma', 'W2MB')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Thousands separator', 'W2MB'); ?></label>
				</th>
				<td>
					<select name="thousands_separator">
						<option value="" <?php if($content_field->thousands_separator == '') echo 'selected'; ?>><?php esc_html_e('no separator', 'W2MB')?></option>
						<option value="." <?php if($content_field->thousands_separator == '.') echo 'selected'; ?>><?php esc_html_e('dot', 'W2MB')?></option>
						<option value="," <?php if($content_field->thousands_separator == ',') echo 'selected'; ?>><?php esc_html_e('comma', 'W2MB')?></option>
						<option value=" " <?php if($content_field->thousands_separator == ' ') echo 'selected'; ?>><?php esc_html_e('space', 'W2MB')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Min', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="min"
						type="text"
						size="2"
						value="<?php echo esc_attr($content_field->min); ?>" />
					<p class="description"><?php esc_html_e("leave empty if you do not need to limit this field", 'W2MB'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Max', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="max"
						type="text"
						size="2"
						value="<?php echo esc_attr($content_field->max); ?>" />
					<p class="description"><?php esc_html_e("leave empty if you do not need to limit this field", 'W2MB'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>