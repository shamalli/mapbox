<form method="POST" action="<?php the_permalink($listing->post->ID); ?>#report-tab" id="w2mb_report_form">
	<input type="hidden" name="listing_id" id="report_listing_id" value="<?php echo esc_attr($listing->post->ID); ?>" />
	<input type="hidden" name="report_nonce" id="report_nonce" value="<?php print wp_create_nonce('w2mb_report_nonce'); ?>" />
	<h3><?php esc_html_e('Send message to moderator', 'W2MB'); ?></h3>
	<h5 id="report_warning" class="w2mb-report-warning"></h5>
	<div class="w2mb-report-form">
		<?php if (is_user_logged_in()): ?>
		<p>
			<?php printf(esc_html__('You are currently logged in as %s. Your message will be sent using your logged in name and email.', 'W2MB'), wp_get_current_user()->user_login); ?>
			<input type="hidden" name="report_name" id="report_name" />
			<input type="hidden" name="report_email" id="report_email" />
		</p>
		<?php else: ?>
		<p>
			<label for="report_name"><?php esc_html_e('Contact Name', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
			<input type="text" name="report_name" id="report_name" class="w2mb-form-control" value="<?php echo esc_attr(w2mb_getValue($_POST, 'report_name')); ?>" size="35" />
		</p>
		<p>
			<label for="report_email"><?php esc_html_e("Contact Email", "W2MB"); ?><span class="w2mb-red-asterisk">*</span></label>
			<input type="text" name="report_email" id="report_email" class="w2mb-form-control" value="<?php echo esc_attr(w2mb_getValue($_POST, 'report_email')); ?>" size="35" />
		</p>
		<?php endif; ?>
		<p>
			<label for="report_message"><?php esc_html_e("Your message", "W2MB"); ?><span class="w2mb-red-asterisk">*</span></label>
			<textarea name="report_message" id="report_message" class="w2mb-form-control" rows="6"><?php echo esc_textarea(w2mb_getValue($_POST, 'report_message')); ?></textarea>
		</p>
		
		<?php echo w2mb_recaptcha(); ?>
		
		<input type="submit" name="submit" class="w2mb-send-message-button w2mb-btn w2mb-btn-primary" value="<?php esc_attr_e('Send message', 'W2MB'); ?>" />
	</div>
</form>