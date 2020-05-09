<?php
include '../../../inc/inc.php';

/*
  Events listed on this page have been scheduled but not approved by 
  administrators.  Only approved events appear in the search result for 
  attendees.
*/

$location = 'admin/gcs/events/unapproved.php';
$title = 'Find Unapproved Events - U-Con Admin';


// unapproved events
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

$sql = <<< EOD
  SELECT 
    E.*, CONCAT(M.s_fname, " ", M.s_lname) as gamemaster
    , E.i_time+E.i_length as endtime
  FROM ucon_event as E, ucon_member as M
  WHERE id_convention=?
    AND E.id_gm=M.id_member
    AND (NOT (s_number=''))
    AND (NOT (e_day='' OR i_time=0 OR isNull(e_day) OR isNull(i_time)))
    AND E.b_approval=0
EOD;
if (isset($_GET['order']))
  $sql .= ' ORDER BY '.$_GET['order'];

include INC_PATH.'db/db.php';
$events = $db->getArray($sql, array($year));

$actions = array(
  'detail' => '../event/index.php?id_event=',
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

$smarty->assign('header', "Unapproved Events $year");
$smarty->assign('directions', "These events have been scheduled but not approved by an administrator. Events must be approved to show up in the user search.");

// render the page
$content = $smarty->fetch('gcs/admin/events/list.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');

