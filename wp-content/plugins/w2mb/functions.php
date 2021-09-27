<?php 

if (!function_exists('w2mb_getValue')) {
	function w2mb_getValue($target, $key, $default = false) {
		$target = is_object($target) ? (array) $target : $target;
	
		if (is_array($target) && isset($target[$key])) {
			$value = $target[$key];
		} else {
			$value = $default;
		}
	
		$value = apply_filters('w2mb_get_value', $value, $target, $key, $default);
		return $value;
	}
}

add_filter('wp_redirect', 'w2mb_redirect_with_messages');
function w2mb_redirect_with_messages($location) {
	global $w2mb_messages;
	
	if ($w2mb_messages) {
		$messages = $w2mb_messages;
		
		$location = remove_query_arg('w2mb_messages', $location);
		
		$r_messages = array();
		foreach ($messages AS $type=>$messages_array) {
			foreach ($messages[$type] AS $key=>$message) {
				// do not take messages containing any HTML
				if ($message == strip_tags($message)) {
					$r_messages[$type][$key] = urlencode($message);
				}
			}
		}
		
		if ($r_messages) {
			$location = add_query_arg(array('w2mb_messages' => $r_messages), $location);
		}
	}
	
	return $location;
}

if (!function_exists('w2mb_addMessage')) {
	function w2mb_addMessage($message, $type = 'updated') {
		global $w2mb_messages;
		
		if (is_array($message)) {
			foreach ($message AS $m) {
				w2mb_addMessage($m, $type);
			}
			return ;
		}
	
		if (!isset($w2mb_messages[$type]) || (isset($w2mb_messages[$type]) && !in_array($message, $w2mb_messages[$type]))) {
			$w2mb_messages[$type][] = $message;
		}
	}
}

if (!function_exists('w2mb_renderMessages')) {
	function w2mb_renderMessages($message = false, $type = false) {
		global $w2mb_messages;
	
		if (!$message) {
			$messages = array();
			
			if (!empty($_GET['w2mb_messages']) && is_array($_GET['w2mb_messages'])) {
				foreach ($_GET['w2mb_messages'] AS $type=>$messages_array) {
					foreach ($_GET['w2mb_messages'][$type] AS $message) {
						$messages[$type][] = esc_html($message);
					}
				}
			}
			
			if (isset($w2mb_messages) && is_array($w2mb_messages) && $w2mb_messages) {
				foreach ($w2mb_messages AS $type=>$messages_array) {
					foreach ($w2mb_messages[$type] AS $message) {
						$messages[$type][] = $message;
					}
				}
			}
		} else {
			$messages[$type][] = $message;
		}
		
		$messages = w2mb_superUnique($messages);
	
		foreach ($messages AS $type=>$messages_array) {
			$message_class = (is_admin()) ? $type : "w2mb-" . $type;

			echo '<div class="' . esc_attr($message_class) . '">';
			foreach ($messages_array AS $message) {
				echo '<p>' . trim(preg_replace("/<p>(.*?)<\/p>/", "$1", $message)) . '</p>';
			}
			echo '</div>';
		}
	}
	function w2mb_superUnique($array) {
		$result = array_map("unserialize", array_unique(array_map("serialize", $array)));
		foreach ($result as $key => $value)
			if (is_array($value))
				$result[$key] = w2mb_superUnique($value);
		return $result;
	}
}

function w2mb_calcExpirationDate($date, $level) {
	$date = strtotime('+'.$level->active_period_days.' day', $date);
	$date = strtotime('+'.$level->active_period_months.' month', $date);
	$date = strtotime('+'.$level->active_period_years.' year', $date);
	
	return $date;
}

/**
 * Workaround the last day of month quirk in PHP's strtotime function.
 *
 * Adding +1 month to the last day of the month can yield unexpected results with strtotime().
 * For example:
 * - 30 Jan 2013 + 1 month = 3rd March 2013
 * - 28 Feb 2013 + 1 month = 28th March 2013
 *
 * What humans usually want is for the date to continue on the last day of the month.
 *
 * @param int $from_timestamp A Unix timestamp to add the months too.
 * @param int $months_to_add The number of months to add to the timestamp.
 */
function w2mb_addMonths($from_timestamp, $months_to_add) {
	$first_day_of_month = date('Y-m', $from_timestamp) . '-1';
	$days_in_next_month = date('t', strtotime("+ {$months_to_add} month", strtotime($first_day_of_month)));
	
	// Payment is on the last day of the month OR number of days in next billing month is less than the the day of this month (i.e. current billing date is 30th January, next billing date can't be 30th February)
	if (date('d m Y', $from_timestamp) === date('t m Y', $from_timestamp) || date('d', $from_timestamp) > $days_in_next_month) {
		for ($i = 1; $i <= $months_to_add; $i++) {
			$next_month = strtotime('+3 days', $from_timestamp); // Add 3 days to make sure we get to the next month, even when it's the 29th day of a month with 31 days
			$next_timestamp = $from_timestamp = strtotime(date('Y-m-t H:i:s', $next_month));
		}
	} else { // Safe to just add a month
		$next_timestamp = strtotime("+ {$months_to_add} month", $from_timestamp);
	}
	
	return $next_timestamp;
}

function w2mb_isResource($resource) {
	if (is_file(get_stylesheet_directory() . '/w2mb-plugin/resources/' . $resource)) {
		return get_stylesheet_directory_uri() . '/w2mb-plugin/resources/' . $resource;
	} elseif (is_file(W2MB_RESOURCES_PATH . $resource)) {
		return W2MB_RESOURCES_URL . $resource;
	}
	
	return false;
}

function w2mb_isCustomResourceDir($dir) {
	if (is_dir(get_stylesheet_directory() . '/w2mb-plugin/resources/' . $dir)) {
		return get_stylesheet_directory() . '/w2mb-plugin/resources/' . $dir;
	}
	
	return false;
}

function w2mb_getCustomResourceDirURL($dir) {
	if (is_dir(get_stylesheet_directory() . '/w2mb-plugin/resources/' . $dir)) {
		return get_stylesheet_directory_uri() . '/w2mb-plugin/resources/' . $dir;
	}
	
	return false;
}

/**
 * possible variants of templates and their paths:
 * - themes/theme/w2mb-plugin/templates/template-custom.tpl.php
 * - themes/theme/w2mb-plugin/templates/template.tpl.php
 * - plugins/w2mb/templates/template-custom.tpl.php
 * - plugins/w2mb/templates/template.tpl.php
 * 
 * templates in addons will be visible by such type of path:
 * - themes/theme/w2mb-plugin/templates/w2mb_fsubmit/template.tpl.php
 * 
 */
function w2mb_isTemplate($template) {
	$custom_template = str_replace('.tpl.php', '', $template) . '-custom.tpl.php';
	$templates = array(
			$custom_template,
			$template
	);

	foreach ($templates AS $template_to_check) {
		// check if it is exact path in $template
		if (is_file($template_to_check)) {
			return $template_to_check;
		} elseif (is_file(get_stylesheet_directory() . '/w2mb-plugin/templates/' . $template_to_check)) { // theme or child theme templates folder
			return get_stylesheet_directory() . '/w2mb-plugin/templates/' . $template_to_check;
		} elseif (is_file(W2MB_TEMPLATES_PATH . $template_to_check)) { // native plugin's templates folder
			return W2MB_TEMPLATES_PATH . $template_to_check;
		}
	}

	return false;
}

