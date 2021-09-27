<?php

define('W2MB_FSUBMIT_PATH', plugin_dir_path(__FILE__));

function w2mb_fsubmit_loadPaths() {
	define('W2MB_FSUBMIT_TEMPLATES_PATH', W2MB_FSUBMIT_PATH . 'templates/');
	define('W2MB_FSUBMIT_RESOURCES_PATH', W2MB_FSUBMIT_PATH . 'resources/');
	define('W2MB_FSUBMIT_RESOURCES_URL', plugins_url('/', __FILE__) . 'resources/');
}
add_action('init', 'w2mb_fsubmit_loadPaths', 0);

define('W2MB_FSUBMIT_SHORTCODE', 'mapbox-submit');
define('W2MB_DASHBOARD_SHORTCODE', 'mapbox-dashboard');

include_once W2MB_FSUBMIT_PATH . 'classes/dashboard_controller.php';
include_once W2MB_FSUBMIT_PATH . 'classes/submit_controller.php';
include_once W2MB_FSUBMIT_PATH . 'classes/submit_button_controller.php';
include_once W2MB_FSUBMIT_PATH . 'classes/functions.php';
include_once W2MB_FSUBMIT_PATH . 'classes/login_registrations.php';

global $w2mb_wpml_dependent_options;
$w2mb_wpml_dependent_options[] = 'w2mb_tospage';
$w2mb_wpml_dependent_options[] = 'w2mb_submit_login_page';

class w2mb_fsubmit_plugin {

	public function init() {
		global $w2mb_instance, $w2mb_shortcodes, $w2mb_shortcodes_init;
		
		if (!get_option('w2mb_installed_fsubmit'))
			//w2mb_install_fsubmit();
			add_action('init', 'w2mb_install_fsubmit', 0);
		add_action('w2mb_version_upgrade', 'w2mb_upgrade_fsubmit');

		add_filter('w2mb_build_settings', array($this, 'plugin_settings'));

		// add new shortcodes for frontend submission and dashboard
		$w2mb_shortcodes['mapbox-submit'] = 'w2mb_submit_controller';
		$w2mb_shortcodes['mapbox-dashboard'] = 'w2mb_dashboard_controller';
		$w2mb_shortcodes['mapbox-submit-button'] = 'w2mb_submit_button_controller';
		
		$w2mb_shortcodes_init['mapbox-submit'] = 'w2mb_submit_controller';
		$w2mb_shortcodes_init['mapbox-dashboard'] = 'w2mb_dashboard_controller';

		add_shortcode('mapbox-submit', array($w2mb_instance, 'renderShortcode'));
		add_shortcode('mapbox-dashboard', array($w2mb_instance, 'renderShortcode'));
		add_shortcode('mapbox-submit-button', array($w2mb_instance, 'renderShortcode'));
		
		add_action('init', array($this, 'getSubmitPage'), 0);
		add_action('init', array($this, 'getDasboardPage'), 0);

		add_filter('w2mb_get_edit_listing_link', array($this, 'edit_listings_links'), 10, 2);

		add_action('w2mb_listing_frontpanel', array($this, 'add_submit_button'), 10);
		add_action('w2mb_listing_frontpanel', array($this, 'add_claim_button'), 11);
		
		add_action('w2mb_listing_frontpanel', array($this, 'add_logout_button'), 12);

		add_action('init', array($this, 'remove_admin_bar'));

		add_action('transition_post_status', array($this, 'on_listing_approval'), 10, 3);
		add_action('w2mb_post_status_on_activation', array($this, 'post_status_on_activation'), 10, 2);
		
		add_filter('no_texturize_shortcodes', array($this, 'w2mb_no_texturize'));

		add_action('w2mb_render_template', array($this, 'check_custom_template'), 10, 2);
	}
	
	public function w2mb_no_texturize($shortcodes) {
		$shortcodes[] = 'mapbox-submit';
		$shortcodes[] = 'mapbox-dashboard';

		return $shortcodes;
	}
	
