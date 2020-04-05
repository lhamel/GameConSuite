<?php
require_once '../inc/inc.php';
$year = $config['gcs']['year'];

//
// initialize smarty
require_once INC_PATH.'smarty.php';
$location = 'gcs/login.php';
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
    echo "Forbidden: logout before logging in";
    exit();
  }

  // collect parameters
  $email = $_POST['s_email'];
  $password = $_POST['s_pass'];
  $rememberMe = isset($_POST['rememberMe']);

  $return = $auth->login($email, $password, 1);
  if ($return['error']) {
    // display error in ribbon
    $ribbon = $return['message'];
    if ($ribbon == "Account has not yet been activated.") {
      $smarty->assign('resendAction', 'resendactivation.php');
    } else if ($ribbon == "Email address / password are incorrect.") {
      $ribbon .= "  Do you need to create your account?";
    }
    $smarty->assign('ribbon', $ribbon);

  } else {

    // check to see if there are associated members
    $members = $associates->listAssociates();
    if (count($members)==0) {

      // automatically associate auth with members based on email address
      $uid = $auth->getCurrentUser()['uid'];
      $email = $auth->getCurrentUser()['email'];
      $sql = "insert into ucon_auth_member "
           . "(select $uid,id_member from ucon_member where s_email=?)";
     $succ = $db->execute($sql, array($email));
      if (!$succ) { echo "Sql Error ($sql): ". $db->ErrorMsg(); exit; }

    }

    // TODO find a more suitable page
    redirect('reg.php');
  }
}


$smarty->assign('config', $config);
$smarty->assign('type', 'login');
$smarty->assign('forgotAction', 'forgot.php');
$smarty->assign('createAction', 'create.php');
$smarty->assign('header', '<h1>Login</h1><p>Submitting events, registering for the convention, and reviewing your schedule now requires a login account.  If you have not logged in before, please use the link in the menu to create your account.</p>');
$smarty->assign('action', 'login.php');
$smarty->assign('content', $smarty->fetch('gcs/auth/form_password.tpl'));
$smarty->display('base.tpl');

