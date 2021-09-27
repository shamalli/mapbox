<?php if ($images): ?>
		<style type="text/css">
			<?php if (!empty($height)): ?>
			#w2mb-slider-wrapper-<?php echo esc_attr($id); ?> .w2mb-bx-wrapper,
			#w2mb-slider-wrapper-<?php echo esc_attr($id); ?> .w2mb-bx-viewport {
				height: <?php echo $height+10; ?>px !important;
			}
			<?php endif; ?>
			#w2mb-slider-wrapper-<?php echo esc_attr($id); ?> .slide img {
				<?php if (!empty($height)): ?>
				height: <?php echo $height+10; ?>px !important;
				<?php endif; ?>
				object-fit: <?php if (empty($crop)): ?>contain<?php else: ?>cover<?php endif; ?>;
			}
		</style>
		<?php if (count($images) > 1): ?>
		<script>
			(function($) {
				"use strict";

				$(function() {

					var slider_<?php echo esc_attr($id); ?> = $("#w2mb-slider-<?php echo esc_attr($id); ?>")
					.css("visibility", "hidden")
					.w2mb_bxslider({
						mode: 'fade',
						<?php if (!empty($captions)): ?>
						captions: true,
						<?php endif; ?>
						adaptiveHeight: true,
						adaptiveHeightSpeed: 200,
						<?php if (!empty($slide_width)): ?>
						slideWidth: <?php echo $slide_width; ?>,
						<?php endif; ?>
						<?php if (!empty($max_slides)): ?>
						moveSlides: 1,
						maxSlides: <?php echo $max_slides; ?>,
						<?php endif; ?>
						nextText: '',
						prevText: '',
						<?php if ($pager && count($thumbs) > 1): ?>
						pagerCustom: '#w2mb-bx-pager-<?php echo esc_attr($id); ?>',
						<?php else: ?>
						pager: false,
						<?php endif; ?>
						<?php if (!empty($auto_slides)): ?>
						auto: true,
						autoHover: true,
						pause: <?php echo $auto_slides_delay; ?>,
						<?php endif; ?>
						onSliderLoad: function(){
							this.css("visibility", "visible");
						}
					});
				});
			})(jQuery);
		</script>
		<?php endif; ?>
		<div class="w2mb-content w2mb-slider-wrapper" id="w2mb-slider-wrapper-<?php echo esc_attr($id); ?>" style="<?php if (!empty($width)): ?>max-width: <?php echo esc_attr($width); ?>px; <?php endif; ?>">
			<div class="w2mb-slider" id="w2mb-slider-<?php echo esc_attr($id); ?>">
				<?php foreach ($images AS $image): ?>
				<div class="slide"><?php echo $image; ?></div>
				<?php endforeach; ?>
			</div>
			<?php if ($pager && count($thumbs) > 1): ?>
			<div class="w2mb-bx-pager" id="w2mb-bx-pager-<?php echo esc_attr($id); ?>">
				<?php foreach ($thumbs AS $index=>$thumb): ?><a data-slide-index="<?php echo esc_attr($index); ?>" href=""><?php echo $thumb; ?></a><?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
<?php endif; ?>