	/**
	 * check is there template in one of these paths:
	 * - themes/theme/w2mb-plugin/templates/w2mb_fsubmit/
	 * - plugins/w2mb/templates/w2mb_fsubmit/
	 * 
	 */
	public function check_custom_template($template, $args) {
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			
			if ($template_path == W2MB_FSUBMIT_TEMPLATES_PATH && ($fsubmit_template = w2mb_isTemplate('w2mb_fsubmit/' . $template_file))) {
				return $fsubmit_template;
			}
		}
		return $template;
	}

	public function plugin_settings($options) {
		global $sitepress; // adapted for WPML

		$pages = get_pages();
		$all_pages[] = array('value' => 0, 'label' => esc_html__('- Select page -', 'W2MB'));
		foreach ($pages AS $page)
			$all_pages[] = array('value' => $page->ID, 'label' => $page->post_title);
		
		$options['template']['menus']['general']['controls']['fsubmit'] = array(
			'type' => 'section',
			'title' => esc_html__('Frontend submission and dashboard', 'W2MB'),
			'fields' => array(
				array(
					'type' => 'radiobutton',
					'name' => 'w2mb_fsubmit_login_mode',
					'label' => esc_html__('Frontend submission login mode', 'W2MB'),
					'items' => array(
						array(
							'value' => 1,
							'label' => esc_html__('login required', 'W2MB'),
						),
						array(
							'value' => 2,
							'label' => esc_html__('necessary to fill in user info form', 'W2MB'),
						),
						array(
							'value' => 3,
							'label' => esc_html__('not necessary to fill in user info form', 'W2MB'),
						),
						array(
							'value' => 4,
							'label' => esc_html__('do not show show user info form', 'W2MB'),
						),
					),
					'default' => array(
						get_option('w2mb_fsubmit_login_mode'),
					),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_fsubmit_moderation',
					'label' => esc_html__('Enable pre-moderation of listings', 'W2MB'),
					'default' => get_option('w2mb_fsubmit_moderation'),
					'description' => esc_html__('Moderation will be required for all listings even after payment', 'W2MB'),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_fsubmit_edit_moderation',
					'label' => esc_html__('Enable moderation after a listing was modified', 'W2MB'),
					'default' => get_option('w2mb_fsubmit_edit_moderation'),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_fsubmit_button',
					'label' => esc_html__('Enable submit listing button on listing page', 'W2MB'),
					'default' => get_option('w2mb_fsubmit_button'),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_hide_admin_bar',
					'label' => esc_html__('Hide top admin bar at the frontend for regular users', 'W2MB'),
					'default' => get_option('w2mb_hide_admin_bar'),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_allow_edit_profile',
					'label' => esc_html__('Allow users to manage own profile at the frontend dashboard', 'W2MB'),
					'default' => get_option('w2mb_allow_edit_profile'),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_enable_tags',
					'label' => esc_html__('Enable listings tags input at the frontend', 'W2MB'),
					'default' => get_option('w2mb_enable_tags'),
				),
				array(
					'type' => 'select',
					'name' => w2mb_get_wpml_dependent_option_name('w2mb_tospage'), // adapted for WPML
					'label' => esc_html__('Require Terms of Services on submission page?', 'W2MB'),
					'description' => esc_html__('If yes, create a WordPress page containing your TOS agreement and assign it using the dropdown above.', 'W2MB') . w2mb_get_wpml_dependent_option_description(),
					'items' => $all_pages,
					'default' => (w2mb_get_wpml_dependent_option('w2mb_tospage') ? array(w2mb_get_wpml_dependent_option('w2mb_tospage')) : array(0)), // adapted for WPML
				),
				array(
					'type' => 'select',
					'name' => w2mb_get_wpml_dependent_option_name('w2mb_submit_login_page'), // adapted for WPML
					'label' => esc_html__('Use custom login page for listings submission process', 'W2MB'),
					'description' => esc_html__('You may use any 3rd party plugin to make custom login page and assign it with submission process using the dropdown above.', 'W2MB') . w2mb_get_wpml_dependent_option_description(),
					'items' => $all_pages,
					'default' => (w2mb_get_wpml_dependent_option('w2mb_submit_login_page') ? array(w2mb_get_wpml_dependent_option('w2mb_submit_login_page')) : array(0)), // adapted for WPML
				),
			),
		);
		$options['template']['menus']['general']['controls']['claim'] = array(
			'type' => 'section',
			'title' => esc_html__('Claim functionality', 'W2MB'),
			'fields' => array(
				array(
					'type' => 'toggle',
					'name' => 'w2mb_claim_functionality',
					'label' => esc_html__('Enable claim functionality', 'W2MB'),
					'default' => get_option('w2mb_claim_functionality'),
					'description' => esc_html__('Each listing will get new option "allow claim". Claim button appears on single listings pages only when user is not logged in as current listing owner and a page [mapbox-dashboard] shortcode exists.', 'W2MB'),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_claim_approval',
					'label' => esc_html__('Approval of claim required', 'W2MB'),
					'description' => esc_html__('In other case claim will be processed immediately without any notifications', 'W2MB'),
					'default' => get_option('w2mb_claim_approval'),
				),
				array(
					'type' => 'radiobutton',
					'name' => 'w2mb_after_claim',
					'label' => esc_html__('What will be with listing status after successful approval?', 'W2MB'),
					'description' => esc_html__('When set to expired - renewal may be payment option', 'W2MB'),
					'items' => array(
						array(
							'value' => 'active',
							'label' =>esc_html__('just approval', 'W2MB'),
						),
						array(
							'value' => 'expired',
							'label' =>esc_html__('expired status', 'W2MB'),
						),
					),
					'default' => array(
							get_option('w2mb_after_claim')
					),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_hide_claim_contact_form',
					'label' => esc_html__('Hide contact form on claimable listings', 'W2MB'),
					'default' => get_option('w2mb_hide_claim_contact_form'),
				),
				array(
					'type' => 'toggle',
					'name' => 'w2mb_hide_claim_metabox',
					'label' => esc_html__('Hide claim metabox at the frontend dashboard', 'W2MB'),
					'default' => get_option('w2mb_hide_claim_metabox'),
				),
			),
		);
		
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$options['template']['menus']['advanced']['controls']['wpml']['fields'][] = array(
				'type' => 'toggle',
				'name' => 'w2mb_enable_frontend_translations',
				'label' => esc_html__('Enable frontend translations management', 'W2MB'),
				'default' => get_option('w2mb_enable_frontend_translations'),
			);
		}
		
		$options['template']['menus']['notifications']['controls']['notifications']['fields'][] = array(
			'type' => 'textarea',
			'name' => 'w2mb_newuser_notification',
			'label' => esc_html__('Registration of new user notification', 'W2MB'),
			'default' => get_option('w2mb_newuser_notification'),
			'description' => esc_html__('Tags allowed: ', 'W2MB') . '[author], [listing], [login], [password]',
		);

		$options['template']['menus']['notifications']['controls']['notifications']['fields'][] = array(
			'type' => 'textarea',
			'name' => 'w2mb_newlisting_admin_notification',
			'label' => esc_html__('Notification to admin about new listing creation', 'W2MB'),
			'default' => get_option('w2mb_newlisting_admin_notification'),
			'description' => esc_html__('Tags allowed: ', 'W2MB') . '[user], [listing], [link]',
		);

		$options['template']['menus']['notifications']['controls']['notifications']['fields'][] = array(
			'type' => 'textarea',
			'name' => 'w2mb_editlisting_admin_notification',
			'label' => esc_html__('Notification to admin about listing modification and pending status', 'W2MB'),
			'default' => get_option('w2mb_editlisting_admin_notification'),
			'description' => esc_html__('Tags allowed: ', 'W2MB') . '[user], [listing], [link]',
		);

		$options['template']['menus']['notifications']['controls']['notifications']['fields'][] = array(
			'type' => 'textarea',
			'name' => 'w2mb_approval_notification',
			'label' => esc_html__('Notification to author about successful listing approval', 'W2MB'),
			'default' => get_option('w2mb_approval_notification'),
			'description' => esc_html__('Tags allowed: ', 'W2MB') . '[author], [listing], [link]',
		);

		$options['template']['menus']['notifications']['controls']['notifications']['fields'][] = array(
			'type' => 'textarea',
			'name' => 'w2mb_claim_notification',
			'label' => esc_html__('Notification of claim to current listing owner', 'W2MB'),
			'default' => get_option('w2mb_claim_notification'),
			'description' => esc_html__('Tags allowed: ', 'W2MB') . '[author], [listing], [claimer], [link], [message]',
		);
		$options['template']['menus']['notifications']['controls']['notifications']['fields'][] = array(
			'type' => 'textarea',
			'name' => 'w2mb_claim_approval_notification',
			'label' => esc_html__('Notification of successful approval of claim', 'W2MB'),
			'default' => get_option('w2mb_claim_approval_notification'),
			'description' => esc_html__('Tags allowed: ', 'W2MB') . '[claimer], [listing], [link]',
		);
		$options['template']['menus']['notifications']['controls']['notifications']['fields'][] = array(
			'type' => 'textarea',
			'name' => 'w2mb_claim_decline_notification',
			'label' => esc_html__('Notification of claim decline', 'W2MB'),
			'default' => get_option('w2mb_claim_decline_notification'),
			'description' => esc_html__('Tags allowed: ', 'W2MB') . '[claimer], [listing]',
		);
		
		return $options;
	}

	public function getSubmitPage() {
		global $w2mb_instance, $wpdb;

		if ($pages = $wpdb->get_results("SELECT ID AS id, post_name AS slug FROM {$wpdb->posts} WHERE (post_content LIKE '%[" . W2MB_FSUBMIT_SHORTCODE . "]%' OR post_content LIKE '%[" . W2MB_FSUBMIT_SHORTCODE . " %') AND post_status = 'publish' AND post_type = 'page'", ARRAY_A)) {

			// adapted for WPML
			global $sitepress;
			if (function_exists('wpml_object_id_filter') && $sitepress) {
				foreach ($pages AS $key=>&$cpage) {
					if ($tpage = apply_filters('wpml_object_id', $cpage['id'], 'page')) {
						$cpage['id'] = $tpage;
						$cpage['slug'] = get_post($cpage['id'])->post_name;
					} else {
						unset($pages[$key]);
					}
				}
			}
			
			$pages = array_unique($pages, SORT_REGULAR);
			
			$submit_page = null;
			
			$shortcodes = array(W2MB_FSUBMIT_SHORTCODE);
			foreach ($pages AS $page_id) {
				$page_id = $page_id['id'];
				$pattern = get_shortcode_regex($shortcodes);
				if (preg_match_all('/'.$pattern.'/s', get_post($page_id)->post_content, $matches) && array_key_exists(2, $matches)) {
					foreach ($matches[2] AS $key=>$shortcode) {
						if (in_array($shortcode, $shortcodes)) {
							$submit_page = $page_id;
							break;
						}
					}
				}
			}

			if ($submit_page) {
				$w2mb_instance->submit_page['id'] = $submit_page;
				$w2mb_instance->submit_page['url'] = get_permalink($submit_page);
				$w2mb_instance->submit_page['slug'] = get_post($submit_page)->post_name;
			}
		}

		if (get_option('w2mb_fsubmit_button') && empty($w2mb_instance->submit_page) && is_admin())
			w2mb_addMessage(sprintf(__("You enabled <b>MapBox locator Frontend submission addon</b>: sorry, but there isn't any page with [mapbox-submit] shortcode. Create new page with [mapbox-submit] shortcode or disable Frontend submission addon in settings.", 'W2MB')));
	}

	public function getDasboardPage() {
		global $w2mb_instance, $wpdb, $wp_rewrite;
		
		$w2mb_instance->dashboard_page_url = '';
		$w2mb_instance->dashboard_page_slug = '';
		$w2mb_instance->dashboard_page_id = 0;

		if ($dashboard_page = $wpdb->get_row("SELECT ID AS id, post_name AS slug FROM {$wpdb->posts} WHERE post_content LIKE '%[" . W2MB_DASHBOARD_SHORTCODE . "]%' AND post_status = 'publish' AND post_type = 'page' LIMIT 1", ARRAY_A)) {
			$w2mb_instance->dashboard_page_id = $dashboard_page['id'];
			$w2mb_instance->dashboard_page_slug = $dashboard_page['slug'];

			// adapted for WPML
			global $sitepress;
			if (function_exists('wpml_object_id_filter') && $sitepress) {
				if ($tpage = apply_filters('wpml_object_id', $w2mb_instance->dashboard_page_id, 'page')) {
					$w2mb_instance->dashboard_page_id = $tpage;
					$w2mb_instance->dashboard_page_slug = get_post($w2mb_instance->dashboard_page_id)->post_name;
				}
			}
			
			if ($wp_rewrite->using_permalinks())
				$w2mb_instance->dashboard_page_url = get_permalink($w2mb_instance->dashboard_page_id);
			else
				$w2mb_instance->dashboard_page_url = add_query_arg('page_id', $w2mb_instance->dashboard_page_id, home_url('/'));
		}
	}
	
	public function add_submit_button($frontpanel_buttons) {
		global $w2mb_instance;

		if ($frontpanel_buttons->isButton('submit') && get_option('w2mb_fsubmit_button') && !empty($w2mb_instance->submit_page)) {
			$href = apply_filters('w2mb_submit_button_href', w2mb_submitUrl(), $frontpanel_buttons);
			
			echo '<a class="w2mb-submit-listing-link w2mb-btn w2mb-btn-primary" href="' . $href . '" rel="nofollow" ' . $frontpanel_buttons->tooltipMeta(esc_html__('Submit new listing', 'W2MB'), true) . '><span class="w2mb-glyphicon w2mb-glyphicon-plus"></span> ' . ((!$frontpanel_buttons->hide_button_text) ? esc_html__('Submit new listing', 'W2MB') : "") . '</a> ';
		}
	}

	public function add_claim_button($frontpanel_buttons) {
		global $w2mb_instance;
		
		if ($frontpanel_buttons->isButton('claim')) {
			if ($listing = w2mb_getListing($frontpanel_buttons->getListingId())) {
				if ($listing && $listing->is_claimable && $w2mb_instance->dashboard_page_url && get_option('w2mb_claim_functionality') && $listing->post->post_author != get_current_user_id()) {
					$href = w2mb_dashboardUrl(array('listing_id' => $listing->post->ID, 'w2mb_action' => 'claim_listing'));
					
					$href = apply_filters('w2mb_claim_button_href', $href, $frontpanel_buttons);
					
					echo '<a class="w2mb-claim-listing-link w2mb-btn w2mb-btn-primary" href="' . $href . '" rel="nofollow" ' . $frontpanel_buttons->tooltipMeta(esc_html__('Is this your ad?', 'W2MB'), true) . '><span class="w2mb-glyphicon w2mb-glyphicon-flag"></span> ' . ((!$frontpanel_buttons->hide_button_text) ? esc_html__('Is this your ad?', 'W2MB') : "") . '</a> ';
				}
			}
		}
	}

	public function add_logout_button($frontpanel_buttons) {
		if ($frontpanel_buttons->isButton('logout')) {
			echo '<a class="w2mb-logout-link w2mb-btn w2mb-btn-primary" href="' . wp_logout_url(w2mb_dashboardUrl()) . '" rel="nofollow" ' . $frontpanel_buttons->tooltipMeta(esc_html__('Log out', 'W2MB'), true) . '><span class="w2mb-glyphicon w2mb-glyphicon-log-out"></span> ' . ((!$frontpanel_buttons->hide_button_text) ? esc_html__('Log out', 'W2MB') : "") . '</a>';
		}
	}
	
	public function remove_admin_bar() {
		if (get_option('w2mb_hide_admin_bar') && !current_user_can('manage_options') && !current_user_can('editor') && !is_admin()) {
			show_admin_bar(false);
			add_filter('show_admin_bar', '__return_false', 99999);
		}
	}

	public function edit_listings_links($url, $post_id) {
		global $w2mb_instance;

		if (!is_admin() && $w2mb_instance->dashboard_page_url && ($post = get_post($post_id)) && $post->post_type == W2MB_POST_TYPE)
			return w2mb_dashboardUrl(array('w2mb_action' => 'edit_listing', 'listing_id' => $post_id));
	
		return $url;
	}

	public function on_listing_approval($new_status, $old_status, $post) {
		if (get_option('w2mb_approval_notification')) {
			if (
				$post->post_type == W2MB_POST_TYPE &&
				'publish' == $new_status &&
				'pending' == $old_status &&
				($listing = w2mb_getListing($post)) &&
				($author = get_userdata($listing->post->post_author))
			) {
				update_post_meta($post->ID, '_listing_approved', true);

				$subject = esc_html__('Approval of listing', 'W2MB');
					
				$body = str_replace('[author]', $author->display_name,
						str_replace('[listing]', $listing->post->post_title,
						str_replace('[link]', w2mb_dashboardUrl(),
				get_option('w2mb_approval_notification'))));

				w2mb_mail($author->user_email, $subject, $body);
			}
		}
	}
	
	public function post_status_on_activation($status, $listing) {
		$is_moderation = get_post_meta($listing->post->ID, '_requires_moderation', true);
		$is_approved = get_post_meta($listing->post->ID, '_listing_approved', true);
		if (!$is_moderation || ($is_moderation && $is_approved)) {
			return 'publish';
		} elseif ($is_moderation && !$is_approved) {
			return 'pending';
		}
		return $status;
	}

	public function enqueue_login_scripts_styles() {
		global $action;
		$action = 'login';
		do_action('login_enqueue_scripts');
		do_action('login_head');
	}
}

