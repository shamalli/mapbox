<?php

function w2mb_getPluginID() {
	return 27645284;
}
function w2mb_getPluginName() {
	return 'MapBox Locator plugin';
}
function w2mb_getEnvatoSlug() {
	return 'mapbox-locator-plugin-for-wordpress';
}
function w2mb_getUpdateDocsLink() {
	return 'https://www.salephpscripts.com/wordpress-mapbox/demo/documentation/update/';
}
function w2mb_getVersionLink() {
	return 'https://www.salephpscripts.com/wordpress-mapbox/version/';
}
function w2mb_getUpdateSupportLink() {
	return 'https://salephpscripts.com/support-renew';
}
function w2mb_getPurchaseLink() {
	return 'https://salephpscripts.com/license-purchase?product=w2mb';
}

class w2mb_updater {
	private $update_path;
	private $slug; // plugin slug
	private $plugin_file; // __FILE__ of our plugin
	private $envato_res;
	private $salephpscripts_res;
	
	private $plugin_data;

	private $purchase_code;
	private $api_key; // buyer's Personal Token required
	
	public function __construct($plugin_file, $purchase_code, $access_token) {
		add_filter("pre_set_site_transient_update_plugins", array($this, "setTransient"));
		add_filter("plugins_api", array($this, "setPluginInfo"), 10, 3);
		
		add_filter("upgrader_package_options", array($this, "setUpdatePackage"));
		add_filter("upgrader_pre_download", array($this, "updateErrorMessage"), 10, 3);

		$this->update_path = w2mb_getVersionLink();
		$this->plugin_file = $plugin_file;
		$this->slug = plugin_basename($this->plugin_file);
		
		add_action('in_plugin_update_message-' . $this->slug, array($this, 'showUpgradeMessage'), 10, 2);

		$this->purchase_code = $purchase_code;
		$this->api_key = $access_token;
		
		add_action('wp_ajax_w2mb_license_support_checker', array($this, 'license_support_checker'));
		add_action('wp_ajax_nopriv_w2mb_license_support_checker', array($this, 'license_support_checker'));
	}
		
	public function license_support_checker() {
		
		check_ajax_referer('w2mb_license_support_checker_nonce', 'security');
		
		if ($this->purchase_code) {
			
			$thirty_minutes = 30*60;
			
			if (
				!get_option("w2mb_license_support_check_last_time") ||
				(get_option("w2mb_license_support_check_last_time") + $thirty_minutes) <= time()
			) {
				$url = "https://salephpscripts.com/license-support-check?purchase_code=" . $this->purchase_code;
				$curl = curl_init($url);
			
				$header = array();
				$header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0';
				$header[] = 'timeout: 20';
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
				curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
				curl_setopt($curl, CURLOPT_HEADER, 0);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
				curl_setopt($curl, CURLOPT_TIMEOUT, 20);
				
				if (($salephpscripts_res_date = curl_exec($curl)) !== false) {
					curl_close($curl);
					
					if (!empty($salephpscripts_res_date)) {
						
						update_option("w2mb_license_support_check_last_time", time());
						update_option("w2mb_license_support_until_date", $salephpscripts_res_date);
						
						echo $this->support_checker_response($salephpscripts_res_date);
					} else {
						echo $this->support_checker_response(false);
					}
				} else {
					echo $this->support_checker_response("cURL error: " . curl_error($curl));
					curl_close($curl);
				}
			} elseif (get_option("w2mb_license_support_until_date")) {
				echo $this->support_checker_response(get_option("w2mb_license_support_until_date"));
			}
		}
		
		die();
	}
	
	public function support_checker_response($date) {
		
		if ($date === false) {
			return '<p class="w2mb-license-support-checker-expired">' . esc_html__("No purchase associated with purchase code.", "W2MB") . ' ' . esc_html__("Please follow the link", "W2MB") . ' ' . '<a href="' . w2mb_getPurchaseLink() . '" target="_blank">' . esc_html__("to buy a license", "W2MB") . '</a></p>';
		}
		
		if (!is_numeric($date)) {
			return false;
		}
		
		if ($date > time()) {
			return '<p class="w2mb-license-support-checker-active">' . esc_html__("Your support is active till ", "W2MB") . date_i18n(get_option('date_format'), intval($date)) . '. ' . esc_html__("Please follow the link if you wish", "W2MB") . ' ' . '<a href="' . w2mb_getUpdateSupportLink() . '" target="_blank">' . esc_html__("to renew", "W2MB") . '</a></p>';
		} else {
			return '<p class="w2mb-license-support-checker-expired">' . esc_html__("Your support has expired since ", "W2MB") . date_i18n(get_option('date_format'), intval($date)) . '. ' . esc_html__("Please follow the link", "W2MB") . ' ' . '<a href="' . w2mb_getUpdateSupportLink() . '" target="_blank">' . esc_html__("to renew", "W2MB") . '</a></p>';
		}
	}
	
