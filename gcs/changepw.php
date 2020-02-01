<?php
require_once '../inc/inc.php';

$year = $config['gcs']['year'];
$location = 'gcs/changepw.php';

//
// initialize smarty
require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';
$smarty->assign('config', $config);

if (isset($_POST['s_pass'])) {
  /**
   *  Respond to a request to create an account
   */
  require_once __DIR__.'/../inc/inc.php';
  require_once __DIR__.'/../inc/auth.php'; // TODO remove after moving to inc.php

  // if the user is currently logged in, this is an error
  if (!$auth->isLogged()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden: must be logged in to change your password";
    exit();
  }

  // collect parameters
  $currpass = $_POST['s_oldpass'];
  $newpass = $_POST['s_pass'];
  $repeatpass = $_POST['s_pass2'];
  $currUser = $auth->getCurrentUser();
  $uid = $currUser['uid'];

  $return = $auth->changePassword($uid, $currpass, $newpass, $repeatpass);
  if ($return['error']) {
    // display error in ribbon
    $smarty->assign('ribbon', $return['message']);

  } else {
    $smarty->assign('header', '<h1>Account Creation Successful</h1>');
    $smarty->assign('successMsg', '<p>'.$return['message'].'</p>');
    $smarty->assign('content', $smarty->fetch('gcs/auth/form_done.tpl'));
    $smarty->display('base.tpl');
    exit;
  }
}


$smarty->assign('type', 'change');
$smarty->assign('header', '<h1>Change Password</h1>');
$smarty->assign('action', 'changepw.php');
$smarty->assign('content', $smarty->fetch('gcs/auth/form_password.tpl'));
$smarty->display('base.tpl');



