<?php
require_once '../inc/inc.php';
$year = YEAR;
$location = 'gcs/login.php';


require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

if (isset($_POST['s_email'])) {
  /**
   *  Respond to a request to create an account
   */
  require_once __DIR__.'/../inc/inc.php';
  require_once __DIR__.'/../inc/auth.php'; // TODO remove after moving to inc.php

  // if the user is currently logged in, this is an error
  if ($auth->isLogged()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden: logout before creating an account";
    exit();
  }

  // collect parameters
  $email = $_POST['s_email'];

  $return = $auth->requestReset($email, true);
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


$smarty->assign('config', $config);
$smarty->assign('type', 'reset-request');
$smarty->assign('header', '<h1>Reset Password</h1>');
$smarty->assign('action', 'forgot.php');
$smarty->assign('content', $smarty->fetch('gcs/auth/form_password.tpl'));
$smarty->display('base.tpl');



