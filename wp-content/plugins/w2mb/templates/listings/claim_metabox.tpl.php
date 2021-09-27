<p><?php esc_html_e("By checking this option you allow registered users to claim this listing.", 'W2MB'); ?></p>

<div class="w2mb-content">
	<div class="w2mb-checkbox">
		<label>
			<input type="checkbox" name="is_claimable" value=1 <?php checked(1, $listing->is_claimable, true); ?> />
			<?php esc_html_e('Allow claim', 'W2MB'); ?>
		</label>
	</div>
</div>
	
<?php do_action('w2mb_claim_metabox_html', $listing); ?>