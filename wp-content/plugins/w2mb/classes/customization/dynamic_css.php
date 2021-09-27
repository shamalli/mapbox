<?php if (w2mb_get_dynamic_option('w2mb_listing_title_font')): ?>
header.w2mb-listing-header h2 {
	font-size: <?php echo w2mb_get_dynamic_option('w2mb_listing_title_font'); ?>px;
}
<?php endif; ?>

<?php if (w2mb_get_dynamic_option('w2mb_links_color')): ?>
div.w2mb-content a,
div.w2mb-content a:visited,
div.w2mb-content a:focus,
div.w2mb-content .w2mb-pagination > li > a,
div.w2mb-content .w2mb-pagination > li > a:visited,
div.w2mb-content .w2mb-pagination > li > a:focus,
div.w2mb-content .w2mb-btn-default, div.w2mb-content div.w2mb-btn-default:visited, div.w2mb-content .w2mb-btn-default:focus,
div.w2mb-content.w2mb-terms-menu .w2mb-categories-root a,
div.w2mb-content.w2mb-terms-menu .w2mb-categories-root a:visited,
div.w2mb-content.w2mb-terms-menu .w2mb-categories-root a:focus,
div.w2mb-content.w2mb-terms-menu .w2mb-locations-root a,
div.w2mb-content.w2mb-terms-menu .w2mb-locations-root a:visited,
div.w2mb-content.w2mb-terms-menu .w2mb-locations-root a:focus {
	color: <?php echo w2mb_get_dynamic_option('w2mb_links_color'); ?>;
}
<?php endif; ?>
<?php if (w2mb_get_dynamic_option('w2mb_links_hover_color')): ?>
div.w2mb-content a:hover,
div.w2mb-content .w2mb-pagination > li > a:hover,
div.w2mb-content.w2mb-terms-menu .w2mb-categories-root a:hover,
div.w2mb-content.w2mb-terms-menu .w2mb-locations-root a:hover {
	color: <?php echo w2mb_get_dynamic_option('w2mb_links_hover_color'); ?>;
}
<?php endif; ?>

