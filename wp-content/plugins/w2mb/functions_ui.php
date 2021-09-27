<?php

function w2mb_tax_dropdowns_menu_init($params) {
	$attrs = array_merge(array(
			'uID' => 0,
			'field_name' => '',
			'count' => true,
			'tax' => 'category',
			'hide_empty' => false,
			'exact_terms' => array(),
			'autocomplete_field' => '',
			'autocomplete_field_value' => '',
			'autocomplete_ajax' => false,
			'placeholder' => '',
			'depth' => 1,
			'term_id' => 0,
	), $params);
	extract($attrs);
	
	// unique ID need when we place some dropdowns groups on one page
	if (!$uID) {
		$uID = rand(1, 10000);
	}
	
	if (!$field_name) {
		$field_name = 'selected_tax[' . $uID . ']';
	}
	
	// we use array_merge with empty array because we need to flush keys in terms array
	if ($count) {
		$terms = array_merge(
				// there is a wp bug with pad_counts in get_terms function - so we use this construction
				wp_list_filter(
						get_categories(array(
								'taxonomy' => $tax,
								'pad_counts' => true,
								'hide_empty' => $hide_empty,
						)),
						array('parent' => 0)
				), array());
	} else {
		$terms = array_merge(
				get_categories(array(
						'taxonomy' => $tax,
						'pad_counts' => true,
						'hide_empty' => $hide_empty,
						'parent' => 0,
				)), array());
	}
	
	// show terms and/or autocomplete search field
	if ($terms || $autocomplete_field) {
		foreach ($terms AS $id=>$term) {
			if ($exact_terms && (!in_array($term->term_id, $exact_terms) && !in_array($term->slug, $exact_terms))) {
				unset($terms[$id]);
			}
		}
		
		// when selected exact sub-categories of non-root category
		if (empty($terms) && !empty($exact_terms)) {
			if ($count) {
				// there is a wp bug with pad_counts in get_terms function - so we use this construction
				$terms = wp_list_filter(get_categories(array('taxonomy' => $tax, 'include' => $exact_terms, 'pad_counts' => true, 'hide_empty' => $hide_empty)));
			} else {
				$terms = get_categories(array('taxonomy' => $tax, 'include' => $exact_terms, 'pad_counts' => true, 'hide_empty' => $hide_empty));
			}
		}
		
		$selected_tax_text = '';
		if ($term_id) {
			if ($term = get_term($term_id)) {
				$selected_tax_text = $term->name;
				$parents = w2mb_get_term_parents($term_id, $tax, false, false, ', ');
				if ($parents) {
					$selected_tax_text .= ', ' . $parents;
				}
			}
		}
		
		echo '<div id="w2mb-tax-dropdowns-wrap-' . esc_attr($uID) . '" class="w2mb-tax-dropdowns-wrap">';
		echo '<input type="hidden" name="' . esc_attr($field_name) . '" id="selected_tax[' . esc_attr($uID) . ']" class="selected_tax_' . esc_attr($tax) . '" value="' . esc_attr($term_id) . '" />';
		echo '<input type="hidden" name="' . esc_attr($field_name) . '_text" id="selected_tax_text[' . esc_attr($uID) . ']" class="selected_tax_text_' . esc_attr($tax) . '" value="' . esc_attr($selected_tax_text) . '" />';
		if ($exact_terms) {
			echo '<input type="hidden" id="exact_terms[' . $uID . ']" value="' . esc_attr(addslashes(implode(',', $exact_terms))) . '" />';
		}
		if ($autocomplete_field) {
			$autocomplete_data = 'data-autocomplete-name="' . esc_attr($autocomplete_field) . '" data-autocomplete-value="' . esc_attr($autocomplete_field_value) . '" data-default-icon="' . w2mb_getDefaultTermIconUrl($tax) . '"';
			if ($autocomplete_ajax) {
				$autocomplete_data .= ' data-ajax-search=1';
			}
		} else {
			$autocomplete_data = '';
		}
		echo '<select class="w2mb-form-control w2mb-selectmenu-' . esc_attr($tax) . '" data-id="' . esc_attr($uID) . '" data-placeholder="' . esc_attr($placeholder) . '" ' . $autocomplete_data . '>';
		foreach ($terms AS $term) {
			
			$term_count = '';
			if ($count) {
				$term_count = 'data-count="' . esc_attr($term->count) . ' ' . _n("result", "results", $term->count, "W2MB") . '"';
			}
			
			$selected = '';
			if ($term->term_id == $term_id) {
				$selected = 'data-selected="selected"';
			}
			
			$icon = '';
			if ($icon_file = w2mb_getTermIconUrl($term->term_id)) {
				$icon = 'data-icon="' . esc_attr($icon_file) . '"';
			}

			echo '<option id="' . esc_attr($term->slug) . '" value="' . esc_attr($term->term_id) . '" data-name="' . esc_attr($term->name)  . '" data-sublabel="" ' . $selected . ' ' . $icon . ' ' . $term_count . '>' . $term->name . '</option>';
			if ($depth > 1) {
				echo _w2mb_tax_dropdowns_menu($tax, $term->term_id, $depth, 1, $term_id, $count, $exact_terms, $hide_empty);
			}
		}
		echo '</select>';
		echo '</div>';
	}
}

