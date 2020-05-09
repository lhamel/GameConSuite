<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/submissions/unscheduled.php', 'label'=>'&lt;&lt; Submissions'),
  array('link'=>'admin/gcs/publish/unedited.php', 'label'=>'Copy Edit'),
  array('link'=>'admin/gcs/publish/homeless.php', 'label'=>'Homeless'),
  array('link'=>'admin/gcs/publish/long.php', 'label'=>'Lengthy'),
);

$smarty->assign('tabs', $tabs);
