<?php 

class w2mb_map_controller extends w2mb_frontend_controller {
	public $request_by = 'maps_controller';

	public function init($args = array()) {
		global $w2mb_instance;
		
		if (!empty($args['id']) && !wp_doing_ajax()) {
			$metaboxes = array(
					'w2mb_map_settings',
					'w2mb_map_ajax',
					'w2mb_map_starting_point',
					'w2mb_map_controls',
					'w2mb_map_sidebar_listings',
					'w2mb_map_search',
					'w2mb_map_markers',
			);
			foreach ($metaboxes AS $metabox) {
				if ($metabox_settings = get_post_meta($args['id'], $metabox)) {
					if (is_array($metabox_settings[0])) {
						$args = array_merge($metabox_settings[0], $args);
					}
				}
			}
		}
		
		parent::init($args);

		$shortcode_atts = array_merge(array(
				'num' => -1,
				'width' => '',
				'height' => 400,
				'radius_circle' => 1,
				'clusters' => 0,
				'sticky_scroll' => 0,
				'sticky_scroll_toppadding' => 0,
				'directions_panel' => 0,
				'show_directions_button' => 1,
				'directions_sidebar_open' => 0,
				'show_readmore_button' => 1,
				'ajax_loading' => 0,
				'ajax_markers_loading' => 0,
				'optimize_ajax_loading' => 0,
				'use_ajax_loader' => 0,
				'counter' => 1,
				'counter_text' => esc_html__('Number of locations %d', 'W2MB'),
				'geolocation' => 0,
				'address' => '',
				'radius' => 0,
				'start_address' => '',
				'start_latitude' => '',
				'start_longitude' => '',
				'start_zoom' => 0,
				'map_style' => (int)w2mb_getSelectedMapStyle(),
				'custom_map_style' => '',
				'include_categories_children' => 0,
				'include_get_params' => 1,
				'search_on_map' => 1,
				'search_on_map_open' => 0,
				'order_by' => 'post_date',
				'order' => 'ASC',
				'show_categories_search' => 1,
				'categories_search_level' => 1,
				'category' => 0,
				'exact_categories' => array(),
				'show_keywords_search' => 1,
				'keywords_ajax_search' => 1,
				'what_search' => '',
				'keywords_placeholder' => '',
				'show_radius_search' => 1,
				'radius' => 0,
				'show_locations_search' => 1,
				'locations_search_level' => 1,
				'show_address_search' => 1,
				'address' => '',
				'address_placeholder' => '',
				'location' => 0,
				'exact_locations' => array(),
				'draw_panel' => 0,
				'author' => 0,
				'enable_full_screen' => 1,
				'enable_full_screen_by_default' => 0,
				'enable_wheel_zoom' => 1,
				'enable_dragging_touchscreens' => 1,
				'center_map_onclick' => 0,
				'zoom_map_onclick' => 0,
				'categories' => '',
				'locations' => '',
				'ratings' => '',
				'uid' => null,
		), $args);
		$shortcode_atts = apply_filters('w2mb_related_shortcode_args', $shortcode_atts, $args);
		
		// do not use starting point on search,
		// also remove params when use_starting_point option unchecked in maps manager
		if (
			w2mb_getValue($_REQUEST, 'w2mb_action') == 'search'
			||
			(isset($args['use_starting_point']) &&
			!$args['use_starting_point'])
		) {
			unset($shortcode_atts['start_latitude']);
			unset($shortcode_atts['start_longitude']);
			unset($shortcode_atts['start_zoom']);
		}
		
		$this->args = $shortcode_atts;
		if ($shortcode_atts['include_get_params']) {
			$get_params = $_GET;
			array_walk_recursive($get_params, 'sanitize_text_field');
			$this->args = array_merge($this->args, $get_params);
		}
		
		/* if ($this->args['uid'] && $w2mb_instance->getListingsShortcodeByuID($this->args['uid'])) {
			return false;
		} */

		$args = array(
				'post_type' => W2MB_POST_TYPE,
				'post_status' => 'publish',
				//'meta_query' => array(array('key' => '_listing_status', 'value' => 'active')),
				'posts_per_page' => ($shortcode_atts['num'] ? $shortcode_atts['num'] : -1),
		);
		if ($shortcode_atts['author']) {
			$args['author'] = $shortcode_atts['author'];
		}
		
		if ($shortcode_atts['ratings']) {
			$ratings_args = explode(',', $shortcode_atts['ratings']);
				
			$meta_query['relation'] = 'OR';
			foreach ($ratings_args AS $rating_value) {
				$meta_query[] = array(
						array(
								'key' => '_avg_rating',
								'value' => $rating_value,
								'type'    => 'numeric',
								'compare' => '>=',
						),
						array(
								'key' => '_avg_rating',
								'value' => ($rating_value+1),
								'type'    => 'numeric',
								'compare' => '<',
						),
				);
			}
			$args['meta_query'][] = $meta_query;
		}

		$args = array_merge($args, apply_filters('w2mb_order_args', array(), $shortcode_atts, true));
		$args = apply_filters('w2mb_search_args', $args, $this->args, $this->args['include_get_params'], $this->hash);

		if (!empty($this->args['post__in'])) {
			if (is_string($this->args['post__in'])) {
				$args = array_merge($args, array('post__in' => explode(',', $this->args['post__in'])));
			} elseif (is_array($this->args['post__in'])) {
				$args['post__in'] = $this->args['post__in'];
			}
		}
		if (!empty($this->args['post__not_in'])) {
			$args = array_merge($args, array('post__not_in' => explode(',', $this->args['post__not_in'])));
		}

		if (isset($this->args['neLat']) && isset($this->args['neLng']) && isset($this->args['swLat']) && isset($this->args['swLng'])) {
			$y1 = $this->args['neLat'];
			$y2 = $this->args['swLat'];
			// when zoom level 2 - there may be problems with neLng and swLng of bounds
			if ($this->args['neLng'] > $this->args['swLng']) {
				$x1 = $this->args['neLng'];
				$x2 = $this->args['swLng'];
			} else {
				$x1 = 180;
				$x2 = -180;
			}
			
			global $wpdb;
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT DISTINCT
					post_id FROM {$wpdb->w2mb_locations_relationships} AS w2mb_lr
				WHERE MBRContains(
				GeomFromText('Polygon((%f %f,%f %f,%f %f,%f %f,%f %f))'),
				GeomFromText(CONCAT('POINT(',w2mb_lr.map_coords_1,' ',w2mb_lr.map_coords_2,')')))", $y2, $x2, $y2, $x1, $y1, $x1, $y1, $x2, $y2, $x2), ARRAY_A);

			$post_ids = array();
			foreach ($results AS $row) {
				$post_ids[] = $row['post_id'];
			}
			$post_ids = array_unique($post_ids);

			if ($post_ids) {
				if (isset($args['post__in'])) {
					$args['post__in'] = array_intersect($args['post__in'], $post_ids);
					if (empty($args['post__in'])) {
						// Do not show any listings
						$args['post__in'] = array(0);
					}
				} else {
					$args['post__in'] = $post_ids;
				}
			} else {
				// Do not show any listings
				$args['post__in'] = array(0);
			}
		}
		
		if (isset($this->args['geo_poly']) && $this->args['geo_poly']) {
			$geo_poly = $this->args['geo_poly'];
			$sql_polygon = array();
			foreach ($geo_poly AS $vertex)
				$sql_polygon[] = $vertex['lat'] . ' ' . $vertex['lng'];
			$sql_polygon[] = $sql_polygon[0];

			global $wpdb, $w2mb_address_locations;
			if (function_exists('mysqli_get_server_info') && $wpdb->dbh && version_compare(preg_replace('#[^0-9\.]#', '', mysqli_get_server_info($wpdb->dbh)), '5.6.1', '<')) {
				// below 5.6.1 version
				$thread_stack = @$wpdb->get_row("SELECT @@global.thread_stack", ARRAY_A);
				if ($thread_stack && $thread_stack['@@global.thread_stack'] <= 131072)
					@$wpdb->query("SET @@global.thread_stack = " . 256*1024);

				if (!$wpdb->get_row("SHOW FUNCTION STATUS WHERE name='GISWithin' AND Db='".DB_NAME."'", ARRAY_A))
					$o = $wpdb->query("CREATE FUNCTION GISWithin(pt POINT, mp POLYGON) RETURNS INT(1) DETERMINISTIC
BEGIN
			
DECLARE str, xy TEXT;
DECLARE x, y, p1x, p1y, p2x, p2y, m, xinters DECIMAL(16, 13) DEFAULT 0;
DECLARE counter INT DEFAULT 0;
DECLARE p, pb, pe INT DEFAULT 0;
			
SELECT MBRWithin(pt, mp) INTO p;
IF p != 1 OR ISNULL(p) THEN
RETURN p;
END IF;
			
SELECT X(pt), Y(pt), ASTEXT(mp) INTO x, y, str;
SET str = REPLACE(str, 'POLYGON((','');
SET str = REPLACE(str, '))', '');
SET str = CONCAT(str, ',');
			
SET pb = 1;
SET pe = LOCATE(',', str);
SET xy = SUBSTRING(str, pb, pe - pb);
SET p = INSTR(xy, ' ');
SET p1x = SUBSTRING(xy, 1, p - 1);
SET p1y = SUBSTRING(xy, p + 1);
SET str = CONCAT(str, xy, ',');
			
WHILE pe > 0 DO
SET xy = SUBSTRING(str, pb, pe - pb);
SET p = INSTR(xy, ' ');
SET p2x = SUBSTRING(xy, 1, p - 1);
SET p2y = SUBSTRING(xy, p + 1);
IF p1y < p2y THEN SET m = p1y; ELSE SET m = p2y; END IF;
IF y > m THEN
IF p1y > p2y THEN SET m = p1y; ELSE SET m = p2y; END IF;
IF y <= m THEN
IF p1x > p2x THEN SET m = p1x; ELSE SET m = p2x; END IF;
IF x <= m THEN
IF p1y != p2y THEN
SET xinters = (y - p1y) * (p2x - p1x) / (p2y - p1y) + p1x;
END IF;
IF p1x = p2x OR x <= xinters THEN
SET counter = counter + 1;
END IF;
END IF;
END IF;
END IF;
SET p1x = p2x;
SET p1y = p2y;
SET pb = pe + 1;
SET pe = LOCATE(',', str, pb);
END WHILE;
			
RETURN counter % 2;
			
END;
");
				$results = $wpdb->get_results("SELECT id, post_id FROM {$wpdb->w2mb_locations_relationships} AS w2mb_lr
				WHERE GISWithin(
				GeomFromText(CONCAT('POINT(',w2mb_lr.map_coords_1,' ',w2mb_lr.map_coords_2,')')), PolygonFromText('POLYGON((" . implode(', ', $sql_polygon) . "))'))", ARRAY_A);
			} else {
				// 5.6.1 version or higher
				$results = $wpdb->get_results("SELECT id, post_id FROM {$wpdb->w2mb_locations_relationships} AS w2mb_lr
				WHERE ST_Contains(
				PolygonFromText('POLYGON((" . implode(', ', $sql_polygon) . "))'), GeomFromText(CONCAT('POINT(',w2mb_lr.map_coords_1,' ',w2mb_lr.map_coords_2,')')))", ARRAY_A);
			}

			$post_ids = array();
			$w2mb_address_locations = array();
			foreach ($results AS $row) {
				$post_ids[] = $row['post_id'];
				$w2mb_address_locations[] = $row['id'];
			}
			$post_ids = array_unique($post_ids);
			
			if ($post_ids) {
				if (isset($args['post__in'])) {
					$args['post__in'] = array_intersect($args['post__in'], $post_ids);
					if (empty($args['post__in'])) {
						// Do not show any listings
						$args['post__in'] = array(0);
					}
				} else {
					$args['post__in'] = $post_ids;
				}
			} else {
				// Do not show any listings
				$args['post__in'] = array(0);
			}
		}

		$this->map = new w2mb_maps($this->args, $this->request_by);
		$this->map->setUniqueId($this->hash);

		if (!$this->map->is_ajax_loading()) {
			$this->query = new WP_Query($args);
			//var_dump($this->query->request);
			$this->processQuery(true, $this->args['ajax_markers_loading']);
		}
		
		apply_filters('w2mb_map_controller_construct', $this);
	}

	public function display() {
		global $w2mb_instance;

		$width = $this->args['width'];
		$height = $this->args['height'];

		ob_start();
		$this->map->display(
				array(
						'width' => $width,
						'height' => $height,
						'sticky_scroll' => $this->args['sticky_scroll'],
						'sticky_scroll_toppadding' => $this->args['sticky_scroll_toppadding'],
						'map_style_name' => ($this->args['custom_map_style']) ? $this->args['custom_map_style'] : $this->args['map_style'],
						'search_form' => $this->args['search_on_map'],
				)
		);
		$output = ob_get_clean();

		wp_reset_postdata();
	
		return $output;
	}
}

?>