		<div class="w2mb-location-in-metabox">
			<?php $uID = rand(1, 10000); ?>
			<input type="hidden" name="w2mb_location[<?php echo esc_attr($uID);?>]" value="1" />

			<?php
			if (w2mb_is_anyone_in_taxonomy(W2MB_LOCATIONS_TAX)) {
				w2mb_tax_dropdowns_init(
					array(
						'tax' => W2MB_LOCATIONS_TAX,
						'term_id' => $location->selected_location,
						'count' => false,
						'labels' => $locations_levels->getNamesArray(),
						'titles' => $locations_levels->getSelectionsArray(),
						'allow_add_term' => $locations_levels->getAllowAddTermArray(),
						'uID' => $uID,
						'exact_locations' => $listing->level->locations,
					)
				);
			}
			?>

			<div class="w2mb-row w2mb-form-group w2mb-location-input w2mb-address-line-1-wrapper <?php if (!w2mb_get_dynamic_option('w2mb_enable_address_line_1')): ?>w2mb-display-none<?php endif; ?>">
				<div class="w2mb-col-md-2">
					<label class="w2mb-control-label">
						<?php
						if (!w2mb_get_dynamic_option('w2mb_enable_address_line_2'))
							esc_html_e('Address', 'W2MB');
						else
							esc_html_e('Address line 1', 'W2MB');
						?>
					</label>
				</div>
				<div class="w2mb-col-md-10">
					<div class="w2mb-has-feedback">
						<input type="text" id="address_line_<?php echo esc_attr($uID);?>" name="address_line_1[<?php echo esc_attr($uID);?>]" class="w2mb-address-line-1 w2mb-form-control <?php if (get_option('w2mb_address_autocomplete')): ?>w2mb-listing-field-autocomplete<?php endif; ?>" value="<?php echo esc_attr($location->address_line_1); ?>" placeholder="" />
						<?php if (get_option('w2mb_address_geocode')): ?>
						<span class="w2mb-get-location w2mb-form-control-feedback w2mb-glyphicon w2mb-glyphicon-screenshot"></span>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="w2mb-row w2mb-form-group w2mb-location-input w2mb-address-line-2-wrapper <?php if (!w2mb_get_dynamic_option('w2mb_enable_address_line_2')): ?>w2mb-display-none<?php endif; ?>">
				<div class="w2mb-col-md-2">
					<label class="w2mb-control-label">
						<?php
						if (!w2mb_get_dynamic_option('w2mb_enable_address_line_1'))
							esc_html_e('Address', 'W2MB');
						else
							esc_html_e('Address line 2', 'W2MB');
						?>
					</label>
				</div>
				<div class="w2mb-col-md-10">
					<input type="text" name="address_line_2[<?php echo esc_attr($uID);?>]" class="w2mb-address-line-2 w2mb-form-control" value="<?php echo esc_attr($location->address_line_2); ?>" />
				</div>
			</div>

			<div class="w2mb-row w2mb-form-group w2mb-location-input w2mb-zip-or-postal-index-wrapper <?php if (!w2mb_get_dynamic_option('w2mb_enable_postal_index')): ?>w2mb-display-none<?php endif; ?>">
				<div class="w2mb-col-md-2">
					<label class="w2mb-control-label">
						<?php esc_html_e('Zip code', 'W2MB'); ?>
					</label>
				</div>
				<div class="w2mb-col-md-10">
					<input type="text" name="zip_or_postal_index[<?php echo esc_attr($uID);?>]" class="w2mb-zip-or-postal-index w2mb-form-control" value="<?php echo esc_attr($location->zip_or_postal_index); ?>" />
				</div>
			</div>

			<div class="w2mb-row w2mb-form-group w2mb-location-input w2mb-additional-info-wrapper <?php if (!w2mb_get_dynamic_option('w2mb_enable_additional_info')): ?>w2mb-display-none<?php endif; ?>">
				<div class="w2mb-col-md-2">
					<label class="w2mb-control-label">
						<?php esc_html_e('Additional info for map marker infowindow', 'W2MB'); ?>
					</label>
				</div>
				<div class="w2mb-col-md-10">
					<textarea name="additional_info[<?php echo esc_attr($uID);?>]" class="w2mb-additional-info w2mb-form-control" rows="2"><?php echo esc_textarea($location->additional_info); ?></textarea>
				</div>
			</div>

			<div class="w2mb-manual-coords-wrapper <?php if (!w2mb_get_dynamic_option('w2mb_enable_manual_coords')): ?>w2mb-display-none<?php endif; ?>">
				<div class="w2mb-row w2mb-location-input w2mb-form-group">
					<div class="w2mb-col-md-12 w2mb-checkbox">
						<label>
							<input type="checkbox" name="manual_coords[<?php echo esc_attr($uID);?>]" value="1" class="w2mb-manual-coords" <?php if ($location->manual_coords) echo 'checked'; ?> /> <?php esc_html_e('Enter coordinates manually', 'W2MB'); ?>
						</label>
					</div>
				</div>

				<!-- w2mb-manual-coords-block - position required for jquery selector -->
				<div class="w2mb-manual-coords-block <?php if (!$location->manual_coords) echo 'w2mb-display-none'; ?>">
					<div class="w2mb-row w2mb-form-group w2mb-location-input">
						<div class="w2mb-col-md-2">
							<label class="w2mb-control-label">
								<?php esc_html_e('Latitude', 'W2MB'); ?>
							</label>
						</div>
						<div class="w2mb-col-md-10">
							<input type="text" name="map_coords_1[<?php echo esc_attr($uID);?>]" class="w2mb-map-coords-1 w2mb-form-control" value="<?php echo esc_attr($location->map_coords_1); ?>">
						</div>
					</div>
	
					<div class="w2mb-row w2mb-form-group w2mb-location-input">
						<div class="w2mb-col-md-2">
							<label class="w2mb-control-label">
								<?php esc_html_e('Longitude', 'W2MB'); ?>
							</label>
						</div>
						<div class="w2mb-col-md-10">
							<input type="text" name="map_coords_2[<?php echo esc_attr($uID);?>]" class="w2mb-map-coords-2 w2mb-form-control" value="<?php echo esc_attr($location->map_coords_2); ?>">
						</div>
					</div>
				</div>
			</div>

			<?php if ($listing->level->map_markers): ?>
			<div class="w2mb-row w2mb-location-input">
				<div class="w2mb-col-md-12">
					<a class="w2mb-select-map-icon" href="javascript: void(0);">
						<span class="w2mb-fa w2mb-fa-map-marker"></span>
						<?php esc_html_e('Select marker icon', 'W2MB'); ?><?php if (get_option('w2mb_map_markers_type') == 'icons') esc_html_e(' (icon and color may depend on selected categories)', 'W2MB'); ?>
					</a>
					<input type="hidden" name="map_icon_file[<?php echo esc_attr($uID);?>]" class="w2mb-map-icon-file" value="<?php if ($location->map_icon_manually_selected) echo esc_attr($location->map_icon_file); ?>">
				</div>
			</div>
			<?php endif; ?>

			<div class="w2mb-row w2mb-location-input">
				<div class="w2mb-col-md-12">
					<a href="javascript: void(0);" class="w2mb-delete-address <?php if (!$delete_location_link) echo 'w2mb-display-none'; ?>">
						<span class="w2mb-fa w2mb-fa-minus"></span>
						<?php esc_html_e('Delete address', 'W2MB'); ?>
					</a>
				</div>
			</div>
		</div>