if (!function_exists('w2mb_renderTemplate')) {
	/**
	 * @param string|array $template
	 * @param array $args
	 * @param bool $return
	 * @return string
	 */
	function w2mb_renderTemplate($template, $args = array(), $return = false) {
		global $w2mb_instance;
	
		if ($args) {
			extract($args);
		}
		
		// filter hooks in all addons (w2mb_fsubmit.php, w2mb_ratings.php)
		$template = apply_filters('w2mb_render_template', $template, $args);
		
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			$template = $template_path . $template_file;
		}
		
		$template = w2mb_isTemplate($template);

		if ($template) {
			if ($return) {
				ob_start();
			}
		
			include($template);
			
			if ($return) {
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
	}
}

function w2mb_getCurrentListingInAdmin() {
	global $w2mb_instance;
	
	return $w2mb_instance->current_listing;
}

function w2mb_get_term_parents($id, $tax, $link = false, $return_array = false, $separator = '/', &$chain = array()) {
	$parent = get_term($id, $tax);
	if (is_wp_error($parent) || !$parent) {
		if ($return_array) {
			return array();
		} else { 
			return '';
		}
	}

	$name = $parent->name;
	
	if ($parent->parent && ($parent->parent != $parent->term_id)) {
		w2mb_get_term_parents($parent->parent, $tax, $link, $return_array, $separator, $chain);
	}

	$url = get_term_link($parent->slug, $tax);
	if ($link && !is_wp_error($url)) {
		$chain[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . $url . '" title="' . esc_attr(sprintf(esc_html__('View all listings in %s', 'W2MB'), $name)) . '"><span itemprop="name">' . $name . '</span></a></li>';
	} else {
		$chain[] = $name;
	}
	
	if ($return_array) {
		return $chain;
	} else {
		return implode($separator, $chain);
	}
}

function w2mb_get_term_parents_slugs($id, $tax, &$chain = array()) {
	$parent = get_term($id, $tax);
	if (is_wp_error($parent) || !$parent) {
		return '';
	}

	$slug = $parent->slug;
	
	if ($parent->parent && ($parent->parent != $parent->term_id)) {
		w2mb_get_term_parents_slugs($parent->parent, $tax, $chain);
	}

	$chain[] = $slug;

	return $chain;
}

function w2mb_get_term_parents_ids($id, $tax, &$chain = array()) {
	$parent = get_term($id, $tax);
	if (is_wp_error($parent) || !$parent) {
		return '';
	}

	$id = $parent->term_id;
	
	if ($parent->parent && ($parent->parent != $parent->term_id)) {
		w2mb_get_term_parents_ids($parent->parent, $tax, $chain);
	}

	$chain[] = $id;

	return $chain;
}

function w2mb_checkQuickList($is_listing_id = null)
{
	if (isset($_COOKIE['favourites']))
		$favourites = explode('*', $_COOKIE['favourites']);
	else
		$favourites = array();
	$favourites = array_values(array_filter($favourites));

	if ($is_listing_id)
		if (in_array($is_listing_id, $favourites))
			return true;
		else 
			return false;

	$favourites_array = array();
	foreach ($favourites AS $listing_id)
		if (is_numeric($listing_id))
		$favourites_array[] = $listing_id;
	return $favourites_array;
}

function w2mb_getDatePickerFormat() {
	$wp_date_format = get_option('date_format');
	return str_replace(
			array('S',  'd', 'j',  'l',  'm', 'n',  'F',  'Y'),
			array('',  'dd', 'd', 'DD', 'mm', 'm', 'MM', 'yy'),
		$wp_date_format);
}

function w2mb_getDatePickerLangFile($locale) {
	if ($locale) {
		$_locale = explode('-', str_replace('_', '-', $locale));
		$lang_code = array_shift($_locale);
		if (is_file(W2MB_RESOURCES_PATH . 'js/i18n/datepicker-'.$locale.'.js'))
			return W2MB_RESOURCES_URL . 'js/i18n/datepicker-'.$locale.'.js';
		elseif (is_file(W2MB_RESOURCES_PATH . 'js/i18n/datepicker-'.$lang_code.'.js'))
			return W2MB_RESOURCES_URL . 'js/i18n/datepicker-'.$lang_code.'.js';
	}
}

function w2mb_getDatePickerLangCode($locale) {
	if ($locale) {
		$_locale = explode('-', str_replace('_', '-', $locale));
		$lang_code = array_shift($_locale);
		if (is_file(W2MB_RESOURCES_PATH . 'js/i18n/datepicker-'.$locale.'.js'))
			return $locale;
		elseif (is_file(W2MB_RESOURCES_PATH . 'js/i18n/datepicker-'.$lang_code.'.js'))
			return $lang_code;
	}
}

function w2mb_generateRandomVal($val = null) {
	if (!$val)
		return rand(1, 10000);
	else
		return $val;
}

/**
 * Fetch the IP Address
 *
 * @return	string
 */
function w2mb_ip_address()
{
	if (isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['HTTP_CLIENT_IP']))
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	elseif (isset($_SERVER['REMOTE_ADDR']))
		$ip_address = $_SERVER['REMOTE_ADDR'];
	elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else
		return false;

	if (strstr($ip_address, ',')) {
		$x = explode(',', $ip_address);
		$ip_address = trim(end($x));
	}

	$validation = new w2mb_form_validation();
	if (!$validation->valid_ip($ip_address))
		return false;

	return $ip_address;
}

function w2mb_crop_content($post_id, $limit = 35, $strip_html = true) {
	if (has_excerpt($post_id)) {
		$raw_content = apply_filters('the_excerpt', get_the_excerpt($post_id));
	} elseif (get_option('w2mb_cropped_content_as_excerpt') && get_post($post_id)->post_content !== '') {
		$raw_content = apply_filters('the_content', get_post($post_id)->post_content);
	} else {
		return ;
	}
	
	$readmore_text = esc_html__('...', 'W2MB');

	$raw_content = str_replace(']]>', ']]&gt;', $raw_content);
	if ($strip_html) {
		$raw_content = strip_tags($raw_content);
		$pattern = get_shortcode_regex();
		// Remove shortcodes from excerpt
		$raw_content = preg_replace_callback("/$pattern/s", 'w2mb_remove_shortcodes', $raw_content);
	}

	if (!$limit) {
		return $raw_content;
	}

	$content = explode(' ', $raw_content, $limit);
	if (count($content) >= $limit) {
		array_pop($content);
		$content = implode(" ", $content) . $readmore_text;
	} else {
		$content = $raw_content;
	}

	return $content;
}

// Remove shortcodes from excerpt
function w2mb_remove_shortcodes($m) {
	if (function_exists('su_cmpt') && su_cmpt() !== false)
	if ($m[2] == su_cmpt() . 'dropcap' || $m[2] == su_cmpt() . 'highlight' || $m[2] == su_cmpt() . 'tooltip')
		return $m[0];

	// allow [[foo]] syntax for escaping a tag
	if ($m[1] == '[' && $m[6] == ']')
		return substr($m[0], 1, -1);

	return $m[1] . $m[6];
}

function w2mb_is_anyone_in_taxonomy($tax) {
	return count(get_categories(array('taxonomy' => $tax, 'hide_empty' => false, 'parent' => 0, 'number' => 1)));
}

function w2mb_comments_open() {
	if (get_option('w2mb_listings_comments_mode') == 'enabled' || (get_option('w2mb_listings_comments_mode') == 'wp_settings' && comments_open()))
		return true;
	else 
		return false;
}

function w2mb_get_term_by_path($term_path, $full_match = true, $output = OBJECT) {
	$term_path = rawurlencode( urldecode( $term_path ) );
	$term_path = str_replace( '%2F', '/', $term_path );
	$term_path = str_replace( '%20', ' ', $term_path );

	global $wp_rewrite;
	if ($wp_rewrite->using_permalinks()) {
		$term_paths = '/' . trim( $term_path, '/' );
		$leaf_path  = sanitize_title( basename( $term_paths ) );
		$term_paths = explode( '/', $term_paths );
		$full_path = '';
		foreach ( (array) $term_paths as $pathdir )
			$full_path .= ( $pathdir != '' ? '/' : '' ) . sanitize_title( $pathdir );
	
		$terms = array();
		if ($term = get_term_by('slug', $leaf_path, W2MB_CATEGORIES_TAX))
			$terms[] = $term;
		if ($term = get_term_by('slug', $leaf_path, W2MB_LOCATIONS_TAX))
			$terms[] = $term;
		if ($term = get_term_by('slug', $leaf_path, W2MB_TAGS_TAX))
			$terms[] = $term;
	
		if ( empty( $terms ) )
			return null;
	
		foreach ( $terms as $term ) {
			$path = '/' . $leaf_path;
			$curterm = $term;
			while ( ( $curterm->parent != 0 ) && ( $curterm->parent != $curterm->term_id ) ) {
				$curterm = get_term( $curterm->parent, $term->taxonomy );
				if ( is_wp_error( $curterm ) )
					return $curterm;
				$path = '/' . $curterm->slug . $path;
			}

			if ( $path == $full_path ) {
				$term = get_term( $term->term_id, $term->taxonomy, $output );
				_make_cat_compat( $term );
				return $term;
			}
		}
	
		// If full matching is not required, return the first cat that matches the leaf.
		if ( ! $full_match ) {
			$term = reset( $terms );
			$term = get_term( $term->term_id, $term->taxonomy, $output );
			_make_cat_compat( $term );
			return $term;
		}
	} else {
		if ($term = get_term_by('slug', $term_path, W2MB_CATEGORIES_TAX))
			return $term;
		if ($term = get_term_by('slug', $term_path, W2MB_LOCATIONS_TAX))
			return $term;
		if ($term = get_term_by('slug', $term_path, W2MB_TAGS_TAX))
			return $term;
	}

	return null;
}

function w2mb_get_fa_icons_names() {
	$icons[] = 'w2mb-fa-adjust';
	$icons[] = 'w2mb-fa-adn';
	$icons[] = 'w2mb-fa-align-center';
	$icons[] = 'w2mb-fa-align-justify';
	$icons[] = 'w2mb-fa-align-left';
	$icons[] = 'w2mb-fa-align-right';
	$icons[] = 'w2mb-fa-ambulance';
	$icons[] = 'w2mb-fa-anchor';
	$icons[] = 'w2mb-fa-android';
	$icons[] = 'w2mb-fa-angellist';
	$icons[] = 'w2mb-fa-angle-double-down';
	$icons[] = 'w2mb-fa-angle-double-left';
	$icons[] = 'w2mb-fa-angle-double-right';
	$icons[] = 'w2mb-fa-angle-double-up';
	$icons[] = 'w2mb-fa-angle-down';
	$icons[] = 'w2mb-fa-angle-left';
	$icons[] = 'w2mb-fa-angle-right';
	$icons[] = 'w2mb-fa-angle-up';
	$icons[] = 'w2mb-fa-apple';
	$icons[] = 'w2mb-fa-archive';
	$icons[] = 'w2mb-fa-area-chart';
	$icons[] = 'w2mb-fa-arrow-circle-down';
	$icons[] = 'w2mb-fa-arrow-circle-left';
	$icons[] = 'w2mb-fa-arrow-circle-o-down';
	$icons[] = 'w2mb-fa-arrow-circle-o-left';
	$icons[] = 'w2mb-fa-arrow-circle-o-right';
	$icons[] = 'w2mb-fa-arrow-circle-o-up';
	$icons[] = 'w2mb-fa-arrow-circle-right';
	$icons[] = 'w2mb-fa-arrow-circle-up';
	$icons[] = 'w2mb-fa-arrow-down';
	$icons[] = 'w2mb-fa-arrow-left';
	$icons[] = 'w2mb-fa-arrow-right';
	$icons[] = 'w2mb-fa-arrow-up';
	$icons[] = 'w2mb-fa-arrows';
	$icons[] = 'w2mb-fa-arrows-alt';
	$icons[] = 'w2mb-fa-arrows-h';
	$icons[] = 'w2mb-fa-arrows-v';
	$icons[] = 'w2mb-fa-asterisk';
	$icons[] = 'w2mb-fa-at';
	$icons[] = 'w2mb-fa-automobile';
	$icons[] = 'w2mb-fa-backward';
	$icons[] = 'w2mb-fa-ban';
	$icons[] = 'w2mb-fa-bank';
	$icons[] = 'w2mb-fa-bar-chart';
	$icons[] = 'w2mb-fa-bar-chart-o';
	$icons[] = 'w2mb-fa-barcode';
	$icons[] = 'w2mb-fa-bars';
	$icons[] = 'w2mb-fa-bed';
	$icons[] = 'w2mb-fa-beer';
	$icons[] = 'w2mb-fa-behance';
	$icons[] = 'w2mb-fa-behance-square';
	$icons[] = 'w2mb-fa-bell';
	$icons[] = 'w2mb-fa-bell-o';
	$icons[] = 'w2mb-fa-bell-slash';
	$icons[] = 'w2mb-fa-bell-slash-o';
	$icons[] = 'w2mb-fa-bicycle';
	$icons[] = 'w2mb-fa-binoculars';
	$icons[] = 'w2mb-fa-birthday-cake';
	$icons[] = 'w2mb-fa-bitbucket';
	$icons[] = 'w2mb-fa-bitbucket-square';
	$icons[] = 'w2mb-fa-bitcoin';
	$icons[] = 'w2mb-fa-bold';
	$icons[] = 'w2mb-fa-bolt';
	$icons[] = 'w2mb-fa-bomb';
	$icons[] = 'w2mb-fa-book';
	$icons[] = 'w2mb-fa-bookmark';
	$icons[] = 'w2mb-fa-bookmark-o';
	$icons[] = 'w2mb-fa-briefcase';
	$icons[] = 'w2mb-fa-btc';
	$icons[] = 'w2mb-fa-bug';
	$icons[] = 'w2mb-fa-building';
	$icons[] = 'w2mb-fa-building-o';
	$icons[] = 'w2mb-fa-bullhorn';
	$icons[] = 'w2mb-fa-bullseye';
	$icons[] = 'w2mb-fa-bus';
	$icons[] = 'w2mb-fa-buysellads';
	$icons[] = 'w2mb-fa-cab';
	$icons[] = 'w2mb-fa-calculator';
	$icons[] = 'w2mb-fa-calendar';
	$icons[] = 'w2mb-fa-calendar-o';
	$icons[] = 'w2mb-fa-camera';
	$icons[] = 'w2mb-fa-camera-retro';
	$icons[] = 'w2mb-fa-car';
	$icons[] = 'w2mb-fa-caret-down';
	$icons[] = 'w2mb-fa-caret-left';
	$icons[] = 'w2mb-fa-caret-right';
	$icons[] = 'w2mb-fa-caret-square-o-down';
	$icons[] = 'w2mb-fa-caret-square-o-left';
	$icons[] = 'w2mb-fa-caret-square-o-right';
	$icons[] = 'w2mb-fa-caret-square-o-up';
	$icons[] = 'w2mb-fa-caret-up';
	$icons[] = 'w2mb-fa-cart-arrow-down';
	$icons[] = 'w2mb-fa-cart-plus';
	$icons[] = 'w2mb-fa-cc';
	$icons[] = 'w2mb-fa-cc-amex';
	$icons[] = 'w2mb-fa-cc-discover';
	$icons[] = 'w2mb-fa-cc-mastercard';
	$icons[] = 'w2mb-fa-cc-paypal';
	$icons[] = 'w2mb-fa-cc-stripe';
	$icons[] = 'w2mb-fa-cc-visa';
	$icons[] = 'w2mb-fa-certificate';
	$icons[] = 'w2mb-fa-chain';
	$icons[] = 'w2mb-fa-chain-broken';
	$icons[] = 'w2mb-fa-check';
	$icons[] = 'w2mb-fa-check-circle';
	$icons[] = 'w2mb-fa-check-circle-o';
	$icons[] = 'w2mb-fa-check-square';
	$icons[] = 'w2mb-fa-check-square-o';
	$icons[] = 'w2mb-fa-chevron-circle-down';
	$icons[] = 'w2mb-fa-chevron-circle-left';
	$icons[] = 'w2mb-fa-chevron-circle-right';
	$icons[] = 'w2mb-fa-chevron-circle-up';
	$icons[] = 'w2mb-fa-chevron-down';
	$icons[] = 'w2mb-fa-chevron-left';
	$icons[] = 'w2mb-fa-chevron-right';
	$icons[] = 'w2mb-fa-chevron-up';
	$icons[] = 'w2mb-fa-child';
	$icons[] = 'w2mb-fa-circle';
	$icons[] = 'w2mb-fa-circle-o';
	$icons[] = 'w2mb-fa-circle-o-notch';
	$icons[] = 'w2mb-fa-circle-thin';
	$icons[] = 'w2mb-fa-clipboard';
	$icons[] = 'w2mb-fa-clock-o';
	$icons[] = 'w2mb-fa-close';
	$icons[] = 'w2mb-fa-cloud';
	$icons[] = 'w2mb-fa-cloud-download';
	$icons[] = 'w2mb-fa-cloud-upload';
	$icons[] = 'w2mb-fa-cny';
	$icons[] = 'w2mb-fa-code';
	$icons[] = 'w2mb-fa-code-fork';
	$icons[] = 'w2mb-fa-codepen';
	$icons[] = 'w2mb-fa-coffee';
	$icons[] = 'w2mb-fa-cog';
	$icons[] = 'w2mb-fa-cogs';
	$icons[] = 'w2mb-fa-columns';
	$icons[] = 'w2mb-fa-comment';
	$icons[] = 'w2mb-fa-comment-o';
	$icons[] = 'w2mb-fa-comments';
	$icons[] = 'w2mb-fa-comments-o';
	$icons[] = 'w2mb-fa-compass';
	$icons[] = 'w2mb-fa-compress';
	$icons[] = 'w2mb-fa-connectdevelop';
	$icons[] = 'w2mb-fa-copy';
	$icons[] = 'w2mb-fa-copyright';
	$icons[] = 'w2mb-fa-credit-card';
	$icons[] = 'w2mb-fa-crop';
	$icons[] = 'w2mb-fa-crosshairs';
	$icons[] = 'w2mb-fa-css3';
	$icons[] = 'w2mb-fa-cube';
	$icons[] = 'w2mb-fa-cubes';
	$icons[] = 'w2mb-fa-cut';
	$icons[] = 'w2mb-fa-cutlery';
	$icons[] = 'w2mb-fa-dashboard';
	$icons[] = 'w2mb-fa-dashcube';
	$icons[] = 'w2mb-fa-database';
	$icons[] = 'w2mb-fa-dedent';
	$icons[] = 'w2mb-fa-delicious';
	$icons[] = 'w2mb-fa-desktop';
	$icons[] = 'w2mb-fa-deviantart';
	$icons[] = 'w2mb-fa-diamond';
	$icons[] = 'w2mb-fa-digg';
	$icons[] = 'w2mb-fa-dollar';
	$icons[] = 'w2mb-fa-dot-circle-o';
	$icons[] = 'w2mb-fa-download';
	$icons[] = 'w2mb-fa-dribbble';
	$icons[] = 'w2mb-fa-dropbox';
	$icons[] = 'w2mb-fa-drupal';
	$icons[] = 'w2mb-fa-edit';
	$icons[] = 'w2mb-fa-eject';
	$icons[] = 'w2mb-fa-ellipsis-h';
	$icons[] = 'w2mb-fa-ellipsis-v';
	$icons[] = 'w2mb-fa-empire';
	$icons[] = 'w2mb-fa-envelope';
	$icons[] = 'w2mb-fa-envelope-o';
	$icons[] = 'w2mb-fa-envelope-square';
	$icons[] = 'w2mb-fa-eraser';
	$icons[] = 'w2mb-fa-eur';
	$icons[] = 'w2mb-fa-euro';
	$icons[] = 'w2mb-fa-exchange';
	$icons[] = 'w2mb-fa-exclamation';
	$icons[] = 'w2mb-fa-exclamation-circle';
	$icons[] = 'w2mb-fa-exclamation-triangle';
	$icons[] = 'w2mb-fa-expand';
	$icons[] = 'w2mb-fa-external-link';
	$icons[] = 'w2mb-fa-external-link-square';
	$icons[] = 'w2mb-fa-eye';
	$icons[] = 'w2mb-fa-eye-slash';
	$icons[] = 'w2mb-fa-eyedropper';
	$icons[] = 'w2mb-fa-facebook';
	$icons[] = 'w2mb-fa-facebook-f';
	$icons[] = 'w2mb-fa-facebook-official';
	$icons[] = 'w2mb-fa-facebook-square';
	$icons[] = 'w2mb-fa-fast-backward';
	$icons[] = 'w2mb-fa-fast-forward';
	$icons[] = 'w2mb-fa-fax';
	$icons[] = 'w2mb-fa-female';
	$icons[] = 'w2mb-fa-fighter-jet';
	$icons[] = 'w2mb-fa-file';
	$icons[] = 'w2mb-fa-file-archive-o';
	$icons[] = 'w2mb-fa-file-audio-o';
	$icons[] = 'w2mb-fa-file-code-o';
	$icons[] = 'w2mb-fa-file-excel-o';
	$icons[] = 'w2mb-fa-file-image-o';
	$icons[] = 'w2mb-fa-file-movie-o';
	$icons[] = 'w2mb-fa-file-o';
	$icons[] = 'w2mb-fa-file-pdf-o';
	$icons[] = 'w2mb-fa-file-photo-o';
	$icons[] = 'w2mb-fa-file-picture-o';
	$icons[] = 'w2mb-fa-file-powerpoint-o';
	$icons[] = 'w2mb-fa-file-sound-o';
	$icons[] = 'w2mb-fa-file-text';
	$icons[] = 'w2mb-fa-file-text-o';
	$icons[] = 'w2mb-fa-file-video-o';
	$icons[] = 'w2mb-fa-file-word-o';
	$icons[] = 'w2mb-fa-file-zip-o';
	$icons[] = 'w2mb-fa-files-o';
	$icons[] = 'w2mb-fa-film';
	$icons[] = 'w2mb-fa-filter';
	$icons[] = 'w2mb-fa-fire';
	$icons[] = 'w2mb-fa-fire-extinguisher';
	$icons[] = 'w2mb-fa-flag';
	$icons[] = 'w2mb-fa-flag-checkered';
	$icons[] = 'w2mb-fa-flag-o';
	$icons[] = 'w2mb-fa-flash';
	$icons[] = 'w2mb-fa-flask';
	$icons[] = 'w2mb-fa-flickr';
	$icons[] = 'w2mb-fa-floppy-o';
	$icons[] = 'w2mb-fa-folder';
	$icons[] = 'w2mb-fa-folder-o';
	$icons[] = 'w2mb-fa-folder-open';
	$icons[] = 'w2mb-fa-folder-open-o';
	$icons[] = 'w2mb-fa-font';
	$icons[] = 'w2mb-fa-forumbee';
	$icons[] = 'w2mb-fa-forward';
	$icons[] = 'w2mb-fa-foursquare';
	$icons[] = 'w2mb-fa-frown-o';
	$icons[] = 'w2mb-fa-futbol-o';
	$icons[] = 'w2mb-fa-gamepad';
	$icons[] = 'w2mb-fa-gavel';
	$icons[] = 'w2mb-fa-gbp';
	$icons[] = 'w2mb-fa-ge';
	$icons[] = 'w2mb-fa-gear';
	$icons[] = 'w2mb-fa-gears';
	$icons[] = 'w2mb-fa-genderless';
	$icons[] = 'w2mb-fa-gift';
	$icons[] = 'w2mb-fa-git';
	$icons[] = 'w2mb-fa-git-square';
	$icons[] = 'w2mb-fa-github';
	$icons[] = 'w2mb-fa-github-alt';
	$icons[] = 'w2mb-fa-github-square';
	$icons[] = 'w2mb-fa-gittip';
	$icons[] = 'w2mb-fa-glass';
	$icons[] = 'w2mb-fa-globe';
	$icons[] = 'w2mb-fa-google';
	$icons[] = 'w2mb-fa-google-plus';
	$icons[] = 'w2mb-fa-google-plus-square';
	$icons[] = 'w2mb-fa-google-wallet';
	$icons[] = 'w2mb-fa-graduation-cap';
	$icons[] = 'w2mb-fa-gratipay';
	$icons[] = 'w2mb-fa-group';
	$icons[] = 'w2mb-fa-h-square';
	$icons[] = 'w2mb-fa-hacker-news';
	$icons[] = 'w2mb-fa-hand-o-down';
	$icons[] = 'w2mb-fa-hand-o-left';
	$icons[] = 'w2mb-fa-hand-o-right';
	$icons[] = 'w2mb-fa-hand-o-up';
	$icons[] = 'w2mb-fa-hdd-o';
	$icons[] = 'w2mb-fa-header';
	$icons[] = 'w2mb-fa-headphones';
	$icons[] = 'w2mb-fa-heart';
	$icons[] = 'w2mb-fa-heart-o';
	$icons[] = 'w2mb-fa-heartbeat';
	$icons[] = 'w2mb-fa-history';
	$icons[] = 'w2mb-fa-home';
	$icons[] = 'w2mb-fa-hospital-o';
	$icons[] = 'w2mb-fa-hotel';
	$icons[] = 'w2mb-fa-html5';
	$icons[] = 'w2mb-fa-ils';
	$icons[] = 'w2mb-fa-image';
	$icons[] = 'w2mb-fa-inbox';
	$icons[] = 'w2mb-fa-indent';
	$icons[] = 'w2mb-fa-info';
	$icons[] = 'w2mb-fa-info-circle';
	$icons[] = 'w2mb-fa-inr';
	$icons[] = 'w2mb-fa-instagram';
	$icons[] = 'w2mb-fa-institution';
	$icons[] = 'w2mb-fa-ioxhost';
	$icons[] = 'w2mb-fa-italic';
	$icons[] = 'w2mb-fa-joomla';
	$icons[] = 'w2mb-fa-jpy';
	$icons[] = 'w2mb-fa-jsfiddle';
	$icons[] = 'w2mb-fa-key';
	$icons[] = 'w2mb-fa-keyboard-o';
	$icons[] = 'w2mb-fa-krw';
	$icons[] = 'w2mb-fa-language';
	$icons[] = 'w2mb-fa-laptop';
	$icons[] = 'w2mb-fa-lastfm';
	$icons[] = 'w2mb-fa-lastfm-square';
	$icons[] = 'w2mb-fa-leaf';
	$icons[] = 'w2mb-fa-leanpub';
	$icons[] = 'w2mb-fa-legal';
	$icons[] = 'w2mb-fa-lemon-o';
	$icons[] = 'w2mb-fa-level-down';
	$icons[] = 'w2mb-fa-level-up';
	$icons[] = 'w2mb-fa-life-bouy';
	$icons[] = 'w2mb-fa-life-ring';
	$icons[] = 'w2mb-fa-life-saver';
	$icons[] = 'w2mb-fa-lightbulb-o';
	$icons[] = 'w2mb-fa-line-chart';
	$icons[] = 'w2mb-fa-link';
	$icons[] = 'w2mb-fa-linkedin';
	$icons[] = 'w2mb-fa-linkedin-square';
	$icons[] = 'w2mb-fa-linux';
	$icons[] = 'w2mb-fa-list';
	$icons[] = 'w2mb-fa-list-alt';
	$icons[] = 'w2mb-fa-list-ol';
	$icons[] = 'w2mb-fa-list-ul';
	$icons[] = 'w2mb-fa-location-arrow';
	$icons[] = 'w2mb-fa-lock';
	$icons[] = 'w2mb-fa-long-arrow-down';
	$icons[] = 'w2mb-fa-long-arrow-left';
	$icons[] = 'w2mb-fa-long-arrow-right';
	$icons[] = 'w2mb-fa-long-arrow-up';
	$icons[] = 'w2mb-fa-magic';
	$icons[] = 'w2mb-fa-magnet';
	$icons[] = 'w2mb-fa-mail-forward';
	$icons[] = 'w2mb-fa-mail-reply';
	$icons[] = 'w2mb-fa-mail-reply-all';
	$icons[] = 'w2mb-fa-male';
	$icons[] = 'w2mb-fa-map-marker';
	$icons[] = 'w2mb-fa-mars';
	$icons[] = 'w2mb-fa-mars-double';
	$icons[] = 'w2mb-fa-mars-stroke';
	$icons[] = 'w2mb-fa-mars-stroke-h';
	$icons[] = 'w2mb-fa-mars-stroke-v';
	$icons[] = 'w2mb-fa-maxcdn';
	$icons[] = 'w2mb-fa-meanpath';
	$icons[] = 'w2mb-fa-medium';
	$icons[] = 'w2mb-fa-medkit';
	$icons[] = 'w2mb-fa-meh-o';
	$icons[] = 'w2mb-fa-mercury';
	$icons[] = 'w2mb-fa-microphone';
	$icons[] = 'w2mb-fa-microphone-slash';
	$icons[] = 'w2mb-fa-minus';
	$icons[] = 'w2mb-fa-minus-circle';
	$icons[] = 'w2mb-fa-minus-square';
	$icons[] = 'w2mb-fa-minus-square-o';
	$icons[] = 'w2mb-fa-mobile';
	$icons[] = 'w2mb-fa-mobile-phone';
	$icons[] = 'w2mb-fa-money';
	$icons[] = 'w2mb-fa-moon-o';
	$icons[] = 'w2mb-fa-mortar-board';
	$icons[] = 'w2mb-fa-motorcycle';
	$icons[] = 'w2mb-fa-music';
	$icons[] = 'w2mb-fa-navicon';
	$icons[] = 'w2mb-fa-neuter';
	$icons[] = 'w2mb-fa-newspaper-o';
	$icons[] = 'w2mb-fa-openid';
	$icons[] = 'w2mb-fa-outdent';
	$icons[] = 'w2mb-fa-pagelines';
	$icons[] = 'w2mb-fa-paint-brush';
	$icons[] = 'w2mb-fa-paper-plane';
	$icons[] = 'w2mb-fa-paper-plane-o';
	$icons[] = 'w2mb-fa-paperclip';
	$icons[] = 'w2mb-fa-paragraph';
	$icons[] = 'w2mb-fa-paste';
	$icons[] = 'w2mb-fa-pause';
	$icons[] = 'w2mb-fa-paw';
	$icons[] = 'w2mb-fa-paypal';
	$icons[] = 'w2mb-fa-pencil';
	$icons[] = 'w2mb-fa-pencil-square';
	$icons[] = 'w2mb-fa-pencil-square-o';
	$icons[] = 'w2mb-fa-phone';
	$icons[] = 'w2mb-fa-phone-square';
	$icons[] = 'w2mb-fa-photo';
	$icons[] = 'w2mb-fa-picture-o';
	$icons[] = 'w2mb-fa-pie-chart';
	$icons[] = 'w2mb-fa-pied-piper';
	$icons[] = 'w2mb-fa-pied-piper-alt';
	$icons[] = 'w2mb-fa-pinterest';
	$icons[] = 'w2mb-fa-pinterest-p';
	$icons[] = 'w2mb-fa-pinterest-square';
	$icons[] = 'w2mb-fa-plane';
	$icons[] = 'w2mb-fa-play';
	$icons[] = 'w2mb-fa-play-circle';
	$icons[] = 'w2mb-fa-play-circle-o';
	$icons[] = 'w2mb-fa-plug';
	$icons[] = 'w2mb-fa-plus';
	$icons[] = 'w2mb-fa-plus-circle';
	$icons[] = 'w2mb-fa-plus-square';
	$icons[] = 'w2mb-fa-plus-square-o';
	$icons[] = 'w2mb-fa-power-off';
	$icons[] = 'w2mb-fa-print';
	$icons[] = 'w2mb-fa-puzzle-piece';
	$icons[] = 'w2mb-fa-qq';
	$icons[] = 'w2mb-fa-qrcode';
	$icons[] = 'w2mb-fa-question';
	$icons[] = 'w2mb-fa-question-circle';
	$icons[] = 'w2mb-fa-quote-left';
	$icons[] = 'w2mb-fa-quote-right';
	$icons[] = 'w2mb-fa-ra';
	$icons[] = 'w2mb-fa-random';
	$icons[] = 'w2mb-fa-rebel';
	$icons[] = 'w2mb-fa-recycle';
	$icons[] = 'w2mb-fa-reddit';
	$icons[] = 'w2mb-fa-reddit-square';
	$icons[] = 'w2mb-fa-refresh';
	$icons[] = 'w2mb-fa-remove';
	$icons[] = 'w2mb-fa-renren';
	$icons[] = 'w2mb-fa-reorder';
	$icons[] = 'w2mb-fa-repeat';
	$icons[] = 'w2mb-fa-reply';
	$icons[] = 'w2mb-fa-reply-all';
	$icons[] = 'w2mb-fa-retweet';
	$icons[] = 'w2mb-fa-rmb';
	$icons[] = 'w2mb-fa-road';
	$icons[] = 'w2mb-fa-rocket';
	$icons[] = 'w2mb-fa-rotate-left';
	$icons[] = 'w2mb-fa-rotate-right';
	$icons[] = 'w2mb-fa-rouble';
	$icons[] = 'w2mb-fa-rss';
	$icons[] = 'w2mb-fa-rss-square';
	$icons[] = 'w2mb-fa-rub';
	$icons[] = 'w2mb-fa-ruble';
	$icons[] = 'w2mb-fa-rupee';
	$icons[] = 'w2mb-fa-save';
	$icons[] = 'w2mb-fa-scissors';
	$icons[] = 'w2mb-fa-search';
	$icons[] = 'w2mb-fa-search-minus';
	$icons[] = 'w2mb-fa-search-plus';
	$icons[] = 'w2mb-fa-sellsy';
	$icons[] = 'w2mb-fa-send';
	$icons[] = 'w2mb-fa-send-o';
	$icons[] = 'w2mb-fa-server';
	$icons[] = 'w2mb-fa-share';
	$icons[] = 'w2mb-fa-share-alt';
	$icons[] = 'w2mb-fa-share-alt-square';
	$icons[] = 'w2mb-fa-share-square';
	$icons[] = 'w2mb-fa-share-square-o';
	$icons[] = 'w2mb-fa-shekel';
	$icons[] = 'w2mb-fa-sheqel';
	$icons[] = 'w2mb-fa-shield';
	$icons[] = 'w2mb-fa-ship';
	$icons[] = 'w2mb-fa-shirtsinbulk';
	$icons[] = 'w2mb-fa-shopping-cart';
	$icons[] = 'w2mb-fa-sign-out';
	$icons[] = 'w2mb-fa-signal';
	$icons[] = 'w2mb-fa-simplybuilt';
	$icons[] = 'w2mb-fa-sitemap';
	$icons[] = 'w2mb-fa-skyatlas';
	$icons[] = 'w2mb-fa-skype';
	$icons[] = 'w2mb-fa-slack';
	$icons[] = 'w2mb-fa-sliders';
	$icons[] = 'w2mb-fa-slideshare';
	$icons[] = 'w2mb-fa-smile-o';
	$icons[] = 'w2mb-fa-soccer-ball-o';
	$icons[] = 'w2mb-fa-sort';
	$icons[] = 'w2mb-fa-sort-alpha-asc';
	$icons[] = 'w2mb-fa-sort-alpha-desc';
	$icons[] = 'w2mb-fa-sort-amount-asc';
	$icons[] = 'w2mb-fa-sort-amount-desc';
	$icons[] = 'w2mb-fa-sort-asc';
	$icons[] = 'w2mb-fa-sort-desc';
	$icons[] = 'w2mb-fa-sort-down';
	$icons[] = 'w2mb-fa-sort-numeric-asc';
	$icons[] = 'w2mb-fa-sort-numeric-desc';
	$icons[] = 'w2mb-fa-sort-up';
	$icons[] = 'w2mb-fa-soundcloud';
	$icons[] = 'w2mb-fa-space-shuttle';
	$icons[] = 'w2mb-fa-spinner';
	$icons[] = 'w2mb-fa-spoon';
	$icons[] = 'w2mb-fa-spotify';
	$icons[] = 'w2mb-fa-square';
	$icons[] = 'w2mb-fa-square-o';
	$icons[] = 'w2mb-fa-stack-exchange';
	$icons[] = 'w2mb-fa-stack-overflow';
	$icons[] = 'w2mb-fa-star';
	$icons[] = 'w2mb-fa-star-half';
	$icons[] = 'w2mb-fa-star-half-empty';
	$icons[] = 'w2mb-fa-star-half-full';
	$icons[] = 'w2mb-fa-star-half-o';
	$icons[] = 'w2mb-fa-star-o';
	$icons[] = 'w2mb-fa-steam';
	$icons[] = 'w2mb-fa-steam-square';
	$icons[] = 'w2mb-fa-step-backward';
	$icons[] = 'w2mb-fa-step-forward';
	$icons[] = 'w2mb-fa-stethoscope';
	$icons[] = 'w2mb-fa-stop';
	$icons[] = 'w2mb-fa-street-view';
	$icons[] = 'w2mb-fa-strikethrough';
	$icons[] = 'w2mb-fa-stumbleupon';
	$icons[] = 'w2mb-fa-stumbleupon-circle';
	$icons[] = 'w2mb-fa-subscript';
	$icons[] = 'w2mb-fa-subway';
	$icons[] = 'w2mb-fa-suitcase';
	$icons[] = 'w2mb-fa-sun-o';
	$icons[] = 'w2mb-fa-superscript';
	$icons[] = 'w2mb-fa-support';
	$icons[] = 'w2mb-fa-table';
	$icons[] = 'w2mb-fa-tablet';
	$icons[] = 'w2mb-fa-tachometer';
	$icons[] = 'w2mb-fa-tag';
	$icons[] = 'w2mb-fa-tags';
	$icons[] = 'w2mb-fa-tasks';
	$icons[] = 'w2mb-fa-taxi';
	$icons[] = 'w2mb-fa-tencent-weibo';
	$icons[] = 'w2mb-fa-terminal';
	$icons[] = 'w2mb-fa-text-height';
	$icons[] = 'w2mb-fa-text-width';
	$icons[] = 'w2mb-fa-th';
	$icons[] = 'w2mb-fa-th-large';
	$icons[] = 'w2mb-fa-th-list';
	$icons[] = 'w2mb-fa-thumb-tack';
	$icons[] = 'w2mb-fa-thumbs-down';
	$icons[] = 'w2mb-fa-thumbs-o-down';
	$icons[] = 'w2mb-fa-thumbs-o-up';
	$icons[] = 'w2mb-fa-thumbs-up';
	$icons[] = 'w2mb-fa-ticket';
	$icons[] = 'w2mb-fa-times';
	$icons[] = 'w2mb-fa-times-circle';
	$icons[] = 'w2mb-fa-times-circle-o';
	$icons[] = 'w2mb-fa-tint';
	$icons[] = 'w2mb-fa-toggle-down';
	$icons[] = 'w2mb-fa-toggle-left';
	$icons[] = 'w2mb-fa-toggle-off';
	$icons[] = 'w2mb-fa-toggle-on';
	$icons[] = 'w2mb-fa-toggle-right';
	$icons[] = 'w2mb-fa-toggle-up';
	$icons[] = 'w2mb-fa-train';
	$icons[] = 'w2mb-fa-transgender';
	$icons[] = 'w2mb-fa-transgender-alt';
	$icons[] = 'w2mb-fa-trash';
	$icons[] = 'w2mb-fa-trash-o';
	$icons[] = 'w2mb-fa-tree';
	$icons[] = 'w2mb-fa-trello';
	$icons[] = 'w2mb-fa-trophy';
	$icons[] = 'w2mb-fa-truck';
	$icons[] = 'w2mb-fa-try';
	$icons[] = 'w2mb-fa-tty';
	$icons[] = 'w2mb-fa-tumblr';
	$icons[] = 'w2mb-fa-tumblr-square';
	$icons[] = 'w2mb-fa-turkish-lira';
	$icons[] = 'w2mb-fa-twitch';
	$icons[] = 'w2mb-fa-twitter';
	$icons[] = 'w2mb-fa-twitter-square';
	$icons[] = 'w2mb-fa-umbrella';
	$icons[] = 'w2mb-fa-underline';
	$icons[] = 'w2mb-fa-undo';
	$icons[] = 'w2mb-fa-university';
	$icons[] = 'w2mb-fa-unlink';
	$icons[] = 'w2mb-fa-unlock';
	$icons[] = 'w2mb-fa-unlock-alt';
	$icons[] = 'w2mb-fa-unsorted';
	$icons[] = 'w2mb-fa-upload';
	$icons[] = 'w2mb-fa-usd';
	$icons[] = 'w2mb-fa-user';
	$icons[] = 'w2mb-fa-user-md';
	$icons[] = 'w2mb-fa-user-plus';
	$icons[] = 'w2mb-fa-user-secret';
	$icons[] = 'w2mb-fa-user-times';
	$icons[] = 'w2mb-fa-users';
	$icons[] = 'w2mb-fa-venus';
	$icons[] = 'w2mb-fa-venus-double';
	$icons[] = 'w2mb-fa-venus-mars';
	$icons[] = 'w2mb-fa-viacoin';
	$icons[] = 'w2mb-fa-video-camera';
	$icons[] = 'w2mb-fa-vimeo-square';
	$icons[] = 'w2mb-fa-vine';
	$icons[] = 'w2mb-fa-vk';
	$icons[] = 'w2mb-fa-volume-down';
	$icons[] = 'w2mb-fa-volume-off';
	$icons[] = 'w2mb-fa-volume-up';
	$icons[] = 'w2mb-fa-warning';
	$icons[] = 'w2mb-fa-wechat';
	$icons[] = 'w2mb-fa-weibo';
	$icons[] = 'w2mb-fa-weixin';
	$icons[] = 'w2mb-fa-whatsapp';
	$icons[] = 'w2mb-fa-wheelchair';
	$icons[] = 'w2mb-fa-wifi';
	$icons[] = 'w2mb-fa-windows';
	$icons[] = 'w2mb-fa-won';
	$icons[] = 'w2mb-fa-wordpress';
	$icons[] = 'w2mb-fa-wrench';
	$icons[] = 'w2mb-fa-xing';
	$icons[] = 'w2mb-fa-xing-square';
	$icons[] = 'w2mb-fa-yahoo';
	$icons[] = 'w2mb-fa-yen';
	$icons[] = 'w2mb-fa-youtube';
	$icons[] = 'w2mb-fa-youtube-play';
	$icons[] = 'w2mb-fa-youtube-square';
	return $icons;
}

function w2mb_current_user_can_edit_listing($listing_id) {
	if (!current_user_can('edit_others_posts')) {
		$post = get_post($listing_id);
		$current_user = wp_get_current_user();
		if ($current_user->ID != $post->post_author)
			return false;
		if ($post->post_status == 'pending'  && !is_admin())
			return false;
	}
	return true;
}

function w2mb_get_edit_listing_link($listing_id, $context = 'display') {
	if (w2mb_current_user_can_edit_listing($listing_id)) {
		$post = get_post($listing_id);
		$current_user = wp_get_current_user();
		if (current_user_can('edit_others_posts') && $current_user->ID != $post->post_author)
			return get_edit_post_link($listing_id, $context);
		else
			return apply_filters('w2mb_get_edit_listing_link', get_edit_post_link($listing_id, $context), $listing_id);
	}
}

function w2mb_show_edit_button($listing_id) {
	global $w2mb_instance;
	if (
		w2mb_current_user_can_edit_listing($listing_id)
		&&
		(
			(get_option('w2mb_fsubmit_addon') && isset($w2mb_instance->dashboard_page_url) && $w2mb_instance->dashboard_page_url)
			||
			((!get_option('w2mb_fsubmit_addon') || !isset($w2mb_instance->dashboard_page_url) || !$w2mb_instance->dashboard_page_url) && !get_option('w2mb_hide_admin_bar') && current_user_can('edit_posts'))
		)
	)
		return true;
}

function w2mb_hex2rgba($color, $opacity = false) {
	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if(empty($color))
		return $default;

	//Sanitize $color if "#" is provided
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}

	//Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}

	//Convert hexadec to rgb
	$rgb =  array_map('hexdec', $hex);

	//Check if opacity is set(rgba or rgb)
	if (abs($opacity) > 1) {
		$opacity = 1.0;
	} elseif (abs($opacity) < 0) {
		$opacity = 0;
	}
	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';

	//Return rgb(a) color string
	return $output;
}

