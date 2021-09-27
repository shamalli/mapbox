<?php

class w2mb_avg_rating {
	public $ratings = array();
	public $ratings_count = 0;
	public $avg_value = 0;
	
	private $post_id;
	
	public function __construct($post_id) {
		global $wpdb, $wp_version;
		
		$this->post_id = $post_id;
	
		if (version_compare($wp_version, '4.0', '<'))
			$like = like_escape(W2MB_RATING_PREFIX);
		else
			$like = $wpdb->esc_like(W2MB_RATING_PREFIX);
		
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key LIKE %s", $post_id, $like.'%'), ARRAY_A);
		foreach ($results AS $row) {
			$rating = new w2mb_rating($row);
			$this->ratings[] = $rating;
			$this->avg_value += $rating->value;
			$this->ratings_count++;
		}
		if ($this->ratings_count)
			$this->avg_value = $this->avg_value/$this->ratings_count;
		$this->avg_value = number_format(round($this->avg_value, 1), 1);
	}
	
	public function update_avg_rating() {
		update_post_meta($this->post_id, W2MB_AVG_RATING_KEY, $this->avg_value);
	}
	
	public function render_star($star_num) {
		$sub = $this->avg_value - $star_num;
		if ($sub >= 0 || abs($sub) <= 0.25) {
			return 'w2mb-fa-star';
		} elseif (abs($sub) >= 0.25 && abs($sub) <= 0.75) {
			return 'w2mb-fa-star-half-o';
		} else {
			return 'w2mb-fa-star-o';
		}
	}
}

class w2mb_rating {
	public $user_id;
	public $ip;
	public $value;

	private static $validation;

	public function __construct($row) {
		$this->value = (int)$row['meta_value'];
		
		$part = str_replace(W2MB_RATING_PREFIX, '', $row['meta_key']);

		if (!self::$validation) {
			self::$validation = new w2mb_form_validation();
		}

		if (self::$validation->valid_ip($part)) {
			$this->ip = $part;
		} else {
			$this->user_id = $part;
		}
	}
	
	public function render_star($star_num) {
		if ($this->value >= $star_num) {
			return 'w2mb-fa-star';
		} else {
			return 'w2mb-fa-star-o';
		}
	}
}

function w2mb_build_single_rating($post_id, $user_id) {
	global $wpdb;
	
	if ($row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key=%s", $post_id, W2MB_RATING_PREFIX.$user_id), ARRAY_A)) {
		$rating = new w2mb_rating($row);
		return $rating;
	}
}

function w2mb_flush_ratings($post_id) {
	global $wpdb;
	
	return ($wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE post_id=%d AND (meta_key=%s OR meta_key LIKE %s)", $post_id, W2MB_AVG_RATING_KEY, W2MB_RATING_PREFIX.'%')) !== false) ? true : false;
}

?>