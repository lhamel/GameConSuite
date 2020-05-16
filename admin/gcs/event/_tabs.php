<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/eventsearch/index.php', 'label'=>'Search'),
  array('link'=>'admin/gcs/event/index.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Event'),
  array('link'=>'admin/gcs/event/copyedit.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Copy Edit'),
  // array('link'=>'admin/gcs/event/checkin.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Checkin'),
  array('link'=>'admin/gcs/event/tickets.php', 'querystring'=>'?id_event='.$_GET['id_event'], 'label'=>'Tickets'),
);

$smarty->assign('tabs', $tabs);