	public function getDownload_url($debug = false) {
		
		if ($this->purchase_code) {
			
			$host = site_url();
				
			$url = "https://salephpscripts.com/license-get-download-url?purchase_code=" . $this->purchase_code . "&host=" . $host;
			$curl = curl_init($url);
		
			$header = array();
			$header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0';
			$header[] = 'timeout: 20';
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
			curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		
			$this->salephpscripts_res = curl_exec($curl);
			curl_close($curl);
				
			if ($debug) {
				var_dump($this->salephpscripts_res);
			}
				
			if (!empty($this->salephpscripts_res)) {
				return $this->salephpscripts_res;
			}
		}
		
		if ($this->purchase_code && $this->api_key) {
			$url = "https://api.envato.com/v3/market/buyer/download?purchase_code=" . $this->purchase_code;
			$curl = curl_init($url);
			
			$header = array();
			$header[] = 'Authorization: Bearer ' . $this->api_key;
			$header[] = 'User-Agent: Purchase code verification on ' . get_bloginfo();
			$header[] = 'timeout: 20';
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
			curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			
			$this->envato_res = curl_exec($curl);
			curl_close($curl);
			$this->envato_res = json_decode($this->envato_res);
			
			if ($debug) {
				var_dump($this->envato_res);
			}
			
			if (isset($this->envato_res->wordpress_plugin) && strpos($this->envato_res->wordpress_plugin, w2mb_getEnvatoSlug()) !== false) {
				return $this->envato_res->wordpress_plugin;
			} else {
				return $this->envato_res->error;
			}
		}
	}
	
	function updateErrorMessage($reply, $package, $upgrader) {
		// Don't override a reply that was set already.
		if ($reply !== false) {
			return $reply;
		}
		
		if (isset($this->envato_res->error)) {
			$error_message = esc_html__("response from Codecanyon - ", "W2MB") . $this->envato_res->error . (!empty($this->envato_res->description) ? ' (' .  $this->envato_res->description . ')' : '');
			
			return new WP_Error('no_package', $error_message);
		}
		
		return $reply;
	}
	
	public function getRemote_version() {
		$request = wp_remote_get($this->update_path);
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
			return $request['body'];
		}
		
		return false;
	}
	
	public function setUpdatePackage($options) {
		$package = $options['package'];
		if ($package === $this->slug) {
			$options['package'] = $this->getDownload_url();
		}
		
		return $options;
	}
	
	// Push in plugin version information to get the update notification
	public function setTransient($transient) {
		// If we have checked the plugin data before, don't re-check
		if (empty($transient->checked)) {
			return $transient;
		}

		// Get plugin & version information
		$remote_version = $this->getRemote_version();

		// If a newer version is available, add the update
		if (version_compare(W2MB_VERSION, $remote_version, '<')) {
			$plugin_data = get_plugin_data($this->plugin_file);
			
			$obj = new stdClass();
			$obj->slug = str_replace('.php', '', $this->slug);
			$obj->new_version = $remote_version;
			$obj->package = $this->slug;
			$obj->url = $plugin_data["PluginURI"];
			$obj->name = w2mb_getPluginName();
			$transient->response[$this->slug] = $obj;
		}
		
		return $transient;
	}
	
	public function showUpgradeMessage($plugin_data, $response) {
		if (empty($response->package)) {
			echo sprintf(__('Correct Envato access token required. You have to download the latest version from <a href="%s" target="_blank">Codecanyon</a> and follow <a href="%s" target="_blank">update instructions</a>.', 'W2MB'), 'https://codecanyon.net/downloads', w2mb_getUpdateDocsLink());
		}
	}
	
	// Push in plugin version information to display in the details lightbox
	public function setPluginInfo($false, $action, $response) {
		if (empty($response->slug) || $response->slug != str_replace('.php', '', $this->slug)) {
			return $false;
		}
		
		if ($action == 'plugin_information') {
			$remote_version = $this->getRemote_version();

			$plugin_data = get_plugin_data($this->plugin_file);
			
			if ($envatoRes = w2mb_get_plugin_info($this->purchase_code)) {
				$response = new stdClass();
				$response->last_updated = $envatoRes->item->updated_at;
				$response->slug = $this->slug;
				$response->name  = $this->pluginData["Name"];
				$response->plugin_name  = $plugin_data["Name"];
				$response->version = $remote_version;
				$response->author = $plugin_data["AuthorName"];
				$response->homepage = $plugin_data["PluginURI"];
	
				if (isset($envatoRes->item->description)) {
					$response->sections = array(
							'description' => $envatoRes->item->description,
					);
				}
				return $response;
			}
		}
	}
}
																																																																																				
