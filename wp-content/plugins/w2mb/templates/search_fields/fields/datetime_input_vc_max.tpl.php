<style>
.wp-customizer div.ui-datepicker {
	z-index: 500001 !important;
}
</style>
<script>
	(function($) {
		"use strict";
	
		$(function() {
			$("#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_max").datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: '<?php echo esc_js($dateformat); ?>',
				firstDay: <?php echo intval(get_option('start_of_week')); ?>,
				onSelect: function(dateText) {
					var tmstmp_str;
					var sDate = $("#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_max").datepicker("getDate");
					if (sDate) {
						sDate.setMinutes(sDate.getMinutes() - sDate.getTimezoneOffset());
						tmstmp_str = $.datepicker.formatDate('@', sDate)/1000;
					} else 
						tmstmp_str = 0;
					$("#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_min").datepicker('option', 'maxDate', sDate);
	
					$("input[name='<?php echo esc_attr($settings['param_name']); ?>']").val(tmstmp_str);
				}
			});
			<?php
			if ($lang_code = w2mb_getDatePickerLangCode(get_locale())): ?>
			$("#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_max").datepicker($.datepicker.regional[ "<?php echo esc_js($lang_code); ?>" ]);
			<?php endif; ?>
	
			<?php if ($value): ?>
			$("#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_max").datepicker('setDate', $.datepicker.parseDate('dd/mm/yy', '<?php echo date('d/m/Y', $value); ?>'));
			$("#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_min").datepicker('option', 'maxDate', $("#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_max").datepicker('getDate'));
			<?php endif; ?>
			$(document).on('click', '#reset_date_max_<?php echo esc_attr($settings['field_id']); ?>', function() {
				$.datepicker._clearDate('#w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_max');
			})
		});
	})(jQuery);
</script>
<input type="text" id="w2mb-field-input-<?php echo esc_attr($settings['field_id']); ?>_max" placeholder="<?php esc_attr_e('End date', 'W2MB'); ?>" class="w2mb-form-control" size="9" />
<input type="hidden" name="<?php echo esc_attr($settings['param_name']); ?>" value="<?php echo esc_attr($value); ?>" class="wpb_vc_param_value"/>
<input type="button" id="reset_date_max_<?php echo esc_attr($settings['field_id']); ?>" value="<?php esc_attr_e('reset', 'W2MB')?>" />