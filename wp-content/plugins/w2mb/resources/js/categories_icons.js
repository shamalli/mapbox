(function($) {
	"use strict";
	
	var category_icon_image_input, category_marker_icon_input, category_marker_icon_tag, category_marker_image_png_input;
	
	$(function() {
		var category_icon_image_url = categories_icons.categories_icons_url;

		$(document).on("click", ".select_icon_image", function() {
			category_icon_image_input = $(this).parent().find('.icon_image');

			var dialog = $('<div id="select_field_icon_dialog"></div>').dialog({
				dialogClass: 'w2mb-content',
				width: ($(window).width()*0.5),
				height: ($(window).height()*0.8),
				modal: true,
				resizable: false,
				draggable: false,
				title: categories_icons.ajax_dialog_title,
				open: function() {
					w2mb_ajax_loader_show();
					$.ajax({
						type: "POST",
						url: w2mb_js_objects.ajaxurl,
						data: {'action': 'w2mb_select_category_icon_dialog'},
						dataType: 'html',
						success: function(response_from_the_action_function){
							if (response_from_the_action_function != 0) {
								$('#select_field_icon_dialog').html(response_from_the_action_function);
								if (category_icon_image_input.val())
									$(".w2mb-icon[icon_file='"+category_icon_image_input.val()+"']").addClass("w2mb-selected-icon");
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
				data: {'action': 'w2mb_select_category_icon', 'icon_file': icon_file, 'category_id': category_icon_image_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (response_from_the_action_function != 0) {
						if (category_icon_image_input) {
							category_icon_image_input.val(icon_file);
							category_icon_image_input.parent().find(".icon_image_tag").attr('src', category_icon_image_url+icon_file).show();
							category_icon_image_input = false;
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
				data: {'action': 'w2mb_select_category_icon', 'category_id': category_icon_image_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (category_icon_image_input) {
						category_icon_image_input.val('');
						category_icon_image_input.parent().find(".icon_image_tag").attr('src', '').hide();
						category_icon_image_input = false;
					}
				},
				complete: function() {
					$('#select_field_icon_dialog').remove();
					w2mb_ajax_loader_hide();
				}
			});
		});

		var categories_markers_images_png_url = categories_icons.categories_markers_images_png_url;
		$(document).on("click", ".select_marker_png_image", function() {
			category_marker_image_png_input = $(this).parent().find('.marker_png_image');
			
			var dialog = $('<div id="select_marker_image_dialog"></div>').dialog({
				dialogClass: 'w2mb-content',
				width: ($(window).width()*0.5),
				height: ($(window).height()*0.8),
				modal: true,
				resizable: false,
				draggable: false,
				title: categories_icons.ajax_dialog_title,
				open: function() {
					w2mb_ajax_loader_show();
					$.ajax({
						type: "POST",
						url: w2mb_js_objects.ajaxurl,
						data: {'action': 'w2mb_select_category_marker_png_image_dialog'},
						dataType: 'html',
						success: function(response_from_the_action_function){
							if (response_from_the_action_function != 0) {
								$('#select_marker_image_dialog').html(response_from_the_action_function);
								if (category_marker_image_png_input.val())
									$(".w2mb-png-image[icon_file='"+category_marker_image_png_input.val()+"']").addClass("w2mb-selected-icon");
							}
						},
						complete: function() {
							w2mb_ajax_loader_hide();
						}
					});
					$(document).on("click", ".ui-widget-overlay", function() { $('#select_marker_image_dialog').remove(); });
				},
				close: function() {
					$('#select_marker_image_dialog').remove();
				}
			});
		});
		$(document).on("click", ".w2mb-png-image", function() {
			$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
			var icon_file = $(this).attr('icon_file');
			w2mb_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2mb_js_objects.ajaxurl,
				data: {'action': 'w2mb_select_category_marker_png_image', 'image_name': icon_file, 'category_id': category_marker_image_png_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (response_from_the_action_function != 0) {
						if (category_marker_image_png_input) {
							category_marker_image_png_input.val(icon_file);
							category_marker_image_png_input.parent().find(".w2mb-marker-image-png-tag").attr('src', categories_markers_images_png_url+icon_file).show();
							category_marker_image_png_input = false;
						}
					}
				},
				complete: function() {
					$(this).addClass("w2mb-selected-icon");
					$('#select_marker_image_dialog').remove();
					w2mb_ajax_loader_hide();
				}
			});
		});
		$(document).on("click", "#reset_png_image", function() {
			$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
			w2mb_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2mb_js_objects.ajaxurl,
				data: {'action': 'w2mb_select_category_marker_png_image', 'category_id': category_marker_image_png_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (category_marker_image_png_input) {
						category_marker_image_png_input.val('');
						category_marker_image_png_input.parent().find(".w2mb-marker-image-png-tag").attr('src', '').hide();
						category_marker_image_png_input = false;
					}
				},
				complete: function() {
					$('#select_marker_image_dialog').remove();
					w2mb_ajax_loader_hide();
				}
			});
		});
		
		$(document).on("click", ".select_marker_icon_image", function() {
			category_marker_icon_input = $(this).parent().find('.marker_icon_image');
			category_marker_icon_tag = $(this).parent().find('.w2mb-marker-icon-tag');

			var dialog = $('<div id="select_marker_icon_dialog"></div>').dialog({
				dialogClass: 'w2mb-content',
				width: ($(window).width()*0.5),
				height: ($(window).height()*0.8),
				modal: true,
				resizable: false,
				draggable: false,
				title: categories_icons.ajax_marker_dialog_title,
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
								if (category_marker_icon_input.val())
									$("#"+category_marker_icon_input.val()).addClass("w2mb-selected-icon");
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
			category_marker_icon_input.val($(this).attr('id'));
			w2mb_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2mb_js_objects.ajaxurl,
				data: {'action': 'w2mb_select_category_marker_icon', 'icon_name': category_marker_icon_input.val(), 'category_id': category_marker_icon_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (response_from_the_action_function != 0) {
						if (category_marker_icon_input) {
							category_marker_icon_tag.removeClass().addClass('w2mb-marker-icon-tag w2mb-fa '+category_marker_icon_input.val());
							category_marker_icon_input = false;
						}
					}
				},
				complete: function() {
					$(this).addClass("w2mb-selected-icon");
					$('#select_marker_icon_dialog').remove();
					w2mb_ajax_loader_hide();
				}
			});
		});
		$(document).on("click", "#w2mb-reset-fa-icon", function() {
			$(".w2mb-selected-icon").removeClass("w2mb-selected-icon");
			category_marker_icon_input.val('');
			w2mb_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2mb_js_objects.ajaxurl,
				data: {'action': 'w2mb_select_category_marker_icon', 'category_id': category_marker_icon_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (category_marker_icon_input) {
						category_marker_icon_tag.removeClass().addClass('w2mb-marker-icon-tag');
						category_marker_icon_input = false;
					}
				},
				complete: function() {
					$('#select_marker_icon_dialog').remove();
					w2mb_ajax_loader_hide();
				}
			});
		});
		
		$(".marker_color").wpColorPicker();
		$(document).on('focus', '.marker_color', function(){
			var parent = $(this).parent();
            $(this).wpColorPicker()
            parent.find('.wp-color-result').trigger('click');
        }); 
		$(document).on("click", ".save_color", function() {
			var category_marker_color_input = $(this).parents(".w2mb-content").find(".marker_color");
			w2mb_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2mb_js_objects.ajaxurl,
				data: {'action': 'w2mb_select_category_marker_color', 'color': category_marker_color_input.val(), 'category_id': category_marker_color_input.parents(".w2mb-content").find(".category_id").val()},
				dataType: 'html',
				complete: function() {
					w2mb_ajax_loader_hide();
				}
			});
		});
	});
})(jQuery);
