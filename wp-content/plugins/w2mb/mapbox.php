<?php
/*
Plugin Name: MapBox locator plugin
Plugin URI: https://www.salephpscripts.com/wordpress-mapbox/
Description: Build powerful, searchable and responsive MapBox (OpenStreetMap) with markers and insert them on pages in some seconds.
Version: 1.0.12
Author: salephpscripts.com
Author URI: https://www.salephpscripts.com
*/

define('W2MB_VERSION', '1.0.12');

define('W2MB_PATH', plugin_dir_path(__FILE__));
define('W2MB_URL', plugins_url('/', __FILE__));

define('W2MB_TEMPLATES_PATH', W2MB_PATH . 'templates/');

define('W2MB_RESOURCES_PATH', W2MB_PATH . 'resources/');
define('W2MB_RESOURCES_URL', W2MB_URL . 'resources/');

define('W2MB_POST_TYPE', 'w2mb_listing');
define('W2MB_MAP_TYPE', 'w2mb_map');
define('W2MB_CATEGORIES_TAX', 'w2mb-category');
define('W2MB_LOCATIONS_TAX', 'w2mb-location');
define('W2MB_TAGS_TAX', 'w2mb-tag');

define('W2MB_MAPBOX_VERSION', 'v2.2.0');

include_once W2MB_PATH . 'install.php';
include_once W2MB_PATH . 'classes/admin.php';
include_once W2MB_PATH . 'classes/form_validation.php';
include_once W2MB_PATH . 'classes/listings/listings_manager.php';
include_once W2MB_PATH . 'classes/listings/listing.php';
include_once W2MB_PATH . 'classes/categories_manager.php';
include_once W2MB_PATH . 'classes/media_manager.php';
include_once W2MB_PATH . 'classes/comments_manager.php';
include_once W2MB_PATH . 'classes/content_fields/content_fields_manager.php';
include_once W2MB_PATH . 'classes/content_fields/content_fields.php';
include_once W2MB_PATH . 'classes/locations/locations_manager.php';
include_once W2MB_PATH . 'classes/locations/locations_levels_manager.php';
include_once W2MB_PATH . 'classes/locations/locations_levels.php';
include_once W2MB_PATH . 'classes/locations/location.php';
include_once W2MB_PATH . 'classes/levels/levels.php';
include_once W2MB_PATH . 'classes/demo_data.php';
include_once W2MB_PATH . 'classes/frontend_controller.php';
include_once W2MB_PATH . 'classes/shortcodes/map_controller.php';
include_once W2MB_PATH . 'classes/shortcodes/search_controller.php';
include_once W2MB_PATH . 'classes/ajax_controller.php';
include_once W2MB_PATH . 'vafpress-framework/bootstrap.php';
include_once W2MB_PATH . 'classes/settings_manager.php';
include_once W2MB_PATH . 'classes/maps/maps.php';
include_once W2MB_PATH . 'classes/maps/maps_manager.php';
include_once W2MB_PATH . 'classes/widgets/widget.php';
include_once W2MB_PATH . 'classes/widgets/search.php';
include_once W2MB_PATH . 'classes/widgets/map.php';
include_once W2MB_PATH . 'classes/csv_manager.php'; 
include_once W2MB_PATH . 'classes/location_geoname.php';
include_once W2MB_PATH . 'classes/search_form.php';
include_once W2MB_PATH . 'classes/search_map_form.php';
include_once W2MB_PATH . 'classes/search_fields/search_fields.php';
include_once W2MB_PATH . 'classes/updater.php';
include_once W2MB_PATH . 'classes/frontpanel_buttons.php';
include_once W2MB_PATH . 'functions.php';
include_once W2MB_PATH . 'functions_ui.php';
include_once W2MB_PATH . 'vc.php';
include_once W2MB_PATH . 'classes/customization/color_schemes.php';

// Categories icons constant
if ($custom_dir = w2mb_isCustomResourceDir('images/categories_icons/')) {
	define('W2MB_CATEGORIES_ICONS_PATH', $custom_dir);
	define('W2MB_CATEGORIES_ICONS_URL', w2mb_getCustomResourceDirURL('images/categories_icons/'));
} else {
	define('W2MB_CATEGORIES_ICONS_PATH', W2MB_RESOURCES_PATH . 'images/categories_icons/');
	define('W2MB_CATEGORIES_ICONS_URL', W2MB_RESOURCES_URL . 'images/categories_icons/');
}

// Locations icons constant
if ($custom_dir = w2mb_isCustomResourceDir('images/locations_icons/')) {
	define('W2MB_LOCATION_ICONS_PATH', $custom_dir);
	define('W2MB_LOCATIONS_ICONS_URL', w2mb_getCustomResourceDirURL('images/locations_icons/'));
} else {
	define('W2MB_LOCATION_ICONS_PATH', W2MB_RESOURCES_PATH . 'images/locations_icons/');
	define('W2MB_LOCATIONS_ICONS_URL', W2MB_RESOURCES_URL . 'images/locations_icons/');
}

// Map Markers Icons Path
if ($custom_dir = w2mb_isCustomResourceDir('images/map_icons/')) {
	define('W2MB_MAP_ICONS_PATH', $custom_dir);
	define('W2MB_MAP_ICONS_URL', w2mb_getCustomResourceDirURL('images/map_icons/'));
} else {
	define('W2MB_MAP_ICONS_PATH', W2MB_RESOURCES_PATH . 'images/map_icons/');
	define('W2MB_MAP_ICONS_URL', W2MB_RESOURCES_URL . 'images/map_icons/');
}

