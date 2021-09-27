<article class="w2mb-listing-location w2mb-listing-has-location-<?php echo esc_attr($location->id); ?>" id="post-<?php echo esc_attr($location->id); ?>" data-location-id="<?php echo esc_attr($location->id); ?>">
	<div class="w2mb-listing-location-content">
		<?php
		if ($img_src = $listing->getSidebarImage()):
		?>
		<div class="w2mb-map-listing-logo-wrap">
			<figure class="w2mb-map-listing-logo">
				<div class="w2mb-map-listing-logo-img-wrap">
					<div style="background-image: url('<?php echo $img_src; ?>');" class="w2mb-map-listing-logo-img">
						<img src="<?php echo $img_src; ?>" />
					</div>
				</div>
			</figure>
		</div>
		<?php endif; ?>
		<div class="w2mb-map-listing-content-wrap">
			<header class="w2mb-map-listing-header">
				<h2><?php echo $listing->title(); ?> <?php do_action('w2mb_listing_title_html', $listing, false); ?></h2>
			</header>
			<?php $listing->renderMapSidebarContentFields($location); ?>
		</div>
	</div>
	<?php 
		if ($show_directions_button || $show_readmore_button):
			if (!$show_directions_button || !$show_readmore_button) {
				$buttons_class = 'w2mb-map-info-window-buttons-single';
			} else {
				$buttons_class = 'w2mb-map-info-window-buttons';
			}
	?>
	<div class="<?php echo esc_attr($buttons_class); ?> w2mb-clearfix">
		<?php if ($show_directions_button): ?>
			<a href="javascript:void(0);" onClick="w2mb_open_directions(<?php echo esc_attr($location->id); ?>, '<?php echo esc_attr($map_id); ?>')" class="w2mb-btn w2mb-btn-primary w2mb-toggle-directions-panel"><?php esc_html_e('« Directions', 'W2MB'); ?></a>
		<?php endif; ?>
		<?php if ($show_readmore_button): ?>
		<a href="#<?php esc_html_e('w2mb-listing', 'W2MB'); ?>-<?php echo esc_attr($listing->post->ID); ?>" onClick="w2mb_show_listing(<?php echo esc_attr($listing->post->ID); ?>, '<?php echo esc_attr($listing->title()); ?>')" class="w2mb-btn w2mb-btn-primary w2mb-open-listing-window"><?php esc_html_e('Read more »', 'W2MB')?></a>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</article>