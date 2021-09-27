<?php 

class w2mb_locations_levels_manager {
	
	public function __construct() {
		add_action('admin_menu', array($this, 'menu'));
	}

	public function menu() {
		if (defined('W2MB_DEMO') && W2MB_DEMO) {
			$capability = 'publish_posts';
		} else {
			$capability = 'manage_options';
		}

		add_submenu_page('w2mb_settings',
				esc_html__('Locations levels', 'W2MB'),
				esc_html__('Locations levels', 'W2MB'),
				$capability,
				'w2mb_locations_levels',
				array($this, 'w2mb_locations_levels')
		);
	}
	
	public function w2mb_locations_levels() {
		if (isset($_GET['action']) && $_GET['action'] == 'add') {
			$this->addOrEditLocationsLevel();
		} elseif (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['level_id'])) {
			$this->addOrEditLocationsLevel($_GET['level_id']);
		} elseif (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['level_id'])) {
			$this->deleteLocationsLevel($_GET['level_id']);
		} else {
			$this->showLocationsLevelsTable();
		}
	}
	
	public function showLocationsLevelsTable() {
		global $w2mb_instance;
		
		$locations_levels = $w2mb_instance->locations_levels;
	
		$locations_levels_table = new w2mb_manage_locations_levels_table();
		$locations_levels_table->prepareItems($locations_levels);
	
		w2mb_renderTemplate('locations/locations_levels_table.tpl.php', array('locations_levels_table' => $locations_levels_table));
	}
	
	public function addOrEditLocationsLevel($level_id = null) {
		global $w2mb_instance;
	
		$locations_levels = $w2mb_instance->locations_levels;
	
		if (!$locations_level = $locations_levels->getLevelById($level_id))
			$locations_level = new w2mb_locations_level();
	
		if (w2mb_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2mb_locations_levels_nonce'], W2MB_PATH)) {
			$validation = new w2mb_form_validation();
			$validation->set_rules('name', esc_html__('Level name', 'W2MB'), 'required');
			$validation->set_rules('in_address_line', esc_html__('In address line', 'W2MB'), 'is_checked');
			$validation->set_rules('allow_add_term', esc_html__('Allow add term', 'W2MB'), 'is_checked');
	
			if ($validation->run()) {
				if ($locations_level->id) {
					if ($locations_levels->saveLevelFromArray($level_id, $validation->result_array())) {
						w2mb_addMessage(esc_html__('Level was updated successfully!', 'W2MB'));
					}
				} else {
					if ($locations_levels->createLevelFromArray($validation->result_array())) {
						w2mb_addMessage(esc_html__('Level was created succcessfully!', 'W2MB'));
					}
				}
				$this->showLocationsLevelsTable();
				//wp_redirect(admin_url('admin.php?page=w2mb_locations_levels'));
				//die();
			} else {
				$locations_level->buildLevelFromArray($validation->result_array());
				w2mb_addMessage($validation->error_array(), 'error');
	
				w2mb_renderTemplate('locations/add_edit_locations_level.tpl.php', array('locations_level' => $locations_level, 'locations_level_id' => $level_id));
			}
		} else {
			w2mb_renderTemplate('locations/add_edit_locations_level.tpl.php', array('locations_level' => $locations_level, 'locations_level_id' => $level_id));
		}
	}
	
	public function deleteLocationsLevel($level_id) {
		global $w2mb_instance;
	
		$locations_levels = $w2mb_instance->locations_levels;
		if ($locations_level = $locations_levels->getLevelById($level_id)) {
			if (w2mb_getValue($_POST, 'submit')) {
				if ($locations_levels->deleteLevel($level_id))
					w2mb_addMessage(esc_html__('Level was deleted successfully!', 'W2MB'));
	
				$this->showLocationsLevelsTable();
				//wp_redirect(admin_url('admin.php?page=w2mb_locations_levels'));
				//die();
			} else
				w2mb_renderTemplate('delete_question.tpl.php', array('heading' => esc_html__('Delete locations level', 'W2MB'), 'question' => sprintf(esc_html__('Are you sure you want delete "%s" locations level?', 'W2MB'), $locations_level->name), 'item_name' => $locations_level->name));
		} else
			$this->showLocationsLevelsTable();
	}
}

?>