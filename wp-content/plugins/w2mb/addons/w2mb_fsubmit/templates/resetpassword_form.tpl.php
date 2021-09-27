<div class="w2mb-content">
	<?php w2mb_renderMessages(); ?>

	<h3><?php esc_html_e('Enter your new password below.', 'W2MB') ?></h3>

	<div class="w2mb-submit-section-adv">
		<?php w2mb_resetpassword_form(array('rp_key' => $rp_key)); ?>
	</div>
</div>