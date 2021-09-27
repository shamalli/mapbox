<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php
	if ($field_id)
		esc_html_e('Edit content field', 'W2MB');
	else
		esc_html_e('Create new content field', 'W2MB');
	?>
</h2>
<?php
if ($field_id && $content_field->isConfigurationPage())
	printf('<a href="?page=%s&action=%s&field_id=%d">' . esc_html__('Configure', 'W2MB') . '</a>', $_GET['page'], 'configure', $field_id);
?>

<?php
if ($field_id && $content_field->isSearchConfigurationPage())
	printf('<a href="?page=%s&action=%s&field_id=%d">' . esc_html__('Configure search', 'W2MB') . '</a>', $_GET['page'], 'configure_search', $field_id);
?>

<?php if ($content_field->is_core_field): ?>
<p class="description"><?php esc_html_e("You can't select assigned categories for core fields such as content, excerpt, categories, tags and addresses", 'W2MB'); ?></p>
<?php endif; ?>

<script>
	(function($) {
		"use strict";
	
		$(function() {
			$("#content_field_name").keyup(function() {
				$("#content_field_slug").val(w2mb_make_slug($("#content_field_name").val()));
			});
	
			<?php if (!$content_field->is_core_field): ?>
			$("#type").change(function() {
				if (
					<?php
					foreach ($content_fields->fields_types_names AS $content_field_type=>$content_field_name){
						$field_class_name = 'w2mb_content_field_' . $content_field_type;
						if (class_exists($field_class_name)) {
							$_content_field = new $field_class_name;
							if (!$_content_field->canBeOrdered()) {
					?>
					$(this).val() == '<?php echo esc_js($content_field_type); ?>' ||
					<?php
							}
						}
					} ?>
				'x'=='y')
					$("#is_ordered_block").hide();
				else
					$("#is_ordered_block").show();
	
				if (
					<?php
					foreach ($content_fields->fields_types_names AS $content_field_type=>$content_field_name){
						$field_class_name = 'w2mb_content_field_' . $content_field_type;
						if (class_exists($field_class_name)) {
							$_content_field = new $field_class_name;
							if (!$_content_field->canBeRequired()) {
					?>
					$(this).val() == '<?php echo esc_js($content_field_type); ?>' ||
					<?php
							}
						}
					} ?>
				'x'=='y')
					$("#is_required_block").hide();
				else
					$("#is_required_block").show();
			});
			<?php endif; ?>
		});
	})(jQuery);
</script>

