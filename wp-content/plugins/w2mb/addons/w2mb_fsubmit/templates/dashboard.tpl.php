<div class="w2mb-content">
	<?php w2mb_renderMessages(); ?>

	<?php $frontpanel_buttons = new w2mb_frontpanel_buttons(array('buttons' => 'submit,logout')); ?>
	<?php $frontpanel_buttons->display(); ?>

	<div class="w2mb-dashboard-tabs-content">
		<ul class="w2mb-dashboard-tabs w2mb-nav w2mb-nav-tabs w2mb-clearfix">
			<li <?php if ($frontend_controller->active_tab == 'listings') echo 'class="w2mb-active"'; ?>><a href="<?php echo w2mb_dashboardUrl(); ?>"><?php esc_html_e('Listings', 'W2MB'); ?> (<?php echo $frontend_controller->listings_count; ?>)</a></li>
			<?php if (get_option('w2mb_allow_edit_profile')): ?>
			<li <?php if ($frontend_controller->active_tab == 'profile') echo 'class="w2mb-active"'; ?>><a href="<?php echo w2mb_dashboardUrl(array('w2mb_action' => 'profile')); ?>"><?php esc_html_e('My profile', 'W2MB'); ?></a></li>
			<?php endif; ?>
			<?php do_action('w2mb_dashboard_links', $frontend_controller); ?>
		</ul>
	
		<div class="w2mb-tab-content w2mb-dashboard">
			<div class="w2mb-tab-pane w2mb-active">
				<?php w2mb_renderTemplate($frontend_controller->subtemplate, $frontend_controller->template_args); ?>
			</div>
		</div>
	</div>
</div>