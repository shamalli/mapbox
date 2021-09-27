<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure price field', 'W2MB'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Currency symbol', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="currency_symbol"
						type="text"
						size="1"
						value="<?php echo esc_attr($content_field->currency_symbol); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Currency symbol position', 'W2MB'); ?></label>
				</th>
				<td>
					<select name="symbol_position">
						<option value="1" <?php if($content_field->symbol_position == '1') echo 'selected'; ?>>$1.00</option>
						<option value="2" <?php if($content_field->symbol_position == '2') echo 'selected'; ?>>$ 1.00</option>
						<option value="3" <?php if($content_field->symbol_position == '3') echo 'selected'; ?>>1.00$</option>
						<option value="4" <?php if($content_field->symbol_position == '4') echo 'selected'; ?>>1.00 $</option>
					</select>
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
					<label><?php esc_html_e('Hide decimals', 'W2MB'); ?></label>
				</th>
				<td>
					<select name="hide_decimals">
						<option value="0" <?php if($content_field->hide_decimals == '0') echo 'selected'; ?>><?php esc_html_e('no', 'W2MB')?></option>
						<option value="1" <?php if($content_field->hide_decimals == '1') echo 'selected'; ?>><?php esc_html_e('yes', 'W2MB')?></option>
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
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>