<?php

// force to use https
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

if (!defined("BASE_PATH")) {
  $rootFilePath = realpath(dirname(__FILE__).'/../');
  $rootFilePath = str_replace("\\", "/", $rootFilePath);
  $currentFilePath = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']; // no relative path this is security problem
  $rootLength = strlen($rootFilePath);
  if (substr($currentFilePath, 0, $rootLength) == $rootFilePath) {
    $scriptPathFromRoot = substr($currentFilePath, $rootLength);
  } else {
    $scriptPathFromRoot = str_replace('/ucon/www','',$_SERVER['SCRIPT_NAME']); // hopefully this is installed at root, else manually str_replace this
  }
/*
echo '<pre>';
echo "$rootFilePath\n";
echo "$currentFilePath\n";
echo "$scriptPathFromRoot\n";
echo '</pre>';
*/

  $countSlashes = substr_count(dirname($scriptPathFromRoot), "/");
  define('BASE_PATH', str_repeat("../",  $countSlashes));
  define('INC_PATH', dirname(__FILE__)."/");
  define('WWW_PATH', dirname(__FILE__)."/../");
  // TODO add admin variable if in /admin
}

if (!defined('DEPTH')) define('DEPTH', BASE_PATH);

if (!isset($config)) $config = array();
$config['page']['basename'] = basename($_SERVER['SCRIPT_NAME']);
$config['page']['location'] = substr($scriptPathFromRoot,1);
$config['page']['depth'] = BASE_PATH;
$config['page']['request'] = $_REQUEST;

require_once dirname(__FILE__).'/../config/config.php';
require_once dirname(__FILE__).'/../config/settings.php';

//echo '<pre>'.print_r($config,1).'</pre>';

if (!function_exists('redirect')) {
  function redirect($page) { header("location: ".$page); exit(); }
}
