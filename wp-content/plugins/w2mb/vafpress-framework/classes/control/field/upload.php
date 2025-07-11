<?php

class VP_W2MB_Control_Field_Upload extends VP_W2MB_Control_Field
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

	public function _setup_data()
	{
		$preview = VP_W2MB_Util_Res::get_preview_from_url($this->get_value());
		$this->add_data('preview', $preview);
		parent::_setup_data();
	}

	public function render($is_compact = false)
	{
		$this->_setup_data();
		$this->add_data('is_compact', $is_compact);
		return VP_W2MB_View::instance()->load('control/upload', $this->get_data());
	}

}

/**
 * EOF
 */