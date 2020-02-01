<?php
require_once '../inc/inc.php';
$year = $config['gcs']['year'];


require_once INC_PATH.'smarty.php';
$location = "gcs/login.php";
require_once INC_PATH.'layout/menu.php';


// if the form was posted
if (isset($_POST['s_key'])) {

  require_once __DIR__.'/../inc/inc.php';
  require_once __DIR__.'/../inc/auth.php'; // TODO remove after moving to inc.php


  // disallow a person who is logged in
  if ($auth->isLogged()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden: logout before activating an account";
    exit();
  }

  $key = $_POST['s_key'];
  $return = $auth->activate($key);
  if ($return['error']) {
    // display error in ribbon
    $ribbon = $return['message'];
    $smarty->assign('ribbon', $ribbon);

  } else {

    $smarty->assign('config', $config);
    $smarty->assign('header', '<h1>Account Activation Successful</h1>');
    $smarty->assign('successMsg', '<p>'.$return['message'].'  <a href="login.php">Login.</a></p>');
    $smarty->assign('content', $smarty->fetch('gcs/auth/form_done.tpl'));
    $smarty->display('base.tpl');
    exit;
  }
}

//
$smarty->assign('config', $config);
$smarty->assign('resendAction','resendactivation.php');
$smarty->assign('header', '<h1>Activate Account</h1>');
$smarty->assign('action', 'activate.php');
$smarty->assign('content', $smarty->fetch('gcs/auth/form_activate.tpl'));
$smarty->display('base.tpl');



