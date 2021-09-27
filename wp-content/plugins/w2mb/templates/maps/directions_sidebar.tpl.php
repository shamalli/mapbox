<div class="w2mb-map-directions-panel-wrapper" id="w2mb-map-directions-panel-wrapper-<?php echo esc_attr($map_id); ?>">
	<div class="w2mb-map-directions-panel">
		<div class="w2mb-form-group">
			<div class="w2mb-has-feedback">
				<input name="address_a" value="" placeholder="<?php esc_attr_e('Enter origin address', 'W2MB')?>" class="w2mb-address-autocomplete w2mb-form-control w2mb-directions-address w2mb-directions-address-a" autocomplete="off" />
			</div>
		</div>
		<div class="w2mb-form-group">
			<div class="w2mb-has-feedback">
				<input name="listing_title" value="" placeholder="<?php esc_attr_e('Select distination on the map', 'W2MB')?>" class="w2mb-form-control w2mb-directions-address w2mb-directions-listing-title" autocomplete="off" readonly="readonly" />
				<input type="hidden" name="destination_coords" value="" class="w2mb-form-control w2mb-directions-address w2mb-directions-address-b" />
				<span class="w2mb-dropdowns-menu-button w2mb-form-control-feedback w2mb-glyphicon w2mb-glyphicon-map-marker"></span>
			</div>
		</div>
		<div class="w2mb-form-group w2mb-map-directions-sidebar-buttons w2mb-clearfix">
			<a href="javascript:void(0);" class="w2mb-btn w2mb-btn-primary w2mb-map-directions-sidebar-close-button" data-id="<?php echo esc_attr($map_id); ?>"><?php esc_html_e("Close", "W2MB"); ?></a>
			<a href="javascript:void(0);" class="w2mb-btn w2mb-btn-primary w2mb-map-directions-sidebar-get-button" data-id="<?php echo esc_attr($map_id); ?>"><?php esc_html_e("Get directions", "W2MB"); ?></a>
		</div>
		<div id="w2mb-route-container-<?php echo esc_attr($map_id); ?>" class="w2mb-route-container">
			<p class="w2mb-route-container-description"><?php esc_html_e("Click 'Directions' button in selected location, enter origin address or use 'My location' button, then click 'Get directions' button.", "W2MB"); ?></p>
		</div>
	</div>
</div>