<h3>
	<?php echo apply_filters('w2mb_claim_option', sprintf(esc_html__('Claim listing "%s"', 'W2MB'), $w2mb_instance->current_listing->title()), $w2mb_instance->current_listing); ?>
</h3>

<!-- <?php if ($frontend_controller->action == 'show'): ?>
<?php if (get_option('w2mb_claim_approval')): ?>
<p><?php esc_html_e('The notification about claim for this listing will be sent to the current listing owner.', 'W2MB'); ?></p>
<p><?php esc_html_e("After approval you will become owner of this listing, you'll receive email notification.", 'W2MB'); ?></p>
<?php endif; ?>
<?php if (get_option('w2mb_after_claim') == 'expired'): ?>
<p><?php echo esc_html__('After approval listing status become expired.', 'W2MB'); ?></p>
<?php endif; ?> -->

<?php do_action('w2mb_claim_html', $w2mb_instance->current_listing); ?>

<form method="post" action="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'claim_listing', 'listing_id' => $w2mb_instance->current_listing->post->ID, 'claim_action' => 'claim')); ?>">
	<input type="hidden" name="referer" value="<?php echo $frontend_controller->referer; ?>" />
	<div class="w2mb-form-group w2mb-row">
		<div class="w2mb-col-md-12">
			<textarea name="claim_message" class="w2mb-form-control" rows="5"></textarea>
			<p class="description"><?php esc_html_e('additional information to moderator', 'W2MB'); ?></p>
		</div>
	</div>
	<input type="submit" class="w2mb-btn w2mb-btn-primary" value="<?php esc_attr_e('Send Claim', 'W2MB'); ?>"></input>
	&nbsp;&nbsp;&nbsp;
	<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Cancel', 'W2MB'); ?></a>
</form>
<?php elseif ($frontend_controller->action == 'claim'): ?>
<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Go back ', 'W2MB'); ?></a>
<?php endif; ?>