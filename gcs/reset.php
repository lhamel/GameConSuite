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
  if ($auth->isLogged()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden: use Change Password instead";
    exit();
  }

  // collect parameters
  $resetkey = $_POST['s_resetkey'];
  $newpass = $_POST['s_pass'];
  $repeatpass = $_POST['s_pass2'];

  $return = $auth->resetPass($resetkey, $newpass, $repeatpass);
  if ($return['error']) {
    // display error in ribbon
    $smarty->assign('ribbon', $return['message']);

  } else {
    $smarty->assign('config', $config);
    $smarty->assign('header', '<h1>Account Creation Successful</h1>');
    $smarty->assign('successMsg', '<p>'.$return['message'].'</p>');
    $smarty->assign('content', $smarty->fetch('gcs/auth/form_done.tpl'));
    $smarty->display('base.tpl');
    exit;
  }
}


$smarty->assign('type', 'reset');
$smarty->assign('header', '<h1>Reset Password</h1>');
$smarty->assign('action', 'reset.php');
$smarty->assign('content', $smarty->fetch('gcs/auth/form_password.tpl'));
$smarty->display('base.tpl');



