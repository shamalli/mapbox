<?php

class w2mb_maps {
	public $args;
	public $controller;
	public $map_id;
	
	public $map_zoom;
	public $listings_array = array();
	public $locations_array = array();
	public $locations_option_array = array();

	public static $map_content_fields;

	public function __construct($args = array(), $controller = 'listings_controller') {
		$this->args = $args;
		$this->controller = $controller;
	}
	
	public function setUniqueId($unique_id) {
		$this->map_id = $unique_id;
	}

	public function collectLocations($listing, $show_directions_button = true, $show_readmore_button = true) {
		global $w2mb_address_locations, $w2mb_tax_terms_locations;

		if (count($listing->locations) == 1)
			$this->map_zoom = $listing->map_zoom;

		foreach ($listing->locations AS $location) {
			if ((!$w2mb_address_locations || in_array($location->id, $w2mb_address_locations)) && (!$w2mb_tax_terms_locations || in_array($location->selected_location, $w2mb_tax_terms_locations))) {
				if (($location->map_coords_1 && $location->map_coords_1 != '0.000000') || ($location->map_coords_2 && $location->map_coords_2 != '0.000000')) {
					$logo_image = '';
					if ($listing->level->logo_enabled) {
						if ($listing->logo_image) {
							$height = apply_filters('w2mb_map_infowindow_logo_height', 0);
							
							$logo_image = $listing->get_logo_url(array(get_option('w2mb_map_infowindow_logo_width'), $height));
						} elseif (get_option('w2mb_enable_nologo') && get_option('w2mb_nologo_url')) {
							$logo_image = get_option('w2mb_nologo_url');
						}
					}
	
					$content_fields_output = $listing->setInfoWindowContent($this->map_id, $location, $show_directions_button, $show_readmore_button);
	
					$this->listings_array[] = $listing;
					$this->locations_array[] = $location;
					$this->locations_option_array[] = array(
							$location->id,
							$listing->post->ID,
							$location->map_coords_1,
							$location->map_coords_2,
							$location->map_icon_file,
							$location->map_icon_color,
							$listing->map_zoom,
							$listing->title(),
							$logo_image,
							$content_fields_output,
					);
				}
			}
		}

		if ($this->locations_option_array)
			return true;
		else
			return false;
	}
	
	public function collectLocationsForAjax($listing) {	
		global $w2mb_address_locations, $w2mb_tax_terms_locations;

		foreach ($listing->locations AS $location) {
			if ((!$w2mb_address_locations || in_array($location->id, $w2mb_address_locations))  && (!$w2mb_tax_terms_locations || in_array($location->selected_location, $w2mb_tax_terms_locations))) {
				if (($location->map_coords_1 && $location->map_coords_1 != '0.000000') || ($location->map_coords_2 && $location->map_coords_2 != '0.000000')) {
					$this->listings_array[] = $listing;
					$this->locations_array[] = $location;
					$this->locations_option_array[] = array(
							$location->id,
							$listing->post->ID,
							$location->map_coords_1,
							$location->map_coords_2,
							$location->map_icon_file,
							$location->map_icon_color,
							null,
							null,
							null,
							null,
					);
				}
			}
		}
		if ($this->locations_option_array)
			return true;
		else
			return false;
	}
	
	public function buildListingsContent($show_directions_button = true, $show_readmore_button = true) {
		$out = '';
		foreach ($this->locations_array AS $key=>$location) {
			$listing = $this->listings_array[$key];
			$listing->setContentFields();
	
			$out .= w2mb_renderTemplate('frontend/listing_location.tpl.php', array('map_id' => $this->map_id, 'listing' => $listing, 'location' => $location, 'show_directions_button' => $show_directions_button, 'show_readmore_button' => $show_readmore_button), true);
		}
		return $out;
	}

	public function display($display_args = array()) {
		$display_args = array_merge(array(
				'width' => false,
				'height' => 400,
				'sticky_scroll' => false,
				'sticky_scroll_toppadding' => 10,
				'map_style_name' => '',
				'search_form' => false,
		), $display_args);
		
		$this->args['map_style'] = w2mb_getSelectedMapStyle($display_args['map_style_name']);
		
		$locations_options = json_encode($this->locations_option_array);
		$map_args = json_encode($this->args, JSON_NUMERIC_CHECK);
		
		// since WP 6.1.0 it adds unescaped decoding="async" in img tags breaking maps output
		add_filter("wp_img_tag_add_decoding_attr", "__return_false", 1000);
		
		w2mb_renderTemplate('maps/map.tpl.php',
				array(
						'locations_options' => $locations_options,
						'locations_array' => $this->locations_array,
						'search_form' => $display_args['search_form'],
						'width' => $display_args['width'],
						'height' => $display_args['height'],
						'sticky_scroll' => $display_args['sticky_scroll'],
						'sticky_scroll_toppadding' => $display_args['sticky_scroll_toppadding'],
						'directions_sidebar' => $this->args['show_directions_button'],
						'directions_sidebar_open' => $this->args['directions_sidebar_open'],
						'controller' => $this->controller,
						'map_object' => $this,
						'map_id' => $this->map_id,
						'listings_content' => $this->buildListingsContent((!empty($this->args['show_directions_button']) ? 1 : 0), (!empty($this->args['show_readmore_button']) ? 1 : 0)),
						'map_args' => $map_args,
						'args' => $this->args
				));
	}
	
	public function is_ajax_loading() {
		if (isset($this->args['ajax_loading']) && $this->args['ajax_loading'] && ((isset($this->args['start_address']) && $this->args['start_address']) || ((isset($this->args['start_latitude']) && $this->args['start_latitude']) && (isset($this->args['start_longitude']) && $this->args['start_longitude']))))
			return true;
		else
			return false;
	}
}

?>