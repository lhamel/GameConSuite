<?php
require_once __DIR__.'/../inc/inc.php';
require_once __DIR__.'/../inc/auth.php'; // TODO remove after moving to inc.php

$year = $config['gcs']['year'];

// If not logged in, redirect to login form
if (!$auth->isLogged()) {
  redirect('login.php');
  exit();
}

// NOTE: this form should only be used to create new members (envelopes)

session_start();

$location = "gcs/gm/reg.php";
require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';
require_once INC_PATH.'resources/member/constants.php';
require_once INC_PATH.'resources/member/db.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
if (isset($action)) {

  if ($action == "editMember") {

    $member = array();
    $member['s_fname'] = trim($_POST['s_fname']);
    $member['s_lname'] = trim($_POST['s_lname']);
    $member['s_email'] = trim($_POST['s_email']);
    $member['s_phone'] = trim($_POST['s_phone']);
    $member['s_addr1'] = trim($_POST['s_addr1']);
    $member['s_addr2'] = trim($_POST['s_addr2']);
    $member['s_city'] = trim($_POST['s_city']);
    $member['s_state'] = trim($_POST['s_state']);
    $member['s_zip'] = trim($_POST['s_zip']);
    $member['s_international'] = (isset($_POST['s_international']) && $_POST['s_international']) ? 1 : 0 ;
    $member['b_volunteer'] = (isset($_POST['b_volunteer']) && $_POST['b_volunteer']) ? 1 : 0;
    $member['b_email'] = (isset($_POST['b_email']) && $_POST['b_email']) ? 1 : 0;

    // save values in the session
    $_SESSION['member'] = $member;
    //echo "<pre>".print_r($_SESSION['member'],1).'</pre>';
  }


  // TODO save after user's review
  if ($action == "save") {

    $uid = $auth->getCurrentUser()['uid'];
    if (!$uid) { die("user unknown"); }

    require_once INC_PATH.'resources/member/db.php';
    $id = saveMember($_SESSION['member']);
    if (!$id) { die("save failed"); }
    $associates->associate($id, $uid);
    unset($_SESSION['member']);
    redirect('reg.php');
    exit;
  }

}


$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('form', array('submit'=>'envelope_edit.php'));


//echo "<pre>".print_r($_SESSION['gm'],1)."</pre>";
//echo "<pre>".print_r($gm,1)."</pre>";

$member = isset($_SESSION['member']) ? $_SESSION['member'] : array();
$errors = validateMember($member);

if (!isset($_SESSION['member'])) {

  // this is the first showing, don't list errors
  $smarty->assign('member', $member);
  $smarty->assign('content', $smarty->fetch('gcs/gm/personal.tpl'));
  $smarty->display('base.tpl');

} elseif ($action == "edit" || count($errors)>0) {

  // assign validation errors to smarty
  $smarty->assign('errors', $errors);

  // display member form
  $smarty->assign('member', $member);
  $smarty->assign('content', $smarty->fetch('gcs/gm/personal.tpl'));
  $smarty->display('base.tpl');

} else {

  // no errors, display a review form
  $smarty->assign('member', $member);
  $smarty->assign('userActions', array(
    'save'=>'envelope_edit.php?action=save',
    'edit'=>'envelope_edit.php?action=edit',
  ));
  $smarty->assign('content', $smarty->fetch('gcs/common/review-member.tpl'));

  //$content = "review";
  //$smarty->assign('content', $content);
  $smarty->display('base.tpl');

}

