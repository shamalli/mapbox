<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2><?php esc_html_e('CSV Import'); ?></h2>

<p class="description"><?php esc_html_e('On this second step collate CSV headers of columns with existing listings fields', 'W2MB'); ?></p>

<form method="POST" action="">
	<input type="hidden" name="action" value="import_collate">
	<input type="hidden" name="import_type" value="<?php echo esc_attr($import_type); ?>">
	<input type="hidden" name="csv_file_name" value="<?php echo esc_attr($csv_file_name); ?>">
	<input type="hidden" name="images_dir" value="<?php echo esc_attr($images_dir); ?>">
	<input type="hidden" name="columns_separator" value="<?php echo esc_attr($columns_separator); ?>">
	<input type="hidden" name="values_separator" value="<?php echo esc_attr($values_separator); ?>">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_csv_import_nonce');?>
	
	<h3><?php esc_html_e('Map CSV columns', 'W2MB'); ?></h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<strong><?php esc_html_e('Column name', 'W2MB'); ?></strong>
					<hr />
				</th>
				<td>
					<strong><?php esc_html_e('Map to field', 'W2MB'); ?></strong>
					<hr />
				</td>
			</tr>
			<?php foreach ($headers AS $i=>$column): ?>
			<tr>
				<th scope="row">
					<label><?php echo $column; ?></label>
				</th>
				<td>
					<select name="fields[]">
						<option value=""><?php esc_html_e('- Select listings field -', 'W2MB'); ?></option>
						<?php foreach ($collation_fields AS $key=>$field): ?>
						<option value="<?php echo esc_attr($key); ?>" <?php if ($collated_fields) selected($collated_fields[$i], $key, true); ?>><?php echo $field; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h3><?php esc_html_e('Import settings', 'W2MB'); ?></h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('What to do when category/location/tag was not found', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="if_term_not_found"
						type="radio"
						value="create"
						<?php isset($if_term_not_found) ? checked($if_term_not_found, 'create') : checked(true); ?> />
					<?php esc_html_e('Create new category/location/tag', 'W2MB'); ?>

					<br />

					<input
						name="if_term_not_found"
						type="radio"
						value="skip"
						<?php isset($if_term_not_found) ? checked($if_term_not_found, 'skip') : ''; ?> />
					<?php esc_html_e('Do not do anything', 'W2MB'); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Listings author', 'W2MB'); ?></label>
				</th>
				<td>
					<select name="listings_author">
						<option value="0" <?php isset($listing_author) ? selected($listings_author, 0) : selected(true); ?>><?php esc_html_e('As defined in CSV column'); ?></option>
						<?php foreach ($users AS $user): ?>
						<option value="<?php echo esc_attr($user->ID); ?>" <?php isset($listings_author) ? selected($listings_author, $user->ID) : ''; ?>><?php echo $user->user_login; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Geocode imported listings by address parts', 'W2MB'); ?></label>
					<p class="description">
						<?php esc_html_e("Required when you don't have coordinates to import, but need listings map markers.", 'W2MB'); ?>
						<?php printf(esc_html__('Maps API key must be working! Check geolocation <a href="%s">response</a>.', 'W2MB'), admin_url('admin.php?page=w2mb_debug')); ?>
					</p>
				</th>
				<td>
					<input
						name="do_geocode"
						type="checkbox"
						value="1"
						<?php checked($do_geocode, 1, true); ?> />
				</td>
			</tr>
			<?php if (get_option('w2mb_fsubmit_addon') && get_option('w2mb_claim_functionality')): ?>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Configure imported listings as claimable', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="is_claimable"
						type="checkbox"
						value="1"
						<?php checked($is_claimable, 1, true); ?> />
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
	
	<?php w2mb_renderTemplate('csv_manager/import_instructions.tpl.php'); ?>
	
	<?php submit_button(esc_html__('Import', 'W2MB'), 'primary', 'submit', false); ?>
	&nbsp;&nbsp;&nbsp;
	<?php submit_button(esc_html__('Test import', 'W2MB'), 'secondary', 'tsubmit', false); ?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>