function w2mb_adjust_brightness($hex, $steps) {
	// Steps should be between -255 and 255. Negative = darker, positive = lighter
	$steps = max(-255, min(255, $steps));

	// Normalize into a six character long hex string
	$hex = str_replace('#', '', $hex);
	if (strlen($hex) == 3) {
		$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
	}

	// Split into three parts: R, G and B
	$color_parts = str_split($hex, 2);
	$return = '#';

	foreach ($color_parts as $color) {
		$color   = hexdec($color); // Convert to decimal
		$color   = max(0,min(255,$color + $steps)); // Adjust color
		$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
	}

	return $return;
}

// adapted for Relevanssi
function w2mb_is_relevanssi_search($defaults = false) {
	if (
		function_exists('relevanssi_do_query') &&
		(
				(
						!$defaults &&
						w2mb_getValue($_REQUEST, 'what_search')
				) ||
				($defaults && isset($defaults['what_search']) && $defaults['what_search'])
		)
	) {
		return apply_filters('w2mb_is_relevanssi_search', true, $defaults);
	}
}

/**
 * print class name to make field caption shorter
 *
 * @param object $group
 * @return boolean
 */
function w2mb_is_any_field_name_in_group($group) {
	if ($group) {
		foreach ($group->content_fields_array AS $field_id=>$field) {
			if (!$field->is_hide_name) {
				return true;
			}
		}
		echo "w2mb-field-caption-short";
		return false;
	}
}

