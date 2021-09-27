<?php

class VP_W2MB_Control_Field_Fontawesome extends VP_W2MB_Control_FieldMulti
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
		$arr['items']['data'][] = array(
			'source' => 'function',
			'value' => 'vp_w2mb_get_fontawesome_icons',
		);

		$instance->_basic_make($arr);
		
		return $instance;
	}

	public function render($is_compact = false)
	{
		$this->_setup_data();
		$this->add_data('is_compact', $is_compact);
		return VP_W2MB_View::instance()->load('control/fontawesome', $this->get_data());
	}

}

/**
 * EOF
 */