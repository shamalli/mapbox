<div id="misc-publishing-actions">
	<?php if ($listing->listing_created): ?>
	<div class="misc-pub-section">
		<label for="post_level"><?php esc_html_e('Listing status', 'W2MB'); ?>:</label>
		<span id="post-level-display">
			<?php if ($listing->status == 'active'): ?>
			<span class="w2mb-badge w2mb-listing-status-active"><?php esc_html_e('active', 'W2MB'); ?></span>
			<?php elseif ($listing->status == 'expired' && get_option('w2mb_enable_renew')): ?>
			<span class="w2mb-badge w2mb-listing-status-expired"><?php esc_html_e('expired', 'W2MB'); ?></span><br />
			<a href="<?php echo admin_url('options.php?page=w2mb_renew&listing_id=' . $listing->post->ID); ?>"><span class="w2mb-fa w2mb-fa-refresh w2mb-fa-lg"></span> <?php echo apply_filters('w2mb_renew_option', esc_html__('renew listing', 'W2MB'), $listing); ?></a>
			<?php elseif ($listing->status == 'unpaid'): ?>
			<span class="w2mb-badge w2mb-listing-status-unpaid"><?php esc_html_e('unpaid ', 'W2MB'); ?></span>
			<?php elseif ($listing->status == 'stopped'): ?>
			<span class="w2mb-badge w2mb-listing-status-stopped"><?php esc_html_e('stopped', 'W2MB'); ?></span>
			<?php endif;?>
			<?php do_action('w2mb_listing_status_option', $listing); ?>
		</span>
		<?php if (get_post_meta($listing->post->ID, '_preexpiration_notification_sent', true)): ?><br /><?php esc_html_e('Pre-expiration notification was sent', 'W2MB'); ?><?php endif; ?>
	</div>
	
	<?php
	$post_type_object = get_post_type_object(W2MB_POST_TYPE);
	$can_publish = current_user_can($post_type_object->cap->publish_posts);
	?>
	<?php if ($can_publish && $listing->status != 'active'): ?>
	<div class="misc-pub-section">
		<input name="w2mb_save_as_active" value="Save as Active" class="button" type="submit">
	</div>
	<?php endif; ?>

	<?php if (get_option('w2mb_enable_stats')): ?>
	<div class="misc-pub-section">
		<label for="post_level"><?php echo sprintf(esc_html__('Total clicks: %d', 'W2MB'), (get_post_meta($w2mb_instance->current_listing->post->ID, '_total_clicks', true) ? get_post_meta($w2mb_instance->current_listing->post->ID, '_total_clicks', true) : 0)); ?></label>
	</div>
	<?php endif; ?>

	<div class="misc-pub-section curtime">
		<span id="timestamp">
			<?php esc_html_e('Sorting date', 'W2MB'); ?>:
			<b><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->order_date)); ?></b>
		</span>
	</div>

	<?php if ($listing->level->eternal_active_period || $listing->expiration_date): ?>
	<div class="misc-pub-section curtime">
		<span id="timestamp">
			<?php esc_html_e('Expire on', 'W2MB'); ?>:
			<?php if ($listing->level->eternal_active_period): ?>
			<b><?php esc_html_e('Eternal active period', 'W2MB'); ?></b>
			<?php else: ?>
			<b><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->expiration_date)); ?></b>
			<?php endif; ?>
		</span>
	</div>
	<?php endif; ?>
	
	<?php do_action('w2mb_listing_info_metabox_html', $listing); ?>

	<?php endif; ?>
</div>