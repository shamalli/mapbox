<?php $listing = $w2mb_instance->current_listing; ?>
<div class="w2mb-content">
	<?php w2mb_renderMessages(); ?>

	<h2><?php echo sprintf(esc_html__('Edit listing "%s"', 'W2MB'), $listing->title()); ?></h2>

	<div class="w2mb-edit-listing-wrapper w2mb-row">
		<?php if ($listing_info): ?>
		<?php w2mb_renderTemplate(array(W2MB_FSUBMIT_TEMPLATES_PATH, 'info_metabox.tpl.php'), array('listing' => $listing)); ?>
		<?php endif; ?>
		<div class="w2mb-edit-listing-form w2mb-col-md-<?php echo ($listing_info) ? 9: 12; ?>">
			<form action="" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="referer" value="<?php echo $frontend_controller->referer; ?>" />
				<?php wp_nonce_field('w2mb_submit', '_submit_nonce'); ?>
		
				<div class="w2mb-submit-section w2mb-submit-section-title">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('Listing title', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></h3>
					<div class="w2mb-submit-section-inside">
						<input type="text" name="post_title" class="w2mb-form-control w2mb-width-100" value="<?php if ($listing->post->post_title != esc_html__('Auto Draft')) echo esc_attr($listing->post->post_title); ?>" />
					</div>
				</div>
				
				<?php if ($listing->level->locations_number > 0): ?>
				<div class="w2mb-submit-section w2mb-submit-section-locations">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('Listing locations', 'W2MB'); ?><?php if ($w2mb_instance->content_fields->getContentFieldBySlug('address')->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?></h3>
					<div class="w2mb-submit-section-inside">
						<?php if ($w2mb_instance->content_fields->getContentFieldBySlug('address')->description): ?><p class="w2mb-description"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('address')->description; ?></p><?php endif; ?>
						<?php $w2mb_instance->locations_manager->listingLocationsMetabox($listing->post); ?>
					</div>
				</div>
				<?php endif; ?>
		
				<?php if (post_type_supports(W2MB_POST_TYPE, 'editor')): ?>
				<div class="w2mb-submit-section w2mb-submit-section-description">
					<h3 class="w2mb-submit-section-label"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('content')->name; ?><?php if ($w2mb_instance->content_fields->getContentFieldBySlug('content')->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?></h3>
					<div class="w2mb-submit-section-inside">
						<?php wp_editor($listing->post->post_content, 'post_content', array('media_buttons' => false, 'editor_class' => 'w2mb-editor-class')); ?>
						<?php if ($w2mb_instance->content_fields->getContentFieldBySlug('content')->description): ?><p class="w2mb-description"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('content')->description; ?></p><?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
		
				<?php if (post_type_supports(W2MB_POST_TYPE, 'excerpt')): ?>
				<div class="w2mb-submit-section w2mb-submit-section- excerpt">
					<h3 class="w2mb-submit-section-label"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('summary')->name; ?><?php if ($w2mb_instance->content_fields->getContentFieldBySlug('summary')->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?></h3>
					<div class="w2mb-submit-section-inside">
						<textarea name="post_excerpt" class="w2mb-form-control" rows="4"><?php echo esc_textarea($listing->post->post_excerpt)?></textarea>
						<?php if ($w2mb_instance->content_fields->getContentFieldBySlug('summary')->description): ?><p class="w2mb-description"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('summary')->description; ?></p><?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
				
				<?php do_action('w2mb_edit_listing_metaboxes_pre', $listing); ?>
		
				<?php if (!$listing->level->eternal_active_period && (get_option('w2mb_change_expiration_date') || current_user_can('manage_options'))): ?>
				<div class="w2mb-submit-section w2mb-submit-section-expiration-date">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('Listing expiration date', 'W2MB'); ?></h3>
					<div class="w2mb-submit-section-inside">
						<?php $w2mb_instance->listings_manager->listingExpirationDateMetabox($listing->post); ?>
					</div>
				</div>
				<?php endif; ?>
				
				<?php if (get_option('w2mb_listing_contact_form') && get_option('w2mb_custom_contact_email')): ?>
				<div class="w2mb-submit-section w2mb-submit-section-contact-email">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('Contact email', 'W2MB'); ?></h3>
					<div class="w2mb-submit-section-inside">
						<?php $w2mb_instance->listings_manager->listingContactEmailMetabox($listing->post); ?>
					</div>
				</div>
				<?php endif; ?>
		
				<?php if (get_option('w2mb_claim_functionality') && !get_option('w2mb_hide_claim_metabox')): ?>
				<div class="w2mb-submit-section w2mb-submit-section-claim">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('Listing claim', 'W2MB'); ?></h3>
					<div class="w2mb-submit-section-inside">
						<?php $w2mb_instance->listings_manager->listingClaimMetabox($listing->post); ?>
					</div>
				</div>
				<?php endif; ?>
			
				<?php if ($listing->level->categories_number > 0 || $listing->level->unlimited_categories): ?>
				<div class="w2mb-submit-section w2mb-submit-section-categories">
					<h3 class="w2mb-submit-section-label"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('categories_list')->name; ?><?php if ($w2mb_instance->content_fields->getContentFieldBySlug('categories_list')->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?></h3>
					<div class="w2mb-submit-section-inside">
						<a href="javascript:void(0);" class="w2mb-expand-terms"><?php esc_html_e('Expand All', 'W2MB'); ?></a> | <a href="javascript:void(0);" class="w2mb-collapse-terms"><?php esc_html_e('Collapse All', 'W2MB'); ?></a>
						<div class="w2mb-categories-tree-panel w2mb-editor-class" id="<?php echo W2MB_CATEGORIES_TAX; ?>-all">
							<?php w2mb_terms_checklist($listing->post->ID); ?>
							<?php if ($w2mb_instance->content_fields->getContentFieldBySlug('categories_list')->description): ?><p class="w2mb-description"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('categories_list')->description; ?></p><?php endif; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
		
				<?php if (get_option('w2mb_enable_tags')): ?>
				<div class="w2mb-submit-section w2mb-submit-section-tags">
					<h3 class="w2mb-submit-section-label"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('listing_tags')->name; ?> <i>(<?php esc_html_e('select existing or type new', 'W2MB'); ?>)</i></h3>
					<div class="w2mb-submit-section-inside">
						<?php w2mb_tags_selectbox($listing->post->ID); ?>
						<?php if ($w2mb_instance->content_fields->getContentFieldBySlug('listing_tags')->description): ?><p class="w2mb-description"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('listing_tags')->description; ?></p><?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
			
				<?php if ($w2mb_instance->content_fields->getNotCoreContentFields()): ?>
				<?php $w2mb_instance->content_fields->renderInputByGroups($listing->post); ?>
				<?php endif; ?>
			
				<?php if ($listing->level->images_number > 0 || $listing->level->videos_number > 0): ?>
				<div class="w2mb-submit-section w2mb-submit-section-media">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('Listing Media', 'W2MB'); ?></h3>
					<div class="w2mb-submit-section-inside">
						<?php $w2mb_instance->media_manager->mediaMetabox($listing->post, array('args' => array('target' => 'listings'))); ?>
					</div>
				</div>
				<?php endif; ?>
				
				<?php do_action('w2mb_edit_listing_metaboxes_post', $listing); ?>
		
				<?php require_once(ABSPATH . 'wp-admin/includes/template.php'); ?>
				<?php submit_button(esc_html__('Save changes', 'W2MB'), 'w2mb-btn w2mb-btn-primary', 'submit', false); ?>
				&nbsp;&nbsp;&nbsp;
				<?php submit_button(esc_html__('Cancel', 'W2MB'), 'w2mb-btn w2mb-btn-primary', 'cancel', false); ?>
			</form>
		</div>
	</div>
</div>