function _w2mb_tax_dropdowns_menu($tax, $parent = 0, $depth = 2, $current_level = 1, $term_id = null, $count = false, $exact_terms = array(), $hide_empty = false) {
	if ($count) {
		// there is a wp bug with pad_counts in get_terms function - so we use this construction
		$terms = wp_list_filter(
				get_categories(array(
						'taxonomy' => $tax,
						'pad_counts' => true,
						'hide_empty' => $hide_empty,
				)),
				array('parent' => $parent)
		);
	} else {
		$terms = get_categories(array(
				'taxonomy' => $tax,
				'pad_counts' => true,
				'hide_empty' => $hide_empty,
				'parent' => $parent,
		));
	}
	
	$html = '';
	if ($terms && ($depth == 0 || !is_numeric($depth) || $depth > $current_level)) {
		foreach ($terms AS $key=>$term) {
			if ($exact_terms && (!in_array($term->term_id, $exact_terms) && !in_array($term->slug, $exact_terms))) {
				unset($terms[$key]);
			}
		}
	
		if ($terms) {
			$current_level++;
			
			$sublabel = w2mb_get_term_parents($term->parent, $tax, false, false, ', ');

			foreach ($terms AS $term) {
				
				$term_count = '';
				if ($count) {
					$term_count = 'data-count="' . esc_attr($term->count) . ' ' . _n("result", "results", $term->count, "W2MB") . '"';
				}
				
				$selected = '';
				if ($term->term_id == $term_id) {
					$selected = 'data-selected="selected"';
				}
				
				$icon = '';
				if ($icon_file = w2mb_getTermIconUrl($term->term_id)) {
					$icon = 'data-icon="' . $icon_file . '"';
				}
			
				echo '<option id="' . esc_attr($term->slug) . '" value="' . esc_attr($term->term_id) . '" data-name="' . esc_attr($term->name)  . '" data-sublabel="' . esc_attr($sublabel) . '" ' . $selected . ' ' . $icon . ' ' . $term_count . '>' . $term->name . '</option>';
				if ($depth > $current_level) {
					echo _w2mb_tax_dropdowns_menu($tax, $term->term_id, $depth, $current_level, $term_id, $count, $exact_terms, $hide_empty);
				}
			}
		}
	}
	return $html;
}

