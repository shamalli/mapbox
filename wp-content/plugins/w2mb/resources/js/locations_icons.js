(function($) {
	"use strict";
	
	var location_icon_image_input;
	
	$(function() {
		var location_icon_image_url = locations_icons.locations_icons_url;

		$(document).on("click", ".select_icon_image", function() {
			location_icon_image_input = $(this).parent().find('.icon_image');

			var dialog = $('<div id="select_field_icon_dialog"></div>').dialog({
				dialogClass: 'w2mb-content',
				width: ($(window).width()*0.5),
				height: ($(window).height()*0.8),
				modal: true,
				resizable: false,
				draggable: false,
				title: locations_icons.ajax_dialog_title,
				open: function() {
					w2mb_ajax_loader_show();
					$.ajax({
						type: "POST",
						url: w2mb_js_objects.ajaxurl,
						data: {'action': 'w2mb_select_location_icon_dialog'},
						dataType: 'html',
						success: function(response_from_the_action_function){
							if (response_from_the_action_function != 0) {
								$('#select_field_icon_dialog').html(response_from_the_action_function);
								if (location_icon_image_input.val())
									$(".w2mb-icon[icon_file='"+location_icon_image_input.val()+"']").addClass("w2mb-selected-icon");
							}
						},
						complete: function() {
							w2mb_ajax_loader_hide();
						}
					});
					$(document).on("click", ".ui-widget-overlay", function() { $('#select_field_icon_dialog').remove(); });
				},
				close: function() {
					$('#select_field_icon_dialog').remove();
				}
			});
		});
		$(document).on("click", ".w2mb-icon", function() {
			$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
			var icon_file = $(this).attr('icon_file');
			w2mb_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2mb_js_objects.ajaxurl,
				data: {'action': 'w2mb_select_location_icon', 'icon_file': icon_file, 'location_id': location_icon_image_input.parent().find(".location_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (response_from_the_action_function != 0) {
						if (location_icon_image_input) {
							location_icon_image_input.val(icon_file);
							location_icon_image_input.parent().find(".icon_image_tag").attr('src', location_icon_image_url+icon_file).show();
							location_icon_image_input = false;
						}
					}
				},
				complete: function() {
					$(this).addClass("w2mb-selected-icon");
					$('#select_field_icon_dialog').remove();
					w2mb_ajax_loader_hide();
				}
			});
		});
		$(document).on("click", "#reset_icon", function() {
			$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
			w2mb_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2mb_js_objects.ajaxurl,
				data: {'action': 'w2mb_select_location_icon', 'location_id': location_icon_image_input.parent().find(".location_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (location_icon_image_input) {
						location_icon_image_input.val('');
						location_icon_image_input.parent().find(".icon_image_tag").attr('src', '').hide();
						location_icon_image_input = false;
					}
				},
				complete: function() {
					$('#select_field_icon_dialog').remove();
					w2mb_ajax_loader_hide();
				}
			});
		});
	});
})(jQuery);