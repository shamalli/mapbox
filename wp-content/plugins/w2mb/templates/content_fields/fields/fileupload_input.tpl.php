<div class="w2mb-form-group w2mb-field w2mb-field-input-block w2mb-field-input-block-<?php echo esc_attr($content_field->id); ?>">
	<div class="w2mb-col-md-2">
		<label class="w2mb-control-label">
			<?php echo $content_field->name; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?>
		</label>
	</div>
	<div class="w2mb-col-md-10">
		<div class="w2mb-row">
			<?php if ($file): ?>
			<div class="w2mb-col-md-6">
				<label><?php esc_html_e('Uploaded file:', 'W2MB'); ?></label>
				<a href="<?php echo esc_url($file->guid); ?>" target="_blank"><?php echo basename($file->guid); ?></a>
				<input type="hidden" name="w2mb-uploaded-file-<?php echo esc_attr($content_field->id); ?>" value="<?php echo esc_attr($file->ID); ?>" />
				<br />
				<label><input type="checkbox" name="w2mb-reset-file-<?php echo esc_attr($content_field->id); ?>" value="1" /> <?php esc_html_e('reset uploaded file', 'W2MB'); ?></label>
			</div>
			<?php endif; ?>
			<div class="w2mb-col-md-6">
				<label><?php esc_html_e('Select file to upload:', 'W2MB'); ?></label>
				<input type="file" name="w2mb-field-input-<?php echo esc_attr($content_field->id); ?>" class="w2mb-field-input-fileupload" />
			</div>
			<?php if ($content_field->use_text): ?>
			<div class="w2mb-col-md-12">
				<label><?php esc_html_e('File title:', 'W2MB'); ?></label>
				<input type="text" name="w2mb-field-input-text-<?php echo esc_attr($content_field->id); ?>" class="w2mb-field-input-text w2mb-form-control regular-text" value="<?php echo esc_attr($content_field->value['text']); ?>" />
			</div>
			<?php endif; ?>
		</div>
		<?php if ($content_field->description): ?><p class="description"><?php echo $content_field->description; ?></p><?php endif; ?>
	</div>
</div>