function w2mb_error_log($wp_error) {
	w2mb_addMessage($wp_error->get_error_message(), 'error');
	error_log($wp_error->get_error_message());
}

function w2mb_country_codes() {
	$codes['Afghanistan'] = 'AF';
	$codes['Åland Islands'] = 'AX';
	$codes['Albania'] = 'AL';
	$codes['Algeria'] = 'DZ';
	$codes['American Samoa'] = 'AS';
	$codes['Andorra'] = 'AD';
	$codes['Angola'] = 'AO';
	$codes['Anguilla'] = 'AI';
	$codes['Antarctica'] = 'AQ';
	$codes['Antigua and Barbuda'] = 'AG';
	$codes['Argentina'] = 'AR';
	$codes['Armenia'] = 'AM';
	$codes['Aruba'] = 'AW';
	$codes['Australia'] = 'AU';
	$codes['Austria'] = 'AT';
	$codes['Azerbaijan'] = 'AZ';
	$codes['Bahamas'] = 'BS';
	$codes['Bahrain'] = 'BH';
	$codes['Bangladesh'] = 'BD';
	$codes['Barbados'] = 'BB';
	$codes['Belarus'] = 'BY';
	$codes['Belgium'] = 'BE';
	$codes['Belize'] = 'BZ';
	$codes['Benin'] = 'BJ';
	$codes['Bermuda'] = 'BM';
	$codes['Bhutan'] = 'BT';
	$codes['Bolivia, Plurinational State of'] = 'BO';
	$codes['Bonaire, Sint Eustatius and Saba'] = 'BQ';
	$codes['Bosnia and Herzegovina'] = 'BA';
	$codes['Botswana'] = 'BW';
	$codes['Bouvet Island'] = 'BV';
	$codes['Brazil'] = 'BR';
	$codes['British Indian Ocean Territory'] = 'IO';
	$codes['Brunei Darussalam'] = 'BN';
	$codes['Bulgaria'] = 'BG';
	$codes['Burkina Faso'] = 'BF';
	$codes['Burundi'] = 'BI';
	$codes['Cambodia'] = 'KH';
	$codes['Cameroon'] = 'CM';
	$codes['Canada'] = 'CA';
	$codes['Cape Verde'] = 'CV';
	$codes['Cayman Islands'] = 'KY';
	$codes['Central African Republic'] = 'CF';
	$codes['Chad'] = 'TD';
	$codes['Chile'] = 'CL';
	$codes['China'] = 'CN';
	$codes['Christmas Island'] = 'CX';
	$codes['Cocos (Keeling) Islands'] = 'CC';
	$codes['Colombia'] = 'CO';
	$codes['Comoros'] = 'KM';
	$codes['Congo'] = 'CG';
	$codes['Congo, the Democratic Republic of the'] = 'CD';
	$codes['Cook Islands'] = 'CK';
	$codes['Costa Rica'] = 'CR';
	$codes['Côte d\'Ivoire'] = 'CI';
	$codes['Croatia'] = 'HR';
	$codes['Cuba'] = 'CU';
	$codes['Curaçao'] = 'CW';
	$codes['Cyprus'] = 'CY';
	$codes['Czech Republic'] = 'CZ';
	$codes['Denmark'] = 'DK';
	$codes['Djibouti'] = 'DJ';
	$codes['Dominica'] = 'DM';
	$codes['Dominican Republic'] = 'DO';
	$codes['Ecuador'] = 'EC';
	$codes['Egypt'] = 'EG';
	$codes['El Salvador'] = 'SV';
	$codes['Equatorial Guinea'] = 'GQ';
	$codes['Eritrea'] = 'ER';
	$codes['Estonia'] = 'EE';
	$codes['Ethiopia'] = 'ET';
	$codes['Falkland Islands (Malvinas)'] = 'FK';
	$codes['Faroe Islands'] = 'FO';
	$codes['Fiji'] = 'FJ';
	$codes['Finland'] = 'FI';
	$codes['France'] = 'FR';
	$codes['French Guiana'] = 'GF';
	$codes['French Polynesia'] = 'PF';
	$codes['French Southern Territories'] = 'TF';
	$codes['Gabon'] = 'GA';
	$codes['Gambia'] = 'GM';
	$codes['Georgia'] = 'GE';
	$codes['Germany'] = 'DE';
	$codes['Ghana'] = 'GH';
	$codes['Gibraltar'] = 'GI';
	$codes['Greece'] = 'GR';
	$codes['Greenland'] = 'GL';
	$codes['Grenada'] = 'GD';
	$codes['Guadeloupe'] = 'GP';
	$codes['Guam'] = 'GU';
	$codes['Guatemala'] = 'GT';
	$codes['Guernsey'] = 'GG';
	$codes['Guinea'] = 'GN';
	$codes['Guinea-Bissau'] = 'GW';
	$codes['Guyana'] = 'GY';
	$codes['Haiti'] = 'HT';
	$codes['Heard Island and McDonald Islands'] = 'HM';
	$codes['Holy See (Vatican City State)'] = 'VA';
	$codes['Honduras'] = 'HN';
	$codes['Hong Kong'] = 'HK';
	$codes['Hungary'] = 'HU';
	$codes['Iceland'] = 'IS';
	$codes['India'] = 'IN';
	$codes['Indonesia'] = 'ID';
	$codes['Iran, Islamic Republic of'] = 'IR';
	$codes['Iraq'] = 'IQ';
	$codes['Ireland'] = 'IE';
	$codes['Isle of Man'] = 'IM';
	$codes['Israel'] = 'IL';
	$codes['Italy'] = 'IT';
	$codes['Jamaica'] = 'JM';
	$codes['Japan'] = 'JP';
	$codes['Jersey'] = 'JE';
	$codes['Jordan'] = 'JO';
	$codes['Kazakhstan'] = 'KZ';
	$codes['Kenya'] = 'KE';
	$codes['Kiribati'] = 'KI';
	$codes['Korea, Democratic People\'s Republic of'] = 'KP';
	$codes['Korea, Republic of'] = 'KR';
	$codes['Kuwait'] = 'KW';
	$codes['Kyrgyzstan'] = 'KG';
	$codes['Lao People\'s Democratic Republic'] = 'LA';
	$codes['Latvia'] = 'LV';
	$codes['Lebanon'] = 'LB';
	$codes['Lesotho'] = 'LS';
	$codes['Liberia'] = 'LR';
	$codes['Libya'] = 'LY';
	$codes['Liechtenstein'] = 'LI';
	$codes['Lithuania'] = 'LT';
	$codes['Luxembourg'] = 'LU';
	$codes['Macao'] = 'MO';
	$codes['Macedonia, the Former Yugoslav Republic of'] = 'MK';
	$codes['Madagascar'] = 'MG';
	$codes['Malawi'] = 'MW';
	$codes['Malaysia'] = 'MY';
	$codes['Maldives'] = 'MV';
	$codes['Mali'] = 'ML';
	$codes['Malta'] = 'MT';
	$codes['Marshall Islands'] = 'MH';
	$codes['Martinique'] = 'MQ';
	$codes['Mauritania'] = 'MR';
	$codes['Mauritius'] = 'MU';
	$codes['Mayotte'] = 'YT';
	$codes['Mexico'] = 'MX';
	$codes['Micronesia, Federated States of'] = 'FM';
	$codes['Moldova, Republic of'] = 'MD';
	$codes['Monaco'] = 'MC';
	$codes['Mongolia'] = 'MN';
	$codes['Montenegro'] = 'ME';
	$codes['Montserrat'] = 'MS';
	$codes['Morocco'] = 'MA';
	$codes['Mozambique'] = 'MZ';
	$codes['Myanmar'] = 'MM';
	$codes['Namibia'] = 'NA';
	$codes['Nauru'] = 'NR';
	$codes['Nepal'] = 'NP';
	$codes['Netherlands'] = 'NL';
	$codes['New Caledonia'] = 'NC';
	$codes['New Zealand'] = 'NZ';
	$codes['Nicaragua'] = 'NI';
	$codes['Niger'] = 'NE';
	$codes['Nigeria'] = 'NG';
	$codes['Niue'] = 'NU';
	$codes['Norfolk Island'] = 'NF';
	$codes['Northern Mariana Islands'] = 'MP';
	$codes['Norway'] = 'NO';
	$codes['Oman'] = 'OM';
	$codes['Pakistan'] = 'PK';
	$codes['Palau'] = 'PW';
	$codes['Palestine, State of'] = 'PS';
	$codes['Panama'] = 'PA';
	$codes['Papua New Guinea'] = 'PG';
	$codes['Paraguay'] = 'PY';
	$codes['Peru'] = 'PE';
	$codes['Philippines'] = 'PH';
	$codes['Pitcairn'] = 'PN';
	$codes['Poland'] = 'PL';
	$codes['Portugal'] = 'PT';
	$codes['Puerto Rico'] = 'PR';
	$codes['Qatar'] = 'QA';
	$codes['Réunion'] = 'RE';
	$codes['Romania'] = 'RO';
	$codes['Russian Federation'] = 'RU';
	$codes['Rwanda'] = 'RW';
	$codes['Saint Barthélemy'] = 'BL';
	$codes['Saint Helena, Ascension and Tristan da Cunha'] = 'SH';
	$codes['Saint Kitts and Nevis'] = 'KN';
	$codes['Saint Lucia'] = 'LC';
	$codes['Saint Martin (French part)'] = 'MF';
	$codes['Saint Pierre and Miquelon'] = 'PM';
	$codes['Saint Vincent and the Grenadines'] = 'VC';
	$codes['Samoa'] = 'WS';
	$codes['San Marino'] = 'SM';
	$codes['Sao Tome and Principe'] = 'ST';
	$codes['Saudi Arabia'] = 'SA';
	$codes['Senegal'] = 'SN';
	$codes['Serbia'] = 'RS';
	$codes['Seychelles'] = 'SC';
	$codes['Sierra Leone'] = 'SL';
	$codes['Singapore'] = 'SG';
	$codes['Sint Maarten (Dutch part)'] = 'SX';
	$codes['Slovakia'] = 'SK';
	$codes['Slovenia'] = 'SI';
	$codes['Solomon Islands'] = 'SB';
	$codes['Somalia'] = 'SO';
	$codes['South Africa'] = 'ZA';
	$codes['South Georgia and the South Sandwich Islands'] = 'GS';
	$codes['South Sudan'] = 'SS';
	$codes['Spain'] = 'ES';
	$codes['Sri Lanka'] = 'LK';
	$codes['Sudan'] = 'SD';
	$codes['Suriname'] = 'SR';
	$codes['Svalbard and Jan Mayen'] = 'SJ';
	$codes['Swaziland'] = 'SZ';
	$codes['Sweden'] = 'SE';
	$codes['Switzerland'] = 'CH';
	$codes['Syrian Arab Republic'] = 'SY';
	$codes['Taiwan, Province of China"'] = 'TW';
	$codes['Tajikistan'] = 'TJ';
	$codes['"Tanzania, United Republic of"'] = 'TZ';
	$codes['Thailand'] = 'TH';
	$codes['Timor-Leste'] = 'TL';
	$codes['Togo'] = 'TG';
	$codes['Tokelau'] = 'TK';
	$codes['Tonga'] = 'TO';
	$codes['Trinidad and Tobago'] = 'TT';
	$codes['Tunisia'] = 'TN';
	$codes['Turkey'] = 'TR';
	$codes['Turkmenistan'] = 'TM';
	$codes['Turks and Caicos Islands'] = 'TC';
	$codes['Tuvalu'] = 'TV';
	$codes['Uganda'] = 'UG';
	$codes['Ukraine'] = 'UA';
	$codes['United Arab Emirates'] = 'AE';
	$codes['United Kingdom'] = 'GB';
	$codes['United States'] = 'US';
	$codes['United States Minor Outlying Islands'] = 'UM';
	$codes['Uruguay'] = 'UY';
	$codes['Uzbekistan'] = 'UZ';
	$codes['Vanuatu'] = 'VU';
	$codes['Venezuela,  Bolivarian Republic of'] = 'VE';
	$codes['Viet Nam'] = 'VN';
	$codes['Virgin Islands, British'] = 'VG';
	$codes['Virgin Islands, U.S.'] = 'VI';
	$codes['Wallis and Futuna'] = 'WF';
	$codes['Western Sahara'] = 'EH';
	$codes['Yemen'] = 'YE';
	$codes['Zambia'] = 'ZM';
	$codes['Zimbabwe'] = 'ZW';
	return $codes;
}

