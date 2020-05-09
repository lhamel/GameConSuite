<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

$tabs = array(
  array('link'=>'admin/gcs/submissions/unscheduled.php', 'label'=>'Unscheduled'),
  array('link'=>'admin/gcs/submissions/numbers.php', 'label'=>'Numbers'),
  array('link'=>'admin/gcs/submissions/pricecheck.php', 'querystring'=>'?approved=false', 'label'=>'Price Check'),
  array('link'=>'admin/gcs/submissions/unapproved.php', 'label'=>'Unapproved'),
  array('link'=>'admin/gcs/publish/unedited.php', 'label'=>'&gt;&gt; Publishing'),
);
$smarty->assign('tabs', $tabs);

