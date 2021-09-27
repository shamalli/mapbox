	<script>
		(function($) {
			"use strict";

			$(function() {
				<?php $align = (is_rtl() ? 'right' : 'left' ); ?>
				$(document).on('mouseenter', '.w2mb-no-touch #w2mb-color-picker-panel', function () {
					$('#w2mb-color-picker-panel').stop().animate({<?php echo esc_js($align); ?>: "0px"}, 500);
				});
				$(document).on('mouseleave', '.w2mb-no-touch #w2mb-color-picker-panel', function () {
					var width = $('#w2mb-color-picker-panel').width() - 50;
					$('#w2mb-color-picker-panel').stop().animate({<?php echo esc_js($align); ?>: - width}, 500);
				});
	
				var panel_opened = false;
				$('html').on('click', '.w2mb-touch #w2mb-color-picker-panel-tools', function () {
					if (panel_opened) {
						var width = $('#w2mb-color-picker-panel').width() - 50;
						$('#w2mb-color-picker-panel').stop().animate({<?php echo esc_js($align); ?>: - width}, 500);
						panel_opened = false;
					} else {
						$('#w2mb-color-picker-panel').stop().animate({<?php echo esc_js($align); ?>: "0px"}, 500);
						panel_opened = true;
					}
				});
			});
		})(jQuery);
	</script>
	<div id="w2mb-color-picker-panel" class="w2mb-content">
		<fieldset id="w2mb-color-picker">
			<label><?php esc_html_e('Choose color palette:'); ?></label>
			<?php $selected_scheme = (isset($_COOKIE['w2mb_compare_palettes']) ? $_COOKIE['w2mb_compare_palettes'] : get_option('w2mb_color_scheme')); ?>
			<?php w2mb_renderTemplate('color_picker/color_picker_settings.tpl.php', array('selected_scheme' => $selected_scheme)); ?>
			<label><?php printf(__('Return to the <a href="%s">backend</a>', 'W2MB'), admin_url('admin.php?page=w2mb_settings#_customization')); ?></label>
		</fieldset>
		<div id="w2mb-color-picker-panel-tools" class="clearfix">
			<img src="<?php echo W2MB_RESOURCES_URL . 'images/settings.png'; ?>" />
		</div>
	</div>