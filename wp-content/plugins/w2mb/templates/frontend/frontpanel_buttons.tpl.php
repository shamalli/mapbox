<div class="w2mb-content w2mb-listing-frontpanel">
	<?php do_action('w2mb_listing_frontpanel', $frontpanel_buttons); ?>
	<?php if ($frontpanel_buttons->isEditButton()): ?>
	<a class="w2mb-edit-listing-link w2mb-btn w2mb-btn-primary" href="<?php echo w2mb_get_edit_listing_link($frontpanel_buttons->getListingId()); ?>" rel="nofollow" <?php $frontpanel_buttons->tooltipMeta(esc_html__('Edit listing', 'W2MB')); ?>><span class="w2mb-glyphicon w2mb-glyphicon-pencil"></span><?php if (!$frontpanel_buttons->hide_button_text): ?> <?php esc_html_e('Edit listing', 'W2MB'); ?><?php endif; ?></a>
	<?php endif; ?>
	<?php do_action('w2mb_listing_frontpanel_after', $frontpanel_buttons); ?>
</div>