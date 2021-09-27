<?php $index = $search_field->content_field->id . '_' . $search_form_id; ?>
<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
<?php $min_value = (($search_field->min_max_value['min']) ? $search_field->min_max_value['min'] : esc_html__('min', 'W2MB')); ?>
<?php $max_value = (($search_field->min_max_value['max']) ? $search_field->min_max_value['max'] : esc_html__('max', 'W2MB')); ?>
<?php elseif ($search_field->mode == 'range_slider'): ?>
<?php $min_value = (($search_field->min_max_value['min']) ? $search_field->min_max_value['min'] : esc_html__('min', 'W2MB')); ?>
<?php $max_value = (($search_field->min_max_value['max']) ? $search_field->min_max_value['max'] : esc_html__('max', 'W2MB')); ?>
<?php endif; ?>
<div class="w2mb-row w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->type); ?> w2mb-field-search-block-<?php echo esc_attr($search_form_id); ?> w2mb-field-search-block-<?php echo esc_attr($index); ?>">
	<div class="w2mb-col-md-12">
		<label><?php echo $search_field->content_field->name; ?> <span id="slider_label_<?php echo esc_attr($index); ?>"><?php echo $min_value . ' - ' . $max_value; ?></span></label>
	</div>
	
	<script>
		(function($) {
			"use strict";
		
			$(function() {
				<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
				var slider_params_<?php echo esc_attr($index); ?> = ['<?php esc_html_e('min', 'W2MB'); ?>', <?php echo implode(',', $search_field->min_max_options); ?>, '<?php esc_html_e('max', 'W2MB'); ?>'];
				var slider_min_<?php echo esc_attr($index); ?> = 0;
				var slider_max_<?php echo esc_attr($index); ?> = slider_params_<?php echo esc_attr($index); ?>.length-1;
				<?php elseif ($search_field->mode == 'range_slider'): ?>
				var slider_min_<?php echo esc_attr($index); ?> = <?php echo $search_field->slider_step_1_min-1; ?>;
				var slider_max_<?php echo esc_attr($index); ?> = <?php echo $search_field->slider_step_1_max+1; ?>;
				<?php endif; ?>
				$('#range_slider_<?php echo esc_attr($index); ?>').slider({
					<?php if (function_exists('is_rtl') && is_rtl()): ?>
					isRTL: true,
					<?php endif; ?>
					min: slider_min_<?php echo esc_attr($index); ?>,
					max: slider_max_<?php echo esc_attr($index); ?>,
					range: true,
					<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
					values: [<?php echo ((($min = array_search($search_field->min_max_value['min'], $search_field->min_max_options)) !== false) ? $min+1 : 0); ?>, <?php echo ((($max = array_search($search_field->min_max_value['max'], $search_field->min_max_options)) !== false) ? $max+1 : count($search_field->min_max_options)+1); ?>],
					<?php elseif ($search_field->mode == 'range_slider'): ?>
					values: [<?php echo (($search_field->min_max_value['min']) ? $search_field->min_max_value['min']+1 : $search_field->slider_step_1_min-1); ?>, <?php echo (($search_field->min_max_value['max']) ? $search_field->min_max_value['max']+1 : $search_field->slider_step_1_max+1); ?>],
					<?php endif; ?>
					slide: function(event, ui) {
						<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
						$('#slider_label_<?php echo esc_attr($index); ?>').html(slider_params_<?php echo esc_attr($index); ?>[ui.values[0]] + ' - ' + slider_params_<?php echo esc_attr($index); ?>[ui.values[1]]);
						<?php elseif ($search_field->mode == 'range_slider'): ?>
						if (ui.values[0] == <?php echo $search_field->slider_step_1_min-1; ?>) {
							var min = '<?php esc_html_e('min', 'W2MB'); ?>';
						} else {
							var min = ui.values[0];
						}
						if (ui.values[1] == <?php echo $search_field->slider_step_1_max+1; ?>) {
							var max = '<?php esc_html_e('max', 'W2MB'); ?>';
						} else {
							var max = ui.values[1];
						}
		
						$('#slider_label_<?php echo esc_attr($index); ?>').html(min + ' - ' + max);
						<?php endif; ?>
					},
					stop: function(event, ui) {
						<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
						if (slider_params_<?php echo esc_attr($index); ?>[ui.values[0]] == '<?php esc_html_e('min', 'W2MB'); ?>')
							$('#field_<?php echo esc_attr($index); ?>_min').val('');
						else
							$('#field_<?php echo esc_attr($index); ?>_min').val(slider_params_<?php echo esc_attr($index); ?>[ui.values[0]]);
						if (slider_params_<?php echo esc_attr($index); ?>[ui.values[1]] == '<?php esc_html_e('max', 'W2MB'); ?>')
							$('#field_<?php echo esc_attr($index); ?>_max').val('').trigger("change");
						else
							$('#field_<?php echo esc_attr($index); ?>_max').val(slider_params_<?php echo esc_attr($index); ?>[ui.values[1]]).trigger("change");
						<?php elseif ($search_field->mode == 'range_slider'): ?>
						if (ui.values[0] == <?php echo $search_field->slider_step_1_min-1; ?>) {
							$('#field_<?php echo esc_attr($index); ?>_min').val('');
						} else {
							$('#field_<?php echo esc_attr($index); ?>_min').val(ui.values[0]);
						}
						if (ui.values[1] == <?php echo $search_field->slider_step_1_max+1; ?>) {
							$('#field_<?php echo esc_attr($index); ?>_max').val('').trigger("change");
						} else {
							$('#field_<?php echo esc_attr($index); ?>_max').val(ui.values[1]).trigger("change");
						}
						<?php endif; ?>
					}
				});
			});
		})(jQuery);
	</script>
	<div class="w2mb-col-md-12">
		<div class="w2mb-jquery-ui-slider">
			<div id="range_slider_<?php echo esc_attr($index); ?>"></div>
			<input type="hidden" id="field_<?php echo esc_attr($index); ?>_min" name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_min" value="<?php echo (($min_value == esc_html__('min', 'W2MB')) ? '' : $min_value); ?>" />
			<input type="hidden" id="field_<?php echo esc_attr($index); ?>_max" name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_max" value="<?php echo (($max_value == esc_html__('max', 'W2MB')) ? '' : $max_value); ?>" />
		</div>
	</div>
</div>
