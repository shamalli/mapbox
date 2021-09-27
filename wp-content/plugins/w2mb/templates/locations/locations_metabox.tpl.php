<script>
	(function($) {
		"use strict";
	
		$(function() {
			var locations_number = <?php echo esc_attr($listing->level->locations_number); ?>;
	
			<?php if ($listing->level->map_markers): ?>
			<?php if (get_option('w2mb_map_markers_type') == 'images'): ?>
				var map_icon_file_input;
				$(document).on("click", ".w2mb-select-map-icon", function() {
					map_icon_file_input = $(this).parents(".w2mb-location-input").find('.w2mb-map-icon-file');
		
					var dialog = $('<div id="w2mb-select-map-icon-dialog"></div>').dialog({
						dialogClass: 'w2mb-content',
						width: ($(window).width()*0.5),
						height: ($(window).height()*0.8),
						modal: true,
						resizable: false,
						draggable: false,
						title: '<?php echo esc_js(esc_html__('Select map marker icon', 'W2MB')); ?>',
						open: function() {
							w2mb_ajax_loader_show();
							$.ajax({
								type: "POST",
								url: w2mb_js_objects.ajaxurl,
								data: {'action': 'w2mb_select_map_icon'},
								dataType: 'html',
								success: function(response_from_the_action_function){
									if (response_from_the_action_function != 0) {
										$('#w2mb-select-map-icon-dialog').html(response_from_the_action_function);
										if (map_icon_file_input.val())
											$(".w2mb-icon[icon_file='"+map_icon_file_input.val()+"']").addClass("w2mb-selected-icon");
									}
								},
								complete: function() {
									w2mb_ajax_loader_hide();
								}
							});
							$(document).on("click", ".ui-widget-overlay", function() { $('#w2mb-select-map-icon-dialog').remove(); });
						},
						close: function() {
							$('#w2mb-select-map-icon-dialog').remove();
						}
					});
				});
				$(document).on("click", ".w2mb-icon", function() {
					$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
					if (map_icon_file_input) {
						map_icon_file_input.val($(this).attr('icon_file'));
						map_icon_file_input = false;
						$(this).addClass("w2mb-selected-icon");
						$('#w2mb-select-map-icon-dialog').remove();
						w2mb_generateMap_backend();
					}
				});
				$(document).on("click", "#reset_icon", function() {
					if (map_icon_file_input) {
						$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
						map_icon_file_input.val('');
						map_icon_file_input = false;
						$('#w2mb-select-map-icon-dialog').remove();
						w2mb_generateMap_backend();
					}
				});
			<?php else: ?>
				var map_icon_file_input;
				$(document).on("click", ".w2mb-select-map-icon", function() {
					map_icon_file_input = $(this).parents(".w2mb-location-input").find('.w2mb-map-icon-file');

					var dialog = $('<div id="select_marker_icon_dialog"></div>').dialog({
						dialogClass: 'w2mb-content',
						width: ($(window).width()*0.5),
						height: ($(window).height()*0.8),
						modal: true,
						resizable: false,
						draggable: false,
						title: '<?php echo esc_js(esc_html__('Select map marker icon', 'W2MB') . ((get_option('w2mb_map_markers_type') == 'icons') ? esc_html__(' (icon and color may depend on selected categories)', 'W2MB') : '')); ?>',
						open: function() {
							w2mb_ajax_loader_show();
							$.ajax({
								type: "POST",
								url: w2mb_js_objects.ajaxurl,
								data: {'action': 'w2mb_select_fa_icon'},
								dataType: 'html',
								success: function(response_from_the_action_function){
									if (response_from_the_action_function != 0) {
										$('#select_marker_icon_dialog').html(response_from_the_action_function);
										if (map_icon_file_input.val())
											$("#"+map_icon_file_input.val()).addClass("w2mb-selected-icon");
									}
								},
								complete: function() {
									w2mb_ajax_loader_hide();
								}
							});
							$(document).on("click", ".ui-widget-overlay", function() { $('#select_marker_icon_dialog').remove(); });
						},
						close: function() {
							$('#select_marker_icon_dialog').remove();
						}
					});
				});
				$(document).on("click", ".w2mb-fa-icon", function() {
					$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
					if (map_icon_file_input) {
						map_icon_file_input.val($(this).attr('id'));
						map_icon_file_input = false;
						$(this).addClass("w2mb-selected-icon");
						$('#select_marker_icon_dialog').remove();
						w2mb_generateMap_backend();
					}
				});
				$(document).on("click", "#w2mb-reset-fa-icon", function() {
					if (map_icon_file_input) {
						$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
						map_icon_file_input.val('');
						map_icon_file_input = false;
						$('#select_marker_icon_dialog').remove();
						w2mb_generateMap_backend();
					}
				});
			<?php endif; ?>
			<?php endif; ?>
			
			$(document).on('click', '.add_address', function() {
				w2mb_ajax_loader_show();
				$.ajax({
					type: "POST",
					url: w2mb_js_objects.ajaxurl,
					data: {'action': 'w2mb_add_location_in_metabox', 'post_id': <?php echo esc_js($listing->post->ID); ?>},
					success: function(response_from_the_action_function){
						if (response_from_the_action_function != 0) {
							$("#w2mb-locations-wrapper").append(response_from_the_action_function);
							$(".w2mb-delete-address").show();
							if (locations_number == $(".w2mb-location-in-metabox").length) {
								$(".add_address").hide();
							}
							if (w2mb_maps_objects.address_autocomplete) {
								w2mb_setupAutocomplete();
							}
						}
					},
					complete: function() {
						w2mb_ajax_loader_hide();
					}
				});
			});
			$(document).on("click", ".w2mb-delete-address", function() {
				$(this).parents(".w2mb-location-in-metabox").remove();
				if ($(".w2mb-location-in-metabox").length == 1)
					$(".w2mb-delete-address").hide();
	
				w2mb_generateMap_backend();
	
				if (locations_number > $(".w2mb-location-in-metabox").length)
					$(".add_address").show();
			});
	
			$(document).on("click", ".w2mb-manual-coords", function() {
	        	if ($(this).is(":checked"))
	        		$(this).parents(".w2mb-manual-coords-wrapper").find(".w2mb-manual-coords-block").slideDown(200);
	        	else
	        		$(this).parents(".w2mb-manual-coords-wrapper").find(".w2mb-manual-coords-block").slideUp(200);
	        });
	
	        if (locations_number > $(".w2mb-location-in-metabox").length)
				$(".add_address").show();
		});
	})(jQuery);
