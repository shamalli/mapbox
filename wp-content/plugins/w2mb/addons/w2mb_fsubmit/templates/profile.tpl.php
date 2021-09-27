	<form action="" method="POST" role="form" class="w2mb-dashboard-profile">
		<input type="hidden" name="referer" value="<?php echo $frontend_controller->referer; ?>" />
		<input type="hidden" name="rich_editing" value="<?php echo ($frontend_controller->user->rich_editing) ? 1 : 0; ?>" />
		<input type="hidden" name="admin_color" value="<?php echo ($frontend_controller->user->admin_color) ? $frontend_controller->user->admin_color : 'fresh'; ?>" />
		<input type="hidden" name="admin_bar_front" value="<?php echo ($frontend_controller->user->show_admin_bar_front) ? 1 : 0; ?>" />

		<div class="w2mb-form-group">
			<p>
				<label for="user_login"><?php esc_html_e('Username', 'W2MB'); ?></label>
				<input type="text" name="user_login" class="w2mb-form-control" value="<?php echo esc_attr($frontend_controller->user->user_login); ?>" disabled="disabled" /> <span class="description"><?php esc_html_e('Usernames cannot be changed.', 'W2MB'); ?></span>
			</p>
			<p>
				<label for="first_name"><?php esc_html_e('First Name', 'W2MB') ?></label>
				<input type="text" name="first_name" class="w2mb-form-control" value="<?php echo esc_attr($frontend_controller->user->first_name); ?>" />
			</p>
			<p>
				<label for="last_name"><?php esc_html_e('Last Name', 'W2MB') ?></label>
				<input type="text" name="last_name" class="w2mb-form-control" value="<?php echo esc_attr($frontend_controller->user->last_name); ?>" />
			</p>
			<p>
				<label for="nickname"><?php esc_html_e('Nickname', 'W2MB') ?> <span class="description"><?php esc_html_e('(required)', 'W2MB'); ?></span></label>
				<input type="text" name="nickname" class="w2mb-form-control" value="<?php echo esc_attr($frontend_controller->user->nickname); ?>" />
			</p>
			<p>
				<label for="display_name"><?php esc_html_e('Display to Public as', 'W2MB') ?></label>
				<select name="display_name" class="w2mb-form-control">
				<?php
					foreach ($public_display as $id => $item) {
				?>
					<option id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($item); ?>"<?php selected($frontend_controller->user->display_name, $item); ?>><?php echo $item; ?></option>
				<?php
					}
				?>
				</select>
			</p>
			<p>
				<label for="email"><?php esc_html_e('E-mail', 'W2MB'); ?> <span class="description"><?php esc_html_e('(required)', 'W2MB'); ?></span></label>
				<input type="text" name="email" class="w2mb-form-control" value="<?php echo esc_attr($frontend_controller->user->user_email); ?>" />
			</p>
			<?php if (!get_option('w2mb_hide_author_link')): ?>
			<p>
				<label for="email"><?php esc_html_e('Website', 'W2MB'); ?></label>
				<input type="text" name="url" class="w2mb-form-control" value="<?php echo esc_url(get_the_author_meta('url')); ?>" />
			</p>
			<?php endif; ?>
			<?php if (get_option('w2mb_payments_addon')): ?>
			<h3><?php esc_html_e('Billing information', 'W2MB'); ?></h3>
			<p>
				<label for="w2mb_billing_name"><?php esc_html_e('Full name', 'W2MB'); ?></label>
				<input type="text" name="w2mb_billing_name" class="w2mb-form-control" value="<?php echo esc_attr($frontend_controller->user->w2mb_billing_name) ?>" />
			</p>
			<p>
				<label for="w2mb_billing_address"><?php esc_html_e('Full Address', 'W2MB'); ?></label>
				<textarea name="w2mb_billing_address" id="w2mb_billing_address" class="w2mb-form-control" rows="3"><?php echo esc_textarea($frontend_controller->user->w2mb_billing_address); ?></textarea>
			</p>
			<?php endif; ?>
			 <div>
			 	<label for="pass1"><?php esc_html_e('New Password', 'W2MB'); ?></label>
			 	<div class="user-pass1-wrap">
					<button type="button" class="button button-secondary wp-generate-pw hide-if-no-js"><?php esc_html_e('Generate Password', 'W2MB'); ?></button>
					<div class="wp-pwd hide-if-js">
						<span class="password-input-wrapper">
							<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr(wp_generate_password(24)); ?>" aria-describedby="pass-strength-result" />
							<div class="user-pass2-wrap hide-if-js"><input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="off" /></div>
						</span>
						<button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e('Hide password', 'W2MB'); ?>">
							<span class="dashicons dashicons-hidden"></span>
							<span class="text"><?php esc_html_e('Hide', 'W2MB'); ?></span>
						</button>
						<button type="button" class="button button-secondary wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e('Cancel password change', 'W2MB'); ?>">
							<span class="text"><?php esc_html_e('Cancel', 'W2MB'); ?></span>
						</button>
						<div class="w2mb-display-none" id="pass-strength-result" aria-live="polite"></div>
					</div>
			 	</div>
			 </div>
		</div>

		<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($frontend_controller->user->ID); ?>" />
		<?php require_once(ABSPATH . 'wp-admin/includes/template.php'); ?>
		<?php submit_button(esc_html__('Save changes', 'W2MB'), 'w2mb-btn w2mb-btn-primary'); ?>
	</form>