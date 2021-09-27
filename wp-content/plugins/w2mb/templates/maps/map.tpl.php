<div class="w2mb-content">
	<script>
		if (typeof w2mb_map_markers_attrs_array == "undefined") {
			alert("<?php esc_html_e("MapBox plugin: Please, enable 'Include plugin JS and CSS files on all pages' on the MapBox Advanced settings tab.", "W2MB") ?>");
		} else {
			w2mb_map_markers_attrs_array.push(new w2mb_map_markers_attrs('<?php echo esc_attr($map_id); ?>', eval(<?php echo $locations_options; ?>), <?php echo $map_args; ?>));
		}
	</script>
	
	<?php
	if ($search_form) {
		$search_form = new w2mb_search_map_form($map_id, $controller, $args, $listings_content, $locations_array);
	}
	?>
	<div id="w2mb-maps-canvas-wrapper-<?php echo esc_attr($map_id); ?>" class="w2mb-maps-canvas-wrapper <?php if ($directions_sidebar_open) echo 'w2mb-directions-sidebar-open'; ?> <?php if ($search_form && $search_form->isCategoriesOrKeywords()) echo 'w2mb-map-search-input-enabled'; ?> <?php if ($sticky_scroll):?>w2mb-sticky-scroll<?php endif; ?>" data-id="<?php echo esc_attr($map_id); ?>" <?php if ($sticky_scroll_toppadding):?>data-toppadding="<?php echo esc_attr($sticky_scroll_toppadding); ?>"<?php endif; ?> data-height="<?php echo esc_attr($height); ?>">
		<?php if ($directions_sidebar): ?>
		<?php w2mb_renderTemplate('maps/directions_sidebar.tpl.php', array('map_id' => $map_id))?>
		<?php endif; ?>
		<?php
		if ($search_form) {
			echo $search_form->display();
		} ?>
		<div id="w2mb-maps-canvas-<?php echo esc_attr($map_id); ?>" class="w2mb-maps-canvas <?php if ($directions_sidebar_open) echo 'w2mb-directions-sidebar-open'; ?> <?php if ($search_form && !empty($args['search_on_map_open'])) echo 'w2mb-sidebar-open'; ?>" data-shortcode-hash="<?php echo esc_attr($map_id); ?>" style="<?php if ($width) echo 'max-width:' . $width . 'px'; ?> height: <?php if ($height) { if ($height == '100%') echo '100%'; else echo esc_attr($height).'px'; } else echo '300px'; ?>"></div>
	</div>
	
	<?php if (current_user_can("manage_options") && isset($map_object->args['id'])): ?>
	<a href="<?php echo get_edit_post_link($map_object->args['id']); ?>" class="w2mb-btn w2mb-btn-primary"><?php esc_html_e("Edit Map", "W2MB")?></a>
	<?php endif; ?>
</div>