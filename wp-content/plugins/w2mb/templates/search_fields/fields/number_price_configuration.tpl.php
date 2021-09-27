<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure number/price search field', 'W2MB'); ?>
</h2>

<script>
	(function($) {
		"use strict";
	
		$(function() {
			$(document).on('click', '#add_selection_item', function() {
				$("#w2mb-selection-items-wrapper").append('<div class="selection_item"><input name="min_max_options[]" type="text" size="9" value="" /><img class="w2mb-delete-selection-item" src="<?php echo W2MB_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove option', 'W2MB')?>" /></div>');
			});
			$(document).on("click", ".w2mb-delete-selection-item", function() {
				$(this).parent().remove();
			});
		});
	})(jQuery);
</script>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Search mode', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<label>
						<input
							name="mode"
							type="radio"
							value="exact_number"
							<?php checked($search_field->mode, 'exact_number'); ?> />
						<?php esc_html_e('Enter exact number into the text input', 'W2MB'); ?>
					</label>
					<br />
					<label>
						<input
							name="mode"
							type="radio"
							value="min_max_exact_number"
							<?php checked($search_field->mode, 'min_max_exact_number'); ?> />
						<?php esc_html_e('Enter min-max numbers into the text inputs', 'W2MB'); ?>
					</label>
					<br />
					<label>
						<input
							name="mode"
							type="radio"
							value="min_max"
							<?php checked($search_field->mode, 'min_max'); ?> />
						<?php esc_html_e('Search using min-max combination of select boxes', 'W2MB'); ?>
					</label>
					<br />
					<label>
						<input
							name="mode"
							type="radio"
							value="min_max_slider"
							<?php checked($search_field->mode, 'min_max_slider'); ?> />
						<?php esc_html_e('Search range slider with steps from Min-Max options', 'W2MB'); ?>
					</label>
					<br />
					<label>
						<input
							name="mode"
							type="radio"
							value="range_slider"
							<?php checked($search_field->mode, 'range_slider'); ?> />
						<?php esc_html_e('Search range slider with step 1.', 'W2MB'); ?>
					</label>
					 <?php esc_html_e('From:', 'W2MB'); ?><input type="text" name="slider_step_1_min" size=4 value="<?php echo esc_attr($search_field->slider_step_1_min); ?>" /> <?php esc_html_e('To:', 'W2MB'); ?><input type="text" name="slider_step_1_max" size=4 value="<?php echo esc_attr($search_field->slider_step_1_max); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Min-Max options:', 'W2MB'); ?>
				</th>
				<td>
					<div id="w2mb-selection-items-wrapper">
						<?php if (count($search_field->min_max_options)): ?>
						<?php foreach ($search_field->min_max_options AS $item): ?>
						<div class="selection_item">
							<input
								name="min_max_options[]"
								type="text"
								size="9"
								value="<?php echo esc_attr($item); ?>" />
							<img class="w2mb-delete-selection-item" src="<?php echo W2MB_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove min-max option', 'W2MB')?>" />
						</div>
						<?php endforeach; ?>
						<?php else: ?>
						<div class="selection_item">
							<input
								name="min_max_options[]"
								type="text"
								size="9"
								value="" />
							<img class="w2mb-delete-selection-item" src="<?php echo W2MB_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove min-max option', 'W2MB')?>" />
						</div>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="button" id="add_selection_item" class="button button-primary" value="<?php esc_attr_e('Add min-max option', 'W2MB'); ?>" />
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>