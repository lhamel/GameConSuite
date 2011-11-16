<?php
require_once dirname(__FILE__).'/lib/Smarty-2.6.26/libs/Smarty.class.php';
//require_once dirname(__FILE__).'/../lib/Smarty-3.0b5/libs/Smarty.class.php';
$smarty = new Smarty();

$smarty->template_dir = dirname(__FILE__).'/templates';
$smarty->compile_dir = dirname(__FILE__).'/tmp/templates_c';
$smarty->cache_dir = dirname(__FILE__).'/tmp/cache';
$smarty->config_dir = dirname(__FILE__).'/tmp/config';

//$smarty->assign('name', 'Ned');
//$smarty->display('index.tpl');
