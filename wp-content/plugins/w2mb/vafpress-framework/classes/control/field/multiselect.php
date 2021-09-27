<?php

class VP_W2MB_Control_Field_MultiSelect extends VP_W2MB_Control_FieldMulti implements VP_W2MB_MultiSelectable
{

	public function __construct()
	{
		parent::__construct();
		$this->_value = array();
	}

	public static function withArray($arr = array(), $class_name = null)
	{
		if(is_null($class_name))
			$instance = new self();
		else
			$instance = new $class_name;
		$instance->_basic_make($arr);
		
		return $instance;
	}

	public function render($is_compact = false)
	{
		$this->_setup_data();
		$this->add_data('is_compact', $is_compact);
		return VP_W2MB_View::instance()->load('control/multiselect', $this->get_data());
	}

}

/**
 * EOF
 */