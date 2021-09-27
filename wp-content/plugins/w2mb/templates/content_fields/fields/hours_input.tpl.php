<div class="w2mb-form-group w2mb-field w2mb-field-input-block w2mb-field-input-block-<?php echo esc_attr($content_field->id); ?>">
	<div class="w2mb-col-md-2">
		<label class="w2mb-control-label">
			<?php echo $content_field->name; ?>
		</label>
	</div>
	<div class="w2mb-col-md-10">
		<?php foreach ($week_days AS $key=>$day): ?>
		<div class="w2mb-week-day-wrap">
			<span class="w2mb-week-day"><?php echo $content_field->week_days_names[$key]; ?></span> <span class="w2mb-week-day-controls"><select name="<?php echo $day; ?>_from_hour_<?php echo esc_attr($content_field->id); ?>" class="w2mb-week-day-input"><?php echo $content_field->getOptionsHour($day.'_from'); ?></select><?php if ($content_field->hours_clock == 12): ?> <select name="<?php echo $day; ?>_from_am_pm_<?php echo esc_attr($content_field->id); ?>" class="w2mb-week-day-input"><?php echo $content_field->getOptionsAmPm($day.'_from'); ?></select><?php endif; ?></span> &nbsp;&nbsp;-&nbsp;&nbsp; <span class="w2mb-week-day-controls"><select name="<?php echo $day; ?>_to_hour_<?php echo esc_attr($content_field->id); ?>" class="w2mb-week-day-input"><?php echo $content_field->getOptionsHour($day.'_to'); ?></select><?php if ($content_field->hours_clock == 12): ?> <select name="<?php echo $day; ?>_to_am_pm_<?php echo esc_attr($content_field->id); ?>" class="w2mb-week-day-input"><?php echo $content_field->getOptionsAmPm($day.'_to'); ?></select><?php endif; ?></span> <label><input type="checkbox" name="<?php echo $day; ?>_closed_<?php echo esc_attr($content_field->id); ?>" class="w2mb-closed-day-option" <?php checked($content_field->value[$day.'_closed'], 1); ?> class="closed_cb" value="1" /> <?php esc_html_e('Closed', 'W2MB'); ?></label>
		</div>
		<?php endforeach; ?>
		<div class="w2mb-week-day-clear-button">
			<button class="w2mb-btn w2mb-btn-primary w2mb-clear-hours"><?php esc_html_e('Reset hours & minutes', 'W2MB'); ?></button>
		</div>
	</div>
</div>