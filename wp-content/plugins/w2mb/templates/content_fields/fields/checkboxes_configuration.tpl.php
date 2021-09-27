<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure checkboxes field', 'W2MB'); ?>
</h2>

<script>
	(function($) {
		"use strict";
	
		$(function() {
			var max_index = <?php echo ((count(array_keys($content_field->selection_items)) ? max(array_keys($content_field->selection_items)) : 1)); ?>;
			$(document).on('click', '#add_selection_item', function() {
				max_index = max_index+1;
				$("#w2mb-selection-items-wrapper").append('<div class="selection_item"><input name="selection_items['+max_index+']" type="text" class="regular-text" value="" /><img class="w2mb-delete-selection-item" src="<?php echo W2MB_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove selection item', 'W2MB')?>" /> <span class="w2mb-display-none w2mb-icon-tag w2mb-icon-tag-'+max_index+'"></span><input type="hidden" name="icon_images['+max_index+']" id="w2mb-icon-image-'+max_index+'" value=""><a class="w2mb-select-fa-icon" href="javascript: void(0);" data-icon-tag="w2mb-icon-tag-'+max_index+'" data-icon-image-name="w2mb-icon-image-'+max_index+'"><?php echo esc_js(esc_html__('select icon', 'W2MB')); ?></a> <span class="w2mb-move-label"><?php esc_attr_e('move', 'W2MB'); ?></span><?php echo esc_js('(ID: ', 'W2MB'); ?>'+max_index+')</div>');
			});
			$(document).on("click", ".w2mb-delete-selection-item", function() {
				$(this).parent().remove();
			});

			$("#w2mb-selection-items-wrapper").sortable({
				delay: 50,
				placeholder: "ui-sortable-placeholder",
				helper: function(e, ui) {
					ui.children().each(function() {
						$(this).width($(this).width());
					});
					return ui;
				},
				start: function(e, ui){
					ui.placeholder.height(ui.item.height());
				}
	    	});
		});
	})(jQuery);
</script>

<?php esc_html_e('You may order items by drag & drop.', 'W2MB'); ?>
<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Number of columns on frontend listing page', 'W2MB'); ?></label>
				</th>
				<td>
					<select name="columns_number">
						<option value="1" <?php selected($content_field->columns_number, 1); ?>>1</option>
						<option value="2" <?php selected($content_field->columns_number, 2); ?>>2</option>
						<option value="3" <?php selected($content_field->columns_number, 3); ?>>3</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('How to display items', 'W2MB'); ?></label>
				</th>
				<td>
					<label>
						<input
							name="how_display_items"
							type="radio"
							value="all"
							<?php checked($content_field->how_display_items, 'all'); ?> />
							<?php esc_html_e('All items with checked/unchecked marks', 'W2MB'); ?>
					</label>
					<br />
					<label>
						<input
							name="how_display_items"
							type="radio"
							value="checked"
							<?php checked($content_field->how_display_items, 'checked'); ?> />
							<?php esc_html_e('Only checked items', 'W2MB'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Selection items:', 'W2MB'); ?></label>
				</th>
				<td>
					<div id="w2mb-selection-items-wrapper">
						<?php if (count($content_field->selection_items)): ?>
						<?php foreach ($content_field->selection_items AS $key=>$item): ?>
						<div class="selection_item">
							<input
								name="selection_items[<?php echo esc_attr($key); ?>]"
								type="text"
								class="regular-text"
								value="<?php echo esc_attr($item); ?>" />
							<img class="w2mb-delete-selection-item" src="<?php echo W2MB_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove selection item', 'W2MB')?>" />
							
							<span class="<?php if (empty($content_field->icon_images[$key])): ?>w2mb-display-none<?php endif; ?> w2mb-icon-tag w2mb-icon-tag-<?php echo esc_attr($key); ?> <?php if (!empty($content_field->icon_images[$key])): ?>w2mb-fa <?php echo esc_attr($content_field->icon_images[$key]); ?><?php endif; ?>"></span>
							<input type="hidden" name="icon_images[<?php echo esc_attr($key); ?>]" id="w2mb-icon-image-<?php echo esc_attr($key); ?>" value="<?php if (!empty($content_field->icon_images[$key])) echo esc_attr($content_field->icon_images[$key]); ?>">
							<a class="w2mb-select-fa-icon" href="javascript: void(0);" data-icon-tag="w2mb-icon-tag-<?php echo esc_attr($key); ?>" data-icon-image-name="w2mb-icon-image-<?php echo esc_attr($key); ?>"><?php echo esc_js(esc_html__('select icon', 'W2MB')); ?></a>
							
							<span class="w2mb-move-label"><?php esc_html_e('move', 'W2MB'); ?></span>
							<?php printf(esc_html__('(ID: %d)', 'W2MB'), $key); ?>
						</div>
						<?php endforeach; ?>
						<?php else: ?>
						<div class="selection_item">
							<input
								name="selection_items[1]"
								type="text"
								class="regular-text"
								value="" />
							<img class="w2mb-delete-selection-item" src="<?php echo W2MB_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove selection item', 'W2MB')?>" />
							
							<span class="w2mb-display-none w2mb-icon-tag w2mb-icon-tag-1"></span>
							<input type="hidden" name="icon_images[1]" id="w2mb-icon-image-1" value="">
							<a class="w2mb-select-fa-icon" href="javascript: void(0);" data-icon-tag="w2mb-icon-tag-1" data-icon-image-name="w2mb-icon-image-1"><?php echo esc_js(esc_html__('select icon', 'W2MB')); ?></a>
							
							<span class="w2mb-move-label"><?php esc_html_e('move', 'W2MB'); ?></span>
							<?php printf(esc_html__('(ID: %d)', 'W2MB'), 1); ?>
						</div>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="button" id="add_selection_item" class="button button-primary" value="<?php esc_attr_e('Add selection item', 'W2MB'); ?>" />
	
	<?php submit_button(esc_html__('Save changes', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>