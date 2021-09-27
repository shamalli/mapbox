<?php if (esc_attr($content_field->value)): ?>
<div class="w2mb-field w2mb-field-output-block w2mb-field-output-block-<?php echo esc_attr($content_field->type); ?> w2mb-field-output-block-<?php echo esc_attr($content_field->id); ?>">
	<?php if ($content_field->icon_image || !$content_field->is_hide_name): ?>
	<span class="w2mb-field-caption <?php w2mb_is_any_field_name_in_group($group); ?> w2mb-field-phone-caption">
		<?php if ($content_field->icon_image): ?>
		<span class="w2mb-field-icon w2mb-fa w2mb-fa-lg <?php echo esc_attr($content_field->icon_image); ?>"></span>
		<?php endif; ?>
		<?php if (!$content_field->is_hide_name): ?>
		<span class="w2mb-field-name"><?php echo $content_field->name; ?>:</span>
		<?php endif; ?>
	</span>
	<?php endif; ?>
	<span class="w2mb-field-content w2mb-field-phone-content">
		<?php if ($content_field->phone_mode == 'phone'): ?>
		<meta itemprop="telephone" content="<?php echo esc_attr($content_field->value); ?>" />
		<a href="tel:<?php echo esc_attr($content_field->value); ?>"><?php echo antispambot(esc_attr($content_field->value)); ?></a>
		<?php elseif ($content_field->phone_mode == 'viber'): ?>
		<a href="viber://chat?number=<?php echo esc_attr($content_field->value); ?>"><?php echo antispambot(esc_attr($content_field->value)); ?></a>
		<?php elseif ($content_field->phone_mode == 'whatsapp'): ?>
		<a href="https://wa.me/<?php echo esc_attr($content_field->value); ?>"><?php echo antispambot(esc_attr($content_field->value)); ?></a>
		<?php elseif ($content_field->phone_mode == 'telegram'): ?>
		<a href="tg://resolve?domain=<?php echo esc_attr($content_field->value); ?>"><?php echo antispambot(esc_attr($content_field->value)); ?></a>
		<?php endif; ?>
	</span>
</div>
<?php endif; ?>