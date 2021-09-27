<?php

/*
|--------------------------------------------------------------------------
| Vafpress Framework Constants
|--------------------------------------------------------------------------
*/

defined('VP_W2MB_VERSION')     or define('VP_W2MB_VERSION'    , '2.0-beta');
defined('VP_W2MB_NAMESPACE')   or define('VP_W2MB_NAMESPACE'  , 'VP_W2MB_');
defined('VP_W2MB_DIR')         or define('VP_W2MB_DIR'        , W2MB_PATH . 'vafpress-framework');
defined('VP_W2MB_DIR_NAME')    or define('VP_W2MB_DIR_NAME'   , basename(VP_W2MB_DIR));
defined('VP_W2MB_IMAGE_DIR')   or define('VP_W2MB_IMAGE_DIR'  , VP_W2MB_DIR . '/public/img');
defined('VP_W2MB_CONFIG_DIR')  or define('VP_W2MB_CONFIG_DIR' , VP_W2MB_DIR . '/config');
defined('VP_W2MB_DATA_DIR')    or define('VP_W2MB_DATA_DIR'   , VP_W2MB_DIR . '/data');
defined('VP_W2MB_CLASSES_DIR') or define('VP_W2MB_CLASSES_DIR', VP_W2MB_DIR . '/classes');
defined('VP_W2MB_VIEWS_DIR')   or define('VP_W2MB_VIEWS_DIR'  , VP_W2MB_DIR . '/views');
defined('VP_W2MB_INCLUDE_DIR') or define('VP_W2MB_INCLUDE_DIR', VP_W2MB_DIR . '/includes');

defined('VP_W2MB_URL')         or define('VP_W2MB_URL'        , W2MB_URL . 'vafpress-framework');
defined('VP_W2MB_PUBLIC_URL')  or define('VP_W2MB_PUBLIC_URL' , VP_W2MB_URL        . '/public');
defined('VP_W2MB_IMAGE_URL')   or define('VP_W2MB_IMAGE_URL'  , VP_W2MB_PUBLIC_URL . '/img');
defined('VP_W2MB_INCLUDE_URL') or define('VP_W2MB_INCLUDE_URL', VP_W2MB_URL        . '/includes');

// Get the start time and memory usage for profiling
defined('VP_W2MB_START_TIME')  or define('VP_W2MB_START_TIME', microtime(true));
defined('VP_W2MB_START_MEM')   or define('VP_W2MB_START_MEM',  memory_get_usage());

/**
 * EOF
 */