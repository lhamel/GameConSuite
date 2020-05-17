<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/schedule/browse.php', 'label'=>'Browse/Filter'),
  //array('link'=>'admin/gcs/schedule/games.php', 'label'=>'Unique Games'),
  array('link'=>'admin/gcs/schedule/roompicture.php', 'label'=>'Schedule Overview'),
);

$smarty->assign('tabs', $tabs);