function w2mb_tax_dropdowns_init($args) {
	$tax = w2mb_getValue($args, 'tax', 'category');
	$field_name = w2mb_getValue($args, 'field_name');
	$term_id = w2mb_getValue($args, 'term_id');
	$count = w2mb_getValue($args, 'count', true);
	$labels = w2mb_getValue($args, 'labels', array());
	$titles = w2mb_getValue($args, 'titles', array());
	$allow_add_term = w2mb_getValue($args, 'allow_add_term', array());
	$uID = w2mb_getValue($args, 'uID');
	$exact_terms = w2mb_getValue($args, 'exact_terms', array());
	$hide_empty = w2mb_getValue($args, 'hide_empty', false);
	
	// unique ID need when we place some dropdowns groups on one page
	if (!$uID) {
		$uID = rand(1, 10000);
	}

	$localized_data[$uID] = array(
			'labels'      => $labels,
			'titles'      => $titles,
			'allow_add_term' => $allow_add_term,
	);
	echo "<script>w2mb_js_objects['tax_dropdowns_" . $uID . "'] = " . json_encode($localized_data) . "</script>";

	if (!is_null($term_id) && $term_id != 0) {
		$chain = array();
		$parent_id = $term_id;
		while ($parent_id != 0) {
			if ($term = get_term($parent_id, $tax)) {
				$chain[] = $term->term_id;
				$parent_id = $term->parent;
			} else {
				break;
			}
		}
	}
	$chain[] = 0;
	$chain = array_reverse($chain);

	if (!$field_name) {
		$field_name = 'selected_tax[' . $uID . ']';
	}

	echo '<div id="w2mb-tax-dropdowns-wrap-' . esc_attr($uID) . '" class="' . esc_attr($tax) . ' cs_count_' . (int)$count . ' cs_hide_empty_' . (int)$hide_empty . ' w2mb-tax-dropdowns-wrap">';
	echo '<input type="hidden" name="' . esc_attr($field_name) . '" id="selected_tax[' . esc_attr($uID) . ']" class="selected_tax_' . esc_attr($tax) . '" value="' . esc_attr($term_id) . '" />';
	echo '<input type="hidden" id="exact_terms[' . esc_attr($uID) . ']" value="' . esc_attr(addslashes(implode(',', $exact_terms))) . '" />';
	foreach ($chain AS $key=>$term_id) {
		if ($count) {
			// there is a wp bug with pad_counts in get_terms function - so we use this construction
			$terms = wp_list_filter(get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => $hide_empty)), array('parent' => $term_id));
		} else {
			$terms = get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => $hide_empty, 'parent' => $term_id));
		}

		if (!empty($terms)) {
			foreach ($terms AS $id=>$term) {
				if ($exact_terms && (!in_array($term->term_id, $exact_terms) && !in_array($term->slug, $exact_terms))) {
					unset($terms[$id]);
				}
			}

			// when selected exact sub-categories of non-root category
			if (empty($terms) && !empty($exact_terms)) {
				if ($count) {
					// there is a wp bug with pad_counts in get_terms function - so we use this construction
					$terms = wp_list_filter(get_categories(array('taxonomy' => $tax, 'include' => $exact_terms, 'pad_counts' => true, 'hide_empty' => $hide_empty)));
				} else {
					$terms = get_categories(array('taxonomy' => $tax, 'include' => $exact_terms, 'pad_counts' => true, 'hide_empty' => $hide_empty));
				}
			}

			if (!empty($terms)) {
				$level_num = $key + 1;
				echo '<div id="wrap_chainlist_' . $level_num . '_' .$uID . '" class="w2mb-row w2mb-form-group w2mb-location-input w2mb-location-chainlist">';
				
					$label_name = '';
					if (isset($labels[$key])) {
						$label_name = $labels[$key];
					}
					echo '<div class="w2mb-col-md-2">';
					echo '<label class="w2mb-control-label" for="chainlist_' . esc_attr($level_num) . '_' . esc_attr($uID) . '">' . $label_name . '</label>';
					echo '</div>';
	
					if (isset($labels[$key])) {
					echo '<div class="w2mb-col-md-10">';
					} else {
					echo '<div class="w2mb-col-md-12">';
					}
						echo '<select id="chainlist_' . esc_attr($level_num) . '_' . esc_attr($uID) . '" class="w2mb-form-control w2mb-selectmenu">';
						echo '<option value="">- ' . ((isset($titles[$key])) ? $titles[$key] : esc_html__('Select term', 'W2MB')) . ' -</option>';
						foreach ($terms AS $term) {
							if ($count)
								$term_count = " ($term->count)";
							else
								 $term_count = '';
							if (isset($chain[$key+1]) && $term->term_id == $chain[$key+1]) {
								$selected = 'selected';
							} else
								$selected = '';
									
							if ($icon_file = w2mb_getTermIconUrl($term->term_id))
								$icon = 'data-class="term-icon" data-icon="' . esc_attr($icon_file) . '"';
							else
								$icon = '';
	
							echo '<option id="' . esc_attr($term->slug) . '" value="' . esc_attr($term->term_id) . '" ' . $selected . ' ' . $icon . '>' . $term->name . $term_count . '</option>';
						}
						echo '</select>';
						
						if (!empty($allow_add_term[$key])) {
							echo '<a class="w2mb-add-term-link" data-tax="' . esc_attr($tax) . '" data-parent="' . esc_attr($term_id) . '" data-uid="' . esc_attr($uID) . '" data-nonce="' . wp_create_nonce('w2mb_add_term_nonce') . '" data-exact-terms="' . implode(',', $exact_terms) . '" href="javascript:void(0);">' . sprintf(esc_html__('Add %s', 'W2MB'), $label_name) . '</a>';
						}
					echo '</div>';
				echo '</div>';
			}
		} else {
			if (isset($labels[$key])) {
				$label_name = $labels[$key];
			
				$level_num = $key + 1;
				
				if (!empty($allow_add_term[$key])) {
					echo '<div id="wrap_chainlist_' . esc_attr($level_num) . '_' .esc_attr($uID) . '" class="w2mb-row w2mb-form-group w2mb-location-input w2mb-location-chainlist">';
						echo '<div class="w2mb-col-md-10 w2mb-col-md-offset-2">';
						echo '<a class="w2mb-add-term-link" data-tax="' . esc_attr($tax) . '" data-parent="' . esc_attr($term_id) . '" data-uid="' . esc_attr($uID) . '" data-nonce="' . wp_create_nonce('w2mb_add_term_nonce') . '" data-exact-terms="' . implode(',', $exact_terms) . '" href="javascript:void(0);">' . sprintf(esc_html__('Add %s', 'W2MB'), $label_name) . '</a>';
						echo '</div>';
					echo '</div>';
				}
			}
		}
	}
	echo '</div>';
}

