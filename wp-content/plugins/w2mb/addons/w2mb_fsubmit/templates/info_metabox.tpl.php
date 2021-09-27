		<div class="w2mb-edit-listing-info w2mb-col-md-3">
			<div class="w2mb-submit-section w2mb-submit-section-locations">
				<h3 class="w2mb-submit-section-label"><?php esc_html_e('Listing info', 'W2MB'); ?></h3>
				<div class="w2mb-submit-section-inside">
					<div class="w2mb-edit-listing-info-label">
						<label><?php esc_html_e('Listing status', 'W2MB'); ?>:</label>
						<?php
						if ($listing->status == 'active')
							echo '<span class="w2mb-badge w2mb-listing-status-active">' . esc_html__('active', 'W2MB') . '</span>';
						elseif ($listing->status == 'expired')
							echo '<span class="w2mb-badge w2mb-listing-status-expired">' . esc_html__('expired', 'W2MB') . '</span>';
						elseif ($listing->status == 'unpaid')
							echo '<span class="w2mb-badge w2mb-listing-status-unpaid">' . esc_html__('unpaid', 'W2MB') . '</span>';
						elseif ($listing->status == 'stopped')
							echo '<span class="w2mb-badge w2mb-listing-status-stopped">' . esc_html__('stopped', 'W2MB') . '</span>';
						do_action('w2mb_listing_status_option', $listing);
						?>
					</div>
					<?php if ($listing->post->post_status == 'pending' || $listing->post->post_status == 'draft'): ?>
					<div class="w2mb-edit-listing-info-label">
						<label><?php esc_html_e('Post status', 'W2MB'); ?>:</label>
						<?php if ($listing->post->post_status == 'pending') echo $listing->getPendingStatus(); ?>
						<?php if ($listing->post->post_status == 'draft') echo  esc_html__('Draft or expired', 'W2MB'); ?>
					</div>
					<?php endif; ?>
					<?php if (get_option('w2mb_enable_stats')): ?>
					<div class="w2mb-edit-listing-info-label">
						<label><?php echo sprintf(esc_html__('Click stats: %d', 'W2MB'), (get_post_meta($listing->post->ID, '_total_clicks', true) ? get_post_meta($listing->post->ID, '_total_clicks', true) : 0)); ?></label>
					</div>
					<?php endif; ?>
					<div class="w2mb-edit-listing-info-label">
						<label><?php esc_html_e('Sorting date', 'W2MB'); ?>:</label>
						<b><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->order_date)); ?></b>
					</div>
					<?php if ($listing->status == 'active' || $listing->status == 'expired'): ?>
					<div class="w2mb-edit-listing-info-label">
						<label><?php esc_html_e('Expire on', 'W2MB'); ?>:</label>
						<?php if ($listing->status == 'expired'): 
							$renew_link = strip_tags(apply_filters('w2mb_renew_option', esc_html__('renew', 'W2MB'), $listing));
							echo '<a href="' . w2mb_dashboardUrl(array('w2mb_action' => 'renew_listing', 'listing_id' => $listing->post->ID)) . '" title="' . esc_attr($renew_link) . '"><span class="w2mb-glyphicon w2mb-glyphicon-refresh"></span> ' . $renew_link . '</a>';
						elseif ($listing->status == 'active'): 
							if ($listing->level->eternal_active_period): ?>
							<b><?php esc_html_e('Eternal active period', 'W2MB'); ?></b>
							<?php else: ?>
							<b><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->expiration_date)); ?></b>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php if ($listing->claim && $listing->claim->isClaimed()): ?>
					<div class="w2mb-edit-listing-info-label">
						<?php echo '<div>' . $listing->claim->getClaimMessage() . '</div>'; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>