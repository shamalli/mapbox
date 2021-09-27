<div class="w2mb-form-group w2mb-field w2mb-field-input-block w2mb-field-input-block-<?php echo esc_attr($content_field->id); ?>">
	<div class="w2mb-col-md-2">
		<label class="w2mb-control-label">
			<?php echo $content_field->name; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?>
		</label>
	</div>
	<div class="w2mb-col-md-10">
		<div class="w2mb-row">
			<div class="w2mb-col-md-12">
				<label><?php esc_html_e('URL:', 'W2MB'); ?></label>
				<input type="text" name="w2mb-field-input-url_<?php echo esc_attr($content_field->id); ?>" class="w2mb-field-input-url w2mb-form-control regular-text" value="<?php echo esc_url($content_field->value['url']); ?>" />
			</div>
			<?php if ($content_field->use_link_text): ?>
			<div class="w2mb-col-md-12">
				<label><?php esc_html_e('Link text:', 'W2MB'); ?></label>
				<input type="text" name="w2mb-field-input-text_<?php echo esc_attr($content_field->id); ?>" class="w2mb-field-input-text w2mb-form-control regular-text" value="<?php echo esc_attr($content_field->value['text']); ?>" />
			</div>
			<?php endif; ?>
		</div>
		<?php if ($content_field->description): ?><p class="description"><?php echo $content_field->description; ?></p><?php endif; ?>
	</div>
</div>