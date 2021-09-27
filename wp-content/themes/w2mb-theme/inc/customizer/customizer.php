<?php

function wdt_customize_register($wp_customize) {
	
	$wp_customize->add_panel('theme_settings',
			array(
					'title' => __('Theme Settings', 'WDT'),
					'title',
					'priority' => 60,
			)
	);
	
	// Fonts Section
	$wp_customize->add_section('fonts',
			array(
					'title' => __('Fonts', 'WDT'),
					'panel' => 'theme_settings',
		/* 'default' => 4,
		  'section' => 'title_tagline',
		  'label' => __( 'Range' ),
		  'description' => __( 'This is the range control description.' ),
		) */
			)
	);
	
	$wp_customize->add_setting('body_font',
			array(
					'default' => wdt_get_option('body_font'),
					'sanitize_callback' => 'wdt_sanitize_font',
			)
	);
	$wp_customize->add_control(
			new Font_Dropdown_Custom_Control($wp_customize, 'body_font', array(
					'label' => __('Body Font', 'WDT'),
					'section' => 'fonts',
        ))
	);
	
	$wp_customize->add_setting('body_font_size',
			array(
					'default' => wdt_get_option('body_font_size'),
			)
	);
	$wp_customize->add_control('body_font_size',
			array(
					'label' => __('Body Font Size', 'WDT'),
					'description'   => __('in pixels', 'WDT'),
					'type' => 'number',
					'section' => 'fonts',
					'input_attrs' => array('min' => 10, 'max' => 60),
			)
	);

	$wp_customize->add_setting('nav_menu_font',
			array(
					'default' => wdt_get_option('nav_menu_font'),
					'sanitize_callback' => 'wdt_sanitize_font',
			)
	);
	$wp_customize->add_control(
			new Font_Dropdown_Custom_Control($wp_customize, 'nav_menu_font', array(
					'label' => __('Navigation Menu Font', 'WDT'),
					'section' => 'fonts',
        ))
	);
	
	$wp_customize->add_setting('nav_menu_font_size',
			array(
					'default' => wdt_get_option('nav_menu_font_size'),
			)
	);
	$wp_customize->add_control('nav_menu_font_size',
			array(
					'label' => __('Navigation Menu Font Size', 'WDT'),
					'description' => __('in pixels', 'WDT'),
					'type' => 'number',
					'section' => 'fonts',
					'input_attrs' => array('min' => 10, 'max' => 60),
			)
	);

	$wp_customize->add_setting('headings_font',
			array(
					'default' => wdt_get_option('headings_font'),
					'sanitize_callback' => 'wdt_sanitize_font',
			)
	);
	$wp_customize->add_control(
			new Font_Dropdown_Custom_Control($wp_customize, 'headings_font', array(
					'label' => __('Headings Font', 'WDT'),
					'section' => 'fonts',
        	))
	);
	
	// Header Section
	$wp_customize->add_section('header',
			array(
					'title' => __('Header', 'WDT'),
					'panel' => 'theme_settings',
			)
	);
	
	$wp_customize->add_setting('site_branding_width',
			array(
					'default' => wdt_get_option('site_branding_width'),
			)
	);
	$wp_customize->add_control('site_branding_width',
			array(
					'label' => __('Site branding width (logo, site title, tagline)', 'WDT'),
					'description' => __('in pixels', 'WDT'),
					'type' => 'number',
					'section' => 'header',
					'input_attrs' => array('min' => 0, 'max' => 500),
			)
	);
	
	$wp_customize->add_setting('show_title',
			array(
					'default' => wdt_get_option('show_title'),
			)
	);
	$wp_customize->add_control('show_title',
			array(
					'label' => __('Show Site Title', 'WDT'),
					'section' => 'header',
					'type' => 'checkbox',
			)
	);
	
	$wp_customize->add_setting('show_tagline',
			array(
					'default' => wdt_get_option('show_tagline'),
			)
	);
	$wp_customize->add_control('show_tagline',
			array(
					'label' => __('Show TagLine', 'WDT'),
					'section' => 'header',
					'type' => 'checkbox',
			)
	);
	
	$wp_customize->add_setting('header_fixed_scroll_enable',
			array(
					'default' => wdt_get_option('header_fixed_scroll_enable'),
			)
	);
	$wp_customize->add_control('header_fixed_scroll_enable',
			array(
					'label' => esc_html__('Enable Fixed Scroll Header', 'WDT'),
					'section' => 'header',
					'type' => 'checkbox',
			)
	);

	$wp_customize->add_setting('header_background',
			array(
					'default' => wdt_get_option('header_background'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'header_background', array(
					'label'   => 'Header Background Color',
					'section' => 'header',
					//'settings' => 'color_setting',
			))
	);
	$wp_customize->add_setting('header_text_color',
			array(
					'default' => wdt_get_option('header_text_color'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'header_text_color', array(
					'label'   => 'Menu Text Color',
					'section' => 'header',
					//'settings' => 'color_setting',
			))
	);
	
	/* $wp_customize->add_setting('header_contact_number',
			array(
					'default' => '',
			)
	);
	$wp_customize->add_control('header_contact_number',
			array(
					'label' => __('Contact Number', 'WDT'),
					'section' => 'header',
					'type' => 'text',
			)
	);

	$wp_customize->add_setting('header_contact_email',
			array(
					'default' => '',
			)
	);
	$wp_customize->add_control('header_contact_email',
			array(
					'label' => __('Contact Email', 'WDT'),
					'section' => 'header',
					'type' => 'text',
			)
	);

	$wp_customize->add_setting('header_contact_address',
			array(
					'default' => '',
			)
	);
	$wp_customize->add_control('header_contact_address',
			array(
					'label' => __('Contact Address', 'WDT'),
					'section' => 'header',
					'type' => 'text',
			)
	);
	
	$wp_customize->add_setting('header_search',
			array(
					'default' => false,
			)
	);
	$wp_customize->add_control('header_search',
			array(
					'label' => __('Enable Search Form', 'WDT'),
					'section' => 'header',
					'type' => 'checkbox',
			)
	); */
	
	$wp_customize->add_setting('nav_menu_paddings',
			array(
					'default' => wdt_get_option('nav_menu_paddings'),
			)
	);
	$wp_customize->add_control('nav_menu_paddings',
			array(
					'label' => __('Horizontal Paddings in Navigation Menu items', 'WDT'),
					'description' => __('in pixels', 'WDT'),
					'type' => 'number',
					'section' => 'header',
					'input_attrs' => array('min' => 0, 'max' => 200),
			)
	);

	$wp_customize->add_setting('page_header_paddings',
			array(
					'default' => wdt_get_option('page_header_paddings'),
			)
	);
	$wp_customize->add_control('page_header_paddings',
			array(
					'label' => __('Page header vertical paddings', 'WDT'),
					'description' => __('in pixels', 'WDT'),
					'type' => 'number',
					'section' => 'header',
					'input_attrs' => array('min' => 0, 'max' => 600),
			)
	);
	
	$wp_customize->add_setting('page_header_background',
			array(
					'default' => wdt_get_option('page_header_background'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'page_header_background', array(
					'label'   => 'Page Header Background Color',
					'section' => 'header',
					//'settings' => 'color_setting',
			))
	);

	$wp_customize->add_setting('page_header_text_color',
			array(
					'default' => wdt_get_option('page_header_text_color'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'page_header_text_color', array(
					'label'   => 'Page Header Text Color',
					'section' => 'header',
					//'settings' => 'color_setting',
			))
	);
	
	$wp_customize->add_setting('header_default_bg_image',
			array(
					'default' => wdt_get_option('header_default_bg_image'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Image_Control($wp_customize, 'header_default_bg_image', array(
					'label' => __('Page header default image', 'WDT'),
					'description' => __('will be used when no image was assigned with a page', 'WDT'),
					'type' => 'image',
					'section' => 'header',
					//'settings'  => 'header_default_bg_image',
			))
	);

	
	// Links Section
	$wp_customize->add_section('links_buttons',
			array(
					'title' => __('Links & Buttons', 'WDT'),
					'panel' => 'theme_settings',
			)
	);
	
	$wp_customize->add_setting('links_color',
			array(
					'default' => wdt_get_option('links_color'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'links_color', array(
					'label'   => 'Links Color',
					'section' => 'links_buttons',
					//'settings' => 'color_setting',
			))
	);
	$wp_customize->add_setting('links_color_hover',
			array(
					'default' => wdt_get_option('links_color_hover'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'links_color_hover', array(
					'label'   => 'Links Hover Color',
					'section' => 'links_buttons',
					//'settings' => 'color_setting',
			))
	);
	
	$wp_customize->add_setting('buttons_color',
			array(
					'default' => wdt_get_option('buttons_color'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'buttons_color', array(
					'label'   => 'Buttons Color',
					'section' => 'links_buttons',
					//'settings' => 'color_setting',
			))
	);
	$wp_customize->add_setting('buttons_color_hover',
			array(
					'default' => wdt_get_option('buttons_color_hover'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'buttons_color_hover', array(
					'label'   => 'Buttons Hover Color',
					'section' => 'links_buttons',
					//'settings' => 'color_setting',
			))
	);
	$wp_customize->add_setting('buttons_color_text',
			array(
					'default' => wdt_get_option('buttons_color_text'),
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize, 'buttons_color_text', array(
					'label'   => 'Buttons Text Color',
					'section' => 'links_buttons',
					//'settings' => 'color_setting',
			))
	);
}
add_action('customize_register', 'wdt_customize_register');

?>
