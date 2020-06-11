<?php
require_once '../../../inc/inc.php';
$year = $config['gcs']['year'];
require_once INC_PATH.'smarty.php';
$location = 'admin/gcs/member/confirm.php';
require_once INC_PATH.'layout/adminmenu.php';
$id_member = $_REQUEST['id_member'];

if (isset($_REQUEST['action']) && $_REQUEST['action']=='submit') {
  require_once INC_PATH.'db/db.php';
  $sql = 'select s_email from ucon_member where id_member=?';
  $toEmail = $db->getOne($sql, array($id_member));

  $subject = $_POST['subject'];
  if (!$subject) $subject = "U-Con $year Registration";
  $from = $_POST['from'];
  if (!$from) $from = 'contact@ucon-gaming.org';
  $cc = $_POST['cc'];
  if (!$cc) $cc = '';
  $message = $_POST['message'];
  $message = wordwrap($message)."\n\n";
  
  $header = "Cc:".$cc."\r\n";
  $header .="From:".$from."\r\n";

  //mail($toEmail, $subject, $message, $header);
  require_once INC_PATH.'mail.php';
  gcs_mail($toEmail, $subject, $message, array('cc'=>$cc, 'from'=>$from));

  redirect('index.php?id_member='.$id_member);
  exit();
}

$smarty->assign('config', $config);
$actions = array('insert'=>'_confirm.php?id_member='.$id_member,
                 'submit'=>'confirm.php?action=submit');
$smarty->assign('actions', $actions);
$smarty->assign('id_member', $id_member);
$content = $smarty->fetch('gcs/admin/member/confirm.tpl');

// render the page
include '_tabs.php';
$smarty->assign('title', 'Register - U-Con Gaming Convention, Ann Arbor Michigan');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
