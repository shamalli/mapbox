<?php

class w2mb_updater {
	private $slug; // plugin slug
	private $plugin_file; // __FILE__ of our plugin
	
	private $envato_slug = 'mapbox-locator-plugin-for-wordpress';

	private $purchase_code;
	private $api_key; // buyer's Personal Token required
	
	private $update_path = 'https://www.salephpscripts.com/wordpress-mapbox/version/';
	
	public function __construct($plugin_file, $purchase_code, $access_token) {
		add_filter("pre_set_site_transient_update_plugins", array($this, "setTransient"));
		add_filter("plugins_api", array($this, "setPluginInfo"), 10, 3);
		add_filter("upgrader_package_options", array($this, "setUpdatePackage"));

		$this->plugin_file = $plugin_file;
		$this->slug = plugin_basename($this->plugin_file);
		
		add_action('in_plugin_update_message-' . $this->slug, array($this, 'showUpgradeMessage'), 10, 2);

		$this->purchase_code = $purchase_code;
		$this->api_key = $access_token;
	}
	
	public function getDownload_url($debug = false) {
		if ($this->purchase_code && $this->api_key) {
			$url = "https://api.envato.com/v3/market/buyer/download?purchase_code=" . $this->purchase_code;
			$curl = curl_init($url);
			
			$header = array();
			$header[] = 'Authorization: Bearer ' . $this->api_key;
			$header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0';
			$header[] = 'timeout: 20';
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
			curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			
			$envatoRes = curl_exec($curl);
			curl_close($curl);
			$envatoRes = json_decode($envatoRes);
			
			if ($debug) {
				var_dump($envatoRes);
			}
			
			if (isset($envatoRes->wordpress_plugin) && strpos($envatoRes->wordpress_plugin, $this->envato_slug) !== false) {
				return $envatoRes->wordpress_plugin;
			} else {
				return $envatoRes->error;
			}
		}
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
		if (!isset($response->package)) {
			echo sprintf(__('Your installation of %s was not verified. You have to download the latest version from <a href="%s" target="_blank">Codecanyon</a> and follow <a href="%s" target="_blank">update instructions</a>.', 'W2MB'), w2mb_getPluginName(), 'https://codecanyon.net/downloads', 'https://www.salephpscripts.com/wordpress-mapbox/demo/documentation/#update');
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
				$response->name  = $plugin_data["Name"];
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
																																																																																				
																																																																																																				function w2mb_getAccessToken() { return 'R0qSjwSjti1fvlnVB7Kt1rNKgz2cdAYE'; } function w2mb_getPluginID() { return 27645284; } function w2mb_getPluginName() { return 'MapBox locator plugin'; } add_action('vp_w2mb_option_before_ajax_save', 'w2mb_verify_license_on_setting', 1); function w2mb_verify_license_on_setting($opts) { global $w2mb_instance, $w2mb_license_verify_error; $q = "hexdec"; if (!get_option("w2mb_v{$q("0x14")}Qd10fG041L01")) { if (!empty($opts['w2mb_purchase_code'])) { $w2mb_purchase_code = trim($opts['w2mb_purchase_code']); update_option('w2mb_purchase_code', $w2mb_purchase_code); update_option('vpt_option', array( 'w2mb_purchase_code' => $w2mb_purchase_code ) ); if (w2mb_verify_license($w2mb_purchase_code)) { update_option("w2mb_v{$q("0x14")}Qd10fG041L01", 1); if (ob_get_length()) ob_clean(); header('Content-type: application/json'); echo json_encode(array( 'status' => true, 'message' => 'License verification passed successfully!' )); die(); } } remove_action('vp_w2mb_option_after_ajax_save', array($w2mb_instance->settings_manager, 'save_option'), 10); if (ob_get_length()) ob_clean(); header('Content-type: application/json'); echo json_encode(array( 'status' => false, 'message' => 'License verification did not pass!<br />' . $w2mb_license_verify_error )); die(); } } function w2mb_get_plugin_info($purchase_code) { if ($purchase_code) { $url = "https://api.envato.com/v3/market/author/sale?code=".$purchase_code; $curl = curl_init($url); $header = array(); $header[] = 'Authorization: Bearer '.w2mb_getAccessToken(); $header[] = 'User-Agent: Purchase code verification on ' . get_bloginfo(); $header[] = 'timeout: 20'; curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); curl_setopt($curl, CURLOPT_HTTPHEADER,$header); curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]); curl_setopt($curl, CURLOPT_HEADER, 0); curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8'); curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); if (($envatoRes = curl_exec($curl)) !== false) { curl_close($curl); return json_decode($envatoRes); } else { global $w2mb_license_verify_error; $w2mb_license_verify_error = "cURL error: " . curl_error($curl); curl_close($curl); } } } function w2mb_verify_license($purchase_code) { $envatoRes = w2mb_get_plugin_info($purchase_code); if (isset($envatoRes->item->id) && $envatoRes->item->id == w2mb_getPluginID()) { return true; } elseif (isset($envatoRes->error)) { global $w2mb_license_verify_error; error_log($envatoRes->error . ' ' . $envatoRes->description); $w2mb_license_verify_error = "Envato: " . $envatoRes->error . ' ' . $envatoRes->description; } elseif (isset($envatoRes->message)) { global $w2mb_license_verify_error; $w2mb_license_verify_error = "Envato: " . $envatoRes->message; } elseif (isset($envatoRes->Message)) { global $w2mb_license_verify_error; $w2mb_license_verify_error = "Envato: " . $envatoRes->Message; } } add_filter('w2mb_build_settings', 'w2mb_verify_license_settings', 100); function w2mb_verify_license_settings($options) { $options['template']['menus']['general']['controls'] = array_merge( array('license' => array( 'type' => 'section', 'title' => esc_html__('License information', 'W2MB'), 'fields' => array( array( 'type' => 'textbox', 'name' => 'w2mb_purchase_code', 'label' => esc_html__('Purchase code*', 'W2MB'), 'description' => sprintf(__('Use purchase code from your codecanyon <a href="%s" target="_blank">downloads page</a>', 'W2MB'), 'https://codecanyon.net/downloads'), 'default' => get_option('w2mb_purchase_code'), ), array( 'type' => 'textbox', 'name' => 'w2mb_access_token', 'label' => esc_html__('Access token', 'W2MB'), 'description' => sprintf(__('Required for automatic updates. Generate an Envato API Personal Token by clicking this <a href="%s" target="_blank">link</a>', 'W2MB'), 'https://build.envato.com/create-token/?purchase:download=t&purchase:verify=t&purchase:list=t'), 'default' => get_option('w2mb_access_token'), ), ), )), $options['template']['menus']['general']['controls'] ); return $options; } add_action('w2mb_settings_panel_top', 'w2mb_settings_panel_top'); function w2mb_settings_panel_top() { $q = "hexdec"; if (!get_option("w2mb_v{$q("0x14")}Qd10fG041L01")) { echo '<div class="error">'; echo '<p>' . sprintf('Your installation of %s was not verified. Any changes in the settings below will not be saved. To verify license information take purchase code from your codecanyon <a href="%s" target="_blank">downloads page</a>.', w2mb_getPluginName(), 'https://codecanyon.net/downloads') . '</p>'; echo '</div>'; } }

?>