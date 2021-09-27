<tr class="form-field hide-if-no-js">
	<th scope="row" valign="top"><label for="description"><?php print esc_html_e('Marker Image', 'W2MB') ?></label></th>
	<td>
		<?php echo $w2mb_instance->categories_manager->choose_marker_image($term->term_id); ?>
		<p class="description"><?php esc_html_e('Associate an image to this category', 'W2MB'); ?></p>
	</td>
</tr>