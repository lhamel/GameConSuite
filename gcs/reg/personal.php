<?php
require_once '../../inc/inc.php';
$location = $config['page']['location'];
$year = YEAR;

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

session_start();
if (!isset($_SESSION['reg']['member'])) {

	require_once INC_PATH.'resources/member/constants.php';
  $smarty->assign('constants', $constants);

	// checking for this first ensure the user is not presented with an error 
	// message if it's the first time visiting
	$_SESSION['reg']['member'] = array();
  $smarty->assign('content', $smarty->fetch('gcs/reg/personal.tpl'));
} else {

	// load the member from the sessionobject
  $member = $_SESSION['reg']['member'];

	// TODO or any POST
	// check if this is the member form
	if ($_POST['action'] == 'editMember') {

	  $member['s_fname'] = trim($_POST['s_fname']);
	  $member['s_lname'] = trim($_POST['s_lname']);
	  $member['s_email'] = trim($_POST['s_email']);
	  $member['s_phone'] = trim($_POST['s_phone']);
	  $member['s_addr1'] = trim($_POST['s_addr1']);
	  $member['s_addr2'] = trim($_POST['s_addr2']);
	  $member['s_city'] = trim($_POST['s_city']);
	  $member['s_state'] = trim($_POST['s_state']);
	  $member['s_zip'] = trim($_POST['s_zip']);
	  $member['s_international'] = trim($_POST['s_international']);
	  $member['b_volunteer'] = trim($_POST['b_volunteer']);
	  $member['b_email'] = trim($_POST['b_email']);
	
    // save values in the session
	  $_SESSION['reg']['member'] = $member;
  
    include_once dirname(__FILE__).'/_personal.php';
	  if (count($errors) == 0) {
  	  header('location: review.php');
	  }
	} else {
		// this will indicate if there are errors
    include_once dirname(__FILE__).'/_personal.php';
	}

	// these are process-level errors
	if (isset($error)) {
    $smarty->assign('error', $error);
  }

  // these are field-level errors
	if (isset($errors) && count($errors)>0) {
    $smarty->assign('errors', $errors);
	}

  require_once INC_PATH.'resources/member/constants.php';
  $smarty->assign('constants', $constants);
  $smarty->assign('config', $config);
//  echo "<pre style=\"text-align: left;\">".print_r($constants,1)."</pre>";
	
  $smarty->assign('member', $member);
  $smarty->assign('content', $smarty->fetch('gcs/reg/personal.tpl'));
}

// TODO add personal.php to tabs
include '../events/_tabs.php';

$actions = array(
  'list'=>'unscheduled.php',
  'detail'=>'schedule.php?id_event=',
);


$smarty->assign('constants', $constants);
$smarty->assign('config', $config);
//$smarty->assign('cart', $_SESSION['reg']['cart']);
$smarty->assign('actions', $actions);


// render the page
//$smarty->assign('content', $content);
$smarty->display('base.tpl');