global $w2mb_instance;
global $w2mb_messages;

/*
 * There are 2 types of shortcodes in the system:
 1. those process as simple wordpress shortcodes
 2. require initialization on 'wp' hook
 
 */
global $w2mb_shortcodes, $w2mb_shortcodes_init;
$w2mb_shortcodes = array(
		'mapbox' => 'w2mb_map_controller',
		'mapbox-search' => 'w2mb_search_controller',
);
$w2mb_shortcodes_init = array();

class w2mb_plugin {
	public $admin;
	public $listings_manager;
	public $comments_manager;
	public $maps_manager;
	public $locations_manager;
	public $locations_levels_manager;
	public $categories_manager;
	public $content_fields_manager;
	public $media_manager;
	public $settings_manager;
	public $demo_data_manager;
	public $levels_manager;
	public $csv_manager;
	public $updater;

	public $current_listing; // this is object of listing under edition right now
	public $levels;
	public $locations_levels;
	public $content_fields;
	public $search_fields;
	public $ajax_controller;
	public $index_page_id;
	public $index_page_slug;
	public $index_page_url;
	public $index_pages_all = array();
	public $listing_pages_all = array();
	public $original_index_page_id;
	public $listing_page_id;
	public $listing_page_slug;
	public $listing_page_url;
	public $frontend_controllers = array();
	public $_frontend_controllers = array(); // this duplicate property needed because we unset each controller when we render shortcodes, but WP doesn't really know which shortcode already was processed
	public $action;
	
	public $radius_values_array = array();
	
	public $order_by_date = false; // special flag, used to display or hide sticky pin

	public function __construct() {
		register_activation_hook(__FILE__, array($this, 'activation'));
		register_deactivation_hook(__FILE__, array($this, 'deactivation'));
	}
	
	public function activation() {
		global $wp_version;

		if (version_compare($wp_version, '3.6', '<')) {
			deactivate_plugins(basename(__FILE__)); // Deactivate ourself
			wp_die("Sorry, but you can't run this plugin on current WordPress version, it requires WordPress v3.6 or higher.");
		}
		flush_rewrite_rules();
		
		wp_schedule_event(current_time('timestamp'), 'hourly', 'scheduled_events');
	}

	public function deactivation() {
		flush_rewrite_rules();

		wp_clear_scheduled_hook('scheduled_events');
	}
	
	public function init() {
		global $w2mb_instance, $w2mb_shortcodes, $wpdb;

		if (isset($_REQUEST['w2mb_action'])) {
			$this->action = $_REQUEST['w2mb_action'];
		}

		add_action('plugins_loaded', array($this, 'load_textdomains'));

		if (!isset($wpdb->w2mb_content_fields))
			$wpdb->w2mb_content_fields = $wpdb->prefix . 'w2mb_content_fields';
		if (!isset($wpdb->w2mb_content_fields_groups))
			$wpdb->w2mb_content_fields_groups = $wpdb->prefix . 'w2mb_content_fields_groups';
		if (!isset($wpdb->w2mb_locations_levels))
			$wpdb->w2mb_locations_levels = $wpdb->prefix . 'w2mb_locations_levels';
		if (!isset($wpdb->w2mb_locations_relationships))
			$wpdb->w2mb_locations_relationships = $wpdb->prefix . 'w2mb_locations_relationships';

		add_action('scheduled_events', array($this, 'suspend_expired_listings'));
		
		foreach ($w2mb_shortcodes AS $shortcode=>$function) {
			add_shortcode($shortcode, array($this, 'renderShortcode'));
		}
		
		add_action('after_setup_theme', array($this, 'register_post_type'));
		add_action('wp', array($this, 'checkMainShortcode'), 1);
		add_filter('body_class', array($this, 'addBodyClasses'));

		add_action('wp', array($this, 'loadFrontendControllers'), 1);

		if (!get_option('w2mb_installed_maps') || get_option('w2mb_installed_maps_version') != W2MB_VERSION) {
			// load classes ONLY after locator was fully installed, otherwise it can not get content fields, e.t.c. from the database
			if (get_option('w2mb_installed_maps')) {
				$this->loadClasses();
			}

			add_action('init', 'w2mb_install_maps', 0);
		} else {
			$this->loadClasses();
		}

		// adapted for Polylang
		add_action('init', array($this, 'pll_setup'));

		add_filter('comments_open', array($this, 'filter_comment_status'), 100, 2);
		
		add_filter('no_texturize_shortcodes', array($this, 'w2mb_no_texturize'));

		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles_custom'), 9999);
		add_action('wp_enqueue_scripts', array($this, 'enqueue_dynamic_css'));
		
		add_filter('wpseo_sitemap_post_type_archive_link', array($this, 'exclude_post_type_archive_link'), 10, 2);
		
		add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
	}

	public function load_textdomains() {
		load_plugin_textdomain('W2MB', '', dirname(plugin_basename( __FILE__ )) . '/languages');
	}
	
	public function loadClasses() {
		$this->locations_levels = new w2mb_locations_levels;
		$this->content_fields = new w2mb_content_fields;
		$this->search_fields = new w2mb_search_fields;
		$this->ajax_controller = new w2mb_ajax_controller;
		$this->admin = new w2mb_admin;
		$this->updater = new w2mb_updater(__FILE__, get_option('w2mb_purchase_code'), get_option('w2mb_access_token'));
	}

