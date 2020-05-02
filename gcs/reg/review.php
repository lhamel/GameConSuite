<?php
require_once '../../inc/inc.php';
$location = $config['page']['location'];
$year = YEAR;

//session_unset();
include_once dirname(__FILE__).'/_review.php';

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

// TODO avoid showing the error if user goes here first
// if an error was detected, then show it
if (isset($error)) {
  $smarty->assign('error', $error);
}

// allow users to complete the purchase because the order and info must be 
// valid to get here
$actions = array('incomplete' => 'cart.php', 'complete' => 'purchase.php');

$smarty->assign('member', $_SESSION['reg']['member']);
$smarty->assign('cart', $_SESSION['reg']['cart']);
$smarty->assign('config', $config);
//$smarty->assign('constants', $constants);
$smarty->assign('actions', $actions);
$content = $smarty->fetch('gcs/reg/review.tpl');

include '../events/_tabs.php';

// render the page
$smarty->assign('content', $content);
$smarty->display('base.tpl');
