<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Configure select/radio buttons field', 'W2MB'); ?>
</h2>

<script>
	(function($) {
		"use strict";
	
		$(function() {
			var max_index = <?php echo ((count(array_keys($content_field->selection_items)) ? max(array_keys($content_field->selection_items)) : 1)); ?>;
			$(document).on('click', '#add_selection_item', function() {
				max_index = max_index+1;
				$("#w2mb-selection-items-wrapper").append('<div class="selection_item"><input name="selection_items['+max_index+']" type="text" class="regular-text" value="" /><img class="w2mb-delete-selection-item" src="<?php echo W2MB_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove selection item', 'W2MB')?>" /><span class="w2mb-move-label"><?php esc_attr_e('move', 'W2MB'); ?></span><?php echo esc_js('(ID: ', 'W2MB'); ?>'+max_index+')</div>');
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
			<?php do_action('w2mb_select_content_field_configuration_html', $content_field); ?>
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