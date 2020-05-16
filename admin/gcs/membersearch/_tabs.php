<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/membersearch/index.php', 'label'=>'Search'),
  // array('link'=>'admin/gcs/membersearch/gamemasters.php', 'label'=>'Gamemasters'),
  // array('link'=>'admin/gcs/membersearch/exhibitors.php', 'label'=>'Exhibitors'),
  // array('link'=>'admin/gcs/membersearch/volunteers.php', 'label'=>'Volunteers'),
  // array('link'=>'admin/gcs/membersearch/staff.php', 'label'=>'Staff'),
  // array('link'=>'admin/gcs/membersearch/paid.php', 'label'=>'Purchased'),
  // array('link'=>'admin/gcs/membersearch/misc.php', 'label'=>'Misc'),
  // array('link'=>'admin/gcs/membersearch/shirts.php', 'label'=>'[Shirts]'),
);

$smarty->assign('tabs', $tabs);
