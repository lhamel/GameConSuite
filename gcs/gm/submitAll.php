<?php
require_once '../../inc/inc.php';
require_once INC_PATH.'db/db.php';

include_once __DIR__.'/../../inc/auth.php';

// If not logged in, redirect to login form
if (!$auth->isLogged()) {
  redirect('../login.php');
  exit();
}

require_once INC_PATH.'resources/member/db.php';
require_once INC_PATH.'resources/event/db.php';

session_start();

//echo "<pre>Session\n".print_r($_SESSION["gm"],1)."</pre>";
$id = $_SESSION['gm']['member']; // get the selected member ID
$uid = $auth->getCurrentUser()['uid'];
$members = $associates->listAssociates($uid);
//echo "<pre>($id)\n".print_r($members,1).'</pre>';
if (!isset($members[$id])) {
  redirect("display.php"); exit;
  echo "not allowed ($id)"; exit;
}

$member = $members[$id];
$regEmail = $config['email']['registration'];
$gmEmail = $members[$id]['s_email'];


// last validation check...
if (!isset($_SESSION['gm']) 
    || !isset($_SESSION['gm']['member'])
    || !isset($_SESSION['gm']['events'])) {
  die('not set');
  redirect('display.php');
  exit;
}

if (!is_numeric($_SESSION['gm']['member'])) {
  die('not valid member');
  redirect('display.php');
  exit;
}

if (count($_SESSION['gm']['events']) <= 0) {
  die('0 events');
  redirect('display.php');
  exit;
}

foreach ($_SESSION['gm']['events'] as $idx => $event) {
  if (count(validateEvent($event)) > 0) {
    die('not valid event '.$event['idx']);
    redirect('display.php');
    exit;
  }
}


// store the GM information
$db->StartTrans();
$id = $_SESSION['gm']['member'];
$_SESSION['gm']['member'] = $id;
$events = $_SESSION['gm']['events'];
saveEvents($id, $events);
$db->CompleteTrans();


//
// initialize smarty
$location = "gm/submit.php";
require_once INC_PATH.'/smarty.php';
require_once INC_PATH.'/layout/menu.php';
require_once INC_PATH.'resources/event/constants.php';

$year = $config['gcs']['year'];
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('member', $member);
$smarty->assign('events', $events);

// fetch the email body
$body = $smarty->fetch('gcs/gm/email.tpl');

// TESTING EMAIL template
//$smarty->assign('content', '<pre>'.wordwrap($body,80).'</pre>'); $smarty->display('base.tpl'); exit;


// the subject is on the first line, so parse that out
$lines = explode("\n", $body);
$subject = trim(array_shift($lines));
$body = join("\n", $lines);

// send email to GM and Event coordinator
require_once INC_PATH."/mail.php";
$ok = ucon_mail($gmEmail, $subject, $body, array('from',$regEmail));
if (!$ok) {error_log("submitAll.php: Error sending email from registration to $gmEmail");}
$ok = ucon_mail($regEmail, $subject, $body, array('from',$gmEmail));
if (!$ok) {error_log("submitAll.php: Error sending email to registration from $gmEmail");}
if (trim($regEmail)=="" || trim($regEmail)=="") {
  error_log("empty email reg ($regEmail) or gm ($gmEmail)");
}

$smarty->assign('content', $smarty->fetch('gcs/gm/thanks.tpl'));
$smarty->display('base.tpl');

// unset($_SESSION['gm']);
