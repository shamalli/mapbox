(function($) {
	"use strict";
	
	$(function() {
		if (level_categories.level_categories_array.length > 0)
			removeUnnecessaryCategories();
	
		function removeUnnecessaryCategories() {
			$('ul.w2mb-categorychecklist li').each(function(i) {
				if ($(this).find('>ul>li').length > 0) {
					if ($.inArray($(this).find('>label>input[type="checkbox"]').val(), level_categories.level_categories_array) == -1) {
						$(this).find('>label>input[type="checkbox"]').attr('disabled', 'disabled');
						var passed = false;
						$(this).find('ul>li>label>input[type="checkbox"]').each(function() {
							if ($.inArray($(this).val(), level_categories.level_categories_array) != -1) {
								passed = true;
								return false;
							}
						});
						if (!passed) {
							$(this).remove();
							removeUnnecessaryCategories();
							return false;
						}
					}
				} else if ($.inArray($(this).find('>label>input[type="checkbox"]').val(), level_categories.level_categories_array) == -1) {
					$(this).remove();
					removeUnnecessaryCategories();
					return false;
				}
			});
			$("ul.w2mb-categorychecklist ul.children").filter( function() {
			    return $.trim($(this).html()) == '';
			}).remove();
		}
		
		$('ul.w2mb-categorychecklist li').each(function() {
			if ($(this).children('ul').length > 0) {
				$(this).addClass('parent');
				$(this).prepend('<span class="w2mb-category-parent"></span>');
				if ($(this).find('ul input[type="checkbox"]:checked').length > 0)
					$(this).find('.w2mb-category-parent').prepend('<span class="w2mb-category-has-checked"></span>');
			} else
				$(this).prepend('<span class="w2mb-category-empty"></span>');
		});
		$('ul.w2mb-categorychecklist li:not(.active) ul').each(function() {
			$(this).hide();
		});
		$(document).on('click', 'ul.w2mb-categorychecklist li.parent > .w2mb-category-parent', function() {
			$(this).parent().toggleClass('active');
			$(this).parent().children('ul').slideToggle('fast');
		});
		$('ul.w2mb-categorychecklist li input[type="checkbox"]').change(function() {
			$('ul.w2mb-categorychecklist li').each(function() {
				if ($(this).children('ul').length > 0) {
					if ($(this).find('ul input[type="checkbox"]:checked').length > 0) {
						if ($(this).find('.w2mb-category-parent .w2mb-category-has-checked').length == 0)
							$(this).find('.w2mb-category-parent').prepend('<span class="w2mb-category-has-checked"></span>');
					} else
							$(this).find('.w2mb-category-parent .w2mb-category-has-checked').remove();
				}
			});
		});
		
		$("input[name='tax_input\\[w2mb-category\\]\\[\\]']").change(function() {w2mb_manageCategories($(this))});
		$("#w2mb-category-pop input[type=checkbox]").change(function() {w2mb_manageCategories($(this))});
		
		function w2mb_manageCategories(checked_object) {
			if (checked_object.is(":checked") && level_categories.level_categories_number != 'unlimited') {
				if ($("input[name='tax_input\\[w2mb-category\\]\\[\\]']:checked").length > level_categories.level_categories_number) {
					alert(level_categories.level_categories_notice_number);
					$("#in-w2mb-category-"+checked_object.val()).prop("checked", false);
					$("#in-popular-w2mb-category-"+checked_object.val()).prop("checked", false);
					checked_object.trigger("change");
				}
			}
	
			if (checked_object.is(":checked") && level_categories.level_categories_array.length > 0) {
				var result = false;
				if ($.inArray(checked_object.val(), level_categories.level_categories_array) == -1) {
					alert(level_categories.level_categories_notice_disallowed);
					$("#in-w2mb-category-"+checked_object.val()).prop("checked", false);
					$("#in-popular-w2mb-category-"+checked_object.val()).prop("checked", false);
					checked_object.trigger("change");
				} else
					return true;
			} else
				return true;
		}
		
		$(document).on('click', '.w2mb-expand-terms', function() {
			$('ul.w2mb-categorychecklist li.parent').each(function() {
				$(this).addClass('active');
				$(this).children('ul').slideDown('fast');
			});
		});
		$(document).on('click', '.w2mb-collapse-terms', function() {
			$('ul.w2mb-categorychecklist li.parent').each(function() {
				$(this).removeClass('active');
				$(this).children('ul').slideUp('fast');
			});
		});
	});
})(jQuery);
