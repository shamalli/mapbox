<?php 

function w2mb_install_maps() {
	global $wpdb;
	
	if (!get_option('w2mb_installed_maps')) {
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2mb_content_fields_groups} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`on_tab` tinyint(1) NOT NULL DEFAULT '0',
					`hide_anonymous` tinyint(1) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields_groups} WHERE name = 'Contact Information'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields_groups} (`name`, `on_tab`, `hide_anonymous`) VALUES ('Contact Information', 0, 0)");
			do_action('MapBox locator', 'The name of content fields group #1', 'Contact Information');
		}

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2mb_content_fields} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`is_core_field` tinyint(1) NOT NULL DEFAULT '0',
					`order_num` int(11) NOT NULL,
					`name` varchar(255) NOT NULL,
					`slug` varchar(255) NOT NULL,
					`description` text NOT NULL,
					`type` varchar(255) NOT NULL,
					`icon_image` varchar(255) NOT NULL,
					`is_required` tinyint(1) NOT NULL DEFAULT '0',
					`is_configuration_page` tinyint(1) NOT NULL DEFAULT '0',
					`is_search_configuration_page` tinyint(1) NOT NULL DEFAULT '0',
					`is_ordered` tinyint(1) NOT NULL DEFAULT '0',
					`is_hide_name` tinyint(1) NOT NULL DEFAULT '0',
					`for_admin_only` tinyint(1) NOT NULL DEFAULT '0',
					`on_listing_page` tinyint(1) NOT NULL DEFAULT '0',
					`on_listing_sidebar` tinyint(1) NOT NULL DEFAULT '0',
					`on_search_form` tinyint(1) NOT NULL DEFAULT '0',
					`on_map` tinyint(1) NOT NULL DEFAULT '0',
					`advanced_search_form` tinyint(1) NOT NULL,
					`categories` text NOT NULL,
					`options` text NOT NULL,
					`search_options` text NOT NULL,
					`group_id` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`),
					KEY `group_id` (`group_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'summary'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 1, 'Summary', 'summary', '', 'excerpt', '', 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, '', '', '', '0');");
			do_action('MapBox locator', 'The name of content field #1', 'Summary');
		}
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'address'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 2, 'Address', 'address', '', 'address', 'w2mb-fa-map-marker', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, '', '', '', '0');");
			do_action('MapBox locator', 'The name of content field #2', 'Address');
		}
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'content'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 3, 'Description', 'content', '', 'content', '', 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, '', '', '', '0');");
			do_action('MapBox locator', 'The name of content field #3', 'Description');
		}
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'categories_list'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 4, 'Categories', 'categories_list', '', 'categories', '', 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, '', '', '', '0');");
			do_action('MapBox locator', 'The name of content field #4', 'Categories');
		}
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'listing_tags'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 5, 'Listing Tags', 'listing_tags', '', 'tags', '', 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, '', '', '', '0');");
			do_action('MapBox locator', 'The name of content field #5', 'Listing Tags');
		}
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'phone'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(0, 6, 'Phone', 'phone', '', 'phone', 'w2mb-fa-phone', 0, 1, 1, 0, 0, 0, 1, 1, 0, 1, 0, '', 'a:3:{s:10:\"max_length\";s:2:\"25\";s:5:\"regex\";s:0:\"\";s:10:\"phone_mode\";s:5:\"phone\";}', '', '1');");
			do_action('MapBox locator', 'The name of content field #6', 'Phone');
		}
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'website'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(0, 7, 'Website', 'website', '', 'website', 'w2mb-fa-globe', 0, 1, 0, 0, 0, 0, 1, 1, 0, 1, 0, '', 'a:5:{s:8:\"is_blank\";i:1;s:11:\"is_nofollow\";i:1;s:13:\"use_link_text\";i:1;s:17:\"default_link_text\";s:13:\"view our site\";s:21:\"use_default_link_text\";i:0;}', '', '1');");
			do_action('MapBox locator', 'The name of content field #7', 'Website');
		}
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_content_fields} WHERE slug = 'email'")) {
			$wpdb->query("INSERT INTO {$wpdb->w2mb_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `for_admin_only`, `on_listing_sidebar`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(0, 8, 'Email', 'email', '', 'email', 'w2mb-fa-envelope-o', 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '1');");
			do_action('MapBox locator', 'The name of content field #8', 'Email');
		}

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2mb_locations_levels} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`in_address_line` tinyint(1) NOT NULL,
					`allow_add_term` tinyint(1) NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_locations_levels} WHERE name = 'Country'"))
			$wpdb->query("INSERT INTO {$wpdb->w2mb_locations_levels} (`name`, `in_address_line`, `allow_add_term`) VALUES ('Country', 1, 1);");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_locations_levels} WHERE name = 'State'"))
			$wpdb->query("INSERT INTO {$wpdb->w2mb_locations_levels} (`name`, `in_address_line`, `allow_add_term`) VALUES ('State', 1, 1);");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2mb_locations_levels} WHERE name = 'City'"))
			$wpdb->query("INSERT INTO {$wpdb->w2mb_locations_levels} (`name`, `in_address_line`, `allow_add_term`) VALUES ('City', 1, 1);");

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2mb_locations_relationships} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`post_id` int(11) NOT NULL,
					`location_id` int(11) NOT NULL,
					`address_line_1` varchar(255) NOT NULL,
					`address_line_2` varchar(255) NOT NULL,
					`zip_or_postal_index` varchar(25) NOT NULL,
					`additional_info` text NOT NULL,
					`manual_coords` tinyint(1) NOT NULL,
					`map_coords_1` float(10,6) NOT NULL,
					`map_coords_2` float(10,6) NOT NULL,
					`map_icon_file` varchar(255) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `location_id` (`location_id`),
					KEY `post_id` (`post_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	
		if (!is_array(get_terms(W2MB_LOCATIONS_TAX)) || !count(get_terms(W2MB_LOCATIONS_TAX))) {
			if (($parent_term = wp_insert_term('United States', W2MB_LOCATIONS_TAX)) && !is_a($parent_term, 'WP_Error')) {
				wp_insert_term('Alabama', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Alaska', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Arkansas', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Arizona', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('California', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Colorado', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Connecticut', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Delaware', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('District of Columbia', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Florida', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Georgia', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Hawaii', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Idaho', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Illinois', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Indiana', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Iowa', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Kansas', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Kentucky', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Louisiana', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Maine', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Maryland', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Massachusetts', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Michigan', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Minnesota', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Mississippi', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Missouri', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Montana', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Nebraska', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Nevada', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New Hampshire', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New Jersey', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New Mexico', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New York', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('North Carolina', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('North Dakota', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Ohio', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Oklahoma', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Oregon', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Pennsylvania', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Rhode Island', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('South Carolina', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('South Dakota', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Tennessee', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Texas', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Utah', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Vermont', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Virginia', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Washington state', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('West Virginina', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Wisconsin', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Wyoming', W2MB_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
			}
		}
		
		add_option('w2mb_mapbox_api_key', '');
		add_option('w2mb_fsubmit_addon', 0);
		add_option('w2mb_ratings_addon', 0);
		add_option('w2mb_eternal_active_period', 1);
		add_option('w2mb_active_period_days', '1'); // default active period in days when eternal was disabled
		add_option('w2mb_active_period_months', '');
		add_option('w2mb_active_period_years', '');
		add_option('w2mb_change_expiration_date', 0);
		add_option('w2mb_enable_renew', 1);
		add_option('w2mb_unlimited_categories', 1);
		add_option('w2mb_categories_number', 0);
		add_option('w2mb_locations_number', 3);
		add_option('w2mb_images_number', 10);
		add_option('w2mb_videos_number', 3);
		add_option('w2mb_enable_map_listing', 1);
		add_option('w2mb_show_directions', 1);
		add_option('w2mb_listing_contact_form', 1);
		add_option('w2mb_listing_contact_form_7', '');
		add_option('w2mb_custom_contact_email', 0);
		add_option('w2mb_hide_views_counter', 0);
		add_option('w2mb_hide_listings_creation_date', 0);
		add_option('w2mb_hide_author_link', 0);
		add_option('w2mb_listings_comments_plugin', 'plugin');
		add_option('w2mb_listings_comments_mode', 'wp_settings');
		add_option('w2mb_listings_tabs_order', array("addresses-tab", "comments-tab", "videos-tab", "contact-tab", "report-tab"));
		add_option('w2mb_enable_stats', 1);
		add_option('w2mb_logo_enabled', 1);
		add_option('w2mb_enable_lighbox_gallery', 1);
		add_option('w2mb_auto_slides_gallery', 0);
		add_option('w2mb_auto_slides_gallery_delay', 3000);
		add_option('w2mb_enable_nologo', 3000);
		add_option('w2mb_nologo_url', W2MB_URL . 'resources/images/nologo.png');
		add_option('w2mb_100_single_logo_width', 1);
		add_option('w2mb_single_logo_width', 400);
		add_option('w2mb_big_slide_bg_mode', 'cover');
		add_option('w2mb_enable_description', 1);
		add_option('w2mb_enable_summary', 1);
		add_option('w2mb_excerpt_length', 25);
		add_option('w2mb_cropped_content_as_excerpt', 1);
		add_option('w2mb_strip_excerpt', 1);
		add_option('w2mb_show_categories_search', 1);
		add_option('w2mb_categories_search_nesting_level', 1);
		add_option('w2mb_show_keywords_search', 1);
		add_option('w2mb_keywords_ajax_search', 1);
		add_option('w2mb_keywords_search_examples', 'sport, business');
		add_option('w2mb_show_locations_search', 1);
		add_option('w2mb_locations_search_nesting_level', 2);
		add_option('w2mb_show_address_search', 1);
		add_option('w2mb_show_location_count_in_search', 1);
		add_option('w2mb_hide_empty_locations', 0);
		add_option('w2mb_show_category_count_in_search', 1);
		add_option('w2mb_hide_empty_categories', 0);
		add_option('w2mb_show_radius_search', 1);
		add_option('w2mb_miles_kilometers_in_search', 'miles');
		add_option('w2mb_radius_search_min', 0);
		add_option('w2mb_radius_search_max', 10);
		add_option('w2mb_radius_search_default', 10);
		add_option('w2mb_default_geocoding_location', '');
		add_option('w2mb_addresses_order', array("line_1", "comma1", "line_2", "comma2", "location", "space1", "zip"));
		add_option('w2mb_address_autocomplete_code', "0");
		add_option('w2mb_enable_address_line_1', 1);
		add_option('w2mb_enable_address_line_2', 1);
		add_option('w2mb_enable_postal_index', 1);
		add_option('w2mb_enable_additional_info', 1);
		add_option('w2mb_enable_manual_coords', 1);
		add_option('w2mb_enable_users_markers', 1);
		add_option('w2mb_map_markers_type', 'icons');
		add_option('w2mb_default_marker_color', '#2393ba');
		add_option('w2mb_default_marker_icon', 'w2mb-fa-star');
		add_option('w2mb_map_marker_width', 48);
		add_option('w2mb_map_marker_height', 48);
		add_option('w2mb_map_marker_anchor_x', 24);
		add_option('w2mb_map_marker_anchor_y', 48);
		add_option('w2mb_map_infowindow_width', 350);
		add_option('w2mb_map_infowindow_offset', 50);
		add_option('w2mb_map_infowindow_logo_width', 110);
		add_option('w2mb_orderby_exclude_null', 0); // Exclude listings with empty values from sorted results
		add_option('w2mb_admin_notifications_email', get_option('admin_email'));
		add_option('w2mb_send_expiration_notification_days', 1);
		add_option('w2mb_preexpiration_notification', 'Your listing "[listing]" will expire in [days] days. You can renew it here [link]');
		add_option('w2mb_expiration_notification', 'Your listing "[listing]" had expired. You can renew it here [link]');
		add_option('w2mb_force_include_js_css', 0);
		add_option('w2mb_images_lightbox', 1);
		add_option('w2mb_notinclude_jqueryui_css', 0);
		add_option('w2mb_prevent_users_see_other_media', 1);
		add_option('w2mb_address_autocomplete', 1);
		add_option('w2mb_address_geocode', 0);
		add_option('w2mb_enable_recaptcha');
		add_option('w2mb_recaptcha_version', 'v2');
		add_option('w2mb_recaptcha_public_key');
		add_option('w2mb_recaptcha_private_key');
		add_option('w2mb_compare_palettes', 0);
		add_option('w2mb_color_scheme', 'default');
		add_option('w2mb_primary_color', '#2393ba');
		add_option('w2mb_secondary_color', '#1f82a5');
		add_option('w2mb_links_color', '#2393ba');
		add_option('w2mb_links_hover_color', '#2a6496');
		add_option('w2mb_button_1_color', '#2393ba');
		add_option('w2mb_button_2_color', '#1f82a5');
		add_option('w2mb_button_text_color', '#FFFFFF');
		add_option('w2mb_button_gradient', 0);
		add_option('w2mb_search_bg_color', '#6bc8c8');
		add_option('w2mb_search_bg_opacity', 100);
		add_option('w2mb_search_text_color', '#FFFFFF');
		add_option('w2mb_search_overlay', 1);
		add_option('w2mb_jquery_ui_schemas', 'redmond');
		add_option('w2mb_categories_icons');
		add_option('w2mb_locations_icons');
		add_option('w2mb_default_map_zoom', 11);
		add_option('w2mb_report_form', 1);
		add_option('w2mb_hide_anonymous_contact_form', 0);
		add_option('w2mb_map_marker_size', '40');
		add_option('w2mb_single_logo_height', 0);
		add_option('w2mb_marker_anchor', 'w2mb-marker');
		add_option('w2mb_listing_anchor', 'w2mb-listing');
		add_option('w2mb_exclude_logo_from_listing', 0);
	
		add_option('w2mb_installed_maps', true);
		add_option('w2mb_installed_maps_version', W2MB_VERSION);
		add_option('w2mb_installed_plugin_time', time());
	} elseif (get_option('w2mb_installed_maps_version') != W2MB_VERSION) {
		$upgrades_list = array(
				'1.0.9',
		);

		$old_version = get_option('w2mb_installed_maps_version');
		foreach ($upgrades_list AS $upgrade_version) {
			if (!$old_version || version_compare($old_version, $upgrade_version, '<')) {
				$upgrade_function_name = 'w2mb_upgrade_to_' . str_replace('.', '_', $upgrade_version);
				if (function_exists($upgrade_function_name))
					$upgrade_function_name();
				do_action('w2mb_version_upgrade', $upgrade_version);
			}
		}

		w2mb_save_dynamic_css();

		update_option('w2mb_installed_maps_version', W2MB_VERSION);
		
		echo '<script>location.reload();</script>';
		exit;
	}
	
	global $w2mb_instance;
	$w2mb_instance->loadClasses();
}

function w2mb_upgrade_to_1_0_9() {
	add_option('w2mb_recaptcha_version', 'v2');
}

?>