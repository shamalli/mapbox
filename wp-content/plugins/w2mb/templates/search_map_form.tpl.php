<form class="w2mb-content w2mb-map-search-form w2mb-search-form-submit <?php $search_form->printClasses(); ?>" id="w2mb-map-search-form-<?php echo esc_attr($uid); ?>" data-id="<?php echo esc_attr($search_form_id); ?>">
	<?php $search_form->outputHiddenFields(); ?>
	<?php if ($search_form->isCategoriesOrKeywords()): ?>
	<div class="w2mb-map-search-wrapper" id="w2mb-map-search-wrapper-<?php echo esc_attr($uid); ?>">
		<div class="w2mb-map-search-input-container">
			<div class="w2mb-row w2mb-form-group">
				<div class="w2mb-col-md-12">
					<?php
					if ($search_form->isCategories()) {
						w2mb_tax_dropdowns_menu_init($search_form->getCategoriesDropdownsMenuParams(esc_html__('Select category', 'W2MB'), esc_html__('Select category or enter keywords', 'W2MB'))); 
					} else { ?>
					<div class="w2mb-has-feedback">
						<input name="what_search" value="<?php echo esc_attr($search_form->getKeywordValue()); ?>" placeholder="<?php esc_attr_e('Enter keywords', 'W2MB')?>" class="<?php if ($search_form->isKeywordsAJAX()): ?>w2mb-keywords-autocomplete<?php endif; ?> w2mb-form-control w2mb-main-search-field" type="text" autocomplete="off" />
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="w2mb-map-search-toggle-container">
			<span class="w2mb-map-search-toggle" data-id="<?php echo esc_attr($uid); ?>"></span>
		</div>
	</div>
	<?php endif; ?>
	<div class="w2mb-map-search-panel-wrapper" id="w2mb-map-search-panel-wrapper-<?php echo esc_attr($uid); ?>">
		<?php if ($search_form->isLocationsOrAddress()): ?>
		<div class="w2mb-map-search-panel">
			<div class="w2mb-row w2mb-form-group">
				<div class="w2mb-col-md-12">
					<?php
					if ($search_form->isLocations()) {
						w2mb_tax_dropdowns_menu_init($search_form->getLocationsDropdownsMenuParams(esc_html__('Select location', 'W2MB'), esc_html__('Select location or enter address', 'W2MB')));
					} else { ?>
					<div class="w2mb-has-feedback">
						<input name="address" value="<?php echo esc_attr($search_form->getAddressValue()); ?>" placeholder="<?php esc_attr_e('Enter address', 'W2MB')?>" class="w2mb-address-autocomplete w2mb-form-control w2mb-main-search-field" type="text" autocomplete="off" />
					</div>
					<?php } ?>
				</div>
			</div>
			<?php if ($search_form->isRadius()): ?>
				<div class="w2mb-jquery-ui-slider">
					<div class="w2mb-search-radius-label">
						<?php esc_html_e('Search in radius', 'W2MB'); ?>
						<strong id="radius_label_<?php echo esc_attr($search_form_id); ?>"><?php echo $search_form->getRadiusValue(); ?></strong>
						<?php if (get_option('w2mb_miles_kilometers_in_search') == 'miles') esc_html_e('miles', 'W2MB'); else esc_html_e('kilometers', 'W2MB'); ?>
					</div>
					<div class="w2mb-radius-slider" data-id="<?php echo esc_attr($search_form_id); ?>" id="radius_slider_<?php echo esc_attr($search_form_id); ?>"></div>
					<input type="hidden" name="radius" id="radius_<?php echo esc_attr($search_form_id); ?>" value="<?php echo $search_form->getRadiusValue(); ?>" />
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<div class="w2mb-map-listings-panel" id="w2mb-map-listings-panel-<?php echo esc_attr($uid); ?>">
			<?php echo $search_form->listings_content; ?>
		</div>
	</div>
	
	<?php if (!$search_form->isCategoriesOrKeywords()): ?>
	<div class="w2mb-map-sidebar-toggle-container w2mb-map-sidebar-toggle-container-<?php echo esc_attr($uid); ?>">
		<span class="w2mb-map-sidebar-toggle" data-id="<?php echo esc_attr($uid); ?>"></span>
	</div>
	<?php endif; ?>
	<div class="w2mb-map-sidebar-toggle-container-mobile w2mb-map-sidebar-toggle-container-mobile-<?php echo esc_attr($uid); ?>" data-id="<?php echo esc_attr($uid); ?>" title="<?php esc_attr_e("Search panel", "W2MB"); ?>">
		<span class="w2mb-map-sidebar-toggle"></span><span class="w2mb-map-sidebar-toggle-label"><?php esc_html_e('listings', 'W2MB'); ?></span>
	</div>
	<input type="submit" name="submit" class="w2mb-submit-button-hidden" tabindex="-1" />
</form>