<?php

class w2mb_admin {

	public function __construct() {
		global $w2mb_instance;

		add_action('admin_menu', array($this, 'menu'));

		$w2mb_instance->settings_manager = new w2mb_settings_manager;

		$w2mb_instance->listings_manager = new w2mb_listings_manager;
		
		$w2mb_instance->maps_manager = new w2mb_maps_manager;

		$w2mb_instance->locations_manager = new w2mb_locations_manager;

		$w2mb_instance->locations_levels_manager = new w2mb_locations_levels_manager;

		$w2mb_instance->categories_manager = new w2mb_categories_manager;

		$w2mb_instance->content_fields_manager = new w2mb_content_fields_manager;

		$w2mb_instance->media_manager = new w2mb_media_manager;

		$w2mb_instance->csv_manager = new w2mb_csv_manager;
		
		$w2mb_instance->comments_manager = new w2mb_comments_manager;
		
		$w2mb_instance->demo_data_manager = new w2mb_demo_data_manager;
		
		add_filter('w2mb_build_settings', array($this, 'addAddonsSettings'));

		// hide some meta-blocks when create/edit posts
		add_action('admin_init', array($this, 'hideMetaBoxes'));
		add_filter('default_hidden_meta_boxes', array($this, 'showAuthorMetaBox'), 10, 2);
		
		add_action('admin_head-post-new.php', array($this, 'hidePreviewButton'));
		
		add_filter('post_row_actions', array($this, 'removeQuickEdit'), 10, 2);
		add_filter('quick_edit_show_taxonomy', array($this, 'removeQuickEditTax'), 10, 2);

		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'), 0);
		
		add_filter('admin_body_class', array($this, 'addBodyClasses'));

		add_action('wp_ajax_w2mb_generate_color_palette', array($this, 'generate_color_palette'));
		add_action('wp_ajax_nopriv_w2mb_generate_color_palette', array($this, 'generate_color_palette'));
		add_action('wp_ajax_w2mb_get_jqueryui_theme', array($this, 'get_jqueryui_theme'));
		add_action('wp_ajax_nopriv_w2mb_get_jqueryui_theme', array($this, 'get_jqueryui_theme'));
		add_action('vp_w2mb_option_before_ajax_save', array($this, 'remove_colorpicker_cookie'));
		add_action('wp_footer', array($this, 'render_palette_picker'));
		
		add_action('admin_notices', array($this, 'renderAdminMessages'));
	}
	
	public function renderAdminMessages() {
		global $pagenow;
	
		if ((($pagenow == 'edit.php' || $pagenow == 'post-new.php') && ($post_type = w2mb_getValue($_GET, 'post_type')) &&
				(in_array($post_type, array(W2MB_POST_TYPE, W2MB_MAP_TYPE)))
		) ||
		($pagenow == 'post.php' && ($post_id = w2mb_getValue($_GET, 'post')) && ($post = get_post($post_id)) &&
				(in_array($post->post_type, array(W2MB_POST_TYPE, W2MB_MAP_TYPE)))
		) ||
		(($pagenow == 'edit-tags.php' || $pagenow == 'term.php') && ($taxonomy = w2mb_getValue($_GET, 'taxonomy')) &&
				(in_array($taxonomy, array(W2MB_LOCATIONS_TAX, W2MB_CATEGORIES_TAX, W2MB_TAGS_TAX)))
		)) {
			w2mb_renderMessages();
		}
	}

	public function menu() {
		if (defined('W2MB_DEMO') && W2MB_DEMO) {
			$capability = 'publish_posts';
		} else {
			$capability = 'manage_options';
		}

		add_menu_page(esc_html__("Maps settings", "W2MB"),
			esc_html__('Maps Admin', 'W2MB'),
			$capability,
			'w2mb_settings',
			'',
			W2MB_RESOURCES_URL . 'images/menuicon.png'
		);
		add_submenu_page(
			'w2mb_settings',
			esc_html__("Maps settings", "W2MB"),
			esc_html__("Maps settings", "W2MB"),
			$capability,
			'w2mb_settings'
		);

		add_submenu_page(
			'',
			esc_html__("Maps Debug", "W2MB"),
			esc_html__("Maps Debug", "W2MB"),
			$capability,
			'w2mb_debug',
			array($this, 'debug')
		);
		add_submenu_page(
			'',
			esc_html__("Maps Reset", "W2MB"),
			esc_html__("Maps Reset", "W2MB"),
			'manage_options',
			'w2mb_reset',
			array($this, 'reset')
		);
	}

