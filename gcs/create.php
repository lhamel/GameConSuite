<?php
require_once '../inc/inc.php';
$year = YEAR;
$location = 'gcs/create.php';


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
  $password = $_POST['s_pass'];
  $repeatPassword = $_POST['s_pass2'];

  $return = $auth->register($email, $password, $repeatPassword, Array(), NULL, true);
  if ($return['error']) {
    // display error in ribbon
    $msg = $return['message'];
    if ($msg == "Password is too weak.") {
      $msg .= "  Try creating a password with four words, the more obscure the better.";
    }
    $smarty->assign('ribbon', $msg);

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
$smarty->assign('type', 'create');
//$smarty->assign('forgotAction', 'pw_reset.php');
$smarty->assign('header', '<h1>Create Account</h1>');
$smarty->assign('action', 'create.php');
$smarty->assign('resendAction', 'resendactivation.php');
$smarty->assign('content', $smarty->fetch('gcs/auth/form_password.tpl'));
$smarty->display('base.tpl');