function w2mb_tax_dropdowns_updateterms() {
	$parentid = w2mb_getValue($_POST, 'parentid');
	$next_level = w2mb_getValue($_POST, 'next_level');
	$tax = w2mb_getValue($_POST, 'tax');
	$count = w2mb_getValue($_POST, 'count');
	$hide_empty = w2mb_getValue($_POST, 'hide_empty');
	$exact_terms = array_filter(explode(',', w2mb_getValue($_POST, 'exact_terms')));
	if (!$label = w2mb_getValue($_POST, 'label'))
		$label = '';
	if (!$title = w2mb_getValue($_POST, 'title'))
		$title = esc_html__('Select term', 'W2MB');
	$allow_add_term = w2mb_getValue($_POST, 'allow_add_term');
	$uID = w2mb_getValue($_POST, 'uID');
	
	if ($hide_empty == 'cs_hide_empty_1') {
		$hide_empty = true;
	} else {
		$hide_empty = false;
	}

	if ($count == 'cs_count_1') {
		// there is a wp bug with pad_counts in get_terms function - so we use this construction
		$terms = wp_list_filter(get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => $hide_empty)), array('parent' => $parentid));
	} else {
		$terms = get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => $hide_empty, 'parent' => $parentid));
	}
	if (!empty($terms)) {
		foreach ($terms AS $id=>$term) {
			if ($exact_terms && (!in_array($term->term_id, $exact_terms) && !in_array($term->slug, $exact_terms))) {
				unset($terms[$id]);
			}
		}

		if (!empty($terms)) {
			echo '<div id="wrap_chainlist_' . esc_attr($next_level) . '_' . esc_attr($uID) . '" class="w2mb-row w2mb-form-group w2mb-location-input w2mb-location-chainlist">';
	
				if ($label) {
					echo '<div class="w2mb-col-md-2">';
					echo '<label class="w2mb-control-label" for="chainlist_' . esc_attr($next_level) . '_' . esc_attr($uID) . '">' . $label . '</label>';
					echo '</div>';
				}
	
				if ($label) {
				echo '<div class="w2mb-col-md-10">';
				} else { 
				echo '<div class="w2mb-col-md-12">';
				}
					echo '<select id="chainlist_' . $next_level . '_' . $uID . '" class="w2mb-form-control w2mb-selectmenu">';
					echo '<option value="">- ' . $title . ' -</option>';
					foreach ($terms as $term) {
						if (!$exact_terms || (in_array($term->term_id, $exact_terms) || in_array($term->slug, $exact_terms))) {
							if ($count == 'cs_count_1') {
								$term_count = " ($term->count)";
							} else {
								$term_count = '';
							}
							
							if ($icon_file = w2mb_getTermIconUrl($term->term_id))
								$icon = 'data-class="term-icon" data-icon="' . esc_attr($icon_file) . '"';
							else
								$icon = '';
							
							echo '<option id="' . esc_attr($term->slug) . '" value="' . esc_attr($term->term_id) . '" ' . $icon . '>' . $term->name . $term_count . '</option>';
						}
					}
					echo '</select>';
					
					if ($allow_add_term) {
						echo '<a class="w2mb-add-term-link" data-tax="' . esc_attr($tax) . '" data-parent="' . esc_attr($parentid) . '" data-uid="' . esc_attr($uID) . '" data-nonce="' . wp_create_nonce('w2mb_add_term_nonce') . '" data-exact-terms="' . implode(',', $exact_terms) . '" href="javascript:void(0);">' . sprintf(esc_html__('Add %s', 'W2MB'), $label) . '</a>';
					}
				echo '</div>';
			echo '</div>';
		}
	} elseif ($label) {
		if ($allow_add_term) {
			echo '<div id="wrap_chainlist_' . esc_attr($next_level) . '_' . esc_attr($uID) . '" class="w2mb-row w2mb-form-group w2mb-location-input w2mb-location-chainlist">';
				echo '<div class="w2mb-col-md-10 w2mb-col-md-offset-2">';
				echo '<a class="w2mb-add-term-link" data-tax="' . esc_attr($tax) . '" data-parent="' . esc_attr($parentid) . '" data-uid="' . esc_attr($uID) . '" data-nonce="' . wp_create_nonce('w2mb_add_term_nonce') . '" data-exact-terms="' . implode(',', $exact_terms) . '" href="javascript:void(0);">' . sprintf(esc_html__('Add %s', 'W2MB'), $label) . '</a>';
				echo '</div>';
			echo '</div>';
		}
	}
	
	die();
}

