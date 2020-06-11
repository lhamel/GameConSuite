<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

if (!is_numeric($_GET['id_member'])) {
  redirect($depth.'admin/db/member/search.php');
}

$tabs = array(
  array('link'=>'admin/gcs/member/index.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Member Summary'),
  array('link'=>'admin/gcs/member/order.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Order'),
  //array('link'=>'admin/gcs/member/addBadge.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Add Badge'),
  // array('link'=>'admin/gcs/member/addItem.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Add Item'),
  //array('link'=>'admin/gcs/member/addPayment.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Add Payment'),
  //array('link'=>'admin/gcs/member/checkin.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Check In'),
  //array('link'=>'admin/gcs/member/history.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'History'),
  array('link'=>'admin/gcs/member/confirm.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Confirm'),
  //array('link'=>'admin/gcs/member/print.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Prereg Print'),
);
$smarty->assign('tabs', $tabs);

