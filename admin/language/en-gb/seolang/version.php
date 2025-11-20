<?php
$_['seolang_version'] = '40.0';
$_['seolang_model'] = 'SEO multilang';

if (!defined('SC_VERSION')) define('SC_VERSION', (int)substr(str_replace('.','',VERSION), 0,2));

if (SC_VERSION > 22) {
	if (file_exists(DIR_APPLICATION. 'controller/module/seolang.php')) {
		@unlink(DIR_APPLICATION. 'controller/module/seolang.php');
	}
}
if (SC_VERSION < 22) {
	if (file_exists(DIR_APPLICATION. 'controller/extension/module/seolang.php')) {
		@unlink(DIR_APPLICATION. 'controller/extension/module/seolang.php');
	}
	$files_extension_module = glob(DIR_APPLICATION. 'controller/extension/module/*.*');
	if (!$files_extension_module && is_dir(DIR_APPLICATION. 'controller/extension/module')) {
    	rmdir(DIR_APPLICATION. 'controller/extension/module');
	}
}