<?php 

class w2mb_location {
	public $id;
	public $listing_id;
	public $post_id;
	public $selected_location = 0;
	public $address_line_1;
	public $address_line_2;
	public $zip_or_postal_index;
	public $additional_info = '';
	public $manual_coords = false;
	public $map_coords_1, $latitude;
	public $map_coords_2, $longitude;
	public $map_icon_file;
	public $map_icon_color;
	public $map_icon_manually_selected;
	
	public function __construct($post_id = null) {
		$this->post_id = $post_id;
	}

	public function createLocationFromArray($array) {
		$this->id = w2mb_getValue($array, 'id');
		$this->listing_id = w2mb_getValue($array, 'post_id');
		$this->selected_location = w2mb_getValue($array, 'selected_location');
		$this->address_line_1 = w2mb_getValue($array, 'address_line_1');
		$this->address_line_2 = w2mb_getValue($array, 'address_line_2');
		$this->zip_or_postal_index = w2mb_getValue($array, 'zip_or_postal_index');
		$this->additional_info = w2mb_getValue($array, 'additional_info');
		$this->manual_coords = w2mb_getValue($array, 'manual_coords');
		$this->map_coords_1 = w2mb_getValue($array, 'map_coords_1');
		$this->map_coords_2 = w2mb_getValue($array, 'map_coords_2');
		$this->map_icon_file = w2mb_getValue($array, 'map_icon_file');
		$this->map_icon_color = w2mb_getValue($array, 'map_icon_color');
		$this->map_icon_manually_selected = w2mb_getValue($array, 'map_icon_manually_selected');
		
		$this->latitude = $this->map_coords_2;
		$this->longitude = $this->map_coords_1;
	}
	
	public function getSelectedLocationString($glue = ', ', $reverse = false) {
		global $w2mb_instance;

		if ($this->selected_location != 0) {
			$chain = array();
			$parent_id = $this->selected_location;
			while ($parent_id != 0) {
				if (!is_wp_error($term = get_term($parent_id, W2MB_LOCATIONS_TAX)) && $term) {
					$chain[] = $term->name;
					$parent_id = $term->parent;
				} else 
					break;
			}

			// reverse locations for actual locations levels 
			$chain = array_reverse($chain);

			$locations_levels = $w2mb_instance->locations_levels;
			$locations_levels_array = array_values($locations_levels->levels_array);
			$result_chain = array();
			foreach ($chain AS $location_key=>$location) {
				if (isset($locations_levels_array[$location_key]) && $locations_levels_array[$location_key]->in_address_line) {
					$result_chain[] = $location;
				}
			}

			// do not reverse as it was reversed before
			if (!$reverse) {
				$result_chain = array_reverse($result_chain);
			}
			
			$result_chain = apply_filters("w2mb_get_selected_location_array", $result_chain);
			
			// re-order example
			/* $order = array_flip(array(1, 2, 0));
			uksort($result_chain, function($key1, $key2) use ($order) {
				return $order[$key1] > $order[$key2];
			}); */
			
			return implode($glue, $result_chain);
		}
	}

	public function getWholeAddress($microdata = true, $glue = ', ', $reverse = false) {
		$out = array();
		$separator_previous = false;
		foreach (get_option('w2mb_addresses_order') AS $part) {
			switch ($part) {
				case 'location':
					if ($location_string = $this->getSelectedLocationString($glue, $reverse)) {
						$out[] = (($microdata) ? '<span itemprop="addressLocality">' : '') . $location_string . (($microdata) ? '</span>' : '');
					} elseif (in_array($separator_previous, array('space','comma','break'))) {
						array_pop($out);
					}
					$separator_previous = false;
					break;
				case 'line_1':
					if (trim($this->address_line_1) && get_option('w2mb_enable_address_line_1')) {
						$out[] = (($microdata) ? '<span itemprop="streetAddress">' : '') . trim($this->address_line_1) . (($microdata) ? '</span>' : '');
					} elseif (in_array($separator_previous, array('space','comma','break'))) {
						array_pop($out);
					}
					$separator_previous = false;
					break;
				case 'line_2':
					if (trim($this->address_line_2) && get_option('w2mb_enable_address_line_2')) {
						$out[] = trim($this->address_line_2);
					} elseif (in_array($separator_previous, array('space','comma','break'))) {
						array_pop($out);
					}
					$separator_previous = false;
					break;
				case 'zip':
					if (trim($this->zip_or_postal_index) && get_option('w2mb_enable_postal_index')) {
						$out[] = (($microdata) ? '<span itemprop="postalCode">' : '') . trim($this->zip_or_postal_index) . (($microdata) ? '</span>' : '');
					} elseif (in_array($separator_previous, array('space','comma','break'))) {
						array_pop($out);
					}
					$separator_previous = false;
					break;
				case 'space1':
				case 'space2':
				case 'space3':
					if ($separator_previous != 'break' && $out) {
						$out[] = ' ';
						$separator_previous = 'space';
					}
					break;
				case 'comma1':
				case 'comma2':
				case 'comma3':
					if ($separator_previous != 'break' && $out) {
						$out[] = ', ';
						$separator_previous = 'comma';
					}
					break;
				case 'break1':
				case 'break2':
				case 'break3':
					if ($separator_previous != 'break' && $out) {
						$out[] = '<br />';
						$separator_previous = 'break';
					}
					break;
			}
		}

		return trim(implode('', $out), ', ');
	}
	
	public function renderInfoFieldForMap() {
		if (get_option('w2mb_enable_additional_info'))
			return $this->additional_info;
	}
}

?>