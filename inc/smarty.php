<?php
// TODO namespace / autoload
require_once dirname(__FILE__).'/../vendor/smarty/smarty/libs/Smarty.class.php';

$smarty = new Smarty();

$smarty->template_dir = __DIR__.'/templates';
$smarty->compile_dir = __DIR__.'/temp/smarty/templates_c';
$smarty->cache_dir = __DIR__.'/temp/smarty/cache';
$smarty->config_dir = __DIR__.'/temp/smarty/configs';