function w2mb_getAdminNotificationEmail() {
	if (get_option('w2mb_admin_notifications_email'))
		return get_option('w2mb_admin_notifications_email');
	else 
		return get_option('admin_email');
}

function w2mb_wpmlTranslationCompleteNotice() {
	global $sitepress;

	if (function_exists('wpml_object_id_filter') && $sitepress && defined('WPML_ST_VERSION')) {
		echo '<p class="description">';
		esc_html_e('After save do not forget to set completed translation status for this string on String Translation page.', 'W2MB');
		echo '</p>';
	}
}

function w2mb_phpmailerInit($phpmailer) {
	$phpmailer->AltBody = wp_specialchars_decode($phpmailer->Body, ENT_QUOTES);
}
function w2mb_mail($email, $subject, $body, $headers = null) {
	// create and add HTML part into emails
	add_action('phpmailer_init', 'w2mb_phpmailerInit');

	if (!$headers) {
		$headers[] = "From: " . get_option('blogname') . " <" . w2mb_getAdminNotificationEmail() . ">";
		$headers[] = "Reply-To: " . w2mb_getAdminNotificationEmail();
		$headers[] = "Content-Type: text/html";
	}
		
	$subject = "[" . get_option('blogname') . "] " .$subject;

	$body = make_clickable(wpautop($body));
	
	$email = apply_filters('w2mb_mail_email', $email, $subject, $body, $headers);
	$subject = apply_filters('w2mb_mail_subject', $subject, $email, $body, $headers);
	$body = apply_filters('w2mb_mail_body', $body, $email, $subject, $headers);
	$headers = apply_filters('w2mb_mail_headers', $headers, $email, $subject, $body);
	
	add_action('wp_mail_failed', 'w2mb_error_log');

	return wp_mail($email, $subject, $body, $headers);
}