<?php if (w2mb_get_dynamic_option('w2mb_button_1_color') && w2mb_get_dynamic_option('w2mb_button_2_color') && w2mb_get_dynamic_option('w2mb_button_text_color')): ?>
<?php if (!w2mb_get_dynamic_option('w2mb_button_gradient')): ?>
div.w2mb-content .w2mb-btn-primary,
div.w2mb-content a.w2mb-btn-primary,
div.w2mb-content input[type="submit"],
div.w2mb-content input[type="button"],
div.w2mb-content .w2mb-btn-primary:visited,
div.w2mb-content a.w2mb-btn-primary:visited,
div.w2mb-content input[type="submit"]:visited,
div.w2mb-content input[type="button"]:visited,
div.w2mb-content .w2mb-btn-primary:focus,
div.w2mb-content a.w2mb-btn-primary:focus,
div.w2mb-content input[type="submit"]:focus,
div.w2mb-content input[type="button"]:focus,
div.w2mb-content .w2mb-btn-primary:disabled,
div.w2mb-content a.w2mb-btn-primary:disabled,
div.w2mb-content .w2mb-btn-primary:disabled:focus,
div.w2mb-content a.w2mb-btn-primary:disabled:focus,
div.w2mb-content .w2mb-btn-primary:disabled:hover,
div.w2mb-content a.w2mb-btn-primary:disabled:hover,
form.w2mb-content .w2mb-btn-primary,
form.w2mb-content a.w2mb-btn-primary,
form.w2mb-content input[type="submit"],
form.w2mb-content input[type="button"],
form.w2mb-content .w2mb-btn-primary:visited,
form.w2mb-content a.w2mb-btn-primary:visited,
form.w2mb-content input[type="submit"]:visited,
form.w2mb-content input[type="button"]:visited,
form.w2mb-content .w2mb-btn-primary:focus,
form.w2mb-content a.w2mb-btn-primary:focus,
form.w2mb-content input[type="submit"]:focus,
form.w2mb-content input[type="button"]:focus,
form.w2mb-content .w2mb-btn-primary:disabled,
form.w2mb-content a.w2mb-btn-primary:disabled,
form.w2mb-content .w2mb-btn-primary:disabled:focus,
form.w2mb-content a.w2mb-btn-primary:disabled:focus,
form.w2mb-content .w2mb-btn-primary:disabled:hover,
form.w2mb-content a.w2mb-btn-primary:disabled:hover,
div.w2mb-content .wpcf7-form .wpcf7-submit,
div.w2mb-content .wpcf7-form .wpcf7-submit:visited,
div.w2mb-content .wpcf7-form .wpcf7-submit:focus {
	color: <?php echo w2mb_get_dynamic_option('w2mb_button_text_color'); ?>;
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?>;
	background-image: none;
	border-color: <?php echo w2mb_adjust_brightness(w2mb_get_dynamic_option('w2mb_button_1_color'), -20); ?>;
}
div.w2mb-content .w2mb-btn-primary:hover,
div.w2mb-content a.w2mb-btn-primary:hover,
div.w2mb-content input[type="submit"]:hover,
div.w2mb-content input[type="button"]:hover,
form.w2mb-content .w2mb-btn-primary:hover,
form.w2mb-content a.w2mb-btn-primary:hover,
form.w2mb-content input[type="submit"]:hover,
form.w2mb-content input[type="button"]:hover,
div.w2mb-content .mapboxgl-ctrl button:not(:disabled):hover,
div.w2mb-content .wpcf7-form .wpcf7-submit:hover {
	color: <?php echo w2mb_get_dynamic_option('w2mb_button_text_color'); ?>;
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?>;
	background-image: none;
	border-color: <?php echo w2mb_adjust_brightness(w2mb_get_dynamic_option('w2mb_button_2_color'), -20); ?>;
	text-decoration: none;
}
<?php else: ?>
div.w2mb-content .w2mb-btn-primary,
div.w2mb-content a.w2mb-btn-primary,
div.w2mb-content input[type="submit"],
div.w2mb-content input[type="button"],
div.w2mb-content .w2mb-btn-primary:visited,
div.w2mb-content a.w2mb-btn-primary:visited,
div.w2mb-content input[type="submit"]:visited,
div.w2mb-content input[type="button"]:visited,
div.w2mb-content .w2mb-btn-primary:focus,
div.w2mb-content a.w2mb-btn-primary:focus,
div.w2mb-content input[type="submit"]:focus,
div.w2mb-content input[type="button"]:focus,
div.w2mb-content .w2mb-btn-primary:disabled,
div.w2mb-content a.w2mb-btn-primary:disabled,
div.w2mb-content .w2mb-btn-primary:disabled:focus,
div.w2mb-content a.w2mb-btn-primary:disabled:focus,
form.w2mb-content .w2mb-btn-primary,
form.w2mb-content a.w2mb-btn-primary,
form.w2mb-content input[type="submit"],
form.w2mb-content input[type="button"],
form.w2mb-content .w2mb-btn-primary:visited,
form.w2mb-content a.w2mb-btn-primary:visited,
form.w2mb-content input[type="submit"]:visited,
form.w2mb-content input[type="button"]:visited,
form.w2mb-content .w2mb-btn-primary:focus,
form.w2mb-content a.w2mb-btn-primary:focus,
form.w2mb-content input[type="submit"]:focus,
form.w2mb-content input[type="button"]:focus,
form.w2mb-content .w2mb-btn-primary:disabled,
form.w2mb-content a.w2mb-btn-primary:disabled,
form.w2mb-content .w2mb-btn-primary:disabled:focus,
form.w2mb-content a.w2mb-btn-primary:disabled:focus,
div.w2mb-content .w2mb-listing-frontpanel input[type="button"],
div.w2mb-content .wpcf7-form .wpcf7-submit,
div.w2mb-content .wpcf7-form .wpcf7-submit:visited,
div.w2mb-content .wpcf7-form .wpcf7-submit:focus {
	background: <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> !important;
	background: -moz-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 100%) !important;
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?>), color-stop(100%, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?>)) !important;
	background: -webkit-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 100%) !important;
	background: -o-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 100%) !important;
	background: -ms-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 100%) !important;
	background: linear-gradient(to bottom, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 100%) !important;
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr= <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> , endColorstr= <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> ,GradientType=0 ) !important;
	color: <?php echo w2mb_get_dynamic_option('w2mb_button_text_color'); ?>;
	background-position: center !important;
	border: none;
}
div.w2mb-content .w2mb-btn-primary:hover,
div.w2mb-content a.w2mb-btn-primary:hover,
div.w2mb-content input[type="submit"]:hover,
div.w2mb-content input[type="button"]:hover,
form.w2mb-content .w2mb-btn-primary:hover,
form.w2mb-content a.w2mb-btn-primary:hover,
form.w2mb-content input[type="submit"]:hover,
form.w2mb-content input[type="button"]:hover,
div.w2mb-content .w2mb-listing-frontpanel input[type="button"]:hover,
div.w2mb-content .mapboxgl-ctrl button:not(:disabled):hover,
div.w2mb-content .wpcf7-form .wpcf7-submit:hover {
	background: <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> !important;
	background: -moz-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 100%) !important;
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?>), color-stop(100%, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?>)) !important;
	background: -webkit-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 100%) !important;
	background: -o-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 100%) !important;
	background: -ms-linear-gradient(top, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 100%) !important;
	background: linear-gradient(to bottom, <?php echo w2mb_get_dynamic_option('w2mb_button_2_color'); ?> 0%, <?php echo w2mb_get_dynamic_option('w2mb_button_1_color'); ?> 100%) !important;
	color: <?php echo w2mb_get_dynamic_option('w2mb_button_text_color'); ?>;
	background-position: center !important;
	border: none;
	text-decoration: none;
}
<?php endif; ?>
<?php endif; ?>

