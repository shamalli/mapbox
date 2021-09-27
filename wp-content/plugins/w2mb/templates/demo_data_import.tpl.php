<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2><?php esc_html_e('Demo Data Import'); ?></h2>

<?php if (!w2mb_getValue($_POST, 'submit')): ?>
<div class="error">
	<p><?php esc_html_e("1. This is Demo Data Import tool. This tool will help you to install some demo content: listings and demo pages.", "W2MB"); ?></p>
	<p><?php esc_html_e("2. Each time you click import button - it creates new set of listings and pages. Avoid duplicates.", "W2MB"); ?></p>
	<p><?php esc_html_e("3. Import will not add pages in your navigation menus.", "W2MB"); ?></p>
	<p><?php esc_html_e("4. This is not 100% copy of the demo site. Just gives some examples of maps usage. Final view and layout depends on your theme options.", "W2MB"); ?></p>
</div>

<form method="POST" action="" id="demo_data_import_form">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_csv_import_nonce');?>
	
	<?php submit_button(esc_html__('Start import', 'W2MB'), 'primary', 'submit', true, array('id' => 'import_button')); ?>
</form>
<?php endif; ?>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>