</script>

<div class="w2mb-locations-metabox w2mb-content">
	<div id="w2mb-locations-wrapper" class="w2mb-form-horizontal">
		<?php
		if ($listing->locations)
			foreach ($listing->locations AS $location)
				w2mb_renderTemplate('locations/locations_in_metabox.tpl.php', array('listing' => $listing, 'location' => $location, 'locations_levels' => $locations_levels, 'delete_location_link' => (count($listing->locations) > 1) ? true : false));
		else
			w2mb_renderTemplate('locations/locations_in_metabox.tpl.php', array('listing' => $listing, 'location' => new w2mb_location, 'locations_levels' => $locations_levels, 'delete_location_link' => false));
		?>
	</div>
	
	<?php if ($listing->level->locations_number > 1): ?>
	<div class="w2mb-row w2mb-form-group w2mb-location-input">
		<div class="w2mb-col-md-12">	
			<a class="add_address w2mb-display-none" href="javascript: void(0);">
				<span class="w2mb-fa w2mb-fa-plus"></span>
				<?php esc_html_e('Add address', 'W2MB'); ?>
			</a>
		</div>
	</div>
	<?php endif; ?>

	<div class="w2mb-row w2mb-form-group w2mb-location-input">
		<div class="w2mb-col-md-12">
			<input type="hidden" name="map_zoom" class="w2mb-map-zoom" value="<?php echo esc_attr($listing->map_zoom); ?>" />
			<input type="button" class="w2mb-btn w2mb-btn-primary" onClick="w2mb_generateMap_backend(); return false;" value="<?php esc_attr_e('Generate on the map', 'W2MB'); ?>" />
		</div>
	</div>
	<div class="w2mb-maps-canvas" id="w2mb-maps-canvas" style="width: auto; height: 450px;"></div>
</div>