<?php if (!w2mb_get_dynamic_option('w2mb_search_overlay')): ?>
.w2mb-search-overlay {
	background: none;
}
<?php endif; ?>
<?php if (w2mb_get_dynamic_option('w2mb_search_bg_color')): ?>
.w2mb-content.w2mb-search-form {
	background: <?php echo w2mb_hex2rgba(w2mb_get_dynamic_option('w2mb_search_bg_color'), w2mb_get_dynamic_option('w2mb_search_bg_opacity')/100); ?>;
}
<?php endif; ?>
<?php if (w2mb_get_dynamic_option('w2mb_search_text_color')): ?>
form.w2mb-content.w2mb-search-form,
form.w2mb-content.w2mb-search-form a,
form.w2mb-content.w2mb-search-form a:hover,
form.w2mb-content.w2mb-search-form a:visited,
form.w2mb-content.w2mb-search-form a:focus,
form.w2mb-content a.w2mb-advanced-search-label,
form.w2mb-content a.w2mb-advanced-search-label:hover,
form.w2mb-content a.w2mb-advanced-search-label:visited,
form.w2mb-content a.w2mb-advanced-search-label:focus {
	color: <?php echo w2mb_get_dynamic_option('w2mb_search_text_color'); ?>;
}
<?php endif; ?>