function w2mb_renderOptionsTerms($tax, $parent, $selected_terms, $level = 0) {
	$terms = get_terms($tax, array('parent' => $parent, 'hide_empty' => false));

	foreach ($terms AS $term) {
		echo '<option value="' . esc_attr($term->term_id) . '" ' . (($selected_terms && (in_array($term->term_id, $selected_terms) || in_array($term->slug, $selected_terms))) ? 'selected' : '') . '>' . (str_repeat('&nbsp;&nbsp;&nbsp;', $level)) . $term->name . '</option>';
		w2mb_renderOptionsTerms($tax, $term->term_id, $selected_terms, $level+1);
	}
	return $terms;
}
function w2mb_termsSelectList($name, $tax = 'category', $selected_terms = array()) {
	echo '<select multiple="multiple" name="' . esc_attr($name) . '[]" class="selected_terms_list w2mb-form-control w2mb-form-group w2mb-select-height-300">';
	echo '<option value="" ' . ((!$selected_terms) ? 'selected' : '') . '>' . esc_html__('- Select All -', 'W2MB') . '</option>';

	w2mb_renderOptionsTerms($tax, 0, $selected_terms);

	echo '</select>';
}

function w2mb_recaptcha() {
	if (get_option('w2mb_enable_recaptcha') && get_option('w2mb_recaptcha_public_key') && get_option('w2mb_recaptcha_private_key')) {
		if (get_option('w2mb_recaptcha_version') == 'v2') {
			return '<div class="g-recaptcha" data-sitekey="'.get_option('w2mb_recaptcha_public_key').'"></div>';
		} elseif (get_option('w2mb_recaptcha_version') == 'v3') {
			ob_start();
			?>
			<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />
			<script>
			(function($) {
				"use strict";
	
				grecaptcha.ready(function() {
					grecaptcha.execute('<?php echo get_option('w2mb_recaptcha_public_key'); ?>').then(function(token) {
						$('#g-recaptcha-response').val(token);
					})
				});
			})(jQuery);
			</script>
			<?php 
			return ob_get_clean();
		}
	}
}

function w2mb_is_recaptcha_passed() {
	if (get_option('w2mb_enable_recaptcha') && get_option('w2mb_recaptcha_public_key') && get_option('w2mb_recaptcha_private_key')) {
		if (isset($_POST['g-recaptcha-response']))
			$captcha = $_POST['g-recaptcha-response'];
		else
			return false;
		
		$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".get_option('w2mb_recaptcha_private_key')."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
		if (!is_wp_error($response)) {
			$body = wp_remote_retrieve_body($response);
			$json = json_decode($body);
			if ($json->success === false)
				return false;
			else
				return true;
		} else
			return false;
	} else
		return true;
}