	public function w2mb_no_texturize($shortcodes) {
		global $w2mb_shortcodes;
		
		foreach ($w2mb_shortcodes AS $shortcode=>$function)
			$shortcodes[] = $shortcode;
		
		return $shortcodes;
	}

	public function renderShortcode() {
		// Some "genial" themes and plugins can load our shortcodes at the admin part, breaking some important functionality
		if (!is_admin()) {
			global $w2mb_shortcodes;
	
			// remove content filters in order not to break the layout of page
			$filters_to_remove = array(
					'wpautop',
					'wptexturize',
					'shortcode_unautop',
					'convert_chars',
					'prepend_attachment',
					'convert_smilies',
			);
			foreach ($filters_to_remove AS $filter) {
				while (($priority = has_filter('the_content', $filter)) !== false) {
					remove_filter('the_content', $filter, $priority);
				}
			}
	
			$attrs = func_get_args();
			$shortcode = $attrs[2];
	
			$filters_where_not_to_display = array(
					'wp_head',
					'init',
					'wp',
					'edit_attachment',
			);
			
			if (isset($this->_frontend_controllers[$shortcode]) && !in_array(current_filter(), $filters_where_not_to_display)) {
				$shortcode_controllers = $this->_frontend_controllers[$shortcode];
				foreach ($shortcode_controllers AS $key=>&$controller) {
					unset($this->_frontend_controllers[$shortcode][$key]); // there are possible more than 1 same shortcodes on a page, so we have to unset which already was displayed
					if (method_exists($controller, 'display'))
						return $controller->display();
				}
			}
	
			if (isset($w2mb_shortcodes[$shortcode])) {
				$shortcode_class = $w2mb_shortcodes[$shortcode];
				if ($attrs[0] === '')
					$attrs[0] = array();
				$shortcode_instance = new $shortcode_class();
				$this->frontend_controllers[$shortcode][] = $shortcode_instance;
				$shortcode_instance->init($attrs[0], $shortcode);
	
				if (method_exists($shortcode_instance, 'display'))
					return $shortcode_instance->display();
			}
		}
	}

	public function loadFrontendControllers() {
		global $post, $wp_query;

		if ($wp_query->posts) {
			$pattern = get_shortcode_regex();
			foreach ($wp_query->posts AS $archive_post) {
				if (isset($archive_post->post_content))
					$this->loadNestedFrontendController($pattern, $archive_post->post_content);
			}
		} elseif ($post && isset($post->post_content)) {
			$pattern = get_shortcode_regex();
			$this->loadNestedFrontendController($pattern, $post->post_content);
		}
	}

	// this may be recursive function to catch nested shortcodes
	public function loadNestedFrontendController($pattern, $content) {
		global $w2mb_shortcodes_init, $w2mb_shortcodes;

		if (preg_match_all('/'.$pattern.'/s', $content, $matches) && array_key_exists(2, $matches)) {
			foreach ($matches[2] AS $key=>$shortcode) {
				if ($shortcode != 'shortcodes') {
					if (isset($w2mb_shortcodes_init[$shortcode]) && class_exists($w2mb_shortcodes_init[$shortcode])) {
						$shortcode_class = $w2mb_shortcodes_init[$shortcode];
						if (!($attrs = shortcode_parse_atts($matches[3][$key])))
							$attrs = array();
						$shortcode_instance = new $shortcode_class();
						$this->frontend_controllers[$shortcode][] = $shortcode_instance;
						$this->_frontend_controllers[$shortcode][] = $shortcode_instance;
						$shortcode_instance->init($attrs, $shortcode);
					} elseif (isset($w2mb_shortcodes[$shortcode]) && class_exists($w2mb_shortcodes[$shortcode])) {
						$shortcode_class = $w2mb_shortcodes[$shortcode];
						$this->frontend_controllers[$shortcode][] = $shortcode_class;
					}
					if ($shortcode_content = $matches[5][$key])
						$this->loadNestedFrontendController($pattern, $shortcode_content);
				}
			}
		}
	}

	public function checkMainShortcode() {
		if (!get_option('w2mb_mapbox_api_key') && is_admin()) {
			w2mb_addMessage(sprintf(__("<b>MapBox locator plugin</b>: MapBox requires mandatory Maps API key for maps created on NEW websites/domains. Please, <a href=\"https://www.salephpscripts.com/wordpress-mapbox/demo/documentation/\" target=\"_blank\">follow instructions</a> and enter API key on <a href=\"%s\">maps settings page</a>. Otherwise it may cause problems with Maps, Geocoding, addition/edition listings locations, autocomplete on addresses fields.", 'W2MB'), admin_url('admin.php?page=w2mb_settings')));
		}
	}

	public function addBodyClasses($classes) {
		$classes[] = 'w2mb-body';
		
		return $classes;
	}

