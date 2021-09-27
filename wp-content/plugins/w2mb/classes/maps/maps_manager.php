<?php 

class w2mb_maps_manager {
	public function __construct() {
		add_action('after_setup_theme', array($this, 'init_metaboxes'));
		
		add_filter('manage_'.W2MB_MAP_TYPE.'_posts_columns', array($this, 'add_maps_table_columns'));
		add_filter('manage_'.W2MB_MAP_TYPE.'_posts_custom_column', array($this, 'manage_maps_table_rows'), 10, 2);
		
		add_filter('post_row_actions', array($this, 'duplicate_map_link'), 10, 2);
		add_action('admin_action_w2mb_duplicate_map', array($this, 'duplicate_map'));
	}
	
	public function add_maps_table_columns($columns) {
		global $w2mb_instance;
	
		$w2mb_columns['w2mb_shortcode'] = esc_html__('Shortcode', 'W2MB');
	
		return array_slice($columns, 1, 1, true) + $w2mb_columns + array_slice($columns, 1, count($columns)-1, true);
	}
	
	public function manage_maps_table_rows($column, $post_id) {
		switch ($column) {
			case "w2mb_shortcode":
				echo '[mapbox id=' . $post_id . ']';
			break;
		}
	}
	
	public function duplicate_map_link($actions, $post) {
		if ($post->post_type == W2MB_MAP_TYPE) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=w2mb_duplicate_map&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="' . esc_html__("Make duplicate", "W2MB") . '">' . esc_html__("Make duplicate", "W2MB") . '</a>';
		}
		
		return $actions;
	}
	
	public function duplicate_map() {
		global $wpdb;
		
		if (empty($_GET['post'])) {
			wp_die('No post to duplicate has been supplied!');
		}

		if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__))) {
			return;
		}
		
		$post_id = $_GET['post'];
		$post = get_post($post_id);
		
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		if (isset($post) && $post != null) {
			$args = array(
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_author'    => $new_post_author,
					'post_content'   => $post->post_content,
					'post_excerpt'   => $post->post_excerpt,
					'post_name'      => $post->post_name . "-duplicate",
					'post_parent'    => $post->post_parent,
					'post_password'  => $post->post_password,
					'post_status'    => 'publish',
					'post_title'     => $post->post_title . " (duplicate)",
					'post_type'      => $post->post_type,
					'to_ping'        => $post->to_ping,
					'menu_order'     => $post->menu_order
			);
			$new_post_id = wp_insert_post( $args );
			
			$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
			if (count($post_meta_infos)) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ($post_meta_infos as $meta_info) {
					$meta_key = $meta_info->meta_key;
					if ($meta_key == '_wp_old_slug') {
						continue;
					}
					$meta_value = addslashes($meta_info->meta_value);
					$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query.= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}
			
			wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
			die();
		} else {
			wp_die('Post creation failed, could not find original post: ' . $post_id);
		}
	}

	public function init_metaboxes() {

		$settings_metabox = new VP_W2MB_Metabox(array(
				'id'          => 'w2mb_map_settings',
				'types'       => array(W2MB_MAP_TYPE),
				'title'       => esc_html__('Map settings', 'W2MB'),
				'priority'    => 'high',
				'template'    => W2MB_PATH . '/classes/maps/metaboxes/settings.php'
		));
		
		$ajax_metabox = new VP_W2MB_Metabox(array(
				'id'          => 'w2mb_map_ajax',
				'types'       => array(W2MB_MAP_TYPE),
				'title'       => esc_html__('Map AJAX loading', 'W2MB'),
				'priority'    => 'high',
				'template'    => W2MB_PATH . '/classes/maps/metaboxes/ajax.php'
		));
		
		$start_point_metabox = new VP_W2MB_Metabox(array(
				'id'          => 'w2mb_map_starting_point',
				'types'       => array(W2MB_MAP_TYPE),
				'title'       => esc_html__('Map starting point and radius', 'W2MB'),
				'priority'    => 'high',
				'template'    => W2MB_PATH . '/classes/maps/metaboxes/starting_point.php'
		));
		
		$controls_metabox = new VP_W2MB_Metabox(array(
				'id'          => 'w2mb_map_controls',
				'types'       => array(W2MB_MAP_TYPE),
				'title'       => esc_html__('Map controls', 'W2MB'),
				'priority'    => 'high',
				'template'    => W2MB_PATH . '/classes/maps/metaboxes/controls.php'
		));
		
		$sidebar_listings_metabox = new VP_W2MB_Metabox(array(
				'id'          => 'w2mb_map_sidebar_listings',
				'types'       => array(W2MB_MAP_TYPE),
				'title'       => esc_html__('Listings sidebar', 'W2MB'),
				'priority'    => 'high',
				'template'    => W2MB_PATH . '/classes/maps/metaboxes/sidebar_listings.php'
		));
		
		$search_metabox = new VP_W2MB_Metabox(array(
				'id'          => 'w2mb_map_search',
				'types'       => array(W2MB_MAP_TYPE),
				'title'       => esc_html__('Search options', 'W2MB'),
				'priority'    => 'high',
				'template'    => W2MB_PATH . '/classes/maps/metaboxes/search.php'
		));
		
		$markers_metabox = new VP_W2MB_Metabox(array(
				'id'          => 'w2mb_map_markers',
				'types'       => array(W2MB_MAP_TYPE),
				'title'       => esc_html__('Filter markers to display', 'W2MB'),
				'priority'    => 'high',
				'template'    => W2MB_PATH . '/classes/maps/metaboxes/markers.php'
		));
	}
}

function w2mb_getMetaboxMapsStyles() {

	$mapbox_map_styles = array();
	foreach (w2mb_getMapBoxStyles() AS $name=>$style) {
		$mapbox_map_styles[] = array('value' => $name, 'label' => $name);
	}
	
	return $mapbox_map_styles;
}

function w2mb_getMetaboxOptionsTerms($tax, &$items = array(), $parent = 0, $level = 0) {
	$terms = get_terms(
			array(
					'taxonomy' => array($tax),
					'parent' => $parent,
					'hide_empty' => false
			)
	);

	foreach ($terms AS $term) {
		$items[] = array('value' => $term->term_id, 'label' => (str_repeat('&nbsp;&nbsp;&nbsp;', $level)) . $term->name);
		w2mb_getMetaboxOptionsTerms($tax, $items, $term->term_id, $level+1);
	}
	
	return $items;
}

?>