<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Maps Reset', 'W2MB'); ?>
</h2>

<h3><?php esc_html_e('Are you sure you want to reset the locator?', 'W2MB'); ?></h3>
<a href="<?php echo admin_url('admin.php?page=w2mb_reset&reset=settings'); ?>"><?php esc_html_e('Reset settings', 'W2MB'); ?></a>
<br />
<a href="<?php echo admin_url('admin.php?page=w2mb_reset&reset=settings_tables'); ?>"><?php esc_html_e('Reset settings and database tables', 'W2MB'); ?></a>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>