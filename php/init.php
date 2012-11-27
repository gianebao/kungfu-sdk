<?php
// Load the autoloader class
define('KUNGFU_ROOT', dirname(__FILE__));
define('KUNGFU_CLASS', KUNGFU_ROOT . '/classes');
define('KUNGFU_CONFIG', KUNGFU_ROOT . '/config');
define('KUNGFU_CERT', KUNGFU_ROOT . '/cert');

if (!defined('KUNGFU_DOMAIN'))
{
    define('KUNGFU_DOMAIN', 'api.matchmove.com');
}

require_once KUNGFU_CLASS . '/kungfu/core.php';
if (!class_exists('Kungfu'))
{
    require_once KUNGFU_CLASS . '/kungfu.php';
}

spl_autoload_register(array('Kungfu', 'auto_load'));
set_error_handler(array('Kungfu', 'error_handle'), E_USER_ERROR);