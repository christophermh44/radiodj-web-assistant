<?php session_start();

$dev = true;

if ($dev) {
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 'On');
}

# includes
spl_autoload_register(function($className) {
	if (is_file(__DIR__.'/classes/'.$className.'.php')) {
		include_once __DIR__.'/classes/'.$className.'.php';
		$className::static_init();
	}
});

$eh = function() {
	var_dump(func_get_args());
	debug_print_backtrace();
};

set_exception_handler($eh);
set_error_handler($eh);