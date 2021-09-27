<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Locations levels', 'W2MB'); ?>
	<?php echo sprintf('<a class="add-new-h2" href="?page=%s&action=%s">' . esc_html__('Create new locations level', 'W2MB') . '</a>', $_GET['page'], 'add'); ?>
</h2>

<?php $locations_levels_table->display(); ?>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>