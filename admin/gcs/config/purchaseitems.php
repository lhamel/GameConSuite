<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
$location = 'admin/gcs/config/purchaseitems.php';


include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';
include '_tabs.php';

$sql = "select * from ucon_prereg_items order by display_order";
include_once (INC_PATH.'db/db.php');
$list = $db->getAll($sql, array());
if (!is_array($list)) { echo "Sql Error: ".$db->ErrorMsg(); exit; }


$smarty->assign('items', $list);

$smarty->assign('reorderLink', "purchaseitems_reorder.php");
$smarty->assign('updateLink', "purchaseitems_update.php");
$smarty->assign('config', $config);

$content = $smarty->fetch('gcs/admin/config/purchaseitems.tpl');
$smarty->assign('title', $config['gcs']['sitetitle'].' - Ops Non-Ticket Items for Purchase');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
