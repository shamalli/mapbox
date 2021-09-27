<?php if ($search_fields || $search_fields_advanced): ?>
<div class="w2mb-clearfix"></div>
<script>
	(function($) {
		"use strict";
	
		$(function() {

			$(document).on("w2mb:selected_tax_change", "#w2mb-search-form-<?php echo esc_attr($search_form_id); ?> .selected_tax_<?php echo W2MB_CATEGORIES_TAX; ?>", function() {
				hideShowFields_<?php echo esc_attr($search_form_id); ?>($(this).val());
			});
	
			if ($("#w2mb-search-form-<?php echo esc_attr($search_form_id); ?> .selected_tax_<?php echo W2MB_CATEGORIES_TAX; ?>").length > 0) {
				hideShowFields_<?php echo esc_attr($search_form_id); ?>($("#w2mb-search-form-<?php echo esc_attr($search_form_id); ?> .selected_tax_<?php echo W2MB_CATEGORIES_TAX; ?>").val());
			} else {
				hideShowFields_<?php echo esc_attr($search_form_id); ?>(0);
			}
	
			function hideShowFields_<?php echo esc_attr($search_form_id); ?>(id) {
				var selected_categories_ids = [id];
	
				$(".w2mb-field-search-block-<?php echo esc_attr($search_form_id); ?>").hide();
				$.each(w2mb_fields_in_categories_<?php echo esc_attr($search_form_id); ?>, function(index, value) {
					var show_field = false;
					if (value != undefined) {
						if (value.length > 0) {
							var key;
							for (key in value) {
								var key2;
								for (key2 in selected_categories_ids)
									if (value[key] == selected_categories_ids[key2])
										show_field = true;
							}
						}
						
						if ((value.length == 0 || show_field) && $(".w2mb-field-search-block-"+index+"_<?php echo esc_attr($search_form_id); ?>").length) {
							$(".w2mb-field-search-block-"+index+"_<?php echo esc_attr($search_form_id); ?>").show();
						}
					}
				});

				<?php if ($is_advanced_search_panel): ?>
				$("#w2mb-advanced-search-label_<?php echo esc_attr($search_form_id); ?>").hide();
				$("#w2mb_advanced_search_fields_<?php echo esc_attr($search_form_id); ?> .w2mb-search-content-field > div").map(function() {
					if ($(this).css("display") == 'block') {
						$("#w2mb-advanced-search-label_<?php echo esc_attr($search_form_id); ?>").show();
					}
				});
				<?php endif; ?>
			}
		});
	})(jQuery);
</script>

<div id="w2mb_standard_search_fields_<?php echo esc_attr($search_form_id); ?>" class="w2mb-search-fields-block">
	<?php foreach ($search_fields AS $search_field): ?>
	<div class="w2mb-search-content-field">
		<?php $search_field->renderSearch($search_form_id, $columns, $defaults); ?>
	</div>
	<?php endforeach; ?>
</div>
<?php if ($is_advanced_search_panel): ?>
<input type="hidden" name="use_advanced" id="use_advanced_<?php echo esc_attr($search_form_id); ?>" value="<?php echo (int)$advanced_open; ?>" autocomplete="off" />
<div id="w2mb_advanced_search_fields_<?php echo esc_attr($search_form_id); ?>" class="w2mb-search-fields-block <?php if (!$advanced_open): ?>w2mb-display-none<?php endif; ?>">
	<?php foreach ($search_fields_advanced AS $search_field): ?>
	<div class="w2mb-search-content-field">
		<?php $search_field->renderSearch($search_form_id, $columns, $defaults); ?>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>