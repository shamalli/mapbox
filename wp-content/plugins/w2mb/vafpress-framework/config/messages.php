<?php

return array(

	////////////////////////////////////////
	// Localized JS Message Configuration //
	////////////////////////////////////////

	/**
	 * Validation Messages
	 */
	'validation' => array(
		'alphabet'     => __('Value needs to be Alphabet', 'W2MB'),
		'alphanumeric' => __('Value needs to be Alphanumeric', 'W2MB'),
		'numeric'      => __('Value needs to be Numeric', 'W2MB'),
		'email'        => __('Value needs to be Valid Email', 'W2MB'),
		'url'          => __('Value needs to be Valid URL', 'W2MB'),
		'maxlength'    => __('Length needs to be less than {0} characters', 'W2MB'),
		'minlength'    => __('Length needs to be more than {0} characters', 'W2MB'),
		'maxselected'  => __('Select no more than {0} items', 'W2MB'),
		'minselected'  => __('Select at least {0} items', 'W2MB'),
		'required'     => __('This is required', 'W2MB'),
	),

	/**
	 * Import / Export Messages
	 */
	'util' => array(
		'import_success'    => __('Import succeed, option page will be refreshed..', 'W2MB'),
		'import_failed'     => __('Import failed', 'W2MB'),
		'export_success'    => __('Export succeed, copy the JSON formatted options', 'W2MB'),
		'export_failed'     => __('Export failed', 'W2MB'),
		'restore_success'   => __('Restoration succeed, option page will be refreshed..', 'W2MB'),
		'restore_nochanges' => __('Options identical to default', 'W2MB'),
		'restore_failed'    => __('Restoration failed', 'W2MB'),
	),

	/**
	 * Control Fields String
	 */
	'control' => array(
		// select2vp select box
		'select2vp_placeholder' => __('Select option(s)', 'W2MB'),
		// fontawesome chooser
		'fac_placeholder'     => __('Select an Icon', 'W2MB'),
	),

);

/**
 * EOF
 */