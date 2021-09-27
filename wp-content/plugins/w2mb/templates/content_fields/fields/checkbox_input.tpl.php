<?php if (count($content_field->selection_items)): ?>
<div class="w2mb-form-group w2mb-field w2mb-field-input-block w2mb-field-input-block-<?php echo esc_attr($content_field->id); ?>">
	<div class="w2mb-col-md-2">
		<label class="w2mb-control-label">
			<?php echo $content_field->name; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?>
		</label>
	</div>
	<div class="w2mb-col-md-10">
		<?php foreach ($content_field->selection_items AS $key=>$item): ?>
		<div class="w2mb-checkbox w2mb-field-checkbox-input">
			<label>
				<?php if ($content_field->icon_images[$key]): ?>
				<span class="w2mb-field-icon w2mb-fa w2mb-fa-lg <?php echo esc_attr($content_field->icon_images[$key]); ?>"></span>
				<?php endif; ?>
				<input type="checkbox" name="w2mb-field-input-<?php echo esc_attr($content_field->id); ?>[]" class="w2mb-field-input-checkbox" value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $content_field->value)) echo 'checked'; ?> />
				<?php echo $item; ?>
			</label>
		</div>
		<?php endforeach; ?>
	</div>
	<?php if ($content_field->description): ?>
	<div class="w2mb-col-md-12 w2mb-col-md-offset-2">
		<p class="description"><?php echo $content_field->description; ?></p>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>