	public function register_post_type() {
		$args = array(
			'labels' => array(
				'name' => esc_html__('Maps listings', 'W2MB'),
				'singular_name' => esc_html__('Maps listing', 'W2MB'),
				'add_new' => esc_html__('Create new listing', 'W2MB'),
				'add_new_item' => esc_html__('Create new listing', 'W2MB'),
				'edit_item' => esc_html__('Edit listing', 'W2MB'),
				'new_item' => esc_html__('New listing', 'W2MB'),
				'view_item' => esc_html__('View listing', 'W2MB'),
				'search_items' => esc_html__('Search listings', 'W2MB'),
				'not_found' =>  esc_html__('No listings found', 'W2MB'),
				'not_found_in_trash' => esc_html__('No listings found in trash', 'W2MB')
			),
			'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			'has_archive' => true,
			'show_ui' => true,
			'description' => esc_html__('Maps listings', 'W2MB'),
			'exclude_from_search' => false, // this must be false otherwise it breaks pagination for custom taxonomies
			'supports' => array('title', 'author', 'comments'),
			'menu_icon' => W2MB_RESOURCES_URL . 'images/menuicon.png',
		);
		if (get_option('w2mb_enable_description'))
			$args['supports'][] = 'editor';
		if (get_option('w2mb_enable_summary'))
			$args['supports'][] = 'excerpt';
		register_post_type(W2MB_POST_TYPE, $args);
		
		$args = array(
				'labels' => array(
						'name' => esc_html__('MapBox Maps', 'W2MB'),
						'singular_name' => esc_html__('Map', 'W2MB'),
						'add_new' => esc_html__('Create new map', 'W2MB'),
						'add_new_item' => esc_html__('Create new map', 'W2MB'),
						'edit_item' => esc_html__('Edit map', 'W2MB'),
						'new_item' => esc_html__('New map', 'W2MB'),
						'view_item' => esc_html__('View map', 'W2MB'),
						'search_items' => esc_html__('Search maps', 'W2MB'),
						'not_found' =>  esc_html__('No maps found', 'W2MB'),
						'not_found_in_trash' => esc_html__('No maps found in trash', 'W2MB')
				),
				'description' => esc_html__('Maps listings', 'W2MB'),
				'public' => false,
				'publicly_queryable' => false, // removes "Preview changes" button
				'show_ui' => true,
				'exclude_from_search' => true,
				'show_in_nav_menus' => false,
				'has_archive' => false,
				'rewrite' => false,
				'supports' => array('title'),
				'menu_icon' => W2MB_RESOURCES_URL . 'images/menuicon.png',
		);
		register_post_type(W2MB_MAP_TYPE, $args);
		
		register_taxonomy(W2MB_CATEGORIES_TAX, W2MB_POST_TYPE, array(
				'hierarchical' => true,
				'has_archive' => true,
				'labels' => array(
					'name' =>  esc_html__('Listing categories', 'W2MB'),
					'menu_name' =>  esc_html__('Maps categories', 'W2MB'),
					'singular_name' => esc_html__('Category', 'W2MB'),
					'add_new_item' => esc_html__('Create category', 'W2MB'),
					'new_item_name' => esc_html__('New category', 'W2MB'),
					'edit_item' => esc_html__('Edit category', 'W2MB'),
					'view_item' => esc_html__('View category', 'W2MB'),
					'update_item' => esc_html__('Update category', 'W2MB'),
					'search_items' => esc_html__('Search categories', 'W2MB'),
				),
			)
		);
		register_taxonomy(W2MB_LOCATIONS_TAX, W2MB_POST_TYPE, array(
				'hierarchical' => true,
				'has_archive' => true,
				'labels' => array(
					'name' =>  esc_html__('Listing locations', 'W2MB'),
					'menu_name' =>  esc_html__('Maps locations', 'W2MB'),
					'singular_name' => esc_html__('Location', 'W2MB'),
					'add_new_item' => esc_html__('Create location', 'W2MB'),
					'new_item_name' => esc_html__('New location', 'W2MB'),
					'edit_item' => esc_html__('Edit location', 'W2MB'),
					'view_item' => esc_html__('View location', 'W2MB'),
					'update_item' => esc_html__('Update location', 'W2MB'),
					'search_items' => esc_html__('Search locations', 'W2MB'),
					
				),
			)
		);
		register_taxonomy(W2MB_TAGS_TAX, W2MB_POST_TYPE, array(
				'hierarchical' => false,
				'labels' => array(
					'name' =>  esc_html__('Listing tags', 'W2MB'),
					'menu_name' =>  esc_html__('Maps tags', 'W2MB'),
					'singular_name' => esc_html__('Tag', 'W2MB'),
					'add_new_item' => esc_html__('Create tag', 'W2MB'),
					'new_item_name' => esc_html__('New tag', 'W2MB'),
					'edit_item' => esc_html__('Edit tag', 'W2MB'),
					'view_item' => esc_html__('View tag', 'W2MB'),
					'update_item' => esc_html__('Update tag', 'W2MB'),
					'search_items' => esc_html__('Search tags', 'W2MB'),
				),
			)
		);
	}

