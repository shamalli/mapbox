<?php

function w2mb_submitUrl($path = '') {
	global $w2mb_instance;
	
	$submit_page_url = '';

	if (!empty($w2mb_instance->submit_page)) {
		$submit_page_url = $w2mb_instance->submit_page['url'];
	}
		
	// adapted for WPML
	global $sitepress;
	if (function_exists('wpml_object_id_filter') && $sitepress) {
		if ($sitepress->get_option('language_negotiation_type') == 3) {
			// remove any previous value.
			$submit_page_url = remove_query_arg('lang', $submit_page_url);
		}
	}

	if (!is_array($path)) {
		if ($path) {
			// found that on some instances of WP "native" trailing slashes may be missing
			$url = rtrim($submit_page_url, '/') . '/' . rtrim($path, '/') . '/';
		} else
			$url = $submit_page_url;
	} else
		$url = add_query_arg($path, $submit_page_url);

	// adapted for WPML
	global $sitepress;
	if (function_exists('wpml_object_id_filter') && $sitepress) {
		$url = $sitepress->convert_url($url);
	}

	return $url;
}

function w2mb_dashboardUrl($path = '') {
	global $w2mb_instance;
	
	if ($w2mb_instance->dashboard_page_url) {
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			if ($sitepress->get_option('language_negotiation_type') == 3) {
				// remove any previous value.
				$w2mb_instance->dashboard_page_url = remove_query_arg('lang', $w2mb_instance->dashboard_page_url);
			}
		}
	
		if (!is_array($path)) {
			if ($path) {
				// found that on some instances of WP "native" trailing slashes may be missing
				$url = rtrim($w2mb_instance->dashboard_page_url, '/') . '/' . rtrim($path, '/') . '/';
			} else
				$url = $w2mb_instance->dashboard_page_url;
		} else
			$url = add_query_arg($path, $w2mb_instance->dashboard_page_url);
	
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$url = $sitepress->convert_url($url);
		}
	} else {
		$url = site_url();
	}

	return $url;
}

?>