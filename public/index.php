<?php

	// This code requires PHP 5.3 or newer, so if we don't have it - complain
	if (PHP_VERSION_ID < 50300) die('This software requires PHP 5.3.0 or newer, but you have an older version. Please upgrade');

	// Set standard constants needed elsewhere
	defined('DS') || define('DS', DIRECTORY_SEPARATOR);
	defined('CASPAR_PATH') || define('CASPAR_PATH', realpath(getcwd() . DS . '..' . DS) . DS . 'caspar' . DS);
	defined('THEBUGGENIE_SESSION_NAME') || define('CASPAR_SESSION_NAME', 'THEBUGGENIE');
	
	// Include the "engine" script, which initializes and sets up stuff
	require CASPAR_PATH . 'bootstrap.inc.php';
	
	// Trigger the framework's start function
	\caspar\core\Caspar::go();