	public function suspend_expired_listings() {
		global $wpdb;

		$posts_ids = $wpdb->get_col($wpdb->prepare("
				SELECT
					wp_pm1.post_id
				FROM
					{$wpdb->postmeta} AS wp_pm1
				LEFT JOIN
					{$wpdb->postmeta} AS wp_pm2 ON wp_pm1.post_id=wp_pm2.post_id
				LEFT JOIN
					{$wpdb->posts} AS wp_posts ON wp_pm1.post_id=wp_posts.ID
				WHERE
					wp_pm1.meta_key = '_expiration_date' AND
					wp_pm1.meta_value < %d AND
					wp_pm2.meta_key = '_listing_status' AND
					(wp_pm2.meta_value = 'active' OR wp_pm2.meta_value = 'stopped')
			", current_time('timestamp')));
		$listings_ids_to_suspend = $posts_ids;
		foreach ($posts_ids AS $post_id) {
			if (!get_post_meta($post_id, '_expiration_notification_sent', true) && $listing = w2mb_getListing($post_id)) {
				if (get_option('w2mb_expiration_notification')) {
					$listing_owner = get_userdata($listing->post->post_author);
			
					$subject = esc_html__('Expiration notification', 'W2MB');
			
					$body = str_replace('[listing]', $listing->title(),
							str_replace('[link]', ((get_option('w2mb_fsubmit_addon') && isset($this->dashboard_page_url) && $this->dashboard_page_url) ? w2mb_dashboardUrl(array('w2mb_action' => 'renew_listing', 'listing_id' => $post_id)) : admin_url('options.php?page=w2mb_renew&listing_id=' . $post_id)),
							get_option('w2mb_expiration_notification')));
					w2mb_mail($listing_owner->user_email, $subject, $body);
					
					add_post_meta($post_id, '_expiration_notification_sent', true);
				}
			}

			// adapted for WPML
			global $sitepress;
			if (function_exists('wpml_object_id_filter') && $sitepress) {
				$trid = $sitepress->get_element_trid($post_id, 'post_' . W2MB_POST_TYPE);
				$translations = $sitepress->get_element_translations($trid, 'post_' . W2MB_POST_TYPE, false, true);
				foreach ($translations AS $lang=>$translation) {
					$listings_ids_to_suspend[] = $translation->element_id;
				}
			} else {
				$listings_ids_to_suspend[] = $post_id;
			}
		}
		$listings_ids_to_suspend = array_unique($listings_ids_to_suspend);
		foreach ($listings_ids_to_suspend AS $listing_id) {
			update_post_meta($listing_id, '_listing_status', 'expired');
			wp_update_post(array('ID' => $listing_id, 'post_status' => 'draft')); // This needed in order terms counts were always actual
			
			$listing = w2mb_getListing($listing_id);
			
			$continue = true;
			$continue_invoke_hooks = true;
			apply_filters('w2mb_listing_renew', $continue, $listing, array(&$continue_invoke_hooks));
		}

		$posts_ids = $wpdb->get_col($wpdb->prepare("
				SELECT
					wp_pm1.post_id
				FROM
					{$wpdb->postmeta} AS wp_pm1
				LEFT JOIN
					{$wpdb->postmeta} AS wp_pm2 ON wp_pm1.post_id=wp_pm2.post_id
				WHERE
					wp_pm1.meta_key = '_expiration_date' AND
					wp_pm1.meta_value < %d AND
					wp_pm2.meta_key = '_listing_status' AND
					(wp_pm2.meta_value = 'active' OR wp_pm2.meta_value = 'stopped')
			", current_time('timestamp')+(get_option('w2mb_send_expiration_notification_days')*86400)));

		$listings_ids = $posts_ids;

		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			foreach ($posts_ids AS $post_id) {
				$trid = $sitepress->get_element_trid($post_id, 'post_' . W2MB_POST_TYPE);
				$listings_ids[] = $trid;
			}
		} else {
			$listings_ids = $posts_ids;
		}

		$listings_ids = array_unique($listings_ids);
		foreach ($listings_ids AS $listing_id) {
			if (!get_post_meta($listing_id, '_preexpiration_notification_sent', true) && ($listing = w2mb_getListing($listing_id))) {
				if (get_option('w2mb_preexpiration_notification')) {
					$listing_owner = get_userdata($listing->post->post_author);

					$subject = esc_html__('Pre-expiration notification', 'W2MB');
					
					$body = str_replace('[listing]', $listing->title(),
							str_replace('[days]', get_option('w2mb_send_expiration_notification_days'),
							str_replace('[link]', ((get_option('w2mb_fsubmit_addon') && isset($this->dashboard_page_url) && $this->dashboard_page_url) ? w2mb_dashboardUrl(array('w2mb_action' => 'renew_listing', 'listing_id' => $listing_id)) : admin_url('options.php?page=w2mb_renew&listing_id=' . $listing_id)),
							get_option('w2mb_preexpiration_notification'))));
					w2mb_mail($listing_owner->user_email, $subject, $body);
					
					add_post_meta($listing_id, '_preexpiration_notification_sent', true);
				}

				$continue_invoke_hooks = true;
				if ($listing = $this->listings_manager->loadListing($listing_id)) {
					apply_filters('w2mb_listing_renew', false, $listing, array(&$continue_invoke_hooks));
				}
			}
		}
	}
	
	function filter_comment_status($open, $post_id) {
		$post = get_post($post_id);
		if ($post->post_type == W2MB_POST_TYPE) {
			if (get_option('w2mb_listings_comments_mode') == 'enabled')
				return true;
			elseif (get_option('w2mb_listings_comments_mode') == 'disabled')
				return false;
		}

		return $open;
	}

	/**
	 * Get property by shortcode name
	 * 
	 * @param string $shortcode
	 * @param string $property if property missed - return controller object
	 * @return mixed
	 */
	public function getShortcodeProperty($shortcode, $property = false) {
		if (!isset($this->frontend_controllers[$shortcode]) || !isset($this->frontend_controllers[$shortcode][0]))
			return false;

		if ($property && !isset($this->frontend_controllers[$shortcode][0]->$property))
			return false;

		if ($property)
			return $this->frontend_controllers[$shortcode][0]->$property;
		else 
			return $this->frontend_controllers[$shortcode][0];
	}
	
	public function getShortcodeByHash($hash) {
		if (!isset($this->frontend_controllers) || !is_array($this->frontend_controllers) || empty($this->frontend_controllers))
			return false;

		foreach ($this->frontend_controllers AS $shortcodes)
			foreach ($shortcodes AS $controller)
				if (is_object($controller) && $controller->hash == $hash)
					return $controller;
	}
	
	public function getListingsShortcodeByuID($uid) {
		foreach ($this->frontend_controllers AS $shortcodes)
			foreach ($shortcodes AS $controller)
				if (is_object($controller) && get_class($controller) == 'w2mb_maps_controller' && $controller->args['uid'] == $uid)
					return $controller;
	}

	public function enqueue_scripts_styles($load_scripts_styles = false) {
		global $w2mb_enqueued;
		if ((($this->frontend_controllers || $load_scripts_styles) && !$w2mb_enqueued) || get_option('w2mb_force_include_js_css')) {
			add_action('wp_head', array($this, 'enqueue_global_vars'));
			
			wp_enqueue_script('jquery');

			wp_register_style('w2mb_bootstrap', W2MB_RESOURCES_URL . 'css/bootstrap.css', array(), W2MB_VERSION);
			wp_register_style('w2mb_frontend', W2MB_RESOURCES_URL . 'css/frontend.css', array(), W2MB_VERSION);

			if (function_exists('is_rtl') && is_rtl()) {
				wp_register_style('w2mb_frontend_rtl', W2MB_RESOURCES_URL . 'css/frontend-rtl.css', array(), W2MB_VERSION);
			}

			wp_register_style('w2mb_font_awesome', W2MB_RESOURCES_URL . 'css/font-awesome.css', array(), W2MB_VERSION);

			wp_register_script('w2mb_js_functions', W2MB_RESOURCES_URL . 'js/js_functions.js', array('jquery'), W2MB_VERSION, true);

			wp_register_script('w2mb_categories_scripts', W2MB_RESOURCES_URL . 'js/manage_categories.js', array('jquery'), false, true);

			wp_register_style('w2mb_media_styles', W2MB_RESOURCES_URL . 'lightbox/css/lightbox.min.css', array(), W2MB_VERSION);
			wp_register_script('w2mb_media_scripts_lightbox', W2MB_RESOURCES_URL . 'lightbox/js/lightbox.js', array('jquery'), false, true);
			wp_enqueue_style('w2mb_media_styles');
			wp_enqueue_script('w2mb_media_scripts_lightbox');

			// this jQuery UI version 1.10.4
			if (get_option('w2mb_jquery_ui_schemas')) $ui_theme = w2mb_get_dynamic_option('w2mb_jquery_ui_schemas'); else $ui_theme = 'smoothness';
			wp_register_style('w2mb-jquery-ui-style', W2MB_RESOURCES_URL . 'css/jquery-ui/themes/' . $ui_theme . '/jquery-ui.css');

			
			wp_register_style('w2mb_listings_slider', W2MB_RESOURCES_URL . 'css/bxslider/jquery.bxslider.css', array(), W2MB_VERSION);
			wp_enqueue_style('w2mb_listings_slider');

			wp_enqueue_style('w2mb_bootstrap');
			wp_enqueue_style('w2mb_font_awesome');
			wp_enqueue_style('w2mb_frontend');
			wp_enqueue_style('w2mb_frontend_rtl');
			
			// Include dynamic-css file only when we are not in palettes comparison mode
			if (!isset($_COOKIE['w2mb_compare_palettes']) || !get_option('w2mb_compare_palettes')) {
				// Include dynamically generated css file if this file exists
				$upload_dir = wp_upload_dir();
				$filename = trailingslashit(set_url_scheme($upload_dir['baseurl'])) . 'w2mb-plugin.css';
				$filename_dir = trailingslashit($upload_dir['basedir']) . 'w2mb-plugin.css';
				global $wp_filesystem;
				if (empty($wp_filesystem)) {
					require_once(ABSPATH .'/wp-admin/includes/file.php');
					WP_Filesystem();
				}
				if ($wp_filesystem && trim($wp_filesystem->get_contents($filename_dir))) { // if css file creation success
					wp_enqueue_style('w2mb-dynamic-css', $filename, array(), time());
				}
			}

			wp_enqueue_script('jquery-ui-dialog');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-selectmenu');
			wp_enqueue_script('jquery-ui-autocomplete');
			if (!get_option('w2mb_notinclude_jqueryui_css')) {
				wp_enqueue_style('w2mb-jquery-ui-style');
			}

			wp_enqueue_script('w2mb_js_functions');

			wp_register_script('w2mb_mapbox_gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . W2MB_MAPBOX_VERSION . '/mapbox-gl.js');
			wp_enqueue_script('w2mb_mapbox_gl');
			wp_register_style('w2mb_mapbox_gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . W2MB_MAPBOX_VERSION . '/mapbox-gl.css');
			wp_enqueue_style('w2mb_mapbox_gl');
			wp_register_script('w2mb_mapbox', W2MB_RESOURCES_URL . 'js/mapboxgl.js', array('jquery'), W2MB_VERSION, true);
			wp_enqueue_script('w2mb_mapbox');
	
			wp_register_script('w2mb_mapbox_draw', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.2/mapbox-gl-draw.js');
			wp_enqueue_script('w2mb_mapbox_draw');
			wp_register_style('w2mb_mapbox_draw', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.2/mapbox-gl-draw.css');
			wp_enqueue_style('w2mb_mapbox_draw');
			
			wp_register_script('w2mb_mapbox_directions', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.js');
			wp_enqueue_script('w2mb_mapbox_directions');
			wp_register_style('w2mb_mapbox_directions', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.css');
			wp_enqueue_style('w2mb_mapbox_directions');
					
			wp_register_script('w2mb_mapbox_language', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-language/v0.10.1/mapbox-gl-language.js');
			wp_enqueue_script('w2mb_mapbox_language');
			
			wp_localize_script(
				'w2mb_js_functions',
				'w2mb_maps_callback',
				array(
						'callback' => 'w2mb_load_maps_api'
				)
			);
			
			if (get_option('w2mb_enable_recaptcha') && get_option('w2mb_recaptcha_public_key') && get_option('w2mb_recaptcha_private_key')) {
				if (get_option('w2mb_recaptcha_version') == 'v2') {
					wp_register_script('w2mb_recaptcha', '//google.com/recaptcha/api.js');
				} elseif (get_option('w2mb_recaptcha_version') == 'v3') {
					wp_register_script('w2mb_recaptcha', '//google.com/recaptcha/api.js?render='.get_option('w2mb_recaptcha_public_key'));
				}
				wp_enqueue_script('w2mb_recaptcha');
			}

			$w2mb_enqueued = true;
		}
	}
	
	public function enqueue_scripts_styles_custom($load_scripts_styles = false) {
		if ((($this->frontend_controllers || $load_scripts_styles)) || get_option('w2mb_force_include_js_css')) {
			if ($frontend_custom = w2mb_isResource('css/frontend-custom.css')) {
				wp_register_style('w2mb_frontend-custom', $frontend_custom, array(), W2MB_VERSION);
				
				wp_enqueue_style('w2mb_frontend-custom');
			}
		}
	}
	
	public function enqueue_global_vars() {
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$ajaxurl = admin_url('admin-ajax.php?lang=' .  $sitepress->get_current_language());
		} else
			$ajaxurl = admin_url('admin-ajax.php');

		echo '
<script>
';
		echo 'var w2mb_controller_args_array = {};
';
		echo 'var w2mb_map_markers_attrs_array = [];
';
		echo 'var w2mb_map_markers_attrs = (function(map_id, markers_array, map_attrs) {
		this.map_id = map_id;
		this.markers_array = markers_array;
		this.map_attrs = map_attrs;
		for (var attrname in map_attrs) { this[attrname] = map_attrs[attrname]; }
		});
';
		echo 'var w2mb_js_objects = ' . json_encode(
				array(
						'ajaxurl' => $ajaxurl,
						'search_map_button_text' => esc_html__('Search on map', 'W2MB'),
						'in_favourites_icon' => 'w2mb-glyphicon-heart',
						'not_in_favourites_icon' => 'w2mb-glyphicon-heart-empty',
						'in_favourites_msg' => esc_html__('Add Bookmark', 'W2MB'),
						'not_in_favourites_msg' => esc_html__('Remove Bookmark', 'W2MB'),
						'is_rtl' => is_rtl(),
						'leave_comment' => esc_html__('Leave a comment', 'W2MB'),
						'leave_reply' => esc_html__('Leave a reply to', 'W2MB'),
						'cancel_reply' => esc_html__('Cancel reply', 'W2MB'),
						'more' => esc_html__('More', 'W2MB'),
						'less' => esc_html__('Less', 'W2MB'),
						'send_button_text' => esc_html__('Send message', 'W2MB'),
						'send_button_sending' => esc_html__('Sending...', 'W2MB'),
						'recaptcha_public_key' => ((get_option('w2mb_enable_recaptcha') && get_option('w2mb_recaptcha_public_key') && get_option('w2mb_recaptcha_private_key')) ? get_option('w2mb_recaptcha_public_key') : ''),
						'lang' => (($sitepress && get_option('w2mb_map_language_from_wpml')) ? ICL_LANGUAGE_CODE : apply_filters('w2mb_map_language', '')),
						'fields_in_categories' => array(),
						'is_admin' => (int)is_admin(),
						'prediction_note' => esc_html__('search nearby', 'W2MB'),
						'listing_tabs_order' => get_option('w2mb_listings_tabs_order'),
						'cancel_button' => esc_html__('Cancel', 'W2MB'),
						'no_listings' => esc_js(__("No listings found by this search", "W2MB")),
						'directions_distance_label' => __('Directions', 'W2MB'),
						'directions_meters_label' => __('m', 'W2MB'),
						'directions_kilometers_label' => __('km', 'W2MB'),
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
						'infowindow_width' => (int)get_option('w2mb_map_infowindow_width'),
						'infowindow_offset' => -(int)get_option('w2mb_map_infowindow_offset'),
						'infowindow_logo_width' => (int)get_option('w2mb_map_infowindow_logo_width'),
						'draw_area_button' => esc_html__('Draw Area', 'W2MB'),
						'edit_area_button' => esc_html__('Edit Area', 'W2MB'),
						'apply_area_button' => esc_html__('Apply Area', 'W2MB'),
						'reload_map_button' => esc_html__('Refresh Map', 'W2MB'),
						'enable_my_location_button' => (int)get_option('w2mb_address_geocode'),
						'my_location_button' => esc_html__('My Location', 'W2MB'),
						'my_location_button_error' => esc_html__('GeoLocation service does not work on your device!', 'W2MB'),
						'map_style' => w2mb_getSelectedMapStyle(),
						'address_autocomplete' => (int)get_option('w2mb_address_autocomplete'),
						'address_autocomplete_code' => get_option('w2mb_address_autocomplete_code'),
						'marker_anchor' => w2mb_get_marker_anchor(),
						'listing_anchor' => w2mb_get_listing_anchor(),
						'mapbox_directions_placeholder_origin' => __('Choose a starting place', 'W2MB'),
						'mapbox_directions_placeholder_destination' => __('Choose destination', 'W2MB'),
						'mapbox_directions_profile_driving_traffic' => __('Traffic', 'W2MB'),
						'mapbox_directions_profile_driving' => __('Driving', 'W2MB'),
						'mapbox_directions_profile_walking' => __('Walking', 'W2MB'),
						'mapbox_directions_profile_cycling' => __('Cycling', 'W2MB'),
						'default_latitude' => apply_filters('w2mb_default_latitude', 34),
						'default_longitude' => apply_filters('w2mb_default_longitude', 0),
						'dimension_unit' => get_option('w2mb_miles_kilometers_in_search'),
				)
		) . ';
';
		echo '</script>
';
	}

	// Include dynamically generated css code if css file does not exist.
	public function enqueue_dynamic_css($load_scripts_styles = false) {
		$upload_dir = wp_upload_dir();
		$filename = trailingslashit(set_url_scheme($upload_dir['baseurl'])) . 'w2mb-plugin.css';
		$filename_dir = trailingslashit($upload_dir['basedir']) . 'w2mb-plugin.css';
		global $wp_filesystem;
		if (empty($wp_filesystem)) {
			require_once(ABSPATH .'/wp-admin/includes/file.php');
			WP_Filesystem();
		}
		if ((!$wp_filesystem || !trim($wp_filesystem->get_contents($filename_dir))) ||
			// When we are in palettes comparison mode - this will build css according to $_COOKIE['w2mb_compare_palettes']
			(isset($_COOKIE['w2mb_compare_palettes']) && get_option('w2mb_compare_palettes')))
		{
			ob_start();
			include W2MB_PATH . '/classes/customization/dynamic_css.php';
			$dynamic_css = ob_get_contents();
			ob_get_clean();
				
			wp_add_inline_style('w2mb_frontend', $dynamic_css);
		}
	}
	
	public function exclude_post_type_archive_link($archive_url, $post_type) {
		if ($post_type == W2MB_POST_TYPE) {
			return false;
		}
		
		return $archive_url;
	}
	
	public function plugin_row_meta($links, $file) {
		if (dirname(plugin_basename(__FILE__) == $file)) {
			$row_meta = array(
					'docs' => '<a href="https://www.salephpscripts.com/wordpress-mapbox/demo/documentation/">' . esc_html__("Documentation", "W2MB") . '</a>',
					'codecanoyn' => '<a href="https://codecanyon.net/item/mapbox-locator-plugin-for-wordpress/27645284#item-description__changelog">' . esc_html__("Changelog", "W2MB") . '</a>',
			);
	
			return array_merge($links, $row_meta);
		}
	
		return $links;
	}
	
	public function plugin_action_links($links) {
		$action_links = array(
				'settings' => '<a href="' . admin_url('admin.php?page=w2mb_settings') . '">' . esc_html__("Settings", "W2MB") . '</a>',
		);
	
		return array_merge($action_links, $links);
	}

	// adapted for Polylang
	public function pll_setup() {
		if (defined("POLYLANG_VERSION")) {
			add_filter('post_type_link', array($this, 'pll_stop_add_lang_to_url_post'), 0, 2);
			add_filter('post_type_link', array($this, 'pll_start_add_lang_to_url_post'), 100, 2);
			add_filter('term_link', array($this, 'pll_stop_add_lang_to_url_term'), 0, 3);
			add_filter('term_link', array($this, 'pll_start_add_lang_to_url_term'), 100, 3);
		}
	}
	public function pll_stop_add_lang_to_url_post($permalink, $post) {
		$this->pll_force_lang = false;
		if ($post->post_type == W2MB_POST_TYPE) {
			global $polylang;
			if (isset($polylang->links->links_model->model->options['force_lang']) && $polylang->links->links_model->model->options['force_lang']) {
				$this->pll_force_lang = true;
				$polylang->links->links_model->model->options['force_lang'] = 0;
			}
		}
		return $permalink;
	}
	public function pll_start_add_lang_to_url_post($permalink, $post) {
		if ($this->pll_force_lang && $post->post_type == W2MB_POST_TYPE) {
			global $polylang;
			$polylang->links->links_model->model->options['force_lang'] = 1;
		}
		return $permalink;
	}
	public function pll_stop_add_lang_to_url_term($permalink, $term, $tax) {
		$this->pll_force_lang = false;
		if ($tax == W2MB_CATEGORIES_TAX || $tax == W2MB_LOCATIONS_TAX || $tax == W2MB_TAGS_TAX) {
			global $polylang;
			if (isset($polylang->links->links_model->model->options['force_lang']) && $polylang->links->links_model->model->options['force_lang']) {
				$this->pll_force_lang = true;
				$polylang->links->links_model->model->options['force_lang'] = 0;
			}
		}
		return $permalink;
	}
	public function pll_start_add_lang_to_url_term($permalink, $term, $tax) {
		if ($this->pll_force_lang && ($tax == W2MB_CATEGORIES_TAX || $tax == W2MB_LOCATIONS_TAX || $tax == W2MB_TAGS_TAX)) {
			global $polylang;
			$polylang->links->links_model->model->options['force_lang'] = 1;
		}
		return $permalink;
	}
}

$w2mb_instance = new w2mb_plugin();
$w2mb_instance->init();

if (get_option('w2mb_fsubmit_addon'))
	include_once W2MB_PATH . 'addons/w2mb_fsubmit/w2mb_fsubmit.php';
if (get_option('w2mb_ratings_addon'))
	include_once W2MB_PATH . 'addons/w2mb_ratings/w2mb_ratings.php';

?>