<?php 

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
function my_theme_enqueue_styles() {
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

}

remove_filter('the_content', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');

function rss_post_thumbnail($content) {
	global $post;
	if(has_post_thumbnail($post->ID)) {
		$content = '<p>' . get_the_post_thumbnail($post->ID,array(32,32)) .'</p>' . get_the_content();
	}
	return $content;
}
add_filter('the_excerpt_rss', 'rss_post_thumbnail');
add_filter('the_content_feed', 'rss_post_thumbnail');

add_action('wp_enqueue_scripts', 'enqueue_scripts_styles');
function enqueue_scripts_styles() {
	wp_register_script('w2mb_theme_script', get_stylesheet_directory_uri() . '/js.js', array('jquery'));
	wp_enqueue_script('w2mb_theme_script');
}

function remove_envato_top_bar() {
	echo "<script>
if (document.referrer.indexOf('preview.codecanyon.net') != -1) top.location.replace(self.location.href);
</script>";
}
// it syddenly does not work (september 2020)
//add_action('wp_head', 'remove_envato_top_bar', 1);

function add_google_analytics() {
	if (strpos($_SERVER['SERVER_NAME'], 'salephpscripts.com') !== false) {
		echo "
<!-- Google Analytics -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-2809045-1', 'auto');
ga('send', 'pageview');
</script>
<!-- End Google Analytics -->
";
	}
}
add_action('wp_head', 'add_google_analytics', 9999);
add_action('admin_head', 'add_google_analytics', 9999);


/* function download_free_link() {
	echo "<a class='button w2mb-download-free' href='https://wordpress.org/plugins/web-directory-free/'>Download Free</a>";
}
add_action('wdt_before_navigation', 'download_free_link', 11); */

function buy_plugin_link() {
	/* echo "<div class='w2mb-codecanyon-link-block'>";
	echo "<a class='w2mb-codecanyon-link-text' href='https://codecanyon.net/item/web-20-directory-plugin-for-wordpress/6463373?ref=Shamalli'>Purchase plugin</a>";
	echo "<a class='w2mb-codecanyon-link-img' href='https://codecanyon.net/item/web-20-directory-plugin-for-wordpress/6463373?ref=Shamalli'>";
	echo "<img src='" . get_stylesheet_directory_uri() . '/buy_plugin.jpg' . "' />";
	echo "</a>";
	echo "</div>"; */
	echo "<a class='button w2mb-codecanyon-button' href='https://1.envato.market/km9P0'><span class='w2mb-purchase-icon fa fa-shopping-cart '></span> Purchase Plugin</a>";
}
add_action('wdt_before_navigation', 'buy_plugin_link');

function submit_listing() {
	if (function_exists('w2mb_submitUrl')) {
		echo "<a class='button w2mb-submit-listing-button' href='" . w2mb_submitUrl() . "'>Submit Listing</a>";
	}
}
add_action('wdt_before_navigation', 'submit_listing', 12);

?>