	public function debug() {
		global $w2mb_instance, $wpdb;
		
		$w2mb_locationGeoname = new w2mb_locationGeoname();
		$geolocation_response = $w2mb_locationGeoname->geocodeRequest('1600 Amphitheatre Parkway Mountain View, CA 94043', 'test');

		$settings = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'w2mb_%'", ARRAY_A);

		w2mb_renderTemplate('debug.tpl.php', array(
			'rewrite_rules' => get_option('rewrite_rules'),
			'geolocation_response' => $geolocation_response,
			'settings' => $settings,
			'levels' => $w2mb_instance->levels,
			'content_fields' => $w2mb_instance->content_fields,
		));
	}

	public function reset() {
		global $w2mb_instance, $wpdb;
		
		if (isset($_GET['reset']) && ($_GET['reset'] == 'settings' || $_GET['reset'] == 'settings_tables')) {
			if ($wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'w2mb_%'") !== false) {
				delete_option('vpt_option');
				w2mb_save_dynamic_css();
				w2mb_addMessage('All maps settings were deleted!');
			}
		}
		if (isset($_GET['reset']) && $_GET['reset'] == 'settings_tables') {
			$wpdb->query("DROP TABLE IF EXISTS $wpdb->w2mb_content_fields_groups");
			$wpdb->query("DROP TABLE IF EXISTS $wpdb->w2mb_content_fields");
			$wpdb->query("DROP TABLE IF EXISTS $wpdb->w2mb_locations_levels");
			$wpdb->query("DROP TABLE IF EXISTS $wpdb->w2mb_locations_relationships");
			w2mb_addMessage('W2MB database tables were dropped!');
		}
		w2mb_renderTemplate('reset.tpl.php');
	}
	
	public function hideMetaBoxes() {
		global $post, $pagenow;

		if (($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == W2MB_POST_TYPE) || ($pagenow == 'post.php' && $post && $post->post_type == W2MB_POST_TYPE)) {
			$user_id = get_current_user_id();
			update_user_meta($user_id, 'metaboxhidden_' . W2MB_POST_TYPE, array('trackbacksdiv', 'commentstatusdiv', 'postcustom'));
		}
	}
	
	public function showAuthorMetaBox($hidden, $screen) {
		if ($screen->post_type == W2MB_POST_TYPE) {
			if ($key = array_search('authordiv', $hidden)) {
				unset($hidden[$key]);
			}
		}
	
		return $hidden;
	}

	public function hidePreviewButton() {
		global $post_type;
    	if ($post_type == W2MB_POST_TYPE)
    		echo '<style type="text/css">#preview-action {display: none;}</style>';
	}

	public function removeQuickEdit($actions, $post) {
		if ($post->post_type == W2MB_POST_TYPE) {
			unset($actions['inline hide-if-no-js']);
			unset($actions['view']);
		}
		return $actions;
	}
	
	public function addBodyClasses($classes) {
		return "$classes w2mb-body";
	}

	public function removeQuickEditTax($show_in_quick_edit, $taxonomy_name) {
		if ($taxonomy_name == W2MB_CATEGORIES_TAX || $taxonomy_name == W2MB_LOCATIONS_TAX)
			$show_in_quick_edit = false;
		
		return $show_in_quick_edit;
	}
	
	public function addAddonsSettings($options) {
		$options['template']['menus']['general']['controls'] = array_merge(
				array('addons' => array(
					'type' => 'section',
					'title' => esc_html__('Addons', 'W2MB'),
					'description' => esc_html__('Refresh this page after switch on/off any addon.', 'W2MB'),
					'fields' => array(
					 	array(
							'type' => 'toggle',
							'name' => 'w2mb_fsubmit_addon',
							'label' => esc_html__('Frontend submission & dashboard addon', 'W2MB'),
					 		'description' => esc_html__('Allow users to submit new listings at the frontend side of your site, also provides users dashboard functionality.', 'W2MB'),
							'default' => get_option('w2mb_fsubmit_addon'),
						),
					 	array(
							'type' => 'toggle',
							'name' => 'w2mb_ratings_addon',
							'label' => esc_html__('Ratings addon', 'W2MB'),
					 		'description' => esc_html__('Ability to place ratings for listings, then manage these ratings by listings owners.', 'W2MB'),
							'default' => get_option('w2mb_ratings_addon'),
						),
					),
				)),
				$options['template']['menus']['general']['controls']
		);
		
		return $options;
	}
	