function w2mb_orderingItems() {
	global $w2mb_instance;

	$ordering = array('post_date' => esc_html__('Date', 'W2MB'), 'title' => esc_html__('Title', 'W2MB'), 'rand' => esc_html__('Random', 'W2MB'));
	$content_fields = $w2mb_instance->content_fields->getOrderingContentFields();
	foreach ($content_fields AS $content_field) {
		$ordering[$content_field->slug] = $content_field->name;
	}
	$ordering = apply_filters('w2mb_default_orderby_options', $ordering);
	$ordering_items = array();
	foreach ($ordering AS $field_slug=>$field_name) {
		$ordering_items[] = array('value' => $field_slug, 'label' => $field_name);
	}
	
	return $ordering_items;
}

function w2mb_terms_checklist($post_id) {
	if ($terms = get_categories(array('taxonomy' => W2MB_CATEGORIES_TAX, 'pad_counts' => true, 'hide_empty' => false, 'parent' => 0))) {
		$checked_categories_ids = array();
		$checked_categories = wp_get_object_terms($post_id, W2MB_CATEGORIES_TAX);
		foreach ($checked_categories AS $term)
			$checked_categories_ids[] = $term->term_id;

		echo '<ul id="w2mb-categorychecklist" class="w2mb-categorychecklist">';
		foreach ($terms AS $term) {
			$classes = '';
			$checked = '';
			if (in_array($term->term_id, $checked_categories_ids)) {
				$checked = 'checked';
			}
			
			if (defined('W2MB_EXPANDED_CATEGORIES_TREE') && W2MB_EXPANDED_CATEGORIES_TREE) {
				$classes .= 'active ';
			}
				
			echo '<li id="' . esc_attr(W2MB_CATEGORIES_TAX) . '-' . esc_attr($term->term_id) . '" class="' . esc_attr($classes) . '">';
			echo '<label class="selectit"><input type="checkbox" ' . $checked . ' id="in-' . esc_attr(W2MB_CATEGORIES_TAX) . '-' . esc_attr($term->term_id) . '" name="tax_input[' . esc_attr(W2MB_CATEGORIES_TAX) . '][]" value="' . esc_attr($term->term_id) . '"> ' . $term->name . '</label>';
			echo _w2mb_terms_checklist($term->term_id, $checked_categories_ids);
			echo '</li>';
		}
		echo '</ul>';
	}
}
function _w2mb_terms_checklist($parent = 0, $checked_categories_ids = array()) {
	$html = '';
	if ($terms = get_categories(array('taxonomy' => W2MB_CATEGORIES_TAX, 'pad_counts' => true, 'hide_empty' => false, 'parent' => $parent))) {
		$html .= '<ul class="children">';
		foreach ($terms AS $term) {
			$checked = '';
			if (in_array($term->term_id, $checked_categories_ids)) {
				$checked = 'checked';
			}
			
			$classes = '';
			if (defined('W2MB_EXPANDED_CATEGORIES_TREE') && W2MB_EXPANDED_CATEGORIES_TREE) {
				$classes .= 'active ';
			}

			$html .= '<li id="' . esc_attr(W2MB_CATEGORIES_TAX) . '-' . esc_attr($term->term_id) . '" class="' . esc_attr($classes) . '">';
			$html .= '<label class="selectit"><input type="checkbox" ' . $checked . ' id="in-' . esc_attr(W2MB_CATEGORIES_TAX) . '-' . esc_attr($term->term_id) . '" name="tax_input[' . esc_attr(W2MB_CATEGORIES_TAX) . '][]" value="' . esc_attr($term->term_id) . '"> ' . $term->name . '</label>';
			$html .= _w2mb_terms_checklist($term->term_id, $checked_categories_ids);
			$html .= '</li>';
		}
		$html .= '</ul>';
	}
	return $html;
}

function w2mb_tags_selectbox($post_id) {
	$terms = get_categories(array('taxonomy' => W2MB_TAGS_TAX, 'pad_counts' => true, 'hide_empty' => false));
	$checked_tags_ids = array();
	$checked_tags_names = array();
	$checked_tags = wp_get_object_terms($post_id, W2MB_TAGS_TAX);
	foreach ($checked_tags AS $term) {
		$checked_tags_ids[] = $term->term_id;
		$checked_tags_names[] = $term->name;
	}

	echo '<select name="' . esc_attr(W2MB_TAGS_TAX) . '[]" multiple="multiple" class="w2mb-tokenizer">';
	foreach ($terms AS $term) {
		$checked = '';
		if (in_array($term->term_id, $checked_tags_ids))
			$checked = 'selected';
		echo '<option value="' . esc_attr($term->name) . '" ' . $checked . '>' . $term->name . '</option>';
	}
	echo '</select>';
}