<?php if (w2mb_get_dynamic_option('w2mb_primary_color') && w2mb_get_dynamic_option('w2mb_secondary_color')): ?>
.w2mb-field-caption .w2mb-field-icon {
	color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-content select:not(.w2mb-week-day-input),
.w2mb-content select:not(.w2mb-week-day-input):focus {
	background-image:
	linear-gradient(50deg, transparent 50%, #FFFFFF 50%),
	linear-gradient(130deg, #FFFFFF 50%, transparent 50%),
	linear-gradient(to right, <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>, <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>) !important;
}
.w2mb-content .w2mb-checkbox .w2mb-control-indicator,
.w2mb-content .w2mb-radio .w2mb-control-indicator {
	border-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-content .w2mb-checkbox label input:checked ~ .w2mb-control-indicator,
.w2mb-content .w2mb-radio label input:checked ~ .w2mb-control-indicator {
	background: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-content .ui-slider.ui-slider-horizontal .ui-widget-header {
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_secondary_color'); ?>;
}
.w2mb-content .ui-slider.ui-widget-content .ui-slider-handle.ui-state-default,
.w2mb-content .ui-slider.ui-widget-content .ui-slider-handle.ui-state-default:focus,
.w2mb-content .ui-slider.ui-widget-content .ui-slider-handle.ui-state-default:active,
.w2mb-content .ui-slider.ui-widget-content .ui-slider-handle.ui-state-focus,
.w2mb-content .ui-slider.ui-widget-content .ui-slider-handle.ui-state-hover {
	border: 1px solid <?php echo w2mb_get_dynamic_option('w2mb_secondary_color'); ?>;
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-content .w2mb-map-info-window-title {
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-content .mapboxgl-popup-content {
	width: <?php echo w2mb_get_dynamic_option('w2mb_map_infowindow_width'); ?>px;
}
.w2mb-content .w2mb-category-label,
.w2mb-content .w2mb-tag-label {
	color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
	border-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
div.w2mb-content .w2mb-pagination > li.w2mb-active > a,
div.w2mb-content .w2mb-pagination > li.w2mb-active > span,
div.w2mb-content .w2mb-pagination > li.w2mb-active > a:hover,
div.w2mb-content .w2mb-pagination > li.w2mb-active > span:hover,
div.w2mb-content .w2mb-pagination > li.w2mb-active > a:focus,
div.w2mb-content .w2mb-pagination > li.w2mb-active > span:focus {
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
	border-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
	color: #FFFFFF;
}
.w2mb-found-listings .w2mb-badge {
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-rating-avgvalue span {
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-content.w2mb-search-map-form .w2mb-search-overlay {
	background-color: <?php echo w2mb_hex2rgba(w2mb_get_dynamic_option('w2mb_primary_color'), 0.8); ?>;
}
.w2mb-field-output-block-string .w2mb-field-phone-content,
.w2mb-field-output-block-website .w2mb-field-content {
	color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
	font-weight: bold;
}
.w2mb-loader:before {
	border-top-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
	border-bottom-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
.w2mb-listing-dialog.ui-dialog .ui-widget-header {
	background-color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
article.w2mb-listing-location-selected {
	background-color: <?php echo w2mb_hex2rgba(w2mb_get_dynamic_option('w2mb_primary_color'), 0.1); ?>;
}
.w2mb-field-checkbox-item-checked span {
	color: <?php echo w2mb_get_dynamic_option('w2mb_primary_color'); ?>;
}
<?php endif; ?>


<?php if (!w2mb_get_dynamic_option('w2mb_100_single_logo_width')): ?>
/* It works with devices width more than 768 pixels. */
@media screen and (min-width: 768px) {
	.w2mb-single-listing-logo-wrap {
		max-width: <?php echo w2mb_get_dynamic_option('w2mb_single_logo_width'); ?>px;
		float: left;
		margin: 0 20px 20px 0;
	}
	.rtl .w2mb-single-listing-logo-wrap {
		float: right;
		margin: 0 0 20px 20px;
	}
	/* temporarily */
	/*.w2mb-single-listing-text-content-wrap {
		margin-left: <?php echo w2mb_get_dynamic_option('w2mb_single_logo_width')+20; ?>px;
	}*/
}
<?php endif; ?>

<?php if (w2mb_get_dynamic_option('w2mb_hide_search_on_map_mobile')): ?>
/* It works with devices width less than 768 pixels. */
@media screen and (max-width: 768px) {
	.w2mb-map-search-form {
		display: none !important;
	}
	.w2mb-maps-canvas.w2mb-sidebar-open {
		margin-left: 0 !important;
		width: 100% !important;
	}
}
<?php endif; ?>

<?php if (w2mb_get_dynamic_option('w2mb_big_slide_bg_mode')): ?>
.w2mb-single-listing-logo-wrap .w2mb-big-slide {
	background-size: <?php echo w2mb_get_dynamic_option('w2mb_big_slide_bg_mode'); ?>;
}
<?php endif; ?>

<?php if (w2mb_get_dynamic_option('w2mb_listing_logo_bg_mode')): ?>
figure.w2mb-listing-logo .w2mb-listing-logo-img {
	background-size: <?php echo w2mb_get_dynamic_option('w2mb_listing_logo_bg_mode'); ?>;
}
<?php endif; ?>

<?php if (w2mb_get_dynamic_option('w2mb_map_marker_size')): ?>
.w2mb-map-marker,
.w2mb-map-marker-empty {
	height: <?php echo w2mb_get_dynamic_option('w2mb_map_marker_size'); ?>px;
	width: <?php echo w2mb_get_dynamic_option('w2mb_map_marker_size'); ?>px;
}
.w2mb-map-marker .w2mb-map-marker-icon {
	<?php if (w2mb_get_dynamic_option('w2mb_map_marker_size') >= 30): ?>
	font-size: <?php echo round(0.55*w2mb_get_dynamic_option('w2mb_map_marker_size')); ?>px !important;
	<?php elseif (w2mb_get_dynamic_option('w2mb_map_marker_size') > 19): ?>
	font-size: <?php echo round(0.40*w2mb_get_dynamic_option('w2mb_map_marker_size')); ?>px !important;
	top: -5%;
	<?php else: ?>
	display: none;
	<?php endif; ?>
}
<?php endif; ?>