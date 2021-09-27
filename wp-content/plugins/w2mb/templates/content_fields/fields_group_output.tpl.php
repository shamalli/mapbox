<div class="w2mb-fields-group" id="w2mb-fields-group-<?php echo esc_attr($content_fields_group->id); ?>">
	<?php if (!$content_fields_group->on_tab): ?>
	<div class="w2mb-fields-group-caption"><?php echo $content_fields_group->name; ?></div>
	<?php endif; ?>
	<?php if (!$content_fields_group->hide_anonymous || is_user_logged_in()): ?>
		<?php foreach ($content_fields_group->content_fields_array AS $content_field): ?>
			<?php if ((!$is_single || ($is_single && $content_field->on_listing_page)) && $content_field->isNotEmpty($listing) && $content_field->filterForAdmins($listing)): ?>
				<?php $content_field->renderOutput($listing, $content_fields_group); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php elseif ($content_fields_group->hide_anonymous && !is_user_logged_in()): ?>
		<?php printf(esc_html__('You must be <a href="%s">logged in</a> to see this info', 'W2MB'), wp_login_url(get_permalink($listing->post->ID))); ?>
	<?php endif; ?>
</div>