function w2mb_getAccessToken() { return 'R0qSjwSjti1fvlnVB7Kt1rNKgz2cdAYE'; } add_action('vp_w2mb_option_before_ajax_save', 'w2mb_verify_license_on_setting', 1); function w2mb_verify_license_on_setting($opts) { global $w2mb_instance, $w2mb_license_verify_error; $q = "hexdec"; if (!get_option("w2mb_v{$q("0x14")}Qd10fG041L01")) { if (!empty($opts['w2mb_purchase_code'])) { $w2mb_purchase_code = trim($opts['w2mb_purchase_code']); update_option('w2mb_purchase_code', $w2mb_purchase_code); update_option('vpt_option', array( 'w2mb_purchase_code' => $w2mb_purchase_code ) ); if (w2mb_verify_license($w2mb_purchase_code)) { update_option("w2mb_v{$q("0x14")}Qd10fG041L01", 1); if (ob_get_length()) ob_clean(); header('Content-type: application/json'); echo json_encode(array( 'status' => true, 'message' => 'License verification passed successfully!' )); die(); } } remove_action('vp_w2mb_option_after_ajax_save', array($w2mb_instance->settings_manager, 'save_option'), 10); if (ob_get_length()) ob_clean(); header('Content-type: application/json'); echo json_encode(array( 'status' => false, 'message' => 'License verification did not pass!<br />' . $w2mb_license_verify_error )); die(); } } function w2mb_get_plugin_info($purchase_code) { if ($purchase_code) { $host = site_url(); $url = "https://salephpscripts.com/license-activate?purchase_code=" . $purchase_code . "&host=" . $host; $curl = curl_init($url); $header = array(); $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0'; $header[] = 'timeout: 20'; curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); curl_setopt($curl, CURLOPT_HTTPHEADER,$header); curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]); curl_setopt($curl, CURLOPT_HEADER, 0); curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8'); curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20); curl_setopt($curl, CURLOPT_TIMEOUT, 20); if (($salephpscripts_res = curl_exec($curl)) !== false) { curl_close($curl); return $salephpscripts_res; } } if ($purchase_code) { $url = "https://api.envato.com/v3/market/author/sale?code=".$purchase_code; $curl = curl_init($url); $header = array(); $header[] = 'Authorization: Bearer '.w2mb_getAccessToken(); $header[] = 'User-Agent: Purchase code verification on ' . get_bloginfo(); $header[] = 'timeout: 20'; curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); curl_setopt($curl, CURLOPT_HTTPHEADER,$header); curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]); curl_setopt($curl, CURLOPT_HEADER, 0); curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8'); curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); if (($envatoRes = curl_exec($curl)) !== false) { curl_close($curl); return json_decode($envatoRes); } else { global $w2mb_license_verify_error; $w2mb_license_verify_error = "cURL error: " . curl_error($curl); curl_close($curl); } } } function w2mb_verify_license($purchase_code) { $res = w2mb_get_plugin_info($purchase_code); if (is_numeric($res)) { return true; } if (isset($res->item->id) && $res->item->id == w2mb_getPluginID()) { return true; } elseif (isset($res->error)) { global $w2mb_license_verify_error; error_log($res->error . ' ' . $res->description); $w2mb_license_verify_error = "Envato: " . $res->error . ' ' . $res->description; } elseif (isset($res->message)) { global $w2mb_license_verify_error; $w2mb_license_verify_error = "Envato: " . $res->message; } elseif (isset($res->Message)) { global $w2mb_license_verify_error; $w2mb_license_verify_error = "Envato: " . $res->Message; } } add_filter('w2mb_build_settings', 'w2mb_verify_license_settings', 100); function w2mb_verify_license_settings($options) { $options['template']['menus']['general']['controls'] = array_merge( array('license' => array( 'type' => 'section', 'title' => __('License information', 'W2MB'), 'fields' => array( array( 'type' => 'textbox', 'name' => 'w2mb_purchase_code', 'label' => __('Purchase code*', 'W2MB'), 'description' => __('You should receive purchase (license) code after purchase <div class="w2mb-license-support-checker" data-nonce="' . wp_create_nonce('w2mb_license_support_checker_nonce') . '"></div>', 'W2MB'), 'default' => get_option('w2mb_purchase_code'), ), ), )), $options['template']['menus']['general']['controls'] ); return $options; } add_action('w2mb_settings_panel_top', 'w2mb_settings_panel_top'); function w2mb_settings_panel_top() { $q = "hexdec"; if (!get_option("w2mb_v{$q("0x14")}Qd10fG041L01")) { echo '<div class="error">'; echo '<p>' . sprintf('Your installation of %s was not verified. Any changes in the settings below will not be saved. To verify license information take purchase code from purchase email.', w2mb_getPluginName()) . '</p>'; echo '</div>'; } } 

?>