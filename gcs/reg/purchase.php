<?php
require_once '../../inc/inc.php';
$location = $config['page']['location'];
$year = YEAR;

include_once INC_PATH.'resources/event/constants.php';

session_start();

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

// this stuff must be in place to process the email
$smarty->assign('member', $_SESSION['reg']['member']);
$smarty->assign('cart', $_SESSION['reg']['cart']);
$smarty->assign('constants', $constants);
$smarty->assign('config', $config);

//session_unset();
include_once dirname(__FILE__).'/_purchase.php';

// TODO avoid showing the error if user goes here first
// if an error was detected, then show it
if (isset($error)) {
  $smarty->assign('error', $error);
}

$smarty->assign('id_member', $id); // populated by _purchase.php
$smarty->assign('content', $smarty->fetch('gcs/reg/purchase.tpl'));

// render the page
include '../events/_tabs.php';
$smarty->display('base.tpl');

// destroy session to prevent resubmitting!
unset($_SESSION['reg']);
