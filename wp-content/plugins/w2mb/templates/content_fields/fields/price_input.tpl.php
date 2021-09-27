<div class="w2mb-form-group w2mb-field w2mb-field-input-block w2mb-field-input-block-<?php echo esc_attr($content_field->id); ?>">
	<div class="w2mb-col-md-2">
		<label class="w2mb-control-label">
			<?php echo $content_field->name; ?> <?php echo $content_field->currency_symbol; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?>
		</label>
	</div>
	<div class="w2mb-col-md-10">
		<input type="text" name="w2mb-field-input-<?php echo esc_attr($content_field->id); ?>" class="w2mb-field-input-price w2mb-form-control" value="<?php echo esc_attr($content_field->value); ?>" size="4" />
		<?php if ($content_field->description): ?><p class="description"><?php echo $content_field->description; ?></p><?php endif; ?>
	</div>
</div>