<h3><?php echo sprintf(esc_html__('Clicks statistics of listing "%s"', 'W2MB'), $w2mb_instance->current_listing->title()); ?></h3>

<h4><?php echo sprintf(esc_html__('Total clicks: %d', 'W2MB'), (get_post_meta($w2mb_instance->current_listing->post->ID, '_total_clicks', true) ? get_post_meta($w2mb_instance->current_listing->post->ID, '_total_clicks', true) : 0)); ?></h4>

<?php 
$months_names = array(
	1 => esc_html__('January', 'W2MB'),	
	2 => esc_html__('February', 'W2MB'),	
	3 => esc_html__('March', 'W2MB'),	
	4 => esc_html__('April', 'W2MB'),	
	5 => esc_html__('May', 'W2MB'),	
	6 => esc_html__('June', 'W2MB'),	
	7 => esc_html__('July', 'W2MB'),	
	8 => esc_html__('August', 'W2MB'),	
	9 => esc_html__('September', 'W2MB'),	
	10 => esc_html__('October', 'W2MB'),	
	11 => esc_html__('November', 'W2MB'),	
	12 => esc_html__('December', 'W2MB'),	
);
if ($clicks_data = get_post_meta($w2mb_instance->current_listing->post->ID, '_clicks_data', true)) {
	foreach ($clicks_data AS $month_year=>$count) {
		$month_year = explode('-', $month_year);
		$data[$month_year[1]][$month_year[0]] = $count;
	}
	ksort($data);
}
?>

<?php if (isset($data)): ?>
<div>
	<?php foreach ($data AS $year=>$months_counts): ?>
	<h4><?php echo $year; ?></h4>

	<div>
		<canvas id="canvas-<?php echo esc_attr($year); ?>" class="w2mb-stats-canvas"></canvas>
		<script>
			var chartData_<?php echo esc_attr($year); ?> = {
				labels : ["<?php echo implode('","', $months_names); ?>"],
				datasets : [
					{
						fillColor : "rgba(151,187,205,0.2)",
						strokeColor : "rgba(151,187,205,1)",
						<?php
						foreach ($months_names AS $month_num=>$name)
							if (!isset($months_counts[$month_num]))
								$months_counts[$month_num] = 0;
						ksort($months_counts);?>
						data : [<?php echo implode(',', $months_counts); ?>]
					}
				]
			};

			(function($) {
				"use strict";

				$(function() {
					var ctx_<?php echo esc_attr($year); ?> = document.getElementById("canvas-<?php echo esc_attr($year); ?>").getContext("2d");
					window.myLine_<?php echo esc_attr($year); ?> = new Chart(ctx_<?php echo esc_attr($year); ?>).Bar(chartData_<?php echo esc_attr($year); ?>, {
						responsive: true
					});
				});
			})(jQuery);
		</script>
	</div>
	<hr />
	<?php endforeach; ?>
</div>
<?php endif; ?>

<a href="<?php echo $frontend_controller->referer; ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e('Go back ', 'W2MB'); ?></a>