function w2mb_install_fsubmit() {
	add_option('w2mb_fsubmit_login_mode', 1);
	add_option('w2mb_fsubmit_button', 1);
	add_option('w2mb_hide_admin_bar', 0);
	add_option('w2mb_newuser_notification', 'Hello [author],
your listing "[listing]" was successfully submitted.

You may manage your listing using following credentials:
login: [login]
password: [password]');
	add_option('w2mb_newlisting_admin_notification', 'Hello,
user [user] created new listing "[listing]".
	
You may manage this listing at
[link]');
	add_option('w2mb_allow_edit_profile', 1);
	add_option('w2mb_enable_frontend_translations', 1);
	add_option('w2mb_enable_tags', 1);
	add_option('w2mb_tospage', "0");
	add_option('w2mb_submit_login_page', "0");
	
	w2mb_upgrade_fsubmit('1.2.0');
	w2mb_upgrade_fsubmit('2.0.0');
	
	if (
		get_option('w2mb_newuser_notification') &&
		get_option('w2mb_claim_notification') &&
		get_option('w2mb_claim_approval_notification') &&
		get_option('w2mb_newlisting_admin_notification') &&
		get_option('w2mb_approval_notification') &&
		get_option('w2mb_claim_decline_notification') &&
		get_option('w2mb_editlisting_admin_notification')
	) {
		add_option('w2mb_installed_fsubmit', 1);
	}
}

function w2mb_upgrade_fsubmit($new_version) {
	if ($new_version == '1.2.0') {
		add_option('w2mb_approval_notification', 'Hello [author],
	
your listing "[listing]" was successfully approved.
	
Now you may manage your listing at the dashboard
[link]');
	}
	
	if ($new_version == '2.0.0') {
		add_option('w2mb_fsubmit_moderation', 0);
		add_option('w2mb_fsubmit_edit_moderation', 0);
		add_option('w2mb_claim_functionality', 0);
		add_option('w2mb_claim_approval', 1);
		add_option('w2mb_after_claim', 'active');
		add_option('w2mb_hide_claim_contact_form', 0);
		add_option('w2mb_hide_claim_metabox', 0);
		add_option('w2mb_claim_notification', 'Hello [author],
		
your listing "[listing]" was claimed by [claimer].
		
You may approve or reject this claim at
[link]
		
[message]');
		add_option('w2mb_claim_approval_notification', 'Hello [claimer],
		
congratulations, your claim for listing "[listing]" was successfully approved.
		
Now you may manage your listing at the dashboard
[link]');
		add_option('w2mb_claim_decline_notification', 'Hello [claimer],
		
your claim for listing "[listing]" was declined.');
		add_option('w2mb_editlisting_admin_notification', 'Hello,
		
user [user] modified listing "[listing]". Now it is pending review.
		
You may manage this listing at
[link]');
	}
}

global $w2mb_fsubmit_instance;

$w2mb_fsubmit_instance = new w2mb_fsubmit_plugin();
$w2mb_fsubmit_instance->init();

?>
