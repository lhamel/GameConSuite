<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/eventsearch/index.php', 'label'=>'Search'),
//  array('link'=>'admin/gcs/membersearch/gamemasters.php', 'label'=>'Gamemasters'),
//  array('link'=>'admin/gcs/membersearch/notGms.php', 'label'=>'Non-Gamemasters'),
//  array('link'=>'admin/gcs/membersearch/exhibitors.php', 'label'=>'Exhibitors'),
//  array('link'=>'admin/gcs/membersearch/staff.php', 'label'=>'Staff'),

);

$smarty->assign('tabs', $tabs);
