<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/ops/materials.php', 'label'=>'Ops Materials'),
  array('link'=>'admin/gcs/ops/popular.php', 'label'=>'Popular Events'),
//  array('link'=>'admin/gcs/registration/notGms.php', 'label'=>'Non-Gamemasters'),
//  array('link'=>'admin/gcs/registration/exhibitors.php', 'label'=>'Exhibitors'),
//  array('link'=>'admin/gcs/registration/staff.php', 'label'=>'Staff'),
);

$smarty->assign('tabs', $tabs);
