<?php

class w2mb_locationGeoname {
	
	private $last_ret;
	private $last_status = '';
	private $last_error = '';
	
	private function getURL($query) {
		$fullUrl = '';
			
		if (get_option('w2mb_address_autocomplete_code')) {
			$iso3166 = strtolower(get_option('w2mb_address_autocomplete_code'));
			$country = '&country='.$iso3166;
		} else {
			$country = '';
		}
			
		if (get_option('w2mb_mapbox_api_key')) {
			$fullUrl = sprintf("https://api.mapbox.com/geocoding/v5/mapbox.places/%s.json?language=en&access_token=%s%s", urlencode($query), get_option('w2mb_mapbox_api_key'), $country);
		}
			
		return $fullUrl;
	}
	
	private function processResult($ret, $return) {
		$use_districts = true;
		$use_provinces = true;
		
		if ($ret) {
			if (!empty($ret['features'])) {
				$this->last_status = 200;
					
				if ($return == 'coordinates') {
					return array($ret["features"][0]["geometry"]["coordinates"][0], $ret["features"][0]["geometry"]["coordinates"][1]);
				} elseif ($return == 'geoname' && $return == 'address') {
					return @$ret["features"][0]["place_name"];
				}
			} elseif (!empty($ret['message'])) {
				$this->last_status = 403;
				$this->last_error = $ret['message'];
					
				return new WP_Error(403, $ret['message']);
			}
		}
		return '';
	}

	/**
	 * When $return = 'test':
	 * - WP_ERROR on remote call fail
	 * - WP_ERROR on maps service fail
	 *
	 * When $return = 'coordinates' or 'geoname':
	 * - WP_ERROR on maps service fail
	 *
	 * @param string $query
	 * @param string $return
	 * @return WP_Error|string|array
	 */
	public function geocodeRequest($query, $return = 'geoname') {
		$fullUrl = $this->getURL($query);
		
		$response = wp_remote_get($fullUrl);
		$body = wp_remote_retrieve_body($response);
		
		$ret = json_decode($body, true);
		
		$this->last_ret = $ret;
		
		if ($return == 'test') {
			if (is_wp_error($response)) {
				return new WP_Error(403, $response->get_error_message());
			} else {
				$result = $this->processResult($ret, $return);
				
				if (is_wp_error($result)) {
					return $result;
				} else {
					return $ret;
				}
			}
		}
		
		$address = $this->processResult($ret, $return);

		return $address;
	}
	
	public function getLastStatus() {
		return $this->last_status;
	}
	
	public function getLastError() {
		return $this->last_error;
	}
}
?>