	public function admin_enqueue_scripts_styles($hook) {
		global $w2mb_instance;
		
		// include admin.css, rtl.css, bootstrap, custom.css and datepicker files in admin,
		// also in customizer and required for VC plugin, SiteOrigin plugin and widgets
		if (
			w2mb_isMapsPageInAdmin() ||
			is_customize_preview() ||
			$hook == "widgets.php" ||
			get_post_meta(get_the_ID(), '_wpb_vc_js_status', true)
		) {
			wp_enqueue_script('jquery-ui-datepicker');
			wp_register_style('w2mb-jquery-ui-style', W2MB_RESOURCES_URL . 'css/jquery-ui/themes/smoothness/jquery-ui.css');
			wp_enqueue_style('w2mb-jquery-ui-style');
			if ($i18n_file = w2mb_getDatePickerLangFile(get_locale())) {
				wp_register_script('datepicker-i18n', $i18n_file, array('jquery-ui-datepicker'));
				wp_enqueue_script('datepicker-i18n');
			}
			
			if (is_customize_preview())
				$this->enqueue_global_vars();
			else
				add_action('admin_head', array($this, 'enqueue_global_vars'));
			
			wp_register_style('w2mb_bootstrap', W2MB_RESOURCES_URL . 'css/bootstrap.css', array(), W2MB_VERSION);
			wp_register_style('w2mb_admin', W2MB_RESOURCES_URL . 'css/admin.css', array(), W2MB_VERSION);
			if (function_exists('is_rtl') && is_rtl()) {
				wp_register_style('w2mb_admin_rtl', W2MB_RESOURCES_URL . 'css/admin-rtl.css', array(), W2MB_VERSION);
			}
			
			if ($admin_custom = w2mb_isResource('css/admin-custom.css')) {
				wp_register_style('w2mb_admin-custom', $admin_custom, array(), W2MB_VERSION);
			}
		}
		
		if (w2mb_isMapsPageInAdmin()) {
			// some plugins decide to disable this thing
			//wp_enqueue_script('jquery-migrate');

			wp_register_style('w2mb_font_awesome', W2MB_RESOURCES_URL . 'css/font-awesome.css', array(), W2MB_VERSION);
			wp_register_script('w2mb_js_functions', W2MB_RESOURCES_URL . 'js/js_functions.js', array('jquery'), false, true);

			wp_register_script('w2mb_categories_edit_scripts', W2MB_RESOURCES_URL . 'js/categories_icons.js', array('jquery'));
			wp_register_script('w2mb_categories_scripts', W2MB_RESOURCES_URL . 'js/manage_categories.js', array('jquery'));
			
			wp_register_script('w2mb_locations_edit_scripts', W2MB_RESOURCES_URL . 'js/locations_icons.js', array('jquery'));
			
			wp_register_style('w2mb_media_styles', W2MB_RESOURCES_URL . 'lightbox/css/lightbox.min.css', array(), W2MB_VERSION);
			wp_register_script('w2mb_media_scripts_lightbox', W2MB_RESOURCES_URL . 'lightbox/js/lightbox.js', array('jquery'));
			
			wp_localize_script(
				'w2mb_js_functions',
				'w2mb_maps_callback',
				array(
						'callback' => 'w2mb_load_maps_api_backend'
				)
			);
			
			wp_enqueue_script('jquery-ui-selectmenu');
			wp_enqueue_script('jquery-ui-autocomplete');
		}
		
		wp_enqueue_style('w2mb_bootstrap');
		wp_enqueue_style('w2mb_font_awesome');
		wp_enqueue_style('w2mb_admin');
		wp_enqueue_style('w2mb_admin_rtl');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('w2mb_js_functions');
		wp_enqueue_style('w2mb_admin-custom');
		
		if (w2mb_isMapsPageInAdmin()) {
			wp_register_script('w2mb_mapbox_gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . W2MB_MAPBOX_VERSION . '/mapbox-gl.js');
			wp_enqueue_script('w2mb_mapbox_gl');
			wp_register_style('w2mb_mapbox_gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . W2MB_MAPBOX_VERSION . '/mapbox-gl.css');
			wp_enqueue_style('w2mb_mapbox_gl');
			wp_register_script('w2mb_mapbox', W2MB_RESOURCES_URL . 'js/mapboxgl.js', array('jquery'), W2MB_VERSION, true);
			wp_enqueue_script('w2mb_mapbox');
	
			/* wp_register_script('w2mb_mapbox_draw', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.js');
			wp_enqueue_script('w2mb_mapbox_draw');
			wp_register_style('w2mb_mapbox_draw', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.css');
			wp_enqueue_style('w2mb_mapbox_draw');
					
			wp_register_script('w2mb_mapbox_directions', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.js');
			wp_enqueue_script('w2mb_mapbox_directions');
			wp_register_style('w2mb_mapbox_directions', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.css');
			wp_enqueue_style('w2mb_mapbox_directions'); */
					
			wp_register_script('w2mb_mapbox_language', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-language/v0.10.1/mapbox-gl-language.js');
			wp_enqueue_script('w2mb_mapbox_language');
		}
	}

	public function enqueue_global_vars() {
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$ajaxurl = admin_url('admin-ajax.php?lang=' .  $sitepress->get_current_language());
		} else {
			$ajaxurl = admin_url('admin-ajax.php');
		}

		echo '
<script>
';
		echo 'var w2mb_js_objects = ' . json_encode(
				array(
						'ajaxurl' => $ajaxurl,
						'is_rtl' => (int)is_rtl(),
						'fields_in_categories' => array(),
						'is_admin' => (int)is_admin(),
						'lang' => (($sitepress && get_option('w2mb_map_language_from_wpml')) ? ICL_LANGUAGE_CODE : ''),
						'cancel_button' => esc_html__('Cancel', 'W2MB'),
				)
		) . ';
';

		echo 'var w2mb_maps_objects = ' . json_encode(
				array(
						'notinclude_maps_api' => ((defined('W2MB_NOTINCLUDE_MAPS_API') && W2MB_NOTINCLUDE_MAPS_API) ? 1 : 0),
						'mapbox_api_key' => get_option('w2mb_mapbox_api_key'),
						'map_markers_type' => get_option('w2mb_map_markers_type'),
						'default_marker_color' => get_option('w2mb_default_marker_color'),
						'default_marker_icon' => get_option('w2mb_default_marker_icon'),
						'global_map_icons_path' => W2MB_MAP_ICONS_URL,
						'marker_image_width' => (int)get_option('w2mb_map_marker_width'),
						'marker_image_height' => (int)get_option('w2mb_map_marker_height'),
						'marker_image_anchor_x' => (int)get_option('w2mb_map_marker_anchor_x'),
						'marker_image_anchor_y' => (int)get_option('w2mb_map_marker_anchor_y'),
						'default_geocoding_location' => get_option('w2mb_default_geocoding_location'),
						'map_style' => w2mb_getSelectedMapStyle(),
						'address_autocomplete' => (int)get_option('w2mb_address_autocomplete'),
						'address_autocomplete_code' => get_option('w2mb_address_autocomplete_code'),
						'enable_my_location_button' => (int)get_option('w2mb_address_geocode'),
						'my_location_button' => esc_html__('My Location', 'W2MB'),
						'my_location_button_error' => esc_html__('GeoLocation service does not work on your device!', 'W2MB'),
						'default_latitude' => apply_filters('w2mb_default_latitude', 34),
						'default_longitude' => apply_filters('w2mb_default_longitude', 0),
				)
		) . ';
';
		echo '</script>
';
	}

	public function generate_color_palette() {
		ob_start();
		include W2MB_PATH . '/classes/customization/dynamic_css.php';
		$dynamic_css = ob_get_contents();
		ob_get_clean();

		echo $dynamic_css;
		die();
	}

	public function get_jqueryui_theme() {
		global $w2mb_color_schemes;

		if (isset($_COOKIE['w2mb_compare_palettes']) && get_option('w2mb_compare_palettes')) {
			$scheme = $_COOKIE['w2mb_compare_palettes'];
			if ($scheme && isset($w2mb_color_schemes[$scheme]['w2mb_jquery_ui_schemas']))
				echo '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/' . $w2mb_color_schemes[$scheme]['w2mb_jquery_ui_schemas'] . '/jquery-ui.css';
		}
		die();
	}
	
	public function remove_colorpicker_cookie($opt) {
		if (isset($_COOKIE['w2mb_compare_palettes'])) {
			unset($_COOKIE['w2mb_compare_palettes']);
			setcookie('w2mb_compare_palettes', null, -1, '/');
		}
	}

	public function render_palette_picker() {
		global $w2mb_instance;

		if (!empty($w2mb_instance->frontend_controllers)) {
			if ((get_option('w2mb_compare_palettes') && current_user_can('manage_options')) || (defined('W2MB_DEMO') && W2MB_DEMO)) {
				w2mb_renderTemplate('color_picker/color_picker_panel.tpl.php');
			}
		}
	}
}
?>