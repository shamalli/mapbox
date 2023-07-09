<div class="w2mb-map-info-window">
	<div class="w2mb-map-info-window-title">
		<a class="w2mb-map-info-window-title-link" href="#<?php echo w2mb_get_listing_anchor().'-'.$listing->post->ID ?>" onClick="w2mb_show_listing(&quot;<?php echo $listing->post->ID; ?>&quot;, &quot;<?php echo $listing->title(); ?>&quot;)">
			<?php echo $listing->title(); ?>
		</a>
		<span class="w2mb-close-info-window w2mb-fa w2mb-fa-close" onclick="w2mb_closeInfoWindow(&quot;<?php echo esc_attr($map_id); ?>&quot;);"></span>
	</div>
	<?php if ($logo_image): ?>
	<div class="w2mb-map-info-window-logo" style="width: <?php echo get_option('w2mb_map_infowindow_logo_width')+10; ?>px">
		<?php if ($listing->level->listings_own_page): ?>
		<a href="<?php echo get_permalink($listing->post->ID); ?>">
			<img src="<?php echo esc_attr($logo_image); ?>" width="<?php echo get_option('w2mb_map_infowindow_logo_width'); ?>px">
		</a>
		<?php else: ?>
		<img src="<?php echo esc_attr($logo_image); ?>" width="<?php echo get_option('w2mb_map_infowindow_logo_width'); ?>px">
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<?php if ($content_fields_output): ?>
	<div class="w2mb-map-info-window-content w2mb-clearfix">
		<?php foreach ($content_fields_output AS $field_slug=>$field_content): ?>
		<?php if ($field_content): ?>
		<div class="w2mb-map-info-window-field">
			<?php if (!empty($map_content_fields_icons[$field_slug])): ?>
			<span class="w2mb-map-field-icon w2mb-fa <?php echo esc_attr($map_content_fields_icons[$field_slug]); ?>"></span>
			<?php endif; ?>
			<?php echo $field_content; ?>
		</div>
		<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ($show_directions_button || $show_readmore_button): ?>
	<?php
	if (!$show_directions_button || !$show_readmore_button) {
		$button_class = 'w2mb-map-info-window-buttons-single';
	} else {
		$button_class = 'w2mb-map-info-window-buttons';
	}
	?>
	<div class="<?php echo $button_class; ?> w2mb-clearfix">
		<?php if ($show_directions_button): ?>
		<a href="javascript:void(0);" class="w2mb-btn w2mb-btn-primary w2mb-toggle-directions-panel" onClick="w2mb_open_directions(&quot;<?php echo esc_attr($location_id); ?>&quot;, &quot;<?php echo esc_attr($map_id); ?>&quot;)"><?php esc_html_e('« Directions', 'W2MB')?></a>
		<?php endif; ?>
		<?php if ($show_readmore_button): ?>
		<a class="w2mb-btn w2mb-btn-primary w2mb-open-listing-window" href="#<?php echo w2mb_get_listing_anchor().'-'.$listing->post->ID ?>" onClick="w2mb_show_listing(&quot;<?php echo $listing->post->ID; ?>&quot;, &quot;<?php echo $listing->title(); ?>&quot;)"><?php esc_html_e('Read more »', 'W2MB'); ?></a>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>