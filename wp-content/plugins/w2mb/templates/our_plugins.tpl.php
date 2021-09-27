<div class="w2mb-our-plugins">
	<h2><?php printf(esc_html__('Please look at our plugins, visit %s', 'W2MB'), '<a href="https://www.salephpscripts.com" target="_blank">salephpscripts.com</a>'); ?></h2>
	<ul>
		<li>
			<a href="https://codecanyon.net/item/web-20-directory-plugin-for-wordpress/6463373" target="_blank"><img src="https://www.salephpscripts.com/common/preview-directory.png" /></a>
		</li>
		<li>
			<a href="https://codecanyon.net/item/woocommerce-search-filter-plugin-for-wordpress/30151200" target="_blank"><img src="https://www.salephpscripts.com/common/preview-wcsearch.png" /></a>
		</li>
		<li>
			<a href="https://codecanyon.net/item/web-20-google-maps-plugin-for-wordpress/14615094" target="_blank"><img src="https://www.salephpscripts.com/common/preview-maps.png" /></a>
		</li>
		<li>
			<a href="https://codecanyon.net/item/ratings-reviews-plugin-for-wordpress/25458834" target="_blank"><img src="https://www.salephpscripts.com/common/preview-reviews.png" /></a>
		</li>
		<li>
			<a href="https://codecanyon.net/item/mapbox-locator-plugin-for-wordpress/27645284" target="_blank"><img src="https://www.salephpscripts.com/common/preview-mapbox.png" /></a>
		</li>
	</ul>
	<?php if ((time() - get_option('w2mb_installed_plugin_time')) > 3600*24*30): ?>
	<p><?php esc_html_e("We've noticed you've been using MapBox locator plugin for some time now, we hope you love it! We'd be happy if you could give us a 5 stars rating", "W2MB"); ?> <a href="https://codecanyon.net/item/mapbox-locator-plugin-for-wordpress/27645284" target="_blank"><img src="https://www.salephpscripts.com/common/5-star-rating.png"></a></p>
	<?php endif; ?>
</div>