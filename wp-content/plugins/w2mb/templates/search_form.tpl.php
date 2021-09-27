<?php $search_form->getSearchFormStyles(); ?>
<form <?php echo $search_form->getFormAttributes(); ?>>
	<?php $search_form->outputHiddenFields(); ?>

	<div class="w2mb-search-overlay w2mb-container-fluid">
		<?php if ($search_form->isDefaultSearchFields()): ?>
		<div class="w2mb-search-section w2mb-row">
			<?php if ($search_form->isCategoriesOrKeywords()): ?>
			<?php do_action('pre_search_what_form_html', $search_form_id); ?>
			<div class="w2mb-col-md-<?php echo $search_form->getColMd(); ?> w2mb-search-input-field-wrap">
				<div class="w2mb-row w2mb-form-group">
					<div class="w2mb-col-md-12">
						<?php
						if ($search_form->isCategories()) {
							w2mb_tax_dropdowns_menu_init($search_form->getCategoriesDropdownsMenuParams(esc_html__('Select category', 'W2MB'), esc_html__('Select category or enter keywords', 'W2MB'))); 
						} else { ?>
						<div class="w2mb-has-feedback">
							<input name="what_search" value="<?php echo esc_attr($search_form->getKeywordValue()); ?>" placeholder="<?php esc_attr_e('Enter keywords', 'W2MB')?>" class="<?php if ($search_form->isKeywordsAJAX()): ?>w2mb-keywords-autocomplete<?php endif; ?> w2mb-form-control w2mb-main-search-field" autocomplete="off" />
						</div>
						<?php } ?>
						<?php if ($search_form->isKeywordsExamples()): ?>
						<p class="w2mb-search-suggestions">
							<?php printf(esc_html__("Try to search: %s", "W2MB"), $search_form->getKeywordsExamples()); ?>
						</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php do_action('post_search_what_form_html', $search_form_id); ?>
			<?php endif; ?>
			
			<?php if ($search_form->isLocationsOrAddress()): ?>
			<?php do_action('pre_search_where_form_html', $search_form_id); ?>
			<div class="w2mb-col-md-<?php echo $search_form->getColMd(); ?> w2mb-search-input-field-wrap">
				<div class="w2mb-row w2mb-form-group">
					<div class="w2mb-col-md-12">
						<?php
						if ($search_form->isLocations()) {
							w2mb_tax_dropdowns_menu_init($search_form->getLocationsDropdownsMenuParams(esc_html__('Select location', 'W2MB'), esc_html__('Select location or enter address', 'W2MB')));
						} else { ?>
						<div class="w2mb-has-feedback">
							<input name="address" value="<?php echo esc_attr($search_form->getAddressValue()); ?>" placeholder="<?php esc_attr_e('Enter address', 'W2MB')?>" class="w2mb-address-autocomplete w2mb-form-control w2mb-main-search-field" autocomplete="off" />
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php do_action('post_search_where_form_html', $search_form_id); ?>
			<?php endif; ?>
			
			<?php if ($search_form->args['on_row_search_button']): ?>
			<div class="w2mb-col-md-2 w2mb-search-submit-button-wrap">
				<?php $search_form->displaySearchButton(true); ?>
			</div>
			<?php endif; ?>
			
			<?php if ($search_form->isRadius()): ?>
			<div class="w2mb-col-md-12">
				<div class="w2mb-jquery-ui-slider">
					<div class="w2mb-search-radius-label">
						<?php esc_html_e('Search in radius', 'W2MB'); ?>
						<strong id="radius_label_<?php echo esc_attr($search_form_id); ?>"><?php echo $search_form->getRadiusValue(); ?></strong>
						<?php if (get_option('w2mb_miles_kilometers_in_search') == 'miles') esc_html_e('miles', 'W2MB'); else esc_html_e('kilometers', 'W2MB'); ?>
					</div>
					<div class="w2mb-radius-slider" data-id="<?php echo esc_attr($search_form_id); ?>" id="radius_slider_<?php echo esc_attr($search_form_id); ?>"></div>
					<input type="hidden" name="radius" id="radius_<?php echo esc_attr($search_form_id); ?>" value="<?php echo $search_form->getRadiusValue(); ?>" />
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php $w2mb_instance->search_fields->render_content_fields($search_form_id, $search_form->args['columns'], $search_form); ?>

		<?php do_action('post_search_form_html', $search_form_id); ?>

		<div class="w2mb-search-section w2mb-search-form-bottom w2mb-row w2mb-clearfix">
			<?php if (!$search_form->args['on_row_search_button']): ?>
			<?php $search_form->displaySearchButton(); ?>
			<?php endif; ?>

			<?php if ($search_form->is_advanced_search_panel): ?>
			<script>
				(function($) {
					"use strict";

					$(function() {
						w2mb_advancedSearch(<?php echo esc_attr($search_form_id); ?>, "<?php esc_html_e('More filters', 'W2MB'); ?>", "<?php esc_html_e('Less filters', 'W2MB'); ?>");
					});
				})(jQuery);
			</script>
			<div class="w2mb-col-md-6 w2mb-form-group w2mb-pull-left">
				<a id="w2mb-advanced-search-label_<?php echo esc_attr($search_form_id); ?>" class="w2mb-advanced-search-label" href="javascript: void(0);"><span class="w2mb-advanced-search-text"><?php esc_html_e('More filters', 'W2MB'); ?></span> <span class="w2mb-advanced-search-toggle w2mb-glyphicon w2mb-glyphicon-chevron-down"></span></a>
			</div>
			<?php endif; ?>

			<?php do_action('buttons_search_form_html', $search_form_id); ?>
		</div>
	</div>
</form>