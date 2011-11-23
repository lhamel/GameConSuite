<?php
require_once '../inc/inc.php';
require_once INC_PATH.'db/db.php';

require_once INC_PATH.'resources/member/db.php';
require_once INC_PATH.'resources/event/db.php';

// last validation check...
session_start();
if (!isset($_SESSION['gm']) 
    || !isset($_SESSION['gm']['member'])
    || !isset($_SESSION['gm']['events'])) {
  die('not set');
  redirect('display.php');
}

if (count(validateMember($_SESSION['gm']['member']))>0) {
  die('not valid member');
  redirect('display.php');
}

if (count($_SESSION['gm']['events']) <= 0) {
  die('0 events');
  redirect('display.php');
}

foreach ($_SESSION['gm']['events'] as $idx => $event) {
  if (count(validateEvent($event)) > 0) {
    die('not valid event '.$event['idx']);
    redirect('display.php');
  }
}

// store the GM information
$db->StartTrans();
$id = saveMember($_SESSION['gm']['member']);
$_SESSION['gm']['member']['id_member'] = $id;
$events = $_SESSION['gm']['events'];
saveEvents($id, $events);
$db->CompleteTrans();

//// create a 1-indexed number for each event
//$ordinal = 1;
//foreach($_SESSION['gm']['events'] as $idx=>$event) {
//  $_SESSION['gm']['events'][$idx]['gmEventNumber'] = $ordinal;
//  $ordinal++;
//}


//
// initialize smarty
require_once INC_PATH.'/smarty.php';
$location = "gm/submit.php";
$year = YEAR;
require_once INC_PATH.'/menu.php';
$smarty->assign('UCON', $UCON);
$smarty->assign('config', $config);

require_once INC_PATH.'resources/event/constants.php';
$smarty->assign('constants', $constants);

$smarty->assign('member', $_SESSION['gm']['member']);
$smarty->assign('events', $events);

// fetch the email body
$body = $smarty->fetch('gcs/gm/email.tpl');

// the subject is on the first line, so parse that out
$lines = explode("\n", $body);
$subject = trim(array_shift($lines));
$body = join("\n", $lines);

// send email to GM and Event coordinator
$regEmail = $config['email']['registration'];
$gmEmail = $_SESSION['gm']['member']['s_email'];
$ok = mail($gmEmail, $subject, $body, 'From: ' . $regEmail);
$ok = mail($regEmail, $subject, $body, 'From: ' . $gmEmail);

$smarty->assign('content', $smarty->fetch('gm/thanks.tpl'));

//$smarty->assign('content', '<pre>'.wordwrap($body,80).'</pre>');
$smarty->display('base.tpl');

//unset($_SESSION['gm']);
