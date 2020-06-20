<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

//if (!is_numeric($_GET['id_member']) && $config.page.location != 'admin/gcs/memberduplicates/index.php') {
//  redirect($depth.'admin/gcs/memberduplicates/index.php');
//}

$tabs = array(
  array('link'=>'admin/gcs/memberduplicates/index.php', 'label'=>'Find Duplicates'),
);

if (isset($_GET['id_member'])) {
  $tabs = $tabs + array(
    array('link'=>'admin/gcs/memberduplicates/ofmember.php', 'querystring'=>'?id_member='.$_GET['id_member'], 'label'=>'Find Duplicates'),
  );
}

if (isset($_GET['id_member']) && isset($_GET['other'])) {
  $tabs = $tabs + array(
    array('link'=>'admin/gcs/memberduplicates/compare.php', 'querystring'=>'?id_member='.$_GET['id_member'].'&amp;other='.$_GET['other'], 'label'=>'Compare'),
    array('link'=>'admin/gcs/memberduplicates/compare.php', 'querystring'=>'?id_member='.$_GET['id_member'].'&amp;other='.$_GET['other'], 'label'=>'Edit'),
  );
}

$smarty->assign('tabs', $tabs);

