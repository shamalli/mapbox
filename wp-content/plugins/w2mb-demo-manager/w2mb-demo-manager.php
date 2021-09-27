<?php 

/*
Plugin Name: W2MB demo manager
Version: 1.0.0
*/

define('W2MB_DEMO_MANAGER_PATH', plugin_dir_path(__FILE__));
define('W2MB_DEMO_MANAGER_CRON_PATH', plugin_dir_path(__FILE__) . '/../../../../../cron/');
define('W2MB_DEMO_MANAGER_BACKUPS_PATH', plugin_dir_path(__FILE__) . '/../../../../../backups/');
define('W2MB_DEMO_MANAGER_DUMP_FILE', DB_NAME . '.sql');
define('W2MB_DEMO_MANAGER_OLD_DUMP_FILE', DB_NAME . '_' . date('Y-m-d_H_i_s') . '.sql');

class w2mb_demo_manager_plugin {
	
	public function init() {
		add_action('init', array($this, 'checkPlugins'));
		
		add_action('admin_notices', array($this, 'addNotice'));

		add_action('init', array($this, 'startFinishEdition'));

		add_action('admin_menu', array($this, 'menu'));
	}
	
	public function menu() {
		add_options_page(
					'W2MB demo manager',
					'Demo manager',
					'administrator',
					'w2mb_demo_manager',
					array($this, 'addPage')
		);
	}
	
	public function checkPlugins() {
		if (!defined('W2MB_VERSION') && !defined('W2MB_LITE_VERSION')) {
			//deactivate_plugins(basename(__FILE__)); // Deactivate ourself
			deactivate_plugins(plugin_basename( __FILE__ ));
			wp_die('No one compatible plugin was not found!');
		}
	}
	
	public function addNotice() {
		if (get_option('w2mb_demo_manager_edition_start')) {
			echo '<div id="message" class="error notice">';
			echo '<p>Demo manage was started, do not forget to finish him.</p>';
			echo '</div>';
		}
	}
	
	public function addPage() {
		echo "<p>";
		
		if (!get_option('w2mb_demo_manager_edition_start')) {
			echo '<a class="button button-primary" href="' . admin_url('options-general.php?page=w2mb_demo_manager&start=1') . '">Start edition</a>';
		} else {
			echo '<a class="button button-primary" href="' . admin_url('options-general.php?page=w2mb_demo_manager&finish=1') . '">End edition</a>';
		}
		
		echo "</p>";
	}
	
	public function startFinishEdition() {
		if (!empty($_GET['start']) && !get_option('w2mb_demo_manager_edition_start')) {
			update_option('w2mb_demo_manager_edition_start', 1);
			
			$this->callDBRefresh();
			$this->redirectAfterRefresh();
		} elseif (!empty($_GET['after_refresh']) && get_option('w2mb_demo_manager_edition_start')) {
			$this->stopCron();
			$this->enableMaintenanceMode();
		} elseif (!empty($_GET['finish']) && get_option('w2mb_demo_manager_edition_start')) {
			update_option('w2mb_demo_manager_edition_start', 0);

			$this->disableMaintenanceMode();
			$this->saveDumpFile();
		}
	}
	
	public function callDBRefresh() {
		include_once W2MB_DEMO_MANAGER_CRON_PATH . 'cron_db_renew.php';
	}
	
	public function redirectAfterRefresh() {
		wp_redirect(admin_url('options-general.php?page=w2mb_demo_manager&after_refresh=1'));
		die();
	}
	
	/*
	 * move dump file from cron folder to backups folder to stop cron jobs
	 */
	public function stopCron() {
		$cron_dump = W2MB_DEMO_MANAGER_CRON_PATH . W2MB_DEMO_MANAGER_DUMP_FILE;
		$backup_dump = W2MB_DEMO_MANAGER_BACKUPS_PATH . W2MB_DEMO_MANAGER_OLD_DUMP_FILE;
		
		if (file_exists($cron_dump)) {
			rename($cron_dump, $backup_dump);
		}
	}
	
	public function enableMaintenanceMode() {
		if (defined('WPMM_PATH')) {
			$wpmm_settings = get_option('wpmm_settings');
		
			$wpmm_settings['general']['status'] = 1;
		
			update_option('wpmm_settings', $wpmm_settings);
		}
	}

	public function disableMaintenanceMode() {
		if (defined('WPMM_PATH')) {
			$wpmm_settings = get_option('wpmm_settings');
		
			$wpmm_settings['general']['status'] = 0;
		
			update_option('wpmm_settings', $wpmm_settings);
		}
	}
	
	public function saveDumpFile() {
		exec(
			sprintf(
				'mysqldump --user=%s --password=%s --host=%s %s > %s',
				DB_USER,
				DB_PASSWORD,
				DB_HOST,
				DB_NAME,
			 	W2MB_DEMO_MANAGER_CRON_PATH . W2MB_DEMO_MANAGER_DUMP_FILE
			)
		);
	}
}

$w2mb_demo_manager_instance = new w2mb_demo_manager_plugin();
$w2mb_demo_manager_instance->init();


// Export to dump file
//exec('C:/xampp/mysql/bin/mysqldump --user=root --host=localhost wordpress > file.sql');

// Import from dump file
//exec('C:/xampp/mysql/bin/mysql --user=root --host=localhost wordpress < file.sql');

?>