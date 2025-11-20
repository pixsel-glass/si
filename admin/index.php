<?php

// Version

define('VERSION', '3.0.3.7');

define('VERSION_CORE', 'ocStore');

define('VERSION_BUILD', '0002');

define('VERSION_LANGPACK', 'UK-EN-RU');



// Configuration

if (is_file('config.php')) {

	require_once('config.php');

}



// Install

if (!defined('DIR_APPLICATION')) {

	header('Location: ../install/index.php');

	exit;

}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('admin');