<?php
if (has_term('', W2MB_TAGS_TAX, $listing->post->ID)) {
	$terms = get_the_terms($listing->post->ID, W2MB_TAGS_TAX);
	$tags = array();
	foreach ($terms as $term) {
		$tags[] = $term->name;
	}
	echo implode(", ", $tags);
}
?>