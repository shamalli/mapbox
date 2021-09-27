<?php $listing = $w2mb_instance->current_listing; ?>
<div class="w2mb-content">
	<?php w2mb_renderMessages(); ?>

	<h3><?php echo apply_filters('w2mb_create_option', esc_html__('Submit new listing', 'W2MB')); ?></h3>

	<div class="w2mb-create-listing-wrapper w2mb-row">
		<div class="w2mb-create-listing-form w2mb-col-md-12">
			<form action="<?php echo w2mb_submitUrl(); ?>" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="listing_id" value="<?php echo esc_attr($listing->post->ID); ?>" />
				<input type="hidden" name="listing_id_hash" value="<?php echo md5($listing->post->ID . wp_salt()); ?>" />
				<?php wp_nonce_field('w2mb_submit', '_submit_nonce'); ?>
		
				<?php if (!is_user_logged_in() && (get_option('w2mb_fsubmit_login_mode') == 2 || get_option('w2mb_fsubmit_login_mode') == 3)): ?>
				<div class="w2mb-submit-section w2mb-submit-section-contact-info">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('User info', 'W2MB'); ?></h3>
					<div class="w2mb-submit-section-inside">
						<label class="w2mb-fsubmit-contact"><?php esc_html_e('Your Name', 'W2MB'); ?><?php if (get_option('w2mb_fsubmit_login_mode') == 2): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?></label>
						<input type="text" name="w2mb_user_contact_name" value="<?php echo esc_attr($frontend_controller->w2mb_user_contact_name); ?>" class="w2mb-form-control w2mb-width-100" />
		
						<label class="w2mb-fsubmit-contact"><?php esc_html_e('Your Email', 'W2MB'); ?><?php if (get_option('w2mb_fsubmit_login_mode') == 2): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?></label>
						<input type="text" name="w2mb_user_contact_email" value="<?php echo esc_attr($frontend_controller->w2mb_user_contact_email); ?>" class="w2mb-form-control w2mb-width-100" />
						<p class="w2mb-description"><?php esc_html_e("Login information will be sent to your email after submission", "W2MB"); ?></p>
					</div>
				</div>
				<?php endif; ?>
		
				<div class="w2mb-submit-section w2mb-submit-section-title">
					<h3 class="w2mb-submit-section-label"><?php esc_html_e('Listing title', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></h3>
					<div class="w2mb-submit-section-inside">
						<input type="text" name="post_title" class="w2mb-form-control w2mb-width-100" value="<?php if ($listing->post->post_title != esc_html__('Auto Draft', 'W2MB')) echo esc_attr($listing->post->post_title); ?>" />
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
				<div class="w2mb-submit-section w2mb-submit-section-excerpt">
					<h3 class="w2mb-submit-section-label"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('summary')->name; ?><?php if ($w2mb_instance->content_fields->getContentFieldBySlug('summary')->is_required): ?><span class="w2mb-red-asterisk">*</span><?php endif; ?></h3>
					<div class="w2mb-submit-section-inside">
						<textarea name="post_excerpt" class="w2mb-editor-class w2mb-form-control" rows="4"><?php echo esc_textarea($listing->post->post_excerpt)?></textarea>
						<?php if ($w2mb_instance->content_fields->getContentFieldBySlug('summary')->description): ?><p class="w2mb-description"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('summary')->description; ?></p><?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
				
				<?php do_action('w2mb_create_listing_metaboxes_pre', $listing); ?>
		
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
						</div>
						<?php if ($w2mb_instance->content_fields->getContentFieldBySlug('categories_list')->description): ?><p class="w2mb-description"><?php echo $w2mb_instance->content_fields->getContentFieldBySlug('categories_list')->description; ?></p><?php endif; ?>
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
				
				<?php do_action('w2mb_create_listing_metaboxes_post', $listing); ?>
		
				<?php if (get_option('w2mb_enable_recaptcha')): ?>
				<div class="w2mb-submit-section-adv">
					<?php echo w2mb_recaptcha(); ?>
				</div>
				<?php endif; ?>
		
				<?php
				if ($tos_page = w2mb_get_wpml_dependent_option('w2mb_tospage')) : ?>
				<div class="w2mb-submit-section-adv">
					<label><input type="checkbox" name="w2mb_tospage" value="1" /> <?php printf(esc_html__('I agree to the ', 'W2MB') . '<a href="%s" target="_blank">%s</a>', get_permalink($tos_page), esc_html__('Terms of Services', 'W2MB')); ?></label>
				</div>
				<?php endif; ?>
		
				<?php require_once(ABSPATH . 'wp-admin/includes/template.php'); ?>
				<?php submit_button(esc_html__('Submit new listing', 'W2MB'), 'w2mb-btn w2mb-btn-primary')?>
			</form>
		</div>
	</div>
</div>