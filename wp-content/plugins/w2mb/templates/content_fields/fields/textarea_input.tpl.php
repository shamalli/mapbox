<div class="w2mb-form-group w2mb-field w2mb-field-input-block w2mb-field-input-block-<?php echo esc_attr($content_field->id); ?>">
	<div class="w2mb-col-md-2">
		<label class="w2mb-control-label">
			<?php echo $content_field->name; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?>
		</label>
	</div>
	<div class="w2mb-col-md-10">
		<?php if ($content_field->html_editor): ?>
		<?php wp_editor($content_field->value, 'w2mb-field-input-'.$content_field->id, array('media_buttons' => true, 'editor_class' => 'w2mb-editor-class')); ?>
		<?php else: ?>
		<textarea name="w2mb-field-input-<?php echo esc_attr($content_field->id); ?>" class="w2mb-field-input-textarea w2mb-form-control" rows="5"><?php echo esc_textarea($content_field->value); ?></textarea>
		<?php endif; ?>
		<?php if ($content_field->description): ?><p class="description"><?php echo $content_field->description; ?></p><?php endif; ?>
	</div>
</div>