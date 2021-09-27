<?php

class VP_W2MB_Option_Control_Field_Restore extends VP_W2MB_Control_Field
{

	public function __construct()
	{
		parent::__construct();
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

	public function render()
	{
		$this->_setup_data();
		return VP_W2MB_View::instance()->load('option/restore', $this->get_data());
	}

}

/**
 * EOF
 */