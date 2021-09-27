<h3>
	<?php echo apply_filters('w2mb_renew_option', sprintf(esc_html__('Renew listing "%s"', 'W2MB'), $w2mb_instance->current_listing->title()), $w2mb_instance->current_listing); ?>
</h3>

<p><?php esc_html_e('Listing will be renewed and raised up.', 'W2MB'); ?></p>

<?php do_action('w2mb_renew_html', $w2mb_instance->current_listing); ?>

<?php if ($frontend_controller->action == 'show'): ?>
<a href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'renew_listing', 'listing_id' => $w2mb_instance->current_listing->post->ID, 'renew_action' => 'renew', 'referer' => urlencode($frontend_controller->referer))); ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Renew listing', 'W2MB'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Cancel', 'W2MB'); ?></a>
<?php elseif ($frontend_controller->action == 'renew'): ?>
<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Go back ', 'W2MB'); ?></a>
<?php endif; ?>