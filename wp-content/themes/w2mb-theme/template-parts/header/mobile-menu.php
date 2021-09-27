<a id="mobile-trigger" href="javascript:void(0);"><i class="fa fa-align-justify"></i></a>
<div id="mobile">
	<?php
	wp_nav_menu(array(
		'theme_location' => 'primary',
		'container'      => '',
		'fallback_cb'    => 'wdt_primary_navigation_callback',
	));
	?>
</div>