<h3>
	<?php echo sprintf(esc_html__('Delete listing "%s"', 'W2MB'), $w2mb_instance->current_listing->title()); ?>
</h3>

<p><?php esc_html_e('Listing will be completely deleted with all metadata, comments and attachments.', 'W2MB'); ?></p>

<a href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'delete_listing', 'listing_id' => $w2mb_instance->current_listing->post->ID, 'delete_action' => 'delete', 'referer' => urlencode($frontend_controller->referer))); ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Delete listing', 'W2MB'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Cancel', 'W2MB'); ?></a>