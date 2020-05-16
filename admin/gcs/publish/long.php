<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/publish/long.php';
$title = $config['gcs']['admintitle']." - Lengthy Events";

// unapproved events
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

$lengthCalculation = "length(E.s_game)+length(E.s_title)+length(if(length(E.s_desc_web)>0,E.s_desc_web,E.s_desc))";

$sql = <<< EOD
    SELECT  E.*, CONCAT(M.s_fname, " ", M.s_lname, " (",($lengthCalculation),")") as gamemaster
      , E.i_time+E.i_length as endtime 
      , CONCAT(s_title, " (",($lengthCalculation),")") as s_title
    FROM ucon_event as E, ucon_member as M 
    WHERE id_convention=? 
      AND E.id_gm=M.id_member
      AND $lengthCalculation>300
    ORDER BY $lengthCalculation DESC
EOD;
if (isset($_GET['order']))
  $sql .= ' ORDER BY '.$_GET['order'];

include INC_PATH.'db/db.php';
$events = $db->getArray($sql, array($year));
if (!is_array($events)) { echo "SQL Error: ".$db->ErrorMsg()."<br><br>$sql"; }

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

$smarty->assign('header', "Lengthy Events $year");
$smarty->assign('directions', "These events need to be trimmed to a shorter length.");

// render the page
$content = $smarty->fetch('gcs/admin/events/list.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');

