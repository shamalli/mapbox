<?php if ($content_field->value['url']): ?>
<div class="w2mb-field w2mb-field-output-block w2mb-field-output-block-<?php echo esc_attr($content_field->type); ?> w2mb-field-output-block-<?php echo esc_attr($content_field->id); ?>">
	<?php if ($content_field->icon_image || !$content_field->is_hide_name): ?>
	<span class="w2mb-field-caption <?php w2mb_is_any_field_name_in_group($group); ?>">
		<?php if ($content_field->icon_image): ?>
		<span class="w2mb-field-icon w2mb-fa w2mb-fa-lg <?php echo esc_attr($content_field->icon_image); ?>"></span>
		<?php endif; ?>
		<?php if (!$content_field->is_hide_name): ?>
		<span class="w2mb-field-name"><?php echo $content_field->name; ?>:</span>
		<?php endif; ?>
	</span>
	<?php endif; ?>
	<span class="w2mb-field-content">
		<a itemprop="url"
			href="<?php echo esc_url($content_field->value['url']); ?>"
			<?php if ($content_field->is_blank) echo 'target="_blank"'; ?>
			<?php if ($content_field->is_nofollow) echo 'rel="nofollow"'; ?>
		><?php if ($content_field->value['text'] && $content_field->use_link_text) echo $content_field->value['text']; else echo $content_field->value['url']; ?></a>
	</span>
</div>
<?php endif; ?>