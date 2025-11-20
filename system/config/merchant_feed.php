<?php
// Site
$_['site_base']        = HTTP_SERVER;
$_['site_ssl']         = HTTPS_SERVER;

// Url
$_['url_autostart']    = false;

// Database
$_['db_autostart']     = true;
$_['db_type']          = DB_DRIVER; // mpdo, mssql, mysql, mysqli or postgre
$_['db_hostname']      = DB_HOSTNAME;
$_['db_username']      = DB_USERNAME;
$_['db_password']      = DB_PASSWORD;
$_['db_database']      = DB_DATABASE;
$_['db_port']          = DB_PORT;

// Session
$_['session_autostart'] = false;
$_['session_engine']     = 'db';
$_['session_name']       = 'OCSESSID';

$registry = new Registry();
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

$query_merchant_feed = $db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'merchant_feed_status'");

if ($query_merchant_feed->num_rows) {
	$_['merchant_feed_status'] = $query_merchant_feed->row['value'];
}else{
	$_['merchant_feed_status'] = 0;
}

//library
$_['library_autoload'] = array(
	'response'
);

// Actions
$_['action_pre_action']  = array(
	'startup/session',
	'startup/startup',
	'startup/error',
	'startup/event',
	'startup/maintenance',
	'startup/seo_url'
);

// Action Events
$_['action_event'] = array(
	'view/*/before'                         => 'event/theme',
);

$_['action_default']       = 'extension/feed/merchant_feed/generatecron';
$_['action_router']        = 'extension/feed/merchant_feed/generatecron';
$_['action_error']         = 'error/not_found';