function w2mb_getListing($post) {
	$listing = new w2mb_listing;
	if ($listing->loadListingFromPost($post)) {
		return $listing;
	}
}

function w2mb_getCurrentCategory() {
	$category = null;

	if ($categories = w2mb_getValue($_REQUEST, 'categories')) {
		if (is_array($categories) || ($categories = array_filter(explode(',', $categories), 'trim'))) {
			$category_id = array_shift($categories);
			if ($category_term = get_term($category_id, W2MB_CATEGORIES_TAX)) {
				$category = $category_term;
			}
		}
	}

	return $category;
}

function w2mb_isMapsPageInAdmin() {
	global $pagenow;

	if (
		is_admin() &&
		(
				(($pagenow == 'edit.php' || $pagenow == 'post-new.php') && ($post_type = w2mb_getValue($_GET, 'post_type')) &&
						(in_array($post_type, array(W2MB_POST_TYPE, W2MB_MAP_TYPE)))
				) ||
				($pagenow == 'post.php' && ($post_id = w2mb_getValue($_GET, 'post')) && ($post = get_post($post_id)) &&
						(in_array($post->post_type, array(W2MB_POST_TYPE, W2MB_MAP_TYPE)))
				) ||
				(($pagenow == 'edit-tags.php' || $pagenow == 'term.php') && ($taxonomy = w2mb_getValue($_GET, 'taxonomy')) &&
						(in_array($taxonomy, array(W2MB_LOCATIONS_TAX, W2MB_CATEGORIES_TAX, W2MB_TAGS_TAX)))
				) ||
				(($page = w2mb_getValue($_GET, 'page')) &&
						(in_array($page,
								array(
										'w2mb_settings',
										'w2mb_locations_levels',
										'w2mb_content_fields',
										'w2mb_csv_import',
										'w2mb_renew',
										'w2mb_changedate',
										'w2mb_raise_up',
										'w2mb_process_claim'
								)
						))
				) ||
				($pagenow == 'widgets.php')
		)
	) {
		return true;
	}
}

