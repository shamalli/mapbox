<?php if ($listing->locations): ?>
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
	<span class="w2mb-field-content w2mb-field-addresses">
	<?php foreach ($listing->locations AS $location): ?>
		<address class="w2mb-location" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<?php if ($location->map_coords_1 && $location->map_coords_2): ?><span class="w2mb-show-on-map" data-location-id="<?php echo esc_attr($location->id); ?>"><?php endif; ?>
			<?php echo $location->getWholeAddress(); ?>
			<?php if ($location->renderInfoFieldForMap()) echo '<div class="w2mb-location-additional-info">' . $location->renderInfoFieldForMap() . '</div>'; ?>
			<?php if ($location->map_coords_1 && $location->map_coords_2): ?></span><?php endif; ?>
		</address>
	<?php endforeach; ?>
	</span>
</div>
<?php endif; ?>