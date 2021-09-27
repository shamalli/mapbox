<script>
	(function($) {
		"use strict";
	
		$(function() {
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>").datepicker({
				changeMonth: true,
				changeYear: true,
				<?php if (function_exists('is_rtl') && is_rtl()): ?>isRTL: true,<?php endif; ?>
				showButtonPanel: true,
				dateFormat: '<?php echo esc_js($dateformat); ?>',
				firstDay: <?php echo intval(get_option('start_of_week')); ?>,
				onSelect: function(dateText) {
					var tmstmp_str;
					var sDate = $("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>").datepicker("getDate");
					if (sDate) {
						sDate.setMinutes(sDate.getMinutes() - sDate.getTimezoneOffset());
						tmstmp_str = $.datepicker.formatDate('@', sDate)/1000;
					} else 
						tmstmp_str = 0;
					$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>").datepicker('option', 'minDate', sDate);
	
					$("input[name=field_<?php echo esc_attr($search_field->content_field->slug); ?>_min]").val(tmstmp_str).trigger("change");
				}
			});
			<?php
			if ($lang_code = w2mb_getDatePickerLangCode(get_locale())): ?>
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>").datepicker($.datepicker.regional[ "<?php echo esc_js($lang_code); ?>" ]);
			<?php endif; ?>
	
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>").datepicker({
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				dateFormat: '<?php echo esc_js($dateformat); ?>',
				firstDay: <?php echo intval(get_option('start_of_week')); ?>,
				onSelect: function(dateText) {
					var tmstmp_str;
					var sDate = $("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>").datepicker("getDate");
					if (sDate) {
						sDate.setMinutes(sDate.getMinutes() - sDate.getTimezoneOffset());
						tmstmp_str = $.datepicker.formatDate('@', sDate)/1000;
					} else 
						tmstmp_str = 0;
					$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>").datepicker('option', 'maxDate', sDate);
	
					$("input[name=field_<?php echo esc_attr($search_field->content_field->slug); ?>_max]").val(tmstmp_str).trigger("change");
				}
			});
			<?php
			if ($lang_code = w2mb_getDatePickerLangCode(get_locale())): ?>
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>").datepicker($.datepicker.regional[ "<?php echo esc_js($lang_code); ?>" ]);
			<?php endif; ?>
	
			<?php if ($search_field->min_max_value['max']): ?>
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>").datepicker('setDate', $.datepicker.parseDate('dd/mm/yy', '<?php echo date('d/m/Y', $search_field->min_max_value['max']); ?>'));
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>").datepicker('option', 'maxDate', $("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>").datepicker('getDate'));
			<?php endif; ?>
			$(document).on('click', '#reset-date-max-<?php echo esc_attr($search_form_id); ?>', function() {
				$.datepicker._clearDate('#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>');
			})
	
			<?php if ($search_field->min_max_value['min']): ?>
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>").datepicker('setDate', $.datepicker.parseDate('dd/mm/yy', '<?php echo date('d/m/Y', $search_field->min_max_value['min']); ?>'));
			$("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>").datepicker('option', 'minDate', $("#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>").datepicker('getDate'));
			<?php endif; ?>
			$(document).on('click', '#reset-date-min-<?php echo esc_attr($search_form_id); ?>', function() {
				$.datepicker._clearDate('#w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>');
			})
		});
	})(jQuery);
</script>
<?php if ($columns == 1) $col_md = 12; else $col_md = 6; ?>
<div class="w2mb-row w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->type); ?> w2mb-field-search-block-<?php echo esc_attr($search_form_id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?>_<?php echo esc_attr($search_form_id); ?>">
	<div class="w2mb-col-md-12">
		<label><?php echo $search_field->content_field->name; ?></label>
	</div>
	<div class="w2mb-col-md-<?php echo esc_attr($col_md); ?> w2mb-form-group">
		<div class="w2mb-row w2mb-form-horizontal">
			<div class="w2mb-col-md-7 w2mb-search-datetime-input-wrap">
				<div class="w2mb-has-feedback">
					<input type="text" class="w2mb-form-control" id="w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-min-<?php echo esc_attr($search_form_id); ?>" placeholder="<?php esc_attr_e('Start date', 'W2MB'); ?>" />
					<span class="w2mb-glyphicon w2mb-glyphicon-calendar w2mb-form-control-feedback"></span>
					<input type="hidden" name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_min" value="<?php echo esc_attr($search_field->min_max_value['min']); ?>"/>
				</div>
			</div>
			<div class="w2mb-col-md-5 w2mb-search-datetime-button-wrap">
				<input type="button" class="w2mb-btn w2mb-btn-primary w2mb-form-control" id="reset-date-min-<?php echo esc_attr($search_form_id); ?>" value="<?php esc_attr_e('reset', 'W2MB')?>" />
			</div>
		</div>
	</div>
	<div class="w2mb-col-md-<?php echo esc_attr($col_md); ?> w2mb-form-group">
		<div class="w2mb-row w2mb-form-horizontal">
			<div class="w2mb-col-md-7 w2mb-search-datetime-input-wrap">
					<div class="w2mb-has-feedback">
					<input type="text" class="w2mb-form-control" id="w2mb-field-input-<?php echo esc_attr($search_field->content_field->id); ?>-max-<?php echo esc_attr($search_form_id); ?>" placeholder="<?php esc_attr_e('End date', 'W2MB'); ?>" />
					<span class="w2mb-glyphicon w2mb-glyphicon-calendar w2mb-form-control-feedback"></span>
					<input type="hidden" name="field_<?php echo esc_attr($search_field->content_field->slug); ?>_max" value="<?php echo esc_attr($search_field->min_max_value['max']); ?>"/>
				</div>
			</div>
			<div class="w2mb-col-md-5 w2mb-search-datetime-button-wrap">
				<input type="button" class="w2mb-btn w2mb-btn-primary w2mb-form-control" id="reset-date-max-<?php echo esc_attr($search_form_id); ?>" value="<?php esc_attr_e('reset', 'W2MB')?>" />
			</div>
		</div>
	</div>
</div>