<?php

global $w2mb_wpml_dependent_options;
$w2mb_wpml_dependent_options[] = 'w2mb_listing_contact_form_7';

class w2mb_settings_manager {
	public function __construct() {
		add_action('init', array($this, 'plugin_settings'));
		
		if (!defined('W2MB_DEMO') || !W2MB_DEMO) {
			add_action('vp_w2mb_option_after_ajax_save', array($this, 'save_option'), 10, 3);
		}
		
		add_action('w2mb_settings_panel_bottom', array($this, 'our_plugins'));
	}
	
	public function our_plugins() {
		w2mb_renderTemplate('our_plugins.tpl.php');
	}
	
	public function plugin_settings() {
		global $w2mb_instance, $sitepress;
		
		if (defined('W2MB_DEMO') && W2MB_DEMO) {
			$capability = 'publish_posts';
		} else {
			$capability = 'edit_theme_options';
		}

		$ordering_items = w2mb_orderingItems();

		$listings_tabs = array(
				array('value' => 'addresses-tab', 'label' => esc_html__('Addresses tab', 'W2MB')),
				array('value' => 'comments-tab', 'label' => esc_html__('Comments tab', 'W2MB')),
				array('value' => 'videos-tab', 'label' => esc_html__('Videos tab', 'W2MB')),
				array('value' => 'contact-tab', 'label' => esc_html__('Contact tab', 'W2MB')),
				array('value' => 'report-tab', 'label' => esc_html__('Report tab', 'W2MB')));
		foreach ($w2mb_instance->content_fields->content_fields_groups_array AS $fields_group) {
			if ($fields_group->on_tab) {
				$listings_tabs[] = array('value' => 'field-group-tab-'.$fields_group->id, 'label' => $fields_group->name);
			}
		}

		$country_codes = array(array('value' => 0, 'label' => 'Worldwide'));
		$w2mb_country_codes = w2mb_country_codes();
		foreach ($w2mb_country_codes AS $country=>$code)
			$country_codes[] = array('value' => $code, 'label' => $country);
		
		$theme_options = array(
				//'is_dev_mode' => true,
				'option_key' => 'vpt_option',
				'page_slug' => 'w2mb_settings',
				'template' => array(
					'title' => esc_html__('MapBox locator Settings', 'W2MB'),
					'logo' => W2MB_RESOURCES_URL . 'images/settings.png',
					'menus' => array(
						'general' => array(
							'name' => 'general',
							'title' => esc_html__('General settings', 'W2MB'),
							'icon' => 'font-awesome:w2mb-fa-home',
							'controls' => array(
								'api_keys' => array(
									'type' => 'section',
									'title' => esc_html__('MapBox API key', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'textbox',
											'name' => 'w2mb_mapbox_api_key',
											'label' => esc_html__('MapBox Access Token', 'W2MB'),
											'description' => sprintf(esc_html__('get your MapBox Access Token %s', 'W2MB'), '<a href="https://www.mapbox.com/account/" target="_blank">here</a>'),
											'default' => get_option('w2mb_mapbox_api_key'),
										),
									),
								),
							),
						),
						'listings' => array(
							'name' => 'listings',
							'title' => esc_html__('Listings', 'W2MB'),
							'icon' => 'font-awesome:w2mb-fa-list-alt',
							'controls' => array(
								'slugs' => array(
									'type' => 'section',
									'title' => esc_html__('Slugs and anchors', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'textbox',
											'name' => 'w2mb_marker_anchor',
											'label' => esc_html__('Map marker slug*', 'W2MB'),
											'description' => esc_html__('This value appears in URL when map marker selected.', 'W2MB'),
											'default' => get_option('w2mb_marker_anchor'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_listing_anchor',
											'label' => esc_html__('Listings slug*', 'W2MB'),
												'description' => esc_html__('This value appears in URL when listing window opened.', 'W2MB'),
											'default' => get_option('w2mb_listing_anchor'),
										),
									),
								),
								'listings' => array(
									'type' => 'section',
									'title' => esc_html__('Listings settings', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_eternal_active_period',
											'label' => esc_html__('Listings will never expire', 'W2MB'),
											'default' => get_option('w2mb_eternal_active_period'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_active_period_days',
											'label' => esc_html__('Active period of listings (in days)', 'W2MB'),
											'description' => esc_html__('Works when listings may expire.', 'W2MB'),
											'default' => get_option('w2mb_active_period_days'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_active_period_months',
											'label' => esc_html__('Active period of listings (in months)', 'W2MB'),
											'description' => esc_html__('Works when listings may expire.', 'W2MB'),
											'default' => get_option('w2mb_active_period_months'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_active_period_years',
											'label' => esc_html__('Active period of listings (in years)', 'W2MB'),
											'description' => esc_html__('Works when listings may expire.', 'W2MB'),
											'default' => get_option('w2mb_active_period_years'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_change_expiration_date',
											'label' => esc_html__('Allow regular users to change listings expiration dates', 'W2MB'),
											'default' => get_option('w2mb_change_expiration_date'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_renew',
											'label' => esc_html__('Allow listings to renew', 'W2MB'),
											'default' => get_option('w2mb_enable_renew'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_unlimited_categories',
											'label' => esc_html__('Allow unlimited categories', 'W2MB'),
											'default' => get_option('w2mb_unlimited_categories'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_categories_number',
											'label' => esc_html__('Number of categories allowed for each listing', 'W2MB'),
											'default' => get_option('w2mb_categories_number'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_locations_number',
											'label' => esc_html__('Number of locations allowed for each listing', 'W2MB'),
											'default' => get_option('w2mb_locations_number'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_images_number',
											'label' => esc_html__('Number of images allowed for each listing (including logo)', 'W2MB'),
											'default' => get_option('w2mb_images_number'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_videos_number',
											'label' => esc_html__('Number of videos allowed for each listing', 'W2MB'),
											'default' => get_option('w2mb_videos_number'),
										),
									),
								),
								'listings_window' => array(
									'type' => 'section',
									'title' => esc_html__('Listings window', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_map_listing',
											'label' => esc_html__('Enable map in listing window', 'W2MB'),
											'default' => get_option('w2mb_enable_map_listing'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_directions',
											'label' => esc_html__('Show directions panel in listing window', 'W2MB'),
											'default' => get_option('w2mb_show_directions'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_listing_contact_form',
											'label' => esc_html__('Enable contact form in listing window', 'W2MB'),
											'description' => esc_html__('Contact Form 7 or standard form will be displayed on each listing page', 'W2MB'),
											'default' => get_option('w2mb_listing_contact_form'),
										),
										array(
											'type' => 'textbox',
											'name' => w2mb_get_wpml_dependent_option_name('w2mb_listing_contact_form_7'),
											'label' => esc_html__('Contact Form 7 shortcode', 'W2MB'),
											'description' => esc_html__('This will work only when Contact Form 7 plugin enabled, otherwise standard contact form will be displayed.', 'W2MB') . w2mb_get_wpml_dependent_option_description(),
											'default' => w2mb_get_wpml_dependent_option('w2mb_listing_contact_form_7'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_hide_anonymous_contact_form',
											'label' => esc_html__('Show contact form only for logged in users', 'W2MB'),
											'default' => get_option('w2mb_hide_anonymous_contact_form'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_custom_contact_email',
											'label' => esc_html__('Allow custom contact emails', 'W2MB'),
											'description' => esc_html__('When enabled users may set up custom contact emails, otherwise messages will be sent directly to authors emails', 'W2MB'),
											'default' => get_option('w2mb_custom_contact_email'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_report_form',
											'label' => esc_html__('Enable report form', 'W2MB'),
											'default' => get_option('w2mb_report_form'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_hide_views_counter',
											'label' => esc_html__('Hide listings views counter', 'W2MB'),
											'default' => get_option('w2mb_hide_views_counter'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_hide_listings_creation_date',
											'label' => esc_html__('Hide listings creation date', 'W2MB'),
											'default' => get_option('w2mb_hide_listings_creation_date'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_hide_author_link',
											'label' => esc_html__('Hide author information', 'W2MB'),
											'description' => esc_html__('Author name and possible link to author website will be hidden on single listing pages.', 'W2MB'),
											'default' => get_option('w2mb_hide_author_link'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2mb_listings_comments_plugin',
											'label' => esc_html__('Listings comments system', 'W2MB'),
											'default' => array(get_option('w2mb_listings_comments_plugin')),
											'items' => array(
													array(
														'value' => 'plugin',
														'label' => esc_attr__("Use plugin's system", 'W2MB'),	
													),
													array(
														'value' => 'native',
														'label' => esc_html__('Use native wordpress comments', 'W2MB'),	
													),
											),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2mb_listings_comments_mode',
											'label' => esc_html__('Listings comments mode', 'W2MB'),
											'default' => array(get_option('w2mb_listings_comments_mode')),
											'items' => array(
													array(
														'value' => 'enabled',
														'label' => esc_html__('Always enabled', 'W2MB'),	
													),
													array(
														'value' => 'disabled',
														'label' => esc_html__('Always disabled', 'W2MB'),	
													),
													array(
														'value' => 'wp_settings',
														'label' => esc_html__('As configured in WP settings', 'W2MB'),	
													),
											),
										),
										array(
											'type' => 'sorter',
											'name' => 'w2mb_listings_tabs_order',
											'label' => esc_html__('Listing tabs order', 'W2MB'),
									 		'items' => $listings_tabs,
											'default' => get_option('w2mb_listings_tabs_order'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_stats',
											'label' => esc_html__('Enable statistics functionality', 'W2MB'),
											'default' => get_option('w2mb_enable_stats'),
										),
									),
								),
								'logos' => array(
									'type' => 'section',
									'title' => esc_html__('Listings logos & images', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_logo_enabled',
											'label' => esc_html__('Show logo in InfoWindow and listings sidebar', 'W2MB'),
											'default' => get_option('w2mb_logo_enabled'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_lightbox_gallery',
											'label' => esc_html__('Enable lightbox on images gallery', 'W2MB'),
											'default' => get_option('w2mb_enable_lightbox_gallery'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_auto_slides_gallery',
											'label' => esc_html__('Enable automatic rotating slideshow on images gallery', 'W2MB'),
											'default' => get_option('w2mb_auto_slides_gallery'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_auto_slides_gallery_delay',
											'label' => esc_html__('The delay in rotation (in ms)', 'W2MB'),
											'default' => get_option('w2mb_auto_slides_gallery_delay'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_exclude_logo_from_listing',
											'label' => esc_html__('Exclude logo image from images gallery', 'W2MB'),
											'default' => get_option('w2mb_exclude_logo_from_listing'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_nologo',
											'label' => esc_html__('Enable default logo image', 'W2MB'),
											'default' => get_option('w2mb_enable_nologo'),
										),
										array(
											'type' => 'upload',
											'name' => 'w2mb_nologo_url',
											'label' => esc_html__('Default logo image', 'W2MB'),
									 		'description' => esc_html__('This image will appear when listing owner did not upload own logo.', 'W2MB'),
											'default' => get_option('w2mb_nologo_url'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_100_single_logo_width',
											'label' => esc_html__('Enable 100% width of images gallery', 'W2MB'),
											'default' => get_option('w2mb_100_logo_width'),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_single_logo_width',
											'label' => esc_html__('Images gallery width (in pixels)', 'W2MB'),
											'description' => esc_html__('This option needed only when 100% width of images gallery is switched off'),
											'min' => 100,
											'max' => 800,
											'default' => get_option('w2mb_single_logo_width'),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_single_logo_height',
											'label' => esc_html__('Images gallery height (in pixels)', 'W2MB'),
											'description' => esc_html__('Set to 0 to fit full height'),
											'min' => 0,
											'max' => 800,
											'default' => get_option('w2mb_single_logo_height'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2mb_big_slide_bg_mode',
											'label' => esc_html__('Do crop images gallery', 'W2MB'),
											'default' => array(get_option('w2mb_big_slide_bg_mode')),
											'items' => array(
													array(
														'value' => 'cover',
														'label' => esc_html__('Cut off image to fit width and height of main slide', 'W2MB'),	
													),
													array(
														'value' => 'contain',
														'label' => esc_html__('Full image inside main slide', 'W2MB'),	
													),
											),
											'description' => esc_html__('Works when gallery height is limited (not set to 0)', 'W2MB'),
										),
									),
								),
								'excerpts' => array(
									'type' => 'section',
									'title' => esc_html__('Description & Excerpt settings', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_description',
											'label' => esc_html__('Enable description field', 'W2MB'),
											'default' => get_option('w2mb_enable_description'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_summary',
											'label' => esc_html__('Enable summary field', 'W2MB'),
											'default' => get_option('w2mb_enable_summary'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_excerpt_length',
											'label' => esc_html__('Excerpt max length', 'W2MB'),
											'description' => esc_html__('Insert the number of words you want to show in the listings excerpts', 'W2MB'),
											'default' => get_option('w2mb_excerpt_length'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_cropped_content_as_excerpt',
											'label' => esc_html__('Use cropped content as excerpt', 'W2MB'),
											'description' => esc_html__('When excerpt field is empty - use cropped main content', 'W2MB'),
											'default' => get_option('w2mb_cropped_content_as_excerpt'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_strip_excerpt',
											'label' => esc_html__('Strip HTML from excerpt', 'W2MB'),
											'description' => esc_html__('Check the box if you want to strip HTML from the excerpt content only', 'W2MB'),
											'default' => get_option('w2mb_strip_excerpt'),
										),
									),
								),
							),
						),
						'search' => array(
							'name' => 'search',
							'title' => esc_html__('Search shortcode', 'W2MB'),
							'icon' => 'font-awesome:w2mb-fa-search',
							'controls' => array(
								'search' => array(
									'type' => 'section',
									'title' => esc_html__('Search shortcode settings', 'W2MB'),
									'description' => esc_html__('These are default settings for all [mapbox-search] shortcodes.', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_categories_search',
											'label' => esc_html__('Enable categories search', 'W2MB'),
											'default' => get_option('w2mb_show_categories_search'),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_categories_search_nesting_level',
											'label' => esc_html__('Categories search depth level', 'W2MB'),
											'min' => 1,
											'max' => 3,
											'default' => get_option('w2mb_categories_search_nesting_level'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_keywords_search',
											'label' => esc_html__('Enable keywords search', 'W2MB'),
											'default' => get_option('w2mb_show_keywords_search'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_keywords_ajax_search',
											'label' => esc_html__('Enable listings autosuggestions by keywords', 'W2MB'),
											'default' => get_option('w2mb_keywords_ajax_search'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_keywords_search_examples',
											'label' => esc_html__('Keywords examples', 'W2MB'),
											'description' => esc_html__('Comma-separated list of suggestions to try to search', 'W2MB'),
											'default' => get_option('w2mb_keywords_search_examples'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_locations_search',
											'label' => esc_html__('Enable locations search', 'W2MB'),
											'default' => get_option('w2mb_show_locations_search'),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_locations_search_nesting_level',
											'label' => esc_html__('Locations search depth level', 'W2MB'),
											'min' => 1,
											'max' => 3,
											'default' => get_option('w2mb_locations_search_nesting_level'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_address_search',
											'label' => esc_html__('Enable address search', 'W2MB'),
											'default' => get_option('w2mb_show_address_search'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_location_count_in_search',
											'label' => esc_html__('Show listings counts in locations search dropboxes', 'W2MB'),
											'default' => get_option('w2mb_show_location_count_in_search'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_hide_empty_locations',
											'label' => esc_html__('Hide empty locations', 'W2MB'),
											'description' => esc_html__('This setting is actual for search shortcode and search widget', 'W2MB'),
											'default' => get_option('w2mb_hide_empty_locations'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_category_count_in_search',
											'label' => esc_html__('Show listings counts in categories search dropboxes', 'W2MB'),
											'default' => get_option('w2mb_show_category_count_in_search'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_hide_empty_categories',
											'label' => esc_html__('Hide empty categories', 'W2MB'),
											'description' => esc_html__('This setting is actual for search shortcode and search widget', 'W2MB'),
											'default' => get_option('w2mb_hide_empty_categories'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_show_radius_search',
											'label' => esc_html__('Show locations radius search', 'W2MB'),
											'description' => sprintf(esc_html__('Check geolocation %s.', 'W2MB'), '<a href="'.admin_url('admin.php?page=w2mb_debug').'">'.esc_html__('response').'</a>'),
											'default' => get_option('w2mb_show_radius_search'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2mb_miles_kilometers_in_search',
											'label' => esc_html__('Dimension in radius search', 'W2MB'),
											'items' => array(
												array(
													'value' => 'miles',
													'label' => esc_html__('miles', 'W2MB'),
												),
												array(
													'value' => 'kilometers',
													'label' => esc_html__('kilometers', 'W2MB'),
												),
											),
											'default' => array(get_option('w2mb_miles_kilometers_in_search')),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_radius_search_min',
											'label' => esc_html__('Minimum radius search', 'W2MB'),
											'default' => get_option('w2mb_radius_search_min'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_radius_search_max',
											'label' => esc_html__('Maximum radius search', 'W2MB'),
											'default' => get_option('w2mb_radius_search_max'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_radius_search_default',
											'label' => esc_html__('Default radius search', 'W2MB'),
											'description' => esc_html__('If you have problems with radius search: check your MapBox API key. ', 'W2MB') . ' ' . sprintf(esc_html__('Check geolocation %s.', 'W2MB'), '<a href="'.admin_url('admin.php?page=w2mb_debug').'">'.esc_html__('response').'</a>'),
											'default' => get_option('w2mb_radius_search_default'),
											'validation' => 'numeric',
										),
									),
								),
							),
						),
						'addresses' => array(
							'name' => 'addresses',
							'title' => esc_html__('Markers & Addresses', 'W2MB'),
							'icon' => 'font-awesome:w2mb-fa-map-marker',
							'controls' => array(
								'addresses' => array(
									'type' => 'section',
									'title' => esc_html__('Addresses settings', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'textbox',
											'name' => 'w2mb_default_geocoding_location',
											'label' => esc_html__('Default country/state for correct geocoding', 'W2MB'),
											'description' => esc_html__('This value needed when you build local store locator, all your listings place in one local area - country or state. This hidden string will be automatically added to the address for correct geocoding when users create/edit listings and when they search by address.', 'W2MB'),
											'default' => get_option('w2mb_default_geocoding_location'),
										),
										array(
											'type' => 'sorter',
											'name' => 'w2mb_addresses_order',
											'label' => esc_html__('Address format', 'W2MB'),
									 		'items' => array(
									 			array('value' => 'location', 'label' => esc_html__('Selected location', 'W2MB')),
									 			array('value' => 'line_1', 'label' => esc_html__('Address Line 1', 'W2MB')),
									 			array('value' => 'line_2', 'label' => esc_html__('Address Line 2', 'W2MB')),
									 			array('value' => 'zip', 'label' => esc_html__('Zip code or postal index', 'W2MB')),
									 			array('value' => 'space1', 'label' => esc_html__('-- Space ( ) --', 'W2MB')),
									 			array('value' => 'space2', 'label' => esc_html__('-- Space ( ) --', 'W2MB')),
									 			array('value' => 'space3', 'label' => esc_html__('-- Space ( ) --', 'W2MB')),
									 			array('value' => 'comma1', 'label' => esc_html__('-- Comma (,) --', 'W2MB')),
									 			array('value' => 'comma2', 'label' => esc_html__('-- Comma (,) --', 'W2MB')),
									 			array('value' => 'comma3', 'label' => esc_html__('-- Comma (,) --', 'W2MB')),
									 			array('value' => 'break1', 'label' => esc_html__('-- Line Break --', 'W2MB')),
									 			array('value' => 'break2', 'label' => esc_html__('-- Line Break --', 'W2MB')),
									 			array('value' => 'break3', 'label' => esc_html__('-- Line Break --', 'W2MB')),
									 		),
											'description' => esc_html__('Order address elements as you wish, commas and spaces help to build address line.'),
											'default' => get_option('w2mb_addresses_order'),
										),
										array(
											'type' => 'select',
											'name' => 'w2mb_address_autocomplete_code',
											'label' => esc_html__('Restriction of address fields for one specific country (autocomplete submission and search fields)', 'W2MB'),
									 		'items' => $country_codes,
											'default' => get_option('w2mb_address_autocomplete_code'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_address_line_1',
											'label' => esc_html__('Enable address line 1 field', 'W2MB'),
											'default' => get_option('w2mb_enable_address_line_1'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_address_line_2',
											'label' => esc_html__('Enable address line 2 field', 'W2MB'),
											'default' => get_option('w2mb_enable_address_line_2'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_postal_index',
											'label' => esc_html__('Enable zip code', 'W2MB'),
											'default' => get_option('w2mb_enable_postal_index'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_additional_info',
											'label' => esc_html__('Enable additional info field', 'W2MB'),
											'default' => get_option('w2mb_enable_additional_info'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_manual_coords',
											'label' => esc_html__('Enable manual coordinates fields', 'W2MB'),
											'default' => get_option('w2mb_enable_manual_coords'),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_default_map_zoom',
											'label' => esc_html__('Default map zoom level (for submission page)', 'W2MB'),
									 		'min' => 1,
									 		'max' => 19,
											'default' => get_option('w2mb_default_map_zoom'),
										),
									),
								),
								'markers' => array(
									'type' => 'section',
									'title' => esc_html__('Map markers & InfoWindow settings', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_users_markers',
											'label' => esc_html__('Allow users to select markers', 'W2MB'),
											'default' => get_option('w2mb_enable_users_markers'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2mb_map_markers_type',
											'label' => esc_html__('Type of Map Markers', 'W2MB'),
											'items' => array(
												array(
													'value' => 'icons',
													'label' =>esc_html__('Font Awesome icons (recommended)', 'W2MB'),
												),
												array(
													'value' => 'images',
													'label' =>esc_html__('PNG images', 'W2MB'),
												),
											),
											'default' => array(
													get_option('w2mb_map_markers_type')
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2mb_default_marker_color',
											'label' => esc_html__('Default Map Marker color', 'W2MB'),
											'default' => get_option('w2mb_default_marker_color'),
											'description' => esc_html__('For Font Awesome icons.', 'W2MB'),
											'dependency' => array(
												'field'    => 'w2mb_map_markers_type',
												'function' => 'w2mb_map_markers_icons_setting',
											),
										),
										array(
											'type' => 'fontawesome',
											'name' => 'w2mb_default_marker_icon',
											'label' => esc_html__('Default Map Marker icon'),
											'description' => esc_html__('For Font Awesome icons.', 'W2MB'),
											'default' => array(
												get_option('w2mb_default_marker_icon')
											),
											'dependency' => array(
												'field'    => 'w2mb_map_markers_type',
												'function' => 'w2mb_map_markers_icons_setting',
											),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_map_marker_size',
											'label' => esc_html__('Map marker size (in pixels)', 'W2MB'),
											'description' => esc_html__('For Font Awesome images.', 'W2MB'),
											'default' => get_option('w2mb_map_marker_size'),
									 		'min' => 10,
									 		'max' => 70,
											'dependency' => array(
												'field'    => 'w2mb_map_markers_type',
												'function' => 'w2mb_map_markers_icons_setting',
											),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_map_marker_width',
											'label' => esc_html__('Map marker width (in pixels)', 'W2MB'),
											'description' => esc_html__('For PNG images.', 'W2MB'),
											'default' => get_option('w2mb_map_marker_width'),
									 		'min' => 10,
									 		'max' => 64,
											'dependency' => array(
												'field'    => 'w2mb_map_markers_type',
												'function' => 'w2mb_map_markers_images_setting',
											),
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2mb_map_marker_height',
											'label' => esc_html__('Map marker height (in pixels)', 'W2MB'),
									 		'description' => esc_html__('For PNG images.', 'W2MB'),
											'default' => get_option('w2mb_map_marker_height'),
									 		'min' => 10,
									 		'max' => 64,
									 		'dependency' => array(
												'field'    => 'w2mb_map_markers_type',
												'function' => 'w2mb_map_markers_images_setting',
											),
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2mb_map_marker_anchor_x',
											'label' => esc_html__('Map marker anchor horizontal position (in pixels)', 'W2MB'),
									 		'description' => esc_html__('For PNG images.', 'W2MB'),
											'default' => get_option('w2mb_map_marker_anchor_x'),
									 		'min' => 0,
									 		'max' => 64,
									 		'dependency' => array(
												'field'    => 'w2mb_map_markers_type',
												'function' => 'w2mb_map_markers_images_setting',
											),
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2mb_map_marker_anchor_y',
											'label' => esc_html__('Map marker anchor vertical position (in pixels)', 'W2MB'),
									 		'description' => esc_html__('For PNG images.', 'W2MB'),
											'default' => get_option('w2mb_map_marker_anchor_y'),
									 		'min' => 0,
									 		'max' => 64,
									 		'dependency' => array(
												'field'    => 'w2mb_map_markers_type',
												'function' => 'w2mb_map_markers_images_setting',
											),
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2mb_map_infowindow_width',
											'label' => esc_html__('Map InfoWindow width (in pixels)', 'W2MB'),
											'default' => get_option('w2mb_map_infowindow_width'),
									 		'min' => 100,
									 		'max' => 600,
									 		'step' => 10,
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_map_infowindow_offset',
											'label' => esc_html__('Map InfoWindow vertical position above marker (in pixels)', 'W2MB'),
											'default' => get_option('w2mb_map_infowindow_offset'),
									 		'min' => 30,
									 		'max' => 120,
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_map_infowindow_logo_width',
											'label' => esc_html__('Map InfoWindow logo width (in pixels)', 'W2MB'),
											'default' => get_option('w2mb_map_infowindow_logo_width'),
									 		'min' => 40,
									 		'max' => 300,
											'step' => 10,
										),
									),
								),
							),
						),
						'notifications' => array(
							'name' => 'notifications',
							'title' => esc_html__('Email notifications', 'W2MB'),
							'icon' => 'font-awesome:w2mb-fa-envelope',
							'controls' => array(
								'notifications' => array(
									'type' => 'section',
									'title' => esc_html__('Email notifications', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'textbox',
											'name' => 'w2mb_admin_notifications_email',
											'label' => esc_html__('This email will be used for notifications to admin and in "From" field. Required to send emails.', 'W2MB'),
											'default' => get_option('w2mb_admin_notifications_email'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2mb_send_expiration_notification_days',
											'label' => esc_html__('Days before pre-expiration notification will be sent', 'W2MB'),
											'default' => get_option('w2mb_send_expiration_notification_days'),
										),
									 	array(
											'type' => 'textarea',
											'name' => 'w2mb_preexpiration_notification',
											'label' => esc_html__('Pre-expiration notification text', 'W2MB'),
											'default' => get_option('w2mb_preexpiration_notification'),
									 		'description' => esc_html__('Tags allowed: ', 'W2MB') . '[listing], [days], [link]',
										),
									 	array(
											'type' => 'textarea',
											'name' => 'w2mb_expiration_notification',
											'label' => esc_html__('Expiration notification text', 'W2MB'),
											'default' => get_option('w2mb_expiration_notification'),
									 		'description' => esc_html__('Tags allowed: ', 'W2MB') . '[listing], [link]',
										),
									),
								),
							),
						),
						'advanced' => array(
							'name' => 'advanced',
							'title' => esc_html__('Advanced settings', 'W2MB'),
							'icon' => 'font-awesome:w2mb-fa-gear',
							'controls' => array(
								'js_css' => array(
									'type' => 'section',
									'title' => esc_html__('JavaScript & CSS', 'W2MB'),
									'description' => esc_html__('Do not touch these settings if you do not know what they mean. It may cause lots of problems.', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_force_include_js_css',
											'label' => esc_html__('Include plugin JS and CSS files on all pages', 'W2MB'),
											'default' => get_option('w2mb_force_include_js_css'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_images_lightbox',
											'label' => esc_html__('Include lightbox slideshow library', 'W2MB'),
											'description' =>  esc_html__('Some themes and 3rd party plugins include own Lighbox library - this may cause conflicts.', 'W2MB'),
											'default' => get_option('w2mb_images_lightbox'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_notinclude_jqueryui_css',
											'label' => esc_html__('Do not include jQuery UI CSS', 'W2MB'),
									 		'description' =>  esc_html__('Some themes and 3rd party plugins include own jQuery UI CSS - this may cause conflicts in styles.', 'W2MB'),
											'default' => get_option('w2mb_notinclude_jqueryui_css'),
										),
									),
								),
								'miscellaneous' => array(
									'type' => 'section',
									'title' => esc_html__('Miscellaneous', 'W2MB'),
									'fields' => array(
									 	array(
											'type' => 'toggle',
											'name' => 'w2mb_prevent_users_see_other_media',
											'label' => esc_html__('Prevent users to see media items of another users', 'W2MB'),
											'default' => get_option('w2mb_prevent_users_see_other_media'),
										),
									 	array(
											'type' => 'toggle',
											'name' => 'w2mb_address_autocomplete',
											'label' => esc_html__('Enable autocomplete on addresses fields', 'W2MB'),
											'default' => get_option('w2mb_address_autocomplete'),
										),
									 	array(
											'type' => 'toggle',
											'name' => 'w2mb_address_geocode',
											'label' => esc_html__('Enable "Get my location" button on addresses fields', 'W2MB'),
											'default' => get_option('w2mb_address_geocode'),
									 		'description' => esc_html__("Requires https", "W2MB"),
										),
									),
								),
								'recaptcha' => array(
									'type' => 'section',
									'title' => esc_html__('reCaptcha settings', 'W2MB'),
									'fields' => array(
									 	array(
											'type' => 'toggle',
											'name' => 'w2mb_enable_recaptcha',
											'label' => esc_html__('Enable reCaptcha', 'W2MB'),
											'default' => get_option('w2mb_enable_recaptcha'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2mb_recaptcha_version',
											'label' => __('reCaptcha version', 'W2MB'),
											'default' => get_option('w2mb_recaptcha_version'),
									 		'items' => array(
												array('value' => 'v2', 'label' => __('reCaptcha v2', 'W2MB')),
												array('value' => 'v3', 'label' => __('reCaptcha v3', 'W2MB')),
											),
										),
									 	array(
											'type' => 'textbox',
											'name' => 'w2mb_recaptcha_public_key',
											'label' => esc_html__('reCaptcha site key', 'W2MB'),
											'description' => sprintf(esc_html__('get your reCAPTCHA API Keys %s', 'W2MB'), '<a href="http://www.google.com/recaptcha" target="_blank">here</a>'),
											'default' => get_option('w2mb_recaptcha_public_key'),
										),
									 	array(
											'type' => 'textbox',
											'name' => 'w2mb_recaptcha_private_key',
											'label' => esc_html__('reCaptcha secret key', 'W2MB'),
											'default' => get_option('w2mb_recaptcha_private_key'),
										),
									),
								),
							),
						),
						'customization' => array(
							'name' => 'customization',
							'title' => esc_html__('Customization', 'W2MB'),
							'icon' => 'font-awesome:w2mb-fa-check',
							'controls' => array(
								'color_schemas' => array(
									'type' => 'section',
									'title' => esc_html__('Color palettes', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2mb_compare_palettes',
											'label' => esc_html__('Compare palettes at the frontend', 'W2MB'),
									 		'description' =>  esc_html__('Do not forget to switch off this setting when comparison will be completed.', 'W2MB'),
											'default' => get_option('w2mb_compare_palettes'),
										),
										array(
											'type' => 'select',
											'name' => 'w2mb_color_scheme',
											'label' => esc_html__('Color palette', 'W2MB'),
											'items' => array(
												array('value' => 'default', 'label' => esc_html__('Default', 'W2MB')),
												array('value' => 'orange', 'label' => esc_html__('Orange', 'W2MB')),
												array('value' => 'red', 'label' => esc_html__('Red', 'W2MB')),
												array('value' => 'yellow', 'label' => esc_html__('Yellow', 'W2MB')),
												array('value' => 'green', 'label' => esc_html__('Green', 'W2MB')),
												array('value' => 'gray', 'label' => esc_html__('Gray', 'W2MB')),
												array('value' => 'blue', 'label' => esc_html__('Blue', 'W2MB')),
											),
											'default' => array(get_option('w2mb_color_scheme')),
										),
										array(
											'type' => 'notebox',
											'description' => esc_attr__("Don't forget to clear cache of your browser and on server (when used) after customization changes were made.", 'W2MB'),
											'status' => 'warning',
										),
									),
								),
								'main_colors' => array(
									'type' => 'section',
									'title' => esc_html__('Main colors', 'W2MB'),
									'fields' => array(
										array(
												'type' => 'color',
												'name' => 'w2mb_primary_color',
												'label' => esc_html__('Primary color', 'W2MB'),
												'description' =>  esc_html__('The color of categories, tags labels, map info window caption, pagination elements', 'W2MB'),
												'default' => get_option('w2mb_primary_color'),
												'binding' => array(
														'field' => 'w2mb_color_scheme',
														'function' => 'w2mb_affect_setting_w2mb_primary_color'
												),
										),
										array(
												'type' => 'color',
												'name' => 'w2mb_secondary_color',
												'label' => esc_html__('Secondary color', 'W2MB'),
												'default' => get_option('w2mb_secondary_color'),
												'binding' => array(
														'field' => 'w2mb_color_scheme',
														'function' => 'w2mb_affect_setting_w2mb_secondary_color'
												),
										),
									),
								),
								'links_colors' => array(
									'type' => 'section',
									'title' => esc_html__('Links & buttons', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'color',
											'name' => 'w2mb_links_color',
											'label' => esc_html__('Links color', 'W2MB'),
											'default' => get_option('w2mb_links_color'),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_links_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2mb_links_hover_color',
											'label' => esc_html__('Links hover color', 'W2MB'),
											'default' => get_option('w2mb_links_hover_color'),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_links_hover_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2mb_button_1_color',
											'label' => esc_html__('Button primary color', 'W2MB'),
											'default' => get_option('w2mb_button_1_color'),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_button_1_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2mb_button_2_color',
											'label' => esc_html__('Button secondary color', 'W2MB'),
											'default' => get_option('w2mb_button_2_color'),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_button_2_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2mb_button_text_color',
											'label' => esc_html__('Button text color', 'W2MB'),
											'default' => get_option('w2mb_button_text_color'),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_button_text_color'
											),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_button_gradient',
											'label' => esc_html__('Use gradient on buttons', 'W2MB'),
											'description' => esc_html__('This will remove all icons from buttons'),
											'default' => get_option('w2mb_button_gradient'),
										),
									),
								),
								'search_colors' => array(
									'type' => 'section',
									'title' => esc_html__('Search block', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'color',
											'name' => 'w2mb_search_bg_color',
											'label' => esc_html__('Search form background color', 'W2MB'),
											'default' => get_option('w2mb_search_bg_color'),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_search_bg_color'
											),
										),
										array(
											'type' => 'slider',
											'name' => 'w2mb_search_bg_opacity',
											'label' => esc_html__('Opacity of search form background, in %', 'W2MB'),
											'min' => '0',
											'max' => '100',
											'default' => get_option('w2mb_search_bg_opacity'),
										),
										array(
											'type' => 'color',
											'name' => 'w2mb_search_text_color',
											'label' => esc_html__('Search form text color', 'W2MB'),
											'default' => get_option('w2mb_search_text_color'),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_search_text_color'
											),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2mb_search_overlay',
											'label' => esc_html__('Use overlay on search form', 'W2MB'),
											'default' => get_option('w2mb_search_overlay'),
										),
									),
								),
								'misc_colors' => array(
									'type' => 'section',
									'title' => esc_html__('Misc settings', 'W2MB'),
									'fields' => array(
										array(
											'type' => 'radioimage',
											'name' => 'w2mb_jquery_ui_schemas',
											'label' => esc_html__('jQuery UI Style', 'W2MB'),
											'description' =>  esc_html__('Controls the color of calendar, dialogs and slider UI widgets', 'W2MB') . (get_option('w2mb_notinclude_jqueryui_css') ? ' <strong>' . esc_html__('Warning: You have enabled not to include jQuery UI CSS on Advanced settings tab. Selected style will not be applied.', 'W2MB') . '</strong>' : ''),
									 		'items' => array(
									 			array(
									 				'value' => 'blitzer',
									 				'label' => 'Blitzer',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/blitzer/thumb.png'
									 			),
									 			array(
									 				'value' => 'smoothness',
									 				'label' => 'Smoothness',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/smoothness/thumb.png'
									 			),
									 			array(
									 				'value' => 'redmond',
									 				'label' => 'Redmond',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/redmond/thumb.png'
									 			),
									 			array(
									 				'value' => 'ui-darkness',
									 				'label' => 'UI Darkness',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/ui-darkness/thumb.png'
									 			),
									 			array(
									 				'value' => 'ui-lightness',
									 				'label' => 'UI Lightness',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/ui-lightness/thumb.png'
									 			),
									 			array(
									 				'value' => 'trontastic',
									 				'label' => 'Trontastic',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/trontastic/thumb.png'
									 			),
									 			array(
									 				'value' => 'start',
									 				'label' => 'Start',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/start/thumb.png'
									 			),
									 			array(
									 				'value' => 'sunny',
									 				'label' => 'Sunny',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/sunny/thumb.png'
									 			),
									 			array(
									 				'value' => 'overcast',
									 				'label' => 'Overcast',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/overcast/thumb.png'
									 			),
									 			array(
									 				'value' => 'le-frog',
									 				'label' => 'Le Frog',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/le-frog/thumb.png'
									 			),
									 			array(
									 				'value' => 'hot-sneaks',
									 				'label' => 'Hot Sneaks',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/hot-sneaks/thumb.png'
									 			),
									 			array(
									 				'value' => 'excite-bike',
									 				'label' => 'Excite Bike',
									 				'img' => W2MB_RESOURCES_URL . 'css/jquery-ui/themes/excite-bike/thumb.png'
									 			),
									 		),
											'default' => array(get_option('w2mb_jquery_ui_schemas')),
											'binding' => array(
												'field' => 'w2mb_color_scheme',
												'function' => 'w2mb_affect_setting_w2mb_jquery_ui_schemas'
											),
										),
									),
								),
							),
						),
					)
				),
				//'menu_page' => 'w2mb_settings',
				'use_auto_group_naming' => true,
				'use_util_menu' => false,
				'minimum_role' => $capability,
				'layout' => 'fixed',
				'page_title' => esc_html__('Maps settings', 'W2MB'),
				'menu_label' => esc_html__('Maps settings', 'W2MB'),
		);
		
		// adapted for WPML /////////////////////////////////////////////////////////////////////////
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$theme_options['template']['menus']['advanced']['controls']['wpml'] = array(
				'type' => 'section',
				'title' => esc_html__('WPML Settings', 'W2MB'),
				'fields' => array(
					array(
						'type' => 'toggle',
						'name' => 'w2mb_map_language_from_wpml',
						'label' => esc_html__('Force WPML language on maps', 'W2MB'),
						'description' => esc_html__("Ignore the browser's language setting and force it to display information in a particular WPML language", 'W2MB'),
						'default' => get_option('w2mb_map_language_from_wpml'),
					),
				),
			);
		}
		
		$theme_options = apply_filters('w2mb_build_settings', $theme_options);

		$VP_W2MB_Option = new VP_W2MB_Option($theme_options);
	}

	public function save_option($opts, $old_opts, $status) {
		global $w2mb_wpml_dependent_options, $sitepress;

		if ($status) {
			foreach ($opts AS $option=>$value) {
				// adapted for WPML
				if (in_array($option, $w2mb_wpml_dependent_options)) {
					if (function_exists('wpml_object_id_filter') && $sitepress) {
						if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE) {
							update_option($option.'_'.ICL_LANGUAGE_CODE, $value);
							continue;
						}
					}
				}

				if (
					$option == 'w2mb_mapbox_api_key'
				) {
					$value = trim($value);
				}
				update_option($option, $value);
			}
			
			w2mb_save_dynamic_css();
		}
	}
}

function w2mb_save_dynamic_css() {
	$upload_dir = wp_upload_dir();
	$filename = trailingslashit($upload_dir['basedir']) . 'w2mb-plugin.css';
		
	ob_start();
	include W2MB_PATH . '/classes/customization/dynamic_css.php';
	$dynamic_css = ob_get_contents();
	ob_get_clean();
		
	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once(ABSPATH .'/wp-admin/includes/file.php');
		WP_Filesystem();
	}
		
	if ($wp_filesystem) {
		$wp_filesystem->put_contents(
				$filename,
				$dynamic_css,
				FS_CHMOD_FILE // predefined mode settings for WP files
		);
	}
}

// adapted for WPML
function w2mb_get_wpml_dependent_option_name($option) {
	global $w2mb_wpml_dependent_options, $sitepress;

	if (in_array($option, $w2mb_wpml_dependent_options))
		if (function_exists('wpml_object_id_filter') && $sitepress)
			if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE)
				if (get_option($option.'_'.ICL_LANGUAGE_CODE) !== false)
					return $option.'_'.ICL_LANGUAGE_CODE;

	return $option;
}
function w2mb_get_wpml_dependent_option($option) {
	return get_option(w2mb_get_wpml_dependent_option_name($option));
}
function w2mb_get_wpml_dependent_option_description() {
	global $sitepress;
	return ((function_exists('wpml_object_id_filter') && $sitepress) ? sprintf(esc_html__('%s This is multilingual option, each language may have own value.', 'W2MB'), '<br /><img src="'.W2MB_RESOURCES_URL . 'images/multilang.png" /><br />') : '');
}

function w2mb_map_markers_icons_setting($value) {
	if ($value == 'icons') {
		return true;
	}
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_map_markers_icons_setting');

function w2mb_map_markers_images_setting($value) {
	if ($value == 'images') {
		return true;
	}
}
VP_W2MB_Security::instance()->whitelist_function('w2mb_map_markers_images_setting');

?>