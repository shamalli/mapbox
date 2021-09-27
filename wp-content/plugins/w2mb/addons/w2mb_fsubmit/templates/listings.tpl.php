	<?php if ($frontend_controller->listings): ?>
		<table class="w2mb-table w2mb-dashboard-listings w2mb-table-striped">
			<tr>
				<th class="w2mb-td-listing-id"><?php esc_html_e('ID', 'W2MB'); ?></th>
				<th class="w2mb-td-listing-title"><?php esc_html_e('Title', 'W2MB'); ?></th>
				<?php 
				// adapted for WPML
				global $sitepress;
				if (function_exists('wpml_object_id_filter') && $sitepress && get_option('w2mb_enable_frontend_translations') && ($languages = $sitepress->get_active_languages()) && count($languages) > 1): ?>
				<th class="w2mb-td-listing-translations">
					<?php foreach ($languages AS $lang_code=>$lang): ?>
					<?php if ($lang_code != ICL_LANGUAGE_CODE && apply_filters('wpml_object_id', $w2mb_instance->dashboard_page_id, 'page', false, $lang_code)): ?>
					<img src="<?php echo $sitepress->get_flag_url($lang_code); ?>" title="<?php esc_attr_e($lang['native_name']); ?>" />&nbsp;&nbsp;
					<?php endif; ?>
					<?php endforeach; ?>
				</th>
				<?php endif; ?>
				<th class="w2mb-td-listing-status"><?php esc_html_e('Status', 'W2MB'); ?></th>
				<th class="w2mb-td-listing-expiration-date"><?php esc_html_e('Expiration date', 'W2MB'); ?></th>
				<th class="w2mb-td-listing-options"></th>
			</tr>
		<?php while ($frontend_controller->query->have_posts()): ?>
			<?php $frontend_controller->query->the_post(); ?>
			<?php $listing = $frontend_controller->listings[get_the_ID()]; ?>
			<tr>
				<td class="w2mb-td-listing-id"><?php echo $listing->post->ID; ?></td>
				<td class="w2mb-td-listing-title">
					<?php
					if (w2mb_current_user_can_edit_listing($listing->post->ID))
						echo '<a href="' . w2mb_get_edit_listing_link($listing->post->ID) . '">' . $listing->title() . '</a>';
					else
						echo $listing->title();
					do_action('w2mb_dashboard_listing_title', $listing);
					?>
					<?php if ($listing->post->post_status == 'pending') echo ' - ' . $listing->getPendingStatus(); ?>
					<?php if ($listing->post->post_status == 'draft') echo ' - ' . esc_html__('Draft or expired', 'W2MB'); ?>
					<?php if ($listing->claim && $listing->claim->isClaimed()) echo '<div>' . $listing->claim->getClaimMessage() . '</div>'; ?>
				</td>
				<?php 
				// adapted for WPML
				global $sitepress;
				if (function_exists('wpml_object_id_filter') && $sitepress && get_option('w2mb_enable_frontend_translations') && ($languages = $sitepress->get_active_languages()) && count($languages) > 1): ?>
				<td class="w2mb-td-listing-translations">
				<?php if (w2mb_current_user_can_edit_listing($listing->post->ID)):
					global $sitepress;
					$trid = $sitepress->get_element_trid($listing->post->ID, 'post_' . W2MB_POST_TYPE);
					$translations = $sitepress->get_element_translations($trid); ?>
					<?php foreach ($languages AS $lang_code=>$lang): ?>
					<?php if ($lang_code != ICL_LANGUAGE_CODE && apply_filters('wpml_object_id', $w2mb_instance->dashboard_page_id, 'page', false, $lang_code)): ?>
					<?php $lang_details = $sitepress->get_language_details($lang_code); ?>
					<?php do_action('wpml_switch_language', $lang_code); ?>
					<?php if (isset($translations[$lang_code])): ?>
					<a class="w2mb-decoration-none" title="<?php echo sprintf(esc_html__('Edit the %s translation', 'sitepress'), $lang_details['display_name']); ?>" href="<?php echo add_query_arg(array('w2mb_action' => 'edit_listing', 'listing_id' => apply_filters('wpml_object_id', $listing->post->ID, W2MB_POST_TYPE, true, $lang_code)), get_permalink(apply_filters('wpml_object_id', $w2mb_instance->dashboard_page_id, 'page', true, $lang_code))); ?>">
						<img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/edit_translation.png" alt="<?php esc_attr_e('edit translation', 'W2MB'); ?>" />
					</a>&nbsp;&nbsp;
					<?php else: ?>
					<a class="w2mb-decoration-none" title="<?php echo sprintf(esc_html__('Add translation to %s', 'sitepress'), $lang_details['display_name']); ?>" href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'add_translation', 'listing_id' => $listing->post->ID, 'to_lang' => $lang_code)); ?>">
						<img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/add_translation.png" alt="<?php esc_attr_e('add translation', 'W2MB'); ?>" />
					</a>&nbsp;&nbsp;
					<?php endif; ?>
					<?php endif; ?>
					<?php endforeach; ?>
					<?php do_action('wpml_switch_language', ICL_LANGUAGE_CODE); ?>
				<?php endif; ?>
				</td>
				<?php endif; ?>
				<td class="w2mb-td-listing-status">
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
				</td>
				<td class="w2mb-td-listing-expiration-date">
					<?php
					if ($listing->level->eternal_active_period)
						esc_html_e('Eternal active period', 'W2MB');
					else
						echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->expiration_date));
					
					if ($listing->expiration_date > time())
						echo '<br />' . human_time_diff(time(), $listing->expiration_date) . '&nbsp;' . esc_html__('left', 'W2MB');
					?>
				</td>
				<td class="w2mb-td-listing-options">
					<?php if (w2mb_current_user_can_edit_listing($listing->post->ID)): ?>
					<div class="w2mb-btn-group">
						<a href="<?php echo w2mb_get_edit_listing_link($listing->post->ID); ?>" class="w2mb-btn w2mb-btn-primary w2mb-btn-sm w2mb-dashboard-edit-btn" title="<?php esc_attr_e('edit listing', 'W2MB'); ?>"><span class="w2mb-glyphicon w2mb-glyphicon-edit"></span></a>
						<a href="<?php echo w2mb_get_edit_listing_link($listing->post->ID); ?>" class="w2mb-dashboard-btn-mobile"><?php esc_html_e('edit', 'W2MB'); ?></a>
						<a href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'delete_listing', 'listing_id' => $listing->post->ID)); ?>" class="w2mb-btn w2mb-btn-primary w2mb-btn-sm w2mb-dashboard-delete-btn" title="<?php esc_attr_e('delete listing', 'W2MB'); ?>"><span class="w2mb-glyphicon w2mb-glyphicon-trash"></span></a>
						<a href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'delete_listing', 'listing_id' => $listing->post->ID)); ?>" class="w2mb-dashboard-btn-mobile"><?php esc_html_e('delete', 'W2MB'); ?></a>
						<?php
						if ($listing->status == 'expired') {
							$renew_link = strip_tags(apply_filters('w2mb_renew_option', esc_html__('renew', 'W2MB'), $listing));
							echo '<a href="' . w2mb_dashboardUrl(array('w2mb_action' => 'renew_listing', 'listing_id' => $listing->post->ID)) . '" class="w2mb-btn w2mb-btn-primary w2mb-btn-sm w2mb-dashboard-renew-btn" title="' . esc_attr($renew_link) . '"><span class="w2mb-glyphicon w2mb-glyphicon-refresh"></span></a>';
							echo '<a href="' . w2mb_dashboardUrl(array('w2mb_action' => 'renew_listing', 'listing_id' => $listing->post->ID)) . '" class="w2mb-dashboard-btn-mobile">' . $renew_link . '</a>';
						}?>
						<?php
						if (get_option('w2mb_enable_stats')) {
							echo '<a href="' . w2mb_dashboardUrl(array('w2mb_action' => 'view_stats', 'listing_id' => $listing->post->ID)) . '" class="w2mb-btn w2mb-btn-primary w2mb-btn-sm w2mb-dashboard-stats-btn" title="' . esc_attr__('view clicks stats', 'W2MB') . '"><span class="w2mb-glyphicon w2mb-glyphicon-signal"></span></a>';
							echo '<a href="' . w2mb_dashboardUrl(array('w2mb_action' => 'view_stats', 'listing_id' => $listing->post->ID)) . '" class="w2mb-dashboard-btn-mobile">' . esc_html__('stats', 'W2MB') . '</a>';
						}?>
						<?php
						if ($listing->status == 'active' && $listing->post->post_status == 'publish' && ($permalink = get_permalink($listing->post->ID))) {
							echo '<a href="' . $permalink . '" class="w2mb-btn w2mb-btn-primary w2mb-btn-sm w2mb-dashboard-view-btn" title="' . esc_attr__('view listing', 'W2MB') . '"><span class="w2mb-glyphicon w2mb-glyphicon-link"></span></a>';
							echo '<a href="' . $permalink . '" class="w2mb-dashboard-btn-mobile">' . esc_html__('view', 'W2MB') . '</a>';
						}?>
						<?php do_action('w2mb_dashboard_listing_options', $listing); ?>
					</div>
					<?php endif; ?>
				</td>
			</tr>
		<?php endwhile; ?>
		</table>
		<?php w2mb_renderPaginator($frontend_controller->query); ?>
		<?php endif; ?>