<p><?php esc_html_e("When field is empty contact messages from contact form will be sent directly to author email.", 'W2MB'); ?></p>

<div class="w2mb-content">
	<input class="w2mb-field-input-string w2mb-form-control" type="text" name="contact_email" value="<?php echo esc_attr($listing->contact_email); ?>" />
</div>
	
<?php do_action('w2mb_contact_email_metabox_html', $listing); ?>