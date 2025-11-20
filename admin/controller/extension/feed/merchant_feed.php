<?php

if (version_compare(PHP_VERSION, '7.2') >= 0) {
	$phpv = '72_73';
} elseif (version_compare(PHP_VERSION, '7.1') >= 0) {
	$phpv = '71';
} elseif (version_compare(PHP_VERSION, '5.6.0') >= 0) {
	$phpv = '56_70';
} else {
	echo "Sorry! Version for PHP 5.6+!";
	exit;
}

require_once DIR_SYSTEM . 'library/feed_merchant/feed_merchant_' . $phpv . '.php';