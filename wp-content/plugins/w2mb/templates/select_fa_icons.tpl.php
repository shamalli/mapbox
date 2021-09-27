<script>
(function($) {
	"use strict";
	
	$(document).on('keyup', '#search_icon', function() {
		if ($(this).val()) {
			$(".w2mb-icons-theme-block .w2mb-fa-icon").hide();
			$(".w2mb-icons-theme-block .w2mb-fa-icon[id*='"+$(this).val()+"']").show();
		} else
			$(".w2mb-icons-theme-block .w2mb-fa-icon").show();
	});
})(jQuery);
</script>

<div class="w2mb-content">
	<div class="w2mb-row">
		<div class="w2mb-col-md-6 w2mb-form-group w2mb-pull-left">
			<input type="text" id="search_icon" class="w2mb-form-control" placeholder="<?php esc_html_e('Search Icon', 'W2MB'); ?>" />
		</div>
		<div class="w2mb-col-md-6 w2mb-form-group w2mb-pull-right w2mb-text-right">
			<input type="button" id="w2mb-reset-fa-icon" class="w2mb-btn w2mb-btn-primary" value="<?php esc_attr_e('Reset Icon', 'W2MB'); ?>" />
		</div>
		<div class="w2mb-clearfix"></div>
	</div>

	<div class="w2mb-icons-theme-block">
	<?php foreach ($icons AS $icon): ?>
		<span class="w2mb-fa-icon w2mb-fa w2mb-fa-lg <?php echo esc_attr($icon); ?>" id="<?php echo esc_attr($icon); ?>" title="<?php echo esc_attr($icon); ?>"></span>
	<?php endforeach;?>
	</div>
	<div class="w2mb-clearfix"></div>
</div>