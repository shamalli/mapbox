<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php echo esc_js($heading); ?>
</h2>

<form action="" method="POST">
	<p>
		<?php echo esc_js($question); ?>
	</p>

	<?php submit_button(esc_html__('Delete', 'W2MB')); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>