function w2mb_isListingEditPageInAdmin() {
	global $pagenow;

	if (
		($pagenow == 'post-new.php' && ($post_type = w2mb_getValue($_GET, 'post_type')) &&
				(in_array($post_type, array(W2MB_POST_TYPE)))
		) ||
		($pagenow == 'post.php' && ($post_id = w2mb_getValue($_GET, 'post')) && ($post = get_post($post_id)) &&
				(in_array($post->post_type, array(W2MB_POST_TYPE)))
		)
	) {
		return true;
	}
}

function w2mb_isLocationsEditPageInAdmin() {
	global $pagenow;

	if (($pagenow == 'edit-tags.php' || $pagenow == 'term.php') && ($taxonomy = w2mb_getValue($_GET, 'taxonomy')) &&
				(in_array($taxonomy, array(W2MB_LOCATIONS_TAX)))) {
		return true;
	}
}

function w2mb_isCategoriesEditPageInAdmin() {
	global $pagenow;

	if (($pagenow == 'edit-tags.php' || $pagenow == 'term.php') && ($taxonomy = w2mb_getValue($_GET, 'taxonomy')) &&
				(in_array($taxonomy, array(W2MB_CATEGORIES_TAX)))) {
		return true;
	}
}

function w2mb_getCategoryIconFile($term_id) {
	if (($icons = get_option('w2mb_categories_icons')) && is_array($icons) && isset($icons[$term_id])) {
		return $icons[$term_id];
	}
}

