<?php

//include '_personal.php';
//if (isset($errors)) {
//	header('location: personal.php');
//	exit;
//}

// start session, check personal and review information
include '_review.php';
if (isset($errors) && count($errors)>0) {
  header('location: review.php');
  exit;
}

////////////////////////////////////////////////////////////////////////////////
// save to database

require_once '../../inc/inc.php';
require_once INC_PATH.'db/db.php';

require_once INC_PATH.'resources/member/db.php';
require_once INC_PATH.'resources/cart/CartReader.php';
require_once INC_PATH.'resources/cart/CartSerializer.php';

$serializer = new CartSerializer();

$db->StartTrans();
$id = saveMember($_SESSION['reg']['member']);
$_SESSION['reg']['member']['id_member'] = $id_member;
$serializer->saveToDatabase($db, $id, YEAR, $_SESSION['reg']['cart']);
$db->CompleteTrans();

////////////////////////////////////////////////////////////////////////////////
// email registration

// send email to GM and Event coordinator
$regEmail = $config['email']['registration'];
$memberEmail = $_SESSION['reg']['member']['s_email'];

require_once INC_PATH.'mail.php';

// fetch the email body
$body = $smarty->fetch('gcs/reg/email.tpl');
$lines = explode("\n", $body);
$subject = trim(array_shift($lines));
$body = join("\n", $lines);
//$ok = mail($regEmail, $subject, $body, 'From: ' . $memberEmail);
gcs_mail($regEmail, $subject, $body, array('from',$memberEmail));

$body = $smarty->fetch('gcs/reg/customerEmail.tpl');
$lines = explode("\n", $body);
$subject = trim(array_shift($lines));
$body = join("\n", $lines);
//$ok = mail($memberEmail, $subject, $body, 'From: ' . $regEmail);
gcs_mail($memberEmail, $subject, $body, array('from',$regEmail));


