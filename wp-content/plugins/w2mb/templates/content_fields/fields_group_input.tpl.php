<script>
	<?php
	foreach ($content_fields AS $content_field): 
		if (!$content_field->isCategories() || $content_field->categories === array()) { ?>
			w2mb_js_objects.fields_in_categories[<?php echo esc_js($content_field->id); ?>] = [];
		<?php } else { ?>
			w2mb_js_objects.fields_in_categories[<?php echo esc_js($content_field->id); ?>] = [<?php echo implode(',', $content_field->categories); ?>];
		<?php } ?>
	<?php endforeach; ?>
</script>

<div class="w2mb-submit-section w2mb-content-fields-metabox">
	<h3 class="w2mb-submit-section-label"><?php echo $group->name; ?></h3>
	<div class="w2mb-submit-section-inside w2mb-form-horizontal">
		<?php
		foreach ($content_fields AS $content_field) {
			$content_field->renderInput();
		}
		?>
	</div>
</div>