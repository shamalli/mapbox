		<div class="w2mb-content w2mb-listing-single">
			<?php w2mb_renderMessages(); ?>

			<?php if ($frontend_controller->listings): ?>
			<?php while ($frontend_controller->query->have_posts()): ?>
				<?php $frontend_controller->query->the_post(); ?>
				<?php $listing = $frontend_controller->listings[get_the_ID()]; ?>

				<div id="<?php echo esc_attr($listing->post->post_name); ?>" itemscope itemtype="http://schema.org/LocalBusiness">
					<meta itemprop="priceRange" content="$$$" />
					<?php $hide_button_text = apply_filters('w2mb_hide_button_text_on_listing', true)?>
					<?php $frontpanel_buttons = new w2mb_frontpanel_buttons(array('listing_id' => $listing->post->ID, 'hide_button_text' => $hide_button_text)); ?>
					<?php $frontpanel_buttons->display(); ?>
				
					<?php if ($listing->title()): ?>
					<header class="w2mb-listing-header">
						<?php do_action('w2mb_listing_title_html', $listing, true); ?>
						<?php if (!get_option('w2mb_hide_views_counter')): ?>
						<div class="w2mb-meta-data">
							<div class="w2mb-views-counter">
								<span class="w2mb-glyphicon w2mb-glyphicon-eye-open"></span> <?php esc_html_e('views', 'W2MB')?>: <?php echo get_post_meta($listing->post->ID, '_total_clicks', true); ?>
							</div>
						</div>
						<?php endif; ?>
						<?php if (!get_option('w2mb_hide_listings_creation_date')): ?>
						<div class="w2mb-meta-data">
							<div class="w2mb-listing-date" datetime="<?php echo date("Y-m-d", mysql2date('U', $listing->post->post_date)); ?>T<?php echo date("H:i", mysql2date('U', $listing->post->post_date)); ?>"><?php echo get_the_date(); ?> <?php echo get_the_time(); ?></div>
						</div>
						<?php endif; ?>
						<?php if (!get_option('w2mb_hide_author_link')): ?>
						<div class="w2mb-meta-data">
							<div class="w2mb-author-link">
								<?php esc_html_e('By', 'W2MB'); ?> <?php echo get_the_author_link(); ?>
							</div>
						</div>
						<?php endif; ?>
					</header>
					<?php endif; ?>

					<article id="post-<?php the_ID(); ?>" class="w2mb-listing">
						<?php if ($listing->logo_image && (!get_option('w2mb_exclude_logo_from_listing') || count($listing->images) > 1)): ?>
						<div class="w2mb-listing-logo-wrap w2mb-single-listing-logo-wrap" id="images">
							<?php do_action('w2mb_listing_pre_logo_wrap_html', $listing); ?>
							<meta itemprop="image" content="<?php echo $listing->get_logo_url(); ?>" />
							
							<?php $listing->renderImagesGallery(); ?>
						</div>
						<?php endif; ?>

						<div class="w2mb-single-listing-text-content-wrap">
							<?php do_action('w2mb_listing_pre_content_html', $listing); ?>
					
							<?php $listing->renderContentFields(true); ?>

							<?php do_action('w2mb_listing_post_content_html', $listing); ?>
						</div>

						<?php if (
							($fields_groups = $listing->getFieldsGroupsOnTabs())
							|| (get_option('w2mb_enable_map_listing') && $listing->isMap())
							|| (w2mb_comments_open())
							|| ($listing->level->videos_number && $listing->videos)
							|| ($listing->isContactForm())
							|| (get_option('w2mb_report_form'))
							): ?>
						<ul class="w2mb-listing-tabs w2mb-nav w2mb-nav-tabs w2mb-clearfix" role="tablist">
							<?php if (get_option('w2mb_enable_map_listing') && $listing->isMap()): ?>
							<li><a href="javascript: void(0);" data-tab="#addresses-tab" data-toggle="w2mb-tab" role="tab"><?php esc_html_e('Map', 'W2MB'); ?></a></li>
							<?php endif; ?>
							<?php if (w2mb_comments_open()): ?>
							<li><a href="javascript: void(0);" data-tab="#comments-tab" data-toggle="w2mb-tab" role="tab"><?php echo _n('Comment', 'Comments', $listing->post->comment_count, 'W2MB'); ?> (<?php echo $listing->post->comment_count; ?>)</a></li>
							<?php endif; ?>
							<?php if ($listing->level->videos_number && $listing->videos): ?>
							<li><a href="javascript: void(0);" data-tab="#videos-tab" data-toggle="w2mb-tab" role="tab"><?php echo _n('Video', 'Videos', count($listing->videos), 'W2MB'); ?> (<?php echo count($listing->videos); ?>)</a></li>
							<?php endif; ?>
							<?php if ($listing->isContactForm()): ?>
							<li><a href="javascript: void(0);" data-tab="#contact-tab" data-toggle="w2mb-tab" role="tab"><?php esc_html_e('Contact', 'W2MB'); ?></a></li>
							<?php endif; ?>
							<?php if (get_option('w2mb_report_form')): ?>
							<li><a href="javascript: void(0);" data-tab="#report-tab" data-toggle="w2mb-tab" role="tab"><?php esc_html_e('Report', 'W2MB'); ?></a></li>
							<?php endif; ?>
							<?php
							foreach ($fields_groups AS $fields_group): ?>
							<li><a href="javascript: void(0);" data-tab="#field-group-tab-<?php echo esc_attr($fields_group->id); ?>" data-toggle="w2mb-tab" role="tab"><?php echo $fields_group->name; ?></a></li>
							<?php endforeach; ?>
							<?php do_action('w2mb_listing_single_tabs', $listing); ?>
						</ul>

						<div class="w2mb-tab-content">
							<?php if (get_option('w2mb_enable_map_listing') && $listing->isMap()): ?>
							<div id="addresses-tab" class="w2mb-tab-pane w2mb-fade" role="tabpanel">
								<?php $listing->renderMap($frontend_controller->hash); ?>
							</div>
							<?php endif; ?>

							<?php if (w2mb_comments_open()): ?>
							<div id="comments-tab" class="w2mb-tab-pane w2mb-fade" role="tabpanel">
								<?php
								global $withcomments;
								$withcomments = true;
								comments_template('', true);
								?>
							</div>
							<?php endif; ?>

							<?php if ($listing->level->videos_number && $listing->videos): ?>
							<div id="videos-tab" class="w2mb-tab-pane w2mb-fade" role="tabpanel">
							<?php foreach ($listing->videos AS $video): ?>
								<?php if (strlen($video['id']) == 11): ?>
								<iframe width="100%" height="400" class="w2mb-video-iframe fitvidsignore" src="//www.youtube.com/embed/<?php echo $video['id']; ?>" frameborder="0" allowfullscreen></iframe>
								<?php elseif (strlen($video['id']) == 9): ?>
								<iframe width="100%" height="400" class="w2mb-video-iframe fitvidsignore" src="https://player.vimeo.com/video/<?php echo $video['id']; ?>?color=d1d1d1&title=0&byline=0&portrait=0" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								<?php endif; ?>
							<?php endforeach; ?>
							</div>
							<?php endif; ?>

							<?php if ($listing->isContactForm()): ?>
							<div id="contact-tab" class="w2mb-tab-pane w2mb-fade" role="tabpanel">
							<?php if (!get_option('w2mb_hide_anonymous_contact_form') || is_user_logged_in()): ?>
								<?php if (defined('WPCF7_VERSION') && w2mb_get_wpml_dependent_option('w2mb_listing_contact_form_7')): ?>
									<?php echo do_shortcode(w2mb_get_wpml_dependent_option('w2mb_listing_contact_form_7')); ?>
								<?php else: ?>
									<?php w2mb_renderTemplate('frontend/contact_form.tpl.php', array('listing' => $listing)); ?>
								<?php endif; ?>
							<?php else: ?>
								<?php printf(esc_html__('You must be <a href="%s">logged in</a> to submit contact form', 'W2MB'), wp_login_url(get_permalink($listing->post->ID))); ?>
							<?php endif; ?>
							</div>
							<?php endif; ?>
							
							<?php if (get_option('w2mb_report_form')): ?>
							<div id="report-tab" class="w2mb-tab-pane w2mb-fade" role="tabpanel">
								<?php w2mb_renderTemplate('frontend/report_form.tpl.php', array('listing' => $listing)); ?>
							</div>
							<?php endif; ?>
							
							<?php foreach ($fields_groups AS $fields_group): ?>
							<div id="field-group-tab-<?php echo esc_attr($fields_group->id); ?>" class="w2mb-tab-pane w2mb-fade" role="tabpanel">
								<?php echo $fields_group->renderOutput($listing, true); ?>
							</div>
							<?php endforeach; ?>
							
							<?php do_action('w2mb_listing_single_tabs_content', $listing); ?>
						</div>
						<?php endif; ?>
					</article>
				</div>
			<?php endwhile; endif; ?>
		</div>