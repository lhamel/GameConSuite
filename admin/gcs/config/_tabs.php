<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/config/purchaseitems.php', 'label'=>'Purchase Items'),
//   array('link'=>'admin/gcs/event/index.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Event'),
//   array('link'=>'admin/gcs/event/copyedit.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Copy Edit'),
//   array('link'=>'admin/gcs/event/tickets.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Tickets'),
//   array('link'=>'admin/gcs/event/checkin.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Checkin'),
);

$smarty->assign('tabs', $tabs);
