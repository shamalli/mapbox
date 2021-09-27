<form method="POST" action="<?php the_permalink($listing->post->ID); ?>#contact-tab" id="w2mb_contact_form">
	<input type="hidden" name="listing_id" id="contact_listing_id" value="<?php echo esc_attr($listing->post->ID); ?>" />
	<input type="hidden" name="contact_nonce" id="contact_nonce" value="<?php print wp_create_nonce('w2mb_contact_nonce'); ?>" />
	<h3><?php
		if (get_option('w2mb_hide_author_link'))
			esc_html_e('Send message to listing owner', 'W2MB');
		else
			printf(esc_html__('Send message to %s', 'W2MB'), get_the_author());
	?></h3>
	<h5 id="contact_warning" class="w2mb-contact-warning"></h5>
	<div class="w2mb-contact-form">
		<?php if (is_user_logged_in()): ?>
		<p>
			<?php printf(esc_html__('You are currently logged in as %s. Your message will be sent using your logged in name and email.', 'W2MB'), wp_get_current_user()->user_login); ?>
			<input type="hidden" name="contact_name" id="contact_name" />
			<input type="hidden" name="contact_email" id="contact_email" />
		</p>
		<?php else: ?>
		<p>
			<label for="contact_name"><?php esc_html_e('Contact Name', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
			<input type="text" name="contact_name" id="contact_name" class="w2mb-form-control" value="<?php echo esc_attr(w2mb_getValue($_POST, 'contact_name')); ?>" size="35" />
		</p>
		<p>
			<label for="contact_email"><?php esc_html_e("Contact Email", "W2MB"); ?><span class="w2mb-red-asterisk">*</span></label>
			<input type="text" name="contact_email" id="contact_email" class="w2mb-form-control" value="<?php echo esc_attr(w2mb_getValue($_POST, 'contact_email')); ?>" size="35" />
		</p>
		<?php endif; ?>
		<p>
			<label for="contact_message"><?php esc_html_e("Your message", "W2MB"); ?><span class="w2mb-red-asterisk">*</span></label>
			<textarea name="contact_message" id="contact_message" class="w2mb-form-control" rows="6"><?php echo esc_textarea(w2mb_getValue($_POST, 'contact_message')); ?></textarea>
		</p>
		
		<?php echo w2mb_recaptcha(); ?>
		
		<input type="submit" name="submit" class="w2mb-send-message-button w2mb-btn w2mb-btn-primary" value="<?php esc_attr_e('Send message', 'W2MB'); ?>" />
	</div>
</form>