<form method="POST" action="">
	<?php wp_nonce_field(W2MB_PATH, 'w2mb_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Field name', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="name"
						id="content_field_name"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->name); ?>" />
					<?php w2mb_wpmlTranslationCompleteNotice(); ?>
				</td>
			</tr>
			<?php if ($content_field->isSlug()) :?>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Field slug', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="slug"
						id="content_field_slug"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->slug); ?>" />
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Hide name', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="is_hide_name"
						type="checkbox"
						value="1"
						<?php checked($content_field->is_hide_name); ?> />
					<p class="description"><?php esc_html_e("Hide field name at the frontend? Only icon will be shown.", 'W2MB'); ?></p>
				</td>
			</tr>
			<?php if (!$content_field->is_core_field): ?>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Only admins can see what was entered', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="for_admin_only"
						type="checkbox"
						value="1"
						<?php checked($content_field->for_admin_only); ?> />
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Field description', 'W2MB'); ?></label>
				</th>
				<td>
					<textarea
						name="description"
						cols="60"
						rows="4" ><?php echo esc_textarea($content_field->description); ?></textarea>
					<?php w2mb_wpmlTranslationCompleteNotice(); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Icon image', 'W2MB'); ?></label>
				</th>
				<td>
					<span class="<?php if (!$content_field->icon_image): ?>w2mb-display-none<?php endif; ?> w2mb-icon-tag <?php if ($content_field->icon_image): ?>w2mb-fa <?php echo esc_attr($content_field->icon_image); ?><?php endif; ?>"></span>
					<input type="hidden" name="icon_image" id="w2mb-icon-image" value="<?php echo esc_attr($content_field->icon_image); ?>">
					<div>
						<a class="w2mb-select-fa-icon" href="javascript: void(0);" data-icon-tag="w2mb-icon-tag" data-icon-image-name="w2mb-icon-image"><?php echo esc_js(esc_html__('Select field icon', 'W2MB')); ?></a>
					</div>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Field type', 'W2MB'); ?><span class="w2mb-red-asterisk">*</span></label>
				</th>
				<td>
					<select name="type" id="type" <?php disabled($content_field->is_core_field); ?>>
						<option value=""><?php esc_html_e('- Select field type -', 'W2MB'); ?></option>
						<?php if ($content_field->is_core_field) :?>
						<option value="excerpt" <?php selected($content_field->type, 'excerpt'); ?> ><?php echo $fields_types_names['excerpt']; ?></option>
						<option value="content" <?php selected($content_field->type, 'content'); ?> ><?php echo $fields_types_names['content']; ?></option>
						<option value="categories" <?php selected($content_field->type, 'categories'); ?> ><?php echo $fields_types_names['categories']; ?></option>
						<option value="tags" <?php selected($content_field->type, 'tags'); ?> ><?php echo $fields_types_names['tags']; ?></option>
						<option value="address" <?php selected($content_field->type, 'address'); ?> ><?php echo $fields_types_names['address']; ?></option>
						<?php endif; ?>
						<option value="string" <?php selected($content_field->type, 'string'); ?> ><?php echo $fields_types_names['string']; ?></option>
						<option value="phone" <?php selected($content_field->type, 'phone'); ?> ><?php echo $fields_types_names['phone']; ?></option>
						<option value="textarea" <?php selected($content_field->type, 'textarea'); ?> ><?php echo $fields_types_names['textarea']; ?></option>
						<option value="number" <?php selected($content_field->type, 'number'); ?> ><?php echo $fields_types_names['number']; ?></option>
						<option value="select" <?php selected($content_field->type, 'select'); ?> ><?php echo $fields_types_names['select']; ?></option>
						<option value="radio" <?php selected($content_field->type, 'radio'); ?> ><?php echo $fields_types_names['radio']; ?></option>
						<option value="checkbox" <?php selected($content_field->type, 'checkbox'); ?> ><?php echo $fields_types_names['checkbox']; ?></option>
						<option value="website" <?php selected($content_field->type, 'website'); ?> ><?php echo $fields_types_names['website']; ?></option>
						<option value="email" <?php selected($content_field->type, 'email'); ?> ><?php echo $fields_types_names['email']; ?></option>
						<option value="datetime" <?php selected($content_field->type, 'datetime'); ?> ><?php echo $fields_types_names['datetime']; ?></option>
						<option value="price" <?php selected($content_field->type, 'price'); ?> ><?php echo $fields_types_names['price']; ?></option>
						<option value="hours" <?php selected($content_field->type, 'hours'); ?> ><?php echo $fields_types_names['hours']; ?></option>
						<option value="fileupload" <?php selected($content_field->type, 'fileupload'); ?> ><?php echo $fields_types_names['fileupload']; ?></option>
					</select>
					<?php if ($content_field->is_core_field): ?>
					<p class="description"><?php esc_html_e("You can't change the type of core fields", 'W2MB'); ?></p>
					<?php endif; ?>
				</td>
			</tr>

			<tr id="is_required_block" class="<?php if (!$content_field->canBeRequired()): ?>w2mb-display-none<?php endif; ?>">
				<th scope="row">
					<label><?php esc_html_e('Is this field required?', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="is_required"
						type="checkbox"
						value="1"
						<?php checked($content_field->is_required); ?> />
				</td>
			</tr>
			<tr id="is_ordered_block" class="<?php if (!$content_field->canBeOrdered()): ?>w2mb-display-none<?php endif; ?>">
				<th scope="row">
					<label><?php esc_html_e('Order by field', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="is_ordered"
						type="checkbox"
						value="1"
						<?php checked($content_field->is_ordered); ?> />
					<p class="description"><?php esc_html_e("It is possible to order listings by this field", 'W2MB'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('On listings sidebar', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="on_listing_sidebar"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_listing_sidebar); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('On listing page', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="on_listing_page"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_listing_page); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('In map marker InfoWindow', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="on_map"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_map); ?> />
				</td>
			</tr>
			
			<script>
				(function($) {
					"use strict";
	
					$(function() {
						<?php if (!$content_field->is_core_field): ?>
						$("#type").change(function() {
							if (
								<?php 
								foreach ($content_fields->fields_types_names AS $content_field_type=>$content_field_name){
									$field_class_name = 'w2mb_content_field_' . $content_field_type;
									if (class_exists($field_class_name)) {
										$_content_field = new $field_class_name;
										if (!$_content_field->canBeSearched()) {
								?>
								$(this).val() == '<?php echo esc_js($content_field_type); ?>' ||
								<?php
										}
									}
								} ?>
							$(this).val() === '')
								$(".can_be_searched_block").hide();
							else
								$(".can_be_searched_block").show();
						});
						$(document).on('click', '#on_search_form', function() {
							if ($(this).is(':checked'))
								$('input[name="advanced_search_form"]').removeAttr('disabled');
							else 
								$('input[name="advanced_search_form"]').attr('disabled', true);
						});
						<?php endif; ?>
					});
				})(jQuery);
			</script>
			<tr class="can_be_searched_block <?php if (!$content_field->canBeSearched()): ?>w2mb-display-none<?php endif; ?>">
				<th scope="row">
					<label><?php esc_html_e('Search by this field', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						id="on_search_form"
						name="on_search_form"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_search_form); ?> />
					<p class="description"><?php esc_html_e("If you choose specific categories below, this field will be shown only when any of these categories selected in a search form.", 'W2MB'); ?></p>
				</td>
			</tr>
			<tr class="can_be_searched_block <?php if (!$content_field->canBeSearched()): ?>w2mb-display-none<?php endif; ?>">
				<th scope="row">
					<label><?php esc_html_e('On advanced search panel', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="advanced_search_form"
						type="checkbox"
						value="1"
						<?php checked($content_field->advanced_search_form); ?>
						<?php disabled(!$content_field->on_search_form)?> />
					<p class="description"><?php esc_html_e("It is in Less/More filters section.", 'W2MB'); ?></p>
				</td>
			</tr>
			
			<?php do_action('w2mb_content_field_html', $content_field); ?>
			
			<?php if ($content_field->isCategories()): ?>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Assigned categories', 'W2MB'); ?></label>
					<?php echo w2mb_get_wpml_dependent_option_description(); ?>
				</th>
				<td>
					<?php w2mb_termsSelectList('categories', W2MB_CATEGORIES_TAX, $content_field->categories); ?>
				</td>
			</tr>
			<?php endif; ?>
			
		</tbody>
	</table>
	
	<?php
	if ($field_id)
		submit_button(esc_html__('Save changes', 'W2MB'));
	else
		submit_button(esc_html__('Create content field', 'W2MB'));
	?>
</form>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>