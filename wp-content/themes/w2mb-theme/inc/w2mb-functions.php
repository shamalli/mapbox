<?php

/* add_filter('breadcrumb_trail_items', 'wdt_directory_breadcrumbs', 10, 2);
function wdt_directory_breadcrumbs($items, $args) {
	global $w2mb_instance;

	if (
		$w2mb_instance &&
		(
				($directory_controller = $w2mb_instance->getShortcodeProperty(w2mb_MAIN_SHORTCODE)) ||
				($directory_controller = $w2mb_instance->getShortcodeProperty(w2mb_LISTING_SHORTCODE)) ||
				($directory_controller = $w2mb_instance->getShortcodeProperty('webdirectory-listing'))
		) &&
		!empty($directory_controller->breadcrumbs)
	) {
		$items = $directory_controller->breadcrumbs;
	}
	return $items;
} */

?>