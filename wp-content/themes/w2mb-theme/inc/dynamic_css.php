<?php if (wdt_get_option('body_font')): ?>
body {
	font-family: '<?php echo wdt_get_option('body_font'); ?>', sans-serif;
}
<?php endif; ?>
<?php if (wdt_get_option('body_font_size')): ?>
body {
	font-size: <?php echo wdt_get_option('body_font_size'); ?>px;
}
<?php endif; ?>
<?php if (wdt_get_option('nav_menu_font')): ?>
.main-navigation ul li a {
	font-family: '<?php echo wdt_get_option('nav_menu_font'); ?>', sans-serif;
}
<?php endif; ?>
<?php if (wdt_get_option('nav_menu_font_size')): ?>
.main-navigation ul li a {
	font-size: <?php echo wdt_get_option('nav_menu_font_size'); ?>px;
}
<?php endif; ?>
<?php if (wdt_get_option('nav_menu_paddings')): ?>
.main-navigation ul li a {
	padding-left: <?php echo wdt_get_option('nav_menu_paddings'); ?>px;
	padding-right: <?php echo wdt_get_option('nav_menu_paddings'); ?>px;
}
<?php endif; ?>
<?php if (wdt_get_option('headings_font')): ?>
h1, .w2mb-content h1,
h2, .w2mb-content h2,
h3, .w2mb-content h3,
h4, .w2mb-content h4,
h5, .w2mb-content h5,
h6, .w2mb-content h6 {
	font-family: '<?php echo wdt_get_option('headings_font'); ?>', sans-serif;
}
<?php endif; ?>
<?php if (wdt_get_option('header_background')): ?>
.site-header,
.main-navigation ul ul {
	background-color: <?php echo wdt_get_option('header_background'); ?>;
}
<?php endif; ?>
<?php if (wdt_get_option('header_text_color')): ?>
.site-header,
.main-navigation,
.main-navigation ul li a {
	color: <?php echo wdt_get_option('header_text_color'); ?>;
}
<?php endif; ?>
<?php if (wdt_get_option('page_header_paddings')): ?>
.page-header {
	padding: <?php echo wdt_get_option('page_header_paddings'); ?>px 0;
}
<?php endif; ?>
<?php if (wdt_get_option('page_header_background')): ?>
.page-header {
	background-color: <?php echo wdt_get_option('page_header_background'); ?>;
}
<?php endif; ?>
<?php if (wdt_get_option('page_header_text_color')): ?>
.page-header,
.page-header h1,
.page-header a,
.page-header a:hover,
.page-header a:focus,
.page-header a:visited {
	color: <?php echo wdt_get_option('page_header_text_color'); ?>;
}
<?php endif; ?>
<?php if (wdt_get_option('links_color')): ?>
a,
a:focus,
a:visited,
.main-navigation li.current-menu-item > a,
.main-navigation li.current_page_item > a {
	color: <?php echo wdt_get_option('links_color'); ?>;
}
<?php endif; ?>
<?php if (wdt_get_option('links_color_hover')): ?>
a:hover,
.main-navigation li:hover > a {
	color: <?php echo wdt_get_option('links_color_hover'); ?>;
}
<?php endif; ?>
<?php if (wdt_get_option('buttons_color')): ?>
button,
a.button,
input[type="button"],
input[type="reset"],
input[type="submit"],
.woocommerce #respond input#submit,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button {
	background-color: <?php echo wdt_get_option('buttons_color'); ?>;
	color: <?php echo wdt_get_option('buttons_color_text'); ?>;
}
<?php endif; ?>
<?php if (wdt_get_option('buttons_color_hover')): ?>
button:hover,
a.button:hover,
input[type="button"]:hover,
input[type="reset"]:hover,
input[type="submit"]:hover,
.woocommerce #respond input#submit:hover,
.woocommerce a.button:hover,
.woocommerce button.button:hover,
.woocommerce input.button:hover {
	background-color: <?php echo wdt_get_option('buttons_color_hover'); ?>;
	color: <?php echo wdt_get_option('buttons_color_text'); ?>;
}
<?php endif; ?>

<?php if (wdt_get_option('site_branding_width')): ?>
.site-branding {
	max-width: <?php echo wdt_get_option('site_branding_width'); ?>px;
}
<?php endif; ?>