<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure opening hours field', 'W2MB'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Time convention', 'W2MB'); ?></label>
				</th>
				<td>
					<label>
						<input
							name="hours_clock"
							type="radio"
							value="12"
							<?php if ($content_field->hours_clock == 12) echo 'checked'; ?> />
						<?php esc_html_e('12-hour clock', 'W2MB')?>
					</label>
					&nbsp;&nbsp;
					<label>
						<input
							name="hours_clock"
							type="radio"
							value="24"
							<?php if ($content_field->hours_clock == 24) echo 'checked'; ?> />
						<?php esc_html_e('24-hour clock', 'W2MB')?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>