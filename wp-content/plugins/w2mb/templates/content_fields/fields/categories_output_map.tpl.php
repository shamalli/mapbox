<?php
if (has_term('', W2MB_CATEGORIES_TAX, $listing->post->ID)) {
	$terms = get_the_terms($listing->post->ID, W2MB_CATEGORIES_TAX);
	$categories = array();
	foreach ($terms as $term) {
		$categories[] = $term->name;
	}
	echo implode(", ", $categories);
}
?>