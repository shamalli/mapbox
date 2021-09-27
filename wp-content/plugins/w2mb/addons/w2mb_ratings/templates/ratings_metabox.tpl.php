<?php if (get_option('w2mb_manage_ratings') || current_user_can('edit_others_posts')): ?>
<script>
	jQuery(document).ready(function($) {
		"use strict";
		
		$("#w2mb-flush-all-ratings").on('click', function() {
			if (confirm('<?php echo esc_js(esc_html__('Are you sure you want to flush all ratings of this listing?', 'W2MB')); ?>')) {
				w2mb_ajax_loader_show();
				$.ajax({
					type: "POST",
					url: w2mb_js_objects.ajaxurl,
					data: {'action': 'w2mb_flush_ratings', 'post_id': <?php echo esc_js($listing->post->ID); ?>},
					success: function(){
						$(".w2mb-ratings-counts").html('0');
						$(".w2mb-admin-avgvalue").remove();
					},
					complete: function() {
						w2mb_ajax_loader_hide();
					}
				});
			    
			}
		});
	});
</script>
<?php endif; ?>
<div class="w2mb-content w2mb-ratings-metabox">
	<div class="w2mb-admin-avgvalue">
		<span class="w2mb-admin-stars">
			<?php echo esc_html_e('Average', 'W2MB'); ?>
		</span>
		<?php w2mb_renderTemplate(array(W2MB_RATINGS_TEMPLATES_PATH, 'avg_rating.tpl.php'), array('listing' => $listing, 'meta_tags' => false, 'active' => false, 'show_avg' => true)); ?>
	</div>
	<?php foreach ($total_counts AS $rating=>$counts): ?>
	<div class="w2mb-admin-rating">
		<span class="w2mb-admin-stars">
			<?php echo $rating; ?> <?php echo _n('Star ', 'Stars', $rating, 'W2MB'); ?>
		</span>
		<div class="w2mb-rating">
			<div class="w2mb-rating-stars">
				<label class="w2mb-rating-icon w2mb-fa <?php echo ($rating >= 5) ? 'w2mb-fa-star' : 'w2mb-fa-star-o' ?>"></label>
				<label class="w2mb-rating-icon w2mb-fa <?php echo ($rating >= 4) ? 'w2mb-fa-star' : 'w2mb-fa-star-o' ?>"></label>
				<label class="w2mb-rating-icon w2mb-fa <?php echo ($rating >= 3) ? 'w2mb-fa-star' : 'w2mb-fa-star-o' ?>"></label>
				<label class="w2mb-rating-icon w2mb-fa <?php echo ($rating >= 2) ? 'w2mb-fa-star' : 'w2mb-fa-star-o' ?>"></label>
				<label class="w2mb-rating-icon w2mb-fa <?php echo ($rating >= 1) ? 'w2mb-fa-star' : 'w2mb-fa-star-o' ?>"></label>
			</div>
		</div>
	 	&nbsp;&nbsp; - &nbsp;&nbsp;<span class="w2mb-ratings-counts"><?php echo $counts; ?></span>
	 </div>
	<?php endforeach; ?>
	
	<?php if (get_option('w2mb_manage_ratings') || current_user_can('edit_others_posts')): ?>
	<br />
	<input id="w2mb-flush-all-ratings" type="button" class="w2mb-btn w2mb-btn-primary" onClick="" value="<?php esc_attr_e('Flush all ratings', 'W2MB'); ?>" />
	<?php endif; ?>
</div>