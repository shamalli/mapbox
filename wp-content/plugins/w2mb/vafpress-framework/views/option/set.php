<div class="wrap">
	<h2><?php echo $set->get_title(); ?></h2>
	<?php echo do_action('w2mb_settings_panel_top'); ?>
	<div id="vp-wrap" class="vp-wrap">
		<div id="vp-option-panel"class="vp-option-panel <?php echo ($set->get_layout() === 'fixed') ? 'fixed-layout' : 'fluid-layout' ; ?>">
			<div class="vp-left-panel">
				<div id="vp-logo" class="vp-logo">
					<img src="<?php echo VP_W2MB_Util_Res::img($set->get_logo()); ?>" alt="<?php echo $set->get_title(); ?>" />
				</div>
				<div id="vp-menus" class="vp-menus">
					<ul class="vp-menu-level-1">
						<?php foreach ($set->get_menus() as $menu): ?>
						<?php $menus          = $set->get_menus(); ?>
						<?php $is_first_lvl_1 = $menu === reset($menus); ?>
						<?php if ($is_first_lvl_1): ?>
						<li class="vp-current">
						<?php else: ?>
						<li>
						<?php endif; ?>
							<?php if ($menu->get_menus()): ?>
							<a href="#<?php echo $menu->get_name(); ?>" class="vp-js-menu-dropdown vp-menu-dropdown">
							<?php else: ?>
							<a href="#<?php echo $menu->get_name(); ?>" class="vp-js-menu-goto vp-menu-goto">
							<?php endif; ?>
								<?php
								$icon = $menu->get_icon();
								$font_awesome = VP_W2MB_Util_Res::is_font_awesome($icon);
								if ($font_awesome !== false):
									VP_W2MB_Util_Text::print_if_exists($font_awesome, '<i class="w2mb-fa %s"></i>');
								else:
									VP_W2MB_Util_Text::print_if_exists(VP_W2MB_Util_Res::img($icon), '<i class="custom-menu-icon" style="background-image: url(\'%s\');"></i>');
								endif;
								?>
								<span><?php echo $menu->get_title(); ?></span>
							</a>
							<?php if ($menu->get_menus()): ?>
							<ul class="vp-menu-level-2">
								<?php foreach ($menu->get_menus() as $submenu): ?>
								<?php $submenus = $menu->get_menus(); ?>
								<?php if ($is_first_lvl_1 and $submenu === reset($submenus)): ?>
								<li class="vp-current">
								<?php else: ?>
								<li>
								<?php endif; ?>
									<a href="#<?php echo $submenu->get_name(); ?>" class="vp-js-menu-goto vp-menu-goto">
										<?php
										$sub_icon = $submenu->get_icon();
										$font_awesome = VP_W2MB_Util_Res::is_font_awesome($sub_icon);
										if ($font_awesome !== false):
											VP_W2MB_Util_Text::print_if_exists($font_awesome, '<i class="w2mb-fa %s"></i>');
										else:
											VP_W2MB_Util_Text::print_if_exists(VP_W2MB_Util_Res::img($sub_icon), '<i class="custom-menu-icon" style="background-image: url(\'%s\');"></i>');
										endif;
										?>
										<span><?php echo $submenu->get_title(); ?></span>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div class="vp-right-panel">
				<form id="vp-option-form" class="vp-option-form vp-js-option-form" method="POST">
					<div id="vp-submit-top" class="vp-submit top">
						<div class="inner">
							<input class="vp-save vp-button button button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'W2MB'); ?>" />
							<p class="vp-js-save-loader save-loader" style="display: none;"><img src="<?php VP_W2MB_Util_Res::img_out('ajax-loader.gif', ''); ?>" /><?php _e('Saving Now', 'W2MB'); ?></p>
							<p class="vp-js-save-status save-status" style="display: none;"></p>
						</div>
					</div>
					<?php foreach ($set->get_menus() as $menu): ?>
					<?php $menus = $set->get_menus(); ?>
					<?php if ($menu === reset($menus)): ?>
						<?php echo $menu->render(array('current' => 1)); ?>
					<?php else: ?>
						<?php echo $menu->render(array('current' => 0)); ?>
					<?php endif; ?>
					<?php endforeach; ?>
					<div id="vp-submit-bottom" class="vp-submit bottom">
						<div class="inner">
							<input class="vp-save vp-button button button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'W2MB'); ?>" />
							<p class="vp-js-save-loader save-loader" style="display: none;"><img src="<?php VP_W2MB_Util_Res::img_out('ajax-loader.gif', ''); ?>" /><?php _e('Saving Now', 'W2MB'); ?></p>
							<p class="vp-js-save-status save-status" style="display: none;"></p>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div id="vp-copyright" class="vp-copyright">
			<p><?php printf(__('This option panel is built using Vafpress Framework %s powered by Vafpress', 'W2MB'), VP_W2MB_VERSION); ?></p>
		</div>
	</div>
	<?php echo do_action('w2mb_settings_panel_bottom'); ?>
</div>