<?php 

class w2mb_login_registrations {
	
	public function is_action() {
		global $w2mb_instance;
		
		$action = $w2mb_instance->action;
		
		return in_array($action, array('lostpassword', 'resetpass', 'rp', 'register', 'logout', 'login'));
	}
	
	public function get_current_url() {
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = remove_query_arg('w2mb_action', $current_url);
		$current_url = remove_query_arg('redirect_to', $current_url);
		$current_url = remove_query_arg('msg', $current_url);
		
		return $current_url;
	}
	
	public function replace_url_password_message($message, $key, $user_login, $user_data) {
		$current_url = $this->get_current_url();
		
		$url = add_query_arg('w2mb_action', 'rp', $current_url);
		$url = add_query_arg('key', $key, $url);
		$url = add_query_arg('login', rawurlencode($user_login), $url);
		
		$redirect_to = w2mb_getValue($_GET, 'redirect_to', $url);
		$url = add_query_arg('redirect_to', urlencode($redirect_to), $url);
	
		$message = str_replace('<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . '>', '<' . $url . '>', $message);
		
		return $message;
	}
	
	public function replace_new_user_notification_email($wp_new_user_notification_email, $user, $blogname) {
		$current_url = $this->get_current_url();
		
		preg_match_all('/([^?&=#]+)=([^&#]*)/', $wp_new_user_notification_email['message'], $m);
		$message_url_params = array_combine($m[1], $m[2]);
		if (!empty($message_url_params['key'])) {
			$key = $message_url_params['key'];
			$url = add_query_arg('w2mb_action', 'rp', $current_url);
			$url = add_query_arg('key', $key, $url);
			$url = add_query_arg('login', rawurlencode($user->user_login), $url);
			
			$redirect_to = w2mb_getValue($_GET, 'redirect_to', $url);
			$url = add_query_arg('redirect_to', urlencode($redirect_to), $url);
		
			$wp_new_user_notification_email['message'] = str_replace('<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ) . '>', '<' . $url . '>', $wp_new_user_notification_email['message']);
			$wp_new_user_notification_email['message'] = str_replace(wp_login_url(), '', $wp_new_user_notification_email['message']);
		}
		
		return $wp_new_user_notification_email;
	}
	
	public function process($controller = false) {
		global $w2mb_instance;

		$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
		
		$action = $w2mb_instance->action;
		switch ($action) {
			case 'lostpassword':

				if ($http_post) {
					if (w2mb_is_recaptcha_passed()) {
						ob_start();
						require_once (ABSPATH . '/wp-login.php');
						ob_end_clean();
						
						add_action('wp_mail_failed', 'w2mb_error_log');
						
						add_filter('retrieve_password_message', array($this, 'replace_url_password_message'), 10, 4);
						$errors = retrieve_password();
						remove_filter('retrieve_password_message', array($this, 'replace_url_password_message'));
						if (!is_wp_error($errors)) {
							$current_url = $this->get_current_url();
							$current_url = add_query_arg('w2mb_action', 'lostpassword', $current_url);
							$current_url = add_query_arg('msg', 'checkemail', $current_url);
							
							$redirect_to = w2mb_getValue($_GET, 'redirect_to', $current_url);
							$current_url = add_query_arg('redirect_to', urlencode($redirect_to), $current_url);
							
							wp_safe_redirect($current_url);
							exit;
						} else {
							w2mb_addMessage(strip_tags($errors->get_error_message()), 'error');
							
							wp_redirect($_SERVER['HTTP_REFERER']);
							exit;
						}
					} else {
						w2mb_addMessage(esc_html__("Anti-bot test wasn't passed!", 'W2MB'), 'error');
					}
				}
				
				if (w2mb_getValue($_GET, 'msg') == 'checkemail') {
					w2mb_addMessage(esc_html__('Check your email for the confirmation link.', 'W2MB'));
				} elseif (w2mb_getValue($_GET, 'msg') == 'expiredkey') {
					w2mb_addMessage(esc_html__('Your password reset link has expired. Please request a new link below.', 'W2MB'), 'error');
				} else if (w2mb_getValue($_GET, 'msg') == 'invalidkey') {
					w2mb_addMessage(esc_html__('Your password reset link appears to be invalid. Please request a new link below.', 'W2MB'), 'error');
				}
				
				return array(W2MB_FSUBMIT_TEMPLATES_PATH, 'lostpassword_form.tpl.php');
				
				break;
			case 'resetpass':
			case 'rp':
				list($rp_path) = explode('?', wp_unslash( $_SERVER['REQUEST_URI']));
				$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
				if (isset($_GET['key'])) {
					$value = sprintf('%s:%s', wp_unslash($_GET['login']), wp_unslash($_GET['key']));
					setcookie($rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
					wp_safe_redirect(remove_query_arg(array('key', 'login')));
					exit;
				}
				
				if (isset($_COOKIE[$rp_cookie]) && 0 < strpos($_COOKIE[$rp_cookie], ':')) {
					list($rp_login, $rp_key) = explode(':', wp_unslash($_COOKIE[$rp_cookie]), 2 );
					$user = check_password_reset_key($rp_key, $rp_login);
					if (isset($_POST['pass1']) && ! hash_equals($rp_key, $_POST['rp_key'])) {
						$user = false;
					}
					$controller->add_template_args(array('rp_key' => $rp_key));
				} else {
					$user = false;
				}
				
				$current_url = $this->get_current_url();
				
				if (!$user || is_wp_error($user)) {
					setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
					if ($user && $user->get_error_code() === 'expired_key') {
						wp_safe_redirect(add_query_arg(array('w2mb_action' => 'lostpassword', 'msg' => 'expiredkey'), $current_url));
					} else {
						wp_safe_redirect(add_query_arg(array('w2mb_action' => 'lostpassword', 'msg' => 'invalidkey'), $current_url));
					}
					exit;
				}
				
				$errors = new WP_Error();
				
				if (isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2']) {
					w2mb_addMessage(esc_html__('The passwords do not match.', 'W2MB'), 'error');
					$errors->add('password_reset_mismatch', esc_html__('The passwords do not match.', 'W2MB'));
				}
				
				do_action( 'validate_password_reset', $errors, $user );

				if ((!$errors->has_errors() ) && isset($_POST['pass1']) && ! empty( $_POST['pass1'])) {
					$redirect_to = w2mb_getValue($_GET, 'redirect_to', $current_url);
					
					reset_password($user, $_POST['pass1']);
					setcookie($rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
					w2mb_addMessage(esc_html__('Your password has been reset.', 'W2MB'));
					wp_safe_redirect(add_query_arg(array('w2mb_action' => 'login', 'redirect_to' => urlencode($redirect_to)), $current_url));
					exit;
				}
				
				include_once ABSPATH . 'wp-admin/includes/user.php';
				wp_enqueue_script('password-strength-meter');
				wp_enqueue_script('user-profile');
				
				return array(W2MB_FSUBMIT_TEMPLATES_PATH, 'resetpassword_form.tpl.php');
				
				break;
			case 'register':
				if ($http_post) {
					if (isset($_POST['user_login']) && is_string($_POST['user_login'])) {
						$user_login = $_POST['user_login'];
					}
				
					if (isset($_POST['user_email']) && is_string($_POST['user_email'])) {
						$user_email = wp_unslash($_POST['user_email']);
					}
					
					$redirect_to = '';
					if (!empty($_REQUEST['redirect_to'])) {
						$redirect_to = $_REQUEST['redirect_to'];
					}
					
					add_action('wp_mail_failed', 'w2mb_error_log');
				
					if (w2mb_is_recaptcha_passed()) {
						add_filter('wp_new_user_notification_email', array($this, 'replace_new_user_notification_email'), 10, 3);
						$errors = register_new_user($user_login, $user_email);
						remove_filter('wp_new_user_notification_email', array($this, 'replace_new_user_notification_email'));
						if (!is_wp_error($errors)) {
							w2mb_addMessage(esc_html__('Registration complete. Please check your email.', 'W2MB'));
							if ($redirect_to) {
								wp_safe_redirect($redirect_to);
								exit;
							}
						} elseif ($errors->get_error_message()) {
							w2mb_addMessage(strip_tags($errors->get_error_message()), 'error');
						}
					} else {
						w2mb_addMessage(esc_html__("Anti-bot test wasn't passed!", 'W2MB'), 'error');
					}
				}
				
				return array(W2MB_FSUBMIT_TEMPLATES_PATH, 'registration_form.tpl.php');
				
				break;
			case 'logout':
				/* wp_logout();
				
				if ( ! empty( $_REQUEST['redirect_to'] ) ) {
					$redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
				} else {
					$redirect_to           = home_url();
					$requested_redirect_to = '';
				} */
				
				$user = wp_get_current_user();
				
				wp_logout();
				
				if ( ! empty( $_REQUEST['redirect_to'] ) ) {
					$redirect_to           = $_REQUEST['redirect_to'];
					$requested_redirect_to = $redirect_to;
				} else {
					$redirect_to = add_query_arg(
							array(
									'loggedout' => 'true',
									'wp_lang'   => get_user_locale( $user ),
							),
							wp_login_url()
					);
				
					$requested_redirect_to = '';
				}

				$redirect_to = apply_filters( 'logout_redirect', $redirect_to, $requested_redirect_to, $user );
				wp_safe_redirect( $redirect_to );
				exit();
				
				break;
			case 'login':
			default:
				$redirect_to = '';
				if (!empty($_REQUEST['redirect_to'])) {
					$redirect_to = $_REQUEST['redirect_to'];
				}

				if (!empty($_POST)) {
					if (w2mb_is_recaptcha_passed()) {
						$user = wp_signon(array());
		
						if (!is_wp_error($user)) {
							if ($redirect_to) {
								wp_safe_redirect($redirect_to);
								exit;
							}
						} elseif ($user->get_error_message()) {
							w2mb_addMessage(strip_tags($user->get_error_message()), 'error');
						}
					} else {
						w2mb_addMessage(esc_html__("Anti-bot test wasn't passed!", 'W2MB'), 'error');
					}
				}

				return array(W2MB_FSUBMIT_TEMPLATES_PATH, 'login_form.tpl.php');
		}
	}
	
	public function login_template() {
		return array(W2MB_FSUBMIT_TEMPLATES_PATH, 'login_form.tpl.php');
	}
}



function w2mb_login_form($args = array()) {
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg('redirect_to', $current_url);
	$default_redirect = $current_url;

	$current_url = remove_query_arg('w2mb_action', $current_url);

	$redirect_to = w2mb_getValue($_GET, 'redirect_to', $default_redirect);

	$defaults = array(
			'redirect' => $redirect_to, // Default redirect is back to the current page
			'form_id' => 'loginform',
			'label_username' => esc_html__( 'Username', 'W2MB' ),
			'label_password' => esc_html__( 'Password', 'W2MB' ),
			'label_remember' => esc_html__( 'Remember Me', 'W2MB' ),
			'label_log_in' => esc_html__( 'Log In', 'W2MB' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => true,
			'value_username' => '',
			'value_remember' => false, // Set this to true to default the "Remember me" checkbox to checked
	);
	$args = wp_parse_args($args, apply_filters('login_form_defaults', $defaults));

	if (defined('W2MB_DEMO') && W2MB_DEMO) {
		$login = 'demo';
		$pass = 'demo';
	} else {
		$login = esc_attr( $args['value_username'] );
		$pass = '';
	}

	$url = add_query_arg(array('w2mb_action' => 'login', 'redirect_to' => urlencode($args['redirect'])), $current_url);

	echo '<div class="w2mb-content">';

	echo '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . $url . '" method="post" class="w2mb_login_form" role="form">
			' . apply_filters('login_form_top', '', $args) . '
			<div class="w2mb-form-group">
				<label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html( $args['label_username'] ) . '</label>
				<input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="w2mb-form-control" value="' . $login . '" />
			</div>
			<div class="w2mb-form-group">
				<label for="' . esc_attr( $args['id_password'] ) . '">' . esc_html( $args['label_password'] ) . '</label>
				<input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="w2mb-form-control" value="' . $pass . '" />
			</div>
			<div class="w2mb-form-group">
			' . apply_filters( 'login_form_middle', '', $args ) . '
			' . ( $args['remember'] ? '<p class="checkbox"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></p>' : '' );
			if (get_option('w2mb_enable_recaptcha')) {
				echo '<div class="w2mb-form-group">';
				echo w2mb_recaptcha();
				echo '</div>';
			}
			echo '</div>
			<div class="w2mb-form-group">
				<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="w2mb-btn w2mb-btn-primary" value="' . esc_attr( $args['label_log_in'] ) . '" />
				<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
			</div>
			' . apply_filters('login_form_bottom', '', $args) . '
		</form>';

	do_action('login_form');
	do_action('login_footer');
	echo '<p id="nav">';
	if (get_option('users_can_register')) {
		echo '<a href="' . esc_url(wp_registration_url()) . '" rel="nofollow">' . esc_html__('Register', 'W2MB') . '</a> | ';
	}

	echo '<a title="' . esc_attr__('Password Lost and Found', 'W2MB') . '" href="' . esc_url(wp_lostpassword_url()) . '">' . esc_html__('Lost your password?', 'W2MB') . '</a>';
	echo '</p>';

	echo '</div>';
}

function w2mb_resetpassword_form($args = array()) {
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg('redirect_to', $current_url);
	$default_redirect = $current_url;

	$current_url = remove_query_arg('w2mb_action', $current_url);

	$redirect_to = w2mb_getValue($_GET, 'redirect_to', $default_redirect);

	$defaults = array(
			'redirect' => $redirect_to, // Default redirect is back to the current page
			'form_id' => 'resetpassform',
			'label_pass1' => esc_html__( 'New password', 'W2MB' ),
			'label_pass2' => esc_html__( 'Confirm new password', 'W2MB' ),
			'label_submit' => esc_html__( 'Reset Password', 'W2MB' ),
			'id_pass1' => 'password1',
			'id_pass2' => 'password2',
			'id_submit' => 'wp-submit',
			'rp_key' => '',
	);
	$args = wp_parse_args($args, apply_filters('resetpassword_form_defaults', $defaults));

	$url = add_query_arg(array('w2mb_action' => 'resetpass', 'redirect_to' => urlencode($args['redirect'])), $current_url);

	echo '<div class="w2mb-content">';

	echo '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . $url . '" method="post" class="w2mb_login_form" role="form">
			<input type="hidden" name="rp_key" value="' . esc_attr( $args['rp_key'] ) . '" />
			' . apply_filters('resetpassword_form_top', '', $args) . '
			<div class="w2mb-form-group">
				<label for="' . esc_attr( $args['id_pass1'] ) . '">' . esc_html( $args['label_pass1'] ) . '</label>
				<input type="password" name="pass1" id="' . esc_attr($args['id_pass1']) . '" class="w2mb-form-control" value="" />
			</div>
			<div class="w2mb-form-group">
				<label for="' . esc_attr( $args['id_pass2'] ) . '">' . esc_html($args['label_pass2']) . '</label>
				<input type="password" name="pass2" id="' . esc_attr($args['id_pass2']) . '" class="w2mb-form-control" value="" />
			</div>
			<div class="w2mb-form-group">
				<input type="submit" name="wp-submit" id="' . esc_attr($args['id_submit']) . '" class="w2mb-btn w2mb-btn-primary" value="' . esc_attr($args['label_submit']) . '" />
			</div>
		</form>';

	echo '</div>';
}

function w2mb_lostpassword_form($args = array()) {
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg('redirect_to', $current_url);
	$default_redirect = $current_url;

	$current_url = remove_query_arg('w2mb_action', $current_url);
	$current_url = remove_query_arg('msg', $current_url);

	$redirect_to = w2mb_getValue($_GET, 'redirect_to', $default_redirect);

	$defaults = array(
			'redirect' => $redirect_to, // Default redirect is back to the current page
			'form_id' => 'lostpasswordform',
			'label_username' => esc_html__('Username or Email Address', 'W2MB'),
			'label_submit' => esc_html__('Get New Password', 'W2MB'),
			'id_username' => 'user_login',
			'id_submit' => 'wp-submit',
	);
	$args = wp_parse_args($args, apply_filters('lostpassword_form_defaults', $defaults));

	$url = add_query_arg(array('w2mb_action' => 'lostpassword', 'redirect_to' => urlencode($args['redirect'])), $current_url);

	echo '<div class="w2mb-content">';

	echo '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . $url . '" method="post" class="w2mb_login_form" role="form">
			' . apply_filters('lostpassword_form_top', '', $args) . '
			<div class="w2mb-form-group">
				<label for="' . esc_attr($args['id_username']) . '">' . esc_html($args['label_username']) . '</label>
				<input type="text" name="user_login" id="' . esc_attr($args['id_username']) . '" class="w2mb-form-control" value="" />
			</div>';
	if (get_option('w2mb_enable_recaptcha')) {
		echo '<div class="w2mb-form-group">';
		echo w2mb_recaptcha();
		echo '</div>';
	}
	echo '<div class="w2mb-form-group">
				<input type="submit" name="wp-submit" id="' . esc_attr($args['id_submit']) . '" class="w2mb-btn w2mb-btn-primary" value="' . esc_attr($args['label_submit']) . '" />
				<input type="hidden" name="redirect_to" value="' . esc_url($args['redirect']) . '" />
			</div>
			' . apply_filters('lostpassword_form_bottom', '', $args) . '
		</form>';

	echo '<p id="nav">';
	echo '<a href="' . esc_url(wp_login_url()) . '" rel="nofollow">' . esc_html__('Log in', 'W2MB') . '</a>';
	if (get_option('users_can_register')) {
		echo ' | <a href="' . esc_url(wp_registration_url()) . '" rel="nofollow">' . esc_html__('Register', 'W2MB') . '</a>';
	}
	echo '</p>';

	echo '</div>';
}

function w2mb_registration_form($args = array()) {
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg('redirect_to', $current_url);
	$default_redirect = $current_url;

	$current_url = remove_query_arg('w2mb_action', $current_url);

	$redirect_to = w2mb_getValue($_GET, 'redirect_to', $default_redirect);

	$defaults = array(
			'redirect' => $redirect_to, // Default redirect is back to the current page
			'form_id' => 'registerform',
			'label_username' => esc_html__('Username', 'W2MB'),
			'label_email' => esc_html__('Email', 'W2MB'),
			'label_submit' => esc_html__('Register', 'W2MB'),
			'id_username' => 'user_login',
			'id_email' => 'user_email',
			'id_submit' => 'wp-submit',
	);
	$args = wp_parse_args($args, apply_filters('registration_form_defaults', $defaults));

	$url = add_query_arg(array('w2mb_action' => 'register', 'redirect_to' => urlencode($args['redirect'])), $current_url);

	echo '<div class="w2mb-content">';

	echo '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . $url . '" method="post" class="w2mb_login_form" role="form">
			' . apply_filters('registration_form_top', '', $args) . '
			<div class="w2mb-form-group">
				<label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html($args['label_username']) . '</label>
				<input type="text" name="user_login" id="' . esc_attr($args['id_username']) . '" class="w2mb-form-control" value="" />
			</div>
			<div class="w2mb-form-group">
				<label for="' . esc_attr( $args['id_email'] ) . '">' . esc_html( $args['label_email'] ) . '</label>
				<input type="text" name="user_email" id="' . esc_attr($args['id_email']) . '" class="w2mb-form-control" value="" />
			</div>';
	if (get_option('w2mb_enable_recaptcha')) {
		echo '<div class="w2mb-form-group">';
		echo w2mb_recaptcha();
		echo '</div>';
	}
	echo do_action('register_form') . '
			<p id="reg_passmail">' . esc_html__('Registration confirmation will be emailed to you.', 'W2MB') . '</p>
			<div class="w2mb-form-group">
				<input type="submit" name="wp-submit" id="' . esc_attr($args['id_submit']) . '" class="w2mb-btn w2mb-btn-primary" value="' . esc_attr($args['label_submit']) . '" />
				<input type="hidden" name="redirect_to" value="' . esc_url($args['redirect']) . '" />
			</div>
			' . apply_filters('registration_form_bottom', '', $args) . '
		</form>';

	echo '<p id="nav">';
	echo '<a href="' . esc_url(wp_login_url()) . '" rel="nofollow">' . esc_html__('Log in', 'W2MB') . '</a> | ';
	echo '<a title="' . esc_attr__('Password Lost and Found', 'W2MB') . '" href="' . esc_url(wp_lostpassword_url()) . '">' . esc_html__('Lost your password?', 'W2MB') . '</a>';
	echo '</p>';

	echo '</div>';
}

function w2mb_get_login_registration_pages() {
	global $w2mb_instance;

	$pages = array();
	if (!empty($w2mb_instance->submit_page)) {
		$pages[] = array('id' => $w2mb_instance->submit_page['id']);
	}
	if (!empty($w2mb_instance->dashboard_page_id)) {
		$pages[] = array('id' => $w2mb_instance->dashboard_page_id);
	}
	$pages = apply_filters('w2mb_login_registration_pages', $pages);

	return $pages;
}

function w2mb_lostpassword_url($url) {
	$current_page_id = get_the_ID();

	$pages = w2mb_get_login_registration_pages();

	global $wp;
	$current_page_url = home_url(add_query_arg(array($_GET), $wp->request));

	$redirect_to = remove_query_arg('redirect_to', $current_page_url);

	foreach ($pages AS $page) {
		if ($page['id'] == $current_page_id) {
			return add_query_arg(array('w2mb_action' => 'lostpassword', 'redirect_to' => urlencode($redirect_to)), $current_page_url);
			break;
		}
	}

	return $url;
}
add_filter('lostpassword_url', 'w2mb_lostpassword_url', 100);

function w2mb_register_url($url) {
	$current_page_id = get_the_ID();

	$pages = w2mb_get_login_registration_pages();

	global $wp;
	$current_page_url = home_url(add_query_arg(array($_GET), $wp->request));

	$redirect_to = remove_query_arg('redirect_to', $current_page_url);

	foreach ($pages AS $page) {
		if ($page['id'] == $current_page_id) {
			return add_query_arg(array('w2mb_action' => 'register', 'redirect_to' => urlencode($redirect_to)), $current_page_url);
			break;
		}
	}

	return $url;
}
add_filter('register_url', 'w2mb_register_url', 100);

function w2mb_logout_url($url, $redirect_to) {
	$current_page_id = get_the_ID();

	$pages = w2mb_get_login_registration_pages();

	global $wp;
	$current_page_url = home_url(add_query_arg(array($_GET), $wp->request));

	$redirect_to = remove_query_arg('redirect_to', $current_page_url);

	foreach ($pages AS $page) {
		if ($page['id'] == $current_page_id) {
			return add_query_arg(array('w2mb_action' => 'logout', 'redirect_to' => urlencode($redirect_to)), $current_page_url);
			break;
		}
	}

	return $url;
}
add_filter('logout_url', 'w2mb_logout_url', 100, 2);

function w2mb_login_url($url, $redirect_to) {
	$current_page_id = get_the_ID();

	$pages = w2mb_get_login_registration_pages();

	global $wp;
	$current_page_url = home_url(add_query_arg(array($_GET), $wp->request));

	$redirect_to = remove_query_arg('redirect_to', $current_page_url);

	foreach ($pages AS $page) {
		if ($page['id'] == $current_page_id) {
			return add_query_arg(array('w2mb_action' => 'login', 'redirect_to' => urlencode($redirect_to)), $current_page_url);
			break;
		}
	}

	return $url;
}
add_filter('login_url', 'w2mb_login_url', 100, 2);

?>