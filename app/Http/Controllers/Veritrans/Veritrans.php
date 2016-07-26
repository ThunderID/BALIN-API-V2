<?php

// This snippet due to the braintree_php.
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
    throw new Exception('PHP version >= 5.2.1 required');
}

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('Veritrans needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Veritrans needs the JSON PHP extension.');
}

// Configurations
require_once('Veritrans_Config.php');

// Veritrans API Resources
require_once('Veritrans_Transaction.php');

// Plumbing
require_once('Veritrans_ApiRequestor.php');
require_once('Veritrans_Notification.php');
require_once('Veritrans_VtDirect.php');
require_once('Veritrans_VtWeb.php');

// Sanitization
require_once('Veritrans_Sanitizer.php');
