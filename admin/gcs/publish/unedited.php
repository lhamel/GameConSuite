<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/publish/unedited.php';
$title = $config['gcs']['admintitle']." - Unedited Events";

// unapproved events
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

$sql = <<< EOD
    SELECT E.*, CONCAT(M.s_fname, " ", M.s_lname) as gamemaster
      , E.i_time+E.i_length as endtime 
    FROM ucon_event as E, ucon_member as M 
    WHERE id_convention=? 
      AND E.id_gm=M.id_member
      #AND (NOT (e_day='' OR i_time=0 OR isNull(e_day) OR isNull(i_time)))
      AND E.b_edited=0
EOD;
if (isset($_GET['order']))
  $sql .= ' ORDER BY '.$_GET['order'];

include INC_PATH.'db/db.php';
$events = $db->getArray($sql, array($year));

$actions = array(
  'detail' => '../event/copyedit.php?id_event=',
  'list' => basename(__FILE__).'?year='.$year,
);

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);
$smarty->assign('events', $events);

$smarty->assign('header', "Unedited Events $year");
$smarty->assign('directions', "These events need to be copy-edited.");

// render the page
$content = $smarty->fetch('gcs/admin/events/list.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');

