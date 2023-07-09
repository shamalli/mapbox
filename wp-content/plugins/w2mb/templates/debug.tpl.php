<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php esc_html_e('Maps Debug', 'W2MB'); ?>
</h2>

<textarea style="width: 100%; height: 700px">
geolocation response = <?php var_dump($geolocation_response); ?>


license keys = <?php $w2mb_instance->updater->getDownload_url(true); ?>

$w2mb_instance->index_pages_all = <?php var_dump($w2mb_instance->index_pages_all); ?>

$w2mb_instance->listing_pages_all = <?php var_dump($w2mb_instance->listing_pages_all); ?>

<?php if (isset($w2mb_instance->submit_page)): ?>
$w2mb_instance->submit_page = <?php var_dump($w2mb_instance->submit_page); ?>
<?php endif; ?>

<?php if (isset($w2mb_instance->dashboard_page_id)): ?>
$w2mb_instance->dashboard_page_id = <?php echo $w2mb_instance->dashboard_page_id; ?>
<?php endif; ?>

<?php
if ($rewrite_rules):
foreach ($rewrite_rules AS $key=>$rule)
echo $key . '
' . $rule . '

';
endif;
?>

image_sizes = <?php var_dump(w2mb_get_registered_image_sizes()); ?>

<?php foreach ($settings AS $setting)
echo $setting['option_name'] . ' = ' . $setting['option_value'] . '

';
?>


<?php var_dump($levels); ?>


<?php var_dump($content_fields); ?>
</textarea>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>