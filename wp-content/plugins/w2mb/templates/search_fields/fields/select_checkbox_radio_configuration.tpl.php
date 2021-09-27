<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure select/checkbox/radio search field', 'W2MB'); ?>
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
					<select name="search_input_mode">
						<option value="checkboxes" <?php selected($search_field->search_input_mode, 'checkboxes'); ?>><?php esc_html_e('checkboxes', 'W2MB'); ?></option>
						<option value="selectbox" <?php selected($search_field->search_input_mode, 'selectbox'); ?>><?php esc_html_e('selectbox', 'W2MB'); ?></option>
						<option value="radiobutton" <?php selected($search_field->search_input_mode, 'radiobutton'); ?>><?php esc_html_e('radio buttons', 'W2MB'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Operator for the search', 'W2MB'); ?></label>
					<p class="description"><?php esc_html_e('Works only in checkboxes mode', 'W2MB'); ?></p>
				</th>
				<td>
					<label>
						<input
							name="checkboxes_operator"
							type="radio"
							value="OR"
							<?php checked($search_field->checkboxes_operator, 'OR'); ?> />
						<?php esc_html_e('OR - any item present is enough', 'W2MB')?>
					</label>
					<br />
					<label>
						<input
							name="checkboxes_operator"
							type="radio"
							value="AND"
							<?php checked($search_field->checkboxes_operator, 'AND'); ?> />
						<?php esc_html_e('AND - require all items', 'W2MB')?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Items counter', 'W2MB'); ?></label>
					<p class="description"><?php esc_html_e('On the search form shows the number of listings per item (in brackets)', 'W2MB'); ?></p>
				</th>
				<td>
					<label>
						<input
							name="items_count"
							type="checkbox"
							value="1"
							<?php checked($search_field->items_count, 1); ?> />
						<?php esc_html_e('enable', 'W2MB')?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>