function w2mb_getCategoryImageUrl($term_id, $size = 'full') {
	global $w2mb_instance;
	
	if ($image_url = $w2mb_instance->categories_manager->get_featured_image_url($term_id, $size)) {
		return $image_url;
	}
}

function w2mb_getLocationIconFile($term_id) {
	if (($icons = get_option('w2mb_locations_icons')) && is_array($icons) && isset($icons[$term_id])) {
		return $icons[$term_id];
	}
}

function w2mb_getLocationImageUrl($term_id, $size = 'full') {
	global $w2mb_instance;

	if ($image_url = $w2mb_instance->locations_manager->get_featured_image_url($term_id, $size)) {
		return $image_url;
	}
}

function w2mb_getSearchTermID($query_var, $get_var, $default_term_id) {
	if (get_query_var($query_var) && ($category_object = w2mb_get_term_by_path(get_query_var($query_var)))) {
		$term_id = $category_object->term_id;
	} elseif (isset($_GET[$get_var]) && is_numeric($_GET[$get_var])) {
		$term_id = $_GET[$get_var];
	} else {
		$term_id = $default_term_id;
	}
	return $term_id;
}

function w2mb_getAllMapStyles() {
	return w2mb_getMapBoxStyles();
}

function w2mb_getSelectedMapStyleName() {
	return "";
} 

function w2mb_getSelectedMapStyle($map_style_name = false) {
	if (!$map_style_name) {
		// when we are on listing dialog-  we should take map style name from current map
		$map_style_name = w2mb_getSelectedMapStyleName();
	}

	$mapbox_styles = w2mb_getMapBoxStyles();
	if (in_array($map_style_name, $mapbox_styles)) {
		return $map_style_name;
	} elseif (array_key_exists($map_style_name, $mapbox_styles)) {
		return $mapbox_styles[$map_style_name];
	} elseif ($map_style_name) {
		return $map_style_name;
	} else {
		return array_shift($mapbox_styles);
	}
}

function w2mb_wrapKeys(&$val) {
	$val = "`".$val."`";
}
function w2mb_wrapValues(&$val) {
	$val = "'".$val."'";
}
function w2mb_wrapIntVal(&$val) {
	$val = intval($val);
}

function w2mb_utf8ize($mixed) {
	if (is_array($mixed)) {
		foreach ($mixed as $key => $value) {
			$mixed[$key] = w2mb_utf8ize($value);
		}
	} elseif (is_string($mixed)) {
		return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
	}
	return $mixed;
}

function w2mb_get_registered_image_sizes($unset_disabled = true) {
	$wais = & $GLOBALS['_wp_additional_image_sizes'];

	$sizes = array();

	foreach (get_intermediate_image_sizes() as $_size) {
		if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
			$sizes[ $_size ] = array(
					'width'  => get_option("{$_size}_size_w"),
					'height' => get_option("{$_size}_size_h"),
					'crop'   => (bool) get_option("{$_size}_crop"),
			);
		}
		elseif (isset($wais[$_size])) {
			$sizes[ $_size ] = array(
					'width'  => $wais[$_size]['width'],
					'height' => $wais[$_size]['height'],
					'crop'   => $wais[$_size]['crop'],
			);
		}

		// size registered, but has 0 width and height
		if($unset_disabled && ($sizes[ $_size ]['width'] == 0) && ($sizes[ $_size ]['height'] == 0)) {
			unset($sizes[$_size]);
		}
	}

	return $sizes;
}

function w2mb_get_marker_anchor() {
	if (get_option('w2mb_marker_anchor')) {
		return get_option('w2mb_marker_anchor');
	} else {
		return 'w2mb-marker';
	}
}

function w2mb_get_listing_anchor() {
	if (get_option('w2mb_listing_anchor')) {
		return get_option('w2mb_listing_anchor');
	} else {
		return 'w2mb-listing';
	}
}

function w2mb_getMapBoxStyles() {
	$styles = array(
			'Streets' => 'mapbox://styles/mapbox/streets-v10',
			'OutDoors' => 'mapbox://styles/mapbox/outdoors-v10',
			'Light' => 'mapbox://styles/mapbox/light-v9',
			'Dark' => 'mapbox://styles/mapbox/dark-v9',
			'Satellite' => 'mapbox://styles/mapbox/satellite-v9',
			'Satellite streets' => 'mapbox://styles/mapbox/satellite-streets-v10',
			'Navigation preview day' => 'mapbox://styles/mapbox/navigation-preview-day-v2',
			'Navigation preview night' => 'mapbox://styles/mapbox/navigation-preview-night-v2',
			'Navigation guidance day' => 'mapbox://styles/mapbox/navigation-guidance-day-v2',
			'Navigation guidance night' => 'mapbox://styles/mapbox/navigation-guidance-night-v2',
	);

	$styles = apply_filters('w2mb_mapbox_maps_styles', $styles);

	return $styles;
}

?>