function w2mb_getTermIconUrl($term_id) {
	$term = get_term($term_id);

	if (!is_wp_error($term)) {
		if ($term->taxonomy == W2MB_CATEGORIES_TAX && ($category_icon = w2mb_getCategoryIconFile($term_id))) {
			return W2MB_CATEGORIES_ICONS_URL . $category_icon;
		}
		if ($term->taxonomy == W2MB_LOCATIONS_TAX && ($location_icon = w2mb_getLocationIconFile($term_id))) {
			return W2MB_LOCATIONS_ICONS_URL . $location_icon;
		}
	}
}

function w2mb_getDefaultTermIconUrl($tax) {
	if ($tax == W2MB_CATEGORIES_TAX) {
		return W2MB_CATEGORIES_ICONS_URL . 'search.png';
	}
	if ($tax == W2MB_LOCATIONS_TAX) {
		return W2MB_LOCATIONS_ICONS_URL . 'icon1.png';
	}
}

function w2mb_show_404() {
	status_header(404);
	nocache_headers();
	include(get_404_template());
	exit;
}


if (!function_exists('w2mb_renderPaginator')) {
	function w2mb_renderPaginator($query) {
		global $w2mb_instance;

		if (get_class($query) == 'WP_Query') {
			if (get_query_var('page'))
				$paged = get_query_var('page');
			elseif (get_query_var('paged'))
				$paged = get_query_var('paged');
			else
				$paged = 1;

			$total_pages = $query->max_num_pages;
			$total_lines = ceil($total_pages/10);
		
			if ($total_pages > 1){
				$current_page = max(1, $paged);
				$current_line = floor(($current_page-1)/10) + 1;
		
				$previous_page = $current_page - 1;
				$next_page = $current_page + 1;
				$previous_line_page = floor(($current_page-1)/10)*10;
				$next_line_page = ceil($current_page/10)*10 + 1;
				
				echo '<div class="w2mb-pagination-wrapper">';
				echo '<ul class="w2mb-pagination">';
				if ($total_pages > 10 && $current_page > 10)
					echo '<li class="w2mb-inactive previous_line"><a href="' . get_pagenum_link($previous_line_page) . '" title="' . esc_attr__('Previous Line', 'W2MB') . '" data-page=' . esc_attr($previous_line_page) . '><<</a></li>' ;
			
				if ($total_pages > 3 && $current_page > 1)
					echo '<li class="w2mb-inactive previous"><a href="' . get_pagenum_link($previous_page) . '" title="' . esc_attr__('Previous Page', 'W2MB') . '" data-page=' . esc_attr($previous_page) . '><</i></a></li>' ;
			
				$count = ($current_line-1)*10;
				$end = ($total_pages < $current_line*10) ? $total_pages : $current_line*10;
				while ($count < $end) {
					$count = $count + 1;
					if ($count == $current_page)
						echo '<li class="w2mb-active"><a href="' . get_pagenum_link($count) . '">' . $count . '</a></li>' ;
					else
						echo '<li class="w2mb-inactive"><a href="' . get_pagenum_link($count) . '" data-page=' . esc_attr($count) . '>' . $count . '</a></li>' ;
				}
			
				if ($total_pages > 3 && $current_page < $total_pages)
					echo '<li class="w2mb-inactive next"><a href="' . get_pagenum_link($next_page) . '" title="' . esc_attr__('Next Page', 'W2MB') . '" data-page=' . esc_attr($next_page) . '>></i></a></li>' ;
			
				if ($total_pages > 10 && $current_line < $total_lines)
					echo '<li class="w2mb-inactive next_line"><a href="' . get_pagenum_link($next_line_page) . '" title="' . esc_attr__('Next Line', 'W2MB') . '" data-page=' . esc_attr($next_line_page) . '>>></a></li>' ;
			
				echo '</ul>';
				echo '</div>';
			}
		}
	}
}

function w2mb_hintMessage($message, $placement = 'auto', $return = false) {
	$out = '<a class="w2mb-hint-icon" href="javascript:void(0);" data-content="' . esc_attr($message) . '" data-html="true" rel="popover" data-placement="' . esc_attr($placement) . '" data-trigger="hover"></a>';
	if ($return) {
		return $out;
	} else {
		echo $out;
	}
}

?>