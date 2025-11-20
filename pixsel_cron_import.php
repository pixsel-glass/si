<?php

ini_set('display_errors', 0);
set_time_limit(30000);

require_once('admin/config.php');
require_once(DIR_SYSTEM . 'startup.php');


// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$config->load('default');
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);



// Store
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`ssl`, 'www.', '') = '" . $db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
} else {
	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
}

if ($store_query->num_rows) {
	$config->set('config_store_id', $store_query->row['store_id']);
} else {
	$config->set('config_store_id', 0);
}

// Settings
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");

foreach ($query->rows as $result) {
	if (!$result['serialized']) {
		$config->set($result['key'], $result['value']);
	} else {
		$config->set($result['key'], json_decode($result['value'], true));
	}
}

if (!$store_query->num_rows) {
	$config->set('config_url', HTTP_SERVER);
	$config->set('config_ssl', HTTPS_SERVER);
}

if (!$registry->get('config')) {
    die("Module settings not found!");
}

$allowed_login = $registry->get('config')->get('module_pixsel_parser_login');
$allowed_password = $registry->get('config')->get('module_pixsel_parser_password');

$login = isset($_POST['login']) ? $_POST['login'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($login !== $allowed_login || $password !== $allowed_password) {
    http_response_code(403);
    die('Forbidden 403');
}

// Language
$language = new Language($config->get('language_directory'));
$registry->set('language', $language);

$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

$request = new Request();
$registry->set('request', $request);

// Cache
$registry->set('cache', new Cache('file'));

$session = new Session($config->get('session_engine'), $registry);
$registry->set('session', $session);

$document = new Document();
$registry->set('document', $document);


$event = new Event($registry);
$registry->set('event', $event);



// Now initiate your controller and call the function

$action = new Action('extension/module/pixsel_parser/importData');
$result = $action->execute($registry, array('all'));

// $controller = new \ControllerExtensionModulePixselParser($registry);
// $controller->importData('all');

// writeToLog("Import completed!");

$logMessage = arrayToLogString($result);
writeToLog($logMessage);


function writeToLog($message) {
  $logFile = DIR_LOGS . 'pixsel_parser_import.log';
  $currentDate = date('Y-m-d H:i:s');
  file_put_contents($logFile, '(cron) ' . $currentDate . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

function arrayToLogString($array) {
  $logString = "";
  foreach ($array as $key => $value) {
      $logString .= $key . ": " . $value . "; ";
  }
  return rtrim($logString, '; ') . '.';
}