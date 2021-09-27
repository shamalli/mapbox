<?php if (count($search_field->content_field->selection_items)): ?>
<?php if ($columns == 1) $col_md = 6; else $col_md = 4; ?>
<div class="w2mb-row w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->type); ?> w2mb-field-search-block-<?php echo esc_attr($search_form_id); ?> w2mb-field-search-block-<?php echo esc_attr($search_field->content_field->id); ?>_<?php echo esc_attr($search_form_id); ?>">
	<div class="w2mb-col-md-12">
		<label><?php echo $search_field->content_field->name; ?></label>
	</div>
	<?php
	if ($search_field->search_input_mode == 'checkboxes' || $search_field->search_input_mode =='radiobutton'):
		$i = 1;
		while ($i <= ($columns+1)): ?>
		<div class="w2mb-col-md-<?php echo esc_attr($col_md); ?> w2mb-form-group">
			<?php $j = 1; ?>
			<?php foreach ($search_field->content_field->selection_items AS $key=>$item): ?>
			<?php if ($i == $j): ?>
			<div class="<?php if ($search_field->search_input_mode =='checkboxes'): ?>w2mb-checkbox<?php elseif ($search_field->search_input_mode =='radiobutton'): ?>w2mb-radio<?php endif; ?>">
				<label>
					<?php if ($search_field->search_input_mode =='checkboxes'): ?>
					<input type="checkbox" name="field_<?php echo esc_attr($search_field->content_field->slug); ?>[]" value="<?php echo esc_attr($key); ?>" <?php if (in_array((string)$key, $search_field->value, true)) echo 'checked'; ?> />
					<?php elseif ($search_field->search_input_mode =='radiobutton'): ?>
					<input type="radio" name="field_<?php echo esc_attr($search_field->content_field->slug); ?>" value="<?php echo esc_attr($key); ?>" <?php if (in_array((string)$key, $search_field->value, true)) echo 'checked'; ?> />
					<?php endif; ?>
					<?php echo $item; ?><?php if ($search_field->items_count && $key !== ""): if (isset($items_count_array[$key])) echo " (".$items_count_array[$key].")"; else echo " (0)"; endif; ?>
				</label>
			</div>
			<?php endif; ?>
			<?php $j++; ?>
			<?php if ($j > ($columns+1)) $j = 1; ?>
			<?php endforeach; ?>
		</div>
		<?php $i++; ?>
		<?php endwhile; ?>
	<?php elseif ($search_field->search_input_mode == 'selectbox'): ?>
	<div class="w2mb-col-md-12 w2mb-form-group">
		<select name="field_<?php echo esc_attr($search_field->content_field->slug); ?>" class="w2mb-form-control w2mb-width-100">
			<option value="" <?php if (!$search_field->value) echo 'selected'; ?>><?php printf(esc_html__('- Select %s -', 'W2MB'), $search_field->content_field->name); ?></option>
			<?php foreach ($search_field->content_field->selection_items AS $key=>$item): ?>
			<option value="<?php echo esc_attr($key); ?>" <?php if (in_array((string)$key, $search_field->value, true)) echo 'selected'; ?>><?php echo $item; ?><?php if ($search_field->items_count): if (isset($items_count_array[$key])) echo " (".$items_count_array[$key].")"; else echo " (0)"; endif; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>