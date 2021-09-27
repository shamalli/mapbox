<h3>
	<?php printf(esc_html__('Approve or decline claim of listing "%s"', 'W2MB'), $w2mb_instance->current_listing->title()); ?>
</h3>

<?php if ($frontend_controller->action == 'show'): ?>
<p><?php printf(esc_html__('User "%s" had claimed this listing.', 'W2MB'), $w2mb_instance->current_listing->claim->claimer->display_name); ?></p>
<?php if ($w2mb_instance->current_listing->claim->claimer_message): ?>
<p><?php esc_html_e('Message from claimer:', 'W2MB'); ?><br /><i><?php echo $w2mb_instance->current_listing->claim->claimer_message; ?></i></p>
<?php endif; ?>
<p><?php esc_html_e('Claimer will receive email notification.', 'W2MB'); ?></p>

<a href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'process_claim', 'listing_id' => $w2mb_instance->current_listing->post->ID, 'claim_action' => 'approve', 'referer' => urlencode($frontend_controller->referer))); ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Approve', 'W2MB'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'process_claim', 'listing_id' => $w2mb_instance->current_listing->post->ID, 'claim_action' => 'decline', 'referer' => urlencode($frontend_controller->referer))); ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Decline', 'W2MB'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Cancel', 'W2MB'); ?></a>
<?php elseif ($frontend_controller->action == 'processed'): ?>
<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Go back ', 'W2MB'); ?></a>
<?php endif; ?>